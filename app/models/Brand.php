<?php

class Brand extends BaseModel
{
	const PAGE_LIMIT = 100;
	
    public static function CreateOrder()
    {
        $staff_name = Session::get('staff_name');
		$brand_name = Input::get("brand_name");
		$brand_remark = Input::get("brand_remark");
	
		$data = DB::insert("
            INSERT INTO `brand`
            SET
                `name` = '$brand_name',
                `remark` = '$brand_remark',
                `display` = '1',
                `update_by` = '$staff_name',
				`create_time` = NOW(),
				`update_time` = NOW()
		");	
		
		if($data){
			return $data;
		}
		return false;
    }
	
    public static function EditOrder()
    {
        $staff_name = Session::get('staff_name');
		$brand_id = Input::get("brand_id");
		$brand_name = Input::get("brand_name");
		$brand_remark = Input::get("brand_remark");

		$data = DB::update("
            UPDATE  `brand`
            SET
                `name` = '$brand_name',
                `remark` = '$brand_remark',
                `display` = '1',
                `update_by` = '$staff_name',
				`update_time` = NOW()
            WHERE `id` = $brand_id
            LIMIT 1				
		");	
		
		if($data){
			return $data;
		}
		return false;
    }	
	
	public static function RemoveOrder(){
		$brand_id = Input::get("brand_id");
		
		$data = DB::delete("
            DELETE FROM `brand` 
            WHERE `id` = '$brand_id'
            LIMIT 1
        ");
		
        if ($data) {
            return 1;
        }
        return 0;		
	}
	
	public static function getTotalRecords()
    {
        $data = DB::select("
            SELECT COUNT(*) AS `total_records`
            FROM `brand`
        ");
        return $data[0]['total_records'];
    }
	
    public static function getBrandList($page = 1)
    {
        $start_record = ($page - 1) * 100;

        // $data = DB::select("
            // SELECT SQL_NO_CACHE * 
            // FROM `brand`
            // ORDER BY `id` desc
            // LIMIT $start_record, " . self::PAGE_LIMIT
        // );
        $data = DB::select("
            SELECT SQL_NO_CACHE * 
            FROM `brand`
            ORDER BY `id` desc"
        );		
        return $data;
    }

	public static function getBrand(){
		$brand_id = Input::get("brand_id");
		$data = DB::select("
			SELECT * FROM brand WHERE id = '$brand_id' Limit 1		
		");
		return $data;
	}
	
    public static function getBrandPopupList($page = 1, $search_params = null)
    {
        $where_clause = null;
        if ($search_params !== null) {
            extract($search_params);
            if ($search_brand_name !== "") {
                $where_clause .= " AND `name` = " . parent::escape(Input::get("search_brand_name"));
            }
            if ($search_brand_id !== "") {
                $where_clause .= " AND `id` = " . parent::escape(Input::get("search_brand_id"));
            }
        }

    	$total_brand = DB::select("
    		SELECT SQL_NO_CACHE
    			COUNT(*) AS `total_brand`
    		FROM `brand`
            WHERE 1 $where_clause
    	");

    	$total_pages = intval(ceil($total_brand[0]['total_brand'] / 100));

        $start_record = ($page - 1) * 100;
        $brand_list = DB::select("
            SELECT SQL_NO_CACHE 
            	*
            FROM `brand`
            WHERE 1 $where_clause
            ORDER BY `id`
            LIMIT $start_record, " . self::PAGE_LIMIT
        );

        return array(
        	'brand_list_total_pages' => $total_pages,
        	'brand_list'	 => $brand_list
        );
    }	
	
    public static function searchBrandList($keyword, $page = null)
    {
        $data = DB::select("
            SELECT SQL_NO_CACHE * 
            FROM `brand`
			WHERE `id` LIKE '%$keyword%' OR `name` LIKE '%$keyword%'"
        );
		return $data;
	}
}
