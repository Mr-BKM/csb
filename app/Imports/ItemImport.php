<?php

namespace App\Imports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ItemImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Item([
            'itm_code'   => $row['itm_code'],
            'itm_barcode' => $row['itm_barcode'],
            'itm_name'   => $row['itm_name'],
            'itm_sinhalaname' => $row['itm_sinhalaname'],
            'itm_book_code'   => $row['itm_book_code'],
            'itm_page_num' => $row['itm_page_num'],
            'itm_group_id'   => $row['itm_group_id'],
            'itm_group' => $row['itm_group'],
            'itm_subgroup_id'   => $row['itm_subgroup_id'],
            'itm_subgroup' => $row['itm_subgroup'],
            'itm_unit_of_measure'   => $row['itm_unit_of_measure'],
            'itm_book_stock'   => $row['itm_book_stock'],
            'itm_loan_stock'   => $row['itm_loan_stock'],
            'itm_stock'   => $row['itm_stock'],
            'itm_reorder_level' => $row['itm_reorder_level'],
            'itm_reorder_flag' => $row['itm_reorder_flag'],
            'itm_description' => $row['itm_description'],
            'itm_status' => $row['itm_status'],
        ]);
    }
}
