

document.addEventListener('DOMContentLoaded', async (e) => {
    const fakultasSelect = document.getElementById('fakultas');
    const prodiSelect = document.getElementById('prodi');
    const form = document.getElementById('filterForm');
    const tbody = document.querySelector('#selection-table tbody');
    const totalEligibleSpan = document.getElementById('totalEligible');
    const totalTidakEligibleSpan = document.getElementById('totalTidakEligible');

    try {
        const res = await fetch(routes.showFaculties);
        const data = await res.json();

        if (!res.ok) {
            throw new Error('Token exp : ' + res.statusText);
        }

        if (data.status === "success" && Array.isArray(data.data)) {
            data.data.forEach(fakultas => {
                const opt = document.createElement('option');
                opt.value = fakultas.facultyid;
                opt.textContent = fakultas.facultyname;
                fakultasSelect.appendChild(opt);
            });
        }

    } catch (err) {
        console.error('Gagal memuat fakultas:', err);
        fakultasSelect.innerHTML = '<option value="">Gagal memuat data fakultas</option>';
    }

    // Ambil data prodi dari API
    fakultasSelect.addEventListener('change', async () => {
        const facultyId = fakultasSelect.value;
        prodiSelect.innerHTML = '<option value="">-- Pilih Program Studi --</option>';

        console.log("Clicked");

        if (!facultyId)
            return;

        try {
            const res = await fetch(`/faculties/${facultyId}`);
            const data = await res.json();

            if (data.success === "success" && Array.isArray(data.data)) {
                data.data.forEach(prodi => {
                    const opt = document.createElement('option');
                    opt.value = prodi.studyprogramid;
                    opt.textContent = prodi.studyprogramname;
                    prodiSelect.appendChild(opt);
                });
            }

        } catch (err) {
            console.error('Gagal memuat prodi:' + err);
            prodiSelect.innerHTML = '<option value="">Gagal memuat data prodi</option>';
        }
    });

    // Fetch data dari api academic lewat filter fakutlas dan prodi
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const facultyId = fakultasSelect.value;
        const prodiId = prodiSelect.value;

        console.log("Clicked");

        try {
            const res = await fetch(routes.filterMhs, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    fakultas: facultyId,
                    prodi: prodiId
                })
            });

            const data = await res.json();

            tbody.innerHTML = '';
            let totalEligible = 0;
            let totalTidakEligible = 0;

            if (Array.isArray(data.mahasiswa) && data.mahasiswa.length > 0) {
                data.mahasiswa.forEach((mhs, idx) => {
                    getMahasiswa(mhs, idx);

                    if (mhs.status === "Eligible") totalEligible++;
                    else totalTidakEligible++;
                });
            } else if (Array.isArray(data.mahasiswa) && data.mahasiswa.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center">Tidak ada data</td></tr>';
            }

            totalEligibleSpan.textContent = totalEligible;
            totalTidakEligibleSpan.textContent = totalTidakEligible;
        } catch (err) {
            console.error(err);
        }
    });
});

