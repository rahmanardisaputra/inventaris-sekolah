<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // 2. Seed Lokasi Ruangan
        \App\Models\LokasiRuangan::insert([
            ['nama_ruangan' => 'Ruang Kepala Sekolah', 'penanggung_jawab' => 'Kepala Sekolah'],
            ['nama_ruangan' => 'Ruang Guru', 'penanggung_jawab' => 'Ketua PGRI'],
            ['nama_ruangan' => 'Lab Komputer', 'penanggung_jawab' => 'Guru TIK'],
            ['nama_ruangan' => 'Perpustakaan', 'penanggung_jawab' => 'Pustakawan'],
            ['nama_ruangan' => 'Gudang Sarpras', 'penanggung_jawab' => 'Staff Sarpras'],
        ]);
    }
}
