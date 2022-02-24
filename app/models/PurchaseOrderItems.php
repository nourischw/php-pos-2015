<?php

class PurchaseOrderItems extends BaseModel
{
    /**
     *  Get PO items
     *  @param integer $po_id PO ID
     *  @return array PO item record 
     */
    public static function get($po_id)
    {
        return DB::select("
            SELECT SQL_NO_CACHE
                `poi`.`id`, `p`.`id` AS `product_id`, `p`.`barcode`, `p`.`name`, `poi`.`unit_price`,
                `poi`.`qty`, `poi`.`total_price`
            FROM `purchase_order_items` AS `poi`
            LEFT JOIN `product` AS `p`
                ON `poi`.`product_id` = `p`.`id`
            WHERE `purchase_order_id` = '$po_id'
        ");
    } 

    /**
     *  Edit record
     *  @param array $params Items for create, update, delete
     *  @return null|array New PO item IDs
     */
    public static function edit($params)
    {
        extract($params);
        
        // Process remove records
        if (!empty($removed_items)) {
            self::deleteRecord($po_id, $removed_items);
        }

        // Process existing records
        if (!empty($old_items)) {
            self::updateRecord($po_id, $old_items);
        }

        // Process new records
        if (!empty($new_items)) {
            return self::createRecord($po_id, $new_items);
        }

        return null;
    }

    /**
     *  Create record
     *  @param integer $id PO ID
     *  @param array $items Items for create
     *  @return array New PO item IDs
     */
    public static function createRecord($id, $items)
    {
        $new_item_ids = array();
        $staff_code = Session::get('staff_code');
        foreach ($items as $item) {
            extract($item);
            $row_index = intval($row_index);
            $qty = intval($qty);
            $unit_price = sprintf('%0.2f', $unit_price);
            $item_total_price = $unit_price * $qty;
            $product_id = intval($product_id);

            $add_items_record = DB::insert("
                INSERT INTO `purchase_order_items`
                SET
                    `purchase_order_id` = '$id',
                    `product_id` = '$product_id',
                    `unit_price` = '$unit_price',
                    `qty` = '$qty',
                    `total_price` = '$item_total_price',
                    `last_update_by` = '$staff_code',
                    `last_update_time` = NOW(),
                    `create_time` = NOW()
            ");

            $new_item_ids[$row_index] = DB::getPdo()->lastInsertId();
        }

        return $new_item_ids;
    }

    /**
     *  Update record
     *  @param integer $id PO ID
     *  @param array $items Items for update
     *  @return void
     */
    private static function updateRecord($id, $items)
    {
        foreach ($items as $values) {
            extract($values);
            $record_id = intval($record_id);
            $unit_price = sprintf('%0.2f', $unit_price);
            $qty = intval($qty);
            $item_total_price = $unit_price * $qty;

            DB::update("
                UPDATE `purchase_order_items`
                SET
                    `unit_price` = '$unit_price',
                    `qty` = '$qty',
                    `total_price` = '$item_total_price'
                WHERE
                    `purchase_order_id` = '$id'
                    AND `id` = '$record_id'
                LIMIT 1
            ");
        }
    }

    /**
     *  Delete record
     *  @param integer $po_id PO ID
     *  @param array $items Items for delete
     *  @return void
     */
    private static function deleteRecord($po_id, $items)
    {
        foreach ($items as $id) {
            $all_record_ids[] = intval($id);
        }
        $total_records = sizeof($all_record_ids);
        $all_record_ids = implode(',', $all_record_ids);

        DB::statement("
            DELETE FROM `purchase_order_items`
            WHERE
                `purchase_order_id` = '$po_id'
                AND `id` IN ($all_record_ids)
            LIMIT $total_records
        ");
    }

    /**
     *  Delete related PO item record
     *  @param array $item_ids Deleted PO record IDs
     *  @return integer Delete result
     */
    public static function deleteRelatedRecords($item_ids)
    {
        $item_ids = implode(',', $item_ids);
        return DB::delete("
            DELETE FROM `purchase_order_items`
            WHERE `purchase_order_id` IN ($item_ids)
        ");
    }

	public static function getGoodsInPurchaseOrderItems($po_id)
	{
		$data = DB::select("
			SELECT SQL_NO_CACHE
				`PO`.`purchase_order_number`,
				`POI`.`id` AS `product_id`, `POI`.`product_id` AS `get_product_id`, `P`.`name` AS `product_name`,
				`P`.`barcode` AS `product_upc`, `P`.`required_imei`,
				`POI`.`unit_price` AS `product_unit_price`,
				`POI`.`qty` AS `product_qty`,
				`POI`.`total_price` AS `product_total_price`
			FROM `purchase_order_items` AS `POI`
			LEFT JOIN `product` AS `P`
			ON `P`.id = `POI`.product_id
			LEFT JOIN `purchase_order` AS `PO`
			ON `PO`.id = `POI`.purchase_order_id
			WHERE `purchase_order_id` = '$po_id'
		");

		return $data;
	}

    /**
     *  Get stock items for stock transfer
     *  @param integer $po_id PO ID
     *  @return array Related stock transfer items
     */
    public static function getStockItems($po_id)
    {
        return DB::select("
            SELECT SQL_NO_CACHE
                `poi`.`product_id`, `poi`.`qty`, `p`.`have_serial_number`
            FROM `purchase_order_items` AS `poi`
            LEFT JOIN `product` AS `p`
                ON `poi`.`product_id` = `p`.`id`
            WHERE `purchase_order_id` = '$po_id'
        ");
    }

	public static function getPurchaseOrderItemsID($po_id)
	{
        $data = DB::select("
			SELECT SQL_NO_CACHE
                `id` AS `new_po_item_id`
            FROM purchase_order_items
            WHERE `purchase_order_id` = '$po_id'
        ");

		return $data;
	}
}
