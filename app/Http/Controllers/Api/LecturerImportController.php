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
    public function downloadTemplate(): BinaryFileResponse
    {
        try {
            $filename = 'template-import-dosen-' . date('Y-m-d') . '.xlsx';

            return Excel::download(new LecturersTemplateExport(), $filename);
        } catch (\Exception $e) {
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

            
            // Store file temporarily
            $path = $file->storeAs('temp/imports', $filename, 'local');

            // Process the file and validate data
            $import = new LecturersImport();
            Excel::import($import, $path);

            $results = $import->getResults();

            
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
            $results = [];

            // Process corrected data if provided (from manual mapping)
            if ($request->has('corrected_data')) {
                $correctedData = $request->input('corrected_data');

                foreach ($correctedData as $data) {
                    try {
                        // Create lecturer from corrected data
                        $import = new LecturersImport(
                            $skipDuplicates,
                            $updateExisting,
                            $forceImportInvalid,
                            $mappingData
                        );

                        // Use the model method directly with the corrected data
                        $lecturer = $import->model($data);

                        // Count results
                        if ($lecturer) {
                            $results['imported'] = ($results['imported'] ?? 0) + 1;
                            $results['total_processed'] = ($results['total_processed'] ?? 0) + 1;
                        }
                    } catch (\Exception $e) {
                        $results['failed'] = ($results['failed'] ?? 0) + 1;
                    }
                }
            } else {
                // Process file normally
                $file = $request->file('file');
                $filename = time() . '_bulk_import_dosen.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('temp/imports', $filename, 'local');

                // Process import with options
                $import = new LecturersImport(
                    $skipDuplicates,
                    $updateExisting,
                    $forceImportInvalid,
                    $mappingData
                );

                Excel::import($import, $path);
                $results = $import->getResults();

                // Clean up
                Storage::disk('local')->delete($path);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Import data dosen berhasil',
                'data' => [
                    'imported' => $results['imported'] ?? 0,
                    'updated' => $results['updated'] ?? 0,
                    'skipped' => $results['skipped'] ?? 0,
                    'failed' => $results['failed'] ?? 0,
                    'total_processed' => $results['total_processed'] ?? 0
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

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

                // Validate email format
                if (!empty($mappedData['email_wajib']) && !filter_var($mappedData['email_wajib'], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Format email tidak valid";
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