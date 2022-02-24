<!-- Modal -->
<div class="modal fade" id="confirmPage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title payment_status" id="myModalLabel"></h4>
			</div>
			<div class="modal-body">
				<table id="confirm-table" class="table table-striped">
					<thead>
						<td>Product Name</td>
						<td>Qty</td>
						<td>Discount</td>
						<td class='text-right'>Total</td>
					</thead>
					<tbody>
					</tbody>
					<tfoot style="background-color: #DDD;">
						<tr>
							<td></td>
							<td></td>
							<td>Total:</td>
							<td id="confirm-cart-total-price-text" class="text-right"></td>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td>Discount:</td>
							<td id="confirm-cart-discount-text" class="text-right "></td>
						</tr>						
						<tr>
							<td></td>
							<td></td>
							<td>Deposit:</td>
							<td id="confirm-cart-deposit-text" class="text-right "></td>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td>Net Total:</td>
							<td id="confirm-cart-final-price-text" class="text-right"></td>
						</tr>
					</tfoot>
				</table>
				<div class="row">
					<div class="col-lg-offset-1 col-md-offset-1 col-lg-6 col-md-5 col-sm-6 col-xs-6 confirm_payment_block">
						<div class="row confirm_payment_list"></div>
					</div>
					
					<div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="input-group">
									<div class="input-group-addon sales_confirm_field">Cashier id</div>
										<input type="text" class="form-control" id="cashier_id" name="cashier_id" placeholder="Cashier id">
								</div>
							</div>
						</div>
						<div class="row">		
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="input-group">
									<div class="input-group-addon sales_confirm_field">Password</div>
									<input type="password" class="form-control" id="cashier_password" name="cashier_password" placeholder="Password">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="input-group">
									<div class="input-group-addon sales_confirm_field">Login as</div>
										<input type="text" class="form-control" id="sales_name" name="sales_name" placeholder="Sales name" value="{{ Session::get('staff_name') }} " readonly>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<input type="hidden" id="confirm_status">
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
				<button type="button" id="btn-pay-now" class="btn btn-primary">確定</button>
			</div>
		</div>
	</div>
</div>