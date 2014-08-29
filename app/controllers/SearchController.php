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
	}
 ?>
