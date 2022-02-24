<?php

class SupplierController extends BaseController
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
        $this->checkAllowAccess("SUPPLIER_ACCESS");
        $this->is_allow_create = $this->checkPermission("SUPPLIER_CREATE");
        $this->is_allow_update = $this->checkPermission("SUPPLIER_UPDATE");
        $this->is_allow_delete = $this->checkPermission("SUPPLIER_DELETE");
    }

    /**
     * 	Show login page
     * 	@used-by (view) login.blade.php
     * 	@return void
     */
    public function showView()
    {
        Session::put("page", "supplier");
        $data = array(
            'supplier_list' => Supplier::getSupplierList(),
            'is_allow_create' => $this->is_allow_create,
            'is_allow_update' => $this->is_allow_update,
            'is_allow_delete' => $this->is_allow_delete
        );

        $this->layout
      			->with('title', "Supplier")
      			->with('css', Config::get('css.CSS_LIST'))
      			->with('page_css', 'supplier')
      			->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('js.JS_LIST'))
      			->with('page_js', 'supplier')
            ->content = View::make('pages.supplier', $data);
    }
	
    /**
     *  Create record
     *  @return mixed JSON result
     */
    public function create()
    {
        if (!$this->is_allow_create) {
            return 0;
        }

        if (Supplier::edit(Config::get('edit_type.CREATE'))) {
            return 1;
        }
        return 0;
    }

    public function update()
    {
        if (!$this->is_allow_update) {
            return 0;
        }
        if (Supplier::edit(Config::get('edit_type.UPDATE'))) {
            return 1;
        }
        return 0;
    }

    public function remove()
    {
        if (!$this->is_allow_delete ||
            Session::get("page") !== "supplier" ||
            empty(Input::get("record_id"))) {
            return 0;
        }

        $record_id = intval(Input::get("record_id"));
        $result = Supplier::deleteRecord($record_id);
        if ($result) {
            return 1;
        }
        return 0;
    }

    public function checkSupplierCode()
    {
        return Supplier::checkSupplierCode();
    }

  	public function get()
    {
        $data = Supplier::getSupplier();
        if (empty($data)) {
  		    return "";
  		}
  		return json_encode($data);
  	}

  	public function getPopupList()
  	{
  		$list = Supplier::getPopupList();
  		if (empty($list)) {
  			return null;
  		}
  		return View::make('popups.list_rows.supplier_list', $list);
  	}

    /**
     *  Get supplier info
     *  @return array The supplier data
     */
    public function getInfo()
    {
        return Supplier::getInfo(array('code' => Input::get('code')));
    }

    public function setSupplier()
    {
        $setSession = Input::get("setSession");
        $keyword = Input::get("keyword");
        ($setSession === "1") ? Session::put('supplier_keyword', $keyword) : Session::forget('supplier_keyword');
    }

  	public function supplierSearch()
  	{
  		$keyword = Input::get("keyword");
  		$suppliers_list = Supplier::searchSupplierList($keyword);
  		if (empty($suppliers_list)) {
  			return "";
  		}
  		return json_encode($suppliers_list);
  	}
}
