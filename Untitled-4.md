TAHAP 1: Persiapan Gudang (Google Drive)
Langkah ini untuk membuat folder penampung dan mengambil ID-nya. Gunakan Akun Gmail Khusus/Baru.

Buka Google Drive dan Login menggunakan akun Gmail yang akan dijadikan penyimpanan.

Buat folder baru (Misal: Data DMS Diskominfo).

Buka (masuk ke dalam) folder tersebut.

Perhatikan URL (link) di bagian atas browser.
Contoh: https://drive.google.com/drive/folders/1y1Ms6UylX6y0VRsy96RAgN1BSobFR1_g

Copy kode acak di belakang tulisan /folders/ tersebut.

Buka Notepad, simpan kode tersebut dengan nama Folder ID.

TAHAP 2: Pembuatan Identitas Aplikasi (Google Cloud Console)
Langkah ini untuk mendaftarkan aplikasi Laravel-mu agar dikenali oleh server Google.

Buka Google Cloud Console menggunakan akun Gmail yang sama.

Klik menu di kiri atas (di sebelah logo Google Cloud), lalu pilih New Project / Buat Project. Beri nama (misal: DMS-Diskominfo) dan klik Create.

Pastikan project tersebut sudah aktif (terpilih di menu atas).

Aktifkan API:

Di menu kiri, pilih APIs & Services > Library.

Ketik "Google Drive API" di kolom pencarian.

Klik hasilnya, lalu tekan tombol biru Enable (Aktifkan).

Atur Layar Persetujuan (OAuth Consent Screen):

Di menu kiri, pilih APIs & Services > OAuth consent screen.

Pilih External, lalu klik Create.

Isi App name (Misal: DMS App), User support email (email kamu), dan Developer contact information (email kamu). Sisanya abaikan.

Klik Save and Continue sampai kembali ke layar utama (Dashboard).

PENTING: Scroll ke bawah ke bagian Test Users. Klik + ADD USERS, lalu masukkan alamat email Gmail kamu tadi. (Jika terlewat, aplikasimu akan diblokir dengan error 401).

Buat KTP Aplikasi (Credentials):

Di menu kiri, pilih APIs & Services > Credentials.

Klik tombol + CREATE CREDENTIALS > OAuth client ID.

Application Type: Pilih Web application.

Name: Isi bebas (Misal: DMS Web).

Di bagian Authorized redirect URIs, klik ADD URI lalu masukkan tepat URL ini:
https://developers.google.com/oauthplayground

Klik Create.

Akan muncul pop-up. Copy nilai Client ID dan Client Secret. Simpan di Notepad tadi.

TAHAP 3: Pengambilan Tiket Permanen (OAuth 2.0 Playground)
Langkah ini untuk mendapatkan izin agar Laravel bisa upload tanpa perlu login manual setiap saat.

Buka tab baru, kunjungi: Google OAuth 2.0 Playground

Klik ikon Gir (Settings) di pojok kanan atas.

Centang opsi Use your own OAuth credentials.

Paste Client ID dan Client Secret dari Notepad-mu ke kotak yang tersedia. Tutup menu Settings.

Di panel sebelah kiri (Step 1):

Scroll ke bawah dan temukan Drive API v3. Klik agar daftar menunya terbuka.

Centang kotak yang tulisannya: https://www.googleapis.com/auth/drive

Klik tombol biru Authorize APIs.

Kamu akan dilempar ke halaman Login Google. Pilih akun Gmail kamu.

Jika muncul layar peringatan (Google hasn't verified this app):

Klik tulisan Advanced / Lanjutan di pojok kiri bawah.

Klik Lanjutkan ke DMS App (tidak aman).

Klik Continue / Izinkan agar aplikasi bisa mengakses Google Drive.

Kamu akan otomatis kembali ke halaman OAuth Playground.

Di panel kiri (Step 2), klik tombol biru Exchange authorization code for tokens.

Tunggu 1 detik. Di kotak sebelah kanan akan muncul respon dari Google. Cari baris tulisan "refresh_token": "...".

Copy kode refresh token tersebut dan simpan di Notepad. Ini adalah Kunci Master-nya.

TAHAP 4: Pemasangan di Mesin Utama (Laravel .env)
Tahap terakhir menyatukan semua kode yang ada di Notepad ke dalam aplikasi.

Buka proyek Laravel-mu di VS Code.

Buka file .env, lalu tambahkan/ubah konfigurasi berikut (tanpa ada spasi di belakang):

Cuplikan kode
GOOGLE_DRIVE_FOLDER_ID="isi_dengan_Folder_ID_dari_Tahap_1"
GOOGLE_CLIENT_ID="isi_dengan_Client_ID_dari_Tahap_2"
GOOGLE_CLIENT_SECRET="isi_dengan_Client_Secret_dari_Tahap_2"
GOOGLE_REFRESH_TOKEN="isi_dengan_Refresh_Token_dari_Tahap_3"
Buka Terminal VS Code. Hentikan server lokal (php artisan serve) dengan menekan Ctrl+C.

Wajib jalankan perintah ini agar Laravel mereset ingatannya dan membaca pengaturan baru:

Bash
php artisan config:clear
php artisan cache:clear
php artisan optimize:clear
Nyalakan lagi php artisan serve.

Selesai! Aplikasi siap digunakan.
