-- CreateTable
CREATE TABLE `work` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `multiple` INTEGER UNSIGNED NULL,

    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `menber` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `last_name` VARCHAR(30) NOT NULL,
    `first_name` VARCHAR(30) NOT NULL,
    `kana_name` VARCHAR(255) NOT NULL,
    `archive` BOOLEAN NOT NULL DEFAULT false,

    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `menber_option` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `work_id` INTEGER NOT NULL,
    `menber_id` INTEGER NOT NULL,
    `status` BOOLEAN NOT NULL DEFAULT false,

    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `history` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `work_id` INTEGER NULL,
    `menber_id` INTEGER NOT NULL,
    `day` DATE NOT NULL,

    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `shuffle_option` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `interval` INTEGER NULL DEFAULT 7,

    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- AddForeignKey
ALTER TABLE `menber_option` ADD CONSTRAINT `menber_option_work_id_fkey` FOREIGN KEY (`work_id`) REFERENCES `work`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `menber_option` ADD CONSTRAINT `menber_option_menber_id_fkey` FOREIGN KEY (`menber_id`) REFERENCES `menber`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `history` ADD CONSTRAINT `history_work_id_fkey` FOREIGN KEY (`work_id`) REFERENCES `work`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `history` ADD CONSTRAINT `history_menber_id_fkey` FOREIGN KEY (`menber_id`) REFERENCES `menber`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
