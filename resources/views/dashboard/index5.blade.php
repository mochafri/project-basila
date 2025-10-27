@extends('layout.layout')

@php
    $title = 'Penetapan Yudisium';
    $subTitle = 'Tambah';
    $id = request('id');
    $script = '
        <script src="' . asset('assets/js/data-table.js') . '"></script>
        <script src="' . asset('assets/js/getMahasiswa.js') . '"></script>
        <script src="' . asset('assets/js/updateYudicium.js') . '"></script>
        <script src="' . asset('assets/js/modalStatus.js') . '"></script>
        <script src="' . asset('assets/js/buttonTetapkan.js') . '"></script>
    ';
@endphp

@section('content')
    <h1 id="title" class="text-2xl font-semibold text-gray-600 mb-5">Penetapan Yudisium</h1>

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
                            <span id="totalEligible" class="font-semibold text-neutral-950">0</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                            <span class="text-sm text-neutral-950">Total Tidak Eligible</span>
                            <span id="totalTidakEligible" class="font-semibold text-neutral-950">0</span>
                        </div>
                    </div>

                    <div class="card-body">
                        <table id="selection-table"
                            class="border border-neutral-200 dark:border-neutral-600 rounded-lg border-separate	">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-neutral-800 dark:text-white">
                                        <div class="form-check style-check flex items-center">
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
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                            </svg>
                                        </div>
                                    </th>
                                    <th scope="col" class="text-neutral-950">
                                        <div class="flex items-center gap-2">
                                            Nama
                                            <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                            </svg>
                                        </div>
                                    </th>
                                    <th scope="col" class="text-neutral-950">
                                        <div class="flex items-center gap-2">
                                            Masa Studi
                                            <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                            </svg>
                                        </div>
                                    </th>
                                    <th scope="col" class="text-neutral-950">
                                        <div class="flex items-center gap-2">
                                            SKS Lulus
                                            <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                            </svg>
                                        </div>
                                    </th>
                                    <th scope="col" class="text-neutral-950">
                                        <div class="flex items-center gap-2">
                                            IPK
                                            <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
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
                                            Alasan
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
                                @foreach ($datas as $idx => $data)
                                    <tr> <!-- class="{{ $loop->odd ? 'bg-blue-100' : 'bg-white' }}"  untuk membedakan background row-->
                                        <td>
                                            <div class="form-check style-check flex items-center">
                                                {{-- <input class="form-check-input" type="checkbox"> --}}
                                                <label class="ms-2 form-check-label">
                                                    {{ $idx + 1 }}
                                                </label>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="flex items-center">
                                                <h6 class="text-base mb-0 ">
                                                    {{ $data->nim }}
                                                </h6>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex items-center">
                                                <h6>
                                                    {{ $data->name }}
                                                </h6>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <h6>
                                                    {{ $data->study_period }}
                                                </h6>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex items-center">
                                                <h6>{{ $data->pass_sks }}</h6>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex items-center">
                                                <h6>{{ $data->ipk }}</h6>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex items-center">
                                                <h6>{{ $data->predikat }}</h6>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="statusSpan px-6 py-1.5 rounded-full font-medium text-sm inline-block cursor-pointer
                                                {{ $data->status === 'Eligible' ? 'bg-success-100 text-success-600' : 'bg-danger-100 text-danger-600' }}"
                                                    data-nim="{{ $data->nim }}" data-status="{{ $data->status }}"
                                                    data-alasan="{{ $data->alasan_status ?? '-' }}"
                                                    data-fakultas="{{ $data->fakultas_id }}"
                                                >
                                                {{ $data->status }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($data->alasan_status)
                                                <span class="text-xs text-gray-500">{{ $data->alasan_status }}</span>
                                            @else
                                                '-'
                                            @endif
                                        </td>
                                        <td>
                                            
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Ubah Status -->
        <div id="statusModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Ubah Status Mahasiswa</h2>
                <form id="statusForm">
                    @csrf
                    <input type="hidden" id="modalNim" name="nim">

                    <!-- Pilih Status -->
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Baru</label>
                    <select id="modalStatus" name="status" class="form-select w-full border rounded p-2 mb-4" required>
                        <option value="">-- Pilih Status --</option>
                        <option value="Eligible">Eligible</option>
                        <option value="Tidak Eligible">Tidak Eligible</option>
                    </select>

                    <!-- Alasan -->
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alasan</label>
                    <textarea id="modalAlasan" name="alasan" rows="3"
                        class="form-input border border-gray-300 rounded w-full p-2 mb-4" required></textarea>

                    <!-- Tombol -->
                    <div class="flex justify-end gap-2">
                        <button type="button" id="closeModal"
                            class="bg-gray-400 text-white px-4 py-2 rounded">Batal</button>
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="flex flex-col md:flex-row items-center gap-4">
            <!-- <input type="text" id="nomorYudisium" class="form-input border border-gray-300 rounded w-full md:w-1/3"
                                                    placeholder="Nomor Yudisium" readonly value="{{ old('no_yudicium') }}" /> -->

            <button type="buttton" id="btnTetapkan"
                class="bg-red-600 text-white px-4 py-2 rounded shadow w-full md:w-auto">
                Tetapkan Yudicium
            </button>
        </div>
    </div>
    <script>
        const routes = {
            approveYudicium: "{{ route('yudicium.approve') }}",
            ubahStatus: "{{ route('tempStatus') }}",
        };
    </script>
@endsection
