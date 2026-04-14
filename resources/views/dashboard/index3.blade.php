@extends('layout.layout')

@php
    $title = 'Penetapan Yudisium';
    $subTitle = 'Tambah';
    $script = '
        <script src="' . asset('assets/js/data-table.js') . '"></script>
        <script src="' . asset('assets/js/getMahasiswa.js') . '"></script>
        <script src="' . asset('assets/js/index3.js') . '"></script>
        <script src="' . asset('assets/js/buttonSave.js') . '"></script>
        <script src="' . asset('assets/js/modalStatus.js') . '"></script>
        <script src="' . asset('assets/js/fakultasSelect.js') . '"></script>
    ';
@endphp



@section('content')
    <h1 class="text-2xl font-semibold text-gray-600 mb-5">{{ __('index3.title') }} / {{ __('index3.subtitle') }}</h1>
    <div class="bg-white p-6 rounded-xl shadow-md mb-6">
        <h2 class="text-lg font-semibold mb-1">{{ __('index3.student_yudisium') }}</h2>
        <p class="text-sm text-gray-500 mb-4">{{ __('index3.instruction') }}
        </p>
        <hr class="mb-6">

        <div class="grid grid-cols-12 gap-6 items-start">
            <!-- Ilustrasi -->
            <div class="col-span-12 md:col-span-2 flex justify-center">
                <img src="{{ asset('assets/images/buku.png') }}" alt="Graduation" class="w-50 h-50">
            </div>

            <!-- Form -->


            <form id="filterForm" class="col-span-12 md:col-span-10 grid grid-cols-12 gap-4" action="{{ route('index3') }}"
                method="GET">
                @csrf
                <!-- Fakultas -->
                <div class="col-span-12 md:col-span-5">
                    <label class="block text-sm font-medium text-gray-500 mb-1">{{ __('index3.faculty') }}</label>
                    <select id="fakultas" name="fakultas" class="form-select w-full border rounded p-2">
                        <option value="">{{ __('index3.select_faculty') }}</option>
                    </select>
                </div>

                <!-- Semester -->
                <div class="col-span-12 md:col-span-5">
                    <label class="block text-sm font-medium text-gray-500 mb-1">{{ __('index3.semester') }}</label>
                    <select name="periode" id="periodeSelect"
                        class="form-select border border-gray-300 rounded-md p-2 text-gray-600">
                        <option value="Pilih">{{ __('index3.select_semester') }}
                        <option>
                            <!-- @foreach ($periodes as $p)
                                <option value="{{ $p['value'] }}" {{ $periode == $p['value'] ? 'selected' : '' }}>
                                    {{ $p['label'] }}
                                </option>
                            @endforeach -->
                    </select>
                </div>

                <!-- Spacer -->
                <div class="hidden md:block md:col-span-2"></div>

                <!-- Program Studi -->
                <div class="col-span-12 md:col-span-5">
                    <label class="block text-sm font-medium text-gray-500 mb-1">{{ __('index3.study_program') }}</label>
                    <select id="prodi" name="prodi" class="form-select w-full border rounded p-2">
                        <option value="">{{ __('index3.select_study') }}</option>
                    </select>
                </div>

                <!-- Tombol -->
                <div class="col-span-12 md:col-start-6 md:col-span-2 flex items-end">
                    <button class="bg-red-600 hover:bg-red-700 text-white font-semibold px-5 py-2 rounded shadow h-[38px] w-[135px]"
                        type="submit">
                        {{ __('index3.show') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Daftar Mahasiswa -->
    <div class="bg-white p-6 rounded-xl shadow-md">
        <!-- Tabel Mahasiswa -->
        <div class="grid grid-cols-12 mt-6">
            <div class="col-span-12">
                <div class="card border-0 overflow-hidden">
                    <h2 class="text-lg font-semibold mb-1">{{ __('index3.student_list') }}</h2>
                    <p class="text-sm text-gray-500 mb-4">{{ __('index3.list_subtitle') }}</p>

                    <!-- Statistik -->
                    <div class="mb-4">
                        <!-- Baris total -->
                        <div class="flex flex-wrap gap-6 mb-4">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                <span class="text-sm text-neutral-950">{{ __('index3.total_selected') }}</span>
                                <span id="totalDipilih" class="font-semibold text-neutral-950">0</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                <span class="text-sm text-neutral-950">{{ __('index3.total_not_selected') }}</span>
                                <span id="totalTidakDipilih" class="font-semibold text-neutral-950">0</span>
                            </div>

                        <!-- Tombol Informasi -->
                            <button id="openInfoModal"
                                class="flex items-center gap-2 px-3 py-1.5 text-sm 
                                    bg-green-50 border border-green-200 text-green-700 rounded-md shadow-sm hover:bg-green-100 transition">

                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-700" viewBox="0 0 24 24"
                                    fill="none" stroke="green" stroke-width="2">
                                    <circle cx="12" cy="12" r="9"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12" y2="16"></line>
                                </svg>

                                {{ __('index3.btnInfo') }}
                            </button>

                        </div>

                        <!-- Modal Informasi -->
                        <div id="infoModal"
                            class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">

                            <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg p-6">

                                <!-- COPY EXACT INFO BOX -->
                                <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-lg shadow-sm">
                                    <h3 class="text-base font-semibold mb-2 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-500" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
                                        </svg>
                                        {{ __('index3.info_yudisium') }}
                                    </h3>
                                    <p class="text-sm mb-3">
                                        {{ __('index3.daftar_mahasiswa') }}
                                        <b>{{ __('index3.eligible') }}</b> {{ __('index3.point_point') }}
                                    </p>
                                    <ul class="list-disc pl-6 text-sm space-y-1 mb-3">
                                        <li>{{ __('index3.study_duration') }}</li>
                                        <li>{{ __('index3.graduation_semester') }}</li>
                                        <li>{{ __('index3.gpa_minimum') }}</li>
                                        <li>{{ __('index3.credits') }}</li>
                                        <li>{{ __('index3.status_mk') }}</li>
                                        <li>{{ __('index3.eprt') }}</li>
                                        <li>{{ __('index3.publikasi') }}</li>
                                        <li>{{ __('index3.tak') }}</li>
                                        <li>{{ __('index3.administratif') }}</li>
                                        <li>{{ __('index3.bpp') }}</li>
                                        <li>{{ __('index3.oplib') }}</li>
                                        <li>{{ __('index3.sanksi') }}</li>
                                    </ul>
                                </div>

                                <!-- Button Close -->
                                <div class="flex justify-end mt-4">
                                    <button id="closeInfoModal"
                                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                                        {{ __('index3.close') }}
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>


                    <div class="card-body">
                        <table id="selection-table" class="border border-neutral-200  rounded-lg border-separate"
                            class="border border-neutral-200 dark:border-neutral-600 rounded-lg border-separate	">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-neutral-800 dark:text-white">
                                        <div class="flex items-center gap-2">
                                            <input id="checkAll" type="checkbox"
                                                class="w-4 h-4 accent-red-600 cursor-pointer">
                                            <label for="checkAll" class="text-neutral-950 font-medium">
                                                {{ __('index3.no') }}
                                            </label>
                                        </div>
                                    </th>
                                    <th scope="col" class="text-neutral-950">
                                        <div class="flex items-center gap-2">
                                            {{ __('index3.nim') }}
                                            <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                            </svg>
                                        </div>
                                    </th>
                                    <th scope="col" class="text-neutral-950">
                                        <div class="flex items-center gap-2">
                                            {{ __('index3.name') }}
                                            <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                            </svg>
                                        </div>
                                    </th>
                                    <th scope="col" class="text-neutral-950">
                                        <div class="flex items-center gap-2">
                                            {{ __('index3.study_duration') }}
                                            <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                            </svg>
                                        </div>
                                    </th>
                                    <th scope="col" class="text-neutral-950">
                                        <div class="flex items-center gap-2">
                                            {{ __('index3.credits') }}
                                            <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                            </svg>
                                        </div>
                                    </th>
                                    <th scope="col" class="text-neutral-950">
                                        <div class="flex items-center gap-2">
                                            {{ __('index3.gpa') }}
                                            <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                            </svg>
                                        </div>
                                    </th>
                                    <th scope="col" class="text-neutral-950">
                                        <div class="flex items-center gap-2">
                                            {{ __('index3.predicate') }}
                                            <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                            </svg>
                                        </div>
                                    </th>
                                    <th scope="col" class="text-neutral-950">
                                        <div class="flex items-center gap-2">
                                            {{ __('index3.status') }}
                                            <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                            </svg>
                                        </div>
                                    </th>
                                    <!-- <th scope="col" class="text-neutral-950">
                                        <div class="flex items-center gap-2">
                                            {{ __('index3.reason') }}
                                            <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                            </svg>
                                        </div>
                                    </th> -->
                                    <th scope="col" class="text-neutral-950">
                                        <div class="flex items-center gap-2">
                                            {{ __('index3.action') }}
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                        <div id="pagination" class="flex justify-center mt-4 gap-2 hidden"></div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Modal Ubah Status -->
        <div id="statusModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6">
                <h2 class="text-lg font-semibold mb-4">{{ __('index3.change_status') }}</h2>
                <form id="statusForm">
                    @csrf
                    <input type="hidden" id="modalNim" name="nim">

                    <!-- Pilih Status -->
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('index3.new_status') }}</label>
                    <select id="modalStatus" name="status" class="form-select w-full border rounded p-2 mb-4" required>
                        <option value="">{{ __('index3.select_status') }}</option>
                        <option value="Eligible">{{ __('index3.eligible') }}</option>
                        <option value="Tidak Eligible">{{ __('index3.not_eligible') }}</option>
                    </select>

                    <!-- Alasan -->
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('index3.reason') }}</label>
                    <textarea id="modalAlasan" name="alasan" rows="3"
                        class="form-input border border-gray-300 rounded w-full p-2 mb-4" required></textarea>

                    <!-- Tombol -->
                    <div class="flex justify-end gap-2">
                        <button type="button" id="closeModal"
                            class="bg-gray-400 text-white px-4 py-2 rounded">{{ __('index3.cancel') }}</button>
                        <button type="submit"
                            class="bg-red-600 text-white px-4 py-2 rounded">{{ __('index3.save') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="infoDetailMahasiswa"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50
           opacity-0 pointer-events-none transition-opacity duration-300 ease-out">

            <div class="bg-white w-full max-w-xl rounded-xl shadow-lg overflow-hidden
               transform scale-95 transition-transform duration-300 ease-out">

                <!-- HEADER MERAH -->
                <div class="bg-red-700 text-white px-5 py-3 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-white">{{ __('index3.detail_student') }}</h2>
                    <button id="closeDetailModal" class="text-white text-lg">✕</button>
                </div>

                <div class="px-6 py-4">

                    <!-- INFORMASI UTAMA -->
                    <div class="grid grid-cols-2 gap-y-2 text-[15px]">
                        <p>{{ __('index3.faculty') }}</p>
                        <p>: <span id="dm-fakultas"></span></p>
                        <p>{{ __('index3.study_program') }}</p>
                        <p>: <span id="dm-prodi"></span></p>
                        <p>{{ __('index3.nim') }}</p>
                        <p>: <span id="dm-nim"></span></p>
                        <p>{{ __('index3.name') }}</p>
                        <p>: <span id="dm-nama"></span></p>
                    </div>

                    <div class="border-t border-gray-300 my-4"></div>

                    <!-- STATUS ELIGIBILITAS -->
                    <div class="text-[15px]">
                        <p class="font-semibold mb-2">{{ __('index3.status_eligibility') }}</p>

                        <ul class="space-y-1 pl-1">

                            <li class="flex items-center gap-2">
                                <span id="dm-study_period_icon"></span> {{ __('index3.study_duration') }}
                            </li>
                            <li class="flex items-center gap-2">
                                <span id="dm-semester_lulus_icon"></span> {{ __('index3.graduation_semester') }}
                            </li>
                            <li class="flex items-center gap-2">
                                <span id="dm-ipk_icon"></span> {{ __('index3.gpa_minimum') }}
                            </li>
                            <li class="flex items-center gap-2">
                                <span id="dm-sks_icon"></span> {{ __('index3.credits') }}
                            </li>
                            <li class="flex items-center gap-2">
                                <span id="dm-mk_icon"></span>{{ __('index3.status_mk') }}
                            </li>
                            <li class="flex items-center gap-2">
                                <span id="dm-bahasa_icon"></span> {{ __('index3.eprt') }}
                            </li>
                            <li class="flex items-center gap-2">
                                <span id="dm-publikasi_icon"></span> {{ __('index3.publikasi') }}
                            </li>
                            <li class="flex items-center gap-2">
                                <span id="dm-tak_icon"></span> {{ __('index3.tak') }}
                            </li>
                            <li class="flex items-center gap-2">
                                <span id="dm-administratif_icon"></span>{{ __('index3.administratif') }}
                            </li>
                            <li class="flex items-center gap-2">
                                <span id="dm-bpp_icon"></span> {{ __('index3.bpp') }}
                            </li>
                            <li class="flex items-center gap-2">
                                <span id="dm-oplib_icon"></span> {{ __('index3.oplib') }}
                            </li>
                            <li class="flex items-center gap-2">
                                <span id="dm-sanksi_icon"></span> {{ __('index3.sanksi') }}
                            </li>

                        </ul>
                    </div>

                </div>

            </div>
        </div>


        <div class="flex flex-col md:flex-row items-center gap-4">
            <!-- <input type="text" id="nomorYudisium" class="form-input border border-gray-300 rounded w-full md:w-1/3" placeholder="Nomor Yudisium" readonly value="{{ old('no_yudicium') }}" /> -->
            <button type="buttton" id="btnSimpan" class="bg-red-600 text-white px-4 py-2 rounded shadow w-full md:w-auto">
                {{ __('index3.save_draft') }}
            </button>
        </div>
    </div>
    <script>
        const routes = {
            showFaculties: "{{ route('show.faculties') }}",
            filterMhs: "{{ route('filterMhs') }}",
            ubahStatus: "{{ route('tempStatus') }}",
            saveDraft: "{{ route('yudicium.save') }}",
        };
    </script>
    <script>
        document.getElementById('setPeriodeBtn').addEventListener('click', function () {
            const periode = document.getElementById('periodeSelect').value;
            window.location.href = `?periode=${encodeURIComponent(periode)}`;
        });
    </script>
    <script>
        const modal = document.getElementById("infoModal");
        const openBtn = document.getElementById("openInfoModal");
        const closeBtn = document.getElementById("closeInfoModal");

        openBtn.addEventListener("click", () => modal.classList.remove("hidden"));
        closeBtn.addEventListener("click", () => modal.classList.add("hidden"));

        // Klik area gelap untuk menutup
        modal.addEventListener("click", (e) => {
            if (e.target === modal) modal.classList.add("hidden");
        });
    </script>
@endsection