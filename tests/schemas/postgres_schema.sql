/*

 Do NOT manually edit this file!

*/

BEGIN;


/*
	Table: 'phpbb_gallery_albums'
*/
CREATE SEQUENCE phpbb_gallery_albums_seq;

CREATE TABLE phpbb_gallery_albums (
	album_id INT4 DEFAULT nextval('phpbb_gallery_albums_seq'),
	parent_id INT4 DEFAULT '0' NOT NULL CHECK (parent_id >= 0),
	left_id INT4 DEFAULT '1' NOT NULL CHECK (left_id >= 0),
	right_id INT4 DEFAULT '2' NOT NULL CHECK (right_id >= 0),
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	album_parents TEXT DEFAULT '' NOT NULL,
	album_type varchar(255) DEFAULT '' NOT NULL,
	album_status INT4 DEFAULT '1' NOT NULL CHECK (album_status >= 0),
	album_contest INT4 DEFAULT '0' NOT NULL CHECK (album_contest >= 0),
	album_name varchar(255) DEFAULT '' NOT NULL,
	album_desc TEXT DEFAULT '' NOT NULL,
	album_desc_options INT4 DEFAULT '7' NOT NULL CHECK (album_desc_options >= 0),
	album_desc_uid varchar(8) DEFAULT '' NOT NULL,
	album_desc_bitfield varchar(255) DEFAULT '' NOT NULL,
	album_images INT4 DEFAULT '0' NOT NULL CHECK (album_images >= 0),
	album_images_real INT4 DEFAULT '0' NOT NULL CHECK (album_images_real >= 0),
	album_last_image_id INT4 DEFAULT '0' NOT NULL CHECK (album_last_image_id >= 0),
	album_image varchar(255) DEFAULT '' NOT NULL,
	album_last_image_time INT4 DEFAULT '0' NOT NULL,
	album_last_image_name varchar(255) DEFAULT '' NOT NULL,
	album_last_username varchar(255) DEFAULT '' NOT NULL,
	album_last_user_colour varchar(6) DEFAULT '' NOT NULL,
	album_last_user_id INT4 DEFAULT '0' NOT NULL CHECK (album_last_user_id >= 0),
	album_watermark INT4 DEFAULT '1' NOT NULL CHECK (album_watermark >= 0),
	album_sort_key varchar(8) DEFAULT '' NOT NULL,
	album_sort_dir varchar(8) DEFAULT '' NOT NULL,
	display_in_rrc INT4 DEFAULT '1' NOT NULL CHECK (display_in_rrc >= 0),
	display_on_index INT4 DEFAULT '1' NOT NULL CHECK (display_on_index >= 0),
	display_subalbum_list INT4 DEFAULT '1' NOT NULL CHECK (display_subalbum_list >= 0),
	PRIMARY KEY (album_id)
);


/*
	Table: 'phpbb_gallery_albums_track'
*/
CREATE TABLE phpbb_gallery_albums_track (
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	album_id INT4 DEFAULT '0' NOT NULL CHECK (album_id >= 0),
	mark_time INT4 DEFAULT '0' NOT NULL CHECK (mark_time >= 0),
	PRIMARY KEY (user_id, album_id)
);


/*
	Table: 'phpbb_gallery_comments'
*/
CREATE SEQUENCE phpbb_gallery_comments_seq;

CREATE TABLE phpbb_gallery_comments (
	comment_id INT4 DEFAULT nextval('phpbb_gallery_comments_seq'),
	comment_image_id INT4 NOT NULL CHECK (comment_image_id >= 0),
	comment_user_id INT4 DEFAULT '0' NOT NULL CHECK (comment_user_id >= 0),
	comment_username varchar(255) DEFAULT '' NOT NULL,
	comment_user_colour varchar(6) DEFAULT '' NOT NULL,
	comment_user_ip varchar(40) DEFAULT '' NOT NULL,
	comment_time INT4 DEFAULT '0' NOT NULL CHECK (comment_time >= 0),
	comment TEXT DEFAULT '' NOT NULL,
	comment_uid varchar(8) DEFAULT '' NOT NULL,
	comment_bitfield varchar(255) DEFAULT '' NOT NULL,
	comment_edit_time INT4 DEFAULT '0' NOT NULL CHECK (comment_edit_time >= 0),
	comment_edit_count INT2 DEFAULT '0' NOT NULL CHECK (comment_edit_count >= 0),
	comment_edit_user_id INT4 DEFAULT '0' NOT NULL CHECK (comment_edit_user_id >= 0),
	PRIMARY KEY (comment_id)
);

CREATE INDEX phpbb_gallery_comments_comment_image_id ON phpbb_gallery_comments (comment_image_id);
CREATE INDEX phpbb_gallery_comments_comment_user_id ON phpbb_gallery_comments (comment_user_id);
CREATE INDEX phpbb_gallery_comments_comment_user_ip ON phpbb_gallery_comments (comment_user_ip);
CREATE INDEX phpbb_gallery_comments_comment_time ON phpbb_gallery_comments (comment_time);

/*
	Table: 'phpbb_gallery_config'
*/
CREATE TABLE phpbb_gallery_config (
	config_name varchar(255) DEFAULT '' NOT NULL,
	config_value varchar(255) DEFAULT '' NOT NULL,
	PRIMARY KEY (config_name)
);


/*
	Table: 'phpbb_gallery_contests'
*/
CREATE SEQUENCE phpbb_gallery_contests_seq;

CREATE TABLE phpbb_gallery_contests (
	contest_id INT4 DEFAULT nextval('phpbb_gallery_contests_seq'),
	contest_album_id INT4 DEFAULT '0' NOT NULL CHECK (contest_album_id >= 0),
	contest_start INT4 DEFAULT '0' NOT NULL CHECK (contest_start >= 0),
	contest_rating INT4 DEFAULT '0' NOT NULL CHECK (contest_rating >= 0),
	contest_end INT4 DEFAULT '0' NOT NULL CHECK (contest_end >= 0),
	contest_marked INT2 DEFAULT '0' NOT NULL,
	contest_first INT4 DEFAULT '0' NOT NULL CHECK (contest_first >= 0),
	contest_second INT4 DEFAULT '0' NOT NULL CHECK (contest_second >= 0),
	contest_third INT4 DEFAULT '0' NOT NULL CHECK (contest_third >= 0),
	PRIMARY KEY (contest_id)
);


/*
	Table: 'phpbb_gallery_copyts_albums'
*/
CREATE SEQUENCE phpbb_gallery_copyts_albums_seq;

CREATE TABLE phpbb_gallery_copyts_albums (
	album_id INT4 DEFAULT nextval('phpbb_gallery_copyts_albums_seq'),
	parent_id INT4 DEFAULT '0' NOT NULL CHECK (parent_id >= 0),
	left_id INT4 DEFAULT '1' NOT NULL CHECK (left_id >= 0),
	right_id INT4 DEFAULT '2' NOT NULL CHECK (right_id >= 0),
	album_name varchar(255) DEFAULT '' NOT NULL,
	album_desc TEXT DEFAULT '' NOT NULL,
	album_user_id INT4 DEFAULT '0' NOT NULL CHECK (album_user_id >= 0),
	PRIMARY KEY (album_id)
);


/*
	Table: 'phpbb_gallery_copyts_users'
*/
CREATE TABLE phpbb_gallery_copyts_users (
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	personal_album_id INT4 DEFAULT '0' NOT NULL CHECK (personal_album_id >= 0),
	PRIMARY KEY (user_id)
);


/*
	Table: 'phpbb_gallery_favorites'
*/
CREATE SEQUENCE phpbb_gallery_favorites_seq;

CREATE TABLE phpbb_gallery_favorites (
	favorite_id INT4 DEFAULT nextval('phpbb_gallery_favorites_seq'),
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	image_id INT4 DEFAULT '0' NOT NULL CHECK (image_id >= 0),
	PRIMARY KEY (favorite_id)
);

CREATE INDEX phpbb_gallery_favorites_user_id ON phpbb_gallery_favorites (user_id);
CREATE INDEX phpbb_gallery_favorites_image_id ON phpbb_gallery_favorites (image_id);

/*
	Table: 'phpbb_gallery_images'
*/
CREATE SEQUENCE phpbb_gallery_images_seq;

CREATE TABLE phpbb_gallery_images (
	image_id INT4 DEFAULT nextval('phpbb_gallery_images_seq'),
	image_filename varchar(255) DEFAULT '' NOT NULL,
	image_thumbnail varchar(255) DEFAULT '' NOT NULL,
	image_name varchar(255) DEFAULT '' NOT NULL,
	image_name_clean varchar(255) DEFAULT '' NOT NULL,
	image_desc TEXT DEFAULT '' NOT NULL,
	image_desc_uid varchar(8) DEFAULT '' NOT NULL,
	image_desc_bitfield varchar(255) DEFAULT '' NOT NULL,
	image_user_id INT4 DEFAULT '0' NOT NULL CHECK (image_user_id >= 0),
	image_username varchar(255) DEFAULT '' NOT NULL,
	image_username_clean varchar(255) DEFAULT '' NOT NULL,
	image_user_colour varchar(6) DEFAULT '' NOT NULL,
	image_user_ip varchar(40) DEFAULT '' NOT NULL,
	image_time INT4 DEFAULT '0' NOT NULL CHECK (image_time >= 0),
	image_album_id INT4 DEFAULT '0' NOT NULL CHECK (image_album_id >= 0),
	image_view_count INT4 DEFAULT '0' NOT NULL CHECK (image_view_count >= 0),
	image_status INT4 DEFAULT '0' NOT NULL CHECK (image_status >= 0),
	image_contest INT4 DEFAULT '0' NOT NULL CHECK (image_contest >= 0),
	image_contest_end INT4 DEFAULT '0' NOT NULL CHECK (image_contest_end >= 0),
	image_contest_rank INT4 DEFAULT '0' NOT NULL CHECK (image_contest_rank >= 0),
	image_filemissing INT4 DEFAULT '0' NOT NULL CHECK (image_filemissing >= 0),
	image_has_exif INT4 DEFAULT '2' NOT NULL CHECK (image_has_exif >= 0),
	image_exif_data varchar(8000) DEFAULT '' NOT NULL,
	image_rates INT4 DEFAULT '0' NOT NULL CHECK (image_rates >= 0),
	image_rate_points INT4 DEFAULT '0' NOT NULL CHECK (image_rate_points >= 0),
	image_rate_avg INT4 DEFAULT '0' NOT NULL CHECK (image_rate_avg >= 0),
	image_comments INT4 DEFAULT '0' NOT NULL CHECK (image_comments >= 0),
	image_last_comment INT4 DEFAULT '0' NOT NULL CHECK (image_last_comment >= 0),
	image_favorited INT4 DEFAULT '0' NOT NULL CHECK (image_favorited >= 0),
	image_reported INT4 DEFAULT '0' NOT NULL CHECK (image_reported >= 0),
	filesize_upload INT4 DEFAULT '0' NOT NULL CHECK (filesize_upload >= 0),
	filesize_medium INT4 DEFAULT '0' NOT NULL CHECK (filesize_medium >= 0),
	filesize_cache INT4 DEFAULT '0' NOT NULL CHECK (filesize_cache >= 0),
	PRIMARY KEY (image_id)
);

CREATE INDEX phpbb_gallery_images_image_album_id ON phpbb_gallery_images (image_album_id);
CREATE INDEX phpbb_gallery_images_image_user_id ON phpbb_gallery_images (image_user_id);
CREATE INDEX phpbb_gallery_images_image_time ON phpbb_gallery_images (image_time);

/*
	Table: 'phpbb_gallery_modscache'
*/
CREATE TABLE phpbb_gallery_modscache (
	album_id INT4 DEFAULT '0' NOT NULL CHECK (album_id >= 0),
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	username varchar(255) DEFAULT '' NOT NULL,
	group_id INT4 DEFAULT '0' NOT NULL CHECK (group_id >= 0),
	group_name varchar(255) DEFAULT '' NOT NULL,
	display_on_index INT2 DEFAULT '1' NOT NULL
);

CREATE INDEX phpbb_gallery_modscache_disp_idx ON phpbb_gallery_modscache (display_on_index);
CREATE INDEX phpbb_gallery_modscache_album_id ON phpbb_gallery_modscache (album_id);

/*
	Table: 'phpbb_gallery_permissions'
*/
CREATE SEQUENCE phpbb_gallery_permissions_seq;

CREATE TABLE phpbb_gallery_permissions (
	perm_id INT4 DEFAULT nextval('phpbb_gallery_permissions_seq'),
	perm_role_id INT4 DEFAULT '0' NOT NULL CHECK (perm_role_id >= 0),
	perm_album_id INT4 DEFAULT '0' NOT NULL CHECK (perm_album_id >= 0),
	perm_user_id INT4 DEFAULT '0' NOT NULL CHECK (perm_user_id >= 0),
	perm_group_id INT4 DEFAULT '0' NOT NULL CHECK (perm_group_id >= 0),
	perm_system INT4 DEFAULT '0' NOT NULL,
	PRIMARY KEY (perm_id)
);


/*
	Table: 'phpbb_gallery_rates'
*/
CREATE SEQUENCE phpbb_gallery_rates_seq;

CREATE TABLE phpbb_gallery_rates (
	rate_image_id INT4 DEFAULT nextval('phpbb_gallery_rates_seq'),
	rate_user_id INT4 DEFAULT '0' NOT NULL CHECK (rate_user_id >= 0),
	rate_user_ip varchar(40) DEFAULT '' NOT NULL,
	rate_point INT4 DEFAULT '0' NOT NULL CHECK (rate_point >= 0)
);

CREATE INDEX phpbb_gallery_rates_rate_image_id ON phpbb_gallery_rates (rate_image_id);
CREATE INDEX phpbb_gallery_rates_rate_user_id ON phpbb_gallery_rates (rate_user_id);
CREATE INDEX phpbb_gallery_rates_rate_user_ip ON phpbb_gallery_rates (rate_user_ip);
CREATE INDEX phpbb_gallery_rates_rate_point ON phpbb_gallery_rates (rate_point);

/*
	Table: 'phpbb_gallery_reports'
*/
CREATE SEQUENCE phpbb_gallery_reports_seq;

CREATE TABLE phpbb_gallery_reports (
	report_id INT4 DEFAULT nextval('phpbb_gallery_reports_seq'),
	report_album_id INT4 DEFAULT '0' NOT NULL CHECK (report_album_id >= 0),
	report_image_id INT4 DEFAULT '0' NOT NULL CHECK (report_image_id >= 0),
	reporter_id INT4 DEFAULT '0' NOT NULL CHECK (reporter_id >= 0),
	report_manager INT4 DEFAULT '0' NOT NULL CHECK (report_manager >= 0),
	report_note TEXT DEFAULT '' NOT NULL,
	report_time INT4 DEFAULT '0' NOT NULL CHECK (report_time >= 0),
	report_status INT4 DEFAULT '0' NOT NULL CHECK (report_status >= 0),
	PRIMARY KEY (report_id)
);


/*
	Table: 'phpbb_gallery_roles'
*/
CREATE SEQUENCE phpbb_gallery_roles_seq;

CREATE TABLE phpbb_gallery_roles (
	role_id INT4 DEFAULT nextval('phpbb_gallery_roles_seq'),
	a_list INT4 DEFAULT '0' NOT NULL CHECK (a_list >= 0),
	i_view INT4 DEFAULT '0' NOT NULL CHECK (i_view >= 0),
	i_watermark INT4 DEFAULT '0' NOT NULL CHECK (i_watermark >= 0),
	i_upload INT4 DEFAULT '0' NOT NULL CHECK (i_upload >= 0),
	i_edit INT4 DEFAULT '0' NOT NULL CHECK (i_edit >= 0),
	i_delete INT4 DEFAULT '0' NOT NULL CHECK (i_delete >= 0),
	i_rate INT4 DEFAULT '0' NOT NULL CHECK (i_rate >= 0),
	i_approve INT4 DEFAULT '0' NOT NULL CHECK (i_approve >= 0),
	i_lock INT4 DEFAULT '0' NOT NULL CHECK (i_lock >= 0),
	i_report INT4 DEFAULT '0' NOT NULL CHECK (i_report >= 0),
	i_count INT4 DEFAULT '0' NOT NULL CHECK (i_count >= 0),
	i_unlimited INT4 DEFAULT '0' NOT NULL CHECK (i_unlimited >= 0),
	c_read INT4 DEFAULT '0' NOT NULL CHECK (c_read >= 0),
	c_post INT4 DEFAULT '0' NOT NULL CHECK (c_post >= 0),
	c_edit INT4 DEFAULT '0' NOT NULL CHECK (c_edit >= 0),
	c_delete INT4 DEFAULT '0' NOT NULL CHECK (c_delete >= 0),
	m_comments INT4 DEFAULT '0' NOT NULL CHECK (m_comments >= 0),
	m_delete INT4 DEFAULT '0' NOT NULL CHECK (m_delete >= 0),
	m_edit INT4 DEFAULT '0' NOT NULL CHECK (m_edit >= 0),
	m_move INT4 DEFAULT '0' NOT NULL CHECK (m_move >= 0),
	m_report INT4 DEFAULT '0' NOT NULL CHECK (m_report >= 0),
	m_status INT4 DEFAULT '0' NOT NULL CHECK (m_status >= 0),
	album_count INT4 DEFAULT '0' NOT NULL CHECK (album_count >= 0),
	album_unlimited INT4 DEFAULT '0' NOT NULL CHECK (album_unlimited >= 0),
	PRIMARY KEY (role_id)
);


/*
	Table: 'phpbb_gallery_users'
*/
CREATE TABLE phpbb_gallery_users (
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	watch_own INT4 DEFAULT '0' NOT NULL CHECK (watch_own >= 0),
	watch_favo INT4 DEFAULT '0' NOT NULL CHECK (watch_favo >= 0),
	watch_com INT4 DEFAULT '0' NOT NULL CHECK (watch_com >= 0),
	user_images INT4 DEFAULT '0' NOT NULL CHECK (user_images >= 0),
	personal_album_id INT4 DEFAULT '0' NOT NULL CHECK (personal_album_id >= 0),
	user_lastmark INT4 DEFAULT '0' NOT NULL CHECK (user_lastmark >= 0),
	user_last_update INT4 DEFAULT '0' NOT NULL CHECK (user_last_update >= 0),
	user_viewexif INT4 DEFAULT '0' NOT NULL CHECK (user_viewexif >= 0),
	user_permissions TEXT DEFAULT '' NOT NULL,
	PRIMARY KEY (user_id)
);


/*
	Table: 'phpbb_gallery_watch'
*/
CREATE SEQUENCE phpbb_gallery_watch_seq;

CREATE TABLE phpbb_gallery_watch (
	watch_id INT4 DEFAULT nextval('phpbb_gallery_watch_seq'),
	album_id INT4 DEFAULT '0' NOT NULL CHECK (album_id >= 0),
	image_id INT4 DEFAULT '0' NOT NULL CHECK (image_id >= 0),
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	PRIMARY KEY (watch_id)
);

CREATE INDEX phpbb_gallery_watch_user_id ON phpbb_gallery_watch (user_id);
CREATE INDEX phpbb_gallery_watch_image_id ON phpbb_gallery_watch (image_id);
CREATE INDEX phpbb_gallery_watch_album_id ON phpbb_gallery_watch (album_id);


COMMIT;