<?php

require "../vendor/autoload.php";

// using the SQS classes from AWS
use Aws\Sqs\SqsClient;
use Aws\Exception\AwsException;


class MySQS
{
    private $access_key;
    private $secret_key;
    private $msg_object_working_on;  // to save temp queue object
    private $msg_body; 
    private $queue_url;
    private $region;
    private $SQS;  // main object

    public function __construct($access_key, $secret_key, $queue_url, $region)
    {
        $this->access_key = $access_key;
        $this->secret_key = $secret_key;
        $this->queue_url = $queue_url;
        $this->region = $region;
        $this->SQS = new SqsClient(
            array(
                "credentials" => array(
                    "key" => $this->access_key,
                    "secret" => $this->secret_key,
                ),
                "region" => $this->region,
                "version" => "latest",
            )
        );
    }


    // read from SQS 
    public function read()
    {
        try 
        {
            $result = $this->SQS->receiveMessage(
                array(
                    'AttributeNames' => ['SentTimestamp'],
                    'MaxNumberOfMessages' => 1,
                    'MessageAttributeNames' => ['Body'],
                    'QueueUrl' => $this->queue_url,
                    'WaitTimeSeconds' => 0,
                )
            );
            
            $this->msg_object_working_on = $result;
            $this->msg_body = $result->get('Messages')[0]['Body'];
            

            // delete msg from SQS if got one
            if (!empty($result->get('Messages'))) 
            {
                // print_r($result->get('Messages')[0]);
                
                $result = $this->SQS->deleteMessage([
                    'QueueUrl' => $this->queue_url, 
                    'ReceiptHandle' => $result->get('Messages')[0]['ReceiptHandle'] 
                ]);
                
                return $this->msg_object_working_on->get('Messages')[0]['Body'];
            } 
            else 
            {
                return NULL;
            }
        } 
        catch (AwsException $e) 
        {
            // error_log($e->getMessage());
            return NULL;
        }
    }


    // write to SQS 
    public function write($description, $body)
    {
        $arguments = [
            'DelaySeconds' => 10,
            'MessageAttributes' => [
                "Title" => [
                    'DataType' => "String",
                    'StringValue' => "RESULT: [OK] ",  // .$this->msg_object_working_on->get('Messages')[0]['MessageId']
                ],
                "Description" => [
                    'DataType' => "String",
                    'StringValue' => $description,
                ]
            ],
            'MessageBody' => $body, 
            'QueueUrl' => $this->queue_url,
        ];

        try
        {
            $this->SQS->sendMessage($arguments);
        }
        catch(AwsException $exc)
        {
            error_log($exc->getMessage());
            return FALSE;
        }
        return TRUE;
    }
}


?>