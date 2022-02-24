<?php

class IndexController extends BaseController
{

    /**
     * 	@var string $layout Define the layout template
     */
    protected $layout = 'layouts.empty';

    /**
     * 	Show index page
     * 	@used-by (view) index.blade.php
     * 	@uses (self) _getAds
     * 	@uses (model) Deal get_index_category_deal
     * 	@uses (model) Deal get_main_deal
     * 	@uses (model) Deal get_today_deal
     * 	@return void
     */
    public function showView()
    {
        $data = array();
        $this->layout->content = View::make('pages.index', $data);
    }

}
