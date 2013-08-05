#
# Do NOT manually edit this file!
#


# Table: 'phpbb_karma'
CREATE TABLE phpbb_karma (
	item_id INTEGER DEFAULT 0 NOT NULL,
	karma_type_id INTEGER DEFAULT 0 NOT NULL,
	giving_user_id INTEGER DEFAULT 0 NOT NULL,
	receiving_user_id INTEGER DEFAULT 0 NOT NULL,
	karma_score INTEGER DEFAULT 0 NOT NULL,
	karma_time INTEGER DEFAULT 0 NOT NULL,
	karma_comment BLOB SUB_TYPE TEXT CHARACTER SET UTF8 DEFAULT '' NOT NULL
);;

ALTER TABLE phpbb_karma ADD PRIMARY KEY (item_id, karma_type_id, giving_user_id);;


# Table: 'phpbb_karma_types'
CREATE TABLE phpbb_karma_types (
	karma_type_id INTEGER NOT NULL,
	karma_type_name VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	karma_type_enabled INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_karma_types ADD PRIMARY KEY (karma_type_id);;


CREATE GENERATOR phpbb_karma_types_gen;;
SET GENERATOR phpbb_karma_types_gen TO 0;;

CREATE TRIGGER t_phpbb_karma_types FOR phpbb_karma_types
BEFORE INSERT
AS
BEGIN
	NEW.karma_type_id = GEN_ID(phpbb_karma_types_gen, 1);
END;;


