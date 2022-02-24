<?php

class CategoryController extends BaseController
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
        $this->checkAllowAccess("CATEGORY_ACCESS");
        $this->is_allow_create = $this->checkPermission("CATEGORY_CREATE");
        $this->is_allow_update = $this->checkPermission("CATEGORY_UPDATE");
        $this->is_allow_delete = $this->checkPermission("CATEGORY_DELETE");
        $this->is_allow_confirm = $this->checkPermission("CATEGORY_CONFIRM");
    }	

    /**
     * 	Show login page
     * 	@used-by (view) login.blade.php
     * 	@return void
     */
    public function showView()
    {
        $js = array('category');
		
		$getCategoryList = Category::getCategoryList();
		
        $data = array(
            'category_list' => $getCategoryList,
			
			'is_allow_create' 	=> $this->is_allow_create,
			'is_allow_update' 	=> $this->is_allow_update,
			'is_allow_delete' 	=> $this->is_allow_delete,
			'is_allow_confirm' 	=> $this->is_allow_confirm,
        );
        $this->layout
			 ->with('title', "Category")
			 ->with('css', Config::get('css.CSS_LIST'))
			 ->with('page_css', 'category')
			 ->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('js.JS_LIST'))
			 ->with('page_js', 'category')	
             ->content = View::make('pages.category', $data);
    }
	
    public function createCategory()
	{
        $result = new stdClass();
        $result->status = "fail";
        if (!$this->is_allow_create) {
            return json_encode($result);
        }		
		$category_order = Category::CreateOrder();
		if ($category_order) {
			$result->status = "success";
			return json_encode($result);
		}
        return json_encode($result);		
    }
	
    public function editCategory()
	{
        $result = new stdClass();        
		$result->status = "fail";
        if (!$this->is_allow_update) {
            return json_encode($result);
        }		
		$category_edit = Category::EditOrder();
		if ($category_edit) {
			$result->status = "success";
			return json_encode($result);
		}
        return json_encode($result);		
    }
	
    public function removecategory()
	{
        $result = new stdClass();        
		$result->status = "fail";
        if (!$this->is_allow_remove) {
            return json_encode($result);
        }		
		$category_remove = Category::RemoveOrder();
		if ($category_remove) {
			$result->status = "success";
			return json_encode($result);
		}
        return json_encode($result);		
    }
	
	public function getCategory(){
		$category_data = Category::getCategory();
        $data = array(
            'category_data' => $category_data,
        );		
		if (empty($data)) {
			return "";
		}
		return json_encode($data);	
	}
	
	public function setCategory()
	{
		$setSession = Input::get("setSession");
		$keyword = Input::get("keyword");
		($setSession === "1") ? Session::put('category_keyword', $keyword) : Session::forget('category_keyword');	
	}
	
	public function categorySearch()
	{
		$keyword = Input::get("keyword");
		$categorys_list = Category::searchCategoryList($keyword);
		if (empty($categorys_list)) {
			return "";
		}
		return json_encode($categorys_list);	
	}
}
