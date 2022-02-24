<?php

class ReportController extends BaseController
{

    /**
     * 	@var string $layout Define the layout template
     */
    protected $layout = 'layouts.normal';

    public function __construct()
    {
        parent::__construct();
        $this->checkAllowAccess("REPORT_ACCESS");
    }

    /**
     * 	Show index page
     * 	@used-by (view) index.blade.php
     * 	@return void
     */
    public function showView()
    {
    	$data = array (
    	   'shop_list' => Shop::getSelectList(),
           'category_list' => Category::getCategoryList(),
            'staff_list' => Staff::getSelectList(),
            'current_date' => date("Y-m-d")
    	);
        $this->layout
            ->with('title', "Generate Report")
            ->with('page_css', 'report')
            ->with('page_js', 'report')
            ->content = View::make('pages.report', $data);

    }

    public function generateReport()
    {
        $date = Input::get("from_date");
  
		$data = Report::downloadSalesReport();
        $filename = "sales_report_".$date;
		
		$output = array();
		
        $output[0] = array(
            'shp_code',
            'shp_name',
            'sm_txdate',
            'sm_smemo',
            'sm_dmemo',
            'sm_sfid',
            'cust_no',
            'pdtbrand',
            'pdtgroup',
            'pdtmodel',
            'pdtcolor',
            'p_code',
            's_no',
            'so_qty',
            'u_price',
            'netpaid',
            'gpshp_tamt',
            'gpoff_tamt',
            'refcost',
            'netcost',
            'compencost',
            'free_desc',
            'sm_remark'
        );
		
		if(count($data)) {
			foreach ($data as $row) {
				extract($row);
				$gpshp_tamt = $unit_price - $reference_cost;
				$gpoff_tamt = $unit_price - $average_cost;

				$output[] = array(
					$shop_code,
					$shop_name,
					$create_time,
					$sales_invoice_number,
					$deposit_number,
					$sales_id,
					$cust_no,
					$product_brand,
					$product_category,
					$product_model,
					$product_color,
					$product_code,
					$serial_number,
					$order_qty,
					$unit_price,
					$net_total_amount,
					$gpshp_tamt,
					$gpoff_tamt,
					$reference_cost,
					"",
					"",
					$description,
					$remark
				);
			}
		}
		
		$excel = App::make('excel');
		Excel::create($filename, function($excel) use($output){

			$excel->sheet('Excel sheet', function($sheet) use($output) {
				$sheet->setOrientation('landscape');
				
				$sheet->fromArray($output, null, 'A1', false, false);
			});
		})->download('xls');
        return Redirect::to('report');
    }


    public function generateDailysalesReport()
    {
        $date = Input::get("date");
  
        $data = Report::downloadDailySalesReport();
        $filename = "daily_sales_report_".$date;
        
        $output = array();
        
        $output[0] = array(
            'shp_code',
            'shp_name',
            'sm_txdate',
            'sm_smemo',
            'sm_txtype',
            'sm_sfid',
            'netpaid',
            'pdtbrand',
            'pdtgroup',
            'pdtmodel',
            'p_code',
            's_no',
            'so_qty',
            'u_price',
            'gpshp_tamt',
            'gpoff_tamt',
            'refcost',
            'netcost',
            'free_desc',
            'sm_remark',
            'sm_txtime'
        );
        
        if(count($data)) {
            foreach ($data as $row) {
                extract($row);
                $gpshp_tamt = ($u_price - $refcost) * $so_qty;
                $gpoff_tamt = ($u_price - $netcost) * $so_qty;

                $output[] = array(
                    $shp_code,
                    $shp_name,
                    $sm_txdate,
                    $sm_smemo,
                    $sm_txtype,
                    $sm_sfid,
                    $netpaid,
                    $pdtbrand,
                    $pdtgroup,
                    $pdtmodel,
                    $p_code,
                    $s_no,
                    $so_qty,
                    $u_price,
                    $gpshp_tamt,
                    $gpoff_tamt,
                    $refcost,
                    $netcost,
                    $free_desc,
                    $sm_remark,
                    $sm_txtime
                );
            }
        }
        
        $excel = App::make('excel');
        Excel::create($filename, function($excel) use($output){

            $excel->sheet('Excel sheet', function($sheet) use($output) {
                $sheet->setOrientation('landscape');
                
                $sheet->fromArray($output, null, 'A1', false, false);
            });
        })->download('xls');
        return Redirect::to('report');
    }

    public function generateGoodsinReport()
    {
        $date = Input::get("goodsin_from_date");
  
        $data = Report::downloadGoodsinReport();
        $filename = "goodsin_report_".$date;
        
        $output = array();
        
        $output[0] = array(
            'grn_sysno',
            'sup_name',
            'sup_code',
            'pom_crtdte',
            'pom_indte',
            'pom_bill',
            'pom_invno',
            'po_qty',
            'act_pri',
            'p_code',
            's_no',
            'p_detail',
            'pom_rmks',
            'upd_dte'
        );
        
        if(count($data)) {
            foreach ($data as $row) {
                extract($row);

                $output[] = array(
                    $sys_no,
                    $name,
                    $code,
                    $pom_crtdte,
                    $pom_indte,
                    $shop_code,
                    $invoice_no,
                    $qty,
                    $actual_price,
                    $barcode,
                    $serial_number,
                    $product_name,
                    $remarks,
                    $gi_update_time
                );
            }
        }
        
        $excel = App::make('excel');
        Excel::create($filename, function($excel) use($output){

            $excel->sheet('Excel sheet', function($sheet) use($output) {
                $sheet->setOrientation('landscape');
                
                $sheet->fromArray($output, null, 'A1', false, false);
            });
        })->download('xls');
        return Redirect::to('report');
    }

    public function generateRealtimeInventoryReport()
    {
        $date = date("Ymd");
  
        $data = Report::downloadRealtimeInventoryReport();
        $filename = "realtimeInventory_report_".$date;

        $shop_list = Shop::getSelectList();
        
        $output = array();
        
        //$output[0] = array();
        $output[0] = array(
            'cls_group',
            'cls_mount',
            'cls_detail',
        //  'cls_pricod',
            'cls_cost',
            'cls_srp',
        );
        for($i = 0; $i < count($shop_list); $i++){
            array_push($output[0],$shop_list[$i]['code']);
            $new_shop_list[$shop_list[$i]['id']] = $shop_list[$i]['code'];
        }
        
        $restructure_data = [];
        foreach ($data as $row) {
            extract($row);
            if(empty($restructure_data[$product_id])){
                $restructure_data[$product_id] = [];
                for($i = 0; $i < count($shop_list); $i++){
                    $shopcode = $new_shop_list[$shop_list[$i]['id']];
                    $restructure_data[$product_id][$shopcode] = '0';
                }
            }

            $restructure_data[$product_id]['cls_group'] = $category;
            $restructure_data[$product_id]['cls_mount'] = $barcode;
            $restructure_data[$product_id]['cls_detail'] = $name;
           // $restructure_data[$product_id]['cls_pricod'] = $name;
            $restructure_data[$product_id]['cls_cost'] = $reference_cost;
            $restructure_data[$product_id]['cls_srp'] = $unit_price;
            $restructure_data[$product_id][$new_shop_list[$shop_id]] = $qty;

        }
        if(count($restructure_data)) {
            $i = 1;
            foreach ($restructure_data as  $row) {
                $output[$i] = array(
                                $row['cls_group'],
                                $row['cls_mount'],
                                $row['cls_detail'],
                               // 'cls_pricod',
                                $row['cls_cost'],
                                $row['cls_srp']
                             );

                for($j = 0; $j < count($shop_list); $j++){
                    $shopcode = $shop_list[$j]['code'];
                   array_push($output[$i], $row[$shopcode]);
                }
                $i++;
            }
        }

        // print_r($output);
        // exit;
        $excel = App::make('excel');
        Excel::create($filename, function($excel) use($output){

            $excel->sheet('Excel sheet', function($sheet) use($output) {
                $sheet->setOrientation('landscape');
                
                $sheet->fromArray($output, null, 'A1', false, false);
            });
        })->download('xls');
        return Redirect::to('report');
    }
}
