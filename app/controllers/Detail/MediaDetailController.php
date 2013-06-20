<?php
/* ----------------------------------------------------------------------
 * controllers/MyProjects/MediaDetailController.php
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

 		# -------------------------------------------------------
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 			parent::__construct($po_request, $po_response, $pa_view_paths);
 			JavascriptLoadManager::register("panel");
 			JavascriptLoadManager::register("3dmodels");
 			
 			# --- load the media object
			$this->opo_item = new ms_media();
			$this->opn_item_id = $this->request->getParameter('media_id', pInteger);
			if($this->opn_item_id){
				$this->opo_item->load($this->opn_item_id);
			}
			if(!$this->opo_item->get("media_id")){
				$this->notification->addNotification("Invalid media_id", __NOTIFICATION_TYPE_ERROR__);
				$this->response->setRedirect(caNavUrl($this->request, "", "", ""));
			}
// 			if (!$this->opo_item->get("published") == 1) {
// 				$this->notification->addNotification("Item is not published", __NOTIFICATION_TYPE_ERROR__);
// 				$this->response->setRedirect(caNavUrl($this->request, "", "", ""));
// 			}
			$this->view->setvar("item_id", $this->opn_item_id);
			$this->view->setvar("media_id", $this->opn_item_id);
			$this->view->setvar("item", $this->opo_item);
 		}
 		# -------------------------------------------------------
 		public function show() {
 		 	$va_bib_citations = array();
 			if($this->opn_item_id){
 				$o_db = new Db();
 				$q_bib = $o_db->query("SELECT b.*, mxb.link_id, mxb.pp FROM ms_media_x_bibliography mxb INNER JOIN ms_bibliography as b on mxb.bibref_id = b.bibref_id WHERE mxb.media_id = ?", $this->opn_item_id);
 				$t_bibliography = new ms_bibliography;
 				if($q_bib->numRows()){
 					while($q_bib->nextRow()){
 						$va_bib_citations[$q_bib->get("link_id")] = array("citation" => $t_bibliography->getCitationText($q_bib->getRow()), "link_id" => $q_bib->get("link_id"), "page" => $q_bib->get("pp"), "bibref_id" => $q_bib->get("bibref_id"));
 					}
 				}
 			}
 			$this->view->setVar("bib_citations", $va_bib_citations);
 			$this->render('ms_media_detail_html.php');
 		}
 		# -------------------------------------------------------
		/**
		 * Download media
		 */ 
		public function DownloadMedia() {
			$ps_version = "_archive_";
			
			$va_versions = $this->opo_item->getMediaVersions('media');
			
			if (!in_array($ps_version, $va_versions)) { $ps_version = 'original'; }
			if (!in_array($ps_version, $va_versions)) { $ps_version = $va_versions[0]; }
			
			$this->view->setVar('version', $ps_version);
			
			$va_version_info = $this->opo_item->getMediaInfo('media', $ps_version);
			$this->view->setVar('version_info', $va_version_info);

			$va_info = $this->opo_item->getMediaInfo('media');
			$vs_idno_proc = $this->opo_item->get('media_id');
			if ($va_version_info['ORIGINAL_FILENAME']) {
				$this->view->setVar('version_download_name', $va_version_info['ORIGINAL_FILENAME'].'.'.$va_version_info['EXTENSION']);					
			} else {
				$this->view->setVar('version_download_name', 'morphosourceM'.$vs_idno_proc.'.'.$va_version_info['EXTENSION']);
			}
			$this->view->setVar('version_path', $this->opo_item->getMediaPath('media', $ps_version));
			
			$vn_rc = $this->render('media_download_binary.php');
			
			$this->response->sendContent();
			return $vn_rc;
		}
 		# -------------------------------------------------------
 		public function mediaViewer() {
 			$pn_media_id = $this->request->getParameter('media_id', pInteger);
 			// TODO: does user own this media?
 			$t_media = new ms_media($pn_media_id);
 			$this->view->setVar('t_media', $t_media);
 			$this->render('../MyProjects/Media/ajax_media_viewer_html.php');
 		}
 		# -------------------------------------------------------
 	}
 ?>