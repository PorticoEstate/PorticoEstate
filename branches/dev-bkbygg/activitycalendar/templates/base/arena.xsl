  
<!-- $Id: arena.xsl 12604 2015-01-15 17:06:11Z nelson224 $ -->
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit"/>
		</xsl:when>
		<xsl:when test="view">
			<xsl:apply-templates select="view"/>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<!-- add / edit  -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit">
	<div>
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>

		<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="arena">
					<input type="hidden" name="id" value="{arena_id}"/>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'name')"/>
						</label>
						<input type="text" name="arena_name" id="arena_name" value="{arena_name}">
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>						
						</input>						
					</div>

					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'address')"/>
						</label>
						<input type="text" name="address" id="address" value="{address}">
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>							
						</input>
						<div id="address_container"></div>									
					</div>
			
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'address_number')"/>
						</label>
						<input type="text" name="address_no" id="address_no" value="{address_no}"/>										
					</div>
					
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'active_arena')"/>
						</label>
						<select id="arena_active" name="arena_active">							
							<xsl:apply-templates select="list_active_options/options"/>
						</select>											
					</div>
				</div>
			</div>
			<div class="proplist-col">
				<input type="submit" class="pure-button pure-button-primary" name="save_contract" value="{lang_save}" onMouseout="window.status='';return true;"/>
				<xsl:variable name="cancel_url">
					<xsl:value-of select="cancel_url"/>
				</xsl:variable>	
				<input type="button" class="pure-button pure-button-primary" name="cancel" value="{lang_cancel}" onMouseout="window.status='';return true;" onClick="window.location = '{cancel_url}';"/>
			</div>
		</form>
	</div>
</xsl:template>


<!-- view  -->
<xsl:template xmlns:php="http://php.net/xsl" match="view">
	<div>
		<form id="form" name="form" method="post" action="" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="arena">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'name')"/>
						</label>
						<xsl:value-of select="arena_name"/>
					</div>

					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'address')"/>
						</label>
						<xsl:value-of select="address"/>
					</div>
			
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'address_number')"/>
						</label>
						<xsl:value-of select="address_no"/>
					</div>
					
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'active_arena')"/>
						</label>
						<xsl:value-of select="active_value"/>
					</div>
				</div>
			</div>
			<div class="proplist-col">
				<xsl:variable name="edit_url">
					<xsl:value-of select="edit_url"/>
				</xsl:variable>
				<input type="button" class="pure-button pure-button-primary" name="edit" value="{lang_edit}" onMouseout="window.status='';return true;" onClick="window.location = '{edit_url}';"/>
				<xsl:variable name="cancel_url">
					<xsl:value-of select="cancel_url"/>
				</xsl:variable>	
				<input type="button" class="pure-button pure-button-primary" name="cancel" value="{lang_cancel}" onMouseout="window.status='';return true;" onClick="window.location = '{cancel_url}';"/>
			</div>
		</form>
	</div>
</xsl:template>


<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of select="name"/>
	</option>
</xsl:template>