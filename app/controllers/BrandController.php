<?php

class BrandController extends BaseController
{

    /**
     * 	@var string $layout Define the layout template
     */
    protected $layout = 'layouts.normal';
	
    private $is_allow_create;
    private $is_allow_update;
    private $is_allow_delete;
    private $is_allow_confirm;

    public function __construct()
    {
        parent::__construct();
        $this->checkAllowAccess("BRAND_ACCESS");
        $this->is_allow_create = $this->checkPermission("BRAND_CREATE");
        $this->is_allow_update = $this->checkPermission("BRAND_UPDATE");
        $this->is_allow_delete = $this->checkPermission("BRAND_DELETE");
    }	

    /**
     * 	Show login page
     * 	@used-by (view) login.blade.php
     * 	@return void
     */
    public function showView()
    {
        $js = array('brand');
		
		$getBrandList = Brand::getBrandList();
		
        $data = array(
            'brand_list' => $getBrandList,
			
            'is_allow_create' => $this->is_allow_create,
            'is_allow_update' => $this->is_allow_update,
            'is_allow_delete' => $this->is_allow_delete,			
        );
		
        $this->layout
			 ->with('title', "Brand")
			 ->with('css', Config::get('css.CSS_LIST'))
			 ->with('page_css', 'brand')
			 ->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('js.JS_LIST'))
			 ->with('page_js', 'brand')					
             ->content = View::make('pages.brand', $data);
    }
	
    public function createBrand()
	{
        $result = new stdClass();
        $result->status = "fail";
        if (!$this->is_allow_create) {
            return json_encode($result);
        }		
		$brand_order = Brand::CreateOrder();
		if ($brand_order) {
			$result->status = "success";
			return json_encode($result);
		}
        return json_encode($result);		
    }
	
    public function editBrand()
	{
        $result = new stdClass();        
		$result->status = "fail";
        if (!$this->is_allow_update) {
            return json_encode($result);
        }	
		$brand_edit = Brand::EditOrder();
		if ($brand_edit) {
			$result->status = "success";
			return json_encode($result);
		}
        return json_encode($result);		
    }
	
    public function removebrand()
	{
        $result = new stdClass();
		$result->status = "fail";
        if (!$this->is_allow_delete) {
            return json_encode($result);
        }			
		$brand_remove = Brand::RemoveOrder();
		if ($brand_remove) {
			$result->status = "success";
			return json_encode($result);
		}
        return json_encode($result);		
    }
	
	public function getBrand(){
		$brand_data = Brand::getBrand();
        $data = array(
            'brand_data' => $brand_data,
        );		
		if (empty($data)) {
			return "";
		}
		return json_encode($data);	
	}
	
	public function setBrand()
	{
		$setSession = Input::get("setSession");
		$keyword = Input::get("keyword");
		($setSession === "1") ? Session::put('brand_keyword', $keyword) : Session::forget('brand_keyword');	
	}
	
	public function searchBrand()
	{
		$keyword = Input::get("keyword");
		$brands_list = Brand::searchBrandList($keyword);
		if (empty($brands_list)) {
			return "";
		}
		return json_encode($brands_list);	
	}
}
