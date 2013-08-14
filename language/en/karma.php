<?php
/**
*
* @package phpBB Karma
* @copyright (c) 2013 rechosen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(
	'KARMA'						=> 'Karma',
	'KARMA_GIVING_KARMA'		=> 'You’re giving karma on %1$s by %2$s.',
	'KARMA_SCORE'				=> 'Score',
	'KARMA_POSITIVE'			=> 'Positive',
	'KARMA_NEGATIVE'			=> 'Negative',
	'KARMA_COMMENT'				=> 'Comment',
	'KARMA_SCORE_OUTOFBOUNDS'	=> 'Invalid karma score.',
	'KARMA_TIME_TOO_LARGE'		=> 'Karma time too large.',
	'KARMA_SCORE_INVALID'		=> 'Please select a karma score.',
	'KARMA_KARMA_GIVEN'			=> 'Karma successfully given.',
	'KARMA_VIEW_ITEM'			=> '%sView the item you gave karma on%s',
	'NO_KARMA_TYPE'				=> 'Karma type %s does not exist.',
	'GIVEKARMA_POSITIVE'		=> 'Give this post a positive karma score.',
	'GIVEKARMA_NEGATIVE'		=> 'Give this post a negative karma score.',
	'UCP_KARMA'					=> 'Karma',
	'UCP_RECENT_KARMA'			=> 'Recent karma',

));
