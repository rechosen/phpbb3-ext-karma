/*

 Do NOT manually edit this file!

*/

BEGIN TRANSACTION
GO

/*
	Table: 'phpbb_karma'
*/
CREATE TABLE [phpbb_karma] (
	[item_id] [int] DEFAULT (0) NOT NULL ,
	[karma_type_id] [int] DEFAULT (0) NOT NULL ,
	[giving_user_id] [int] DEFAULT (0) NOT NULL ,
	[receiving_user_id] [int] DEFAULT (0) NOT NULL ,
	[karma_score] [int] DEFAULT (0) NOT NULL ,
	[karma_time] [int] DEFAULT (0) NOT NULL ,
	[karma_comment] [varchar] (4000) DEFAULT ('') NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_karma] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_karma] PRIMARY KEY  CLUSTERED 
	(
		[item_id],
		[karma_type_id],
		[giving_user_id]
	)  ON [PRIMARY] 
GO


/*
	Table: 'phpbb_karma_types'
*/
CREATE TABLE [phpbb_karma_types] (
	[karma_type_id] [int] IDENTITY (1, 1) NOT NULL ,
	[karma_type_name] [varchar] (255) DEFAULT ('') NOT NULL ,
	[karma_type_enabled] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_karma_types] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_karma_types] PRIMARY KEY  CLUSTERED 
	(
		[karma_type_id]
	)  ON [PRIMARY] 
GO



COMMIT
GO

