<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <title>Php dev write</title>
  </head>
  <body>
    <main>
        <div class="container w-50 text-center p-4">
            <h1>WRITE</h1>

            <?php

                /* ______________________________________________ SETUP ______________________________________________ */
                require("S3_class.php");
                require("SQS_class.php");
                
                $config_file_location = "../configs/aws-config.json";
                $temp_file = "temp.json";
                if(!file_exists($temp_file))
                {
                    echo "<p>Sorry, this page is not available at the moment.</p>";
                    echo '<a class="btn btn-success" href="/">Back to main page</a>';
                    exit(0);
                }


                // decoding json array
                $config = json_decode(file_get_contents($config_file_location), true);
                $s3bucket_config = json_decode(file_get_contents($temp_file), true);
                
                // using classes from S3_class.php / SQS_class.php 
                $s3OBJECT = new MyS3($config['access-key'], $config['secret-key'], $s3bucket_config["bucket-name"], $s3bucket_config["bucket-region"]);
                $sqsOBJECT = new MySQS($config['access-key'], $config['secret-key'], $config["queue-url"], $config["sqs-region"]);

                // file, readed from s3bucket
                $file_full_path = $s3bucket_config['file-path'];
                $file_name = basename($file_full_path);
                /* ______________________________________________ SETUP ______________________________________________ */




                /* ________________________________________ WORKING WITH FILE ________________________________________ */
                // decoding to array
                $json = json_decode(file_get_contents($file_name), true);
                
                // new array to store json data with tag[-1] == '1'
                $data = array();
                foreach($json["prices"] as $item => $val)
                {
                    // getting the last char
                    if(substr($val["tag"], -1) == '1')
                    {
                        // data[0->1->2->3->...] = val->(typeof array)
                        $data[] = $val;
                    }
                }
                $data = array("prices" => $data);
                // var_dump($data);

                // encoding to json 
                $json = json_encode($data);

                // filling in and saving the file
                file_put_contents($file_name, $json);
                /* ________________________________________ WORKING WITH FILE ________________________________________ */




                /* ___________________________________________ WRITING _______________________________________________ */
                // in S3_class.php (line 46) (write)
                echo $s3OBJECT->write($file_name, $file_full_path) ? "<h3>Success.</h3>" : "<h3>Error.</h3>";

                // in SQS_class (line 81) (write)
                $sqsOBJECT->write($file_name, $s3bucket_config['s3-object-link']);
                /* ___________________________________________ WRITING _______________________________________________ */
            ?>

            <div>
                <form action="email.php" method="POST">
                    <p>Send a result on email: (This email must be verified in AWS-SES as we are using sandbox)</p>
                    <input class="form-control w-100" type="email" name="mail-to" placeholder="Email" required>
                    <br>
                    <input class="btn btn-success" type="submit" name="sbmt-btn" value="Send email">
                </form>
            </div>
            <br>
            <a class="btn btn-warning" href="/aws/clear.php?filename=<?php echo $file_full_path ?>">Back to main page</a>
        </div>
    </main>
  </body>
</html>
