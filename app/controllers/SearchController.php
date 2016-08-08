<?php
/* ----------------------------------------------------------------------
 * pawtucket2/app/controllers/SearchController.php : controller for object search request handling - processes searches from top search bar
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2008-2013 Whirl-i-Gig
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
 	require_once(__CA_LIB_DIR__."/ca/BaseSearchController.php");
 	require_once(__CA_LIB_DIR__."/ca/Browse/ObjectBrowse.php");
 	require_once(__CA_LIB_DIR__."/ca/Browse/MediaBrowse.php");
	require_once(__CA_LIB_DIR__."/ca/Search/DidYouMean.php");
	require_once(__CA_LIB_DIR__."/core/Datamodel.php");
 	require_once(__CA_LIB_DIR__."/ca/Search/MediaSearch.php");
 	require_once(__CA_LIB_DIR__."/ca/Search/SpecimenSearch.php");
 	require_once(__CA_LIB_DIR__.'/core/GeographicMap.php');
 	require_once(__CA_MODELS_DIR__.'/ms_projects.php');
 	require_once(__CA_APP_DIR__.'/helpers/morphoSourceHelpers.php');
 	require_once(__CA_LIB_DIR__.'/core/Parsers/ZipStream.php');
	
 	class SearchController extends BaseSearchController {
 		# -------------------------------------------------------
 		/**
 		 * Name of subject table (ex. for an object search this is 'ms_media')
 		 */
 		protected $ops_tablename = null;
 		
 		/** 
 		 * Number of items per search results page
 		 */
 		protected $opa_items_per_page = array(12, 24, 36);
 		
 		/** 
 		 * Default number of items per search results page
 		 */
 		protected $opn_items_per_page_default = 12;
 		 
 		/** 
 		 * Number of items per secondary search results page
 		 */
 		protected $opa_items_per_secondary_search_page = 8;
 		 
 		/**
 		 * List of search-result views supported for this find
 		 * Is associative array: keys are view labels, values are view specifier to be incorporated into view name
 		 */ 
 		protected $opa_views;
 		
 		/**
 		 * List of search-result view options
 		 * Is associative array: keys are view labels, arrays for each view contain description and icon graphic name for use in view
 		 */ 
 		protected $opa_views_options;
 		 
 		 
 		/**
 		 * List of available search-result sorting fields
 		 * Is associative array: values are display names for fields, keys are full fields names (table.field) to be used as sort
 		 */
 		protected $opa_sorts;
 		
 		protected $ops_find_type = 'basic_search';
 		# -------------------------------------------------------
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 			parent::__construct($po_request, $po_response, $pa_view_paths);
 			JavascriptLoadManager::register('tabUI');
 			
 			// redirect user if not logged in
			if (($this->request->config->get('pawtucket_requires_login')&&!($this->request->isLoggedIn()))||($this->request->config->get('show_bristol_only')&&!($this->request->isLoggedIn()))) {
                $this->response->setRedirect(caNavUrl($this->request, "", "LoginReg", "form"));
            } elseif (($this->request->config->get('show_bristol_only'))&&($this->request->isLoggedIn())) {
            	$this->response->setRedirect(caNavUrl($this->request, "bristol", "Show", "Index"));
            }	
            
 			// get configured items per page options, if specified
 			if ($va_items_per_page_for_ms_media = $po_request->config->getList('items_per_page_options_for_ms_media_search')) {
 				$this->opa_items_per_page = $va_items_per_page_for_ms_media;
 			}
 			if ($vn_items_per_secondary_search_page = $po_request->config->get('items_per_secondary_search_page')) {
 				$this->opa_items_per_secondary_search_page = $vn_items_per_secondary_search_page;
 			}
 			
 			if (!($vs_search_target = $po_request->getParameter('target', pString))) {
 				$vs_search_target = $po_request->session->getVar('pawtucket2_search_target');
 			}
 			
 			//
 			// Minimal view list (all targets have a "full" results view)
 			//
 			$this->opa_views = array(
				'full' => _t('List')
			);
			$this->opa_views_options = array(
				'full' => array("description" => _t("View results in a list"), "icon" => "icon_list.gif")
			);
 			
 			switch($vs_search_target) {
 				default:
 					$this->ops_tablename = 'ms_media';
 					$this->opo_result_context = new ResultContext($po_request, $this->ops_tablename, $this->ops_find_type);
 					$this->opo_browse = new MediaBrowse($this->opo_result_context->getParameter('browse_id', true), 'pawtucket2');	
 					
 					// get configured result views, if specified
					if ($va_result_views_for_ms_media = $po_request->config->getAssoc('result_views_for_ms_media')) {
						$this->opa_views = $va_result_views_for_ms_media;
					}else{
						$this->opa_views = array(
							'thumbnail' => _t('Thumbnails'),
							'full' => _t('List')
						 );
					}
					// get configured result sort options, if specified
					if ($va_sort_options_for_ms_media = $po_request->config->getAssoc('result_sort_options_for_ms_media')) {
						$this->opa_sorts = $va_sort_options_for_ms_media;
					}else{
						$this->opa_sorts = array(
							'ms_media.media_id' => _t('ID')
						);
					}
					
					if (is_array($va_view_opts = $po_request->config->get("result_views_options_for_ms_media"))) {
						$this->opa_views_options = array_merge($this->opa_views_options, $va_view_opts);
					}
 					break;
 			}
 			
 			// if target changes we need clear out all browse criteria as they are no longer valid
 			if ($vs_search_target != $po_request->session->getVar('pawtucket2_search_target')) {
				$this->opo_browse->removeAllCriteria();
			}
			
			
			// Set up target vars and controls
 			$po_request->session->setVar('pawtucket2_search_target', $vs_search_target);
 			
 			// set current result view options so can check we are including a configured result view
 			$this->view->setVar('result_views', $this->opa_views);
 			
 			// get configured items per page options, if specified
 			if ($va_items_per_page_for = $po_request->config->getList('items_per_page_options_for_'.$this->ops_tablename.'_search')) {
 				$this->opa_items_per_page = $va_items_per_page_for;
 			}
 			if (($vn_items_per_page_default = (int)$po_request->config->get('items_per_page_default_for_'.$this->ops_tablename.'_search')) > 0) {
				$this->opn_items_per_page_default = $vn_items_per_page_default;
			} else {
				$this->opn_items_per_page_default = $this->opa_items_per_page[0];
			}
			
			// secondary search settings
 			if ($vn_items_per_secondary_search_page = $po_request->config->get('items_per_secondary_search_page')) {
 				$this->opa_items_per_secondary_search_page = $vn_items_per_secondary_search_page;
 			}
 			
 			
 			// set current result view options so can check we are including a configured result view
 			$this->view->setVar('result_views', $this->opa_views);
 			$this->view->setVar('result_views_options', $this->opa_views_options);
 			
 			if ($this->opn_type_restriction_id = $this->opo_result_context->getTypeRestriction($pb_type_restriction_has_changed)) {
 				$_GET['type_id'] = $this->opn_type_restriction_id;								// push type_id into globals so breadcrumb trail can pick it up
 				$this->opb_type_restriction_has_changed =  $pb_type_restriction_has_changed;	// get change status
 			}
 		}
 		# -------------------------------------------------------
 		/**
 		 * Search handler (returns search form and results, if any)
 		 * Most logic is contained in the BaseSearchController->Search() method; all you usually
 		 * need to do here is instantiate a new subject-appropriate subclass of BaseSearch 
 		 * (eg. ObjectSearch for objects, EntitySearch for entities) and pass it to BaseSearchController->Search() 
 		 */ 
 		public function Index($pa_options=null) {
 			$ps_search = $this->opo_result_context->getSearchExpression();
 			$va_access_values = caGetUserAccessValues($this->request);
 			
 			if ($this->request->config->get('do_secondary_searches')) {
				if ($this->request->config->get('do_secondary_search_for_ms_specimens')) {
					$o_search = new SpecimenSearch();
					$qr_res = $o_search->search($ps_search, array('no_cache' => true, 'checkAccess' => $va_access_values));
					$qr_res_without_medialess_specimens = $this->_setResultContextForSecondarySearch('ms_specimens', $ps_search, $qr_res);
					
					// We want to use the  result set that has been filtered to omit specimens that don't have at least one
					// published media item
					$this->view->setVar('secondary_search_ms_specimens', $qr_res_without_medialess_specimens ? $qr_res_without_medialess_specimens : $qr_res);
				}
			}
 			$this->view->setVar('secondaryItemsPerPage', $this->opa_items_per_secondary_search_page);
 			
			$pa_options['search'] = new MediaSearch(); //$this->opo_browse;
 			return parent::Index($pa_options);
 		}
 		# -------------------------------------------------------
 		/**
 		 *
 		 */
 		public function secondarySearch() {
 			$pn_spage = (int)$this->request->getParameter('spage', pInteger);
 			$ps_type = $this->request->getParameter('type', pString);
 			$this->view->setVar('search_type', $ps_type);
 			$va_access_values = caGetUserAccessValues($this->request);
 			
 			$ps_search = $this->opo_result_context->getSearchExpression();
 			switch($ps_type) {
				case 'ms_specimens':
					$o_search = new SpecimenSearch();
					$qr_res = $o_search->search($ps_search, array('checkAccess' => $va_access_values));
					break;
				default:
					$this->response->setRedirect($this->request->config->get('error_display_url').'/n/'._t('Invalid secondary search type').'?r='.urlencode($this->request->getFullUrlPath()));
					return;
					break;
			}
			
 			$this->view->setVar('secondaryItemsPerPage', $this->opa_items_per_secondary_search_page);
 			$this->view->setVar('page_'.$ps_type, $pn_spage);
 			
 			if ($pn_spage > 0) {
 				$qr_res->seek($pn_spage * $this->opa_items_per_secondary_search_page);
 			}
			$this->view->setVar('secondary_search_'.$ps_type, $qr_res);
 			
 			$this->render('Results/search_secondary_results/'.$ps_type.'_html.php');
 		}
 		# -------------------------------------------------------
 		private function _setResultContextForSecondarySearch($ps_table_name, $ps_expression, $po_result) {
 			$opo_result_context = new ResultContext($this->request, $ps_table_name, 'basic_search');
 			$opo_result_context->setSearchExpression($ps_expression);
 			
			$t_model = $this->opo_datamodel->getInstanceByTableName($ps_table_name, true);
 			$vs_pk = $t_model->primaryKey();
 			
 			$po_result->seek(0);
 			
 			$va_found_item_ids = array();
 			
 			$t_project = new ms_projects();
 			$va_projects = caExtractArrayValuesFromArrayOfArrays($t_project->getProjectsForMember($this->request->getUserID()), 'project_id');
 			
 			$vb_is_logged_in = (bool)$this->request->isLoggedIn();
 			
 			while($po_result->nextHit()) {
 				// Only show items that have at least one published media item related
 				// unles the item is in the logged in users' projects
 				$va_is_published = $po_result->get('ms_media.published', array('returnAsArray' => true));
 				if(is_array($va_is_published) && sizeof($va_is_published)) {
 					foreach($va_is_published as $vn_published) {
 						if (($vn_published > 0) || (($vn_published == 0) && $vb_is_logged_in && in_array($po_result->get($ps_table_name.'.project_id'), $va_projects))) {
 							$va_found_item_ids[] = $po_result->get($ps_table_name.'.'.$vs_pk);
 							break;
 						}
 					}
 				}
 			}
 			
			$opo_result_context->setResultList($va_found_item_ids);
			$opo_result_context->setAsLastFind();
			$opo_result_context->saveContext();
			
			$po_result = caMakeSearchResult($ps_table_name, $va_found_item_ids);
			
			// return modified search result
			return $po_result;
		}
 		# -------------------------------------------------------
 		# "Searchlight" autocompleting search
 		# -------------------------------------------------------
 		public function lookup() {
 			$vs_search = $this->request->getParameter('q', pString);
 			
 			$t_list = new ca_lists();
 			$va_data = array();
 			
 			$va_access_values = caGetUserAccessValues($this->request);
 			
 			#
 			# Do "quicksearches" on so-configured tables
 			#
 			if ($this->request->config->get('quicksearch_return_ms_media')) {
				$va_results = caExtractValuesByUserLocale(SearchEngine::quickSearch($vs_search, 'ms_media', 57, array('limit' => 3, 'checkAccess' => $va_access_values)));
				// break found objects out by type
				foreach($va_results as $vn_id => $va_match_info) {
					$vs_type = unicode_ucfirst($t_list->getItemFromListForDisplayByItemID('object_types', $va_match_info['type_id'], true));
					$va_data['ms_media'][$vs_type][$vn_id] = $va_match_info;
				}
			}
			
 			$this->view->setVar('matches', $va_data);
 			$this->render('Search/ajax_search_lookup_json.php');
 		}
 		# -------------------------------------------------------
		public function searchName($ps_mode='singular') {
 			return ($ps_mode == 'singular') ? _t('search') : _t('searches');
 		}
		# -------------------------------------------------------
		public function exportMediaReport(){
			$va_media_ids = $this->opo_result_context->getResultList();
			if(sizeof($va_media_ids)){
				# - generate report
				# - should include
// Specimen number
// media #
// DOI or current URL on MorphoSource
// description
// project membership
// publication status
// copyright holder
// copyright status
// resolution
// facility
// number of views
// view diversity
// number of downloads
// download diversity
// downloads for research
// downloads for college education
// downloads for k-12 education
// downloads for 'other' purposes
				
			$o_db = new Db();
			if(!$t_specimen){
				$t_specimen = new ms_specimens();
			}
			$t_media = new ms_media();
			$t_media_file = new ms_media_files();
			$q_media_files = $o_db->query("
				SELECT mf.media_file_id, mf.title file_title, mf.notes file_notes, mf.side file_side, mf.element file_element, mf.media file_media, mf.doi, mf.file_type, mf.distance_units, mf.max_distance_x, mf.max_distance_3d, mf.published file_pub, m.*, f.name facility, i.name institution, p.name
				FROM ms_media m
				INNER JOIN ms_media_files as mf ON mf.media_id = m.media_id
				LEFT JOIN ms_specimens as s ON m.specimen_id = s.specimen_id
				LEFT JOIN ms_facilities as f ON f.facility_id = m.facility_id
				LEFT JOIN ms_institutions as i ON s.institution_id = i.institution_id
				LEFT JOIN ms_projects as p ON m.project_id = p.project_id
				WHERE m.media_id IN (".join(", ", $va_media_ids).")");
			$va_all_md = array();
			if($q_media_files->numRows()){
				$va_specimen_info = array();
				# --- header row
				$va_header = array(
									"specimen",
									"specimen taxonomy",
									"insitution",
									"media file number",
									"media file number",
									"project",
									"doi",
									"description/element",
									"copyright holder",
									"copyright license",
									"citation instruction statement (to be copy-pasted into acknolwedgements)",
									"publication status",
									"facility",
									"x res",
									"y res",
									"z res",
									"media views",
									"media file downloads",
									"media file download diversity",
									"downloads for research",
									"downloads for college education",
									"downloads for k-12 education",
									"downloads for 'other' purposes"
								);
				#$va_all_md[] = join(",", $va_header);
				#file_type, mf.distance_units, mf.max_distance_x, mf.max_distance_3d
				$va_all_md[] = $va_header;
				$t_download_stats = new ms_media_download_stats();
				while($q_media_files->nextRow()){
					$va_media_md = array();
					$vn_pub = "";
					if($q_media_files->get("published") !== null){
						$vn_pub = $q_media_files->get("file_pub");
					}else{
						$vn_pub = $q_media_files->get("published");
					}
					if($vn_pub > 0){
						$vs_specimen_taxonomy = $vs_specimen_name = "";
						if(!$va_specimen_info[$q_media_files->get("specimen_id")]){
							if($q_media_files->get("specimen_id")){
								$vs_specimen_name = $t_specimen->getSpecimenNumber($q_media_files->get("specimen_id"));
								$va_specimen_info[$q_media_files->get("specimen_id")]["specimen_name"] = $vs_specimen_name;
								$vs_specimen_taxonomy = join(", ", $t_specimen->getSpecimenTaxonomy($q_media_files->get("specimen_id")));
								$va_specimen_info[$q_media_files->get("specimen_id")]["specimen_taxonomy"] = join(", ", $t_specimen->getSpecimenTaxonomy($q_media_files->get("specimen_id")));
							}
						}else{
							$vs_specimen_name = $va_specimen_info[$q_media_files->get("specimen_id")]["specimen_name"];
							$vs_specimen_taxonomy = $va_specimen_info[$q_media_files->get("specimen_id")]["specimen_taxonomy"];
						}
						$va_versions = $q_media_files->getMediaVersions('file_media');
						$va_properties = $q_media_files->getMediaInfo('file_media', in_array('_archive_', $va_versions) ? '_archive_' : 'original');
					
					
						$va_media_md[] = $vs_specimen_name;
						$va_media_md[] = $vs_specimen_taxonomy;
						$va_media_md[] = $q_media_files->get("institution");
						$va_media_md[] = "M".$q_media_files->get("media_id");
						$va_media_md[] = "M".$q_media_files->get("media_id")."-".$q_media_files->get("media_file_id");
						$va_media_md[] = $q_media_files->get("name");
						$va_tmp = preg_split("![ ]*\|[ ]*!", $q_media_files->get('doi'));
						$va_media_md[] = trim($va_tmp[0]);
						if($q_media_files->get("file_element")){
							$va_media_md[] = preg_replace("/\r|\n/", " ", $q_media_files->get("file_element"));
						}else{
							$va_media_md[] = preg_replace("/\r|\n/", " ", $q_media_files->get("element"));
						}
						$va_media_md[] = $q_media_files->get("copyright_info");
						$va_media_md[] = $t_media->getChoiceListValue("copyright_license", $q_media_files->get("copyright_license"));
						if($q_media_files->get("media_citation_instruction1")){
							$va_media_md[] = "Citation: ".$t_media->getMediaCitationInstructionsFromFields(array("media_citation_instruction1" => $q_media_files->get("media_citation_instruction1"), "media_citation_instruction2" => $q_media_files->get("media_citation_instruction2"), "media_citation_instruction3" => $q_media_files->get("media_citation_instruction3")));
						}else{
							$va_media_md[] = "";
						}
						$va_media_md[] = $t_media->getChoiceListValue("published", $vn_pub);
						$va_media_md[] = $q_media_files->get("facility");
						$va_media_md[] = $q_media_files->get("scanner_x_resolution")." mm";
						$va_media_md[] = $q_media_files->get("scanner_y_resolution")." mm";
						$va_media_md[] = $q_media_files->get("scanner_z_resolution")." mm";
					
						# --- media views
						$q_media_views = $o_db->query("SELECT * from ms_media_view_stats where media_id = ?", $q_media_files->get("media_id"));
						$va_media_md[] = $q_media_views->numRows();
					
						# --- media file download
						$q_media_file_downloads = $o_db->query("SELECT download_id, user_id, intended_use, intended_use_other from ms_media_download_stats where media_file_id = ?", $q_media_files->get("media_file_id"));
						$va_media_md[] = $q_media_file_downloads->numRows();
						# --- download diversity and use
						$va_download_users = array();
						$va_download_use = array("research" => 0, "college" => 0, "k-12" => 0, "other" => 0);
						if($q_media_file_downloads->numRows()){
							while($q_media_file_downloads->nextRow()){
								if($q_media_file_downloads->get("user_id")){
									$va_download_users[$q_media_file_downloads->get("user_id")] = $q_media_file_downloads->get("user_id");
								}
								$t_download_stats->load($q_media_file_downloads->get("download_id"));
								$va_intended_use = $t_download_stats->get("intended_use");
								if(is_array($va_intended_use) and sizeof($va_intended_use)){
									foreach($va_intended_use as $vs_use){
										switch($vs_use){
											case "Research":
												$va_download_use["research"] = $va_download_use["research"] + 1;
											break;
											# -------------------------------------------
											case "School_K_6":
											case "School_7_12":
											case "Education_K_6":
											case "Education_7_12":
												$va_download_use["k-12"] = $va_download_use["k-12"] + 1;
											break;
											# -------------------------------------------
											case "School_College_Post_Secondary":
											case "School_Graduate_school":
											case "Education_College_Post_Secondary":
												$va_download_use["college"] = $va_download_use["college"] + 1;
											break;
											# ----------------------------------
											default:
												$va_download_use["other"] = $va_download_use["other"] + 1;
											break;
										}
									}
								}
							}
						}
						$va_media_md[] = sizeof($va_download_users);
						$va_media_md[] = $va_download_use["research"];
						$va_media_md[] = $va_download_use["college"];
						$va_media_md[] = $va_download_use["k-12"];
						$va_media_md[] = $va_download_use["other"];
					
						$va_all_md[] = $va_media_md;
					}
				}
					
				#return join($va_all_md, "\n")."\n\nThis text file is a selective, not an exhaustive distillation of the metadata available for your downloaded files. If you require more information, it may still be available within MorphoSource and you should seek it there before contacting the data author or making the assumption that it does not exist.\n\n";
				
				if(sizeof($va_all_md)){
						if (!($vn_limit = ini_get('max_execution_time'))) { $vn_limit = 30; }
						set_time_limit($vn_limit * 2);
						# --- generate text file for media in cart
						$vs_tmp_file_name = tempnam(caGetTempDirPath(), 'mediaDownloadTxt');
						$vs_text_file_name = "morphosourceMedia_".date('m_d_y_His');
						$vo_file = fopen($vs_tmp_file_name, "w");
						foreach($va_all_md as $va_row){
							fputcsv($vo_file, $va_row);			
						}
						fclose($vo_file);
						
						$o_zip = new ZipStream();
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
				$this->Index();
			}
		}
	}
 ?>
