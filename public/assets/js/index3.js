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
    let mahasiswaList = [];

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
        const totalCheckbox = qsa('.row-checkbox').length;
        const checkedCheckbox = qsa('.row-checkbox:checked').length;

        totalDipilihSpan.textContent = checkedCheckbox;
        totalTidakDipilihSpan.textContent = totalCheckbox - checkedCheckbox;
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

    // ======================================================
    // RENDER 1 ROW MAHASISWA
    // ======================================================
    function renderRow(mhs, idx) {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="text-center">
                <input type="checkbox"
                    class="row-checkbox w-4 h-4 accent-red-600 cursor-pointer"
                    value="${mhs.nim}"
                checked>
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
                    prodi: prodiSelect.value
                })
            });

            const data = await res.json();
            mahasiswaList = data.mahasiswa || [];

            tbody.innerHTML = '';

            if (mahasiswaList.length === 0) {
                tbody.innerHTML = `<tr><td colspan="10" class="text-center">Tidak ada data</td></tr>`;
                updateSelectionCount();
                return;
            }

            mahasiswaList.forEach((mhs, idx) => renderRow(mhs, idx));
            checkAll.checked = true;
            updateSelectionCount();

        } catch (err) {
            console.error("Error fetching mahasiswa:", err);
        }
    });

    // ======================================================
    // CHECK ALL CHECKBOXES
    // ======================================================
    checkAll.addEventListener('change', () => {
        qsa('.row-checkbox').forEach(cb => (cb.checked = checkAll.checked));
        updateSelectionCount();
    });

    // Sync checkAll when individual checkbox clicked
    document.addEventListener('change', (e) => {
        if (e.target.classList.contains('row-checkbox')) {
            const all = qsa('.row-checkbox');
            const checked = qsa('.row-checkbox:checked');

            checkAll.checked = (all.length === checked.length);

            updateSelectionCount();
        }
    });

    // ======================================================
    // DETAIL MODAL
    // ======================================================
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-detail');
        if (!btn) return;

        const nim = btn.dataset.nim;
        const mhs = mahasiswaList.find(m => m.nim == nim);
        if (!mhs) return;

        // Show modal
        qs("#infoDetailMahasiswa").classList.remove("hidden");

        // Identitas
        setHTML("dm-nama", mhs.name);
        setHTML("dm-nim", mhs.nim);
        setHTML("dm-prodi", mhs.prodi);
        setHTML("dm-fakultas", mhs.fakultas);

        // Icon helper
        const icon = (id, val) => setHTML(id, val ? "✔️" : "❌");

        icon("dm-study_period_icon", mhs.study_period >= 1);
        icon("dm-semester_lulus_icon", mhs.SMT_CURRENT >= 1);
        icon("dm-ipk_icon", mhs.ipk >= 0);
        icon("dm-sks_icon", mhs.pass_sks >= 1);
        icon("dm-mk_icon", mhs.STATUS);
        icon("dm-bahasa_icon", mhs.BAHASA_ASING);
        icon("dm-publikasi_icon", mhs.PUBLIKASI);
        icon("dm-tak_icon", mhs.TAK);
        icon("dm-administratif_icon", mhs.ADMINISTRATIF);
        icon("dm-bpp_icon", mhs.BPP);
        icon("dm-oplib_icon", mhs.OPENLIB);
        icon("dm-sanksi_icon", mhs.SANKSI);
    });

    // Close modal
    qs("#closeDetailModal").addEventListener('click', () => {
        qs("#infoDetailMahasiswa").classList.add("hidden");
    });

});
