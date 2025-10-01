document.addEventListener('DOMContentLoaded', async () => {
    const fakultasSelect = document.getElementById('fakultas');
    const prodiSelect = document.getElementById('prodi');
    const form = document.getElementById('filterForm');
    const tbody = document.querySelector('#selection-table tbody');
    const totalEligibleSpan = document.getElementById('totalEligible');
    const totalTidakEligibleSpan = document.getElementById('totalTidakEligible');
    const btnTetapkan = document.getElementById('btnTetapkan');

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

    fakultasSelect.addEventListener('change', async () => {

        const facultyId = fakultasSelect.value;
        prodiSelect.innerHTML = '<option value="">-- Pilih Program Studi --</option>';

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

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const facultyId = fakultasSelect.value;
        const prodiId = prodiSelect.value;

        if (!facultyId || !prodiId) {
            alert('Pilih Fakultas dan Program Studi terlebih dahulu');
            return;
        }

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
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${idx + 1}</td>
                        <td>${mhs.nim}</td>
                        <td>${mhs.name}</td>
                        <td>${mhs.study_period} Semester</td>
                        <td>${mhs.pass_sks}</td>
                        <td>${mhs.ipk}</td>
                        <td>${mhs.predikat}</td>
                        <td>
                            <span
                                class="statusSpan ${mhs.status === "Eligible"
                                    ? "bg-success-100 text-success-600"
                                    : "bg-danger-100 text-danger-600"
                                } px-6 py-1.5 rounded-full font-medium text-sm inline-block cursor-pointer"
                                data-nim="${mhs.nim}"
                                data-status="${mhs.status}">
                                ${mhs.status}
                            </span>
                        </td>
                        <td>
                            ${mhs.alasan_status
                                    ? `<span class="text-xs text-gray-500">${mhs.alasan_status}</span>`
                                    : '-'
                                }
                        </td>
                        <td>
                            <a href="javascript:void(0)" class="w-8 h-8 bg-primary-50 text-primary-600 rounded-full inline-flex items-center justify-center">
                                <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
                            </a>
                        </td>
            `;
                    tbody.appendChild(tr);

                    if (mhs.status === "Eligible") totalEligible++;
                    else totalTidakEligible++;
                });
            }

            totalEligibleSpan.textContent = totalEligible;
            totalTidakEligibleSpan.textContent = totalTidakEligible;
        } catch (err) {
            console.error("Error : ", err);
        }
    });

    btnTetapkan.addEventListener('click', async (e) => {
        e.preventDefault();

        console.log("Tetapkan clicked");

        try {
            const facultyId = parseInt(fakultasSelect.value);
            const prodiId = parseInt(prodiSelect.value);

            if (!facultyId || !prodiId) {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Pilih Fakultas dan Program Studi terlebih dahulu.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'bg-red-600 text-white px-4 py-2 rounded-lg'
                    },
                })
                return;
            }

            const res = await fetch(routes.approveYudisium, {
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

            if (!res.ok) {
                throw new Error("Ini error nya : " + res.statusText);
            }

            const data = await res.json();

            if (data.success) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Yudisium berhasil ditetapkan.',
                    icon: 'success'
                })
            } else {
                Swal.fire({
                    title: 'Gagal!',
                    text: data.message || 'Terjadi kesalahan.',
                    icon: 'error'
                })
            }
        } catch (err) {
            console.log("Error : ", err);

            Swal.fire({
                title: 'Error!',
                text: 'Tidak bisa menetapkan yudisium.',
                icon: 'error',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'bg-red-600 text-white px-4 py-2 rounded-lg'
                },
            })
        }
    });

    tbody.addEventListener('click', (e) => {
        if (e.target.classList.contains('statusSpan')) {
            document.getElementById('modalNim').value = e.target.dataset.nim;
            document.getElementById('modalStatus').value = e.target.dataset.status;
            document.getElementById('modalAlasan').value = '';
            document.getElementById('statusModal').classList.remove('hidden');
        }
    });

    document.getElementById('closeModal').addEventListener('click', () => {
        document.getElementById('statusModal').classList.add('hidden');
    });

    document.getElementById('statusForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const nim = document.getElementById('modalNim').value;
        const status = document.getElementById('modalStatus').value;
        const alasan = document.getElementById('modalAlasan').value;

        try {
            const res = await fetch(routes.ubahStatus, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ nim, status, alasan })
            });

            const data = await res.json();
            if (data.success) {
                Swal.fire("Berhasil!", "Status berhasil diubah", "success");
                document.getElementById('statusModal').classList.add('hidden');
                form.dispatchEvent(new Event("submit"));
            } else {
                Swal.fire("Gagal!", data.message || "Terjadi kesalahan", "error");
            }
        } catch (err) {
            console.error(err);
            Swal.fire("Error!", "Tidak bisa mengubah status", "error");
        }
    });

    document.getElementById('closeModal').addEventListener('click', () => {
        document.getElementById('statusModal').classList.add('hidden');
    });

});
