# Testing Guide - Uncheck Mahasiswa Feature

## Overview
Fitur ini memungkinkan user untuk uncheck mahasiswa di Index 5 (Penetapan Draft) dengan logika berbeda berdasarkan sumber data:
- **Data dari API**: Hit API stt=11 untuk reset status (SELECTED=N)
- **Data dari Database/mhs_yudiciums**: Hapus dari tabel `mhs_yudiciums`

---

## Prerequisites

1. **Database**: Pastikan ada data di tabel `yudiciums` dan `mhs_yudiciums`
2. **API**: Pastikan API endpoint tersedia dan bisa diakses
3. **Environment**: Pastikan `.env` memiliki `URL_RESET_ACADEMIC`

---

## Test Scenarios

### Scenario 1: Uncheck Mahasiswa dari API

**Setup:**
1. Buka Index 3 (Tambah Yudisium)
2. Pilih Fakultas dan Prodi yang memiliki data di API
3. Klik "Tampilkan" - data akan muncul dari API
4. Pilih beberapa mahasiswa dan klik "Simpan Draft"
5. Data akan di-set ke API dengan SELECTED=Y

**Test Steps:**
1. Buka Index 5 (Penetapan Draft) untuk yudisium yang baru dibuat
2. Mahasiswa akan muncul dengan `source: 'api'`
3. **Uncheck** beberapa mahasiswa
4. Klik "Tetapkan Yudisium"
5. Buka Console Browser (F12)

**Expected Results:**
```javascript
// Console log akan menampilkan:
Unchecked Mahasiswa: 2 [
  { nim: "05420197880", source: "api" },
  { nim: "05420208609", source: "api" }
]

Processing unchecked mahasiswa...
Uncheck result: {
  success: true,
  message: "Berhasil memproses 2 mahasiswa",
  results: {
    api_reset: ["05420197880", "05420208609"],
    database_deleted: [],
    errors: []
  }
}
```

**Verification:**
- Cek API dengan stt=9 atau stt=10, mahasiswa yang di-uncheck seharusnya SELECTED=N atau tidak muncul
- Mahasiswa yang masih checked akan masuk ke `mhs_yudiciums` dengan status 'Eligible'

---

### Scenario 2: Uncheck Mahasiswa dari Database

**Setup:**
1. Buka Index 3 (Tambah Yudisium)
2. Pilih Fakultas dan Prodi yang TIDAK memiliki data di API (akan fallback ke database)
3. Klik "Tampilkan" - data akan muncul dari database
4. Pilih beberapa mahasiswa dan klik "Simpan Draft"
5. Data akan langsung masuk ke `mhs_yudiciums`

**Test Steps:**
1. Buka Index 5 (Penetapan Draft) untuk yudisium yang baru dibuat
2. Mahasiswa akan muncul dengan `source: 'database'` atau `'mhs_yudiciums'`
3. **Uncheck** beberapa mahasiswa
4. Klik "Tetapkan Yudisium"
5. Buka Console Browser (F12)

**Expected Results:**
```javascript
// Console log akan menampilkan:
Unchecked Mahasiswa: 2 [
  { nim: "05420220878", source: "database" },
  { nim: "05420220999", source: "mhs_yudiciums" }
]

Processing unchecked mahasiswa...
Uncheck result: {
  success: true,
  message: "Berhasil memproses 2 mahasiswa",
  results: {
    api_reset: [],
    database_deleted: ["05420220878", "05420220999"],
    errors: []
  }
}
```

**Verification:**
- Cek tabel `mhs_yudiciums`, mahasiswa yang di-uncheck seharusnya sudah terhapus
- Mahasiswa yang masih checked akan tetap ada di `mhs_yudiciums`

---

### Scenario 3: Mixed Sources (API + Database)

**Setup:**
1. Buat yudisium dengan mahasiswa dari API
2. Buat yudisium lain dengan mahasiswa dari Database
3. Atau gunakan yudisium yang sudah Rejected (akan load dari mhs_yudiciums)

**Test Steps:**
1. Buka Index 5 dengan yudisium yang memiliki mixed sources
2. Beberapa mahasiswa akan memiliki `source: 'api'`
3. Beberapa mahasiswa akan memiliki `source: 'database'` atau `'mhs_yudiciums'`
4. **Uncheck** mahasiswa dari kedua source
5. Klik "Tetapkan Yudisium"

**Expected Results:**
```javascript
Unchecked Mahasiswa: 4 [
  { nim: "05420197880", source: "api" },
  { nim: "05420208609", source: "api" },
  { nim: "05420220878", source: "database" },
  { nim: "05420220999", source: "mhs_yudiciums" }
]

Uncheck result: {
  success: true,
  message: "Berhasil memproses 4 mahasiswa",
  results: {
    api_reset: ["05420197880", "05420208609"],
    database_deleted: ["05420220878", "05420220999"],
    errors: []
  }
}
```

---

### Scenario 4: Error Handling - API Timeout

**Setup:**
1. Matikan koneksi internet atau block API endpoint
2. Buat yudisium dengan mahasiswa dari API

**Test Steps:**
1. Buka Index 5
2. Uncheck mahasiswa dengan `source: 'api'`
3. Klik "Tetapkan Yudisium"

**Expected Results:**
```javascript
Uncheck result: {
  success: false,
  message: "Berhasil memproses 0 mahasiswa, 2 error",
  results: {
    api_reset: [],
    database_deleted: [],
    errors: [
      "NIM 05420197880: API error (status 500)",
      "NIM 05420208609: API error (status 500)"
    ]
  }
}
```

**Verification:**
- User akan melihat error message
- Proses tetapkan yudisium akan tetap berjalan untuk mahasiswa yang checked

---

## Debugging Tips

### 1. Check Console Logs
Buka Browser Console (F12) dan cek:
```javascript
// Initial state
console.log('Initialized states:', checkboxStates.size, 'total checkboxes');

// When checkbox changed
console.log('Checkbox changed:', nim, checked);

// Before tetapkan
console.log('Selected NIMs:', selectedNims.length, selectedNims);
console.log('Unchecked Mahasiswa:', uncheckedMahasiswa.length, uncheckedMahasiswa);

// After uncheck API call
console.log('Uncheck result:', uncheckData);
```

### 2. Check Laravel Logs
```bash
tail -f storage/logs/laravel.log
```

Look for:
```
[timestamp] local.INFO: Uncheck mahasiswa dari API, hit stt=11 {"nim":"05420197880","url":"..."}
[timestamp] local.INFO: Berhasil reset API untuk NIM: 05420197880
[timestamp] local.INFO: Berhasil hapus dari mhs_yudiciums untuk NIM: 05420220878
```

### 3. Check Database
```sql
-- Cek mahasiswa di mhs_yudiciums
SELECT nim, name, yudicium_id, status FROM mhs_yudiciums WHERE yudicium_id = 1;

-- Cek yudicium status
SELECT id, no_yudicium, approval_status, periode FROM yudiciums WHERE id = 1;
```

### 4. Check API Response
Gunakan Postman atau curl untuk test API:
```bash
# Test API stt=11 (Reset)
curl "https://webservice-feeder.telkomuniversity.ac.id/apidikti/getRegpd.php?stt=11&id=05420197880&periode=2025-05-03"

# Expected response: "true" atau "1"
```

---

## Common Issues

### Issue 1: `routes.uncheckMahasiswa is undefined`
**Cause**: Route tidak terdefinisi di blade file
**Solution**: Pastikan di `index5.blade.php` ada:
```javascript
const routes = {
    approveYudicium: "{{ route('yudicium.tetapkan') }}",
    uncheckMahasiswa: "{{ route('yudicium.uncheck') }}",
    ubahStatus: "{{ route('tempStatus') }}",
};
```

### Issue 2: `source` field is undefined
**Cause**: Data dari controller tidak memiliki field `source`
**Solution**: Pastikan di `UpdateYudiciumController.php` semua data object memiliki field `'source' => 'api'` atau `'source' => 'mhs_yudiciums'`

### Issue 3: API stt=11 return false
**Cause**: NIM tidak ditemukan atau periode tidak match
**Solution**: 
- Cek apakah NIM benar-benar ada di API
- Cek apakah periode match dengan data di API
- Cek log Laravel untuk melihat URL yang di-hit

### Issue 4: Data tidak terhapus dari mhs_yudiciums
**Cause**: NIM atau yudicium_id tidak match
**Solution**:
- Cek query di `uncheckMahasiswa()` method
- Pastikan NIM dan yudicium_id benar
- Cek log Laravel untuk melihat hasil delete

---

## Success Criteria

✅ **Uncheck mahasiswa dari API**:
- API stt=11 di-hit dengan benar
- Response API adalah "true" atau "1"
- Mahasiswa tidak muncul lagi di API stt=9 atau stt=10

✅ **Uncheck mahasiswa dari Database**:
- Data terhapus dari tabel `mhs_yudiciums`
- Query DELETE berhasil dijalankan

✅ **Tetapkan Yudisium**:
- Hanya mahasiswa yang masih checked yang masuk ke `mhs_yudiciums`
- Status yudicium berubah menjadi 'Waiting'
- Nomor yudisium ter-generate dengan benar

✅ **Error Handling**:
- Error API ditangani dengan baik
- User mendapat feedback yang jelas
- Proses tidak berhenti karena 1 error

---

## API Endpoints Reference

### 1. URL_RESET_ACADEMIC (stt=11)
```
https://webservice-feeder.telkomuniversity.ac.id/apidikti/getRegpd.php?stt=11&id=NIM&periode=TANGGAL
```
**Purpose**: Reset SELECTED status untuk mahasiswa
**Parameters**:
- `stt=11`: Status code untuk reset
- `id=NIM`: NIM mahasiswa
- `periode=TANGGAL`: Tanggal periode (format: YYYY-MM-DD)

**Response**: `"true"` atau `"1"` jika berhasil

### 2. Backend Endpoint
```
POST /dashboard/uncheck-mahasiswa
```
**Request Body**:
```json
{
  "yudicium_id": 1,
  "unchecked_mahasiswa": [
    { "nim": "05420197880", "source": "api" },
    { "nim": "05420220878", "source": "database" }
  ]
}
```

**Response**:
```json
{
  "success": true,
  "message": "Berhasil memproses 2 mahasiswa",
  "results": {
    "api_reset": ["05420197880"],
    "database_deleted": ["05420220878"],
    "errors": []
  }
}
```

---

## Conclusion

Fitur uncheck mahasiswa sudah diimplementasikan dengan:
1. ✅ Deteksi source data (API vs Database)
2. ✅ Logika berbeda untuk setiap source
3. ✅ Error handling yang baik
4. ✅ Logging untuk debugging
5. ✅ Feedback ke user

Silakan test sesuai scenario di atas dan laporkan jika ada issue! 🚀
