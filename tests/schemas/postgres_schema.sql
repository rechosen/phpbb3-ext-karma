/*

 Do NOT manually edit this file!

*/

BEGIN;


/*
	Table: 'phpbb_karma'
*/
CREATE TABLE phpbb_karma (
	post_id INT4 DEFAULT '0' NOT NULL CHECK (post_id >= 0),
	giving_user_id INT4 DEFAULT '0' NOT NULL CHECK (giving_user_id >= 0),
	receiving_user_id INT4 DEFAULT '0' NOT NULL CHECK (receiving_user_id >= 0),
	karma_score INT2 DEFAULT '0' NOT NULL,
	karma_time INT4 DEFAULT '0' NOT NULL CHECK (karma_time >= 0),
	karma_comment varchar(4000) DEFAULT '' NOT NULL
);



COMMIT;