document.addEventListener('DOMContentLoaded', async () => {
    const btnUpdate  = document.getElementById('btnUpdate');
    const checkAll   = document.getElementById('checkAll');

    // ======================================================
    // TRACKING CHECKBOX STATE ACROSS PAGINATION
    // ======================================================
    // Store checkbox states globally (NIM -> checked status)
    window.checkboxStates = window.checkboxStates || {};
    
    // Initialize all checkboxes from backend data (ALL mahasiswa, not just visible)
    function initializeCheckboxStates() {
        // Use data from backend (passed via blade)
        if (window.allMahasiswaData && window.allMahasiswaData.length > 0) {
            console.log('Initializing from backend data');
            window.allMahasiswaData.forEach(mhs => {
                window.checkboxStates[mhs.nim] = mhs.checked; // Default true
            });
        } else {
            // Fallback: Get from DOM if backend data not available
            console.log('Fallback: Initializing from DOM');
            const allRows = document.querySelectorAll('#selection-table tbody tr');
            allRows.forEach(row => {
                const checkbox = row.querySelector('.row-checkbox');
                if (checkbox) {
                    const nim = checkbox.dataset.nim;
                    if (window.checkboxStates[nim] === undefined) {
                        window.checkboxStates[nim] = checkbox.checked;
                    }
                }
            });
        }
        
        console.log('Initialized checkbox states:', window.checkboxStates);
        console.log('Total mahasiswa:', Object.keys(window.checkboxStates).length);
        
        // Restore checkbox states for visible rows
        restoreCheckboxStates();
        updateCounts();
    }

    // ======================================================
    // CHECK ALL
    // ======================================================
    if (checkAll) {
        checkAll.addEventListener('change', function () {
            const isChecked = checkAll.checked;
            
            // Update ALL NIMs in global state (not just visible ones)
            Object.keys(window.checkboxStates).forEach(nim => {
                window.checkboxStates[nim] = isChecked;
            });
            
            // Update visible checkboxes
            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.checked = isChecked;
            });
            
            console.log('Check All clicked:', isChecked);
            updateCounts();
        });
    }

    function updateCounts() {
        // Count from global state (all pages)
        const allNims = Object.keys(window.checkboxStates);
        const total = allNims.length;
        const selected = allNims.filter(nim => window.checkboxStates[nim]).length;

        const dipilihEl      = document.getElementById('totalDipilih');
        const tidakDipilihEl = document.getElementById('totalTidakDipilih');

        if (dipilihEl)      dipilihEl.textContent      = selected;
        if (tidakDipilihEl) tidakDipilihEl.textContent = total - selected;

        console.log('Update counts - Total:', total, 'Selected:', selected);

        // Update checkAll state based on global state
        if (checkAll) {
            checkAll.checked = total > 0 && selected === total;
        }
    }

    // Track individual checkbox changes
    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('row-checkbox')) {
            const nim = e.target.dataset.nim;
            window.checkboxStates[nim] = e.target.checked;
            console.log('Checkbox changed:', nim, '=', e.target.checked);
            updateCounts();
        }
    });

    // ======================================================
    // RESTORE CHECKBOX STATES AFTER DOM CHANGES
    // ======================================================
    function restoreCheckboxStates() {
        document.querySelectorAll('.row-checkbox').forEach(cb => {
            const nim = cb.dataset.nim;
            if (window.checkboxStates[nim] !== undefined) {
                cb.checked = window.checkboxStates[nim];
            }
        });
        console.log('Restored checkbox states for visible rows');
    }

    // ======================================================
    // HANDLE PAGINATION - Use MutationObserver to detect DOM changes
    // ======================================================
    // Wait for DataTable to be initialized
    setTimeout(() => {
        if (window.selectionTable) {
            console.log('DataTable detected, setting up event listeners');
            
            // Listen to page change events
            window.selectionTable.on('datatable.page', function(page) {
                console.log('Page changed to:', page);
                setTimeout(() => {
                    restoreCheckboxStates();
                    updateCounts();
                }, 100);
            });
            
            // Listen to search events
            window.selectionTable.on('datatable.search', function(query, matched) {
                console.log('Search performed:', query);
                setTimeout(() => {
                    restoreCheckboxStates();
                    updateCounts();
                }, 100);
            });
            
            // Listen to sort events
            window.selectionTable.on('datatable.sort', function(column, direction) {
                console.log('Sort performed:', column, direction);
                setTimeout(() => {
                    restoreCheckboxStates();
                    updateCounts();
                }, 100);
            });
        } else {
            console.log('DataTable not found, using MutationObserver fallback');
        }
        
        // Fallback: Use MutationObserver to detect any table changes
        const tableBody = document.querySelector('#selection-table tbody');
        if (tableBody) {
            const observer = new MutationObserver(function(mutations) {
                // Check if rows were added/removed
                const hasRowChanges = mutations.some(mutation => 
                    mutation.type === 'childList' && 
                    (mutation.addedNodes.length > 0 || mutation.removedNodes.length > 0)
                );
                
                if (hasRowChanges) {
                    console.log('Table DOM changed, restoring states');
                    setTimeout(() => {
                        restoreCheckboxStates();
                        updateCounts();
                    }, 50);
                }
            });
            
            observer.observe(tableBody, {
                childList: true,
                subtree: true
            });
            
            console.log('MutationObserver set up for table changes');
        }
    }, 500); // Wait 500ms for DataTable to initialize

    // Init checkbox states on load
    initializeCheckboxStates();

    // ======================================================
    // TOMBOL HAPUS INDIVIDUAL
    // ======================================================
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-hapus-mhs');
        if (!btn) return;

        const nim  = btn.dataset.nim;
        const name = btn.dataset.name;

        Swal.fire({
            title: 'Hapus Mahasiswa?',
            text: `${name} (${nim}) akan dihapus dari daftar yudisium.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
            buttonsStyling: false,
            customClass: { confirmButton: 'btn-tetapkan', cancelButton: 'btn-batal' }
        }).then(async (result) => {
            if (!result.isConfirmed) return;

            const urlParams = new URLSearchParams(window.location.search);
            const yudiciumId = parseInt(urlParams.get('id'));

            try {
                const res = await fetch('/yudicium/hapus-mhs', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ nim, yudicium_id: yudiciumId })
                });

                const data = await res.json();
                if (data.success) {
                    // Remove from global state
                    delete window.checkboxStates[nim];
                    
                    // Hapus baris dari tabel
                    btn.closest('tr').remove();
                    
                    // Update counts
                    updateCounts();
                    
                    Swal.fire({
                        title: 'Dihapus!',
                        text: `${name} berhasil dihapus dari daftar.`,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'btn-ok' }
                    });
                } else {
                    throw new Error(data.message || 'Gagal menghapus');
                }
            } catch (err) {
                Swal.fire({ title: 'Error!', text: err.message, icon: 'error', confirmButtonText: 'OK', buttonsStyling: false, customClass: { confirmButton: 'btn-ok' } });
            }
        });
    });

    // ======================================================
    // TOMBOL DETAIL MAHASISWA (sama dengan index3/index5)
    // ======================================================
    const detailModal   = document.getElementById('infoDetailMahasiswa');
    const detailContent = detailModal?.querySelector('.transform');

    function openDetailModal() {
        if (!detailModal) return;
        detailModal.classList.remove('opacity-0', 'pointer-events-none');
        detailModal.classList.add('opacity-100');
        detailContent?.classList.remove('scale-95');
        detailContent?.classList.add('scale-100');
    }

    function closeDetailModal() {
        if (!detailModal) return;
        detailModal.classList.add('opacity-0', 'pointer-events-none');
        detailModal.classList.remove('opacity-100');
        detailContent?.classList.add('scale-95');
        detailContent?.classList.remove('scale-100');
    }

    document.getElementById('closeDetailModal')?.addEventListener('click', closeDetailModal);
    detailModal?.addEventListener('click', e => { if (e.target === detailModal) closeDetailModal(); });

    const setHTML = (id, value) => {
        const el = document.getElementById(id);
        if (el) el.innerHTML = value;
    };

    const isTrue = val =>
        val === true || val === 1 || val === '1' ||
        val === 'YA' || val === 'LULUS' || val === 'VALID';

    const icon = (id, val) => setHTML(id,
        val
            ? `<iconify-icon icon="mingcute:check-fill" class="text-green-500 text-lg"></iconify-icon>`
            : `<iconify-icon icon="mingcute:close-fill" class="text-red-500 text-lg"></iconify-icon>`
    );

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-detail-mhs');
        if (!btn) return;

        setHTML('dm-nama',    btn.dataset.name    || '-');
        setHTML('dm-nim',     btn.dataset.nim     || '-');
        setHTML('dm-prodi',   btn.dataset.prodi   || '-');
        setHTML('dm-fakultas',btn.dataset.fakultas|| '-');

        const studyPeriod = parseFloat(btn.dataset.study) || 0;
        const ipk         = parseFloat(btn.dataset.ipk)   || 0;
        const sks         = parseFloat(btn.dataset.sks)   || 0;

        icon('dm-study_period_icon',   studyPeriod >= 1);
        icon('dm-semester_lulus_icon', parseFloat(btn.dataset.smtmasuk || 0) >= 1);
        icon('dm-ipk_icon',            ipk >= 2.0);
        icon('dm-sks_icon',            sks >= 144);
        icon('dm-mk_icon',             isTrue(btn.dataset.status));
        icon('dm-bahasa_icon',         isTrue(btn.dataset.basing));
        icon('dm-publikasi_icon',      isTrue(btn.dataset.publikasi));
        icon('dm-tak_icon',            isTrue(btn.dataset.tak));
        icon('dm-administratif_icon',  isTrue(btn.dataset.admin));
        icon('dm-bpp_icon',            isTrue(btn.dataset.bpp));
        icon('dm-oplib_icon',          isTrue(btn.dataset.openlib));
        icon('dm-sanksi_icon',         isTrue(btn.dataset.sanksi));

        openDetailModal();
    });

    // ======================================================
    // TETAPKAN ULANG — kirim NIM yang dicentang, hapus yang tidak
    // ======================================================
    btnUpdate.addEventListener('click', async () => {
        const urlParams  = new URLSearchParams(window.location.search);
        const id         = parseInt(urlParams.get('id'));

        // Get checked/unchecked NIMs from global state (all pages)
        const allNims = Object.keys(window.checkboxStates);
        const checkedNims = allNims.filter(nim => window.checkboxStates[nim]);
        const uncheckedNims = allNims.filter(nim => !window.checkboxStates[nim]);

        if (checkedNims.length === 0) {
            Swal.fire({
                title: 'Perhatian!',
                text: 'Pilih minimal satu mahasiswa untuk ditetapkan.',
                icon: 'warning',
                confirmButtonText: 'OK',
                buttonsStyling: false,
                customClass: { confirmButton: 'btn-ok' }
            });
            return;
        }

        Swal.fire({
            title: 'Tetapkan Ulang?',
            html: `<b>${checkedNims.length}</b> mahasiswa akan ditetapkan.<br>
                   ${uncheckedNims.length > 0 ? `<b>${uncheckedNims.length}</b> mahasiswa tidak dicentang akan dihapus dari daftar.` : ''}`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Konfirmasi',
            cancelButtonText: 'Batal',
            buttonsStyling: false,
            reverseButtons: true,
            customClass: { confirmButton: 'btn-tetapkan', cancelButton: 'btn-batal' }
        }).then(async (result) => {
            if (!result.isConfirmed) return;

            try {
                const res = await fetch(routes.updateYudicium, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        id,
                        mahasiswa_nims: checkedNims,       // yang dicentang → tetap Eligible
                        remove_nims: uncheckedNims          // yang tidak dicentang → hapus dari DB
                    })
                });

                if (res.status === 403) {
                    const err = await res.json();
                    Swal.fire({ title: 'Gagal!', text: err.message, icon: 'error', confirmButtonText: 'OK', buttonsStyling: false, customClass: { confirmButton: 'btn-ok' } });
                    return;
                }

                if (!res.ok) {
                    const err = await res.json().catch(() => ({}));
                    throw new Error(err.message || 'Terjadi kesalahan server');
                }

                const data = await res.json();
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message || 'Yudisium berhasil ditetapkan ulang.',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'btn-ok' }
                    }).then(r => {
                        if (r.isConfirmed) window.location.href = '/dashboard/penetapan-yudisium';
                    });
                }
            } catch (err) {
                Swal.fire({ title: 'Error!', text: err.message, icon: 'error', confirmButtonText: 'OK', buttonsStyling: false, customClass: { confirmButton: 'btn-ok' } });
            }
        });
    });
});
