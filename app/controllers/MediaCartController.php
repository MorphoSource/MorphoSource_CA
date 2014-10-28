<?php
/* ----------------------------------------------------------------------
 * controllers/MediaCartController.php
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2014 Whirl-i-Gig
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
	require_once(__CA_MODELS_DIR__."/ca_users.php");
	require_once(__CA_MODELS_DIR__."/ms_media.php");
	require_once(__CA_MODELS_DIR__."/ms_media_sets.php");
	require_once(__CA_MODELS_DIR__."/ms_media_set_items.php");
 	require_once(__CA_APP_DIR__.'/helpers/morphoSourceHelpers.php');
 
 	class MediaCartController extends ActionController {
 		# -------------------------------------------------------
			protected $opo_media_set;
			protected $opn_set_id;
			protected $opn_media_id;
			protected $opo_user;

 		# -------------------------------------------------------
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 			parent::__construct($po_request, $po_response, $pa_view_paths);
 			if(!$this->request->isLoggedIn()){
 				$this->notification->addNotification("You must be logged in to access your media cart", __NOTIFICATION_TYPE_ERROR__);
				$this->response->setRedirect(caNavUrl($this->request, "", "LoginReg", "form"));
 			}
 			$this->opo_user = $this->request->user;
 			$this->opo_media_set = new ms_media_sets();
			# --- Does the user already have a media set?  Or do we need to create on for them?
			$this->opo_media_set->load(array("user_id" => $this->opo_user->get("user_id")));
			if(!$this->opo_media_set->get("set_id")){
				# --- no set for user so make one now
				$this->opo_media_set->set("user_id", $this->opo_user->get("user_id"));
				if ($this->opo_media_set->numErrors() > 0) {
					foreach ($this->opo_media_set->getErrors() as $vs_e) {
						$va_errors["user_id"] = $vs_e;
					}
				}		
				if (sizeof($va_errors) == 0) {
					# do insert
					$this->opo_media_set->setMode(ACCESS_WRITE);
					$this->opo_media_set->insert();
					
					if ($this->opo_media_set->numErrors()) {
						foreach ($this->opo_media_set->getErrors() as $vs_e) {  
							$va_errors["general"] = $va_errors["general"]." ".$vs_e.", ";
						}
					}
				}
				if (sizeof($va_errors)){
					$this->notification->addNotification("There were errors creating your media cart: ".join(", ", $va_errors), __NOTIFICATION_TYPE_ERROR__);
					$this->response->setRedirect(caNavUrl($this->request, "", "MediaCart", "Cart"));
				}
			}
			$this->opn_set_id = $this->opo_media_set->get("set_id");
			if(!$this->opn_set_id){
				$this->notification->addNotification("No cart selected", __NOTIFICATION_TYPE_ERROR__);
				$this->response->setRedirect(caNavUrl($this->request, "", "MediaCart", "Cart"));
			}
			$this->view->setVar("set_id", $this->opn_set_id);
			# --- has a media_id been passed?  this is used for adding to set or removing from set
			$this->opn_media_id = $this->request->getParameter('media_id', pInteger);
			$this->view->setVar("media_id", $this->opn_media_id);
			# --- class to pass to cart link
			$this->view->setVar("linkClass", urldecode($this->request->getParameter('class', pString)));
 		}
 		# -------------------------------------------------------
 		public function cart() {
 			# --- select all media in user's cart
 			$o_db = new Db();
 			$q_set_items = $o_db->query("SELECT si.media_id, m.media, m.specimen_id FROM ms_media_set_items si INNER JOIN ms_media as m ON si.media_id = m.media_id WHERE si.set_id = ?", $this->opn_set_id);
 			$this->view->setVar("items", $q_set_items);
 			$this->render('MediaCart/cart_html.php');
 		}
 		# -------------------------------------------------------
 		public function add() {
 			if(!$this->opn_media_id){
 				$this->notification->addNotification("No media id was passed to add to cart", __NOTIFICATION_TYPE_ERROR__);
				$this->response->setRedirect(caNavUrl($this->request, "", "MediaCart", "Cart"));
 			}
 			$t_media_set_items = new ms_media_set_items();
 			# --- check the media hadn't already been added to the set
 			$t_media_set_items->load(array("set_id" => $this->opn_set_id, "media_id" => $this->opn_media_id));
 			if($t_media_set_items->get("item_id")){
 				$this->render('MediaCart/cart_link_html.php');
 			}else{
 				# --- add item
				$t_media_set_items->set('media_id', $this->opn_media_id);
				if ($t_media_set_items->numErrors() > 0) {
					foreach ($t_media_set_items->getErrors() as $vs_e) {
						$va_errors['media_id'] = $vs_e;
					}
				}
				$t_media_set_items->set('set_id', $this->opn_set_id);
				if ($t_media_set_items->numErrors() > 0) {
					foreach ($t_media_set_items->getErrors() as $vs_e) {
						$va_errors['set_id'] = $vs_e;
					}
				}
				if (sizeof($va_errors) == 0) {
					# do insert
					$t_media_set_items->setMode(ACCESS_WRITE);
					$t_media_set_items->insert();
					
					if ($t_media_set_items->numErrors()) {
						foreach ($t_media_set_items->getErrors() as $vs_e) {  
							$va_errors["general"] = $va_errors["general"]." ".$vs_e.", ";
						}
					}
				}
				if (sizeof($va_errors)){
					$this->notification->addNotification("There were errors adding media to your  cart: ".join(", ", $va_errors), __NOTIFICATION_TYPE_ERROR__);
					$this->response->setRedirect(caNavUrl($this->request, "", "MediaCart", "Cart"));
				}else{
					$this->render('MediaCart/cart_link_html.php');
				}
 			}
 		}
 		# -------------------------------------------------------
 		public function remove() {
 			if(!$this->opn_media_id){
 				$this->notification->addNotification("No media id was passed to add to cart", __NOTIFICATION_TYPE_ERROR__);
				$this->response->setRedirect(caNavUrl($this->request, "", "MediaCart", "Cart"));
 			}
 			$t_media_set_item = new ms_media_set_items();
 			# --- load set item
 			$t_media_set_item->load(array("set_id" => $this->opn_set_id, "media_id" => $this->opn_media_id));
 			if($t_media_set_item->get("item_id")){
 				# --- remove item
				$t_media_set_item->setMode(ACCESS_WRITE);
 				$t_media_set_item->delete();
				if ($t_media_set_item->numErrors() > 0) {
					foreach ($t_bib_link->getErrors() as $vs_e) {
						$va_errors[] = $vs_e;
					}
					$this->view->setVar("errors", join(", ", $va_errors));
				}
 			}
 			if($this->request->isAjax()){
 				$this->render('MediaCart/cart_link_html.php');
 			}else{
 				$this->cart();
 			}
 		}
 		# -------------------------------------------------------
 		public function clearCart() {
 			if(!$this->opn_set_id){
 				$this->notification->addNotification("No set to clear", __NOTIFICATION_TYPE_ERROR__);
				$this->response->setRedirect(caNavUrl($this->request, "", "MediaCart", "Cart"));
 			}
 			$o_db = new Db();
 			$q_set_items = $o_db->query("DELETE FROM ms_media_set_items WHERE set_id = ?", $this->opn_set_id);
 			if ($q_set_items->numErrors() > 0) {
				foreach ($q_set_items->getErrors() as $vs_e) {
					$va_errors[] = $vs_e;
				}
				$this->view->setVar("errors", join(", ", $va_errors));
			}
 			$this->cart();
 		}
 		# -------------------------------------------------------
 		public function DownloadCart() {
			# --- get items in cart
			$o_db = new Db();
 			$q_set_items = $o_db->query("SELECT si.media_id, m.media, m.specimen_id FROM ms_media_set_items si INNER JOIN ms_media as m ON si.media_id = m.media_id WHERE si.set_id = ?", $this->opn_set_id);
 			$t_media = new ms_media();
 			$t_specimens = new ms_specimens();
			if($q_set_items->numRows()){
				$ps_version = "_archive_";								
				$va_file_names = array();
				$va_file_paths = array();
				while($q_set_items->nextRow()){
					$vs_file_name = "";
					$vs_file_path = "";
					$va_versions = $q_set_items->getMediaVersions('media');			
					if (!in_array($ps_version, $va_versions)) { $ps_version = 'original'; }
					if (!in_array($ps_version, $va_versions)) { $ps_version = $va_versions[0]; }$vs_idno_proc = $q_set_items->get('media_id');
					$vs_specimen_number = $t_specimens->getSpecimenNumber($q_set_items->get("specimen_id"));
					$va_version_info = $q_set_items->getMediaInfo('media', $ps_version);
					$vs_file_name = $vs_specimen_number.'_M'.$vs_idno_proc.'.'.$va_version_info['EXTENSION'];
					$vs_file_path = $q_set_items->getMediaPath('media', $ps_version);
					# --- record download
					$t_media->recordDownload($this->request->getUserID(), $q_set_items->get("media_id"));
					$va_file_names[$vs_file_name] = true;
					$va_file_paths[$vs_file_path] = $vs_file_name;	
				}				
				if (sizeof($va_file_paths) > 1) {
					if (!($vn_limit = ini_get('max_execution_time'))) { $vn_limit = 30; }
					set_time_limit($vn_limit * 2);
					$o_zip = new ZipFile();
					foreach($va_file_paths as $vs_path => $vs_name) {
						if(file_exists($vs_path)){
							$o_zip->addFile($vs_path, $vs_name, null, array('compression' => 0));	// don't try to compress
						}
					}
					$this->view->setVar('version_path', $vs_path = $o_zip->output(ZIPFILE_FILEPATH));
					$this->view->setVar('version_download_name', preg_replace('![^A-Za-z0-9\.\-]+!', '_', "morphosourceMedia_".date('m_d_y_His')).'.zip');
					
					$this->response->sendHeaders();
					$vn_rc = $this->render('Detail/media_download_binary.php');
					$this->response->sendContent();
				} else {
					foreach($va_file_paths as $vs_path => $vs_name) {
						$this->view->setVar('version_path', $vs_path);
						$this->view->setVar('version_download_name', $vs_name);
					}
					$this->response->sendHeaders();
					$vn_rc = $this->render('Detail/media_download_binary.php');
					$this->response->sendContent();
				}
				
				return $vn_rc;
			}else{
				$this->view->setVar("errors", "There is no media in your cart to download");
 				$this->cart();
			}
		}		
 		# -------------------------------------------------------
 	}
 ?>