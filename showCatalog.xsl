<?xml version="1.0" encoding="UTF-8"?>

<!--
    Document   : showCatalog.xsl
    Created on : 15 October 2017, 6:38 PM
    Author     : clarktrinh
    Description:
        Purpose of transformation follows.
-->

<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:output method="html"/>

    <!-- TODO customize transformation rules 
         syntax recommendation http://www.w3.org/TR/xslt 
    -->
    <xsl:template match="/">
                <h2 style="margin-top:30px;"> Shopping Catalog</h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Item number</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Add</th>
                        </tr>
                    </thead>
                    <tbody id="tblCatalog">
                        <xsl:for-each select="/goods/good">
                            <xsl:variable name="itemNo" select="itemNumber"/>
                            <tr>
                                <td><xsl:value-of select="itemNumber"/></td>
                                <td><xsl:value-of select="name"/></td>
                                <td><xsl:value-of select="description"/></td>
                                <td><xsl:value-of select="price"/></td>
                                <td><xsl:value-of select="quantity"/></td>
                                <td><button class="btn-sm btn btn-primary" onclick="addItemToCart({$itemNo});">Add one to cart</button></td>
                            </tr>
                        </xsl:for-each>
                    </tbody>
                </table>
    </xsl:template>

</xsl:stylesheet>
