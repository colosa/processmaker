<?php

/**
 * class.jrml.php
 *
 * @package workflow.engine.ProcessMaker
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2011 Colosa Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */

/**
 * Jrml - Jrml class
 *
 * @package workflow.engine.ProcessMaker
 * @author Maborak <maborak@maborak.com>
 * @copyright 2008 COLOSA
 */

class Jrml
{
    public $rows;
    public $sql;
    private $data;

    /**
     * This function is the constructor of the class Jrml
     *
     * @param array $data
     * @return void
     */
    function __construct ($data = array())
    {
        $this->data = $data;
        $this->sql = $data['sql'];
        $this->rows = $this->get_rows( $data['type'] );
        $this->md = $this->get_md();
    }

    /**
     * This function is for get rows
     *
     * @param array $a
     * @return array
     */
    private function get_rows ($a)
    {
        $b = array ();
        foreach ($a as $key => $value) {
            $b[] = $key;
        }
        return $b;
    }

    public function get_md ()
    {
    }

    /**
     * This function is for get the header
     *
     * @return string
     */
    public function get_header ()
    {
        $xml = "<queryString><![CDATA[{$this->sql}]]></queryString>";
        foreach ($this->data['type'] as $key => $value) {
            $xml .= "<field name='{$key}' class='{$value}'><fieldDescription><![CDATA[]]></fieldDescription></field>";
        }
        $xml .= "<background><band/></background>";
        $xml .= '
           <title>
           <band height="58">
            <line>
                <reportElement x="0" y="8" width="555" height="1"/>
            </line>
            <line>
                <reportElement positionType="FixRelativeToBottom" x="0" y="51" width="555" height="1"/>
            </line>
            <staticText>
                <reportElement x="65" y="13" width="424" height="35"/>
                <textElement textAlignment="Center">
                    <font size="26" isBold="true"/>
                </textElement>
                <text><![CDATA[' . $this->data['title'] . ']]></text>
            </staticText>
        </band>
    </title>
    <pageHeader>
        <band/>
    </pageHeader>';
        return $xml;
    }

    /**
     * This function is for get a column of the header
     *
     * @return string
     */
    public function get_column_header ()
    {
        $xml = "<columnHeader><band height='18'>";
        $w = (int) ($this->data['columnWidth'] / sizeof( $this->rows ));
        $i = 0;
        foreach ($this->data['type'] as $key => $value) {
            $xml .= "<staticText><reportElement mode='Opaque' x='{$i}' y='0' width='{$w}' height='18' forecolor='#FFFFFF' backcolor='#999999'/>
                      <textElement>
                            <font size='12'/>
                        </textElement>
                        <text><![CDATA[{$key}]]></text>
                    </staticText>";
            $i = $i + $w;
        }
        $xml .= "    </band></columnHeader>";
        return $xml;
    }

    /**
     * This function is for get the detail
     *
     * @return string
     */
    public function get_detail ()
    {
        $xml = '<detail><band height="20">';
        $w = (int) ($this->data['columnWidth'] / sizeof( $this->rows ));
        $i = 0;
        foreach ($this->data['type'] as $key => $value) {
            $xml .= "<textField hyperlinkType='None'><reportElement x='{$i}' y='0' width='{$w}' height='20'/><textElement><font size='12'/></textElement><textFieldExpression class='{$value}'><![CDATA[\$F{{$key}}]]></textFieldExpression></textField>";
            $i = $i + $w;
        }
        $xml .= '</band></detail>';
        return $xml;
    }

    /**
     * This function is for get the footer
     *
     * @return string
     */
    public function get_footer ()
    {
        $xml = '<columnFooter>
      <band/>
    </columnFooter>
    <pageFooter>
        <band height="26">
            <textField evaluationTime="Report" pattern="" isBlankWhenNull="false" hyperlinkType="None">
                <reportElement key="textField" x="516" y="6" width="36" height="19" forecolor="#000000" backcolor="#FFFFFF"/>
                <box>
                    <topPen lineWidth="0.0" lineStyle="Solid" lineColor="#000000"/>
                    <leftPen lineWidth="0.0" lineStyle="Solid" lineColor="#000000"/>
                    <bottomPen lineWidth="0.0" lineStyle="Solid" lineColor="#000000"/>
                    <rightPen lineWidth="0.0" lineStyle="Solid" lineColor="#000000"/>
                </box>
                <textElement>
                    <font size="10"/>
                </textElement>
                <textFieldExpression class="java.lang.String"><![CDATA["" + $V{PAGE_NUMBER}]]></textFieldExpression>
            </textField>
            <textField pattern="" isBlankWhenNull="false" hyperlinkType="None">
                <reportElement key="textField" x="342" y="6" width="170" height="19" forecolor="#000000" backcolor="#FFFFFF"/>
                <box>
                    <topPen lineWidth="0.0" lineStyle="Solid" lineColor="#000000"/>
                    <leftPen lineWidth="0.0" lineStyle="Solid" lineColor="#000000"/>
                    <bottomPen lineWidth="0.0" lineStyle="Solid" lineColor="#000000"/>
                    <rightPen lineWidth="0.0" lineStyle="Solid" lineColor="#000000"/>
                </box>
                <textElement textAlignment="Right">
                    <font size="10"/>
                </textElement>
                <textFieldExpression class="java.lang.String"><![CDATA["Page " + $V{PAGE_NUMBER} + " of "]]></textFieldExpression>
            </textField>
            <textField pattern="" isBlankWhenNull="false" hyperlinkType="None">
                <reportElement key="textField" x="1" y="6" width="209" height="19" forecolor="#000000" backcolor="#FFFFFF"/>
                <box>
                    <topPen lineWidth="0.0" lineStyle="Solid" lineColor="#000000"/>
                    <leftPen lineWidth="0.0" lineStyle="Solid" lineColor="#000000"/>
                    <bottomPen lineWidth="0.0" lineStyle="Solid" lineColor="#000000"/>
                    <rightPen lineWidth="0.0" lineStyle="Solid" lineColor="#000000"/>
                </box>
                <textElement>
                    <font size="10"/>
                </textElement>
                <textFieldExpression class="java.util.Date"><![CDATA[new Date()]]></textFieldExpression>
            </textField>
        </band>
    </pageFooter>
    <summary>
        <band/>
    </summary>';
        return $xml;
    }

    /**
     * This function is for export
     *
     * @return string
     */
    public function export ()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
          <jasperReport xmlns="http://jasperreports.sourceforge.net/jasperreports" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://jasperreports.sourceforge.net/jasperreports http://jasperreports.sourceforge.net/xsd/jasperreport.xsd" name="' . $this->data['name'] . '" pageWidth="' . $this->data['pageWidth'] . '" pageHeight="842" columnWidth="' . $this->data['columnWidth'] . '" leftMargin="20" rightMargin="20" topMargin="20" bottomMargin="20">';
        $xml .= $this->get_header();
        $xml .= $this->get_column_header();
        $xml .= $this->get_detail();
        $xml .= $this->get_footer();
        $xml .= '</jasperReport>';
        return $xml;
    }
}