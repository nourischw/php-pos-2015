<?php

class BrandOrderController extends BaseController
{

    /**
     * 	@var string $layout Define the layout template
     */
    protected $layout = 'layouts.normal';

    /**
     * 	Show login page
     * 	@used-by (view) login.blade.php
     * 	@return void
     */
    public function showView()
    {
        $js = array('brand_order');
		
		$getBrandList = Brand::getBrandList();
		
        $data = array(
            'brand_list' => $getBrandList,
        );
		
        $this->layout
			 ->with('title', "Brand Order")
			 ->with('css', Config::get('css.CSS_LIST'))
			 ->with('page_css', 'brand_order')
			 ->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('js.JS_LIST'))
			 ->with('page_js', 'brand_order')	
             ->content = View::make('pages.brand_order', $data);
    }
	
    public function createBrand()
	{
        $cashier = Input::get("cashier_id");
        $password = Input::get("cashier_password");
        $result = new stdClass();
        $is_staff = STAFF::processLogin($cashier, $password);
        if ($is_staff) {
            $brand_order = Brand::CreateOrder();
            if ($brand_order) {
                $result->status = "success";
                return json_encode($result);
            }
        }

        $result->status = "fail";
        return json_encode($result);		
    }
	
	public function brandSearch()
	{
		$keyword = Input::get("keyword");
		$brands_list = Brand::searchBrandList($keyword);
		// dd($brands_list);
		if (empty($brands_list)) {
			return "";
		}
		return json_encode($brands_list);	
	}
}
