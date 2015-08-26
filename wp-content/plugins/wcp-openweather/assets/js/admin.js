(function($) {  
    $(document).ready(function() { 
        $('.agp-color-picker').wpColorPicker(); 
        
        if( /Android|iPhone|iPad|iPod|webOS|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {   
            $("#rpw-constructor-box").colorbox({inline:true, width:"96%"});
        } else {
            $("#rpw-constructor-box").colorbox({inline:true, width:"50%"});
        }        
    });
})(jQuery);


