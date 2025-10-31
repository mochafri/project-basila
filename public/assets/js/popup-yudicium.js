document.addEventListener("DOMContentLoaded", () => {
    const popup = document.getElementById("popup");
    const popupBody = document.getElementById("popup-body");
    const closeButton = document.getElementById("popup-close");
    const approvalSelect = document.getElementById("approval");
    const catatan = document.getElementById('catatan');
    const btnApprove = document.getElementById('btn-simpan');

    if (!popup || !popupBody || !closeButton) {
        console.log("Popup : ", popup);
        console.log("Popup Body : ", popupBody);
        console.log("Close Button : ", closeButton);
        console.error("Popup elements not found")
        return;
    }

    document.addEventListener("click", async (e) => {

        const button = e.target.closest(".btn-popup");
        if (!button) return;

        e.preventDefault();
        yudId = button.dataset.id;

        console.log("ID : ", yudId);

        try {
            const res = await fetch(`/yudicium/${yudId}/mahasiswa`);
            if (!res.ok) throw new Error('Failed to fetch');

            const data = await res.json();
            console.log("Data:", data);

            if (Array.isArray(data.mahasiswa) && data.mahasiswa.length > 0) {
                popupBody.innerHTML = `
                    ${data.mahasiswa.map((mhs, index) => `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${mhs.nim}</td>
                            <td>${mhs.name}</td>
                            <td>${mhs.study_period}</td>
                            <td>${mhs.pass_sks}</td>
                            <td>${mhs.ipk}</td>
                            <td>${mhs.predikat}</td>
                            <td>${mhs.status && mhs.status.trim() !== ""
                        ? mhs.status
                        : mhs.status_otomatis
                    }</td>
                            <td>${mhs.alasan_status || '-'}</td>
                        </tr>
                    `).join('')}
                `;
            }

            if (document.getElementById('approve-yudisium')) {
                if (Array.isArray(data.yudisium) && data.yudisium.length > 0) {
                    const status = data.yudisium[0].approval_status;

                    const option = ['Waiting', 'Approved', 'Rejected'];

                    approvalSelect.innerHTML = option.map(opt =>
                        `<option value="${opt}" ${opt} === ${status} ? 'selected' : ''}>
                            ${opt.charAt(0).toUpperCase() + opt.slice(1)}
                        </option>`
                    ).join('');
                }
            }

            popup.classList.remove("hidden");
        } catch (err) {
            console.error(err);
            popupBody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center">Gagal memuat data</td>
                </tr>
            `;
            popup.classList.remove("hidden");
        }
    });

    closeButton.addEventListener("click", () => {
        popup.classList.add("hidden");
    });

    popup.addEventListener("click", (e) => {
        if (e.target === popup)
            popup.classList.add("hidden");
    });

    btnApprove.addEventListener('click', async (e) => {
        e.preventDefault();
        const status = approvalSelect.value;
        const alasan = catatan.value;

        console.log("Clicked");

        if (!yudId) {
            console.error("Yudisium ID not found");
            return;
        }

        try {
            const res = await fetch(routes.updateYudisium, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    yudisium_id: parseInt(yudId),
                    approval_status: status,
                    catatan: alasan
                })
            });

            if (!res.ok) {
                throw new Error('Failed to fetch');
            }

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
                }).then((result) => {
                    if(result.isConfirmed) {
                        window.location.reload();
                    }
                });
                document.getElementById('popup').classList.add('hidden');
            } else {
                Swal.fire({
                    title: 'Gagal!',
                    text: data.message || "Terjadi kesalahan error",
                    confirmButtonText: 'OK',
                    icon: 'error',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn-ok'
                    }
                });
            }
        } catch (err) {
            console.error(err);
            Swal.fire({
                title: 'Error!',
                text: err.message || 'Terjadi kesalahan saat mengirim data.',
                icon: 'error',
                confirmButtonText: 'OK',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn-ok'
                }
            })
        }
    });
});
