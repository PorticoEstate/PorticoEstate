
<!-- $Id: multi_upload_file.xsl 14753 2016-02-18 18:23:21Z sigurdne $ -->

<!-- New template-->
<xsl:template xmlns:php="http://php.net/xsl" name="multi_upload_file_inline">
	<xsl:param name="class" />
	<xsl:param name="multi_upload_action" />


	<div id="drop-area" class="{$class}">
		<div id="fileupload" style="border: 2px dashed #ccc; padding: 20px;">
			<p>
				<xsl:value-of select="php:function('lang', 'Upload multiple files with the file dialog, or by dragging and dropping images onto the dashed region')"/>
			</p>
			<div class="fileupload-buttonbar">
				<div class="fileupload-buttons">
					<!-- The fileinput-button span is used to style the file input field as button -->
					<span class="fileinput-button pure-button">
						<span>
							<xsl:value-of select="php:function('lang', 'Add files')"/>...</span>
						<input type="file" id="files" name="files[]" multiple="">
							<xsl:attribute name="accept">image/*</xsl:attribute>
							<xsl:attribute name="capture">camera</xsl:attribute>
						</input>
					</span>
					<button id="start_upload_button" type="button" class="start pure-button"><xsl:value-of select="php:function('lang', 'Start upload')"/></button>

					<!-- The global file processing state -->
					<span class="fileupload-process"></span>
				</div>
<!--				<div class="fileupload-count">
					<xsl:value-of select="php:function('lang', 'Number files')"/>: <span id="files-count"></span>
				</div>-->
				<div class="fileupload-progress fade" style="display:none">
					<!-- The global progress bar -->
					<div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
					<!-- The extended global progress state -->
					<div class="progress-extended">&nbsp;</div>
				</div>
			</div>
			<!-- The table listing the files available for upload/download -->
			<div class="content_upload_download">
				<div class="presentation files" style="display: inline-table;"></div>
			</div>
		</div>
	</div>

	<!-- The template to display files available for upload -->
	<script id="template-upload" type="text/x-tmpl">
<![CDATA[
	{% for (var i=0, file; file=o.files[i]; i++) { %}
		<div class="template-upload">
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
		{% if (file.error) { %}
		<div class="template-download">
			<div class="table-cell">						
				<div class="name">
					{%=file.name%}							
				</div>
				<div class="error">Error: {%=file.error%} </div>
			</div>
			<div class="table-cell">
				<div class="size">{%=o.formatFileSize(file.size)%}</div>
			</div>

		</div>
		{% } %}
	{% } %}
]]>	
	</script>

	<script>
		var Allowed_Methods = [];
		$(function () {
		'use strict';
		// Initialize the jQuery File Upload widget:
		$('#fileupload').fileupload({
		// Uncomment the following to send cross-domain cookies:
		//xhrFields: {withCredentials: true},
		url: '<xsl:value-of select="$multi_upload_action"/>',
		limitConcurrentUploads: 4,
		//	maxChunkSize: 838855500
		maxChunkSize: 8388000
		//acceptFileTypes: /(\.|\/)(png|pdf)$/i
		});
				
		// Enable iframe cross-domain access via redirect option:
		$('#fileupload').fileupload(
			'option',
			'redirect',
			window.location.href.replace(
				/\/[^\/]*$/,
				'/cors/result.html?%s'
			)
		);
				
		$('#fileupload')
		.bind('fileuploadcompleted', function (e, data) {
			$( "#files-count" ).html(data.result.num_files);
			refresh_files();
		});
				
		$('#fileupload')
		.bind('fileuploaddestroyed', function (e, data) {
		var n = 0;
		$( ".template-download" ).each(function( i ) {
		n ++;
		});
		$("#files-count").html(n);
		});
												
		// Load existing files:
		$('#fileupload').addClass('fileupload-processing');
		$.ajax({
		// Uncomment the following to send cross-domain cookies:
		//xhrFields: {withCredentials: true},
		url: $('#fileupload').fileupload('option', 'url'),
		dataType: 'json',
		context: $('#fileupload')[0]
		}).always(function () {
		$(this).removeClass('fileupload-processing');
		}).done(function (result, dummy, xhr) {
		Allowed_Methods = xhr.getResponseHeader("Access-Control-Allow-Methods").split(",").map(function(item)
		{
		return item.trim();
		});
		$(this).fileupload('option', 'done').call(this, $.Event('done'), {result: result});
		});
		});
	</script>
</xsl:template>