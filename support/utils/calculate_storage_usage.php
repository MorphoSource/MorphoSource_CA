#!/usr/local/bin/php
<?php
	ini_set('memory_limit', '4000m');
	set_time_limit(24 * 60 * 60 * 7); /* maximum indexing time: 7 days :-) */
	
	if(!file_exists('./setup.php')) {
		die("ERROR: Can't load setup.php. Please create the file in the same directory as this script or create a symbolic link to the one in your web root.\n");
	}
	
	require_once("./setup.php");
	
	require_once(__CA_MODELS_DIR__."/ms_projects.php");
	require_once(__CA_LIB_DIR__."/core/Db.php");
	
	$va_storage_allocations = array();
	
	$o_db = new Db();
	
	$qr_res = $o_db->query("
		SELECT * FROM ms_media
	");
	
	while($qr_res->nextRow()) {
		$va_versions = $qr_res->getMediaVersions("media");
		
		$vn_alloc = 0;
		foreach($va_versions as $vs_version) {
			$va_info = $qr_res->getMediaInfo("media", $vs_version);
			$vn_alloc += $va_info['PROPERTIES']['filesize'];
		}
		
		$va_storage_allocations[$qr_res->get('project_id')] += $vn_alloc;
	}
	//print_R($va_storage_allocations);
	foreach($va_storage_allocations as $vn_project_id => $vn_alloc) {
		$t_project = new ms_projects($vn_project_id);
		$t_project->setMode(ACCESS_WRITE);
		$t_project->set('total_storage_allocation', $vn_alloc);
		$t_project->update();
		if($t_project->numErrors()) {
			print "Error: " . join("; ", $t_project->getErrors())."\n";
		}
	}
?>