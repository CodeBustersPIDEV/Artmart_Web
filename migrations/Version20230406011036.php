<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230406011036 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity (activityID INT AUTO_INCREMENT NOT NULL, date DATETIME DEFAULT CURRENT_TIMESTAMP  NOT NULL, title VARCHAR(255) NOT NULL, host VARCHAR(255) NOT NULL, eventID INT DEFAULT NULL, INDEX eventID (eventID), PRIMARY KEY(activityID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `admin` (admin_ID INT AUTO_INCREMENT NOT NULL, user_ID INT NOT NULL, department VARCHAR(255) DEFAULT NULL , INDEX user_ID (user_ID), PRIMARY KEY(admin_ID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE apply (apply_ID INT AUTO_INCREMENT NOT NULL, status VARCHAR(255) NOT NULL, artist_ID INT DEFAULT NULL, customproduct_ID INT DEFAULT NULL, INDEX customproduct_ID (customproduct_ID), INDEX artist_ID (artist_ID), PRIMARY KEY(apply_ID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE artist (artist_ID INT AUTO_INCREMENT NOT NULL, nbr_artwork INT NOT NULL, user_ID INT NOT NULL, bio VARCHAR(255) DEFAULT NULL , INDEX IDX_1599687984D7FF (user_ID), PRIMARY KEY(artist_ID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog_tags (id INT AUTO_INCREMENT NOT NULL, blog_id INT DEFAULT NULL, tag_id INT DEFAULT NULL, INDEX blog_id (blog_id), INDEX tag_id (tag_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blogcategories (categories_ID INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(categories_ID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blogs (author INT DEFAULT NULL, blogs_ID INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, content TEXT NOT NULL, date DATETIME DEFAULT CURRENT_TIMESTAMP  NOT NULL, rating DOUBLE PRECISION DEFAULT NULL , nb_views INT NOT NULL, INDEX IDX_F41BCA70BDAFD8C8 (author), PRIMARY KEY(blogs_ID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categories (categories_ID INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(categories_ID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (client_ID INT AUTO_INCREMENT NOT NULL, nbr_orders INT NOT NULL, nbr_demands INT NOT NULL, user_ID INT DEFAULT NULL, INDEX IDX_C7440455984D7FF (user_ID), PRIMARY KEY(client_ID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comments (author INT DEFAULT NULL, comments_ID INT AUTO_INCREMENT NOT NULL, content TEXT NOT NULL, date DATETIME DEFAULT CURRENT_TIMESTAMP , rating INT DEFAULT NULL, blog_ID INT DEFAULT NULL, INDEX author (author), INDEX blog_ID (blog_ID), PRIMARY KEY(comments_ID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customproduct (custom_product_ID INT AUTO_INCREMENT NOT NULL, client_ID INT DEFAULT NULL, product_ID INT DEFAULT NULL, INDEX client_ID (client_ID), INDEX product_ID (product_ID), PRIMARY KEY(custom_product_ID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (eventID INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, location VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, description TEXT NOT NULL, entryFee DOUBLE PRECISION NOT NULL, capacity INT NOT NULL, startDate DATETIME NOT NULL, endDate DATETIME NOT NULL, image VARCHAR(255) DEFAULT NULL , status VARCHAR(255) DEFAULT \'\'\'Scheduled\'\'\', userID INT DEFAULT NULL, INDEX userID (userID), PRIMARY KEY(eventID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE eventreport (reportID INT AUTO_INCREMENT NOT NULL, attendance INT NOT NULL, eventID INT DEFAULT NULL, INDEX eventID (eventID), PRIMARY KEY(reportID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feedback (feedbackID INT AUTO_INCREMENT NOT NULL, rating INT NOT NULL, comment TEXT NOT NULL, date DATETIME DEFAULT CURRENT_TIMESTAMP , eventID INT DEFAULT NULL, userID INT DEFAULT NULL, INDEX eventID (eventID), INDEX userID (userID), PRIMARY KEY(feedbackID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE has_blog_category (id INT AUTO_INCREMENT NOT NULL, blog_id INT DEFAULT NULL, category_id INT DEFAULT NULL, INDEX blog_id (blog_id), INDEX category_id (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media (blog_id INT DEFAULT NULL, media_ID INT AUTO_INCREMENT NOT NULL, file_name VARCHAR(255) NOT NULL, file_type VARCHAR(255) NOT NULL, file_path VARCHAR(255) NOT NULL, INDEX IDX_6A2CA10CDAE07E97 (blog_id), PRIMARY KEY(media_ID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL , INDEX IDX_75EA56E016BA31DB (delivered_at), INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (order_ID INT AUTO_INCREMENT NOT NULL, Quantity INT DEFAULT NULL, ShippingAddress TEXT DEFAULT NULL, OrderDate DATE DEFAULT CURRENT_TIMESTAMP, TotalCost NUMERIC(10, 2) DEFAULT NULL, UserID INT DEFAULT NULL, ProductID INT DEFAULT NULL, ShippingMethod INT DEFAULT NULL, PaymentMethod INT DEFAULT NULL, INDEX ShippingMethod (ShippingMethod), INDEX PaymentMethod (PaymentMethod), INDEX ProductID (ProductID), INDEX UserID (UserID), PRIMARY KEY(order_ID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orderrefund (orderRefund_ID INT AUTO_INCREMENT NOT NULL, RefundAmount NUMERIC(10, 2) DEFAULT NULL , Reason TEXT DEFAULT NULL, Date DATE DEFAULT NULL , OrderID INT DEFAULT NULL, INDEX OrderID (OrderID), PRIMARY KEY(orderRefund_ID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orderstatus (orderStatus_ID INT AUTO_INCREMENT NOT NULL, Status VARCHAR(255) DEFAULT NULL , Date DATE DEFAULT NULL , OrderID INT DEFAULT NULL, INDEX OrderID (OrderID), PRIMARY KEY(orderStatus_ID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orderupdate (orderUpdate_ID INT AUTO_INCREMENT NOT NULL, UpdateMessage TEXT DEFAULT NULL, Date DATE DEFAULT NULL , OrderID INT DEFAULT NULL, INDEX OrderID (OrderID), PRIMARY KEY(orderUpdate_ID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participation (participationID INT AUTO_INCREMENT NOT NULL, attendanceStatus VARCHAR(255) DEFAULT \'\'\'Not attending\'\'\', registrationDate DATETIME DEFAULT CURRENT_TIMESTAMP , eventID INT DEFAULT NULL, userID INT DEFAULT NULL, INDEX userID (userID), INDEX IDX_AB55E24F10409BA4 (eventID), UNIQUE INDEX eventID (eventID, userID), PRIMARY KEY(participationID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paymentoption (paymentOption_ID INT AUTO_INCREMENT NOT NULL, Name VARCHAR(255) DEFAULT NULL , AvailableCountries VARCHAR(255) DEFAULT NULL , PRIMARY KEY(paymentOption_ID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (product_ID INT AUTO_INCREMENT NOT NULL, category_ID INT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, dimensions VARCHAR(255) NOT NULL, weight NUMERIC(10, 2) NOT NULL, material VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, INDEX category_ID (category_ID), PRIMARY KEY(product_ID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE productreview (review_ID INT AUTO_INCREMENT NOT NULL, ready_product_ID INT NOT NULL, user_ID INT NOT NULL, title VARCHAR(255) NOT NULL, text TEXT NOT NULL, rating DOUBLE PRECISION NOT NULL, date DATETIME DEFAULT CURRENT_TIMESTAMP , INDEX user_ID (user_ID), INDEX ready_product_ID (ready_product_ID), PRIMARY KEY(review_ID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE readyproduct (ready_product_ID INT AUTO_INCREMENT NOT NULL, price INT NOT NULL, product_ID INT NOT NULL, user_ID INT NOT NULL, INDEX user_ID (user_ID), INDEX product_ID (product_ID), PRIMARY KEY(ready_product_ID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE receipt (receipt_ID INT AUTO_INCREMENT NOT NULL, OrderID INT DEFAULT NULL, ProductID INT DEFAULT NULL, Quantity INT DEFAULT NULL, Price NUMERIC(10, 2) DEFAULT NULL , Tax NUMERIC(10, 2) DEFAULT NULL , TotalCost NUMERIC(10, 2) DEFAULT NULL , Date DATE DEFAULT NULL , INDEX OrderID (OrderID), INDEX ProductID (ProductID), PRIMARY KEY(receipt_ID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE salesreport (salesReport_ID INT AUTO_INCREMENT NOT NULL, ProductID INT DEFAULT NULL, TotalSales NUMERIC(10, 2) DEFAULT NULL , AverageSalesPerDay NUMERIC(10, 2) DEFAULT NULL , Date DATE DEFAULT NULL , INDEX ProductID (ProductID), PRIMARY KEY(salesReport_ID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shippingoption (shippingOption_ID INT AUTO_INCREMENT NOT NULL, Name VARCHAR(255) DEFAULT NULL , Carrier VARCHAR(255) DEFAULT NULL , ShippingSpeed VARCHAR(255) DEFAULT NULL , ShippingFee NUMERIC(10, 2) DEFAULT NULL , AvailableRegions VARCHAR(255) DEFAULT NULL , PRIMARY KEY(shippingOption_ID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tags (tags_ID INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(tags_ID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (user_ID INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, birthday DATE NOT NULL, phoneNumber VARCHAR(255) NOT NULL, role VARCHAR(30) NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, picture VARCHAR(255) DEFAULT NULL , blocked TINYINT(1) DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, dateOfCreation DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, token VARCHAR(255) DEFAULT NULL , PRIMARY KEY(user_ID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wishlist (wishlist_ID INT AUTO_INCREMENT NOT NULL, UserID INT DEFAULT NULL, ProductID INT DEFAULT NULL, Date DATE DEFAULT NULL , Quantity INT DEFAULT NULL, Price DOUBLE PRECISION DEFAULT NULL , INDEX ProductID (ProductID), INDEX UserID (UserID), PRIMARY KEY(wishlist_ID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095A10409BA4');
        $this->addSql('ALTER TABLE `admin` DROP FOREIGN KEY FK_880E0D76984D7FF');
        $this->addSql('ALTER TABLE apply DROP FOREIGN KEY FK_BD2F8C1F197D0892');
        $this->addSql('ALTER TABLE apply DROP FOREIGN KEY FK_BD2F8C1FECC8B48B');
        $this->addSql('ALTER TABLE artist DROP FOREIGN KEY FK_1599687984D7FF');
        $this->addSql('ALTER TABLE blog_tags DROP FOREIGN KEY FK_8F6C18B6DAE07E97');
        $this->addSql('ALTER TABLE blog_tags DROP FOREIGN KEY FK_8F6C18B6BAD26311');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455984D7FF');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962ABDAFD8C8');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A740A7AFD');
        $this->addSql('ALTER TABLE customproduct DROP FOREIGN KEY FK_1ED4372AB7016D4B');
        $this->addSql('ALTER TABLE customproduct DROP FOREIGN KEY FK_1ED4372AEB6E6230');
        $this->addSql('ALTER TABLE eventreport DROP FOREIGN KEY FK_F0C2A3410409BA4');
        $this->addSql('ALTER TABLE feedback DROP FOREIGN KEY FK_D229445810409BA4');
        $this->addSql('ALTER TABLE feedback DROP FOREIGN KEY FK_D22944585FD86D04');
        $this->addSql('ALTER TABLE has_blog_category DROP FOREIGN KEY FK_D0E841E1DAE07E97');
        $this->addSql('ALTER TABLE has_blog_category DROP FOREIGN KEY FK_D0E841E112469DE2');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10CDAE07E97');
        $this->addSql('ALTER TABLE orderrefund DROP FOREIGN KEY FK_528A8C39EF06D63');
        $this->addSql('ALTER TABLE orderstatus DROP FOREIGN KEY FK_72A6FD7DEF06D63');
        $this->addSql('ALTER TABLE orderupdate DROP FOREIGN KEY FK_9183AD19EF06D63');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24F10409BA4');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24F5FD86D04');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE `admin`');
        $this->addSql('DROP TABLE apply');
        $this->addSql('DROP TABLE artist');
        $this->addSql('DROP TABLE blog_tags');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE customproduct');
        $this->addSql('DROP TABLE eventreport');
        $this->addSql('DROP TABLE feedback');
        $this->addSql('DROP TABLE has_blog_category');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('DROP TABLE orderrefund');
        $this->addSql('DROP TABLE orderstatus');
        $this->addSql('DROP TABLE orderupdate');
        $this->addSql('DROP TABLE participation');
        $this->addSql('DROP TABLE productreview');
        $this->addSql('DROP TABLE readyproduct');
        $this->addSql('DROP TABLE receipt');
        $this->addSql('DROP TABLE salesreport');
        $this->addSql('DROP TABLE tags');
        $this->addSql('DROP TABLE wishlist');
        $this->addSql('ALTER TABLE event CHANGE status status VARCHAR(255) DEFAULT \'\'\'\'\'\'\'Scheduled\'\'\'\'\'\'\'');
        $this->addSql('ALTER TABLE `order` CHANGE OrderDate OrderDate DATE DEFAULT CURRENT_TIMESTAMP, CHANGE TotalCost TotalCost NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE dateOfCreation dateOfCreation DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }
}
