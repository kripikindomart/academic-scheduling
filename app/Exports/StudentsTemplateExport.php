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

class StudentsTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    public function collection()
    {
        return collect([
            [
                'nama_wajib' => 'Contoh: Ahmad Rizki Pratama',
                'email_wajib' => 'ahmad.rizki@universitas.ac.id',
                'nim_wajib' => '2021001001',
                'no_hp_wajib' => '081234567890',
                'nomor_ktp' => '3214011234560001 (opsional)',
                'jenis_kelamin' => 'L/P (Laki-laki/Perempuan)',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '2000-01-15 (YYYY-MM-DD)',
                'alamat' => 'Jl. Sudirman No. 123',
                'kota' => 'Jakarta',
                'provinsi' => 'DKI Jakarta',
                'kode_pos' => '12345',
                'kewarganegaraan' => 'Indonesia',
                'agama' => 'Islam',
                'golongan_darah' => 'A/B/AB/O',
                'status_wajib' => 'active/graduated/dropped_out/on_leave/inactive',
                'tanggal_masuk' => '2020-08-01 (YYYY-MM-DD)',
                'tanggal_lulus' => '2024-06-30 (YYYY-MM-DD) (opsional)',
                'semester_saat_ini' => '6',
                'tahun_ajar_saat_ini' => '3',
                'ipk' => '3.75',
                'kelas' => 'A',
                'angkatan_wajib' => '2020',
                'status_mahasiswa_wajib' => 'regular/non_regular',
                'nama_ayah' => 'Bapak Ahmad Sudirman',
                'nama_ibu' => 'Ibu Siti Nurhaliza',
                'no_hp_ortu' => '081234567891',
                'email_ortu' => 'orangtua@email.com (opsional)',
                'alamat_ortu' => 'Alamat orang tua (opsional)',
                'program_studi_wajib' => 'Teknik Informatika (ID program studi)',
                'catatan' => 'Mahasiswa berprestasi dengan IPK tinggi',
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'nama_wajib*',
            'email_wajib*',
            'nim_wajib*',
            'no_hp_wajib*',
            'nomor_ktp',
            'jenis_kelamin',
            'tempat_lahir',
            'tanggal_lahir',
            'alamat',
            'kota',
            'provinsi',
            'kode_pos',
            'kewarganegaraan',
            'agama',
            'golongan_darah',
            'status_wajib*',
            'tanggal_masuk',
            'tanggal_lulus',
            'semester_saat_ini',
            'tahun_ajar_saat_ini',
            'ipk',
            'kelas',
            'angkatan_wajib*',
            'status_mahasiswa_wajib*',
            'nama_ayah',
            'nama_ibu',
            'no_hp_ortu',
            'email_ortu',
            'alamat_ortu',
            'program_studi_wajib*',
            'catatan'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style header row (row 1)
        $sheet->getStyle('A1:AD1')->applyFromArray([
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
            ],
        ]);

        // Make header text wrap
        $sheet->getStyle('A1:AD1')->getAlignment()->setWrapText(true);

        // Style example data row (row 2)
        $sheet->getStyle('A2:AD2')->applyFromArray([
            'font' => [
                'italic' => true,
                'color' => ['rgb' => '6B7280'], // Gray color
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F9FAFB'], // Light gray background
            ],
        ]);

        // Set row height for header and example
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(25);

        // Style required fields indicator
        $requiredColumns = ['A', 'B', 'C', 'D', 'P', 'W', 'X', 'AA'];

        foreach ($requiredColumns as $column) {
            $sheet->getStyle($column . '1')->applyFromArray([
                'font' => [
                    'color' => ['rgb' => 'FFFFFF'], // White text
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'DC2626'], // Red background for required fields
                ],
            ]);
        }

        // Add border to all cells
        $sheet->getStyle('A1:AD2')->applyFromArray([
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
            'A' => 25, // nama_wajib
            'B' => 30, // email_wajib
            'C' => 15, // nim_wajib
            'D' => 15, // no_hp_wajib
            'E' => 20, // nomor_ktp
            'F' => 12, // jenis_kelamin
            'G' => 15, // tempat_lahir
            'H' => 15, // tanggal_lahir
            'I' => 30, // alamat
            'J' => 15, // kota
            'K' => 15, // provinsi
            'L' => 10, // kode_pos
            'M' => 15, // kewarganegaraan
            'N' => 12, // agama
            'O' => 12, // golongan_darah
            'P' => 20, // status_wajib
            'Q' => 15, // tanggal_masuk
            'R' => 15, // tanggal_lulus
            'S' => 18, // semester_saat_ini
            'T' => 18, // tahun_ajar_saat_ini
            'U' => 10, // ipk
            'V' => 10, // kelas
            'W' => 12, // angkatan_wajib
            'X' => 20, // status_mahasiswa_wajib
            'Y' => 20, // nama_ayah
            'Z' => 20, // nama_ibu
            'AA' => 15, // no_hp_ortu
            'AB' => 25, // email_ortu
            'AC' => 30, // alamat_ortu
            'AD' => 35, // program_studi_wajib
            'AE' => 35, // catatan
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Add instructions at the top
                $sheet->insertNewRowBefore(1, 6);

                $sheet->setCellValue('A1', 'PETUNJUK IMPORT DATA MAHASISWA');
                $sheet->setCellValue('A2', '1. Kolom dengan tanda (*) adalah WAJIB diisi');
                $sheet->setCellValue('A3', '2. Format tanggal: YYYY-MM-DD (contoh: 2000-01-15)');
                $sheet->setCellValue('A4', '3. Jenis kelamin: L (Laki-laki) atau P (Perempuan)');
                $sheet->setCellValue('A5', '4. Status mahasiswa: active, graduated, dropped_out, on_leave, inactive');
                $sheet->setCellValue('A6', '5. Program Studi: masukkan ID program studi yang sudah ada di sistem');

                // Style instructions
                $sheet->mergeCells('A1:AE1');
                $sheet->mergeCells('A2:AE2');
                $sheet->mergeCells('A3:AE3');
                $sheet->mergeCells('A4:AE4');
                $sheet->mergeCells('A5:AE5');
                $sheet->mergeCells('A6:AE6');

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

                $sheet->getStyle('A2:A6')->applyFromArray([
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

                // Add validation for required fields in data rows (from row 8 onwards)
                $sheet->freezePane('A8');
            },
        ];
    }
}