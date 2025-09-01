プロジェクト名：Own Data Display – Enhanced PHP Media & File Management System 

期間：2025年X月〜2025年X月（個人開発／GitHub公開） 
使用技術：PHP 7.4+, Composer, AWS DynamoDB, HTML5, CSS3, JavaScript, Vue.js（UI一部）, 
JSON, Apache/Nginx, AWS CLI 開発環境：Windows, GitHub, AWS環境（DynamoDB, IAM）, 
ローカルストレージ

概要： 
　ユーザー認証、メディアブラウジング、ファイルストリーミング、データ管理を統合した
　PHPベースのWebアプリケーションを開発。
　AWS DynamoDBとローカルJSONによるハイブリッドデータストレージを実装し、
　動画・音声・画像の高度な再生機能やレスポンシブUIを提供。

担当工程： 要件定義／設計／フロントエンド実装／バックエンド実装／AWS連携／テスト／デプロイ

主な機能：

🔐 ユーザー認証：パスワードハッシュ化による安全なログイン／登録機能

📁 ファイルブラウザ：ローカルドライブ（D:\, G:\）の階層指定ブラウズ

🎬 メディアプレイヤー：動画・音声・画像のストリーミング再生（範囲リクエスト対応）

🖼 画像ビューア：ズーム／パン／ナビゲーション機能

🎵 音声同期再生：動画と別音声の同期再生

🔍 検索・フィルタ：リアルタイム検索とスマートフィルタ

📱 レスポンシブデザイン：モバイル対応UI

☁️ AWS連携：DynamoDBによるユーザーデータ管理（ローカルJSONフォールバック）

工夫点・成果：

AWS DynamoDBとローカルJSONの二重化でオフライン耐性を確保

動画＋音声の同期再生や自動リカバリ機能でユーザー体験を向上

Range Requestによる大容量ファイルの効率的ストリーミング

i18n対応を見据えたUI設計とキーボードショートカット実装

セキュリティ強化（ディレクトリトラバーサル防止、XSS対策、CORS設定）

GitHub： https://github.com/Kentafrost/aws-web-dev/tree/main/own-data-display