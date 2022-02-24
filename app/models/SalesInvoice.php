<?php
class SalesInvoice extends BaseModel
{
	const PAGE_LIMIT = 20;

	const STATUS_CONFIRMED = 1;
	const STATUS_PENDING = 2;
	const STATUS_VOIDED = 3;
	const STATUS_FINISHED = 1;

	const ORDER_STATUS_ON_HOLD = -2;        // Stock is on holding
	const ORDER_STATUS_SUSPENDED = -1;      //
	const ORDER_STATUS_FAIL = 0;            // Payment failed
	const ORDER_STATUS_PENDING = 1;         // Complete payment, coupon not generated
	const ORDER_STATUS_COMPLETED = 2;       // Complete payment, coupon generated
	const ORDER_CODE = "sm";

    public function __construct() {
  		ini_set("log_speeds", 1);
  		ini_set("error_log", Config::get('path.LOG_QUERY_TESTSPEED'));
  	}

    /**
     * 	Create new order
     * 	@used-by (controller) ShoppingCartController
     * 	@used-by (controller) CheckoutProcessController
     * 	@param array $cart_params The shopping cart's parameters
     * 	@return array Array of order number
     */
    public static function CreateOrder()
    {
		$shopcode = Session::get('shop_code');
		$sales_name = Input::get("sales_name");
		$cashier_code = Input::get('cashier_id');

		$status = Input::get("status");

		$staff = Session::get('staff_id');
		$shop_id = Shop::getID($shopcode);
		$cashier = Staff::getStaffDetailByCode($cashier_code);
		extract($cashier, EXTR_PREFIX_ALL, "staff");
		$cashier_id = $staff_id;
		$cashier_code = $staff_staff_code;
		$sales= Staff::getStaffDetailByName($sales_name);
		extract($sales, EXTR_PREFIX_ALL, "sales");
		$sales_id = $sales_id;
		$sales_code = $sales_staff_code;
		$deposit_id = Input::get('deposit_id');
		$quotation_id = Input::get('quotation_id');
		$deposit_number = null;
		$deposit_amount = null;

  		if($deposit_id!=null){
    		$deposit_number = Deposit::getDepositNumber($deposit_id);
  			$deposit_amount = Deposit::getDepositPrice($deposit_id);
  		}

		$discount = Input::get('total_discount');
		$discount_type = Input::get('total_discount_type');
		$total_amount = Input::get('total_amount');
		$total_deposit_amount = Input::get('total_deposit_amount');
		$net_total_amount = Input::get('net_total_amount');
		$term = Input::get('term');
		$term_list = Config::get('cod_terms');
		$term_name = $term_list[$term];
		$remarks = Input::get('remark');

		$cart = Input::get("cart");
		$payment = Input::get("payment");

		$data = DB::select("
			SELECT MAX(reciept_id)+1 AS reciept_id
			FROM sales_invoice
			WHERE shop_id = '".$shop_id."'
		");
		extract($data[0]);

  		if ($reciept_id === null){
  			$reciept_id = '1';
  		}

		$shopsyb = Shop::getShopSymbol($shopcode);
		$yy = date("y");
		$sales_invoice_number = self::ORDER_CODE.$yy."-".$shopsyb."01-".sprintf("%05d", $reciept_id);
		$add_record = DB::insert("
		  INSERT INTO `sales_invoice` (
			  `shop_id`,
			  `sales_invoice_number`,
			  `reciept_id`,
			  `cashier_id`,
			  `cashier_code`,
			  `sales_id`,
			  `sales_code`,
			  `deposit_id`,
			  `quotation_id`,
			  `deposit_number`,
			  `discount`,
			  `discount_type`,
			  `total_amount`,
			  `deposit_payment_amount`,
			  `net_total_amount`,
			  `term`,
			  `term_name`,
			  `status`,
			  `remark`,
			  `last_update_by`,
			  `last_update_time`,
			  `create_time`
		  ) VALUES (
			  '$shop_id',
			  '".strtoupper($sales_invoice_number)."',
			  '$reciept_id',
			  '$cashier_id',
			  '$cashier_code',
			  '$sales_id',
			  '$sales_code',
			  '$deposit_id',
			  '$quotation_id',
			  '$deposit_number',
			  '$discount',
			  '$discount_type',
			  '$total_amount',
			  '$total_deposit_amount',
			  '$net_total_amount',
			  '$term',
			  '$term_name',
			  '$status',
			  '$remarks',
			  '$staff',
			  NOW(),
			  NOW()
		  )
		");

		$sales_invoice_id = DB::getPdo()->lastInsertId();

		if ($add_record) {
			$item_total = 0;
			$net_total_item = 0;
			$item_deposit_id = null;
			$item_quotation_id = null;
			foreach ($cart as $cart_id => $item) {
				if(!empty($item["deposit_id"])){
					$item_quotation_id = null;
					$item["stock_id"] = "";
					$item_total_price = $item["product_unit_price"] * $item["product_qty"];
					$item_deposit_id = $item["deposit_id"];
				}else if(!empty($item["quotation_id"])){
					$item_deposit_id = null;
					$item["stock_id"] = "";
					$item_total_price = $item["product_unit_price"] * $item["product_qty"];
					$item_quotation_id = $item["quotation_id"];
				}else{
					$item_deposit_id = null;
					$item_quotation_id = null;
					$item_total_price = ($item["product_unit_price"] * $item["product_qty"]) - $item["product_discount"];
				}
				$item_total += $item_total_price;

				$add_items_record = DB::insert("
					INSERT INTO `sales_invoice_items` (
						`sales_invoice_id`,
						`sales_invoice_number`,
						`stock_id`,
						`deposit_id`,
						`quotation_id`,
						`product_id`,
						`product_code`,
						`serial_number`,
						`unit_price`,
						`qty`,
						`discount`,
						`total_price`,
						`last_update_by`,
						`last_update_time`,
						`create_time`
					) VALUES (
						'$sales_invoice_id',
						'".strtoupper($sales_invoice_number)."',
						'".$item["stock_id"]."',
						'$item_deposit_id',
						'$item_quotation_id',
						'".$item["product_id"]."',
						'".$item["product_code"]."',
						'".$item["serial_number"]."',
						'".$item["product_unit_price"]."',
						'".$item["product_qty"]."',
						'".$item["product_discount"]."',
						'$item_total_price',
						'$staff',
						NOW(),
						NOW()
					)
				");

				if($add_items_record){
					if(!empty($item["stock_id"])){
						if($item["stock_id"] != ""){
							$get_stock_qty = DB::select("
								SELECT qty FROM stock WHERE id = '".$item["stock_id"]."'
							");
							$stock_qty = $get_stock_qty[0]['qty'];
							$stock_qty_count = $stock_qty - $item["product_qty"];

							if(!empty($get_stock_qty) && $status == "1"){
								$update_stock_qty = DB::statement("
									UPDATE `stock`
									SET
										`qty` = '$stock_qty_count',
										`last_update_by` = '$staff',
										`last_update` = NOW()
									WHERE `id` = '".$item["stock_id"]."'
								");
							}
						}
					}
				}
			}
			foreach ($payment as $payment_id => $payment_item) {
				$add_payment_items = DB::insert("
					INSERT INTO `payment` (
						`shop_id`,
						`shop_code`,
						`sales_invoice_id`,
						`sales_invoice_number`,
						`amount`,
						`payment_type`,
						`payment_type_name`,
						`createtime`
					) VALUES (
					  '$shop_id',
					  '".$shopcode."',
					  '$sales_invoice_id',
					  '".strtoupper($sales_invoice_number)."',
					  '".$payment_item["payment_method_amount"]."',
					  '".$payment_item["payment_method_id"]."',
					  '".$payment_item["payment_method_name"]."',
					  NOW()
					)
				");
			}
			$item_total_count = 0;
			$item_total_count = $item_total - $total_deposit_amount;
			if ($discount_type == 1) {
				$net_total_item =  $item_total_count - $discount;
			}else if ($discount_type == 2) {
				$net_total_item = $item_total_count * ((100 - $discount) / 100);
			}else{
				$net_total_item = $item_total_count;
			}

			$total_item = count(self::GetSalesInvoiceItems($sales_invoice_id));
			$total_qty_item = self::GetSalesInvoiceQty($sales_invoice_id);

			$update_total_price_result = DB::statement("
				UPDATE `sales_invoice`
				SET
					`total_amount` = '$item_total',
					`deposit_payment_amount` = '$total_deposit_amount',
					`net_total_amount` = '$net_total_item',
					`total_item` = '$total_item',
					`total_qty` = '$total_qty_item',
					`last_update_time` = NOW()
				WHERE `id` = '$sales_invoice_id'
			");

			if($status == 1){
				if(!empty($quotation_id)){
					Quotation::updateQuotationStatus($quotation_id);
				}
				if(!empty($deposit_id)){
					Deposit::updateDepositStatus($deposit_id);
				}
				Session::put('sales_list_status', '1');
			}else{
				Session::put('sales_list_status', '2');
			}

			if($update_total_price_result){
			  return $sales_invoice_id;
			}
		}
			return 0;
    }

    public static function UpdateOrder()
    {
		$record_id = Input::get('sales_invoice_edit_id');
		$shopcode = Session::get('shop_code');
		$sales_name = Input::get("sales_name");
		$cashier_code = Input::get('cashier_id');
		$status = Input::get("status");

		$staff = Session::get('staff_id');
		$shop_id = Shop::getID($shopcode);
		$cashier = Staff::getStaffDetailByCode($cashier_code);
		extract($cashier, EXTR_PREFIX_ALL, "staff");
		$cashier_id = $staff_id;
		$cashier_code = $staff_staff_code;
		$sales= Staff::getStaffDetailByName($sales_name);
		extract($sales, EXTR_PREFIX_ALL, "sales");
		$sales_id = $sales_id;
		$sales_code = $sales_staff_code;

		$deposit_id = Input::get('deposit_id');
		$quotation_id = Input::get('quotation_id');
		$old_deposit_id = Input::get('old_deposit_id');
		$old_quotation_id = Input::get('old_quotation_id');
		$item_deposit = Input::get('deposit_payment_amount');
		$deposit_id = ($deposit_id == '') ? 0 : $deposit_id;
		$quotation_id = ($quotation_id == '') ? 0 : $quotation_id;
		$deposit_number = null;
		$deposit_amount = null;

  		if($deposit_id != '0'){
    		$deposit_number = Deposit::getDepositNumber($deposit_id);
  			$deposit_amount = Deposit::getDepositPrice($deposit_id);
  		}
		$discount = Input::get('total_discount');
		$discount_type = Input::get('total_discount_type');
		$total_amount = Input::get('total_amount');
		$net_total_amount = Input::get('net_total_amount');
		$term = Input::get('term');
		$term_list = Config::get('cod_terms');
		$term_name = $term_list[$term];
		$remarks = Input::get('remark');

		$remove_cart_ids = Input::get('remove_cart_ids');
		$remove_payment_ids = Input::get('remove_payment_ids');

		$cart = Input::get("cart");
		$cart_new = Input::get("cart_new");

		$payment = Input::get("payment");
		$payment_new = Input::get("payment_new");
		$shopsyb = Shop::getShopSymbol($shopcode);
		$sales_invoice_number = Self::getSalesInvoiceNumber($record_id);

		$update_record = DB::insert("
			UPDATE `sales_invoice` SET
			  `shop_id` = '$shop_id',
			  `cashier_id` = '$cashier_id',
			  `cashier_code` = '$cashier_code',
			  `deposit_id` = '$deposit_id',
			  `quotation_id` = '$quotation_id',
			  `deposit_number` = '$deposit_number',
			  `discount` = '$discount',
			  `discount_type` = '$discount_type',
			  `total_amount` = '$total_amount',
			  `net_total_amount` = '$net_total_amount',
			  `term` = '$term',
			  `term_name` = '$term_name',
			  `remark` = '$remarks',
			  `status` = '$status',
			  `last_update_by` = '$staff',
			  `last_update_time` = NOW()
			  WHERE `id` = '$record_id'
			");

		if ($update_record) {
			$item_total_new = 0;
			$item_total_update = 0;
			$item_total_price_new = 0;
			$item_total_price_update = 0;
			$net_total_item = 0;
			$item_deposit_id = null;
			$item_quotation_id = null;

			if(!empty($cart_new)){
				foreach ($cart_new as $cart_new_item) {
					if($cart_new_item["deposit_id"] != 0){
						$item_quotation_id = null;
						$cart_new_item["stock_id"] = "";
						$item_total_price_new = $cart_new_item["product_unit_price"] * $cart_new_item["product_qty"];
						$item_deposit_id = $cart_new_item["deposit_id"];
					}else if(!empty($cart_new_item["quotation_id"])){
						$item_deposit_id = null;
						$cart_new_item["stock_id"] = "";
						$item_total_price_new = $cart_new_item["product_unit_price"] * $cart_new_item["product_qty"];
						$item_quotation_id = $cart_new_item["quotation_id"];
					}else{
						$item_deposit_id = null;
						$item_quotation_id = null;
						$item_total_price_new = ($cart_new_item["product_unit_price"] * $cart_new_item["product_qty"]) - $cart_new_item["product_discount"];
					}
					$item_total_new += $item_total_price_new;
					$add_items_record = DB::insert("
						INSERT INTO `sales_invoice_items` (
								`sales_invoice_id`,
								`sales_invoice_number`,
								`stock_id`,
								`deposit_id`,
								`quotation_id`,
								`product_id`,
								`product_code`,
								`serial_number`,
								`unit_price`,
								`qty`,
								`discount`,
								`total_price`,
								`last_update_by`,
								`last_update_time`,
								`create_time`
						) VALUES (
							'$record_id',
							'".strtoupper($sales_invoice_number)."',
							'".$cart_new_item["stock_id"]."',
							'$item_deposit_id',
							'$item_quotation_id',
							'".$cart_new_item["product_id"]."',
							'".$cart_new_item["product_code"]."',
							'".$cart_new_item["serial_number"]."',
							'".$cart_new_item["product_unit_price"]."',
							'".$cart_new_item["product_qty"]."',
							'".$cart_new_item["product_discount"]."',
							'".sprintf("%.2f", $item_total_price_new)."',
							'$staff',
							NOW(),
							NOW()
						)
					");

					if($status == "1"){
						if($cart_new_item["stock_id"] != ""){
							$get_stock_qty = DB::select("
								SELECT qty FROM stock WHERE id = '".$cart_new_item["stock_id"]."'
							");
							$stock_qty = $get_stock_qty[0]['qty'];
							$stock_qty_count = $stock_qty - $cart_new_item["product_qty"];
							if($stock_qty_count > 0){
								$update_stock_qty_new = DB::statement("
									UPDATE `stock`
									SET
										`qty` = '$stock_qty_count',
										`last_update_by` = '$staff',
										`last_update` = NOW()
									WHERE `id` = '".$cart_new_item["stock_id"]."'
								");
							}else{
								return false;
							}
						}
					}
				}
			}

			if(!empty($cart)){
				foreach ($cart as $cart_item) {
					if($cart_item["deposit_id"] != "0"){
						$item_quotation_id = null;
						$cart_item["stock_id"] = "";
						$item_total_price_update = $cart_item["product_unit_price"] * $cart_item["product_qty"];
						$item_deposit_id = $cart_item["deposit_id"];
					}else if(!empty($item["quotation_id"])){
						$item_deposit_id = null;
						$cart_item["stock_id"] = "";
						$item_total_price_update = $cart_item["product_unit_price"] * $cart_item["product_qty"];
						$item_quotation_id = $cart_item["quotation_id"];
					}else{
						$item_deposit_id = null;
						$item_quotation_id = null;
						$item_total_price_update = ($cart_item["product_unit_price"] * $cart_item["product_qty"]) - $cart_item["product_discount"];
					}
					$item_total_update += $item_total_price_update;

					$update_items_record = DB::insert("
						UPDATE `sales_invoice_items` SET
							`unit_price` = '".$cart_item["product_unit_price"]."',
							`qty` = '".$cart_item["product_qty"]."',
							`discount` = '".$cart_item["product_discount"]."',
							`total_price` = '".sprintf("%.2f", $item_total_price_update)."',
							`last_update_by` = '$staff',
							`last_update_time` = NOW()
						WHERE `id` = '".$cart_item["product_item_id"]."'
					");

					if($update_items_record){
						if(!empty($cart_item["stock_id"])){
							if($status == "1"){
								$get_stock_qty = DB::select("
									SELECT qty FROM stock WHERE id = '".$cart_item["stock_id"]."'
								");
								$stock_qty = $get_stock_qty[0]['qty'];
								$stock_qty_count = $stock_qty - $cart_item["product_qty"];

								if($stock_qty_count > 0){
									$update_stock_qty_update = DB::statement("
										UPDATE `stock`
											SET
										`qty` = '$stock_qty_count',
										`last_update_by` = '$staff',
										`last_update` = NOW()
										WHERE `id` = '".$cart_item["stock_id"]."'
									");
								}else{
									return false;
								}
							}
						}
					}
				}
			}
		// insert payment db
			if(!empty($payment_new)){
				foreach ($payment_new as $payment_item_new) {
				  $add_payment_items = DB::insert("
					  INSERT INTO `payment` (
						  `shop_id`,
						  `shop_code`,
						  `sales_invoice_id`,
						  `sales_invoice_number`,
						  `amount`,
						  `payment_type`,
						  `payment_type_name`,
						  `createtime`
					  ) VALUES (
						'$shop_id',
						'".$shopcode."',
						'$record_id',
						'".strtoupper($sales_invoice_number)."',
						'".$payment_item_new["payment_method_amount"]."',
						'".$payment_item_new["payment_method_id"]."',
						'".$payment_item_new["payment_method_name"]."',
						NOW()
					  )
				  ");
				}
			}

			if(!empty($payment)){
				foreach ($payment as $payment_item) {
				  $update_payment_items = DB::insert("
					UPDATE `payment` SET
					  `shop_id` = '$shop_id',
					  `shop_code` = '".$shopcode."',
					  `sales_invoice_id` = '$record_id',
					  `sales_invoice_number` = '".strtoupper($sales_invoice_number)."',
					  `amount` = '".$payment_item["payment_method_amount"]."',
					  `payment_type` = '".$payment_item["payment_method_id"]."',
					  `payment_type_name` = '".$payment_item["payment_method_name"]."'
					  WHERE `id` = '".$payment_item["payment_item_id"]."'
				  ");
				}
			}

			if($old_deposit_id != $deposit_id && $old_deposit_id != 0){
				$remove_si_items = DB::delete("
					DELETE FROM `sales_invoice_items`
					WHERE `sales_invoice_id` = '".$record_id."'
					AND `deposit_id` = $old_deposit_id
				");
			}

			if($old_quotation_id != $quotation_id && $old_quotation_id != 0){
				$remove_si_items = DB::delete("
					DELETE FROM `sales_invoice_items`
					WHERE `sales_invoice_id` = '".$record_id."'
					AND `quotation_id` = $old_quotation_id
				");
			}
			// exit;
			if(!empty($remove_cart_ids)){
				$remove_si_items = DB::delete("
					DELETE FROM `sales_invoice_items`
					WHERE `id` IN ($remove_cart_ids)
				");
			}

			if(!empty($remove_payment_ids)){
				$remove_si_items = DB::delete("
					DELETE FROM `payment`
					WHERE `id` IN ($remove_payment_ids)
				");
			}

			$item_total_count = 0;
			$item_total = $item_total_new + $item_total_update;
			$item_total_count = $item_total - $item_deposit;
			if ($discount_type == 1) {
				$net_total_item =  $item_total_count - $discount;
			}else if ($discount_type == 2) {
				$net_total_item = $item_total_count * ((100 - $discount) / 100);
			}else{
				$net_total_item = $item_total_count;
			}

			$total_item = count(self::GetSalesInvoiceItems($record_id));
			$total_qty_item = self::GetSalesInvoiceQty($record_id);

			$update_total_price_result = DB::statement("
				UPDATE `sales_invoice`
				SET
					`total_amount` = '$item_total',
					`deposit_payment_amount` = '$item_deposit',
					`net_total_amount` = '$net_total_item',
					`total_item` = '$total_item',
					`total_qty` = '$total_qty_item',
					`last_update_time` = NOW()
				WHERE `id` = '$record_id'
			");

			if($status == 1){
				if(!empty($quotation_id)){
					Quotation::updateQuotationStatus($quotation_id);
				}
				if(!empty($deposit_id)){
					Deposit::updateDepositStatus($deposit_id);
				}
				Session::put('sales_list_status', '1');
			}else{
				Session::put('sales_list_status', '2');
			}

			if($update_total_price_result){
			  return $record_id;
			}
		}
		return 0;
    }

    public static function getSalesInvoiceInfo($sales_invoice_id)
    {
    	$sales_invoice_id = intval($sales_invoice_id);

    	$data = DB::select("
    		SELECT SQL_NO_CACHE
    			`s`.`code` AS `shop_code`, `si`.`sales_invoice_number`
    		FROM `sales_invoice` AS `si`
    		LEFT JOIN `shop` AS `s`
    			ON `s`.`id` = `si`.`shop_id`
    		WHERE `si`.`id` = '$sales_invoice_id'
    		LIMIT 0, 1
    	");

    	return $data[0];
    }

    private static function GetNewRecieptID()
    {
        $data = DB::select("
            SELECT MAX(reciept_id) + 1 AS reciept_id
            FROM sales_invoice
            WHERE shop_id = '".$shopcode."'
        ");

        extract($data);
        return $reciept_id;
    }

    public static function GetSalesInvoice($id = null)
    {
      $sales_invoice_id = $id;

      $data = DB::select("
  			SELECT
  				`P`.`name` AS `product_name`,
				`P`.`product_spec`, `SI`.*,
				`SI`.`discount` AS `invoice_discount`,
  				`SII`.*, `SII`.`discount` AS `item_discount`,
  				`SHP`.`address` AS `shop_address`,
  				`SHP`.`telephone` AS `shop_tel`,
  				`SHP`.`fax` AS `shop_fax`,
  				`SHP`.`name` AS `shop_name`,
  				`ST`.`staff_code` AS `cashier`,
  				`ST2`.`staff_code` AS `sales`
  			FROM `sales_invoice` AS `SI`
  			LEFT JOIN `sales_invoice_items` AS `SII`
  				ON `SI`.`id` = `SII`.`sales_invoice_id`
  			LEFT JOIN `product` AS `P`
  				ON `P`.`id` = `SII`.`product_id`
  			LEFT JOIN `shop` AS `SHP`
  				ON `SI`.`shop_id` = `SHP`.`id`
  			LEFT JOIN `staff` AS `ST`
  				ON `SI`.`cashier_id` = `ST`.`id`
  			LEFT JOIN `staff` AS `ST2`
  				ON `SI`.`sales_id` = `ST2`.`id`
  			WHERE `SI`.`id` =  '$sales_invoice_id'
      ");
      if (empty($data)) {
          return null;
      }
      return $data;
    }

    public static function GetSalesInvoiceList($si_id)
    {
      $data = DB::select("
        SELECT
          `SI`.*,
          `SHP`.`code` AS `shop_code`,
          `ST`.`name` AS `cashier`,
          `ST`.`staff_code` as `cashier_code`,
          `ST2`.`name` AS `sales`,
          `ST2`.`staff_code` as `sales_code`,
          `DPS`.`deposit_number` as `deposit_no`,
          `QUO`.`quotation_number` as `quotation_no`
        FROM `sales_invoice` AS `SI`
        LEFT JOIN `shop` AS `SHP`
          ON `SI`.`shop_id` = `SHP`.`id`
        LEFT JOIN `staff` AS `ST`
          ON `SI`.`cashier_id` = `ST`.`id`
        LEFT JOIN `staff` AS `ST2`
          ON `SI`.`sales_id` = `ST2`.`id`
        LEFT JOIN `deposit` AS `DPS`
          ON `SI`.`deposit_id` = `DPS`.`id`
        LEFT JOIN `quotation` AS `QUO`
          ON `SI`.`quotation_id` = `QUO`.`id`
        WHERE `SI`.`id` =  '$si_id'
      ");

      if (empty($data)) {
          return null;
      }
      return $data;
    }

    public static function GetSalesInvoiceItems($si_id)
    {
      $data = DB::select("
        SELECT `SII`.*, `P`.`name` AS `product_name`
        FROM `sales_invoice_items` AS `SII`
        LEFT JOIN `product` AS `P`
          ON `P`.`id` = `SII`.`product_id`
        WHERE `SII`.`sales_invoice_id` = '$si_id'
      ");

      if (empty($data)) {
          return null;
      }
      return $data;
    }

    public static function GetSalesInvoiceQty($si_id)
    {
      $data = DB::select("
			SELECT
				SUM(`sii`.`qty`) as `total_qty`
			FROM `sales_invoice` AS `si`
			LEFT JOIN `sales_invoice_items` `sii`
				ON `si`.`id` = `sii`.`sales_invoice_id`
			WHERE `SII`.`sales_invoice_id` = '$si_id'
			GROUP BY `sii`.`sales_invoice_id`
      ");

      if (empty($data)) {
          return null;
      }
      return $data[0]['total_qty'];
    }

    public static function GetPaymentList($si_id)
    {
      $data = DB::select("
        SELECT
          `payment_type` as `payment_type`,
          `amount` as `payment_amount`
        FROM `payment`
        WHERE sales_invoice_id = '$si_id'
      ");

      if (empty($data)) {
          return null;
      }
      return $data;
    }

    public static function getList()
    {
        $page = (Input::has("page")) ? intval(Input::get("page")) : 1;
        $get_status = intval(Session::get('sales_list_status'));
        $sales_list_status = ($get_status != '4') ? $get_status : 1;

        $status = (Input::has("search_status") && intval(Input::get("search_status") != '0')) ? intval(Input::get("search_status")) : $sales_list_status;

        $where_clause = " AND `si`.`status` = '$status'";
        if (Input::has("search_sales_invoice_number")) {
            $sales_invoice_number = Input::get("search_sales_invoice_number");
            $where_clause .= " AND `si`.`sales_invoice_number` LIKE '%$sales_invoice_number%'";
        }
        if (Input::has("search_shop")) {
            $shop = intval(Input::get("search_shop"));
            $where_clause .= " AND `issue_shop`.`id` = '$shop'";
        }

        if (Input::has("search_from_date")) {
            $from_date = parent::escape(Input::get("search_from_date"));
            $where_clause .= " AND DATE(`si`.`create_time`) >= $from_date";
        }

        if (Input::get("search_to_date")) {
            $to_date = parent::escape(Input::get("search_to_date"));
            $where_clause .= " AND DATE(`si`.`create_time`) <= $to_date";
        }
        $total_records_data = DB::select("
            SELECT SQL_NO_CACHE
            	COUNT(*) AS `total_records`
            FROM `sales_invoice` AS `si`
            WHERE 1 $where_clause
        ");
        $total_records = $total_records_data[0]['total_records'];
        $total_pages = intval(ceil($total_records / self::PAGE_LIMIT));
        $start_record = ($page - 1) * self::PAGE_LIMIT;

        $list_data = null;
        if ($total_records > 0) {
          $list_data = DB::select("
			SELECT SQL_NO_CACHE
				`si`.`id`, `si`.`sales_invoice_number`, `si`.`create_time`, `issue_shop`.`code`,
				`si`.`last_update_by`, `si`.`total_amount`, `si`.`total_item`, `si`.`total_qty`,
				`si`.`net_total_amount`, `sf`.`name` as `staff_name`
			FROM `sales_invoice` AS `si`
			LEFT JOIN `shop` `issue_shop`
				ON `si`.`shop_id` = `issue_shop`.`id`
			LEFT JOIN `staff` `sf`
				ON `si`.`sales_id` = `sf`.`id`
			WHERE 1 $where_clause
			ORDER BY `si`.`id` DESC
			LIMIT $start_record, " . self::PAGE_LIMIT
          );
        }

        return array(
            'status' => $status,
            'list_data' => $list_data,
            'total_records' => $total_records,
            'total_pages' => $total_pages,
            'page' => $page,
            'end_page' => min(10, $total_pages),
        );
    }

    public static function updateSIStatus()
    {
        if (!Input::has("record_id")) {
            return 0;
        }
        $record_id = Input::get("record_id");

        $record_id = explode(',', $record_id);
        $total_ids = sizeof($record_id);
        $ids = array();
        foreach ($record_id as $id) {
            $ids[] = intval($id);
        }
        $record_id = implode(',', $ids);

        $status = intval(Input::get("status"));
        $valid_status = array(
            self::STATUS_CONFIRMED,
            self::STATUS_VOIDED,
            self::STATUS_FINISHED
        );

        if (!in_array($status, $valid_status)) {
            return 0;
        }
        $result = DB::update("
            UPDATE `sales_invoice`
            SET
                `status` = '$status',
				`void_time` = NOW()
            WHERE
                `id` IN ($record_id)
            LIMIT $total_ids
        ");

        if ($result) {
		        foreach ($ids as $id) {
								$result = self::voidSalesInvoice($id);
		        }
            return 1;
        }
        return 0;
    }

	public static function updateSIVoidStatus()
	{
        if (!Input::has("record_id")) {
            return 0;
        }
        if (!Input::has("status")) {
            return 0;
        }
		$record_id = Input::get("record_id");
		$status = Input::get("status");

        $result = DB::update("
            UPDATE `sales_invoice`
            SET
                `status` = '$status'
            WHERE
                `id` = '$record_id'
            LIMIT 1
        ");

        if ($result) {
            return 1;
        }
        return 0;
	}

    public static function getSalesInvoiceEditList($record_id)
    {
      $salse_invoice_data = DB::select("
        SELECT * FROM sales_invoice WHERE id = $record_id
      ");
      extract($salse_invoice_data[0]);

      if($status == '1'){
        return false;
      }else{
        $salse_invoice_items_data = DB::select("
          SELECT
            `SII`.*, `P`.barcode as product_upc,
            `P`.name as product_name, `S`.qty as total_qty,
			`SHP`.`code` AS shop_code
          FROM sales_invoice_items SII
          LEFT JOIN product P
			ON `p`.id = `SII`.product_id
          LEFT JOIN stock S
			ON `S`.id = `SII`.stock_id
		  LEFT JOIN shop SHP
			ON `SHP`.id = `s`.shop_id
          WHERE sales_invoice_id = $record_id
          GROUP by id
        ");

        $salse_invoice_payment_data = DB::select("
          SELECT * FROM payment WHERE sales_invoice_id = $record_id
        ");

        $deposit_amount = "";
        $deposit_number = "";

        if($deposit_id != ""){
          $deposit_amount = Deposit::getDepositPrice($deposit_id);
          $deposit_number = Deposit::getDepositNumber($deposit_id);
        }

        $sales_invoice_deposit_data = array(
          'deposit_amount'  => $deposit_amount,
          'deposit_number' => $deposit_number
        );

        $quotation_number = "";

        if($quotation_id != ""){
          $quotation_number = Quotation::getQuotationNumber($quotation_id);
        }
        $sales_invoice_quotation_data = array(
          'quotation_number' => $quotation_number
        );

        return array(
          'sales_invoice_list' => $salse_invoice_data[0],
          'sales_invoice_item_list' => $salse_invoice_items_data,
          'sales_invoice_payment_list' => $salse_invoice_payment_data,
          'sales_invoice_deposit_list' => $sales_invoice_deposit_data,
          'sales_invoice_quotation_list' => $sales_invoice_quotation_data
        );
      }
    }

    public static function getSalesInvoiceNumber($record_id)
    {
      $data = DB::select("
        SELECT `sales_invoice_number`
        FROM sales_invoice
        WHERE `id` = '$record_id'
      ");
      extract($data[0]);
      return $sales_invoice_number;
    }

		public static function voidSalesInvoice($record_id)
		{
      $data = DB::select("
        SELECT `SI`.`shop_id`, `SII`.*
        FROM sales_invoice `SI`
				LEFT JOIN `sales_invoice_items` `SII`
				ON `SI`.`id` = `SII`.`sales_invoice_id`
        WHERE `SI`.`id` = '$record_id'
      ");
			foreach ($data as $item_id => $items) {
				$shop_id = $items['shop_id'];
				$product_id = $items['product_id'];
				$qty = $items['qty'];
				$serial_number = $items['serial_number'];
				$result = Stock::revokeQty($shop_id, $product_id, $qty, $serial_number);
			}
			return true;
		}
}
