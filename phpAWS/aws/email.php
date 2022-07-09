<?php
if(isset($_POST["sbmt-btn"]) && isset($_POST["mail-to"]))
{
    require "SES_class.php";

    $config_path = "../configs/aws-config.json";
    $config = json_decode(file_get_contents($config_path), true);
    $sesOBJECT = new MySES($config["access-key"], $config["secret-key"], $config["ses-region"], $config["email-sender"]);

    if($sesOBJECT->send_email($_POST["mail-to"]))
    {
        header("Location: /?what=MailSuccess");
    }
    else
    {
        header("Location: /");
    }

}
else
{
    header("Location: /");
}

