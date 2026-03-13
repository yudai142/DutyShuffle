-- AlterTable - Change reset_date from TEXT to DATE
ALTER TABLE "shuffle_option" DROP COLUMN "reset_date";
ALTER TABLE "shuffle_option" ADD COLUMN "reset_date" date;
