document.addEventListener('DOMContentLoaded', async () => {
    const fakultasSelect = document.getElementById('fakultas');
    const prodiSelect = document.getElementById('prodi');
    const form = document.getElementById('filterForm');
    const tbody = document.querySelector('#selection-table tbody');
    const totalEligibleSpan = document.getElementById('totalEligible');
    const totalTidakEligibleSpan = document.getElementById('totalTidakEligible');
    const btnTetapkan = document.getElementById('btnTetapkan');

    // Ambil data fakultas dari API
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
            } else if (Array.isArray(data.mahasiswa) && data.mahasiswa.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center">Tidak ada data</td></tr>';
            }

            totalEligibleSpan.textContent = totalEligible;
            totalTidakEligibleSpan.textContent = totalTidakEligible;
        } catch (err) {
            console.error(err);
        }
    });

    // Listener Event buat button tetapkan
    btnTetapkan.addEventListener('click', async (e) => {
        const status = document.getElementById('modalStatus').value;
        const alasan = document.getElementById('modalAlasan').value;

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
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            fakultas_id: facultyId,
                            prodi_id: prodiId,
                            status: status,
                            alasan: alasan
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
                        })
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

    // Modal atau pop up dari ubah status
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

    // Submit ke database buat status sama alasan nya
    document.getElementById('statusForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const status = document.getElementById('modalStatus').value;
        const alasan = document.getElementById('modalAlasan').value;
        const nim = document.getElementById('modalNim').value;

        try {
            const res = await fetch(routes.ubahStatus, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    status: status,
                    alasan: alasan,
                    nim: nim
                })
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
                })
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

    // // Submit by variable lewat request waktu tetapkan No Yudisium
    // document.getElementById('statusForm').addEventListener('submit', (e) => {
    //     e.preventDefault();

    //     const status = document.getElementById('modalStatus').value;
    //     const alasan = document.getElementById('modalAlasan').value;

    //     const statusSpans = tbody.querySelectorAll('.statusSpan');
    //     statusSpans.forEach(span => {
    //         span.textContent = status;
    //         span.className = `statusSpan ${status === "Eligible" ? "bg-success-100 text-success-600" : "bg-danger-100 text-danger-600"
    //             } px-6 py-1.5 rounded-full font-medium text-sm inline-block cursor-pointer`;

    //         const alasanCell = span.closest('tr').children[8];
    //         alasanCell.innerHTML = alasan ? `<span class="text-xs text-gray-500">${alasan}</span>` : '-';
    //     });

    //     document.getElementById('statusModal').classList.add('hidden');
    // });

    // Buat fetch semua mahasiswa dari API academic
    // try {
    //     const res = await fetch(routes.getAllMhs);
    //     const data = await res.json();

    //     if (res.ok && data.success && Array.isArray(data.mahasiswa)) {
    //         await renderMahasiswa(data.mahasiswa);
    //     } else {
    //         tbody.innerHTML = '<tr><td colspan="10" class="text-center text-red-500">Gagal memuat data mahasiswa</td></tr>';
    //     }
    // } catch (err) {
    //     console.error('Gagal memuat data default mahasiswa:', err);
    //     tbody.innerHTML = '<tr><td colspan="10" class="text-center text-red-500">Error memuat data</td></tr>';
    // }

    // async function renderMahasiswa(mahasiswa) {
    //     tbody.innerHTML = '';
    //     let totalEligible = 0;
    //     let totalTidakEligible = 0;

    //     if (Array.isArray(mahasiswa) && mahasiswa.length > 0) {
    //         mahasiswa.forEach((mhs, idx) => {
    //             const tr = document.createElement('tr');
    //             tr.innerHTML = `
    //             <td>${idx + 1}</td>
    //             <td>${mhs.nim}</td>
    //             <td>${mhs.name}</td>
    //             <td>${mhs.study_period} Semester</td>
    //             <td>${mhs.pass_sks}</td>
    //             <td>${mhs.ipk}</td>
    //             <td>${mhs.predikat}</td>
    //             <td>
    //                 <span
    //                     class="statusSpan ${mhs.status === "Eligible"
    //                     ? "bg-success-100 text-success-600"
    //                     : "bg-danger-100 text-danger-600"}
    //                         px-6 py-1.5 rounded-full font-medium text-sm inline-block cursor-pointer"
    //                     data-nim="${mhs.nim}"
    //                     data-status="${mhs.status}">
    //                     ${mhs.status}
    //                 </span>
    //             </td>
    //             <td>
    //                 ${mhs.alasan_status
    //                     ? `<span class="text-xs text-gray-500">${mhs.alasan_status}</span>`
    //                     : '-'}
    //             </td>
    //             <td>
    //                 <a href="javascript:void(0)" class="w-8 h-8 bg-primary-50 text-primary-600 rounded-full inline-flex items-center justify-center">
    //                     <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
    //                 </a>
    //             </td>
    //         `;
    //             tbody.appendChild(tr);

    //             if (mhs.status === "Eligible") totalEligible++;
    //             else totalTidakEligible++;
    //         });
    //     } else {
    //         tbody.innerHTML = '<tr><td colspan="10" class="text-center">Tidak ada data</td></tr>';
    //     }

    //     totalEligibleSpan.textContent = totalEligible;
    //     totalTidakEligibleSpan.textContent = totalTidakEligible;
    // }
});
