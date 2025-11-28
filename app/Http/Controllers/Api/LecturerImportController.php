<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lecturer;
use App\Models\ProgramStudy;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LecturersImport;
use App\Exports\LecturersTemplateExport;

class LecturerImportController extends Controller
{
    /**
     * Download template for import
     */
    public function downloadTemplate(): JsonResponse
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
                    'invalid_data' => $results['invalid_data']
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
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
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

            $file = $request->file('file');
            $filename = time() . '_bulk_import_dosen.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('temp/imports', $filename, 'local');

            $skipDuplicates = $request->input('skip_duplicates', true);
            $updateExisting = $request->input('update_existing', false);
            $forceImportInvalid = $request->input('force_import_invalid', false);
            $mappingData = $request->input('mapping_data', []);

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

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Import data dosen berhasil',
                'data' => [
                    'imported' => $results['imported'],
                    'updated' => $results['updated'],
                    'skipped' => $results['skipped'],
                    'failed' => $results['failed'],
                    'total_processed' => $results['total_processed']
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
}