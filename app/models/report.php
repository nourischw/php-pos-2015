<?php

class Report extends BaseModel
{
    const PAGE_LIMIT = 100;

    public static function downloadSalesReport()
    {

        $where_clause = null;

        if (Input::has("from_date")) {
            $from_date = Input::get("from_date");
            $where_clause .= " AND `so`.`create_time` >= '$from_date' ";
        }

        if (Input::has("to_date")) {
            $to_date = Input::get("to_date");
            $where_clause .= " AND `so`.`create_time` <= '$to_date' ";
        }

        if (Input::has("shop")) {
            $shop = Input::get("shop");
			if($shop){
				$where_clause .= " AND `shp`.`id` = $shop ";
			}
        }

        if (Input::has("sales")) {
            $sales = Input::get("sales");
			if($sales){
				$where_clause .= " AND `so`.`sales_id` = $sales ";
			}

        }

        $list_data = DB::select("
            SELECT SQL_NO_CACHE
			`shp`.`code` AS `shop_code`,
			`shp`.`name` AS `shop_name`,
			`so`.`create_time`,
			`so`.`sales_invoice_number`,
			`dp`.`deposit_number`,
			`so`.`sales_id`,
			`so`.`net_total_amount`,
			`so`.`cust_no`,
			`b`.`name` AS `product_brand`,
			`pc`.`name` AS `product_category`,
			`p`.`model` AS `product_model`,
			`p`.`color` AS `product_color`,
			`p`.`average_cost`, `soi`.`product_code`,
			`soi`.`serial_number`,
			`soi`.`qty` AS `order_qty`,
			`soi`.`unit_price`,
			`p`.`reference_cost`,
			`p`.`name` AS `description`,
			`so`.`remark`
			FROM  `sales_invoice` AS `so`
			LEFT JOIN `sales_invoice_items` AS `soi` ON `soi`.`sales_invoice_id` = `so`.`id`
			LEFT JOIN `deposit` AS `dp` ON `so`.`deposit_id` = `dp`.`id`
			LEFT JOIN `product` AS `p` ON `p`.`id` = `soi`.`product_id`
			LEFT JOIN `brand` AS `b` ON `b`.`id` = `p`.`brand_id`
			LEFT JOIN `product_category` AS `pc` ON `pc`.`id` = `p`.`category_id`
			LEFT JOIN `shop` AS `shp` ON `shp`.`id` = `so`.`shop_id`
            WHERE 1 $where_clause
            ORDER BY `so`.`id` ASC
        ");


        return $list_data;
    }

    public static function downloadGoodsinReport()
    {

        $where_clause = null;

        if (Input::has("goodsin_from_date")) {
            $from_date = Input::get("goodsin_from_date");
            $where_clause .= " AND `GI`.`create_time` >= '$from_date' ";
        }

        if (Input::has("goodsin_to_date")) {
            $to_date = Input::get("goodsin_to_date");
            $where_clause .= " AND `GI`.`create_time` <= '$to_date' ";
        }

        $list_data = DB::select("
                SELECT 
                GI.sys_no,
                S.name,
                S.code,
                GII.shop_code,
                GI.invoice_no,
                GII.qty,
                GII.actual_price,
                GII.barcode,
                GII.serial_number,
                P.name AS product_name,
                PO.remarks,
                PO.create_time AS pom_crtdte,
                PO.last_update AS pom_indte,
                GI.update_time AS gi_update_time
                FROM pos.goods_in GI
                LEFT JOIN pos.goods_in_items GII
                ON GI.id = GII.gi_id
                LEFT JOIN pos.supplier S
                ON GI.supplier_id = S.id

                LEFT JOIN pos.product P
                ON GII.product_id = P.id
                LEFT JOIN pos.purchase_order PO
                ON GI.po_id = PO.id
                WHERE 1 $where_clause
                ORDER BY GI.update_time
            ");


        return $list_data;
    }


    public static function downloadRealtimeInventoryReport()
    {

        $where_clause = null;

        // if (Input::has("goodsin_from_date")) {
        //     $from_date = Input::get("goodsin_from_date");
        //     $where_clause .= " AND `GI`.`create_time` >= '$from_date' ";
        // }

        // if (Input::has("goodsin_to_date")) {
        //     $to_date = Input::get("goodsin_to_date");
        //     $where_clause .= " AND `GI`.`create_time` <= '$to_date' ";
        // }

        if (Input::has("shop")) {
            $shop = Input::get("shop");
            if($shop){
                $where_clause .= " AND `S`.`shop_id` = $shop ";
            }
        }

        if (Input::has("category")) {
            $category = Input::get("category");
            if($category){
                $where_clause .= " AND `P`.`category_id` = $category ";
            }
        }

        $list_data = DB::select("
                SELECT 
                    S.shop_id, 
                    P.id AS product_id, 
                    P.category, 
                    P.barcode, 
                    P.product_spec, 
                    P.name, 
                    P.reference_cost, 
                    P.unit_price, 
                    sum(qty) AS qty
                    FROM pos.stock S
                    LEFT JOIN pos.product P
                    ON S.product_id = P.id
                    WHERE product_id in (
                    select S.product_id 
                    from pos.stock S
                    LEFT JOIN pos.product P
                    ON S.product_id = P.id
                    where S.qty > 0 
                    $where_clause
                    )
                    group by product_id, shop_id 
                    order by product_id desc
            ");


        return $list_data;
    }

    public static function downloadDailySalesReport()
    {

        $where_clause = null;

        if (Input::has("dailysales_date")) {
            $date = Input::get("dailysales_date");
            $where_clause .= " AND `SI`.`create_time` like '$date%' ";
        }

        if (Input::has("shop")) {
            $shop = Input::get("shop");
            if($shop){
                $where_clause .= " AND `SI`.`shop_id` = $shop ";
            }
        }

        if (Input::has("category")) {
            $category = Input::get("category");
            if($category){
                $where_clause .= " AND `P`.`category_id` = $category ";
            }
        }

        $list_data = DB::select("
                SELECT  
                    S.code AS shp_code, 
                    S.name AS shp_name,
                    SI.create_time AS sm_txdate,
                    SI.sales_invoice_number AS sm_smemo,
                    SI.type_name AS sm_txtype,
                    SI.sales_code AS sm_sfid,
                    SII.total_price AS netpaid,
                    B.name AS pdtbrand,
                    P.category AS pdtgroup,
                    P.name AS pdtmodel,
                    SII.product_code AS p_code,
                    SII.serial_number as s_no,
                    SII.qty AS so_qty,
                    SII.unit_price AS u_price,
                    P.reference_cost AS refcost,
                    P.average_cost AS netcost,
                    P.product_spec AS free_desc,
                    SI.remark AS sm_remark,
                    SI.create_time as sm_txtime
                FROM pos.sales_invoice SI
                LEFT JOIN pos.shop S
                ON SI.shop_id = S.id
                LEFT JOIN pos.sales_invoice_items SII
                ON SI.id = SII.sales_invoice_id
                LEFT JOIN pos.product P
                ON SII.product_id = P.id
                LEFT JOIN pos.brand B
                ON P.brand_id = B.id
                WHERE 1  $where_clause
                ORDER BY SI.create_time
            ");


        return $list_data;
    }


	public static function getReportList()
    {
        $where_clause = null;

        $period_year = intval(Input::get("period_year"));
        $period_month = intval(Input::get("period_month"));
        $shop = parent::escape(Input::get("shop"));
        $sales = parent::escape(Input::get("sales"));


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
