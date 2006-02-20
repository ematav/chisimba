<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}


/**
* @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package groupadmin
* @subpackage view
* @version 0.1
* @since 22 November 2004
* @author Jonathan Abrahams
* @filesource
*/
/**
* Uses classes for the groupadmin tree class.
* @package groupadmin
* @author Jonathan Abrahams
*/
class groupadmin_tree extends object {
    /**
    * @var groupAdminModel an object reference.
    */
    var $_objGroupAdminModel;
    /**
    * @var treeMenu an object reference.
    */
    var $_objTreeMenu;
    /**
    * @var array extra tree options for display.
    */
    var $_extra;
    /**
    * @var string Group Id of root node.
    */
    var $_rootNode;
    
    /**
    * @var true|false Enable/Disabel context sensistive tree.
    */
    var $_contextSensitive;
    /**
    * Method to initialize the object.
    */
    function init()
    {
        $this->loadClass('treemenu','tree');
        $this->loadClass('treenode','tree');
        $this->loadClass('dhtml','tree');
        $this->loadClass('listbox','tree');
        $this->loadClass('tree_dropdown','groupadmin');
        
        // Enable/Disable Context sensitive tree
        $this->_contextSensitive = false;
        
        $this->_objGroupAdminModel =& $this->getObject( 'groupAdminModel', 'groupadmin' );
        $this->createTreeMenu();

        $objSkin =& $this->getObject( 'skin', 'skin' );
        $this->_extra = array(
            'images' => $objSkin->getSkinURL().'treeimages/groupadmin',
            'defaultClass' => 'treeMenuDefault' );
    }

    /**
    * Method to get the tree menu object
    * @access public
    * @return object reference
    */
    function &getTreeMenu()
    {
        return $this->_objTreeMenu;
    }
    
    /**
    * Method to get the array of extra options
    * @access public
    * @return array reference
    */
    function &getExtra()
    {
        return $this->_extra;
    }
    
    /**
    * Method to create a root node.
    * @access private
    * @return object reference
    */
    function &createRootNode()
    {
        // Context Aware;
        $objDBContext = &$this->getObject('dbcontext','context');
        if( $this->_contextSensitive && $objDBContext->isInContext()){
            $contextCode = $objDBContext->getContextCode();
            $groupId = $this->_objGroupAdminModel->getLeafId(array($contextCode));
            $link    = $this->uri( array( 'action'=> 'main', 'groupId' => $groupId ) );
            $this->_rootNode = $groupId;
            return new treenode( array (
                'text' => '<STRONG> '.$contextCode.'</STRONG>',
                'link'         => $link,
                'value'        => $groupId,
                'icon' => 'base.gif' ));
        } else {
            $this->_rootNode = NULL;
            return new treenode( array ( 'text' => '<STRONG>Groups</STRONG>', 'icon' => 'base.gif' ));
        }
    }

    /**
    * Method to create a tree menu object.
    * @access private
    * @return nothing
    */
    function createTreeMenu()
    {
        $this->_objTreeMenu =& new treemenu();
        
        $rootMenu = &$this->createRootNode();
        $this->_objTreeMenu->addItem( $rootMenu );
        
        $this->recureTree( $this->_rootNode, $rootMenu );
    }
    
    /**
    * Method to create a tree node.
    * @param array contains the current group row.
    * @access private
    * @return object reference
    */
    function &createTreeNode( &$row )
    {
        // Initialize locals
        $groupId   = $row['id'];
        $groupName = $row['name'];
        $model =& $this->_objGroupAdminModel;

        // Create clickable links on the tree
        $link      = $this->uri( array( 'action'=> 'main', 'groupId' => $groupId ) );
        
        // Get the list of group members
        $users = $model->getSubGroupUsers( $groupId );
        
        // Check if an empty group
        $isEmptyFolder = (count( $users )) == 0;
        
        $icon   = $isEmptyFolder ? 'folder.gif' : 'group-folder.gif';
        $expand = $isEmptyFolder ? 'folder-expanded.gif' : 'group-folder-expanded.gif';
        
        // Check if group has no subgroups
        $noSubgroups = ( count ( $model->getSubGroups( $groupId ) ) ) == 1;

        $icon = $noSubgroups
                    ? $isEmptyFolder ? 'folder.gif' : 'foldergroups.gif'
                    : $icon;
        
        return new treenode(  array (
                    'text'         => $groupName,
                    'link'         => $link,
                    'value'        => $groupId,
                    'icon'         => $icon,
                    'expandedIcon' => $expand,
                    'cssClass'     => '',
                    'linkTarget'   => null
                ));
    }

    /**
    * Method to show the tree as a list box.
    * @access public
    * @return string the HTML output
    */
    function showListbox()
    {
        $listboxMenu = new listbox( $this->_objTreeMenu, $this->_extra );
        return $listboxMenu->getMenu();
    }

    /**
    * Method to show the tree as a drop down.
    * @access public
    * @return string the HTML output
    */
    function showDropdown()
    {
        $dropdownMenu = new tree_dropdown( $this->_objTreeMenu, $this->_extra );
        return $dropdownMenu->getMenu();
    }

    /**
    * Method to show the tree as a tree like structure.
    * @access public
    * @return string the HTML output
    */
    function showDHTML()
    {
        $dhtmlMenu = new dhtml( $this->_objTreeMenu, $this->_extra );
        return $dhtmlMenu->getMenu();
    }
    /**
    * Method to build the tree structure.
    * @access private
    * @return nothing
    */
    function recureTree( &$node, &$parentNode )
    {
        $objGA = &$this->_objGroupAdminModel;
        $menu  = &$this->_objTreeMenu;

        $isRoot = is_null( $node );
        $result = ( $isRoot  ) ? $objGA->getRoot( ) : $objGA->getChildren( $node );

        if ( $result ) {
            foreach ( $result as $row ) {
                if( $this->isVisible( $row ) ) {
                    $newNode = &$this->createTreeNode( $row );
                    if  ( is_null( $parentNode ) ) {
                        $menu->addItem( $newNode );
                    } else {
                        $parentNode->addItem( $newNode );
                    }
                    $this->recureTree( $row['id'], $newNode );
                }
            }
        }
    }
    
    /**
    * Method to test if the group row is visible.
    * @access private
    * @return TRUE|FALSE
    */
    function isVisible( $row )
    {
        return TRUE;
    }
}
