下記、WEBページの構成案をまとめてます。

### ログイン処理：

- ユーザー名 / パスワード入力 → 認証リクエスト送信
- DynamoDB から照合 → 一致すれば Top Page に遷移

---

### CSVベースのHTMLコンテンツ自動生成

- ローカルPCから CSVファイルを S3 にアップロード（PUT）
- S3PUT Eventにより、Lambdaがトリガーされ、1行ずつ DynamoDB に登録 ※PHP, Python
- 登録済みデータを元に HTML ファイルを自動生成
- 生成された HTML を S3 にアップロード（静的サイトとして公開）
- Top Page の HTML を更新し、新規ページへのリンクを自動追加

---

## 🔄 システム構成フロー

[自PC] → [CSV Upload → S3] → [Lambda Trigger] → [CSV → DynamoDB] → [Lambda → HTML生成 → S3] → [Top Page更新 → S3反映]

- Top Page から各HTMLページへのアクセスが可能になります。

---

## 📝 補足

- 本構成は静的ファイルベースのホスティングに最適化されており、動的サーバー不要。
- IAMロールやAPI Gatewayの設定は別途記載予定。

---

## 🚀 将来拡張のアイデア

- 多言語対応（HTMLテンプレートの切り替え）
- HTMLテンプレートのレイアウト調整（CSS/JS）
- ユーザー別ページのアクセス制限（S3の署名付きURLなど）

---
