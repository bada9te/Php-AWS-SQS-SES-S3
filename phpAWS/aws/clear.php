<?php
    if(isset($_GET["filename"]))
    {
        if(file_exists("temp.json"))
        {
            unlink("temp.json");
        }
        if(file_exists($_GET["filename"]))
        {
            unlink($_GET["filename"]);
        }
        header("Location: /index.php");
    }
    else
    {
        header("Location: /");
    }
      
?>