<?php

namespace App\Lib\Office;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;

class Excel
{
    public function export(array $data)
    {
        $spread_sheet = new Spreadsheet();
        $spread_sheet->setActiveSheetIndex(0);
        $active_sheet = $spread_sheet->getActiveSheet();
        $row = $data[0];
        $column_number = count($row);
        $col = 'A';
        $max_col = null;
        while ($column_number > 0) {
            $max_col = $col;
            $active_sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
            $column_number--;
        }
        
        $last_row = count($data) + 1;
        $data_cells = 'A1:'.$max_col.$last_row;
        $active_sheet->fromArray($data, null, 'A1');
        $active_sheet->getStyle($data_cells)->getFont()->setSize(13);
        $active_sheet->getStyle($data_cells)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        return new Xlsx($spread_sheet);
    }
}
