<?php
/**
 * Rankings Component for Joomla 3.x
 *
 * @version    2.0
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) 2019 Spindata. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Rankings Component Hill Climb Riderhistories Model
 *
 * @since 2.0
 */
class RankingsModelHcriderhistories extends RankingsModelRiderhistories
{
	/**
	 * Constructor.
	 *
	 * @param   array  	$config  	An optional associative array of configuration settings.
	 *
	 * @see     \JModelList
	 * @since   2.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->prefix = 'hc_';
	}
}
