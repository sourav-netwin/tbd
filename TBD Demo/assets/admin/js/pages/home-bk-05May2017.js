var user_tb_obj='';
var ret_user_tb_obj='';
var ret_store_tb_obj='';
var rev_store_tb_obj='';
var rev_special_tb_obj='';
var rev_con_ret_tb_obj='';
var rev_region_tb_obj='';
var rev_gender_tb_obj='';
var rev_device_tb_obj='';
var rev_cat_sub_tb_obj='';
var rev_ret_stst_tb_obj='';
var chart = '';
var pro_chart = '';
var base_url = '';
$(document).ready(function(){
	base_url = $('#base_url').val();
	initTimepicker();
        
        // Added by MK - 06Apr17
        if($('#store_products_count').length > 0){
            getStoreProductsCounts();
        }
        
	//	$(document).on('change','#report_selector', function(e){
	//
	//		var curr_report= $('#report_selector').val();
	//
	//		if(curr_report=='stores_state_report')
	//		{
	//			$('.retailer_selection').removeClass('hide');
	//
	//		} else {
	//
	//			$('.retailer_selection').removeClass('hide');
	//			$('.retailer_selection').addClass('hide');
	//		}
	//
	//		$.getScript(javascriptPath, curr_report);
	//
	//
	//
	//		$('.reports').addClass('hide');
	//
	//		$('#store_details').addClass('hide');
	//		$('#products_details').addClass('hide');
	//
	//		$('#'+curr_report).removeClass('hide');
	//		$(window).resize();
	//
	//	});
	
	
	
	
	
	$(document).on('click','.report_selector', function(e){

		$('.report_selector').each(function(){
			$(this).removeClass('rep_active');
		});
		var elm = $(this); 
		elm.addClass('rep_active');
		var curr_report= elm.attr('data-val');

		if(curr_report=='stores_state_report')
		{
			$('.retailer_selection').removeClass('hide');

		} else {

			$('.retailer_selection').removeClass('hide');
			$('.retailer_selection').addClass('hide');
		}
		

		$.getScript(javascriptPath, curr_report);



		$("#user_listing tbody").html('');
		$('#user_state_name').text('');
		$("#retailer_user_details tbody").html('');
		$('#user_retailer_name').text('');

		$("#retailer_user_details").addClass('hide');
		$("#user_details").addClass('hide');
		$('.reports').addClass('hide');

		$('#store_details').addClass('hide');
		$('#products_details').addClass('hide');

		$('#'+curr_report).removeClass('hide');
		if(curr_report == 'users_stores_report'){
			chart.render();
		}
		if(curr_report == 'product_stores_report'){
			pro_chart.render();
		}
		$(window).resize();

	});

	$(document).on('click','.state_count', function(e){

		var state_code = $(this).attr("data-code");

		var state_name = $(this).attr("data-name");

		get_stores(state_name,state_code);
	});


	//Onclick of retailer

	$(document).on('change','#retailers,#store_format', function(e){

		var val = $('#retailers').val();

		var store_format_id = $('#store_format').val();

		$.ajax({
			url : 'stores_states/'+val+'/'+store_format_id,

			method : 'POST',
			dataType: 'json',
			success : function(data)
			{
				store_data = data.result_stores;
				$.getScript(javascriptPath, stores_state_report);

				$('#stores_state_report_table').html(data.result_stores_table);
			}
		});
	});

	//INITIALIZE MAP

	var baseMapPath = "http://code.highcharts.com/mapdata/",
	showDataLabels = false, // Switch for data labels enabled/disabled
	mapCount = 0,
	searchText,
	mapOptions = '',
	mapDesc = 'South Africa',
	mapKey = 'countries/za/za-all.js',
	map = 'countries/za/za-all',
	javascriptPath = baseMapPath + mapKey;

	function pointClick() {
		return this.name;
	}

	function users_state_report()
	{
		if($('#user_container').length > 0){
			// Users Map
			// Initiate the chart
			$('#user_container').highcharts('Map', {
				exporting: {
					enabled: false
				},
				credits: {
					enabled: false
				},
				title : {
					text : null
				},

				subtitle : {
					text : null
				},
				mapNavigation: {
					enabled: false,
					buttonOptions: {
						verticalAlign: 'bottom'
					}
				},
				chart : {
					backgroundColor: null
				},
				tooltip: {
					backgroundColor: '#fff',
					borderWidth: 0,
					shadow: false,
					useHTML: true,
					padding: 0,
					pointFormat: '<span class="f32"></span>'+ ' <span style="font-size:13px">{point.name}</span>: <span style="font-size:15px"><b>{point.value}</b></span> users'

				},
				colorAxis: {
					min: 0,
					stops: [
					[0, '#EEEEEE'],
					[0.5, '#CCCCCC'],
					[1, '#666666']
					]
				},

				series : [{
					data : user_data,
					mapData: Highcharts.maps[map],
					joinBy: 'hc-key',
					name: 'Users',
					states: {
						hover: {
							color: '#FFFFFF'
						}
					},
					dataLabels: {
						enabled: false,
						format: '{point.name}'
					},
					point: {
						events: {
							click: function () {
								var name = this.name;
								var key = this.st_key;
								get_users(name,key);
							}
						}
					}
				},
				{
					name: 'Separators',
					type: 'mapline',
					data: Highcharts.geojson(Highcharts.maps['countries/za/za-all'], 'mapline'),
					color: '#666666',
					showInLegend: false,
					enableMouseTracking: false
				},
				{
					type: 'mappoint',
					name: 'Users',
					color: Highcharts.getOptions().colors[1],
					dataLabels: false

				}
				]
			});
		}

	}
	$.getScript(javascriptPath, users_state_report);

	function stores_state_report() {
		// Initiate the chart
		if($('#container').length > 0){
			$('#container').highcharts('Map', {
				exporting: {
					enabled: false
				},
				credits: {
					enabled: false
				},
				title : {
					text : null
				},

				subtitle : {
					text : null
				},
				chart : {
					backgroundColor: null
				},
				mapNavigation: {
					enabled: false,
					buttonOptions: {
						verticalAlign: 'bottom'
					}
				},
				tooltip: {
					backgroundColor: '#FFFFFF',
					borderWidth: 0,
					shadow: false,
					useHTML: true,
					padding: 0,
					pointFormat: '<span class="f32"></span>'+ ' <span style="font-size:13px">{point.name}</span>: <span style="font-size:15px"><b>{point.value}</b></span> stores'

				},
				colorAxis: {
					min: 0,
					stops: [
					[0, '#EEEEEE'],
					[0.5, '#CCCCCC'],
					[1, '#666666']
					]
				},

				series : [{
					data : store_data,
					mapData: Highcharts.maps[map],
					joinBy: ['hc-key', 'key'],
					name: 'Stores',
					states: {
						hover: {
							color: '#FFFFFF'
						}
					},
					dataLabels: {
						enabled: false,
						format: '{point.name}'
					},
					point: {
						events: {
							click: function () {
								var name = this.name;
								var key = this.key;

								get_stores(name,key);

							}
						}
					}
				},
				{
					name: 'Separators',
					type: 'mapline',
					data: Highcharts.geojson(Highcharts.maps['countries/za/za-all'], 'mapline'),
					color: '#707070',
					showInLegend: false,
					enableMouseTracking: false
				},
				{
					type: 'mappoint',
					name: 'Stores',
					color: Highcharts.getOptions().colors[1],
					dataLabels: true,
					data: [

				]
				}
				]
			});
		}

	}
	$.getScript(javascriptPath, stores_state_report);


	//HighCharts

	// Create the chart
	 
	//	$("#retailer_user").CanvasJSChart({ 
	//		title: { 
	//			text: "" 
	//		}, 
	//		data: [ 
	//		{ 
	//			type: "doughnut", 
	//			animationEnabled: true,
	//			indexLabel: "{label}: {y}%",
	//			toolTipContent: "{label}: {y}%",
	//			dataPoints: retailer_users ,
	//			click: test_click,
	//			cursor:"pointer"
	//		} 
	//		] 
	//	});
	
	CanvasJS.addColorSet("customColor1",
		[
		"#FFA200"             
		]);
		
	CanvasJS.addColorSet("customColor2",
		[
		"#FB5532"             
		]);
	CanvasJS.addColorSet("customColor3",
		[
		"#32A6FB"             
		]);
	CanvasJS.addColorSet("customColor4",
		[
		"#04A100"             
		]);
	CanvasJS.addColorSet("customColor4",
		[
		"#6D50FF"             
		]);
	CanvasJS.addColorSet("expandColor",
		[
		"#66CDAA",           
		"#778899",           
		"#F08080",           
		"#F0E68C",           
		"#DAA520",           
		"#1E90FF",           
		"#DC143C", 
		"#9ACD32",           
		"#EE82EE",           
		"#40E0D0",           
		"#FF6347",           
		"#008080",           
		"#4682B4",           
		"#6A5ACD",           
		"#A0522D",           
		"#F4A460",           
		"#FA8072",           
		"#4169E1",           
		"#D87093",           
		"#98FB98",           
		"#FF4500",           
		"#000080",           
		"#C71585",           
		"#00FA9A"         
		]);
	
	if($('#retailer_user').length > 0){
		chart = new CanvasJS.Chart("retailer_user",
		{
        
			data: [
			{
				indexLabel: "{label}: {y}",
				toolTipContent: "{label}: {y}",
				type: "doughnut",
				cursor:"pointer",
				animationEnabled: true,
				responsive:false,
				dataPoints: retailer_users
			}
			],
			colorSet:  "colorSet3"
		});
		chart.options.data[0].click = function(e){
			//alert(e.dataPointIndex);
			var dataSeries = e.dataSeries;
			var dataPoint = e.dataPoint;
			var dataPointIndex = e.dataPointIndex;
    
			for(var i = 0; i < dataSeries.dataPoints.length; i++){
				if(i === dataPointIndex){
					dataSeries.dataPoints[i].exploded = true;            
				}else        
					dataSeries.dataPoints[i].exploded = false;            
			}
			get_retailer_users(e);
    
    
			chart.render();
		};
		chart.render();
	}
	
	

	
	
	var config = {
		type: 'doughnut',
		circumference:20,
		data: {
			datasets: [
			{
				data: [10,20,30],
				backgroundColor: ["#F7464A","#46BFBD","#FDB45C"]
			}
			],
			labels: [
			"Principal Amount",
			"Interest Amount",
			"Processing Fee"
			]
		},
		options: {
			responsive: true,
			legend: {
				display: false
			}
		}
	};
	
	//	var ctx = document.getElementById("retailer_count").getContext("2d");
	//	window.myPie = new Chart(ctx, config);
	
	//	var myDoughnutChart = new Chart(donutChartCanvas);
	//	var options = {};
	//	myDoughnutChart.Doughnut(retailer_count, options);
	
	if($('#retailer_count').length > 0){
		var retailer_count_chart = new CanvasJS.Chart("retailer_count",
		{
			backgroundColor: "transparent",
			data: [
			{
				type: "doughnut",
				showInLegend: false,
				toolTipContent: "{name}: {y}",
				cursor:"pointer",
				dataPoints: [
				{
					name: 'Retailers',
					y: '10'
				}
				],
				click: retailer_details_show
			}
			],
			colorSet:  "customColor1",
			toolTip:{
				enabled: false
			}
		});
	
		retailer_count_chart.render();
	}
	
	/*var stores_count_chart = new CanvasJS.Chart("stores_count",
	{
		backgroundColor: "transparent",
		data: [
		{
			type: "doughnut",
			showInLegend: false,
			toolTipContent: "{name}: {y}",
			cursor:"pointer",
			dataPoints: [
			{
				name: 'Stores',
				y: '10'
			}
			],
			click: stores_details_show
		}
		],
		colorSet:  "customColor2",
		toolTip:{
			enabled: false
		}
	});
	
	stores_count_chart.render();*/
	if($('#users_count').length > 0){
		var users_count_chart = new CanvasJS.Chart("users_count",
		{
			backgroundColor: "transparent",
			data: [
			{
				type: "doughnut",
				showInLegend: false,
				toolTipContent: "{name}: {y}",
				cursor:"pointer",
				dataPoints: [
				{
					name: 'Users',
					y: '10'
				}
				],
				click: consumer_details_show
			}
			],
			colorSet:  "customColor3",
			toolTip:{
				enabled: false
			}
		});
	
		users_count_chart.render();
	}
	
	if($('#products_count').length > 0){
		var products_count_chart = new CanvasJS.Chart("products_count",
		{
			backgroundColor: "transparent",
			data: [
			{
				type: "doughnut",
				showInLegend: false,
				toolTipContent: "{name}: {y}",
				cursor:"pointer",
				dataPoints: [
				{
					name: 'Products',
					y: '10'
				}
				],
				click: products_details_show
			}
			],
			colorSet:  "customColor4",
			toolTip:{
				enabled: false
			}
		});
	
		products_count_chart.render();
	}
	
	/*var categories_count_chart = new CanvasJS.Chart("categories_count",
	{
		backgroundColor: "transparent",
		data: [
		{
			type: "doughnut",
			showInLegend: false,
			toolTipContent: "{name}: {y}",
			cursor:"pointer",
			dataPoints: [
			{
				name: 'Products',
				y: '10'
			}
			],
			click: categories_details_show
		}
		],
		colorSet:  "customColor5",
		toolTip:{
			enabled: false
		}
	});
	
	categories_count_chart.render();*/
	
	 
	 
	 
	 
	//	$('#retailer_user').highcharts({
	//		exporting: {
	//			enabled: false
	//		},
	//		chart: {
	//			type: 'column',
	//			backgroundColor: null,
	//			style: {
	//				color: "#FFFFFF"
	//			}
	//		},
	//		title: {
	//			text: 'Number of Users per having Retailers as their preferred brand ',
	//			style: {
	//				"color": "#FFFFFF",
	//				"fontSize": "18px"
	//			}
	//		},
	//		subtitle: {
	//			text: ''
	//		},
	//		xAxis: {
	//			type: 'category',
	//			labels: {
	//				style: {
	//					color: "#FFFFFF"
	//				}
	//			}
	//		},
	//		yAxis: {
	//			title: {
	//				text: 'Total number of users'
	//			}
	//		},
	//		legend: {
	//			enabled: false
	//		},
	//		plotOptions: {
	//			series: {
	//				borderWidth: 0,
	//				dataLabels: {
	//					enabled: true,
	//					format: '{point.y:.1f}',
	//					style: {
	//						color: "#FFFFFF"
	//					}
	//				},
	//				style: {
	//					color: "#FFFFFF"
	//				},
	//				point: {
	//					events: {
	//						click: function () {
	//							console.log(this);
	//							get_retailer_users(this.id, this.name);
	//						}
	//					}
	//				}
	//			}
	//		},
	//
	//		tooltip: {
	//			headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
	//			pointFormat: '<span style="color:{point.color}">{point.name}</span>: <span style="font-size:22px"><b>{point.y:.2f}</b> of total</span><br/>'
	//		},
	//
	//		series: [
	//		{
	//			name: "Retailers",
	//			colorByPoint: true,
	//			data: retailer_users,
	//			dataLabels: {
	//				style: {
	//					color: 'white'
	//				}
	//			}
	//		}
	//		]
	//
	//	});


	// Create the chart
	if($('#products_users').length > 0){
		pro_chart = new CanvasJS.Chart("products_users",
		{
        
			data: [
			{
				indexLabel: "{name}: {y}",
				toolTipContent: "{name}: {y}",
				type: "doughnut",
				cursor:"pointer",
				animationEnabled: true,
				responsive:false,
				dataPoints: products_users
			}
			],
			colorSet:  "colorSet3"
		});
		pro_chart.options.data[0].click = function(e){
			//alert(e.dataPointIndex);
			var dataSeries = e.dataSeries;
			var dataPoint = e.dataPoint;
			var dataPointIndex = e.dataPointIndex;
    
			for(var i = 0; i < dataSeries.dataPoints.length; i++){
				if(i === dataPointIndex){
					dataSeries.dataPoints[i].exploded = true;            
				}else        
					dataSeries.dataPoints[i].exploded = false;            
			}
			get_products(e);
    
    
			pro_chart.render();
		};
		pro_chart.render();
	}
	
	

	
	 
	 
	 
	 
	//	$('#products_users').highcharts({
	//		exporting: {
	//			enabled: false
	//		},
	//		chart: {
	//			type: 'column',
	//			backgroundColor: null
	//		},
	//		title: {
	//			text: 'Number of product views per retailer',
	//			style: {
	//				"color": "#FFFFFF",
	//				"fontSize": "18px"
	//			}
	//		},
	//		subtitle: {
	//			text: ''
	//		},
	//		xAxis: {
	//			type: 'category'
	//		},
	//		yAxis: {
	//			title: {
	//				text: 'Total count of product views'
	//			}
	//
	//		},
	//		legend: {
	//			enabled: false
	//		},
	//		plotOptions: {
	//			series: {
	//				borderWidth: 0,
	//				dataLabels: {
	//					enabled: true,
	//					format: '{point.y:.1f}'
	//				},
	//				point: {
	//					events: {
	//						click: function () {
	//							get_products(this.name);
	//						}
	//					}
	//				}
	//			}
	//		},
	//
	//		tooltip: {
	//			headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
	//			pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}</b> of total<br/>'
	//		},
	//
	//		series: [{
	//			name: "Retailers",
	//			colorByPoint: true,
	//			data: products_users
	//
	//		}]
	//
	//	});

	//function to get the states

	function get_stores(state_name,state_code) {
		loading();
		var retailer_id = $('#retailers').val();

		var store_format_id = $('#store_format').val();


		$.ajax({
			url : 'get_retailer_stores/'+state_code+'/'+retailer_id+'/'+store_format_id,

			method : 'POST',
			dataType: 'json',
			success : function(data)
			{
				unloading();
				if(typeof ret_store_tb_obj == 'object'){
					ret_store_tb_obj.fnDestroy();
				}
				$("#store_listing tbody").html(data.html);
				ret_store_tb_obj = $("#store_listing").dataTable({
					"iDisplayLength":100,
					"lengthMenu": [ 25, 50, 75, 100 ],
					"aoColumnDefs": [
					{
						"targets": [2,3],
						"bSortable" : false,
						"visible": false
					}
					],
					fnDrawCallback: function( oSettings ) {
						var elm = $('select[name=store_listing_length]').parent().parent();
						$('select[name=store_listing_length]').parent().remove();
						elm.html('<div class="form-group" style="margin-bottom: 5px;"><label>Search</label><input type="text" id="ret_store_search" class="form-control"></div>');
						elm.parent().switchClass('col-sm-6','col-sm-3');
						elm.parent().after('<div class="col-sm-3"><select id="filt_retailer"><option value="">Select Retailer</option>'+data.retailers+'</select></div><div class="col-sm-3"><select id="filt_store_type"><option value="">Select Store Format</option></select></div>');
						$('select').select2();
					}
				});
				//				$("#store_listing tbody").html(data);
				$('#state_name').text(state_name);

				$('html, body').animate({
					scrollTop:$('#store_details').position().top
				}, 'slow');
				$("#store_details").removeClass('hide');
			},
			error: function(){
				unloading();
			}
		});
	}
	
	function get_users(state_name,state_code) {

		//		var retailer_id = $('#retailers').val();
		//
		//		var store_format_id = $('#store_format').val();

		loading();
		$.ajax({
			url : 'get_retailer_users/'+state_code+'/'+state_name,
			method : 'POST',
			dataType: 'json',
			success : function(data)
			{
				unloading();
				
				$('#user_state_name').text(state_name);

				$('html, body').animate({
					scrollTop:$('#user_details').position().top
				}, 'slow');
				$("#user_details").removeClass('hide');
				if(typeof user_tb_obj == 'object'){
					user_tb_obj.fnDestroy();
				}
				$("#user_listing tbody").html(data);
				user_tb_obj = $("#user_listing").dataTable({
					"iDisplayLength":100,
					"lengthMenu": [ 25, 50, 75, 100 ],
					fnDrawCallback: function( oSettings ) {
						var elm = $('select[name=user_listing_length]').parent().parent();
						$('select[name=user_listing_length]').parent().remove();
						elm.html('<div class="form-group" style="margin-bottom: 5px;"><label>Search</label><input type="text" id="state_user_search" class="form-control"></div>');
					}
				});
					
			},
			error: function(){
				unloading();
			}
		});
	}
	
	function get_retailer_users(e) {
		var retailer_id = e.dataPoint.id;
		var retailer_name = e.dataPoint.label;
		//		var retailer_id = $('#retailers').val();
		//
		//		var store_format_id = $('#store_format').val();

		loading();
		$.ajax({
			url : 'get_retailer_users_have/'+retailer_id,
			method : 'POST',
			dataType: 'json',
			success : function(data)
			{
				unloading();
				
				$('#user_retailer_name').text(retailer_name);

				$('html, body').animate({
					scrollTop:$('#retailer_user_details').position().top
				}, 'slow');
				$("#retailer_user_details").removeClass('hide');
				if(typeof ret_user_tb_obj == 'object'){
					ret_user_tb_obj.fnDestroy();
				}
				$("#retailer_user_listing tbody").html(data);
				ret_user_tb_obj = $("#retailer_user_listing").dataTable({
					"iDisplayLength":100,
					"lengthMenu": [ 25, 50, 75, 100 ],
					fnDrawCallback: function( oSettings ) {
						var elm = $('select[name=retailer_user_listing_length]').parent().parent();
						$('select[name=retailer_user_listing_length]').parent().remove();
						elm.html('<div class="form-group" style="margin-bottom: 5px;"><label>Search</label><input type="text" id="retailer_user_search" class="form-control"></div>');
					}
				});
				$('html, body').animate({
					scrollTop: $("#retailer_user_details").offset().top
				}, 100);
			},
			error: function(){
				unloading();
			}
		});
	}

	//function to get the products
	function get_products(e) {
		loading();
		var retailer_name = e.dataPoint.name;
		$.ajax({
			url : 'get_products_view/'+retailer_name,

			method : 'POST',
			dataType: 'json',
			success : function(data)
			{
				unloading();
				if(typeof rev_store_tb_obj == 'object'){
					rev_store_tb_obj.fnDestroy();
				}
				$("#products_listing tbody").html(data);
				rev_store_tb_obj = $("#products_listing").dataTable({
					"iDisplayLength":100,
					"lengthMenu": [ 25, 50, 75, 100 ],
					fnDrawCallback: function( oSettings ) {
						var elm = $('select[name=products_listing_length]').parent().parent();
						$('select[name=products_listing_length]').parent().remove();
						elm.html('<div class="form-group" style="margin-bottom: 5px;"><label>Search</label><input type="text" id="retailer_product_search" class="form-control"></div>');
					}
				});
				//				$("#products_listing tbody").html(data);
				$('#retailer_name').text(retailer_name);

				$('html, body').animate({
					scrollTop:$('#products_details').position().top
				}, 'slow');
				$("#products_details").removeClass('hide');
				$('html, body').animate({
					scrollTop: $("#products_details").offset().top
				}, 100);
			},
			error: function(){
				unloading();
			}
		});
	}



});

// Function to get the Store Products Counts
function getStoreProductsCounts()
{
    loading_store_products();
    // Here we have to show loader which says Calculating...
    $.ajax({
            url: base_url+'admin/home/get_store_products_counts',
            type: 'POST',
            dataType: 'json',
            data: {},
            success: function(data){
                unloading_store_products();
                if(data.result == 1){
                   $('#store_products_count').html(data.message.store_products_counts);
                }
            },
            error: function(){
                unloading_store_products();
            }
    });
}

function initTimepicker(){
	if($('.timepicker').length > 0){
		$('.timepicker').timepicker({
			defaultTime: false
		});
	}
	
}
$('body').on('click','#copy_time',function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	var cnt = 1;
	var from_time = '';
	var to_time = '';
	var err = 0;
	if($('input[name="open_hours[]"]').length > 0){
		$('input[name="open_hours[]"]').each(function(){
			var elm = $(this);
			if(cnt == 1){
				from_time = elm.val();
				if(from_time == '' || typeof from_time == 'undefined'){
					alert('Please set from time for monday');
					err++;
					return false;
				}
			}
			else if(cnt == 2){
				to_time = elm.val();
				if(to_time == '' || typeof to_time == 'undefined'){
					alert('Please set to time for monday');
					err++;
					return false;
				}
			}
			else{
				if(cnt % 2 !== 0){
					elm.val(from_time);
				}
				else{
					elm.val(to_time);
				}
			}
			cnt ++;
		});
	}
	var cnt = 1;
	var checked = false;
	if(err == 0){
		$('input[name="open_days[]"]').each(function(){
			var elm = $(this);
			if(cnt == 1){
				checked = elm.prop('checked');
			}
			else{
				elm.prop('checked', checked);
			}
			cnt ++;
		});
	}
});
$('body').on('input', '#state_user_search', function(){
	user_tb_obj.fnFilter( $(this).val() );
});
$('body').on('input', '#retailer_user_search', function(){
	ret_user_tb_obj.fnFilter( $(this).val() );
});
$('body').on('input', '#ret_store_search', function(){
	ret_store_tb_obj.fnFilter( $(this).val() );
});
$('body').on('input', '#retailer_product_search', function(){
	rev_store_tb_obj.fnFilter( $(this).val() );
});
$('body').on('input', '#ret_special_search', function(){
	rev_special_tb_obj.fnFilter( $(this).val() );
});
$('body').on('input', '#ret_con_ret_search', function(){
	rev_con_ret_tb_obj.fnFilter( $(this).val());
});
$('body').on('input', '#ret_region_search', function(){
	rev_region_tb_obj.fnFilter( $(this).val());
});
$('body').on('input', '#ret_gender_search', function(){
	rev_gender_tb_obj.fnFilter( $(this).val());
});
$('body').on('input', '#ret_device_search', function(){
	rev_device_tb_obj.fnFilter( $(this).val());
});
$('body').on('input', '#ret_cat_sub_search', function(){
	rev_cat_cub_tb_obj.fnFilter( $(this).val());
});
$('body').on('input', '#ret_stst_search', function(){
	rev_ret_stst_tb_obj.fnFilter( $(this).val());
});
$('body').on('change', '#filt_store_type', function(){
	ret_store_tb_obj.fnFilter( $(this).val(),3 );
});
$('body').on('change', '#filt_retailer', function(){
	ret_store_tb_obj.fnFilter( $(this).val(),2 );
	$('#filt_store_type').html('<option value="">Select Store Type</option>');
	$('#filt_store_type').select2('');
	if($(this).val() != '' && $(this).val() != 'undefined'){
		loading();
		$.ajax({
			url: base_url+'admin/home/get_store_types',
			type: 'POST',
			dataType: 'json',
			data: {
				retailer: $(this).val()
			},
			success: function(data){
				unloading();
				if(data.result == 1){
					$('#filt_store_type').html(data.message);
					$('#filt_store_type').select2('');
				}
			},
			error: function(){
				unloading();
			}
		});
	}
		
});


function retailer_details_show(){
	$('#back_btn_dash').hide();
	loading();
	$('#main_expansion').html('');
	$('#main_details_expansion').html('');
	$.ajax({
		url: base_url+'admin/home/get_retailer_count_expansion',
		type: 'POST',
		dataType: 'json',
		data: {},
		success: function(data){
			unloading();
			if(data.result == 1){
				$.each(data.message, function(index1, element1) {
					var index_arr = index1.split('::');
					var html_id = 'retailer_expansion_'+index_arr[0];
					var chart_id = 'retailer_chart_'+index_arr[0];
					$('#main_expansion').append('<div class="col-sm-3"><span class="t_sm_count"></span><div class="text-center retailer_expansion_donut" id="'+html_id+'" style="height: 200px;padding-left: 0px;"></div></div>');
					bc_rows = ['Retailers',];
					chart_create(chart_id,html_id, index_arr[1], element1,get_store_special_count,bc_rows,1,'retailer_expansion_','ret_count_full');
				}); 
			}
		},
		error: function(){
			unloading();
		}
	});
}

//function retailer_details_show(){
//	loading();
//	$('.retailer_expansion_donut').hide();
//	$('.stores_expansion_donut').hide();
//	$.ajax({
//		url: base_url+'admin/home/get_user_count_expansion',
//		type: 'POST',
//		dataType: 'json',
//		data: {},
//		success: function(data){
//			unloading();
//			if(data.result == 1){
//				$('.retailer_expansion_donut').show();
//				var users_count_exp_store_chart = new CanvasJS.Chart("ret_exp_store",
//				{
//					title: {
//						text: "Stores"
//					},
//					backgroundColor: "transparent",
//					data: [
//					{
//						type: "doughnut",
//						responsive:true,
//						showInLegend: false,
//						indexLabel: "{name}: {y}",
//						cursor:"pointer",
//						radius:  "70%", 
//						indexLabelFontSize: 11,
//						dataPoints: data.message.retailer_store_count,
//						click: get_storetype_store_count
//					}
//					],
//					colorSet:  "expandColor",
//					toolTip:{
//						enabled: true
//					}
//				});
//				users_count_exp_store_chart.options.data[0].click = function(e){
//					//alert(e.dataPointIndex);
//					var dataSeries = e.dataSeries;
//					var dataPoint = e.dataPoint;
//					var dataPointIndex = e.dataPointIndex;
//    
//					for(var i = 0; i < dataSeries.dataPoints.length; i++){
//						if(i === dataPointIndex){
//							dataSeries.dataPoints[i].exploded = true;            
//						}else        
//							dataSeries.dataPoints[i].exploded = false;            
//					}
//    
//					users_count_exp_store_chart.render();
//					get_storetype_store_count(e);
//				};
//	
//				users_count_exp_store_chart.render();
//				
//				
//				var users_count_exp_consumer_chart = new CanvasJS.Chart("ret_exp_consumer",
//				{
//					title: {
//						text: "Consumers"
//					},
//					backgroundColor: "transparent",
//					data: [
//					{
//						type: "doughnut",
//						responsive:true,
//						showInLegend: false,
//						indexLabel: "{name}: {y}",
//						cursor:"pointer",
//						radius:  "70%", 
//						indexLabelFontSize: 11,
//						dataPoints: data.message.retailer_consumer_count
//					}
//					],
//					colorSet:  "expandColor",
//					toolTip:{
//						enabled: true
//					}
//				});
//				
//				users_count_exp_consumer_chart.options.data[0].click = function(e){
//					//alert(e.dataPointIndex);
//					var dataSeries = e.dataSeries;
//					var dataPoint = e.dataPoint;
//					var dataPointIndex = e.dataPointIndex;
//    
//					for(var i = 0; i < dataSeries.dataPoints.length; i++){
//						if(i === dataPointIndex){
//							dataSeries.dataPoints[i].exploded = true;            
//						}else        
//							dataSeries.dataPoints[i].exploded = false;            
//					}
//    
//					users_count_exp_consumer_chart.render();
//				};
//	
//				users_count_exp_consumer_chart.render();
//				
//				var users_count_exp_admin_chart = new CanvasJS.Chart("ret_exp_admin",
//				{
//					title: {
//						text: "Admins"
//					},
//					backgroundColor: "transparent",
//					data: [
//					{
//						type: "doughnut",
//						responsive:true,
//						showInLegend: false,
//						indexLabel: "{name}: {y}",
//						cursor:"pointer",
//						radius:  "70%", 
//						indexLabelFontSize: 11,
//						dataPoints: data.message.retailer_admin_count
//					}
//					],
//					colorSet:  "expandColor",
//					toolTip:{
//						enabled: true
//					}
//				});
//				users_count_exp_admin_chart.options.data[0].click = function(e){
//					//alert(e.dataPointIndex);
//					var dataSeries = e.dataSeries;
//					var dataPoint = e.dataPoint;
//					var dataPointIndex = e.dataPointIndex;
//    
//					for(var i = 0; i < dataSeries.dataPoints.length; i++){
//						if(i === dataPointIndex){
//							dataSeries.dataPoints[i].exploded = true;            
//						}else        
//							dataSeries.dataPoints[i].exploded = false;            
//					}
//    
//					users_count_exp_admin_chart.render();
//				};
//	
//				users_count_exp_admin_chart.render();
//				
//				var users_count_exp_product_chart = new CanvasJS.Chart("ret_exp_product",
//				{
//					title: {
//						text: "Products"
//					},
//					backgroundColor: "transparent",
//					data: [
//					{
//						type: "doughnut",
//						responsive:true,
//						showInLegend: false,
//						indexLabel: "{name}: {y}",
//						cursor:"pointer",
//						radius:  "70%", 
//						indexLabelFontSize: 11,
//						dataPoints: data.message.retailer_product_count
//					}
//					],
//					colorSet:  "expandColor",
//					toolTip:{
//						enabled: true
//					}
//				});
//				users_count_exp_product_chart.options.data[0].click = function(e){
//					//alert(e.dataPointIndex);
//					var dataSeries = e.dataSeries;
//					var dataPoint = e.dataPoint;
//					var dataPointIndex = e.dataPointIndex;
//    
//					for(var i = 0; i < dataSeries.dataPoints.length; i++){
//						if(i === dataPointIndex){
//							dataSeries.dataPoints[i].exploded = true;            
//						}else        
//							dataSeries.dataPoints[i].exploded = false;            
//					}
//    
//					users_count_exp_product_chart.render();
//				};
//	
//				users_count_exp_product_chart.render();
//			}
//		},
//		error: function(){
//			unloading();
//		}
//	});
//}
function stores_details_show(){
	$('#back_btn_dash').hide();
	$('#main_expansion').html('');
	$('#main_details_expansion').html('');
	loading();
	$.ajax({
		url: base_url+'admin/home/get_store_count_expansion',
		type: 'POST',
		dataType: 'json',
		data: {},
		success: function(data){
			unloading();
			if(data.result == 1){
				$('#main_expansion').append('<div class="col-sm-4 text-center" id="store_exp_store" style="height: 270px;padding-left: 0px;"></div>');
				chart_create('stores_count_state_chart','store_exp_store', 'State Wise', data.message.store_state_count);
				
				$('#main_expansion').append('<div class="col-sm-4 text-center" id="store_exp_format" style="height: 270px;padding-left: 0px;"></div>');
				chart_create('stores_count_format_chart','store_exp_format', 'Store Format', data.message.store_format_count);
			}
		},
		error: function(){
			unloading();
		}
	});
}

function consumer_details_show(){
	loading();
	$('#back_btn_dash').hide();
	$('#main_expansion').html('');
	$('#main_details_expansion').html('');
	$.ajax({
		url: base_url+'admin/home/get_consumers_count_expansion',
		type: 'POST',
		dataType: 'json',
		data: {},
		success: function(data){
			unloading();
			if(data.result == 1){
				$('#main_expansion').append('<div class="col-sm-4 text-center" id="consumer_exp_count" style="height: 270px;padding-left: 0px;"></div>');
				bc_rows = ['Consumers','Retailer'];
				chart_create('consumers_count_chart','consumer_exp_count', 'Consumer Retailer', data.message.users_count, consumer_retailer_expand);
				
				$('#main_expansion').append('<div class="col-sm-4 text-center" id="consumer_exp_region_count" style="height: 270px;padding-left: 0px;"></div>');
				bc_rows = ['Consumers','Region'];
				chart_create('consumers_region_count_chart','consumer_exp_region_count', 'Consumer Region', data.message.region_users_count, consumer_region_expand);
				
				$('#main_expansion').append('<div class="col-sm-4 text-center" id="consumer_exp_gender_count" style="height: 270px;padding-left: 0px;"></div>');
				bc_rows = ['Consumers','Gender'];
				chart_create('consumers_gender_count_chart','consumer_exp_gender_count', 'Consumer Gender', data.message.gender_users_count, consumer_gender_expand);
				
				$('#main_expansion').append('<div class="col-sm-4 text-center" id="consumer_exp_device_count" style="height: 270px;padding-left: 0px;"></div>');
				bc_rows = ['Consumers','Device'];
				chart_create('consumers_device_count_chart','consumer_exp_device_count', 'Consumer Device', data.message.device_users_count, consumer_device_expand);
			}
			bc_rows = ['Consumers'];
			place_bc(bc_rows);
		},
		error: function(){
			unloading();
		}
	});
}
function users_details_show(){
	$('#back_btn_dash').hide();
	$('#main_expansion').html('');
	$('#main_details_expansion').html('');
	$.ajax({
		url: base_url+'admin/home/get_users_count_expansion',
		type: 'POST',
		dataType: 'json',
		data: {},
		success: function(data){
		//			unloading();
		//			if(data.result == 1){
		//				$('#main_expansion').append('<div class="col-sm-4 text-center" id="store_exp_store" style="height: 270px;padding-left: 0px;"></div>');
		//				chart_create('stores_count_state_chart','store_exp_store', 'State Wise', data.message.store_state_count);
		//				
		//				$('#main_expansion').append('<div class="col-sm-4 text-center" id="store_exp_format" style="height: 270px;padding-left: 0px;"></div>');
		//				chart_create('stores_count_format_chart','store_exp_format', 'Store Format', data.message.store_format_count);
		//			}
		},
		error: function(){
			unloading();
		}
	});
}
function products_details_show(){
	loading();
	$('#back_btn_dash').hide();
	$('#main_expansion').html('');
	$('#main_details_expansion').html('');
	$.ajax({
		url: base_url+'admin/home/get_products_count_expansion',
		type: 'POST',
		dataType: 'json',
		data: {},
		success: function(data){
			unloading();
			if(data.result == 1){
				$.each(data.message, function(index1, element1) {
					var index_arr = index1.split('::');
					var html_id = 'category_expansion_'+index_arr[0];
					var chart_id = 'category_chart_'+index_arr[0];
					$('#main_expansion').append('<div class="col-sm-3"><span class="t_sm_count"></span><div class=" text-center product_expansion_donut" id="'+html_id+'" style="height: 200px;padding-left: 0px;"></div></div>');
					chart_create(chart_id,html_id, index_arr[1], element1,get_product_last_table,'',1,'category_expansion_','cat_exp_all');
				}); 
				//				$('#main_expansion').append('<div class="col-sm-4 text-center" id="product_exp_count" style="height: 250px;padding-left: 0px;"></div>');
				//				chart_create('products_count_chart','product_exp_count', 'Product Category', data.message.prduct_count, product_category_expand);
				bc_rows = ['Products'];
				place_bc(bc_rows);
			}
		},
		error: function(){
			unloading();
		}
	});
}
function categories_details_show(){
	loading();
	$('#back_btn_dash').hide();
	$('#main_expansion').html('');
	$('#main_details_expansion').html('');
	$.ajax({
		url: base_url+'admin/home/get_products_count_expansion',
		type: 'POST',
		dataType: 'json',
		data: {},
		success: function(data){
			unloading();
			if(data.result == 1){
				$('#main_expansion').append('<div class="col-sm-4 text-center" id="product_exp_count" style="height: 250px;padding-left: 0px;"></div>');
				chart_create('products_count_chart','product_exp_count', 'Main Category wise', data.message.prduct_count);
			}
		},
		error: function(){
			unloading();
		}
	});
}


function get_storetype_store_count(e){
	loading();
	$.ajax({
		url: base_url+'admin/home/get_storetype_store_count',
		type: 'POST',
		dataType: 'json',
		data: {
			retailer_id: e.dataPoint.id
		},
		success: function(data){
			unloading();
		},
		error: function(){
			unloading();
		}
	});
}

function get_store_special_count(e){
	$('#main_details_expansion').html('');
	loading();
	$.ajax({
		url: base_url+'admin/home/get_store_special_count',
		type: 'POST',
		dataType: 'json',
		data: {
			retailer: e.dataPoint.retailer,
			store_type: e.dataPoint.id
		},
		success: function(data){
			unloading();
			if(data.result == 1){
				$('#main_expansion').html('');
				$('#back_btn_dash').attr('data-type','retailer_details_show');
				$('#back_btn_dash').show();
				$('#main_expansion').append('<div class="col-sm-4 text-center" id="store_exp_specials" style="height: 270px;padding-left: 0px;"></div>');
				bc_rows = ['Retailers',e.dataPoint.retailer_name,e.dataPoint.label,'Specials'];
				chart_create('stores_exp_special_chart','store_exp_specials', 'Specials', data.message.specials_count, get_special_count_table,bc_rows);
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

function test_click(e){
	console.log(e);
}

function chart_create(chart_id,element_id, title_text, datapoints,click_function,bc_rows,add_count,crop_id,class_name,no_label,big_graph){
	if(bc_rows){
		place_bc(bc_rows);
	}
	if(no_label == 1){
		indexlabel = "";
	}
	else{
		indexlabel = "{label}: {y}";
	}
	
	chart_id = new CanvasJS.Chart(element_id,
	{
		title: {
			text: title_text
		},
		backgroundColor: "transparent",
		data: [
		{
			type: "doughnut",
			responsive:true,
			showInLegend: false,
			indexLabel: indexlabel,
			cursor:"pointer",
			radius:  "70%", 
			indexLabelFontSize: 11,
			dataPoints: datapoints
		}
		],
		colorSet:  "expandColor",
		toolTip:{
			enabled: true
		}
	});
	if(add_count == 1){
		var count_total = 0;
		$.each(datapoints,function(index, elm){
			count_total +=  parseInt(elm.y);
		});
		if(count_total > 0){
			var retailer_id = element_id.replace(crop_id,'');
			var add_html = '<a href="javascript:void(0)" class="'+class_name+'" data-id="'+retailer_id+'" data-title="'+title_text+'">'+count_total+'</a>';
			if(big_graph == 1){
				$('#'+element_id).parent().find('.t_bg_count').html(add_html);
			}
			else{
				$('#'+element_id).parent().find('.t_sm_count').html(add_html);
			}
			
		}
	}
	chart_id.options.data[0].click = function(e){
		//alert(e.dataPointIndex);
		var dataSeries = e.dataSeries;
		var dataPoint = e.dataPoint;
		var dataPointIndex = e.dataPointIndex;
    
		for(var i = 0; i < dataSeries.dataPoints.length; i++){
			if(i === dataPointIndex){
				dataSeries.dataPoints[i].exploded = true;            
			}else        
				dataSeries.dataPoints[i].exploded = false;            
		}
		if(click_function != '' && typeof click_function != 'undefined'){
			click_function(e);
		}
		
		chart_id.render();
	};
	
	chart_id.render();
	$('html, body').animate({
		scrollTop: $("#main_expansion").offset().top
	}, 100);
}

$('body').on('click','#back_btn_dash',function(){
	function_name = eval($(this).attr('data-type'));
	function_name();
});

function get_special_count_table(e){
	$('#main_details_expansion').html('');
	loading();
	$.ajax({
		url: base_url+'admin/home/get_special_count_expansion',
		type: 'POST',
		dataType: 'json',
		data: {
			retailer: e.dataPoint.retailer,
			store_type: e.dataPoint.store_type,
			store: e.dataPoint.id
		},
		success: function(data){
			unloading();
			if(data.result == 1){
				$('#main_details_expansion').html(data.message);
				if(typeof rev_special_tb_obj == 'object'){
					rev_special_tb_obj.fnDestroy();
				}
				rev_special_tb_obj = $("#product_special_expansion_table").dataTable({
					"iDisplayLength":100,
					"lengthMenu": [ 25, 50, 75, 100 ],
					//					"aoColumnDefs": [
					//					{
					//						"targets": [2,3],
					//						"bSortable" : false,
					//						"visible": false
					//					}
					//					],
					fnDrawCallback: function( oSettings ) {
						var elm = $('select[name=product_special_expansion_table_length]').parent().parent();
						$('select[name=product_special_expansion_table_length]').parent().remove();
						elm.html('<div class="form-group" style="margin-bottom: 5px;"><label>Search</label><input type="text" id="ret_special_search" class="form-control"></div>');
					}
				});
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

function consumer_retailer_expand(e){
	loading();
	$('#main_details_expansion').html('');
	$.ajax({
		url: base_url+'admin/home/get_consumer_retailer_expansion',
		type: 'POST',
		dataType: 'json',
		data: {
			retailer: e.dataPoint.id
		},
		success: function(data){
			unloading();
			if(data.result == 1){
				$('#main_details_expansion').html(data.message);
				if(typeof rev_con_ret_tb_obj == 'object'){
					rev_con_ret_tb_obj.fnDestroy();
				}
				rev_con_ret_tb_obj = $("#consumer_retailer_table").dataTable({
					"iDisplayLength":100,
					"lengthMenu": [ 25, 50, 75, 100 ],
					fnDrawCallback: function( oSettings ) {
						var elm = $('select[name=consumer_retailer_table_length]').parent().parent();
						$('select[name=consumer_retailer_table_length]').parent().remove();
						elm.html('<div class="form-group" style="margin-bottom: 5px;"><label>Search</label><input type="text" id="ret_con_ret_search" class="form-control"></div>');
					}
				});
				$('html, body').animate({
					scrollTop: $("#main_details_expansion").offset().top
				}, 100);
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

function consumer_region_expand(e){
	loading();
	$('#main_details_expansion').html('');
	$.ajax({
		url: base_url+'admin/home/get_consumer_region_expansion',
		type: 'POST',
		dataType: 'json',
		data: {
			state: e.dataPoint.id
		},
		success: function(data){
			unloading();
			if(data.result == 1){
				$('#main_details_expansion').html(data.message);
				if(typeof rev_region_tb_obj == 'object'){
					rev_region_tb_obj.fnDestroy();
				}
				rev_region_tb_obj = $("#consumer_region_table").dataTable({
					"iDisplayLength":100,
					"lengthMenu": [ 25, 50, 75, 100 ],
					fnDrawCallback: function( oSettings ) {
						var elm = $('select[name=consumer_region_table_length]').parent().parent();
						$('select[name=consumer_region_table_length]').parent().remove();
						elm.html('<div class="form-group" style="margin-bottom: 5px;"><label>Search</label><input type="text" id="ret_region_search" class="form-control"></div>');
					}
				});
				$('html, body').animate({
					scrollTop: $("#main_details_expansion").offset().top
				}, 100);
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

function consumer_gender_expand(e){
	loading();
	$('#main_details_expansion').html('');
	$.ajax({
		url: base_url+'admin/home/get_consumer_gender_expansion',
		type: 'POST',
		dataType: 'json',
		data: {
			gender: e.dataPoint.id
		},
		success: function(data){
			unloading();
			if(data.result == 1){
				$('#main_details_expansion').html(data.message);
				if(typeof rev_gender_tb_obj == 'object'){
					rev_gender_tb_obj.fnDestroy();
				}
				rev_gender_tb_obj = $("#consumer_gender_table").dataTable({
					"iDisplayLength":100,
					"lengthMenu": [ 25, 50, 75, 100 ],
					fnDrawCallback: function( oSettings ) {
						var elm = $('select[name=consumer_gender_table_length]').parent().parent();
						$('select[name=consumer_gender_table_length]').parent().remove();
						elm.html('<div class="form-group" style="margin-bottom: 5px;"><label>Search</label><input type="text" id="ret_gender_search" class="form-control"></div>');
					}
				});
				$('html, body').animate({
					scrollTop: $("#main_details_expansion").offset().top
				}, 100);
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
function consumer_device_expand(e){
	loading();
	$('#main_details_expansion').html('');
	$.ajax({
		url: base_url+'admin/home/get_consumer_device_expansion',
		type: 'POST',
		dataType: 'json',
		data: {
			device: e.dataPoint.id
		},
		success: function(data){
			unloading();
			if(data.result == 1){
				$('#main_details_expansion').html(data.message);
				if(typeof rev_device_tb_obj == 'object'){
					rev_device_tb_obj.fnDestroy();
				}
				rev_device_tb_obj = $("#consumer_device_table").dataTable({
					"iDisplayLength":100,
					"lengthMenu": [ 25, 50, 75, 100 ],
					fnDrawCallback: function( oSettings ) {
						var elm = $('select[name=consumer_device_table_length]').parent().parent();
						$('select[name=consumer_device_table_length]').parent().remove();
						elm.html('<div class="form-group" style="margin-bottom: 5px;"><label>Search</label><input type="text" id="ret_device_search" class="form-control"></div>');
					}
				});
				$('html, body').animate({
					scrollTop: $("#main_details_expansion").offset().top
				}, 100);
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

function get_product_last_table(e){
	loading();
	$('#main_details_expansion').html('');
	$.ajax({
		url: base_url+'admin/home/get_category_sub_count_expansion',
		type: 'POST',
		dataType: 'json',
		data: {
			main_cat: e.dataPoint.main_cat,
			parent_cat: e.dataPoint.id
		},
		success: function(data){
			unloading();
			if(data.result == 1){
				$('#main_details_expansion').html(data.message);
				if(typeof rev_cat_sub_tb_obj == 'object'){
					rev_cat_sub_tb_obj.fnDestroy();
				}
				rev_cat_sub_tb_obj = $("#cat_sub_table").dataTable({
					"iDisplayLength":100,
					"lengthMenu": [ 25, 50, 75, 100 ],
					fnDrawCallback: function( oSettings ) {
						var elm = $('select[name=cat_sub_table_length]').parent().parent();
						$('select[name=cat_sub_table_length]').parent().remove();
						elm.html('<div class="form-group" style="margin-bottom: 5px;"><label>Search</label><input type="text" id="ret_cat_sub_search" class="form-control"></div>');
					}
				});
				$('html, body').animate({
					scrollTop: $("#main_details_expansion").offset().top
				}, 100);
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

function product_category_expand(e){
	console.log(e);
}

function place_bc(values){
	$('#dash_bc ul').html('');
	$.each(values, function(index, element){
		$('#dash_bc ul').append('<li>'+element+'</li>');
	});
}
$('body').on('click','.ret_count_full',function(){
	loading();
	var elm = $(this);
	var id = elm.attr('data-id');
	var title = elm.attr('data-title');
	$('#main_details_expansion').html('');
	$.ajax({
		url: base_url+'admin/home/get_retailer_each_exapansion',
		type: 'POST',
		dataType: 'json',
		data: {
			retailer: id
		},
		success: function(data){
			unloading();
			if(data.result == 1){
				$('#main_expansion').html('');
				$('#back_btn_dash').attr('data-type','retailer_details_show');
				$('#back_btn_dash').show();
				$.each(data.message.storetypes, function(index, element){
					var index_arr = index.split('::');
					$('#main_expansion').append('<div class="col-sm-3"><span class="t_sm_count"></span><div class="text-center" id="retailer_storetype_graph_'+index_arr[0]+'" style="height: 250px;padding-left: 0px;"></div>');
					bc_rows = ['Retailers',title];
					chart_create('retailer_exp_storetype_chart','retailer_storetype_graph_'+index_arr[0], index_arr[1], element, get_retailer_storetype_count_table,bc_rows,1,'retailer_storetype_graph_','retailer_st_str_list',1);
				});
				
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

function get_retailer_storetype_count_table(e){
	console.log(e);
	show_retailer_storetype_count_table(e.dataPoint.storetype);
}

$('body').on('click', '.retailer_st_str_list', function(){
	var elm = $(this);
	var storetype_id = elm.attr('data-id');
	show_retailer_storetype_count_table(storetype_id);
});

function show_retailer_storetype_count_table(storetype_id){
	loading();
	$.ajax({
		url: base_url+'admin/home/show_retailer_storetype_count_table',
		type: 'POST',
		dataType: 'json',
		data: {
			store_type: storetype_id
		},
		success: function(data){
			unloading();
			if(data.result == 1){
				$('#main_details_expansion').html(data.message);
				if(typeof rev_ret_stst_tb_obj == 'object'){
					rev_ret_stst_tb_obj.fnDestroy();
				}
				rev_ret_stst_tb_obj = $("#retailer_stst_table").dataTable({
					"iDisplayLength":100,
					"lengthMenu": [ 25, 50, 75, 100 ],
					fnDrawCallback: function( oSettings ) {
						var elm = $('select[name=retailer_stst_table_length]').parent().parent();
						$('select[name=retailer_stst_table_length]').parent().remove();
						elm.html('<div class="form-group" style="margin-bottom: 5px;"><label>Search</label><input type="text" id="ret_stst_search" class="form-control"></div>');
					}
				});
				$('html, body').animate({
					scrollTop: $("#main_details_expansion").offset().top
				}, 100);
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

$('body').on('click','.store_exp_modal',function(e){
	loading();
	e.preventDefault();
	e.stopImmediatePropagation();
	var elm = $(this);
	var store_id=elm.attr('data-id');
	var store_name = elm.text();
	$.ajax({
		url: base_url+'admin/home/get_store_modal_graphs',
		type: 'POST',
		dataType: 'json',
		data: {
			store: store_id
		},
		success: function(data){
			unloading();
			if(data.result == 1){
				var html = '<div class="row"><div class="col-sm-3"><span class="t_sm_count"></span><div class="text-center" id="store_det_gr_csmr" style="height: 200px;padding-left: 0px;"></div></div>\n\
<div class="col-sm-3"><span class="t_sm_count"></span><div class="text-center" id="store_det_gr_user" style="height: 200px;padding-left: 0px;"></div></div>\n\
<div class="col-sm-3"><span class="t_sm_count"></span><div class="text-center" id="store_det_gr_product" style="height: 200px;padding-left: 0px;"></div></div>\n\
<div class="col-sm-3"><span class="t_sm_count"></span><div class="text-center" id="store_det_gr_special" style="height: 200px;padding-left: 0px;"></div></div></div>';
				createModal('store-graph-modal', store_name, html,'wd-75');
				setTimeout(function(){
					chart_create('store_det_consumer_chart','store_det_gr_csmr', 'Consumers', data.message.consumer_count, '','',1,'','',1);
					chart_create('store_det_user_chart','store_det_gr_user', 'Users', data.message.user_count, '','',1,'','',1);
					chart_create('store_det_product_chart','store_det_gr_product', 'Products', data.message.product_count, '','',1,'','',1);
					chart_create('store_det_special_chart','store_det_gr_special', 'Specials', data.message.special_count, '','',1,'','',1);
				},200);
			}
		},
		error: function(){
			unloading();
		}
	});
});

