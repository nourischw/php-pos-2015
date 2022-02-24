<?php

class StaffController extends BaseController
{
    /**
     *  @var string $layout Define the layout template
     */
    protected $layout = 'layouts.normal';

    private $is_allow_create;
    private $is_allow_update;
    private $is_allow_delete;
    private $is_allow_reset_password;

    public function __construct()
    {
        parent::__construct();
        $this->checkAllowAccess("STAFF_ACCESS");
        $this->is_allow_create = $this->checkPermission("STAFF_CREATE");
        $this->is_allow_update = $this->checkPermission("STAFF_UPDATE");
        $this->is_allow_delete = $this->checkPermission("STAFF_DELETE");
        $this->is_allow_reset_password = $this->checkPermission("STAFF_RESET_PASSWORD");
    }

    /**
     *  Show list page
     *  @return void
     */
    public function showListView()
    {
        Session::put("page", "staff_list");
        $st_list = Staff::getList();
        extract($st_list);

        $data = array(
            'list_data' => $list_data,
            'shop_list' => Shop::getSelectList(),
            'staff_group' => StaffGroup::getSelectList(),
            'total_records' => $total_records,
            'total_pages' => $total_pages,
            'page' => (Input::has("page")) ? intval(Input::get("page")) : 1,
            'end_page' => min(10, $total_pages),
            'have_record' => ($total_records > 0) ? true : false,

            'is_allow_create' => $this->is_allow_create,
            'is_allow_update' => $this->is_allow_update,
            'is_allow_delete' => $this->is_allow_delete,
            'is_allow_reset_password' => $this->is_allow_reset_password,
            'is_show_checkbox' => $this->is_allow_delete
        );

        $this->layout
            ->with('title', "Staff List")
            ->with('css', Config::get('css.CSS_LIST'))
            ->with('page_css', 'staff_list')
            ->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('js.JS_LIST'))
            ->with('page_js', 'staff_list')
            ->content = View::make('pages.staff_list', $data);
    }

    /**
     *  Show edit page
     *  @return void
     */
    public function showEditView($id = null)
    {
        $record_id = (!empty($id)) ? intval($id) : 0;
        if ($record_id === 0) {
            if (!$this->is_allow_create) {
                return Redirect::route('forbidden');
            }
        } else {
            if (!$this->is_allow_update) {
                return Redirect::route('staff_list');
            }
        }

        Session::put('page', 'staff_edit');
        $staff_data = null;

        // Set default form value
        $st_staff_code = null;
        $st_name = null;
        $st_shop_id = 0;
        $st_staff_group = 3;
        $st_title = null;
        $st_telephone = null;
        $st_mobile = null;
        $st_email = null;

        if ($record_id > 0) {
            $staff_data = Staff::get($record_id);
            if (empty($staff_data)) {
                Redirect::route("staff_list");
            }
            extract($staff_data, EXTR_PREFIX_ALL, 'st');
        }

        // Set header
        $data = array(
            'shop_list' => Shop::getSelectList(),
            'staff_group_list' => StaffGroup::getSelectList(),
            'record_id' => $record_id,
            'is_update' => ($record_id > 0) ? true : false,

            'st_staff_code' => $st_staff_code,
            'st_name' => $st_name,
            'st_shop_id' => $st_shop_id,
            'st_staff_group' => $st_staff_group,
            'st_title' => $st_title,
            'st_telephone' => $st_telephone,
            'st_mobile' => $st_mobile,
            'st_email' => $st_email,

            'is_allow_create' => $this->is_allow_create,
            'is_allow_update' => $this->is_allow_update,
            'is_allow_delete' => $this->is_allow_delete,
            'is_allow_reset_password' => $this->is_allow_reset_password
        );

        $this->layout
            ->with('title', "Edit Staff")
            ->with('page_css', 'staff_edit')
            ->with('js', Config::get('js.JS_FORM_VALIDATOR'))
            ->with('page_js', 'staff_edit')
            ->content = View::make('pages.staff_edit', $data);
    }

    /**
     *  Show reset password page
     *  @return void
     */
    public function showResetPasswordView()
    {
        if (!$this->is_allow_reset_password) {
            return Redirect::route('forbidden');
        }

        $id = intval(Input::get("record_id"));
        $staff_code = Staff::getStaffCode($id);
        if ($staff_code == null) {
            Redirect::route("staff_list");
        }

        $data = array(
            'record_id' => $id,
            'staff_code' => $staff_code,
            'from_list' => (Input::has("from_list")) ? true : false
        );

        $this->layout
            ->with('title', "Reset Staff Password")
            ->with('page_css', 'reset_staff_password')
            ->with('js', Config::get('js.JS_FORM_VALIDATOR'))
            ->with('page_js', 'reset_staff_password')
            ->content = View::make('pages.reset_staff_password', $data);
    }

    /**
     *  Show detail page
     *  @param integer $id Record ID
     *  @return void
     */
    public function showDetailsView($id = null)
    {
        if (empty($id)) {
            return Redirect::route('forbidden');
        }
        Session::put('page', 'staff_details');

        $record_id = intval($id);
        $staff_data = Staff::get($record_id);
        if (empty($staff_data)) {
            return Redirect::route("staff_list");
        }

        $data = array(
            'staff_data' => $staff_data,
            'record_id' => $record_id,

            'is_allow_create' => $this->is_allow_create,
            'is_allow_update' => $this->is_allow_update,
            'is_allow_delete' => $this->is_allow_delete,
            'is_allow_reset_password' => $this->is_allow_reset_password
        );

        $this->layout
            ->with('title', "View Staff")
            ->with('title', "View Staff")
            ->with('css', Config::get('css.CSS_LIST'))
            ->with('page_css', 'staff_details')
            ->with('page_js', 'staff_details')
            ->content = View::make('pages.staff_details', $data);
    }
    
    /**
     *  Search record
     *  @return void
     */
    public function search()
    {
        $staff_list = Staff::getList();
        $data = array(
            'staff_list' => $staff_list,
            'have_records' => (!empty($staff_list)) ? true : false,

            'is_allow_update' => $this->is_allow_update,
            'is_allow_delete' => $this->is_allow_delete,
            'is_allow_reset_password' => $this->is_allow_reset_password
        );
        return View::make('list_rows.staff_list', $data);
    }

    /**
     *  Create record
     *  @return mixed JSON result
     */
    public function create()
    {
        $result = new stdClass();
        $result->status = "failure";
        if (!$this->is_allow_create) {
            return json_encode($result);
        }

        $create_result = Staff::edit(Config::get('edit_type.CREATE'));

        if ($create_result['success']) {
            extract($create_result);
            $result->status = "success";
            $result->staff_id = $new_id;
        }

        return json_encode($result);
    }

    /**
     *  Update record
     *  @return mixed JSON result
     */
    public function update()
    {
        $result = new stdClass();
        $result->status = "failure";
        if (!$this->is_allow_update || !Input::has("record_id")) {
            return json_encode($result);
        }

        $update_result = Staff::edit(Config::get('edit_type.UPDATE'));
        if ($update_result["success"]) {
            $result->status = "success";
        }
        return json_encode($result);
    }

    /**
     *  Delete record
     *  @return integer Delete result
     */
    public function remove()
    {
        if (!$this->is_allow_delete ||
            (Session::get("page") !== "staff_list" &&
            Session::get("page") !== "staff_edit" &&
            Session::get("page") !== "staff_details") ||
            empty(Input::get("record_id"))) {
            return 0;
        }

        $record_id = Input::get("record_id");
        if (empty(Input::get("record_id"))) {
            return 0;
        }

        $record_ids = explode(",", $record_id);
        foreach ($record_ids as $id) {
            $all_ids[] = intval($id);
        }

        return Staff::deleteRecord($all_ids);
    }

    public function checkStaffCode()
    {
        return Staff::checkStaffCode();
    }

    public function processResetStaffPassword()
    {
        $result = ($this->is_allow_reset_password) ? Staff::resetStaffPassword() : 0;
        return $result;
    }

    public function checkStaffAccount()
    {
        return Staff::checkStaffAccount();
    }
}
