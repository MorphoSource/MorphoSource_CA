<?php
/* ----------------------------------------------------------------------
 * controllers/SplashController.php
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2011 Whirl-i-Gig
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
 	require_once(__CA_APP_DIR__.'/helpers/accessHelpers.php');
 	require_once(__CA_MODELS_DIR__."/ms_projects.php");
 
 	class SplashController extends ActionController {
 		# -------------------------------------------------------
 		 
 		
 		protected $ops_find_type = 'basic_browse';
 		# -------------------------------------------------------
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 			parent::__construct($po_request, $po_response, $pa_view_paths);
 			
 			// redirect user if not logged in
			if (($this->request->config->get('pawtucket_requires_login')&&!($this->request->isLoggedIn()))||($this->request->config->get('show_bristol_only')&&!($this->request->isLoggedIn()))) {
                $this->response->setRedirect(caNavUrl($this->request, "", "LoginReg", "form"));
            } elseif (($this->request->config->get('show_bristol_only'))&&($this->request->isLoggedIn())) {
            	$this->response->setRedirect(caNavUrl($this->request, "bristol", "Show", "Index"));
            }
 		}
 		# -------------------------------------------------------
 		function Index($pa_options=null) {
 			// Remove any browse criteria previously set
			JavascriptLoadManager::register('imageScroller');
 			JavascriptLoadManager::register('browsable');
 			JavascriptLoadManager::register('tabUI');
 			JavascriptLoadManager::register('cycle');
 			$this->render('Splash/splash_html.php');
 		}
 		# -------------------------------------------------------
 	}
 ?>