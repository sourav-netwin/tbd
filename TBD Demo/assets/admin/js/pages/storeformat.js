// StoreFormats Datatables
var base_url = $("#base_url").val();

var retailer_id = $('#retailer_id').val();

var oTable = $('#storeformat-table').dataTable({
    "bProcessing": true,
    "bServerSide": true,
    "sAjaxSource": base_url+'admin/storeformat/datatable/'+retailer_id,
    "bJQueryUI": true,
    "sPaginationType": "full_numbers",
    "iDisplayLength":25,
    "lengthMenu": [ 25, 50, 75, 100 ],
    "stateSave": true,
    "columns": [
    {
        data: "StoreType"
    },
    {
        data: "Stores",
        "bSortable" : false,
        "aTargets" : [ "no-sort" ]
    },
    {
        data: "Users",
        "bSortable" : false,
        "aTargets" : [ "no-sort" ]
    },
    {
        data: "Actions",
        "bSortable" : false,
        "aTargets" : [ "no-sort" ]
    }
    ],
    "dom": '<"top"f>rt<"bottom row"<"col-md-3"l><"col-md-3"i><"col-md-6"p>>',
    "oLanguage": {
        "sProcessing": "<img src='"+base_url+"assets/admin/img/ajax-loader_dark.gif'>"
    },
    'fnServerData': function (sSource, aoData, fnCallback) {
		aoData.push({name: 'csrf_tbd_token', value: tbd_csrf});
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
        $(nRow).attr('data-id',aData['s_id']);
    },
    "fnRowCallback" : function(nRow, aData, iDisplayIndex){
        var html = status_html(aData["active"]);
        $("td:eq(3)", nRow).prepend(html);
    },
    fnDrawCallback: function( oSettings ) {
        $(".pagination li").removeClass("ui-button ui-state-default");
        $(".first.disabled, .previous.disabled, .next.disabled, .last.disabled, .fg-button.active").off( "click" );
        $(".first.disabled a, .previous.disabled a, .next.disabled a, .last.disabled a, .fg-button.active a").attr('href','javascript:void(0);');
    }
});


$('table#storeformat-table tbody').on('click', '.delete', function () {
    var data = $(this).parents('tr').data();

    if (confirm("Are you sure you want to delete this store format?")) {
        window.location = base_url+"admin/storeformat/delete/" + data['id']+"/"+retailer_id;;
    }
});

$('table#storeformat-table tbody').on('click', '.Edit', function () {
    var data = $(this).parents('tr').data();

    $('#storeformat_id').val(data['id']);
    window.location = base_url+"admin/retailers/"+retailer_id+"/storeformat/edit/"+data['id']+"/";
    ;
    return false;
});

$('table#storeformat-table tbody').on('click', '.active', function () {
    var data = $(this).parents('tr').data();

    var status = $(this).data('status');
    if (confirm("Are you sure you want to change the store format?")) {
        window.location =  base_url+"admin/storeformat/change_status/" + data['id'] +"/"+status+"/"+retailer_id;
    }
});

$.validator.addMethod("one_required", function() {
    return $("#storeformat_form").find(".one_required:checked").length > 0;
}, 'Please select at least one group.');

$("#storeformat_form").validate({
    ignore: [],
    errorElement: "div",
    rules: {
        'groupId':{
                one_required :true
        },
        storeformat_name : {
            required: true
        },
        logo :{
            required: {
                depends: function(element) {
                    return ( $("#old_logo").length == 1 ? false : true );
                }
            },
            checkEmpty:"Please upload image file.",
            checkFile:true
        }
    },
    messages: {
        storeformat_name : {
            required: "Please enter store format name"
        },
        logo :{
            required: "Please upload logo",
            checkEmpty:true
        }
    },
    errorPlacement: function(error, element) {
        if ($(element).hasClass("one_required")) {
            //error.insertAfter($(element).closest("div"));
            error.insertAfter(".showError");
        } else {
            error.insertAfter(element);
        }
    }
});
$('body').on('change', '#retailer_sel', function(){
	window.location.href = base_url+'admin/storeformat/index/'+$(this).val();
});

$('body').on('click','#add-user-btn', function(e){
	if($('#retailer_sel').length > 0){
		var ret_id = $('#retailer_sel').val();
		if(ret_id == '' || typeof ret_id == 'undefined'){
			alert('Please select retailer');
			e.preventDefault();
			return false;
		}
	}
});
