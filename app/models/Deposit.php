<?php

class Deposit extends BaseModel
{
    const ORDER_CODE = "DP";
    const PAGE_LIMIT = 100;

    const STATUS_NORMAL = 0;
    const STATUS_VOIDED = 1;
    /**
     *  Get list
     *  @param integer $page Record page
     *  @return array Record list
     */
    public static function getList()
    {
        $page = (Input::has("page")) ? intval(Input::get("page")) : 1;
        $status = (Input::has("search_status")) ? intval(Input::get("search_status")) : 0;
        $where_clause = " AND `status` = '$status'";

        if (Input::has("search_deposit_terms")) {
            $deposit_terms = intval(Input::get("search_deposit_terms"));
            $where_clause .= " AND `deposit_terms` = '$deposit_terms'";
        }

        if (Input::has("search_payment_type")) {
            $payment_type = intval(input::get("search_payment_type"));
            $where_clause .= " AND `payment_type` = '$payment_type'";
        }

        if (Input::has("search_deposit_number")) {
            $deposit_number = Input::get("search_deposit_number");
            $where_clause .= " AND `deposit_number` LIKE '%$deposit_number%'";
        }

        if (Input::has("search_from_date")) {
            $from_date = parent::escape(Input::get("search_from_date"));
            $where_clause .= " AND `deposit_date` >= $from_date";
        }

        if (Input::has("search_to_date")) {
            $to_date = parent::escape(Input::get("search_to_date"));
            $where_clause .= " AND `deposit_date` <= $to_date";
        }

        $total_records_data = DB::select("
            SELECT SQL_NO_CACHE
                COUNT(*) AS `total_records`
            FROM `deposit`
            WHERE 1 $where_clause
        ");

        $total_records = $total_records_data[0]['total_records'];
        $total_pages = intval(ceil($total_records / self::PAGE_LIMIT));
        $start_record = ($page - 1) * self::PAGE_LIMIT;

        $list_data = null;
        if ($total_records > 0) {
            $list_data = DB::select("
                SELECT SQL_NO_CACHE
                    `id`, `deposit_number`, `deposit_date`, `shop_code`, `quotation_number`, `deposit_terms`,
                    `payment_type`, `total_items`, `total_qty`, `total_amount`, `payment_amount`,
                    `sub_total_amount`"
                . (($status === self::STATUS_VOIDED) ? ", `void_time`" : null) .
                "FROM `deposit`
                WHERE 1 $where_clause
                ORDER BY `id` DESC
                LIMIT $start_record, " . self::PAGE_LIMIT
            );
        }
        return array(
            'payment_type' => Config::get("payment_type"),
            'deposit_terms' => Config::get("cod_terms"),
            'status' => $status,
            'list_data' => $list_data,
            'page' => $page,
            'total_records' => $total_records,
            'total_pages' => $total_pages
        );
    }

    /**
     *  Get popup list
     *  @return array Record list
     */
    public static function getPopupList()
    {
        $page = (Input::has("page")) ? intval(Input::get("page")) : 1;
        $where_clause = null;

        if (Input::has("search_deposit_number")) {
            $deposit_number = Input::get("search_deposit_number");
            $where_clause .= " AND `deposit_number` = '$deposit_number' ";
        }

        if (Input::has("search_product_name")) {
            $product_name = Input::get("search_product_name");
            $where_clause .= " AND `p`.`name` LIKE '%$product_name%'";
        }

        $total_records_data = DB::select("
            SELECT SQL_NO_CACHE
                COUNT(*) AS `total_records`
            FROM `deposit` AS `d`
            LEFT JOIN `deposit_items` AS `di`
                ON `d`.`id` = `di`.`deposit_id`
            LEFT JOIN `product` AS `p`
                ON `di`.`product_id` = `p`.`id`
            WHERE `qty` > 0 $where_clause
        ");

		$total_records = $total_records_data[0]['total_records'];
        $total_pages = intval(ceil($total_records / self::PAGE_LIMIT));
        $start_record = ($page - 1) * self::PAGE_LIMIT;

        $list_data = null;
        if ($total_records > 0) {
	        $list_data = DB::select("
				SELECT SQL_NO_CACHE
					`id`, `deposit_number`, `deposit_date`, `deposit_terms`, `total_items`, 
					`total_qty`, `total_amount`, `payment_amount`, `sub_total_amount`,
					`shop_code`
				FROM `deposit`
				WHERE 1 $where_clause
					AND `status` = " . self::STATUS_NORMAL . "
				ORDER BY `deposit_date`
				LIMIT $start_record, " . self::PAGE_LIMIT
	        );
	    }

        return array(
			'list_data' => $list_data,
			'total_records' => $total_records,
			'total_pages' => $total_pages
        );
    }
	
    public static function getDepositItem($deposit_id, $shop_code)
    {
		$shop_id = Shop::getID($shop_code);
		$data = DB::select("
			SELECT SQL_NO_CACHE
				`di`.`id`, `p`.`id` AS `product_id`, `p`.`barcode`, `p`.`name` AS `product_name`, `di`.`unit_price`,
				IF(sum(`s`.`qty`) = 0, 0, `di`.`qty`) AS `qty`, `di`.`total_price`, IF(sum(`s`.`qty`) = 0, 0,sum(`s`.`qty`)) AS `stock_qty` , `shy`.`code`
			FROM `deposit_items` AS `di`
			LEFT JOIN `product` AS `p`
				ON `di`.`product_id` = `p`.`id`
			LEFT JOIN `deposit` AS `d`
				ON `di`.`deposit_id` = `d`.`id`
			LEFT JOIN `stock` AS `s`
				ON `di`.`product_id` = `s`.`product_id`
            LEFT JOIN `shop` AS `shy`
                ON `d`.`shop_code` = `shy`.`code`
			WHERE `deposit_id` = '$deposit_id'
				AND `s`.shop_id = $shop_id
			GROUP BY `id`
		");
		if($data){
			return $data;
		}
		
		return 0;
    }	

    /**
     *  Get record
     *  @param integer $id Record ID
     *  @return array Record data
     */
    public static function get($id)
    {
        $data = DB::select("
            SELECT SQL_NO_CACHE
                `d`.*, `sf`.`staff_code`
            FROM `deposit` AS `d`
            LEFT JOIN `staff` AS `sf`
                ON `d`.`staff_id` = `sf`.`id`
            WHERE `d`.`id` =  '$id'
        ");

        if (empty($data)) {
            return null;
        }

        return $data[0];
    }

    /**
     *  Edit record
     *  @param integer $edit_type Create/Update
     *  @return array Edit result
     */
    public static function edit($edit_type)
    {
        $fail_result = array("success" => false);
        $record_id = (Input::has("record_id")) ? intval(Input::get("record_id")) : 0;
        $is_update = ($edit_type === Config::get('edit_type.UPDATE')) ? true : false;
        $status = intval(Input::get("status"));

        // Validate the form fields
        $rules = array(
            'deposit_date' => 'required|date_format:Y-m-d',
            'payment_type' => 'required|integer',
            'deposit_terms' => 'required|integer',
            'shop_code' => 'required',
            'staff_id' => 'required|integer',
            'payment_amount' => 'required|numeric',
            'deposit_status' => 'numeric'
        );

        if ($is_update) {
            $rules['record_id'] = 'required|integer';
        }

        $v = Validator::make(Input::all(), $rules);
        if ($v->fails()) {
            return $fail_result;
        }

        $total_items = 0;
        $total_qty = 0;
        $total_amount = 0.00;

        $items = null;
        $removedItems = null;
        $oldItems = null;
        $newItems = null;
        $status = 0;

        $rules = array(
            'product_id' => 'required|integer',
            'qty' => 'required|integer',
            'unit_price' => 'required|numeric'
        );

        // Create new deposit
        if (!$is_update) {
            $items = Input::get("items");
            if ($items != null) {
                $result = Product::validateItems($items, $rules);
                if ($result === null) {
                    return $fail_result;
                } else {
                    extract($result);
                }
            }
        }

        // Update old deposit
        else {
            $removedItems = Input::get("removedItems");
            $oldItems = Input::get("oldItems");
            $newItems = Input::get("newItems");

            if ($oldItems != null) {
                $old_item_rules = array(
                    'record_id' => 'required|integer',
                    'qty' => 'required|integer',
                    'unit_price' => 'required|numeric'
                );

                $result = Product::validateItems($oldItems, $old_item_rules);
                if ($result === null) {
                    return $fail_result;
                } else {
                    extract($result);
                }
            }

            if ($newItems != null) {
                $result = Product::validateItems($newItems, $rules);
                if ($result === null) {
                    return $fail_result;
                } else {
                    $total_items += $result["total_items"];
                    $total_qty += $result["total_qty"];
                    $total_amount += $result["total_amount"];
                }
            }
        }

        $deposit_date = parent::escape(Input::get("deposit_date"));
        $quotation_number = parent::escape(Input::get("quotation_number"));
        $payment_type = intval(Input::get("payment_type"));
        $deposit_terms = intval(Input::get("deposit_terms"));
        $cheque_number = parent::escape(Input::get("cheque_number"));
        $cheque_date = parent::escape(Input::get("cheque_date"));
        $shop_code = parent::escape(Input::get("shop_code"));
        $staff_id = intval(Input::get("staff_id"));
        $remarks = parent::escape(Input::get("remarks"));
        $payment_amount = floatval(Input::get("payment_amount"));
        $sub_total_amount = $total_amount - $payment_amount;

        $staff_code = Session::get("staff_code");

        if ($sub_total_amount < 0.00) {
            return $fail_result;
        }

        $base_query = "
            `deposit`
            SET
                `deposit_date` = $deposit_date,
                `quotation_number` = $quotation_number,
                `deposit_terms` = '$deposit_terms',
                `payment_type` = '$payment_type',
                `cheque_number` = $cheque_number,
                `cheque_date` = $cheque_date,
                `shop_code` = $shop_code,
                `staff_id` = '$staff_id',
                `total_items` = '$total_items',
                `total_qty` = '$total_qty',
                `total_amount` = '$total_amount',
                `payment_amount` = '$payment_amount',
                `sub_total_amount` = '$sub_total_amount',
                `remarks` = $remarks,
                `last_update` = NOW(),
                `last_update_by` = '$staff_code'
        ";

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

            $params = array(
                'deposit_id' => $record_id,
                'removed_items' => $removedItems,
                'old_items' => $oldItems,
                'new_items' => $newItems,
            );

            $new_item_ids = DepositItems::edit($params);
            return array(
                "success" => true,
                "new_item_ids" => $new_item_ids
            );
        }

        // Create record
        else {
            $shop_code = Session::get('shop_code');
            $shop_id = Shop::getID($shop_code);
            $create_by = $staff_code;

            $data = DB::select("
                SELECT MAX(`receipt_id`) + 1 AS `receipt_id`
                FROM `deposit`
            ");

            extract($data[0]);
            if (empty($receipt_id)) {
                $receipt_id = 1;
            }

            $deposit_number = self::ORDER_CODE . date("y") . "-" . Session::get('shop_syb') . "01-" . sprintf("%05d", $receipt_id);

            $result = DB::insert("
                INSERT INTO
                    $base_query,
                    `receipt_id` = '$receipt_id',
                    `create_time` = NOW(),
                    `create_by` = '$create_by',
                    `deposit_number` = '$deposit_number'
            ");

            $new_id = DB::getPdo()->lastInsertId();
            $new_deposit_item_id = DepositItems::createRecord($new_id, $items);
            if (!$new_deposit_item_id) {
                return $fail_result;
            }

            return array(
                'success' => true,
                'new_id' => $new_id,
                'deposit_number' => $deposit_number,
                'deposit_item_id'   => $new_deposit_item_id,
            );
        }
    }

    public static function updateStatus()
    {
        $status = intval(Input::get("status"));
        $valid_status = array(
            self::STATUS_NORMAL,
            self::STATUS_VOIDED
        );

        if (!in_array($status, $valid_status) || !Input::has("record_id")) {
            return 0;
        }

        $record_id = explode(',', Input::get("record_id"));
        $total_ids = sizeof($record_id);
        $ids = array();
        foreach ($record_id as $id) {
            $ids[] = intval($id);
        }
        $record_id = implode(',', $ids);

        $result = DB::update("
            UPDATE `deposit`
            SET
                `last_update` = NOW(),
                `last_update_by` = '" . Session::get("staff_code") . "',
                `status` = '$status', 
                `void_time` = " . (($status == self::STATUS_VOIDED) ? 'NOW()' : "'0000-00-00 00:00:00'") .
            " WHERE `id` IN ($record_id)
            LIMIT $total_ids
        ");

        if ($result) {
            return 1;
        }
        return 0;
    }

    public static function deleteRecord($all_ids)
    {
        $total_items = sizeof($all_ids);
        $all_ids = implode(",", $all_ids);
        return DB::delete("
            DELETE FROM `deposit`
            WHERE `id` IN ($all_ids)
            LIMIT $total_items
        ");
    }

    public static function getDepositPrice($deposit_id)
    {
        $data = DB::select("
            SELECT SQL_NO_CACHE 
                `total_amount` 
            FROM `deposit` 
            WHERE `id` = '$deposit_id'
            LIMIT 0, 1
        ");

        return $data[0]['total_amount'];
    }

    public static function getDepositNumber($deposit_id = null)
    {
		$data = DB::select("
            SELECT SQL_NO_CACHE
                `deposit_number`
			FROM `deposit`
			WHERE `id` = '$deposit_id'
            LIMIT 0, 1
		");

		if ($data) {
            return $data[0]['deposit_number'];
		}
		return "";
    }
	
    public static function updateDepositStatus($record_id)
    {
		$data = DB::update("
            UPDATE `deposit`
            SET 
                `last_update` = NOW(),
                `last_update_by` = '" . Session::get("staff_code") . "',
                `status` = '2'
            WHERE `id` = '$record_id'
            LIMIT 1
        ");
	}	

}
