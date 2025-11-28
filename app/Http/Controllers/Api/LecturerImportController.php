<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lecturer;
use App\Models\ProgramStudy;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LecturersImport;
use App\Exports\LecturersTemplateExport;

class LecturerImportController extends Controller
{
    /**
     * Download template for import
     */
    public function downloadTemplate()
    {
        try {
            $filename = 'template-import-dosen-' . date('Y-m-d') . '.xlsx';

            // Create the export object
            $export = new LecturersTemplateExport();

            // Generate and get the file content
            $fileContent = Excel::raw($export, \Maatwebsite\Excel\Excel::XLSX);

            // Set headers for Excel download
            $headers = [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ];

            return response($fileContent, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Template download failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunduh template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate uploaded Excel file
     */
    public function validateFile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('file');
            $filename = time() . '_import_dosen.' . $file->getClientOriginalExtension();

            Log::info("Starting file validation", [
                'filename' => $filename,
                'original_filename' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType()
            ]);

            // Store file temporarily
            $path = $file->storeAs('temp/imports', $filename, 'local');

            // Process the file and validate data
            $import = new LecturersImport();

            Log::info("Beginning Excel import process", [
                'file_path' => $path
            ]);

            Excel::import($import, $path);

            $results = $import->getResults();

            Log::info("Excel import completed", [
                'results' => $results,
                'all_data_count' => count($results['all_data'] ?? []),
                'invalid_data_count' => count($results['invalid_data'] ?? [])
            ]);


            // Remove temporary file
            Storage::disk('local')->delete($path);

            return response()->json([
                'success' => true,
                'message' => 'File berhasil divalidasi',
                'data' => [
                    'total_rows' => $results['total_rows'],
                    'valid_rows' => $results['valid_rows'],
                    'invalid_rows' => $results['invalid_rows'],
                    'duplicate_rows' => $results['duplicate_rows'],
                    'invalid_data' => $results['invalid_data'],
                    'all_data' => $results['all_data'] ?? []
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process bulk import with options
     */
    public function processImport(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required_without:corrected_data|mimes:xlsx,xls,csv|max:10240',
            'corrected_data' => 'required_without:file|array',
            'corrected_data.*' => 'array',
            'skip_duplicates' => 'boolean',
            'update_existing' => 'boolean',
            'force_import_invalid' => 'boolean',
            'mapping_data' => 'array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Request tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $skipDuplicates = $request->input('skip_duplicates', true);
            $updateExisting = $request->input('update_existing', false);
            $forceImportInvalid = $request->input('force_import_invalid', false);
            $mappingData = $request->input('mapping_data', []);

            Log::info('Starting bulk import', [
                'skip_duplicates' => $skipDuplicates,
                'update_existing' => $updateExisting,
                'force_import_invalid' => $forceImportInvalid,
                'has_corrected_data' => $request->has('corrected_data'),
                'has_file' => $request->hasFile('file')
            ]);

            $results = [
                'imported' => 0,
                'updated' => 0,
                'skipped' => 0,
                'failed' => 0,
                'total_processed' => 0,
                'failed_records' => [] // Store detailed failure information
            ];

            // Process corrected data if provided (from manual mapping)
            if ($request->has('corrected_data')) {
                $correctedData = $request->input('corrected_data');
                Log::info('Processing corrected data', ['count' => count($correctedData)]);

                foreach ($correctedData as $index => $data) {
                    $results['total_processed']++;

                    try {
                        // Validate email before creating lecturer
                        if (!empty($data['email_wajib']) && !filter_var($data['email_wajib'], FILTER_VALIDATE_EMAIL)) {
                            Log::warning("Skipping invalid email in corrected data", [
                                'index' => $index,
                                'email' => $data['email_wajib']
                            ]);
                            $results['failed']++;
                            continue;
                        }

                        // Create lecturer from validated data
                        $lecturerService = new \App\Services\LecturerService();
                        $lecturer = $lecturerService->createLecturerFromValidatedData($data, auth()->user());

                        if ($lecturer) {
                            $results['imported']++;

                            Log::info('Successfully imported lecturer from corrected data', [
                                'lecturer_id' => $lecturer->id,
                                'employee_number' => $lecturer->employee_number,
                                'name' => $lecturer->name
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to import lecturer from corrected data', [
                            'index' => $index,
                            'data' => $data,
                            'error' => $e->getMessage()
                        ]);
                        $results['failed']++;

                        // Add detailed failure information
                        $results['failed_records'][] = [
                            'row_number' => $index + 1,
                            'name' => $data['nama_wajib'] ?? 'Unknown',
                            'email' => $data['email_wajib'] ?? 'Unknown',
                            'employee_number' => $data['nip_nidn_wajib'] ?? 'Unknown',
                            'error' => $e->getMessage(),
                            'data_preview' => array_intersect_key($data, array_flip(['nama_wajib', 'email_wajib', 'nip_nidn_wajib', 'jenis_kelamin']))
                        ];
                    }
                }
            } else {
                // Process file normally
                $file = $request->file('file');
                $filename = time() . '_bulk_import_dosen.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('temp/imports', $filename, 'local');

                Log::info('Processing file import', ['filename' => $filename, 'path' => $path]);

                // Process validation first
                $import = new LecturersImport(
                    null, // programStudyId
                    auth()->user(), // user
                    $skipDuplicates,
                    $updateExisting,
                    $forceImportInvalid,
                    $mappingData
                );

                Excel::import($import, $path);
                $validationResults = $import->getResults();

                Log::info('File validation completed', [
                    'total_rows' => $validationResults['total_rows'],
                    'valid_rows' => $validationResults['valid_rows'],
                    'invalid_rows' => $validationResults['invalid_rows'],
                    'duplicate_rows' => $validationResults['duplicate_rows']
                ]);

                // Now actually import the valid data
                $lecturerService = new \App\Services\LecturerService();
                $importedCount = 0;
                $failedCount = 0;

                foreach ($validationResults['all_data'] as $data) {
                    if ($data['is_valid'] && !$data['is_duplicate']) {
                        try {
                            $lecturer = $lecturerService->createLecturerFromValidatedData($data['mapped_data'], auth()->user());
                            if ($lecturer) {
                                $importedCount++;

                                Log::info('Successfully imported lecturer', [
                                    'row_number' => $data['row_number'],
                                    'lecturer_id' => $lecturer->id,
                                    'employee_number' => $lecturer->employee_number,
                                    'name' => $lecturer->name
                                ]);
                            }
                        } catch (\Exception $e) {
                            Log::error('Failed to import lecturer', [
                                'row_number' => $data['row_number'],
                                'data' => $data['mapped_data'],
                                'error' => $e->getMessage()
                            ]);
                            $failedCount++;

                            // Add detailed failure information
                            $results['failed_records'][] = [
                                'row_number' => $data['row_number'],
                                'name' => $data['mapped_data']['nama_wajib'] ?? 'Unknown',
                                'email' => $data['mapped_data']['email_wajib'] ?? 'Unknown',
                                'employee_number' => $data['mapped_data']['nip_nidn_wajib'] ?? 'Unknown',
                                'error' => $e->getMessage(),
                                'data_preview' => array_intersect_key($data['mapped_data'], array_flip(['nama_wajib', 'email_wajib', 'nip_nidn_wajib', 'jenis_kelamin']))
                            ];
                        }
                    } elseif ($data['is_duplicate']) {
                        Log::info('Skipped duplicate lecturer', [
                            'row_number' => $data['row_number'],
                            'employee_number' => $data['mapped_data']['nip_nidn_wajib'] ?? 'N/A'
                        ]);
                    } else {
                        Log::warning('Skipped invalid lecturer', [
                            'row_number' => $data['row_number'],
                            'errors' => $data['errors'] ?? 'Unknown error'
                        ]);
                    }
                }

                $results = [
                    'imported' => $importedCount,
                    'updated' => 0,
                    'skipped' => $validationResults['duplicate_rows'],
                    'failed' => $failedCount + $validationResults['invalid_rows'],
                    'total_processed' => $validationResults['total_rows']
                ];

                // Clean up
                Storage::disk('local')->delete($path);
            }

            DB::commit();

            Log::info('Bulk import completed', $results);

            // Determine if import was actually successful
            $hasSuccess = $results['imported'] > 0 || $results['updated'] > 0;
            $hasFailures = $results['failed'] > 0;

            if ($hasFailures && !$hasSuccess) {
                // All data failed to import
                return response()->json([
                    'success' => false,
                    'message' => 'Semua data gagal diimport. Periksa kembali format data Anda.',
                    'data' => $results
                ], 400);
            } elseif ($hasFailures && $hasSuccess) {
                // Partial success
                return response()->json([
                    'success' => true,
                    'message' => "Import selesai dengan {$results['failed']} data gagal dan {$results['imported']} berhasil",
                    'data' => $results
                ]);
            } else {
                // Full success
                return response()->json([
                    'success' => true,
                    'message' => 'Import data dosen berhasil',
                    'data' => $results
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Bulk import failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengimport data: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTrace() : null
            ], 500);
        }
    }

    /**
     * Get program studies for mapping
     */
    public function getProgramStudies(): JsonResponse
    {
        try {
            $programStudies = ProgramStudy::select('id', 'name', 'code', 'faculty')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $programStudies
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data program studi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check duplicate NIP/NIDN
     */
    public function checkDuplicates(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'employee_numbers' => 'required|array',
            'employee_numbers.*' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Request tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $employeeNumbers = $request->input('employee_numbers');
            $duplicates = Lecturer::whereIn('employee_number', $employeeNumbers)
                ->select('employee_number', 'name', 'email')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'duplicates' => $duplicates,
                    'duplicate_count' => $duplicates->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengecek duplikat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Revalidate corrected data
     */
    public function revalidateData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'corrected_data' => 'required|array',
            'corrected_data.*.row_number' => 'required|integer',
            'corrected_data.*.mapped_data' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Request tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $correctedData = $request->input('corrected_data');
            $invalidData = [];
            $invalidRows = 0;
            $validRows = 0;

            foreach ($correctedData as $item) {
                $mappedData = $item['mapped_data'];
                $errors = [];

                // Validate required fields
                $requiredFields = ['nama_wajib', 'email_wajib', 'nip_nidn_wajib', 'no_hp_wajib', 'status_kepegawaian_wajib', 'jenis_pegawai_wajib', 'status_dosen_wajib'];

                foreach ($requiredFields as $field) {
                    if (empty($mappedData[$field])) {
                        $errors[] = "Field {$field} wajib diisi";
                    }
                }

                // Validate email format and duplicate
                if (!empty($mappedData['email_wajib'])) {
                    // Check email format
                    if (!filter_var($mappedData['email_wajib'], FILTER_VALIDATE_EMAIL)) {
                        $errors[] = "Format email tidak valid";
                    } else {
                        // Check for duplicate email
                        $existingEmail = Lecturer::where('email', $mappedData['email_wajib'])->first();
                        if ($existingEmail) {
                            $errors[] = "Email sudah ada di sistem";
                        }
                    }
                }

                // Validate NIP/NIDN duplicates
                if (!empty($mappedData['nip_nidn_wajib'])) {
                    $existingLecturer = Lecturer::where('employee_number', $mappedData['nip_nidn_wajib'])->first();
                    if ($existingLecturer) {
                        $errors[] = "NIP/NIDN sudah ada di sistem";
                    }
                }

                // Validate phone format (basic)
                if (!empty($mappedData['no_hp_wajib']) && !preg_match('/^[0-9+\-\s()]+$/', $mappedData['no_hp_wajib'])) {
                    $errors[] = "Format nomor HP tidak valid";
                }

                // Validate program study if provided
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
                        $errors[] = "Program studi tidak ditemukan";
                    }
                }

                if (!empty($errors)) {
                    $invalidData[] = [
                        'row_number' => $item['row_number'],
                        'mapped_data' => $mappedData,
                        'errors' => implode(', ', $errors),
                        'errors_array' => $errors
                    ];
                    $invalidRows++;
                } else {
                    $validRows++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Revalidasi selesai',
                'data' => [
                    'invalid_rows' => $invalidRows,
                    'valid_rows' => $validRows,
                    'invalid_data' => $invalidData
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ggal merevalidasi data: ' . $e->getMessage()
            ], 500);
        }
    }
}