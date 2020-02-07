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
 * Statistics Dashboard View
 *
 * @since 2.0
 */
class RankingsViewDashboard extends JViewLegacy
{
	/**
	 * Display the Dashboard view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed 	A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{
		// Get the model
		$model = $this->getModel('statistics');

		// Get the parameters
		$this->params = $model->getState('params');

		// Get the form
		$this->form = $model->getForm('statistics');

		// Get the model state
		$this->state = $this->get('State');

		// Get some data from the model

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			\JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		// Prepare document
		$this->setDocumentTitle($this->params->get('page_title'));

		return parent::display($tpl);
	}
}
