<?php

class Staff extends BaseModel
{
	const PAGE_LIMIT = 50;

    public static function processLogin()
    {
        /*
        if (!Session::has("onetime_token")) {
            return 0;
        }
        $onetime_token = Session::pull("onetime_token");        
        */

        // Validate the form fields
        $rules = array(
            'shop_code' => 'required',
            'staff_code' => 'required',
            'password' => 'required'
        );

        $v = Validator::make(Input::all(), $rules);
        if ($v->fails()) {
            return 0;
        }
        
        $staff_code = parent::escape(Input::get("staff_code"));
        $data = DB::select("
            SELECT SQL_NO_CACHE `password`
            FROM `staff`
            WHERE `staff_code` = $staff_code
            LIMIT 0, 1
        ");

        if (empty($data)) {
            return 0;
        }

        // Check whether the old password is match
        if (Input::get("password") != $data[0]['password']) {
            return 0;
        }

        $shop_code = parent::escape(Input::get("shop_code"));
        $data = DB::select("
            SELECT SQL_NO_CACHE
                `s`.`id` AS `staff_id`, `s`.`staff_code`, `s`.`name` AS `staff_name`, `s`.`staff_group`,
                `sg`.`permissions` AS `staff_permission`, `sh`.`id` AS `shop_id`, `sh`.`code` AS `shop_code`, 
                `sh`.`shop_symbol`, `sh`.`name` AS `shop_name`, `sh`.`address` AS `shop_address`, 
                `sh`.`telephone` AS `shop_telephone`, `sh`.`fax` AS `shop_fax`
            FROM `staff` AS `s`
            LEFT JOIN `shop` AS `sh`
                ON `s`.`shop_id` = `sh`.`id`
            LEFT JOIN `staff_group` AS `sg`
                ON `s`.`staff_group` = `sg`.`id`
            WHERE `s`.`staff_code` = $staff_code
            LIMIT 1
        ");

        if (empty($data)) {
            return 0;
        }

        extract($data[0]);
		// dd($staff_permission);
        $staff_permission = array_map('intval', explode(',', $staff_permission));
        Session::put('staff_id', $staff_id);
        Session::put('staff_name', $staff_name);
        Session::put('staff_code', $staff_code);
        Session::put('staff_group', $staff_group);
        Session::put('staff_permission', $staff_permission);

        Session::put('shop_id', $shop_id);
        Session::put('shop_code', $shop_code);
        Session::put('shop_compy', 'MOBILE CITY (HK) LIMITED');
        Session::put('shop_name', $shop_name);
        Session::put('shop_address', $shop_address);
        Session::put('shop_tel', $shop_telephone);
        Session::put('shop_fax', $shop_fax);
        Session::put('shop_syb', $shop_symbol);
        Session::regenerate();

        return 1;
    }

    public static function getList() {
        $page = (Input::has("page")) ? intval(Input::get("page")) : 1;
        $where_clause = null;

        if (Input::has("search_staff_code")) {
            $staff_code = Input::get("search_staff_code");
            $where_clause .= " AND `st`.`staff_code` LIKE '%$staff_code%'";
        }

        if (Input::has("search_staff_name")) {
            $from_shop_id = Input::get("search_staff_name");
            $where_clause .= " AND `st`.`name` LIKE '%$search_staff_name%'";
        }

        if (Input::has("search_staff_group")) {
            $staff_group = intval(Input::get("search_staff_group"));
            $where_clause .= " AND `st`.`staff_group` = '$staff_group'";
        }

        if (Input::has("search_shop_id")) {
            $shop_id = intval(Input::get("search_shop_id"));
            $where_clause .= " AND `st`.`shop_id` = '$shop_id'";
        }

        $total_records_data = DB::select("
            SELECT SQL_NO_CACHE COUNT(*) AS `total_records`
            FROM `staff` AS `st`
            LEFT JOIN `shop` AS `shop`
                ON `st`.`shop_id` = `shop`.`id`
            WHERE 1 $where_clause
        ");

        $total_records = $total_records_data[0]['total_records'];
        $total_pages = intval(ceil($total_records / self::PAGE_LIMIT));
        $start_record = ($page - 1) * self::PAGE_LIMIT;

        $list_data = null;
        if ($total_records > 0) {
            $list_data = DB::select("
                SELECT SQL_NO_CACHE
                    `st`.`id`, `st`.`staff_code`, `st`.`name`, `st`.`staff_group`, `shop`.`code` AS `shop_code`,
                    `sg`.`name` AS `staff_group_name`
                FROM `staff` AS `st`
                LEFT JOIN `shop` AS `shop`
                    ON `st`.`shop_id` = `shop`.`id`
                LEFT JOIN `staff_group` AS `sg`
                    ON `sg`.`id` = `st`.`staff_group`
                WHERE 1 $where_clause
                ORDER BY `st`.`staff_code`
                LIMIT $start_record, " . self::PAGE_LIMIT
            );
        }

        return array(
            'list_data' => $list_data,
            'page' => $page,
            'total_records' => $total_records,
            'total_pages' => $total_pages,
            'have_records' => (!empty($list_data)) ? true : false
        );
    }

    public static function getSelectList()
    {
        return DB::select("
            SELECT `id`, `staff_code`, `name`
            FROM `staff`
            ORDER BY `staff_code` ASC
        ");
    }

    public static function get($staff_id)
    {
        $data = DB::select("
            SELECT SQL_NO_CACHE
                `s`.`staff_code`, `s`.`shop_id`, `s`.`staff_group`, `s`.`name`, `s`.`title`, `s`.`telephone`, 
                `s`.`mobile`, `s`.`email`, `sh`.`code` AS `shop_code`, `sg`.`name` AS `staff_group_name`
            FROM `staff` AS `s`
            LEFT JOIN `shop` AS `sh`
                ON `sh`.`id` = `s`.`shop_id`
            LEFT JOIN `staff_group` AS `sg`
                ON `sg`.`id` = `s`.`staff_group`
            WHERE `s`.`id` =  '$staff_id'
        ");
        
        if (empty($data)) {
            return null;
        }

        return $data[0];
    }

    public static function edit($record_type)
    {
        $fail_result = array("success" => false);
        $record_id = (Input::has("record_id")) ? intval(Input::get("record_id")) : 0;
        $is_update = ($record_type === Config::get("edit_type.UPDATE")) ? true : false;

        $rules = array(
            'staff_code' => 'required',
            'name' => 'required',
            'shop_id' => 'required|integer',
            'staff_group' => 'required|integer',
  			'telephone' => 'integer',
  			'mobile' => 'integer',
  			'email' => 'email',
        );

        if (!$is_update) {
            $rules['password'] = 'required';
        } else {
            $rules['record_id'] = 'required|integer';
        }

		$v = Validator::make(Input::all(), $rules);
		if ($v->fails()) {
			return $fail_result;
		}

		if (self::checkStaffCode($record_id) > 0) {
			return $fail_result;
		}
		
		$staff_code = parent::escape(Input::get("staff_code"));
		$shop_id = intval(Input::get("shop_id"));
		$staff_group = intval(Input::get("staff_group"));
		$name = parent::escape(Input::get("name"));
		$title = parent::escape(Input::get("title"));
		$telephone = parent::escape(Input::get("telephone"));
		$mobile = parent::escape(Input::get("mobile"));
		$email = parent::escape(Input::get("email"));
		$password = parent::escape(Input::get("password"));

        $base_query = "
            `staff` 
            SET
				`staff_code` = $staff_code,
				`shop_id` = '$shop_id',
				`staff_group` = '$staff_group',
				`name` = $name,
				`title` = $title,
				`telephone` = $telephone,
				`mobile` = $mobile,
				`email` = $email,
                `last_update` = NOW(),
                `last_update_by` = '" . Session::get('staff_code') . "'";

        // Update record
        if ($is_update)  {
            $result = DB::update("
                UPDATE $base_query
                WHERE `id` = '$record_id'
                LIMIT 1
            ");

            if (!$result) {
                return $fail_result;
            }

            StaffGroup::countGroupMembers($staff_group);
            return array(
                "success" => true
            );
        }

        // Create record
        else {
            $result = DB::insert("
                INSERT INTO
                    $base_query,
                    `password` = $password,
                    `create_date` = NOW()
            ");

			if (!$result) {
				return $fail_result;
			}
			$new_id = DB::getPdo()->lastInsertId();

            StaffGroup::countGroupMembers($staff_group);
			
            return array(
                'success' => true,
                'new_id' => $new_id
            );
        }
    }

    public static function changePassword()
    {
        $rules = array(
            'old_password' => 'required',
            'new_password' => 'required'
        );
        
        $v = Validator::make(Input::all(), $rules);
        if ($v->fails()) {
            return 0;
        }

        $staff_id = Session::get("staff_id");
        $old_password = Input::get("old_password");
        $new_password = parent::escape(Input::get("new_password"));

        $data = DB::select("
            SELECT SQL_NO_CACHE `password`
            FROM `staff`
            WHERE `id` = '$staff_id'
            LIMIT 1
        ");

        if ($old_password !== $data[0]['password']) {
            return 0;
        }

        // Update staff's password
        return DB::update("
            UPDATE `staff`
            SET `password` = $new_password
            WHERE `id` = '$staff_id'
            LIMIT 1
        ");
    }

    public static function checkStaffCode($record_id = 0)
    {
        $staff_code = parent::escape(Input::get("staff_code"));
        $staff_id = intval(Input::get("staff_id"));

        if ($record_id > 0) {
            $staff_id = $record_id;
        }

        $data = DB::select("
            SELECT SQL_NO_CACHE 
                COUNT(*) AS `is_used`
            FROM `staff`
            WHERE `staff_code` = $staff_code"
            . (($staff_id > 0) ? " AND `id` != '$staff_id'" : null) .
            " LIMIT 1
        ");

        return $data[0]['is_used'];
    }

    public static function resetStaffPassword()
    {
        $rules = array(
            'staff_id' => 'required|integer',
            'password' => 'required'
        );

        $v = Validator::make(Input::all(), $rules);
        if ($v->fails()) {
            return 0;
        }

        $staff_id = intval(Input::get("staff_id"));
        $new_password = parent::escape(Input::get("password"));

        return DB::update("
            UPDATE `staff`
            SET `password` = $new_password
            WHERE `id` = '$staff_id'
            LIMIT 1
        ");
    }

  	public static function getStaffDetailByName($staff_name) {
      $data = DB::select("
        SELECT SQL_NO_CACHE
	        `id`, `staff_code`
        FROM `staff`
        WHERE name = '$staff_name'
      ");

      if (empty($data)) {
          return null;
      }

      return $data[0];
  	}

    public static function getStaffCode($id)
    {
        $data = DB::select("
            SELECT SQL_NO_CACHE `staff_code`
            FROM `staff`
            WHERE `id` = '$id'
            LIMIT 1
        ");

        if (empty($data)) {
            return null;
        }
        return $data[0]['staff_code'];
    }

  	public static function getStaffDetailByCode($staff_code){
      $data = DB::select("
        SELECT SQL_NO_CACHE
         `id`, `staff_code`
        FROM `staff`
        WHERE `staff_code` = '$staff_code'
      ");

      if (empty($data)) {
          return null;
      }
      return $data[0];
  	}
	
    public static function deleteRecord($all_ids)
    {
        $total_items = sizeof($all_ids);
        $all_ids = implode(",", $all_ids);

        $result = DB::delete("
            DELETE FROM `staff`
            WHERE `id` IN ($all_ids)
            LIMIT $total_items
        ");

        if ($result) {
            return 1;
        }
        return 0;
    }
	
	public static function SalesInvoiceStaffProcess($cashier, $password)
	{
      $data = DB::select("
        SELECT SQL_NO_CACHE
			*
        FROM `staff`
        WHERE `staff_code` = '$cashier'
			AND `password` = '$password'
      ");

      if (empty($data)) {
          return null;
      }
      return $data[0];		
	}

    public static function checkStaffAccount()
    {
        if (Input::get("staff_code") == "" || Input::get("staff_password") == "") {
            return 0;
        }
        $staff_code = parent::escape(Input::get("staff_code"));
        $staff_password = parent::escape(Input::get("staff_password"));

        $data = DB::select("
            SELECT SQL_NO_CACHE `staff_code`
            FROM `staff`
            WHERE 
                `staff_code` = $staff_code
                AND `password` = $staff_password
            LIMIT 0, 1
        ");

        if ($data == null) {
            return "";
        }

        return $data[0]['staff_code'];
    }
}
