var base_url = '';
$(document).ready(function(){
	validate_advertisement_form();
        show_client_area();
        
	// Products Datatables
	base_url = $("#base_url").val();

	oTable = $('#advertisement-table').dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": base_url+'admin/advertisements/datatable/',
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
                            data: "AdvertisementTitle",
                            className: "adTitle"
                    },		
                    {
                            data: "StartDate","bSortable" : false
                    },
                    {
                            data: "EndDate","bSortable" : false
                    },                
                    {
                            data: "Actions", 
                            "bSortable" : false, 
                            "aTargets" : [ "no-sort" ]
                    }
		],
                "order": [[0, 'DESC' ]],                
		"oLanguage": {
			"sProcessing": "<img src='../assets/admin/img/ajax-loader_dark.gif'>"
		},
		"dom": '<"top"f>rt<"bottom row"<"col-md-3"l><"col-md-3"i><"col-md-6"p>>',
		'fnServerData': function (sSource, aoData, fnCallback) {			
                        sSource = sSource;
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
			$("td:eq(3)", nRow).prepend(html);

			//Warning for no image
			if(!aData["AdvertisementImage"]) {
                            var warning_txt = '<a href="javascript:void(0)" ><i class="fa fa-fw fa-warn fa-exclamation-triangle" title="No image added"></i></a>';
                            $("td:eq(3)", nRow).append(warning_txt);
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

	$('table#advertisement-table tbody').on('click', '.delete', function () {
            var data = $(this).parents('tr').data();

            if (confirm("Are you sure you want to delete this advertisement?")) {
                    window.location = "advertisements/delete/" + data['id'];
            }
	});
	
	$('table#advertisement-table tbody').on('click','.Edit', function(e) {
		e.preventDefault();
		var elm = $(this);
		var parent = elm.parent().parent().parent();
		var product_name = parent.find('.adTitle').html();
		var url = elm.attr('data-href');
		$.ajax({
			url: url,
			data: {},
			success: function(data){
				//createModal('edit-product-catalogue-modal', 'You are editing <b>'+product_name+'</b>', data,'wd-50');
                                createModal('edit-product-catalogue-modal', 'You are editing <b>'+product_name+'</b>', data,'wd-75');
				validate_advertisement_form();
				$('select').select2();
                                
                                show_client_area();
                                
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

	$('table#advertisement-table tbody').on('click', '.active', function () {
            var data = $(this).parents('tr').data();

            var status = $(this).data('status');
            if (confirm("Are you sure you want to change the status?")) {
                    window.location = "advertisements/change_status/" + data['id'] +"/"+status;
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
                        // required: "Please upload zip file"
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
function validate_advertisement_form(){
	$("#advertisement_form").validate({
		ignore: [],
		rules: {
                        MainCategoryId: {
                            //required: true
                            required: "#home_page:unchecked"
			},
			AdvertisementTitle : {
				required: true
			},			
			AdvertisementDescription : {
				required: true
			},
                        /*
                        AdvertisementUrl : {
				required: true
			},*/
                        StartDate : {
				required: true
			},
			EndDate : {
				required: true
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
                        ,
                        CompanyName: {
                            //required: true
                            required: "#new_client:checked"
			},
                        ClientEmail: {
                            //required: true
                            required: "#new_client:checked"
			},
                        ContactNumber: {
                            //required: true
                            required: "#new_client:checked"
			},
                        ContactPerson: {
                            //required: true
                            required: "#new_client:checked"
			},
                        RetailerId: {
                            //required: true
                            required: "#existing_client:checked"
			},
                        StoreTypeId: {
                            //required: true
                            required: "#existing_client:checked"
			},
                        StoreId: {
                            //required: true
                            required: "#existing_client:checked"
			}
            
		},
		messages: {
                        MainCategoryId : {
                            required: "Please enter category"
			},
			AdvertisementTitle : {
                            required: "Please enter advertisement title"
			},			
			AdvertisementDescription : {
                            required: "Please enter description"
			}, 
                        /*
                        AdvertisementUrl : {
                            required: "Please enter advertisement url"
			},*/
                        StartDate : {
                            required: "Please select start date"
			},
			EndDate : {
                            required: "Please select end date"
			}
                        // product_image :{
                        //     required: "Please upload product image"
                        // }
                        ,
                        CompanyName : {
                            required: "Please enter company name"
			}, 
                        ClientEmail : {
                            required: "Please enter client email"
			}, 
                        ContactNumber : {
                            required: "Please enter contact number"
			}, 
                        ContactPerson : {
                            required: "Please enter contact person"
			},
                        RetailerId : {
                            required: "Please select retailer"
			}, 
                        StoreTypeId : {
                            required: "Please select store format"
			}, 
                        StoreId : {
                            required: "Please select store"
			}
		}
	});
}


$('body').on('click', '#submit_advertisement_edit', function(){
    submit_advertisement_form();
});

function submit_advertisement_form(){
	var elm = $('.modalCustom #advertisement_form');
	var url = elm.attr('action');
	loading();
	$.ajax({
		url: url,
		type: 'POST',
		dataType: 'json',
		data: new FormData($('.modalCustom #advertisement_form')[0]), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
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
				placeError(data.result,data.message,'advertisement_form')
			}
		},
		error: function(){
			unloading();
		}
	});
}

/*
$('.rdobtn').click(function() {   
        show_client_area();
    });
 */

 $(document).on('change','.rdobtn', function(e){
    show_client_area(); 
 });

function show_client_area()
{
    if($('#new_client').is(':checked')) {
        $('#new_client_area').show();
        $('#existing_client_area').hide();
    }
    
    if($('#existing_client').is(':checked')) {
        $('#new_client_area').hide();
        $('#existing_client_area').show();
    }  
}

$(document).on('change','#RetailerId', function(e){   
    var element = $(this).attr("id");
    var val = $(this).val();
    if(val == '' || typeof val == 'undefined'){
        $("#StoreTypeId").html('<option value="">Select Store Format</option>');
        $("#StoreTypeId").select2();
    }else{
        $.ajax({
            url : base_url+'admin/stores/get_store_formats/'+val,
            data : {
                id: val,
                type: element
            },
            method : 'POST',
            dataType: 'json',
            success : function(data)
            {
                $("#StoreTypeId").html(data.store_formats);
                $("#StoreTypeId").select2();
            }
        });
    }
});

/* Get stores based on Store Format */
$(document).on('change','#StoreTypeId', function(e){
    //alert("Success : Retailer Change");
    var element = $(this).attr("id");
    var val = $(this).val();
    if(val == '' || typeof val == 'undefined'){
        $("#StoreId").html('<option value="">Select Store</option>');
        $("#StoreId").select2();
    }else{
        $.ajax({
            url : base_url+'admin/stores/get_stores/'+val,
            data : {
                id: val,
                type: element
            },
            method : 'POST',
            dataType: 'json',
            success : function(data)
            {
                $("#StoreId").html(data.stores);               
                $("#StoreId").select2();
            }
        });
    }
});