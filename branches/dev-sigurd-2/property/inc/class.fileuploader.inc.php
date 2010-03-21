<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage location
 	* @version $Id: class.uilocation.inc.php 5083 2010-03-19 14:29:26Z sigurd $
	*/

	/**
	 * Description
	 * @package property
	 */
	phpgw::import_class('phpgwapi.yui');

	class property_fileuploader
	{

		var $public_functions = array
		(
			'add'  	=> true
		);

		function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app']			= true;
			$GLOBALS['phpgw_info']['flags']['noframework']		= true;
			$GLOBALS['phpgw_info']['flags']['no_reset_fonts']	= true;
		}

		function add()
		{
			$upload_target 	= phpgw::get_var('upload_target');
			$id			 	= phpgw::get_var('id');

			$link_data = array
			(
				'menuaction'			=> $upload_target,
				'id' 					=> $id,
				'last_loginid'			=> phpgw::get_var('last_loginid'),
				'last_domain'			=> phpgw::get_var('last_domain'),
				'sessionphpgwsessid'	=> phpgw::get_var('sessionphpgwsessid'),
				'domain'				=> phpgw::get_var('domain')
			);
				
			foreach ($_GET as $varname => $value)
			{
				if(strpos($varname, '_')===0)
				{
					$link_data[substr($varname,1,strlen($varname)-1)] =  $value;
				}
			}

			$upload_url 	= $GLOBALS['phpgw']->link('/index.php', $link_data);

			$js_code = self::get_js($upload_url);
		
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/fonts/fonts-min.css');
			phpgwapi_yui::load_widget('uploader');

			$GLOBALS['phpgw']->xslttpl->add_file(array('fileuploader'));
			$data = array
			(
				'js_code' => $js_code,
			);
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('fileuploader' => $data));
		}

		
		static function get_js($upload_url = '')
		{
			$js_code = <<<JS
			YAHOO.util.Event.onDOMReady(function () { 
			var uiLayer = YAHOO.util.Dom.getRegion('selectLink');
			var overlay = YAHOO.util.Dom.get('uploaderOverlay');
			YAHOO.util.Dom.setStyle(overlay, 'width', uiLayer.right-uiLayer.left + "px");
			YAHOO.util.Dom.setStyle(overlay, 'height', uiLayer.bottom-uiLayer.top + "px");
			});

			// Custom URL for the uploader swf file (same folder).

			YAHOO.widget.Uploader.SWFURL = "{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/js/yahoo/uploader/assets/uploader.swf";

			// Instantiate the uploader and write it to its placeholder div.
			var uploader = new YAHOO.widget.Uploader( "uploaderOverlay" );
			
			// Add event listeners to various events on the uploader.
			// Methods on the uploader should only be called once the 
			// contentReady event has fired.
	
			uploader.addListener('contentReady', handleContentReady);
			uploader.addListener('fileSelect', onFileSelect)
			uploader.addListener('uploadStart', onUploadStart);
			uploader.addListener('uploadProgress', onUploadProgress);
			uploader.addListener('uploadCancel', onUploadCancel);
			uploader.addListener('uploadComplete', onUploadComplete);
			uploader.addListener('uploadCompleteData', onUploadResponse);
			uploader.addListener('uploadError', onUploadError);
			uploader.addListener('rollOver', handleRollOver);
			uploader.addListener('rollOut', handleRollOut);
			uploader.addListener('click', handleClick);
    	
			// Variable for holding the filelist.
			var fileList;
	
			// When the mouse rolls over the uploader, this function
			// is called in response to the rollOver event.
			// It changes the appearance of the UI element below the Flash overlay.
			function handleRollOver () {
				YAHOO.util.Dom.setStyle(YAHOO.util.Dom.get('selectLink'), 'color', "#FFFFFF");
				YAHOO.util.Dom.setStyle(YAHOO.util.Dom.get('selectLink'), 'background-color', "#000000");
			}

			// On rollOut event, this function is called, which changes the appearance of the
			// UI element below the Flash layer back to its original state.
			function handleRollOut () {
				YAHOO.util.Dom.setStyle(YAHOO.util.Dom.get('selectLink'), 'color', "#0000CC");
				YAHOO.util.Dom.setStyle(YAHOO.util.Dom.get('selectLink'), 'background-color', "#FFFFFF");
			}

			// When the Flash layer is clicked, the "Browse" dialog is invoked.
			// The click event handler allows you to do something else if you need to.
			function handleClick () {
			}

			// When contentReady event is fired, you can call methods on the uploader.
			function handleContentReady () {
			    // Allows the uploader to send log messages to trace, as well as to YAHOO.log
				uploader.setAllowLogging(true);

				// Allows multiple file selection in "Browse" dialog.
				uploader.setAllowMultipleFiles(true);

				// New set of file filters.
				var ff = new Array({description:"Images", extensions:"*.jpg;*.png;*.gif"},
				                   {description:"Videos", extensions:"*.avi;*.mov;*.mpg"});

				// Apply new set of file filters to the uploader.
//				uploader.setFileFilters(ff);
			}

			// Actually uploads the files. In this case,
			// uploadAll() is used for automated queueing and upload 
			// of all files on the list.
			// You can manage the queue on your own and use "upload" instead,
			// if you need to modify the properties of the request for each
			// individual file.
			function upload() {
				if (fileList != null) {
					uploader.setSimUploadLimit(parseInt(document.getElementById("simulUploads").value));
					uploader.uploadAll("{$upload_url}", "POST", null, "Filedata");
				}	
			}

			// Fired when the user selects files in the "Browse" dialog
			// and clicks "Ok".
			function onFileSelect(event) {
				if('fileList' in event && event.fileList != null) {
					fileList = event.fileList;
					createDataTable(fileList);
				}
			}

			function createDataTable(entries) {
			  rowCounter = 0;
			  this.fileIdHash = {};
			  this.dataArr = [];
			  for(var i in entries) {
			     var entry = entries[i];
				 entry["progress"] = "<div style='height:5px;width:100px;background-color:#CCC;'></div>";
			     dataArr.unshift(entry);
			  }

			  for (var j = 0; j < dataArr.length; j++) {
			    this.fileIdHash[dataArr[j].id] = j;
			  }

			    var myColumnDefs = [
			        {key:"name", label: "File Name", sortable:false},
			     	{key:"size", label: "Size", sortable:false},
			     	{key:"progress", label: "Upload progress", sortable:false}
			    ];

			this.myDataSource = new YAHOO.util.DataSource(dataArr);
			this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
			this.myDataSource.responseSchema = {
          		fields: ["id","name","created","modified","type", "size", "progress"]
      		};

			this.singleSelectDataTable = new YAHOO.widget.DataTable("dataTableContainer",
			           myColumnDefs, this.myDataSource, {
			               caption:"Files To Upload",
			               selectionMode:"single"
			           });
			}

			// Do something on each file's upload start.
			function onUploadStart(event) {
			
			}

			// Do something on each file's upload progress event.
			function onUploadProgress(event) {
				rowNum = fileIdHash[event["id"]];
				prog = Math.round(100*(event["bytesLoaded"]/event["bytesTotal"]));
				progbar = "<div style='height:5px;width:100px;background-color:#CCC;'><div style='height:5px;background-color:#F00;width:" + prog + "px;'></div></div>";
				singleSelectDataTable.updateRow(rowNum, {name: dataArr[rowNum]["name"], size: dataArr[rowNum]["size"], progress: progbar});	
			}

			// Do something when each file's upload is complete.
			function onUploadComplete(event) {
				rowNum = fileIdHash[event["id"]];
				prog = Math.round(100*(event["bytesLoaded"]/event["bytesTotal"]));
				progbar = "<div style='height:5px;width:100px;background-color:#CCC;'><div style='height:5px;background-color:#F00;width:100px;'></div></div>";
				singleSelectDataTable.updateRow(rowNum, {name: dataArr[rowNum]["name"], size: dataArr[rowNum]["size"], progress: progbar});
			}

			// Do something if a file upload throws an error.
			// (When uploadAll() is used, the Uploader will
			// attempt to continue uploading.
			function onUploadError(event) {

			}

			// Do something if an upload is cancelled.
			function onUploadCancel(event) {

			}

			// Do something when data is received back from the server.
			function onUploadResponse(event) {

			}
JS;
			return $js_code;
		}
 }
