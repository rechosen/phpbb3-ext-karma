#
# Do NOT manually edit this file!
#


# Table: 'phpbb_karma'
CREATE TABLE phpbb_karma (
	post_id INTEGER DEFAULT 0 NOT NULL,
	giving_user_id INTEGER DEFAULT 0 NOT NULL,
	receiving_user_id INTEGER DEFAULT 0 NOT NULL,
	karma_score INTEGER DEFAULT 0 NOT NULL,
	karma_time INTEGER DEFAULT 0 NOT NULL,
	karma_comment BLOB SUB_TYPE TEXT CHARACTER SET UTF8 DEFAULT '' NOT NULL
);;


