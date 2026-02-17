-- PostgreSQL用サンプルデータ

-- テーブル作成
CREATE TABLE "member" (
  "id" SERIAL PRIMARY KEY,
  "family_name" VARCHAR(255) NOT NULL,
  "given_name" VARCHAR(255) NOT NULL,
  "kana_name" VARCHAR(255) NOT NULL,
  "archive" BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE TABLE "work" (
  "id" SERIAL PRIMARY KEY,
  "name" VARCHAR(255) NOT NULL,
  "multiple" INTEGER,
  "archive" BOOLEAN NOT NULL DEFAULT FALSE
);

-- 必要に応じて他のテーブルも追加してください

-- データ挿入
INSERT INTO "member" ("id", "family_name", "given_name", "kana_name", "archive") VALUES
(1, 'テスト', '太郎', 'てすとたろう', TRUE),
(2, 'テスト', '花子', 'てすとはなこ', TRUE),
(3, '釘子', '津佳冴', 'くぎこつかさ', FALSE),
(4, '長谷川', '異風', 'はせがわいふう', FALSE),
(5, '樋口', '伊吹', 'ひぐちいぶき', FALSE),
(6, '小泉', '俊一', 'こいずみしゅんいち', FALSE),
(7, '北川', '優斗', 'きたがわゆうと', FALSE),
(8, '小股', '晄', 'おまたあき', FALSE),
(9, '田鹿', '蒼史', 'たじかそうじ', FALSE),
(10, '芦生', '浩明', 'あしおひろあき', FALSE),
(11, '高宮城', '誉有治', 'たかみやぎようき', FALSE),
(12, '朴', '清', 'ぼくきよし', FALSE),
(13, '西加治工', '祐樹', 'にしかじくゆうき', FALSE),
(14, '雉子谷', '茂夫', 'きじだにしげお', FALSE),
(15, '渡谷', '身志', 'わたりやみり', FALSE),
(16, '室石', '遙摯', 'むろいしはると', FALSE),
(17, '塩足', '壱', 'しおたりいち', FALSE),
(18, '筒屋', '厳春', 'つつやみねはる', FALSE),
(19, '日陰茂井', '昊', 'ひかげもいそら', FALSE),
(20, '精廬', '里備', 'とぐろさとはる', FALSE),
(21, '喜美候部', '智絃', 'きみこうべちづる', FALSE),
(22, '竹乘', '成也', 'たけのりなりや', FALSE),
(23, '安達', '城灯', 'あだちきと', FALSE),
(24, '森田', '悠翔', 'もりたはると', FALSE),
(25, '矢野', '英一', 'やのえいいち', FALSE),
(26, '誉田', '和樹', 'ほまれだかずき', FALSE),
(27, '数', '瑛斗', 'かずえいと', FALSE);

INSERT INTO "work" ("id", "name", "multiple", "archive") VALUES
(1, 'リーダー', 1, FALSE),
(2, 'ハンディモップ', 1, FALSE),
(3, 'アクリルボード', 1, FALSE),
(4, 'ガラス拭き', 1, FALSE),
(5, '除菌シート', 1, FALSE),
(6, '窓の出っ張り', 1, FALSE),
(7, 'コロコロ', 1, FALSE),
(8, 'アルコール拭き', 1, FALSE),
(9, '水拭き', 1, FALSE),
(10, 'ゴミ捨て', 1, FALSE),
(11, '掃除機', 1, TRUE);
