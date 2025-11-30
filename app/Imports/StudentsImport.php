<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\ProgramStudy;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class StudentsImport implements ToCollection, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnError, SkipsOnFailure
{
    use SkipsFailures, SkipsErrors;

    private $userId;
    private $importErrors = [];
    private $importSuccess = [];
    private $rowNumber = 0;

    public function __construct($userId = null)
    {
        $this->userId = $userId ?? auth()->id();
    }

    public function collection(Collection $rows)
    {
        $this->rowNumber = 0;

        foreach ($rows as $row) {
            $this->rowNumber++;

            try {
                // Skip empty rows
                if ($this->isEmptyRow($row)) {
                    continue;
                }

                // Validate required fields first
                $this->validateRequiredFields($row, $this->rowNumber);

                // Process the student
                $student = $this->processStudent($row);

                $this->importSuccess[] = [
                    'row' => $this->rowNumber,
                    'nim' => $student->student_number,
                    'name' => $student->name,
                    'action' => $student->wasRecentlyCreated ? 'created' : 'updated'
                ];

            } catch (\Exception $e) {
                $this->importErrors[] = [
                    'row' => $this->rowNumber,
                    'data' => $row->toArray(),
                    'error' => $e->getMessage()
                ];
            }
        }
    }

    private function isEmptyRow($row)
    {
        $requiredFields = ['nama_wajib', 'email_wajib', 'nim_wajib', 'no_hp_wajib'];
        foreach ($requiredFields as $field) {
            if (!empty($row[$field])) {
                return false;
            }
        }
        return true;
    }

    private function validateRequiredFields($row, $rowNumber)
    {
        $requiredFields = [
            'nama_wajib' => 'Nama',
            'email_wajib' => 'Email',
            'nim_wajib' => 'NIM',
            'no_hp_wajib' => 'No. HP',
            'status_wajib' => 'Status',
            'angkatan_wajib' => 'Angkatan',
            'status_mahasiswa_wajib' => 'Status Mahasiswa',
            'program_studi_wajib' => 'Program Studi'
        ];

        $missingFields = [];
        foreach ($requiredFields as $field => $label) {
            if (empty($row[$field])) {
                $missingFields[] = $label;
            }
        }

        if (!empty($missingFields)) {
            throw new \Exception('Field wajib tidak boleh kosong: ' . implode(', ', $missingFields));
        }
    }

    private function processStudent($row)
    {
        // Clean and prepare data
        $data = $this->prepareStudentData($row);

        // Find or create program study
        $programStudy = $this->findOrCreateProgramStudy($row['program_studi_wajib']);
        $data['program_study_id'] = $programStudy->id;

        // Check for existing student
        $existingStudent = Student::where('student_number', $data['student_number'])
                                  ->orWhere('email', $data['email'])
                                  ->first();

        if ($existingStudent) {
            // Update existing student
            $existingStudent->update($data);

            // Create/update user account if needed
            $this->createUserAccount($existingStudent, $data);

            return $existingStudent;
        } else {
            // Create new student
            $data['created_by'] = $this->userId;
            $data['updated_by'] = $this->userId;

            $student = Student::create($data);

            // Create user account
            $this->createUserAccount($student, $data);

            return $student;
        }
    }

    private function prepareStudentData($row)
    {
        return [
            'student_number' => $this->cleanString($row['nim_wajib']),
            'name' => $this->cleanString($row['nama_wajib']),
            'email' => strtolower(trim($row['email_wajib'])),
            'phone' => $this->cleanString($row['no_hp_wajib']),
            'id_card_number' => $this->cleanString($row['nomor_ktp'] ?? ''),
            'gender' => $this->mapGender($row['jenis_kelamin'] ?? ''),
            'birth_place' => $this->cleanString($row['tempat_lahir'] ?? ''),
            'birth_date' => $this->parseDate($row['tanggal_lahir'] ?? null),
            'address' => $this->cleanString($row['alamat'] ?? ''),
            'city' => $this->cleanString($row['kota'] ?? ''),
            'province' => $this->cleanString($row['provinsi'] ?? ''),
            'postal_code' => $this->cleanString($row['kode_pos'] ?? ''),
            'nationality' => $this->cleanString($row['kewarganegaraan'] ?? 'Indonesia'),
            'religion' => $this->cleanString($row['agama'] ?? ''),
            'blood_type' => $this->mapBloodType($row['golongan_darah'] ?? ''),
            'status' => $this->mapStatus($row['status_wajib']),
            'enrollment_date' => $this->parseDate($row['tanggal_masuk'] ?? null),
            'graduation_date' => $this->parseDate($row['tanggal_lulus'] ?? null),
            'current_semester' => (int)($row['semester_saat_ini'] ?? 1),
            'current_year' => (int)($row['tahun_ajar_saat_ini'] ?? 1),
            'gpa' => (float)($row['ipk'] ?? 0),
            'class' => $this->cleanString($row['kelas'] ?? ''),
            'batch_year' => (int)$row['angkatan_wajib'],
            'is_regular' => $this->mapStudentType($row['status_mahasiswa_wajib']),
            'is_active' => $this->mapStatus($row['status_wajib']) === 'active',
            'father_name' => $this->cleanString($row['nama_ayah'] ?? ''),
            'mother_name' => $this->cleanString($row['nama_ibu'] ?? ''),
            'parent_phone' => $this->cleanString($row['no_hp_ortu'] ?? ''),
            'parent_email' => strtolower(trim($row['email_ortu'] ?? '')),
            'parent_address' => $this->cleanString($row['alamat_ortu'] ?? ''),
            'notes' => $this->cleanString($row['catatan'] ?? ''),
            'updated_by' => $this->userId,
        ];
    }

    private function findOrCreateProgramStudy($programStudyIdentifier)
    {
        // Try to find by ID first
        $programStudy = ProgramStudy::find($programStudyIdentifier);

        if ($programStudy) {
            return $programStudy;
        }

        // Try to find by name
        $programStudy = ProgramStudy::where('name', 'like', '%' . $programStudyIdentifier . '%')->first();

        if ($programStudy) {
            return $programStudy;
        }

        // Create default program study if not found
        return ProgramStudy::firstOrCreate(
            ['name' => $programStudyIdentifier],
            [
                'code' => strtoupper(substr(str_replace(' ', '', $programStudyIdentifier), 0, 5)),
                'duration_years' => 4,
                'minimum_credits' => 144,
                'created_by' => $this->userId,
                'updated_by' => $this->userId,
            ]
        );
    }

    private function createUserAccount($student, $data)
    {
        // Only create user account if one doesn't exist
        if (!$student->user_id) {
            $user = User::where('email', $student->email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $student->name,
                    'email' => $student->email,
                    'password' => Hash::make($student->student_number), // Default password is NIM
                    'email_verified_at' => now(),
                    'created_by' => $this->userId,
                    'updated_by' => $this->userId,
                ]);

                // Assign student role
                $user->assignRole('mahasiswa');
            }

            $student->update(['user_id' => $user->id]);
        }
    }

    private function cleanString($value)
    {
        return trim(preg_replace('/\s+/', ' ', $value ?? ''));
    }

    private function mapGender($gender)
    {
        $gender = strtolower(trim($gender));

        if (in_array($gender, ['l', 'laki-laki', 'male', 'pria'])) {
            return 'L';
        } elseif (in_array($gender, ['p', 'perempuan', 'female', 'wanita'])) {
            return 'P';
        }

        return null;
    }

    private function mapBloodType($bloodType)
    {
        $bloodType = strtoupper(trim($bloodType));

        if (in_array($bloodType, ['A', 'B', 'AB', 'O'])) {
            return $bloodType;
        }

        return null;
    }

    private function mapStatus($status)
    {
        $status = strtolower(trim($status));

        $statusMap = [
            'aktif' => 'active',
            'active' => 'active',
            'lulus' => 'graduated',
            'graduated' => 'graduated',
            'drop out' => 'dropped_out',
            'dropped_out' => 'dropped_out',
            'cuti' => 'on_leave',
            'on_leave' => 'on_leave',
            'tidak aktif' => 'inactive',
            'inactive' => 'inactive',
        ];

        return $statusMap[$status] ?? 'active';
    }

    private function mapStudentType($type)
    {
        $type = strtolower(trim($type));

        if (in_array($type, ['regular', 'tetap', 'full-time'])) {
            return true;
        }

        return false;
    }

    private function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        try {
            // Try different date formats
            $formats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'Y/m/d'];

            foreach ($formats as $format) {
                $parsed = \DateTime::createFromFormat($format, $date);
                if ($parsed) {
                    return $parsed->format('Y-m-d');
                }
            }

            // Try to parse as natural date
            $parsed = new \DateTime($date);
            return $parsed->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'nama_wajib' => 'required|string|max:255',
            'email_wajib' => 'required|email|max:255',
            'nim_wajib' => 'required|string|max:50',
            'no_hp_wajib' => 'required|string|max:20',
            'status_wajib' => 'required|string|in:active,graduated,dropped_out,on_leave,inactive',
            'angkatan_wajib' => 'required|integer|min:1900|max:' . (date('Y') + 10),
            'status_mahasiswa_wajib' => 'required|string|in:regular,non_regular',
            'program_studi_wajib' => 'required|string|max:255',
            'ipk' => 'nullable|numeric|min:0|max:4',
            'semester_saat_ini' => 'nullable|integer|min:1|max:14',
            'tahun_ajar_saat_ini' => 'nullable|integer|min:1|max:10',
            'jenis_kelamin' => 'nullable|in:L,P,l,p,male,female,laki-laki,perempuan',
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function getImportResults()
    {
        return [
            'success' => $this->importSuccess,
            'errors' => $this->importErrors,
            'total_success' => count($this->importSuccess),
            'total_errors' => count($this->importErrors),
        ];
    }

    public function getImportSummary()
    {
        $totalCreated = collect($this->importSuccess)->where('action', 'created')->count();
        $totalUpdated = collect($this->importSuccess)->where('action', 'updated')->count();

        return [
            'total_processed' => $this->rowNumber,
            'total_success' => count($this->importSuccess),
            'total_created' => $totalCreated,
            'total_updated' => $totalUpdated,
            'total_errors' => count($this->importErrors),
            'success_rate' => $this->rowNumber > 0 ? round((count($this->importSuccess) / $this->rowNumber) * 100, 2) : 0,
        ];
    }
}