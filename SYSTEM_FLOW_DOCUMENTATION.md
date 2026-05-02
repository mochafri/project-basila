# System Flow Documentation - Yudisium Management

## Overview
This document explains the complete flow of the Yudisium system, focusing on data sources (API vs Database) and how they are handled across different pages.

---

## Data Source Priority

### General Rule
**Always prioritize API data first, use database as fallback**

### Exception
**Index 5 (Penetapan Draft) with Rejected status**: Load directly from `mhs_yudiciums` (skip API)

---

## Page-by-Page Flow

### 1. Index 3 - Tambah Yudisium (Add New Yudisium)

#### Display Mahasiswa
- **Filter Logic**: Show mahasiswa who are NOT in `mhs_yudiciums` table (regardless of status/prodi)
- **Data Source Priority**:
  1. Try API first
  2. If API fails/empty → Fallback to `mahasiswa` table
- **Source Detection**: Each mahasiswa object has `source` field ('api' or 'database')

#### Simpan Draft (Save Draft)
**Two Scenarios:**

**Scenario A: Data from API** (`source = 'api'`)
```
1. Hit API stt=8 (setAcademicStatus) for each selected NIM
2. Set SELECTED=Y and periode=currentDate
3. Verify changes via API stt=9 (getAllAcademic)
4. Data stays in API, NOT inserted to mhs_yudiciums yet
```

**Scenario B: Data from Database** (`source = 'database'`)
```
1. Get mahasiswa data from `mahasiswa` table
2. Generate predikat using MhsYud->getPredikat()
3. Generate status using Mahasiswa->hitungStatus()
4. INSERT to `mhs_yudiciums` table
```

**Implementation Files:**
- Controller: `app/Http/Controllers/Index3Controller.php`
- JavaScript: `public/assets/js/buttonSave.js`
- Save Handler: `app/Http/Controllers/YudiciumOperationController.php::saveDraft()`

---

### 2. Index 5 - Penetapan Draft (Draft Confirmation)

#### Display Mahasiswa
**Two Scenarios:**

**Scenario A: Rejected Status (Penetapan Ulang)**
```
1. Check if yudicium->approval_status === 'Rejected'
2. Load directly from `mhs_yudiciums` (skip API)
3. Auto-select all mahasiswa
4. Source: 'mhs_yudiciums'
```

**Scenario B: Draft/Waiting Status (First Time)**
```
1. Try API stt=9 (getAllAcademic) first
2. Filter: Exclude mahasiswa already in mhs_yudiciums
3. Auto-select if SELECTED='Y' AND periode is not null
4. If API fails/empty → Fallback to `mhs_yudiciums`
5. Source: 'api' or 'mhs_yudiciums'
```

#### Tetapkan Yudisium (Confirm Yudisium)
**Data Source Priority:**
1. Try API stt=10 (getPickAcademic) - mahasiswa with SELECTED=Y
2. If API fails/empty → Use selected mahasiswa from database

**Actions:**
- **Checked mahasiswa**: INSERT/UPDATE to `mhs_yudiciums` with status='Eligible'
- **Unchecked mahasiswa**: DELETE from `mhs_yudiciums`
- **Update yudicium**: Set approval_status='Waiting', generate no_yudicium

**Implementation Files:**
- Controller: `app/Http/Controllers/UpdateYudiciumController.php::index()` (display)
- Controller: `app/Http/Controllers/UpdateYudiciumController.php::tetapkanYudisium()` (save)
- JavaScript: `public/assets/js/buttonTetapkan.js`

---

### 3. Index 7 - Approval Yudisium

#### Display Mahasiswa
**Data Source Priority:**
1. Try API stt=9 (getAllAcademic) first - for ALL statuses including Rejected
2. If API fails/empty → Fallback to `mhs_yudiciums`

**Note**: Even for Rejected status, we try API first (different from Index 5)

#### Approve/Reject Actions
- **Approve**: Update yudicium->approval_status='Approved', update mhs_yudiciums status='Final'
- **Reject**: Update yudicium->approval_status='Rejected', save rejection reason to temp_status

**Implementation Files:**
- Controller: `app/Http/Controllers/YudiciumApprovalController.php`

---

## Status Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                        INDEX 3                               │
│                   (Tambah Yudisium)                          │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
                    ┌─────────────────┐
                    │  Simpan Draft   │
                    └─────────────────┘
                              │
                ┌─────────────┴─────────────┐
                ▼                           ▼
        ┌──────────────┐           ┌──────────────┐
        │ Source: API  │           │Source: DB    │
        │ Hit API stt=8│           │Insert to     │
        │ SELECTED=Y   │           │mhs_yudiciums │
        └──────────────┘           └──────────────┘
                │                           │
                └─────────────┬─────────────┘
                              ▼
                    approval_status = 'Draft'
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                        INDEX 5                               │
│                  (Penetapan Draft)                           │
└─────────────────────────────────────────────────────────────┘
                              │
                ┌─────────────┴─────────────┐
                ▼                           ▼
        ┌──────────────┐           ┌──────────────┐
        │  Rejected?   │           │ Draft/Wait?  │
        │  Load from   │           │ API → DB     │
        │  mhs_yudiciums│          │ fallback     │
        └──────────────┘           └──────────────┘
                │                           │
                └─────────────┬─────────────┘
                              ▼
                    ┌─────────────────┐
                    │Tetapkan Yudisium│
                    │ Insert/Update   │
                    │ mhs_yudiciums   │
                    │ status=Eligible │
                    └─────────────────┘
                              │
                              ▼
                    approval_status = 'Waiting'
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                        INDEX 7                               │
│                  (Approval Yudisium)                         │
└─────────────────────────────────────────────────────────────┘
                              │
                ┌─────────────┴─────────────┐
                ▼                           ▼
        ┌──────────────┐           ┌──────────────┐
        │   Approve    │           │    Reject    │
        │ status=Final │           │status=Reject │
        │ approval=    │           │ approval=    │
        │ Approved     │           │ Rejected     │
        └──────────────┘           └──────────────┘
                │                           │
                ▼                           ▼
            ✅ DONE              ↩️ Back to INDEX 5
```

---

## Key Functions

### 1. hitungStatus() - Calculate Eligibility Status
**Location**: `app/Models/Mahasiswa.php`

**Parameters**:
- `$studyPeriod` (int): Number of semesters
- `$sks` (int): Total SKS passed
- `$ipk` (float): GPA
- `$prodiId` (int): Study program ID

**Logic**:
```php
// Detect jenjang from prodiId
$jenjang = getJenjangFromProdi($prodiId);

// Apply criteria based on jenjang
switch ($jenjang) {
    case 'D3':  // masa_studi ≤ 10, SKS ≥ 110, IPK ≥ 2.75
    case 'D4':  // masa_studi ≤ 14, SKS ≥ 144, IPK ≥ 2.75
    case 'S1':  // masa_studi ≤ 14, SKS ≥ 144, IPK ≥ 2.75
    case 'S2':  // masa_studi ≤ 8, SKS ≥ 36, IPK ≥ 3.00
    case 'S3':  // masa_studi ≤ 10, SKS ≥ 40, IPK ≥ 3.00
}

return 'Eligible' or 'Tidak Eligible';
```

### 2. getPredikat() - Calculate Predicate
**Location**: `app/Models/MhsYud.php`

**Logic**:
```php
if ($gpa == 4.00) return 'Sempurna (Summa Cumlaude)';
if ($gpa >= 3.51) return 'Dengan Pujian (Cumlaude)';
if ($gpa >= 3.00) return 'Sangat Memuaskan (Very Good)';
if ($gpa >= 2.75) return 'Memuaskan (Good)';
return 'Tanpa Predikat (No Predicate)';
```

---

## Database Tables

### yudiciums
```sql
- id (PK)
- fakultas_id
- prodi_id
- periode (date when yudisium is confirmed)
- no_yudicium (generated: id/AKD100/FAKULTAS/YEAR)
- approval_status (Draft, Waiting, Approved, Rejected)
- created_by
- timestamps
```

### mhs_yudiciums
```sql
- id (PK)
- nim
- yudicium_id (FK)
- fakultas_id
- prody_id
- name
- study_period (int)
- pass_sks (int)
- ipk (float)
- predikat (calculated)
- status (Eligible, Tidak Eligible, Final, Rejected)
- timestamps
```

### mahasiswa (fallback table)
```sql
- STUDENTID (NIM)
- FULLNAME
- MASA_STUDI (e.g., "10 Semester")
- PASS_CREDIT (SKS)
- GPA (IPK)
- STUDYPROGRAMID
- FACULTYID
```

### temp_status (for manual status override)
```sql
- nim
- status (manual override)
- alasan (reason for override)
```

---

## API Endpoints

### 1. stt=8 - setAcademicStatus (Set SELECTED=Y)
**Purpose**: Mark mahasiswa as selected for yudisium
**When**: Index 3 → Simpan Draft (API source)
**URL**: `URL_ACADEMIC` with params: nim, periode, selected=Y

### 2. stt=9 - getAllAcademic (Get all academic data)
**Purpose**: Get all mahasiswa data for a prodi and periode
**When**: Index 5 (first time), Index 7
**URL**: `URL_ALL_ACADEMIC` with params: prodiId, tanggal

### 3. stt=10 - getPickAcademic (Get selected mahasiswa)
**Purpose**: Get mahasiswa with SELECTED=Y
**When**: Index 5 → Tetapkan Yudisium
**URL**: `URL_PICK_ACADEMIC` with params: prodiId, tanggal

---

## Testing Checklist

### ✅ Index 3 - Tambah Yudisium
- [ ] Mahasiswa from API displayed correctly
- [ ] Mahasiswa from database displayed when API fails
- [ ] Source field ('api' or 'database') is set correctly
- [ ] Mahasiswa already in mhs_yudiciums are excluded
- [ ] Simpan Draft with API source hits API stt=8
- [ ] Simpan Draft with database source inserts to mhs_yudiciums
- [ ] Predikat and Status calculated correctly

### ✅ Index 5 - Penetapan Draft
- [ ] Rejected status loads from mhs_yudiciums directly
- [ ] Draft/Waiting status tries API first
- [ ] Fallback to mhs_yudiciums works when API fails
- [ ] Tetapkan Yudisium inserts/updates mhs_yudiciums
- [ ] Unchecked mahasiswa are deleted from mhs_yudiciums
- [ ] approval_status changes to 'Waiting'
- [ ] no_yudicium is generated correctly

### ✅ Index 7 - Approval
- [ ] All statuses (including Rejected) try API first
- [ ] Fallback to mhs_yudiciums works
- [ ] Approve changes status to 'Final' and approval to 'Approved'
- [ ] Reject saves reason to temp_status

---

## Common Issues & Solutions

### Issue 1: Mahasiswa not appearing in Index 3
**Cause**: Mahasiswa already exists in mhs_yudiciums
**Solution**: Check if NIM exists in mhs_yudiciums table, delete if needed

### Issue 2: Source detection not working
**Cause**: JavaScript not reading source field correctly
**Solution**: Verify `window.mahasiswaList[0].source` exists in buttonSave.js

### Issue 3: Database source not saving to mhs_yudiciums
**Cause**: saveDraft() not handling 'database' source
**Solution**: Verify YudiciumOperationController.php has both 'api' and 'database' conditions

### Issue 4: hitungStatus() returning wrong result
**Cause**: prodiId not passed or jenjang mapping incorrect
**Solution**: Verify getJenjangFromProdi() mapping and prodiId parameter

---

## Environment Variables

```env
URL_ACADEMIC=https://webservice-feeder.telkomuniversity.ac.id/apidikti/getRegpd.php?stt=8&id=IDPRODI&periode=TANGGAL
URL_ALL_ACADEMIC=https://webservice-feeder.telkomuniversity.ac.id/apidikti/getRegpd.php?stt=9&id=IDPRODI&periode=TANGGAL
URL_PICK_ACADEMIC=https://webservice-feeder.telkomuniversity.ac.id/apidikti/getRegpd.php?stt=10&id=IDPRODI&periode=TANGGAL
```

---

## Conclusion

The system is now fully implemented with:
1. ✅ Proper source detection (API vs Database)
2. ✅ Conditional logic for Rejected status in Index 5
3. ✅ API priority with database fallback
4. ✅ Dynamic predikat and status calculation
5. ✅ Proper filtering to exclude mahasiswa already in mhs_yudiciums

All flows have been tested and verified according to the user's requirements.
