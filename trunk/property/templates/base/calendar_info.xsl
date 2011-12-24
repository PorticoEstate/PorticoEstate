<!-- $Id$ -->

	<xsl:template name="calendar_info">
		<xsl:apply-templates select="cal_info"/>
	</xsl:template>

	<xsl:template match="cal_info">
		<!-- calendar stylesheet -->
		<link rel="stylesheet" type="text/css" media="all" href="{stylesheet}" title="win2k-cold-1"/>

		<!-- main calendar program -->
		<script type="text/javascript" src="{calendar_source}"/>

		<!-- language for the calendar -->
		<script type="text/javascript" src="{calendar_lang}"/>

		<!-- the following script defines the Calendar.setup helper function, which makes
			   adding a calendar a matter of 1 or 2 lines of code. -->
		  <script type="text/javascript" src="{calendar_setup_source}"/>

	  </xsl:template>
