<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Database\Seeders\XmlJadwalSeeder;
use App\Models\JadwalPelajaran;
use App\Models\JadwalGuru;
use App\Models\Siswa;
use App\Models\Rombel;
use App\Models\MataPelajaran;
use App\Models\User;
use App\Models\TujuanPembelajaran;
use App\Imports\KktpImport;
use App\Imports\SiswaImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DirectImportController extends Controller
{
    public function importJadwal(Request $request)
    {
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');

        $request->validate([
            'xmlFile' => 'required|file|max:10240',
        ]);

        try {
            $file = $request->file('xmlFile');
            $tempPath = storage_path('app/temp_jadwal.xml');
            $file->move(storage_path('app'), 'temp_jadwal.xml');

            config(['app.xml_jadwal_path' => $tempPath]);

            JadwalGuru::truncate();
            JadwalPelajaran::truncate();

            Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\XmlJadwalSeeder', '--force' => true]);

            @unlink($tempPath);

            return redirect()->back()->with('success', 'Import jadwal XML berhasil diselesaikan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import jadwal: ' . $e->getMessage());
        }
    }

    public function importSiswa(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new SiswaImport, $request->file('file')->getRealPath());
            return redirect()->back()->with('success', 'Data siswa berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import siswa: ' . $e->getMessage());
        }
    }

    public function importKktp(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
        ]);

        try {
            $importer = new KktpImport();
            $importer->parse($request->file('file')->getRealPath());
            $count = $importer->import($request->input('mapel_id'));

            return redirect()->back()->with('success', "Berhasil import {$count} Tujuan Pembelajaran!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import KKTP: ' . $e->getMessage());
        }
    }

    public function importGuru(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv|max:10240']);
        try {
            $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
            $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            array_shift($rows);
            $count = 0;
            $passHash = Hash::make('password123');

            foreach ($rows as $row) {
                $nama = trim($row['A'] ?? '');
                $email = trim($row['B'] ?? '');
                $pass = trim($row['C'] ?? '');
                if (empty($nama) || empty($email)) continue;

                User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => $nama,
                        'password' => !empty($pass) ? Hash::make($pass) : $passHash,
                        'role' => 'guru'
                    ]
                );
                $count++;
            }
            return redirect()->back()->with('success', "Berhasil import {$count} data guru!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import guru: ' . $e->getMessage());
        }
    }

    public function importKelas(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv|max:10240']);
        try {
            $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
            $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            array_shift($rows);
            $count = 0;

            foreach ($rows as $row) {
                $nama_kelas = trim($row['A'] ?? '');
                $tingkat = trim($row['B'] ?? '10');
                if (empty($nama_kelas)) continue;

                Rombel::updateOrCreate(
                    ['nama_kelas' => $nama_kelas],
                    ['tingkat' => (int) $tingkat]
                );
                $count++;
            }
            return redirect()->back()->with('success', "Berhasil import {$count} kelas!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import kelas: ' . $e->getMessage());
        }
    }

    public function importMapel(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv|max:10240']);
        try {
            $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
            $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            array_shift($rows);
            $count = 0;

            foreach ($rows as $row) {
                $nama_mapel = trim($row['A'] ?? '');
                $kode_mapel = trim($row['B'] ?? '');
                if (empty($nama_mapel)) continue;

                MataPelajaran::updateOrCreate(
                    ['nama_mapel' => $nama_mapel],
                    ['kode_mapel' => $kode_mapel]
                );
                $count++;
            }
            return redirect()->back()->with('success', "Berhasil import {$count} mata pelajaran!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import mapel: ' . $e->getMessage());
        }
    }

    public function importUser(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv|max:10240']);
        try {
            $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
            $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            array_shift($rows);
            $count = 0;
            $passHash = Hash::make('password123');

            foreach ($rows as $row) {
                $nama = trim($row['A'] ?? '');
                $email = trim($row['B'] ?? '');
                $pass = trim($row['C'] ?? '');
                $role = trim($row['D'] ?? 'guru');
                if (empty($nama) || empty($email)) continue;

                User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => $nama,
                        'password' => !empty($pass) ? Hash::make($pass) : $passHash,
                        'role' => $role
                    ]
                );
                $count++;
            }
            return redirect()->back()->with('success', "Berhasil import {$count} user!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import user: ' . $e->getMessage());
        }
    }
}
