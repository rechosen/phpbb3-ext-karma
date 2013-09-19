<?php
/**
*
* @package phpBB Karma
* @copyright (c) 2013 phpBB
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

interface phpbb_ext_phpbb_karma_includes_type_interface
{
	/**
	 * Get the url of the specified item
	 * 
	 * @param	$item_id	The ID of the item
	 * @return	string		A url to the specified item
	 */
	public function get_url($item_id);

	/**
	 * Get the title of the specified item
	 * 
	 * @param	$item_id	The ID of the item
	 * @return	string		The title of the specified item
	 */
	public function get_title($item_id);

	/**
	 * Get the user_id, username and user_colour of the author of the specified item
	 * 
	 * @param	$item_id	The ID of the item
	 * @return	array		The user_id, username and user_colour of the author of the specified item
	 */
	public function get_author($item_id);

	/**
	 * Get the timestamp of the last edit of the specified item
	 * 
	 * @param	$item_id	The ID of the item
	 * @return	int			The timestamp of the last edit of the specified item
	 */
	public function get_last_edit($item_id);

	/**
	 * Checks if the current user has permission to read the specified item
	 * 
	 * @param	$item_id	The ID of the item
	 * @return	bool		Whether the current user has reading permissions
	 */
	public function check_permission($item_id);
}
