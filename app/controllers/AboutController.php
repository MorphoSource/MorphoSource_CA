<?php
/* ----------------------------------------------------------------------
 * includes/AboutController.php
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2010 Whirl-i-Gig
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
 
 	class AboutController extends ActionController {
 		# -------------------------------------------------------
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 			parent::__construct($po_request, $po_response, $pa_view_paths);
 			if (($this->request->config->get('pawtucket_requires_login') && !($this->request->isLoggedIn()))||($this->request->config->get('show_bristol_only'))&&!($this->request->isLoggedIn())) {
                 $this->response->setRedirect(caNavUrl($this->request, "", "LoginReg", "form"));
            }
 		}
 		# -------------------------------------------------------
 		/**
 		 * Generic action handler - used for any action that is not implemented
 		 * in the controller
 		 */
 		public function __call($ps_name, $pa_arguments) {
 			$ps_name = preg_replace('![^A-Za-z0-9_\-]!', '', $ps_name);
 			return $this->render('About/'.$ps_name.'.php');
 		}
 		# -------------------------------------------------------
 		public function report() {
 			$vs_sep = DIRECTORY_SEPARATOR;
 			$va_item_array = [];
 			$vs_rootpath = 'rss';
 			$va_blacklist = ['.', '..', 'ms.rss']; 			
 			foreach (scandir('rss') as $p) {
 				$vs_pub_dir = $vs_rootpath . DIRECTORY_SEPARATOR . $p;
 				if (!in_array($p, $va_blacklist) && is_dir($vs_pub_dir)) {
 					$va_item_array[$p] = [
 						'id' => $p,
 						'name' => '',
 						'recordsets' => []
 					];

 					foreach (scandir($vs_pub_dir) as $r) {
 						$vs_eml_dir = $vs_pub_dir . $vs_sep . $r . $vs_sep . 'eml';
 						if (!in_array($r, $va_blacklist) && is_dir($vs_eml_dir)) {
 							$va_item_array[$p]['recordsets'][$r] = [
 								'id' => $r,
 								'name' => '',
 								'pubTime' => null,
 							];

 							foreach (scandir($vs_eml_dir) as $e) {
 								if (!in_array($e, $va_blacklist) && strpos($e, '.xml')) {
 									$vo_xml = simplexml_load_file($vs_eml_dir . $vs_sep . $e);
 									$vs_csv_link = (string)$vo_xml->dataset->online;
 									$vs_eml_link = (string)$vo_xml->dataset->alternateIdentifier;
 									$vs_pub_time = (string)$vo_xml->additionalMetadata->metadata->morphosource->pubTime;
 									$vs_r_name = (string)$vo_xml->additionalMetadata->metadata->idigbio->recordset->name;
 									$vs_p_name = (string)$vo_xml->additionalMetadata->metadata->idigbio->publisher->name;
 									$va_item_array[$p]['recordsets'][$r][basename($vs_csv_link)] = $vs_csv_link;
 									$va_item_array[$p]['recordsets'][$r][basename($vs_eml_link)] = $vs_eml_link;
 									$va_item_array[$p]['recordsets'][$r]['pubTime'] = $vs_pub_time;
 									$va_item_array[$p]['recordsets'][$r]['name'] = $vs_r_name;
 									$va_item_array[$p]['name'] = $vs_p_name;
 								}
 							}

 							usort($va_item_array[$p]['recordsets'], function ($a, $b) {
    							return strcmp(strtolower($a['name']), strtolower($b['name']));
							});
 						}
 					}
 				}
 			}

 			usort($va_item_array, function ($a, $b) {
    			return strcmp(strtolower($a['name']), strtolower($b['name']));
			});

 			$this->view->setVar('item_array', $va_item_array);

 			$this->render('About/report_html.php');
 		}
 	}
 ?>
