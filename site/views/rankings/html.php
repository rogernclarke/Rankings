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
 * HTML View class for the Rankings Component
 */
class RankingsViewsRankingsHtml extends JViewHtml
{
	/**
	 * Display the Rankings view
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
                // List the rankings
                $this->rankings = $this->model->listItems();
                $this->_rankingsListView = $viewHelper->load('Rankings');

                // Handle pagination
                $this->pagination = $this->model->getPagination();
                
                //$this->_rankingsListView = $viewHelper->load('Rankings','_entry','phtml');

                // Get the total number of riders
                //$this->totalCourses = $this->model->getTotal();
            break;
        }
        	
		//display
		return parent::render();
	}
}