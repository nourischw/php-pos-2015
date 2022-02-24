<?php

class BaseController extends Controller
{
	public function __construct()
	{
		$current_page = "index";
		if (!empty(Request::segment(1))) {
			$current_page = Request::segment(1);
		}
		View::share('current_page', $current_page);
		View::share('title', '');

		// Test permission
		$staff_permission = [
			Config::get("group_permissions.PURCHASE_ORDER_ACCESS"),
			Config::get("group_permissions.PURCHASE_ORDER_CREATE"),
			Config::get("group_permissions.PURCHASE_ORDER_UPDATE"),
			Config::get("group_permissions.PURCHASE_ORDER_DELETE"),
			Config::get("group_permissions.PURCHASE_ORDER_PRINT"),
			Config::get("group_permissions.PURCHASE_ORDER_CONFIRM"),
			Config::get("group_permissions.PURCHASE_ORDER_VOID"),
			Config::get("group_permissions.PURCHASE_ORDER_UNVOID"),
			Config::get("group_permissions.PURCHASE_ORDER_FINISH"),

			Config::get("group_permissions.SALES_INVOICE_ACCESS"),
			Config::get("group_permissions.SALES_INVOICE_CREATE"),
			Config::get("group_permissions.SALES_INVOICE_UPDATE"),
			Config::get("group_permissions.SALES_INVOICE_DELETE"),
			Config::get("group_permissions.SALES_INVOICE_PRINT"),
			Config::get("group_permissions.SALES_INVOICE_CONFIRM"),
			Config::get("group_permissions.SALES_INVOICE_VOID"),
			// Config::get("group_permissions.SALES_INVOICE_UNVOID"),
			// Config::get("group_permissions.SALES_INVOICE_FINISH"),

			Config::get("group_permissions.GOODS_IN_ACCESS"),
			Config::get("group_permissions.GOODS_IN_CREATE"),
			Config::get("group_permissions.GOODS_IN_PRINT"),
			Config::get("group_permissions.GOODS_IN_CONFIRM"),

			Config::get("group_permissions.PRODUCT_ACCESS"),
			Config::get("group_permissions.PRODUCT_CREATE"),
			Config::get("group_permissions.PRODUCT_PRINT"),
			Config::get("group_permissions.PRODUCT_CONFIRM"),

			Config::get("group_permissions.BRAND_ACCESS"),
			Config::get("group_permissions.BRAND_CREATE"),
			Config::get("group_permissions.BRAND_UPDATE"),
			Config::get("group_permissions.BRAND_DELETE"),

			Config::get("group_permissions.CATEGORY_ACCESS"),
			Config::get("group_permissions.CATEGORY_CREATE"),
			Config::get("group_permissions.CATEGORY_UPDATE"),
			Config::get("group_permissions.CATEGORY_DELETE"),

			Config::get("group_permissions.QUOTATION_ACCESS"),
			Config::get("group_permissions.QUOTATION_CREATE"),
			Config::get("group_permissions.QUOTATION_UPDATE"),
			Config::get("group_permissions.QUOTATION_DELETE"),
			Config::get("group_permissions.QUOTATION_PRINT"),
			Config::get("group_permissions.QUOTATION_VOID"),
			Config::get("group_permissions.QUOTATION_UNVOID"),

			Config::get("group_permissions.DEPOSIT_ACCESS"),
			Config::get("group_permissions.DEPOSIT_CREATE"),
			Config::get("group_permissions.DEPOSIT_UPDATE"),
			Config::get("group_permissions.DEPOSIT_DELETE"),
			Config::get("group_permissions.DEPOSIT_PRINT"),
			Config::get("group_permissions.DEPOSIT_VOID"),
			Config::get("group_permissions.DEPOSIT_UNVOID"),

			Config::get("group_permissions.STOCK_TRANSFER_ACCESS"),
			Config::get("group_permissions.STOCK_TRANSFER_CREATE"),
			Config::get("group_permissions.STOCK_TRANSFER_UPDATE"),
			Config::get("group_permissions.STOCK_TRANSFER_DELETE"),
			Config::get("group_permissions.STOCK_TRANSFER_PRINT"),
			Config::get("group_permissions.STOCK_TRANSFER_CONFIRM"),
			Config::get("group_permissions.STOCK_TRANSFER_FINISH"),
            Config::get("group_permissions.STOCK_TRANSFER_CANCEL"),

			Config::get("group_permissions.SUPPLIER_ACCESS"),
			Config::get("group_permissions.SUPPLIER_CREATE"),
			Config::get("group_permissions.SUPPLIER_UPDATE"),
			Config::get("group_permissions.SUPPLIER_DELETE"),

			Config::get("group_permissions.STAFF_ACCESS"),
			Config::get("group_permissions.STAFF_CREATE"),
			Config::get("group_permissions.STAFF_UPDATE"),
			Config::get("group_permissions.STAFF_DELETE"),
			Config::get("group_permissions.STAFF_RESET_PASSWORD"),
			Config::get("group_permissions.STAFF_ATTENDANCE_ACCESS"),
			Config::get("group_permissions.STAFF_ATTENDANCE_CREATE"),

			Config::get("group_permissions.STAFF_GROUP_ACCESS"),
			Config::get("group_permissions.STAFF_GROUP_CREATE"),
			Config::get("group_permissions.STAFF_GROUP_UPDATE"),
			Config::get("group_permissions.STAFF_GROUP_DELETE"),

			Config::get("group_permissions.STOCK_LEVEL_ACCESS"),

			Config::get("group_permissions.SN_TRANSACTION_ACCESS"),

			Config::get("group_permissions.REPORT_ACCESS"),
		];
		//Session::put("staff_permission", $staff_permission);
	}

	protected function checkAllowAccess($permission)
	{
		$is_allow_access = (Session::get("staff_group") == 1 || in_array(Config::get("group_permissions." . $permission), Session::get("staff_permission"))) ? true : false;

		if (!$is_allow_access) {
			return Redirect::to('forbidden')->send();
		}
	}

	protected function checkPermission($permission)
	{
		$result = (Session::get("staff_group") == 1 || in_array(Config::get("group_permissions." . $permission), Session::get("staff_permission"))) ? true : false;
		return $result;
	}

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if (!is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

	public static function generateOnetimeToken()
	{
		$token = bin2hex(openssl_random_pseudo_bytes(16));
		Session::put('onetime_token', $token);
		return $token;
	}
}
