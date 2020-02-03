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

		// Add the filtering fields whitelist.
		if (isset($config['filter_fields']))
		{
			$this->filter_fields = $config['filter_fields'];
		}
	}

	/**
	 * Method to get the form
	 *
	 * @param 	string 	$name 	Form name
	 *
	 * @return  form object
	 */
	public function getForm($name = null)
	{
		$name = $name ?? $this->getName();

		// Get the form
		JForm::addFormPath(JPATH_COMPONENT . '/model/forms');
		JForm::addFieldPath(JPATH_COMPONENT . '/model/fields');
		JForm::addRulePath(JPATH_COMPONENT . '/model/rules');

		// Create the form
		try
		{
			$form = JForm::getInstance('jform', $name, array('control' => 'jform'));
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');

			return false;
		}

		return $form;
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

		return $this->_item[$this->id];
	}

	/**
	 * Returns the last rankings calculation date
	 *
	 * @return date Date of last ranking calcuation
	 *
	 * @since 2.0
	 */
	public function getLastRunDate()
	{
		// Create a new query object.
		$db 	= $this->getDbo();
		$query 	= $db->getQuery(true);

		$query
			->select('MAX(effective_date)')
			->from($db->qn('#__' . $this->prefix . 'rider_history'));

		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Gets the value of a user state variable and sets it in the session
	 *
	 * This is the same as the method in \JApplication except that this also can optionally
	 * force you back to the first page when a filter has changed
	 *
	 * @param   string   $key        The key of the user state variable.
	 * @param   string   $request    The name of the variable passed in a request.
	 * @param   string   $default    The default value for the variable if not found. Optional.
	 * @param   string   $type       Filter for the variable, for valid values see {@link \JFilterInput::clean()}. Optional.
	 * @param   boolean  $resetPage  If true, the limitstart in request is set to zero
	 *
	 * @return  mixed  The request user state.
	 *
	 * @since   2.0
	 */
	public function getUserStateFromRequest($key, $request, $default = null, $type = 'none', $resetPage = true)
	{
		// Get the input
		$app    = \JFactory::getApplication();
		$jinput = $app->input;
		$jform  = $jinput->get('jform', array(), 'array');
		$list 	= $jinput->getVar('list');

		// Get the old, current and new states
		$oldState = $app->getUserState($key);
		$curState = $oldState !== null ? $oldState : $default;
		$newState = $jinput->get($request, null, $type);

		// Handle filters
		if ($newState === null && strpos($request, 'filter_') === 0)
		{
			$newState = $jform[$request];

			if ($newState === null)
			{
				$newState = $jinput->getVar(substr($request,7));

				if ($newState === null)
				{
					$newState = $default;
				}
			}

			if ($curState != $newState && $newState !== null && $resetPage)
			{
				// Reset page
				$jinput->set('limitstart', 0);
			}
		}

		// Save the new value only if it is set in this request.
		if ($newState !== null)
		{
			if (empty($list))
			{
				$app->setUserState($key, $newState);
			}
			elseif (strpos($key, $list))
			{
				$app->setUserState($key, $newState);
			}
			else
			{
				// List value posted is not for this list key
				$newState = $curState;
			}
		}
		else
		{
			$newState = $curState;
		}

		return $newState;
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
		$params = $app->getParams();
		$this->setState('params', $params);
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
