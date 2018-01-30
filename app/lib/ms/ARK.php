<?php
	require_once(__CA_BASE_DIR__."/vendor/autoload.php");

	use GuzzleHttp\Client;
	
	class ARK {
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
		public function createARK($ps_id, $pa_metadata) {
			$vs_root = $this->config->get('ark_url');
			$vs_username = $this->config->get('ark_username');
			$vs_password = $this->config->get('ark_password');
			$vs_shoulder = $this->config->get('ark_shoulder');

			if (!$vs_root) {
				$this->error = "No ARK URL.";
				return false;
			}
	
			if (substr($vs_root, -1) != '/') { $vs_root = $vs_root.'/'; }

			$vs_url = $vs_root.$vs_shoulder.'/'.$ps_id;
			
			foreach($pa_metadata as $vs_key => $vs_val) {
				$vs_metadata .= "{$vs_key}: ".ARK::escapeForANVL($vs_val)."\n";
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
					return $va_matches[1];
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
		public function modifyARK($ps_id, $pa_metadata) {
			$vs_root = $this->config->get('ark_url');
			$vs_username = $this->config->get('ark_username');
			$vs_password = $this->config->get('ark_password');
			$vs_shoulder = $this->config->get('ark_shoulder');

			if (!$vs_root) {
				$this->error = "No ARK URL.";
				return false;
			}
	
			if (substr($vs_root, -1) != '/') { $vs_root = $vs_root.'/'; }

			$vs_url = $vs_root.$vs_shoulder.'/'.$ps_id;
			
			foreach($pa_metadata as $vs_key => $vs_val) {
				$vs_metadata .= "{$vs_key}: ".ARK::escapeForANVL($vs_val)."\n";
			}
			
			$o_response = $this->client->post($vs_url, [
				'auth' =>  [$vs_username, $vs_password],
				'body' => $vs_metadata,
				'headers' => ['Content-type' => 'text/plain']
			]);
			// Send the request and get the response
			try {
				$vs_response = (string)$o_response->getBody();
				
				if (preg_match("!^success:(.*)$!", $vs_response, $va_matches)) {
					$this->error = null;
					return $va_matches[1];
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
		public function deleteARK($ps_id) {
			$vs_root = $this->config->get('ark_url');
			$vs_username = $this->config->get('ark_username');
			$vs_password = $this->config->get('ark_password');
			$vs_shoulder = $this->config->get('ark_shoulder');

			if (!$vs_root) {
				$this->error = "No ARK URL.";
				return false;
			}
	
			if (substr($vs_root, -1) != '/') { $vs_root = $vs_root.'/'; }

			$vs_url = $vs_root.$vs_shoulder.'/'.$ps_id;
						
			$o_response = $this->client->delete($vs_url, [
				'auth' =>  [$vs_username, $vs_password],
				'headers' => ['Content-type' => 'text/plain']
			]);
			// Send the request and get the response
			try {
				$vs_response = (string)$o_response->getBody();
				
				if (preg_match("!^success:(.*)$!", $vs_response, $va_matches)) {
					$this->error = null;
					return $va_matches[1];
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