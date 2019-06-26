<?php

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        # FIX: Replace this email with recipient email
        $mail_to = "abc@gmail.com";
        
        # Sender Data
        $subject = trim($_POST["subject"]);
        $name = str_replace(array("\r","\n"),array(" "," ") , strip_tags(trim($_POST["fname"])));
        $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
        $message = trim($_POST["message"]);
		
		
        
    if ( empty($name) OR !filter_var($email, FILTER_VALIDATE_EMAIL) OR empty($subject) OR empty($message)) {
            # Set a 400 (bad request) response code and exit.
            http_response_code(400);
            echo "Please complete the form and try again.";
            exit;
        }
        
        # Mail Content
        $content = "Name: $name\n";
        $content .= "Email: $email\n\n";
        $content .= "Message:\n$message\n";

        # email headers.
        $headers = "From: $name <$email>";

        # Send the email.
        $success = mail($mail_to, $subject, $content, $headers);
        if ($success) {
            # Set a 200 (okay) response code.
            http_response_code(200);
            echo "Thank You! Your message has been sent.";
        } else {
            # Set a 500 (internal server error) response code.
            http_response_code(500);
            echo "Oops! Something went wrong, we couldn't send your message.";
        }
		
		////////////////// insert to database ////////////////////////
	
		$con = mysqli_connect("127.0.0.1","root","","custom_house");
		if (!$con)
		  {
		  die('Could not connect: ' . mysqli_error());
		  }
		$result = mysqli_query($con, "SELECT * FROM contact WHERE Email ='$email' ");

		if( mysqli_num_rows($result) > 0) {
			mysqli_query($con, "UPDATE contact SET Name = '$name', Email = '$email', Subject = '$subject', Message = '$message' WHERE Email ='$email' ");
		} else {
			mysqli_query($con, "INSERT INTO contact (Name, Email, Subject, Message) VALUES ('$name', '$email', '$subject', '$message') ");
		}
		mysqli_close($con);
		
		//////////////////////////////////////////////////////////////////

    } else {
        # Not a POST request, set a 403 (forbidden) response code.
        http_response_code(403);
        echo "There was a problem with your submission, please try again.";
    }
	
	

?>
