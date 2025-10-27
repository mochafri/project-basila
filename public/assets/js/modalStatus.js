document.addEventListener('DOMContentLoaded', async () => {
    const tbody = document.querySelector('#selection-table tbody');
    const form = document.getElementById('filterForm');

    // Modal atau pop up dari ubah status
    tbody.addEventListener('click', (e) => {
        if (e.target.classList.contains('statusSpan')) {
            document.getElementById('modalNim').value = e.target.dataset.nim;
            document.getElementById('modalStatus').value = e.target.dataset.status;
            document.getElementById('modalAlasan').value = e.target.dataset.alasan || '';
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

        console.log("Nim : ", nim);

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

                if(form != null) {
                    form.dispatchEvent(new Event("submit"));
                }
            } else {
                Swal.fire("Gagal!",
                    data.message || "Terjadi kesalahan", "error");
            }
        } catch (err) {
            console.error(err);
            Swal.fire("Error!", "Tidak bisa mengubah status", "error");
        }
    });
});