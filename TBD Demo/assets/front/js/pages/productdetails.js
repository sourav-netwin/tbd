$(document).ready(function(){
	init_tab();
	init_slider();
	initAutocomplete();
	showChart();
	var base_url = $("#base_url").val();

	// Rating stars to be displayed
	$('span.product_detail_rating, .review_rating').raty({
		score: function() {
			return $(this).attr('data-score');
		},
		readOnly: true,
		path: base_url+'assets/front/img/',
		numberMax: 5
	});

	//	if($('#prod-zoom').length > 0){
	//		$('#prod-zoom').zoomify();
	//	}
	if($('#prod-zoom').length > 0){
		if($(window).width() < 992){
			$('#prod-zoom').elevateZoom(
			{
				zoomType: "lens"
			}
			); 
		}
		else{
			$('#prod-zoom').elevateZoom(); 
		}
	}
	// Mark or Unmark a product as favourite
	$(document).on('click','#add_to_fav', function (e){
                
		e.stopImmediatePropagation();
		$.LoadingOverlay("show");

		var product_id = $(this).attr('data-product-id');
                var special_id = $(this).attr('data-special-id');                   
		var is_fav = $(this).attr('data-is-fav');
		var element = $(this);

		$.ajax({
			url : $("#base_url").val()+'topoffers/toggle_favourite',
			data : {
				product_id: product_id,
                                special_id: special_id,
				is_fav: is_fav
			},
			method : 'POST',
			success : function(data)
			{
				$.LoadingOverlay("hide");

				if( is_fav == 1 )
				{
					element.attr('title','Add to Favourites');
					element.find('i').removeClass('text-danger');
					element.attr('data-is-fav', 0);
					Command: toastr["success"]("Product unmarked as favourite");
				}
				else
				{
					element.attr('title','Remove from Favourites');
					element.find('i').addClass('text-danger');
					element.attr('data-is-fav', 1);
					Command: toastr["success"]("Product marked as favourite");
				}
			}
		});
	});

	//Validate Add to Wish List Form
	$("#add_to_list_form").validate({
		rules: {
			existing_list : {
				required: {
					depends: function(element) {
						return ( $("#new_list").val() != '' ? false : true )
					}
				}
			},
			new_list : {
				required: {
					depends: function(element) {
						return ( $("#existing_list").length && $("#existing_list").val() != '' ? false : true )
					}
				},
				nospecials: true
			}
		},
		messages: {
			existing_list : {
				required: "Please select an exisitng list"
			},
			new_list : {
				required: "Please enter list"
			}
		}
	});

	$(document).on('click','#add_to_list_btn',function(e){
		$("#add_to_list_form").submit();
	});
	

	// Data loading text to be displayed only if client side validations pass
	$('#add_to_list_form').submit(function () {
		if( $(this).valid() ) {
			$('#add_to_list_btn').button('loading');
			return true;
		}
	});

	// Hide new list option when existing selected
	$(document).on('change','#existing_list',function(e){
		if( $(this).val() == '' )
		{
			$("#new_list_container").show();
		}
		else
		{
			$("#new_list").val('');
			$("#new_list_container").hide();
		}
	});

	// Hide existing list option when new list entered
	$(document).on('keyup','#new_list',function(e){
		if( $(this).val() == '' )
		{
			$("#existing_list_container").show();
		}
		else
		{
			$("#existing_list").val('');
			$("#existing_list_container").hide();
		}
	});

	// Price alert checkbox to replace with toggle switcher
	// http://simontabor.com/labs/toggles/
	$('.toggle').toggles({
		clicker:$('.clickme'),
		width: 40, // width used if not set in css
		height: 11
	});

	// Getting notified of changes, and the new state:
	$('body').on('click','#price_watch', function () {
		var elm = $(this);
		var price_alert = elm.attr('is_active');

		$.LoadingOverlay("show");

		$.ajax({
			url : $("#base_url").val()+'productdetails/toggle_price_alert',
			data : {
				price_alert : price_alert, 
				product_id: $("#product").val()
			},
			method : 'POST',
			success : function(data)
			{
				$.LoadingOverlay("hide");

				if( data == "success" )
				{
					if( price_alert == '1' )
					{
						elm.attr('is_active','0');
						elm.attr('title','Turn on Price Watch');
						elm.find('i').removeClass('text-danger');
							Command: toastr["success"]("Price alert removed for product");
					}
					else
					{
						elm.attr('is_active','1');
						elm.attr('title','Turn off Price Watch');
						elm.find('i').addClass('text-danger');
							Command: toastr["success"]("Price alert added for product");
							
					}
				}
			}
		});
	});

	$(document).on('click',"#compare_all", function(e){
		$(".product_comparison_container").show();
		$("#compare_all").hide();
	});

	// Rating stars to be displayed
	$('.add_review_rating').raty({
		score: function() {
			return $(this).attr('data-score');
		},
		path: base_url+'assets/front/img/',
		numberMax: 5,
		click: function(score, evt) {
			$("#rating").val(score);
		}
	});

	//Validate Add Review Form
	$("#add_review_form").validate({
		rules: {
			review : {
				required: true,
				nospecials: true
			}
		},
		messages: {
			review : {
				required: "Please enter review"
			}
		}
	});

	$(document).on('click','#add_review',function(e){
		if( $("#add_review_form").valid() ) {

			$('#add_review').button('loading');
			$.LoadingOverlay("show");

			$.ajax({
				url : $("#base_url").val()+'productdetails/add_review',
				data : {
					review : $("#review").val(), 
					rating: $("#rating").val(), 
					product_id: $("#product").val()
				},
				method : 'POST',
				success : function(data)
				{
					$('#add_review').button('reset');
					$.LoadingOverlay("hide");

					if( data != '' )
					{
						var full_data = '<div class="row rating-div">'+data+'</div>';
						$(".comment_sec").append(full_data);
							Command: toastr["success"]("Review added successfully");
						$(".timeago").timeago();

						$("#review").val('');
                        
						$('.review_rating').raty({
							score: function() {
								return $(this).attr('data-score');
							},
							path: base_url+'assets/front/img/',
							numberMax: 5,
							click: function(score, evt) {
								$("#rating").val(score);
							}
						});
					}
					else
					{
						Command: toastr["error"]("Error while adding review");
					}
				}
			});
		}
	});

	// View all reviews
	$(document).on('click',"#view_all_reviews", function(e){
		$(".comment_list li").show();
		$("#view_all_reviews").hide();
	});

	$(".timeago").timeago();

	// Zoom image on product detail page
	$(document).on('click',".zoom img",function(e){
		$("#zoom_img_modal").modal();
	});
	
	$( 'a[href="#"]' ).click( function(e) {
		e.preventDefault();
	} );
	
	
});


function statusChangeCallback(response) {
	// console.log('statusChangeCallback');
	// console.log(response);
	// The response object is returned with a status field that lets the
	// app know the current login status of the person.
	// Full docs on the response object can be found in the documentation
	// for FB.getLoginStatus().
	if (response.status === 'connected') {
	// Logged into your app and Facebook.
	//testAPI();
	} else if (response.status === 'not_authorized') {
		// The person is logged into Facebook, but not your app.
		alert('Please log into this app.');
	} else {
	// The person is not logged into Facebook, so we're not sure if
	// they are logged into this app or not.
	// alert('Please log into Facebook.');
	}
}
	
$('#fb_test').on('click', function(e){
	e.preventDefault();
	var name = $('#pro_name').val();
	var price = $('#prod_sh_price').val();
	var picture = $('#prod_sh_pic').val();
	var spl_cnt = $('#spl_cnt_nm').val();
	var description = $('#description').val();
	var offer_starts_at = $('#offer_stdt').val();
	var offer_ends_at = $('#offer_eddt').val();
	var retailer_name = $('#retailer_nm').val();
	spl_cnt > 1 ? '' : spl_cnt = '';
	var heading = spl_cnt+' '+name+' is available for R'+price+' from '+retailer_name+'. Valid from '+offer_starts_at+' to '+offer_ends_at;
	if(offer_starts_at == '' || typeof offer_starts_at == 'undefined' || offer_ends_at == '' || typeof offer_ends_at == 'undefined' || retailer_name == '' || typeof retailer_name == 'undefined'){
		offer_starts_at = $('#offer_stdt').val();
		offer_ends_at = $('#offer_eddt').val();
		retailer_name = $('#retailer_nm').val();
		heading = name+' is avialable for R'+price;
	}
	FB.ui(
	{
		method: 'feed',
		name: heading,
		link: $(location).attr('href'),
		picture: picture,
		caption: 'Come and use your chance',
		description: description,
		message: 'Come and use your chance',
		hashtag: '#TheBestDeals'
	}, function(response) {
		if (response && !response.error_code) {
			$.ajax({
				url: $("#base_url").val()+'productdetails/add_share_count',
				type: 'POST',
				dataType: 'json',
				data: {
					prod_id: $('#prodid').val()
				},
				success: function(data){
					if(data.result == 1){
						$('#share_count').html(data.message);
					}
				}
			});
		}
	}
	);
});

$(document).on('click','#twitter-share-button',function(e){
	e.preventDefault();
	var name = $('#pro_name').val();
	var price = $('#prod_sh_price').val();
	var picture = $('#prod_sh_pic').val();
	var spl_cnt = $('#spl_cnt_nm').val();
	var description = $('#description').val();
	var offer_starts_at = $('#offer_stdt').val();
	var offer_ends_at = $('#offer_eddt').val();
	var retailer_name = $('#retailer_nm').val();
	spl_cnt > 1 ? '' : spl_cnt = '';
	var heading = spl_cnt + ' ' + name+' is available for R'+price+' from '+retailer_name+'. Valid from '+offer_starts_at+' to '+offer_ends_at;
	if(offer_starts_at == '' || typeof offer_starts_at == 'undefined' || offer_ends_at == '' || typeof offer_ends_at == 'undefined' || retailer_name == '' || typeof retailer_name == 'undefined'){
		offer_starts_at = $('#offer_stdt').val();
		offer_ends_at = $('#offer_eddt').val();
		retailer_name = $('#retailer_nm').val();
		heading = name+' is avialable for R'+price;
	}
	window.open('https://twitter.com/intent/tweet?text='+heading+'&hashtags=TBD', '_blank');
});

window.fbAsyncInit = function() {
	FB.init({
		appId      : '833131023451351',
		xfbml      : true,
		version    : 'v2.5'
	});
};
(function(d, s, id){
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) {
		return;
	}
	js = d.createElement(s);
	js.id = id;
	js.src = "//connect.facebook.net/en_US/sdk.js";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

function init_slider(){
	if($('#slider').length > 0){
		var range = document.getElementById('slider');

		range.style.width = '96%';
		range.style.margin = '0 auto 30px';

		noUiSlider.create(range, {
			start: [ $('#init_dist').val() ], // 4 handles, starting at...
			//margin: 300, // Handles must be at least 300 apart
			//limit: 600, // ... but no more than 600
			connect: true, // Display a colored bar between the handles
			direction: 'ltr', // Put '0' at the bottom of the slider
			orientation: 'horizontal', // Orient the slider vertically
			behaviour: 'tap-drag', // Move handle on tap, bar is draggable
			step: 1,
			tooltips: true,
			range: {
				'min': 0,
				'max': 1000
			},
			format: wNumb({
				decimals: 0,
				//thousand: '.',
				postfix: 'KM'
			})
		/*,
	pips: { // Show a scale with the slider
						mode: 'steps',
						stepped: true,
						density: 4
					}*/
		});
		
		
		marginSlider = document.getElementById('slider');
		var mapDist = document.getElementById('us_radius_hd');
		marginSlider.noUiSlider.on('update', function ( values, handle ) {
			var rad_val = parseInt(((values[handle].split('KM')[0])*1000));
			$('#us_radius_hd').val(rad_val);
		});
		
			
	}
}

var mapAuto;
function initAutocomplete() {
	if($('#street_address').length > 0){
		geocoder = new google.maps.Geocoder();
		var latlng = new google.maps.LatLng($('#us_lat_pref').val(), $('#us_lng_pref').val());

		geocoder.geocode({
			'latLng': latlng
		}, function(results, status) {
			if(status == google.maps.GeocoderStatus.OK) {
				if(results[0]) {
					console.log(results[0]);
					var addr = results[0].formatted_address;
					addr = $.trim(addr.substring(addr.indexOf(", ") + 1));
					$('#street_address').val(addr);
				} 
			} 
		});
		
		var options = {
			types: ['geocode']
		};
		mapAuto = new google.maps.places.Autocomplete((document.getElementById('street_address')),options);

		google.maps.event.addListener(mapAuto, 'place_changed', function () {
			var place = mapAuto.getPlace();
			$('#us_lat_pref').val(place.geometry.location.lat());
			$('#us_lng_pref').val(place.geometry.location.lng());
		});
	}
		
}

$('body').on('click','#get_prod_comparison',function(e){
	e.preventDefault();
	$('#prod_comp_div').html('');
	$.LoadingOverlay("show");
	var lat = $('#us_lat_pref').val();
	var lng = $('#us_lng_pref').val();
	var prodId = $('#prodid').val();
	var dist = ($('#us_radius_hd').val()/1000);
	$.ajax({
		url: base_url+'productdetails/compare_product_user',
		type: 'POST',
		dataType: 'json',
		data: {
			lat: lat,
			lng: lng,
			prodId: prodId,
			dist: dist
		},
		success: function(data){
			$.LoadingOverlay("hide");
			if(data.result == 1){
				$('#prod_comp_div').html(data.message);
			}
			else{
				Command: toastr["error"](data.message);
			}
		},
		error: function(){
			$.LoadingOverlay("hide");
		}
	});
});
function init_accordion(){
	$( ".accordion-div" ).accordion({
		collapsible: true,
		active: false
	});
}
function init_tab(){
	$( "#tabs" ).tabs();
}

function showChart(){
	var prodId = $('#prodid').val();
	$.ajax({
		url: base_url+'productdetails/get_chart_details',
		type: 'POST',
		dataType: 'json',
		data: {
			prodId: prodId
		},
		success: function(data){
			if(data.result == 1){
				var ctx = document.getElementById('myChart').getContext('2d');
				var myLineChart = new Chart(ctx, {
					type: 'line',
					data: {
						labels: data.day,
						datasets: [{
							label: 'Price Change',
//							lineTension: 0,
							data: data.price,
							store: data.store,
							backgroundColor: "rgba(255,255,255,0.4)",
							borderColor: "rgba(0, 183, 255,0.4)",
							pointRadius: 5,
							pointBorderColor: "rgba(255,0,0,0.4)",
							pointBackgroundColor: "rgba(255,0,0,0.4)",
						}]
					},
					options: {
						tooltips: {
							enabled: true,
							mode: 'single',
							callbacks: {
								label: function(tooltipItems, data) { 
									var multistringText = ['Price: '+tooltipItems.yLabel];
									multistringText.push('Retailer: '+data.datasets[0].store[tooltipItems.index].retailer);
									multistringText.push('Store: '+data.datasets[0].store[tooltipItems.index].store);
									//return 'Price: '+tooltipItems.yLabel+', Retailer: '+data.datasets[0].store[tooltipItems.index].retailer+', Store: '+data.datasets[0].store[tooltipItems.index].store;
									return multistringText;
								}
							}
						}
					}
				});
			}
		//console.log(data);
		}
	});
}

