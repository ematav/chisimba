<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}


/**
* Class frameset class to group items
* 
* Used to create frameset that helps
* grouped items
* 
* @package frameset
* @category HTML Controls
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license GNU GPL
* @author Wesley Nitsckie updated by Derek Keats 2004 03 16
* @example :
*/
class fieldsetex extends object{
	/**
	*@var $legend The heading of the frameset
	*/
	var $legend;
	/**
	*@var $legendalign The alignment for the legend
	*/
	var $legendalign;

	/**
	*@var $content The contents of the frameset
	*/
	var $contents;
    /**
	*@var $width The width attribute
	*/
	var $width;
    /**
	*@var $extra Any other extra items that needs to be added 
	*/
	var $extra;
    
        /**
        *@var $align how the table is aligned - added 2005 03 31 by James Scoble
        */
        var $align;

	/**
	*Initialize
	*/
	function init()
	{
		$this->contents="";
	}
	
	/**
	*The show Method
    *@return null
    *@access public
	*/
	function show()
	{
            $str="";
            //Add the width if it exists !added by derek
            $align='';
            if (isset($this->align)){
                $align=" align='".$this->align."' ";
            }
            if (isset($this->width)) {
                $str.= "<table $align width=\"" . $this->width . "\"><tr><td>";
            }   
	    $str .= '<fieldset';
        
        if (isset($this->extra)) {
            $str.=$this->extra;
        }
        $str .= '>';
        
        if (isset($this->legend)) {
            $str .= '<legend';
            if (isset($this->legendalign)) {
                $str .= '  ALIGN="' . $this->legendalign . '"';
            }
            $str .= '>'.$this->legend.'</legend>';
        }
		$str.="<table>";
		$str.=$this->contents;
		$str.="</table>";
		$str.='</fieldset>';
        //End the width if it exists !added by derek
        if (isset($this->width)) {
            $str.= "</td></tr></table>";
        }
		return $str;
	}
	
	
	function addLabel($label){
        if (is_object($label)) {
            $str = $label->show();
        } else {
            $str = $label;
        } 
		$this->contents.='<tr><td align="left" colspan="2">'.$str.'</td></tr>';
	}
	
	function addLabelledField($label,$field){
        if (is_object($label)) {
            $str1 = $label->show();
        } else {
            $str1 = $label;
        } 
        if (is_object($field)) {
            $str2 = $field->show();
        } else {
            $str2 = $field;
        } 
		$this->contents.='<tr><td align="right">'.$str1.'</td><td align="left">'.$str2.'</td></tr>';
	}

	/**
	*Method to reset the fields
    *@return null
    *@access public
	*/
	function reset(){
		$this->contents=null;
		$this->legend=null;
	}
	
	/**
	*Method to add the legend
	*@param $legend string  The legend to be added to the fieldset
    *@return null
    *@access public
	*/
	function setLegend($legend){
		$this->legend=$legend;
	}
    
    /**
	*Method to add extra parameters
	*@param $parameters string  String of parameters that can be added
    *@return null
    *@access public
	*/
	function setExtra($parameters){
		$this->extra=$parameters;
	}
}
?>
