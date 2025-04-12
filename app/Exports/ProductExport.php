<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Product::all();  // Ambil semua data produk
    }

    /**
     * Menentukan heading di file Excel
     *
     * @return array
     */
    public function headings(): array
    {
        return ['Id', 'Name', 'Description', 'Price', 'Stock'];
    }
}

