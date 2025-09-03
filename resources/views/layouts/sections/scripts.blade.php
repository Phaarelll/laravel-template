<!-- BEGIN: Vendor JS-->
<script src="{{ asset(mix('assets/vendor/libs/jquery/jquery.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/popper/popper.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/js/bootstrap.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')) }}"></script>

<script src="{{ asset(mix('assets/vendor/js/menu.js')) }}"></script>
<!-- endbuild -->

<!-- Vendors JS -->
@yield('vendor-script')

<!-- Main JS -->
<script src="{{ asset(mix('assets/js/main.js')) }}"></script>

<!-- Dark Mode JS -->
<script src="{{ asset('assets/js/dark-mode.js') }}"></script>

<!-- Page JS -->
@yield('page-script')
<!-- Pricing Modal JS-->
@stack('pricing-script')
<!-- END: Pricing Modal JS-->
