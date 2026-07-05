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

        function displayForm($Name, $Email, $Topic, $Message) {
            ?><h2 style='text-align:center'>Send Message</h2>
            <form name='message' action='' method='post'>
                <input type='hidden' name='_token' value='<?php echo csrf_token(); ?>'>
                <p>Full name:
                    <input type='text' name='Name' value='<?php echo $Name; ?>' /></p>
                <p>Email address:
                    <input type='text' name='Email' value='<?php echo $Email; ?>' /></p>
                <p>Topic of message:
                    <input type='text' name='Topic' value='<?php echo $Topic; ?>' /></p>
                <p>Message:<br />
                    <textarea name='Message'><?php echo $Message; ?></textarea></p>
                <p><input type='reset' value='Clear Form' />&nbsp; &nbsp;
                    <input type='submit' name='Submit' value='Send Form' /></p>
            </form>
        <?php }