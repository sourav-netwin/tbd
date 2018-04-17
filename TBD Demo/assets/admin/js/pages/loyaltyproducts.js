var base_url = '';
$(document).ready(function(){
	validate_products_form();
	// Products Datatables
	base_url = $("#base_url").val();

	oTable = $('#products-table').dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": base_url+'admin/loyaltyproducts/datatable/',
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"iDisplayLength":25,
		"lengthMenu": [ 25, 50, 75, 100 ],
		"dom": '<"top"f>rt<"bottom row"<"col-md-3"l><"col-md-3"i><"col-md-6"p>>',
		"columns": [
                   
                {
                    data: "Id",
                    "bVisible" : false
		}, 
                {
			data: "LoyaltyTitle",
                        className: "productName"
		},
		//  { data : "ProductImage", "bSortable" : false, "aTargets" : [ "no-sort" ] },
                {
			data: "CategoryName"
		},
		{
			data: "BrandName"
		},
		{
			data: "StartDate"
		},
		{
			data: "EndDate"
		},
                {
			data: "LoyaltyPoints"
		},
		{
			data: "Actions", 
			"bSortable" : false, 
			"aTargets" : [ "no-sort" ]
		}
		],
                "order": [[0, 'desc' ]],
		"oLanguage": {
			"sProcessing": "<img src='../assets/admin/img/ajax-loader_dark.gif'>"
		},
		"dom": '<"top"f>rt<"bottom row"<"col-md-3"l><"col-md-3"i><"col-md-6"p>>',
		'fnServerData': function (sSource, aoData, fnCallback) {			
                        var category_id = $("#category_id").val();

			sSource = sSource+''+category_id;
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
			$("td:eq(6)", nRow).prepend(html);

			//Warning for no image
			if(!aData["ProductImage"]) {
				var warning_txt = '<a href="javascript:void(0)" ><i class="fa fa-fw fa-warn fa-exclamation-triangle" title="No image added"></i></a>';
				$("td:eq(6)", nRow).append(warning_txt);
			}
		},
		fnDrawCallback: function( oSettings ) {
			$('div.dataTables_filter input').addClass("form-control");
			$(".pagination li").removeClass("ui-button ui-state-default");
			$(".first.disabled, .previous.disabled, .next.disabled, .last.disabled, .fg-button.active").off( "click" );
			$(".first.disabled a, .previous.disabled a, .next.disabled a, .last.disabled a, .fg-button.active a").attr('href','javascript:void(0);');
		}
	});
        
        
        //Datable change on search filters
        $("#brand_id, #category_id").change(function(){
            oTable.fnDraw();
        });

	$('table#products-table tbody').on('click', '.delete', function () {
		var data = $(this).parents('tr').data();

		if (confirm("Are you sure you want to delete this loyalty product?")) {
			window.location = "loyaltyproducts/delete/" + data['id'];
		}
	});
	
	$('table#products-table tbody').on('click','.Edit', function(e) {
		e.preventDefault();
		var elm = $(this);
		var parent = elm.parent().parent().parent();
		var product_name = parent.find('.productName').html();
		var url = elm.attr('data-href');
		$.ajax({
			url: url,
			data: {},
			success: function(data){
				createModal('edit-product-catalogue-modal', 'You are editing <b>'+product_name+'</b>', data,'wd-50');
				validate_products_form();
				$('select').select2();
                                
                                // Initialize date pickers 
                                
                                $("#StartDate").datepicker({
                                        format: 'yyyy-mm-dd',
                                        todayHighlight: true
                                });

                                $("#EndDate").datepicker({
                                        format: 'yyyy-mm-dd',
                                        todayHighlight: true
                                });

                                $('#StartDate').datepicker({
                                        format: 'yyyy-mm-dd',
                                        autoclose: true
                                }).on('changeDate', function(selected){
                                        startDate = new Date(selected.date.valueOf());
                                        startDate.setDate(startDate.getDate(new Date(selected.date.valueOf())));
                                        $('#EndDate').datepicker('setStartDate', startDate);
                                });

                                $('#EndDate').datepicker({
                                        format: 'yyyy-mm-dd',
                                        autoclose: true
                                }).on('changeDate', function(selected){
                                        FromEndDate = new Date(selected.date.valueOf());
                                        FromEndDate.setDate(FromEndDate.getDate(new Date(selected.date.valueOf())));
                                        $('#StartDate').datepicker('setEndDate', FromEndDate);
                                });
        
			}
		});
			
	});

	$('table#products-table tbody').on('click', '.active', function () {
		var data = $(this).parents('tr').data();

		var status = $(this).data('status');
		if (confirm("Are you sure you want to change the status?")) {
			window.location = "loyaltyproducts/change_status/" + data['id'] +"/"+status;
		}
	});

    
        

	$("#import_products_form").validate({
		errorElement: "div",
		ignore: [],
		rules: {
			import_file :{
				required: true,
				checkFileExcel:true
			},
			import_zip_file:{
				//                required: true,
				checkZipFile: true
			}
		},
		messages: {
			import_file :{
				required: "Please upload file to import"
			},
			import_zip_file:{
		//                required: "Please upload zip file"
		}
		}
	});

	$(document).on('click','#import_products', function(e){
		$("#import_products_form").submit();
	});

	$(document).on('change','.search_filter_container #brand_id', function(e){

		var element = $(this).attr("id");
		var val = $(this).val();

                /*
		$.ajax({
			url : base_url+'admin/products/get_category_listing',
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
            */
	});
        
        $("#StartDate").datepicker({
		format: 'yyyy-mm-dd',
		todayHighlight: true
	});

	$("#EndDate").datepicker({
		format: 'yyyy-mm-dd',
		todayHighlight: true
	});

	$('#StartDate').datepicker({
		format: 'yyyy-mm-dd',
		autoclose: true
	}).on('changeDate', function(selected){
		startDate = new Date(selected.date.valueOf());
		startDate.setDate(startDate.getDate(new Date(selected.date.valueOf())));
		$('#EndDate').datepicker('setStartDate', startDate);
	});

	$('#EndDate').datepicker({
		format: 'yyyy-mm-dd',
		autoclose: true
	}).on('changeDate', function(selected){
		FromEndDate = new Date(selected.date.valueOf());
		FromEndDate.setDate(FromEndDate.getDate(new Date(selected.date.valueOf())));
		$('#StartDate').datepicker('setEndDate', FromEndDate);
	});
        
});


//Validate Product Form
function validate_products_form(){
	$("#products_form").validate({
		ignore: [],
		rules: {
			LoyaltyTitle : {
				required: true
			},			
			LoyaltyDescription : {
				required: true
			},
                        CategoryId : {
				required: true
			},
                        BrandName : {
				required: true
			},
                        StartDate : {
				required: true
			},
			EndDate : {
				required: true
			},
			LoyaltyPoints:{
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
			LoyaltyTitle : {
				required: "Please enter Product name"
			},			
			LoyaltyDescription : {
				required: "Please enter description"
			},
                        CategoryId : {
				required: "Please select category"
			},
                        BrandName : {
				required: "Please enter Brand name"
			},
                        StartDate : {
				required: "Please select start date"
			},
			EndDate : {
				required: "Please select end date"
			},
			LoyaltyPoints:{
				required: "Please enter product price"
			}
                        //            product_image :{
                        //                required: "Please upload product image"
                        //            }
		}
	});
}


$('body').on('click', '#submit_product_edit', function(){
	submit_product_form();
});

function submit_product_form(){
	var elm = $('.modalCustom #products_form');
	var url = elm.attr('action');
	loading();
	$.ajax({
		url: url,
		type: 'POST',
		dataType: 'json',
		data: new FormData($('.modalCustom #products_form')[0]), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
		contentType: false,       // The content type used when sending data to the server.
		cache: false,             // To unable request pages to be cached
		processData:false,
		success: function(data){                        
			unloading();
			if(data.result == 1){
				Command: toastr["success"](data.message);
				removeModal('edit-product-catalogue-modal');
				oTable.fnStandingRedraw();
				//location.reload();
			}
			else{
				placeError(data.result,data.message,'products_form')
			}
		},
		error: function(){
			unloading();
		}
	});
}

//$('body').on('submit','.modalCustom #products_form', function(e){
//	e.preventDefault();
//	e.stopImmediatePropagation();
//	
//});

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

