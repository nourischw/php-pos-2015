<?php

class ImagesController extends BaseController
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
    public function getProductImages($images_id = null)
    {
		$path = Config::get('path.ROOT') . "app/images/product/" . $images_id;
		return $path;
		// echo "<img src='$path'>";
    }	
}
