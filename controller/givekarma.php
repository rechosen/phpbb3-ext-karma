<?php
/**
*
* @package phpBB Karma
* @copyright (c) 2013 rechosen
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

use Symfony\Component\DependencyInjection\ContainerBuilder;

class phpbb_ext_phpbb_karma_controller_givekarma
{
	// TODO add variable declarations

	/**
	* Constructor
	* NOTE: The parameters of this method must match in order and type with
	* the dependencies defined in the services.yml file for this service.
	*
	* @param phpbb_auth					$auth				Auth object
	* @param ContainerBuilder			$container			Container object
	* @param phpbb_db_driver			$db					Database object
	* @param phpbb_request				$request			Request object
	* @param phpbb_template				$template			Template object
	* @param phpbb_user					$user				User object
	* @param phpbb_controller_helper	$helper				Controller helper object
	* @param string						$phpbb_root_path	phpBB root path
	* @param string						$php_ext			phpEx
	*/
	public function __construct(phpbb_auth $auth, ContainerBuilder $container, phpbb_db_driver $db, phpbb_request $request, phpbb_template $template, phpbb_user $user, phpbb_controller_helper $helper, $phpbb_root_path, $php_ext)
	{
		$this->auth = $auth;
		$this->container = $container;
		$this->db = $db;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	/**
	* @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function handle($karma_type_name, $item_id)
	{
		// Load the extension's language file and the posting language file
		$this->user->add_lang_ext('phpbb/karma', 'karma');
		$this->user->add_lang(array('posting'));

		// Get an instance of the karma manager
		$karma_manager = $this->container->get('karma.includes.manager');

		// Retrieve info about the item karma is given on
		try
		{
			$item_data = $karma_manager->get_item_data($karma_type_name, $item_id);
		}
		catch (Exception $e)
		{
			trigger_error(e.getMessage());
		}

		// Handle the form submission if appropriate
		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('give_karma_form'))
			{
				trigger_error('FORM_INVALID');
			}

			$error = $this->validate_and_store_karma($karma_type_name, $item_id);

			if (empty($error))
			{
				// Show the success page and redirect after three seconds
				meta_refresh(3, $item_data['url']);
				$message = $this->user->lang['KARMA_KARMA_GIVEN'] . '<br /><br />' . sprintf($this->user->lang['KARMA_VIEW_ITEM'], '<a href="' . $item_data['url'] . '">', '</a>');
				trigger_error($message);
			}
		}

		// Set the template variables to display the form
		$item_link = "<a href=\"{$item_data['url']}\">{$item_data['title']}</a>";
		$receiving_user = $this->get_full_username_string($item_data['author']);
		$karma_score = $this->request->variable('karma_score', 0);
		if ($karma_score === 0)
		{
			$get_score = $this->request->variable('score', '');
			if ($get_score === 'positive')
			{
				$karma_score = 1;
			}
			if ($get_score === 'negative')
			{
				$karma_score = -1;
			}
		}
		$template_vars = array(
			'ERROR'					=> (!empty($error)) ? implode('<br />', $error) : '',
			'KARMA_GIVING_KARMA'	=> sprintf($this->user->lang['KARMA_GIVING_KARMA'], $item_link, $receiving_user),
			'KARMA_COMMENT'			=> $this->request->variable('karma_comment', ''),
			'KARMA_SCORE'			=> $karma_score,
		);
		$this->template->assign_vars($template_vars);

		// Set a form key
		add_form_key('give_karma_form');

		/*
		* The render method takes up to three other arguments
		* @param string Name of the template file to display
		* Template files are searched for two places:
		* - phpBB/styles/<style_name>/template/
		* - phpBB/ext/<all_active_extensions>/styles/<style_name>/template/
		* @param string Page title
		* @param int Status code of the page (200 - OK [ default ], 403 - Unauthorized, 404 - Page not found)
		*/
		return $this->helper->render('givekarma_body.html', $this->user->lang['KARMA_GIVE_KARMA'], 200);
	}

	/**
	 * Validates the POST variables and, if successful, stores the karma
	 * 
	 * @return	array	An array of form errors to be displayed to the user
	 */
	private function validate_and_store_karma($karma_type_name, $item_id)
	{
		$error = array();

		// Validate the input TODO make karma_comment required depending on a setting
		// TODO prevent the anonymous user from giving karma?
		$karma_score = $this->request->variable('karma_score', 0);
		if ($karma_score == 0)
		{
			$error[] = 'KARMA_SCORE_INVALID';
		}
		else
		{
			// Any positive number becomes 1, any negative number -1
			// TODO change this when implementing weighted karma scores
			$karma_score /= abs($karma_score);
		}

		// Store the karma into the database
		if (empty($error))
		{
			$karma_manager = $this->container->get('karma.includes.manager');
			try
			{
				$karma_manager->store_karma($karma_type_name, $item_id, $this->user->data['user_id'], $karma_score, $this->request->variable('karma_comment', ''));
			}
			catch (Exception $e)
			{
				trigger_error($e->getMessage());
			}
		}

		// Replace "error" strings with their real, localised form
		$error = array_map(array($this->user, 'lang'), $error);

		return $error;
	}

	/**
	 * Get a linked and colored string of the username belonging with the specified user_id
	 * 
	 * @param	int	$user_id	The ID of the user to get the username string of
	 * @return	string			A linked an colored username string, obtained from get_username_string('full', ...)
	 */
	private function get_full_username_string($user_id)
	{
		$sql_array = array(
			'SELECT'	=> 'username, user_colour',
			'FROM'		=> array(USERS_TABLE => 'u'),
			'WHERE'		=> 'user_id = ' . (int) $user_id,
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$username = $this->db->sql_fetchfield('username');
		$user_colour = $this->db->sql_fetchfield('user_colour');
		$this->db->sql_freeresult($result);

		return get_username_string('full', $user_id, $username, $user_colour);
	}
}
