$(document).ready(function(){
	validate_retailers_form();
	// Retailers Datatables
	var base_url = $("#base_url").val();
	oTable = $('#retailers-table').dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": base_url+'admin/retailers/datatable',
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"iDisplayLength":25,
		"lengthMenu": [ 25, 50, 75, 100 ],
		"dom": '<"top"f>rt<"bottom row"<"col-md-3"l><"col-md-3"i><"col-md-6"p>>',
		"columns": [
		{
			data: "CompanyName"
		},
		{
			data : "Logo",
			"bSortable" : false,
			"aTargets" : [ "no-sort" ]
		},
		{
			data: null,
			"bSortable" : false,
			"aTargets" : [ "no-sort" ]
		},
		{
			data: null,
			"bSortable" : false,
			"aTargets" : [ "no-sort" ]
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
			$(nRow).attr('data-id',aData['Id']);
		},
		"fnRowCallback" : function(nRow, aData, iDisplayIndex){
			var html = status_html(aData["active"]);
			$("td:eq(4)", nRow).prepend(html);

			var store_category_counts = get_store_category_count( aData['Id'] );
			$("td:eq(2)", nRow).html(store_category_counts.store);
			$("td:eq(3)", nRow).html(store_category_counts.category);
		},
		fnDrawCallback: function( oSettings ) {
			$(".pagination li").removeClass("ui-button ui-state-default");
			$(".first.disabled, .previous.disabled, .next.disabled, .last.disabled, .fg-button.active").off( "click" );
			$(".first.disabled a, .previous.disabled a, .next.disabled a, .last.disabled a, .fg-button.active a").attr('href','javascript:void(0);');
		}
	});

	$('table#retailers-table tbody').on('click', '.delete', function () {
		var data = $(this).parents('tr').data();

		if (confirm("Are you sure you want to delete this retailer?")) {
			window.location = "retailers/delete/" + data['id'];
		}
	});
	
	$('table#retailers-table tbody').on('click','.Edit', function(e) {
		e.preventDefault();
		var url = $(this).attr('data-href');
		$.ajax({
			url: url,
			type: 'POST',
			data: {},
			success: function(data){
				createModal('edit-retailer-modal', 'Edit Retailer', data,'wd-50');
				validate_retailers_form();
                                $('select').select2();
			}
		});
			
	});

	$('table#retailers-table tbody').on('click', '.active', function () {
		var data = $(this).parents('tr').data();

		var status = $(this).data('status');
		if (confirm("Are you sure you want to change the status?")) {
			window.location = "retailers/change_status/" + data['id'] +"/"+status;
		}
	});

    

	$("#assign_category_form").validate({
		rules: {
			'category[]' : {
				required: true
			}
		},
		messages: {
			'category[]' : {
				required: "Please select a category"
			}
		},
		errorPlacement: function(error, element){
			error.appendTo($('#category_error'));

		}
	});
})

function get_store_category_count( retailer_id )
{
	var html = {};

	$.ajax({
		url : $("#base_url").val()+'admin/retailers/get_store_category_count/'+retailer_id,
		method : 'POST',
		type: 'JSON',
		async: false,
		success : function(data)
		{
			data = JSON.parse(data);

			html.store = data.store;
			html.category = data.category;
		}
	});
	return html;
}

$('body').on('submit','.modalCustom #retailers_form', function(e){
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
				placeError(data.result,data.message,'retailers_form')
			}
		},
		error: function(){
			unloading();
		}
	});
});

//Validate User Form
function validate_retailers_form(){
    	$("#retailers_form").validate({
		//ignore: [],
		rules: {
                        "groupId[]":{
				required: true
			},
			company_name : {
				required: true
			},
			company_description : {
				required: true
			},
			/*logo :{
				required: {
					depends: function(element) {
						return ( $("#old_logo").length == 1 ? false : true );
					}
				},
				checkEmpty:"Please upload image file.",
				checkFile:true
			},*/
			first_name : {
				required: true,
				maxlength: 50
			},
			last_name : {
				required: true,
				maxlength: 50
			},
			password : {
				required: true
			},
			confirm_password : {
				required: true,
				equalTo: "#password"
			},
			email : {
				required: true,
				validateEmail: true
			},
			contact_tel:{
				validatePhoneNumber: true
			}
		},
		messages: {
                        "groupId[]" : {
				required: "Please select group"
			},
			company_name : {
				required: "Please enter company name"
			},
			company_description : {
				required: "Please enter company description"
			},
			/*logo :{
				required: "Please upload company logo",
				checkEmpty:true
			},*/
			first_name : {
				required: "Please enter first name"
			},
			last_name : {
				required: "Please enter last name"
			},
			email : {
				required: "Please enter email"
			},
			password : {
				required: "Please enter password"
			},
			confirm_password : {
				required: "Please enter confirm password"
			}
		}
                
	});
}