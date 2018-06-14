<?php
/* ----------------------------------------------------------------------
 * controllers/SplashController.php
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2011 Whirl-i-Gig
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
 	require_once(__CA_APP_DIR__.'/helpers/accessHelpers.php');
 	require_once(__CA_MODELS_DIR__."/ms_projects.php");
 	require_once(__CA_MODELS_DIR__."/ms_media.php");
 
 	class SplashController extends ActionController {
 		# -------------------------------------------------------
 		 
 		
 		protected $ops_find_type = 'basic_browse';
 		# -------------------------------------------------------
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 			parent::__construct($po_request, $po_response, $pa_view_paths);
 			
 			// redirect user if not logged in
			if (($this->request->config->get('pawtucket_requires_login')&&!($this->request->isLoggedIn()))||($this->request->config->get('show_bristol_only')&&!($this->request->isLoggedIn()))) {
                $this->response->setRedirect(caNavUrl($this->request, "", "LoginReg", "form"));
            } elseif (($this->request->config->get('show_bristol_only'))&&($this->request->isLoggedIn())) {
            	$this->response->setRedirect(caNavUrl($this->request, "bristol", "Show", "Index"));
            }
 		}
 		# -------------------------------------------------------
 		function Index($pa_options=null) {
 			// Remove any browse criteria previously set
			JavascriptLoadManager::register('imageScroller');
 			JavascriptLoadManager::register('browsable');
 			JavascriptLoadManager::register('tabUI');
 			JavascriptLoadManager::register('cycle');
 			# --- get a recent media image to display
 			$vn_recent = "";
 			$vs_recent_media = "";
 			$o_db = new Db();
 			$t_media = new ms_media();
 			#$q_recent_media = $o_db->query("SELECT m.media_id, m.project_id, m.published_on from ms_media m INNER JOIN ms_projects AS p ON m.project_id = p.project_id WHERE m.published > 0 AND p.deleted = 0 GROUP BY m.project_id ORDER BY m.published_on DESC LIMIT 10");
			
 			# --- featured projects
 			$va_featured_projects = array(
 				"394" => array(
 					"media" => array(
 						15845 => ($vs_pre = $t_media->getPreviewMediaFile(
 								15845, 
 								array("preview190"), 
 								true)["media"]["preview190"]
 							) ? $vs_pre : null,
 						15847 => ($vs_pre = $t_media->getPreviewMediaFile(
 								15847, 
 								array("preview190"), 
 								true)["media"]["preview190"]
 							) ? $vs_pre : null,
 						15848 => ($vs_pre = $t_media->getPreviewMediaFile(
 								15848, 
 								array("preview190"), 
 								true)["media"]["preview190"]
 							) ? $vs_pre : null,
 						15849 => ($vs_pre = $t_media->getPreviewMediaFile(
 								15849, 
 								array("preview190"), 
 								true)["media"]["preview190"]
 							) ? $vs_pre : null,
 					),
 					"title" => "Frogs in amber from the Cretaceous of Myanmar</br>​</br>",
 					"links" => array(
 						"<b>".caNavLink($this->request, _t("See all project specimens"), '', 'Detail', 'ProjectDetail', 'Show', array('project_id' => 394))."</b>",
 						"<b><a href='https://www.nature.com/articles/s41598-018-26848-w'>Read the published article</a></b>"
 					)
 				),
 				"206" => array(
 					"media" => array(
 						8412 => ($vs_pre = $t_media->getPreviewMediaFile(
 								8412, 
 								array("preview190"), 
 								true)["media"]["preview190"]
 							) ? $vs_pre : null,
 						8539 => ($vs_pre = $t_media->getPreviewMediaFile(
 								8539, 
 								array("preview190"), 
 								true)["media"]["preview190"]
 							) ? $vs_pre : null,
 						8470 => ($vs_pre = $t_media->getPreviewMediaFile(
 								8470, 
 								array("preview190"), 
 								true)["media"]["preview190"]
 							) ? $vs_pre : null,
 						8388 => ($vs_pre = $t_media->getPreviewMediaFile(
 								8388, 
 								array("preview190"), 
 								true)["media"]["preview190"]
 							) ? $vs_pre : null,
 					),
 					"title" => "<b>The Arene Candide 3D database - Upper Paleolithic funerary behavior in Liguria (Italy)</b></br>",
 					"links" => array(
 						"<b>".caNavLink($this->request, _t("See all project specimens"), '', 'Detail', 'ProjectDetail', 'Show', array('project_id' => 206))."</b>",
 						"<b><a href='http://www.isita-org.com/jass/Contents/ContentsVol96.htm'>Read the published article</a></b>"
 					)
 				)
 			);

 			foreach ($va_featured_projects as $vn_proj_id => $vn_proj) {
 				$va_featured_projects[$vn_proj_id]['media'] = 
 					array_filter($va_featured_projects[$vn_proj_id]['media']);
 			}

 			# --- get media for featured projects
 			// COMMENTED OUT BUT THIS CODE WORKS FOR GETTING RANDOM MEDIA FROM FEATURED PROJECTS
			// foreach($va_featured_projects as $vn_project_id => $va_info){
			// 	$q_recent_media = $o_db->query("SELECT m.media_id, m.project_id, m.published_on from ms_media m INNER JOIN ms_projects AS p ON m.project_id = p.project_id WHERE m.published > 0 AND p.project_id = ? ORDER BY RAND() LIMIT 10", $vn_project_id);
			// 	$va_recent_media = array();
			// 	if($q_recent_media->numRows()){
			// 		$i = 0;
			// 		while($q_recent_media->nextRow()){
			// 			$va_preview_media = $t_media->getPreviewMediaFile($q_recent_media->get("media_id"), array("preview190"), true);
			// 			if($va_preview_media["media"]["preview190"]){
			// 				$va_recent_media[$q_recent_media->get("media_id")] = $va_preview_media["media"]["preview190"];
			// 				$i++;
			// 			}
			// 			if($i == 4){
			// 				break;
			// 			}
			// 		}
			// 	}
			// 	$va_featured_projects[$vn_project_id]["media"] = $va_recent_media;
			// }

			$this->view->setVar("featured_projects", $va_featured_projects);
 			
//  			if($vn_recent = $this->request->config->get("recently_published_media_id")){
//  				$t_media->load($vn_recent);
//  				$t_media->getMediaTag("media", "preview190");
//  				$va_preview_media = $t_media->getPreviewMediaFile(null, array("preview190"));
// 				$vs_recent_media = $va_preview_media["media"]["preview190"];
//  			}else{
// 				$q_recent_media = $o_db->query("SELECT media_id, media from ms_media WHERE published = 1 ORDER BY published_on DESC LIMIT 1");
// 				if($q_recent_media->numRows()){
// 					$q_recent_media->nextRow();
// 					$vn_recent = $q_recent_media->get("media_id");
// 					$va_preview_media = $t_media->getPreviewMediaFile($vn_recent, array("preview190"));
// 					$vs_recent_media = $va_preview_media["media"]["preview190"];
// 				}
// 			}
//  			$this->view->setVar("recent_media", $vs_recent_media);
//  			$this->view->setVar("recent_media_id", $vn_recent);
//  			$vn_random = "";
//  			$vs_random_media = "";
//  			if($vn_random = $this->request->config->get("random_media_media_id")){
//  				$t_media->load($vn_random);
//  				$vs_random_media = $t_media->getMediaTag("media", "preview190");
//  			}else{
// 				$q_random_media = $o_db->query("SELECT media_id, media from ms_media WHERE published = 1 ORDER BY RAND() DESC LIMIT 1");
// 				if($q_random_media->numRows()){
// 					$q_random_media->nextRow();
// 					$vn_random = $q_random_media->get("media_id");
// 					$vs_random_media = $q_random_media->getMediaTag("media", "preview190");
// 				}
// 			}
//  			$this->view->setVar("random_media", $vs_random_media);
//  			$this->view->setVar("random_media_id", $vn_random);
 			$this->render('Splash/splash_html.php');
 		}
 		# -------------------------------------------------------
 	}
 ?>