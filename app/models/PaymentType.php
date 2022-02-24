<?php

class PaymentType extends BaseModel
{

    /**
     * 	Process user login
     * 	@used-by (controller) LoginController loginProcess
     * 	@uses (self) validateLogin
     * 	@return boolean The login success result
     */
	public static function getPaymentType()
	{
		$data = DB::select("
			SELECT 
				`id`, `name`
			FROM `payment_type`
			ORDER BY `id`
		");
		
		return $data;
	}
	
}
