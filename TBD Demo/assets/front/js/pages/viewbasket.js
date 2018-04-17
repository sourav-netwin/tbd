var base_url = '';
$(document).ready(function(){
	base_url = $('#base_url').val();
	$(document).on('click',".remove_basket", function (e) {
		var product = $(this).attr('data-product');
		$.LoadingOverlay("show");

		$.ajax({
			url : $("#base_url").val()+'viewbasket/remove_from_basket',
			data : {
				product : product
			},
			method : 'POST',
			success : function(data)
			{
				$.LoadingOverlay("hide");
				if( data == 'success' )
				{
					Command: toastr["success"]("Product removed from basket");
				location.reload();
				}
				else
					Command: toastr["error"]("Error while removing product basket");
			}
		});
	});


	// Display retailers with the price
	$(".cart_list").flexisel({
		visibleItems: 3,
		animationSpeed: 600,
		autoPlay: true,
		autoPlaySpeed: 3000,
		pauseOnHover: true,
		clone:true,
		enableResponsiveBreakpoints: true,
		responsiveBreakpoints: {
			mobilexs: {
				changePoint:430,
				visibleItems: 1
			},
			mobile: {
				changePoint:630,
				visibleItems: 2
			},
			portrait: {
				changePoint:768,
				visibleItems: 3
			},
			landscape: {
				changePoint:1040,
				visibleItems: 2
			},
			tablet: {
				changePoint:1200,
				visibleItems: 3
			}
		}
	});
});

$('body').on('change','.basket-pr-change',function(){
	var elm = $(this);
	var count = elm.val();
	var sel_id = elm.attr('data-id');
	var parent = elm.parent().parent().parent();
	var price_main = parent.find('.basket-num .b-main').text();
	var price_sub = parent.find('.basket-num .b-sub').text();
	var price = (price_main+'.'+price_sub)*1;
	if(isNaN(count)){
		elm.val('1');
	}
	else if(count <= 0){
		elm.val('1');
	}
	else if(count > 999){
		elm.val('999');
	}
	$.ajax({
		url: base_url+'viewbasket/update_basket_count',
		type: 'POST',
		dataType: 'json',
		data: {
			sel_id: sel_id,
			count: count
		},
		success: function(data){
			if(data.result == 1){
				var price = data.message+'';
				var price_arr = price.split('.');
				parent.find('.basket-num .b-main').text(price_arr[0]);
				parent.find('.basket-num .b-sub').text(price_arr[1]);
				update_total_basket();
			}
			else{
				Command: toastr["error"](data.message);
			}
		},
		error: function(){
			
		}
	});
});

function update_total_basket(){
	var total = 0;
	$('.basket-num').each(function(){
		var elm = $(this);
		var price_main = elm.find('.b-main').text();
		var price_sub = elm.find('.b-sub').text();
		var price = (price_main+'.'+price_sub)*1;
		total += price;
	});
	total = total+'';
	var total_arr = total.split('.');
	if(typeof total_arr[1] == 'undefined'){
		total_arr[1] = '00';
	}
	else{
		if(total_arr[1].length > 2){
			total_arr[1] = total_arr[1].substring(0, 2);
		}
	}
	$('.basket-tot-num .b-main').text(total_arr[0]);
	$('.basket-tot-num .b-sub').text(total_arr[1]);
}

$('body').on('click','.plus-cart',function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	var elm = $(this);
	var count_elm = elm.parent().find('input');
	var count = parseInt(count_elm.val());
	count += 1;
	count_elm.val(count);
	if(isNaN(count)){
		count_elm.val('1');
	}
	else if(count <= 0){
		count_elm.val('1');
	}
	else if(count > 999){
		count_elm.val('999');
	}
	count_elm.change();
});
$('body').on('click','.minus-cart',function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	var elm = $(this);
	var count_elm = elm.parent().find('input');
	var count = parseInt(count_elm.val());
	count -= 1;
	count_elm.val(count);
	if(isNaN(count)){
		count_elm.val('1');
	}
	else if(count <= 0){
		count_elm.val('1');
	}
	else if(count > 999){
		count_elm.val('999');
	}
	count_elm.change();
});