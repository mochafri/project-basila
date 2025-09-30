// file: public/assets/js/data-tables.js
document.addEventListener('DOMContentLoaded', function () {

    function initTable(tableId) {
        const tableEl = document.getElementById(tableId);
        if (!tableEl) return null;

        const table = new simpleDatatables.DataTable(`#${tableId}`, {
            searchable: true,
            perPageSelect: false,
            columns: [
                { select: [0, 6], sortable: false } // Kolom No & Status tidak sortable
            ],
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

        // Mark all rows as unselected
        table.data.data.forEach(data => data.selected = false);

        // Klik per row
        table.on("datatable.selectrow", (rowIndex, event) => {
            event.preventDefault();
            const row = table.data.data[rowIndex];
            row.selected = !row.selected;
            table.update();
        });

        return table;
    }

    const selectionTable = initTable("selection-table");

    const popupTable = initTable("popup-table");

    // Checkbox master untuk selection-table
    const masterCheckBox = document.getElementById("serial");
    if (masterCheckBox && selectionTable) {
        masterCheckBox.addEventListener("change", () => {
            const checked = masterCheckBox.checked;
            selectionTable.data.data.forEach(row => row.selected = checked);
            document.querySelectorAll("#selection-table tbody input[type=checkbox]").forEach(cb => cb.checked = checked);
            selectionTable.update();
        });
    }
});
