<?php
/**
*
* @package phpBB Karma
* @copyright (c) 2013 rechosen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_phpbb_karma_migrations_0_0_1 extends phpbb_db_migration
{
	public function effectively_installed()
	{
		$sql = 'SELECT config_value
				FROM ' . $this->table_prefix . "config
				WHERE config_name = 'phpbb_karma_version'";
		$result = $this->db->sql_query($sql);
		$version = $this->db->sql_fetchfield('config_value');
		$this->db->sql_freeresult($result);

		return $version && (version_compare($version, '0.0.1') >= 0);
	}

	static public function depends_on()
	{
		return array('phpbb_db_migration_data_310_dev');
	}

	public function update_schema()
	{
		$ret = array(
			'add_tables'	=> array(
				$this->table_prefix . 'karma'		=> array(
					'COLUMNS'		=> array(
						'karma_id'				=> array('UINT', NULL, 'auto_increment'),
						'karma_type_id'			=> array('UINT', 0),
						'item_id'				=> array('UINT', 0),
						'giving_user_id'		=> array('UINT', 0),
						'receiving_user_id'		=> array('UINT', 0),
						'karma_score'			=> array('TINT:4', 0),
						'karma_time'			=> array('TIMESTAMP', 0),
						'karma_comment'			=> array('TEXT_UNI', ''),
					),
					'PRIMARY_KEY'	=> 'karma_id',
					// TODO add indexes to speed up SELECT queries
				),
				$this->table_prefix . 'karma_types'	=> array(
					'COLUMNS'		=> array(
						'karma_type_id'			=> array('UINT', NULL, 'auto_increment'),
						'karma_type_name'		=> array('VCHAR:255', ''),
						'karma_type_enabled'	=> array('BOOL', 0),
					),
					'PRIMARY_KEY'	=> 'karma_type_id',
				),
			),
			'add_columns'	=> array(
				$this->table_prefix . 'users'	=> array(
					'user_karma_score'	=> array('INT:11', 0),
				),
			),
		);
		return $ret;
	}

	public function revert_schema()
	{
		return array(
			'drop_columns'	=> array(
				$this->table_prefix . 'users'	=> array(
					'user_karma_score',
				),
			),
			'drop_tables'	=> array(
				$this->table_prefix . 'karma',
				$this->table_prefix . 'karma_types',
			),
		);
	}

	public function update_data()
	{
		return array(
			array('config.add', array('phpbb_karma_version', '0.0.1')),

			// UCP module
			array('module.add', array('ucp', '', 'UCP_KARMA')),
			array('module.add', array('ucp', 'UCP_KARMA', array(
				'module_basename'	=> 'phpbb_ext_phpbb_karma_ucp_received_karma',
				'module_langname'	=> 'UCP_RECEIVED_KARMA',
				'module_mode'		=> 'overview',
				'module_auth'		=> '',
			))),
		);
	}
}
