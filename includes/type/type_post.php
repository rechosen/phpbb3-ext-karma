<?php
/**
*
* @package phpBB Karma
* @copyright (c) 2013 phpBB
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbb\karma\includes\type;

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

class type_post extends \phpbb\karma\includes\type\type_base implements \phpbb\karma\includes\type\type_interface
{
	/**
	* Get the url of the specified item
	* 
	* @param	$item_id	The ID of the item
	* @return	string		A url to the specified item
	*/
	public function get_url($item_id)
	{
		// Get the topic_id of this post TODO make this code more DRY
		$sql_array = array(
			'SELECT'	=> 'topic_id',
			'FROM'		=> array(POSTS_TABLE => 'p'),
			'WHERE'		=> 'post_id = ' . (int) $item_id,
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$topic_id = $this->db->sql_fetchfield('topic_id');
		$this->db->sql_freeresult($result);
		if ($topic_id === false) {
			throw new OutOfBoundsException('NO_POST');
		}

		return append_sid($this->phpbb_root_path . 'viewtopic.' . $this->php_ext, "t=$topic_id&amp;p=$item_id") . "#p$item_id";
	}

	/**
	* Get the title of the specified item
	* 
	* @param	$item_id	The ID of the item
	* @return	string		The title of the specified item
	*/
	public function get_title($item_id)
	{
		$sql_array = array(
			'SELECT'	=> 'post_subject',
			'FROM'		=> array(POSTS_TABLE => 'p'),
			'WHERE'		=> 'post_id = ' . (int) $item_id,
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$title = $this->db->sql_fetchfield('post_subject');
		$this->db->sql_freeresult($result);
		if ($title === false) {
			throw new OutOfBoundsException('NO_POST');
		}

		return $title;
	}

	/**
	* Get the user_id, username and user_colour of the author of the specified item
	* 
	* @param	$item_id	The ID of the item
	* @return	array		The user_id, username and user_colour of the author of the specified item
	*/
	public function get_author($item_id)
	{
		$sql_array = array(
			'SELECT'	=> 'u.user_id, u.username, u.user_colour',
			'FROM'		=> array(
				POSTS_TABLE => 'p',
				USERS_TABLE => 'u',
			),
			'WHERE'		=> 'p.poster_id = u.user_id
							AND post_id = ' . (int) $item_id,
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$author = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		if ($author === false) {
			throw new OutOfBoundsException('NO_POST');
		}

		return $author;
	}

	/**
	* Get the timestamp of the last edit of the specified item
	* 
	* @param	$item_id	The ID of the item
	* @return	int			The timestamp of the last edit of the specified item
	*/
	public function get_last_edit($item_id)
	{
		$sql_array = array(
			'SELECT'	=> 'post_edit_time',
			'FROM'		=> array(POSTS_TABLE => 'p'),
			'WHERE'		=> 'post_id = ' . (int) $item_id,
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$last_edit = $this->db->sql_fetchfield('post_edit_time');
		$this->db->sql_freeresult($result);
		if ($last_edit === false) {
			throw new OutOfBoundsException('NO_POST');
		}

		return $last_edit;
	}

	/**
	* Checks if the current user has permission to read the specified item
	* 
	* @param	$item_id	The ID of the item
	* @return	bool		Whether the current user has reading permissions
	*/
	public function check_permission($item_id)
	{
		// Get the forum_id of this post TODO make this code more DRY
		$sql_array = array(
			'SELECT'	=> 'forum_id',
			'FROM'		=> array(POSTS_TABLE => 'p'),
			'WHERE'		=> 'post_id = ' . (int) $item_id,
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$forum_id = $this->db->sql_fetchfield('forum_id');
		$this->db->sql_freeresult($result);
		if ($forum_id === false)
		{
			throw new OutOfBoundsException('NO_POST');
		}

		// Check if the user has read permissions for this post
		if (!$this->auth->acl_get('f_read', $forum_id))
		{
			if ($this->user->data['user_id'] != ANONYMOUS)
			{
				trigger_error('SORRY_AUTH_READ');
			}

			login_box('', $this->user->lang['LOGIN_VIEWFORUM']); // TODO if login_forum_box can be avoided, avoid this as well
		}

		// Check if the forum is password-protected but no password was entered yet
		// TODO this query could be avoided by using a JOIN earlier
		$sql_array = array(
			'SELECT'	=> 'forum_password, forum_name',
			'FROM'		=> array(FORUMS_TABLE => 'f'),
			'WHERE'		=> 'forum_id = ' . (int) $forum_id,
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$forum_password = $this->db->sql_fetchfield('forum_password');
		$forum_name = $this->db->sql_fetchfield('forum_name');
		$this->db->sql_freeresult($result);
		if ($forum_password === false) {
			throw new OutOfBoundsException('NO_TOPIC');
		}

		// TODO There must be a way to check this without overriding output with a password form
		if ($forum_password)
		{
			login_forum_box(array(
				'forum_id'			=> $forum_id,
				'forum_password'	=> $forum_password,
				'forum_name'		=> $forum_name,
			));
		}

		return true;
	}
}
