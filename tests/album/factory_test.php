<?php
/**
*
* @package phpBB Gallery Testing
* @copyright (c) 2013 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_gallery_tests_album_factory_test extends phpbb_ext_gallery_database_test_case
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

	public function test_get_types()
	{
		$this->assertEquals(array(
			'album'		=> 'GALLERY_ALBUM_TYPE_ALBUM',
			'category'	=> 'GALLERY_ALBUM_TYPE_CATEGORY',
		), $this->factory->get_types());
	}

	public function create_data()
	{
		return array(
			array('album'),
			array('category'),
		);
	}

	/**
	* @dataProvider create_data
	*/
	public function test_create($type)
	{
		$this->assertInstanceOf('phpbb_ext_gallery_core_album_type_' . $type, $this->factory->create($type));
	}

	/**
	* @expectedException			OutOfBoundsException
	* @expectedExceptionMessage		GALLERY_ALBUM_INVALID_TYPE
	*/
	public function test_create_not_exist()
	{
		$this->factory->create('does_not_exist');
	}

	public function test_get()
	{
		$album = $this->factory->get(1);
		$this->assertInstanceOf('phpbb_ext_gallery_core_album_type_category', $album);
		$this->assertEquals(1, $album->get('id'));
	}

	/**
	* @expectedException			OutOfBoundsException
	* @expectedExceptionMessage		GALLERY_ALBUM_INVALID_ITEM
	*/
	public function test_get_not_exist()
	{
		$this->factory->get(3);
	}

	/**
	* @expectedException			OutOfBoundsException
	* @expectedExceptionMessage		GALLERY_ALBUM_INVALID_TYPE
	*/
	public function test_get_type_not_exist()
	{
		$this->factory->get(2);
	}

	public function validate_type_data()
	{
		return array(
			array('category', true),
			array('does_not_exist', false),
		);
	}

	/**
	* @dataProvider validate_type_data
	*/
	public function test_validate_type($type, $expected)
	{
		$this->assertEquals($expected, $this->factory->validate_type($type));
	}
}
