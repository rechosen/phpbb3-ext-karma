<?php
/**
*
* @package phpBB Gallery Testing
* @copyright (c) 2013 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

require_once dirname(__FILE__) . '/../tree/nestedset_album_base.php';

class phpbb_ext_gallery_tests_album_nestedset_base extends phpbb_ext_gallery_tests_tree_nestedset_album_base
{
	protected $factory,
		$container;

	public function setUp()
	{
		parent::setUp();

		// Container
		$this->container = new phpbb_mock_container_builder();
		$this->container->set('gallery.album.nestedset', $this->set);

		$type_collection = array();
		$types = array('category');
		foreach ($types as $type)
		{
			$class_name = 'phpbb_ext_gallery_core_album_type_' . $type;
			$type_class = new $class_name($this->db, $this->set, 'phpbb_gallery_albums');
			$type_collection[] = $type_class;
			$this->container->set('gallery.album.type.' . $type, $type_class);
		}

		$this->factory = new phpbb_ext_gallery_core_album_factory(
			$this->db, $this->container, $type_collection, 'phpbb_gallery_albums'
		);
	}
}
