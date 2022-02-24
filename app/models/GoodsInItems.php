<?php

class GoodsInItems extends BaseModel
{
    /**
     * 	Create new order
     * 	@used-by (controller) ShoppingCartController
     * 	@used-by (controller) CheckoutProcessController
     * 	@param array $cart_params The shopping cart's parameters
     * 	@return array Array of order number
     */
    public static function insertGoodsInItems($params)
    {
		extract($params);
		$values = array();
		$po_item_data = PurchaseOrderItems::getGoodsInPurchaseOrderItems($po_id);

		$po_items = array();
		foreach ($po_item_data as $value) {
			extract($value);
			$po_items[$poi_id] = [
				"barcode"		=> $barcode,
				"unit_price" 	=> $unit_price
			];
		}

		$shop_code = Session::get("shop_code");
		$staff_id = Session::get("staff_id");
		$staff_name = Session::get("staff_name");

		foreach ($gi_items as $value) {
			extract($value);
			$po_item_id = intval($po_item_id);
			$serial_number = parent::escape($serial_number);
			$qty = intval($qty);
			$barcode = $po_items[$po_item_id]["barcode"];
			$actual_price = $po_items[$po_item_id]["unit_price"];
			$query = "(
				'$gi_id',
				'$barcode',
				$serial_number,
				'$qty',
				'$actual_price',
				'$supplier_id',
				'$supplier_code',
				'$invoice_number',
				 '$shop_code',
				'$staff_id',
				NOW(),
				'$staff_name'
			)";
			array_push($values, $query);
		}

		$values = implode(", ", $values);

		$result = DB::insert("
			INSERT INTO	`goods_in_items` (
				`gi_id`,
				`barcode`,
				`serial_number`,
				`qty`,
                `actual_price`,
				`supplier_id`,
				`supplier_code`,
				`invoice_number`,
				`shop_code`,
				`staff_id`,
				`update_time`,
				`update_by`
			) VALUES $values
		");

		if ($result) {
			return true;
		}

		return false;
	}

	public static function getGoodsInItems($gi_id) {
		$po_data = DB::select("
			SELECT SQL_NO_CACHE `po`.`purchase_order_id`
			FROM `goods_in` AS `gi`
			LEFT JOIN `purchase_order` AS `po`
				ON `po`.`purchase_order_number` = `gi`.`po_ref_no`
			WHERE `gi`.`gi_id` = '$gi_id'
			LIMIT 0, 1
		");

		$po_id = $po_data[0]['purchase_order_id'];

        $item_data = DB::select("
			SELECT SQL_NO_CACHE
            `gii`.`barcode`, `gii`.`serial_number`, `gii`.`qty`, `P`.`P_DETAIL` AS `product_name`, `gii`.`actual_price` AS `unit_price`
            FROM `goods_in_items` AS `gii`
            LEFT JOIN `product` AS `p`
            ON `gii`.`barcode` = `p`.`P_MOUNT`
            WHERE `gi_id` = '$gi_id'
		");

		$total_qty_data = DB::select("
			SELECT SQL_NO_CACHE
				SUM(`qty`) AS `total_qty`
			FROM `goods_in_items`
			WHERE `gi_id` = '$gi_id'
		");

		$total_qty = $total_qty_data[0]['total_qty'];

		$total_cost_data = DB::select("
			SELECT SQL_NO_CACHE
				SUM(`actual_price`) AS `total_cost`
			FROM `goods_in_items`
			WHERE `gi_id` = '$gi_id'
		");

		$total_cost = $total_cost_data[0]['total_cost'];

		return [
			"goods_in_items" => $item_data,
			"total_qty" => $total_qty,
			"total_cost" => $total_cost
		];
	}

    public static function getWithdrawPopupList()
    {
    	$page_limit = 50;
    	$supplier_id = intval(Input::get("search_supplier_id"));
        $page = (Input::has("page")) ? intval(Input::get("page")) : 1;
        $where_clause = null;

        if (Input::has("search_gi_code")) {
        	$gi_code = Input::get("search_gi_code");
        	$where_clause .= " AND `gi`.`sys_no` LIKE '%$gi_code%'";
        }

        if (Input::has("search_product_upc")) {
            $product_upc = Input::get("search_product_upc");
            $where_clause .= " AND `gii`.`barcode` LIKE '%$product_upc%'";
        }

        if (Input::has("search_product_name")) {
            $product_name = Input::get("search_product_name");
            $where_clause .= " AND `p`.`name` LIKE '%$product_name%'";
        }

        if (Input::has("search_serial_number")) {
            $serial_number = Input::get("search_serial_number");
            $where_clause .= " AND `gii`.`serial_number` LIKE '%$serial_number%'";
        }

        $total_records_data = DB::select("
            SELECT SQL_NO_CACHE
                COUNT(*) AS `total_records`
            FROM `goods_in_items` AS `gii`
            LEFT JOIN `goods_in` AS `gi`
            	ON `gii`.`gi_id` = `gi`.`id`
            LEFT JOIN `product` AS `p`
                ON `gii`.`product_id` = `p`.`id`
            LEFT JOIN `stock` AS `stk`
            	ON `stk`.`product_id` = `gii`.`product_id`
            	AND `stk`.`serial_number` = `gii`.`serial_number` COLLATE utf8_unicode_ci
            WHERE
            	 `gii`.`supplier_id` = '$supplier_id'
            	AND `stk`.`shop_id` = '11'
            	AND `stk`.`qty` > 0
            	$where_clause
            GROUP BY `stk`.`id`
        ");

		$total_records = $total_records_data[0]['total_records'];
        $total_pages = intval(ceil($total_records / $page_limit));
        $start_record = ($page - 1) * $page_limit;

        $list_data = null;
        if ($total_records > 0) {
	        $list_data = DB::select("
	            SELECT SQL_NO_CACHE
	                `gii`.`gi_id`, `gi`.`sys_no` AS `gi_code`, `stk`.`id` AS `stock_id`, `stk`.`qty` AS `remain_qty`, `gii`.`serial_number`, `gii`.`product_id`,
					`gii`.`barcode` AS `product_upc`, `p`.`name` AS `product_name`, `gii`.`actual_price`
	            FROM `goods_in_items` AS `gii`
	            LEFT JOIN `goods_in` AS `gi`
	            	ON `gii`.`gi_id` = `gi`.`id`
	            LEFT JOIN `product` AS `p`
	                ON `gii`.`product_id` = `p`.`id`
	            LEFT JOIN `stock` AS `stk`
	            	ON `stk`.`product_id` = `gii`.`product_id`
	            	AND `stk`.`serial_number` = `gii`.`serial_number` COLLATE utf8_unicode_ci
	            WHERE
	            	`gii`.`supplier_id` = '$supplier_id'
	            	AND `stk`.`shop_id` = '11'
	            	AND `stk`.`qty` > 0
	            	$where_clause
	            GROUP BY `stk`.`id`
	            ORDER BY `product_upc`
	            LIMIT $start_record, $page_limit"
	        );
	    }

        return array(
            'list_data' => $list_data,
            'page' => $page,
			'total_records' => $total_records,
            'total_pages' => $total_pages
        );
    }
}
