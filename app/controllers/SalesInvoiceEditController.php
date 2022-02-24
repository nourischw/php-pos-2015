<?php

class SalesInvoiceEditController extends BaseController
{
    /**
     * 	@var string $layout Define the layout template
     */
    protected $layout = 'layouts.normal';

    /**
     * 	Show index page
     * 	@used-by (view) index.blade.php
     * 	@return void
     */
    public function showView($id = null)
    {
      Session::forget('record_id');

      $record_id = (!empty($id)) ? intval($id) : 0;

      if ($record_id > 0) {
        $sales_invoice_list = SalesInvoice::getSalesInvoiceEditList($record_id);
      }
      if($sales_invoice_list != false){
        $data = array(
          'record_id' => $record_id,
          'shop_list' => Shop::getSelectList(),
          'payment_term_list' => Config::get('cod_terms'),
          'payment_type_list' => Config::get('payment_type'),
          'is_update' => ($record_id > 0) ? true : false,
          'sales_invoice_edit_list' => $sales_invoice_list
        );
        $this->layout
			 ->with('title', "Sales Invoice Edit")
			 ->with('css', Config::get('css.CSS_LIST'))
			 ->with('page_css', 'sales_invoice_edit')
			 ->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('js.JS_LIST'))
			 ->with('page_js', 'sales_invoice_edit')		
			 ->content = View::make('pages.sales_invoice_edit', $data);
      }
    }
    public function updateSalesInvoice()
    {
      $cashier = Input::get("cashier_id");
      $password = Input::get("cashier_password");
      $result = new stdClass();
      $is_staff = STAFF::processLogin($cashier, $password);
      if ($is_staff) {
          $sales_invoice_id = SalesInvoice::UpdateOrder();
          if ($sales_invoice_id) {
              $result->status = "success";
              $result->sales_invoice_id = $sales_invoice_id;
              return json_encode($result);
          }
      }

      $result->status = "fail";
      return json_encode($result);
    }
	
	public function updateSIVoidStatus()
	{
        $result = SalesInvoice::updateSIVoidStatus();
        return $result;	
	}	
}
