<?php

class StockWithdrawController extends BaseController
{
    /**
     *  @var string $layout Define the layout template
     */
    protected $layout = 'layouts.normal';

    private $is_allow_create;
    private $is_allow_delete;
    private $is_allow_finish;

    public function __construct()
    {
        parent::__construct();
        $this->checkAllowAccess("STOCK_WITHDRAW_ACCESS");
        $this->is_allow_create = $this->checkPermission("STOCK_WITHDRAW_CREATE");
        $this->is_allow_delete = $this->checkPermission("STOCK_WITHDRAW_DELETE");
		$this->is_allow_finish = $this->checkPermission("STOCK_WITHDRAW_FINISH");
	}

    /**
     *  Show list page
     *  @return void
     */
    public function showListView()
    {
        Session::put("page", "stock_withdraw_list");
        $list = StockWithdraw::getList();
        extract($list);

        $data = array(
            'list_data' => $list_data,
            'shop_list' => Shop::getSelectList(),
			'supplier_list' => Supplier::getSupplierList(),
            'total_records' => $total_records,
            'total_pages' => $total_pages,
            'page' => $page,
            'end_page' => min(10, $total_pages),
            'have_record' => ($total_records > 0) ? true : false,

            'is_allow_create' => $this->is_allow_create,
			'is_allow_delete' => $this->is_allow_delete,
			'is_allow_finish' => $this->is_allow_finish
        );

        $this->layout
            ->with('title', 'Stock Withdraw List')
            ->with('css', Config::get('css.CSS_LIST'))
            ->with('page_css', 'stock_withdraw_list')
            ->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('js.JS_LIST'))
            ->with('page_js', 'stock_withdraw_list')
            ->content = View::make('pages.stock_withdraw_list', $data);
    }

    /**
     *  Show create page
     *  @return void
     */
    public function showCreateView()
    {
		if (!$this->is_allow_create) {
			return Redirect::route('forbidden');
		}

        Session::put('page', 'stock_withdraw_edit');
        $staff_code = Session::get("staff_code");

        // Set header
        $data = array(
            'supplier_list' => Supplier::getSelectList()
        );

        $this->layout
            ->with('title', 'Create Stock Withdraw')
            ->with('css', Config::get('css.CSS_LIST'))
            ->with('page_css', 'stock_withdraw_edit')
            ->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('js.JS_LIST'))
            ->with('page_js', 'stock_withdraw_edit')
            ->content = View::make('pages.stock_withdraw_edit', $data);
    }

    /**
     *  Show details page
     *  @return void
     */
    public function showDetailsView($id = null)
    {
        if (empty($id)) {
            return Redirect::route("stock_withdraw_list");
        }

        Session::put('page', 'stock_withdraw_details');
        $record_id = intval($id);
        $stock_withdraw_data = StockWithdraw::get($record_id);
        $stock_withdraw_items_data = StockWithdrawItems::get($record_id);
        extract($stock_withdraw_data, EXTR_PREFIX_ALL, 'st');
		$status_text = "";

		switch($st_status) {
			case StockWithdraw::STATUS_PROCESSING:
				$status_text = "Processing";
				break;

			case StockWithdraw::STATUS_FINISHED:
				$status_text = "Finished";
				break;

			default:
				break;
		}

        $stock_withdraw_data['status_text'] = $status_text;

        $data = array(
            'staff_list' => Staff::getSelectList(Session::get('shop_id')),
            'stock_withdraw_data' => $stock_withdraw_data,
            'stock_withdraw_items_data' => $stock_withdraw_items_data,
            'total_items' => $st_total_items,
            'record_id' => $record_id,

            'is_allow_create' => $this->is_allow_create,
			'is_allow_delete' => $this->is_allow_delete,
            'is_allow_finish' => $this->is_allow_finish
        );

        $this->layout
            ->with('title', 'View Stock Withdraw Details')
            ->with('css', Config::get('css.CSS_LIST'))
            ->with('page_css', 'stock_withdraw_details')
            ->with('page_js', 'stock_withdraw_details')
            ->content = View::make('pages.stock_withdraw_details', $data);
    }

    /**
     *  Search record
     *  @return void
     */
    public function search()
    {
        $stock_withdraw_list = StockWithdraw::getList();
        $data = array(
            'stock_withdraw_list' => $stock_withdraw_list,
            'have_records' => (!empty($stock_withdraw_list)) ? true : false,

            'is_allow_create' => $this->is_allow_create,
            'is_allow_delete' => $this->is_allow_delete,
            'is_allow_finish' => $this->is_allow_finish
        );

        return View::make('list_rows.stock_withdraw_list', $data);
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

        $create_result = StockWithdraw::edit(Config::get('edit_type.CREATE'));
        if ($create_result["status"] == "success") {
            $new_id = $create_result["new_id"];
            $supplier_code = StockWithdraw::getSupplierCode($new_id);
            $items = StockWithdrawItems::getWithdrawQty($new_id);

            $log_params = array();
            $i = 0;
            foreach($items as $value) {
                $log_params[$i] = [
                    "type" => StockTransportLog::STOCK_TRANSPORT_TYPE_STOCK_WITHDRAW,
                    "stock_id" => $value["stock_id"],
                    "desc" => "[Stock Withdraw ID.: $new_id], hold [" . $value["qty"] . " qty] for stock withdraw to [Supplier Code: $supplier_code] by [Staff: " . Session::get("staff_code") . "]"
                ];
                $i++;
            }
            StockTransportLog::createRecord($log_params);

			extract($create_result);
            $result->status = "success";
            $result->stock_withdraw_id = $new_id;
        } elseif ($create_result["status"] == "have_invalid_items") {
			$result->status = "have_invalid_items";
			$result->invalid_items = $create_result["invalid_items"];
		}

        return json_encode($result);
    }

    /**
     *  Delete record
     *  @return integer Success result
     */
    public function remove()
    {
		if (!$this->is_allow_delete || empty(Input::get("record_id"))) {
			return 0;
		}

		$record_id = intval(Input::get("record_id"));
		$result = StockWithdraw::deleteRecord($record_id);
		if ($result) {
			Stock::releaseHoldingWithdrawItems($record_id);
            $supplier_code = StockWithdraw::getSupplierCode($record_id);
            $items = StockWithdrawItems::getWithdrawQty($record_id);

            $log_params = array();
            $i = 0;
            foreach($items as $value) {
                $log_params[$i] = [
                    "type" => StockTransportLog::STOCK_TRANSPORT_TYPE_STOCK_WITHDRAW,
                    "stock_id" => $value["stock_id"],
                    "desc" => "[Stock Withdraw ID.: $record_id], cancel stock withdraw by [Staff: " . Session::get("staff_code") . "], returned [" . $value["qty"] . " qty]"
                ];
                $i++;
            }
            StockTransportLog::createRecord($log_params);

			return 1;
		}

		return 0;
    }

    /**
     *  Finish stock withdraw
     *  @return integer Success result
     */
    public function finish()
    {
        $result = new stdClass();
        $result->status = "failure";
        if (!$this->is_allow_finish) {
            return json_encode($result);
        }

		$finish_result = StockWithdraw::finish();
		if ($finish_result) {
            $record_id = intval(Input::get("record_id"));
            $supplier_code = StockWithdraw::getSupplierCode($record_id);
            $items = StockWithdrawItems::getWithdrawQty($record_id);

            $log_params = array();
            $i = 0;
            foreach($items as $value) {
                $log_params[$i] = [
                    "type" => StockTransportLog::STOCK_TRANSPORT_TYPE_STOCK_WITHDRAW,
                    "stock_id" => $value["stock_id"],
                    "desc" => "[Stock Withdraw ID.: $record_id], mark finished by [Staff: " . Session::get("staff_code") . "], withdraw [" . $value["qty"] . " qty] to [Supplier Code:  $supplier_code]"
                ];
                $i++;
            }
            StockTransportLog::createRecord($log_params);

			$result->status = "success";
            $result->finished_date = date("Y-m-d");
		}

        return json_encode($result);
    }
}
