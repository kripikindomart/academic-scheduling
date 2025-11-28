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

class LecturersTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    public function collection()
    {
        return collect([
            [
                'nama_wajib' => 'Contoh: Dr. Budi Santoso, M.Kom',
                'email_wajib' => 'budi.santoso@universitas.ac.id',
                'nip_nidn_wajib' => '123456789012345678',
                'no_hp_wajib' => '081234567890',
                'no_ktp' => '3214011234560001 (opsional)',
                'jenis_kelamin' => 'L/P (Laki-laki/Perempuan)',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1980-01-15 (YYYY-MM-DD)',
                'alamat' => 'Jl. Sudirman No. 123',
                'kota' => 'Jakarta',
                'provinsi' => 'DKI Jakarta',
                'kode_pos' => '12345',
                'kebangsaan' => 'Indonesia',
                'agama' => 'Islam',
                'golongan_darah' => 'A/B/AB/O',
                'status_kepegawaian_wajib' => 'PNS/Tetap/Kontrak/Honorer',
                'jenis_pegawai_wajib' => 'Tetap/Kontrak/Paruh Waktu/Tamu',
                'status_dosen_wajib' => 'Aktif/Cuti/Tidak Aktif',
                'tanggal_masuk' => '2010-01-01 (YYYY-MM-DD)',
                'jabatan' => 'Dosen',
                'gelar' => 'Lektor Kepala',
                'bidang_keahlian' => 'Kecerdasan Buatan',
                'departemen' => 'Teknik Informatika',
                'fakultas' => 'Fakultas Teknik',
                'pendidikan_tertinggi' => 'S1/S2/S3',
                'institusi_pendidikan' => 'Universitas Indonesia',
                'jurusan_pendidikan' => 'Teknik Informatika',
                'tahun_lulus' => '2015',
                'no_ruang_kantor' => 'Lab Komputer 1',
                'catatan' => 'Dosen dengan spesialisasi AI',
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'nama_wajib*',
            'email_wajib*',
            'nip_nidn_wajib*',
            'no_hp_wajib*',
            'no_ktp',
            'jenis_kelamin',
            'tempat_lahir',
            'tanggal_lahir',
            'alamat',
            'kota',
            'provinsi',
            'kode_pos',
            'kebangsaan',
            'agama',
            'golongan_darah',
            'status_kepegawaian_wajib*',
            'jenis_pegawai_wajib*',
            'status_dosen_wajib*',
            'tanggal_masuk',
            'jabatan',
            'gelar',
            'bidang_keahlian',
            'departemen',
            'fakultas',
            'pendidikan_tertinggi',
            'institusi_pendidikan',
            'jurusan_pendidikan',
            'tahun_lulus',
            'no_ruang_kantor',
            'catatan'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style header row (row 1)
        $sheet->getStyle('A1:Z1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5'], // Indigo color
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ]);

        // Make header text wrap
        $sheet->getStyle('A1:Z1')->getAlignment()->setWrapText(true);

        // Style example data row (row 2)
        $sheet->getStyle('A2:Z2')->applyFromArray([
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
        $requiredColumns = ['A', 'B', 'C', 'D', 'P', 'Q', 'R'];

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
        $sheet->getStyle('A1:Z2')->applyFromArray([
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
            'C' => 20, // nip_nidn_wajib
            'D' => 15, // no_hp_wajib
            'E' => 20, // no_ktp_wajib
            'F' => 12, // jenis_kelamin
            'G' => 15, // tempat_lahir
            'H' => 15, // tanggal_lahir
            'I' => 30, // alamat
            'J' => 15, // kota
            'K' => 15, // provinsi
            'L' => 10, // kode_pos
            'M' => 15, // kebangsaan
            'N' => 12, // agama
            'O' => 12, // golongan_darah
            'P' => 20, // status_kepegawaian_wajib
            'Q' => 18, // jenis_pegawai_wajib
            'R' => 15, // status_dosen_wajib
            'S' => 15, // tanggal_masuk
            'T' => 15, // jabatan
            'U' => 18, // gelar
            'V' => 25, // bidang_keahlian
            'W' => 20, // departemen
            'X' => 18, // fakultas
            'Y' => 15, // pendidikan_tertinggi
            'Z' => 25, // institusi_pendidikan
            'AA' => 20, // jurusan_pendidikan
            'AB' => 10, // tahun_lulus
            'AC' => 18, // no_ruang_kantor
            'AD' => 35, // catatan
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Add instructions at the top
                $sheet->insertNewRowBefore(1, 5);

                $sheet->setCellValue('A1', 'PETUNJUK IMPORT DATA DOSEN');
                $sheet->setCellValue('A2', '1. Kolom dengan tanda (*) adalah WAJIB diisi');
                $sheet->setCellValue('A3', '2. Format tanggal: YYYY-MM-DD (contoh: 1980-01-15)');
                $sheet->setCellValue('A4', '3. Jenis kelamin: L (Laki-laki) atau P (Perempuan)');
                $sheet->setCellValue('A5', '4. Status kepegawaian: PNS, Tetap, Kontrak, atau Honorer');

                // Style instructions
                $sheet->mergeCells('A1:Z1');
                $sheet->mergeCells('A2:Z2');
                $sheet->mergeCells('A3:Z3');
                $sheet->mergeCells('A4:Z4');
                $sheet->mergeCells('A5:Z5');

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

                // Add validation for required fields in data rows (from row 7 onwards)
                $sheet->freezePane('A7');
            },
        ];
    }
}