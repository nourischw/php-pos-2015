<?php

class Stock extends BaseModel
{
    const PAGE_LIMIT = 100;

    /**
     *  Get popup list
     *  @return array Record list
     */
    public static function getPopupList()
    {
        $page = (Input::has("page")) ? intval(Input::get("page")) : 1;
        $where_clause = null;

        if (Input::has("search_from_shop")) {
            $from_shop = intval(Input::get("search_from_shop"));
            $where_clause .= " AND `stk`.`shop_id` = '$from_shop' ";
        }

        if (Input::has("search_product_upc")) {
            $product_upc = Input::get("search_product_upc");
            $where_clause .= " AND `p`.`barcode` LIKE '%$product_upc%'";
        }

        if (Input::has("search_product_name")) {
            $product_name = Input::get("search_product_name");
            $where_clause .= " AND `p`.`name` LIKE '%$product_name%'";
        }

        if (Input::has("search_serial_number")) {
            $serial_number = Input::get("search_serial_number");
            $where_clause .= " AND `stk`.`serial_number` LIKE '%$serial_number%'";
        }

        $total_records_data = DB::select("
            SELECT SQL_NO_CACHE
                COUNT(*) AS `total_records`
            FROM `stock` AS `stk`
            LEFT JOIN `product` AS `p`
                ON `stk`.`product_id` = `p`.`id`
            WHERE `qty` > 0 $where_clause
        ");

		$total_records = $total_records_data[0]['total_records'];
        $total_pages = intval(ceil($total_records / self::PAGE_LIMIT));
        $start_record = ($page - 1) * self::PAGE_LIMIT;

        $list_data = null;
        if ($total_records > 0) {
	        $list_data = DB::select("
	            SELECT SQL_NO_CACHE
	                `stk`.`id` AS `stock_id`, `shp`.`id` AS `from_shop_id`, `shp`.`name` AS `from_shop`,
	                `stk`.`qty` AS `remain_qty`, `stk`.`serial_number`, `p`.`id` AS `product_id`,
					`p`.`barcode` AS `product_upc`, `p`.`name` AS `product_name`, `p`.`unit_price`
	            FROM `stock` AS `stk`
	            LEFT JOIN `shop` AS `shp`
	                ON `stk`.`shop_id` = `shp`.`id`
	            LEFT JOIN `product` AS `p`
	                ON `stk`.`product_id` = `p`.`id`
	            WHERE `qty` > 0 $where_clause
	            ORDER BY `product_upc`
	            LIMIT $start_record, " . self::PAGE_LIMIT
	        );
	    }

        return array(
            'list_data' => $list_data,
            'page' => $page,
			'total_records' => $total_records,
            'total_pages' => $total_pages
        );
    }

    public static function getList()
    {
        $page = (Input::has("page")) ? intval(Input::get("page")) : 1;
        $where_clause = null;

        if (Input::has("search_shop")) {
            $shop = intval(Input::get("search_shop"));
            $where_clause .= " AND `stk`.`shop_id` = '$shop' ";
        }

        if (Input::has("search_product_upc")) {
            $product_upc = Input::get("search_product_upc");
            $where_clause .= " AND `p`.`barcode` LIKE '%$product_upc%'";
        }

        if (Input::has("search_product_name")) {
            $product_name = Input::get("search_product_name");
            $where_clause .= " AND `p`.`name` LIKE '%$product_name%'";
        }

        if (Input::has("search_serial_number")) {
            $serial_number = Input::get("search_serial_number");
            $where_clause .= " AND `stk`.`serial_number` LIKE '%$serial_number%'";
        }

        $total_records_data = DB::select("
            SELECT SQL_NO_CACHE
                COUNT(*) AS `total_records`
            FROM `stock` AS `stk`
            LEFT JOIN `product` AS `p`
                ON `stk`.`product_id` = `p`.`id`
            WHERE 1 $where_clause
        ");

		$total_records = $total_records_data[0]['total_records'];
        $total_pages = intval(ceil($total_records / self::PAGE_LIMIT));
        $start_record = ($page - 1) * self::PAGE_LIMIT;

        $list_data = null;
        if ($total_records > 0) {
	        $list_data = DB::select("
	            SELECT SQL_NO_CACHE
	                `stk`.`id` AS `stock_id`, `shp`.`code` AS `shop_code`, `stk`.`qty` AS `remain_qty`,
	                `stk`.`serial_number`, `p`.`id` AS `product_id`, `p`.`barcode` AS `product_upc`,
	                `p`.`name` AS `product_name`, `p`.`unit_price`
	            FROM `stock` AS `stk`
	            LEFT JOIN `shop` AS `shp`
	                ON `stk`.`shop_id` = `shp`.`id`
	            LEFT JOIN `product` AS `p`
	                ON `stk`.`product_id` = `p`.`id`
	            WHERE 1 $where_clause
	            ORDER BY `stk`.`shop_id`
	            LIMIT $start_record, " . self::PAGE_LIMIT
	        );
	    }

        return array(
            'list_data' => $list_data,
            'page' => $page,
			'total_records' => $total_records,
            'total_pages' => $total_pages
        );
    }

	public static function getQuickSearchItem($where_clause = null)
	{
		$shop_code = Input::get("quick_shop_code");
		$search_keyword = Input::get("quick_search");
		$search_type = Input::get("quick_status");

		$where_clause .= " AND `shp`.`code` = '$shop_code'";

        if ($search_type==="1") {
            $where_clause .= " AND `p`.`barcode` = '$search_keyword'";
        }

        if ($search_type==="2") {
             $where_clause .= " AND `stk`.`serial_number` = '$search_keyword'";
        }

		$item_data = DB::select("
			SELECT SQL_NO_CACHE
				`stk`.`id` AS `stock_id`, `shp`.`id` AS `from_shop_id`, `shp`.`name` AS `from_shop`,
				`stk`.`qty` AS `remain_qty`, `stk`.`serial_number`, `p`.`id` AS `product_id`,
				`p`.`barcode` AS `product_upc`, `p`.`name` AS `product_name`, `p`.`unit_price`
			FROM `stock` AS `stk`
			LEFT JOIN `shop` AS `shp`
				ON `stk`.`shop_id` = `shp`.`id`
			LEFT JOIN `product` AS `p`
				ON `stk`.`product_id` = `p`.`id`
			WHERE `qty` > 0 $where_clause
			LIMIT 1"
		);
        return $item_data;
	}

	public static function validateStockTransferItems($from_shop, $item_ids)
	{
		$total_items = count($item_ids);
		$all_item_ids = implode(",", $item_ids);
		$stock_data = DB::select("
			SELECT SQL_NO_CACHE
				COUNT(*) AS `total_valid_items`
			FROM `stock`
			WHERE
				`id` IN ($all_item_ids)
				AND `shop_id` = '$from_shop'
		");

		if ($stock_data[0]['total_valid_items'] != $total_items) {
			return false;
		}

		return true;
	}

	public static function validateStockWithdrawItems($item_ids)
	{
		$total_items = count($item_ids);
		$all_item_ids = implode(",", $item_ids);
		$stock_data = DB::select("
			SELECT SQL_NO_CACHE COUNT(*) AS `valid_items`
			FROM `stock`
			WHERE
				`id` IN ($all_item_ids)
				AND `shop_id` = '11'
			LIMIT 0, 1
		");

		if (empty($stock_data)) {
			return false;
		}

		return true;
	}

	public static function InsertStock($stock_params)
	{
        extract($stock_params);
        $new_items = array();
        $shop_code = Shop::getShopCode($stock_values[0]["goods_in_to"]);

        $i = 0;
        foreach ($stock_values as $value) {
            extract($value);
    		$data = DB::insert("
				INSERT INTO `stock` (
					`product_id`,
					`shop_id`,
					`qty`,
					`serial_number`,
					`repair`,
					`status`,
					`create_time`,
					`last_update_by`,
					`last_update`
				) VALUES (
                    '$product_id',
                    '$goods_in_to',
                    '$qty',
                    '$serial_number',
                    '0',
                    '',
                    NOW(),
                    '$staff_name',
                    NOW()
                )
			");

            $log_params = array();

    		if ($data) {
                $log_params[$i] = [
                    "type" => StockTransportLog::STOCK_TRANSPORT_TYPE_GOODS_IN,
                    "stock_id" => DB::getPdo()->lastInsertId(),
                    "desc" => "[Goods In ID.: $gi_id], goods in [$qty qty] from [Supplier Code: $supplier_code] to [Shop Code: $shop_code]"
                ];
                $i++;
    		}

            if (!empty($log_params)) {
                StockTransportLog::createRecord($log_params);
            }
        }

        return true;
	//	return false;
	}

	public static function checkHaveInvalidItem($stock_items)
	{
		$item_ids = implode(',',array_keys($stock_items));
		$data = DB::select("
			SELECT SQL_NO_CACHE
				`id`, `qty`
			FROM `stock`
			WHERE `id` IN ($item_ids);
		");

		$invalid_items = array();
		$i = 0;
		foreach($data as $item) {
			if ($stock_items[$item['id']] > $item['qty']) {
				$invalid_items[$i] = array(
					'id' => $item['id'],
					'remain_qty' => $item['qty']
				);
				$i++;
			}
		}

		if (sizeof($invalid_items) > 0) {
			return $invalid_items;
		}

		return null;
	}

	public static function updateStockQty($po_id)
	{
		$staff_code = Session::get('staff_code');
		$ship_to_shop = PurchaseOrder::getShipToShop($po_id);
		$po_items = PurchaseOrderItems::getStockItems($po_id);

		foreach ($po_items as $item) {
			extract($item);

			// Add stock qty one by one if the product have serial number
			if ($have_serial_number) {
				$values = array();
				for ($i = 0; $i < $qty; $i++) {
					$values[$i] = "('$po_id', $product_id', '$ship_to_shop', '1', '$staff_code', NOW(), NOW())";
				}
				$values = implode(', ', $values);
				DB::insert("
					INSERT INTO `stock` (
						`purchase_order_id`, `product_id`, `shop_id`, `qty`, `last_update_by`, `last_update`,
						`create_time`
					) VALUES $values
				");
			}

			else {
				// Check whether the stock item is already exist in this shop
				$have_stock_record = self::checkHaveRecord($ship_to_shop, $product_id);
				if ($have_stock_record) {
					DB::update("
						UPDATE `stock`
						SET
							`qty` = `qty` + '$qty',
							`last_update_by` = '$staff_code',
							`last_update` = NOW()
						WHERE
							`product_id` = '$product_id'
							AND `serial_number` = ''
							AND `shop_id` = '$ship_to_shop'
						LIMIT 1
					");
				}

				// No stock record found from the current shop
				else {
					DB::insert("
						INSERT INTO `stock`
						SET
							`product_id` = '$product_id',
							`shop_id` = '$ship_to_shop',
							`qty` = '$qty',
							`create_time` = NOW(),
							`last_update_by` = '$staff_code',
							`last_update` = NOW()
					");
				}
			}
		}
	}

	public static function checkHaveRecord($shop_id, $product_id)
	{
		$data = DB::select("
			SELECT SQL_NO_CACHE
				COUNT(*) AS `have_stock_record`
			FROM `stock`
			WHERE
				`product_id` = '$product_id'
				AND `shop_id` = '$shop_id'
		");

		if ($data[0]['have_stock_record'] > 0) {
			return true;
		}

		return false;
	}

    public static function holdStockTransferItems($stock_transfer_id)
    {
        $stock_transfer_data = StockTransfer::get($stock_transfer_id);
		$from_shop_id = $stock_transfer_data['from_shop_id'];
        $stock_transfer_items = StockTransferItems::get($stock_transfer_id);

        foreach ($stock_transfer_items as $item) {
			$hold_qty = $item['qty'];
			$stock_id = $item['stock_id'];
			DB::update("
                UPDATE `stock`
                SET `qty` = `qty` - $hold_qty
                WHERE
                    `id` = '$stock_id'
                    AND `shop_id` = '$from_shop_id'
                LIMIT 1
            ");
        }
    }

    public static function holdStockWithdrawItems($withdraw_items)
    {
    	foreach ($withdraw_items as $item) {
    		$withdraw_qty = $item['qty'];
    		$stock_id = $item['stock_id'];
    		DB::update("
    			UPDATE `stock`
    			SET `qty` = `qty` - $withdraw_qty
    			WHERE
    				`id` = '$stock_id'
    				AND `shop_id` = '11'
    			LIMIT 1
    		");
    	}
    }

	public static function performStockTransfer($stock_transfer_id)
	{
		$stock_transfer_data = StockTransfer::get($stock_transfer_id);
		$stock_transfer_items_data = StockTransferItems::get($stock_transfer_id);
		$to_shop_id = $stock_transfer_data['to_shop_id'];

		foreach ($stock_transfer_items_data as $value) {
			extract($value);
			// Check stock details
			$stock_data = DB::select("
				SELECT SQL_NO_CACHE
					`product_id`, `qty` AS `product_qty`, `serial_number`
				FROM `stock`
				WHERE `id` = '$stock_id'
				LIMIT 0, 1
			");

			extract($stock_data[0]);

			// Update the shop ID if the old record qty is as same as the transfer qty
			if (!empty($serial_number)) {
				DB::update("
					UPDATE `stock`
					SET
						`shop_id` = '$to_shop_id',
						`qty` = '$qty'
					WHERE `id` = '$stock_id'
					LIMIT 1
				");
			}

			else {
				// Check whether already have old record
				$have_record = DB::select("
					SELECT SQL_NO_CACHE `id`
					FROM `stock`
					WHERE
						`product_id` = '$product_id'
						AND `serial_number` IS NULL
						AND `shop_id` = '$to_shop_id'
					LIMIT 0, 1
				");

				// Create new record if old record is not exist
				if (empty($have_record)) {
					DB::insert("
						INSERT INTO `stock`
						SET
							`product_id` = '$product_id',
							`shop_id` = '$to_shop_id',
							`qty` = '$qty',
							`last_update_by` = '" . Session::get('staff_id') . "',
							`last_update` = NOW(),
							`create_time` = NOW()
					");
				}

				// Update old record if exist
				else {
					$old_record_id = $have_record[0]['id'];

					DB::update("
						UPDATE `stock`
						SET	`qty` = `qty` + $qty
						WHERE `id` = '$old_record_id'
						LIMIT 1
					");
				}
			}
		}
	}

	public static function releaseHoldingStockItems($stock_transfer_id)
	{
        $stock_transfer_data = StockTransfer::get($stock_transfer_id);
		$from_shop_id = $stock_transfer_data['from_shop_id'];
        $stock_transfer_items = StockTransferItems::get($stock_transfer_id);

        foreach ($stock_transfer_items as $item) {
			$hold_qty = $item['qty'];
			$stock_id = $item['stock_id'];
			DB::update("
                UPDATE `stock`
                SET `qty` = `qty` + $hold_qty
                WHERE
                    `id` = '$stock_id'
                    AND `shop_id` = '$from_shop_id'
                LIMIT 1
            ");
        }
	}

	public static function releaseHoldingWithdrawItems($stock_withdraw_id)
	{
        $stock_withdraw_data = StockWithdraw::get($stock_withdraw_id);
        $stock_withdraw_items = StockWithdrawItems::get($stock_withdraw_id);

        foreach ($stock_withdraw_items as $item) {
			$hold_qty = $item['qty'];
			$stock_id = $item['stock_id'];
			DB::update("
                UPDATE `stock`
                SET `qty` = `qty` + $hold_qty
                WHERE `id` = '$stock_id'
                LIMIT 1
            ");
        }
	}

	public static function getStockLevel($product_id)
	{
		$data = DB::select("
			SELECT SQL_NO_CACHE
				`s`.`code`, sum(`stk`.`qty`) as `qty`
			FROM `stock` AS `stk`
			LEFT JOIN `shop` AS `s`
				ON `stk`.`shop_id` = `s`.`id`
			WHERE `stk`.`product_id` = '$product_id'
			GROUP BY `s`.`code`
			ORDER BY `s`.`id`
		");
		$total_shop_records = sizeof($data);
		return array(
			'list_data' => $data,
			'total_shop_records' => $total_shop_records,
			'have_record' => ($total_shop_records > 0) ? true : false
		);
	}

	public static function getStockData($stock_id)
	{
		$data = DB::select("
			SELECT
				`p`.`barcode` AS `product_upc`, `p`.`name` AS `product_name`, `s`.`serial_number`
			FROM `stock` AS `s`
			LEFT JOIN `product` AS `p`
				ON `s`.`product_id` = `p`.`id`
			WHERE `s`.`id` = '$stock_id'
			LIMIT 1
		");

		return $data[0];
	}

	public static function getSNList()
	{
		$page = (Input::has("page")) ? intval(Input::get("page")) : 1;
		$where_clause = null;

		if (Input::has("search_shop_id")) {
			$shop_id = intval(Input::get("search_shop_id"));
			$where_clause .= " AND `s`.`shop_id` = '$shop_id' ";
		}

		if (Input::has("search_product_barcode")) {
			$product_barcode = Input::get("search_product_barcode");
			$where_clause .= " AND `p`.`barcode` LIKE '%$product_barcode%' ";
		}

		if (Input::has("search_product_name")) {
			$product_name = Input::get("search_product_name");
			$where_clause .= " AND `p`.`name` LIKE '%$product_name%' ";
		}

		if (Input::has("search_serial_number")) {
			$serial_number = Input::get("search_serial_number");
			$where_clause .= " AND `s`.`serial_number` LIKE '%$serial_number%' ";
		}

		$total_records_data = DB::select("
			SELECT SQL_NO_CACHE
				COUNT(*) AS `total_records`
			FROM `stock` AS `s`
            LEFT JOIN `shop` AS `shp`
                ON `s`.`shop_id` = `shp`.`id`
            LEFT JOIN `product` AS `p`
                ON `s`.`product_id` = `p`.`id`
            WHERE `s`.`serial_number` != '' $where_clause
		");

		$total_records = $total_records_data[0]['total_records'];
        $total_pages = intval(ceil($total_records / self::PAGE_LIMIT));
        $start_record = ($page - 1) * self::PAGE_LIMIT;

        $list_data = null;
        if ($total_records > 0) {
			$list_data = DB::select("
				SELECT SQL_NO_CACHE
					`s`.`id`, `shp`.`code` AS `shop_code`, `p`.`barcode` AS `product_barcode`,
					`p`.`name` AS `product_name`, `s`.`serial_number`
				FROM `stock` AS `s`
				LEFT JOIN `shop` AS `shp`
					ON `shp`.`id` = `s`.`shop_id`
				LEFT JOIN `product` AS `p`
					ON `p`.`id` = `s`.`product_id`
				WHERE `s`.`serial_number` != '' $where_clause
				ORDER BY `shop_code`
				LIMIT $start_record, " . self::PAGE_LIMIT
			);
		}

        return array(
            'list_data' => $list_data,
            'page' => $page,
			'total_records' => $total_records,
            'total_pages' => $total_pages
        );
	}

	public static function updateSerialNumber()
	{
        $rules = array(
            'stock_id' => 'required|integer',
            'new_serial_number' => 'required'
        );

        $v = Validator::make(Input::all(), $rules);
        if ($v->fails()) {
            return 0;
        }

		$stock_id = intval(Input::get("stock_id"));
		$new_serial_number = parent::escape(Input::get("new_serial_number"));

		return DB::update("
			UPDATE `stock`
			SET `serial_number` = $new_serial_number
			WHERE `id` = '$stock_id'
			LIMIT 1
		");
	}

	public static function revokeQty($shop_id, $product_id, $qty, $serial_number = '')
	{
		$where_clause = "";
		if (!empty($serial_number)) {
			$where_clause = " AND `serial_number` = '".$serial_number."'";
		}
		$result =   $result = DB::update("
			UPDATE `stock`
			SET `qty` = `qty` + '$qty'
			WHERE
				`product_id` = '$product_id'
				AND `shop_id` = '$shop_id'
				$where_clause
	  ");
		return $result;
	}
}
