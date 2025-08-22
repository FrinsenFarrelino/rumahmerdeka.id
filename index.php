<?php
// [UPDATE v1.1] Kode untuk menghitung page views
require_once 'config.php';
$counter_conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$counter_conn->connect_error) {
    $page_view_id = 'page_views';
    $sql = "INSERT INTO button_clicks (button_id, click_count) VALUES (?, 1) ON DUPLICATE KEY UPDATE click_count = click_count + 1";
    $stmt = $counter_conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $page_view_id);
        $stmt->execute();
        $stmt->close();
    }
    $counter_conn->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RUMAH MERDEKA - Wujudkan Mimpimu Punya Rumah!</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        html { scroll-behavior: smooth; }
        body { font-family: 'Poppins', sans-serif; background-color: #f8fafc; }
        .hero-gradient { background: linear-gradient(135deg, #e63946, #c9184a); }
        .btn-cta { background-image: linear-gradient(to right, #e11d48, #be123c); color: white; font-weight: 700; padding: 1rem 2.5rem; border-radius: 9999px; display: inline-block; transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1); border: 2px solid transparent; }
        .btn-cta:hover { transform: scale(1.05) translateY(-0.25rem); box-shadow: 0 0 20px 5px rgb(244 63 94 / 0.5); }
        .btn-cta:focus { outline: none; box-shadow: 0 0 0 4px #fecdd3; }
        .btn-whatsapp { background-color: #22c55e; color: white; font-weight: 700; padding: 0.75rem 1.5rem; border-radius: 9999px; display: flex; align-items: center; gap: 0.5rem; transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1); }
        .btn-whatsapp:hover { transform: scale(1.05) translateY(-0.25rem); background-color: #16a34a; box-shadow: 0 0 20px 5px rgb(34 197 94 / 0.5); }
        .btn-whatsapp:focus { outline: none; box-shadow: 0 0 0 4px #bbf7d0; }
        .card { background-color: white; border-radius: 1rem; padding: 2rem 1.5rem; box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.07), 0 4px 6px -4px rgb(0 0 0 / 0.07); transition: transform 300ms; }
        .card:hover { transform: translateY(-0.5rem); }
        .countdown-box { background-image: linear-gradient(to bottom right, rgba(255,255,255,0.3), rgba(255,255,255,0.1)); backdrop-filter: blur(4px); text-align: center; padding: 1rem; border-radius: 0.75rem; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1); }
        .countdown-number { font-size: 24px; line-height: 2.5rem; font-weight: 800; letter-spacing: -0.025em; text-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        @media (min-width: 768px) { .countdown-number { font-size: 60px; line-height: 1; } .countdown-label { font-size: 14px; } }
        .countdown-label { font-size: 10px; line-height: 1.25rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: rgba(255, 255, 255, 0.8); }
        .floating-whatsapp { position: fixed; bottom: 20px; right: 20px; z-index: 1000; }
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 2000; }
        #testimonial-slider { touch-action: pan-y; }
        .grabbing { cursor: grabbing; cursor: -webkit-grabbing; }
        .testimonial-slide { box-sizing: border-box; display: flex; align-items: center; min-height: 420px; }
        .slider-dot { width: 12px; height: 12px; border-radius: 50%; background-color: #cbd5e1; border: none; padding: 0; cursor: pointer; transition: all 0.3s; }
        .slider-dot.active { background-color: #c9184a; transform: scale(1.25); }
    </style>
</head>

<script async src="https://www.googletagmanager.com/gtag/js?id=G-RFY58PMMBJ"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-RFY58PMMBJ');
</script>
<body class="text-gray-800">

    <header class="hero-gradient text-white">
        <div class="container mx-auto px-6 py-20 md:py-32 text-center">
            <div class="bg-white/90 text-red-600 font-bold py-1 px-4 rounded-full inline-block mb-4 text-sm animate-pulse text-xl">
                KHUSUS PEKERJA INDONESIA!
            </div>
            <h1 class="text-4xl md:text-6xl font-extrabold leading-tight mb-4">Bukan Lagi Mimpi, <br> Punya Rumah Sendiri!</h1>
            <p class="text-lg md:text-xl max-w-3xl mx-auto mb-8 text-white/90">
                Program RUMAH MERDEKA hadir untuk membantumu mewujudkan hunian idaman dengan kemudahan yang belum pernah ada sebelumnya. Promo spesial hanya di bulan Kemerdekaan!
            </p>
            <a href="#pendaftaran" id="daftar-sekarang-btn" class="btn-cta text-lg show-form-trigger">
                DAFTAR SEKARANG
                <i data-lucide="arrow-right" class="inline-block ml-2 w-5 h-5"></i>
            </a>
        </div>
    </header>

    <main>
        <section id="kenapa-harus" class="py-20 bg-white"><div class="container mx-auto px-6"><div class="text-center mb-12"><h2 class="text-xl sm:text-3xl lg:text-4xl font-bold mb-2">Capek Nge-kost? Saatnya <span class="text-red-600">#MerdekaPunyaRumah!</span></h2><p class="text-gray-600 max-w-2xl mx-auto">Lupakan cicilan mahal dan DP yang bikin pusing. RUMAH MERDEKA memberikan solusi nyata untuk kamu yang masih ngekost/kontrak/nebeng di rumah mertua.</p><p class="mt-2 text-red-600 max-w-2xl mx-auto text-bold"><strong>#ModalMAUaja #ModalNIATbaik</strong></p></div><div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8"><div class="card text-center"><div class="bg-red-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4"><i data-lucide="wallet" class="w-8 h-8 text-red-600"></i></div><h3 class="text-xl font-bold mb-2">DP 0% alias TANPA DP</h3><p class="text-gray-600">Betul, kamu tidak salah baca. Langsung pilih unit tanpa pusing mikirin uang muka dan biaya - biaya!</p></div><div class="card text-center"><div class="bg-red-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4"><i data-lucide="trending-down" class="w-8 h-8 text-red-600"></i></div><h3 class="text-xl font-bold mb-2">Cicilan Super Ringan</h3><p class="text-gray-600">Angsuran KPR subsidi yang <strong>flat sampai lunas</strong>, lebih ringan dari cicilan motor. Angsuran Rp. 1.072.000.</p></div><div class="card text-center"><div class="bg-red-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4"><i data-lucide="rocket" class="w-8 h-8 text-red-600"></i></div><h3 class="text-xl font-bold mb-2">Proses Anti Ribet</h3><p class="text-gray-600">Dengan BI Checking Instan dan pendampingan penuh, proses jadi sat-set-sat-set!</p></div><div class="card text-center"><div class="bg-red-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4"><i data-lucide="shield-check" class="w-8 h-8 text-red-600"></i></div><h3 class="text-xl font-bold mb-2">Didukung Pemerintah</h3><p class="text-gray-600">Program ini adalah kolaborasi resmi berbagai pihak untuk kesejahteraan pekerja.</p></div></div></div></section>
        <section id="penawaran-terbatas" class="py-20 hero-gradient text-white"><div class="container mx-auto px-6 text-center"><i data-lucide="clock" class="w-16 h-16 mx-auto mb-4"></i><h2 class="text-3xl md:text-4xl font-bold mb-4">Jangan Sampai Ketinggalan!</h2><p class="text-lg max-w-3xl mx-auto mb-8">Amankan unitmu sebelum kuota habis!</p><div id="countdown" class="grid grid-cols-4 gap-4 max-w-2xl mx-auto"><div class="countdown-box"><span id="days" class="countdown-number">00</span><br><span class="countdown-label">Hari</span></div><div class="countdown-box"><span id="hours" class="countdown-number">00</span><br><span class="countdown-label">Jam</span></div><div class="countdown-box"><span id="minutes" class="countdown-number">00</span><br><span class="countdown-label">Menit</span></div><div class="countdown-box"><span id="seconds" class="countdown-number">00</span><br><span class="countdown-label">Detik</span></div></div><br><br><video width="100%" controls class="rounded-lg shadow-lg"><source src="assets/video/video.mp4" type="video/mp4">Your browser does not support the video tag.</video></div></section>
        
        <section id="kisah-sukses" class="py-20 bg-slate-50">
            <div class="container mx-auto px-6">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold mb-2 text-gray-800">Dari Kontrakan ke Rumah Impian</h2>
                    <p class="text-gray-600 max-w-3xl mx-auto">Lihat bagaimana program ini mengubah hidup para pekerja seperti kamu. Geser untuk melihat kisah lainnya.</p>
                </div>
            </div>

            <div id="testimonial-slider" class="relative max-w-6xl mx-auto">
                <div class="overflow-hidden rounded-2xl shadow-lg bg-white">
                    <div id="slider-track" class="flex">
                        
                        <!-- Slide 1 -->
                        <div class="testimonial-slide w-full flex-shrink-0">
                            <div class="flex flex-col md:flex-row items-center justify-center gap-8 p-6 md:p-8 w-full">
                                <div class="w-48 h-36 md:w-64 md:h-48 flex-shrink-0">
                                    <img src="assets/images/testimonial.jpg" alt="Foto Sultan Hamsah dan keluarga" class="object-cover w-full h-full rounded-2xl shadow-lg">
                                </div>
                                <div class="md:w-2/3 text-center md:text-left">
                                    <i data-lucide="quote" class="w-10 h-10 text-red-200 mb-4 inline-block"></i>
                                    <blockquote class="text-lg md:text-xl text-gray-600 italic leading-relaxed">
                                        "Dulu tiap tahun pusing pindah kontrakan, sekarang alhamdulillah bisa pulang ke rumah sendiri. Rasanya tenang banget. Anak-istri juga happy. Terima kasih RUMAH MERDEKA!"
                                    </blockquote>
                                    <div class="mt-6">
                                        <p class="font-bold text-lg text-red-600">Sultan Hamsah</p>
                                        <p class="text-gray-500">Pekerja & Pemilik Rumah Baru</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 2 -->
                        <div class="testimonial-slide w-full flex-shrink-0">
                            <div class="flex flex-col md:flex-row items-center justify-center gap-8 p-6 md:p-8 w-full">
                                <div class="w-48 h-36 md:w-64 md:h-48 flex-shrink-0">
                                     <img src="assets/images/testimonial2.jpg" alt="Foto Reni Yolanda di depan rumah barunya" class="object-cover w-full h-full rounded-2xl shadow-lg">
                                </div>
                                <div class="md:w-2/3 text-center md:text-left">
                                    <i data-lucide="quote" class="w-10 h-10 text-red-200 mb-4 inline-block"></i>
                                    <blockquote class="text-gray-600 italic">
                                        <p class="text-lg leading-relaxed">"Gokil! Ternyata Sat Set Banget! Rumah impian akhirnya jadi kenyataan! ✨ Dulu boro-boro ngajak kumpul keluarga, di rumah ortu yang sepetak, tiap ada tamu kudu gulung kasur dulu. Sempit, cuy! Sekarang? Speechless. Program RUMAH MERDEKA prosesnya cuma 2 HARI dari pemberkasan sampe serah terima kunci. Gak pake ribet!"</p>
                                        <p class="text-lg leading-relaxed mt-4 font-semibold">Highly recommended!</p>
                                    </blockquote>
                                    <div class="mt-6">
                                        <p class="font-bold text-lg text-red-600">Reni Yolanda</p>
                                        <p class="text-gray-500">Karyawati Pabrik & Pemilik Rumah Baru</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Arrows -->
                <button id="prev-slide" aria-label="Previous Slide" class="absolute top-1/2 -left-4 transform -translate-y-1/2 bg-white/80 hover:bg-white rounded-full p-2 shadow-md transition z-10 hidden xl:flex items-center justify-center">
                    <i data-lucide="chevron-left" class="w-6 h-6 text-gray-700"></i>
                </button>
                <button id="next-slide" aria-label="Next Slide" class="absolute top-1/2 -right-4 transform -translate-y-1/2 bg-white/80 hover:bg-white rounded-full p-2 shadow-md transition z-10 hidden xl:flex items-center justify-center">
                    <i data-lucide="chevron-right" class="w-6 h-6 text-gray-700"></i>
                </button>

                <!-- Dot Navigation -->
                <div id="slider-dots" class="flex justify-center gap-3 mt-6"></div>
            </div>
        </section>

        <section class="py-20 bg-gray-50 text-center"><div class="container mx-auto px-6"><h2 class="text-3xl md:text-4xl font-bold mb-4 text-gray-800">Siap Mengambil Langkah Pertama?</h2><p class="text-gray-600 max-w-2xl mx-auto mb-8">Jangan tunda lagi mimpimu. Kuota terbatas dan waktu terus berjalan. Daftar sekarang juga!</p><a href="#pendaftaran" id="saya-mau-daftar-btn" class="btn-cta text-lg show-form-trigger">SAYA MAU DAFTAR <i data-lucide="home" class="inline-block ml-2 w-5 h-5"></i></a></div></section>
        <section class="py-12 bg-white"><div class="container mx-auto px-6"><h3 class="text-center text-gray-500 font-semibold uppercase tracking-widest mb-8">Didukung Penuh Oleh</h3><div class="flex flex-wrap justify-center items-center gap-x-12 gap-y-6"><img src="assets/images/2.png" alt="Logo KEMNAKER" class="h-10 opacity-60 hover:opacity-100 transition"><img src="assets/images/3.png" alt="Logo Kementerian PUPR" class="h-10 opacity-60 hover:opacity-100 transition"><img src="assets/images/5.png" alt="Logo BP TAPERA" class="h-10 opacity-60 hover:opacity-100 transition"><img src="assets/images/6.png" alt="Logo Bank BTN" class="h-12 opacity-60 hover:opacity-100 transition"><img src="assets/images/7.png" alt="Logo Grand Citeras" class="h-12 opacity-60 hover:opacity-100 transition"></div></div></section>
    </main>
    
    <section id="pendaftaran" class="hidden py-20 bg-red-50">
        <div class="container mx-auto px-6 max-w-3xl">
             <div class="text-center mb-6">
                <h2 class="text-3xl md:text-4xl font-bold mb-2">Formulir Pendaftaran</h2>
                <p class="text-gray-600">Isi data lengkap di bawah ini. Tim kami akan segera menghubungimu.</p>
            </div>

            <form id="registrationForm" novalidate class="bg-white p-8 rounded-2xl shadow-lg">
                <div id="formMessage" class="hidden p-4 mb-4 text-sm rounded-lg" role="alert"></div>
                
                <h3 class="text-xl font-bold text-red-600 border-b-2 border-red-200 pb-2 mb-4">Data Karyawan (Pendaftar)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label for="nama_karyawan" class="block font-semibold mb-1">Nama Lengkap</label><input type="text" id="nama_karyawan" name="nama_karyawan" placeholder="Sesuai KTP" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition" required></div>
                    <div><label for="nik_karyawan" class="block font-semibold mb-1">NIK</label><input type="text" id="nik_karyawan" name="nik_karyawan" placeholder="16 digit angka" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition" required></div>
                    <!-- [UPDATE v2.4] Field Nama Perusahaan -->
                    <div><label for="nama_perusahaan" class="block font-semibold mb-1">Nama Perusahaan</label><input type="text" id="nama_perusahaan" name="nama_perusahaan" placeholder="Nama Perusahaan" class="w-full p-3 border border-gray-300 rounded-lg" required></div>
                    <div><label for="nomor_induk_karyawan" class="block font-semibold mb-1">Nomor Induk Karyawan</p><input type="text" id="nomor_induk_karyawan" name="nomor_induk_karyawan" placeholder="Nomor Induk Karyawan" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition" required></div>
                    <div><label for="no_hp_karyawan" class="block font-semibold mb-1">No. HP / WhatsApp</label><input type="tel" id="no_hp_karyawan" name="no_hp_karyawan" placeholder="Contoh: 081234567890" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition" required></div>
                    <div><label for="email_karyawan" class="block font-semibold mb-1">Email <span class="text-gray-500 font-normal">(Opsional)</span></label><input type="email" id="email_karyawan" name="email_karyawan" placeholder="email@anda.com" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"></div>
                    <div class="md:col-span-2"><label for="alamat_karyawan" class="block font-semibold mb-1">Alamat Domisili</label><textarea id="alamat_karyawan" name="alamat_karyawan" rows="3" placeholder="Alamat lengkap saat ini" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition" required></textarea></div>
                    <div><label for="ktp_karyawan" class="block font-semibold mb-1">Upload KTP</label><input type="file" id="ktp_karyawan" name="ktp_karyawan" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100" required></div>
                </div>

                <div class="mt-6"><label class="block font-semibold mb-2">Status Perkawinan</label><div class="flex items-center gap-6"><label for="lajang" class="flex items-center gap-2 cursor-pointer"><input type="radio" id="lajang" name="status_perkawinan" value="lajang" class="h-4 w-4 text-red-600 focus:ring-red-500" checked><span>Lajang</span></label><label for="menikah" class="flex items-center gap-2 cursor-pointer"><input type="radio" id="menikah" name="status_perkawinan" value="menikah" class="h-4 w-4 text-red-600 focus:ring-red-500"><span>Sudah Menikah</span></label></div></div>
                
                <div id="incomeFields" class="mt-6 pt-6 border-t border-gray-200">
                    <div id="incomeSingle"><label class="block font-semibold mb-2">Apakah penghasilan Anda di antara Rp 4.000.000 - Rp 8.500.000 per bulan?</label><div class="flex items-center gap-6"><label class="flex items-center gap-2 cursor-pointer"><input type="radio" name="penghasilan_sesuai" value="ya" class="h-4 w-4 text-red-600 focus:ring-red-500" required><span>Ya</span></label><label class="flex items-center gap-2 cursor-pointer"><input type="radio" name="penghasilan_sesuai" value="tidak" class="h-4 w-4 text-red-600 focus:ring-red-500" required><span>Tidak</span></label></div></div>
                    <div id="incomeMarried" class="hidden"><label class="block font-semibold mb-2">Apakah penghasilan gabungan (Anda & Pasangan) di antara Rp 4.000.000 - Rp 10.000.000 per bulan?</label><div class="flex items-center gap-6"><label class="flex items-center gap-2 cursor-pointer"><input type="radio" name="penghasilan_sesuai_gabungan" value="ya" class="h-4 w-4 text-red-600 focus:ring-red-500"><span>Ya</span></label><label class="flex items-center gap-2 cursor-pointer"><input type="radio" name="penghasilan_sesuai_gabungan" value="tidak" class="h-4 w-4 text-red-600 focus:ring-red-500"><span>Tidak</span></label></div></div>
                </div>

                <div id="spouseFields" class="hidden mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-xl font-bold text-red-600 border-b-2 border-red-200 pb-2 mb-4">Data Pasangan (Suami/Istri)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label for="nama_pasangan" class="block font-semibold mb-1">Nama Lengkap Pasangan</label><input type="text" id="nama_pasangan" name="nama_pasangan" placeholder="Sesuai KTP" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"></div>
                        <div><label for="nik_pasangan" class="block font-semibold mb-1">NIK Pasangan</label><input type="text" id="nik_pasangan" name="nik_pasangan" placeholder="16 digit angka" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"></div>
                        <div><label for="no_hp_pasangan" class="block font-semibold mb-1">No. HP Pasangan</label><input type="tel" id="no_hp_pasangan" name="no_hp_pasangan" placeholder="Contoh: 081234567890" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"></div>
                        <div><label for="email_pasangan" class="block font-semibold mb-1">Email Pasangan <span class="text-gray-500 font-normal">(Opsional)</span></label><input type="email" id="email_pasangan" name="email_pasangan" placeholder="email@pasangan.com" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"></div>
                        <div class="md:col-span-2"><label for="alamat_pasangan" class="block font-semibold mb-1">Alamat Domisili Pasangan</label><textarea id="alamat_pasangan" name="alamat_pasangan" rows="3" placeholder="Alamat lengkap saat ini" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"></textarea></div>
                        <div><label for="ktp_pasangan" class="block font-semibold mb-1">Upload KTP Pasangan</label><input type="file" id="ktp_pasangan" name="ktp_pasangan" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100"></div>
                    </div>
                </div>
                <div class="mt-8 text-center"><button type="submit" id="submitBtn" class="btn-cta text-lg w-full md:w-auto">KIRIM PENDAFTARAN</button></div>
            </form>
        </div>
    </section>

    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; <span id="year"></span> RUMAH MERDEKA.</p>
            <p class="text-sm text-gray-400 mt-2">Sebuah Inisiatif untuk Kesejahteraan Pekerja Indonesia.</p>
        </div>
    </footer>

    <div class="floating-whatsapp">
        <a href="https://wa.me/6285811900138?text=Halo%20Admin%20RUMAH%20MERDEKA,%20saya%20tertarik%20dengan%20programnya%20dan%20ingin%20bertanya." target="_blank" class="btn-whatsapp">
            <i data-lucide="message-circle" class="w-6 h-6"></i>
            <span>Tanya CS</span>
        </a>
    </div>

    <div id="successModal" class="modal-overlay bg-black/60 flex items-center justify-center p-4 transition-opacity duration-300 hidden opacity-0">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full text-center transform scale-95 transition-transform duration-300">
            <div class="w-24 h-24 mx-auto mb-6">
                <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="50" cy="50" r="46" fill="#ecfdf5"/>
                    <path d="M50,4 A46,46 0 1,1 50,96 A46,46 0 1,1 50,4" stroke="#10b981" stroke-width="4" fill="none"/>
                    <path d="M30 50 L45 65 L70 35" stroke="#10b981" stroke-width="8" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Pendaftaran Berhasil!</h2>
            <p class="text-gray-600 mb-4">Terima kasih telah mendaftar di program RUMAH MERDEKA. Tim kami akan segera meninjau data Anda dan akan menghubungi Anda dalam 1-2 hari kerja.</p>
            <p class="text-gray-600 mb-6">Selain itu, silahkan untuk mendaftarkan diri melalui aplikasi SiKasep dengan urutan sebagai berikut:</p>
            <img src="assets/images/sikasep.jpg" alt="SiKasep" class="w-full h-auto mb-6">
            <button id="closeModalBtn" class="bg-red-600 text-white font-bold py-3 px-8 rounded-full hover:bg-red-700 transition-colors">Tutup</button>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="assets/js/main.js?v=<?= time(); ?>"></script>
</body>
</html>