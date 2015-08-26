
jQuery(document).ready(function($){
    if($(".ch-fes-message").length) $(".ch-fes-message").focus();
    $(".ch-fes-form #ev-from").datepicker({ dateFormat: 'yy-mm-dd' });
    $(".ch-fes-form #ev-from").datepicker({ dateFormat: 'yy-mm-dd' });
    $(".ch-fes-form #ev-to").datepicker({ dateFormat: 'yy-mm-dd' });
    $(".ch-fes-form input.start-time").datepicker({ dateFormat: 'yy-mm-dd' });
    $(".ch-fes-form input.end-time").datepicker({ dateFormat: 'yy-mm-dd' });
    $(".ch-fes-form #chronosly_category-add-toggle").click(function(){
        $(".ch-fes-form .ch-fes-category-box").slideToggle("slow");
        $(".ch-fes-form #chronosly_category-add").slideToggle("slow");
        setTimeout(function (){
            $('html, body').animate({
                scrollTop: $(".ch-fes-form #chronosly_category-add").parents(".ch-fes-box").offset().top-100
            }, 1000);
        }, 200);
        $(".ch-fes-form #chronosly_category-add").parents(".ch-fes-box").toggleClass("open");
    });
    $(".ch-fes-form #chronosly_organizer-add-toggle").click(function(){
        $(".ch-fes-form .ch-fes-organizer-box").slideToggle("slow");
        $(".ch-fes-form #chronosly_organizer-add").slideToggle("slow");
        setTimeout(function (){
            $('html, body').animate({
                scrollTop:  $(".ch-fes-form #chronosly_organizer-add").parents(".ch-fes-box").offset().top-100
            }, 1000);
        }, 200);
        $(".ch-fes-form #chronosly_organizer-add").parents(".ch-fes-box").toggleClass("open");
    });
    $(".ch-fes-form #chronosly_place-add-toggle").click(function(){
        $(".ch-fes-form .ch-fes-place-box").slideToggle("slow");
        $(".ch-fes-form #chronosly_place-add").slideToggle("slow");
        setTimeout(function (){
                $('html, body').animate({
                    scrollTop:  $(".ch-fes-form #chronosly_place-add").parents(".ch-fes-box").offset().top-100
                }, 1000);
            } , 200);
        $(".ch-fes-form #chronosly_place-add").parents(".ch-fes-box").toggleClass("open");
    });
});