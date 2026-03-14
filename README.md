# DutyShuffle - 掃除当番管理アプリ

家族やチームの掃除当番を効率的に管理・割り当てするWebアプリケーションです。複雑なルールに対応した自動シャッフル機能と、柔軟な当番設定が特徴です。

## 📋 目次

- [機能](#機能)
- [技術スタック](#技術スタック)
- [セットアップ](#セットアップ)
- [使い方](#使い方)
- [ファイル構成](#ファイル構成)
- [データベーススキーマ](#データベーススキーマ)
- [API仕様](#api仕様)

---

## 🎯 機能

### 📊 メンバー・作業管理
- **メンバー管理**: メンバー情報の登録・編集・削除（アーカイブ対応）
- **作業管理**: 掃除作業の登録・編集・削除、参加人数設定

### 🎲 自動シャッフル機能
複数の割り当てモードで柔軟に対応：

#### 1. **週別モード** (`week_use = true`)
- 指定した曜日を基準に、過去7日以内のレコードを参照
- 同じ作業の重複割り当てを防止

#### 2. **リセット日ベースモード** (`week_use = false`)
- 最も新しいリセット日付以降のレコードを参照
- 一定期間ごとにリセットする運用に対応

### 👥 メンバーオプション
- **固定割り当て** (status=0)
  - 指定したメンバーを特定の作業に常に割り当て
  - 除外ルールや過去割り当ての重複防止に対応

- **除外設定** (status=1)
  - 特定のメンバーを特定の作業から除外
  - 割り当て時に自動的にスキップ

### ⚙️ 作業割り当てルール
- **以上モード** (`is_above = true`)
  - 最低でも指定人数を割り当て
  - 必要に応じて追加割り当て可能（無制限）

- **以下モード** (`is_above = false`)
  - 指定人数以下の割り当てを厳密に制限
  - キャパシティ管理が重要な場合に使用

### 📅 区間設定
- **割り当て間隔**: 過去n日間のレコードを参照
- 同じメンバーの短期間での重複割り当てを防止

### 🗓️ リセット日付管理
- 複数のリセット日付を登録可能
- 各リセット日以降のデータを独立して管理

---

## 💻 技術スタック

| 层 | 技術 |
|-----|------|
| **フロントエンド** | HTML5, CSS3, jQuery 3.6.1 |
| **バックエンド** | PHP 7.4+ |
| **データベース** | PostgreSQL 12+ / MySQL 5.7+ |
| **ORM** | Prisma |
| **コンテナ化** | Docker, Docker Compose |
| **ビルドツール** | Node.js (Prisma, npm) |

---

## 🚀 セットアップ

### 必要環境
- Docker & Docker Compose
- Node.js 14.0+
- Gitクライアント

### インストール手順

#### 1. リポジトリのクローン
```bash
git clone <repository-url>
cd DutyShuffle
```

#### 2. 環境ファイルの設定
```bash
cp env.php.example env.php
# env.phpをデータベース接続情報など適切に編集
```

#### 3. Dockerコンテナの起動
```bash
docker-compose up -d --build
```

#### 4. マイグレーション実行（初回用）
```bash
docker-compose exec -T web npx prisma migrate dev
# または既存のマイグレーションをリセット（開発環境のみ）
docker-compose exec -T web npx prisma migrate reset
```

#### 5. シードデータの投入（開発環境）
```bash
docker-compose exec -T web npx prisma db seed
```

#### 6. アプリケーションにアクセス
```
http://localhost/index.php
```

---

## 📖 使い方

### 1. メンバー登録
1. サイドバーから「メンバー」を選択
2. 「メンバー追加」ボタンをクリック
3. 姓・名・ふりがなを入力して確定

### 2. 作業登録
1. サイドバーから「作業」を選択
2. 「作業追加」ボタンをクリック
3. 作業名・参加人数・割り当てルール（以上/以下）を設定

### 3. 当番者の選択
1. 「割り当て」ページを開く
2. 希望する日付を選択
3. 参加メンバーを選択してから「シャッフル」を実行

### 4. 自動シャッフル実行
- **シャッフル前準備**:
  - オプション設定で、割り当て間隔とモード（週別/リセット日ベース）を設定
  - 固定割り当てルールを設定（必要な場合）
  - 除外ルールを設定（必要な場合）

- **シャッフル実行時の処理**:
  1. 固定割り当てルールを優先適用
  2. 除外メンバーと過去割り当ての重複をスキップ
  3. 残りのメンバーを均等に割り当て
  4. 割り当て不可のメンバーは「サポート」(null)扱い

### 5. メンバーの移動
1. 割り当て画面で、移動させたいメンバーをクリック
2. 「移動先を選んでください」から作業を選択
3. 「複製して追加」チェックで、別の作業に追加割り当て可能

---

## 📁 ファイル構成

```
DutyShuffle/
├── public/               # 公開ファイル（HTMLページ）
│   ├── top.php          # トップページ
│   ├── allocation.php   # 割り当てページ
│   ├── option.php       # オプション設定ページ
│   ├── create-edit.php  # メンバー・作業管理ページ
│   ├── modal/           # モーダルテンプレート
│   └── cording_data/    # 設計時のHTMLコーディング
│
├── classes/
│   ├── ajax.php         # AJAX処理（重要）
│   ├── functions.php    # ユーティリティ関数
│   └── dummy/           # テスト用ダミーファイル
│
├── src/
│   ├── css/             # スタイルシート
│   ├── js/              # JavaScriptファイル
│   │   ├── ajax.js      # AJAX及びDOM操作
│   │   ├── modal.js     # モーダル制御
│   │   └── date.js      # 日付操作
│   └── image/           # 画像ファイル
│
├── component/           # 共有コンポーネント
│   ├── head_component.php      # ヘッダー
│   └── sidebar_component.php   # サイドバー
│
├── prisma/
│   ├── schema.prisma   # Prismaスキーマ定義
│   ├── seed.js         # シードデータスクリプト
│   └── migrations/     # マイグレーションファイル
│
├── dbc/
│   └── dbc.php         # データベース接続
│
├── docker/             # Docker設定
│   ├── mysql/
│   └── php/
│
└── docker-compose.yml  # Docker Compose設定
```

---

## 🗄️ データベーススキーマ

### 主要テーブル

#### `member` - メンバーテーブル
```sql
id              SERIAL PRIMARY KEY
family_name     VARCHAR(50)      -- 姓
given_name      VARCHAR(50)      -- 名
kana_name       VARCHAR(50)      -- ふりがな
archive         BOOLEAN          -- アーカイブ状態
```

#### `work` - 作業テーブル
```sql
id              SERIAL PRIMARY KEY
name            VARCHAR(100)     -- 作業名
multiple        INTEGER          -- 参加人数
is_above        BOOLEAN          -- 割り当てルール（true=以上, false=以下）
archive         BOOLEAN          -- アーカイブ状態
```

#### `history` - 割り当て履歴テーブル
```sql
id              SERIAL PRIMARY KEY
date            DATE             -- 割り当て日
member_id       INTEGER FK       -- メンバーID
work_id         INTEGER FK       -- 作業ID (NULL=未割り当て)
```

#### `member_option` - メンバーオプションテーブル
```sql
id              SERIAL PRIMARY KEY
member_id       INTEGER FK       -- メンバーID
work_id         INTEGER FK       -- 作業ID
status          INTEGER          -- 0=固定割り当て, 1=除外
```

#### `worksheet` - 割り当て設定テーブル
```sql
id              SERIAL PRIMARY KEY
interval        INTEGER          -- 割り当て間隔（日数）
week_use        BOOLEAN          -- true=週別モード, false=リセット日ベース
week            INTEGER          -- 曜日 (0=日, 1=月, ..., 6=土)
```

#### `shuffle_option` - リセット日付テーブル
```sql
id              SERIAL PRIMARY KEY
reset_date      DATE             -- リセット日付
```

#### `off_work` - 休業作業テーブル
```sql
id              SERIAL PRIMARY KEY
date            DATE             -- 対象日
work_id         INTEGER FK       -- 作業ID
```

---

## 🔌 API仕様

### AJAX エンドポイント（`classes/ajax.php`）

#### メンバー関連
| type | メソッド | 説明 |
|------|---------|------|
| `member_list` | POST | メンバー一覧取得 |
| `member_add` | POST | メンバー追加 |
| `member_edit` | POST | メンバー編集情報取得 |
| `member_update` | POST | メンバー更新 |

#### 作業関連
| type | メソッド | 説明 |
|------|---------|------|
| `work_list` | POST | 作業一覧取得 |
| `work_add` | POST | 作業追加 |
| `work_edit` | POST | 作業編集情報取得 |
| `work_update` | POST | 作業更新 |

#### 割り当て関連
| type | メソッド | 説明 |
|------|---------|------|
| `allocation_list` | POST | 指定日の割り当て状況取得 |
| `member_select_work` | POST | 作業別メンバー選択リスト |
| `work_select_definition` | POST | メンバーを作業に割り当て |
| `shuffle` | POST | **自動シャッフル実行** |

#### オプション関連
| type | メソッド | 説明 |
|------|---------|------|
| `get_interval` / `save_interval` | POST | 割り当て間隔設定 |
| `get_week_use` / `save_week_use` | POST | モード設定 |
| `get_week` / `save_week` | POST | 曜日設定 |
| `get_reset_dates` / `save_reset_dates` | POST | リセット日付管理 |
| `option_list` | POST | オプション一覧取得 |
| `add-member_option` / `update-member_option` | POST | メンバーオプション管理 |

---

## 🔑 核となるシャッフルアルゴリズム

`classes/ajax.php` の `case 'shuffle'` で実装

### 処理フロー

```
1. 過去N日間のメンバー作業履歴を取得
   ├─ week_use=true → 指定曜日から遡った7日以内
   └─ week_use=false → 最新リセット日付以降

2. 作業リストを準備
   ├─ is_above=true → unlimited capacity (-1)
   └─ is_above=false → exact limit

3. メンバー数 > 作業スロット数 の場合
   └─ is_above=true の作業に追加割り当て枠を拡張

4. 固定割り当てメンバーを優先指定
   ├─ 除外ルールチェック
   ├─ 過去割り当ての重複チェック
   └─ 条件OK → 割り当て / 条件NG → null 出力

5. 一般メンバーを均等割り当て
   ├─ 各メンバーで異なる試行開始位置（均等分散）
   ├─ 除外・重複チェック実施
   └─ スロット到達まで割り当て

6. 割り当て不可メンバーは null で処理確定
```

### 重要な特徴

- ✅ **無限ループ防止**: 固定回ループでタイムアウト回避
- ✅ **均等分散**: メンバーごとに試行開始位置をずらして公平性確保
- ✅ **柔軟なキャパシティ管理**: is_above フラグで以上/以下を切り替え
- ✅ **全メンバーがWHEN句対象**: nullも含め必ずデータベース更新対象

---

## 📝 ライセンス

このプロジェクトはMITライセンスの下で公開されています。

---

## 👨‍💻 開発者向け

### 開発環境での実行
```bash
# コンテナ内PHPでの実行
docker-compose exec web php -S localhost:8000

# Prismaでのデータベース操作
docker-compose exec web npx prisma studio  # GUI操作
docker-compose exec web npx prisma reset   # リセット（開発環境のみ）
```

---

**最終更新**: 2026年3月14日
