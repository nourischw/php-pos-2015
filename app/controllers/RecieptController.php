<?php

class RecieptController extends BaseController
{

    /**
     * 	@var string $layout Define the layout template
     */
    protected $layout = 'layouts.empty';

    /**
     * 	Show index page
     * 	@used-by (view) index.blade.php
     * 	@return void
     */
    public function showView()
    {
        $data = array();
        $this->layout->content = View::make('pages.reciept', $data);
    }
}
