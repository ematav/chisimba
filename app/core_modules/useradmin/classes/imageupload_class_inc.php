<?php
/** 
* Handles upload of images.
* @author James Scoble
* @copyright 2004
* @license GNU GPL
*/
class imageupload extends object
{
    var $objConfig;
    var $objUser;
	/**
	* @var string The path of the file.
	*/
    var $imagePath;
	/**
	* @var string The URL of the file.
	*/
    var $imageUrl;
   
    function init()
    {
        $this->objConfig=&$this->getObject('altconfig','config');
        $this->objUser=&$this->getObject('user','security');
        $this->imagePath = $this->objConfig->getsiteRootPath().'/user_images/';
        $this->imageUrl = $this->objConfig->getsiteRoot().'user_images/';
    }

    /**
    * Upload the file and resize it.
    * @param string $redim
    * @param string 4extra
    */
    function doUpload($userId, $redim=120, $extra='')
    {
        $name=$_FILES['userFile']['name'];
        $type=$_FILES['userFile']['type'];
        $size=$_FILES['userFile']['size'];
        $tmp_name=$_FILES['userFile']['tmp_name'];
        if (
			($type=='image/jpeg')
			||($type=='image/gif')
			||($type=='image/png')
			||($type=='image/bmp')
		){
            $dirObj=$this->getObject('dircreate','utilities');
            $dirObj->makeFolder('user_images');
            $objResize=$this->getObject('resize');
            if ($objResize->loadimage($tmp_name,$name)){
                $objResize->size_auto($redim);
                $objResize->setOutput('jpg');
                $objResize->save($this->imageFolder.$userId.$extra.'.jpg');
            }
        }
    }

    /**
    * Return url to user's picture.
    * @param string $userId
    * @returns string The url
    */
    function userpicture($userId)
    {
        if (file_exists($this->imagePath.$userId.".jpg")){
            return($this->imageUrl.$userId.".jpg");
        } else {
            return ($this->imageUrl."default.jpg");
        }
    }
    
    /**
    * Return url to user's small picture.
    * @param string $userId
    * @returns string The url
    */
    function smallUserPicture($userId)
    {
        if (file_exists($this->imagePath.$userId."_small.jpg")){
            return($this->imageUrl.$userId."_small.jpg");
        } else {
            return ($this->imageUrl."default_small.jpg");
        }
    }

    /**
    * Reset user's picture.
    * @param string $userId
    */
    function resetImage($userId)
    {
        if (file_exists($this->imagePath.$userId.".jpg")){
            @unlink($this->imagePath.$userId.".jpg");
        }
    }
    
}
?>
