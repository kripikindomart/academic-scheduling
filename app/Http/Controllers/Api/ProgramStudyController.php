<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProgramStudy;
use App\Http\Requests\ProgramStudy\StoreProgramStudyRequest;
use App\Http\Requests\ProgramStudy\UpdateProgramStudyRequest;
use App\Services\ResponseService;
use App\Services\ProgramStudyService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ProgramStudyController extends Controller
{
    protected ProgramStudyService $programStudyService;

    public function __construct(ProgramStudyService $programStudyService)
    {
        $this->programStudyService = $programStudyService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'faculty',
                'level',
                'degree',
                'is_active',
                'status',
                'search'
            ]);

            // Map frontend status to backend is_active
            if (isset($filters['status'])) {
                if ($filters['status'] === 'active') {
                    $filters['is_active'] = 1;
                } elseif ($filters['status'] === 'inactive') {
                    $filters['is_active'] = 0;
                }
                unset($filters['status']);
            }

            $perPage = $request->get('per_page', 20);
            $programs = $this->programStudyService->getAllProgramStudies($perPage, $filters);

            return ResponseService::paginated(
                $programs,
                'Program studies retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to retrieve program studies: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProgramStudyRequest $request): JsonResponse
    {
        try {
            $program = $this->programStudyService->createProgramStudy($request->validated());

            return ResponseService::success(
                $program,
                'Program study created successfully',
                201
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to create program study: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProgramStudy $programStudy): JsonResponse
    {
        try {
            $programStudy->load([
                'creator',
                'courses' => function ($query) {
                    $query->active();
                },
                'lecturers'
            ]);

            return ResponseService::success(
                $programStudy,
                'Program study retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to retrieve program study: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProgramStudyRequest $request, ProgramStudy $programStudy): JsonResponse
    {
        try {
            // Log request data for debugging
            \Log::info('Update request data:', [
                'request_data' => $request->all(),
                'program_study_id' => $programStudy->id,
                'validated_data' => $request->validated()
            ]);

            $updatedProgram = $this->programStudyService->updateProgramStudy($programStudy->id, $request->validated());

            return ResponseService::success(
                $updatedProgram,
                'Program study updated successfully'
            );
        } catch (\Exception $e) {
            \Log::error('Update failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return ResponseService::error(
                'Failed to update program study: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProgramStudy $programStudy): JsonResponse
    {
        try {
            $this->programStudyService->deleteProgramStudy($programStudy->id);

            return ResponseService::success(
                null,
                'Program study deleted successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to delete program study: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Get program study statistics.
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->programStudyService->getProgramStudyStatistics();

            return ResponseService::success(
                $stats,
                'Program study statistics retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to retrieve program study statistics: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Get faculties list.
     */
    public function faculties(): JsonResponse
    {
        try {
            $faculties = $this->programStudyService->getFaculties();

            return ResponseService::success(
                $faculties,
                'Faculties retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to retrieve faculties: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Assign lecturer to program study.
     */
    public function assignLecturer(Request $request, ProgramStudy $programStudy): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'role' => 'required|in:head,coordinator,lecturer',
            ]);

            $this->programStudyService->assignLecturer(
                $programStudy->id,
                $validated['user_id'],
                $validated['role']
            );

            return ResponseService::success(
                null,
                'Lecturer assigned successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to assign lecturer: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Remove lecturer from program study.
     */
    public function removeLecturer(Request $request, ProgramStudy $programStudy): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);

            $this->programStudyService->removeLecturer($programStudy->id, $validated['user_id']);

            return ResponseService::success(
                null,
                'Lecturer removed successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to remove lecturer: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Bulk update program studies.
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'program_study_ids' => 'required|array|min:1',
                'program_study_ids.*' => 'required|integer|exists:program_studies,id',
                'updates' => 'required|array|min:1',
                'updates.*' => 'string'
            ]);

            $result = $this->programStudyService->bulkUpdateProgramStudies($validated['program_study_ids'], $validated['updates']);

            return ResponseService::success(
                $result,
                'Program studies updated successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to bulk update program studies: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Bulk delete program studies.
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'program_study_ids' => 'required|array|min:1',
                'program_study_ids.*' => 'required|integer|exists:program_studies,id'
            ]);

            $result = $this->programStudyService->bulkDeleteProgramStudies($validated['program_study_ids']);

            return ResponseService::success(
                $result,
                'Program studies deleted successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to bulk delete program studies: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Bulk activate/deactivate program studies.
     */
    public function bulkToggleStatus(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'program_study_ids' => 'required|array|min:1',
                'program_study_ids.*' => 'required|integer|exists:program_studies,id',
                'is_active' => 'required|boolean'
            ]);

            $result = $this->programStudyService->bulkToggleProgramStudyStatus($validated['program_study_ids'], $validated['is_active']);

            return ResponseService::success(
                $result,
                'Program study status updated successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to bulk toggle program study status: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Import program studies from file.
     */
    public function import(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv|max:10240'
            ]);

            $result = $this->programStudyService->importProgramStudies($validated['file']);

            return ResponseService::success(
                $result,
                'Program studies imported successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to import program studies: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Export program studies to file.
     */
    public function export(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'faculty',
                'level',
                'degree',
                'is_active',
                'format'
            ]);

            $file = $this->programStudyService->exportProgramStudies($filters);
            $filename = 'program-studies.' . ($filters['format'] ?? 'xlsx');

            // Generate a temporary download URL
            $downloadUrl = url('/api/program-studies/download/' . basename($file));

            return ResponseService::success([
                'download_url' => $downloadUrl,
                'filename' => $filename,
                'file_size' => file_exists($file) ? filesize($file) : 0,
                'format' => $filters['format'] ?? 'xlsx'
            ], 'Export completed successfully');
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to export program studies: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Download exported file.
     */
    public function download(string $filename)
    {
        try {
            $filePath = storage_path('app/exports/' . $filename);

            if (!file_exists($filePath)) {
                return ResponseService::notFound('File not found');
            }

            return response()->download($filePath, $filename);
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to download file: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Display trashed program studies.
     */
    public function trash(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'search',
                'faculty',
                'level'
            ]);

            $perPage = $request->get('per_page', 20);
            $programs = $this->programStudyService->getTrashedProgramStudies($perPage, $filters);

            return ResponseService::paginated(
                $programs,
                'Trashed program studies retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to retrieve trashed program studies: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Restore trashed program study.
     */
    public function restore(Request $request, $id): JsonResponse
    {
        try {
            $programStudy = ProgramStudy::onlyTrashed()->findOrFail($id);
            $program = $this->programStudyService->restoreProgramStudy($programStudy->id);

            return ResponseService::success(
                $program,
                'Program study restored successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to restore program study: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Toggle program study status.
     */
    public function toggleStatus(ProgramStudy $programStudy, Request $request): JsonResponse
    {
        try {
            $isActive = $request->input('is_active', false);
            $program = $this->programStudyService->toggleProgramStudyStatus($programStudy->id, $isActive);

            return ResponseService::success(
                $program,
                'Program study status updated successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to update program study status: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Duplicate program study.
     */
    public function duplicate(ProgramStudy $programStudy): JsonResponse
    {
        try {
            $newProgram = $this->programStudyService->duplicateProgramStudy($programStudy->id);

            return ResponseService::success(
                $newProgram,
                'Program study duplicated successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to duplicate program study: ' . $e->getMessage(),
                null,
                500
            );
        }
      }

    /**
     * Force delete program study permanently.
     */
    public function forceDelete(Request $request, $id): JsonResponse
    {
        try {
            $programStudy = ProgramStudy::onlyTrashed()->findOrFail($id);
            $this->programStudyService->forceDeleteProgramStudy($programStudy->id);

            return ResponseService::success(
                null,
                'Program study deleted permanently successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to permanently delete program study: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
}
