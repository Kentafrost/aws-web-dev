<?php
// Lambda function to handle incoming requests and store data in DynamoDB
require 'vendor/autoload.php';

use Aws\DynamoDb\DynamoDbClient;

function handler($event, $context) {
    $body = json_decode($event['body'], true);

    $name = $body['name'] ?? '';
    $email = $body['email'] ?? '';
    $phone = $body['phone'] ?? '';
    $question = $body['question'] ?? '';

    $dynamodb = new DynamoDbClient([
        'region' => 'ap-southeast-2',
        'version' => 'latest'
    ]);

    $ssm = new Aws\Ssm\SsmClient([
        'region' => 'ap-southeast-2',
        'version' => 'latest'
    ]);

    try {
        $result = $dynamodb->putItem([
            'TableName' => 'questionnaire-tbl',
            'Item' => [
                'name'  => ['S' => $name],
                'email' => ['S' => $email],
                'phone' => ['S' => $phone],
                'question' => ['S' => $question],
            ]
        ]);
    } catch (Exception $e) {
        return [
            'statusCode' => 500,
            'body' => json_encode(['message' => 'Failed to save data.', 'error' => $e->getMessage()])
        ];
    }

    $mailaddress = $ssm->getParameter('Gmail_addr');

    // send e-mail
    try {
        $to = $mailaddress['Parameter']['Value'];
        $subject = 'New Questionnaire Submission';
        $message = "Name: $name\nEmail: $email\nPhone: $phone\nQuestion: $question";
        $headers = 'From: ' . $mailaddress['Parameter']['Value'] . "\r\n" .
                'Reply-To: ' . $mailaddress['Parameter']['Value'] . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);
    } catch (Exception $e) {
        return [
            'statusCode' => 500,
            'body' => json_encode(['message' => 'Failed to send email.', 'error' => $e->getMessage()])
        ];
    }

    return [
        'statusCode' => 200,
        'body' => json_encode(['message' => 'Data saved!', 'result' => $result])
    ];
}