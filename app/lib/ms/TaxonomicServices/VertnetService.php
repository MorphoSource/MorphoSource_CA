<?php
/** ---------------------------------------------------------------------
 * app/lib/mb/VertnetService.php.php.php : 
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
 * @subpackage Core
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
 *
 * ----------------------------------------------------------------------
 */
 
 /**
  *
  */
  
  require_once(__CA_LIB_DIR__.'/ms/BaseTaxonomicService.php');
	
class VertnetService extends BaseTaxonomicService {
	# -------------------------------------------------------------------
	/**
	 *
	 */
	private $ops_service_url = "http://api.vertnet-portal.appspot.com/api/search?q=";
	private $ops_request_url = '';
	# -------------------------------------------------------------------
	# Constructor
	# -------------------------------------------------------------------
	public function __construct() {	
		parent::__construct();
	}
	# -------------------------------------------------------------------
	/**
	 * Returns parameters for service request(s) initiated by the plugin
	 *
	 * @param string $pa_names A list of names to be sent to the web service for processing
	 * @return array A list of request arrays. Each array contains information about a service request. The keys 
	 *			in the array are:
	 *				url = The service URL
	 *				method = The type of HTTP request to use: GET or POST
	 *				data = An array of data to send, if the method is POST
	 *				service = The name of this service plugin
	 *				serviceName = The name of the service this plugin implements access to, for display to the user
	 */
	public function getQueryParams($pa_names, $pa_options=null) {
		$va_params = [];
		
		$vn_limit = caGetOption('limit', $pa_options, 1000);
		
		$vn_i = 1;
		while(sizeof($pa_names) > 0) {
			$va_buf = array_splice($pa_names, 0, (sizeof($pa_names) > 100) ? 100 : sizeof($pa_names));
			$va_names = array_map(function($v) { return (sizeof(explode(' ', $v)) > 1) ? "scientificname:\"{$v}\"" : "genus:\"{$v}\""; }, $va_buf);
			
			$va_params[] = array(
				'url' => $this->ops_request_url = $this->ops_service_url.urlencode(json_encode(["q" => join(" OR ", $va_names), "l" => $vn_limit])),
				'method' => 'GET',
				'data' => array(),
				'service' => 'VertnetService',
				'serviceName' => 'Global Names Resolver lookup',
				'name' => 'VertnetService'.$vn_i
			);
			$vn_i++;
		}
		
		return $va_params;
	}
	# -------------------------------------------------------------------
	/**
	 * Parse a raw service response into taxonomic units suitable for use the by application
	 *
	 * @param string $ps_response The text of the raw service response
	 * @return array An list of arrays, each containing information about a taxon
	 */
	public function parseResponse($ps_response) {
		$va_response = json_decode($ps_response, true);
		$va_taxa = array();
		
		$va_flds = ["kingdom", "phylum", "class", "order", "family", "genus", "specificepithet", "infraspecificepithet"];
		if(is_array($va_response['recs'])) {
			foreach($va_response['recs'] as $va_taxon_data) {
				$vs_taxon_name = $va_taxon_data['genus'].' '.$va_taxon_data['specificepithet'];
				if (isset($va_taxa[$vs_taxon_name])) { continue; }
				
				$va_taxa[$vs_taxon_name] = [
					'source_url' => $this->ops_request_url,
				];
				
				foreach($va_flds as $vs_fld) {
					$va_taxa[$vs_taxon_name][$vs_fld] = $va_taxon_data[$vs_fld];
				}
				
			}
		}
		return $va_taxa;
	}
	# -------------------------------------------------------------------
}