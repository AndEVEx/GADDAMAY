<?php
function level($kode){
    if($kode==1){
        echo "Administrator";
    }elseif($kode==2){
        echo "Dosen";
    }elseif($kode==3){
        echo "Karyawan";
    }elseif($kode==4){
        echo "Kepala Sekolah";
    }
} 
function jnskerja($kode){
    if($kode==1){
        echo "Dosen";
    }elseif($kode==2){
        echo "Karyawan";
    }elseif($kode=='All'){
        echo "Semua";
    }
}
function stsizin($kode){
    if($kode==2){
        echo "Sakit";
    }elseif($kode==3){
        echo "Ijin";
    }
}

function merekmesin($kode){
    if($kode==1){
        echo "Fingerspot";
    }elseif($kode==2){
        echo "Solution dll";
    }
}

function tgl_indo($tanggal){
	$bulan = array (
		1 =>   'Januari',
		'Februari',
		'Maret',
		'April',
		'Mei',
		'Juni',
		'Juli',
		'Agustus',
		'September',
		'Oktober',
		'November',
		'Desember'
	);
	$pecahkan = explode('-', $tanggal);
	
	// variabel pecahkan 0 = tanggal
	// variabel pecahkan 1 = bulan
	// variabel pecahkan 2 = tahun
 
	return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
}
function stsptk($kode){
    if($kode==1){
        echo "Aktif";
    }elseif($kode==2){
        echo "TIdak Aktif";
    }
}

function sts_hari($kode){
    if($kode==1){
        echo "Masuk";
    }elseif($kode==2){
        echo "Libur";
    }
}

function jk($kode){
    if($kode==1){
        echo "Laki-laki";
    }else{
        echo "Perempuan";
    }
}
function stsapprove($kode){
    if($kode==1){
        echo "Disetujui";
    }elseif($kode==2){
        echo "Ditolak";
    }else{
        echo "Menunggu";
    }
}

function stsabsen($kode){
    if($kode==0){
        echo "Masuk";
    }else{
        echo "Pulang";
    }
}
function ststransaksi($kode){
    if($kode==1){
        echo "Debit";
    }else{
        echo "Kredit";
    }
}

function jmljenisketenagaan($id){
    $db = \Config\Database::connect();
    $builder = $db->table('t_ptk');
    $builder->where('id_jenis_ptk', $id);
    $builder->where('status_ptk', 1);
    $all =  $builder->countAllResults();
    
    return $all;
}

function updatemsinfinger($id) {
    $db = \Config\Database::connect();
    $builder = $db->table('tweb_log_command_adms');
    $builder->where('serial_number', $id);
    $all =  $builder->countAllResults();
    if($all>0){
        //total cuti
        $query1 = $db->query("SELECT tanggal_akses FROM tweb_log_command_adms where serial_number='$id' order by id DESC");
        $row1 = $query1->getRow();
        $jml_total = $row1->tanggal_akses;
    }else{
        $jml_total = "0000-00-00 00:00";
    }
    return $jml_total;
}

function formatTanggal($date){
    // pisahkan tanda - dan jadikan array
    $pecah = explode('-', $date);
    return $pecah[2].'-'.$pecah[1].'-'.$pecah[0];
}

  function rupiah($angka){
	
	$hasil_rupiah = "Rp " . number_format($angka,2,',','.');
	return $hasil_rupiah;
}

function  hide_mobile ( $mobile )  {
    return  substr ( $mobile ,  0 ,  - 4 )  .  "****" ;
}

function  hide_hp ( $str )  {
    $str_panjang  =  strlen ( $str );
    return  substr ( $str ,  0 ,  3 ). str_repeat ( '*' ,  $str_panjang  -  2 ). substr ( $str ,  $str_panjang  -  1 ,  1 );    
}

function kode_acak($panjang) {
    $tipe = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $kode_acak = '';
    for ($i = 0; $i<=$panjang; $i++) { 
        $random = rand(0, strlen($tipe) - 1); 
        $kode_acak .= $tipe[$random];
    }
    return $kode_acak;
}

//fungsi untuk mengkonversi csv ke array asosiatif
function csvToArray($file, $delimiter) { 
    if (($handle = fopen($file, 'r')) !== FALSE) { 
      $i = 0; 
      while (($lineArray = fgetcsv($handle, 4000, $delimiter, '"')) !== FALSE) { 
        for ($j = 0; $j < count($lineArray); $j++) { 
          $arr[$i][$j] = $lineArray[$j]; 
        } 
        $i++; 
      } 
      fclose($handle); 
    } 
    return $arr; 
  } 

  function bulan($bulan)
  {
  Switch ($bulan){
      case 1 : $bulan="Januari";
          Break;
      case 2 : $bulan="Februari";
          Break;
      case 3 : $bulan="Maret";
          Break;
      case 4 : $bulan="April";
          Break;
      case 5 : $bulan="Mei";
          Break;
      case 6 : $bulan="Juni";
          Break;
      case 7 : $bulan="Juli";
          Break;
      case 8 : $bulan="Agustus";
          Break;
      case 9 : $bulan="September";
          Break;
      case 10 : $bulan="Oktober";
          Break;
      case 11 : $bulan="November";
          Break;
      case 12 : $bulan="Desember";
          Break;
      }
  return $bulan;
  }

  function hari_ini(){
	$hari = date ("D");
 
	switch($hari){
		case 'Sun':
			$hari_ini = "Minggu";
		break;
 
		case 'Mon':			
			$hari_ini = "Senin";
		break;
 
		case 'Tue':
			$hari_ini = "Selasa";
		break;
 
		case 'Wed':
			$hari_ini = "Rabu";
		break;
 
		case 'Thu':
			$hari_ini = "Kamis";
		break;
 
		case 'Fri':
			$hari_ini = "Jumat";
		break;
 
		case 'Sat':
			$hari_ini = "Sabtu";
		break;
		
		default:
			$hari_ini = "Tidak di ketahui";		
		break;
	}
 
	return $hari_ini;
 
}
function hari($kode){
    if($kode=="Sunday"){
        echo "Mingg";
    }elseif($kode=="Monday"){
        echo "Senin";
    }elseif($kode=="Tuesday"){
        echo "Selasa";
    }elseif($kode=="Wednesday"){
        echo "Rabu";
    }elseif($kode=="Thursday"){
        echo "Kamis";
    }elseif($kode=="Friday"){
        echo "Jumat";
    }elseif($kode=="Saturday"){
        echo "Sabtu";
    }

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
function ketabsen($id_ptk,$tgl) {
    //cek apakah ada absen masuk
    $db = \Config\Database::connect();
	$builder = $db->table('tweb_pegawai_absen');
    $builder->where('ID_PEGAWAI', $id_ptk);
    $builder->where('TANGGAL_ABSEN', $tgl);
    $all =  $builder->countAllResults();
    if($all>0){
        //ambil jam masuk
        $query = $db->query("SELECT KETERANGAN FROM tweb_pegawai_absen where ID_PEGAWAI='$id_ptk' and TANGGAL_ABSEN='$tgl'");
        $row = $query->getRow();
        $sts=$row->KETERANGAN;  
    }else{
        $sts="";
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
function jummasukblnadmin($bln) {
    //cek apakah ada absen masuk
    $db = \Config\Database::connect();
	$builder = $db->table('tweb_pegawai_hadir');
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
function jumsakitblnadmin($bln) {
    //cek apakah ada absen sakit
    $db = \Config\Database::connect();
	$builder = $db->table('tweb_pegawai_absen');
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

function jumizinblnadmin($bln) {
    //cek apakah ada absen izin
    $db = \Config\Database::connect();
	$builder = $db->table('tweb_pegawai_absen');
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
function jumalphatgl($id_ptk) {
    $jumHari=date('d');
    $sum = 0;
    $sumlb = 0;
    $bln = date('m');

    //cek apakah ada jadwal libur perindividu
    $db = \Config\Database::connect();
    $builder_lbr = $db->table('libur');
    $builder_lbr->where('id_ptk', $id_ptk);
    $libur =  $builder_lbr->countAllResults();

    for($i=1;$i<=$jumHari;$i++){
        $tanggal = date('Y').'-'.date('m').'-'.$i;
        $namahari = date('l', strtotime($tanggal));

        if($libur>0){
            if(hariliburindividu($namahari, $id_ptk)==1){
                $sum++;
            } 

            //cek hari libur besar
            $querylb = $db->query("SELECT tgl_libur FROM libur_besar where MONTH(tgl_libur)='$bln' AND tgl_libur <= '$tanggal'");
            foreach ($querylb->getResult() as $rowlb) {
                $tgllb = $rowlb->tgl_libur;
                $namahari = date('l', strtotime($tgllb));
                if(hariliburindividu($namahari, $id_ptk)!=1){
                    $sumlb++;
                }
            }
        }else{
            if(harilibur($namahari)==2){
                $sum++;
            }

            //cek hari libur besar
            $querylb = $db->query("SELECT tgl_libur FROM libur_besar where MONTH(tgl_libur)='$bln' AND tgl_libur <= '$tanggal'");
            foreach ($querylb->getResult() as $rowlb) {
                $tgllb = $rowlb->tgl_libur;
                $namahari = date('l', strtotime($tgllb));
                if(harilibur($namahari)!=2){
                    $sumlb++;
                }
            }
        }
    }

    $harilibur = $sum;
    $harimasuk = $jumHari-$harilibur;
    $all_hb = $sumlb;

    $db = \Config\Database::connect();
	$builder = $db->table('tweb_pegawai_hadir');
    $builder->where('ID_PEGAWAI', $id_ptk);
    $builder->where('STATUS', 0);
    $builder->where('TANGGAL <=', date('Y-m-d'));
    $builder->where('MONTH(TANGGAL)', $bln);
    $all =  $builder->countAllResults();
    if($all>0){
       $masuk = $all;
    }else{
        $masuk=0;
    }

    //cek sakit /izin
	$builder1 = $db->table('tweb_pegawai_absen');
    $builder1->where('ID_PEGAWAI', $id_ptk);
    $builder1->where('TANGGAL_ABSEN <=', date('Y-m-d'));
    $builder1->where('MONTH(TANGGAL_ABSEN)', $bln);
    $all1 =  $builder1->countAllResults();
    if($all1>0){
       $ijinsakit = $all1;
    }else{
        $ijinsakit=0;
    }

    $sts     = $harimasuk-$masuk-$ijinsakit-$all_hb;
    return $sts;
} 
function jumalphabln($idptk,$bln) {
    $thn = date('Y');
    $bulan = date('m');
    $sumalpha = 0;
  
    if($bulan==$bln){
        $jumHari = date('d');
    }else{
        $jumHari = jumlah_hari($bulan,$thn);
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

function jumterlambattgl($tgl) {
    $sumterlambat = 0;
    $db = \Config\Database::connect();
    //ambil data karyawan
    $query = $db->query("SELECT id_ptk FROM t_ptk where status_ptk=1");
    foreach ($query->getResult() as $row) {
        $id_ptk = $row->id_ptk;
        $sts = sts_absen($id_ptk,$tgl);
        if($sts=='Terlambat'){
            $sumterlambat++;
        }
    }
     $terlambat = $sumterlambat;

     return $terlambat;
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
function jumalphablnadmin($bln) {
    $db = \Config\Database::connect();
   //ambil data karyawan
   $query = $db->query("SELECT id_ptk FROM t_ptk where status_ptk=1");
   foreach ($query->getResult() as $row) {
    $id_ptk = $row->id_ptk;
    $jml_alpha[] = jumalphabln($id_ptk,$bln);
   }

   if(count($jml_alpha) != 0) {
    $sts = array_sum($jml_alpha);
   }else{
    $sts = 0;
   }
   
    return $sts;
} 
function jumterlambatbln($id_ptk,$bln) {
    $thn = date('Y');
    $bulan = date('m');
    $sum = 0;
  
    if($bulan==$bln){
        $jumHari = date('d');
    }else{
        $jumHari = jumlah_hari($bulan,$thn);
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
function jumterlambatblnadmin($bln) {
    $db = \Config\Database::connect();
   //ambil data karyawan
   $query = $db->query("SELECT id_ptk FROM t_ptk where status_ptk=1");
   foreach ($query->getResult() as $row) {
    $id_ptk = $row->id_ptk;
    $jml_terlambat[] = jumterlambatbln($id_ptk,$bln);
   }

   if(count($jml_terlambat) != 0) {
    $sts = array_sum($jml_terlambat);
   }else{
    $sts = 0;
   }
   
    return $sts;
}
function jmlpoint($id_ptk,$bln) {
    $db = \Config\Database::connect();
    $builder = $db->table('t_point');
    $builder->where('id_ptk', $id_ptk);
    $builder->where('MONTH(tgl_point)', $bln);
    $all =  $builder->countAllResults();
    if($all>0){
       //jumlah point
        $query = $db->query("SELECT sum(nilai) as point FROM t_point where id_ptk='$id_ptk' and MONTH(tgl_point)='$bln'");
        $row = $query->getRow();
        $sts = $row->point;
    }else{
        $sts=0;
    }
    
    return $sts;
}
function jmlpointtgl($id_ptk,$tgl) {
    $db = \Config\Database::connect();
    $builder = $db->table('t_point');
    $builder->where('id_ptk', $id_ptk);
    $builder->where('tgl_point', $tgl);
    $all =  $builder->countAllResults();
    if($all>0){
       //jumlah point
        $query = $db->query("SELECT nilai FROM t_point where id_ptk='$id_ptk' and tgl_point='$tgl'");
        $row = $query->getRow();
        $sts = $row->nilai;
    }else{
        $sts=0;
    }
    
    return $sts;
}

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
function hariliburindividu($kode, $id_ptk) {
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
    $builder = $db->table('libur');
    $builder->where('id_ptk', $id_ptk);
    $builder->where($hari, 1);
    $all =  $builder->countAllResults();
    if($all>0){
        $sts=1;
    }else{
        $sts=0;
    }
   
    return $sts;
}

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
function gajidetail($id_ptk,$bln,$thn,$kat) {
    $db = \Config\Database::connect();
    $builder = $db->table('gaji');
    $builder->where('id_ptk', $id_ptk);
    $builder->where('bln', $bln);
    $builder->where('thn', $thn);
    $builder->where('id_kat_gaji', $kat);
    $all =  $builder->countAllResults();
    if($all>0){
       //jumlah point
        $query = $db->query("SELECT nominal FROM gaji where id_ptk='$id_ptk' and bln='$bln' and thn='$thn' and id_kat_gaji='$kat'");
        $row = $query->getRow();
        $sts = $row->nominal;
    }else{
        $sts=0;
    }
    
    return $sts;
} 
function approvegaji($id_ptk,$bln,$thn) {
    $db = \Config\Database::connect();
    $builder = $db->table('gaji_approve');
    $builder->where('id_ptk', $id_ptk);
    $builder->where('bln', $bln);
    $builder->where('thn', $thn);
    $all =  $builder->countAllResults();
    
    return $all;
} 

function penyebut($nilai) {
    $nilai = abs($nilai);
    $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
    $temp = "";
    if ($nilai < 12) {
        $temp = " ". $huruf[$nilai];
    } else if ($nilai <20) {
        $temp = penyebut($nilai - 10). " belas";
    } else if ($nilai < 100) {
        $temp = penyebut($nilai/10)." puluh". penyebut($nilai % 10);
    } else if ($nilai < 200) {
        $temp = " seratus" . penyebut($nilai - 100);
    } else if ($nilai < 1000) {
        $temp = penyebut($nilai/100) . " ratus" . penyebut($nilai % 100);
    } else if ($nilai < 2000) {
        $temp = " seribu" . penyebut($nilai - 1000);
    } else if ($nilai < 1000000) {
        $temp = penyebut($nilai/1000) . " ribu" . penyebut($nilai % 1000);
    } else if ($nilai < 1000000000) {
        $temp = penyebut($nilai/1000000) . " juta" . penyebut($nilai % 1000000);
    } else if ($nilai < 1000000000000) {
        $temp = penyebut($nilai/1000000000) . " milyar" . penyebut(fmod($nilai,1000000000));
    } else if ($nilai < 1000000000000000) {
        $temp = penyebut($nilai/1000000000000) . " trilyun" . penyebut(fmod($nilai,1000000000000));
    }     
    return $temp;
}

function terbilang($nilai) {
    if($nilai<0) {
        $hasil = "minus ". trim(penyebut($nilai));
    } else {
        $hasil = trim(penyebut($nilai));
    }     		
    return $hasil;
}
function shiftjadwal($id_ptk,$tgl) {
    $db = \Config\Database::connect();
    $builder = $db->table('jadwal_absen');
    $builder->where('id_ptk', $id_ptk);
    $builder->where('tgl_jadwal', $tgl);
    $builder->where('id_shift !=', 0);
    $all =  $builder->countAllResults();
    if($all>0){
    //jumlah point
    $query = $db->query("SELECT nm_shift,jadwal_absen.id_shift as id_shift FROM jadwal_absen LEFT JOIN r_shift ON r_shift.id_shift = jadwal_absen.id_shift where id_ptk='$id_ptk' and tgl_jadwal='$tgl'");
    $row = $query->getRow();
    if(($row->id_shift)==99){
        $sts = 99;
    }else{
        $sts = $row->nm_shift;
    }
    
    }else{
        $sts=0;
    }
    return $sts;
}
function shiftjadwalid($id_ptk,$tgl) {
    $db = \Config\Database::connect();
    $builder = $db->table('jadwal_absen');
    $builder->where('id_ptk', $id_ptk);
    $builder->where('tgl_jadwal', $tgl);
    $builder->where('id_shift !=', 0);
    $all =  $builder->countAllResults();
    if($all>0){
    //jumlah point
    $query = $db->query("SELECT id_shift FROM jadwal_absen where id_ptk='$id_ptk' and tgl_jadwal='$tgl'");
    $row = $query->getRow();
    $sts = $row->id_shift;
    }else{
        $sts=0;
    }
    return $sts;
}
function nmrombel($id_rombel) {
    $db = \Config\Database::connect();
    $builder = $db->table('t_rombel');
    $builder->where('id_rombel', $id_rombel);
   
    $all =  $builder->countAllResults();
    if($all>0){
       //jumlah point
        $query = $db->query("SELECT nm_rombel FROM t_rombel where id_rombel='$id_rombel'");
        $row = $query->getRow();
        $sts = $row->nm_rombel;
    }else{
        $sts="";
    }
    
    return $sts;
}
function idsiswa($induk) {
    $db = \Config\Database::connect();
    $builder = $db->table('t_siswa');
    $builder->where('no_induk', $induk);
   
    $all =  $builder->countAllResults();
    if($all>0){
       //jumlah point
        $query = $db->query("SELECT id_siswa FROM t_siswa where no_induk='$induk'");
        $row = $query->getRow();
        $sts = $row->id_siswa;
    }else{
        $sts=0;
    }
    
    return $sts;
}
function rfidtoidsiswa($rfid) {
    $db = \Config\Database::connect();
    $builder = $db->table('t_siswa');
    $builder->where('rfid', $rfid);
   
    $all =  $builder->countAllResults();
    if($all>0){
       //jumlah point
        $query = $db->query("SELECT id_siswa FROM t_siswa where rfid='$rfid'");
        $row = $query->getRow();
        $sts = $row->id_siswa;
    }else{
        $sts=0;
    }
    
    return $sts;
}
function rombelwalikelas($id, $tapel) {
    $db = \Config\Database::connect();
    $builder = $db->table('t_rombel');
    $builder->where('id_walikelas', $id);
    $builder->where('id_tapel', $tapel);
    $all =  $builder->countAllResults();
    if($all>0){
       //jumlah point
        $query = $db->query("SELECT id_rombel FROM t_rombel where id_walikelas='$id' and id_tapel='$tapel'");
        $row = $query->getRow();
        $sts = $row->id_rombel;
    }else{
        $sts=0;
    }
    
    return $sts;
}
function idsiswatoidrombel($id,$idtapel) {
    $db = \Config\Database::connect();
    $builder = $db->table('t_siswa_rombel');
    $builder->where('id_siswa', $id);
    $builder->where('id_tapel', $idtapel);
   
    $all =  $builder->countAllResults();
    if($all>0){
       //cek id rombel
        $query = $db->query("SELECT id_rombel FROM t_siswa_rombel where id_siswa='$id' and id_tapel='$idtapel'");
        $row = $query->getRow();
        $sts = $row->id_rombel;
    }else{
        $sts=0;
    }
    
    return $sts;
}
function jammasukhari($hari) {
    $db = \Config\Database::connect();
    
    //cek jam masuk
    $query = $db->query("SELECT jammasuk FROM r_hari where nm_hari='$hari'");
    $row = $query->getRow();
    $sts = $row->jammasuk;
    
    return $sts;
}
function nmshift($id) {
    $db = \Config\Database::connect();
    
    //cek jam masuk
    $query = $db->query("SELECT nm_shift FROM r_shift where id_shift='$id'");
    $row = $query->getRow();
    $sts = $row->nm_shift;
    
    return $sts;
}

function jumlah_hari($bulan, $tahun)
{
    if ($bulan < 1 OR $bulan > 12)
    {
  return 0;
    }
    if ( ! is_numeric($tahun) OR strlen($tahun) != 4)
    {
  $tahun = date('Y');
    }
    if ($bulan == 2)
    {
  if ($tahun % 400 == 0 OR ($tahun % 4 == 0 AND $tahun % 100 != 0))
  {
  return 29;
  }
    }
    $jumlah_hari    = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    return $jumlah_hari[$bulan - 1];
}

// ============================================================
// Daily Attendance by Kelas (Rombel) Functions - Today Only
// ============================================================

/**
 * Count hadir (present on time) students per kelas for today
 */
function jumHadirKelasHariIni($id_rombel) {
    $db = \Config\Database::connect();
    $tgl = date('Y-m-d');
    $id_tapel = session()->get('id_tapel');
    
    // Get jam masuk setting for today
    $nmhari = hari_ini();
    $query_jam = $db->query("SELECT jammasuk FROM r_hari where nm_hari='$nmhari'");
    $row_jam = $query_jam->getRow();
    $jammasuk = $row_jam->jammasuk ?? '07:10:00';
    
    // Count students who are present (sts_hadir=0) and on time (jam <= jammasuk)
    $query = $db->query("
        SELECT COUNT(*) as total FROM t_siswa_hadir sh
        JOIN t_siswa_rombel sr ON sr.id_siswa = sh.id_siswa AND sr.id_tapel = '$id_tapel'
        WHERE sh.tgl_hadir = '$tgl' 
        AND sh.sts_hadir = 0 
        AND sh.jam <= '$jammasuk'
        AND sr.id_rombel = '$id_rombel'
    ");
    $row = $query->getRow();
    return $row->total ?? 0;
}

/**
 * Count terlambat (late) students per kelas for today
 */
function jumTerlambatKelasHariIni($id_rombel) {
    $db = \Config\Database::connect();
    $tgl = date('Y-m-d');
    $id_tapel = session()->get('id_tapel');
    
    // Get jam masuk setting for today
    $nmhari = hari_ini();
    $query_jam = $db->query("SELECT jammasuk FROM r_hari where nm_hari='$nmhari'");
    $row_jam = $query_jam->getRow();
    $jammasuk = $row_jam->jammasuk ?? '07:10:00';
    
    // Count students who are present (sts_hadir=0) but late (jam > jammasuk)
    $query = $db->query("
        SELECT COUNT(*) as total FROM t_siswa_hadir sh
        JOIN t_siswa_rombel sr ON sr.id_siswa = sh.id_siswa AND sr.id_tapel = '$id_tapel'
        WHERE sh.tgl_hadir = '$tgl' 
        AND sh.sts_hadir = 0 
        AND sh.jam > '$jammasuk'
        AND sr.id_rombel = '$id_rombel'
    ");
    $row = $query->getRow();
    return $row->total ?? 0;
}

/**
 * Count tidak hadir (absent) students per kelas for today
 */
function jumTidakHadirKelasHariIni($id_rombel) {
    $db = \Config\Database::connect();
    $tgl = date('Y-m-d');
    $id_tapel = session()->get('id_tapel');
    
    // Count total students in this kelas
    $query_total = $db->query("
        SELECT COUNT(*) as total FROM t_siswa_rombel sr
        JOIN t_siswa s ON s.id_siswa = sr.id_siswa
        WHERE sr.id_rombel = '$id_rombel' 
        AND sr.id_tapel = '$id_tapel'
        AND s.sts_siswa = 1
    ");
    $total_siswa = $query_total->getRow()->total ?? 0;
    
    // Count students who have attended today (sts_hadir=0 means masuk)
    $query_hadir = $db->query("
        SELECT COUNT(DISTINCT sh.id_siswa) as total FROM t_siswa_hadir sh
        JOIN t_siswa_rombel sr ON sr.id_siswa = sh.id_siswa AND sr.id_tapel = '$id_tapel'
        WHERE sh.tgl_hadir = '$tgl' 
        AND sh.sts_hadir = 0 
        AND sr.id_rombel = '$id_rombel'
    ");
    $hadir = $query_hadir->getRow()->total ?? 0;
    
    // Tidak hadir = total siswa - yang hadir
    return max(0, $total_siswa - $hadir);
}

/**
 * Get all rombel (kelas) for current tapel
 */
function getAllRombelForChart() {
    $db = \Config\Database::connect();
    $id_tapel = session()->get('id_tapel');
    
    $query = $db->query("
        SELECT id_rombel, nm_rombel FROM t_rombel 
        WHERE id_tapel = '$id_tapel'
        ORDER BY nm_rombel ASC
    ");
    return $query->getResult();
}

/**
 * Get Rekap Absen per Kelas Hari Ini
 */
function getRekapKelasHariIni($id_rombel) {
    $db = \Config\Database::connect();
    $tgl = date('Y-m-d');
    $id_tapel = session()->get('id_tapel');
    
    // Total Siswa
    $query_total = $db->query("
        SELECT COUNT(*) as total FROM t_siswa_rombel sr
        JOIN t_siswa s ON s.id_siswa = sr.id_siswa
        WHERE sr.id_rombel = '$id_rombel' 
        AND sr.id_tapel = '$id_tapel'
        AND s.sts_siswa = 1
    ");
    $total_siswa = $query_total->getRow()->total ?? 0;
    
    // Hadir + Terlambat (sts_hadir = 0 artinya masuk)
    $query_hadir = $db->query("
        SELECT COUNT(DISTINCT sh.id_siswa) as total FROM t_siswa_hadir sh
        JOIN t_siswa_rombel sr ON sr.id_siswa = sh.id_siswa AND sr.id_tapel = '$id_tapel'
        WHERE sh.tgl_hadir = '$tgl' 
        AND sh.sts_hadir = 0 
        AND sr.id_rombel = '$id_rombel'
    ");
    $hadir = $query_hadir->getRow()->total ?? 0;
    
    // Sakit (sts_absen = 2)
    $query_sakit = $db->query("
        SELECT COUNT(DISTINCT sa.id_siswa) as total FROM t_siswa_absen sa
        JOIN t_siswa_rombel sr ON sr.id_siswa = sa.id_siswa AND sr.id_tapel = '$id_tapel'
        WHERE sa.tgl_absen = '$tgl' 
        AND sa.sts_absen = 2 
        AND sr.id_rombel = '$id_rombel'
    ");
    $sakit = $query_sakit->getRow()->total ?? 0;
    
    // Izin (sts_absen = 3)
    $query_izin = $db->query("
        SELECT COUNT(DISTINCT sa.id_siswa) as total FROM t_siswa_absen sa
        JOIN t_siswa_rombel sr ON sr.id_siswa = sa.id_siswa AND sr.id_tapel = '$id_tapel'
        WHERE sa.tgl_absen = '$tgl' 
        AND sa.sts_absen = 3 
        AND sr.id_rombel = '$id_rombel'
    ");
    $izin = $query_izin->getRow()->total ?? 0;
    
    $alfa = max(0, $total_siswa - $hadir - $sakit - $izin);
    
    return [
        'total' => $total_siswa,
        'hadir'  => $hadir,
        'sakit'  => $sakit,
        'izin'   => $izin,
        'alfa'   => $alfa
    ];
}


