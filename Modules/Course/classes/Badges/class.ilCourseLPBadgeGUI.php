<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

require_once "./Services/Badge/interfaces/interface.ilBadgeTypeGUI.php";

/**
 * Course LP badge gui 
 *
 * @author Jörg Lützenkirchen <luetzenkirchen@leifos.com>
 * @version $Id:$
 *
 * @ingroup ModulesCourse
 */
class ilCourseLPBadgeGUI implements ilBadgeTypeGUI
{	
	protected $parent_ref_id; // [int]
	
	public function initConfigForm(ilPropertyFormGUI $a_form, $a_parent_ref_id)
	{
		global $lng, $tree;
		
		$this->parent_ref_id = (int)$a_parent_ref_id;
	
		include_once "Services/Form/classes/class.ilRepositorySelector2InputGUI.php";
		$subitems = new ilRepositorySelector2InputGUI($lng->txt("objects"), "subitems", true);
		
		$exp = $subitems->getExplorerGUI();		
		$exp->setSkipRootNode(true);
		$exp->setRootId($this->parent_ref_id);		
		$exp->setTypeWhiteList($this->getLPTypes($this->parent_ref_id));	
		
		$subitems->setRequired(true);
		$a_form->addItem($subitems);				
	}
	
	protected function getLPTypes($a_parent_ref_id)
	{
		global $tree;
			
		$res = array();
							
		$root = $tree->getNodeData($a_parent_ref_id);
		$sub_items = $tree->getSubTree($root);
		array_shift($sub_items); // remove root
		
		include_once "Services/Object/classes/class.ilObjectLP.php";
		foreach($sub_items as $node)
		{
			if(ilObjectLP::isSupportedObjectType($node["type"]))
			{
				$res[] = $node["type"];
			}
		}
		
		return $res;
	}
	
	public function importConfigToForm(ilPropertyFormGUI $a_form, array $a_config)
	{
		if(is_array($a_config["subitems"]))
		{	
			$items = $a_form->getItemByPostVar("subitems");		
			$items->setValue($a_config["subitems"]);			
		}
	}
	
	public function getConfigFromForm(ilPropertyFormGUI $a_form)
	{		
		return array("subitems" => $a_form->getInput("subitems"));
	}
}