<!-- App favicon -->
    {{-- <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" /> --}}

    <!-- Bootstrap & Select2 (CDN) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">


<!-- Favicon -->
<link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

<!-- Vendor CSS -->
<link href="{{ asset('assets/css/vendor.min.css') }}" rel="stylesheet" type="text/css">

<!-- Icons CSS -->
<link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css">

<!-- App CSS -->
<link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css">

<!-- Theme Config JS -->
<script src="{{ asset('assets/js/config.min.js') }}"></script>

<!-- jQuery (CDN) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 (CDN) -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Vendor JS -->
<script src="{{ asset('assets/js/vendor.js') }}"></script>

<!-- App JS -->
<script src="{{ asset('assets/js/app.js') }}"></script>

<!-- Vector Map JS -->
<script src="{{ asset('assets/vendor/jsvectormap/js/jsvectormap.min.js') }}"></script>
<script src="{{ asset('assets/vendor/jsvectormap/maps/world-merc.js') }}"></script>
<script src="{{ asset('assets/vendor/jsvectormap/maps/world.js') }}"></script>

<!-- Dashboard JS -->
<script src="{{ asset('assets/js/pages/dashboard.js') }}"></script>

<!-- App favicon -->
     {{-- <link rel="shortcut icon" href="assets/images/favicon.ico">

     <!-- Vendor css (Require in all Page) -->
     <link href="assets/css/vendor.min.css" rel="stylesheet" type="text/css" />

     <!-- Icons css (Require in all Page) -->
     <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />

     <!-- App css (Require in all Page) -->
     <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />

     <!-- Theme Config js (Require in all Page) -->
     <script src="assets/js/config.min.js"></script>

     <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- Gridjs Plugin css -->
    <link href="assets/vendor/gridjs/theme/mermaid.min.css" rel="stylesheet" type="text/css" /> --}}

    {{-- Grid.js --}}
{{-- <script src="https://unpkg.com/gridjs/dist/gridjs.umd.js"></script>
<link href="https://unpkg.com/gridjs/dist/theme/mermaid.min.css" rel="stylesheet" /> --}}

     <style>
        .select2-container {
            width: 100% !important;
            font-size: smaller large!important;
        }
    </style>

    {{-- <style>
/* Make Select2 match Bootstrap form-select font size */
.select2-container--bootstrap-5 .select2-selection {
    font-size: 0.875rem;  /* same as .form-select-sm */
    padding: 0.375rem 0.75rem;
    min-height: calc(1.5em + 0.75rem + 2px);
}
.select2-container--bootstrap-5 .select2-selection__rendered {
    line-height: 1.5;
}
.select2-container--bootstrap-5 .select2-selection__arrow {
    top: 50%;
    transform: translateY(-50%);
}
</style> --}}

<style>
/* Make Select2 look like Bootstrap form-control */
.select2-container--bootstrap-5 .select2-selection__placeholder{
    color: #6e8092;
}

.select2-container--bootstrap-5 .select2-selection {
    display: block;
    height: calc(1.5em + .75rem + 5px);
    padding: .375rem .75rem;
    font-size: .88rem;
    font-weight: 400;
    line-height: 1.5;
    color: #292621;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: .375rem;
    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
    box-shadow: none !important;
    outline: none !important;
}

.select2-container--bootstrap-5 .select2-selection__rendered {
    padding: 2px 20px 0px 3px;
    color: #63768a;
    line-height: 1.5;
}

.select2-container--bootstrap-5 .select2-selection__arrow {
    height: 100%;
    top: 0;
    right: 0.75rem;
}

/* On focus */
.select2-container--bootstrap-5 .select2-selection:focus,
.select2-container--bootstrap-5 .select2-selection:active,
.select2-container--bootstrap-5 .select2-selection--single:focus {
    border: 1px solid #80bdff !important; /* Bootstrap-like focus border */
    box-shadow: none !important;
    outline: none !important;
}

/* Dropdown search box focus */
.select2-container--bootstrap-5 .select2-search__field:focus {
    border: 1px solid #ced4da !important;
    box-shadow: none !important;
    outline: none !important;
}
</style>





