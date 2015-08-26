
jQuery(document).ready(function($){

    $(".place_select").change(function(){
        var val = $(this).find("option:selected").val();
        if(val == 2) $(".place_hide").show();
        else $(".place_hide").hide();
    }).change();
    $(".organizer_select").change(function(){
        var val = $(this).find("option:selected").val();
        if(val == 2) $(".organizer_hide").show();
        else $(".organizer_hide").hide();
    }).change();
});