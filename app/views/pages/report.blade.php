@section('content')
<div id="report" class="page_content">
	<h2>Sales Report</h2>
	<form id="form_report" action="generate_sales_report" method="post">
		<div class="form_row">
			<label for="start_date" class="form_label">Period:</label>
			<div class="input-group calendar_field">
				<div class="input-group-addon calendar_icon"><span class="glyphicon glyphicon-calendar"></span></div>
				<input type="text" class="form-control datepicker calendar_text_field" id="from_date" name="from_date" value="{{ $current_date }}" />
			</div>
			<span class="fL" style="margin: 0px 5px;"> to </span>
			<div class="input-group calendar_field">
				<div class="input-group-addon calendar_icon"><span class="glyphicon glyphicon-calendar"></span></div>
				<input type="text" class="form-control datepicker calendar_text_field" id="to_date" name="to_date" value="{{ $current_date }}" />
			</div>
		</div>
		<div class="form_row">
			<label for="shop" class="form_label">Shop:</label>
			<select class="text_field form-control validateItem" id="shop" name="shop">
				<option value="0">--All--</option>
				@foreach ($shop_list as $shop)
					<?php extract($shop, EXTR_PREFIX_ALL, 'shop'); ?>
				<option value="{{ $shop_id }}">{{ $shop_code }}</option>
				@endforeach
			</select>
		</div>
		<div class="form_row">
			<label for="sales" class="form_label">Sales:</label>
			<select class="text_field form-control validateItem" id="sales" name="sales">
				<option value="0">--All--</option>
				@foreach ($staff_list as $sales)
					<?php extract($sales); ?>
				<option value="{{ $id }}">{{ $staff_code }}</option>
				@endforeach
			</select>
		</div>
		<div class="page_button_row">
			<input type="button" id="print_report" class="btn btn-default btn-sm page_buttons" value="Download" />
			<input type="reset" class="btn btn-default btn-sm page_buttons" value="Reset All" />
		</div>
	</form>

	<h2> Daily Sales Report</h2>
	<form id="form_dailysales_report" action="generate_dailysales_report" method="post">
		<div class="form_row">
			<label for="date" class="form_label">Date:</label>
			<div class="input-group calendar_field">
				<div class="input-group-addon calendar_icon"><span class="glyphicon glyphicon-calendar"></span></div>
				<input type="text" class="form-control datepicker calendar_text_field" id="dailysales_date" name="dailysales_date" value="{{ $current_date }}" />
			</div>
		</div>
		<div class="form_row">
			<label for="shop" class="form_label">Shop:</label>
			<select class="text_field form-control validateItem" id="shop" name="shop">
				<option value="0">--All--</option>
				@foreach ($shop_list as $shop)
					<?php extract($shop, EXTR_PREFIX_ALL, 'shop'); ?>
				<option value="{{ $shop_id }}">{{ $shop_code }}</option>
				@endforeach
			</select>
		</div>
		<div class="form_row">
			<label for="category" class="form_label">Category:</label>
			<select class="text_field form-control validateItem" id="category" name="category">
				<option value="0">--All--</option>
				@foreach ($category_list as $category)
					<?php extract($category, EXTR_PREFIX_ALL, 'category'); ?>
				<option value="{{ $category_id }}">{{ $category_name }}</option>
				@endforeach
			</select>
		</div>
		<div class="page_button_row">
			<input type="button" id="print_dailysales_report" class="btn btn-default btn-sm page_buttons" value="Download" />
			<input type="reset" class="btn btn-default btn-sm page_buttons" value="Reset All" />
		</div>
	</form>

	<h2> Goods In Report</h2>
	<form id="form_goodsin_report" action="generate_goodsin_report" method="post">
		<div class="form_row">
			<label for="start_date" class="form_label">Period:</label>
			<div class="input-group calendar_field">
				<div class="input-group-addon calendar_icon"><span class="glyphicon glyphicon-calendar"></span></div>
				<input type="text" class="form-control datepicker calendar_text_field" id="goodsin_from_date" name="goodsin_from_date" value="{{ $current_date }}" />
			</div>
			<span class="fL" style="margin: 0px 5px;"> to </span>
			<div class="input-group calendar_field">
				<div class="input-group-addon calendar_icon"><span class="glyphicon glyphicon-calendar"></span></div>
				<input type="text" class="form-control datepicker calendar_text_field" id="goodsin_to_date" name="goodsin_to_date" value="{{ $current_date }}" />
			</div>
		</div>
		<div class="page_button_row">
			<input type="button" id="print_goodsin_report" class="btn btn-default btn-sm page_buttons" value="Download" />
			<input type="reset" class="btn btn-default btn-sm page_buttons" value="Reset All" />
		</div>
	</form>


	<h2> Realtime Inventory Report</h2>
	<form id="form_inventory_report" action="generate_realtime_inventory_report" method="post">

		<div class="form_row">
			<label for="shop" class="form_label">Shop:</label>
			<select class="text_field form-control validateItem" id="shop" name="shop">
				<option value="0">--All--</option>
				@foreach ($shop_list as $shop)
					<?php extract($shop, EXTR_PREFIX_ALL, 'shop'); ?>
				<option value="{{ $shop_id }}">{{ $shop_code }}</option>
				@endforeach
			</select>
		</div>
		<div class="form_row">
			<label for="category" class="form_label">Category:</label>
			<select class="text_field form-control validateItem" id="category" name="category">
				<option value="0">--All--</option>
				@foreach ($category_list as $category)
					<?php extract($category, EXTR_PREFIX_ALL, 'category'); ?>
				<option value="{{ $category_id }}">{{ $category_name }}</option>
				@endforeach
			</select>
		</div>
		<div class="page_button_row">
			<input type="button" id="print_inventory_report" class="btn btn-default btn-sm page_buttons" value="Download" />
			<input type="reset" class="btn btn-default btn-sm page_buttons" value="Reset All" />
		</div>
	</form>
</div>
@stop
