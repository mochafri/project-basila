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


            <form id="filterForm" class="col-span-12 md:col-span-10 grid grid-cols-12 gap-4">
                @csrf
                <!-- Fakultas -->
                <div class="col-span-12 md:col-span-5">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Fakultas</label>
                    <select id="fakultas" name="fakultas" class="form-select w-full border rounded p-2">
                        <option value="">-- Pilih Fakultas --</option>
                    </select>
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

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-col md:flex-row items-center gap-4 mt-6">
            <input type="text" id="nomorYudisium" class="form-input border border-gray-300 rounded w-full md:w-1/3"
                placeholder="Nomor Yudisium" readonly value="{{ old('no_yudicium') }}" />

            <button type="buttton" id="btnTetapkan"
                class="bg-red-600 text-white px-4 py-2 rounded shadow w-full md:w-auto">
                Tetapkan Yudisium
            </button>
        </div>

        // Debug lewat php

        {{-- <form action="{{ route('yudicium.generate') }}" method="POST">
            @csrf

            <div class="flex flex-col md:flex-row items-center gap-4 mt-6">
                <input type="hidden" name="fakultas" value="{{ old('fakultas', $selectedFakultas ?? '') }}">
                <input type="hidden" name="prodi" value="{{ old('prodi', $selectedProdi ?? '') }}">
                <input type="hidden" name="total_mahasiswa" value="{{ $totalMahasiswa ?? 0 }}">

                <input type="text" id="nomorYudisium"
                    class="form-input border border-gray-300 rounded w-full md:w-1/3" placeholder="Nomor Yudisium"
                    readonly value="{{ old('no_yudicium') }}" />

                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded shadow w-full md:w-auto">
                    Tetapkan Yudisium
                </button>
            </div>
        </form>
        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif --}}
        
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const fakultasSelect = document.getElementById('fakultas');
            const prodiSelect = document.getElementById('prodi');
            const form = document.getElementById('filterForm');
            const tbody = document.querySelector('#selection-table tbody');
            const totalEligibleSpan = document.getElementById('totalEligible');
            const totalTidakEligibleSpan = document.getElementById('totalTidakEligible');
            const nomorYudisiumInput = document.getElementById('nomorYudisium');
            const btnTetapkan = document.getElementById('btnTetapkan');

            try {
                const res = await fetch("{{ route('show.faculties') }}");
                const data = await res.json();

                if (!res.ok) {
                    throw new Error('Token exp : ' + res.statusText);
                }

                if (data.status === "success" && Array.isArray(data.data)) {
                    data.data.forEach(fakultas => {
                        const opt = document.createElement('option');
                        opt.value = fakultas.facultyid;
                        opt.textContent = fakultas.facultyname;
                        fakultasSelect.appendChild(opt);
                    });
                }
            } catch (err) {
                console.error('Gagal memuat fakultas:', err);
                fakultasSelect.innerHTML = '<option value="">Gagal memuat data fakultas</option>';
            }

            fakultasSelect.addEventListener('change', async () => {
                const facultyId = fakultasSelect.value;
                prodiSelect.innerHTML = '<option value="">-- Pilih Program Studi --</option>';
                if (!facultyId) return;

                try {
                    const res = await fetch(`/faculties/${facultyId}`);
                    const data = await res.json();
                    if (data.success === "success" && Array.isArray(data.data)) {
                        data.data.forEach(prodi => {
                            const opt = document.createElement('option');
                            opt.value = prodi.studyprogramid;
                            opt.textContent = prodi.studyprogramname;
                            prodiSelect.appendChild(opt);
                        });
                    }
                } catch (err) {
                    console.error('Gagal memuat prodi:' + err);
                    prodiSelect.innerHTML = '<option value="">Gagal memuat data prodi</option>';
                }
            });

            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const facultyId = fakultasSelect.value;
                const prodiId = prodiSelect.value;

                if (!facultyId || !prodiId) {
                    alert('Pilih Fakultas dan Program Studi terlebih dahulu');
                    return;
                }

                console.log("URL fetch:", "{{ route('filterMhs') }}");

                try {
                    const res = await fetch("{{ route('filterMhs') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            fakultas: facultyId,
                            prodi: prodiId
                        })
                    });

                    console.log("Response:", res);

                    const data = await res.json();
                    console.log("Data:", data);

                    tbody.innerHTML = '';
                    let totalEligible = 0;
                    let totalTidakEligible = 0;

                    if (Array.isArray(data.mahasiswa) && data.mahasiswa.length > 0) {
                        data.mahasiswa.forEach((mhs, idx) => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                            <td>${idx + 1}</td>
                            <td>${mhs.nim}</td>
                            <td>${mhs.name}</td>
                            <td>${mhs.study_period} Semester</td>
                            <td>${mhs.pass_sks}</td>
                            <td>${mhs.ipk}</td>
                            <td>${mhs.predikat}</td>
                            <td>
                                ${mhs.status === "Eligible"
                                    ? `<span class="bg-success-100 text-success-600 px-6 py-1.5 rounded-full font-medium text-sm">Eligible</span>`
                                    : `<span class="bg-danger-100 text-danger-600 px-6 py-1.5 rounded-full font-medium text-sm">Tidak Eligible</span>`
                                }
                            </td>
                            <td>
                                <a href="javascript:void(0)" class="w-8 h-8 bg-primary-50 text-primary-600 rounded-full inline-flex items-center justify-center">
                                    <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
                                </a>
                            </td>
                        `;
                            tbody.appendChild(tr);

                            if (mhs.status === "Eligible") totalEligible++;
                            else totalTidakEligible++;
                        });
                    }

                    totalEligibleSpan.textContent = totalEligible;
                    totalTidakEligibleSpan.textContent = totalTidakEligible;

                    document.querySelector('input[name="fakultas"]').value = facultyId;
                    document.querySelector('input[name="prodi"]').value = prodiId;
                    document.querySelector('input[name="total_mahasiswa"]').value = totalEligible;

                } catch (err) {
                    console.error(err);
                }
            });
            btnTetapkan.addEventListener('click', async (e) => {
                e.preventDefault();
                try {
                    const url = "{{ route('yudicium.approve') }}";
                    console.log("URL fetch:", url);

                    const facultyId = parseInt(fakultasSelect.value);
                    const prodiId = parseInt(prodiSelect.value);

                    const res = await fetch("{{ route('yudicium.approve') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            fakultas: facultyId,
                            prodi: prodiId
                        })
                    });

                    if (!res.ok) {
                        throw new Error("Ini error nya : " + res.statusText);
                    }

                    console.log("Response : ", res);
                    const data = await res.json();
                    console.log("Data : ", data);

                    nomorYudisiumInput.value = data.nomor_yudisium;

                    if (data.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Yudisium berhasil ditetapkan.',
                            icon: 'success'
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.message || 'Terjadi kesalahan.',
                            icon: 'error'
                        });
                    }
                } catch (err) {
                    console.log("Error:" + err);
                }
            });
        });
    </script>
@endsection
