<?php

class QuotationItems extends BaseModel
{
    /**
     *  Create new order
     *  @used-by (controller) ShoppingCartController
     *  @used-by (controller) CheckoutProcessController
     *  @param array $cart_params The shopping cart's parameters
     *  @return array Array of order number
     */
    public static function get($quotation_id)
    {
        return DB::select("
            SELECT SQL_NO_CACHE
                `qi`.`id`, `p`.`id` AS `product_id`, `p`.`barcode`, `p`.`name` AS `product_name`, `qi`.`unit_price`,
                `qi`.`qty`, `qi`.`total_price`
            FROM `quotation_items` AS `qi`
            LEFT JOIN `product` AS `p`
                ON `qi`.`product_id` = `p`.`id`
            WHERE `quotation_id` = '$quotation_id'
        ");
    }
	
    public static function getList($quotation_id, $shop_code)
    {	
		$shop_id = Shop::getID($shop_code);
        $data = DB::select("
           SELECT SQL_NO_CACHE
                `qi`.`id`, `p`.`id` AS `product_id`, `p`.`barcode`, `p`.`name` AS `product_name`, `qi`.`unit_price`,
                IF(sum(`s`.`qty`) = 0, 0, `qi`.`qty`) AS `qty`, `qi`.`total_price`, IF(sum(`s`.`qty`) = 0, 0,1) AS `stock_qty`, `shy`.`code`
            FROM `quotation_items` AS `qi`
            LEFT JOIN `product` AS `p`
                ON `qi`.`product_id` = `p`.`id`
            LEFT JOIN `quotation` AS `q`
                ON `qi`.`quotation_id` = `q`.`id`
            LEFT JOIN `stock` AS `s`
                ON `qi`.`product_id` = `s`.`product_id`
            LEFT JOIN `shop` AS `shy`
                ON `q`.`shop_id` = `shy`.`id`
            WHERE `quotation_id` = '$quotation_id'
				AND `s`.shop_id = $shop_id
			GROUP BY `id`
        ");
		
		if (!$data) {
			return 0;
		}
		
		return $data;
    }
	
	public static function getItems($quotation_id) 
	{
		return DB::select("
           SELECT SQL_NO_CACHE
                `qi`.`id`, `p`.`id` AS `product_id`, `p`.`barcode`, `p`.`name` AS `product_name`, `qi`.`unit_price`,
                IF(sum(`s`.`qty`) = 0, 0, `qi`.`qty`) AS `qty`, `qi`.`total_price`, IF(sum(`s`.`qty`) = 0, 0,1) AS `stock_qty`, `shy`.`code`
            FROM `quotation_items` AS `qi`
            LEFT JOIN `product` AS `p`
                ON `qi`.`product_id` = `p`.`id`
            LEFT JOIN `quotation` AS `q`
                ON `qi`.`quotation_id` = `q`.`id`
            LEFT JOIN `stock` AS `s`
                ON `qi`.`product_id` = `s`.`product_id`
            LEFT JOIN `shop` AS `shy`
                ON `q`.`shop_id` = `shy`.`id`
            WHERE `quotation_id` = '$quotation_id'
			GROUP BY `id`
        ");
	}

    public static function edit($params)
    {
        if (Session::get("page") !== "quotation_edit") {
            return 0;
        }
        extract($params);

        // Process delete records
        if (!empty($removed_items)) {
            self::deleteRecord($quotation_id, $removed_items);
        }

        // Process existing records
        if (!empty($old_items)) {
            self::updateRecord($quotation_id, $old_items);
        }

        // Process new records
        if (!empty($new_items)) {
            return self::createRecord($quotation_id, $new_items);
        }
    }

	public static function createRecord($quotation_id, $quotation_items)
	{
        $new_item_ids = array();
        $staff_code = Session::get('staff_code');

        foreach ($quotation_items as $id => $item) {
            extract($item);
            $row_index = intval($row_index);
            $qty = intval($qty);
            $unit_price =sprintf('%0.2f', $unit_price);
            $item_total_price = $unit_price * $qty;
            $product_id = intval($product_id);

            $add_items_record = DB::insert("
                INSERT INTO `quotation_items`
                SET
                    `quotation_id` = '$quotation_id',
                    `product_id` = '$product_id',
                    `unit_price` = '$unit_price',
                    `qty` = '$qty',
                    `total_price` = '$item_total_price'
            ");

            $new_item_ids[$row_index] = DB::getPdo()->lastInsertId();
        }

		return $new_item_ids;
	}

    public static function updateRecord($quotation_id, $items)
    {
        foreach ($items as $values) {
            extract($values);
            $record_id = intval($record_id);
            $unit_price = sprintf('%0.2f', $unit_price);
            $qty = intval($qty);
            $item_total_price = $unit_price * $qty;

            DB::update("
                UPDATE `quotation_items`
                SET
                    `unit_price` = '$unit_price',
                    `qty` = '$qty',
                    `total_price` = '$item_total_price'
                WHERE
                    `quotation_id` = '$quotation_id'
                    AND `id` = '$record_id'
                LIMIT 1
            ");
        }
    }

    public static function deleteRecord($quotation_id, $items)
    {
        foreach ($items as $id) {
            $all_record_ids[] = intval($id);
        }
        $total_records = sizeof($all_record_ids);
        $all_record_ids = implode(',', $all_record_ids);

        DB::statement("
            DELETE FROM `quotation_items`
            WHERE
                `quotation_id` = '$quotation_id'
                AND `id` IN ($all_record_ids)
            LIMIT $total_records
        ");
    }

    public static function deleteRelatedRecords($all_quotation_ids)
    {
        $all_quotation_ids = implode(',', $all_quotation_ids);
        return DB::delete("
            DELETE FROM `quotation_items`
            WHERE `quotation_id` IN ($all_quotation_ids)
        ");
    }

}
