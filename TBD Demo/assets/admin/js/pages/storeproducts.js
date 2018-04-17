var oTableCust = '';
var price_array = {};
var base_url = '';
$(document).ready(function(){
	validate_storeproducts_form();
	validate_storeproducts_search_form();
	init_tab();
	// Store Products Datatables
	base_url = $("#base_url").val();
	oTable = $('#store-products-table').dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": base_url+'admin/storeproducts/datatable/',
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"iDisplayLength":25,
		"lengthMenu": [ 25, 50, 75, 100 ],
		"dom": '<"top"f>rt<"bottom row"<"col-md-3"l><"col-md-3"i><"col-md-6"p>>',
		"columns": [
		{
			data: "ProductName",
                        className: "productName"
		},
		{
			data: "CompanyName"
		},
		{
			data : "Address"
		},
		{
			data : "Price",
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
			var retailer_id = $('#retailers').val();

			sSource = sSource+''+retailer_id;
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
			if(aData['IsNew'] == '1'){
                            $(nRow).addClass('new-tb-row');
			}
		},
		"fnRowCallback" : function(nRow, aData, iDisplayIndex){
			var html = status_html(aData["active"]);
			$("td:eq(4)", nRow).prepend(html);
                        
                        var usertype = $('#usertype').val();
                        if(usertype == 3 ){
                            var allPricehtml = all_store_price_html();
                            $("td:eq(4)", nRow).append(allPricehtml);
                        }
		},
		fnDrawCallback: function( oSettings ) {
			$(".pagination li").removeClass("ui-button ui-state-default");
			$(".first.disabled, .previous.disabled, .next.disabled, .last.disabled, .fg-button.active").off( "click" );
			$(".first.disabled a, .previous.disabled a, .next.disabled a, .last.disabled a, .fg-button.active a").attr('href','javascript:void(0);');
			
			if($('.change_store_pr').length > 0){
				var count = 0;
				var i;

				for (i in price_array) {
					if (price_array.hasOwnProperty(i)) {
						count++;
					}
				}
				if(count > 0){
					fill_selected_price();
					add_error_price();
				}
			}
		}
	});

        $('table#store-products-table tbody').on('click', '.all-store-price', function () {
                var data = $(this).parents('tr').data();
                var rowId = data['id'];
                var price = $('#store_price_'+rowId).val();                
                var product_name = $(this).parents('tr').find('.productName').html();
                
                //alert(" product_name : " + product_name);
                
                //if(price <= 0 || !$.isNumeric(price)){
                if(!$.isNumeric(price)){
                    alert('Please enter valid price to change.');
                    return false;
		}
                
                
		if (confirm('Are you sure you want to change the price of "'+product_name+'" in all stores of this Retailer?')) {
                    loading();
                    $.ajax({
                            url : base_url+'admin/storeproducts/change_price_in_all_stores',
                            data : { id: rowId,price: price	},
                            method : 'POST',
                            dataType: 'json',
                            success : function(data)
                            {
                                //unloading();
                                window.location.reload();
                            },
                            error: function(){
                                unloading();
                            }
                    });
		}
	});
        
        
	$('table#store-products-table tbody').on('click', '.active', function () {
		var data = $(this).parents('tr').data();

		var status = $(this).data('status');
		if (confirm("Are you sure you want to change the status?")) {
			window.location = "storeproducts/change_status/" + data['id'] +"/"+status;
		}
	});
	
	$('table#store-products-table tbody').on('click','.edit', function(e) {
		e.preventDefault();
		var url = $(this).attr('data-href');
		var ret_txt = $(this).parent().parent().parent().find('td:nth-child(2)').html();
		$.ajax({
			url: url,
			type: 'POST',
			data: {},
			success: function(data){
				createModal('edit-stores-product-modal', 'Edit Store Product - '+ret_txt, data,'small');
				validate_storeproducts_form();
			}
		});
			
	});


    

	$("#export_store_products_form").validate({
		rules: {
			retailers:{
				required: true
			},
			product_main_category:{
				required:true
			},
			'store_format_list[]':{
				required: true
			},
			'stores_list[]':{
				required: true
			}
		},
		messages: {
			retailers:{
				required: "Please select a retailer"
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

	$("#import_store_products_form").validate({
		errorElement: "div",
		ignore: [],
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
	$("#import_store_price_form").validate({
		errorElement: "div",
		ignore: [],
		rules: {
			import_price_file :{
				required: true,
				checkFileExcel:true
			}
		},
		messages: {
			import_price_file :{
				required: "Please upload file to import"
			}
		}
	});

	$(document).on('click','#import_store_products', function(e){
		$("#import_store_products_form").submit();
	});
	$(document).on('click','#import_store_prices', function(e){
		$("#import_store_price_form").submit();
	});

	//Add store product
	$(document).on('change','#retailers_store', function(e){

		$('#ret_demo_disp').html('');
		var element = $(this).attr("id");
		var val = $(this).val();

		$.ajax({
			url : base_url+'admin/storeproducts/get_retailer_categories/'+val,
			data : {
				id: val,
				type: element
			},
			method : 'POST',
			dataType: 'json',
			success : function(data)
			{
				if(data.retailer_image){
					$('#ret_demo_disp').html('<img style="max-height: 35px" src="'+data.retailer_image+'" />');
				}
				$("#product_main_category").html(data.retailer_categories);
				$("#product_main_category").select2();
			}
		});

		$.ajax({
			url : base_url+'admin/storeproducts/get_retailer_store_formats/'+val,
			data : {
				id: val,
				type: element
			},
			method : 'POST',
			dataType: 'json',
			success : function(data)
			{
				$("#store_formats").html(data.retailer_store_format);
			}
		});
		$("#stores").html('');
	});
	$(document).on('change','#retailers_store_search', function(e){

		var element = $(this).attr("id");
		var val = $(this).val();
		search_product_list();
		$.ajax({
			url : base_url+'admin/storeproducts/get_retailer_categories/'+val,
			data : {
				id: val,
				type: element
			},
			method : 'POST',
			dataType: 'json',
			success : function(data)
			{
				if(data.retailer_image){
					$('#ret_demo_disp_src').html('<img style="max-height: 35px" src="'+data.retailer_image+'" />');
				}
			}
		});

		$.ajax({
			url : base_url+'admin/storeproducts/get_retailer_store_formats/'+val,
			data : {
				id: val,
				type: element
			},
			method : 'POST',
			dataType: 'json',
			success : function(data)
			{
				$("#store_formats_search").html(data.retailer_store_format);
			}
		});
		$("#stores_search").html('');
	});

	$(document).on('change',"#store_formats input[name='store_format_list[]']", function(e){
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
	$(document).on('change',"#store_formats_search input[name='store_format_list[]']", function(e){
		var checkedValues = $("#store_formats_search input[name='store_format_list[]']:checked").map(function() {
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
				$("#stores_search").html(data.retailer_store);
			}
		});
	});
	$(document).on('change',"#store_formats_search input[name='store_format_list[]']", function(e){
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
				$("#stores_search").html(data.retailer_store);
			}
		});
	});

	$(document).on('click','#list_product', function(e){

		var form = $( "#storeproducts_form" );
		if(form.valid()) {

			var val = $('#product_main_category').val();
			var retailer_id = $('#retailers_store').val();

			var checkedValues = $("input[name='stores_list[]']:checked").map(function() {
				return this.value;
			}).get();

			$("#store-product-list-table  tbody").html("");
			var is_store = false;
			var is_retailer = false;
			var store_type_id = '';
			var store_id = '';
			if($('#is_store').length > 0){
				is_store = true;
			}
			if($('#is_retailer').length > 0){
				is_retailer = true;
			}
			if(is_store){
				retailer_id = $('#retailer_sel').val();
				store_type_id = $('#storetype_sel').val();
				store_id = $('#store_sel').val();
			}
			if(is_retailer){
				retailer_id = $('#retailer_sel').val();
			}
			$.ajax({
				url : base_url+'admin/storeproducts/get_products_by_category/'+val+'/'+retailer_id+'/',
				data : {
					id: val,
					store_ids: checkedValues,
					retailer_id: retailer_id,
					store_type_id: store_type_id,
					store_id: store_id
				},
				method : 'POST',
				dataType: 'json',
				success : function(data)
				{
					if(data.categories_products == "<tr><td colspan='7' align='center'> No products avaliable </td></tr>"){
						$('#store-product-list-table').dataTable().fnDestroy();
						$('#store-product-list-table tbody').html(data.categories_products);
					}
					else{
						//select all check box
						$("#select_products").prop('checked',true);

					
						if(oTableCust != ''){
							oTableCust = '';
							$('#store-product-list-table').dataTable().fnDestroy();
							$("#store-product-list-table  tbody").html(data.categories_products); 
							oTableCust = $('#store-product-list-table').dataTable({
								"searching" : true,
								"lengthChange": false,
								"paging": false,
								"bSort" : false,
								"fnDrawCallback": function (oSettings) {
									if($('#store-product-list .row .col-sm-6:first').html() == ''){
										$('#store-product-list .row .col-sm-6:first').remove();
									}
								}
							});
						}
						else{
							$("#store-product-list-table  tbody").html(data.categories_products); 
							oTableCust = $('#store-product-list-table').dataTable({
								"searching" : true,
								"lengthChange": false,
								"paging": false,
								"bSort" : false,
								"fnDrawCallback": function (oSettings) {
									if($('#store-product-list .row .col-sm-6:first').html() == ''){
										$('#store-product-list .row .col-sm-6:first').remove();
									}
								}
							});
						}
					}
					
				}
			});
		}
	});
	$(document).on('click','#list_product_search', function(e){
		search_product_list();
	});

	//Reset form on popupp close
	$('#myModal').on('hidden.bs.modal', function () {
		$('#export_store_products_form').trigger('reset');
		$('#retailers_store').select2();
		$('#product_main_category').select2();
	});
});

$('body').on('submit','.modalCustom #storeproducts_form', function(e){
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
				placeError(data.result,data.message,'storeproducts_form')
			}
		},
		error: function(){
			unloading();
		}
	});
});

$('body').on('submit','.add_stpr #storeproducts_form', function(e){
	var elm = $(this);
	//$('#store-product-list-table_filter input').val('').change();
	oTableCust.fnFilter('');
	setTimeout(function(){
		$(this).submit();
	},1000);
	
});

//Validate Stores Form
function validate_storeproducts_form(){
	$("#storeproducts_form").validate({
		rules: {
			retailers : {
				required: true
			},
			product_main_category:{
				required: true
			},
			'store_format_list[]':{
				required: true
			},
			'stores_list[]':{
				required: true
			},
			'store_products[]':{
				required: true
			}
		},
		messages: {
			retailers : {
				required: "Please select a retailer"
			},
			product_main_category:{
				required: "Please select a category"
			},
			'store_format_list[]':{
				required: "Please select a store format"
			},
			'stores_list[]':{
				required: "Please select a store"
			},
			'store_products[]' : {
				required: "Please select a product"
			}
		}
	});
}
function validate_storeproducts_search_form(){
	$("#storeproducts_search_form").validate({
		rules: {
			retailers_store_search : {
				required: true
			},
			product_name_search:{
				required: true
			},
			'store_format_list[]':{
				required: true
			},
			'stores_list_search[]':{
				required: true
			},
			'store_products_search[]':{
				required: true
			}
		},
		messages: {
			retailers_store_search : {
				required: "Please select a retailer"
			},
			product_name_search:{
				required: "Please enter product name"
			},
			'store_format_list[]':{
				required: "Please select a store format"
			},
			'stores_list[]':{
				required: "Please select a store"
			},
			'store_products_search[]' : {
				required: "Please select a product"
			}
		}
	});
}
function init_tab(){
	if($( "#tabs" ).length > 0){
		$( "#tabs" ).tabs();
	}
}

function add_to_search(id){
	if(id == '' || typeof id == 'undefined'){
		$('input[name="store_products_search[]"]:checked').each(function(){
			var elm = $(this);
			var id = elm.val();
			search_array[id] = {
				"price": $('input[name="product_price_'+id+'"]').val() == '' ? "0.00" : $('input[name="product_price_'+id+'"]').val(), 
				"main": $('input[name="product_price_'+id+'"]').attr('data-main')
			}
		});
	}
	else{
		search_array[id] = {
			"price": $('input[name="product_price_'+id+'"]').val() == '' ? "0.00" : $('input[name="product_price_'+id+'"]').val(), 
			"main": $('input[name="product_price_'+id+'"]').attr('data-main')
		};
	}
	
}

function remove_search_value(id){
	if(id == '' || typeof id == 'undefined'){
		$('input[name="store_products_search[]"]').each(function(){
			var elm = $(this);
			if(!elm.prop('checked')){
				delete search_array[elm.val()];
			}
		});
	}
	else{
		delete search_array[id];
	}
	
}
$('body').on('change','input[name="store_products_search[]"]', function(){
	var elm = $(this);
	var val = elm.val();
	if(elm.prop('checked')){
		add_to_search(val);
	}
	else{
		remove_search_value(val);
	}
	console.log(search_array);
});
$('body').on('change','#select_products_search', function(){
	var elm = $(this);
	if(elm.prop('checked')){
		add_to_search();
	}
	else{
		remove_search_value();
	}
	console.log(search_array);
});

$('body').on('change', '.prod_prc', function(){
	var elm = $(this);
	var id = elm.attr('name').replace('product_price_','');
	add_to_search(id);
	console.log(search_array);
});

$('body').on('submit', '#storeproducts_search_form', function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	var elm = $(this);
	var count = 0;
	var i;

	for (i in search_array) {
		if (search_array.hasOwnProperty(i)) {
			count++;
		}
	}
	//var retailer_id = $('#retailers_store_search').val();
	if(count > 0){
		var data = elm.serialize()+'&selectedProd='+JSON.stringify(search_array);
		var url = elm.attr('action');
		$.ajax({
			url:url,
			type: 'POST',
			data: data,
			success: function(data){
				if(data==1){
					window.location.reload();
				}
			},
			error: function(){

			}
		});
	}
	else{
		alert('Select atleast one product to add');
	}
		
});

function search_product_list(){
	var form = $( "#storeproducts_search_form" );
	var validation_condition = '';
	var is_store = false;
	var is_retailer = false;
	var is_storetype = false;
	var store_type_id = '';
	var store_id = '';
	var retailer_id = '';
	if($('#is_store').length > 0){
		validation_condition = $('input[name="product_name_search"]').valid();
		is_store = true;
	}
	else if($('#is_retailer').length > 0){
		validation_condition = $('input[name="product_name_search"]').valid();
		is_retailer = true;
	}
	else if($('#is_stype').length > 0){
		validation_condition = $('input[name="product_name_search"]').valid();
		is_storetype = true;
	}
	else{
		validation_condition = $('select[name="retailers_store_search"]').valid() && $('input[name="product_name_search"]').valid();
	}
	if(validation_condition) {

		var search_pro = $('#product_name_search').val();
		if(is_store){
			retailer_id = $('#retailer_sel').val();
			store_type_id = $('#storetype_sel').val();
			store_id = $('#store_sel').val();
		}
		else if(is_retailer){
			retailer_id = $('#retailer_sel').val();
		}
		else{
			retailer_id = $('#retailers_store_search').val();
		}
		

		var checkedValues = $("#stores_search input[name='stores_list[]']:checked").map(function() {
			return this.value;
		}).get();
		

		$("#store-product-search-list-table  tbody").html("");
		$.ajax({
			url : $("#base_url").val()+'admin/storeproducts/get_products_custom',
			data : {
				store_ids: checkedValues,
				search_pro: search_pro,
				retailer_id: retailer_id,
				store_type_id: store_type_id,
				store_id: store_id
			},
			method : 'POST',
			dataType: 'json',
			success : function(data)
			{
				//select all check box
				//$("#select_products_search").prop('checked',true);
				$("#store-product-search-list-table  tbody").html(data.categories_products); 
				add_to_search();
				console.log(search_array);
					
			}
		});
	}
}

$('body').on('change','#sel_all_cat_build', function(){
	var elm = $(this);
	$('.store_main_cat').prop('checked', elm.prop('checked'));
});
$('body').on('click', '#add_auto_category', function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	var elm = $(this);
	if($('.store_main_cat:checked').length == 0){
		alert('Select atleast one categry to add product catelogue');
	}
	else{
		loading();
		$.ajax({
			url: $("#base_url").val()+'admin/storeproducts/get_add_count',
			type: 'POST',
			dataType: 'json',
			data: $('#auto_cat_form').serialize()+'&csrf_tbd_token=' + tbd_csrf,
			success: function(data){
				unloading();
				if(data.result == 1){
					var c = confirm('This will add '+data.message+' products to your store. It may take a while. Do not close/refresh the window in-between. It may cause partial data entry. Do you want to continue?');
					if(c){
						loading();
						$.ajax({
							url: $("#base_url").val()+'admin/storeproducts/add_auto_catalogue',
							type: 'POST',
							dataType: 'json',
							data: $('#auto_cat_form').serialize()+'&csrf_tbd_token=' + tbd_csrf,
							success: function(data){
								unloading();
								if(data.result == 1){
									window.location.href = $("#base_url").val()+'admin/storeproducts/finish_auto';
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

function add_to_search(id){
	if(id == '' || typeof id == 'undefined'){
		$('input[name="store_products[]"]:checked').each(function(){
			var elm = $(this);
			var id = elm.val();
			var price_elm = elm.parent().parent().find('input[type=text]');
			var qty_elm = elm.parent().parent().find('select');
			search_array[id] = {
				"price": price_elm.val() == '' ? "0.00" : price_elm.val(), 
				"qty": qty_elm.val()
			}
		});
	}
	else{
		if($('input[name="store_products[]"][value="'+id+'"]').prop('checked')){
			var elm_sel = $('input[name="store_products[]"][value="'+id+'"]');
			var price_elm = elm_sel.parent().parent().find('input[type="text"]');
			var qty_elm = elm_sel.parent().parent().find('select');
			search_array[id] = {
				"price": price_elm.val() == '' ? "0.00" : price_elm.val(), 
				"qty": qty_elm.val()
			};
		}
		else{
			remove_search_value(id);
		}
			
	}
	show_sel_count();
}

function remove_search_value(id){
	if(id == '' || typeof id == 'undefined'){
		$('input[name="store_products[]"]').each(function(){
			var elm = $(this);
			if(!elm.prop('checked')){
				delete search_array[elm.val()];
			}
		});
	}
	else{
		delete search_array[id];
	}
	show_sel_count();
}

function fill_selected_price(){
	$.each(price_array, function( index, value ) {
		var price_elm = $('#store_price_'+index);
		price_elm.val(value);
	});
}
$('body').on('change','.change_store_pr',function(){
	var elm = $(this);
	var id = elm.attr('id').replace('store_price_','');
	var price = elm.val();
	if(price <= 0){
		delete price_array[id];
	}
	else{
		price_array[id] = price;
	}
	console.log(price_array);
});

$('body').on('click','#update_store_price',function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	var count = 0;
	var i;

	for (i in price_array) {
		if (price_array.hasOwnProperty(i)) {
			count++;
		}
	}
	var error = 0;
	$.each(price_array, function( index, value ) {
		var parent = $('#store_price_'+index).parent().parent();
		if(value <= 0 || !$.isNumeric(value)){
			parent.attr('style','background-color:#ffbdbd');
			error++;
		}
		else{
			parent.removeAttr('style');
		}
	});
	if(error > 0){
		alert('Please enter valid price(s) in the highlighted column(s).');
	}
	else if(count <= 0){
		alert('Change atleast one price and try again!');
	}
	else if(count > 0){
		var c = confirm('This will update '+count+' product prices. Are you sure to continue?');
		if(c){
			loading();
			$.ajax({
				url: base_url+'admin/storeproducts/update_store_price',
				type: 'POST',
				dataType: 'json',
				data: {
					price_list: JSON.stringify(price_array)
				},
				success: function(data){
					unloading();
					if(data.result == 1){
						window.location.reload();
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
	}
});

function add_error_price(){
	$.each(price_array, function( index, value ) {
		var parent = $('#store_price_'+index).parent().parent();
		if(value <= 0 || !$.isNumeric(value)){
			parent.attr('style','background-color:#ffbdbd');
		}
		else{
			parent.removeAttr('style');
		}
	});
}