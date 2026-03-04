<?php
function level($kode){
    if($kode==1){
        echo "Administrator";
    }elseif($kode==2){
        echo "Walikelas";
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

function stsabsen($kode){
    if($kode==0){
        echo "Masuk";
    }else{
        echo "Pulang";
    }
}
function stsizin($kode){
    if($kode==2){
        echo "Sakit";
    }elseif($kode==3){
        echo "Ijin";
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
function ststransaksi($kode){
    if($kode==1){
        echo "Debit";
    }else{
        echo "Kredit";
    }
}



function formatTanggal($date){
    // pisahkan tanda - dan jadikan array
    $pecah = explode('-', $date);
    return $pecah[2].'-'.$pecah[1].'-'.$pecah[0];
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
        echo "Minggu";
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
 
  function sts_absen($id_siswa,$tgl) {
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
            $id_tapel = session()->get('id_tapel');
            $idrombel = idsiswatoidrombel($id_siswa,$id_tapel);

            //cek apakah ada jadwal jam masuk pulang di entri
            $builder_jadwal = $db->table('r_hari');
            $jadwal =  $builder_jadwal->countAllResults();
            if($jadwal>0){
                $query = $db->query("SELECT sts_hari,jammasuk FROM r_hari where nm_hari='$hari'");
                $row = $query->getRow();
                $sts = $row->sts_hari;
                $jam_masuk = $row->jammasuk;
                if($sts==2){
                    $sts="Libur";
                }else{
                        //cek apakah ada absen masuk
                        $builder = $db->table('t_siswa_hadir');
                        $builder->where('id_siswa', $id_siswa);
                        $builder->where('sts_hadir', 0);
                        $builder->where('tgl_hadir', $tgl);
                        $all =  $builder->countAllResults();
                        
                        if($all>0){
                            //ambil jam masuk
                            $queryjam = $db->query("SELECT jam FROM t_siswa_hadir 
                            where id_siswa='$id_siswa' and tgl_hadir='$tgl' and sts_hadir=0");
                            $rowjam = $queryjam->getRow();
                            $jam = $rowjam->jam;
                            if($jam>$jam_masuk){
                                $sts="Terlambat";
                            }else{
                                $sts="Masuk";
                            }
                            
                        }else{
                            //cek data di t_siswa_absen
                            $builder_abs = $db->table('t_siswa_absen');
                            $builder_abs->where('id_siswa', $id_siswa);
                            $builder_abs->where('tgl_absen', $tgl);
                            $builder_abs->where('sts_approve', 1);
                            $all_abs =  $builder_abs->countAllResults();
                            if($all_abs>0){
                                //cari status absen
                                $query = $db->query("SELECT sts_absen FROM t_siswa_absen where id_siswa='$id_siswa' and tgl_absen='$tgl' and sts_approve='1'");
                                $row = $query->getRow();
                                $status = $row->sts_absen;
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
                $sts="Belum Disetting Jam";
            }

        }

    }
     
    
    return $sts;
}

function jammasuk($id_siswa,$tgl) {
    //cek apakah ada absen masuk
    $db = \Config\Database::connect();
	$builder = $db->table('t_siswa_hadir');
    $builder->where('id_siswa', $id_siswa);
    $builder->where('sts_hadir', 0);
    $builder->where('tgl_hadir', $tgl);
    $all =  $builder->countAllResults();
    if($all>0){
        //ambil jam masuk
        $query = $db->query("SELECT jam FROM t_siswa_hadir where id_siswa='$id_siswa' and tgl_hadir='$tgl' and sts_hadir=0");
        $row = $query->getRow();
        $sts=$row->jam;  
    }else{
        $sts="";
    }
    return $sts;
}
function jampulang($id_siswa,$tgl) {
    //cek apakah ada absen masuk
    $db = \Config\Database::connect();
	$builder = $db->table('t_siswa_hadir');
    $builder->where('id_siswa', $id_siswa);
    $builder->where('sts_hadir', 1);
    $builder->where('tgl_hadir', $tgl);
    $all =  $builder->countAllResults();
    if($all>0){
        //ambil jam pulang
        $query = $db->query("SELECT jam FROM t_siswa_hadir where id_siswa='$id_siswa' and tgl_hadir='$tgl' and sts_hadir=1");
        $row = $query->getRow();
        $sts=$row->jam; 
    }else{
        $sts="";
    }
    return $sts;
}
function ketabsen($id_siswa,$tgl) {
    //cek apakah ada absen masuk
    $db = \Config\Database::connect();
	$builder = $db->table('t_siswa_absen');
    $builder->where('id_siswa', $id_siswa);
    $builder->where('tgl_absen', $tgl);
    $all =  $builder->countAllResults();
    if($all>0){
        //ambil jam masuk
        $query = $db->query("SELECT ket_absen FROM t_siswa_absen where id_siswa='$id_siswa' and tgl_absen='$tgl'");
        $row = $query->getRow();
        $sts=$row->ket_absen;  
    }else{
        $sts="";
    }
    return $sts;
}
function jummasukbln($id_siswa,$bln) { 
    //cek apakah ada absen masuk
    $db = \Config\Database::connect();
    $thn = date('Y');
	$builder = $db->table('t_siswa_hadir');
    $builder->where('id_siswa', $id_siswa);
    $builder->where('sts_hadir', 0);
    $builder->where('MONTH(tgl_hadir)', $bln);
    $builder->where('YEAR(tgl_hadir)', $thn);
    $all =  $builder->countAllResults();
    if($all>0){
       $sts = $all;
    }else{
        $sts=0;
    }
    return $sts;
}
function jummasukpertanggal($id_siswa,$tgl1,$tgl2) {
    //cek apakah ada absen masuk
    $db = \Config\Database::connect();
	$builder = $db->table('t_siswa_hadir');
    $builder->where('id_siswa', $id_siswa);
    $builder->where('sts_hadir', 0);
    $builder->where('tgl_hadir >=', $tgl1);
    $builder->where('tgl_hadir <=', $tgl2);
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
    $thn = date('Y');
	$builder = $db->table('t_siswa_hadir');
    $builder->where('sts_hadir', 0);
    $builder->where('MONTH(tgl_hadir)', $bln);
    $builder->where('YEAR(tgl_hadir)', $thn);
    $all =  $builder->countAllResults();
    if($all>0){
       $sts = $all;
    }else{
        $sts=0;
    }
    return $sts;
} 
function jummasukblnrombel($bln,$idrombel) {
    //cek apakah ada absen masuk
    $db = \Config\Database::connect();
    $thn = date('Y');
	$builder = $db->table('t_siswa_hadir');
    $builder->join('t_siswa_rombel', 't_siswa_rombel.id_siswa = t_siswa_hadir.id_siswa');
    $builder->where('sts_hadir', 0);
    $builder->where('id_rombel', $idrombel);
    $builder->where('MONTH(tgl_hadir)', $bln);
    $builder->where('YEAR(tgl_hadir)', $thn);
    $all =  $builder->countAllResults();
    if($all>0){
       $sts = $all;
    }else{
        $sts=0;
    }
    return $sts;
}
function jumsakitbln($id_siswa,$bln) {
    //cek apakah ada absen sakit
    $db = \Config\Database::connect();
    $thn = date('Y');
	$builder = $db->table('t_siswa_absen');
    $builder->where('id_siswa', $id_siswa);
    $builder->where('sts_absen', 2);
    $builder->where('sts_approve', 1);
    $builder->where('MONTH(tgl_absen)', $bln);
    $builder->where('YEAR(tgl_absen)', $thn);
    $all =  $builder->countAllResults();
    if($all>0){
       $sts = $all;
    }else{
        $sts=0;
    }
    return $sts;
}
function jumsakitpertanggal($id_siswa,$tgl1,$tgl2) {
    //cek apakah ada absen sakit
    $db = \Config\Database::connect();
	$builder = $db->table('t_siswa_absen');
    $builder->where('id_siswa', $id_siswa);
    $builder->where('sts_absen', 2);
    $builder->where('sts_approve', 1);
    $builder->where('tgl_absen >=', $tgl1);
    $builder->where('tgl_absen <=', $tgl2);
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
function jumizinbln($id_siswa,$bln) {
    //cek apakah ada absen izin
    $thn = date('Y');
    $db = \Config\Database::connect();
	$builder = $db->table('t_siswa_absen');
    $builder->where('id_siswa', $id_siswa);
    $builder->where('sts_absen', 3);
    $builder->where('sts_approve', 1);
    $builder->where('MONTH(tgl_absen)', $bln);
    $builder->where('YEAR(tgl_absen)', $thn);
    $all =  $builder->countAllResults();
    if($all>0){
       $sts = $all;
    }else{
        $sts=0;
    }
    return $sts;
}
function jumizinpertanggal($id_siswa,$tgl1,$tgl2) {
    //cek apakah ada absen izin
    $db = \Config\Database::connect();
	$builder = $db->table('t_siswa_absen');
    $builder->where('id_siswa', $id_siswa);
    $builder->where('sts_absen', 3);
    $builder->where('sts_approve', 1);
    $builder->where('tgl_absen >=', $tgl1);
    $builder->where('tgl_absen <=', $tgl2);
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
function jumalphatgl($id_siswa) {
    $jumHari=date('d');
    $sum = 0;

    for($i=1;$i<=$jumHari;$i++){
        $tanggal = date('Y').'-'.date('m').'-'.$i;
        $namahari = date('l', strtotime($tanggal));
        if(harilibur($namahari)==2){
            $sum++;
        }
    }
    $harilibur = $sum;
    $harimasuk = $jumHari-$harilibur;

    $db = \Config\Database::connect();
    $tgl = date('Y-m-d');
    $bln = date('m');
    $thn = date('Y');

    //cek absen masuk
	$builder = $db->table('t_siswa_hadir');
    $builder->where('id_siswa', $id_siswa);
    $builder->where('sts_hadir', 0);
    $builder->where('tgl_hadir <=', $tgl);
    $builder->where('MONTH(tgl_hadir)', $bln);
    $builder->where('YEAR(tgl_hadir)', $thn);
    $all =  $builder->countAllResults();
    if($all>0){
       $masuk = $all;
    }else{
        $masuk=0;
    }

    //cek sakit /izin
	$builder1 = $db->table('t_siswa_absen');
    $builder1->where('id_siswa', $id_siswa);
    $builder1->where('tgl_absen <=', $tgl);
    $builder1->where('MONTH(tgl_absen)', $bln);
    $builder1->where('YEAR(tgl_absen)', $thn);
    $all1 =  $builder1->countAllResults();
    if($all1>0){
       $ijinsakit = $all1;
    }else{
        $ijinsakit=0;
    }

    $sts     = $harimasuk-$masuk-$ijinsakit;
    return $sts;
} 
function jumalphabln($id_siswa,$bln) {
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
            $sts = sts_absen($id_siswa,$tanggal);
            if($sts=='Alpha'){
                $sumalpha++;
            }
        }
    }
    $alpha = $sumalpha;
    return $alpha;
}
function jumalphapertanggal($id_siswa,$tgl1,$tgl2) {
    $tg1 = $tgl1;
    $tg2 = $tgl2;
    
    $sum = 0;
    $jml = 0;

    while (strtotime($tg1) <= strtotime($tg2)) {
        $jml++;
        $tanggal = $tg1;
        $tg1 = date ("Y-m-d", strtotime("+1 day", strtotime($tg1)));//looping tambah 1 date
        $namahari = date('l', strtotime($tanggal));

       
        if(harilibur($namahari)==2){
            $sum++;
        }
        
    }
    $jumHari = $jml;
    $harilibur = $sum;
    $harimasuk = $jumHari-$harilibur;

    $db = \Config\Database::connect();
	$builder = $db->table('t_siswa_hadir');
    $builder->where('id_siswa', $id_siswa);
    $builder->where('sts_hadir', 0);
    $builder->where('tgl_hadir >=', $tgl1);
    $builder->where('tgl_hadir <=', $tgl2);
    $all =  $builder->countAllResults();
    if($all>0){
       $masuk = $all;
    }else{
        $masuk=0;
    }

    //cek sakit /izin
	$builder1 = $db->table('t_siswa_absen');
    $builder1->where('id_siswa', $id_siswa);
    $builder1->where('tgl_absen >=', $tgl1);
    $builder1->where('tgl_absen <=', $tgl2);
    $all1 =  $builder1->countAllResults();
    if($all1>0){
       $ijinsakit = $all1;
    }else{
        $ijinsakit=0;
    }

    $sts     = $harimasuk-$masuk-$ijinsakit;
    return $sts;
}
function jumalphablnadmin($bln) {
    $db = \Config\Database::connect();
    $thn = date('Y');
   //ambil data karyawan
   $query = $db->query("SELECT id_siswa FROM t_siswa where sts_siswa=1");
   foreach ($query->getResult() as $row) {
        $id_siswa = $row->id_siswa;
        $jml_alpha[] = jumalphabln($id_siswa,$bln);
   }

   if(count($jml_alpha) != 0) {
    $sts = array_sum($jml_alpha);
   }else{
    $sts = 0;
   }
   
    return $sts;
}
function jumalphablnrombel($bln,$idrombel) {
    $db = \Config\Database::connect();
    $thn = date('Y');
   //ambil data karyawan
   $query = $db->query("SELECT t_siswa.id_siswa as id_siswa FROM t_siswa JOIN t_siswa_rombel ON t_siswa_rombel.id_siswa = t_siswa.id_siswa where sts_siswa=1 and id_rombel='$idrombel'");
   foreach ($query->getResult() as $row) {
    $id_siswa = $row->id_siswa;
    $jml_alpha[] = jumalphabln($id_siswa,$bln);
   }

   if(count($jml_alpha) != 0) {
    $sts = array_sum($jml_alpha);
   }else{
    $sts = 0;
   }
   
    return $sts;
}
function jumterlambatbln($id_siswa,$bln) {
    $db = \Config\Database::connect();
    $thn = date('Y');
	$builder = $db->table('t_siswa_hadir');
    $builder->where('id_siswa', $id_siswa);
    $builder->where('sts_hadir', 0);
    $builder->where('MONTH(tgl_hadir)', $bln);
    $builder->where('YEAR(tgl_hadir)', $bln);
    $all =  $builder->countAllResults();
    if($all>0){
        //ambil jam masuk
        $query = $db->query("SELECT tgl_hadir FROM t_siswa_hadir 
        where MONTH(tgl_hadir)='$bln' and YEAR(tgl_hadir)='$thn' and sts_hadir=0 and id_siswa='$id_siswa'");
        foreach ($query->getResult() as $row) {

            $tgl_abs =  $row->tgl_hadir;
            if(sts_absen($id_siswa,$tgl_abs)=='Terlambat'){
                $terlambat[]=1;
            }else{
                $terlambat[]=0;
            }  
        }
        $sts = array_sum($terlambat);
       
    }else{
        $sts = 0;
    }

    return $sts;
}
function jumterlambatpertanggal($id_siswa,$tgl1,$tgl2) {
    $tg1 = $tgl1;
    $tg2 = $tgl2;
    $sumterlambat = 0;

    $jml = 0;
    
    while (strtotime($tg1) <= strtotime($tg2)) {
        $jml++;
        $tanggal = $tg1;
        $tg1 = date ("Y-m-d", strtotime("+1 day", strtotime($tg1)));//looping tambah 1 date
        $sts = sts_absen($id_siswa,$tanggal);
        if($sts=='Terlambat'){
            $sumterlambat++;
        }
    }

    $terlambat = $sumterlambat;
    return $terlambat;
}
function jumterlambatblnadmin($bln) {
    $db = \Config\Database::connect();
    $thn = date('Y');
	$builder = $db->table('t_siswa_hadir');
    $builder->where('sts_hadir', 0);
    $builder->where('MONTH(tgl_hadir)', $bln);
    $builder->where('YEAR(tgl_hadir)', $thn);
    $all =  $builder->countAllResults();
    if($all>0){
        //ambil jam masuk
        $query = $db->query("SELECT id_siswa,jam,tgl_hadir FROM t_siswa_hadir 
        where MONTH(tgl_hadir)='$bln' and YEAR(tgl_hadir)='$thn' and sts_hadir=0");
        foreach ($query->getResult() as $row) {
            $id_siswa =  $row->id_siswa;
            $jml_terlambat[] = jumterlambatbln($id_siswa,$bln);
        }

        if(count($jml_terlambat) != 0) {
            $sts = array_sum($jml_terlambat);
        }else{
            $sts = 0;
        }
            
        return $sts;
       
    }else{
        $sts = 0;
    }

    return $sts;
}
function jumterlambatblnrombel($bln,$idrombel) {
    $db = \Config\Database::connect();
    $thn = date('Y');
	$builder = $db->table('t_siswa_hadir');
    $builder->join('t_siswa_rombel', 't_siswa_rombel.id_siswa = t_siswa_hadir.id_siswa');
    $builder->where('id_rombel', $idrombel);
    $builder->where('sts_hadir', 0);
    $builder->where('MONTH(tgl_hadir)', $bln);
    $builder->where('YEAR(tgl_hadir)', $thn);
    $all =  $builder->countAllResults();
    if($all>0){
        //ambil jam masuk
        $query = $db->query("SELECT t_siswa_hadir.id_siswa as id_siswa,jam,tgl_hadir FROM t_siswa_hadir
        Join t_siswa_rombel ON t_siswa_rombel.id_siswa = t_siswa_hadir.id_siswa
        where MONTH(tgl_hadir)='$bln' and YEAR(tgl_hadir)='$thn' and sts_hadir=0 AND id_rombel='$idrombel'");
        foreach ($query->getResult() as $row) {
            $id_siswa =  $row->id_siswa;
            $jam_abs =  $row->jam;
            $tgl_abs =  $row->tgl_hadir;

            
            $kode = date('l', strtotime($tgl_abs)); 
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

            //cek jam masuk pada hari ini
            $query_jam = $db->query("SELECT sts_hari,jammasuk FROM r_hari where nm_hari='$hari'");
            $row_jam = $query_jam->getRow();
            $jammasuk = $row_jam->jammasuk;

            if(($row_jam->sts_hari)==2){
                $terlambat[] = 0;
            }else{
                if($jam_abs>$jammasuk){
                    $terlambat[] = 1;
                }else{
                    $terlambat[] = 0;
                }
            }

                
        }
        $sts = array_sum($terlambat);
       
    }else{
        $sts = 0;
    }

    return $sts;
}

function jumterlambattgl($tgl) {
    $sumterlambat = 0;
    $db = \Config\Database::connect();
    //ambil data siswa
    $query = $db->query("SELECT id_siswa FROM t_siswa where sts_siswa=1");
    foreach ($query->getResult() as $row) {
        $id_siswa = $row->id_siswa;
        $sts = sts_absen($id_siswa,$tgl);
        if($sts=='Terlambat'){
            $sumterlambat++;
        }
    }
     $terlambat = $sumterlambat;

     return $terlambat;
}

function jmlpoint($id_siswa,$bln) {
    $db = \Config\Database::connect();
    $thn = date('Y');
    $builder = $db->table('t_point_siswa');
    $builder->where('id_siswa', $id_siswa);
    $builder->where('MONTH(tgl_point)', $bln);
    $builder->where('YEAR(tgl_point)', $thn);
    $all =  $builder->countAllResults();
    if($all>0){
       //jumlah point
        $query = $db->query("SELECT sum(nilai) as point FROM t_point_siswa where id_siswa='$id_siswa' and MONTH(tgl_point)='$bln' and YEAR(tgl_point)='$thn'");
        $row = $query->getRow();
        $sts = $row->point;
    }else{
        $sts=0;
    }
    
    return $sts;
}
function jmlpointtgl($id_siswa,$tgl) {
    $db = \Config\Database::connect();
    $builder = $db->table('t_point_siswa');
    $builder->where('id_siswa', $id_siswa);
    $builder->where('tgl_point', $tgl);
    $all =  $builder->countAllResults();
    if($all>0){
       //jumlah point
        $query = $db->query("SELECT nilai FROM t_point_siswa where id_siswa='$id_siswa' and tgl_point='$tgl'");
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
function hariliburtglindividu($tgl) {
    $kode = date('l', strtotime($tgl)); 
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