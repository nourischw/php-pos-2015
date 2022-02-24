<?php

class StaffAttendanceController extends BaseController
{
    /**
     *  @var string $layout Define the layout template
     */
    protected $layout = 'layouts.normal';

    private $is_allow_create;

    public function __construct()
    {
        parent::__construct();
        $this->checkAllowAccess("STAFF_ATTENDANCE_ACCESS");
        $this->is_allow_create = $this->checkPermission("STAFF_ATTENDANCE_CREATE");
    }

    /**
     *  Show Staff Attendance page
     *  @param integer $id Record ID
     *  @return void
     */
      public function showAttendanceView($id = null)
      {
          Session::put('page', 'staff_attendance');
/*
          $record_id = intval($id);
          $staff_data = Staff::get($record_id);
          if (empty($staff_data)) {
              return Redirect::route("staff_list");
          }
*/
          $data = array(
              'is_allow_create' => $this->is_allow_create
          );

          $this->layout
              ->with('title', "Staff Attendance")
              ->with('css', Config::get('css.CSS_LIST'))
              ->with('page_css', 'staff_attendance')
              ->with('page_js', 'staff_attendance')
              ->content = View::make('pages.staff_attendance', $data);
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

    public function checkStaffCode()
    {
        return Staff::checkStaffCode();
    }

    public function checkStaffAccount()
    {
        return Staff::checkStaffAccount();
    }
}
