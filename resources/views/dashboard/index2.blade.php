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
                                    class="absolute bg-white border border-gray-300 shadow-lg w-60 rounded-lg text-neutral-700">
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
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold px-5 py-2 rounded-lg shadow-md transition">
                        <a href="{{ route('index3') }}">+ Tambah</a>
                    </button>
                </div>
                <div class="card-body">
                    <table id="selection-table"
                        class="border border-neutral-200  rounded-lg border-separate	">
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
                            @foreach ($datas as $data)
                                <tr> <!-- class="{{ $loop->odd ? 'bg-blue-100' : 'bg-white' }}"  untuk membedakan background row-->
                                    <td>
                                        <div class="form-check style-check flex items-center">
                                            <input class="form-check-input" type="checkbox">
                                            <label class="ms-2 form-check-label">
                                                {{ $data->id }}
                                            </label>
                                        </div>
                                    </td>
                                    <td><a href="javascript:void(0)" class="text-primary-600">KR.418/AKD15/AKD
                                            BAA/2024</a></td>
                                    <td>
                                        <div class="flex items-center">
                                            <h6 class="text-base mb-0 ">
                                                {{ $data->no_yudicium }}
                                            </h6>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex items-center">
                                            <h6>
                                                {{ $data->periode }}
                                            </h6>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <h6>
                                                {{ $data->fakultas }}
                                            </h6>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex items-center">
                                            <h6>{{ $data->prodi }}</h6>
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
                                                class="bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 px-6 py-1.5 rounded-full font-medium text-sm">Done</span>
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
                            @endforeach

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
