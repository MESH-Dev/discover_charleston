
jQuery(document).ready(function(){
    ch_filter_init();

});


function ch_filter_init(){
    jQuery(".ch-fas-form .less").hide();
    jQuery(".ch-fas-form .more").unbind("click").click(function(){
        jQuery(this).parent().find(".noshow").show();
        jQuery(this).parent().find(".less").show();
        jQuery(this).hide();
    });
    jQuery(".ch-fas-form .less").unbind("click").click(function(){
        jQuery(this).parent().find(".noshow").hide();
        jQuery(this).parent().find(".more").show();
        jQuery(this).hide();
    });
    jQuery(".fas-select").unbind("click").click(function(){
        jQuery(this).parent().toggleClass("open");
        jQuery(this).next().slideToggle();
    })
    jQuery(".ch-field-date").datepicker({ dateFormat: 'yy-mm-dd' });
    // jQuery( ".ch-fas-form .slider-range" ).slider({
    //     range: true,
    //     min: 0,
    //     max: translated.pricemax,
    //     slide: function( event, ui ) {
    //         jQuery( ".ch-fas-form .ch-field-range-min" ).val( ui.values[ 0 ]);
    //         jQuery( ".ch-fas-form .ch-field-range-max" ).val( ui.values[ 1 ]);
    //         jQuery(".ch-fas-form").submit();
    //     }
    // });
    // jQuery( ".ch-fas-form .ch-field-range-min").unbind("change").change(function(){
    //     jQuery( ".ch-fas-form .slider-range" ).slider({ values: [ jQuery(this).val(),jQuery( ".ch-fas-form .ch-field-range-max" ).val() ] });

    // });
    // jQuery( ".ch-fas-form .ch-field-range-max").unbind("change").change(function(){
    //     jQuery( ".ch-fas-form .slider-range" ).slider({ values: [ jQuery( ".ch-fas-form .ch-field-range-min" ).val() ,jQuery(this).val() ] });

    // });
    jQuery(".ch-fas-form input").unbind("change").change(function(){jQuery(".ch-fas-form").submit();});
    jQuery(".ch-fas-form select").unbind("change").change(function(){jQuery(".ch-fas-form").submit();});
    jQuery(".ch-fas-form").unbind("submit").submit(function(e){
        jQuery(".chronosly-content-block").html(jQuery(".ch-fas-container .ch-spinner").clone());
        e.preventDefault();
        ch_filter(jQuery(this), 0);
        return false;
    });
}

function ch_filter(form, navigate){

    var args = {};
    if(navigate){
  
       form = jQuery(form).parents(".chronosly-closure").find(".ch-fas-form");
    }

    form.find("input").each(function(){
        if(jQuery(this).val()) {
            if(jQuery(this).attr("type") == "checkbox" && jQuery(this).is(":checked")){
                if(args[jQuery(this).attr("name").replace("[]", "")]) {
                    args[jQuery(this).attr("name").replace("[]", "")] += ","+jQuery(this).val();
                }
                else {
                    args[jQuery(this).attr("name").replace("[]", "")] = jQuery(this).val();
                }


            }
            else if(jQuery(this).attr("type") != "checkbox" ){
                args[jQuery(this).attr("name")] = jQuery(this).val();
            }
        }
    });
    form.find("select").each(function(){
        args[jQuery(this).attr("name")] = jQuery(this).val();
    });
    args["action"] = "chronosly_filter_and_sort";
    if(args["type"] == "calendar") {
        var id = form.parents(".ch_js_loader").attr("class").replace("ch_js_loader id", "");
        args["calendarid"] = id;

    }

     if(navigate){
       jQuery.each(navigate,function(key, val){
        args[key] = val;
       });
    }

    jQuery.post(translated.ajaxurl,args,function(data){
    // console.log(jQuery(data).find(".ch-fas-nav").html());
       jQuery(".chronosly-content-block").html(jQuery(data).find(".chronosly-content-block").html());
       jQuery(".ch-header").html(jQuery(data).find(".ch-header").html());
       jQuery("input[name='from']").val(jQuery(data).find(".ch_from").html());
       jQuery("input[name='to']").val(jQuery(data).find(".ch_to").html());
       jQuery(".ch-fas-nav").html(jQuery(data).find(".ch-fas-nav").html());

        if(args["type"] != "calendar") setTimeout(function(){jQuery(window).load();},500);
        else onready_calendar();

        jQuery(".ch-fas-form").unbind("submit").submit(function(e){
            jQuery(".chronosly-content-block").html(jQuery(".ch-fas-container .ch-spinner").clone());
            e.preventDefault();
            ch_filter(jQuery(this),0);
            return false;
        });
    });
    }

