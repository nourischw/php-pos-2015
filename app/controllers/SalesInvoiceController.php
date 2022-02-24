<?php

class SalesInvoiceController extends BaseController
{
    /**
     * 	@var string $layout Define the layout template
     */
    protected $layout = 'layouts.normal';

    private $is_allow_create;
    private $is_allow_update;
    private $is_allow_delete;
    private $is_allow_print;
    private $is_allow_confirm;
    private $is_allow_void;
    private $is_allow_unvoid;
    private $is_allow_finish;

    public function __construct()
    {
        parent::__construct();
        $this->checkAllowAccess("SALES_INVOICE_ACCESS");
        $this->is_allow_create = $this->checkPermission("SALES_INVOICE_CREATE");
        $this->is_allow_update = $this->checkPermission("SALES_INVOICE_UPDATE");
        $this->is_allow_delete = $this->checkPermission("SALES_INVOICE_DELETE");
        $this->is_allow_print = $this->checkPermission("SALES_INVOICE_PRINT");
        $this->is_allow_confirm = $this->checkPermission("SALES_INVOICE_CONFIRM");
        $this->is_allow_void = $this->checkPermission("SALES_INVOICE_VOID");
        $this->is_allow_unvoid = $this->checkPermission("SALES_INVOICE_UNVOID");
    }

    /**
     * 	Show index page
     * 	@used-by (view) index.blade.php
     * 	@return void
     */
    public function showListView($page = null)
    {
        Session::put("page", "sales_invoice_list");
        $status = Session::get('sales_list_status');
        if (empty($status)){
            Session::put('sales_list_status', '2');
        }
        $sales_invoice_list = SalesInvoice::getList();
        extract($sales_invoice_list);
        $have_record = ($total_records > 0) ? true : false;
        $data = array(
            'list_data' => $list_data,
            'shop_list' => Shop::getSelectList(),
            'total_records' => $total_records,
            'total_pages' => $total_pages,
            'page' => $page,
            'end_page' => $end_page,
            'have_record' => ($total_records > 0) ? true : false,

            'is_allow_create' => $this->is_allow_create,
            'is_allow_update' => $this->is_allow_update,
            'is_allow_delete' => $this->is_allow_delete,
            'is_allow_print' => $this->is_allow_print,
            'is_allow_confirm' => $this->is_allow_confirm,
            'is_allow_void' => $this->is_allow_void,
            'is_allow_unvoid' => $this->is_allow_unvoid,
            'is_show_checkbox' => $this->is_allow_delete
        );
        $this->layout
			 ->with('title', "Sales Invoice List")
			 ->with('css', Config::get('css.CSS_LIST'))
			 ->with('page_css', 'sales_invoice_list')
			 ->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('js.JS_LIST'))
			 ->with('page_js', 'sales_invoice_list')
             ->content = View::make('pages.sales_invoice_list', $data);
    }

    public function showEditView($id = null)
    {
        $record_id = (!empty($id)) ? intval($id) : 0;
        if ($record_id === 0) {
            if (!$this->is_allow_create) {
                return Redirect::route('forbidden');
            }
        } else {
            if (!$this->is_allow_update) {
                return Redirect::route('forbidden');
            }
        }
        Session::forget('record_id');

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
                'sales_invoice_edit_list' => $sales_invoice_list,

                'is_allow_create' => $this->is_allow_create,
                'is_allow_update' => $this->is_allow_update,
                'is_allow_delete' => $this->is_allow_delete,
                'is_allow_print' => $this->is_allow_print,
                'is_allow_void' => $this->is_allow_void,
                'is_allow_unvoid' => $this->is_allow_unvoid,
                'is_allow_confirm' => $this->is_allow_confirm,
			);

			$this->layout
				 ->with('title', "Edit Sales Invoice")
				 ->with('css', Config::get('css.CSS_LIST'))
				 ->with('page_css', 'sales_invoice_edit')
				 ->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('js.JS_LIST'))
				 ->with('page_js', 'sales_invoice_edit')
				 ->content = View::make('pages.sales_invoice_edit', $data);
			}
    }

    public function showDetailsView($id = null)
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

            'is_allow_create' => $this->is_allow_create,
            'is_allow_print' => $this->is_allow_print,
            'is_allow_void' => $this->is_allow_void,
            'is_allow_unvoid' => $this->is_allow_unvoid
		);

		$this->layout
		   ->with('title', "View Sales Invoice Details")
		   ->with('css', Config::get('css.CSS_LIST'))
		   ->with('page_css', 'sales_invoice_details')
		   ->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('js.JS_LIST'))
		   ->with('page_js', 'sales_invoice_details')
		   ->content = View::make('pages.sales_invoice_details', $data);
    }

    public function search()
    {
        $sales_invoice_list = SalesInvoice::getList();
        $data = array(
            'sales_invoice_list' => $sales_invoice_list,
            'have_records' => (!empty($sales_invoice_list['list_data'])) ? true : false,

            'is_allow_update' => $this->is_allow_update,
            'is_allow_delete' => $this->is_allow_delete,
            'is_allow_print' => $this->is_allow_print,
            'is_allow_confirm' => $this->is_allow_confirm,
            'is_allow_void' => $this->is_allow_void,
            'is_allow_unvoid' => $this->is_allow_unvoid
        );

        return View::make('list_rows.sales_invoice_list', $data);
    }

    public function order($id = null)
    {
        if (!$this->is_allow_create) {
            return Redirect::route('forbidden');
        }
        Session::forget('record_id');
        $record_id = (!empty($id)) ? intval($id) : 0;
        if ($record_id > 0) {
            Session::put('record_id', $record_id);
            $stock_transfer_data = StockTransfer::getStockTransfer($record_id);
            if (empty($stock_transfer_data)) {
                Redirect::route("stock_transfer_list");
            }
            $stock_transfer_items_data = StockTransferItems::getStockTransferItems($record_id);
            extract($stock_transfer_data, EXTR_PREFIX_ALL, 'st');
            $total_items = sizeof($stock_transfer_items_data);
            $total_qty = $st_total_qty;
        }

        $data = array(
            'record_id' => $record_id,
            'shop_list' => Shop::getSelectList(),
			'payment_term_list' => Config::get('cod_terms'),
            'payment_type_list' => Config::get('payment_type'),
            'is_update' => ($record_id > 0) ? true : false,

            'is_allow_create' => $this->is_allow_create,
            'is_allow_confirm' => $this->is_allow_confirm,
		);

        $this->layout
			 ->with('title', "Sales Invoice")
			 ->with('css', Config::get('css.CSS_LIST'))
			 ->with('page_css', 'sales_invoice')
			 ->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('js.JS_LIST'))
			 ->with('page_js', 'sales_invoice')
			 ->content = View::make('pages.sales_invoice', $data);
    }

    public function create()
    {
        $result = new stdClass();
        $result->status = "fail";
        if (!$this->is_allow_create) {
            return json_encode($result);
        }

        $cashier = Input::get("cashier_id");
        $password = Input::get("cashier_password");
        $is_staff = Staff::SalesInvoiceStaffProcess($cashier, $password);
        if ($is_staff) {
            $sales_invoice_id = SalesInvoice::CreateOrder();
            if ($sales_invoice_id) {
                $info = SalesInvoice::getSalesInvoiceInfo($sales_invoice_id);
                $items = SalesInvoiceItems::getSalesQty($sales_invoice_id);

                $log_params = array();
                $i = 0;
                foreach($items as $value) {
                    $log_params[$i] = [
                        "type" => StockTransportLog::STOCK_TRANSPORT_TYPE_SALES_INVOICE,
                        "stock_id" => $value["stock_id"],
                        "desc" => "[Sales Invoice No.: " . $info["sales_invoice_number"] . "], sale [" . $value["qty"] . " qty] from [Shop Code: " . $info["shop_code"] . "] by [Staff: " . Session::get("staff_code") . "]"
                    ];
                    $i++;
                }
                StockTransportLog::createRecord($log_params);

                $result->status = "success";
                $result->sales_invoice_id = $sales_invoice_id;
                return json_encode($result);
            }
        }

        return json_encode($result);
    }

    public function update()
    {
        $result = new stdClass();
        $result->status = "fail";
        if (!$this->is_allow_update || !Input::has("sales_invoice_edit_id")) {
            return json_encode($result);
        }

        $cashier = Input::get("cashier_id");
        $password = Input::get("cashier_password");
        $is_staff = Staff::SalesInvoiceStaffProcess($cashier, $password);
        if ($is_staff) {
            $sales_invoice_id = SalesInvoice::UpdateOrder();
            if ($sales_invoice_id) {
                $info = SalesInvoice::getSalesInvoiceInfo($sales_invoice_id);
                $items = SalesInvoiceItems::getSalesQty($sales_invoice_id);

                $log_params = array();
                $i = 0;
                foreach($items as $value) {
                    $log_params[$i] = [
                        "type" => StockTransportLog::STOCK_TRANSPORT_TYPE_SALES_INVOICE,
                        "stock_id" => $value["stock_id"],
                        "desc" => "[Sales Invoice No.: " . $info["sales_invoice_number"] . "], update sale invoice [" . $value["qty"] . " qty] from [Shop Code: " . $info["shop_code"] . "] by [Staff: " . Session::get("staff_code") . "]"
                    ];
                    $i++;
                }
                StockTransportLog::createRecord($log_params);

                $result->status = "success";
                $result->sales_invoice_id = $sales_invoice_id;
				Session::put('sales_list_status', '1');
                return json_encode($result);
            }
        }

        return json_encode($result);
    }

    public function printPDF($id = null)
    {
        if (!$this->is_allow_print || $id == null || intval($id) == 0) {
            return Redirect::route("sales_invoice_list");
        }

        $sales_invoice_id = intval($id);
        if ($sales_invoice_id) {
            $invoice = SalesInvoice::GetSalesInvoice($sales_invoice_id);
            $invoice[0]["product_spec"] = nl2br($invoice[0]["product_spec"]);

            extract($invoice[0]);
            $data["invoice"] = $invoice;
            $data["shop_address"] = $shop_address;
            $data["shop_tel"] = $shop_tel;
            $data["shop_fax"] = $shop_fax;
            $data["shop_name"] = $shop_name;
            $data["sales_invoice_number"] = $sales_invoice_number;
            $data["create_time"] = $create_time;
            $data["cashier"] = $cashier;
            $data["sales"] = $sales;
            $data["total_amount"] = $total_amount;
            $data["deposit_payment_amount"] = $deposit_payment_amount;
            $data["net_total_amount"] = $net_total_amount;
            $data["discount"] = $invoice_discount;
            $data["discount_type"] = $discount_type;
            $data["item_discount"] = $item_discount;
            if ($discount_type == 1) {
                $data["discount_text"] = "- $".$data["discount"];
            } elseif ($discount_type == 2) {
                $data["discount_text"] = "-".$data["discount"]."%";
            }else{
                $data["discount_text"] = '';
            }
            self::outputPDF($data);
        }
    }

    private static function outputPDF($params)
    {
        include 'app/classes/Tcpdf.php';

        $pdf = new SOPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set header
        $header_params = array(
            'shop_address' => $params['shop_address'],
            'shop_tel' => $params['shop_tel'],
            'shop_fax' => $params['shop_fax'],
            'shop_company' => $params['shop_name'],
            'sales_invoice_number' => $params['sales_invoice_number'],
            'create_time' => $params['create_time'],
            'sales' => $params['sales'],
            'order_discount_text' => $params['discount_text'],
            'cashier' => $params['cashier']
        );
        $header = View::make('outputs.sales_invoice.header', array('params' => $header_params));
        $pdf->setHTMLHeader($header);

        // Set footer
        $footer_params = array(
            'discount' => $params['discount_text'],
            'total_amount' => $params['total_amount'],
            'deposit_payment_amount' => $params['deposit_payment_amount'],
            'net_total_amount' => $params['net_total_amount']
        );

        $footer = View::make('outputs.sales_invoice.footer', array('params' => $footer_params));
        $pdf->setHTMLFooter($footer);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Vlab POS');
        $pdf->SetTitle('Vlab POS');
        $pdf->SetSubject('Vlab POS');
        $pdf->SetKeywords('Vlab POS');

        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(10, 62, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(26);
        $pdf->SetFooterMargin(55);
        $pdf->SetAutoPageBreak(TRUE, 55);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetFont('msungstdlight', '', 8);
        $pdf->AddPage('L', 'A4');

        $html = View::make('outputs.sales_invoice.content', array('params' => $params));
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->setPage(1, false);

        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => true,
            'padding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => array(255, 255, 255),
            'text' => true,
            'font' => 'msungstdlight',
            'fontsize' => 8,
            'stretchtext' => 4
        );
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 128);
        $pdf->SetFont('times', 'BI', 16);
        $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 4, 'color' => array(255, 255, 255)));
        $pdf->lastPage();

        $pdf->Output($params["sales_invoice_number"].'.pdf', 'I');

		return null;
    }

	public function setStatus()
	{
		$status = Input::get("status");
		Session::put('sales_list_status', $status);
		return $status;
	}

    public function updateSIStatus()
    {
        extract(Input::all());
        $status = intval($status);
        $valid_status = array(1, 3, 4);

        if (empty($record_id) || !in_array($status, $valid_status)) {
            return 0;
        }

        switch($status) {
        case 1:
            if (!$this->is_allow_confirm) {
                return 0;
            }
            break;

        case 3:
            if (!$this->is_allow_void) {
                return 0;
            }
            break;

        case 4:
            if (!$this->is_allow_finish) {
                return 0;
            }
            break;

        default:
            break;
        }

        $result = SalesInvoice::updateSIStatus();
        return $result;
    }

	public function getQuickItem(){
		$data = Stock::getQuickSearchItem();
		return json_encode($data);
	}

}
