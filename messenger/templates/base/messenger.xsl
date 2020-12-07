<!-- $Id: support.xsl 4904 2010-02-24 13:32:35Z sigurd $ -->

<xsl:template match="compose_groups" xmlns:php="http://php.net/xsl">
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
		
		<div id="groups">
			<h2>
				<xsl:value-of select="php:function('lang', 'groups')" />
			</h2>
			<ul class="list-group">
				<xsl:apply-templates select="group_list" />
			</ul>
		</div>

		<h2>
			<xsl:value-of select="php:function('lang', 'Compose message')" />
		</h2>

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
</xsl:template>

<!-- BEGIN group_list -->
<xsl:template match="group_list">

	<li class="list-group-item">
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
	</li>
</xsl:template>

