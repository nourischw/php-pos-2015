<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the Closure to execute when that URI is requested.
  |
 */

/* Set website locale */
$languages = array('zh', 'en');
$locale = Request::segment(1);

if (!in_array($locale, $languages)) {
    $locale = 'zh';
}

App::setLocale($locale);

// Set 404 Not found routing path
/*
App::missing(function()
{
	return Redirect::route('page_not_found');
});
*/

// Set shop_code only page redirect path
Route::filter('login_require', function() {
    if (!Session::has('shop_code')) {
        return Redirect::route('login');
    }
});

Route::get('/', ['as' => 'home', 'before' => 'login_require', 'uses' => 'IndexController@showView']);
Route::get('/login', ['as' => 'login', 'uses' => 'LoginController@showView']);
Route::post('/login_process', 'LoginController@processLogin');
Route::get('/logout', 'LoginController@processLogout');

Route::get('/reciept', array('as' => 'reciept', 'before' => 'login_require', 'uses' => 'recieptController@showView') );
Route::post('/create_purchase_order', array('as' => 'add_purchase_order', 'uses' => 'PurchaseOrderController@addPurchaseOrder') );
Route::post('/create_order', array('as' => 'add_sales_order', 'uses' => 'SalesOrderController@AddSalesOrder') );
Route::get('/print_sales_order', array('uses' => 'SalesOrderController@PrintSalesOrder') );

Route::post('/get_product_info', 'ProductController@getInfo');
Route::post('/get_supplier_info', 'SupplierController@getInfo');
Route::get("/inventory", ['as' => 'inventory', 'before' => 'login_require', 'uses' => 'InventoryController@showView']);
Route::get('/inventory/{page}', ['as' => 'inventory', 'before' => 'login_require', 'uses' => 'InventoryController@showView']);

Route::get('/generate_onetime_token', 'BaseController@generateOnetimeToken');

Route::group(['before' => 'login_require'], function() {
	Route::get('/index', 'IndexController@showView');
	Route::post("/check_staff_account", 'StaffController@checkStaffAccount');

	/* Sales order routes */
	Route::get('/sales_invoice', ['as' => 'sales_invoice_list', 'uses' => 'SalesInvoiceController@showListView']);
	Route::get('/sales_invoice/list', ['as' => 'sales_invoice_list', 'uses' => 'SalesInvoiceController@showListView']);
	Route::get('/sales_invoice/edit/{id}', 'SalesInvoiceController@showEditView');
	Route::get('/sales_invoice/details/{id}', 'SalesInvoiceController@showDetailsView');
	Route::get('/sales_invoice/print/{id}', 'SalesInvoiceController@printPDF');
	Route::get('/sales_invoice/order', 'SalesInvoiceController@order');
	Route::post('/sales_invoice/search', 'SalesInvoiceController@search');
	Route::post('/sales_invoice/create', 'SalesInvoiceController@create');
	Route::post('/sales_invoice/update', 'SalesInvoiceController@update');
	Route::post('/sales_invoice/delete', 'SalesInvoiceController@delete');
	Route::post('/sales_invoice/set_status', 'SalesInvoiceController@setStatus');
	Route::post('/sales_invoice/update_status', 'SalesInvoiceController@updateStatus');
	Route::post('/sales_invoice/update_si_status', 'SalesInvoiceController@updateSIStatus');
	Route::post('/sales_invoice/get_quick_item', 'SalesInvoiceController@getQuickItem');

	/* Purchase order */
	Route::get('/purchase_order', ['as' => 'purchase_order_list', 'uses' => 'PurchaseOrderController@showListView']);
	Route::get('/purchase_order/list', ['as' => 'purchase_order_list', 'uses' => 'PurchaseOrderController@showListView']);
	Route::get('/purchase_order/edit/{id?}', 'PurchaseOrderController@showEditView');
	Route::get('/purchase_order/details/{id}', 'PurchaseOrderController@showDetailsView');
	Route::get('/purchase_order/print/{id}', 'PurchaseOrderController@printPDF');
	Route::post('/purchase_order/search', 'PurchaseOrderController@search');
	Route::post('/purchase_order/create', 'PurchaseOrderController@create');
	Route::post('/purchase_order/update', 'PurchaseOrderController@update');
	Route::post('/purchase_order/delete', 'PurchaseOrderController@remove');
	Route::post('/purchase_order/update_status', 'PurchaseOrderController@updateStatus');

	/* Goods in */
	Route::get('/goods_in', ['as' => 'goods_in_list', 'uses' => 'GoodsInController@showListView']);
	Route::get('/goods_in/list', ['as' => 'goods_in_list', 'uses' => 'GoodsInController@showListView']);
	Route::get('/goods_in/details/{id}', 'GoodsInController@showDetailsView');
	Route::get('/goods_in/print/{id}', 'GoodsInController@printPDF');
	Route::get('/goods_in/order', 'GoodsInController@order');
	Route::post('/goods_in/search', 'GoodsInController@search');
	Route::post('/goods_in/create', 'GoodsInController@create');
	Route::post('/goods_in/getinfo', 'GoodsInController@getInfo');
	Route::post('/get_goods_in_product', 'GoodsInController@getProductGoodsIn');
	Route::post('/get_goods_in_purchase_order', 'GoodsInController@getPurchaseOrderGoodsIn');
	Route::post('/get_goods_in_supplier', 'GoodsInController@getSupplierGoodsIn');
	Route::post('/get_po_item', 'GoodsInController@getPurchaseOrderItem');

	/* Stock transfer */
	Route::get('/stock_transfer', ['as' => 'stock_transfer_list', 'uses' => 'StockTransferController@showListView']);
	Route::get('/stock_transfer/list', ['as' => 'stock_transfer_list', 'uses' => 'StockTransferController@showListView']);
	Route::get('/stock_transfer/edit/{id?}', 'StockTransferController@showEditView');
	Route::get('/stock_transfer/details/{id}', 'StockTransferController@showDetailsView');
	Route::get('/stock_transfer/print/{id}', 'StockTransferController@printPDF');
	Route::post('/stock_transfer/search', 'StockTransferController@search');
	Route::post('/stock_transfer/create', 'StockTransferController@create');
	Route::post('/stock_transfer/update', 'StockTransferController@update');
	Route::post('/stock_transfer/delete', 'StockTransferController@remove');
	Route::post('/stock_transfer/confirm_deliver', 'StockTransferController@confirmDeliver');
	Route::post('/stock_transfer/finish', 'StockTransferController@finish');
	Route::post('/stock_transfer/cancel', 'StockTransferController@cancel');
	Route::post('/stock_transfer/retransfer', 'StockTransferController@retransfer');

	/* Stock withdraw */
	Route::get('/stock_withdraw', ['as' => 'stock_withdraw_list', 'uses' => 'StockWithdrawController@showListView']);
	Route::get('/stock_withdraw/list', ['as' => 'stock_withdraw_list', 'uses' => 'StockWithdrawController@showListView']);
	Route::get('/stock_withdraw/edit/{id?}', 'StockWithdrawController@showCreateView');
	Route::get('/stock_withdraw/details/{id}', 'StockWithdrawController@showDetailsView');
	Route::post('/stock_withdraw/search', 'StockWithdrawController@search');
	Route::post('/stock_withdraw/create', 'StockWithdrawController@create');
	Route::post('/stock_withdraw/delete', 'StockWithdrawController@remove');
	Route::post('/stock_withdraw/finish', 'StockWithdrawController@finish');

	/* Stock level */
	Route::get('/stock_level', 'StockLevelController@showView');
	Route::post('/stock_level/search', 'StockLevelController@search');
	Route::post('/stock_level/get_product_stock_level', 'StockLevelController@getProductStockLevel');

	/* Quotation */
	Route::get('/quotation', ['as' => 'quotation_list', 'uses' => 'QuotationController@showListView']);
	Route::get('/quotation/list', ['as' => 'quotation_list', 'uses' => 'QuotationController@showListView']);
	Route::get('/quotation/edit/{id?}', 'QuotationController@showEditView');
	Route::get('/quotation/details/{id}', 'QuotationController@showDetailsView');
	Route::get('/quotation/print/{id}', 'QuotationController@printPDF');
	Route::post('/quotation/search', 'QuotationController@search');
	Route::post('/quotation/create', 'QuotationController@create');
	Route::post('/quotation/update', 'QuotationController@update');
	Route::post('/quotation/delete', 'QuotationController@remove');
	Route::post('/quotation/update_status', 'QuotationController@updateStatus');
	Route::post('/quotation/get_quotation_items', 'QuotationController@getItems');
	Route::post('/quotation/get_quotation_deposit_items', 'QuotationController@getDepositItems');
	
	
	/* Deposit */
	Route::get('/deposit', ['as' => 'deposit_list', 'uses' => 'DepositController@showListView']);
	Route::get('/deposit/list', ['as' => 'deposit_list', 'uses' => 'DepositController@showListView']);
	Route::get('/deposit/edit/{id?}', 'DepositController@showEditView');
	Route::get('/deposit/details/{id?}', 'DepositController@showDetailsView');
	Route::get('/deposit/print/{id}', 'DepositController@printPDF');
	Route::post('/deposit/search', 'DepositController@search');
	Route::post('/deposit/create', 'DepositController@create');
	Route::post('/deposit/update', 'DepositController@update');
	Route::post('/deposit/delete', 'DepositController@remove');
	Route::post('/deposit/update_status', 'DepositController@updateStatus');
	Route::post('/deposit/get_deposit_items', 'DepositController@getItems');

	/* Report routes */
	Route::get('/report', 'ReportController@showView');
	Route::post('/generate_sales_report', 'ReportController@generateReport');
	Route::post('/generate_dailysales_report', 'ReportController@generateDailysalesReport');
	Route::post('/generate_goodsin_report', 'ReportController@generateGoodsinReport');
	Route::post('/generate_realtime_inventory_report', 'ReportController@generateRealtimeInventoryReport');

	/* SN transaction routes */
	Route::get('/sn_transaction', 'SNTransactionController@showView');
	Route::post('/sn_transaction/search', 'SNTransactionController@search');
	Route::post('/sn_transaction/update_serial_number', 'SNTransactionController@updateSerialNumber');

	/* Product */
	// Route::get('/product', 'ProductOrderController@showView');
	Route::get('/product/{page}', 'ProductOrderController@showView');
	Route::post('/product/search', 'ProductOrderController@search');
	Route::post('/product/create', 'ProductOrderController@create');
	Route::post("/product/edit", "ProductOrderController@edit");
	Route::post("/product/remove", "ProductOrderController@remove");
	Route::post("/product/keyword", "ProductOrderController@setKeywordSession");
	Route::post("/product/upload", "ProductOrderController@uploadImages");
	Route::post("/product/check", "ProductOrderController@check");
	Route::get("/product/images/{images_id}", "ImagesController@getProductImages");

	Route::get('/search_product', 'ProductController@SearchProduct');
	Route::post('/get_product', 'ProductController@GetProduct');

	/* Brand */
	Route::get('/brand', 'BrandController@showView');
	Route::post('/brand/search', 'BrandController@brandSearch');
	Route::post('/brand/create', 'BrandController@createBrand');
	Route::post('/brand/edit', 'BrandController@editBrand');
	Route::post('/brand/remove', 'BrandController@removebrand');
	Route::post('/get_brand', 'BrandController@getBrand');
	Route::post('/set_brand', 'BrandController@setBrand');

	/* Category */
	Route::get('/category', 'CategoryController@showView');
	Route::post('/category_search', 'CategoryController@categorySearch');
	Route::post('/create_category', 'CategoryController@createCategory');
	Route::post('/edit_category', 'CategoryController@editCategory');
	Route::post('/remove_category', 'CategoryController@removecategory');
	Route::post('/get_category', 'CategoryController@getCategory');
	Route::post('/set_category', 'CategoryController@setCategory');

	/* Supplier */
	Route::get("/supplier", ['as' => 'supplier', 'uses' => "SupplierController@showView"]);
	Route::post("/supplier/create", "SupplierController@create");
	Route::post("/supplier/update", "SupplierController@update");
	Route::post("/supplier/delete", "SupplierController@remove");
	Route::post('/supplier/get', 'SupplierController@get');
	Route::post('/supplier/check_supplier_code', 'SupplierController@checkSupplierCode');
	Route::post('/set_supplier', 'SupplierController@setSupplier');
	Route::post('/supplier_search', 'SupplierController@supplierSearch');

	/* Staff */
	Route::get('/staff', ['as' => 'staff_list', 'uses' => 'StaffController@showListView']);
	Route::get('/staff/list', ['as' => 'staff_list', 'uses' => 'StaffController@showListView']);
	Route::get('/staff/edit/{id?}', 'StaffController@showEditView');
	Route::get('/staff/details/{id}', 'StaffController@showDetailsView');
  	Route::get('/staff/attendance', 'StaffAttendanceController@showAttendanceView');
	Route::post('/staff/search', 'StaffController@search');
	Route::post('/staff/create', 'StaffController@create');
	Route::post('/staff/update', 'StaffController@update');
	Route::post('/staff/delete', 'StaffController@remove');
	Route::post('/staff/check_staff_code', 'StaffController@checkStaffCode');
	Route::post('/staff/reset_password', ['as' => 'reset_staff_password', 'uses' => 'StaffController@showResetPasswordView']);
	Route::post('/staff/reset_password_process', 'StaffController@processResetStaffPassword');

	/* Staff group */
	Route::get('/staff_group', ['as' => 'staff_group_list', 'uses' => 'StaffGroupController@showListView']);
	Route::get('/staff_group/list', ['as' => 'staff_group_list', 'uses' => 'StaffGroupController@showListView']);
	Route::get('/staff_group/edit/{id?}', 'StaffGroupController@showEditView');
	Route::get('/staff_group/details/{id}', 'StaffGroupController@showDetailsView');
	Route::post('/staff_group/search', 'StaffGroupController@search');
	Route::post('/staff_group/create', 'StaffGroupController@create');
	Route::post('/staff_group/update', 'StaffGroupController@update');
	Route::post('/staff_group/delete', 'StaffGroupController@remove');

	/* Change password */
	Route::get('/change_password', ['as' => 'change_password', 'uses' => 'ChangePasswordController@showView']);
	Route::post('/change_password/process', 'ChangePasswordController@process');

	/* Stock Transport Log */
	Route::get('/stock/list', ['as' => 'stock_list', 'uses' => 'StockTransportLogController@showListView']);
	Route::post('stock/search', 'StockTransportLogController@searchList');
	Route::get('/stock_transport_log/{id}', 'StockTransportLogController@showLogView');
	Route::post('/stock_transport_log/search', 'StockTransportLogController@searchLog');

	/* Not authorized page route */
	Route::get('/forbidden', ['as' => 'forbidden', 'uses' => 'ForbiddenController@showView']);

	/* Popup page routes */
	Route::post('/product_popup_list', 'ProductOrderController@getPopupList');
	Route::post('/supplier_popup_list', 'SupplierController@getPopupList');
	Route::post('/stock_popup_list', 'StockController@getPopupList');
	Route::post('/stock_withdraw_popup_list', 'StockController@getWithdrawPopupList');
	Route::post('/purchase_order_popup_list', 'PurchaseOrderController@getPopupList');
	Route::post('/brand_popup_list', 'BrandController@searchBrand');
	Route::post('/quotation_popup_list', 'QuotationController@getPopupList');
	Route::post('/deposit_popup_list', 'DepositController@getPopupList');
});
