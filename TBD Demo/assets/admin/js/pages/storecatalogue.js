$(function(){
	var base_url = $("#base_url").val();
	oTable = $('#store-catalogue-table').dataTable({
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": base_url+'admin/storecatalogue/datatable/',
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"iDisplayLength":25,
			"lengthMenu": [ 25, 50, 75, 100 ],
			"dom": '<"top"f>rt<"bottom row"<"col-md-3"l><"col-md-3"i><"col-md-6"p>>',
			"columns": [
//			{
//				data: "selectVal",
//				"bSortable" : false,
//				"aTargets" : [ "no-sort" ]
//			},
			{
				data: "ProductName"
			},
			{
				data : "StoreName"
			},
			{
				data : "RRP"
			},
			{
				data: "SpecialPrice"
			},
			{
				data: "PriceAppliedFrom"
			},
			{
				data: "PriceAppliedTo"
			}
			],
			"oLanguage": {
				"sProcessing": "<img src='../assets/admin/img/ajax-loader_dark.gif'>"
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
				$(nRow).attr('data-user-role',aData['RoleId']);
			},
			fnDrawCallback: function( oSettings ) {
				$('div.dataTables_filter input').addClass("form-control");
				$(".pagination li").removeClass("ui-button ui-state-default");
				$(".first.disabled, .previous.disabled, .next.disabled, .last.disabled, .fg-button.active").off( "click" );
				$(".first.disabled a, .previous.disabled a, .next.disabled a, .last.disabled a, .fg-button.active a").attr('href','javascript:void(0);');
			}
		});
});

