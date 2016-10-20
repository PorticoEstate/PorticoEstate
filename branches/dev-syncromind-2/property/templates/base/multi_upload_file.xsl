
<!-- $Id: multi_upload_file.xsl 14753 2016-02-18 18:23:21Z sigurdne $ -->

<!-- New template-->
<xsl:template xmlns:php="http://php.net/xsl" name="multi_upload_file">
	<xsl:variable name="action">
		<xsl:value-of select="multi_upload_action"/>
	</xsl:variable>
	<form id="multi_upload_file" action="{$action}" method="POST" enctype="multipart/form-data">
		<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
		<div class="fileupload-buttonbar">
			<div class="fileupload-buttons">
				<!-- The fileinput-button span is used to style the file input field as button -->
				<span class="fileinput-button pure-button">
					<span>Add files...</span>
					<input type="file" id="files" name="files[]" multiple=""/>
				</span>
				<button type="submit" class="start pure-button">Start upload</button>
				<button type="reset" class="cancel pure-button">Cancel upload</button>
				<button type="button" class="delete pure-button">Delete</button>
				<input type="checkbox" class="toggle"/>
				<!-- The global file processing state -->
				<span class="fileupload-process"></span>
			</div>
			<div class="fileupload-progress fade" style="display:none">
				<!-- The global progress bar -->
				<div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
				<!-- The extended global progress state -->
				<div class="progress-extended">&nbsp;</div>
			</div>
		</div>
		<!-- The table listing the files available for upload/download -->
		<div style="position: relative; overflow: auto; max-height: 50vh; width: 100%;">					
			<div class="presentation files" style="display: inline-table; width: 100%;"></div>
		</div>
	</form>

	<!-- The template to display files available for upload -->
	<script id="template-upload" type="text/x-tmpl">
<![CDATA[
	{% for (var i=0, file; file=o.files[i]; i++) { %}
		<div class="template-upload fade table-row">
			<div class="table-cell">
				<div class="name">{%=file.name%}</div>
				<div class="error"></div>
			</div>
			<div class="table-cell">
				<div class="size">Processing...</div>
			</div>
			<div class="table-cell">
				<div class="progress" style="width: 100px;"></div>
			</div>
			<div class="table-cell">
				{% if (!i && !o.options.autoUpload) { %}
					<button class="start pure-button" disabled="">Start</button>
				{% } %}
				{% if (!i) { %}
					<button class="cancel pure-button">Cancel</button>
				{% } %}
			</div>
		</div>
	{% } %}
]]>	
	</script>
	<!-- The template to display files available for download -->
	<script id="template-download" type="text/x-tmpl">
<![CDATA[
	{% for (var i=0, file; file=o.files[i]; i++) { %}
		<div class="template-download fade table-row">
			<div class="table-cell">						
				<div class="name">
					<!--<a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>-->
					{%=file.name%}							
				</div>
				{% if (file.error) { %} <div class="error">Error: {%=file.error%} </div>{% } %}
			</div>
			<div class="table-cell">
				<div class="size">{%=o.formatFileSize(file.size)%}</div>
			</div>
			<div class="table-cell">
				<button class="delete pure-button" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>Delete</button>
				<input type="checkbox" name="delete" value="1" class="toggle"/>
			</div>
		</div>
	{% } %}
]]>	
	</script>

	<script>
		$(function () {
			'use strict';
					
			// Initialize the jQuery File Upload widget:
			$('#multi_upload_file').fileupload({
				// Uncomment the following to send cross-domain cookies:
				//xhrFields: {withCredentials: true},
				url: '<xsl:value-of select="multi_upload_action"/>',
				limitConcurrentUploads: 4,
				//acceptFileTypes: /(\.|\/)(png|pdf)$/i
			});
				
			// Enable iframe cross-domain access via redirect option:
			$('#multi_upload_file').fileupload(
				'option',
				'redirect',
				window.location.href.replace(
					/\/[^\/]*$/,
					'/cors/result.html?%s'
				)
			);
				
			// Load existing files:
			$('#multi_upload_file').addClass('fileupload-processing');
			$.ajax({
				// Uncomment the following to send cross-domain cookies:
				//xhrFields: {withCredentials: true},
				url: $('#multi_upload_file').fileupload('option', 'url'),
				dataType: 'json',
				context: $('#multi_upload_file')[0]
			}).always(function () {
				$(this).removeClass('fileupload-processing');
			}).done(function (result) {
				$(this).fileupload('option', 'done').call(this, $.Event('done'), {result: result});
			});
		});
	</script>
</xsl:template>
