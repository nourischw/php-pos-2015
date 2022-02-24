<?php

class QuotationController extends BaseController
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
        $this->checkAllowAccess("QUOTATION_ACCESS");
        $this->is_allow_create = $this->checkPermission("QUOTATION_CREATE");
        $this->is_allow_update = $this->checkPermission("QUOTATION_UPDATE");
        $this->is_allow_delete = $this->checkPermission("QUOTATION_DELETE");
        $this->is_allow_print = $this->checkPermission("QUOTATION_PRINT");
        $this->is_allow_void = $this->checkPermission("QUOTATION_VOID");
        $this->is_allow_unvoid = $this->checkPermission("QUOTATION_UNVOID");
    }

    /**
     *  Show list page
     *  @return void
     */
    public function showListView()
    {
        Session::put("page", "quotation_list");
        $list = Quotation::getList();
        extract($list);

        $data = array(
            'quote_type' => Config::get('quote_type'),
            'quote_terms' => Config::get('cod_terms'),
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
            ->with('title', "Quotation List")
            ->with('css', Config::get('css.CSS_LIST'))
            ->with('page_css', 'quotation_list')
			->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('js.JS_LIST'))
            ->with('page_js', 'quotation_list')
            ->content = View::make('pages.quotation_list', $data);
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
                return Redirect::route('quotation_list');
            } 
        }

        Session::put('page', 'quotation_edit');
        $quotation_items_data = null;
        $staff_id = Session::get("staff_id");

        // Set default values
        $q_quotation_number = null;
        $q_quote_date = date("Y-m-d");
        $q_quote_type = null;
        $q_quote_terms = null;
        $q_staff_id = Session::get("staff_id");
        $q_status = 0;
        $q_total_amount = 0.00;
        $q_discount_amount = 0.00;
        $q_sub_total_amount = 0.00;
        $q_remarks = null;
        $q_comment = null;
        $q_total_items = 0;
        $q_total_qty = 0;        

        if ($record_id > 0) {
            $quotation_data = Quotation::get($record_id);
            if (empty($quotation_data)) {
                Redirect::route("quotation_list");
            }
            $quotation_items_data = QuotationItems::get($record_id);
            extract($quotation_data, EXTR_PREFIX_ALL, 'q');
        }

        $data = array(
            'quotation_items_data' => $quotation_items_data,
            'quote_type' => Config::get('quote_type'),
            'quote_terms' => Config::get('cod_terms'),
            'staff_list' => Staff::getSelectList(),
            'shop_list' => Shop::getSelectList(),
            'record_id' => $record_id,
            'have_list_item' => (!empty($quotation_items_data)) ? true : false,

            'q_quotation_number' => $q_quotation_number,
            'q_quote_date' => $q_quote_date,
            'q_quote_type' => $q_quote_type,
            'q_quote_terms' => $q_quote_terms,
            'q_staff_id' => $q_staff_id,
            'q_status' => $q_status,
            'q_total_items' => $q_total_items,
            'q_total_qty' => $q_total_qty,
            'q_remarks' => $q_remarks,
            'q_comment' => $q_comment,
            'q_status' => $q_status,
            'q_total_amount' => $q_total_amount,
            'q_discount_amount' => $q_discount_amount,
            'q_sub_total_amount' => $q_sub_total_amount,

            'is_allow_create' => $this->is_allow_create,
            'is_allow_update' => $this->is_allow_update,
            'is_allow_delete' => $this->is_allow_delete,
            'is_allow_print' => $this->is_allow_print,
            'is_allow_void' => $this->is_allow_void,
            'is_allow_unvoid' => $this->is_allow_unvoid
        );

        $this->layout
            ->with('title', "Edit Quotation")
            ->with('css', Config::get('css.CSS_LIST'))
            ->with('page_css', 'quotation_edit')
            ->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('js.JS_LIST'))
            ->with('page_js', 'quotation_edit')
            ->content = View::make('pages.quotation_edit', $data);
    }

    /**
     *  Show detail page
     *  @param integer $id Record ID
     *  @return void
     */
    public function showDetailsView($id = null)
    {
        if (empty($id)) {
            return Redirect::route("quotation_list");
        }
        Session::put('page', 'quotation_details');

        $record_id = intval($id);
        $quotation_data = Quotation::get($record_id);
        if (empty($quotation_data)) {
            return Redirect::route("quotation_list");
        }

        extract($quotation_data, EXTR_PREFIX_ALL, 'q');

        $status_text = null;
        switch($q_status) {
        case 0:
            $status_text = "Normal";
            break;
        case 1:
            $status_text = "Voided";
            break;
        }
        $quotation_data['status_text'] = $status_text;

        $data = array(
            'quotation_data' => $quotation_data,
            'quotation_items_data' => QuotationItems::get($record_id),
            'total_items' => $q_total_items,
            'total_qty' => $q_total_qty,
            'record_id' => $record_id,

            'is_allow_create' => $this->is_allow_create,
            'is_allow_update' => $this->is_allow_update,
            'is_allow_delete' => $this->is_allow_delete,
            'is_allow_print' => $this->is_allow_print,
            'is_allow_void' => $this->is_allow_void,
            'is_allow_unvoid' => $this->is_allow_unvoid
        );

        $this->layout
            ->with('title', "View Quotation")
            ->with('css', Config::get('css.CSS_LIST'))
            ->with('page_css', 'quotation_details')
            ->with('page_js', 'quotation_details')
            ->content = View::make('pages.quotation_details', $data);
    }

    /**
     *  Search record
     *  @return void
     */
    public function search()
    {
        $quotation_list = Quotation::getList();
        $data = array(
            'quotation_list' => $quotation_list,
            'have_records' => (!empty($quotation_list["list_data"])) ? true : false,

            'is_allow_update' => $this->is_allow_update,
            'is_allow_delete' => $this->is_allow_delete,
            'is_allow_print' => $this->is_allow_print,
            'is_allow_void' => $this->is_allow_void,
            'is_allow_unvoid' => $this->is_allow_unvoid
        );
        return View::make('list_rows.quotation_list', $data);
    }

    public function getPopupList()
    {
        $list = Quotation::getPopupList();
        if (empty($list)) {
            return null;
        }
        return View::make('popups.list_rows.quotation_list', $list);
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

        $create_result = Quotation::edit(Config::get('edit_type.CREATE'));
        
        if ($create_result['success']) {
            extract($create_result);
            $result->status = "success";
            $result->quotation_id = $new_id;
            $result->quotation_number = $quotation_number;
            $result->quotation_item_id = $quotation_item_id;
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

        $update_result = Quotation::edit(Config::get('edit_type.UPDATE'));
        if ($update_result["success"] === true) {
            $result->status = "success";
            $result->new_item_ids = $update_result["new_item_ids"];
        }

        return json_encode($result);
    }

    /**
     *  Delete record
     *  @return mixed JSON result
     */
    public function remove()
    {
        if (!$this->is_allow_delete ||
            (Session::get("page") !== "quotation_list" &&
            Session::get("page") !== "quotation_edit" &&
            Session::get("page") !== "quotation_details") ||
            empty(Input::get("record_id"))) {
            return 0;
        }

        $record_ids = explode(",", Input::get("record_id"));
        foreach ($record_ids as $id) {
            $all_quotation_ids[] = intval($id);
        }

        $result = Quotation::deleteRecord($all_quotation_ids);
        if ($result) {
            QuotationItems::deleteRelatedRecords($all_quotation_ids);
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
        $valid_status = array(0, 1);

        if (empty($record_id) || !in_array($status, $valid_status) ||
            ($status === 0 && !$this->is_allow_unvoid) ||
            ($status === 1 && !$this->is_allow_void)) {
            return 0;
        }

        return Quotation::updateStatus();
    }

    /**
     *  Print record
     *  @return void
     */
    public function printPDF($id = null) 
    {
        if (!$this->is_allow_print || $id == null || intval($id) == 0) {
            return Redirect::route("quotation_list");
        }

        $this->layout = null;
        $id = intval($id);
        $data = Quotation::get($id);
        if (empty($data)) {
            return Redirect::route("quotation_list");
        }

        $data['items'] = QuotationItems::get($id);
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
        $pdf = new QUPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        $date = substr($quote_date, 0, 10);
        $quote_date = new DateTime($date);

        $header_params = [
            'quotation_number' => $quotation_number,
            'quote_date' => $quote_date->format("d-F-Y"),
            'shop_address' => $shop_address,
            'shop_telephone' => $shop_telephone,
            'shop_fax' => $shop_fax,
            'staff_code' => $staff_code,
            'staff_title' => $staff_title,
            'staff_telephone' => $staff_telephone,
            'staff_mobile' => $staff_mobile,
            'staff_email' => $staff_email
        ];
        $header = View::make('outputs.quotation.header', array('params' => $header_params));
        $pdf->setHTMLHeader($header);

        $quote_terms_list = Config::get('cod_terms');
        $quote_terms_text = $quote_terms_list[$quote_terms];

        $footer_params = array(
            'shop_code' => $shop_code
        );

        $footer = View::make('outputs.quotation.footer', array('params' => $footer_params));
        $pdf->setHTMLFooter($footer);

        $last_page_footer_params = [
            'payment_terms' => $quote_terms_text,
            'shop_code' => $shop_code,
            'total_amount' => $total_amount,
            'discount_amount' => $discount_amount,
            'sub_total_amount' => $sub_total_amount
        ];

        $last_page_footer = View::make('outputs.quotation.last_page_footer', array('params' => $last_page_footer_params));
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
        $pdf->SetMargins(8, 38, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(4);
        $pdf->SetFooterMargin(62);
        $pdf->SetAutoPageBreak(TRUE, 59);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetFont('msungstdlight', '', 12);
        $pdf->AddPage('P', 'A4');

        $html = View::make('outputs.quotation.content', array('items' => $items));
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->lastPage();
        $pdf->Output($params["quotation_number"].'.pdf', 'I');

        return null;
    }

    public static function getItems()
    {
        $quotation_id = intval(Input::get("quotation_id"));
        $shop_code = Input::get("shop_code");
        return QuotationItems::getList($quotation_id, $shop_code);
    }
	
	public static function getDepositItems()
	{
        $quotation_id = intval(Input::get("quotation_id"));
        return QuotationItems::getItems($quotation_id);
	}
}
