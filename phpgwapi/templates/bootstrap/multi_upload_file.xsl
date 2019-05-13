
<!-- $Id: multi_upload_file.xsl 14753 2016-02-18 18:23:21Z sigurdne $ -->

<!-- New template-->
<xsl:template xmlns:php="http://php.net/xsl" name="multi_upload_file">
	<xsl:variable name="action">
		<xsl:value-of select="multi_upload_action"/>
	</xsl:variable>


    <form id="multi_upload_file"  action="{$action}" method="POST" enctype="multipart/form-data">
        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
        <div class="row fileupload-buttonbar">
            <div class="col-lg-7">
                <!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button">
                    <i class="fas fa-plus"></i>
					<span><xsl:value-of select="php:function('lang', 'Add files')"/>...</span>
					<input type="file" id="files" name="files[]" multiple="">
						<xsl:attribute name="accept">image/*</xsl:attribute>
						<xsl:attribute name="capture">camera</xsl:attribute>
					</input>
               </span>
                <button type="submit" class="btn btn-primary start">
                    <i class="fas fa-arrow-circle-up"></i>
                    <span><xsl:value-of select="php:function('lang', 'Start upload')"/></span>
                </button>
                <button type="reset" class="btn btn-warning cancel">
                    <i class="fas fa-ban"></i>
                    <span><xsl:value-of select="php:function('lang', 'Cancel upload')"/></span>
                </button>
                <button type="button" class="btn btn-danger delete">
                    <i class="fas fa-trash-alt"></i>
                    <span><xsl:value-of select="php:function('lang', 'Delete')"/></span>
                </button>
                <input type="checkbox" class="toggle"></input>
                <!-- The global file processing state -->
                <span class="fileupload-process"></span>
            </div>
            <!-- The global progress state -->
            <div class="col-lg-5 fileupload-progress fade">
                <!-- The global progress bar -->
                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                </div>
                <!-- The extended global progress state -->
                <div class="progress-extended">&nbsp;</div>
            </div>
        </div>
        <!-- The table listing the files available for upload/download -->
        <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
    </form>
 

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

               <button class="btn btn-primary start" disabled>
                    <i class="fas fa-arrow-circle-up"></i>
                    <span>Start</span>
                </button>
				{% } %}
				{% if (!i) { %}
               <button class="btn btn-warning cancel">
                   <i class="fas fa-ban"></i>
                    <span>Cancel</span>
                </button>
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
		<div class="template-download">
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
				<button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %} ><i class="fas fa-trash-alt"></i>Delete</button>
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
			//	maxChunkSize: 838855500
				maxChunkSize: 8388000
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
				
			$('#multi_upload_file')
				.bind('fileuploadcompleted', function (e, data) { 	
				$( "#files-count" ).html(data.result.num_files);
			});
				
			$('#multi_upload_file')
				.bind('fileuploaddestroyed', function (e, data) { 	
				var n = 0;
				$( ".template-download" ).each(function( i ) {
					n ++;
				});
				$("#files-count").html(n);
			}); 
												
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