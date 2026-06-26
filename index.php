<?php 
include 'koneksi.php'; 

if (isset($_GET['aksi']) && $_GET['aksi'] == 'suka') {
    $id_suka = mysqli_real_escape_string($koneksi, $_GET['id_film']);
    mysqli_query($koneksi, "UPDATE film SET suka = suka + 1 WHERE id = '$id_suka'");
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAUL CINEMA - Streaming Film & Trailer</title>
    <link rel="icon" type="image/png" href="assets/logo.png">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background-color: #060913; color: #ffffff; padding-bottom: 60px; }
        nav { background-color: #0f172a; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e50914; position: sticky; top: 0; z-index: 100; gap: 10px; }
        nav h1 { color: #ffffff; font-size: 20px; font-weight: 900; letter-spacing: 2px; flex: 1; white-space: nowrap; }
        nav h1 span { color: #e50914; }
        .search-container { flex: 2; display: flex; justify-content: center; max-width: 450px; }
        .search-input { width: 100%; background-color: #1e293b; border: 2px solid #e50914; color: white; padding: 8px 15px; border-radius: 25px; font-size: 13px; font-weight: 500; }
        .right-menu { flex: 1; display: flex; justify-content: flex-end; gap: 10px; }
        .status-badge { background-color: rgba(229, 9, 20, 0.1); color: #ff4d4d; padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; border: 1px solid rgba(229, 9, 20, 0.3); text-decoration: none; }
        
        main { max-width: 1250px; margin: 30px auto; padding: 0 20px; }
        .section-film { margin-bottom: 45px; }
        h2 { font-size: 20px; font-weight: 800; margin-bottom: 25px; text-transform: uppercase; border-left: 5px solid #e50914; padding-left: 15px; }
        
        /* FIX: Menghilangkan scrollbar mengambang di mobile */
        .barisan-film-baru { display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important; gap: 20px !important; overflow-x: auto !important; padding-bottom: 20px; padding-left: 5px; -ms-overflow-style: none; scrollbar-width: none; }
        .barisan-film-baru::-webkit-scrollbar { display: none; }
        
        .kartu-film-baru { background-color: #0f172a; border-radius: 12px; overflow: hidden; text-decoration: none; color: white; border: 1px solid #1e293b; transition: all 0.25s ease; display: flex; flex-direction: column; flex: 0 0 170px; width: 170px; position: relative; }
        .kartu-film-baru:hover { transform: translateY(-8px); border-color: #e50914; }
        .rating-badge { position: absolute; top: 10px; right: 10px; background: #f59e0b; color: #000000; padding: 3px 6px; font-size: 10px; font-weight: 900; border-radius: 5px; z-index: 10; }
        .coming-badge { position: absolute; top: 10px; left: 10px; background: #e50914; color: #ffffff; padding: 3px 8px; font-size: 9px; font-weight: 800; border-radius: 5px; z-index: 10; text-transform: uppercase; }
        
        .btn-like { position: absolute; bottom: 80px; left: 10px; background-color: rgba(15, 23, 42, 0.9); border: 1px solid #1e293b; color: #ff4d4d; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; text-decoration: none; z-index: 20; display: flex; align-items: center; gap: 4px; }
        .info-wrapper { padding: 12px; background-color: #0f172a; z-index: 10; }
        .info-wrapper h3 { font-size: 14px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .info-wrapper p { color: #cbd5e1; font-size: 11px; margin-top: 5px; }
        .stats-info { display: flex; justify-content: space-between; margin-top: 8px; font-size: 10px; color: #64748b; font-weight: 600; }
        #pesan-kosong { color: #64748b; font-style: italic; font-size: 14px; display: none; padding: 20px 0; }
        @media (max-width: 480px) { nav { padding: 15px 10px; } nav h1 { font-size: 16px; } }
    </style>
</head>
<body>

    <nav>
        <h1>MAUL <span>CINEMA</span></h1>
        <div class="search-container">
            <input type="text" id="cariFilm" class="search-input" placeholder="Cari judul film favoritmu...">
        </div>
        <div class="right-menu">
            <a href="admin.php" class="status-badge" style="color: #f59e0b; border-color: #f59e0b;">⚙️ KELOLA</a>
            <div class="status-badge">● VIP MODE</div>
        </div>
    </nav>

    <main>
        <div id="pesan-kosong">Film tidak ditemukan... Coba cari judul lain.</div>
        
        <section class="section-film" id="sectionSedangTayang">
            <h2>Sedang Tayang</h2>
            <div class="barisan-film-baru">
                <?php
                $query1 = mysqli_query($koneksi, "SELECT * FROM film WHERE status = 'sedang_tayang' OR status IS NULL OR status = ''");
                while($data = mysqli_fetch_array($query1)) {
                    $gambar_final = (strpos(strtolower($data['judul']), 'naruto') !== false) ? 'assets/poster5.jpg' : 'assets/' . $data['poster'];
                ?>
                <div style="position: relative;">
                    <a href="index.php?aksi=suka&id_film=<?= $data['id']; ?>" class="btn-like">❤️ <span><?= $data['suka']; ?></span></a>
                    <a href="detail.php?id=<?= $data['id']; ?>" class="kartu-film-baru" data-judul="<?= strtolower($data['judul']); ?>">
                        <div style="position: relative; width: 170px; height: 245px; overflow: hidden; background-color: #1e293b; border-radius: 8px 8px 0 0;">
                            <div class="rating-badge">★ <?= $data['rating']; ?></div>
                            <img src="<?= $gambar_final; ?>" alt="<?= $data['judul']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div class="info-wrapper">
                            <h3><?= $data['judul']; ?></h3>
                            <p><?= $data['genre']; ?> • <?= $data['tahun']; ?></p>
                            <div class="stats-info">
                                <span>👁️ <?= number_format($data['dilihat'] ?? 0); ?> x</span>
                            </div>
                        </div>
                    </a>
                </div>
                <?php } ?>
            </div>
        </section>

        <section class="section-film" id="sectionAkanDatang">
            <h2>Akan Datang (Coming Soon)</h2>
            <div class="barisan-film-baru">
                <?php
                $query2 = mysqli_query($koneksi, "SELECT * FROM film WHERE status = 'akan_datang'");
                if(mysqli_num_rows($query2) == 0) {
                    echo "<p style='color: #64748b; font-style: italic; font-size: 13px; padding-left: 15px;'>Belum ada jadwal film baru...</p>";
                } else {
                    while($data2 = mysqli_fetch_array($query2)) {
                        $gambar_final2 = (strpos(strtolower($data2['judul']), 'naruto') !== false) ? 'assets/poster5.jpg' : 'assets/' . $data2['poster'];
                    ?>
                    <div>
                        <a href="detail.php?id=<?= $data2['id']; ?>" class="kartu-film-baru" data-judul="<?= strtolower($data2['judul']); ?>">
                            <div style="position: relative; width: 170px; height: 245px; overflow: hidden; background-color: #1e293b; border-radius: 8px 8px 0 0;">
                                <div class="coming-badge">SOON</div>
                                <img src="<?= $gambar_final2; ?>" alt="<?= $data2['judul']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div class="info-wrapper">
                                <h3><?= $data2['judul']; ?></h3>
                                <p><?= $data2['genre']; ?> • <?= $data2['tahun']; ?></p>
                                <div class="stats-info">
                                    <span>👁️ <?= number_format($data2['dilihat'] ?? 0); ?> x</span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php } } ?>
            </div>
        </section>
    </main>

    <script>
        const inputCari = document.getElementById('cariFilm');
        const kartuFilm = document.querySelectorAll('.kartu-film-baru');
        const pesanKosong = document.getElementById('pesan-kosong');
        const sectionSedangTayang = document.getElementById('sectionSedangTayang');
        const sectionAkanDatang = document.getElementById('sectionAkanDatang');

        inputCari.addEventListener('input', function() {
            const kataKunci = inputCari.value.toLowerCase().trim();
            let adaFilmCocok = false;

            kartuFilm.forEach(function(kartu) {
                const judulFilm = kartu.getAttribute('data-judul');
                if (judulFilm.includes(kataKunci)) {
                    kartu.parentElement.style.display = 'block';
                    adaFilmCocok = true;
                } else {
                    kartu.parentElement.style.display = 'none';
                }
            });

            if (kataKunci !== '') {
                sectionSedangTayang.querySelector('h2').style.display = 'none';
                sectionAkanDatang.querySelector('h2').style.display = 'none';
            } else {
                sectionSedangTayang.querySelector('h2').style.display = 'block';
                sectionAkanDatang.querySelector('h2').style.display = 'block';
            }

            if (!adaFilmCocok && kataKunci !== '') { pesanKosong.style.display = 'block'; } 
            else { pesanKosong.style.display = 'none'; }
        });
    </script>
</body>
</html>