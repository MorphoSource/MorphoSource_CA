<?php
	require_once(__CA_BASE_DIR__."/vendor/autoload.php");

	use GuzzleHttp\Client;
	use GuzzleHttp\Post\PostFile;
	
	class DOI {
		# -------------------------------------------------------
		private $client;
		private $config;
		
		private $error = null;
		private $xml = null;
		
		# -------------------------------------------------------
		public function __construct() {
			$this->client = new Client();
			$this->config = Configuration::load();
		}
		# -------------------------------------------------------
		public function createDOI() {
			$vs_url = $this->config->get('doi_url');
			$vs_username = $this->config->get('doi_username');
			$vs_password = $this->config->get('doi_password');
			
			if (substr($vs_url, -1) != '/') { $vs_url = $vs_url.'/'; }

			if (!$this->xml) {
				$this->error = "XML Metadata not set before DOI creation.";
				return false;
			}			
			
			// Change this to encode XML here
			$o_request = $this->client->createRequest('POST', $vs_url, array(
				'body' => array(
					'operation' => 'doMDUpload',
					'login_id' => $vs_username,
					'login_passwd' => $vs_password,
				)
			));
			$o_body = $o_request->getBody();
			$o_body->addFile(new PostFile('fname', $this->xml));
			$o_body->forceMultipartUpload(true);
			$o_body->applyRequestHeaders($o_request);

			# Send the request and get the response
			try {
				$o_response = $o_request->send();
				
				if (($o_response->getStatusCode() == 200) 
					|| ($o_response->getStatusCode() == 201)) 
				{
					$this->error = null;
					$vs_doi = "doi:".$vs_shoulder.$ps_id;
					return $vs_doi;
				} else {
					$this->error = $o_response->getBody();
					return false;
				}
			} catch (Exception $e) {
				$this->error = $e->getMessage();
				return false;
			}
		}
		# -------------------------------------------------------
		private static function escapeForANVL($ps_val) {
			$ps_val = str_replace("%", "%25", $ps_val);
			$ps_val = str_replace(":", "%3A", $ps_val);
			$ps_val = str_replace("\r", "%0D", $ps_val);
			$ps_val = str_replace("\n", "%0A", $ps_val);
			return $ps_val;
		}
		# -------------------------------------------------------
		public function XMLMetadata($ps_id, $vs_title, $vs_resourceType, 
			$vs_URL, $vs_authorFirst, $vs_authorLast, $vs_pubYear=null
		) {
			if (!$vs_pubYear) {
				$vs_pubYear = date("Y");
			}

			$vs_shoulder = $this->config->get('doi_shoulder');
			$vs_doi = $vs_shoulder.$ps_id;

			$xmlTree = new SimpleXMLElement("<doi_batch></doi_batch>");
			$xmlTree->addAttribute("xmlns", 
				"http://www.crossref.org/schema/4.4.1");
			$xmlTree->addAttribute("xmlns:xsi", 
				"http://www.w3.org/2001/XMLSchema-instance");
			$xmlTree->addAttribute("version", "4.4.1");
			$xmlTree->addAttribute("xsi:schemaLocation", 
				"http://www.crossref.org/schema/4.4.1 ".
				"http://www.crossref.org/schemas/crossref4.4.1.xsd");

			$xmlHead = $xmlTree->addChild("head");
			$xmlDOIBatchId = $xmlHead->addChild("doi_batch_id", uniqid());
			$xmlTimestamp = $xmlHead->addChild("timestamp", time());
			$xmlDepositor = $xmlHead->addChild("depositor");
			$xmlDepName = $xmlDepositor->addChild("depositor_name", "Duke University Libraries");
			$xmlEmail = $xmlDepositor->addChild("email_address", "ddrhelp@duke.edu");
			$xmlRegistrant = $xmlHead->addChild("registrant", "Duke University Libraries");

			$xmlBody = $xmlTree->addChild("body");
			$xmlDB = $xmlBody->addChild("database");
			
			$xmlDBMetadata = $xmlDB->addChild("database_metadata");
			$xmlDBMetadata->addAttribute("language", "en");
			$xmlDBTitles = $xmlDBMetadata->addChild("titles");
			$xmlDBTitle = $xmlDBTitles->addChild("title", "MorphoSource Media");
			$xmlDBInst = $xmlDBMetadata->addChild("institution");
			$xmlDBInstName = $xmlDBInst->addChild("institution_name", "MorphoSource");

			$xmlDataset = $xmlDB->addChild("dataset");
			$xmlDataset->addAttribute("dataset_type", "record");
			
			$xmlContrib = $xmlDataset->addChild("contributors");
			$xmlPersName = $xmlContrib->addChild("person_name");
			$xmlPersName->addAttribute("contributor_role", "author");
			$xmlPersName->addAttribute("sequence", "first");
			$xmlGivenName = $xmlPersName->addChild("given_name", $vs_authorFirst);
			$xmlSurname = $xmlPersName->addChild("surname", $vs_authorLast);

			$xmlDSTitles = $xmlDataset->addChild("titles");
			$xmlDSTitle = $xmlDSTitles->addChild("title", $vs_title);

			$xmlDBDate = $xmlDataset->addChild("database_date");
			$xmlPubDate = $xmlDBDate->addChild("publication_date");
			$xmlPubYear = $xmlPubDate->addChild("year", $vs_pubYear);

			$xmlFormat = $xmlDataset->addChild("format", $vs_resourceType);

			$xmlDoiData = $xmlDataset->addChild("doi_data");
			$xmlDoi = $xmlDoiData->addChild("doi", $vs_doi);
			$xmlResource = $xmlDoiData->addChild("resource", $vs_URL);

			$this->xml = $xmlTree->asXML();

			return true;
		}
		# -------------------------------------------------------
		public function getError() {
			return $this->error;
		}
		# -------------------------------------------------------
		public function clearError() {
			$this->error = null;
		}
		# -------------------------------------------------------
	}
?>