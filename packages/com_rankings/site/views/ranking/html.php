<?php
/**
 * Rankings Component for Joomla 3.x
 * 
 * @version    1.8
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the Rankings Component - Ranking
 */
class RankingsViewsRankingHtml extends JViewHtml
{
	/**
	 * Display the Ranking view
	 *
	 * @return void
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
            break;

            case "list":
            default:
                // List the rankings
                $this->rankings = $this->model->listItems();

                // Handle pagination
                $this->pagination = $this->model->getPagination();

                // Get parameters
                $this->params = $this->model->getParams();
                
                // Get the list state
                $this->state = $this->model->getState();
                
                // Load the part html views
                $this->_rankingView = $viewHelper->load('ranking','_ranking','phtml');
                $this->_rideView = $viewHelper->load('ranking','_ride','phtml');

                // Get the total number of rankings
                $this->totalRankings = $this->model->getTotal();

                // Get the form
                $this->form = $this->model->getForm();
            break;
        }
        	
		//display
		return parent::render();
	}
}