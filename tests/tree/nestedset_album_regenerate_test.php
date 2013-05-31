<?php
/**
*
* @package phpBB Gallery Testing
* @copyright (c) 2013 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

require_once dirname(__FILE__) . '/nestedset_album_base.php';

class phpbb_ext_gallery_tests_tree_nestedset_album_recalculate_test extends phpbb_ext_gallery_tests_tree_nestedset_album_base
{
	protected $fixed_set = array(
		array('album_id' => 1, 'parent_id' => 0, 'user_id' => 0, 'left_id' => 1, 'right_id' => 6, 'album_parents' => ''),
		array('album_id' => 2, 'parent_id' => 1, 'user_id' => 0, 'left_id' => 2, 'right_id' => 3, 'album_parents' => ''),
		array('album_id' => 3, 'parent_id' => 1, 'user_id' => 0, 'left_id' => 4, 'right_id' => 5, 'album_parents' => ''),

		array('album_id' => 4, 'parent_id' => 0, 'user_id' => 0, 'left_id' => 7, 'right_id' => 12, 'album_parents' => ''),
		array('album_id' => 5, 'parent_id' => 4, 'user_id' => 0, 'left_id' => 8, 'right_id' => 11, 'album_parents' => ''),
		array('album_id' => 6, 'parent_id' => 5, 'user_id' => 0, 'left_id' => 9, 'right_id' => 10, 'album_parents' => ''),

		array('album_id' => 7, 'parent_id' => 0, 'user_id' => 0, 'left_id' => 13, 'right_id' => 22, 'album_parents' => ''),
		array('album_id' => 8, 'parent_id' => 7, 'user_id' => 0, 'left_id' => 14, 'right_id' => 15, 'album_parents' => ''),
		array('album_id' => 9, 'parent_id' => 7, 'user_id' => 0, 'left_id' => 16, 'right_id' => 19, 'album_parents' => ''),
		array('album_id' => 10, 'parent_id' => 9, 'user_id' => 0, 'left_id' => 17, 'right_id' => 18, 'album_parents' => ''),
		array('album_id' => 11, 'parent_id' => 7, 'user_id' => 0, 'left_id' => 20, 'right_id' => 21, 'album_parents' => ''),

		array('album_id' => 12, 'parent_id' => 0, 'user_id' => 2, 'left_id' => 1, 'right_id' => 6, 'album_parents' => 'a:0:{}'),
		array('album_id' => 13, 'parent_id' => 12, 'user_id' => 2, 'left_id' => 2, 'right_id' => 3, 'album_parents' => 'a:0:{}'),
		array('album_id' => 14, 'parent_id' => 12, 'user_id' => 2, 'left_id' => 4, 'right_id' => 5, 'album_parents' => 'a:0:{}'),
	);

	public function regenerate_left_right_ids_data()
	{
		return array(
			array('UPDATE phpbb_gallery_albums
				SET left_id = 0,
					right_id = 0
				WHERE user_id = 0', false),
			array('UPDATE phpbb_gallery_albums
				SET left_id = 28,
					right_id = 28
				WHERE user_id = 0
					AND left_id > 12', false),
			array('UPDATE phpbb_gallery_albums
				SET left_id = left_id * 2,
					right_id = right_id * 2
				WHERE user_id = 0', false),
			array('UPDATE phpbb_gallery_albums
				SET left_id = left_id * 2,
					right_id = right_id * 2
				WHERE user_id = 0
					AND left_id > 12', false),
			array('UPDATE phpbb_gallery_albums
				SET left_id = left_id - 4,
					right_id = right_id * 4
				WHERE user_id = 0
					AND left_id > 4', false),
			array('UPDATE phpbb_gallery_albums
				SET left_id = 0,
					right_id = 0
				WHERE user_id = 0
					AND left_id > 12', true),
		);
	}

	/**
	* @dataProvider regenerate_left_right_ids_data
	*/
	public function test_regenerate_left_right_ids($breaking_query, $reset_ids)
	{
		$result = $this->db->sql_query($breaking_query);

		$this->assertEquals(23, $this->set->regenerate_left_right_ids(1, 0, $reset_ids));

		$result = $this->db->sql_query('SELECT album_id, parent_id, user_id, left_id, right_id, album_parents
			FROM phpbb_gallery_albums
			ORDER BY user_id, left_id, album_id ASC');
		$this->assertEquals($this->fixed_set, $this->db->sql_fetchrowset($result));
	}
}
