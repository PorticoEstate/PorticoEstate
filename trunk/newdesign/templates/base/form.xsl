<func:function name="phpgw:label">
	<func:result>
		<xsl:if test="title">
			<label for="{phpgw:or(id,generate-id())}">
				<xsl:if test="tooltip">
					<xsl:attribute name="title">
						<xsl:value-of select="tooltip"/>
					</xsl:attribute>

					<xsl:attribute name="style">
						<xsl:text>cursor:help</xsl:text>
					</xsl:attribute>
				</xsl:if>

				<xsl:attribute name="class">
					<xsl:if test="required">
							<xsl:text>required </xsl:text>
					</xsl:if>
					<xsl:if test="error">
							<xsl:text>error </xsl:text>
					</xsl:if>
				</xsl:attribute>


				<xsl:choose>
					<xsl:when test="accesskey and contains(title, accesskey)">
						<xsl:value-of select="substring-before (title, accesskey)"/>
						<span class="accesskey">
							<xsl:value-of select="accesskey"/>
						</span>
						<xsl:value-of select="substring-after (title, accesskey)"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="title"/>
					</xsl:otherwise>
				</xsl:choose>
			</label>
		</xsl:if>
	</func:result>
</func:function>

<xsl:template match="phpgw">
	<xsl:apply-templates />
	<div id="autocomplete">
		<select id="assign-to-select" name="values[assignedto]" class="forms" onMouseover="window.status='!Select the user the selection belongs to. To do not use a user select NO USER'; return true;" onMouseout="window.status='';return true;"><option value="">!Select user</option>
			<option value="97">Abrahamsen Alf Steinar</option>
			<option value="94">Andreassen Kristin</option>

			<option value="98">Eidissen Oystein</option>
			<option value="14">Etternavn14 Fornavn14</option>
			<option value="17">Etternavn17 Fornavn17</option>
			<option value="19">Etternavn19 Fornavn19</option>
			<option value="21">Etternavn21 Fornavn21</option>
			<option value="30">Etternavn30 Fornavn30</option>
			<option value="31">Etternavn31 Fornavn31</option>
			<option value="32">Etternavn32 Fornavn32</option>
			<option value="33">Etternavn33 Fornavn33</option>

			<option value="35">Etternavn35 Fornavn35</option>
			<option value="38">Etternavn38 Fornavn38</option>
			<option value="39">Etternavn39 Fornavn39</option>
			<option value="40">Etternavn40 Fornavn40</option>
			<option value="41">Etternavn41 Fornavn41</option>
			<option value="42">Etternavn42 Fornavn42</option>
			<option value="43">Etternavn43 Fornavn43</option>
			<option value="44">Etternavn44 Fornavn44</option>
			<option value="45">Etternavn45 Fornavn45</option>

			<option value="48">Etternavn48 Fornavn48</option>
			<option value="49">Etternavn49 Fornavn49</option>
			<option value="50">Etternavn50 Fornavn50</option>
			<option value="52">Etternavn52 Fornavn52</option>
			<option value="68">Etternavn68 Fornavn68</option>
			<option value="8">Etternavn8 Fornavn8</option>
			<option value="92">Jenssen Svein Inge</option>
			<option value="84">Johansen Bengt</option>
			<option value="91">Langvand Hans</option>

			<option value="96">Lynum Kari</option>
			<option value="82">Nes Sigurd</option>
			<option value="83">Næss Bjørn</option>
			<option value="101">Smevik Arnstein</option>
			<option value="93">Svendsen Oddmund</option>
			<option value="12">Tegnander jan</option>
			<option value="85">Themte Roy</option>
			<option value="90">Thomassen Knut H</option>
			<option value="6">User Admin</option>

			<option value="81">demo demo</option>
		</select>
	</div>

	<script>
		try
		{
			// Get select-element to create autocomplete from and hide it
			var select = document.getElementById('assign-to-select');
			select.style.display = "none";

			// Insert HTML for autocomplete dropdown
			var autocomplete_node = YAHOO.util.Dom.insertAfter( document.createElement('div'), select );
			var input_node = autocomplete_node.appendChild(document.createElement('input'));
			var drop_down_node = autocomplete_node.appendChild( document.createElement('div') );
			input_node.style.width = drop_down_node.style.width = "15em";

			// Create datasource from options inside select-element
			var data_array = new Array();
			for( var i=0; i &lt; select.options.length; i++ )
			{
				if( select.options[i].value )
				{
					data_array[i] = [ select.options[i].text, select.options[i].value ];
				}
			}
			var data_source = new YAHOO.widget.DS_JSArray( data_array, {queryMatchContains: true} );

			// Create autocomplete dropdown
			var autocomplete = new YAHOO.widget.AutoComplete(input_node, drop_down_node, data_source, {
				useShadow: true,
				typeAhead: true,
				minQueryLength: 0,
				forceSelection: true,
				maxResultsDisplayed: 20
			});
			// Show list when input gets focus

			autocomplete.textboxFocusEvent.subscribe(function(){
				var input_value = input_node.value;

				if(input_value.length === 0) {
					var oSelf = this;
					setTimeout(function(){oSelf.sendQuery(input_value);},0);
				}
			});

			// Set value of select-element when selected item in autocomplete changes
			autocomplete.itemSelectEvent.subscribe(function(oSelf , elItem , oData){
				select.value = elItem[1];
			});
		}
		catch (e)
		{
			alert(e);
		}
	</script>
	</xsl:template>


<xsl:template name="form" match="form">
	<div class="yui-skin-sam">
		<form id="test-form" action="{action}">
			<xsl:attribute name="class">
				<xsl:if test="tabbed">
					<xsl:text>tabbed </xsl:text>
				</xsl:if>
			</xsl:attribute>

			<h2><xsl:value-of select="title"/></h2>

			<p class="required">
				Detonates required field
			</p>

			<div id="form-content">
				<xsl:apply-templates select="fieldset" />
				<xsl:apply-templates select="field | textarea" />
				<xsl:apply-templates select="bottom_toolbar" />
			</div>

			<p>
				<input type="submit" value="Save" />
				<input type="submit" value="Apply" />
				<input type="submit" value="Cancel" />
			</p>

			<div id="calendar"></div>
		</form>
	</div>
	<br style="clear: both"/>
</xsl:template>

<xsl:template match="fieldset">
	<fieldset>
		<legend>
			<xsl:value-of select="title"/>
		</legend>
		<xsl:apply-templates select="field | textarea" />
	</fieldset>
</xsl:template>

<xsl:attribute-set name="core-attributes">
	<!--  class, id, style, title -->
	<xsl:attribute name="id">
		<xsl:value-of select="phpgw:or(id,generate-id())"/>
	</xsl:attribute>

	<xsl:attribute name="class">
		<xsl:if test="error">error </xsl:if>
		<xsl:if test="readonly">readonly </xsl:if>
		<xsl:if test="disabled">disabled </xsl:if>
		<xsl:if test="type='date'">date </xsl:if>
		<xsl:value-of select="class"/>
	</xsl:attribute>

	<xsl:attribute name="style">
		<xsl:value-of select="style"/>
	</xsl:attribute>
</xsl:attribute-set>

<xsl:attribute-set name="input-attributes">
	<xsl:attribute name="name">
		<xsl:value-of select="name"/>
	</xsl:attribute>
</xsl:attribute-set>

<xsl:template name="field" match="field">
	<xsl:copy-of select="phpgw:label()"/>

	<xsl:choose>
		<xsl:when test="type='textarea'">
			<xsl:call-template name="textarea"/>
		</xsl:when>
		<xsl:when test="type='date'">
			<xsl:call-template name="date"/>
		</xsl:when>
		<xsl:otherwise>
			<xsl:call-template name="textfield"/>
		</xsl:otherwise>
	</xsl:choose>

	<xsl:if test="error">
		<div class="error">
			<xsl:value-of select="error"/>
		</div>
	</xsl:if>
	<br />
</xsl:template>

<xsl:template name="textfield">
	<input xsl:use-attribute-sets="input-attributes core-attributes" value="{value}" type="{phpgw:or(type,'text')}">
		<xsl:if test="accesskey">
			<xsl:attribute name="accesskey">
				<xsl:value-of select="accesskey"/>
			</xsl:attribute>
		</xsl:if>

		<xsl:if test="maxlength">
			<xsl:attribute name="maxlength">
				<xsl:value-of select="maxlength"/>
			</xsl:attribute>
		</xsl:if>

		<xsl:if test="disabled">
			<xsl:attribute name="disabled">disabled</xsl:attribute>
		</xsl:if>

		<xsl:if test="readonly">
			<xsl:attribute name="readonly">readonly</xsl:attribute>
		</xsl:if>
	</input>
</xsl:template>

<xsl:template name="textarea">
	<textarea xsl:use-attribute-sets="input-attributes core-attributes" cols="{phpgw:or(cols,20)}" rows="{phpgw:or(rows,3)}">
		<xsl:if test="accesskey">
			<xsl:attribute name="accesskey">
				<xsl:value-of select="accesskey"/>
			</xsl:attribute>
		</xsl:if>
		<xsl:value-of select="value"/>
	</textarea>
</xsl:template>

<xsl:template name="date">
	<input xsl:use-attribute-sets="input-attributes core-attributes" value="{value}" type="text">
		<xsl:if test="accesskey">
			<xsl:attribute name="accesskey">
				<xsl:value-of select="accesskey"/>
			</xsl:attribute>
		</xsl:if>
	</input>
</xsl:template>
