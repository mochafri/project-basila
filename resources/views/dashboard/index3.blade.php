@extends('layout.layout')

@php
    $title = 'Penetapan Yudisium';
    $subTitle = 'Tambah';
    $script = '<script src="' . asset('assets/js/data-table.js') . '"></script>';
@endphp

@section('content')
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
                <select class="form-select w-full text-neutral-900 bg-gray-50">
                    <option>INFORMATKA</option>
                </select>
            </div>

            <!-- Semester -->
            <div class="col-span-12 md:col-span-5">
                <label class="block text-sm font-medium text-gray-500 mb-1">Semester</label>
                <select class="form-select w-full text-neutral-900 bg-gray-100">
                    <option>Ganjil 2024/2025</option>
                </select>
            </div>

            <!-- Spacer -->
            <div class="hidden md:block md:col-span-2"></div>

            <!-- Program Studi -->
            <div class="col-span-12 md:col-span-5">
                <label class="block text-sm font-medium text-gray-500 mb-1">Program Studi</label>
                <select class="form-select w-full text-neutral-900 bg-gray-50">
                    <option>S1 Informatika</option>
                </select>
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
                    <h2 class="text-lg font-semibold mb-1">Daftar Mahasiswa Yudisium</h2>
                    <p class="text-sm text-gray-500 mb-4">Berikut daftar yudisium periode Ganjil 2024/2025</p>

                    <!-- Statistik -->
                    <div class="flex gap-6 mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <span class="text-sm text-neutral-950">Total Eligible</span>
                            <span class="font-semibold text-neutral-950">1</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                            <span class="text-sm text-neutral-950">Total Tidak Eligible</span>
                            <span class="font-semibold text-neutral-950">1</span>
                        </div>
                    </div>
            
                <div class="card-body">                    
                    <table id="selection-table" class="border border-neutral-200 dark:border-neutral-600 rounded-lg border-separate	">
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
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-950">
                                    <div class="flex items-center gap-2">
                                        Nama
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-950">
                                    <div class="flex items-center gap-2">
                                        Masa Studi
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-950">
                                    <div class="flex items-center gap-2">
                                        SKS Lulus
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-950">
                                    <div class="flex items-center gap-2">
                                        IPK
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-950">
                                    <div class="flex items-center gap-2">
                                       Predikat
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-950">
                                    <div class="flex items-center gap-2">
                                        Status
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
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
                                        <span class="bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 px-6 py-1.5 rounded-full font-medium text-sm">Eligible</span>
                                    </div>
                                </td>
                                <td>
                                    <a href="javascript:void(0)" class="w-8 h-8 bg-primary-50 dark:bg-primary-600/10 text-primary-600 dark:text-primary-400 rounded-full inline-flex items-center justify-center">
                                        <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
                                    </a>
                                    <a href="javascript:void(0)" class="w-8 h-8 bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 rounded-full inline-flex items-center justify-center">
                                        <iconify-icon icon="lucide:edit"></iconify-icon>
                                    </a>
                                    <a href="javascript:void(0)" class="w-8 h-8 bg-danger-100 dark:bg-danger-600/25 text-danger-600 dark:text-danger-400 rounded-full inline-flex items-center justify-center">
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
                                        <span class="bg-danger-100 dark:bg-danger-600/25 text-danger-600 dark:text-danger-400 px-6 py-1.5 rounded-full font-medium text-sm">Tidak Eligible</span>
                                    </div>
                                </td>
                                <td>
                                    <a href="javascript:void(0)" class="w-8 h-8 bg-primary-50 dark:bg-primary-600/10 text-primary-600 dark:text-primary-400 rounded-full inline-flex items-center justify-center">
                                        <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
                                    </a>
                                    <a href="javascript:void(0)" class="w-8 h-8 bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 rounded-full inline-flex items-center justify-center">
                                        <iconify-icon icon="lucide:edit"></iconify-icon>
                                    </a>
                                    <a href="javascript:void(0)" class="w-8 h-8 bg-danger-100 dark:bg-danger-600/25 text-danger-600 dark:text-danger-400 rounded-full inline-flex items-center justify-center">
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
    <div class="flex flex-col md:flex-row items-center gap-4 mt-6">
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
    </div>
</div>
@endsection
