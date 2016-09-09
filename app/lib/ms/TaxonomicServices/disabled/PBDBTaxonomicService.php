<?php
/** ---------------------------------------------------------------------
 * app/lib/mb/PBDBTaxonomicService.php : 
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
	require_once(__CA_LIB_DIR__.'/mb/BaseTaxonomicService.php');
	
	class PBDBTaxonomicService extends BaseTaxonomicService {
		# -------------------------------------------------------------------
		/**
		 *
		 */
		private $ops_service_url = "https://paleobiodb.org/data1.2/taxa/single.txt?name=";
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
		 * @param string $ps_query The query to be sent to the web service for processing
		 * @return array A list of request arrays. Each array contains information about a service request. The keys 
		 *			in the array are:
		 *				url = The service URL
		 *				method = The type of HTTP request to use: GET or POST
		 *				data = An array of data to send, if the method is POST
		 *				service = The name of this service plugin
		 *				serviceName = The name of the service this plugin implements access to, for display to the user
		 */
		public function getQueryParams($ps_query) {
			$va_params = array(
				'url' => $this->ops_service_url.urlencode($ps_query).'*',
				'method' => 'GET',
				'data' => array(),
				'service' => 'PBDBTaxonomicService',
				'serviceName' => 'Paleobiology Database Taxonomic lookup',
				'name' => 'PBDBTaxonomicService'
			);
		
			return array($va_params);
		}
		# -------------------------------------------------------------------
		/**
		 * Parse a raw service response into taxonomic units suitable for use the by application
		 *
		 * @param string $ps_response The text of the raw service response
		 * @return array An list of arrays, each containing information about a taxon
		 */
		public function parseResponse($ps_response) {
			if(!($o_xml = @simplexml_load_string($ps_response))) { return array(); }
			$va_taxa = array();
		
			foreach($o_xml->result as $o_result) {
				$va_taxon = array(
					'name' => (string)$o_result->name,
					'source' => (string)$o_result->source_database,
					'source_url' => (string)$o_result->url,
					'author' => (string)$o_result->author,
					'genus' => (string)$o_result->genus,
					'species' => (string)$o_result->species
				);
			
				if ($o_result->classification->taxon) {
					foreach($o_result->classification->taxon as $o_taxon) {
						$va_taxon[strtolower((string)$o_taxon->rank)] = (string)$o_taxon->name;
					}
				}
				$va_taxa[] = $va_taxon;
			}
		
			return $va_taxa;
		}
		# -------------------------------------------------------------------
	}