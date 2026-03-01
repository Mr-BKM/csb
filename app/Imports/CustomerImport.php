<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Customer([

            'cus_id'   => $row['cus_id'],
            'cus_name' => $row['cus_name'],
            'cus_address' => $row['cus_address'],
            'cus_telephone' => $row['cus_telephone'],
            'cus_description' => $row['cus_description'],

        ]);
    }
}
