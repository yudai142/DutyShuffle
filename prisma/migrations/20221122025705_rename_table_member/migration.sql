/*
  Warnings:

  - You are about to drop the column `menber_id` on the `history` table. All the data in the column will be lost.
  - You are about to drop the `menber` table. If the table is not empty, all the data it contains will be lost.
  - You are about to drop the `menber_option` table. If the table is not empty, all the data it contains will be lost.
  - Added the required column `member_id` to the `history` table without a default value. This is not possible if the table is not empty.

*/
-- DropForeignKey
ALTER TABLE `history` DROP FOREIGN KEY `history_menber_id_fkey`;

-- DropForeignKey
ALTER TABLE `menber_option` DROP FOREIGN KEY `menber_option_menber_id_fkey`;

-- DropForeignKey
ALTER TABLE `menber_option` DROP FOREIGN KEY `menber_option_work_id_fkey`;

-- AlterTable
ALTER TABLE `history` DROP COLUMN `menber_id`,
    ADD COLUMN `member_id` INTEGER NOT NULL;

-- DropTable
DROP TABLE `menber`;

-- DropTable
DROP TABLE `menber_option`;

-- CreateTable
CREATE TABLE `member` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `last_name` VARCHAR(30) NOT NULL,
    `first_name` VARCHAR(30) NOT NULL,
    `kana_name` VARCHAR(255) NOT NULL,
    `archive` BOOLEAN NOT NULL DEFAULT false,

    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `member_option` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `work_id` INTEGER NOT NULL,
    `member_id` INTEGER NOT NULL,
    `status` INTEGER NOT NULL,

    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- AddForeignKey
ALTER TABLE `member_option` ADD CONSTRAINT `member_option_work_id_fkey` FOREIGN KEY (`work_id`) REFERENCES `work`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `member_option` ADD CONSTRAINT `member_option_member_id_fkey` FOREIGN KEY (`member_id`) REFERENCES `member`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `history` ADD CONSTRAINT `history_member_id_fkey` FOREIGN KEY (`member_id`) REFERENCES `member`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
