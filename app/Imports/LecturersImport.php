<?php

namespace App\Imports;

use App\Models\Lecturer;
use App\Models\ProgramStudy;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use DateTime;
use DateInterval;

class LecturersImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithCustomCsvSettings, WithMultipleSheets
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
        'all_data' => [], // Semua data dari Excel
        'invalid_data' => []
    ];
    protected $currentRow = 0;

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

        // Store ALL data from Excel for manual mapping interface
        $this->results['all_data'][] = [
            'row_number' => $this->results['total_rows'],
            'original_data' => $row,
            'mapped_data' => $mappedData,
            'is_valid' => false,
            'is_duplicate' => false,
            'errors' => [],
            'errors_array' => []
        ];

        // Validate required fields
        $requiredFields = ['nama_wajib', 'email_wajib', 'nip_nidn_wajib', 'no_hp_wajib', 'status_kepegawaian_wajib', 'jenis_pegawai_wajib', 'status_dosen_wajib'];
        $missingFields = [];
        $errors = [];
        $errors_array = [];
        $warnings = [];
        $warnings_array = [];

        foreach ($requiredFields as $field) {
            if (empty($mappedData[$field])) {
                $missingFields[] = $field;
                $error_msg = "Field {$field} wajib diisi";
                $errors[] = $error_msg;
                $errors_array[] = $error_msg;
            }
        }

        // Validate email format
        if (!empty($mappedData['email_wajib']) && !filter_var($mappedData['email_wajib'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Format email tidak valid";
            $errors_array[] = "Format email tidak valid";
        }

        // Check for duplicate employee_number
        $existingLecturer = Lecturer::where('employee_number', $mappedData['nip_nidn_wajib'])->first();
        $isDuplicate = $existingLecturer !== null;

        if ($isDuplicate) {
            $this->results['duplicate_rows']++;
            $errors[] = "NIP/NIDN sudah ada di sistem";
            $errors_array[] = "NIP/NIDN sudah ada di sistem";
        }

        // Validate phone format
        if (!empty($mappedData['no_hp_wajib']) && !preg_match('/^[0-9+\-\s()]+$/', $mappedData['no_hp_wajib'])) {
            $errors[] = "Format nomor HP tidak valid";
            $errors_array[] = "Format nomor HP tidak valid";
        }

        // Validate program study if provided - NOW AS WARNING, NOT BLOCKING ERROR
        if (!empty($mappedData['departemen']) || !empty($mappedData['fakultas'])) {
            $programStudy = ProgramStudy::where(function($query) use ($mappedData) {
                if (!empty($mappedData['departemen'])) {
                    $query->where('name', 'like', '%' . $mappedData['departemen'] . '%');
                }
                if (!empty($mappedData['fakultas'])) {
                    $query->orWhere('faculty', 'like', '%' . $mappedData['fakultas'] . '%');
                }
            })->first();

            if (!$programStudy) {
                $warning_msg = "Program studi '{$mappedData['departemen']}' tidak ditemukan. Silakan pilih dari program studi yang tersedia atau biarkan kosong.";
                $warnings[] = $warning_msg;
                $warnings_array[] = $warning_msg;
            }
        }

        // Update the last entry with validation results
        $lastIndex = count($this->results['all_data']) - 1;

        // Data considered valid if no critical errors (warnings are OK)
        $hasOnlyWarnings = empty($errors) && !empty($warnings);
        $this->results['all_data'][$lastIndex]['is_valid'] = empty($errors) && !$isDuplicate;
        $this->results['all_data'][$lastIndex]['is_duplicate'] = $isDuplicate;
        $this->results['all_data'][$lastIndex]['errors'] = empty($errors) ? null : implode(', ', $errors);
        $this->results['all_data'][$lastIndex]['errors_array'] = $errors_array;
        $this->results['all_data'][$lastIndex]['warnings'] = empty($warnings) ? null : implode(', ', $warnings);
        $this->results['all_data'][$lastIndex]['warnings_array'] = $warnings_array;

        // If there are errors, add to invalid_data (warnings don't make it invalid)
        if (!empty($errors) && !$this->forceImportInvalid) {
            $this->results['invalid_rows']++;
            $this->results['invalid_data'][] = $this->results['all_data'][$lastIndex];
        }

        // Handle duplicates
        if ($isDuplicate) {
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

        // If data is valid and not a duplicate, create new lecturer
        if (empty($errors) && !$isDuplicate) {
            try {
                $lecturer = $this->createLecturer($mappedData);
                $this->results['imported']++;
                $this->results['valid_rows']++;
                $this->results['total_processed']++;

                return $lecturer;

            } catch (\Exception $e) {
                $this->results['failed']++;
                $error_msg = $e->getMessage();
                $this->results['all_data'][$lastIndex]['errors'] = $error_msg;
                $this->results['all_data'][$lastIndex]['errors_array'] = [$error_msg];
                $this->results['all_data'][$lastIndex]['is_valid'] = false;

                $this->results['invalid_rows']++;
                $this->results['invalid_data'][] = $this->results['all_data'][$lastIndex];
                return null;
            }
        }

        return null;
    }

    private function mapColumns(array $row): array
    {
        $mapping = [
            'nama_wajib' => 'nama_wajib',
            'email_wajib' => 'email_wajib',
            'nip_nidn_wajib' => 'nip_nidn_wajib',
            'no_hp_wajib' => 'no_hp_wajib',
            'no_ktp' => 'no_ktp',
            'jenis_kelamin' => 'jenis_kelamin',
            'tempat_lahir' => 'tempat_lahir',
            'tanggal_lahir' => 'tanggal_lahir',
            'alamat' => 'alamat',
            'kota' => 'kota',
            'provinsi' => 'provinsi',
            'kode_pos' => 'kode_pos',
            'kebangsaan' => 'kebangsaan',
            'agama' => 'agama',
            'golongan_darah' => 'golongan_darah',
            'status_kepegawaian_wajib' => 'status_kepegawaian_wajib',
            'jenis_pegawai_wajib' => 'jenis_pegawai_wajib',
            'status_dosen_wajib' => 'status_dosen_wajib',
            'tanggal_masuk' => 'tanggal_masuk',
            'jabatan' => 'jabatan',
            'gelar' => 'gelar',
            'bidang_keahlian' => 'bidang_keahlian',
            'departemen' => 'departemen',
            'fakultas' => 'fakultas',
            'pendidikan_tertinggi' => 'pendidikan_tertinggi',
            'institusi_pendidikan' => 'institusi_pendidikan',
            'jurusan_pendidikan' => 'jurusan_pendidikan',
            'tahun_lulus' => 'tahun_lulus',
            'no_ruang_kantor' => 'no_ruang_kantor',
            'catatan' => 'catatan'
        ];

        // Apply custom mapping if provided
        if (!empty($this->mappingData)) {
            $mapping = array_merge($mapping, $this->mappingData);
        }

        $mappedData = [];
        foreach ($mapping as $excelColumn => $fieldName) {
            $mappedData[$excelColumn] = $this->sanitizeValue($row[$fieldName] ?? null);
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
            'employee_number' => $this->formatEmployeeNumber($data['nip_nidn_wajib']),
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
            'id_card_number' => $data['no_ktp'], // Fixed: no_ktp instead of no_ktp_wajib
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

            // Check if it's an Excel serial number (like 29235, 40179)
            if (is_numeric($date) && $date > 25000 && $date < 100000) {
                // Excel base date is 1900-01-01, but Excel incorrectly treats 1900 as a leap year
                // So we need to adjust by 1 day for dates after 1900-02-28
                $excelDate = (int)$date;
                if ($excelDate > 60) {
                    $excelDate -= 1; // Adjust for Excel leap year bug
                }

                $baseDate = new DateTime('1900-01-01');
                $dateObj = $baseDate->add(new DateInterval("P{$excelDate}D"));
                return $dateObj->format('Y-m-d');
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

    private function formatEmployeeNumber($employeeNumber): string
    {
        if (empty($employeeNumber)) {
            return '';
        }

        // Handle scientific notation from Excel
        if (is_numeric($employeeNumber)) {
            // Convert scientific notation to string without decimal points
            $formatted = number_format($employeeNumber, 0, '', '');

            // Ensure it's a valid length for NIP/NIDN (typically 16-18 digits)
            if (strlen($formatted) > 15) {
                return $formatted;
            }
        }

        // For non-numeric values, return as string
        return (string) $employeeNumber;
    }


    public function headingRow(): int
    {
        return 6; // Header di baris 6, data dimulai dari baris 7
    }

    
    public function chunkSize(): int
    {
        return 100;
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
            'enclosure' => '"',
            'escape' => '\\',
            'input_encoding' => 'UTF-8'
        ];
    }

    public function sheets(): array
    {
        return [
            $this
        ];
    }

    public function onUnknownSheet($sheetName)
    {
        return null;
    }

    public function getResults(): array
    {
        return $this->results;
    }
}