<?xml version="1.0" encoding="UTF-8"?>
<!-- 
 mbi: largely inspired by the excellent work by Makenshi: http://chaz6.com/static/xml/test.opml 
-->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output method="html" doctype-system="http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd" doctype-public="-//W3C//DTD XHTML 1.1//EN"/>
	
	<xsl:param name="sort-by">
		<xsl:value-of select="@title"/>
	</xsl:param>
	
	<xsl:param name="sort-type" select="'text'"/>
	<xsl:param name="sort-order" select="'ascending'"/>

	<xsl:template match="/">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
			<head>
				 <title><xsl:value-of select="/opml/head/title"/></title> 
				<link rel="stylesheet" href="css/css.css" type="text/css" />
			</head>
			<xsl:apply-templates/>
		</html>
	</xsl:template>
	
	<xsl:template match="opml/body">
		<body>
			<div id="channels" class="frame" style="width: 90%">
					<h1><xsl:value-of select="/opml/head/title" /></h1>			
						<p>
						   You can use this document within an 
						   <a href="http://www.google.com/search?q=RSS+News+Aggregator">RSS News Aggregator</a>.
						</p>
						<ul>
						<xsl:apply-templates select="outline">
							<xsl:sort select="@title"/>
						</xsl:apply-templates>
						</ul>
			 </div>
		</body>
	</xsl:template>

	<xsl:template match="outline">
		<xsl:choose>
			<xsl:when test="not(@xmlUrl)">
				<li class="folder"><span><xsl:value-of select="@text"/></span></li>
				<li><ul>
				  <xsl:apply-templates select="outline"><xsl:sort select="@text"/></xsl:apply-templates>
				</ul>
				</li>
			</xsl:when>
			<xsl:otherwise>
			        <li>
				<a href="{@xmlUrl}">XML</a> -
				<xsl:choose>
					<xsl:when test="starts-with(@htmlUrl,'http')">
						<a href="{@htmlUrl}"><xsl:value-of select="@text"/></a>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="@text"/>
					</xsl:otherwise>
				</xsl:choose>
				</li>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

</xsl:stylesheet>
