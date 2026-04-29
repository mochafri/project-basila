<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en">

<x-head />

<body class="dark:bg-neutral-800 bg-neutral-100 dark:text-white overflow-hidden">

    <section class="bg-white dark:bg-dark-2 flex flex-wrap min-h-[100vh] text-neutral-400">
        <div class="lg:w-1/2 lg:block hidden">
            <div class="flex items-center flex-col h-full justify-center background-primary">
                <img src="{{ asset('assets/basila_images/telu.png') }}" alt="telu" width="500px">
            </div>
        </div>

        <div class="lg:w-1/2 py-8 px-6 flex flex-col justify-center">
            <div class="lg:max-w-[464px] mx-auto w-full">
                <div>
                    <a href="{{ route('index') }}" class="mb-2.5 max-w-[290px]">
                        <img src="{{ asset('assets/basila_images/basila_color.png') }}" alt="" width="100px">
                    </a>
                    <h4 class="mb-3">Pilih Role Anda</h4>
                    <p class="mb-8 text-secondary-light text-lg">Silakan pilih role yang ingin Anda gunakan untuk sesi ini</p>
                </div>

                <form action="{{ route('role.select.process') }}" method="POST" class="form">
                    @csrf
                    <div class="icon-field mb-4 relative">
                        <span class="absolute start-4 top-1/2 -translate-y-1/2 pointer-events-none flex text-xl">
                            <iconify-icon icon="solar:user-id-linear"></iconify-icon>
                        </span>
                        <select name="role"
                            class="form-control py-4 ps-11 border-neutral-300
                            bg-neutral-50 dark:bg-dark-2 rounded-xl"
                            required
                            oninvalid="this.setCustomValidity('Pilih salah satu role')"
                            oninput="this.setCustomValidity('')">
                            <option value="">-- Pilih Role --</option>
                            @if(session('available_roles'))
                                @foreach(session('available_roles') as $roleData)
                                    <option value="{{ $roleData['role'] }}">{{ $roleData['role'] }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <button type="submit"
                        class="btn bg-red-500 justify-center text-sm btn-sm px-3 py-4 w-full rounded-xl mt-8 text-neutral-50">
                        Lanjutkan</button>
                    
                    <div class="mt-4 text-center">
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-sm text-neutral-500 hover:text-red-500">Batalkan dan Kembali Login</a>
                    </div>
                </form>
                
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </section>

    <x-script />

</body>

</html>
