![This is an image](https://myoctocat.com/assets/images/base-octocat.svg)

## In order to use AWS, the configuration file (phpAWS/configs/aws-config.json) must contain:

- access-key (aws user special key)
- secret-key (aws user special key)
- queue-url (link to the queue)
- sqs-region (for queue)
- email-sender (verified aws-ses email)
- ses-region (for email sending)

## Features

- Reading message from SQS queue with a link to an object in S3 bucket
- Getting data from file (as a JSON), parse it, and get tag(s) ending with 1
- Writing the result into S3 bucket
- Sending a message about the result into SQS queue.
- Sending an email to a given (verified) address with the result as an attachment

## Moreover
After the configuration setup you can build the docker container and run the program
easily:
```sh
 docker build -t [your_image_name] .
```
And run it:
```sh
 docker run -p [port]:80 -d [your_image_name]
```
Finally, type in your browser:
```sh
 localhost:[port]
```

## Do not forget to configure AWS policies as well:
## S3 Bucket Policy:
```sh
 {
    "Version": "2012-10-17",
    "Id": "Policy1488494182833",
    "Statement": [
        {
            "Sid": "Stmt1488493308547",
            "Effect": "Allow",
            "Principal": {
                "AWS": "__________YOUR_USER_ARN__________"
            },
            "Action": [
                "s3:ListBucket",
                "s3:ListBucketVersions",
                "s3:GetBucketLocation",
                "s3:Get*",
                "s3:Put*"
            ],
            "Resource": "__________YOUR_BUCKET_ARN__________"
        }
    ]
}
```

## S3 Bucket Cross-origin resource sharing (CORS)
```sh
 [
    {
        "AllowedHeaders": [
            "*"
        ],
        "AllowedMethods": [
            "GET",
            "HEAD",
            "PUT",
            "POST"
        ],
        "AllowedOrigins": [
            "*"
        ],
        "ExposeHeaders": [],
        "MaxAgeSeconds": 3000
    }
]
```

## Amazon SES
```sh
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Sid": "stmt1657317778552",
      "Effect": "Allow",
      "Principal": {
        "AWS": "__________YOUR_USER_ARN__________"
      },
      "Action": [
        "ses:SendEmail",
        "ses:SendRawEmail",
        "ses:SendTemplatedEmail",
        "ses:SendBulkTemplatedEmail"
      ],
      "Resource": "__________YOUR_SES_IDENTITY_ARN__________",
      "Condition": {}
    }
  ]
}
```

## SQS Policy 
```sh
{
  "Version": "2008-10-17",
  "Id": "__default_policy_ID",
  "Statement": [
    {
      "Sid": "__sender_statement",
      "Effect": "Allow",
      "Principal": {
        "AWS": "__________YOUR_USER_ARN__________"
      },
      "Action": [
        "SQS:SendMessage",
        "SQS:ChangeMessageVisibility",
        "SQS:DeleteMessage",
        "SQS:ReceiveMessage"
       ],
      "Resource": "__________YOUR_QUEUE_ARN__________"
    }
  ]
}
```

## User Inline Policy
```sh
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": [
                "s3:ListAllMyBuckets",
                "s3:PutObject",
                "s3:GetObject"
            ],
            "Resource": [
                "arn:aws:s3:::*"
            ]
        }
    ]
}
```
