
/* ---------------------------------------------------------------------- */
/* APPLICATION											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'APPLICATION')
BEGIN
	 DECLARE @reftable_1 nvarchar(60), @constraintname_1 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'APPLICATION'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_1, @constraintname_1
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_1+' drop constraint '+@constraintname_1)
	   FETCH NEXT from refcursor into @reftable_1, @constraintname_1
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [APPLICATION]
END


CREATE TABLE [APPLICATION]
(
	[APP_UID] VARCHAR(32) default '' NOT NULL,
	[APP_NUMBER] INT default 0 NOT NULL,
	[APP_PARENT] VARCHAR(32) default '0' NOT NULL,
	[APP_STATUS] VARCHAR(100) default '' NOT NULL,
	[PRO_UID] VARCHAR(32) default '' NOT NULL,
	[APP_PROC_STATUS] VARCHAR(100) default '' NOT NULL,
	[APP_PROC_CODE] VARCHAR(100) default '' NOT NULL,
	[APP_PARALLEL] VARCHAR(32) default 'NO' NOT NULL,
	[APP_INIT_USER] VARCHAR(32) default '' NOT NULL,
	[APP_CUR_USER] VARCHAR(32) default '' NOT NULL,
	[APP_CREATE_DATE] CHAR(19)  NOT NULL,
	[APP_INIT_DATE] CHAR(19)  NOT NULL,
	[APP_FINISH_DATE] CHAR(19)  NULL,
	[APP_UPDATE_DATE] CHAR(19)  NOT NULL,
	[APP_DATA] NVARCHAR(MAX)  NOT NULL,
	[APP_PIN] VARCHAR(32) default '' NOT NULL,
	CONSTRAINT APPLICATION_PK PRIMARY KEY ([APP_UID])
);

CREATE INDEX [indexApp] ON [APPLICATION] ([PRO_UID],[APP_STATUS],[APP_UID]);

CREATE INDEX [indexAppNumber] ON [APPLICATION] ([APP_NUMBER]);

CREATE INDEX [indexAppStatus] ON [APPLICATION] ([APP_STATUS]);

/* ---------------------------------------------------------------------- */
/* APP_DELEGATION											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'APP_DELEGATION')
BEGIN
	 DECLARE @reftable_2 nvarchar(60), @constraintname_2 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'APP_DELEGATION'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_2, @constraintname_2
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_2+' drop constraint '+@constraintname_2)
	   FETCH NEXT from refcursor into @reftable_2, @constraintname_2
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [APP_DELEGATION]
END


CREATE TABLE [APP_DELEGATION]
(
	[APP_UID] VARCHAR(32) default '' NOT NULL,
	[DEL_INDEX] INT default 0 NOT NULL,
	[DEL_PREVIOUS] INT default 0 NOT NULL,
	[PRO_UID] VARCHAR(32) default '' NOT NULL,
	[TAS_UID] VARCHAR(32) default '' NOT NULL,
	[USR_UID] VARCHAR(32) default '' NOT NULL,
	[DEL_TYPE] VARCHAR(32) default 'NORMAL' NOT NULL,
	[DEL_THREAD] INT default 0 NOT NULL,
	[DEL_THREAD_STATUS] VARCHAR(32) default 'OPEN' NOT NULL,
	[DEL_PRIORITY] VARCHAR(32) default '3' NOT NULL,
	[DEL_DELEGATE_DATE] CHAR(19)  NOT NULL,
	[DEL_INIT_DATE] CHAR(19)  NULL,
	[DEL_TASK_DUE_DATE] CHAR(19)  NULL,
	[DEL_FINISH_DATE] CHAR(19)  NULL,
	[DEL_DURATION] FLOAT default 0 NULL,
	[DEL_QUEUE_DURATION] FLOAT default 0 NULL,
	[DEL_DELAY_DURATION] FLOAT default 0 NULL,
	[DEL_STARTED] TINYINT default 0 NULL,
	[DEL_FINISHED] TINYINT default 0 NULL,
	[DEL_DELAYED] TINYINT default 0 NULL,
	[DEL_DATA] NVARCHAR(MAX)  NOT NULL,
	[APP_OVERDUE_PERCENTAGE] FLOAT default 0 NOT NULL,
	CONSTRAINT APP_DELEGATION_PK PRIMARY KEY ([APP_UID],[DEL_INDEX])
);

/* ---------------------------------------------------------------------- */
/* APP_DOCUMENT											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'APP_DOCUMENT')
BEGIN
	 DECLARE @reftable_3 nvarchar(60), @constraintname_3 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'APP_DOCUMENT'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_3, @constraintname_3
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_3+' drop constraint '+@constraintname_3)
	   FETCH NEXT from refcursor into @reftable_3, @constraintname_3
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [APP_DOCUMENT]
END


CREATE TABLE [APP_DOCUMENT]
(
	[APP_DOC_UID] VARCHAR(32) default '' NOT NULL,
	[DOC_VERSION] INT default 1 NOT NULL,
	[APP_UID] VARCHAR(32) default '' NOT NULL,
	[DEL_INDEX] INT default 0 NOT NULL,
	[DOC_UID] VARCHAR(32) default '' NOT NULL,
	[USR_UID] VARCHAR(32) default '' NOT NULL,
	[APP_DOC_TYPE] VARCHAR(32) default '' NOT NULL,
	[APP_DOC_CREATE_DATE] CHAR(19)  NOT NULL,
	[APP_DOC_INDEX] INT  NOT NULL,
	[FOLDER_UID] VARCHAR(32) default '' NULL,
	[APP_DOC_PLUGIN] VARCHAR(150) default '' NULL,
	[APP_DOC_TAGS] NVARCHAR(MAX)  NULL,
	[APP_DOC_STATUS] VARCHAR(32) default 'ACTIVE' NOT NULL,
	[APP_DOC_STATUS_DATE] CHAR(19)  NULL,
	CONSTRAINT APP_DOCUMENT_PK PRIMARY KEY ([APP_DOC_UID],[DOC_VERSION])
);


CREATE INDEX [indexAppDocument] ON [APP_DOCUMENT] ([FOLDER_UID],[APP_DOC_UID]);

/* ---------------------------------------------------------------------- */
/* APP_MESSAGE											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'APP_MESSAGE')
BEGIN
	 DECLARE @reftable_4 nvarchar(60), @constraintname_4 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'APP_MESSAGE'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_4, @constraintname_4
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_4+' drop constraint '+@constraintname_4)
	   FETCH NEXT from refcursor into @reftable_4, @constraintname_4
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [APP_MESSAGE]
END


CREATE TABLE [APP_MESSAGE]
(
	[APP_MSG_UID] VARCHAR(32)  NOT NULL,
	[MSG_UID] VARCHAR(32)  NULL,
	[APP_UID] VARCHAR(32) default '' NOT NULL,
	[DEL_INDEX] INT default 0 NOT NULL,
	[APP_MSG_TYPE] VARCHAR(100) default '' NOT NULL,
	[APP_MSG_SUBJECT] VARCHAR(150) default '' NOT NULL,
	[APP_MSG_FROM] VARCHAR(100) default '' NOT NULL,
	[APP_MSG_TO] NVARCHAR(MAX)  NOT NULL,
	[APP_MSG_BODY] NVARCHAR(MAX)  NOT NULL,
	[APP_MSG_DATE] CHAR(19)  NOT NULL,
	[APP_MSG_CC] NVARCHAR(MAX)  NULL,
	[APP_MSG_BCC] NVARCHAR(MAX)  NULL,
	[APP_MSG_TEMPLATE] NVARCHAR(MAX)  NULL,
	[APP_MSG_STATUS] VARCHAR(20)  NULL,
	[APP_MSG_ATTACH] NVARCHAR(MAX)  NULL,
	[APP_MSG_SEND_DATE] CHAR(19)  NOT NULL,
	CONSTRAINT APP_MESSAGE_PK PRIMARY KEY ([APP_MSG_UID])
);

/* ---------------------------------------------------------------------- */
/* APP_OWNER											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'APP_OWNER')
BEGIN
	 DECLARE @reftable_5 nvarchar(60), @constraintname_5 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'APP_OWNER'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_5, @constraintname_5
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_5+' drop constraint '+@constraintname_5)
	   FETCH NEXT from refcursor into @reftable_5, @constraintname_5
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [APP_OWNER]
END


CREATE TABLE [APP_OWNER]
(
	[APP_UID] VARCHAR(32) default '' NOT NULL,
	[OWN_UID] VARCHAR(32) default '' NOT NULL,
	[USR_UID] VARCHAR(32) default '' NOT NULL,
	CONSTRAINT APP_OWNER_PK PRIMARY KEY ([APP_UID],[OWN_UID],[USR_UID])
);

/* ---------------------------------------------------------------------- */
/* CONFIGURATION											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'CONFIGURATION')
BEGIN
	 DECLARE @reftable_6 nvarchar(60), @constraintname_6 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'CONFIGURATION'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_6, @constraintname_6
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_6+' drop constraint '+@constraintname_6)
	   FETCH NEXT from refcursor into @reftable_6, @constraintname_6
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [CONFIGURATION]
END


CREATE TABLE [CONFIGURATION]
(
	[CFG_UID] VARCHAR(32) default '' NOT NULL,
	[OBJ_UID] VARCHAR(128) default '' NOT NULL,
	[CFG_VALUE] NVARCHAR(MAX)  NOT NULL,
	[PRO_UID] VARCHAR(32) default '' NOT NULL,
	[USR_UID] VARCHAR(32) default '' NOT NULL,
	[APP_UID] VARCHAR(32) default '' NOT NULL,
	CONSTRAINT CONFIGURATION_PK PRIMARY KEY ([CFG_UID],[OBJ_UID],[PRO_UID],[USR_UID],[APP_UID])
);

/* ---------------------------------------------------------------------- */
/* CONTENT											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'CONTENT')
BEGIN
	 DECLARE @reftable_7 nvarchar(60), @constraintname_7 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'CONTENT'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_7, @constraintname_7
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_7+' drop constraint '+@constraintname_7)
	   FETCH NEXT from refcursor into @reftable_7, @constraintname_7
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [CONTENT]
END


CREATE TABLE [CONTENT]
(
	[CON_CATEGORY] VARCHAR(30) default '' NOT NULL,
	[CON_PARENT] VARCHAR(32) default '' NOT NULL,
	[CON_ID] VARCHAR(100) default '' NOT NULL,
	[CON_LANG] VARCHAR(10) default '' NOT NULL,
	[CON_VALUE] NVARCHAR(MAX)  NOT NULL,
	CONSTRAINT CONTENT_PK PRIMARY KEY ([CON_CATEGORY],[CON_PARENT],[CON_ID],[CON_LANG])
);

CREATE INDEX [indexUid] ON [CONTENT] ([CON_ID],[CON_CATEGORY],[CON_LANG]);

/* ---------------------------------------------------------------------- */
/* DEPARTMENT											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'DEPARTMENT')
BEGIN
	 DECLARE @reftable_8 nvarchar(60), @constraintname_8 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'DEPARTMENT'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_8, @constraintname_8
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_8+' drop constraint '+@constraintname_8)
	   FETCH NEXT from refcursor into @reftable_8, @constraintname_8
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [DEPARTMENT]
END


CREATE TABLE [DEPARTMENT]
(
	[DEP_UID] VARCHAR(32) default '' NOT NULL,
	[DEP_PARENT] VARCHAR(32) default '' NOT NULL,
	[DEP_MANAGER] VARCHAR(32) default '' NOT NULL,
	[DEP_LOCATION] INT default 0 NOT NULL,
	[DEP_STATUS] VARCHAR(10) default 'ACTIVE' NOT NULL,
	[DEP_REF_CODE] VARCHAR(50) default '' NOT NULL,
	[DEP_LDAP_DN] VARCHAR(255) default '' NOT NULL,
	CONSTRAINT DEPARTMENT_PK PRIMARY KEY ([DEP_UID])
);

CREATE INDEX [DEP_BYPARENT] ON [DEPARTMENT] ([DEP_PARENT]);

CREATE INDEX [BY_DEP_LDAP_DN] ON [DEPARTMENT] ([DEP_LDAP_DN]);

/* ---------------------------------------------------------------------- */
/* DYNAFORM											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'DYNAFORM')
BEGIN
	 DECLARE @reftable_9 nvarchar(60), @constraintname_9 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'DYNAFORM'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_9, @constraintname_9
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_9+' drop constraint '+@constraintname_9)
	   FETCH NEXT from refcursor into @reftable_9, @constraintname_9
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [DYNAFORM]
END


CREATE TABLE [DYNAFORM]
(
	[DYN_UID] VARCHAR(32) default '' NOT NULL,
	[PRO_UID] VARCHAR(32) default '0' NOT NULL,
	[DYN_TYPE] VARCHAR(20) default 'xmlform' NOT NULL,
	[DYN_FILENAME] VARCHAR(100) default '' NOT NULL,
	CONSTRAINT DYNAFORM_PK PRIMARY KEY ([DYN_UID])
);

/* ---------------------------------------------------------------------- */
/* GROUPWF											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'GROUPWF')
BEGIN
	 DECLARE @reftable_10 nvarchar(60), @constraintname_10 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'GROUPWF'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_10, @constraintname_10
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_10+' drop constraint '+@constraintname_10)
	   FETCH NEXT from refcursor into @reftable_10, @constraintname_10
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [GROUPWF]
END


CREATE TABLE [GROUPWF]
(
	[GRP_UID] VARCHAR(32) default '' NOT NULL,
	[GRP_STATUS] CHAR(8) default 'ACTIVE' NOT NULL,
	[GRP_LDAP_DN] VARCHAR(255) default '' NOT NULL,
	[GRP_UX] VARCHAR(128) default 'NORMAL' NULL,
	CONSTRAINT GROUPWF_PK PRIMARY KEY ([GRP_UID])
);

/* ---------------------------------------------------------------------- */
/* GROUP_USER											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'GROUP_USER')
BEGIN
	 DECLARE @reftable_11 nvarchar(60), @constraintname_11 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'GROUP_USER'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_11, @constraintname_11
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_11+' drop constraint '+@constraintname_11)
	   FETCH NEXT from refcursor into @reftable_11, @constraintname_11
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [GROUP_USER]
END


CREATE TABLE [GROUP_USER]
(
	[GRP_UID] VARCHAR(32) default '0' NOT NULL,
	[USR_UID] VARCHAR(32) default '0' NOT NULL,
	CONSTRAINT GROUP_USER_PK PRIMARY KEY ([GRP_UID],[USR_UID])
);

/* ---------------------------------------------------------------------- */
/* HOLIDAY											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'HOLIDAY')
BEGIN
	 DECLARE @reftable_12 nvarchar(60), @constraintname_12 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'HOLIDAY'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_12, @constraintname_12
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_12+' drop constraint '+@constraintname_12)
	   FETCH NEXT from refcursor into @reftable_12, @constraintname_12
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [HOLIDAY]
END


CREATE TABLE [HOLIDAY]
(
	[HLD_UID] INT  NOT NULL IDENTITY,
	[HLD_DATE] VARCHAR(10) default '0000-00-00' NOT NULL,
	[HLD_DESCRIPTION] VARCHAR(200) default '' NOT NULL,
	CONSTRAINT HOLIDAY_PK PRIMARY KEY ([HLD_UID])
);

/* ---------------------------------------------------------------------- */
/* INPUT_DOCUMENT											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'INPUT_DOCUMENT')
BEGIN
	 DECLARE @reftable_13 nvarchar(60), @constraintname_13 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'INPUT_DOCUMENT'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_13, @constraintname_13
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_13+' drop constraint '+@constraintname_13)
	   FETCH NEXT from refcursor into @reftable_13, @constraintname_13
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [INPUT_DOCUMENT]
END


CREATE TABLE [INPUT_DOCUMENT]
(
	[INP_DOC_UID] VARCHAR(32) default '' NOT NULL,
	[PRO_UID] VARCHAR(32) default '0' NOT NULL,
	[INP_DOC_FORM_NEEDED] VARCHAR(20) default 'REAL' NOT NULL,
	[INP_DOC_ORIGINAL] VARCHAR(20) default 'COPY' NOT NULL,
	[INP_DOC_PUBLISHED] VARCHAR(20) default 'PRIVATE' NOT NULL,
	[INP_DOC_VERSIONING] TINYINT default 0 NOT NULL,
	[INP_DOC_DESTINATION_PATH] NVARCHAR(MAX)  NULL,
	[INP_DOC_TAGS] NVARCHAR(MAX)  NULL,
	CONSTRAINT INPUT_DOCUMENT_PK PRIMARY KEY ([INP_DOC_UID])
);

/* ---------------------------------------------------------------------- */
/* ISO_COUNTRY											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'ISO_COUNTRY')
BEGIN
	 DECLARE @reftable_14 nvarchar(60), @constraintname_14 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'ISO_COUNTRY'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_14, @constraintname_14
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_14+' drop constraint '+@constraintname_14)
	   FETCH NEXT from refcursor into @reftable_14, @constraintname_14
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [ISO_COUNTRY]
END


CREATE TABLE [ISO_COUNTRY]
(
	[IC_UID] VARCHAR(2) default '' NOT NULL,
	[IC_NAME] VARCHAR(255)  NULL,
	[IC_SORT_ORDER] VARCHAR(255)  NULL,
	CONSTRAINT ISO_COUNTRY_PK PRIMARY KEY ([IC_UID])
);

/* ---------------------------------------------------------------------- */
/* ISO_LOCATION											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'ISO_LOCATION')
BEGIN
	 DECLARE @reftable_15 nvarchar(60), @constraintname_15 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'ISO_LOCATION'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_15, @constraintname_15
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_15+' drop constraint '+@constraintname_15)
	   FETCH NEXT from refcursor into @reftable_15, @constraintname_15
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [ISO_LOCATION]
END


CREATE TABLE [ISO_LOCATION]
(
	[IC_UID] VARCHAR(2) default '' NOT NULL,
	[IL_UID] VARCHAR(5) default '' NOT NULL,
	[IL_NAME] VARCHAR(255)  NULL,
	[IL_NORMAL_NAME] VARCHAR(255)  NULL,
	[IS_UID] VARCHAR(4)  NULL,
	CONSTRAINT ISO_LOCATION_PK PRIMARY KEY ([IC_UID],[IL_UID])
);

/* ---------------------------------------------------------------------- */
/* ISO_SUBDIVISION											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'ISO_SUBDIVISION')
BEGIN
	 DECLARE @reftable_16 nvarchar(60), @constraintname_16 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'ISO_SUBDIVISION'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_16, @constraintname_16
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_16+' drop constraint '+@constraintname_16)
	   FETCH NEXT from refcursor into @reftable_16, @constraintname_16
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [ISO_SUBDIVISION]
END


CREATE TABLE [ISO_SUBDIVISION]
(
	[IC_UID] VARCHAR(2) default '' NOT NULL,
	[IS_UID] VARCHAR(4) default '' NOT NULL,
	[IS_NAME] VARCHAR(255) default '' NOT NULL,
	CONSTRAINT ISO_SUBDIVISION_PK PRIMARY KEY ([IC_UID],[IS_UID])
);

/* ---------------------------------------------------------------------- */
/* LANGUAGE											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'LANGUAGE')
BEGIN
	 DECLARE @reftable_17 nvarchar(60), @constraintname_17 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'LANGUAGE'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_17, @constraintname_17
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_17+' drop constraint '+@constraintname_17)
	   FETCH NEXT from refcursor into @reftable_17, @constraintname_17
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [LANGUAGE]
END


CREATE TABLE [LANGUAGE]
(
	[LAN_ID] VARCHAR(4) default '' NOT NULL,
	[LAN_NAME] VARCHAR(30) default '' NOT NULL,
	[LAN_NATIVE_NAME] VARCHAR(30) default '' NOT NULL,
	[LAN_DIRECTION] CHAR(1) default 'L' NOT NULL,
	[LAN_WEIGHT] INT default 0 NOT NULL,
	[LAN_ENABLED] CHAR(1) default '1' NOT NULL,
	[LAN_CALENDAR] VARCHAR(30) default 'GREGORIAN' NOT NULL,
	CONSTRAINT LANGUAGE_PK PRIMARY KEY ([LAN_ID])
);

/* ---------------------------------------------------------------------- */
/* LEXICO											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'LEXICO')
BEGIN
	 DECLARE @reftable_18 nvarchar(60), @constraintname_18 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'LEXICO'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_18, @constraintname_18
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_18+' drop constraint '+@constraintname_18)
	   FETCH NEXT from refcursor into @reftable_18, @constraintname_18
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [LEXICO]
END


CREATE TABLE [LEXICO]
(
	[LEX_TOPIC] VARCHAR(64) default '' NOT NULL,
	[LEX_KEY] VARCHAR(128) default '' NOT NULL,
	[LEX_VALUE] VARCHAR(128) default '' NOT NULL,
	[LEX_CAPTION] VARCHAR(128) default '' NOT NULL,
	CONSTRAINT LEXICO_PK PRIMARY KEY ([LEX_TOPIC],[LEX_KEY])
);

/* ---------------------------------------------------------------------- */
/* OUTPUT_DOCUMENT											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'OUTPUT_DOCUMENT')
BEGIN
	 DECLARE @reftable_19 nvarchar(60), @constraintname_19 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'OUTPUT_DOCUMENT'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_19, @constraintname_19
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_19+' drop constraint '+@constraintname_19)
	   FETCH NEXT from refcursor into @reftable_19, @constraintname_19
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [OUTPUT_DOCUMENT]
END


CREATE TABLE [OUTPUT_DOCUMENT]
(
	[OUT_DOC_UID] VARCHAR(32) default '' NOT NULL,
	[PRO_UID] VARCHAR(32) default '' NOT NULL,
	[OUT_DOC_LANDSCAPE] TINYINT default 0 NOT NULL,
	[OUT_DOC_MEDIA] VARCHAR(10) default 'Letter' NOT NULL,
	[OUT_DOC_LEFT_MARGIN] INT default 30 NULL,
	[OUT_DOC_RIGHT_MARGIN] INT default 15 NULL,
	[OUT_DOC_TOP_MARGIN] INT default 15 NULL,
	[OUT_DOC_BOTTOM_MARGIN] INT default 15 NULL,
	[OUT_DOC_GENERATE] VARCHAR(10) default 'BOTH' NOT NULL,
	[OUT_DOC_TYPE] VARCHAR(32) default 'HTML' NOT NULL,
	[OUT_DOC_CURRENT_REVISION] INT default 0 NULL,
	[OUT_DOC_FIELD_MAPPING] NVARCHAR(MAX)  NULL,
	[OUT_DOC_VERSIONING] TINYINT default 0 NOT NULL,
	[OUT_DOC_DESTINATION_PATH] NVARCHAR(MAX)  NULL,
	[OUT_DOC_TAGS] NVARCHAR(MAX)  NULL,
	[OUT_DOC_PDF_SECURITY_ENABLED] TINYINT default 0 NULL,
	[OUT_DOC_PDF_SECURITY_OPEN_PASSWORD] VARCHAR(32) default '' NULL,
	[OUT_DOC_PDF_SECURITY_OWNER_PASSWORD] VARCHAR(32) default '' NULL,
	[OUT_DOC_PDF_SECURITY_PERMISSIONS] VARCHAR(150) default '' NULL,
	CONSTRAINT OUTPUT_DOCUMENT_PK PRIMARY KEY ([OUT_DOC_UID])
);

/* ---------------------------------------------------------------------- */
/* PROCESS											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'PROCESS')
BEGIN
	 DECLARE @reftable_20 nvarchar(60), @constraintname_20 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'PROCESS'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_20, @constraintname_20
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_20+' drop constraint '+@constraintname_20)
	   FETCH NEXT from refcursor into @reftable_20, @constraintname_20
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [PROCESS]
END


CREATE TABLE [PROCESS]
(
	[PRO_UID] VARCHAR(32) default '' NOT NULL,
	[PRO_PARENT] VARCHAR(32) default '0' NOT NULL,
	[PRO_TIME] FLOAT default 1 NOT NULL,
	[PRO_TIMEUNIT] VARCHAR(20) default 'DAYS' NOT NULL,
	[PRO_STATUS] VARCHAR(20) default 'ACTIVE' NOT NULL,
	[PRO_TYPE_DAY] CHAR(1) default '0' NOT NULL,
	[PRO_TYPE] VARCHAR(20) default 'NORMAL' NOT NULL,
	[PRO_ASSIGNMENT] VARCHAR(20) default 'FALSE' NOT NULL,
	[PRO_SHOW_MAP] TINYINT default 1 NOT NULL,
	[PRO_SHOW_MESSAGE] TINYINT default 1 NOT NULL,
	[PRO_SUBPROCESS] TINYINT default 0 NOT NULL,
	[PRO_TRI_DELETED] VARCHAR(32) default '' NOT NULL,
	[PRO_TRI_CANCELED] VARCHAR(32) default '' NOT NULL,
	[PRO_TRI_PAUSED] VARCHAR(32) default '' NOT NULL,
	[PRO_TRI_REASSIGNED] VARCHAR(32) default '' NOT NULL,
	[PRO_SHOW_DELEGATE] TINYINT default 1 NOT NULL,
	[PRO_SHOW_DYNAFORM] TINYINT default 0 NOT NULL,
	[PRO_CATEGORY] VARCHAR(48) default '' NOT NULL,
	[PRO_SUB_CATEGORY] VARCHAR(48) default '' NOT NULL,
	[PRO_INDUSTRY] INT default 1 NOT NULL,
	[PRO_UPDATE_DATE] CHAR(19)  NULL,
	[PRO_CREATE_DATE] CHAR(19)  NOT NULL,
	[PRO_CREATE_USER] VARCHAR(32) default '' NOT NULL,
	[PRO_HEIGHT] INT default 5000 NOT NULL,
	[PRO_WIDTH] INT default 10000 NOT NULL,
	[PRO_TITLE_X] INT default 0 NOT NULL,
	[PRO_TITLE_Y] INT default 6 NOT NULL,
	[PRO_DEBUG] INT default 0 NOT NULL,
	[PRO_DYNAFORMS] NVARCHAR(MAX)  NULL,
	[PRO_DERIVATION_SCREEN_TPL] VARCHAR(128) default '' NULL,
	CONSTRAINT PROCESS_PK PRIMARY KEY ([PRO_UID])
);

/* ---------------------------------------------------------------------- */
/* PROCESS_OWNER											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'PROCESS_OWNER')
BEGIN
	 DECLARE @reftable_21 nvarchar(60), @constraintname_21 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'PROCESS_OWNER'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_21, @constraintname_21
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_21+' drop constraint '+@constraintname_21)
	   FETCH NEXT from refcursor into @reftable_21, @constraintname_21
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [PROCESS_OWNER]
END


CREATE TABLE [PROCESS_OWNER]
(
	[OWN_UID] VARCHAR(32) default '' NOT NULL,
	[PRO_UID] VARCHAR(32) default '' NOT NULL,
	CONSTRAINT PROCESS_OWNER_PK PRIMARY KEY ([OWN_UID],[PRO_UID])
);

/* ---------------------------------------------------------------------- */
/* REPORT_TABLE											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'REPORT_TABLE')
BEGIN
	 DECLARE @reftable_22 nvarchar(60), @constraintname_22 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'REPORT_TABLE'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_22, @constraintname_22
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_22+' drop constraint '+@constraintname_22)
	   FETCH NEXT from refcursor into @reftable_22, @constraintname_22
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [REPORT_TABLE]
END


CREATE TABLE [REPORT_TABLE]
(
	[REP_TAB_UID] VARCHAR(32) default '' NOT NULL,
	[PRO_UID] VARCHAR(32) default '' NOT NULL,
	[REP_TAB_NAME] VARCHAR(100) default '' NOT NULL,
	[REP_TAB_TYPE] VARCHAR(6) default '' NOT NULL,
	[REP_TAB_GRID] VARCHAR(150) default '' NULL,
	[REP_TAB_CONNECTION] VARCHAR(32) default '' NOT NULL,
	[REP_TAB_CREATE_DATE] CHAR(19)  NOT NULL,
	[REP_TAB_STATUS] CHAR(8) default 'ACTIVE' NOT NULL,
	CONSTRAINT REPORT_TABLE_PK PRIMARY KEY ([REP_TAB_UID])
);

/* ---------------------------------------------------------------------- */
/* REPORT_VAR											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'REPORT_VAR')
BEGIN
	 DECLARE @reftable_23 nvarchar(60), @constraintname_23 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'REPORT_VAR'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_23, @constraintname_23
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_23+' drop constraint '+@constraintname_23)
	   FETCH NEXT from refcursor into @reftable_23, @constraintname_23
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [REPORT_VAR]
END


CREATE TABLE [REPORT_VAR]
(
	[REP_VAR_UID] VARCHAR(32) default '' NOT NULL,
	[PRO_UID] VARCHAR(32) default '' NOT NULL,
	[REP_TAB_UID] VARCHAR(32) default '' NOT NULL,
	[REP_VAR_NAME] VARCHAR(255) default '' NOT NULL,
	[REP_VAR_TYPE] VARCHAR(20) default '' NOT NULL,
	CONSTRAINT REPORT_VAR_PK PRIMARY KEY ([REP_VAR_UID])
);

/* ---------------------------------------------------------------------- */
/* ROUTE											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'ROUTE')
BEGIN
	 DECLARE @reftable_24 nvarchar(60), @constraintname_24 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'ROUTE'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_24, @constraintname_24
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_24+' drop constraint '+@constraintname_24)
	   FETCH NEXT from refcursor into @reftable_24, @constraintname_24
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [ROUTE]
END


CREATE TABLE [ROUTE]
(
	[ROU_UID] VARCHAR(32) default '' NOT NULL,
	[ROU_PARENT] VARCHAR(32) default '0' NOT NULL,
	[PRO_UID] VARCHAR(32) default '' NOT NULL,
	[TAS_UID] VARCHAR(32) default '' NOT NULL,
	[ROU_NEXT_TASK] VARCHAR(32) default '0' NOT NULL,
	[ROU_CASE] INT default 0 NOT NULL,
	[ROU_TYPE] VARCHAR(25) default 'SEQUENTIAL' NOT NULL,
	[ROU_CONDITION] VARCHAR(512) default '' NOT NULL,
	[ROU_TO_LAST_USER] VARCHAR(20) default 'FALSE' NOT NULL,
	[ROU_OPTIONAL] VARCHAR(20) default 'FALSE' NOT NULL,
	[ROU_SEND_EMAIL] VARCHAR(20) default 'TRUE' NOT NULL,
	[ROU_SOURCEANCHOR] INT default 1 NULL,
	[ROU_TARGETANCHOR] INT default 0 NULL,
	[ROU_TO_PORT] INT default 1 NOT NULL,
	[ROU_FROM_PORT] INT default 2 NOT NULL,
	[ROU_EVN_UID] VARCHAR(32) default '' NOT NULL,
	[GAT_UID] VARCHAR(32) default '' NOT NULL,
	CONSTRAINT ROUTE_PK PRIMARY KEY ([ROU_UID])
);

/* ---------------------------------------------------------------------- */
/* STEP											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'STEP')
BEGIN
	 DECLARE @reftable_25 nvarchar(60), @constraintname_25 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'STEP'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_25, @constraintname_25
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_25+' drop constraint '+@constraintname_25)
	   FETCH NEXT from refcursor into @reftable_25, @constraintname_25
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [STEP]
END


CREATE TABLE [STEP]
(
	[STEP_UID] VARCHAR(32) default '' NOT NULL,
	[PRO_UID] VARCHAR(32) default '0' NOT NULL,
	[TAS_UID] VARCHAR(32) default '0' NOT NULL,
	[STEP_TYPE_OBJ] VARCHAR(20) default 'DYNAFORM' NOT NULL,
	[STEP_UID_OBJ] VARCHAR(32) default '0' NOT NULL,
	[STEP_CONDITION] NVARCHAR(MAX)  NOT NULL,
	[STEP_POSITION] INT default 0 NOT NULL,
	[STEP_MODE] VARCHAR(10) default 'EDIT' NULL,
	CONSTRAINT STEP_PK PRIMARY KEY ([STEP_UID])
);

/* ---------------------------------------------------------------------- */
/* STEP_TRIGGER											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'STEP_TRIGGER')
BEGIN
	 DECLARE @reftable_26 nvarchar(60), @constraintname_26 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'STEP_TRIGGER'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_26, @constraintname_26
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_26+' drop constraint '+@constraintname_26)
	   FETCH NEXT from refcursor into @reftable_26, @constraintname_26
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [STEP_TRIGGER]
END


CREATE TABLE [STEP_TRIGGER]
(
	[STEP_UID] VARCHAR(32) default '' NOT NULL,
	[TAS_UID] VARCHAR(32) default '' NOT NULL,
	[TRI_UID] VARCHAR(32) default '' NOT NULL,
	[ST_TYPE] VARCHAR(20) default '' NOT NULL,
	[ST_CONDITION] VARCHAR(255) default '' NOT NULL,
	[ST_POSITION] INT default 0 NOT NULL,
	CONSTRAINT STEP_TRIGGER_PK PRIMARY KEY ([STEP_UID],[TAS_UID],[TRI_UID],[ST_TYPE])
);

/* ---------------------------------------------------------------------- */
/* SWIMLANES_ELEMENTS											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'SWIMLANES_ELEMENTS')
BEGIN
	 DECLARE @reftable_27 nvarchar(60), @constraintname_27 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'SWIMLANES_ELEMENTS'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_27, @constraintname_27
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_27+' drop constraint '+@constraintname_27)
	   FETCH NEXT from refcursor into @reftable_27, @constraintname_27
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [SWIMLANES_ELEMENTS]
END


CREATE TABLE [SWIMLANES_ELEMENTS]
(
	[SWI_UID] VARCHAR(32) default '' NOT NULL,
	[PRO_UID] VARCHAR(32) default '' NOT NULL,
	[SWI_TYPE] VARCHAR(20) default 'LINE' NOT NULL,
	[SWI_X] INT default 0 NOT NULL,
	[SWI_Y] INT default 0 NOT NULL,
	[SWI_WIDTH] INT default 0 NOT NULL,
	[SWI_HEIGHT] INT default 0 NOT NULL,
	[SWI_NEXT_UID] VARCHAR(32) default '' NULL,
	CONSTRAINT SWIMLANES_ELEMENTS_PK PRIMARY KEY ([SWI_UID])
);

/* ---------------------------------------------------------------------- */
/* TASK											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'TASK')
BEGIN
	 DECLARE @reftable_28 nvarchar(60), @constraintname_28 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'TASK'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_28, @constraintname_28
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_28+' drop constraint '+@constraintname_28)
	   FETCH NEXT from refcursor into @reftable_28, @constraintname_28
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [TASK]
END


CREATE TABLE [TASK]
(
	[PRO_UID] VARCHAR(32) default '' NOT NULL,
	[TAS_UID] VARCHAR(32) default '' NOT NULL,
	[TAS_TYPE] VARCHAR(20) default 'NORMAL' NOT NULL,
	[TAS_DURATION] FLOAT default 0 NOT NULL,
	[TAS_DELAY_TYPE] VARCHAR(30) default '' NOT NULL,
	[TAS_TEMPORIZER] FLOAT default 0 NOT NULL,
	[TAS_TYPE_DAY] CHAR(1) default '1' NOT NULL,
	[TAS_TIMEUNIT] VARCHAR(20) default 'DAYS' NOT NULL,
	[TAS_ALERT] VARCHAR(20) default 'FALSE' NOT NULL,
	[TAS_PRIORITY_VARIABLE] VARCHAR(100) default '' NOT NULL,
	[TAS_ASSIGN_TYPE] VARCHAR(30) default 'BALANCED' NOT NULL,
	[TAS_ASSIGN_VARIABLE] VARCHAR(100) default '@@SYS_NEXT_USER_TO_BE_ASSIGNED' NOT NULL,
	[TAS_MI_INSTANCE_VARIABLE] VARCHAR(100) default '@@SYS_VAR_TOTAL_INSTANCE' NOT NULL,
	[TAS_MI_COMPLETE_VARIABLE] VARCHAR(100) default '@@SYS_VAR_TOTAL_INSTANCES_COMPLETE' NOT NULL,
	[TAS_ASSIGN_LOCATION] VARCHAR(20) default 'FALSE' NOT NULL,
	[TAS_ASSIGN_LOCATION_ADHOC] VARCHAR(20) default 'FALSE' NOT NULL,
	[TAS_TRANSFER_FLY] VARCHAR(20) default 'FALSE' NOT NULL,
	[TAS_LAST_ASSIGNED] VARCHAR(32) default '0' NOT NULL,
	[TAS_USER] VARCHAR(32) default '0' NOT NULL,
	[TAS_CAN_UPLOAD] VARCHAR(20) default 'FALSE' NOT NULL,
	[TAS_VIEW_UPLOAD] VARCHAR(20) default 'FALSE' NOT NULL,
	[TAS_VIEW_ADDITIONAL_DOCUMENTATION] VARCHAR(20) default 'FALSE' NOT NULL,
	[TAS_CAN_CANCEL] VARCHAR(20) default 'FALSE' NOT NULL,
	[TAS_OWNER_APP] VARCHAR(32) default '' NOT NULL,
	[STG_UID] VARCHAR(32) default '' NOT NULL,
	[TAS_CAN_PAUSE] VARCHAR(20) default 'FALSE' NOT NULL,
	[TAS_CAN_SEND_MESSAGE] VARCHAR(20) default 'TRUE' NOT NULL,
	[TAS_CAN_DELETE_DOCS] VARCHAR(20) default 'FALSE' NOT NULL,
	[TAS_SELF_SERVICE] VARCHAR(20) default 'FALSE' NOT NULL,
	[TAS_START] VARCHAR(20) default 'FALSE' NOT NULL,
	[TAS_TO_LAST_USER] VARCHAR(20) default 'FALSE' NOT NULL,
	[TAS_SEND_LAST_EMAIL] VARCHAR(20) default 'TRUE' NOT NULL,
	[TAS_DERIVATION] VARCHAR(100) default 'NORMAL' NOT NULL,
	[TAS_POSX] INT default 0 NOT NULL,
	[TAS_POSY] INT default 0 NOT NULL,
	[TAS_WIDTH] INT default 110 NOT NULL,
	[TAS_HEIGHT] INT default 60 NOT NULL,
	[TAS_COLOR] VARCHAR(32) default '' NOT NULL,
	[TAS_EVN_UID] VARCHAR(32) default '' NOT NULL,
	[TAS_BOUNDARY] VARCHAR(32) default '' NOT NULL,
	[TAS_DERIVATION_SCREEN_TPL] VARCHAR(128) default '' NULL,
	CONSTRAINT TASK_PK PRIMARY KEY ([TAS_UID])
);

/* ---------------------------------------------------------------------- */
/* TASK_USER											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'TASK_USER')
BEGIN
	 DECLARE @reftable_29 nvarchar(60), @constraintname_29 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'TASK_USER'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_29, @constraintname_29
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_29+' drop constraint '+@constraintname_29)
	   FETCH NEXT from refcursor into @reftable_29, @constraintname_29
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [TASK_USER]
END


CREATE TABLE [TASK_USER]
(
	[TAS_UID] VARCHAR(32) default '' NOT NULL,
	[USR_UID] VARCHAR(32) default '' NOT NULL,
	[TU_TYPE] INT default 1 NOT NULL,
	[TU_RELATION] INT default 0 NOT NULL,
	CONSTRAINT TASK_USER_PK PRIMARY KEY ([TAS_UID],[USR_UID],[TU_TYPE],[TU_RELATION])
);

/* ---------------------------------------------------------------------- */
/* TRANSLATION											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'TRANSLATION')
BEGIN
	 DECLARE @reftable_30 nvarchar(60), @constraintname_30 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'TRANSLATION'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_30, @constraintname_30
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_30+' drop constraint '+@constraintname_30)
	   FETCH NEXT from refcursor into @reftable_30, @constraintname_30
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [TRANSLATION]
END


CREATE TABLE [TRANSLATION]
(
	[TRN_CATEGORY] VARCHAR(100) default '' NOT NULL,
	[TRN_ID] VARCHAR(100) default '' NOT NULL,
	[TRN_LANG] VARCHAR(10) default 'en' NOT NULL,
	[TRN_VALUE] NVARCHAR(MAX)  NOT NULL,
	[TRN_UPDATE_DATE] CHAR(19)  NULL,
	CONSTRAINT TRANSLATION_PK PRIMARY KEY ([TRN_CATEGORY],[TRN_ID],[TRN_LANG])
);

/* ---------------------------------------------------------------------- */
/* TRIGGERS											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'TRIGGERS')
BEGIN
	 DECLARE @reftable_31 nvarchar(60), @constraintname_31 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'TRIGGERS'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_31, @constraintname_31
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_31+' drop constraint '+@constraintname_31)
	   FETCH NEXT from refcursor into @reftable_31, @constraintname_31
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [TRIGGERS]
END


CREATE TABLE [TRIGGERS]
(
	[TRI_UID] VARCHAR(32) default '' NOT NULL,
	[PRO_UID] VARCHAR(32) default '' NOT NULL,
	[TRI_TYPE] VARCHAR(20) default 'SCRIPT' NOT NULL,
	[TRI_WEBBOT] NVARCHAR(MAX)  NOT NULL,
	[TRI_PARAM] NVARCHAR(MAX)  NULL,
	CONSTRAINT TRIGGERS_PK PRIMARY KEY ([TRI_UID])
);

/* ---------------------------------------------------------------------- */
/* USERS											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'USERS')
BEGIN
	 DECLARE @reftable_32 nvarchar(60), @constraintname_32 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'USERS'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_32, @constraintname_32
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_32+' drop constraint '+@constraintname_32)
	   FETCH NEXT from refcursor into @reftable_32, @constraintname_32
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [USERS]
END


CREATE TABLE [USERS]
(
	[USR_UID] VARCHAR(32) default '' NOT NULL,
	[USR_USERNAME] VARCHAR(100) default '' NOT NULL,
	[USR_PASSWORD] VARCHAR(32) default '' NOT NULL,
	[USR_FIRSTNAME] VARCHAR(50) default '' NOT NULL,
	[USR_LASTNAME] VARCHAR(50) default '' NOT NULL,
	[USR_EMAIL] VARCHAR(100) default '' NOT NULL,
	[USR_DUE_DATE] CHAR(19)  NOT NULL,
	[USR_CREATE_DATE] CHAR(19)  NOT NULL,
	[USR_UPDATE_DATE] CHAR(19)  NOT NULL,
	[USR_STATUS] VARCHAR(32) default 'ACTIVE' NOT NULL,
	[USR_COUNTRY] VARCHAR(3) default '' NOT NULL,
	[USR_CITY] VARCHAR(3) default '' NOT NULL,
	[USR_LOCATION] VARCHAR(3) default '' NOT NULL,
	[USR_ADDRESS] VARCHAR(255) default '' NOT NULL,
	[USR_PHONE] VARCHAR(24) default '' NOT NULL,
	[USR_FAX] VARCHAR(24) default '' NOT NULL,
	[USR_CELLULAR] VARCHAR(24) default '' NOT NULL,
	[USR_ZIP_CODE] VARCHAR(16) default '' NOT NULL,
	[DEP_UID] VARCHAR(32) default '' NOT NULL,
	[USR_POSITION] VARCHAR(100) default '' NOT NULL,
	[USR_RESUME] VARCHAR(100) default '' NOT NULL,
	[USR_BIRTHDAY] CHAR(19)  NULL,
	[USR_ROLE] VARCHAR(32) default 'PROCESSMAKER_ADMIN' NULL,
	[USR_REPORTS_TO] VARCHAR(32) default '' NULL,
	[USR_REPLACED_BY] VARCHAR(32) default '' NULL,
	[USR_UX] VARCHAR(128) default 'NORMAL' NULL,
	CONSTRAINT USERS_PK PRIMARY KEY ([USR_UID])
);

/* ---------------------------------------------------------------------- */
/* APP_THREAD											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'APP_THREAD')
BEGIN
	 DECLARE @reftable_33 nvarchar(60), @constraintname_33 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'APP_THREAD'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_33, @constraintname_33
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_33+' drop constraint '+@constraintname_33)
	   FETCH NEXT from refcursor into @reftable_33, @constraintname_33
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [APP_THREAD]
END


CREATE TABLE [APP_THREAD]
(
	[APP_UID] VARCHAR(32) default '' NOT NULL,
	[APP_THREAD_INDEX] INT default 0 NOT NULL,
	[APP_THREAD_PARENT] INT default 0 NOT NULL,
	[APP_THREAD_STATUS] VARCHAR(32) default 'OPEN' NOT NULL,
	[DEL_INDEX] INT default 0 NOT NULL,
	CONSTRAINT APP_THREAD_PK PRIMARY KEY ([APP_UID],[APP_THREAD_INDEX])
);

/* ---------------------------------------------------------------------- */
/* APP_DELAY											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'APP_DELAY')
BEGIN
	 DECLARE @reftable_34 nvarchar(60), @constraintname_34 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'APP_DELAY'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_34, @constraintname_34
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_34+' drop constraint '+@constraintname_34)
	   FETCH NEXT from refcursor into @reftable_34, @constraintname_34
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [APP_DELAY]
END


CREATE TABLE [APP_DELAY]
(
	[APP_DELAY_UID] VARCHAR(32) default '' NOT NULL,
	[PRO_UID] VARCHAR(32) default '0' NOT NULL,
	[APP_UID] VARCHAR(32) default '0' NOT NULL,
	[APP_THREAD_INDEX] INT default 0 NOT NULL,
	[APP_DEL_INDEX] INT default 0 NOT NULL,
	[APP_TYPE] VARCHAR(20) default '0' NOT NULL,
	[APP_STATUS] VARCHAR(20) default '0' NOT NULL,
	[APP_NEXT_TASK] VARCHAR(32) default '0' NULL,
	[APP_DELEGATION_USER] VARCHAR(32) default '0' NULL,
	[APP_ENABLE_ACTION_USER] VARCHAR(32) default '0' NOT NULL,
	[APP_ENABLE_ACTION_DATE] CHAR(19)  NOT NULL,
	[APP_DISABLE_ACTION_USER] VARCHAR(32) default '0' NULL,
	[APP_DISABLE_ACTION_DATE] CHAR(19)  NULL,
	[APP_AUTOMATIC_DISABLED_DATE] CHAR(19)  NULL,
	CONSTRAINT APP_DELAY_PK PRIMARY KEY ([APP_DELAY_UID])
);

CREATE INDEX [indexAppDelay] ON [APP_DELAY] ([PRO_UID],[APP_UID],[APP_THREAD_INDEX],[APP_DEL_INDEX],[APP_NEXT_TASK],[APP_DELEGATION_USER],[APP_DISABLE_ACTION_USER]);

/* ---------------------------------------------------------------------- */
/* PROCESS_USER											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'PROCESS_USER')
BEGIN
	 DECLARE @reftable_35 nvarchar(60), @constraintname_35 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'PROCESS_USER'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_35, @constraintname_35
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_35+' drop constraint '+@constraintname_35)
	   FETCH NEXT from refcursor into @reftable_35, @constraintname_35
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [PROCESS_USER]
END


CREATE TABLE [PROCESS_USER]
(
	[PU_UID] VARCHAR(32) default '' NOT NULL,
	[PRO_UID] VARCHAR(32) default '' NOT NULL,
	[USR_UID] VARCHAR(32) default '' NOT NULL,
	[PU_TYPE] VARCHAR(20) default '' NOT NULL,
	CONSTRAINT PROCESS_USER_PK PRIMARY KEY ([PU_UID])
);

/* ---------------------------------------------------------------------- */
/* SESSION											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'SESSION')
BEGIN
	 DECLARE @reftable_36 nvarchar(60), @constraintname_36 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'SESSION'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_36, @constraintname_36
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_36+' drop constraint '+@constraintname_36)
	   FETCH NEXT from refcursor into @reftable_36, @constraintname_36
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [SESSION]
END


CREATE TABLE [SESSION]
(
	[SES_UID] VARCHAR(32) default '' NOT NULL,
	[SES_STATUS] VARCHAR(16) default 'ACTIVE' NOT NULL,
	[USR_UID] VARCHAR(32) default 'ACTIVE' NOT NULL,
	[SES_REMOTE_IP] VARCHAR(32) default '0.0.0.0' NOT NULL,
	[SES_INIT_DATE] VARCHAR(19) default '' NOT NULL,
	[SES_DUE_DATE] VARCHAR(19) default '' NOT NULL,
	[SES_END_DATE] VARCHAR(19) default '' NOT NULL,
	CONSTRAINT SESSION_PK PRIMARY KEY ([SES_UID])
);

CREATE INDEX [indexSession] ON [SESSION] ([SES_UID]);

/* ---------------------------------------------------------------------- */
/* DB_SOURCE											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'DB_SOURCE')
BEGIN
	 DECLARE @reftable_37 nvarchar(60), @constraintname_37 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'DB_SOURCE'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_37, @constraintname_37
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_37+' drop constraint '+@constraintname_37)
	   FETCH NEXT from refcursor into @reftable_37, @constraintname_37
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [DB_SOURCE]
END


CREATE TABLE [DB_SOURCE]
(
	[DBS_UID] VARCHAR(32) default '' NOT NULL,
	[PRO_UID] VARCHAR(32) default '0' NOT NULL,
	[DBS_TYPE] VARCHAR(8) default '0' NOT NULL,
	[DBS_SERVER] VARCHAR(100) default '0' NOT NULL,
	[DBS_DATABASE_NAME] VARCHAR(100) default '0' NOT NULL,
	[DBS_USERNAME] VARCHAR(32) default '0' NOT NULL,
	[DBS_PASSWORD] VARCHAR(32) default '' NULL,
	[DBS_PORT] INT default 0 NULL,
	[DBS_ENCODE] VARCHAR(32) default '' NULL,
	CONSTRAINT DB_SOURCE_PK PRIMARY KEY ([DBS_UID],[PRO_UID])
);

CREATE INDEX [indexDBSource] ON [DB_SOURCE] ([PRO_UID]);

/* ---------------------------------------------------------------------- */
/* STEP_SUPERVISOR											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'STEP_SUPERVISOR')
BEGIN
	 DECLARE @reftable_38 nvarchar(60), @constraintname_38 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'STEP_SUPERVISOR'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_38, @constraintname_38
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_38+' drop constraint '+@constraintname_38)
	   FETCH NEXT from refcursor into @reftable_38, @constraintname_38
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [STEP_SUPERVISOR]
END


CREATE TABLE [STEP_SUPERVISOR]
(
	[STEP_UID] VARCHAR(32) default '' NOT NULL,
	[PRO_UID] VARCHAR(32) default '0' NOT NULL,
	[STEP_TYPE_OBJ] VARCHAR(20) default 'DYNAFORM' NOT NULL,
	[STEP_UID_OBJ] VARCHAR(32) default '0' NOT NULL,
	[STEP_POSITION] INT default 0 NOT NULL,
	CONSTRAINT STEP_SUPERVISOR_PK PRIMARY KEY ([STEP_UID])
);

CREATE INDEX [indexStepSupervisor] ON [STEP_SUPERVISOR] ([PRO_UID],[STEP_TYPE_OBJ],[STEP_UID_OBJ]);

/* ---------------------------------------------------------------------- */
/* OBJECT_PERMISSION											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'OBJECT_PERMISSION')
BEGIN
	 DECLARE @reftable_39 nvarchar(60), @constraintname_39 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'OBJECT_PERMISSION'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_39, @constraintname_39
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_39+' drop constraint '+@constraintname_39)
	   FETCH NEXT from refcursor into @reftable_39, @constraintname_39
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [OBJECT_PERMISSION]
END


CREATE TABLE [OBJECT_PERMISSION]
(
	[OP_UID] VARCHAR(32) default '0' NOT NULL,
	[PRO_UID] VARCHAR(32) default '0' NOT NULL,
	[TAS_UID] VARCHAR(32) default '0' NOT NULL,
	[USR_UID] VARCHAR(32) default '0' NOT NULL,
	[OP_USER_RELATION] INT default 0 NOT NULL,
	[OP_TASK_SOURCE] VARCHAR(32) default '0' NULL,
	[OP_PARTICIPATE] INT default 0 NOT NULL,
	[OP_OBJ_TYPE] VARCHAR(15) default '0' NOT NULL,
	[OP_OBJ_UID] VARCHAR(32) default '0' NOT NULL,
	[OP_ACTION] VARCHAR(10) default '0' NOT NULL,
	[OP_CASE_STATUS] VARCHAR(10) default '0' NULL,
	CONSTRAINT OBJECT_PERMISSION_PK PRIMARY KEY ([OP_UID])
);

CREATE INDEX [indexObjctPermission] ON [OBJECT_PERMISSION] ([PRO_UID],[TAS_UID],[USR_UID],[OP_TASK_SOURCE],[OP_OBJ_UID]);

/* ---------------------------------------------------------------------- */
/* CASE_TRACKER											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'CASE_TRACKER')
BEGIN
	 DECLARE @reftable_40 nvarchar(60), @constraintname_40 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'CASE_TRACKER'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_40, @constraintname_40
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_40+' drop constraint '+@constraintname_40)
	   FETCH NEXT from refcursor into @reftable_40, @constraintname_40
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [CASE_TRACKER]
END


CREATE TABLE [CASE_TRACKER]
(
	[PRO_UID] VARCHAR(32) default '0' NOT NULL,
	[CT_MAP_TYPE] VARCHAR(10) default '0' NOT NULL,
	[CT_DERIVATION_HISTORY] INT default 0 NOT NULL,
	[CT_MESSAGE_HISTORY] INT default 0 NOT NULL,
	CONSTRAINT CASE_TRACKER_PK PRIMARY KEY ([PRO_UID])
);

/* ---------------------------------------------------------------------- */
/* CASE_TRACKER_OBJECT											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'CASE_TRACKER_OBJECT')
BEGIN
	 DECLARE @reftable_41 nvarchar(60), @constraintname_41 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'CASE_TRACKER_OBJECT'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_41, @constraintname_41
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_41+' drop constraint '+@constraintname_41)
	   FETCH NEXT from refcursor into @reftable_41, @constraintname_41
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [CASE_TRACKER_OBJECT]
END


CREATE TABLE [CASE_TRACKER_OBJECT]
(
	[CTO_UID] VARCHAR(32) default '' NOT NULL,
	[PRO_UID] VARCHAR(32) default '0' NOT NULL,
	[CTO_TYPE_OBJ] VARCHAR(20) default 'DYNAFORM' NOT NULL,
	[CTO_UID_OBJ] VARCHAR(32) default '0' NOT NULL,
	[CTO_CONDITION] NVARCHAR(MAX)  NOT NULL,
	[CTO_POSITION] INT default 0 NOT NULL,
	CONSTRAINT CASE_TRACKER_OBJECT_PK PRIMARY KEY ([CTO_UID])
);

CREATE INDEX [indexCaseTrackerObject] ON [CASE_TRACKER_OBJECT] ([PRO_UID],[CTO_UID_OBJ]);

/* ---------------------------------------------------------------------- */
/* STAGE											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'STAGE')
BEGIN
	 DECLARE @reftable_42 nvarchar(60), @constraintname_42 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'STAGE'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_42, @constraintname_42
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_42+' drop constraint '+@constraintname_42)
	   FETCH NEXT from refcursor into @reftable_42, @constraintname_42
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [STAGE]
END


CREATE TABLE [STAGE]
(
	[STG_UID] VARCHAR(32) default '' NOT NULL,
	[PRO_UID] VARCHAR(32) default '' NOT NULL,
	[STG_POSX] INT default 0 NOT NULL,
	[STG_POSY] INT default 0 NOT NULL,
	[STG_INDEX] INT default 0 NOT NULL,
	CONSTRAINT STAGE_PK PRIMARY KEY ([STG_UID])
);

/* ---------------------------------------------------------------------- */
/* SUB_PROCESS											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'SUB_PROCESS')
BEGIN
	 DECLARE @reftable_43 nvarchar(60), @constraintname_43 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'SUB_PROCESS'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_43, @constraintname_43
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_43+' drop constraint '+@constraintname_43)
	   FETCH NEXT from refcursor into @reftable_43, @constraintname_43
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [SUB_PROCESS]
END


CREATE TABLE [SUB_PROCESS]
(
	[SP_UID] VARCHAR(32) default '' NOT NULL,
	[PRO_UID] VARCHAR(32) default '' NOT NULL,
	[TAS_UID] VARCHAR(32) default '' NOT NULL,
	[PRO_PARENT] VARCHAR(32) default '' NOT NULL,
	[TAS_PARENT] VARCHAR(32) default '' NOT NULL,
	[SP_TYPE] VARCHAR(20) default '' NOT NULL,
	[SP_SYNCHRONOUS] INT default 0 NOT NULL,
	[SP_SYNCHRONOUS_TYPE] VARCHAR(20) default '' NOT NULL,
	[SP_SYNCHRONOUS_WAIT] INT default 0 NOT NULL,
	[SP_VARIABLES_OUT] NVARCHAR(MAX)  NOT NULL,
	[SP_VARIABLES_IN] NVARCHAR(MAX)  NULL,
	[SP_GRID_IN] VARCHAR(50) default '' NOT NULL,
	CONSTRAINT SUB_PROCESS_PK PRIMARY KEY ([SP_UID])
);

CREATE INDEX [indexSubProcess] ON [SUB_PROCESS] ([PRO_UID],[PRO_PARENT]);

/* ---------------------------------------------------------------------- */
/* SUB_APPLICATION											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'SUB_APPLICATION')
BEGIN
	 DECLARE @reftable_44 nvarchar(60), @constraintname_44 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'SUB_APPLICATION'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_44, @constraintname_44
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_44+' drop constraint '+@constraintname_44)
	   FETCH NEXT from refcursor into @reftable_44, @constraintname_44
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [SUB_APPLICATION]
END


CREATE TABLE [SUB_APPLICATION]
(
	[APP_UID] VARCHAR(32) default '' NOT NULL,
	[APP_PARENT] VARCHAR(32) default '' NOT NULL,
	[DEL_INDEX_PARENT] INT default 0 NOT NULL,
	[DEL_THREAD_PARENT] INT default 0 NOT NULL,
	[SA_STATUS] VARCHAR(32) default '' NOT NULL,
	[SA_VALUES_OUT] NVARCHAR(MAX)  NOT NULL,
	[SA_VALUES_IN] NVARCHAR(MAX)  NULL,
	[SA_INIT_DATE] CHAR(19)  NULL,
	[SA_FINISH_DATE] CHAR(19)  NULL,
	CONSTRAINT SUB_APPLICATION_PK PRIMARY KEY ([APP_UID],[APP_PARENT],[DEL_INDEX_PARENT],[DEL_THREAD_PARENT])
);

/* ---------------------------------------------------------------------- */
/* LOGIN_LOG											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'LOGIN_LOG')
BEGIN
	 DECLARE @reftable_45 nvarchar(60), @constraintname_45 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'LOGIN_LOG'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_45, @constraintname_45
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_45+' drop constraint '+@constraintname_45)
	   FETCH NEXT from refcursor into @reftable_45, @constraintname_45
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [LOGIN_LOG]
END


CREATE TABLE [LOGIN_LOG]
(
	[LOG_UID] VARCHAR(32) default '' NOT NULL,
	[LOG_STATUS] VARCHAR(100) default '' NOT NULL,
	[LOG_IP] VARCHAR(15) default '' NOT NULL,
	[LOG_SID] VARCHAR(100) default '' NOT NULL,
	[LOG_INIT_DATE] CHAR(19)  NULL,
	[LOG_END_DATE] CHAR(19)  NULL,
	[LOG_CLIENT_HOSTNAME] VARCHAR(100) default '' NOT NULL,
	[USR_UID] VARCHAR(32) default '' NOT NULL,
	CONSTRAINT LOGIN_LOG_PK PRIMARY KEY ([LOG_UID])
);

/* ---------------------------------------------------------------------- */
/* USERS_PROPERTIES											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'USERS_PROPERTIES')
BEGIN
	 DECLARE @reftable_46 nvarchar(60), @constraintname_46 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'USERS_PROPERTIES'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_46, @constraintname_46
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_46+' drop constraint '+@constraintname_46)
	   FETCH NEXT from refcursor into @reftable_46, @constraintname_46
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [USERS_PROPERTIES]
END


CREATE TABLE [USERS_PROPERTIES]
(
	[USR_UID] VARCHAR(32) default '' NOT NULL,
	[USR_LAST_UPDATE_DATE] CHAR(19)  NULL,
	[USR_LOGGED_NEXT_TIME] INT default 0 NULL,
	[USR_PASSWORD_HISTORY] NVARCHAR(MAX)  NULL,
	CONSTRAINT USERS_PROPERTIES_PK PRIMARY KEY ([USR_UID])
);

/* ---------------------------------------------------------------------- */
/* ADDITIONAL_TABLES											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'ADDITIONAL_TABLES')
BEGIN
	 DECLARE @reftable_47 nvarchar(60), @constraintname_47 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'ADDITIONAL_TABLES'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_47, @constraintname_47
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_47+' drop constraint '+@constraintname_47)
	   FETCH NEXT from refcursor into @reftable_47, @constraintname_47
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [ADDITIONAL_TABLES]
END


CREATE TABLE [ADDITIONAL_TABLES]
(
	[ADD_TAB_UID] VARCHAR(32) default '' NOT NULL,
	[ADD_TAB_NAME] VARCHAR(60) default '' NOT NULL,
	[ADD_TAB_CLASS_NAME] VARCHAR(100) default '' NOT NULL,
	[ADD_TAB_DESCRIPTION] NVARCHAR(MAX)  NULL,
	[ADD_TAB_SDW_LOG_INSERT] TINYINT default 0 NULL,
	[ADD_TAB_SDW_LOG_UPDATE] TINYINT default 0 NULL,
	[ADD_TAB_SDW_LOG_DELETE] TINYINT default 0 NULL,
	[ADD_TAB_SDW_LOG_SELECT] TINYINT default 0 NULL,
	[ADD_TAB_SDW_MAX_LENGTH] INT default 0 NULL,
	[ADD_TAB_SDW_AUTO_DELETE] TINYINT default 0 NULL,
	[ADD_TAB_PLG_UID] VARCHAR(32) default '' NULL,
	[DBS_UID] VARCHAR(32) default '' NULL,
	[PRO_UID] VARCHAR(32) default '' NULL,
	[ADD_TAB_TYPE] VARCHAR(32) default '' NULL,
	[ADD_TAB_GRID] VARCHAR(256) default '' NULL,
	[ADD_TAB_TAG] VARCHAR(256) default '' NULL,
	CONSTRAINT ADDITIONAL_TABLES_PK PRIMARY KEY ([ADD_TAB_UID])
);

/* ---------------------------------------------------------------------- */
/* FIELDS											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'FIELDS')
BEGIN
	 DECLARE @reftable_48 nvarchar(60), @constraintname_48 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'FIELDS'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_48, @constraintname_48
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_48+' drop constraint '+@constraintname_48)
	   FETCH NEXT from refcursor into @reftable_48, @constraintname_48
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [FIELDS]
END


CREATE TABLE [FIELDS]
(
	[FLD_UID] VARCHAR(32) default '' NOT NULL,
	[ADD_TAB_UID] VARCHAR(32) default '' NOT NULL,
	[FLD_INDEX] INT default 1 NOT NULL,
	[FLD_NAME] VARCHAR(60) default '' NOT NULL,
	[FLD_DESCRIPTION] NVARCHAR(MAX)  NOT NULL,
	[FLD_TYPE] VARCHAR(20) default '' NOT NULL,
	[FLD_SIZE] INT default 0 NULL,
	[FLD_NULL] TINYINT default 1 NOT NULL,
	[FLD_AUTO_INCREMENT] TINYINT default 0 NOT NULL,
	[FLD_KEY] TINYINT default 0 NOT NULL,
	[FLD_FOREIGN_KEY] TINYINT default 0 NOT NULL,
	[FLD_FOREIGN_KEY_TABLE] VARCHAR(32) default '' NOT NULL,
	[FLD_DYN_NAME] VARCHAR(128) default '' NULL,
	[FLD_DYN_UID] VARCHAR(128) default '' NULL,
	[FLD_FILTER] TINYINT default 0 NULL,
	CONSTRAINT FIELDS_PK PRIMARY KEY ([FLD_UID])
);

/* ---------------------------------------------------------------------- */
/* SHADOW_TABLE											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'SHADOW_TABLE')
BEGIN
	 DECLARE @reftable_49 nvarchar(60), @constraintname_49 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'SHADOW_TABLE'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_49, @constraintname_49
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_49+' drop constraint '+@constraintname_49)
	   FETCH NEXT from refcursor into @reftable_49, @constraintname_49
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [SHADOW_TABLE]
END


CREATE TABLE [SHADOW_TABLE]
(
	[SHD_UID] VARCHAR(32) default '' NOT NULL,
	[ADD_TAB_UID] VARCHAR(32) default '' NOT NULL,
	[SHD_ACTION] VARCHAR(10) default '' NOT NULL,
	[SHD_DETAILS] NVARCHAR(MAX)  NOT NULL,
	[USR_UID] VARCHAR(32) default '' NOT NULL,
	[APP_UID] VARCHAR(32) default '' NOT NULL,
	[SHD_DATE] CHAR(19)  NULL,
	CONSTRAINT SHADOW_TABLE_PK PRIMARY KEY ([SHD_UID])
);

CREATE INDEX [indexShadowTable] ON [SHADOW_TABLE] ([SHD_UID]);

/* ---------------------------------------------------------------------- */
/* EVENT											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'EVENT')
BEGIN
	 DECLARE @reftable_50 nvarchar(60), @constraintname_50 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'EVENT'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_50, @constraintname_50
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_50+' drop constraint '+@constraintname_50)
	   FETCH NEXT from refcursor into @reftable_50, @constraintname_50
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [EVENT]
END


CREATE TABLE [EVENT]
(
	[EVN_UID] VARCHAR(32) default '' NOT NULL,
	[PRO_UID] VARCHAR(32) default '' NOT NULL,
	[EVN_STATUS] VARCHAR(16) default 'OPEN' NOT NULL,
	[EVN_WHEN_OCCURS] VARCHAR(32) default 'SINGLE' NULL,
	[EVN_RELATED_TO] VARCHAR(16) default 'SINGLE' NULL,
	[TAS_UID] VARCHAR(32) default '' NOT NULL,
	[EVN_TAS_UID_FROM] VARCHAR(32) default '' NULL,
	[EVN_TAS_UID_TO] VARCHAR(32) default '' NULL,
	[EVN_TAS_ESTIMATED_DURATION] FLOAT default 0 NULL,
	[EVN_TIME_UNIT] VARCHAR(10) default 'DAYS' NOT NULL,
	[EVN_WHEN] FLOAT default 0 NOT NULL,
	[EVN_MAX_ATTEMPTS] TINYINT default 3 NOT NULL,
	[EVN_ACTION] VARCHAR(50) default '' NOT NULL,
	[EVN_CONDITIONS] NVARCHAR(MAX)  NULL,
	[EVN_ACTION_PARAMETERS] NVARCHAR(MAX)  NULL,
	[TRI_UID] VARCHAR(32) default '' NULL,
	[EVN_POSX] INT default 0 NOT NULL,
	[EVN_POSY] INT default 0 NOT NULL,
	[EVN_TYPE] VARCHAR(32) default '' NULL,
	[TAS_EVN_UID] VARCHAR(32) default '' NULL,
	CONSTRAINT EVENT_PK PRIMARY KEY ([EVN_UID])
);

CREATE INDEX [indexEventTable] ON [EVENT] ([EVN_UID]);

/* ---------------------------------------------------------------------- */
/* GATEWAY											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'GATEWAY')
BEGIN
	 DECLARE @reftable_51 nvarchar(60), @constraintname_51 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'GATEWAY'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_51, @constraintname_51
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_51+' drop constraint '+@constraintname_51)
	   FETCH NEXT from refcursor into @reftable_51, @constraintname_51
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [GATEWAY]
END


CREATE TABLE [GATEWAY]
(
	[GAT_UID] VARCHAR(32) default '' NOT NULL,
	[PRO_UID] VARCHAR(32) default '' NOT NULL,
	[TAS_UID] VARCHAR(32) default '' NOT NULL,
	[GAT_NEXT_TASK] VARCHAR(32) default '' NOT NULL,
	[GAT_X] INT default 0 NOT NULL,
	[GAT_Y] INT default 0 NOT NULL,
	[GAT_TYPE] VARCHAR(32) default '' NOT NULL,
	CONSTRAINT GATEWAY_PK PRIMARY KEY ([GAT_UID])
);

/* ---------------------------------------------------------------------- */
/* APP_EVENT											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'APP_EVENT')
BEGIN
	 DECLARE @reftable_52 nvarchar(60), @constraintname_52 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'APP_EVENT'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_52, @constraintname_52
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_52+' drop constraint '+@constraintname_52)
	   FETCH NEXT from refcursor into @reftable_52, @constraintname_52
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [APP_EVENT]
END


CREATE TABLE [APP_EVENT]
(
	[APP_UID] VARCHAR(32) default '' NOT NULL,
	[DEL_INDEX] INT default 0 NOT NULL,
	[EVN_UID] VARCHAR(32) default '' NOT NULL,
	[APP_EVN_ACTION_DATE] CHAR(19)  NOT NULL,
	[APP_EVN_ATTEMPTS] TINYINT default 0 NOT NULL,
	[APP_EVN_LAST_EXECUTION_DATE] CHAR(19)  NULL,
	[APP_EVN_STATUS] VARCHAR(32) default 'OPEN' NOT NULL,
	CONSTRAINT APP_EVENT_PK PRIMARY KEY ([APP_UID],[DEL_INDEX],[EVN_UID])
);

/* ---------------------------------------------------------------------- */
/* APP_CACHE_VIEW											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'APP_CACHE_VIEW')
BEGIN
	 DECLARE @reftable_53 nvarchar(60), @constraintname_53 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'APP_CACHE_VIEW'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_53, @constraintname_53
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_53+' drop constraint '+@constraintname_53)
	   FETCH NEXT from refcursor into @reftable_53, @constraintname_53
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [APP_CACHE_VIEW]
END


CREATE TABLE [APP_CACHE_VIEW]
(
	[APP_UID] VARCHAR(32) default '' NOT NULL,
	[DEL_INDEX] INT default 0 NOT NULL,
	[APP_NUMBER] INT default 0 NOT NULL,
	[APP_STATUS] VARCHAR(32) default '' NOT NULL,
	[USR_UID] VARCHAR(32) default '' NOT NULL,
	[PREVIOUS_USR_UID] VARCHAR(32) default '' NULL,
	[TAS_UID] VARCHAR(32) default '' NOT NULL,
	[PRO_UID] VARCHAR(32) default '' NOT NULL,
	[DEL_DELEGATE_DATE] CHAR(19)  NOT NULL,
	[DEL_INIT_DATE] CHAR(19)  NULL,
	[DEL_TASK_DUE_DATE] CHAR(19)  NULL,
	[DEL_FINISH_DATE] CHAR(19)  NULL,
	[DEL_THREAD_STATUS] VARCHAR(32) default 'OPEN' NULL,
	[APP_THREAD_STATUS] VARCHAR(32) default 'OPEN' NULL,
	[APP_TITLE] VARCHAR(255) default '' NOT NULL,
	[APP_PRO_TITLE] VARCHAR(255) default '' NOT NULL,
	[APP_TAS_TITLE] VARCHAR(255) default '' NOT NULL,
	[APP_CURRENT_USER] VARCHAR(128) default '' NULL,
	[APP_DEL_PREVIOUS_USER] VARCHAR(128) default '' NULL,
	[DEL_PRIORITY] VARCHAR(32) default '3' NOT NULL,
	[DEL_DURATION] FLOAT default 0 NULL,
	[DEL_QUEUE_DURATION] FLOAT default 0 NULL,
	[DEL_DELAY_DURATION] FLOAT default 0 NULL,
	[DEL_STARTED] TINYINT default 0 NOT NULL,
	[DEL_FINISHED] TINYINT default 0 NOT NULL,
	[DEL_DELAYED] TINYINT default 0 NOT NULL,
	[APP_CREATE_DATE] CHAR(19)  NOT NULL,
	[APP_FINISH_DATE] CHAR(19)  NULL,
	[APP_UPDATE_DATE] CHAR(19)  NOT NULL,
	[APP_OVERDUE_PERCENTAGE] FLOAT  NOT NULL,
	CONSTRAINT APP_CACHE_VIEW_PK PRIMARY KEY ([APP_UID],[DEL_INDEX])
);

CREATE INDEX [indexAppNumber] ON [APP_CACHE_VIEW] ([APP_NUMBER]);

CREATE INDEX [indexAppUser] ON [APP_CACHE_VIEW] ([USR_UID],[APP_STATUS]);

/* ---------------------------------------------------------------------- */
/* DIM_TIME_DELEGATE											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'DIM_TIME_DELEGATE')
BEGIN
	 DECLARE @reftable_54 nvarchar(60), @constraintname_54 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'DIM_TIME_DELEGATE'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_54, @constraintname_54
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_54+' drop constraint '+@constraintname_54)
	   FETCH NEXT from refcursor into @reftable_54, @constraintname_54
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [DIM_TIME_DELEGATE]
END


CREATE TABLE [DIM_TIME_DELEGATE]
(
	[TIME_ID] VARCHAR(10) default '' NOT NULL,
	[MONTH_ID] INT default 0 NOT NULL,
	[QTR_ID] INT default 0 NOT NULL,
	[YEAR_ID] INT default 0 NOT NULL,
	[MONTH_NAME] VARCHAR(3) default '0' NOT NULL,
	[MONTH_DESC] VARCHAR(9) default '' NOT NULL,
	[QTR_NAME] VARCHAR(4) default '' NOT NULL,
	[QTR_DESC] VARCHAR(9) default '' NOT NULL,
	CONSTRAINT DIM_TIME_DELEGATE_PK PRIMARY KEY ([TIME_ID])
);

/* ---------------------------------------------------------------------- */
/* DIM_TIME_COMPLETE											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'DIM_TIME_COMPLETE')
BEGIN
	 DECLARE @reftable_55 nvarchar(60), @constraintname_55 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'DIM_TIME_COMPLETE'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_55, @constraintname_55
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_55+' drop constraint '+@constraintname_55)
	   FETCH NEXT from refcursor into @reftable_55, @constraintname_55
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [DIM_TIME_COMPLETE]
END


CREATE TABLE [DIM_TIME_COMPLETE]
(
	[TIME_ID] VARCHAR(10) default '' NOT NULL,
	[MONTH_ID] INT default 0 NOT NULL,
	[QTR_ID] INT default 0 NOT NULL,
	[YEAR_ID] INT default 0 NOT NULL,
	[MONTH_NAME] VARCHAR(3) default '0' NOT NULL,
	[MONTH_DESC] VARCHAR(9) default '' NOT NULL,
	[QTR_NAME] VARCHAR(4) default '' NOT NULL,
	[QTR_DESC] VARCHAR(9) default '' NOT NULL,
	CONSTRAINT DIM_TIME_COMPLETE_PK PRIMARY KEY ([TIME_ID])
);

/* ---------------------------------------------------------------------- */
/* APP_HISTORY											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'APP_HISTORY')
BEGIN
	 DECLARE @reftable_56 nvarchar(60), @constraintname_56 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'APP_HISTORY'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_56, @constraintname_56
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_56+' drop constraint '+@constraintname_56)
	   FETCH NEXT from refcursor into @reftable_56, @constraintname_56
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [APP_HISTORY]
END


CREATE TABLE [APP_HISTORY]
(
	[APP_UID] VARCHAR(32) default '' NOT NULL,
	[DEL_INDEX] INT default 0 NOT NULL,
	[PRO_UID] VARCHAR(32) default '' NOT NULL,
	[TAS_UID] VARCHAR(32) default '' NOT NULL,
	[DYN_UID] VARCHAR(32) default '' NOT NULL,
	[USR_UID] VARCHAR(32) default '' NOT NULL,
	[APP_STATUS] VARCHAR(100) default '' NOT NULL,
	[HISTORY_DATE] CHAR(19)  NULL,
	[HISTORY_DATA] NVARCHAR(MAX)  NOT NULL
);

CREATE INDEX [indexAppHistory] ON [APP_HISTORY] ([APP_UID],[TAS_UID],[USR_UID]);

/* ---------------------------------------------------------------------- */
/* APP_FOLDER											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'APP_FOLDER')
BEGIN
	 DECLARE @reftable_57 nvarchar(60), @constraintname_57 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'APP_FOLDER'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_57, @constraintname_57
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_57+' drop constraint '+@constraintname_57)
	   FETCH NEXT from refcursor into @reftable_57, @constraintname_57
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [APP_FOLDER]
END


CREATE TABLE [APP_FOLDER]
(
	[FOLDER_UID] VARCHAR(32) default '' NOT NULL,
	[FOLDER_PARENT_UID] VARCHAR(32) default '' NOT NULL,
	[FOLDER_NAME] NVARCHAR(MAX)  NOT NULL,
	[FOLDER_CREATE_DATE] CHAR(19)  NOT NULL,
	[FOLDER_UPDATE_DATE] CHAR(19)  NOT NULL,
	CONSTRAINT APP_FOLDER_PK PRIMARY KEY ([FOLDER_UID])
);

/* ---------------------------------------------------------------------- */
/* FIELD_CONDITION											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'FIELD_CONDITION')
BEGIN
	 DECLARE @reftable_58 nvarchar(60), @constraintname_58 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'FIELD_CONDITION'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_58, @constraintname_58
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_58+' drop constraint '+@constraintname_58)
	   FETCH NEXT from refcursor into @reftable_58, @constraintname_58
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [FIELD_CONDITION]
END


CREATE TABLE [FIELD_CONDITION]
(
	[FCD_UID] VARCHAR(32) default '' NOT NULL,
	[FCD_FUNCTION] VARCHAR(50)  NOT NULL,
	[FCD_FIELDS] NVARCHAR(MAX)  NULL,
	[FCD_CONDITION] NVARCHAR(MAX)  NULL,
	[FCD_EVENTS] NVARCHAR(MAX)  NULL,
	[FCD_EVENT_OWNERS] NVARCHAR(MAX)  NULL,
	[FCD_STATUS] VARCHAR(10)  NULL,
	[FCD_DYN_UID] VARCHAR(32)  NOT NULL,
	CONSTRAINT FIELD_CONDITION_PK PRIMARY KEY ([FCD_UID])
);

/* ---------------------------------------------------------------------- */
/* LOG_CASES_SCHEDULER											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'LOG_CASES_SCHEDULER')
BEGIN
	 DECLARE @reftable_59 nvarchar(60), @constraintname_59 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'LOG_CASES_SCHEDULER'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_59, @constraintname_59
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_59+' drop constraint '+@constraintname_59)
	   FETCH NEXT from refcursor into @reftable_59, @constraintname_59
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [LOG_CASES_SCHEDULER]
END


CREATE TABLE [LOG_CASES_SCHEDULER]
(
	[LOG_CASE_UID] VARCHAR(32) default '' NOT NULL,
	[PRO_UID] VARCHAR(32) default '' NOT NULL,
	[TAS_UID] VARCHAR(32) default '' NOT NULL,
	[USR_NAME] VARCHAR(32) default '' NOT NULL,
	[EXEC_DATE] CHAR(19)  NOT NULL,
	[EXEC_HOUR] VARCHAR(32) default '12:00' NOT NULL,
	[RESULT] VARCHAR(32) default 'SUCCESS' NOT NULL,
	[SCH_UID] VARCHAR(32) default 'OPEN' NOT NULL,
	[WS_CREATE_CASE_STATUS] NVARCHAR(MAX)  NOT NULL,
	[WS_ROUTE_CASE_STATUS] NVARCHAR(MAX)  NOT NULL,
	CONSTRAINT LOG_CASES_SCHEDULER_PK PRIMARY KEY ([LOG_CASE_UID])
);

/* ---------------------------------------------------------------------- */
/* CASE_SCHEDULER											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'CASE_SCHEDULER')
BEGIN
	 DECLARE @reftable_60 nvarchar(60), @constraintname_60 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'CASE_SCHEDULER'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_60, @constraintname_60
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_60+' drop constraint '+@constraintname_60)
	   FETCH NEXT from refcursor into @reftable_60, @constraintname_60
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [CASE_SCHEDULER]
END


CREATE TABLE [CASE_SCHEDULER]
(
	[SCH_UID] VARCHAR(32)  NOT NULL,
	[SCH_DEL_USER_NAME] VARCHAR(100)  NOT NULL,
	[SCH_DEL_USER_PASS] VARCHAR(100)  NOT NULL,
	[SCH_DEL_USER_UID] VARCHAR(100)  NOT NULL,
	[SCH_NAME] VARCHAR(100)  NOT NULL,
	[PRO_UID] VARCHAR(32) default '' NOT NULL,
	[TAS_UID] VARCHAR(32) default '' NOT NULL,
	[SCH_TIME_NEXT_RUN] CHAR(19)  NOT NULL,
	[SCH_LAST_RUN_TIME] CHAR(19)  NULL,
	[SCH_STATE] VARCHAR(15) default 'ACTIVE' NOT NULL,
	[SCH_LAST_STATE] VARCHAR(60) default '' NOT NULL,
	[USR_UID] VARCHAR(32) default '' NOT NULL,
	[SCH_OPTION] TINYINT default 0 NOT NULL,
	[SCH_START_TIME] CHAR(19)  NOT NULL,
	[SCH_START_DATE] CHAR(19)  NOT NULL,
	[SCH_DAYS_PERFORM_TASK] CHAR(5) default '' NOT NULL,
	[SCH_EVERY_DAYS] TINYINT default 0 NULL,
	[SCH_WEEK_DAYS] CHAR(14) default '0|0|0|0|0|0|0' NOT NULL,
	[SCH_START_DAY] CHAR(6) default '' NOT NULL,
	[SCH_MONTHS] CHAR(24) default '0|0|0|0|0|0|0|0|0|0|0|0' NOT NULL,
	[SCH_END_DATE] CHAR(19)  NULL,
	[SCH_REPEAT_EVERY] VARCHAR(15) default '' NOT NULL,
	[SCH_REPEAT_UNTIL] VARCHAR(15) default '' NOT NULL,
	[SCH_REPEAT_STOP_IF_RUNNING] TINYINT default 0 NULL,
	[CASE_SH_PLUGIN_UID] VARCHAR(100)  NULL,
	CONSTRAINT CASE_SCHEDULER_PK PRIMARY KEY ([SCH_UID])
);

/* ---------------------------------------------------------------------- */
/* CALENDAR_DEFINITION											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'CALENDAR_DEFINITION')
BEGIN
	 DECLARE @reftable_61 nvarchar(60), @constraintname_61 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'CALENDAR_DEFINITION'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_61, @constraintname_61
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_61+' drop constraint '+@constraintname_61)
	   FETCH NEXT from refcursor into @reftable_61, @constraintname_61
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [CALENDAR_DEFINITION]
END


CREATE TABLE [CALENDAR_DEFINITION]
(
	[CALENDAR_UID] VARCHAR(32) default '' NOT NULL,
	[CALENDAR_NAME] VARCHAR(100) default '' NOT NULL,
	[CALENDAR_CREATE_DATE] CHAR(19)  NOT NULL,
	[CALENDAR_UPDATE_DATE] CHAR(19)  NULL,
	[CALENDAR_WORK_DAYS] VARCHAR(100) default '' NOT NULL,
	[CALENDAR_DESCRIPTION] NVARCHAR(MAX)  NOT NULL,
	[CALENDAR_STATUS] VARCHAR(8) default 'ACTIVE' NOT NULL,
	CONSTRAINT CALENDAR_DEFINITION_PK PRIMARY KEY ([CALENDAR_UID])
);

/* ---------------------------------------------------------------------- */
/* CALENDAR_BUSINESS_HOURS											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'CALENDAR_BUSINESS_HOURS')
BEGIN
	 DECLARE @reftable_62 nvarchar(60), @constraintname_62 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'CALENDAR_BUSINESS_HOURS'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_62, @constraintname_62
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_62+' drop constraint '+@constraintname_62)
	   FETCH NEXT from refcursor into @reftable_62, @constraintname_62
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [CALENDAR_BUSINESS_HOURS]
END


CREATE TABLE [CALENDAR_BUSINESS_HOURS]
(
	[CALENDAR_UID] VARCHAR(32) default '' NOT NULL,
	[CALENDAR_BUSINESS_DAY] VARCHAR(10) default '' NOT NULL,
	[CALENDAR_BUSINESS_START] VARCHAR(10) default '' NOT NULL,
	[CALENDAR_BUSINESS_END] VARCHAR(10) default '' NOT NULL,
	CONSTRAINT CALENDAR_BUSINESS_HOURS_PK PRIMARY KEY ([CALENDAR_UID],[CALENDAR_BUSINESS_DAY],[CALENDAR_BUSINESS_START],[CALENDAR_BUSINESS_END])
);

/* ---------------------------------------------------------------------- */
/* CALENDAR_HOLIDAYS											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'CALENDAR_HOLIDAYS')
BEGIN
	 DECLARE @reftable_63 nvarchar(60), @constraintname_63 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'CALENDAR_HOLIDAYS'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_63, @constraintname_63
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_63+' drop constraint '+@constraintname_63)
	   FETCH NEXT from refcursor into @reftable_63, @constraintname_63
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [CALENDAR_HOLIDAYS]
END


CREATE TABLE [CALENDAR_HOLIDAYS]
(
	[CALENDAR_UID] VARCHAR(32) default '' NOT NULL,
	[CALENDAR_HOLIDAY_NAME] VARCHAR(100) default '' NOT NULL,
	[CALENDAR_HOLIDAY_START] CHAR(19)  NOT NULL,
	[CALENDAR_HOLIDAY_END] CHAR(19)  NOT NULL,
	CONSTRAINT CALENDAR_HOLIDAYS_PK PRIMARY KEY ([CALENDAR_UID],[CALENDAR_HOLIDAY_NAME])
);

/* ---------------------------------------------------------------------- */
/* CALENDAR_ASSIGNMENTS											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'CALENDAR_ASSIGNMENTS')
BEGIN
	 DECLARE @reftable_64 nvarchar(60), @constraintname_64 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'CALENDAR_ASSIGNMENTS'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_64, @constraintname_64
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_64+' drop constraint '+@constraintname_64)
	   FETCH NEXT from refcursor into @reftable_64, @constraintname_64
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [CALENDAR_ASSIGNMENTS]
END


CREATE TABLE [CALENDAR_ASSIGNMENTS]
(
	[OBJECT_UID] VARCHAR(32) default '' NOT NULL,
	[CALENDAR_UID] VARCHAR(32) default '' NOT NULL,
	[OBJECT_TYPE] VARCHAR(100) default '' NOT NULL,
	CONSTRAINT CALENDAR_ASSIGNMENTS_PK PRIMARY KEY ([OBJECT_UID])
);

/* ---------------------------------------------------------------------- */
/* PROCESS_CATEGORY											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'PROCESS_CATEGORY')
BEGIN
	 DECLARE @reftable_65 nvarchar(60), @constraintname_65 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'PROCESS_CATEGORY'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_65, @constraintname_65
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_65+' drop constraint '+@constraintname_65)
	   FETCH NEXT from refcursor into @reftable_65, @constraintname_65
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [PROCESS_CATEGORY]
END


CREATE TABLE [PROCESS_CATEGORY]
(
	[CATEGORY_UID] VARCHAR(32) default '' NOT NULL,
	[CATEGORY_PARENT] VARCHAR(32) default '0' NOT NULL,
	[CATEGORY_NAME] VARCHAR(100) default '' NOT NULL,
	[CATEGORY_ICON] VARCHAR(100) default '' NULL,
	CONSTRAINT PROCESS_CATEGORY_PK PRIMARY KEY ([CATEGORY_UID])
);

/* ---------------------------------------------------------------------- */
/* APP_NOTES											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'APP_NOTES')
BEGIN
	 DECLARE @reftable_66 nvarchar(60), @constraintname_66 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'APP_NOTES'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_66, @constraintname_66
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_66+' drop constraint '+@constraintname_66)
	   FETCH NEXT from refcursor into @reftable_66, @constraintname_66
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [APP_NOTES]
END


CREATE TABLE [APP_NOTES]
(
	[APP_UID] VARCHAR(32) default '' NOT NULL,
	[USR_UID] VARCHAR(32) default '' NOT NULL,
	[NOTE_DATE] CHAR(19)  NOT NULL,
	[NOTE_CONTENT] NVARCHAR(MAX)  NOT NULL,
	[NOTE_TYPE] VARCHAR(32) default 'USER' NOT NULL,
	[NOTE_AVAILABILITY] VARCHAR(32) default 'PUBLIC' NOT NULL,
	[NOTE_ORIGIN_OBJ] VARCHAR(32) default '' NULL,
	[NOTE_AFFECTED_OBJ1] VARCHAR(32) default '' NULL,
	[NOTE_AFFECTED_OBJ2] VARCHAR(32) default '' NOT NULL,
	[NOTE_RECIPIENTS] NVARCHAR(MAX)  NULL
);

CREATE INDEX [indexAppNotesDate] ON [APP_NOTES] ([APP_UID],[NOTE_DATE]);

CREATE INDEX [indexAppNotesUser] ON [APP_NOTES] ([APP_UID],[USR_UID]);

/* ---------------------------------------------------------------------- */
/* DASHLET											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'DASHLET')
BEGIN
	 DECLARE @reftable_67 nvarchar(60), @constraintname_67 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'DASHLET'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_67, @constraintname_67
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_67+' drop constraint '+@constraintname_67)
	   FETCH NEXT from refcursor into @reftable_67, @constraintname_67
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [DASHLET]
END


CREATE TABLE [DASHLET]
(
	[DAS_UID] VARCHAR(32) default '' NOT NULL,
	[DAS_CLASS] VARCHAR(50) default '' NOT NULL,
	[DAS_TITLE] VARCHAR(255) default '' NOT NULL,
	[DAS_DESCRIPTION] NVARCHAR(MAX)  NULL,
	[DAS_VERSION] VARCHAR(10) default '1.0' NOT NULL,
	[DAS_CREATE_DATE] CHAR(19)  NOT NULL,
	[DAS_UPDATE_DATE] CHAR(19)  NULL,
	[DAS_STATUS] TINYINT default 1 NOT NULL,
	CONSTRAINT DASHLET_PK PRIMARY KEY ([DAS_UID])
);

/* ---------------------------------------------------------------------- */
/* DASHLET_INSTANCE											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'DASHLET_INSTANCE')
BEGIN
	 DECLARE @reftable_68 nvarchar(60), @constraintname_68 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'DASHLET_INSTANCE'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_68, @constraintname_68
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_68+' drop constraint '+@constraintname_68)
	   FETCH NEXT from refcursor into @reftable_68, @constraintname_68
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [DASHLET_INSTANCE]
END


CREATE TABLE [DASHLET_INSTANCE]
(
	[DAS_INS_UID] VARCHAR(32) default '' NOT NULL,
	[DAS_UID] VARCHAR(32) default '' NOT NULL,
	[DAS_INS_OWNER_TYPE] VARCHAR(20) default '' NOT NULL,
	[DAS_INS_OWNER_UID] VARCHAR(32) default '' NULL,
	[DAS_INS_ADDITIONAL_PROPERTIES] NVARCHAR(MAX)  NULL,
	[DAS_INS_CREATE_DATE] CHAR(19)  NOT NULL,
	[DAS_INS_UPDATE_DATE] CHAR(19)  NULL,
	[DAS_INS_STATUS] TINYINT default 1 NOT NULL,
	CONSTRAINT DASHLET_INSTANCE_PK PRIMARY KEY ([DAS_INS_UID])
);

/* ---------------------------------------------------------------------- */
/* APP_SOLR_QUEUE											*/
/* ---------------------------------------------------------------------- */


IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'APP_SOLR_QUEUE')
BEGIN
	 DECLARE @reftable_69 nvarchar(60), @constraintname_69 nvarchar(60)
	 DECLARE refcursor CURSOR FOR
	 select reftables.name tablename, cons.name constraintname
	  from sysobjects tables,
		   sysobjects reftables,
		   sysobjects cons,
		   sysreferences ref
	   where tables.id = ref.rkeyid
		 and cons.id = ref.constid
		 and reftables.id = ref.fkeyid
		 and tables.name = 'APP_SOLR_QUEUE'
	 OPEN refcursor
	 FETCH NEXT from refcursor into @reftable_69, @constraintname_69
	 while @@FETCH_STATUS = 0
	 BEGIN
	   exec ('alter table '+@reftable_69+' drop constraint '+@constraintname_69)
	   FETCH NEXT from refcursor into @reftable_69, @constraintname_69
	 END
	 CLOSE refcursor
	 DEALLOCATE refcursor
	 DROP TABLE [APP_SOLR_QUEUE]
END


CREATE TABLE [APP_SOLR_QUEUE]
(
	[APP_UID] VARCHAR(32) default '' NOT NULL,
 [APP_CHANGE_DATE] CHAR(19)  NOT NULL,
 [APP_CHANGE_TRACE] VARCHAR(500)  NOT NULL,
	[APP_UPDATED] TINYINT default 1 NOT NULL,
	CONSTRAINT APP_SOLR_QUEUE_PK PRIMARY KEY ([APP_UID])
);
