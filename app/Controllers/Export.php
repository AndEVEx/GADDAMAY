<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Pegawai_model;
use App\Models\Lembur_model;
use App\Models\Absensi_model;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
 
class Export extends BaseController
{
    public function harian()
    {
        $model = new Absensi_model;
        $m_pegawai = new Pegawai_model;
        $tanggal = $this->request->getPost('tgl');
        $users = $m_pegawai->getPegawaiall();

        $spreadsheet = new Spreadsheet();
        
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Nomor Finger')
            ->setCellValue('C1', 'Nama Pegawai')
            ->setCellValue('D1', 'Jenis Kerja')
            ->setCellValue('E1', 'Jam Masuk')
            ->setCellValue('F1', 'Status')
            ->setCellValue('G1', 'Jam Pulang')
            ->setCellValue('H1', 'Total Jam');
        
            function jammasuk($id_ptk,$tgl) {
                //cek apakah ada absen masuk
                $db = \Config\Database::connect();
                $builder = $db->table('tweb_pegawai_hadir');
                $builder->where('ID_PEGAWAI', $id_ptk);
                $builder->where('STATUS', 0);
                $builder->where('TANGGAL', $tgl);
                $all =  $builder->countAllResults();
                if($all>0){
                    //ambil jam masuk
                    $query = $db->query("SELECT JAM FROM tweb_pegawai_hadir where ID_PEGAWAI='$id_ptk' and TANGGAL='$tgl' and STATUS=0");
                    $row = $query->getRow();
                    $sts=$row->JAM;  
                }else{
                    $sts="";
                }
                return $sts;
            }
            function jampulang($id_ptk,$tgl) {
                //cek apakah ada absen masuk
                $db = \Config\Database::connect();
                $builder = $db->table('tweb_pegawai_hadir');
                $builder->where('ID_PEGAWAI', $id_ptk);
                $builder->where('STATUS', 1);
                $builder->where('TANGGAL', $tgl);
                $all =  $builder->countAllResults();
                if($all>0){
                    //ambil jam masuk
                    $query = $db->query("SELECT JAM FROM tweb_pegawai_hadir where ID_PEGAWAI='$id_ptk' and TANGGAL='$tgl' and STATUS=1");
                    $row = $query->getRow();
                    $sts=$row->JAM;  
                }else{
                    $sts="";
                }
                return $sts;
            }

            function sts_absen($id_ptk,$tgl) {
               //cek hari libur
    $namahari = date('l', strtotime($tgl));
    if($namahari=="Sunday"){
        $hari= "Minggu";
    }elseif($namahari=="Monday"){
        $hari= "Senin";
    }elseif($namahari=="Tuesday"){
        $hari= "Selasa";
    }elseif($namahari=="Wednesday"){
        $hari= "Rabu";
    }elseif($namahari=="Thursday"){
        $hari= "Kamis";
    }elseif($namahari=="Friday"){
        $hari= "Jumat";
    }elseif($namahari=="Saturday"){
        $hari= "Sabtu";
    }

     //cek apakah ada jadwal libur hari besar
     $db = \Config\Database::connect();
     $builder_lbrbesar = $db->table('libur_besar');
     $builder_lbrbesar->where('tgl_libur', $tgl);
     $liburbesar =  $builder_lbrbesar->countAllResults();
     if($liburbesar>0){
         $sts="Libur HB";
     }else{
        //cek apakah ada jadwal khusus
        $db = \Config\Database::connect();
        $builder_lbr = $db->table('jadwal_khusus');
        $builder_lbr->where('id_ptk', $id_ptk);
        $libur =  $builder_lbr->countAllResults();
        if($libur>0){
            //cek apakah hari sekarang dia libur
            $builder_stslbr = $db->table('jadwal_khusus');
            $builder_stslbr->where('id_ptk', $id_ptk);
            $builder_stslbr->where($hari, 99);
            $stslibur =  $builder_stslbr->countAllResults();
            if($stslibur>0){
                $sts="Libur";
            }else{
                //cek apakah ada absen masuk
                $builder = $db->table('tweb_pegawai_hadir');
                $builder->where('ID_PEGAWAI', $id_ptk);
                $builder->where('STATUS', 0);
                $builder->where('TANGGAL', $tgl);
                $all =  $builder->countAllResults();
                if($all>0){
                    //ambil jam masuk
                    $query = $db->query("SELECT JAM FROM tweb_pegawai_hadir
                    where ID_PEGAWAI='$id_ptk' and TANGGAL='$tgl' and STATUS=0");
                    $row = $query->getRow();

                    //ambil jam masuk jadwal shift
                    $query_jm = $db->query("SELECT $hari,jam_masuk FROM jadwal_khusus 
                    Join r_shift ON r_shift.id_shift = jadwal_khusus.$hari
                    where id_ptk='$id_ptk'");
                    $row_jm = $query_jm->getRow();

                    $jam = $row->JAM;
                    $jam_masuk = $row_jm->jam_masuk;
                    if($jam>$jam_masuk){
                        $sts="Terlambat";
                    }else{
                        $sts="Masuk";
                    }
                    
                }else{
                    //cek data di tweb_pegawai_absen
                    $builder_abs = $db->table('tweb_pegawai_absen');
                    $builder_abs->where('ID_PEGAWAI', $id_ptk);
                    $builder_abs->where('TANGGAL_ABSEN', $tgl);
                    $all_abs =  $builder_abs->countAllResults();
                    if($all_abs>0){
                        //cari nama relawan
                        $query = $db->query("SELECT STATUS FROM tweb_pegawai_absen where ID_PEGAWAI='$id_ptk' and TANGGAL_ABSEN='$tgl'");
                        $row = $query->getRow();
                        $status = $row->STATUS;
                        if($status==2){
                            $sts="Sakit";
                        }elseif($status==3){
                            $sts="Izin";
                        }else{
                            $sts="Alpha";
                        }
                    }else{
                        $sts="Alpha";
                    }
                }
            }
        }else{
            //karywan umum
                $query = $db->query("SELECT sts_hari,jammasuk FROM r_hari where nm_hari='$hari'");
                $row = $query->getRow();
                $sts = $row->sts_hari;
                if($sts==2){
                    $sts="Libur";
                }else{
                    //cek apakah ada absen masuk
                    $builder = $db->table('tweb_pegawai_hadir');
                    $builder->where('ID_PEGAWAI', $id_ptk);
                    $builder->where('STATUS', 0);
                    $builder->where('TANGGAL', $tgl);
                    $all =  $builder->countAllResults();
                    if($all>0){
                        //ambil jam masuk
                        $query_jam = $db->query("SELECT JAM FROM tweb_pegawai_hadir 
                        where ID_PEGAWAI='$id_ptk' and TANGGAL='$tgl' and STATUS=0");
                        $row_jam = $query_jam->getRow();
                        $jam = $row_jam->JAM;
                        $jam_masuk = $row->jammasuk;
                        if($jam>$jam_masuk){
                            $sts="Terlambat";
                        }else{
                            $sts="Masuk";
                        }
                        
                    }else{
                        //cek data di tweb_pegawai_absen
                        $builder_abs = $db->table('tweb_pegawai_absen');
                        $builder_abs->where('ID_PEGAWAI', $id_ptk);
                        $builder_abs->where('TANGGAL_ABSEN', $tgl);
                        $all_abs =  $builder_abs->countAllResults();
                        if($all_abs>0){
                            //cari nama relawan
                            $query = $db->query("SELECT STATUS FROM tweb_pegawai_absen where ID_PEGAWAI='$id_ptk' and TANGGAL_ABSEN='$tgl'");
                            $row = $query->getRow();
                            $status = $row->STATUS;
                            if($status==2){
                                $sts="Sakit";
                            }elseif($status==3){
                                $sts="Izin";
                            }else{
                                $sts="Alpha";
                            }
                        }else{
                            $sts="Alpha";
                        }
                    }
                }
            
           
        }
     }
            
                return $sts;
            }
            

        $column = 2;
        $no=0;
        foreach ($users as $data) {
            $no++;
            $induk =$data['nomor_absensi'];
            $id =$data['id_ptk'];
            $jammasuk = jammasuk($id,$tanggal);
            $jampulang = jampulang($id,$tanggal);

            if(empty($jampulang)){
                $jam    =0;
                $menit    =0;
            }else{
                $diff    =strtotime($jampulang) - strtotime($jammasuk);
                $jam    =floor($diff / (60 * 60));
                $menit    =$diff - $jam * (60 * 60);
            }
           

            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $column, $no)
                ->setCellValue('B' . $column, $data['nomor_absensi'])
                ->setCellValue('C' . $column, $data['nama_ptk'])
                ->setCellValue('D' . $column, $data['nama_jenis_ptk'])
                ->setCellValue('E' . $column, $jammasuk)
                ->setCellValue('F' . $column, sts_absen($id,$tanggal))
                ->setCellValue('G' . $column, $jampulang)
                ->setCellValue('H' . $column, $jam.' jam,'.floor( $menit / 60 ).' menit');

            $column++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = date('Y-m-d-His'). '-Data-Absensi-Harian';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }
    public function bulanan()
    {
        $model = new Pegawai_model;
        $getBulan = $this->request->getPost('bln');
        $users = $model->getPegawai();
       
        $spreadsheet = new Spreadsheet();
        
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Nomor Finger')
            ->setCellValue('C1', 'Nama Pegawai')
            ->setCellValue('D1', 'Jenis Kerja')
            ->setCellValue('E1', 'Masuk')
            ->setCellValue('F1', 'Terlambat')
            ->setCellValue('G1', 'Sakit')
            ->setCellValue('H1', 'Izin')
            ->setCellValue('I1', 'Alpha');
        
            function harilibur($kode) {
                if($kode=="Sunday"){
                    $hari= "Minggu";
                }elseif($kode=="Monday"){
                    $hari= "Senin";
                }elseif($kode=="Tuesday"){
                    $hari= "Selasa";
                }elseif($kode=="Wednesday"){
                    $hari= "Rabu";
                }elseif($kode=="Thursday"){
                    $hari= "Kamis";
                }elseif($kode=="Friday"){
                    $hari= "Jumat";
                }elseif($kode=="Saturday"){
                    $hari= "Sabtu";
                }
                $db = \Config\Database::connect();
                $query = $db->query("SELECT sts_hari FROM r_hari where nm_hari='$hari'");
                $row = $query->getRow();
                $sts = $row->sts_hari;
               
                return $sts;
            }

            function sts_absen($id_ptk,$tgl) {
                $tglpil = strtotime($tgl);
                $tglnow = strtotime(date('Y-m-d'));
                //cek hari libur
                $namahari = date('l', strtotime($tgl));
                if($namahari=="Sunday"){
                    $hari= "Minggu";
                }elseif($namahari=="Monday"){
                    $hari= "Senin";
                }elseif($namahari=="Tuesday"){
                    $hari= "Selasa";
                }elseif($namahari=="Wednesday"){
                    $hari= "Rabu";
                }elseif($namahari=="Thursday"){
                    $hari= "Kamis";
                }elseif($namahari=="Friday"){
                    $hari= "Jumat";
                }elseif($namahari=="Saturday"){
                    $hari= "Sabtu";
                }
            
                if($tglpil>$tglnow){
                    $sts="-";
                }else{
            
                    //cek apakah ada jadwal libur hari besar
                    $db = \Config\Database::connect();
                    $builder_lbrbesar = $db->table('libur_besar');
                    $builder_lbrbesar->where('tgl_libur', $tgl);
                    $liburbesar =  $builder_lbrbesar->countAllResults();
                    if($liburbesar>0){
                        $sts="Libur HB";
                    }else{
                        //cek apakah ada jadwal khusus
                        $db = \Config\Database::connect();
                        $builder_lbr = $db->table('jadwal_khusus');
                        $builder_lbr->where('id_ptk', $id_ptk);
                        $libur =  $builder_lbr->countAllResults();
                        if($libur>0){
                            //cek apakah hari sekarang dia libur
                            $builder_stslbr = $db->table('jadwal_khusus');
                            $builder_stslbr->where('id_ptk', $id_ptk);
                            $builder_stslbr->where($hari, 99);
                            $stslibur =  $builder_stslbr->countAllResults();
                            if($stslibur>0){
                                $sts="Libur";
                            }else{
                                //cek apakah ada absen masuk
                                $builder = $db->table('tweb_pegawai_hadir');
                                $builder->where('ID_PEGAWAI', $id_ptk);
                                $builder->where('STATUS', 0);
                                $builder->where('TANGGAL', $tgl);
                                $all =  $builder->countAllResults();
                                if($all>0){
                                    //ambil jam masuk
                                    $query = $db->query("SELECT JAM FROM tweb_pegawai_hadir
                                    where ID_PEGAWAI='$id_ptk' and TANGGAL='$tgl' and STATUS=0");
                                    $row = $query->getRow();
            
                                    //ambil jam masuk jadwal shift
                                    $query_shift = $db->query("SELECT $hari FROM jadwal_khusus 
                                    where id_ptk='$id_ptk'");
                                    $row_shift = $query_shift->getRow();
                                    $shift_ptk = $row_shift->$hari;
            
                                    //ambil jam masuk shift
                                    $query_jm = $db->query("SELECT jam_masuk FROM r_shift 
                                    where id_shift='$shift_ptk'");
                                    $row_jm = $query_jm->getRow();
            
                                    $jam = $row->JAM;
                                    $jam_masuk = $row_jm->jam_masuk;
                                    if($jam>$jam_masuk){
                                        $sts="Terlambat";
                                    }else{
                                        $sts="Masuk";
                                    }
                                    
                                }else{
                                    //cek data di tweb_pegawai_absen
                                    $builder_abs = $db->table('tweb_pegawai_absen');
                                    $builder_abs->where('ID_PEGAWAI', $id_ptk);
                                    $builder_abs->where('TANGGAL_ABSEN', $tgl);
                                    $all_abs =  $builder_abs->countAllResults();
                                    if($all_abs>0){
                                        //cari nama relawan
                                        $query = $db->query("SELECT STATUS FROM tweb_pegawai_absen where ID_PEGAWAI='$id_ptk' and TANGGAL_ABSEN='$tgl'");
                                        $row = $query->getRow();
                                        $status = $row->STATUS;
                                        if($status==2){
                                            $sts="Sakit";
                                        }elseif($status==3){
                                            $sts="Izin";
                                        }else{
                                            $sts="Alpha";
                                        }
                                    }else{
                                        $sts="Alpha";
                                    }
                                }
                            }
                        }else{
                            //karywan umum
                                $query = $db->query("SELECT sts_hari,jammasuk FROM r_hari where nm_hari='$hari'");
                                $row = $query->getRow();
                                $sts = $row->sts_hari;
                                if($sts==2){
                                    $sts="Libur";
                                }else{
                                    //cek apakah ada absen masuk
                                    $builder = $db->table('tweb_pegawai_hadir');
                                    $builder->where('ID_PEGAWAI', $id_ptk);
                                    $builder->where('STATUS', 0);
                                    $builder->where('TANGGAL', $tgl);
                                    $all =  $builder->countAllResults();
                                    if($all>0){
                                        //ambil jam masuk
                                        $query_jam = $db->query("SELECT JAM FROM tweb_pegawai_hadir 
                                        where ID_PEGAWAI='$id_ptk' and TANGGAL='$tgl' and STATUS=0");
                                        $row_jam = $query_jam->getRow();
                                        $jam = $row_jam->JAM;
                                        $jam_masuk = $row->jammasuk;
                                        if($jam>$jam_masuk){
                                            $sts="Terlambat";
                                        }else{
                                            $sts="Masuk";
                                        }
                                        
                                    }else{
                                        //cek data di tweb_pegawai_absen
                                        $builder_abs = $db->table('tweb_pegawai_absen');
                                        $builder_abs->where('ID_PEGAWAI', $id_ptk);
                                        $builder_abs->where('TANGGAL_ABSEN', $tgl);
                                        $all_abs =  $builder_abs->countAllResults();
                                        if($all_abs>0){
                                            //cari nama relawan
                                            $query = $db->query("SELECT STATUS FROM tweb_pegawai_absen where ID_PEGAWAI='$id_ptk' and TANGGAL_ABSEN='$tgl'");
                                            $row = $query->getRow();
                                            $status = $row->STATUS;
                                            if($status==2){
                                                $sts="Sakit";
                                            }elseif($status==3){
                                                $sts="Izin";
                                            }else{
                                                $sts="Alpha";
                                            }
                                        }else{
                                            $sts="Alpha";
                                        }
                                    }
                                }
                            
                        
                        }
                    }
            
                }
                 
            
                return $sts;
            }
            function jummasukbln($id_ptk,$bln) {
                //cek apakah ada absen masuk
                $db = \Config\Database::connect();
                $builder = $db->table('tweb_pegawai_hadir');
                $builder->where('ID_PEGAWAI', $id_ptk);
                $builder->where('STATUS', 0);
                $builder->where('MONTH(TANGGAL)', $bln);
                $all =  $builder->countAllResults();
                if($all>0){
                   $sts = $all;
                }else{
                    $sts=0;
                }
                return $sts;
            }

            function jumsakitbln($id_ptk,$bln) {
                //cek apakah ada absen sakit
                $db = \Config\Database::connect();
                $builder = $db->table('tweb_pegawai_absen');
                $builder->where('ID_PEGAWAI', $id_ptk);
                $builder->where('STATUS', 2);
                $builder->where('MONTH(TANGGAL_ABSEN)', $bln);
                $all =  $builder->countAllResults();
                if($all>0){
                   $sts = $all;
                }else{
                    $sts=0;
                }
                return $sts;
            }

            function jumizinbln($id_ptk,$bln) {
                //cek apakah ada absen izin
                $db = \Config\Database::connect();
                $builder = $db->table('tweb_pegawai_absen');
                $builder->where('ID_PEGAWAI', $id_ptk);
                $builder->where('STATUS', 3);
                $builder->where('MONTH(TANGGAL_ABSEN)', $bln);
                $all =  $builder->countAllResults();
                if($all>0){
                   $sts = $all;
                }else{
                    $sts=0;
                }
                return $sts;
            }
            function jumalphabln($idptk,$bln) {
                $thn = date('Y');
                $bulan = date('m');
                $sumalpha = 0;
              
                if($bulan==$bln){
                    $jumHari = date('d');
                }else{
                    $jumHari = cal_days_in_month(CAL_GREGORIAN, $bln, $thn);
                }
            
                if($bln>$bulan){
                    $alpha = 0;
                }else{
                    for($i=1;$i<=$jumHari;$i++){
                        $tanggal = date('Y').'-'.$bln.'-'.$i;
                        $sts = sts_absen($idptk,$tanggal);
                        if($sts=='Alpha'){
                            $sumalpha++;
                        }
                    }
                }
            
                $alpha = $sumalpha;
                return $alpha;
            }
            function jumterlambatbln($id_ptk,$bln) {
                $thn = date('Y');
                $bulan = date('m');
                $sum = 0;
              
                if($bulan==$bln){
                    $jumHari = date('d');
                }else{
                    $jumHari = cal_days_in_month(CAL_GREGORIAN, $bln, $thn);
                }
            
                if($bln>$bulan){
                    $terlambat = 0;
                }else{
                    for($i=1;$i<=$jumHari;$i++){
                        $tanggal = date('Y').'-'.$bln.'-'.$i;
                        $sts = sts_absen($id_ptk,$tanggal);
                        if($sts=='Terlambat'){
                            $sum++;
                        }
                    }
                    $terlambat = $sum;
                }
                
                return $terlambat;
            }
            
        $column = 2;
        $no=0;
        foreach ($users as $data) {
            $no++;
            $id =$data['id_ptk'];
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $column, $no)
                ->setCellValue('B' . $column, $data['nomor_absensi'])
                ->setCellValue('C' . $column, $data['nama_ptk'])
                ->setCellValue('D' . $column, $data['nama_jenis_ptk'])
                ->setCellValue('E' . $column, jummasukbln($id,$getBulan))
                ->setCellValue('F' . $column, jumterlambatbln($id,$getBulan))
                ->setCellValue('G' . $column, jumsakitbln($id,$getBulan))
                ->setCellValue('H' . $column, jumizinbln($id,$getBulan))
                ->setCellValue('I' . $column, jumalphabln($id,$getBulan));

            $column++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = date('Y-m-d-His'). '-Data-Absensi';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }
    public function pertanggal()
    {
        $model = new Pegawai_model;
        $tgl1 = $this->request->getPost('tgl1');
        $tgl2 = $this->request->getPost('tgl2');
        $users = $model->getPegawai();
       
        $spreadsheet = new Spreadsheet();
        
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Nomor Finger')
            ->setCellValue('D1', 'Nama Pegawai')
            ->setCellValue('E1', 'Jenis Kerja')
            ->setCellValue('F1', 'Masuk')
            ->setCellValue('G1', 'Terlambat')
            ->setCellValue('H1', 'Sakit')
            ->setCellValue('I1', 'Izin')
            ->setCellValue('J1', 'Alpha');
        
            function harilibur($kode) {
                if($kode=="Sunday"){
                    $hari= "Minggu";
                }elseif($kode=="Monday"){
                    $hari= "Senin";
                }elseif($kode=="Tuesday"){
                    $hari= "Selasa";
                }elseif($kode=="Wednesday"){
                    $hari= "Rabu";
                }elseif($kode=="Thursday"){
                    $hari= "Kamis";
                }elseif($kode=="Friday"){
                    $hari= "Jumat";
                }elseif($kode=="Saturday"){
                    $hari= "Sabtu";
                }
                $db = \Config\Database::connect();
                $query = $db->query("SELECT sts_hari FROM r_hari where nm_hari='$hari'");
                $row = $query->getRow();
                $sts = $row->sts_hari;
               
                return $sts;
            }

            function sts_absen($id_ptk,$tgl) {
                //cek hari libur
    $namahari = date('l', strtotime($tgl));
    if($namahari=="Sunday"){
        $hari= "Minggu";
    }elseif($namahari=="Monday"){
        $hari= "Senin";
    }elseif($namahari=="Tuesday"){
        $hari= "Selasa";
    }elseif($namahari=="Wednesday"){
        $hari= "Rabu";
    }elseif($namahari=="Thursday"){
        $hari= "Kamis";
    }elseif($namahari=="Friday"){
        $hari= "Jumat";
    }elseif($namahari=="Saturday"){
        $hari= "Sabtu";
    }

     //cek apakah ada jadwal libur hari besar
     $db = \Config\Database::connect();
     $builder_lbrbesar = $db->table('libur_besar');
     $builder_lbrbesar->where('tgl_libur', $tgl);
     $liburbesar =  $builder_lbrbesar->countAllResults();
     if($liburbesar>0){
         $sts="Libur HB";
     }else{
        //cek apakah ada jadwal khusus
        $db = \Config\Database::connect();
        $builder_lbr = $db->table('jadwal_khusus');
        $builder_lbr->where('id_ptk', $id_ptk);
        $libur =  $builder_lbr->countAllResults();
        if($libur>0){
            //cek apakah hari sekarang dia libur
            $builder_stslbr = $db->table('jadwal_khusus');
            $builder_stslbr->where('id_ptk', $id_ptk);
            $builder_stslbr->where($hari, 99);
            $stslibur =  $builder_stslbr->countAllResults();
            if($stslibur>0){
                $sts="Libur";
            }else{
                //cek apakah ada absen masuk
                $builder = $db->table('tweb_pegawai_hadir');
                $builder->where('ID_PEGAWAI', $id_ptk);
                $builder->where('STATUS', 0);
                $builder->where('TANGGAL', $tgl);
                $all =  $builder->countAllResults();
                if($all>0){
                    //ambil jam masuk
                    $query = $db->query("SELECT JAM FROM tweb_pegawai_hadir
                    where ID_PEGAWAI='$id_ptk' and TANGGAL='$tgl' and STATUS=0");
                    $row = $query->getRow();

                    //ambil jam masuk jadwal shift
                    $query_jm = $db->query("SELECT $hari FROM jadwal_khusus 
                    Join r_shift ON r_shift.id_shift = jadwal_khusus.$hari
                    where id_ptk='$id_ptk'");
                    $row_jm = $query_jm->getRow();

                    $jam = $row->JAM;
                    $jam_masuk = $row_jm->jam_masuk;
                    if($jam>$jam_masuk){
                        $sts="Terlambat";
                    }else{
                        $sts="Masuk";
                    }
                    
                }else{
                    //cek data di tweb_pegawai_absen
                    $builder_abs = $db->table('tweb_pegawai_absen');
                    $builder_abs->where('ID_PEGAWAI', $id_ptk);
                    $builder_abs->where('TANGGAL_ABSEN', $tgl);
                    $all_abs =  $builder_abs->countAllResults();
                    if($all_abs>0){
                        //cari nama relawan
                        $query = $db->query("SELECT STATUS FROM tweb_pegawai_absen where ID_PEGAWAI='$id_ptk' and TANGGAL_ABSEN='$tgl'");
                        $row = $query->getRow();
                        $status = $row->STATUS;
                        if($status==2){
                            $sts="Sakit";
                        }elseif($status==3){
                            $sts="Izin";
                        }else{
                            $sts="Alpha";
                        }
                    }else{
                        $sts="Alpha";
                    }
                }
            }
        }else{
            //karywan umum
                $query = $db->query("SELECT sts_hari,jammasuk FROM r_hari where nm_hari='$hari'");
                $row = $query->getRow();
                $sts = $row->sts_hari;
                if($sts==2){
                    $sts="Libur";
                }else{
                    //cek apakah ada absen masuk
                    $builder = $db->table('tweb_pegawai_hadir');
                    $builder->where('ID_PEGAWAI', $id_ptk);
                    $builder->where('STATUS', 0);
                    $builder->where('TANGGAL', $tgl);
                    $all =  $builder->countAllResults();
                    if($all>0){
                        //ambil jam masuk
                        $query_jam = $db->query("SELECT JAM FROM tweb_pegawai_hadir 
                        where ID_PEGAWAI='$id_ptk' and TANGGAL='$tgl' and STATUS=0");
                        $row_jam = $query_jam->getRow();
                        $jam = $row_jam->JAM;
                        $jam_masuk = $row->jammasuk;
                        if($jam>$jam_masuk){
                            $sts="Terlambat";
                        }else{
                            $sts="Masuk";
                        }
                        
                    }else{
                        //cek data di tweb_pegawai_absen
                        $builder_abs = $db->table('tweb_pegawai_absen');
                        $builder_abs->where('ID_PEGAWAI', $id_ptk);
                        $builder_abs->where('TANGGAL_ABSEN', $tgl);
                        $all_abs =  $builder_abs->countAllResults();
                        if($all_abs>0){
                            //cari nama relawan
                            $query = $db->query("SELECT STATUS FROM tweb_pegawai_absen where ID_PEGAWAI='$id_ptk' and TANGGAL_ABSEN='$tgl'");
                            $row = $query->getRow();
                            $status = $row->STATUS;
                            if($status==2){
                                $sts="Sakit";
                            }elseif($status==3){
                                $sts="Izin";
                            }else{
                                $sts="Alpha";
                            }
                        }else{
                            $sts="Alpha";
                        }
                    }
                }
            
           
        }
     }
            
                return $sts;
            }
            function jummasukpertanggal($id_ptk,$tgl1,$tgl2) {
                //cek apakah ada absen masuk
                $db = \Config\Database::connect();
                $builder = $db->table('tweb_pegawai_hadir');
                $builder->where('ID_PEGAWAI', $id_ptk);
                $builder->where('STATUS', 0);
                $builder->where('TANGGAL >=', $tgl1);
                $builder->where('TANGGAL <=', $tgl2);
                $all =  $builder->countAllResults();
                if($all>0){
                   $sts = $all;
                }else{
                    $sts=0;
                }
                return $sts;
            }

            function jumsakitpertanggal($id_ptk,$tgl1,$tgl2) {
                //cek apakah ada absen sakit
                $db = \Config\Database::connect();
                $builder = $db->table('tweb_pegawai_absen');
                $builder->where('ID_PEGAWAI', $id_ptk);
                $builder->where('STATUS', 2);
                $builder->where('TANGGAL_ABSEN >=', $tgl1);
                $builder->where('TANGGAL_ABSEN <=', $tgl2);
                $all =  $builder->countAllResults();
                if($all>0){
                   $sts = $all;
                }else{
                    $sts=0;
                }
                return $sts;
            }

            function jumizinpertanggal($id_ptk,$tgl1,$tgl2) {
                //cek apakah ada absen izin
                $db = \Config\Database::connect();
                $builder = $db->table('tweb_pegawai_absen');
                $builder->where('ID_PEGAWAI', $id_ptk);
                $builder->where('STATUS', 3);
                $builder->where('TANGGAL_ABSEN >=', $tgl1);
                $builder->where('TANGGAL_ABSEN <=', $tgl2);
                $all =  $builder->countAllResults();
                if($all>0){
                   $sts = $all;
                }else{
                    $sts=0;
                }
                return $sts;
            }
            function jumalphapertanggal($id_ptk,$tgl1,$tgl2) {
                $tg1 = $tgl1;
                $tg2 = $tgl2;
                $sumalpha = 0;
            
                $jml = 0;
            
                while (strtotime($tg1) <= strtotime($tg2)) {
                    $jml++;
                    $tanggal = $tg1;
                    $tg1 = date ("Y-m-d", strtotime("+1 day", strtotime($tg1)));//looping tambah 1 date
                    $sts = sts_absen($id_ptk,$tanggal);
                    if($sts=='Alpha'){
                        $sumalpha++;
                    }
                }
            
                $alpha = $sumalpha;
                return $alpha;
            }
            function jumterlambatpertanggal($id_ptk,$tgl1,$tgl2) {
                $tg1 = $tgl1;
                $tg2 = $tgl2;
                $sumterlambat = 0;
            
                $jml = 0;
                
                while (strtotime($tg1) <= strtotime($tg2)) {
                    $jml++;
                    $tanggal = $tg1;
                    $tg1 = date ("Y-m-d", strtotime("+1 day", strtotime($tg1)));//looping tambah 1 date
                    $sts = sts_absen($id_ptk,$tanggal);
                    if($sts=='Terlambat'){
                        $sumterlambat++;
                    }
                }
            
                $terlambat = $sumterlambat;
                return $terlambat;
            }
            
        $column = 2;
        $no=0;
        foreach ($users as $data) {
            $no++;
            $id =$data['id_ptk'];
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $column, $no)
                ->setCellValue('B' . $column, $data['nomor_absensi'])
                ->setCellValue('C' . $column, $data['nama_ptk'])
                ->setCellValue('D' . $column, $data['nama_jenis_ptk'])
                ->setCellValue('E' . $column, jummasukpertanggal($id,$tgl1,$tgl2))
                ->setCellValue('R' . $column, jumterlambatpertanggal($id,$tgl1,$tgl2))
                ->setCellValue('G' . $column, jumsakitpertanggal($id,$tgl1,$tgl2))
                ->setCellValue('H' . $column, jumizinpertanggal($id,$tgl1,$tgl2))
                ->setCellValue('I' . $column, jumalphapertanggal($id,$tgl1,$tgl2));

            $column++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = date('Y-m-d-His'). '-Data-Absensi';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function pertanggaluser()
    {
        $model = new Absensi_model;
        $tgl1 = $this->request->getPost('tgl1');
        $tgl2 = $this->request->getPost('tgl2');
        $id_ptk = $this->request->getVar('id_ptk');
        $users = $model->getAbsensitgluser($id_ptk, $tgl1, $tgl2);
       
        $spreadsheet = new Spreadsheet();
        
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Tanggal')
            ->setCellValue('C1', 'Jam Masuk')
            ->setCellValue('D1', 'Status')
            ->setCellValue('E1', 'Jam Pulang')
            ->setCellValue('F1', 'Total Jam');
        
            function jammasuk($id_ptk,$tgl) {
                //cek apakah ada absen masuk
                $db = \Config\Database::connect();
                $builder = $db->table('tweb_pegawai_hadir');
                $builder->where('ID_PEGAWAI', $id_ptk);
                $builder->where('STATUS', 0);
                $builder->where('TANGGAL', $tgl);
                $all =  $builder->countAllResults();
                if($all>0){
                    //ambil jam masuk
                    $query = $db->query("SELECT JAM FROM tweb_pegawai_hadir where ID_PEGAWAI='$id_ptk' and TANGGAL='$tgl' and STATUS=0");
                    $row = $query->getRow();
                    $sts=$row->JAM;  
                }else{
                    $sts="";
                }
                return $sts;
            }
            function jampulang($id_ptk,$tgl) {
                //cek apakah ada absen masuk
                $db = \Config\Database::connect();
                $builder = $db->table('tweb_pegawai_hadir');
                $builder->where('ID_PEGAWAI', $id_ptk);
                $builder->where('STATUS', 1);
                $builder->where('TANGGAL', $tgl);
                $all =  $builder->countAllResults();
                if($all>0){
                    //ambil jam masuk
                    $query = $db->query("SELECT JAM FROM tweb_pegawai_hadir where ID_PEGAWAI='$id_ptk' and TANGGAL='$tgl' and STATUS=1");
                    $row = $query->getRow();
                    $sts=$row->JAM;  
                }else{
                    $sts="";
                }
                return $sts;
            }
            
            function sts_absen($id_ptk,$tgl) {
                //cek hari libur
    $namahari = date('l', strtotime($tgl));
    if($namahari=="Sunday"){
        $hari= "Minggu";
    }elseif($namahari=="Monday"){
        $hari= "Senin";
    }elseif($namahari=="Tuesday"){
        $hari= "Selasa";
    }elseif($namahari=="Wednesday"){
        $hari= "Rabu";
    }elseif($namahari=="Thursday"){
        $hari= "Kamis";
    }elseif($namahari=="Friday"){
        $hari= "Jumat";
    }elseif($namahari=="Saturday"){
        $hari= "Sabtu";
    }

     //cek apakah ada jadwal libur hari besar
     $db = \Config\Database::connect();
     $builder_lbrbesar = $db->table('libur_besar');
     $builder_lbrbesar->where('tgl_libur', $tgl);
     $liburbesar =  $builder_lbrbesar->countAllResults();
     if($liburbesar>0){
         $sts="Libur HB";
     }else{
        //cek apakah ada jadwal khusus
        $db = \Config\Database::connect();
        $builder_lbr = $db->table('jadwal_khusus');
        $builder_lbr->where('id_ptk', $id_ptk);
        $libur =  $builder_lbr->countAllResults();
        if($libur>0){
            //cek apakah hari sekarang dia libur
            $builder_stslbr = $db->table('jadwal_khusus');
            $builder_stslbr->where('id_ptk', $id_ptk);
            $builder_stslbr->where($hari, 99);
            $stslibur =  $builder_stslbr->countAllResults();
            if($stslibur>0){
                $sts="Libur";
            }else{
                //cek apakah ada absen masuk
                $builder = $db->table('tweb_pegawai_hadir');
                $builder->where('ID_PEGAWAI', $id_ptk);
                $builder->where('STATUS', 0);
                $builder->where('TANGGAL', $tgl);
                $all =  $builder->countAllResults();
                if($all>0){
                    //ambil jam masuk
                    $query = $db->query("SELECT JAM FROM tweb_pegawai_hadir
                    where ID_PEGAWAI='$id_ptk' and TANGGAL='$tgl' and STATUS=0");
                    $row = $query->getRow();

                    //ambil jam masuk jadwal shift
                    $query_jm = $db->query("SELECT $hari FROM jadwal_khusus 
                    Join r_shift ON r_shift.id_shift = jadwal_khusus.$hari
                    where id_ptk='$id_ptk'");
                    $row_jm = $query_jm->getRow();

                    $jam = $row->JAM;
                    $jam_masuk = $row_jm->jam_masuk;
                    if($jam>$jam_masuk){
                        $sts="Terlambat";
                    }else{
                        $sts="Masuk";
                    }
                    
                }else{
                    //cek data di tweb_pegawai_absen
                    $builder_abs = $db->table('tweb_pegawai_absen');
                    $builder_abs->where('ID_PEGAWAI', $id_ptk);
                    $builder_abs->where('TANGGAL_ABSEN', $tgl);
                    $all_abs =  $builder_abs->countAllResults();
                    if($all_abs>0){
                        //cari nama relawan
                        $query = $db->query("SELECT STATUS FROM tweb_pegawai_absen where ID_PEGAWAI='$id_ptk' and TANGGAL_ABSEN='$tgl'");
                        $row = $query->getRow();
                        $status = $row->STATUS;
                        if($status==2){
                            $sts="Sakit";
                        }elseif($status==3){
                            $sts="Izin";
                        }else{
                            $sts="Alpha";
                        }
                    }else{
                        $sts="Alpha";
                    }
                }
            }
        }else{
            //karywan umum
                $query = $db->query("SELECT sts_hari,jammasuk FROM r_hari where nm_hari='$hari'");
                $row = $query->getRow();
                $sts = $row->sts_hari;
                if($sts==2){
                    $sts="Libur";
                }else{
                    //cek apakah ada absen masuk
                    $builder = $db->table('tweb_pegawai_hadir');
                    $builder->where('ID_PEGAWAI', $id_ptk);
                    $builder->where('STATUS', 0);
                    $builder->where('TANGGAL', $tgl);
                    $all =  $builder->countAllResults();
                    if($all>0){
                        //ambil jam masuk
                        $query_jam = $db->query("SELECT JAM FROM tweb_pegawai_hadir 
                        where ID_PEGAWAI='$id_ptk' and TANGGAL='$tgl' and STATUS=0");
                        $row_jam = $query_jam->getRow();
                        $jam = $row_jam->JAM;
                        $jam_masuk = $row->jammasuk;
                        if($jam>$jam_masuk){
                            $sts="Terlambat";
                        }else{
                            $sts="Masuk";
                        }
                        
                    }else{
                        //cek data di tweb_pegawai_absen
                        $builder_abs = $db->table('tweb_pegawai_absen');
                        $builder_abs->where('ID_PEGAWAI', $id_ptk);
                        $builder_abs->where('TANGGAL_ABSEN', $tgl);
                        $all_abs =  $builder_abs->countAllResults();
                        if($all_abs>0){
                            //cari nama relawan
                            $query = $db->query("SELECT STATUS FROM tweb_pegawai_absen where ID_PEGAWAI='$id_ptk' and TANGGAL_ABSEN='$tgl'");
                            $row = $query->getRow();
                            $status = $row->STATUS;
                            if($status==2){
                                $sts="Sakit";
                            }elseif($status==3){
                                $sts="Izin";
                            }else{
                                $sts="Alpha";
                            }
                        }else{
                            $sts="Alpha";
                        }
                    }
                }
            
           
        }
     }
            
                return $sts;
            }
            
           
        $column = 2;
        $no=0;
        foreach ($users as $data) {
            $no++;
            $tgl = $data['TANGGAL'];

            $jammasuk = jammasuk($id_ptk,$tgl);
            $jampulang = jampulang($id_ptk,$tgl);

    
            $diff    =strtotime($jampulang) - strtotime($jammasuk);
            $jam    =floor($diff / (60 * 60));
            $menit    =$diff - $jam * (60 * 60);
            
            if(empty($jampulang)){
                $total= "";
                $menit= "";
            }elseif(empty($jammasuk)){
                $total= "";
                $menit= "";
            }else{
                $total = $jam;
                $menit= floor( $menit / 60 );
            }

            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $column, $no)
                ->setCellValue('B' . $column, $data['TANGGAL'])
                ->setCellValue('C' . $column, $jammasuk)
                ->setCellValue('D' . $column, sts_absen($id_ptk,$tgl))
                ->setCellValue('E' . $column, $jampulang)
                ->setCellValue('F' . $column, $total.' jam,'.$menit.' menit');

            $column++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = date('Y-m-d-His'). '-Data-Absensi';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function lembur()
    {
        $model = new Lembur_model;
        $tgl1 = $this->request->getPost('tgl1');
        $tgl2 = $this->request->getPost('tgl2');
        $users = $model->getLemburtgl($tgl1,$tgl2);

        $spreadsheet = new Spreadsheet();
        
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Nama Pegawai')
            ->setCellValue('C1', 'Jenis Kerja')
            ->setCellValue('D1', 'Tanggal Lembur')
            ->setCellValue('E1', 'Tanggal Approve')
            ->setCellValue('F1', 'User Approve');
        
            
        $column = 2;
        $no=0;
        foreach ($users as $data) {
            $no++;
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $column, $no)
                ->setCellValue('B' . $column, $data['nama_ptk'])
                ->setCellValue('C' . $column, $data['nama_jenis_ptk'])
                ->setCellValue('D' . $column, $data['tgl_lembur'])
                ->setCellValue('E' . $column, $data['tgl_approve'])
                ->setCellValue('F' . $column, $data['nama']);

            $column++;
        }
 
        $writer = new Xlsx($spreadsheet);
        $filename = date('Y-m-d-His'). '-Data-Lembur';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }
    public function gajiall()
    {
        $model = new Pegawai_model;
        $bln = $this->request->getPost('bln');
        $thn = $this->request->getPost('thn');
        $users = $model->getPegawaiall();

        $spreadsheet = new Spreadsheet();
        
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Nip')
            ->setCellValue('C1', 'Nama Pegawai')
            ->setCellValue('D1', 'Jenis Kerja')
            ->setCellValue('E1', 'Jumlah Gaji');
            function gaji($id_ptk,$bln,$thn) {
                $db = \Config\Database::connect();
                $builder = $db->table('gaji');
                $builder->where('id_ptk', $id_ptk);
                $builder->where('bln', $bln);
                $builder->where('thn', $thn);
                $all =  $builder->countAllResults();
                if($all>0){
                   //jumlah point
                    $query = $db->query("SELECT sum(nominal) as nominal FROM gaji where id_ptk='$id_ptk' and bln='$bln' and thn='$thn'");
                    $row = $query->getRow();
                    $sts = $row->nominal;
                }else{
                    $sts=0;
                }
                
                return $sts;
            }
            
        $column = 2;
        $no=0;
        foreach ($users as $data) {
            $no++;
            $id =$data['id_ptk'];
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $column, $no)
                ->setCellValue('B' . $column, $data['nip'])
                ->setCellValue('C' . $column, $data['nama_ptk'])
                ->setCellValue('D' . $column, $data['nama_jenis_ptk'])
                ->setCellValue('E' . $column, gaji($id,$bln,$thn));

            $column++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = date('Y-m-d-His'). '-Data-Gaji';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }
}
