<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Title Meta -->
    <meta charset="utf-8" />
    <title>H-FlowStock | Customer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully responsive premium admin dashboard template" />
    <meta name="author" content="Techzaa" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    @include('libraries.style')

</head>

<body>

    <!-- START Wrapper -->
    <div class="wrapper">

        <!-- ========== Topbar Start ========== -->
        @include('components.topbar')

        <!-- Right Sidebar (Theme Settings) -->
        @include('components.rightsidebar')

        <!-- ========== Topbar End ========== -->

        <!-- ========== App Menu Start ========== -->
        @include('components.appmenu')

        <!-- ========== App Menu End ========== -->

        <div class="page-content">
            <!-- ==================================================== -->
            <!-- Start right Content here -->
            <!-- ==================================================== -->
            @yield('content')
            <!-- ==================================================== -->
            <!-- End Page Content -->
            <!-- ==================================================== -->
            <!-- ========== Footer Start ========== -->
            @include('components.footer')
            <!-- ========== Footer End ========== -->
        </div>

    </div>
    <!-- END Wrapper -->

    @include('libraries.scripts')

</body>

</html>
