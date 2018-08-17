<?php
 
include_once("./Services/COPage/classes/class.ilPageComponentPluginGUI.php");
 
/**
 * DICE Stations plugin for Page Editor
 *
 * 
 * @author Stephan Winiker <webmaster@subclauses.net>
 * @version $Id$
 * @ilCtrl_isCalledBy ilDICEStationsPluginGUI: ilPCPluggedGUI
 */
class ilDICEStationsPluginGUI extends ilPageComponentPluginGUI {
		private $ctrl;
		private $tpl;
		private $tree;
		private $obj_def;
		private $ref_id;
		
		function __construct() {
			global $DIC;
			$this->ctrl = $DIC->ctrl();
			$this->tpl = $DIC->ui()->mainTemplate();
			$this->tree = $DIC->repositoryTree();
			$this->obj_def = $DIC['objDefinition'];
			$this->ref_id = (int)$_GET['ref_id'];
					
			parent::__construct();
		}
		
        /**
         * Execute command
         *
         * @param
         * @return
         */
        function executeCommand() {
                // perform valid commands
                $cmd = $this->ctrl->getCmd();
                if (in_array($cmd, array("create", "save", "edit", "update", "cancel"))) {
                	$this->$cmd();
                }
        }
        
        
        /**
         * Form for new elements
         */
        function insert() {
                global $tpl;
                
                $form = $this->initForm(true);
                $tpl->setContent($form->getHTML());
        }
        
        /**
         * Save new DICE Station Element
         */
        public function create() {
                $form = $this->initForm(true);
                if ($form->checkInput()) {
                        $properties = array(
                                "block" => $form->getInput("block")
                                );
                        if ($this->createElement($properties)) {
                                ilUtil::sendSuccess($this->lng->txt("msg_obj_modified"), true);
                                $this->returnToParent();
                        }
                }
 
                $form->setValuesByPost();
                $this->tpl->setContent($form->getHtml());
        }
        
        /**
         * Edit
         *
         * @param
         * @return
         */
        function edit() {
			$form = $this->initForm();
            $this->tpl->setContent($form->getHTML());                
        }
        
        /**
         * Update
         *
         * @param
         * @return
         */
        function update() {
                $form = $this->initForm(true);
                if ($form->checkInput()) {
                	$properties = array(
                			"block" => (int) $form->getInput("block")
                		);
                        if ($this->updateElement($properties)) {
                                ilUtil::sendSuccess($this->lng->txt("msg_obj_modified"), true);
                                $this->returnToParent();
                        }
                }
 
                $form->setValuesByPost();
                $this->tpl->setContent($form->getHtml());
 
        }
        
        
        /**
         * Init editing form
         *
         * @param        int        $a_mode        Edit Mode
         */
        public function initForm($a_create = false) {
                include_once("Services/Form/classes/class.ilPropertyFormGUI.php");
                $form = new ilPropertyFormGUI();
 
                // Select the block containing the elements to show as stations 
                $block = new ilSelectInputGUI($this->getPlugin()->txt('select_block'), 'block');
                
                $children = $this->tree->getChilds($this->ref_id);
                $item_groups = array();
                foreach ($children as $child) {
                	if ($child["type"] == "itgr") {
                		$item_groups[$child["ref_id"]] = $child["title"];
                	}
                }
                
                if (count($item_groups) == 0) {
	                $item_groups['0'] = '--'.$this->getPlugin()->txt('no_item_groups').'--';
	                $block->setInfo($this->getPlugin()->txt('select_block_no_item_groups_info'));
	                $no_item_groups = true;
                } else {
                	$block->setInfo($this->getPlugin()->txt('select_block_info'));
                }
                
                $block->setOptions($item_groups);
                $block->setRequired(true);
                $form->addItem($block);
                
                if (!$a_create) {
                        $prop = $this->getProperties();
                        if ($prop['block'] == 0) {
                        	$item_groups['0'] = '--'.$this->plugin->txt('invalid_item_group').'--';
                        	$block->setOptions($item_groups);
                        }
                        
                        $block->setValue($prop['block']);
                }
 
                // save and cancel commands
                if ($no_item_groups) {
                	$form->addCommandButton("cancel", $this->lng->txt("cancel"));
                	$form->setTitle($this->getPlugin()->txt("no_item_groups_title"));
                } else if ($a_create) {
                    $this->addCreationButton($form);
                    $form->addCommandButton("cancel", $this->lng->txt("cancel"));
                    $form->setTitle($this->getPlugin()->txt("cmd_insert"));
                } else {
                    $form->addCommandButton("update", $this->lng->txt("save"));
                    $form->addCommandButton("cancel", $this->lng->txt("cancel"));
                    $form->setTitle($this->getPlugin()->txt("edit_dice_stations_el"));
                }
                
                $form->setFormAction($this->ctrl->getFormAction($this));
                
                return $form;
        }
 
        /**
         * Cancel
         */
        function cancel() {
                $this->returnToParent();
        }
        
        /**
         * Get HTML for element
         *
         * @param string $a_mode (edit, presentation, preview, offline)s
         * @return string $html
         */
        function getElementHTML($a_mode, array $a_properties, $a_plugin_version) {
        	$block_ref = (int) $a_properties['block'];
        	if ($this->tree->isGrandChild($this->ref_id, $block_ref)) {
	        	include_once "./Modules/ItemGroup/classes/class.ilItemGroupItems.php";
	        	$itgr_items = new ilItemGroupItems($block_ref);
		       	$items = $itgr_items->getValidItems();
	
	        	$tpl = $this->plugin->getTemplate("default/tpl.stations.html");
	        	$tpl->setCurrentBlock("xcode_station_display");
	
	                foreach ($items as $item) {
	                    $timings = ilObjectActivation::getItem($item);
	                    if (ilObject2::_lookupType($item, true) == 'grp' && 
	                        ($timings['timing_type'] != ilObjectActivation::TIMINGS_ACTIVATION || 
	                            $timings["timing_start"] < time() && $timings["timing_end"] > time())) {
	                		$obj = new ilObjGroup($item);
	               			$tpl->setVariable('GRP_IMG', ilObject::_getIcon($obj->getId(),'big', 'grp'));
	               			$tpl->setVariable('GRP_TITLE', $obj->getTitle());
	               			
	               			$this->ctrl->setParameterByClass("ilobjgroupgui", "ref_id", $item);
	               			$link = $this->ctrl->getLinkTargetByClass(array("ilrepositorygui", "ilobjgroupgui"), "");
	               			$this->ctrl->setParameterByClass("ilobjgroupgui", "ref_id", $this->ref_id);
	               			$tpl->setVariable('GRP_LINK', $link);
	               			
	               			$tpl->setVariable('GRP_DESC', $obj->getDescription());

	                        $tpl->parseCurrentBlock();
	                	}
	                }

	                return $tpl->get();
        	} else {
        		$form = $this->initForm(true);
        		$properties = array(
        				"block" => 0
        		);
        		$this->updateElement($properties);
        	}
        }  
}
?>
