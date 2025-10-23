// Buat tbody blade
window.getMahasiswa = function (mhs, idx) {
    const tbody = document.querySelector('#selection-table tbody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
                        <td>${idx + 1}</td>
                        <td>${mhs.nim}</td>
                        <td>${mhs.name}</td>
                        <td>${mhs.study_period} Semester</td>
                        <td>${mhs.pass_sks}</td>
                        <td>${mhs.ipk}</td>
                        <td>${mhs.predikat}</td>
                        <td>
                            <span
                                class="statusSpan ${mhs.status === "Eligible"
            ? "bg-success-100 text-success-600"
            : "bg-danger-100 text-danger-600"
        } px-6 py-1.5 rounded-full font-medium text-sm inline-block cursor-pointer"
                                data-nim="${mhs.nim}"
                                data-status="${mhs.status}"
                                data-alasan="${mhs.alasan_status}">
                                ${mhs.status}
                            </span>
                        </td>
                        <td>
                            ${mhs.alasan_status
            ? `<span class="text-xs text-gray-500">${mhs.alasan_status}</span>`
            : '-'
        }
                        </td>
                        <td>
                            <a href="javascript:void(0)" class="w-8 h-8 bg-primary-50 text-primary-600 rounded-full inline-flex items-center justify-center">
                                <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
                            </a>
                        </td>
                    `;
    tbody.appendChild(tr);

}