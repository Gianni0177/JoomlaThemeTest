<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.cassiopeia
 *
 * @copyright   (C) 2017 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

        function findObjectByName($array, $name){
    		foreach ( $array as $element ) {
             	if ( $name == $element->name ) {
        		    return $element;
        		}
    		}

    		return false;
		}

        function findObjectValueByName($array, $name){
    		foreach ( $array as $element ) {
             	if ( $name == $element->name ) {
        		    return $element->value;
        		}
    		}

    		return false;
		}

        function findObjectRawValueByName($array, $name){
    		foreach ( $array as $element ) {
             	if ( $name == $element->name ) {
        		    return $element->rawvalue;
        		}
    		}

    		return false;
		}

		function estimateReadingTime($html, $showSeconds = false){
            $word = str_word_count(strip_tags($html));
        	$m = floor($word / 120);
 			 $s = floor($word % 120 / (120 / 60));
        	if($m === floor(0)) $m = 1;
           
        
  			return $m . ' minut' . ($m == 1 ? 'o' : 'i') . ($showSeconds ? ', '. $s . ' second' . ($s == 1 ? 'o' : 'i') : '') ;
        }


		function getDateFmt($type = null) {
        	if($type == null) return new IntlDateFormatter('it_IT', IntlDateFormatter::FULL, IntlDateFormatter::FULL,  'Europe/Rome', IntlDateFormatter::GREGORIAN,"dd MMMM Y");
        	
       	    // short
       	    return new IntlDateFormatter('it_IT', IntlDateFormatter::FULL,   IntlDateFormatter::FULL,  'Europe/Rome', IntlDateFormatter::GREGORIAN,"dd MMM Y");
        }

		function printDateFmt($date, $type = null) {
        	$format =  new IntlDateFormatter('it_IT', IntlDateFormatter::FULL, IntlDateFormatter::FULL,  'Europe/Rome', IntlDateFormatter::GREGORIAN,"dd MMMM Y");
         
        	if($type === "short") {
            	$format =  new IntlDateFormatter('it_IT', IntlDateFormatter::FULL, IntlDateFormatter::FULL,  'Europe/Rome', IntlDateFormatter::GREGORIAN,"dd MMM Y");
            } else if ($type === "complete") {
            	$format = new IntlDateFormatter('it_IT', IntlDateFormatter::FULL, IntlDateFormatter::FULL,  'Europe/Rome', IntlDateFormatter::GREGORIAN,"eeee dd MMMM Y");
         	} else if ($type === "monthyear") {
            	$format =  new IntlDateFormatter('it_IT', IntlDateFormatter::FULL, IntlDateFormatter::FULL,  'Europe/Rome', IntlDateFormatter::GREGORIAN,"MMM/YY");
         	} else if ($type === "month") {
            	$format =  new IntlDateFormatter('it_IT', IntlDateFormatter::FULL, IntlDateFormatter::FULL,  'Europe/Rome', IntlDateFormatter::GREGORIAN,"MMMM");
         	}
        
       	    return datefmt_format( $format, $date->getTimestamp());
        }

		function strClean($string) {
   			$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        	$string = strtolower($string);
   			return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
		}
?>