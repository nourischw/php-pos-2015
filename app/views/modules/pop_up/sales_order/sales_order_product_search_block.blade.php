<!-- Modal -->
<div class="modal fade" id="searchPage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:900px">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">搜尋</h4>
      </div>
      <div class="modal-body">
          <form method="post" name="search_product">
              <div class="row">
                <div class=" col-lg-4 col-md-4 col-sm-4 col-xs-4">
                    <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-default active">
                            <input type="radio" name="search_type" autocomplete="off" value='1' checked>序號
                        </label>
                        <label class="btn btn-default ">
                            <input type="radio" name="search_type" autocomplete="off" value='2'>IMEI
                        </label>
                        <label class="btn btn-default ">
                            <input type="radio" name="search_type" autocomplete="off" value='4'>貨品名稱
                        </label>
                        <label class="btn btn-default ">
                            <input type="radio" name="search_type" autocomplete="off" value='5'>價錢
                        </label>
                    </div>
                </div>
                <div class=" col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="input-group">
                        <div class="input-group-addon search_field">關鍵字</div>
                            <input type="text" class="form-control" id="search_keyword" name="search_keyword" placeholder="">
                    </div>
                </div>
                <div class=" col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="input-group">
                        <button id="search-now" type="button" class="btn btn-primary">搜尋</button>
                    </div>
                </div> 
            </div>
        </form>  
        <table id="search-table" class="table table-striped">
            <thead>
                <td>店舖</td>
                <td>貨品名稱</td>
                <td>序號 / 條碼</td>
                <td>貨存</td>
                <td>成本</td>
                <td>單價</td>
            </thead>
            <tbody>
            </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="button" id="btn-search-select" class="btn btn-primary">加入</button>
      </div>
    </div>
  </div>
</div>