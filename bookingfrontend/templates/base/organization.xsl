
<xsl:template match="data" xmlns:php="http://php.net/xsl">
	
    <div class="container my-container-top-fix wrapper">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a>
                    <xsl:attribute name="href">
                        <xsl:value-of select="php:function('get_phpgw_link', '/bookingfrontend/index.php', 'menuaction:bookingfrontend.uisearch.index')"/>
                    </xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Home')" />
                </a>
            </li>
            <li class="breadcrumb-item active"><xsl:value-of select="php:function('lang', 'organization')" /></li>       
        </ol>
        <div class="row">
            <div class="col-lg-6">
                
                <div class="row">
                    <div class="px-2 p-3">
                        <h3 id="main-item-header"></h3>
                        <i class="fas fa-map-marker d-inline"> </i>
                        <div class="building-place-adr">
                            <span id="item-street"></span>
                            <span id="item-zip-city"></span>
                        </div>
                        
                        <a id="item-web-href" class="d-block mt-2" href="">
                            <span id="item-web-url"></span>
                        </a>
                        
                    </div>
                    <p class="px-2 p-3">
                        Lorem ipsumdolor sit amet, consectetur adipiscing elit. Phasellus eget lorem pulvinar neque dignissim viverra at vel magna. In convallis dolor et tellus fringilla tincidunt. Duis gravida euismod nisi, a gravida nibh viverra dignissim. Aenean dapibus justo vitae sapien eleifend, vitae egestas arcu vehicula. Vivamus aliquam nibh vitae metus venenatis tempor quis vitae leo.
                    </p>
                    
                    <div class="building-accordion">
                        <div class="building-card">
                            <div class="building-card-header">
                                <h5 class="mb-0">
                                    <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false">
                                        Om klubben
                                    </button>
                                    <button data-toggle="collapse" data-target="#collapseOne" class="btn fas fa-plus float-right"></button>
                                    
                                </h5>
                                
                            </div>

                            <div id="collapseOne" class="collapse">
                                <div class="card-body">
                                    <p id="item-about"></p>
                                </div>
                            </div>
                        </div>
                        <div class="building-card">
                            <div class="building-card-header">
                                <h5 class="mb-0">
                                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false">
                                        Kontaktperson
                                    </button>
                                    <button data-toggle="collapse" data-target="#collapseThree" class="btn fas fa-plus float-right"></button>
                                </h5>
                            </div>
                            <div id="collapseThree" class="collapse">
                                <div class="card-body" >
                                    <div data-bind="foreach: contacts">
                                        <div class="d-block">
                                            <label class="font-weight-bold" data-bind="if: item_contact_person_name">Navn:&#160;</label>
                                            <span data-bind="text: item_contact_person_name"></span>
                                        </div>
                                        <div class="d-block">
                                            <label class="font-weight-bold" data-bind="if: item_contact_person_email">Email:&#160;</label>
                                            <span data-bind="text: item_contact_person_email"></span>
                                        </div>
                                        <div class="d-block mb-2">
                                            <label class="font-weight-bold" data-bind="if: item_contact_person_phone">Tel:&#160;</label>
                                            <span data-bind="text: item_contact_person_phone" class="d-inline-block"></span>
                                        </div>
                                    </div>
                                    <label class="font-weight-bold">Tel:&#160;</label>
                                    <span id="item_contact_org_phone"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    

            </div>
            
            <div class="col-lg-6 building-bookable">
                <h3 class=""><xsl:value-of select="php:function('lang', 'group')" /></h3>
                <div data-bind="foreach: groups">
                    <div class="custom-card">
                        <h2 data-bind="text: name"></h2>
                        <div class="d-block">
                            <label class="font-weight-bold" data-bind="if: group_contact_person_name">Navn:&#160;</label>
                            <span data-bind="text: group_contact_person_name"></span>
                        </div>
                        <div class="d-block">
                            <label class="font-weight-bold" data-bind="if: group_contact_person_email">Email:&#160;</label>
                            <span data-bind="text: group_contact_person_email"></span>
                        </div>
                        <div class="d-block mb-2">
                            <label class="font-weight-bold" data-bind="if: group_contact_person_phone">Tel:&#160;</label>
                            <span data-bind="text: group_contact_person_phone" class="d-inline-block"></span>
                        </div>
                    </div>
                </div>
                
            </div>
            
        </div>
        
        
        
        <div class="push"></div>
    </div>
    

    <script type="text/javascript">
            
            var script = document.createElement("script"); 
            script.src = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/" + "/js/base/organization.js";

            document.head.appendChild(script);			
        </script>
</xsl:template>
