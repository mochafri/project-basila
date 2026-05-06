<aside class="sidebar">
    <button type="button" class="sidebar-close-btn !mt-4">
        <iconify-icon icon="radix-icons:cross-2" class="text-neutral-200"></iconify-icon>
    </button>
    <div>
        <a href="{{ route('index') }}" class="sidebar-logo background-primary flex items-center justify-center">
            <img src="{{ asset('assets/basila_images/basila_white.png') }}" alt="site logo" class="light-logo"
                width="120">
            <img src="{{ asset('assets/images/logo-light.png') }}" alt="site logo" class="dark-logo">
            <img src="{{ asset('assets/basila_images/logo_basila.png') }}" alt="site logo" class="logo-icon" width="30">
        </a>
    </div>
    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">

            <!-- Penambahan -->
            <div class="identitas flex flex-col items-center gap-3 my-10">
                <img src="{{ session('profilephoto') }}"
                    class="border br-white border-width-[2px] w-[200px] h-[200px] rounded-full object-cover mx-auto object-top"
                    alt="logo basila" id="logo">
                <div class="flex flex-col items-center w-full">
                    <h1 class="uppercase text-xl truncate overflow-hidden whitespace-nowrap max-w-[80%]"
                        id="sidebar-name">{{ session('username') }}</h1>
                    <h5 class="text-sm" id="sidebar-nim">{{ session('nim') }}</h5>
                </div>
            </div>
            <!-- Penambahan -->

            <li class="mb-4">
            <li class="mb-4">
                <a href="https://basila.telkomuniversity.ac.id/basilav2/"
                    onclick="confirm('Anda akan diarahkan ke halaman lain, lanjutkan?')">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
                    <span>{{ __('sidebar.home') }}</span>
                </a>
            </li>
            </li>
            {{-- <li class="dropdown mb-4">
                <a href="javascript:void(0)">
                    <iconify-icon icon="fluent:signature-16-regular" class="menu-icon"></iconify-icon>
                    <span>Digital Signature</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('invoiceList') }}"><i
                                class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> List</a>
                    </li>
                    <li>
                        <a href="{{ route('invoicePreview') }}"><i
                                class="ri-circle-fill circle-icon text-warning-600 w-auto"></i> Preview</a>
                    </li>
                    <li>
                        <a href="{{ route('invoiceAdd') }}"><i
                                class="ri-circle-fill circle-icon text-info-600 w-auto"></i> Add new</a>
                    </li>
                    <li>
                        <a href="{{ route('invoiceEdit') }}"><i
                                class="ri-circle-fill circle-icon text-danger-600 w-auto"></i> Edit</a>
                    </li>
                </ul>
            </li> --}}
            {{-- <li class="dropdown mb-4">
                <a href="javascript:void(0)">
                    <iconify-icon icon="streamline-cyber:report-problem-warning-hexagon"
                        class="menu-icon"></iconify-icon>
                    <span>Neo Feeder</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('textGenerator') }}"><i
                                class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Text Generator</a>
                    </li>
                    <li>
                        <a href="{{ route('codeGenerator') }}"><i
                                class="ri-circle-fill circle-icon text-warning-600 w-auto"></i> Code Generator</a>
                    </li>
                    <li>
                        <a href="{{ route('imageGenerator') }}"><i
                                class="ri-circle-fill circle-icon text-info-600 w-auto"></i> Image Generator</a>
                    </li>
                    <li>
                        <a href="{{ route('voiceGenerator') }}"><i
                                class="ri-circle-fill circle-icon text-danger-600 w-auto"></i> Voice Generator</a>
                    </li>
                    <li>
                        <a href="{{ route('videoGenerator') }}"><i
                                class="ri-circle-fill circle-icon text-success-600 w-auto"></i> Video Generator</a>
                    </li>
                </ul>
            </li> --}}

            {{-- <li class="dropdown mb-4">
                <a href="javascript:void(0)">
                    <iconify-icon icon="icon-park-outline:good-two" class="menu-icon"></iconify-icon>
                    <span>Service Management</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('wallet') }}"><i
                                class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Wallet</a>
                    </li>
                </ul>
            </li> --}}
            <li class="dropdown mb-4">
                <a href="javascript:void(0)">
                    <iconify-icon icon="icons8:student" class="menu-icon"></iconify-icon>
                    <span>{{ __('sidebar.student_yudisium') }}</span>
                </a>
                <ul class="sidebar-submenu">
                    @if(in_array(session('active_role'), ['SUPERADMIN', 'ADMIN LAAK', 'DEKAN', 'DOSEN']))
                    <li>
                        <a href="{{ route('index') }}">
                            <i class="ri-circle-fill circle-icon text-dark w-auto"></i>
                            {{ __('sidebar.dashboard') }}
                        </a>
                    </li>
                    @endif

                    @if(in_array(session('active_role'), ['SUPERADMIN', 'ADMIN LAAK']))
                    <li>
                        <a href="{{ route('index2') }}">
                            <i class="ri-circle-fill circle-icon text-dark w-auto"></i>
                            {{ __('sidebar.determination') }}
                        </a>
                    </li>
                    @endif

                    @if(in_array(session('active_role'), ['SUPERADMIN', 'DEKAN', 'DOSEN']))
                    <li>
                        <a href="{{ route('index4') }}">
                            <i class="ri-circle-fill circle-icon text-dark w-auto"></i>
                            {{ __('sidebar.approval') }}
                        </a>
                    </li>
                    @endif

                    @if(in_array(session('active_role'), ['SUPERADMIN']))
                    <li>
                        <a href="{{ route('index6') }}">
                            <i class="ri-circle-fill circle-icon text-dark w-auto"></i>
                            {{ __('sidebar.report') }}
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
        </ul>
    </div>
</aside>