document.addEventListener("DOMContentLoaded", () => {
    const popup = document.getElementById("popup");
    const popupBody = document.getElementById("popup-body");
    const closeButton = document.getElementById("popup-close");

    // Pagination variables
    let currentPage = 1; // Default halaman ke-1 untuk Penetapan Yudisium
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
                <div class="flex flex-1 items-center justify-between sm:hidden">
                    <button ${currentPage === 1 ? 'disabled' : ''} 
                        class="pagination-btn relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}"
                        data-page="${currentPage - 1}">
                        Previous
                    </button>
                    <span class="text-sm font-medium text-gray-700">
                        ${currentPage}/${totalPages}
                    </span>
                    <button ${currentPage === totalPages ? 'disabled' : ''} 
                        class="pagination-btn relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''}"
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
        const yudId = button.dataset.id;

        // Tampilkan loading di popup
        popupBody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="flex items-center justify-center gap-2 text-gray-500">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                        </svg>
                        Memuat data...
                    </div>
                </td>
            </tr>
        `;
        popup.classList.remove("hidden");

        try {
            const res = await fetch(`/yudicium/${yudId}/mahasiswa`);
            if (!res.ok) throw new Error('Failed to fetch');

            const data = await res.json();

            // Tampilkan badge status approval di header popup jika ada
            const approvalStatus = data.yudisium?.[0]?.approval_status ?? '';
            const statusBadgeEl = document.getElementById('popup-approval-status');
            if (statusBadgeEl) {
                const badgeClass = {
                    'Approved' : 'bg-green-100 text-green-700',
                    'Rejected' : 'bg-red-100 text-red-700',
                    'Waiting'  : 'bg-yellow-100 text-yellow-700',
                    'Draft'    : 'bg-blue-100 text-blue-700',
                }[approvalStatus] ?? 'bg-gray-100 text-gray-700';

                statusBadgeEl.innerHTML = approvalStatus
                    ? `<span class="px-3 py-1 rounded-full text-xs font-semibold ${badgeClass}">${approvalStatus}</span>`
                    : '';
            }

            if (Array.isArray(data.mahasiswa) && data.mahasiswa.length > 0) {
                allMahasiswa = data.mahasiswa;
                currentPage = 1;
                renderMahasiswaTable(allMahasiswa, currentPage);
            } else {
                popupBody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center py-6 text-gray-500">
                            Tidak ada data mahasiswa untuk yudisium ini.
                        </td>
                    </tr>
                `;
                const paginationContainer = document.getElementById('pagination-controls');
                if (paginationContainer) paginationContainer.innerHTML = '';
            }

        } catch (err) {
            console.error(err);
            popupBody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-6 text-red-500">
                        Gagal memuat data. Silakan coba lagi.
                    </td>
                </tr>
            `;
            const paginationContainer = document.getElementById('pagination-controls');
            if (paginationContainer) paginationContainer.innerHTML = '';
        }
    });

    closeButton.addEventListener("click", () => {
        popup.classList.add("hidden");
    });

    popup.addEventListener("click", (e) => {
        if (e.target === popup)
            popup.classList.add("hidden");
    });
});
