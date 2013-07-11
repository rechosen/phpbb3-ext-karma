<?php
/**
*
* @package phpBB Karma Testing
* @copyright (c) 2013 rechosen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_phpbb_karma_tests_karma_karma_test extends phpbb_ext_phpbb_karma_database_test_case
{
	public function getDataSet()
	{
		return $this->createXMLDataSet(dirname(__FILE__) . '/fixtures/karma.xml');
	}

	protected $karma_model;

	public function setUp()
	{
		parent::setUp();

		$this->karma_model = new phpbb_ext_phpbb_karma_includes_karma_model(
			$this->db, 'phpbb_karma'
		);
	}

	public function create_data()
	{
		// List all the options that should be tested
		$big_number = 1000000;
		$testoptions = array(
			'post_id' => array(1, -1, $big_number),
			'giving_user_id' => array(2, -1, $big_number),
			'receiving_user_id' => array(2, -1, $big_number),
			'karma_score' => array(1, -1, -128, 127),
			'karma_time' => array(0, time(), $big_number),
			'karma_comment' => array('', str_repeat('Long comment.', 100)),
			// TODO test values that _should_ go wrong and catch errors
			// Attain full code coverage
		);

		// Generate testing rows so that every option is tested at least once
		$max_options = 0;
		foreach ($testoptions as $options)
		{
			if (sizeof($options) > $max_options)
			{
				$max_options = sizeof($options);
			}
		}
		$return = array();
		for ($i = 0; $i < $max_options; $i++) {
			$row = array();
			foreach ($testoptions as $field => $options) {
				$row[$field] = $options[min($i, sizeof($options))];
			}
			$return[] = array($row);
		}

		return $return;
	}

	/**
	 * @dataProvider create_data
	 */
	public function test_store_karma($karma)
	{
		$this->karma_model->store_karma($karma);

		$sql_ary = $karma;
		$sql = 'SELECT COUNT(*) AS num_rows FROM phpbb_karma WHERE ' . $this->db->sql_build_array('SELECT', $sql_ary);
		$result = $this->db->sql_query($sql);
		$this->assertEquals(1, $this->db->sql_fetchfield('num_rows'));
		$this->db->sql_freeresult($result);	
	}
}
