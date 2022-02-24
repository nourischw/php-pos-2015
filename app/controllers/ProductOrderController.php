<?php

class ProductOrderController extends BaseController
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
        $this->checkAllowAccess("PRODUCT_ACCESS");
        $this->is_allow_create = $this->checkPermission("PRODUCT_CREATE");
        $this->is_allow_update = $this->checkPermission("PRODUCT_UPDATE");
        $this->is_allow_delete = $this->checkPermission("PRODUCT_DELETE");
        $this->is_allow_confirm = $this->checkPermission("PRODUCT_CONFIRM");
    }
    /**
     * 	Show login page
     * 	@used-by (view) login.blade.php
     * 	@return void
     */
    public function showView($page = 1)
    {
      $total_records = Product::getTotalRecords();
      $total_pages = 0;
      if ($total_records != 0){
        $total_pages = ceil($total_records / 100);
        $page = max(1, $page);
        $page = min($total_pages, $page);
      }
      $products = Product::getProductList($page);
      $brands = Brand::getBrandPopupList();
      $categories = Category::getCategoryList();

      $data = array(
        "page" 			     => $page,
        "total_pages"		 => $total_pages,
        "products" 		   => $products,
        "brands" 		     => $brands,
        "categories" 		 => $categories,
        'have_record' => ($total_records > 0) ? true : false,

        "is_allow_create"		=>	$this->is_allow_create,
        "is_allow_update"		=>	$this->is_allow_update,
        "is_allow_delete"		=>	$this->is_allow_delete,
        "is_allow_confirm"	=>	$this->is_allow_confirm,
      );

      $js = array('product');
      $this->layout
            ->with('title', "Product")
            ->with('css', Config::get('css.CSS_LIST'))
            ->with('page_css', 'product')
            ->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('j  JS_LIST'))
            ->with('page_js', 'product')
            ->content = View::make('pages.product', $data);
    }

    /**
     *  Show popup list page
     *  @return void
     */
    public function getPopupList()
    {
        $list = Product::getPopupList();
        if (empty($list)) {
            return null;
        }
        return View::make('popups.list_rows.product_list', $list);
    }

	public function search()
	{
		$page = (Input::has("page")) ? intval(Input::get("page")) : 1;
		$keyword = Input::get("keyword");
		$clickResult = Input::get("clickResult");
		$where_clause = null;

		if(!empty($keyword)){
			if($clickResult == '1'){
				$where_clause = "P.`id` = '$keyword'";
			}else{
				$where_clause = "P.`barcode` LIKE '%$keyword%' OR P.`name` LIKE '%$keyword%'";
			}
		}

		$list_data = Product::searchProductList($page, $where_clause);
		if (empty($list_data)) {
			return "";
		}

		extract($list_data);

		return array(
			'total_pages' => $search_total_pages,
			'current_pages' => $current_pages,
			'product_list' => $product_list
		);
	}

	public function check()
	{
		return Product::checkProduct();
	}

    public function create()
	{
		if(!$this->is_allow_create){
			return false;
		}
        return Product::createProduct();
    }

    public function edit()
	{
		if (!$this->is_allow_update) {
			return false;
		}
        return Product::editProduct();
    }

    public function remove()
    {
		if(!$this->is_allow_delete){
			return false;
		}
        return Product::removeProduct();
    }

    public function setKeywordSession()
    {
        return Product::setKeywordSession();
    }

	public function uploadImages()
	{
		if(!$this->is_allow_confirm){
			return false;
		}
		return Product::uploadImages();
	}
}
