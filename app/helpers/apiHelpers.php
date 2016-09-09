<?php
/** ---------------------------------------------------------------------
 * app/helpers/apiHelpers.php : 
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
 * @package CollectiveAccess
 * @subpackage utils
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
 * 
 * ----------------------------------------------------------------------
 */

	/**
	 *
	 */
	use ICanBoogie\Inflector;
	
	require_once(__CA_LIB_DIR__.'/core/Datamodel.php');
	
	# -------------------------------------------------------
	/**
	 *
	 */
	function msInflectFieldNames($pa_field_names, $ps_format=null) {
		$o_inflector = Inflector::get('en');

		$va_field_names_proc = [];
		foreach($pa_field_names as $vs_field) {
			$va_field_bits = explode(".", $vs_field);
			if (!$va_field_names_proc[$vs_field]) {
				$va_field_names_proc[$vs_field] = $o_inflector->singularize(preg_replace("!^(ms|ca)_!", "", $va_field_bits[0])).($va_field_bits[1] ? ".".$va_field_bits[1] : "");
			}
		}
		
		return $va_field_names_proc;
	}
	# -------------------------------------------------------
	/**
	 *
	 */
	function msDeflectFieldNames($pa_field_names) {
		$o_dm = Datamodel::load();
		$o_inflector = Inflector::get('en');

		$va_field_names_proc = [];
		foreach($pa_field_names as $vs_field) {
			$va_field_bits = explode(".", $vs_field);
			if (!$va_field_names_proc[$vs_field]) {
				$vs_barename = $o_inflector->pluralize($va_field_bits[0]);
				if(!($t_instance = $o_dm->getInstanceByTableName($vs_name = "ca_{$vs_barename}", true)) && !($t_instance = $o_dm->getInstanceByTableName($vs_name = "ms_{$vs_barename}", true))) {
					continue;
				}
				if (!$t_instance->hasField($va_field_bits[1])) { continue; }
				
				$va_field_names_proc[$vs_field] = $vs_name.".".$va_field_bits[1];
			}
		}
		
		return $va_field_names_proc;
	}
	# -------------------------------------------------------
	/**
	 *
	 */
	function msGetFieldInfo($pa_fields) {
		$o_dm = Datamodel::load();
		
		$va_field_info = [];
		foreach($pa_fields as $vs_field => $vs_field_display) {
			$va_field_bits = explode(".", $vs_field);
			if(!($t_instance = $o_dm->getInstanceByTableName($va_field_bits[0], true))) {
				continue;
			}
			if (!$va_field_info['_instances'][$va_field_bits[0]]) { 
				$va_field_info['_instances'][$va_field_bits[0]] = $t_instance;
			}
			
			$va_field_info[$vs_field] = $t_instance->getFieldInfo($va_field_bits[1]);
		}	
		
		return $va_field_info;
	}
	# -------------------------------------------------------
	/**
	 *
	 */
	function msRewriteAPIQuery($ps_query) {
		$o_dm = Datamodel::load();
		$o_inflector = Inflector::get('en');
		
		$vs_query = $ps_query;
		
		if(preg_match_all("!([A-Za-z0-9_]+\.[A-Za-z0-9_]+:)!", $ps_query, $va_matches)) {
			foreach($va_matches[1] as $vs_inflected_field) {
				$va_field_bits = explode(".", $vs_inflected_field);
				
				$vs_barename = $o_inflector->pluralize($va_field_bits[0]);
				if(!($t_instance = $o_dm->getInstanceByTableName($vs_name = "ca_{$vs_barename}", true)) && !($t_instance = $o_dm->getInstanceByTableName($vs_name = "ms_{$vs_barename}", true))) {
					continue;
				}
				
				if (!$t_instance->hasField(substr($va_field_bits[1], 0, strlen($va_field_bits[1]) - 1))) { continue; }
				
				$vs_query = str_replace($vs_inflected_field, "{$vs_name}.".$va_field_bits[1], $vs_query);
			}
		}
		return $vs_query;
	}
	# -------------------------------------------------------
	/**
	 *
	 */
	function msFieldToDarwinCoreName($ps_fieldname) {
		global $g_ms_to_darwincore_table;
		return $g_ms_to_darwincore_table[$ps_fieldname];
	}
	# -------------------------------------------------------
	
	global $g_ms_to_darwincore_table; $g_ms_to_darwincore_table = [
		"ms_specimens.absolute_age" => "earliestEonOrLowestEonothem",
		"ms_specimens.approval_status" => "dcterms:accessRights", // Added to indicate access to media
		"ms_specimens.body_mass" => "measurement",
		"ms_specimens.body_mass_comments" => "measurement",
		"ms_specimens.catalog_number" => "catalogNumber", // Use catalogNumber twice, once for this field and another for voucherized field
		"ms_specimens.collected_on" => "eventDate",
		"ms_specimens.collection_code" => "collectionCode",
		"ms_specimens.collector" => "recordedBy",
		//"ms_specimens.created_on" => "N/A",
		"ms_specimens.description" => "occurrenceRemarks",
		"ms_specimens.element" => "occurrenceRemarks", // Generic field for addtional data, element and side are both recorded this way
		"ms_specimens.institution_code" => "institutionCode",
		"ms_specimens.institution_id" => "institutionID",
		"ms_specimens.last_modified_on" => "dcterms:modified", // Added to show how recent a record was modified. Not sure if creation date should be included
		"ms_specimens.locality_absolute_age" => "earliestEonOrLowestEonothem",
		"ms_specimens.locality_coordinates" => "verbatimCoordinates", // Just mapped this field
		"ms_specimens.locality_datum_zone" => "verbatimCoordinateSystem",
		"ms_specimens.locality_description" => "locality",
		"ms_specimens.locality_easting_coordinate" => "verbatimLongitude",
		"ms_specimens.locality_northing_coordinate" => "verbatimLatitude",
		"ms_specimens.locality_relative_age" => "lithostratigraphicTerms",
		"ms_specimens.locality_relative_age_bibref_id" => "dcterms:references",
		"ms_specimens.notes" => "occurrenceRemarks",
		"ms_specimens.reference_source" => "dynamicProperties",
		"ms_specimens.relative_age" => "lithostratigraphicTerms",
		"ms_specimens.sex" => "sex",
		"ms_specimens.side" => "occurrenceRemarks", // Generic field, mapped same as Element
		"ms_specimens.specimen_id" => "occurrenceID",
		"ms_specimens.taxon_id" => "associatedTaxa",
		"ms_specimens.url"	=> "dcterms:references",
		
		"ms_facilities.address1" => "institutionID",
		"ms_facilities.address2" => "institutionID",
		"ms_facilities.city" => "institutionID",
		"ms_facilities.contact" => "institutionID",
		"ms_facilities.country" => "institutionID",
		"ms_facilities.created_on" => "institutionID",
		"ms_facilities.description" => "institutionID",
		"ms_facilities.facility_id" => "institutionID",
		"ms_facilities.institution" => "institutionID",
		"ms_facilities.last_modified_on" => "dcterms:modified",
		"ms_facilities.name" => "institutionID",
		"ms_facilities.postalcode" => "institutionID",
		"ms_facilities.stateprov" => "institutionID",
		
		"ms_media.approval_status" => "dcterms:accessRights", // Provides information on users access to media
		"ms_media.copyright_info" => "dcterms:accessRights",
		"ms_media.copyright_license" => "dcterms:accessRights",
		"ms_media.copyright_permission" => "dcterms:accessRights",
		//"ms_media.created_on" => "N/A",
		"ms_media.element" => "occurrenceRemarks",
		"ms_media.grant_support" => "dcterms:bibliographicCitation",
		"ms_media.is_copyrighted" => "dcterms:accessRights",
		"ms_media.last_modified_on" => "dcterms:modified", // Added date record was last modified
		"ms_media.media_citation_instructions" => "dcterms:bibliographicCitations",
		"ms_media.media_citation_instruction1-3" => "dcterms:bibliographicCitations",
		"ms_media.media_id" => "datasetID", // This seems to be the most accurrate mapping
		"ms_media.notes" => "associatedMedia",
		//"ms_media.published_on" => "N/A",
		"ms_media.scanner_acquisition_time" => "measurement",
		"ms_media.scanner_amperage" => "measurement",
		"ms_media.scanner_calibration_description" => "measurement",
		"ms_media.scanner_calibration_flux_normalization" => "measurement",
		"ms_media.scanner_calibration_geometric_calibration" => "measurement",
		"ms_media.scanner_calibration_shading_correction" => "measurement",
		"ms_media.scanner_frame_averaging" => "measurement",
		"ms_media.scanner_projections" => "measurement",
		"ms_media.scanner_technicians" => "measurement",
		"ms_media.scanner_voltage" => "measurement",
		"ms_media.scanner_watts" => "measurement",
		"ms_media.scanner_wedge" => "measurement",
		"ms_media.scanner_x_resolution" => "measurement",
		"ms_media.scanner_y_resolution" => "measurement",
		"ms_media.scanner_z_resolution" => "measurement",
		"ms_media.side" => "associatedMedia",
		"ms_media.title" => "datasetName", // Again, this seems to be the most accurate mapping for media groups

		"ms_media_files.title" => "associatedMedia",
		"ms_media_files.mimetype" => "associatedMedia",
		"ms_media_files.filesize" => "associatedMedia",
		"ms_media_files.doi" => "dcterms:references",
		"ms_media_files.side" => "associatedMedia",
		"ms_media_files.element" => "associatedMedia",
		// "ms_media_file.published_on" => "N/A",
		"ms_media_files.download" => "associatedMedia",

		"ms_projects.abstract" => "dcterms:bibliographicCitation",
		"ms_projects.approval_status" => "dcterms:accessRights",
		//"ms_projects.created_on" => "N/A",
		"ms_projects.last_modified_on" => "dcterms:modified",
		"ms_projects.name" => "datasetName/dcterms:bibliographicCitation",
		"ms_projects.project_id" => "datasetID",
		//"ms_projects.published_on" => "N/A",
		"ms_projects.url" => "dcterms:references",
		
		"ms_taxonomy_names.author" => "scientificNameAuthorship",
		"ms_taxonomy_names.ht_kingdom" => "kingdom",
		"ms_taxonomy_names.ht_phylum" => "phylum",
		"ms_taxonomy_names.ht_class" => "class",
		"ms_taxonomy_names.ht_subclass" => "higherClassification",
		"ms_taxonomy_names.ht_superorder" => "higherClassification",
		"ms_taxonomy_names.ht_order" => "order",
		"ms_taxonomy_names.ht_suborder" => "higherClassification",
		"ms_taxonomy_names.ht_superfamily" => "higherClassification",
		"ms_taxonomy_names.ht_family" => "family",
		"ms_taxonomy_names.ht_subfamily" => "higherClassification",
		"ms_taxonomy_names.genus" => "genus",
		"ms_taxonomy_names.ht_supraspecific_clade" => "higherClassification",
		"ms_taxonomy.species" => "specificEpithet",
		"ms_taxonomy_names.subspecies" => "infraspecificEpithet",
		"ms_taxonomy_names.variety" => "infraspecificEpithet",
		"ms_taxonomy_names.year" => "namePublishedInYear",
		"ms_taxonomy.notes" => "taxonRemarks",
		"ms_taxonomy.is_primary" => "taxonomicStatus",
		//"ms_taxonomy.created_on" => "N/A",
		"ms_taxonomy_names.last_modified_on" => "dcterms:modified",
		"ms_taxonomy_names.review_status" => "taxonRemarks",
		"ms_taxonomy_names.review_notes" => "taxonRemarks",
		"ms_taxonomy_names.is_extinct" => "taxonRemarks",
		"ms_taxonomy_names.justification" => "taxonRemarks", 		

		"ms_scanners.facility_id" => "institutionID",
		"ms_scanners.make_model" => "institutionID",
		"ms_scanners.description" => "institutionID",

		"ms_institutions.name" => "institutionID",
		"ms_institutions.description" => "institutionID",
		"ms_institutions.city" => "institutionID",
		"ms_institutions.state" => "institutionID",
		"ms_institutions.county" => "institutionID",

		"ms_bibliography.reference_type" => "dcterms:references",
		"ms_bibliography.article_title" => "dcterms:references",
		"ms_bibliography.secondary_title" => "dcterms:references",
		"ms_bibliography.journal_title" => "dcterms:references",
		"ms_bibliography.monograph_title" => "dcterms:references",
		"ms_bibliography.authors" => "dcterms:references",
		"ms_bibliography.secondary_authors" => "dcterms:references",
		"ms_bibliography.editors" => "dcterms:references",
		"ms_bibliography.volume" => "dcterms:references",
		"ms_bibliography.publisher" => "dcterms:references",
		"ms_bibliography.publication_year" => "dcterms:references",
		"ms_bibliography.publication_place" => "dcterms:references",
		"ms_bibliography.abstract" => "dcterms:references",
		"ms_bibliography.description" => "dcterms:references",
		"ms_bibliography.collation" => "dcterms:references",
		"ms_bibliography.external_identifer" => "dcterms:references",
		"ms_bibliography.url" => "dcterms:references",
		"ms_bibliography.worktype" => "dcterms:references",
		"ms_bibliography.section" => "dcterms:references",
		"ms_bibliography.page_number" => "dcterms:references",
		"ms_bibliography.isbn" => "dcterms:references",
		"ms_bibliography.eletronic_resource_number" => "dcterms:references",
		"ms_bibliography.language" => "dcterms:references",
		"ms_bibliography.keywords" => "dcterms:references",
		//"ms_bibliography.created_on" => "N/A",
		"ms_bibliography.last_modified_on" => "dcterms:modified"
	];
