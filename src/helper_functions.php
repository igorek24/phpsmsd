<?php declare(strict_types=1);
function yearsToCurrent($date = null): int|string
{
    $date = (empty($date)) ? "01/01/2010" : $date;
    $date = explode("/", $date);
    return (date("Y") - $date[2]);
}

function domainFromEmail($email)
{
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $emailArr = explode('@', $email);

        $domain = array_pop($emailArr);

        return $domain;
    }
    return false;
}

function validateMXRecord($domain)
{
    if (checkdnsrr($domain, 'MX')) {
        return true;
    }
    return false;
}

function validateEmailAddress($email)
{
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        if (validateMXRecord(domainFromEmail($email))) {
            return true;
        }
    }
    return false;
}

function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

/**
 * @param $string
 * @return array|string|null
 * Remove all special characters from a string
 */
function cleanString($string, $spaceChar = ''): array|string|null
{
    $string = str_replace(' ', $spaceChar, $string);
    return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}

function randomString($length = 10, $specialChars = false, $characters = null): ?string
{
    //Under the string $Characters you write all the characters you want to be used to randomly generate the code.
    if ($specialChars === true) {
        $specialChars = "!@#$%^&*()_+=-/?><";
    } elseif ($specialChars === false) {
        $specialChars = null;
    }
    //Under the string $Characters you write all the characters you want to be used to randomly generate the code.
    if ($characters === null) {
        $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ" . $specialChars;
    }

    $quantedaCharacters = strlen($characters);
    $quantedaCharacters--;

    $Hash = NULL;
    for ($x = 1; $x <= $length; $x++) {
        $posicao = rand(0, $quantedaCharacters);
        $Hash .= substr($characters, $posicao, 1);
    }

    return $Hash;
}

function htmlEntitiesToUtf8($string): string
{
    return html_entity_decode($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function encryptText($textToEncrypt, $encryptionMethod = null, $encryptionHash = null): array|string
{
    if (empty($encryptionMethod)) {
        $encryptionMethod = $_ENV['APP_VAR_ENCRYPTION_METHOD'];
    }
    if (empty($encryptionHash)) {
        $encryptionHash = $_ENV['APP_VAR_ENCRYPTION_HASH'];
    }
    if (is_array($textToEncrypt)) {
        $encryptedArrVol = [];
        foreach ($textToEncrypt as $key => $value) {
            if (is_bool($value) || $value == "" || $value == null) {
                $encryptedArrVol[$key] = $value;
            } else {
                $iv = randomString(16);
                $encryptedValue = openssl_encrypt($value, $encryptionMethod, $encryptionHash, 0, $iv);
                $encryptedArrVol[$key] = base64_encode($iv . $encryptedValue);
            }

        }
        return $encryptedArrVol;
    } else {
        $iv = randomString(16);
        $encryptedText = openssl_encrypt($textToEncrypt, $encryptionMethod, $encryptionHash, 0, $iv);
        return base64_encode($iv . $encryptedText);
    }
}

function decryptText($encryptedText, $encryptionMethod = null, $encryptionHash = null): string|array|false
{
    if (empty($encryptionMethod)) {
        $encryptionMethod = $_ENV['APP_VAR_ENCRYPTION_METHOD'];
    }
    if (empty($encryptionHash)) {
        $encryptionHash = $_ENV['APP_VAR_ENCRYPTION_HASH'];
    }
    if (is_array($encryptedText)) {
        $decryptedArrVol = [];
        foreach ($encryptedText as $key => $value) {
            if (is_bool($value) || $value == "" || $value == null) {
                $decryptedArrVol[$key] = $value;
            } else {
                $encryptedValue = base64_decode($value);
                $iv = substr($encryptedValue, 0, 16);
                $decryptedMessage = openssl_decrypt(substr($encryptedValue, 16), $encryptionMethod, $encryptionHash, 0, $iv);
                $decryptedArrVol[$key] = $decryptedMessage;
            }
        }
        return $decryptedArrVol;
    } else {
        $encryptedText = base64_decode($encryptedText);
        $iv = substr($encryptedText, 0, 16);
        return openssl_decrypt(substr($encryptedText, 16), $encryptionMethod, $encryptionHash, 0, $iv);
    }
}
function validatePhoneNumber($phone)
{
    // Allow +, - and . in phone number
    $filteredPhoneNumber= filter_var($phone, FILTER_SANITIZE_NUMBER_INT);
    // Remove "-" from number
    $phoneToCheck = str_replace("-", "", $filteredPhoneNumber);
    // Check the lenght of number
    // This can be customized if you want phone number from a specific country
    if (strlen($phoneToCheck) < 10 || strlen($phoneToCheck) > 14) {
        return false;
    } else {
        return true;
    }
}