/*

 Do NOT manually edit this file!

*/


/*
	Table: 'phpbb_gallery_albums'
*/
CREATE TABLE phpbb_gallery_albums (
	album_id number(8) NOT NULL,
	parent_id number(8) DEFAULT '0' NOT NULL,
	left_id number(8) DEFAULT '1' NOT NULL,
	right_id number(8) DEFAULT '2' NOT NULL,
	user_id number(8) DEFAULT '0' NOT NULL,
	album_parents clob DEFAULT '' ,
	album_type varchar2(255) DEFAULT '' ,
	album_status number(1) DEFAULT '1' NOT NULL,
	album_contest number(8) DEFAULT '0' NOT NULL,
	album_name varchar2(255) DEFAULT '' ,
	album_desc clob DEFAULT '' ,
	album_desc_options number(3) DEFAULT '7' NOT NULL,
	album_desc_uid varchar2(8) DEFAULT '' ,
	album_desc_bitfield varchar2(255) DEFAULT '' ,
	album_images number(8) DEFAULT '0' NOT NULL,
	album_images_real number(8) DEFAULT '0' NOT NULL,
	album_last_image_id number(8) DEFAULT '0' NOT NULL,
	album_image varchar2(255) DEFAULT '' ,
	album_last_image_time number(11) DEFAULT '0' NOT NULL,
	album_last_image_name varchar2(255) DEFAULT '' ,
	album_last_username varchar2(255) DEFAULT '' ,
	album_last_user_colour varchar2(6) DEFAULT '' ,
	album_last_user_id number(8) DEFAULT '0' NOT NULL,
	album_watermark number(1) DEFAULT '1' NOT NULL,
	album_sort_key varchar2(8) DEFAULT '' ,
	album_sort_dir varchar2(8) DEFAULT '' ,
	display_in_rrc number(1) DEFAULT '1' NOT NULL,
	display_on_index number(1) DEFAULT '1' NOT NULL,
	display_subalbum_list number(1) DEFAULT '1' NOT NULL,
	CONSTRAINT pk_phpbb_gallery_albums PRIMARY KEY (album_id)
)
/


CREATE SEQUENCE phpbb_gallery_albums_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_gallery_albums
BEFORE INSERT ON phpbb_gallery_albums
FOR EACH ROW WHEN (
	new.album_id IS NULL OR new.album_id = 0
)
BEGIN
	SELECT phpbb_gallery_albums_seq.nextval
	INTO :new.album_id
	FROM dual;
END;
/


/*
	Table: 'phpbb_gallery_albums_track'
*/
CREATE TABLE phpbb_gallery_albums_track (
	user_id number(8) DEFAULT '0' NOT NULL,
	album_id number(8) DEFAULT '0' NOT NULL,
	mark_time number(11) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_gallery_albums_track PRIMARY KEY (user_id, album_id)
)
/


/*
	Table: 'phpbb_gallery_comments'
*/
CREATE TABLE phpbb_gallery_comments (
	comment_id number(8) NOT NULL,
	comment_image_id number(8) NOT NULL,
	comment_user_id number(8) DEFAULT '0' NOT NULL,
	comment_username varchar2(255) DEFAULT '' ,
	comment_user_colour varchar2(6) DEFAULT '' ,
	comment_user_ip varchar2(40) DEFAULT '' ,
	comment_time number(11) DEFAULT '0' NOT NULL,
	comment clob DEFAULT '' ,
	comment_uid varchar2(8) DEFAULT '' ,
	comment_bitfield varchar2(255) DEFAULT '' ,
	comment_edit_time number(11) DEFAULT '0' NOT NULL,
	comment_edit_count number(4) DEFAULT '0' NOT NULL,
	comment_edit_user_id number(8) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_gallery_comments PRIMARY KEY (comment_id)
)
/

CREATE INDEX phpbb_gallery_comments_comment_image_id ON phpbb_gallery_comments (comment_image_id)
/
CREATE INDEX phpbb_gallery_comments_comment_user_id ON phpbb_gallery_comments (comment_user_id)
/
CREATE INDEX phpbb_gallery_comments_comment_user_ip ON phpbb_gallery_comments (comment_user_ip)
/
CREATE INDEX phpbb_gallery_comments_comment_time ON phpbb_gallery_comments (comment_time)
/

CREATE SEQUENCE phpbb_gallery_comments_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_gallery_comments
BEFORE INSERT ON phpbb_gallery_comments
FOR EACH ROW WHEN (
	new.comment_id IS NULL OR new.comment_id = 0
)
BEGIN
	SELECT phpbb_gallery_comments_seq.nextval
	INTO :new.comment_id
	FROM dual;
END;
/


/*
	Table: 'phpbb_gallery_config'
*/
CREATE TABLE phpbb_gallery_config (
	config_name varchar2(255) DEFAULT '' ,
	config_value varchar2(255) DEFAULT '' ,
	CONSTRAINT pk_phpbb_gallery_config PRIMARY KEY (config_name)
)
/


/*
	Table: 'phpbb_gallery_contests'
*/
CREATE TABLE phpbb_gallery_contests (
	contest_id number(8) NOT NULL,
	contest_album_id number(8) DEFAULT '0' NOT NULL,
	contest_start number(11) DEFAULT '0' NOT NULL,
	contest_rating number(11) DEFAULT '0' NOT NULL,
	contest_end number(11) DEFAULT '0' NOT NULL,
	contest_marked number(1) DEFAULT '0' NOT NULL,
	contest_first number(8) DEFAULT '0' NOT NULL,
	contest_second number(8) DEFAULT '0' NOT NULL,
	contest_third number(8) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_gallery_contests PRIMARY KEY (contest_id)
)
/


CREATE SEQUENCE phpbb_gallery_contests_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_gallery_contests
BEFORE INSERT ON phpbb_gallery_contests
FOR EACH ROW WHEN (
	new.contest_id IS NULL OR new.contest_id = 0
)
BEGIN
	SELECT phpbb_gallery_contests_seq.nextval
	INTO :new.contest_id
	FROM dual;
END;
/


/*
	Table: 'phpbb_gallery_copyts_albums'
*/
CREATE TABLE phpbb_gallery_copyts_albums (
	album_id number(8) NOT NULL,
	parent_id number(8) DEFAULT '0' NOT NULL,
	left_id number(8) DEFAULT '1' NOT NULL,
	right_id number(8) DEFAULT '2' NOT NULL,
	album_name varchar2(255) DEFAULT '' ,
	album_desc clob DEFAULT '' ,
	album_user_id number(8) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_gallery_copyts_albums PRIMARY KEY (album_id)
)
/


CREATE SEQUENCE phpbb_gallery_copyts_albums_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_gallery_copyts_albums
BEFORE INSERT ON phpbb_gallery_copyts_albums
FOR EACH ROW WHEN (
	new.album_id IS NULL OR new.album_id = 0
)
BEGIN
	SELECT phpbb_gallery_copyts_albums_seq.nextval
	INTO :new.album_id
	FROM dual;
END;
/


/*
	Table: 'phpbb_gallery_copyts_users'
*/
CREATE TABLE phpbb_gallery_copyts_users (
	user_id number(8) DEFAULT '0' NOT NULL,
	personal_album_id number(8) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_gallery_copyts_users PRIMARY KEY (user_id)
)
/


/*
	Table: 'phpbb_gallery_favorites'
*/
CREATE TABLE phpbb_gallery_favorites (
	favorite_id number(8) NOT NULL,
	user_id number(8) DEFAULT '0' NOT NULL,
	image_id number(8) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_gallery_favorites PRIMARY KEY (favorite_id)
)
/

CREATE INDEX phpbb_gallery_favorites_user_id ON phpbb_gallery_favorites (user_id)
/
CREATE INDEX phpbb_gallery_favorites_image_id ON phpbb_gallery_favorites (image_id)
/

CREATE SEQUENCE phpbb_gallery_favorites_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_gallery_favorites
BEFORE INSERT ON phpbb_gallery_favorites
FOR EACH ROW WHEN (
	new.favorite_id IS NULL OR new.favorite_id = 0
)
BEGIN
	SELECT phpbb_gallery_favorites_seq.nextval
	INTO :new.favorite_id
	FROM dual;
END;
/


/*
	Table: 'phpbb_gallery_images'
*/
CREATE TABLE phpbb_gallery_images (
	image_id number(8) NOT NULL,
	image_filename varchar2(255) DEFAULT '' ,
	image_thumbnail varchar2(255) DEFAULT '' ,
	image_name varchar2(255) DEFAULT '' ,
	image_name_clean varchar2(255) DEFAULT '' ,
	image_desc clob DEFAULT '' ,
	image_desc_uid varchar2(8) DEFAULT '' ,
	image_desc_bitfield varchar2(255) DEFAULT '' ,
	image_user_id number(8) DEFAULT '0' NOT NULL,
	image_username varchar2(255) DEFAULT '' ,
	image_username_clean varchar2(255) DEFAULT '' ,
	image_user_colour varchar2(6) DEFAULT '' ,
	image_user_ip varchar2(40) DEFAULT '' ,
	image_time number(11) DEFAULT '0' NOT NULL,
	image_album_id number(8) DEFAULT '0' NOT NULL,
	image_view_count number(11) DEFAULT '0' NOT NULL,
	image_status number(3) DEFAULT '0' NOT NULL,
	image_contest number(1) DEFAULT '0' NOT NULL,
	image_contest_end number(11) DEFAULT '0' NOT NULL,
	image_contest_rank number(3) DEFAULT '0' NOT NULL,
	image_filemissing number(3) DEFAULT '0' NOT NULL,
	image_has_exif number(3) DEFAULT '2' NOT NULL,
	image_exif_data clob DEFAULT '' ,
	image_rates number(8) DEFAULT '0' NOT NULL,
	image_rate_points number(8) DEFAULT '0' NOT NULL,
	image_rate_avg number(8) DEFAULT '0' NOT NULL,
	image_comments number(8) DEFAULT '0' NOT NULL,
	image_last_comment number(8) DEFAULT '0' NOT NULL,
	image_favorited number(8) DEFAULT '0' NOT NULL,
	image_reported number(8) DEFAULT '0' NOT NULL,
	filesize_upload number(20) DEFAULT '0' NOT NULL,
	filesize_medium number(20) DEFAULT '0' NOT NULL,
	filesize_cache number(20) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_gallery_images PRIMARY KEY (image_id)
)
/

CREATE INDEX phpbb_gallery_images_image_album_id ON phpbb_gallery_images (image_album_id)
/
CREATE INDEX phpbb_gallery_images_image_user_id ON phpbb_gallery_images (image_user_id)
/
CREATE INDEX phpbb_gallery_images_image_time ON phpbb_gallery_images (image_time)
/

CREATE SEQUENCE phpbb_gallery_images_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_gallery_images
BEFORE INSERT ON phpbb_gallery_images
FOR EACH ROW WHEN (
	new.image_id IS NULL OR new.image_id = 0
)
BEGIN
	SELECT phpbb_gallery_images_seq.nextval
	INTO :new.image_id
	FROM dual;
END;
/


/*
	Table: 'phpbb_gallery_modscache'
*/
CREATE TABLE phpbb_gallery_modscache (
	album_id number(8) DEFAULT '0' NOT NULL,
	user_id number(8) DEFAULT '0' NOT NULL,
	username varchar2(255) DEFAULT '' ,
	group_id number(8) DEFAULT '0' NOT NULL,
	group_name varchar2(255) DEFAULT '' ,
	display_on_index number(1) DEFAULT '1' NOT NULL
)
/

CREATE INDEX phpbb_gallery_modscache_disp_idx ON phpbb_gallery_modscache (display_on_index)
/
CREATE INDEX phpbb_gallery_modscache_album_id ON phpbb_gallery_modscache (album_id)
/

/*
	Table: 'phpbb_gallery_permissions'
*/
CREATE TABLE phpbb_gallery_permissions (
	perm_id number(8) NOT NULL,
	perm_role_id number(8) DEFAULT '0' NOT NULL,
	perm_album_id number(8) DEFAULT '0' NOT NULL,
	perm_user_id number(8) DEFAULT '0' NOT NULL,
	perm_group_id number(8) DEFAULT '0' NOT NULL,
	perm_system number(3) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_gallery_permissions PRIMARY KEY (perm_id)
)
/


CREATE SEQUENCE phpbb_gallery_permissions_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_gallery_permissions
BEFORE INSERT ON phpbb_gallery_permissions
FOR EACH ROW WHEN (
	new.perm_id IS NULL OR new.perm_id = 0
)
BEGIN
	SELECT phpbb_gallery_permissions_seq.nextval
	INTO :new.perm_id
	FROM dual;
END;
/


/*
	Table: 'phpbb_gallery_rates'
*/
CREATE TABLE phpbb_gallery_rates (
	rate_image_id number(8) NOT NULL,
	rate_user_id number(8) DEFAULT '0' NOT NULL,
	rate_user_ip varchar2(40) DEFAULT '' ,
	rate_point number(3) DEFAULT '0' NOT NULL
)
/

CREATE INDEX phpbb_gallery_rates_rate_image_id ON phpbb_gallery_rates (rate_image_id)
/
CREATE INDEX phpbb_gallery_rates_rate_user_id ON phpbb_gallery_rates (rate_user_id)
/
CREATE INDEX phpbb_gallery_rates_rate_user_ip ON phpbb_gallery_rates (rate_user_ip)
/
CREATE INDEX phpbb_gallery_rates_rate_point ON phpbb_gallery_rates (rate_point)
/

CREATE SEQUENCE phpbb_gallery_rates_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_gallery_rates
BEFORE INSERT ON phpbb_gallery_rates
FOR EACH ROW WHEN (
	new.rate_image_id IS NULL OR new.rate_image_id = 0
)
BEGIN
	SELECT phpbb_gallery_rates_seq.nextval
	INTO :new.rate_image_id
	FROM dual;
END;
/


/*
	Table: 'phpbb_gallery_reports'
*/
CREATE TABLE phpbb_gallery_reports (
	report_id number(8) NOT NULL,
	report_album_id number(8) DEFAULT '0' NOT NULL,
	report_image_id number(8) DEFAULT '0' NOT NULL,
	reporter_id number(8) DEFAULT '0' NOT NULL,
	report_manager number(8) DEFAULT '0' NOT NULL,
	report_note clob DEFAULT '' ,
	report_time number(11) DEFAULT '0' NOT NULL,
	report_status number(3) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_gallery_reports PRIMARY KEY (report_id)
)
/


CREATE SEQUENCE phpbb_gallery_reports_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_gallery_reports
BEFORE INSERT ON phpbb_gallery_reports
FOR EACH ROW WHEN (
	new.report_id IS NULL OR new.report_id = 0
)
BEGIN
	SELECT phpbb_gallery_reports_seq.nextval
	INTO :new.report_id
	FROM dual;
END;
/


/*
	Table: 'phpbb_gallery_roles'
*/
CREATE TABLE phpbb_gallery_roles (
	role_id number(8) NOT NULL,
	a_list number(3) DEFAULT '0' NOT NULL,
	i_view number(3) DEFAULT '0' NOT NULL,
	i_watermark number(3) DEFAULT '0' NOT NULL,
	i_upload number(3) DEFAULT '0' NOT NULL,
	i_edit number(3) DEFAULT '0' NOT NULL,
	i_delete number(3) DEFAULT '0' NOT NULL,
	i_rate number(3) DEFAULT '0' NOT NULL,
	i_approve number(3) DEFAULT '0' NOT NULL,
	i_lock number(3) DEFAULT '0' NOT NULL,
	i_report number(3) DEFAULT '0' NOT NULL,
	i_count number(8) DEFAULT '0' NOT NULL,
	i_unlimited number(3) DEFAULT '0' NOT NULL,
	c_read number(3) DEFAULT '0' NOT NULL,
	c_post number(3) DEFAULT '0' NOT NULL,
	c_edit number(3) DEFAULT '0' NOT NULL,
	c_delete number(3) DEFAULT '0' NOT NULL,
	m_comments number(3) DEFAULT '0' NOT NULL,
	m_delete number(3) DEFAULT '0' NOT NULL,
	m_edit number(3) DEFAULT '0' NOT NULL,
	m_move number(3) DEFAULT '0' NOT NULL,
	m_report number(3) DEFAULT '0' NOT NULL,
	m_status number(3) DEFAULT '0' NOT NULL,
	album_count number(8) DEFAULT '0' NOT NULL,
	album_unlimited number(3) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_gallery_roles PRIMARY KEY (role_id)
)
/


CREATE SEQUENCE phpbb_gallery_roles_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_gallery_roles
BEFORE INSERT ON phpbb_gallery_roles
FOR EACH ROW WHEN (
	new.role_id IS NULL OR new.role_id = 0
)
BEGIN
	SELECT phpbb_gallery_roles_seq.nextval
	INTO :new.role_id
	FROM dual;
END;
/


/*
	Table: 'phpbb_gallery_users'
*/
CREATE TABLE phpbb_gallery_users (
	user_id number(8) DEFAULT '0' NOT NULL,
	watch_own number(3) DEFAULT '0' NOT NULL,
	watch_favo number(3) DEFAULT '0' NOT NULL,
	watch_com number(3) DEFAULT '0' NOT NULL,
	user_images number(8) DEFAULT '0' NOT NULL,
	personal_album_id number(8) DEFAULT '0' NOT NULL,
	user_lastmark number(11) DEFAULT '0' NOT NULL,
	user_last_update number(11) DEFAULT '0' NOT NULL,
	user_viewexif number(1) DEFAULT '0' NOT NULL,
	user_permissions clob DEFAULT '' ,
	CONSTRAINT pk_phpbb_gallery_users PRIMARY KEY (user_id)
)
/


/*
	Table: 'phpbb_gallery_watch'
*/
CREATE TABLE phpbb_gallery_watch (
	watch_id number(8) NOT NULL,
	album_id number(8) DEFAULT '0' NOT NULL,
	image_id number(8) DEFAULT '0' NOT NULL,
	user_id number(8) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_gallery_watch PRIMARY KEY (watch_id)
)
/

CREATE INDEX phpbb_gallery_watch_user_id ON phpbb_gallery_watch (user_id)
/
CREATE INDEX phpbb_gallery_watch_image_id ON phpbb_gallery_watch (image_id)
/
CREATE INDEX phpbb_gallery_watch_album_id ON phpbb_gallery_watch (album_id)
/

CREATE SEQUENCE phpbb_gallery_watch_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_gallery_watch
BEFORE INSERT ON phpbb_gallery_watch
FOR EACH ROW WHEN (
	new.watch_id IS NULL OR new.watch_id = 0
)
BEGIN
	SELECT phpbb_gallery_watch_seq.nextval
	INTO :new.watch_id
	FROM dual;
END;
/


