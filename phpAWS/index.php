<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <title>Php dev</title>
  </head>
  <body>
    <main>
        <div class="container text-center p-4">
            <h1>PHP-AWS-SQS-S3-SES</h1>
            <br>
            <a class="btn btn-primary" href="aws/read.php">Read</a>
            <a class="btn btn-primary" href="aws/write.php">Write</a>
            <br>
            <br>
            <?php
              if(isset($_GET["what"]) && $_GET["what"] == "MailSuccess")
              {
                echo "<p>Mail was successfully sent.<p>";
              }
            ?>
        </div>
    </main>
  </body>
</html>