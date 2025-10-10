@extends('layout.layout')
@php
    $title = 'Approval Yudisium';
    $subTitle = 'Cryptocracy';
    $script = '
        <script src="' . asset('assets/js/homeFourChart.js') . '"></script>
        <script src="' . asset('assets/js/data-table.js') . '" defer></script>
        <script src="' . asset('assets/js/approveYudcium.js') . '" defer></script>
    ';
@endphp

@section('content')
    <!-- Crypto Home Widgets Start -->
    <h1 class="text-2xl font-semibold text-gray-600 mb-5">Approval Yudisium</h1>

    <!-- Crypto Home Widgets End -->

    <div class="grid grid-cols-1 3xl:grid-cols-12 gap-6 mt-6">
        <div class="2xl:col-span-12 3xl:col-span-4">
            <div class="grid grid-cols-1 gap-6">

                <div class="card h-full rounded-lg border-0">
                    <div
                        class="card-body yudisium w-full grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 gap-4 items-center">
                        <div class="left">
                            <h1 class="text-xl font-bold mb-5">Yudisium</h1>
                            <form action="">
                                <div class="w-[60%] flex flex-col gap-2">
                                    <div class="flex justify-between items-center">
                                        <label for="fakultas" class="text-neutral-800">Fakultas</label>
                                        <select name="fakultas" id="fakultas"
                                            class="text-neutral-800 uppercase w-[55%] form-select text-sm">
                                            {{-- <option value="">-- Pilih Fakultas --</option>
                                            @foreach ($faculties as $faculty)
                                                <option value="{{ $faculty['facultyid'] }}">
                                                    {{ $faculty['facultyname'] }}
                                                </option>
                                            @endforeach --}}

                                        </select>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <label for="Semester" class="text-neutral-800">Semester</label>
                                        <select name="Semester" id="Semester"
                                            class="text-neutral-800 w-[55%] form-select text-sm">
                                            <option value="genap24">Genap 2024/2025</option>
                                            <option value="ganjil24">Ganjil 2024/2025</option>
                                            <option value="genap25">Genap 2025/2026 </option>
                                            <option value="ganjil25">Ganjil 2025/2026 </option>
                                        </select>
                                    </div>
                                    <button id="filterButton"
                                        class="text-neutral-100 border background-primary rounded-md shadow-xl w-1/3 px-2 py-1">
                                        Tampilkan
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="right">
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 gap-4 ">
                                {{-- Card Total Yudisium --}}
                                <div
                                    class="bg-[#e51411] text-white rounded-xl p-5 shadow-lg flex items-center justify-center space-x-4">
                                    <div class="w-16 h-16 bg-[#c7110f] rounded-full flex items-center justify-center">
                                        <iconify-icon icon="clarity:gavel-solid" class="text-white text-4xl"></iconify-icon>
                                    </div>
                                    <div class="flex flex-col text-center">
                                        <h2 class="text-4xl text-white font-bold leading-tight">{{
                                            optional($datas->first())->approval_status === 'approved'
                                            ? $postCount : 0 }}</h2>
                                        <p class="text-sm">Total Yudisium</p>
                                    </div>
                                </div>

                                {{-- Card Total Mahasiswa --}}
                                <div
                                    class="bg-[#3ea83f] text-white rounded-xl p-5 shadow-lg flex items-center justify-center space-x-4">
                                    <div class="w-16 h-16 bg-[#2f812f] rounded-full flex items-center justify-center">
                                        <iconify-icon icon="ph:student-fill" class="text-white text-4xl"></iconify-icon>
                                    </div>
                                    <div class="flex flex-col text-center">
                                        <h2 class="text-4xl text-white font-bold leading-tight">{{ $totalMhsYud }}</h2>
                                        <p class="text-sm">Total Mahasiswa</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-12">
                    <div class="col-span-12">
                        <div class="card border-0 overflow-hidden">
                            <div class="card-header">
                                <h6 class="card-title text-lg font-bold mb-2">Daftar Yudisium</h6>
                                <p class="text-sm text-neutral-400 mb-5">Berikut daftar yudisium periode Ganjil 2024/2025
                                </p>
                            </div>
                            <div class="card-body">
                                <table id="selection-table" class="border border-neutral-200 rounded-lg border-separate	">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="text-neutral-800 dark:text-white"
                                                style="color: black;">
                                                <div class="form-check style-check flex items-center">
                                                    <label class="ms-2 form-check-label" for="serial">
                                                        No
                                                    </label>
                                                </div>
                                            </th>
                                            <th scope="col" class="text-neutral-800 dark:text-white"
                                                style="color: black;">
                                                <div class="flex items-center gap-2">
                                                    Nomor Yudisium
                                                    <svg class="w-4 h-4 ms-1" aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2"
                                                            d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                                    </svg>
                                                </div>
                                            </th>
                                            <th scope="col" class="text-neutral-800 dark:text-white"
                                                style="color: black;">
                                                <div class="flex items-center gap-2">
                                                    Periode
                                                    <svg class="w-4 h-4 ms-1" aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2"
                                                            d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                                    </svg>
                                                </div>
                                            </th>
                                            <th scope="col" class="text-neutral-800 dark:text-white"
                                                style="color: black;">
                                                <div class="flex items-center gap-2">
                                                    Fakultas
                                                    <svg class="w-4 h-4 ms-1" aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2"
                                                            d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                                    </svg>
                                                </div>
                                            </th>
                                            <th scope="col" class="text-neutral-800 dark:text-white"
                                                style="color: black;">
                                                <div class="flex items-center gap-2">
                                                    Program Studi
                                                    <svg class="w-4 h-4 ms-1" aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2"
                                                            d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                                    </svg>
                                                </div>
                                            </th>
                                            <th scope="col" class="text-neutral-800 dark:text-white"
                                                style="color: black;">
                                                <div class="flex items-center gap-2">
                                                    Total Mahasiswa
                                                    <svg class="w-4 h-4 ms-1" aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2"
                                                            d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                                    </svg>
                                                </div>
                                            </th>
                                            <th scope="col" class="text-neutral-800 dark:text-white"
                                                style="color: black;">
                                                <div class="flex items-center gap-2">
                                                    Action
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                            {{-- PopUp table --}}

                            <div id="popup"
                                class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                                <div
                                    class="bg-white p-6 rounded-lg shadow-lg w-[40%] max-w-6xl max-h-[100%] overflow-y-auto relative text-center">
                                    <h2 class="text-2xl font-bold mb-4">Detail Mahasiswa</h2>
                                    <button id="popup-close"
                                        class="absolute top-4 right-4 text-gray-600 hover:text-gray-800">
                                        <iconify-icon icon="mdi:close" class="text-2xl"></iconify-icon>
                                    </button>

                                    <table id="popup-table" class="border border-neutral-200 rounded-lg border-separate">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="text-neutral-800 dark:text-white">
                                                    <div class="form-check style-check flex items-center">
                                                        <label class="ms-2 text-neutral-950 form-check-label"
                                                            for="serial">
                                                            No
                                                        </label>
                                                    </div>
                                                </th>
                                                <th scope="col" class="text-neutral-950">
                                                    <div class="flex items-center gap-2">
                                                        NIM
                                                        <svg class="w-4 h-4 ms-1" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" fill="none" viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round"
                                                                stroke-linejoin="round" stroke-width="2"
                                                                d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                                        </svg>
                                                    </div>
                                                </th>
                                                <th scope="col" class="text-neutral-950">
                                                    <div class="flex items-center gap-2">
                                                        Nama
                                                        <svg class="w-4 h-4 ms-1" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" fill="none" viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round"
                                                                stroke-linejoin="round" stroke-width="2"
                                                                d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                                        </svg>
                                                    </div>
                                                </th>
                                                <th scope="col" class="text-neutral-950">
                                                    <div class="flex items-center gap-2">
                                                        Masa Studi
                                                        <svg class="w-4 h-4 ms-1" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" fill="none" viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round"
                                                                stroke-linejoin="round" stroke-width="2"
                                                                d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                                        </svg>
                                                    </div>
                                                </th>
                                                <th scope="col" class="text-neutral-950">
                                                    <div class="flex items-center gap-2">
                                                        SKS Lulus
                                                        <svg class="w-4 h-4 ms-1" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" fill="none" viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round"
                                                                stroke-linejoin="round" stroke-width="2"
                                                                d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                                        </svg>
                                                    </div>
                                                </th>
                                                <th scope="col" class="text-neutral-950">
                                                    <div class="flex items-center gap-2">
                                                        IPK
                                                        <svg class="w-4 h-4 ms-1" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" fill="none" viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round"
                                                                stroke-linejoin="round" stroke-width="2"
                                                                d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                                        </svg>
                                                    </div>
                                                </th>
                                                <th scope="col" class="text-neutral-950">
                                                    <div class="flex items-center gap-2">
                                                        Predikat
                                                        <svg class="w-4 h-4 ms-1" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" fill="none" viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round"
                                                                stroke-linejoin="round" stroke-width="2"
                                                                d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                                        </svg>
                                                    </div>
                                                </th>
                                                <th scope="col" class="text-neutral-950">
                                                    <div class="flex items-center gap-2">
                                                        Status
                                                        <svg class="w-4 h-4 ms-1" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" fill="none" viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round"
                                                                stroke-linejoin="round" stroke-width="2"
                                                                d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                                        </svg>
                                                    </div>
                                                </th>
                                                <th scope="col" class="text-neutral-950">
                                                    <div class="flex items-center gap-2">
                                                        Alasan
                                                        <svg class="w-4 h-4 ms-1" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" fill="none" viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round"
                                                                stroke-linejoin="round" stroke-width="2"
                                                                d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                                        </svg>
                                                    </div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="popup-body">

                                        </tbody>
                                    </table>
                                    <div id="approve-yudisium"
                                        class="flex flex-col items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 gap-5">
                                        <select id="approval" class="form-select w-full text-neutral-900">
                                            <option value="Eligible">Eligible</option>
                                            <option value="Tidak Eligible">Tidak Eligible</option>
                                        </select>
                                        <input type="text" id="catatan"
                                            class="form-input w-full text-neutral-900" placeholder="Alasan">
                                        <button id="btn-simpan"
                                            class="bg-red-600 text-white px-4 py-2 rounded">Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const routes = {
            showFaculties: "{{ route('show.faculties') }}",
            filterYudisium: "{{ route('yudicium.filter') }}",
            updateYudisium: "{{ route('yudicium.update') }}"
        };
    </script>
    <script src="{{ asset('assets/js/popup-yudicium.js') }}" defer></script>
@endsection
