<?php

class StockWithdraw extends BaseModel
{
    const PAGE_LIMIT = 100;

    const STATUS_PROCESSING = 1;
    const STATUS_FINISHED = 2;

    public static function getList()
    {
        $page = (Input::has("page")) ? intval(Input::get("page")) : 1;
        $status = (Input::has("search_status")) ? intval(Input::get("search_status")) : self::STATUS_PROCESSING;
		$where_clause = " AND `sw`.`status` = '$status'";

        if (Input::has("search_id")) {
            $id = Input::get("search_id");
            $where_clause .= " AND `sw`.`id` = '$search_id'";
        }

        if (Input::has("search_supplier_id")) {
            $supplier_id = intval(Input::get("search_supplier_id"));
            $where_clause .= " AND `sw`.`supplier_id` = '$supplier_id'";
        }

        if (Input::has("search_from_date")) {
            $from_date = parent::escape(Input::get("search_from_date"));
            $where_clause .= " AND `sw`.`withdraw_date` >= $from_date";
        }

        if (Input::get("search_to_date")) {
            $to_date = parent::escape(Input::get("search_to_date"));
            $where_clause .= " AND `sw`.`withdraw_date` <= $to_date";
        }

        $total_records_data = DB::select("
            SELECT SQL_NO_CACHE
                COUNT(*) AS `total_records`
            FROM `stock_withdraw` AS `sw`
            WHERE 1 $where_clause
        ");

        $total_records = $total_records_data[0]['total_records'];
        $total_pages = intval(ceil($total_records / self::PAGE_LIMIT));
        $start_record = ($page - 1) * self::PAGE_LIMIT;

        $list_data = null;
        if ($total_records > 0) {
            $list_data = DB::select("
                SELECT SQL_NO_CACHE
					`sw`.`id`, DATE(`sw`.`withdraw_date`) AS `withdraw_date`, `s`.`code` AS `supplier_code`, `sw`.`status`,
					`sw`.`create_by`, `sw`.`total_items`, `sw`.`total_amount`, `sw`.`finished_date`, `sw`.`finished_by`
                FROM `stock_withdraw` AS `sw`
                LEFT JOIN `supplier` AS `s`
                    ON `s`.`id` = `sw`.`supplier_id`
                WHERE 1 $where_clause
                ORDER BY `id` DESC
                LIMIT $start_record, " . self::PAGE_LIMIT
            );
        }

        return array(
            'status' => $status,
            'list_data' => $list_data,
            'page' => $page,
            'total_records' => $total_records,
            'total_pages' => $total_pages,
            'have_records' => (!empty($list_data)) ? true : false
        );
    }

	public static function get($record_id)
	{
		$record_id = intval($record_id);

		$data = DB::select("
			SELECT SQL_NO_CACHE
				`sw`.*, `s`.`code` AS `supplier_code`
			FROM `stock_withdraw` AS `sw`
            LEFT JOIN `supplier` AS `s`
                ON `sw`.`supplier_id` = `s`.`id`
			WHERE `sw`.`id` = '$record_id'
			LIMIT 0, 1
		");

        if (empty($data)) {
            return null;
        }

        return $data[0];
	}

    public static function getSupplierCode($record_id)
    {
        $record_id = intval($record_id);

        $data = DB::select("
            SELECT SQL_NO_CACHE `s`.`code` AS `supplier_code`
            FROM `stock_withdraw` AS `sw`
            LEFT JOIN `supplier` AS `s`
                ON `s`.`id` = `sw`.`supplier_id`
            WHERE `sw`.`id` = '$record_id'
            LIMIT 0, 1
        ");

        if (empty($data)) {
            return null;
        }

        return $data[0]["supplier_code"];
    }

	public static function edit()
    {
        $fail_result = array("status" => "failed");

		// Validate the form fields
		$rules = array(
			'withdraw_date' => 'required|date_format:Y-m-d',
			'supplier_id' => 'required|integer'
		);

		$v = Validator::make(Input::all(), $rules);
		if ($v->fails()) {
			return $fail_result;
		}

        $items = null;
        $total_items = 0;

        $item_rules = array(
            'qty' => 'required|integer',
            'stock_id' => 'required|integer',
        );

		$items = Input::get("items");
		if (empty($items)) {
			return $fail_result;
		}

		foreach ($items as $key => $value) {
			$qty = intval($value['qty']);
			$v = Validator::make($items[$key], $item_rules);
			if ($v->fails() || $qty < 0) {
				return $fail_result;
			}

			$total_items++;
		}

		$all_item_ids = array_fetch($items, "stock_id");
		$is_valid = Stock::validateStockWithdrawItems($all_item_ids);
		if (!$is_valid) {
			return $fail_result;
		}

		// Check stock items qty
		$stock_items = array();
		foreach($items as $item) {
			$stock_items[$item['stock_id']] = intval($item['qty']);
		}

		$invalid_items = Stock::checkHaveInvalidItem($stock_items);
		if ($invalid_items != null) {
			return array(
				"status" => "have_invalid_items",
				"invalid_items" => $invalid_items
			);
		}

        $total_items = 0;
        $total_amount = 0.00;

        if ($items != null) {
	        $rules = array(
	            'stock_id' => 'required|integer',
	            'qty' => 'required|integer'
	        );

            $result = Product::validateItems($items, $rules);
            if ($result === null) {
                return $fail_result;
            } else {
                extract($result);
            }
        }

		$withdraw_date = parent::escape(Input::get("withdraw_date"));
		$supplier_id = intval(Input::get("supplier_id"));
        $remarks = parent::escape(Input::get("remarks"));

		$result = DB::insert("
			INSERT INTO `stock_withdraw`
			SET
				`withdraw_date` = $withdraw_date,
				`supplier_id` = '$supplier_id',
				`create_by` = '" . Session::get("staff_code") . "',
				`create_date` = NOW(),
				`total_items` = '$total_items',
				`total_amount` = '$total_amount',
                `remarks` = $remarks,
                `status` = '" . self::STATUS_PROCESSING . "'
		");

		if ($result) {
			$new_id = DB::getPdo()->lastInsertId();
			$create_items_result = StockWithdrawItems::createRecord($new_id, $items);
            Stock::holdStockWithdrawItems($items);
			if (!$create_items_result) {
				return $fail_result;
			}

			return array(
				'status' => "success",
				'new_id' => $new_id,
			);
		}

		return $fail_result;
    }

	public static function finish()
	{
		$rules = array(
            'record_id' => 'required|integer',
            'finished_by' => 'required'
        );
        $v = Validator::make(Input::all(), $rules);
        if ($v->fails()) {
            return 0;
        }

        $record_id = intval(Input::get("record_id"));
        $finished_by = parent::escape(Input::get("finished_by"));
        $result = DB::update("
            UPDATE `stock_withdraw`
            SET
                `status` = '" . self::STATUS_FINISHED . "',
                `finished_date` = NOW(),
                `finished_by` = $finished_by
            WHERE
				`id` = '$record_id'
				AND `status` = '" . self::STATUS_PROCESSING . "'
            LIMIT 1"
        );

        if ($result) {
            return 1;
        }

        return 0;
	}

    public static function deleteRecord($record_id)
    {
		$record_id = intval($record_id);
        $result = DB::delete("
            DELETE FROM `stock_withdraw`
            WHERE
                `id` = '$record_id'
                AND `status` = '" . self::STATUS_PROCESSING . "'
            LIMIT 1
        ");

        if ($result) {
            return 1;
        }

        return 0;
    }
}
