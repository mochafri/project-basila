let counter = 1; 
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-yudisium');
    const fakultasSelect = document.getElementById('fakultas');
    const prodiSelect = document.getElementById('prodi');
    const nomorInput = document.getElementById('nomor_yudisium');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // pakai text, bukan value
        const fakultas = fakultasSelect.options[fakultasSelect.selectedIndex].text;
        const prodi = prodiSelect.value;

        if (!fakultas || !prodi) {
            Swal.fire("Oops!", "Pilih fakultas dan prodi dulu.", "warning");
            return;
        }

        const today = new Date();
        const tahun = today.getFullYear();
        const kodeFakultas = {
            "ILMU TERAPAN": "IL-DEK",
            "REKAYASA INDUSTRI": "RI-DEK",
            "INDUSTRI KREATIF": "IK-DEK",
            "TEKNIK ELEKTRO": "TE-DEK",
            "ILMU KOMUNIKASI": "IK-DEK",
            "INFORMATIKA": "IF-DEK",
            "EKONOMI DAN BISNIS": "EB-DEK",
            "KOMUNIKASI DAN ILMU SOSIAL": "KI-DEK",
            "DIREKTORAT KAMPUS SURABAYA": "DKS-DEK",
            "DIREKTORAT KAMPUS PURWOKERTO": "DKP-DEK",
        };

        const kode = kodeFakultas[fakultas];
        const nomorUrut = String(counter++).padStart(2, '0'); 

        nomorInput.value = `${nomorUrut}/AKD15/${kode}/${tahun}`;
    });
});
