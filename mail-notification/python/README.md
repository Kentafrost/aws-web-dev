# Investment Notification System

An automated AWS-based system that sends monthly investment reminders with portfolio allocation recommendations via email notifications.

## 🚀 Features

- **📅 Scheduled Notifications**: Monthly automated investment reminders
- **📊 Portfolio Management**: Configurable investment targets with percentage allocations
- **📧 Email Notifications**: Clean email formatting with investment tables
- **📝 Activity Logging**: All notifications logged for tracking
- **☁️ Serverless Architecture**: Built on AWS Lambda, EventBridge, and SNS

## 📂 Project Structure

```
mail-notification/
├── README.md
├── python/
│   └── lambda/
│       ├── investment-notification.py    # Main Lambda function
│       ├── investment_shares.py         # Portfolio configuration
│       └── shares_transition_notification.py  # API integration
│
└── aws/
    ├── lambda/
    │   └── investment-lambda.yaml       # Lambda template
    └── eventbridge/
        └── notification-log-evb.yaml    # EventBridge scheduler
```

## 🎯 Portfolio Configuration

Customize your investment targets in `investment_shares.py`:

```python
def target_companies():
    return [
        ('Company A', 40),  # 40% allocation
        ('Company B', 35),  # 35% allocation
        ('Company C', 25)   # 25% allocation
    ]

def total_money():
    return 50000  # Your monthly investment budget
```

## 🛠️ Prerequisites

### AWS Services Required
- AWS Lambda (Python 3.12)
- Amazon EventBridge (scheduling)
- Amazon SNS (email notifications)
- Amazon DynamoDB (logging)
- AWS Systems Manager Parameter Store (configuration)

## 📋 Quick Setup

### 1. Configure AWS Parameters

```bash
# Set up required SSM parameters
aws ssm put-parameter --name "/CodeS3BucketName" --value "your-bucket" --type "String"
aws ssm put-parameter --name "/basicSNSTopic" --value "your-sns-topic-arn" --type "String"
aws ssm put-parameter --name "/NotificationLogTable" --value "notification-logs" --type "String"
```

### 2. Deploy Resources

All AWS resources are created using CloudFormation templates provided in the `aws/` directory.

### 3. Package Lambda Code

```bash
cd python/lambda/
zip -r investment-notification.zip *.py
aws s3 cp investment-notification.zip s3://your-bucket/lambda/zip/
```

### 4. Deploy Stacks

Use the provided CloudFormation templates to deploy the Lambda function and EventBridge scheduler.

## 📧 Email Format

The system sends formatted emails with:
- Investment date
- Portfolio allocation table
- Total investment amount
- Investment reminder message

## ⏰ Schedule

Runs monthly on the 1st at midnight UTC:
```yaml
ScheduleExpression: "cron(0 0 1 * ? *)"
```

**Schedule Examples:**
- `cron(0 0 1 * ? *)` - 1st of every month at midnight
- `cron(0 9 1 * ? *)` - 1st of every month at 9:00 AM
- `cron(0 0 15 * ? *)` - 15th of every month at midnight

## 🔧 Configuration

### Environment Variables
| Variable | Description |
|----------|-------------|
| `SNS_TOPIC_ARN` | SNS topic for notifications |
| `DYNAMODB_TABLE` | DynamoDB table name |
| `ACCOUNT_ID` | AWS Account ID |

### Customization
1. **Change Schedule**: Modify `ScheduleExpression` in EventBridge template
2. **Update Portfolio**: Edit `investment_shares.py`
3. **Modify Email**: Update template in `investment-notification.py`

## 🧪 Testing

### Test Lambda Function
```bash
aws lambda invoke \
    --function-name investment-notification-function \
    --payload '{}' \
    response.json
```

### Monitor Logs
```bash
aws logs describe-log-groups --log-group-name-prefix "/aws/lambda/investment-notification"
```

## 🚨 Troubleshooting

### Common Issues
- **Permission errors**: Check IAM role policies
- **Emails not received**: Verify SNS subscription
- **DynamoDB failures**: Ensure table exists with correct permissions

### Debug Steps
1. Check CloudWatch logs for errors
2. Verify SSM parameters are set
3. Test Lambda function manually
4. Confirm all resources exist in correct region

## 🔮 Future Features
- Real-time stock data integration
- Performance tracking such as using CloudWatch metric
- Market alerts
- Monitor shares growth every week
- Web dashboard with PHP, Javascript(vue.js) in local environment

## 🤝 Contributing

1. Fork the repository
2. Create feature branch
3. Make changes with proper testing
4. Submit pull request

---

**Built for automated investment discipline**