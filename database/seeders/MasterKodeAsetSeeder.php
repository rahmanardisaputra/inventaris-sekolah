<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterKodeAsetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'kategori' => 'Alat Kantor dan Rumah Tangga',
                'kelompok' => 'Alat Kantor',
                'jenis' => 'Mebel',
                'kode_prefix' => '03.01.01.07',
                'keterangan' => 'Kursi Besi',
            ],
            [
                'kategori' => 'Alat Kantor dan Rumah Tangga',
                'kelompok' => 'Alat Rumah Tangga',
                'jenis' => 'Alat Pendingin',
                'kode_prefix' => '03.01.02.04',
                'keterangan' => 'AC Split',
            ],
            [
                'kategori' => 'Alat Studio dan Komunikasi',
                'kelompok' => 'Alat Studio',
                'jenis' => 'Peralatan Komputer',
                'kode_prefix' => '03.02.01.03',
                'keterangan' => 'CPU',
            ],
            [
                'kategori' => 'Alat Studio dan Komunikasi',
                'kelompok' => 'Alat Studio',
                'jenis' => 'Peralatan Komputer',
                'kode_prefix' => '03.02.01.04',
                'keterangan' => 'Monitor',
            ],
            [
                'kategori' => 'Alat Studio dan Komunikasi',
                'kelompok' => 'Alat Studio',
                'jenis' => 'Peralatan Komputer',
                'kode_prefix' => '03.02.01.16',
                'keterangan' => 'Printer',
            ],
        ];

        foreach ($data as $item) {
            \App\Models\MasterKodeAset::create($item);
        }
    }
}
