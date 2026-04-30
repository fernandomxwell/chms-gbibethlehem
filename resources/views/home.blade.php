@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h1 class="mb-1">@lang('home.index')</h1>
    <p class="text-muted">Selamat datang di sistem manajemen gereja. Halaman ini menjelaskan setiap modul yang tersedia beserta cara penggunaannya.</p>
</div>

{{-- Alur Kerja --}}
<div class="card border-primary mb-4">
    <div class="card-header bg-primary text-white fw-bold">Alur Kerja yang Disarankan</div>
    <div class="card-body">
        <p class="mb-3">Untuk menggunakan sistem ini dari awal, ikuti urutan berikut agar semua modul dapat berfungsi dengan benar:</p>
        <div class="row g-2">
            @foreach([
                ['step' => '1', 'label' => 'Buat Aktivitas', 'desc' => 'Daftarkan kegiatan gereja beserta jadwal pengulangannya.'],
                ['step' => '2', 'label' => 'Buat Jenis Pelayanan', 'desc' => 'Tentukan posisi/peran yang ada dalam setiap aktivitas.'],
                ['step' => '3', 'label' => 'Daftarkan Jemaat', 'desc' => 'Masukkan data anggota dan simpatisan gereja.'],
                ['step' => '4', 'label' => 'Atur Pelayanan Jemaat', 'desc' => 'Tentukan siapa saja yang dapat melayani di posisi apa.'],
                ['step' => '5', 'label' => 'Buat Jadwal', 'desc' => 'Generate jadwal pelayanan secara otomatis.'],
            ] as $item)
            <div class="col-12 col-md">
                <div class="border rounded p-3 h-100 text-center">
                    <div class="fs-3 fw-bold text-primary mb-1">{{ $item['step'] }}</div>
                    <div class="fw-semibold mb-1">{{ $item['label'] }}</div>
                    <div class="text-muted small">{{ $item['desc'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Modul: Aktivitas --}}
<div class="card mb-4" id="modul-aktivitas">
    <div class="card-header fw-bold bg-light">Modul 1 &mdash; Aktivitas</div>
    <div class="card-body">
        <h6 class="fw-bold">Apa itu Aktivitas?</h6>
        <p>Aktivitas adalah kegiatan rutin gereja yang membutuhkan tim pelayan, misalnya <em>Ibadah Minggu Pagi</em>, <em>Ibadah Pemuda</em>, atau <em>Ibadah Hari Raya</em>. Modul ini menjadi fondasi utama sistem karena jadwal dan jenis pelayanan semuanya terkait ke sebuah aktivitas.</p>

        <h6 class="fw-bold mt-3">Informasi yang Dicatat</h6>
        <ul>
            <li><strong>Nama</strong> &mdash; nama kegiatan, misalnya "Ibadah Minggu".</li>
            <li><strong>Deskripsi</strong> &mdash; keterangan tambahan tentang kegiatan (opsional).</li>
            <li><strong>Aturan Pengulangan</strong> &mdash; seberapa sering kegiatan ini berlangsung. Tersedia pilihan: tidak berulang, harian, mingguan, bulanan, atau tahunan. Untuk mingguan, Anda bisa memilih hari spesifik (misalnya setiap Minggu).</li>
        </ul>

        <h6 class="fw-bold mt-3">Cara Menggunakan</h6>
        <ol>
            <li>Buka menu <strong>Aktivitas</strong> di sidebar.</li>
            <li>Klik <strong>Buat Aktivitas</strong> untuk menambahkan kegiatan baru.</li>
            <li>Isi nama, deskripsi, dan atur pengulangan sesuai jadwal kegiatan.</li>
            <li>Klik <strong>Kirim</strong> untuk menyimpan.</li>
        </ol>

        <h6 class="fw-bold mt-3">Fitur Tersedia</h6>
        <div class="row g-2">
            <div class="col-md-3"><div class="border rounded p-2 small"><strong>Lihat</strong><br>Tampilkan detail dan ringkasan aturan pengulangan.</div></div>
            <div class="col-md-3"><div class="border rounded p-2 small"><strong>Ubah</strong><br>Edit nama, deskripsi, atau jadwal pengulangan.</div></div>
            <div class="col-md-3"><div class="border rounded p-2 small"><strong>Hapus</strong><br>Hapus satu aktivitas. Tidak dapat dibatalkan.</div></div>
            <div class="col-md-3"><div class="border rounded p-2 small"><strong>Hapus Terpilih</strong><br>Hapus beberapa aktivitas sekaligus dengan centang.</div></div>
        </div>

        <div class="alert alert-warning mt-3 mb-0 small">
            <strong>Perhatian:</strong> Menghapus aktivitas yang sudah memiliki jadwal terkait dapat memengaruhi data jadwal yang ada. Pastikan tidak ada jadwal aktif sebelum menghapus aktivitas.
        </div>
    </div>
</div>

{{-- Modul: Jenis Pelayanan --}}
<div class="card mb-4" id="modul-jenis-pelayanan">
    <div class="card-header fw-bold bg-light">Modul 2 &mdash; Jenis Pelayanan</div>
    <div class="card-body">
        <h6 class="fw-bold">Apa itu Jenis Pelayanan?</h6>
        <p>Jenis Pelayanan adalah posisi atau peran yang dibutuhkan dalam sebuah aktivitas, misalnya <em>Worship Leader</em>, <em>Pianist</em>, <em>Singer</em>, atau <em>Pembaca Alkitab</em>. Setiap jenis pelayanan dikaitkan dengan satu atau lebih aktivitas, sehingga sistem tahu posisi apa yang perlu diisi saat membuat jadwal.</p>

        <h6 class="fw-bold mt-3">Informasi yang Dicatat</h6>
        <ul>
            <li><strong>Nama</strong> &mdash; nama posisi pelayanan.</li>
            <li><strong>Deskripsi</strong> &mdash; penjelasan singkat tentang peran ini (opsional).</li>
            <li><strong>Aktivitas</strong> &mdash; aktivitas mana saja yang membutuhkan peran ini. Satu jenis pelayanan bisa dikaitkan ke beberapa aktivitas.</li>
        </ul>

        <h6 class="fw-bold mt-3">Cara Menggunakan</h6>
        <ol>
            <li>Buka menu <strong>Jenis Pelayanan</strong> di sidebar.</li>
            <li>Klik <strong>Buat Jenis Pelayanan</strong>.</li>
            <li>Isi nama, deskripsi, dan pilih aktivitas yang relevan.</li>
            <li>Klik <strong>Kirim</strong> untuk menyimpan.</li>
        </ol>

        <h6 class="fw-bold mt-3">Fitur Tersedia</h6>
        <div class="row g-2">
            <div class="col-md-3"><div class="border rounded p-2 small"><strong>Lihat</strong><br>Tampilkan detail beserta daftar aktivitas terkait.</div></div>
            <div class="col-md-3"><div class="border rounded p-2 small"><strong>Ubah</strong><br>Edit nama, deskripsi, atau aktivitas yang terkait.</div></div>
            <div class="col-md-3"><div class="border rounded p-2 small"><strong>Hapus / Hapus Terpilih</strong><br>Hapus satu atau beberapa sekaligus.</div></div>
            <div class="col-md-3"><div class="border rounded p-2 small"><strong>Unduh / Unggah CSV</strong><br>Export seluruh data atau import dari file CSV.</div></div>
        </div>

        <h6 class="fw-bold mt-3">Import CSV</h6>
        <p class="mb-1 small">File CSV harus memiliki tiga kolom berikut (baris pertama adalah header):</p>
        <table class="table table-sm table-bordered small mb-2">
            <thead class="table-light"><tr><th>Kolom</th><th>Wajib?</th><th>Keterangan</th></tr></thead>
            <tbody>
                <tr><td><code>name</code></td><td>Ya</td><td>Nama jenis pelayanan, maks. 100 karakter.</td></tr>
                <tr><td><code>description</code></td><td>Tidak</td><td>Deskripsi singkat.</td></tr>
                <tr><td><code>activities</code></td><td>Tidak</td><td>Nama aktivitas dipisahkan tanda titik koma (<code>;</code>). Nama harus sama persis dengan yang ada di sistem.</td></tr>
            </tbody>
        </table>
        <p class="small text-muted mb-0">Jika nama jenis pelayanan sudah ada di sistem, data yang ada akan <strong>diperbarui</strong> (bukan digandakan). Gunakan tombol <strong>Unduh Template CSV</strong> untuk mendapatkan contoh format yang benar.</p>
    </div>
</div>

{{-- Modul: Jemaat --}}
<div class="card mb-4" id="modul-jemaat">
    <div class="card-header fw-bold bg-light">Modul 3 &mdash; Jemaat</div>
    <div class="card-body">
        <h6 class="fw-bold">Apa itu Jemaat?</h6>
        <p>Modul Jemaat adalah database seluruh anggota dan simpatisan gereja. Data jemaat digunakan oleh modul Pelayanan Jemaat untuk menentukan siapa yang dapat dilibatkan dalam jadwal pelayanan.</p>

        <h6 class="fw-bold mt-3">Informasi yang Dicatat</h6>
        <ul>
            <li><strong>Gelar</strong> &mdash; Bpk., Ibu, Sdr., atau Sdri. (opsional).</li>
            <li><strong>Nama Lengkap</strong> &mdash; wajib diisi.</li>
            <li><strong>Jenis Kelamin</strong> &mdash; Pria atau Wanita.</li>
            <li><strong>Tanggal Lahir</strong> &mdash; format YYYY-MM-DD (opsional).</li>
            <li><strong>Nomor Telepon</strong> &mdash; nomor HP Indonesia, akan dinormalisasi otomatis ke format internasional (opsional).</li>
            <li><strong>Email</strong> &mdash; opsional.</li>
            <li><strong>Tanggal Baptis</strong> &mdash; opsional.</li>
            <li><strong>Status</strong> &mdash; Anggota atau Simpatisan.</li>
        </ul>

        <h6 class="fw-bold mt-3">Cara Menggunakan</h6>
        <ol>
            <li>Buka menu <strong>Jemaat</strong> di sidebar.</li>
            <li>Gunakan tombol <strong>Buat Jemaat</strong> untuk menambah satu per satu, atau <strong>Unggah CSV</strong> untuk memasukkan banyak data sekaligus.</li>
            <li>Untuk mencari data, gunakan kotak pencarian atau filter berdasarkan status dan jenis kelamin.</li>
        </ol>

        <h6 class="fw-bold mt-3">Fitur Tersedia</h6>
        <div class="row g-2">
            <div class="col-md-3"><div class="border rounded p-2 small"><strong>Lihat</strong><br>Tampilkan detail lengkap jemaat.</div></div>
            <div class="col-md-3"><div class="border rounded p-2 small"><strong>Ubah</strong><br>Edit informasi pribadi jemaat.</div></div>
            <div class="col-md-3"><div class="border rounded p-2 small"><strong>Hapus / Hapus Terpilih</strong><br>Hapus satu atau beberapa sekaligus.</div></div>
            <div class="col-md-3"><div class="border rounded p-2 small"><strong>Unduh / Unggah CSV</strong><br>Export seluruh data atau import massal dari file CSV.</div></div>
        </div>

        <h6 class="fw-bold mt-3">Import CSV</h6>
        <p class="mb-1 small">File CSV harus memiliki delapan kolom berikut (baris pertama adalah header):</p>
        <table class="table table-sm table-bordered small mb-2">
            <thead class="table-light"><tr><th>Kolom</th><th>Wajib?</th><th>Nilai yang Diterima</th></tr></thead>
            <tbody>
                <tr><td><code>honorific_title</code></td><td>Tidak</td><td><code>bpk</code> / <code>ibu</code> / <code>sdr</code> / <code>sdri</code></td></tr>
                <tr><td><code>full_name</code></td><td>Ya</td><td>Teks bebas.</td></tr>
                <tr><td><code>gender</code></td><td>Ya</td><td><code>male</code> atau <code>female</code></td></tr>
                <tr><td><code>date_of_birth</code></td><td>Tidak</td><td>Format <code>YYYY-MM-DD</code>, misalnya <code>1990-01-15</code></td></tr>
                <tr><td><code>phone_number</code></td><td>Tidak</td><td>Nomor HP Indonesia, misalnya <code>08123456789</code></td></tr>
                <tr><td><code>email</code></td><td>Tidak</td><td>Alamat email valid.</td></tr>
                <tr><td><code>date_of_baptism</code></td><td>Tidak</td><td>Format <code>YYYY-MM-DD</code></td></tr>
                <tr><td><code>status</code></td><td>Ya</td><td><code>member</code> atau <code>sympathizer</code></td></tr>
            </tbody>
        </table>
        <p class="small text-muted mb-0">Gunakan tombol <strong>Unduh Template CSV</strong> untuk mendapatkan contoh format yang benar beserta satu baris contoh data.</p>
    </div>
</div>

{{-- Modul: Pelayanan Jemaat --}}
<div class="card mb-4" id="modul-pelayanan-jemaat">
    <div class="card-header fw-bold bg-light">Modul 4 &mdash; Pelayanan Jemaat</div>
    <div class="card-body">
        <h6 class="fw-bold">Apa itu Pelayanan Jemaat?</h6>
        <p>Modul ini adalah jembatan antara <strong>Jemaat</strong>, <strong>Aktivitas</strong>, dan <strong>Jenis Pelayanan</strong>. Di sinilah Anda mendaftarkan siapa saja yang boleh melayani di posisi tertentu dalam suatu aktivitas. Data inilah yang digunakan sistem untuk memilih pelayan secara otomatis saat jadwal dibuat.</p>

        <h6 class="fw-bold mt-3">Contoh Skenario</h6>
        <p class="small">Budi Santoso didaftarkan sebagai <em>Worship Leader</em> dan <em>Pianist</em> untuk aktivitas <em>Ibadah Minggu</em>. Artinya, saat jadwal Ibadah Minggu dibuat, sistem berpotensi memilih Budi untuk salah satu dari dua posisi tersebut.</p>

        <h6 class="fw-bold mt-3">Cara Menggunakan</h6>
        <ol>
            <li>Buka menu <strong>Pelayanan Jemaat</strong> di sidebar.</li>
            <li>Klik <strong>Buat Pelayanan Jemaat</strong>.</li>
            <li>Pilih nama jemaat dari dropdown (bisa dicari).</li>
            <li>Pada bagian <strong>Jenis Pelayanan</strong>, centang posisi yang dapat diemban oleh jemaat tersebut. Jenis pelayanan sudah dikelompokkan per aktivitas. Gunakan dropdown filter di atas tabel untuk mempersempit tampilan berdasarkan aktivitas tertentu.</li>
            <li>Centang <strong>Bersedia melayani beberapa minggu berturut-turut</strong> jika jemaat ini boleh dijadwalkan bahkan saat sudah melayani minggu sebelumnya (sebagai cadangan).</li>
            <li>Klik <strong>Kirim</strong> untuk menyimpan.</li>
        </ol>

        <h6 class="fw-bold mt-3">Opsi "Bersedia Melayani Berturut-turut"</h6>
        <p class="small">Saat jadwal dibuat, sistem mengutamakan jemaat yang belum melayani pada periode terdekat. Jika jumlah kandidat kurang dari yang dibutuhkan, sistem akan mengambil dari jemaat yang mencentang opsi ini sebagai pelengkap. Artinya, opsi ini adalah "pelayan cadangan" — aktif hanya jika jemaat lain tidak cukup.</p>

        <h6 class="fw-bold mt-3">Fitur Tersedia</h6>
        <div class="row g-2">
            <div class="col-md-3"><div class="border rounded p-2 small"><strong>Ubah</strong><br>Perbarui daftar posisi pelayanan jemaat.</div></div>
            <div class="col-md-3"><div class="border rounded p-2 small"><strong>Hapus / Hapus Terpilih</strong><br>Hapus seluruh penugasan pelayanan untuk satu atau beberapa jemaat.</div></div>
            <div class="col-md-3"><div class="border rounded p-2 small"><strong>Unduh CSV</strong><br>Export seluruh data pelayanan jemaat.</div></div>
            <div class="col-md-3"><div class="border rounded p-2 small"><strong>Unggah CSV</strong><br>Import penugasan pelayanan secara massal.</div></div>
        </div>

        <h6 class="fw-bold mt-3">Import CSV</h6>
        <p class="mb-1 small">Karena satu jemaat bisa memiliki beberapa penugasan, format CSV menggunakan <strong>satu baris per penugasan</strong>. Jemaat yang sama cukup ditulis ulang di baris berikutnya dengan aktivitas/posisi yang berbeda.</p>
        <table class="table table-sm table-bordered small mb-2">
            <thead class="table-light"><tr><th>Kolom</th><th>Wajib?</th><th>Keterangan</th></tr></thead>
            <tbody>
                <tr><td><code>full_name</code></td><td>Ya</td><td>Nama lengkap jemaat, harus sama persis dengan yang terdaftar di sistem.</td></tr>
                <tr><td><code>can_serve_consecutively</code></td><td>Ya</td><td><code>1</code> = bersedia berturut-turut, <code>0</code> = tidak.</td></tr>
                <tr><td><code>activity</code></td><td>Ya</td><td>Nama aktivitas, harus sama persis dengan yang ada di sistem.</td></tr>
                <tr><td><code>service_type</code></td><td>Ya</td><td>Nama jenis pelayanan, harus sama persis dengan yang ada di sistem.</td></tr>
            </tbody>
        </table>
        <div class="bg-light border rounded p-2 small mb-0">
            <strong>Contoh:</strong><br>
            <code>Budi Santoso,1,Ibadah Minggu,Worship Leader</code><br>
            <code>Budi Santoso,1,Ibadah Minggu,Pianist</code><br>
            <code>Sari Dewi,0,Ibadah Pemuda,Singer</code><br>
            <span class="text-muted">Dua baris pertama mendaftarkan Budi untuk dua posisi berbeda dalam aktivitas yang sama.</span>
        </div>
        <p class="small text-muted mt-2 mb-0"><strong>Catatan:</strong> Import akan <strong>menggantikan</strong> seluruh penugasan yang ada untuk setiap jemaat yang tercantum dalam file. Penugasan lama tidak akan dipertahankan kecuali ikut ditulis ulang di file CSV.</p>
    </div>
</div>

{{-- Modul: Jadwal --}}
<div class="card mb-4" id="modul-jadwal">
    <div class="card-header fw-bold bg-light">Modul 5 &mdash; Jadwal (dalam Manajemen Jadwal)</div>
    <div class="card-body">
        <h6 class="fw-bold">Apa itu Jadwal?</h6>
        <p>Modul Jadwal menghasilkan jadwal pelayanan secara otomatis. Berdasarkan aktivitas, rentang tanggal, dan data pelayanan jemaat yang sudah diisi, sistem akan memilih pelayan untuk setiap posisi di setiap tanggal kegiatan — tanpa perlu menyusun manual satu per satu.</p>

        <h6 class="fw-bold mt-3">Cara Membuat Jadwal</h6>
        <ol>
            <li>Buka <strong>Manajemen Jadwal &rarr; Jadwal</strong> di sidebar.</li>
            <li>Klik <strong>Buat Jadwal</strong>.</li>
            <li>Pilih <strong>Aktivitas</strong> yang akan dijadwalkan. Tabel jenis pelayanan di bawah akan otomatis menyaring posisi yang relevan dengan aktivitas tersebut.</li>
            <li>Isi <strong>Tanggal Mulai</strong> dan <strong>Tanggal Selesai</strong> untuk menentukan periode jadwal.</li>
            <li>Pada tabel <strong>Jenis Pelayanan</strong>:
                <ul>
                    <li>Centang kolom <strong>Sertakan?</strong> untuk posisi yang ingin diisi dalam jadwal ini.</li>
                    <li>Isi kolom <strong>Jumlah</strong> dengan berapa orang yang dibutuhkan per posisi per tanggal.</li>
                    <li>Centang kolom <strong>Bisa Diulang?</strong> jika posisi ini boleh diisi oleh jemaat yang melayani minggu sebelumnya (sebagai cadangan).</li>
                </ul>
            </li>
            <li>Klik <strong>Hasilkan</strong> untuk membuat jadwal.</li>
        </ol>

        <h6 class="fw-bold mt-3">Cara Sistem Memilih Pelayan</h6>
        <p class="small">Untuk setiap posisi di setiap tanggal, sistem bekerja sebagai berikut:</p>
        <ol class="small">
            <li>Kumpulkan semua jemaat yang terdaftar untuk posisi tersebut di aktivitas yang bersangkutan.</li>
            <li>Saring: keluarkan jemaat yang sudah dijadwalkan di tanggal yang sama (tidak boleh dua posisi sekaligus).</li>
            <li>Utamakan jemaat yang belum melayani pada tanggal terdekat sebelumnya.</li>
            <li>Jika jumlah kandidat masih kurang dan posisi mencentang <em>Bisa Diulang?</em>, tambahkan jemaat yang mencentang opsi <em>bersedia berturut-turut</em> sebagai pelengkap.</li>
            <li>Pilih secara acak sejumlah yang dibutuhkan dari kandidat yang tersisa.</li>
        </ol>

        <h6 class="fw-bold mt-3">Melihat dan Mengunduh Jadwal</h6>
        <ol>
            <li>Dari daftar jadwal, klik <strong>Lihat</strong> pada jadwal yang ingin ditampilkan.</li>
            <li>Jadwal ditampilkan dalam format tabel: baris = tanggal, kolom = jenis pelayanan.</li>
            <li>Klik <strong>Unduh</strong> untuk mengunduh jadwal dalam format CSV, siap dibuka di Excel atau Google Sheets.</li>
        </ol>

        <h6 class="fw-bold mt-3">Fitur Tersedia</h6>
        <div class="row g-2">
            <div class="col-md-3"><div class="border rounded p-2 small"><strong>Lihat</strong><br>Tampilkan jadwal dalam format tabel per tanggal.</div></div>
            <div class="col-md-3"><div class="border rounded p-2 small"><strong>Unduh CSV</strong><br>Export jadwal ke file CSV.</div></div>
            <div class="col-md-3"><div class="border rounded p-2 small"><strong>Hapus</strong><br>Hapus satu periode jadwal beserta seluruh isinya.</div></div>
            <div class="col-md-3"><div class="border rounded p-2 small"><strong>Hapus Terpilih</strong><br>Hapus beberapa periode jadwal sekaligus.</div></div>
        </div>

        <div class="alert alert-info mt-3 mb-0 small">
            <strong>Tips:</strong> Pastikan data <strong>Pelayanan Jemaat</strong> sudah lengkap sebelum membuat jadwal. Semakin banyak jemaat yang terdaftar untuk suatu posisi, semakin merata distribusi jadwal pelayanan yang dihasilkan.
        </div>
    </div>
</div>

{{-- Pertanyaan Umum --}}
<div class="card mb-4">
    <div class="card-header fw-bold bg-light">Pertanyaan yang Sering Ditanyakan</div>
    <div class="card-body">
        <div class="mb-3">
            <p class="fw-semibold mb-1">Apa yang terjadi jika jumlah jemaat tidak mencukupi saat jadwal dibuat?</p>
            <p class="small text-muted mb-0">Sistem akan tetap mengambil sebanyak yang tersedia. Posisi yang tidak terpenuhi akan dikosongkan. Anda dapat menambahkan jemaat ke modul Pelayanan Jemaat lalu membuat ulang jadwal.</p>
        </div>
        <div class="mb-3">
            <p class="fw-semibold mb-1">Apakah jadwal yang sudah dibuat bisa diubah?</p>
            <p class="small text-muted mb-0">Saat ini jadwal tidak bisa diedit langsung. Jika ada perubahan, hapus jadwal yang ada lalu buat ulang.</p>
        </div>
        <div class="mb-3">
            <p class="fw-semibold mb-1">Apakah satu jemaat bisa dijadwalkan untuk dua posisi berbeda di tanggal yang sama?</p>
            <p class="small text-muted mb-0">Tidak. Sistem secara otomatis mencegah satu jemaat mengisi lebih dari satu posisi dalam tanggal yang sama.</p>
        </div>
        <div class="mb-3">
            <p class="fw-semibold mb-1">Apa perbedaan status "Anggota" dan "Simpatisan"?</p>
            <p class="small text-muted mb-0">Status ini hanya bersifat informatif untuk keperluan pencatatan jemaat. Keduanya bisa sama-sama didaftarkan ke Pelayanan Jemaat dan dijadwalkan.</p>
        </div>
        <div class="mb-0">
            <p class="fw-semibold mb-1">Bagaimana jika nama aktivitas atau jenis pelayanan saya ubah setelah data sudah diisi?</p>
            <p class="small text-muted mb-0">Data relasi (pelayanan jemaat, jadwal) menggunakan ID internal sehingga perubahan nama tidak memutus relasi yang sudah ada. Namun, file CSV yang diekspor sebelum perubahan nama perlu disesuaikan jika akan digunakan untuk import ulang.</p>
        </div>
    </div>
</div>
@endsection
