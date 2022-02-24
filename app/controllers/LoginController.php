<?php

class LoginController extends BaseController
{

    /**
     * 	@var string $layout Define the layout template
     */
    protected $layout = 'layouts.empty';

    /**
     * 	Show login page
     * 	@used-by (view) login.blade.php
     * 	@return void
     */
    public function showView()
    {
        // Set header
        $data = array(
            'shop_list' => Shop::getSelectList()
        );

        $this->layout->content = View::make('pages.login', $data);
    }
	
    public function processLogin()
	{
        return Staff::processLogin();
    }
	
    public function processLogout()
    {
		Session::forget('shop_code');
        Session::forget('staff_id');
        Session::forget('staff_code');
        Session::forget('staff_type');
        Session::forget('staff_name');
        Session::forget('shop_name');
        Session::forget('shop_address');
        Session::forget('shop_tel');
        Session::forget('shop_fax');
        Session::forget('shop_syb');
        Session::regenerate();
		
		return Redirect::home();
    }	
}
