<?php
date_default_timezone_set('UTC');

class Zoom
{
    // ...

    public function createMeeting($username, $dateTime, $userTimezone = 'America/Los_Angeles')
    {
        // Set your Zoom API credentials
        $accountID = 'q2zXPLjgQCCbepKpOY2sxw';
        $clientID = 'hcnUDgf5QaatR7yiddsZUw';
        $clientSecret = 'eVrxPEkkvmQeU1GIriVVDbAD93n6f54O';

        // Prepare the authorization header
        $authHeader = base64_encode($clientID . ':' . $clientSecret);

        // Prepare the POST fields
        $postFields = http_build_query([
            'grant_type' => 'account_credentials',
            'account_id' => $accountID,
        ]);

        // Initialize a CURL session
        $ch = curl_init();

        // Set the CURL options
        curl_setopt($ch, CURLOPT_URL, "https://zoom.us/oauth/token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $authHeader, 'Content-Type: application/x-www-form-urlencoded']);

        // Execute the CURL session and fetch the response
        $response = curl_exec($ch);

        // Close the CURL session
        curl_close($ch);

        // Decode the response
        $response = json_decode($response);

        // Check if we got an access token
        if (isset($response->access_token)) {
            $accessToken = $response->access_token;

            $meetingDetails = [
                'topic' => 'My Language Tutor',
                'type' => 2,
                'start_time' => date('Y-m-d\TH:i:s', strtotime($dateTime)), // Updated here
                'duration' => 60,
                'timezone' => $userTimezone,
            ];

            // Initialize another CURL session
            $ch = curl_init();

            // Set the CURL options for creating a meeting
            curl_setopt($ch, CURLOPT_URL, "https://api.zoom.us/v2/users/me/meetings");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($meetingDetails));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken, 'Content-Type: application/json']);

            // Execute the CURL session and fetch the response
            $response = curl_exec($ch);

            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // Close the CURL session
            curl_close($ch);

            if ($httpcode == 201) {
                // Decode the response
                $response = json_decode($response);

                // Check if we got the join_url of the meeting
                if (isset($response->join_url)) {
                    // Return the join_url
                    return $response->join_url;
                } else {
                    throw new Exception("Failed to create the Zoom meeting");
                }
            } else {
                throw new Exception("Failed to create the Zoom meeting. HTTP response code: $httpcode");
            }
        } else {
            throw new Exception("Failed to obtain the Zoom access token");
        }
    }
}
?>
