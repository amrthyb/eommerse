<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CategoryExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Category::all();
    }

    /**
     * Menentukan heading di file Excel
     *
     * @return array
     */
    public function headings(): array
    {
        return ['Id', 'Name', 'Description'];
    }

}
