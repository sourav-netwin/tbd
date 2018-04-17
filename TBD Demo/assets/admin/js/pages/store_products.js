$(document).ready(function(){
    // Store Products Datatables
    var base_url = $("#base_url").val();
    oTable = $('#store-products-table').dataTable({
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": base_url+'admin/storeproducts/datatable',
        "bJQueryUI": true,
        "sPaginationType": "full_numbers",
        "iDisplayLength":25,
        "lengthMenu": [ 25, 50, 75, 100 ],
        "columns": [
        {
            data: "ProductName"
        },
        {
            data: "CompanyName"
        },
        {
            data : "Address"
        },
        {
            data: "Actions",
            "bSortable" : false,
            "aTargets" : [ "no-sort" ]
        }
        ],
        "oLanguage": {
            "sProcessing": "<img src='../assets/admin/img/ajax-loader_dark.gif'>"
        },
        "dom": '<"top"f>rt<"bottom row"<"col-md-4"l><"col-md-4"i><"col-md-4"p>>',
        'fnServerData': function (sSource, aoData, fnCallback) {
            $.ajax
            ({
                'dataType': 'json',
                'type': 'POST',
                'url': sSource,
                'data': aoData,
                'success': fnCallback
            });
        },
        'fnCreatedRow': function( nRow, aData, iDataIndex ) {
            $(nRow).attr('data-id',aData['Id']);
        },
        "fnRowCallback" : function(nRow, aData, iDisplayIndex){
            var html = status_html(aData["active"]);
            $("td:eq(3)", nRow).prepend(html);
        }
    });

    $('table#store-products-table tbody').on('click', '.active', function () {
        var data = $(this).parents('tr').data();

        var status = $(this).data('status');
        if (confirm("Are you sure you want to change the status?")) {
            window.location = "storeproducts/change_status/" + data['id'] +"/"+status;
        }
    });

    //Validate Stores Form

    $("#storeproducts_form").validate({
        errorElement: "div",
        rules: {
            products : {
                required: true
            },
            retailers : {
                required: true
            },
            stores : {
                required: true
            },
            price_store : {
                required: true
            },
            price : {
                required: true
            },
            product_main_category : {
                required: true
            }
        },
        messages: {
            products : {
                required: "Please select a product"
            },
            retailers : {
                required: "Please select a retailer"
            },
            stores : {
                required: "Please select a store"
            },
            price_store : {
                required: "Please select if price is for all stores"
            },
            price : {
                required: "Please enter price"
            },
            product_main_category : {
                required: "Please select a category"
            }
        }
    });

    $("#export_store_products_form").validate({
        errorElement: "div",
        rules: {
            retailers:{
                required: true
            },
            stores :{
                required: true
            }
        },
        messages: {
            retailers:{
                required: "Please select a retailer"
            },
            stores :{
                required: "Please select a store"
            }
        }
    });

    $("#import_store_products_form").validate({
        errorElement: "div",
        rules: {
            import_file :{
                required: true,
                checkFileExcel:true
            }
        },
        messages: {
            import_file :{
                required: "Please upload file to import"
            }
        }
    });

    $(document).on('click','#import_store_products', function(e){
        $("#import_store_products_form").submit();
    })

    $(document).on('change','#price_store', function(e){
        if( $(this).val() == 0 )
            $("#stores").parent().parent().show();
        else
            $("#stores").parent().parent().hide();
    });

  


})