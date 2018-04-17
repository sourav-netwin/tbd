// User Datatables
$(document).ready(function(){
	
	var base_url = $("#base_url").val();
	
	
	if($('#promotions-table').length > 0){
		oTable = $('#promotions-table').dataTable({
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": base_url+'admin/promotions/datatable/',
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"iDisplayLength":25,
			"lengthMenu": [ 25, 50, 75, 100 ],
			"dom": '<"top"f>rt<"bottom row"<"col-md-3"l><"col-md-3"i><"col-md-6"p>>',
			"columns": [
			{
				data: "RetailerName"
			},
			{
				data : "SpecialName"
			},			
                        {
				data : "SpecialFrom"
			},                        
                        {
				data : "SpecialTo"
			}
                        /*
                        ,
			{
				data: "Actions",
				"bSortable" : false,
				"aTargets" : [ "no-sort" ]
			}
                        */
			],
			"oLanguage": {
				"sProcessing": "<img src='../../assets/admin/img/ajax-loader_dark.gif'>"
			},
			"dom": '<"top"f>rt<"bottom row"<"col-md-3"l><"col-md-3"i><"col-md-6"p>>',
			'fnServerData': function (sSource, aoData, fnCallback) {
				sSource = sSource;
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
				$(nRow).attr('data-id',aData['u_id']);
			},
			"fnRowCallback" : function(nRow, aData, iDisplayIndex){
				/*
                                var userId = aData['u_id'];
                                var baseUrl = $('#baseUrl').attr('data-id');
                                    url = baseUrl + "/users/get_loyalty_balance/"+userId;
                                
                                $.ajax({
                                        url: url,
                                        data: {},
                                        success: function(data){
                                               
                                        }
                                });
                                */
                               
                                //var html = status_html(aData["active"]);
                                //$("td:eq(4)", nRow).prepend(html);
                                                         
				//$("td:eq(3)", nRow).prepend(html);                                
                                     
			},
			fnDrawCallback: function( oSettings ) {
				$('div.dataTables_filter input').addClass("form-control");
				$(".pagination li").removeClass("ui-button ui-state-default");
				$(".first.disabled, .previous.disabled, .next.disabled, .last.disabled, .fg-button.active").off( "click" );
				$(".first.disabled a, .previous.disabled a, .next.disabled a, .last.disabled a, .fg-button.active a").attr('href','javascript:void(0);');
			}
		});
	}
		
	

	// se si chiude la modale resettiamo i dati dell'iframe per impedire ad un video di continuare a riprodursi anche quando la modale è chiusa
	$('#myModal').on('hidden.bs.modal', function(){
		$(this).find('iframe').html("");
		$(this).find('iframe').attr("src", "");
	});
	
});

$('table#promotions-table tbody').on('click','.loyalty', function(e) {
        e.preventDefault();
        var url = $(this).attr('data-href');
        $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: {},
                success: function(data){
                    createModal('show-user-loyalty-points-modal', 'Show Loyalty - '+data.name, data.html,'wd-10');
                    
                    $('#show-user-loyalty-points-modal').find('.modal-dialog').css({
                        width:'450'
                    });
                }
        });

});