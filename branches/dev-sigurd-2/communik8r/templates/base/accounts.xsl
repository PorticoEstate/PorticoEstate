<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:phpgw="http://dtds.phpgroupware.org/phpgw.dtd"
	xmlns:phpgwapi="http://dtds.phpgroupware.org/phpgwapi.dtd"
	xmlns:communik8r="http://dtds.phpgroupware.org/communik8r.dtd">
	<xsl:output method="xml" indent="yes" />
	<xsl:template match="/">

		<xsl:variable name="subicon"
			select="/phpgw:response/communik8r:response/communik8r:info/communik8r:subicon"/>
		<xsl:variable name="nosubicon"
			select="/phpgw:response/communik8r:response/communik8r:info/communik8r:nosubicon"/>
		<xsl:variable name="foldericon"
			select="/phpgw:response/communik8r:response/communik8r:info/communik8r:foldericon"/>

		<ul>
			<xsl:for-each select="/phpgw:response/communik8r:response/communik8r:accounts/communik8r:account">
				<xsl:call-template name="account">
					<xsl:with-param name="subicon" select="$subicon"/>
					<xsl:with-param name="nosubicon" select="$nosubicon"/>
					<xsl:with-param name="foldericon" select="$foldericon"/>
				</xsl:call-template>
			</xsl:for-each>
		</ul>
	</xsl:template>
	
	<xsl:template name="account">
		<xsl:param name="subicon"/>
		<xsl:param name="nosubicon"/>
		<xsl:param name="foldericon"/>

		<xsl:variable name="acct_id" select="@id"/>

		<li class="acct_folder" id="{concat('acct_', @id)}">
			<xsl:choose>
				<xsl:when test="count(communik8r:mailboxes/communik8r:mailbox) > 0">
					<img src="{$subicon}" alt="sub folders"/>
				</xsl:when>
				<xsl:otherwise>
					<img src="{$nosubicon}" alt="no sub folders"/>
				</xsl:otherwise>
			</xsl:choose>
			<img src="{@icon}" alt="folder icon" /> <strong><xsl:value-of select="communik8r:account_name" /></strong>
			<xsl:choose>
				<xsl:when test="count(communik8r:mailboxes/communik8r:mailbox) > 0">
					<ul>
						<xsl:for-each select="communik8r:mailboxes/communik8r:mailbox">
							<xsl:call-template name="folders">
								<xsl:with-param name="subicon" select="$subicon"/>
								<xsl:with-param name="nosubicon" select="$nosubicon"/>
								<xsl:with-param name="foldericon" select="$foldericon"/>
								<xsl:with-param name="acct_id" select="$acct_id"/>
							</xsl:call-template>
						</xsl:for-each>
					</ul>
				</xsl:when>
			</xsl:choose>
		</li>
	</xsl:template>
		
	<xsl:template name="folders">
		<xsl:param name="subicon"/>
		<xsl:param name="nosubicon"/>
		<xsl:param name="foldericon"/>
		<xsl:param name="acct_id"/>
		<xsl:param name="parent_mbox"/>

		<xsl:variable name="cur_folder">
			<xsl:choose>
				<xsl:when test="$parent_mbox">
					<xsl:value-of select="concat($parent_mbox, '.', communik8r:mailbox_name)"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="communik8r:mailbox_name"/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>

		<li id="{concat('mailbox_', $acct_id, '_', $cur_folder)}">
			<span>
				<xsl:choose>
					<xsl:when test="count(communik8r:mailbox) > 0">
						<img src="{$subicon}" alt="sub folders"/>
					</xsl:when>
					<xsl:otherwise>
						<img src="{$nosubicon}" alt="no sub folders"/>
					</xsl:otherwise>
				</xsl:choose>
				<img src="{$foldericon}" alt="folder icon" /> 
				<xsl:choose>
					<xsl:when test="communik8r:mailbox_name/@unread &gt; 0">
						<strong><xsl:value-of select="communik8r:mailbox_name" /> 
							(<xsl:value-of select="communik8r:mailbox_name/@unread" />)</strong>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="communik8r:mailbox_name" />
					</xsl:otherwise>
				</xsl:choose>
			</span>
			<xsl:choose>
				<xsl:when test="count(communik8r:mailbox) > 0">
					<ul>
						<xsl:for-each select="communik8r:mailbox">
							<xsl:call-template name="folders">
								<xsl:with-param name="subicon" select="$subicon" />
								<xsl:with-param name="nosubicon" select="$nosubicon" />
								<xsl:with-param name="foldericon" select="$foldericon" />
								<xsl:with-param name="acct_id" select="$acct_id" />
								<xsl:with-param name="parent_mbox" select="$cur_folder" />
							</xsl:call-template>
						</xsl:for-each>
					</ul>
				</xsl:when>
			</xsl:choose>
		</li>
	</xsl:template>
</xsl:stylesheet>
