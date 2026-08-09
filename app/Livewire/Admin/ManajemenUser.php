<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Hash;

#[Layout('components.layouts.app')]
#[Title('Manajemen User')]
class ManajemenUser extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $filterRole = '';
    public bool $showForm = false;
    public bool $editing = false;
    public string $editId = '';
    public string $nama = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'guru';
    public bool $confirmDelete = false;
    public string $deleteId = '';
    public string $deleteName = '';
    public $importFile;

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterRole() { $this->resetPage(); }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        $this->editId = $user->id;
        $this->nama = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->role = $user->role;
        $this->editing = true;
        $this->showForm = true;
    }

    public function save()
    {
        $rules = [
            'nama' => 'required|min:3',
            'email' => 'required|email|unique:user,email,' . ($this->editing ? $this->editId : 'NULL') . ',id',
            'role' => 'required|in:admin,guru,kepsek,waka,ketua_mgmp,ketua_kelas',
        ];

        if (!$this->editing) {
            $rules['password'] = 'required|min:6';
        }

        $this->validate($rules);

        if ($this->editing) {
            $user = User::findOrFail($this->editId);
            $data = ['name' => $this->nama, 'email' => $this->email, 'role' => $this->role];
            if (!empty($this->password)) {
                $data['password'] = Hash::make($this->password);
            }
            $user->update($data);
            AuditLogService::logUpdate($user);
            $this->dispatch('show-toast', message: 'User berhasil diperbarui!', type: 'success');
        } else {
            $user = User::create([
                'name' => $this->nama,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => $this->role,
            ]);
            AuditLogService::logCreate($user);
            $this->dispatch('show-toast', message: 'User berhasil ditambahkan!', type: 'success');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function confirmDeleteUser(string $id)
    {
        $user = User::findOrFail($id);
        $this->deleteId = $id;
        $this->deleteName = $user->name;
        $this->confirmDelete = true;
    }

    public function deleteUser()
    {
        $user = User::findOrFail($this->deleteId);
        AuditLogService::logDelete($user);
        $user->delete();
        $this->confirmDelete = false;
        $this->dispatch('show-toast', message: 'User berhasil dihapus!', type: 'success');
    }

    public function resetPassword(string $id)
    {
        $user = User::findOrFail($id);
        $user->update(['password' => Hash::make('password123')]);
        $this->dispatch('show-toast', message: "Password {$user->name} direset ke 'password123'", type: 'info');
    }

    private function resetForm()
    {
        $this->reset(['nama', 'email', 'password', 'role', 'editId', 'editing']);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data User');
        $sheet->setCellValue('A1', 'nama');
        $sheet->setCellValue('B1', 'email');
        $sheet->setCellValue('C1', 'password');
        $sheet->setCellValue('D1', 'role');
        $sheet->setCellValue('A2', 'Ahmad Admin');
        $sheet->setCellValue('B2', 'admin@smkn2indramayu.sch.id');
        $sheet->setCellValue('C2', 'password123');
        $sheet->setCellValue('D2', 'admin');
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getStyle('A1:D1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('A1:D1')->getFont()->getColor()->setRGB('FFFFFF');
        foreach (['A','B','C','D'] as $col) $sheet->getColumnDimension($col)->setAutoSize(true);
        $path = storage_path('app/template_import_user.xlsx');
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save($path);
        return response()->download($path, 'template_import_user.xlsx')->deleteFileAfterSend(true);
    }

    public function importData()
    {
        $this->validate(['importFile' => 'required|file|mimes:xlsx,xls,csv|max:10240']);
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->importFile->getRealPath());
            $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            array_shift($rows);
            $count = 0;
            foreach ($rows as $row) {
                $nama = trim($row['A'] ?? '');
                $email = trim($row['B'] ?? '');
                $password = trim($row['C'] ?? 'password123');
                $role = User::normalizeRole(trim($row['D'] ?? 'guru'));
                if (empty($nama) || empty($email)) continue;
                User::updateOrCreate(
                    ['email' => $email],
                    ['name' => $nama, 'password' => Hash::make($password), 'role' => $role]
                );
                $count++;
            }
            $this->reset('importFile');
            $this->dispatch('show-toast', message: "Berhasil import {$count} user!", type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('show-toast', message: 'Gagal import: ' . $e->getMessage(), type: 'danger');
        }
    }

    public function exportExcel()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data User');

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama');
        $sheet->setCellValue('C1', 'Email');
        $sheet->setCellValue('D1', 'Role');
        $sheet->setCellValue('E1', 'Tanggal Dibuat');
        
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('A1:E1')->getFont()->getColor()->setRGB('FFFFFF');

        $users = User::when($this->search, function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            })
            ->when($this->filterRole, fn($q) => $q->where('role', $this->filterRole))
            ->orderBy('name')->get();

        $row = 2;
        $no = 1;
        foreach ($users as $user) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $user->name);
            $sheet->setCellValue('C' . $row, $user->email);
            $sheet->setCellValue('D' . $row, ucfirst(str_replace('_', ' ', $user->role)));
            $sheet->setCellValue('E' . $row, $user->created_at?->format('Y-m-d H:i:s') ?? '-');
            $row++;
        }

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'export_user_' . date('Y-m-d') . '.xlsx';
        $path = storage_path('app/' . $filename);
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    public function render()
    {
        $users = User::when($this->search, function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            })
            ->when($this->filterRole, fn($q) => $q->where('role', $this->filterRole))
            ->orderBy('name')
            ->paginate(20);

        return view('livewire.admin.manajemen-user', ['users' => $users]);
    }
}
