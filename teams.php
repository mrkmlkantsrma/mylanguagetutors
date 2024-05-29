
<?php
// require_once 'header.php';

if(session_status() == PHP_SESSION_NONE) { session_start(); }

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

$pathroot = __DIR__; 
require_once 'src/controllers/UserController.php';
require_once $pathroot . '/src/models/User.php';

require_once $pathroot . '/vendor/autoload.php';

use Microsoft\Graph\Model;

$guzzle = new \GuzzleHttp\Client();

$tenantId = $_ENV['MICROSOFT_TENANT_ID'];
$clientId = $_ENV['MICROSOFT_CLIENT_ID'];
$clientSecret = $_ENV['MICROSOFT_SECRET_KEY'];
$responseUri = $_ENV['MICROSOFT_REDIRECT_URL'];


$url = 'https://login.microsoftonline.com/' . $tenantId . '/oauth2/token?api-version=1.0';
$token = json_decode($guzzle->post($url, [
    'form_params' => [
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'resource' => 'https://graph.microsoft.com/',
        'grant_type' => 'client_credentials',
    ],
])->getBody()->getContents());
$accessToken = $token->access_token;

$date = $_POST['date'];
$time = $_POST['time'];

$datetime = $date . 'T' . $time . ':00';

// Create DateTime object
$dt = new DateTime($datetime, new DateTimeZone('America/Los_Angeles'));

$formattedStartDateTime = $dt->format('Y-m-d\TH:i:s.uP');

$dt->modify('+60 days');

$formatteEnddDateTime = $dt->format('Y-m-d\TH:i:s.uP');


$curl = curl_init();

curl_setopt_array($curl, array(
CURLOPT_URL => 'https://graph.microsoft.com/v1.0/users/e0b1353f-d911-4a1e-b76d-4f6c72b39ece/onlineMeetings',
CURLOPT_RETURNTRANSFER => true,
CURLOPT_ENCODING => '',
CURLOPT_MAXREDIRS => 10,
CURLOPT_TIMEOUT => 0,
CURLOPT_FOLLOWLOCATION => true,
CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
CURLOPT_CUSTOMREQUEST => 'POST',
CURLOPT_POSTFIELDS =>'{
"startDateTime":"'.$formattedStartDateTime.'",
"endDateTime":"'.$formatteEnddDateTime.'",
"subject":"Online Meeting",
"participants": {
        "attendees": [
            {
                "identity": {
                    "user": {
                        "id": "e0b1353f-d911-4a1e-b76d-4f6c72b39ece"
                    }
                },
                "upn": "mavis.chan93@gmail.com"
            }
        ]
    }
}',
CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'Authorization: Bearer ' . $accessToken .''
),
));

$response = curl_exec($curl);

curl_close($curl);

$MeetingData = json_decode($response);

echo json_encode([
    'meeting_link' => $MeetingData->joinWebUrl,
]);

die;

