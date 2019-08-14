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

