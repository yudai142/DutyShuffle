/*
  Warnings:

  - You are about to drop the column `day` on the `history` table. All the data in the column will be lost.
  - You are about to drop the column `first_name` on the `member` table. All the data in the column will be lost.
  - You are about to drop the column `last_name` on the `member` table. All the data in the column will be lost.
  - Added the required column `date` to the `history` table without a default value. This is not possible if the table is not empty.
  - Added the required column `family_name` to the `member` table without a default value. This is not possible if the table is not empty.
  - Added the required column `given_name` to the `member` table without a default value. This is not possible if the table is not empty.

*/
-- AlterTable
ALTER TABLE `history` DROP COLUMN `day`,
    ADD COLUMN `date` DATE NOT NULL;

-- AlterTable
ALTER TABLE `member` DROP COLUMN `first_name`,
    DROP COLUMN `last_name`,
    ADD COLUMN `family_name` VARCHAR(30) NOT NULL AFTER `id`,
    ADD COLUMN `given_name` VARCHAR(30) NOT NULL AFTER `family_name`;

-- CreateTable
CREATE TABLE `off_work` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `work_id` INTEGER NOT NULL,
    `date` DATE NOT NULL,

    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- AddForeignKey
ALTER TABLE `off_work` ADD CONSTRAINT `off_work_work_id_fkey` FOREIGN KEY (`work_id`) REFERENCES `work`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
