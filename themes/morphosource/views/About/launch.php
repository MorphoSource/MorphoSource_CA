<div class="blueRule"><!-- empty --></div>
<div style="float:right; padding:5px 5px 5px 0px; width:185px; border:2px solid #EDEDED; margin:20px 0px 20px 20px; font-size:11px;">
	<div style="padding-left:10px;"><b>Jump to:</b></div>
	<ul style="padding:0px 0px 0px 30px; margin:0px;">
		<li><a href="#dates" class="blueText">Important Dates</a></li>
		<li><a href="#contributor" class="blueText">Contributor Action Item</a></li>
		<li><a href="#services" class="blueText">Temporarily Unavailable Services</a></li>
		<li><a href="#features" class="blueText">New Feature Highlights</a></li>
	</ul>
</div>
<h1>MorphoSource 2.0 Launch Information</h1>
<div class="textContent">
	<div>
		This page contains details and further explanation for the forthcoming launch of MorphoSource 2.0. This is a significant expansion and improvement of the MorphoSource software platform, and has been in progress for the last three years, with support from NSF (DBI-1661386) and the Duke University Libraries.
	</div>

	<div>
		<h2 class="tealBottomRule"><a name="dates"></a>Important Dates</h2>

		<div>
			<b>September 23rd:</b> No new batch upload users will be added after this date.
		</div>

		<div>
			<b>October 9th:</b> MorphoSource will be view-only. This means downloads, uploads, and metadata editing will become unavailable. Batch upload SFTP home directories will become permanently inaccessible for non-oVert batch upload users.
		</div>

		<div>
			<b>Week of October 19th (hopefully Monday):</b> MorphoSource 2.0 will launch!
		</div>
	</div>

	<div>
		<h2 class="tealBottomRule"><a name="contributor"></a>Contributor Action Item</h2>

		<div>
			In MorphoSource 1.0, media data files have their access controls set by projects. In MorphoSource 2.0, media can have access controls set secondarily by projects, but they must primarily be controlled by a user. Having "primary access/control" means to have overriding ability to modify access and/or control by other users and projects. When we launch MorphoSource 2.0, media will continue to be located in the same projects as in MorphoSource 1.0, and these projects will continue to have the same members. You will still have access to all of the same projects you could access previously, and the members of these projects will continue to have the same user status (administrator, full-access, non-administrator, or read-only) that they had before. 
		</div>

		<div>
			When MS 2.0 launches, the administrator of each data project will be designated as the user with primary control/access over all media in that project. The media will continue to exist in your projects, and all project members will have the same access and control over the media that they had in MS 1.0. But the MS 1.0 project administrator (as of migration) will become the primary user associated with those media,  and they will have final, ultimate control of the media. This only applies to the MS 2.0 launch, moving forward the primary user for media will be either the uploading user or a specific user designated on upload.
		</div>

		<div>
			Prior to launch, you should review your contributed data projects on MorphoSource, and be sure that the user selected as project administrator is the most appropriate person to receive individual-level control over the media in that project.
		</div>

		<div>
			If you encounter problems changing administrator status, please <a href='mailto:info@morphosource.org'>contact us</a>. Provide us with a list of projects to be changed, with the full name and corresponding number of each project and the email address for the user that should be made the administrator for each project. If we receive requests for administrator changes from users not associated with a project, we may need some kind of referral from another user that is associated.
		</div>
	</div>

	<div>
		<h2 class="tealBottomRule"><a name="services"></a>Temporarily Unavailable Services</h2>

		<div>
			Some MorphoSource services will be unavailable after launch on October 19th. We intend to have these services available again as soon as possible. At the most, we estimate these may be down until the end of the calendar year. These services are below.
		</div>

		<div>
			<i>* For users of services marked with an asterisk who rely on these services as part of their standard workflow(s), we have interim solutions to bridge the gap. Please <a href='mailto:info@morphosource.org'>contact us</a> to discuss this further.</i>
		</div>

		<div>
			<ul>
				<li>Morphosource Metadata Query API*</li>
				<li>RSS iDigBio Data Reporting*</li>
				<li>Batch Editing of Metadata and Publication Status</li>
				<li>
					Batch Uploading
					<ul>
						<li>Starting September 23rd, we will no longer be adding new batch upload users.</li>
						<li>Non-oVert batch upload users will lose SFTP access on October 9th, and any files not ingested at this point will be lost.</li>
						<li>oVert batch upload users can continue to deposit files via SFTP. See oVert BaseCamp for further instructions.</li>
						<li>When batch uploading returns, the ingest manifest will be different. We will publish details on manifest changes soon.</li>
						<li>All users who temporarily lose batch upload privileges during the transition will have them restored.</li>
					</ul>
				</li>
			</ul>
		</div>
	</div>

	<div>
		<h2 class="tealBottomRule"><a name="features"></a>New Feature Highlights</h2>

		<div><b>3D Previews</b></div>

		<ul>
			<li>Browser previews of color/textured meshes (e.g., photogrammetry and structured light models)</li>
			<li>Browser previews of 3D volumes (e.g., TIFF and DICOM data uploads), with ability to scroll through slices and adjust contrast</li>
			<li>Browser previews are embeddable and have early versions of measurement tools</li>
		</ul>

		<div><b>Data Submission</b></div>

		<ul>
			<li>More streamlined data submission tools to help ensure better quality of resulting records</li>
			<li>Automated parsing of key file-level metadata from uploaded files (saving users time not having to manually enter this metadata)</li>
			<li>More nuanced metadata that anticipates diverse workflows and imaging modalities</li>
			<li>Metadata support for photogrammetry, structured light and laser scanning workflows</li>
		</ul>

		<div><b>Data Discovery, Access, and Management</b></div>

		<ul>
			<li>More powerful faceted searching on things like data modality, specimen type, etc.</li>
			<li>It is possible to request download access to multiple media at once using the media cart</li>
			<li>For download access reviewers, more flexibility, including:
			<ul>
				<li>Ability to review previously granted or denied requests to change their status (approved/denied/cleared) or modify access time length</li>
				<li>Ability to set customized length of time media is accessible</li>
			</ul>
			<li>Projects and teams can have multiple administrators, and multiple other sets of users with customized access permissions</li>
			<li>More detailed and flexible specification of data use restrictions and more tailorable downloader agreement form</li>
		</ul>

		<div><b>User Groups</b></div>

		<ul>
			<li>User Teams: A group of users that can contain media (like a project) but can also contain multiple team projects</li>
			<ul>
				<li>Users who are members of a team have access settings similar to those of a project, and these access settings propogate to nested team projects</li>
				<li>Example User Team: A collaborative grant with several sub-groups, where each sub-group uploads their individual data to a single team project, but the team has access to all projects</li>
			</ul>
			<li>Organization-Linked Teams: A user team where the team members are official representatives of a museum collection or other organization, and want deeper access and control over media associated with specimens from their organization</li>
			<ul>
				<li>Members of organization-linked team have view access to all media representing specimens from their organization (e.g., museum collection, laboratory holdings, etc.)</li>
				<li>Members of organization-linked teams can specify default licensing and use restriction settings that are automatically suggested for any new media uploaded representing an organizational specimen by any user</li>
			</ul>
		</ul>

		<div><b>Data Integrity and Standards</b></div>

		<div>
			<u>Digital Preservation:</u> A primary reason for the refactor was to upgrade MorphoSource from a custom-written LAMP stack web application to a digital preservation stack. MorphoSource 2.0 is a Rails-based customized Hyrax instance. <a href='https://hyrax.samvera.org/'>Hyrax</a> is an open source platform widely adopted by digital repositories around the world and thus has an active community of developers maintaining and advancing it. At launch, MorphoSource 2.0 will use Fedora as the digital asset management layer and Solr for efficient querying at scale, allowing for steady future repository growth.
		</div>
		
		<div>
			<u>IIIF and Universal Viewer:</u> The <a href='https://iiif.io/'>International Image Interoperability Framework</a> (IIIF) is an API and set of standards for enabling standardized display and transmission of documents and media on the web and in other domains. <a href='http://universalviewer.io/'>Universal Viewer</a> (UV) is a popular open source browser viewer that relies on IIIF, and is supported by Hyrax by default. MorphoSource has worked with Ed Silverton of <a href='https://mnemoscene.io/'>Mnemoscene</a> to create <a href='https://github.com/aleph-viewer/aleph'>Aleph</a>, an extension to the UV to enable 3D volume viewing, 3D annotations and measurements, and more nuanced viewing of meshes, including colored/textured surface meshes. Aleph is built on an <a href='https://aframe.io/'>A-Frame</a> foundation, and so will soon be VR-capable. As Aleph and Universal Viewer are both open source, this technology is available for use by other teams and other Hyrax repositories in particular. We intend to continue collaboratively developing this viewer for the benefit of MorphoSource users and the larger community.			
		</div>
	</div>
</div>