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

class phpbb_ext_phpbb_karma_controller_reportkarma
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
	public function handle($karma_id)
	{
		// Load the extension's language file and the posting language file
		$this->user->add_lang_ext('phpbb/karma', 'karma');

		// Get the necessary instances
		$karma_manager = $this->container->get('karma.includes.manager');
		$karma_report_model = $this->container->get('karma.includes.report_model');
		
		// Retrieve info about the karma being reported, checking if it exists at the same time
		if (!($karma_data = $karma_manager->get_karma_data($karma_id)))
		{
			trigger_error('NO_KARMA');
		}

		// Check if the user isn't trying to report karma given to someone else
		if ($karma_data['receiving_user_id'] != $this->user->data['user_id'])
		{
			trigger_error('NO_REPORT_OTHERS_KARMA');
		}

		// Check if this karma has already been reported
		if ($karma_data['reported'])
		{
			trigger_error('KARMA_ALREADY_REPORTED');
		}

		// Handle the form submission if appropriate
		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('report_karma_form'))
			{
				trigger_error('FORM_INVALID');
			}

			$error = $this->validate_and_store_karma_report($karma_id);

			if (empty($error))
			{
				// Show the success page and redirect after three seconds
				$redirect = append_sid("{$this->phpbb_root_path}ucp.{$this->php_ext}", 'i=phpbb_ext_phpbb_karma_ucp_received_karma&amp;mode=overview');
				meta_refresh(3, $redirect);
				$message = $this->user->lang['KARMA_KARMA_REPORTED'] . '<br /><br />' . sprintf($this->user->lang['RETURN_PAGE'], "<a href=\"$redirect\">", '</a>');
				// TODO generation of <a>'s is inconsistent right now; sometimes the template does it, and sometimes the sprintf
				trigger_error($message);
			}
		}

		// Set the template variables to display the form
		$block_row = array();
		foreach ($karma_data as $key => $value)
		{
			$block_row[strtoupper($key)] = $value;
		}
		$this->template->assign_block_vars('received_karma', $block_row);
		$template_vars = array(
			'ERROR'					=> (!empty($error)) ? implode('<br />', $error) : '',
			'KARMA_REPORT_TEXT'		=> $this->request->variable('karma_report_text', ''),
		);
		$this->template->assign_vars($template_vars);

		// Set a form key
		add_form_key('report_karma_form');

		/*
		* The render method takes up to three other arguments
		* @param string Name of the template file to display
		* Template files are searched for two places:
		* - phpBB/styles/<style_name>/template/
		* - phpBB/ext/<all_active_extensions>/styles/<style_name>/template/
		* @param string Page title
		* @param int Status code of the page (200 - OK [ default ], 403 - Unauthorized, 404 - Page not found)
		*/
		return $this->helper->render('reportkarma_body.html', $this->user->lang['KARMA_REPORT_KARMA'], 200);
	}

	/**
	 * Validates the POST variables and, if successful, stores the karma report
	 * 
	 * @param	int		$karma_id	The ID of the karma being reported
	 * @return	array	An array of form errors to be displayed to the user
	 */
	private function validate_and_store_karma_report($karma_id)
	{
		$error = array();

		// Validate the input
		$karma_report_text = $this->request->variable('karma_report_text', '');
		if (empty($karma_report_text))
		{
			$error[] = 'KARMA_REPORT_TEXT_EMPTY';
		}

		// Store the karma report into the database
		if (empty($error))
		{
			$karma_report_model = $this->container->get('karma.includes.report_model');
			try
			{
				$karma_report_model->report_karma($karma_id, $this->user->data['user_id'], $karma_report_text);
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
}
