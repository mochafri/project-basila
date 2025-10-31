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
            placeholder: "Search...",
            perPage: "{select} entries per page",
            noRows: "", // kosongkan teks “No entries found”
            info: "",
        },
            
            rowRender: (row, tr) => {
                if (!tr.attributes) tr.attributes = {};
                if (!tr.attributes.class) tr.attributes.class = "";

                if (row.selected) {
                    tr.attributes.class += " selected";
                } else {
                    tr.attributes.class = tr.attributes.class.replace(" selected", "");
                }

                return tr;
            }
        });

        return table;
    }

    const selectionTable = initTable("selection-table");

    const popupTable = initTable("popup-table");
});
