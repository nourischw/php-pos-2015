<?php

class StockWithdrawItems extends BaseModel
{
    /**
     *  Get Stock withdraw items
     *  @param integer $stock_transfer_id Stock withdraw ID
     *  @return array stock withdraw item record
     */
    public static function get($stock_withdraw_id)
    {
        return DB::select("
            SELECT SQL_NO_CACHE
                `swi`.* , `gi`.`sys_no` AS `gi_code`, `p`.`barcode`, `p`.`name` AS `product_name`, `stk`.`serial_number`
            FROM `stock_withdraw_items` AS `swi`
            LEFT JOIN `goods_in` AS `gi`
                ON `swi`.`gi_id` = `gi`.`id`
            LEFT JOIN `stock` AS `stk`
                ON `swi`.`stock_id` = `stk`.`id`
            LEFT JOIN `product` AS `p`
                ON `p`.`id` = `stk`.`product_id`
            WHERE
                `swi`.`stock_withdraw_id` = '$stock_withdraw_id'
        ");
    }

    public static function getWithdrawQty($stock_withdraw_id)
    {
        $stock_withdraw_id = intval($stock_withdraw_id);
        return DB::select("
            SELECT SQL_NO_CACHE
                `id`, `stock_id`, `qty`
            FROM `stock_withdraw_items`
            WHERE `stock_withdraw_id` = '$stock_withdraw_id'
        ");
    }

    /**
     *  Create record
     *  @param integer $withdraw_id Stock withdraw ID
     *  @param array $items Items for create
     *  @return array New stock withdraw item IDs
     */
    public static function createRecord($withdraw_id, $withdraw_items)
    {
        $new_item_ids = array();
        foreach ($withdraw_items as $item) {
            extract($item);
            $gi_id = intval($gi_id);
            $stock_id = intval($stock_id);
            $qty = intval($qty);
            $unit_price = sprintf('%0.2f', $unit_price);
            $total_price = $qty * $unit_price;

            $add_items_record = DB::insert("
                INSERT INTO `stock_withdraw_items`
                SET
                    `gi_id` = '$gi_id',
                    `stock_withdraw_id` = '$withdraw_id',
                    `stock_id` = '$stock_id',
                    `qty` = '$qty',
                    `price` = '$unit_price',
                    `total_price` = '$total_price'
            ");

            $new_item_ids[] = DB::getPdo()->lastInsertId();
        }
        return $new_item_ids;
    }

    /**
     *  Delete related Stock withdraw item record
     *  @param array $item_ids Deleted Stock withdraw record IDs
     *  @return integer Delete result
     */
	public static function deleteRelatedRecords($record_id)
	{
		$record_id = intval($record_id);
		$result = DB::delete("
			DELETE FROM `stock_withdraw_items`
			WHERE `stock_withdraw_id` = '$record_id'
		");
        if ($result) {
            return 1;
        }
        return 0;
	}
}
