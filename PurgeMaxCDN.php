<?php

/**
 * PurgeCDN (MaxCDN)
 * 
 * @copyright 2015
 * @author Michele Fantetti (WaPoNe)
 * @version 1.0 2015-02-20
 */

// Include Composer Autoloader
$loader = require_once(__DIR__ . "/../vendor/autoload.php");


class PurgeMaxCDN {

	// Credentials
	private $zone_id = 'your zone id';
	private $consumer_key = 'your consumer key';
	private $consumer_secret = 'your consumer secret';
	private $company_alias = 'your company alias';

	/**
    * Purge method
    */
	private function purge($params = "")
	{
		$api = new MaxCDN($this->company_alias, $this->consumer_key, $this->consumer_secret);

		try {
			// delete a file from the cache
			return $api->delete('/zones/pull.json/'.$this->zone_id.'/cache', $params);
		} catch(CurlException $e) {
			print_r($e->getMessage());
			print_r($e->getHeaders());
		}
	}
	
  /**
   * Run script
   */
  public function run($argv)
  {
  	$result = "";
  	$final_result;
  	
  	if(isset($argv[1]) && $argv[1] == "--purge"):
  		// Purging all CDNs files
  		if(isset($argv[2]) && $argv[2] == "all"):
  			echo "Begin to purge all CDNs files\n";
  			$this->result = $this->purge();
  		// Purging a specific file
  		elseif(isset($argv[2]) && $argv[2] !== ""):
  			$file_to_purge = $argv[2];
  			echo "Begin to purge: {$file_to_purge} file\n";
  			$params = array('file' => $file_to_purge);
  			$this->result = $this->purge($params);
  		else:
  			echo $this->usageHelp();
  		endif;
  	else:
  		echo $this->usageHelp();
  	endif;
  	
  	// Final outcome
  	$this->final_result = json_decode($this->result);
  	// In positive case 'delete' method (MaxCDN API) returns 200
  	if($this->final_result->{'code'} == '200'):
  		echo "Finish to purge. File(s) purged.\n";
  	else:
  		echo "Finish to purge. File(s) not purged.\n";
  	endif;
	}
	
  /**
   * Retrieve Usage Help Message
   */
  public function usageHelp()
	{
        return <<<USAGE
Usage:  php purgeCDN.php -- [options]

  --purge <file_to_purge>       Purge the specific file in this format "path/to/the/file" whitout base URL. Ex: http://cdn_url/path/to/the/file -> path/to/the/file
  --purge all                   Purge all CDNs files
  --help                        This help

USAGE;
    }

}

$purger = new PurgeMaxCDN();
$purger->run($argv);
