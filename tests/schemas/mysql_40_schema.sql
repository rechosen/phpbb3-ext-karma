#
# Do NOT manually edit this file!
#

# Table: 'phpbb_gallery_albums'
CREATE TABLE phpbb_gallery_albums (
	album_id mediumint(8) UNSIGNED NOT NULL auto_increment,
	parent_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	left_id mediumint(8) UNSIGNED DEFAULT '1' NOT NULL,
	right_id mediumint(8) UNSIGNED DEFAULT '2' NOT NULL,
	user_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	album_parents mediumblob NOT NULL,
	album_type varbinary(255) DEFAULT '' NOT NULL,
	album_status int(1) UNSIGNED DEFAULT '1' NOT NULL,
	album_contest mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	album_name varbinary(255) DEFAULT '' NOT NULL,
	album_desc mediumblob NOT NULL,
	album_desc_options int(3) UNSIGNED DEFAULT '7' NOT NULL,
	album_desc_uid varbinary(8) DEFAULT '' NOT NULL,
	album_desc_bitfield varbinary(255) DEFAULT '' NOT NULL,
	album_images mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	album_images_real mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	album_last_image_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	album_image varbinary(255) DEFAULT '' NOT NULL,
	album_last_image_time int(11) DEFAULT '0' NOT NULL,
	album_last_image_name varbinary(255) DEFAULT '' NOT NULL,
	album_last_username varbinary(255) DEFAULT '' NOT NULL,
	album_last_user_colour varbinary(6) DEFAULT '' NOT NULL,
	album_last_user_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	album_watermark int(1) UNSIGNED DEFAULT '1' NOT NULL,
	album_sort_key varbinary(8) DEFAULT '' NOT NULL,
	album_sort_dir varbinary(8) DEFAULT '' NOT NULL,
	display_in_rrc int(1) UNSIGNED DEFAULT '1' NOT NULL,
	display_on_index int(1) UNSIGNED DEFAULT '1' NOT NULL,
	display_subalbum_list int(1) UNSIGNED DEFAULT '1' NOT NULL,
	PRIMARY KEY (album_id)
);


# Table: 'phpbb_gallery_albums_track'
CREATE TABLE phpbb_gallery_albums_track (
	user_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	album_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	mark_time int(11) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (user_id, album_id)
);


# Table: 'phpbb_gallery_comments'
CREATE TABLE phpbb_gallery_comments (
	comment_id mediumint(8) UNSIGNED NOT NULL auto_increment,
	comment_image_id mediumint(8) UNSIGNED NOT NULL,
	comment_user_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	comment_username varbinary(255) DEFAULT '' NOT NULL,
	comment_user_colour varbinary(6) DEFAULT '' NOT NULL,
	comment_user_ip varbinary(40) DEFAULT '' NOT NULL,
	comment_time int(11) UNSIGNED DEFAULT '0' NOT NULL,
	comment mediumblob NOT NULL,
	comment_uid varbinary(8) DEFAULT '' NOT NULL,
	comment_bitfield varbinary(255) DEFAULT '' NOT NULL,
	comment_edit_time int(11) UNSIGNED DEFAULT '0' NOT NULL,
	comment_edit_count smallint(4) UNSIGNED DEFAULT '0' NOT NULL,
	comment_edit_user_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (comment_id),
	KEY comment_image_id (comment_image_id),
	KEY comment_user_id (comment_user_id),
	KEY comment_user_ip (comment_user_ip),
	KEY comment_time (comment_time)
);


# Table: 'phpbb_gallery_config'
CREATE TABLE phpbb_gallery_config (
	config_name varbinary(255) DEFAULT '' NOT NULL,
	config_value varbinary(255) DEFAULT '' NOT NULL,
	PRIMARY KEY (config_name)
);


# Table: 'phpbb_gallery_contests'
CREATE TABLE phpbb_gallery_contests (
	contest_id mediumint(8) UNSIGNED NOT NULL auto_increment,
	contest_album_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	contest_start int(11) UNSIGNED DEFAULT '0' NOT NULL,
	contest_rating int(11) UNSIGNED DEFAULT '0' NOT NULL,
	contest_end int(11) UNSIGNED DEFAULT '0' NOT NULL,
	contest_marked tinyint(1) DEFAULT '0' NOT NULL,
	contest_first mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	contest_second mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	contest_third mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (contest_id)
);


# Table: 'phpbb_gallery_copyts_albums'
CREATE TABLE phpbb_gallery_copyts_albums (
	album_id mediumint(8) UNSIGNED NOT NULL auto_increment,
	parent_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	left_id mediumint(8) UNSIGNED DEFAULT '1' NOT NULL,
	right_id mediumint(8) UNSIGNED DEFAULT '2' NOT NULL,
	album_name varbinary(255) DEFAULT '' NOT NULL,
	album_desc mediumblob NOT NULL,
	album_user_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (album_id)
);


# Table: 'phpbb_gallery_copyts_users'
CREATE TABLE phpbb_gallery_copyts_users (
	user_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	personal_album_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (user_id)
);


# Table: 'phpbb_gallery_favorites'
CREATE TABLE phpbb_gallery_favorites (
	favorite_id mediumint(8) UNSIGNED NOT NULL auto_increment,
	user_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	image_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (favorite_id),
	KEY user_id (user_id),
	KEY image_id (image_id)
);


# Table: 'phpbb_gallery_images'
CREATE TABLE phpbb_gallery_images (
	image_id mediumint(8) UNSIGNED NOT NULL auto_increment,
	image_filename varbinary(255) DEFAULT '' NOT NULL,
	image_thumbnail varbinary(255) DEFAULT '' NOT NULL,
	image_name varbinary(255) DEFAULT '' NOT NULL,
	image_name_clean varbinary(255) DEFAULT '' NOT NULL,
	image_desc mediumblob NOT NULL,
	image_desc_uid varbinary(8) DEFAULT '' NOT NULL,
	image_desc_bitfield varbinary(255) DEFAULT '' NOT NULL,
	image_user_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	image_username varbinary(255) DEFAULT '' NOT NULL,
	image_username_clean varbinary(255) DEFAULT '' NOT NULL,
	image_user_colour varbinary(6) DEFAULT '' NOT NULL,
	image_user_ip varbinary(40) DEFAULT '' NOT NULL,
	image_time int(11) UNSIGNED DEFAULT '0' NOT NULL,
	image_album_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	image_view_count int(11) UNSIGNED DEFAULT '0' NOT NULL,
	image_status int(3) UNSIGNED DEFAULT '0' NOT NULL,
	image_contest int(1) UNSIGNED DEFAULT '0' NOT NULL,
	image_contest_end int(11) UNSIGNED DEFAULT '0' NOT NULL,
	image_contest_rank int(3) UNSIGNED DEFAULT '0' NOT NULL,
	image_filemissing int(3) UNSIGNED DEFAULT '0' NOT NULL,
	image_has_exif int(3) UNSIGNED DEFAULT '2' NOT NULL,
	image_exif_data blob NOT NULL,
	image_rates mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	image_rate_points mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	image_rate_avg mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	image_comments mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	image_last_comment mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	image_favorited mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	image_reported mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	filesize_upload int(20) UNSIGNED DEFAULT '0' NOT NULL,
	filesize_medium int(20) UNSIGNED DEFAULT '0' NOT NULL,
	filesize_cache int(20) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (image_id),
	KEY image_album_id (image_album_id),
	KEY image_user_id (image_user_id),
	KEY image_time (image_time)
);


# Table: 'phpbb_gallery_modscache'
CREATE TABLE phpbb_gallery_modscache (
	album_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	user_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	username varbinary(255) DEFAULT '' NOT NULL,
	group_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	group_name varbinary(255) DEFAULT '' NOT NULL,
	display_on_index tinyint(1) DEFAULT '1' NOT NULL,
	KEY disp_idx (display_on_index),
	KEY album_id (album_id)
);


# Table: 'phpbb_gallery_permissions'
CREATE TABLE phpbb_gallery_permissions (
	perm_id mediumint(8) UNSIGNED NOT NULL auto_increment,
	perm_role_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	perm_album_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	perm_user_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	perm_group_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	perm_system int(3) DEFAULT '0' NOT NULL,
	PRIMARY KEY (perm_id)
);


# Table: 'phpbb_gallery_rates'
CREATE TABLE phpbb_gallery_rates (
	rate_image_id mediumint(8) UNSIGNED NOT NULL auto_increment,
	rate_user_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	rate_user_ip varbinary(40) DEFAULT '' NOT NULL,
	rate_point int(3) UNSIGNED DEFAULT '0' NOT NULL,
	KEY rate_image_id (rate_image_id),
	KEY rate_user_id (rate_user_id),
	KEY rate_user_ip (rate_user_ip),
	KEY rate_point (rate_point)
);


# Table: 'phpbb_gallery_reports'
CREATE TABLE phpbb_gallery_reports (
	report_id mediumint(8) UNSIGNED NOT NULL auto_increment,
	report_album_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	report_image_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	reporter_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	report_manager mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	report_note mediumblob NOT NULL,
	report_time int(11) UNSIGNED DEFAULT '0' NOT NULL,
	report_status int(3) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (report_id)
);


# Table: 'phpbb_gallery_roles'
CREATE TABLE phpbb_gallery_roles (
	role_id mediumint(8) UNSIGNED NOT NULL auto_increment,
	a_list int(3) UNSIGNED DEFAULT '0' NOT NULL,
	i_view int(3) UNSIGNED DEFAULT '0' NOT NULL,
	i_watermark int(3) UNSIGNED DEFAULT '0' NOT NULL,
	i_upload int(3) UNSIGNED DEFAULT '0' NOT NULL,
	i_edit int(3) UNSIGNED DEFAULT '0' NOT NULL,
	i_delete int(3) UNSIGNED DEFAULT '0' NOT NULL,
	i_rate int(3) UNSIGNED DEFAULT '0' NOT NULL,
	i_approve int(3) UNSIGNED DEFAULT '0' NOT NULL,
	i_lock int(3) UNSIGNED DEFAULT '0' NOT NULL,
	i_report int(3) UNSIGNED DEFAULT '0' NOT NULL,
	i_count mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	i_unlimited int(3) UNSIGNED DEFAULT '0' NOT NULL,
	c_read int(3) UNSIGNED DEFAULT '0' NOT NULL,
	c_post int(3) UNSIGNED DEFAULT '0' NOT NULL,
	c_edit int(3) UNSIGNED DEFAULT '0' NOT NULL,
	c_delete int(3) UNSIGNED DEFAULT '0' NOT NULL,
	m_comments int(3) UNSIGNED DEFAULT '0' NOT NULL,
	m_delete int(3) UNSIGNED DEFAULT '0' NOT NULL,
	m_edit int(3) UNSIGNED DEFAULT '0' NOT NULL,
	m_move int(3) UNSIGNED DEFAULT '0' NOT NULL,
	m_report int(3) UNSIGNED DEFAULT '0' NOT NULL,
	m_status int(3) UNSIGNED DEFAULT '0' NOT NULL,
	album_count mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	album_unlimited int(3) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (role_id)
);


# Table: 'phpbb_gallery_users'
CREATE TABLE phpbb_gallery_users (
	user_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	watch_own int(3) UNSIGNED DEFAULT '0' NOT NULL,
	watch_favo int(3) UNSIGNED DEFAULT '0' NOT NULL,
	watch_com int(3) UNSIGNED DEFAULT '0' NOT NULL,
	user_images mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	personal_album_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	user_lastmark int(11) UNSIGNED DEFAULT '0' NOT NULL,
	user_last_update int(11) UNSIGNED DEFAULT '0' NOT NULL,
	user_viewexif int(1) UNSIGNED DEFAULT '0' NOT NULL,
	user_permissions mediumblob NOT NULL,
	PRIMARY KEY (user_id)
);


# Table: 'phpbb_gallery_watch'
CREATE TABLE phpbb_gallery_watch (
	watch_id mediumint(8) UNSIGNED NOT NULL auto_increment,
	album_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	image_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	user_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (watch_id),
	KEY user_id (user_id),
	KEY image_id (image_id),
	KEY album_id (album_id)
);


