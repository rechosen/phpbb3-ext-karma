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

		// Check if the user is allowed to give karma
		if (!$this->auth->acl_get('u_givekarma'))
		{
			if ($this->user->data['user_id'] != ANONYMOUS)
			{
				trigger_error('SORRY_AUTH_KARMA');
			}

			login_box('', $this->user->lang['LOGIN_GIVEKARMA']);
		}

		// Check if somebody else's karma is to be edited
		$giving_user_id = $this->request->variable('giver', 0);
		if ($giving_user_id !== 0)
		{
			// Check if this user has the appropriate moderator permissions
			// TODO is an m_edit_karma permission appropriate?
			if (!$this->auth->acl_get('m_karma_report'))
			{
				trigger_error('SORRY_AUTH_KARMA_EDIT');
			}

			$moderator_edit = true;
		}
		else
		{
			// This user is giving karma as him/herself
			$giving_user_id = $this->user->data['user_id'];
			$moderator_edit = false;
		}

		// Get an instance of the karma manager
		$karma_manager = $this->container->get('karma.includes.manager');

		// Retrieve info about the item karma is given on
		try
		{
			$item_data = $karma_manager->get_item_data($karma_type_name, $item_id);
		}
		catch (Exception $e)
		{
			trigger_error($e->getMessage());
		}

		// Prevent the user from giving karma on his/her own item
		// TODO should I also prevent moderators from editing karma given to their own items? If I don't, a moderator gone bad might make all karma to him/herself positive
		if (!$moderator_edit && $giving_user_id == $item_data['author']['user_id'])
		{
			trigger_error('NO_SELF_KARMA');
		}

		// Check if the user has already given karma on this item
		$given_karma = $karma_manager->get_given_karma_row($karma_type_name, $item_id, $giving_user_id);
		if ($given_karma !== false)
		{
			// Karma was already given, so we're editing it now.
			// TODO this would be the place to check if editing karma is allowed on this board :)

			// If this is a moderator edit, check if the karma was reported
			// Moderators may not edit unreported karma
			if ($moderator_edit && !$given_karma['karma_reported'])
			{
				// Do not give an alternative error message as that yield the information that this user gave karma on the item
				trigger_error('NO_KARMA');
			}

			$edit = true;
			$karma_score = $given_karma['karma_score'];
			$karma_comment = $given_karma['karma_comment'];
			$title = $this->user->lang['KARMA_EDIT_KARMA'];
			$giving_karma_text = $this->user->lang['KARMA_EDITING_KARMA'];
			$giving_user = ($moderator_edit)
				? get_username_string('full', $giving_user_id, $given_karma['giving_username'], $given_karma['giving_user_colour'])
				: $this->user->lang['KARMA_EDITING_KARMA_YOU'];
			$karma_given_text = $this->user->lang['KARMA_KARMA_EDITED'];
		}
		else
		{
			// Check if this is supposed to be a moderator edit
			if ($moderator_edit)
			{
				// Do not allow moderators to give new karma in somebody else's name
				trigger_error('NO_KARMA');
			}

			$edit = false;
			$title = $this->user->lang['KARMA_GIVE_KARMA'];
			$giving_karma_text = $this->user->lang['KARMA_GIVING_KARMA'];
			$giving_user = ''; // Not used
			$karma_given_text = $this->user->lang['KARMA_KARMA_GIVEN'];
		}

		// Handle the form submission if appropriate
		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('give_karma_form'))
			{
				trigger_error('FORM_INVALID');
			}

			$error = $this->validate_and_store_karma($karma_type_name, $item_id, $giving_user_id);

			if (empty($error))
			{
				// Show the success page and redirect after three seconds
				meta_refresh(3, $item_data['url']);
				$message = $karma_given_text . '<br /><br />' . sprintf($this->user->lang['KARMA_VIEW_ITEM'], "<a href=\"{$item_data['url']}\">", '</a>');
				trigger_error($message);
			}
		}

		// Set the template variables to display the form
		$item_link = "<a href=\"{$item_data['url']}\">{$item_data['title']}</a>";
		$receiving_user = get_username_string('full', $item_data['author']['user_id'], $item_data['author']['username'], $item_data['author']['user_colour']);
		if (!$edit)
		{
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
		}
		$template_vars = array(
			'L_TITLE'				=> $title,
			'ERROR'					=> (!empty($error)) ? implode('<br />', $error) : '',
			'KARMA_GIVING_KARMA'	=> sprintf($giving_karma_text, $giving_user, $item_link, $receiving_user),
			'KARMA_COMMENT'			=> ($edit) ? $karma_comment : $this->request->variable('karma_comment', ''),
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
		return $this->helper->render('givekarma_body.html', $title, 200);
	}

	/**
	 * Validates the POST variables and, if successful, stores the karma
	 * 
	 * @return	array	An array of form errors to be displayed to the user
	 */
	private function validate_and_store_karma($karma_type_name, $item_id, $giving_user_id)
	{
		$error = array();

		// Validate the input TODO make karma_comment required depending on a setting
		$karma_score = $this->request->variable('karma_score', 0);
		if ($karma_score == 0)
		{
			// TODO perhaps a score of 0 could be used to delete given karma?
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
				$karma_manager->store_karma($karma_type_name, $item_id, $giving_user_id, $karma_score, $this->request->variable('karma_comment', ''));
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
