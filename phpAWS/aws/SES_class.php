<?php
require "../vendor/autoload.php";


use Aws\Ses\SesClient;
use Aws\Exception\AwsException;


class MySES
{
    private $access_key;
    private $secret_key;
    private $email_sender;
    private $region;
    private $SES;  // main object


    public function __construct($access_key, $secret_key, $region, $email_sender)
    {
        $this->access_key = $access_key;
        $this->secret_key = $secret_key;
        $this->email_sender = $email_sender;
        $this->region = $region;


        $this->SES = new SesClient(
            array(
                "credentials" => array(
                    "key" => $access_key,
                    "secret" => $secret_key,
                ),
                "version" => "latest",
                "region" => $region,
            )
        );
    }


    public function send_email($receiver)
    {
        $attechment_file_path = json_decode(file_get_contents("temp.json"), true)['file-path'];


        // __________________________________________ EMAIL SETUP _________________________________________ //
        $mail = new \PHPMailer\PHPMailer\PHPMailer;
        //Email values
        $mail->setFrom($this->email_sender, "Php Dev");
        $mail->addAddress($receiver);
        $mail->Subject = "Information message";

        $mail->Body = <<<EOS
            <h3>This email was sent using amazon web services.</h3>
            EOS;

        $mail->AltBody = <<<EOS
            The result file is atteched below.
            EOS;

        $mail->addAttachment($attechment_file_path, basename($attechment_file_path));
        $mail->preSend();
        $msg = $mail->getSentMIMEMessage();
        // __________________________________________ EMAIL SETUP _________________________________________ //

        
        try 
        {
            $result = $this->SES->sendRawEmail([
                "RawMessage" => [
                    "Data" => $msg
                ]
            ]);
            // var_dump($result);
        } 
        catch (AwsException $exc) 
        {
            echo("<h3>The email was not sent. Error message: </h3>".$exc->getAwsErrorMessage()."\n\n");
            return FALSE;
        }
        unlink("temp.json");
        unlink($attechment_file_path);
        return TRUE;
    }
};

?>
