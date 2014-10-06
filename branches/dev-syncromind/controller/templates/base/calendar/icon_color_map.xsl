<!-- $Id: view_calendar_month.xsl 9200 2012-04-21 20:05:34Z vator $ -->
<xsl:template name="icon_color_map" xmlns:php="http://php.net/xsl">
   <ul id="icon_color_map">
    <li>
      <img height="15" src="controller/images/status_icon_yellow_ring.png" />
      <span>Kontroll satt opp</span>
    </li>
    <li>
      <img height="15" src="controller/images/status_icon_yellow.png" />
      <span>Kontroll har planlagt dato</span>
    </li>
    <li>
      <img height="15" src="controller/images/status_icon_dark_green.png" />
      <span>Kontroll gjennomført uten åpne saker før frist</span>
    </li>
    <li>
      <img height="15" src="controller/images/status_icon_light_green.png" />
      <span>Kontroll gjennomført uten åpne saker etter frist</span>
    </li>
    <li>
      <img height="15" src="controller/images/status_icon_red_empty.png" />
      <span>Kontroll gjennomført med åpne saker</span>
    </li>
    <li>
      <img height="15" src="controller/images/status_icon_red_cross.png" />
      <span>Kontroll ikke gjennomført</span>
    </li>
    <li>
      <img height="15" src="controller/images/status_icon_black_cross.png" />
      <span>Kontroll kansellert</span>
    </li>
  </ul>
</xsl:template>
