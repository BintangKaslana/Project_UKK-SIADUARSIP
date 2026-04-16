#  SIADUARSIP
### Sistem Aduan Aspirasi Pelajar

> Proyek akhir (UKK) — SMK Jurusan Rekayasa Perangkat Lunak  
> *my code, don't fail me now. UwU*

---

##  Tentang Proyek

**SIADUARSIP** adalah aplikasi web berbasis PHP yang memungkinkan siswa menyampaikan aspirasi dan pengaduan terkait **sarana dan prasarana sekolah** secara mudah dan transparan. Admin dapat mereview, menanggapi, dan memperbarui status setiap pengaduan.

---

##  Fitur

###  Sisi Siswa (Publik)

| Fitur | Deskripsi |
|-------|-----------|
| **Form Pengaduan** | Siswa mengisi NIS, nama, kelas, kategori, lokasi, dan deskripsi pengaduan |
| **Upload Bukti Foto** | Lampirkan foto bukti (JPG/PNG/WEBP, maks 2MB) |
| **Kirim Anonim** | Pilihan mengirim pengaduan tanpa nama tampil di histori publik (data tetap tersimpan untuk verifikasi admin) |
| **Batas Spam** | Maksimal 3 pengaduan per NIS per hari untuk mencegah penyalahgunaan |
| **Histori Aspirasi** | Cari riwayat pengaduan berdasarkan NIS — termasuk status & feedback dari admin |
| **Pagination** | Histori ditampilkan per halaman (5 per halaman) |
| **Modal Detail** | Lihat deskripsi lengkap & feedback admin tanpa pindah halaman |

---

###  Sisi Admin (Panel Admin)

####  Autentikasi
- Login & logout admin dengan session
- Sistem role: **Head Admin** dan **Admin** biasa

####  Dashboard Aspirasi
- Tampilkan seluruh aspirasi masuk dalam tabel lengkap
- **Filter** berdasarkan: Kategori, NIS, Status, Bulan, Tanggal
- Badge notifikasi jumlah aspirasi yang belum direview
- Edit status & feedback aspirasi
- Hapus aspirasi *(khusus Head Admin)*
- Pagination (10 per halaman)
- Modal detail aspirasi langsung dari tabel

####  Review Aspirasi
- Aspirasi baru masuk dengan status `pending` dan harus disetujui sebelum tampil ke publik
- Admin bisa **Setujui** atau **Tolak** setiap pengaduan
- Pagination antrian review (5 per halaman)

####  Kelola Admin *(khusus Head Admin)*
- Lihat daftar semua akun admin
- Tambah, edit, dan hapus akun admin
- Proteksi: tidak bisa hapus diri sendiri atau Head Admin terakhir

####  Kelola Kategori *(khusus Head Admin)*
- Tambah, edit, dan hapus kategori pengaduan
- Kategori digunakan di form pengaduan siswa

#### 👤 Profil Admin
- Lihat & update profil (nama lengkap)
- Ganti password

---

##  Struktur Direktori

```
siaduarsip/
├── app/
│   ├── bootstrap.php        # Inisialisasi koneksi & konstanta
│   ├── routes.web.php       # Routing halaman publik
│   └── routes.admin.php     # Routing panel admin
├── public/
│   └── uploads/bukti/       # Penyimpanan foto bukti pengaduan
├── views/
│   ├── pages/               # Halaman publik (beranda, aspirasi, histori)
│   ├── admin/               # Halaman panel admin
│   ├── layouts/             # Template layout
│   └── partials/            # Komponen reusable (navbar, dll)
├── create_database.php      # Script pembuatan database
├── migrate.php              # Script migrasi tabel
├── seeder.php               # Script seeder data awal
└── .htaccess                # Konfigurasi URL routing
```

---

##  Teknologi

- **Backend:** PHP (native, tanpa framework)
- **Database:** PostgreSQL (via PDO)
- **Frontend:** HTML, Tailwind CSS (CLI)
- **Server:** Apache / Laragon
- **Package Manager:** npm (untuk dependensi frontend)

---

##  Instalasi & Setup

### Prasyarat
- PHP >= 8.0
- PostgreSQL
- Apache / Laragon
- Composer *(opsional)*



---

## 👤 Author

**Bintang** — SMK Rekayasa Perangkat Lunak  
Proyek UKK (Uji Kompetensi Keahlian) — 2026
