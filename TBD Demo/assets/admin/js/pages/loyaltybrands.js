var base_url = '';
$(function(){
	base_url = $('#base_url').val();
	show_brand_table();
	validate_brand_form();
});

function show_brand_table(){
	if($('#brand-table').length > 0){
		oTable = $('#brand-table').dataTable({
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": base_url+'admin/loyaltybrands/datatable/',
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"iDisplayLength":25,
			"lengthMenu": [ 25, 50, 75, 100 ],
			"dom": '<"top"f>rt<"bottom row"<"col-md-3"l><"col-md-3"i><"col-md-6"p>>',
			"columns": [
                        {
				data: "b_id",
                                "bVisible" : false
			},    
			{
				data: "BrandName"
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
			"dom": '<"top"f>rt<"bottom row"<"col-md-3"l><"col-md-3"i><"col-md-6"p>>',
			'fnServerData': function (sSource, aoData, fnCallback) {
				aoData.push({
					name: 'csrf_tbd_token', 
					value: tbd_csrf
				});
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
				$(nRow).attr('data-id',aData['b_id']);
			},
			"fnRowCallback" : function(nRow, aData, iDisplayIndex){                            
                            var html = status_html(aData["active"]);
                            $("td:eq(1)", nRow).prepend(html);
			},
			fnDrawCallback: function( oSettings ) {
				$('div.dataTables_filter input').addClass("form-control");
				$(".pagination li").removeClass("ui-button ui-state-default");
				$(".first.disabled, .previous.disabled, .next.disabled, .last.disabled, .fg-button.active").off( "click" );
				$(".first.disabled a, .previous.disabled a, .next.disabled a, .last.disabled a, .fg-button.active a").attr('href','javascript:void(0);');
			}
		});
	}
}

function validate_brand_form(){
	if($('#brand_form').length > 0){
		$("#brand_form").validate({
			ignore: [],
			rules: {
				terms_text : {
					required: true,
					maxlength: 300,
					minlength: 10
				}
			},
			messages: {
				terms_text : {
					required: "Please enter Terms and Conditions"
				}
			}
		});
	}
		
}

$('table#brand-table tbody').on('click','.edit', function(e) {
	e.preventDefault();
	var url = $(this).attr('data-href');
	$.ajax({
		url: url,
		type: 'POST',
		dataType: 'json',
		data: {},
		success: function(data){
			createModal('edit-brand-modal', 'Edit T & C', data.html,'small');
			validate_brand_form();
		}
	});
			
});

$('body').on('submit','.modalCustom #brand_form', function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	var elm = $(this);
	var url = elm.attr('action');
	loading();
	$.ajax({
		url: url,
		type: 'POST',
		dataType: 'json',
		data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
		contentType: false,       // The content type used when sending data to the server.
		cache: false,             // To unable request pages to be cached
		processData:false,
		success: function(data){
			unloading();
			if(data.result == 1){
				location.reload();
			}
			else{
				Command: toastr["error"](data.message);
			}
		},
		error: function(){
			unloading();
		}
	});
});

$('table#brand-table tbody').on('click','.delete', function(e) {
	e.preventDefault();
	e.stopImmediatePropagation();
	var parent = $(this).parent().parent().parent();
	var c = confirm('Are you sure to delete the Brand?');
	if(c){
		loading();
		var id = parent.attr('data-id');
		$.ajax({
			url: base_url+'admin/loyaltybrand/delete_brand',
			type: 'POST',
			dataType: 'json',
			data: {
				id: id
			},
			success: function(data){
				unloading();
				if(data.result == 1){
					parent.slideUp('slow');
					Command: toastr["success"](data.message);
				}
				else{
					Command: toastr["error"](data.message);
				}
			},
			error: function(){
				unloading();
			}
		});
	}
});

$('table#brand-table tbody').on('click', '.active', function () {
        var brandId = $(this).parents('tr').attr('data-id');
        var status = $(this).data('status');
        if (confirm("Are you sure you want to change the status?")) {
                window.location = "loyaltybrands/change_status/" + brandId +"/"+status;
        }
});