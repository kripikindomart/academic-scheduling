<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class StudentsExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithMapping, WithEvents
{
    protected $students;
    protected $includeHeaders = true;
    protected $filters = [];

    public function __construct($students = null, $filters = [])
    {
        $this->filters = $filters;

        if ($students) {
            $this->students = $students;
        } else {
            // Get students with filters
            $query = Student::with(['programStudy', 'user', 'creator', 'updater']);

            // Apply filters
            if (!empty($filters['program_study_id'])) {
                $query->where('program_study_id', $filters['program_study_id']);
            }

            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            if (!empty($filters['batch_year'])) {
                $query->where('batch_year', $filters['batch_year']);
            }

            if (!empty($filters['gender'])) {
                $query->where('gender', $filters['gender']);
            }

            if (!empty($filters['is_regular'])) {
                $query->where('is_regular', $filters['is_regular']);
            }

            if (!empty($filters['is_active'])) {
                $query->where('is_active', $filters['is_active']);
            }

            if (!empty($filters['gpa_min'])) {
                $query->where('gpa', '>=', $filters['gpa_min']);
            }

            if (!empty($filters['gpa_max'])) {
                $query->where('gpa', '<=', $filters['gpa_max']);
            }

            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('student_number', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            $this->students = $query->orderBy('created_at', 'desc')->get();
        }
    }

    public function collection()
    {
        return $this->students;
    }

    public function map($student): array
    {
        return [
            $student->student_number ?? '',
            $student->name ?? '',
            $student->email ?? '',
            $student->phone ?? '',
            $student->id_card_number ?? '',
            $this->mapGender($student->gender),
            $student->birth_place ?? '',
            $this->formatDate($student->birth_date),
            $student->address ?? '',
            $student->city ?? '',
            $student->province ?? '',
            $student->postal_code ?? '',
            $student->nationality ?? '',
            $student->religion ?? '',
            $student->blood_type ?? '',
            $student->status ?? '',
            $this->formatDate($student->enrollment_date),
            $this->formatDate($student->graduation_date),
            $student->current_semester ?? '',
            $student->current_year ?? '',
            $student->gpa ?? '',
            $student->class ?? '',
            $student->batch_year ?? '',
            $student->is_regular ? 'Regular' : 'Non-Regular',
            $student->is_active ? 'Aktif' : 'Tidak Aktif',
            $student->father_name ?? '',
            $student->mother_name ?? '',
            $student->parent_phone ?? '',
            $student->parent_email ?? '',
            $student->parent_address ?? '',
            $student->programStudy->name ?? '',
            $student->programStudy->code ?? '',
            $student->hasUserAccount() ? 'Ya' : 'Tidak',
            $student->getAcademicStandingAttribute(),
            $student->getAttendanceRateAttribute(),
            $student->getAverageGradeAttribute(),
            $student->getCreditsCompletedAttribute(),
            $student->getRemainingCreditsAttribute(),
            $student->notes ?? '',
            $this->formatDate($student->created_at),
            $student->creator->name ?? 'System',
            $this->formatDate($student->updated_at),
            $student->updater->name ?? 'System',
        ];
    }

    public function headings(): array
    {
        return [
            'NIM',
            'Nama Lengkap',
            'Email',
            'No. HP',
            'No. KTP',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Alamat',
            'Kota',
            'Provinsi',
            'Kode Pos',
            'Kewarganegaraan',
            'Agama',
            'Golongan Darah',
            'Status',
            'Tanggal Masuk',
            'Tanggal Lulus',
            'Semester Saat Ini',
            'Tahun Ajar Saat Ini',
            'IPK',
            'Kelas',
            'Angkatan',
            'Jenis Mahasiswa',
            'Status Aktif',
            'Nama Ayah',
            'Nama Ibu',
            'No. HP Orang Tua',
            'Email Orang Tua',
            'Alamat Orang Tua',
            'Program Studi',
            'Kode Program Studi',
            'Memiliki Akun User',
            'Prestasi Akademik',
            'Tingkat Kehadiran (%)',
            'Nilai Rata-rata',
            'SKS Selesai',
            'SKS Tersisa',
            'Catatan',
            'Tanggal Dibuat',
            'Dibuat Oleh',
            'Tanggal Diupdate',
            'Diupdate Oleh',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style header row (row 1)
        $sheet->getStyle('A1:AJ1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '059669'], // Green color
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
                'wrapText' => true,
            ],
        ]);

        // Set row height for header
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Style data rows
        $highestRow = $sheet->getHighestDataRow();
        $highestColumn = $sheet->getHighestDataColumn();

        // Apply borders to all data
        $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'E5E7EB'],
                ],
            ],
        ]);

        // Alternating row colors for better readability
        for ($row = 2; $row <= $highestRow; $row++) {
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F9FAFB'],
                    ],
                ]);
            }
        }

        // Auto-size columns for better fit
        foreach (range('A', $highestColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Freeze header row
        $sheet->freezePane('A2');

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // NIM
            'B' => 25, // Nama Lengkap
            'C' => 25, // Email
            'D' => 15, // No. HP
            'E' => 18, // No. KTP
            'F' => 12, // Jenis Kelamin
            'G' => 15, // Tempat Lahir
            'H' => 12, // Tanggal Lahir
            'I' => 30, // Alamat
            'J' => 15, // Kota
            'K' => 15, // Provinsi
            'L' => 10, // Kode Pos
            'M' => 15, // Kewarganegaraan
            'N' => 12, // Agama
            'O' => 12, // Golongan Darah
            'P' => 15, // Status
            'Q' => 12, // Tanggal Masuk
            'R' => 12, // Tanggal Lulus
            'S' => 18, // Semester Saat Ini
            'T' => 18, // Tahun Ajar Saat Ini
            'U' => 8,  // IPK
            'V' => 8,  // Kelas
            'W' => 8,  // Angkatan
            'X' => 15, // Jenis Mahasiswa
            'Y' => 12, // Status Aktif
            'Z' => 20, // Nama Ayah
            'AA' => 20, // Nama Ibu
            'AB' => 15, // No. HP Orang Tua
            'AC' => 25, // Email Orang Tua
            'AD' => 25, // Alamat Orang Tua
            'AE' => 20, // Program Studi
            'AF' => 15, // Kode Program Studi
            'AG' => 15, // Memiliki Akun User
            'AH' => 18, // Prestasi Akademik
            'AI' => 18, // Tingkat Kehadiran
            'AJ' => 15, // Nilai Rata-rata
            'AK' => 12, // SKS Selesai
            'AL' => 12, // SKS Tersisa
            'AM' => 30, // Catatan
            'AN' => 12, // Tanggal Dibuat
            'AO' => 15, // Dibuat Oleh
            'AP' => 12, // Tanggal Diupdate
            'AQ' => 15, // Diupdate Oleh
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Add summary information at the top if headers are included
                if ($this->includeHeaders) {
                    $sheet->insertNewRowBefore(1, 3);

                    $totalStudents = count($this->students);
                    $activeStudents = $this->students->where('status', 'active')->count();
                    $graduatedStudents = $this->students->where('status', 'graduated')->count();
                    $avgGpa = $this->students->avg('gpa');

                    $sheet->setCellValue('A1', 'LAPORAN DATA MAHASISWA');
                    $sheet->setCellValue('A2', "Total Mahasiswa: {$totalStudents} | Aktif: {$activeStudents} | Lulus: {$graduatedStudents} | Rata-rata IPK: " . number_format($avgGpa, 2));
                    $sheet->setCellValue('A3', 'Tanggal Export: ' . now()->format('d/m/Y H:i:s'));

                    // Merge cells for summary
                    $sheet->mergeCells('A1:AQ1');
                    $sheet->mergeCells('A2:AQ2');
                    $sheet->mergeCells('A3:AQ3');

                    // Style summary rows
                    $sheet->getStyle('A1:A3')->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 14,
                            'color' => ['rgb' => '1F2937'],
                        ],
                        'alignment' => [
                            'horizontal' => 'center',
                            'vertical' => 'center',
                        ],
                    ]);

                    $sheet->getStyle('A1')->getFill()->applyFromArray([
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'DBEAFE'],
                    ]);

                    $sheet->getStyle('A2')->getFill()->applyFromArray([
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F3F4F6'],
                    ]);

                    $sheet->getStyle('A3')->getFill()->applyFromArray([
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FEF3C7'],
                    ]);

                    // Adjust row heights
                    $sheet->getRowDimension(1)->setRowHeight(25);
                    $sheet->getRowDimension(2)->setRowHeight(20);
                    $sheet->getRowDimension(3)->setRowHeight(20);
                }

                // Set print settings
                $sheet->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

                $sheet->getPageMargins()->setTop(0.5);
                $sheet->getPageMargins()->setRight(0.5);
                $sheet->getPageMargins()->setBottom(0.5);
                $sheet->getPageMargins()->setLeft(0.5);
            },
        ];
    }

    private function mapGender($gender)
    {
        switch ($gender) {
            case 'L':
                return 'Laki-laki';
            case 'P':
                return 'Perempuan';
            default:
                return $gender ?? '';
        }
    }

    private function formatDate($date)
    {
        return $date ? \Carbon\Carbon::parse($date)->format('d/m/Y') : '';
    }

    public function setIncludeHeaders($include)
    {
        $this->includeHeaders = $include;
        return $this;
    }
}