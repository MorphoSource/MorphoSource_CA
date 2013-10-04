<?php
/* ----------------------------------------------------------------------
 * controllers/Administration/ListController.php
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2013 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *
 * This source code is free and modifiable under the terms of 
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * ----------------------------------------------------------------------
 */
 
 	require_once(__CA_LIB_DIR__."/core/Error.php");
	require_once(__CA_LIB_DIR__."/core/Datamodel.php");
 
 	class ListController extends ActionController {
 		# -------------------------------------------------------
			protected $opo_project;
			protected $opn_project_id;
			protected $ops_project_name;
			
			protected $opo_item;
			protected $opn_item_id;
			protected $ops_item_name;
			protected $ops_name_singular;
			protected $ops_name_plural;
			protected $ops_primary_key;
			protected $ops_table;
			protected $opa_order_by;
			protected $opo_datamodel;

 		# -------------------------------------------------------
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
			parent::__construct($po_request, $po_response, $pa_view_paths);
 			if(!$this->request->isLoggedIn()){
 				$this->notification->addNotification("You must be logged in to access admin screens", __NOTIFICATION_TYPE_ERROR__);
				$this->response->setRedirect(caNavUrl($this->request, "", "LoginReg", "form"));
 			}
 			if(!$po_request->user->canDoAction("is_administrator")) {
 				$this->notification->addNotification("You must be an administrator access admin screens", __NOTIFICATION_TYPE_ERROR__);
				$this->response->setRedirect(caNavUrl($this->request, "", "", "")); 			
 			}
 			$ps_table = $this->request->getParameter("table", pString);
 			if((!$ps_table) || (!in_array($ps_table, array("ms_institutions", "ms_facilities", "ms_specimens", "ms_taxonomy_names", "ms_bibliography")))){
 				$this->notification->addNotification("invalid table", __NOTIFICATION_TYPE_ERROR__);
				$this->response->setRedirect(caNavUrl($this->request, "", "", ""));
 			}
 			$this->ops_table = $ps_table;
 			# --- load the object
			$this->opo_datamodel = Datamodel::load();
			$this->view->setVar("o_datamodel", $this->opo_datamodel);
 			if(!$this->opo_item = $this->opo_datamodel->getInstanceByTableName($this->ops_table, true)) {
 				die("Invalid table name ".$this->ops_table."");		// shouldn't happen
 			}
 			$this->view->setvar("table", $ps_table);
 			$this->opn_item_id = $this->request->getParameter($this->opo_item->getProperty("PRIMARY_KEY"), pInteger);
			if($this->opn_item_id){
				$this->opo_item->load($this->opn_item_id);
			}
			$this->view->setvar("item_id", $this->opn_item_id);
			$this->view->setvar("item", $this->opo_item);
			$this->ops_name_singular = $this->opo_item->getProperty("NAME_SINGULAR");
			$this->ops_name_plural = $this->opo_item->getProperty("NAME_PLURAL");
			$this->ops_primary_key = $this->opo_item->getProperty("PRIMARY_KEY");
			$this->view->setvar("name_singular", $this->ops_name_singular);
			$this->view->setvar("name_plural", $this->ops_name_plural);
			$this->view->setvar("primary_key", $this->ops_primary_key);
			$this->view->setvar($this->ops_primary_key, $this->opo_item->get($this->ops_primary_key));
			$this->ops_item_name = "";
			$va_list_fields = $this->opo_item->getProperty("LIST_FIELDS");	
			$this->view->setvar("list_fields", $va_list_fields);
			$va_order_by = $this->opo_item->getProperty("ORDER_BY");
			$this->opa_order_by = $va_order_by;
			$this->view->setvar("order_by", $va_order_by);
			if($this->opo_item->get($this->ops_primary_key)){
				$i = 0;
				foreach($va_list_fields as $vs_field){
					$this->ops_item_name .= $this->opo_item->get($vs_field);
					$i++;
					if(($i < sizeof($va_list_fields)) && ($this->opo_item->get($vs_field))){
						$this->ops_item_name .= ", ";
					}
				}
			}
			$this->view->setvar("item_name", $this->ops_item_name);			
 		}
 		# -------------------------------------------------------
 		public function form() {
 			switch($this->ops_table){
 				case "ms_facilities":
 					$this->view->setVar('scannerList', $this->opo_item->getScanners());
 				break;
 			}
 			$this->render('form_html.php');
 		}
 		# -------------------------------------------------------
 		public function listItems() {
			JavascriptLoadManager::register('tableList');
 			$o_db = new Db();
			$vs_order_by = "";
			if(is_array($this->opa_order_by) && sizeof($this->opa_order_by)){
				$vs_order_by = " ORDER BY ".implode(", ", $this->opa_order_by);
			}
			$q_listings = $o_db->query("SELECT * FROM ".$this->ops_table.$vs_order_by);
			$this->view->setVar("listings", $q_listings);
			$this->render('list_html.php');
 		}
 		# -------------------------------------------------------
 		public function save() {
			# get names of form fields
			$va_fields = $this->opo_item->getFormFields();
			$va_errors = array();
			# loop through fields
			
			while(list($vs_f,$va_attr) = each($va_fields)) {
				
				switch($vs_f) {
					# -----------------------------------------------
					case 'user_id':
						if(!$this->opo_item->get("user_id")){
							$this->opo_item->set($vs_f,$this->request->user->get("user_id"));
						}
						break;
					# -----------------------------------------------
					default:
						$this->opo_item->set($vs_f,$_REQUEST[$vs_f]); # set field values
						break;
					# -----------------------------------------------
				}
				if ($this->opo_item->numErrors() > 0) {
					foreach ($this->opo_item->getErrors() as $vs_e) {
						$va_errors[$vs_f] = $vs_e;
					}
				}
			}
		
			if (sizeof($va_errors) == 0) {
				# do insert or update
				$this->opo_item->setMode(ACCESS_WRITE);
				if ($this->opo_item->get($this->ops_primary_key)){
					$this->opo_item->update();
				} else {
					$this->opo_item->insert();
				}
	
				if ($this->opo_item->numErrors()) {
					foreach ($this->opo_item->getErrors() as $vs_e) {  
						$va_errors["general"] = $vs_e;
					}
				}else{
					$this->notification->addNotification("Saved ".$this->ops_name_singular, __NOTIFICATION_TYPE_INFO__);
				}
			}
			if(sizeof($va_errors) > 0){
				$this->notification->addNotification("There were errors in your form".(($va_errors["general"]) ? ": ".$va_errors["general"] : ""), __NOTIFICATION_TYPE_INFO__);
				$this->view->setVar("errors", $va_errors);
				$this->form();
			}else{
				$this->opn_item_id = $this->opo_item->get($this->ops_primary_key);
				$this->view->setVar("item_id", $this->opn_item_id);
				$this->view->setVar("item", $this->opo_item);
				$this->listItems();
			} 			 			
 		}
 		# -------------------------------------------------------
 		public function delete() {
 			if ($this->request->getParameter('delete_confirm', pInteger)) {
 				$va_errors = array();
				$this->opo_item->setMode(ACCESS_WRITE);
				$this->opo_item->delete(true);
				if ($this->opo_item->numErrors()) {
					foreach ($this->opo_item->getErrors() as $vs_e) {  
						$va_errors["general"] = $vs_e;
					}
					if(sizeof($va_errors) > 0){
						$this->notification->addNotification("There were errors".(($va_errors["general"]) ? ": ".$va_errors["general"] : ""), __NOTIFICATION_TYPE_INFO__);
					}
					$this->form();
				}else{
					$this->notification->addNotification("Deleted ".$this->ops_name_singular, __NOTIFICATION_TYPE_INFO__);
					$this->listItems();
				}				
			}else{
				$this->view->setVar("item_name", $this->ops_item_name);
				$this->render('delete_html.php');
			}
 		}
 		# -------------------------------------------------------
 	}
 ?>