<?php

class StockTransferController extends BaseController
{
    /**
     *  @var string $layout Define the layout template
     */
    protected $layout = 'layouts.normal';

    private $is_allow_create;
    private $is_allow_update;
    private $is_allow_delete;
    private $is_allow_print;
    private $is_allow_confirm;
	private $is_allow_confirm_delivery;
	private $is_allow_cancel;
    private $is_allow_finish;
	private $is_allow_view_log;

    public function __construct()
    {
        parent::__construct();
        $this->checkAllowAccess("STOCK_TRANSFER_ACCESS");
        $this->is_allow_create = $this->checkPermission("STOCK_TRANSFER_CREATE");
        $this->is_allow_update = $this->checkPermission("STOCK_TRANSFER_UPDATE");
        $this->is_allow_delete = $this->checkPermission("STOCK_TRANSFER_DELETE");
        $this->is_allow_print = $this->checkPermission("STOCK_TRANSFER_PRINT");
        $this->is_allow_confirm = $this->checkPermission("STOCK_TRANSFER_CONFIRM");
		$this->is_allow_confirm_delivery = $this->checkPermission("STOCK_TRANSFER_CONFIRM_DELIVERY");
		$this->is_allow_cancel = $this->checkPermission("STOCK_TRANSFER_CANCEL");
		$this->is_allow_finish = $this->checkPermission("STOCK_TRANSFER_FINISH");
        $this->is_allow_retransfer = $this->checkPermission("STOCK_TRANSFER_RETRANSFER");
	}

    /**
     *  Show list page
     *  @return void
     */
    public function showListView()
    {
        Session::put("page", "stock_transfer_list");
        $list = StockTransfer::getList();
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
			'is_allow_delete' => $this->is_allow_delete,
            'is_allow_print' => $this->is_allow_print,
			'is_allow_cancel' => $this->is_allow_cancel,
			'is_allow_view_log' => $this->is_allow_view_log,
            'is_show_checkbox' => $this->is_allow_delete
        );

        $this->layout
            ->with('title', 'Stock Transfer List')
            ->with('css', Config::get('css.CSS_LIST'))
            ->with('page_css', 'stock_transfer_list')
            ->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('js.JS_LIST'))
            ->with('page_js', 'stock_transfer_list')
            ->content = View::make('pages.stock_transfer_list', $data);
    }

    /**
     *  Show edit page
     *  @return void
     */
    public function showEditView($id = null)
    {
        $record_id = (!empty($id)) ? intval($id) : 0;
        if ($record_id === 0) {
            if (!$this->is_allow_create) {
                return Redirect::route('forbidden');
            }
        } else if (!$this->is_allow_update) {
			return Redirect::route('forbidden');
        }

        Session::put('page', 'stock_transfer_edit');
        $stock_transfer_data = null;
        $stock_transfer_items_data = null;
        $staff_code = Session::get("staff_code");

        // Set default form value
        $st_stock_transfer_number = null;
        $st_from_shop_id = 0;
        $st_from_shop_code = null;
        $st_date_out = date("Y-m-d");
        $st_to_shop_id = null;
        $st_staff_id = Session::get("staff_id");
        $st_request_by = $staff_code;
        $st_remarks = null;
        $st_total_items = 0;
        $st_total_qty = 0;

        if ($record_id > 0) {
            Session::put('record_id', $record_id);
            $stock_transfer_data = StockTransfer::get($record_id);
            if (empty($stock_transfer_data)) {
                Redirect::route("stock_transfer_list");
            }
            $stock_transfer_items_data = StockTransferItems::get($record_id);
            extract($stock_transfer_data, EXTR_PREFIX_ALL, 'st');
            $st_total_items = sizeof($stock_transfer_items_data);
        }

        // Set header
        $data = array(
            'stock_transfer_items_data' => $stock_transfer_items_data,
            'staff_list' => Staff::getSelectList(),
            'shop_list' => Shop::getSelectList(),
            'record_id' => $record_id,
            'have_list_item' => (!empty($stock_transfer_items_data)) ? true : false,
            'is_update' => ($record_id > 0) ? true : false,

            'st_stock_transfer_number' => $st_stock_transfer_number,
            'st_from_shop_id' => $st_from_shop_id,
            'st_from_shop_code' => $st_from_shop_code,
            'st_date_out' => $st_date_out,
            'st_to_shop_id' => $st_to_shop_id,
            'st_staff_id' => $st_staff_id,
            'st_request_by' => $st_request_by,
            'st_remarks' => $st_remarks,
            'st_total_items' => $st_total_items,
            'st_total_qty' => $st_total_qty,

            'is_allow_create' => $this->is_allow_create,
            'is_allow_update' => $this->is_allow_update,
            'is_allow_delete' => $this->is_allow_delete,
            'is_allow_print' => $this->is_allow_print,
            'is_allow_confirm' => $this->is_allow_confirm,
            'is_allow_finish' => $this->is_allow_finish
        );

		// Check whether any transfer items is not have enough stock (Only for update record)
        if ($record_id > 0) {
			$have_invalid_item = false;
			foreach($stock_transfer_items_data as $item) {
				if ($item['remain_qty'] <= 0) {
					$have_invalid_item = true;
					break;
				}
			}
			$data['have_invalid_item'] = $have_invalid_item;
        }

        $this->layout
            ->with('title', 'Edit Stock Transfer')
            ->with('css', Config::get('css.CSS_LIST'))
            ->with('page_css', 'stock_transfer_edit')
            ->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('js.JS_LIST'))
            ->with('page_js', 'stock_transfer_edit')
            ->content = View::make('pages.stock_transfer_edit', $data);
    }
    /**
     *  Show details page
     *  @return void
     */
    public function showDetailsView($id = null)
    {
        if (empty($id)) {
            return Redirect::route("stock_transfer_list");
        }

        Session::put('page', 'stock_transfer');
        $record_id = intval($id);
        $stock_transfer_data = StockTransfer::get($record_id);
        $stock_transfer_items_data = StockTransferItems::get($record_id);
        extract($stock_transfer_data, EXTR_PREFIX_ALL, 'st');
		$status_text = "";

		switch($st_status) {
			case StockTransfer::STATUS_PROCESSING:
				$status_text = "Processing";
				break;

			case StockTransfer::STATUS_DELIVERED:
				$status_text = "Delivered";
				break;

			case StockTransfer::STATUS_FINISHED:
				$status_text = "Finished";
				break;

			case StockTransfer::STATUS_CANCELLED:
				$status_text = "Cancelled";
				break;

			default:
				break;
		}

        $stock_transfer_data['status_text'] = $status_text;
        $date_out = strtotime($stock_transfer_data['date_out']);
        $current_time = date("Y-m-d");

        $is_valid_shop = $stock_transfer_data['to_shop_id'] == Session::get("shop_id");

        $data = array(
            'shop_list' => Shop::getSelectList(),
            'staff_list' => Staff::getSelectList(Session::get('shop_id')),
            'to_shop_staff_list' => Staff::getSelectList($st_to_shop_id),
            'stock_transfer_data' => $stock_transfer_data,
			'current_shop_id' => $stock_transfer_data['to_shop_id'],
            'stock_transfer_items_data' => $stock_transfer_items_data,
            'total_items' => $st_total_items,
            'total_qty' => $st_total_qty,
            'record_id' => $record_id,
            'date_in' => ($date_out > strtotime($current_time)) ? $stock_transfer_data['date_out'] : $current_time,
            'today' => $current_time,

            'is_allow_create' => $this->is_allow_create,
            'is_allow_print' => $this->is_allow_print,
			'is_allow_confirm_delivery' => $this->is_allow_confirm_delivery && $is_valid_shop,
			'is_allow_confirm' => $this->is_allow_confirm && $is_valid_shop,
			'is_allow_cancel' => $this->is_allow_cancel,
            'is_allow_finish' => $this->is_allow_finish && $is_valid_shop,
			'is_allow_view_log' => $this->is_allow_view_log
        );

        $this->layout
            ->with('title', 'View Stock Transfer Details')
            ->with('css', Config::get('css.CSS_LIST'))
            ->with('page_css', 'stock_transfer_details')
            ->with('page_js', 'stock_transfer_details')
            ->content = View::make('pages.stock_transfer_details', $data);
    }


    /**
     *  Show stock transfer log page
     *  @return void
     */
	public function showLogView($stock_transfer_id = null)
	{
		if (!$this->is_allow_view_log || $stock_transfer_id == null) {
			return Redirect::Route("stock_transfer_list");
		}

		$stock_transfer_id = intval($stock_transfer_id);
		$stock_transfer_data = StockTransfer::get($stock_transfer_id);
		if ($stock_transfer_data == null) {
			return Redirect::Route("stock_transfer_list");
		}

		$data = array(
			'stock_transfer_number' => $stock_transfer_data["stock_transfer_number"],
			'record_id' => $stock_transfer_id
		);

		$this->layout
			->with('title', 'View Stock Transfer Log')
			->with('css', Config::get('css.CSS_LIST'))
			->with('page_css', 'stock_transfer_log')
			->with('page_js', 'stock_transfer_log')
			->content = View::make('pages.stock_transfer_log', $data);
	}

    /**
     *  Search record
     *  @return void
     */
    public function search()
    {
        $stock_transfer_list = StockTransfer::getList();
        $data = array(
            'stock_transfer_list' => $stock_transfer_list,
            'have_records' => (!empty($stock_transfer_list)) ? true : false,

            'is_allow_update' => $this->is_allow_update,
            'is_allow_delete' => $this->is_allow_delete,
			'is_allow_cancel' => $this->is_allow_cancel,
            'is_allow_print' => $this->is_allow_print,
            'is_allow_confirm' => $this->is_allow_confirm,
			'is_allow_view_log' => $this->is_allow_view_log
        );

        return View::make('list_rows.stock_transfer_list', $data);
    }

    /**
     *  Create record
     *  @return mixed JSON result
     */
    public function create()
    {
        $result = new stdClass();
        $result->status = "failure";
		$transfer_status = intval(Input::get("status"));
        if (!$this->is_allow_create || ($transfer_status == StockTransfer::STATUS_PROCESSING && !$this->is_allow_confirm)) {
            return json_encode($result);
        }

        $create_result = StockTransfer::edit(Config::get('edit_type.CREATE'));
        if ($create_result["status"] == "success") {
			extract($create_result);
			$shop_codes = StockTransfer::getShopCode($new_id);
            if ($transfer_status == StockTransfer::STATUS_PROCESSING) {
				self::confirmProcess($new_id);
            }

            $result->status = "success";
            $result->stock_transfer_id = $new_id;
            $result->stock_transfer_number = $stock_transfer_number;
            $result->new_item_ids = $new_item_ids;
        } elseif ($create_result["status"] == "have_invalid_items") {
			$result->status = "have_invalid_items";
			$result->invalid_items = $create_result["invalid_items"];
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
		$transfer_status = intval(Input::get("status"));
        if (!$this->is_allow_update || empty(Input::get('record_id')) || ($transfer_status == StockTransfer::STATUS_PROCESSING && !$this->is_allow_confirm)) {
            return json_encode($result);
        }

        $update_result = StockTransfer::edit(Config::get('edit_type.UPDATE'));
		if ($update_result["status"] == "success") {
            $record_id = intval(Input::get("record_id"));
            if ($transfer_status == StockTransfer::STATUS_PROCESSING) {
				self::confirmProcess($record_id);
            }

            $result->status = "success";
            $result->new_item_ids = $update_result["new_item_ids"];
        } elseif ($update_result["status"] == "have_invalid_items") {
			$result->status = "have_invalid_items";
			$result->invalid_items = $update_result["invlid_items"];
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
            (Session::get("page") !== "stock_transfer_list" &&
            Session::get("page") !== "stock_transfer_edit") ||
            empty(Input::get("record_id"))) {
            return 0;
        }

        $record_id = Input::get("record_id");
        $record_ids = explode(",", $record_id);
        foreach ($record_ids as $id) {
            $all_ids[] = intval($id);
        }

        $result = StockTransfer::deleteRecord($all_ids);
        if ($result) {
            StockTransferItems::deleteRelatedRecords($all_ids);
            return 1;
        }
        return 0;
    }

    /**
     *  Confirm stock transfer
     *  @return integer Success result
     */
    public function confirmProcess($record_id)
    {
		Stock::holdStockTransferItems($record_id);
        $items = StockTransferItems::getTransferQty($record_id);
		$shop_codes = StockTransfer::getShopCode($record_id);
		$stock_transfer_number = $shop_codes["stock_transfer_number"];

        $log_params = array();
        $i = 0;
        foreach($items as $value) {
            $log_params[$i] = [
                "type" => StockTransportLog::STOCK_TRANSPORT_TYPE_STOCK_TRANSFER,
                "stock_id" => $value["stock_id"],
                "desc" => "[Stock Transfer No.: $stock_transfer_number], Transfer [" . $value["qty"] . " qty] from [Shop: " . $shop_codes["from_shop_code"] . "] by [Staff: " . Session::get("staff_code") . "]"
            ];
            $i++;
        }

        StockTransportLog::createRecord($log_params);
    }

    /**
     *  Confirm deliver of stock transfer
     *  @return integer Success result
     */
    public function confirmDeliver()
    {
        if (!$this->is_allow_confirm_delivery || empty(Input::get("record_id"))) {
            return 0;
        }

		$record_id = intval(Input::get("record_id"));
		$result = StockTransfer::confirmDeliver();
		if ($result) {
			$shop_codes = StockTransfer::getShopCode($record_id);
			$receive_shop_code = $shop_codes["to_shop_code"];
			$receive_staff_code = Input::get('receive_by');
			$deliver_staff_code = Input::get('deliver_by');
			$is_mark_finished = intval(Input::get("is_mark_finished"));
			$stock_transfer_number = $shop_codes["stock_transfer_number"];

            $items = StockTransferItems::getTransferQty($record_id);

            $log_params = array();
            $i = 0;
            foreach($items as $value) {
                $log_params[$i] = [
                    "type" => StockTransportLog::STOCK_TRANSPORT_TYPE_STOCK_TRANSFER,
                    "stock_id" => $value["stock_id"],
                    "desc" => "[Stock Transfer No.: $stock_transfer_number], Delivered [" . $value["qty"] . " qty] to [Shop: " . $shop_codes["to_shop_code"] . "] by [Staff: $deliver_staff_code], Received by [Staff: $receive_staff_code]"
                ];
                $i++;
            }
            StockTransportLog::createRecord($log_params);

			if ($is_mark_finished == 1) {
                Stock::performStockTransfer($record_id);

                $log_params = array();
                $i = 0;
                foreach($items as $value) {
                    $log_params[$i] = [
                        "type" => StockTransportLog::STOCK_TRANSPORT_TYPE_STOCK_TRANSFER,
                        "stock_id" => $value["stock_id"],
                        "desc" => "[Stock Transfer No.: $stock_transfer_number], Mark finished by [Staff: " . Session::get("staff_code") . "], add [" . $value["qty"] . " qty] to [Shop: " . $shop_codes["from_shop_code"] . "]"
                    ];
                    $i++;
                }
                StockTransportLog::create($log_params);
			}
			return 1;
		}

        return 0;
    }

    /**
     * Retransfer stock transfer items
     * @return integer Success result
     */
    public function retransfer()
    {
        if (!$this->is_allow_retransfer || empty(Input::get("record_id"))) {
            return 0;
        }

        $record_id = intval(Input::get("record_id"));
        $result = StockTransfer::retransfer();
        if ($result) {
            $shop_codes = StockTransfer::getShopCode($record_id);
            $items = StockTransferItems::getTransferQty($record_id);
            $stock_transfer_number = $shop_codes["stock_transfer_number"];

            $log_params = array();
            $i = 0;
            foreach($items as $value) {
                $log_params[$i] = [
                    "type" => StockTransportLog::STOCK_TRANSPORT_TYPE_STOCK_TRANSFER,
                    "stock_id" => $value["stock_id"],
                    "desc" => "[Stock Transfer No.: $stock_transfer_number], Re-transfer [" . $value["qty"] . " qty] from [Shop: " . $shop_codes["from_shop_code"] ."] by [Staff: " . Session::get("staff_code") . "]"
                ];
                $i++;
            }
            StockTransportLog::createRecord($log_params);

            return 1;
        }

        return 0;
    }


    /**
     *  Finish stock transfer
     *  @return integer Success result
     */
    public function finish()
    {
        if (!$this->is_allow_finish || empty(Input::get("record_id"))) {
            return 0;
        }

        $record_id = intval(Input::get("record_id"));
		$result = StockTransfer::finish();
		if ($result) {
            $shop_codes = StockTransfer::getShopCode($record_id);
            $items = StockTransferItems::getTransferQty($record_id);
            $stock_transfer_number = $shop_codes["stock_transfer_number"];
			Stock::performStockTransfer($record_id, $shop_codes["to_shop_code"]);

            $log_params = array();
            $i = 0;
            foreach($items as $value) {
                $log_params[$i] = [
                    "type" => StockTransportLog::STOCK_TRANSPORT_TYPE_STOCK_TRANSFER,
                    "stock_id" => $value["stock_id"],
                    "desc" => "[Stock Transfer No.: $stock_transfer_number], Mark finished by [Staff: " . Session::get("staff_code") . "], add [" . $value["qty"] . " qty] to [Shop: " . $shop_codes["from_shop_code"] . "]"
                ];
                $i++;
            }
            StockTransportLog::createRecord($log_params);

			return 1;
		}

        return 0;
    }

    /**
     *  Cancel stock transfer
     *  @return integer Success result
     */
    public function cancel()
    {
		if (empty(Input::get("record_id"))) {
			return 0;
		}

		$record_id = intval(Input::get("record_id"));
		$result = StockTransfer::cancel($record_id);
		if ($result) {
			Stock::releaseHoldingStockItems($record_id);
            $shop_codes = StockTransfer::getShopCode($record_id);
            $items = StockTransferItems::getTransferQty($record_id);
            $stock_transfer_number = $shop_codes["stock_transfer_number"];

            $log_params = array();
            $i = 0;
            foreach($items as $value) {
                $log_params[$i] = [
                    "type" => StockTransportLog::STOCK_TRANSPORT_TYPE_STOCK_TRANSFER,
                    "stock_id" => $value["stock_id"],
                    "desc" => "[Stock Transfer No.: $stock_transfer_number], Cancelled by [Staff: " . Session::get("staff_code") . "], Returned stock [" . $value["qty"] . " qty] to [Shop: " . $shop_codes["from_shop_code"] . "]"
                ];
                $i++;
            }
            StockTransportLog::createRecord($log_params);

			return 1;
		}

		return 0;
    }

    /**
     *  print record
     *  @param integer $id Record ID
     *  @return void
     */
    public function printPDF($id = null)
    {
        if (!$this->is_allow_print || ($id == null && intval($id) == 0)) {
            return Redirect::route("stock_transfer_list");
        }

        $this->layout = null;
        $id = intval($id);
        $data = StockTransfer::get($id);
        if (empty($data)) {
            return Redirect::route("stock_transfer_list");
        }

        $data['transfer_items'] = StockTransferItems::get($id);
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
        $pdf = new TXPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $barcode = $pdf->serializeTCPDFtagParameters(array($stock_transfer_number, 'C39', '', '', 60, 12, 0.4, array('position'=>'N', 'border'=>false, 'padding'=>1, 'fgcolor'=>array(0,0,0), 'bgcolor'=>array(255,255,255), 'text'=>true, 'font'=>'helvetica', 'fontsize'=>8, 'stretchtext'=>1), 'N'));

        $header_params = [
            'stock_transfer_number' => $stock_transfer_number,
            'from_shop_code' => $from_shop_code,
            'date_out' => $date_out,
            'date_in' => $date_in,
            'remarks' => $remarks,
            'total_qty' => $total_qty,
            'to_shop_code' => $to_shop_code,
            'issue_staff' => $issue_staff,
            'request_by' => $request_by,
            'deliver_by' => $deliver_by,
            'receive_by' => $receive_by,
            'barcode' => $barcode
        ];
        $header = View::make('outputs.stock_transfer.header', array('params' => $header_params));
        $pdf->setHTMLHeader($header);

        $footer_params = [
            'print_datetime' => date("Y-m-d H:i:s"),
            'prepared_by' => Session::get('staff_name')
        ];
        $footer = View::make('outputs.stock_transfer.footer', array('params' => $footer_params));
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
        $pdf->SetMargins(12, 60, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(7);
        $pdf->SetFooterMargin(14);
        $pdf->SetAutoPageBreak(TRUE, 23);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetFont('msungstdlight', '', 12);
        $pdf->AddPage('P', 'A4');

        $content_params = [
            'transfer_items' => $transfer_items,
            'total_qty' => $total_qty
        ];

        $html = View::make('outputs.stock_transfer.content', array('params' => $content_params));
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->lastPage();
        $pdf->Output($params["stock_transfer_number"].'.pdf', 'I');

        return null;
    }
}
