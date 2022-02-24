<?php

class SNTransactionController extends BaseController
{
    /**
     *  @var string $layout Define the layout template
     */
    protected $layout = 'layouts.normal';

    public function __construct()
    {
        parent::__construct();
        $this->checkAllowAccess("SN_TRANSACTION_ACCESS");
    }

    /**
     *  Show index page
     *  @used-by (view) index.blade.php
     *  @return void
     */
    public function showView()
    {
        $stock_list = Stock::getSNList();
        extract($stock_list);

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
            ->with('title', 'Serial Number Transaction')
            ->with('css', Config::get('css.CSS_LIST'))
            ->with('page_css', 'sn_transaction')
            ->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('js.JS_LIST'))
            ->with('page_js', 'sn_transaction')
            ->content = View::make('pages.sn_transaction', $data);
    }
    
    public function search()
    {
        $list = Stock::getSNList();
        if (empty($list)) {
            return null;
        }
        return View::make('list_rows.sn_transaction', $list);
    }
    
    public function updateSerialNumber()
    {
        return Stock::updateSerialNumber();
    }
}
