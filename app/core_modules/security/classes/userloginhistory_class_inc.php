<?php
/* -------------------- UserLoginHistory CLASS ----------------*/

/**
* Class to handle login history of the user
*/
class userLoginHistory extends dbTable {

    /**
    * Constructor method to define the table
    */
    function init()
    {
        parent::init('tbl_userloginhistory');
    }

    /**
    * Method to add a login history entry to the tbl_userloginhistory 
    * table
    * @param string $userId: The Unique userId of the user who is logging in.
    */
    function addHistoryEntry($userId)
    {
        $this->insert(array('userId' => $userId,
                            'lastLoginDateTime' => date('Y-m-d H:m:s')));
    }

    /**
    * Property returns the last login date for the user
    * denoted by $userId
    * @param string $userId: The Unique userId of the user being looked up
    */
    function doGetLastLogin($userId)
    {
        $sql="SELECT MAX(lastLoginDateTime) AS laston FROM tbl_userloginhistory WHERE userId='".$userId."'";
        $rs = $this->query($sql);
        $line = $rs->fetchRow();
        return $line['laston'];
    }
    
}  #end of class
?>