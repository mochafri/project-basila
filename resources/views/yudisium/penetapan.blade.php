@extends('layouts.app')

@section('title', 'Penetapan Yudisium')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Penetapan Yudisium</h4>
                    <p class="text-muted">Sistem Dual-Source: API (Primary) & Database (Fallback)</p>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="fakultas">Fakultas</label>
                            <select class="form-control" id="fakultas">
                                <option value="">Pilih Fakultas</option>
                                <!-- Populate from backend -->
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="prodi">Program Studi</label>
                            <select class="form-control" id="prodi">
                                <option value="">Pilih Prodi</option>
                                <!-- Populate from backend -->
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="periode">Periode</label>
                            <input type="text" class="form-control" id="periode" placeholder="20241">
                        </div>
                        <div class="col-md-3">
                            <label for="yudicium_id">Yudisium ID</label>
                            <input type="number" class="form-control" id="yudicium_id" placeholder="1">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <button type="button" class="btn btn-primary" onclick="filterMahasiswa()">
                                <i class="ri-search-line"></i> Tampilkan Data
                            </button>
                            <button type="button" class="btn btn-success" onclick="simpanDraft()" id="btnSimpanDraft" disabled>
                                <i class="ri-save-line"></i> Simpan Draft
                            </button>
                            <button type="button" class="btn btn-info" onclick="getDraft()" id="btnGetDraft" disabled>
                                <i class="ri-file-list-line"></i> Lihat Draft
                            </button>
                            <button type="button" class="btn btn-warning" onclick="tetapkanYudisium()" id="btnTetapkan" disabled>
                                <i class="ri-check-double-line"></i> Tetapkan Yudisium
                            </button>
                        </div>
                    </div>

                    <!-- Source Indicator -->
                    <div class="alert alert-info" id="sourceIndicator" style="display: none;">
                        <strong>Sumber Data:</strong> <span id="sourceText"></span>
                    </div>

                    <!-- Data Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="mahasiswaTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="checkAll" onclick="toggleCheckAll()">
                                    </th>
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    <th>Masa Studi</th>
                                    <th>SKS Lulus</th>
                                    <th>IPK</th>
                                    <th>Predikat</th>
                                    <th>Status</th>
                                    <th>Source</th>
                                </tr>
                            </thead>
                            <tbody id="mahasiswaTableBody">
                                <tr>
                                    <td colspan="9" class="text-center text-muted">
                                        Silakan pilih filter dan klik "Tampilkan Data"
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary -->
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="alert alert-secondary">
                                <strong>Total Data:</strong> <span id="totalData">0</span> |
                                <strong>Terpilih:</strong> <span id="totalSelected">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentSource = null;
    let mahasiswaData = [];

    // CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    /**
     * Filter Mahasiswa (Tampilkan Data)
     */
    async function filterMahasiswa() {
        const prodi = document.getElementById('prodi').value;
        const periode = document.getElementById('periode').value;
        const fakultas = document.getElementById('fakultas').value;

        if (!prodi || !periode) {
            alert('Prodi dan Periode harus diisi!');
            return;
        }

        try {
            showLoading();

            const response = await fetch('/yudisium/filter-mahasiswa', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ prodi, periode, fakultas })
            });

            const result = await response.json();

            if (result.success) {
                currentSource = result.source;
                mahasiswaData = result.data;

                // Update UI
                document.getElementById('sourceText').textContent = 
                    result.source === 'api' ? 'API Eksternal' : 'Database Lokal (Fallback)';
                document.getElementById('sourceIndicator').style.display = 'block';

                renderTable(result.data);
                updateSummary();

                // Enable buttons
                document.getElementById('btnSimpanDraft').disabled = false;
                document.getElementById('btnGetDraft').disabled = false;
                document.getElementById('btnTetapkan').disabled = false;

                showNotification('success', result.message);
            } else {
                showNotification('error', result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('error', 'Terjadi kesalahan saat mengambil data');
        } finally {
            hideLoading();
        }
    }

    /**
     * Render Table
     */
    function renderTable(data) {
        const tbody = document.getElementById('mahasiswaTableBody');
        tbody.innerHTML = '';

        if (data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center text-muted">Tidak ada data</td>
                </tr>
            `;
            return;
        }

        data.forEach((item, index) => {
            const row = `
                <tr>
                    <td>
                        <input type="checkbox" class="mahasiswa-check" 
                               data-index="${index}" 
                               onchange="updateSummary()">
                    </td>
                    <td>${item.nim || '-'}</td>
                    <td>${item.nama || '-'}</td>
                    <td>${item.masa_studi || '-'}</td>
                    <td>${item.sks_lulus || '-'}</td>
                    <td>${item.ipk || '-'}</td>
                    <td>${item.predikat || '-'}</td>
                    <td>
                        <span class="badge ${item.status === 'Eligible' ? 'bg-success' : 'bg-warning'}">
                            ${item.status || '-'}
                        </span>
                    </td>
                    <td>
                        <span class="badge ${item.source === 'api' ? 'bg-primary' : 'bg-secondary'}">
                            ${item.source || '-'}
                        </span>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });

        document.getElementById('totalData').textContent = data.length;
    }

    /**
     * Toggle Check All
     */
    function toggleCheckAll() {
        const checkAll = document.getElementById('checkAll');
        const checkboxes = document.querySelectorAll('.mahasiswa-check');
        
        checkboxes.forEach(cb => {
            cb.checked = checkAll.checked;
        });

        updateSummary();
    }

    /**
     * Update Summary
     */
    function updateSummary() {
        const checkboxes = document.querySelectorAll('.mahasiswa-check:checked');
        document.getElementById('totalSelected').textContent = checkboxes.length;
    }

    /**
     * Get Selected Mahasiswa
     */
    function getSelectedMahasiswa() {
        const checkboxes = document.querySelectorAll('.mahasiswa-check:checked');
        const selected = [];

        checkboxes.forEach(cb => {
            const index = cb.getAttribute('data-index');
            selected.push(mahasiswaData[index]);
        });

        return selected;
    }

    /**
     * Simpan Draft
     */
    async function simpanDraft() {
        const yudisiumId = document.getElementById('yudicium_id').value;
        const periode = document.getElementById('periode').value;
        const selectedMahasiswa = getSelectedMahasiswa();

        if (!yudisiumId) {
            alert('Yudisium ID harus diisi!');
            return;
        }

        if (selectedMahasiswa.length === 0) {
            alert('Pilih minimal 1 mahasiswa!');
            return;
        }

        if (!confirm(`Simpan ${selectedMahasiswa.length} mahasiswa sebagai draft?`)) {
            return;
        }

        try {
            showLoading();

            const response = await fetch('/yudisium/simpan-draft', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    yudicium_id: yudisiumId,
                    periode: periode,
                    mahasiswa: selectedMahasiswa
                })
            });

            const result = await response.json();

            if (result.success) {
                let message = result.message;
                
                if (result.api_success && result.api_success.length > 0) {
                    message += `\nAPI Success: ${result.api_success.length} mahasiswa`;
                }
                
                if (result.db_success && result.db_success.length > 0) {
                    message += `\nDB Success: ${result.db_success.length} mahasiswa`;
                }
                
                if (result.errors && result.errors.length > 0) {
                    message += `\nErrors: ${result.errors.length}`;
                    console.error('Errors:', result.errors);
                }

                showNotification('success', message);
            } else {
                showNotification('error', result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('error', 'Terjadi kesalahan saat menyimpan draft');
        } finally {
            hideLoading();
        }
    }

    /**
     * Get Draft
     */
    async function getDraft() {
        const yudisiumId = document.getElementById('yudicium_id').value;
        const periode = document.getElementById('periode').value;

        if (!yudisiumId) {
            alert('Yudisium ID harus diisi!');
            return;
        }

        try {
            showLoading();

            const response = await fetch(
                `/yudisium/get-draft?yudicium_id=${yudisiumId}&periode=${periode}&source=${currentSource}`,
                {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                }
            );

            const result = await response.json();

            if (result.success) {
                mahasiswaData = result.data;
                renderTable(result.data);
                updateSummary();
                showNotification('success', 'Data draft berhasil dimuat');
            } else {
                showNotification('error', result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('error', 'Terjadi kesalahan saat mengambil draft');
        } finally {
            hideLoading();
        }
    }

    /**
     * Tetapkan Yudisium
     */
    async function tetapkanYudisium() {
        const yudisiumId = document.getElementById('yudicium_id').value;
        const periode = document.getElementById('periode').value;

        if (!yudisiumId) {
            alert('Yudisium ID harus diisi!');
            return;
        }

        if (!confirm('Tetapkan yudisium? Data akan menjadi FINAL dan tidak dapat diubah!')) {
            return;
        }

        try {
            showLoading();

            const response = await fetch('/yudisium/tetapkan-yudisium', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    yudicium_id: yudisiumId,
                    periode: periode,
                    source: currentSource
                })
            });

            const result = await response.json();

            if (result.success) {
                showNotification('success', result.message);
                
                // Refresh data
                setTimeout(() => {
                    filterMahasiswa();
                }, 1500);
            } else {
                showNotification('error', result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('error', 'Terjadi kesalahan saat menetapkan yudisium');
        } finally {
            hideLoading();
        }
    }

    /**
     * Show Loading
     */
    function showLoading() {
        // Implement your loading indicator
        console.log('Loading...');
    }

    /**
     * Hide Loading
     */
    function hideLoading() {
        // Implement your loading indicator
        console.log('Loading complete');
    }

    /**
     * Show Notification
     */
    function showNotification(type, message) {
        // Implement your notification system (e.g., SweetAlert, Toastr)
        if (type === 'success') {
            alert('✓ ' + message);
        } else {
            alert('✗ ' + message);
        }
    }
</script>
@endpush
@endsection
