<?php

class ProductController extends BaseController
{

    /**
     * 	@var string $layout Define the layout template
     */
    protected $layout = 'layouts.normal';

    /**
     * 	Show index page
     * 	@used-by (view) index.blade.php
     * 	@uses (self) _getAds
     * 	@uses (model) Deal get_index_category_deal
     * 	@uses (model) Deal get_main_deal
     * 	@uses (model) Deal get_today_deal
     * 	@return void
     */
    public function showView()
    {
        $data = array();
        $this->layout
            ->content = View::make('pages.sales_order', $data);
    }

    /**
     *  Get product info
     *  @return array The product data
     */
    public function getInfo()
    {
        return Product::getInfo(array('product_upc' => Input::get("product_upc")));
    }

    public function getPopupList()
    {
        $list = Product::getPopupList();
        if (empty($list)) {
            return null;
        }
        return View::make('popups.list_rows.product_list', $list);
    }
    
    public function SearchProduct(){
        
        $shopcode = Session::get('shop_code');
        
        $type = Input::get('search_type');
        $keyword = Input::get('search_keyword');

        if ($type == 1) {
            $data = PRODUCT::searchProductBySNO($shopcode, $keyword);
        } elseif($type == 2) {
            $data = PRODUCT::searchProductByIMEI($shopcode, $keyword);
        } elseif($type == 3) {
            $data = PRODUCT::searchProductByProductCode($shopcode, $keyword);
        } elseif($type == 4) {
            $data = PRODUCT::searchProductByName($shopcode, $keyword);
        } elseif($type == 5) {
            $data = PRODUCT::searchProductByPrice($shopcode, $keyword);
        }
        return json_encode($data, JSON_PRETTY_PRINT);
    }
    
    public function GetProduct(){
        
        $shopcode = Session::get('shop_code');
        
        $type = Input::get('search_type');
        $keyword = Input::get('search_keyword');

        if ($type == 1) {
            $data = PRODUCT::getProductBySNO($shopcode, $keyword);
        } elseif($type == 2) {
            $data = PRODUCT::getProductByIMEI($shopcode, $keyword);
        } elseif($type == 3) {
            $data = PRODUCT::getProductByProductCode($shopcode, $keyword);
        }
        return json_encode($data);
    }
}
