var processFinished = true;
var progressLastRun = false;
var lastPrintedMessage = '';
var initialRun = 1;
var selected_paths = [];
var lastZipResponse = {};
var isPaused = false;
var cwd = '';
var dir_listing;
var nextstep='';
var currentOption='';
var flmbkp_slug='';
var tmp_var1;
var url_redirect_afterbkp;

var flmbkp_counter_qu={};

var dirListingObj;
window.dirListingObj = dirListingObj;

var progressLogObj;
window.progressLogObj = progressLogObj;

var progressBarObj;
window.progressBarObj = progressBarObj = {};

var progressBarMsgObj;
window.progressBarMsgObj = progressBarMsgObj = {}; 

$uifm(document).ready(function($) {
    dirListingObj = $('#flmbkp_directoryListing');
progressLogObj = $('#flmbkp_processLog');  

}); 

$uifm(document).ready(function($) {
    
    //adding new class
    $(document.body).addClass('sfdclauncher');
    
     //confirmation
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
});

;(function( $ ){
    

 
})( jQuery );

jQuery(function($){
    $.fn.removeCss = function() {
  var removedCss = $.makeArray(arguments);
  return this.each(function() {
    var e$ = $(this);
    var style = e$.attr('style');
    if (typeof style !== 'string') return;
    style = $.trim(style);
    var styles = style.split(/;+/);
    var sl = styles.length;
    for (var l = removedCss.length, i = 0; i < l; i++) {
      var r = removedCss[i];
      if (!r) continue;
      for (var j = 0; j < sl;) {
        var sp = $.trim(styles[j]);
        if (!sp || (sp.indexOf(r) === 0 && $.trim(sp.substring(r.length)).indexOf(':') === 0)) {
          styles.splice(j, 1);
          sl--;
        } else {
          j++;
        }
      }
    }
    if (styles.length === 0) {
      e$.removeAttr('style');
    } else {
      e$.attr('style', styles.join(';'));
    }
  });
};

   
      
});
 