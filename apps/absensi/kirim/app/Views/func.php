<?php
function level($kode){
    if($kode==1){
        echo "Administrator";
    }elseif($kode==2){
        echo "Dosen";
    }elseif($kode==3){
        echo "Karyawan";
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
    $db = \Config\Database::connect();
    $query = $db->query("SELECT sts_hari FROM r_hari where nm_hari='$hari'");
    $row = $query->getRow();
    $sts = $row->sts_hari;
    if($sts==2){
        $sts="Libur";
    }else{
        //cek apakah ada absen masuk
        $db = \Config\Database::connect();
        $builder = $db->table('tweb_pegawai_hadir');
        $builder->where('ID_PEGAWAI', $id_ptk);
        $builder->where('STATUS', 0);
        $builder->where('TANGGAL', $tgl);
        $all =  $builder->countAllResults();
        if($all>0){
            //ambil jam masuk
            $query = $db->query("SELECT JAM,t_anggota_shift.id_shift as id_shift,jam_masuk FROM tweb_pegawai_hadir 
            JOIN t_anggota_shift ON t_anggota_shift.id_ptk = tweb_pegawai_hadir.ID_PEGAWAI
            JOIN r_shift ON t_anggota_shift.id_shift = r_shift.id_shift
            where ID_PEGAWAI='$id_ptk' and TANGGAL='$tgl' and STATUS=0");
            $row = $query->getRow();
            $jam = $row->JAM;
            $jam_masuk = $row->jam_masuk;
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
function jumalphatgl($id_ptk) {
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

    //cek absen masuk
	$builder = $db->table('tweb_pegawai_hadir');
    $builder->where('ID_PEGAWAI', $id_ptk);
    $builder->where('STATUS', 0);
    $builder->where('TANGGAL <=', $tgl);
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
    $builder1->where('TANGGAL_ABSEN <=', $tgl);
    $builder1->where('MONTH(TANGGAL_ABSEN)', $bln);
    $all1 =  $builder1->countAllResults();
    if($all1>0){
       $ijinsakit = $all1;
    }else{
        $ijinsakit=0;
    }

    $sts     = $harimasuk-$masuk-$ijinsakit;
    return $sts;
}
function jumalphabln($id_ptk,$bln) {
    $thn = date('Y');
    $sum = 0;
    //jumlah hari di bulan terpilih
    $jumHari = cal_days_in_month(CAL_GREGORIAN, $bln, $thn);
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
	$builder = $db->table('tweb_pegawai_hadir');
    $builder->where('ID_PEGAWAI', $id_ptk);
    $builder->where('STATUS', 0);
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
    $builder1->where('MONTH(TANGGAL_ABSEN)', $bln);
    $all1 =  $builder1->countAllResults();
    if($all1>0){
       $ijinsakit = $all1;
    }else{
        $ijinsakit=0;
    }

    $sts     = $harimasuk-$masuk-$ijinsakit;
    return $sts;
}
function jumterlambatbln($id_ptk,$bln) {
    $db = \Config\Database::connect();
	$builder = $db->table('tweb_pegawai_hadir');
    $builder->where('ID_PEGAWAI', $id_ptk);
    $builder->where('STATUS', 0);
    $builder->where('MONTH(TANGGAL)', $bln);
    $all =  $builder->countAllResults();
    if($all>0){
        //ambil jam masuk
        $query = $db->query("SELECT JAM,t_anggota_shift.id_shift as id_shift,jam_masuk FROM tweb_pegawai_hadir 
        JOIN t_anggota_shift ON t_anggota_shift.id_ptk = tweb_pegawai_hadir.ID_PEGAWAI
        JOIN r_shift ON t_anggota_shift.id_shift = r_shift.id_shift
        where ID_PEGAWAI='$id_ptk' and MONTH(TANGGAL)='$bln' and STATUS=0");
        /*
        $row_terlambat  = $query->getResult();
        $jml_terlambat = count($row_terlambat);
        */
        foreach ($query->getResult() as $row) {
            $jam = $row->JAM;
            $jam_masuk = $row->jam_masuk;
            if($jam>$jam_masuk){
                $jml[]=1;
            }else{
                $jml[]=0;
            }
        }
        $sts = array_sum($jml);
       
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