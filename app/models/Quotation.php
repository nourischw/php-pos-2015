<?php

class Quotation extends BaseModel
{
    const ORDER_CODE = "QM";
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

        if (Input::has("search_quote_type")) {
            $quote_type = intval(input::get("search_quote_type"));
            $where_clause .= " AND `quote_type` = '$quote_type'";
        }

        if (Input::has("search_quote_terms")) {
            $quote_terms = intval(Input::get("search_quote_terms"));
            $where_clause .= " AND `quote_terms` = '$quote_terms'";
        }

        if (Input::has("search_quotation_number")) {
            $quotation_number = Input::get("search_quotation_number");
            $where_clause .= " AND `quotation_number` LIKE '%$quotation_number%'";
        }

        if (Input::has("search_from_date")) {
            $from_date = parent::escape(Input::get("search_from_date"));
            $where_clause .= " AND `quote_date` >= $from_date";
        }

        if (Input::has("search_to_date")) {
            $to_date = parent::escape(Input::get("search_to_date"));
            $where_clause .= " AND `quote_date` <= $to_date";
        }

        $total_records_data = DB::select("
            SELECT SQL_NO_CACHE
                COUNT(*) AS `total_records`
            FROM `quotation`
            WHERE 1 $where_clause
        ");

        $total_records = $total_records_data[0]['total_records'];
        $total_pages = intval(ceil($total_records / self::PAGE_LIMIT));
        $start_record = ($page - 1) * self::PAGE_LIMIT;

        $list_data = null;
        if ($total_records > 0) {
            $list_data = DB::select("
                SELECT SQL_NO_CACHE
                    `id`, `quotation_number`, `quote_date`, `quote_type`, `quote_terms`, `total_items`, 
                    `total_qty`, `total_amount`, `discount_amount`, `sub_total_amount`, `remarks`, `comment`
                FROM `quotation`
                WHERE 1 $where_clause
                ORDER BY `id` DESC
                LIMIT $start_record, " . self::PAGE_LIMIT
            );
        }
        
        return array(
            'status' => $status,
            'quote_type' => Config::get("quote_type"),
            'quote_terms' => Config::get("cod_terms"),
            'list_data' => $list_data,
            'page' => $page,
            'total_records' => $total_records,
            'total_pages' => $total_pages
        );
    }

    public static function getPopupList()
    {
        $page = (Input::has("page")) ? intval(Input::get("page")) : 1;
        $where_clause = null;

        if (Input::has("search_quotation_number")) {
            $quotation_number = Input::get("search_quotation_number");
            $where_clause .= " AND `quotation_number` LIKE '%$quotation_number%'";
        }

        $total_records_data = DB::select("
            SELECT SQL_NO_CACHE COUNT(*) AS `total_records`
            FROM `quotation`
            WHERE 1 $where_clause
        ");

        $total_records = $total_records_data[0]['total_records'];
        $total_pages = intval(ceil($total_records / self::PAGE_LIMIT));
        $start_record = ($page - 1) * self::PAGE_LIMIT;

        $list_data = DB::select("
            SELECT SQL_NO_CACHE
                `Q`.`id`,  `Q`.`quotation_number`,  `Q`.`quote_date`,  `Q`.`quote_type`,  `Q`.`quote_terms`,  `Q`.`total_items`, 
                `Q`.`total_qty`,  `Q`.`total_amount`,  `Q`.`discount_amount`,  `Q`.`sub_total_amount`, 
				`S`.`code`
            FROM `quotation` AS `Q`
			LEFT JOIN `shop` AS `S`
				ON `Q`.`shop_id` = `S`.`id`
            WHERE 1 $where_clause
				AND `status` = " . self::STATUS_NORMAL . "
            ORDER BY `quote_date`
            LIMIT $start_record, " . self::PAGE_LIMIT
        );

        return array(
            'quote_type' => Config::get("quote_type"),
            'quote_terms' => Config::get("payment_terms"),
            'list_data' => $list_data,
            'total_records' => $total_records,
            'total_pages' => $total_pages
        );
    }

    /**
     *  Get record
     *  @param integer $id Record ID
     *  @return array Record data
     */
    public static function get($id)
    {
        $id = intval($id);
        $data = DB::select("
            SELECT SQL_NO_CACHE
                `q`.*, `s`.`code` AS `shop_code`, `s`.`address` AS `shop_address`, 
                `s`.`telephone` AS `shop_telephone`, `s`.`fax` AS `shop_fax`, `staff_code`,
                `sf`.`title` AS `staff_title`, `sf`.`telephone` AS `staff_telephone`, 
                `sf`.`mobile` AS `staff_mobile`, `sf`.`email` AS `staff_email`
            FROM `quotation` AS `q`
            LEFT JOIN `shop` AS `s`
                ON `q`.`shop_id` = `s`.`id`
            LEFT JOIN `staff` AS `sf`
                ON `q`.`staff_id` = `sf`.`id`
            WHERE `q`.`id` = '$id'
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
    public static function edit($record_type)
    {
        $fail_result = array("success" => false);
        $record_id = (Input::has("record_id")) ? intval(Input::get("record_id")) : 0;
        $is_update = ($record_type === Config::get('edit_type.UPDATE')) ? true : false;

		// Validate the form fields
		$rules = array(
            'quote_date' => 'required|date_format:Y-m-d',
            'quote_type' => 'required|integer',
            'quote_terms' => 'required|integer',
            'staff_id' => 'required|integer',
			'discount_amount' => 'required|numeric'
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

        $rules = array(
            'product_id' => 'required|integer',
            'qty' => 'required|integer',
            'unit_price' => 'required|numeric'
        );

        // Create new purchase order
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

        // Update old purchase order
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

        $shop_id = Session::get('shop_id');
        $quote_date = parent::escape(Input::get("quote_date"));
        $quote_type = intval(Input::get("quote_type"));
        $quote_terms = intval(Input::get("quote_terms"));
        $staff_id = intval(Input::get("staff_id"));
        $discount_amount = floatval(Input::get("discount_amount"));
        $comment = parent::escape(Input::get("comment"));
        $remarks = parent::escape(Input::get("remarks"));
        
		if ($discount_amount < 0.00) {
			return $fail_result;
		}
		
        $sub_total_amount = $total_amount;
        if ($discount_amount > 0.00) {
			if ($discount_amount > $sub_total_amount) {
				return $fail_result;
			}
            $sub_total_amount -= $discount_amount;
        }
		
        $base_query = "
            `quotation`
            SET
                `quote_date` = $quote_date,
                `quote_type` = '$quote_type',
                `quote_terms` = '$quote_terms',
                `staff_id` = '$staff_id',
                `total_items` = '$total_items',
                `total_qty` = '$total_qty',
                `total_amount` = '$total_amount',
                `discount_amount` = '$discount_amount',
                `sub_total_amount` = '$sub_total_amount',
                `comment` = $comment,
                `remarks` = $remarks,
                `last_update` = NOW(),
                `last_update_by` = '" . Session::get('staff_code') . "'
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
                'quotation_id' => $record_id,
                'removed_items' => $removedItems,
                'old_items' => $oldItems,
                'new_items' => $newItems,
            );

            $new_item_ids = QuotationItems::edit($params);
            return array(
                "success" => true,
                "new_item_ids" => $new_item_ids
            );
        } 

        // Create record
        else {
            $shop_code = Session::get('shop_code');
            $shop_id = Shop::getID($shop_code);
            $create_by = Session::get('staff_code');
            
            $data = DB::select("
                SELECT MAX(`receipt_id`) + 1 AS `receipt_id` 
                FROM `quotation`
            ");
            
            extract($data[0]);
            if (empty($receipt_id)) {
                $receipt_id = 1;
            }

            $quotation_number = self::ORDER_CODE . date("y") . "-0021-" . sprintf("%05d", $receipt_id);
            $result = DB::insert("
                INSERT INTO 
                    $base_query,
                    `shop_id` = '$shop_id',
                    `receipt_id` = '$receipt_id',
                    `create_time` = NOW(),
                    `create_by` = '$create_by', 
                    `quotation_number` = '$quotation_number'
            ");

            $new_id = DB::getPdo()->lastInsertId();
            $new_quotation_item_id = QuotationItems::createRecord($new_id, $items);
            if (!$new_quotation_item_id) {
                return $fail_result;
            }

            return array(
                'success' => true,
                'new_id' => $new_id,
                'quotation_number' => $quotation_number,
				'quotation_item_id'	=> $new_quotation_item_id,
            );
        }
    }

    /**
     *  Delete record
     *  @param array $all_ids Record IDs
     *  @return integer Delete result
     */
    public static function deleteRecord($all_ids)
    {
        $total_items = sizeof($all_ids);
        $all_ids = implode(",", $all_ids);
        $result = DB::delete("
            DELETE FROM `quotation`
            WHERE `id` IN ($all_ids)
            LIMIT $total_items
        ");

        if ($result) {
            return 1;
        }
        return 0;
    }

    /**
     *  Update status
     *  @return integer update status result
     */
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

        $record_id = Input::get("record_id");
        $record_id = explode(',', $record_id);
        $total_ids = sizeof($record_id);
        $ids = array();
        foreach ($record_id as $id) {
            $ids[] = intval($id);
        }
        $record_id = implode(',', $ids);
        $result = DB::update("
            UPDATE `quotation`
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
	
    public static function updateQuotationStatus($record_id)
    {
		$data = DB::update("
            UPDATE `quotation`
            SET 
                `last_update` = NOW(),
                `last_update_by` = '" . Session::get("staff_code") . "',
                `status` = '2'
            WHERE `id` = '$record_id'
            LIMIT 1
        ");
	}
	
	public static function getQuotationNumber($record_id)
	{
		$data = DB::select("
			SELECT `quotation_number`
			FROM `quotation`
			WHERE `id` = $record_id
		");
		if($data){
			return $data[0]['quotation_number'];
		}
		return 0;
	}

}
