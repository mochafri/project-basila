<aside class="sidebar">
    <button type="button" class="sidebar-close-btn !mt-4">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        <a href="{{ route('index') }}" class="sidebar-logo background-primary flex items-center justify-center">
            <img src="{{ asset('assets/basila_images/basila_white.png') }}" alt="site logo" class="light-logo" width="120">
            <img src="{{ asset('assets/images/logo-light.png') }}" alt="site logo" class="dark-logo">
            <img src="{{ asset('assets/basila_images/logo_basila.png') }}" alt="site logo" class="logo-icon" width="30">
        </a>
    </div>
    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">

            <!-- Penambahan -->
            <div class="identitas flex flex-col items-center gap-1 my-10">
                <img src="{{ asset('assets/basila_images/favicon.png')}}" alt="logo basila" width="50" id="logo">
                <h1 class="uppercase text-xl " id="sidebar-name">fadhlullah B</h1>
                <h5 class="text-sm" id="sidebar-nim">19930026</h5>
            </div>
            <!-- Penambahan -->

            <li class="mb-4">
                <a href="{{ route('index') }}">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
                    <span>Dashboard</span>
                </a>
                
            </li>
            <li class="dropdown mb-4">
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
            </li>
            <li class="dropdown mb-4">
                <a href="javascript:void(0)">
                    <iconify-icon icon="streamline-cyber:report-problem-warning-hexagon" class="menu-icon"></iconify-icon>
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
            </li>

            <li class="dropdown mb-4">
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
            </li>
            <li class="dropdown mb-4">
                <a href="javascript:void(0)">
                    <iconify-icon icon="icons8:student" class="menu-icon"></iconify-icon>
                    <span>Student Yudicium</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('index') }}"><i
                                class="ri-circle-fill circle-icon text-dark w-auto"></i> Dashboard</a>
                    </li>
                    <li>
                        <a href="{{ route('index2') }}"><i
                                class="ri-circle-fill circle-icon text-dark w-auto"></i>Penetapan Yudisium</a>
                    </li>
                    <li>
                        <a href="{{ route('index3') }}"><i
                                class="ri-circle-fill circle-icon text-dark w-auto"></i>Approval Yudisium</a>
                    </li>
                    <li>
                        <a href="{{ route('index4') }}"><i
                                class="ri-circle-fill circle-icon text-dark w-auto"></i>Eligibilitas Yudisium</a>
                    </li>
                    <li>
                        <a href="{{ route('index5') }}"><i
                                class="ri-circle-fill circle-icon text-dark w-auto"></i>Laporan</a>
                    </li>
                </ul>
            </li>

            <!-- Buka komen kalau mau lihat tampilan component -->
            <!-- <li class="sidebar-menu-group-title">UI Elements</li>

            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:document-text-outline" class="menu-icon"></iconify-icon>
                    <span>Components</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('typography') }}"><i
                                class="ri-circle-fill circle-icon text-dark w-auto"></i> Typography</a>
                    </li>
                    <li>
                        <a href="{{ route('colors') }}"><i
                                class="ri-circle-fill circle-icon text-warning-600 w-auto"></i> Colors</a>
                    </li>
                    <li>
                        <a href="{{ route('button') }}"><i
                                class="ri-circle-fill circle-icon text-success-600 w-auto"></i> Button</a>
                    </li>
                    <li>
                        <a href="{{ route('dropdown') }}"><i
                                class="ri-circle-fill circle-icon text-purple-600  dark:text-purple-400 w-auto"></i>
                            Dropdown</a>
                    </li>
                    <li>
                        <a href="{{ route('alert') }}"><i
                                class="ri-circle-fill circle-icon text-warning-600 w-auto"></i> Alerts</a>
                    </li>
                    <li>
                        <a href="{{ route('card') }}"><i class="ri-circle-fill circle-icon text-danger-600 w-auto"></i>
                            Card</a>
                    </li>
                    <li>
                        <a href="{{ route('carousel') }}"><i
                                class="ri-circle-fill circle-icon text-info-600 w-auto"></i> Carousel</a>
                    </li>
                    <li>
                        <a href="{{ route('avatar') }}"><i
                                class="ri-circle-fill circle-icon text-success-600 w-auto"></i> Avatars</a>
                    </li>
                    <li>
                        <a href="{{ route('progress') }}"><i
                                class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Progress bar</a>
                    </li>
                    <li>
                        <a href="{{ route('tabs') }}"><i class="ri-circle-fill circle-icon text-warning-600 w-auto"></i>
                            Tab & Accordion</a>
                    </li>
                    <li>
                        <a href="{{ route('pagination') }}"><i
                                class="ri-circle-fill circle-icon text-danger-600 w-auto"></i> Pagination</a>
                    </li>
                    <li>
                        <a href="{{ route('badges') }}"><i class="ri-circle-fill circle-icon text-info-600 w-auto"></i>
                            Badges</a>
                    </li>
                    <li>
                        <a href="{{ route('tooltip') }}"><i
                                class="ri-circle-fill circle-icon dark:text-purple-400 w-auto"></i> Tooltip &
                            Popover</a>
                    </li>
                    <li>
                        <a href="{{ route('videos') }}"><i class="ri-circle-fill circle-icon text-cyan-600 w-auto"></i>
                            Videos</a>
                    </li>
                    <li>
                        <a href="{{ route('starRating') }}"><i
                                class="ri-circle-fill circle-icon text-[#7f27ff] w-auto"></i> Star Ratings</a>
                    </li>
                    <li>
                        <a href="{{ route('tags') }}"><i class="ri-circle-fill circle-icon text-[#8252e9] w-auto"></i>
                            Tags</a>
                    </li>
                    <li>
                        <a href="{{ route('list') }}"><i class="ri-circle-fill circle-icon text-[#e30a0a] w-auto"></i>
                            List</a>
                    </li>
                    <li>
                        <a href="{{ route('calendar') }}"><i
                                class="ri-circle-fill circle-icon text-yellow-400 w-auto"></i> Calendar</a>
                    </li>
                    <li>
                        <a href="{{ route('radio') }}"><i class="ri-circle-fill circle-icon text-orange-500 w-auto"></i>
                            Radio</a>
                    </li>
                    <li>
                        <a href="{{ route('switch') }}"><i class="ri-circle-fill circle-icon text-pink-600 w-auto"></i>
                            Switch</a>
                    </li>
                    <li>
                        <a href="{{ route('imageUpload') }}"><i
                                class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Upload</a>
                    </li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="heroicons:document" class="menu-icon"></iconify-icon>
                    <span>Forms</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('form') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>
                            Input Forms</a>
                    </li>
                    <li>
                        <a href="{{ route('formLayout') }}"><i
                                class="ri-circle-fill circle-icon text-warning-600 w-auto"></i> Input Layout</a>
                    </li>
                    <li>
                        <a href="{{ route('formValidation') }}"><i
                                class="ri-circle-fill circle-icon text-success-600 w-auto"></i> Form Validation</a>
                    </li>
                    <li>
                        <a href="{{ route('wizard') }}"><i
                                class="ri-circle-fill circle-icon text-danger-600 w-auto"></i> Form Wizard</a>
                    </li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="mingcute:storage-line" class="menu-icon"></iconify-icon>
                    <span>Table</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('tableBasic') }}"><i
                                class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Basic Table</a>
                    </li>
                    <li>
                        <a href="{{ route('tableData') }}"><i
                                class="ri-circle-fill circle-icon text-warning-600 w-auto"></i> Data Table</a>
                    </li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:pie-chart-outline" class="menu-icon"></iconify-icon>
                    <span>Chart</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('lineChart') }}"><i
                                class="ri-circle-fill circle-icon text-danger-600 w-auto"></i> Line Chart</a>
                    </li>
                    <li>
                        <a href="{{ route('columnChart') }}"><i
                                class="ri-circle-fill circle-icon text-warning-600 w-auto"></i> Column Chart</a>
                    </li>
                    <li>
                        <a href="{{ route('pieChart') }}"><i
                                class="ri-circle-fill circle-icon text-success-600 w-auto"></i> Pie Chart</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{ route('widgets') }}">
                    <iconify-icon icon="fe:vector" class="menu-icon"></iconify-icon>
                    <span>Widgets</span>
                </a>
            </li>
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="flowbite:users-group-outline" class="menu-icon"></iconify-icon>
                    <span>Users</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('usersList') }}"><i
                                class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Users List</a>
                    </li>
                    <li>
                        <a href="{{ route('usersGrid') }}"><i
                                class="ri-circle-fill circle-icon text-warning-600 w-auto"></i> Users Grid</a>
                    </li>
                    <li>
                        <a href="{{ route('addUser') }}"><i class="ri-circle-fill circle-icon text-info-600 w-auto"></i>
                            Add User</a>
                    </li>
                    <li>
                        <a href="{{ route('viewProfile') }}"><i
                                class="ri-circle-fill circle-icon text-danger-600 w-auto"></i> View Profile</a>
                    </li>
                </ul>
            </li>

            <li class="sidebar-menu-group-title">Application</li>

            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="simple-line-icons:vector" class="menu-icon"></iconify-icon>
                    <span>Authentication</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('signin') }}"><i
                                class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Sign In</a>
                    </li>
                    <li>
                        <a href="{{ route('signup') }}"><i
                                class="ri-circle-fill circle-icon text-warning-600 w-auto"></i> Sign Up</a>
                    </li>
                    <li>
                        <a href="{{ route('forgotPassword') }}"><i
                                class="ri-circle-fill circle-icon text-info-600 w-auto"></i> Forgot Password</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{ route('gallery') }}">
                    <iconify-icon icon="solar:gallery-wide-linear" class="menu-icon"></iconify-icon>
                    <span>Gallery</span>
                </a>
            </li>
            <li>
                <a href="{{ route('pricing') }}">
                    <iconify-icon icon="hugeicons:money-send-square" class="menu-icon"></iconify-icon>
                    <span>Pricing</span>
                </a>
            </li>
            <li>
                <a href="{{ route('faq') }}">
                    <iconify-icon icon="mage:message-question-mark-round" class="menu-icon"></iconify-icon>
                    <span>FAQs.</span>
                </a>
            </li>
            <li>
                <a href="{{ route('pageError') }}">
                    <iconify-icon icon="streamline:straight-face" class="menu-icon"></iconify-icon>
                    <span>404</span>
                </a>
            </li>
            <li>
                <a href="{{ route('termsCondition') }}">
                    <iconify-icon icon="octicon:info-24" class="menu-icon"></iconify-icon>
                    <span>Terms & Conditions</span>
                </a>
            </li>
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="icon-park-outline:setting-two" class="menu-icon"></iconify-icon>
                    <span>Settings</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('company') }}"><i
                                class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Company</a>
                    </li>
                    <li>
                        <a href="{{ route('notification') }}"><i
                                class="ri-circle-fill circle-icon text-warning-600 w-auto"></i> Notification</a>
                    </li>
                    <li>
                        <a href="{{ route('notificationAlert') }}"><i
                                class="ri-circle-fill circle-icon text-info-600 w-auto"></i> Notification Alert</a>
                    </li>
                    <li>
                        <a href="{{ route('theme') }}"><i class="ri-circle-fill circle-icon text-danger-600 w-auto"></i>
                            Theme</a>
                    </li>
                    <li>
                        <a href="{{ route('currencies') }}"><i
                                class="ri-circle-fill circle-icon text-danger-600 w-auto"></i> Currencies</a>
                    </li>
                    <li>
                        <a href="{{ route('language') }}"><i
                                class="ri-circle-fill circle-icon text-danger-600 w-auto"></i> Languages</a>
                    </li>
                    <li>
                        <a href="{{ route('paymentGateway') }}"><i
                                class="ri-circle-fill circle-icon text-danger-600 w-auto"></i> Payment Gateway</a>
                    </li>
                </ul>
            </li> -->
        </ul>
    </div>
</aside>

