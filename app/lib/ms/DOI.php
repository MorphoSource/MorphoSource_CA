<?php
	require_once(__CA_BASE_DIR__."/vendor/autoload.php");

	use GuzzleHttp\Client;
	
	class DOI {
		# -------------------------------------------------------
		private $client;
		private $config;
		
		private $error = null;
		
		# -------------------------------------------------------
		public function __construct() {
			$this->client = new Client();
			$this->config = Configuration::load();
		}
		# -------------------------------------------------------
		public function createDOI($ps_id, $pa_metadata) {
			$vs_root = $this->config->get('doi_url');
			$vs_username = $this->config->get('doi_username');
			$vs_password = $this->config->get('doi_password');
			$vs_shoulder = $this->config->get('doi_shoulder');
	
			if (substr($vs_root, -1) != '/') { $vs_root = $vs_root.'/'; }

			$vs_url = $vs_root.$vs_shoulder.'/'.$ps_id;
			
			foreach($pa_metadata as $vs_key => $vs_val) {
				$vs_metadata .= "{$vs_key}: ".DOI::escapeForANVL($vs_val)."\n";
			}
			
			$o_response = $this->client->put($vs_url, [
				'auth' =>  [$vs_username, $vs_password],
				'body' => $vs_metadata,
				'headers' => ['Content-type' => 'text/plain']
			]);
			// Send the request and get the response
			try {
				$vs_response = (string)$o_response->getBody();
				
				if (preg_match("!^success:(.*)$!", $vs_response, $va_matches)) {
					$this->error = null;
					$vs_doi = $va_matches[1];
					if (strpos($vs_doi, ' |') !== false) {
						$vs_doi = substr($vs_doi, 0, strpos($vs_doi, ' |')); 
					}
					return $vs_doi;
				} else {
					$this->error = $vs_response;
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