<?php
/** ---------------------------------------------------------------------
 * app/lib/mb/GlobalNamesResolverService.php.php : 
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
	
class GlobalNamesResolverService extends BaseTaxonomicService {
	# -------------------------------------------------------------------
	/**
	 *
	 */
	private $ops_service_url = "http://resolver.globalnames.org/name_resolvers.json?names=";
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
	public function getQueryParams($pa_names) {
		$va_params = [];
		
		$vn_i = 1;
		while(sizeof($pa_names) > 0) {
			$va_buf = array_splice($pa_names, 0, (sizeof($pa_names) > 100) ? 100 : sizeof($pa_names));
			$va_params[] = array(
				'url' => $this->ops_request_url = $this->ops_service_url.urlencode(join("|", $va_buf)),
				'method' => 'GET',
				'data' => array(),
				'service' => 'GlobalNamesResolverService',
				'serviceName' => 'Global Names Resolver lookup',
				'name' => 'GlobalNamesResolverService'.$vn_i
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
		
		if(is_array($va_response['data'])) {
			foreach($va_response['data'] as $va_taxon_data) {
				$vs_taxon_name = $va_taxon_data['supplied_name_string'];
				
				$va_taxa[$vs_taxon_name] = [
					'source_url' => $this->ops_request_url,
				];
				if (!is_array($va_taxon_data['results'])) { continue; }
				
				// reorder
				$va_results = [];
				
				$vn_c = 0;
				foreach($va_taxon_data['results'] as $va_result) {
					switch($vs_source = $va_result['data_source_title']) {
						case 'NCBI':
							//$va_results[80] = $va_result;
							break;
						case 'Freebase':
							//$va_results[3] = $va_result;
							break;
						case 'GBIF Backbone Taxonomy':
							//$va_results[40] = $va_result;
							break;
						case 'ITIS':
							//$va_results[500] = $va_result;
							break;
						case 'EOL':
							//$va_results[1] = $va_result;
							break;
						case 'The Paleobiology Database':
							$va_results[100] = $va_result;
							break;
						default:
							//$va_results[1000 + $vn_c] = $va_result;
							$vn_c++;
							break;
							//print "OTHER [$vs_source]\n";
					}
				}
				ksort($va_results);
				
				$va_results_prioritized = array_values($va_results);
				
				foreach($va_results_prioritized as $va_result) {
					if (
						(!str_replace("|", "", $va_result['classification_path']))
						||
						(!str_replace("|", "", $va_result['classification_path_ranks']))
					) {
						continue;
					}
					
					$va_ranks = explode("|", strtolower($va_result['classification_path_ranks']));
					$va_taxon_elements = explode("|", $va_result['classification_path']);
					
					$vb_isset = false;
					foreach($va_ranks as $vn_i => $vs_rank) {
						if(!$vs_rank) continue;
						if (!isset($va_taxa[$vs_rank])) { $va_taxa[$vs_taxon_name][$vs_rank] = $va_taxon_elements[$vn_i]; $vb_isset = true; }
					}
					$va_taxa[$vs_taxon_name]['source'] = $va_result['data_source_title'];
					
					if ($vb_isset) { 
						print "USED ".$va_result['data_source_title']."\n";
						break; 
					}
				}
				
				if ($va_taxa[$vs_taxon_name]['genus'] || $va_taxa[$vs_taxon_name]['species']) {
					$va_taxa[$vs_taxon_name]['name'] = trim($va_taxa[$vs_taxon_name]['genus'].' '.$va_taxa[$vs_taxon_name]['species']);
				}
				
			}
		}
		return $va_taxa;
	}
	# -------------------------------------------------------------------
}