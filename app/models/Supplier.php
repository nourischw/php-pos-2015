<?php

class Supplier extends BaseModel
{
    /**
     * 	Create new order
     * 	@used-by (controller) ShoppingCartController
     * 	@used-by (controller) CheckoutProcessController
     * 	@param array $cart_params The shopping cart's parameters
     * 	@return array Array of order number
     */
    const PAGE_LIMIT = 100;



    public static function edit($edit_type)
    {
        $record_id = (Input::has("record_id")) ? intval(Input::get("record_id")) : 0;
        $is_update = ($edit_type === Config::get('edit_type.UPDATE')) ? true : false;

        // Validate the form fields
        $rules = array(
            'supplier_code' => 'required',
            'supplier_name' => 'required',
            'telephone' => 'integer',
            'mobile' => 'integer',
            'fax' => 'integer',
            'email' => 'email'
        );

        if ($is_update) {
            $rules['record_id'] = 'required|integer';
        }

        $v = Validator::make(Input::all(), $rules);
        if ($v->fails()) {
            return false;
        }

        $staff_name = Session::get('staff_name');
        $record_id = intval(Input::get("record_id"));
        $supplier_code = Input::get("supplier_code");
        $supplier_name = Input::get("supplier_name");
        $address = Input::get("address");
        $contact_person = Input::get("contact_person");
        $contact_person_title = Input::get("contact_person_title");
        $telephone = Input::get("telephone");
        $mobile = Input::get("mobile");
        $fax = Input::get("fax");
        $email = Input::get("email");

        $base_query = "
            `supplier`
            SET
                `code` = '$supplier_code',
                `name` = '$supplier_name',
                `address` = '$address',
                `contact_person` = '$contact_person',
                `contact_person_title` = '$contact_person_title',
                `telephone` = '$telephone',
                `mobile` = '$mobile',
                `fax` = '$fax',
                `email` = '$email',
                `last_update_by` = '$staff_name',
                `last_update` = NOW()
        ";

        $supplier_code_is_used = self::checkSupplierCode($record_id);
        if ($supplier_code_is_used) {
            return false;
        }

        $result = null;

        // Update record
        if ($is_update)  {
            $result = DB::update("
                UPDATE $base_query
                WHERE `id` = '$record_id'
                LIMIT 1
            ");
        }

        // Create record
        else {
            $result = DB::insert("
                INSERT INTO $base_query
            ");
        }

        if (!$result) {
            return false;
        }

        return true;
    }

    public static function deleteRecord($id)
    {
        $result = DB::delete("
            DELETE FROM `supplier`
            WHERE `id` = '$id'
            LIMIT 1
        ");
        
        if ($result) {
            return 1;
        }
        return 0;
    }

    public static function checkSupplierCode($record_id = 0)
    {
        $supplier_code = parent::escape(Input::get("supplier_code"));
        $supplier_id = ($record_id > 0) ? $record_id : intval(Input::get("supplier_id"));
        $data = DB::select("
            SELECT SQL_NO_CACHE 
                COUNT(*) AS `is_used`
            FROM `supplier`
            WHERE `code` = $supplier_code"
            . (($supplier_id > 0) ? " AND `id` != '$supplier_id'" : null) .
            " LIMIT 1
        ");

        return $data[0]['is_used'];
    }

    public static function getPopupList()
    {
        $page = (Input::has("page")) ? intval(Input::get("page")) : 1;
        $where_clause = null;

        if (Input::has("search_supplier_code")) {
            $supplier_code = Input::get("search_supplier_code");
            $where_clause .= " AND `code` LIKE '%$supplier_code%'";
        }

        if (Input::has("search_supplier_name")) {
            $supplier_name = Input::get("search_supplier_name");
            $where_clause .= " AND `name` LIKE '%$supplier_name%'";
        }

        $total_records_data = DB::select("
            SELECT SQL_NO_CACHE
                COUNT(*) AS `total_records`
            FROM `supplier`
            WHERE 1 $where_clause
        ");

		$total_records = $total_records_data[0]['total_records'];
        $total_pages = intval(ceil($total_records / self::PAGE_LIMIT));
        $start_record = ($page - 1) * self::PAGE_LIMIT;

        $list_data = DB::select("
            SELECT SQL_NO_CACHE
                `id`, `code`, `name`, `mobile`, `fax`, `email`
            FROM `supplier`
            WHERE 1 $where_clause
            ORDER BY `code`
            LIMIT $start_record, " . self::PAGE_LIMIT
        );

        return array(
            'list_data' => $list_data,
			'total_records' => $total_records,
            'total_pages' => $total_pages
        );
    }

	public static function getSupplierCode($supplier_id)
	{
		$supplier_id = intval($supplier_id);
		$data = DB::select("
			SELECT SQL_NO_CACHE `code`
			FROM `supplier`
			WHERE `id` = '$supplier_id'
			LIMIT 0, 1
		");

		return $data[0]['code'];
	}

    public static function getSupplierList($page = 1)
    {
        $start_record = ($page - 1) * self::PAGE_LIMIT;
        return DB::select("
            SELECT SQL_NO_CACHE *
            FROM `supplier`
            ORDER BY `id`
            LIMIT $start_record, " . self::PAGE_LIMIT
        );
    }

    public static function getSelectList()
    {
        return DB::select("
            SELECT SQL_NO_CACHE
                `id`, `code`, `name`
            FROM `supplier`
            ORDER BY `code`
        ");
    }

	public static function getPOSupplier($supplier_id)
	{
		$data = DB::select("
			SELECT SQL_NO_CACHE
				`id`, `code`
			FROM `supplier`
			WHERE `id` = $supplier_id
			LIMIT 0, 1
		");

		extract($data[0]);
		return array(
			'supplier_id'    => $id,
			'supplier_code'  => $code
		);
	}

	public static function getInfo($params)
	{
        $id = (isset($params['id'])) ? intval($params['id']) : null;
        $code = (isset($params['code'])) ? parent::escape($params['code']) : null;
		$data = DB::select("
			SELECT SQL_NO_CACHE
				`id` AS `supplier_id`, `name` AS `supplier_name`, `mobile`, `fax`, `email`
			FROM `supplier`
			WHERE 1 " .
            ((!empty($id)) ? " AND `id` = '$id' " : null) .
            ((!empty($code)) ? " AND `code` = $code " : null) .
			"LIMIT 0, 1
		");

		if (empty($data)) {
			return null;
		}
		return $data[0];
	}

	public static function searchGoodsInSupplier()
	{
		$keyword = Input::get("keyword");
		$data = DB::select("
			SELECT * FROM `supplier`
			WHERE `code` = '$keyword';
		");
		return $data[0];
	}

	public static function getSupplier()
    {
		$supplier_id = Input::get("supplier_id");
		$data = DB::select("
			SELECT SQL_NO_CACHE *
            FROM `supplier` 
            WHERE `id` = '$supplier_id'
            LIMIT 0, 1
		");
        if (empty($data)) {
            return null;
        }
		return $data[0];
	}

    public static function searchSupplierList($keyword, $page = 1)
    {
		$start_record = ($page - 1) * 100;
        $data = DB::select("
            SELECT SQL_NO_CACHE *
            FROM `supplier`
			WHERE `code` LIKE '%$keyword%' OR `name` LIKE '%$keyword%'
			LIMIT $start_record, " . self::PAGE_LIMIT
        );
		return $data;
	}
}
