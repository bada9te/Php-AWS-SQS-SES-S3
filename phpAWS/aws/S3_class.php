<?php

require "../vendor/autoload.php";

// adding some Classes from Aws\S3
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;


class MyS3
{
    private $access_key;
    private $secret_key;
    private $bucket_name;
    private $region;
    private $S3;  // main object


    public function __construct($access_key, $secret_key, $bucket_name, $region) 
    {
        $this->access_key = $access_key;
        $this->secret_key = $secret_key;
        $this->bucket_name = $bucket_name;
        $this->region = $region;
        
        try 
        {
            $this->S3 = new S3Client(
                array (
                    "credentials" => array(
                        "key" => $this->access_key,
                        "secret" => $this->secret_key,
                    ),
                    "version" => "latest",
                    "region" => $this->region,
                )
            );
        } 
        catch(InvalidArgumentException $exc) 
        {
            echo "An error occurred while S3Client initializing: ".$exc->getMessage();
        }
    }


    // func to write sth into AWS s3 bucket
    public function write($local_file_path, $destination) 
    {
        try 
        {
            $this->S3->putObject(
                array(
                    "Bucket" => $this->bucket_name,
                    "Key" => $destination,
                    "SourceFile" => $local_file_path,
                    "ACL" => "public-read",
                )
            );
        } 
        catch (S3Exception $exc) 
        {
            echo "<h4>An error occured while file uploading: </h4>";
            echo $exc->getMessage();
            return FALSE;
        }
        return TRUE;
    }


    // func to read from AWS s3 bucket
    public function read($file)
    {
        $response = NULL;
        try
        {
            $response = $this->S3->getObject(
                array(
                    "Bucket" => $this->bucket_name,
                    'Key' => $file,
                    'SaveAs' => $file,
                )
            );
        }
        catch(S3Exception $exc)
        {
            echo "<h4>An error occurred while file reading: </h4>";
            echo $exc->getMessage();
            return NULL;
        }
        return $response;
    }
}

?>
