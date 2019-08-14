if(typeof($uifm) === 'undefined') {
	$uifm = jQuery;
}
var flmbkp_back_fm = flmbkp_back_fm || null;
if(!$uifm.isFunction(flmbkp_back_fm)){
(function($, window) {
 "use strict";  
    
var flmbkp_back_fm = function(){
    var flmbkp_variable = [];
    flmbkp_variable.innerVars = {};
    flmbkp_variable.externalVars = {};
    
    this.initialize = function() {
        //event
       
        $(document).on("change",".uiform-editing-header select",function(e) {
            flmbkp_back_fm.header_options_submit();
          });  
    };
 
    this.header_options_submit = function(){
       
        console.log('change  submit');
        $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                       'action': 'flmbkp_header_options',
                       'page':'flmbkp_file_manager',
                       'flmbkp_security':flmbkp_vars.ajax_nonce,
                       'options':$('#flmbkp_header_opt').serialize()
                        },
                    success: function(msg) {
                       flmbkp_back_helper.redirect_tourl(msg.url);
                    }
                });
        
    };
    
    
};
window.flmbkp_back_fm = flmbkp_back_fm = $.flmbkp_back_fm = new flmbkp_back_fm();

flmbkp_back_fm.initialize();

})($uifm,window);
} 