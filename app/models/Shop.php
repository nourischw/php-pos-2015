<?php

class Shop extends BaseModel
{
    public static function get($param)
    {
        $id = (isset($param['id'])) ? intval($param['id']) : null;
        $code = (isset($param['code'])) ? parent::escape($param['code']) : null;

        $data = DB::select("
            SELECT SQL_NO_CACHE *
            FROM `shop`
            WHERE " .
            ((!empty($id)) ? " `id` = '$id' " : null) .
            ((!empty($code)) ? " `code` = $code " : null) .
            " LIMIT 0, 1
        ");

        if (empty($data)) {
            return null;
        }

        return $data[0];
    }

    public static function getID($code)
    {
        $data = DB::select("
            SELECT SQL_NO_CACHE `id`
            FROM `shop`
            WHERE `code` = '$code'
            LIMIT 0, 1
        ");

        return $data[0]['id'];
    }
	
	public static function getShopCode($id)
	{
        $id = intval($id);
		$data = DB::select("
			SELECT SQL_NO_CACHE `code`
			FROM `shop`
			WHERE `id` = '$id'
			LIMIT 0, 1
		");
		
		return $data[0]['code'];
	}

    public static function getShopSymbol($code)
    {
        $code = parent::escape($code);
        $data = DB::select("
            SELECT SQL_NO_CACHE 
                `shop_symbol` AS `shop_syb`
            FROM `shop`
            WHERE `code` = $code
            LIMIT 0, 1
        ");

        if (empty($data)) {
            return false;
        }

        return $data[0]['shop_syb'];
    }
    
	public static function getSelectList()
	{
		return DB::select("
			SELECT SQL_NO_CACHE
				`id`, `code`, `name`
			FROM `shop`
			ORDER BY `id` ASC
		");
	}

    public static function getShopInfo() {
        $shop_code = parent::escape(Input::get("shop_code"));

        $data = DB::select("
            SELECT SQL_NO_CACHE
                `SUP_NAME`
            FROM `supplier`
            WHERE `SUP_CODE` = $shop_code
            LIMIT 0, 1
        ");

        if (empty($data)) {
            return null;
        }

        return $data[0];
    }
}
