<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>{{ $title }}</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 30px;
    }

    /* html{margin:0px} */
    @page { margin: 0px 0px; }

    .receipt {
      border: 2px solid #333;
      padding: 20px;
      width: 700px;
      /* margin: auto; */
    }

    .header, .footer {
      font-size: 0.8em;
      margin-bottom: 10px;
    }

    .title {
      text-align: center;
      font-size: 1.5em;
      font-weight: bold;
      margin: 20px 0;
    }

    .section {
      margin-bottom: 15px;
    }

    .section p {
      margin: 5px 0;
      border-bottom: 1px dotted #555;
      padding-bottom: 3px;
    }

    .section2 p {
      margin-top: 30px;
      padding-bottom: 3px;
    }
    
    .header-section {
      display: flex !important;
      justify-content: space-between !important; 
      margin-top: 5px !important;
      width:fit-content !important;
    }
    
    .signature-section {
      display: flex;
      /* justify-content: space-between; */
      margin-top: 5px;
    }

    .signature {
      width: 45%;
      text-align: center;
    }

    .print-button {
      display: block;
      margin: 20px auto;
      padding: 10px 20px;
      font-size: 16px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    .print-button:hover {
      background-color: #0056b3;
    }

    @media print {
      .print-button {
        display: none;
      }
    }
  </style>
</head>
<body>

<div class="receipt">
  <!--<div class="header">-->
  <!--  No. Dokumen: FM-FAL-01-003<br>-->
  <!--  Revisi: 01<br>-->
  <!--  Tanggal Terbit: 30 Mei 2016 | TT: 039209-->
  <!--</div>-->
  
  <table style="width: 100%">
    <tr class="header">
      <td>No. Dokumen: FM-FAL-01-003</td>
      <td>TT: {{$transaksi_id}}</td>
    </tr>
    <tr class="header">
      <td>Revisi: 01</td>
      <td></td>
    </tr>
    <tr class="header"  >
      <td>Tanggal Terbit: 30 Mei 2016</td>
      <td></td>
    </tr>
  </table>

  
  <br>
  <div class="title">{{ $title }}</div>
  <br>
  <div class="section">
    <p><strong>Telah diterima dari&nbsp;:</strong> {{ $name }}</p>
    <p><strong>Berupa&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</strong> Uang tunai Rp. {{$nominal}},- ({{$terbilang}} Rupiah)</p>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Untuk Biaya {{$jenis_pelatihan}} Periode: -</p>
  </div>

  <div class="section2">
    <t><p><strong>Surabaya, {{$tanggal}}</strong></p>
  </div>

  <table style="width: 100%">
    <tr class="header" style = "text-align: center">
      <td>Yang Menyerahkan</td>
      <td>Penerima</td>
    </tr>
    <tr class="header">
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr class="header">
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr class="header">
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr class="header" style = "text-align: center">
      <td>({{ $name }})</td>
      <td>(M. Choiril A)</td>
    </tr>
  </table>
</div>

<!--<button class="print-button" onclick="window.print()">Print or Save as PDF</button>-->

</body>
</html>