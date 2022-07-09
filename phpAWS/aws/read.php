<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <title>Php dev read</title>
  </head>
  <body>
    <main>
        <div class="container text-center p-4">
            <h1>READ</h1>

            <?php
                /* ______________________________________________ SETUP ______________________________________________ */
                require("SQS_class.php");
                
                $temp_file = "temp.json";
                if(file_exists($temp_file))
                {
                    echo "<p>Sorry, this page is not available at the moment.</p>";
                    echo '<a class="btn btn-success" href="/">Back to main page</a>';
                    exit(0);
                }

                $config_file_location = "../configs/aws-config.json";
                
                // decoding json array
                $config = json_decode(file_get_contents($config_file_location), true);

                // objects from S3_class.php / SQS_class.php 
                $sqsOBJECT = new MySQS($config['access-key'], $config['secret-key'], $config["queue-url"], $config["sqs-region"]);
                /* ______________________________________________ SETUP ______________________________________________ */





                /* _______________________________________ READING SQS MESSAGE _______________________________________ */
                // read SQS (line 38 in SQS_class.php)
                $sqs_message = $sqsOBJECT->read();
                /* _______________________________________ READING SQS MESSAGE _______________________________________ */





                /* _______________________________________ WORKING WITH MESSAGE _______________________________________ */
                if($sqs_message != NULL)
                {
                  echo "<p>".$sqs_message."</p>";


                  // _______________________________  TAKING INFO ABOUT S3-BUCKET FROM LINK (MESSAGE)  
                  // extracting file path
                  $s3_pos = strpos($sqs_message, ".s3.");
                  $amazonaws_pos = strpos($sqs_message, ".amazonaws.com/");

                  //filename
                  $file_path = substr($sqs_message, $amazonaws_pos + strlen(".amazonaws.com/"));
                  echo "<p> File: ".$file_path."</p>";

                  //bucketname
                  $bucket_name = substr($sqs_message, 8, strlen($sqs_message) - strlen(substr($sqs_message, $s3_pos)) - 8);
                  echo "<p> Bucket: ".$bucket_name."</p>";

                  //regionname
                  $region_name = substr($sqs_message, $s3_pos + 4);
                  $region_name = substr($region_name, 0, strlen($region_name) - strlen($file_path) - strlen(".amazonaws.com/"));
                  echo "<p> Region: ".$region_name."</p>";  


                  // _______________________________  SAVING A FILE WITH INFO 
                  // saving an info taken from sqs link
                  $file_data = array();
                  $file_data['s3-object-link'] = $sqs_message;
                  $file_data['file-path'] = $file_path;
                  $file_data['bucket-name'] = $bucket_name;
                  $file_data['bucket-region'] = $region_name;
                  file_put_contents($temp_file, json_encode($file_data));


                  // _______________________________  SAVING A FILE WITH INFO FROM SQS 
                  // decoding and saving file via link from sqs queue
                  file_put_contents(basename($file_path), (file_get_contents($sqs_message)));

                }
                else
                {
                  "<h3> No messages in queue. <h3>";
                }
                /* _______________________________________ WORKING WITH MESSAGE _______________________________________ */

            ?>

            <a class="btn btn-warning" href="/">Back to main page</a>
            <a class="btn btn-primary" href="/aws/write.php">Write</a>
        </div>
    </main>
  </body>
</html>