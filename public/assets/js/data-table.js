// file: public/assets/js/data-tables.js
document.addEventListener('DOMContentLoaded', function () {

    function initTable(tableId) {
        const tableEl = document.getElementById(tableId);
        if (!tableEl) return null;

        const table = new simpleDatatables.DataTable(`#${tableId}`, {
            searchable: true,
            perPageSelect: false,
            columns: [
                { select: [0, 6], sortable: false }
            ],
            labels: {
                placeholder: "Search for a user...",
                noRows: "Tidak ada data",
                info: ""
            },
        });

        return table;
    }

    window.selectionTable = initTable("selection-table");

    const popupTable = initTable("popup-table");
});
