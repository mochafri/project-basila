document.addEventListener('DOMContentLoaded', async (e) => {
    const btnTetapkan = document.getElementById('btnTetapkan');

    const span = document.querySelector('.statusSpan');
    if (!span) return Swal.fire('Error', 'Data mahasiswa tidak ditemukan.', 'error');

    const fakultasId = span.dataset.fakultas;
    console.log("Fakultas ID:", fakultasId);

    // Store ALL checkbox states in memory (including disabled ones)
    const checkboxStates = new Map(); // { nim: { checked: boolean, disabled: boolean, source: string } }

    // Handle Check All
    const checkAll = document.getElementById('checkAll');
    const totalDipilih = document.getElementById('totalDipilih');
    const totalTidakDipilih = document.getElementById('totalTidakDipilih');
    
    // Initialize checkbox states from server data (ALL mahasiswa from all pages)
    function initializeStates() {
        // First, initialize from server data if available
        if (window.allMahasiswaData && Array.isArray(window.allMahasiswaData)) {
            console.log('Initializing from server data:', window.allMahasiswaData.length, 'mahasiswa');
            window.allMahasiswaData.forEach(mhs => {
                checkboxStates.set(mhs.nim, {
                    checked: mhs.checked,
                    disabled: mhs.disabled,
                    source: mhs.source || 'api',
                    initialChecked: mhs.checked // Track initial state
                });
            });
        } else {
            // Fallback: initialize from current DOM only
            console.log('Fallback: Initializing from DOM');
            document.querySelectorAll('.row-checkbox').forEach(cb => {
                const nim = cb.dataset.nim;
                if (nim) {
                    checkboxStates.set(nim, {
                        checked: cb.checked,
                        disabled: cb.disabled,
                        source: cb.dataset.source || 'api',
                        initialChecked: cb.checked
                    });
                }
            });
        }
        console.log('Initialized states:', checkboxStates.size, 'total checkboxes');
    }
    
    // Sync DOM checkboxes with stored states
    function syncCheckboxes() {
        document.querySelectorAll('.row-checkbox').forEach(cb => {
            const nim = cb.dataset.nim;
            if (nim && checkboxStates.has(nim)) {
                const state = checkboxStates.get(nim);
                cb.checked = state.checked;
            }
        });
    }
    
    function updateCounts() {
        // Count from ALL stored states (including disabled)
        const totalStates = checkboxStates.size;
        const selectedStates = Array.from(checkboxStates.values())
            .filter(state => state.checked)
            .length;
        
        console.log('Update counts - Total:', totalStates, 'Selected:', selectedStates);
        
        if (totalDipilih) totalDipilih.textContent = selectedStates;
        if (totalTidakDipilih) totalTidakDipilih.textContent = totalStates - selectedStates;
        
        // Check All should only consider non-disabled checkboxes
        const eligibleStates = Array.from(checkboxStates.values()).filter(state => !state.disabled);
        const eligibleSelected = eligibleStates.filter(state => state.checked).length;
        
        if (checkAll) {
            checkAll.checked = eligibleStates.length > 0 && eligibleSelected === eligibleStates.length;
        }
    }

    if (checkAll) {
        checkAll.addEventListener('change', function() {
            console.log('Check All clicked:', checkAll.checked);
            
            // Update only non-disabled states in memory
            checkboxStates.forEach((state, nim) => {
                if (!state.disabled) {
                    state.checked = checkAll.checked;
                }
            });
            
            // Update visible checkboxes (only non-disabled)
            document.querySelectorAll('.row-checkbox:not(:disabled)').forEach(cb => {
                cb.checked = checkAll.checked;
                const nim = cb.dataset.nim;
                if (nim && checkboxStates.has(nim)) {
                    checkboxStates.get(nim).checked = checkAll.checked;
                }
            });
            
            updateCounts();
        });
    }

    // Attach event listeners to all individual checkboxes
    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('row-checkbox')) {
            const nim = e.target.dataset.nim;
            if (nim && checkboxStates.has(nim)) {
                checkboxStates.get(nim).checked = e.target.checked;
                console.log('Checkbox changed:', nim, e.target.checked);
            }
            updateCounts();
        }
    });
    
    // Listen for DataTable page changes using event delegation
    let lastPageContent = '';
    
    function checkForPageChange() {
        const table = document.querySelector('#selection-table tbody');
        if (table) {
            const currentContent = table.innerHTML;
            if (currentContent !== lastPageContent) {
                lastPageContent = currentContent;
                console.log('Page changed detected');
                
                // Add new checkboxes to state (including disabled ones)
                document.querySelectorAll('.row-checkbox').forEach(cb => {
                    const nim = cb.dataset.nim;
                    if (nim && !checkboxStates.has(nim)) {
                        checkboxStates.set(nim, {
                            checked: cb.checked,
                            disabled: cb.disabled,
                            source: cb.dataset.source || 'api',
                            initialChecked: cb.checked
                        });
                    }
                });
                
                syncCheckboxes();
                updateCounts();
            }
        }
    }
    
    // Use setInterval to check for page changes (fallback if MutationObserver doesn't work)
    setInterval(checkForPageChange, 500);
    
    // Also use MutationObserver for immediate detection
    const table = document.querySelector('#selection-table');
    if (table) {
        const observer = new MutationObserver(() => {
            checkForPageChange();
        });
        
        observer.observe(table, {
            childList: true,
            subtree: true
        });
    }
    
    // Initialize states and count on load
    initializeStates();
    updateCounts();

    // Listener Event buat button tetapkan
    btnTetapkan.addEventListener('click', async () => {
        const urlParams = new URLSearchParams(window.location.search);
        const id = urlParams.get('id');
        const parseId = parseInt(id);

        // Get selected NIMs from stored states (only checked ones)
        const selectedNims = Array.from(checkboxStates.entries())
            .filter(([nim, state]) => state.checked)
            .map(([nim, state]) => nim);
        
        // Get unchecked NIMs (yang awalnya checked tapi sekarang unchecked)
        const uncheckedMahasiswa = Array.from(checkboxStates.entries())
            .filter(([nim, state]) => state.initialChecked && !state.checked)
            .map(([nim, state]) => ({
                nim: nim,
                source: state.source
            }));
        
        console.log('Selected NIMs:', selectedNims.length, selectedNims);
        console.log('Unchecked Mahasiswa:', uncheckedMahasiswa.length, uncheckedMahasiswa);

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Apakah Anda yakin ingin menetapkan yudisium ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Konfirmasi',
            cancelButtonText: 'Batal',
            buttonsStyling: false,
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn-tetapkan',
                cancelButton: 'btn-batal'
            }
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const parseFaculty = parseInt(fakultasId);

                    // Step 1: Handle unchecked mahasiswa first
                    if (uncheckedMahasiswa.length > 0) {
                        console.log('Processing unchecked mahasiswa...');
                        
                        const uncheckRes = await fetch(routes.uncheckMahasiswa, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                yudicium_id: parseId,
                                unchecked_mahasiswa: uncheckedMahasiswa
                            })
                        });

                        if (!uncheckRes.ok) {
                            const errData = await uncheckRes.json().catch(() => ({}));
                            console.error('Uncheck error:', errData);
                            throw new Error(errData.message || "Gagal memproses mahasiswa yang di-uncheck");
                        }

                        const uncheckData = await uncheckRes.json();
                        console.log('Uncheck result:', uncheckData);
                    }

                    // Step 2: Proceed with tetapkan yudisium
                    const res = await fetch(routes.approveYudicium, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            id: parseId,
                            facultyId: parseFaculty,
                            mahasiswa_nims: selectedNims
                        })
                    });

                    if (res.status === 403) {
                        const errData = await res.json();
                        Swal.fire({
                            title: 'Gagal!',
                            text: errData.message || 'Tidak ada mahasiswa yang eligible.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            buttonsStyling: false,
                            customClass: { confirmButton: 'btn-ok' }
                        });
                        return;
                    }

                    if (!res.ok) {
                        const errData = await res.json().catch(() => ({}));
                        throw new Error(errData.message || "Terjadi kesalahan server");
                    }

                    const data = await res.json();
                    console.log("Data nya : ", data);

                    if (data.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: data.message || 'Yudisium berhasil ditetapkan.',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            buttonsStyling: false,
                            customClass: { confirmButton: 'btn-ok' }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '/dashboard/penetapan-yudisium';
                            }
                        });
                    }
                } catch (err) {
                    console.error("Error:", err);
                    Swal.fire({
                        title: 'Error!',
                        text: err.message || 'Terjadi kesalahan saat mengirim data.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'btn-ok' }
                    })
                }
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire({
                    title: 'Dibatalkan',
                    text: 'Penetapan yudisium dibatalkan.',
                    icon: 'info',
                    confirmButtonText: 'OK',
                    buttonsStyling: false,
                    customClass: { confirmButton: 'btn-ok' }
                })
            }
        });
    });
    
    // ======================================================
    // DETAIL MODAL FADE HELPERS
    // ======================================================
    const detailModal = document.querySelector("#infoDetailMahasiswa");
    if (detailModal) {
        const detailContent = detailModal.querySelector('.transform');
        const closeBtn = document.querySelector("#closeDetailModal");

        function openDetailModal() {
            detailModal.classList.remove('opacity-0', 'pointer-events-none');
            detailModal.classList.add('opacity-100');

            detailContent.classList.remove('scale-95');
            detailContent.classList.add('scale-100');
        }

        function closeDetailModal() {
            detailModal.classList.add('opacity-0', 'pointer-events-none');
            detailModal.classList.remove('opacity-100');

            detailContent.classList.add('scale-95');
            detailContent.classList.remove('scale-100');
        }

        if (closeBtn) closeBtn.addEventListener('click', closeDetailModal);

        detailModal.addEventListener('click', (e) => {
            if (e.target === detailModal) {
                closeDetailModal();
            }
        });

        // Handle detail button clicks
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-detail');
            if (!btn) return;

            openDetailModal();

            const setHTML = (id, value) => {
                const el = document.getElementById(id);
                if(el) el.innerHTML = value;
            };

            setHTML("dm-nama", btn.dataset.name);
            setHTML("dm-nim", btn.dataset.nim);
            setHTML("dm-prodi", btn.dataset.prodi || '-');
            setHTML("dm-fakultas", btn.dataset.fakultas || '-');

            const isTrue = (val) =>
                val === true ||
                val === 1 ||
                val === "1" ||
                val === "YA" ||
                val === "LULUS" ||
                val === "VALID";

            const icon = (id, val) =>
                setHTML(
                    id,
                    val
                        ? `<iconify-icon icon="mingcute:check-fill" class="text-green-500 text-lg w-24px h-24px "></iconify-icon>`
                        : `<iconify-icon icon="mingcute:close-fill" class="text-red-500 text-lg w-24px h-24px "></iconify-icon>`
                );

            const studyPeriod = parseFloat(btn.dataset.study) || 0;
            const ipk = parseFloat(btn.dataset.ipk) || 0;
            const sks = parseFloat(btn.dataset.sks) || 0;

            icon("dm-study_period_icon", studyPeriod >= 1);
            icon("dm-semester_lulus_icon", parseFloat(btn.dataset.smtmasuk) >= 1);
            icon("dm-ipk_icon", ipk >= 2.0);
            icon("dm-sks_icon", sks >= 144);

            icon("dm-mk_icon", isTrue(btn.dataset.status));
            icon("dm-bahasa_icon", isTrue(btn.dataset.basing));
            icon("dm-publikasi_icon", isTrue(btn.dataset.publikasi));
            icon("dm-tak_icon", isTrue(btn.dataset.tak));
            icon("dm-administratif_icon", isTrue(btn.dataset.admin));
            icon("dm-bpp_icon", isTrue(btn.dataset.bpp));
            icon("dm-oplib_icon", isTrue(btn.dataset.openlib));
            icon("dm-sanksi_icon", isTrue(btn.dataset.sanksi));
        });
    }

});