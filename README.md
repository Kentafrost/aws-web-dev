# AWS Web Automation Project

このプロジェクトは、AWS上で動作する自動化されたWeb構築システムです。S3を利用した静的ホスティング、
API Gatewayを介したLambda関数（Python/PHP）を組み合わせ、

1: ユーザー作成～ログイン～トップページ遷移までを自動処理
2: コマンドでDynamoDBにアップロードしたデータを使用して、自動でHTMLファイルを作成、および
　 それをS3にアップロードし、Top Pageに追加したURLを自動追加

<構成案>

1: HTML内に新規ユーザー作成ボタン作成
　⇒登録ボタン押下 ⇒ API GW ⇒ Lambda(PHP) ⇒ DynamoDBへのアップロード ⇒ 登録完了メッセージ表示

　ユーザー、PW入力し、ログインボタン押下 ⇒ API GW ⇒ Lambda(PHP) ⇒ DynamoDB内のデータで一致するものがあるか確認
　⇒ 一致するものがあった場合は、TOPページに移行　


2: 自PC ⇒ CSVのデータ ⇒ S3 Bucket PUT ⇒ Lambda ⇒ CSVデータを1行ずつDynamoDBにアップロード 
　　⇒ 自動的にHTML作成 & S3 アップロード ⇒ Top PageのURLを全HTMLファイルのリンクで更新しS3 bucketにアップロード

  　⇒ Top pageから各HTMLページにアクセスできるように。


## 🧩 構成図

  S3 --> APIGW
  APIGW --> Lambda
  Lambda --> SSM
  CFN --> S3
  CFN --> APIGW
  CFN --> Lambda
  CFN --> SSM
