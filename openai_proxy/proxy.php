<?php
// Define the OpenAI API key and endpoint
$openaiApiKey = "your-openai-api-key-here";
$openaiEndpoint = "https://api.openai.com";

// Capture the incoming request payload and headers
$incomingPayload = file_get_contents('php://input');
$headers = getallheaders();

// Remove any Authorization header that might have come from the client
unset($headers['Authorization']);

// Initialize a cURL session
$ch = curl_init();

// Create a proper URL based on the incoming request
$requestUri = $_SERVER['REQUEST_URI'];
$forwardingUrl = $openaiEndpoint . str_replace("/your-proxy-base-url", "", $requestUri);

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $forwardingUrl);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $_SERVER['REQUEST_METHOD']);
curl_setopt($ch, CURLOPT_POSTFIELDS, $incomingPayload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . $openaiApiKey,
    "Content-Type: " . $headers["Content-Type"],
]);

// Execute the cURL request and capture the response
$response = curl_exec($ch);

// Forward the cURL response back to the client
echo $response;

// Close the cURL session
curl_close($ch);
