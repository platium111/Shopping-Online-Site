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
        <h2 style="margin-top:30px;"> Shopping Cart</h2>
        <table class="table table-striped" >
            <thead>
                <tr>
                    <th>Item number</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody id="tblCart">
                <xsl:for-each select="/cart/good">
                    <xsl:variable name="itemNo" select="itemNumber"/>
                    <tr>
                        <td>
                            <xsl:value-of select="itemNumber"/>
                        </td>
                        <td>
                            $<xsl:value-of select="price"/>
                        </td>
                        <td>
                            <xsl:value-of select="quantity"/>
                        </td>
                        <td>
                            <button class="btn-sm btn btn-primary" onclick="removeItemFromCart({$itemNo});">Remove from cart</button>
                        </td>
                    </tr>
                </xsl:for-each>
                <tr class="alert alert-success">
                    <td colspan="3"><span class="">Total:</span></td>
                    <td><xsl:value-of select="/cart/total"/></td>
                </tr>
                <tr>
                    <td colspan="2"><button class="btn btn-success" onclick="confirmPurchase();">Confirm Purchase</button></td>
                    <td colspan="2"><button class="btn btn-danger" onclick="cancelPurchase();">Cancel Purchase</button></td>
                </tr>
            </tbody>
        </table>
    </xsl:template>

</xsl:stylesheet>
