<?php
class PurchaseOrder extends BaseModel
{
    const ORDER_CODE = "PO";
    const PAGE_LIMIT = 20;

    const STATUS_CONFIRMED = 1;
    const STATUS_PENDING = 2;
    const STATUS_VOIDED = 3;
    const STATUS_FINISHED = 4;

    /**
     *  Get list
     *  @param integer $page Record page
     *  @return array Record list
     */
    public static function getList()
    {
        $page = (Input::has("page")) ? intval(Input::get("page")) : 1;
        $status = (Input::has("search_status")) ? intval(Input::get("search_status")) : 1;
        $where_clause = " AND `po`.`status` = '$status'";

        if (Input::has("search_purchase_order_number")) {
            $purchase_order_number = Input::get("search_purchase_order_number");
            $where_clause .= " AND `po`.`purchase_order_number` LIKE '%$purchase_order_number%'";
        }

        if (Input::has("search_shop")) {
            $shop = intval(Input::get("search_shop"));
            $where_clause .= " AND `issue_shop`.`id` = '$shop'";
        }

        if (Input::has("search_from_date")) {
            $from_date = parent::escape(Input::get("search_from_date"));
            $where_clause .= " AND DATE(`po`.`create_time`) >= $from_date";
        }

        if (Input::get("search_to_date")) {
            $to_date = parent::escape(Input::get("search_to_date"));
            $where_clause .= " AND DATE(`po`.`create_time`) <= $to_date";
        }

        $total_records_data = DB::select("
            SELECT SQL_NO_CACHE
                COUNT(*) AS `total_records`
            FROM `purchase_order` AS `po`
            LEFT JOIN `shop` `issue_shop`
                ON `po`.`shop_id` = `issue_shop`.`id`
            WHERE 1 $where_clause
        ");

        $total_records = $total_records_data[0]['total_records'];
        $total_pages = intval(ceil($total_records / self::PAGE_LIMIT));
        $start_record = ($page - 1) * self::PAGE_LIMIT;

        $list_data = null;
        if ($total_records > 0) {
            $list_data = DB::select("
                SELECT SQL_NO_CACHE
                    `po`.`id`, `issue_shop`.`code` AS `shop_code`, `ship_to_shop`.`code` AS `ship_to`,
                    `po`.`purchase_order_number`, `po`.`create_time`, `po`.`request_by`, `po`.`total_items`,
                    `po`.`total_qty`, `po`.`total_amount`, `po`.`discount_amount`, `po`.`net_amount`
                FROM `purchase_order` AS `po`
                LEFT JOIN `shop` `issue_shop`
                    ON `po`.`shop_id` = `issue_shop`.`id`
                LEFT JOIN `shop` `ship_to_shop`
                    ON `po`.`ship_to` = `ship_to_shop`.`id`
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
        if (Input::has("search_purchase_order_code")) {
            $purchase_order_code = Input::get("search_purchase_order_code");
            $where_clause .= " AND `purchase_order_number` LIKE '%$purchase_order_code%'";
        }
        $total_records_data = DB::select("
            SELECT SQL_NO_CACHE
                COUNT(*) AS `total_records`
            FROM `purchase_order`
            WHERE 1 $where_clause
        ");
		$total_records = $total_records_data[0]['total_records'];
        $total_pages = intval(ceil($total_records / self::PAGE_LIMIT));
        $start_record = ($page - 1) * self::PAGE_LIMIT;

        $list_data = DB::select("
            SELECT SQL_NO_CACHE
                `PO`.*, `SP`.code AS supplier_code, `SP`.id AS supplier_id,
				`SP`.name AS supplier_name, `SP`.mobile AS supplier_mobile,
				`SP`.fax AS supplier_fax, `SP`.email AS supplier_email,
				`S`.code AS shop_code
            FROM `purchase_order` AS PO
			LEFT JOIN `supplier` AS `SP`
			ON `SP`.id = `PO`.supplier_id
			LEFT JOIN `shop` AS `S`
			ON `S`.id = `PO`.ship_to
            WHERE 1 $where_clause
			AND `status` = ". self::STATUS_CONFIRMED . "
            ORDER BY `purchase_order_number`
            LIMIT $start_record, " . self::PAGE_LIMIT
        );

        return array(
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
        $data = DB::select("
            SELECT SQL_NO_CACHE
                `po`.*, `s`.`code` AS `shop_code`, `s`.`address` AS `shop_address`,
                `s`.`telephone` AS `shop_telephone`, `s`.`fax` AS `shop_fax`,
                `sup`.`id` AS `supplier_id`, `sup`.`code` AS `supplier_code`,
                `sup`.`name` AS `supplier_name`, `sup`.`mobile` AS `supplier_mobile`,
                `sup`.`fax` AS `supplier_fax`, `sup`.`email` AS `supplier_email`
            FROM `purchase_order` AS `po`
            LEFT JOIN `shop` AS `s`
                ON `po`.`shop_id` = `s`.`id`
            LEFT JOIN `supplier` AS `sup`
                ON `po`.`supplier_id` = `sup`.`id`
            WHERE `po`.`id` =  '$id'
        ");

        if (empty($data)) {
            return null;
        }

        $data[0]['ship_to_shop'] = Shop::getShopCode($data[0]['ship_to']);
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
            'discount_amount' => 'required|numeric',
            'order_date' => 'required|date_format:Y-m-d',
            'staff_code' => 'required',
            'ship_to' => 'required|integer',
            'request_by' => 'required',
            'payment_type' => 'required',
            'supplier_id' => 'required|integer',
            'status' => 'required|integer|between:1,2'
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

        $POItems = null;
        $removedPOItems = null;
        $oldPOItems = null;
        $newPOItems = null;
        $rules = array(
            'product_id' => 'required|integer',
            'qty' => 'required|integer',
            'unit_price' => 'required|numeric'
        );

        // Create new purchase order
        if (!$is_update) {
            $POItems = Input::get("POItems");
            if ($POItems != null) {
                $result = Product::validateItems($POItems, $rules);
                if ($result === null) {
                    return $fail_result;
                } else {
                    extract($result);
                }
            }
        }

        // Update old purchase order
        else {
            $removedPOItems = Input::get("removedPOItems");
            $oldPOItems = Input::get("oldPOItems");
            $newPOItems = Input::get("newPOItems");

            if ($oldPOItems != null) {
                $old_item_rules = array(
                    'record_id' => 'required|integer',
                    'qty' => 'required|integer',
                    'unit_price' => 'required|numeric'
                );

                $result = Product::validateItems($oldPOItems, $old_item_rules);
                if ($result === null) {
                    return $fail_result;
                } else {
                    extract($result);
                }
            }

            if ($newPOItems != null) {
                $result = Product::validateItems($newPOItems, $rules);
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
        $discount_amount = floatval(Input::get("discount_amount"));
        $order_date = parent::escape(Input::get("order_date"));
        $staff_code = parent::escape(Input::get("staff_code"));
        $deposit_no = parent::escape(Input::get("deposit_no"));
        $ship_to = intval(Input::get("ship_to"));
        $request_by = parent::escape(Input::get("request_by"));
        $payment_type = parent::escape(Input::get("payment_type"));
        $supplier_id = intval(Input::get("supplier_id"));
        $remarks = parent::escape(Input::get("remarks"));
        $status = intval(Input::get("status"));

        if ($discount_amount < 0.00) {
            return $fail_result;
        }

        $net_amount = $total_amount;
        if ($discount_amount > 0.00) {
            if ($discount_amount > $net_amount) {
                return $fail_result;
            }
            $net_amount -= $discount_amount;
        }

        $base_query = "
            `purchase_order`
            SET
                `shop_id` = '$shop_id',
                `order_date` = $order_date,
                `supplier_id` = '$supplier_id',
                `staff_code` = $staff_code,
                `deposit_no` = $deposit_no,
                `request_by` = $request_by,
                `ship_to` = '$ship_to',
                `payment_type` = $payment_type,
                `total_items` = '$total_items',
                `total_qty` = '$total_qty',
                `total_amount` = '$total_amount',
                `discount_amount` = '$discount_amount',
                `net_amount` = '$net_amount',
                `remarks` = $remarks,
                `last_update` = NOW(),
                `last_update_by` = '" . Session::get('staff_code') . "',
                `status` = '$status'
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
                'po_id' => $record_id,
                'removed_items' => $removedPOItems,
                'old_items' => $oldPOItems,
                'new_items' => $newPOItems,
            );

            $new_item_ids = PurchaseOrderItems::edit($params);
            return array(
                "success" => true,
                "new_item_ids" => $new_item_ids
            );
        }

        // Create record
        else {
            $shop_code = Session::get('shop_code');
            $shop_id = Shop::getID($shop_code);

            $data = DB::select("
                SELECT MAX(`receipt_id`) + 1 AS `receipt_id`
                FROM `purchase_order`
                WHERE `shop_id` = '$shop_id'
            ");

            extract($data[0]);
            if (empty($receipt_id)) {
                $receipt_id = 1;
            }

            $purchase_order_number = self::ORDER_CODE . date("y") . "-" . Session::get('shop_syb') . "-" . sprintf("%05d", $receipt_id);
            $result = DB::insert("
                INSERT INTO
                    $base_query,
                    `receipt_id` = '$receipt_id',
                    `create_time` = NOW(),
                    `purchase_order_number` = '" . strtoupper($purchase_order_number) . "'
            ");

            $new_id = DB::getPdo()->lastInsertId();
            $new_purchase_order_item_id = PurchaseOrderItems::createRecord($new_id, $POItems);
            if (!$new_purchase_order_item_id) {
                return 0;
            }

            return array(
                'new_id' => $new_id,
                'purchase_order_number' => $purchase_order_number,
                'purchase_order_item_id' => $new_purchase_order_item_id,
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
            DELETE FROM `purchase_order`
            WHERE
                `id` IN ($all_ids)
                AND `status` = '2'
            LIMIT $total_items
        ");
        
        if ($result) {
            return 1;
        }
        return 0;
    }

    /**
     *  Update status
     *  @return integer Delete result
     */
    public static function updateStatus()
    {
        $status = intval(Input::get("status"));
        $valid_status = array(
            self::STATUS_CONFIRMED,
            self::STATUS_VOIDED,
            self::STATUS_FINISHED
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

        if ($status === self::STATUS_FINISHED) {
            $deliver_by = parent::escape(Input::get("deliver_by"));
        }

        $result = DB::update("
            UPDATE `purchase_order`
            SET
                `status` = '$status',
                `last_update` = NOW(),
                `last_update_by` = '" . Session::get("staff_code") . "'
                " .
            (($status === self::STATUS_FINISHED) ? ", `deliver_by` = $deliver_by " : null) .
            " WHERE
                `id` IN ($record_id) " .
            (($status === self::STATUS_FINISHED) ? " AND `status` = '1' AND `deliver_by` IS NULL " : null) .
            " LIMIT $total_ids
        ");

        if ($result) {
            return 1;
        }
        return 0;
    }

	public static function searchGoodsInPurchaseOrder()
	{
		$where_clause = null;
		$keyword = Input::get("keyword");
		$where_clause .= " AND `purchase_order_number` = '$keyword'";
		
		$data = DB::select("
            SELECT SQL_NO_CACHE
                `PO`.*, `SP`.code AS supplier_code, `SP`.id AS supplier_id,
				`SP`.name AS supplier_name, `SP`.mobile AS supplier_mobile,
				`SP`.fax AS supplier_fax, `SP`.email AS supplier_email,
				`S`.code AS shop_code
            FROM `purchase_order` AS PO
			LEFT JOIN `supplier` AS `SP`
			ON `SP`.id = `PO`.supplier_id
			LEFT JOIN `shop` AS `S`
			     ON `S`.id = `PO`.ship_to
            WHERE 1 $where_clause
			AND `status` = ". self::STATUS_CONFIRMED
        );
		return $data[0];
	}	

    /**
     *  Get PO ID
     *  @param string $po_number PO Number
     *  @return integer PO ID
     */
	public static function getPOID($po_number)
	{
		$po_number = parent::escape($po_number);
		$data = DB::select("
			SELECT SQL_NO_CACHE `id`
			FROM `purchase_order`
			WHERE `purchase_order_number` = $po_number
			LIMIT 0, 1
		");

		return $data[0]['id'];
	}

    private function GetNewRecieptID()
    {
        $data = DB::select("
            SELECT MAX(reciept_id) + 1 AS RECIEPT_ID
            FROM sales_order
            WHERE shop_id = '".$shopcode."'
        ");

        extract($data);
        return $RECIEPT_ID;
    }

    public static function getShipToShop($po_id)
    {
        $data = DB::select("
            SELECT SQL_NO_CACHE `ship_to`
            FROM `purchase_order`
            WHERE `id` = '$po_id'
            LIMIT 0, 1
        ");

        return $data[0]['ship_to'];
    }

}
