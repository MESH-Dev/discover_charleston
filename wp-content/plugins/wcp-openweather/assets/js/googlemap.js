RPwGmLib.prototype = {
    placeChanged : function () {
        var place = this.getPlace();
        if (place.address_components) {
            city = '';
            country = ''
            state = '';

            for (var i = 0; i < place.address_components.length; i++) {
                var component = place.address_components[i];
                if (!city && (jQuery.inArray('locality', component.types) != -1 || jQuery.inArray('administrative_area_level_3', component.types) != -1)) {
                    city = component.long_name;    
                }
                if (jQuery.inArray('administrative_area_level_1', component.types) != -1) {
                    state = component.long_name;    
                }                            
                if (jQuery.inArray('country', component.types) != -1) {
                    country = component.long_name;    
                }
            }

            var form = jQuery(this.rpwOwner).closest('.form');
            if (form.length == 0) {
                form = jQuery(this.rpwOwner).closest('form');
            }

            jQuery(form).find('.rpw-gm-city-data').val('');                        

            if (city) {
                full_name = city;
                city_data = 'city=' + city;

                if (state) {
                    full_name = full_name + ', ' + state;
                    city_data = city_data + '&state=' + state;
                }                                                        

                if (country) {
                    full_name = full_name + ', ' + country;
                    city_data = city_data + '&country=' + country;
                }

                city_data = city_data + '&full_name=' + full_name;
                city_data = city_data + '&lat=' + place.geometry.location.lat();
                city_data = city_data + '&lng=' + place.geometry.location.lng();

                jQuery(this.rpwOwner).val(full_name);
                

                
                jQuery(form).find('.rpw-gm-city-data').val(city_data);
            }                                   
        }
    }
};

function RPwGmLib () {  
};



