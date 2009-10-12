<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0">
	<channel>
		<title>{title}</title>
		<link>{link}</link>
		<description>
			<![CDATA[{description}]]>
		</description>
		<language>{lang}</language> 
		<lastBuildDate>{lastmod}</lastBuildDate>
		<generator>phpGroupWare</generator>
		
		<image>
			<title>{img_title}</title>
			<link>{link}</link>
			<url>{img_url}</url>
		</image>
		<!-- BEGIN item -->
		<item>
			<title>{subject}</title>
			<link>{item_link}</link>
			<description><![CDATA[{teaser}]]></description>
			<category>{cat}</category>
			<pubDate>{item_lastmod}</pubDate>
			<guid isPermaLink="true">{item_link}</guid>
		</item>

		<!-- END item -->
	</channel>
</rss>
