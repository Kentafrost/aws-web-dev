// AWS Lambda function to handle incoming requests and store data in DynamoDB
const { DynamoDBClient, PutItemCommand } = require('@aws-sdk/client-dynamodb');
const { SSMClient, GetParameterCommand } = require('@aws-sdk/client-ssm');
const { SESClient, SendEmailCommand } = require('@aws-sdk/client-ses');

exports.handler = async (event, context) => {
    try {
        const body = JSON.parse(event.body || '{}');

        const name = body.name || '';
        const email = body.email || '';
        const phone = body.phone || '';
        const question = body.question || '';

        // Initialize AWS clients
        const dynamodb = new DynamoDBClient({
            region: 'ap-southeast-2'
        });

        const ssm = new SSMClient({
            region: 'ap-southeast-2'
        });

        const ses = new SESClient({
            region: 'ap-southeast-2'
        });

        // Store data in DynamoDB
        try {
            const putCommand = new PutItemCommand({
                TableName: 'questionnaire-tbl',
                Item: {
                    name: { S: name },
                    email: { S: email },
                    phone: { S: phone },
                    question: { S: question },
                    timestamp: { S: new Date().toISOString() }
                }
            });

            const result = await dynamodb.send(putCommand);
            console.log('Data saved to DynamoDB:', result);

        } catch (error) {
            console.error('DynamoDB error:', error);
            return {
                statusCode: 500,
                headers: {
                    'Content-Type': 'application/json',
                    'Access-Control-Allow-Origin': '*'
                },
                body: JSON.stringify({
                    message: 'Failed to save data.',
                    error: error.message
                })
            };
        }

        // Get email address from SSM Parameter Store
        try {
            const getParameterCommand = new GetParameterCommand({
                Name: 'Gmail_addr'
            });
            
            const mailaddressResult = await ssm.send(getParameterCommand);
            const toEmail = mailaddressResult.Parameter.Value;

            // Send email using SES
            const emailParams = {
                Source: toEmail,
                Destination: {
                    ToAddresses: [toEmail]
                },
                Message: {
                    Subject: {
                        Data: 'New Questionnaire Submission',
                        Charset: 'UTF-8'
                    },
                    Body: {
                        Text: {
                            Data: `Name: ${name}\nEmail: ${email}\nPhone: ${phone}\nQuestion: ${question}`,
                            Charset: 'UTF-8'
                        },
                        Html: {
                            Data: `
                                <h3>New Questionnaire Submission</h3>
                                <p><strong>Name:</strong> ${name}</p>
                                <p><strong>Email:</strong> ${email}</p>
                                <p><strong>Phone:</strong> ${phone}</p>
                                <p><strong>Question:</strong></p>
                                <p>${question.replace(/\n/g, '<br>')}</p>
                            `,
                            Charset: 'UTF-8'
                        }
                    }
                }
            };

            const sendEmailCommand = new SendEmailCommand(emailParams);
            await ses.send(sendEmailCommand);
            console.log('Email sent successfully');

        } catch (error) {
            console.error('Email sending error:', error);
            // Don't fail the entire request if email fails
            console.log('Continuing despite email failure...');
        }

        return {
            statusCode: 200,
            headers: {
                'Content-Type': 'application/json',
                'Access-Control-Allow-Origin': '*'
            },
            body: JSON.stringify({
                message: 'Questionnaire submitted successfully!',
                timestamp: new Date().toISOString()
            })
        };

    } catch (error) {
        console.error('Handler error:', error);
        return {
            statusCode: 500,
            headers: {
                'Content-Type': 'application/json',
                'Access-Control-Allow-Origin': '*'
            },
            body: JSON.stringify({
                message: 'Internal server error',
                error: error.message
            })
        };
    }
};