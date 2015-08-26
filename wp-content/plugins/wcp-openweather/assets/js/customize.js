(function($) {  
    $(document).on('focus', '.rpw-gm-city', function() {
        $('.pac-container').css('z-index', '1000000');    
    });

    var options = {
      types: ['(cities)']
    };            

    $(document).find('.rpw-gm-city').each(function() {
        if (typeof this.rpwOwner == 'undefined') {
            $(this).on('change', function() {
                var form = $(this).closest('.form');
                if (form.length == 0) {
                    form = $(this).closest('form');
                }
                $(form).find('.rpw-gm-city-data').val('');
            });
            var autocomplete = new google.maps.places.Autocomplete($(this).get(0), options);           
            autocomplete.rpwOwner = this;
            this.rpwOwner = autocomplete;
            var gm_lib = new RPwGmLib();
            google.maps.event.addListener(autocomplete, 'place_changed', gm_lib.placeChanged);                
        }
    });  
})(jQuery);