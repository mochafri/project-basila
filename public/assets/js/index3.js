document.addEventListener('DOMContentLoaded', async () => {
    // ======================================================
    // DOM ELEMENTS
    // ======================================================
    const fakultasSelect = document.getElementById('fakultas');
    const prodiSelect = document.getElementById('prodi');
    const form = document.getElementById('filterForm');
    const tbody = document.querySelector('#selection-table tbody');
    const totalDipilihSpan = document.getElementById('totalDipilih');
    const totalTidakDipilihSpan = document.getElementById('totalTidakDipilih');
    const checkAll = document.getElementById('checkAll');

    // GLOBAL DATA
    window.mahasiswaList = [];

    // ======================================================
    // UTILITIES
    // ======================================================
    const setHTML = (id, value) => (document.getElementById(id).innerHTML = value);
    const qs = (sel) => document.querySelector(sel);
    const qsa = (sel) => document.querySelectorAll(sel);

    // ======================================================
    // UPDATE SUMMARY COUNTS
    // ======================================================
    function updateSelectionCount() {
        const total = window.mahasiswaList.length;
            const selected = window.mahasiswaList.filter(m => m.selected).length;

            totalDipilihSpan.textContent = selected;
            totalTidakDipilihSpan.textContent = total - selected;

            checkAll.checked = (selected === total);
    }


    // ======================================================
    // LOAD PRODI BY FAKULTAS
    // ======================================================
    fakultasSelect.addEventListener('change', async () => {
        const facultyId = fakultasSelect.value;

        prodiSelect.innerHTML = '<option value="">-- Pilih Program Studi --</option>';
        if (!facultyId) return;

        try {
            const res = await fetch(`/faculties/${facultyId}`);
            const data = await res.json();

            if (data.success === "success") {
                data.data.forEach(prodi => {
                    const opt = new Option(prodi.studyprogramname, prodi.studyprogramid);
                    prodiSelect.appendChild(opt);
                });
            }
        } catch (err) {
            console.error('Gagal memuat prodi:', err);
            prodiSelect.innerHTML = '<option value="">Gagal memuat data prodi</option>';
        }
    });
    const periodeSelect = document.getElementById('periodeSelect');
    
    function renderTable() {
        const tableEl = document.querySelector('#selection-table');
        if (!tableEl) return;

        // Jika sudah ada instance, hancurkan dulu supaya bersih
        if (window.selectionTable) {
            window.selectionTable.destroy();
        }

        // Inisialisasi ulang DataTable
        window.selectionTable = new simpleDatatables.DataTable('#selection-table', {
            searchable: true,
            perPageSelect: false,
            columns: [{ select: [0, 8], sortable: false }],
            labels: {
                placeholder: 'Search for a user...',
                noRows: 'Tidak ada data',
                info: ''
            }
        });

        if (window.mahasiswaList.length === 0) return;

        const headings = null; 
        const rows = window.mahasiswaList.map((mhs) => {
            const checkbox = `<input type="checkbox" class="row-checkbox w-4 h-4 accent-red-600 cursor-pointer" data-nim="${mhs.nim}" ${mhs.selected ? 'checked' : ''}>`;
            const detail   = `<button class="btn-detail" data-nim="${mhs.nim}"><iconify-icon icon="iconamoon:eye-light"></iconify-icon></button>`;
            return [checkbox, mhs.nim, mhs.name, mhs.study_period, mhs.pass_sks, mhs.ipk, mhs.predikat, mhs.status, detail];
        });

        window.selectionTable.insert({ headings, data: rows });
    }



    // ======================================================
    // RENDER 1 ROW MAHASISWA
    // ======================================================
    function renderRow(mhs, idx) {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="text-center">
                <input type="checkbox"
                    class="row-checkbox w-4 h-4 accent-red-600 cursor-pointer"
                    data-nim="${mhs.nim}"
                    ${mhs.selected ? 'checked' : ''}
                >
            </td>
            <td>${mhs.nim}</td>
            <td>${mhs.name}</td>
            <td>${mhs.study_period}</td>
            <td>${mhs.pass_sks}</td>
            <td>${mhs.ipk}</td>
            <td>${mhs.predikat}</td>
            <td>${mhs.status}</td>
            <td>
                <button class="btn-detail" data-nim="${mhs.nim}">
                    <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    }

    // ======================================================
    // FETCH FILTERED MAHASISWA
    // ======================================================
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

        try {
            const res = await fetch(routes.filterMhs, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": qs('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    fakultas: fakultasSelect.value,
                    prodi: prodiSelect.value,
                    periode: periodeSelect.value
                })
            });

            const data = await res.json();
            window.mahasiswaList = (data.mahasiswa || []).map(mhs => ({
                ...mhs,
                selected: true
            }));

            renderTable();
            updateSelectionCount();

        } catch (err) {
            console.error("Error fetching mahasiswa:", err);
        }
    });
}

    // ======================================================
    // CHECK ALL CHECKBOXES
    // ======================================================
    if (checkAll) {
        checkAll.addEventListener('change', () => {
            window.mahasiswaList.forEach(m => m.selected = checkAll.checked);
            renderTable();
            updateSelectionCount();
        });
    }

    // Sync checkAll when individual checkbox clicked
    document.addEventListener('change', (e) => {
        if (e.target.classList.contains('row-checkbox')) {
            if (!e.target.classList.contains('row-checkbox')) return;

            const nim = e.target.dataset.nim;
            const mhs = window.mahasiswaList.find(m => m.nim == nim);
            if (!mhs) return;

            mhs.selected = e.target.checked;

            updateSelectionCount();
        }
    });

    // ======================================================
    // DETAIL MODAL FADE HELPERS
    // ======================================================
    const detailModal = qs("#infoDetailMahasiswa");
    const detailContent = detailModal.querySelector('.transform');

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

    // ======================================================
    // DETAIL MODAL
    // ======================================================
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-detail');
        if (!btn) return;

        const nim = btn.dataset.nim;
        const mhs = window.mahasiswaList.find(m => m.nim == nim);
        if (!mhs) return;

        // Show modal
        openDetailModal();

        // Identitas
        setHTML("dm-nama", mhs.name);
        setHTML("dm-nim", mhs.nim);
        setHTML("dm-prodi", mhs.prodi);
        setHTML("dm-fakultas", mhs.fakultas);


        const isTrue = (val) =>
        val === true ||
        val === 1 ||
        val === "1" ||
        val === "YA" ||
        val === "LULUS" ||
        val === "VALID";

        // Icon helper
       const icon = (id, val) =>
                    setHTML(
                        id,
                        val
                        ? `<iconify-icon icon="mingcute:check-fill" class="text-green-500 text-lg w-24px h-24px "></iconify-icon>`
                        : `<iconify-icon icon="mingcute:close-fill" class="text-red-500 text-lg w-24px h-24px "></iconify-icon>`
                    );


        icon("dm-study_period_icon", mhs.study_period >= 1);
        icon("dm-semester_lulus_icon", mhs.SMT_CURRENT >= 1);
        icon("dm-ipk_icon", mhs.ipk >= 2.0);
        icon("dm-sks_icon", mhs.pass_sks >= 144);

        icon("dm-mk_icon", isTrue(mhs.STATUS));
        icon("dm-bahasa_icon", isTrue(mhs.BAHASA_ASING));
        icon("dm-publikasi_icon", isTrue(mhs.PUBLIKASI));
        icon("dm-tak_icon", isTrue(mhs.TAK));
        icon("dm-administratif_icon", isTrue(mhs.ADMINISTRATIF));
        icon("dm-bpp_icon", isTrue(mhs.BPP));
        icon("dm-oplib_icon", isTrue(mhs.OPENLIB));
        icon("dm-sanksi_icon", isTrue(mhs.SANKSI));

    });

    // Close modal button
    qs("#closeDetailModal").addEventListener('click', closeDetailModal);

    // Klik backdrop untuk close
    detailModal.addEventListener('click', (e) => {
        if (e.target === detailModal) {
            closeDetailModal();
        }
    });

    // --- Info Modal Logic ---
    const openInfoBtn  = document.getElementById("openInfoModal");
    const closeInfoBtn = document.getElementById("closeInfoModal");
    const infoMdl      = document.getElementById("infoModal");
    const infoCnt      = document.getElementById("infoModalContent");

    if (openInfoBtn && infoMdl) {
        const openInfo = () => {
            infoMdl.classList.remove('opacity-0', 'pointer-events-none');
            infoMdl.classList.add('opacity-100');
            if (infoCnt) {
                infoCnt.classList.remove('scale-95');
                infoCnt.classList.add('scale-100');
            }
        };

        const closeInfo = () => {
            infoMdl.classList.add('opacity-0', 'pointer-events-none');
            infoMdl.classList.remove('opacity-100');
            if (infoCnt) {
                infoCnt.classList.add('scale-95');
                infoCnt.classList.remove('scale-100');
            }
        };

        openInfoBtn.addEventListener('click', (e) => {
            e.preventDefault();
            openInfo();
        });

        if (closeInfoBtn) closeInfoBtn.addEventListener('click', closeInfo);
        infoMdl.addEventListener('click', (e) => {
            if (e.target === infoMdl) closeInfo();
        });
    }

});
