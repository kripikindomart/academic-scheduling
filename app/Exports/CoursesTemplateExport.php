<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class CoursesTemplateExport implements FromCollection, WithStyles, WithColumnWidths, WithEvents
{
    public function collection()
    {
        return collect([
            // Baris 6: Field codes (untuk sistem)
            [
                'id_wajib' => 'id_wajib',
                'course_code_wajib' => 'course_code_wajib',
                'course_name_wajib' => 'course_name_wajib',
                'description' => 'description',
                'credits_wajib' => 'credits_wajib',
                'semester_wajib' => 'semester_wajib',
                'academic_year' => 'academic_year',
                'course_type_wajib' => 'course_type_wajib',
                'level' => 'level',
                'capacity_wajib' => 'capacity_wajib',
                'program_study_name_wajib' => 'program_study_name_wajib',
                'is_active' => 'is_active',
                'prerequisites' => 'prerequisites',
            ],
            // Baris 7: Header titles (untuk user) - dalam Bahasa Indonesia
            [
                'id_wajib' => 'ID',
                'course_code_wajib' => 'Kode Mata Kuliah',
                'course_name_wajib' => 'Nama Mata Kuliah',
                'description' => 'Deskripsi',
                'credits_wajib' => 'SKS',
                'semester_wajib' => 'Semester',
                'academic_year' => 'Tahun Akademik',
                'course_type_wajib' => 'Jenis Mata Kuliah',
                'level' => 'Jenjang',
                'capacity_wajib' => 'Kapasitas',
                'program_study_name_wajib' => 'Program Studi',
                'is_active' => 'Status Aktif',
                'prerequisites' => 'Prasyarat',
            ],
            // Baris 8: Sample data
            [
                'id_wajib' => '1',
                'course_code_wajib' => 'INF101',
                'course_name_wajib' => 'Algoritma dan Pemrograman',
                'description' => 'Mata kuliah dasar algoritma dan pemrograman komputer',
                'credits_wajib' => '3',
                'semester_wajib' => 'ganjil',
                'academic_year' => '2024/2025',
                'course_type_wajib' => 'mandatory',
                'level' => 'S1',
                'capacity_wajib' => '50',
                'program_study_name_wajib' => 'Teknik Informatika',
                'is_active' => '1',
                'prerequisites' => '',
            ]
        ]);
    }

    // headings() tidak digunakan karena kita menggunakan custom structure dengan 3 baris

    public function styles(Worksheet $sheet)
    {
        // Style field codes row (baris 6) - untuk sistem
        $sheet->getStyle('A6:M6')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 10,
                'color' => ['rgb' => '6B7280'], // Gray color
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F3F4F6'], // Light gray background
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ]);

        // Style header titles row (baris 7) - untuk user (dengan warna)
        $sheet->getStyle('A7:M7')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3B82F6'], // Blue color
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ]);

        // Make header text wrap
        $sheet->getStyle('A7:M7')->getAlignment()->setWrapText(true);

        // Style sample data row (baris 8)
        $sheet->getStyle('A8:M8')->applyFromArray([
            'font' => [
                'italic' => true,
                'color' => ['rgb' => '6B7280'], // Gray color
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F9FAFB'], // Light gray background
            ],
        ]);

        // Set row height
        $sheet->getRowDimension(6)->setRowHeight(25);
        $sheet->getRowDimension(7)->setRowHeight(30);
        $sheet->getRowDimension(8)->setRowHeight(25);

        // Style required fields indicator (di baris 7 - header titles)
        $requiredColumns = ['A', 'B', 'C', 'E', 'F', 'H', 'J', 'K'];

        foreach ($requiredColumns as $column) {
            $sheet->getStyle($column . '7')->applyFromArray([
                'font' => [
                    'color' => ['rgb' => 'FFFFFF'], // White text
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'EF4444'], // Red background for required fields
                ],
            ]);
        }

        // Add border to all data rows (6-8)
        $sheet->getStyle('A6:M8')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'E5E7EB'],
                ],
            ],
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,  // id_wajib
            'B' => 15,  // course_code_wajib
            'C' => 30,  // course_name_wajib
            'D' => 35,  // description
            'E' => 8,   // credits_wajib
            'F' => 12,  // semester_wajib
            'G' => 15,  // academic_year
            'H' => 15,  // course_type_wajib
            'I' => 10,  // level
            'J' => 10,  // capacity_wajib
            'K' => 25,  // program_study_name_wajib
            'L' => 15,  // is_active
            'M' => 30,  // prerequisites
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Add instructions at the top
                $sheet->insertNewRowBefore(1, 5);

                $sheet->setCellValue('A1', 'PETUNJUK IMPORT DATA MATA KULIAH');
                $sheet->setCellValue('A2', '1. Kolom dengan tanda (*) adalah WAJIB diisi');
                $sheet->setCellValue('A3', '2. Course Code: Kode unik mata kuliah (contoh: INF101)');
                $sheet->setCellValue('A4', '3. Credits: Jumlah SKS (angka: 1-6)');
                $sheet->setCellValue('A5', '4. Semester: ganjil/genap (contoh: ganjil/genap)');

                // Style instructions
                $sheet->mergeCells('A1:M1');
                $sheet->mergeCells('A2:M2');
                $sheet->mergeCells('A3:M3');
                $sheet->mergeCells('A4:M4');
                $sheet->mergeCells('A5:M5');

                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['rgb' => '1F2937'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FEF3C7'], // Yellow background
                    ],
                    'alignment' => [
                        'horizontal' => 'center',
                        'vertical' => 'center',
                    ],
                ]);

                $sheet->getStyle('A2:A5')->applyFromArray([
                    'font' => [
                        'size' => 11,
                        'color' => ['rgb' => '4B5563'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F3F4F6'], // Light gray background
                    ],
                    'alignment' => [
                        'horizontal' => 'left',
                        'vertical' => 'center',
                    ],
                ]);

                // Apply additional styling after all data is in place
                // Re-apply styles to ensure they are visible
                $this->applyHeaderStyles($sheet);

                // Freeze header rows (6-7) agar tetap terlihat saat scroll
                $sheet->freezePane('A8');
            },
        ];
    }

    private function applyHeaderStyles($sheet)
    {
        // Force apply styles to header row (baris 7) - ini adalah row kedua dari collection
        $headerRow = 7; // Baris pertama dari collection (setelah insert instructions)

        // Style required fields indicator (di baris 7 - header titles)
        $requiredColumns = ['A', 'B', 'C', 'E', 'F', 'H', 'J', 'K'];

        foreach ($requiredColumns as $column) {
            // Apply blue first, then override with red for required fields
            $sheet->getStyle($column . $headerRow)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'EF4444'], // Red background for required fields
                ],
            ]);
        }

        // Style optional fields (blue)
        $optionalColumns = ['D', 'G', 'I', 'L', 'M'];
        foreach ($optionalColumns as $column) {
            $sheet->getStyle($column . $headerRow)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '3B82F6'], // Blue background for optional fields
                ],
            ]);
        }
    }
}