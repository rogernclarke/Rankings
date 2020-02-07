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
 * Rider Controller for Rankings component
 *
 * @since 2.0
 */
class RankingsControllerRider extends RankingsControllerDefault
{
	/**
	 * Display the rider.
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
			'resultspending',
			'ttrides',
			'hcrides',
			'ttawards',
			'hcawards',
			'ttriderhistories',
			'hcriderhistories'
		);

		// Set redirect if POST
		if ($this->input->getMethod() == 'POST')
		{
			// Redirect post to get
			$app 	= JFactory::getApplication('site');
			$jform 	= $app->input->get('jform', array(), 'array');

			$this->setRedirect(JRoute::_(RankingsHelperRoute::getRiderRoute($this->input->getInt('cid'), $jform['filter_year']), false));

			return $this;
		}
		else
		{
			return parent::display('rider', $modelNames);
		}
	}
}
