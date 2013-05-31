<?php
/**
*
* @package phpBB Gallery Testing
* @copyright (c) 2013 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

require_once dirname(__FILE__) . '/nestedset_base.php';

class phpbb_ext_gallery_tests_album_base_delete_test extends phpbb_ext_gallery_tests_album_nestedset_base
{
	public function delete_data()
	{
		return array(
			array(1, array(1, 2, 3), array(
				array('album_id' => 4, 'parent_id' => 0, 'user_id' => 0, 'left_id' => 1, 'right_id' => 6),
				array('album_id' => 5, 'parent_id' => 4, 'user_id' => 0, 'left_id' => 2, 'right_id' => 5),
				array('album_id' => 6, 'parent_id' => 5, 'user_id' => 0, 'left_id' => 3, 'right_id' => 4),
				array('album_id' => 7, 'parent_id' => 0, 'user_id' => 0, 'left_id' => 7, 'right_id' => 16),
				array('album_id' => 8, 'parent_id' => 7, 'user_id' => 0, 'left_id' => 8, 'right_id' => 9),
				array('album_id' => 9, 'parent_id' => 7, 'user_id' => 0, 'left_id' => 10, 'right_id' => 13),
				array('album_id' => 10, 'parent_id' => 9, 'user_id' => 0, 'left_id' => 11, 'right_id' => 12),
				array('album_id' => 11, 'parent_id' => 7, 'user_id' => 0, 'left_id' => 14, 'right_id' => 15),
				array('album_id' => 12, 'parent_id' => 0, 'user_id' => 2, 'left_id' => 1, 'right_id' => 6),
				array('album_id' => 13, 'parent_id' => 12, 'user_id' => 2, 'left_id' => 2, 'right_id' => 3),
				array('album_id' => 14, 'parent_id' => 12, 'user_id' => 2, 'left_id' => 4, 'right_id' => 5),
			)),
			array(2, array(2), array(
				array('album_id' => 1, 'parent_id' => 0, 'user_id' => 0, 'left_id' => 1, 'right_id' => 4),
				array('album_id' => 3, 'parent_id' => 1, 'user_id' => 0, 'left_id' => 2, 'right_id' => 3),
				array('album_id' => 4, 'parent_id' => 0, 'user_id' => 0, 'left_id' => 5, 'right_id' => 10),
				array('album_id' => 5, 'parent_id' => 4, 'user_id' => 0, 'left_id' => 6, 'right_id' => 9),
				array('album_id' => 6, 'parent_id' => 5, 'user_id' => 0, 'left_id' => 7, 'right_id' => 8),
				array('album_id' => 7, 'parent_id' => 0, 'user_id' => 0, 'left_id' => 11, 'right_id' => 20),
				array('album_id' => 8, 'parent_id' => 7, 'user_id' => 0, 'left_id' => 12, 'right_id' => 13),
				array('album_id' => 9, 'parent_id' => 7, 'user_id' => 0, 'left_id' => 14, 'right_id' => 17),
				array('album_id' => 10, 'parent_id' => 9, 'user_id' => 0, 'left_id' => 15, 'right_id' => 16),
				array('album_id' => 11, 'parent_id' => 7, 'user_id' => 0, 'left_id' => 18, 'right_id' => 19),
				array('album_id' => 12, 'parent_id' => 0, 'user_id' => 2, 'left_id' => 1, 'right_id' => 6),
				array('album_id' => 13, 'parent_id' => 12, 'user_id' => 2, 'left_id' => 2, 'right_id' => 3),
				array('album_id' => 14, 'parent_id' => 12, 'user_id' => 2, 'left_id' => 4, 'right_id' => 5),
			)),
		);
	}

	/**
	* @dataProvider delete_data
	*/
	public function test_delete($album_id, $expected_deleted, $expected)
	{
		$album = $this->factory->get($album_id);
		$this->assertEquals($expected_deleted, $album->delete());

		$result = $this->db->sql_query("SELECT album_id, parent_id, user_id, left_id, right_id
			FROM phpbb_gallery_albums
			ORDER BY user_id, left_id, album_id ASC");
		$this->assertEquals($expected, $this->db->sql_fetchrowset($result));
	}
}
