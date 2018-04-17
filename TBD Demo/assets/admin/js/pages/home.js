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
var baseUrl=$('#baseUrl').val();
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
					"lengthMenu": [ 25, 50, 75, 100,125 ],
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
				  // specialPercent=((data.message.special_product_count/data.message.store_products_counts)*100);
				//	$('#specialproducts').attr('data-percent',data.message.special_product_count);
				//	$('#specialproducts').attr('data-text',data.message.special_product_count);
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
                            
                            var itemsArray = [];
                            
                            $.each(data.message, function(index1, element1) {
					var index_arr = index1.split('::');
					var html_id = 'retailer_expansion_'+index_arr[0];
					var chart_id = 'retailer_chart_'+index_arr[0];
					
                                        var retId = index_arr[0];
                                        var retName = index_arr[1];
                                        //alert(html_id + " >>>> " + chart_id + " >>>> " + retName);
                                        
                                        var count_total = parseInt(0);
                                        var retailerLogo = "";
                                        $.each(element1,function(index, elm){
                                            count_total +=  parseInt(elm.y);
                                            retailerLogo = elm.retailer_logo_image;
                                            
                                            //alert("retailerLogo : " + elm.retailer_logo_image);
                                        });
                                        
                                        //alert("retailerLogo : " + retailerLogo);
                                        
                                        itemsArray.push({
                                            retailerId   : retId,
                                            retailerName : retName,
                                            retailerLogo : retailerLogo,
                                            storeCount   : count_total
                                        });
				});
                                
                                showRetailersStores(itemsArray);
                                
    
                                
                                /*
				$.each(data.message, function(index1, element1) {
					var index_arr = index1.split('::');
					var html_id = 'retailer_expansion_'+index_arr[0];
					var chart_id = 'retailer_chart_'+index_arr[0];
					$('#main_expansion').append('<div class="col-sm-3"><span class="t_sm_count"></span><div class="text-center retailer_expansion_donut" id="'+html_id+'" style="height: 200px;padding-left: 0px;"></div></div>');
					bc_rows = ['Retailers',];
					chart_create(chart_id,html_id, index_arr[1], element1,get_store_special_count,bc_rows,1,'retailer_expansion_','ret_count_full');
				});
                                */
			}
		},
		error: function(){
			unloading();
		}
	});
}


function showRetailersStores_old(itemsArray)
{
   var headingHtml = "";
   headingHtml ="<ul><li>Retailers</li></ul>";
   $('#dash_bc').html(headingHtml);
   
   var showHtml = "";
   showHtml += '<div class="col-xs-12" style="margin-top: 5px !important;">';
   
   $.each(itemsArray, function (i, v) {   
         showHtml +='<div class="col-md-3 col-sm-6 col-xs-12" style="padding-left: 0px !important;">'
         showHtml +='<div class="info-box">';
         //showHtml +='<div class="info-box-text" style="font-weight: bold; margin-bottom: -2px; text-align: center;">'+v.retailerName+'</div>';
         showHtml +='<div class="info-box-text123"><a href="javascript:void(0)" class="ret_count_full" data-id="'+v.retailerId+'" data-title="'+v.retailerName+'"><img src="'+v.retailerLogo+'" alt="'+v.retailerName+'" title="'+v.retailerName+'" height="50" width="228"></a></div>';
         showHtml +='<div class="info-box-content"> <span class="info-box-number">'+v.storeCount+'</span></div>';
         showHtml +='</div></div>';
   });
                                
   showHtml +='</div>';
   $('#main_expansion').html(showHtml);
}


function showRetailersStores(itemsArray)
{
   var headingHtml = "";
   headingHtml ="<ul><li>Retailers</li></ul>";
   $('#dash_bc').html(headingHtml);
   
   var showHtml = "";
   showHtml += '<div class="col-xs-12" style="margin-top: 5px !important;">';
   
   $.each(itemsArray, function (i, v) {   
      showHtml +='<div class="col-md-3" style="padding-left: 0px !important;">' 
      showHtml +='<div class="box box-danger123">';
      //showHtml +='<div class="box-header with-border">';
      //<h3 class="box-title">'+v.retailerName+'</h3>';
      //showHtml +='<div class="box-tools pull-right"><a href="javascript:void(0)" class="ret_count_full" data-id="'+v.retailerId+'" data-title="'+v.retailerName+'"><span class="info-box-number">'+v.storeCount+'</span></a></div></div>';                  
      showHtml +='<div class="box-body chart-responsive">';
      showHtml +='<div style="height: 100px;position: relative;">';
      
      //showHtml +='<div class="info-box-content"> <span class="info-box-number" style="font-family: Source Sans Pro !important;font-size: 40px">'+v.storeCount+'</span></div>';
      if(v.retailerLogo)
      {
         showHtml +='<div class="info-box-text123" style="height:30px"><a href="javascript:void(0)" class="ret_count_full" data-id="'+v.retailerId+'" data-title="'+v.retailerName+'"><img src="'+v.retailerLogo+'" width="50%"></a></div>';
      }else{
         showHtml +='<div class="info-box-text123" style="height:30px"></div>'; 
      } 
      
      showHtml +='<div class="info-box-content"> <a href="javascript:void(0)" class="ret_count_full" style="color: #3E3733" data-id="'+v.retailerId+'" data-title="'+v.retailerName+'"><span class="info-box-number" style="font-family: Source Sans Pro !important;font-size: 40px">'+v.storeCount+'</span></a></div>';
      //showHtml +='<div class="info-box-content"> <h1 class="box-title">'+v.storeCount+'</h1></div>';
      showHtml +='</div></div>';
      showHtml +='</div></div>';
   });
   

   showHtml +='</div>';
   $('#main_expansion').html(showHtml);
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
				
                                /*
                                $.each(data.message.storetypes, function(index, element){
					var index_arr = index.split('::');
					$('#main_expansion').append('<div class="col-sm-3"><span class="t_sm_count"></span><div class="text-center" id="retailer_storetype_graph_'+index_arr[0]+'" style="height: 250px;padding-left: 0px;"></div>');
					bc_rows = ['Retailers',title];
					chart_create('retailer_exp_storetype_chart','retailer_storetype_graph_'+index_arr[0], index_arr[1], element, get_retailer_storetype_count_table,bc_rows,1,'retailer_storetype_graph_','retailer_st_str_list',1);
				});
                                */
                               
                               // New coding stared
                               var storeTypesArray = [];
                               $.each(data.message.storetypes, function(index, element){
                                    $.each(element,function(ind, elm){
                                        storeTypesArray.push({
                                            retailerId   : elm.retailer,
                                            storeTypeId   : elm.storetype,
                                            storeTypeName : elm.name,                                                    
                                            storeCount   : elm.y
                                        });
                                    });
				});
                                
                                showStoreFormats(storeTypesArray,title);
                                
                                // New coding stops 
                                
                                 
                                
				
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

// Show Store formats
function showStoreFormats(itemsArray, retailerName)
{
   var headingHtml = "";
   headingHtml ="<ul><li>Retailers > "+ retailerName +"</li></ul>";
   $('#dash_bc').html(headingHtml);
   
   var showHtml = "";
   showHtml += '<div class="col-xs-12" style="margin-top: 5px !important;">';
   
   $.each(itemsArray, function (i, v) {   
         showHtml +='<div class="col-md-3 col-sm-6 col-xs-12" style="padding-left: 0px !important;">'
         showHtml +='<div class="box box-danger123">';
         showHtml +='<div class="box-header with-border" style="text-align:center"><h3 class="box-title">'+v.storeTypeName+'</h3></div>';
         showHtml +='<div style="height: 60px;position: relative;">';         
         showHtml +='<div class="info-box-content" style="margin-bottom:15px;"> <a href="javascript:void(0)" style="color: #3E3733; font-family: Source Sans Pro !important;font-size: 40px" class="retailer_st_str_list" data-id="'+v.storeTypeId+'" data-title="'+v.storeTypeName+'"><span class="info-box-number">'+v.storeCount+'</span></a></div>';
         showHtml +='</div></div></div>';
   });
                                
   showHtml +='</div>';
   $('#main_expansion').html(showHtml);
}


function showStoreFormats_old(itemsArray, retailerName)
{
   var headingHtml = "";
   headingHtml ="<ul><li>Retailers > "+ retailerName +"</li></ul>";
   $('#dash_bc').html(headingHtml);
   
   var showHtml = "";
   showHtml += '<div class="col-xs-12" style="margin-top: 5px !important;">';
   
   $.each(itemsArray, function (i, v) {   
         showHtml +='<div class="col-md-3 col-sm-6 col-xs-12" style="padding-left: 0px !important;">'
         showHtml +='<div class="info-box">';
         showHtml +='<div class="info-box-text" style="font-weight: bold; margin-top:15px;margin-bottom: -2px; text-align: center;">'+v.storeTypeName+'</div>';
         showHtml +='<div class="info-box-content" style="margin-bottom:15px;"> <a href="javascript:void(0)" class="retailer_st_str_list" data-id="'+v.storeTypeId+'" data-title="'+v.storeTypeName+'"><span class="info-box-number">'+v.storeCount+'</span></a></div>';
         showHtml +='</div></div>';
   });
                                
   showHtml +='</div>';
   $('#main_expansion').html(showHtml);
}

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
                                
                                //alert("Success");
				setTimeout(function(){
                                    
                                        /*
					chart_create('store_det_consumer_chart','store_det_gr_csmr', 'Consumers', data.message.consumer_count, '','',1,'','',1);
					chart_create('store_det_user_chart','store_det_gr_user', 'Users', data.message.user_count, '','',1,'','',1);
					chart_create('store_det_product_chart','store_det_gr_product', 'Products', data.message.product_count, '','',1,'','',1);
					chart_create('store_det_special_chart','store_det_gr_special', 'Specials', data.message.special_count, '','',1,'','',1);
                                        */
                                       
				},200);
			}
		},
		error: function(){
			unloading();
		}
	});
});

$('#store_listing').on('click','.showpromodetails',function(e){
	showpromodetails(e,$(this).attr('data-href'));
});
function showpromodetails(e,url)
{
		loading();
		e.preventDefault();
		e.stopImmediatePropagation();
		//var url = $(this).attr('data-href');
		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			data: {},
			success: function(data){
				unloading();
				createModal('show-promo-list', 'All Specials By - ' + data.storeName,data.result_specials_data,'wd-75');			
				$("#specialListing").dataTable({
						"pageLength": 50,
						paging: true,
						bFilter: true,
						ordering: true,
						searching: true,
					
					});
				
			}
		});
}


function showStoredetails(e,url)
{
		loading();
		e.preventDefault();
		e.stopImmediatePropagation();
		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			data: {},
			success: function(data){
				unloading();
				createModal('show-store-list', 'All Stores By - ' + data.StoreType,data.html,'wd-75');			
					$('#storeListing').on('click','.showpromodetails',function(e){
						showpromodetails(e,$(this).attr('data-href'));
					});
					
					$("#storeListing").dataTable({
						"pageLength": 50,
						paging: true,
						bFilter: true,
						ordering: true,
						searching: true,
						initComplete: function () {
							$('<div class="form-group filterstorebyproviencediv"></div>').appendTo($("#storeListing_length"))
							this.api().columns().every(function () {
								var column = this;

								if (column.index() == 1) {       
								var select = $('<select class="form-control getproductByCategory"><option value="">Select Province</option>'+data.provinceHtml+'</select>')
									.appendTo($(".filterstorebyproviencediv"))
									.on('change', function () {
									var val = $.fn.dataTable.util.escapeRegex(
									$(this).val());                                     

									column.search(val ? '^' + val + '$' : '', true, false)
										.draw();
								});
								
							}
							});
						}
					});
								
			}
		});
}

$( ".storeFormatsBlock" ).click(function(e) {
	loading();
	$('.report_header').html('Store Formats');	
			
	showAllStoreFormats();
});
function showAllStoreFormats()
{
	
	$('.htmltochange').html('<table id="storeFormatListing" class="table table-bordered dataTables dataTable no-footer">'+
			'<thead>'+
				'<tr role="row">'+
					'<th>Store Format</th>'+
					'<th>Retailer</th>'+
					'<th>Stores</th>'+					
				'</tr>'+
			'</thead>'+
			'<tbody>'+

			'</tbody>'+
		'</table>');

	$.ajax({
			url: baseUrl+'home/getStoreFormatsOfUserType',
			type: 'POST',
			dataType: 'json',
			data: {},
			success: function(data){
				unloading();
				
				$('#storeFormatListing tbody').html(data.html);
								
				$( ".getStores" ).click(function(e) {
					loading();				
					showStoredetails(e,$(this).attr('data-href'));
					
				});
				
				$("#storeFormatListing").dataTable({
					"pageLength": 50,
					paging: true,
					bFilter: true,
					ordering: true,
					searching: true,
					initComplete: function () {
						$('<div class="form-group filterstorebyretailerdiv"></div>').appendTo($("#storeFormatListing_length"))
						this.api().columns().every(function () {
							var column = this;

							if (column.index() == 1) {       
							var select = $('<select class="form-control getstoreformatbyretailer"><option value="">Select Retailer</option>'+data.retailerHtml+'</select>')
								.appendTo($(".filterstorebyretailerdiv"))
								.on('change', function () {
								var val = $.fn.dataTable.util.escapeRegex(
								$(this).val());                                     

								column.search(val ? '^' + val + '$' : '', true, false)
									.draw();
							});
							
						}
						});
					}
				});			
				
			}
		});
}

$( ".storeFormatCount" ).click(function(e) {
	loading();
	$('.report_header').html('Store Formats');	
			
	showStoreFormats();
});
showStoreFormats();
function showStoreFormats()
{

	$.ajax({
			url: baseUrl+'home/getStoreFormatsOfUserType',
			type: 'POST',
			dataType: 'json',
			data: {},
			success: function(data){
				unloading();
				
				$('.storeformatsdiv').html(data.html);
				$( ".storeFormatName" ).click(function(e) {
					loading();
					$('.report_header').html('Stores In '+$(this).html());
					$('#store_listing').remove();
					$('.htmltochange').html('<table id="store_listing" class="table table-bordered dataTables dataTable no-footer">'+
										'<thead>'+
											'<tr role="row">'+
												'<th>Store Name</th>'+
												'<th>Province</th>'+
												'<th width="10%">Products</th>'+
												'<th  width="10%">Specials</th>'+
												'<th  width="10%">Admins</th>'+
												'<th  width="10%">Users</th>'+
												
											'</tr>'+
										'</thead>'+
										'<tbody>'+

										'</tbody>'+
									'</table>');
					
					e.preventDefault();
					e.stopImmediatePropagation();
					var url = $(this).attr('data-href');
					$.ajax({
						url: url,
						type: 'POST',
						dataType: 'json',
						data: {},
						success: function(data){
							unloading();
							
							$("#storeFormat").html(data.StoreType);
							$("#store_listing tbody").html(data.html);
							$("#store_details").removeClass('hide');	
								
								$('#store_listing').on('click','.showpromodetails',function(e){
									showpromodetails(e,$(this).attr('data-href'));
								});
							$("#store_listing").dataTable({
									"pageLength": 50,
									paging: true,
									bFilter: true,
									ordering: true,
									searching: true,
									
									initComplete: function () {
										$('<div class="form-group filterstorebyproviencediv"></div>').appendTo($("#store_listing_length"))
										this.api().columns().every(function () {
											var column = this;

											if (column.index() == 1) {       
											var select = $('<select class="form-control getproductByCategory"><option value="">Select Province</option>'+data.provinceHtml+'</select>')
												.appendTo($(".filterstorebyproviencediv"))
												.on('change', function () {
												var val = $.fn.dataTable.util.escapeRegex(
												$(this).val());                                     

												column.search(val ? '^' + val + '$' : '', true, false)
													.draw();
											});
											
										}
										});
									}
								});
								
							}
					});
					
		});
				
			}
		});
}
$( ".activeStores" ).click(function(e) {
		loading();
		$('.report_header').html('Active Stores');
		$('#store_listing').remove();
		$('.htmltochange').html('<table id="store_listing" class="table table-bordered dataTables dataTable no-footer">'+
							'<thead>'+
								'<tr role="row">'+
									'<th>Store Name</th>'+
									'<th>Province</th>'+
									'<th width="10%">Products</th>'+
									'<th  width="10%">Specials</th>'+
									'<th  width="10%">Admins</th>'+
									'<th  width="10%">Users</th>'+
									
								'</tr>'+
							'</thead>'+
							'<tbody>'+

							'</tbody>'+
						'</table>');
		
		e.preventDefault();
		e.stopImmediatePropagation();
		var url = baseUrl+'home/getAllActiveStores';
		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			data: {},
			success: function(data){
				unloading();
				
				$("#storeFormat").html(data.StoreType);
				$("#store_listing tbody").html(data.html);
				$("#store_details").removeClass('hide');	
					
					$('#store_listing').on('click','.showpromodetails',function(e){
						showpromodetails(e,$(this).attr('data-href'));
					});
				$("#store_listing").dataTable({
						"pageLength": 50,
						paging: true,
						bFilter: true,
						ordering: true,
						searching: true,
						
						initComplete: function () {
							$('<div class="form-group filterstorebyproviencediv"></div>').appendTo($("#store_listing_length"))
							this.api().columns().every(function () {
								var column = this;

								if (column.index() == 1) {       
								var select = $('<select class="form-control getproductByCategory"><option value="">Select Province</option>'+data.provinceHtml+'</select>')
									.appendTo($(".filterstorebyproviencediv"))
									.on('change', function () {
									var val = $.fn.dataTable.util.escapeRegex(
									$(this).val());                                     

									column.search(val ? '^' + val + '$' : '', true, false)
										.draw();
								});
								
							}
							});
						}
					});
					
				}
		});
					
});

$( ".provienceName" ).click(function(e) {
					loading();
					
					$('html, body').animate({
						scrollTop: $(".report_header").offset().top
					}, 100);
	
					$('.report_header').html('Stores In '+$(this).html());
					$('#store_listingOfProvience').remove();
					$('.htmltochange').html('<table id="store_listingOfProvience" class="table table-bordered dataTables dataTable no-footer">'+
										'<thead>'+
											'<tr role="row">'+
												'<th>Store Name</th>'+
												'<th>Retailer</th>'+
												'<th width="10%">Products</th>'+
												'<th  width="10%">Specials</th>'+
												'<th  width="10%">Admins</th>'+
												'<th  width="10%">Users</th>'+
												
											'</tr>'+
										'</thead>'+
										'<tbody>'+

										'</tbody>'+
									'</table>');
					
					e.preventDefault();
					e.stopImmediatePropagation();
					var url = $(this).attr('data-href');
					$.ajax({
						url: url,
						type: 'POST',
						dataType: 'json',
						data: {},
						success: function(data){
							unloading();							
							
							$("#store_listingOfProvience tbody").html(data.html);
								$("#store_listingOfProvience tbody tr:not('.storelistrow')").remove();	
								$("#store_listingOfProvience tbody .container").remove();	
								$("#store_listingOfProvience tbody").html($("#store_listingOfProvience tbody").html());
								$('#store_listingOfProvience').on('click','.showpromodetails',function(e){
									showpromodetails(e,$(this).attr('data-href'));
								});
								
								$("#store_listingOfProvience").dataTable({
									"pageLength": 50,
									paging: true,
									bFilter: true,
									ordering: true,
									searching: true,
									initComplete: function () {
										$('<div class="form-group filterstorebyretailerdiv"></div>').appendTo($("#store_listingOfProvience_length"))
										this.api().columns().every(function () {
											var column = this;

											if (column.index() == 1) {       
											var select = $('<select class="form-control"><option value="">Select Retailers</option>'+data.RetailerHtml+'</select>')
												.appendTo($(".filterstorebyretailerdiv"))
												.on('change', function () {
												var val = $.fn.dataTable.util.escapeRegex(
												$(this).val());                                     

												column.search(val ? '^' + val + '$' : '', true, false)
													.draw();
											});
											
										}
										});
									}
									
								});
								
							}
					});
					
		});
		


$('body').on('click', '.retailersCount', function(){
	loading();
	$('.report_header').html('Retailers');
	$('.htmltochange').html('<table id="retailerTable" class="table table-bordered table-hover table-striped dataTables">'+
               '<thead>'+
                  '  <tr>'+
                       ' <th width="20%">Retailer</th>'+
					    ' <th>Company</th>'+	 ' <th>Catalogue</th>'+	' <th>Active Specials</th>'+' <th>Store Formats</th>'+		
                    '</tr>'+
                '</thead>'+
                '<tbody></tbody>'+
            '</table>');
			var url = base_url+'admin/home/getAllRetailers/';
			$.ajax({
				url: url,
				type: 'POST',
				dataType: 'json',
				data: {},
				success: function(data){
					unloading();
				
					$("#retailerTable tbody").html(data.html);
					
					var table =$("#retailerTable").dataTable({		
												
						  "pageLength": 50,
							paging: true,
							bFilter: true,
							ordering: true,
							searching: true,
							
						});			
					
					//table.columns( '.hide_column' ).visible( false );				
				}
			});
});

$('body').on('click', '.usersCount', function(){
	getUsers('AllUsers');
});
$('body').on('click', '.newUsers', function(){
	getUsers('newUsers');
});
function getUsers(parameter){
	loading();
	$('.report_header').html('Users');
	$('.htmltochange').html('<table id="userTable" class="table table-bordered table-hover table-striped dataTables">'+
               '<thead>'+
                  '  <tr>'+
                       ' <th width="40%">Name</th>'+
					    ' <th>Email</th>'+					  
						' <th>DateOfBirth</th>'+	
						
						' <th>Registered Date</th>'+	
                    '</tr>'+
                '</thead>'+
                '<tbody></tbody>'+
            '</table>');
			var url = base_url+'admin/home/getAllUsers/'+parameter;
			$.ajax({
				url: url,
				type: 'POST',
				dataType: 'json',
				data: {},
				success: function(data){
					unloading();
				
					$("#userTable tbody").html(data.html);
					
					var table =$("#userTable").dataTable({		
												
						  "pageLength": 50,
							paging: true,
							bFilter: true,
							ordering: true,
							searching: true,
							
						});			
					
					//table.columns( '.hide_column' ).visible( false );				
				}
			});
}
$('body').on('click', '.productCatalogue', function(){
	loadproducts('generalproducts');
	$('.report_header').html('Product Catalogue');
});
$('body').on('click', '.specialproductcount', function(){
	loadproducts('specialproducts');
	$('.report_header').html('Products on Special');
});
$('body').on('click', '.itemsonspecial', function(){
	loadproducts('specialproducts');
	$('.report_header').html('Items On Specials');
});
function loadproducts(parameter){
	loading();
	
	$('.htmltochange').html('<table id="products_table" class="table table-bordered table-hover table-striped dataTables">'+
               '<thead>'+
                  '  <tr>'+
                       ' <th width="40%">Product Name</th>'+
					   ' <th class="hide_column">Category</th>'+
					    ' <th>Shares</th>'+
					   ' <th>Views</th>'+
					   ' <th>Reviews</th>'+
                    '</tr>'+
                '</thead>'+
                '<tbody></tbody>'+
            '</table>');
			var url = base_url+'admin/home/getProducts/'+parameter;
			$.ajax({
				url: url,
				type: 'POST',
				dataType: 'json',
				data: {},
				success: function(data){
					unloading();
				
					$("#products_table tbody").html(data.html);
					
					var table =$("#products_table").dataTable({		
												
						  "pageLength": 50,
							paging: true,
							bFilter: true,
							ordering: true,
							searching: true,
							
							 initComplete: function () {
								$('<div class="form-group filterproductsbycategorydiv" style="margin-bottom: 5px;float:right"></div>').appendTo($("#products_table_length"))
								this.api().columns().every(function () {
									var column = this;

									if (column.index() == 1) {       
									var select = $('<select class="form-control getproductByCategory"><option value="">Select Category</option>'+data.categoriesHtml+'</select>')
										.appendTo($(".filterproductsbycategorydiv"))
										.on('change', function () {
										var val = $.fn.dataTable.util.escapeRegex(
										$(this).val());                                     

										column.search(val ? '^' + val + '$' : '', true, false)
											.draw();
									});
									
								}
								});
							}
						});			
					
					//table.columns( '.hide_column' ).visible( false );				
				}
			});
}

$('body').on('click', '.toptrendingproductsofspecial', function(){
	$('.report_header').html('Top 20 Trending Products By Special');
	$('.trendingproductblocktab').removeClass('activetrendingproducttab');
	$(this).addClass('activetrendingproducttab');
	trendingproductset('productspecial');
});
$('body').on('click', '.toptrendingproductsofviews', function(){
	$('.report_header').html('Top 20 Trending Products By Views');	 
	$('.trendingproductblocktab').removeClass('activetrendingproducttab');
	$(this).addClass('activetrendingproducttab');	
	trendingproductset('productviews');
});
$('body').on('click', '.toptrendingproductsofreview', function(){
	$('.report_header').html('Top 20 Trending Products By Reviews');	
	$('.trendingproductblocktab').removeClass('activetrendingproducttab');
	$(this).addClass('activetrendingproducttab');
	trendingproductset('productreviews');
});
$('body').on('click', '.toptrendingproductsofshare', function(){
	$('.report_header').html('Top 20 Trending Products By Share');	
	$('.trendingproductblocktab').removeClass('activetrendingproducttab');
	$(this).addClass('activetrendingproducttab');
	trendingproductset('productshares');
});




function lastThreeMonthsOptions(monthYr=''){
	var str = "<option value=''>Select Month</option>";

        var monthNames = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];


        var varDate = new Date();
        var month = varDate.getMonth()+1;
        var currentDay = varDate.getDate();


        for ( var i = 2; i >= 0; i--) {

            var now = new Date();
            now.setDate(1);
			
            var date = new Date(now.setMonth(now.getMonth() - i));
            var datex =  date.getFullYear()+'-'+("0" + (date.getMonth() + 1)).slice(-2) + "";
			if(monthYr==datex)
			 str+= "<option selected='selected' value='"+datex+"'>" + monthNames[date.getMonth()] + "-" + date.getFullYear() + "</option>";
			else str+= "<option value='"+datex+"'>" + monthNames[date.getMonth()] + "-" + date.getFullYear() + "</option>";
        }
		
        $(".monthlyReports").html(str);
		if(monthYr=='')
			$('.monthlyReports option:last').prop('selected', true);
		
}
function trendingproductset(parameter='',monthYr=''){
	
	loading();

$('.htmltochange').html('<table id="trendingproductstbl" class="table table-bordered table-hover table-striped dataTables ">'+
               '<thead>'+
                  '  <tr>'+
                       ' <th width="80%">Product Name</th>'+
					   ' <th>Count</th>'+
                    '</tr>'+
                '</thead>'+
                '<tbody class="trendingproducts"></tbody>'+
            '</table>');
		
	
	var url = base_url+'admin/home/gettrendingproducts/'+parameter+'/'+monthYr+'/';
	$.ajax({
		url: url,
		type: 'POST',
		dataType: 'json',
		data: {},
		success: function(data){
			unloading();
	
				$('.trendingproducts').html(data.html);
				var table =$("#trendingproductstbl").dataTable({		

				  "pageLength": 50,
					paging: true,
					bFilter: true,
					ordering: true,
					searching: true,
					 initComplete: function () {
								$('<div class="form-group row" style="margin-bottom: 5px;float:right">'+
								 ' <div class="col-md-6"><b style="display: inline-block;    margin-top: 7px;">Filter By Month</b></div><div class=" col-md-3">'+
								  '<select class="form-control monthlyReports">'+
								  '</select>'+
								  '</div></div><div class="clear clearfix"></div>').appendTo($("#trendingproductstbl_length"));															
									lastThreeMonthsOptions(monthYr);
									
									$( ".monthlyReports" ).change(function(e) {
										loading();
										trendingproductset(parameter,$(".monthlyReports" ).val());
									});
								}
				});
				
				
						
		}
	});
}
$( ".mostvisitedproducts" ).click(function(e) {
	getMostVisitedProducts();
});
function getMostVisitedProducts(monthYr=''){
	loading();
	$('.htmltochange').html('<table id="mostvisitedproducts" class="table table-bordered table-hover table-striped dataTables ">'+
               '<thead>'+
                  '  <tr>'+
                       ' <th width="80%">Product Name</th>'+
					   ' <th>Count</th>'+
                    '</tr>'+
                '</thead>'+
                '<tbody class=""></tbody>'+
            '</table>');
		
	
	var url = base_url+'admin/home/getMostVisitedProducts/'+monthYr+'/';
	$.ajax({
		url: url,
		type: 'POST',
		dataType: 'json',
		data: {},
		success: function(data){
			unloading();
	
				$('#mostvisitedproducts tbody').html(data.html);
				var table =$("#mostvisitedproducts").dataTable({		

				  "pageLength": 50,
					paging: true,
					bFilter: true,
					ordering: true,
					searching: true,
					 initComplete: function () {
								$('<div class="form-group row" style="margin-bottom: 5px;float:right">'+
								 ' <div class="col-md-6"><b style="display: inline-block;    margin-top: 7px;">Filter By Month</b></div><div class=" col-md-3">'+
								  '<select class="form-control monthlyReports">'+
								  '</select>'+
								  '</div></div><div class="clear clearfix"></div>').appendTo($("#mostvisitedproducts_length"));															
									lastThreeMonthsOptions(monthYr);
									
									$( ".monthlyReports" ).change(function(e) {
										loading();
										getMostVisitedProducts($(".monthlyReports" ).val());
									});
								}
				});
				
				
						
		}
	});
}
$( ".livePromos" ).click(function(e) {
	loading();	  
	$('.htmltochange').html('<table id="specialListing" class="table table-bordered table-hover table-striped dataTables ">'+
               '<thead>'+
                  '  <tr>'+
                       ' <th>Special</th>'+
					   ' <th>From</th>'+ ' <th>To</th>'+ ' <th>Count</th>'+
                    '</tr>'+
                '</thead>'+
                '<tbody></tbody>'+
            '</table>');
	$('.report_header').html('Live Promos');
	
	var url = base_url+'admin/home/getAllLiveSpecials/';
	$.ajax({
		url: url,
		type: 'POST',
		dataType: 'json',
		data: {},
		success: function(data){
			unloading();	
				$('#specialListing tbody').html(data.html);				
				var table =$("#specialListing").dataTable({		

						  "pageLength": 50,
							paging: true,
							bFilter: true,
							ordering: true,
							searching: true,
							
						});
				
			
		}
	});
});
$( ".checkedinusers" ).click(function(e) {
	showCheckedinUsers();
});
function showCheckedinUsers(monthYr=''){
	loading();	  
	$('.htmltochange').html(''+
			'<div class="form-group col-md-10" style="margin-bottom: 35px;">'+
			 ' <div class=" col-md-2" style="margin-top: 10px;"><b>Filter By Month</b></div><div class=" col-md-3">'+
			  '<select class="form-control monthlyReports">'+
			  '</select>'+
			  '</div></div>'+'<table id="storecheckedinusers" class="table table-bordered table-hover table-striped dataTables ">'+
               '<thead>'+
                  '  <tr>'+
                       ' <th>User</th>'+
					   ' <th>Store</th>'+ ' <th>Store Address</th>'+ ' <th>CheckedIn Time</th>'+
                    '</tr>'+
                '</thead>'+
                '<tbody></tbody>'+
            '</table>');
	$('.report_header').html('Checkedin Users');
	lastThreeMonthsOptions(monthYr);
	var url = base_url+'admin/home/getUsersInfo/storecheckedin/'+monthYr;
	$.ajax({
		url: url,
		type: 'POST',
		dataType: 'json',
		data: {},
		success: function(data){
			unloading();	
				$('#storecheckedinusers tbody').html(data.html);
				$( ".monthlyReports" ).change(function(e) {
					loading();
					showCheckedinUsers($(".monthlyReports" ).val());
				});
				var table =$("#storecheckedinusers").dataTable({		

						  "pageLength": 50,
							paging: true,
							bFilter: true,
							ordering: true,
							searching: true,
							
						});
				
			
		}
	});
}
function GetMonthName(monthNumber) {
  var months = ["Jan","Feb","Mar","Apr","May","June","July","Aug",'Sept',"Oct","Nov","Dec"];
  return months[monthNumber-1];
}
$( ".signedusers" ).click(function(e) {
	var date = new Date();
	showSignupUsers(date.getFullYear());
});
function showSignupUsers(monthYr=''){
	loading();
	$('.report_header').html('Registered Users');	
	
	var date = new Date();
	var html='';
	
	for(var i = date.getFullYear(); i > (date.getFullYear()-4); i--){ 
		if(i==monthYr)
			html+='<option selected="selected" value="'+i+'">'+i+'</option>';
		else html+='<option value="'+i+'">'+i+'</option>';
	}
	$('.htmltochange').html('<div class="usagebarchart"><div class="col-sm-3 filterusersbyyear"><label> Filter By Year</label><select id="filterusersbyyear"><option value="">Select Year</option>'+html+
	'</select></div><div class="clearfix clear"></div><div id="chart" class="" style="height: 250px;"></div></div>');
	
	var url = base_url+'admin/home/getsignupusers/'+monthYr;
	$.ajax({
		url: url,
		type: 'POST',
		dataType: 'json',
		data: {},
		success: function(result){
			
			unloading();	
			
			var resultArr=[];
			$.each(result, function(index, val) {						
						resultArr.push({'month':index,'value':val});
					});
			
		  Morris.Bar({
		  element: 'chart',
		  data:resultArr ,
		  xkey: 'month',
		  ykeys: ['value'],
		  labels: ['Registered Users'],
		 barColors: function (row, series, type) {
				return "#ED3237";
		 }
		});
		
		$( "#filterusersbyyear" ).change(function(e) {
					
					showSignupUsers($("#filterusersbyyear" ).val());
		});
				
	
		}
	});
	
}

	var url = base_url+'getsignupuserforcurryear';
	$.ajax({
		url: url,
		type: 'POST',
		dataType: 'json',
		data: {},
		success: function(result){
			
			unloading();	
			
			var resultArr=[];
			$.each(result, function(index, val) {						
						resultArr.push({'month':index,'value':val});
					});
			
		  Morris.Bar({
		  element: 'chartContainer',
		  data:resultArr ,
		  xkey: 'month',
		   xLabelMargin: 10,
		  ykeys: ['value'],
		  labels: ['Registered Users'],
		 barColors: function (row, series, type) {
				return "#ED3237";
		 }
		});
	
		}
	});

$( ".info-box-content a" ).click(function(e) {
	$('.info-box').removeClass('selectedview');
	$('html, body').animate({
				scrollTop: $(".report_header").offset().top
			}, 1000);
	if($(this).hasClass('usersbygeo')){	
		
		$('.report_header').html('Users by Geographic Area');
		$('#users_state_report').show();
		$(".htmltochange").html('');
		
	} 
	else
		$('#users_state_report').hide();
});	


$( ".specialbrowsebox" ).click(function(e) {
	loading();	  
	specialbrowse();
	$('.htmltochange').html('<table id="specialbrowsetable" class="table table-bordered table-hover table-striped dataTables ">'+
               '<thead>'+
                  '  <tr>'+
                       ' <th>Specials</th>'+
					   ' <th>Browse Count</th>'+
                    '</tr>'+
                '</thead>'+
                '<tbody></tbody>'+
            '</table>');
	$('.report_header').html('Specials Visits');

});
specialbrowse();
function specialbrowse(){
	var url = baseUrl+'home/getspecialsbrowseofretailer';
		$.ajax({
		url: url,
		type: 'POST',
		dataType: 'json',
		data: {},
		success: function(data){
			unloading();
			$('#specialbrowse').html(data.browseCount);
			$('#specialbrowsetable tbody').html(data.html);
			
			var table =$("#specialbrowsetable").dataTable({		

					  "pageLength": 50,
						paging: true,
						bFilter: true,
						ordering: true,
						searching: true,
						
					});
		}
	});
}

(function(w,d,s,g,js,fjs){
g=w.gapi||(w.gapi={});g.analytics={q:[],ready:function(cb)     {this.q.push(cb)}};
js=d.createElement(s);fjs=d.getElementsByTagName(s)[0];
js.src='https://apis.google.com/js/platform.js';
fjs.parentNode.insertBefore(js,fjs);js.onload=function() {g.load('analytics')};
}(window,document,'script'));

$( ".nav-tabs li" ).click(function(e) {	
	$('.htmltochange').html('');
	$('.report_header').html('');
});

$( ".usersbygender" ).click(function(e) {
	loading();
	$('.report_header').html('Users by Gender');
	
	var url = baseUrl+'home/getUsersByGender';
		$.ajax({
		url: url,
		type: 'POST',
		dataType: 'json',
		data: {},
		success: function(data){
			unloading();
			$('.htmltochange').html('<div id="usagepiechart"></div>');
			var resultArrForPieChart=[];
			$.each(data.usersbygender, function(index, val) {
						 valToput=(val/data.totalusers *100) ;
						resultArrForPieChart.push({'label':index,'value':val+'( '+valToput.toFixed(2)+' %)'});
					});

			Morris.Donut({
			  element: 'usagepiechart',
			  data: resultArrForPieChart,
			  labelColor: '#000000',
				colors: [
					'#ffbf00',
					'#ff4000',					
					
				],
				 formatter: function (x) { return x + "" }
			});
		}
	});
	
});	

$( ".usersbyage" ).click(function(e) {
	loading();
	$('.report_header').html('Users by Age');

	var url = baseUrl+'home/getUsersByAge';
		$.ajax({
		url: url,
		type: 'POST',
		dataType: 'json',
		data: {},
		success: function(data){
			unloading();
			$('.htmltochange').html('<div id="useragechart"></div>');
			var resultArrForPieChart=[];
				$.each(data.usersGroupByAge, function(index, val) {
							 valToput=(val/data.totalSumOfValues *100) ;
							resultArrForPieChart.push({'label':index,'value':valToput.toFixed(2)});
						});

				Morris.Donut({
					element: 'useragechart',
					data: resultArrForPieChart,
					labelColor: '#000000',
					colors: [
						'#ffbf00',
						'#ff4000',
						'#d81922',
						'#F81922',
						'#16a085',
						'#f39c12',
						'#ffbf00',
						'#0080ff',
					],
					 formatter: function (x) { return x + "%" }
				});
			/*var resultArrForBarChart=[];
			$.each(data.usersGroupByAge, function(index, val) {						
				resultArrForBarChart.push({'agegroup':index,'value':val});
			});
			
			Morris.Bar({
			element: 'useragechart',
			data:resultArrForBarChart ,
			xkey: 'agegroup',
			xLabelMargin: 2,
			ykeys: ['value'],
			labels: ['Users'],
			 barColors: function (row, series, type) {
					return "#ED3237";
			 }
		});*/
		}
	});
	
});	


getUsageActivityClick();

function getUsageActivityClick(){
	$( ".bylastweek" ).click(function(e) {
		getusageactivity('bylastweek','visits','week','');
	});
	$( ".timeofday" ).click(function(e) {	
		$('.trendingproductblocktab .info-box').removeClass('selectedview');
		$(this).parent().parent().addClass('selectedview');
		getusageactivity('timeofday','visits','hour','');
		
	});	
	$( ".totalvisits" ).click(function(e) {
		$('.trendingproductblocktab .info-box').removeClass('selectedview');
		$(this).parent().parent().addClass('selectedview');
		getusageactivity('year','visits','month','');
	});
	$( ".usagetimespend" ).click(function(e) {
		$('.trendingproductblocktab .info-box').removeClass('selectedview');
		$(this).parent().parent().addClass('selectedview');
		getusageactivity('year','sessionDuration','month','');
	});
}

var date = new Date();
getusageactivity('monthly','visits','week','currentmonthhere');

function getusageactivity(timeduration='',matrics='',dimension='',month='',dailydate='notselected',startdate='notselected',enddate='notselected')
{
		loading();
		var date = new Date();
			monthtopass=month;
			if(month=='currentmonthhere'){				
				monthtopass=date.getFullYear()+'-'+'0'+(date.getMonth()+1);
				
			}
		var url = baseUrl+'home/getusageanalytics/'+timeduration+'/'+matrics+'/'+dimension+'/'+monthtopass+'/'+dailydate+'/'+startdate+'/'+enddate;
		$.ajax({
		url: url,
		type: 'POST',
		dataType: 'json',
		data: {},
		success: function(data){
			var date = new Date();
			currentmonthhere='';
			if(month=='currentmonthhere'){
				currentmonthhere='currentmonthhere';
				month=date.getFullYear()+'-'+(date.getMonth()+1);
				
			}
				
			unloading();
			var d = new Date(month+'-01');
			var monthNames = ["January", "February", "March", "April", "May", "June",
				"July", "August", "September", "October", "November", "December"
			];
			
			labelforgraph='Usage Activity Of ';
			if(matrics=='visits') labelforgraph='VISITS ';
			else labelforgraph='Session ';
			if(month=='')
			labelforgraph+='FOR '+date.getFullYear()+' Year';	
			else {
				
				labelforgraph+='FOR '+monthNames[d.getMonth()]+' '+date.getFullYear();
			}
			labelForDailyReport='';
			if(month!='' && startdate=='notselected'){
				labelForDailyReport='Monthly Report '+monthNames[d.getMonth()]+' '+date.getFullYear();
			}
			else if(month!='' && startdate!='notselected'){
				labelForDailyReport='Weekly Report From '+startdate+' To '+enddate;
			}
			if(timeduration=='timeofday')
				labelforgraph='Usage Activity For Today';
			weeklyGraphLabel='';
			if(dimension=='week'){
				labelforgraph=''; weeklyGraphLabel='WEEKLY REPORT FOR '+monthNames[d.getMonth()]+' '+date.getFullYear();
			}
			if(dailydate!='notselected') labelforgraph='Daily Report for Date '+dailydate;
			$('.report_header').html('Usage Activities');
			filterbymonth='';
			sdate=startdate;
			if(startdate=='notselected') sdate='';
			edate=enddate;
			if(enddate=='notselected') edate='';
			if(timeduration!='timeofday'){
				/*filterbymonth='<div class="form-group row">'+
				 ' <div class="col-md-2"><b style="display: inline-block;    margin-top: 7px;">Filter By Month</b></div><div class=" col-md-3">'+
				  '<select class="form-control monthlyReports">'+
				  '</select>'+
				  '</div>';
				  */
				  filterbymonth+='<div class="buttonsforfilter col-md-3"><div class="info-box "><div id="" class="info-box-content ">													 <span class="info-box-text">Monthly View <input style="height: 0; width: 0; border-color: transparent;" type="text" placeholder="Select Month" id="monthlycalendar" class="" >  <a  href="javascript:void(0);" class="toselectemonths "> <i class="fa fa-calendar usagecalender monthlyview " aria-hidden="true"></i></a></span>  </div>							<!-- /.info-box-content -->						</div>'+
				  '</div>';
				  
				//if(timeduration=='monthly')  {
					filterbymonth+='<div class="buttonsforfilter col-md-3"><div class="info-box weeklyviewcal"><div class="info-box-content"> <span class="info-box-text" >Weekly View<a data-toggle="modal"  href="javascript:void(0);" class=" "><i class="fa fa-calendar usagecalender weeklyview " aria-hidden="true"></i></a></span><div style="display:none;" class="weeklyviewcaldiv"></div>  </div>							<!-- /.info-box-content -->						</div></div>'
				  + '<div class="buttonsforfilter col-md-3"><div class="info-box dailyviewcal"><div class="info-box-content"> <span class="info-box-text">Daily View<a  href="javascript:void(0);" ><i class="fa fa-calendar usagecalender dailyview " aria-hidden="true"></i></a></span> </div>							<!-- /.info-box-content -->						</div></div>';
				//}

				 filterbymonth+= '</div><div class="clear clearfix"></div>';
		  }
				weeklyshowdatepicker=dailyshowdatepicker='Please Select Month Firstly';
				dailydatee='';
				if(dailydate!='notselected')dailydatee=dailydate;
				if(month!=''){
			
				
				weeklyshowdatepicker='<div class="">'+'<div class="form-group">'+
            '<div class="row">'+
                '<div class="col-md-6">'+
                    '<label for="StartDate">Start Date <span>*</span></label>'+
                     ' <i class="fa fa-calendar StartDate"></i>'+
'                        <input class=" valid" style="height: 0; width: 0; border-color: transparent;"  name="StartDate" id="StartDate" placeholder="Start Date" value="'+sdate+'" aria-invalid="false" type="text">'+
'                </div>'+
'                <div class="col-md-6">'+
'                    <label for="EndDate">EndDate <span>*</span></label>'+
						' <i class="fa fa-calendar EndDate"></i>'+                    '<input class="" style="height: 0; width: 0; border-color: transparent;"  name="EndDate" id="EndDate" placeholder="End Date" value="'+edate+'" type="text">'+                 
'                    <div class="error">'+
'                                            </div>'+
'                </div>'+
'            </div>'+
'         </div></div>';
				}
			if(month!=''){
				dailyshowdatepicker='<div class="" style="text-align: center;">'+''+
            '<div class="row">'+
                '<div class="col-md-12">'+
                    '<div class="">'+
                     '   <label for="StartDate">Select Date <span>*</span></label>'+
'                            <i class="fa fa-calendar dailydate"></i>'+
'                '+
'                        <input class="valid" style="height: 0; width: 0; border-color: transparent;"  style="left: 15px;"  name="" id="dailydate" placeholder="Select Date" value="'+dailydatee+'" aria-invalid="false" type="text">'+
              '      </div>'+
 
'            </div>'+
'         </div></div>';
				}
				graphreports='';
				if(dimension=='week'){
					graphreports=
							'<div class="col-md-12 col-sm-12 col-xs-12 weeklyreportlabel "><h4 class="usagegraphlabel">'+weeklyGraphLabel+'</h4></div>'+
							'<div class="col-md-6 col-sm-6 col-xs-6 usagebarchart "><div id="usagebarchart" ></div></div>'+
							'<div class="col-md-6 col-sm-6 col-xs-6 usagepiechart"><div id="usagepiechart" ></div></div>'+
							
														
							'<div class="col-md-12 col-sm-12 col-xs-12 "><hr/><h4 class="usagegraphlabel">'+labelForDailyReport+'</h4></div>'+
							'<div class="col-md-12 col-sm-12 col-xs-12 usagebarchartfordate "><div id="usagebarchartfordate" ></div></div>'+
							'';//	'<div class="col-md-6 col-sm-6 col-xs-6 usagepiechartfordate "><div id="usagepiechartfordate" ></div></div>'+
				}
				else if(dimension=='hour'){
					graphreports=							
							'<div class="col-md-12 col-sm-12 col-xs-12 usagebarchart "><div id="usagebarchart" ></div></div>'+
							'';
				}
				else{
					graphreports=							
							'<div class="col-md-6 col-sm-6 col-xs-6 usagebarchart "><div id="usagebarchart" ></div></div>'+
							'<div class="col-md-6 col-sm-6 col-xs-6 usagepiechart "><div id="usagepiechart" ></div></div>'+
							'';
				}
				$('.htmltochange').html(filterbymonth+
				'<div class="row">'+							
				
				'<div class="col-md-12 col-sm-12 col-xs-6 usagegraph"><h4 class="usagegraphlabel">'+labelforgraph+'</h4>'+
				graphreports+
				
				'</div>'
				  );	
				 $('.htmltochange').append('<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">'+' <div class="modal-dialog" style="width:260px !important" role="document">'+
				'<div class="modal-content">'+
				 
				  '<div class="modal-body tofilterusagegraph">'+
					
				 ' </div>'+
				 
				'</div>'+
			  '</div>'+
			'</div>');
								
				/* to open months list */
				
				if(dimension=='week' && month!=''  && startdate=='notselected' && dailydate=='notselected'){					
					$(".toselectemonths").addClass('selectedview');
				}
				var varDate = new Date();
				
				$(".toselectemonths").click(function () {	
						$("#monthlycalendar").datepicker("show");		
						$('.weeklyviewcaldiv').hide();
				});	
				$('#monthlycalendar').datepicker({						
					format: 'yyyy-mm',
					minViewMode: 1,
					autoclose: true,
					startDate: new Date(date.getFullYear()+'-01-01'),
					endDate: new Date(date.getFullYear()+'-'+parseInt(date.getMonth()+1)+'-01'),

				});
				
				
				$("#monthlycalendar").change(function(e) {
	
					topassyearmonth=$(this).val();					
					getusageactivity('monthly',matrics,'week',topassyearmonth);
					$('#myModal').modal('hide');
				
				});
				/* to open calender for week */
				
				if(dimension=='week' && month!='' && startdate!='notselected'){	
				  
					$(".weeklyviewcal").addClass('selectedview');
				}				
				
					$(".weeklyviewcal").click(function () {	
						if(month!=''){
								$('.weeklyviewcaldiv').show();
								$('.weeklyviewcaldiv').html('<div class="weekcalendar viewstoshow">'+weeklyshowdatepicker+'</div>');
						}
						else alert('Please Select Month First');
				   
					});
					$('#StartDate').datepicker({
							format: 'yyyy-mm-dd',
							autoclose: true,
							startDate: new Date(month+'-01'),
							endDate: new Date(data.returnLastDate),
						}).on('changeDate', function(selected){
								startDate = new Date(selected.date.valueOf());
								startDate.setDate(startDate.getDate(new Date(selected.date.valueOf())));
								$('#EndDate').datepicker('setStartDate', startDate);
						});
						
						$('#EndDate').datepicker({
								format: 'yyyy-mm-dd',
								autoclose: true,
								startDate: new Date(month+'-01'),
								endDate: new Date(data.returnLastDate),
						}).on('changeDate', function(selected){
								FromEndDate = new Date(selected.date.valueOf());
								FromEndDate.setDate(FromEndDate.getDate(new Date(selected.date.valueOf())));
								$('#StartDate').datepicker('setEndDate', FromEndDate);
						});
						
						$('.StartDate').click(function () {	
							$("#StartDate").datepicker("show");
						});
						$('.EndDate').click(function () {	
							$("#EndDate").datepicker("show");
						});
						
						$( "#EndDate" ).change(function(e) {
							$('#myModal').modal('hide');
							if($('#StartDate').val()!='' && $('#EndDate').val()!='')
							getusageactivity('monthly',matrics,'week',month,'notselected',$('#StartDate').val(),$('#EndDate').val());
						});
				/* to open calender for date */

				if(dimension=='hour' && dailydate!='notselected'){

					$(".dailyviewcal").addClass('selectedview');
				}

				$(".dailyview").click(function () {
					$('.weeklyviewcaldiv').hide();
					//$('.tofilterusagegraph').html('<div class="">'+dailyshowdatepicker+'</div>');
					if(month!=''){
						
						$("#dailydate").datepicker("show");
					}
					else alert('Please Select Month First');
				});
				$('#dailydate').datepicker({
						format: 'yyyy-mm-dd',
						autoclose: true,
						startDate: new Date(month+'-01'),
						endDate: new Date(data.returnLastDate),
					});
					
					$( "#dailydate" ).change(function(e) {
						$('#myModal').modal('hide');
						if($('#dailydate').val()!='') 
						getusageactivity('monthly',matrics,'hour',month,$('#dailydate').val());
					});
			
				var str = "";

				var monthNames = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
				var varDate = new Date();
				var currmonth = varDate.getMonth()+1;
				var currentDay = varDate.getDate();

				var now = new Date();
				now.setDate(1); 

				$( ".month" ).click(function(e) {
					if (!$(this).hasClass("disabled")) {
						if(parseInt(new Date(Date.parse($(this).html() +" 1, "+varDate.getFullYear())).getMonth()+1)>10)
							mnth=parseInt(new Date(Date.parse($(this).html() +" 1, "+varDate.getFullYear())).getMonth()+1);
						else mnth='0'+parseInt(new Date(Date.parse($(this).html() +" 1, "+varDate.getFullYear())).getMonth()+1);
						topassyearmonth=varDate.getFullYear()+'-'+mnth;					
						getusageactivity('monthly',matrics,'week',topassyearmonth);
					}
					
				});
				
					if($('#usagebarchart').length>0){
						
						var resultArrForBarChart=[];
						$.each(data.arrayForDimension, function(index, val) {						
									resultArrForBarChart.push({'dimension':index,'value':val});
								});
							if(matrics=='visits') lbl='Visit Users'; else lbl='Session Minuts';
						  Morris.Bar({
						  element: 'usagebarchart',
						  data:resultArrForBarChart ,
						  xkey: 'dimension',
						  xLabelMargin: 2,
						  ykeys: ['value'],
						  labels: [lbl],
						  barColors: function (row, series, type) {
								
									
								 if (row.label  in data.maxVisitsLabel)
											return "#ED3237";
										else return "#999599"
							
						 }
						});
					}
					if($('#usagepiechart').length>0){
						var resultArrForPieChart=[];
						$.each(data.arrayForDimension, function(index, val) {
									 valToput=(val/data.totalSumOfValues *100) ;
									resultArrForPieChart.push({'label':index,'value':val+'('+valToput.toFixed(2)+' %)'});
								});

						Morris.Donut({
						  element: 'usagepiechart',
						  data: resultArrForPieChart,
						  labelColor: '#000000',
							colors: [
								'#ffbf00',
								'#ff4000',
								'#d81922',
								'#F81922',
								'#16a085',
								'#f39c12',
								'#ffbf00',
								'#0080ff',
							],
							 formatter: function (x) { return x + "" }
						});
					}
					if(dimension=='week' && data.arrayForDimensionForDate!=''){
						var resultArrForDate=[];
						$.each(data.arrayForDimensionForDate, function(index, val) {						
									resultArrForDate.push({'dimension':index,'value':val});
								});
							
							if(matrics=='visits') lbl='Visit Users'; else lbl='Session Minuts';
						  Morris.Bar({
						  element: 'usagebarchartfordate',
						  data:resultArrForDate ,
						  xkey: 'dimension',
						  xLabelMargin: 2,
						  ykeys: ['value'],
						  labels: [lbl],
						 barColors: function (row, series, type) {
									  if (row.label  in data.maxVisitsLabelForDate)
											return "#ED3237";
										else return "#999599"
								
						 }
						});
						if(currentmonthhere=='currentmonthhere'){ 
							$('.buttonsforfilter').remove();
							$('.usagebarchart').remove();
							$('.usagepiechart').remove();
							$('.weeklyreportlabel').remove();
						}
						$("tspan").each(function(){   
							$(this).html($(this).html().replace(/.\s*$/, ""));
						});
					//	$('#usagebarchartfordate').html(resultArrForDate);
					/*	var resultArrForPieDate=[];
						$.each(data.arrayForDimensionForDate, function(index, val) {	
									valToput=(val/data.totalSumOfValues *100) ;
									resultArrForPieDate.push({'label':index,'value':valToput.toFixed(2)});
								});
							
						  Morris.Donut({
						  element: 'usagepiechartfordate',
						  data: resultArrForPieDate,
						  labelColor: '#000000',
							colors: [
								'#ffbf00',
								'#ff4000',
								'#d81922',
								'#F81922',
								'#16a085',							
								'#f39c12',
								'#ffbf00',
								'#0080ff',								
							],
							 formatter: function (x) { return x + "%" }
						});*/
					}
					if(data.arrayForDimension==''){
						$('#usagebarchart').html('No Data Found');
					}				
		
		}
	});	
}