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
	/**
	 * Auth object
	 * @var phpbb_auth
	 */
	protected $auth;

	/**
	 * Container object
	 * @var ContainerBuilder
	 */
	protected $container;

	/**
	 * Request object
	 * @var phpbb_request
	 */
	protected $request;

	/**
	 * Template object
	 * @var phpbb_template
	 */
	protected $template;

	/**
	 * User object
	 * @var phpbb_user
	 */
	protected $user;

	/**
	 * Controller helper object
	 * @var phpbb_controller_helper
	 */
	protected $helper;

	/**
	* Constructor
	* NOTE: The parameters of this method must match in order and type with
	* the dependencies defined in the services.yml file for this service.
	*
	* @param phpbb_auth					$auth				Auth object
	* @param ContainerBuilder			$container			Container object
	* @param phpbb_request				$request			Request object
	* @param phpbb_template				$template			Template object
	* @param phpbb_user					$user				User object
	* @param phpbb_controller_helper	$helper				Controller helper object
	*/
	public function __construct(phpbb_auth $auth, ContainerBuilder $container, phpbb_request $request, phpbb_template $template, phpbb_user $user, phpbb_controller_helper $helper)
	{
		$this->auth = $auth;
		$this->container = $container;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
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
			if (!$this->auth->acl_get('m_karma_edit'))
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
			// If this is a normal edit, check if the user may edit his/her karma
			if (!$moderator_edit && !$this->auth->acl_get('u_karma_edit'))
			{
				trigger_error('SORRY_AUTH_KARMA_EDIT');
			}

			// If this is a moderator edit, check if the karma was reported
			// Moderators may not edit unreported karma
			if ($moderator_edit && !$given_karma['karma_reported'])
			{
				// For security reasons, do not give a specific error message as that
				// would yield the information that this user gave karma on the item
				trigger_error('NO_KARMA');
			}

			$edit = true;
			$deletion_allowed = $this->auth->acl_get(($moderator_edit) ? 'm_karma_delete' : 'u_karma_delete');
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
			// No given karma exists, so the user is trying to give new karma
			// Check if this is supposed to be a moderator edit
			if ($moderator_edit)
			{
				// Do not allow moderators to give new karma in somebody else's name
				trigger_error('NO_KARMA');
			}

			$edit = false;
			$deletion_allowed = false; // No sense in allowing deletion of something that isn't there yet
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

			$store_result = $this->validate_and_store_karma($karma_type_name, $item_id, $giving_user_id, $deletion_allowed);
			if ($store_result['karma_deleted'])
			{
				$karma_given_text = $this->user->lang['KARMA_KARMA_DELETED']; // TODO rename this and friends to KARMA_SUCCESSFULLY_*
			}
			$error = $store_result['error'];

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
			'S_DELETE_KARMA'		=> $deletion_allowed,
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
	 * @param	string	$karma_type_name	The name of the type of the item on which the karma is given
	 * @param	int		$item_id			The ID of the item on which karma is given
	 * @param	int		$giving_user_id		The ID of the user giving this karma
	 * @param	bool	$deletion_allowed	Whether the current user is allowed to delete this karma
	 * @return	array	An array with the following key-value pairs:
	 * 						'karma_deleted' => (bool) Whether the karma was deleted or not
	 * 						'error' => (array) An array of form errors to be displayed to the user
	 */
	private function validate_and_store_karma($karma_type_name, $item_id, $giving_user_id, $deletion_allowed)
	{
		$karma_deleted = false;
		$error = array();

		// Validate the input TODO make karma_comment required depending on a setting
		// Do _not_ use a default of 0 for the karma score, as any stray input then deletes the karma
		$karma_score = $this->request->variable('karma_score', '');
		if (((string) ((int) $karma_score)) !== $karma_score)
		{
			$error[] = 'KARMA_SCORE_INVALID';
		}
		
		// Now we may safely cast the karma score to an int and use it as such
		$karma_score = (int) $karma_score;
		if ($karma_score !== 0)
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
				if ($karma_score === 0)
				{
					if (!$deletion_allowed)
					{
						// For security reasons, do not give a specific error message as that
						// could yield the information that this user gave karma on the item
						// Besides, you can't end up here normally without delete permissions
						trigger_error('NO_KARMA');
					}

					$karma_manager->delete_karma($karma_type_name, $item_id, $giving_user_id);
					$karma_deleted = true;
				}
				else
				{
					$karma_manager->store_karma($karma_type_name, $item_id, $giving_user_id, $karma_score, $this->request->variable('karma_comment', ''));
				}
			}
			catch (Exception $e)
			{
				trigger_error($e->getMessage());
			}
		}

		// Replace "error" strings with their real, localised form
		$error = array_map(array($this->user, 'lang'), $error);

		return array(
			'karma_deleted'	=> $karma_deleted,
			'error'			=> $error,
		);
	}
}
