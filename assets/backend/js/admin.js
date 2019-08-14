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
if (typeof ($uifm) === 'undefined') {
    $uifm = jQuery;
}
var flmbkp_back_backup = flmbkp_back_backup || null;
if (!$uifm.isFunction(flmbkp_back_backup)) {
    (function ($, window) {
        "use strict";

        var flmbkp_back_backup = function () {
            var flmbkp_variable = [];
            flmbkp_variable.innerVars = {};
            flmbkp_variable.externalVars = {};

            this.initialize = function () {
                //event

                $(document).on("click", "#flmbkp_backup_form button", function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    $(this).prop("disabled", true);

                    flmbkp_back_backup.options_createRec();
                    return false;
                });



            };

            /*
             * create record
             * @returns slug
             */
            this.options_createRec = function () {

                var atLeastOneIsChecked = $('#flmbkp_backup_form input:checked').length > 0;

                if (atLeastOneIsChecked) {
                    //show progress bar
                    $('#flmbkp_progress_graph').show();

                    $.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        dataType: 'json',

                        data: {
                            'action': 'flmbkp_backup_createrec',
                            'page': 'flmbkp_page_backups',
                            'flmbkp_security': flmbkp_vars.ajax_nonce,
                            'options': $('#flmbkp_backup_form').serialize()
                        },
                        success: function (msg) {
                            flmbkp_slug = msg.slug;
                            flmbkp_counter_qu= msg.pending;
                             
                            url_redirect_afterbkp=msg.url_redirect;
                            //showing the loading bar graphs
                            for (var a in flmbkp_counter_qu) {
                                $('#flmbkp_progress_'+flmbkp_counter_qu[a]).show();
                             }
                            
                            flmbkp_back_backup.options_routeNextStep();

                        }
                    });
                } else {
                    $('#flmbkp_backup_form button').prop("disabled", false);
                    alert('Select one option at least');
                }
            };


            /*
             * Delete record
             */
            this.records_delreg = function (rec_id) {

                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        'action': 'flmbkp_backup_delete_records',
                        'page': 'flmbkp_page_backups',
                        'flmbkp_security': flmbkp_vars.ajax_nonce,
                        'rec_id': rec_id
                    },
                    success: function (msg) {
                        $(".sfdc-block1-container a[data-recid='" + rec_id + "']").closest('tr').fadeOut("slow");

                    }
                });
            };


            /*
             * Restore record
             */
            this.records_restore = function (rec_id) {

                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        'action': 'flmbkp_backup_restore_records',
                        'page': 'flmbkp_page_backups',
                        'flmbkp_security': flmbkp_vars.ajax_nonce,
                        'rec_id': rec_id
                    },
                    success: function (msg) {

                        $('#flmbkp_Modal').modal({
                                show: true,
                                backdrop: 'static',
                                keyboard: false
                            });
                            
                        $('#flmbkp_Modal .modal-title').html(msg.modal_title);
                            $('#flmbkp_Modal .modal-body').html(msg.modal_body);    
                    }
                });
            };

            /*
             * route next step
             */
            this.options_routeNextStep = function () {
                
                if(jQuery.isEmptyObject(flmbkp_counter_qu)){
                                nextstep = '';
                                processFinished = true;

                                $('#flmbkp_backup_form').find('button').prop("disabled", true);
                                flmbkp_back_helper.redirect_tourl(url_redirect_afterbkp);
                   
                }else{
                     var first_func = function(obj) {

                                        for (var a in obj) {
                                            return [obj[a],a];
                                        }
                                    };
                                     
   
                        processFinished = true;
                        progressLastRun = false;
                        lastPrintedMessage = '';
                        initialRun = 1;
                        selected_paths = [];
                        lastZipResponse = {};
                        isPaused = false;
                        cwd = '';
                        dir_listing = [];
                        tmp_var1 = first_func(flmbkp_counter_qu);
                        currentOption =tmp_var1[0]; 
                        delete flmbkp_counter_qu[tmp_var1[1]];
                        
                        switch (String(currentOption)) {
                            case 'plugins':
                                progressBarObj[currentOption] = $('#flmbkp_plugins_progress');
                                progressBarMsgObj[currentOption] = $('#flmbkp_plugins_progress_msg');
                                flmbkp_back_backup.options_filebackup();
                                break;
                            case 'themes':
                                progressBarObj[currentOption] = $('#flmbkp_themes_progress');
                                progressBarMsgObj[currentOption] = $('#flmbkp_themes_progress_msg');
                                flmbkp_back_backup.options_filebackup();
                                break;
                            case 'uploads':
                                progressBarObj[currentOption] = $('#flmbkp_uploads_progress');
                                progressBarMsgObj[currentOption] = $('#flmbkp_uploads_progress_msg');
                                flmbkp_back_backup.options_filebackup();
                                break;
                            case 'others':
                                progressBarObj[currentOption] = $('#flmbkp_others_progress');
                                progressBarMsgObj[currentOption] = $('#flmbkp_others_progress_msg');
                                flmbkp_back_backup.options_filebackup();
                                break;
                            case 'database':
                                progressBarObj[currentOption] = $('#flmbkp_database_progress');
                                progressBarMsgObj[currentOption] = $('#flmbkp_database_progress_msg');
                                flmbkp_back_backup.options_filebackup();
                                break;
                            default:
                                console.log('there is no option');

                        }
                }
                
                
               
            };

            /*
             * download files 
             */

            this.options_downloadFiles = function (file) {

                $("body").append("<iframe src='" + ajaxurl + "?action=flmbkp_backup_downloadfile&page=flmbkp_page_backups&flmbkp_security=" + flmbkp_vars.ajax_nonce + "&flm_file=" + file + "' style='display: none;' ></iframe>");

            };
            /*
             * 
             * backup process
             */
            this.options_filebackup = function () {

                isPaused = false;
                selected_paths = [cwd];

                if (isPaused)
                    return;

                if (initialRun) {
                    progressLogObj.val("");
                    lastPrintedMessage = '';
                }

                //$('#btnZipAll, #btnZipSelected, #btnContinue').addClass('disabled');
                //$('#btnPause').removeClass('disabled');

                progressBarObj[currentOption].addClass('active');

                var flushToDisk = 50;
                var maxExecutionTime = 20;
                var exclude_strings = '';
                var useSystemCalls = false;
                var preloadFiles = false;
                var zip_url = preloadFiles ? 'zip-preload' : 'zip';

                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    dataType: 'json',
                    beforeSend: function () {
                        processFinished = false;
                        if (initialRun) {
                            flmbkp_back_backup.watchProgress();
                        }
                        initialRun = 0;
                    },
                    data: {
                        targets: selected_paths,
                        flush_to_disk: flushToDisk,
                        max_execution_time: maxExecutionTime,
                        excludes: exclude_strings,
                        is_initial_run: initialRun,
                        use_system_calls: useSystemCalls,
                        'nexstep':currentOption,
                        'flmbkp_slug': flmbkp_slug,
                        'action': 'flmbkp_backup_sendoptions',
                        'page': 'flmbkp_page_backups',
                        'flmbkp_security': flmbkp_vars.ajax_nonce
                    },
                    success: function (msg) {
                        lastZipResponse = msg;
                        //flmbkp_back_helper.redirect_tourl(msg.url);
                        
                        if (msg.continue) {

                        } else {
                            
                        }



                    },
                    complete: function () {
                        if (!lastZipResponse.error && lastZipResponse.continue) {
                            flmbkp_back_backup.options_filebackup();
                        } else {
                            setTimeout(function () {
                                flmbkp_back_backup.options_routeNextStep();
                            }, 3000);
                            processFinished = true;

                        }
                    }
                });

            };

            /*
             * watch progress
             */
            this.watchProgress = function () {
                if (processFinished || isPaused)
                    progressLastRun = true;
                else
                    progressLastRun = false;
                setTimeout(function () {
                    $.ajax({
                        url: ajaxurl,
                        type: 'GET',
                        data: {
                            'action': 'flmbkp_backup_watchprogress',
                            'page': 'flmbkp_page_backups',
                            'flmbkp_security': flmbkp_vars.ajax_nonce,
                        },
                        dataType: 'json',
                        success: function (resp) {

                            if (currentOption != '') {
                                var start = resp.msgs.indexOf(lastPrintedMessage);


                                var newMessages = '';
                                var newMessagesCount = 0;
                                var x = 0;
                                for (x = start + 1; x < resp.msgs.length; x++) {
                                    lastPrintedMessage = resp.msgs[x];
                                    newMessages += "\n" + resp.msgs[x];
                                    newMessagesCount++;
                                }

                                var logLength = progressLogObj.val().split("\n").length;
                                var logMessages = progressLogObj.val();
                                if (length >= 200) {
                                    var logHistory = progressLogObj.val().split("\n");
                                    logHistory.splice(0, logLength - (199 + newMessagesCount)); //Keep the history with a maximum of 1000 lines
                                    logMessages = logHistory.join("\n");
                                }
                                progressLogObj.val(logMessages + newMessages);
                                progressLogObj.scrollTop(progressLogObj[0].scrollHeight);

                                progressBarObj[currentOption].attr('aria-valuenow', resp.percent);
                                progressBarObj[currentOption].css('width', resp.percent + '%');
                                progressBarMsgObj[currentOption].text(resp.percent + '% completed');
                                progressBarMsgObj[currentOption].css('width', resp.percent + '%');

                                progressBarMsgObj[currentOption].removeClass('progress-bar-animated');


                                if (progressLastRun) {
                                    progressBarObj[currentOption].removeClass('active');

                                }
                            }


                        },
                        complete: function () {

                            if (!progressLastRun) {

                                flmbkp_back_backup.watchProgress();
                            } else {

                                //flmbkp_back_backup.options_routeNextStep(); 
                            }
                        }
                    });
                }, 1000);
            };


        };
        window.flmbkp_back_backup = flmbkp_back_backup = $.flmbkp_back_backup = new flmbkp_back_backup();

        flmbkp_back_backup.initialize();

    })($uifm, window);
}


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


if(typeof($uifm) === 'undefined') {
	$uifm = jQuery;
}
var flmbkp_back_helper = flmbkp_back_helper || null;
if(!$uifm.isFunction(flmbkp_back_helper)){
(function($, window) {
 "use strict";  
    
var flmbkp_back_helper = function(){
    var flmbkp_variable = [];
    flmbkp_variable.innerVars = {};
    flmbkp_variable.externalVars = {};
    
    this.initialize = function() {
        
    }
    
    this.length_obj = function(obj) {
        var count = 0;
            for (var p in obj) {
              obj.hasOwnProperty(p) && count++;
            }
            return count;
    }
     
    this.generateUniqueID = function (nrodec) {
                var number = Math.random() // 0.9394456857981651
                number.toString(36); // '0.xtis06h6'
                var id = number.toString(36).substr(2, nrodec); // 'xtis06h6'
                return id;
    }; 
     
    this.versionCompare = function(v1, v2, options) {
        
        /*assert(versionCompare("1.7.1", "1.7.10") < 0);
        assert(versionCompare("1.7.2", "1.7.10") < 0);
        assert(versionCompare("1.6.1", "1.7.10") < 0);
        assert(versionCompare("1.6.20", "1.7.10") < 0);
        assert(versionCompare("1.7.1", "1.7.10") < 0);
        assert(versionCompare("1.7", "1.7.0") < 0);
        assert(versionCompare("1.7", "1.8.0") < 0);

        assert(versionCompare("1.7.2", "1.7.10b", {lexicographical: true}) > 0);
        assert(versionCompare("1.7.10", "1.7.1") > 0);
        assert(versionCompare("1.7.10", "1.6.1") > 0);
        assert(versionCompare("1.7.10", "1.6.20") > 0);
        assert(versionCompare("1.7.0", "1.7") > 0);
        assert(versionCompare("1.8.0", "1.7") > 0);

        assert(versionCompare("1.7.10", "1.7.10") === 0);
        assert(versionCompare("1.7", "1.7") === 0);
        assert(versionCompare("1.7", "1.7.0", {zeroExtend: true}) === 0);

        assert(isNaN(versionCompare("1.7", "1..7")));
        assert(isNaN(versionCompare("1.7", "Bad")));
        assert(isNaN(versionCompare("1..7", "1.7")));
        assert(isNaN(versionCompare("Bad", "1.7")));*/
        
        
        var lexicographical = options && options.lexicographical,
        zeroExtend = options && options.zeroExtend,
        v1parts = v1.split('.'),
        v2parts = v2.split('.');

    function isValidPart(x) {
        return (lexicographical ? /^\d+[A-Za-z]*$/ : /^\d+$/).test(x);
    }

    if (!v1parts.every(isValidPart) || !v2parts.every(isValidPart)) {
        return NaN;
    }

    if (zeroExtend) {
        while (v1parts.length < v2parts.length) v1parts.push("0");
        while (v2parts.length < v1parts.length) v2parts.push("0");
    }

    if (!lexicographical) {
        v1parts = v1parts.map(Number);
        v2parts = v2parts.map(Number);
    }

    for (var i = 0; i < v1parts.length; ++i) {
        if (v2parts.length == i) {
            return 1;
        }

        if (v1parts[i] == v2parts[i]) {
            continue;
        }
        else if (v1parts[i] > v2parts[i]) {
            return 1;
        }
        else {
            return -1;
        }
    }

    if (v1parts.length != v2parts.length) {
        return -1;
    }

    return 0;
    }
    
    /*tools for add,subs data*/
    this.getData= function(mainarr, name) {
        return mainarr[name];
    };
    
    this.setData = function(mainarr,name,value) {
         mainarr[name]=value;
    };
    
    
    this.getData2 = function(mainarr,name,index) {
                try{
                return mainarr[name][index];
                    }
                catch(err) {
                    console.log('error getUiData2: '+err.message);
                } 
            };
    this.setData2 = function(mainarr,name,index,value) {
      if (!mainarr.hasOwnProperty(name)){
            mainarr[name]= {};
          }
      if (!mainarr[name].hasOwnProperty(index)){
            mainarr[name][index]= {};
          }
        mainarr[name][index]=value;   
    };
    this.getData3 = function(mainarr,name,index,key) {
                try{
                return mainarr[name][index][key];
                    }
                catch(err) {
                    console.log('error getUiData3: '+err.message);
                } 
            };
    this.delData3 = function(mainarr,name,index,key) {
                delete mainarr[name][index][key];
            };
            
    this.setData3 = function(mainarr,name,index,key,value) {
       if (!mainarr.hasOwnProperty(name)){
            mainarr[name]= {};
          }
       if (!mainarr[name].hasOwnProperty(index)){
           mainarr[name][index]={};
          }
        
       mainarr[name][index][key]=value;   
    };
    this.setData4 = function(mainarr,name,index,key,option,value) {
        
        if (!mainarr.hasOwnProperty(name)){
            mainarr[name]= {};
        }
        if (!mainarr[name].hasOwnProperty(index)){
            mainarr[name][index]={};
        }

        if (!mainarr[name][index].hasOwnProperty(key)){
            mainarr[name][index][key]={};
        }
        
        mainarr[name][index][key][option]=value;
        
           
    };
    this.getData4 = function(mainarr,name,index,key,option) {
        try{
                return mainarr[name][index][key][option];
             }
        catch(err) {
            console.log('error getUiData4: '+err.message);
        }     
    };
    this.getData5 = function(mainarr,name,index,key,section,option) {
        try {
            if(typeof mainarr[name][index] == 'undefined') {
              return '';
            }else{
                return mainarr[name][index][key][section][option];
            } 
        }
        catch(err) {
            console.log('error getUiData5: '+err.message);
            return '';
        } 
       }; 
      this.setData5 = function(mainarr,name,index,key,section,option,value) {
            
            if (!mainarr.hasOwnProperty(name)){
            mainarr[name]= {};
            }
            if (!mainarr[name].hasOwnProperty(index)){
                mainarr[name][index]={};
            }

            if (!mainarr[name][index].hasOwnProperty(key)){
                mainarr[name][index][key]={};
            }
            
            if (!mainarr[name][index][key].hasOwnProperty(section)){
                mainarr[name][index][key][section]={};
            }
            
            mainarr[name][index][key][section][option]=value;
               
    };
    this.addIndexData5 = function(mainarr,name,index,key,section,option,value) {
            if(typeof mainarr[name][index][key][section][option] == 'undefined') {
              
            }else{
                mainarr[name][index][key][section][option][value]={};
            }    
    };
    
    this.getData6 = function(mainarr,name,index,key,section,option,option2) {
        try {
            if(typeof mainarr[name][index][key][section][option][option2] == 'undefined') {
              return '';
            }else{
                return mainarr[name][index][key][section][option][option2];
            } 
        }
        catch(err) {
            console.log('error handled - getUiData6: '+err.message);
            return '';
        } 
       };
      
       
    this.setData6 = function(mainarr,name,index,key,section,option,option2,value) {
            
            if (!mainarr.hasOwnProperty(name)){
                mainarr[name]= {};
            }
            if (!mainarr[name].hasOwnProperty(index)){
                mainarr[name][index]={};
            }

            if (!mainarr[name][index].hasOwnProperty(key)){
                mainarr[name][index][key]={};
            }
            
            if (!mainarr[name][index][key].hasOwnProperty(section)){
                mainarr[name][index][key][section]={};
            }
            
            if (!mainarr[name][index][key][section].hasOwnProperty(option)){
                mainarr[name][index][key][section][option]={};
            }
            
            mainarr[name][index][key][section][option][option2]=value;
             
    };
    
    this.delData6 = function(mainarr,name,index,key,section,option,option2) {
                delete mainarr[name][index][key][section][option][option2];
            };
            
    this.redirect_tourl = function (redirect) {
              if(window.event ) {/*IE 6*/
                    window.event.returnValue = false;
                    window.location =redirect;
                    //return false;
                }else {/*firefox*/
                    location.href =redirect;
                }
             };        
    
};
window.flmbkp_back_helper = flmbkp_back_helper = $.flmbkp_back_helper = new flmbkp_back_helper();


})($uifm,window);
} 