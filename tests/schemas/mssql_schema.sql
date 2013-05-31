/*

 Do NOT manually edit this file!

*/

BEGIN TRANSACTION
GO

/*
	Table: 'phpbb_gallery_albums'
*/
CREATE TABLE [phpbb_gallery_albums] (
	[album_id] [int] IDENTITY (1, 1) NOT NULL ,
	[parent_id] [int] DEFAULT (0) NOT NULL ,
	[left_id] [int] DEFAULT (1) NOT NULL ,
	[right_id] [int] DEFAULT (2) NOT NULL ,
	[user_id] [int] DEFAULT (0) NOT NULL ,
	[album_parents] [text] DEFAULT ('') NOT NULL ,
	[album_type] [varchar] (255) DEFAULT ('') NOT NULL ,
	[album_status] [int] DEFAULT (1) NOT NULL ,
	[album_contest] [int] DEFAULT (0) NOT NULL ,
	[album_name] [varchar] (255) DEFAULT ('') NOT NULL ,
	[album_desc] [text] DEFAULT ('') NOT NULL ,
	[album_desc_options] [int] DEFAULT (7) NOT NULL ,
	[album_desc_uid] [varchar] (8) DEFAULT ('') NOT NULL ,
	[album_desc_bitfield] [varchar] (255) DEFAULT ('') NOT NULL ,
	[album_images] [int] DEFAULT (0) NOT NULL ,
	[album_images_real] [int] DEFAULT (0) NOT NULL ,
	[album_last_image_id] [int] DEFAULT (0) NOT NULL ,
	[album_image] [varchar] (255) DEFAULT ('') NOT NULL ,
	[album_last_image_time] [int] DEFAULT (0) NOT NULL ,
	[album_last_image_name] [varchar] (255) DEFAULT ('') NOT NULL ,
	[album_last_username] [varchar] (255) DEFAULT ('') NOT NULL ,
	[album_last_user_colour] [varchar] (6) DEFAULT ('') NOT NULL ,
	[album_last_user_id] [int] DEFAULT (0) NOT NULL ,
	[album_watermark] [int] DEFAULT (1) NOT NULL ,
	[album_sort_key] [varchar] (8) DEFAULT ('') NOT NULL ,
	[album_sort_dir] [varchar] (8) DEFAULT ('') NOT NULL ,
	[display_in_rrc] [int] DEFAULT (1) NOT NULL ,
	[display_on_index] [int] DEFAULT (1) NOT NULL ,
	[display_subalbum_list] [int] DEFAULT (1) NOT NULL 
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO

ALTER TABLE [phpbb_gallery_albums] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_gallery_albums] PRIMARY KEY  CLUSTERED 
	(
		[album_id]
	)  ON [PRIMARY] 
GO


/*
	Table: 'phpbb_gallery_albums_track'
*/
CREATE TABLE [phpbb_gallery_albums_track] (
	[user_id] [int] DEFAULT (0) NOT NULL ,
	[album_id] [int] DEFAULT (0) NOT NULL ,
	[mark_time] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_gallery_albums_track] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_gallery_albums_track] PRIMARY KEY  CLUSTERED 
	(
		[user_id],
		[album_id]
	)  ON [PRIMARY] 
GO


/*
	Table: 'phpbb_gallery_comments'
*/
CREATE TABLE [phpbb_gallery_comments] (
	[comment_id] [int] IDENTITY (1, 1) NOT NULL ,
	[comment_image_id] [int] NOT NULL ,
	[comment_user_id] [int] DEFAULT (0) NOT NULL ,
	[comment_username] [varchar] (255) DEFAULT ('') NOT NULL ,
	[comment_user_colour] [varchar] (6) DEFAULT ('') NOT NULL ,
	[comment_user_ip] [varchar] (40) DEFAULT ('') NOT NULL ,
	[comment_time] [int] DEFAULT (0) NOT NULL ,
	[comment] [text] DEFAULT ('') NOT NULL ,
	[comment_uid] [varchar] (8) DEFAULT ('') NOT NULL ,
	[comment_bitfield] [varchar] (255) DEFAULT ('') NOT NULL ,
	[comment_edit_time] [int] DEFAULT (0) NOT NULL ,
	[comment_edit_count] [int] DEFAULT (0) NOT NULL ,
	[comment_edit_user_id] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO

ALTER TABLE [phpbb_gallery_comments] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_gallery_comments] PRIMARY KEY  CLUSTERED 
	(
		[comment_id]
	)  ON [PRIMARY] 
GO

CREATE  INDEX [comment_image_id] ON [phpbb_gallery_comments]([comment_image_id]) ON [PRIMARY]
GO

CREATE  INDEX [comment_user_id] ON [phpbb_gallery_comments]([comment_user_id]) ON [PRIMARY]
GO

CREATE  INDEX [comment_user_ip] ON [phpbb_gallery_comments]([comment_user_ip]) ON [PRIMARY]
GO

CREATE  INDEX [comment_time] ON [phpbb_gallery_comments]([comment_time]) ON [PRIMARY]
GO


/*
	Table: 'phpbb_gallery_config'
*/
CREATE TABLE [phpbb_gallery_config] (
	[config_name] [varchar] (255) DEFAULT ('') NOT NULL ,
	[config_value] [varchar] (255) DEFAULT ('') NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_gallery_config] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_gallery_config] PRIMARY KEY  CLUSTERED 
	(
		[config_name]
	)  ON [PRIMARY] 
GO


/*
	Table: 'phpbb_gallery_contests'
*/
CREATE TABLE [phpbb_gallery_contests] (
	[contest_id] [int] IDENTITY (1, 1) NOT NULL ,
	[contest_album_id] [int] DEFAULT (0) NOT NULL ,
	[contest_start] [int] DEFAULT (0) NOT NULL ,
	[contest_rating] [int] DEFAULT (0) NOT NULL ,
	[contest_end] [int] DEFAULT (0) NOT NULL ,
	[contest_marked] [int] DEFAULT (0) NOT NULL ,
	[contest_first] [int] DEFAULT (0) NOT NULL ,
	[contest_second] [int] DEFAULT (0) NOT NULL ,
	[contest_third] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_gallery_contests] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_gallery_contests] PRIMARY KEY  CLUSTERED 
	(
		[contest_id]
	)  ON [PRIMARY] 
GO


/*
	Table: 'phpbb_gallery_copyts_albums'
*/
CREATE TABLE [phpbb_gallery_copyts_albums] (
	[album_id] [int] IDENTITY (1, 1) NOT NULL ,
	[parent_id] [int] DEFAULT (0) NOT NULL ,
	[left_id] [int] DEFAULT (1) NOT NULL ,
	[right_id] [int] DEFAULT (2) NOT NULL ,
	[album_name] [varchar] (255) DEFAULT ('') NOT NULL ,
	[album_desc] [text] DEFAULT ('') NOT NULL ,
	[album_user_id] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO

ALTER TABLE [phpbb_gallery_copyts_albums] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_gallery_copyts_albums] PRIMARY KEY  CLUSTERED 
	(
		[album_id]
	)  ON [PRIMARY] 
GO


/*
	Table: 'phpbb_gallery_copyts_users'
*/
CREATE TABLE [phpbb_gallery_copyts_users] (
	[user_id] [int] DEFAULT (0) NOT NULL ,
	[personal_album_id] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_gallery_copyts_users] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_gallery_copyts_users] PRIMARY KEY  CLUSTERED 
	(
		[user_id]
	)  ON [PRIMARY] 
GO


/*
	Table: 'phpbb_gallery_favorites'
*/
CREATE TABLE [phpbb_gallery_favorites] (
	[favorite_id] [int] IDENTITY (1, 1) NOT NULL ,
	[user_id] [int] DEFAULT (0) NOT NULL ,
	[image_id] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_gallery_favorites] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_gallery_favorites] PRIMARY KEY  CLUSTERED 
	(
		[favorite_id]
	)  ON [PRIMARY] 
GO

CREATE  INDEX [user_id] ON [phpbb_gallery_favorites]([user_id]) ON [PRIMARY]
GO

CREATE  INDEX [image_id] ON [phpbb_gallery_favorites]([image_id]) ON [PRIMARY]
GO


/*
	Table: 'phpbb_gallery_images'
*/
CREATE TABLE [phpbb_gallery_images] (
	[image_id] [int] IDENTITY (1, 1) NOT NULL ,
	[image_filename] [varchar] (255) DEFAULT ('') NOT NULL ,
	[image_thumbnail] [varchar] (255) DEFAULT ('') NOT NULL ,
	[image_name] [varchar] (255) DEFAULT ('') NOT NULL ,
	[image_name_clean] [varchar] (255) DEFAULT ('') NOT NULL ,
	[image_desc] [text] DEFAULT ('') NOT NULL ,
	[image_desc_uid] [varchar] (8) DEFAULT ('') NOT NULL ,
	[image_desc_bitfield] [varchar] (255) DEFAULT ('') NOT NULL ,
	[image_user_id] [int] DEFAULT (0) NOT NULL ,
	[image_username] [varchar] (255) DEFAULT ('') NOT NULL ,
	[image_username_clean] [varchar] (255) DEFAULT ('') NOT NULL ,
	[image_user_colour] [varchar] (6) DEFAULT ('') NOT NULL ,
	[image_user_ip] [varchar] (40) DEFAULT ('') NOT NULL ,
	[image_time] [int] DEFAULT (0) NOT NULL ,
	[image_album_id] [int] DEFAULT (0) NOT NULL ,
	[image_view_count] [int] DEFAULT (0) NOT NULL ,
	[image_status] [int] DEFAULT (0) NOT NULL ,
	[image_contest] [int] DEFAULT (0) NOT NULL ,
	[image_contest_end] [int] DEFAULT (0) NOT NULL ,
	[image_contest_rank] [int] DEFAULT (0) NOT NULL ,
	[image_filemissing] [int] DEFAULT (0) NOT NULL ,
	[image_has_exif] [int] DEFAULT (2) NOT NULL ,
	[image_exif_data] [varchar] (8000) DEFAULT ('') NOT NULL ,
	[image_rates] [int] DEFAULT (0) NOT NULL ,
	[image_rate_points] [int] DEFAULT (0) NOT NULL ,
	[image_rate_avg] [int] DEFAULT (0) NOT NULL ,
	[image_comments] [int] DEFAULT (0) NOT NULL ,
	[image_last_comment] [int] DEFAULT (0) NOT NULL ,
	[image_favorited] [int] DEFAULT (0) NOT NULL ,
	[image_reported] [int] DEFAULT (0) NOT NULL ,
	[filesize_upload] [int] DEFAULT (0) NOT NULL ,
	[filesize_medium] [int] DEFAULT (0) NOT NULL ,
	[filesize_cache] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO

ALTER TABLE [phpbb_gallery_images] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_gallery_images] PRIMARY KEY  CLUSTERED 
	(
		[image_id]
	)  ON [PRIMARY] 
GO

CREATE  INDEX [image_album_id] ON [phpbb_gallery_images]([image_album_id]) ON [PRIMARY]
GO

CREATE  INDEX [image_user_id] ON [phpbb_gallery_images]([image_user_id]) ON [PRIMARY]
GO

CREATE  INDEX [image_time] ON [phpbb_gallery_images]([image_time]) ON [PRIMARY]
GO


/*
	Table: 'phpbb_gallery_modscache'
*/
CREATE TABLE [phpbb_gallery_modscache] (
	[album_id] [int] DEFAULT (0) NOT NULL ,
	[user_id] [int] DEFAULT (0) NOT NULL ,
	[username] [varchar] (255) DEFAULT ('') NOT NULL ,
	[group_id] [int] DEFAULT (0) NOT NULL ,
	[group_name] [varchar] (255) DEFAULT ('') NOT NULL ,
	[display_on_index] [int] DEFAULT (1) NOT NULL 
) ON [PRIMARY]
GO

CREATE  INDEX [disp_idx] ON [phpbb_gallery_modscache]([display_on_index]) ON [PRIMARY]
GO

CREATE  INDEX [album_id] ON [phpbb_gallery_modscache]([album_id]) ON [PRIMARY]
GO


/*
	Table: 'phpbb_gallery_permissions'
*/
CREATE TABLE [phpbb_gallery_permissions] (
	[perm_id] [int] IDENTITY (1, 1) NOT NULL ,
	[perm_role_id] [int] DEFAULT (0) NOT NULL ,
	[perm_album_id] [int] DEFAULT (0) NOT NULL ,
	[perm_user_id] [int] DEFAULT (0) NOT NULL ,
	[perm_group_id] [int] DEFAULT (0) NOT NULL ,
	[perm_system] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_gallery_permissions] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_gallery_permissions] PRIMARY KEY  CLUSTERED 
	(
		[perm_id]
	)  ON [PRIMARY] 
GO


/*
	Table: 'phpbb_gallery_rates'
*/
CREATE TABLE [phpbb_gallery_rates] (
	[rate_image_id] [int] IDENTITY (1, 1) NOT NULL ,
	[rate_user_id] [int] DEFAULT (0) NOT NULL ,
	[rate_user_ip] [varchar] (40) DEFAULT ('') NOT NULL ,
	[rate_point] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY]
GO

CREATE  INDEX [rate_image_id] ON [phpbb_gallery_rates]([rate_image_id]) ON [PRIMARY]
GO

CREATE  INDEX [rate_user_id] ON [phpbb_gallery_rates]([rate_user_id]) ON [PRIMARY]
GO

CREATE  INDEX [rate_user_ip] ON [phpbb_gallery_rates]([rate_user_ip]) ON [PRIMARY]
GO

CREATE  INDEX [rate_point] ON [phpbb_gallery_rates]([rate_point]) ON [PRIMARY]
GO


/*
	Table: 'phpbb_gallery_reports'
*/
CREATE TABLE [phpbb_gallery_reports] (
	[report_id] [int] IDENTITY (1, 1) NOT NULL ,
	[report_album_id] [int] DEFAULT (0) NOT NULL ,
	[report_image_id] [int] DEFAULT (0) NOT NULL ,
	[reporter_id] [int] DEFAULT (0) NOT NULL ,
	[report_manager] [int] DEFAULT (0) NOT NULL ,
	[report_note] [text] DEFAULT ('') NOT NULL ,
	[report_time] [int] DEFAULT (0) NOT NULL ,
	[report_status] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO

ALTER TABLE [phpbb_gallery_reports] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_gallery_reports] PRIMARY KEY  CLUSTERED 
	(
		[report_id]
	)  ON [PRIMARY] 
GO


/*
	Table: 'phpbb_gallery_roles'
*/
CREATE TABLE [phpbb_gallery_roles] (
	[role_id] [int] IDENTITY (1, 1) NOT NULL ,
	[a_list] [int] DEFAULT (0) NOT NULL ,
	[i_view] [int] DEFAULT (0) NOT NULL ,
	[i_watermark] [int] DEFAULT (0) NOT NULL ,
	[i_upload] [int] DEFAULT (0) NOT NULL ,
	[i_edit] [int] DEFAULT (0) NOT NULL ,
	[i_delete] [int] DEFAULT (0) NOT NULL ,
	[i_rate] [int] DEFAULT (0) NOT NULL ,
	[i_approve] [int] DEFAULT (0) NOT NULL ,
	[i_lock] [int] DEFAULT (0) NOT NULL ,
	[i_report] [int] DEFAULT (0) NOT NULL ,
	[i_count] [int] DEFAULT (0) NOT NULL ,
	[i_unlimited] [int] DEFAULT (0) NOT NULL ,
	[c_read] [int] DEFAULT (0) NOT NULL ,
	[c_post] [int] DEFAULT (0) NOT NULL ,
	[c_edit] [int] DEFAULT (0) NOT NULL ,
	[c_delete] [int] DEFAULT (0) NOT NULL ,
	[m_comments] [int] DEFAULT (0) NOT NULL ,
	[m_delete] [int] DEFAULT (0) NOT NULL ,
	[m_edit] [int] DEFAULT (0) NOT NULL ,
	[m_move] [int] DEFAULT (0) NOT NULL ,
	[m_report] [int] DEFAULT (0) NOT NULL ,
	[m_status] [int] DEFAULT (0) NOT NULL ,
	[album_count] [int] DEFAULT (0) NOT NULL ,
	[album_unlimited] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_gallery_roles] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_gallery_roles] PRIMARY KEY  CLUSTERED 
	(
		[role_id]
	)  ON [PRIMARY] 
GO


/*
	Table: 'phpbb_gallery_users'
*/
CREATE TABLE [phpbb_gallery_users] (
	[user_id] [int] DEFAULT (0) NOT NULL ,
	[watch_own] [int] DEFAULT (0) NOT NULL ,
	[watch_favo] [int] DEFAULT (0) NOT NULL ,
	[watch_com] [int] DEFAULT (0) NOT NULL ,
	[user_images] [int] DEFAULT (0) NOT NULL ,
	[personal_album_id] [int] DEFAULT (0) NOT NULL ,
	[user_lastmark] [int] DEFAULT (0) NOT NULL ,
	[user_last_update] [int] DEFAULT (0) NOT NULL ,
	[user_viewexif] [int] DEFAULT (0) NOT NULL ,
	[user_permissions] [text] DEFAULT ('') NOT NULL 
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO

ALTER TABLE [phpbb_gallery_users] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_gallery_users] PRIMARY KEY  CLUSTERED 
	(
		[user_id]
	)  ON [PRIMARY] 
GO


/*
	Table: 'phpbb_gallery_watch'
*/
CREATE TABLE [phpbb_gallery_watch] (
	[watch_id] [int] IDENTITY (1, 1) NOT NULL ,
	[album_id] [int] DEFAULT (0) NOT NULL ,
	[image_id] [int] DEFAULT (0) NOT NULL ,
	[user_id] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_gallery_watch] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_gallery_watch] PRIMARY KEY  CLUSTERED 
	(
		[watch_id]
	)  ON [PRIMARY] 
GO

CREATE  INDEX [user_id] ON [phpbb_gallery_watch]([user_id]) ON [PRIMARY]
GO

CREATE  INDEX [image_id] ON [phpbb_gallery_watch]([image_id]) ON [PRIMARY]
GO

CREATE  INDEX [album_id] ON [phpbb_gallery_watch]([album_id]) ON [PRIMARY]
GO



COMMIT
GO

