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
    <h1 class="text-2xl font-semibold text-gray-600 mb-5">{{ __('index4.title') }}</h1>

    <!-- Crypto Home Widgets End -->

    <div class="grid grid-cols-1 3xl:grid-cols-12 gap-6 mt-6">
        <div class="2xl:col-span-12 3xl:col-span-4">
            <div class="grid grid-cols-1 gap-6">

                <div class="card h-full rounded-lg border-0">
                    <div
                        class="card-body yudisium w-full grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 gap-4 items-center">
                        <div class="left">
                            <h1 class="text-xl font-bold mb-5">{{ __('index4.yudisium') }}</h1>
                            <form action="">
                                <div class="w-[60%] flex flex-col gap-2">
                                    <div class="flex justify-between items-center">
                                        <label for="fakultas" class="text-neutral-800">{{ __('index4.faculty') }}</label>
                                        <select name="fakultas" id="fakultas"
                                            class="text-neutral-800 uppercase w-[55%] form-select text-sm">
                                            <option value="">{{ __('index4.select_faculty') }}</option>
                                            {{-- <option value="">-- Pilih Fakultas --</option>
                                            @foreach ($faculties as $faculty)
                                                <option value="{{ $faculty['facultyid'] }}">
                                                    {{ $faculty['facultyname'] }}
                                                </option>
                                                @endforeach --}}
                                    </select>
                                </div>

                                <div class="flex justify-between items-center">
                                    <label for="Semester" class="text-neutral-800">{{ __('index4.semester') }}</label>
                                    <form action="{{ route('index4') }}" method="GET">
                                        <select name="periode" id="periodeSelect"
                                            class="border border-gray-300 rounded-md  w-[55%] p-2 text-gray-600">
                                            <option value="Pilih">{{ __('index3.select_period') }}</option>
                                            @foreach ($periodes as $p)
                                                <option value="{{ $p['value'] }}"
                                                    {{ $periode == $p['value'] ? 'selected' : '' }}>
                                                    {{ $p['label'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </div>
                                <button id="filterButton"
                                    class="text-neutral-100 border background-primary rounded-md shadow-xl w-1/3 px-2 py-1 setPeriodeBtn">
                                    {{ __('index4.show') }}
                                </button>
                            </div>
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
                                        <h2 class="text-4xl text-white font-bold leading-tight">
                                            {{ $countApproval }}
                                        </h2>
                                        <p class="text-sm">{{ __('index4.total_yudisium') }}</p>
                                    </div>
                                </div>

                                {{-- Card Total Mahasiswa --}}
                                <div
                                    class="bg-[#3ea83f] text-white rounded-xl p-5 shadow-lg flex items-center justify-center space-x-4">
                                    <div class="w-16 h-16 bg-[#2f812f] rounded-full flex items-center justify-center">
                                        <iconify-icon icon="ph:student-fill" class="text-white text-4xl"></iconify-icon>
                                    </div>
                                    <div class="flex flex-col text-center">
                                        <h2 class="text-4xl text-white font-bold leading-tight">
                                            {{ $totalMhsYud }}
                                        </h2>
                                        <p class="text-sm">{{ __('index4.total_graduates') }}</p>
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
                                <h6 class="card-title text-lg font-bold mb-2">{{ __('index4.list_title') }}</h6>
                                <p class="text-sm text-neutral-400 mb-5">{{ __('index4.list_subtitle') }}
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
                                                        {{ __('index4.no') }}
                                                    </label>
                                                </div>
                                            </th>
                                            <th scope="col" class="text-neutral-800 dark:text-white"
                                                style="color: black;">
                                                <div class="flex items-center gap-2">
                                                    {{ __('index4.yudisium_number') }}
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
                                                    {{ __('index4.period') }}
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
                                                    {{ __('index4.faculty') }}
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
                                                    {{ __('index4.study_program') }}
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
                                                    {{ __('index4.total_students') }}
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
                                                    {{ __('index4.action') }}
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
                                    <h2 class="text-2xl font-bold mb-4">{{ __('index4.student_detail') }}</h2>
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
                                                            {{ __('index4.no') }}
                                                        </label>
                                                    </div>
                                                </th>
                                                <th scope="col" class="text-neutral-950">
                                                    <div class="flex items-center gap-2">
                                                        {{ __('index4.nim') }}
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
                                                        {{ __('index4.name') }}
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
                                                        {{ __('index4.study_duration') }}
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
                                                        {{ __('index4.credits') }}
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
                                                        {{ __('index4.gpa') }}
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
                                                        {{ __('index4.predicate') }}
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
                                                        {{ __('index4.status') }}
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
                                                        {{ __('index4.reason') }}
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
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('index4.approval_status') }}</label>
                                        <select id="approval" class="form-select w-full text-neutral-900">
                                            <option value="Eligible">{{ __('index4.eligible') }}</option>
                                            <option value="Tidak Eligible">{{ __('index4.not_eligible') }}</option>
                                        </select>

                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('index4.note') }}</label>
                                        <textarea id="catatan" rows="3" class="form-input border border-gray-300 rounded w-full p-2 mb-4" required></textarea>
                                        <button id="btn-simpan"
                                            class="bg-red-600 text-white px-4 py-2 rounded">{{ __('index4.save') }}</button>
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
                updateYudisium: "{{ route('yudicium.update') }}",
                getAllYud: "{{ route('yudicium.getAll') }}"
            };
        </script>
        <script src="{{ asset('assets/js/popup-yudicium.js') }}" defer></script>
        <script>
            document.getElementsByClassName('setPeriodeBtn').addEventListener('click', function() {
                const periode = document.getElementById('periodeSelect').value;
                window.location.href = `?periode=${encodeURIComponent(periode)}`;
            });
        </script>
    </div>
@endsection
