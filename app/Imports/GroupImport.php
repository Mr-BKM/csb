<?php

namespace App\Imports;

use App\Models\Group;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // This handles the first row as headers

class GroupImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Group([
            'grp_id'   => $row['grp_id'],   // Matches your Excel column name
            'grp_name' => $row['grp_name'], // Matches your Excel column name
        ]);
    }
}
