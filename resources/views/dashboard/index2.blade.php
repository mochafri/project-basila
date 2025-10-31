@extends('layout.layout')

@php
    $title = 'Penetapan Yudisium';
    $subTitle = 'Daftar Yudisium';
    $script = '
                <script src="' . asset('assets/js/data-table.js') . '" defer></script>
            ';
@endphp

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <h1 class="text-2xl font-semibold text-gray-600 mb-5 lg:col-span-12">{{ __('penetapan.title') }}</h1>
        <!-- Filter dan Statistik -->
        <div class="col-span-12 bg-white p-6 rounded-xl shadow-lg">
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">

                <!-- Filter -->
                <div class="flex flex-col gap-3 w-full sm:w-auto">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                        <label for="semester" class="text-sm font-medium text-neutral-700 whitespace-nowrap">
                            {{ __('penetapan.semester') }}
                        </label>
                        <select id="semester" name="semester" class="form-select text-neutral-950 w-full sm:w-48">
                            <option>Ganjil 2024/2025</option>
                            <option>Genap 2024/2025</option>
                            <option>Ganjil 2025/2026</option>
                            <option>Genap 2025/2026</option>
                        </select>
                    </div>
                    <button
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold px-5 py-2 rounded-lg shadow-md transition w-full sm:w-fit">
                        {{ __('penetapan.show') }}
                    </button>
                </div>

                <!-- Statistik -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 w-full sm:w-auto">

                    <!-- Card Total Yudisium -->
                    <div
                        class="bg-[#e51411] text-white rounded-xl p-5 shadow-lg flex items-center justify-center space-x-4">
                        <div class="w-16 h-16 bg-[#c7110f] rounded-full flex items-center justify-center">
                            <iconify-icon icon="clarity:gavel-solid" class="text-white text-4xl"></iconify-icon>
                        </div>
                        <div class="flex flex-col text-center">
                            <h2 class="text-4xl text-white font-bold leading-tight">{{ $countApproval }}</h2>
                            <p class="text-sm">{{ __('penetapan.total_yudisium') }}</p>
                            <h2 class="text-4xl text-white font-bold leading-tight">
                                {{ optional($datas->first())->approval_status === 'Approved' ? $countApproval : 0 }}
                            </h2>
                            <p class="text-sm">Total Yudisium</p>
                        </div>
                    </div>

                    <!-- Card Total Mahasiswa -->
                    <div
                        class="bg-[#3ea83f] text-white rounded-xl p-5 shadow-lg flex items-center justify-center space-x-4 w-full sm:w-auto">
                        <div class="w-16 h-16 bg-[#2f812f] rounded-full flex items-center justify-center">
                            <iconify-icon icon="ph:student-fill" class="text-white text-4xl"></iconify-icon>
                        </div>
                        <div class="flex flex-col text-center sm:text-left">
                            <h2 class="text-4xl text-white font-bold">{{ $totalMhsYud }}</h2>
                            <p class="text-sm">{{ __('penetapan.total_graduate') }}</p>
                            <h2 class="text-4xl text-white font-bold">
                                {{ optional($datas->first())->approval_status === 'Approved' ? $totalMhsYud : 0 }}
                            </h2>
                            <p class="text-sm">Total Mahasiswa</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-12 mt-6">
        <div class="col-span-12">
            <div class="card border-0 overflow-hidden">
                <div class="card-header flex justify-between items-center">
                    <div>
                        <h6 class="card-title mb-0 text-lg font-bold">{{ __('penetapan.list_title') }}</h6>
                        <h6 class="card-title mb-0 text-xs text-gray-600">{{ __('penetapan.list_subtitle') }}
                        </h6>
                    </div>
                    <!-- Tombol Tambah -->
                    <button
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold px-5 py-2 rounded-lg shadow-md transition">
                        <a href="{{ route('index3') }}">{{ __('penetapan.add') }}</a>
                    </button>
                </div>
                <div class="card-body">
                    <table id="selection-table" class="border border-neutral-200  rounded-lg border-separate">
                        <thead>
                            <tr>
                                <th scope="col" class="text-neutral-800 dark:text-white">
                                    <div class="form-check style-check flex items-center">
                                        <label class="ms-2 text-neutral-950 form-check-label" for="serial">
                                            {{ __('penetapan.no') }}
                                        </label>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-950">
                                    <div class="flex items-center gap-2">
                                        {{ __('penetapan.sk_number') }}
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-950">
                                    <div class="flex items-center gap-2">
                                        {{ __('penetapan.yudisium_number') }}
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-950">
                                    <div class="flex items-center gap-2">
                                        {{ __('penetapan.period') }}
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-950">
                                    <div class="flex items-center gap-2">
                                        {{ __('penetapan.faculty') }}
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-950">
                                    <div class="flex items-center gap-2">
                                        {{ __('penetapan.study_program') }}
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-950">
                                    <div class="flex items-center gap-2">
                                        {{ __('penetapan.approval') }}
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-950">
                                    <div class="flex items-center gap-2">
                                        {{ __('penetapan.reason') }}
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-950">
                                    <div class="flex items-center gap-2">
                                        {{ __('penetapan.status_dkd') }}
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="text-neutral-950">
                                    <div class="flex items-center gap-2">
                                        {{ __('penetapan.action') }}
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datas as $data)
                                <tr> <!-- class="{{ $loop->odd ? 'bg-blue-100' : 'bg-white' }}"  untuk membedakan background row-->
                                    <td>
                                        <div class="form-check style-check flex items-center">
                                            {{-- <input class="form-check-input" type="checkbox"> --}}
                                            <label class="ms-2 form-check-label">
                                                {{ $data->id }}
                                            </label>
                                        </div>
                                    </td>
                                    <td><a href="javascript:void(0)" class="text-primary-600"></a></td>
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
                                                {{ $data->facultyname }}
                                            </h6>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex items-center">
                                            <h6>{{ $data->prodyname }}</h6>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($data->approval_status === 'Approved')
                                            <div class="flex items-center">
                                                <span
                                                    class="bg-success-100  text-success-600  px-6 py-1.5 rounded-full font-medium text-sm">Approved</span>
                                            </div>
                                        @elseif($data->approval_status === 'Rejected')
                                            <div class="flex items-center">
                                                <span
                                                    class="bg-danger-100  text-danger-600  px-6 py-1.5 rounded-full font-medium text-sm">Rejected</span>
                                            </div>
                                        @else
                                            <div class="flex items-center">
                                                <span
                                                    class="bg-warning-100  text-warning-600  px-6 py-1.5 rounded-full font-medium text-sm">{{ $data->approval_status}}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($data->approval_status === 'Rejected' && !empty($data->catatan))
                                            <div class="flex items-start justify-start text-gray-700 px-2 py-1 leading-relaxed text-sm whitespace-pre-line break-words">
                                                {{ $data->catatan }}
                                            </div>
                                        @else
                                            <div class="flex items-center justify-center text-gray-500">
                                                -
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="flex items-center">
                                            <span
                                                class="bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 px-6 py-1.5 rounded-full font-medium text-sm">Done</span>
                                        </div>
                                    </td>
                                    <td>
                                        <button
                                            class="btn-popup w-8 h-8 bg-primary-50 dark:bg-primary-600/10 text-primary-600 dark:text-primary-400 rounded-full inline-flex items-center justify-center"
                                            data-id={{ $data->id }}>
                                            <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
                                        </button>
                                        <a href="{{ route('index3', ['id' => $data->id]) }}"
                                            class="w-8 h-8 bg-warning-100 dark:bg-warning-600/25 text-warning-600 dark:text-warning-400 rounded-full inline-flex items-center justify-center">
                                            <iconify-icon icon="mingcute:edit-2-line"></iconify-icon>
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

                    {{-- PopUp table --}}

                    <div id="popup"
                        class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                        <div
                            class="bg-white p-6 rounded-lg shadow-lg w-[90%] max-w-6xl max-h-[90%] overflow-y-auto relative text-center">
                            <h2 class="text-2xl font-bold mb-4">{{ __('penetapan.detail_student') }}</h2>
                            <button id="popup-close" class="absolute top-4 right-4 text-gray-600 hover:text-gray-800">
                                <iconify-icon icon="mdi:close" class="text-2xl"></iconify-icon>
                            </button>

                            <table id="popup-table" class="border border-neutral-200  rounded-lg border-separate">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-neutral-800 dark:text-white">
                                            <div class="form-check style-check flex items-center">
                                                <label class="ms-2 text-neutral-950 form-check-label" for="serial">
                                                    {{ __('penetapan.no') }}
                                                </label>
                                            </div>
                                        </th>
                                        <th scope="col" class="text-neutral-950">
                                            <div class="flex items-center gap-2">
                                                {{ __('penetapan.nim') }}
                                                <svg class="w-4 h-4 ms-1" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                                </svg>
                                            </div>
                                        </th>
                                        <th scope="col" class="text-neutral-950">
                                            <div class="flex items-center gap-2">
                                                {{ __('penetapan.name') }}
                                                <svg class="w-4 h-4 ms-1" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                                </svg>
                                            </div>
                                        </th>
                                        <th scope="col" class="text-neutral-950">
                                            <div class="flex items-center gap-2">
                                                {{ __('penetapan.study_duration') }}
                                                <svg class="w-4 h-4 ms-1" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                                </svg>
                                            </div>
                                        </th>
                                        <th scope="col" class="text-neutral-950">
                                            <div class="flex items-center gap-2">
                                                {{ __('penetapan.credits') }}
                                                <svg class="w-4 h-4 ms-1" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                                </svg>
                                            </div>
                                        </th>
                                        <th scope="col" class="text-neutral-950">
                                            <div class="flex items-center gap-2">
                                                {{ __('penetapan.') }}
                                                <svg class="w-4 h-4 ms-1" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                    viewBox="0 0 24 24">
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
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                    viewBox="0 0 24 24">
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
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                    viewBox="0 0 24 24">
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
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                    viewBox="0 0 24 24">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- @foreach ($datas as $data)
        <form action="{{ route('yudicium.approve', $data->id) }}" method="POST">
            @csrf
            @method('PUT')
            <button type="submit" class="btn bg-red-500 text-white btn-sm mt-5">Approve</button>
        </form>
        @endforeach --}}
    </div>
    <script src="{{ asset('assets/js/popup-yudicium.js') }}" defer></script>
@endsection
