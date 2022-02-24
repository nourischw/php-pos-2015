<?php

class StockTransferItems extends BaseModel
{
    /**
     *  Get Stock transfer items
     *  @param integer $stock_transfer_id Stock transfer ID
     *  @return array stock transfer item record
     */
    public static function get($stock_transfer_id)
    {
        return DB::select("
            SELECT SQL_NO_CACHE
                `sti`.`id`, `sti`.`stock_id`, `p`.`barcode`, `p`.`name` AS `product_name`,
                `stk`.`serial_number`, `sti`.`qty`, `stk`.`qty` AS `remain_qty`
            FROM `stock_transfer_items` AS `sti`
            LEFT JOIN `stock` AS `stk`
                ON `sti`.`stock_id` = `stk`.`id`
            LEFT JOIN `product` AS `p`
                ON `p`.`id` = `stk`.`product_id`
            WHERE `sti`.`stock_transfer_id` = '$stock_transfer_id'
        ");
    }

	public static function getIds($stock_transfer_id)
	{
		return DB::select("
			SELECT SQL_NO_CACHE `id`
			FROM `stock_transfer_items`
			WHERE `stock_transfer_id` = '$stock_transfer_id'
		");
	}

    public static function getTransferQty($stock_transfer_id)
    {
        return DB::select("
            SELECT SQL_NO_CACHE `id`, `stock_id`, `qty`
            FROM `stock_transfer_items`
            WHERE `stock_transfer_id` = '$stock_transfer_id'
        ");
    }

    /**
     *  Edit record
     *  @param array $params Items for create, update, delete
     *  @return null|array New stock transfer item IDs
     */
    public static function edit($params)
    {
        extract($params);

        // Process remove records
        if (!empty($removed_items)) {
            $removed_items = explode(',', $removed_items);
            foreach ($removed_items as $id) {
                $all_record_ids[] = intval($id);
            }
            $total_records = sizeof($all_record_ids);
            $all_record_ids = implode(',', $all_record_ids);

            DB::statement("
                DELETE FROM `stock_transfer_items`
                WHERE
                    `stock_transfer_id` = '$stock_transfer_id'
                    AND `id` IN ($all_record_ids)
                LIMIT $total_records
            ");
        }

        // Process existing records
        if (!empty($old_items)) {
            self::updateRecord($stock_transfer_id, $old_items);
        }

        // Process new records
        if (!empty($new_items)) {
            return self::createRecord($stock_transfer_id, $new_items);
        }
    }

    /**
     *  Create record
     *  @param integer $id Stock transfer ID
     *  @param array $items Items for create
     *  @return array New stock transfer item IDs
     */
    public static function createRecord($id, $transfer_items)
    {
        $new_item_ids = array();
        foreach ($transfer_items as $item) {
            extract($item);
            $stock_id = intval($stock_id);
            $qty = intval($transfer_qty);

            $add_items_record = DB::insert("
                INSERT INTO `stock_transfer_items`
                SET
                    `stock_transfer_id` = '$id',
                    `stock_id` = '$stock_id',
                    `qty` = '$qty',
                    `last_update` = NOW()
            ");

            $new_item_ids[] = DB::getPdo()->lastInsertId();
        }
        return $new_item_ids;
    }

    /**
     *  Update record
     *  @param integer $id Stock transfer ID
     *  @param array $items Items for update
     *  @return void
     */
    public static function updateRecord($id, $items)
    {
        foreach ($items as $key => $values) {
            extract($values);
            $record_id = intval($record_id);
            $qty = intval($transfer_qty);

            DB::update("
                UPDATE `stock_transfer_items`
                SET `qty` = '$qty'
                WHERE
                    `stock_transfer_id` = '$id'
                    AND `id` = '$record_id'
                LIMIT 1
            ");
        }
    }

    /**
     *  Delete record
     *  @param integer $stock_transfer_id Stock Transfer ID
     *  @param array $items Items for delete
     *  @return void
     */
    public static function deleteRecord($stock_transfer_id, $items)
    {
        $items = explode(',', $items);
        foreach ($items as $id) {
            $all_record_ids[] = intval($id);
        }
        $total_records = sizeof($all_record_ids);
        $all_record_ids = implode(',', $all_record_ids);

        DB::statement("
            DELETE FROM `stock_transfer_items`
            WHERE
                `stock_transfer_id` = '$stock_transfer_id'
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
		$result = DB::delete("
			DELETE FROM `stock_transfer_items`
			WHERE `stock_transfer_id` IN ($item_ids)
		");
        if ($result) {
            return 1;
        }
        return 0;
	}
}
