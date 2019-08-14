if(typeof($uifm) === 'undefined') {
	$uifm = jQuery;
}
var flmbkp_back_general = flmbkp_back_general || null;
if(!$uifm.isFunction(flmbkp_back_general)){
(function($, window) {
 "use strict";  
    
var flmbkp_back_general = function(){
    var flmbkp_variable = [];
    flmbkp_variable.innerVars = {};
    flmbkp_variable.externalVars = {};
    
    this.initialize = function() {
        
    }
    
    this.formslist_search_refresh = function(){
      this.formslist_search_process(0);
    };
    
    this.formslist_search_refresh_save = function(){
      this.formslist_search_process(1);
      
      alert('Filter paramaters saved');
    }
    
    this.formslist_search_process = function(opt_save){
         var tmp_params = $('#zgfm-listform-filter-panel-form').serialize();
      
      $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                       'action': 'flmbkp_fbuilder_formlist_filter',
                       'page':'flmbkp_file_manager',
                       'flmbkp_security':flmbkp_vars.ajax_nonce,
                       'data_filter':tmp_params,
                       'opt_save':opt_save,
                       'opt_offset':$('#uifm_listform_offset_val').val()
                        },
                    success: function(msg) {
                       $('#zgfm-back-listform-maintable-container').html(msg['content']);
                       
                         //confirmation action
                            $(".uiform-confirmation-func-action").click(function (e) {
                                e.preventDefault(); ///first, prevent the action
                                var targetUrl = $(this).attr("href"); ///the original delete call
                                var tmp_callback =$(this).data('dialog-callback'); 
                                ///construct the dialog
                                $("#uiform-confirmation-func-action-dialog").dialog({
                                    autoOpen: false,
                                    title: 'Confirmation',
                                    modal: true,
                                    buttons: {
                                        "OK" : function () {
                                            ///if the user confirms, proceed with the original action
                                           // window.location.href = targetUrl;
                                           $(this).dialog("close");
                                           eval(tmp_callback);

                                        },
                                        "Cancel" : function () {
                                            ///otherwise, just close the dialog; the delete event was already interrupted
                                            $(this).dialog("close");
                                        }
                                    }
                                });

                                //change title
                                $("#uiform-confirmation-func-action-dialog").dialog('option', 'title', $(this).data('dialog-title'));

                                ///open the dialog window
                                $("#uiform-confirmation-func-action-dialog").dialog("open");
                            });
                    }
                });
    }
    
    
};
window.flmbkp_back_general = flmbkp_back_general = $.flmbkp_back_general = new flmbkp_back_general();


})($uifm,window);
} 