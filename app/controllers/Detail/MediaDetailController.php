<?php
/* ----------------------------------------------------------------------
 * controllers/MyProjects/MediaDetailController.php
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2013-2015 Whirl-i-Gig
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
 	require_once(__CA_MODELS_DIR__."/ms_projects.php");
 	require_once(__CA_MODELS_DIR__."/ms_media.php");
 	require_once(__CA_MODELS_DIR__."/ms_media_files.php");
 	require_once(__CA_MODELS_DIR__."/ms_specimens.php");
 	require_once(__CA_MODELS_DIR__."/ms_taxonomy_names.php");
 	require_once(__CA_MODELS_DIR__."/ms_bibliography.php");
 	require_once(__CA_MODELS_DIR__."/ms_media_x_bibliography.php");
 	require_once(__CA_MODELS_DIR__."/ms_facilities.php");
 	require_once(__CA_MODELS_DIR__."/ms_institutions.php");
 	require_once(__CA_APP_DIR__.'/helpers/morphoSourceHelpers.php');
 	require_once(__CA_LIB_DIR__.'/ca/ResultContext.php');
 	require_once(__CA_LIB_DIR__.'/core/Parsers/ZipStream.php');
 
 	class MediaDetailController extends ActionController {
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
 			protected $ops_context = '';

 		# -------------------------------------------------------
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 			parent::__construct($po_request, $po_response, $pa_view_paths);
 			JavascriptLoadManager::register("panel");
 			JavascriptLoadManager::register("3dmodels");
 			
 			# --- don't check access when just loading the download survey for media cart
 			if(!$this->request->getParameter('set_id', pInteger)){
				# --- load the media object
				$this->opo_item = new ms_media();
				if (!($this->opn_item_id = $this->request->getParameter('media_id', pInteger))) {
					$vn_file_id = $this->request->getParameter('media_file_id', pInteger);
					$t_file = new ms_media_files($vn_file_id);
					$this->opn_item_id = $t_file->get('media_id');
				}
				if($this->opn_item_id){
					$this->opo_item->load($this->opn_item_id);
				}
				if(!$this->opo_item->get("media_id")){
					$this->notification->addNotification("Invalid media_id", __NOTIFICATION_TYPE_ERROR__);
					$this->response->setRedirect(caNavUrl($this->request, "splash", "index", ""));
				}
				# --- does user have read only access to the media group?
				if($this->opo_item->userHasReadOnlyAccessToMedia($this->request->user->get("user_id"))){
					$this->opn_read_only = true;
					# --- check if this is a shared media so can display the info about it
					$o_db = new Db();
					$q_media_shares = $o_db->query("SELECT ms.created_on, u.fname, u.lname, u.email, ms.use_restrictions FROM ms_media_shares ms INNER JOIN ca_users as u ON ms.shared_by_user_id = u.user_id WHERE ms.user_id = ? AND ms.media_id = ? AND ms.created_on > ".(time() - (60 * 60 * 24 * 30)), $this->request->user->get("user_id"), $this->opn_item_id);
					if($q_media_shares->numRows()){
						$q_media_shares->nextRow();
						$this->view->setVar("share", true);
						$this->view->setVar("share_use_restrictions", $q_media_shares->get("use_restrictions"));
						$this->view->setVar("share_expires", date("m/d/y", $q_media_shares->get("created_on") + (60 * 60 * 24 * 30)));
						$this->view->setVar("share_shared_by", trim($q_media_shares->get("fname")." ".$q_media_shares->get("lname"))." (".$q_media_shares->get("email").")");
					}
				}else{
					$this->opn_read_only = false;
				}
				if ($this->opo_item->get("published") == 0) {
					# --- item is not published
					# --- check if user has access to the project
					$t_project = new ms_projects($this->opo_item->get("project_id"));
					if(!($this->request->isLoggedIn()) || (!$t_project->isMember($this->request->user->get("user_id")) && !$this->opn_read_only)){
						$this->notification->addNotification("Item is not published", __NOTIFICATION_TYPE_ERROR__);
						$this->response->setRedirect(caNavUrl($this->request, "splash", "index", ""));
					}
				}
				$t_project = new ms_projects($this->opo_item->get("project_id"));
				if($t_project->get("deleted")){
					$this->notification->addNotification("Item is deleted", __NOTIFICATION_TYPE_ERROR__);
					$this->response->setRedirect(caNavUrl($this->request, "splash", "index", ""));
				}
				$this->view->setvar("item_id", $this->opn_item_id);
				$this->view->setvar("media_id", $this->opn_item_id);
				$this->view->setvar("item", $this->opo_item);
			
				$this->view->setvar("read_only", $this->opn_read_only);
				# Next and previous navigation
				$opo_result_context = new ResultContext($this->request, "ms_media", ResultContext::getLastFind($this->request, "ms_media"));
				# Is the item we're show details for in the result set?
				$this->view->setVar('is_in_result_list', ($opo_result_context->getIndexInResultList($this->opn_item_id) != '?'));
					
				$this->view->setVar('next_id', $opo_result_context->getNextID($this->opn_item_id));
				$this->view->setVar('previous_id', $opo_result_context->getPreviousID($this->opn_item_id));
				$this->view->setVar('result_context', $opo_result_context);
			} 			
 		}
 		# -------------------------------------------------------
 		public function show() {
 		 	$va_bib_citations = array();
 			if($this->opn_item_id){
 				$this->opo_item->recordView($this->request->getUserID());
 				$o_db = new Db();
 				$q_bib = $o_db->query("SELECT b.*, mxb.link_id, mxb.pp, mxb.media_file_id FROM ms_media_x_bibliography mxb INNER JOIN ms_bibliography as b on mxb.bibref_id = b.bibref_id WHERE mxb.media_id = ?", $this->opn_item_id);
 				$t_bibliography = new ms_bibliography;
 				if($q_bib->numRows()){
 					while($q_bib->nextRow()){
 						$va_bib_citations[$q_bib->get("link_id")] = array("citation" => $t_bibliography->getCitationText($q_bib->getRow()), "link_id" => $q_bib->get("link_id"), "media_file_id" => $q_bib->get("media_file_id"), "page" => $q_bib->get("pp"), "bibref_id" => $q_bib->get("bibref_id"));
 					}
 				}
				$t_project = new ms_projects();
				# --- can user edit record? - must have full access to the project
				$vb_show_edit_link = false;
				if($this->request->isLoggedIn() && $t_project->isFullAccessMember($this->request->user->get("user_id"), $this->opo_item->get("project_id"))){
					$vb_show_edit_link = true;
				}
				# --- can user download record even if it is unpublished? - can be read only project member to download
				# --- do we need to limit what files are shown based on publication status of group and files?
				$vb_show_download_link = false;
				$vb_show_all_files = false;
				if($this->request->isLoggedIn() && ($t_project->isMember($this->request->user->get("user_id"), $this->opo_item->get("project_id")) || $this->opn_read_only)){
					$vb_show_download_link = true;
					$vb_show_all_files = true;
				}
 			}
 			$this->view->setVar("show_edit_link", $vb_show_edit_link);
 			$this->view->setVar("show_download_link", $vb_show_download_link);
 			$this->view->setVar("show_all_files", $vb_show_all_files);
 			$this->view->setVar("bib_citations", $va_bib_citations);
 			$this->render('ms_media_detail_html.php');
 		}
  		# -------------------------------------------------------
		/**
		 * Download survey
		 */ 
		public function DownloadMediaSurvey() {
			$pn_media_file_id = $this->request->getParameter('media_file_id', pInteger);
			$pn_media_id = $this->request->getParameter('media_id', pInteger);
			$pn_set_id = $this->request->getParameter('set_id', pInteger);
			$ps_download_action = $this->request->getParameter('download_action', pString);
			
			$this->view->setVar("media_id", $pn_media_id);
			$this->view->setVar("media_file_id", $pn_media_file_id);
			$this->view->setVar("set_id", $pn_set_id);
			$this->view->setVar("download_action", $ps_download_action);
			
			$this->render('ms_download_survey_html.php');
		}
 		# -------------------------------------------------------
		/**
		 * Download media
		 */ 
		public function DownloadMedia() {
			$t_media_file = new ms_media_files();
			$pn_media_file_id = $this->request->getParameter('media_file_id', pInteger);
			$t_media_file->load($pn_media_file_id);
			if(!$t_media_file->get("media_file_id")){
				$va_errors["general"] = "media file id not defined";
				$this->mediaInfo();
			}
			if (!$this->request->isLoggedIn() || !$this->opo_item->userCanDownloadMediaFile($this->request->getUserID(), null, $pn_media_file_id)) {
				return;
			}
			$vs_element = "";
			if($t_media_file->get("element")){
				$vs_element = "_".$t_media_file->get("element");
			}else{
				$t_media = new ms_media($t_media_file->get("media_id"));
				if($t_media->get("element")){
					$vs_element = "_".$t_media->get("element");
				}
			}
			$vs_element = str_replace(" ", "_", $vs_element);
			$t_specimens = new ms_specimens();
			$ps_version = "_archive_";
			
			$va_versions = $t_media_file->getMediaVersions('media');
			
			if (!in_array($ps_version, $va_versions)) { $ps_version = 'original'; }
			if (!in_array($ps_version, $va_versions)) { $ps_version = $va_versions[0]; }
			
			$va_version_info = $t_media_file->getMediaInfo('media', $ps_version);
			
			$va_info = $t_media_file->getMediaInfo('media');
			$vs_idno_proc = $this->opo_item->get('media_id');
			$vs_specimen_number = $t_specimens->getSpecimenNumber($this->opo_item->get("specimen_id"));
            if ($vs_specimen_number == '') {
                $vs_specimen_name = '';
                // for constructing file names, set the temp variables to no_specimen if no specimen 
                $vs_specimen_number_temp = 'no_specimen';
            } else {
                $vs_specimen_name = str_replace(" ", "_", strip_tags(array_shift($t_specimens->getSpecimenTaxonomy($this->opo_item->get("specimen_id")))));
                $vs_specimen_number_temp = $vs_specimen_number;
            }
			# --- record download
			$this->opo_item->recordDownload($this->request->getUserID(), $this->opo_item->get("media_id"), $pn_media_file_id, $_REQUEST["intended_use"], $_REQUEST["intended_use_other"], $_REQUEST["3d_print"]);
			
			if (!($vn_limit = ini_get('max_execution_time'))) { $vn_limit = 30; }
			set_time_limit($vn_limit * 2);
			
			$o_zip = new ZipStream();
			$vs_path = $t_media_file->getMediaPath('media', $ps_version);
			if(file_exists($vs_path)){
				$o_zip->addFile($vs_path, preg_replace("![^A-Za-z0-9_\-]+!", "_", $vs_specimen_number_temp.'_M'.$vs_idno_proc.'-'.$pn_media_file_id).'.'.$va_version_info['EXTENSION']);	// don't try to compress
			}
			# --- generate text file for media downloaded and add it to zip
			$vs_tmp_file_name = '';
			$vs_text_file_name = 'Morphosource_'.$vs_specimen_number_temp.'_M'.$vs_idno_proc.'-'.$pn_media_file_id.'.csv';
			$va_text_file_text = $t_media_file->mediaMdText(array($pn_media_file_id), $t_specimens);
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
			
			$this->view->setVar('version_path', $vs_path = $t_media_file->getMediaPath('media', $ps_version));
			$this->view->setVar('version_download_name', preg_replace("![^A-Za-z0-9_\-]+!", "_", 'Morphosource_'.$vs_specimen_number_temp.'_M'.$vs_idno_proc.'-'.$pn_media_file_id).'.zip');
				
			$this->response->sendHeaders();
			$vn_rc = $this->render('media_download_binary.php');
			
			$this->response->sendContent();
			
			if ($vs_tmp_file_name) { @unlink($vs_tmp_file_name); }
		}
		# -------------------------------------------------------
		/**
		 * Download all media files for media
		 */ 
		public function DownloadAllMedia() {
			if (!$this->request->isLoggedIn() || !$this->opo_item->userCanDownloadMedia($this->request->getUserID())) {
				$this->notification->addNotification("You may not download this media", __NOTIFICATION_TYPE_ERROR__);
				$this->show();
				return;
			}
			
			# --- get all media files
			$o_db = new Db();
			$q_media_files = $o_db->query("SELECT mf.media, mf.media_file_id, mf.element, m.element media_element FROM ms_media_files mf INNER JOIN ms_media AS m ON m.media_id = mf.media_id where mf.media_id = ?", $this->opo_item->get("media_id"));
			if($q_media_files->numRows()){
				$t_specimens = new ms_specimens();
				$t_media_file = new ms_media_files();
				$vs_specimen_number = $t_specimens->getSpecimenNumber($this->opo_item->get("specimen_id"));
                if ($vs_specimen_number == '') {
                    $vs_specimen_name = '';
                    // for constructing file names, set the temp variables to no_specimen if no specimen 
                    $vs_specimen_name_temp = 'no_specimen';
                    $vs_specimen_number_temp = 'no_specimen'; 
                } else {
                    $vs_specimen_name = str_replace(" ", "_", strip_tags(array_shift($t_specimens->getSpecimenTaxonomy($this->opo_item->get("specimen_id")))));
                    $vs_specimen_name_temp = $vs_specimen_name;
                    $vs_specimen_number_temp = $vs_specimen_number;
                }
				$va_media_file_ids = array();
				while($q_media_files->nextRow()){
					if($this->opo_item->userCanDownloadMediaFile($this->request->getUserID(), null, $q_media_files->get("media_file_id"))){
						$t_media_file->load($q_media_files->get("media_file_id"));
						$vs_element = "";
						if($q_media_files->get("element")){
							$vs_element = "_".$q_media_files->get("element");
						}elseif($q_media_files->get("media_element")){
							$vs_element = "_".$q_media_files->get("media_element");
						}
						$vs_element = str_replace(" ", "_", $vs_element);
						$ps_version = "_archive_";
						$va_versions = $t_media_file->getMediaVersions('media');
						if (!in_array($ps_version, $va_versions)) { $ps_version = 'original'; }
						if (!in_array($ps_version, $va_versions)) { $ps_version = $va_versions[0]; }
						$vs_idno_proc = $this->opo_item->get('media_id');
						$va_version_info = $t_media_file->getMediaInfo('media', $ps_version);
					
						$vs_file_name = preg_replace("![^A-Za-z0-9_\-]+!", "_", $vs_specimen_number_temp.'_M'.$vs_idno_proc.'-'.$q_media_files->get("media_file_id").'_'.$vs_specimen_name_temp.$vs_element).'.'.$va_version_info['EXTENSION'];
						$vs_file_path = $q_media_files->getMediaPath('media', $ps_version);
						$va_file_names[$vs_file_name] = true;
						$va_file_paths[$vs_file_path] = $vs_file_name;
						# --- record download
						$this->opo_item->recordDownload($this->request->getUserID(), $this->opo_item->get("media_id"), $q_media_files->get("media_file_id"), $_REQUEST["intended_use"], $_REQUEST["intended_use_other"], $_REQUEST["3d_print"]);			
						$va_media_file_ids[] = $q_media_files->get("media_file_id");
					}
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
                $vs_text_file_name = 'Morphosource_'.$vs_specimen_name_temp.'_M'.$vs_idno_proc.'.csv';                    
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
			
				$this->response->sendHeaders();
				$this->view->setVar('version_download_name', preg_replace("![^A-Za-z0-9_\-]+!", "_", 'Morphosource_'.$vs_specimen_number_temp.'_M'.$vs_idno_proc).'.zip');
				$vn_rc = $this->render('media_download_binary.php');
			
				$this->response->sendContent();
				
				if ($vs_tmp_file_name) { @unlink($vs_tmp_file_name); }
			}
				
		}
		# -------------------------------------------------------
		/**
		 * Request access to media
		 */ 
		public function RequestDownload() {
			if (!$this->request->isLoggedIn()) {
				$this->notification->addNotification("You must login to request download of this media", __NOTIFICATION_TYPE_ERROR__);
				$this->show();
				return;
			}
			#if ($this->opo_item->userCanDownloadMedia($this->request->getUserID())) {
			#	$this->DownloadMedia();
			#	return;
			#}
			
			// record request
			if (!$this->request->getParameter('request', pString) || !$this->request->getUserID() || !$this->request->getUser()->get('email')) { 
				$this->notification->addNotification("You must describe your planned usage.", __NOTIFICATION_TYPE_ERROR__);
			}elseif (!$this->opo_item->requestDownload($this->request->getUserID(), $this->request->getParameter('request', pString), null, array('request' => $this->request))) {
				$this->notification->addNotification("Could not save media request. Try again later.", __NOTIFICATION_TYPE_ERROR__);
			} else {
				$this->notification->addNotification("Sent your request to the author.", __NOTIFICATION_TYPE_INFO__);
			}
			$this->Show();
		}
 		# -------------------------------------------------------
 		public function mediaViewer() {
 			$pn_media_id = $this->request->getParameter('media_id', pInteger);
 			// TODO: does user own this media?
 			$t_media = new ms_media($pn_media_id);
 			$t_media_files = new ms_media_files();
 			$pn_media_file_id = $this->request->getParameter('media_file_id', pInteger);
 			$t_media_files->load($pn_media_file_id);
 			$this->view->setVar('t_media_file', $t_media_files);
 			$this->view->setVar('media_file_id', $pn_media_file_id);
 			$this->view->setVar('t_media', $t_media);
 			$this->render('../MyProjects/Media/ajax_media_viewer_html.php');
 		}
 		# -------------------------------------------------------
 	}
 ?>