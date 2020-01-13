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
	require_once(__CA_MODELS_DIR__."/ms_media_files.php");
	require_once(__CA_MODELS_DIR__."/ms_media_sets.php");
	require_once(__CA_MODELS_DIR__."/ms_media_set_items.php");
 	require_once(__CA_APP_DIR__.'/helpers/morphoSourceHelpers.php');
 	require_once(__CA_LIB_DIR__.'/core/Parsers/ZipStream.php');
 
 	class MediaCartController extends ActionController {
 		# -------------------------------------------------------
			protected $opo_media_set;
			protected $opn_set_id;
			protected $opn_media_file_id;
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
			# --- has a media_file_id been passed?  this is used for adding to set or removing from set
			$this->opn_media_file_id = $this->request->getParameter('media_file_id', pInteger);
			$this->view->setVar("media_file_id", $this->opn_media_file_id);
			# --- has a media_id been passed?  this is used for adding a group to set or removing from set
			$this->opn_media_id = $this->request->getParameter('media_id', pInteger);
			$this->view->setVar("media_id", $this->opn_media_id);
			# --- class to pass to cart link
			$this->view->setVar("linkClass", urldecode($this->request->getParameter('class', pString)));
 		}
 		# -------------------------------------------------------
 		public function cart() {
 			JavascriptLoadManager::register("panel");
 			# --- select all media in user's cart
 			$o_db = new Db();
 			$q_set_items = $o_db->query("SELECT si.media_file_id, m.specimen_id, m.published, mf.media, m.media_id FROM ms_media_set_items si INNER JOIN ms_media_files as mf ON si.media_file_id = mf.media_file_id INNER JOIN ms_media as m ON mf.media_id = m.media_id INNER JOIN ms_projects as p ON m.project_id = p.project_id WHERE si.set_id = ? AND p.deleted = 0", $this->opn_set_id);
 			$this->view->setVar("items", $q_set_items);
 			$this->render('MediaCart/cart_html.php');
 		}
 		# -------------------------------------------------------
 		public function add() {
 			if(!$this->opn_media_file_id){
 				$this->notification->addNotification("No media id was passed to add to cart", __NOTIFICATION_TYPE_ERROR__);
				$this->response->setRedirect(caNavUrl($this->request, "", "MediaCart", "Cart"));
 			}
 			$t_media_set_items = new ms_media_set_items();
 			# --- check the media hadn't already been added to the set
 			$t_media_set_items->load(array("set_id" => $this->opn_set_id, "media_file_id" => $this->opn_media_file_id));
 			if($t_media_set_items->get("item_id")){
 				$this->render('MediaCart/cart_link_html.php');
 			}else{
 				# --- add item
				$t_media_set_items->set('media_file_id', $this->opn_media_file_id);
				if ($t_media_set_items->numErrors() > 0) {
					foreach ($t_media_set_items->getErrors() as $vs_e) {
						$va_errors['media_file_id'] = $vs_e;
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
 			if(!$this->opn_media_file_id){
 				$this->notification->addNotification("No media id was passed to add to cart", __NOTIFICATION_TYPE_ERROR__);
				$this->response->setRedirect(caNavUrl($this->request, "", "MediaCart", "Cart"));
 			}
 			$t_media_set_item = new ms_media_set_items();
 			# --- load set item
 			$t_media_set_item->load(array("set_id" => $this->opn_set_id, "media_file_id" => $this->opn_media_file_id));
 			if($t_media_set_item->get("item_id")){
 				# --- remove item
				$t_media_set_item->setMode(ACCESS_WRITE);
 				$t_media_set_item->delete();
				if ($t_media_set_item->numErrors() > 0) {
					foreach ($t_media_set_item->getErrors() as $vs_e) {
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
 		public function addGroup() {
 			if(!$this->opn_media_id){
 				$this->notification->addNotification("No media group id was passed to add to cart", __NOTIFICATION_TYPE_ERROR__);
				$this->response->setRedirect(caNavUrl($this->request, "", "MediaCart", "Cart"));
				exit;
 			}
 			$t_media = new ms_media($this->opn_media_id);
 			if (!$this->request->isLoggedIn() || !$t_media->userCanDownloadMedia($this->request->getUserID())) {
				$this->notification->addNotification("You may not download this media", __NOTIFICATION_TYPE_ERROR__);
				$this->response->setRedirect(caNavUrl($this->request, "", "MediaCart", "Cart"));
				exit;
			}
 			# --- get files for the group
 			$o_db = new Db();
 			$q_media_files = $o_db->query("SELECT media_file_id FROM ms_media_files where media_id = ?", $this->opn_media_id);
			if($q_media_files->numRows()){
				while($q_media_files->nextRow()){
					if($t_media->userCanDownloadMediaFile($this->request->getUserID(), null, $q_media_files->get("media_file_id"))){
						$t_media_set_items = new ms_media_set_items();
						# --- check the media hadn't already been added to the set
						$t_media_set_items->load(array("set_id" => $this->opn_set_id, "media_file_id" => $q_media_files->get("media_file_id")));
						if(!$t_media_set_items->get("item_id")){
							# --- add item
							$t_media_set_items->set('media_file_id', $q_media_files->get("media_file_id"));
							if ($t_media_set_items->numErrors() > 0) {
								foreach ($t_media_set_items->getErrors() as $vs_e) {
									$va_errors['media_file_id'] = $vs_e;
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
							}
						}
					}
				}
				if (!sizeof($va_errors)){
					$this->render('MediaCart/cart_group_link_html.php');
				}
			}
 		}
 		# -------------------------------------------------------
 		public function removeGroup() {
 			if(!$this->opn_media_id){
 				$this->notification->addNotification("No media group id was passed to add to cart", __NOTIFICATION_TYPE_ERROR__);
				$this->response->setRedirect(caNavUrl($this->request, "", "MediaCart", "Cart"));
				exit;
 			}
 			# --- get files for the group
 			$o_db = new Db();
 			$q_media_files = $o_db->query("SELECT media_file_id FROM ms_media_files where media_id = ?", $this->opn_media_id);
			if($q_media_files->numRows()){
				while($q_media_files->nextRow()){
					$t_media_set_item = new ms_media_set_items();
					# --- load set item
					$t_media_set_item->load(array("set_id" => $this->opn_set_id, "media_file_id" => $q_media_files->get("media_file_id")));
					if($t_media_set_item->get("item_id")){
						# --- remove item
						$t_media_set_item->setMode(ACCESS_WRITE);
						$t_media_set_item->delete();
						if ($t_media_set_item->numErrors() > 0) {
							foreach ($t_media_set_item->getErrors() as $vs_e) {
								$va_errors[] = $vs_e;
							}
							$this->view->setVar("errors", join(", ", $va_errors));
						}
					}
				}				
				if (!sizeof($va_errors)){
					$this->render('MediaCart/cart_group_link_html.php');
				}
			}
 		}
 		# -------------------------------------------------------
 		public function addProjectMediaToCart() {
 			$t_project = new ms_projects();
			$pn_project_id = $this->request->getParameter('project_id', pInteger);
			$t_project->load($pn_project_id);
			if(!$t_project->isMember($this->opo_user->get("user_id"))){
				$this->notification->addNotification("You do not have access to the project", __NOTIFICATION_TYPE_ERROR__);
				$this->response->setRedirect(caNavUrl($this->request, "splash", "index", ""));
				return;
			}
			$t_media = new ms_media();
			
			# --- get media for project
			$va_project_media = $t_project->getProjectMedia();
			if(sizeof($va_project_media)){
				foreach ($va_project_media as $vn_media_id => $va_media) {
					# --- get files for the group
					$o_db = new Db();
					$q_media_files = $o_db->query("SELECT media_file_id FROM ms_media_files where media_id = ?", $vn_media_id);
					if($q_media_files->numRows()){
						$t_media->load($vn_media_id);
						while($q_media_files->nextRow()){
							if($t_media->userCanDownloadMediaFile($this->request->getUserID(), null, $q_media_files->get("media_file_id"))){
								$t_media_set_items = new ms_media_set_items();
								# --- check the media hadn't already been added to the set
								$t_media_set_items->load(array("set_id" => $this->opn_set_id, "media_file_id" => $q_media_files->get("media_file_id")));
								if(!$t_media_set_items->get("item_id")){
									# --- add item
									$t_media_set_items->set('media_file_id', $q_media_files->get("media_file_id"));
									if ($t_media_set_items->numErrors() > 0) {
										foreach ($t_media_set_items->getErrors() as $vs_e) {
											$va_errors['media_file_id'] = $vs_e;
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
										$this->notification->addNotification("There were errors adding media to your cart: ".join(", ", $va_errors), __NOTIFICATION_TYPE_ERROR__);
									}
								}
							}
						}
					}				
				}
			}else{
				$this->notification->addNotification("There were errors adding media to your  cart: Your project has no media.", __NOTIFICATION_TYPE_ERROR__);
				$this->cart();										
			}
			$this->notification->addNotification("Project media added to your cart", __NOTIFICATION_TYPE_ERROR__);
			$this->cart();
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
 			$q_set_items = $o_db->query("SELECT si.media_file_id, m.specimen_id, m.published, mf.media, m.media_id, mf.element, m.element media_element FROM ms_media_set_items si INNER JOIN ms_media_files as mf ON si.media_file_id = mf.media_file_id INNER JOIN ms_media as m ON mf.media_id = m.media_id INNER JOIN ms_projects as p ON m.project_id = p.project_id WHERE si.set_id = ? AND p.deleted = 0", $this->opn_set_id);
 			$t_media = new ms_media();
 			$t_specimens = new ms_specimens();
			if($q_set_items->numRows()){
				$ps_version = "_archive_";								
				$va_file_paths = array();
				$t_media_file = new ms_media_files();
				$va_media_file_ids = array();
				while($q_set_items->nextRow()){
					if($t_media->userCanDownloadMediaFile($this->request->getUserID(), $q_set_items->get("media_id"), $q_set_items->get("media_file_id"))){
						$vs_specimen_number = $t_specimens->getSpecimenNumber($q_set_items->get("specimen_id"));
                        if ($vs_specimen_number == '') {
                            $vs_specimen_name = '';
                            // for constructing file names, set the temp variables to no_specimen if no specimen 
                            $vs_specimen_name_temp = 'no_specimen';
                            $vs_specimen_number_temp = 'no_specimen';
                        } else {
                            $vs_specimen_name = str_replace(" ", "_", strip_tags(array_shift($t_specimens->getSpecimenTaxonomy($q_set_items->get("specimen_id")))));
                            $vs_specimen_name_temp = $vs_specimen_name;
                            $vs_specimen_number_temp = $vs_specimen_number;
                        }
						$vs_element = "";
						if($q_set_items->get("element")){
							$vs_element = "_".$q_set_items->get("element");
						}elseif($q_set_items->get("media_element")){
							$vs_element = "_".$q_set_items->get("media_element");
						}
						$vs_element = str_replace(" ", "_", $vs_element);
						# --- record download
						$t_media->recordDownload($this->request->getUserID(), $q_set_items->get("media_id"), $q_set_items->get("media_file_id"), $_REQUEST["intended_use"], $_REQUEST["intended_use_other"], $_REQUEST["3d_print"]);
						$vs_file_name = "";
						$vs_file_path = "";
						$t_media_file->load($q_set_items->get("media_file_id"));
						$ps_version = "_archive_";
						$va_versions = $t_media_file->getMediaVersions('media');
						if (!in_array($ps_version, $va_versions)) { $ps_version = 'original'; }
						if (!in_array($ps_version, $va_versions)) { $ps_version = $va_versions[0]; }
						$vs_idno_proc = $q_set_items->get("media_id");
						$va_version_info = $t_media_file->getMediaInfo('media', $ps_version);
						$vs_file_name = $vs_specimen_number_temp.'_M'.$vs_idno_proc.'-'.$q_set_items->get("media_file_id").'_'.$vs_specimen_name_temp.$vs_element.'.'.$va_version_info['EXTENSION'];
						$vs_file_path = $q_set_items->getMediaPath('media', $ps_version);
						$va_file_paths[$vs_file_path] = $vs_file_name;					
						$va_media_file_ids[] = $q_set_items->get("media_file_id");
					}
				}				
				if (sizeof($va_file_paths)) {
					if (!($vn_limit = ini_get('max_execution_time'))) { $vn_limit = 30; }
					set_time_limit($vn_limit * 2);
					
					$o_zip = new ZipStream();
					foreach($va_file_paths as $vs_path => $vs_name) {
						if(file_exists($vs_path)){
							$o_zip->addFile($vs_path, $vs_name);
						}
					}
					# --- generate text file for media downloaded and add it to zip
					$vs_tmp_file_name = '';
					$vs_text_file_name = "morphosourceMedia_".date('m_d_y_His').'.csv';
					$va_text_file_text = $t_media_file->mediaMdText($va_media_file_ids, $t_specimens);
					if(sizeof($va_text_file_text)){
						
						$vs_tmp_file_name = tempnam(caGetTempDirPath(), 'mediaDownloadTxt');
						$vo_file = fopen($vs_tmp_file_name, "w");
						foreach($va_text_file_text as $va_row){
							fputcsv($vo_file, $va_row);			
						}
						fclose($vo_file);
						$o_zip->addFile($vs_tmp_file_name, $vs_text_file_name);
					}
					
					# --- include download agreement form and add it to zip ---
					$vs_pdf_file_name = 'MorphoSource_download_use_agreement.pdf';
					$vs_pdf_file_location = $this->request->getThemeDirectoryPath().
						'/static/'.$vs_pdf_file_name;
					$o_zip->addFile($vs_pdf_file_location, $vs_pdf_file_name);
			

					$this->view->setVar('zip_stream', $o_zip);
					$this->view->setVar('version_download_name', preg_replace('![^A-Za-z0-9\.\-]+!', '_', "morphosourceMedia_".date('m_d_y_His')).'.zip');
					
					$this->response->sendHeaders();
					$vn_rc = $this->render('Detail/media_download_binary.php');
					$this->response->sendContent();
					
					if ($vs_tmp_file_name) { @unlink($vs_tmp_file_name); }
				}
				
			}else{
				$this->view->setVar("errors", "There is no media in your cart to download");
 				$this->cart();
			}
		}
 		# -------------------------------------------------------
 		public function DownloadCartMd() {
			# --- get items in cart
			$o_db = new Db();
 			$q_set_items = $o_db->query("SELECT si.media_file_id, m.specimen_id, m.published, mf.media, m.media_id FROM ms_media_set_items si INNER JOIN ms_media_files as mf ON si.media_file_id = mf.media_file_id INNER JOIN ms_media as m ON mf.media_id = m.media_id INNER JOIN ms_projects as p ON m.project_id = p.project_id WHERE si.set_id = ? AND p.deleted = 0", $this->opn_set_id);
 			$t_media = new ms_media();
 			$t_specimens = new ms_specimens();
			if($q_set_items->numRows()){
				$ps_version = "_archive_";								
				$va_file_paths = array();
				$t_media_file = new ms_media_files();
				$va_media_file_ids = array();
				while($q_set_items->nextRow()){
					if($t_media->userCanDownloadMediaFile($this->request->getUserID(), $q_set_items->get("media_id"), $q_set_items->get("media_file_id"))){
						$vs_specimen_name = $t_specimens->getSpecimenNumber($q_set_items->get("specimen_id"));
						$vs_file_name = "";
						$vs_file_path = "";
						$t_media_file->load($q_set_items->get("media_file_id"));
											
						$va_media_file_ids[] = $q_set_items->get("media_file_id");
					}
				}
				if(is_array($va_media_file_ids) && sizeof($va_media_file_ids)){
					if (!($vn_limit = ini_get('max_execution_time'))) { $vn_limit = 30; }
					set_time_limit($vn_limit * 2);
					# --- generate text file for media in cart
					$vs_tmp_file_name = tempnam(caGetTempDirPath(), 'mediaDownloadTxt');
					$vs_text_file_name = "morphosourceMedia_".date('m_d_y_His');
					$va_text_file_text = $t_media_file->mediaMdText($va_media_file_ids, $t_specimens);
					if(sizeof($va_text_file_text)){
						$vo_file = fopen($vs_tmp_file_name, "w");
						foreach($va_text_file_text as $va_row){
							fputcsv($vo_file, $va_row);			
						}
						fclose($vo_file);
						
						$o_zip = new ZipStream();

						# --- include download agreement form and add it to zip ---
						$vs_pdf_file_name = 'MorphoSource_download_use_agreement.pdf';
						$vs_pdf_file_location = $this->request->getThemeDirectoryPath().
							'/static/'.$vs_pdf_file_name;
						$o_zip->addFile($vs_pdf_file_location, $vs_pdf_file_name);

						$o_zip->addFile($vs_tmp_file_name, $vs_text_file_name.".csv");
						
						$this->view->setVar('zip_stream', $o_zip);
						$this->view->setVar('version_download_name', $vs_text_file_name.".zip");
					
						$this->response->sendHeaders();
						$vn_rc = $this->render('Detail/media_download_binary.php');
						$this->response->sendContent();
						
						@unlink($vs_tmp_file_name);
					}
					
					return $vn_rc;
				}
			}else{
				$this->view->setVar("errors", "There is no media in your cart to download");
 				$this->cart();
			}
		}	
 		# -------------------------------------------------------
 		public function removeByMimetype() {
 			$vs_mimetype = $this->request->getParameter('mimetype', pString);
 			if($vs_mimetype){
 				$va_errors = array();
 				# --- get items in cart
				$t_media_set_item = new ms_media_set_items();
				$o_db = new Db();
 				$q_set_items = $o_db->query("SELECT si.item_id, si.media_file_id, mf.media FROM ms_media_set_items si INNER JOIN ms_media_files as mf ON si.media_file_id = mf.media_file_id WHERE si.set_id = ?", $this->opn_set_id);
				if($q_set_items->numRows()){
					$va_items_to_remove = array();
					while($q_set_items->nextRow()){
						$va_properties = $q_set_items->getMediaInfo('media', 'original');
						if($va_properties["MIMETYPE"] == $vs_mimetype){
							# --- remove the set item from cart
							$va_items_to_remove[] = $q_set_items->get("item_id");
							 # --- remove item
							$t_media_set_item->load($q_set_items->get("item_id"));
							$t_media_set_item->setMode(ACCESS_WRITE);
							$t_media_set_item->delete();
							if ($t_media_set_item->numErrors() > 0) {
								foreach ($t_media_set_item->getErrors() as $vs_e) {
									$va_errors[] = $vs_e;
								}
							}

						}
					}
					if(sizeof($va_errors)){
						$this->view->setVar("errors", join(", ", $va_errors));
					}
					#print_r($va_items_to_remove);
					if(sizeof($va_items_to_remove)){
						$this->notification->addNotification("Removed ".sizeof($va_items_to_remove)." files from your cart", __NOTIFICATION_TYPE_ERROR__);
					}
				}
 			}
 			$this->cart();
 		}
 		# -------------------------------------------------------
 		 function MCZspecimen() {
 		 	$o_db = new Db();
 		 	$q_specimen_ids = $o_db->query("select mf.media_file_id, m.media_id, m.project_id, m.specimen_id from ms_specimens s INNER JOIN ms_media as m ON m.specimen_id = s.specimen_id  INNER JOIN ms_media_files as mf ON mf.media_id = m.media_id where s.institution_code = 'MCZ'");
 		 	if($q_specimen_ids->numRows()){
 		 		while($q_specimen_ids->nextRow()){
 		 			$t_media_set_items = new ms_media_set_items();
					# --- add item
					$t_media_set_items->set('media_file_id', $q_specimen_ids->get("media_file_id"));
					$t_media_set_items->set('set_id', 41);
					$t_media_set_items->setMode(ACCESS_WRITE);
					$t_media_set_items->insert();
				
					if ($t_media_set_items->numErrors()) {
						foreach ($t_media_set_items->getErrors() as $vs_e) {  
							print "media_file_id:".$q_specimen_ids->get("media_file_id")." - ".$vs_e."<br/>";
						}
					}
				}
 		 	}
		}
 		# -------------------------------------------------------
 	}
 ?>