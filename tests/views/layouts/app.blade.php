<html>

<head>
    <title>App Name - @yield('title')</title>
</head>

<body>
    <div class="sidebar">
        @section('sidebar')
        <p>This is the master sidebar.</p>
        @show
    </div>
    <div class="container">
        @yield('content')
    </div>
</body>

</html>
