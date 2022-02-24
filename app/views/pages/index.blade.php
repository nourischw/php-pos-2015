<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body>
 @section('content')
<link media="all" type="text/css" rel="stylesheet" href="{{ Config::get('path.CSS') }}index.css" />
<!-- Start of login page block -->
<div id="index">
	<div class="w100 border menu_header">
		<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
			<div class="fs22 menu_sysyem">零售系統 - {{ Session::get('shop_code') }}</div>
		</div>
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
			<div class="fs22 menu_main">主目錄</div>
		</div>
	</div>
	<div class="w100 menu_content">
		<div class="menu_btn">
			<div class="fL small">
				<div class="btn_xs">
					@if (Session::get("staff_group") == 1 || in_array(Config::get('group_permissions.SALES_INVOICE_ACCESS'), Session::get("staff_permission")))
					<a href="{{ Config::get('path.ROOT') }}sales_invoice/order" class="tdNone">	
						<button type="button" id="btn-fn-scan" class="btn btn_border_a btn-default btn_xs" aria-label="Left Align">
							Sales Invoice
						</button>	
					</a>
					@else
						<div class="empty_block"></div>
					@endif
				</div>
				<div class="mg_top btn_xs">
					@if (Session::get("staff_group") == 1 || in_array(Config::get('group_permissions.PURCHASE_ORDER_ACCESS'), Session::get("staff_permission")))
					<a href="{{ Config::get('path.ROOT') }}purchase_order/list" class="tdNone">	
						<button type="button" id="btn-fn-scan" class="btn btn-default btn_xs" aria-label="Left Align">
							Purchase Order
						</button>	
					</a>
					@else
						<div class="empty_block"></div>
					@endif
				</div>
				<div class="mg_top btn_xs">
					@if (Session::get("staff_group") == 1 || in_array(Config::get('group_permissions.GOODS_IN_ACCESS'), Session::get("staff_permission")))
					<a href="{{ Config::get('path.ROOT') }}goods_in/list" class="tdNone">	
						<button type="button" id="btn-fn-scan" class="btn btn_border_r btn-default btn_xs" aria-label="Left Align">
							Goods In
						</button>
					</a>
					@else
						<div class="empty_block"></div>
					@endif
				</div>
				<div class="mg_top btn_xs">
					@if (Session::get("staff_group") == 1 || in_array(Config::get('group_permissions.STOCK_TRANSPORT_ACCESS'), Session::get("staff_permission")))
					<a href="{{ Config::get('path.ROOT') }}stock/list" class="tdNone">	
						<button type="button" id="btn-fn-scan" class="btn btn_border_r btn-default btn_xs" aria-label="Left Align">
							Stock Transport <br />Log
						</button>
					</a>
					@else
						<div class="empty_block"></div>
					@endif
				</div>	
			</div>
			<div class="fL small">
				<div class="btn_xs">
					@if (Session::get("staff_group") == 1 || in_array(Config::get('group_permissions.PRODUCT_ACCESS'), Session::get("staff_permission")))
					<a href="{{ Config::get('path.ROOT') }}product/1" class="tdNone">	
						<button type="button" id="btn-fn-scan" class="btn btn_border_d btn-default btn_xs" aria-label="Left Align">
							Product
						</button>
					</a>
					@else
						<div class="empty_block"></div>
					@endif
				</div>
				<div class="mg_top btn_xs">
					@if (Session::get("staff_group") == 1 || in_array(Config::get('group_permissions.STOCK_TRANSFER_ACCESS'), Session::get("staff_permission")))
					<a href="{{ Config::get('path.ROOT') }}stock_transfer/list" class="tdNone">
						<button type="button" id="btn-fn-scan" class="btn btn_border_c btn-default btn_xs" aria-label="Left Align">
							Stock Transfer
						</button>
					</a>
					@else
						<div class="empty_block"></div>
					@endif
				</div>
				<div class="mg_top btn_xs">
					@if (Session::get("staff_group") == 1 || in_array(Config::get('group_permissions.STOCK_LEVEL_ACCESS'), Session::get("staff_permission")))
					<a href="{{ Config::get('path.ROOT') }}stock_level" class="tdNone">
						<button type="button" id="btn-fn-scan" class="btn btn_border_b btn-default btn_xs" aria-label="Left Align">
							Stock Level
						</button>
					</a>
					@else
						<div class="empty_block"></div>
					@endif
				</div>				
			</div>
			<div class="fL small">
				<div class="btn_xs">
					@if (Session::get("staff_group") == 1 || in_array(Config::get('group_permissions.BRAND_ACCESS'), Session::get("staff_permission")))
					<a href="{{ Config::get('path.ROOT') }}brand" class="tdNone">	
						<button type="button" id="btn-fn-scan" class="btn btn_border_e btn-default btn_xs" aria-label="Left Align">
							Brand
						</button>
					</a>
					@else
						<div class="empty_block"></div>
					@endif
				</div>
				<div class="mg_top btn_xs">
					@if (Session::get("staff_group") == 1 || in_array(Config::get('group_permissions.SN_TRANSACTION_ACCESS'), Session::get("staff_permission")))
					<a href="{{ Config::get('path.ROOT') }}sn_transaction" class="tdNone">
						<button type="button" id="btn-fn-scan" class="btn btn_border_f btn-default btn_xs" aria-label="Left Align">
							SN Transaction
						</button>
					</a>
					@else
						<div class="empty_block"></div>
					@endif
				</div>
				<div class="mg_top btn_xs">
					@if (Session::get("staff_group") == 1 || in_array(Config::get('group_permissions.QUOTATION_ACCESS'), Session::get("staff_permission")))
					<a href="{{ Config::get('path.ROOT') }}quotation/list" class="tdNone">	
						<button type="button" id="btn-fn-scan" class="btn btn_border_g btn-default btn_xs" aria-label="Left Align">
							Quotation
						</button>
					</a>
					@else
						<div class="empty_block"></div>
					@endif
				</div>				
			</div>
			<div class="fL small">
				<div class="btn_xs">
					@if (Session::get("staff_group") == 1 || in_array(Config::get('group_permissions.DEPOSIT_ACCESS'), Session::get("staff_permission")))
					<a href="{{ Config::get('path.ROOT') }}deposit/list" class="tdNone">	
						<button type="button" id="btn-fn-scan" class="btn btn_border_g btn-default btn_xs" aria-label="Left Align">
							Deposit
						</button>
					</a>
					@else
						<div class="empty_block"></div>
					@endif
				</div>
				<div class="mg_top btn_xs">
					@if (Session::get("staff_group") == 1 || in_array(Config::get('group_permissions.CATEGORY_ACCESS'), Session::get("staff_permission")))
					<a href="{{ Config::get('path.ROOT') }}category" class="tdNone">	
						<button type="button" id="btn-fn-scan" class="btn btn_border_h btn-default btn_xs" aria-label="Left Align">
							Category
						</button>
					</a>
					@else
						<div class="empty_block"></div>
					@endif
				</div>				
				<div class="mg_top btn_xs">
					@if (Session::get("staff_group") == 1 || in_array(Config::get('group_permissions.REPORT_ACCESS'), Session::get("staff_permission")))
					<a href="{{ Config::get('path.ROOT') }}report" class="tdNone">	
						<button type="button" id="btn-fn-scan" class="btn btn_border_h btn-default btn_xs" aria-label="Left Align">
							Report
						</button>	
					</a>
					@else
						<div class="empty_block"></div>
					@endif
				</div>
			</div>

			<div class="fL small">
				<div class="btn_xs">
					@if (Session::get("staff_group") == 1 || in_array(Config::get('group_permissions.SUPPLIER_ACCESS'), Session::get("staff_permission")))
					<a href="{{ Config::get('path.ROOT') }}supplier" class="tdNone">	
						<button type="button" id="btn-fn-scan" class="btn btn_border_g btn-default btn_xs" aria-label="Left Align">
							Supplier
						</button>
					</a>
					@else
						<div class="empty_block"></div>
					@endif
				</div>
				<div class="mg_top btn_xs">
					@if (Session::get("staff_group") == 1 || in_array(Config::get('group_permissions.STAFF_ACCESS'), Session::get("staff_permission")))
					<a href="{{ Config::get('path.ROOT') }}staff/list" class="tdNone">	
						<button type="button" id="btn-fn-scan" class="btn btn_border_g btn-default btn_xs" aria-label="Left Align">
							Staff
						</button>
					</a>
					@else
						<div class="empty_block"></div>
					@endif
				</div>
				<div class="mg_top btn_xs">
					@if (Session::get("staff_group") == 1 || in_array(Config::get('group_permissions.STAFF_GROUP_ACCESS'), Session::get("staff_permission")))
					<a href="{{ Config::get('path.ROOT') }}staff_group/list" class="tdNone">	
						<button type="button" id="btn-fn-scan" class="btn btn_border_h btn-default btn_xs" aria-label="Left Align">
							Staff Group
						</button>
					</a>
					@else
						<div class="empty_block"></div>
					@endif
				</div>
			</div>

			<div class="fL small">
				<div class="btn_xs">
					@if (Session::get("staff_group") == 1 || in_array(Config::get('group_permissions.STOCK_WITHDRAW_ACCESS'), Session::get("staff_permission")))
					<a href="{{ Config::get('path.ROOT') }}stock_withdraw/list" class="tdNone">	
						<button type="button" id="btn-fn-scan" class="btn btn_border_h btn-default btn_xs" aria-label="Left Align">
							Stock Withdraw
						</button>
					</a>
					@else
						<div class="empty_block"></div>
					@endif
				</div>
			
				<div class="mg_top btn_xs">
					<a href="{{ Config::get('path.ROOT') }}change_password" class="tdNone">	
						<button type="button" id="btn-fn-scan logout_btn" class="btn btn_border_i btn-default btn_xs" aria-label="Left Align">
							Change Password
						</button>
					</a>
				</div>
				<div class="mg_top btn_xs">
					<a href="{{ Config::get('path.ROOT') }}logout" class="tdNone">	
						<button type="button" id="btn-fn-scan logout_btn" class="btn btn_border_i btn-default btn_xs" aria-label="Left Align">
							Exit
						</button>
					</a>
				</div>
			</div>
		</div>
	</div>
	<footer class="w100 border menu_footer"></footer>	
</div>
	<script data-main="{{ Config::get('path.ROOT') }}app/js/main" src="{{ Config::get('path.ROOT') }}app/js/app/index.js"></script>		
<!-- End of login order page block -->
@stop
</body>
</html>