<?xml version="1.0" encoding="utf-8"?>
<database name="workflow">

  <table name="CONFIGURATION">
    <vendor type="mysql">
      <parameter name="Name" value="CONFIGURATION"/>
      <parameter name="Engine" value="InnoDB"/>
      <parameter name="Version" value="10"/>
      <parameter name="Row_format" value="Dynamic"/>
      <parameter name="Rows" value="11"/>
      <parameter name="Avg_row_length" value="615"/>
      <parameter name="Data_length" value="6768"/>
      <parameter name="Max_data_length" value="281474976710655"/>
      <parameter name="Index_length" value="5120"/>
      <parameter name="Data_free" value="0"/>
      <parameter name="Auto_increment" value=""/>
      <parameter name="Create_time" value="2007-12-04 11:21:20"/>
      <parameter name="Update_time" value="2007-12-09 11:48:16"/>
      <parameter name="Check_time" value=""/>
      <parameter name="Collation" value="utf8_general_ci"/>
      <parameter name="Checksum" value=""/>
      <parameter name="Create_options" value=""/>
      <parameter name="Comment" value="Stores the users, processes and/or applications configuratio"/>
    </vendor>
    <column name="CFG_UID" type="VARCHAR" size="32" required="true" primaryKey="true" default="">
      <vendor type="mysql">
        <parameter name="Field" value="CFG_UID"/>
        <parameter name="Type" value="varchar(32)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value="PRI"/>
        <parameter name="Default" value=""/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="OBJ_UID" type="VARCHAR" size="128" required="true" primaryKey="true" default="">
      <vendor type="mysql">
        <parameter name="Field" value="OBJ_UID"/>
        <parameter name="Type" value="varchar(128)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value="PRI"/>
        <parameter name="Default" value=""/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="CFG_VALUE" type="LONGVARCHAR" required="true">
      <vendor type="mysql">
        <parameter name="Field" value="CFG_VALUE"/>
        <parameter name="Type" value="text"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value=""/>
        <parameter name="Default" value=""/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="PRO_UID" type="VARCHAR" size="32" required="true" primaryKey="true" default="">
      <vendor type="mysql">
        <parameter name="Field" value="PRO_UID"/>
        <parameter name="Type" value="varchar(32)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value="PRI"/>
        <parameter name="Default" value=""/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="USR_UID" type="VARCHAR" size="32" required="true" primaryKey="true" default="">
      <vendor type="mysql">
        <parameter name="Field" value="USR_UID"/>
        <parameter name="Type" value="varchar(32)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value="PRI"/>
        <parameter name="Default" value=""/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="APP_UID" type="VARCHAR" size="32" required="true" primaryKey="true" default="">
      <vendor type="mysql">
        <parameter name="Field" value="APP_UID"/>
        <parameter name="Type" value="varchar(32)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value="PRI"/>
        <parameter name="Default" value=""/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
  </table>


  <table name="LANGUAGE">
    <vendor type="mysql">
      <parameter name="Name" value="LANGUAGE"/>
      <parameter name="Engine" value="InnoDB"/>
      <parameter name="Version" value="10"/>
      <parameter name="Row_format" value="Dynamic"/>
      <parameter name="Rows" value="136"/>
      <parameter name="Avg_row_length" value="37"/>
      <parameter name="Data_length" value="5096"/>
      <parameter name="Max_data_length" value="281474976710655"/>
      <parameter name="Index_length" value="1024"/>
      <parameter name="Data_free" value="0"/>
      <parameter name="Auto_increment" value=""/>
      <parameter name="Create_time" value="2007-12-04 11:21:20"/>
      <parameter name="Update_time" value="2007-12-04 11:31:47"/>
      <parameter name="Check_time" value=""/>
      <parameter name="Collation" value="utf8_general_ci"/>
      <parameter name="Checksum" value=""/>
      <parameter name="Create_options" value=""/>
      <parameter name="Comment" value=""/>
    </vendor>
    <column name="LAN_ID" type="VARCHAR" size="4" required="true" default="" primaryKey="true">
      <vendor type="mysql">
        <parameter name="Field" value="LAN_ID"/>
        <parameter name="Type" value="varchar(4)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value="PRI"/>
        <parameter name="Default" value=""/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="LAN_NAME" type="VARCHAR" size="30" required="true" default="">
      <vendor type="mysql">
        <parameter name="Field" value="LAN_NAME"/>
        <parameter name="Type" value="varchar(30)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value=""/>
        <parameter name="Default" value=""/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="LAN_LOCATION" type="VARCHAR" size="4" required="true" default=""/>
      <vendor type="mysql">
        <parameter name="Field" value="LAN_LOCATION"/>
        <parameter name="Type" value="varchar(4)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value=""/>
        <parameter name="Default" value=""/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="LAN_NATIVE_NAME" type="VARCHAR" size="30" required="true" default="">
      <vendor type="mysql">
        <parameter name="Field" value="LAN_NATIVE_NAME"/>
        <parameter name="Type" value="varchar(30)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value=""/>
        <parameter name="Default" value=""/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="LAN_DIRECTION" type="CHAR" size="1" required="true" default="L">
      <vendor type="mysql">
        <parameter name="Field" value="LAN_DIRECTION"/>
        <parameter name="Type" value="char(1)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value=""/>
        <parameter name="Default" value="L"/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="LAN_WEIGHT" type="INTEGER" required="true" default="0">
      <vendor type="mysql">
        <parameter name="Field" value="LAN_WEIGHT"/>
        <parameter name="Type" value="int(11)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value=""/>
        <parameter name="Default" value="0"/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="LAN_ENABLED" type="CHAR" size="1" required="true" default="1">
      <vendor type="mysql">
        <parameter name="Field" value="LAN_ENABLED"/>
        <parameter name="Type" value="char(1)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value=""/>
        <parameter name="Default" value="1"/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="LAN_CALENDAR" type="VARCHAR" size="30" required="true" default="GREGORIAN">
      <vendor type="mysql">
        <parameter name="Field" value="LAN_CALENDAR"/>
        <parameter name="Type" value="varchar(30)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value=""/>
        <parameter name="Default" value="GREGORIAN"/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <validator column="LAN_DIRECTION">
      <rule name="validValues" value="L|R" message="Please select a valid Language Direccion." />
      <rule name="required" message="Document access is required." />
    </validator>
    <validator column="LAN_ENABLED">
      <rule name="validValues" value="1|0" message="Please select a valid Language Direccion." />
      <rule name="required" message="Document access is required." />
    </validator>
  </table>

  <table name="TRANSLATION">
    <vendor type="mysql">
      <parameter name="Name" value="TRANSLATION"/>
      <parameter name="Engine" value="InnoDB"/>
      <parameter name="Version" value="10"/>
      <parameter name="Row_format" value="Dynamic"/>
      <parameter name="Rows" value="121"/>
      <parameter name="Avg_row_length" value="46"/>
      <parameter name="Data_length" value="5572"/>
      <parameter name="Max_data_length" value="281474976710655"/>
      <parameter name="Index_length" value="4096"/>
      <parameter name="Data_free" value="0"/>
      <parameter name="Auto_increment" value=""/>
      <parameter name="Create_time" value="2007-12-04 11:21:20"/>
      <parameter name="Update_time" value="2007-12-09 13:22:56"/>
      <parameter name="Check_time" value=""/>
      <parameter name="Collation" value="utf8_general_ci"/>
      <parameter name="Checksum" value=""/>
      <parameter name="Create_options" value=""/>
      <parameter name="Comment" value=""/>
    </vendor>
    <column name="TRN_CATEGORY" type="VARCHAR" size="100" required="true" primaryKey="true" default="">
      <vendor type="mysql">
        <parameter name="Field" value="TRN_CATEGORY"/>
        <parameter name="Type" value="varchar(100)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value="PRI"/>
        <parameter name="Default" value=""/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="TRN_ID" type="VARCHAR" size="100" required="true" primaryKey="true" default="">
      <vendor type="mysql">
        <parameter name="Field" value="TRN_ID"/>
        <parameter name="Type" value="varchar(100)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value="PRI"/>
        <parameter name="Default" value=""/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="TRN_LANG" type="VARCHAR" size="10" required="true" primaryKey="true" default="en">
      <vendor type="mysql">
        <parameter name="Field" value="TRN_LANG"/>
        <parameter name="Type" value="varchar(10)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value="PRI"/>
        <parameter name="Default" value=""/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="TRN_VALUE" type="VARCHAR" size="200" required="true" default="">
      <vendor type="mysql">
        <parameter name="Field" value="TRN_VALUE"/>
        <parameter name="Type" value="varchar(200)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value=""/>
        <parameter name="Default" value=""/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <validator column="TRN_CATEGORY">
      <rule name="maxLength" value="100" message="Category can be no larger than $ in size" />
      <rule name="required" message="Category is required." />
    </validator>
    <validator column="TRN_ID">
      <rule name="maxLength" value="100" message="ID can be no larger than $ in size" />
      <rule name="required" message="ID is required." />
    </validator>
    <validator column="TRN_LANG">
      <rule name="maxLength" value="5" message="Language can be no larger than $ in size" />
      <rule name="required" message="Language is required." />
    </validator>
    <validator column="TRN_VALUE">
      <rule name="maxLength" value="200" message="Value can be no larger than $ in size" />
      <rule name="required" message="Value is required." />
    </validator>
  </table>

  <table name="USERS">
    <vendor type="mysql">
      <parameter name="Name" value="USERS"/>
      <parameter name="Engine" value="InnoDB"/>
      <parameter name="Version" value="10"/>
      <parameter name="Row_format" value="Dynamic"/>
      <parameter name="Rows" value="9"/>
      <parameter name="Avg_row_length" value="158"/>
      <parameter name="Data_length" value="1428"/>
      <parameter name="Max_data_length" value="281474976710655"/>
      <parameter name="Index_length" value="2048"/>
      <parameter name="Data_free" value="0"/>
      <parameter name="Auto_increment" value=""/>
      <parameter name="Create_time" value="2007-12-04 11:47:46"/>
      <parameter name="Update_time" value="2007-12-05 17:36:55"/>
      <parameter name="Check_time" value=""/>
      <parameter name="Collation" value="utf8_general_ci"/>
      <parameter name="Checksum" value=""/>
      <parameter name="Create_options" value=""/>
      <parameter name="Comment" value="Users"/>
    </vendor>
    <column name="USR_UID" type="VARCHAR" size="32" required="true" primaryKey="true" default="">
      <vendor type="mysql">
        <parameter name="Field" value="USR_UID"/>
        <parameter name="Type" value="varchar(32)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value="PRI"/>
        <parameter name="Default" value=""/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="USR_USERNAME" type="VARCHAR" size="100" required="true" default="">
      <vendor type="mysql">
        <parameter name="Field" value="USR_USERNAME"/>
        <parameter name="Type" value="varchar(50)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value=""/>
        <parameter name="Default" value=""/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="USR_PASSWORD" type="VARCHAR" size="32" required="true" default="">
      <vendor type="mysql">
        <parameter name="Field" value="USR_PASSWORD"/>
        <parameter name="Type" value="varchar(32)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value=""/>
        <parameter name="Default" value=""/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="USR_FIRSTNAME" type="VARCHAR" size="50" required="true" default="">
      <vendor type="mysql">
        <parameter name="Field" value="USR_FIRSTNAME"/>
        <parameter name="Type" value="varchar(50)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value=""/>
        <parameter name="Default" value=""/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="USR_LASTNAME" type="VARCHAR" size="50" required="true" default="">
      <vendor type="mysql">
        <parameter name="Field" value="USR_LASTNAME"/>
        <parameter name="Type" value="varchar(50)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value=""/>
        <parameter name="Default" value=""/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="USR_EMAIL" type="VARCHAR" size="100" required="true" default="">
      <vendor type="mysql">
        <parameter name="Field" value="USR_EMAIL"/>
        <parameter name="Type" value="varchar(50)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value=""/>
        <parameter name="Default" value=""/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="USR_DUE_DATE" type="DATE" required="true" >
      <vendor type="mysql">
        <parameter name="Field" value="USR_DUE_DATE"/>
        <parameter name="Type" value="date"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value=""/>
        <!-- <parameter name="Default" value="0000-00-00"/> -->
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="USR_CREATE_DATE" type="TIMESTAMP" required="true">
      <vendor type="mysql">
        <parameter name="Field" value="USR_CREATE_DATE"/>
        <parameter name="Type" value="datetime"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value=""/>
        <!-- <parameter name="Default" value="0000-00-00 00:00:00"/> -->
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="USR_UPDATE_DATE" type="TIMESTAMP" required="true">
      <vendor type="mysql">
        <parameter name="Field" value="USR_UPDATE_DATE"/>
        <parameter name="Type" value="datetime"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value=""/>
        <!-- <parameter name="Default" value="0000-00-00 00:00:00"/> -->
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="USR_STATUS" type="VARCHAR" size="32" required="true" default="ACTIVE">
      <vendor type="mysql">
        <parameter name="Field" value="USR_STATUS"/>
        <parameter name="Type" value="varchar(8)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value=""/>
        <parameter name="Default" value="ACTIVE"/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="USR_COUNTRY" type="VARCHAR" size="3" required="true" default="">
      <vendor type="mysql">
        <parameter name="Field" value="USR_COUNTRY"/>
        <parameter name="Type" value="varchar(3)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value=""/>
        <parameter name="Default" value=""/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="USR_CITY" type="VARCHAR" size="3" required="true" default="">
      <vendor type="mysql">
        <parameter name="Field" value="USR_CITY"/>
        <parameter name="Type" value="varchar(3)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value=""/>
        <parameter name="Default" value=""/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="USR_LOCATION" type="VARCHAR" size="3" required="true" default="">
      <vendor type="mysql">
        <parameter name="Field" value="USR_LOCATION"/>
        <parameter name="Type" value="varchar(3)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value=""/>
        <parameter name="Default" value=""/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="USR_ADDRESS" type="VARCHAR" size="255" required="true" default="">
      <vendor type="mysql">
        <parameter name="Field" value="USR_ADDRESS"/>
        <parameter name="Type" value="varchar(255)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value=""/>
        <parameter name="Default" value=""/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="USR_PHONE" type="VARCHAR" size="24" required="true" default="">
      <vendor type="mysql">
        <parameter name="Field" value="USR_PHONE"/>
        <parameter name="Type" value="varchar(24)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value=""/>
        <parameter name="Default" value=""/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="USR_FAX" type="VARCHAR" size="24" required="true" default="">
      <vendor type="mysql">
        <parameter name="Field" value="USR_FAX"/>
        <parameter name="Type" value="varchar(24)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value=""/>
        <parameter name="Default" value=""/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="USR_CELLULAR" type="VARCHAR" size="24" required="true" default="">
      <vendor type="mysql">
        <parameter name="Field" value="USR_CELLULAR"/>
        <parameter name="Type" value="varchar(24)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value=""/>
        <parameter name="Default" value=""/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="USR_ZIP_CODE" type="VARCHAR" size="16" required="true" default="">
      <vendor type="mysql">
        <parameter name="Field" value="USR_ZIP_CODE"/>
        <parameter name="Type" value="varchar(16)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value=""/>
        <parameter name="Default" value=""/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="USR_DEPARTMENT" type="INTEGER" required="true" default="0">
      <vendor type="mysql">
        <parameter name="Field" value="USR_DEPARTMENT"/>
        <parameter name="Type" value="int(11)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value=""/>
        <parameter name="Default" value="0"/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="USR_POSITION" type="VARCHAR" size="100" required="true" default="">
      <vendor type="mysql">
        <parameter name="Field" value="USR_POSITION"/>
        <parameter name="Type" value="varchar(100)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value=""/>
        <parameter name="Default" value=""/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="USR_RESUME" type="VARCHAR" size="100" required="true" default="">
      <vendor type="mysql">
        <parameter name="Field" value="USR_RESUME"/>
        <parameter name="Type" value="varchar(100)"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value=""/>
        <parameter name="Default" value=""/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="USR_BIRTHDAY" type="DATE" required="true" >
      <vendor type="mysql">
        <parameter name="Field" value="USR_BIRTHDAY"/>
        <parameter name="Type" value="date"/>
        <parameter name="Null" value="NO"/>
        <parameter name="Key" value=""/>
        <!-- <parameter name="Default" value="0000-00-00"/> -->
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <column name="USR_ROLE" type="VARCHAR" size="32" default="PROCESSMAKER_ADMIN">
      <vendor type="mysql">
        <parameter name="Field" value="USR_ROLE"/>
        <parameter name="Type" value="varchar(32)"/>
        <parameter name="Null" value="YES"/>
        <parameter name="Key" value=""/>
        <parameter name="Default" value="PROCESSMAKER_ADMIN"/>
        <parameter name="Extra" value=""/>
      </vendor>
    </column>
    <validator column="USR_STATUS">
      <rule name="validValues" value="ACTIVE|INACTIVE|VACATION|CLOSED" message="Please select a valid type." />
      <rule name="required" message="Type is required." />
    </validator>
  </table>


</database>
