<?php

class DepositController extends BaseController
{
    /**
     * 	@var string $layout Define the layout template
     */
    protected $layout = 'layouts.normal';

    private $is_allow_create;
    private $is_allow_update;
    private $is_allow_delete;
    private $is_allow_print;
    private $is_allow_void;
    private $is_allow_unvoid;

    public function __construct()
    {
        parent::__construct();
        $this->checkAllowAccess("DEPOSIT_ACCESS");
        $this->is_allow_create = $this->checkPermission("DEPOSIT_CREATE");
        $this->is_allow_update = $this->checkPermission("DEPOSIT_UPDATE");
        $this->is_allow_delete = $this->checkPermission("DEPOSIT_DELETE");
        $this->is_allow_print = $this->checkPermission("DEPOSIT_PRINT");
        $this->is_allow_void = $this->checkPermission("DEPOSIT_VOID");
        $this->is_allow_unvoid = $this->checkPermission("DEPOSIT_UNVOID");
    }

    /**
     *  Show list page
     *  @return void
     */
    public function showListView()
    {
        Session::put("page", "deposit_list");
        $list = Deposit::getList();
        extract($list);
        
        $data = array(
            'shop_list' => Shop::getSelectList(),
            'deposit_terms' => Config::get('cod_terms'),
            'payment_type' => Config::get('payment_type'),
            'list_data' => $list_data,
            'total_records' => $total_records,
            'total_pages' => $total_pages,
            'page' => $page,
            'end_page' => min(10, $total_pages),
            'have_record' => ($total_records > 0) ? true : false,

            'is_allow_create' => $this->is_allow_create,
            'is_allow_update' => $this->is_allow_update,
            'is_allow_delete' => $this->is_allow_delete,
            'is_allow_print' => $this->is_allow_print,
            'is_allow_void' => $this->is_allow_void,
            'is_allow_unvoid' => $this->is_allow_unvoid,
            'is_show_checkbox' => $this->is_allow_delete || $this->is_allow_void
        );

        $this->layout
            ->with('title', 'Deposit List')
            ->with('css', Config::get('css.CSS_LIST'))
            ->with('page_css', 'deposit_list')
            ->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('js.JS_LIST'))
            ->with('page_js', 'deposit_list')
            ->content = View::make('pages.deposit_list', $data);
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
                return Redirect::route('deposit_list');
            } 
        }

        Session::put('page', 'deposit_edit');
        $deposit_data = null;
        $deposit_items_data = null;

        // Set default form value
        $d_deposit_number = null;
        $d_quotation_number = null;
        $d_deposit_date = date("Y-m-d");
        $d_shop_code = Session::get("shop_code");
        $d_staff_id = Session::get('staff_id');
        $d_payment_type = 5;
        $d_deposit_terms = null;
        $d_cheque_number = null;
        $d_cheque_date = date("Y-m-d");
        $d_status = 0;
        $d_remarks = null;
        $d_total_items = 0;
        $d_total_qty = 0;  
        $d_total_amount = 0.00;
        $d_payment_amount = 0.00;
        $d_sub_total_amount = 0.00;
   
        if ($record_id > 0) {
            $deposit_data = Deposit::get($record_id);
            if (empty($deposit_data)) {
                Redirect::route("deposit_list");
            }
            $deposit_items_data = DepositItems::get($record_id);
            extract($deposit_data, EXTR_PREFIX_ALL, 'd');
        }

        $data = array(
            'deposit_items_data' => $deposit_items_data,
            'shop_list' => Shop::getSelectList(),
            'staff_list' => Staff::getSelectList(),
            'deposit_terms_list' => Config::get('cod_terms'),
            'payment_type_list' => Config::get('payment_type'),
            'record_id' => $record_id,
            'have_list_item' => (!empty($deposit_items_data)) ? true : false,

            'd_deposit_number' => $d_deposit_number,
            'd_quotation_number' => $d_quotation_number,
            'd_deposit_date' => $d_deposit_date,
            'd_shop_code' => $d_shop_code,
            'd_staff_id' => $d_staff_id,
            'd_deposit_terms' => $d_deposit_terms,
            'd_payment_type' => $d_payment_type,
            'd_cheque_number' => $d_cheque_number,
            'd_cheque_date' => $d_cheque_date,
            'd_status' => $d_status,
            'd_remarks' => $d_remarks,
            'd_total_items' => $d_total_items,
            'd_total_qty' => $d_total_qty,            
            'd_total_amount' => $d_total_amount,
            'd_payment_amount' => $d_payment_amount,
            'd_sub_total_amount' => $d_sub_total_amount,

            'is_allow_create' => $this->is_allow_create,
            'is_allow_update' => $this->is_allow_update,
            'is_allow_delete' => $this->is_allow_delete,
            'is_allow_print' => $this->is_allow_print,
            'is_allow_void' => $this->is_allow_void,
            'is_allow_unvoid' => $this->is_allow_unvoid
        );

        $this->layout
            ->with('title', 'Edit Deposit')
            ->with('css', Config::get('css.CSS_LIST'))
            ->with('page_css', 'deposit_edit')
            ->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('js.JS_LIST'))
            ->with('page_js', 'deposit_edit')
            ->content = View::make('pages.deposit_edit', $data);
    }

    /**
     *  Show detail page
     *  @param integer $id Record ID
     *  @return void
     */
    public function showDetailsView($id = null)
    {
        if (empty($id)) {
            return Redirect::route("deposit_list");
        }
        Session::put('page', 'deposit_details');

        $record_id = (!empty($id)) ? intval($id) : 0;
        $deposit_data = Deposit::get($record_id);
        if (empty($deposit_data)) {
            return Redirect::route("deposit_list");
        }

        extract($deposit_data, EXTR_PREFIX_ALL, 'd');

        $status_text = null;
        switch($d_status) {
        case 0:
            $status_text = "Normal";
            break;
        case 1:
            $status_text = "Voided";
            break;
        }
        $deposit_data['status_text'] = $status_text;

        $data = array(
            'deposit_terms' => Config::get('cod_terms'),
            'payment_type' => Config::get('payment_type'),
            'deposit_data' => $deposit_data,
            'deposit_items_data' => DepositItems::get($record_id),
            'total_items' => $d_total_items,
            'total_qty' => $d_total_qty,
            'record_id' => $record_id,

            'is_allow_create' => $this->is_allow_create,
            'is_allow_update' => $this->is_allow_update,
            'is_allow_delete' => $this->is_allow_delete,
            'is_allow_print' => $this->is_allow_print,
            'is_allow_void' => $this->is_allow_void,
            'is_allow_unvoid' => $this->is_allow_unvoid
        );

        $this->layout
            ->with('title', "View Deposit")
            ->with('css', Config::get('css.CSS_LIST'))
            ->with('page_css', 'deposit_details')
            ->with('page_js', 'deposit_details')
            ->content = View::make('pages.deposit_details', $data);
    }

    /**
     *  Show popup list page
     *  @return void
     */
    public function getPopupList()
    {
        $list = Deposit::getPopupList();
        if (empty($list)) {
            return null;
        }
        return View::make('popups.list_rows.deposit_list', $list);
    }

    /**
     *  Search record
     *  @return void
     */
    public function search()
    {
        $deposit_list = Deposit::getList();
        $data = array(
            'deposit_list' => $deposit_list,
            'have_records' => (!empty($deposit_list)) ? true : false,
            
            'is_allow_create' => $this->is_allow_create,
            'is_allow_update' => $this->is_allow_update,
            'is_allow_delete' => $this->is_allow_delete,
            'is_allow_print' => $this->is_allow_print,
            'is_allow_void' => $this->is_allow_void,
            'is_allow_unvoid' => $this->is_allow_unvoid
        );
        return View::make('list_rows.deposit_list', $data);
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
        $create_result = Deposit::edit(Config::get('edit_type.CREATE'));

        if ($create_result) {
            extract($create_result);
            $result->status = "success";
            $result->deposit_id = $new_id;
            $result->deposit_number = $deposit_number;
            $result->deposit_item_id = $deposit_item_id;
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
        if (!$this->is_allow_update || !Input::has("record_id")) {
            return json_encode($result);
        }

        $update_result = Deposit::edit(Config::get('edit_type.UPDATE'));
        if ($update_result["success"] === true) {
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
            (Session::get("page") !== "deposit_list" &&
            Session::get("page") !== "deposit_edit" &&
            Session::get("page") !== "deposit_details") ||
            empty(Input::get("record_id"))) {
            return 0;
        }

        $record_id = Input::get("record_id");
        $record_ids = explode(",", $record_id);
        foreach ($record_ids as $id) {
            $all_deposit_ids[] = intval($id);
        }

        $result = Deposit::deleteRecord($all_deposit_ids);
        if ($result) {
            DepositItems::deleteRelatedRecords($all_deposit_ids);
            return 1;
        }

        return 0;
    }

    /**
     *  Update Deposit status
     *  @return integer Success result
     */
    public function updateStatus()
    {
        extract(Input::all());
        $status = intval($status);
        if (empty($record_id) || 
            ($status !== 0 && $status !== 1) ||
            ($status === 0 && !$this->is_allow_unvoid) ||
            ($status === 1 && !$this->is_allow_void)) {
            return 0;
        }

        return Deposit::updateStatus();
    }
	
    public static function getItems()
    {
        $deposit_id = intval(Input::get("deposit_id"));
        $shop_code = Input::get("shop_code");
        return Deposit::getDepositItem($deposit_id, $shop_code);
    }	

    public function printPDF($id)
    {
        if (!$this->is_allow_print || ($id == null && intval($id) == 0)) {
            return Redirect::route("deposit_list");
        }

        $this->layout = null;
        $id = intval($id);
        $data = Deposit::get($id);
        if (empty($data)) {
            return Redirect::route("deposit_list");
        }

        $payment_type = Config::get("payment_type");
        $shop_data = Shop::get(array('code' => $data['shop_code']));
        $data['shop_address'] = $shop_data['address'];
        $data['shop_tel'] = $shop_data['telephone'];
        $data['shop_fax'] = $shop_data['fax'];
        $data['shop_name'] = $shop_data['name'];
        $data['sales'] = Staff::getStaffCode($data['staff_id']);
        $data['payment_type_text'] = $payment_type[$data['payment_type']];

        $data['deposit_items'] = DepositItems::get($id);
        self::outputPDF($data);
    }

    private static function outputPDF($params)
    {
        include 'app/classes/Tcpdf.php';
        $pdf = new DEPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set header
        $header_params = array(
            'shop_address' => $params['shop_address'],
            'shop_tel' => $params['shop_tel'],
            'shop_fax' => $params['shop_fax'],
            'shop_company' => $params['shop_name'],
            'deposit_number' => $params['deposit_number'],
            'create_time' => $params['create_time'],
            'sales' => $params['sales'],
            'order_discount_text' => '---',
            'cashier' => '---'
        );
        $header = View::make('outputs.deposit.header', array('params' => $header_params));
        $pdf->setHTMLHeader($header);

        // Set footer
        $footer_params = array(
            'payment_amount' => $params['payment_amount'],
            'total_amount' => $params['total_amount'],
            'sub_total_amount' => $params['sub_total_amount'],
            'payment_type_text' => $params['payment_type_text']
        );

        $footer = View::make('outputs.deposit.footer', array('params' => $footer_params));
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
        $pdf->SetMargins(10, 42, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(55);
        $pdf->SetAutoPageBreak(TRUE, 55);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetFont('msungstdlight', '', 8);
        $pdf->AddPage('L', 'A4');

        $html = View::make('outputs.deposit.content', array('params' => $params));
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

        $pdf->Output($params["deposit_number"].'.pdf', 'I');

        return null;
    }
}
