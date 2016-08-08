<div class="blueRule"><!-- empty --></div>
<div style="float:right; padding:5px 5px 5px 0px; width:185px; border:2px solid #EDEDED; margin:20px 0px 20px 20px; font-size:11px;">
	<div style="padding-left:10px;"><b>Jump to:</b></div>
	<ul style="padding:0px 0px 0px 30px; margin:0px;">
		<li><a href="#using" class="blueText">Using the API</a></li>
		<li><a href="#find" class="blueText">Locating and retrieving resources</a></li>
		<li><a href="#response" class="blueText">The API response</a></li>
		<li><a href="#adv_queries" class="blueText">Advanced queries</a></li>
		<li><a href="#adv_queries" class="blueText">Sorting</a></li>
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
		<br/>The API is a RESTful web service that delivers data primarily as JSON documents. All GET requests must be URL-encoded.
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
      "specimen.approval_status":"New",
      "specimen.body_mass":"",
      "specimen.body_mass_comments":"",
      "specimen.catalog_number":"0179",
      "specimen.collected_on":"",
      "specimen.collection_code":"",
      "specimen.collector":"",
      "specimen.created_on":"April 24 2013 at 15:37:02",
      "specimen.description":"",
      "specimen.element":"skull",
      "specimen.institution_code":"BAA",
      "specimen.institution_id":"7",
      "specimen.last_modified_on":"October 24 2013 at 14:40:10",
      "specimen.locality_absolute_age":"",
      "specimen.locality_coordinates":"",
      "specimen.locality_datum_zone":"",
      "specimen.locality_description":"DUPC",
      "specimen.locality_easting_coordinate":"",
      "specimen.locality_northing_coordinate":"",
      "specimen.locality_relative_age":"",
      "specimen.locality_relative_age_bibref_id":"",
      "specimen.notes":"",
      "specimen.reference_source":"Vouchered",
      "specimen.relative_age":"adult",
      "specimen.sex":"Female",
      "specimen.side":"",
      "specimen.specimen_id":"17",
      "specimen.url":"",
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
              "is_primary":"1",
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
	
</div><!-- end textContent -->