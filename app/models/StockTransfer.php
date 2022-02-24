<?php

class StockTransfer extends BaseModel
{
	const TRANSFER_CODE = "TX";
    const PAGE_LIMIT = 100;    

    const STATUS_PROCESSING = 1;
    const STATUS_PENDING = 2;
    const STATUS_FINISHED = 3;
	const STATUS_DELIVERED = 4;
    const STATUS_CANCELLED = 5;

    public static function getList()
    {
        $page = (Input::has("page")) ? intval(Input::get("page")) : 1;
        $status = (Input::has("search_status")) ? intval(Input::get("search_status")) : 1;
		$where_clause = " AND `st`.`status` = '$status'";
		
        if (Input::has("search_stock_transfer_number")) {
            $stock_transfer_number = Input::get("search_stock_transfer_number");
            $where_clause .= " AND `st`.`stock_transfer_number` LIKE '%$stock_transfer_number%'";
        }

        if (Input::has("search_from_shop")) {
            $from_shop_id = intval(Input::get("search_from_shop"));
            $where_clause .= " AND `from_shop`.`id` = '$from_shop_id'";
        }

        if (Input::has("search_to_shop")) {
        	$to_shop_id = intval(Input::get("search_to_shop"));
        	$where_clause .= " AND `to_shop`.`id` = '$to_shop_id'";
        }

        if (Input::has("search_from_date_out")) {
            $from_date_out = parent::escape(Input::get("search_from_date_out"));
            $where_clause .= " AND `st`.`date_out` >= $from_date_out";
        }
        
        if (Input::get("search_to_date_out")) {
            $to_date_out = parent::escape(Input::get("search_to_date_out"));
            $where_clause .= " AND `st`.`date_out` <= $to_date_out";
        }

        $total_records_data = DB::select("
            SELECT SQL_NO_CACHE
                COUNT(*) AS `total_records`
            FROM `stock_transfer` AS `st`
            LEFT JOIN `shop` `from_shop`
                ON `st`.`from_shop_id` = `from_shop`.`id`
            LEFT JOIN `shop` `to_shop`
            	ON `st`.`to_shop_id` = `to_shop`.`id`
            WHERE 1 $where_clause
        ");

        $total_records = $total_records_data[0]['total_records'];
        $total_pages = intval(ceil($total_records / self::PAGE_LIMIT));
        $start_record = ($page - 1) * self::PAGE_LIMIT;

        $list_data = null;
        if ($total_records > 0) {
            $list_data = DB::select("
                SELECT SQL_NO_CACHE
                    `st`.`id`, `from_shop`.`code` AS `from_shop_code`, `to_shop`.`code` AS `to_shop_code`,
                    `st`.`from_shop_id`, `st`.`stock_transfer_number`, `st`.`date_out`, `st`.`date_in`, 
                    `st`.`request_by`, `st`.`total_qty`, COUNT(sti.id) AS `total_items` 
                FROM `stock_transfer` AS `st`
                LEFT JOIN `shop` `from_shop`
                    ON `st`.`from_shop_id` = `from_shop`.`id`
                LEFT JOIN `shop` `to_shop`
                    ON `st`.`to_shop_id` = `to_shop`.`id`
                LEFT JOIN `stock_transfer_items` AS `sti`
                    ON `st`.`id` = `sti`.`stock_transfer_id`
                WHERE 1 $where_clause
                GROUP BY `st`.`id`
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

	public static function get($tx_id)
	{
		$data = DB::select("
			SELECT SQL_NO_CACHE
				`st`.`from_shop_id`, `from_shop`.`code` AS `from_shop_code`, `to_shop_id` AS `to_shop_id`, 
                `to_shop`.`code` AS `to_shop_code`, `st`.`stock_transfer_number`, `st`.`date_in`,
                `st`.`date_out`, `st`.`staff_id`, `sf`.`staff_code` AS `issue_staff`, `st`.`request_by`, 
                `st`.`deliver_by`, `st`.`receive_by`, `st`.`remarks`, `st`.`total_items`, 
                `st`.`total_qty`, `st`.`create_by`, `st`.`status`
			FROM `stock_transfer` AS `st`
			LEFT JOIN `shop` `from_shop`
				ON `st`.`from_shop_id` = `from_shop`.`id`
			LEFT JOIN `shop` `to_shop`
				ON `st`.`to_shop_id` = `to_shop`.`id`
			LEFT JOIN `staff` AS `sf`
				ON `st`.`staff_id` = `sf`.`id`
			WHERE `st`.`id` = '$tx_id'
			LIMIT 0, 1
		");
		
        if (empty($data)) {
            return null;
        }

        return $data[0];
	}
	
	public static function edit($record_type)
    {
        $fail_result = array("status" => "failed");
        $record_id = (Input::has("record_id")) ? intval(Input::get("record_id")) : 0;
        $is_update = ($record_type === Config::get('edit_type.UPDATE')) ? true : false;

		// Validate the form fields
		$rules = array(
			'date_out' => 'required|date_format:Y-m-d',
			'from_shop' => 'required|integer',
			'to_shop' => 'required|integer',
			'staff_id' => 'required|integer',
			'request_by' => 'required',
            'status' => 'required|integer'
		);

        if ($is_update) {
            $rules['record_id'] = 'required|integer';
        }

		$v = Validator::make(Input::all(), $rules);
		if ($v->fails()) {
			return $fail_result;
		}

        $items = null;
        $removedItems = null;
        $oldItems = null;
        $newItems = null;
        $total_items = 0;
        $total_qty = 0;
        $from_shop = intval(Input::get("from_shop"));
		
		$stock_items = array();

        $item_rules = array(
            'transfer_qty' => 'required|integer',
            'stock_id' => 'required|integer',
        );

		// Create new record
        if (!$is_update) {
            $items = Input::get("items");
			if (empty($items)) {
				return $fail_result;
			}
			
			$items = Input::get("items");
			
            foreach ($items as $key => $value) {
                $qty = intval($value['transfer_qty']);
                $v = Validator::make($items[$key], $item_rules);
                if ($v->fails() || $qty < 0) {
                    return $fail_result;
                }

                $total_items++;
                $total_qty += $qty;
            }

            $all_item_ids = array_fetch($items, "stock_id");
            $is_valid = Stock::validateStockTransferItems($from_shop, $all_item_ids);
            if (!$is_valid) {
                return $fail_result;
            }
		}
		
		// Update old record
        else {
            $removedItems = Input::get("removedItems");
            $oldItems = Input::get("oldItems");
            $newItems = Input::get("newItems");
			$old_items = array();
			$new_items = array();

            $old_item_rules = array(
                'record_id' => 'required|integer',
                'transfer_qty' => 'required|integer'
            );
			
			if (empty($oldItems) && empty($newItems)) {
				return $fail_result;
			}

            if (!empty($oldItems)) {
				$old_items = $oldItems;
                foreach ($oldItems as $key => $value) {
                    $qty = intval($value['transfer_qty']);
                    $v = Validator::make($oldItems[$key], $old_item_rules);
                    if ($v->fails() || $qty < 0) {
                        return $fail_result;
                    }

                    $total_items++;
                    $total_qty += $qty;
                }

                $is_valid = Stock::validateStockTransferItems($from_shop, $oldItems);
                if (!$is_valid) {
                    return $fail_result;
                }
            }

            // Validate new items
            if (!empty($newItems)) {
				$new_items = $newItems;
                foreach ($newItems as $key => $value) {
                    $qty = intval($value['transfer_qty']);
                    $v = Validator::make($newItems[$key], $item_rules);
                    if ($v->fails() || $qty < 0) {
                        return $fail_result;
                    }
                    
                    $total_items++;
                    $total_qty += $qty;
                }

                $is_valid = Stock::validateStockTransferItems($from_shop, $newItems);
                if (!$is_valid) {
                    return $fail_result;
                }
            }
			
			$items = array_merge($old_items, $new_items);
        }
		
		// Check stock items qty
		foreach($items as $item) {
			$stock_items[$item['stock_id']] = intval($item['transfer_qty']);
		}
		
		$invalid_items = Stock::checkHaveInvalidItem($stock_items);
		if ($invalid_items != null) {
			return array(
				"status" => "have_invalid_items",
				"invalid_items" => $invalid_items
			);
		}		
		
		$to_shop_id = intval(Input::get("to_shop_id"));
		$date_out = parent::escape(Input::get("date_out"));
		$from_shop = intval(Input::get("from_shop"));
        $to_shop = intval(Input::get("to_shop"));
		$staff_id = intval(Input::get("staff_id"));
		$request_by = parent::escape(Input::get("request_by"));
        $remarks = parent::escape(Input::get("remarks"));
        $status = intval(Input::get("status"));
        
        $base_query = "
            `stock_transfer`
            SET
                `date_out` = $date_out,
                `from_shop_id` = '$from_shop',
                `to_shop_id` = '$to_shop',
                `staff_id` = '$staff_id',
                `request_by` = $request_by,
                `remarks` = $remarks,
                `total_items` = '$total_items',
                `total_qty` = '$total_qty',
                `last_update` = NOW(),
                `last_update_by` = '" . Session::get('staff_code') . "',
                `status` = '$status'
        ";
        
		// Update old record
        if ($is_update) {
            $result = DB::update("
                UPDATE $base_query
                WHERE `id` = '$record_id'
                LIMIT 1
            ");

            if (!$result) {
                return $fail_result;
            }

            $params = array(
                'stock_transfer_id' => $record_id,
                'removed_items' => $removedItems,
                'old_items' => $oldItems,
                'new_items' => $newItems
            );

            $new_item_ids = StockTransferItems::edit($params);    
            
            return array(
                "status" => "success",
                "new_item_ids" => $new_item_ids
            );
        } 

		// Create new record
        else {
            $shop_code = Session::get('shop_code');
            $shop_id = Shop::getID($shop_code);

            $data = DB::select("
                SELECT MAX(`receipt_id`) + 1 AS `receipt_id` 
                FROM `stock_transfer`
                WHERE `request_shop_id` = '" . $shop_id . "'
            ");
            
            extract($data[0]);
            if (empty($receipt_id)) {
                $receipt_id = 1;
            }

            $stock_transfer_number = self::TRANSFER_CODE . date("y") . "-" . Session::get('shop_syb') . "-" . sprintf("%05d", $receipt_id);
            $result = DB::insert("
                INSERT INTO
                    $base_query,
                    `request_shop_id` = '$shop_id',
                    `receipt_id` = '$receipt_id',
                    `create_time` = NOW(),
                    `create_by` = '" . Session::get("staff_code") . "',
                    `stock_transfer_number` = '" . strtoupper($stock_transfer_number) . "'
            ");

            $new_id = DB::getPdo()->lastInsertId();
            $new_item_ids = StockTransferItems::createRecord($new_id, $items);
            if (!$new_item_ids) {
                return $fail_result;
            }

            Session::put("record_id", $new_id);
            
            return array(
                'status' => "success",
                'new_id' => $new_id,
                'stock_transfer_number' => $stock_transfer_number,
                'new_item_ids' => $new_item_ids
            );
        }
		
		return $fail_result;
    }
	
	public static function confirmDeliver()
	{
        $rules = array(
            'record_id' => 'required|integer',
            'deliver_by' => 'required',
            'receive_by' => 'required',
			'mark_finished' => 'integer'
        );
        $v = Validator::make(Input::all(), $rules);
        if ($v->fails()) {
            return 0;
        }
		
		extract(Input::all());
		$current_staff_code = Session::get("staff_code");
		$deliver_by = parent::escape($deliver_by);
		$receive_by = parent::escape($receive_by);
		$mark_finish_by = Session::get("staff_code");
		$status = ($is_mark_finished == 1) ? self::STATUS_FINISHED : self::STATUS_DELIVERED;
		
		$record_id = intval($record_id);
		$result = DB::update("
			UPDATE `stock_transfer`
			SET
				`date_in` = CURDATE(),
				`deliver_by` = $deliver_by,
				`receive_by` = $receive_by, " .
			(($is_mark_finished == 1) ? "`mark_finished_by` = '$current_staff_code', " : "") .
				"`last_update` = NOW(),
				`last_update_by` = '$current_staff_code',
				`status` = '$status'
			WHERE 
				`id` = '$record_id'
				AND `status` = '" . self::STATUS_PROCESSING . "'
			LIMIT 1
		");

		if ($result == 1) {
            return 1;
		}

		return 0;
	}

    public static function retransfer()
    {
        $rules = array(
            'record_id' => 'required|integer',
            'to_shop_id' => 'required|integer'
        );
        $v = Validator::make(Input::all(), $rules);
        if ($v->fails()) {
            return 0;
        }

        extract(Input::all());

        $stock_transfer_data = StockTransfer::get($record_id);
        $record_id = intval($record_id);
        $current_staff_code = Session::get("staff_code");
        $from_shop_id = intval($stock_transfer_data["to_shop_id"]);
        $to_shop_code = intval($to_shop_id);
        $status = self::STATUS_PROCESSING;

        $result = DB::update("
            UPDATE `stock_transfer`
            SET
                `date_out` = CURDATE(),
                `from_shop_id` = '$from_shop_id',
                `to_shop_id` = '$to_shop_id',
                `last_update` = NOW(),
                `last_update_by` = '$current_staff_code',
                `status` = '$status'
            WHERE
                `id` = '$record_id'
                AND `status` = '" . self::STATUS_DELIVERED . "'
            LIMIT 1
        ");

        if ($result) {
            return 1;
        }

        return 0;
    }
	
	public static function finish()
	{
		$rules = array(
            'record_id' => 'required|integer'
        );
        $v = Validator::make(Input::all(), $rules);
        if ($v->fails()) {
            return 0;
        }

        $record_id = intval(Input::get("record_id"));
		$current_staff_code = Session::get("staff_code");
		
        $result = DB::update("
            UPDATE `stock_transfer`
            SET
                `status` = '" . self::STATUS_FINISHED . "',
                `mark_finished_by` = '$current_staff_code',
                `last_update` = NOW(),
                `last_update_by` = '$current_staff_code'
            WHERE 
				`id` = '$record_id'
				AND `status` = '" . self::STATUS_DELIVERED . "'
            LIMIT 1"
        );

        if ($result) {
            return 1;
        }

        return 0;
	}
	
    public static function cancel($record_id)
    {
		if (empty($record_id)) {
			return 0;
		}
		
        $result = DB::update("
            UPDATE `stock_transfer`
            SET 
                `status` = '" . self::STATUS_CANCELLED . "',
				`cancelled_by` = '" . Session::get('staff_code') . "',
                `last_update` = NOW(),
                `last_update_by` = '" . Session::get('staff_code') . "'
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
    
    public static function deleteRecord($all_ids)
    {
        $total_items = sizeof($all_ids);
        $all_ids = implode(",", $all_ids);
        
        $result = DB::delete("
            DELETE FROM `stock_transfer`
            WHERE
                `id` IN ($all_ids)
                AND `status` = '" . self::STATUS_PENDING . "'
            LIMIT $total_items
        ");

        if ($result) {
            return 1;
        }

        return 0;
    }
	
    public static function getShopCode($stock_transfer_id)
    {
        $data = DB::select("
            SELECT SQL_NO_CACHE
                `stock_transfer_number`,
				`from_shop`.`code` AS `from_shop_code`, `to_shop`.`code` AS `to_shop_code`
            FROM `stock_transfer` AS `st`
			LEFT JOIN `shop` `from_shop`
				ON `st`.`from_shop_id` = `from_shop`.`id`
			LEFT JOIN `shop` `to_shop`
				ON `st`.`to_shop_id` = `to_shop`.`id`
            WHERE `st`.`id` = '$stock_transfer_id'
			LIMIT 0, 1
        ");
        
        return $data[0];
    }
}