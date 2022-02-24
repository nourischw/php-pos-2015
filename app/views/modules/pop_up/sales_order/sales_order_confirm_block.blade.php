<!-- Modal -->
<div class="modal fade" id="confirmPage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">結賬</h4>
      </div>
      <div class="modal-body">
        <table id="confirm-table" class="table table-striped">
            <thead>
                <td>貨品名稱</td>
                <td>數量</td>
                <td>折扣</td>
                <td class='text-right'>小計</td>
            </thead>
            <tbody>
            </tbody>
            <tfoot style="background-color: #DDD;">
                <tr>
                    <td></td>
                    <td></td>
                    <td>合計</td>
                    <td id="confirm-cart-total-price-text" class="text-right"></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>折扣</td>
                    <td id="confirm-cart-discount-text" class="text-right "></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>總金額</td>
                    <td id="confirm-cart-final-price-text" class="text-right"></td>
                </tr>
            </tfoot>
        </table>
          <div class="row">
            <div class="col-md-offset-6 col-lg-6 col-md-6 col-sm-8 col-xs-8">
                <div class="input-group">
                    <div class="input-group-addon sales_confirm_field">收銀員</div>
                        <input type="text" class="form-control" id="cashier_id" name="cashier_id" placeholder="收銀員">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-offset-6 col-lg-6 col-md-6 col-sm-8 col-xs-8">
                <div class="input-group">
                    <div class="input-group-addon sales_confirm_field">密碼</div>
                        <input type="password" class="form-control" id="cashier_password" name="cashier_password" placeholder="密碼">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-offset-6 col-lg-6 col-md-6 col-sm-8 col-xs-8">
                <div class="input-group">
                    <div class="input-group-addon sales_confirm_field">銷售員</div>
                        <input type="text" class="form-control" id="sales_id" name="sales_id" placeholder="銷售員" value="{{ Session::get('staff_name') }}">
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="button" id="btn-pay-now" class="btn btn-primary">確定</button>
      </div>
    </div>
  </div>
</div>