<div class="row">
    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
        <div class="row">全單折扣</div>

        <div class="row form-group">
            <div class="col-sm-2">
                <div class="radio"><label><input name="cart-discount-type-form" type="radio" value="1"> 實數</label></div>
            </div>

            <div class="col-sm-4"><input class="form-control input-sm" id="cart-discount-form" name="cart-discount-form" placeholder="折扣" type="number"></div>
            <div class="col-sm-4"><button class="btn btn-default" id="btn-cal-final-price" type="button">給予折扣</button></div>
        </div>

        <div class="row form-group">
            <div class="col-sm-2">
                <div class="radio"><label><input checked name="cart-discount-type-form" type="radio" value="2"> - %</label></div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><h4>合計</h4></div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
                <h4 id="cart-total-price-text">$0.00</h4><input id="cart-total-price" name="cart-total-price" type="hidden" value="0">
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><h4>折扣</h4></div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
                <h4 id="cart-discount-text"></h4>
                <input id="cart-discount" name="cart-discount" type="hidden" value="0">
                <input id="cart-discount-type" name="cart-discount-type" type="hidden" value="0">
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><h4>總金額</h4></div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
                <h4 id="cart-final-price-text">$0.00</h4>
                <input id="cart-final-price" name="cart-final-price" type="hidden" value="0">
            </div>
        </div>
    </div>
</div>