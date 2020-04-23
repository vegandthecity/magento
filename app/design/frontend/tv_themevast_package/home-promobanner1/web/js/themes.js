require([
	'jquery',
	'mage/mage',
	'themevast/owl'
], function ($) {
	'use strict';

  jQuery(document).ready(function ($) {
    var previousScroll = 0;
    $(window).scroll(function(){
        if($(this).scrollTop() > 150 && $(window).width() > 767 ){
            $('.top_menu_wrapper').addClass("header-sticky animated slideInDown");
            } else {
            $('.top_menu_wrapper').removeClass("header-sticky animated slideInDown");
        }
        });
    });

	jQuery(".instagram_list .owl-carousel").owlCarousel({
		autoplay :true,
		items : 6,
		smartSpeed : 500,
		dotsSpeed : 500,
		rewindSpeed : 500,
		nav : false,
		autoplayHoverPause : true,
		dots : false,
		scrollPerPage:true,
		navText: ['<i class="envy-icon envy-icon-left"></i>','<i class="envy-icon envy-icon-right"></i>'],
		margin: 0,
		responsive: {
		0: {
			items: 2,
		},
		480: {
			items:3,
		},
		768: {
			items:3,
		},
		991: {
			items:4,
		},						
		1200: {
			items:5,
		},
    1400: {
      items:5,
    },
    1600: {
      items:6,
    }
	 }
	});

	$(".footer-links .block-title").on("click", function(){
	  $(this).next().toggleClass("show");
	});

	$(".avatar-team .owl-carousel").owlCarousel({
        autoplay :false,
        items : 4,
        smartSpeed : 500,
        dotsSpeed : 500,
        rewindSpeed : 500,
        nav : false,
        autoplayHoverPause : true,
        dots : false,
        scrollPerPage:true,
        margin: 30,
        loop: false,
        responsive: {
        0: {
            items: 1,
        },
        480: {
            items:2,
        },
        768: {
            items:2,
        },
        991: {
            items:3,
        },                      
        1200: {
            items:4,
        }
     }
    });

    jQuery(document).ready(function () {
    jQuery('.about-box .box-drop').children('.drop-content').slideUp();
    jQuery('.about-box .box-drop > h3').on('click', function () {
    jQuery('.about-box .box-drop').children('.drop-content').slideUp();
     var edq = jQuery(this);
     edq.parent().siblings().find('span').removeClass('active');
     edq.parent().siblings().find('span').addClass('notactive');
      if (edq.children('span').hasClass('notactive')) {
          edq.parent().children('.drop-content').slideDown();
          edq.children('span').removeClass('notactive');
          edq.children('span').addClass('active');
      }else if(edq.children('span').hasClass('active')){
         edq.parent().children('.drop-content').slideUp();
         edq.children('span').removeClass('active');
         edq.children('span').addClass('notactive');         
      }else{
        
      }
    });
  });

});