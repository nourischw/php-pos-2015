<?php

class StockController extends BaseController
{

    public function getPopupList()
    {
        $list = Stock::getPopupList();
        if (empty($list)) {
            return null;
        }
        return View::make('popups.list_rows.stock_list', $list);
    }

    public function getWithdrawPopupList()
    {
    	$list = GoodsInItems::getWithdrawPopupList();
    	if (empty($list)) {
    		return null;
    	}
    	return View::make('popups.list_rows.stock_withdraw_list', $list);
    }
}
