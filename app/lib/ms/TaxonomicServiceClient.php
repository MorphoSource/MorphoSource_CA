<?php
/** ---------------------------------------------------------------------
 * app/lib/core/TaxonomicServiceClient.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2011 Whirl-i-Gig
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
 * @subpackage Core
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
 *
 * ----------------------------------------------------------------------
 */
 
 /**
  *
  */
  
  require_once(__CA_LIB_DIR__.'/core/HTTPMultiClient.php');
	
class TaxonomicServiceClient {
	# -------------------------------------------------------------------
	/**
	 *
	 */
	private $opa_responses = array();
	
	static $s_plugin_names = null;
	static $s_plugin_cache = array();
	
	static $s_plugin_dir = __CA_LIB_DIR__.'/ms/TaxonomicServices';
	
	# -------------------------------------------------------------------
	# Constructor
	# -------------------------------------------------------------------
	public function __construct() {	
		
	}
	# -------------------------------------------------------------------
	/**
	 * 
	 */
	public function query($pa_names, $pa_options=null) {
		
		// Get plugins
		$va_plugins = $this->getPluginNames();
		
		// Call each plugin to get request params
		$va_requests = array();
		$va_plugin_instances = array();
		foreach($va_plugins as $vs_plugin_name) {
			$va_plugin_instances[$vs_plugin_name] = $o_plugin = $this->getPlugin($vs_plugin_name);
			$va_plugin_requests = $o_plugin->getQueryParams($pa_names, $pa_options);
			
			foreach($va_plugin_requests as $o_request) {
				$va_requests[] = $o_request;
			}
		}
	
		// Start HTTPMultiClient
		$o_client = new HTTPMultiClient($va_requests);
		$o_result = $o_client->execute();
		
		while(!$o_result->done()) {
			sleep(1);
		}
		
		// Call plugins to parse results
		$va_responses = $o_result->getAllResponses();
		
		$vn_i = 0;
		$va_taxa = array();
		
		foreach($va_responses as $vs_key => $va_response) {
			$va_taxa = array_merge($va_taxa, $va_plugin_instances[$va_response['service']]->parseResponse($va_response['response']));
			$vn_i++;
		}
		
		// Return results
		return $va_taxa;
	}
	# -------------------------------------------------------------------
	/**
	  *
	  */
	private function getPluginNames() {
		if (is_array(TaxonomicServiceClient::$s_plugin_names)) { return TaxonomicServiceClient::$s_plugin_names; }
		
		$o_config = Configuration::load();
		TaxonomicServiceClient::$s_plugin_names = array();
		$dir = opendir(TaxonomicServiceClient::$s_plugin_dir);
		while (($plugin = readdir($dir)) !== false) {
			if (preg_match("/^([A-Za-z_]+[A-Za-z0-9_]*).php$/", $plugin, $m)) {
				TaxonomicServiceClient::$s_plugin_names[] = $m[1];
			}
		}
		
		sort(TaxonomicServiceClient::$s_plugin_names);
		
		return TaxonomicServiceClient::$s_plugin_names;
	}
	# -------------------------------------------------------------------
	/**
	 *
	 */
	private function getPlugin($ps_plugin_name) {
		if (TaxonomicServiceClient::$s_plugin_cache[$ps_plugin_name]) { return TaxonomicServiceClient::$s_plugin_cache[$ps_plugin_name]; }
		$o_config = Configuration::load();
		
		# load the plugin
		require_once(TaxonomicServiceClient::$s_plugin_dir."/{$ps_plugin_name}.php");
		$p = new $ps_plugin_name();
		return TaxonomicServiceClient::$s_plugin_cache[$ps_plugin_name] = $p;
	}
	# -------------------------------------------------------------------
}