<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="/">
        <orders>
            <xsl:for-each select="./ediroot/interchange/group[@GroupType='PO']">
                <xsl:for-each select="./transaction">
                    <order>
                        <order_id>
                            <xsl:value-of
                                    select="./segment[@Id='REF']/element[@Id='REF01' and text()='GV']/../element[@Id='REF02']/text()"/>
                        </order_id>
                        <ref_3_x>
                            <xsl:value-of
                                    select="./segment[@Id='REF']/element[@Id='REF01' and text()='3X']/../element[@Id='REF02']/text()"/>
                        </ref_3_x>
                        <po_number>
                            <xsl:value-of select="./segment[@Id='BEG']/element[@Id='BEG03']/text()"/>
                        </po_number>
                        <date>
                            <xsl:value-of select="./segment[@Id='BEG']/element[@Id='BEG05']/text()"/>
                        </date>
                        <cancel_after>
                            <xsl:value-of select="./segment[@Id='DTM']/element[@Id='DTM02']/text()"/>
                        </cancel_after>
                        <shipping_method>
                            <xsl:value-of select="./segment[@Id='TD5']/element[@Id='TD512']/text()"/>
                        </shipping_method>
                        <shipping_address>
                            <name>
                                <xsl:value-of select="./loop[@Id='N1']/segment[@Id='N1']/element[@Id='N102']/text()"/>
                            </name>
                            <address_line_1>
                                <xsl:value-of select="./loop[@Id='N1']/segment[@Id='N3']/element[@Id='N301']/text()"/>
                            </address_line_1>
                            <address_line_2>
                                <xsl:value-of select="./loop[@Id='N1']/segment[@Id='N3']/element[@Id='N302']/text()"/>
                            </address_line_2>
                            <city>
                                <xsl:value-of select="./loop[@Id='N1']/segment[@Id='N4']/element[@Id='N401']/text()"/>
                            </city>
                            <state>
                                <xsl:value-of select="./loop[@Id='N1']/segment[@Id='N4']/element[@Id='N402']/text()"/>
                            </state>
                            <postal_code>
                                <xsl:value-of select="./loop[@Id='N1']/segment[@Id='N4']/element[@Id='N403']/text()"/>
                            </postal_code>
                            <telephone>
                                <xsl:value-of select="./loop[@Id='N1']/segment[@Id='PER']/element[@Id='PER04']/text()"/>
                            </telephone>
                        </shipping_address>
                        <items>
                            <xsl:for-each select="./loop[@Id='PO1']">
                                <item>
                                    <line_number>
                                        <xsl:value-of select="./segment[@Id='PO1']/element[@Id='PO101']/text()"/>
                                    </line_number>
                                    <quantity>
                                        <xsl:value-of select="./segment[@Id='PO1']/element[@Id='PO102']/text()"/>
                                    </quantity>
                                    <price>
                                        <xsl:value-of select="./segment[@Id='PO1']/element[@Id='PO104']/text()"/>
                                    </price>
                                    <sku>
                                        <xsl:value-of select="./segment[@Id='PO1']/element[@Id='PO107']/text()"/>
                                    </sku>
                                </item>
                            </xsl:for-each>
                        </items>
                    </order>
                </xsl:for-each>
            </xsl:for-each>
        </orders>
    </xsl:template>
</xsl:stylesheet>
