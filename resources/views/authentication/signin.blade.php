<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en">

<x-head />

<body class="dark:bg-neutral-800 bg-neutral-100 dark:text-white overflow-hidden">

    <!-- alert -->
    @if(session('error'))
        <div class="alert alert-danger bg-red-200 text-danger-600 border border-danger-600 px-6 py-[11px] font-semibold text-lg rounded-lg flex items-center justify-between w-1/2 absolute translate-x-1/2 mt-2 animate-fade-in"
            role="alert">
            {{ session('error') }}
            <button class="remove-button text-danger-600 text-2xl line-height-1">
                <iconify-icon icon="iconamoon:sign-times-light" class="icon"></iconify-icon>
            </button>
        </div>
    @endif
    <!-- end alert -->

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
                    <h4 class="mb-3">Masuk ke akun anda</h4>
                    <p class="mb-8 text-secondary-light text-lg">Selamat datang kembali! Silakan masukkan detail Anda</p>
                </div>

                <!-- username alert -->
                @if ($errors->has('signin'))
                    <div class="alert alert-danger bg-danger-100 dark:bg-danger-600/25 text-danger-600 dark:text-danger-400 border-danger-100 px-6 py-[11px] mb-5 font-semibold text-lg rounded-lg"
                        role="alert">
                        <div class="flex items-start justify-between text-lg">
                            <div class="flex items-start gap-2">
                                <iconify-icon icon="ep:warn-triangle-filled"
                                    class="icon text-xl mt-1.5 shrink-0"></iconify-icon>
                                <div>
                                    <p class="font-medium text-danger-600 text-sm mt-2">{{ $errors->first('signin') }}</p>
                                </div>
                            </div>
                            <button class="remove-button text-danger-600 text-2xl line-height-1"> <iconify-icon
                                    icon="iconamoon:sign-times-light" class="icon"></iconify-icon></button>
                        </div>
                    </div>

                @endif

                <!-- end username alert -->

                <form action="{{ route('signin.process') }}" method="POST" class="form">
                    @csrf
                    <div class="icon-field mb-4 relative">
                        <span class="absolute start-4 top-1/2 -translate-y-1/2 pointer-events-none flex text-xl">
                            <iconify-icon icon="mage:email"></iconify-icon>
                        </span>
                        <input type="text" name="username"
                            class="form-control h-[56px] ps-11 border-neutral-300 bg-neutral-50 dark:bg-dark-2 rounded-xl"
                            placeholder="username SSO" autocomplete="username" required
                            oninvalid="this.setCustomValidity('Username wajib diisi')"
                            oninput="this.setCustomValidity('')">
                    </div>
                    <div class="relative mb-5">
                        <div class="icon-field">
                            <span class="absolute start-4 top-1/2 -translate-y-1/2 pointer-events-none flex text-xl">
                                <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                            </span>
                            <input type="password" name="password"
                                class="form-control h-[56px] ps-11 border-neutral-300 bg-neutral-50 dark:bg-dark-2 rounded-xl"
                                id="your-password" placeholder="Password" autocomplete="current-password" required
                                oninvalid="this.setCustomValidity('Password wajib diisi')"
                                oninput="this.setCustomValidity('')">
                        </div>
                        <span
                            class="toggle-password ri-eye-line cursor-pointer absolute end-0 top-1/2 -translate-y-1/2 me-4 text-secondary-light"
                            data-toggle="#your-password"></span>
                    </div>
                    <div class="mt-7">
                        <div class="flex justify-between gap-2">
                            <div class="flex items-center">
                                <input class="form-check-input border border-neutral-300" type="checkbox" value=""
                                    id="remember" name="remember">
                                <label class="ps-2" for="remember" name="remember">Remember me </label>
                            </div>
                            <a href="https://satu.telkomuniversity.ac.id/auth/forgot-password"
                                class="text-primary-600 font-medium hover:underline">Lupa password?</a>
                        </div>
                    </div>

                    <button type="submit"
                        class="btn bg-red-500 justify-center text-sm btn-sm px-3 py-4 w-full rounded-xl mt-8 text-neutral-50">
                        SSO Login</button>
                </form>
            </div>
        </div>
    </section>

    @php
        $script = '
                <script>
                document.addEventListener("DOMContentLoaded", function() {
                    // ================== Password Show Hide Js Start ==========
                    function initializePasswordToggle(toggleSelector) {
                        $(toggleSelector).on("click", function() {
                            $(this).toggleClass("ri-eye-off-line");
                            var input = $($(this).attr("data-toggle"));
                            if (input.attr("type") === "password") {
                                input.attr("type", "text");
                            } else {
                                input.attr("type", "password");
                            }
                        });
                    }
                    // Call the function
                    initializePasswordToggle(".toggle-password");
                    // ========================= Password Show Hide Js End ===========================
                });
                </script>';
    @endphp

    <x-script />

    {{-- Pastikan script dimunculkan setelah <x-script /> agar jQuery sudah dimuat --}}
    {!! $script !!}

    <script>
        $(".remove-button").on("click", function () {
            $(this).closest(".alert").addClass("hidden")
        });
    </script>';

</body>

</html>