document.addEventListener('DOMContentLoaded', () => {
    const btnSimpan = document.getElementById('btnSimpan');
    const fakultasSelect = document.getElementById('fakultas');
    const prodiSelect = document.getElementById('prodi');

    // Listener Event buat button simpan
    btnSimpan.addEventListener('click', async () => {
        const status = document.getElementById('modalStatus').value || '';
        const alasan = document.getElementById('modalAlasan').value || '';

        console.log("Clicked");

        console.log("Fakultas : ", fakultasSelect.value, "Prodi : ", prodiSelect.value);
        console.log("Status : ", status, "Alasan : ", alasan);

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
                // Show loading SweetAlert
                Swal.fire({
                    title: 'Menyimpan Data...',
                    html: 'Sedang menyimpan data yudisium, mohon tunggu sebentar',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    facultyId = parseInt(fakultasSelect.value);
                    prodiId = parseInt(prodiSelect.value);

                    const selectedNims = window.mahasiswaList ? window.mahasiswaList.filter(m => m.selected).map(m => m.nim) : [];
                    
                    // Deteksi source dari data mahasiswa pertama
                    const source = window.mahasiswaList && window.mahasiswaList.length > 0 
                        ? (window.mahasiswaList[0].source || 'api') 
                        : 'api';

                    const res = await fetch(routes.saveDraft, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            fakultas_id: facultyId,
                            prodi_id: prodiId,
                            alasan: alasan || null,
                            status: status || null,
                            mahasiswa_nims: selectedNims,
                            source: source // 'api' atau 'database'
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