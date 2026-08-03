# 🦷 Sistem Informasi Reservasi dan Rekam Medis

Sistem Informasi Reservasi dan Rekam Medis berbasis web yang dikembangkan sebagai **Tugas Akhir Diploma III Teknik Informatika**. Sistem ini dirancang untuk membantu proses reservasi pasien, pengelolaan antrean, jadwal dokter, serta pencatatan rekam medis secara digital pada **Klinik Gigi Dokter Ndeso**.

---

## ✨ Fitur Utama

- 🔐 Login Multi Role (Admin, Dokter, Pasien)
- 📅 Reservasi Online
- ⏰ Manajemen Jadwal Dokter
- 🎫 Sistem Antrean Otomatis
- 🦷 Pengelolaan Rekam Medis
- 📖 Artikel Edukasi Kesehatan Gigi
- 🔔 Pengingat Reservasi
- 📊 Dashboard Admin & Dokter
- 👤 Manajemen Data Pasien
- 📍 Multi Klinik

---

## 🛠️ Tech Stack

| Technology | Description |
|------------|-------------|
| Laravel | Backend Framework |
| PHP | Programming Language |
| MySQL | Database |
| Bootstrap | Frontend Framework |
| JavaScript | Interactive Components |
| HTML5 | Markup Language |
| CSS3 | Styling |

---

## 👥 User Roles

### 👨‍💼 Admin
- Mengelola data pasien
- Mengelola data dokter
- Mengatur jadwal dokter
- Mengelola reservasi
- Melihat dashboard
- Mengelola artikel

### 👨‍⚕️ Dokter
- Melihat jadwal praktik
- Mengelola rekam medis pasien
- Melihat riwayat pasien

### 👤 Pasien
- Registrasi akun
- Melakukan reservasi online
- Melihat nomor antrean
- Mengakses riwayat rekam medis
- Membaca artikel kesehatan

---

## 📸 Preview

### Login
> *Tambahkan screenshot halaman login di sini.*

### Dashboard Admin
> *Tambahkan screenshot dashboard admin.*

### Reservasi Online
> *Tambahkan screenshot halaman reservasi.*

### Rekam Medis
> *Tambahkan screenshot halaman rekam medis.*

---

## 🚀 Instalasi

Clone repository

```bash
git clone https://github.com/andinardelinaa/sistem-informasi-reservasi-rekam-medis.git
```

Masuk ke folder project

```bash
cd sistem-informasi-reservasi-rekam-medis
```

Install dependency

```bash
composer install
```

Copy file environment

```bash
cp .env.example .env
```

Generate application key

```bash
php artisan key:generate
```

Konfigurasi database pada file `.env`

Jalankan migration

```bash
php artisan migrate
```

Jalankan server

```bash
php artisan serve
```

---

## 📂 Struktur Project

```text
app/
bootstrap/
config/
database/
public/
resources/
routes/
storage/
```

---

## 🎯 Tujuan Project

Membangun sistem informasi yang mampu membantu proses administrasi klinik gigi menjadi lebih efektif melalui digitalisasi proses reservasi, pengelolaan antrean, jadwal dokter, dan rekam medis pasien.

---

## 👩‍💻 Pengembang

**Andin Ardelina Saputri**

Fresh Graduate Diploma III Teknik Informatika

- GitHub : https://github.com/andinardelinaa
- LinkedIn : https://www.linkedin.com/in/andinardelinasaputri

---

## ⭐ Dukungan

Jika repository ini bermanfaat, jangan lupa berikan ⭐ pada repository ini.
