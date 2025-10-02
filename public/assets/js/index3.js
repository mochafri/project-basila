document.addEventListener('DOMContentLoaded', async () => {
    const fakultasSelect = document.getElementById('fakultas');
    const prodiSelect = document.getElementById('prodi');
    const form = document.getElementById('filterForm');
    const tbody = document.querySelector('#selection-table tbody');
    const totalEligibleSpan = document.getElementById('totalEligible');
    const totalTidakEligibleSpan = document.getElementById('totalTidakEligible');
    const nomorYudisiumInput = document.getElementById('nomorYudisium');
    const btnTetapkan = document.getElementById('btnTetapkan');

    // 🔹 Load Fakultas
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

    // 🔹 Load Prodi berdasarkan fakultas
    fakultasSelect.addEventListener('change', async () => {
        const facultyId = fakultasSelect.value;
        prodiSelect.innerHTML = '<option value="">-- Pilih Program Studi --</option>';
        if (!facultyId) return;

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

    // 🔹 Submit filter mahasiswa
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

            document.querySelector('input[name="fakultas"]').value = facultyId;
            document.querySelector('input[name="prodi"]').value = prodiId;
            document.querySelector('input[name="total_mahasiswa"]').value = totalEligible;

        } catch (err) {
            console.error(err);
        }
    });

    // 🔹 Tetapkan Yudisium
btnTetapkan.addEventListener('click', async (e) => {
    e.preventDefault();

    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Yudisium akan ditetapkan untuk fakultas & prodi yang dipilih.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Tetapkan!',
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
                const facultyId = parseInt(fakultasSelect.value);
                const prodiId = parseInt(prodiSelect.value);

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
                        text: data.message || 'Yudisium berhasil ditetapkan.',
                        icon: 'success',
                        showCancelButton: false,
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'btn-ok'
                        }
                        
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.message || 'Terjadi kesalahan.',
                        icon: 'error',
                        showCancelButton: false,
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'btn-ok'
                        }
                    });
                }
            } catch (err) {
                console.log("Error:", err);
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat mengirim data.',
                    icon: 'error',
                    showCancelButton: false,
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'btn-ok'
                        }
                });
            }
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire({
                title: 'Dibatalkan',
                text: 'Penetapan yudisium dibatalkan.',
                icon: 'info',
                showCancelButton: false,
                confirmButtonText: 'OK',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn-ok'
                }
            });
        }
    });
});


    // Event delegation: klik span status
    tbody.addEventListener('click', (e) => {
        if (e.target.classList.contains('statusSpan')) {
            document.getElementById('modalNim').value = e.target.dataset.nim;
            document.getElementById('modalStatus').value = e.target.dataset.status;
            document.getElementById('modalAlasan').value = '';
            document.getElementById('statusModal').classList.remove('hidden');
        }
    });
    // Tutup modal
    document.getElementById('closeModal').addEventListener('click', () => {
        document.getElementById('statusModal').classList.add('hidden');
    });

    // Submit form modal
    document.getElementById('statusForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const nim = document.getElementById('modalNim').value;
        const status = document.getElementById('modalStatus').value;
        const alasan = document.getElementById('modalAlasan').value;

        try {
            const res = await fetch(routes.ubahStatus, { // ganti sesuai route update status
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ nim, status, alasan })
            });

            const data = await res.json();
            if (data.success) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Status berhasil diubah',
                    icon: 'success',
                    showCancelButton: false,
                    confirmButtonText: 'OK',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn-ok'
                    }
                });
                document.getElementById('statusModal').classList.add('hidden');
                form.dispatchEvent(new Event("submit")); 
            } else {
                Swal.fire("Gagal!", 
                    data.message || "Terjadi kesalahan", "error");
            }
        } catch (err) {
            console.error(err);
            Swal.fire("Error!", "Tidak bisa mengubah status", "error");
        }
    });
    // Tutup modal
    document.getElementById('closeModal').addEventListener('click', () => {
        document.getElementById('statusModal').classList.add('hidden');
    });
});
