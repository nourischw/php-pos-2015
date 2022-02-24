<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>

<body>
    @section('content')
    <link href="{{ Config::get('path.CSS') }}login.css" media="all" rel="stylesheet" type="text/css" />

    <!-- Start of login page block -->
    <div id="login">
        <div class="container">
            {{ Form::open(array('id'=> 'form_login')) }}
            <div class="row">
                <div class="text pos_login">
                    <div class="login_bg">
                        <div class="taC fs18 bold border login_text">請先登入: </div>
						<div class="w100 input-group">
							<div class="input-group-addon sales_confirm_field">商店號碼</div>
                            <select class="form-control" id="shop_code" name="shop_code">
                                @foreach ($shop_list as $shop)
                                    <?php extract($shop); ?>
                                <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </select>
						</div>
						<div class="w100 input-group">
							<div class="input-group-addon sales_confirm_field">收銀員ID</div>
							<input type="text" class="form-control" id="staff_code" name="staff_code" placeholder="收銀員">
						</div>							
						<div class="w100 input-group">
							<div class="input-group-addon sales_confirm_field">密碼</div>
							<input type="password" class="form-control" id="password" name="password" placeholder="密碼" autocomplete="off">
						</div>
                        <div class="taR login_btn">
                            <input class="btn btn-default btn-fn-key" id="btn-fn-minusone" type="reset" value="清除">
                            <input class="btn btn-default btn-fn-key" id="btn-fn-minusone" type="submit" value="登入">
                        </div>
                        <div class="fL taC fs12 w100 alert alert-info" id="login_process">登入處理中, 請稍候...</div>
                        <div class="fL taC fs12 w100 alert alert-danger" id="login_failed">無效號碼，輸入錯誤 或 未註冊</div>
                    </div>
                </div>
            </div>
            <!--
            <input type="hidden" id="password_hash" name="password" /> -->
			{{ Form::close() }}
        </div>
    </div>
    <script src="{{ Config::get('path.JS')}}common.js"></script>
    <script src="{{ Config::get('path.JS')}}login.js"></script>
    <!-- End of login order page block -->
    @stop
</body>
</html>