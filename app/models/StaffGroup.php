<?php

class StaffGroup extends BaseModel
{
	const PAGE_LIMIT = 50;

    public static function getList() {
        $page = (Input::has("page")) ? intval(Input::get("page")) : 1;
        $where_clause = null;

        if (Input::has("search_staff_group_name")) {
            $group_name = Input::get("search_staff_group_name");
            $where_clause .= " AND `name` LIKE '%$group_name%'";
        }

        $total_records_data = DB::select("
            SELECT SQL_NO_CACHE COUNT(*) AS `total_records`
            FROM `staff_group`
            WHERE 1 $where_clause
        ");

        $total_records = $total_records_data[0]['total_records'];
        $total_pages = intval(ceil($total_records / self::PAGE_LIMIT));
        $start_record = ($page - 1) * self::PAGE_LIMIT;

        $list_data = null;
        if ($total_records > 0) {
            $list_data = DB::select("
                SELECT SQL_NO_CACHE 
                    `id`, `name`, `description`, `members`, `is_primary`
                FROM `staff_group`
                WHERE 1 $where_clause
                ORDER BY `name`
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

    public static function getSelectList() {
        return DB::select("
            SELECT `id`, `name`
            FROM `staff_group`
            ORDER BY `name` ASC
        ");
    }

    public static function get($group_id)
    {
        $data = DB::select("
            SELECT SQL_NO_CACHE *
            FROM `staff_group`
            WHERE `id` =  '$group_id'
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
        $is_update = ($record_type === Config::get('edit_type.UPDATE')) ? true : false;

        $rules = array(
            'name' => 'required'
        );

        if ($is_update) {
            $rules['record_id'] = 'required|integer';
        }

		$v = Validator::make(Input::all(), $rules);
		if ($v->fails()) {
			return $fail_result;
		}

		$name = parent::escape(Input::get("name"));
		$description = parent::escape(Input::get("description"));
        $permissions = Input::get("permission");
        if ($permissions != null) {
            $permissions = implode(',', $permissions);
        }
        

        $base_query = "
            `staff_group` 
            SET
				`name` = $name,
				`description` = $description,
                `permissions` = '$permissions',
                `last_update` = NOW()";

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

            return array(
                "success" => true
            );
        }

        // Create record
        else {
            $result = DB::insert("
                INSERT INTO $base_query
            ");

			if (!$result) {
				return $fail_result;
			}
			
            $new_id = DB::getPdo()->lastInsertId();
            return array(
                'success' => true,
                'new_id' => $new_id,
            );
        }
    }

    public static function countGroupMembers($group_id)
    {
        $data = DB::select("
            SELECT SQL_NO_CACHE COUNT(*) AS `total_members`
            FROM `staff`
            WHERE `staff_group` = '$group_id'
        ");

        $total_members = $data[0]['total_members'];
        DB::update("
            UPDATE `staff_group`
            SET `members` = '$total_members'
            WHERE `id` = '$group_id'
            LIMIT 1
        ");
    }

    public static function remove($all_ids)
    {
        $total_items = sizeof($all_ids);
        $all_ids = implode(",", $all_ids);

        return DB::delete("
            DELETE FROM `staff_group`
            WHERE
                `is_primary` != 1
                AND `members` < 1
                AND `id` IN ($all_ids)
            LIMIT $total_items
        ");
    }

}
