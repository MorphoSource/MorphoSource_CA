<?php
	$t_user = $this->getVar('user');
	$t_specimen = $this->getVar('specimen');
	$t_project = $this->getVar('project');
	$o_request = $this->getVar('request');
	if (!($vs_url_root = $o_request->config->get('ca_url_root'))) { $vs_url_root = '/'; }
	$vs_specimen_url = $o_request->config->get('site_host').$vs_url_root.caNavUrl($o_request, 'Detail', 'SpecimenDetail', 'Show', array('specimen_id' => $t_specimen->getPrimaryKey()));

?>
<p>A specimen record was imported from iDigbio.org by <?php print $t_user->get("fname")." ".$t_user->get("lname")." (".$t_user->get("email").")"; ?>.</p>

<p>
	<b>Specimen:</b> <?php print "<a href='".$vs_specimen_url."'>".$t_specimen->getSpecimenName(); ?></a><br/>
	<b>Project:</b> <?php print $t_project->get("name"); ?>
</p>