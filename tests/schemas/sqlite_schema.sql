#
# Do NOT manually edit this file!
#

BEGIN TRANSACTION;

# Table: 'phpbb_karma'
CREATE TABLE phpbb_karma (
	item_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	karma_type_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	giving_user_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	receiving_user_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	karma_score tinyint(4) NOT NULL DEFAULT '0',
	karma_time INTEGER UNSIGNED NOT NULL DEFAULT '0',
	karma_comment text(65535) NOT NULL DEFAULT '',
	PRIMARY KEY (item_id, karma_type_id, giving_user_id)
);


# Table: 'phpbb_karma_types'
CREATE TABLE phpbb_karma_types (
	karma_type_id INTEGER PRIMARY KEY NOT NULL ,
	karma_type_name varchar(255) NOT NULL DEFAULT '',
	karma_type_enabled INTEGER UNSIGNED NOT NULL DEFAULT '0'
);



COMMIT;
