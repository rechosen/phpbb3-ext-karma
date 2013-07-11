#
# Do NOT manually edit this file!
#

BEGIN TRANSACTION;

# Table: 'phpbb_karma'
CREATE TABLE phpbb_karma (
	post_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	giving_user_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	receiving_user_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	karma_score tinyint(4) NOT NULL DEFAULT '0',
	karma_time INTEGER UNSIGNED NOT NULL DEFAULT '0',
	karma_comment mediumtext(16777215) NOT NULL DEFAULT ''
);



COMMIT;