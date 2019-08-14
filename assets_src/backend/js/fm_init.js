;(function( $ ){

  window.queueForRequire = [];
window.r = function (deps, callback) {
    window.queueForRequire.push({
        deps:     deps,
        callback: callback
    });
};

 if (typeof jQuery === 'function') {
        define('jquery', function () { return jQuery; });
    }
    
  for (var i = 0; i < window.queueForRequire.length; i++) {
        require(window.queueForRequire[i].deps, window.queueForRequire[i].callback);
    }  

   
})(jQuery);
define('elFinderConfig', {
				// elFinder options (REQUIRED)
				// Documentation for client options:
				// https://github.com/Studio-42/elFinder/wiki/Client-configuration-options
				defaultOpts : {
					url : ajaxurl // connector URL (REQUIRED)
					,commandsOptions : {
					 
						quicklook : {
							// to enable CAD-Files and 3D-Models preview with sharecad.org
							sharecadMimes : ['image/vnd.dwg', 'image/vnd.dxf', 'model/vnd.dwf', 'application/vnd.hp-hpgl', 'application/plt', 'application/step', 'model/iges', 'application/vnd.ms-pki.stl', 'application/sat', 'image/cgm', 'application/x-msmetafile'],
							// to enable preview with Google Docs Viewer
							googleDocsMimes : ['application/pdf', 'image/tiff', 'application/vnd.ms-office', 'application/msword', 'application/vnd.ms-word', 'application/vnd.ms-excel', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/postscript', 'application/rtf'],
							// to enable preview with Microsoft Office Online Viewer
							// these MIME types override "googleDocsMimes"
							officeOnlineMimes : ['application/vnd.ms-office', 'application/msword', 'application/vnd.ms-word', 'application/vnd.ms-excel', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/vnd.oasis.opendocument.text', 'application/vnd.oasis.opendocument.spreadsheet', 'application/vnd.oasis.opendocument.presentation']
						}
					}
					// bootCalback calls at before elFinder boot up 
					,bootCallback : function(fm, extraObj) {
						/* any bind functions etc. */
						fm.bind('init', function() {
							// any your code
						});
						// for example set document.title dynamically.
						var title = document.title;
						fm.bind('open', function() {
							var path = '',
								cwd  = fm.cwd();
							if (cwd) {
								path = fm.path(cwd.hash) || null;
							}
							document.title = path? path + ':' + title : title;
						}).bind('destroy', function() {
							document.title = title;
						});
					},
                                        defaultView : 'list',
                                        resizable: true,
                                        cssAutoLoad : [flmbkp_vars.plugin_url+flmbkp_vars.opt_theme], // Array of additional CSS URLs
                                        lang : flmbkp_vars.opt_lang,
                                        height:  jQuery(window).height() - 200,
                                        customData: {
							'flmbkp_security':flmbkp_vars.ajax_nonce,
                                                        'action': 'flmbkp_back_initfm',
                                                        'page':'flmbkp_file_manager',
							}
				},
                                
				managers : {
					// 'DOM Element ID': { /* elFinder options of this DOM Element */ }
					'elfinder': {}
				},
                            
                         
			});