@section('content')
<div id="stock_level_list" class="page_content">
    <form id="form_stock_level_list" data-list_id="stock_level_list">
        <div class="form_block">
            <div class="field_column">
                <div class="form_row">
                    <label class="form_label" for="search_product_category">Category:</label>
                    <select class="form-control text_field" id="search_product_category" name="search_product_category">
                        <option value="">All</option>
                        @foreach ($product_category_list as $cat)
                            <?php extract($cat); ?>
                        <option value="{{ $id }}">{{ "$id - $name" }}</option>
                        @endforeach
                    </select>
                    <label class="form_label" for="search_supplier">Shop:</label>
                    <select class="text_field form-control" id="search_shop_id" name="search_shop_id">
                        <option value="">All</option>
                        @foreach ($shop_list as $shop)
                            <?php extract($shop, EXTR_PREFIX_ALL, 'shop'); ?>
                        <option value="{{ $shop_id }}">{{ $shop_code }}</option>
                        @endforeach
                    </select>
                    <input type="checkbox" name="search_have_qty_item_only" id="have_qty_item_only" style="margin-left: 20px; margin-top: 6px;" value="1" disabled />
                    <label for="have_qty_item_only">Show have Qty item only (for selected shop only)</label>
                </div>
                <div class="form_row" style="margin-bottom: 0px;">
                    <label class="form_label" for="search_product_name">Product Name:</label>
                    <input type="text" class="form-control text_field" id="search_product_name" name="search_product_name" />
                    <label class="form_label" for="search_product_upc">Product UPC:</label>
                    <input type="text" class="form-control text_field" id="search_product_upc" name="search_product_upc" />
                </div>
            </div>

            <div class="button_column">
                <button class="btn btn-default btn-sm search_block_buttons list_search_button">Search</button><br />
                <button class="btn btn-default btn-sm search_block_buttons list_reset_button">Clear Search</button>
            </div>
        </div>
    </form>

    <div class="list_container left_list">
        <div id="item_list" class="item_list">
        	<div class="list_header">
                <span class="list_column col_category">Category</span>
                <span class="list_column col_product_upc">UPC</span>
                <span class="list_column col_product_name">Product Name</span>
            </div>
            <div class="list_no_items_row" {{ ($have_record) ? 'style="display: none"' : null; }}>(No record found)</div>
            <div class="list_items" id="list_items">
            @if ($have_record)
                @foreach ($list_data as $value)
                    <?php extract($value, EXTR_PREFIX_ALL, 'sl'); ?>
                <div class="list_item_row" data-product_id="{{ $sl_id }}" data-product_image="{{ $sl_product_image }}">
                    <span class="list_column col_category">{{ $sl_category_id, ' - ', $sl_category_name }}</span>
                    <span class="list_column col_product_upc">{{ $sl_barcode }}</span>
                    <span class="list_column col_product_name">{{ $sl_name }}</span>
                </div>
                @endforeach
            @endif
            </div>
            <div class="list_button_row">
                <div class="button_row_left_column">
                    <div class="total_records_column">
                        <strong>Total records found: </strong>
                        <span id="total_records">{{ $total_records }}</span>
                    </div>
                </div>
                <div class="paging_bar_column" {{ ($total_pages < 2) ? 'style="display: none"' : null }}>
                    <ul class="pagination pagination-sm fR list_pagination_bar" id="page_bar" data-list_id="stock_level_list">
                        <li class="disabled"><a href="#" aria-label="First" class="page_button disable" data-type="first"><span aria-hidden="true">&laquo;</span></a></li>
                        <li class="disabled"><a href="#" aria-label="Previous" class="page_button disable" data-type="prev"><span aria-hidden="true">&lsaquo;</span></a></li>
                        @for ($i = 1; $i <= $end_page; $i++)
                        <li class="list_pages {{ ($page === $i) ? 'active' : null }}"><a href="#" class="page_button" data-type="page">{{ $i }}</a></li>
                        @endfor
                        <li {{ ($total_pages > 1 && $page === $total_pages) ? 'class="disable"' : null; }}><a href="#" aria-label="Next" class="page_button {{ ($page > 1 && $page < $end_page) ? 'disable' : null; }}" data-type="next"><span aria-hidden="true">&rsaquo;</span></a></li>
                        <li {{ ($total_pages > 1 && $page === $total_pages) ? 'class="disable"' : null; }}><a href="#" aria-label="Last" class="page_button {{ ($page > 1 && $page < $end_page) ? 'disable' : null; }}" data-type="last"><span aria-hidden="true">&raquo;</span></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="list_container right_list">
        <div id="product_info" class="form_block">
            <strong>Product Image:</strong><br />
            <img src="{{ Config::get('path.IMAGES') }}noimg.png" id="product_image" class="product_image" /><br />
            <strong>Product Category:</strong><br />
            <span id="product_category_text">---</span><br />
            <strong>Product UPC:</strong><br />
            <span id="product_upc_text">---</span><br />
            <strong>Product Name:</strong><br />
            <span id="product_name_text">---</span><br />
        </div>
        <div id="shop_qty_list_block">
            <div id="shop_qty_list" class="item_list">
                <div class="list_header">
                    <span class="list_column col_location">Location</span>
                    <span class="list_column col_qty">Qty</span>
                </div>
                <div id="list_select_first_message">(Please double click a product in the list)</div>
                <div id="no_record_message">(No shop record for this product)</div>
                <div class="list_items" id="shop_qty_location_list"></div>
            </div>
            <div class="list_button_row">
                <strong>Total shops:</strong>
                <span id="total_shops">0</span>
            </div>
        </div>

        <div class="fL w100 taC alert alert-info" id="shop_qty_loading_message" role="alert">Loading data, Please wait</div>

        <div class="legend_row">
            <div class="fL color_block no_qty">&nbsp;</div>
            <div class="fL color_label"> = No qty</div>
            <div class="fL color_block qty_warning">&nbsp;</div>
            <div class="fL color_label"> = < 5 qty</div>
        </div>
    </div>
</div>
@stop
