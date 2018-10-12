<?php

$name= $_POST['name'];
$email= $_POST['email'];
$mobile= $_POST['phone'];

    #### 2Factor API Setting
    $APIKey='###Enter API Key of your account###';
    $OTPMessage="<p>We have sent an OTP to $mobile,<br>Please enter the same below</p>";
    
    #### Custom Logic
    $otpValue=(( isset($_REQUEST['otp']) AND $_REQUEST['otp']<>'' ) ? $_REQUEST['otp'] : '' );
    
    if ( $otpValue =='' AND $mobile=="")
    {
        echo "<script type='text/javascript'> window.history.back(); </script>";
        die();
    }
    else
    if ( $mobile =='' AND $email=='' )
    {
        echo "<script type='text/javascript'> alert('Please provide either a mobile number or an email address to proceed');window.history.back(); </script>";
        die();
    }
    else if (  $mobile =='' AND $email <> '' )
    $forceSubmitWithEmail=1;

    if ( ( $mobile =='' OR strlen($mobile) <>10 OR substr($mobile,0,2) < 60) AND $email =='')
    {
        echo "<script type='text/javascript'> alert('Please enter valid mobile number');window.history.back(); </script>";
        die();
    }
     if ( $otpValue <> '') ### OTP value entered by user
    {
        ### Check if OTP is matching or not
        $VerificationSessionId=$_REQUEST['VerificationSessionId'];
        $API_Response_json=json_decode(file_get_contents("https://2factor.in/API/V1/$APIKey/SMS/VERIFY/$VerificationSessionId/$otpValue"),false);
        $VerificationStatus= $API_Response_json->Details;
            
            ### Check if OTP is matching
            if ( $VerificationStatus =='OTP Matched')
            {
                
            
            echo "Congratulations $mobile has been verified. Following are the details captured: <br>Name : $name <br> Email:  $email <br> Mobile : $mobile ";
                die();
                
            }
            else
            {
                echo "<script type='text/javascript'>alert('Sorry, OTP entered was incorrect. Please enter correct OTP');  window.history.back();  </script>";
                die();
            }
        
    }
    else
    {    
            ### Send OTP
            $API_Response_json=json_decode(file_get_contents("https://2factor.in/API/V1/$APIKey/SMS/$mobile/AUTOGEN"),false);
            $VerificationSessionId= $API_Response_json->Details;
            
    }

?>



<!--HTML Part-->


<html>
   <head>
      <meta charset="UTF-8">
   </head>
   <body>
      <form action="action_otp.php" method="post">
         Password:<br>
         <input type="text" id='otp' name="otp" maxlength="6" placeholder="XXXXXX"  required="required">	
         <input type="hidden"  name="VerificationSessionId" value="<?php echo $VerificationSessionId; ?>" >
         <input type="hidden" name="name" value="<?php echo $name; ?>"  >	
         <input type="hidden"  name="email" value="<?php echo $email; ?>" >	
         <input type="hidden"  name="phone" value="<?php echo $mobile; ?>" >
         <input type="submit" value="Submit">
      </form>
   </body>
</html>

