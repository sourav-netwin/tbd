var base_url = $("#base_url").val();
$(function(){
	validate_products_form();
	oTable = $('#products-table').dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": base_url+'admin/houseproduct/datatable/',
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
			data: "main_parent_cat"
		},
		{
			data: "parent_cat"
		},
		{
			data: "sub_category"
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
			var product_main_category = $("#product_main_category").val();
			var product_parent_category = $("#product_parent_category").val();
			var product_sub_category = $("#product_sub_category").val();

			sSource = sSource+''+product_main_category+'/'+product_parent_category+'/'+product_sub_category;
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
			$("td:eq(4)", nRow).prepend(html);

			//Warning for no image
			if(!aData["ProductImage"]) {
				var warning_txt = '<a href="javascript:void(0)" ><i class="fa fa-fw fa-warn fa-exclamation-triangle" title="No image added"></i></a>';
				$("td:eq(4)", nRow).append(warning_txt);
			}

			//Warning for subcategory
			if(!aData["sub_category"]) {
				//                var warning_cat_txt = '<a href="javascript:void(0)"><em class="small">Level 2 Product</em></a>';
				//$("td:eq(3)", nRow).append(aData["parent_cat"]);
			}

		},
		fnDrawCallback: function( oSettings ) {
			$('div.dataTables_filter input').addClass("form-control");
			$(".pagination li").removeClass("ui-button ui-state-default");
			$(".first.disabled, .previous.disabled, .next.disabled, .last.disabled, .fg-button.active").off( "click" );
			$(".first.disabled a, .previous.disabled a, .next.disabled a, .last.disabled a, .fg-button.active a").attr('href','javascript:void(0);');
		}
	});
});

$(document).on('change','#product_main_category, #product_parent_category', function(e){

	var element = $(this).attr("id");
	var val = $(this).val();

	$.ajax({
		url : base_url+'admin/houseproduct/get_category_listing',
		data : {
			id: val, 
			type: element
		},
		method : 'POST',
		dataType: 'json',
		success : function(data)
		{
			if(element == 'product_main_category')
			{
				$("#product_parent_category").html(data.parent_category);
				$("#product_parent_category").select2();

				if( data.sub_category != '' )
				{
					$("#product_sub_category").html(data.sub_category);
					$("#product_sub_category").select2();
				}
			}
			else
			{
				$("#product_sub_category").html(data.parent_category);
				$("#product_sub_category").select2();
			}
			$(".select2-selection__rendered").removeAttr('title');
		}
	});
});

//Validate Product Form
function validate_products_form(){
	$("#products_form").validate({
		ignore: [],
		rules: {
			product_main_category : {
				required: true
			},
			product_parent_category : {
				required: true
			},
			//            product_sub_category : {
			//                required: true
			//            },

			product_name : {
				required: true
			},
			brand : {
				required: true
			},
			product_description : {
				required: true
			},
			product_rate:{
				required: true,
				number: true
			}/*,
            product_image :{
                required: {
                    depends: function(element) {
                        return ( $("#old_product_image").length == 1 ? false : true );
                    }
                },
                checkEmpty:"Please upload image file",
                checkFile:true
            }*/
		},
		messages: {
			product_main_category : {
				required: "Please select main category"
			},
			product_parent_category : {
				required: "Please select category"
			},
			//            product_sub_category : {
			//                required: "Please select sub category"
			//            },

			product_name : {
				required: "Please enter Product name"
			},
			brand : {
				required: "Please enter Brand"
			},
			product_description : {
				required: "Please enter product description"
			},
			product_rate:{
				required: "Please enter product price"
			}
		//            product_image :{
		//                required: "Please upload product image"
		//            }
		}
	});
}

$('table#products-table tbody').on('click', '.active', function () {
	var data = $(this).parents('tr').data();

	var status = $(this).data('status');
	if (confirm("Are you sure you want to change the status?")) {
		window.location = "houseproduct/change_status/" + data['id'] +"/"+status;
	}
});

$('table#products-table tbody').on('click', '.delete', function () {
	var data = $(this).parents('tr').data();

	if (confirm("Are you sure you want to delete this product?")) {
		window.location = "houseproduct/delete/" + data['id'];
	}
});

$('table#products-table tbody').on('click','.Edit', function(e) {
	e.preventDefault();
	var elm = $(this);
	var parent = elm.parent().parent().parent();
	var product_name = parent.find('.sorting_1').html();
	var url = elm.attr('data-href');
	$.ajax({
		url: url,
		data: {},
		success: function(data){
			createModal('edit-product-catalogue-modal', 'You are editing <b>'+product_name+'</b>', data,'wd-50');
			validate_products_form();
		}
	});
			
});

$('body').on('submit','.modalCustom #products_form', function(e){
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
				placeError(data.result,data.message,'products_form')
			}
		},
		error: function(){
			unloading();
		}
	});
});


