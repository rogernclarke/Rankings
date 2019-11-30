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
 * Rankings Component Controller
 *
 * @since 2.0
 */
class RankingsController extends JControllerLegacy
{
	/**
	 * Method to display an item view.
	 *
	 * @return  RankingsController 	 This object to support chaining.
	 *
	 * @since   2.0
	 */
	public function display()
	{
		$viewName = $this->input->get('view', 'list');
		$this->input->set('view', $viewName);

		if ($this->input->getMethod() == 'POST')
		{
			$cacheable = false;

			$this->setRedirect(JRoute::_('index.php?option=com_rankings&view=' . $viewName), false);
		}

		$safeurlparams = array(
			'id'               => 'INT',
			'limit'            => 'UINT',
			'limitstart'       => 'UINT',
			'filter_order'     => 'CMD',
			'filter_order_Dir' => 'CMD',
			'lang'             => 'CMD'
		);

		return parent::display($cacheable, $safeurlparams);
	}
}
