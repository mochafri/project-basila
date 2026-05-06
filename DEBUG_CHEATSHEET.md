# Debug Cheat Sheet - Uncheck Mahasiswa Feature

## Quick Commands

### Clear Cache
```bash
php artisan view:clear
php artisan cache:clear
php artisan config:clear
```

### Watch Logs
```bash
# Watch Laravel logs in real-time
tail -f storage/logs/laravel.log

# Filter for uncheck-related logs
tail -f storage/logs/laravel.log | grep -i "uncheck"

# Filter for specific NIM
tail -f storage/logs/laravel.log | grep "05420197880"
```

### Database Queries
```sql
-- Check mahasiswa in mhs_yudiciums
SELECT nim, name, yudicium_id, status, created_at 
FROM mhs_yudiciums 
WHERE yudicium_id = 1 
ORDER BY nim;

-- Check yudicium details
SELECT id, no_yudicium, approval_status, periode, created_at 
FROM yudiciums 
WHERE id = 1;

-- Count mahasiswa per yudicium
SELECT yudicium_id, COUNT(*) as total 
FROM mhs_yudiciums 
GROUP BY yudicium_id;

-- Find mahasiswa by NIM
SELECT * FROM mhs_yudiciums WHERE nim = '05420197880';

-- Delete specific mahasiswa (for testing)
DELETE FROM mhs_yudiciums WHERE nim = '05420197880' AND yudicium_id = 1;
```

---

## Browser Console Commands

### Check Initial Data
```javascript
// Check if allMahasiswaData is loaded
console.log('Total mahasiswa:', window.allMahasiswaData.length);
console.log('Sample data:', window.allMahasiswaData[0]);

// Check sources
window.allMahasiswaData.forEach(mhs => {
    console.log(`${mhs.nim}: ${mhs.source}`);
});

// Count by source
const sources = window.allMahasiswaData.reduce((acc, mhs) => {
    acc[mhs.source] = (acc[mhs.source] || 0) + 1;
    return acc;
}, {});
console.log('Sources:', sources);
```

### Check Checkbox States
```javascript
// Access checkboxStates (run in console after page load)
// Note: checkboxStates is in closure, so you need to add this to buttonTetapkan.js:
// window.debugCheckboxStates = () => checkboxStates;

// Then in console:
const states = window.debugCheckboxStates();
console.log('Total states:', states.size);

// Show all states
states.forEach((state, nim) => {
    console.log(`${nim}: checked=${state.checked}, source=${state.source}, initial=${state.initialChecked}`);
});

// Show only unchecked
states.forEach((state, nim) => {
    if (state.initialChecked && !state.checked) {
        console.log(`UNCHECKED: ${nim} (${state.source})`);
    }
});
```

### Test Uncheck API Manually
```javascript
// Test uncheck API call
fetch('/dashboard/uncheck-mahasiswa', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        yudicium_id: 1,
        unchecked_mahasiswa: [
            { nim: '05420197880', source: 'api' },
            { nim: '05420220878', source: 'database' }
        ]
    })
})
.then(res => res.json())
.then(data => console.log('Result:', data))
.catch(err => console.error('Error:', err));
```

---

## API Testing

### Test API stt=11 (Reset)
```bash
# Using curl
curl "https://webservice-feeder.telkomuniversity.ac.id/apidikti/getRegpd.php?stt=11&id=05420197880&periode=2025-05-03"

# Expected response: "true" or "1"
```

### Test API stt=9 (Get All Academic)
```bash
# Check if mahasiswa still appears after reset
curl "https://webservice-feeder.telkomuniversity.ac.id/apidikti/getRegpd.php?stt=9&id=PRODI_ID&periode=2025-05-03"

# Look for STUDENTID in response
```

### Test API stt=10 (Get Selected)
```bash
# Check selected mahasiswa
curl "https://webservice-feeder.telkomuniversity.ac.id/apidikti/getRegpd.php?stt=10&id=PRODI_ID&periode=2025-05-03"

# Should NOT include unchecked mahasiswa
```

---

## Common Issues & Quick Fixes

### Issue 1: `routes.uncheckMahasiswa is undefined`

**Check:**
```javascript
console.log('Routes:', routes);
// Should show: { approveYudicium: "...", uncheckMahasiswa: "...", ubahStatus: "..." }
```

**Fix:**
```bash
php artisan view:clear
# Refresh browser
```

---

### Issue 2: `source` field is undefined

**Check in Console:**
```javascript
console.log('Has source?', window.allMahasiswaData[0].hasOwnProperty('source'));
console.log('Source value:', window.allMahasiswaData[0].source);
```

**Check in Controller:**
```php
// Add this to UpdateYudiciumController.php index() method
Log::info('Mahasiswa data sample', ['data' => $datas->first()]);
```

**Fix:**
- Ensure all data objects in controller have `'source' => 'api'` or `'source' => 'mhs_yudiciums'`
- Clear view cache: `php artisan view:clear`

---

### Issue 3: Uncheck API not called

**Check in Console:**
```javascript
// Add breakpoint or console.log in buttonTetapkan.js
console.log('Unchecked count:', uncheckedMahasiswa.length);
console.log('Unchecked data:', uncheckedMahasiswa);
```

**Check Network Tab:**
- Open DevTools → Network tab
- Click "Tetapkan Yudisium"
- Look for POST request to `/dashboard/uncheck-mahasiswa`
- Check request payload and response

**Fix:**
- Ensure `initialChecked` is set correctly
- Ensure checkbox state changes are tracked
- Check if `uncheckedMahasiswa.length > 0` condition is met

---

### Issue 4: API stt=11 returns false

**Check Laravel Log:**
```bash
tail -f storage/logs/laravel.log | grep "API reset"
```

**Check API Response:**
```php
// In UpdateYudiciumController.php uncheckMahasiswa()
Log::info('API Response', [
    'nim' => $nim,
    'status' => $response->status(),
    'body' => $response->body()
]);
```

**Possible Causes:**
- NIM not found in API
- Periode doesn't match
- API endpoint down
- Network timeout

**Fix:**
- Verify NIM exists in API (use stt=9)
- Verify periode format (YYYY-MM-DD)
- Check API endpoint availability
- Increase timeout: `Http::timeout(30)->get($apiUrl)`

---

### Issue 5: Database delete not working

**Check Laravel Log:**
```bash
tail -f storage/logs/laravel.log | grep "hapus dari mhs_yudiciums"
```

**Check Database:**
```sql
-- Before delete
SELECT * FROM mhs_yudiciums WHERE nim = '05420220878' AND yudicium_id = 1;

-- After delete (should return 0 rows)
SELECT * FROM mhs_yudiciums WHERE nim = '05420220878' AND yudicium_id = 1;
```

**Check Delete Query:**
```php
// In UpdateYudiciumController.php uncheckMahasiswa()
$deleted = MhsYud::where('nim', $nim)
    ->where('yudicium_id', $yudiciumId)
    ->delete();

Log::info('Delete result', ['nim' => $nim, 'deleted' => $deleted]);
```

**Fix:**
- Verify NIM and yudicium_id are correct
- Check if record exists before delete
- Check database connection

---

## Performance Monitoring

### Check API Response Time
```php
// In UpdateYudiciumController.php
$startTime = microtime(true);
$response = Http::timeout(10)->get($apiUrl);
$endTime = microtime(true);
$duration = ($endTime - $startTime) * 1000; // milliseconds

Log::info('API Response Time', [
    'nim' => $nim,
    'duration_ms' => $duration
]);
```

### Check Database Query Time
```php
// Enable query log
DB::enableQueryLog();

// Your delete query
MhsYud::where('nim', $nim)->where('yudicium_id', $yudiciumId)->delete();

// Get queries
$queries = DB::getQueryLog();
Log::info('Database Queries', ['queries' => $queries]);
```

---

## Debugging Workflow

### Step 1: Verify Data Flow
1. ✅ Check controller sends `source` field
2. ✅ Check blade passes to JavaScript
3. ✅ Check JavaScript receives data
4. ✅ Check checkbox states are tracked

### Step 2: Verify Uncheck Detection
1. ✅ Uncheck some mahasiswa
2. ✅ Check console for unchecked array
3. ✅ Verify source field is present
4. ✅ Verify initialChecked vs checked comparison

### Step 3: Verify API Call
1. ✅ Check Network tab for POST request
2. ✅ Check request payload
3. ✅ Check response status and body
4. ✅ Check Laravel log for API calls

### Step 4: Verify Backend Processing
1. ✅ Check Laravel log for processing logs
2. ✅ Check API stt=11 calls (for API source)
3. ✅ Check database deletes (for database source)
4. ✅ Check error logs

### Step 5: Verify Results
1. ✅ Check API stt=9 or stt=10 (mahasiswa should not appear)
2. ✅ Check database (mahasiswa should be deleted)
3. ✅ Check tetapkan yudisium proceeds correctly
4. ✅ Check final yudicium status

---

## Quick Test Script

### Create Test Data
```sql
-- Insert test mahasiswa
INSERT INTO mhs_yudiciums (nim, yudicium_id, fakultas_id, prody_id, name, study_period, pass_sks, ipk, predikat, status)
VALUES 
('TEST001', 1, 3, 11, 'Test Student 1', 8, 144, 3.50, 'Dengan Pujian (Cumlaude)', 'Eligible'),
('TEST002', 1, 3, 11, 'Test Student 2', 8, 144, 3.75, 'Dengan Pujian (Cumlaude)', 'Eligible');
```

### Test Uncheck
```javascript
// In browser console
fetch('/dashboard/uncheck-mahasiswa', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        yudicium_id: 1,
        unchecked_mahasiswa: [
            { nim: 'TEST001', source: 'database' }
        ]
    })
})
.then(res => res.json())
.then(data => {
    console.log('Success:', data.success);
    console.log('Message:', data.message);
    console.log('Results:', data.results);
});
```

### Verify Delete
```sql
-- Should return 0 rows
SELECT * FROM mhs_yudiciums WHERE nim = 'TEST001';

-- Should return 1 row
SELECT * FROM mhs_yudiciums WHERE nim = 'TEST002';
```

---

## Emergency Rollback

### If something goes wrong:

```bash
# 1. Revert code changes
git checkout HEAD -- app/Http/Controllers/UpdateYudiciumController.php
git checkout HEAD -- routes/web.php
git checkout HEAD -- resources/views/dashboard/index5.blade.php
git checkout HEAD -- public/assets/js/buttonTetapkan.js

# 2. Clear cache
php artisan view:clear
php artisan cache:clear
php artisan config:clear

# 3. Restart server (if needed)
php artisan serve
```

---

## Contact & Support

If you encounter issues not covered here:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check browser console for JavaScript errors
3. Check Network tab for failed requests
4. Review `TESTING_UNCHECK_MAHASISWA.md` for detailed scenarios
5. Review `IMPLEMENTATION_CHECKLIST.md` for implementation details

---

**Last Updated**: 2026-05-03
**Version**: 1.0
**Status**: Production Ready ✅
