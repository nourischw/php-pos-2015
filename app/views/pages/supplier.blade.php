<?php extract($supplier_list); ?>
@section('content')
<div id="supplier" class="page_content">
    <div class="left_column">
		<div class="left_column_form_block">
			<div class="w100">
				<div class="supplier_search_text_field input-group">
					<label class="fL new_item_label" for="supplier_search">Supplier Search:</label>
					<input type="text" class="w100 form-control supplier_search_text_field" id="supplier_search" value="" name="supplier_search" placeholder="Supplier Search" value="{{ Session::get('supplier_keyword') }}">
				</div>
			</div>
		</div>
		<div class="list_container">
			<div id="item_list" class="item_list">
				<div class="list_header">
					<span class="list_column col_no_row">#</span>
					<span class="list_column col_supplier_code">Code</span>
					<span class="list_column col_supplier_name">Name</span>
					<span class="list_column col_update_date">Update Date</span>
					<span class="list_column col_update_by">Update By</span>
				</div>
				<div class="supplier_item_list" id="supplier_item_list">
					@foreach ( $supplier_list as $key => $supplier )
					<?php extract($supplier); ?>
					<div id="list_item_row" class="list_item_row" data-item_id="{{ $id }}">
						<span class="list_column col_no_row">{{ $key+1 }}</span>
						<span class="list_column col_supplier_code">{{ $code }}</span>
						<span class="list_column col_supplier_name">{{ $name }}</span>
						<span class="list_column col_update_date">{{ $last_update }}</span>
						<span class="list_column col_update_by">{{ $last_update_by }}</span>
						<input type="hidden" class="supplier_code_value" name="supplier_code_value" value="{{ $id }}">
						<input type="hidden" class="supplier_desc_value" name="supplier_desc_value" value="{{ $name }}">
						<input type="hidden" class="supplier_update_value" name="supplier_update_value" value="{{ $last_update }}">
						<input type="hidden" class="supplier_by_value" name="supplier_by_value" value="{{ $last_update_by }}">
					</div>
					@endforeach
				</div>
			</div>
		</div>
	</div>
	<div class="right_column">
		<div class="form_block">
			<div id="supplier_title" class="form_title">Supplier Create:</div>
				<form id="form_supplier_edit">
					<div class="form_row">
						<label for="supplier_code" class="form_label"><sup class="crRed">*</sup>Supplier Code:</label>
						<input type="text" class="text_field form-control validateItem" id="supplier_code" name="supplier_code" />
						<div class="form-error_message" id="error_supplier_code"></div>
					</div>
					<div class="form_row">
						<label for="supplier_name" class="form_label"><sup class="crRed">*</sup>Supplier Name:</label>
						<input type="text" class="text_field form-control validateItem" id="supplier_name" name="supplier_name" />
						<div class="form-error_message" id="error_supplier_name"></div>
					</div>
					<div class="form_row">
						<label for="address" class="form_label">Supplier Address:</label>
						<textarea class="text_field textarea_field form-control" id="address" name="address" rows="3"/></textarea>
					</div>
					<div class="form_row">
						<label for="contact_person" class="form_label">Contact Person:</label>
						<input type="text" class="text_field form-control" id="contact_person" name="contact_person" />
					</div>
					<div class="form_row">
						<label for="contact_person_title" class="form_label">Contact Person Title:</label>
						<input type="text" class="text_field form-control" id="contact_person_title" name="contact_person_title" />
					</div>
					<div class="form_row">
						<label for="telephone" class="form_label">Telephone:</label>
						<input type="text" class="text_field form-control validateItem" id="telephone" name="telephone" />
						<div class="form-error_message" id="error_telephone"></div>
					</div>
					<div class="form_row">
						<label for="mobile" class="form_label">Mobile:</label>
						<input type="text" class="text_field form-control validateItem" id="mobile" name="mobile" />
						<div class="form-error_message" id="error_mobile"></div>
					</div>
					<div class="form_row">
						<label for="fax" class="form_label">Fax:</label>
						<input type="text" class="text_field form-control validateItem" id="fax" name="fax" />
						<div class="form-error_message" id="error_fax"></div>
					</div>
					<div class="form_row">
						<label for="email" class="form_label">Email:</label>
						<input type="text" class="text_field form-control validateItem" id="email" name="email" />
						<div class="form-error_message" id="error_email"></div>
					</div>
					<div class="form_row">
						<div id="supplier_edit" class="product_edit" style="display: none">
							<input type="reset" class="btn btn-default btn-sm product_block_buttons" id="btn-order-reset" value="Cancel">
							@if ($is_allow_delete)
							<input type="button" class="btn btn-default btn-sm product_block_buttons" id="btn-order-delete" value="Delete">
							@endif
							@if ($is_allow_update)
							<input type="button" class="btn btn-default btn-sm product_block_buttons" id="btn-order-edit" value="Update">
							@endif
						</div>
						@if ($is_allow_create)
						<div id="supplier_submit" class="product_submit">
							<input type="reset" class="btn btn-default btn-sm supplier_block_buttons" id="btn-order-reset" value="Reset">
							<input type="button" class="btn btn-default btn-sm supplier_block_buttons" id="btn-order-add" value="Create">
						</div>
						@endif
					</div>

					<input type="hidden" value="0" id="supplier_id">
				</form>
			</div>
		</div>
	</div>
</div>
<script src="{{ Config::get('path.ROOT') }}app/js/libs/donetyping.js"></script>
@stop