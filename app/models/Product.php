<?php

class Product extends BaseModel
{
    const PAGE_LIMIT = 100;

    public static function getTotalRecords($where_clause = null)
    {
        $data = DB::select("
            SELECT COUNT(*) AS `total_records`
            FROM `product`
            WHERE 1 $where_clause
        ");

        return $data[0]['total_records'];
    }

    public static function getPopupList()
    {
        $page = (Input::has("page")) ? intval(Input::get("page")) : 1;
        $where_clause = null;

        if (Input::has("search_product_upc")) {
            $product_upc = Input::get("search_product_upc");
            $where_clause .= " AND `barcode` LIKE '%$product_upc%'";
        }

        if (Input::has("search_product_name")) {
            $product_name = Input::get("search_product_name");
            $where_clause .= " AND `name` LIKE '%$product_name%'";
        }

        $total_records_data = DB::select("
            SELECT SQL_NO_CACHE COUNT(*) AS `total_records`
            FROM `product`
            WHERE 1 $where_clause
        ");

        $total_records = $total_records_data[0]['total_records'];
        $total_pages = intval(ceil($total_records / self::PAGE_LIMIT));
        $start_record = ($page - 1) * self::PAGE_LIMIT;

        $list_data = DB::select("
            SELECT SQL_NO_CACHE
                `barcode` AS `upc`, `name`, unit_price, `id` AS `product_id`,
				`required_imei`
            FROM `product`
            WHERE 1 $where_clause
            ORDER BY `upc`
            LIMIT $start_record, " . self::PAGE_LIMIT
        );
        return array(
            'list_data' => $list_data,
            'total_records' => $total_records,
            'total_pages' => $total_pages
        );
    }

    public static function getProductList($page = 1, $where_clause = null)
    {
        $start_record = ($page - 1) * self::PAGE_LIMIT;
        $data = DB::select("
            SELECT SQL_NO_CACHE
      				P.*, B.`id` AS `brand_id`,
      				B.`name` AS `brand_name`
                  FROM `product` AS P
      			LEFT JOIN `brand` AS B
      			ON P.`brand_id` = B.`id`
            WHERE 1 $where_clause
            ORDER BY P.`id` DESC
            LIMIT $start_record, " . self::PAGE_LIMIT
        );
        return $data;
    }
	public static function getProductDetailData($product_id)
	{
		$data = DB::select("
			SELECT
				`barcode` AS `gi_barcooe`,
				`unit_price` AS `gi_unit_price`,
				`id` AS `gi_product_id`
			FROM `product`
			WHERE `id` = '$product_id';
		");
		return $data[0];
	}

    public static function validateItems($items, $rules)
    {
        $total_items = 0;
        $total_qty = 0;
        $total_amount = 0.00;
        foreach ($items as $value) {
            extract($value);
            $qty = intval($qty);
            $unit_price = floatval($unit_price);
            $v = Validator::make($value, $rules);
            if ($v->fails() || $qty <= 0 || $unit_price < 0.00) {
                return null;
            }

            $total_items++;
            $total_qty += $qty;
            $total_amount += ($unit_price * $qty);
        }

        return array(
            'total_items' => $total_items,
            'total_qty' => $total_qty,
            'total_amount' => $total_amount
        );
    }

    public static function getStockLevel($page)
    {
        $where_clause = null;
        $join_clause = null;

        if (Input::has("search_product_category")) {
            $product_category = intval(Input::get("search_product_category"));
            $where_clause .= " AND `p`.`category_id` = '$product_category' ";
        }

        $shop_id = null;
        if (Input::has("search_shop_id")) {
            $shop_id = intval(Input::get("search_shop_id"));
            $join_clause = " LEFT JOIN `stock` AS `s` ON `s`.`product_id` = `p`.`id` ";
            $where_clause .= " AND `s`.`shop_id` = '$shop_id' ";
        }

        if (Input::has("search_product_name")) {
            $product_name = Input::get("search_product_name");
            $where_clause .= " AND `p`.`name` LIKE '%$product_name%' ";
        }

        if (Input::has("search_product_upc")) {
            $product_barcode = Input::get("search_product_upc");
            $where_clause .= " AND `p`.`barcode` LIKE '%$product_barcode%' ";
        }

        if (Input::has("search_have_qty_item_only") && $join_clause != null) {
            $where_clause .= " AND `s`.`qty` > 0";
        }

        $total_records_data = DB::select("
            SELECT SQL_NO_CACHE
                COUNT(*) AS `total_records`
            FROM `product` AS `p` $join_clause
            WHERE 1 $where_clause
        ");

        $total_records = $total_records_data[0]['total_records'];
        $total_pages = intval(ceil($total_records / self::PAGE_LIMIT));
        $start_record = ($page - 1) * self::PAGE_LIMIT;

        $list_data = null;
        if ($total_records > 0) {
            $list_data = DB::select("
                SELECT SQL_NO_CACHE
                    `p`.`id`, `p`.`barcode`, `p`.`name`, `p`.`photo` AS `product_image`, `pc`.`id` AS `category_id`,
                    `pc`.`name` AS `category_name`
                FROM `product` AS `p`
                LEFT JOIN `product_category` AS `pc`
                    ON `p`.`category_id` = `pc`.`id` $join_clause
                WHERE 1 $where_clause
                ORDER BY `id`
                LIMIT $start_record, " . self::PAGE_LIMIT
            );
        }

        return array(
            'list_data' => $list_data,
            'total_records' => $total_records,
            'total_pages' => $total_pages,
            'have_record' => ($total_records > 0) ? true : false
        );
    }

    public static function getInfo($params)
    {
        extract($params);
        $where_clause = null;

        if (isset($product_upc)) {
            $product_upc = parent::escape($product_upc);
            $where_clause .= " AND `barcode` = $product_upc ";
        }

        $data = DB::select("
            SELECT SQL_NO_CACHE
				`id`, `name`
            FROM `product`
            WHERE 1 $where_clause
            LIMIT 0, 1
        ");

        if (empty($data)) {
            return null;
        }

        return $data[0];
    }

    public static function searchProductList($page = 1, $where_clause = null)
    {
		$total_product = DB::select("
		SELECT SQL_NO_CACHE
		  COUNT(*) AS `total_product`
		FROM `product` AS P
		LEFT JOIN `brand` AS B
		  ON (P.`brand_id` = B.`id`)
		WHERE $where_clause
		");

		$total_pages = intval(ceil($total_product[0]['total_product'] / 100));

		$start_record = ($page - 1) * 100;

		$product_list = DB::select("
			SELECT SQL_NO_CACHE
				P.*, B.`id` AS `brand_id`,
				B.`name` AS `brand_name`
            FROM `product` AS P
			LEFT JOIN `brand` AS B
			ON (P.`brand_id` = B.`id`)
			WHERE $where_clause
            ORDER BY P.`id` DESC
			LIMIT $start_record, " . self::PAGE_LIMIT
        );

        return array(
            'search_total_pages' => $total_pages,
            'current_pages' => $page,
            'product_list' => $product_list
        );
    }

	public static function searchGoodsInProduct()
	{
		$keyword = Input::get("keyword");
		$data = DB::select("
			SELECT * FROM product
			WHERE barcode = '$keyword'
		");

		return $data[0];
	}

    public static function createProduct()
    {
        extract(Input::all());
        $product_upc = parent::escape($product_upc);
        $product_brand = parent::escape($product_brand_id);
        $product_name = parent::escape($product_name);
        $unit_price = parent::escape($unit_price);
        $reference_cost = parent::escape($reference_cost);
		$average_cost = ($unit_price + $reference_cost) / 2;
        $product_spec = parent::escape($product_spec);
        $product_remark = parent::escape($product_remark);
        $product_category = parent::escape($product_category);
        $product_category_name = Category::getCategoryName($product_category);
        $required_imei = Input::get("required_imei");

        $result = DB::insert("
          INSERT INTO `product`
          SET
            `brand_id` = $product_brand,
            `barcode` = $product_upc,
            `name` = $product_name,
            `unit_price` = $unit_price,
            `reference_cost` = $reference_cost,
            `average_cost` = $average_cost,
            `product_spec` = $product_spec,
			`remark` = $product_remark,
			`category_id` = $product_category,
			`category` = '$product_category_name',
            `update_time` = NOW(),
            `create_time` = NOW(),
			`required_imei` = '$required_imei',
            `update_by` = '" . Session::get("staff_name"). "'
        ");

		if ($result) {
			$id = DB::getPdo()->lastInsertId();
			return $id;
		}
        return 0;
    }

	public static function checkProduct()
	{
		extract(Input::all());
		$data = DB::select("SELECT `barcode` FROM `product` WHERE `barcode` = '$product_upc'");
		if($data){
			return 0;
		}
		return 1;
	}

	public static function uploadImages()
	{
		extract(Input::all());
		$product_id = $id;
		$file_name = $product_id. ".png";

		$tempPath = $_FILES['file']['tmp_name'];
		$targetPath = str_replace('\\', '/', dirname(dirname(__FILE__))) . "/images/product/".$file_name;
		$result = DB::update("
			UPDATE product
			SET
				`photo` = '$file_name'
			WHERE `id` = '$product_id'
			LIMIT 1
		");

		$check = DB::select("
			SELECT photo FROM Product
			WHERE `id` = '$product_id'
			LIMIT 1
		");

		$check_photo = $check[0]['photo'];

		if(!empty($check_photo)){
			move_uploaded_file($tempPath, $targetPath);
			return 1;
		}
		return 0;
	}

    public static function removeProduct()
    {
		$product_id = intval(Input::get("product_id"));
		$result = DB::delete("
		  DELETE FROM `product`
		  WHERE `id` = '$product_id'
		  LIMIT 1
		");

		$file_name = $product_id. ".png";
		if (file_exists(str_replace('\\', '/', dirname(dirname(__FILE__))) . "/images/product/". $file_name)) {
			unlink(str_replace('\\', '/', dirname(dirname(__FILE__))) . "/images/product/". $file_name);
		}

		if ($result) {
			return 1;
		}
		return 0;
	}

    public static function editProduct()
    {
        $product_id = intval(Input::get("product_id"));
        $product_upc = Input::get("product_upc");
        $product_brand = Input::get("product_brand");
        $product_name = Input::get("product_name");
        $product_unit_price = Input::get("product_unit_price");
        $product_reference_cost = Input::get("product_reference_cost");
        $product_spec = Input::get("product_spec");
        $product_remark = Input::get("product_remark");
        $product_category = Input::get("product_category");
        $product_category_name = Category::getCategoryName($product_category);
        $required_imei = Input::get("required_imei");
        $remove_images = Input::get("remove_images");
		$file_name = $product_id. ".png";
		$where_clause = null;

		if ($remove_images == $product_id){
			unlink(dirname(dirname(__FILE__)) . "/images/product/". $file_name);
			$where_clause = "`photo` = '',";
		}

		$product_average_cost = ($product_unit_price + $product_reference_cost) / 2;

        $result = DB::update("
            UPDATE `product`
			SET
				`brand_id` = '$product_brand',
				`barcode` = '$product_upc',
				`name` = '$product_name',
				`unit_price` = '$product_unit_price',
				`reference_cost` = '$product_reference_cost',
				`average_cost` = '$product_average_cost',
				`product_spec` = '$product_spec',
				`remark` = '$product_remark',
				`category_id` = '$product_category',
				`category` = '$product_category_name',
				`required_imei` = '$required_imei',
				$where_clause
				`update_time` = NOW()
            WHERE `id` = $product_id
            LIMIT 1
        ");

        if ($result) {
            return 1;
        }
        return 0;
    }

    public static function searchProductByName($shop_code, $keyword)
    {
        $search_shop = "";
        if (count($shop_code) > 0) {
            $search_shop = " and I.IY_TXSHOP ='".$shop_code."' ";
        }

        $data = DB::select("
            SELECT
                I.IY_TXSHOP AS SHOPCODE, I.IY_SNO AS SNO, P.name AS PRODUCT_NAME, I.IY_QTY AS QTY,
                PP.REFCOST AS COST, PP.RETAIL AS RETAIL, P.barcode AS PRODUCT_CODE
            FROM pos.PRODUCT AS P
            LEFT JOIN pos.INVENTY AS I
                ON P.barcode = I.IY_PCODE
            LEFT JOIN pos.PRODUCTPRICE AS PP
                ON P.barcode = PP.PP_MOUNT
            WHERE P.name LIKE '%".$keyword."%' ".$search_shop."
        ");

        if (empty($data)) {
            return null;
        }

        return $data;
    }

    public static function searchProductBySNO($shop_code, $keyword)
    {
        $search_shop = "";
        if (count($shop_code) > 0) {
            $search_shop = " and I.IY_TXSHOP ='".$shop_code."' ";
        }

        $data = DB::select("
            SELECT
                I.IY_TXSHOP AS SHOPCODE, I.IY_SNO AS SNO, P.name AS PRODUCT_NAME, I.IY_QTY AS QTY,
                PP.REFCOST AS COST, PP.RETAIL AS RETAIL, P.barcode AS PRODUCT_CODE
            FROM pos.PRODUCT AS P
            LEFT JOIN pos.INVENTY AS I
                ON P.barcode = I.IY_PCODE
            LEFT JOIN pos.PRODUCTPRICE AS PP
                ON P.barcode = PP.PP_MOUNT
            WHERE I.IY_SNO = '".$keyword."' ".$search_shop."
        ");

        if (empty($data)) {
            return null;
        }

        return $data;
    }

    public static function GetProduct($product_code, $serial_number){

         $data = DB::select("SELECT I.IY_TXSHOP AS SHOPCODE, I.IY_SNO AS SNO, P.name AS PRODUCT_NAME, I.IY_QTY AS QTY, PP.REFCOST AS COST, PP.RETAIL AS RETAIL, P.barcode AS PRODUCT_CODE
            FROM pos.PRODUCT AS P
            LEFT JOIN pos.INVENTY AS I
            ON P.barcode = I.IY_PCODE
            LEFT JOIN pos.PRODUCTPRICE AS PP
            ON P.barcode = PP.PP_MOUNT
            WHERE P.barcode = '".$product_code."'
            AND I.IY_SNO = '".$serial_number."'
        ");

        if (empty($data)) {
            return '1';
        }

        return $data;
    }

    public static function getPOListProducts($barcode, $shopcode) {
        $search_shop = "";
        if (count($shopcode) > 0) {
            $search_shop = " and I.IY_TXSHOP ='".$shopcode."' ";
        }

        $data = DB::select("
            SELECT SQL_NO_CACHE
                I.IY_TXSHOP AS SHOPCODE, I.IY_SNO AS SNO, P.name AS PRODUCT_NAME, I.IY_QTY AS QTY,
                PP.REFCOST AS COST, PP.RETAIL AS RETAIL, P.barcode AS PRODUCT_CODE
            FROM pos.PRODUCT AS P
            LEFT JOIN pos.INVENTY AS I
                ON P.barcode = I.IY_PCODE
            LEFT JOIN pos.PRODUCTPRICE AS PP
                ON P.barcode = PP.PP_MOUNT
            WHERE P.P_BARCODE = '".$barcode."' ".$search_shop."
            LIMIT 0, 1
        ");

        if (empty($data)) {
            return null;
        }

        return $data[0];
    }

    public static function getProductByUPC($product_upc = null)
    {
        if ($product_upc === null) {
            $product_upc = Input::get("product_upc");
        }

        $product_upc = parent::escape($product_upc);
        $data = DB::select("
            SELECT SQL_NO_CACHE `name` AS `product_name`
            FROM `product`
            WHERE `barcode` = $product_upc
            LIMIT 0, 1;
        ");

        if (empty($data)) {
            return null;
        }

        return $data[0];
    }

    public static function GetProductBySNO($shopcode, $serial_number){
        $search_shop = "";
        if(count($shopcode) > 0){
            $search_shop = " and I.IY_TXSHOP ='".$shopcode."' ";
        }

        $data = DB::select("
            SELECT
                I.IY_TXSHOP AS SHOPCODE, I.IY_SNO AS SNO, P.name AS PRODUCT_NAME, I.IY_QTY AS QTY,
                PP.REFCOST AS COST, PP.RETAIL AS RETAIL, P.barcode AS PRODUCT_CODE
            FROM pos.PRODUCT AS P
            LEFT JOIN pos.INVENTY AS I
                ON P.barcode = I.IY_PCODE
            LEFT JOIN pos.PRODUCTPRICE AS PP
                ON P.barcode = PP.PP_MOUNT
            WHERE I.IY_SNO = '".$serial_number."' ".$search_shop."
            LIMIT 0, 1
        ");

        if (empty($data)) {
            return '2';
        }

        return $data;
    }

    public static function getProductByProductCode($shopcode, $product_code){
       $search_shop = "";
        if(count($shopcode) > 0){
            $search_shop = " and I.IY_TXSHOP ='".$shopcode."' ";
        }

        $data = DB::select("
            SELECT
                I.IY_TXSHOP AS SHOPCODE, I.IY_SNO AS SNO, P.name AS PRODUCT_NAME, I.IY_QTY AS QTY,
                PP.REFCOST AS COST, PP.RETAIL AS RETAIL, P.barcode AS PRODUCT_CODE
            FROM pos.PRODUCT AS P
            LEFT JOIN pos.INVENTY AS I
                ON P.barcode = I.IY_PCODE
            LEFT JOIN pos.PRODUCTPRICE AS PP
                ON P.barcode = PP.PP_MOUNT
            WHERE P.P_MOUNT = '".$product_code."' ".$search_shop."
            LIMIT 0, 1
        ");

        if (empty($data)) {
            return null;
        }

        return $data;
    }

    public static function setKeywordSession()
    {
		$keyword = Input::get("keyword");

		if($keyword != ''){
			Session::put('product_keyword', $keyword);
		}else{
			Session::put('product_keyword', '');
		}
        return 1;
    }


}
