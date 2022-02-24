<?php

class SalesInvoiceItems extends BaseModel
{
    public static function getSalesQty($sales_invoice_id)
    {
        $sales_invoice_id = intval($sales_invoice_id);
        return DB::select("
            SELECT SQL_NO_CACHE 
                `id`, `stock_id`, `qty`
            FROM `sales_invoice_items`
            WHERE `sales_invoice_id` = '$sales_invoice_id'
        ");
    }
}
