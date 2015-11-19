<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    @yield('title')

    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">


    @yield('css')

</head>
<body>
{{--@section('sidebar')--}}
    {{--This is the master sidebar.--}}
{{--@show--}}

<div class="container">
    @yield('content')
</div>
</body>
</html>