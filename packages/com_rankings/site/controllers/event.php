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
class RankingsControllerEvent extends JControllerLegacy
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
		// Get the ID from the request
		$id	= $this->input->getInt('cid');

		// Get the model
		$model = $this->getModel('Event', '', array('ignore_request' => true));
		//$modelLink->setState('filter.published', 1);

		// Get the rider
		$rider = $model->getItem($id);

		// Increment the hits on the rider
		$model->hit();

		// Set the view name
		$viewName = JFile::makeSafe($this->input->get('view', 'list'));
		$this->input->set('view', 'Event');

		// Display view
		$cacheable 		= true;
		$safeurlparams  = array(
			'cid' => 'INT',
		);

		return parent::display($cacheable, $safeurlparams);
	}
}
