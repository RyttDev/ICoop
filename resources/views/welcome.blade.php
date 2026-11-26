<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>iCOOP — Sistem Pengurusan Koperasi</title>
    <style>@php echo str_replace("@import 'tailwindcss';", '', file_get_contents(resource_path('css/app.css'))); @endphp</style>
</head>
<body>
@php
    $pages = ['dashboard'=>['Papan Pemuka','▦'],'ahli'=>['Ahli Koperasi','♙'],'simpanan'=>['Stock Management','◈'],'pembiayaan'=>['Statistik Jualan Bulanan','◫'],'transaksi'=>['Transaksi','⇄'],'laporan'=>['Laporan','▤'],'tetapan'=>['Tetapan','⚙']];
    $active = $page ?? 'dashboard';
@endphp
<div class="app-shell">
    <aside class="sidebar">
        <a class="brand" href="/dashboard"><span class="brand-mark">i</span><span>i<span>COOP</span></span></a>
        <p class="side-label">MENU UTAMA</p><nav>
        @foreach ($pages as $key => $item)<a href="/{{ $key }}" class="nav-link {{ $active === $key ? 'active' : '' }}"><i>{{ $item[1] }}</i>{{ $item[0] }}</a>@endforeach
        </nav>
        <div class="side-bottom"><div class="help-card"><span>?</span><div><b>Perlukan bantuan?</b><small>Rujuk pusat bantuan iCOOP</small></div></div><a href="/profil" class="profile"><span class="avatar">NA</span><span><b>Nur Aisyah</b><small>Administrator</small></span><em>⌄</em></a></div>
    </aside>
    <main class="main-content">
        <header class="topbar"><div class="mobile-brand">i<span>COOP</span></div><div class="search">⌕ <input aria-label="Carian" placeholder="Cari ahli, transaksi atau rekod..."></div><div class="top-actions"><label class="theme-toggle" aria-label="Tukar mod cerah atau gelap"><input id="theme-mode" type="checkbox"><span class="toggle-track"><i class="sun">☀</i><i class="moon">☾</i><b></b></span></label><a href="/notifikasi" class="icon-button" aria-label="Notifikasi">♧<b></b></a><a href="/profil" class="top-avatar">NA</a></div></header>
        @if ($active === 'dashboard')
        <section class="page-heading"><div><p class="eyebrow">SELAMAT KEMBALI, NUR AISYAH</p><h1>Ringkasan Koperasi</h1><p class="muted">Pantau prestasi koperasi anda dalam satu paparan.</p></div><a href="/transaksi" class="button primary">＋ Transaksi Baharu</a></section>
        <section class="metric-grid">
            <article class="metric-card"><div class="metric-icon green">◈</div><p>Jumlah Simpanan</p><h2>RM 248,500.00</h2><small class="up">↑ 8.4% <span>berbanding bulan lalu</span></small></article>
            <article class="metric-card"><div class="metric-icon blue">♙</div><p>Jumlah Ahli Aktif</p><h2>1,248</h2><small class="up">↑ 12 ahli <span>bulan ini</span></small></article>
            <article class="metric-card"><div class="metric-icon amber">◫</div><p>Pembiayaan Aktif</p><h2>RM 156,800.00</h2><small class="neutral">38 permohonan sedang diproses</small></article>
            <article class="metric-card"><div class="metric-icon purple">▤</div><p>Hasil Bulan Ini</p><h2>RM 18,920.00</h2><small class="up">↑ 5.2% <span>berbanding bulan lalu</span></small></article>
        </section>
        <section class="content-grid"><article class="panel chart-panel"><div class="panel-title"><div><h3>Aliran Kewangan</h3><p>Simpanan dan pengeluaran untuk 6 bulan terakhir</p></div><a href="/laporan" class="select-like">6 Bulan Terakhir⌄</a></div><div class="chart"><div class="chart-y"><span>RM 50k</span><span>RM 37.5k</span><span>RM 25k</span><span>RM 12.5k</span><span>RM 0</span></div><div class="plot"><div class="gridline l1"></div><div class="gridline l2"></div><div class="gridline l3"></div><div class="gridline l4"></div><svg viewBox="0 0 610 185" preserveAspectRatio="none"><path class="area" d="M0,138 C45,123 52,110 100,119 S165,72 205,87 S275,101 310,71 S365,80 405,48 S475,82 510,45 S565,42 610,20 L610,185 L0,185Z"/><path class="line" d="M0,138 C45,123 52,110 100,119 S165,72 205,87 S275,101 310,71 S365,80 405,48 S475,82 510,45 S565,42 610,20"/></svg><div class="chart-months"><span>Apr</span><span>Mei</span><span>Jun</span><span>Jul</span><span>Ogo</span><span>Sep</span></div></div></div><div class="legend"><span><i class="green-dot"></i>Simpanan</span><span><i class="gray-dot"></i>Pengeluaran</span></div></article>
        <article class="panel activity"><div class="panel-title"><div><h3>Aktiviti Terkini</h3><p>Transaksi terkini dalam koperasi</p></div><a href="/transaksi" class="text-link">Lihat Semua →</a></div><div class="activity-list"><div class="activity-row"><span class="round green">↓</span><div><b>Simpanan Bulanan</b><p>Ahmad Firdaus · Hari ini, 10:42 AM</p></div><strong class="income">+ RM 250.00</strong></div><div class="activity-row"><span class="round blue">♙</span><div><b>Ahli Baharu Berdaftar</b><p>Siti Nurhaliza · Hari ini, 9:15 AM</p></div><strong class="plain">#A-1249</strong></div><div class="activity-row"><span class="round amber">↗</span><div><b>Pengeluaran Simpanan</b><p>Mohd Rizal · Semalam, 4:30 PM</p></div><strong class="outcome">− RM 500.00</strong></div><div class="activity-row"><span class="round purple">◫</span><div><b>Pembiayaan Diluluskan</b><p>Nur Amira · Semalam, 2:18 PM</p></div><strong class="plain">RM 5,000.00</strong></div></div></article></section>
        <section class="panel table-panel"><div class="panel-title"><div><h3>Permohonan Menunggu Kelulusan</h3><p>Permohonan yang memerlukan tindakan anda</p></div><a href="/pembiayaan" class="text-link">Lihat Semua →</a></div><div class="table-wrap"><table><thead><tr><th>PEMOHON</th><th>JENIS PERMOHONAN</th><th>JUMLAH</th><th>TARIKH MOHON</th><th>STATUS</th><th></th></tr></thead><tbody><tr><td><span class="table-person">AF</span> Ahmad Firdaus</td><td>Pembiayaan Peribadi</td><td>RM 10,000.00</td><td>08 Sep 2026</td><td><span class="badge review">Dalam Semakan</span></td><td><a href="/pembiayaan" class="more">•••</a></td></tr><tr><td><span class="table-person blue">SN</span> Siti Nurhaliza</td><td>Pembiayaan Pendidikan</td><td>RM 8,500.00</td><td>07 Sep 2026</td><td><span class="badge pending">Menunggu Dokumen</span></td><td><a href="/pembiayaan" class="more">•••</a></td></tr><tr><td><span class="table-person violet">MA</span> Mohd Arif</td><td>Pengeluaran Simpanan</td><td>RM 2,000.00</td><td>06 Sep 2026</td><td><span class="badge review">Dalam Semakan</span></td><td><a href="/simpanan" class="more">•••</a></td></tr></tbody></table></div></section>
        @elseif ($active === 'profil')
        <section class="page-heading"><div><p class="eyebrow">iCOOP / PROFIL SAYA</p><h1>Profil Saya</h1><p class="muted">Kemas kini maklumat akaun dan keselamatan anda.</p></div></section>
        <section class="profile-layout">
            <article class="panel profile-card"><div class="profile-hero"><span>{{ strtoupper(substr($profileUser->name, 0, 1)) }}</span><div><h2>{{ $profileUser->name }}</h2><p>Administrator iCOOP</p></div></div><dl><div><dt>E-mel</dt><dd>{{ $profileUser->email }}</dd></div><div><dt>Peranan</dt><dd><span class="badge role">Administrator</span></dd></div></dl></article>
            <div class="profile-forms">
                <article class="panel settings-form"><div class="panel-title"><div><h3>Maklumat Peribadi</h3><p>Tukar nama dan alamat e-mel untuk profil anda.</p></div></div>
                    @if (session('profile_status'))<p class="form-success">✓ {{ session('profile_status') }}</p>@endif
                    <form method="POST" action="{{ route('profile.update') }}">@csrf @method('PUT')
                        <div class="field-grid"><label>Nama Penuh<input name="name" value="{{ old('name', $profileUser->name) }}" required></label><label>Alamat E-mel<input type="email" name="email" value="{{ old('email', $profileUser->email) }}" required></label></div>
                        @if ($errors->any())<p class="form-error">{{ $errors->first() }}</p>@endif
                        <button class="button primary" type="submit">Simpan Perubahan</button>
                    </form>
                </article>
                <article class="panel settings-form"><div class="panel-title"><div><h3>Tukar Kata Laluan</h3><p>Gunakan kata laluan yang sukar diteka untuk melindungi akaun anda.</p></div></div>
                    @if (session('password_status'))<p class="form-success">✓ {{ session('password_status') }}</p>@endif
                    <form method="POST" action="{{ route('profile.password') }}">@csrf @method('PUT')
                        <div class="field-grid single"><label>Kata Laluan Semasa<input type="password" name="current_password" required autocomplete="current-password"></label><label>Kata Laluan Baharu<input type="password" name="password" required autocomplete="new-password"><small>Minimum 8 aksara.</small></label><label>Sahkan Kata Laluan Baharu<input type="password" name="password_confirmation" required autocomplete="new-password"></label></div>
                        @if ($errors->passwordUpdate->any())<p class="form-error">{{ $errors->passwordUpdate->first() }}</p>@endif
                        <button class="button primary" type="submit">Tukar Kata Laluan</button>
                    </form>
                </article>
            </div>
        </section>
        @else
        @php $copy=['ahli'=>['Ahli Koperasi','Urus maklumat dan keahlian semua anggota koperasi.','Daftar Ahli Baharu'],'simpanan'=>['Simpanan','Pantau baki, caruman dan permohonan pengeluaran simpanan.','Rekod Simpanan'],'pembiayaan'=>['Pembiayaan','Semak permohonan dan status pembiayaan anggota.','Permohonan Baharu'],'transaksi'=>['Transaksi','Rekod semua aliran wang dan transaksi koperasi.','Transaksi Baharu'],'laporan'=>['Laporan','Akses ringkasan kewangan dan prestasi koperasi.','Jana Laporan'],'tetapan'=>['Tetapan','Konfigurasi maklumat koperasi dan pilihan sistem.','Kemaskini Tetapan']]; $detail=$copy[$active]??['Profil Saya','Urus maklumat akaun dan akses anda.','Kemaskini Profil']; @endphp
        <section class="page-heading"><div><p class="eyebrow">iCOOP / {{ strtoupper($detail[0]) }}</p><h1>{{ $detail[0] }}</h1><p class="muted">{{ $detail[1] }}</p></div><a href="/dashboard" class="button primary">＋ {{ $detail[2] }}</a></section><section class="panel empty-page"><div class="empty-icon">{{ $pages[$active][1] ?? '♙' }}</div><h2>Ruang {{ $detail[0] }}</h2><p>Ini ialah paparan asas yang sedia untuk disambungkan kepada fungsi sistem anda kemudian.</p><a href="/dashboard" class="button secondary">← Kembali ke Papan Pemuka</a></section>
        @endif
    </main>
</div>
<script>
    const themeToggle = document.getElementById('theme-mode');
    themeToggle.checked = localStorage.getItem('icoop-theme') === 'dark';
    themeToggle.addEventListener('change', () => {
        localStorage.setItem('icoop-theme', themeToggle.checked ? 'dark' : 'light');
    });
</script>
</body>
</html>
