<?php

class Smemo extends BaseModel
{
    public static function getReportList()
    {
        $where_clause = null;

        $period_year = intval(Input::get("period_year"));
        $period_month = intval(Input::get("period_month"));
        $shop = parent::escape(Input::get("shop"));
        $sales = parent::escape(Input::get("sales"));

        dd("
            SELECT SQL_NO_CACHE
                `shp`.`code` AS `shop_code`,
                `shp`.`name` AS `shop_name`,
                `sm`.`sm_txdate`,
                `sm`.`sm_smemo`,
                `sm`.`sm_dmemo`,
                `sm`.`sm_txtype`, 
                `sm`.`sm_rmemo`,
                `sm`.`sm_sfid`,
                `sm`.`cust_no`,
                `sm`.`sm_compy` AS `company`,
                `b`.`name` AS `product_brand`,
                `pc`.`name` AS `product_category`, 
                `p`.`model` AS `product_model`,
                `p`.`color` AS `product_color`,
                `p`.`average_cost`,
                `soi`.`product_code`,
                `soi`.`serial_number`,
                `soi`.`qty` AS `order_qty`,
                `soi`.`unit_price`,
                `p`.`reference_cost`,
                `p`.`name` AS `description`,
                `sm`.`sm_remark` AS `remarks`,
                `sm`.`sm_txtime`
            FROM `smemo` AS `sm`
            LEFT JOIN `sales_order` AS `so`
                ON `sm`.`sm_smemo` = `so`.`sales_order_number` COLLATE utf8_unicode_ci
            LEFT JOIN `sales_order_items` AS `soi`
                ON `soi`.`sales_order_id` = `so`.`sales_order_id`
            LEFT JOIN `product` AS `p`
                ON `p`.`id` = `soi`.`product_id`
            LEFT JOIN `brand` AS `b`
                ON `b`.`id` = `p`.`brand_id`
            LEFT JOIN `product_category` AS `pc`
                ON `pc`.`id` = `p`.`category_id`
            LEFT JOIN `shop` AS `shp`
                ON `shp`.`id` = `so`.`shop_id`
            WHERE 1 $where_clause
            ORDER BY `sm`.`id` ASC
        ");

        $data = DB::select("
            SELECT SQL_NO_CACHE
                `shp`.`code` AS `shop_code`,
                `shp`.`name` AS `shop_name`,
                `sm`.`sm_txdate`,
                `sm`.`sm_smemo`,
                `sm`.`sm_dmemo`,
                `sm`.`sm_txtype`, 
                `sm`.`sm_rmemo`,
                `sm`.`sm_sfid`,
                `sm`.`cust_no`,
                `sm`.`sm_compy` AS `company`,
                `b`.`name` AS `product_brand`,
                `pc`.`name` AS `product_category`, 
                `p`.`model` AS `product_model`,
                `p`.`color` AS `product_color`,
                `p`.`average_cost`,
                `soi`.`product_code`,
                `soi`.`serial_number`,
                `soi`.`qty` AS `order_qty`,
                `soi`.`unit_price`,
                `p`.`reference_cost`,
                `p`.`name` AS `description`,
                `sm`.`sm_remark` AS `remarks`,
                `sm`.`sm_txtime`
            FROM `smemo` AS `sm`
            LEFT JOIN `sales_order` AS `so`
                ON `sm`.`sm_smemo` = `so`.`sales_order_number` COLLATE utf8_unicode_ci
            LEFT JOIN `sales_order_items` AS `soi`
                ON `soi`.`sales_order_id` = `so`.`sales_order_id`
            LEFT JOIN `product` AS `p`
                ON `p`.`id` = `soi`.`product_id`
            LEFT JOIN `brand` AS `b`
                ON `b`.`id` = `p`.`brand_id`
            LEFT JOIN `product_category` AS `pc`
                ON `pc`.`id` = `p`.`category_id`
            LEFT JOIN `shop` AS `shp`
                ON `shp`.`id` = `so`.`shop_id`
            WHERE 1 $where_clause
            ORDER BY `sm`.`id` ASC
        ");

        if (empty($data)) {
            return null;
        }

        return $data;
    }

}