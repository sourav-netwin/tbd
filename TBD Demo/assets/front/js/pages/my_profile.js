var hash = window.location.hash;
hash && $('ul.nav a[href="' + hash + '"]').tab('show');
console.log(window.location.hash);
if(hash) {

	var url = window.location.hash;
	var hash = url.substring(url.indexOf('#') +1);

	// Ajax call to the tab
	$.ajax({
		url : $("#base_url").val()+'my_profile/get_'+hash,
		method : 'POST',
		dataType: 'JSON',
		success : function(data)
		{
			$('#'+hash+'  .prd_list_wrap .row').html(data.view);
		}
	});
}
//$('#myTab').tabCollapse();

$('#myTab a').click(function (e) {
	$(this).tab('show');

	var target =  $(this).attr('href');

	var scrollmem = $('body').scrollTop();
	//        window.location.hash = this.hash;


	$('html,body').scrollTop(scrollmem);



});


$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
	e.target // newly activated tab
	e.relatedTarget // previous active tab

	var activated_tab = e.target;
	window.location.hash = this.hash;
	var url = activated_tab['href'];
	url = url.replace('-collapse','');
	var hash = url.substring(url.indexOf('#') +1);

	// Ajax call to the tab
	$.ajax({
		url : $("#base_url").val()+'my_profile/get_'+hash,
		method : 'POST',
		dataType: 'JSON',
		success : function(data)
		{
			$('#'+hash+'  .prd_list_wrap .row').html(data.view);
		}
	});
});


//Get Wishlist Details
$(document).on('click',".wishlist_detail", function (e) {

	$.LoadingOverlay("show");

	var wishlist_id = $(this).attr("data-id");

	// Ajax call to the tab
	$.ajax({
		url : $("#base_url").val()+'my_profile/get_wishlist_detail/'+wishlist_id,
		method : 'POST',
		dataType: 'JSON',
		success : function(data)
		{
			$.LoadingOverlay("hide");
			$('#wishlists .prd_list_wrap .row').html(data.view);
		}
	});
});

//Delete  wishlist
$(document).on('click',".remove_wishlist", function (e) {

	$.LoadingOverlay("show");

	var wishlist_id = $(this).attr("data-id");

	// Ajax call to the tab
	$.ajax({
		url : $("#base_url").val()+'my_profile/delete_list/'+wishlist_id,
		method : 'POST',
		dataType: 'JSON',
		success : function(data)
		{
			$.LoadingOverlay("hide");
			$('#wishlists .prd_list_wrap .row').html(data.view);
		}
	});
});

//Delete product from wishlist
$(document).on('click',".remove_wishlist_product", function (e) {

	$.LoadingOverlay("show");

	var wishlist_product_id = $(this).attr("data-id");

	var wishlist_id = $(this).attr("data-wishlist-id");

	// Ajax call to the tab
	$.ajax({
		url : $("#base_url").val()+'my_profile/delete_wishlist_product/'+wishlist_id+'/'+wishlist_product_id,
		method : 'POST',
		dataType: 'JSON',
		success : function(data)
		{
			$.LoadingOverlay("hide");
			$('#wishlists .prd_list_wrap .row').html(data.view);
		}
	});
});

//Delete all favorites
$(document).on('click',".remove_favorites", function (e) {

	$.LoadingOverlay("show");

	// Ajax call to the tab
	$.ajax({
		url : $("#base_url").val()+'my_profile/delete_favorites',
		method : 'POST',
		dataType: 'JSON',
		success : function(data)
		{
			$.LoadingOverlay("hide");
			$('#favorites .prd_list_wrap .row').html("");
		}
	});
});

//Delete all price alerts
$(document).on('click',".remove_price_alert", function (e) {

	$.LoadingOverlay("show");

	// Ajax call to the tab
	$.ajax({
		url : $("#base_url").val()+'my_profile/delete_pricealerts',
		method : 'POST',
		dataType: 'JSON',
		success : function(data)
		{
			$.LoadingOverlay("hide");
			$('#pricealerts .prd_list_wrap .row').html("");
		}
	});
});


//Delete all notifications
$(document).on('click',".remove_notifications", function (e) {

	$.LoadingOverlay("show");

	// Ajax call to the tab
	$.ajax({
		url : $("#base_url").val()+'my_profile/delete_all_notifications',
		method : 'POST',
		dataType: 'JSON',
		success : function(data)
		{
			$.LoadingOverlay("hide");
			$('#notification .prd_list_wrap .row').html("");
		}
	});
});

//Delete single notification
$(document).on('click',".delete_notification", function (e) {

	$.LoadingOverlay("show");

	var wishlist_id = $(this).attr("data-id");

	// Ajax call to the tab
	$.ajax({
		url : $("#base_url").val()+'my_profile/delete_notification/'+wishlist_id,
		method : 'POST',
		dataType: 'JSON',
		success : function(data)
		{
			$.LoadingOverlay("hide");
			$('#notification .prd_list_wrap .row').html(data.view);
		}
	});
});

//Add a new wishlist
$(document).on('click','#add_to_list_btn',function(e){
	$("#add_to_list_form").submit();
});

// Data loading text to be displayed only if client side validations pass
$('#add_to_list_form').submit(function () {

	//    if($('#new_list').val()!="") {
	$('#add_to_list_btn').button('loading');
	return true;
//    }

});

$(function(){
	init_location_pick();
	init_slider();
});

function init_location_pick(){
	if($('#location-div').length > 0){
		$('#location-div').locationpicker({
			location: {
				latitude: $('#set_lat').val(),
				longitude: $('#set_lon').val()
			},
			radius: ($('#set_dist').val()*1000),
			inputBinding: {
				latitudeInput: $('#us_latitude'),
				longitudeInput: $('#us_longitude'),
				radiusInput: $('#us_radius_hd'),
				locationNameInput: $('#us_address')
			},
			 mapOptions: {
				 markerInCenter: true
			 },
			enableAutocomplete: true,
			addressFormat: 'street_address'
		});
	}
}
var set = 0;
function init_slider(){
	if($('#slider').length > 0){
		var range = document.getElementById('slider');

		range.style.width = '96%';
		range.style.margin = '0 auto 30px';

		noUiSlider.create(range, {
			start: [ $('#set_dist').val() ], // 4 handles, starting at...
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
				'max': 100
			},
			format: wNumb({
				decimals: 0,
				//thousand: '.',
				postfix: 'KM'
			})
		});
		
		
		marginSlider = document.getElementById('slider');
		var mapDist = document.getElementById('us_radius_hd');
		marginSlider.noUiSlider.on('update', function ( values, handle ) {
			if(set != 0){
				var rad_val = parseInt(((values[handle].split('KM')[0])*1000));
				$('#us_radius_hd').val(rad_val);
				$('#location-div').locationpicker({
					radius: rad_val,
					location: {
						latitude: $('#us_latitude').val(),
						longitude: $('#us_longitude').val()
					}
				});
			}
			set = 1;
		});
		
			
	}
}