<?php

class SalesInvoiceListController extends BaseController
{
    const DELETE_TYPE_PURCHASE_ORDER = 1;
    const DELETE_TYPE_PURCHASE_ORDER_ITEM = 2;

    /**
     * 	@var string $layout Define the layout template
     */
    protected $layout = 'layouts.normal';

    /**
     * 	Show index page
     * 	@used-by (view) index.blade.php
     * 	@return void
     */
    public function showView()
    {
        Session::put("page", "sales_invoice_list");
        $status = Session::get('sales_list_status');
        if (empty($status)){
            Session::put('sales_list_status', '1');
        }
        $sales_invoice_list = SalesInvoice::getList();
        extract($sales_invoice_list);
        $have_record = ($total_records > 0) ? true : false;
        $page = (Input::has("page")) ? intval(Input::get("page")) : 1;
        $end_page = min(10, $total_pages);

        $data = array(
            'list_data' => $list_data,
            'shop_list' => Shop::getSelectList(),
            'total_records' => $total_records,
            'total_pages' => $total_pages,
            'page' => $page,
            'end_page' => $end_page,
            'have_record' => $have_record
        );

        $css = array(
			Config::get('css.CSS_LIST') |
			Config::get('css.CSS_FILE')
		);
		$js = array(
			Config::get('js.JS_FORM_VALIDATOR') |
			Config::get('js.JS_LIST') |
			Config::get('js.JS_FILE')
		);
        $this->layout
			 ->with('title', "Sales Invoice List")
			 ->with('css', Config::get('css.CSS_LIST'))
			 ->with('page_css', 'sales_invoice_list')
			 ->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('js.JS_LIST'))
			 ->with('page_js', 'sales_invoice_list')
             ->content = View::make('pages.sales_invoice_list', $data);
    }

	public function setStatus()
	{
		$status = Input::get("status");
		Session::put('sales_list_status', $status);
		return $status;
	}

    public function getList()
    {
        $list = SalesInvoice::getList();
        return View::make('list_rows.sales_invoice_list', $list);
    }

    public function updateSIStatus()
    {
        extract(Input::all());
        $status = intval($status);

        if (empty($record_id)) {
            return 0;
        }

        if ($status !== 1 && $status !== 3 && $status !== 4) {
            return 0;
        }
        $result = SalesInvoice::updateSIStatus();
        return $result;
    }

    public function deletePurchaseOrder()
    {
        if (Session::get("page") !== "sales_invoice_list" &&
            Session::get("page") !== "purchase_order_edit") {
            return 0;
        }

        $record_id = Input::get("record_id");
        if (empty(Input::get("record_id"))) {
            return 0;
        }

        $record_ids = explode(",", $record_id);
        foreach ($record_ids as $id) {
            $all_po_ids[] = intval($id);
        }

        $result = PurchaseOrder::deletePurchaseOrder($all_po_ids);
        if ($result) {
            PurchaseOrderItems::deleteRelatedPurchaseOrderItems($all_po_ids);
            return 1;
        }

        return 0;
    }
	
}
