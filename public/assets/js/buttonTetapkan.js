document.addEventListener('DOMContentLoaded', async (e) => {
    const btnTetapkan = document.getElementById('btnTetapkan');

    const span = document.querySelector('.statusSpan');
    if (!span) return Swal.fire('Error', 'Data mahasiswa tidak ditemukan.', 'error');

    const fakultasId = span.dataset.fakultas;
    console.log("Fakultas ID:", fakultasId);

    // Listener Event buat button tetapkan
    btnTetapkan.addEventListener('click', async () => {
        const urlParams = new URLSearchParams(window.location.search);
        const id = urlParams.get('id');
        const parseId = parseInt(id);

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Apakah Anda yakin ingin menyimpan data ini?",
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
                    parseFaculty = parseInt(fakultasId);

                    const res = await fetch(routes.approveYudicium, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            id: parseId,
                            facultyId: parseFaculty
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
});