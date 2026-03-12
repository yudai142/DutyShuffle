const { PrismaClient } = require('@prisma/client');

const prisma = new PrismaClient();

async function main() {
  console.log('🌱 Seeding database...');

  // メンバーデータの挿入
  const memberData = [
    { id: 1, family_name: 'テスト', given_name: '太郎', kana_name: 'てすとたろう', archive: true },
    { id: 2, family_name: 'テスト', given_name: '花子', kana_name: 'てすとはなこ', archive: true },
    { id: 3, family_name: '釘子', given_name: '津佳冴', kana_name: 'くぎこつかさ', archive: false },
    { id: 4, family_name: '長谷川', given_name: '異風', kana_name: 'はせがわいふう', archive: false },
    { id: 5, family_name: '樋口', given_name: '伊吹', kana_name: 'ひぐちいぶき', archive: false },
    { id: 6, family_name: '小泉', given_name: '俊一', kana_name: 'こいずみしゅんいち', archive: false },
    { id: 7, family_name: '北川', given_name: '優斗', kana_name: 'きたがわゆうと', archive: false },
    { id: 8, family_name: '小股', given_name: '晄', kana_name: 'おまたあき', archive: false },
    { id: 9, family_name: '田鹿', given_name: '蒼史', kana_name: 'たじかそうじ', archive: false },
    { id: 10, family_name: '芦生', given_name: '浩明', kana_name: 'あしおひろあき', archive: false },
    { id: 11, family_name: '高宮城', given_name: '誉有治', kana_name: 'たかみやぎようき', archive: false },
    { id: 12, family_name: '朴', given_name: '清', kana_name: 'ぼくきよし', archive: false },
    { id: 13, family_name: '西加治工', given_name: '祐樹', kana_name: 'にしかじくゆうき', archive: false },
    { id: 14, family_name: '雉子谷', given_name: '茂夫', kana_name: 'きじだにしげお', archive: false },
    { id: 15, family_name: '渡谷', given_name: '身志', kana_name: 'わたりやみり', archive: false },
    { id: 16, family_name: '室石', given_name: '遙摯', kana_name: 'むろいしはると', archive: false },
    { id: 17, family_name: '塩足', given_name: '壱', kana_name: 'しおたりいち', archive: false },
    { id: 18, family_name: '筒屋', given_name: '厳春', kana_name: 'つつやみねはる', archive: false },
    { id: 19, family_name: '日陰茂井', given_name: '昊', kana_name: 'ひかげもいそら', archive: false },
    { id: 20, family_name: '精廬', given_name: '里備', kana_name: 'とぐろさとはる', archive: false },
    { id: 21, family_name: '喜美候部', given_name: '智絃', kana_name: 'きみこうべちづる', archive: false },
    { id: 22, family_name: '竹乘', given_name: '成也', kana_name: 'たけのりなりや', archive: false },
    { id: 23, family_name: '安達', given_name: '城灯', kana_name: 'あだちきと', archive: false },
    { id: 24, family_name: '森田', given_name: '悠翔', kana_name: 'もりたはると', archive: false },
    { id: 25, family_name: '矢野', given_name: '英一', kana_name: 'やのえいいち', archive: false },
    { id: 26, family_name: '誉田', given_name: '和樹', kana_name: 'ほまれだかずき', archive: false },
    { id: 27, family_name: '数', given_name: '瑛斗', kana_name: 'かずえいと', archive: false },
  ];

  console.log(`📝 Inserting ${memberData.length} members...`);
  await prisma.member.createMany({
    data: memberData,
    skipDuplicates: true,
  });

  // 作業データの挿入
  const workData = [
    { id: 1, name: 'リーダー', multiple: 1, archive: false },
    { id: 2, name: 'ハンディモップ', multiple: 1, archive: false },
    { id: 3, name: 'アクリルボード', multiple: 1, archive: false },
    { id: 4, name: 'ガラス拭き', multiple: 1, archive: false },
    { id: 5, name: '除菌シート', multiple: 1, archive: false },
    { id: 6, name: '窓の出っ張り', multiple: 1, archive: false },
    { id: 7, name: 'コロコロ', multiple: 1, archive: false },
    { id: 8, name: 'アルコール拭き', multiple: 1, archive: false },
    { id: 9, name: '水拭き', multiple: 1, archive: false },
    { id: 10, name: 'ゴミ捨て', multiple: 1, archive: false },
    { id: 11, name: '掃除機', multiple: 1, archive: true },
  ];

  console.log(`📝 Inserting ${workData.length} works...`);
  await prisma.work.createMany({
    data: workData,
    skipDuplicates: true,
  });

  console.log('✅ Seed completed successfully!');
}

main()
  .catch(e => {
    console.error('❌ Seed failed:', e);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });
