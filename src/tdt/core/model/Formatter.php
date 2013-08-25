<?php

/**
 * This file contains the Formatter
 *
 * @copyright (C) 2011, 2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@iRail.be>
 * @author Pieter Colpaert   <pieter@iRail.be>
 */

namespace tdt\core\model;

use tdt\exceptions\TDTException;

class Formatter {

    private $format;
    private $extraFormatters;
    
    public function __construct($format = "") {
        $this->setFormat($format);
    }

    /**
     * sets the requested format in the factory from the request URL
     * @param string $urlformat The format of the request i.e. json,xml...
     */
    public function setFormat($urlformat = "") {
        //We define the format like this:
        // * Check if $urlformat has been set
        //   - if not: probably something fishy happened, set format as error for logging purpose
        //   - else if is about: do content negotiation
        //   - else check if format exists
        //        × throw exception when it doesn't
        //        × if it does, set $this->format with ucfirst
        //first, let's be sure about the case of the format
        $urlformat = strtoupper($urlformat);

        if (strtoupper($urlformat) == "ABOUT" || $urlformat == "") { //urlformat can be empty on SPECTQL query
            $cn = new \tdt\negotiators\ContentNegotiator();
            $format = strtoupper($cn->pop());
            while (!$this->formatExists($format) && $cn->hasNext()) {
                $format = strtoupper($cn->pop());
                if ($format == "*") {
                    $format == "XML";
                }
            }
            if (!$this->formatExists($format)) {
                throw new TDTException(451, array($format)); // could not find a suitible format
            }
            $this->format = $format;
            //We've found our format through about, so let's set the header for content-location to the right one
            //to do this we're building our current URL and changing .about in .format
            $format = strtoupper($this->format);
            $pageURL = 'http';
            if (isset($_SERVER["HTTPS"])) {
                $pageURL .= "s";
            }
            $pageURL .= "://";
            if ($_SERVER["SERVER_PORT"] != "80") {
                $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
            } else {
                $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
            }
            $contentlocation = str_ireplace(".about", "." . $format, $pageURL);
            header("Content-Location:" . $contentlocation);
        } else if ($this->formatExists($urlformat)) {
            $this->format = $urlformat;
        } else {
            throw new TDTException(451, array($urlformat));
        }
    }

    private function formatExists($format) {
        return class_exists("\\tdt\\formatters\\strategies\\$format");
    }

    /*
     * This function has to create a strategy and print everything using this strategy.
     */

    public function execute($rootname, $thing) {
        $format = "\\tdt\\formatters\\strategies\\" . $this->format;
        $strategy = new $format($rootname, $thing);
        
        //Didn't really make sense to split the formats up, so for the moment this is commented
        /**
         * Check if which formatter we're dealing with (normal object formatter or ARC grap formatter)
         * According to the result of this control check, convert (if necessary the object to the appropriate object structure e.g. from graph to php object or vice versa)
         */
        /*if (!$this->isObjectAGraph($thing)) {
            if (array_key_exists('tdt\\formatters\\interfaces\\iSemanticFormatter', class_implements($strategy))) {
                $thing = $this->convertPHPObjectToARC($thing);
            }
        } else {
            if (!array_key_exists('tdt\\formatters\\interfaces\\iSemanticFormatter', class_implements($strategy))) {
                $thing = $this->convertARCToPHPObject($thing);
            }
        }*/

        // remake the formatting strategy
        //$strategy = new $format($rootname, $thing);
        $strategy->execute();
    }

    //This logic has moved to AStrategy and will probably stay there. For now, keep it commented.
    
//    protected function isObjectAGraph($object) {
//        foreach ($object as $class => $prop)
//            return ($prop instanceof \ARC2_RDFParser);
//
//        return false;
//    }
//
//    protected function convertPHPObjectToARC($object) {
//        //REWRITE
////        //Unwrap the object
////        foreach ($this->objectToPrint as $class => $prop){
////            if (is_a($prop,"MemModel")){
////                $this->objectToPrint = $prop;
////                break;
////            }
////        }
////        //When the objectToPrint has a MemModel, it is already an RDF model and is ready for serialisation.
////        //Else it's retrieved data of which we need to build an rdf output
////        if (!is_a($this->objectToPrint,"MemModel")) {
////            $outputter = new RDFOutput();
////            $this->objectToPrint = $outputter->buildRdfOutput($this->objectToPrint);
////        }
////
////        // Import Package Syntax
////        include_once(RDFAPI_INCLUDE_DIR . PACKAGE_SYNTAX_N3);
////
////        $ser = new N3Serializer();
////
////        $rdf = $ser->serialize($this->objectToPrint);
//
//        throw new \Exception("This resource does not contain semantic information");
//
//
//        return $object;
//    }
//
//    protected function convertARCToPHPObject($graph) {
//        foreach ($graph as $class => &$prop) {
//            //$graph->$class = $object;
//            $index = $prop->getSimpleIndex();
//
//            //$result = $this->stripObject($index);
//            $result = $index;
//            $graph->$class = $result;
//            return $graph;
//        }
//        return $graph;
//    }
//
//    private function stripObject($obj, $result = array()) {
//
//        foreach ($obj as $key => $value) {
//            
//            $new_key = $this->stripURI($key);
//            $result[$new_key] = array();
//            
//            if (is_array($value))
//                $result[$new_key] = $this->stripObject($value, $result[$new_key]);
//            else
//                $result[$new_key] = $this->stripURI($value);
//        }
//
//        return $result;
//    }
//
//    private function stripURI($uri) {
//        $pos = strrpos($uri, "#");
//
//        if (!$pos)
//            $pos = strrpos($uri, "/");
//
//
//        if (!$pos)
//            $pos = strrpos($uri, ":");
//        
//        if (!$pos)
//            return $uri;
//        
//        return substr($uri, $pos+1);
//    }

    /**
     * Returns the format that has been set by the request
     * @return A format object
     */
    public function getFormat() {
        return $this->format;
    }

    public function getFormatterDocumentation() {
        $doc = array();
        //open the custom directory and loop through it
        if ($handle = opendir(__DIR__ . '/strategies')) {
            while (false !== ($formatter = readdir($handle))) {

                $filenameparts = explode(".", $formatter);
                $formattername = $filenameparts[0];
                //if the object read is a directory and the configuration methods file exists, then add it to the installed formatters
                $classname = "tdt\\formatters\\strategies\\" . $formattername;

                if ($formatter != "." && $formatter != ".." && class_exists($classname)) {

                    if (is_subclass_of($classname, "tdt\\formatters\\AStrategy")) {

                        /*
                         * Get the name without Formatter as $formattername
                         */
                        /* $matches = array();
                          preg_match('/(.*)Formatter.*', $classname, $matches);
                          if (isset($matches[1])) {
                          $formattername = $matches[1];
                          } */

                        /*
                         * Remove the namespace if present from the formattername
                         */
                        $pieces = explode("\\", $formattername);
                        $formattername = end($pieces);

                        $doc[$formattername] = $classname::getDocumentation();
                    }
                }
            }
            closedir($handle);
        }
        return $doc;
    }

}
