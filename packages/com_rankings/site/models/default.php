<?php
/**
 * Rankings Component for Joomla 3.x
 * 
 * @version    1.0
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Public functions
 * __construct  Construct the requested object
 * get              ??
 * getForm          ??
 * getItem          Build a query, where clause and return an item
 * getPagination    Generate pagination
 * getState         Get model state variables
 * getTotal         Get total number of rows for pagination
 * listItems        Build a query, where clause, order clause and return a list of items
 * set              Modifies a property of the object, creating it if it does not already exist
 * setId            Set the item ID
 * store            ??
 *
 * Private functions
 * _getDBConnection     Gets a database connection
 * _getLastRunDate      Gets last ranking calulation date
 * _getList             Gets an array of objects from the results of database query
 * _getListCount        Returns a record count for the query
 * _ordinal             Sets ordinal suffix for a number
 *
 **/

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\Registry\Registry;

/**
 * Rankings Component Default Model
 */
class RankingsModelsDefault extends JModelBase
{
    /**
     * Protected fields
     **/
    // Inherited from JModelLegacy??
    protected $__state_set = null;

    // Defined here
    protected $_context = null;     // Component name . view name
    protected $_db = null;          // JDatabaseDriver object
    protected $_id = null;          // ID of item
    protected $_id_name = null;     // Name of ID field
    protected $_check_fields = array(); // Valid checkbox fields
    protected $_filter_fields = array(); // Valid filter fields or ordering
    protected $_pagination = null;  // JPagination object
    protected $_query = array();    // JDatabaseQuery object - internal cache for the last query used
    protected $_table_name = null;  // Table name for model
    protected $_total = null;       // Total of items in list
    protected $_view = null;        // Name of model / view

    /**
     * Constructor
     **/
    public function __construct()
    /* Should the following be an input?: JDatabaseDriver $db = null) */
    {
        parent::__construct($state);

        // Set the database connection
        $this->_db = $this->_loadDb();

        // Set the context
        $option = JRequest::getCmd('option');
        $app = JFactory::getApplication();
        $jinput = $app->input;
        $this->_view = $jinput->getWord('view');
        $this->_context = $option . '.' . $this->_view;

        // Get the item ids
        $array = $jinput->get('cid', array(), 'ARRAY');

        // If an item id has been specified then set the object ID
        if (!empty($array))
        {
        	$this->setId((int)$array[0]);
    	}
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     **/
    protected function populateState($ordering = null, $direction = null)
    {
        $inputFilter = \JFilterInput::getInstance();

        // Get the input
        $app = JFactory::getApplication();
        $jinput = $app->input;
        $jform = $jinput->get('jform', array(), 'array');

        // Set filters
        for($i=0, $n = count($this->_filter_fields); $i<$n; $i++) 
        {
            $filter_field = $this->_filter_fields[$i];

            $filter_key = $this->_context . '.filter.' . $filter_field;
            $filter_name = 'filter_' . $filter_field;

            $this->state->set('filter.' . $filter_field, $this->getUserStateFromRequest($filter_key, $filter_name, '', string, true));
        }

        // Set checkboxes
        for($i=0, $n = count($this->_check_fields); $i<$n; $i++) 
        {
            $check_field = $this->_check_fields[$i];

            $check_key = $this->_context . '.check.' . $check_field;
            $check_name = 'check_' . $check_field;

            $this->state->set('check.' . $check_field, $this->getUserStateFromRequest($check_key, $check_name, '', string, true));
        }

        // Receive & set list options
        $this->state->set('list.limit', $this->getUserStateFromRequest($this->_context . '.list.limit', 'limit', $app->getCfg('list_limit'), 'int'));
        $this->state->set('list.start', $this->getUserStateFromRequest($this->_context . '.list.start', 'limitstart', 0 ));

        if ($list = $app->getUserStateFromRequest($this->_context . '.list', 'list', array(), 'array'))
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
        } 
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
     * @since   0.0.1
     */
    public function getUserStateFromRequest($key, $request, $default = null, $type = 'none', $resetPage = true)
    {
        // Get the input
        $app    = \JFactory::getApplication();
        $jinput = $app->input;
        $jform  = $jinput->get('jform', array(), 'array');
        
        // Get the old, current and new states
        $old_state = $app->getUserState($key);
        $cur_state = $old_state !== null ? $old_state : $default;
        $new_state = $jinput->get($request, null, $type);

        // Handle checkboxes
        if ($new_state === null && strpos($request, 'check_') === 0)
        {
            // Check for reset of filters and checkboxes
            $reset = $jinput->get('check_reset', 0, boolean);

            $new_state = $jform[$request];

            if ($reset === TRUE)
            {
                $new_state = 0;
            }

            if ($cur_state != $new_state && $new_state !== null && $resetPage)
            {
                // Reset page
                $jinput->set('limitstart', 0);
            }
        }

        // Handle filters
        if ($new_state === null && strpos($request, 'filter_') === 0)
        {
            $new_state = $jform[$request];

            if ($cur_state != $new_state && $new_state !== null && $resetPage)
            {
                // Reset page
                $jinput->set('limitstart', 0);
            }
        }

        // Save the new value only if it is set in this request.
        if ($new_state !== null)
        {
            $app->setUserState($key, $new_state);
        }
        else
        {
            $new_state = $cur_state;
        }

        return $new_state;
    }

    /**
     * Method to set the item identifier
     *
     * @access  public
     * @param   int Item ID
     **/
    public function setId($id)
    {
        // Set item id
        $this->_id   = $id;
    }

    /**
     * Method to get the form
     *
     * @access  public
     * @return  form object
     **/
    public function getForm()
    {     
        // Get the form
        JForm::addFormPath(JPATH_COMPONENT . '/models/forms');
        JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
        JForm::addRulePath(JPATH_COMPONENT . '/models/rules');

        // Create the form
        try
        {
            $form = JForm::getInstance('jform', $this->_view, array('control' => 'jform'));
        }

        catch (Exception $e)
        {
            $this->_app->enqueueMessage($e->getMessage(), 'error');
            return false;
        }


        // Get the data for an existing item
        /*if (isset($this->_id))
        {
            $data = (array) $this->getItem();
            // Bind the form data if present
            if (!empty($data))
            {
                $form->bind($data);
            }
        }*/
        return $form;
    }

    /**
     * Method to store the data
     *
     * @access  public
     * @param   data
     * @return  form object
     **/
    public function store($data=null)
    {
        // Get the input
        $jinput = JFactory::getApplication()->input;
        $viewName = $jinput->getWord('view', '');
        $viewName = JFile::makeSafe($viewName);
        //$post = $jinput->post->getArray();
        //$data = $post['jform'];

        $row = JTable::getInstance($viewName,'RankingsTable');

        $date = date("Y-m-d H:i:s");
 
        // Bind the form fields to the table
        if (!$row->bind($data))
        {
            return false;
        }

        $row->modified = $date;
        if (!$row->created)
        {
            $row->created = $date;
        }

        // Make sure the record is valid
        if (!$row->check())
        {
            return false;
        }

        // Store the web link table to the database
        if (!$row->store())
        {
            return false;
        }

        return $row;
    }

    /**
     * Modifies a property of the object, creating it if it does not already exist.
     *
     * @param string $property The name of the property.
     * @param mixed $value The value of the property to set.
     *
     * @return mixed Previous value of the property.
     **/
    public function set($property, $value = null)
    {
        $previous = isset($this->$property) ? $this->$property : null;
        $this->$property = $value;
        return $previous;
    }

    public function get($property, $default = null)
    {
        return isset($this->$property) ? $this->$property : $default;
    }

    /**
     * Build a query, where clause and return an object
     *
     **/
    public function getItem()
    {
        // Build query
        $query = $this->_buildQuery();
        $query = $this->_buildWhere($query);
        
        // Execute query
        $this->_db->setQuery($query);
        $item = $this->_db->loadObject();

        return $item;
    }

    /**
     * Build query and where for protected _getList function and return a list
     *
     * @return array An array of results.
     **/
    public function listItems($offset=null, $limit = null)
    {
        // Build query
        $query = $this->_buildQuery();
        $query = $this->_buildWhere($query);
        $query = $this->_buildOrder($query);

        //Set the offset
        if (!isset($offset))
        {
            $offset = $this->getState('list.start');
        }

        //Set the limit
        if (!isset($limit))
        {
            $limit = $this->getState('list.limit');
        }
//JFactory::getApplication()->enqueueMessage(JText::_($query), 'debug');
        // Execute query
        $list = $this->_getList($query, $offset, $limit);
        
        return $list;
    }

    /**
     * Obtain database connection
     **/
    protected function _loadDb()
    {
        $app = JFactory::getApplication();
        $params = $app->getParams('com_rankings');
        
        $db = JDatabaseDriver::getInstance($params);

        return $db;
    }
    
    /**
     * Gets an array of objects from the results of database query.
     *
     * @param string $query The query.
     * @param integer $limitstart Offset.
     * @param integer $limit The number of records.
     *
     * @return array An array of results.
     **/
    protected function _getList($query, $limitstart = 0, $limit = 0)
    {
        
        $this->_db->setQuery($query, $limitstart, $limit);

        $list = $this->_db->loadObjectList();
        
        return $list;
    }
     
    /**
     * Returns a record count for the query
     *
     * @param string $query The query.
     *
     * @return integer Number of rows for query
     **/
    protected function _getListCount($query)
    {
        $this->_db->setQuery($query);
        $this->_db->query();

        return $this->_db->getNumRows();
    }

    /**
     * Method to get model state variables
     *
     * @param string $property Optional parameter name
     * @param mixed $default Optional default value
     *
     * @return object The property where specified, the state object where omitted
     **/
    public function getState($property = null, $default = null)
    {
        // Only populate the state if not already set
        if (!$this->__state_set)
        {
            // Protected method to auto-populate the model state.
            $this->populateState();

            // Set the model state set flag to true.
            $this->__state_set = true;
        }

        return $property === null ? $this->state : $this->state->get($property, $default);
    }

    /**
     * Get total number of rows for pagination
     **/
    public function getTotal()
    {
        if (empty ($this->total))
        {
            $query = $this->_buildQuery();
            $query = $this->_buildWhere($query);
            $this->total = $this->_getListCount($query);
        }

        return $this->total;
    }
     
    /**
     * Generate pagination
     **/
    public function getPagination()
    {
        // Load the content if it doesn't already exist
        if (empty($this->_pagination))
        {
            $this->_pagination = new JPagination($this->getTotal(), $this->getState('list.start'), $this->getState('list.limit'));
        }
        
        return $this->_pagination;
    }
    
    /**
     * Set ordinal suffix for a number
     **/
    protected function _ordinal($number)
    {
        $ends = array('th','st','nd','rd','th','th','th','th','th','th');
        if ((($number % 100) >= 11) && (($number%100) <= 13))
            return $number. 'th';
        else
            return $number. $ends[$number % 10];
    }

    /**
     * Returns the last rankings calculation date
     *
     * @return date Date of last ranking calcuation
     **/
    protected function _getLastRunDate()
    {
        $query = $this->_db->getQuery(TRUE);

        $rundate = "MAX(rh.effective_date)";
        $query->select($rundate);
        $query->from('#__rider_history as rh');

        $this->_db->setQuery($query);
        $this->_db->query();

        return $this->_db->loadresult();
    }

    /**
     * Updates the number of hits for an item
     *
     * @return boolean Result of update
     **/
    protected function _updateHits()
    {
        $query = $this->_db->getQuery(TRUE);

        // Table to update
        //$table = $this->_db->quoteName('#__riders');

        // Fields to update
        $fields = array($this->_db->quoteName('hits') . ' = ' . $this->_db->quoteName('hits') . ' + 1');

        // Conditions for which records should be updated
        $conditions = array($this->_db->quoteName($this->_id_name) . ' = 
            ' . $this->_id);

        $query->update($this->_table_name)->set($fields)->where($conditions);

        $this->_db->setQuery($query);

        $result = $this->_db->execute();

        return $result;
    }
}