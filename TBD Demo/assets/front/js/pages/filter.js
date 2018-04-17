var list_count = $("#quick_search_count").val();
for (var i = 0; i < list_count; i++) {
    $(".flexisel_"+i).flexisel({
        visibleItems: 4,
        animationSpeed: 600,
        autoPlay: true,
        autoPlaySpeed: 3000,
        pauseOnHover: true,
        clone:false,
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
}

$(".quicklist_box").keyup(function(e) {
    while($(this).outerHeight() < this.scrollHeight + parseFloat($(this).css("borderTopWidth")) + parseFloat($(this).css("borderBottomWidth"))) {
        $(this).height($(this).height()+1);
    };
});

 //Quick Search

//    $(document).on('click',"#quicklist_button", function (e) {
//        alert("s");
//        if($(".quicklist_box").length && trim($(".quicklist_box").val()).length) {
//            $("#quicklist_form").submit();
//            return true;
//        } else {
//            return false;
//        }
//
//    });
