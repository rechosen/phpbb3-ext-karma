/*

 Do NOT manually edit this file!

*/


/*
	Table: 'phpbb_karma'
*/
CREATE TABLE phpbb_karma (
	item_id number(8) DEFAULT '0' NOT NULL,
	karma_type_id number(8) DEFAULT '0' NOT NULL,
	giving_user_id number(8) DEFAULT '0' NOT NULL,
	receiving_user_id number(8) DEFAULT '0' NOT NULL,
	karma_score number(4) DEFAULT '0' NOT NULL,
	karma_time number(11) DEFAULT '0' NOT NULL,
	karma_comment clob DEFAULT '' ,
	CONSTRAINT pk_phpbb_karma PRIMARY KEY (item_id, karma_type_id, giving_user_id)
)
/


/*
	Table: 'phpbb_karma_types'
*/
CREATE TABLE phpbb_karma_types (
	karma_type_id number(8) NOT NULL,
	karma_type_name varchar2(255) DEFAULT '' ,
	karma_type_enabled number(1) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_karma_types PRIMARY KEY (karma_type_id)
)
/


CREATE SEQUENCE phpbb_karma_types_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_karma_types
BEFORE INSERT ON phpbb_karma_types
FOR EACH ROW WHEN (
	new.karma_type_id IS NULL OR new.karma_type_id = 0
)
BEGIN
	SELECT phpbb_karma_types_seq.nextval
	INTO :new.karma_type_id
	FROM dual;
END;
/


