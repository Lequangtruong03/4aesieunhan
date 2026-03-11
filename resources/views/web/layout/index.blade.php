<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Client Cinema</title>

    <meta name="google-site-verification" content="1PaYB4dlqRjhgBy-jyq5O89I4a8BzAc3d1E_s1BXLPs" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="telephone=no" name="format-detection">

    <base href="{{asset('')}}">

    <link rel="icon" type="image/png" href="images/favicon/cinema.png ">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">

    <!-- Fontawesome -->
    <link href="/web_assets/fonts/fontawesome/css/all.css" rel="stylesheet" />

    <!-- select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Owl Carousel -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.css">

    @yield('link_css')

    <style>
        @yield('css')
    </style>

</head>

<body>

<div class="wrapper">

    {{-- Header --}}
    @include('web.common.header')

    {{-- Warning --}}
    @if(count($errors) > 0)
    <div class="alert alert-danger">
        @foreach($errors->all() as $arr)
        {{ $arr }}<br>
        @endforeach
    </div>
    @endif

    @if (session('warning'))
    <div class="alert alert-warning">
        {{ session('warning') }}
    </div>
    @endif

    @if (session('danger'))
    <div class="alert alert-danger">
        {{ session('danger') }}
    </div>
    @endif

    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    {{-- Content --}}
    @yield('content')

    {{-- Footer --}}
    @include('web.common.footer')

</div>

{{-- login --}}
@include('web.common.login')


<!-- Zalo cá nhân -->
<a href="https://zalo.me/0971385003"
   target="_blank"
   style="position:fixed;bottom:20px;right:20px;z-index:999;">
   <img src="https://upload.wikimedia.org/wikipedia/commons/9/91/Icon_of_Zalo.svg"
        width="55"
        style="border-radius:50%;box-shadow:0 0 10px rgba(0,0,0,0.3);">
</a>


<!-- ============================================= -->


<!-- Bootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

<!-- JQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>

<!-- select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- html2canvas -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<!-- Owl Carousel -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.js"></script>

<!-- CKEditor -->
<script src="https://cdn.ckeditor.com/ckeditor5/38.1.1/classic/ckeditor.js"></script>

@yield('js')

</body>
</html>