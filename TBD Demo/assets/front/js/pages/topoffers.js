$(document).ready(function(){
	var base_url = $("#base_url").val();

	$(document).on('click',"#all_supermarkets", function (e) {
		var total_display = $(this).attr('data-display');
		$.LoadingOverlay("show");

		$.ajax({
			url : $("#base_url").val()+'topoffers/get_retailers',
			data : {
				display : total_display
			},
			method : 'POST',
			success : function(data)
			{
				$.LoadingOverlay("hide");
				$("#top_offer_retailers").html( data );
				if( total_display == 'all' )
				{
					$("#all_supermarkets").attr('data-display', 'minimize');
					$("#all_supermarkets").html('Minimize');
				}
				else
				{
					$("#all_supermarkets").attr('data-display', 'all');
					$("#all_supermarkets").html('See all supermarkets');
				}
			}
		});
	});

	// Rating stars to be displayed on top offers page
	$('span.product_rating').raty({
		score: function() {
			return $(this).attr('data-score');
		},
		readOnly: true,
		path: base_url+'assets/front/img/',
		numberMax: 5
	});

	// Change user preference on click of retailers also reload page to display offers by selected retailer
	$(document).on('click',"#top_offer_retailers li a", function (e) {
		var retailer_id = $(this).attr("data-retailer-id");

		update_user_preference( retailer_id );
	});

	// Change user preference on click of retailers drop down also reload page to display offers by selected retailer
	$(document).on("click", "#retailer_select a", function(e){
		$("#retailer_select_btn").html( $(this).html()+'<span class="caret"></span>' );
		var retailer_id = $(this).attr("data-retailer-id");
		$("#retailer_select_btn").attr("data-retailer-id", retailer_id);

		update_user_preference( retailer_id );
	})

	// Change user preference on click of stores drop down also reload page to display offers by selected store
	$(document).on('click','#store_select a', function (e){
		$("#store_select_btn").html( $(this).html()+'<span class="caret"></span>' );
		var store_id = $(this).attr("data-store-id");
		var retailer_id = $("#retailer_select_btn").attr("data-retailer-id");
		$("#store_select_btn").attr("data-store-id", store_id);

		update_user_preference( retailer_id, store_id );
	});

	// Display products as per range selected by user
	$(document).on('change','#price_range_filter .checkbox input', function (e){
		$.LoadingOverlay("show");

		var price_range = [];
		$('#price_range_filter .checkbox input:checked').each(function( index, item ) {
			var obj = {};
			obj['max'] = $(item).attr('data-max');
			obj['min'] = $(item).attr('data-min');

			price_range.push(obj);
		});

		// Price range filter for products
		if( $("#category_id").length && $("#category_type").length )
		{
			$.ajax({
				url : $("#base_url").val()+'productslist/search_products',
				data : {
					price_range: price_range, 
					category_id: $("#category_id").val(), 
					category_type: $("#category_type").val()
				},
				method : 'POST',
				dataType: 'JSON',
				success : function(data)
				{
					$.LoadingOverlay("hide");

					$('.prd_list_wrap .row').html( data.view );
					$('#last_product').val(data.last_product);
				}
			});
		}
		else // Price range filter for top offer
		{
			$.ajax({
				url : $("#base_url").val()+'topoffers/search_products',
				data : {
					price_range: price_range
				},
				method : 'POST',
				dataType: 'JSON',
				success : function(data)
				{
					$.LoadingOverlay("hide");

					$('.prd_list_wrap .row').html( data.view );
					$('#last_offer_product').val(data.last_product);
				}
			});
		}
	});
	$(document).on('change','#dist_range_filter .radio input', function (e){
		$.LoadingOverlay("show");
		$('#prod_comp_div').html('');
		var dist_range = [];
		if($('#dist_range_filter .radio input:checked').length > 0){
			var obj = {};
			obj['max'] = $(this).val();
			obj['min'] = 0;

			dist_range.push(obj);
		}
		else{
			var obj = {};
			obj['max'] = $('#init_dist').val();
			obj['min'] = 0;

			dist_range.push(obj);
		}
		        
		var prodId = $('#prodid').val();
		$.ajax({
			url: base_url+'productdetails/compare_product_user',
			type: 'POST',
			dataType: 'json',
			data: {
				prodId: prodId,
				dist_range: dist_range
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
	$(document).on('change','#top_dist_range_filter .radio input', function (e){
		e.preventDefault();
		e.stopImmediatePropagation();
		$.LoadingOverlay("show");
		var dist_range = [];
		if($('#top_dist_range_filter .radio input:checked').length > 0){
			var obj = {};
			obj['max'] = $(this).val();
			obj['min'] = 0;

			dist_range.push(obj);
		}
		else{
			var obj = {};
			obj['max'] = $('#init_dist').val();
			obj['min'] = 0;

			dist_range.push(obj);
		}
		
		$('#topoffer-container').html('');
		$.ajax({
			url: base_url+'topoffers/get_top_offer_by_distance',
			type: 'POST',
			dataType: 'json',
			data: {
				dist_range: dist_range
			},
			success: function(data){
				$.LoadingOverlay("hide");
				if(data.result == 1){
					$('#topoffer-container').html(data.message);
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
	
	$('#top_dist_range_filter .radio input').mousedown(function(e){
		var $self = $(this);
		if( $self.is(':checked') ){
			var uncheck = function(){
				setTimeout(function(){
					$self.removeAttr('checked');
					$self.change();
				},0);
			};
			var unbind = function(){
				$self.unbind('mouseup',up);
			};
			var up = function(){
				uncheck();
				unbind();
			};
			$self.bind('mouseup',up);
			$self.one('mouseout', unbind);
		}
	});
	$('#dist_range_filter .radio input').mousedown(function(e){
		var $self = $(this);
		if( $self.is(':checked') ){
			var uncheck = function(){
				setTimeout(function(){
					$self.removeAttr('checked');
					$self.change();
				},0);
			};
			var unbind = function(){
				$self.unbind('mouseup',up);
			};
			var up = function(){
				uncheck();
				unbind();
			};
			$self.bind('mouseup',up);
			$self.one('mouseout', unbind);
		}
	});

	// Mark or Unmark a product as favourite
	$(document).on('click','.fav_product', function (e){
                e.preventDefault();
		e.stopImmediatePropagation();
		$.LoadingOverlay("show");

		var product_id = $(this).attr('data-product-id');
                var special_id = $(this).attr('data-special-id');
                var list_item = $(this).attr('data-fav-item');
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
				$('.fav_product').blur();
				if( is_fav == 1 )
				{
					element.parent().removeClass('is-added');
					element.parent().addClass('is-not-added');
					element.attr('data-is-fav', 0);
					Command: toastr["success"]("Product unmarked as favourite");
                                        
                                        var pgClassName = $('#pgClassName').val();
                                        if( pgClassName == 'my_profile')
                                        {   // Remove item flow list if unmark favourite
                                            list_item = list_item.replace("item-", "");
                                            $('#fav-'+list_item).remove();
                                        }
				}
				else
				{
					element.parent().removeClass('is-not-added');
					element.parent().addClass('is-added');					
					element.attr('data-is-fav', 1);
					Command: toastr["success"]("Product marked as favourite");
				}
			}
		});
	});

	// Show more products
	$(document).on('click','#show_more', function (e){
		$.LoadingOverlay("show");
		e.preventDefault();
		e.stopImmediatePropagation();

		if( $("#last_product").length )
		{
			var last_product = $('#last_product').val();
			var show_offer = $('#offer_select').is(":checked") ? 1 : 0;
			var price_range = [];
			$('#price_range_filter .checkbox input:checked').each(function( index, item ) {
				var obj = {};
				obj['max'] = $(item).attr('data-max');
				obj['min'] = $(item).attr('data-min');

				price_range.push(obj);
			});

			$.ajax({
				url : $("#base_url").val()+'productslist/show_more_products',
				data : {
					last_product: last_product, 
					category_id: $("#category_id").val(), 
					category_type: $("#category_type").val(), 
					show_offer: show_offer, 
					price_range: price_range
				},
				method : 'POST',
				dataType: 'JSON',
				success : function(data)
				{
					$.LoadingOverlay("hide");

					$('.prd_list_wrap .row').append( data.view );
					$('#last_product').val(data.last_product);

					if( data.count == $(".prd_wrap").length )
						$("#show_more").hide();
				}
			});
		}
		else
		{
			var last_offer_product = $('#last_offer_product').val();

			$.ajax({
				url : $("#base_url").val()+'topoffers/show_more_products',
				data : {
					last_offer_product: last_offer_product
				},
				method : 'POST',
				dataType: 'JSON',
				success : function(data)
				{
					$.LoadingOverlay("hide");

					$('.prd_list_wrap .row').append( data.view );
					$('#last_offer_product').val(data.last_product);

					if( data.count == $(".prd_wrap").length )
						$("#show_more").hide();
				}
			});
		}
	});

	$(document).on('change',"#offer_select", function(e){
		$.LoadingOverlay("show");

		var show_offer = $('#offer_select').is(":checked") ? 1 : 0;
		var price_range = [];
		$('#price_range_filter .checkbox input:checked').each(function( index, item ) {
			var obj = {};
			obj['max'] = $(item).attr('data-max');
			obj['min'] = $(item).attr('data-min');

			price_range.push(obj);
		});

		$.ajax({
			url : $("#base_url").val()+'productslist/show_offer_products',
			data : {
				show_offer: show_offer, 
				price_range: price_range, 
				category_id: $("#category_id").val(), 
				category_type: $("#category_type").val(),
				search_text:$("#search_text").val()
			},
			method : 'POST',
			dataType: 'JSON',
			success : function(data)
			{
				$.LoadingOverlay("hide");

				$('.prd_list_wrap .row').html( data.view );
				$('#last_product').val(data.last_product);

				$('#product_count').text( data.count );
				if( data.count == $(".prd_wrap").length )
					$("#show_more").hide();

				// Rating stars to be displayed on top offers page
				$('span.product_rating').raty({
					score: function() {
						return $(this).attr('data-score');
					},
					readOnly: true,
					path: base_url+'assets/front/img/',
					numberMax: 5
				});
			}
		});
	});
});

// function to update user preferences
function update_user_preference( retailer_id, store_id )
{
	$.LoadingOverlay("show");

	$.ajax({
		url : $("#base_url").val()+'topoffers/update_user_preference',
		data : {
			retailer_id : retailer_id, 
			store_id : store_id
		},
		method : 'POST',
		success : function(data)
		{
			if( data == 1 )
			{
				//                location.reload();
				window.location = window.location.href;
			}
			else
			{
				$.LoadingOverlay("hide");
					//Error toast message
					Command: toastr["error"]("Error while changing retailer/store");
			}
		}
	});
}