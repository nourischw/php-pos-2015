<?php

class ChangePasswordController extends BaseController
{
    /**
     * 	@var string $layout Define the layout template
     */
    protected $layout = 'layouts.normal';

    /**
     *  Show index page
     *  @return void
     */
    public function showView()
    {
        if (empty(Session::get("staff_id"))) {
            Redirect::route("login");
        }
        
        $this->layout
            ->with('css', Config::get('css.CSS_FILE'))
            ->with('js', Config::get('js.JS_FORM_VALIDATOR'))
            ->with('page_js', 'change_password')
            ->content = View::make('pages.change_password');
    }
	
    public function process()
    {
        $result = Staff::changePassword();
        return Redirect::route("change_password")->with("change_password_result", $result);
    }
}