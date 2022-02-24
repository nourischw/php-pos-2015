<!DOCTYPE html>

<html lang="zh-hanT">
<head>
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta charset="utf8">
    <meta content="" name="keywords">
    <meta content="" name="description">
    <link href="logo.ico" rel="shortcut icon">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">

    <title>Vlab - POS</title>
</head>

<body>
    <link media="all" type="text/css" rel="stylesheet" href="{{ Config::get('path.CSS') }}bootstrap/css/bootstrap.css" />
    <link media="all" type="text/css" rel="stylesheet" href="{{ Config::get('path.CSS') }}sticky-footer.css" />
    <link media="all" type="text/css" rel="stylesheet" href="{{ Config::get('path.CSS') }}common.css" />
    <!--[if lt IE 9]>
        <script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
        <script type='text/javascript' src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <script type='text/javascript' src="//cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.js"></script>
    <![endif]-->
    <link href="{{ Config::get('path.CSS') }}bootstrap-lightbox.min.css" media="all" rel="stylesheet" type="text/css">
    @if (isset($css))
        @foreach ($css as $css_file)
    <link media="all" type="text/css" rel="stylesheet" href="{{ Config::get('path.CSS'),$css_file }}.css" />
        @endforeach
    @endif    
    <style>
        #layout{ width: 1180px; }
        .btn-fn-key{ width: 160px; height: 40px; margin: 2px 10px; }
        .sales_order_add_bar{ margin: 0px 2px; }
        .main_content{ margin-top: 80px; }
        header{ background-color: #f5f5f5; }
    </style>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js" type="text/javascript"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="{{ Config::get('path.ROOT') }}app/js/libs/bootstrap-lightbox.js" type="text/javascript"></script>
    <script src="{{ Config::get('path.ROOT') }}app/js/libs/shortcut.js" type="text/javascript"></script>
	<script src="{{ Config::get('path.ROOT') }}app/js/app/common.js" type="text/javascript"></script>
    @if (isset($js))
        @foreach ($js as $js_file)
    <script src="{{ Config::get('path.ROOT') }}app/js/app/{{ $js_file }}.js"></script>
		
        @endforeach
    @endif
    
    @include('modules.layout.header_bar')

    <!-- Begin page content -->
    @yield('content')

    <div class="fixed_background" id="fixed_background"></div>
</body>
</html>