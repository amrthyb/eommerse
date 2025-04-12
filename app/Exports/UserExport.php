<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Mengambil data pengguna dengan role 'user'
        return User::where('role', 'user')->get(['id', 'name', 'email']);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
        ];
    }
}

