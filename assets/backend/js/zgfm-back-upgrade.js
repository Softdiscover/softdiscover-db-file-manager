if(typeof($uifm) === 'undefined') {
	$uifm = jQuery;
}
var flmbkp_back_upgrade = flmbkp_back_upgrade || null;
if(!$uifm.isFunction(flmbkp_back_upgrade)){
(function($, window) {
 "use strict";  
    
var flmbkp_back_upgrade = function(){
    var flmbkp_variable = [];
    flmbkp_variable.innerVars = {};
    flmbkp_variable.externalVars = {};
    
    this.initialize = function() {

        let cur_core_arr=rocketform.getUiData('app_ver');
        //if version prev to 3.4.1
        
        //only calculators
        switch(flmbkp_back_helper.versionCompare(String(cur_core_arr),"3.4.1")){
            case 1:
                break;
            case -1:
            case 0:
                    this.upgrade_prev_3_4_1();
                break;
        }
        
    }
    
   this.upgrade_prev_3_4_1 = function(){
       
      
   }
    
};
window.flmbkp_back_upgrade = flmbkp_back_upgrade = $.flmbkp_back_upgrade = new flmbkp_back_upgrade();


})($uifm,window);
} 