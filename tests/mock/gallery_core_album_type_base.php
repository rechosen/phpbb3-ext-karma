<?php
/**
*
* @package phpBB Gallery Core
* @copyright (c) 2013 nickvergessen
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

class phpbb_mock_gallery_core_album_type_base extends phpbb_ext_gallery_core_album_base
{
	/**
	* @inheritdoc
	*/
	public function get_type_id()
	{
		return 'base';
	}

	/**
	* @inheritdoc
	*/
	public function get_type_name()
	{
		return 'BASE';
	}

	/**
	* @inheritdoc
	*/
	public function can_contain_images()
	{
		return false;
	}

	/**
	* @inheritdoc
	*/
	public function can_upload_images()
	{
		return false;
	}
}
