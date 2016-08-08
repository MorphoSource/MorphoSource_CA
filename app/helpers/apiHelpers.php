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
	function msInflectFieldNames($pa_field_names) {
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