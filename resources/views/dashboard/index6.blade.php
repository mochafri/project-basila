@extends('layout.layout')
@php
    $title = 'Dashboard';
    $subTitle = 'LMS / Learning System';
    $script = '
<script></script>';
@endphp

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-600">Laporan</h1>
        <div class="flex items-center space-x-4">
            <!-- Dropdown Periode -->
            <div class="relative">
                <select id="periodeSelect"
                    class="text-gray-400 border border-gray-300 text-sm rounded-md py-2 pl-10 pr-4 appearance-none focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option selected>Ganjil 2024/2025</option>
                    <option>Genap 2024/2025</option>
                    <option>Ganjil 2025/2026</option>
                    <option>Genap 2025/2026</option>
                </select>
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <iconify-icon icon="ph:graduation-cap-light"></iconify-icon>
                </div>
            </div>

            <!-- Tombol Tetapkan Periode -->
            <button
                class="bg-[#e51411] hover:bg-red-700 text-white font-bold py-2 px-6 rounded-full flex items-center space-x-2">
                <iconify-icon icon="ph:graduation-cap-bold"></iconify-icon>
                <span>TETAPKAN PERIODE</span>
            </button>
        </div>
    </div>
    <div class="grid grid-cols-1 3xl:grid-cols-12 gap-6 mt-6">
        <div class="2xl:col-span-12 3xl:col-span-4">
            <div class="grid grid-cols-1 gap-6">
                <div class="card h-full rounded-lg border-0">
                    <div
                        class="card-body yudisium w-full grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 gap-4 items-center">
                        <div class="left">
                        <img src="{{ asset('assets/basila_images/hero.png')}}" alt="">
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

                                {{-- Card Total SK Terbit --}}
                                <div
                                    class="bg-[#ffb800] text-white rounded-xl p-5 shadow-lg flex items-center justify-center space-x-4">
                                    <div class="w-16 h-16 bg-[#d19c00] rounded-full flex items-center justify-center">
                                        <iconify-icon icon="mdi:file-document" class="text-white text-4xl"></iconify-icon>
                                    </div>
                                    <div class="flex flex-col text-center">
                                        <h2 class="text-4xl text-white font-bold leading-tight">43</h2>
                                        <p class="text-sm">Total SK Terbit</p>
                                    </div>
                                </div>
                                {{-- Card Total DKD --}}
                                <div
                                    class="bg-[#268a04] text-white rounded-xl p-5 shadow-lg flex items-center justify-center space-x-4">
                                    <div class="w-16 h-16 bg-[#207504] rounded-full flex items-center justify-center">
                                        <iconify-icon icon="flowbite:mail-box-outline"
                                            class="text-white text-4xl"></iconify-icon>
                                    </div>
                                    <div class="flex flex-col text-center">
                                        <h2 class="text-4xl text-white font-bold leading-tight">43</h2>
                                        <p class="text-sm">Total DKD</p>
                                    </div>
                                </div>

                                {{-- Card Total Reservasi PISN --}}
                                <div
                                    class="bg-[#0094d8] text-white rounded-xl p-5 shadow-lg flex items-center justify-center space-x-4">
                                    <div class="w-16 h-16 bg-[#067ab0] rounded-full flex items-center justify-center">
                                        <iconify-icon icon="fa6-solid:file-signature"
                                            class="text-white text-4xl"></iconify-icon>
                                    </div>
                                    <div class="flex flex-col text-center">
                                        <h2 class="text-4xl text-white font-bold leading-tight">214</h2>
                                        <p class="text-sm">Total Reservasi PISN</p>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-2  justify-center">
                                    <div class="flex items-center justify-between font-medium">
                                        <div class="flex items-center gap-2">
                                            <span class="ri-circle-fill circle-icon text-green-500 w-auto"></span>
                                            <p class="text-neutral-800">Total Eligible</p>
                                        </div>
                                        <p class="text-neutral-800">214</p>
                                    </div>
                                    <div class="flex items-center justify-between font-medium">
                                        <div class="flex items-center gap-2">
                                            <span class="ri-circle-fill circle-icon text-red-500 w-auto"></span>
                                            <p class="text-neutral-800">Total Tidak Eligible</p>
                                        </div>
                                        <p class="text-neutral-800">0</p>
                                    </div>
                                </div>
                            </div>
                            <div class="GenerateLaporan my-5">
                                <h1 class="text-xl font-bold mb-5">Generate Laporan</h1>
                                <form action="">
                                    <div class="w-[60%] flex flex-col gap-2 font-medium">
                                        <div class="flex justify-between items-center">
                                            <label for="fakultas" class="text-neutral-500">Jenis Laporan</label>
                                            <div class="flex flex-col space-y-1">
                                                <button id="btn" type="button"
                                                    class="w-56 bg-neutral-300 rounded-md h-8">
                                                    <div
                                                        class="flex flex-row items-center justify-between px-3 text-neutral-500">
                                                        <span id="selected">Yudisium</span>
                                                        <iconify-icon icon="ph:caret-down-bold"
                                                            class="text-gray-500 rotate-[-90deg]" id="caret-down"></iconify-icon>
                                                    </div>
                                                </button>
                                                <div id="dropdown">
                                                    <ul
                                                        class="absolute bg-white border border-gray-300 shadow-lg w-56 rounded-lg text-center z-10">
                                                        <li class="p-2 hover:bg-gray-100 cursor-pointer">Yudisium</li>
                                                        <li class="p-2 hover:bg-gray-100 cursor-pointer">Tidak Yudisium</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <label for="Semester" class="text-neutral-500">Format</label>
                                            <div class="flex flex-col space-y-1">
                                                <button id="btn2" type="button"
                                                    class="text-neutral-950 w-56 bg-neutral-300 rounded-md h-8">
                                                    <div class="flex justify-between items-center px-3 text-neutral-500">
                                                        <span id="selected2">Excel</span>
                                                        <iconify-icon icon="ph:caret-down-bold"
                                                            class="text-gray-500 rotate-[-90deg]" id="caret-down2"></iconify-icon>
                                                    </div>
                                                </button>
                                                <div id="dropdown2">
                                                    <ul
                                                        class="absolute bg-white border border-gray-300 shadow-lg w-56 rounded-lg text-center">
                                                        <li class="p-2 hover:bg-gray-100 cursor-pointer">Excel</li>
                                                        <li class="p-2 hover:bg-gray-100 cursor-pointer">PDF</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            <button
                                class="text-neutral-100 border bg-blue-800 rounded-md shadow-xl w-1/3 px-2 py-1">Generate</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/dropdown.js') }}">
        toggleDropdown('btn', 'dropdown', 'selected', 'caret-down');
        toggleDropdown('btn2', 'dropdown2', 'selected2', 'caret-down2);
    </script>
@endpush
