if (document.getElementById("selection-table")) {

    let multiSelect = false;
    let rowNavigation = false;
    let table = null;

    const resetTable = function () {
        if (table) {
            table.destroy();
        }

        const options = {
            searchable: true,
            perPageSelect: false,

            template: (props) => `
            <div class='${props.classes.left}'>
                    ${props.searchable ? `
                        <div class='${props.classes.search}'>
                            <input class='${props.classes.input}' placeholder='${props.labels.placeholder}' type='search' title='${props.labels.searchTitle}'>
                        </div>
                        ` : ""}
                <nav class='${props.classes.pagination}'></nav>
                </div>
                <div class='${props.classes.top}'>
                    ${props.paging && props.perPageSelect ? `...` : ""}
                </div>
                <div class='${props.classes.container}'></div>
            ${props.paging ? `<div class='${props.classes.info}'></div>` : ""}
            `,

            columns: [
                { select: [0, 6], sortable: false } // Disable sorting pada kolom No & Status
            ],
            rowRender: (row, tr, _index) => {
                if (!tr.attributes) {
                    tr.attributes = {};
                }
                if (!tr.attributes.class) {
                    tr.attributes.class = "";
                }
                if (row.selected) {
                    tr.attributes.class += " selected";
                } else {
                    tr.attributes.class = tr.attributes.class.replace(" selected", "");
                }
                return tr;
            }
        };
        if (rowNavigation) {
            options.rowNavigation = true;
            options.tabIndex = 1;
        }

        table = new simpleDatatables.DataTable("#selection-table", options);

        // Mark all rows as unselected
        table.data.data.forEach(data => {
            data.selected = false;
        });

        // Klik checkbox per row
        table.on("datatable.selectrow", (rowIndex, event) => {
            event.preventDefault();
            const row = table.data.data[rowIndex];
            row.selected = !row.selected;
            table.update();
        });

        // --- Tambahin fungsi select all ---
        const headerCheckbox = document.querySelector("#selection-table thead input[type='checkbox']");
        if (headerCheckbox) {
            headerCheckbox.addEventListener("change", function () {
                const checked = this.checked;

                // Set semua row selected sesuai kondisi header
                table.data.data.forEach(data => {
                    data.selected = checked;
                });

                // Update semua checkbox row
                document.querySelectorAll("#selection-table tbody input[type='checkbox']").forEach(cb => {
                    cb.checked = checked;
                });

                table.update();
            });
        }
    };

    // Row navigation makes no sense on mobile
    const isMobile = window.matchMedia("(any-pointer:coarse)").matches;
    if (isMobile) {
        rowNavigation = false;
    }

    resetTable();

    const masterCheckBox = document.getElementById("serial");
    if(masterCheckBox){
        masterCheckBox.addEventListener("change", () => {
            const checked = document.querySelectorAll("#selection-table input[type=checkbox]");
            checked.forEach(ele => {
                ele.checked = masterCheckBox.checked;
            })
        });
    }
}

