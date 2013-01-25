
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- APPLICATION
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `APPLICATION`;


CREATE TABLE `APPLICATION`
(
	`APP_UID` VARCHAR(32) default '' NOT NULL,
	`APP_NUMBER` INTEGER default 0 NOT NULL,
	`APP_PARENT` VARCHAR(32) default '0' NOT NULL,
	`APP_STATUS` VARCHAR(100) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`APP_PROC_STATUS` VARCHAR(100) default '' NOT NULL,
	`APP_PROC_CODE` VARCHAR(100) default '' NOT NULL,
	`APP_PARALLEL` VARCHAR(32) default 'NO' NOT NULL,
	`APP_INIT_USER` VARCHAR(32) default '' NOT NULL,
	`APP_CUR_USER` VARCHAR(32) default '' NOT NULL,
	`APP_CREATE_DATE` DATETIME  NOT NULL,
	`APP_INIT_DATE` DATETIME  NOT NULL,
	`APP_FINISH_DATE` DATETIME,
	`APP_UPDATE_DATE` DATETIME  NOT NULL,
	`APP_DATA` MEDIUMTEXT  NOT NULL,
	`APP_PIN` VARCHAR(32) default '' NOT NULL,
	PRIMARY KEY (`APP_UID`),
	KEY `indexApp`(`PRO_UID`, `APP_STATUS`, `APP_UID`),
	KEY `indexAppNumber`(`APP_NUMBER`),
	KEY `indexAppStatus`(`APP_STATUS`),
	KEY `indexAppCreateDate`(`APP_CREATE_DATE`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='The application';
#-----------------------------------------------------------------------------
#-- APP_DELEGATION
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `APP_DELEGATION`;


CREATE TABLE `APP_DELEGATION`
(
	`APP_UID` VARCHAR(32) default '' NOT NULL,
	`DEL_INDEX` INTEGER default 0 NOT NULL,
	`DEL_PREVIOUS` INTEGER default 0 NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`TAS_UID` VARCHAR(32) default '' NOT NULL,
	`USR_UID` VARCHAR(32) default '' NOT NULL,
	`DEL_TYPE` VARCHAR(32) default 'NORMAL' NOT NULL,
	`DEL_THREAD` INTEGER default 0 NOT NULL,
	`DEL_THREAD_STATUS` VARCHAR(32) default 'OPEN' NOT NULL,
	`DEL_PRIORITY` VARCHAR(32) default '3' NOT NULL,
	`DEL_DELEGATE_DATE` DATETIME  NOT NULL,
	`DEL_INIT_DATE` DATETIME,
	`DEL_TASK_DUE_DATE` DATETIME,
	`DEL_FINISH_DATE` DATETIME,
	`DEL_DURATION` DOUBLE default 0,
	`DEL_QUEUE_DURATION` DOUBLE default 0,
	`DEL_DELAY_DURATION` DOUBLE default 0,
	`DEL_STARTED` TINYINT default 0,
	`DEL_FINISHED` TINYINT default 0,
	`DEL_DELAYED` TINYINT default 0,
	`DEL_DATA` MEDIUMTEXT  NOT NULL,
	`APP_OVERDUE_PERCENTAGE` DOUBLE default 0 NOT NULL,
	PRIMARY KEY (`APP_UID`,`DEL_INDEX`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Delegation a task to user';
#-----------------------------------------------------------------------------
#-- APP_DOCUMENT
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `APP_DOCUMENT`;


CREATE TABLE `APP_DOCUMENT`
(
	`APP_DOC_UID` VARCHAR(32) default '' NOT NULL,
	`DOC_VERSION` INTEGER default 1 NOT NULL,
	`APP_UID` VARCHAR(32) default '' NOT NULL,
	`DEL_INDEX` INTEGER default 0 NOT NULL,
	`DOC_UID` VARCHAR(32) default '' NOT NULL,
	`USR_UID` VARCHAR(32) default '' NOT NULL,
	`APP_DOC_TYPE` VARCHAR(32) default '' NOT NULL,
	`APP_DOC_CREATE_DATE` DATETIME  NOT NULL,
	`APP_DOC_INDEX` INTEGER  NOT NULL,
	`FOLDER_UID` VARCHAR(32) default '',
	`APP_DOC_PLUGIN` VARCHAR(150) default '',
	`APP_DOC_TAGS` MEDIUMTEXT,
	`APP_DOC_STATUS` VARCHAR(32) default 'ACTIVE' NOT NULL,
	`APP_DOC_STATUS_DATE` DATETIME,
	`APP_DOC_FIELDNAME` VARCHAR(150),
	PRIMARY KEY (`APP_DOC_UID`,`DOC_VERSION`),
	KEY `indexAppDocument`(`FOLDER_UID`, `APP_DOC_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Documents in an Application';
#-----------------------------------------------------------------------------
#-- APP_MESSAGE
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `APP_MESSAGE`;


CREATE TABLE `APP_MESSAGE`
(
	`APP_MSG_UID` VARCHAR(32)  NOT NULL,
	`MSG_UID` VARCHAR(32),
	`APP_UID` VARCHAR(32) default '' NOT NULL,
	`DEL_INDEX` INTEGER default 0 NOT NULL,
	`APP_MSG_TYPE` VARCHAR(100) default '' NOT NULL,
	`APP_MSG_SUBJECT` VARCHAR(150) default '' NOT NULL,
	`APP_MSG_FROM` VARCHAR(100) default '' NOT NULL,
	`APP_MSG_TO` MEDIUMTEXT  NOT NULL,
	`APP_MSG_BODY` MEDIUMTEXT  NOT NULL,
	`APP_MSG_DATE` DATETIME  NOT NULL,
	`APP_MSG_CC` MEDIUMTEXT,
	`APP_MSG_BCC` MEDIUMTEXT,
	`APP_MSG_TEMPLATE` MEDIUMTEXT,
	`APP_MSG_STATUS` VARCHAR(20),
	`APP_MSG_ATTACH` MEDIUMTEXT,
	`APP_MSG_SEND_DATE` DATETIME  NOT NULL,
	`APP_MSG_SHOW_MESSAGE` TINYINT default 1 NOT NULL,
	PRIMARY KEY (`APP_MSG_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Messages in an Application';
#-----------------------------------------------------------------------------
#-- APP_OWNER
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `APP_OWNER`;


CREATE TABLE `APP_OWNER`
(
	`APP_UID` VARCHAR(32) default '' NOT NULL,
	`OWN_UID` VARCHAR(32) default '' NOT NULL,
	`USR_UID` VARCHAR(32) default '' NOT NULL,
	PRIMARY KEY (`APP_UID`,`OWN_UID`,`USR_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- CONFIGURATION
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `CONFIGURATION`;


CREATE TABLE `CONFIGURATION`
(
	`CFG_UID` VARCHAR(32) default '' NOT NULL,
	`OBJ_UID` VARCHAR(128) default '' NOT NULL,
	`CFG_VALUE` MEDIUMTEXT  NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`USR_UID` VARCHAR(32) default '' NOT NULL,
	`APP_UID` VARCHAR(32) default '' NOT NULL,
	PRIMARY KEY (`CFG_UID`,`OBJ_UID`,`PRO_UID`,`USR_UID`,`APP_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Stores the users, processes and/or applications configuratio';
#-----------------------------------------------------------------------------
#-- CONTENT
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `CONTENT`;


CREATE TABLE `CONTENT`
(
	`CON_CATEGORY` VARCHAR(30) default '' NOT NULL,
	`CON_PARENT` VARCHAR(32) default '' NOT NULL,
	`CON_ID` VARCHAR(100) default '' NOT NULL,
	`CON_LANG` VARCHAR(10) default '' NOT NULL,
	`CON_VALUE` MEDIUMTEXT  NOT NULL,
	PRIMARY KEY (`CON_CATEGORY`,`CON_PARENT`,`CON_ID`,`CON_LANG`),
	KEY `indexUid`(`CON_ID`, `CON_CATEGORY`, `CON_LANG`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- DEPARTMENT
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `DEPARTMENT`;


CREATE TABLE `DEPARTMENT`
(
	`DEP_UID` VARCHAR(32) default '' NOT NULL,
	`DEP_PARENT` VARCHAR(32) default '' NOT NULL,
	`DEP_MANAGER` VARCHAR(32) default '' NOT NULL,
	`DEP_LOCATION` INTEGER default 0 NOT NULL,
	`DEP_STATUS` VARCHAR(10) default 'ACTIVE' NOT NULL,
	`DEP_REF_CODE` VARCHAR(50) default '' NOT NULL,
	`DEP_LDAP_DN` VARCHAR(255) default '' NOT NULL,
	PRIMARY KEY (`DEP_UID`),
	KEY `DEP_BYPARENT`(`DEP_PARENT`),
	KEY `BY_DEP_LDAP_DN`(`DEP_LDAP_DN`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Departments';
#-----------------------------------------------------------------------------
#-- DYNAFORM
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `DYNAFORM`;


CREATE TABLE `DYNAFORM`
(
	`DYN_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '0' NOT NULL,
	`DYN_TYPE` VARCHAR(20) default 'xmlform' NOT NULL,
	`DYN_FILENAME` VARCHAR(100) default '' NOT NULL,
	PRIMARY KEY (`DYN_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Forms required';
#-----------------------------------------------------------------------------
#-- GROUPWF
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `GROUPWF`;


CREATE TABLE `GROUPWF`
(
	`GRP_UID` VARCHAR(32) default '' NOT NULL,
	`GRP_STATUS` CHAR(8) default 'ACTIVE' NOT NULL,
	`GRP_LDAP_DN` VARCHAR(255) default '' NOT NULL,
	`GRP_UX` VARCHAR(128) default 'NORMAL',
	PRIMARY KEY (`GRP_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- GROUP_USER
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `GROUP_USER`;


CREATE TABLE `GROUP_USER`
(
	`GRP_UID` VARCHAR(32) default '0' NOT NULL,
	`USR_UID` VARCHAR(32) default '0' NOT NULL,
	PRIMARY KEY (`GRP_UID`,`USR_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- HOLIDAY
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `HOLIDAY`;


CREATE TABLE `HOLIDAY`
(
	`HLD_UID` INTEGER  NOT NULL AUTO_INCREMENT,
	`HLD_DATE` VARCHAR(10) default '0000-00-00' NOT NULL,
	`HLD_DESCRIPTION` VARCHAR(200) default '' NOT NULL,
	PRIMARY KEY (`HLD_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- INPUT_DOCUMENT
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `INPUT_DOCUMENT`;


CREATE TABLE `INPUT_DOCUMENT`
(
	`INP_DOC_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '0' NOT NULL,
	`INP_DOC_FORM_NEEDED` VARCHAR(20) default 'REAL' NOT NULL,
	`INP_DOC_ORIGINAL` VARCHAR(20) default 'COPY' NOT NULL,
	`INP_DOC_PUBLISHED` VARCHAR(20) default 'PRIVATE' NOT NULL,
	`INP_DOC_VERSIONING` TINYINT default 0 NOT NULL,
	`INP_DOC_DESTINATION_PATH` MEDIUMTEXT,
	`INP_DOC_TAGS` MEDIUMTEXT,
	PRIMARY KEY (`INP_DOC_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Documentation required';
#-----------------------------------------------------------------------------
#-- ISO_COUNTRY
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `ISO_COUNTRY`;


CREATE TABLE `ISO_COUNTRY`
(
	`IC_UID` VARCHAR(2) default '' NOT NULL,
	`IC_NAME` VARCHAR(255),
	`IC_SORT_ORDER` VARCHAR(255),
	PRIMARY KEY (`IC_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- ISO_LOCATION
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `ISO_LOCATION`;


CREATE TABLE `ISO_LOCATION`
(
	`IC_UID` VARCHAR(2) default '' NOT NULL,
	`IL_UID` VARCHAR(5) default '' NOT NULL,
	`IL_NAME` VARCHAR(255),
	`IL_NORMAL_NAME` VARCHAR(255),
	`IS_UID` VARCHAR(4),
	PRIMARY KEY (`IC_UID`,`IL_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- ISO_SUBDIVISION
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `ISO_SUBDIVISION`;


CREATE TABLE `ISO_SUBDIVISION`
(
	`IC_UID` VARCHAR(2) default '' NOT NULL,
	`IS_UID` VARCHAR(4) default '' NOT NULL,
	`IS_NAME` VARCHAR(255) default '' NOT NULL,
	PRIMARY KEY (`IC_UID`,`IS_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- LANGUAGE
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `LANGUAGE`;


CREATE TABLE `LANGUAGE`
(
	`LAN_ID` VARCHAR(4) default '' NOT NULL,
	`LAN_NAME` VARCHAR(30) default '' NOT NULL,
	`LAN_NATIVE_NAME` VARCHAR(30) default '' NOT NULL,
	`LAN_DIRECTION` CHAR(1) default 'L' NOT NULL,
	`LAN_WEIGHT` INTEGER default 0 NOT NULL,
	`LAN_ENABLED` CHAR(1) default '1' NOT NULL,
	`LAN_CALENDAR` VARCHAR(30) default 'GREGORIAN' NOT NULL,
	PRIMARY KEY (`LAN_ID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- LEXICO
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `LEXICO`;


CREATE TABLE `LEXICO`
(
	`LEX_TOPIC` VARCHAR(64) default '' NOT NULL,
	`LEX_KEY` VARCHAR(128) default '' NOT NULL,
	`LEX_VALUE` VARCHAR(128) default '' NOT NULL,
	`LEX_CAPTION` VARCHAR(128) default '' NOT NULL,
	PRIMARY KEY (`LEX_TOPIC`,`LEX_KEY`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='LEXICOS, una tabla que contiene tablas';
#-----------------------------------------------------------------------------
#-- OUTPUT_DOCUMENT
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `OUTPUT_DOCUMENT`;


CREATE TABLE `OUTPUT_DOCUMENT`
(
	`OUT_DOC_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`OUT_DOC_REPORT_GENERATOR` VARCHAR(10) default 'HTML2PDF' NOT NULL,
	`OUT_DOC_LANDSCAPE` TINYINT default 0 NOT NULL,
	`OUT_DOC_MEDIA` VARCHAR(10) default 'Letter' NOT NULL,
	`OUT_DOC_LEFT_MARGIN` INTEGER default 30,
	`OUT_DOC_RIGHT_MARGIN` INTEGER default 15,
	`OUT_DOC_TOP_MARGIN` INTEGER default 15,
	`OUT_DOC_BOTTOM_MARGIN` INTEGER default 15,
	`OUT_DOC_GENERATE` VARCHAR(10) default 'BOTH' NOT NULL,
	`OUT_DOC_TYPE` VARCHAR(32) default 'HTML' NOT NULL,
	`OUT_DOC_CURRENT_REVISION` INTEGER default 0,
	`OUT_DOC_FIELD_MAPPING` MEDIUMTEXT,
	`OUT_DOC_VERSIONING` TINYINT default 0 NOT NULL,
	`OUT_DOC_DESTINATION_PATH` MEDIUMTEXT,
	`OUT_DOC_TAGS` MEDIUMTEXT,
	`OUT_DOC_PDF_SECURITY_ENABLED` TINYINT default 0,
	`OUT_DOC_PDF_SECURITY_OPEN_PASSWORD` VARCHAR(32) default '',
	`OUT_DOC_PDF_SECURITY_OWNER_PASSWORD` VARCHAR(32) default '',
	`OUT_DOC_PDF_SECURITY_PERMISSIONS` VARCHAR(150) default '',
	PRIMARY KEY (`OUT_DOC_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- PROCESS
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `PROCESS`;


CREATE TABLE `PROCESS`
(
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_PARENT` VARCHAR(32) default '0' NOT NULL,
	`PRO_TIME` DOUBLE default 1 NOT NULL,
	`PRO_TIMEUNIT` VARCHAR(20) default 'DAYS' NOT NULL,
	`PRO_STATUS` VARCHAR(20) default 'ACTIVE' NOT NULL,
	`PRO_TYPE_DAY` CHAR(1) default '0' NOT NULL,
	`PRO_TYPE` VARCHAR(20) default 'NORMAL' NOT NULL,
	`PRO_ASSIGNMENT` VARCHAR(20) default 'FALSE' NOT NULL,
	`PRO_SHOW_MAP` TINYINT default 1 NOT NULL,
	`PRO_SHOW_MESSAGE` TINYINT default 1 NOT NULL,
	`PRO_SUBPROCESS` TINYINT default 0 NOT NULL,
	`PRO_TRI_DELETED` VARCHAR(32) default '' NOT NULL,
	`PRO_TRI_CANCELED` VARCHAR(32) default '' NOT NULL,
	`PRO_TRI_PAUSED` VARCHAR(32) default '' NOT NULL,
	`PRO_TRI_REASSIGNED` VARCHAR(32) default '' NOT NULL,
	`PRO_SHOW_DELEGATE` TINYINT default 1 NOT NULL,
	`PRO_SHOW_DYNAFORM` TINYINT default 0 NOT NULL,
	`PRO_CATEGORY` VARCHAR(48) default '' NOT NULL,
	`PRO_SUB_CATEGORY` VARCHAR(48) default '' NOT NULL,
	`PRO_INDUSTRY` INTEGER default 1 NOT NULL,
	`PRO_UPDATE_DATE` DATETIME,
	`PRO_CREATE_DATE` DATETIME  NOT NULL,
	`PRO_CREATE_USER` VARCHAR(32) default '' NOT NULL,
	`PRO_HEIGHT` INTEGER default 5000 NOT NULL,
	`PRO_WIDTH` INTEGER default 10000 NOT NULL,
	`PRO_TITLE_X` INTEGER default 0 NOT NULL,
	`PRO_TITLE_Y` INTEGER default 6 NOT NULL,
	`PRO_DEBUG` INTEGER default 0 NOT NULL,
	`PRO_DYNAFORMS` MEDIUMTEXT,
	`PRO_DERIVATION_SCREEN_TPL` VARCHAR(128) default '',
	PRIMARY KEY (`PRO_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Store process Information';
#-----------------------------------------------------------------------------
#-- PROCESS_OWNER
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `PROCESS_OWNER`;


CREATE TABLE `PROCESS_OWNER`
(
	`OWN_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	PRIMARY KEY (`OWN_UID`,`PRO_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- REPORT_TABLE
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `REPORT_TABLE`;


CREATE TABLE `REPORT_TABLE`
(
	`REP_TAB_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`REP_TAB_NAME` VARCHAR(100) default '' NOT NULL,
	`REP_TAB_TYPE` VARCHAR(6) default '' NOT NULL,
	`REP_TAB_GRID` VARCHAR(150) default '',
	`REP_TAB_CONNECTION` VARCHAR(32) default '' NOT NULL,
	`REP_TAB_CREATE_DATE` DATETIME  NOT NULL,
	`REP_TAB_STATUS` CHAR(8) default 'ACTIVE' NOT NULL,
	PRIMARY KEY (`REP_TAB_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- REPORT_VAR
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `REPORT_VAR`;


CREATE TABLE `REPORT_VAR`
(
	`REP_VAR_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`REP_TAB_UID` VARCHAR(32) default '' NOT NULL,
	`REP_VAR_NAME` VARCHAR(255) default '' NOT NULL,
	`REP_VAR_TYPE` VARCHAR(20) default '' NOT NULL,
	PRIMARY KEY (`REP_VAR_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- ROUTE
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `ROUTE`;


CREATE TABLE `ROUTE`
(
	`ROU_UID` VARCHAR(32) default '' NOT NULL,
	`ROU_PARENT` VARCHAR(32) default '0' NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`TAS_UID` VARCHAR(32) default '' NOT NULL,
	`ROU_NEXT_TASK` VARCHAR(32) default '0' NOT NULL,
	`ROU_CASE` INTEGER default 0 NOT NULL,
	`ROU_TYPE` VARCHAR(25) default 'SEQUENTIAL' NOT NULL,
	`ROU_CONDITION` VARCHAR(512) default '' NOT NULL,
	`ROU_TO_LAST_USER` VARCHAR(20) default 'FALSE' NOT NULL,
	`ROU_OPTIONAL` VARCHAR(20) default 'FALSE' NOT NULL,
	`ROU_SEND_EMAIL` VARCHAR(20) default 'TRUE' NOT NULL,
	`ROU_SOURCEANCHOR` INTEGER default 1,
	`ROU_TARGETANCHOR` INTEGER default 0,
	`ROU_TO_PORT` INTEGER default 1 NOT NULL,
	`ROU_FROM_PORT` INTEGER default 2 NOT NULL,
	`ROU_EVN_UID` VARCHAR(32) default '' NOT NULL,
	`GAT_UID` VARCHAR(32) default '' NOT NULL,
	PRIMARY KEY (`ROU_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Differents flows for a flow in business process';
#-----------------------------------------------------------------------------
#-- STEP
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `STEP`;


CREATE TABLE `STEP`
(
	`STEP_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '0' NOT NULL,
	`TAS_UID` VARCHAR(32) default '0' NOT NULL,
	`STEP_TYPE_OBJ` VARCHAR(20) default 'DYNAFORM' NOT NULL,
	`STEP_UID_OBJ` VARCHAR(32) default '0' NOT NULL,
	`STEP_CONDITION` MEDIUMTEXT  NOT NULL,
	`STEP_POSITION` INTEGER default 0 NOT NULL,
	`STEP_MODE` VARCHAR(10) default 'EDIT',
	PRIMARY KEY (`STEP_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- STEP_TRIGGER
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `STEP_TRIGGER`;


CREATE TABLE `STEP_TRIGGER`
(
	`STEP_UID` VARCHAR(32) default '' NOT NULL,
	`TAS_UID` VARCHAR(32) default '' NOT NULL,
	`TRI_UID` VARCHAR(32) default '' NOT NULL,
	`ST_TYPE` VARCHAR(20) default '' NOT NULL,
	`ST_CONDITION` VARCHAR(255) default '' NOT NULL,
	`ST_POSITION` INTEGER default 0 NOT NULL,
	PRIMARY KEY (`STEP_UID`,`TAS_UID`,`TRI_UID`,`ST_TYPE`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- SWIMLANES_ELEMENTS
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `SWIMLANES_ELEMENTS`;


CREATE TABLE `SWIMLANES_ELEMENTS`
(
	`SWI_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`SWI_TYPE` VARCHAR(20) default 'LINE' NOT NULL,
	`SWI_X` INTEGER default 0 NOT NULL,
	`SWI_Y` INTEGER default 0 NOT NULL,
	`SWI_WIDTH` INTEGER default 0 NOT NULL,
	`SWI_HEIGHT` INTEGER default 0 NOT NULL,
	`SWI_NEXT_UID` VARCHAR(32) default '',
	PRIMARY KEY (`SWI_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- TASK
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `TASK`;


CREATE TABLE `TASK`
(
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`TAS_UID` VARCHAR(32) default '' NOT NULL,
	`TAS_TYPE` VARCHAR(20) default 'NORMAL' NOT NULL,
	`TAS_DURATION` DOUBLE default 0 NOT NULL,
	`TAS_DELAY_TYPE` VARCHAR(30) default '' NOT NULL,
	`TAS_TEMPORIZER` DOUBLE default 0 NOT NULL,
	`TAS_TYPE_DAY` CHAR(1) default '1' NOT NULL,
	`TAS_TIMEUNIT` VARCHAR(20) default 'DAYS' NOT NULL,
	`TAS_ALERT` VARCHAR(20) default 'FALSE' NOT NULL,
	`TAS_PRIORITY_VARIABLE` VARCHAR(100) default '' NOT NULL,
	`TAS_ASSIGN_TYPE` VARCHAR(30) default 'BALANCED' NOT NULL,
	`TAS_ASSIGN_VARIABLE` VARCHAR(100) default '@@SYS_NEXT_USER_TO_BE_ASSIGNED' NOT NULL,
	`TAS_GROUP_VARIABLE` VARCHAR(100) default '@@SYS_GROUP_TO_BE_ASSIGNED',
	`TAS_MI_INSTANCE_VARIABLE` VARCHAR(100) default '@@SYS_VAR_TOTAL_INSTANCE' NOT NULL,
	`TAS_MI_COMPLETE_VARIABLE` VARCHAR(100) default '@@SYS_VAR_TOTAL_INSTANCES_COMPLETE' NOT NULL,
	`TAS_ASSIGN_LOCATION` VARCHAR(20) default 'FALSE' NOT NULL,
	`TAS_ASSIGN_LOCATION_ADHOC` VARCHAR(20) default 'FALSE' NOT NULL,
	`TAS_TRANSFER_FLY` VARCHAR(20) default 'FALSE' NOT NULL,
	`TAS_LAST_ASSIGNED` VARCHAR(32) default '0' NOT NULL,
	`TAS_USER` VARCHAR(32) default '0' NOT NULL,
	`TAS_CAN_UPLOAD` VARCHAR(20) default 'FALSE' NOT NULL,
	`TAS_VIEW_UPLOAD` VARCHAR(20) default 'FALSE' NOT NULL,
	`TAS_VIEW_ADDITIONAL_DOCUMENTATION` VARCHAR(20) default 'FALSE' NOT NULL,
	`TAS_CAN_CANCEL` VARCHAR(20) default 'FALSE' NOT NULL,
	`TAS_OWNER_APP` VARCHAR(32) default '' NOT NULL,
	`STG_UID` VARCHAR(32) default '' NOT NULL,
	`TAS_CAN_PAUSE` VARCHAR(20) default 'FALSE' NOT NULL,
	`TAS_CAN_SEND_MESSAGE` VARCHAR(20) default 'TRUE' NOT NULL,
	`TAS_CAN_DELETE_DOCS` VARCHAR(20) default 'FALSE' NOT NULL,
	`TAS_SELF_SERVICE` VARCHAR(20) default 'FALSE' NOT NULL,
	`TAS_START` VARCHAR(20) default 'FALSE' NOT NULL,
	`TAS_TO_LAST_USER` VARCHAR(20) default 'FALSE' NOT NULL,
	`TAS_SEND_LAST_EMAIL` VARCHAR(20) default 'TRUE' NOT NULL,
	`TAS_DERIVATION` VARCHAR(100) default 'NORMAL' NOT NULL,
	`TAS_POSX` INTEGER default 0 NOT NULL,
	`TAS_POSY` INTEGER default 0 NOT NULL,
	`TAS_WIDTH` INTEGER default 110 NOT NULL,
	`TAS_HEIGHT` INTEGER default 60 NOT NULL,
	`TAS_COLOR` VARCHAR(32) default '' NOT NULL,
	`TAS_EVN_UID` VARCHAR(32) default '' NOT NULL,
	`TAS_BOUNDARY` VARCHAR(32) default '' NOT NULL,
	`TAS_DERIVATION_SCREEN_TPL` VARCHAR(128) default '',
	`TAS_SELFSERVICE_TIMEOUT` INTEGER default 0,
	`TAS_SELFSERVICE_TIME` VARCHAR(15) default '',
	`TAS_SELFSERVICE_TIME_UNIT` VARCHAR(15) default '',
	`TAS_SELFSERVICE_TRIGGER_UID` VARCHAR(32) default '',
	PRIMARY KEY (`TAS_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Task of workflow';
#-----------------------------------------------------------------------------
#-- TASK_USER
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `TASK_USER`;


CREATE TABLE `TASK_USER`
(
	`TAS_UID` VARCHAR(32) default '' NOT NULL,
	`USR_UID` VARCHAR(32) default '' NOT NULL,
	`TU_TYPE` INTEGER default 1 NOT NULL,
	`TU_RELATION` INTEGER default 0 NOT NULL,
	PRIMARY KEY (`TAS_UID`,`USR_UID`,`TU_TYPE`,`TU_RELATION`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- TRANSLATION
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `TRANSLATION`;


CREATE TABLE `TRANSLATION`
(
	`TRN_CATEGORY` VARCHAR(100) default '' NOT NULL,
	`TRN_ID` VARCHAR(100) default '' NOT NULL,
	`TRN_LANG` VARCHAR(10) default 'en' NOT NULL,
	`TRN_VALUE` MEDIUMTEXT  NOT NULL,
	`TRN_UPDATE_DATE` DATE,
	PRIMARY KEY (`TRN_CATEGORY`,`TRN_ID`,`TRN_LANG`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- TRIGGERS
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `TRIGGERS`;


CREATE TABLE `TRIGGERS`
(
	`TRI_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`TRI_TYPE` VARCHAR(20) default 'SCRIPT' NOT NULL,
	`TRI_WEBBOT` MEDIUMTEXT  NOT NULL,
	`TRI_PARAM` MEDIUMTEXT,
	PRIMARY KEY (`TRI_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- USERS
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `USERS`;


CREATE TABLE `USERS`
(
	`USR_UID` VARCHAR(32) default '' NOT NULL,
	`USR_USERNAME` VARCHAR(100) default '' NOT NULL,
	`USR_PASSWORD` VARCHAR(32) default '' NOT NULL,
	`USR_FIRSTNAME` VARCHAR(50) default '' NOT NULL,
	`USR_LASTNAME` VARCHAR(50) default '' NOT NULL,
	`USR_EMAIL` VARCHAR(100) default '' NOT NULL,
	`USR_DUE_DATE` DATE  NOT NULL,
	`USR_CREATE_DATE` DATETIME  NOT NULL,
	`USR_UPDATE_DATE` DATETIME  NOT NULL,
	`USR_STATUS` VARCHAR(32) default 'ACTIVE' NOT NULL,
	`USR_COUNTRY` VARCHAR(3) default '' NOT NULL,
	`USR_CITY` VARCHAR(3) default '' NOT NULL,
	`USR_LOCATION` VARCHAR(3) default '' NOT NULL,
	`USR_ADDRESS` VARCHAR(255) default '' NOT NULL,
	`USR_PHONE` VARCHAR(24) default '' NOT NULL,
	`USR_FAX` VARCHAR(24) default '' NOT NULL,
	`USR_CELLULAR` VARCHAR(24) default '' NOT NULL,
	`USR_ZIP_CODE` VARCHAR(16) default '' NOT NULL,
	`DEP_UID` VARCHAR(32) default '' NOT NULL,
	`USR_POSITION` VARCHAR(100) default '' NOT NULL,
	`USR_RESUME` VARCHAR(100) default '' NOT NULL,
	`USR_BIRTHDAY` DATE,
	`USR_ROLE` VARCHAR(32) default 'PROCESSMAKER_ADMIN',
	`USR_REPORTS_TO` VARCHAR(32) default '',
	`USR_REPLACED_BY` VARCHAR(32) default '',
	`USR_UX` VARCHAR(128) default 'NORMAL',
	PRIMARY KEY (`USR_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Users';
#-----------------------------------------------------------------------------
#-- APP_THREAD
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `APP_THREAD`;


CREATE TABLE `APP_THREAD`
(
	`APP_UID` VARCHAR(32) default '' NOT NULL,
	`APP_THREAD_INDEX` INTEGER default 0 NOT NULL,
	`APP_THREAD_PARENT` INTEGER default 0 NOT NULL,
	`APP_THREAD_STATUS` VARCHAR(32) default 'OPEN' NOT NULL,
	`DEL_INDEX` INTEGER default 0 NOT NULL,
	PRIMARY KEY (`APP_UID`,`APP_THREAD_INDEX`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='APP_THREAD';
#-----------------------------------------------------------------------------
#-- APP_DELAY
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `APP_DELAY`;


CREATE TABLE `APP_DELAY`
(
	`APP_DELAY_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '0' NOT NULL,
	`APP_UID` VARCHAR(32) default '0' NOT NULL,
	`APP_THREAD_INDEX` INTEGER default 0 NOT NULL,
	`APP_DEL_INDEX` INTEGER default 0 NOT NULL,
	`APP_TYPE` VARCHAR(20) default '0' NOT NULL,
	`APP_STATUS` VARCHAR(20) default '0' NOT NULL,
	`APP_NEXT_TASK` VARCHAR(32) default '0',
	`APP_DELEGATION_USER` VARCHAR(32) default '0',
	`APP_ENABLE_ACTION_USER` VARCHAR(32) default '0' NOT NULL,
	`APP_ENABLE_ACTION_DATE` DATETIME  NOT NULL,
	`APP_DISABLE_ACTION_USER` VARCHAR(32) default '0',
	`APP_DISABLE_ACTION_DATE` DATETIME,
	`APP_AUTOMATIC_DISABLED_DATE` DATETIME,
	PRIMARY KEY (`APP_DELAY_UID`),
	KEY `indexAppDelay`(`PRO_UID`, `APP_UID`, `APP_THREAD_INDEX`, `APP_DEL_INDEX`, `APP_NEXT_TASK`, `APP_DELEGATION_USER`, `APP_DISABLE_ACTION_USER`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='APP_DELAY';
#-----------------------------------------------------------------------------
#-- PROCESS_USER
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `PROCESS_USER`;


CREATE TABLE `PROCESS_USER`
(
	`PU_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`USR_UID` VARCHAR(32) default '' NOT NULL,
	`PU_TYPE` VARCHAR(20) default '' NOT NULL,
	PRIMARY KEY (`PU_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- SESSION
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `SESSION`;


CREATE TABLE `SESSION`
(
	`SES_UID` VARCHAR(32) default '' NOT NULL,
	`SES_STATUS` VARCHAR(16) default 'ACTIVE' NOT NULL,
	`USR_UID` VARCHAR(32) default 'ACTIVE' NOT NULL,
	`SES_REMOTE_IP` VARCHAR(32) default '0.0.0.0' NOT NULL,
	`SES_INIT_DATE` VARCHAR(19) default '' NOT NULL,
	`SES_DUE_DATE` VARCHAR(19) default '' NOT NULL,
	`SES_END_DATE` VARCHAR(19) default '' NOT NULL,
	PRIMARY KEY (`SES_UID`),
	KEY `indexSession`(`SES_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='SESSION';
#-----------------------------------------------------------------------------
#-- DB_SOURCE
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `DB_SOURCE`;


CREATE TABLE `DB_SOURCE`
(
	`DBS_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '0' NOT NULL,
	`DBS_TYPE` VARCHAR(8) default '0' NOT NULL,
	`DBS_SERVER` VARCHAR(100) default '0' NOT NULL,
	`DBS_DATABASE_NAME` VARCHAR(100) default '0' NOT NULL,
	`DBS_USERNAME` VARCHAR(32) default '0' NOT NULL,
	`DBS_PASSWORD` VARCHAR(32) default '',
	`DBS_PORT` INTEGER default 0,
	`DBS_ENCODE` VARCHAR(32) default '',
	PRIMARY KEY (`DBS_UID`,`PRO_UID`),
	KEY `indexDBSource`(`PRO_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='DB_SOURCE';
#-----------------------------------------------------------------------------
#-- STEP_SUPERVISOR
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `STEP_SUPERVISOR`;


CREATE TABLE `STEP_SUPERVISOR`
(
	`STEP_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '0' NOT NULL,
	`STEP_TYPE_OBJ` VARCHAR(20) default 'DYNAFORM' NOT NULL,
	`STEP_UID_OBJ` VARCHAR(32) default '0' NOT NULL,
	`STEP_POSITION` INTEGER default 0 NOT NULL,
	PRIMARY KEY (`STEP_UID`),
	KEY `indexStepSupervisor`(`PRO_UID`, `STEP_TYPE_OBJ`, `STEP_UID_OBJ`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='STEP_SUPERVISOR';
#-----------------------------------------------------------------------------
#-- OBJECT_PERMISSION
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `OBJECT_PERMISSION`;


CREATE TABLE `OBJECT_PERMISSION`
(
	`OP_UID` VARCHAR(32) default '0' NOT NULL,
	`PRO_UID` VARCHAR(32) default '0' NOT NULL,
	`TAS_UID` VARCHAR(32) default '0' NOT NULL,
	`USR_UID` VARCHAR(32) default '0' NOT NULL,
	`OP_USER_RELATION` INTEGER default 0 NOT NULL,
	`OP_TASK_SOURCE` VARCHAR(32) default '0',
	`OP_PARTICIPATE` INTEGER default 0 NOT NULL,
	`OP_OBJ_TYPE` VARCHAR(15) default '0' NOT NULL,
	`OP_OBJ_UID` VARCHAR(32) default '0' NOT NULL,
	`OP_ACTION` VARCHAR(10) default '0' NOT NULL,
	`OP_CASE_STATUS` VARCHAR(10) default '0',
	PRIMARY KEY (`OP_UID`),
	KEY `indexObjctPermission`(`PRO_UID`, `TAS_UID`, `USR_UID`, `OP_TASK_SOURCE`, `OP_OBJ_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='OBJECT_PERMISSION';
#-----------------------------------------------------------------------------
#-- CASE_TRACKER
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `CASE_TRACKER`;


CREATE TABLE `CASE_TRACKER`
(
	`PRO_UID` VARCHAR(32) default '0' NOT NULL,
	`CT_MAP_TYPE` VARCHAR(10) default '0' NOT NULL,
	`CT_DERIVATION_HISTORY` INTEGER default 0 NOT NULL,
	`CT_MESSAGE_HISTORY` INTEGER default 0 NOT NULL,
	PRIMARY KEY (`PRO_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='CASE_TRACKER';
#-----------------------------------------------------------------------------
#-- CASE_TRACKER_OBJECT
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `CASE_TRACKER_OBJECT`;


CREATE TABLE `CASE_TRACKER_OBJECT`
(
	`CTO_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '0' NOT NULL,
	`CTO_TYPE_OBJ` VARCHAR(20) default 'DYNAFORM' NOT NULL,
	`CTO_UID_OBJ` VARCHAR(32) default '0' NOT NULL,
	`CTO_CONDITION` MEDIUMTEXT  NOT NULL,
	`CTO_POSITION` INTEGER default 0 NOT NULL,
	PRIMARY KEY (`CTO_UID`),
	KEY `indexCaseTrackerObject`(`PRO_UID`, `CTO_UID_OBJ`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- STAGE
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `STAGE`;


CREATE TABLE `STAGE`
(
	`STG_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`STG_POSX` INTEGER default 0 NOT NULL,
	`STG_POSY` INTEGER default 0 NOT NULL,
	`STG_INDEX` INTEGER default 0 NOT NULL,
	PRIMARY KEY (`STG_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- SUB_PROCESS
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `SUB_PROCESS`;


CREATE TABLE `SUB_PROCESS`
(
	`SP_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`TAS_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_PARENT` VARCHAR(32) default '' NOT NULL,
	`TAS_PARENT` VARCHAR(32) default '' NOT NULL,
	`SP_TYPE` VARCHAR(20) default '' NOT NULL,
	`SP_SYNCHRONOUS` INTEGER default 0 NOT NULL,
	`SP_SYNCHRONOUS_TYPE` VARCHAR(20) default '' NOT NULL,
	`SP_SYNCHRONOUS_WAIT` INTEGER default 0 NOT NULL,
	`SP_VARIABLES_OUT` MEDIUMTEXT  NOT NULL,
	`SP_VARIABLES_IN` MEDIUMTEXT,
	`SP_GRID_IN` VARCHAR(50) default '' NOT NULL,
	PRIMARY KEY (`SP_UID`),
	KEY `indexSubProcess`(`PRO_UID`, `PRO_PARENT`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- SUB_APPLICATION
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `SUB_APPLICATION`;


CREATE TABLE `SUB_APPLICATION`
(
	`APP_UID` VARCHAR(32) default '' NOT NULL,
	`APP_PARENT` VARCHAR(32) default '' NOT NULL,
	`DEL_INDEX_PARENT` INTEGER default 0 NOT NULL,
	`DEL_THREAD_PARENT` INTEGER default 0 NOT NULL,
	`SA_STATUS` VARCHAR(32) default '' NOT NULL,
	`SA_VALUES_OUT` MEDIUMTEXT  NOT NULL,
	`SA_VALUES_IN` MEDIUMTEXT,
	`SA_INIT_DATE` DATETIME,
	`SA_FINISH_DATE` DATETIME,
	PRIMARY KEY (`APP_UID`,`APP_PARENT`,`DEL_INDEX_PARENT`,`DEL_THREAD_PARENT`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- LOGIN_LOG
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `LOGIN_LOG`;


CREATE TABLE `LOGIN_LOG`
(
	`LOG_UID` VARCHAR(32) default '' NOT NULL,
	`LOG_STATUS` VARCHAR(100) default '' NOT NULL,
	`LOG_IP` VARCHAR(15) default '' NOT NULL,
	`LOG_SID` VARCHAR(100) default '' NOT NULL,
	`LOG_INIT_DATE` DATETIME,
	`LOG_END_DATE` DATETIME,
	`LOG_CLIENT_HOSTNAME` VARCHAR(100) default '' NOT NULL,
	`USR_UID` VARCHAR(32) default '' NOT NULL,
	PRIMARY KEY (`LOG_UID`),
	KEY `indexLoginLog`(`USR_UID`, `LOG_INIT_DATE`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- USERS_PROPERTIES
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `USERS_PROPERTIES`;


CREATE TABLE `USERS_PROPERTIES`
(
	`USR_UID` VARCHAR(32) default '' NOT NULL,
	`USR_LAST_UPDATE_DATE` DATETIME,
	`USR_LOGGED_NEXT_TIME` INTEGER default 0,
	`USR_PASSWORD_HISTORY` MEDIUMTEXT,
	PRIMARY KEY (`USR_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- ADDITIONAL_TABLES
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `ADDITIONAL_TABLES`;


CREATE TABLE `ADDITIONAL_TABLES`
(
	`ADD_TAB_UID` VARCHAR(32) default '' NOT NULL,
	`ADD_TAB_NAME` VARCHAR(60) default '' NOT NULL,
	`ADD_TAB_CLASS_NAME` VARCHAR(100) default '' NOT NULL,
	`ADD_TAB_DESCRIPTION` MEDIUMTEXT,
	`ADD_TAB_SDW_LOG_INSERT` TINYINT default 0,
	`ADD_TAB_SDW_LOG_UPDATE` TINYINT default 0,
	`ADD_TAB_SDW_LOG_DELETE` TINYINT default 0,
	`ADD_TAB_SDW_LOG_SELECT` TINYINT default 0,
	`ADD_TAB_SDW_MAX_LENGTH` INTEGER default 0,
	`ADD_TAB_SDW_AUTO_DELETE` TINYINT default 0,
	`ADD_TAB_PLG_UID` VARCHAR(32) default '',
	`DBS_UID` VARCHAR(32) default '',
	`PRO_UID` VARCHAR(32) default '',
	`ADD_TAB_TYPE` VARCHAR(32) default '',
	`ADD_TAB_GRID` VARCHAR(256) default '',
	`ADD_TAB_TAG` VARCHAR(256) default '',
	PRIMARY KEY (`ADD_TAB_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- FIELDS
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `FIELDS`;


CREATE TABLE `FIELDS`
(
	`FLD_UID` VARCHAR(32) default '' NOT NULL,
	`ADD_TAB_UID` VARCHAR(32) default '' NOT NULL,
	`FLD_INDEX` INTEGER default 1 NOT NULL,
	`FLD_NAME` VARCHAR(60) default '' NOT NULL,
	`FLD_DESCRIPTION` MEDIUMTEXT  NOT NULL,
	`FLD_TYPE` VARCHAR(20) default '' NOT NULL,
	`FLD_SIZE` INTEGER default 0,
	`FLD_NULL` TINYINT default 1 NOT NULL,
	`FLD_AUTO_INCREMENT` TINYINT default 0 NOT NULL,
	`FLD_KEY` TINYINT default 0 NOT NULL,
	`FLD_FOREIGN_KEY` TINYINT default 0 NOT NULL,
	`FLD_FOREIGN_KEY_TABLE` VARCHAR(32) default '' NOT NULL,
	`FLD_DYN_NAME` VARCHAR(128) default '',
	`FLD_DYN_UID` VARCHAR(128) default '',
	`FLD_FILTER` TINYINT default 0,
	PRIMARY KEY (`FLD_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- SHADOW_TABLE
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `SHADOW_TABLE`;


CREATE TABLE `SHADOW_TABLE`
(
	`SHD_UID` VARCHAR(32) default '' NOT NULL,
	`ADD_TAB_UID` VARCHAR(32) default '' NOT NULL,
	`SHD_ACTION` VARCHAR(10) default '' NOT NULL,
	`SHD_DETAILS` MEDIUMTEXT  NOT NULL,
	`USR_UID` VARCHAR(32) default '' NOT NULL,
	`APP_UID` VARCHAR(32) default '' NOT NULL,
	`SHD_DATE` DATETIME,
	PRIMARY KEY (`SHD_UID`),
	KEY `indexShadowTable`(`SHD_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- EVENT
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `EVENT`;


CREATE TABLE `EVENT`
(
	`EVN_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`EVN_STATUS` VARCHAR(16) default 'OPEN' NOT NULL,
	`EVN_WHEN_OCCURS` VARCHAR(32) default 'SINGLE',
	`EVN_RELATED_TO` VARCHAR(16) default 'SINGLE',
	`TAS_UID` VARCHAR(32) default '' NOT NULL,
	`EVN_TAS_UID_FROM` VARCHAR(32) default '',
	`EVN_TAS_UID_TO` VARCHAR(32) default '',
	`EVN_TAS_ESTIMATED_DURATION` DOUBLE default 0,
	`EVN_TIME_UNIT` VARCHAR(10) default 'DAYS' NOT NULL,
	`EVN_WHEN` DOUBLE default 0 NOT NULL,
	`EVN_MAX_ATTEMPTS` TINYINT default 3 NOT NULL,
	`EVN_ACTION` VARCHAR(50) default '' NOT NULL,
	`EVN_CONDITIONS` MEDIUMTEXT,
	`EVN_ACTION_PARAMETERS` MEDIUMTEXT,
	`TRI_UID` VARCHAR(32) default '',
	`EVN_POSX` INTEGER default 0 NOT NULL,
	`EVN_POSY` INTEGER default 0 NOT NULL,
	`EVN_TYPE` VARCHAR(32) default '',
	`TAS_EVN_UID` VARCHAR(32) default '',
	PRIMARY KEY (`EVN_UID`),
	KEY `indexEventTable`(`EVN_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- GATEWAY
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `GATEWAY`;


CREATE TABLE `GATEWAY`
(
	`GAT_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`TAS_UID` VARCHAR(32) default '' NOT NULL,
	`GAT_NEXT_TASK` VARCHAR(32) default '' NOT NULL,
	`GAT_X` INTEGER default 0 NOT NULL,
	`GAT_Y` INTEGER default 0 NOT NULL,
	`GAT_TYPE` VARCHAR(32) default '' NOT NULL,
	PRIMARY KEY (`GAT_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- APP_EVENT
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `APP_EVENT`;


CREATE TABLE `APP_EVENT`
(
	`APP_UID` VARCHAR(32) default '' NOT NULL,
	`DEL_INDEX` INTEGER default 0 NOT NULL,
	`EVN_UID` VARCHAR(32) default '' NOT NULL,
	`APP_EVN_ACTION_DATE` DATETIME  NOT NULL,
	`APP_EVN_ATTEMPTS` TINYINT default 0 NOT NULL,
	`APP_EVN_LAST_EXECUTION_DATE` DATETIME,
	`APP_EVN_STATUS` VARCHAR(32) default 'OPEN' NOT NULL,
	PRIMARY KEY (`APP_UID`,`DEL_INDEX`,`EVN_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8';
#-----------------------------------------------------------------------------
#-- APP_CACHE_VIEW
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `APP_CACHE_VIEW`;


CREATE TABLE `APP_CACHE_VIEW`
(
	`APP_UID` VARCHAR(32) default '' NOT NULL,
	`DEL_INDEX` INTEGER default 0 NOT NULL,
	`APP_NUMBER` INTEGER default 0 NOT NULL,
	`APP_STATUS` VARCHAR(32) default '' NOT NULL,
	`USR_UID` VARCHAR(32) default '' NOT NULL,
	`PREVIOUS_USR_UID` VARCHAR(32) default '',
	`TAS_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`DEL_DELEGATE_DATE` DATETIME  NOT NULL,
	`DEL_INIT_DATE` DATETIME,
	`DEL_TASK_DUE_DATE` DATETIME,
	`DEL_FINISH_DATE` DATETIME,
	`DEL_THREAD_STATUS` VARCHAR(32) default 'OPEN',
	`APP_THREAD_STATUS` VARCHAR(32) default 'OPEN',
	`APP_TITLE` VARCHAR(255) default '' NOT NULL,
	`APP_PRO_TITLE` VARCHAR(255) default '' NOT NULL,
	`APP_TAS_TITLE` VARCHAR(255) default '' NOT NULL,
	`APP_CURRENT_USER` VARCHAR(128) default '',
	`APP_DEL_PREVIOUS_USER` VARCHAR(128) default '',
	`DEL_PRIORITY` VARCHAR(32) default '3' NOT NULL,
	`DEL_DURATION` DOUBLE default 0,
	`DEL_QUEUE_DURATION` DOUBLE default 0,
	`DEL_DELAY_DURATION` DOUBLE default 0,
	`DEL_STARTED` TINYINT default 0 NOT NULL,
	`DEL_FINISHED` TINYINT default 0 NOT NULL,
	`DEL_DELAYED` TINYINT default 0 NOT NULL,
	`APP_CREATE_DATE` DATETIME  NOT NULL,
	`APP_FINISH_DATE` DATETIME,
	`APP_UPDATE_DATE` DATETIME  NOT NULL,
	`APP_OVERDUE_PERCENTAGE` DOUBLE  NOT NULL,
	PRIMARY KEY (`APP_UID`,`DEL_INDEX`),
	KEY `indexAppNumber`(`APP_NUMBER`),
	KEY `indexAppUser`(`USR_UID`, `APP_STATUS`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Application cache view';
#-----------------------------------------------------------------------------
#-- DIM_TIME_DELEGATE
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `DIM_TIME_DELEGATE`;


CREATE TABLE `DIM_TIME_DELEGATE`
(
	`TIME_ID` VARCHAR(10) default '' NOT NULL,
	`MONTH_ID` INTEGER default 0 NOT NULL,
	`QTR_ID` INTEGER default 0 NOT NULL,
	`YEAR_ID` INTEGER default 0 NOT NULL,
	`MONTH_NAME` VARCHAR(3) default '0' NOT NULL,
	`MONTH_DESC` VARCHAR(9) default '' NOT NULL,
	`QTR_NAME` VARCHAR(4) default '' NOT NULL,
	`QTR_DESC` VARCHAR(9) default '' NOT NULL,
	PRIMARY KEY (`TIME_ID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='The application';
#-----------------------------------------------------------------------------
#-- DIM_TIME_COMPLETE
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `DIM_TIME_COMPLETE`;


CREATE TABLE `DIM_TIME_COMPLETE`
(
	`TIME_ID` VARCHAR(10) default '' NOT NULL,
	`MONTH_ID` INTEGER default 0 NOT NULL,
	`QTR_ID` INTEGER default 0 NOT NULL,
	`YEAR_ID` INTEGER default 0 NOT NULL,
	`MONTH_NAME` VARCHAR(3) default '0' NOT NULL,
	`MONTH_DESC` VARCHAR(9) default '' NOT NULL,
	`QTR_NAME` VARCHAR(4) default '' NOT NULL,
	`QTR_DESC` VARCHAR(9) default '' NOT NULL,
	PRIMARY KEY (`TIME_ID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='The application';
#-----------------------------------------------------------------------------
#-- APP_HISTORY
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `APP_HISTORY`;


CREATE TABLE `APP_HISTORY`
(
	`APP_UID` VARCHAR(32) default '' NOT NULL,
	`DEL_INDEX` INTEGER default 0 NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`TAS_UID` VARCHAR(32) default '' NOT NULL,
	`DYN_UID` VARCHAR(32) default '' NOT NULL,
	`USR_UID` VARCHAR(32) default '' NOT NULL,
	`APP_STATUS` VARCHAR(100) default '' NOT NULL,
	`HISTORY_DATE` DATETIME,
	`HISTORY_DATA` MEDIUMTEXT  NOT NULL,
	KEY `indexAppHistory`(`APP_UID`, `TAS_UID`, `USR_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='History table for Dynaforms';
#-----------------------------------------------------------------------------
#-- APP_FOLDER
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `APP_FOLDER`;


CREATE TABLE `APP_FOLDER`
(
	`FOLDER_UID` VARCHAR(32) default '' NOT NULL,
	`FOLDER_PARENT_UID` VARCHAR(32) default '' NOT NULL,
	`FOLDER_NAME` MEDIUMTEXT  NOT NULL,
	`FOLDER_CREATE_DATE` DATETIME  NOT NULL,
	`FOLDER_UPDATE_DATE` DATETIME  NOT NULL,
	PRIMARY KEY (`FOLDER_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Folder System PM Documents';
#-----------------------------------------------------------------------------
#-- FIELD_CONDITION
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `FIELD_CONDITION`;


CREATE TABLE `FIELD_CONDITION`
(
	`FCD_UID` VARCHAR(32) default '' NOT NULL,
	`FCD_FUNCTION` VARCHAR(50)  NOT NULL,
	`FCD_FIELDS` MEDIUMTEXT,
	`FCD_CONDITION` MEDIUMTEXT,
	`FCD_EVENTS` MEDIUMTEXT,
	`FCD_EVENT_OWNERS` MEDIUMTEXT,
	`FCD_STATUS` VARCHAR(10),
	`FCD_DYN_UID` VARCHAR(32)  NOT NULL,
	PRIMARY KEY (`FCD_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Conditions store to show or hide dynaform fields..';
#-----------------------------------------------------------------------------
#-- LOG_CASES_SCHEDULER
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `LOG_CASES_SCHEDULER`;


CREATE TABLE `LOG_CASES_SCHEDULER`
(
	`LOG_CASE_UID` VARCHAR(32) default '' NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`TAS_UID` VARCHAR(32) default '' NOT NULL,
	`USR_NAME` VARCHAR(32) default '' NOT NULL,
	`EXEC_DATE` DATE  NOT NULL,
	`EXEC_HOUR` VARCHAR(32) default '12:00' NOT NULL,
	`RESULT` VARCHAR(32) default 'SUCCESS' NOT NULL,
	`SCH_UID` VARCHAR(32) default 'OPEN' NOT NULL,
	`WS_CREATE_CASE_STATUS` MEDIUMTEXT  NOT NULL,
	`WS_ROUTE_CASE_STATUS` MEDIUMTEXT  NOT NULL,
	PRIMARY KEY (`LOG_CASE_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Cases Launched with Case Scheduler';
#-----------------------------------------------------------------------------
#-- CASE_SCHEDULER
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `CASE_SCHEDULER`;


CREATE TABLE `CASE_SCHEDULER`
(
	`SCH_UID` VARCHAR(32)  NOT NULL,
	`SCH_DEL_USER_NAME` VARCHAR(100)  NOT NULL,
	`SCH_DEL_USER_PASS` VARCHAR(100)  NOT NULL,
	`SCH_DEL_USER_UID` VARCHAR(100)  NOT NULL,
	`SCH_NAME` VARCHAR(100)  NOT NULL,
	`PRO_UID` VARCHAR(32) default '' NOT NULL,
	`TAS_UID` VARCHAR(32) default '' NOT NULL,
	`SCH_TIME_NEXT_RUN` DATETIME  NOT NULL,
	`SCH_LAST_RUN_TIME` DATETIME,
	`SCH_STATE` VARCHAR(15) default 'ACTIVE' NOT NULL,
	`SCH_LAST_STATE` VARCHAR(60) default '' NOT NULL,
	`USR_UID` VARCHAR(32) default '' NOT NULL,
	`SCH_OPTION` TINYINT default 0 NOT NULL,
	`SCH_START_TIME` DATETIME  NOT NULL,
	`SCH_START_DATE` DATETIME  NOT NULL,
	`SCH_DAYS_PERFORM_TASK` CHAR(5) default '' NOT NULL,
	`SCH_EVERY_DAYS` TINYINT default 0,
	`SCH_WEEK_DAYS` CHAR(14) default '0|0|0|0|0|0|0' NOT NULL,
	`SCH_START_DAY` CHAR(6) default '' NOT NULL,
	`SCH_MONTHS` CHAR(24) default '0|0|0|0|0|0|0|0|0|0|0|0' NOT NULL,
	`SCH_END_DATE` DATETIME,
	`SCH_REPEAT_EVERY` VARCHAR(15) default '' NOT NULL,
	`SCH_REPEAT_UNTIL` VARCHAR(15) default '' NOT NULL,
	`SCH_REPEAT_STOP_IF_RUNNING` TINYINT default 0,
	`CASE_SH_PLUGIN_UID` VARCHAR(100),
	PRIMARY KEY (`SCH_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Conditions store to show or hide dynaform fields..';
#-----------------------------------------------------------------------------
#-- CALENDAR_DEFINITION
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `CALENDAR_DEFINITION`;


CREATE TABLE `CALENDAR_DEFINITION`
(
	`CALENDAR_UID` VARCHAR(32) default '' NOT NULL,
	`CALENDAR_NAME` VARCHAR(100) default '' NOT NULL,
	`CALENDAR_CREATE_DATE` DATETIME  NOT NULL,
	`CALENDAR_UPDATE_DATE` DATETIME,
	`CALENDAR_WORK_DAYS` VARCHAR(100) default '' NOT NULL,
	`CALENDAR_DESCRIPTION` MEDIUMTEXT  NOT NULL,
	`CALENDAR_STATUS` VARCHAR(8) default 'ACTIVE' NOT NULL,
	PRIMARY KEY (`CALENDAR_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Calendar Definition used by PM';
#-----------------------------------------------------------------------------
#-- CALENDAR_BUSINESS_HOURS
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `CALENDAR_BUSINESS_HOURS`;


CREATE TABLE `CALENDAR_BUSINESS_HOURS`
(
	`CALENDAR_UID` VARCHAR(32) default '' NOT NULL,
	`CALENDAR_BUSINESS_DAY` VARCHAR(10) default '' NOT NULL,
	`CALENDAR_BUSINESS_START` VARCHAR(10) default '' NOT NULL,
	`CALENDAR_BUSINESS_END` VARCHAR(10) default '' NOT NULL,
	PRIMARY KEY (`CALENDAR_UID`,`CALENDAR_BUSINESS_DAY`,`CALENDAR_BUSINESS_START`,`CALENDAR_BUSINESS_END`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Calendar Business Hours';
#-----------------------------------------------------------------------------
#-- CALENDAR_HOLIDAYS
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `CALENDAR_HOLIDAYS`;


CREATE TABLE `CALENDAR_HOLIDAYS`
(
	`CALENDAR_UID` VARCHAR(32) default '' NOT NULL,
	`CALENDAR_HOLIDAY_NAME` VARCHAR(100) default '' NOT NULL,
	`CALENDAR_HOLIDAY_START` DATETIME  NOT NULL,
	`CALENDAR_HOLIDAY_END` DATETIME  NOT NULL,
	PRIMARY KEY (`CALENDAR_UID`,`CALENDAR_HOLIDAY_NAME`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Calendar Holidays';
#-----------------------------------------------------------------------------
#-- CALENDAR_ASSIGNMENTS
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `CALENDAR_ASSIGNMENTS`;


CREATE TABLE `CALENDAR_ASSIGNMENTS`
(
	`OBJECT_UID` VARCHAR(32) default '' NOT NULL,
	`CALENDAR_UID` VARCHAR(32) default '' NOT NULL,
	`OBJECT_TYPE` VARCHAR(100) default '' NOT NULL,
	PRIMARY KEY (`OBJECT_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Calendar Holidays';
#-----------------------------------------------------------------------------
#-- PROCESS_CATEGORY
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `PROCESS_CATEGORY`;


CREATE TABLE `PROCESS_CATEGORY`
(
	`CATEGORY_UID` VARCHAR(32) default '' NOT NULL,
	`CATEGORY_PARENT` VARCHAR(32) default '0' NOT NULL,
	`CATEGORY_NAME` VARCHAR(100) default '' NOT NULL,
	`CATEGORY_ICON` VARCHAR(100) default '',
	PRIMARY KEY (`CATEGORY_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Calendar Holidays';
#-----------------------------------------------------------------------------
#-- APP_NOTES
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `APP_NOTES`;


CREATE TABLE `APP_NOTES`
(
	`APP_UID` VARCHAR(32) default '' NOT NULL,
	`USR_UID` VARCHAR(32) default '' NOT NULL,
	`NOTE_DATE` DATETIME  NOT NULL,
	`NOTE_CONTENT` MEDIUMTEXT  NOT NULL,
	`NOTE_TYPE` VARCHAR(32) default 'USER' NOT NULL,
	`NOTE_AVAILABILITY` VARCHAR(32) default 'PUBLIC' NOT NULL,
	`NOTE_ORIGIN_OBJ` VARCHAR(32) default '',
	`NOTE_AFFECTED_OBJ1` VARCHAR(32) default '',
	`NOTE_AFFECTED_OBJ2` VARCHAR(32) default '' NOT NULL,
	`NOTE_RECIPIENTS` MEDIUMTEXT,
	KEY `indexAppNotesDate`(`APP_UID`, `NOTE_DATE`),
	KEY `indexAppNotesUser`(`APP_UID`, `USR_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Application Notes';
#-----------------------------------------------------------------------------
#-- DASHLET
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `DASHLET`;


CREATE TABLE `DASHLET`
(
	`DAS_UID` VARCHAR(32) default '' NOT NULL,
	`DAS_CLASS` VARCHAR(50) default '' NOT NULL,
	`DAS_TITLE` VARCHAR(255) default '' NOT NULL,
	`DAS_DESCRIPTION` MEDIUMTEXT,
	`DAS_VERSION` VARCHAR(10) default '1.0' NOT NULL,
	`DAS_CREATE_DATE` DATETIME  NOT NULL,
	`DAS_UPDATE_DATE` DATETIME,
	`DAS_STATUS` TINYINT default 1 NOT NULL,
	PRIMARY KEY (`DAS_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Dashblets definitions';
#-----------------------------------------------------------------------------
#-- DASHLET_INSTANCE
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `DASHLET_INSTANCE`;


CREATE TABLE `DASHLET_INSTANCE`
(
	`DAS_INS_UID` VARCHAR(32) default '' NOT NULL,
	`DAS_UID` VARCHAR(32) default '' NOT NULL,
	`DAS_INS_OWNER_TYPE` VARCHAR(20) default '' NOT NULL,
	`DAS_INS_OWNER_UID` VARCHAR(32) default '',
	`DAS_INS_ADDITIONAL_PROPERTIES` MEDIUMTEXT,
	`DAS_INS_CREATE_DATE` DATETIME  NOT NULL,
	`DAS_INS_UPDATE_DATE` DATETIME,
	`DAS_INS_STATUS` TINYINT default 1 NOT NULL,
	PRIMARY KEY (`DAS_INS_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='Dashblets definitions';
#-----------------------------------------------------------------------------
#-- APP_SOLR_QUEUE
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `APP_SOLR_QUEUE`;


CREATE TABLE `APP_SOLR_QUEUE`
(
	`APP_UID` VARCHAR(32) default '' NOT NULL,
 `APP_CHANGE_DATE` DATETIME  NOT NULL,
 `APP_CHANGE_TRACE` VARCHAR(500)  NOT NULL,
	`APP_UPDATED` TINYINT default 1 NOT NULL,
	PRIMARY KEY (`APP_UID`)
)ENGINE=MyISAM  DEFAULT CHARSET='utf8' COMMENT='APP_SOLR_QUEUE';
# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
