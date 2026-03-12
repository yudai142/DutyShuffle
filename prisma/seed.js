const { PrismaClient } = require('@prisma/client');

const prisma = new PrismaClient();

async function main() {
  console.log('🌱 Seeding database...');

  // メンバーデータの挿入
  const memberData = [
    { id: 1, familyName: 'テスト', givenName: '太郎', kanaName: 'てすとたろう', archive: true },
    { id: 2, familyName: 'テスト', givenName: '花子', kanaName: 'てすとはなこ', archive: true },
    { id: 3, familyName: '釘子', givenName: '津佳冴', kanaName: 'くぎこつかさ', archive: false },
    { id: 4, familyName: '長谷川', givenName: '異風', kanaName: 'はせがわいふう', archive: false },
    { id: 5, familyName: '樋口', givenName: '伊吹', kanaName: 'ひぐちいぶき', archive: false },
    { id: 6, familyName: '小泉', givenName: '俊一', kanaName: 'こいずみしゅんいち', archive: false },
    { id: 7, familyName: '北川', givenName: '優斗', kanaName: 'きたがわゆうと', archive: false },
    { id: 8, familyName: '小股', givenName: '晄', kanaName: 'おまたあき', archive: false },
    { id: 9, familyName: '田鹿', givenName: '蒼史', kanaName: 'たじかそうじ', archive: false },
    { id: 10, familyName: '芦生', givenName: '浩明', kanaName: 'あしおひろあき', archive: false },
    { id: 11, familyName: '高宮城', givenName: '誉有治', kanaName: 'たかみやぎようき', archive: false },
    { id: 12, familyName: '朴', givenName: '清', kanaName: 'ぼくきよし', archive: false },
    { id: 13, familyName: '西加治工', givenName: '祐樹', kanaName: 'にしかじくゆうき', archive: false },
    { id: 14, familyName: '雉子谷', givenName: '茂夫', kanaName: 'きじだにしげお', archive: false },
    { id: 15, familyName: '渡谷', givenName: '身志', kanaName: 'わたりやみり', archive: false },
    { id: 16, familyName: '室石', givenName: '遙摯', kanaName: 'むろいしはると', archive: false },
    { id: 17, familyName: '塩足', givenName: '壱', kanaName: 'しおたりいち', archive: false },
    { id: 18, familyName: '筒屋', givenName: '厳春', kanaName: 'つつやみねはる', archive: false },
    { id: 19, familyName: '日陰茂井', givenName: '昊', kanaName: 'ひかげもいそら', archive: false },
    { id: 20, familyName: '精廬', givenName: '里備', kanaName: 'とぐろさとはる', archive: false },
    { id: 21, familyName: '喜美候部', givenName: '智絃', kanaName: 'きみこうべちづる', archive: false },
    { id: 22, familyName: '竹乘', givenName: '成也', kanaName: 'たけのりなりや', archive: false },
    { id: 23, familyName: '安達', givenName: '城灯', kanaName: 'あだちきと', archive: false },
    { id: 24, familyName: '森田', givenName: '悠翔', kanaName: 'もりたはると', archive: false },
    { id: 25, familyName: '矢野', givenName: '英一', kanaName: 'やのえいいち', archive: false },
    { id: 26, familyName: '誉田', givenName: '和樹', kanaName: 'ほまれだかずき', archive: false },
    { id: 27, familyName: '数', givenName: '瑛斗', kanaName: 'かずえいと', archive: false },
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
