<?php

class BrandListPopupController extends BaseController
{
    const PAGE_LIMIT = 100;

    protected $layout = 'layouts.popup';
	
    public function showView()
    {
        $search_brand_name = Input::has("search_brand_name") ? Input::get("search_brand_name") : null;
        $search_brand_id = Input::has("search_brand_id") ? Input::get("search_brand_id") : null;
        $page = Input::get("page");
        $where_clause = 
            ( ($search_brand_name != '') ? " AND `name` LIKE '%$search_brand_name%'" : null) .
            ( ($search_brand_id != '') ? " AND `id` LIKE '%$search_brand_id%'" : null);
        $total_records = Brand::getTotalRecords();
        $total_pages = ceil($total_records / self::PAGE_LIMIT);
        $page = max(1, min($total_pages, $page));
        $brands = Brand::getBrandList($page, $where_clause);

        $css = array(
            Config::get('css.CSS_LIST') |
            Config::get('css.CSS_FILE')
        );
        $js = array(
            Config::get('js.JS_LIST')
        );
        $data = array(
            "page" => $page,
            "total_pages" => $total_pages,
            "brand_list" => $brands,
            "search_params" => array(
                "search_brand_name" => $search_brand_name,
                "search_brand_id" => $search_brand_id
            )
        ); 
        $this->layout
			 ->with('title', "Brand List POPUP")
			 ->with('css', Config::get('css.CSS_LIST'))
			 ->with('page_css', 'brand_list_popup')
			 ->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('js.JS_LIST'))
			 ->with('page_js', 'brand_list_popup')	
             ->content = View::make('popups.brand_list_popup', array("params" => $data));
    }

	public function searchBrand()
	{
		$keyword = Input::get("keyword");
		$page = Input::get("page");
		$brands_list = Brand::searchBrandList($keyword, $page);
		if (empty($brands_list)) {
			return "";
		}
		return json_encode($brands_list);
	}	
}
