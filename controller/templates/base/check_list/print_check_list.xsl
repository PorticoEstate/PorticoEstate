<!-- $Id: choose_control_items.xsl 8267 2011-12-11 12:27:18Z sigurdne $ -->

<xsl:template match="data">

<ul class="groups">
	<xsl:for-each select="saved_groups_with_items_array">
		<li class="drag_group list_item">
			<h3><span class="group_order_nr"><xsl:number/></span>. <xsl:value-of select="control_group/group_name"/></h3>
	
			<form action="index.php?menuaction=controller.uicontrol_item.save_item_order" class="frm_save_order">
			   	<xsl:variable name="control_group_id"><xsl:value-of select="control_group/id"/></xsl:variable>
				<input type="hidden" name="control_group_id" value="{$control_group_id}" />
		
			 	<ul id="list">
					<xsl:for-each select="control_items">
						<xsl:variable name="control_item_id"><xsl:value-of select="id"/></xsl:variable>
						<xsl:variable name="order_tag">
							<xsl:choose>
								<xsl:when test="order_nr > 0">
									<xsl:value-of select="order_nr"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:number/>
								</xsl:otherwise>
							</xsl:choose>:<xsl:value-of select="id"/>
						</xsl:variable>
														
			 			<li class="list_item">
			 				<span class="order_nr"><xsl:number/></span>. <xsl:value-of select="title"/><input type="hidden" name="order_nr[]" value="{$order_tag}" />
			 			</li>
					</xsl:for-each>
				</ul>
			</form>
		</li>
	</xsl:for-each>
</ul>

<style>
.btn{
	background: none repeat scroll 0 0 #2647A0;
    color: #FFFFFF;
    display: inline-block;
    margin-right: 5px;
    padding: 5px 10px;
    text-decoration: none;
    border: 1px solid #173073;
    cursor: pointer;
}

ul{
	list-style: none outside none;
}

li{
	list-style: none outside none;
}

ul.groups li {
    padding: 3px 0;
}

ul.groups li.odd{
    background: none repeat scroll 0 0 #DBE7F5;
}

ul.groups h3 {
    font-size: 18px;
    margin: 0 0 5px;
}

</style>
<a style="margin:20px 0 0 40px;" href="#print" class="btn" onClick="window.print()">Skriv ut</a>

</xsl:template>
