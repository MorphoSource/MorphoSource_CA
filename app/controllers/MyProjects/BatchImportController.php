<?php
/* ----------------------------------------------------------------------
 * controllers/MyProjects/BatchImportController.php
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2017 Whirl-i-Gig
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
 	require_once(__CA_MODELS_DIR__."/ms_media_files.php");
 	require_once(__CA_MODELS_DIR__."/ms_specimens.php");
 	require_once(__CA_MODELS_DIR__."/ms_media.php");
 	require_once(__CA_MODELS_DIR__."/ms_facilities.php");
 	require_once(__CA_MODELS_DIR__."/ms_institutions.php");
 	require_once(__CA_MODELS_DIR__."/ms_scanners.php");
 	require_once(__CA_MODELS_DIR__."/ms_taxonomy.php");
 	require_once(__CA_MODELS_DIR__."/ms_taxonomy_names.php");
 	require_once(__CA_MODELS_DIR__."/ms_specimens_x_taxonomy.php");
 	require_once(__CA_APP_DIR__.'/helpers/morphoSourceHelpers.php');
 	require_once(__CA_LIB_DIR__."/core/Parsers/DelimitedDataParser.php");
 
 	class BatchImportController extends ActionController {
 		# -------------------------------------------------------
			/** 
			 * declare table instance
			*/
			protected $opo_project;
			protected $opn_project_id;
			protected $ops_project_name;
			protected $opn_batch_institution_id;
			protected $opn_batch_facility_id;
			protected $opn_batch_scanner_id;

 		# -------------------------------------------------------
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 			parent::__construct($po_request, $po_response, $pa_view_paths);
 			if(!$this->request->isLoggedIn()){
 				$this->notification->addNotification("You must be logged in to access the Dashboard", __NOTIFICATION_TYPE_ERROR__);
				$this->response->setRedirect(caNavUrl($this->request, "", "LoginReg", "form"));
 			}
 			$this->opo_project = new ms_projects();
			# --- is there a project already selected, are we selecting a project
			$vn_select_project_id = $this->request->getParameter('select_project_id', pInteger);
			if($vn_select_project_id){
				# --- select project
				msSelectProject($this, $this->request);
			}
 			if($this->request->session->getVar('current_project_id') && ($this->request->user->canDoAction("is_administrator") || $this->opo_project->isFullAccessMember($this->request->user->get("user_id"), $this->request->session->getVar('current_project_id')))){
 				$this->opn_project_id = $this->request->session->getVar('current_project_id');
				$this->opo_project->load($this->opn_project_id);
				$this->ops_project_name = $this->opo_project->get("name");
				$this->view->setvar("project_id", $this->opn_project_id);
				$this->view->setvar("project_name", $this->ops_project_name);
 			}else{
 				$this->notification->addNotification("Please select a project", __NOTIFICATION_TYPE_ERROR__);
				$this->response->setRedirect(caNavUrl($this->request, "MyProjects", "Dashboard", "projectList"));
 			}
			$this->view->setvar("project", $this->opo_project);
			
			# --- universal batch settings
			$this->opn_batch_institution_id = $this->request->session->getVar('batch_institution_id');
			$this->opn_batch_facility_id = $this->request->session->getVar('batch_facility_id');
			$this->opn_batch_scanner_id = $this->request->session->getVar('batch_scanner_id');
 		}
 		# -------------------------------------------------------
 		function overview() {
			$this->render('BatchImport/overview_html.php');
 		}
 		# -------------------------------------------------------
 		function importSettingsForm() {
			# --- wipe out session vars
			$this->request->session->setVar('batch_institution_id', '');
 			$this->request->session->setVar('batch_facility_id', '');
 			$this->request->session->setVar('batch_scanner_id', '');
 			$this->request->session->setVar('batch_info', '');
 			
			$this->render('BatchImport/import_settings_form_html.php');
 		}
 		# -------------------------------------------------------
 		function reviewImportSettings() {
 			# --- check all setting have been selected
 			if($this->request->getParameter('institution_id', pInteger)){
 				$this->request->session->setVar('batch_institution_id', $this->request->getParameter('institution_id', pInteger));
 			}
 			if($this->request->getParameter('facility_id', pInteger)){
 				$this->request->session->setVar('batch_facility_id', $this->request->getParameter('facility_id', pInteger));
 			}
 			if($this->request->getParameter('scanner_id', pInteger)){
 				$this->request->session->setVar('batch_scanner_id', $this->request->getParameter('scanner_id', pInteger));
 			}
 			
 			$this->opn_batch_institution_id = $this->request->session->getVar('batch_institution_id');
			$this->opn_batch_facility_id = $this->request->session->getVar('batch_facility_id');
			$this->opn_batch_scanner_id = $this->request->session->getVar('batch_scanner_id');
			
			$this->view->setVar("batch_institution_id", $this->opn_batch_institution_id);
			$this->view->setVar("batch_facility_id", $this->opn_batch_facility_id);
			$this->view->setVar("batch_scanner_id", $this->opn_batch_scanner_id);

 			if($this->opn_batch_institution_id && $this->opn_batch_facility_id && $this->opn_batch_scanner_id){
 				# --- get the institution, facility, scanner names for review before file upload
 				$t_intitution = new ms_institutions($this->opn_batch_institution_id);
 				$this->view->setVar("institution_name", $t_intitution->get("name"));
 				$t_facility = new ms_facilities($this->opn_batch_facility_id);
 				$this->view->setVar("facility_name", $t_facility->get("name"));
 				$t_scanner = new ms_scanners($this->opn_batch_scanner_id);
 				$this->view->setVar("scanner_name", $t_scanner->get("name"));
 				$this->render('BatchImport/review_import_settings_html.php');
 			}else{
 				$this->importSettingsForm();
 			}
 		}
 		# -------------------------------------------------------
 		function uploadFile(){
 			$this->view->setVar("batch_institution_id", $this->opn_batch_institution_id);
			$this->view->setVar("batch_facility_id", $this->opn_batch_facility_id);
			$this->view->setVar("batch_scanner_id", $this->opn_batch_scanner_id);
 			#print_r($_FILES["spreadsheet"]);
 			if ($_FILES["spreadsheet"]["tmp_name"]) {
 				# --- uploaded file
 				$o_parser = DelimitedDataParser::load($_FILES["spreadsheet"]["tmp_name"][0]);
 				$vn_row = 1;
 				$vn_col = 1;
 				$va_field_key = array();
 				$va_batch = array();
 				$va_linked_specimen = array();
 				$vn_new_specimen = 0;
 				$vn_new_media_groups = 0;
 				$vn_new_media_files = 0;
 				$vn_error_rows = 0;
 				$vs_user_upload_directory = $this->request->user->getPreference('user_upload_directory')."/";
				while($o_parser->nextRow()) {
					if($vn_row > 1){
						$va_row = $o_parser->getRow();
						if($vn_row == 2){
							# --- table.field names, use this as a key
							$va_field_key = $va_row;
						}elseif($vn_row > 3){
							$vn_specimen_id = null;
							$t_specimen = new ms_specimens();
							$t_media = new ms_media();
							$t_taxonomy = new ms_taxonomy();
							$t_taxonomy_names = new ms_taxonomy_names();
							$t_specimen_lookup = new ms_specimens();
							$va_batch[$vn_row]['errors'] = false;
							$va_batch[$vn_row]['hasData'] = false;
							foreach($va_row as $vn_key => $vs_value){
								# --- skip first col, it's a label
								if($vn_key > 0){
									# --- use key to get the table and the field
									$vs_table_field = $va_field_key[$vn_key];
									$va_table_field = explode(".", $va_field_key[$vn_key]);
									$vs_table = $va_table_field[0];
									$vs_field = $va_table_field[1];
									switch($vs_table){
										case "ms_specimens":
											# --- try to match specimen to existing records first
											if(!$vn_specimen_id){
												if($vs_value){
													$va_batch[$vn_row]['hasData'] = true;
													if($vs_field == "specimen_id"){
														$t_specimen_lookup->load(str_replace("s", "", strtolower($vs_value)));
														if($t_specimen_lookup->get("specimen_id")){
															$vn_specimen_id = $t_specimen_lookup->get("specimen_id");
														}
													}elseif($vs_field == "occurrence_id"){
														$t_specimen_lookup->load(array("occurrence_id" => $vs_value));
														if($t_specimen_lookup->get("specimen_id")){
															$vn_specimen_id = $t_specimen_lookup->get("specimen_id");
														}else{
															$t_specimen->set($vs_field, $vs_value);
														}
													}elseif($vs_field == "catalog_number"){
														$t_specimen->set($vs_field, $vs_value);
														# --- check if there is a specimen with the identical institution_code, collection_code, catalog_number
														$t_specimen_lookup->load(array("institution_code" => $t_specimen->get("institution_code"), "collection_code" => $t_specimen->get("collection_code"), "catalog_number" => $t_specimen->get("catalog_number")));
														if($t_specimen_lookup->get("specimen_id")){
															$vn_specimen_id = $t_specimen_lookup->get("specimen_id");
														}
													}else{
														# --- need to handle dropdown values
														switch($vs_field){
															# --- selects
															case "reference_source":
															case "type":
															case "sex":
																$t_specimen->set($vs_field, $t_specimen->getChoiceListInternalValue($vs_field, $vs_value));
															break;
															# -------------------------------------
															default:
																$t_specimen->set($vs_field, $vs_value);
															break;
															# -------------------------------------
														}
													}
												}
												$vs_error = "";
												if ($t_specimen->numErrors() > 0) {
													$vs_error = join("; ", $t_specimen->getErrors());
													$va_batch[$vn_row]['errors'] = true;
												}
												$va_batch[$vn_row][$vs_table_field] = (($vs_error) ? "<div class='formErrors'>".$vs_error."</div>" : "").$vs_value;
											}else{
												$va_batch[$vn_row][$vs_table_field] = "";
											}
										break;
										# --------------------------------------------
										case "ms_taxonomy":
											if(!$vn_specimen_id){
												if($vs_value){
													$va_batch[$vn_row]['hasData'] = true;
													# --- need to handle dropdown values
													switch($vs_field){
														# -------------------------------------
														default:
															$t_taxonomy->set($vs_field, $vs_value);
														break;
														# -------------------------------------
													}
												}
												$vs_error = "";
												if ($t_taxonomy->numErrors() > 0) {
													$vs_error = join("; ", $t_taxonomy->getErrors());
													$va_batch[$vn_row]['errors'] = true;
												}
												$va_batch[$vn_row][$vs_table_field] = (($vs_error) ? "<div class='formErrors'>".$vs_error."</div>" : "").$vs_value;
											}else{
												$va_batch[$vn_row][$vs_table_field] = "";
											}										
										break;
										# --------------------------------------------
										case "ms_taxonomy_names":
											if(!$vn_specimen_id){
												if($vs_value){
													$va_batch[$vn_row]['hasData'] = true;
													# --- need to handle dropdown values
													switch($vs_field){
														# --- select
														case "is_extinct":
															$t_taxonomy_names->set($vs_field, $t_taxonomy_names->getChoiceListInternalValue($vs_field, $vs_value));
														break;
														# -------------------------------------
														default:
															$t_taxonomy_names->set($vs_field, $vs_value);
														break;
														# -------------------------------------
													}
												}
												$vs_error = "";
												if ($t_taxonomy_names->numErrors() > 0) {
													$vs_error = join("; ", $t_taxonomy_names->getErrors());
													$va_batch[$vn_row]['errors'] = true;
												}
												$va_batch[$vn_row][$vs_table_field] = (($vs_error) ? "<div class='formErrors'>".$vs_error."</div>" : "").$vs_value;
											}else{
												$va_batch[$vn_row][$vs_table_field] = "";
											}
										break;
										# --------------------------------------------
										case "ms_media":
											if($vs_value){
												$va_batch[$vn_row]['hasData'] = true;
												# --- need to handle dropdown values
												switch($vs_field){
													# -------------------------------------
													# --- selects
													case "published":
													case "side":
													case "is_copyrighted":
													case "scanner_calibration_shading_correction":
													case "scanner_calibration_flux_normalization":
													case "scanner_calibration_geometric_calibration":
													case "copyright_permission":
													case "copyright_license":
														$t_media->set($vs_field, $t_media->getChoiceListInternalValue($vs_field, $vs_value));
													break;
													# -------------------------------------
													default:
														$t_media->set($vs_field, $vs_value);
													break;
													# -------------------------------------
												}
											}
											$vs_error = "";
											if ($t_media->numErrors() > 0) {
												$vs_error = join("; ", $t_media->getErrors());
												$va_batch[$vn_row]['errors'] = true;
											}
											$va_batch[$vn_row][$vs_table_field] = (($vs_error) ? "<div class='formErrors'>".$vs_error."</div>" : "").$vs_value;
										
										break;
										# --------------------------------------------
										case "ms_media_files":
											if($vs_field == "media"){
												# --- do this here since there can be more than one file per row
												$t_media_files = new ms_media_files();
											}
											if($vs_value){
												$va_batch[$vn_row]['hasData'] = true;
												# --- need to handle dropdown values
												switch($vs_field){
													case "media":
													case "media_preview":
														# --- check if file_exists - but don't set it.
														if(!file_exists($vs_user_upload_directory.$vs_value)){
															$va_batch[$vn_row]['errors'] = true;
															$va_batch[$vn_row][$vs_table_field] = "<div class='formErrors'>File is not in your upload directory</div>";
														}elseif($vs_field == "media"){
															$t_media_files->set($vs_field, $vs_user_upload_directory.$vs_value);
															$vn_new_media_files++;
														}
													break;
													# -------------------------------------
													# --- selects
													case "side":
													case "use_for_preview":
													case "file_type":
													case "distance_units":
													case "max_distance_x":
													case "published":
														$t_media_files->set($vs_field, $t_media_files->getChoiceListInternalValue($vs_field, $vs_value));
													break;
													# -------------------------------------
													default:
														$t_media_files->set($vs_field, $vs_value);
													break;
													# -------------------------------------
												}
											}
											$vs_error = "";
											if ($t_media_files->numErrors() > 0) {
												$vs_error = join("; ", $t_media_files->getErrors());
												$va_batch[$vn_row]['errors'] = true;
											}
											$va_batch[$vn_row][$vs_table_field] = (($vs_error) ? "<div class='formErrors'>".$vs_error."</div>" : "").$vs_value;
											
										break;
										# --------------------------------------------
									}
								}
							}
						}
						if($va_batch[$vn_row]['hasData']){
							if(!$va_batch[$vn_row]['errors']){
								if($vn_specimen_id){
									$va_linked_specimen[] = $vn_specimen_id;
									$va_batch[$vn_row]["ms_specimens.specimen_id"] = $vn_specimen_id;
								}else{
									$vn_new_specimen++;
								}
								$vn_new_media_groups++;
							}else{
								$vn_error_rows++;
							}
						}else{
							unset($va_batch[$vn_row]);
						}
					}

					$vn_row++;
				}
				# --- build array of all batch info and stats and save as session/ pass to view for review
				$va_batch_info = array(
					"stats" => array("errors" => $vn_error_rows, "linked_specimen" => sizeof($va_linked_specimen), "new_specimen" => $vn_new_specimen, "new_media_groups" => $vn_new_media_groups, "new_media_files" => $vn_new_media_files),
					"batch" => $va_batch
				);
				$this->view->setVar("batch_info", $va_batch_info);
				$this->request->session->setVar('batch_info', $va_batch_info);
				$this->render('BatchImport/review_import_html.php');
 			}else{
 				$va_batch_info = $this->request->session->getVar('batch_info');
 				if(is_array($va_batch_info) && sizeof($va_batch_info)){
 					$this->view->setVar("batch_info", $va_batch_info);
 					$this->render('BatchImport/review_import_html.php');
 				}else{
 					$this->view->setVar("errors", $va_errors["Please upload a spreadsheet file to import"]);
 					$this->render('BatchImport/review_import_settings_html.php');
 				}
 			}
 		}
 		# -------------------------------------------------------
 		function saveBatch(){
 			$va_batch_info = $this->request->session->getVar('batch_info');
 			$this->view->setVar("batch_institution_id", $this->opn_batch_institution_id);
			$this->view->setVar("batch_facility_id", $this->opn_batch_facility_id);
			$this->view->setVar("batch_scanner_id", $this->opn_batch_scanner_id);
 			$va_rows_not_imported = array();
 			$vn_linked_specimen = 0;
 			$vn_new_specimen = 0;
 			$vn_linked_taxonomy = 0;
			$vn_new_taxonomy = 0;
			$vn_new_media_groups = 0;
			$vn_new_media_files = 0;
			$va_new_media_groups = array();
			$va_errors = array();
			$vs_user_upload_directory = $this->request->user->getPreference('user_upload_directory')."/";
			$vs_upload_base_directory = $this->request->getAppConfig()->get('upload_base_directory');
 			if(is_array($va_batch_info["batch"]) && sizeof($va_batch_info["batch"])){
 				foreach($va_batch_info["batch"] as $vs_row => $va_row){
 					# --- skip if there were errors
 					if($va_row["errors"]){
 						$va_rows_not_imported[] = $vs_row;
 					}else{
 						$vn_specimen_id = null;
						$t_specimen = new ms_specimens();
						$t_media = new ms_media();
						$t_media_files = new ms_media_files();
						$t_taxonomy = new ms_taxonomy();
						$t_taxonomy_names = new ms_taxonomy_names();
						$t_specimen_lookup = new ms_specimens();
 						$va_taxonomy_names_lookup = array();
 						$va_media_files = array();
 						foreach($va_row as $vs_table_field => $vs_value){
 							$va_table_field = explode(".", $vs_table_field);
							$vs_table = $va_table_field[0];
							$vs_field = $va_table_field[1];
							$vn_media_file = $va_table_field[2];
							
							if($vs_value){
								switch($vs_table){
									case "ms_specimens":
										# --- try to match specimen to existing records first
										if(!$vn_specimen_id){
											if($vs_field == "specimen_id"){
												$t_specimen_lookup->load(str_replace("s", "", strtolower($vs_value)));
												if($t_specimen_lookup->get("specimen_id")){
													$vn_specimen_id = $t_specimen_lookup->get("specimen_id");
												}
											}elseif($vs_field == "occurrence_id"){
												$t_specimen_lookup->load(array("occurrence_id" => $vs_value));
												if($t_specimen_lookup->get("specimen_id")){
													$vn_specimen_id = $t_specimen_lookup->get("specimen_id");
												}else{
													$t_specimen->set($vs_field, $vs_value);
													# --- see if we can get the idigbio uuid by searching on the occurrence id
													$va_idigbio_results = $t_specimen->getIDBSpecimenInfo(array("occurrenceid" => $vs_value));
													if($va_idigbio_results["success"]){
														$vs_uuid = $va_idigbio_results["data"]["items"][0]["uuid"];
														$t_specimen->set("uuid", $vs_uuid);
													}
												}
											}elseif($vs_field == "catalog_number"){
												$t_specimen->set($vs_field, $vs_value);
												# --- check if there is a specimen with the identical institution_code, collection_code, catalog_number
												$t_specimen_lookup->load(array("institution_code" => $t_specimen->get("institution_code"), "collection_code" => $t_specimen->get("collection_code"), "catalog_number" => $t_specimen->get("catalog_number")));
												if($t_specimen_lookup->get("specimen_id")){
													$vn_specimen_id = $t_specimen_lookup->get("specimen_id");
												}
											}else{
												# --- need to handle dropdown values
												switch($vs_field){
													# --- selects
													case "reference_source":
													case "type":
													case "sex":
														$t_specimen->set($vs_field, $t_specimen->getChoiceListInternalValue($vs_field, $vs_value));
													break;
													# -------------------------------------
													default:
														$t_specimen->set($vs_field, $vs_value);
													break;
													# -------------------------------------
												}
											}
											
											$vs_error = "";
											if ($t_specimen->numErrors() > 0) {
												$vs_error = join("; ", $t_specimen->getErrors());
												$va_errors[$vs_row]['errors'] = $vs_error;
												$va_rows_not_imported[] = $vs_row;
											}
										}
									break;
									# --------------------------------------------
									case "ms_taxonomy":
										if(!$vn_specimen_id){
											# --- need to handle dropdown values
											switch($vs_field){
												# -------------------------------------
												default:
													$t_taxonomy->set($vs_field, $vs_value);
												break;
												# -------------------------------------
											}
											$vs_error = "";
											if ($t_taxonomy->numErrors() > 0) {
												$vs_error = join("; ", $t_taxonomy->getErrors());
												$va_errors[$vs_row]['errors'] = $vs_error;
												$va_rows_not_imported[] = $vs_row;
											}
										}										
									break;
									# --------------------------------------------
									case "ms_taxonomy_names":
										if(!$vn_specimen_id){
											# --- need to handle dropdown values
											switch($vs_field){
												# --- select
												case "is_extinct":
													$t_taxonomy_names->set($vs_field, $t_taxonomy_names->getChoiceListInternalValue($vs_field, $vs_value));
												break;
												# -------------------------------------
												default:
													$t_taxonomy_names->set($vs_field, $vs_value);
													if(!in_array("$vs_field", array("source_info", "notes"))){
														$va_taxonomy_names_lookup[$vs_field] = $vs_value;
													}
												break;
												# -------------------------------------
											}
											$vs_error = "";
											if ($t_taxonomy_names->numErrors() > 0) {
												$vs_error = join("; ", $t_taxonomy_names->getErrors());
												$va_errors[$vs_row]['errors'] = $vs_error;
												$va_rows_not_imported[] = $vs_row;
											}
										}
									break;
									# --------------------------------------------
									case "ms_media":
										# --- need to handle dropdown values
										switch($vs_field){
											# -------------------------------------
											# --- selects
											case "published":
											case "side":
											case "is_copyrighted":
											case "scanner_calibration_shading_correction":
											case "scanner_calibration_flux_normalization":
											case "scanner_calibration_geometric_calibration":
											case "copyright_permission":
											case "copyright_license":
												$t_media->set($vs_field, $t_media->getChoiceListInternalValue($vs_field, $vs_value));
											break;
											# -------------------------------------
											default:
												$t_media->set($vs_field, $vs_value);
											break;
											# -------------------------------------
										}
										$vs_error = "";
										if ($t_media->numErrors() > 0) {
											$vs_error = join("; ", $t_media->getErrors());
											$va_errors[$vs_row]['errors'] = $vs_error;
											$va_rows_not_imported[] = $vs_row;
										}
									break;
									# --------------------------------------------
									case "ms_media_files":
										# --- store info in array and set all at once at end after media record is inserted
										if($vs_field == "media"){
											# --- do this here since there can be more than one file per row
											$t_media_files = new ms_media_files();
										}
										if($vs_value){
											# --- need to handle dropdown values
											switch($vs_field){
												case "media":
												case "media_preview":
													# --- check if file_exists - but don't set it.
													if($vs_user_upload_directory && $vs_upload_base_directory && (preg_match('!^'.$vs_upload_base_directory.'!', $vs_user_upload_directory))){
														if(file_exists($vs_user_upload_directory.$vs_value)){
															$va_media_files[$vn_media_file][$vs_field] = $vs_user_upload_directory.$vs_value;
															$va_media_files[$vn_media_file][$vs_field."_original_filename"] = $vs_value;
														}else{
															$va_errors[$vs_row]['errors'] = 'media file is not in your upload directory';
														}
													}
												break;
												# -------------------------------------
												# --- selects
												case "side":
												case "use_for_preview":
												case "file_type":
												case "distance_units":
												case "max_distance_x":
												case "published":
													$va_media_files[$vn_media_file][$vs_field] = $t_media_files->getChoiceListInternalValue($vs_field, $vs_value);
												break;
												# -------------------------------------
												default:
													$va_media_files[$vn_media_file][$vs_field] = $vs_value;
												break;
												# -------------------------------------
											}
										}
									break;
									# --------------------------------------------
								}
							}	
 						}
 						# --- set constants and do inserts if no errors
						if(!$va_errors[$vs_row]){
							if(!$vn_specimen_id){
								# --- check if there is a taxonomy record in MorphoSource to link to
								$t_taxonomy_names_lookup = new ms_taxonomy_names();
								$t_taxonomy_names_lookup->load($va_taxonomy_names_lookup);
								if($t_taxonomy_names_lookup->get("alt_id")){
									$vn_taxon_id = $t_taxonomy_names_lookup->get("taxon_id");
									$vn_alt_id = $t_taxonomy_names_lookup->get("alt_id");
									$vn_linked_taxonomy++;	
								}else{
									# --- add the ms_taxonomy record
									$t_taxonomy->set('project_id', $this->opn_project_id);
									$t_taxonomy->set('user_id', $this->request->getUserID());
							
									# --- add the taxonomy_names record
									$t_taxonomy_names->set("is_primary", 1);
									$t_taxonomy_names->set('project_id', $this->opn_project_id);
									$t_taxonomy_names->set('user_id', $this->request->getUserID());
							
							
									# do insert for ms_taxonomy
									$t_taxonomy->setMode(ACCESS_WRITE);
									$t_taxonomy->insert();
							
									if ($t_taxonomy->numErrors()) {
										$va_errors[$vs_row] = "There were errors saving the taxonomy record: ".join("; ", $t_taxonomy->getErrors());
										$va_rows_not_imported[] = $vs_row;
									}else{
										# do insert for ms_taxonomy_names
										$t_taxonomy_names->set('taxon_id', $t_taxonomy->get("taxon_id"));
										$t_taxonomy_names->setMode(ACCESS_WRITE);
										$t_taxonomy_names->insert();

										if ($t_taxonomy_names->numErrors()) {
											$va_errors[$vs_row] = "There were errors saving the taxonomy names record: ".join("; ", $t_taxonomy_names->getErrors());
											$va_rows_not_imported[] = $vs_row;
										}else{
											$vn_new_taxonomy++;
											$vn_taxon_id = $t_taxonomy->get("taxon_id");
											$vn_alt_id = $t_taxonomy_names->get("alt_id");
										}
									}
								}		
								if(!$va_errors[$vs_row] && $vn_alt_id && $vn_taxon_id){
									$t_specimen->set("institution_id", $this->opn_batch_institution_id);
									$t_specimen->set("user_id", $this->request->user->get("user_id"));
									$t_specimen->set("project_id", $this->opn_project_id);
									$t_specimen->set("batch_status", 1);
									$t_specimen->setMode(ACCESS_WRITE);
									$t_specimen->insert();
									if ($t_specimen->numErrors()) {
										$va_errors[$vs_row] = "There were errors saving the specimen record: ".join("; ", $t_specimen->getErrors());
										$va_rows_not_imported[] = $vs_row;
									}else{
										$vn_new_specimen++;
									}
									$vn_specimen_id = $t_specimen->get("specimen_id");
									# --- link specimen to taxonomy
									$t_specimens_x_taxonomy = new ms_specimens_x_taxonomy();
									$t_specimens_x_taxonomy->set("specimen_id",$vn_specimen_id);
									$t_specimens_x_taxonomy->set("alt_id",$vn_alt_id);
									$t_specimens_x_taxonomy->set("user_id",$this->request->user->get("user_id"));
									$t_specimens_x_taxonomy->set("taxon_id",$vn_taxon_id);

									# do insert
									$t_specimens_x_taxonomy->setMode(ACCESS_WRITE);
									$t_specimens_x_taxonomy->insert();
	
									if ($t_specimens_x_taxonomy->numErrors()) {
										$va_errors[$vs_row] = "There were errors linking the specimen record to taxonomy: ".join("; ", $t_specimens_x_taxonomy->getErrors());
										$va_rows_not_imported[] = $vs_row;
									}
								}
							}else{
								$vn_linked_specimen++;
							}
							if(!$va_errors[$vs_row] && $vn_specimen_id){
								$t_media->set("specimen_id", $vn_specimen_id);
								$t_media->set("facility_id", $this->opn_batch_facility_id);
								$t_media->set("scanner_id", $this->opn_batch_scanner_id);
								$t_media->set("user_id", $this->request->user->get("user_id"));
								$t_media->set("project_id", $this->opn_project_id);
								$t_media->set("batch_status", 1);
								$t_media->setMode(ACCESS_WRITE);
								$t_media->insert();
								if ($t_media->numErrors()) {
									$va_errors[$vs_row] = join("; ", $t_media->getErrors());
									$va_rows_not_imported[] = $vs_row;
								}else{
									$va_new_media_groups[] = $t_media->get("media_id");
									$vn_new_media_groups++;
									# --- media files
									$vb_has_preview = false;
									foreach($va_media_files as $vn_i => $va_media_file_info){
										$t_media_file = new ms_media_files();
										$t_media_file->set("user_id", $this->request->user->get("user_id"));
										$t_media_file->set("media_id", $t_media->get("media_id"));
										$t_media_file->set("batch_status", 1);
										foreach($va_media_file_info as $vs_field => $vs_value){
											switch($vs_field){
												case "media":
													$t_media_file->set("media", $vs_value, array("original_filename" => $va_media_file_info["media_original_filename"]));
												break;
												# ---------------------------------------------
												case "media_original_filename":
												case "media_preview":
												case "media_preview_original_filename":
													# --- skip
												break;
												# ---------------------------------------------
												default:
													$t_media_file->set($vs_field, $vs_value);
												break;
												# ---------------------------------------------
											}												
										}
										if($t_media_file->get("use_for_preview")){
											$vb_has_preview = true;
										}else{
											if(!$vb_has_preview && ($vn_i == sizeof($va_media_files))){
												$t_media_file->set("use_for_preview", 1);
											}
										}
										# --- insert
										$t_media_file->setMode(ACCESS_WRITE);
										$t_media_file->insert();
										if ($t_media_file->numErrors() == 0) {
											$vn_new_media_files++;
											# --- check for preview files
											if (isset($va_media_file_info["media_preview"])) {
												$t_media_file->set('media', $va_media_file_info["media_preview"], array(
													'original_filename' => $va_media_file_info["media_preview_original_filename"]
												));
												$va_update_opts['updateOnlyMediaVersions'] = array('icon', 'tiny', 'thumbnail', 'widethumbnail', 'small', 'preview', 'preview190', 'widepreview', 'medium', 'mediumlarge', 'large');
												$t_media_file->update($va_update_opts);
											}
										}else{
											$va_errors[$vs_row] = "Error uploading file ".$va_media_file_info["media_original_filename"].": ".join("; ", $t_media_file->getErrors());
											$va_rows_not_imported[] = $vs_row;
										}
									}
								}
							}
						}else{
							$va_rows_not_imported[] = $vs_row;
						}
 					}
 				}
 				# --- wipe out session vars
				$this->request->session->setVar('batch_institution_id', '');
				$this->request->session->setVar('batch_facility_id', '');
				$this->request->session->setVar('batch_scanner_id', '');
				$this->request->session->setVar('batch_info', '');
 				
				$this->view->setVar("stats", array("linked_specimen" => $vn_linked_specimen,  "new_specimen" => $vn_new_specimen, "linked_taxonomy" => $vn_linked_taxonomy, "new_taxonomy" => $vn_new_taxonomy, "new_media_groups" => $vn_new_media_groups, "new_media_group_ids" => $va_new_media_groups, "new_media_files" => $vn_new_media_files));
				$this->view->setVar("rows_not_imported", $va_rows_not_imported);
				$this->view->setVar("errors", $va_errors);
				$this->render('BatchImport/review_imported_records_html.php');
 			}else{
 				$this->view->setVar("errors", $va_errors["There is no batch data to import"]);
 				$this->render('BatchImport/review_imported_records_html.php');
 			}
 		}
 		# -------------------------------------------------------
			

 	}
 ?>