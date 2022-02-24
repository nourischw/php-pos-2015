<?php

class PurchaseOrderController extends BaseController
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
        $this->checkAllowAccess("PURCHASE_ORDER_ACCESS");
        $this->is_allow_create = $this->checkPermission("PURCHASE_ORDER_CREATE");
        $this->is_allow_update = $this->checkPermission("PURCHASE_ORDER_UPDATE");
        $this->is_allow_delete = $this->checkPermission("PURCHASE_ORDER_DELETE");
        $this->is_allow_print = $this->checkPermission("PURCHASE_ORDER_PRINT");
        $this->is_allow_confirm = $this->checkPermission("PURCHASE_ORDER_CONFIRM");
        $this->is_allow_void = $this->checkPermission("PURCHASE_ORDER_VOID");
        $this->is_allow_unvoid = $this->checkPermission("PURCHASE_ORDER_UNVOID");
        $this->is_allow_finish = $this->checkPermission("PURCHASE_ORDER_FINISH");
    }

    /**
     * 	Show list page
     * 	@return void
     */
    public function showListView()
    {
        Session::put("page", "purchase_order_list");
        $list = PurchaseOrder::getList();
        extract($list);

        $data = array(
            'list_data' => $list_data,
            'shop_list' => Shop::getSelectList(),
            'total_records' => $total_records,
            'total_pages' => $total_pages,
            'page' => $page,
            'end_page' => min(10, $total_pages),
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
            ->with('title', "Purchase Order List")
            ->with('css', Config::get('css.CSS_LIST'))
            ->with('page_css', 'purchase_order_list')
            ->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('js.JS_LIST'))
            ->with('page_js', 'purchase_order_list')
            ->content = View::make('pages.purchase_order_list', $data);
    }
	
    /**
     *  Show edit page
     *  @param integer $id Record ID
     *  @return void
     */
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

        Session::put('page', 'purchase_order_edit');
        $purchase_order_data = null;
        $purchase_order_items_data = null;

        // Set default form value
        $po_purchase_order_number = null;
        $po_order_date = date("Y-m-d");
        $po_staff_code = Session::get('staff_code');
        $po_deposit_no = null;
        $po_ship_to = 11;
        $po_request_by = Session::get('staff_code');
        $po_payment_type = 6;
        $po_remarks = null;
        $po_supplier_id = null;
        $po_supplier_code = null;
        $po_supplier_name = null;
        $po_supplier_mobile = null;
        $po_supplier_fax = null;
        $po_supplier_email = null;
        $po_total_amount = 0.00;
        $po_discount_amount = 0.00;
        $po_net_amount = 0.00;
        $po_total_items = 0;
        $po_total_qty = 0;        

        if ($record_id > 0) {
            $purchase_order_data = PurchaseOrder::get($record_id);
            if (empty($purchase_order_data)) {
                Redirect::route("purchase_order_list");
            }
            $purchase_order_items_data = PurchaseOrderItems::get($record_id);
            extract($purchase_order_data, EXTR_PREFIX_ALL, 'po');
        }

        $data = array(
            'purchase_order_items_data' => $purchase_order_items_data,
            'staff_list' => Staff::getSelectList(),
            'shop_list' => Shop::getSelectList(),
            'payment_type_list' => Config::get('payment_type'),
            'record_id' => $record_id,
            'have_list_items' => !empty($purchase_order_items_data),

            'po_purchase_order_number' => $po_purchase_order_number,
            'po_order_date' => $po_order_date,
            'po_staff_code' => $po_staff_code,
            'po_deposit_no' => $po_deposit_no,
            'po_ship_to' => $po_ship_to,
            'po_request_by' => $po_request_by,
            'po_payment_type' => $po_payment_type,
            'po_remarks' => $po_remarks,
            'po_supplier_code' => $po_supplier_code,
            'po_supplier_name' => $po_supplier_name,
            'po_supplier_mobile' => $po_supplier_mobile,
            'po_supplier_fax' => $po_supplier_fax,
            'po_supplier_email' => $po_supplier_email,
            'po_supplier_id' => $po_supplier_id,
            'po_total_amount' => $po_total_amount,
            'po_discount_amount' => $po_discount_amount,
            'po_net_amount' => $po_net_amount,
            'po_total_items' => $po_total_items,
            'po_total_qty' => $po_total_qty,

            'is_allow_create' => $this->is_allow_create,
            'is_allow_update' => $this->is_allow_update,
            'is_allow_delete' => $this->is_allow_delete,
            'is_allow_print' => $this->is_allow_print,
            'is_allow_confirm' => $this->is_allow_confirm,
            'is_allow_finish' => $this->is_allow_finish
        );

        $this->layout
            ->with('title', "Edit Purchase Order")
            ->with('css', Config::get('css.CSS_LIST'))
            ->with('page_css', 'purchase_order_edit')
            ->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('js.JS_LIST'))
            ->with('page_js', 'purchase_order_edit')
            ->content = View::make('pages.purchase_order_edit', $data);
    }

    /**
     *  Show details page
     *  @return void
     */
    public function showDetailsView($id = null)
    {
        if (empty($id)) {
            return Redirect::route("purchase_order_list");
        }
        Session::put('page', 'purchase_order');

        $record_id = intval($id);
        $purchase_order_data = PurchaseOrder::get($record_id);
        if (empty($purchase_order_data)) {
            return Redirect::route("purchase_order_list");
        }

        $purchase_order_items_data = PurchaseOrderItems::get($record_id);
        extract($purchase_order_data, EXTR_PREFIX_ALL, 'po');
        $payment_type_list = Config::get("payment_type");
        $payment_type_text = $payment_type_list[$po_payment_type];
        $purchase_order_data['payment_type_text'] = $payment_type_text;

        $status_text = null;
        switch($po_status) {
        case 1:
            $status_text = "Confirmed";
            break;
        case 3:
            $status_text = "Voided";
            break;
        case 4:
            $status_text = "Finished";
            break;
        }
        $purchase_order_data['status_text'] = $status_text;
        
        $data = array(
            'staff_list' => Staff::getSelectList(Session::get('shop_id')),
            'purchase_order_data' => $purchase_order_data,
            'purchase_order_items_data' => $purchase_order_items_data,
            'total_items' => $po_total_items,
            'total_qty' => $po_total_qty,
            'record_id' => $record_id,

            'is_allow_create' => $this->is_allow_create,
            'is_allow_print' => $this->is_allow_print,
            'is_allow_void' => $this->is_allow_void,
            'is_allow_unvoid' => $this->is_allow_unvoid,
            'is_allow_finish' => $this->is_allow_finish
        );

        $this->layout
            ->with('title', "View Purchase Order")
            ->with('css', Config::get('css.CSS_LIST'))
            ->with('page_css', 'purchase_order_details')
            ->with('page_js', 'purchase_order_details')
            ->content = View::make('pages.purchase_order_details', $data);
    }

    /**
     *  Show popup list page
     *  @return void
     */
    public function getPopupList()
    {
        $list = PurchaseOrder::getPopupList();
        if (empty($list)) {
            return null;
        }
        return View::make('popups.list_rows.purchase_order_list', $list);
    }

    /**
     *  Search record
     *  @return void
     */
    public function search()
    {
        $purchase_order_list = PurchaseOrder::getList();
        $data = array(
            'purchase_order_list' => $purchase_order_list,
            'have_records' => (!empty($purchase_order_list['list_data'])) ? true : false,

            'is_allow_update' => $this->is_allow_update,
            'is_allow_delete' => $this->is_allow_delete,
            'is_allow_print' => $this->is_allow_print,
            'is_allow_confirm' => $this->is_allow_confirm,
            'is_allow_void' => $this->is_allow_void,
            'is_allow_unvoid' => $this->is_allow_unvoid
        );

        return View::make('list_rows.purchase_order_list', $data);
    }

    /**
     *  Create record
     *  @return mixed JSON result
     */
    public function create()
    {
        $result = new stdClass();
        $result->status = "failure";
        if (!$this->is_allow_create) {
            return json_encode($result);
        }

        $create_result = PurchaseOrder::edit(Config::get('edit_type.CREATE'));
        
        if ($create_result) {
            extract($create_result);
            $result->status = "success";
            $result->purchase_order_id = $new_id;
            $result->purchase_order_number = $purchase_order_number;
            $result->purchase_order_item_id = $purchase_order_item_id;
        }
        return json_encode($result);
    }

    /**
     *  Update record
     *  @return mixed JSON result
     */
    public function update()
    {
        $result = new stdClass();
        $result->status = "failure";
        if (!$this->is_allow_update) {
            return json_encode($result);
        }

        if (!Input::has("record_id")) {
            return json_encode($result);
        }

        $update_result = PurchaseOrder::edit(Config::get('edit_type.UPDATE'));
        if ($update_result["success"]) {
            $result->status = "success";
            $result->new_item_ids = $update_result["new_item_ids"];
        }

        return json_encode($result);
    }

    /**
     *  Delete record
     *  @return integer Success result
     */
    public function remove()
    {
        if (!$this->is_allow_delete ||
            (Session::get("page") !== "purchase_order_list" &&
            Session::get("page") !== "purchase_order_edit") || 
            empty(Input::get("record_id"))) {
            return 0;
        }

        $record_ids = explode(",", Input::get("record_id"));
        foreach ($record_ids as $id) {
            $all_po_ids[] = intval($id);
        }

        $result = PurchaseOrder::deleteRecord($all_po_ids);
        if ($result) {
            PurchaseOrderItems::deleteRelatedRecords($all_po_ids);
            return 1;
        }

        return 0;
    }

    /**
     *  Update PO status
     *  @return integer Success result
     */
    public function updateStatus()
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

        return PurchaseOrder::updateStatus();
    }

    /**
     *  print record
     *  @param integer $id Record ID
     *  @return void
     */
    public function printPDF($id = null)
    {
        if (!$this->is_allow_print || $id == null || intval($id) == 0) {
            return Redirect::route("purchase_order_list");
        }

        $this->layout = null;
        $id = intval($id);
        $data = PurchaseOrder::get($id);
        if (empty($data)) {
            return Redirect::route("purchase_order_list");
        }

        $data['po_items'] = PurchaseOrderItems::get($id);
        self::outputPDF($data);
    }

    /**
     *  output PDF
     *  @param array $params PDF params
     *  @return null
     */
    private static function outputPDF($params)
    {
        include 'app/classes/Tcpdf.php';
        extract($params);
        $pdf = new POPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        $date = substr($create_time, 0, 10);
        $po_date = new DateTime($date);
        $po_date = $po_date->format("d-F-Y");

        $header_params = [
            'purchase_order_number' => $purchase_order_number,
            'shop_address' => $shop_address,
            'shop_telephone' => $shop_telephone,
            'shop_fax' => $shop_fax,            
            'supplier_code' => $supplier_code,
            'supplier_name' => $supplier_name,
            'supplier_mobile' => $supplier_mobile,
            'supplier_fax' => $supplier_fax,
            'supplier_email' => $supplier_email,
            'request_by' => $request_by,
            'remarks' => $remarks,
            'ship_to_shop' => $ship_to_shop,
            'po_date' => $po_date,
        ];
        $header = View::make('outputs.purchase_order.header', array('params' => $header_params));
        $pdf->setHTMLHeader($header);

        $payment_type_list = Config::get('payment_type');
        $payment_type_text = $payment_type_list[$payment_type];

        $footer_params = [
            'deliver_by' => $deliver_by,
            'payment_type' => $payment_type_text,
            'shop_code' => $shop_code
        ];
        $footer = View::make('outputs.purchase_order.footer', array('params' => $footer_params));
        $pdf->setHTMLFooter($footer);

        $last_page_footer_params = [
            'deliver_by' => $deliver_by,
            'payment_type' => $payment_type_text,
            'shop_code' => $shop_code,
            'total_qty' => $total_qty,
            'total_amount' => $total_amount,
            'discount_amount' => $discount_amount,
            'net_amount' => $net_amount
        ];

        $last_page_footer = View::make('outputs.purchase_order.last_page_footer', array('params' => $last_page_footer_params));
        $pdf->setHTMLLastPageFooter($last_page_footer);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Vlab POS');
        $pdf->SetTitle('Vlab POS');
        $pdf->SetSubject('Vlab POS');
        $pdf->SetKeywords('Vlab POS');

        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(12, 86, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(62);
        $pdf->SetAutoPageBreak(TRUE, 62);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetFont('msungstdlight', '', 12);
        $pdf->AddPage('P', 'A4');

        $html = View::make('outputs.purchase_order.content', array('po_items' => $po_items));
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->setPage(1, false);
        $pdf->lastPage();
        $pdf->Output($params["purchase_order_number"].'.pdf', 'I');
        return null;
    }
}
