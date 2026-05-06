// file: public/assets/js/data-tables.js
document.addEventListener('DOMContentLoaded', function () {

    function initTable(tableId, customOptions = {}) {
        const tableEl = document.getElementById(tableId);
        if (!tableEl) return null;

        // Default options
        const defaultOptions = {
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
        };

        // Merge custom options with defaults
        const options = { ...defaultOptions, ...customOptions };

        const table = new simpleDatatables.DataTable(`#${tableId}`, options);

        return table;
    }

    // Check if we're on index7 page (update-yudisium route)
    const isIndex7 = window.location.pathname.includes('update-yudisium');

    if (isIndex7) {
        // Index7: Enable pagination with custom settings
        window.selectionTable = initTable("selection-table", {
            perPageSelect: [10, 25, 50, 100],
            perPage: 10,
            labels: {
                placeholder: "Search for a user...",
                noRows: "Tidak ada data",
                info: "Showing {start} to {end} of {rows} entries"
            }
        });
    } else {
        // Other pages: Use default (no pagination)
        window.selectionTable = initTable("selection-table");
    }

    const popupTable = initTable("popup-table");
});
