@section('content')
<div id="staff_group_edit" class="page_content">
	<div class="row">
		<form id="form_staff_group_edit" method="post">
			<div class="form_row">
				<h1>{{ ($record_id === 0) ? 'Create' : 'Update' }} Staff Group</h1>
	        </div>

			<div class="form_row">
				<label for="name" class="form_label"><sup class="required">*</sup>Group Name:</label>
	            <input type="text" class="text_field form-control validateItem" id="name" name="name" maxlength="50" value="{{ $sg_name }}" />
	            <span class="form-error_message" id="error_name"></span>
	        </div>

			<div class="form_row">
				<label for="description" class="form_label">Description:</label>
				<textarea name="description">{{ $sg_description }}</textarea>
			</div>

			<strong class="row">Group Permissions</strong>
			<div class="permission_block">
				<div class="section_row">
					<div class="section_name">Sales Invoice</div>
					<div class="section_permissions">
						<label class="permission_label"><input type="checkbox" class="permission_checkbox access_checkbox" data-section="chk_sales_invoice" name="permission[]" value="{{ Config::get('group_permissions.SALES_INVOICE_ACCESS') }}" {{ (in_array(Config::get('group_permissions.SALES_INVOICE_ACCESS'), $sg_permissions)) ? 'checked' : null }} />Access</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_sales_invoice" name="permission[]" value="{{ Config::get('group_permissions.SALES_INVOICE_CREATE') }}" {{ (in_array(Config::get('group_permissions.SALES_INVOICE_CREATE'), $sg_permissions)) ? 'checked' : null }} />Create</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_sales_invoice" name="permission[]" value="{{ Config::get('group_permissions.SALES_INVOICE_UPDATE') }}" {{ (in_array(Config::get('group_permissions.SALES_INVOICE_UPDATE'), $sg_permissions)) ? 'checked' : null }} />Update</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_sales_invoice" name="permission[]" value="{{ Config::get('group_permissions.SALES_INVOICE_DELETE') }}" {{ (in_array(Config::get('group_permissions.SALES_INVOICE_DELETE'), $sg_permissions)) ? 'checked' : null }} />Delete</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_sales_invoice" name="permission[]" value="{{ Config::get('group_permissions.SALES_INVOICE_PRINT') }}" {{ (in_array(Config::get('group_permissions.SALES_INVOICE_PRINT'), $sg_permissions)) ? 'checked' : null }} />Print</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_sales_invoice" name="permission[]" value="{{ Config::get('group_permissions.SALES_INVOICE_CONFIRM') }}" {{ (in_array(Config::get('group_permissions.SALES_INVOICE_CONFIRM'), $sg_permissions)) ? 'checked' : null }} />Confirm</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_sales_invoice" name="permission[]" value="{{ Config::get('group_permissions.SALES_INVOICE_VOID') }}" {{ (in_array(Config::get('group_permissions.SALES_INVOICE_VOID'), $sg_permissions)) ? 'checked' : null }} />Void</label>
					</div>
				</div>

				<div class="section_row">
					<div class="section_name">Purchase order</div>
					<div class="section_permissions">
						<label class="permission_label"><input type="checkbox" class="permission_checkbox access_checkbox" data-section="chk_purchase_order" name="permission[]" value="{{ Config::get('group_permissions.PURCHASE_ORDER_ACCESS') }}" {{ (in_array(21, $sg_permissions)) ? 'checked' : null }} />Access</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_purchase_order" name="permission[]" value="{{ Config::get('group_permissions.PURCHASE_ORDER_CREATE') }}" {{ (in_array(Config::get('group_permissions.PURCHASE_ORDER_CREATE'), $sg_permissions)) ? 'checked' : null }} />Create</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_purchase_order" name="permission[]" value="{{ Config::get('group_permissions.PURCHASE_ORDER_UPDATE') }}" {{ (in_array(Config::get('group_permissions.PURCHASE_ORDER_UPDATE'), $sg_permissions)) ? 'checked' : null }} />Update</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_purchase_order" name="permission[]" value="{{ Config::get('group_permissions.PURCHASE_ORDER_DELETE') }}" {{ (in_array(Config::get('group_permissions.PURCHASE_ORDER_DELETE'), $sg_permissions)) ? 'checked' : null }} />Delete</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_purchase_order" name="permission[]" value="{{ Config::get('group_permissions.PURCHASE_ORDER_PRINT') }}" {{ (in_array(Config::get('group_permissions.PURCHASE_ORDER_PRINT'), $sg_permissions)) ? 'checked' : null }} />Print</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_purchase_order" name="permission[]" value="{{ Config::get('group_permissions.PURCHASE_ORDER_CONFIRM') }}" {{ (in_array(Config::get('group_permissions.PURCHASE_ORDER_CONFIRM'), $sg_permissions)) ? 'checked' : null }} />Confirm</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_purchase_order" name="permission[]" value="{{ Config::get('group_permissions.PURCHASE_ORDER_VOID') }}" {{ (in_array(Config::get('group_permissions.PURCHASE_ORDER_VOID'), $sg_permissions)) ? 'checked' : null }} />Void</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_purchase_order" name="permission[]" value="{{ Config::get('group_permissions.PURCHASE_ORDER_UNVOID') }}" {{ (in_array(Config::get('group_permissions.PURCHASE_ORDER_UNVOID'), $sg_permissions)) ? 'checked' : null }} />Unvoid</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_purchase_order" name="permission[]" value="{{ Config::get('group_permissions.PURCHASE_ORDER_FINISH') }}" {{ (in_array(Config::get('group_permissions.PURCHASE_ORDER_FINISH'), $sg_permissions)) ? 'checked' : null }} />Finish</label>
					</div>
				</div>

				<div class="section_row">
					<div class="section_name">Goods In</div>
					<div class="section_permissions">
						<label class="permission_label"><input type="checkbox" class="permission_checkbox access_checkbox" data-section="chk_goods_in" name="permission[]" value="{{ Config::get('group_permissions.GOODS_IN_ACCESS') }}" {{ (in_array(Config::get('group_permissions.GOODS_IN_ACCESS'), $sg_permissions)) ? 'checked' : null }} />Access</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_goods_in" name="permission[]" value="{{ Config::get('group_permissions.GOODS_IN_CREATE') }}" {{ (in_array(Config::get('group_permissions.GOODS_IN_CREATE'), $sg_permissions)) ? 'checked' : null }} />Create</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_goods_in" name="permission[]" value="{{ Config::get('group_permissions.GOODS_IN_PRINT') }}" {{ (in_array(Config::get('group_permissions.GOODS_IN_PRINT'), $sg_permissions)) ? 'checked' : null }} />Print</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_goods_in" name="permission[]" value="{{ Config::get('group_permissions.GOODS_IN_FINISH') }}" {{ (in_array(Config::get('group_permissions.GOODS_IN_FINISH'), $sg_permissions)) ? 'checked' : null }} />Finish</label>
					</div>
				</div>

				<div class="section_row">
					<div class="section_name">Product</div>
					<div class="section_permissions">
						<label class="permission_label"><input type="checkbox" class="permission_checkbox access_checkbox" data-section="chk_product" name="permission[]" value="{{ Config::get('group_permissions.PRODUCT_ACCESS') }}" {{ (in_array(Config::get('group_permissions.PRODUCT_ACCESS'), $sg_permissions)) ? 'checked' : null }} />Access</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_product" name="permission[]" value="{{ Config::get('group_permissions.PRODUCT_CREATE') }}" {{ (in_array(Config::get('group_permissions.PRODUCT_CREATE'), $sg_permissions)) ? 'checked' : null }} />Create</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_product" name="permission[]" value="{{ Config::get('group_permissions.PRODUCT_UPDATE') }}" {{ (in_array(Config::get('group_permissions.PRODUCT_UPDATE'), $sg_permissions)) ? 'checked' : null }} />Update</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_product" name="permission[]" value="{{ Config::get('group_permissions.PRODUCT_DELETE') }}" {{ (in_array(Config::get('group_permissions.PRODUCT_DELETE'), $sg_permissions)) ? 'checked' : null }} />Delete</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_product" name="permission[]" value="{{ Config::get('group_permissions.PRODUCT_CONFIRM') }}" {{ (in_array(Config::get('group_permissions.PRODUCT_CONFIRM'), $sg_permissions)) ? 'checked' : null }} />Confirm</label>
					</div>
				</div>

				<div class="section_row">
					<div class="section_name">Stock Transfer</div>
					<div class="section_permissions">
						<label class="permission_label"><input type="checkbox" class="permission_checkbox access_checkbox" data-section="chk_stock_transfer" name="permission[]" value="{{ Config::get('group_permissions.STOCK_TRANSFER_ACCESS') }}" {{ (in_array(Config::get('group_permissions.STOCK_TRANSFER_ACCESS'), $sg_permissions)) ? 'checked' : null }} />Access</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_stock_transfer" name="permission[]" value="{{ Config::get('group_permissions.STOCK_TRANSFER_CREATE') }}" {{ (in_array(Config::get('group_permissions.STOCK_TRANSFER_CREATE'), $sg_permissions)) ? 'checked' : null }} />Create</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_stock_transfer" name="permission[]" value="{{ Config::get('group_permissions.STOCK_TRANSFER_UPDATE') }}" {{ (in_array(Config::get('group_permissions.STOCK_TRANSFER_UPDATE'), $sg_permissions)) ? 'checked' : null }} />Update</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_stock_transfer" name="permission[]" value="{{ Config::get('group_permissions.STOCK_TRANSFER_DELETE') }}" {{ (in_array(Config::get('group_permissions.STOCK_TRANSFER_DELETE'), $sg_permissions)) ? 'checked' : null }} />Delete</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_stock_transfer" name="permission[]" value="{{ Config::get('group_permissions.STOCK_TRANSFER_PRINT') }}" {{ (in_array(Config::get('group_permissions.STOCK_TRANSFER_PRINT'), $sg_permissions)) ? 'checked' : null }} />Print</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_stock_transfer" name="permission[]" value="{{ Config::get('group_permissions.STOCK_TRANSFER_CONFIRM') }}" {{ (in_array(Config::get('group_permissions.STOCK_TRANSFER_CONFIRM'), $sg_permissions)) ? 'checked' : null }} />Confirm</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_stock_transfer" name="permission[]" value="{{ Config::get('group_permissions.STOCK_TRANSFER_CONFIRM_DELIVERY') }}" {{ (in_array(Config::get('group_permissions.STOCK_TRANSFER_CONFIRM_DELIVERY'), $sg_permissions)) ? 'checked' : null }} />Confirm Delivery</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_stock_transfer" name="permission[]" value="{{ Config::get('group_permissions.STOCK_TRANSFER_TRANSFER') }}" {{ (in_array(Config::get('group_permissions.STOCK_TRANSFER_TRANSFER'), $sg_permissions)) ? 'checked' : null }} />Transfer</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_stock_transfer" name="permission[]" value="{{ Config::get('group_permissions.STOCK_TRANSFER_CANCEL') }}" {{ (in_array(Config::get('group_permissions.STOCK_TRANSFER_CANCEL'), $sg_permissions)) ? 'checked' : null }} />Cancel</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_stock_transfer" name="permission[]" value="{{ Config::get('group_permissions.STOCK_TRANSFER_FINISH') }}" {{ (in_array(Config::get('group_permissions.STOCK_TRANSFER_FINISH'), $sg_permissions)) ? 'checked' : null }} />Finish</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_stock_transfer" name="permission[]" value="{{ Config::get('group_permissions.STOCK_TRANSFER_RETRANSFER') }}" {{ (in_array(Config::get('group_permissions.STOCK_TRANSFER_RETRANSFER'), $sg_permissions)) ? 'checked' : null }} />Retransfer</label>
					</div>
				</div>

				<div class="section_row">
					<div class="section_name">Stock Level</div>
					<div class="section_permissions">
						<label class="permission_label"><input type="checkbox" class="permission_checkbox" name="permission[]" value="{{ Config::get('group_permissions.STOCK_LEVEL_ACCESS') }}" {{ (in_array(Config::get('group_permissions.STOCK_LEVEL_ACCESS'), $sg_permissions)) ? 'checked' : null }} />Access</label>
					</div>
				</div>

				<div class="section_row">
					<div class="section_name">Brand</div>
					<div class="section_permissions">
						<label class="permission_label"><input type="checkbox" class="permission_checkbox access_checkbox" data-section="chk_brand" name="permission[]" value="{{ Config::get('group_permissions.BRAND_ACCESS') }}" {{ (in_array(Config::get('group_permissions.BRAND_ACCESS'), $sg_permissions)) ? 'checked' : null }} />Access</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_brand" name="permission[]" value="{{ Config::get('group_permissions.BRAND_CREATE') }}" {{ (in_array(Config::get('group_permissions.BRAND_CREATE'), $sg_permissions)) ? 'checked' : null }} />Create</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_brand" name="permission[]" value="{{ Config::get('group_permissions.BRAND_UPDATE') }}" {{ (in_array(Config::get('group_permissions.BRAND_UPDATE'), $sg_permissions)) ? 'checked' : null }} />Update</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_brand" name="permission[]" value="{{ Config::get('group_permissions.BRAND_DELETE') }}" {{ (in_array(Config::get('group_permissions.BRAND_DELETE'), $sg_permissions)) ? 'checked' : null }} />Delete</label>
					</div>
				</div>

				<div class="section_row">
					<div class="section_name">Supplier</div>
					<div class="section_permissions">
						<label class="permission_label"><input type="checkbox" class="permission_checkbox access_checkbox" data-section="chk_brand" name="permission[]" value="{{ Config::get('group_permissions.SUPPLIER_ACCESS') }}" {{ (in_array(Config::get('group_permissions.SUPPLIER_ACCESS'), $sg_permissions)) ? 'checked' : null }} />Access</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_brand" name="permission[]" value="{{ Config::get('group_permissions.SUPPLIER_CREATE') }}" {{ (in_array(Config::get('group_permissions.SUPPLIER_CREATE'), $sg_permissions)) ? 'checked' : null }} />Create</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_brand" name="permission[]" value="{{ Config::get('group_permissions.SUPPLIER_UPDATE') }}" {{ (in_array(Config::get('group_permissions.SUPPLIER_UPDATE'), $sg_permissions)) ? 'checked' : null }} />Update</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_brand" name="permission[]" value="{{ Config::get('group_permissions.SUPPLIER_DELETE') }}" {{ (in_array(Config::get('group_permissions.SUPPLIER_DELETE'), $sg_permissions)) ? 'checked' : null }} />Delete</label>
					</div>
				</div>

				<div class="section_row">
					<div class="section_name">SN Transcation</div>
					<div class="section_permissions">
						<label class="permission_label"><input type="checkbox" class="permission_checkbox" name="permission[]" value="{{ Config::get('group_permissions.SN_TRANSACTION_ACCESS') }}" {{ (in_array(Config::get('group_permissions.SN_TRANSACTION_ACCESS'), $sg_permissions)) ? 'checked' : null }} />Access</label>
					</div>
				</div>

				<div class="section_row">
					<div class="section_name">Quotation</div>
					<div class="section_permissions">
						<label class="permission_label"><input type="checkbox" class="permission_checkbox access_checkbox" data-section="chk_quotation" name="permission[]" value="{{ Config::get('group_permissions.QUOTATION_ACCESS') }}" {{ (in_array(181, $sg_permissions)) ? 'checked' : null }} />Access</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_quotation" name="permission[]" value="{{ Config::get('group_permissions.QUOTATION_CREATE') }}" {{ (in_array(Config::get('group_permissions.QUOTATION_CREATE'), $sg_permissions)) ? 'checked' : null }} />Create</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_quotation" name="permission[]" value="{{ Config::get('group_permissions.QUOTATION_UPDATE') }}" {{ (in_array(Config::get('group_permissions.QUOTATION_UPDATE'), $sg_permissions)) ? 'checked' : null }} />Update</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_quotation" name="permission[]" value="{{ Config::get('group_permissions.QUOTATION_DELETE') }}" {{ (in_array(Config::get('group_permissions.QUOTATION_DELETE'), $sg_permissions)) ? 'checked' : null }} />Delete</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_quotation" name="permission[]" value="{{ Config::get('group_permissions.QUOTATION_PRINT') }}" {{ (in_array(Config::get('group_permissions.QUOTATION_PRINT'), $sg_permissions)) ? 'checked' : null }} />Print</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_quotation" name="permission[]" value="{{ Config::get('group_permissions.QUOTATION_VOID') }}" {{ (in_array(Config::get('group_permissions.QUOTATION_VOID'), $sg_permissions)) ? 'checked' : null }} />Void</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_quotation" name="permission[]" value="{{ Config::get('group_permissions.QUOTATION_UNVOID') }}" {{ (in_array(Config::get('group_permissions.QUOTATION_UNVOID'), $sg_permissions)) ? 'checked' : null }} />Unvoid</label>
					</div>
				</div>

				<div class="section_row">
					<div class="section_name">Report</div>
					<div class="section_permissions">
						<label class="permission_label"><input type="checkbox" class="permission_checkbox" name="permission[]" value="{{ Config::get('group_permissions.REPORT_ACCESS') }}" {{ (in_array(Config::get('group_permissions.REPORT_ACCESS'), $sg_permissions)) ? 'checked' : null }} />Access</label>
					</div>
				</div>

				<div class="section_row">
					<div class="section_name">Deposit</div>
					<div class="section_permissions">
						<label class="permission_label"><input type="checkbox" class="permission_checkbox access_checkbox" data-section="chk_deposit" name="permission[]" value="{{ Config::get('group_permissions.DEPOSIT_ACCESS') }}" {{ (in_array(Config::get('group_permissions.DEPOSIT_ACCESS'), $sg_permissions)) ? 'checked' : null }} />Access</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_deposit" name="permission[]" value="{{ Config::get('group_permissions.DEPOSIT_CREATE') }}" {{ (in_array(Config::get('group_permissions.DEPOSIT_CREATE'), $sg_permissions)) ? 'checked' : null }} />Create</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_deposit" name="permission[]" value="{{ Config::get('group_permissions.DEPOSIT_UPDATE') }}" {{ (in_array(Config::get('group_permissions.DEPOSIT_UPDATE'), $sg_permissions)) ? 'checked' : null }} />Update</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_deposit" name="permission[]" value="{{ Config::get('group_permissions.DEPOSIT_DELETE') }}" {{ (in_array(Config::get('group_permissions.DEPOSIT_DELETE'), $sg_permissions)) ? 'checked' : null }} />Delete</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_deposit" name="permission[]" value="{{ Config::get('group_permissions.DEPOSIT_PRINT') }}" {{ (in_array(Config::get('group_permissions.DEPOSIT_PRINT'), $sg_permissions)) ? 'checked' : null }} />Print</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_deposit" name="permission[]" value="{{ Config::get('group_permissions.DEPOSIT_VOID') }}" {{ (in_array(Config::get('group_permissions.DEPOSIT_VOID'), $sg_permissions)) ? 'checked' : null }} />Void</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_deposit" name="permission[]" value="{{ Config::get('group_permissions.DEPOSIT_UNVOID') }}" {{ (in_array(Config::get('group_permissions.DEPOSIT_UNVOID'), $sg_permissions)) ? 'checked' : null }} />Unvoid</label>
					</div>
				</div>

				<div class="section_row">
					<div class="section_name">Supplier</div>
					<div class="section_permissions">
						<label class="permission_label"><input type="checkbox" class="permission_checkbox access_checkbox" data-section="chk_supplier" name="permission[]" value="{{ Config::get('group_permissions.SUPPLIER_ACCESS') }}" {{ (in_array(Config::get('group_permissions.SUPPLIER_ACCESS'), $sg_permissions)) ? 'checked' : null }} />Access</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_supplier" name="permission[]" value="{{ Config::get('group_permissions.SUPPLIER_CREATE') }}" {{ (in_array(Config::get('group_permissions.SUPPLIER_CREATE'), $sg_permissions)) ? 'checked' : null }} />Create</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_supplier" name="permission[]" value="{{ Config::get('group_permissions.SUPPLIER_UPDATE') }}" {{ (in_array(Config::get('group_permissions.SUPPLIER_UPDATE'), $sg_permissions)) ? 'checked' : null }} />Update</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_supplier" name="permission[]" value="{{ Config::get('group_permissions.SUPPLIER_DELETE') }}" {{ (in_array(Config::get('group_permissions.SUPPLIER_DELETE'), $sg_permissions)) ? 'checked' : null }} />Delete</label>
					</div>
				</div>

				<div class="section_row">
					<div class="section_name">Staff</div>
					<div class="section_permissions">
						<label class="permission_label"><input type="checkbox" class="permission_checkbox access_checkbox" data-section="chk_staff" name="permission[]" value="{{ Config::get('group_permissions.STAFF_ACCESS') }}" {{ (in_array(Config::get('group_permissions.STAFF_ACCESS'), $sg_permissions)) ? 'checked' : null }} />Access</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_staff" name="permission[]" value="{{ Config::get('group_permissions.STAFF_CREATE') }}" {{ (in_array(Config::get('group_permissions.STAFF_CREATE'), $sg_permissions)) ? 'checked' : null }} />Create</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_staff" name="permission[]" value="{{ Config::get('group_permissions.STAFF_UPDATE') }}" {{ (in_array(Config::get('group_permissions.STAFF_UPDATE'), $sg_permissions)) ? 'checked' : null }} />Update</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_staff" name="permission[]" value="{{ Config::get('group_permissions.STAFF_DELETE') }}" {{ (in_array(Config::get('group_permissions.STAFF_DELETE'), $sg_permissions)) ? 'checked' : null }} />Delete</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_staff" name="permission[]" value="{{ Config::get('group_permissions.STAFF_RESET_PASSWORD') }}" {{ (in_array(Config::get('group_permissions.STAFF_RESET_PASSWORD'), $sg_permissions)) ? 'checked' : null }} />Reset Password</label>
					</div>
				</div>

				<div class="section_row">
					<div class="section_name">Staff Attendance</div>
					<div class="section_permissions">
						<label class="permission_label"><input type="checkbox" class="permission_checkbox access_checkbox" data-section="chk_staff_attendance" name="permission[]" value="{{ Config::get('group_permissions.STAFF_ATTENDANCE_ACCESS') }}" {{ (in_array(Config::get('group_permissions.STAFF_ATTENDANCE_ACCESS'), $sg_permissions)) ? 'checked' : null }} />Access</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_staff_attendance" name="permission[]" value="{{ Config::get('group_permissions.STAFF_ATTENDANCE_CREATE') }}" {{ (in_array(Config::get('group_permissions.STAFF_ATTENDANCE_CREATE'), $sg_permissions)) ? 'checked' : null }} />Create</label>
					</div>
				</div>

				<div class="section_row">
					<div class="section_name">Staff Group</div>
					<div class="section_permissions">
						<label class="permission_label"><input type="checkbox" class="permission_checkbox access_checkbox" data-section="chk_staff_group" name="permission[]" value="{{ Config::get('group_permissions.STAFF_GROUP_ACCESS') }}" {{ (in_array(Config::get('group_permissions.STAFF_GROUP_ACCESS'), $sg_permissions)) ? 'checked' : null }} />Access</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_staff_group" name="permission[]" value="{{ Config::get('group_permissions.STAFF_GROUP_CREATE') }}" {{ (in_array(Config::get('group_permissions.STAFF_GROUP_CREATE'), $sg_permissions)) ? 'checked' : null }} />Create</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_staff_group" name="permission[]" value="{{ Config::get('group_permissions.STAFF_GROUP_UPDATE') }}" {{ (in_array(Config::get('group_permissions.STAFF_GROUP_UPDATE'), $sg_permissions)) ? 'checked' : null }} />Update</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_staff_group" name="permission[]" value="{{ Config::get('group_permissions.STAFF_GROUP_DELETE') }}" {{ (in_array(Config::get('group_permissions.STAFF_GROUP_DELETE'), $sg_permissions)) ? 'checked' : null }} />Delete</label>
					</div>
				</div>

				<div class="section_row">
					<div class="section_name">Category</div>
					<div class="section_permissions">
						<label class="permission_label"><input type="checkbox" class="permission_checkbox access_checkbox" data-section="chk_category" name="permission[]" value="{{ Config::get('group_permissions.CATEGORY_ACCESS') }}" {{ (in_array(Config::get('group_permissions.CATEGORY_ACCESS'), $sg_permissions)) ? 'checked' : null }} />Access</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_category" name="permission[]" value="{{ Config::get('group_permissions.CATEGORY_CREATE') }}" {{ (in_array(Config::get('group_permissions.CATEGORY_CREATE'), $sg_permissions)) ? 'checked' : null }} />Create</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_category" name="permission[]" value="{{ Config::get('group_permissions.CATEGORY_UPDATE') }}" {{ (in_array(Config::get('group_permissions.CATEGORY_UPDATE'), $sg_permissions)) ? 'checked' : null }} />Update</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_category" name="permission[]" value="{{ Config::get('group_permissions.CATEGORY_DELETE') }}" {{ (in_array(Config::get('group_permissions.CATEGORY_DELETE'), $sg_permissions)) ? 'checked' : null }} />Delete</label>
					</div>
				</div>

				<div class="section_row">
					<div class="section_name">Stock Withdraw</div>
					<div class="section_permissions">
						<label class="permission_label"><input type="checkbox" class="permission_checkbox access_checkbox" data-section="chk_stock_withdraw" name="permission[]" value="{{ Config::get('group_permissions.STOCK_WITHDRAW_ACCESS') }}" {{ (in_array(Config::get('group_permissions.STOCK_WITHDRAW_ACCESS'), $sg_permissions)) ? 'checked' : null }} />Access</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_stock_withdraw" name="permission[]" value="{{ Config::get('group_permissions.STOCK_WITHDRAW_CREATE') }}" {{ (in_array(Config::get('group_permissions.STOCK_WITHDRAW_CREATE'), $sg_permissions)) ? 'checked' : null }} />Create</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_stock_withdraw" name="permission[]" value="{{ Config::get('group_permissions.STOCK_WITHDRAW_DELETE') }}" {{ (in_array(Config::get('group_permissions.STOCK_WITHDRAW_DELETE'), $sg_permissions)) ? 'checked' : null }} />Delete</label>
						<label class="permission_label"><input type="checkbox" class="permission_checkbox chk_stock_withdraw" name="permission[]" value="{{ Config::get('group_permissions.STOCK_WITHDRAW_FINISH') }}" {{ (in_array(Config::get('group_permissions.STOCK_WITHDRAW_FINISH'), $sg_permissions)) ? 'checked' : null }} />Finish</label>
					</div>
				</div>

				<div class="section_row">
					<div class="section_name">Stock Transport Log</div>
					<div class="section_permissions">
						<label class="permission_label"><input type="checkbox" class="permission_checkbox access_checkbox" data-section="chk_stock_withdraw" name="permission[]" value="{{ Config::get('group_permissions.STOCK_TRANSPORT_LOG_ACCESS') }}" {{ (in_array(Config::get('group_permissions.STOCK_TRANSPORT_LOG_ACCESS'), $sg_permissions)) ? 'checked' : null }} />Access</label>
					</div>
				</div>
			</div>

			<div class="page_button_row">
				<input type="button" id="submit_button" class="btn btn-default btn-sm page_buttons" value="Confirm" />
				<input type="reset" class="btn btn-default btn-sm page_buttons" value="Reset All" />
				@if ($is_allow_delete && $sg_members < 1)
	       		<button id="remove_button" class="btn btn-default btn-sm page_buttons" {{ ($record_id > 0) ? null : 'style="display: none"' }}>Delete</button>
	        	@endif
	        	<button class="btn btn-default btn-sm page_buttons redirect_button" data-redirect_page="staff_group/list">Return to List</button>
	        	<button class="btn btn-primary btn-sm redirect_button" id="create_new_record_button" {{ ($record_id > 0) ? null : 'style="display: none"' }} data-redirect_page="staff_group/edit">Create New Record</button>
			</div>

		    @if ($record_id > 0)
		    <input type="hidden" name="record_id" id="record_id" value="{{ $record_id }}" />
		    @endif
		</form>
	</div>

    <div class="panel panel-success result_alert_box" id="update_success_result">
        <div class="panel-heading"><h3 class="panel-title">Success</h3></div>
        <div class="panel-body">
            Update staff group successfully.<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    <div class="panel panel-danger result_alert_box" id="edit_failure_result">
        <div class="panel-heading"><h3 class="panel-title">Failure</h3></div>
        <div class="panel-body">
            Failed to edit staff group. Please try again later<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>

    <div class="panel panel-danger result_alert_box" id="delete_failure_result">
        <div class="panel-heading"><h3 class="panel-title">Failure</h3></div>
        <div class="panel-body">
            Failed to delete staff group. Please try again later<br /><br />
            <span class="close_result_box">Close</span>
        </div>
    </div>
</div>
@stop
