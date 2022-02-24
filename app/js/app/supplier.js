"use strict";
var orderObject = new Object();

$(function() {
    var supplier_title = $("#supplier_title");
    var supplier_submit = $("#supplier_submit");
    var supplier_edit = $("#supplier_edit");
    var supplier_search = getValue("supplier_search");

    $.setRules({
        "supplier_code": {
            "required": true,
        },
        "supplier_name": {
            "required": true
        },
        "telephone": {
            "format": "number"
        },
        "mobile": {
            "format": "number"
        },
        "fax": {
            "format": "number"
        },
        "email": {
            "format": "email"
        }
    });

    function checkSearchValue() {
        if (supplier_search === "") {
            checksupplierSearch();
        } else {
            getsupplierSearchData();
        }
    }

    function Selectsupplier(changeClass) {
        if (changeClass.hasClass("info")) {
            changeClass.removeClass("info");
            CancelFormAction();
        } else {
            $(".info").removeClass("info");
            changeClass.addClass("info");
            supplier_title.text('Supplier Information');
            supplier_submit.hide();
            supplier_edit.show();
            getsuppliertData($(".info").data("item_id"));
        }
    }

    function getsuppliertData(item_id) {
        var jqxhr = $.ajax({
            type: 'POST',
            url: ROOT + "supplier/get",
            async: false,
            data: { supplier_id: item_id }
        });

        jqxhr.done(function(result) {
            if (result != "") {
                var data = JSON.parse(result);
                $("#supplier_id").val(data.id);
                $("#supplier_code").val(data.code);
                $("#supplier_name").val(data.name);
                $("#address").html(data.address);
                $("#contact_person").val(data.contact_person);
                $("#contact_person_title").val(data.contact_person_title);
                $("#telephone").val(data.telephone);
                $("#mobile").val(data.mobile);
                $("#fax").val(data.fax);
                $("#email").val(data.email);
            }
        });
    }

    function getsupplierSearchData() {
        var i = 0;
        var jqxhr = $.ajax({
            type: 'POST',
            url: ROOT + "supplier_search",
            async: false,
            data: {
                keyword: getValue("supplier_search"),
                page: "1"
            }
        });

        jqxhr.done(function(result) {
            $("#supplier_item_list").empty();
            console.log(result);
            if (result != "") {
                result = $.parseJSON(result);
                $(".list_item_row").hide();
                var item_row = "";
                $.each(result, function() {
                    if (result[i].update_by === "") {
                        result[i].update_by = "---";
                    }
                    item_row += ' \
                        <div id="list_item_row" class="list_item_row search_supplier" data-item_id="' + result[i].id + '"> \
                            <span class="list_column col_no_row">' + (i + 1) + '</span> \
                            <span class="list_column col_supplier_code">' + result[i].code + '</span> \
                            <span class="list_column col_supplier_name">' + result[i].name + '</span> \
                            <span class="list_column col_update_date">' + result[i].last_update + '</span> \
                            <span class="list_column col_update_by">' + result[i].last_update_by + '</span> \
                        </div>';
                    i++;
                });
                $("#supplier_item_list").html(item_row);
            }
        });

        $("#supplier_item_list .search_supplier").on("click", function() {
            Selectsupplier($(this));
        });
        checksupplierSearch();
        setSession();
    }

    function checksupplierSearch() {
        if (getValue("supplier_search") === "") {
            $("#supplier_item_list .search_supplier").remove();
            $("#supplier_item_list .list_item_row").show();
        }
    }

    function CancelFormAction() {
        $("#form_supplier_edit")[0].reset();
        $("#address").empty();
        $("#supplier_id").val("0");
        supplier_title.text('supplier Create');
        supplier_submit.show();
        supplier_edit.hide();
    }

    function setSession() {
        var jqxhr = $.ajax({
            type: 'POST',
            url: ROOT + "set_supplier",
            async: false,
            data: {
                setSession: "1",
                keyword: getValue("supplier_search")
            }
        });
    }

    function processEdit( edit_type ) {
        var ok = $.checkFields("form_supplier_edit");
        if (!ok) {
            return false;
        }
        var supplier_code = getValue("supplier_code");
        var record_id = getValue("supplier_id");

        if ( supplier_code !== '' ) {
            var jqxhr = $.ajax({
                type: "POST",
                url: ROOT + "supplier/check_supplier_code",
                async: false,
                data: {
                    supplier_code: getValue("supplier_code"),
                    supplier_id: record_id
                }
            });

            jqxhr.done(function( is_used ) {
                if ( parseInt(is_used) > 0 ) {
                    $("#error_supplier_code").text("This supplier code is used").show();
                    ok = false;
                }
            });
        }

        if (!ok) {
            return false;
        }

        var type = (edit_type === EDIT_TYPE_NEW) ? "create" : "update";
        var ans = window.confirm("Confirm to " + type + " the supplier?");
        if (!ans) {
            return false;
        }

        var data = $("#form_supplier_edit").serialize();
        if (edit_type === EDIT_TYPE_UPDATE) {
            data += "&record_id=" + record_id;
        }

        console.log(data);
        var jqxhr = $.ajax({
            type: 'POST',
            url: ROOT + "supplier/" + type,
            async: false,
            data: data
        });

        jqxhr.done(function(result) {
            var msg = (edit_type === EDIT_TYPE_NEW) ? "Create" : "Update"
            if (parseInt(result) === 1) {
                alert(msg + " supplier successfully");
                location.reload();
            } else {
                alert("Failed to " + type + " supplier. Please try again later");
            }
        });
    }

    $("#btn-order-add").on("click", function() {
        processEdit(EDIT_TYPE_NEW);
    });

    $("#btn-order-edit").on("click", function() {
        processEdit(EDIT_TYPE_UPDATE);
    });

    $("#btn-order-delete").on("click", function() {
        var ans = window.confirm("Confirm to delete this supplier?");
        if (!ans) {
            return false;
        }

        var jqxhr = $.ajax({
            type: 'POST',
            url: ROOT + "supplier/delete",
            async: false,
            data: { record_id: getValue("supplier_id") }
        });

        jqxhr.done(function(result) {
            if (parseInt(result) === 1) {
                alert("Delete supplier record successfully");
                location.reload();
            } else {
                alert("Failed to delete supplier record. Please try again later");
            }
        });
    });

    $("#btn-order-reset").on("click", function() {
        Selectsupplier($(".list_item_row"));
    });

    $("#supplier_search").donetyping(getsupplierSearchData, 1500);

    $("#supplier_item_list .list_item_row").on("click", function() {
        Selectsupplier($(this));
    });

    checkSearchValue();
});