/*==============================================================*/
/* Nom de SGBD :  PostgreSQL 8                                  */
/* Date de cr�ation :  12/04/2010 22:56:04                      */
/*==============================================================*/


drop index COMMENT2_FK;

drop index COMMENT_FK;

drop index COMMENT_PK;

drop table COMMENT;

drop index CONTEST_PK;

drop table CONTEST;

drop index SEND_DEDICACE_FK;

drop index DEDICACE_PK;

drop table DEDICACE;

drop index FAVORITE2_FK;

drop index FAVORITE_FK;

drop index FAVORITE_PK;

drop table FAVORITE;

drop index CONTAINS_FK;

drop index MESSAGE_PK;

drop table MESSAGE;

drop index PARTICIPE2_FK;

drop index PARTICIPE_FK;

drop index PARTICIPE_PK;

drop table PARTICIPE;

drop index UPLOAD_FK;

drop index PHOTO_PK;

drop table PHOTO;

drop index RECEIVE2_FK;

drop index RECEIVE_FK;

drop index RECEIVE_PK;

drop table RECEIVE;

drop index SUPER_MESSAGES_PK;

drop table SUPER_MESSAGES;

drop index START_FK;

drop index WITH_FK;

drop index THREAD_PK;

drop table THREAD;

drop index USER_PK;

drop table "USER";

drop index VISIT2_FK;

drop index VISIT_FK;

drop index VISIT_PK;

drop table VISIT;

drop index FOR_FK;

drop index VOTE_FK;

drop index VOTE_PK;

drop table VOTE;

/*==============================================================*/
/* Table : ARCHIVE                                              */
/*==============================================================*/
create table ARCHIVE (
  ARCHIVE_ID           SERIAL               not null,
  MONTH                INT4          		not null,
  YEAR		           INT4                 not null,
  USER_ID	           INT8                 not null,
  PHOTO_ID             INT8                 not null,
  constraint PK_ARCHIVE primary key (ARCHIVE_ID)
);

/*==============================================================*/
/* Index : ARCHIVE_PK                                           */
/*==============================================================*/
create unique index ARCHIVE_PK on ARCHIVE (
ARCHIVE_ID
);

/*==============================================================*/
/* Table : COMMENT                                              */
/*==============================================================*/
create table COMMENT (
  COMMENT_ID 		   SERIAL				not null,
  USER_ID              INT4                 not null,
  USE_USER_ID          INT4                 not null,
  MESSAGE              VARCHAR(1024)        null,
  DATE                 DATE                 null,
  constraint PK_COMMENT primary key (COMMENT_ID)
);

/*==============================================================*/
/* Index : COMMENT_PK                                           */
/*==============================================================*/
create unique index COMMENT_PK on COMMENT (
COMMENT_ID
);

/*==============================================================*/
/* Index : COMMENT_FK                                           */
/*==============================================================*/
create  index COMMENT_FK on COMMENT (
USER_ID
);

/*==============================================================*/
/* Index : COMMENT2_FK                                          */
/*==============================================================*/
create  index COMMENT2_FK on COMMENT (
USE_USER_ID
);

/*==============================================================*/
/* Table : CONTEST                                              */
/*==============================================================*/
create table CONTEST (
  CONTEST_ID           SERIAL                 not null,
  TITLE                VARCHAR(50)          null,
  DESCRIPTION          TEXT                 null,
  DATE_BEGIN           DATE                 null,
  DATE_END             DATE                 null,
  constraint PK_CONTEST primary key (CONTEST_ID)
);

/*==============================================================*/
/* Index : CONTEST_PK                                           */
/*==============================================================*/
create unique index CONTEST_PK on CONTEST (
CONTEST_ID
);

/*==============================================================*/
/* Table : DEDICACE                                             */
/*==============================================================*/
create table DEDICACE (
  DEDICACE_ID          SERIAL               not null,
  USER_ID              INT4                 not null,
  CONTENT              TEXT                 null,
  DATE_END             DATE                 null,
  constraint PK_DEDICACE primary key (DEDICACE_ID)
);

/*==============================================================*/
/* Index : DEDICACE_PK                                          */
/*==============================================================*/
create unique index DEDICACE_PK on DEDICACE (
DEDICACE_ID
);

/*==============================================================*/
/* Index : SEND_DEDICACE_FK                                     */
/*==============================================================*/
create  index SEND_DEDICACE_FK on DEDICACE (
USER_ID
);

/*==============================================================*/
/* Table : FAVORITE                                             */
/*==============================================================*/
create table FAVORITE (
  USER_ID              INT4                 not null,
  USE_USER_ID          INT4                 not null,
  constraint PK_FAVORITE primary key (USER_ID, USE_USER_ID)
);

/*==============================================================*/
/* Index : FAVORITE_PK                                          */
/*==============================================================*/
create unique index FAVORITE_PK on FAVORITE (
USER_ID, USE_USER_ID
);

/*==============================================================*/
/* Index : FAVORITE_FK                                          */
/*==============================================================*/
create  index FAVORITE_FK on FAVORITE (
USER_ID
);

/*==============================================================*/
/* Index : FAVORITE2_FK                                         */
/*==============================================================*/
create  index FAVORITE2_FK on FAVORITE (
USE_USER_ID
);

/*==============================================================*/
/* Table : MESSAGE                                              */
/*==============================================================*/
create table MESSAGE (
  MESSAGE_ID           SERIAL              	not null,
  THREAD_ID            INT4                 not null,
  USER_ID			   INT4					not null,
  CONTENT              TEXT                 null,
  DATE                 DATE                 null,
  constraint PK_MESSAGE primary key (MESSAGE_ID)
);

/*==============================================================*/
/* Index : MESSAGE_PK                                           */
/*==============================================================*/
create unique index MESSAGE_PK on MESSAGE (
MESSAGE_ID
);

/*==============================================================*/
/* Index : CONTAINS_FK                                          */
/*==============================================================*/
create  index CONTAINS_FK on MESSAGE (
THREAD_ID
);

/*==============================================================*/
/* Index : POSTED_FK                                          */
/*==============================================================*/
create  index POSTED_FK on MESSAGE (
USER_ID
);

/*==============================================================*/
/* Table : PARTICIPE                                            */
/*==============================================================*/
create table PARTICIPE (
  USER_ID              INT4                 not null,
  CONTEST_ID           INT2                 not null,
  VOTE                 INT4                 null,
  constraint PK_PARTICIPE primary key (USER_ID, CONTEST_ID)
);

/*==============================================================*/
/* Index : PARTICIPE_PK                                         */
/*==============================================================*/
create unique index PARTICIPE_PK on PARTICIPE (
USER_ID,
CONTEST_ID
);

/*==============================================================*/
/* Index : PARTICIPE_FK                                         */
/*==============================================================*/
create  index PARTICIPE_FK on PARTICIPE (
USER_ID
);

/*==============================================================*/
/* Index : PARTICIPE2_FK                                        */
/*==============================================================*/
create  index PARTICIPE2_FK on PARTICIPE (
CONTEST_ID
);

/*==============================================================*/
/* Table : PHOTO                                                */
/*==============================================================*/
create table PHOTO (
  PHOTO_ID             SERIAL                 not null,
  USER_ID              INT4                 not null,
  VALIDATE             BOOL                 null,
  DATE                 DATE                 null,
  constraint PK_PHOTO primary key (PHOTO_ID)
);

/*==============================================================*/
/* Index : PHOTO_PK                                             */
/*==============================================================*/
create unique index PHOTO_PK on PHOTO (
PHOTO_ID
);

/*==============================================================*/
/* Index : UPLOAD_FK                                            */
/*==============================================================*/
create  index UPLOAD_FK on PHOTO (
USER_ID
);

/*==============================================================*/
/* Table : RECEIVE                                              */
/*==============================================================*/
create table RECEIVE (
  SUPER_ID             INT4                 not null,
  USER_ID              INT4                 not null,
  SUPER_READ           BOOL                 null,
  constraint PK_RECEIVE primary key (SUPER_ID, USER_ID)
);

/*==============================================================*/
/* Index : RECEIVE_PK                                           */
/*==============================================================*/
create unique index RECEIVE_PK on RECEIVE (
SUPER_ID,
USER_ID
);

/*==============================================================*/
/* Index : RECEIVE_FK                                           */
/*==============================================================*/
create  index RECEIVE_FK on RECEIVE (
SUPER_ID
);

/*==============================================================*/
/* Index : RECEIVE2_FK                                          */
/*==============================================================*/
create  index RECEIVE2_FK on RECEIVE (
USER_ID
);

/*==============================================================*/
/* Table : SUPER_MESSAGES                                       */
/*==============================================================*/
create table SUPER_MESSAGES (
  SUPER_ID             SERIAL                 not null,
  CONTENT              TEXT                 null,
  DATE                 DATE                 null,
  constraint PK_SUPER_MESSAGES primary key (SUPER_ID)
);

/*==============================================================*/
/* Index : SUPER_MESSAGES_PK                                    */
/*==============================================================*/
create unique index SUPER_MESSAGES_PK on SUPER_MESSAGES (
SUPER_ID
);

/*==============================================================*/
/* Table : THREAD                                               */
/*==============================================================*/
create table THREAD (
  THREAD_ID            SERIAL                not null,
  USER_ID              INT4                 not null,
  USE_USER_ID          INT4                 not null,
  SUBJECT              VARCHAR(50)          null,
  LAST_MESSAGE		   TIMESTAMP			null,	
  constraint PK_THREAD primary key (THREAD_ID)
);

/*==============================================================*/
/* Index : THREAD_PK                                            */
/*==============================================================*/
create unique index THREAD_PK on THREAD (
THREAD_ID
);

/*==============================================================*/
/* Table : THREAD_USER                                          */
/*==============================================================*/
create table THREAD_USER (
  THREAD_USER_ID	   SERIAL				not null,	
  THREAD_ID            INT4              	not null,
  USER_ID              INT4                 not null,
  READ				   BOOLEAN				not null,
  DELETED			   BOOLEAN				not null,	
  constraint PK_THREAD_USER primary key (THREAD_USER_ID)
);

/*==============================================================*/
/* Index : THREAD_USER_PK                                       */
/*==============================================================*/
create unique index THREAD_USER_PK on THREAD_USER (
THREAD_USER_ID
);

/*==============================================================*/
/* Index : THREAD_USER_FK                                       */
/*==============================================================*/
create index THREAD_USER_FK on THREAD_USER (
THREAD_ID
);

/*==============================================================*/
/* Index : THREAD_USER_FK2                                      */
/*==============================================================*/
create index THREAD_USER_FK2 on THREAD_USER (
USER_ID
);

/*==============================================================*/
/* Index : WITH_FK                                              */
/*==============================================================*/
create  index WITH_FK on THREAD (
USER_ID
);

/*==============================================================*/
/* Index : START_FK                                             */
/*==============================================================*/
create  index START_FK on THREAD (
USE_USER_ID
);

/*==============================================================*/
/* Table : "USER"                                               */
/*==============================================================*/
create table "USER" (
  USER_ID              SERIAL               not null,
  FBID                 INT8                 null,
  NICKNAME             VARCHAR(20)          null,
  BIRTHDAY             DATE                 null,
  SEX                  BOOL                 null,
  "LIKE"               TEXT		            null,
  DISLIKE              TEXT			        null,
  BIO                  TEXT                 null,
  MINIATURE_ID         INT4                 null,
  POINTS			   INT4					default 0,
  VOTE				   INT4					default 0,
  ACTIVE			   BOOL					default true,
  END_VIP              DATE                 null,
  IS_VIP               BOOL                 null,
  IS_MODERATOR         BOOL                 null,
  IS_ADMIN             BOOL                 null,
  LAST_LOGIN		   TIMESTAMP			null,
  constraint PK_USER primary key (USER_ID)
);

/*==============================================================*/
/* Index : USER_PK                                              */
/*==============================================================*/
create unique index USER_PK on "USER" (
USER_ID
);

/*==============================================================*/
/* Table : VISIT                                               */
/*==============================================================*/
create table VISIT (
  VISIT_ID			   SERIAL				not null,	
  USE_USER_ID          INT4                 not null,
  USER_ID              INT4                 not null,
  VISIT_DATE           DATE                 null,
  constraint PK_VISIT primary key (VISIT_ID)
);

/*==============================================================*/
/* Index : VISIT_PK                                            */
/*==============================================================*/
create unique index VISIT_PK on VISIT (
VISIT_ID
);

/*==============================================================*/
/* Index : VISIT_FK                                            */
/*==============================================================*/
create  index VISIT_FK on VISIT (
USE_USER_ID
);

/*==============================================================*/
/* Index : VISIT2_FK                                           */
/*==============================================================*/
create  index VISIT2_FK on VISIT (
USER_ID
);

/*==============================================================*/
/* Table : VOTE                                                 */
/*==============================================================*/
create table VOTE (
  VOTE_ID              SERIAL               not null,
  USER_ID              INT4                 not null,
  USE_USER_ID          INT4                 not null,
  DATE                 DATE                 null,
  POINTS               INT2                 null,
  constraint PK_VOTE primary key (VOTE_ID)
);

/*==============================================================*/
/* Index : VOTE_PK                                              */
/*==============================================================*/
create unique index VOTE_PK on VOTE (
VOTE_ID
);

/*==============================================================*/
/* Index : VOTE_FK                                              */
/*==============================================================*/
create  index VOTE_FK on VOTE (
USER_ID
);

/*==============================================================*/
/* Index : FOR_FK                                               */
/*==============================================================*/
create  index FOR_FK on VOTE (
USE_USER_ID
);

alter table ARCHIVE
  add constraint FK_ARCHIVE_ARCHIVE_USER foreign key (USER_ID)
     references "USER" (USER_ID)
     on delete cascade on update restrict;

alter table ARCHIVE
  add constraint FK_ARCHIVE_ARHIVE_PHOTO foreign key (PHOTO_ID)
     references PHOTO (PHOTO_ID)
     on delete cascade on update restrict;

alter table COMMENT
  add constraint FK_COMMENT_COMMENT_USER foreign key (USER_ID)
     references "USER" (USER_ID)
     on delete restrict on update restrict;

alter table COMMENT
  add constraint FK_COMMENT_COMMENT2_USER foreign key (USE_USER_ID)
     references "USER" (USER_ID)
     on delete restrict on update restrict;

alter table DEDICACE
  add constraint FK_DEDICACE_SEND_DEDI_USER foreign key (USER_ID)
     references "USER" (USER_ID)
     on delete restrict on update restrict;

alter table FAVORITE
  add constraint FK_FAVORITE_FAVORITE_USER foreign key (USER_ID)
     references "USER" (USER_ID)
     on delete restrict on update restrict;

alter table FAVORITE
  add constraint FK_FAVORITE_FAVORITE2_USER foreign key (USE_USER_ID)
     references "USER" (USER_ID)
     on delete restrict on update restrict;

alter table MESSAGE
  add constraint FK_MESSAGE_CONTAINS_THREAD foreign key (THREAD_ID)
     references THREAD (THREAD_ID)
     on delete cascade on update restrict;

alter table MESSAGE
  add constraint FK_MESSAGE_POSTED_USER foreign key (USER_ID)
     references "USER" (USER_ID)
     on delete restrict on update restrict;

alter table PARTICIPE
  add constraint FK_PARTICIP_PARTICIPE_USER foreign key (USER_ID)
     references "USER" (USER_ID)
     on delete restrict on update restrict;

alter table PARTICIPE
  add constraint FK_PARTICIP_PARTICIPE_CONTEST foreign key (CONTEST_ID)
     references CONTEST (CONTEST_ID)
     on delete restrict on update restrict;

alter table PHOTO
  add constraint FK_PHOTO_UPLOAD_USER foreign key (USER_ID)
     references "USER" (USER_ID)
     on delete restrict on update restrict;

alter table RECEIVE
  add constraint FK_RECEIVE_RECEIVE_SUPER_ME foreign key (SUPER_ID)
     references SUPER_MESSAGES (SUPER_ID)
     on delete restrict on update restrict;

alter table RECEIVE
  add constraint FK_RECEIVE_RECEIVE2_USER foreign key (USER_ID)
     references "USER" (USER_ID)
     on delete restrict on update restrict;

alter table THREAD
  add constraint FK_THREAD_START_USER foreign key (USE_USER_ID)
     references "USER" (USER_ID)
     on delete restrict on update restrict;
     
alter table THREAD_USER
  add constraint FK_THREAD_USER_WITH_USER foreign key (USER_ID)
     references "USER" (USER_ID)
     on delete restrict on update restrict;
     
alter table THREAD_USER
  add constraint FK_THREAD_USER_WITH_THREAD foreign key (THREAD_ID)
     references THREAD (THREAD_ID)
     on delete cascade on update restrict;

alter table VISIT
  add constraint FK_VISIT_VISIT_USER foreign key (USE_USER_ID)
     references "USER" (USER_ID)
     on delete restrict on update restrict;

alter table VISIT
  add constraint FK_VISIT_VISIT2_USER foreign key (USER_ID)
     references "USER" (USER_ID)
     on delete restrict on update restrict;

alter table VOTE
  add constraint FK_VOTE_FOR_USER foreign key (USE_USER_ID)
     references "USER" (USER_ID)
     on delete restrict on update restrict;

alter table VOTE
  add constraint FK_VOTE_VOTE_USER foreign key (USER_ID)
     references "USER" (USER_ID)
     on delete restrict on update restrict;