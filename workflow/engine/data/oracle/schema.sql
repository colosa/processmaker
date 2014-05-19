

/* -----------------------------------------------------------------------
   APPLICATION
   ----------------------------------------------------------------------- */

DROP TABLE "APPLICATION" CASCADE CONSTRAINTS;


CREATE TABLE "APPLICATION"
(
	"APP_UID" VARCHAR2(32) default '' NOT NULL,
	"APP_NUMBER" NUMBER default 0 NOT NULL,
	"APP_PARENT" VARCHAR2(32) default '0' NOT NULL,
	"APP_STATUS" VARCHAR2(100) default '' NOT NULL,
	"PRO_UID" VARCHAR2(32) default '' NOT NULL,
	"APP_PROC_STATUS" VARCHAR2(100) default '' NOT NULL,
	"APP_PROC_CODE" VARCHAR2(100) default '' NOT NULL,
	"APP_PARALLEL" VARCHAR2(32) default 'NO' NOT NULL,
	"APP_INIT_USER" VARCHAR2(32) default '' NOT NULL,
	"APP_CUR_USER" VARCHAR2(32) default '' NOT NULL,
	"APP_CREATE_DATE" DATE  NOT NULL,
	"APP_INIT_DATE" DATE  NOT NULL,
	"APP_FINISH_DATE" DATE,
	"APP_UPDATE_DATE" DATE  NOT NULL,
	"APP_DATA" VARCHAR2(2000)  NOT NULL,
	"APP_PIN" VARCHAR2(32) default '' NOT NULL
);

	ALTER TABLE "APPLICATION"
		ADD CONSTRAINT "APPLICATION_PK"
	PRIMARY KEY ("APP_UID");
CREATE INDEX "indexApp" ON "APPLICATION" ("PRO_UID","APP_STATUS","APP_UID");
CREATE INDEX "indexAppNumber" ON "APPLICATION" ("APP_NUMBER");
CREATE INDEX "indexAppStatus" ON "APPLICATION" ("APP_STATUS");
CREATE INDEX "indexAppCreateDate" ON "APPLICATION" ("APP_CREATE_DATE");
CREATE INDEX "indexApp" ON "APPLICATION" ("PRO_UID","APP_STATUS","APP_UID");
CREATE INDEX "indexAppNumber" ON "APPLICATION" ("APP_NUMBER");
CREATE INDEX "indexAppStatus" ON "APPLICATION" ("APP_STATUS");
CREATE INDEX "indexAppCreateDate" ON "APPLICATION" ("APP_CREATE_DATE");


/* -----------------------------------------------------------------------
   APP_DELEGATION
   ----------------------------------------------------------------------- */

DROP TABLE "APP_DELEGATION" CASCADE CONSTRAINTS;


CREATE TABLE "APP_DELEGATION"
(
	"APP_UID" VARCHAR2(32) default '' NOT NULL,
	"DEL_INDEX" NUMBER default 0 NOT NULL,
	"DEL_PREVIOUS" NUMBER default 0 NOT NULL,
	"DEL_LAST_INDEX" NUMBER default 0 NOT NULL,
	"PRO_UID" VARCHAR2(32) default '' NOT NULL,
	"TAS_UID" VARCHAR2(32) default '' NOT NULL,
	"USR_UID" VARCHAR2(32) default '' NOT NULL,
	"DEL_TYPE" VARCHAR2(32) default 'NORMAL' NOT NULL,
	"DEL_THREAD" NUMBER default 0 NOT NULL,
	"DEL_THREAD_STATUS" VARCHAR2(32) default 'OPEN' NOT NULL,
	"DEL_PRIORITY" VARCHAR2(32) default '3' NOT NULL,
	"DEL_DELEGATE_DATE" DATE  NOT NULL,
	"DEL_INIT_DATE" DATE,
	"DEL_TASK_DUE_DATE" DATE,
	"DEL_FINISH_DATE" DATE,
	"DEL_DURATION" FLOAT default 0,
	"DEL_QUEUE_DURATION" FLOAT default 0,
	"DEL_DELAY_DURATION" FLOAT default 0,
	"DEL_STARTED" NUMBER(3,0) default 0,
	"DEL_FINISHED" NUMBER(3,0) default 0,
	"DEL_DELAYED" NUMBER(3,0) default 0,
	"DEL_DATA" VARCHAR2(2000)  NOT NULL,
	"APP_OVERDUE_PERCENTAGE" FLOAT default 0 NOT NULL
);

	ALTER TABLE "APP_DELEGATION"
		ADD CONSTRAINT "APP_DELEGATION_PK"
	PRIMARY KEY ("APP_UID","DEL_INDEX");


/* -----------------------------------------------------------------------
   APP_DOCUMENT
   ----------------------------------------------------------------------- */

DROP TABLE "APP_DOCUMENT" CASCADE CONSTRAINTS;


CREATE TABLE "APP_DOCUMENT"
(
	"APP_DOC_UID" VARCHAR2(32) default '' NOT NULL,
	"DOC_VERSION" NUMBER default 1 NOT NULL,
	"APP_UID" VARCHAR2(32) default '' NOT NULL,
	"DEL_INDEX" NUMBER default 0 NOT NULL,
	"DOC_UID" VARCHAR2(32) default '' NOT NULL,
	"USR_UID" VARCHAR2(32) default '' NOT NULL,
	"APP_DOC_TYPE" VARCHAR2(32) default '' NOT NULL,
	"APP_DOC_CREATE_DATE" DATE  NOT NULL,
	"APP_DOC_INDEX" NUMBER  NOT NULL,
	"FOLDER_UID" VARCHAR2(32) default '',
	"APP_DOC_PLUGIN" VARCHAR2(150) default '',
	"APP_DOC_TAGS" VARCHAR2(2000),
	"APP_DOC_STATUS" VARCHAR2(32) default 'ACTIVE' NOT NULL,
	"APP_DOC_STATUS_DATE" DATE,
	"APP_DOC_FIELDNAME" VARCHAR2(150)
);

	ALTER TABLE "APP_DOCUMENT"
		ADD CONSTRAINT "APP_DOCUMENT_PK"
	PRIMARY KEY ("APP_DOC_UID","DOC_VERSION");
CREATE INDEX "indexAppDocument" ON "APP_DOCUMENT" ("FOLDER_UID","APP_DOC_UID");
CREATE INDEX "indexAppDocument" ON "APP_DOCUMENT" ("FOLDER_UID","APP_DOC_UID");


/* -----------------------------------------------------------------------
   APP_MESSAGE
   ----------------------------------------------------------------------- */

DROP TABLE "APP_MESSAGE" CASCADE CONSTRAINTS;


CREATE TABLE "APP_MESSAGE"
(
	"APP_MSG_UID" VARCHAR2(32)  NOT NULL,
	"MSG_UID" VARCHAR2(32),
	"APP_UID" VARCHAR2(32) default '' NOT NULL,
	"DEL_INDEX" NUMBER default 0 NOT NULL,
	"APP_MSG_TYPE" VARCHAR2(100) default '' NOT NULL,
	"APP_MSG_SUBJECT" VARCHAR2(150) default '' NOT NULL,
	"APP_MSG_FROM" VARCHAR2(100) default '' NOT NULL,
	"APP_MSG_TO" VARCHAR2(2000)  NOT NULL,
	"APP_MSG_BODY" VARCHAR2(2000)  NOT NULL,
	"APP_MSG_DATE" DATE  NOT NULL,
	"APP_MSG_CC" VARCHAR2(2000),
	"APP_MSG_BCC" VARCHAR2(2000),
	"APP_MSG_TEMPLATE" VARCHAR2(2000),
	"APP_MSG_STATUS" VARCHAR2(20),
	"APP_MSG_ATTACH" VARCHAR2(2000),
	"APP_MSG_SEND_DATE" DATE  NOT NULL,
	"APP_MSG_SHOW_MESSAGE" NUMBER(3,0) default 1 NOT NULL
);

	ALTER TABLE "APP_MESSAGE"
		ADD CONSTRAINT "APP_MESSAGE_PK"
	PRIMARY KEY ("APP_MSG_UID");


/* -----------------------------------------------------------------------
   APP_OWNER
   ----------------------------------------------------------------------- */

DROP TABLE "APP_OWNER" CASCADE CONSTRAINTS;


CREATE TABLE "APP_OWNER"
(
	"APP_UID" VARCHAR2(32) default '' NOT NULL,
	"OWN_UID" VARCHAR2(32) default '' NOT NULL,
	"USR_UID" VARCHAR2(32) default '' NOT NULL
);

	ALTER TABLE "APP_OWNER"
		ADD CONSTRAINT "APP_OWNER_PK"
	PRIMARY KEY ("APP_UID","OWN_UID","USR_UID");


/* -----------------------------------------------------------------------
   CONFIGURATION
   ----------------------------------------------------------------------- */

DROP TABLE "CONFIGURATION" CASCADE CONSTRAINTS;


CREATE TABLE "CONFIGURATION"
(
	"CFG_UID" VARCHAR2(32) default '' NOT NULL,
	"OBJ_UID" VARCHAR2(128) default '' NOT NULL,
	"CFG_VALUE" VARCHAR2(2000)  NOT NULL,
	"PRO_UID" VARCHAR2(32) default '' NOT NULL,
	"USR_UID" VARCHAR2(32) default '' NOT NULL,
	"APP_UID" VARCHAR2(32) default '' NOT NULL
);

	ALTER TABLE "CONFIGURATION"
		ADD CONSTRAINT "CONFIGURATION_PK"
	PRIMARY KEY ("CFG_UID","OBJ_UID","PRO_UID","USR_UID","APP_UID");


/* -----------------------------------------------------------------------
   CONTENT
   ----------------------------------------------------------------------- */

DROP TABLE "CONTENT" CASCADE CONSTRAINTS;


CREATE TABLE "CONTENT"
(
	"CON_CATEGORY" VARCHAR2(30) default '' NOT NULL,
	"CON_PARENT" VARCHAR2(32) default '' NOT NULL,
	"CON_ID" VARCHAR2(100) default '' NOT NULL,
	"CON_LANG" VARCHAR2(10) default '' NOT NULL,
	"CON_VALUE" VARCHAR2(2000)  NOT NULL
);

	ALTER TABLE "CONTENT"
		ADD CONSTRAINT "CONTENT_PK"
	PRIMARY KEY ("CON_CATEGORY","CON_PARENT","CON_ID","CON_LANG");
CREATE INDEX "indexUid" ON "CONTENT" ("CON_ID","CON_CATEGORY","CON_LANG");
CREATE INDEX "indexUid" ON "CONTENT" ("CON_ID","CON_CATEGORY","CON_LANG");


/* -----------------------------------------------------------------------
   DEPARTMENT
   ----------------------------------------------------------------------- */

DROP TABLE "DEPARTMENT" CASCADE CONSTRAINTS;


CREATE TABLE "DEPARTMENT"
(
	"DEP_UID" VARCHAR2(32) default '' NOT NULL,
	"DEP_PARENT" VARCHAR2(32) default '' NOT NULL,
	"DEP_MANAGER" VARCHAR2(32) default '' NOT NULL,
	"DEP_LOCATION" NUMBER default 0 NOT NULL,
	"DEP_STATUS" VARCHAR2(10) default 'ACTIVE' NOT NULL,
	"DEP_REF_CODE" VARCHAR2(50) default '' NOT NULL,
	"DEP_LDAP_DN" VARCHAR2(255) default '' NOT NULL
);

	ALTER TABLE "DEPARTMENT"
		ADD CONSTRAINT "DEPARTMENT_PK"
	PRIMARY KEY ("DEP_UID");
CREATE INDEX "DEP_BYPARENT" ON "DEPARTMENT" ("DEP_PARENT");
CREATE INDEX "BY_DEP_LDAP_DN" ON "DEPARTMENT" ("DEP_LDAP_DN");
CREATE INDEX "DEP_BYPARENT" ON "DEPARTMENT" ("DEP_PARENT");
CREATE INDEX "BY_DEP_LDAP_DN" ON "DEPARTMENT" ("DEP_LDAP_DN");


/* -----------------------------------------------------------------------
   DYNAFORM
   ----------------------------------------------------------------------- */

DROP TABLE "DYNAFORM" CASCADE CONSTRAINTS;


CREATE TABLE "DYNAFORM"
(
	"DYN_UID" VARCHAR2(32) default '' NOT NULL,
	"PRO_UID" VARCHAR2(32) default '0' NOT NULL,
	"DYN_TYPE" VARCHAR2(20) default 'xmlform' NOT NULL,
	"DYN_FILENAME" VARCHAR2(100) default '' NOT NULL
);

	ALTER TABLE "DYNAFORM"
		ADD CONSTRAINT "DYNAFORM_PK"
	PRIMARY KEY ("DYN_UID");


/* -----------------------------------------------------------------------
   GROUPWF
   ----------------------------------------------------------------------- */

DROP TABLE "GROUPWF" CASCADE CONSTRAINTS;


CREATE TABLE "GROUPWF"
(
	"GRP_UID" VARCHAR2(32) default '' NOT NULL,
	"GRP_STATUS" CHAR(8) default 'ACTIVE' NOT NULL,
	"GRP_LDAP_DN" VARCHAR2(255) default '' NOT NULL,
	"GRP_UX" VARCHAR2(128) default 'NORMAL'
);

	ALTER TABLE "GROUPWF"
		ADD CONSTRAINT "GROUPWF_PK"
	PRIMARY KEY ("GRP_UID");


/* -----------------------------------------------------------------------
   GROUP_USER
   ----------------------------------------------------------------------- */

DROP TABLE "GROUP_USER" CASCADE CONSTRAINTS;


CREATE TABLE "GROUP_USER"
(
	"GRP_UID" VARCHAR2(32) default '0' NOT NULL,
	"USR_UID" VARCHAR2(32) default '0' NOT NULL
);

	ALTER TABLE "GROUP_USER"
		ADD CONSTRAINT "GROUP_USER_PK"
	PRIMARY KEY ("GRP_UID","USR_UID");


/* -----------------------------------------------------------------------
   HOLIDAY
   ----------------------------------------------------------------------- */

DROP TABLE "HOLIDAY" CASCADE CONSTRAINTS;

DROP SEQUENCE "HOLIDAY_SEQ";


CREATE TABLE "HOLIDAY"
(
	"HLD_UID" NUMBER  NOT NULL,
	"HLD_DATE" VARCHAR2(10) default '0000-00-00' NOT NULL,
	"HLD_DESCRIPTION" VARCHAR2(200) default '' NOT NULL
);

	ALTER TABLE "HOLIDAY"
		ADD CONSTRAINT "HOLIDAY_PK"
	PRIMARY KEY ("HLD_UID");
CREATE SEQUENCE "HOLIDAY_SEQ" INCREMENT BY 1 START WITH 1 NOMAXVALUE NOCYCLE NOCACHE ORDER;


/* -----------------------------------------------------------------------
   INPUT_DOCUMENT
   ----------------------------------------------------------------------- */

DROP TABLE "INPUT_DOCUMENT" CASCADE CONSTRAINTS;


CREATE TABLE "INPUT_DOCUMENT"
(
	"INP_DOC_UID" VARCHAR2(32) default '' NOT NULL,
	"PRO_UID" VARCHAR2(32) default '0' NOT NULL,
	"INP_DOC_FORM_NEEDED" VARCHAR2(20) default 'REAL' NOT NULL,
	"INP_DOC_ORIGINAL" VARCHAR2(20) default 'COPY' NOT NULL,
	"INP_DOC_PUBLISHED" VARCHAR2(20) default 'PRIVATE' NOT NULL,
	"INP_DOC_VERSIONING" NUMBER(3,0) default 0 NOT NULL,
	"INP_DOC_DESTINATION_PATH" VARCHAR2(2000),
	"INP_DOC_TAGS" VARCHAR2(2000)
);

	ALTER TABLE "INPUT_DOCUMENT"
		ADD CONSTRAINT "INPUT_DOCUMENT_PK"
	PRIMARY KEY ("INP_DOC_UID");


/* -----------------------------------------------------------------------
   ISO_COUNTRY
   ----------------------------------------------------------------------- */

DROP TABLE "ISO_COUNTRY" CASCADE CONSTRAINTS;


CREATE TABLE "ISO_COUNTRY"
(
	"IC_UID" VARCHAR2(2) default '' NOT NULL,
	"IC_NAME" VARCHAR2(255),
	"IC_SORT_ORDER" VARCHAR2(255)
);

	ALTER TABLE "ISO_COUNTRY"
		ADD CONSTRAINT "ISO_COUNTRY_PK"
	PRIMARY KEY ("IC_UID");


/* -----------------------------------------------------------------------
   ISO_LOCATION
   ----------------------------------------------------------------------- */

DROP TABLE "ISO_LOCATION" CASCADE CONSTRAINTS;


CREATE TABLE "ISO_LOCATION"
(
	"IC_UID" VARCHAR2(2) default '' NOT NULL,
	"IL_UID" VARCHAR2(5) default '' NOT NULL,
	"IL_NAME" VARCHAR2(255),
	"IL_NORMAL_NAME" VARCHAR2(255),
	"IS_UID" VARCHAR2(4)
);

	ALTER TABLE "ISO_LOCATION"
		ADD CONSTRAINT "ISO_LOCATION_PK"
	PRIMARY KEY ("IC_UID","IL_UID");


/* -----------------------------------------------------------------------
   ISO_SUBDIVISION
   ----------------------------------------------------------------------- */

DROP TABLE "ISO_SUBDIVISION" CASCADE CONSTRAINTS;


CREATE TABLE "ISO_SUBDIVISION"
(
	"IC_UID" VARCHAR2(2) default '' NOT NULL,
	"IS_UID" VARCHAR2(4) default '' NOT NULL,
	"IS_NAME" VARCHAR2(255) default '' NOT NULL
);

	ALTER TABLE "ISO_SUBDIVISION"
		ADD CONSTRAINT "ISO_SUBDIVISION_PK"
	PRIMARY KEY ("IC_UID","IS_UID");


/* -----------------------------------------------------------------------
   LANGUAGE
   ----------------------------------------------------------------------- */

DROP TABLE "LANGUAGE" CASCADE CONSTRAINTS;


CREATE TABLE "LANGUAGE"
(
	"LAN_ID" VARCHAR2(4) default '' NOT NULL,
	"LAN_NAME" VARCHAR2(30) default '' NOT NULL,
	"LAN_NATIVE_NAME" VARCHAR2(30) default '' NOT NULL,
	"LAN_DIRECTION" CHAR(1) default 'L' NOT NULL,
	"LAN_WEIGHT" NUMBER default 0 NOT NULL,
	"LAN_ENABLED" CHAR(1) default '1' NOT NULL,
	"LAN_CALENDAR" VARCHAR2(30) default 'GREGORIAN' NOT NULL
);

	ALTER TABLE "LANGUAGE"
		ADD CONSTRAINT "LANGUAGE_PK"
	PRIMARY KEY ("LAN_ID");


/* -----------------------------------------------------------------------
   LEXICO
   ----------------------------------------------------------------------- */

DROP TABLE "LEXICO" CASCADE CONSTRAINTS;


CREATE TABLE "LEXICO"
(
	"LEX_TOPIC" VARCHAR2(64) default '' NOT NULL,
	"LEX_KEY" VARCHAR2(128) default '' NOT NULL,
	"LEX_VALUE" VARCHAR2(128) default '' NOT NULL,
	"LEX_CAPTION" VARCHAR2(128) default '' NOT NULL
);

	ALTER TABLE "LEXICO"
		ADD CONSTRAINT "LEXICO_PK"
	PRIMARY KEY ("LEX_TOPIC","LEX_KEY");


/* -----------------------------------------------------------------------
   OUTPUT_DOCUMENT
   ----------------------------------------------------------------------- */

DROP TABLE "OUTPUT_DOCUMENT" CASCADE CONSTRAINTS;


CREATE TABLE "OUTPUT_DOCUMENT"
(
	"OUT_DOC_UID" VARCHAR2(32) default '' NOT NULL,
	"PRO_UID" VARCHAR2(32) default '' NOT NULL,
	"OUT_DOC_REPORT_GENERATOR" VARCHAR2(10) default 'HTML2PDF' NOT NULL,
	"OUT_DOC_LANDSCAPE" NUMBER(3,0) default 0 NOT NULL,
	"OUT_DOC_MEDIA" VARCHAR2(10) default 'Letter' NOT NULL,
	"OUT_DOC_LEFT_MARGIN" NUMBER default 30,
	"OUT_DOC_RIGHT_MARGIN" NUMBER default 15,
	"OUT_DOC_TOP_MARGIN" NUMBER default 15,
	"OUT_DOC_BOTTOM_MARGIN" NUMBER default 15,
	"OUT_DOC_GENERATE" VARCHAR2(10) default 'BOTH' NOT NULL,
	"OUT_DOC_TYPE" VARCHAR2(32) default 'HTML' NOT NULL,
	"OUT_DOC_CURRENT_REVISION" NUMBER default 0,
	"OUT_DOC_FIELD_MAPPING" VARCHAR2(2000),
	"OUT_DOC_VERSIONING" NUMBER(3,0) default 0 NOT NULL,
	"OUT_DOC_DESTINATION_PATH" VARCHAR2(2000),
	"OUT_DOC_TAGS" VARCHAR2(2000),
	"OUT_DOC_PDF_SECURITY_ENABLED" NUMBER(3,0) default 0,
	"OUT_DOC_PDF_SECURITY_OPEN_PASSWORD" VARCHAR2(32) default '',
	"OUT_DOC_PDF_SECURITY_OWNER_PASSWORD" VARCHAR2(32) default '',
	"OUT_DOC_PDF_SECURITY_PERMISSIONS" VARCHAR2(150) default '',
	"OUT_DOC_OPEN_TYPE" NUMBER default 0
);

	ALTER TABLE "OUTPUT_DOCUMENT"
		ADD CONSTRAINT "OUTPUT_DOCUMENT_PK"
	PRIMARY KEY ("OUT_DOC_UID");


/* -----------------------------------------------------------------------
   PROCESS
   ----------------------------------------------------------------------- */

DROP TABLE "PROCESS" CASCADE CONSTRAINTS;


CREATE TABLE "PROCESS"
(
	"PRO_UID" VARCHAR2(32) default '' NOT NULL,
	"PRO_PARENT" VARCHAR2(32) default '0' NOT NULL,
	"PRO_TIME" FLOAT default 1 NOT NULL,
	"PRO_TIMEUNIT" VARCHAR2(20) default 'DAYS' NOT NULL,
	"PRO_STATUS" VARCHAR2(20) default 'ACTIVE' NOT NULL,
	"PRO_TYPE_DAY" CHAR(1) default '0' NOT NULL,
	"PRO_TYPE" VARCHAR2(20) default 'NORMAL' NOT NULL,
	"PRO_ASSIGNMENT" VARCHAR2(20) default 'FALSE' NOT NULL,
	"PRO_SHOW_MAP" NUMBER(3,0) default 1 NOT NULL,
	"PRO_SHOW_MESSAGE" NUMBER(3,0) default 1 NOT NULL,
	"PRO_SUBPROCESS" NUMBER(3,0) default 0 NOT NULL,
	"PRO_TRI_DELETED" VARCHAR2(32) default '' NOT NULL,
	"PRO_TRI_CANCELED" VARCHAR2(32) default '' NOT NULL,
	"PRO_TRI_PAUSED" VARCHAR2(32) default '' NOT NULL,
	"PRO_TRI_REASSIGNED" VARCHAR2(32) default '' NOT NULL,
	"PRO_SHOW_DELEGATE" NUMBER(3,0) default 1 NOT NULL,
	"PRO_SHOW_DYNAFORM" NUMBER(3,0) default 0 NOT NULL,
	"PRO_CATEGORY" VARCHAR2(48) default '' NOT NULL,
	"PRO_SUB_CATEGORY" VARCHAR2(48) default '' NOT NULL,
	"PRO_INDUSTRY" NUMBER default 1 NOT NULL,
	"PRO_UPDATE_DATE" DATE,
	"PRO_CREATE_DATE" DATE  NOT NULL,
	"PRO_CREATE_USER" VARCHAR2(32) default '' NOT NULL,
	"PRO_HEIGHT" NUMBER default 5000 NOT NULL,
	"PRO_WIDTH" NUMBER default 10000 NOT NULL,
	"PRO_TITLE_X" NUMBER default 0 NOT NULL,
	"PRO_TITLE_Y" NUMBER default 6 NOT NULL,
	"PRO_DEBUG" NUMBER default 0 NOT NULL,
	"PRO_DYNAFORMS" VARCHAR2(2000),
	"PRO_DERIVATION_SCREEN_TPL" VARCHAR2(128) default ''
);

	ALTER TABLE "PROCESS"
		ADD CONSTRAINT "PROCESS_PK"
	PRIMARY KEY ("PRO_UID");


/* -----------------------------------------------------------------------
   PROCESS_OWNER
   ----------------------------------------------------------------------- */

DROP TABLE "PROCESS_OWNER" CASCADE CONSTRAINTS;


CREATE TABLE "PROCESS_OWNER"
(
	"OWN_UID" VARCHAR2(32) default '' NOT NULL,
	"PRO_UID" VARCHAR2(32) default '' NOT NULL
);

	ALTER TABLE "PROCESS_OWNER"
		ADD CONSTRAINT "PROCESS_OWNER_PK"
	PRIMARY KEY ("OWN_UID","PRO_UID");


/* -----------------------------------------------------------------------
   REPORT_TABLE
   ----------------------------------------------------------------------- */

DROP TABLE "REPORT_TABLE" CASCADE CONSTRAINTS;


CREATE TABLE "REPORT_TABLE"
(
	"REP_TAB_UID" VARCHAR2(32) default '' NOT NULL,
	"PRO_UID" VARCHAR2(32) default '' NOT NULL,
	"REP_TAB_NAME" VARCHAR2(100) default '' NOT NULL,
	"REP_TAB_TYPE" VARCHAR2(6) default '' NOT NULL,
	"REP_TAB_GRID" VARCHAR2(150) default '',
	"REP_TAB_CONNECTION" VARCHAR2(32) default '' NOT NULL,
	"REP_TAB_CREATE_DATE" DATE  NOT NULL,
	"REP_TAB_STATUS" CHAR(8) default 'ACTIVE' NOT NULL
);

	ALTER TABLE "REPORT_TABLE"
		ADD CONSTRAINT "REPORT_TABLE_PK"
	PRIMARY KEY ("REP_TAB_UID");


/* -----------------------------------------------------------------------
   REPORT_VAR
   ----------------------------------------------------------------------- */

DROP TABLE "REPORT_VAR" CASCADE CONSTRAINTS;


CREATE TABLE "REPORT_VAR"
(
	"REP_VAR_UID" VARCHAR2(32) default '' NOT NULL,
	"PRO_UID" VARCHAR2(32) default '' NOT NULL,
	"REP_TAB_UID" VARCHAR2(32) default '' NOT NULL,
	"REP_VAR_NAME" VARCHAR2(255) default '' NOT NULL,
	"REP_VAR_TYPE" VARCHAR2(20) default '' NOT NULL
);

	ALTER TABLE "REPORT_VAR"
		ADD CONSTRAINT "REPORT_VAR_PK"
	PRIMARY KEY ("REP_VAR_UID");


/* -----------------------------------------------------------------------
   ROUTE
   ----------------------------------------------------------------------- */

DROP TABLE "ROUTE" CASCADE CONSTRAINTS;


CREATE TABLE "ROUTE"
(
	"ROU_UID" VARCHAR2(32) default '' NOT NULL,
	"ROU_PARENT" VARCHAR2(32) default '0' NOT NULL,
	"PRO_UID" VARCHAR2(32) default '' NOT NULL,
	"TAS_UID" VARCHAR2(32) default '' NOT NULL,
	"ROU_NEXT_TASK" VARCHAR2(32) default '0' NOT NULL,
	"ROU_CASE" NUMBER default 0 NOT NULL,
	"ROU_TYPE" VARCHAR2(25) default 'SEQUENTIAL' NOT NULL,
	"ROU_CONDITION" VARCHAR2(512) default '' NOT NULL,
	"ROU_TO_LAST_USER" VARCHAR2(20) default 'FALSE' NOT NULL,
	"ROU_OPTIONAL" VARCHAR2(20) default 'FALSE' NOT NULL,
	"ROU_SEND_EMAIL" VARCHAR2(20) default 'TRUE' NOT NULL,
	"ROU_SOURCEANCHOR" NUMBER default 1,
	"ROU_TARGETANCHOR" NUMBER default 0,
	"ROU_TO_PORT" NUMBER default 1 NOT NULL,
	"ROU_FROM_PORT" NUMBER default 2 NOT NULL,
	"ROU_EVN_UID" VARCHAR2(32) default '' NOT NULL,
	"GAT_UID" VARCHAR2(32) default '' NOT NULL
);

	ALTER TABLE "ROUTE"
		ADD CONSTRAINT "ROUTE_PK"
	PRIMARY KEY ("ROU_UID");


/* -----------------------------------------------------------------------
   STEP
   ----------------------------------------------------------------------- */

DROP TABLE "STEP" CASCADE CONSTRAINTS;


CREATE TABLE "STEP"
(
	"STEP_UID" VARCHAR2(32) default '' NOT NULL,
	"PRO_UID" VARCHAR2(32) default '0' NOT NULL,
	"TAS_UID" VARCHAR2(32) default '0' NOT NULL,
	"STEP_TYPE_OBJ" VARCHAR2(20) default 'DYNAFORM' NOT NULL,
	"STEP_UID_OBJ" VARCHAR2(32) default '0' NOT NULL,
	"STEP_CONDITION" VARCHAR2(2000)  NOT NULL,
	"STEP_POSITION" NUMBER default 0 NOT NULL,
	"STEP_MODE" VARCHAR2(10) default 'EDIT'
);

	ALTER TABLE "STEP"
		ADD CONSTRAINT "STEP_PK"
	PRIMARY KEY ("STEP_UID");


/* -----------------------------------------------------------------------
   STEP_TRIGGER
   ----------------------------------------------------------------------- */

DROP TABLE "STEP_TRIGGER" CASCADE CONSTRAINTS;


CREATE TABLE "STEP_TRIGGER"
(
	"STEP_UID" VARCHAR2(32) default '' NOT NULL,
	"TAS_UID" VARCHAR2(32) default '' NOT NULL,
	"TRI_UID" VARCHAR2(32) default '' NOT NULL,
	"ST_TYPE" VARCHAR2(20) default '' NOT NULL,
	"ST_CONDITION" VARCHAR2(255) default '' NOT NULL,
	"ST_POSITION" NUMBER default 0 NOT NULL
);

	ALTER TABLE "STEP_TRIGGER"
		ADD CONSTRAINT "STEP_TRIGGER_PK"
	PRIMARY KEY ("STEP_UID","TAS_UID","TRI_UID","ST_TYPE");


/* -----------------------------------------------------------------------
   SWIMLANES_ELEMENTS
   ----------------------------------------------------------------------- */

DROP TABLE "SWIMLANES_ELEMENTS" CASCADE CONSTRAINTS;


CREATE TABLE "SWIMLANES_ELEMENTS"
(
	"SWI_UID" VARCHAR2(32) default '' NOT NULL,
	"PRO_UID" VARCHAR2(32) default '' NOT NULL,
	"SWI_TYPE" VARCHAR2(20) default 'LINE' NOT NULL,
	"SWI_X" NUMBER default 0 NOT NULL,
	"SWI_Y" NUMBER default 0 NOT NULL,
	"SWI_WIDTH" NUMBER default 0 NOT NULL,
	"SWI_HEIGHT" NUMBER default 0 NOT NULL,
	"SWI_NEXT_UID" VARCHAR2(32) default ''
);

	ALTER TABLE "SWIMLANES_ELEMENTS"
		ADD CONSTRAINT "SWIMLANES_ELEMENTS_PK"
	PRIMARY KEY ("SWI_UID");


/* -----------------------------------------------------------------------
   TASK
   ----------------------------------------------------------------------- */

DROP TABLE "TASK" CASCADE CONSTRAINTS;


CREATE TABLE "TASK"
(
	"PRO_UID" VARCHAR2(32) default '' NOT NULL,
	"TAS_UID" VARCHAR2(32) default '' NOT NULL,
	"TAS_TYPE" VARCHAR2(20) default 'NORMAL' NOT NULL,
	"TAS_DURATION" FLOAT default 0 NOT NULL,
	"TAS_DELAY_TYPE" VARCHAR2(30) default '' NOT NULL,
	"TAS_TEMPORIZER" FLOAT default 0 NOT NULL,
	"TAS_TYPE_DAY" CHAR(1) default '1' NOT NULL,
	"TAS_TIMEUNIT" VARCHAR2(20) default 'DAYS' NOT NULL,
	"TAS_ALERT" VARCHAR2(20) default 'FALSE' NOT NULL,
	"TAS_PRIORITY_VARIABLE" VARCHAR2(100) default '' NOT NULL,
	"TAS_ASSIGN_TYPE" VARCHAR2(30) default 'BALANCED' NOT NULL,
	"TAS_ASSIGN_VARIABLE" VARCHAR2(100) default '@@SYS_NEXT_USER_TO_BE_ASSIGNED' NOT NULL,
	"TAS_GROUP_VARIABLE" VARCHAR2(100) default '',
	"TAS_MI_INSTANCE_VARIABLE" VARCHAR2(100) default '@@SYS_VAR_TOTAL_INSTANCE' NOT NULL,
	"TAS_MI_COMPLETE_VARIABLE" VARCHAR2(100) default '@@SYS_VAR_TOTAL_INSTANCES_COMPLETE' NOT NULL,
	"TAS_ASSIGN_LOCATION" VARCHAR2(20) default 'FALSE' NOT NULL,
	"TAS_ASSIGN_LOCATION_ADHOC" VARCHAR2(20) default 'FALSE' NOT NULL,
	"TAS_TRANSFER_FLY" VARCHAR2(20) default 'FALSE' NOT NULL,
	"TAS_LAST_ASSIGNED" VARCHAR2(32) default '0' NOT NULL,
	"TAS_USER" VARCHAR2(32) default '0' NOT NULL,
	"TAS_CAN_UPLOAD" VARCHAR2(20) default 'FALSE' NOT NULL,
	"TAS_VIEW_UPLOAD" VARCHAR2(20) default 'FALSE' NOT NULL,
	"TAS_VIEW_ADDITIONAL_DOCUMENTATION" VARCHAR2(20) default 'FALSE' NOT NULL,
	"TAS_CAN_CANCEL" VARCHAR2(20) default 'FALSE' NOT NULL,
	"TAS_OWNER_APP" VARCHAR2(32) default '' NOT NULL,
	"STG_UID" VARCHAR2(32) default '' NOT NULL,
	"TAS_CAN_PAUSE" VARCHAR2(20) default 'FALSE' NOT NULL,
	"TAS_CAN_SEND_MESSAGE" VARCHAR2(20) default 'TRUE' NOT NULL,
	"TAS_CAN_DELETE_DOCS" VARCHAR2(20) default 'FALSE' NOT NULL,
	"TAS_SELF_SERVICE" VARCHAR2(20) default 'FALSE' NOT NULL,
	"TAS_START" VARCHAR2(20) default 'FALSE' NOT NULL,
	"TAS_TO_LAST_USER" VARCHAR2(20) default 'FALSE' NOT NULL,
	"TAS_SEND_LAST_EMAIL" VARCHAR2(20) default 'TRUE' NOT NULL,
	"TAS_DERIVATION" VARCHAR2(100) default 'NORMAL' NOT NULL,
	"TAS_POSX" NUMBER default 0 NOT NULL,
	"TAS_POSY" NUMBER default 0 NOT NULL,
	"TAS_WIDTH" NUMBER default 110 NOT NULL,
	"TAS_HEIGHT" NUMBER default 60 NOT NULL,
	"TAS_COLOR" VARCHAR2(32) default '' NOT NULL,
	"TAS_EVN_UID" VARCHAR2(32) default '' NOT NULL,
	"TAS_BOUNDARY" VARCHAR2(32) default '' NOT NULL,
	"TAS_DERIVATION_SCREEN_TPL" VARCHAR2(128) default '',
	"TAS_SELFSERVICE_TIMEOUT" NUMBER default 0,
	"TAS_SELFSERVICE_TIME" VARCHAR2(15) default '',
	"TAS_SELFSERVICE_TIME_UNIT" VARCHAR2(15) default '',
	"TAS_SELFSERVICE_TRIGGER_UID" VARCHAR2(32) default ''
);

	ALTER TABLE "TASK"
		ADD CONSTRAINT "TASK_PK"
	PRIMARY KEY ("TAS_UID");


/* -----------------------------------------------------------------------
   TASK_USER
   ----------------------------------------------------------------------- */

DROP TABLE "TASK_USER" CASCADE CONSTRAINTS;


CREATE TABLE "TASK_USER"
(
	"TAS_UID" VARCHAR2(32) default '' NOT NULL,
	"USR_UID" VARCHAR2(32) default '' NOT NULL,
	"TU_TYPE" NUMBER default 1 NOT NULL,
	"TU_RELATION" NUMBER default 0 NOT NULL
);

	ALTER TABLE "TASK_USER"
		ADD CONSTRAINT "TASK_USER_PK"
	PRIMARY KEY ("TAS_UID","USR_UID","TU_TYPE","TU_RELATION");


/* -----------------------------------------------------------------------
   TRANSLATION
   ----------------------------------------------------------------------- */

DROP TABLE "TRANSLATION" CASCADE CONSTRAINTS;


CREATE TABLE "TRANSLATION"
(
	"TRN_CATEGORY" VARCHAR2(100) default '' NOT NULL,
	"TRN_ID" VARCHAR2(100) default '' NOT NULL,
	"TRN_LANG" VARCHAR2(10) default 'en' NOT NULL,
	"TRN_VALUE" VARCHAR2(2000)  NOT NULL,
	"TRN_UPDATE_DATE" DATE
);

	ALTER TABLE "TRANSLATION"
		ADD CONSTRAINT "TRANSLATION_PK"
	PRIMARY KEY ("TRN_CATEGORY","TRN_ID","TRN_LANG");


/* -----------------------------------------------------------------------
   TRIGGERS
   ----------------------------------------------------------------------- */

DROP TABLE "TRIGGERS" CASCADE CONSTRAINTS;


CREATE TABLE "TRIGGERS"
(
	"TRI_UID" VARCHAR2(32) default '' NOT NULL,
	"PRO_UID" VARCHAR2(32) default '' NOT NULL,
	"TRI_TYPE" VARCHAR2(20) default 'SCRIPT' NOT NULL,
	"TRI_WEBBOT" VARCHAR2(2000)  NOT NULL,
	"TRI_PARAM" VARCHAR2(2000)
);

	ALTER TABLE "TRIGGERS"
		ADD CONSTRAINT "TRIGGERS_PK"
	PRIMARY KEY ("TRI_UID");


/* -----------------------------------------------------------------------
   USERS
   ----------------------------------------------------------------------- */

DROP TABLE "USERS" CASCADE CONSTRAINTS;


CREATE TABLE "USERS"
(
	"USR_UID" VARCHAR2(32) default '' NOT NULL,
	"USR_USERNAME" VARCHAR2(100) default '' NOT NULL,
	"USR_PASSWORD" VARCHAR2(32) default '' NOT NULL,
	"USR_FIRSTNAME" VARCHAR2(50) default '' NOT NULL,
	"USR_LASTNAME" VARCHAR2(50) default '' NOT NULL,
	"USR_EMAIL" VARCHAR2(100) default '' NOT NULL,
	"USR_DUE_DATE" DATE  NOT NULL,
	"USR_CREATE_DATE" DATE  NOT NULL,
	"USR_UPDATE_DATE" DATE  NOT NULL,
	"USR_STATUS" VARCHAR2(32) default 'ACTIVE' NOT NULL,
	"USR_COUNTRY" VARCHAR2(3) default '' NOT NULL,
	"USR_CITY" VARCHAR2(3) default '' NOT NULL,
	"USR_LOCATION" VARCHAR2(3) default '' NOT NULL,
	"USR_ADDRESS" VARCHAR2(255) default '' NOT NULL,
	"USR_PHONE" VARCHAR2(24) default '' NOT NULL,
	"USR_FAX" VARCHAR2(24) default '' NOT NULL,
	"USR_CELLULAR" VARCHAR2(24) default '' NOT NULL,
	"USR_ZIP_CODE" VARCHAR2(16) default '' NOT NULL,
	"DEP_UID" VARCHAR2(32) default '' NOT NULL,
	"USR_POSITION" VARCHAR2(100) default '' NOT NULL,
	"USR_RESUME" VARCHAR2(100) default '' NOT NULL,
	"USR_BIRTHDAY" DATE,
	"USR_ROLE" VARCHAR2(32) default 'PROCESSMAKER_ADMIN',
	"USR_REPORTS_TO" VARCHAR2(32) default '',
	"USR_REPLACED_BY" VARCHAR2(32) default '',
	"USR_UX" VARCHAR2(128) default 'NORMAL'
);

	ALTER TABLE "USERS"
		ADD CONSTRAINT "USERS_PK"
	PRIMARY KEY ("USR_UID");


/* -----------------------------------------------------------------------
   APP_THREAD
   ----------------------------------------------------------------------- */

DROP TABLE "APP_THREAD" CASCADE CONSTRAINTS;


CREATE TABLE "APP_THREAD"
(
	"APP_UID" VARCHAR2(32) default '' NOT NULL,
	"APP_THREAD_INDEX" NUMBER default 0 NOT NULL,
	"APP_THREAD_PARENT" NUMBER default 0 NOT NULL,
	"APP_THREAD_STATUS" VARCHAR2(32) default 'OPEN' NOT NULL,
	"DEL_INDEX" NUMBER default 0 NOT NULL
);

	ALTER TABLE "APP_THREAD"
		ADD CONSTRAINT "APP_THREAD_PK"
	PRIMARY KEY ("APP_UID","APP_THREAD_INDEX");


/* -----------------------------------------------------------------------
   APP_DELAY
   ----------------------------------------------------------------------- */

DROP TABLE "APP_DELAY" CASCADE CONSTRAINTS;


CREATE TABLE "APP_DELAY"
(
	"APP_DELAY_UID" VARCHAR2(32) default '' NOT NULL,
	"PRO_UID" VARCHAR2(32) default '0' NOT NULL,
	"APP_UID" VARCHAR2(32) default '0' NOT NULL,
	"APP_THREAD_INDEX" NUMBER default 0 NOT NULL,
	"APP_DEL_INDEX" NUMBER default 0 NOT NULL,
	"APP_TYPE" VARCHAR2(20) default '0' NOT NULL,
	"APP_STATUS" VARCHAR2(20) default '0' NOT NULL,
	"APP_NEXT_TASK" VARCHAR2(32) default '0',
	"APP_DELEGATION_USER" VARCHAR2(32) default '0',
	"APP_ENABLE_ACTION_USER" VARCHAR2(32) default '0' NOT NULL,
	"APP_ENABLE_ACTION_DATE" DATE  NOT NULL,
	"APP_DISABLE_ACTION_USER" VARCHAR2(32) default '0',
	"APP_DISABLE_ACTION_DATE" DATE,
	"APP_AUTOMATIC_DISABLED_DATE" DATE
);

	ALTER TABLE "APP_DELAY"
		ADD CONSTRAINT "APP_DELAY_PK"
	PRIMARY KEY ("APP_DELAY_UID");
CREATE INDEX "indexAppDelay" ON "APP_DELAY" ("PRO_UID","APP_UID","APP_THREAD_INDEX","APP_DEL_INDEX","APP_NEXT_TASK","APP_DELEGATION_USER","APP_DISABLE_ACTION_USER");
CREATE INDEX "indexAppDelay" ON "APP_DELAY" ("PRO_UID","APP_UID","APP_THREAD_INDEX","APP_DEL_INDEX","APP_NEXT_TASK","APP_DELEGATION_USER","APP_DISABLE_ACTION_USER");


/* -----------------------------------------------------------------------
   PROCESS_USER
   ----------------------------------------------------------------------- */

DROP TABLE "PROCESS_USER" CASCADE CONSTRAINTS;


CREATE TABLE "PROCESS_USER"
(
	"PU_UID" VARCHAR2(32) default '' NOT NULL,
	"PRO_UID" VARCHAR2(32) default '' NOT NULL,
	"USR_UID" VARCHAR2(32) default '' NOT NULL,
	"PU_TYPE" VARCHAR2(20) default '' NOT NULL
);

	ALTER TABLE "PROCESS_USER"
		ADD CONSTRAINT "PROCESS_USER_PK"
	PRIMARY KEY ("PU_UID");


/* -----------------------------------------------------------------------
   SESSION
   ----------------------------------------------------------------------- */

DROP TABLE "SESSION" CASCADE CONSTRAINTS;


CREATE TABLE "SESSION"
(
	"SES_UID" VARCHAR2(32) default '' NOT NULL,
	"SES_STATUS" VARCHAR2(16) default 'ACTIVE' NOT NULL,
	"USR_UID" VARCHAR2(32) default 'ACTIVE' NOT NULL,
	"SES_REMOTE_IP" VARCHAR2(32) default '0.0.0.0' NOT NULL,
	"SES_INIT_DATE" VARCHAR2(19) default '' NOT NULL,
	"SES_DUE_DATE" VARCHAR2(19) default '' NOT NULL,
	"SES_END_DATE" VARCHAR2(19) default '' NOT NULL
);

	ALTER TABLE "SESSION"
		ADD CONSTRAINT "SESSION_PK"
	PRIMARY KEY ("SES_UID");
CREATE INDEX "indexSession" ON "SESSION" ("SES_UID");
CREATE INDEX "indexSession" ON "SESSION" ("SES_UID");


/* -----------------------------------------------------------------------
   DB_SOURCE
   ----------------------------------------------------------------------- */

DROP TABLE "DB_SOURCE" CASCADE CONSTRAINTS;


CREATE TABLE "DB_SOURCE"
(
	"DBS_UID" VARCHAR2(32) default '' NOT NULL,
	"PRO_UID" VARCHAR2(32) default '0' NOT NULL,
	"DBS_TYPE" VARCHAR2(8) default '0' NOT NULL,
	"DBS_SERVER" VARCHAR2(100) default '0' NOT NULL,
	"DBS_DATABASE_NAME" VARCHAR2(100) default '0' NOT NULL,
	"DBS_USERNAME" VARCHAR2(32) default '0' NOT NULL,
	"DBS_PASSWORD" VARCHAR2(32) default '',
	"DBS_PORT" NUMBER default 0,
	"DBS_ENCODE" VARCHAR2(32) default '',
 "DBS_CONNECTION_TYPE" VARCHAR2(32) default 'NORMAL',
 "DBS_TNS" VARCHAR2(256) default ''
);

	ALTER TABLE "DB_SOURCE"
		ADD CONSTRAINT "DB_SOURCE_PK"
	PRIMARY KEY ("DBS_UID","PRO_UID");
CREATE INDEX "indexDBSource" ON "DB_SOURCE" ("PRO_UID");
CREATE INDEX "indexDBSource" ON "DB_SOURCE" ("PRO_UID");


/* -----------------------------------------------------------------------
   STEP_SUPERVISOR
   ----------------------------------------------------------------------- */

DROP TABLE "STEP_SUPERVISOR" CASCADE CONSTRAINTS;


CREATE TABLE "STEP_SUPERVISOR"
(
	"STEP_UID" VARCHAR2(32) default '' NOT NULL,
	"PRO_UID" VARCHAR2(32) default '0' NOT NULL,
	"STEP_TYPE_OBJ" VARCHAR2(20) default 'DYNAFORM' NOT NULL,
	"STEP_UID_OBJ" VARCHAR2(32) default '0' NOT NULL,
	"STEP_POSITION" NUMBER default 0 NOT NULL
);

	ALTER TABLE "STEP_SUPERVISOR"
		ADD CONSTRAINT "STEP_SUPERVISOR_PK"
	PRIMARY KEY ("STEP_UID");
CREATE INDEX "indexStepSupervisor" ON "STEP_SUPERVISOR" ("PRO_UID","STEP_TYPE_OBJ","STEP_UID_OBJ");
CREATE INDEX "indexStepSupervisor" ON "STEP_SUPERVISOR" ("PRO_UID","STEP_TYPE_OBJ","STEP_UID_OBJ");


/* -----------------------------------------------------------------------
   OBJECT_PERMISSION
   ----------------------------------------------------------------------- */

DROP TABLE "OBJECT_PERMISSION" CASCADE CONSTRAINTS;


CREATE TABLE "OBJECT_PERMISSION"
(
	"OP_UID" VARCHAR2(32) default '0' NOT NULL,
	"PRO_UID" VARCHAR2(32) default '0' NOT NULL,
	"TAS_UID" VARCHAR2(32) default '0' NOT NULL,
	"USR_UID" VARCHAR2(32) default '0' NOT NULL,
	"OP_USER_RELATION" NUMBER default 0 NOT NULL,
	"OP_TASK_SOURCE" VARCHAR2(32) default '0',
	"OP_PARTICIPATE" NUMBER default 0 NOT NULL,
	"OP_OBJ_TYPE" VARCHAR2(15) default '0' NOT NULL,
	"OP_OBJ_UID" VARCHAR2(32) default '0' NOT NULL,
	"OP_ACTION" VARCHAR2(10) default '0' NOT NULL,
	"OP_CASE_STATUS" VARCHAR2(10) default '0'
);

	ALTER TABLE "OBJECT_PERMISSION"
		ADD CONSTRAINT "OBJECT_PERMISSION_PK"
	PRIMARY KEY ("OP_UID");
CREATE INDEX "indexObjctPermission" ON "OBJECT_PERMISSION" ("PRO_UID","TAS_UID","USR_UID","OP_TASK_SOURCE","OP_OBJ_UID");
CREATE INDEX "indexObjctPermission" ON "OBJECT_PERMISSION" ("PRO_UID","TAS_UID","USR_UID","OP_TASK_SOURCE","OP_OBJ_UID");


/* -----------------------------------------------------------------------
   CASE_TRACKER
   ----------------------------------------------------------------------- */

DROP TABLE "CASE_TRACKER" CASCADE CONSTRAINTS;


CREATE TABLE "CASE_TRACKER"
(
	"PRO_UID" VARCHAR2(32) default '0' NOT NULL,
	"CT_MAP_TYPE" VARCHAR2(10) default '0' NOT NULL,
	"CT_DERIVATION_HISTORY" NUMBER default 0 NOT NULL,
	"CT_MESSAGE_HISTORY" NUMBER default 0 NOT NULL
);

	ALTER TABLE "CASE_TRACKER"
		ADD CONSTRAINT "CASE_TRACKER_PK"
	PRIMARY KEY ("PRO_UID");


/* -----------------------------------------------------------------------
   CASE_TRACKER_OBJECT
   ----------------------------------------------------------------------- */

DROP TABLE "CASE_TRACKER_OBJECT" CASCADE CONSTRAINTS;


CREATE TABLE "CASE_TRACKER_OBJECT"
(
	"CTO_UID" VARCHAR2(32) default '' NOT NULL,
	"PRO_UID" VARCHAR2(32) default '0' NOT NULL,
	"CTO_TYPE_OBJ" VARCHAR2(20) default 'DYNAFORM' NOT NULL,
	"CTO_UID_OBJ" VARCHAR2(32) default '0' NOT NULL,
	"CTO_CONDITION" VARCHAR2(2000)  NOT NULL,
	"CTO_POSITION" NUMBER default 0 NOT NULL
);

	ALTER TABLE "CASE_TRACKER_OBJECT"
		ADD CONSTRAINT "CASE_TRACKER_OBJECT_PK"
	PRIMARY KEY ("CTO_UID");
CREATE INDEX "indexCaseTrackerObject" ON "CASE_TRACKER_OBJECT" ("PRO_UID","CTO_UID_OBJ");
CREATE INDEX "indexCaseTrackerObject" ON "CASE_TRACKER_OBJECT" ("PRO_UID","CTO_UID_OBJ");


/* -----------------------------------------------------------------------
   STAGE
   ----------------------------------------------------------------------- */

DROP TABLE "STAGE" CASCADE CONSTRAINTS;


CREATE TABLE "STAGE"
(
	"STG_UID" VARCHAR2(32) default '' NOT NULL,
	"PRO_UID" VARCHAR2(32) default '' NOT NULL,
	"STG_POSX" NUMBER default 0 NOT NULL,
	"STG_POSY" NUMBER default 0 NOT NULL,
	"STG_INDEX" NUMBER default 0 NOT NULL
);

	ALTER TABLE "STAGE"
		ADD CONSTRAINT "STAGE_PK"
	PRIMARY KEY ("STG_UID");


/* -----------------------------------------------------------------------
   SUB_PROCESS
   ----------------------------------------------------------------------- */

DROP TABLE "SUB_PROCESS" CASCADE CONSTRAINTS;


CREATE TABLE "SUB_PROCESS"
(
	"SP_UID" VARCHAR2(32) default '' NOT NULL,
	"PRO_UID" VARCHAR2(32) default '' NOT NULL,
	"TAS_UID" VARCHAR2(32) default '' NOT NULL,
	"PRO_PARENT" VARCHAR2(32) default '' NOT NULL,
	"TAS_PARENT" VARCHAR2(32) default '' NOT NULL,
	"SP_TYPE" VARCHAR2(20) default '' NOT NULL,
	"SP_SYNCHRONOUS" NUMBER default 0 NOT NULL,
	"SP_SYNCHRONOUS_TYPE" VARCHAR2(20) default '' NOT NULL,
	"SP_SYNCHRONOUS_WAIT" NUMBER default 0 NOT NULL,
	"SP_VARIABLES_OUT" VARCHAR2(2000)  NOT NULL,
	"SP_VARIABLES_IN" VARCHAR2(2000),
	"SP_GRID_IN" VARCHAR2(50) default '' NOT NULL
);

	ALTER TABLE "SUB_PROCESS"
		ADD CONSTRAINT "SUB_PROCESS_PK"
	PRIMARY KEY ("SP_UID");
CREATE INDEX "indexSubProcess" ON "SUB_PROCESS" ("PRO_UID","PRO_PARENT");
CREATE INDEX "indexSubProcess" ON "SUB_PROCESS" ("PRO_UID","PRO_PARENT");


/* -----------------------------------------------------------------------
   SUB_APPLICATION
   ----------------------------------------------------------------------- */

DROP TABLE "SUB_APPLICATION" CASCADE CONSTRAINTS;


CREATE TABLE "SUB_APPLICATION"
(
	"APP_UID" VARCHAR2(32) default '' NOT NULL,
	"APP_PARENT" VARCHAR2(32) default '' NOT NULL,
	"DEL_INDEX_PARENT" NUMBER default 0 NOT NULL,
	"DEL_THREAD_PARENT" NUMBER default 0 NOT NULL,
	"SA_STATUS" VARCHAR2(32) default '' NOT NULL,
	"SA_VALUES_OUT" VARCHAR2(2000)  NOT NULL,
	"SA_VALUES_IN" VARCHAR2(2000),
	"SA_INIT_DATE" DATE,
	"SA_FINISH_DATE" DATE
);

	ALTER TABLE "SUB_APPLICATION"
		ADD CONSTRAINT "SUB_APPLICATION_PK"
	PRIMARY KEY ("APP_UID","APP_PARENT","DEL_INDEX_PARENT","DEL_THREAD_PARENT");


/* -----------------------------------------------------------------------
   LOGIN_LOG
   ----------------------------------------------------------------------- */

DROP TABLE "LOGIN_LOG" CASCADE CONSTRAINTS;


CREATE TABLE "LOGIN_LOG"
(
	"LOG_UID" VARCHAR2(32) default '' NOT NULL,
	"LOG_STATUS" VARCHAR2(100) default '' NOT NULL,
	"LOG_IP" VARCHAR2(15) default '' NOT NULL,
	"LOG_SID" VARCHAR2(100) default '' NOT NULL,
	"LOG_INIT_DATE" DATE,
	"LOG_END_DATE" DATE,
	"LOG_CLIENT_HOSTNAME" VARCHAR2(100) default '' NOT NULL,
	"USR_UID" VARCHAR2(32) default '' NOT NULL
);

	ALTER TABLE "LOGIN_LOG"
		ADD CONSTRAINT "LOGIN_LOG_PK"
	PRIMARY KEY ("LOG_UID");
CREATE INDEX "indexLoginLog" ON "LOGIN_LOG" ("USR_UID","LOG_INIT_DATE");
CREATE INDEX "indexLoginLog" ON "LOGIN_LOG" ("USR_UID","LOG_INIT_DATE");


/* -----------------------------------------------------------------------
   USERS_PROPERTIES
   ----------------------------------------------------------------------- */

DROP TABLE "USERS_PROPERTIES" CASCADE CONSTRAINTS;


CREATE TABLE "USERS_PROPERTIES"
(
	"USR_UID" VARCHAR2(32) default '' NOT NULL,
	"USR_LAST_UPDATE_DATE" DATE,
	"USR_LOGGED_NEXT_TIME" NUMBER default 0,
	"USR_PASSWORD_HISTORY" VARCHAR2(2000)
);

	ALTER TABLE "USERS_PROPERTIES"
		ADD CONSTRAINT "USERS_PROPERTIES_PK"
	PRIMARY KEY ("USR_UID");


/* -----------------------------------------------------------------------
   ADDITIONAL_TABLES
   ----------------------------------------------------------------------- */

DROP TABLE "ADDITIONAL_TABLES" CASCADE CONSTRAINTS;


CREATE TABLE "ADDITIONAL_TABLES"
(
	"ADD_TAB_UID" VARCHAR2(32) default '' NOT NULL,
	"ADD_TAB_NAME" VARCHAR2(60) default '' NOT NULL,
	"ADD_TAB_CLASS_NAME" VARCHAR2(100) default '' NOT NULL,
	"ADD_TAB_DESCRIPTION" VARCHAR2(2000),
	"ADD_TAB_SDW_LOG_INSERT" NUMBER(3,0) default 0,
	"ADD_TAB_SDW_LOG_UPDATE" NUMBER(3,0) default 0,
	"ADD_TAB_SDW_LOG_DELETE" NUMBER(3,0) default 0,
	"ADD_TAB_SDW_LOG_SELECT" NUMBER(3,0) default 0,
	"ADD_TAB_SDW_MAX_LENGTH" NUMBER default 0,
	"ADD_TAB_SDW_AUTO_DELETE" NUMBER(3,0) default 0,
	"ADD_TAB_PLG_UID" VARCHAR2(32) default '',
	"DBS_UID" VARCHAR2(32) default '',
	"PRO_UID" VARCHAR2(32) default '',
	"ADD_TAB_TYPE" VARCHAR2(32) default '',
	"ADD_TAB_GRID" VARCHAR2(256) default '',
	"ADD_TAB_TAG" VARCHAR2(256) default ''
);

	ALTER TABLE "ADDITIONAL_TABLES"
		ADD CONSTRAINT "ADDITIONAL_TABLES_PK"
	PRIMARY KEY ("ADD_TAB_UID");


/* -----------------------------------------------------------------------
   FIELDS
   ----------------------------------------------------------------------- */

DROP TABLE "FIELDS" CASCADE CONSTRAINTS;


CREATE TABLE "FIELDS"
(
	"FLD_UID" VARCHAR2(32) default '' NOT NULL,
	"ADD_TAB_UID" VARCHAR2(32) default '' NOT NULL,
	"FLD_INDEX" NUMBER default 1 NOT NULL,
	"FLD_NAME" VARCHAR2(60) default '' NOT NULL,
	"FLD_DESCRIPTION" VARCHAR2(2000)  NOT NULL,
	"FLD_TYPE" VARCHAR2(20) default '' NOT NULL,
	"FLD_SIZE" NUMBER default 0,
	"FLD_NULL" NUMBER(3,0) default 1 NOT NULL,
	"FLD_AUTO_INCREMENT" NUMBER(3,0) default 0 NOT NULL,
	"FLD_KEY" NUMBER(3,0) default 0 NOT NULL,
	"FLD_FOREIGN_KEY" NUMBER(3,0) default 0 NOT NULL,
	"FLD_FOREIGN_KEY_TABLE" VARCHAR2(32) default '' NOT NULL,
	"FLD_DYN_NAME" VARCHAR2(128) default '',
	"FLD_DYN_UID" VARCHAR2(128) default '',
	"FLD_FILTER" NUMBER(3,0) default 0
);

	ALTER TABLE "FIELDS"
		ADD CONSTRAINT "FIELDS_PK"
	PRIMARY KEY ("FLD_UID");


/* -----------------------------------------------------------------------
   SHADOW_TABLE
   ----------------------------------------------------------------------- */

DROP TABLE "SHADOW_TABLE" CASCADE CONSTRAINTS;


CREATE TABLE "SHADOW_TABLE"
(
	"SHD_UID" VARCHAR2(32) default '' NOT NULL,
	"ADD_TAB_UID" VARCHAR2(32) default '' NOT NULL,
	"SHD_ACTION" VARCHAR2(10) default '' NOT NULL,
	"SHD_DETAILS" VARCHAR2(2000)  NOT NULL,
	"USR_UID" VARCHAR2(32) default '' NOT NULL,
	"APP_UID" VARCHAR2(32) default '' NOT NULL,
	"SHD_DATE" DATE
);

	ALTER TABLE "SHADOW_TABLE"
		ADD CONSTRAINT "SHADOW_TABLE_PK"
	PRIMARY KEY ("SHD_UID");
CREATE INDEX "indexShadowTable" ON "SHADOW_TABLE" ("SHD_UID");
CREATE INDEX "indexShadowTable" ON "SHADOW_TABLE" ("SHD_UID");


/* -----------------------------------------------------------------------
   EVENT
   ----------------------------------------------------------------------- */

DROP TABLE "EVENT" CASCADE CONSTRAINTS;


CREATE TABLE "EVENT"
(
	"EVN_UID" VARCHAR2(32) default '' NOT NULL,
	"PRO_UID" VARCHAR2(32) default '' NOT NULL,
	"EVN_STATUS" VARCHAR2(16) default 'OPEN' NOT NULL,
	"EVN_WHEN_OCCURS" VARCHAR2(32) default 'SINGLE',
	"EVN_RELATED_TO" VARCHAR2(16) default 'SINGLE',
	"TAS_UID" VARCHAR2(32) default '' NOT NULL,
	"EVN_TAS_UID_FROM" VARCHAR2(32) default '',
	"EVN_TAS_UID_TO" VARCHAR2(32) default '',
	"EVN_TAS_ESTIMATED_DURATION" FLOAT default 0,
	"EVN_TIME_UNIT" VARCHAR2(10) default 'DAYS' NOT NULL,
	"EVN_WHEN" FLOAT default 0 NOT NULL,
	"EVN_MAX_ATTEMPTS" NUMBER(3,0) default 3 NOT NULL,
	"EVN_ACTION" VARCHAR2(50) default '' NOT NULL,
	"EVN_CONDITIONS" VARCHAR2(2000),
	"EVN_ACTION_PARAMETERS" VARCHAR2(2000),
	"TRI_UID" VARCHAR2(32) default '',
	"EVN_POSX" NUMBER default 0 NOT NULL,
	"EVN_POSY" NUMBER default 0 NOT NULL,
	"EVN_TYPE" VARCHAR2(32) default '',
	"TAS_EVN_UID" VARCHAR2(32) default ''
);

	ALTER TABLE "EVENT"
		ADD CONSTRAINT "EVENT_PK"
	PRIMARY KEY ("EVN_UID");
CREATE INDEX "indexEventTable" ON "EVENT" ("EVN_UID");
CREATE INDEX "indexEventTable" ON "EVENT" ("EVN_UID");


/* -----------------------------------------------------------------------
   GATEWAY
   ----------------------------------------------------------------------- */

DROP TABLE "GATEWAY" CASCADE CONSTRAINTS;


CREATE TABLE "GATEWAY"
(
	"GAT_UID" VARCHAR2(32) default '' NOT NULL,
	"PRO_UID" VARCHAR2(32) default '' NOT NULL,
	"TAS_UID" VARCHAR2(32) default '' NOT NULL,
	"GAT_NEXT_TASK" VARCHAR2(32) default '' NOT NULL,
	"GAT_X" NUMBER default 0 NOT NULL,
	"GAT_Y" NUMBER default 0 NOT NULL,
	"GAT_TYPE" VARCHAR2(32) default '' NOT NULL
);

	ALTER TABLE "GATEWAY"
		ADD CONSTRAINT "GATEWAY_PK"
	PRIMARY KEY ("GAT_UID");


/* -----------------------------------------------------------------------
   APP_EVENT
   ----------------------------------------------------------------------- */

DROP TABLE "APP_EVENT" CASCADE CONSTRAINTS;


CREATE TABLE "APP_EVENT"
(
	"APP_UID" VARCHAR2(32) default '' NOT NULL,
	"DEL_INDEX" NUMBER default 0 NOT NULL,
	"EVN_UID" VARCHAR2(32) default '' NOT NULL,
	"APP_EVN_ACTION_DATE" DATE  NOT NULL,
	"APP_EVN_ATTEMPTS" NUMBER(3,0) default 0 NOT NULL,
	"APP_EVN_LAST_EXECUTION_DATE" DATE,
	"APP_EVN_STATUS" VARCHAR2(32) default 'OPEN' NOT NULL
);

	ALTER TABLE "APP_EVENT"
		ADD CONSTRAINT "APP_EVENT_PK"
	PRIMARY KEY ("APP_UID","DEL_INDEX","EVN_UID");


/* -----------------------------------------------------------------------
   APP_CACHE_VIEW
   ----------------------------------------------------------------------- */

DROP TABLE "APP_CACHE_VIEW" CASCADE CONSTRAINTS;


CREATE TABLE "APP_CACHE_VIEW"
(
	"APP_UID" VARCHAR2(32) default '' NOT NULL,
	"DEL_INDEX" NUMBER default 0 NOT NULL,
	"DEL_LAST_INDEX" NUMBER default 0 NOT NULL,
	"APP_NUMBER" NUMBER default 0 NOT NULL,
	"APP_STATUS" VARCHAR2(32) default '' NOT NULL,
	"USR_UID" VARCHAR2(32) default '' NOT NULL,
	"PREVIOUS_USR_UID" VARCHAR2(32) default '',
	"TAS_UID" VARCHAR2(32) default '' NOT NULL,
	"PRO_UID" VARCHAR2(32) default '' NOT NULL,
	"DEL_DELEGATE_DATE" DATE  NOT NULL,
	"DEL_INIT_DATE" DATE,
	"DEL_TASK_DUE_DATE" DATE,
	"DEL_FINISH_DATE" DATE,
	"DEL_THREAD_STATUS" VARCHAR2(32) default 'OPEN',
	"APP_THREAD_STATUS" VARCHAR2(32) default 'OPEN',
	"APP_TITLE" VARCHAR2(255) default '' NOT NULL,
	"APP_PRO_TITLE" VARCHAR2(255) default '' NOT NULL,
	"APP_TAS_TITLE" VARCHAR2(255) default '' NOT NULL,
	"APP_CURRENT_USER" VARCHAR2(128) default '',
	"APP_DEL_PREVIOUS_USER" VARCHAR2(128) default '',
	"DEL_PRIORITY" VARCHAR2(32) default '3' NOT NULL,
	"DEL_DURATION" FLOAT default 0,
	"DEL_QUEUE_DURATION" FLOAT default 0,
	"DEL_DELAY_DURATION" FLOAT default 0,
	"DEL_STARTED" NUMBER(3,0) default 0 NOT NULL,
	"DEL_FINISHED" NUMBER(3,0) default 0 NOT NULL,
	"DEL_DELAYED" NUMBER(3,0) default 0 NOT NULL,
	"APP_CREATE_DATE" DATE  NOT NULL,
	"APP_FINISH_DATE" DATE,
	"APP_UPDATE_DATE" DATE  NOT NULL,
	"APP_OVERDUE_PERCENTAGE" FLOAT  NOT NULL
);

	ALTER TABLE "APP_CACHE_VIEW"
		ADD CONSTRAINT "APP_CACHE_VIEW_PK"
	PRIMARY KEY ("APP_UID","DEL_INDEX");
CREATE INDEX "indexAppNumber" ON "APP_CACHE_VIEW" ("APP_NUMBER");
CREATE INDEX "indexAppUser" ON "APP_CACHE_VIEW" ("USR_UID","APP_STATUS");
CREATE INDEX "indexAppNumber" ON "APP_CACHE_VIEW" ("APP_NUMBER");
CREATE INDEX "indexAppUser" ON "APP_CACHE_VIEW" ("USR_UID","APP_STATUS");


/* -----------------------------------------------------------------------
   DIM_TIME_DELEGATE
   ----------------------------------------------------------------------- */

DROP TABLE "DIM_TIME_DELEGATE" CASCADE CONSTRAINTS;


CREATE TABLE "DIM_TIME_DELEGATE"
(
	"TIME_ID" VARCHAR2(10) default '' NOT NULL,
	"MONTH_ID" NUMBER default 0 NOT NULL,
	"QTR_ID" NUMBER default 0 NOT NULL,
	"YEAR_ID" NUMBER default 0 NOT NULL,
	"MONTH_NAME" VARCHAR2(3) default '0' NOT NULL,
	"MONTH_DESC" VARCHAR2(9) default '' NOT NULL,
	"QTR_NAME" VARCHAR2(4) default '' NOT NULL,
	"QTR_DESC" VARCHAR2(9) default '' NOT NULL
);

	ALTER TABLE "DIM_TIME_DELEGATE"
		ADD CONSTRAINT "DIM_TIME_DELEGATE_PK"
	PRIMARY KEY ("TIME_ID");


/* -----------------------------------------------------------------------
   DIM_TIME_COMPLETE
   ----------------------------------------------------------------------- */

DROP TABLE "DIM_TIME_COMPLETE" CASCADE CONSTRAINTS;


CREATE TABLE "DIM_TIME_COMPLETE"
(
	"TIME_ID" VARCHAR2(10) default '' NOT NULL,
	"MONTH_ID" NUMBER default 0 NOT NULL,
	"QTR_ID" NUMBER default 0 NOT NULL,
	"YEAR_ID" NUMBER default 0 NOT NULL,
	"MONTH_NAME" VARCHAR2(3) default '0' NOT NULL,
	"MONTH_DESC" VARCHAR2(9) default '' NOT NULL,
	"QTR_NAME" VARCHAR2(4) default '' NOT NULL,
	"QTR_DESC" VARCHAR2(9) default '' NOT NULL
);

	ALTER TABLE "DIM_TIME_COMPLETE"
		ADD CONSTRAINT "DIM_TIME_COMPLETE_PK"
	PRIMARY KEY ("TIME_ID");


/* -----------------------------------------------------------------------
   APP_HISTORY
   ----------------------------------------------------------------------- */

DROP TABLE "APP_HISTORY" CASCADE CONSTRAINTS;


CREATE TABLE "APP_HISTORY"
(
	"APP_UID" VARCHAR2(32) default '' NOT NULL,
	"DEL_INDEX" NUMBER default 0 NOT NULL,
	"PRO_UID" VARCHAR2(32) default '' NOT NULL,
	"TAS_UID" VARCHAR2(32) default '' NOT NULL,
	"DYN_UID" VARCHAR2(32) default '' NOT NULL,
	"USR_UID" VARCHAR2(32) default '' NOT NULL,
	"APP_STATUS" VARCHAR2(100) default '' NOT NULL,
	"HISTORY_DATE" DATE,
	"HISTORY_DATA" VARCHAR2(2000)  NOT NULL
);
CREATE INDEX "indexAppHistory" ON "APP_HISTORY" ("APP_UID","TAS_UID","USR_UID");
CREATE INDEX "indexAppHistory" ON "APP_HISTORY" ("APP_UID","TAS_UID","USR_UID");


/* -----------------------------------------------------------------------
   APP_FOLDER
   ----------------------------------------------------------------------- */

DROP TABLE "APP_FOLDER" CASCADE CONSTRAINTS;


CREATE TABLE "APP_FOLDER"
(
	"FOLDER_UID" VARCHAR2(32) default '' NOT NULL,
	"FOLDER_PARENT_UID" VARCHAR2(32) default '' NOT NULL,
	"FOLDER_NAME" VARCHAR2(2000)  NOT NULL,
	"FOLDER_CREATE_DATE" DATE  NOT NULL,
	"FOLDER_UPDATE_DATE" DATE  NOT NULL
);

	ALTER TABLE "APP_FOLDER"
		ADD CONSTRAINT "APP_FOLDER_PK"
	PRIMARY KEY ("FOLDER_UID");


/* -----------------------------------------------------------------------
   FIELD_CONDITION
   ----------------------------------------------------------------------- */

DROP TABLE "FIELD_CONDITION" CASCADE CONSTRAINTS;


CREATE TABLE "FIELD_CONDITION"
(
	"FCD_UID" VARCHAR2(32) default '' NOT NULL,
	"FCD_FUNCTION" VARCHAR2(50)  NOT NULL,
	"FCD_FIELDS" VARCHAR2(2000),
	"FCD_CONDITION" VARCHAR2(2000),
	"FCD_EVENTS" VARCHAR2(2000),
	"FCD_EVENT_OWNERS" VARCHAR2(2000),
	"FCD_STATUS" VARCHAR2(10),
	"FCD_DYN_UID" VARCHAR2(32)  NOT NULL
);

	ALTER TABLE "FIELD_CONDITION"
		ADD CONSTRAINT "FIELD_CONDITION_PK"
	PRIMARY KEY ("FCD_UID");


/* -----------------------------------------------------------------------
   LOG_CASES_SCHEDULER
   ----------------------------------------------------------------------- */

DROP TABLE "LOG_CASES_SCHEDULER" CASCADE CONSTRAINTS;


CREATE TABLE "LOG_CASES_SCHEDULER"
(
	"LOG_CASE_UID" VARCHAR2(32) default '' NOT NULL,
	"PRO_UID" VARCHAR2(32) default '' NOT NULL,
	"TAS_UID" VARCHAR2(32) default '' NOT NULL,
	"USR_NAME" VARCHAR2(32) default '' NOT NULL,
	"EXEC_DATE" DATE  NOT NULL,
	"EXEC_HOUR" VARCHAR2(32) default '12:00' NOT NULL,
	"RESULT" VARCHAR2(32) default 'SUCCESS' NOT NULL,
	"SCH_UID" VARCHAR2(32) default 'OPEN' NOT NULL,
	"WS_CREATE_CASE_STATUS" VARCHAR2(2000)  NOT NULL,
	"WS_ROUTE_CASE_STATUS" VARCHAR2(2000)  NOT NULL
);

	ALTER TABLE "LOG_CASES_SCHEDULER"
		ADD CONSTRAINT "LOG_CASES_SCHEDULER_PK"
	PRIMARY KEY ("LOG_CASE_UID");


/* -----------------------------------------------------------------------
   CASE_SCHEDULER
   ----------------------------------------------------------------------- */

DROP TABLE "CASE_SCHEDULER" CASCADE CONSTRAINTS;


CREATE TABLE "CASE_SCHEDULER"
(
	"SCH_UID" VARCHAR2(32)  NOT NULL,
	"SCH_DEL_USER_NAME" VARCHAR2(100)  NOT NULL,
	"SCH_DEL_USER_PASS" VARCHAR2(100)  NOT NULL,
	"SCH_DEL_USER_UID" VARCHAR2(100)  NOT NULL,
	"SCH_NAME" VARCHAR2(100)  NOT NULL,
	"PRO_UID" VARCHAR2(32) default '' NOT NULL,
	"TAS_UID" VARCHAR2(32) default '' NOT NULL,
	"SCH_TIME_NEXT_RUN" DATE  NOT NULL,
	"SCH_LAST_RUN_TIME" DATE,
	"SCH_STATE" VARCHAR2(15) default 'ACTIVE' NOT NULL,
	"SCH_LAST_STATE" VARCHAR2(60) default '' NOT NULL,
	"USR_UID" VARCHAR2(32) default '' NOT NULL,
	"SCH_OPTION" NUMBER(3,0) default 0 NOT NULL,
	"SCH_START_TIME" DATE  NOT NULL,
	"SCH_START_DATE" DATE  NOT NULL,
	"SCH_DAYS_PERFORM_TASK" CHAR(5) default '' NOT NULL,
	"SCH_EVERY_DAYS" NUMBER(3,0) default 0,
	"SCH_WEEK_DAYS" CHAR(14) default '0|0|0|0|0|0|0' NOT NULL,
	"SCH_START_DAY" CHAR(6) default '' NOT NULL,
	"SCH_MONTHS" CHAR(24) default '0|0|0|0|0|0|0|0|0|0|0|0' NOT NULL,
	"SCH_END_DATE" DATE,
	"SCH_REPEAT_EVERY" VARCHAR2(15) default '' NOT NULL,
	"SCH_REPEAT_UNTIL" VARCHAR2(15) default '' NOT NULL,
	"SCH_REPEAT_STOP_IF_RUNNING" NUMBER(3,0) default 0,
	"CASE_SH_PLUGIN_UID" VARCHAR2(100)
);

	ALTER TABLE "CASE_SCHEDULER"
		ADD CONSTRAINT "CASE_SCHEDULER_PK"
	PRIMARY KEY ("SCH_UID");


/* -----------------------------------------------------------------------
   CALENDAR_DEFINITION
   ----------------------------------------------------------------------- */

DROP TABLE "CALENDAR_DEFINITION" CASCADE CONSTRAINTS;


CREATE TABLE "CALENDAR_DEFINITION"
(
	"CALENDAR_UID" VARCHAR2(32) default '' NOT NULL,
	"CALENDAR_NAME" VARCHAR2(100) default '' NOT NULL,
	"CALENDAR_CREATE_DATE" DATE  NOT NULL,
	"CALENDAR_UPDATE_DATE" DATE,
	"CALENDAR_WORK_DAYS" VARCHAR2(100) default '' NOT NULL,
	"CALENDAR_DESCRIPTION" VARCHAR2(2000)  NOT NULL,
	"CALENDAR_STATUS" VARCHAR2(8) default 'ACTIVE' NOT NULL
);

	ALTER TABLE "CALENDAR_DEFINITION"
		ADD CONSTRAINT "CALENDAR_DEFINITION_PK"
	PRIMARY KEY ("CALENDAR_UID");


/* -----------------------------------------------------------------------
   CALENDAR_BUSINESS_HOURS
   ----------------------------------------------------------------------- */

DROP TABLE "CALENDAR_BUSINESS_HOURS" CASCADE CONSTRAINTS;


CREATE TABLE "CALENDAR_BUSINESS_HOURS"
(
	"CALENDAR_UID" VARCHAR2(32) default '' NOT NULL,
	"CALENDAR_BUSINESS_DAY" VARCHAR2(10) default '' NOT NULL,
	"CALENDAR_BUSINESS_START" VARCHAR2(10) default '' NOT NULL,
	"CALENDAR_BUSINESS_END" VARCHAR2(10) default '' NOT NULL
);

	ALTER TABLE "CALENDAR_BUSINESS_HOURS"
		ADD CONSTRAINT "CALENDAR_BUSINESS_HOURS_PK"
	PRIMARY KEY ("CALENDAR_UID","CALENDAR_BUSINESS_DAY","CALENDAR_BUSINESS_START","CALENDAR_BUSINESS_END");


/* -----------------------------------------------------------------------
   CALENDAR_HOLIDAYS
   ----------------------------------------------------------------------- */

DROP TABLE "CALENDAR_HOLIDAYS" CASCADE CONSTRAINTS;


CREATE TABLE "CALENDAR_HOLIDAYS"
(
	"CALENDAR_UID" VARCHAR2(32) default '' NOT NULL,
	"CALENDAR_HOLIDAY_NAME" VARCHAR2(100) default '' NOT NULL,
	"CALENDAR_HOLIDAY_START" DATE  NOT NULL,
	"CALENDAR_HOLIDAY_END" DATE  NOT NULL
);

	ALTER TABLE "CALENDAR_HOLIDAYS"
		ADD CONSTRAINT "CALENDAR_HOLIDAYS_PK"
	PRIMARY KEY ("CALENDAR_UID","CALENDAR_HOLIDAY_NAME");


/* -----------------------------------------------------------------------
   CALENDAR_ASSIGNMENTS
   ----------------------------------------------------------------------- */

DROP TABLE "CALENDAR_ASSIGNMENTS" CASCADE CONSTRAINTS;


CREATE TABLE "CALENDAR_ASSIGNMENTS"
(
	"OBJECT_UID" VARCHAR2(32) default '' NOT NULL,
	"CALENDAR_UID" VARCHAR2(32) default '' NOT NULL,
	"OBJECT_TYPE" VARCHAR2(100) default '' NOT NULL
);

	ALTER TABLE "CALENDAR_ASSIGNMENTS"
		ADD CONSTRAINT "CALENDAR_ASSIGNMENTS_PK"
	PRIMARY KEY ("OBJECT_UID");


/* -----------------------------------------------------------------------
   PROCESS_CATEGORY
   ----------------------------------------------------------------------- */

DROP TABLE "PROCESS_CATEGORY" CASCADE CONSTRAINTS;


CREATE TABLE "PROCESS_CATEGORY"
(
	"CATEGORY_UID" VARCHAR2(32) default '' NOT NULL,
	"CATEGORY_PARENT" VARCHAR2(32) default '0' NOT NULL,
	"CATEGORY_NAME" VARCHAR2(100) default '' NOT NULL,
	"CATEGORY_ICON" VARCHAR2(100) default ''
);

	ALTER TABLE "PROCESS_CATEGORY"
		ADD CONSTRAINT "PROCESS_CATEGORY_PK"
	PRIMARY KEY ("CATEGORY_UID");


/* -----------------------------------------------------------------------
   APP_NOTES
   ----------------------------------------------------------------------- */

DROP TABLE "APP_NOTES" CASCADE CONSTRAINTS;


CREATE TABLE "APP_NOTES"
(
	"APP_UID" VARCHAR2(32) default '' NOT NULL,
	"USR_UID" VARCHAR2(32) default '' NOT NULL,
	"NOTE_DATE" DATE  NOT NULL,
	"NOTE_CONTENT" VARCHAR2(2000)  NOT NULL,
	"NOTE_TYPE" VARCHAR2(32) default 'USER' NOT NULL,
	"NOTE_AVAILABILITY" VARCHAR2(32) default 'PUBLIC' NOT NULL,
	"NOTE_ORIGIN_OBJ" VARCHAR2(32) default '',
	"NOTE_AFFECTED_OBJ1" VARCHAR2(32) default '',
	"NOTE_AFFECTED_OBJ2" VARCHAR2(32) default '' NOT NULL,
	"NOTE_RECIPIENTS" VARCHAR2(2000)
);
CREATE INDEX "indexAppNotesDate" ON "APP_NOTES" ("APP_UID","NOTE_DATE");
CREATE INDEX "indexAppNotesUser" ON "APP_NOTES" ("APP_UID","USR_UID");
CREATE INDEX "indexAppNotesDate" ON "APP_NOTES" ("APP_UID","NOTE_DATE");
CREATE INDEX "indexAppNotesUser" ON "APP_NOTES" ("APP_UID","USR_UID");


/* -----------------------------------------------------------------------
   DASHLET
   ----------------------------------------------------------------------- */

DROP TABLE "DASHLET" CASCADE CONSTRAINTS;


CREATE TABLE "DASHLET"
(
	"DAS_UID" VARCHAR2(32) default '' NOT NULL,
	"DAS_CLASS" VARCHAR2(50) default '' NOT NULL,
	"DAS_TITLE" VARCHAR2(255) default '' NOT NULL,
	"DAS_DESCRIPTION" VARCHAR2(2000),
	"DAS_VERSION" VARCHAR2(10) default '1.0' NOT NULL,
	"DAS_CREATE_DATE" DATE  NOT NULL,
	"DAS_UPDATE_DATE" DATE,
	"DAS_STATUS" NUMBER(3,0) default 1 NOT NULL
);

	ALTER TABLE "DASHLET"
		ADD CONSTRAINT "DASHLET_PK"
	PRIMARY KEY ("DAS_UID");


/* -----------------------------------------------------------------------
   DASHLET_INSTANCE
   ----------------------------------------------------------------------- */

DROP TABLE "DASHLET_INSTANCE" CASCADE CONSTRAINTS;


CREATE TABLE "DASHLET_INSTANCE"
(
	"DAS_INS_UID" VARCHAR2(32) default '' NOT NULL,
	"DAS_UID" VARCHAR2(32) default '' NOT NULL,
	"DAS_INS_OWNER_TYPE" VARCHAR2(20) default '' NOT NULL,
	"DAS_INS_OWNER_UID" VARCHAR2(32) default '',
	"DAS_INS_ADDITIONAL_PROPERTIES" VARCHAR2(2000),
	"DAS_INS_CREATE_DATE" DATE  NOT NULL,
	"DAS_INS_UPDATE_DATE" DATE,
	"DAS_INS_STATUS" NUMBER(3,0) default 1 NOT NULL
);

	ALTER TABLE "DASHLET_INSTANCE"
		ADD CONSTRAINT "DASHLET_INSTANCE_PK"
	PRIMARY KEY ("DAS_INS_UID");


/* -----------------------------------------------------------------------
   APP_SOLR_QUEUE
   ----------------------------------------------------------------------- */

DROP TABLE "APP_SOLR_QUEUE" CASCADE CONSTRAINTS;


CREATE TABLE "APP_SOLR_QUEUE"
(
	"APP_UID" VARCHAR2(32) default '' NOT NULL,
	"APP_UPDATED" NUMBER(3,0) default 1 NOT NULL
);

	ALTER TABLE "APP_SOLR_QUEUE"
		ADD CONSTRAINT "APP_SOLR_QUEUE_PK"
	PRIMARY KEY ("APP_UID");
