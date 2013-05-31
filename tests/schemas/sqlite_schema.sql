#
# Do NOT manually edit this file!
#

BEGIN TRANSACTION;

# Table: 'phpbb_gallery_albums'
CREATE TABLE phpbb_gallery_albums (
	album_id INTEGER PRIMARY KEY NOT NULL ,
	parent_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	left_id INTEGER UNSIGNED NOT NULL DEFAULT '1',
	right_id INTEGER UNSIGNED NOT NULL DEFAULT '2',
	user_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	album_parents mediumtext(16777215) NOT NULL DEFAULT '',
	album_type varchar(255) NOT NULL DEFAULT '',
	album_status INTEGER UNSIGNED NOT NULL DEFAULT '1',
	album_contest INTEGER UNSIGNED NOT NULL DEFAULT '0',
	album_name varchar(255) NOT NULL DEFAULT '',
	album_desc mediumtext(16777215) NOT NULL DEFAULT '',
	album_desc_options INTEGER UNSIGNED NOT NULL DEFAULT '7',
	album_desc_uid varchar(8) NOT NULL DEFAULT '',
	album_desc_bitfield varchar(255) NOT NULL DEFAULT '',
	album_images INTEGER UNSIGNED NOT NULL DEFAULT '0',
	album_images_real INTEGER UNSIGNED NOT NULL DEFAULT '0',
	album_last_image_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	album_image varchar(255) NOT NULL DEFAULT '',
	album_last_image_time int(11) NOT NULL DEFAULT '0',
	album_last_image_name varchar(255) NOT NULL DEFAULT '',
	album_last_username varchar(255) NOT NULL DEFAULT '',
	album_last_user_colour varchar(6) NOT NULL DEFAULT '',
	album_last_user_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	album_watermark INTEGER UNSIGNED NOT NULL DEFAULT '1',
	album_sort_key varchar(8) NOT NULL DEFAULT '',
	album_sort_dir varchar(8) NOT NULL DEFAULT '',
	display_in_rrc INTEGER UNSIGNED NOT NULL DEFAULT '1',
	display_on_index INTEGER UNSIGNED NOT NULL DEFAULT '1',
	display_subalbum_list INTEGER UNSIGNED NOT NULL DEFAULT '1'
);


# Table: 'phpbb_gallery_albums_track'
CREATE TABLE phpbb_gallery_albums_track (
	user_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	album_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	mark_time INTEGER UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (user_id, album_id)
);


# Table: 'phpbb_gallery_comments'
CREATE TABLE phpbb_gallery_comments (
	comment_id INTEGER PRIMARY KEY NOT NULL ,
	comment_image_id INTEGER UNSIGNED NOT NULL ,
	comment_user_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	comment_username varchar(255) NOT NULL DEFAULT '',
	comment_user_colour varchar(6) NOT NULL DEFAULT '',
	comment_user_ip varchar(40) NOT NULL DEFAULT '',
	comment_time INTEGER UNSIGNED NOT NULL DEFAULT '0',
	comment mediumtext(16777215) NOT NULL DEFAULT '',
	comment_uid varchar(8) NOT NULL DEFAULT '',
	comment_bitfield varchar(255) NOT NULL DEFAULT '',
	comment_edit_time INTEGER UNSIGNED NOT NULL DEFAULT '0',
	comment_edit_count INTEGER UNSIGNED NOT NULL DEFAULT '0',
	comment_edit_user_id INTEGER UNSIGNED NOT NULL DEFAULT '0'
);

CREATE INDEX phpbb_gallery_comments_comment_image_id ON phpbb_gallery_comments (comment_image_id);
CREATE INDEX phpbb_gallery_comments_comment_user_id ON phpbb_gallery_comments (comment_user_id);
CREATE INDEX phpbb_gallery_comments_comment_user_ip ON phpbb_gallery_comments (comment_user_ip);
CREATE INDEX phpbb_gallery_comments_comment_time ON phpbb_gallery_comments (comment_time);

# Table: 'phpbb_gallery_config'
CREATE TABLE phpbb_gallery_config (
	config_name varchar(255) NOT NULL DEFAULT '',
	config_value varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (config_name)
);


# Table: 'phpbb_gallery_contests'
CREATE TABLE phpbb_gallery_contests (
	contest_id INTEGER PRIMARY KEY NOT NULL ,
	contest_album_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	contest_start INTEGER UNSIGNED NOT NULL DEFAULT '0',
	contest_rating INTEGER UNSIGNED NOT NULL DEFAULT '0',
	contest_end INTEGER UNSIGNED NOT NULL DEFAULT '0',
	contest_marked tinyint(1) NOT NULL DEFAULT '0',
	contest_first INTEGER UNSIGNED NOT NULL DEFAULT '0',
	contest_second INTEGER UNSIGNED NOT NULL DEFAULT '0',
	contest_third INTEGER UNSIGNED NOT NULL DEFAULT '0'
);


# Table: 'phpbb_gallery_copyts_albums'
CREATE TABLE phpbb_gallery_copyts_albums (
	album_id INTEGER PRIMARY KEY NOT NULL ,
	parent_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	left_id INTEGER UNSIGNED NOT NULL DEFAULT '1',
	right_id INTEGER UNSIGNED NOT NULL DEFAULT '2',
	album_name varchar(255) NOT NULL DEFAULT '',
	album_desc mediumtext(16777215) NOT NULL DEFAULT '',
	album_user_id INTEGER UNSIGNED NOT NULL DEFAULT '0'
);


# Table: 'phpbb_gallery_copyts_users'
CREATE TABLE phpbb_gallery_copyts_users (
	user_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	personal_album_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (user_id)
);


# Table: 'phpbb_gallery_favorites'
CREATE TABLE phpbb_gallery_favorites (
	favorite_id INTEGER PRIMARY KEY NOT NULL ,
	user_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	image_id INTEGER UNSIGNED NOT NULL DEFAULT '0'
);

CREATE INDEX phpbb_gallery_favorites_user_id ON phpbb_gallery_favorites (user_id);
CREATE INDEX phpbb_gallery_favorites_image_id ON phpbb_gallery_favorites (image_id);

# Table: 'phpbb_gallery_images'
CREATE TABLE phpbb_gallery_images (
	image_id INTEGER PRIMARY KEY NOT NULL ,
	image_filename varchar(255) NOT NULL DEFAULT '',
	image_thumbnail varchar(255) NOT NULL DEFAULT '',
	image_name varchar(255) NOT NULL DEFAULT '',
	image_name_clean varchar(255) NOT NULL DEFAULT '',
	image_desc mediumtext(16777215) NOT NULL DEFAULT '',
	image_desc_uid varchar(8) NOT NULL DEFAULT '',
	image_desc_bitfield varchar(255) NOT NULL DEFAULT '',
	image_user_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	image_username varchar(255) NOT NULL DEFAULT '',
	image_username_clean varchar(255) NOT NULL DEFAULT '',
	image_user_colour varchar(6) NOT NULL DEFAULT '',
	image_user_ip varchar(40) NOT NULL DEFAULT '',
	image_time INTEGER UNSIGNED NOT NULL DEFAULT '0',
	image_album_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	image_view_count INTEGER UNSIGNED NOT NULL DEFAULT '0',
	image_status INTEGER UNSIGNED NOT NULL DEFAULT '0',
	image_contest INTEGER UNSIGNED NOT NULL DEFAULT '0',
	image_contest_end INTEGER UNSIGNED NOT NULL DEFAULT '0',
	image_contest_rank INTEGER UNSIGNED NOT NULL DEFAULT '0',
	image_filemissing INTEGER UNSIGNED NOT NULL DEFAULT '0',
	image_has_exif INTEGER UNSIGNED NOT NULL DEFAULT '2',
	image_exif_data text(65535) NOT NULL DEFAULT '',
	image_rates INTEGER UNSIGNED NOT NULL DEFAULT '0',
	image_rate_points INTEGER UNSIGNED NOT NULL DEFAULT '0',
	image_rate_avg INTEGER UNSIGNED NOT NULL DEFAULT '0',
	image_comments INTEGER UNSIGNED NOT NULL DEFAULT '0',
	image_last_comment INTEGER UNSIGNED NOT NULL DEFAULT '0',
	image_favorited INTEGER UNSIGNED NOT NULL DEFAULT '0',
	image_reported INTEGER UNSIGNED NOT NULL DEFAULT '0',
	filesize_upload INTEGER UNSIGNED NOT NULL DEFAULT '0',
	filesize_medium INTEGER UNSIGNED NOT NULL DEFAULT '0',
	filesize_cache INTEGER UNSIGNED NOT NULL DEFAULT '0'
);

CREATE INDEX phpbb_gallery_images_image_album_id ON phpbb_gallery_images (image_album_id);
CREATE INDEX phpbb_gallery_images_image_user_id ON phpbb_gallery_images (image_user_id);
CREATE INDEX phpbb_gallery_images_image_time ON phpbb_gallery_images (image_time);

# Table: 'phpbb_gallery_modscache'
CREATE TABLE phpbb_gallery_modscache (
	album_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	user_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	username varchar(255) NOT NULL DEFAULT '',
	group_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	group_name varchar(255) NOT NULL DEFAULT '',
	display_on_index tinyint(1) NOT NULL DEFAULT '1'
);

CREATE INDEX phpbb_gallery_modscache_disp_idx ON phpbb_gallery_modscache (display_on_index);
CREATE INDEX phpbb_gallery_modscache_album_id ON phpbb_gallery_modscache (album_id);

# Table: 'phpbb_gallery_permissions'
CREATE TABLE phpbb_gallery_permissions (
	perm_id INTEGER PRIMARY KEY NOT NULL ,
	perm_role_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	perm_album_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	perm_user_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	perm_group_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	perm_system int(3) NOT NULL DEFAULT '0'
);


# Table: 'phpbb_gallery_rates'
CREATE TABLE phpbb_gallery_rates (
	rate_image_id INTEGER PRIMARY KEY NOT NULL ,
	rate_user_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	rate_user_ip varchar(40) NOT NULL DEFAULT '',
	rate_point INTEGER UNSIGNED NOT NULL DEFAULT '0'
);

CREATE INDEX phpbb_gallery_rates_rate_image_id ON phpbb_gallery_rates (rate_image_id);
CREATE INDEX phpbb_gallery_rates_rate_user_id ON phpbb_gallery_rates (rate_user_id);
CREATE INDEX phpbb_gallery_rates_rate_user_ip ON phpbb_gallery_rates (rate_user_ip);
CREATE INDEX phpbb_gallery_rates_rate_point ON phpbb_gallery_rates (rate_point);

# Table: 'phpbb_gallery_reports'
CREATE TABLE phpbb_gallery_reports (
	report_id INTEGER PRIMARY KEY NOT NULL ,
	report_album_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	report_image_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	reporter_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	report_manager INTEGER UNSIGNED NOT NULL DEFAULT '0',
	report_note mediumtext(16777215) NOT NULL DEFAULT '',
	report_time INTEGER UNSIGNED NOT NULL DEFAULT '0',
	report_status INTEGER UNSIGNED NOT NULL DEFAULT '0'
);


# Table: 'phpbb_gallery_roles'
CREATE TABLE phpbb_gallery_roles (
	role_id INTEGER PRIMARY KEY NOT NULL ,
	a_list INTEGER UNSIGNED NOT NULL DEFAULT '0',
	i_view INTEGER UNSIGNED NOT NULL DEFAULT '0',
	i_watermark INTEGER UNSIGNED NOT NULL DEFAULT '0',
	i_upload INTEGER UNSIGNED NOT NULL DEFAULT '0',
	i_edit INTEGER UNSIGNED NOT NULL DEFAULT '0',
	i_delete INTEGER UNSIGNED NOT NULL DEFAULT '0',
	i_rate INTEGER UNSIGNED NOT NULL DEFAULT '0',
	i_approve INTEGER UNSIGNED NOT NULL DEFAULT '0',
	i_lock INTEGER UNSIGNED NOT NULL DEFAULT '0',
	i_report INTEGER UNSIGNED NOT NULL DEFAULT '0',
	i_count INTEGER UNSIGNED NOT NULL DEFAULT '0',
	i_unlimited INTEGER UNSIGNED NOT NULL DEFAULT '0',
	c_read INTEGER UNSIGNED NOT NULL DEFAULT '0',
	c_post INTEGER UNSIGNED NOT NULL DEFAULT '0',
	c_edit INTEGER UNSIGNED NOT NULL DEFAULT '0',
	c_delete INTEGER UNSIGNED NOT NULL DEFAULT '0',
	m_comments INTEGER UNSIGNED NOT NULL DEFAULT '0',
	m_delete INTEGER UNSIGNED NOT NULL DEFAULT '0',
	m_edit INTEGER UNSIGNED NOT NULL DEFAULT '0',
	m_move INTEGER UNSIGNED NOT NULL DEFAULT '0',
	m_report INTEGER UNSIGNED NOT NULL DEFAULT '0',
	m_status INTEGER UNSIGNED NOT NULL DEFAULT '0',
	album_count INTEGER UNSIGNED NOT NULL DEFAULT '0',
	album_unlimited INTEGER UNSIGNED NOT NULL DEFAULT '0'
);


# Table: 'phpbb_gallery_users'
CREATE TABLE phpbb_gallery_users (
	user_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	watch_own INTEGER UNSIGNED NOT NULL DEFAULT '0',
	watch_favo INTEGER UNSIGNED NOT NULL DEFAULT '0',
	watch_com INTEGER UNSIGNED NOT NULL DEFAULT '0',
	user_images INTEGER UNSIGNED NOT NULL DEFAULT '0',
	personal_album_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	user_lastmark INTEGER UNSIGNED NOT NULL DEFAULT '0',
	user_last_update INTEGER UNSIGNED NOT NULL DEFAULT '0',
	user_viewexif INTEGER UNSIGNED NOT NULL DEFAULT '0',
	user_permissions mediumtext(16777215) NOT NULL DEFAULT '',
	PRIMARY KEY (user_id)
);


# Table: 'phpbb_gallery_watch'
CREATE TABLE phpbb_gallery_watch (
	watch_id INTEGER PRIMARY KEY NOT NULL ,
	album_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	image_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	user_id INTEGER UNSIGNED NOT NULL DEFAULT '0'
);

CREATE INDEX phpbb_gallery_watch_user_id ON phpbb_gallery_watch (user_id);
CREATE INDEX phpbb_gallery_watch_image_id ON phpbb_gallery_watch (image_id);
CREATE INDEX phpbb_gallery_watch_album_id ON phpbb_gallery_watch (album_id);


COMMIT;