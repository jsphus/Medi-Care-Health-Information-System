<?php
require_once __DIR__ . '/../config/config.php';

class CloudinaryUpload {
    private $cloudName;
    private $apiKey;
    private $apiSecret;
    private $uploadUrl;
    
    public function __construct() {
        $this->cloudName = CLOUDINARY_CLOUD_NAME;
        $this->apiKey = CLOUDINARY_API_KEY;
        $this->apiSecret = CLOUDINARY_API_SECRET;
        $this->uploadUrl = "https://api.cloudinary.com/v1_1/{$this->cloudName}/image/upload";
    }
    
    /**
     * Upload image to Cloudinary
     * @param array $file $_FILES array element
     * @param string $folder Folder path on Cloudinary
     * @param int $userId User ID for public_id
     * @return array|string Returns array with 'url' and 'public_id' on success, error message string on failure
     */
    public function uploadImage($file, $folder = 'profile_pictures', $userId = null) {
        // Check if Cloudinary credentials are configured
        if (empty($this->cloudName) || empty($this->apiKey) || empty($this->apiSecret)) {
            return 'Cloudinary credentials are not configured. Please check your .env file.';
        }
        
        // Check if file was uploaded
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            $uploadError = $file['error'] ?? 'Unknown error';
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive',
                UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
            ];
            return $errorMessages[$uploadError] ?? 'File upload error: ' . $uploadError;
        }
        
        // Validate file size (5MB max)
        if ($file['size'] > 5 * 1024 * 1024) {
            return 'File size exceeds 5MB limit. Current size: ' . round($file['size'] / 1024 / 1024, 2) . 'MB';
        }
        
        // Validate file type by extension
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedExtensions)) {
            return 'Invalid file type. Allowed: ' . implode(', ', $allowedExtensions) . '. Got: ' . $fileExtension;
        }
        
        // Validate file type by MIME type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = mime_content_type($file['tmp_name']);
        
        // Also check using finfo if available
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $fileType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
        }
        
        if (!in_array($fileType, $allowedTypes)) {
            return 'Invalid MIME type. Allowed: ' . implode(', ', $allowedTypes) . '. Got: ' . $fileType;
        }
        
        // Generate public_id with folder included (folder should be in public_id, not as separate parameter for signed uploads)
        $timestamp = time();
        $publicId = $userId ? "{$folder}/user_{$userId}_{$timestamp}" : "{$folder}/upload_{$timestamp}";
        
        // Generate signature for authentication
        // Note: file, api_key, resource_type, and signature itself are NOT included in signature calculation
        // Folder is included in public_id, so it's part of the signature through public_id
        // According to Cloudinary docs, resource_type is not included in signature for image uploads
        $signParams = [
            'timestamp' => (string)$timestamp,
            'public_id' => $publicId,
            'overwrite' => 'true'
        ];
        
        // Create signature
        $signature = $this->generateSignature($signParams);
        
        // Build final params array for upload
        // Note: We do NOT send folder as a separate parameter when using signed uploads
        // The folder is included in the public_id instead
        $params = [
            'file' => null, // Will be set below
            'api_key' => $this->apiKey,
            'timestamp' => (string)$timestamp,
            'public_id' => $publicId,
            'overwrite' => 'true',
            'resource_type' => 'image',
            'signature' => $signature
        ];
        
        // Prepare file for upload (must be last in array for multipart/form-data)
        $cfile = new CURLFile($file['tmp_name'], $fileType, $file['name']);
        
        // Remove the null placeholder and add file at the end
        unset($params['file']);
        $params['file'] = $cfile;
        
        // Upload to Cloudinary
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->uploadUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($curlError) {
            return 'cURL error: ' . $curlError;
        }
        
        if ($httpCode === 200) {
            $result = json_decode($response, true);
            if ($result && isset($result['secure_url'])) {
                return [
                    'url' => $result['secure_url'],
                    'public_id' => $result['public_id']
                ];
            } else {
                return 'Invalid response from Cloudinary: ' . $response;
            }
        } else {
            $errorData = json_decode($response, true);
            $errorMessage = isset($errorData['error']['message']) ? $errorData['error']['message'] : 'Unknown error';
            return 'Cloudinary API error (HTTP ' . $httpCode . '): ' . $errorMessage;
        }
    }
    
    /**
     * Delete image from Cloudinary
     * @param string $publicId Public ID of the image to delete
     * @return bool True on success, false on failure
     */
    public function deleteImage($publicId) {
        if (empty($publicId)) {
            return false;
        }
        
        $timestamp = time();
        $params = [
            'timestamp' => $timestamp,
            'public_id' => $publicId
        ];
        
        $signature = $this->generateSignature($params);
        $params['signature'] = $signature;
        $params['api_key'] = $this->apiKey;
        
        $deleteUrl = "https://api.cloudinary.com/v1_1/{$this->cloudName}/image/destroy";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $deleteUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode === 200;
    }
    
    /**
     * Generate transformed URL for image
     * @param string $publicId Public ID of the image
     * @param array $options Transformation options
     * @return string Transformed image URL
     */
    public function transformImage($publicId, $options = []) {
        $defaultOptions = [
            'width' => 400,
            'height' => 400,
            'crop' => 'fill',
            'gravity' => 'face',
            'quality' => 'auto',
            'format' => 'auto'
        ];
        
        $options = array_merge($defaultOptions, $options);
        
        $transformations = [];
        if (isset($options['width']) && isset($options['height'])) {
            $transformations[] = "w_{$options['width']},h_{$options['height']}";
        }
        if (isset($options['crop'])) {
            $transformations[] = "c_{$options['crop']}";
        }
        if (isset($options['gravity'])) {
            $transformations[] = "g_{$options['gravity']}";
        }
        if (isset($options['quality'])) {
            $transformations[] = "q_{$options['quality']}";
        }
        if (isset($options['format'])) {
            $transformations[] = "f_{$options['format']}";
        }
        
        $transformationString = implode(',', $transformations);
        $baseUrl = "https://res.cloudinary.com/{$this->cloudName}/image/upload";
        
        return "{$baseUrl}/{$transformationString}/{$publicId}";
    }
    
    /**
     * Generate signature for Cloudinary API authentication
     * @param array $params Parameters to sign (should NOT include folder, file, or signature)
     * @return string Signature
     */
    private function generateSignature($params) {
        // Remove any parameters that should not be in signature
        $signParams = $params;
        unset($signParams['file']);
        unset($signParams['signature']);
        unset($signParams['folder']);
        unset($signParams['api_key']);
        
        // Sort parameters alphabetically by key
        ksort($signParams);
        
        // Create string to sign: param1=value1&param2=value2&...&api_secret
        $stringToSign = '';
        foreach ($signParams as $key => $value) {
            $stringToSign .= $key . '=' . $value . '&';
        }
        $stringToSign = rtrim($stringToSign, '&');
        $stringToSign .= $this->apiSecret;
        
        // Generate SHA1 hash
        return sha1($stringToSign);
    }
    
    /**
     * Extract public_id from Cloudinary URL
     * @param string $url Cloudinary URL
     * @return string|false Public ID or false if not found
     */
    public function extractPublicId($url) {
        if (empty($url)) {
            return false;
        }
        
        // Match Cloudinary URL pattern
        $pattern = '/\/v\d+\/(.+?)(?:\.[^.]+)?$/';
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
        
        // Try alternative pattern for transformed URLs
        $pattern = '/\/([^\/]+?)(?:\.[^.]+)?$/';
        if (preg_match($pattern, $url, $matches)) {
            // Remove transformation parameters if present
            $publicId = preg_replace('/^[^\/]+\//', '', $matches[1]);
            return $publicId;
        }
        
        return false;
    }
}

