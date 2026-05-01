document.addEventListener("DOMContentLoaded", () => {
    const popup = document.getElementById("popup");
    const popupBody = document.getElementById("popup-body");
    const closeButton = document.getElementById("popup-close");
    const approvalSelect = document.getElementById("approval");
    const catatan = document.getElementById('catatan');
    const btnApprove = document.getElementById('btn-simpan');

    // Pagination variables
    let currentPage = 2; // Default halaman ke-2
    let itemsPerPage = 5; // 5 mahasiswa per halaman
    let allMahasiswa = []; // Store all mahasiswa data

    if (!popup || !popupBody || !closeButton) {
        console.log("Popup : ", popup);
        console.log("Popup Body : ", popupBody);
        console.log("Close Button : ", closeButton);
        console.error("Popup elements not found")
        return;
    }

    // Function to render mahasiswa table with pagination
    function renderMahasiswaTable(mahasiswaData, page = 1) {
        const startIndex = (page - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const paginatedData = mahasiswaData.slice(startIndex, endIndex);
        const totalPages = Math.ceil(mahasiswaData.length / itemsPerPage);

        // Render table rows
        popupBody.innerHTML = `
            ${paginatedData.map((mhs, index) => `
                <tr>
                    <td>${startIndex + index + 1}</td>
                    <td>${mhs.nim}</td>
                    <td>${mhs.name}</td>
                    <td>${mhs.study_period}</td>
                    <td>${mhs.pass_sks}</td>
                    <td>${mhs.ipk}</td>
                    <td>${mhs.predikat}</td>
                    <td>${mhs.status && mhs.status.trim() !== ""
                        ? mhs.status
                        : mhs.status_otomatis
                    }</td>
                </tr>
            `).join('')}
        `;

        // Render pagination controls
        renderPaginationControls(page, totalPages, mahasiswaData.length);
    }

    // Function to render pagination controls
    function renderPaginationControls(currentPage, totalPages, totalItems) {
        const paginationContainer = document.getElementById('pagination-controls');
        if (!paginationContainer) return;

        const startItem = ((currentPage - 1) * itemsPerPage) + 1;
        const endItem = Math.min(currentPage * itemsPerPage, totalItems);

        let paginationHTML = `
            <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                <div class="flex flex-1 justify-between sm:hidden">
                    <button ${currentPage === 1 ? 'disabled' : ''} 
                        class="pagination-btn relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}"
                        data-page="${currentPage - 1}">
                        Previous
                    </button>
                    <button ${currentPage === totalPages ? 'disabled' : ''} 
                        class="pagination-btn relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''}"
                        data-page="${currentPage + 1}">
                        Next
                    </button>
                </div>
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium">${startItem}</span> to <span class="font-medium">${endItem}</span> of{' '}
                            <span class="font-medium">${totalItems}</span> results
                        </p>
                    </div>
                    <div>
                        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                            <button ${currentPage === 1 ? 'disabled' : ''} 
                                class="pagination-btn relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}"
                                data-page="${currentPage - 1}">
                                <span class="sr-only">Previous</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                                </svg>
                            </button>
        `;

        // Generate page numbers
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                paginationHTML += `
                    <button class="pagination-btn relative inline-flex items-center px-4 py-2 text-sm font-semibold ${i === currentPage 
                        ? 'z-10 bg-red-600 text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600' 
                        : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0'}"
                        data-page="${i}">
                        ${i}
                    </button>
                `;
            } else if (i === currentPage - 2 || i === currentPage + 2) {
                paginationHTML += `
                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0">...</span>
                `;
            }
        }

        paginationHTML += `
                            <button ${currentPage === totalPages ? 'disabled' : ''} 
                                class="pagination-btn relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''}"
                                data-page="${currentPage + 1}">
                                <span class="sr-only">Next</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </nav>
                    </div>
                </div>
            </div>
        `;

        paginationContainer.innerHTML = paginationHTML;

        // Add event listeners to pagination buttons
        document.querySelectorAll('.pagination-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const page = parseInt(e.currentTarget.dataset.page);
                if (page >= 1 && page <= totalPages) {
                    currentPage = page;
                    renderMahasiswaTable(allMahasiswa, currentPage);
                }
            });
        });
    }

    document.addEventListener("click", async (e) => {

        const button = e.target.closest(".btn-popup");
        if (!button) return;

        e.preventDefault();
        yudId = button.dataset.id;

        console.log("ID : ", yudId);

        try {
            const res = await fetch(`/yudicium/${yudId}/mahasiswa`);
            if (!res.ok) throw new Error('Failed to fetch');

            const data = await res.json();
            console.log("Data:", data);

            if (Array.isArray(data.mahasiswa) && data.mahasiswa.length > 0) {
                allMahasiswa = data.mahasiswa; // Store all data
                currentPage = 2; // Reset to page 2 (default)
                
                // Render table with pagination starting at page 2
                renderMahasiswaTable(allMahasiswa, currentPage);
            } else {
                popupBody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center">tidak ada data</td>
                    </tr>
                `;
                document.getElementById('pagination-controls').innerHTML = '';
            }

            if (document.getElementById('approve-yudisium')) {
                if (Array.isArray(data.yudisium) && data.yudisium.length > 0) {
                    const status = data.yudisium[0].approval_status;

                    const option = ['Waiting', 'Approved', 'Rejected'];

                    approvalSelect.innerHTML = option.map(opt =>
                        `<option value="${opt}" ${opt === status ? 'selected' : ''}>
                            ${opt.charAt(0).toUpperCase() + opt.slice(1)}
                        </option>`
                    ).join('');
                }
            }

            popup.classList.remove("hidden");
        } catch (err) {
            console.error(err);
            popupBody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center">Gagal memuat data</td>
                </tr>
            `;
            document.getElementById('pagination-controls').innerHTML = '';
            popup.classList.remove("hidden");
        }
    });

    closeButton.addEventListener("click", () => {
        popup.classList.add("hidden");
    });

    popup.addEventListener("click", (e) => {
        if (e.target === popup)
            popup.classList.add("hidden");
    });

    btnApprove.addEventListener('click', async (e) => {
        e.preventDefault();
        const status = approvalSelect.value;
        const alasan = catatan.value;

        console.log("Clicked");

        if (!yudId) {
            console.error("Yudisium ID not found");
            return;
        }

        try {
            const res = await fetch(routes.updateYudisium, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    yudisium_id: parseInt(yudId),
                    approval_status: status,
                    catatan: alasan
                })
            });

            if (!res.ok) {
                throw new Error('Failed to fetch');
            }

            const data = await res.json();

            if (data.success) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Status berhasil diubah',
                    icon: 'success',
                    showCancelButton: false,
                    confirmButtonText: 'OK',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn-ok'
                    }
                }).then((result) => {
                    if(result.isConfirmed) {
                        window.location.reload();
                    }
                });
                document.getElementById('popup').classList.add('hidden');
            } else {
                Swal.fire({
                    title: 'Gagal!',
                    text: data.message || "Terjadi kesalahan error",
                    confirmButtonText: 'OK',
                    icon: 'error',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn-ok'
                    }
                });
            }
        } catch (err) {
            console.error(err);
            Swal.fire({
                title: 'Error!',
                text: err.message || 'Terjadi kesalahan saat mengirim data.',
                icon: 'error',
                confirmButtonText: 'OK',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn-ok'
                }
            })
        }
    });
});
