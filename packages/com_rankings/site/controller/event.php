<?php
/**
 * Rankings Component for Joomla 3.x
 *
 * @version    2.0
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) 2019 Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Event Controller for Rankings component
 *
 * @since 2.0
 */
class RankingsControllerEvent extends RankingsControllerDefault
{
	/**
	 * Display the event.
	 *
	 * @return  boolean  True
	 *
	 * @since 2.0
	 */
	public function display()
	{
		// Specify additional models required
		$modelNames = array(
			'entries',
			'results',
			'awards'
		);

		return parent::display('event', $modelNames);
	}
}
