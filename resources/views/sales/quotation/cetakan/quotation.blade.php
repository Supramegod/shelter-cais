<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QUOTATION - {{$leads->nama_perusahaan}}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 0;
            line-height: 1.5;
            color:#002060;
            text-align: justify;
        }

        @page {
            size: A4;
            margin: 0; /* Menghilangkan semua margin untuk pencetakan */
        }

        .cover {
            height: 100vh; /* Mengisi halaman penuh */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cover img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Memastikan gambar mengisi area dengan proporsional */
        }

        .content {
            page-break-after: always; /* Memastikan setiap konten di halaman baru */
            margin-left: 20mm;
            margin-right: 20mm;
            margin-bottom: 20mm;
        }

        h1, h2, h3 {
            margin: 0;
            padding: 0;
        }

        .kata-pengantar {
            margin-top: 40mm; /* Margin atas untuk konten */
        }
        .pendahuluan {
            margin-top: 20mm; /* Margin atas untuk konten */
        }
        .bordered table {
            border-collapse: collapse; /* Menghilangkan jarak antar border */
            width: 100%; /* Mengisi lebar penuh halaman */
            margin-top: 20px; /* Jarak atas tabel */
        }
        .bordered th,.bordered td {
            border: 1px solid black; /* Border solid 1px hitam */
            text-align: left; /* Rata kiri untuk teks dalam sel */
            color:black !important;
            padding-left:5px;
            padding-right:5px;

        }
    
        .no-l-border {
            border-left:none !important;
        }
        .bordered th {
            background-color: #f2f2f2; /* Warna latar belakang untuk header */
        }

        @media print {
            th {
                background-color: #f2f2f2 !important; /* Mengatur warna latar belakang header saat mencetak */
                color: black; /* Pastikan teks berwarna hitam */
            }
            /* Menghilangkan warna latar belakang untuk sel data jika diinginkan */
            td {
                background-color: white !important; /* Mengatur latar belakang sel data saat mencetak */
            }
        }

    </style>
</head>
<body>

    <!-- Halaman 1: Cover -->
    <div class="cover">
        <img src="{{ asset('public/assets/img/cover-quotation.jpg') }}" alt="Cover Image">
    </div>

    <!-- Halaman 2: Kata Pengantar -->
    <div class="content">
        <div class="kata-pengantar"><br><br><br>
            <h1>SURAT <br>
            PENGANTAR</h1>
            <br>
<p>Dengan hormat,</p>
<p>Kami, <b>Shelter Indonesia</b> mengucapkan salam sejahtera untuk Bpk/Ibu dan seluruh jajaran
manajemen di <b>{{$leads->nama_perusahaan}}</b>. Sehubungan dengan semakin kompleksnya
tuntutan bisnis dimasa sekarang, kami ingin menawarkan layanan yang dapat membantu
meningkatkan efisiensi dan produktivitas operasional perusahaan Bpk/Ibu.</p>
<p><b>Shelter Indonesia</b> merupakan perusahaan yang berdedikasi untuk memberikan solusi Alih
Daya terkini dan berkualitas tinggi kepada pelanggan kami. Kami memiliki pengalaman yang
luas dalam menyediakan berbagai layanan Alih Daya di berbagai sektor industri. Melalui
pendekatan yang terintegrasi dengan teknologi dan berorientasi pada kualitas, kami
bertekad untuk menjadi mitra yang handal dalam mendukung kesuksesan dan pertumbuhan
perusahaan Bpk/Ibu.</p>
<p>
Kami berharap dapat mengadakan pertemuan lebih lanjut untuk membahas proposal
penawaran kami lebih detail. Terlampir, kami sertakan proposal lengkap yang merinci
layanan-layanan yang kami tawarkan bersama dengan biaya yang terkait.</p>

<p>Demikian surat pengantar ini kami sampaikan. Kami mengucapkan terima kasih atas
perhatian Bpk/Ibu, dan kami sangat berharap dapat menjadi mitra strategis yang membantu
mencapai tujuan bisnis perusahaan Bpk/Ibu.
</p>
<p>
Hormat kami,
<br><br><br>
<b>Achmad Firmanto</b>
<br>Business Development
<br>+62 822 404 480 55
</p>
</div>
</div>

        <!-- Halaman 3: Pendahuluan -->
        <div class="content">
            <div class="pendahuluan">
                <h1>KETENTUAN PENAWARAN HARGA</h1>
                <h1>Jasa {{$quotationKebutuhan[0]->kebutuhan}}</h1>
                <div style="margin-top:20px">
                    <table>
                        @foreach($listKerjasama as $kker => $valker)
                        <tr>
                            <td style="width:10%;vertical-align: top;">{{$kker+1}}.</td>
                            <td>{!! $valker->perjanjian !!}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
</body>
<script>
        window.print();
    </script>
</html>
