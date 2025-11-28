<?php

namespace App\Imports;

use App\Models\Lecturer;
use App\Models\ProgramStudy;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class LecturersImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    protected $skipDuplicates = true;
    protected $updateExisting = false;
    protected $forceImportInvalid = false;
    protected $mappingData = [];
    protected $results = [
        'total_rows' => 0,
        'valid_rows' => 0,
        'invalid_rows' => 0,
        'duplicate_rows' => 0,
        'imported' => 0,
        'updated' => 0,
        'skipped' => 0,
        'failed' => 0,
        'total_processed' => 0,
        'invalid_data' => []
    ];

    public function __construct($skipDuplicates = true, $updateExisting = false, $forceImportInvalid = false, $mappingData = [])
    {
        $this->skipDuplicates = $skipDuplicates;
        $this->updateExisting = $updateExisting;
        $this->forceImportInvalid = $forceImportInvalid;
        $this->mappingData = $mappingData;
    }

    public function model(array $row)
    {
        $this->results['total_rows']++;

        // Map column names from Excel to database fields
        $mappedData = $this->mapColumns($row);

        // Validate required fields
        $requiredFields = ['nama_wajib', 'email_wajib', 'nip_nidn_wajib', 'no_hp_wajib', 'status_kepegawaian_wajib', 'jenis_pegawai_wajib', 'status_dosen_wajib'];
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (empty($mappedData[$field])) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields) && !$this->forceImportInvalid) {
            $this->results['invalid_rows']++;
            $this->results['invalid_data'][] = [
                'row_number' => $this->results['total_rows'],
                'data' => $row,
                'errors' => 'Field wajib kosong: ' . implode(', ', $missingFields),
                'mapped_data' => $mappedData
            ];
            return null;
        }

        // Check for duplicate employee_number
        $existingLecturer = Lecturer::where('employee_number', $mappedData['nip_nidn_wajib'])->first();

        if ($existingLecturer) {
            $this->results['duplicate_rows']++;

            if ($this->skipDuplicates) {
                $this->results['skipped']++;
                return null;
            }

            if ($this->updateExisting) {
                // Update existing record
                $this->updateLecturer($existingLecturer, $mappedData);
                $this->results['updated']++;
                $this->results['total_processed']++;
                return null;
            }
        }

        // Create new lecturer
        try {
            $lecturer = $this->createLecturer($mappedData);
            $this->results['imported']++;
            $this->results['valid_rows']++;
            $this->results['total_processed']++;

            return $lecturer;

        } catch (\Exception $e) {
            $this->results['failed']++;
            $this->results['invalid_data'][] = [
                'row_number' => $this->results['total_rows'],
                'data' => $row,
                'errors' => $e->getMessage(),
                'mapped_data' => $mappedData
            ];
            return null;
        }
    }

    private function mapColumns(array $row): array
    {
        $mapping = [
            'nama_wajib' => 'name',
            'email_wajib' => 'email',
            'nip_nidn_wajib' => 'employee_number',
            'no_hp_wajib' => 'phone',
            'no_ktp' => 'id_card_number',
            'jenis_kelamin' => 'gender',
            'tempat_lahir' => 'birth_place',
            'tanggal_lahir' => 'birth_date',
            'alamat' => 'address',
            'kota' => 'city',
            'provinsi' => 'province',
            'kode_pos' => 'postal_code',
            'kebangsaan' => 'nationality',
            'agama' => 'religion',
            'golongan_darah' => 'blood_type',
            'status_kepegawaian_wajib' => 'employment_status',
            'jenis_pegawai_wajib' => 'employment_type',
            'status_dosen_wajib' => 'status',
            'tanggal_masuk' => 'hire_date',
            'jabatan' => 'position',
            'gelar' => 'rank',
            'bidang_keahlian' => 'specialization',
            'departemen' => 'department',
            'fakultas' => 'faculty',
            'pendidikan_tertinggi' => 'highest_education',
            'institusi_pendidikan' => 'education_institution',
            'jurusan_pendidikan' => 'education_major',
            'tahun_lulus' => 'graduation_year',
            'no_ruang_kantor' => 'office_room',
            'catatan' => 'notes'
        ];

        // Apply custom mapping if provided
        if (!empty($this->mappingData)) {
            $mapping = array_merge($mapping, $this->mappingData);
        }

        $mappedData = [];
        foreach ($mapping as $excelColumn => $dbField) {
            $mappedData[$excelColumn] = $this->sanitizeValue($row[$dbField] ?? null);
        }

        return $mappedData;
    }

    private function sanitizeValue($value): string
    {
        if ($value === null) {
            return '';
        }

        // Trim whitespace
        $value = trim($value);

        // Convert to proper format
        $value = htmlspecialchars(strip_tags($value), ENT_QUOTES, 'UTF-8');

        return $value;
    }

    private function createLecturer(array $data): Lecturer
    {
        // Map enum values
        $gender = $this->mapGender($data['jenis_kelamin']);
        $employmentType = $this->mapEmploymentType($data['jenis_pegawai_wajib']);
        $status = $this->mapStatus($data['status_dosen_wajib']);

        // Find program study if specified
        $programStudyId = null;
        if (!empty($data['departemen']) || !empty($data['fakultas'])) {
            $programStudy = ProgramStudy::where(function($query) use ($data) {
                if (!empty($data['departemen'])) {
                    $query->where('name', 'like', '%' . $data['departemen'] . '%');
                }
                if (!empty($data['fakultas'])) {
                    $query->orWhere('faculty', 'like', '%' . $data['fakultas'] . '%');
                }
            })->first();

            if ($programStudy) {
                $programStudyId = $programStudy->id;
            }
        }

        return Lecturer::create([
            'employee_number' => $data['nip_nidn_wajib'],
            'name' => $data['nama_wajib'],
            'email' => $data['email_wajib'],
            'phone' => $data['no_hp_wajib'],
            'gender' => $gender,
            'birth_date' => $this->formatDate($data['tanggal_lahir']),
            'birth_place' => $data['tempat_lahir'],
            'address' => $data['alamat'],
            'city' => $data['kota'],
            'province' => $data['provinsi'],
            'postal_code' => $data['kode_pos'],
            'nationality' => $data['kebangsaan'] ?: 'Indonesia',
            'religion' => $data['agama'],
            'blood_type' => $this->mapBloodType($data['golongan_darah']),
            'id_card_number' => $data['no_ktp_wajib'],
            'status' => $status,
            'employment_status' => $data['status_kepegawaian_wajib'],
            'employment_type' => $employmentType,
            'hire_date' => $this->formatDate($data['tanggal_masuk']),
            'position' => $data['jabatan'],
            'rank' => $data['gelar'],
            'specialization' => $data['bidang_keahlian'],
            'department' => $data['departemen'],
            'faculty' => $data['fakultas'],
            'highest_education' => $data['pendidikan_tertinggi'],
            'education_institution' => $data['institusi_pendidikan'],
            'education_major' => $data['jurusan_pendidikan'],
            'graduation_year' => $this->formatYear($data['tahun_lulus']),
            'office_room' => $data['no_ruang_kantor'],
            'notes' => $data['catatan'],
            'program_study_id' => $programStudyId,
            'is_active' => $status === 'Aktif',
            'created_by' => auth()->id(),
        ]);
    }

    private function updateLecturer(Lecturer $lecturer, array $data): void
    {
        // Map enum values
        $gender = $this->mapGender($data['jenis_kelamin']);
        $employmentType = $this->mapEmploymentType($data['jenis_pegawai_wajib']);
        $status = $this->mapStatus($data['status_dosen_wajib']);

        $lecturer->update([
            'name' => $data['nama_wajib'] ?? $lecturer->name,
            'email' => $data['email_wajib'] ?? $lecturer->email,
            'phone' => $data['no_hp_wajib'] ?? $lecturer->phone,
            'gender' => $gender ?: $lecturer->gender,
            'birth_date' => $this->formatDate($data['tanggal_lahir']) ?? $lecturer->birth_date,
            'birth_place' => $data['tempat_lahir'] ?? $lecturer->birth_place,
            'address' => $data['alamat'] ?? $lecturer->address,
            'city' => $data['kota'] ?? $lecturer->city,
            'province' => $data['provinsi'] ?? $lecturer->province,
            'postal_code' => $data['kode_pos'] ?? $lecturer->postal_code,
            'nationality' => $data['kebangsaan'] ?: $lecturer->nationality,
            'religion' => $data['agama'] ?? $lecturer->religion,
            'blood_type' => $this->mapBloodType($data['golongan_darah']) ?? $lecturer->blood_type,
            'status' => $status ?: $lecturer->status,
            'employment_status' => $data['status_kepegawaian_wajib'] ?? $lecturer->employment_status,
            'employment_type' => $employmentType ?: $lecturer->employment_type,
            'hire_date' => $this->formatDate($data['tanggal_masuk']) ?? $lecturer->hire_date,
            'position' => $data['jabatan'] ?? $lecturer->position,
            'rank' => $data['gelar'] ?? $lecturer->rank,
            'specialization' => $data['bidang_keahlian'] ?? $lecturer->specialization,
            'department' => $data['departemen'] ?? $lecturer->department,
            'faculty' => $data['fakultas'] ?? $lecturer->faculty,
            'highest_education' => $data['pendidikan_tertinggi'] ?? $lecturer->highest_education,
            'education_institution' => $data['institusi_pendidikan'] ?? $lecturer->education_institution,
            'education_major' => $data['jurusan_pendidikan'] ?? $lecturer->education_major,
            'graduation_year' => $this->formatYear($data['tahun_lulus']) ?? $lecturer->graduation_year,
            'office_room' => $data['no_ruang_kantor'] ?? $lecturer->office_room,
            'notes' => $data['catatan'] ?? $lecturer->notes,
            'is_active' => $status === 'Aktif',
            'updated_by' => auth()->id(),
        ]);
    }

    private function mapGender($gender): string
    {
        $gender = strtolower(trim($gender));
        return in_array($gender, ['l', 'laki-laki', 'pria', 'male']) ? 'L' : 'P';
    }

    private function mapEmploymentType($type): string
    {
        $type = strtolower(trim($type));
        if (in_array($type, ['tetap', 'pns', 'permanent'])) {
            return 'Tetap';
        } elseif (in_array($type, ['kontrak', 'contract'])) {
            return 'Kontrak';
        } elseif (in_array($type, ['paruh waktu', 'part-time', 'partime'])) {
            return 'Paruh';
        } elseif (in_array($type, ['tamu', 'guest'])) {
            return 'Tamu';
        }
        return 'Tetap'; // Default
    }

    private function mapStatus($status): string
    {
        $status = strtolower(trim($status));
        if (in_array($status, ['aktif', 'active'])) {
            return 'Aktif';
        } elseif (in_array($status, ['cuti', 'on_leave'])) {
            return 'Cuti';
        } elseif (in_array($status, ['tidak aktif', 'non-aktif', 'inactive'])) {
            return 'Tidak';
        }
        return 'Aktif'; // Default
    }

    private function mapBloodType($bloodType): ?string
    {
        $bloodType = strtoupper(trim($bloodType));
        return in_array($bloodType, ['A', 'B', 'AB', 'O']) ? $bloodType : null;
    }

    private function formatDate($date): ?string
    {
        if (empty($date)) {
            return null;
        }

        try {
            // Try different date formats
            $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y', 'Y/m/d'];

            foreach ($formats as $format) {
                $dateObj = \DateTime::createFromFormat($format, $date);
                if ($dateObj) {
                    return $dateObj->format('Y-m-d');
                }
            }

            // Try to parse as timestamp or standard format
            $timestamp = strtotime($date);
            if ($timestamp !== false) {
                return date('Y-m-d', $timestamp);
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function formatYear($year): ?int
    {
        if (empty($year)) {
            return null;
        }

        $year = (int) $year;
        return ($year >= 1950 && $year <= (date('Y') + 5)) ? $year : null;
    }

    public function rules(): array
    {
        return [
            'nama_wajib' => 'required|string|max:255',
            'email_wajib' => 'required|email|max:255',
            'nip_nidn_wajib' => 'required|string|max:50',
            'no_hp_wajib' => 'required|string|max:20',
            'no_ktp_wajib' => 'required|string|max:30',
            'status_kepegawaian_wajib' => 'required|string|max:50',
            'jenis_pegawai_wajib' => 'required|string|max:50',
            'status_dosen_wajib' => 'required|string|max:50',
        ];
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function getResults(): array
    {
        return $this->results;
    }
}