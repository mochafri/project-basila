@extends('layout.layout')

@php
    $title = 'Penetapan Yudisium';
    $subTitle = 'Tambah';
    $script = '
<script src="' . asset('assets/js/data-table.js') . '"></script>';
@endphp

@section('content')
    <h1 class="text-2xl font-bold text-neutral-400 mb-5">Penetapan Yudisium / Tambah</h1>
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


            <form class="col-span-12 md:col-span-10 grid grid-cols-12 gap-4">
                @csrf
                <!-- Fakultas -->
                <div class="col-span-12 md:col-span-5">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Fakultas</label>
                    <select id="fakultas" name="fakultas" class="form-select w-full border rounded p-2">
                        <option value="">-- Pilih Fakultas --</option>
                    </select>
                    {{-- <select name="fakultas" class="form-select w-full border rounded p-2">
                        <option value="">-- Pilih Fakultas --</option>
                        @foreach ($faculties as $faculty)
                            <option value="{{ $faculty->facultyid }}">{{ $faculty->facultyname }}</option>
                        @endforeach
                    </select> --}}
                </div>

                <!-- Semester -->
                <div class="col-span-12 md:col-span-5">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Semester</label>
                    <select class="form-select w-full text-neutral-900 bg-gray-100" name="semester">
                        <option>Ganjil 2024/2025</option>
                        <option>Genap 2025/2026</option>
                    </select>
                </div>

                <!-- Spacer -->
                <div class="hidden md:block md:col-span-2"></div>

                <!-- Program Studi -->
                <div class="col-span-12 md:col-span-5">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Program Studi</label>
                    <select id="prodi" name="prodi" class="form-select w-full border rounded p-2">
                        <option value="">-- Pilih Program Studi --</option>
                    </select>
                </div>


                <!-- Tombol -->
                <div class="col-span-12 md:col-start-6 md:col-span-2 flex items-end">
                    <button class="bg-red-600 hover:bg-red-700 text-white font-semibold px-5 py-2 rounded shadow w-full"
                        type="submit">
                        Tampilkan
                    </button>
                </div>
            </form>


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
                            @foreach ($mahasiswa as $mhs)
                                @if ($mhs->status == 'Eligible')
                                    @php
                                        $totalEligible = isset($totalEligible) ? $totalEligible + 1 : 1;
                                    @endphp
                                @elseif ($mhs->status == 'Tidak Eligible')
                                    @php
                                        $totalTidakEligible = isset($totalTidakEligible) ? $totalTidakEligible + 1 : 1;
                                    @endphp
                                @endif
                            @endforeach
                            <span class="font-semibold text-neutral-950">{{ $totalEligible ?? 0 }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                            <span class="text-sm text-neutral-950">Total Tidak Eligible</span>
                            <span class="font-semibold text-neutral-950">{{ $totalTidakEligible ?? 0 }}</span>
                        </div>
                    </div>

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
                                @foreach ($mahasiswa as $mhs)
                                    <tr>
                                        <td>
                                            <div class="form-check style-check flex items-center">
                                                <input class="form-check-input" type="checkbox">
                                                <label class="ms-2 form-check-label">
                                                    {{ $loop->iteration }}
                                                </label>
                                            </div>
                                        </td>
                                        <td><a href="javascript:void(0)" class="text-primary-600">
                                                {{ $mhs->nim }}
                                            </a>
                                        </td>
                                        <td>
                                            <div class="flex items-center">
                                                <h6 class="text-base mb-0 ">
                                                    {{ $mhs->name }}
                                                </h6>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex items-center">
                                                <h6>
                                                    {{ $mhs->study_period }} semester
                                                </h6>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <h6>
                                                    {{ $mhs->pass_sks }}
                                                </h6>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex items-center">
                                                <h6>{{ $mhs->ipk }}</h6>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex items-center">
                                                {{ $mhs->predikat }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex items-center">
                                                @if ($mhs->status == 'Eligible')
                                                    <span
                                                        class="bg-success-100  text-success-600  px-6 py-1.5 rounded-full font-medium text-sm">Eligible</span>
                                                @else
                                                    <span
                                                        class="bg-danger-100  text-danger-600  px-6 py-1.5 rounded-full font-medium text-sm">Tidak
                                                        Eligible</span>
                                                @endif

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

        <!-- Hasil generate kode -->
        <div class="flex flex-col md:flex-row items-center gap-4 mt-6">
            @if (isset($kode))
                <input type="text" class="form-input border border-gray-300 rounded w-full md:w-1/3"
                    placeholder="Nomor Yudisium" value="{{ $kode }}" readonly />
            @else
                <input type="text" class="form-input border border-gray-300 rounded w-full md:w-1/3"
                    placeholder="Nomor Yudisium" value="" readonly />
            @endif
            <button class="bg-red-600 text-white px-4 py-2 rounded shadow w-full md:w-auto" onclick="confirmButton()">
                Tetapkan Yudisium
            </button>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const fakultasSelect = document.getElementById('fakultas');
            const prodiSelect = document.getElementById('prodi');

            fetch('{{ route('api.faculties') }}')
                .then(res => {
                    if (!res.ok) throw new Error('Status: ' + res.status);
                    return res.json();
                })
                .then(response => {
                    console.log('Response fakultas:', response);

                    if (response.status === 'success' && Array.isArray(response.data)) {
                        response.data.forEach(faculty => {
                            const opt = document.createElement('option');
                            opt.value = faculty.facultyid;
                            opt.textContent = faculty.facultyname;
                            fakultasSelect.appendChild(opt);
                        });
                    } else {
                        console.warn('Data fakultas tidak sesuai format');
                    }
                })
                .catch(err => {
                    console.error('Gagal memuat fakultas:', err);
                    const opt = document.createElement('option');
                    opt.textContent = 'Data fakultas tidak tersedia';
                    opt.disabled = true;
                    fakultasSelect.appendChild(opt);
                });

            fakultasSelect.addEventListener('change', function() {
                const facultyId = this.value;
                prodiSelect.innerHTML = '<option value="">-- Pilih Program Studi --</option>';

                if (!facultyId) return;

                fetch(`/api/faculties/${facultyId}`)
                    .then(res => {
                        if (!res.ok) throw new Error('Status: ' + res.status);
                        return res.json();
                    })
                    .then(data => {
                        console.log('Response prodi:', data);

                        let prodis = [];
                        if (data.Success === 'Success' && Array.isArray(data.Data)) {
                            prodis = data.Data;
                        }

                        if (prodis.length > 0) {
                            prodis.forEach(prody => {
                                const opt = document.createElement('option');
                                opt.value = prody.studyprogramid; 
                                opt.textContent = prody.studyprogramname;
                                prodiSelect.appendChild(opt);
                            });
                        } else {
                            const opt = document.createElement('option');
                            opt.textContent = 'Data prodi tidak tersedia';
                            opt.disabled = true;
                            prodiSelect.appendChild(opt);
                        }
                    })
                .catch(err => {
                    console.error('Gagal memuat program studi:', err);
                    const opt = document.createElement('option');
                    opt.textContent = 'Gagal memuat data prodi';
                    opt.disabled = true;
                    prodiSelect.appendChild(opt);
                });
            });
        });

        function confirmButton() {
            Swal.fire({
                title: "Are you sure?",
                text: "Anda yakin akan menetapkan yudisium?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Tetapkan!",
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "bg-red-600 text-white"
                },
                buttonStyling: false,

            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Success!",
                        text: "Berhasil di tetapkan",
                        icon: "success",
                        confirmButton: "OK",
                        customClass: {
                            confirmButton: "bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700",
                        },
                        buttonStyling: false
                    });
                }
            });
        }
    </script>
@endsection
