<?php

class GoodsIn extends BaseModel
{
	const GI_CODE = "GI";

    const STATUS_CONFIRMED = 1;
    const STATUS_PENDING = 2;
    const STATUS_VOIDED = 3;
    const STATUS_FINISHED = 4;

	const PAGE_LIMIT = 100;

    /**
     * 	Create new order
     * 	@used-by (controller) ShoppingCartController
     * 	@used-by (controller) CheckoutProcessController
     * 	@param array $cart_params The shopping cart's parameters
     * 	@return array Array of order number
     */
    public static function CreateGoodsInRecord($params)
    {
		extract($params);
		$supplier_id = intval($supplier_id);
		$invoice_no = $invoice_no;
		$invoice_date = $invoice_date;
		$po_number = (!empty($po_number)) ? $po_number : null;
		$consignment = $consignment;
		$remarks = $remarks;
		$goods_in_to = $goods_in_to;
		$po_code = explode("-", $po_number);
		$staff_name = Session::get("staff_name");
		$shop_code = Session::get("shop_code");
		$shop_syb = Shop::getShopSymbol($shop_code);

        $data = DB::select("
            SELECT MAX(seq_no)+1 AS receipt_id
            FROM goods_in
            WHERE goods_in_to = $goods_in_to
        ");

        extract($data[0]);

		if ($receipt_id === null){
			$receipt_id = '1';
		}

		$yy = date("y");

		$gi_no = self::GI_CODE.$yy."-".$shop_syb."-".sprintf("%05d", $receipt_id);
		$result = DB::insert("
			INSERT INTO `goods_in`
				SET
			`seq_no` = '$receipt_id',
			`sys_no` = '".strtoupper($gi_no)."',
			`supplier_id` = '$supplier_id',
			`invoice_no` = '$invoice_no',
			`po_id` = '$po_id',
			`po_ref_no` = '$po_number',
			`po_remark` = '$remarks',
			`goods_in_to` = '$goods_in_to',
			`consign` = '$consignment',
			`update_time` = NOW(),
			`update_by` = '$staff_name',
			`create_time` = NOW()
		");
		if ($result) {
			$id = DB::getPdo()->lastInsertId();
			return $id;
		}
		return 0;
	}

    public static function InsertGoodsInItems($params)
    {
		extract($params);
		$values = array();
		$stock_values = array();
		$po_item_data = PurchaseOrderItems::getGoodsInPurchaseOrderItems($po_id);
		$po_items = array();
		foreach ($po_item_data as $value) {
			extract($value);
			$po_items[$product_id] = [
				"barcode"		=> $product_upc,
				"unit_price" 	=> $product_unit_price,
				"get_product_id" 	=> $get_product_id
			];
		}
		$shop_code = Session::get("shop_code");
		$staff_id = Session::get("staff_id");
		$staff_name = Session::get("staff_name");
		$new_po_item = PurchaseOrderItems::getPurchaseOrderItemsID($po_id);
		$product_id_array = array();
		foreach ($gi_items as $key => $value) {
			extract($value);
			$qty = intval($qty);

			$po_item_id = intval($po_item_id);

			if($get_type == "load"){
				$new_po_item_id = $po_item_id;
				$barcode = $po_items[$new_po_item_id]["barcode"];
				$actual_price = $po_items[$new_po_item_id]["unit_price"] * $qty;
				$get_product_id = $po_items[$new_po_item_id]["get_product_id"];
			}

			if($get_type == "add"){
				$get_product_data = Product::getProductDetailData($create_product_id);
				extract($get_product_data);
				$barcode = $gi_barcooe;
				$actual_price = $create_unit_price * $qty;
				$get_product_id = $gi_product_id;
			}
			$product_item[$key] = [
				"product_id"		=> $get_product_id
			];

			$query = "(
				'$gi_id',
				'$get_product_id',
				'$barcode',
				'$serial_number',
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

			$stock_array = [
				"product_id" => $get_product_id,
				"goods_in_to" => $goods_in_to,
				"qty" => $qty,
				"serial_number" => $serial_number,
				"staff_name" => $staff_name
			];

			/*
			$stock_array = "(
				'$get_product_id',
				'$goods_in_to',
				'$qty',
				'$serial_number',
				'0',
				'',
				NOW(),
				'$staff_name',
				NOW()
			)";
			*/
			array_push($stock_values, $stock_array);
		}
	//	$stock_values = implode(", ", $stock_values);
		$values = implode(", ", $values);

		$stock_params = [
			"gi_id" => $gi_id,
			"supplier_code" => $supplier_code,
			"shop_code" => Shop::getShopCode($stock_values[0]["goods_in_to"]),
			"stock_values" => $stock_values
		];

		$insert_stock = Stock::InsertStock($stock_params);

		$result = DB::insert("
			INSERT INTO	`goods_in_items` (
				`gi_id`,
				`product_id`,
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

		$stock_params = [
			"product_id" 	=> $supplier_id,
		];

		if($result){
			$update_product_avg = Self::UpdateProductAverageCost($product_item);
			if($po_id != ""){
				$update_po_status = Self::UpdatePurchaseOrderStatus($po_id);
			}
			return true;
		}
		return false;
	}

	public static function getGoodsInRecord($gi_id)
	{
		$data = DB::select("
			SELECT SQL_NO_CACHE
				`s`.`name` AS `supplier_name`, `gi`.*,
				SUM(`gii`.`actual_price`) AS `total_cost`
			FROM `goods_in` AS `gi`
			LEFT JOIN `supplier` AS `s`
				ON `s`.`id` = `gi`.`supplier_id`
			LEFT JOIN `goods_in_items` AS `gii`
				ON `gii`.`gi_id` = `gi`.`id`
			WHERE `gii`.`gi_id` = $gi_id
		");

		return $data[0];
	}

	public static function UpdatePurchaseOrderStatus($po_id)
	{
		$result = DB::update("
            UPDATE `purchase_order`
            SET
                `status` = " . self::STATUS_FINISHED ." ,
				`deliver_by` = 'null',
				`last_update` = NOW()
			WHERE
                `id` = $po_id
			LIMIT 1
        ");
		if ($result) {
			return true;
		}
	}

	public static function UpdateProductAverageCost($product_item)
	{
		for($i = 0; $i < count($product_item); $i++){
			$product_id = $product_item[$i]["product_id"];

			$total_cost = 0.00;
			$total_qty = 0;
			$average_cost = 0.00;

			$data = DB::select("
				SELECT
					actual_price, qty
				FROM goods_in_items
				WHERE product_id = '$product_id';
			");

			foreach ($data as $value) {
				extract($value);
				$total_cost += ($actual_price * $qty);
				$total_qty += $qty;
			}
			$average_cost = $total_cost / $total_qty;

			$result = DB::update("
				UPDATE `product`
				SET
					`average_cost` = '$average_cost' ,
					`update_time` = NOW()
				WHERE
					`id` = $product_id
				LIMIT 1
			");
		}

		if ($result) {
			return true;
		}

	}

    public static function getGoodsIn($gi_id)
    {
        $data = DB::select("
            SELECT SQL_NO_CACHE
                `gi`.*, `s`.`code` AS `shop_code`, `s`.`address` AS `shop_address`,
				`s`.`telephone` AS `shop_telephone`, `s`.`fax` AS `shop_fax`,
                `sup`.`id` AS `supplier_id`, `sup`.`code` AS `supplier_code`,
                `sup`.`name` AS `supplier_name`, `sup`.`mobile` AS `supplier_mobile`,
                `sup`.`fax` AS `supplier_fax`, `sup`.`email` AS `supplier_email`
            FROM `goods_in` AS `gi`
			LEFT JOIN `shop` AS `s`
				ON `gi`.`goods_in_to` = `s`.`id`
            LEFT JOIN `supplier` AS `sup`
                ON `gi`.`supplier_id` = `sup`.`id`
            WHERE `gi`.`id` =  '$gi_id'
        ");

        if (empty($data)) {
            return null;
        }

		$data[0]['goods_in_to_shop'] = Shop::getShopCode($data[0]['goods_in_to']);
        return $data[0];
    }

	public static function getGoodsInItems($gi_id) {
		$po_data = DB::select("
			SELECT SQL_NO_CACHE `po`.`id`
			FROM `goods_in` AS `gi`
			LEFT JOIN `purchase_order` AS `po`
				ON `po`.`purchase_order_number` = `gi`.`po_ref_no` COLLATE utf8_unicode_ci
			WHERE `gi`.`id` = '$gi_id'
			LIMIT 0, 1
		");

		$po_id = $po_data[0]['id'];

        $item_data = DB::select("
			SELECT SQL_NO_CACHE
            `gii`.`barcode`, `gii`.`serial_number`, `gii`.`qty`, `P`.`name` AS `product_name`, `gii`.`actual_price` AS `unit_price`
            FROM `goods_in_items` AS `gii`
            LEFT JOIN `product` AS `p`
            ON `gii`.`barcode` = `p`.`barcode` COLLATE utf8_unicode_ci
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
				SUM(`actual_price` * `qty`) AS `total_cost`
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

	public static function getGoodsInDetails($gi_id)
	{
		$data = DB::select("
			SELECT SQL_NO_CACHE
				`gi`.`id`, `gi`.`sys_no` as `goods_in_number`, `gi`.`create_time`, `gi`.`po_remark`,
				`issue_shop`.`code` as `goods_in_to`, `supplier_shop`.`code` as `supplier_code`, `supplier_shop`.`name` as `supplier_name`,
				`gi`.`update_by` as `request_by`, count(`gii`.`gi_id`) as `total_items`,
				sum(`gii`.`qty`) as `total_qty`, sum(`gii`.`actual_price`) as `total_price`, `gi`.`invoice_no`,
				`gi`.`update_by`, `gi`.`update_time`
			FROM `goods_in` AS `gi`
			LEFT JOIN `goods_in_items` `gii`
				ON `gi`.`id` = `gii`.`gi_id`
			LEFT JOIN `shop` `issue_shop`
				ON `gi`.`goods_in_to` = `issue_shop`.`id`
			LEFT JOIN `supplier` `supplier_shop`
				ON `gi`.`supplier_id` = `supplier_shop`.`id`
			WHERE `gi`.`id` =  '$gi_id'
			GROUP BY `gii`.`gi_id`
		");

        if (empty($data)) {
            return null;
        }
		// $data['goods_in_to_shop'] = Shop::getShopCode($data[0]['goods_in_to']);
		return $data[0];
	}

	public static function getGoodsInItemsDetails($gi_id) {
        $data = DB::select("
            SELECT SQL_NO_CACHE
                `gii`.`id`, `p`.`id` AS `product_id`, `p`.`barcode`, `p`.`name`, `gii`.`actual_price`,
                `gii`.`qty`, `gii`.`serial_number`, sum(`gii`.`actual_price` * `gii`.`qty`) as `total_price`
            FROM `goods_in_items` AS `gii`
			LEFT JOIN `product` AS `p`
				ON `gii`.`product_id` = `p`.`id`
            WHERE `gii`.`gi_id` = '$gi_id'
			GROUP BY `gii`.`id`
        ");

        if (empty($data)) {
            return null;
        }

        return $data;
	}


    public static function getList()
    {
        $page = (Input::has("page")) ? intval(Input::get("page")) : 1;
        $status = 4;
        $where_clause = null;

        if (Input::has("search_goods_in_number")) {
            $goods_in_number = Input::get("search_goods_in_number");
            $where_clause .= " AND `gi`.`sys_no` LIKE '%$goods_in_number%'";
        }

        if (Input::has("search_shop")) {
            $shop = intval(Input::get("search_shop"));
            $where_clause .= " AND `issue_shop`.`id` = '$shop'";
        }

        if (Input::has("search_from_date")) {
            $from_date = parent::escape(Input::get("search_from_date"));
            $where_clause .= " AND DATE(`gi`.`create_time`) >= $from_date";
        }

        if (Input::get("search_to_date")) {
            $to_date = parent::escape(Input::get("search_to_date"));
            $where_clause .= " AND DATE(`gi`.`create_time`) <= $to_date";
        }

        $total_records_data = DB::select("
            SELECT SQL_NO_CACHE
                COUNT(*) AS `total_records`
            FROM `goods_in` AS `gi`
            LEFT JOIN `shop` `issue_shop`
                ON `gi`.`goods_in_to` = `issue_shop`.`id`
            WHERE 1 $where_clause
        ");

        $total_records = $total_records_data[0]['total_records'];
        $total_pages = intval(ceil($total_records / self::PAGE_LIMIT));
        $start_record = ($page - 1) * self::PAGE_LIMIT;

        $list_data = null;
        if ($total_records > 0) {
            $list_data = DB::select("
                SELECT SQL_NO_CACHE
                    `gi`.`id`, `gi`.`sys_no` as `goods_in_number`, `gi`.`create_time`,
                    `issue_shop`.`code` as `goods_in_to`, `gi`.`update_by` as `request_by`, count(`gii`.`gi_id`) as `total_items`,
					sum(`gii`.`qty`) as `total_qty`, `gi`.`invoice_no`
                FROM `goods_in` AS `gi`
                LEFT JOIN `goods_in_items` `gii`
                    ON `gi`.`id` = `gii`.`gi_id`
                LEFT JOIN `shop` `issue_shop`
                    ON `gi`.`goods_in_to` = `issue_shop`.`id`
                WHERE 1 $where_clause
				GROUP BY `gii`.`gi_id`
                ORDER BY `id` DESC
                LIMIT $start_record, " . self::PAGE_LIMIT
            );
        }

        return array(
            'status' => $status,
            'list_data' => $list_data,
            'total_records' => $total_records,
            'total_pages' => $total_pages
        );
    }
}
