プロジェクト名：Investment Notification System（AWS自動投資通知システム） 

使用技術：
　Python 3.12, AWS Lambda, Amazon EventBridge, Amazon SNS, Amazon DynamoDB, 
　AWS Systems Manager Parameter Store, CloudFormation, S3, GitHub 

　開発環境：AWSクラウド環境, ローカル開発環境（Windows）, Git

概要： 
　AWSのサーバーレスアーキテクチャを活用し、毎月の投資リマインダーとポートフォリオ配分提案を
　自動でメール送信するシステムを構築。投資配分は設定ファイルで柔軟に変更可能で、通知履歴はDynamoDBに保存。
　CloudFormationによるインフラ自動構築と、EventBridgeによるスケジュール実行を実装。

担当工程： 要件定義／設計／AWSインフラ構築／Lambda実装／テスト／デプロイ／運用設計

主な機能：

📅 スケジュール通知：毎月1日に自動で投資リマインダー送信

📊 ポートフォリオ管理：銘柄別の投資比率設定（例：40%・35%・25%）

📧 メール通知：投資日・配分表・総額を整形して送信

📝 通知ログ管理：DynamoDBに全通知履歴を保存

☁️ サーバーレス構成：Lambda＋EventBridge＋SNSによる低運用負荷設計

工夫点・成果：

CloudFormationテンプレートでLambda・EventBridge・SNS・DynamoDBを自動構築

AWS Systems Manager Parameter Storeで機密情報・設定値を安全に管理

投資配分やスケジュールをコード変更なしで柔軟に更新可能

CloudWatchログによる稼働監視とトラブルシューティング手順を整備

将来機能（株価リアルタイム連携、Webダッシュボード）を見据えた拡張性設計

GitHub： https://github.com/Kentafrost/aws-web-dev/tree/main/mail-notification/python