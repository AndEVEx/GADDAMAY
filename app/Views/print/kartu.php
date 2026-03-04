<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Kartu Pelajar</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

  <style>
    body {
      background: #f0f0f0;
      font-family: 'Poppins', sans-serif;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 20px;
      margin: 0;
    }

    .global-actions {
      margin-bottom: 20px;
    }

    .global-actions button {
      padding: 8px 15px;
      margin: 5px;
      border: none;
      background-color: #006837;
      color: white;
      border-radius: 5px;
      cursor: pointer;
    }

    .global-actions button:hover {
      background-color: #004d29;
    }

    .kartu-wrapper {
      margin-bottom: 30px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .kartu-pelajar {
      width: 600px;
      height: 340px;
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      transform: translateZ(0);
    }

    .kartu-header {
      background-color: #006837;
      color: white;
      display: flex;
      align-items: center;
      padding: 10px 20px;
    }

    .kartu-header img.logo {
      height: 50px;
      margin-right: 15px;
    }

    .kartu-header .header-text {
      flex: 1;
      text-align: left;
    }

    .kartu-header .header-text h1 {
      margin: 0;
      font-size: 18px;
    }

    .kartu-header .header-text p {
      margin: 0;
      font-size: 12px;
    }

    .kartu-body {
      display: flex;
      flex: 1;
      padding: 15px;
    }

    .foto-siswa {
      width: 120px;
      height: 150px;
      border: 2px solid #ccc;
      border-radius: 10px;
      overflow: hidden;
      margin-right: 15px;
    }

    .foto-siswa img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .data-siswa {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .data-siswa h2 {
      margin: 0 0 10px 0;
      color: #003366;
      font-size: 16px;
    }

    .data-siswa table {
      font-size: 12px;
      width: 100%;
    }

    .data-siswa td {
      padding: 3px 5px;
      vertical-align: top;
    }

    .data-siswa .info-dan-footer {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
    }

    .qr {
      width: 80px; /* kamu bisa atur sesuai desain */
      aspect-ratio: 1 / 1;
      background: #fff;
      padding: 8px;
      border-radius: 8px;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .qr img {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }


    .footer {
      font-size: 11px;
      text-align: center;
      padding-left: 10px;
    }

    .footer .ttd {
      border-top: 1px solid #666;
      margin-top: 5px;
      padding-top: 2px;
    }

    .actions {
      margin-top: 8px;
    }

    .actions button {
      padding: 5px 10px;
      margin: 3px;
      border: none;
      background-color: #006837;
      color: white;
      border-radius: 5px;
      cursor: pointer;
      font-size: 12px;
    }

    .actions button:hover {
      background-color: #004d29;
    }

    @media print {
      .global-actions,
      .actions {
        display: none !important;
      }
      body {
        background: none;
      }
    }
  </style>
</head>
<body>

<div class="global-actions">
  <button onclick="window.print()">Cetak Semua</button>
</div>

<?php foreach ($getSiswa as $i => $data): 
  $id = 'kartuPelajar_' . $i;
?>
<div class="kartu-wrapper" id="wrapper-<?= $i ?>">
  <div class="kartu-pelajar" id="<?= $id ?>">
    <div class="kartu-header">
      <img src="<?= base_url() ?>/image/<?= $getLogo ?>" alt="Logo Sekolah" class="logo">
      <div class="header-text">
        <h1><?= $getNama ?></h1>
        <p><?= $getAlamat ?></p>
      </div>
    </div>

    <div class="kartu-body">
      <div class="foto-siswa">
        <img src="<?= base_url() ?>/image/siswa/<?= $data['file']; ?>" alt="Foto Siswa">
      </div>
      <div class="data-siswa">
        <div>
          <h2><?= $data['nm_siswa'] ?></h2>
          <table>
            <tr>
              <td style="width: 140px;">Nomor Induk</td>
              <td>:</td>
              <td><?= $data['no_induk'] ?></td>
            </tr>
            <tr>
              <td>Tempat, Tanggal Lahir</td>
              <td>:</td>
              <td><?= $data['tempat_lahir'] ?>, <?= formatTanggal($data['tgl_lahir']) ?></td>
            </tr>
            <tr>
              <td>Kelas</td>
              <td>:</td>
              <td><?= $data['nm_rombel'] ?></td>
            </tr>
            <tr>
              <td>Alamat</td>
              <td>:</td>
              <td><?= $data['alamat'] ?></td>
            </tr>
          </table>
        </div>
 
        <div class="info-dan-footer">
          <div class="footer">
            Kepala Sekolah<br><br>
            <div class="ttd"><strong><?= $getKepsek ?></strong></div>
          </div>
          <div class="qr">
          <img crossorigin="anonymous"
            src="<?= base_url('Siswa/qr/' . $data['nisn']) ?>"
            alt="Barcode">
          </div>
        </div>
      </div>

    </div>
 
  </div>

  <div class="actions">
    <button onclick="downloadPDF('<?= $id ?>')">Download PDF</button>
    <button onclick="downloadImage('<?= $id ?>')">Download Gambar</button>
  </div>
</div>
<?php endforeach; ?>

</body>
</html>
<script>
async function waitImagesLoaded(element) {
    const images = element.querySelectorAll("img");
    await Promise.all([...images].map(img => {
        if (img.complete) return Promise.resolve();
        return new Promise(resolve => img.onload = resolve);
    }));
}

async function generateCanvas(cardId) {
    const element = document.getElementById(cardId);
    await waitImagesLoaded(element);

    return await html2canvas(element, {
        scale: 3,
        useCORS: true,
        backgroundColor: null
    });
}

async function downloadImage(cardId) {
    const canvas = await generateCanvas(cardId);
    const link = document.createElement("a");
    link.download = cardId + ".png";
    link.href = canvas.toDataURL("image/png");
    link.click();
}

async function downloadPDF(cardId) {
    const { jsPDF } = window.jspdf;
    const card = document.getElementById(cardId);
    const rect = card.getBoundingClientRect();

    const canvas = await generateCanvas(cardId);
    const imgData = canvas.toDataURL("image/png");

    const mmWidth = rect.width * 0.264583; // pixel → mm
    const mmHeight = rect.height * 0.264583;

    const pdf = new jsPDF({
        orientation: mmWidth > mmHeight ? "landscape" : "portrait",
        unit: "mm",
        format: [mmWidth, mmHeight]
    });

    pdf.addImage(imgData, "PNG", 0, 0, mmWidth, mmHeight);
    pdf.save(cardId + ".pdf");
}
</script>
