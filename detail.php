<?php 
include 'koneksi.php'; 

if(!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
$id = mysqli_real_escape_string($koneksi, $id); 

// FITUR VIEW COUNTER: Otomatis bertambah +1 setiap kali link halaman ini dibuka pengunjung
mysqli_query($koneksi, "UPDATE film SET dilihat = dilihat + 1 WHERE id = '$id'");

$query = mysqli_query($koneksi, "SELECT * FROM film WHERE id = '$id'");
$data = mysqli_fetch_array($query);

if (!$data) {
    echo "<script>alert('Film tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}

if (isset($_POST['kirim_komentar'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $isi = mysqli_real_escape_string($koneksi, $_POST['isi_komentar']);
    
    if (!empty($nama) && !empty($isi)) {
        $insert = mysqli_query($koneksi, "INSERT INTO komentar (id_film, nama, isi_komentar) VALUES ('$id', '$nama', '$isi')");
        if ($insert) {
            header("Location: detail.php?id=" . $id);
            exit;
        }
    }
}

$gambar_detail_final = (strpos(strtolower($data['judul']), 'naruto') !== false) ? 'assets/poster5.jpg' : 'assets/' . $data['poster'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['judul']; ?> - MAUL CINEMA</title>
    <link rel="icon" type="image/png" href="assets/logo.png">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; transition: background-color 0.4s ease; }
        body { background-color: #060913; color: #ffffff; padding-bottom: 80px; position: relative; }
        
        /* ================= CSS FIX MODE BIOSKOP (MATI LAMPU) ================= */
        body::after {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.95); /* Tirai hitam pekat */
            z-index: 999;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.4s ease;
        }
        body.mode-gelap::after {
            opacity: 1;
            pointer-events: auto;
        }
        .video-box-wrapper, #btnBioskop {
            position: relative;
            z-index: 10000 !important;
        }
        body.mode-gelap .video-box {
            box-shadow: 0 0 50px rgba(229, 9, 20, 0.4); /* Efek glow bioskop */
            border-color: #e50914;
        }
        /* ===================================================================== */
        
        nav { background-color: #0f172a; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #1f2937; }
        nav a { color: #e50914; text-decoration: none; font-size: 14px; font-weight: 700; letter-spacing: 1px; }
        
        .container { max-width: 1000px; margin: 30px auto; padding: 0 20px; }
        
        /* FIX: Rasio Layar Dikunci 16:9 & Video Di-cover Agar Pas dan Tidak Memanjang Kebawah */
        .video-box { background-color: #000000; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.7); border: 1px solid #1e293b; margin-bottom: 15px; width: 100%; aspect-ratio: 16/9; }
        video { width: 100% !important; height: 100% !important; display: block; object-fit: cover; }
        
        .btn-bioskop { background-color: #1e293b; color: #cbd5e1; border: 1px solid #334155; padding: 8px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; cursor: pointer; margin-bottom: 25px; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; }
        .btn-bioskop:hover { background-color: #e50914; color: white; }
        
        .detail-flex { display: flex; gap: 30px; background-color: #0f172a; padding: 25px; border-radius: 12px; border: 1px solid #1e293b; margin-bottom: 35px; }
        .detail-poster { flex: 0 0 170px; height: 250px; border-radius: 8px; overflow: hidden; border: 1px solid #374151; }
        .detail-poster img { width: 100%; height: 100%; object-fit: cover; }
        
        .detail-info { flex: 1; display: flex; flex-direction: column; justify-content: center; }
        .detail-info h2 { font-size: 28px; font-weight: 800; margin-bottom: 10px; }
        .meta-tags { display: flex; gap: 10px; align-items: center; font-size: 13px; color: #94a3b8; margin-bottom: 18px; }
        .tag-genre { background-color: rgba(229, 9, 20, 0.15); color: #ff4d4d; padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 12px; border: 1px solid rgba(229, 9, 20, 0.2); }
        .rating-star { color: #eab308; font-weight: 700; }
        .sinopsis { color: #94a3b8; font-size: 14px; line-height: 1.6; border-top: 1px solid #1e293b; padding-top: 15px; }
        
        .section-komentar { background-color: #0f172a; border-radius: 12px; border: 1px solid #1e293b; padding: 25px; }
        .section-komentar h3 { font-size: 18px; font-weight: 700; margin-bottom: 20px; border-bottom: 2px solid #1e293b; padding-bottom: 10px; color: #ff4d4d; }
        .form-grup { margin-bottom: 15px; }
        .form-grup input, .form-grup textarea { width: 100%; background-color: #060913; border: 1px solid #1e293b; border-radius: 8px; padding: 12px; color: white; font-size: 14px; }
        .form-grup input:focus, .form-grup textarea:focus { outline: none; border-color: #e50914; }
        .btn-kirim { background-color: #e50914; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 14px; transition: background 0.2s; }
        .btn-kirim:hover { background-color: #b90710; }
        
        .box-komentar-user { background-color: #060913; border: 1px solid #1e293b; padding: 15px; border-radius: 8px; margin-top: 15px; }
        .nama-user { font-weight: 700; color: #f59e0b; font-size: 14px; margin-bottom: 4px; }
        .waktu-komentar { font-size: 11px; color: #6b7280; font-weight: normal; margin-left: 10px; }
        .isi-teks-user { color: #cbd5e1; font-size: 14px; line-height: 1.5; }
        
        @media (max-width: 640px) { .detail-flex { flex-direction: column; } .detail-poster { display: none; } }
    </style>
</head>
<body>

    <nav id="navbarMain">
        <a href="index.php">← KEMBALI KE BERANDA</a>
        <div style="font-size: 12px; color: #94a3b8; font-weight: 600;">MAUL CINEMA PLAYER</div>
    </nav>

    <div class="container">
        
        <div class="video-box-wrapper">
            <div class="video-box">
                <?php if (!empty($data['video_trailer'])): ?>
                    <video controls preload="auto">
                        <source src="assets/<?= $data['video_trailer']; ?>" type="video/mp4">
                        Browser Anda tidak mendukung pemutar video HTML5.
                    </video>
                <?php else: ?>
                    <div style="width: 100%; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; background-color: #0f172a; color: #cbd5e1; border-radius: 12px;">
                        <span style="font-size: 50px;">🎬</span>
                        <p style="margin-top: 10px; font-weight: 600;">Trailer belum tersedia untuk film ini</p>
                        <p style="font-size: 12px; color: #e50914; margin-top: 5px;">Nantikan segera di MAUL CINEMA!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($data['video_trailer'])): ?>
            <button class="btn-bioskop" id="btnBioskop" onclick="toggleBioskop()">💡 Matikan Lampu</button>
        <?php endif; ?>

        <div class="detail-flex" id="detailContent">
            <div class="detail-poster">
                <img src="<?= $gambar_detail_final; ?>" alt="<?= $data['judul']; ?>">
            </div>
            <div class="detail-info">
                <h2><?= $data['judul']; ?></h2>
                <div class="meta-tags">
                    <span class="tag-genre"><?= $data['genre']; ?></span>
                    <span>•</span>
                    <span>Tahun: <b><?= $data['tahun']; ?></b></span>
                    <span>•</span>
                    <span class="rating-star">★ <?= $data['rating']; ?> / 10</span>
                    <span>•</span>
                    <span style="color:#a855f7; font-weight:600;">👁️ Ditonton: <?= number_format($data['dilihat'] ?? 0); ?> kali</span>
                </div>
                <p class="sinopsis"><?= $data['sinopsis']; ?></p>
            </div>
        </div>

        <div class="section-komentar" id="komentarContent">
            <h3>Ulasan & Komentar Penonton</h3>
            
            <form action="" method="POST">
                <div class="form-grup">
                    <input type="text" name="nama" placeholder="Masukkan nama Anda..." required>
                </div>
                <div class="form-grup">
                    <textarea name="isi_komentar" rows="3" placeholder="Tulis ulasan film di sini..." required></textarea>
                </div>
                <button type="submit" name="kirim_komentar" class="btn-kirim">Kirim Ulasan</button>
            </form>

            <div style="margin-top: 30px;">
                <?php
                $ambil_komentar = mysqli_query($koneksi, "SELECT * FROM komentar WHERE id_film = '$id' ORDER BY waktu DESC");
                if (mysqli_num_rows($ambil_komentar) == 0) {
                    echo "<p style='color: #6b7280; font-size: 14px; font-style: italic;'>Belum ada ulasan untuk film ini. Jadi yang pertama berkomentar!</p>";
                } else {
                    while ($kom = mysqli_fetch_array($ambil_komentar)) {
                ?>
                    <div class="box-komentar-user">
                        <div class="nama-user">
                            <?= htmlspecialchars($kom['nama']); ?>
                            <span class="waktu-komentar"><?= $kom['waktu']; ?></span>
                        </div>
                        <p class="isi-teks-user"><?= nl2br(htmlspecialchars($kom['isi_komentar'])); ?></p>
                    </div>
                <?php 
                    }
                } 
                ?>
            </div>
        </div>
    </div>

    <script>
        function toggleBioskop() {
            const body = document.body;
            const btn = document.getElementById('btnBioskop');
            
            body.classList.toggle('mode-gelap');
            
            if(body.classList.contains('mode-gelap')) {
                btn.innerText = "💡 Hidupkan Lampu";
                btn.style.backgroundColor = "#e50914";
                btn.style.color = "#ffffff";
                btn.style.borderColor = "#e50914";
            } else {
                btn.innerText = "💡 Matikan Lampu";
                btn.style.backgroundColor = "#1e293b";
                btn.style.color = "#cbd5e1";
                btn.style.borderColor = "#334155";
            }
        }
    </script>

</body>
</html>