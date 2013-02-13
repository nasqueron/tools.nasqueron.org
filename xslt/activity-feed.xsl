<?xml version="1.0" encoding="utf-8" ?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" indent="yes" version="5.0" encoding="utf-8" />

<!-- Main document -->
<xsl:template match="/">
<html>
<head>
	<title><xsl:value-of select="/items/@name" /> — Activity feed</title>
</head>
<body>
	<h1><xsl:value-of select="/items/@name" /></h1>
<xsl:for-each select="items/item">
	<h2><span class="change-type">
<xsl:choose>
	<xsl:when test="@type = 'abandon'">Change abandoned</xsl:when>
	<xsl:when test="@type = 'merge'">Merged change</xsl:when>
	<xsl:when test="@type = 'merged'">Change merged</xsl:when>
	<xsl:otherwise>New <xsl:value-of select="@type" /></xsl:otherwise>
</xsl:choose>
	</span>: 
	<xsl:element name="a">
		<xsl:attribute name="title">
			<xsl:value-of select="change/@id" />
		</xsl:attribute>
		<xsl:attribute name="href">
			https://gerrit.wikimedia.org/r/#q,<xsl:value-of select="change/@id" />,n,z
		</xsl:attribute>
		<xsl:value-of select="substring(change/@id, 0, 9)" />
	</xsl:element> —
	<xsl:element name="span">
		<xsl:attribute name="class">change-subject</xsl:attribute>
		<xsl:value-of select="change" />
	</xsl:element>
	</h2>

<xsl:if test="date">
	<p><strong>Date: </strong> <xsl:value-of select="date" /></p>
</xsl:if>
<xsl:if test="topic != ''">
	<p><strong>Topic: </strong> <xsl:value-of select="topic" /></p>
</xsl:if>
<xsl:if test="branch">
<xsl:if test="branch != 'master'">
	<p><strong>Branch: </strong> <xsl:value-of select="branch" /></p>
</xsl:if>
</xsl:if>
	<p><strong>From: </strong> <xsl:value-of select="user" /></p>
	<p><strong>Project: </strong> <xsl:value-of select="project" /></p>
<xsl:if test="message">
	<xsl:if test="@type = 'comment'">
	<xsl:if test="message/@cr != '0'">
	<p><strong>Code Review: </strong> <xsl:value-of select="message/@cr" /></p>
	</xsl:if>
	</xsl:if>
	<p><xsl:call-template name="nl2br"><xsl:with-param name="string" select="message" /></xsl:call-template></p>
</xsl:if>
<xsl:if test="approvals">
	<h3>Approvals</h3>
	<ul>
	<xsl:for-each select="approvals/approval">
		<li><span class="review-type">
<xsl:choose>
	<xsl:when test="@type = 'CRVW'">Code review </xsl:when>
	<xsl:when test="@type = 'VRIF'">Verified </xsl:when>
	<xsl:otherwise><xsl:value-of select="@type" /></xsl:otherwise>
</xsl:choose>		
		</span>
<span class="review-score"><xsl:value-of select="value" /></span> by <span class="review-reviewer"><xsl:value-of select="user" /></span></li>
	</xsl:for-each>
	</ul>
</xsl:if>
	<hr />
</xsl:for-each>
<p>This is the HTML view of an XML document. The XML document could contain more information, like IDs.</p>

</body>
</html>
</xsl:template>

<!-- nl2br - http://getsymphony.com/download/xslt-utilities/view/26522/ -->
<xsl:template name="nl2br">
	<xsl:param name="string"/>
	<xsl:value-of select="normalize-space(substring-before($string,'&#10;'))"/>
	<xsl:choose>
		<xsl:when test="contains($string,'&#10;')">
			<br />
			<xsl:call-template name="nl2br">
				<xsl:with-param name="string" select="substring-after($string,'&#10;')"/>
			</xsl:call-template>
		</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="$string"/>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

</xsl:stylesheet>
