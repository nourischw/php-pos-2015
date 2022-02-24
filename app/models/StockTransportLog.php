<?php

class StockTransportLog extends BaseModel
{
	const STOCK_TRANSPORT_TYPE_GOODS_IN = 1;
	const STOCK_TRANSPORT_TYPE_SALES_INVOICE = 2;
	const STOCK_TRANSPORT_TYPE_STOCK_TRANSFER = 3;
	const STOCK_TRANSPORT_TYPE_STOCK_WITHDRAW = 4;

    const PAGE_LIMIT = 50;

	public static function createRecord($params)
	{
		$current = date("Y-m-d H:i:s");
		$values = array();
		$i = 0;

		foreach ($params as $data) {
			extract($data, EXTR_PREFIX_ALL, 'stl');
			$values[$i] = "('$stl_stock_id', '$stl_type', '$current', '$stl_desc')";
			$i++;
		}
		$values = implode(",", $values);

		DB::insert("
			INSERT INTO `stock_transport_log` (`stock_id`, `type`, `log_time`, `desc`)
			VALUES $values
		");
	}

    public static function getList($stock_id)
    {
		$stock_id = intval($stock_id);
        $page = (Input::has("page")) ? intval(Input::get("page")) : 1;
		$where_clause = null;

		if (Input::has("search_type")) {
			$log_type = intval(Input::get("search_type"));
			if ($log_type > 0) {
				$where_clause .= " AND `type` = '$log_type'";
			}
		}

        if (Input::has("search_from_date")) {
            $from_date = parent::escape(Input::get("search_from_date"));
            $where_clause .= " AND `log_time` >= $from_date";
        }

        if (Input::get("search_to_date")) {
            $to_date = parent::escape(Input::get("search_to_date"));
            $where_clause .= " AND `log_time` <= $to_date";
        }

        $total_records_data = DB::select("
            SELECT SQL_NO_CACHE
                COUNT(*) AS `total_records`
            FROM `stock_transport_log`
            WHERE `stock_id` = '$stock_id'
				$where_clause
        ");

        $total_records = $total_records_data[0]['total_records'];
        $total_pages = intval(ceil($total_records / self::PAGE_LIMIT));
        $start_record = ($page - 1) * self::PAGE_LIMIT;

        $list_data = null;
        if ($total_records > 0) {
            $list_data = DB::select("
                SELECT SQL_NO_CACHE *
                FROM `stock_transport_log`
                WHERE `stock_id` = '$stock_id'
					$where_clause
                ORDER BY `log_time` DESC
                LIMIT $start_record, " . self::PAGE_LIMIT
            );
        }

		return array(
			'list_data' => $list_data,
			'page' => $page,
			'total_records' => $total_records,
			'total_pages' => $total_pages,
			'have_records' => (!empty($list_data)) ? true : false
		);
	}
}
