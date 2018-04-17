var base_url =  '';
$(function(){
	base_url = $('#base_url').val();
	
	$(".knob1").knob({
		thickness: 0.2,
		fgColor: '#CD0A3E',
		inputColor: '#000000',
		format : function (value) {
			return value + '%';
		}
	});
	new Morris.Bar({
		// ID of the element in which to draw the chart.
		element: 'mychart1',
		// Chart data records -- each entry in this array corresponds to a point on
		// the chart.
		data: [
		{
			year: '2008', 
			value: 20
		},
		{
			year: '2009', 
			value: 10
		},
		{
			year: '2010', 
			value: 5
		},
		{
			year: '2011', 
			value: 5
		},
		{
			year: '2012', 
			value: 20
		}
		],
		barColors:[
		'#CD0A3E'
		],
		// The name of the data record attribute that contains x-values.
		xkey: 'year',
		// A list of names of data record attributes that contain y-values.
		ykeys: ['value'],
		// Labels for the ykeys -- will be displayed when you hover over the
		// chart.
		labels: ['Value']
	});
	if($('#view_graph').length > 0){
		$.ajax({
			url: base_url+'admin/insights/get_user_view_chart',
			type: 'POST',
			dataType: 'json',
			data:{},
			success: function(data){
				console.log(data.message);
				if(data.result == 1){
					new Morris.Line({
						// ID of the element in which to draw the chart.
						element: 'view_graph',
						// Chart data records -- each entry in this array corresponds to a point on
						// the chart.
						data: data.message,
						lineColors:[
						'#CD0A3E'
						],
						// The name of the data record attribute that contains x-values.
						xkey: 'day',
						// A list of names of data record attributes that contain y-values.
						ykeys: ['views'],
						parseTime: false,
						// Labels for the ykeys -- will be displayed when you hover over the
						// chart.
						labels: ['views']
					});
				}
			}
		});
	}
	
					
	/*
         // Comment By Manoj - 09 Feb 2017 
	var salesChartCanvas = $("#salesChart").get(0).getContext("2d");
	// This will get the first returned node in the jQuery collection.
	var salesChart = new Chart(salesChartCanvas);
	
	var salesChartData = {
		labels: ["January", "February", "March", "April", "May", "June", "July"],
		datasets: [
		{
			label: "Electronics",
			fillColor: "rgb(210, 214, 222)",
			strokeColor: "rgb(210, 214, 222)",
			pointColor: "rgb(210, 214, 222)",
			pointStrokeColor: "#c1c7d1",
			pointHighlightFill: "#fff",
			pointHighlightStroke: "rgb(220,220,220)",
			data: [65, 59, 80, 81, 56, 55, 40]
		},
		{
			label: "Digital Goods",
			fillColor: "rgba(60,141,188,0.9)",
			strokeColor: "rgba(60,141,188,0.8)",
			pointColor: "#3b8bba",
			pointStrokeColor: "rgba(60,141,188,1)",
			pointHighlightFill: "#fff",
			pointHighlightStroke: "rgba(60,141,188,1)",
			data: [28, 48, 40, 19, 86, 27, 90]
		}
		]
	};

	var salesChartOptions = {
		//Boolean - If we should show the scale at all
		showScale: true,
		//Boolean - Whether grid lines are shown across the chart
		scaleShowGridLines: false,
		//String - Colour of the grid lines
		scaleGridLineColor: "rgba(0,0,0,.05)",
		//Number - Width of the grid lines
		scaleGridLineWidth: 1,
		//Boolean - Whether to show horizontal lines (except X axis)
		scaleShowHorizontalLines: true,
		//Boolean - Whether to show vertical lines (except Y axis)
		scaleShowVerticalLines: true,
		//Boolean - Whether the line is curved between points
		bezierCurve: true,
		//Number - Tension of the bezier curve between points
		bezierCurveTension: 0.3,
		//Boolean - Whether to show a dot for each point
		pointDot: false,
		//Number - Radius of each point dot in pixels
		pointDotRadius: 4,
		//Number - Pixel width of point dot stroke
		pointDotStrokeWidth: 1,
		//Number - amount extra to add to the radius to cater for hit detection outside the drawn point
		pointHitDetectionRadius: 20,
		//Boolean - Whether to show a stroke for datasets
		datasetStroke: true,
		//Number - Pixel width of dataset stroke
		datasetStrokeWidth: 2,
		//Boolean - Whether to fill the dataset with a color
		datasetFill: true,
		//String - A legend template
		legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%=datasets[i].label%></li><%}%></ul>",
		//Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
		maintainAspectRatio: true,
		//Boolean - whether to make the chart responsive to window resizing
		responsive: true
	};

	//Create the line chart
	salesChart.Line(salesChartData, salesChartOptions);
	*/
       
	if($('.sparkbar').length > 0){
		$('.sparkbar').each(function () {
			var $this = $(this);
			$this.sparkline('html', {
				type: 'bar',
				height: $this.data('height') ? $this.data('height') : '30',
				barColor: $this.data('color')
			});
		});
	}
		
	
       
	$('#world-map-markers').vectorMap({
		map: 'za_mill',
		normalizeFunction: 'polynomial',
		hoverOpacity: 0.7,
		hoverColor: false,
		backgroundColor: 'transparent',
		regionStyle: {
			initial: {
				fill: 'rgba(210, 214, 222, 1)',
				"fill-opacity": 1,
				stroke: 'none',
				"stroke-width": 0,
				"stroke-opacity": 1
			},
			hover: {
				"fill-opacity": 0.7,
				cursor: 'pointer'
			},
			selected: {
				fill: 'yellow'
			},
			selectedHover: {
		}
		},
		markerStyle: {
			initial: {
				fill: '#00a65a',
				stroke: '#111'
			}
		}		
	});
	map = $("#world-map-markers").vectorMap('get', 'mapObject');
	map.setScale(0.5);
	
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
                
                
	
	
	if($('#users_count').length > 0){
		var user_count_chart = new CanvasJS.Chart("users_count",
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
				click: users_details_show
			}
			],
			colorSet:  "customColor1",
			toolTip:{
				enabled: false
			}
		});
	
		user_count_chart.render();
	}
	
	if($('#products_count').length > 0){
		var product_count_chart = new CanvasJS.Chart("products_count",
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
			colorSet:  "customColor2",
			toolTip:{
				enabled: false
			}
		});
	
		product_count_chart.render();
	}
	
	if($('#categories_count').length > 0){
		var categories_count_chart = new CanvasJS.Chart("categories_count",
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
				]
			//click: categories_details_show
			}
			],
			colorSet:  "customColor5",
			toolTip:{
				enabled: false
			}
		});
	
		categories_count_chart.render();
	}
        
        // Show Popular categories
        if($('#popular-category-view').length > 0){
            $.ajax({
                    url: base_url+'admin/insights/get_popular_category_view',
                    type: 'POST',
                    dataType: 'json',
                    data:{},
                    success: function(data){
                            console.log(data.message);
                            if(data.result == 1){
                                    var showHtml ="";
                                    var index = parseInt(1);
                                    var progressClass= "";
                                    $.each(data.message, function(index, element) {
                                        switch (index) { 
                                                case 1: 
                                                        progressClass = 'progress-bar-aqua';
                                                        break;
                                                case 2: 
                                                        progressClass = 'progress-bar-red';
                                                        break;
                                                case 3: 
                                                        progressClass = 'progress-bar-green';
                                                        break;
                                                case 4: 
                                                        progressClass = 'progress-bar-yellow';
                                                        break;
                                                case 5: 
                                                        progressClass = 'progress-bar-aqua';
                                                        break;        
                                        }

                                        showHtml += '<div class="progress-group">';
                                        showHtml += '<span class="progress-text">'+element.CategoryName+'</span>';
                                        showHtml += '<span class="progress-number"><b>'+element.views+'</b>/'+data.totalViews+'</span>';
                                        showHtml += '<div class="progress sm">';
                                        showHtml += '<div class="progress-bar '+progressClass+'" style="width: '+element.viewsPercentage+'%"></div>';
                                        showHtml += '</div>';
                                        showHtml += '</div>';

                                        index = parseInt(index) + parseInt(1);
                                    });

                                    $('#popular-category-view').html(showHtml);
                            }
                    }
            });
	}
        
        // Show Monthly Recap Report [ Get yearly product views]
        if($('#salesChart').length > 0){
            $.ajax({
                    url: base_url+'admin/insights/get_yearly_view',
                    type: 'POST',
                    dataType: 'json',
                    data:{},
                    success: function(data){
                       if(data.result == 1){
                           var monthlyViews = []; 
                           var months = [];
                           $.each(data.message, function(index, element) {
                                 monthlyViews.push(element.views)
                                 months.push(element.monthYear)
                            });
                            
                            $('#monthly-recap-report-duration').html(data.duration);
                                    
                            // Show Yearly data in graph
                            var salesChartCanvas = $("#salesChart").get(0).getContext("2d");
                            // This will get the first returned node in the jQuery collection.
                            var salesChart = new Chart(salesChartCanvas);                            
                            var salesChartData = {
                                    //labels: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October","Novenber", "December"],
                                    labels:months,
                                    datasets: [
                                        {
                                                label: "Yearly Product Views",
                                                fillColor: "rgba(60,141,188,0.9)",
                                                strokeColor: "rgba(60,141,188,0.8)",
                                                pointColor: "#3b8bba",
                                                pointStrokeColor: "rgba(60,141,188,1)",
                                                pointHighlightFill: "#fff",
                                                pointHighlightStroke: "rgba(60,141,188,1)",
                                                data: monthlyViews
                                                //data: [28, 48, 40, 19, 86, 27, 90,19, 86, 27, 90,80]
                                        }
                                    ]
                            };

                            var salesChartOptions = {
                                    //Boolean - If we should show the scale at all
                                    showScale: true,
                                    //Boolean - Whether grid lines are shown across the chart
                                    scaleShowGridLines: false,
                                    //String - Colour of the grid lines
                                    scaleGridLineColor: "rgba(0,0,0,.05)",
                                    //Number - Width of the grid lines
                                    scaleGridLineWidth: 1,
                                    //Boolean - Whether to show horizontal lines (except X axis)
                                    scaleShowHorizontalLines: true,
                                    //Boolean - Whether to show vertical lines (except Y axis)
                                    scaleShowVerticalLines: true,
                                    //Boolean - Whether the line is curved between points
                                    bezierCurve: true,
                                    //Number - Tension of the bezier curve between points
                                    bezierCurveTension: 0.3,
                                    //Boolean - Whether to show a dot for each point
                                    pointDot: false,
                                    //Number - Radius of each point dot in pixels
                                    pointDotRadius: 4,
                                    //Number - Pixel width of point dot stroke
                                    pointDotStrokeWidth: 1,
                                    //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
                                    pointHitDetectionRadius: 20,
                                    //Boolean - Whether to show a stroke for datasets
                                    datasetStroke: true,
                                    //Number - Pixel width of dataset stroke
                                    datasetStrokeWidth: 2,
                                    //Boolean - Whether to fill the dataset with a color
                                    datasetFill: true,
                                    //String - A legend template
                                    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%=datasets[i].label%></li><%}%></ul>",
                                    //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
                                    maintainAspectRatio: true,
                                    //Boolean - whether to make the chart responsive to window resizing
                                    responsive: true
                            };

                            //Create the line chart
                            salesChart.Line(salesChartData, salesChartOptions);
                       }// if(data.result == 1)
                    }
            });
	}
});

function users_details_show(){
	if($.trim($('#main_expansion').html()) != ''){
		$('#main_expansion').html('');
		$('#main_details_expansion').html('');
		$('#dash_bc ul').html('');
	}
	else{
		loading();
		$('#back_btn_dash').hide();
		$('#main_expansion').html('');
		$('#main_details_expansion').html('');
		$.ajax({
			url: base_url+'admin/insights/get_consumers_count_expansion',
			type: 'POST',
			dataType: 'json',
			data: {},
			success: function(data){
				unloading();
				if(data.result == 1){

					$('#main_expansion').append('<div class="col-sm-4 text-center" id="consumer_exp_region_count" style="height: 270px;padding-left: 0px;"></div>');
					bc_rows = ['Users','Region'];
					chart_create('consumers_region_count_chart','consumer_exp_region_count', 'Consumer Region', data.message.region_users_count);

					$('#main_expansion').append('<div class="col-sm-4 text-center" id="consumer_exp_gender_count" style="height: 270px;padding-left: 0px;"></div>');
					bc_rows = ['Users','Gender'];
					chart_create('consumers_gender_count_chart','consumer_exp_gender_count', 'Consumer Gender', data.message.gender_users_count);

					$('#main_expansion').append('<div class="col-sm-4 text-center" id="consumer_exp_device_count" style="height: 270px;padding-left: 0px;"></div>');
					bc_rows = ['Users','Device'];
					chart_create('consumers_device_count_chart','consumer_exp_device_count', 'Consumer Device', data.message.device_users_count);
				}
				bc_rows = ['Users'];
				place_bc(bc_rows);
			},
			error: function(){
				unloading();
			}
		});
	}
		
}

function chart_create(chart_id,element_id, title_text, datapoints,click_function,bc_rows,add_count,crop_id,class_name,no_label,big_graph){
	if(bc_rows){
		place_bc(bc_rows);
	}
	if(no_label == 1){
		indexlabel = "";
	}
	else{
		//indexlabel = "{label}: {y}";
		indexlabel = "";
	}
	
	chart_id = new CanvasJS.Chart(element_id,
	{
		title: {
			text: title_text
		},
		backgroundColor: "transparent",
		data: [
		{
			type: "column",
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
			//$('#'+element_id).parent().find('.t_bg_count').html(add_html);
			}
			else{
			//$('#'+element_id).parent().find('.t_sm_count').html(add_html);
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

function consumer_region_expand(e){
	loading();
	$('#main_details_expansion').html('');
	$.ajax({
		url: base_url+'admin/insights/get_consumer_region_expansion',
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
		url: base_url+'admin/insights/get_consumer_gender_expansion',
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
		url: base_url+'admin/insights/get_consumer_device_expansion',
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

function place_bc(values){
	$('#dash_bc ul').html('');
	$.each(values, function(index, element){
		$('#dash_bc ul').append('<li>'+element+'</li>');
	});
}

function products_details_show(){
	if($.trim($('#main_expansion').html()) != ''){
		$('#main_expansion').html('');
		$('#main_details_expansion').html('');
		$('#dash_bc ul').html('');
		$('#back_btn_dash').hide();
	}
	else{
		loading();
		$('#back_btn_dash').hide();
		$('#main_expansion').html('');
		$('#main_details_expansion').html('');
		$.ajax({
			url: base_url+'admin/insights/get_products_count_expansion',
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
						$('#main_expansion').append('<div class="col-sm-6"><span class="t_sm_count"></span><div class=" text-center product_expansion_donut" id="'+html_id+'" style="height: 200px;padding-left: 0px;"></div></div>');
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
		
}
function get_product_last_table(e){
	loading();
	$('#main_details_expansion').html('');
	$.ajax({
		url: base_url+'admin/insights/get_category_sub_count_expansion',
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
				chart_create('products_count_chart','product_exp_count', 'Main Categories', data.message.prduct_count);
			}
		},
		error: function(){
			unloading();
		}
	});
}
