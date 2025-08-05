document.addEventListener("DOMContentLoaded", function () {
    var options = {
        chart: {
            type: 'bar',
            height: 300,
            toolbar: { show: false }
        },
        series: [{
            name: 'Jumlah Yudisium',
            data: [5, 3, 8, 6, 5, 7, 6, 8, 6]
            
        }],
        xaxis: {
            categories: ['FTE', 'FRI', 'FIF', 'FEB', 'FKB', 'FIK', 'FIT', 'Kampus Surabaya', 'Kampus Purwokerto']
        },
        colors: ['#facc15'],
        plotOptions: {
            bar: {
                borderRadius: 4,
                columnWidth: '40%'
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#yudisiumBarChart"), options);
    chart.render();
});