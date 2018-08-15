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
class RankingsViewsRideHtml extends JViewHtml
{
	/**
	 * Display the Ride view
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
                // Get the ride
                $this->ride = $this->model->getItem();
            break;

            case "list":
            default:
                // List the riders
                $this->rides = $this->model->listItems();
                $this->_ridesListView = $viewHelper->load('Ride');
                //$this->_rankingsListView = $viewHelper->load('Rankings','_entry','phtml');

                // Get the total number of rides
                //$this->totalCourses = $this->model->getTotal();
            break;
        }
        	
		//display
		return parent::render();
	}
}