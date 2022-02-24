<?php

class DepositItems extends BaseModel
{
    /**
     *  Get deposit items
     *  @param integer $deposit_id deposit ID
     *  @return array Deposit item record 
     */

    public static function get($id)
    {
        return DB::select("
            SELECT SQL_NO_CACHE
                `di`.`id`, `p`.`id` AS `product_id`, `p`.`barcode`, `p`.`name` AS `product_name`,
                `di`.`unit_price`, `di`.`qty`, `di`.`total_price`
            FROM `deposit_items` AS `di`
            LEFT JOIN `product` AS `p`
                ON `di`.`product_id` = `p`.`id`
            WHERE `deposit_id` = '$id'
        ");
    }

    /**
     *  Edit record
     *  @param array $params Items for create, update, delete
     *  @return null|array New deposit item IDs
     */
    public static function edit($params)
    {
        extract($params);

        // Process remove records
        if (!empty($removed_items)) {
            self::deleteRecord($deposit_id, $removed_items);
        }

        // Process existing records
        if (!empty($old_items)) {
            self::updateRecord($deposit_id, $old_items);
        }

        // Process new records
        if (!empty($new_items)) {
            return self::createRecord($deposit_id, $new_items);
        }
    }

    /**
     *  Create record
     *  @param integer $id Deposit ID
     *  @param array $items Items for create
     *  @return array New deposit item IDs
     */
    public static function createRecord($id, $deposit_items)
    {
        $new_ids = array();

        foreach ($deposit_items as $item) {
            extract($item);
            $row_index = intval($row_index);
            $qty = intval($qty);
            $unit_price = sprintf('%0.2f', $unit_price);
            $item_total_price = $unit_price * $qty;
            $product_id = intval($product_id);

            $add_items_record = DB::insert("
                INSERT INTO `deposit_items`
                SET
                    `deposit_id` = '$id',
                    `product_id` = '$product_id',
                    `unit_price` = '$unit_price',
                    `qty` = '$qty',
                    `total_price` = '$item_total_price'
            ");

            $new_ids[$row_index] = DB::getPdo()->lastInsertId();
        }

        return $new_ids;
    }

    /**
     *  Update record
     *  @param integer $id Deposit ID
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
                UPDATE `deposit_items`
                SET
                    `unit_price` = '$unit_price',
                    `qty` = '$qty',
                    `total_price` = '$item_total_price'
                WHERE
                    `deposit_id` = '$id'
                    AND `id` = '$record_id'
                LIMIT 1
            ");
        }
    }

    /**
     *  Delete record
     *  @param integer $deposit_id Deposit ID
     *  @param array $items Items for delete
     *  @return void
     */
    private static function deleteRecord($deposit_id, $items)
    {
        foreach ($items as $id) {
            $all_record_ids[] = intval($id);
        }
        $total_records = sizeof($all_record_ids);
        $all_record_ids = implode(',', $all_record_ids);

        DB::statement("
            DELETE FROM `deposit_items`
            WHERE
                `deposit_id` = '$deposit_id'
                AND `id` IN ($all_record_ids)
            LIMIT $total_records
        ");
    }

    /**
     *  Delete related deposit item record
     *  @param array $item_ids Deleted deposit record IDs
     *  @return integer Delete result
     */
    public static function deleteRelatedRecords($all_deposit_ids)
    {
        $all_deposit_ids = implode(',', $all_deposit_ids);
        return DB::delete("
            DELETE FROM `deposit_items`
            WHERE `deposit_id` IN ($all_deposit_ids)
        ");
    }
}
