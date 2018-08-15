<?php
/**
 * Rankings Component for Joomla 3.x
 * 
 * @version    0.0.1
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Rider View for the Rankings Component
 */
class RankingsViewsRiderHtml extends JViewHtml
{
	/**
	 * Display the Rider view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function render()
	{
		// Get the input
        $jinput = JFactory::getApplication()->input;

        // Create new helper
        $viewHelper = new RankingsHelpersView();

        switch($this->layout)
        {
            case "display":
                // Get the rider
                $this->rider = $this->model->getItem();
            break;

            case "list":
            default:
                // List the riders
                $this->riders = $this->model->listItems();
                $this->_ridersListView = $viewHelper->load('Rider');

                // Handle pagination
                $this->pagination = $this->model->getPagination();

                // Get the list state
                $this->state = $this->model->getState();
                
                //$this->_rankingsListView = $viewHelper->load('Rankings','_entry','phtml');

                // Get the total number of riders
                //$this->totalCourses = $this->model->getTotal();
            break;
        }
        	
		//display
		return parent::render();
	}
}