<?php
/* ----------------------------------------------------------------------
 * controllers/MyProjects/SpecimenDetailController.php
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
 	require_once(__CA_MODELS_DIR__."/ms_projects.php");
 	require_once(__CA_MODELS_DIR__."/ms_media.php");
 	require_once(__CA_MODELS_DIR__."/ms_specimens.php");
 	require_once(__CA_MODELS_DIR__."/ms_taxonomy_names.php");
 	require_once(__CA_MODELS_DIR__."/ms_bibliography.php");
 	require_once(__CA_MODELS_DIR__."/ms_media_x_bibliography.php");
 	require_once(__CA_MODELS_DIR__."/ms_facilities.php");
 	require_once(__CA_MODELS_DIR__."/ms_institutions.php");
 	require_once(__CA_APP_DIR__.'/helpers/morphoSourceHelpers.php');
 	require_once(__CA_LIB_DIR__.'/ca/ResultContext.php');
 
 	class SpecimenDetailController extends ActionController {
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
 		/**
 		 * Sets current browse context
 		 * Settings for the current browse are stored per-context. This means if you
 		 * have multiple interfaces in the same application using browse services
 		 * you can keep their settings (and caches) separate by varying the context.
 		 *
 		 * The browse engine and browse controller both have their own context settings
 		 * but the BaseDetailController is setup to make the browse engine's context its own.
 		 * Thus you only need set the context for the engine; the controller will inherit it.
 		 */
 		public function setContext($ps_context) {
 			$this->ops_context = $ps_context;
 		}
 		# -------------------------------------------------------
 		/**
 		 * Returns the current browse context
 		 */
 		public function getContext($ps_context) {
 			return $this->ops_context;
 		}
 		# -------------------------------------------------------
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 			parent::__construct($po_request, $po_response, $pa_view_paths);
 			JavascriptLoadManager::register("panel");
 			JavascriptLoadManager::register("3dmodels");
 			
 			# --- load the specimen object
			$this->opo_item = new ms_specimens();
			$this->opn_item_id = $this->request->getParameter('specimen_id', pInteger);
			if($this->opn_item_id){
				$this->opo_item->load($this->opn_item_id);
			}
			if(!$this->opo_item->get("specimen_id")){
				$this->notification->addNotification("Invalid specimen_id", __NOTIFICATION_TYPE_ERROR__);
				$this->response->setRedirect(caNavUrl($this->request, "splash", "index", ""));
			}
// 			if (!$this->opo_item->get("published") == 1) {
// 				$this->notification->addNotification("Item is not published", __NOTIFICATION_TYPE_ERROR__);
// 				$this->response->setRedirect(caNavUrl($this->request, "splash", "index", ""));
// 			}
			$this->view->setvar("item_id", $this->opn_item_id);
			$this->view->setvar("specimen_id", $this->opn_item_id);
			$this->view->setvar("item", $this->opo_item);
			# Next and previous navigation
 			$opo_result_context = new ResultContext($this->request, "ms_specimens", ResultContext::getLastFind($this->request, "ms_specimens"));
			# Is the item we're show details for in the result set?
 			$this->view->setVar('is_in_result_list', ($opo_result_context->getIndexInResultList($this->opn_item_id) != '?'));
 					
 			$this->view->setVar('next_id', $opo_result_context->getNextID($this->opn_item_id));
 			$this->view->setVar('previous_id', $opo_result_context->getPreviousID($this->opn_item_id));
 			$this->view->setVar('result_context', $opo_result_context);
 			
 		}
 		# -------------------------------------------------------
 		public function show() {
 		 	$va_bib_citations = array();
 			if($this->opn_item_id){
 				$this->opo_item->recordView($this->request->getUserID());
 				$o_db = new Db();
 				$q_bib = $o_db->query("SELECT b.*, sxb.link_id, sxb.pp FROM ms_specimens_x_bibliography sxb INNER JOIN ms_bibliography as b on sxb.bibref_id = b.bibref_id WHERE sxb.specimen_id = ?", $this->opn_item_id);
 				$t_bibliography = new ms_bibliography;
 				if($q_bib->numRows()){
 					while($q_bib->nextRow()){
 						$va_bib_citations[$q_bib->get("link_id")] = array("citation" => $t_bibliography->getCitationText($q_bib->getRow()), "link_id" => $q_bib->get("link_id"), "page" => $q_bib->get("pp"), "bibref_id" => $q_bib->get("bibref_id"));
 					}
 				}
				# --- can user edit record?
				$vb_show_edit_link = false;
				$t_project = new ms_projects();
				if($this->request->isLoggedIn() && $t_project->isFullAccessMember($this->request->user->get("user_id"), $this->opo_item->get("project_id"))){
					$vb_show_edit_link = true;
				}
 			}
 			$this->view->setVar("show_edit_link", $vb_show_edit_link);
 			$this->view->setVar("bib_citations", $va_bib_citations);
 			$this->render('ms_specimens_detail_html.php');
 		}
 		# -------------------------------------------------------
		/**
		 * Download media
		 */ 
/*		public function DownloadMedia() {
			if (!$this->request->isLoggedIn() || !$this->opo_item->userCanDownloadMedia($this->request->getUserID())) {
				$this->notification->addNotification("You may not download this media", __NOTIFICATION_TYPE_ERROR__);
				$this->show();
				return;
			}
			$ps_version = "_archive_";
			
			$va_versions = $this->opo_item->getMediaVersions('media');
			
			if (!in_array($ps_version, $va_versions)) { $ps_version = 'original'; }
			if (!in_array($ps_version, $va_versions)) { $ps_version = $va_versions[0]; }
			
			$this->view->setVar('version', $ps_version);
			
			$va_version_info = $this->opo_item->getMediaInfo('media', $ps_version);
			$this->view->setVar('version_info', $va_version_info);

			$va_info = $this->opo_item->getMediaInfo('media');
			$vs_idno_proc = $this->opo_item->get('specimen_id');
			if ($va_version_info['ORIGINAL_FILENAME']) {
				$this->view->setVar('version_download_name', $va_version_info['ORIGINAL_FILENAME'].'.'.$va_version_info['EXTENSION']);					
			} else {
				$this->view->setVar('version_download_name', 'morphosourceM'.$vs_idno_proc.'.'.$va_version_info['EXTENSION']);
			}
			$this->view->setVar('version_path', $this->opo_item->getMediaPath('media', $ps_version));
			
			$vn_rc = $this->render('media_download_binary.php');
			
			$this->response->sendContent();
			return $vn_rc;
		}*/
		# -------------------------------------------------------
		/**
		 * Request access to media
		 */ 
/*		public function RequestDownload() {
			if (!$this->request->isLoggedIn()) {
				$this->notification->addNotification("You must login to request download of this media", __NOTIFICATION_TYPE_ERROR__);
				$this->show();
				return;
			}
			if ($this->opo_item->userCanDownloadMedia($this->request->getUserID())) {
				$this->DownloadMedia();
				return;
			}
			
			// record request
			if (!$this->opo_item->requestDownload($this->request->getUserID(), $this->request->getParameter('request', pString), null, array('request' => $this->request))) {
				$this->notification->addNotification("Could not save media request. Try again later.", __NOTIFICATION_TYPE_ERROR__);
			} else {
				$this->notification->addNotification("Sent your request to the author.", __NOTIFICATION_TYPE_INFO__);
			}
			$this->Show();
		}
 		# -------------------------------------------------------
 		public function mediaViewer() {
 			$pn_specimen_id = $this->request->getParameter('specimen_id', pInteger);
 			// TODO: does user own this media?
 			$t_media = new ms_media($pn_specimen_id);
 			$t_media_files = new ms_media_files();
 			$pn_media_file_id = $this->request->getParameter('media_file_id', pInteger);
 			$t_media_files->load($pn_media_file_id);
 			$this->view->setVar('t_media_file', $t_media_files);
 			$this->view->setVar('media_file_id', $pn_media_file_id);
 			$this->view->setVar('t_media', $t_media);
 			$this->render('../MyProjects/Media/ajax_media_viewer_html.php');
 		}*/
 		# -------------------------------------------------------
 	}
 ?>