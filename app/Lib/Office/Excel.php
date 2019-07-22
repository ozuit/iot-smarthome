<?php

namespace App\Lib\Office;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Excel
{
    public function export(array $data, string $title = 'Export', array $center_columns = [])
    {
        $spread_sheet = new Spreadsheet();
        $spread_sheet->setActiveSheetIndex(0);
        $active_sheet = $spread_sheet->getActiveSheet();
        $row = $data[0];
        $column_number = count($row);
        $col = 'A';
        $active_sheet->setCellValueByColumnAndRow(1, 1, $title);
        $max_col = null;
        while ($column_number > 0) {
            $max_col = $col;
            $active_sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
            $column_number--;
        }
        $title_cells = 'A1:'.$max_col.'1';
        $header_cells = 'A2:'.$max_col.'2';
        $active_sheet->mergeCells($title_cells);
        $active_sheet->getStyle($title_cells)->getFont()->setSize(16);
        $active_sheet->getStyle($title_cells)->getFont()->setBold(true);
        $active_sheet->getStyle($title_cells)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $active_sheet->getStyle($header_cells)->getFont()->setSize(13);
        $active_sheet->getStyle($header_cells)->getFont()->setBold(true);
        $active_sheet->getStyle($header_cells)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $last_row = count($data) + 1;
        $data_cells = 'A2:'.$max_col.$last_row;
        $active_sheet->fromArray($data, null, 'A2');
        $active_sheet->getStyle($data_cells)->getFont()->setSize(13);
        $active_sheet->getStyle($data_cells)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);
        foreach ($center_columns as $col) {
            $cells = $col.'3:'.$col.$last_row;
            $active_sheet->getStyle($cells)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $obj_writer = new Xlsx($spread_sheet);
        ob_start();
        $obj_writer->save('php://output');
        return ob_get_clean();
    }

    public function import(UploadedFile $file): array
    {
        $spread_sheet = IOFactory::load($file->getPathname());
        $spread_sheet->setActiveSheetIndex(0);
        $active_sheet = $spread_sheet->getActiveSheet();
        $highest_column = $active_sheet->getHighestColumn();
        $highest_row = intval($active_sheet->getHighestRow());
        $data = $active_sheet->rangeToArray('A2'.':'.$highest_column.$highest_row);
        return is_array($data) ? $data : [];
    }
}
