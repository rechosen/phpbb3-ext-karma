<?php
/**
*
* @package phpBB Gallery Testing
* @copyright (c) 2013 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_gallery_tests_tree_nestedset_album_base extends phpbb_ext_gallery_database_test_case
{
	public function getDataSet()
	{
		return $this->createXMLDataSet(dirname(__FILE__) . '/fixtures/phpbb_gallery_albums.xml');
	}

	protected $album_data = array(
		// \__/
		1	=> array('album_id' => 1, 'parent_id' => 0, 'user_id' => 0, 'left_id' => 1, 'right_id' => 6, 'album_parents' => 'a:0:{}'),
		2	=> array('album_id' => 2, 'parent_id' => 1, 'user_id' => 0, 'left_id' => 2, 'right_id' => 3, 'album_parents' => 'a:0:{}'),
		3	=> array('album_id' => 3, 'parent_id' => 1, 'user_id' => 0, 'left_id' => 4, 'right_id' => 5, 'album_parents' => 'a:0:{}'),

		// \  /
		//  \/
		4	=> array('album_id' => 4, 'parent_id' => 0, 'user_id' => 0, 'left_id' => 7, 'right_id' => 12, 'album_parents' => 'a:0:{}'),
		5	=> array('album_id' => 5, 'parent_id' => 4, 'user_id' => 0, 'left_id' => 8, 'right_id' => 11, 'album_parents' => 'a:0:{}'),
		6	=> array('album_id' => 6, 'parent_id' => 5, 'user_id' => 0, 'left_id' => 9, 'right_id' => 10, 'album_parents' => 'a:0:{}'),

		// \_  _/
		//   \/
		7	=> array('album_id' => 7, 'parent_id' => 0, 'user_id' => 0, 'left_id' => 13, 'right_id' => 22, 'album_parents' => 'a:0:{}'),
		8	=> array('album_id' => 8, 'parent_id' => 7, 'user_id' => 0, 'left_id' => 14, 'right_id' => 15, 'album_parents' => 'a:0:{}'),
		9	=> array('album_id' => 9, 'parent_id' => 7, 'user_id' => 0, 'left_id' => 16, 'right_id' => 19, 'album_parents' => 'a:0:{}'),
		10	=> array('album_id' => 10, 'parent_id' => 9, 'user_id' => 0, 'left_id' => 17, 'right_id' => 18, 'album_parents' => 'a:0:{}'),
		11	=> array('album_id' => 11, 'parent_id' => 7, 'user_id' => 0, 'left_id' => 20, 'right_id' => 21, 'album_parents' => 'a:0:{}'),

		// \__/
		12	=> array('album_id' => 12, 'parent_id' => 0, 'user_id' => 2, 'left_id' => 1, 'right_id' => 6, 'album_parents' => 'a:0:{}'),
		13	=> array('album_id' => 13, 'parent_id' => 12, 'user_id' => 2, 'left_id' => 2, 'right_id' => 3, 'album_parents' => 'a:0:{}'),
		14	=> array('album_id' => 14, 'parent_id' => 12, 'user_id' => 2, 'left_id' => 4, 'right_id' => 5, 'album_parents' => 'a:0:{}'),

		// Albums that do not exist
		0	=> array('album_id' => 0, 'parent_id' => 0, 'user_id' => 0, 'left_id' => 0, 'right_id' => 0, 'album_parents' => 'a:0:{}'),
		200	=> array('album_id' => 200, 'parent_id' => 0, 'user_id' => 0, 'left_id' => 0, 'right_id' => 0, 'album_parents' => 'a:0:{}'),
	);

	protected $set,
		$config,
		$lock;

	public function setUp()
	{
		parent::setUp();

		global $config;

		$config = new phpbb_config(array('phpbb_gallery_album_lock' => 0));
		set_config(null, null, null, $config);

		$this->lock = new phpbb_lock_db('phpbb_gallery_album_lock', $config, $this->db);
		$this->set = new phpbb_ext_gallery_core_album_nestedset($this->db, $this->lock, 'phpbb_gallery_albums');

		$this->set_up_albums();

		$this->set->set_user_id(0);

		$sql = "UPDATE phpbb_gallery_albums
			SET album_parents = 'a:0:{}'";
		$this->db->sql_query($sql);
	}

	protected function set_up_albums()
	{
		$this->create_album('Parent with two flat children');
		$this->create_album('Flat child #1', 1);
		$this->create_album('Flat child #2', 1);

		$this->create_album('Parent with two nested children');
		$this->create_album('Nested child #1', 4);
		$this->create_album('Nested child #2', 5);

		$this->create_album('Parent with flat and nested children');
		$this->create_album('Mixed child #1', 7);
		$this->create_album('Mixed child #2', 7);
		$this->create_album('Nested child #1 of Mixed child #2', 9);
		$this->create_album('Mixed child #3', 7);

		$this->set->set_user_id(2);
		$this->create_album('User: Parent with two flat children');
		$this->create_album('User: Flat child #1', 12);
		$this->create_album('User: Flat child #2', 12);
	}

	protected function create_album($name, $parent_id = 0)
	{
		$album = $this->set->insert(array('album_name' => $name, 'album_type' => 'category', 'album_desc' => ''));
		$this->set->change_parent($album['album_id'], $parent_id);
	}
}
