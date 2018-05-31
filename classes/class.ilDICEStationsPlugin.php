<?php
 
include_once("./Services/COPage/classes/class.ilPageComponentPlugin.php");
 
/**
 * DICE Stations plugin for Page Editor
 *
 * @author Stephan Winiker <webmaster@subclauses.net>
 * @version $Id$
 *
 */
class ilDICEStationsPlugin extends ilPageComponentPlugin {
        /**
         * Get plugin name 
         *
         * @return string
         */
        function getPluginName() {
                return 'DICEStations';
        }
        
        
        /**
         * Get valid parent object type 
         *
         * @return boolean
         */
        function isValidParentType($a_parent_type) {
                if ($a_parent_type == 'cont') {
                        return true;
                }
                return false;
        }
        
        /**
         * Get Javascript files
         */
        function getJavascriptFiles($a_mode)
        {
                return array("templates/dicestations.js");
        }
        
        /**
         * Get css files
         */
        function getCssFiles($a_mode)
        {
                return array("templates/dicestations.css");
        }
 
}

?>
