document.addEventListener("DOMContentLoaded", function () {
    // Pastikan data dari Blade sudah ada
    if (typeof fakultasLabels === 'undefined' || typeof fakultasData === 'undefined') {
        console.error("Data fakultas belum tersedia");
        return;
    }

    // 🏷️ Mapping alias fakultas → singkatan
    const fakultasAlias = {
        "TEKNIK ELEKTRO": "FTE",
        "REKAYASA INDUSTRI": "FRI",
        "INFORMATIKA": "FIF",
        "EKONOMI DAN BISNIS": "FEB",
        "KOMUNIKASI DAN ILMU SOSIAL": "FKS",
        "INDUSTRI KREATIF": "FIK",
        "ILMU TERAPAN": "FIT",
        "DIREKTORAT KAMPUS SURABAYA": "Kampus Surabaya",
        "DIREKTORAT KAMPUS PURWOKERTO": "Kampus Purwokerto"
    };

    // 🔁 Ubah label fakultas dari API menjadi singkatan
    const fakultasLabelsAlias = fakultasLabels.map(label => fakultasAlias[label.toUpperCase()] || label);

    // Konfigurasi chart
    const options = {
        chart: {
            type: 'bar',
            height: 300,
            toolbar: { show: false }
        },
        series: [{
            name: 'Jumlah Yudisium',
            data: fakultasData  // ← data dari controller
        }],
        xaxis: {
            categories: fakultasLabelsAlias  // ← label sudah disingkat
        },
        colors: ['#facc15'],
        plotOptions: {
            bar: {
                borderRadius: 4,
                columnWidth: '40%'
            }
        },
        dataLabels: {
            enabled: true
        }
    };

    const chart = new ApexCharts(document.querySelector("#yudisiumBarChart"), options);
    chart.render();
});
