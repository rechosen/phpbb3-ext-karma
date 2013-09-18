/*

 Do NOT manually edit this file!

*/

BEGIN;


/*
	Table: 'phpbb_karma'
*/
CREATE TABLE phpbb_karma (
	item_id INT4 DEFAULT '0' NOT NULL CHECK (item_id >= 0),
	karma_type_id INT4 DEFAULT '0' NOT NULL CHECK (karma_type_id >= 0),
	giving_user_id INT4 DEFAULT '0' NOT NULL CHECK (giving_user_id >= 0),
	receiving_user_id INT4 DEFAULT '0' NOT NULL CHECK (receiving_user_id >= 0),
	karma_score INT2 DEFAULT '0' NOT NULL,
	karma_time INT4 DEFAULT '0' NOT NULL CHECK (karma_time >= 0),
	karma_comment varchar(4000) DEFAULT '' NOT NULL,
	PRIMARY KEY (item_id, karma_type_id, giving_user_id)
);


/*
	Table: 'phpbb_karma_types'
*/
CREATE SEQUENCE phpbb_karma_types_seq;

CREATE TABLE phpbb_karma_types (
	karma_type_id INT4 DEFAULT nextval('phpbb_karma_types_seq'),
	karma_type_name varchar(255) DEFAULT '' NOT NULL,
	karma_type_enabled INT2 DEFAULT '0' NOT NULL CHECK (karma_type_enabled >= 0),
	PRIMARY KEY (karma_type_id)
);



COMMIT;
