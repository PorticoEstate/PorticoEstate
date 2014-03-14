<!-- $Id: choose_control_items.xsl 8267 2011-12-11 12:27:18Z sigurdne $ -->

<xsl:template name="case_row" xmlns:php="http://php.net/xsl">

  <xsl:param name="control_item_type" />
  <xsl:variable name="session_url">&amp;<xsl:value-of select="php:function('get_phpgw_session_url')" /></xsl:variable>	
  <li class="check_item_case">
    <xsl:choose>
      <xsl:when test="cases_array/child::node()">
        <h4>
          <span>
            <xsl:value-of select="control_item/title"/>
          </span>
          <xsl:if test="$control_item_type = 'control_item_type_2' or $control_item_type = 'control_item_type_3' or $control_item_type = 'control_item_type_4'">
            <span style="margin-left:3px;">(Måling)</span>
          </xsl:if>
        </h4>
        <span class="control_item_type ext_info">
          <xsl:value-of select="control_item/type" />
        </span>
        <ul>
          <xsl:for-each select="cases_array">
            <xsl:variable name="cases_id">
              <xsl:value-of select="id"/>
            </xsl:variable>
            <li>
              <!--  ==================== COL1: ORDERNR ===================== -->
              <div class="col_1">
                <span class="order_nr">
                  <xsl:number />
                </span>.
              </div>
              <!--  ==================== COL2: CASE CONTENT ===================== -->
              <div class="col_2">
                <div class="case_info">

	                 <xsl:choose>
	                      <xsl:when test="component_descr != ''">
			                  <div class="row">
			                    <label>
									<xsl:value-of select="php:function('lang','component')" />
			                    </label> 
			                  </div>
			                   <div class="component_descr">
			                    <xsl:value-of select="component_descr"/>
			                  </div>
	                     </xsl:when>
                    </xsl:choose>

                  <!-- STATUS -->
                  <xsl:if test="$control_item_type = 'control_item_type_2' or $control_item_type = 'control_item_type_3' or $control_item_type = 'control_item_type_4'">
            	
                    <div class="row first">
                      <label>Status:</label>
                      <span class="case_status">
                        <xsl:choose>
                          <xsl:when test="status = 0">Åpen</xsl:when>
                          <xsl:when test="status = 1">Lukket</xsl:when>
                          <xsl:when test="status = 2">Venter på tilbakemelding</xsl:when>
                        </xsl:choose>
                      </span>
                    </div>
                    
                    <!--  MEASUREMENT -->
                    <div class="row">
                      <label>Måleverdi:</label> 
                      <span class="measurement">
                        <xsl:value-of select="measurement"/>
                      </span>
                    </div>
                  </xsl:if>

                  <!--  DESCRIPTION -->
                  <div class="row">
                    <label>Beskrivelse:</label> 
                  </div>
                  <div class="case_descr">
                    <xsl:value-of select="descr"/>
                  </div>
                              
                  <!-- === QUICK EDIT MENU === -->
                  <div class="quick_menu">
                    <a class="quick_edit_case first" href="">
                      endre
                    </a>
                    <a class="close_case">
                      <xsl:attribute name="href">
                        <xsl:text>index.php?menuaction=controller.uicase.close_case</xsl:text>
                        <xsl:text>&amp;case_id=</xsl:text>
                        <xsl:value-of select="id"/>
                        <xsl:text>&amp;check_list_id=</xsl:text>
                        <xsl:value-of select="//check_list/id"/>
                        <xsl:text>&amp;phpgw_return_as=json</xsl:text>
                        <xsl:value-of select="$session_url"/>
                      </xsl:attribute>
                      lukk
                    </a>
                    <xsl:choose>
                      <xsl:when test="location_item_id = 0">
                        <a class="delete_case">
                          <xsl:attribute name="href">
                            <xsl:text>index.php?menuaction=controller.uicase.delete_case</xsl:text>
                            <xsl:text>&amp;case_id=</xsl:text>
                            <xsl:value-of select="id"/>
                            <xsl:text>&amp;check_list_id=</xsl:text>
                            <xsl:value-of select="//check_list/id"/>
                            <xsl:text>&amp;phpgw_return_as=json</xsl:text>
                            <xsl:value-of select="$session_url"/>
                          </xsl:attribute>
                          slett
                        </a>
                      </xsl:when>
                    </xsl:choose>
                  </div>
                </div>
                  
                <!--  =================== UPDATE CASE FORM =================== -->
                <form class="frm_update_case">
                  <xsl:attribute name="action">
                    <xsl:text>index.php?menuaction=controller.uicase.save_case</xsl:text>
                    <xsl:text>&amp;case_id=</xsl:text>
                    <xsl:value-of select="id"/>
                    <xsl:text>&amp;check_list_id=</xsl:text>
                    <xsl:value-of select="//check_list/id"/>
                    <xsl:text>&amp;control_item_type=</xsl:text>
                    <xsl:value-of select="//control_item/type" />
                    <xsl:text>&amp;phpgw_return_as=json</xsl:text>
                    <xsl:value-of select="$session_url"/>
                  </xsl:attribute>
                  <input type="hidden" name="control_item_type">
                   <xsl:attribute name="value"><xsl:value-of select="//control_item/type" /></xsl:attribute>
                 	</input>
                 	
                  <xsl:if test="$control_item_type = 'control_item_type_2' or $control_item_type = 'control_item_type_3' or $control_item_type = 'control_item_type_4'">
                    <!--  STATUS -->
                    <div class="row first">
                      <label>Status:</label> 
                      <select name="case_status">
                        <xsl:choose>
                          <xsl:when test="status = 0">
                            <option value="0" SELECTED="SELECTED">Åpen</option>
                            <option value="2">Venter på tilbakemelding</option>	
                          </xsl:when>
                          <xsl:when test="status = 1">
                            <option value="0">Åpen</option>
                            <option value="2">Venter på tilbakemelding</option>	
                          </xsl:when>
                          <xsl:when test="status = 2">
                            <option value="0">Åpen</option>
                            <option value="2" SELECTED="SELECTED">Venter på tilbakemelding</option>
                          </xsl:when>
                        </xsl:choose>
                      </select>
                    </div>
                    <xsl:choose>
                      <xsl:when test="$control_item_type = 'control_item_type_2'">
                        <!--  MEASUREMENT -->
                        <div class="row">
                          <label>Måleverdi:</label> 
                          <input type="text" name="measurement">
                            <xsl:attribute name="value">
                              <xsl:value-of select="measurement"/>
                            </xsl:attribute>
                          </input>
                        </div>
                      </xsl:when>
                      <xsl:when test="$control_item_type = 'control_item_type_3'">
                        <!--  MEASUREMENT -->
                        <div class="row">
                          <label class="comment">Velg verdi fra liste</label>
                          <select name="measurement">
                            <xsl:for-each select="../control_item/options_array">
                              <option>
                                <xsl:attribute name="value">
                                  <xsl:value-of select="option_value"/>
                                </xsl:attribute>
                                <xsl:value-of select="option_value"/>
                              </option>	
                            </xsl:for-each>
                          </select>
                        </div>
                      </xsl:when>
                      <xsl:when test="$control_item_type = 'control_item_type_4'">
                        <!--  MEASUREMENT -->
                        <div class="row">
                          <label class="comment">Velg verdi fra liste</label>
                            <xsl:for-each select="../control_item/options_array">
                          		<input type="radio" name="measurement" value="female">
                          		 <xsl:attribute name="value">
                                  <xsl:value-of select="option_value"/>
                                </xsl:attribute>
                                </input>
                          		<xsl:value-of select="option_value"/>
                            </xsl:for-each>
                        </div>
                      </xsl:when>
                    </xsl:choose>
                  </xsl:if>
                              
                  <!--  DESCRIPTION -->
                  <label>Beskrivelse:</label>
                  <div class="row"> 
                    <textarea name="case_descr">
                      <xsl:value-of select="descr"/>
                    </textarea>
                  </div>
                  <div>
                    <input class='btn_m' type='submit' value='Oppdater' />
                    <input class='btn_m cancel' type='button' value='Avbryt' />
                  </div>
                </form>
              </div>
              <!--  ==================== COL3: MESSAGE LINK ===================== -->
              <div class="col_3">
                <xsl:choose>
                  <xsl:when test="location_item_id > 0">
                    <a target="_blank">
                      <xsl:attribute name="href">
                        <xsl:text>index.php?menuaction=property.uitts.view</xsl:text>
                        <xsl:text>&amp;id=</xsl:text>
                        <xsl:value-of select="location_item_id"/>
                        <xsl:value-of select="$session_url"/>
                      </xsl:attribute>
                      Vis melding
                    </a>
                  </xsl:when>
                  <xsl:otherwise>
                    <span class="message">Ingen melding</span>
                  </xsl:otherwise>
                </xsl:choose>
              </div>
            </li>
          </xsl:for-each>
        </ul>
      </xsl:when>	
    </xsl:choose>
  </li>
</xsl:template>
