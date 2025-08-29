@extends('layout.layout')

@php
    $title = 'Penetapan Yudisium';
    $subTitle = 'Daftar Yudisium';
    $script = '<script src="' . asset('assets/js/data-table.js') . '"></script>';
@endphp

@section('content')
    <h1 class="text-2xl font-bold mb-4 text-neutral-500">Penetapan Yudisium</h1>
    <div class="grid grid-cols-12 gap-6">
        <!-- Filter dan Statistik -->
        <div class="col-span-12 bg-white p-6 rounded-xl shadow-lg">
            <div class="flex items-center justify-between gap-4">
                <div class="flex flex-col gap-4">
                    <h1 class="text-2xl font-bold">Yudisium</h1>
                    <div class="flex items-center gap-3">
                        <label for="semester" class="text-sm font-medium text-neutral-700 whitespace-nowrap">Semester</label>
                        <div class="text-neutral-950">
                            <button id="btn" class="bg-neutral-100 rounded-lg">
                                <div class="p-1 flex items-center justify-between gap-20 px-3">
                                    <span id="selected" class="text-sm text-neutral-600">Ganjil 2024/2025</span>
                                    <iconify-icon icon="ph:caret-down-bold" class="text-gray-500 rotate-[-90deg]"
                                        id="caret-down"></iconify-icon>
                                </div>
                            </button>
                            <div id="dropdown" class="mt-1">
                                <ul
                                    class="absolute bg-white border border-gray-300 shadow-lg w-56 rounded-lg text-center text-neutral-700">
                                    <li class="p-2 hover:bg-gray-100 cursor-pointer">Ganjil 2024/2025</li>
                                    <li class="p-2 hover:bg-gray-100 cursor-pointer">Genap 2024/2025</li>
                                    <li class="p-2 hover:bg-gray-100 cursor-pointer">Ganjil 2023/2024</li>
                                    <li class="p-2 hover:bg-gray-100 cursor-pointer">Genap 2023/2024</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <button
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold px-5 py-2 rounded-lg shadow-md transition w-fit ml-[73px]">
                        Tampilkan
                    </button>
                </div>

                <div class="right">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 gap-4">
                        {{-- Card Total Yudisium --}}
                        <div
                            class="bg-red-600 text-white rounded-xl p-5 shadow-lg flex items-center justify-center space-x-4">
                            <div class="w-16 h-16 bg-red-800 rounded-full flex items-center justify-center">
                                <iconify-icon icon="clarity:gavel-solid" class="text-white text-4xl"></iconify-icon>
                            </div>
                            <div class="flex flex-col text-center">
                                <h2 class="text-4xl text-white font-bold">43</h2>
                                <p class="text-sm">Total Yudisium</p>
                            </div>
                        </div>

                        {{-- Card Total Mahasiswa --}}
                        <div
                            class="bg-green-600 text-white rounded-xl p-5 shadow-lg flex items-center justify-center space-x-4">
                            <div class="w-16 h-16 bg-green-800 rounded-full flex items-center justify-center">
                                <iconify-icon icon="ph:student-fill" class="text-white text-4xl"></iconify-icon>
                            </div>
                            <div class="flex flex-col text-center">
                                <h2 class="text-4xl text-white font-bold">214</h2>
                                <p class="text-sm">Total Mahasiswa</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-12 mt-6">
        <div class="col-span-12">
            <div class="card border-0 overflow-hidden px-8 py-4 flex flex-col gap-5">
                <div class="flex justify-between items-center">
                    <div class="flex flex-col gap-2">
                        <h6 class="card-title mb-0 text-lg font-bold">Daftar Yudisium</h6>
                        <h6 class="card-title mb-0 text-xs text-gray-600">Berikut Daftar Yudisium Periode Ganjil 2024/2025
                        </h6>
                    </div>
                    <!-- Tombol Tambah -->
                    <button
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold px-5 py-1.5 rounded-lg shadow-xl transition mt-2 mr-5">
                        <a href="{{ route('index3') }}">+ Tambah</a>
                    </button>
                </div>
                {{-- <div class="px-5 space-y-6">
                    <div class="w-full h-[3px] bg-neutral-100"></div>
                    <div class="flex justify-between">
                        <p class="text-gray-500">Rows per page</p>
                    </div>
                    <table id="selection-table" class="border-separate">
                        <thead>
                            <tr class="bg-blue-100 shadow-md">
                                <th scope="col" class="text-neutral-800 dark:text-white px-5 py-1">
                                    <div class="form-check style-check flex flex-col items-center gap-2">
                                        <label class="text-neutral-950 form-check-label" for="serial">
                                            No
                                        </label>
                                        <input class="form-check-input" id="serial" type="checkbox">
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-800 dark:text-white px-5 py-1">
                                    <div class="form-check style-check flex items-center gap-2">
                                        <label class="text-neutral-900 form-check-label mb-5" for="serial">
                                            Nomor SK
                                        </label>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-800 dark:text-white px-5 py-1">
                                    <div class="form-check style-check flex items-center gap-2">
                                        <label class="text-neutral-900 form-check-label mb-5" for="serial">
                                            Nomor Yudisium
                                        </label>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-800 dark:text-white px-5 py-1">
                                    <div class="form-check style-check flex items-center gap-2">
                                        <label class="text-neutral-900 form-check-label mb-5" for="serial">
                                            Periode
                                        </label>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-800 dark:text-white px-5 py-1">
                                    <div class="form-check style-check flex items-center gap-2">
                                        <label class="text-neutral-900 form-check-label mb-5" for="serial">
                                            Fakultas
                                        </label>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-800 dark:text-white px-5 py-1">
                                    <div class="form-check style-check flex items-center gap-2">
                                        <label class="text-neutral-900 form-check-label mb-5" for="serial">
                                            Program Studi
                                        </label>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-800 dark:text-white px-5 py-1">
                                    <div class="form-check style-check flex items-center gap-2">
                                        <label class="text-neutral-900 form-check-label mb-5" for="serial">
                                            Approval Dekan/Dir
                                        </label>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-800 dark:text-white px-5 py-1">
                                    <div class="form-check style-check flex items-center gap-2">
                                        <label class="text-neutral-900 form-check-label mb-5" for="serial">
                                            Status DKD
                                        </label>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-800 dark:text-white px-5 py-1">
                                    <div class="form-check style-check flex items-center gap-2">
                                        <label class="text-neutral-900 form-check-label mb-5" for="serial">
                                            Action
                                        </label>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div> --}}
                <div class="w-full h-[3px] bg-neutral-100"></div>
                <div class="card-body">
                    <table id="selection-table"
                        class="border border-neutral-200 dark:border-neutral-600 rounded-lg border-separate	">
                        <thead>
                            <tr>
                                <th scope="col" class="text-neutral-800 dark:text-white">
                                    <div class="form-check style-check flex items-center gap-2">
                                        <input class="form-check-input" id="serial" type="checkbox">
                                        <label class="text-neutral-950 form-check-label" for="serial">
                                            No
                                        </label>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-950">
                                    <div class="flex items-center gap-2">
                                        Nomor SK
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-950">
                                    <div class="flex items-center gap-2">
                                        Nomor Yudisium
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-950">
                                    <div class="flex items-center gap-2">
                                        Periode
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-950">
                                    <div class="flex items-center gap-2">
                                        Fakultas
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-950">
                                    <div class="flex items-center gap-2">
                                        Program Studi
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-950">
                                    <div class="flex items-center gap-2">
                                        Approval Dekan/Dir
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-950">
                                    <div class="flex items-center gap-2">
                                        Status DKD
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-950">
                                    <div class="flex items-center gap-2">
                                        Action
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="form-check style-check flex items-center">
                                        <input class="form-check-input" type="checkbox">
                                        <label class="ms-2 form-check-label">
                                            01
                                        </label>
                                    </div>
                                </td>
                                <td><a href="javascript:void(0)" class="text-primary-600">KR.418/AKD15/AKD
                                        BAA/2024</a></td>
                                <td>
                                    <div class="flex items-center">
                                        <h6 class="text-base mb-0 ">
                                            14/AKD15/RI-DEK/2024
                                        </h6>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                        <h6>
                                            09-08-2024
                                        </h6>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <h6>
                                            Rekayasa Industri
                                        </h6>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                        <h6>S1 Teknik Industri</h6>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                        <span
                                            class="bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 px-6 py-1.5 rounded-full font-medium text-sm">Approved</span>
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 px-6 py-1.5 rounded-full font-medium text-sm">Done</span>
                                </td>
                                <td class="flex items-center gap-2">
                                    <a href="javascript:void(0)"
                                        class="w-8 h-8 bg-primary-50 dark:bg-primary-600/10 text-primary-600 dark:text-primary-400 rounded-full inline-flex items-center justify-center">
                                        <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
                                    </a>
                                    <a href="javascript:void(0)"
                                        class="w-8 h-8 bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 rounded-full inline-flex items-center justify-center">
                                        <iconify-icon icon="lucide:edit"></iconify-icon>
                                    </a>
                                    <a href="javascript:void(0)"
                                        class="w-8 h-8 bg-danger-100 dark:bg-danger-600/25 text-danger-600 dark:text-danger-400 rounded-full inline-flex items-center justify-center">
                                        <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-check style-check flex items-center">
                                        <input class="form-check-input" type="checkbox">
                                        <label class="ms-2 form-check-label">
                                            02
                                        </label>
                                    </div>
                                </td>
                                <td><a href="javascript:void(0)" class="text-primary-600">KR.419/AKD15/AKD
                                        BAA/2024
                                    </a>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                        <h6 class="text-base mb-0 ">
                                            32/AKD15/IF-DEK/2024
                                        </h6>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                        <h6>
                                            20-08-2024
                                        </h6>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <h6>
                                            Informatika
                                        </h6>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                        <h6>S1 Informatika</h6>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                        <span
                                            class="bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 px-6 py-1.5 rounded-full font-medium text-sm">Approved</span>
                                    </div>
                                </td>
                                <td>
                                    <div
                                        class="flex items-center bg-warning-100 dark:bg-warning-600/25 text-warning-600
                                        dark:text-warning-400 px-4 py-1.5 rounded-full font-medium text-sm">
                                        <span class="text-sm font-medium text-center">Sirkular Basila : Rektor</span>
                                    </div>
                                </td>
                                <td>
                                    <a href="javascript:void(0)"
                                        class="w-8 h-8 bg-primary-50 dark:bg-primary-600/10 text-primary-600 dark:text-primary-400 rounded-full inline-flex items-center justify-center">
                                        <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
                                    </a>
                                    <a href="javascript:void(0)"
                                        class="w-8 h-8 bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 rounded-full inline-flex items-center justify-center">
                                        <iconify-icon icon="lucide:edit"></iconify-icon>
                                    </a>
                                    <a href="javascript:void(0)"
                                        class="w-8 h-8 bg-danger-100 dark:bg-danger-600/25 text-danger-600 dark:text-danger-400 rounded-full inline-flex items-center justify-center">
                                        <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-check style-check flex items-center">
                                        <input class="form-check-input" type="checkbox">
                                        <label class="ms-2 form-check-label">
                                            03
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                        <span
                                            class="bg-danger-100 dark:bg-danger-600/25 text-danger-600 dark:text-danger-400 px-6 py-1.5 rounded-full font-medium text-sm">Sirkulir
                                            RIS: KaUr ADM</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                        <h6 class="text-base mb-0 ">
                                            08/AKD15/TE-DEK/2024
                                        </h6>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                        <h6>
                                            28-11-2024
                                        </h6>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <h6>
                                            Teknik Elektro
                                        </h6>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                        <h6>S1 Teknik Elektro</h6>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                        <span
                                            class="bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 px-6 py-1.5 rounded-full font-medium text-sm">Approved</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                        <span
                                            class="bg-danger-100 dark:bg-danger-600/25 text-danger-600 dark:text-danger-400 px-6 py-1.5 rounded-full font-medium text-sm text-center">Menunggu
                                            SK</span>
                                    </div>
                                </td>
                                <td>
                                    <a href="javascript:void(0)"
                                        class="w-8 h-8 bg-primary-50 dark:bg-primary-600/10 text-primary-600 dark:text-primary-400 rounded-full inline-flex items-center justify-center">
                                        <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
                                    </a>
                                    <a href="javascript:void(0)"
                                        class="w-8 h-8 bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 rounded-full inline-flex items-center justify-center">
                                        <iconify-icon icon="lucide:edit"></iconify-icon>
                                    </a>
                                    <a href="javascript:void(0)"
                                        class="w-8 h-8 bg-danger-100 dark:bg-danger-600/25 text-danger-600 dark:text-danger-400 rounded-full inline-flex items-center justify-center">
                                        <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/dropdown.js') }}">
        toggleDropdown('btn', 'dropdown', 'selected', 'caret-down');
    </script>
@endpush
