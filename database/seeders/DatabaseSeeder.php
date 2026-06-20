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
            ['nama_ruangan' => 'Lab Komputer', 'penanggung_jawab' => 'Guru TIK', 'kode_ruangan' => '18.72.01.01.04.0001.1.8.1'],
            ['nama_ruangan' => 'Ruang Aula', 'penanggung_jawab' => 'Guru', 'kode_ruangan' => '18.72.01.02.02.0001.2.28'],
            ['nama_ruangan' => 'Kelas 1 Al Malik', 'penanggung_jawab' => 'Wali Kelas'   , 'kode_ruangan' => '18.72.01.02.02.0001.1.1.22'],
        ]);

        $this->call([
            MasterKodeAsetSeeder::class,
        ]);
    }
}
