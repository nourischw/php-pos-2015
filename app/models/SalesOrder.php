<?php



class SalesOrder extends BaseModel
{
	const ORDER_STATUS_ON_HOLD = -2;        // Stock is on holding
	const ORDER_STATUS_SUSPENDED = -1;      //
	const ORDER_STATUS_FAIL = 0;            // Payment failed
	const ORDER_STATUS_PENDING = 1;         // Complete payment, coupon not generated
	const ORDER_STATUS_COMPLETED = 2;       // Complete payment, coupon generated
    const ORDER_CODE = "sm";
	
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
        $staff = Session::get('staff_id');
        $cashier = Input::get("cashier_id");
        $sales = Input::get("sales_id");
        $cart = Input::get("cart");
        $cart_total_amt = Input::get("total_amt");
        $cart_discount = Input::get("cart_discount");
        $cart_discount_type = Input::get("cart_discount_type");
		
        $data = DB::select("
            SELECT MAX(reciept_id)+1 AS RECIEPT_ID 
            FROM sales_order 
            WHERE shop_id='".$shopcode."'
        ");
        extract($data[0]);
		
		if ($RECIEPT_ID === null){
			$RECIEPT_ID = '1';
		}
		
        $shopsyb = SHOP::GetShopSyb($shopcode);
        $yy = date("y");
        $sales_order_number = self::ORDER_CODE.$yy."-".$shopsyb."01-".sprintf("%05d", $RECIEPT_ID);
		
        $add_record = DB::insert("
            INSERT INTO `sales_order` (
                `shop_id`,
                `reciept_id`,
                `sales_order_number`,
                `cashier`,
                `sales`,
                `order_discount`,
                `order_discount_type`,
                `total_amt`,
                `last_update_by`,
                `last_update_time`,
                `create_time`
            ) VALUES (
                '$shopcode',
                $RECIEPT_ID,
                '".strtoupper($sales_order_number)."',
                '$cashier',
                '$sales',
                $cart_discount,
                $cart_discount_type,
                $cart_total_amt,
                '$staff',
                NOW(),
                NOW()
            )
        ");
        
        $sales_order_id = DB::getPdo()->lastInsertId();
        
        if ($add_record) {
            $total_amt = 0;
            foreach ($cart as $cart_id => $item) {
                $product = PRODUCT::GetProduct($item["product_code"], $item["serial_number"]);
                extract($product[0]);
                $item_total_price = $RETAIL * $item["qty"] * ((100 - $item["discount"]) / 100);
                $total_amt += $item_total_price;
                $add_items_record = DB::insert("
                    INSERT INTO `sales_order_items` (
                        `sales_order_id`,
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
                        '".$sales_order_id."',
                        '".$item['product_code']."',
                        '".$item['serial_number']."',
                        '".$RETAIL."',
                        ".$item["qty"].",
                        ".$item["discount"].",
                        ".$item_total_price.",
                        '".$staff."',
                        NOW(),
                        NOW()
                    )
                ");
            }
            
            if ($cart_discount_type == 1) {
                $total_amt -= $cart_discount;
            } elseif ($cart_discount_type == 2) {
                $total_amt = $total_amt * ((100 - $cart_discount) / 100);
            }
            
            DB::statement("
                UPDATE `sales_order` 
                SET `total_amt` = '$total_amt'
                WHERE `sales_order_id` = '$sales_order_id'
            ");
            
            return $sales_order_id;
        }
        return 0;
    }
    
    private function GetNewRecieptID()
    {
        $data = DB::select("
            SELECT MAX(reciept_id) + 1 AS RECIEPT_ID 
            FROM sales_order 
            WHERE shop_id = '".$shopcode."'
        ");
        
        extract($data);
        return $RECIEPT_ID;
    }
    
    public static function GetSalesOrder()
    {
       $sales_order_id = Input::get("so_id"); 
        
        $data = DB::select("
			SELECT 
				P.P_DETAIL, SO.*, SOI.*,
				SHP.SHP_ADD, SHP.SHP_TELA, SHP.SHP_FAXA,
				SHP.SHP_COMPY
			FROM vlab_pos.sales_order AS SO 
			LEFT JOIN vlab_pos.sales_order_items AS SOI 
				ON SO.sales_order_id = SOI.sales_order_id 
			LEFT JOIN vlab_pos.PRODUCT AS P 
				ON SOI.product_code = P.P_MOUNT
			LEFT JOIN vlab_pos.SHOP AS SHP
				ON SO.shop_id = SHP.SHP_CODE
            WHERE SO.sales_order_id =  '".$sales_order_id."' 
        "); 
        
        if (empty($data)) {
            return null;
        }

        return $data;
    }   

}
