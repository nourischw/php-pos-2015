<?php

class StockLevelController extends BaseController
{
    /**
     *  @var string $layout Define the layout template
     */
    protected $layout = 'layouts.normal';

    public function __construct()
    {
        parent::__construct();
        $this->checkAllowAccess("STOCK_LEVEL_ACCESS");
    }

    /**
     *  Show index page
     *  @used-by (view) index.blade.php
     *  @return void
     */
    public function showView()
    {
        $page = (Input::has("page")) ? intval(Input::get("page")) : 1;
        $list = Product::getStockLevel($page);
        if (empty($list)) {
            return null;
        }        
        extract($list);
        $data = array(
            'product_category_list' => ProductCategory::getSelectList(),
            'shop_list' => Shop::getSelectList(),            
            'list_data' => $list_data,
            'total_records' => $total_records,
            'total_pages' => $total_pages,
            'page' => $page,
            'end_page' => min(10, $total_pages),
            'have_record' => ($total_records > 0) ? true : false
        );

        $this->layout
            ->with('title', 'Stock Level')
            ->with('css', Config::get('css.CSS_LIST'))
            ->with('page_css', 'stock_level')
            ->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('js.JS_LIST'))
            ->with('page_js', 'stock_level')
            ->content = View::make('pages.stock_level', $data);
    }
    
    public function search()
    {
        $page = intval(Input::get("page"));
        $list = Product::getStockLevel($page);
        if (empty($list)) {
            return null;
        }
        return View::make('list_rows.stock_level_list', $list);
    }

    public function getProductStockLevel()
    {
        $product_id = intval(Input::get("product_id"));
        $list_data = Stock::getStockLevel($product_id);
        return View::make('list_rows.stock_level_shop_list', $list_data);
    }

}
