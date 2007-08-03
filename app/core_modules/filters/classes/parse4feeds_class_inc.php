<?php

/**
 * Class to parse a string (e.g. page content) that contains a request
 * to load a RSS feed in the form [FEED]username[/FEED]
 * 
 * PHP version 5
 * 
 * This program is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License 
 * along with this program; if not, write to the 
 * Free Software Foundation, Inc., 
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 
 * @category  Chisimba
 * @package   filters
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2007 Derek Keats
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       
 */
/**
*
* Class to parse a string (e.g. page content) that contains a request
* to load a RSS feed in the form [FEED]username[/FEED]
*
* @author Derek Keats
*         
*/

class parse4feeds extends object
{
    
    /**
     * 
     * Standard Chisimba init method. 
     * to use
     * 
     * @access public
     *                
     */
    public function init()
    {

    }
    
    /**
    *
    * Method to parse the string
    * @param  String $str The string to parse
    * @return The    parsed string
    *                
    */
    public function parse($str)
    {
        $str = stripslashes($str);
        //Get all the tags that are in links into an array
        preg_match_all('/\\[FEED]<a.*?href="(?P<feedlink>.*?)".*?>.*?<\/a>\\[\/FEED]/', $str, $results, PREG_PATTERN_ORDER);
        //Get the ones that are just in URLs
        preg_match_all('/\\[FEED](.*?)\\[\/FEED]/', $str, $results2, PREG_PATTERN_ORDER);
        $counter = 0;
        foreach ($results[0] as $item) {
        	$link = $results['feedlink'][$counter];
        	$replacement = "<div class=\"feedhopper\" id=\"feedhopper" . $counter . "\">" . $this->fetchFeed($link) . "</div>";
            $str = str_replace($item, $replacement, $str);
            $counter++;
        }
        //Get the ones that are straight URL links
        $counter = 0;
        foreach ($results2[0] as $item)
        {
            $link = $results2[1][$counter];
        	$replacement = "<div class=\"feedhopper\" id=\"feedhopper" . $counter . "\">" . $this->fetchFeed($link) . "</div>";
            $str = str_replace($item, $replacement, $str);
            $counter++;
        }
        return $str;
    }
    
    /**
     * 
     * Method to use the feed module to get the feed
     * @param  string $url The URL for the feed to process
     * @return string The full feed with title, link, and description
     *                
     */
    public function fetchFeed($url)
    {
    	$url =  $this->cleanUrl($url);
        $objRss = $this->newObject('rssreader', 'feed');
        $objRss->parseRss($url);
        $ar = $objRss->getRssItems();
        $total = count($ar);
        //Do some layout of flickr images
        $url = strtolower($url); //Make sure its lower case
        $pos = strpos($url, "flickr.com");
        if (!$pos === FALSE) {
            $isFlickr = TRUE;
            $ret = "<table>\n";
            if ($this->isOdd($total)) {
                $closingCell = "<td>&nbsp;</td></tr>";
            } else {
                $closingCell = "";
            }
        } else {
            $isFlickr = FALSE;
            $ret = "<ul>\n";
        }
        //Loop and build the output string
        $counter=0;
		foreach ($ar as $item) {
			$counter++;
			//var_dump($item);
        	if(!isset($item['link'])) {
        		$item['link'] = NULL;
        	}
        	if ($isFlickr == TRUE) {
        		if ($this->isOdd($counter)==TRUE) {
        		    @$ret .= "<tr><td><a href=\"" . htmlentities($item['link']) 
		    		  . "\">" . htmlentities($item['title']) . "</a><br />\n"
		    		  . $item['description'] . "</td>";  
        		} else  {
        		    @$ret .= "<td><a href=\"" . htmlentities($item['link']) 
		    		  . "\">" . htmlentities($item['title']) . "</a><br />\n"
		    		  . $item['description'] . "</td></tr>";  
        		}
        	} else {
	    		@$ret .= "<li><a href=\"" . htmlentities($item['link']) 
	    		  . "\">" . htmlentities($item['title']) . "</a></li>\n"
	    		  . $item['description'] . "<br /><br />";  
	        }

		}
		//End the table or UL depending on if we are parsing a Flickr image feed or not
		if ($isFlickr) {
		    $ret .= $closingCell . "</table>\n";
		} else {
		    $ret .=  "</ul>\n";
		}
		return $ret;
    }
    
    /**
     * 
     * The stupid WYSWYG editor in Chisimba replaces & with &amp; in URLs
     * so this needs to be reversed for the feed to work
     * 
     * @param  string $url The Url to be cleaned
     * @return string The Url with &amp; replaced by &
     *                
     *                
     */
    public function cleanUrl($url) 
    {
       return str_replace("&amp;", "&", $url);
    }
    
    /**
    * 
    * Method to determine if a number is odd
    * 
    * @access public 
    * @param  int     $num The number to test
    * @return boolean TRUE|FALSE depending on odd or even status of $num
    *                 
    */
    function isOdd( $num )
	{
		if( $num%2 == 1 ) {
    		// $odd == 1; the remainder of 25/2
    		return TRUE;
		} else {
		    // $odd == 0; nothing remains 
    		return FALSE;
		}
	}
	
}
?>