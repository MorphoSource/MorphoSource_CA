<?php
/** ---------------------------------------------------------------------
 * app/lib/mb/uBioTaxonomicService.php : 
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
	
	class uBioTaxonomicService extends BaseTaxonomicService {
		# -------------------------------------------------------------------
		/**
		 *
		 */
		private $ops_service_url = "http://www.ubio.org/webservices/service.php?function=namebank_search&sci=1&vern=1";
		private $ops_query = null;
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
			$o_config = Configuration::load();
			$vs_ubio_keycode = trim($o_config->get("ubio_keycode"));
			$va_params = array(
				'url' => $this->ops_service_url."&keyCode={$vs_ubio_keycode}&searchName=".urlencode(trim($ps_query)),
				'method' => 'GET',
				'data' => array(),
				'service' => 'uBioTaxonomicService',
				'serviceName' => 'uBio Taxonomic lookup',
				'name' => 'uBioTaxonomicService'
			);
		
			$this->ops_query = $ps_query;
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
			if(!($o_xml = simplexml_load_string($ps_response))) { return array(); }
			$va_taxa = array();
			if (!$o_xml->scientificNames->value) { return array(); }
			foreach($o_xml->scientificNames->value as $o_result) {
			
				if ($o_result->rankName) {
					$vs_rankname = strtolower((string)$o_result->rankName);
					switch($vs_rankname) {
						case 'sub-species':
							$vs_rankname = 'subspecies';
							break;
						case 'ssp.':
							$vs_rankname = 'order';
							break;
						case 'trinomial':
						case 'form':
							$vs_rankname = 'family';
							break;
					}
				
					$va_taxa[(int)$o_result->packageID][$vs_rankname] = (string)$o_result->packageName;
				}
			}
		
			foreach($va_taxa as $vn_i => $va_taxon) {
				$va_taxa[$vn_i]['source'] = 'uBio';
				$va_taxa[$vn_i]['source_url'] = $this->ops_service_url.'&searchName='.$this->ops_query;
				$va_taxa[$vn_i]['name'] = join(" ", $va_taxa[$vn_i]);
			}
		
			return array_values($va_taxa);
		}
		# -------------------------------------------------------------------
	}