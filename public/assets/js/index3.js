

document.addEventListener('DOMContentLoaded', async (e) => {
    const fakultasSelect = document.getElementById('fakultas');
    const prodiSelect = document.getElementById('prodi');
    const form = document.getElementById('filterForm');
    const tbody = document.querySelector('#selection-table tbody');
    const totalEligibleSpan = document.getElementById('totalEligible');
    const totalTidakEligibleSpan = document.getElementById('totalTidakEligible');

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

