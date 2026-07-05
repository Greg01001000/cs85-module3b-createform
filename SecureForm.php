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
                    echo "\"$retVal\" is not a valid email address. <br />\n";
                    ++$errorCount;                    
                }
            }
            return($retVal);
        }

        function displayForm($Name, $Email, $Topic, $Message) {
            ?><h2 style='text-align:center'>Send Message</h2>
            <form name='message' action='' method='post'>
                <input type='hidden' name='_token' value='<?php echo csrf_token(); ?>'>
                <p>Your full name:
                    <input type='text' name='Name' value='<?php echo $Name; ?>' /></p>
                <p>Your email address:
                    <input type='text' name='Email' value='<?php echo $Email; ?>' /></p>
                <p>Topic of your message:
                    <input type='text' name='Topic' value='<?php echo $Topic; ?>' /></p>
                <p>Your message:<br />
                    <textarea name='Message'><?php echo $Message; ?></textarea></p>
                <p><input type='reset' value='Clear Form' />&nbsp; &nbsp;
                    <input type='submit' name='Submit' value='Send Form' /></p>
            </form>
        <?php }

        $ShowForm = TRUE;
        global $errorCount;
        $errorCount = 0;
        $Name = '';
        $Email = '';
        $Topic = '';
        $Message = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $Name = validateInput($_POST['Name'],'Your full name');
            $Email = validateEmail($_POST['Email'],'Your email address');
            $Topic = validateInput($_POST['Topic'],'Topic of message');
            $Message = validateInput($_POST['Message'],'Your message');
            if ($errorCount == 0)
                $ShowForm = FALSE;
            else
                $ShowForm = TRUE;
        }

        /* OUTPUT PREDICTIONS: 1st load should show the HTML form with the centered 
        title, 'Send Message', in h2 style, and four empty fields arranged vertically, 
        labeled 'Your full name:','Your email address:', 'Topic of your message:', and 
        'Your message:'. The last field is a textarea; the other fields are simple text
        boxes. It should show two buttons at the bottom, labeled 'Clear Form' and 
        'Submit'. If the user submits incomplete input and/or an invalid email address,
        it should show a message for each invalid or empty field, saying that the field
        is required, or for the 'Email' field, possibly saying the submitted email 
        address is invalid, while showing the submitted data in each field. If data are
        submitted in all fields and the email address appears valid, it should send the
        user-submitted message as an email to both the recipient and the user and show
        a status message on the web page saying whether the email was able to be sent.
        
        In $_POST, after the user submits the form, I expect to see a 6-element 
        associative array with keys of 'Name', 'Email', 'Topic', 'Message', 'Submit',
        and '_token'. The first four elements' values should be the corresponding user-
        submitted data. The 'Submit' value should be 'Send Form'. The '_token' value
        should be a randomly-generated string unique to the user's session and matching
        the token stored on our server for the session. */

        if ($ShowForm == TRUE) {
            if ($errorCount>0)
                echo "<p>Please re-enter the form information below.</p>\n";
            displayForm($Name, $Email, $Topic, $Message);
        } else {
            $SenderAddress = "$Name <$Email>";
            $Headers = "From: $SenderAddress\nCC: $SenderAddress\n";

            $result = mail('recipient@example.com', $Topic, $Message, $Headers);

            if ($result)
                echo "<p>Your message has been sent. Thank you, " . $Name . ".</p>\n";
            else
                echo "<p>There was an error sending your message, " . $Name . ".</p>\n";

        }
    ?>
</body>
</html>