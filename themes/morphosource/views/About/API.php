<div class="blueRule"><!-- empty --></div>
<div style="float:right; padding:5px 5px 5px 0px; width:185px; border:2px solid #EDEDED; margin:20px 0px 20px 20px; font-size:11px;">
	<div style="padding-left:10px;"><b>Jump to:</b></div>
	<ul style="padding:0px 0px 0px 30px; margin:0px;">
		<li><a href="#using" class="blueText">Using the API</a></li>
		<li><a href="#find" class="blueText">Locating and retrieving resources</a></li>
		<li><a href="#response" class="blueText">The API response</a></li>
		<li><a href="#adv_queries" class="blueText">Advanced queries</a></li>
		<li><a href="#adv_queries" class="blueText">Sorting</a></li>
		<li><a href="#naming" class="blueText">Field Names</a></li>
		<li><a href="#data_dict" class="blueText">Data Dictionary</a>
			<ul style="padding:0px 0px 0px 30px; margin:0px;">
				<li><a href="#dict_specimen" class="blueText">Specimens</a></li>
				<li><a href="#dict_facility" class="blueText">Facilities</a></li>
				<li><a href="#dict_media" class="blueText">Media</a></li>
				<li><a href="#dict_media_file" class="blueText">Media Files</a></li>
				<li><a href="#dict_taxonomy" class="blueText">Taxonomy</a></li>
				<li><a href="#dict_scanner" class="blueText">Facility Scanners</a></li>
				<li><a href="#dict_institution" class="blueText">Institutions</a></li>
				<li><a href="#dict_bibliography" class="blueText">Bibliography</a></li>
			</ul>
		</li>
	</ul>
</div>
<h1>MorphoSource API</h1>
<div class="textContent api">
	<div>
		The MorphoSource public API supports HTTP GET search requests for published resources. Five types of resources are currently available via the API:
		
		<br/><br/><table class="docTable">
			<thead><tr><th>API Resources</th></tr></thead>
			<tbody>
				<tr>
					<td>Specimens</td>
				</tr>
				<tr>
					<td>Media</td>
				</tr>
				<tr>
					<td>Taxonomy</td>
				</tr>
				<tr>
					<td>Projects</td>
				</tr>
				<tr>
					<td>Facilities</td>
				</tr>
			</tbody>
		</table>
		<br/>
		<br/>The API is a RESTful web service that delivers data as JSON documents. All GET requests must be URL-encoded.
	</div>
	
	<div>
		<h2 class="tealBottomRule"><a name="using"></a>Using the API</h2>
		
		All API functions are accessed using URLs in the form
		
		<code><pre>http://www.morphosource.org/api/v1/[command]/[resource type]?[url-encoded parameter list]</pre></code>
		
		where <code>[command]</code> is an API function and <code>[resource type]</code> is one of the following: specimens, media, taxonomy, projects, facilities. <code>[url-encoded parameter list]</code> is an HTTP <a href="https://en.wikipedia.org/wiki/Query_string">query string</a> containing command options.
	</div>
	
	<div>
		The API presently includes a single command, <em>find</em>, that performs full-text and field-qualified searches on the MorphoSource repository and returns comprehensive resource data.
	</div>
	
	<div>
		<h2 class="tealBottomRule"><a name="find"></a>Locating and retrieving resources</h2>
		
		The API <em>Find</em> command is a general purpose tool for locating and retriving a variety of resources from the MorphoSource repository. The <a href="#query">search syntax</a> supports both full text and field-qualifiedsearch, wildcards and boolean combinations, allowing queries to be as broad or specific as needed. Results may be ordered by any field present in the response.
	</div>
	<div>
		The <em>find</em> command takes the following parameters:
		
		<table class="docTable">
			<thead><tr><th>Option</th><th>Description</th></tr></thead>
			<tbody>
				<tr>
					<td>q</td>
					<td>The query</td>
				</tr>
				<tr>
					<td>sort</td>
					<td>A semicolon delimited list of fields to order the results on. For example: <em>project.name;specimen.specimen_id</em></td>
				</tr>
				<tr>
					<td>start</td>
					<td>The index of the resource at which to begin returning results. Default is zero (the first resource in the result set)</td>
				</tr>
				<tr>
					<td>limit</td>
					<td>Maximum number of records to return. Must be between 1 and 100. Default is 100</td>
				</tr>
				<tr>
					<td>naming</td>
					<td>Format to use for field names. When set to <em>morphosource</em> Morphosource field names are used. When set to <em>darwincore</em> DarwinCore field names are used whenever possible. If omitted Morphosource field names are used by default.</td>
				</tr>
			</tbody>
			
		</table>
	</div>
	<div>
		For example, to find all specimens where the word "molar" is used the API URL is:
		
		<code><pre>http://www.morphosource.org/api/v1/find/specimens?q=molar</pre></code>
		
		This will return the first 100 results. To find the second set of 100 use the <em>start</em> parameter:
		
<code><pre>http://www.morphosource.org/api/v1/find/specimens?q=molar&start=100</pre></code>
		
		You can page through the result set 100 records at a time by incrementing the <em>start</em> parameter.
	</div>
	
	<div>
		<h2 class="tealBottomRule"><a name="response"></a>The API response</h2>
		
		The API Find command will return a JSON object similar to this one:
		
		<div class="codeBlock"><code><pre>{
  "status":"ok",
  "q":"taxonomy_names.ht_order:primates",
  "totalResults":1235,
  "returnedResults":25,
  "start":0,
  "results":[
    {
      "institution.name":"Duke University, Evolutionary Anthropology",
      "project.name":"Allen Primate Skull Collection",
      "project.project_id":"77",
      "specimen.absolute_age":"",
      "specimen.body_mass":"",
      "specimen.body_mass_bibref_id":"",
      "specimen.body_mass_comments":"",
      "specimen.catalog_number":"0179",
      "specimen.collected_on":"",
      "specimen.collection_code":"",
      "specimen.collector":"",
      "specimen.created_on":"April 24 2013 at 15:37:02",
      "specimen.description":"",
      "specimen.institution_code":"BAA",
      "specimen.institution_id":"7",
      "specimen.last_modified_on":"October 24 2013 at 14:40:10",
      "specimen.locality_absolute_age":"",
      "specimen.locality_absolute_age_bibref_id":"",
      "specimen.locality_coordinates":"",
      "specimen.locality_datum_zone":"",
      "specimen.locality_description":"DUPC",
      "specimen.locality_easting_coordinate":"",
      "specimen.locality_northing_coordinate":"",
      "specimen.locality_relative_age":"",
      "specimen.locality_relative_age_bibref_id":"",
      "specimen.notes":"",
      "specimen.occurrence_id":"",
      "specimen.reference_source":"Vouchered",
      "specimen.relative_age":"adult",
      "specimen.type":"Yes",
      "specimen.sex":"Female",
      "specimen.specimen_id":"17",
      "specimen.url":"",
      "specimen.uuid":"",
      "taxonomy_name":[
        {
          "taxon_id":"24",
          "names":[
            {
              "ht_kingdom":"Animalia",
              "ht_phylum":"Chordata",
              "ht_class":"Mammalia",
              "ht_subclass":"",
              "ht_superorder":"",
              "ht_order":"Primates",
              "ht_suborder":"Strepsirrhini",
              "ht_superfamily":"",
              "ht_family":"Lorisidae",
              "ht_subfamily":"Lorisinae",
              "genus":"Nyticebus",
              "ht_supraspecific_clade":"Lorisidae",
              "species":"coucang",
              "subspecies":"",
              "variety":"",
              "author":"",
              "year":"0",
              "notes":"",
              "created_on":null,
              "last_modified_on":null
            }
          ]
        }
      ],
      "user.email":"kari.allen@wustl.edu"
    },
    ... [ and 185 more ] ...
  ]
}</pre></code></div>
		
		If the request was successful the <em>status</em> property will be set to <em>ok</em>, otherwise it will be set to <em>err</em>. When an error occurs the <em>message</em> property will include a description of the error. The <em>totalResults</em> property will indicate the total number of resources found, while the <em>returnedResults</em> property contains the number of resources returned in the current request.
	</div>
	<div>
		The heart of the response is the <em>results</em> property, which contains a list of JSON objects representing each returned resource. The properties of the resource object are resource fields in the form [resource type].[field name]. Most resources will contain fields from related resources in addition to their own fields. For example, specimens in a response also include fields from the project (ex. <em>project.name</em>) and institution (ex. <em>institution.name</em>) they belong to.
	</div>
	
	<div>
		<h2 class="tealBottomRule"><a name="adv_queries"></a>Advanced queries</h2>
		You may restrict your query to any fields contained in the response by prefacing your search text with the field name and a colon. For example to restrict the "molar" query to the specimen <em>element</em> field use
			<code><pre>http://www.morphosource.org/api/v1/find/specimens?q=specimen.element:molar</pre></code>
			
		If your search text includes spaces be sure to enclose the text in quotes, otherwise the field qualification will only apply to the text before the first space:
		
<code><pre>http://www.morphosource.org/api/v1/find/specimens?q=specimen.element:"m2 molar"</pre></code> (note that the query must be URL encoded – encoding is not applied here for readability)
	</div>
	
	<div>
		You may use the "AND" and "OR" keywords to combine several searches into a single result set. To search for all records where either the specimen taxonomy order <em>or</em> specimen notes contains the text "primate":
			<code><pre>http://www.morphosource.org/api/v1/find/specimens?q=specimen.notes:primate OR taxonomy_names:ht_order:primate</pre></code> (again, note that this query must be URL encoded)

Using "AND" would return only resources that have primate in both fields.	
	</div>
	
	<div>
		The wildcard character "*" may be used to match resources having content beginning with a text fragment. For example:
	
<code><pre>http://www.morphosource.org/api/v1/find/specimens?q=specimen.notes:prim*</pre></code> 	

Wildcards may be used only at the end of a text fragment. To find all records of a given resource type query on the wildcard character itself. To find, all projects in MorphoSource, for example:

<code><pre>http://www.morphosource.org/api/v1/find/projects?q=*</pre></code>
		
	</div>
	
	<div>
		<h2 class="tealBottomRule"><a name="adv_queries"></a>Sorting</h2>
		Results may be sorted by any field present in the response, using the field name and the <em>sort</em> option. To sort by specimen element:
			<code><pre>http://www.morphosource.org/api/v1/find/specimens?q=molar&sort=specimen.element</pre></code>
			
		Separate field names with semicolons to sort on more than one field:
		
<code><pre>http://www.morphosource.org/api/v1/find/specimens?q=molar&sort=specimen.element,taxonomy_names.ht_order</pre></code> 
	</div>

	<div>
		<h2 class="tealBottomRule"><a name="naming"></a>Field Names</h2>
		Results may be returned in JSON using either internal MorphoSource field names or DarwinCore terms. To set the naming convention use the <em>naming</em> option:

			<code><pre>http://www.morphosource.org/api/v1/find/specimens?q=molar&naming=darwincore</pre></code>

		The codes "morphosource" and "darwincore" will set the API format. If omitted, the API will default to MorphoSource field names.


	<div>
		<h2 class="tealBottomRule"><a name="data_dict"></a>Data Dictionary</h2>
		<table class="docTable" style="width: 100%">
			<thead>
				<tr>
					<th>MorphoSource Field</th>
					<th>Field Description</th>
					<th>DarwinCore Field</th>
				</tr>
				<tr>
					<th colspan="3"><a name="dict_specimen"></a>Specimen</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>absolute_age</td>
					<td>Absolute age of the specimen</td>
					<td>earliestEonOrLowestEonothem</td>
				</tr>
				<tr>
					<td>body_mass</td>
					<td>Specimen body mass</td>
					<td>measurement</td>
				</tr>
				<tr>
					<td>body_mass_bibref_id</td>
					<td>A unique identifier for a bibliographic reference to the body mass of the specimen</td>
					<td>dcterms:references</td>
				</tr>
				<tr>
					<td>body_mass_comments</td>
					<td>Additional notes regarding the body mass of the specimen</td>
					<td>measurement</td>
				</tr>
				<tr>
					<td>catalog_number</td>
					<td>Unique alphanumeric string identifying a specimen within a repository and subcollection</td>
					<td>catalogNumber</td>
				</tr>
				<tr>
					<td>collected_on</td>
					<td>Collection date for specimen</td>
					<td>eventDate</td>
				</tr>
				<tr>
					<td>collection_code</td>
					<td>Collection code modifier, typically designates a sub-collection within a repository. Not a universal element because not all repositories have sub-collections. However, this field is critical when applicable to avoid confusing specimens from different sub-collections. Please make sure you do not incorrectly omit a collection code fromt the specimen identifier</td>
					<td>collectionCode</td>
				</tr>
				<tr>
					<td>collector</td>
					<td>Specimen Collector</td>
					<td>recordedBy</td>
				</tr>
				<tr>
					<td>created_on</td>
					<td>Date of this record's creation</td>
					<td>N/A</td>
				</tr>
				<tr>
					<td>description</td>
					<td>General description of specimen physical content</td>
					<td>occurrenceRemarks</td>
				</tr>
				<tr>
					<td>institution_code</td>
					<td>Institution code prefix, mandatory component typically equivalent to repository institution’s acronym in full specimen identifier</td>
					<td>institutionCode</td>
				</tr>
				<tr>
					<td>institution_id</td>
					<td>Morphosource identifier of the associated institution which owns the physical specimen</td>
					<td>institutionID</td>
				</tr>
				<tr>
					<td>last_modified_on</td>
					<td>The date/time of the last changes made to the specimen record</td>
					<td>dcterms:modified</td>
				</tr>
				<tr>
					<td>locality_absolute_age</td>
					<td>Absolute age of locality in years</td>
					<td>earliestEonOrLowestEonothem</td>
				</tr>
				<tr>
					<td>locality_absolute_age_bibref_id</td>
					<td>A unique identifier for a bibliographic reference to the absolute age of the locality</td>
					<td>dcterms:references</td>
				</tr>
				<tr>
					<td>locality_coordinates</td>
					<td>Type of geographic reference (e.g. point or Latitude/Longitude)</td>
					<td>verbatimCoordinates</td>
				</tr>
				<tr>
					<td>locality_datum_zone</td>
					<td>Type of geographic datum type. If datum is UTM the zone is provided.</td>
					<td>verbatimCoordinateSystem</td>
				</tr>
				<tr>
					<td>locality_description</td>
					<td>General description/name of the locality</td>
					<td>locality</td>
				</tr>
				<tr>
					<td>locality_easting_coordinate</td>
					<td>Geographic coordinate, given in meters from UTM zone or longitude if system is WGS 84</td>
					<td>verbatimLongitude</td>
				</tr>
				<tr>
					<td>locality_northing_coordinate</td>
					<td>Geographic coordinate, given in meters from UTM zone or longitude if system is WGS 84</td>
					<td>verbatimLatitude</td>
				</tr>
				<tr>
					<td>locality_relative_age</td>
					<td>Relative age of the locality in geochronologic units</td>
					<td>lithostratigraphicTerms</td>
				</tr>
				<tr>
					<td>locality_relative_age_bibref_id</td>
					<td>A unique identifier for a bibliographic reference to the relative age of the locality</td>
					<td>dcterms:references</td>
				</tr>
				<tr>
					<td>notes</td>
					<td>General notes pertaining to the specimen</td>
					<td>occurrenceRemarks</td>
				</tr>
				<tr>
					<td>occurrence_id</td>
					<td>Unique Institutional identifier for the specimen</td>
					<td></td>
				</tr>
				<tr>
					<td>project_id</td>
					<td>MorphoSource identifier for the assocaited project</td>
					<td></td>
				</tr>
				<tr>
					<td>reference_source</td>
					<td>Indicates if this specimen was acessioned as part of a collection. "Vouchered" if true, "Unvouchered" if false</td>
					<td>dynamicProperties</td>
				</tr>
				<tr>
					<td>relative_age</td>
					<td>Relative age of the specimen in geochronologic units</td>
					<td>lithostratigraphicTerms</td>
				</tr>
				<tr>
					<td>sex</td>
					<td>The sex of the specimen</td>
					<td>sex</td>
				</tr>
				<tr>
					<td>specimen_id</td>
					<td>MorphoSource identifier for specimen</td>
					<td>occurrenceID</td>
				</tr>
				<tr>
					<td>taxon_id</td>
					<td>MorphoSource identifier for associated taxonomy</td>
					<td>associatedTaxa</td>
				</tr>
				<tr>
					<td>type</td>
					<td>Holotype? (yes/no)</td>
					<td></td>
				</tr>
				<tr>
					<td>url</td>
					<td>A URL to the specimen's record in the owning institutions' repository or DOI identifier for the record</td>
					<td>dcterms:references</td>
				</tr>
				<tr>
					<td>uuid</td>
					<td>iDigBio UUID</td>
					<td>dcterms:references</td>
				</tr>

			</tbody>
			<thead>
				<tr>
					<th colspan="3"><a name="dict_facility"></a>Facility</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>address1</td>
					<td>Facility Address Line 1</td>
					<td>institutionID</td>
				</tr>
				<tr>
					<td>address2</td>
					<td>Address Line 2</td>
					<td>institutionID</td>
				</tr>
				<tr>
					<td>city</td>
					<td>Facility City</td>
					<td>institutionID</td>
				</tr>
				<tr>
					<td>contact</td>
					<td>Email address for facility contact</td>
					<td>institutionID</td>
				</tr>
				<tr>
					<td>country</td>
					<td>Facility Country</td>
					<td>institutionID</td>
				</tr>
				<tr>
					<td>created_on</td>
					<td>Date of creation for facility record</td>
					<td>institutionID</td>
				</tr>
				<tr>
					<td>description</td>
					<td>Facility Description</td>
					<td>institutionID</td>
				</tr>
				<tr>
					<td>facility_id</td>
					<td>MorphoSource identifer for the facility</td>
					<td>institutionID</td>
				</tr>
				<tr>
					<td>institution</td>
					<td>Facility parent institution</td>
					<td>institutionID</td>
				</tr>
				<tr>
					<td>last_modified_on</td>
					<td>Date the facility record was last edited</td>
					<td>dcterms:modified</td>
				</tr>
				<tr>
					<td>name</td>
					<td>Official facility name</td>
					<td>institutionID</td>
				</tr>
				<tr>
					<td>postalcode</td>
					<td>Facility ZIP/Postal Code</td>
					<td>institutionID</td>
				</tr>
				<tr>
					<td>stateprov</td>
					<td>Facility State or Province</td>
					<td>institutionID</td>
				</tr>
			</tbody>
			<thead>
				<tr>
					<th colspan="3"><a name="dict_media"></a>Media Group</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>copyright_info</td>
					<td>Copyright holder</td>
					<td>dcterms:accessRights</td>
				</tr>
				<tr>
					<td>copyright_license</td>
					<td>License governing the terms of use/resuse of the media</td>
					<td>dcterms:accessRights</td>
				</tr>
				<tr>
					<td>copyright_permission</td>
					<td>Specific rights granted for use or  reuse of the media</td>
					<td>dcterms:accessRights</td>
				</tr>
				<tr>
					<td>derived_from_media_id</td>
					<td>MorphoSource identifier of media group record was derived from</td>
					<td>associatedMedia</td>
				</tr>
				<tr>
					<td>created_on</td>
					<td>Date of creation for the media record</td>
					<td>N/A</td>
				</tr>
				<tr>
					<td>element</td>
					<td>The anatomical element depicted in the media</td>
					<td>associatedMedia</td>
				</tr>
				<tr>
					<td>grant_support</td>
					<td>The names of supporting institutions and identifiers for grants which supported creation of the media</td>
					<td>dcterms:bibliographicCitation</td>
				</tr>
				<tr>
					<td>is_copyrighted</td>
					<td>Flag indicating the copyright status of the media. "1" indicates that it is copyrighted, "0" that it is not.</td>
					<td>dcterms:accessRights</td>
				</tr>
				<tr>
					<td>last_modified_on</td>
					<td>Date the media record was last changed</td>
					<td>dcterms:modified</td>
				</tr>
				<tr>
					<td>media_citation_instruction1-3</td>
					<td>Fields containing additional information pertaining to the formal citation language</td>
					<td>dcterms:bibliographicCitations</td>
				</tr>
				<tr>
					<td>media_id</td>
					<td>MorphoSource identifier for this media record</td>
					<td>datasetID</td>
				</tr>
				<tr>
					<td>notes</td>
					<td>General notes describing the media</td>
					<td>associatedMedia</td>
				</tr>
				<tr>
					<td>published</td>
					<td>Publication status</td>
					<td>dcterms:accessRights</td>
				</tr>
				<tr>
					<td>published_on</td>
					<td>Date on which the media record was published in MorphoSource</td>
					<td>N/A</td>
				</tr>
				<tr>
					<td>scanner_acquisition_time</td>
					<td>Acquisition Time</td>
					<td>measurement</td>
				</tr>
				<tr>
					<td>scanner_amperage</td>
					<td>Amperage, units in microAmps</td>
					<td>measurement</td>
				</tr>
				<tr>
					<td>scanner_calibration_description</td>
					<td>Calibration Description</td>
					<td>measurement</td>
				</tr>
				<tr>
					<td>scanner_calibration_flux_normalization</td>
					<td>Flux Normalization Calibration</td>
					<td>measurement</td>
				</tr>
				<tr>
					<td>scanner_calibration_geometric_calibration</td>
					<td>Geometric Calibration</td>
					<td>measurement</td>
				</tr>
				<tr>
					<td>scanner_calibration_shading_correction</td>
					<td>Shading Correction Calibration</td>
					<td>measurement</td>
				</tr>
				<tr>
					<td>scanner_exposure_time</td>
					<td>Exposure time, units in seconds</td>
					<td>measurement</td>
				</tr>
				<tr>
					<td>scanner_filter</td>
					<td>Filter</td>
					<td>measurement</td>
				</tr>
				<tr>
					<td>scanner_frame_averaging</td>
					<td>Frame Averaging. The number of images acquired at each projection angle and their average</td>
					<td>measurement</td>
				</tr>
				<tr>
					<td>scanner_id</td>
					<td>Unique MorphoSource identifier for scanner</td>
					<td>measurementID</td>
				</tr>
				<tr>
					<td>scanner_name</td>
					<td>Name of scanner</td>
					<td>measurement</td>
				</tr>
				<tr>
					<td>scanner_projections</td>
					<td>Projections. The number of images at distinct angles used to create a 3D image</td>
					<td>measurement</td>
				</tr>
				<tr>
					<td>scanner_technicians</td>
					<td>Individual(s) responsible for scanning &amp; uploading the specimen</td>
					<td>measurement</td>
				</tr>
				<tr>
					<td>scanner_voltage</td>
					<td>Voltage, units in kilovolts</td>
					<td>measurement</td>
				</tr>
				<tr>
					<td>scanner_watts</td>
					<td>Watts (power = kV*Amps)</td>
					<td>measurement</td>
				</tr>
				<tr>
					<td>scanner_wedge</td>
					<td>Wedge</td>
					<td>measurement</td>
				</tr>
				<tr>
					<td>scanner_x_resolution</td>
					<td>X-Axis Resolution</td>
					<td>measurement</td>
				</tr>
				<tr>
					<td>scanner_y_resolution</td>
					<td>Y-Axis Resolution</td>
					<td>measurement</td>
				</tr>
				<tr>
					<td>scanner_z_resolution</td>
					<td>Z-Axis Resolution</td>
					<td>measurement</td>
				</tr>
				<tr>
					<td>side</td>
					<td>The anatomical side of the organism that the media group represents when applicable</td>
					<td>associatedMedia</td>
				</tr>
				<tr>
					<td>specimen_institution_code</td>
					<td>Unique code identifying an institution</td>
					<td>institutionCode</td>
				</tr>
				<tr>
					<td>specimen_collection_code</td>
					<td>Code identifying institutional collection specimen resides in</td>
					<td>collectionCode</td>
				</tr>
				<tr>
					<td>specimen_catalog_number</td>
					<td>Unique alphanumeric string identifying a specimen within a repository and subcollection</td>
					<td>catalogNumber</td>
				</tr>
				<tr>
					<td>specimen_specimen_id</td>
					<td>Unique MorpoSource identifier for the specimen</td>
					<td>occurrenceID</td>
				</tr>
				<tr>
					<td>title</td>
					<td>Title of media group</td>
					<td>datasetName</td>
				</tr>
			</tbody>
			<thead>
				<tr>
					<th colspan="3"><a name="dict_media_file"></a>Media File</th>
				<tr>
			</thead>
			<tbody>
				<tr>
					<td>doi</td>
					<td>Global unique identifier for the media file</td>
					<td>dcterms:references</td>
				</tr>
				<tr>
					<td>download</td>
					<td>If enabled, a link to download the media file from MorphoSource</td>
					<td>associatedMedia</td>
				</tr>
				<tr>
					<td>element</td>
					<td>The anatomical element depicted by the media file</td>
					<td>associatedMedia</td>
				</tr>
				<tr>
					<td>file_type</td>
					<td>Raw file of group or derivative file</td>
					<td></td>
				</tr>
				<tr>
					<td>filesize</td>
					<td>Size of the file in mebibyte (MiB)</td>
					<td>associatedMedia</td>
				</tr>
				<tr>
					<td>notes</td>
					<td>Media file notes</td>
					<td></td>
				</tr>
				<tr>
					<td>media_file_id</td>
					<td>MorphoSource identifier for media file</td>
					<td></td>
				</tr>
				<tr>
					<td>mimetype</td>
					<td>Identifier for file format (e.g. "application/ply" or "image/tiff")</td>
					<td>associatedMedia</td>
				</tr>
				<tr>
					<td>published</td>
					<td>Publication status (supersedes that of the media group)</td>
					<td>dcterms:accessRights</td>
				</tr>
				<tr>
					<td>published_on</td>
					<td>Date on which the media file record was published on MorphoSource</td>
					<td>N/A</td>
				</tr>
				<tr>
					<td>side</td>
					<td>The anatomical side of the organism that the element represents, when applicable</td>
					<td>associatedMedia</td>
				</tr>
				<tr>
					<td>title</td>
					<td>Formal, descriptive, title of the media file</td>
					<td>associatedMedia</td>
				</tr>
			</tbody>
			<thead>
				<tr>
					<th colspan="3"><a name="dict_project"></a>Projects</th>
				</tr>
			</thead>
			<tbody>	
				<tr>
					<td>abstract</td>
					<td>A general description of the goals and scope of the project</td>
					<td>dcterms:bibliographicCitation</td>
				</tr>
				<tr>
					<td>created_on</td>
					<td>Date the project was created in MorphoSource</td>
					<td>N/A</td>
				</tr>
				<tr>
					<td>last_modified_on</td>
					<td>Date the project was last modified in MorphoSource</td>
					<td>dcterms:modified</td>
				</tr>
				<tr>
					<td>name</td>
					<td>Official name of the project</td>
					<td>datasetName/dcterms:bibliographicCitation</td>
				</tr>
				<tr>
					<td>project_id</td>
					<td>Unique identifer for the project within MorphoSource</td>
					<td>datasetID</td>
				</tr>
				<tr>
					<td>published_on</td>
					<td>Date on which the project was published</td>
					<td>N/A</td>
				</tr>
				<tr>
					<td>url</td>
					<td>Link to an external page describing or containing other information concerning the project, or DOI uniquely identifying the resource</td>
					<td>dcterms:reference</td>
				</tr>
			</tbody>
			<thead>
				<tr>
					<th colspan="3"><a name="dict_taxonomy"></a>Taxonomy</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>author</td>
					<td>Scientific Name Author</td>
					<td>scientificNameAuthorship</td>
				</tr>
				<tr>
					<td>created_on</td>
					<td>Date taxonomy was created in MorphoSource</td>
					<td>N/A</td>
				</tr>
				<tr>
					<td>ht_kingdom</td>
					<td>Kingdom</td>
					<td>kingdom</td>
				</tr>
				<tr>
					<td>ht_phylum</td>
					<td>Phylum</td>
					<td>phylum</td>
				</tr>
				<tr>
					<td>ht_class</td>
					<td>Class</td>
					<td>class</td>
				</tr>
				<tr>
					<td>ht_subclass</td>
					<td>Subclass</td>
					<td>higherClassification</td>
				</tr>
				<tr>
					<td>ht_superorder</td>
					<td>Superorder</td>
					<td>higherClassification</td>
				</tr>
				<tr>
					<td>ht_order</td>
					<td>Order</td>
					<td>order</td>
				</tr>
				<tr>
					<td>ht_suborder</td>
					<td>Suborder</td>
					<td>higherClassification</td>
				</tr>
				<tr>
					<td>ht_superfamily</td>
					<td>Superfamily</td>
					<td>higherClassification</td>
				</tr>
				<tr>
					<td>ht_family</td>
					<td>Family</td>
					<td>family</td>
				</tr>
				<tr>
					<td>ht_subfamily</td>
					<td>Subfamily</td>
					<td>higherClassification</td>
				</tr>
				<tr>
					<td>genus</td>
					<td>Genus</td>
					<td>genus</td>
				</tr>
				<tr>
					<td>ht_supraspecific_clade</td>
					<td>Supraspecific clade</td>
					<td>higherClassification</td>
				</tr>
				<tr>
					<td>species</td>
					<td>Species</td>
					<td>specificEpithet</td>
				</tr>
				<tr>
					<td>subspecies</td>
					<td>Subspecies</td>
					<td>infraspecificEpithet</td>
				</tr>
				<tr>
					<td>variety</td>
					<td>Variety</td>
					<td>infraspecificEpithet</td>
				</tr>
				<tr>
					<td>is_extinct</td>
					<td>Flag that indicates if the subject of the current taxonomy is extinct</td>
					<td>taxonRemarks</td>
				</tr>
				<!--<tr>
					<td>is_primary</td>
					<td>Flag that indicates if the current taxonomy is the primary identification</td>
					<td>taxonomicStatus</td>
				</tr>
				<tr>
					<td>justification</td>
					<td>Explanation for creation of current taxonomy</td>
					<td>taxonRemarks</td>
				</tr>-->
				<tr>
					<td>last_modified_on</td>
					<td>Date taxonomy was last modified</td>
					<td>dcterms:modified</td>
				</tr>
				<tr>
					<td>notes</td>
					<td>General remarks pertaining to the Taxonomy</td>
					<td>taxonRemarks</td>
				</tr>
				<!--<tr>
					<td>review_notes</td>
					<td>Notes on the status of review of this taxonomy</td>
					<td>taxonRemarks</td>
				</tr>
				<tr>
					<td>review_status</td>
					<td>Status of the review of this taxonomy</td>
					<td>taxonRemarks</td>
				</tr>-->
				<tr>
					<td>taxon_id</td>
					<td>Unique MorphoSource identifier for the taxonomy</td>
					<td>taxonID</td>
				</tr>
				<tr>
					<td>year</td>
					<td>Year of Authorship</td>
					<td>namePublishedInYear</td>
				</tr>
			</tbody>
			<thead>
				<tr>
					<th colspan="3"><a name="dict_scanner"></a>Facility Scanners</th>
				<tr>
			</thead>
			<tbody>
				<tr>
					<td>description</td>
					<td>General description of the scanner</td>
					<td>institutionID</td>
				</tr>
				<tr>
					<td>facility_id</td>
					<td>MorphoSource ID for facility</td>
					<td>institutionID</td>
				</tr>
				<tr>
					<td>name</td>
					<td>Make &amp; Model of the scanner</td>
					<td>institutionID</td>
				</tr>
			</tbody>
			<thead>
				<tr>
					<th colspan="3"><a name="dict_institution"></a>Institutions</th>
				<tr>
			</thead>
			<tbody>
				<tr>
					<td>city</td>
					<td>City</td>
					<td>institutionID</td>
				</tr>
				<tr>
					<td>county</td>
					<td>County</td>
					<td>institutionID</td>
				</tr>
				<tr>
					<td>description</td>
					<td>Description of the institution</td>
					<td>institutionID</td>
				</tr>
				<tr>
					<td>name</td>
					<td>Formal name of the institution</td>
					<td>institutionID</td>
				</tr>
				<tr>
					<td>state</td>
					<td>State</td>
					<td>institutionID</td>
				</tr>
			</tbody>
			<thead>
				<tr>
					<th colspan="3"><a name="dict_bibliography"></a>Bibliography</th>
				<tr>
			</thead>
			<tbody>
				<tr>
					<td>abstract</td>
					<td>Article Abstract</td>
					<td>dcterms:references</td>
				</tr>
				<tr>
					<td>article_title</td>
					<td>Article Tite</td>
					<td>dcterms:references</td>
				</tr>
				<tr>
					<td>authors</td>
					<td>Article Authors</td>
					<td>dcterms:references</td>
				</tr>
				<tr>
					<td>collation</td>
					<td>Collation</td>
					<td>dcterms:references</td>
				</tr>
				<tr>
					<td>created_on</td>
					<td>Date record created for this resource</td>
					<td>N/A</td>
				</tr>
				<tr>
					<td>description</td>
					<td>Article Description</td>
					<td>dcterms:references</td>
				</tr>
				<tr>
					<td>editors</td>
					<td>Editors</td>
					<td>dcterms:references</td>
				</tr>
				<tr>
					<td>eletronic_resource_number</td>
					<td>An externally assigned reference (usually a DOI)</td>
					<td>dcterms:references</td>
				</tr>
				<tr>
					<td>external_identifer</td>
					<td>An externally assigned reference (usually a DOI)</td>
					<td>dcterms:references</td>
				</tr>
				<tr>
					<td>isbn</td>
					<td>ISBN</td>
					<td>dcterms:references</td>
				</tr>
				<tr>
					<td>journal_title</td>
					<td>Journal Title</td>
					<td>dcterms:references</td>
				</tr>
				<tr>
					<td>keywords</td>
					<td>Keywords</td>
					<td>dcterms:references</td>
				</tr>
				<tr>
					<td>language</td>
					<td>Language</td>
					<td>dcterms:references</td>
				</tr>
				<tr>
					<td>last_modified_on</td>
					<td>Date this record was last modified</td>
					<td>dcterms:modified</td>
				</tr>
				<tr>
					<td>monograph_title</td>
					<td>Monography Title</td>
					<td>dcterms:references</td>
				</tr>
				<tr>
					<td>page_number</td>
					<td>Total page count</td>
					<td>dcterms:references</td>
				</tr>
				<tr>
					<td>publication_place</td>
					<td>Place of Publication</td>
					<td>dcterms:references</td>
				</tr>
				<tr>
					<td>publication_year</td>
					<td>Year of Publication</td>
					<td>dcterms:references</td>
				</tr>
				<tr>
					<td>publisher</td>
					<td>Publisher</td>
					<td>dcterms:references</td>
				</tr>
				<tr>
					<td>reference_type</td>
					<td>Reference Type</td>
					<td>dcterms:references</td>
				</tr>
				<tr>
					<td>secondary_authors</td>
					<td>Secondary Article Authors</td>
					<td>dcterms:references</td>
				</tr>
				<tr>
					<td>secondary_title</td>
					<td>Secondary Article Title</td>
					<td>dcterms:references</td>
				</tr>
				<tr>
					<td>section</td>
					<td>Section</td>
					<td>dcterms:references</td>
				</tr>
				<tr>
					<td>url</td>
					<td>Article URL</td>
					<td>dcterms:references</td>
				</tr>
				<tr>
					<td>volume</td>
					<td>Journal Volume</td>
					<td>dcterms:references</td>
				</tr>
				<tr>
					<td>worktype</td>
					<td>Worktype</td>
					<td>dcterms:references</td>
				</tr>
	</table>
		</div>	
	</div><!-- end textContent -->
