<?php

namespace App\Imports;

use App\Models\SubGroup;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;;

class SubGroupImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new SubGroup([
            'subgrp_id'   => $row['subgrp_id'],   // Matches your Excel column name
            'subgrp_name' => $row['subgrp_name'], // Matches your Excel column name
        ]);
    }
}
