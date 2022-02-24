<?php

class StockTransportLogController extends BaseController
{
    /**
     *  @var string $layout Define the layout template
     */
    protected $layout = 'layouts.normal';

    public function __construct()
    {
        parent::__construct();
        $this->checkAllowAccess("STOCK_TRANSPORT_ACCESS");
	}

    /**
     *  Show list page
     *  @return void
     */
    public function showListView()
    {
        $list = Stock::getList();
        extract($list);

        $data = array(
            'list_data' => $list_data,
            'shop_list' => Shop::getSelectList(),
            'total_records' => $total_records,
            'total_pages' => $total_pages,
            'page' => $page,
            'end_page' => min(10, $total_pages),
            'have_record' => ($total_records > 0) ? true : false
        );

        $this->layout
            ->with('title', 'Stock Transport List')
            ->with('css', Config::get('css.CSS_LIST'))
            ->with('page_css', 'stock_list')
            ->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('js.JS_LIST'))
            ->with('page_js', 'stock_list')
            ->content = View::make('pages.stock_list', $data);
    }

    public function showLogView($stock_id)
    {
        $stock_data = Stock::getStockData($stock_id);
        $list = StockTransportLog::getList($stock_id);
        extract($list);

        $data = array(
            'stock_id' => $stock_id,
            'stock_data' => $stock_data,
            'list_data' => $list_data,
            'total_records' => $total_records,
            'total_pages' => $total_pages,
            'page' => $page,
            'end_page' => min(10, $total_pages),
            'have_record' => ($total_records > 0) ? true: false
        );

        $this->layout
            ->with('title', 'Stock Transport Logs')
            ->with('css', Config::get('css.CSS_LIST'))
            ->with('page_css', 'stock_transport_logs')
            ->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('js.JS_LIST'))
            ->with('page_js', 'stock_transport_logs')
            ->content = View::make('pages.stock_transport_logs', $data);
    }

    /**
     *  Search record
     *  @return void
     */
    public function searchList()
    {
        $stock_id = intval(Input::get("stock_id"));
        $stock_list = Stock::getList($stock_id);
        $data = array(
            'stock_list' => $stock_list,
            'have_records' => (!empty($stock_list['list_data'])) ? true : false
        );

        return View::make('list_rows.stock_list', $data);
    }

    public function searchLog()
    {
        $stock_id = intval(Input::get("stock_id"));
        $log_list = StockTransportLog::getList($stock_id);

        $data = array(
            'stock_transport_log_list' => $log_list,
            'have_records' => $log_list['have_records'],
			'total_records' => $log_list['total_records'],
			'total_pages' => $log_list['total_pages'],
        );

        return View::make('list_rows.stock_transport_logs', $data);
    }
}
