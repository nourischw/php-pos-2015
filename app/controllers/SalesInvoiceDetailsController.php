<?php

class SalesInvoiceDetailsController extends BaseController
{
    const RECORD_TYPE_NEW = 1;
    const RECORD_TYPE_UPDATE = 2;

    /**
     * 	@var string $layout Define the layout template
     */
    protected $layout = 'layouts.normal';

    public function showView($id = null)
    {
      if (empty($id)) {
          return Redirect::route("sales_invoice_list");
      }

      $record_id = intval($id);
      Session::put('page', 'sales_invoice');
      Session::put('record_id', $record_id);
      $sales_invoice_data = SalesInvoice::GetSalesInvoiceList($record_id);
      extract($sales_invoice_data[0], EXTR_PREFIX_ALL, 'si');
      $sales_invoice_items_data = SalesInvoice::GetSalesInvoiceItems($record_id);
      $sales_invoice_payment = SalesInvoice::GetPaymentList($record_id);
      $payment_type_list = Config::get("payment_type");
      $payment_term_list = Config::get("cod_terms");

      foreach ($sales_invoice_payment as $key => $sales_invoice_payments) {
        extract($sales_invoice_payments, EXTR_PREFIX_ALL, 'p');
        $payment_method_list[$key]['payment_name'] = $payment_type_list[$p_payment_type];
        $payment_method_list[$key]['payment_amount'] = $p_payment_amount;
      }
      $total_items = count($sales_invoice_items_data);
      $total_qty = 0;
      foreach ($sales_invoice_items_data as $value) {
        $total_qty += $value['qty'];
      }

      $status_text = null;
      switch($si_status) {
      case 1:
          $status_text = "Finished";
          break;
      case 2:
          $status_text = "Pending";
          break;
      case 3:
          $status_text = "Voided";
          break;
      }

      $sales_invoice_data[0]['status_text'] = $status_text;
      $sales_invoice_data[0]['payment_term_name'] = $payment_term_list[$si_term];

      $data = array(
          'staff_list' => Staff::getSelectList(Session::get('shop_id')),
          'sales_invoice_data' => $sales_invoice_data,
          'sales_invoice_items_data' => $sales_invoice_items_data,
          'payment_method_list' => $payment_method_list,
          'total_items' => $total_items,
          'total_qty' => $total_qty,
          'record_id' => $record_id,
      );
      $css = array(
  			Config::get('css.CSS_LIST') |
  			Config::get('css.CSS_FILE')
  		);
  		$js = array(
  			Config::get('js.JS_FILE')
  		);
      $this->layout
		   ->with('title', "Sales Invoice Details")
		   ->with('css', Config::get('css.CSS_LIST'))
		   ->with('page_css', 'sales_invoice_details')
		   ->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('js.JS_LIST'))
		   ->with('page_js', 'sales_invoice_details')
           ->content = View::make('pages.sales_invoice_details', $data);
    }
}
