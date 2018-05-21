<?php 
	$va_item_array = $this->getVar("item_array");
?>

<div>
	<div>
		<h1>Data reporting for MorphoSource media</h1>
		<p>MorphoSource provides summary reports of media, download usage, and download requests for media that represent specimens that have been reported to <a href='https://www.idigbio.org'>iDigBio</a>. The media report is formatted according to the Audubon Core metadata standard, and so can be incorporated into publisher reporting software, such as an IPT. All report files are linked and described in a single RSS feed, which can be used to receive regular report updates via automated download. Additionally, all report files are individually listed below, sorted by iDigBio publisher and recordset. For each report, there is a link to: <b>1)</b> a Comma Separated Values (CSV) spreadsheet with the primary media, download usage, or download request metadata; and <b>2)</b> an XML file encoded in Ecological Markup Language (EML) providing metadata about the CSV spreadsheet. These reports are updated as necessary on a daily basis.</p>
	</div>
	<div>
		<b>RSS Feed: </b><a href='https://www.morphosource.org/rss/ms.rss'>https://www.morphosource.org/rss/ms.rss</a>
	</div>
	<div class='dataReportDisclaimer'>
		<p><b>IMPORTANT</b></p> 
		<p>Museum curators and others who intend to use these resources to integrate MorphoSource media information into specimen collection record systems should be aware that occasional changes may be made to the data schema used in these RSS and CSV files. Data managers should also understand that potential negative repercussions on collections data could occur if care is not taken to be aware of data schema changes. Interested individuals can send an e-mail to <a href="mailto:info@morphosource.org">info@morphosource.org</a> to be added to a mailing list that will inform users in advance of upcoming changes to these resources. Changes to data resources are also described at the bottom of this page.</p>
	</div>
	<div style='margin-top: 30px;'>
<?php
		// print "<h1>Data publishers</h1>";
		foreach ($va_item_array as $va_pub) {
			print "<div>";
				print "<h2>".($va_pub["name"] ? $va_pub["name"] : "No publisher name available")." (".$va_pub['id'].")</h2>";
			print "</div>";
			print "<div>";
				print "<table class='listtable'>";
				print "<thead>";
				print "<tr>";
				print "<th class='list-header-unsorted' style='width: 55%;'>Recordset</td>";
				print "<th class='list-header-unsorted' style='width: 10%;'>Media</td>";
				print "<th class='list-header-unsorted' style='width: 10%;'>Downloads</td>";
				print "<th class='list-header-unsorted' style='width: 10%;'>Download Requests</td>";
				print "<th class='list-header-unsorted' style='width: 15%;'>Pub Date</td>";
				print "</tr>";
				print "</thead>";
				foreach ($va_pub['recordsets'] as $va_recordset) {
					print "<tr>";
					print "<td><a href='https://www.idigbio.org/portal/recordsets/".$va_recordset['id']."' target='_blank'>".($va_recordset['name'] ? $va_recordset['name']: 'No recordset name available')." (".$va_recordset['id'].")</a></td>";
					print "<td><a href='".$va_recordset['ms.csv']."'>CSV</a></br><a href='".$va_recordset['ms.xml']."'>EML</a></td>";
					print "<td><a href='".$va_recordset['dl.csv']."'>CSV</a></br><a href='".$va_recordset['dl.xml']."'>EML</a></td>";
					print "<td><a href='".$va_recordset['dl_request.csv']."'>CSV</a></br><a href='".$va_recordset['dl_request.xml']."'>EML</a></td>";
					print "<td>".$va_recordset['pubTime']."</td>";
					print "</tr>";
				}
				print "</table>";
			print "</div>";
		}
?>
	</div>
	<div>
		<h1>Recent changes to data reports</h1>
		<div>
			<p><b>To to be implemented soon</b></p>
			<ul>
				<li>RSS Feed: File name has been changed from ms.rss to ms_rss.xml.</li>
				<li>RSS Feed: Feed formatting errors have been fixed. Item elements now nest properly in channel elements.</li>
				<li>Audubon Core Media CSV: Column names have been changed to exclude the "ac:" namespace and other namespaces that should be automatically recognized by IPT software. Automapping of columns should be easier now. </li>
				<li>Audubon Core Media CSV: An Audubon Core field "IDofContainingCollection" has been added to record media group number per media file.</li>
				<li>RSS, Audubon Core Media EML: Recordset names have been added to title tags.</li>
			</ul>
		</div>
	</div>
</div>