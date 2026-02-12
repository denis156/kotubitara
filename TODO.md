# TODO - Sistem Informasi Kependudukan Desa (Kotubitara)

## ✅ Data Master (Selesai)
1. ✅ **Desa** (soft delete)
   - CRUD desa dengan wilayah administrasi
   - Observer auto-fill kode wilayah
   - Policy: Petugas kecamatan full access, petugas desa read-only desa mereka

2. ✅ **User/Pengguna** (soft delete)
   - Role: Petugas Kecamatan & Petugas Desa
   - Avatar upload dengan auto-delete foto lama (Observer)
   - Relasi many-to-many dengan Desa
   - Policy: Hanya petugas kecamatan bisa akses resource User

---

## ✅ Data Kependudukan (Selesai)

### A. Penduduk
- ✅ Model Penduduk (soft delete)
  - `nik` (string, unique, 16 digit)
  - `kartu_keluarga_id` (foreignId -> kartu_keluargas, nullable)
  - `desa_id` (foreignId -> desas)
  - `nama_lengkap` (string)
  - `jenis_kelamin` (enum: laki-laki, perempuan)
  - `tempat_lahir` (string)
  - `tanggal_lahir` (date)
  - `agama` (enum: islam, kristen, katolik, hindu, buddha, konghucu)
  - `status_perkawinan` (enum: belum-kawin, kawin, cerai-hidup, cerai-mati)
  - `hubungan_keluarga` (enum: kepala-keluarga, suami, istri, anak, menantu, cucu, orang-tua, mertua, **saudara-kandung**, famili-lain, pembantu, lainnya)
  - `pekerjaan` (string, nullable)
  - `pendidikan` (string, nullable)
  - `nama_ayah` (string, nullable)
  - `nama_ibu` (string, nullable)
  - `kewarganegaraan` (enum: wni, wna, default: wni)
  - timestamps, soft deletes
- ✅ Enums: JenisKelamin, Agama, StatusPerkawinan, HubunganKeluarga, Kewarganegaraan
- ✅ Resource Penduduk (Filament)
  - Form: lengkap dengan enums dan validation
  - Table: searchable (NIK, nama), filter by desa, jenis kelamin, agama
  - **Filter query scope untuk petugas desa** (hanya lihat penduduk di desa mereka)
- ✅ Policy Penduduk
  - Petugas kecamatan: full access
  - Petugas desa: hanya penduduk di desa mereka
- ✅ Factory Penduduk (untuk testing)

### B. Kartu Keluarga (KK)
- ✅ Model KK (soft delete)
  - `no_kk` (string, unique, 16 digit)
  - `kepala_keluarga_id` (foreignId -> penduduks)
  - `desa_id` (foreignId -> desas)
  - `alamat` (text, nullable)
  - `rt` (string, nullable)
  - `rw` (string, nullable)
  - timestamps, soft deletes
- ✅ Resource KK (Filament)
  - Form: Input no KK, pilih kepala keluarga (filter: hanya "Kepala Keluarga"), anggota keluarga (multi-select, exclude "Kepala Keluarga"), alamat lengkap (RT/RW)
  - Table: List KK dengan nama kepala keluarga, alamat, desa
  - **Filter query scope untuk petugas desa** (hanya lihat KK di desa mereka)
  - Helper text yang informatif di setiap field
  - Label anggota keluarga menampilkan hubungan keluarga
- ✅ Policy KK
  - Petugas kecamatan: full access
  - Petugas desa: hanya KK di desa mereka
- ✅ Observer KK
  - Auto-assign kepala keluarga ke KK saat created/updated/restored
  - Auto-clear kepala keluarga lama saat diganti
  - **Auto-clear semua anggota keluarga saat KK di-soft delete**
  - Auto-restore kepala keluarga saat KK di-restore
- ✅ Factory KK (untuk testing)


---

## 📊 Data Demografi (Events)

### C. Kelahiran
- ✅ Model Kelahiran (soft delete)
  - Data bayi (nama, NIK, jenis kelamin, tempat/tanggal/waktu lahir, berat/panjang)
  - `ayah_id` & `ibu_id` (foreignId -> penduduks)
  - `desa_id` (foreignId -> desas)
  - Data pelapor (nama, NIK, hubungan, alamat, telepon)
  - TTD digital pelapor (SignaturePad & file upload)
  - `kepala_desa_id` (foreignId -> aparat_desas)
  - `no_surat_kelahiran` (auto-generate: SP/LHR/YYYY/MM/XXXXX)
  - `tanggal_surat` (date)
  - Foto surat RS/Bidan (file upload)
- ✅ Resource Kelahiran (Filament)
  - Form: sections (Identitas Bayi, Waktu & Tempat, Orang Tua, Pelapor, TTD & Dokumen, Nomor Surat)
  - Table: columns (nama bayi, NIK, jenis kelamin, tanggal lahir, ayah, ibu, no surat, desa)
  - Preview & PDF generation (A4 format)
- ✅ Policy Kelahiran (sama pattern dengan Penduduk/KK)
- ✅ Observer Kelahiran (auto-generate nomor surat, file cleanup)
- ✅ Factory & Seeder Kelahiran
- ✅ Helper: SuratHelper untuk generate nomor surat
- ✅ View: Preview surat dengan responsive scaling (JS)

### D. Kematian
- ✅ Model Kematian (soft delete)
  - `penduduk_id` (foreignId -> penduduks)
  - `desa_id` (foreignId -> desas)
  - `tanggal_meninggal` (date)
  - `waktu_meninggal` (time)
  - `tempat_meninggal` (string)
  - `sebab_kematian` (string)
  - `tempat_pemakaman` (string)
  - `tanggal_pemakaman` (date)
  - Data pelapor (nama, NIK, hubungan, alamat, telepon)
  - TTD digital pelapor (SignaturePad & file upload)
  - `kepala_desa_id` (foreignId -> aparat_desas)
  - `no_surat_kematian` (auto-generate: SK/KMT/YYYY/MM/XXXXX)
  - `tanggal_surat` (date)
  - Foto surat dokter/RS (file upload)
- ✅ Resource Kematian (Filament)
  - Form: sections (Informasi Kematian, Pelapor, TTD & Dokumen, Dokumen & Keterangan)
  - Table: columns (nama lengkap, NIK, tanggal meninggal, tempat, sebab, no surat, desa)
  - Preview & PDF generation (A4 format)
- ✅ Policy Kematian (sama pattern dengan Penduduk/KK)
- ✅ Observer Kematian (auto-generate nomor surat, file cleanup)
- ✅ Factory & Seeder Kematian
- ✅ View: Preview surat dengan responsive scaling (JS)

### E. Mutasi Penduduk (Optional)
- [ ] Model Pindah Datang (soft delete)
- [ ] Model Pindah Keluar (soft delete)
- [ ] Resources & Policies

---

## 🔮 Fase Berikutnya (Future)

### F. Pelayanan Surat (Prioritas berdasarkan riset)

#### Phase 1 - Most Important (Paling Sering Diurus)
- [ ] **Surat Keterangan Domisili (SKD)**
  - Model: data penduduk, alamat lengkap, RT/RW, keperluan
  - Untuk: KTP, KK, NPWP, daftar sekolah, melamar kerja
  - Auto-generate nomor: SK/DOM/YYYY/MM/XXXXX

- [ ] **Surat Keterangan Usaha (SKU)**
  - Model: data penduduk, jenis usaha, nama usaha, alamat usaha, modal, karyawan
  - Untuk: NPWP, pinjaman bank, izin usaha, tender
  - Auto-generate nomor: SK/USH/YYYY/MM/XXXXX

- [ ] **Surat Pengantar SKCK**
  - Model: data penduduk, keperluan SKCK, kelakuan baik
  - Untuk: Melamar kerja, keperluan hukum, administrasi
  - Auto-generate nomor: SP/SKCK/YYYY/MM/XXXXX

#### Phase 2 - Important
- [ ] **Surat Keterangan Tidak Mampu (SKTM)**
  - Model: data kepala keluarga, jumlah tanggungan, penghasilan, keperluan
  - Untuk: Beasiswa, bantuan sosial, rumah sakit, pengobatan
  - Auto-generate nomor: SK/TM/YYYY/MM/XXXXX

- [ ] **Surat Keterangan Belum Menikah**
  - Model: data penduduk, status perkawinan, orang tua
  - Untuk: Persiapan menikah, administrasi KUA
  - Auto-generate nomor: SK/BM/YYYY/MM/XXXXX

#### Phase 3 - Needed
- [ ] **Surat Pengantar Pindah**
  - Model: data kepala keluarga, anggota keluarga, alamat tujuan, alasan pindah
  - Untuk: Mutasi KK, pindah domisili antar desa/kota
  - Auto-generate nomor: SP/PND/YYYY/MM/XXXXX

- [ ] **Surat Keterangan Kehilangan**
  - Model: data penduduk, barang hilang, kronologi, tempat/tanggal kejadian
  - Untuk: Laporan polisi, klaim asuransi, penggantian dokumen
  - Auto-generate nomor: SK/HL/YYYY/MM/XXXXX

#### Phase 4 - Additional
- [ ] **Surat Keterangan Penghasilan**
  - Model: data penduduk, pekerjaan, nominal penghasilan, keperluan
  - Untuk: Kredit, KPR, beasiswa, subsidi
  - Auto-generate nomor: SK/PGH/YYYY/MM/XXXXX

- [ ] **Surat Keterangan Ahli Waris**
  - Model: data pewaris (meninggal), data ahli waris (multiple), hubungan keluarga
  - Untuk: Warisan tanah, klaim asuransi, pencairan dana
  - Auto-generate nomor: SK/AW/YYYY/MM/XXXXX

- [ ] **Surat Keterangan Janda/Duda**
  - Model: data penduduk, data pasangan (meninggal/cerai), tanggal cerai/meninggal
  - Untuk: Menikah lagi, administrasi, bantuan sosial
  - Auto-generate nomor: SK/JD/YYYY/MM/XXXXX

#### Technical Notes
- Semua surat menggunakan pattern yang sama:
  - TTD digital (SignaturePad)
  - File upload pendukung
  - Auto-generate nomor surat dengan SuratHelper
  - Preview & PDF generation (A4)
  - Observer untuk file cleanup & nomor surat
  - Policy dengan scope desa untuk petugas desa
  - Factory & Seeder untuk testing

### G. Pengaduan & Aspirasi
- [ ] Model Pengaduan
- [ ] Tracking status pengaduan
- [ ] Notifikasi

### H. Dashboard & Statistik
- [ ] Widget jumlah penduduk by desa
- [ ] Chart demografi (usia, jenis kelamin)
- [ ] Grafik kelahiran/kematian per bulan
- [ ] Export laporan (Excel, PDF)

---

## 🔧 Recent Improvements (2026-02-10)

### Data Kependudukan
1. ✅ **Tambah enum "Saudara Kandung"** ke HubunganKeluarga
   - Migration untuk ALTER enum column
   - Update enum class dengan case SAUDARA_KANDUNG

2. ✅ **Filter Select Kepala Keluarga & Anggota**
   - Kepala Keluarga: hanya tampilkan penduduk dengan hubungan_keluarga = "kepala-keluarga"
   - Anggota Keluarga: exclude yang sudah jadi kepala keluarga
   - Label anggota keluarga menampilkan hubungan keluarga (misal: "Nama - NIK (Istri)")

3. ✅ **Query Scope untuk Petugas Desa**
   - PendudukResource: filter otomatis berdasarkan desa petugas
   - KartuKeluargaResource: filter otomatis berdasarkan desa petugas
   - Implementasi `getEloquentQuery()` method di Resource

4. ✅ **Observer Pattern Cleanup**
   - KartuKeluargaObserver: handle kepala keluarga (created, updated, restored)
   - Pages (Create/Edit): handle anggota keluarga dengan property class
   - Separation of concerns yang jelas dan maintainable
   - Fix error "Attempt to assign property on null"

5. ✅ **Policy Kartu Keluarga**
   - viewAny: semua user bisa akses resource
   - view/update/delete/restore: petugas kecamatan full access, petugas desa hanya KK di desa mereka
   - forceDelete: hanya petugas kecamatan
   - Pattern sama dengan PendudukPolicy untuk konsistensi

6. ✅ **Fix Soft Delete Handling**
   - Observer KK `deleted()`: auto-clear `kartu_keluarga_id` dari semua penduduk
   - Penduduk jadi available untuk di-assign ke KK lain setelah KK di-soft delete
   - Fix bug: kepala keluarga tidak muncul di dropdown setelah KK dihapus
   - Observer KK `restored()`: hanya restore kepala keluarga, anggota manual via edit
   - Separation yang jelas antara soft delete (observer handle) vs force delete (database cascade)

### Code Quality
- ✅ Semua code sudah di-format dengan Laravel Pint
- ✅ Best practices Laravel Observer sesuai dokumentasi
- ✅ Consistent naming conventions
- ✅ Proper use of Filament 4.x patterns
- ✅ All policies implemented with proper authorization logic

---

## 📝 Notes

### Design Patterns
- **Semua model menggunakan soft delete** untuk audit trail
- **Policy pattern**:
  - Petugas kecamatan = full access (termasuk force delete)
  - Petugas desa = scoped to their desa(s), bisa restore tapi tidak bisa force delete
  - Restore diperbolehkan untuk petugas desa (reversible action)
  - Force delete hanya untuk petugas kecamatan (irreversible, governance)
- **Observer pattern**:
  - Auto-fill, validation, file cleanup
  - Soft delete: observer handle manual cleanup
  - Force delete: database cascade handle otomatis
- **Seeder**: Provide sample data untuk development & testing

### Authorization Matrix
| Action | Petugas Kecamatan | Petugas Desa |
|--------|-------------------|--------------|
| View Any | ✅ All | ✅ All |
| View | ✅ All | ✅ Desa scope |
| Create | ✅ Yes | ✅ Yes |
| Update | ✅ All | ✅ Desa scope |
| Delete (Soft) | ✅ All | ✅ Desa scope |
| Restore | ✅ All | ✅ Desa scope |
| Force Delete | ✅ Yes | ❌ No |
