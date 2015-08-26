(function() {
    
    tinymce.create('tinymce.plugins.wcpOpenWeather', {
        init : function(ed, url) {
            ed.addButton('wcp_openweather', {
               title : ajax_rpw.tinyMCEButtonTitle,
               image : url+'/ico.png',
               onclick : function() {
                    jQuery('.wcp-constructor-apply').unbind('click');
                    jQuery('.wcp-constructor-apply').bind('click', function(event) {
                        var data = 'action=getWCPShortcode&nonce=' + ajax_rpw.ajax_nonce;
                        data = data + "&" + jQuery('#wcp-openweather-constructor-params').serialize();
                        
                        jQuery.ajax({
                            url: ajax_rpw.ajax_url,
                            type: 'POST' ,
                            data: data,
                            dataType: 'json',
                            cache: false,
                            success: function(data) {
                                if (data['shortcode']) {
                                    ed.execCommand('mceInsertContent', false, data['shortcode']);
                                }
                                jQuery.colorbox.close();
                            },
                            error: function (request, status, error) {
                            }
                        });                               
                    });
                    jQuery("#rpw-constructor-box").click();
               }
            }); 
        },
        createControl : function(n, cm) {
            return null;
        },
        getInfo : function() {
            return {
               longname : "WCP OpenWeather shortcode generator",
               author : 'Webcodin',
               authorurl : 'http://wpdemo.webcodin.com/',
               version : "1.0"
            };
        }
    });
    tinymce.PluginManager.add('wcp_openweather', tinymce.plugins.wcpOpenWeather);

})();