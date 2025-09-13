import boto3
import datetime
import os
import investment_shares as investment_shares


# 環境変数から取得（SNSトピックARNとDynamoDBテーブル名）
try:
    SNS_TOPIC_ARN = os.environ['SNS_TOPIC_ARN']
    DYNAMODB_TABLE = os.environ['DYNAMODB_TABLE']
    print(f"Environment variables loaded - SNS_TOPIC_ARN: {SNS_TOPIC_ARN}, DYNAMODB_TABLE: {DYNAMODB_TABLE}")
except KeyError as e:
    print(f"Missing environment variable: {e}")
    raise

def lambda_handler(event, context):
    try:
        print("Lambda function started")
        print(f"Event: {event}")
        
        # 日付を取得
        now = datetime.datetime.now()
        date_str = now.strftime("%Y-%m-%d")
        print(f"Processing investment notification for date: {date_str}")

        total_money = investment_shares.total_money()

        # company and money rate
        investment_target_dict = {}
        investment_target_dict[investment_shares.target_companies()[0][0]] = total_money * investment_shares.target_companies()[0][1] / 100
        investment_target_dict[investment_shares.target_companies()[1][0]] = total_money * investment_shares.target_companies()[1][1] / 100
        investment_target_dict[investment_shares.target_companies()[2][0]] = total_money * investment_shares.target_companies()[2][1] / 100

        print(f"Investment targets calculated: {investment_target_dict}")

        # HTML通知メッセージ
        html_message = f"""
        <html>
        <body>
            <h2>📈 {date_str} - 今月の投資をしましょう</h2>
            <table border="1" cellpadding="6" cellspacing="0" style="border-collapse: collapse; font-family: Arial; font-size: 14px;">
            <tr style="background-color: #f2f2f2;">
                <th>銘柄</th>
                <th>割合</th>
                <th>金額</th>
            </tr>
            <tr>
                <td>{investment_target_dict[investment_shares.target_companies()[0][0]]}</td>
                <td>{investment_shares.target_companies()[0][1]}%</td>
                <td>¥{investment_target_dict['INPEX']}</td>
            </tr>
            <tr>
                <td>{investment_target_dict[investment_shares.target_companies()[1][0]]}</td>
                <td>{investment_shares.target_companies()[1][1]}%</td>
                <td>¥{investment_target_dict['NTT']}</td>
            </tr>
            <tr>
                <td>{investment_target_dict[investment_shares.target_companies()[2][0]]}</td>
                <td>{investment_shares.target_companies()[2][1]}%</td>
                <td>¥{investment_target_dict['CocaCola']}</td>
            </tr>
            <tr style="font-weight: bold;">
                <td>合計</td>
                <td>100%</td>
                <td>¥{sum(investment_target_dict.values())}</td>
            </tr>
            </table>
            <p style="margin-top: 20px;">今月も語れる投資を。ポートフォリオを確認して、意味ある選択を。</p>
        </body>
        </html>
        """

        short_message = f"投資通知({date_str})& INPEX 50%(¥{investment_target_dict['INPEX']}) & NTT 30%(¥{investment_target_dict['NTT']}) & コカ・コーラBJ 20%(¥{investment_target_dict['CocaCola']}) & 合計 100%(¥{sum(investment_target_dict.values())})"

        # SNS通知
        try:
            print("Sending SNS notification...")
            sns = boto3.client('sns')
            response = sns.publish(
                TopicArn=SNS_TOPIC_ARN,
                Message=html_message,
                Subject="月次投資リマインダー"
            )
            print(f"SNS notification sent successfully: {response['MessageId']}")
        except Exception as e:
            print(f"Failed to send SNS notification: {str(e)}")
            raise

        # DynamoDB保存
        try:
            print("Saving to DynamoDB...")
            dynamodb = boto3.resource('dynamodb')
            table = dynamodb.Table(DYNAMODB_TABLE)
            table.put_item(
                Item={
                    'timestamp': date_str,
                    'message': short_message
                }
            )
            print("Data saved to DynamoDB successfully")
        except Exception as e:
            print(f"Failed to save to DynamoDB: {str(e)}")
            raise

        print("Lambda function completed successfully")
        return {
            'statusCode': 200,
            'body': f"通知送信＆記録完了: {date_str}"
        }
    
    except Exception as e:
        print(f"Lambda function failed with error: {str(e)}")
        print(f"Error type: {type(e).__name__}")
        import traceback
        print(f"Traceback: {traceback.format_exc()}")
        
        return {
            'statusCode': 500,
            'body': f"エラーが発生しました: {str(e)}"
        }