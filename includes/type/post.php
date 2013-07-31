<?php
/**
*
* @package phpBB Karma
* @copyright (c) 2013 rechosen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

class phpbb_ext_phpbb_karma_includes_type_post extends phpbb_ext_phpbb_karma_includes_type_base implements phpbb_ext_phpbb_karma_includes_type_interface
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
	 * Get the user_id of the author of the specified item
	 * 
	 * @param	$item_id	The ID of the item
	 * @return	int			The user_id of the author of the specified item
	 */
	public function get_author($item_id)
	{
		$sql_array = array(
			'SELECT'	=> 'poster_id',
			'FROM'		=> array(POSTS_TABLE => 'p'),
			'WHERE'		=> 'post_id = ' . (int) $item_id,
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$user_id = $this->db->sql_fetchfield('poster_id');
		$this->db->sql_freeresult($result);
		if ($user_id === false) {
			throw new OutOfBoundsException('NO_POST');
		}

		return $user_id;
	}
}
