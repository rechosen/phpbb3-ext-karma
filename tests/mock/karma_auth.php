<?php
/**
*
* @package phpBB Karma Testing
* @copyright (c) 2013 phpBB
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_mock_karma_auth extends phpbb_auth
{
	function acl_get_list($user_id = false, $opts = false, $forum_id = false)
	{
		$user_id = (!is_array($user_id)) ? array($user_id) : $user_id;
		$opts = (!is_array($opts)) ? array($opts) : $opts;
		$forum_id = (!is_array($forum_id)) ? array($forum_id) : $forum_id;

		$auth_list = array();

		foreach ($forum_id as $fid)
		{
			foreach ($opts as $opt)
			{
				$auth_list[$fid][$opt] = array();

				foreach ($user_id as $uid)
				{
					$auth_list[$fid][$opt][] = $uid;
				}
			}
		}

		return $auth_list;
	}

	function acl_get($opt, $f = 0)
	{
		return true;
	}
}
