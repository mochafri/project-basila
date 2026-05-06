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
        <script src="' . asset('assets/js/buttonUpdate.js') . '"></script>
    ';
@endphp

@section('content')
    <h1 class="text-2xl font-semibold text-gray-600 mb-5">{{__('index7.title')}}</h1>
    
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
                            <span class="text-sm text-neutral-950">Total Dipilih</span>
                            <span id="totalDipilih" class="font-semibold text-neutral-950">0</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                            <span class="text-sm text-neutral-950">Total Tidak Dipilih</span>
                            <span id="totalTidakDipilih" class="font-semibold text-neutral-950">0</span>
                        </div>
                    </div>

                    <div class="card-body">
                        <table id="selection-table"
                            class="border border-neutral-200 dark:border-neutral-600 rounded-lg border-separate	">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-neutral-800 dark:text-white">
                                        <div class="flex items-center gap-2">
                                            <input id="checkAll" type="checkbox" class="w-4 h-4 accent-red-600 cursor-pointer">
                                            <label class="ms-2 text-neutral-950 form-check-label">No</label>
                                        </div>
                                    </th>
                                    <th scope="col" class="text-neutral-950">
                                        <div class="flex items-center gap-2">NIM <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" /></svg></div>
                                    </th>
                                    <th scope="col" class="text-neutral-950">
                                        <div class="flex items-center gap-2">Nama <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" /></svg></div>
                                    </th>
                                    <th scope="col" class="text-neutral-950">
                                        <div class="flex items-center gap-2">Masa Studi <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" /></svg></div>
                                    </th>
                                    <th scope="col" class="text-neutral-950">
                                        <div class="flex items-center gap-2">SKS Lulus <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" /></svg></div>
                                    </th>
                                    <th scope="col" class="text-neutral-950">
                                        <div class="flex items-center gap-2">IPK <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" /></svg></div>
                                    </th>
                                    <th scope="col" class="text-neutral-950">
                                        <div class="flex items-center gap-2">Predikat <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" /></svg></div>
                                    </th>
                                    <th scope="col" class="text-neutral-950">
                                        <div class="flex items-center gap-2">Status <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" /></svg></div>
                                    </th>
                                    <th scope="col" class="text-neutral-950">
                                        <div class="flex items-center gap-2">Alasan <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" /></svg></div>
                                    </th>
                                    <th scope="col" class="text-neutral-950">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($datas as $idx => $data)
                                    <tr>
                                        {{-- Checkbox --}}
                                        <td>
                                            <div class="flex items-center gap-2">
                                                <input type="checkbox"
                                                    class="row-checkbox w-4 h-4 accent-red-600 cursor-pointer"
                                                    data-nim="{{ $data->nim }}"
                                                    checked>
                                                <label class="ms-2 form-check-label">{{ $idx + 1 }}</label>
                                            </div>
                                        </td>
                                        <td><h6 class="text-base mb-0">{{ $data->nim }}</h6></td>
                                        <td><h6>{{ $data->name }}</h6></td>
                                        <td><h6>{{ $data->study_period }}</h6></td>
                                        <td><h6>{{ $data->pass_sks }}</h6></td>
                                        <td><h6>{{ $data->ipk }}</h6></td>
                                        <td><h6>{{ $data->predikat }}</h6></td>
                                        <td>
                                            <span class="statusSpan px-4 py-1 rounded-full font-medium text-sm inline-block cursor-pointer
                                                {{ $data->status === 'Eligible' ? 'bg-success-100 text-success-600' : 'bg-danger-100 text-danger-600' }}"
                                                data-nim="{{ $data->nim }}"
                                                data-status="{{ $data->status }}"
                                                data-alasan="{{ $data->alasan_status ?? '-' }}"
                                                data-fakultas="{{ $data->fakultas_id }}">
                                                {{ $data->status }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($data->alasan_status && $data->alasan_status !== '-')
                                                <span class="text-xs text-gray-500">{{ $data->alasan_status }}</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="flex items-center gap-2">
                                                {{-- Tombol Detail --}}
                                                <button type="button"
                                                    class="btn-detail-mhs w-8 h-8 bg-primary-50 text-primary-600 rounded-full inline-flex items-center justify-center"
                                                    data-nim="{{ $data->nim }}"
                                                    data-name="{{ $data->name }}"
                                                    data-prodi="{{ $data->prodi_name ?? '-' }}"
                                                    data-fakultas="{{ $data->fakultas_name ?? '-' }}"
                                                    data-study="{{ $data->study_period }}"
                                                    data-sks="{{ $data->pass_sks }}"
                                                    data-ipk="{{ $data->ipk }}"
                                                    data-predikat="{{ $data->predikat }}"
                                                    data-status="{{ $data->status }}"
                                                    data-alasan="{{ $data->alasan_status ?? '-' }}"
                                                    data-smtmasuk="{{ $data->smt_current ?? $data->id_smt_masuk ?? '' }}"
                                                    data-basing="{{ $data->bahasa_asing ?? '' }}"
                                                    data-publikasi="{{ $data->publikasi ?? '' }}"
                                                    data-tak="{{ $data->tak ?? '' }}"
                                                    data-admin="{{ $data->administratif ?? '' }}"
                                                    data-bpp="{{ $data->bpp ?? '' }}"
                                                    data-openlib="{{ $data->openlib ?? '' }}"
                                                    data-sanksi="{{ $data->sanksi ?? '' }}"
                                                    title="Lihat Detail">
                                                    <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
                                                </button>
                                                {{-- Tombol Hapus dari daftar --}}
                                                <button type="button"
                                                    class="btn-hapus-mhs w-8 h-8 bg-danger-100 text-danger-600 rounded-full inline-flex items-center justify-center"
                                                    data-nim="{{ $data->nim }}"
                                                    data-name="{{ $data->name }}"
                                                    title="Hapus dari daftar">
                                                    <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Detail Mahasiswa (sama dengan index3) -->
        <div id="infoDetailMahasiswa"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50
           opacity-0 pointer-events-none transition-opacity duration-300 ease-out">

            <div class="bg-white w-full max-w-xl rounded-xl shadow-lg overflow-hidden
               transform scale-95 transition-transform duration-300 ease-out">

                <!-- HEADER MERAH -->
                <div class="bg-red-700 text-white px-5 py-3 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-white">Detail Mahasiswa</h2>
                    <button id="closeDetailModal" class="text-white text-lg">✕</button>
                </div>

                <div class="px-6 py-4">

                    <!-- INFORMASI UTAMA -->
                    <div class="grid grid-cols-2 gap-y-2 text-[15px]">
                        <p>Fakultas</p>
                        <p>: <span id="dm-fakultas"></span></p>
                        <p>Program Studi</p>
                        <p>: <span id="dm-prodi"></span></p>
                        <p>NIM</p>
                        <p>: <span id="dm-nim"></span></p>
                        <p>Nama</p>
                        <p>: <span id="dm-nama"></span></p>
                    </div>

                    <div class="border-t border-gray-300 my-4"></div>

                    <!-- STATUS ELIGIBILITAS -->
                    <div class="text-[15px]">
                        <p class="font-semibold mb-2">Status Eligibilitas</p>
                        <ul class="space-y-1 pl-1">
                            <li class="flex items-center gap-2">
                                <span id="dm-study_period_icon"></span> Masa Studi
                            </li>
                            <li class="flex items-center gap-2">
                                <span id="dm-semester_lulus_icon"></span> Semester Lulus
                            </li>
                            <li class="flex items-center gap-2">
                                <span id="dm-ipk_icon"></span> IPK Minimum
                            </li>
                            <li class="flex items-center gap-2">
                                <span id="dm-sks_icon"></span> SKS Lulus
                            </li>
                            <li class="flex items-center gap-2">
                                <span id="dm-mk_icon"></span> Status Kelulusan MK
                            </li>
                            <li class="flex items-center gap-2">
                                <span id="dm-bahasa_icon"></span> Sertifikasi Kecakapan Bahasa Asing
                            </li>
                            <li class="flex items-center gap-2">
                                <span id="dm-publikasi_icon"></span> Publikasi Karya Ilmiah
                            </li>
                            <li class="flex items-center gap-2">
                                <span id="dm-tak_icon"></span> TAK
                            </li>
                            <li class="flex items-center gap-2">
                                <span id="dm-administratif_icon"></span> Kewajiban Administratif
                            </li>
                            <li class="flex items-center gap-2">
                                <span id="dm-bpp_icon"></span> BPP
                            </li>
                            <li class="flex items-center gap-2">
                                <span id="dm-oplib_icon"></span> OpenLibrary
                            </li>
                            <li class="flex items-center gap-2">
                                <span id="dm-sanksi_icon"></span> Bebas Sanksi Akademik
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>

        <!-- Modal Ubah Status (tetap ada untuk modal status) -->
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

            <button type="buttton" id="btnUpdate"
                class="bg-red-600 text-white px-4 py-2 rounded shadow w-full md:w-auto">
                Tetapkan Ulang
            </button>
        </div>
    </div>
    <script>
        const routes = {
            approveYudicium: "{{ route('yudicium.approve') }}",
            ubahStatus: "{{ route('tempStatus') }}",
            updateStatus: "{{ route('yudicium.update') }}",
            updateYudicium: "{{ route('yudicium.updateYudicium') }}"
        };
        
        // Pass all mahasiswa data to JavaScript for checkbox tracking
        window.allMahasiswaData = @json($datas->map(function($data) {
            return [
                'nim' => $data->nim,
                'name' => $data->name,
                'checked' => true // Default all checked
            ];
        }));
        
        console.log('Total mahasiswa from backend:', window.allMahasiswaData.length);
    </script>
@endsection
