<?php
/* ----------------------------------------------------------------------
 * app/controllers/api/v1/FindController.php
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2016 Whirl-i-Gig
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
 
	require_once(__CA_APP_DIR__."/helpers/apiHelpers.php");
	require_once(__CA_LIB_DIR__."/ms/BaseServiceController.php");
	
	require_once(__CA_LIB_DIR__."/ca/Search/SpecimenSearch.php");
	require_once(__CA_LIB_DIR__."/ca/Search/TaxonomyNameSearch.php");
	require_once(__CA_LIB_DIR__."/ca/Search/FacilitySearch.php");
	require_once(__CA_LIB_DIR__."/ca/Search/MediaSearch.php");
	require_once(__CA_LIB_DIR__."/ca/Search/ProjectSearch.php");

	require __CA_BASE_DIR__ . '/vendor/autoload.php';
	
 
 	class FindController extends BaseServiceController {
 		# -------------------------------------------------------
 		/**
 		 *
 		 */
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 			parent::__construct($po_request, $po_response, $pa_view_paths);
 			$o_app = AppController::getInstance($po_request, $po_response);
 			$o_app->removeAllPlugins();
 		}
 		# -------------------------------------------------------
 		/**
 		 *
 		 */
 		public function __call($ps_function, $pa_args) {
 			$o_dm = Datamodel::load();
 			
 			$ps_function = strtolower($ps_function);
 			
 			$ps_rewritten_q = msRewriteAPIQuery($ps_q = $this->request->getParameter('q', pString));
 			$pa_sort = explode(";", $this->request->getParameter('sort', pString));
 			$pn_limit = $this->request->getParameter('limit', pInteger);
 			if ($pn_limit < 1) { $pn_limit = 25; }
 			if ($pn_limit > 25) { $pn_limit = 25; }
 			$pn_start = $this->request->getParameter('start', pInteger);
 			if ($pn_start < 1) { $pn_start = 0; }
 			
 			
 			$ps_naming = strtolower($this->request->getParameter('naming', pString));
 			if (!in_array($ps_naming, ['morphosource', 'darwincore'])) { $ps_naming = 'morphosource'; }
 			
 			
 			if (is_array($pa_sort) && sizeof($pa_sort)) {
 				$ps_sort = join(";", msDeflectFieldNames($pa_sort));
 			}
 	
 			try {		
				switch($ps_function) {
					default:
					case 'specimens':
						$o_search = new SpecimenSearch();
					
						$va_fields = msInflectFieldNames([
							"ms_specimens.specimen_id","ms_specimens.notes",
							"ms_specimens.reference_source","ms_specimens.institution_code","ms_specimens.collection_code","ms_specimens.catalog_number",
							"ms_specimens.created_on","ms_specimens.last_modified_on","ms_specimens.sex","ms_specimens.element","ms_specimens.side",
							"ms_specimens.relative_age","ms_specimens.absolute_age","ms_specimens.body_mass","ms_specimens.body_mass_comments",
							"ms_specimens.locality_description","ms_specimens.locality_coordinates",
							"ms_specimens.locality_absolute_age","ms_specimens.locality_relative_age",
							"ms_specimens.locality_relative_age_bibref_id","ms_specimens.approval_status","ms_specimens.institution_id",
							"ms_specimens.description","ms_specimens.collector","ms_specimens.collected_on","ms_specimens.locality_northing_coordinate",
							"ms_specimens.locality_easting_coordinate","ms_specimens.locality_datum_zone","ms_specimens.url",
							"ms_institutions.name",
							"ms_projects.project_id", "ms_projects.name",
							"ca_users.email"
						]);
						if (!$ps_sort) { $ps_sort = "ms_specimens.specimen_id"; }
						break;
					case 'taxonomy':
						$o_search = new TaxonomyNameSearch();
					
						$va_fields = msInflectFieldNames([
							"ms_taxonomy_names.taxon_id","ms_taxonomy_names.notes","ms_taxonomy_names.species","ms_taxonomy_names.subspecies",
							"ms_taxonomy_names.variety","ms_taxonomy_names.author","ms_taxonomy_names.year","ms_taxonomy_names.ht_supraspecific_clade",
							"ms_taxonomy_names.ht_kingdom","ms_taxonomy_names.ht_phylum","ms_taxonomy_names.ht_class","ms_taxonomy_names.ht_subclass",
							"ms_taxonomy_names.ht_order","ms_taxonomy_names.ht_suborder","ms_taxonomy_names.ht_superfamily","ms_taxonomy_names.ht_family",
							"ms_taxonomy_names.ht_subfamily","ms_taxonomy_names.created_on","ms_taxonomy_names.last_modified_on",
							"ms_taxonomy_names.justification","ms_taxonomy_names.review_status","ms_taxonomy_names.review_notes",
							"ms_taxonomy_names.is_primary","ms_taxonomy_names.genus","ms_taxonomy_names.ht_superorder",
						
							"ms_taxonomy.notes", "ms_taxonomy.is_extinct"
						]);
					
						break;
					case 'projects':
						$o_search = new ProjectSearch();
						$o_search->addResultFilter("ms_projects.publication_status", "=", 1);
				
						$va_fields = msInflectFieldNames([
							"ms_projects.project_id","ms_projects.name","ms_projects.abstract","ms_projects.published_on",
							"ms_projects.created_on","ms_projects.last_modified_on",
							"ms_projects.approval_status","ms_projects.url",
							"ca_users.email"
						]);
						if (!$ps_sort) { $ps_sort = "ms_projects.project_id"; }
						break;
					case 'media':
						$o_search = new MediaSearch();
						$o_search->addResultFilter("ms_media.published", "=", 1);
					
						$va_fields = msInflectFieldNames([
							"ms_media.media_id",
							"ms_media.notes","ms_media.is_copyrighted","ms_media.copyright_info","ms_media.copyright_permission",
							"ms_media.copyright_license","ms_media.created_on","ms_media.last_modified_on","ms_media.approval_status",
							"ms_media.scanner_x_resolution","ms_media.scanner_y_resolution","ms_media.scanner_z_resolution","ms_media.scanner_voltage",
							"ms_media.scanner_amperage","ms_media.scanner_watts","ms_media.scanner_exposure_time","ms_media.scanner_filter","ms_media.scanner_projections","ms_media.scanner_frame_averaging",
							"ms_media.scanner_acquisition_time","ms_media.scanner_wedge","ms_media.scanner_calibration_description",
							"ms_media.scanner_technicians","ms_media.published_on","ms_media.element","ms_media.title",
							"ms_media.side","ms_media.grant_support","ms_media.media_citation_instructions",
							"ms_media.scanner_calibration_shading_correction","ms_media.scanner_calibration_flux_normalization",
							"ms_media.scanner_calibration_geometric_calibration","ms_media.media_citation_instruction1","ms_media.media_citation_instruction2",
							"ms_media.media_citation_instruction3",
							"ms_scanners.scanner_id", "ms_scanners.name",
							"ms_specimens.specimen_id", "ms_specimens.institution_code","ms_specimens.collection_code","ms_specimens.catalog_number",
							"ms_facilities.facility_id", "ms_facilities.name",
							"ms_projects.project_id", "ms_projects.name",
							"ca_users.email"
						]);
						if (!$ps_sort) { $ps_sort = "ms_media.media_id"; }
						break;
					case 'facilities':
						$o_search = new FacilitySearch();
					
						$va_fields = msInflectFieldNames([
							"ms_facilities.facility_id","ms_facilities.name","ms_facilities.description","ms_facilities.institution",
							"ms_facilities.address1","ms_facilities.address2","ms_facilities.city","ms_facilities.stateprov",
							"ms_facilities.postalcode","ms_facilities.country","ms_facilities.contact","ms_facilities.created_on",
							"ms_facilities.last_modified_on"
						]);
						if (!$ps_sort) { $ps_sort = "ms_facilities.facility_id"; }
						break;
					//default:
					//	throw new Exception("Invalid find type");
					//	break;
				}
 			
				$va_field_info = msGetFieldInfo($va_fields);
			
				$qr_res = $o_search->search($ps_rewritten_q, ['sort' => $ps_sort]);
				if ($pn_start > 0) {
					$qr_res->seek($pn_start);
				}
			
				$this->view->setVar('q', $ps_q);
				$this->view->setVar('result', $qr_res);
			
				$va_results = [];
				$vn_c = 0;
				while($qr_res->nextHit()) {
					$va_row = [];
					foreach($va_fields as $vs_field => $vs_displayname) {
						$va_field_bits = explode(".", $vs_field);
						
						if ($ps_naming == 'darwincore') { $vs_displayname = msFieldToDarwinCoreName($vs_field); }
						if (!$vs_displayname) {continue; }
					
						switch($va_field_info[$vs_field]['FIELD_TYPE']) {
							case FT_TIMESTAMP:
								if ($va_row[$vs_displayname]) { $va_row[$vs_displayname] .= '; '; }
								$va_row[$vs_displayname] .= $qr_res->getDate($vs_field);
								break;
							default:
						
								if ($va_field_info[$vs_field]['BOUNDS_CHOICE_LIST']) {
									if ($va_row[$vs_displayname]) { $va_row[$vs_displayname] .= ' '; }
									$va_row[$vs_displayname] .= $va_field_info['_instances'][$va_field_bits[0]]->getChoiceListValue($va_field_bits[1], $qr_res->get($vs_field));
									break;
								} 
								
								if($va_row[$vs_displayname] AND $qr_res->get($vs_field)) { $va_row[$vs_displayname] .= '; '; }
								$va_row[$vs_displayname] .= $qr_res->get($vs_field, ['delimiter' => ';']);
								break;
						}
					}
				
					// add additional table specific fields
					switch($ps_function) {
						case 'specimens':
							// get taxa
							if (sizeof($va_taxon_ids = $qr_res->get('ms_taxonomy.taxon_id', ['returnAsArray' => true])) > 0) {
								if (!($qr_taxa = caMakeSearchResult('ms_taxonomy', $va_taxon_ids))) { break; }
								
								$va_row['taxonomy_name'] = [];
								while($qr_taxa->nextHit()) {
									
									$va_taxon = [
										'names' => []
									];
									if ($ps_naming != 'darwincore') {
										$va_taxon['taxon_id'] = $qr_taxa->get('ms_taxonomy.taxon_id');
									}
								
									if (sizeof($va_name_ids = $qr_taxa->get('ms_taxonomy_names.alt_id', ['returnAsArray' => true]))) {
										$qr_names = caMakeSearchResult('ms_taxonomy_names', $va_name_ids);
										while($qr_names->nextHit()) {
											$va_name = [];
									
											foreach([
												"ht_kingdom","ht_phylum","ht_class","ht_subclass", "ht_superorder","ht_order",
												"ht_suborder","ht_superfamily","ht_family","ht_subfamily",
												"genus", "ht_supraspecific_clade", "species","subspecies","variety","author","year",
												"notes", "is_primary",
												"created_on","last_modified_on"
											] as $vs_field) {
												$vs_displayname = $vs_field;
												
											if ($ps_naming == 'darwincore') { 
												$vs_displayname = msFieldToDarwinCoreName("ms_taxonomy_names.{$vs_field}"); 
												if (!$vs_displayname) {$vs_displayname = msFieldToDarwinCoreName("ms_taxonomy.{$vs_field}"); } 
											}
											if (!$vs_displayname) {continue; }
											#if ($va_name[$vs_displayname]) { $va_name[$vs_displayname] .= ' '; }
											$delim = '';
											if($va_name[$vs_displayname] AND $qr_names->get($vs_field)) {$va_name[$vs_displayname] .= '; '; }
												$va_name[$vs_displayname] .= (in_array($vs_field, ["created_on","last_modified_on"])) ? $qr_names->getDate($vs_field) : $qr_names->get($vs_field);
											
											}
								
								
											$va_taxon['names'][] = $va_name;
										}
									}
									$va_row['taxonomy_name'][] = $va_taxon;
								}
							}
							break;
						case 'media':
							// get media files
							if (sizeof($va_file_ids = $qr_res->get('ms_media_files.media_file_id', ['returnAsArray' => true])) > 0) {
								if (!($qr_files = caMakeSearchResult('ms_media_files', $va_file_ids))) { break; }
							
								$va_row['medium.media'] = [];
								while($qr_files->nextHit()) {
									if (!$qr_files->get('ms_media_files.published')) { continue; }
									$va_info = $qr_files->getMediaInfo('media', 'original');
									$va_info = [
										'title' => $qr_files->get('ms_media_files.title'),
										'mimetype' => $va_info['MIMETYPE'],
										'filesize' => caHumanFilesize($va_info['PROPERTIES']['filesize']),
										'doi' => $qr_files->get('ms_media_files.doi'),
										'side' => $qr_files->get('ms_media_files.side'),
										'element' => $qr_files->get('ms_media_files.element'),
										'published_on' => ($qr_files->get('ms_media_files.published_on') > 0) ? $qr_files->getDate('ms_media_files.published_on') : "",
										'download' => "http://www.morphosource.org/index.php/Detail/MediaDetail/DownloadMedia/media_id/".$qr_files->get('ms_media.media_id')."/media_file_id/".$qr_files->get('ms_media_files.media_file_id')
									];
									
									if ($ps_naming == 'darwincore') {
										foreach($va_info as $vs_field => $vs_value) {
											$vs_displayname = msFieldToDarwinCoreName("ms_media_files.{$vs_field}");
											if (!$vs_displayname) { continue; }
											if($va_info[$vs_displayname] AND $vs_value) { $va_info[$vs_displayname] .= '; '; }
											$va_info[$vs_displayname] .= $vs_value;
											unset($va_info[$vs_field]);
										}
									} 	
									$va_row['medium.media'][] = $va_info;
								}
							}
							break;
					}
				
					ksort($va_row);
					$va_results[] = $va_row;
				
					$vn_c++;
					if (($pn_limit > 0) && ($vn_c >= $pn_limit)) { break; }
				}
		
				$this->view->setVar('response', ['status' => 'ok', 'q' => $ps_q, 'totalResults' => $qr_res->numHits(), 'returnedResults' => sizeof($va_results), 'start' => $pn_start, 'results' => $va_results]);
			} catch (Exception $e) {
				$this->view->setVar('response', ['status' => 'err', 'message' => $e->getMessage()]);
			}
			
 			$this->render("find_json.php");
 		}
 		# -------------------------------------------------------
 	}
