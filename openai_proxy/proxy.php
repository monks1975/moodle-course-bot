<?php

// Load API key from file
$apiKeyFilePath = '/api_key.txt';
$apiKey = trim(file_get_contents($apiKeyFilePath));

// Get the path from the incoming request and sanitize it
$requestPath = ltrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Initialize cURL session
$ch = curl_init("https://api.openai.com/{$requestPath}");

// Read incoming request payload
$incomingPayload = file_get_contents('php://input');

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $_SERVER['REQUEST_METHOD']); // Pass through the HTTP method (GET, POST, etc.)
curl_setopt($ch, CURLOPT_POSTFIELDS, $incomingPayload);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey,
]);

// Execute and capture the response
$response = curl_exec($ch);

// Close cURL session
curl_close($ch);

// Send the response back to the client
header('Content-Type: application/json');
echo $response;
