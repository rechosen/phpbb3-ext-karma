#
# Do NOT manually edit this file!
#


# Table: 'phpbb_gallery_albums'
CREATE TABLE phpbb_gallery_albums (
	album_id INTEGER NOT NULL,
	parent_id INTEGER DEFAULT 0 NOT NULL,
	left_id INTEGER DEFAULT 1 NOT NULL,
	right_id INTEGER DEFAULT 2 NOT NULL,
	user_id INTEGER DEFAULT 0 NOT NULL,
	album_parents BLOB SUB_TYPE TEXT CHARACTER SET UTF8 DEFAULT '' NOT NULL,
	album_type VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	album_status INTEGER DEFAULT 1 NOT NULL,
	album_contest INTEGER DEFAULT 0 NOT NULL,
	album_name VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	album_desc BLOB SUB_TYPE TEXT CHARACTER SET UTF8 DEFAULT '' NOT NULL,
	album_desc_options INTEGER DEFAULT 7 NOT NULL,
	album_desc_uid VARCHAR(8) CHARACTER SET NONE DEFAULT '' NOT NULL,
	album_desc_bitfield VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	album_images INTEGER DEFAULT 0 NOT NULL,
	album_images_real INTEGER DEFAULT 0 NOT NULL,
	album_last_image_id INTEGER DEFAULT 0 NOT NULL,
	album_image VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	album_last_image_time INTEGER DEFAULT 0 NOT NULL,
	album_last_image_name VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	album_last_username VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	album_last_user_colour VARCHAR(6) CHARACTER SET NONE DEFAULT '' NOT NULL,
	album_last_user_id INTEGER DEFAULT 0 NOT NULL,
	album_watermark INTEGER DEFAULT 1 NOT NULL,
	album_sort_key VARCHAR(8) CHARACTER SET NONE DEFAULT '' NOT NULL,
	album_sort_dir VARCHAR(8) CHARACTER SET NONE DEFAULT '' NOT NULL,
	display_in_rrc INTEGER DEFAULT 1 NOT NULL,
	display_on_index INTEGER DEFAULT 1 NOT NULL,
	display_subalbum_list INTEGER DEFAULT 1 NOT NULL
);;

ALTER TABLE phpbb_gallery_albums ADD PRIMARY KEY (album_id);;


CREATE GENERATOR phpbb_gallery_albums_gen;;
SET GENERATOR phpbb_gallery_albums_gen TO 0;;

CREATE TRIGGER t_phpbb_gallery_albums FOR phpbb_gallery_albums
BEFORE INSERT
AS
BEGIN
	NEW.album_id = GEN_ID(phpbb_gallery_albums_gen, 1);
END;;


# Table: 'phpbb_gallery_albums_track'
CREATE TABLE phpbb_gallery_albums_track (
	user_id INTEGER DEFAULT 0 NOT NULL,
	album_id INTEGER DEFAULT 0 NOT NULL,
	mark_time INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_gallery_albums_track ADD PRIMARY KEY (user_id, album_id);;


# Table: 'phpbb_gallery_comments'
CREATE TABLE phpbb_gallery_comments (
	comment_id INTEGER NOT NULL,
	comment_image_id INTEGER NOT NULL,
	comment_user_id INTEGER DEFAULT 0 NOT NULL,
	comment_username VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	comment_user_colour VARCHAR(6) CHARACTER SET NONE DEFAULT '' NOT NULL,
	comment_user_ip VARCHAR(40) CHARACTER SET NONE DEFAULT '' NOT NULL,
	comment_time INTEGER DEFAULT 0 NOT NULL,
	comment BLOB SUB_TYPE TEXT CHARACTER SET UTF8 DEFAULT '' NOT NULL,
	comment_uid VARCHAR(8) CHARACTER SET NONE DEFAULT '' NOT NULL,
	comment_bitfield VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	comment_edit_time INTEGER DEFAULT 0 NOT NULL,
	comment_edit_count INTEGER DEFAULT 0 NOT NULL,
	comment_edit_user_id INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_gallery_comments ADD PRIMARY KEY (comment_id);;

CREATE INDEX phpbb_gallery_comments_comment_image_id ON phpbb_gallery_comments(comment_image_id);;
CREATE INDEX phpbb_gallery_comments_comment_user_id ON phpbb_gallery_comments(comment_user_id);;
CREATE INDEX phpbb_gallery_comments_comment_user_ip ON phpbb_gallery_comments(comment_user_ip);;
CREATE INDEX phpbb_gallery_comments_comment_time ON phpbb_gallery_comments(comment_time);;

CREATE GENERATOR phpbb_gallery_comments_gen;;
SET GENERATOR phpbb_gallery_comments_gen TO 0;;

CREATE TRIGGER t_phpbb_gallery_comments FOR phpbb_gallery_comments
BEFORE INSERT
AS
BEGIN
	NEW.comment_id = GEN_ID(phpbb_gallery_comments_gen, 1);
END;;


# Table: 'phpbb_gallery_config'
CREATE TABLE phpbb_gallery_config (
	config_name VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	config_value VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL
);;

ALTER TABLE phpbb_gallery_config ADD PRIMARY KEY (config_name);;


# Table: 'phpbb_gallery_contests'
CREATE TABLE phpbb_gallery_contests (
	contest_id INTEGER NOT NULL,
	contest_album_id INTEGER DEFAULT 0 NOT NULL,
	contest_start INTEGER DEFAULT 0 NOT NULL,
	contest_rating INTEGER DEFAULT 0 NOT NULL,
	contest_end INTEGER DEFAULT 0 NOT NULL,
	contest_marked INTEGER DEFAULT 0 NOT NULL,
	contest_first INTEGER DEFAULT 0 NOT NULL,
	contest_second INTEGER DEFAULT 0 NOT NULL,
	contest_third INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_gallery_contests ADD PRIMARY KEY (contest_id);;


CREATE GENERATOR phpbb_gallery_contests_gen;;
SET GENERATOR phpbb_gallery_contests_gen TO 0;;

CREATE TRIGGER t_phpbb_gallery_contests FOR phpbb_gallery_contests
BEFORE INSERT
AS
BEGIN
	NEW.contest_id = GEN_ID(phpbb_gallery_contests_gen, 1);
END;;


# Table: 'phpbb_gallery_copyts_albums'
CREATE TABLE phpbb_gallery_copyts_albums (
	album_id INTEGER NOT NULL,
	parent_id INTEGER DEFAULT 0 NOT NULL,
	left_id INTEGER DEFAULT 1 NOT NULL,
	right_id INTEGER DEFAULT 2 NOT NULL,
	album_name VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	album_desc BLOB SUB_TYPE TEXT CHARACTER SET UTF8 DEFAULT '' NOT NULL,
	album_user_id INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_gallery_copyts_albums ADD PRIMARY KEY (album_id);;


CREATE GENERATOR phpbb_gallery_copyts_albums_gen;;
SET GENERATOR phpbb_gallery_copyts_albums_gen TO 0;;

CREATE TRIGGER t_phpbb_gallery_copyts_albums FOR phpbb_gallery_copyts_albums
BEFORE INSERT
AS
BEGIN
	NEW.album_id = GEN_ID(phpbb_gallery_copyts_albums_gen, 1);
END;;


# Table: 'phpbb_gallery_copyts_users'
CREATE TABLE phpbb_gallery_copyts_users (
	user_id INTEGER DEFAULT 0 NOT NULL,
	personal_album_id INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_gallery_copyts_users ADD PRIMARY KEY (user_id);;


# Table: 'phpbb_gallery_favorites'
CREATE TABLE phpbb_gallery_favorites (
	favorite_id INTEGER NOT NULL,
	user_id INTEGER DEFAULT 0 NOT NULL,
	image_id INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_gallery_favorites ADD PRIMARY KEY (favorite_id);;

CREATE INDEX phpbb_gallery_favorites_user_id ON phpbb_gallery_favorites(user_id);;
CREATE INDEX phpbb_gallery_favorites_image_id ON phpbb_gallery_favorites(image_id);;

CREATE GENERATOR phpbb_gallery_favorites_gen;;
SET GENERATOR phpbb_gallery_favorites_gen TO 0;;

CREATE TRIGGER t_phpbb_gallery_favorites FOR phpbb_gallery_favorites
BEFORE INSERT
AS
BEGIN
	NEW.favorite_id = GEN_ID(phpbb_gallery_favorites_gen, 1);
END;;


# Table: 'phpbb_gallery_images'
CREATE TABLE phpbb_gallery_images (
	image_id INTEGER NOT NULL,
	image_filename VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	image_thumbnail VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	image_name VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	image_name_clean VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	image_desc BLOB SUB_TYPE TEXT CHARACTER SET UTF8 DEFAULT '' NOT NULL,
	image_desc_uid VARCHAR(8) CHARACTER SET NONE DEFAULT '' NOT NULL,
	image_desc_bitfield VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	image_user_id INTEGER DEFAULT 0 NOT NULL,
	image_username VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	image_username_clean VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	image_user_colour VARCHAR(6) CHARACTER SET NONE DEFAULT '' NOT NULL,
	image_user_ip VARCHAR(40) CHARACTER SET NONE DEFAULT '' NOT NULL,
	image_time INTEGER DEFAULT 0 NOT NULL,
	image_album_id INTEGER DEFAULT 0 NOT NULL,
	image_view_count INTEGER DEFAULT 0 NOT NULL,
	image_status INTEGER DEFAULT 0 NOT NULL,
	image_contest INTEGER DEFAULT 0 NOT NULL,
	image_contest_end INTEGER DEFAULT 0 NOT NULL,
	image_contest_rank INTEGER DEFAULT 0 NOT NULL,
	image_filemissing INTEGER DEFAULT 0 NOT NULL,
	image_has_exif INTEGER DEFAULT 2 NOT NULL,
	image_exif_data BLOB SUB_TYPE TEXT CHARACTER SET NONE DEFAULT '' NOT NULL,
	image_rates INTEGER DEFAULT 0 NOT NULL,
	image_rate_points INTEGER DEFAULT 0 NOT NULL,
	image_rate_avg INTEGER DEFAULT 0 NOT NULL,
	image_comments INTEGER DEFAULT 0 NOT NULL,
	image_last_comment INTEGER DEFAULT 0 NOT NULL,
	image_favorited INTEGER DEFAULT 0 NOT NULL,
	image_reported INTEGER DEFAULT 0 NOT NULL,
	filesize_upload INTEGER DEFAULT 0 NOT NULL,
	filesize_medium INTEGER DEFAULT 0 NOT NULL,
	filesize_cache INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_gallery_images ADD PRIMARY KEY (image_id);;

CREATE INDEX phpbb_gallery_images_image_album_id ON phpbb_gallery_images(image_album_id);;
CREATE INDEX phpbb_gallery_images_image_user_id ON phpbb_gallery_images(image_user_id);;
CREATE INDEX phpbb_gallery_images_image_time ON phpbb_gallery_images(image_time);;

CREATE GENERATOR phpbb_gallery_images_gen;;
SET GENERATOR phpbb_gallery_images_gen TO 0;;

CREATE TRIGGER t_phpbb_gallery_images FOR phpbb_gallery_images
BEFORE INSERT
AS
BEGIN
	NEW.image_id = GEN_ID(phpbb_gallery_images_gen, 1);
END;;


# Table: 'phpbb_gallery_modscache'
CREATE TABLE phpbb_gallery_modscache (
	album_id INTEGER DEFAULT 0 NOT NULL,
	user_id INTEGER DEFAULT 0 NOT NULL,
	username VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	group_id INTEGER DEFAULT 0 NOT NULL,
	group_name VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	display_on_index INTEGER DEFAULT 1 NOT NULL
);;

CREATE INDEX phpbb_gallery_modscache_disp_idx ON phpbb_gallery_modscache(display_on_index);;
CREATE INDEX phpbb_gallery_modscache_album_id ON phpbb_gallery_modscache(album_id);;

# Table: 'phpbb_gallery_permissions'
CREATE TABLE phpbb_gallery_permissions (
	perm_id INTEGER NOT NULL,
	perm_role_id INTEGER DEFAULT 0 NOT NULL,
	perm_album_id INTEGER DEFAULT 0 NOT NULL,
	perm_user_id INTEGER DEFAULT 0 NOT NULL,
	perm_group_id INTEGER DEFAULT 0 NOT NULL,
	perm_system INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_gallery_permissions ADD PRIMARY KEY (perm_id);;


CREATE GENERATOR phpbb_gallery_permissions_gen;;
SET GENERATOR phpbb_gallery_permissions_gen TO 0;;

CREATE TRIGGER t_phpbb_gallery_permissions FOR phpbb_gallery_permissions
BEFORE INSERT
AS
BEGIN
	NEW.perm_id = GEN_ID(phpbb_gallery_permissions_gen, 1);
END;;


# Table: 'phpbb_gallery_rates'
CREATE TABLE phpbb_gallery_rates (
	rate_image_id INTEGER NOT NULL,
	rate_user_id INTEGER DEFAULT 0 NOT NULL,
	rate_user_ip VARCHAR(40) CHARACTER SET NONE DEFAULT '' NOT NULL,
	rate_point INTEGER DEFAULT 0 NOT NULL
);;

CREATE INDEX phpbb_gallery_rates_rate_image_id ON phpbb_gallery_rates(rate_image_id);;
CREATE INDEX phpbb_gallery_rates_rate_user_id ON phpbb_gallery_rates(rate_user_id);;
CREATE INDEX phpbb_gallery_rates_rate_user_ip ON phpbb_gallery_rates(rate_user_ip);;
CREATE INDEX phpbb_gallery_rates_rate_point ON phpbb_gallery_rates(rate_point);;

CREATE GENERATOR phpbb_gallery_rates_gen;;
SET GENERATOR phpbb_gallery_rates_gen TO 0;;

CREATE TRIGGER t_phpbb_gallery_rates FOR phpbb_gallery_rates
BEFORE INSERT
AS
BEGIN
	NEW.rate_image_id = GEN_ID(phpbb_gallery_rates_gen, 1);
END;;


# Table: 'phpbb_gallery_reports'
CREATE TABLE phpbb_gallery_reports (
	report_id INTEGER NOT NULL,
	report_album_id INTEGER DEFAULT 0 NOT NULL,
	report_image_id INTEGER DEFAULT 0 NOT NULL,
	reporter_id INTEGER DEFAULT 0 NOT NULL,
	report_manager INTEGER DEFAULT 0 NOT NULL,
	report_note BLOB SUB_TYPE TEXT CHARACTER SET UTF8 DEFAULT '' NOT NULL,
	report_time INTEGER DEFAULT 0 NOT NULL,
	report_status INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_gallery_reports ADD PRIMARY KEY (report_id);;


CREATE GENERATOR phpbb_gallery_reports_gen;;
SET GENERATOR phpbb_gallery_reports_gen TO 0;;

CREATE TRIGGER t_phpbb_gallery_reports FOR phpbb_gallery_reports
BEFORE INSERT
AS
BEGIN
	NEW.report_id = GEN_ID(phpbb_gallery_reports_gen, 1);
END;;


# Table: 'phpbb_gallery_roles'
CREATE TABLE phpbb_gallery_roles (
	role_id INTEGER NOT NULL,
	a_list INTEGER DEFAULT 0 NOT NULL,
	i_view INTEGER DEFAULT 0 NOT NULL,
	i_watermark INTEGER DEFAULT 0 NOT NULL,
	i_upload INTEGER DEFAULT 0 NOT NULL,
	i_edit INTEGER DEFAULT 0 NOT NULL,
	i_delete INTEGER DEFAULT 0 NOT NULL,
	i_rate INTEGER DEFAULT 0 NOT NULL,
	i_approve INTEGER DEFAULT 0 NOT NULL,
	i_lock INTEGER DEFAULT 0 NOT NULL,
	i_report INTEGER DEFAULT 0 NOT NULL,
	i_count INTEGER DEFAULT 0 NOT NULL,
	i_unlimited INTEGER DEFAULT 0 NOT NULL,
	c_read INTEGER DEFAULT 0 NOT NULL,
	c_post INTEGER DEFAULT 0 NOT NULL,
	c_edit INTEGER DEFAULT 0 NOT NULL,
	c_delete INTEGER DEFAULT 0 NOT NULL,
	m_comments INTEGER DEFAULT 0 NOT NULL,
	m_delete INTEGER DEFAULT 0 NOT NULL,
	m_edit INTEGER DEFAULT 0 NOT NULL,
	m_move INTEGER DEFAULT 0 NOT NULL,
	m_report INTEGER DEFAULT 0 NOT NULL,
	m_status INTEGER DEFAULT 0 NOT NULL,
	album_count INTEGER DEFAULT 0 NOT NULL,
	album_unlimited INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_gallery_roles ADD PRIMARY KEY (role_id);;


CREATE GENERATOR phpbb_gallery_roles_gen;;
SET GENERATOR phpbb_gallery_roles_gen TO 0;;

CREATE TRIGGER t_phpbb_gallery_roles FOR phpbb_gallery_roles
BEFORE INSERT
AS
BEGIN
	NEW.role_id = GEN_ID(phpbb_gallery_roles_gen, 1);
END;;


# Table: 'phpbb_gallery_users'
CREATE TABLE phpbb_gallery_users (
	user_id INTEGER DEFAULT 0 NOT NULL,
	watch_own INTEGER DEFAULT 0 NOT NULL,
	watch_favo INTEGER DEFAULT 0 NOT NULL,
	watch_com INTEGER DEFAULT 0 NOT NULL,
	user_images INTEGER DEFAULT 0 NOT NULL,
	personal_album_id INTEGER DEFAULT 0 NOT NULL,
	user_lastmark INTEGER DEFAULT 0 NOT NULL,
	user_last_update INTEGER DEFAULT 0 NOT NULL,
	user_viewexif INTEGER DEFAULT 0 NOT NULL,
	user_permissions BLOB SUB_TYPE TEXT CHARACTER SET UTF8 DEFAULT '' NOT NULL
);;

ALTER TABLE phpbb_gallery_users ADD PRIMARY KEY (user_id);;


# Table: 'phpbb_gallery_watch'
CREATE TABLE phpbb_gallery_watch (
	watch_id INTEGER NOT NULL,
	album_id INTEGER DEFAULT 0 NOT NULL,
	image_id INTEGER DEFAULT 0 NOT NULL,
	user_id INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_gallery_watch ADD PRIMARY KEY (watch_id);;

CREATE INDEX phpbb_gallery_watch_user_id ON phpbb_gallery_watch(user_id);;
CREATE INDEX phpbb_gallery_watch_image_id ON phpbb_gallery_watch(image_id);;
CREATE INDEX phpbb_gallery_watch_album_id ON phpbb_gallery_watch(album_id);;

CREATE GENERATOR phpbb_gallery_watch_gen;;
SET GENERATOR phpbb_gallery_watch_gen TO 0;;

CREATE TRIGGER t_phpbb_gallery_watch FOR phpbb_gallery_watch
BEFORE INSERT
AS
BEGIN
	NEW.watch_id = GEN_ID(phpbb_gallery_watch_gen, 1);
END;;


