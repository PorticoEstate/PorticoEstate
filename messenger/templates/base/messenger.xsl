<!-- $Id: support.xsl 4904 2010-02-24 13:32:35Z sigurd $ -->

<xsl:template match="compose_groups" xmlns:php="http://php.net/xsl">
	<div class="card shadow mb-4">
		<!-- Card Header - Dropdown -->
		<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
			<h6 class="m-0 font-weight-bold text-primary">
				<xsl:value-of select="php:function('lang', 'Compose message')" />
			</h6>
			<div class="dropdown no-arrow">
				<a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
				</a>
				<div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink" style="">
					<div class="dropdown-header">Dropdown Header:</div>
					{read_buttons}

				</div>
			</div>
		</div>
		<div class="card-body">

			<form ENCTYPE="multipart/form-data" name="form" method="post" action="{form_action}">
				<table cellpadding="0" cellspacing="0" width="100%">
					<xsl:choose>
						<xsl:when test="msgbox_data != ''">
							<tr>
								<td align="left" colspan="2">
									<xsl:call-template name="msgbox"/>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
				</table>
		
				<div class="form-group">
					<label for="account_groups">
						<xsl:value-of select="php:function('lang', 'groups')" />
					</label>

					<select id="account_groups" name="account_groups[]" class="form-control" multiple="multiple">
						<xsl:for-each select="group_list">
							<xsl:sort select="account_lid"/>
							<option>
								<xsl:attribute name="value">
									<xsl:value-of select="account_id"/>
								</xsl:attribute>
								<xsl:if test="i_am_admin != 1">
									<xsl:attribute name="disabled" value="disabled" />
								</xsl:if>
								<xsl:value-of select="account_lid"/>
							</option>
						</xsl:for-each>
					</select>

					<!--			<ul class="list-group">
						<xsl:apply-templates select="group_list" />
					</ul>-->
				</div>

				<div class="form-group">
					<label for="subject">
						<xsl:value-of select="php:function('lang', 'subject')" />
					</label>
					<input type="text" name="values[subject]" class="form-control" value='{value_subject}' id="subject">
						<xsl:attribute name="size">
							<xsl:text>60</xsl:text>
						</xsl:attribute>
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'subject')" />
						</xsl:attribute>
						<xsl:attribute name="placeholder">
							<xsl:value-of select="php:function('lang', 'subject')" />
						</xsl:attribute>
					</input>
				</div>

				<div class="form-group">
					<label for="content">
						<xsl:value-of select="php:function('lang', 'content')" />
					</label>
					<textarea cols="60" rows="10" name="values[content]" id="content" class="form-control">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'content')" />
						</xsl:attribute>
						<xsl:value-of select="value_content"/>
					</textarea>
				</div>

				<xsl:variable name="lang_send">
					<xsl:value-of select="php:function('lang', 'send')" />
				</xsl:variable>
				<input type="submit" name="values[save]" value="{$lang_send}" title='{$lang_send}' class="btn btn-primary">
				</input>

			</form>
		</div>
	</div>
	<script>
		$("#account_groups").multiselect({
			buttonClass: 'form-select',
			templates: {
			button: '<button type="button" class="multiselect dropdown-toggle" data-bs-toggle="dropdown"><span class="multiselect-selected-text"></span></button>',
			},
			buttonWidth: 450,
			includeSelectAllOption: true,
			enableFiltering: true,
			enableCaseInsensitiveFiltering: true,
			onChange: function ($option)
		{
		// Check if the filter was used.
		var query = $("#account_groups").find("li.multiselect-filter input").val();
		if (query)
		{
		$("#account_groups").find("li.multiselect-filter input").val("").trigger("keydown");
		}
		}
		});

		$(".btn-group").addClass('w-100');
		$(".multiselect").addClass('form-control');
		$(".multiselect").removeClass('btn');
		$(".multiselect").removeClass('btn-default');

	</script>

</xsl:template>

<!-- BEGIN group_list -->
<xsl:template match="group_list">

	<!--	<li class="list-group-item">
		<div class="custom-control custom-checkbox">
			<xsl:choose>
				<xsl:when test="i_am_admin = 1">
					<input type="checkbox" class="custom-control-input" id="account_groups{account_id}" name="account_groups[]" value="{account_id}">
						<xsl:choose>
							<xsl:when test="selected = '1'">
								<xsl:attribute name="checked" value="checked" />
							</xsl:when>
						</xsl:choose>
					</input>
				</xsl:when>
				<xsl:otherwise>
					<input type="checkbox" class="custom-control-input" readonly='true'>
					</input>
				</xsl:otherwise>
			</xsl:choose>

			<label class="custom-control-label"  for="account_groups{account_id}">
				<xsl:value-of select="account_lid"/>
			</label>
		</div>
	</li>-->
</xsl:template>

