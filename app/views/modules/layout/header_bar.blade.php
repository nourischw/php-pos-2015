<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="collapse navbar-collapse" id="navbar">
            <ul class="nav nav-pills pull-left">
                <li><h4>零售系統</h4></li>
            </ul>

            <ul class="nav nav-pills pull-right">
                <li><h4>商店: {{ Session::get('shop_code') }}</h4></li>
                <!--li><h4>12:00:00 15/04/2015</h4></li>
                <li><button class="btn btn-danger" type="submit">介面上鎖</button></li-->
            </ul>
        </div>
        <!--/.nav-collapse -->
    </div>
</nav>