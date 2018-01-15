<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <script type="text/javascript">
        //		var selected_part_of_towns = "<xsl:value-of select="selected_part_of_towns"/>";
    </script>


    <a href="#" class="scrollup">
        <xsl:value-of select="php:function('lang', 'scroll to top')" />
    </a>
    <div class="jumbotron">

            <div class="header-text">          
                <a href="{site_url}">
                    <p class="header-style-m">Din portal til</p>
                    <p class="header-style-l">AKTIVITETER OG LOKALER</p>
                    <p class="header-style-m">Nært deg.</p>
                </a>
            </div>
    </div>
    <div class="container-fluid main-container" id="main-page">

        <section class="text-center">
            <div class="container-fluid">
                <p class="lead">Søk etter anlegg eller ressurs som du ønsker å reservere</p>
                <p>Bruk søkefeltene under, eller trykk avansert søk for filtrering</p>
            </div>
        </section>
        <!-- Insert margins here instead of <br>-->
        <br/>
        <br/>
    </div>
    

    <div class="container">
        <form method="GET" id="search">
            <input type="hidden" id="menuaction" name="menuaction" value="bookingfrontend.uisearch.index" />
        </form>
      
             
        <div id="building_container" class="search-container main-search">
           <div class="form-group">
               
               
               <div class="input-group">
                
                    <input id="field_searchterm"  class="form-control form-control-lg text-center" name="searchterm" type="text">
                        <xsl:attribute name="value">
                            <xsl:value-of select="searchterm"/>
                        </xsl:attribute>
                        <xsl:attribute name="placeholder">
                            <xsl:text> Søk i fritekst</xsl:text>
                        </xsl:attribute>
                    </input>
                    <span class="input-group-btn">

                        <button class="btn btn-default search-button" id="submit_searchterm" type="submit">
                            <i class="icon ion-search"/>
                        </button>
                    </span>

  
                </div>
            <!--     <input id="field_building_name" name="building_name" class="form-control form-control-lg text-center" type="text">
                    <xsl:attribute name="value">
                        <xsl:value-of select="building_name"/>
                    </xsl:attribute>
                    <xsl:attribute name="placeholder">
                        <xsl:text> Søk etter lokale/bygning/anlegg</xsl:text>
                    </xsl:attribute>
                </input>
                
                <input id="field_building_id" name="building_id" class="form-control" type="hidden">
                    <xsl:attribute name="value">
                        <xsl:value-of select="building_id"/>
                    </xsl:attribute>
                </input> -->
      
                         <p id="adv-search-toggler" class="adv-search-button text-right">Avansert søk</p>
           
            </div> 
        </div> 
        
    </div> 

  
   
                                  
                
          
    <div id="advance-search-container" class="container-fluid advance-search bg-light">
        <div class="container advance-search-padding">
            <div class="row">
                <div class="col-lg-4">
                    <div class="pure-u-1 select-box">
                        <p class="lead">
                            <xsl:value-of select="php:function('lang', 'part of town')" />
                        </p>
                        <ul id="part_of_town">
                            <xsl:for-each select="part_of_towns">
                                <li>
                                    <label class="control control--checkbox">
                                        <input  type="checkbox" name="part_of_town[]">
                                            <xsl:attribute name="value">
                                                <xsl:value-of select="id"/>
                                            </xsl:attribute>
                                            <xsl:if test="checked = 1">
                                                <xsl:attribute name="checked">
                                                    <xsl:text>checked</xsl:text>
                                                </xsl:attribute>
                                            </xsl:if>
                                        </input>
                                        <xsl:value-of select="name"/>
                                        <div class="control__indicator"></div>
                                    </label>
                                </li>
                            </xsl:for-each>
                        </ul>
                    </div>
                </div>    
            
                <div class="col-lg-4">
                    <div class="pure-u-1 select-box">
                        <p class="lead">
                            <!--xsl:value-of select="php:function('lang', 'Activity')" /-->
                            Velg hovedkategori/avdeling
                        </p>
                        <ul id="top_level">
                            <xsl:for-each select="top_levels">
                                <li>
                                    <label class="control control--checkbox">
                                        <input type="checkbox" name="top_levels[]">
                                            <xsl:attribute name="value">
                                                <xsl:value-of select="id"/>
                                            </xsl:attribute>
                                            <xsl:attribute name="id">
                                                <xsl:value-of select="location"/>
                                            </xsl:attribute>
                                            <xsl:if test="checked = 1">
                                                <xsl:attribute name="checked">
                                                    <xsl:text>checked</xsl:text>
                                                </xsl:attribute>
                                            </xsl:if>
                                        </input>
                                        <xsl:value-of select="name"/>
                                        <div class="control__indicator"></div>
                                    </label>
                                </li>
                            </xsl:for-each>
                        </ul>
                    </div>
                
                </div>  
            
                <div class="col-lg-4">
                    <div class="pure-u-1 select-box">
                        <p class="lead">
                            <!--xsl:value-of select="php:function('lang', 'type')" /-->
                            Vis kun treff som er:
                        </p>
                        <ul id="search_type">
                            <li>
                                <label class="control control--checkbox">
                                    <input type="checkbox" name="search_type[]" value="building"/>
                                    <xsl:value-of select="php:function('lang', 'building')" />
                                    <div class="control__indicator"></div>
                                </label>
                            </li>
                            <li>
                                <label class="control control--checkbox">
                                    <input type="checkbox" name="search_type[]" value="resource"/>
                                    <xsl:value-of select="php:function('lang', 'resource')" />
                                    <div class="control__indicator"></div>
                                </label>
                            </li>
                            <li>
                                <label class="control control--checkbox">
                                    <input type="checkbox" name="search_type[]" value="organization"/>
                                    <xsl:value-of select="php:function('lang', 'organization')" />
                                    <div class="control__indicator"></div>
                                </label>
                            </li>
                            <li>
                                <label class="control control--checkbox">
                                    <input type="checkbox" name="search_type[]" value="event"/>
                                    <xsl:value-of select="php:function('lang', 'event')" />
                                    <div class="control__indicator"></div>
                                </label>
                            </li>
                        </ul>
                    </div>
                </div>  
            </div>  
          
            <button id="update-search-result" class="btn btn-main">
                <xsl:value-of select="php:function('lang', 'Update results')" />
            </button>
         
        </div> 
         
        
    </div>                                                                                             
                                                                                 
    <div class="container-fluid result">                                                                                                         
                                                                                                                         
        <div class="container">                                                                                                                                             
            
            <div class="container" style="margin-top: 5px">
            <div id = "total_records_top"></div> 
        </div>  
            <div id="result"></div>
        </div>
    </div>
	
</xsl:template>
