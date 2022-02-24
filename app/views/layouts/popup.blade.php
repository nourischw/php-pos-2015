<!DOCTYPE html>
<html lang="zh-hanT">
<head>
    @include('includes.head_loader')
    @include('includes.css_loader')
    @include('includes.js_loader')
</head>

<body>
    <div id="container">
        @yield('content')
    </div>
</body>
</html>