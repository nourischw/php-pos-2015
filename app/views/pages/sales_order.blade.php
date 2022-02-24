<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>

<body>
    @section('content')
    <link href="{{ Config::get('path.CSS') }}sales_order.css" media="all" rel="stylesheet" type="text/css">
    @include('modules/pop_up/sales_order/sales_order_confirm_block')
    @include('modules/pop_up/sales_order/sales_order_result_block')
    @include('modules/pop_up/sales_order/sales_order_product_search_block')

    <!-- Start of sales order page block -->
    <form action="/create_order" id="processOrder" method="post" name="processOrder"></form>

    <div class="container main_content">
        <div class="row">
            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
                <div>
                    <div class="pull-left sales_order_add_bar btn-group" data-toggle="buttons">
                        <label class="btn btn-default active"><input autocomplete="off" data-label="序號" checked name="order_search_type" class="search_type" data-search_type="1" type="radio" value='1'>序號</label>
                        <label class="btn btn-default"><input autocomplete="off" data-label="IMEI" name="order_search_type" type="radio" value='2' class="search_type" data-search_type="2" >IMEI</label>
                        <label class="btn btn-default"><input autocomplete="off" data-label="Barcode" name="order_search_type" type="radio" value='3' class="search_type" data-search_type="3">Barcode</label>
						<input type="hidden" id="search_type" name="search_type">
                    </div>

                    <div class="pull-left sales_order_add_bar" style="width: 330px">
                        <div class="input-group">
                            <input class="form-control" id="order-product-code" name="order-product-code" type="hidden" value="">
                            <input class="form-control" id="order-key-number" name="order-key-number" placeholder="序號" type="text">
                            <div class="input-group-addon">X</div>
                            <input class="form-control" id="order-qty" name="order-qty" placeholder="數量" style="width: 80px" type="number">
                        </div>
                    </div>

                    <div class="pull-left sales_order_add_bar" style="width: 130px">
                        <div class="input-group">
                            <input class="form-control" id="order-discount" name="order-discount" pattern="[0-9]+([\.|,][0-9]+)?" placeholder="折扣" step="5" type="number">
                            <div class="input-group-addon">%</div>
                        </div>
                    </div>

                    <div class="pull-left sales_order_add_bar" style="width: 150px">
                        <input class="form-control" disabled id="order-total" name="order-total" placeholder="合計" type="text" value="">
                    </div>

                    <div class="pull-right">
                        <div class="pull-left sales_order_add_bar"><button class="btn btn-default" id="btn-order-add">新增</button></div>
                        <div class="pull-left sales_order_add_bar"><button class="btn btn-default" id="btn-order-clear">清除</button></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <table class="table table-striped" id="cart-table">
                            <thead>
                                <tr>
                                    <td>序號</td>
                                    <td>貨品名稱</td>
                                    <td>數量</td>
                                    <td>單價</td>
                                    <td>折扣</td>
                                    <td class='text-right'>小計</td>
                                </tr>
                            </thead>

                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                @include('modules.fn_menu.sales_order_fn')
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
                    @include('modules.footer.sales_order_footer')
                </div>
            </div>
        </div>
    </footer>
    <script src="{{ Config::get('path.ROOT') }}app/js/app/sales_order.js"></script>
    <script src="{{ Config::get('path.ROOT') }}app/js/libs/donetyping.js"></script>
    <!-- End of sales order page block -->
    @stop
</body>
</html>