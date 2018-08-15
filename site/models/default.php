<?php
/**
 * Rankings Component for Joomla 3.x
 * 
 * @version    0.0.1
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
 * getState         Get model state variables??
 * getTotal         Get total number of rows for pagination
 * listItems        Build a query, where clause, order clause and return a list of items
 * set              Modifies a property of the object, creating it if it does not already exist
 * setId            Set the item ID
 * store            ??
 *
 * Private functions
 * _getDBConnection     Get a database connection
 * _getList             Gets an array of objects from the results of database query
 * _getListCount        Returns a record count for the query
 * _ordinal             Set ordinal suffix for a number
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
    protected $_app = null;         // JApplicationCms object
    protected $_db = null;          // JDatabaseDriver object
    protected $_context = null;     // Component name  view name
    protected $_id = null;          // ID of item
    protected $_filter_fields = array(); // Valid filter fields or ordering
    protected $_limitstart = 0;     // Start limit for listing items
    protected $_limit = 20;         // Number of list items to get
    protected $_option = null;      // Component name
    protected $_pagination = null;  // JPagination object
    protected $_query = array();    // JDatabaseQuery object - internal cache for the last query used
    protected $_total = null;       // Total of items in list
    protected $_view = null;        // Name of model

    /**
     * Constructor
     **/
    public function __construct(Registry $state = null)
    /* From JModelDatabase:
     * public function __construct(Registry $state = null, JDatabaseDriver $db = null) */
    {
        parent::__construct($state);

        // Set the database connection
        $this->_db = isset($_db) ? $_db : $this->_loadDb();

        // Set the component name
        $this->_option = JRequest::getCmd('option');

        // Get the input
        $this->_app = JFactory::getApplication();
        $jinput = $this->_app->input;
        
        // Set the view name
        $this->_view = $jinput->getWord('view');

        // Set the model state
        if (isset($state))
        {
            $this->state = $state;
        }
        else
        {
            $this->state = new JRegistry;
        }

        // Get the item ids
        $array = $jinput->get('cid', array(), 'ARRAY');

        // If an item id has been specified then set the object ID
        if (!empty($array))
        {
        	$this->setId((int)$array[0]);
    	}

        // Set pagination limits
        $this->_limit = $this->_app->getUserStateFromRequest($this->_context . '.limit', 'limit', $this->_app->getCfg('list_limit'), 'int');
        $this->_limitstart = $this->_app->getUserStateFromRequest($this->_context . '.limitstart', 'limitstart', 0 );
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     **/
    protected function populateState($ordering = null, $direction = null)
    {
        // Load the filter state.
        //$search = $this->_app->getUserStateFromRequest($this->_option . '.' . $this->_view . '.filter.search', 'filter_search');
        $search = $this->_app->getUserStateFromRequest('filter.search', 'filter_search');

        $registry = new JRegistry;
        $registry->set('filter.search', $search);
        $this->setState($registry);

        // Load the parameters.
        $params = JComponentHelper::getParams('com_rankings');
        //$this->setState(new JRegistry('params', $params));
        //$this->setState('params', $params);

        // List state information.
        //parent::populateState('rr.name', 'asc');
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
        // Get the application
        $app = JFactory::getApplication();

        // Get the view
        $viewName = $app->input->getWord('view', 'cpanel');
        $viewName = JFile::makeSafe($viewName);
        
        // Get the form
        JForm::addFormPath(JPATH_COMPONENT . '/models/forms');
        JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
        JForm::addRulePath(JPATH_COMPONENT . '/models/rules');

        // Create the form
        try
        {
            $form = JForm::getInstance('jform', $viewName, array('control' => 'jform'));
        }

        catch (Exception $e)
        {
            $app->enqueueMessage($e->getMessage(), 'error');
            return false;
        }

        // Get the data for an existing item
        if (isset($this->_id))
        {
            $data = (array) $this->getItem();
            // Bind the form data if present
            if (!empty($data))
            {
                $form->bind($data);
            }
        }
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
        $post = $jinput->post->getArray();
        $data = $post['jform'];

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
    public function listItems()
    {
        // Build query
        $query = $this->_buildQuery();
        $query = $this->_buildWhere($query);
        $query = $this->_buildOrder($query);

        // Execute query
        $list = $this->_getList($query, $this->_limitstart, $this->_limit);
        
        return $list;
    }

    /**
     * Obtain database connection
     **/
    protected function _loadDb()
    {
        $options = array();
        $options['driver']   = 'mysqli';
        $options['host']     = 'localhost';
        $options['user']     = 'spindata_ttspdt';
        $options['password'] = 'p=WXMpzAWK[k';
        $options['database'] = 'spindata_tttest';
        $options['prefix']   = 'tt_';

        return JDatabaseDriver::getInstance($options);
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

        return $this->_db->loadObjectList();
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
            $this->_pagination = new JPagination($this->getTotal(), $this->_limitstart, $this->_limit );
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
}