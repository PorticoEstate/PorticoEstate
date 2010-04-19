<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:phpgw="http://dtds.phpgroupware.org/phpgw.dtd"
	xmlns:phpgwapi="http://dtds.phpgroupware.org/phpgwapi.dtd"
	xmlns:communik8r="http://dtds.phpgroupware.org/communik8r.dtd">
	<xsl:output method="xml" indent="yes" />
	<xsl:template match="/">

		<xsl:variable name="base_url" 
			select="concat(/phpgw:response/phpgwapi:info/phpgwapi:base_url, '/communik8r/')"/>
		<xsl:variable name="img_url" 
			select="concat($base_url, 'templates/default/images/')"/>
		<xsl:variable name="mailbox" 
			select="/phpgw:response/communik8r:response/communik8r:info/communik8r:mailbox"/>
		<xsl:variable name="account_id" 
			select="/phpgw:response/communik8r:response/communik8r:info/communik8r:account/@id"/>
		<xsl:variable name="hide_deleted" 
			select="/phpgw:response/communik8r:response/communik8r:info/communik8r:hide_deleted"/>
		<div id="summary">
			<table id="summary_tbl">
				<col />
				<col />
				<col />
				<col />
				<col />
				<col />
				<col />
				<col />
				<col />
				<thead>
					<tr>
						<th class="sum_img">
							<img src="{$img_url}mail-16x16.png" alt="status" title="status" />
						</th>
						<th class="sum_img">
							<img src="{$img_url}attach-16x16.png" alt="attachments" 
								title="attachment" />
						</th>
						<th class="sum_img">
							<img src="{$img_url}important-16x16.png" 
								alt="important" title="important" />
						</th>
						<th class="sum_from">From</th>
						<th class="sum_subject">Subject</th>
						<th class="sum_hidden">Date Hidden</th>
						<th class="sum_date">Date</th>
						<th class="sum_hidden">Size Hidden</th>
						<th class="sum_size">Size</th>
					</tr>
				</thead>
				<tbody>
				<xsl:for-each select="/phpgw:response/communik8r:response/communik8r:msg_sums/communik8r:msg_sum">
					<xsl:if test="(communik8r:msg_flags/@flag_deleted='false') or ($hide_deleted='false')">
						<xsl:call-template name="msg_sum">
							<xsl:with-param name="img_url" select="$img_url"/>
							<xsl:with-param name="account_id" select="$account_id"/>
							<xsl:with-param name="mailbox" select="$mailbox"/>
						</xsl:call-template>
					</xsl:if>
				</xsl:for-each>
				</tbody>
			</table>
		</div>
		<script>
			oSummary.bHideDeleted = ('<xsl:value-of select="$hide_deleted" />' == 'true');//needed for vfolders
		</script>
	</xsl:template>

	<xsl:template name="msg_sum">
		<xsl:param name="img_url"/>
		<xsl:param name="account_id"/>
		<xsl:param name="mailbox"/>

		<xsl:variable name="msgid" select="@id"/>
		
		<xsl:variable name="css_seen">
			<xsl:choose>
				<xsl:when test="communik8r:msg_flags/@flag_seen='true'"> </xsl:when>
				<xsl:otherwise>unread</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>

		<xsl:variable name="css_deleted">
			<xsl:choose>
				<xsl:when test="communik8r:msg_flags/@flag_deleted='true'">deleted</xsl:when>
				<xsl:otherwise> </xsl:otherwise>
			</xsl:choose>
		</xsl:variable>

		<xsl:variable name="icon">
			<xsl:choose>
				<xsl:when test="communik8r:msg_flags/@flag_answered='true'"><xsl:value-of select="$img_url"/>mail-replied-16x16.png</xsl:when>
				<xsl:when test="communik8r:msg_flags/@flag_draft='true'"><xsl:value-of select="$img_url"/>mail-compose-16x16.png</xsl:when>
				<xsl:when test="communik8r:msg_flags/@flag_seen='true'"><xsl:value-of select="$img_url"/>mail-open-16x16.png</xsl:when>
				<xsl:otherwise><xsl:value-of select="$img_url"/>mail-16x16.png</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>

		<xsl:variable name="icon_alt">
			<xsl:choose>
				<xsl:when test="communik8r:msg_flags/@flag_answered='true'">replied message</xsl:when>
				<xsl:when test="communik8r:msg_flags/@flag_draft='true'">draft message</xsl:when>
				<xsl:when test="communik8r:msg_flags/@flag_seen='true'">opened message</xsl:when>
				<xsl:otherwise>unopened message</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>

		<tr id="{$msgid}" class="{concat($css_seen, ' ', $css_deleted)}">
			<td class="sum_img"><img src="{$icon}" alt="{$icon_alt}" title="{$icon_alt}" /></td>
			<td class="sum_img">
				<xsl:choose>
					<xsl:when test="communik8r:msg_flags/@attachments='true'">
						<img src="{$img_url}attach-16x16.png" alt="attachment" />
					</xsl:when>
					<xsl:otherwise>
						&#160;
					</xsl:otherwise>
				</xsl:choose>
			</td>
			<td class="sum_img">
				<xsl:choose>
					<xsl:when test="communik8r:msg_flags/@flag_flagged='true'">
						<img src="{$img_url}important-16x16.png" alt="important" />
					</xsl:when>
					<xsl:otherwise>
						&#160;
					</xsl:otherwise>
				</xsl:choose>
			</td>
			<td class="sum_from"><xsl:value-of select="communik8r:msg_sender"/></td>
			<td class="sum_subject"><xsl:value-of select="communik8r:msg_subject"/></td>
			<td class="sum_hidden"><xsl:value-of select="communik8r:msg_date/@intval"/></td>
			<td class="sum_date"><xsl:value-of select="communik8r:msg_date"/></td>
			<td class="sum_hidden"><xsl:value-of select="communik8r:msg_size/@intval"/></td>
			<td class="sum_size"><xsl:value-of select="communik8r:msg_size"/></td>
		</tr>
	</xsl:template>
</xsl:stylesheet>
