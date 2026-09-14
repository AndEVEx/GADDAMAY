<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Rombel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $kelasName = $row['kelas'] ?? $row['rombel'] ?? null;
        if (!$kelasName) {
            return null;
        }

        $rombel = Rombel::where('nama_kelas', $kelasName)->first();
        if (!$rombel) {
            return null;
        }

        return new Siswa([
            'nama' => $row['nama'] ?? '',
            'nis' => $row['nis'] ?? null,
            'rombel_id' => $rombel->id,
        ]);
    }
}
