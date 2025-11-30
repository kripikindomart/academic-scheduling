<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AcademicYearService;

class ProgramStudyFilter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip filtering for guest users
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Admin can access all program studies, but we still need to set academic year context
        if ($user->isAdmin()) {
            // Set academic year context for all authenticated users
            $this->setAcademicYearContext($request);
            return $next($request);
        }

        // For non-admin users, ensure they have a program study assigned
        if (!$user->program_study_id) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Program study not assigned',
                    'message' => 'Your account is not assigned to any program study. Please contact administrator.',
                ], 403);
            }

            return redirect()->route('dashboard')
                ->with('error', 'Your account is not assigned to any program study. Please contact administrator.');
        }

        // Set academic year context
        $this->setAcademicYearContext($request);

        // Add program study filter to request for later use in controllers
        $request->merge([
            'program_study_filter' => $user->program_study_id,
            'academic_year_filter' => AcademicYearService::getCurrentActiveAcademicYearId(),
        ]);

        return $next($request);
    }

    /**
     * Set academic year context for the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    private function setAcademicYearContext(Request $request)
    {
        // Check if academic year is specified in request (for admin override)
        if ($request->has('academic_year_id') && Auth::user()->isAdmin()) {
            AcademicYearService::setActiveAcademicYearForSession($request->input('academic_year_id'));
        }

        // Ensure we have an active academic year
        $activeYear = AcademicYearService::getActiveAcademicYearId();
        if (!$activeYear) {
            // Set to current year as default
            AcademicYearService::setActiveAcademicYearId((int)date('Y'));
        }
    }
}