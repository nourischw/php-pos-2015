<?php

class StockTransferLog extends BaseModel
{
    const PAGE_LIMIT = 50;
	
    const STATUS_INITIAL = 1;
	const STATUS_PROCESSING = 2;
	const STATUS_DELIVERED = 3;
	const STATUS_RETRANSFER = 4;
	const STATUS_FINISHED = 5;
    const STATUS_CANCELLED = 6;
	
	public static function createRecord($params)
	{
		extract($params);
		$stock_transfer_id = intval($stock_transfer_id);
		$shop_code = parent::escape($shop_code);
		$staff_code = parent::escape($staff_code);
		$status = intval($status);
		
		// Only used for confirm deliver record
		$deliver_staff_code = ($status == self::STATUS_DELIVERED) ? parent::escape($deliver_staff_code) : "''";

		DB::insert("
			INSERT INTO `stock_transfer_log`
			SET 
				`log_date` = NOW(),
				`stock_transfer_id` = '$stock_transfer_id',
				`shop_code` = $shop_code,
				`staff_code` = $staff_code,
				`deliver_staff_code` = $deliver_staff_code,
				`status` = '$status'
		");
	}
	
	public static function get($stock_transfer_id)
	{
		return DB::select("
			SELECT SQL_NO_CACHE *
			FROM `stock_transfer_log`
			WHERE `stock_transfer_id` = '$stock_transfer_id'
			ORDER BY `log_date` ASC
		");
	}
}