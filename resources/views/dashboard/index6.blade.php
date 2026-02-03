@extends('layout.layout')
@php
    $title = 'Dashboard';
    $subTitle = 'LMS / Learning System';
    $script = '
                    <script src="' . asset('assets/js/fakultasSelect.js') . '"></script>
                ';
@endphp

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-600">{{ __('index6.title') }}</h1>
        <div class="flex items-center space-x-4">
            <form action="{{ route('index6') }}" method="GET" class="flex items-center space-x-4">

                <select name="periode" class="border border-gray-300 rounded-md p-2 text-gray-600">
                    <option value="">-- Pilih Periode --</option>
                    @foreach ($periodes as $p)
                        <option value="{{ $p['value'] }}" {{ $periode == $p['value'] ? 'selected' : '' }}>
                            {{ $p['label'] }}
                        </option>
                    @endforeach
                </select>

                <button type="submit"
                    class="bg-[#e51411] hover:bg-red-700 text-white font-bold py-2 px-6 rounded-full flex items-center space-x-2">
                    <iconify-icon icon="ph:graduation-cap-bold"></iconify-icon>
                    <span>{{ __('index6.set_period') }}</span>
                </button>

            </form>
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
                                        <h2 class="text-4xl text-white font-bold leading-tight">{{ $countApproval }}</h2>
                                        <p class="text-sm">{{ __('index6.total_yudisium') }}</p>
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
                                        <p class="text-sm">{{ __('index6.total_graduate') }}</p>
                                    </div>
                                </div>

                                {{-- Card Total SK Terbit --}}
                                <div
                                    class="bg-[#ffb800] text-white rounded-xl p-5 shadow-lg flex items-center justify-center space-x-4">
                                    <div class="w-16 h-16 bg-[#d19c00] rounded-full flex items-center justify-center">
                                        <iconify-icon icon="mdi:file-document" class="text-white text-4xl"></iconify-icon>
                                    </div>
                                    <div class="flex flex-col text-center">
                                        <h2 class="text-4xl text-white font-bold leading-tight">{{ $totalMhsYud }}</h2>
                                        <p class="text-sm">{{ __('index6.total_sk') }}</p>
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
                                        <h2 class="text-4xl text-white font-bold leading-tight">{{ $totalMhsYud }}</h2>
                                        <p class="text-sm">{{ __('index6.total_dkd') }}</p>
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
                                        <p class="text-sm">{{ __('index6.total_pisn') }}</p>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-2  justify-center">
                                    <div class="flex items-center justify-between font-medium">
                                        <div class="flex items-center gap-2">
                                            <span class="ri-circle-fill circle-icon text-green-500 w-auto"></span>
                                            <p class="text-neutral-800">{{ __('index6.eligible') }}</p>
                                        </div>
                                        <p class="text-neutral-800">{{ $totalMhsYud }}</p>
                                    </div>
                                    <div class="flex items-center justify-between font-medium">
                                        <div class="flex items-center gap-2">
                                            <span class="ri-circle-fill circle-icon text-red-500 w-auto"></span>
                                            <p class="text-neutral-800">{{ __('index6.not_eligible') }}</p>
                                        </div>
                                        <p class="text-neutral-800">0</p>
                                    </div>
                                </div>
                            </div>
                            <div class="GenerateLaporan my-5">
                                <h1 class="text-xl font-bold mb-5">{{ __('index6.generate') }}</h1>
                                <form action="{{ route('yudisium.print.rekap') }}" method="GET" target="_blank">
                                    <div class="w-[60%] flex flex-col gap-2 font-medium">

                                        {{-- Fakultas --}}
                                        <div class="flex justify-between items-center">
                                            <label class="text-neutral-500">{{ __('index6.faculty') }}</label>
                                            <select name="fakultas_id" id="fakultas"
                                                class="text-neutral-500 w-[50%] form-select text-sm" required>
                                                <option value="">{{ __('index6.select_faculty') }}</option>
                                            </select>
                                        </div>

                                        {{-- Periode --}}
                                        <div class="flex justify-between items-center">
                                            <label class="text-neutral-500">{{ __('index6.period') }}</label>
                                            <select name="periode"
                                                class="border border-gray-300 w-[50%] form-select text-neutral-500 text-sm"
                                                required>
                                                <option value="">{{ __('index6.select_period') }}</option>
                                                @foreach ($periodes as $p)
                                                    <option value="{{ $p['value'] }}">{{ $p['label'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <button
                                            class="text-neutral-100 border bg-blue-800 rounded-md shadow-xl w-1/3 px-2 py-1">
                                            {{ __('index6.btn_report') }}
                                        </button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const routes = {
            showFaculties: "{{ route('show.faculties') }}"
        };
    </script>


@endsection