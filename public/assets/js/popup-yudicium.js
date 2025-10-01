document.addEventListener("DOMContentLoaded", () => {
    const popup = document.getElementById("popup");
    const popupBody = document.getElementById("popup-body");
    const closeButton = document.getElementById("popup-close");

    if (!popup || !popupBody || !closeButton) {
        console.error("Popup elements not found")
        return;
    }

    document.addEventListener("click", async (e) => {
        const button = e.target.closest(".btn-popup");
        if (!button) return;

        e.preventDefault();

        const yudId = button.dataset.id;

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
                            <td>${mhs.status}</td>
                        </tr>
                    `).join('')}
                `;
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
});
