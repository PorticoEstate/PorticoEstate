<!--
	Function
	phpgw:conditional( expression $test, mixed $true, mixed $false )
	Evaluates test expression and returns the contents in the true variable if
	the expression is true and the contents of the false variable if its false

	Returns mixed
-->
<func:function name="phpgw:conditional">
	<xsl:param name="test"/>
	<xsl:param name="true"/>
	<xsl:param name="false"/>

	<func:result>
		<xsl:choose>
			<xsl:when test="$test">
	        	<xsl:value-of select="$true"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$false"/>
			</xsl:otherwise>
		</xsl:choose>
  	</func:result>
</func:function>

<!--
	Function
	phpgw:max( number $first, number $second, string $subject )
	Returns the larges number of first and second

	Returns number
-->
<func:function name="phpgw:max">
	<xsl:param name="first"/>
	<xsl:param name="second"/>

	<func:result>
		<xsl:value-of select="phpgw:conditional($first &gt; $second, $first, $second)"/>
  	</func:result>
</func:function>

<!--
	Function
	phpgw:min( number $first, number $second, string $subject )
	Returns the smalles number of first and second

	Returns number
-->
<func:function name="phpgw:min">
	<xsl:param name="first" />
	<xsl:param name="second" />

	<func:result>
		<xsl:value-of select="phpgw:conditional($first &lt; $second, $first, $second)"/>
  	</func:result>
</func:function>

<!--
	Function
	phpgw:replace( string $search, string $replace, string $subject )
	Replace first occurrence of the search string with the replacement string in subject

	Returns string
	If search string is found subject with replaced search string is returned
	If search string is not found subject is returned unchanged
-->
<func:function name="phpgw:replace">
	<xsl:param name="search" />
	<xsl:param name="replace" />
	<xsl:param name="subject" />

	<func:result>
		<xsl:choose>
    		<xsl:when test="contains($subject, $search)">
				<xsl:value-of select="substring-before($subject, $search)"/>
				<xsl:value-of select="$replace"/>
				<xsl:value-of select="substring-after($subject, $search)"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$subject"/>
	    	</xsl:otherwise>
		</xsl:choose>
  	</func:result>

</func:function>

<!--
	Template
	match=phpgw()

	Entrypoint for this tempalte
-->
<xsl:template match="phpgw">
	<xsl:apply-templates select="datatable/menu" />

		<!--  xsl:choose>
			<xsl:when test="datatable/locdata!=''">
				<div class="toolbar-first">
					<div class="toolbar">

					</div>
				</div>
			</xsl:when>
		</xsl:choose>-->
		<div class="toolbar-container">
			<div class="toolbar">
				<xsl:apply-templates select="datatable/actions" />
			</div>
		</div>
	<xsl:apply-templates select="datatable" />

	<div><br/></div>

	<xsl:choose>
		<xsl:when test="datatable/actions/down-toolbar/fields/field!=''">
			<div class="toolbar-container">
				<div class="toolbar">
					<form>
						<xsl:apply-templates select="datatable/actions/down-toolbar/fields/field" />
					</form>
				</div>
			</div>
		</xsl:when>
	</xsl:choose>

</xsl:template>

<!--
	Template
	link_builder( 	string $text, bool $active, string $url,
					string $paramteres, string $class, bool $no-sort-params )

	Constructs a link tag based on input parameters, sort fields and the fields
	from forms in the actions element

	The link is constructed by combinding the following:
	$url +
	field[n] = value[n] (for each field in actions which is not empty) +
	order = value from //datatable/sorting/order +
	sort  = value from //datatable/sorting/sort +
	$parameters

	Parameters
	$text 		(string) (req) : inner HTML of link tag
	$active 	(bool) 	 (opt) : if false a span element with class attribute
								 and text as inner HTML is rendered instead
								 (Default: true)
	$url 		(string) (opt) : if empty //datatable/config/base_url is used
	$parameters (string) (opt) : added to end of url
	$class		(string) (opt) : used for class attribute of generated tag
	$no-sort-params(string)(opt):if true sort params are omitted from
								 constructed url. (Default: false)

	Returns link element if active is true and span element if active is false

	Example

	XML:
	<datatable>
		<config>
			<base_url>http://some.base.url?action=blah</base_url>
		</config>
		<actions>
			<...>
				<field>
					<name>var_1</name>
					<value>value_1</name>
				</field>
				<field>
					<name>var_2</name>
					<value></name>
				</field>
			</...>
		</actions>
		<sorting>
			<order>column_1</order>
			<sort>ASC</sort>
		</sorting>
	</datatable>

	link_builder( 'My Link', true, '', 'something=that', 'my-class', false ):
	<a href="http://some.base.url?action=blah&var_1=value_1&order=column_1&sort\
	=ASC&something=that" class="my-class">My Link</a>
-->
<xsl:template name="link_builder">
	<xsl:param name="text">Link text</xsl:param>
	<xsl:param name="active">1</xsl:param>
	<xsl:param name="url"/>
	<xsl:param name="parameters"/>
	<xsl:param name="class"/>
	<xsl:param name="no-sort-params"/>

  	<xsl:if test="$active">
    	<xsl:variable name="href">
	      	<xsl:value-of select="phpgw:conditional($url, $url, //datatable/config/base_url)"/>

			<!-- Find each field under //datatable/actions which aren't empty or of type submit -->
	        <xsl:for-each select="//datatable/actions//field[not(value='') and not(type='submit')]">
				<xsl:if test="not(type='checkbox') or checked='1'">
					<xsl:text>&amp;</xsl:text>
					<xsl:value-of select="name"/>=<xsl:value-of select="value"/>
				</xsl:if>
	        </xsl:for-each>

			<xsl:if test="not($no-sort-params)">
	        	<xsl:if test="//datatable/sorting/order">&amp;order=<xsl:value-of select="//datatable/sorting/order"/></xsl:if>
	        	<xsl:if test="//datatable/sorting/sort">&amp;sort=<xsl:value-of select="//datatable/sorting/sort"/></xsl:if>
	        </xsl:if>

	        <xsl:if test="$parameters">
	            <xsl:text>&amp;</xsl:text>
	            <xsl:value-of select="$parameters"/>
			</xsl:if>
		</xsl:variable>

    	<a href="{$href}" class="{$class}">
			<xsl:value-of select="$text"/>
    	</a>
	</xsl:if>

  	<xsl:if test="not($active)">
    	<span class="{$class}">
			<xsl:value-of select="$text"/>
    	</span>
  	</xsl:if>
</xsl:template>

<xsl:template match="actions">
	<xsl:apply-templates select="form" />
</xsl:template>

<!--
	Template
	match=form()

	Constructs form tag a that that goes into the toolbar.

	Form tag attributes:
	method = form/method or 'POST' if method field is missing
	action = form/action or //database/config/base_url if action field is missing
-->
<xsl:template match="form">
	<form>
		<xsl:attribute name="method">
			<xsl:value-of select="phpgw:conditional(not(method), 'POST', method)"/>
		</xsl:attribute>

		<xsl:attribute name="action">
			<xsl:value-of select="phpgw:conditional(not(action), //datatable/config/base_url, action)"/>
		</xsl:attribute>

		<xsl:apply-templates select="fields" />
	</form>
</xsl:template>

<xsl:template match="fields">
	<xsl:apply-templates select="field" />
</xsl:template>

<!--
	Template
	match=field()

	Constructs inputfields with labels that goes into form tags.

	Type of inputfield is decided by the type field.
	If text field is obmitted no label is created. This allows the same code to
	be used for creating for ex. submit buttons.

	Currently the following inputfields are supported:
	* input
	* password
	* hidden
	* checkbox
	* submit

	Label tag attributes:
		id: field/id or unique autogenerated if id is missing
	Label tag innerHTML:
		field/text, label is not rendered if field/text is empty

	Input tag attributes / value:
		id: field/id or unique autogenerated if id is missing
		type: field/type
		name: field/name
		value: field/value
		size: field/size
		checked: if field/type=checkbox and field/checked == 1 checked=checked
-->
<xsl:template match="field">
	<xsl:variable name="id" select="phpgw:conditional(id, id, generate-id())"/>
	<xsl:variable name="align">
		<xsl:choose>
			<xsl:when test="style='filter'">float:left</xsl:when>
			<xsl:otherwise>float:right</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
	<div style="{$align}" class="field">
		<xsl:if test="text">
			<label for="{$id}">
				<xsl:value-of select="text"/>
				<xsl:text> </xsl:text>
			</label>
		</xsl:if>

		<xsl:choose>
			<xsl:when test="type='link'">
				<a id="{id}" href="#" onclick="{url}" tabindex="{tab_index}"><xsl:value-of select="value"/></a>
			</xsl:when>
			<xsl:when test="type='label_date'">
				<table><tbody><tr><td><span id="txt_start_date"></span></td></tr><tr><td><span id="txt_end_date"></span></td></tr></tbody></table>
			</xsl:when>
			<xsl:when test="type='label'">
				<xsl:value-of select="value"/>
			</xsl:when>
			<xsl:when test="type='img'">
				<img id="{id}" src="{src}" alt="{alt}" title="{alt}" style="cursor:pointer; cursor:hand;" tabindex="{tab_index}" />
			</xsl:when>
			<xsl:when test="type='select'">
				<select id="{id}" name="{name}" alt="{alt}" title="{alt}" style="cursor:pointer; cursor:hand;" tabindex="{tab_index}">
					<xsl:if test="onchange">
						<xsl:attribute name="onchange"><xsl:value-of select="onchange"/></xsl:attribute>
					</xsl:if>
 		     		<xsl:for-each select="values">
						<option value="{id}">
							<xsl:if test="selected != 0">
								<xsl:attribute name="selected" value="selected" />
							</xsl:if>
							<xsl:value-of disable-output-escaping="yes" select="name"/>
						</option>
 		     		</xsl:for-each>
				</select>			
			</xsl:when>
			<xsl:otherwise>
				<input id="{$id}" type="{type}" name="{name}" value="{value}" class="{type}">
					<xsl:if test="size">
						<xsl:attribute name="size"><xsl:value-of select="size"/></xsl:attribute>
					</xsl:if>

					<xsl:if test="tab_index">
						<xsl:attribute name="tabindex"><xsl:value-of select="tab_index"/></xsl:attribute>
					</xsl:if>

					<xsl:if test="type = 'checkbox' and checked = '1'">
						<xsl:attribute name="checked">checked</xsl:attribute>
					</xsl:if>

					<xsl:if test="readonly">
						<xsl:attribute name="readonly">'readonly'</xsl:attribute>
						<xsl:attribute name="onMouseout">window.status='';return true;</xsl:attribute>
					</xsl:if>

					<xsl:if test="onkeypress">
						<xsl:attribute name="onkeypress"><xsl:value-of select="onkeypress"/></xsl:attribute>
					</xsl:if>
					<xsl:if test="class">
						<xsl:attribute name="class"><xsl:value-of select="class"/></xsl:attribute>
					</xsl:if>

				</input>
			</xsl:otherwise>
		</xsl:choose>

	</div>
</xsl:template>

<!--
	Template
	match=datatable()

	Entrypoint for this datatable. Renders pagination and datatable.
-->
<xsl:template match="datatable">
	  <!--    <div class="pagination-container">
				<xsl:apply-templates select="pagination" />
		  </div> -->

		<xsl:choose>
			<xsl:when test="//exchange_values!=''">
				<script type="text/javascript">
					//function Exchange_values(thisform)
					function valida(data,param)
					{
						<xsl:value-of select="//valida"/>
					}

					function Exchange_values(data)
					{
						<xsl:value-of select="//exchange_values"/>
					}

				</script>
			</xsl:when>
			<xsl:otherwise>
				<script type="text/javascript">
					function Exchange_values(data)
					{

					}
				</script>
			</xsl:otherwise>

		</xsl:choose>

	 <br/>
	<div id="message"> </div>
	<div id="paging"> </div>
  	<div class="datatable-container">
    <!-- 
    	<table class="datatable">
      		 <xsl:apply-templates select="headers" />
      		<xsl:apply-templates select="rows" />
      		
    	</table>
    -->
  	</div>
   	<div id="datatable-detail" style="background-color:#000000;color:#FFFFFF;display:none">
		<div class="hd" style="background-color:#000000;color:#000000; border:0; text-align:center"> Record Detail </div>
		<div class="bd" style="text-align:center;"> </div>
	</div>

  	<div id="footer"> </div>
  	<xsl:call-template name="datatable-yui-definition" />


</xsl:template>



<!--
	Template
	match=pagination()

	Constructs pagination links for the dataset like this

	Records x - x of x 	Previous 1 ... 4 5 6 7 |8| 9 10 ... 11 Next

	Previous / Next = Link
	1 - 11 = Link
	|8| = current page

	The pagaination logic is as follows:
	if current page > 0 show active linkt to previous page and 1st page
	if current page < last_page show active linkt to previous page and last page

	if curent page - pages to show/2 > 1 show ...
	Show pages from current - pages to show/2 to current + pages to show/2
	if current page + pages to show/2 > last page show ...

	The pagesToShow variable decides how many pages to show in total,
	but link to first and last page if ellipsis are in use is not
	accounted for.
-->
<xsl:template match="pagination">
	<div class="pagination">
		<!-- Calculate page related variables -->
		<xsl:variable name="pagesToShow" select="8"/>
		<xsl:variable name="activePage" select="floor(records_start div records_limit) + 1"/>
		<xsl:variable name="totalPages" select="ceiling(records_total div records_limit)"/>
		<xsl:variable name="minLastPage" select="phpgw:min($pagesToShow div 2, $totalPages - $activePage)" />
		<xsl:variable name="startPage" select="phpgw:max($activePage - $pagesToShow + $minLastPage, 1)" />
		<xsl:variable name="endPage" select="phpgw:min($totalPages, $activePage + $pagesToShow - ($activePage - $startPage))" />

		<!-- Show info about records in dataset -->
		<div class="resultset">
			<xsl:if test="records_returned &gt; 0">
				<!-- Replace values $1, $2 and $3 in the transladed string. Could probably be cleaned up -->
				<xsl:variable name="tmp1" select="phpgw:replace('$1', records_start + 1, lang/overview)"/>
				<xsl:variable name="tmp2" select="phpgw:replace('$2', records_start + records_returned, $tmp1)"/>
				<xsl:value-of select="phpgw:replace('$3', records_total, $tmp2)"/>
           </xsl:if>
		</div>

		<div class="navigation">
			<!-- Previous link -->
			<xsl:call-template name="link_builder">
				<xsl:with-param name="text" select="lang/previous"/>
				<xsl:with-param name="parameters">
          			<xsl:text>start=</xsl:text>
					<xsl:value-of select="phpgw:max( records_start - records_returned, 0)"/>
				</xsl:with-param>
				<xsl:with-param name="active" select="records_start > 0"/>
				<xsl:with-param name="class">previous</xsl:with-param>
			</xsl:call-template>

			<!-- Link to first page and ellipsis -->
			<xsl:if test="$startPage &gt; 1">
				<xsl:call-template name="pagination-pages-loop">
					<xsl:with-param name="currentPage" select="1"/>
					<xsl:with-param name="endPage" select="1"/>
				</xsl:call-template>
				<xsl:if test="$startPage &gt; 2">
					<span class="ellipsis">...</span>
				</xsl:if>
			</xsl:if>

			<!-- Actual page link loop -->
			<xsl:call-template name="pagination-pages-loop">
				<xsl:with-param name="activePage" select="$activePage"/>
				<xsl:with-param name="currentPage" select="$startPage"/>
				<xsl:with-param name="endPage" select="$endPage"/>
			</xsl:call-template>

			<!-- Ellipsis -->
			<xsl:if test="$endPage &lt; $totalPages">
				<span class="ellipsis">...</span>
				<xsl:call-template name="pagination-pages-loop">
					<xsl:with-param name="currentPage" select="$totalPages"/>
          			<xsl:with-param name="endPage" select="$totalPages"/>
        		</xsl:call-template>
      		</xsl:if>

			<!--  Link to last page -->
      		<xsl:call-template name="link_builder">
        		<xsl:with-param name="text" select="lang/next"/>
        		<xsl:with-param name="parameters">
          			<xsl:text>start=</xsl:text>
          			<xsl:value-of select="records_start + records_limit"/>
        		</xsl:with-param>
        		<xsl:with-param name="active" select="(records_start + records_returned) &lt; records_total"/>
        		<xsl:with-param name="class">next</xsl:with-param>
      		</xsl:call-template>
      </div>
  </div>
</xsl:template>

<!--
	Template
	pagination-pages-loop( 	int $activePage, int $currentPage, int endPage )

	Construct page links from $currentPage to $endPage with $activePage marked
	width plass "current-page"

	This works by increasing the $currentPage each time a page is rendered and
	call the template again if currentPage < $endPage

	Parameters
	$activePage 	(int) (req) : First page to render
	$currentPage 	(int) (opt) : Active page that should not be a link
	$endPage 		(int) (req) : Last page to render

-->
<xsl:template name="pagination-pages-loop">
	<xsl:param name="activePage">-1</xsl:param>
	<xsl:param name="currentPage">1</xsl:param>
	<xsl:param name="endPage">1</xsl:param>

  	<xsl:call-template name="link_builder">
    	<xsl:with-param name="text" select="$currentPage"/>

    	<xsl:with-param name="parameters">
      		<xsl:text>start=</xsl:text>
      		<xsl:value-of select="(number($currentPage) - 1) * records_limit"/>
    	</xsl:with-param>

    	<xsl:with-param name="active" select="$currentPage != $activePage"/>
    	<xsl:with-param name="class">
      		<xsl:if test="$currentPage = $activePage">current-page</xsl:if>
    	</xsl:with-param>
  	</xsl:call-template>

  	<xsl:if test="$currentPage &lt; $endPage">
    	<xsl:call-template name="pagination-pages-loop">
      		<xsl:with-param name="currentPage" select="$currentPage+1"/>
      		<xsl:with-param name="activePage" select="$activePage"/>
      		<xsl:with-param name="endPage" select="$endPage"/>
    	</xsl:call-template>
  	</xsl:if>
</xsl:template>

<!--
	Template
	match=headers()

	Construct header columns for datatable with clickable links for sorting.
	Rowactions are added to the end of the headers.

	First goes through every headers/header element and creates TH elements.
	Second goes through every //datatable/rowactions/action element and creates
	TH elements.

	If a column is sorted its class attribute with 'sorted-desc'o r 'sorted-asc'
	is added depending on direction.

	Parameters for header elements
	$visible 	(int) (opt) 	: If = 0 the header is not rendered
	$sortable	(int) (opt) 	: If != 0 a link for sorting on the column is
								  created based on current sorting column,
								  direction and $sortcolumn
	$name		(string) (req)	: Name used for constructing sort url
	$sortcolumn	(string) (opt)	: Used instead of $name for sorting
	$text 		(string) (req) 	: Used for th innerHTML


	Parameters for action elements
	$text 		(string) (req) 	: Used for th innerHTML
-->
<xsl:template match="headers">
	<thead>
		<tr>
      		<xsl:for-each select="header[not(visible = 0)]">
        		<th>
          			<xsl:choose>
               			<xsl:when test="not(sortable = 0)">
               				<xsl:variable name="sortcolumn" select="phpgw:conditional(sortcolumn, sortcolumn, name)"/>

							<xsl:call-template name="link_builder">
        						<xsl:with-param name="text" select="text"/>
        						<xsl:with-param name="no-sort-params" select="1"/>
        						<xsl:with-param name="parameters">
        							<xsl:text>order=</xsl:text>
                  					<xsl:value-of select="$sortcolumn"/>
                  					<xsl:text>&amp;sort=</xsl:text>
                  					<xsl:value-of select="phpgw:conditional($sortcolumn = //datatable/sorting/order and //datatable/sorting/sort = 'ASC', 'DESC', 'ASC')"/>
        						</xsl:with-param>
        						<xsl:with-param name="class">
        							<xsl:if test="$sortcolumn = //datatable/sorting/order">
        								<xsl:value-of select="phpgw:conditional(//datatable/sorting/sort = 'DESC', 'sorted-desc', 'sorted-asc')"/>
        							</xsl:if>
        						</xsl:with-param>
      						</xsl:call-template>
               			</xsl:when>
               			<xsl:otherwise>
                 			<xsl:value-of select="text"/>
               			</xsl:otherwise>
            		</xsl:choose>
        		</th>
      		</xsl:for-each>

      		<xsl:for-each select="//datatable/rowactions/action">
          		<th class="action">
            		<xsl:value-of select="text"/>
          		</th>
      		</xsl:for-each>
    	</tr>
  	</thead>
</xsl:template>

<!--
	Template
	match=rows()

	Renders the actual data rows depending on which headers are shown.
	Adds rowactions at the end.

	For each row we go though the headers and add columns based on the
	order of the headers and if they are set to be shown.


	The url for rowactions are constructed by taking $action and
	adding each parameter with its value based on source from the row.

	Parameters for row/column elements
	$name 		(string) (req) 	: Name of column
	$value		(string) (req) 	: actual value

	Parameters for rowactions/action
	$text		(string) (req)	: Used for innerHTML for link
	$action		(string) (req)	: Base url for action

	Parameters for rowactions/action/parameters/parameter
	$name		(string) (req)	: name of the url parameter to add
	$source		(string) (opt)	: name of the row column where the value should
								  be taken from. If left empty $name is used
								  for source.

	Rowaction Example:

	XML:

	<row>
		<column>
			<name>id</name>
			<value>101</name>
		</column>
		<column>
			<name>name</name>
			<value>some_value</name>
		</column>
	</row>

	<rowactions>
		<action>
			<text>Show</text>
			<action>http://phpgw/index.php?action=view</action>
			<parameters>
				<parameter>
					<name>id</name>
				</parameter>
			</parameters>
		</action>
		<action>
			<text>Filter</text>
			<action>http://phpgw/index.php?action=filter</action>
			<parameters>
				<parameter>
					<name>id</name>
				</parameter>
				<parameter>
					<name>filter</name>
					<source>name</name>
				</parameter>
			</parameters>
		</action>
	</rowactions>

	HTML Output:
	<a href="http://phpgw/index.php?action=view&id=101">Show</a>
	<a href="http://phpgw/index.php?action=view&id=101&filter=some_value">Filter</a>
-->
<xsl:template match="rows">
	<tbody>
    	<!-- Go throught each row and show columns based on header definition -->
    	<xsl:for-each select="row">
	      	<tr>
	        	<xsl:attribute name="class">
	        		<xsl:value-of select="phpgw:conditional(position() mod 2 = 0, 'row_off', 'row_on')"/>
	        	</xsl:attribute>

	        	<xsl:variable name="row_pos" select="position()"/>

	        	<xsl:for-each select="../../headers/header[not(visible = 0)]">
	          		<xsl:variable name="header_name" select="name"/>
	          		<td>
	            		<xsl:attribute name="class">
	              			<xsl:value-of select="format"/>
	            		</xsl:attribute>

						<xsl:choose>
							<xsl:when test="format= 'link'">
								<a href="{../../rows/row[$row_pos]/column[name=$header_name]/link}" target ="{../../rows/row[$row_pos]/column[name=$header_name]/target}"><xsl:value-of select="../../rows/row[$row_pos]/column[name=$header_name]/value"/></a>
							</xsl:when>
							<xsl:otherwise>
	            				<xsl:value-of select="../../rows/row[$row_pos]/column[name=$header_name]/value"/>
							</xsl:otherwise>
						</xsl:choose>
	          		</td>
	        	</xsl:for-each>

	        	<xsl:for-each select="//datatable/rowactions/action">
	          		<td class="action">
	            		<a>
	              			<xsl:attribute name="href">
	                			<xsl:value-of select="action"/>
	                			<xsl:for-each select="parameters/parameter">
	                  				<xsl:variable name="source" select="phpgw:conditional(not(source), name, source)"/>

	                  				<xsl:text>&amp;</xsl:text>
	                  				<xsl:value-of select="name"/>
	                  				<xsl:text>=</xsl:text>
									<xsl:value-of select="//datatable/rows/row[$row_pos]/column[name=$source]/value"/>
	                			</xsl:for-each>
	              			</xsl:attribute>
	              			<xsl:value-of select="text"/>
	            		</a>
	          		</td>
	        	</xsl:for-each>
	      	</tr>
    	</xsl:for-each>
  	</tbody>
</xsl:template>

<!--
	Experimental support for YUI datatable
 -->

<xsl:template name="datatable-yui-definition">
	<script type="text/javascript">
		var allow_allrows = "<xsl:value-of select="//datatable/config/allow_allrows"/>";

  		var property_js = "<xsl:value-of select="//datatable/property_js"/>";

		var base_java_url = "{<xsl:value-of select="//datatable/config/base_java_url"/>}";
 
		<xsl:choose>
			<xsl:when test="//datatable/json_data != ''">
  				var json_data = <xsl:value-of select="//datatable/json_data" disable-output-escaping="yes" />;
			</xsl:when>
		</xsl:choose>

		var myColumnDefs = [
			<xsl:for-each select="//datatable/headers/header">
				{
					key: "<xsl:value-of select="name"/>",
					label: "<xsl:value-of select="text"/>",
					resizeable:true,
					sortable: <xsl:value-of select="phpgw:conditional(not(sortable = 0), 'true', 'false')"/>,
					visible: <xsl:value-of select="phpgw:conditional(not(visible = 0), 'true', 'false')"/>,
					format: "<xsl:value-of select="format"/>",
					formatter: <xsl:value-of select="formatter"/>,
					<xsl:choose>
						<xsl:when test="width">
							width: <xsl:value-of select="width"/>,
						</xsl:when>
					</xsl:choose>
					source: "<xsl:value-of select="sort_field"/>",
					className: "<xsl:value-of select="className"/>"
				}<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>
			</xsl:for-each>
		];

		var values_combo_box = [
			<xsl:for-each select="//datatable/actions/form/fields/hidden_value">
				{
					id: "<xsl:value-of select="id"/>",
					value: "<xsl:value-of select="value"/>"
				}<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>
			</xsl:for-each>
		];


	</script>
</xsl:template>
