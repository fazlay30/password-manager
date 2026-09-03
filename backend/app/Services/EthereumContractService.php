<?php

namespace App\Services;

use FurqanSiddiqui\ECDSA\Curves\Secp256k1;
use FurqanSiddiqui\Ethereum\Ethereum as EthereumClient;
use FurqanSiddiqui\Ethereum\Networks\Ethereum as EthereumNetwork;
use FurqanSiddiqui\Ethereum\Contracts\Contract;
use FurqanSiddiqui\Ethereum\Contracts\ABI_Factory;
use FurqanSiddiqui\Ethereum\RPC\Infura;
use FurqanSiddiqui\Ethereum\Buffers\EthereumAddress;
use Illuminate\Support\Facades\Log;

class EthereumContractService
{
    protected $ethereum;
    protected $contractAddress;
    protected $contractAbi;
    protected $walletAddress;
    protected $privateKey;
    protected $deployedContract;
    protected $infuraApiKey;
    protected $infuraApiSecret;
    protected $infuraRpc;
    
    public function __construct()
    {
        // Get values from .env file
        $this->contractAddress = env('ETHEREUM_CONTRACT_ADDRESS');
        $this->walletAddress = env('ETHEREUM_WALLET_ADDRESS');
        $this->privateKey = env('ETHEREUM_PRIVATE_KEY');
        $this->infuraApiKey = env('INFURA_API_KEY');
        $this->infuraApiSecret = env('INFURA_API_SECRET');

        // Get RPC URL from config
        // $rpcUrl = config('services.ethereum.rpc_url'); // From .env
        
        // Setup the Ethereum client
        $ecc = new Secp256k1();
        
        // Create custom network config for Holesky
        $networkConfig = EthereumNetwork::Sepolia();
        
        // Initialize Ethereum client and assign to class property
        $this->ethereum = new EthereumClient($ecc, $networkConfig);
        
        // Load the contract ABI
        $abiPath = storage_path('contract_abi.json');
        // $this->contractAbi = json_decode(file_get_contents($abiPath), true);
        $errors = [];
        $factory = new ABI_Factory();
        $contract = $factory->fromJSONFile($abiPath, true, $errors);            

        // If you want to interact with deployed contract:
        $infura = new Infura(
            apiKey: $this->infuraApiKey,
            apiSecret: '',
            networkId: 'sepolia' // or 'goerli', 'mainnet', etc.
        );
        $infura->ignoreSSL = true;
        $this->infuraRpc = $infura;

        $this->deployedContract = $factory->deployedAt($this->infuraRpc, $contract, $this->ethereum->getAddress($this->contractAddress));
        
    }

    
    public function getUserCredentials($userID)
    {
        try {
            // Call the getUserCredential function from the contract
            $res = $this->deployedContract->call('getAllUserCredentialsByUserID', [$userID]);
            $result = $this->decodeUserCredentialsResponse($res, $userID);
            return $result;
        } catch (\Exception $e) {
            Log::error('Error calling getUserCredential: ' . $e->getMessage());
            throw new \Exception('Failed to retrieve user credential: ' . $e->getMessage());
        }
    }

    public function saveUserCredentialByApi($userCredentialID, $userID, $userName, $password, $site_url, $site_name){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => env('NODE_SERVER_URL') . "user/save",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
            "credentialID" : ' . $userCredentialID . ',
            "userID"   :  ' . $userID     . ',
            "userName" : "' . $userName   . '",
            "password" : "' . $password   . '",
            "domain"   : "' . $site_url   . '",
            "website"  : "' . $site_name  . '" 
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public function updateUserCredentialByApi($userCredentialID, $userID, $userName, $password, $site_url, $site_name){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => env('NODE_SERVER_URL') . "user/update",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_POSTFIELDS => '{
            "credentialID" : ' . $userCredentialID . ',
            "userID"   :  ' . $userID     . ',
            "userName" : "' . $userName   . '",
            "password" : "' . $password   . '",
            "domain"   : "' . $site_url   . '",
            "website"  : "' . $site_name  . '" 
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public function deleteUserCredentialByApi($userCredentialID, $userID){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => env('NODE_SERVER_URL') . "user/delete",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'DELETE',
        CURLOPT_POSTFIELDS => '{
            "credentialID" : ' . $userCredentialID . ',
            "userID" : ' . $userID . '
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }




    public function getGroupUserCredentials($groupProjectID)
    {
        try {
            // Call the getUserCredential function from the contract
            $res = $this->deployedContract->call('getAllGroupCredentialsByGroupProjectID', [$groupProjectID]);
            $result = $this->decodeGroupCredentialsResponse($res, $groupProjectID);
            return $result;
        } catch (\Exception $e) {
            Log::error('Error calling getUserCredential: ' . $e->getMessage());
            throw new \Exception('Failed to retrieve user credential: ' . $e->getMessage());
        }
    }

    public function saveGroupCredentialByApi($groupProjectCredentialID, $groupProjectID, $userName, $password, $site_url, $site_name){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => env('NODE_SERVER_URL') . "group/save",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
            "groupProjectCredentialID" : ' . $groupProjectCredentialID . ',
            "groupProjectID"   :  ' . $groupProjectID     . ',
            "userName" : "' . $userName   . '",
            "password" : "' . $password   . '",
            "domain"   : "' . $site_url   . '",
            "website"  : "' . $site_name  . '" 
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public function updateGroupCredentialByApi($groupProjectCredentialID, $groupProjectID, $userName, $password, $site_url, $site_name){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => env('NODE_SERVER_URL') . "group/update",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_POSTFIELDS => '{
            "groupProjectCredentialID" : ' . $groupProjectCredentialID . ',
            "groupProjectID"   :  ' . $groupProjectID     . ',
            "userName" : "' . $userName   . '",
            "password" : "' . $password   . '",
            "domain"   : "' . $site_url   . '",
            "website"  : "' . $site_name  . '" 
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public function deleteGroupCredentialByApi($groupProjectCredentialID, $groupProjectID){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => env('NODE_SERVER_URL') . "group/delete",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'DELETE',
        CURLOPT_POSTFIELDS => '{
            "groupProjectCredentialID" : ' . $groupProjectCredentialID . ',
            "groupProjectID" : ' . $groupProjectID . '
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }






    /**
     * Decode the eth_call response for getAllUserCredentialsByUserID
     *
     * @param string $hexResponse The hex response from the Ethereum call
     * @return array Decoded credentials array
     */
    private function decodeUserCredentialsResponse(string $hexResponse, int $userID): array
    {
        // Remove '0x' prefix if present
        if (str_starts_with($hexResponse, '0x')) {
            $hexResponse = substr($hexResponse, 2);
        }
        // \Log::warning("hexResponse {$hexResponse}");
        
        // Convert to lowercase for consistency
        $hex = strtolower($hexResponse);
        
        // Get array data offset (first 32 bytes/64 hex chars)
        $dataOffset = $this->hexToInt(substr($hex, 0, 64));
        // \Log::info("dataOffset {$dataOffset}");

        // Get array length at the offset position
        $arrayLengthHex = substr($hex, $dataOffset * 2, 64); 
        // \Log::info("arrayLengthHex {$arrayLengthHex}");
        
        $arrayLength = $this->hexToInt($arrayLengthHex);
        \Log::info("Found {$arrayLength} credentials in response");
        
        $credentials = [];
        $position = $dataOffset + 32; // Start position of tuple offsets (after array length)
        \Log::info("initial position {$position}");

        for ($i = 0; $i < $arrayLength; $i++) {
            // Get offset to this tuple's data
            $tupleOffsetHex = substr($hex, $position * 2, 64);
            // \Log::warning("tupleOffsetHex {$tupleOffsetHex}");
            $tupleOffset = $this->hexToInt($tupleOffsetHex);
            // \Log::warning("tupleOffset {$tupleOffset}.");
            
            // Actual position where this tuple's data starts
            $tupleStart = $dataOffset + 32 + $tupleOffset;
            // \Log::warning("tupleStart {$tupleStart}.");
            
            $position += 32; // Move to next tuple offset
            // \Log::warning("{$i} position {$position}.");

            // Decode credential ID (first field of tuple)
            $credentialIdHex = substr($hex, $tupleStart * 2, 64);
            // \Log::warning("credentialIdHex {$credentialIdHex}.");
            $credentialId = $this->hexToInt($credentialIdHex);
            // \Log::warning("credentialId {$credentialId}.");
            
            // Get offsets to string data (relative to tuple start)
            $userNameOffsetHex = substr($hex, ($tupleStart + 32) * 2, 64);
            $passwordOffsetHex = substr($hex, ($tupleStart + 64) * 2, 64);
            $domainOffsetHex = substr($hex, ($tupleStart + 96) * 2, 64);
            $websiteOffsetHex = substr($hex, ($tupleStart + 128) * 2, 64);
            // \Log::warning("userNameOffsetHex {$userNameOffsetHex} passwordOffsetHex {$passwordOffsetHex} domainOffsetHex {$domainOffsetHex}.");

            // Get version/status (last field of tuple)
            $versionHex = substr($hex, ($tupleStart + 160) * 2, 64);
            $version = $this->hexToInt($versionHex);
            // \Log::warning("version {$version}");
            
            // Calculate actual positions of string data
            $userNamePos = $tupleStart + $this->hexToInt($userNameOffsetHex);
            $passwordPos = $tupleStart + $this->hexToInt($passwordOffsetHex);
            $domainPos = $tupleStart + $this->hexToInt($domainOffsetHex);
            $websitePos = $tupleStart + $this->hexToInt($websiteOffsetHex);
            // \Log::warning("userNamePos {$userNamePos} passwordPos {$passwordPos} domainPos {$domainPos}.");
            
            // Decode strings
            $userName = $this->decodeString($hex, $userNamePos);
            $password = $this->decodeString($hex, $passwordPos);
            $domain = $this->decodeString($hex, $domainPos);
            $website = $this->decodeString($hex, $websitePos);
            // \Log::warning("userName {$userName} password {$password} domain {$domain}.");
            
            // Add to result array
            $credentials[] = [
                'id' => $credentialId,
                'site_name' => $website,
                'username' => $userName,
                'password' => $password,
                'site_url' => $domain,
                // 'version' => $version,
                'fk_user_id' => $userID
            ];
        }
        
        return $credentials;
    }
    
    /**
     * Decode the eth_call response for getAllUserCredentialsByUserID
     *
     * @param string $hexResponse The hex response from the Ethereum call
     * @return array Decoded credentials array
     */
    private function decodeGroupCredentialsResponse(string $hexResponse, int $groupProjectId): array
    {
        // Remove '0x' prefix if present
        if (str_starts_with($hexResponse, '0x')) {
            $hexResponse = substr($hexResponse, 2);
        }
        // \Log::warning("hexResponse {$hexResponse}");
        
        // Convert to lowercase for consistency
        $hex = strtolower($hexResponse);
        
        // Get array data offset (first 32 bytes/64 hex chars)
        $dataOffset = $this->hexToInt(substr($hex, 0, 64));
        // \Log::info("dataOffset {$dataOffset}");

        // Get array length at the offset position
        $arrayLengthHex = substr($hex, $dataOffset * 2, 64); 
        // \Log::info("arrayLengthHex {$arrayLengthHex}");
        
        $arrayLength = $this->hexToInt($arrayLengthHex);
        \Log::info("Found {$arrayLength} credentials in response");
        
        $credentials = [];
        $position = $dataOffset + 32; // Start position of tuple offsets (after array length)
        // \Log::info("initial position {$position}");

        for ($i = 0; $i < $arrayLength; $i++) {
            // Get offset to this tuple's data
            $tupleOffsetHex = substr($hex, $position * 2, 64);
            // \Log::warning("tupleOffsetHex {$tupleOffsetHex}");
            $tupleOffset = $this->hexToInt($tupleOffsetHex);
            // \Log::warning("tupleOffset {$tupleOffset}.");
            
            // Actual position where this tuple's data starts
            $tupleStart = $dataOffset + 32 + $tupleOffset;
            // \Log::warning("tupleStart {$tupleStart}.");
            
            $position += 32; // Move to next tuple offset
            // \Log::warning("{$i} position {$position}.");

            // Decode credential ID (first field of tuple)
            $credentialIdHex = substr($hex, $tupleStart * 2, 64);
            // \Log::warning("credentialIdHex {$credentialIdHex}.");
            $credentialId = $this->hexToInt($credentialIdHex);
            // \Log::warning("credentialId {$credentialId}.");
            
            // Get offsets to string data (relative to tuple start)
            $userNameOffsetHex = substr($hex, ($tupleStart + 32) * 2, 64);
            $passwordOffsetHex = substr($hex, ($tupleStart + 64) * 2, 64);
            $domainOffsetHex = substr($hex, ($tupleStart + 96) * 2, 64);
            $websiteOffsetHex = substr($hex, ($tupleStart + 128) * 2, 64);
            // \Log::warning("userNameOffsetHex {$userNameOffsetHex} passwordOffsetHex {$passwordOffsetHex} domainOffsetHex {$domainOffsetHex}.");

            // Get version/status (last field of tuple)
            $versionHex = substr($hex, ($tupleStart + 160) * 2, 64);
            $version = $this->hexToInt($versionHex);
            // \Log::warning("version {$version}");
            
            // Calculate actual positions of string data
            $userNamePos = $tupleStart + $this->hexToInt($userNameOffsetHex);
            $passwordPos = $tupleStart + $this->hexToInt($passwordOffsetHex);
            $domainPos = $tupleStart + $this->hexToInt($domainOffsetHex);
            $websitePos = $tupleStart + $this->hexToInt($websiteOffsetHex);
            // \Log::warning("userNamePos {$userNamePos} passwordPos {$passwordPos} domainPos {$domainPos}.");
            
            // Decode strings
            $userName = $this->decodeString($hex, $userNamePos);
            $password = $this->decodeString($hex, $passwordPos);
            $domain = $this->decodeString($hex, $domainPos);
            $website = $this->decodeString($hex, $websitePos);
            // \Log::warning("userName {$userName} password {$password} domain {$domain}.");
            
            // Add to result array
            $credentials[] = [
                'id' => $credentialId,
                'site_name' => $website,
                'username' => $userName,
                'password' => $password,
                'site_url' => $domain,
                // 'version' => $version,
                'fk_group_project_id' => $groupProjectId
            ];
        }
        
        return $credentials;
    }

    /**
     * Convert hex string to integer, safely handling large numbers
     *
     * @param string $hex Hex string without 0x prefix
     * @return int Integer value
     */
    private function hexToInt(string $hex): int
    {
        // Use gmp extension for large numbers if available
        if (function_exists('gmp_init')) {
            $num = gmp_init('0x' . $hex);
            // If number is too large for PHP int, cap it
            if (gmp_cmp($num, PHP_INT_MAX) > 0) {
                $maxNum = PHP_INT_MAX;
                \Log::warning("Number {$num} exceeds PHP_INT_MAX {$maxNum}. Using maximum value.");
                return PHP_INT_MAX;
            }
            return gmp_intval($num);
        }
        
        // Fallback to built-in conversion
        $result = @hexdec($hex);
        
        // Check for overflow
        if ($result === INF || is_float($result)) {
            \Log::warning("Number overflow in hexToInt. Using maximum value.");
            return PHP_INT_MAX;
        }
        
        return $result;
    }
    
    /**
     * Decode an ABI-encoded string
     *
     * @param string $hex Full hex data
     * @param int $position Position where string data starts
     * @return string Decoded string
     */
    private function decodeString(string $hex, int $position): string
    {
        // Get string length (first 32 bytes at position)
        $lengthHex = substr($hex, $position * 2, 64);
        $length = $this->hexToInt($lengthHex);
        
        // Ensure length is reasonable
        if ($length > 1024 * 1024) { // Cap at 1MB to prevent DoS
            \Log::warning("String length too large: {$length}. Capping at 1MB");
            $length = 1024 * 1024;
        }
        
        // Calculate length in hex chars (2 chars per byte)
        $hexLength = $length * 2;
        
        // Extract string data bytes
        $stringHex = substr($hex, ($position + 32) * 2, $hexLength);
        
        if ($stringHex === false) {
            \Log::error("Failed to extract string data at position {$position}");
            return '';
        }
        
        // Convert hex bytes to UTF-8 string
        $result = '';
        for ($i = 0; $i < strlen($stringHex); $i += 2) {
            if ($i + 1 >= strlen($stringHex)) break; // Avoid errors with incomplete byte
            $charCode = hexdec(substr($stringHex, $i, 2));
            $result .= chr($charCode);
        }
        
        return $result;
    }
  
    // Add other methods for interacting with different functions as needed (update, delete, etc.)
}