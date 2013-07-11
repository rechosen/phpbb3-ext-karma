/*

 Do NOT manually edit this file!

*/


/*
	Table: 'phpbb_karma'
*/
CREATE TABLE phpbb_karma (
	post_id number(8) DEFAULT '0' NOT NULL,
	giving_user_id number(8) DEFAULT '0' NOT NULL,
	receiving_user_id number(8) DEFAULT '0' NOT NULL,
	karma_score number(4) DEFAULT '0' NOT NULL,
	karma_time number(11) DEFAULT '0' NOT NULL,
	karma_comment clob DEFAULT '' 
)
/


