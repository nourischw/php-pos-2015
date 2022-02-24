<!-- Modal -->
<div class="modal fade" id="confirmDeliverStockTransfer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<div class="form_header">Finish Stock Transfer</div>
				<button type="button" class="close fL" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="form_label">Date In:</div>
					<div class="form_field" id="today" data-value="{{ $today }}">{{ $today }}</div>
				</div>
				<div class="row">
					<div class="form_label">Deliver By:</div>
					<div class="form_field">
		                <select class="text_field form-control" id="deliver_by">
	                        @foreach ($staff_list as $index => $staff)
	                            <?php extract($staff); ?>
	                        <option value="{{ $staff_code }}">{{ $staff_code }}</option>
	                        @endforeach
	                    </select>
					</div>
				</div>

				@if ($is_allow_finish)
				<div class="row">
					<input type="checkbox" class="mark_finish_checkbox" id="mark_finished" value="1" />
					<strong>Mark as finished</strong>
				</div>
				<div><strong class="crRed">Notice: The stock item will be added to the shop only when "Mark as finished" checkbox is checked</strong></div>
				@endif

				<div class="staff_block row">
					<div class="row">
						<div class="form_label">Receiver Staff Code</div>
						<div class="form_field">
							<input type="text" class="form-control" id="receiver_staff_code" name="receiver_staff_code" placeholder="Staff Code" />
						</div>
					</div>
					<div class="row">
						<div class="form_label">Password</div>
						<div class="form_field">
							<input type="password" class="form-control" id="staff_password" name="staff_password" placeholder="Password" />
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" id="btn-confirm_delivery" class="btn btn-primary">Confirm</button>
			</div>
		</div>
	</div>
</div>