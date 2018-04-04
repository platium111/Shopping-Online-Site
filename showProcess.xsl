<?xml version="1.0" encoding="UTF-8"?>

<!--
    Document   : showCart.xsl
    Created on : 17 October 2017, 9:45 AM
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
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Item Number</th>
                    <th scope="col">Item Name</th>
                    <th scope="col">Price</th>
                    <th scope="col">Quantity Available</th>
                    <th scope="col">Quantity on Hold</th>
                    <th scope="col">Quantity Sold</th>
                </tr>
            </thead>
            <tbody>
                <xsl:for-each select="/goods/good">
                    <xsl:if test = 'quantitySold &gt; 0'> 
                        <tr>
                            <th scope="row">
                                <xsl:value-of select="itemNumber"/>
                            </th>
                            <td>
                                <xsl:value-of select="name"/>
                            </td>
                            <td>
                                <xsl:value-of select="price"/>
                            </td>
                            <td>
                                <xsl:value-of select="quantity"/>
                            </td>
                            <td>
                                <xsl:value-of select="quantityHold"/>
                            </td>
                            <td>
                                <xsl:value-of select="quantitySold"/>
                            </td>
                        </tr>
                    </xsl:if>
                </xsl:for-each>
                <tr><td colspan="6" align="center"><button type="button" class="btn btn-primary" onclick="clickProcess();">Process</button></td></tr>
            </tbody>
        </table>
    </xsl:template>

</xsl:stylesheet>
