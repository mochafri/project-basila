window.faculty_Id = null;
window.prodi_Id = null;

document.addEventListener('DOMContentLoaded', async () => {
    const totalEligibleSpan = document.getElementById('totalEligible');
    const totalTidakEligibleSpan = document.getElementById('totalTidakEligible');

    try {
        const urlParams = new URLSearchParams(window.location.search);
        const id = urlParams.get('id');

        const res = await fetch(`/index3/${id}`);

        if (!res.ok)
            throw new Error('Failed to fetch');

        const data = await res.json();

        console.log("Data : ", data);

        let totalEligible = 0;
        let totalTidakEligible = 0;

        if (Array.isArray(data.data) && Array.isArray(data.data)) {
            data.data.forEach((mhs, idx) => {
                getMahasiswa(mhs, idx);

                if (mhs.status === "Eligible") totalEligible++;
                else totalTidakEligible++;

                faculty_Id = mhs.fakultas;
                prodi_Id = mhs.prodi;
            });
        }

        totalEligibleSpan.textContent = totalEligible;
        totalTidakEligibleSpan.textContent = totalTidakEligible;
    } catch (err) {
        console.error(err);
    }
});