document.addEventListener('DOMContentLoaded', async() => {
    const fakultasSelect = document.getElementById('fakultas');

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
});