var base_url = '';
$(document).ready(function(){
	validate_order_form();
	// Products Datatables
	base_url = $("#base_url").val();

	oTable = $('#loyaltyorders-table').dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": base_url+'admin/loyaltyorders/datatable/',
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
			data: "OrderNumber",
                        className: "orderNumber"
		},		
                {
			data: "Name",
                        className: "customerName"
                        
		},
		{
			data: "Email"
		},
                {
			data: "OrderTotal"
		},
                {
			data: "OrderStatus"
		},
		{
			data: "CreatedOn"
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
                       //sSource = sSource;
                        
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
			if(aData['isAdminReviewed']==0)
				$(nRow).attr('class','unviewed');
			$(nRow).attr('isAdminReviewed',aData['isAdminReviewed']);
		},
		"fnRowCallback" : function(nRow, aData, iDisplayIndex){
			/*
                        var html = status_html(aData["active"]);
			$("td:eq(6)", nRow).prepend(html);

			//Warning for no image
			if(!aData["ProductImage"]) {
				var warning_txt = '<a href="javascript:void(0)" ><i class="fa fa-fw fa-warn fa-exclamation-triangle" title="No image added"></i></a>';
				$("td:eq(6)", nRow).append(warning_txt);
			}
                        */
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
	
	$('table#loyaltyorders-table tbody').on('click','.Edit', function(e) {
		e.preventDefault();
		var elm = $(this);
		var parent = elm.parent().parent().parent();
		var orderNumber = parent.find('.orderNumber').html();
		
                var customerName = parent.find('.customerName').html();
		var url = elm.attr('data-href');
		$.ajax({
			url: url,
			data: {},
			success: function(data){
				
				parent.removeClass('unviewed');
					if(parent.attr('isadminreviewed')==0 && $('.unReadOrders').text()!='0'){
						$('.unReadOrders').text(parseInt($('.unReadOrders').text(), 10) - 1);
						
						parent.attr('isadminreviewed','1');
					}					
				
				if($('.unReadOrders').text()==0)
					$('.unReadOrders').remove();
				createModal('edit-product-catalogue-modal', 'You are editing <b>#'+orderNumber+'</b> for : <b>'+customerName+'</b>', data,'wd-50');
				validate_order_form();
				$('select').select2();
				
			}
		});
			
	});


//Validate Product Form
function validate_order_form(){
    $("#order_form").validate({
            ignore: [],
            rules: {
                    OrderStatus : {
                            required: true
                    }
            },
            messages: {			
                    OrderStatus : {
                            required: "Please select Order Status"
                    }
            }
    });
}


$('body').on('click', '#submit_order_edit', function(){    
   submit_order_form();
});

function submit_order_form(){
	var elm = $('.modalCustom #order_form');
	var url = elm.attr('action');
	loading();
	$.ajax({
		url: url,
		type: 'POST',
		dataType: 'json',
		data: new FormData($('.modalCustom #order_form')[0]), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
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
				placeError(data.result,data.message,'order_form')
			}
		},
		error: function(){
			unloading();
		}
	});
}
});