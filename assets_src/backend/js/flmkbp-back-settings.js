if(typeof($uifm) === 'undefined') {
	$uifm = jQuery;
}
var flmbkp_back_settings = flmbkp_back_settings || null;
if(!$uifm.isFunction(flmbkp_back_settings)){
(function($, window) {
 "use strict";  
    
var flmbkp_back_settings = function(){
    var flmbkp_variable = [];
    flmbkp_variable.innerVars = {};
    flmbkp_variable.externalVars = {};
    
    this.initialize = function() {
        //event

     
         
        $(document).on("click","#dbflm_page_settings .btn.btn-primary",function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('ja');
                
                console.log($('#dbflm_page_settings_form').serialize());
                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    dataType: 'json',
                    beforeSend: function() {
                       
                    },
                    data: {
                        'action': 'flmbkp_settings_saveoptions',
                       'page':'flmbkp_page_settings',
                       'flmbkp_security':flmbkp_vars.ajax_nonce,
                       'options':$('#dbflm_page_settings_form').serialize()
                        },
                    success: function(msg) {
               
                    if(msg.success){
                        alert('User Roles saved successfully');
                    }
                       
                    },
              
                });
                
                
                  return false;
          });
        
    };
    
 
    
    
};
window.flmbkp_back_settings = flmbkp_back_settings = $.flmbkp_back_settings = new flmbkp_back_settings();

flmbkp_back_settings.initialize();

})($uifm,window);
} 

