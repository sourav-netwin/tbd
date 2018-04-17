$(document).ready(function(e){
	init_dates();
	validate_specialproducts_form();
	// Special Products Datatables
	var base_url = $("#base_url").val();
	oTable = $('#special-products-table').dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": base_url+'admin/specialproducts/datatable/',
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"iDisplayLength":25,
		"lengthMenu": [ 25, 50, 75, 100 ],
		"dom": '<"top"f>rt<"bottom row"<"col-md-3"l><"col-md-3"i><"col-md-6"p>>',
		"columns": [
		{
			data: "ProductName"
		},
		{
			data: "Address"
		},
		{
			data: "SpecialName"
		},
		{
			data: "Price"
		},
		{
			data : "PriceAppliedFrom"
		},
		{
			data : "PriceAppliedTo"
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
		"aoColumnDefs": [
		{
			"targets": [2],
			"bSortable" : false,
			"visible": false
		}
		],
		"dom": '<"top"f>rt<"bottom row"<"col-md-3"l><"col-md-3"i><"col-md-6"p>>',
		'fnServerData': function (sSource, aoData, fnCallback) {
			//			var price_from = ( $("#price_from").val() != '' ? $("#price_from").val() : 0 );
			//			var price_to = ( $("#price_to").val() != '' ? $("#price_to").val() : 0 );
			var price_from = 0;
			var price_to = 0;

			sSource = sSource+''+price_from+'/'+price_to;
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
			$(nRow).attr('data-id',aData['Id']);
		},
		"fnRowCallback" : function(nRow, aData, iDisplayIndex){
			var html = status_html(aData["active"]);
			$("td:eq(5)", nRow).prepend(html);

			var html = approve_html(aData["approved"]);
			$("td:eq(5)", nRow).prepend(html);

			$("td:eq(3)", nRow).addClass('alignCenter');
			$("td:eq(4)", nRow).addClass('alignCenter');
		},
		fnDrawCallback: function( oSettings ) {
			$('div.dataTables_filter input').addClass("form-control");
			$(".pagination li").removeClass("ui-button ui-state-default");
			$(".first.disabled, .previous.disabled, .next.disabled, .last.disabled, .fg-button.active").off( "click" );
			$(".first.disabled a, .previous.disabled a, .next.disabled a, .last.disabled a, .fg-button.active a").attr('href','javascript:void(0);');
		}
	});

	$('table#special-products-table tbody').on('click', '.active', function () {
		var data = $(this).parents('tr').data();

		var status = $(this).data('status');
		if (confirm("Are you sure you want to change the status?")) {
			window.location = "specialproducts/change_status/" + data['id'] +"/"+status;
		}
	});

	$('table#special-products-table tbody').on('click', '.approve', function (e) {
                alert("Success");
                return false;
                
		e.preventDefault();
		e.stopImmediatePropagation();
		var data = $(this).parents('tr').data();
		var elm = $(this);
		elm.blur();
		var status = $(this).data('status');
		//if (confirm("Are you sure you want to approve this product?")) {
		loading();
			$.ajax({
				url: $("#base_url").val()+'admin/specialproducts/approve_product/' + data['id'],
				type: 'POST',
				dataType: 'json',
				data: {},
				success: function(data){
					unloading();
					if(data.result == 1){
						//Command: toastr["success"](data.message);
						elm.removeClass('approve');
						elm.removeAttr('title');
						elm.removeAttr('data-status');
						elm.attr('data-status','0');
						elm.addClass('disapprove');
						elm.html('<i class="fa fa-fw fa-med fa-thumbs-o-up"  style="color:#3c763d"></i>');
					}
					else{
						Command: toastr["error"](data.message);
					}
				},
				error: function(){
					unloading();
					Command: toastr["error"]('Failed to complete request');
				}
			});
		//window.location = "specialproducts/approve_product/" + data['id'];
		//}
	});

	$('table#special-products-table tbody').on('click', '.delete', function () {
		var data = $(this).parents('tr').data();

		if (confirm("Are you sure you want to delete this special product?")) {
			window.location = "specialproducts/delete/" + data['id'];
		}
	});

    

	

	$(document).on('change',"input[name='store_format_list[]']", function(e){

		var checkedValues = $("input[name='store_format_list[]']:checked").map(function() {
			return this.value;
		}).get();
		$.ajax({
			url : base_url+'admin/storeproducts/get_storeformat_stores/',
			data : {
				store_format: checkedValues
			},
			method : 'POST',
			dataType: 'json',
			success : function(data)
			{
				$("#stores").html(data.retailer_store);
			}
		});

	});

	//Method for selecting categories
	$(document).on('change','#product_main_category', function(e){

		var val = $(this).val();
		$('#default_price').val('');
		$("#products").html('<option value="">Select Product</option>');
		$("#products").select2();
		if(val == '' || typeof val == 'undefined'){
			$("#products").html('<option value="">Select Product</option>');
			$("#products").select2();
		}
		else{
			$.ajax({
				url : base_url+'admin/specialproducts/get_products_by_category/'+val,
				data : {
					id: val
				},
				method : 'POST',
				dataType: 'json',
				success : function(data)
				{
					$("#products").select2();
					$("#products").html(data.categories_products);
				}
			});
		}
			
	});

	//Select All Stores
	$(document).on('click','#all_store_formats,#all_stores',function(event){
		var name = $(this).attr('name');

		if(this.checked) {
			// Iterate each checkbox
			$("input[name='"+name+"']:checkbox").each(function() {
				this.checked = true;
			});
		}
		else {
			$("input[name='"+name+"']:checkbox").each(function() {
				this.checked = false;
			});
		}
	});
	
	$("#price_from").datepicker({
		format: 'yyyy-mm-dd',
		todayHighlight: true
	});

	$("#price_to").datepicker({
		format: 'yyyy-mm-dd',
		todayHighlight: true
	});

	$('#price_from').datepicker({
		format: 'yyyy-mm-dd',
		autoclose: true
	}).on('changeDate', function(selected){
		startDate = new Date(selected.date.valueOf());
		startDate.setDate(startDate.getDate(new Date(selected.date.valueOf())));
		$('#price_to').datepicker('setStartDate', startDate);
	});

	$('#price_to').datepicker({
		format: 'yyyy-mm-dd',
		autoclose: true
	}).on('changeDate', function(selected){
		FromEndDate = new Date(selected.date.valueOf());
		FromEndDate.setDate(FromEndDate.getDate(new Date(selected.date.valueOf())));
		$('#price_from').datepicker('setEndDate', FromEndDate);
	});

	$("#price_from, #price_to").change(function(){
		oTable.fnDraw();
	});
});

$('table#special-products-table tbody').on('click','.Edit', function(e) {
	e.preventDefault();
	var url = $(this).attr('data-href');
	$.ajax({
		url: url,
		type: 'POST',
		data: {},
		success: function(data){
			createModal('edit-special-products-modal', 'Edit Special Products', data,'small');
			setTimeout(function(){
				validate_specialproducts_form();
				init_dates();
			},500);
			
		}
	});
			
});
	
$('body').on('submit','.modalCustom #specialproducts_form', function(e){
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
				placeError(data.result,data.message,'specialproducts_form');
			}
		},
		error: function(){
			unloading();
		}
	});
});

//Validate Stores Form
function validate_specialproducts_form(){
	$("#specialproducts_form").validate({
		ignore: [],
		rules: {
			products : {
				required: true
			},
			price_store : {
				required: true
			},
			special_quantity : {
				required: true
			},
			special_price : {
				required: true
			},
			default_price : {
				required: true
			},
			price_from : {
				required: true
			},
			price_to : {
				required: true
			},
			from_price : {
				required: true
			},
			to_price : {
				required: true
			},
			product_main_category:{
				required: true
			},
			"store_format_list[]":{
				required: true
			},
			"stores_list[]":{
				required: true
			}

		},
		messages: {
			products : {
				required: "Please select a product"
			},
			price_store : {
				required: "Please select if price is for all stores"
			},
			stores : {
				required: "Please select a store"
			},
			actual_price : {
				required: "Please enter actual price"
			},
			default_price : {
				required: "Please enter default price"
			},
			special_quantity : {
				required: "Please enter special quantity"
			},
			special_price : {
				required: "Please enter special price"
			},
			price_from : {
				required: "Please enter special product date from"
			},
			price_to : {
				required: "Please enter special product date to"
			},
			from_price : {
				required: "Please enter special product date from"
			},
			to_price : {
				required: "Please enter special product date to"
			},
			product_main_category:{
				required: "Please select a category"
			},
			'store_format_list[]':{
				required: "Please select a store format"
			},
			'stores_list[]':{
				required: "Please select a store"
			}
		}
	});
}

function init_dates(){
	$("#from_price").datepicker({
		format: 'yyyy-mm-dd',
		todayHighlight: true
	});

	$("#to_price").datepicker({
		format: 'yyyy-mm-dd',
		todayHighlight: true
	});

	$('#from_price').datepicker({
		format: 'yyyy-mm-dd',
		autoclose: true
	}).on('changeDate', function(selected){
		startDate = new Date(selected.date.valueOf());
		startDate.setDate(startDate.getDate(new Date(selected.date.valueOf())));
		$('#to_price').datepicker('setStartDate', startDate);
	});

	$('#to_price').datepicker({
		format: 'yyyy-mm-dd',
		autoclose: true
	}).on('changeDate', function(selected){
		FromEndDate = new Date(selected.date.valueOf());
		FromEndDate.setDate(FromEndDate.getDate(new Date(selected.date.valueOf())));
		$('#from_price').datepicker('setEndDate', FromEndDate);
	});

}
$('body').on('change', '#products', function(){
	var category = $('#product_main_category') .val();
	var product = $('#products') .val();
	if(category != '' && typeof category != 'undefined' && product != '' && typeof product != 'undefined'){
		$.ajax({
			url: $("#base_url").val()+'admin/specialproducts/get_default_price',
			type: 'POST',
			data: {
				category: category,
				product: product
			},
			success: function(data){
				if(data != ''){
					$('#default_price').val(data);
				}
				else{
					$('#default_price').val('');
				}
			},
			error: function(){
				
			}
		});
	}	
});

$('body').on('change','#special_category',function(){
	var search_val = $("#special_category").val();
	var search_text = $("#special_category option:selected").text();
	if(search_val == '' || search_val == 'undefined'){
		search_text = '';
	}
	oTable.fnFilter(search_text,1) ;
	
});