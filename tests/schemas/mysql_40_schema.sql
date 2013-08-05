#
# Do NOT manually edit this file!
#

# Table: 'phpbb_karma'
CREATE TABLE phpbb_karma (
	item_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	karma_type_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	giving_user_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	receiving_user_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	karma_score tinyint(4) DEFAULT '0' NOT NULL,
	karma_time int(11) UNSIGNED DEFAULT '0' NOT NULL,
	karma_comment blob NOT NULL,
	PRIMARY KEY (item_id, karma_type_id, giving_user_id)
);


# Table: 'phpbb_karma_types'
CREATE TABLE phpbb_karma_types (
	karma_type_id mediumint(8) UNSIGNED NOT NULL auto_increment,
	karma_type_name varbinary(255) DEFAULT '' NOT NULL,
	karma_type_enabled tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (karma_type_id)
);


