

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
	"APP_CREATE_DATE" DATE default '0000-00-00 00:00:00' NOT NULL,
	"APP_INIT_DATE" DATE default '0000-00-00 00:00:00' NOT NULL,
	"APP_FINISH_DATE" DATE default '0000-00-00 00:00:00' NOT NULL,
	"APP_UPDATE_DATE" DATE default '0000-00-00 00:00:00' NOT NULL,
	"APP_DATA" VARCHAR2(2000)  NOT NULL
);

	ALTER TABLE "APPLICATION"
		ADD CONSTRAINT "APPLICATION_PK"
	PRIMARY KEY ("APP_UID");
CREATE INDEX "indexApp" ON "APPLICATION" ("PRO_UID","APP_UID");
CREATE INDEX "indexApp" ON "APPLICATION" ("PRO_UID","APP_UID");


/* -----------------------------------------------------------------------
   APP_DELEGATION
   ----------------------------------------------------------------------- */

DROP TABLE "APP_DELEGATION" CASCADE CONSTRAINTS;


CREATE TABLE "APP_DELEGATION"
(
	"APP_UID" VARCHAR2(32) default '' NOT NULL,
	"DEL_INDEX" NUMBER default 0 NOT NULL,
	"DEL_PREVIOUS" NUMBER default 0 NOT NULL,
	"PRO_UID" VARCHAR2(32) default '' NOT NULL,
	"TAS_UID" VARCHAR2(32) default '' NOT NULL,
	"USR_UID" VARCHAR2(32) default '' NOT NULL,
	"DEL_TYPE" VARCHAR2(32) default 'NORMAL' NOT NULL,
	"DEL_THREAD" NUMBER default 0 NOT NULL,
	"DEL_THREAD_STATUS" VARCHAR2(32) default 'OPEN' NOT NULL,
	"DEL_PRIORITY" VARCHAR2(32) default '0' NOT NULL,
	"DEL_DELEGATE_DATE" DATE default '0000-00-00 00:00:00' NOT NULL,
	"DEL_INIT_DATE" DATE  NOT NULL,
	"DEL_TASK_DUE_DATE" DATE default '' NOT NULL,
	"DEL_FINISH_DATE" DATE
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
	"APP_UID" VARCHAR2(32) default '' NOT NULL,
	"DEL_INDEX" NUMBER default 0 NOT NULL,
	"DOC_UID" VARCHAR2(32) default '' NOT NULL,
	"USR_UID" VARCHAR2(32) default '' NOT NULL,
	"APP_DOC_TYPE" VARCHAR2(32) default '' NOT NULL,
	"APP_DOC_CREATE_DATE" DATE default '0000-00-00 00:00:00' NOT NULL
);

	ALTER TABLE "APP_DOCUMENT"
		ADD CONSTRAINT "APP_DOCUMENT_PK"
	PRIMARY KEY ("APP_DOC_UID");


    CREATE INDEX "indexAppDocument" ON "APP_DOCUMENT" ("FOLDER_UID","APP_DOC_UID");

/* -----------------------------------------------------------------------
   APP_MESSAGE
   ----------------------------------------------------------------------- */

DROP TABLE "APP_MESSAGE" CASCADE CONSTRAINTS;


CREATE TABLE "APP_MESSAGE"
(
	"APP_MSG_UID" VARCHAR2(32) default '' NOT NULL,
	"MSG_UID" VARCHAR2(32),
	"APP_UID" VARCHAR2(32) default '' NOT NULL,
	"DEL_INDEX" NUMBER default 0 NOT NULL,
	"APP_MSG_TYPE" VARCHAR2(100) default 'CUSTOM_MESSAGE' NOT NULL,
	"APP_MSG_SUBJECT" VARCHAR2(150) default '' NOT NULL,
	"APP_MSG_FROM" VARCHAR2(100) default '' NOT NULL,
	"APP_MSG_TO" VARCHAR2(2000)  NOT NULL,
	"APP_MSG_BODY" VARCHAR2(2000)  NOT NULL,
	"APP_MSG_DATE" DATE default '0000-00-00 00:00:00' NOT NULL,
	"APP_MSG_CC" VARCHAR2(2000),
	"APP_MSG_BCC" VARCHAR2(2000),
	"APP_MSG_ATTACH" VARCHAR2(2000)
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
	"DEP_STATUS" CHAR(1) default 'A' NOT NULL,
	"DEP_TYPE" VARCHAR2(5) default 'INTER' NOT NULL,
	"DEP_REF_CODE" VARCHAR2(10) default '' NOT NULL
);

	ALTER TABLE "DEPARTMENT"
		ADD CONSTRAINT "DEPARTMENT_PK"
	PRIMARY KEY ("DEP_UID");
CREATE INDEX "DEP_BYPARENT" ON "DEPARTMENT" ("DEP_PARENT");
CREATE INDEX "DEP_BYPARENT" ON "DEPARTMENT" ("DEP_PARENT");


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
	"GRP_STATUS" CHAR(8) default 'ACTIVE' NOT NULL
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
	"INP_DOC_PUBLISHED" VARCHAR2(20) default 'PRIVATE' NOT NULL
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
   KT_APPLICATION
   ----------------------------------------------------------------------- */

DROP TABLE "KT_APPLICATION" CASCADE CONSTRAINTS;


CREATE TABLE "KT_APPLICATION"
(
	"APP_UID" VARCHAR2(32) default '' NOT NULL,
	"KT_FOLDER_ID" NUMBER default 0 NOT NULL,
	"KT_PARENT_ID" NUMBER default 0 NOT NULL,
	"KT_FOLDER_NAME" VARCHAR2(100) default '' NOT NULL,
	"KT_FULL_PATH" VARCHAR2(255) default '' NOT NULL,
	"KT_CREATE_USER" VARCHAR2(32) default '' NOT NULL,
	"KT_CREATE_DATE" DATE default '0000-00-00 00:00:00' NOT NULL,
	"KT_UPDATE_DATE" DATE default '0000-00-00 00:00:00' NOT NULL
);

	ALTER TABLE "KT_APPLICATION"
		ADD CONSTRAINT "KT_APPLICATION_PK"
	PRIMARY KEY ("APP_UID");
CREATE INDEX "indexApp" ON "KT_APPLICATION" ("KT_FOLDER_ID");
CREATE INDEX "indexApp" ON "KT_APPLICATION" ("KT_FOLDER_ID");


/* -----------------------------------------------------------------------
   KT_PROCESS
   ----------------------------------------------------------------------- */

DROP TABLE "KT_PROCESS" CASCADE CONSTRAINTS;


CREATE TABLE "KT_PROCESS"
(
	"PRO_UID" VARCHAR2(32) default '' NOT NULL,
	"KT_FOLDER_ID" NUMBER default 0 NOT NULL,
	"KT_PARENT_ID" NUMBER default 0 NOT NULL,
	"KT_FOLDER_NAME" VARCHAR2(100) default '' NOT NULL,
	"KT_FULL_PATH" VARCHAR2(255) default '' NOT NULL,
	"KT_CREATE_USER" VARCHAR2(32) default '' NOT NULL,
	"KT_CREATE_DATE" DATE default '0000-00-00 00:00:00' NOT NULL,
	"KT_UPDATE_DATE" DATE default '0000-00-00 00:00:00' NOT NULL
);

	ALTER TABLE "KT_PROCESS"
		ADD CONSTRAINT "KT_PROCESS_PK"
	PRIMARY KEY ("PRO_UID");
CREATE INDEX "indexApp" ON "KT_PROCESS" ("KT_FOLDER_ID");
CREATE INDEX "indexApp" ON "KT_PROCESS" ("KT_FOLDER_ID");


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
	"PRO_UID" VARCHAR2(32) default '' NOT NULL
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
	"PRO_TRI_DELETED" VARCHAR(32) default '' NOT NULL,
	"PRO_TRI_CANCELED" VARCHAR(32) default '' NOT NULL,
	"PRO_TRI_PAUSED" VARCHAR(32) default '' NOT NULL,
	"PRO_TRI_REASSIGNED" VARCHAR(32) default '' NOT NULL,
	"PRO_SHOW_DELEGATE" NUMBER(3,0) default 1 NOT NULL,
	"PRO_SHOW_DYNAFORM" NUMBER(3,0) default 0 NOT NULL,
	"PRO_CATEGORY" VARCHAR2(48) default '' NOT NULL,
	"PRO_SUB_CATEGORY" VARCHAR2(48) default '' NOT NULL,
	"PRO_INDUSTRY" NUMBER default 1 NOT NULL,
	"PRO_UPDATE_DATE" DATE default '',
	"PRO_CREATE_DATE" DATE default '' NOT NULL,
	"PRO_CREATE_USER" VARCHAR2(32) default '' NOT NULL,
	"PRO_HEIGHT" NUMBER default 5000 NOT NULL,
	"PRO_WIDTH" NUMBER default 10000 NOT NULL,
	"PRO_TITLE_X" NUMBER default 0 NOT NULL,
	"PRO_TITLE_Y" NUMBER default 6 NOT NULL
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
	"ROU_CONDITION" VARCHAR2(255) default '' NOT NULL,
	"ROU_TO_LAST_USER" VARCHAR2(20) default 'FALSE' NOT NULL,
	"ROU_OPTIONAL" VARCHAR2(20) default 'FALSE' NOT NULL,
	"ROU_SEND_EMAIL" VARCHAR2(20) default 'TRUE' NOT NULL,
	"ROU_SOURCEANCHOR" NUMBER default 1,
	"ROU_TARGETANCHOR" NUMBER default 0
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
	"STEP_POSITION" NUMBER default 0 NOT NULL
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
	"SWI_Y" NUMBER default 0 NOT NULL
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
	"TAS_COLOR" VARCHAR2(32) default '' NOT NULL
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
	"TRN_VALUE" VARCHAR2(200) default '' NOT NULL
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
	"TRI_WEBBOT" VARCHAR2(2000)  NOT NULL
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
	"USR_DUE_DATE" DATE default '0000-00-00' NOT NULL,
	"USR_CREATE_DATE" DATE default '0000-00-00 00:00:00' NOT NULL,
	"USR_UPDATE_DATE" DATE default '0000-00-00 00:00:00' NOT NULL,
	"USR_STATUS" NUMBER default 1 NOT NULL,
	"USR_COUNTRY" VARCHAR2(3) default '' NOT NULL,
	"USR_CITY" VARCHAR2(3) default '' NOT NULL,
	"USR_LOCATION" VARCHAR2(3) default '' NOT NULL,
	"USR_ADDRESS" VARCHAR2(255) default '' NOT NULL,
	"USR_PHONE" VARCHAR2(24) default '' NOT NULL,
	"USR_FAX" VARCHAR2(24) default '' NOT NULL,
	"USR_CELLULAR" VARCHAR2(24) default '' NOT NULL,
	"USR_ZIP_CODE" VARCHAR2(16) default '' NOT NULL,
	"USR_DEPARTMENT" NUMBER default 0 NOT NULL,
	"USR_POSITION" VARCHAR2(100) default '' NOT NULL,
	"USR_RESUME" VARCHAR2(100) default '' NOT NULL,
	"USR_BIRTHDAY" DATE default '0000-00-00' NOT NULL,
	"USR_ROLE" VARCHAR2(32) default 'PROCESSMAKER_ADMIN'
);

	ALTER TABLE "USERS"
		ADD CONSTRAINT "USERS_PK"
	PRIMARY KEY ("USR_UID");
