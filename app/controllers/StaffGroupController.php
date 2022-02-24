<?php

class StaffGroupController extends BaseController
{
    /**
     *  @var string $layout Define the layout template
     */
    protected $layout = 'layouts.normal';

    private $is_allow_create;
    private $is_allow_update;
    private $is_allow_delete;

    public function __construct()
    {
        parent::__construct();
        $this->checkAllowAccess("STAFF_GROUP_ACCESS");
        $this->is_allow_create = $this->checkPermission("STAFF_GROUP_CREATE");
        $this->is_allow_update = $this->checkPermission("STAFF_GROUP_UPDATE");
        $this->is_allow_delete = $this->checkPermission("STAFF_GROUP_DELETE");
    }

    /**
     *  Show list page
     *  @return void
     */
    public function showListView()
    {
        Session::put("page", "staff_group_list");
        $st_list = StaffGroup::getList();
        extract($st_list);

        $data = array(
            'list_data' => $list_data,
            'total_records' => $total_records,
            'total_pages' => $total_pages,
            'page' => $page,
            'end_page' => min(10, $total_pages),
            'have_record' => ($total_records > 0) ? true : false,

            'is_allow_create' => $this->is_allow_create,
            'is_allow_update' => $this->is_allow_update,
            'is_allow_delete' => $this->is_allow_delete,
            'is_show_checkbox' => $this->is_allow_delete
        );

        $this->layout
            ->with('title', "Staff Group List")
            ->with('css', Config::get('css.CSS_LIST'))
            ->with('page_css', 'staff_group_list')
            ->with('js', Config::get('js.JS_FORM_VALIDATOR') | Config::get('js.JS_LIST'))
            ->with('page_js', 'staff_group_list')
            ->content = View::make('pages.staff_group_list', $data);
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
                return Redirect::route('staff_group_list');
            } 
        }

        Session::put('page', 'staff_group_edit');
        $group_data = null;

        // Set default form value
        $sg_name = null;
        $sg_description = null;
        $sg_is_primary = 0;
        $sg_members = 0;
        $sg_permissions = array();

        if ($record_id > 0) {
            $group_data = StaffGroup::get($record_id);
            if (empty($group_data)) {
                Redirect::route("staff_group_list");
            }
            extract($group_data, EXTR_PREFIX_ALL, 'sg');
            $sg_permissions = explode(',', $sg_permissions);
        }

        // Set header
        $data = array(
            'record_id' => $record_id,
            'is_update' => ($record_id > 0) ? true : false,

            'sg_name' => $sg_name,
            'sg_description' => $sg_description,
            'sg_is_primary' => ($sg_is_primary == 1) ? true : false,
            'sg_members' => $sg_members,
            'sg_permissions' => $sg_permissions,

            'is_allow_create' => $this->is_allow_create,
            'is_allow_update' => $this->is_allow_update,
            'is_allow_delete' => $this->is_allow_delete
        );

        $this->layout
            ->with('title', "Edit Staff Group")
            ->with('page_css', 'staff_group_edit')
            ->with('js', Config::get('js.JS_FORM_VALIDATOR'))
            ->with('page_js', 'staff_group_edit')
            ->content = View::make('pages.staff_group_edit', $data);
    }

    /**
     *  Show details page
     *  @return void
     */
    public function showDetailsView($id = null)
    {
        if (empty($id)) {
            return Redirect::route("staff_group_list");
        }
        Session::put('page', 'staff_group_details');

        $record_id = intval($id);
        $staff_group_data = StaffGroup::get($record_id);
        if (empty($staff_group_data)) {
            return Redirect::route("staff_group_list");
        }

        extract($staff_group_data, EXTR_PREFIX_ALL, 'sg');
        $sg_permissions = explode(',', $sg_permissions);

        $data = array(
            'sg_name' => $sg_name,
            'sg_description' => $sg_description,
            'sg_is_primary' => ($sg_is_primary == 1) ? true : false,
            'sg_permissions' => $sg_permissions,
            'sg_members' => $sg_members,
            'record_id' => $record_id,

            'is_allow_create' => $this->is_allow_create,
            'is_allow_update' => $this->is_allow_update,
            'is_allow_delete' => $this->is_allow_delete
        );

        $this->layout
            ->with('title', "View Staff Group")
            ->with('css', Config::get('css.CSS_LIST'))
            ->with('page_css', 'staff_group_details')
            ->with('page_js', 'staff_group_details')
            ->content = View::make('pages.staff_group_details', $data);
    }

    /**
     *  Search record
     *  @return void
     */
    public function search()
    {
        $staff_group_list = StaffGroup::getList();
        $data = array(
            'staff_group_list' => $staff_group_list,
            'have_records' => (!empty($staff_group_list['list_data'])) ? true : false,

            'is_allow_update' => $this->is_allow_update,
            'is_allow_delete' => $this->is_allow_delete
        );

        return View::make('list_rows.staff_group_list', $data);
    }

    /**
     *  Create record
     *  @return mixed Create staff result
     */
    public function create()
    {
        $result = new stdClass();
        $result->status = "failure";
        if (!$this->is_allow_create) {
            return json_encode($result);
        }
        $create_result = StaffGroup::edit(Config::get('edit_type.CREATE'));

        if ($create_result['success']) {
            extract($create_result);
            $result->status = "success";
            $result->staff_id = $new_id;
        }

        return json_encode($result);
    }

    /**
     *  Update record
     *  @return mixed Update staff result
     */
    public function update()
    {
        $result = new StdClass();
        $result->status = "failure";
        if (!$this->is_allow_update) {
            return json_encode($result);
        }
        
        $update_result = StaffGroup::edit(Config::get('edit_type.UPDATE'));
        if ($update_result["success"]) {
            $result->status = "success";
        }
        return json_encode($result);
    }

    /**
     *  Remove record
     *  @return integer Remove result
     */
    public function remove()
    {
        if (!$this->is_allow_delete ||
            (Session::get("page") !== "staff_group_list" &&
            Session::get("page") !== "staff_group_edit" &&
            Session::get("page") !== "staff_group_details") ||
            empty(Input::get("record_id"))) {
            return 0;
        }

        $record_id = Input::get("record_id");
        $record_ids = explode(",", $record_id);
        foreach ($record_ids as $id) {
            $all_ids[] = intval($id);
        }

        return StaffGroup::remove($all_ids);
    }
}
