<?php

class GoodsInController extends BaseController
{
    protected $layout = 'layouts.normal';

    private $is_allow_create;
    private $is_allow_print;
    private $is_allow_confirm;
    private $is_allow_finish;

    public function __construct()
    {
        parent::__construct();
        $this->checkAllowAccess("GOODS_IN_ACCESS");
        $this->is_allow_create = $this->checkPermission("GOODS_IN_CREATE");
        $this->is_allow_print = $this->checkPermission("GOODS_IN_PRINT");
        $this->is_allow_confirm = $this->checkPermission("GOODS_IN_CONFIRM");
    }

	public function showListView()
	{
        Session::put("page", "goods_in_list");
        $goods_in_list = GoodsIn::getList();
        extract($goods_in_list);
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
            'have_record' => $have_record,

            'is_allow_create' => $this->is_allow_create,
            'is_allow_print' => $this->is_allow_print,
            'is_allow_confirm' => $this->is_allow_confirm
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
			 ->with('title', "Goods In List")
			 ->with('css', Config::get('css.CSS_LIST'))
			 ->with('page_css', 'goods_in_list')
			 ->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('js.JS_LIST'))
			 ->with('page_js', 'goods_in_list')
             ->content = View::make('pages.goods_in_list', $data);
	}

	public function showDetailsView($id = null)
	{
        if (empty($id)) {
            return Redirect::route("goods_in");
        }

        $record_id = intval($id);
        Session::put('page', 'goods_in');
        Session::put('record_id', $record_id);
        $goods_in_data = GoodsIn::getGoodsInDetails($record_id);
        $goods_in_items_data = GoodsIn::getGoodsInItemsDetails($record_id);
        extract($goods_in_data, EXTR_PREFIX_ALL, 'gi');
        $status_text = null;
		$status_text = "Finished";
        $goods_in_data['status_text'] = $status_text;

        $data = array(
            'staff_list' => Staff::getSelectList(Session::get('shop_id')),
            'goods_in_data' => $goods_in_data,
            'goods_in_items_data' => $goods_in_items_data,
            'total_items' => $gi_total_items,
            'total_qty' => $gi_total_qty,
            'record_id' => $record_id,

            'is_allow_print' => $this->is_allow_print
        );

        $css = array(
			Config::get('css.CSS_LIST') |
			Config::get('css.CSS_FILE')
		);
		$js = array(
			Config::get('js.JS_FILE')
		);
        $this->layout
			 ->with('title', "Goods In Detail")
			 ->with('css', Config::get('css.CSS_LIST'))
			 ->with('page_css', 'goods_in_details')
			 ->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('js.JS_LIST'))
			 ->with('page_js', 'goods_in_details')
             ->content = View::make('pages.goods_in_details', $data);
	}

	public function printPDF($id = null)
	{
        if (!$this->is_allow_print || $id == null || intval($id) == 0) {
            return Redirect::route("goods_in");
        }
		return self::outputPDF($id);
	}

    public function getInfo()
    {
        $product_upc = Input::get("product_upc");
        $params = array(
            'product_upc' => $product_upc
        );
		$data = Product::getInfo($params);
		if (empty($data)) {
			return "";
		}

        return $data;
    }


	public function outputPDF($id)
	{
        include 'app/classes/Tcpdf.php';
        $pdf = new SOPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$gi_id = intval($id);
        $goods_in_data = GoodsIn::getGoodsInRecord($gi_id);
        $header = View::make('outputs.goods_in_report.header', array('params' => $goods_in_data));
        $pdf->setHTMLHeader($header);
        $footer_params = [
			"update_time" => $goods_in_data["update_time"],
			"update_by" => $goods_in_data["update_by"]
		];
        $footer = View::make('outputs.goods_in_report.footer', array('params' => $footer_params));
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
        $pdf->SetMargins(10, 60, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(20);
        $pdf->SetAutoPageBreak(TRUE, 25);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetFont('msungstdlight', '', 12);
        $pdf->AddPage('P', 'A4');

		$item_data = GoodsIn::getGoodsInItems($gi_id);

        $html = View::make('outputs.goods_in_report.content', array('params' => $item_data));
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
            'fontsize' => 12,
            'stretchtext' => 4
        );

        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 128);
        $pdf->SetFont('times', 'BI', 16);
        $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 4, 'color' => array(255, 255, 255)));
        $pdf->lastPage();

        // $pdf->Output($params["sales_order_number"].'.pdf', 'I');
        $pdf->Output('goods_test.pdf', 'i');

		return null;
	}

    public function search()
    {
		$goods_in_list = GoodsIn::getList();
		$data = array(
			'goods_in_list' => $goods_in_list,
            'have_records' => (!empty($goods_in_list['list_data'])) ? true : false,

            'is_allow_print' => $this->is_allow_print,
            'is_allow_confirm' => $this->is_allow_confirm,
            'is_allow_finish' => $this->is_allow_finish,
		);
        return View::make('list_rows.goods_in_list', $data);
    }

	public function create()
	{
		$result = ["status" => "failed"];

        if (!$this->is_allow_create) {
            return json_encode($result);
        }
		extract(Input::all());
		$po_id = "";
		if($po_number != null){
			$po_id = PurchaseOrder::getPOID($po_number);
		}
		$supplier_data = Supplier::getPOSupplier($supplier);
		extract($supplier_data);

		$gi_params = [
			"supplier_id" 	=> $supplier_id,
			"invoice_no" 	=> $invoice_no,
			"invoice_date" 	=> $invoice_date,
            "po_id" => $po_id,
			"po_number" 	=> $po_number,
			"consignment"	=> $consignment,
			"remarks" 		=> $remarks,
			"goods_in_to" 	=> $goods_in_to
		];
		$gi_id = GoodsIn::createGoodsInRecord($gi_params);
		if ($gi_id !== 0) {
			$gii_params = [
				"po_id" => $po_id,
				"gi_id" => $gi_id,
				"gi_items" => $gi_items,
				"supplier_id" => $supplier_id,
				"supplier_code" => $supplier_code,
				"invoice_number" => $invoice_no	,
				"goods_in_to" => $goods_in_to
			];
			$insert_result = GoodsIn::InsertGoodsInItems($gii_params);
			if ($insert_result) {
				$result = [
					"status" => "success",
					"purchase_order_id" => $po_id,
					"goods_in_id" => $gi_id
				];
			}
		}
		return json_encode($result);
	}

    public function order()
    {
        if (!$this->is_allow_create) {
            return Redirect::route('forbidden');
        }
		$po_payment_type = 6;
		$data = array (
			'shop_list' 		=> Shop::getSelectList(),
            'staff_list'		=> Staff::getSelectList(Session::get('shop_id')),
            'payment_type' 		=> Config::get('payment_type'),
			'po_payment_type'	=> $po_payment_type
            // 'payment_type' => Config::get('payment.PAYMENT_TYPE')
		);
        $js = array('goods_in');
        $this->layout
			 ->with('title', "Goods In")
			 ->with('css', Config::get('css.CSS_LIST'))
			 ->with('page_css', 'goods_in')
			 ->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('js.JS_LIST'))
			 ->with('page_js', 'goods_in')
             ->content = View::make('pages.goods_in', $data);
    }

	public function getPurchaseOrderItem()
	{
		$po_id = Input::get("po_id");

		$data = array(
			'po_item_list' => PurchaseOrderItems::getGoodsInPurchaseOrderItems($po_id),
		);
		extract($data);

		return array(
			'po_item_list' => $po_item_list
		);
	}

	public function getProductGoodsIn()
	{
		$data = Product::searchGoodsInProduct();
		return $data;
	}

	public function getPurchaseOrderGoodsIn()
	{
		$data = PurchaseOrder::searchGoodsInPurchaseOrder();
		return $data;
	}

	public function getSupplierGoodsIn()
	{
		$data = Supplier::searchGoodsInSupplier();
		return $data;
	}

}
