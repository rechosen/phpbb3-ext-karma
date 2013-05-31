<?php
/**
*
* @package phpBB Gallery Testing
* @copyright (c) 2013 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

require_once dirname(__FILE__) . '/nestedset_album_base.php';

class phpbb_ext_gallery_tests_tree_nestedset_album_basic_test extends phpbb_ext_gallery_tests_tree_nestedset_album_base
{
	public function album_constructor_data()
	{
		return array(
			array(array(
				array('album_id' => 1, 'parent_id' => 0, 'user_id' => 0, 'left_id' => 1, 'right_id' => 6, 'album_parents' => 'a:0:{}'),
				array('album_id' => 2, 'parent_id' => 1, 'user_id' => 0, 'left_id' => 2, 'right_id' => 3, 'album_parents' => 'a:0:{}'),
				array('album_id' => 3, 'parent_id' => 1, 'user_id' => 0, 'left_id' => 4, 'right_id' => 5, 'album_parents' => 'a:0:{}'),

				array('album_id' => 4, 'parent_id' => 0, 'user_id' => 0, 'left_id' => 7, 'right_id' => 12, 'album_parents' => 'a:0:{}'),
				array('album_id' => 5, 'parent_id' => 4, 'user_id' => 0, 'left_id' => 8, 'right_id' => 11, 'album_parents' => 'a:0:{}'),
				array('album_id' => 6, 'parent_id' => 5, 'user_id' => 0, 'left_id' => 9, 'right_id' => 10, 'album_parents' => 'a:0:{}'),

				array('album_id' => 7, 'parent_id' => 0, 'user_id' => 0, 'left_id' => 13, 'right_id' => 22, 'album_parents' => 'a:0:{}'),
				array('album_id' => 8, 'parent_id' => 7, 'user_id' => 0, 'left_id' => 14, 'right_id' => 15, 'album_parents' => 'a:0:{}'),
				array('album_id' => 9, 'parent_id' => 7, 'user_id' => 0, 'left_id' => 16, 'right_id' => 19, 'album_parents' => 'a:0:{}'),
				array('album_id' => 10, 'parent_id' => 9, 'user_id' => 0, 'left_id' => 17, 'right_id' => 18, 'album_parents' => 'a:0:{}'),
				array('album_id' => 11, 'parent_id' => 7, 'user_id' => 0, 'left_id' => 20, 'right_id' => 21, 'album_parents' => 'a:0:{}'),

				array('album_id' => 12, 'parent_id' => 0, 'user_id' => 2, 'left_id' => 1, 'right_id' => 6, 'album_parents' => 'a:0:{}'),
				array('album_id' => 13, 'parent_id' => 12, 'user_id' => 2, 'left_id' => 2, 'right_id' => 3, 'album_parents' => 'a:0:{}'),
				array('album_id' => 14, 'parent_id' => 12, 'user_id' => 2, 'left_id' => 4, 'right_id' => 5, 'album_parents' => 'a:0:{}'),
			)),
		);
	}

	/**
	* @dataProvider album_constructor_data
	*/
	public function test_album_constructor($expected)
	{
		$result = $this->db->sql_query('SELECT album_id, parent_id, user_id, left_id, right_id, album_parents
			FROM phpbb_gallery_albums
			ORDER BY user_id, left_id, album_id ASC');
		$this->assertEquals($expected, $this->db->sql_fetchrowset($result));
	}

	public function get_sql_where_data()
	{
		return array(
			array('SELECT album_id
				FROM phpbb_gallery_albums
				%s
				ORDER BY album_id ASC',
				'WHERE', '', array(
				array('album_id' => 1),
				array('album_id' => 2),
				array('album_id' => 3),

				array('album_id' => 4),
				array('album_id' => 5),
				array('album_id' => 6),

				array('album_id' => 7),
				array('album_id' => 8),
				array('album_id' => 9),
				array('album_id' => 10),
				array('album_id' => 11),
			)),
			array('SELECT a.album_id
				FROM phpbb_gallery_albums a
				%s
				ORDER BY a.album_id ASC',
				'WHERE', 'a.', array(
				array('album_id' => 1),
				array('album_id' => 2),
				array('album_id' => 3),

				array('album_id' => 4),
				array('album_id' => 5),
				array('album_id' => 6),

				array('album_id' => 7),
				array('album_id' => 8),
				array('album_id' => 9),
				array('album_id' => 10),
				array('album_id' => 11),
			)),
			array('SELECT album_id
				FROM phpbb_gallery_albums
				WHERE album_id < 4 %s
				ORDER BY album_id ASC',
				'AND', '', array(
				array('album_id' => 1),
				array('album_id' => 2),
				array('album_id' => 3),
			)),
			array('SELECT a.album_id
				FROM phpbb_gallery_albums a
				WHERE a.album_id < 4 %s
				ORDER BY a.album_id ASC',
				'AND', 'a.', array(
				array('album_id' => 1),
				array('album_id' => 2),
				array('album_id' => 3),
			)),
		);
	}

	/**
	* @dataProvider get_sql_where_data
	*/
	public function test_get_sql_where($sql_query, $operator, $column_prefix, $expected)
	{
		$result = $this->db->sql_query(sprintf($sql_query, $this->set->get_sql_where($operator, $column_prefix)));
		$this->assertEquals($expected, $this->db->sql_fetchrowset($result));
	}
}
