<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en" >

<x-head />

<body class=" bg-neutral-100">

    <!-- ..::  header area start ::.. -->
    <x-sidebar />
    <!-- ..::  header area end ::.. -->

    <main class="dashboard-main">

        <!-- ..::  navbar start ::.. -->
        <x-navbar />
        <!-- ..::  navbar end ::.. -->
        <div class="dashboard-main-body">

            <!-- ..::  breadcrumb  start ::.. -->
            <x-breadcrumb title='{{ isset($title) ? $title : "" }}'
                subTitle='{{ isset($subTitle) ? $subTitle : "" }}' />
            <!-- ..::  header area end ::.. -->

            @yield('content')

        </div>
        <!-- ..::  footer  start ::.. -->
        <x-footer />
        <!-- ..::  footer area end ::.. -->

    </main>

    <!-- ..::  scripts  start ::.. -->
    <x-script script='{!! isset($script) ? $script : "" !!}' />
    <!-- ..::  scripts  end ::.. -->

    <!-- Script sidebar -->
    <script src="{{ asset('assets/js/sidebar.js')}}"></script>
</body>

</html>