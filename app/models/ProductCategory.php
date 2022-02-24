<?php

class ProductCategory extends BaseModel
{

    public static function getSelectList()
    {
        $data = DB::select("
            SELECT
                `id`, `name`
            FROM `product_category`
            ORDER BY `id`
        ");

        return $data;
    }

}
