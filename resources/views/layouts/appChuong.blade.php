<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  {{--  <link rel="stylesheet" href="/css/app.css">  --}}
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="/css/all.css">
  <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
  <title>Document</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @yield('header')
</head>
<body>
  @include('inc.navbar')

  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12 col-lg-12">
        @yield('content')
      </div>
    </div>
  </div>  

  <footer id="footer" class="text-center">
    <p>Copyright 2018 &copy; Duong Nguyen IMC</p>
  </footer>

  <script src="/js/app.js"></script>
  <script src="/js/appChuong.js"></script>
  @yield('scripts')
</body>
</html>