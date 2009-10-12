<?xml version="1.0"?>
<rdf:RDF 
 xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
 xmlns="http://purl.org/rss/1.0/"
 xmlns:dc="http://purl.org/dc/elements/1.1/"
>
	<channel rdf:about="{link}">
		<title>{title}</title>
		<link>{link}</link>
		<description>
			<![CDATA[{description}]]>
		</description>
		<dc:date>{feed_dc_date}</dc:date>

		<image rdf:resource="{img_url}" />
		
		<items>
			<rdf:Seq>
				<!-- BEGIN seq -->
					<rdf:li rdf:resource="{item_link}"/>
				<!-- END seq -->
			</rdf:Seq>
		</items>
	</channel>
	
	<image rdf:about="{img_url}">
		<title>{img_title}</title>
		<link>{img_link}</link>
		<url>{img_url}</url>
	</image>
<!-- BEGIN item -->
	<item rdf:about="{item_link}">
		<title>{subject}</title>
		<link>{item_link}</link>
		<dc:subject>{cat}</dc:subject>
		<description>
			<![CDATA[{teaser}]]>
		</description>
		<dc:date>{item_lastmod}</dc:date>
	</item>
<!-- END item -->
</rdf:RDF>
