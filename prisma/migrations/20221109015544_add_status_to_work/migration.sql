-- AlterTable
ALTER TABLE `menber_option` MODIFY `status` INTEGER NOT NULL;

-- AlterTable
ALTER TABLE `work` ADD COLUMN `archive` BOOLEAN NOT NULL DEFAULT false;
