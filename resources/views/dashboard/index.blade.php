@extends('layout.layout')

@php
    $script = '<script src="' . asset('assets/js/yudisiumChart.js') . '"></script>';
@endphp

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-600">Dashboard</h1>
        <div class="flex items-center space-x-4">
            <!-- Dropdown Periode -->
            <div class="relative">
                <div class="flex items-end">
                    <button id="periode-btn" type="button"
                        class="text-gray-400 border text-sm bg-white rounded-md py-2 pl-10 pr-4 appearance-none">
                        <div class="flex items-center space-x-2">
                            <span id="periode-selected">Periode Ganjil 2024/2025</span>
                            <iconify-icon icon="ph:caret-down-bold" id="caret-down" class="text-gray-500 rotate-[-90deg]"></iconify-icon>
                        </div>
                    </button>
                    <div id="periode-dropdown">
                        <ul class="absolute left-0 w-full bg-white border rounded-md shadow-lg z-10 text-center text-neutral-700">
                            <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Periode Ganjil 2024/2025</li>
                            <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Periode Genap 2024/2025</li>
                            <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Periode Ganjil 2023/2024</li>
                        </ul>
                    </div>
                </div>
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

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Card Total Yudisium --}}
        <div class="bg-[#e51411] text-white rounded-xl p-5 shadow-lg flex items-center justify-center space-x-4">
            <div class="w-16 h-16 bg-[#c7110f] rounded-full flex items-center justify-center">
                <iconify-icon icon="clarity:gavel-solid" class="text-white text-4xl"></iconify-icon>
            </div>
            <div class="flex flex-col text-center">
                <h2 class="text-4xl text-white font-bold leading-tight">43</h2>
                <p class="text-sm">Total Yudisium</p>
            </div>
        </div>

        {{-- Card Total Mahasiswa --}}
        <div class="bg-[#3ea83f] text-white rounded-xl p-5 shadow-lg flex items-center justify-center space-x-4">
            <div class="w-16 h-16 bg-[#2f812f] rounded-full flex items-center justify-center">
                <iconify-icon icon="ph:student-fill" class="text-white text-4xl"></iconify-icon>
            </div>
            <div class="flex flex-col text-center">
                <h2 class="text-4xl text-white font-bold leading-tight">214</h2>
                <p class="text-sm">Total Mahasiswa</p>
            </div>
        </div>

        {{-- Card Total SK Terbit --}}
        <div class="bg-[#ffb800] text-white rounded-xl p-5 shadow-lg flex items-center justify-center space-x-4">
            <div class="w-16 h-16 bg-[#d19c00] rounded-full flex items-center justify-center">
                <iconify-icon icon="mdi:file-document" class="text-white text-4xl"></iconify-icon>
            </div>
            <div class="flex flex-col text-center">
                <h2 class="text-4xl text-white font-bold leading-tight">43</h2>
                <p class="text-sm">Total SK Terbit</p>
            </div>
        </div>

        {{-- Card Total Reservasi PISN --}}
        <div class="bg-[#0094d8] text-white rounded-xl p-5 shadow-lg flex items-center justify-center space-x-4">
            <div class="w-16 h-16 bg-[#067ab0] rounded-full flex items-center justify-center">
                <iconify-icon icon="fa6-solid:file-signature" class="text-white text-4xl"></iconify-icon>
            </div>
            <div class="flex flex-col text-center">
                <h2 class="text-4xl text-white font-bold leading-tight">214</h2>
                <p class="text-sm">Total Reservasi PISN</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <div class="flex justify-between items-center mb-4">
                <!-- Judul dan Deskripsi -->
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Periode Yudisium</h3>
                    <p class="text-xs text-gray-500">Berikut detail yudisium mahasiswa periode ganjil 2024/2025</p>
                </div>

                <!-- Total Yudisium dengan Gavel -->
                <div class="flex items-center space-x-2">
                    <div class="bg-red-600 px-4 py-3 rounded-full">
                        <iconify-icon icon="clarity:gavel-solid" class="text-white text-2xl"></iconify-icon>
                    </div>
                    <div class="flex flex-col justify-center items-center text-center">
                        <h2 class="text-3xl font-bold text-red-600 leading-tight">43</h2>
                        <p class="text-xs text-red-600">Total Yudisium</p>
                    </div>
                </div>
            </div>

            <!-- Chart -->
            <div id="yudisiumBarChart" class="apexcharts-tooltip-style-1"></div>
        </div>


        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <!-- Header Section -->
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Predikat Mahasiswa</h3>
                    <p class="text-xs text-gray-500">Berikut detail predikat mahasiswa periode ganjil 2024/2025</p>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="bg-green-600 px-4 py-3 rounded-full">
                        <iconify-icon icon="ph:student-fill" class="text-white text-xl"></iconify-icon>
                    </div>
                    <div class="flex flex-col justify-center items-center text-center">
                        <h2 class="text-3xl font-bold text-green-600 leading-tight">214</h2>
                        <p class="text-xs text-green-600">Total Yudisium</p>
                    </div>
                </div>
            </div>

            <!-- Progress Bars + Label -->
            @foreach ([['label' => 'Istimewa (Summa Cumlaude)', 'value' => 24.8], ['label' => 'Dengan Pujian (Cumlaude)', 'value' => 24.8], ['label' => 'Sangat Memuaskan (Very Good)', 'value' => 24.8], ['label' => 'Memuaskan (Good)', 'value' => 24.8], ['label' => 'Tanpa Predikat', 'value' => 24.8]] as $item)
                <div class="flex flex-col gap-1 mb-4">
                    <!-- Progress Bar -->
                    <div class="flex items-center gap-3">
                        <div class="w-[100px] bg-red-500 text-white text-xs font-semibold text-center rounded-full py-0.5">
                            {{ $item['value'] }}%
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-red-500 h-2 rounded-full" style="width: {{ $item['value'] }}%"></div>
                        </div>
                    </div>

                    <!-- Label Keterangan -->
                    <div class="flex justify-between text-sm text-gray-700">
                        <span>{{ $item['label'] }}</span>
                        <span>20 / 214</span>
                    </div>
                </div>
            @endforeach
        </div>

    </div>


    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-5 rounded-lg shadow">
            <h4 class="text-xl font-semibold mb-2">Status Penerbitan SK</h4>
            <h5 class="text-sm font-normal mb-2 text-gray-500">Berikut detail penerbitan SK</h5>
            <ul class="text-sm space-y-1">
                <li class="text-yellow-500">🟡 Total Waiting: 0</li>
                <li class="text-green-600">🟢 Total Approved: 0</li>
                <li class="text-red-600">🔴 Total Rejected: 0</li>
                <li class="text-blue-600">🔵 Total Done: 43</li>
            </ul>
        </div>

        <div class="bg-white p-5 rounded-lg shadow">
            <h4 class="text-xl font-semibold mb-2">Status PISN</h4>
            <h5 class="text-sm font-normal mb-2 text-gray-500">Berikut detail reservasi PISN</h5>
            <ul class="text-sm space-y-1">
                <li class="text-green-600">🟢 Total Eligible: 214</li>
                <li class="text-red-600">🔴 Total Tidak Eligible: 0</li>
            </ul>
        </div>

        <div class="bg-white p-5 rounded-lg shadow">
            <h4 class="text-xl font-semibold mb-2">Penerbitan Dokumen Kelulusan</h4>
            <h5 class="text-sm font-normal mb-2 text-gray-500">Berikut detail penerbitan dokumen kelulusan digital</h5>
            <ul class="text-sm space-y-1">
                <li class="text-yellow-500">🟡 Total Waiting: 0</li>
                <li class="text-green-600">🟢 Total Approved: 0</li>
                <li class="text-red-600">🔴 Total Rejected: 0</li>
                <li class="text-blue-600">🔵 Total Done: 214</li>
            </ul>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/dropdown.js') }}"></script>
    <script>
        // Inisialisasi dropdown untuk periode
        toggleDropdown('periode-btn', 'periode-dropdown', 'periode-selected', 'caret-down');
    </script>
@endpush
