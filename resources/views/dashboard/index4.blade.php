@extends('layout.layout')
@php
    $title = 'Approval Yudisium';
    $subTitle = 'Cryptocracy';
    $script = '
<script src="' . asset('assets/js/homeFourChart.js') . '"></script>
<script src="' . asset('assets/js/data-table.js') . '"></script>
';
@endphp

@section('content')
    <!-- Crypto Home Widgets Start -->
    <h1 class="text-2xl font-bold text-neutral-400 mb-5">Approval Yudisium</h1>

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
                                        <div class="flex flex-col space-y-1">
                                            <button id="btn" type="button"
                                                class="text-neutral-950 w-56 border border-neutral-200 rounded-md h-8">
                                                <div class="flex flex-row items-center justify-between px-2">
                                                    <span id="selected">Informatika</span>
                                                    <iconify-icon icon="ph:caret-down-bold"
                                                        class="text-gray-500 rotate-[-90deg]"
                                                        id="caret-down"></iconify-icon>
                                                </div>
                                            </button>
                                            <div id="dropdown">
                                                <ul
                                                    class="absolute bg-white border border-gray-300 shadow-lg w-56 rounded-lg text-center z-10">
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
                                    <div class="flex justify-between items-center">
                                        <label for="Semester" class="text-neutral-800">Semester</label>
                                        <div class="flex flex-col space-y-1">
                                            <button id="btn2" type="button"
                                                class="text-neutral-950 w-56 border border-neutral-200 rounded-md h-8">
                                                <div class="flex justify-between items-center px-2">
                                                    <span id="selected2">Ganjil 2024/2025</span>
                                                    <iconify-icon icon="ph:caret-down-bold" id="caret-down2"
                                                        class="text-gray-500 rotate-[-90deg]"></iconify-icon>
                                                </div>
                                            </button>
                                            <div id="dropdown2">
                                                <ul
                                                    class="absolute bg-white border border-gray-300 shadow-lg w-56 rounded-lg text-center">
                                                    <li class="p-2 hover:bg-gray-100 cursor-pointer">Ganjil 2024/2025</li>
                                                    <li class="p-2 hover:bg-gray-100 cursor-pointer">Genap 2024/2025</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <button
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
                                        <h2 class="text-4xl text-white font-bold leading-tight">43</h2>
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
                                        <h2 class="text-4xl text-white font-bold leading-tight">214</h2>
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
                                <table id="selection-table"
                                    class="border border-neutral-200 dark:border-neutral-600 rounded-lg border-separate	">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="text-neutral-800 dark:text-white"
                                                style="color: black;">
                                                <div class="form-check style-check flex items-center">
                                                    <input class="form-check-input" id="serial" type="checkbox">
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
                                        <tr>
                                            <td>
                                                <div class="form-check style-check flex items-center">
                                                    <input class="form-check-input" type="checkbox">
                                                    <label class="ms-2 form-check-label">
                                                        01
                                                    </label>
                                                </div>
                                            </td>
                                            <td><a href="javascript:void(0)" class="text-primary-600">#526534</a></td>
                                            <td>
                                                <div class="flex items-center">
                                                    <img src="{{ asset('assets/images/user-list/user-list1.png') }}"
                                                        alt="" class="shrink-0 me-3 rounded-lg">
                                                    <h6 class="text-base mb-0 font-medium grow">Kathryn Murphy</h6>
                                                </div>
                                            </td>
                                            <td>25 Jan 2025</td>
                                            <td>$200.00</td>
                                            <td> <span
                                                    class="bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 px-6 py-1.5 rounded-full font-medium text-sm">Paid</span>
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
                                                        02
                                                    </label>
                                                </div>
                                            </td>
                                            <td><a href="javascript:void(0)" class="text-primary-600">#696589</a></td>
                                            <td>
                                                <div class="flex items-center">
                                                    <img src="{{ asset('assets/images/user-list/user-list2.png') }}"
                                                        alt="" class="shrink-0 me-3 rounded-lg">
                                                    <h6 class="text-base mb-0 font-medium grow">Annette Black</h6>
                                                </div>
                                            </td>
                                            <td>25 Jan 2025</td>
                                            <td>$200.00</td>
                                            <td> <span
                                                    class="bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 px-6 py-1.5 rounded-full font-medium text-sm">Paid</span>
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
                                            <td><a href="javascript:void(0)" class="text-primary-600">#256584</a></td>
                                            <td>
                                                <div class="flex items-center">
                                                    <img src="{{ asset('assets/images/user-list/user-list3.png') }}"
                                                        alt="" class="shrink-0 me-3 rounded-lg">
                                                    <h6 class="text-base mb-0 font-medium grow">Ronald Richards</h6>
                                                </div>
                                            </td>
                                            <td>10 Feb 2025</td>
                                            <td>$200.00</td>
                                            <td> <span
                                                    class="bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 px-6 py-1.5 rounded-full font-medium text-sm">Paid</span>
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
                                                        04
                                                    </label>
                                                </div>
                                            </td>
                                            <td><a href="javascript:void(0)" class="text-primary-600">#526587</a></td>
                                            <td>
                                                <div class="flex items-center">
                                                    <img src="{{ asset('assets/images/user-list/user-list4.png') }}"
                                                        alt="" class="shrink-0 me-3 rounded-lg">
                                                    <h6 class="text-base mb-0 font-medium grow">Eleanor Pena</h6>
                                                </div>
                                            </td>
                                            <td>10 Feb 2025</td>
                                            <td>$150.00</td>
                                            <td> <span
                                                    class="bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 px-6 py-1.5 rounded-full font-medium text-sm">Paid</span>
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
                                                        05
                                                    </label>
                                                </div>
                                            </td>
                                            <td><a href="javascript:void(0)" class="text-primary-600">#105986</a></td>
                                            <td>
                                                <div class="flex items-center">
                                                    <img src="{{ asset('assets/images/user-list/user-list5.png') }}"
                                                        alt="" class="shrink-0 me-3 rounded-lg">
                                                    <h6 class="text-base mb-0 font-medium grow">Leslie Alexander</h6>
                                                </div>
                                            </td>
                                            <td>15 March 2025</td>
                                            <td>$150.00</td>
                                            <td> <span
                                                    class="bg-warning-100 dark:bg-warning-600/25 text-warning-600 dark:text-warning-400 px-6 py-1.5 rounded-full font-medium text-sm">Pending</span>
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
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/dropdown.js') }}">
        toggleDropdown('btn', 'dropdown', 'selected', 'caret-down');
        toggleDropdown('btn2', 'dropdown2', 'selected2', 'caret-down2');
    </script>
@endpush
