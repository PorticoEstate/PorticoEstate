
<!-- $Id: multi_upload_file.xsl 14753 2016-02-18 18:23:21Z sigurdne $ -->

<!-- New template-->
<xsl:template xmlns:php="http://php.net/xsl" name="multi_upload_file_inline">
	<xsl:param name="class" />
	<xsl:param name="multi_upload_action" />


	<style>

.file {
   position: relative;
   background: linear-gradient(to right, lightblue 50%, transparent 50%);
   background-size: 200% 100%;
   background-position: right bottom;
   transition:all 1s ease;
}
 .file.done {
   background: lightgreen;
}
 .file a {
   display: block;
   position: relative;
   padding: 5px;
   color: black;
}
		</style>


	<div id="drop-area" class="{$class}">
		<div style="border: 2px dashed #ccc; padding: 20px;">
			<p>
				<xsl:value-of select="php:function('lang', 'Upload multiple files with the file dialog, or by dragging and dropping images onto the dashed region')"/>
			</p>
			<div class="fileupload-buttonbar">
				<div class="fileupload-buttons">
					<!-- The fileinput-button span is used to style the file input field as button -->
					<span class="fileinput-button pure-button">
						<span>
							<xsl:value-of select="php:function('lang', 'Add files')"/>...</span>
						<input type="file" id="fileupload" name="files[]" multiple="">
							<xsl:attribute name="accept">image/*</xsl:attribute>
							<xsl:attribute name="capture">camera</xsl:attribute>
							<xsl:attribute name="data-url">
								<xsl:value-of select="$multi_upload_action"/>
							</xsl:attribute>
						</input>
					</span>

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

	<script>
	var multi_upload_action = '<xsl:value-of select="$multi_upload_action"/>';

<![CDATA[

var Allowed_Methods = [];
$(function ()
{
	'use strict';
	// Initialize the jQuery File Upload widget:
	$('#fileupload').fileupload({
		// Uncomment the following to send cross-domain cookies:
		//xhrFields: {withCredentials: true},
		limitConcurrentUploads: 4,
		//	maxChunkSize: 838855500
		maxChunkSize: 8388000,
		dataType: "json",
		add: function (e, data)
		{
			data.context = $('<p class="file">')
				.append($('<span>').text(data.files[0].name))
				.appendTo($(".content_upload_download"));
			data.submit();
		},
		progress: function (e, data)
		{
			var progress = parseInt((data.loaded / data.total) * 100, 10);
			data.context.css("background-position-x", 100 - progress + "%");
		},
		done: function (e, data)
		{
			if (data.result.files[0].error)
			{
				data.context
					.removeClass("file")
					.addClass("error")
					.find("span")
					.text(data.result.files[0].name + ', Error: ' + data.result.files[0].error);
			}
			else
			{
				data.context
					.addClass("done");
				refresh_files();
			}

		}
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
});
]]>

	</script>
</xsl:template>