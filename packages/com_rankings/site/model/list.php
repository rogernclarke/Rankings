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
 * Rankings Component Default List Model
 *
 * @since 2.0
 */
class RankingsModelList extends JModelList
{
	/**
	 * Array of items
	 *
	 * @var    string
	 * @since  2.0
	 */
	private $items = null;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     \JModelList
	 * @since   2.0
	 */
	public function __construct($config = array())
	{
		// Specify database connection parameters
		$app 			= JFactory::getApplication();
		$params 		= $app->getParams('com_rankings');
		$config['dbo'] 	= JDatabaseDriver::getInstance($params);

		parent::__construct($config);

		// Set the context
		if (isset($config['subcontext']))
		{
			$this->context = strtolower($this->option . '.' . $config['subcontext'] . '.' . $this->getName());
		}

		// Add the check fields.
		if (isset($config['check_fields']))
		{
			$this->check_fields = $config['check_fields'];
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
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return  JDatabaseQuery   A JDatabaseQuery object to retrieve the dataset.
	 *
	 * @since   2.0
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db 	= $this->getDbo();
		$query 	= $db->getQuery(true);

		$query = $this->getQuerySelect($db, $query);
		$query = $this->getQueryFrom($db, $query);
		$query = $this->getQueryFilters($db, $query);
		$query = $this->getQueryOrder($db, $query);

		// JFactory::getApplication()->enqueueMessage(JText::_('List query: ' . $query), 'debug');

		return $query;
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

		// Handle checkboxes
		if ($newState === null && strpos($request, 'check_') === 0)
		{
			// Check for reset of filters and checkboxes
			$reset = $jinput->get('check_reset', 0, boolean);

			$newState = $jform[$request];

			if ($reset === true)
			{
				$newState = 0;
			}

			if ($curState != $newState && $newState !== null && $resetPage)
			{
				// Reset page
				$jinput->set('limitstart', 0);
			}
		}

		// Handle filters
		if ($newState === null && strpos($request, 'filter_') === 0)
		{
			$newState = $jform[$request];

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
	 * @param 	string 	$ordering 	Not sure of usage!
	 * @param 	string 	$direction 	Not sure of usage!
	 *
	 * @since 2.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$inputFilter = \JFilterInput::getInstance();

		// Get the input
		$app 	= JFactory::getApplication();
		$jinput = $app->input;
		$jform 	= $jinput->get('jform', array(), 'array');

		// Set filters
		for ($i = 0, $n = count($this->filter_fields); $i < $n; $i++)
		{
			$filterField 	= $this->filter_fields[$i];
			$filterKey 		= $this->context . '.filter.' . $filterField;
			$filterName 	= 'filter_' . $filterField;

			$this->state->set('filter.' . $filterField, $this->getUserStateFromRequest($filterKey, $filterName, '', string, true));
		}

		// Set checkboxes
		for ($i = 0, $n = count($this->check_fields); $i < $n; $i++)
		{
			$checkField = $this->check_fields[$i];

			$checkKey = $this->context . '.check.' . $checkField;
			$checkName = 'check_' . $checkField;

			$this->state->set('check.' . $checkField, $this->getUserStateFromRequest($checkKey, $checkName, '', string, true));
		}

		// Receive & set list options
		$this->state->set('list.limit', $this->getUserStateFromRequest($this->context . '.list.limit', 'limit', $app->getCfg('list_limit'), 'int'));
		$this->state->set('list.start', $this->getUserStateFromRequest($this->context . '.list.start', 'limitstart', 0));
		/*if ($list = $app->getUserStateFromRequest($this->context . '.list', 'list', array(), 'array'))
		{
			foreach ($list as $name => $value)
			{
				switch ($name)
				{
					case 'fullordering':
						break;
					case 'ordering':
						break;
					case 'direction':
						break;
					case 'limit':
						break;
					case 'select':
						break;
				}
			}

			// Set the list option
			$this->state->set('list.' . $name, $value);
		}*/

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
	}

	/**
	 * Method to add the ordinal for a given number.
	 *
	 * @param 	integer $number Number to have ordinal added.
	 *
	 * @return  string 	A string containing the number and ordinal.
	 *
	 * @since   2.0
	 */
	protected function setOrdinal($number)
	{
		$ends = array('th','st','nd','rd','th','th','th','th','th','th');

		if ((($number % 100) >= 11) && (($number % 100) <= 13))
		{
			$number .= 'th';
		}
		else
		{
			$number .= $ends[$number % 10];
		}

		return $number;
	}
}
