<?php
/**
*
* @package phpBB Gallery Testing
* @copyright (c) 2013 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_gallery_tests_album_types_test extends phpbb_ext_gallery_database_test_case
{
	public function getDataSet()
	{
		return $this->createXMLDataSet(dirname(__FILE__) . '/fixtures/albums.xml');
	}

	protected $factory;

	public function setUp()
	{
		parent::setUp();

		// Container
		$container = new phpbb_mock_container_builder();

		$config = new phpbb_config(array('phpbb_gallery_album_lock' => 0));
		set_config(null, null, null, $config);
		$lock = new phpbb_lock_db('phpbb_gallery_album_lock', $config, $this->db);
		$nestedset = new phpbb_ext_gallery_core_album_nestedset($this->db, $lock, 'phpbb_gallery_albums');
		$container->set('gallery.album.nestedset', $nestedset);

		$type_collection = array();
		$types = array('album', 'category');
		foreach ($types as $type)
		{
			$class_name = 'phpbb_ext_gallery_core_album_type_' . $type;
			$type_class = new $class_name($this->db, $nestedset, 'phpbb_gallery_albums');
			$type_collection[] = $type_class;
			$container->set('gallery.album.type.' . $type, $type_class);
		}

		$this->factory = new phpbb_ext_gallery_core_album_factory(
			$this->db, $container, $type_collection, 'phpbb_gallery_albums'
		);
	}

	public function can_images_data()
	{
		return array(
			array('album', true, true),
			array('category', false, false),
		);
	}

	/**
	* @dataProvider can_images_data
	*/
	public function test_can_images($type, $expected_contain, $expected_upload)
	{
		$album = $this->factory->create($type);

		// Values were changed
		$this->assertEquals($expected_contain, $album->can_contain_images());
		$this->assertEquals($expected_upload, $album->can_upload_images());
	}
}
