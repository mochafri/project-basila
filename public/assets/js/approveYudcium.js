document.addEventListener('DOMContentLoaded', async () => {
    const fakultasSelect = document.getElementById('fakultas');
    const filterButton = document.getElementById('filterButton');
    const tbody = document.querySelector('#selection-table tbody');

    console.log('popup-body?', document.getElementById('popup-body'));

    try {
        const res = await fetch(routes.showFaculties);

        if (!res.ok) {
            throw new Error('Token exp : ' + res.statusText);
        }

        const data = await res.json();
        console.log("Data fakultas : ", data);

        if (data.status === "success" && Array.isArray(data.data)) {
            data.data.forEach(fakultas => {
                const opt = document.createElement('option');
                opt.value = fakultas.facultyid;
                opt.textContent = fakultas.facultyname;
                fakultasSelect.appendChild(opt);
            });
        }
    } catch (err) {
        console.error(err);
    }

    filterButton.addEventListener('click', async (e) => {
        e.preventDefault();

        console.log("Clicked");

        try {
            const parseId = parseInt(fakultasSelect.value);

            if (!parseId) {
                alert('Pilih Fakultas terlebih dahulu');
                return;
            }

            const res = await fetch(routes.filterYudisium, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    fakultas_id: parseId
                })
            });

            if (!res.ok) {
                throw new Error('Token exp : ' + res.statusText);
            }

            const data = await res.json();

            tbody.innerHTML = '';
            if (Array.isArray(data.data) && data.data.length > 0) {
                data.data.forEach((yud, idx) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${idx + 1}</td>
                        <td>${yud.no_yudicium}</td>
                        <td>${yud.periode}</td>
                        <td>${yud.fakultasname}</td>
                        <td>${yud.prodiname}</td>
                        <td>${yud.total_mhs}</td>
                        <td>
                            <button
                                class="btn-popup w-8 h-8 bg-primary-50 dark:bg-primary-600/10 text-primary-600 dark:text-primary-400 rounded-full inline-flex items-center justify-center"
                                data-id="${yud.id}">
                                <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
                            </button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            } else if (Array.isArray(data.data) && data.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center">Tidak ada data</td></tr>';
            }

            console.log("Data yudisium : ", data);
        } catch (err) {
            console.error(err);
        }
    });
});

