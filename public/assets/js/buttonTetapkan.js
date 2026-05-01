document.addEventListener('DOMContentLoaded', async (e) => {
    const btnTetapkan = document.getElementById('btnTetapkan');

    const span = document.querySelector('.statusSpan');
    if (!span) return Swal.fire('Error', 'Data mahasiswa tidak ditemukan.', 'error');

    const fakultasId = span.dataset.fakultas;
    console.log("Fakultas ID:", fakultasId);

    // Handle Check All
    const checkAll = document.getElementById('checkAll');
    const totalDipilih = document.getElementById('totalDipilih');
    const totalTidakDipilih = document.getElementById('totalTidakDipilih');
    
    function updateCounts() {
        const checkboxes = document.querySelectorAll('.row-checkbox:not(:disabled)');
        const checked = document.querySelectorAll('.row-checkbox:checked:not(:disabled)');
        const total = checkboxes.length;
        const selected = checked.length;
        
        if (totalDipilih) totalDipilih.textContent = selected;
        if (totalTidakDipilih) totalTidakDipilih.textContent = total - selected;
        
        if (checkAll) {
            checkAll.checked = total > 0 && selected === total;
        }
    }

    if (checkAll) {
        checkAll.addEventListener('change', function() {
            // Hanya ubah checkbox yang tidak disabled
            const checkboxes = document.querySelectorAll('.row-checkbox:not(:disabled)');
            checkboxes.forEach(cb => cb.checked = checkAll.checked);
            updateCounts();
        });
    }

    // Attach event listeners to all individual checkboxes
    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('row-checkbox')) {
            updateCounts();
        }
    });
    
    // Initialize count on load
    updateCounts();

    // Listener Event buat button tetapkan
    btnTetapkan.addEventListener('click', async () => {
        const urlParams = new URLSearchParams(window.location.search);
        const id = urlParams.get('id');
        const parseId = parseInt(id);

        const selectedNims = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.dataset.nim);
        


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