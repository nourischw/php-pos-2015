<?php

class Category extends BaseModel
{
	
    public static function getCategoryList($where_clause = null)
    {
        $data = DB::select("
            SELECT SQL_NO_CACHE 
				*
            FROM `product_category` 	
            WHERE 1 $where_clause "
        );
        return $data;
    }
	
    public static function getCategoryName($id = null)
    {
        $data = DB::select("
            SELECT SQL_NO_CACHE 
				name as category_name
            FROM `product_category` 	
            WHERE id = $id"
        );
		extract($data[0]);
        return $category_name;
    }	
	
	public static function getCategory(){
		$category_id = Input::get("category_id");
		$data = DB::select("
			SELECT * FROM product_category WHERE id = '$category_id' Limit 1		
		");
		return $data;
	}
	
    public static function CreateOrder()
    {
		$category_name = Input::get("category_name");
	
		$data = DB::insert("
            INSERT INTO `product_category`
            SET
                `name` = '$category_name',
                `last_update` = NOW()
		");	
		
		if($data){
			return $data;
		}
		return false;
    }
	
    public static function EditOrder()
    {
		$category_id = Input::get("category_id");
		$category_name = Input::get("category_name");
		$category_remark = Input::get("category_remark");

		$data = DB::update("
            UPDATE  `product_category`
            SET
                `name` = '$category_name',
				`last_update` = NOW()
            WHERE `id` = $category_id
            LIMIT 1				
		");	
		
		if($data){
			return $data;
		}
		return false;
    }	
	
	public static function RemoveOrder()
	{
		$category_id = Input::get("category_id");
		
		$data = DB::delete("
            DELETE FROM `product_category` 
            WHERE `id` = '$category_id'
            LIMIT 1
        ");
		
        if ($data) {
            return 1;	
        }
        return 0;		
	}
	
	public static function searchCategoryList($keyword, $page = null)
    {
		$start_record = ($page - 1) * 100;

        $data = DB::select("
            SELECT SQL_NO_CACHE * 
            FROM `product_category`
			WHERE `id` LIKE '%$keyword%' OR `name` LIKE '%$keyword%'"
        );
		return $data;
	}
}