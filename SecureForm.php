<!--CS 85 Module 3, Assignment 3B by Gregory Hagen 7/3/26-->
<!DOCTYPE HTML>
<html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <title>Secure Contact Form</title>
    </head>
<body>
    <?php
        function validateInput($data, $fieldName) {
            global $errorCount;
            // If $data only has spaces and/or slashes, treat it as empty
            $retVal = trim(stripslashes($data)); 
            if (empty($retVal)) {
                echo "\"$fieldName\" is a required field.<br />\n";
                ++$errorCount; $retVal = '';
            }
            else {
                $retVal = htmlspecialchars($retVal);
            }
            return($retVal); }

        function validateEmail($data, $fieldName) {
            global $errorCount;
            if (empty(trim(stripslashes(strip_tags($data))))) {
                echo "\"$fieldName\" is a required field.<br />\n";
                ++$errorCount; $retVal = '';
            }
            else {
                $retVal = filter_var($data, FILTER_SANITIZE_EMAIL);
                if (!filter_var($retVal, FILTER_VALIDATE_EMAIL)) {
                    echo "\"$data\" is not a valid email address. <br />\n";
                    ++$errorCount;
                }
            }
            return($retVal);
        }

        