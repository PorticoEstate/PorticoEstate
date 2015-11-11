<!-- $Id$ -->
<xsl:template match="helpdesk" xmlns:php="http://php.net/xsl">
	
    <xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
	<xsl:variable name="location_id"><xsl:value-of select="location_id"/></xsl:variable>
	
	<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned">
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<div id="{$location_id}">
        	<xsl:choose>
				<xsl:when test="normalize-space(//header/selected_location) != ''">
					<div class="toolbar-container">
							<div>
								<xsl:for-each select="filters">
									<xsl:variable name="name"><xsl:value-of select="name"/></xsl:variable>
									<select id="{$name}" name="{$name}">
										<xsl:for-each select="list">
											<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
											<xsl:choose>
												<xsl:when test="id = 'NEW'">
													<option value="{$id}" selected="selected">
														<xsl:value-of select="name"/>
													</option>
												</xsl:when>
												<xsl:otherwise>
													<xsl:choose>
														<xsl:when test="selected = 'selected'">
															<option value="{$id}" selected="selected">
																<xsl:value-of select="name"/>
															</option>
														</xsl:when>
														<xsl:otherwise>
															<option value="{$id}">
																<xsl:value-of select="name"/>
															</option>
														</xsl:otherwise>
													</xsl:choose>
												</xsl:otherwise>
											</xsl:choose>
										</xsl:for-each>
									</select>									
								</xsl:for-each>
							</div>
		            </div>
		            <div class="tickets">
		            	<table cellpadding="2" cellspacing="2" width="95%" align="center">
					        <xsl:choose>
					            <xsl:when test="msgbox_data != ''">
					                <tr>
					                    <td align="left" colspan="3">
					                        <xsl:call-template name="msgbox"/>
					                    </td>
					                </tr>
					            </xsl:when>
					        </xsl:choose>
					    </table>
							<xsl:for-each select="datatable_def">
								<xsl:if test="container = 'datatable-container_0'">
									<xsl:call-template name="table_setup">
										<xsl:with-param name="container" select ='container'/>
										<xsl:with-param name="requestUrl" select ='requestUrl' />
										<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
										<xsl:with-param name="tabletools" select ='tabletools' />
										<xsl:with-param name="data" select ='data' />
										<xsl:with-param name="config" select ='config' />
									</xsl:call-template>
								</xsl:if>
							</xsl:for-each>
		            </div>
				</xsl:when>
				<xsl:otherwise>
					<div class="tickets">
		            	<xsl:value-of select="php:function('lang', 'no_buildings')"/>
		            </div>
				</xsl:otherwise>
			</xsl:choose>
            
        </div>
    </div>
	</form>
	<script type="text/javascript" class="init">

		<xsl:for-each select="filters">
			<xsl:if test="type = 'filter'">
				$('select#<xsl:value-of select="name"/>').change( function() 
				{
					<xsl:value-of select="extra"/>
					filterData('<xsl:value-of select="name"/>', $(this).val());
				});
			</xsl:if>
		</xsl:for-each>

		<![CDATA[
			function filterData(param, value)
			{
				oTable0.dataTableSettings[0]['ajax']['data'][param] = value;
				oTable0.fnDraw();
			}
		]]>
		
	</script>
</xsl:template>

<xsl:template match="lightbox_name" xmlns:php="http://php.net/xsl">
</xsl:template>

<xsl:template match="add_ticket" xmlns:php="http://php.net/xsl">
	<xsl:choose>
        <xsl:when test="normalize-space(redirect) != ''">
            <script>
            	window.parent.location = '<xsl:value-of select="redirect"/>';
            	window.close();
            </script>
        </xsl:when>
    </xsl:choose>
    <h2>Ny melding</h2>
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


            <xsl:if test="noform != 1">
                <tr>
                   <td class="th_text" valign="top">
                        <xsl:value-of select="php:function('lang', 'category')" />
                    </td>
                    <td class="th_text" valign="top">
						<select name="values[cat_id]" >
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'category')" />
							</xsl:attribute>
							<xsl:apply-templates select="category_list"/>
						</select>			
                    </td>
                </tr>
                <tr>
                    <td class="th_text" valign="top">
                        <xsl:value-of select="php:function('lang', 'subject')" />
                    </td>
                    <td class="th_text" valign="top">
                        <input type="text" name="values[title]" value="{title}"/>
                    </td>
                </tr>

                <tr>
                    <td class="th_text" valign="top">
                        <xsl:value-of select="php:function('lang', 'locationdesc')" />
                    </td>
                    <td class="th_text" valign="top">
                        <input type="text" name="values[locationdesc]" value="{locationdesc}"/>
                    </td>
                </tr>

                <tr>
                    <td valign="top">
                        <xsl:value-of select="php:function('lang', 'description')" />
                    </td>
                    <td>
                        <textarea cols="60" rows="10" name="values[description]" wrap="virtual" onMouseout="window.status='';return true;">
                            <xsl:value-of select="description"/>
                        </textarea>
                    </td>
                </tr>

                <tr>
                    <td valign="top">
                        <xsl:value-of select="php:function('lang', 'file')" />
                    </td>
                    <td>
                        <input type="file" name="file" size="50">
                            <xsl:attribute name="title">
                                <xsl:value-of select="php:function('lang', 'file')" />
                            </xsl:attribute>
                        </input>
                    </td>
                </tr>

                <tr height="50">
                    <td>
                        <xsl:variable name="lang_send"><xsl:value-of select="php:function('lang', 'send')" /></xsl:variable>
                        <input type="submit" name="values[save]" value="{$lang_send}" title='{$lang_send}'/>
                    </td>
                </tr>
                 <tr>
                  <td>
                  </td>
                  <td >

						<xsl:apply-templates select="custom_attributes/attributes"/>

                    </td>
                </tr>

            </xsl:if>
        </table>
    </form>
</xsl:template>

<xsl:template match="category_list">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected" />
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>


