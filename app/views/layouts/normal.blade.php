<!DOCTYPE html>
<html lang="zh-hanT">
<head>
    @include('includes.head_loader')
    @include('includes.css_loader')
    @include('includes.js_loader')
</head>

<body>
    <div id="container">
        <header id="layout-header">
            <div class="div_center">
                <div id="layout-header_left_column">
                    <button id="home_button" class="btn btn-primary"><span class="glyphicon glyphicon-home"></span> Home</button>
                    {{ $title }}
                </div>
                <div id="layout-header_right_column">
                    <div class="fL w100">
                        <span class="session_info_label">Shop Code:</span>
                        {{ Session::get('shop_code') }}
                    </div>
                    <div class="fL w100">
                        <span class="session_info_label">Staff Name:</span>
                        {{ Session::get('staff_name') }}
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Begin page content -->
        <div id="layout-content">
        	@yield('content')
		</div>
    </div>
    
    <!--footer id="layout-footer">
        <div class="div_center">
            footer
        </div>
    </footer-->

    <div id="fixed_background"></div>
</body>
</html>