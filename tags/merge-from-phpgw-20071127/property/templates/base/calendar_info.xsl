<!-- $Id: calendar_info.xsl,v 1.1 2005/01/17 10:03:18 sigurdne Exp $ -->

	<xsl:template name="calendar_info">
		<xsl:apply-templates select="cal_info"/>
	</xsl:template>

	<xsl:template match="cal_info">
		  <!-- calendar stylesheet -->
		  <link rel="stylesheet" type="text/css" media="all" href="{stylesheet}" title="win2k-cold-1" />
		
		  <!-- main calendar program -->
		  <script type="text/javascript" src="{calendar_source}"></script>
	
		  <!-- language for the calendar -->
		  <script type="text/javascript" src="{calendar_lang}"></script>

		  <!-- the following script defines the Calendar.setup helper function, which makes
		       adding a calendar a matter of 1 or 2 lines of code. -->
		  <script type="text/javascript" src="{calendar_setup_source}"></script>

	</xsl:template>
