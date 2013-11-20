<div class="blueRule"><!-- empty --></div>
<div style="float:right; padding:5px 5px 5px 0px; width:185px; border:2px solid #EDEDED; margin:20px 0px 20px 20px; font-size:11px;">
	<div style="padding-left:10px;"><b>Jump to:</b></div>
	<ul style="padding:0px 0px 0px 30px; margin:0px;">
		<li><a href="#login" class="blueText">Logging in/Registering</a></li>
		<li><a href="#creatingProject" class="blueText">Creating a Project</a></li>
		<li><a href="#addSpecimen" class="blueText">Adding a Specimen</a></li>
		<li><a href="#addMedia" class="blueText">Adding Media</a></li>
		<li><a href="#dashboardFunctions" class="blueText">Other dashboard functions</a></li>
		<li><a href="#members" class="blueText">Managing Members</a></li>
		<li><a href="#searchingBrowsing" class="blueText">Searching and Browsing</a></li>
		<li><a href="#downloading" class="blueText">Downloading Media</a></li>
	</ul>
</div>
<h1>Morphosource Users Guide</h1>
<div class="textContent">
	<div>
		Morphosource is an image-sharing resource designed to allow users to upload, search, and browse high quality 3d images. This guide will provide instructions for registering, creating projects, uploading and publishing media, searching and browsing, and downloading media from other projects.
	</div>
	<div>
		<h2><a name="login"></a>Logging in/Registering</h2>
		To begin working with Morphosource, you must first register and create a login:
	</div>
	<div style="text-align:center;">
		<img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/morphosource/userGuide/header.png">
	</div>
	<div>
		Once you’ve entered your personal information, you can begin browsing and downloading published media. However, you will not be able to create your own projects and upload media until you’ve received permission from the Morphosource administrators. To request permission, click the blue button that says “Become a Contributor”:
	</div>
	<div style="text-align:center;">
		<img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/morphosource/userGuide/becomeContributor.png">
	</div>
	<div>
		You will be asked to say a few words about yourself and your interest in Morphosource, and then you will receive an email from the staff when you’re approved. Once you’ve been approved as a contributor, the button above will be replaced by one that reads “Create a Morphosource Project” and, if you are a member of any other projects, a project list. From this point forward, you will be free to search, browse, upload, and edit media in Morphosource. 
	</div>
	<div>
		<h2><a name="creatingProject"></a>Creating a Project</h2>
		To create a new project (to which you will be able to add images and specimen information), click “Create a Morphosource Project.” This will allow you to enter a title and abstract as a way of organizing your media. However, it is up to you whether you want to make these project details available to the public. If the title and abstract are simply for internal purposes, you can choose to keep the project information private using the drop-down at the bottom of the Project Information Screen:
	</div>
	<div style="text-align:center;">
		<img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/morphosource/userGuide/projectInformation.png">
	</div>
	<div>
		Once you’ve set up a project (or have been made a member of other projects), you will see your projects listed on the “Dashboard.” Click on the title of any project to see the individual “Dashboard” for that project. From within this project Dashboard, you can add new Specimens, Media, Bibliography, Taxonomy, and Facilities. If you are a Project Administrator, you can also edit Project Info and Manage Members from this screen. We’ll discuss each of these tools in more detail, but first let’s talk about how to add a Specimen and begin your work in earnest.
	</div>
	<div>
		<h2><a name="addSpecimen"></a>Adding a Specimen</h2>
		A typical project workflow will begin with Specimens. Although Morphosource is designed to feature media, these images will not be particularly useful without the context provided by specimen information. To create a new specimen, click the blue “New Specimen” button on the Project Dashboard.
	</div>
	<div style="text-align:center;">
		<img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/morphosource/userGuide/newSpecimen.png">
	</div>
	<div>
		First, you will be prompted to enter the catalog number for your specimen to see if it already exists in the database. If it does, you can simply select the correct number from your search results and you will immediately be directed to the Media Information/upload page. If it does not, you can click “create” and the Specimen Information fields will appear.
	</div>
	<div style="text-align:center;">
		<img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/morphosource/userGuide/specimenLookup.png">
	</div>
	<div>
		The Specimen information form includes many fields, but not all are required. If the specimen is vouchered, then you must, at minimum, include an institution code, catalog number, and institution. You will notice several fields, including the institution field, in which there is a downward-pointing arrow (just like in the Specimen search).
	</div>
	<div style="text-align:center;">
		<img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/morphosource/userGuide/lookupFields.png">
	</div>
	<div>
		That means that the field is actually a lookup, rather than a text field, in which typing the first few letters of an institution (or whatever the field requires) will return a list of matching results. This helps to ensure consistency across the database. If you type an institution’s name and there are no matching results, you will see text that says: 
	</div>
	<div style="text-align:center;">
		<img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/morphosource/userGuide/lookupFields2.png">
	</div>
	<div> 
		Click this text to reveal institution information fields. Once created, the new institution will become a list item available throughout the database. The same can be done with bibliographic references for body mass, absolute age, and relative age. Once you’ve saved the specimen, you will see two new lookups, for Taxonomy and general Bibliography. Similarly to institutions, taxa and bibliographic references are shared across projects so you may find that other users have already entered the information you need. If not, you can add it by clicking on the “does not exist. Create?” text mentioned above, and filling out the resultant fields. Taxonomy, Bibliography, and Institution information can also be added separately through the project dashboard, which we’ll discuss shortly.
	</div>
	<div>
		<h2><a name="addMedia"></a>Adding Media</h2>
		Once you’ve saved your specimen information, you will also have the option to Add Media. Simply click the “Add Media” button on the Specimen Information screen to begin the process. You’ll open the “Media Information” screen, which allows you to store any necessary image metadata. In order to save the media, you must at minimum upload a file, add a title, and indicate the facility at which the media file was created. It’s also advisable to enter information about the scanner used and the resolution, as well as entering copyright information. You should also enter citation information about the image so that other users will know the proper source. The citation information is formatted as a sentence in to which you can plug the relevant values:
	</div>
	<div style="text-align:center;">
		<img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/morphosource/userGuide/mediaCitationInstructions.png">
	</div>
	<div>
		Further copyright information, such as the appropriate creative commons license to use for the image, can be entered when the “is under copyright” box is checked.
	</div>
	<div>
		Once the file has been uploaded and successfully saved to the specimen, you will see a Project Media screen that displays the image along with metadata and further options. If you are the person who uploaded the media, you will see a row of buttons beneath the image that include “Download,” “Publish,” Edit Media,” “Clone Media,” and “Delete.” If you are a project member (not administrator) who did not upload the given file, you will see all of the above except for “Publish” and “Delete.” If the image is already published, you will not see the “Publish” options.
	</div>
	<div>
		You will also be able to retroactively add a specimen, taxonomic name, and bibliographic citation from lookups to the right of the screen if you have not already done this. Finally, if you are a Project Administrator, you have the option to move media from one project of which you are a member to another.
	</div>
	<div style="text-align:center;">
		<img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/morphosource/userGuide/mediaTools.png">
	</div>
	<div> 
		Some of the media tools bear further explanation, so let’s discuss.
		<ol>
			<li>Publish – of course, a file sharing database is more useful when the files are actually available. As a result, Publishing your media is an important step. You can publish one media file without publishing others in a project, and you can publish all of your media files without publishing other project information, if you so choose. The “Publish” button below your uploaded image will immediately publish the media, but you can take a more nuanced approach by selecting “Edit Info.” The “Publication status” drop-down that you will find there allows you to unpublish a previously published image, publish an image for search and download, or publish an image for search with a request-only download process. You can also publish Media (and other project information) from your project dashboard.</li>
			<li>Clone Media – This tool reproduces all of your media metadata and allows you to upload a new file. This is useful if you are uploading several images, and only need to change a few fields.</li>
			<li>Edit Media opens editable fields – these are all the same as in the initial Media Information editing screen, but the layout is slightly different.</li>
		</ol>
	</div>
	<div>
		<h2><a name="dashboardFunctions"></a>Other dashboard functions</h2>
		If at any point during the process you need to return to a basic overview of your project, you can click “Dashboard.” This screen will list all project members, number of media, number of downloads (if your images are published), the amount of storage used, the number of specimens, the number of citations, the creation date, and thumbnails of project media. This screen is also the other angle from which to approach Bibliography, Taxonomy, and Facilities (as mentioned under “Adding a Specimen”). Clicking on any of these buttons will provide a list of all citations, taxa, and scanning facilities associated with your project. If an entry in any of these categories is one that was created by your project or a project you have access to, you have the option to edit or delete. If, however, the entry was created through a project you do not have access to, it will not be editable.  Furthermore, you can not delete records used by other projects.  This is a function of Morphosource’s strength as a platform for sharing data – any taxon, citation, or institution entered by any user will become available to all other users. This helps to ensure consistency and efficiency throughout the database. 
	</div>
	<div style="text-align:center;">
		<img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/morphosource/userGuide/itemOwnership.png">
	</div>
	<div> 
		And, as previously mentioned, you can publish any unpublished media in one step from the project dashboard. Under “Number of Media” you will see a button that allows you to “Publish Unpublished Media” followed by the number of files in question. When you click that button, you will then have the chance to choose whether the affected files will be published with restricted download.
	</div>
	<div style="text-align:center;">
		<img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/morphosource/userGuide/publish.png">
	</div>
	<div>
		<h2><a name="members"></a>Managing Members</h2>
		There are different levels of membership in any project. A participant can simply be a project member, with the ability to upload and edit media and specimens, or project administrator, with the ability to manage other members.  To manage members, go to your project Dashboard and select “manage members.”
	</div>
	<div style="text-align:center;">
		<img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/morphosource/userGuide/manageMembersLink.png">
	</div>
	<div> 
		To add members, select the “Add Members” button at the top of the Manage Members screen and enter the email address(es) of  your invitees. You should also include a message along with the invitation, which will be sent to the new members’ inboxes. Before you send the invitation, you should also choose the appropriate membership type. An invitee can either have full membership, or read-only membership. A read-only member, of course, would not be able to edit project data or upload media.
	</div>
	<div style="text-align:center;">
		<img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/morphosource/userGuide/inviteMemberForm.png">
	</div>
	<div> 
		A project administrator can relinquish his or her duties to another project member by selecting the “Make Project Admin” button next to any member’s name.  You will also notice that there is a “Manage Downloads” button. This is used to give a project member the power to approve or deny user requests for download, which we’ll discuss at the end of this document. 
	</div>
	<div style="text-align:center;">
		<img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/morphosource/userGuide/projectMembersList.png">
	</div>
	<div>
		<h2><a name="searchingBrowsing"></a>Searching and Browsing</h2>
		All users, whether or not they have permission to upload media, have the ability to search and browse the database. One can browse by Institution, Taxonomy, Bibliography, or Project. Browsing by Project may yield fewer results, as project members have the option to keep project data private even as they make media and taxonomy public. To search, type your term in the search-bar at the top of the screen, and you will see two columns of results – Media that includes your search term, and specimens.
	</div>
	<div style="text-align:center;">
		<img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/morphosource/userGuide/search.png">
	</div>
	<div>
		<h2><a name="downloading"></a>Downloading Media</h2>
		When publishing media, you can choose to publish with the ability to download, OR you can choose to publish with the ability to download by request only. Remember that in order to select the “download by request” option, you must set publication status from within the “edit media” screen. If you simply click “publish,” it will automatically be free for download. When approval is required, users will see this button below the media thumbnail when they navigate to it from their search results:
	</div>
	<div style="text-align:center;">
		<img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/morphosource/userGuide/requestDownloadButton.png">
	</div>
	<div>
		Clicking this button will reveal a text box in which you can describe your request. An email will then be sent to project members who have “manage downloads” status (set in the Manage Members screen). The email notification will alert those members to the pending request, but in order to approve it they will have to go to their project dashboard where pending requests will be listed:
	</div>
	<div style="text-align:center;">
		<img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/morphosource/userGuide/pendingRequests.png">
	</div>
	<div>
		Morphosource is designed to allow great flexibility and specificity in what you choose to share. You always have the option of removing materials from public view, and adding new media to longstanding projects. For any further questions, please contact <a href="mailto:doug.boyer@duke.edu">doug.boyer@duke.edu</a>.
	</div>
</div><!-- end textContent -->