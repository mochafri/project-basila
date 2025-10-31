document.addEventListener("DOMContentLoaded", function () {
    if (typeof fakultasLabels === 'undefined' || typeof fakultasData === 'undefined') {
        console.error("Data fakultas belum tersedia");
        return;
    }

    const fakultasAlias = {
        "TEKNIK ELEKTRO": "FTE",
        "REKAYASA INDUSTRI": "FRI",
        "INFORMATIKA": "FIF",
        "EKONOMI DAN BISNIS": "FEB",
        "KOMUNIKASI DAN ILMU SOSIAL": "FKS",
        "INDUSTRI KREATIF": "FIK",
        "ILMU TERAPAN": "FIT",
        "DIREKTORAT KAMPUS SURABAYA": "DKS",
        "DIREKTORAT KAMPUS PURWOKERTO": "DKP"
    };

    // Ubah label API ke singkatan
    const fakultasLabelsAlias = fakultasLabels.map(label => fakultasAlias[label.toUpperCase()] || label);

    // Gabungkan label & data → biar bisa diurutkan
    const combined = fakultasLabelsAlias.map((label, i) => ({
        label: label,
        value: fakultasData[i]
    }));

    // Urutan yang diinginkan
    const desiredOrder = ["FTE", "FRI", "FIF", "FEB", "FKS", "FIK", "FIT", "DKS", "DKP"];

    // Urutkan data sesuai urutan di atas
    const sorted = desiredOrder
        .map(orderLabel => combined.find(item => item.label === orderLabel))
        .filter(Boolean); // hapus yang tidak ada

    // Pisahkan kembali ke 2 array
    const sortedLabels = sorted.map(item => item.label);
    const sortedData = sorted.map(item => item.value);

    // Konfigurasi chart
    const options = {
        chart: {
            type: 'bar',
            height: 300,
            toolbar: { show: false }
        },
        series: [{
            name: 'Jumlah Yudisium',
            data: sortedData
        }],
        xaxis: {
            categories: sortedLabels
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
