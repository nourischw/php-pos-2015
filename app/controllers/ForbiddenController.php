<?php

class ForbiddenController extends BaseController
{
    /**
     * 	@var string $layout Define the layout template
     */
    protected $layout = 'layouts.normal';

    /**
     *  Show index page
     *  @return void
     */
    public function showView($id = null)
    {
        if (Session::get("staff_id") === null) {
            Redirect::route("login");
        }

        $this->layout->content = View::make('pages.forbidden');
    }
}