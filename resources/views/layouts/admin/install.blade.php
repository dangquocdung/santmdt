<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <title>@yield('title')</title>
  <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
  <link rel="stylesheet" href="{{ URL::asset('bootstrap/css/bootstrap.min.css') }}" />
  <link rel="stylesheet" href="{{ URL::asset('font-awesome-4.6.3/css/font-awesome.min.css') }}" />
  <link rel="stylesheet" href="{{ URL::asset('dist/css/Admin.min.css') }}" />
  <link rel="stylesheet" href="{{ URL::asset('plugins/iCheck/square/blue.css') }}" />
  <link rel="stylesheet" href="{{ URL::asset('css/admin/shopist.css') }}" />
</head>
<body class="hold-transition register-page">
  @yield('content')
  
  <script type="text/javascript" src="{{ URL::asset('jquery/jquery-1.10.2.js') }}"></script>
  <script type="text/javascript" src="{{ URL::asset('bootstrap/js/bootstrap.min.js') }}"></script>
  <script type="text/javascript" src="{{ URL::asset('plugins/iCheck/icheck.min.js') }}"></script>
  <script>
    $(function () {
      $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%'
      });
    });
  </script>
</body>
</html>