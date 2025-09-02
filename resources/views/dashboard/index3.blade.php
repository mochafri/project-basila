@extends('layout.layout')

@php
    $title = 'Penetapan Yudisium';
    $subTitle = 'Tambah';
    $script = '<script src="' . asset('assets/js/data-table.js') . '"></script>';
@endphp

@section('content')
    <h1 class="text-2xl font-bold mb-4 text-neutral-500">Penetapan Yudisium / Tambah</h1>
    <div class="bg-white p-6 rounded-xl shadow-md mb-6">
        <h2 class="text-lg font-semibold mb-1">Mahasiswa Yudisium</h2>
        <p class="text-sm text-gray-500 mb-4">Pastikan data yang dipilih telah sesuai untuk menampilkan data mahasiswa</p>
        <hr class="mb-6">

        <div class="grid grid-cols-12 gap-6 items-start">
            <!-- Ilustrasi -->
            <div class="col-span-12 md:col-span-2 flex justify-center">
                <img src="{{ asset('assets/images/buku.png') }}" alt="Graduation" class="w-50 h-50">
            </div>

            <!-- Form -->
            <div class="col-span-12 md:col-span-10 grid grid-cols-12 gap-4">
                <!-- Fakultas -->
                <div class="col-span-12 md:col-span-5">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Fakultas</label>
                    <div class="flex flex-col space-y-1">
                        <button id="btn" type="button"
                            class="text-neutral-950 w-72 border border-neutral-200 rounded-md h-8">
                            <div class="flex justify-between items-center px-2">
                                <span id="selected">Fakultas</span>
                                <iconify-icon icon="ph:caret-down-bold" id="caret-down"
                                    class="text-gray-500 rotate-[-90deg]"></iconify-icon>
                            </div>
                        </button>
                        <div id="dropdown">
                            <ul class="absolute bg-white border border-gray-300 shadow-lg w-60 rounded-lg z-10">
                                <li class="p-2 hover:bg-gray-100 cursor-pointer">Fakultas Ilmu Terapan</li>
                                <li class="p-2 hover:bg-gray-100 cursor-pointer">Fakultas Informatika</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Semester -->
                <div class="col-span-12 md:col-span-5">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Semester</label>
                    <div class="flex flex-col space-y-1">
                        <button id="btn2" type="button"
                            class="text-neutral-950 w-72 border border-neutral-200 rounded-md h-8">
                            <div class="flex justify-between items-center px-2">
                                <span id="selected2">Periode Ganjil 2024/2025</span>
                                <iconify-icon icon="ph:caret-down-bold" id="caret-down2"
                                    class="text-gray-500 rotate-[-90deg]"></iconify-icon>
                            </div>
                        </button>
                        <div id="dropdown2">
                            <ul class="absolute bg-white border border-gray-300 shadow-lg w-60 rounded-lg z-10">
                                <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Periode Genap 2024/2025</li>
                                <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Periode Ganjil 2023/2024</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Spacer -->
                <div class="hidden md:block md:col-span-2"></div>

                <!-- Program Studi -->
                <div class="col-span-12 md:col-span-5">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Program Studi</label>
                    <div class="flex flex-col space-y-1">
                        <button id="btn3" type="button"
                            class="text-neutral-950 w-72 border border-neutral-200 rounded-md h-8">
                            <div class="flex flex-row items-center justify-between px-2 ">
                                <span id="selected3">Informatika</span>
                                <iconify-icon icon="ph:caret-down-bold" class="text-gray-500 rotate-[-90deg]"
                                    id="caret-down3"></iconify-icon>
                            </div>
                        </button>
                        <div id="dropdown3">
                            <ul class="absolute bg-white border border-gray-300 shadow-lg w-60 rounded-lg z-10">
                                <li class="p-2 hover:bg-gray-100 cursor-pointer">Ilmu Terapan</li>
                                <li class="p-2 hover:bg-gray-100 cursor-pointer">Industri Kreatif</li>
                                <li class="p-2 hover:bg-gray-100 cursor-pointer">Teknik Electro</li>
                                <li class="p-2 hover:bg-gray-100 cursor-pointer">Rekayasa Industri</li>
                                <li class="p-2 hover:bg-gray-100 cursor-pointer">Ekonomi dan Bisnis</li>
                                <li class="p-2 hover:bg-gray-100 cursor-pointer">Komunikasi dan Bisnis</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Tombol -->
                <div class="col-span-12 md:col-start-6 md:col-span-2 flex items-end">
                    <button class="bg-red-600 hover:bg-red-700 text-white font-semibold px-5 py-2 rounded shadow w-full">
                        Tampilkan
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Daftar Mahasiswa -->
    <div class="bg-white p-6 rounded-xl shadow-md">
        <!-- Tabel Mahasiswa -->
        <div class="grid grid-cols-12 mt-6">
            <div class="col-span-12">
                <div class="card border-0 overflow-hidden">
                    <div class="flex justify-between">
                        <div class="flex flex-col">
                            <h2 class="text-lg font-semibold mb-1">Daftar Mahasiswa Yudisium</h2>
                            <p class="text-sm text-gray-500 mb-4">Berikut daftar yudisium periode Ganjil 2024/2025</p>
                        </div>
                        <!-- Statistik -->
                        <div class="flex mb-4 flex-row md:flex-row gap-20 items-center">
                            <div class="flex flex-col gap-2 font-semibold text-2xl">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-green-500 rounded-full"></div>
                                    <span class="text-sm text-neutral-950">Total Eligible</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-red-500 rounded-full"></div>
                                    <span class="text-sm text-neutral-950">Total Tidak Eligible</span>
                                </div>
                            </div>
                            <div class="flex flex-col gap-3">
                                <span class="font-semibold text-neutral-950">1</span>
                                <span class="font-semibold text-neutral-950">1</span>
                            </div>
                        </div>
                    </div>
                    <div class="w-full h-[3px] bg-neutral-100"></div>
                    <div class="card-body">
                        <table id="selection-table"
                            class="border border-neutral-200 dark:border-neutral-600 rounded-lg border-separate	">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-neutral-800 dark:text-white">
                                        <div class="form-check style-check flex items-center">
                                            <input class="form-check-input" id="serial" type="checkbox">
                                            <label class="ms-2 text-neutral-950 form-check-label" for="serial">
                                                No
                                            </label>
                                        </div>
                                    </th>
                                    <th scope="col" class="text-neutral-950">
                                        <div class="flex items-center gap-2">
                                            NIM
                                            <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                width="24" height="24" fill="none" viewBox="0 0 24 24">
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
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                fill="none" viewBox="0 0 24 24">
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
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                fill="none" viewBox="0 0 24 24">
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
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                fill="none" viewBox="0 0 24 24">
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
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                fill="none" viewBox="0 0 24 24">
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
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                fill="none" viewBox="0 0 24 24">
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
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="m8 15 4 4 4-4m0-6-4-4-4 4" />
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
                                    <td><a href="javascript:void(0)" class="text-primary-600">
                                            123567890
                                        </a>
                                    </td>
                                    <td>
                                        <div class="flex items-center">
                                            <h6 class="text-base mb-0 ">
                                                Mochammad Tontowi
                                            </h6>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex items-center">
                                            <h6>
                                                6 semester
                                            </h6>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <h6>
                                                99+
                                            </h6>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex items-center">
                                            <h6>4.00</h6>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex items-center">
                                            Very Good (Sangat Memuaskan)
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex items-center">
                                            <span
                                                class="bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 px-6 py-1.5 rounded-full font-medium text-sm">Eligible</span>
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
                                                01
                                            </label>
                                        </div>
                                    </td>
                                    <td><a href="javascript:void(0)" class="text-primary-600">
                                            123567890
                                        </a>
                                    </td>
                                    <td>
                                        <div class="flex items-center">
                                            <h6 class="text-base mb-0 ">
                                                Mochammad Tontowi
                                            </h6>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex items-center">
                                            <h6>
                                                6 semester
                                            </h6>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <h6>
                                                99+
                                            </h6>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex items-center">
                                            <h6>4.00</h6>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex items-center">
                                            Very Good (Sangat Memuaskan)
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex items-center">
                                            <span
                                                class="bg-danger-100 dark:bg-danger-600/25 text-danger-600 dark:text-danger-400 px-6 py-1.5 rounded-full font-medium text-sm">Tidak
                                                Eligible</span>
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

        <!-- Nomor Yudisium & Button -->
        {{-- <div class="flex flex-col md:flex-row items-center gap-4 mt-6">
        <input
            type="text"
            class="form-input border border-gray-300 rounded w-full md:w-1/3"
            placeholder="Nomor Yudisium"
            value="32/AKD15/IF-DEK/2024"
            readonly
        />
        <button class="bg-red-600 text-white px-4 py-2 rounded shadow w-full md:w-auto">
            Tetapkan Yudisium
        </button>
    </div> --}}
        <div>
            <div class="text-neutral-500 flex gap-10 items-center">
                <p class="font-semibold">Nomor Yudisium</p>
                <input type="text" class="text-md border border-gray-300 rounded-md py-1 w-2/12"
                    value="32/AKD15/IF-DEK/2024" readonly />
            </div>
            <button type="submit"
                class="bg-red-600 text-white px-6 py-1 rounded shadow-lg mt-4 md:w-auto md:font-semibold relative bottom-1">
                Tetapkan Yudisium
            </button>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/dropdown.js') }}">
        toggleDropdown('btn', 'dropdown', 'selected', 'caret-down');
        toggleDropdown('btn2', 'dropdown2', 'selected2', 'caret-down2');
        toggleDropdown('btn3', 'dropdown3', 'selected3', 'caret-down3');
    </script>
@endpush
