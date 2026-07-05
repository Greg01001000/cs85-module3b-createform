<!--CS 85 Module 3, Assignment 3B by Gregory Hagen 7/3/26-->
<!DOCTYPE HTML>
<html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <title>Secure Contact Form</title>
    </head>
<body>
    <?php
        /* Remove backslashes and leading & trailing spaces from the user-submitted 
        field. Next, if it's empty (including if it only had backslashes and spaces), 
        notify the user that it's required and keep count of the error. If not empty, 
        convert HTML special characters to harmless versions to prevent XSS attacks and
        return the cleaned data field. */
        function validateInput($data, $fieldName) {
            global $errorCount;
            // If $data only has spaces and/or backslashes, treat it as empty
            $retVal = trim(stripslashes($data)); 
            if (empty($retVal)) {
                echo "\"$fieldName\" is a required field.<br />\n"; //Tell user
                ++$errorCount;  // Keep track of the error
                $retVal = "";   // Set the data value this function will return
            }
            else { // If not empty, convert HTML special characters to harmless versions
                $retVal = htmlspecialchars($retVal);
            }
            return($retVal); }  // Return the cleaned or empty data

        /* If user-supplied email address is empty or contains only HTML tags, 
        backslashes, and/or spaces, notify the user that it's required and keep count of 
        the error. If not empty, remove extra characters and check for valid email 
        address format. Return cleaned field data. */
        function validateEmail($data, $fieldName) {
            global $errorCount;
            // If $data only has HTML tags, spaces and/or slashes, treat it as empty
            if (empty(trim(stripslashes(strip_tags($data))))) {
                echo "\"$fieldName\" is a required field.<br />\n"; //Tell user
                ++$errorCount;  // Keep count of the error
                $retVal = "";   // Set the data value this function will return
            }
            else { // If not empty, remove characters not allowed in email addresses
                $retVal = filter_var($data, FILTER_SANITIZE_EMAIL);
                // Is the user-supplied data in a valid email address format?
                if (!filter_var($retVal, FILTER_VALIDATE_EMAIL)) {
                    // If not, tell the user it's invalid
                    echo "\"$retVal\" is not a valid email address. <br />\n";
                    ++$errorCount;  // Keep count of the error
                }
            }
            return($retVal);  // Return the cleaned or empty data
        }

        /* This function takes one parameter for each form field (placeholder text) and
        displays the form with buttons to clear the form and to submit the form */
        function displayForm($Name, $Email, $Topic, $Message) {
            ?><h2 style='text-align:center'>Send Message</h2> <!-- Center form title -->
            <form name='message' action='' method='post'>
                <!--Laravel Herd requires a hidden token field in any POST form, to protect 
                against CSRF.-->
                <input type='hidden' name='_token' value='<?php echo csrf_token(); ?>'>
                <p>Your full name:
                    <input type='text' name='Name' value='<?php echo $Name; ?>' /></p>
                <p>Your email address:
                    <input type='text' name='Email' value='<?php echo $Email; ?>' /></p>
                <p>Topic of your message:
                    <input type='text' name='Topic' value='<?php echo $Topic; ?>' /></p>
                <p>Your message:<br />
                    <textarea name='Message'><?php echo $Message; ?></textarea></p>
                <!-- Button to clear form -->
                <p><input type='reset' value='Clear Form' />&nbsp; &nbsp;
                    <!-- Button to submit form -->
                    <input type='submit' name='Submit' value='Send Form' /></p>
            </form>
        <?php }

        // Initialize variables
        $ShowForm = TRUE;
        // Without this line here (global $errorCount), $errorCount inside and outside
        // the user-defined functions above are different variables when Laravel Herd
        // runs this code
        global $errorCount;
        $errorCount = 0;
        $Name = '';
        $Email = '';
        $Topic = '';
        $Message = '';

        /* If user submitted form, check each field for missing or invalid data
        using the custom functions defined above. Email address has its own validation
        function. If no errors are found, get ready to show the form. */
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $Name = validateInput($_POST['Name'],'Your full name');
            $Email = validateEmail($_POST['Email'],'Your email address');
            $Topic = validateInput($_POST['Topic'],'Topic of your message');
            $Message = validateInput($_POST['Message'],'Your message');
            // If user submitted form and an error was found, get ready to show form
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

        /* CODE COMMENT: If the form hasn't been displayed, or if the user submitted 
        unsatisfactory input, display the form. If the user submitted unsatisfactory 
        input, tell the user. Else (if the user submitted satisfactory input), prepare 
        to email the input, and tell the user whether the message is able to be sent. */
        if ($ShowForm == TRUE) {
            if ($errorCount > 0)
                echo "<p>Please re-enter the form information below.</p>\n";
            displayForm($Name, $Email, $Topic, $Message);
        } else {  // The user submitted satisfactory input. Prepare to email it.
            $SenderAddress = "$Name <$Email>";
            $Headers = "From: $SenderAddress\nCC: $SenderAddress\n"; // User gets a copy

            // Send the email
            $result = mail('recipient@example.com', $Topic, $Message, $Headers);

            if ($result)    // Show the user a status message
                echo '<p>Thank you, ' . $Name . '! We received your message about: "' . 
                    $Topic . '"<br>We\'ll get back to you at ' . $Email . ".</p>\n";
            else
                echo "<p>There was an error sending your message, " . $Name . ".</p>\n";

        }
    /* AFTER TEST: One thing I got wrong was that on the web page, the last button is
    labeled 'Send Form' instead of 'Submit', as specified in the code. Another thing is
    that when I submit data for all fields including a valid email address, it doesn't 
    send an email or show the status message to the user, because I don't have an email
    server on my computer. I knew that would happen from experience with our previous
    assignment, but when writing my predictions, I was thinking of what the code is
    supposed to do, not what is actually going to happen on this setup. And the error
    message when I submitted with an empty 'Topic' field said, "Topic of message", which
    was different from the field label shown, "Topic of your message"; so I fixed that
    mistake. As far as I can see, everything else worked as expected. */
    ?>
</body>
</html>