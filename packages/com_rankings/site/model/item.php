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
 * Rankings Component Default Item Model
 *
 * @since 2.0
 */
class RankingsModelItem extends JModelItem
{
	/**
	 * Context string for the model type.  This is used to handle uniqueness when dealing with the getStoreId() method and caching data structures.
	 *
	 * @var    string
	 * @since  2.0
	 */
	protected $context = null;

	/**
	 * Id of the item
	 *
	 * @var    integer
	 * @since  2.0
	 */
	protected $id = null;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     \JModelBaseDatabase
	 * @since   2.0
	 */
	public function __construct($config = array())
	{
		// Specify database connection parameters
		$app 			= JFactory::getApplication();
		$params 		= $app->getParams('com_rankings');
		$config['dbo'] 	= JDatabaseDriver::getInstance($params);

		parent::__construct($config);

		// Guess the context as Option.ModelName.
		if (empty($this->context))
		{
			$this->context = strtolower($this->option . '.' . $this->getName());
		}

		// Auto-populate the model state.
		$this->populateState();

		// Set the model state set flag to true.
		$this->__state_set = true;
	}

	/**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @param 	integer $id 	Id of requested event
	 *
	 * @return  mixed  Object on success, false on failure.
	 *
	 * @since   2.0
	 */
	public function getItem($id=null)
	{
		// Use the id if specified, otherwise read it from the model state
		$this->id = (!empty($id)) ? $id : (int) $this->getState($this->getName() . '.id');

		if ($this->_item === null)
		{
			$this->_item = array();
		}

		if (!isset($this->_item[$this->id]))
		{
			try
			{
				// Create a new query object.
				$db 	= $this->getDbo();
				$query 	= $db->getQuery(true);

				$query = $this->getQuerySelect($db, $query);
				$query = $this->getQueryFrom($db, $query);
				$query = $this->getQueryFilters($db, $query);

				// JFactory::getApplication()->enqueueMessage(JText::_('Item query: ' . $query), 'debug');

				$db->setQuery($query);

				$data = $db->loadObject();

				if (empty($data))
				{
					JError::raiseError(404, JText::_('COM_RANKINGS_ERROR_ITEM_NOT_FOUND'));
				}

				$this->_item[$id] = $data;
			}
			catch (Exception $e)
			{
				$this->setError($e);
				$this->_item[$id] = false;
			}
		}

		return $this->_item[$id];
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return 	void
	 *
	 * @since 2.0
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('site');

		// Load the object state.
		$id = $app->input->getInt('cid');
		$this->setState($this->getName() . '.id', $id);

		// Load the parameters.
		//$params = $app->getParams();
		//$this->setState('params', $params);
	}

	/**
	 * Method to set the item identifier
	 *
	 * @param   integer 	$id 	Item ID
	 *
	 * @return 	void
	 *
	 * @since 2.0
	 */
	public function setId($id)
	{
		// Set item id
		$this->id = $id;
	}
}
