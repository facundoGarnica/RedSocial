<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250613215113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE post CHANGE descripcion descripcion VARCHAR(255) DEFAULT NULL, CHANGE imagen imagen VARCHAR(30) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reaccion_post ADD post_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reaccion_post ADD CONSTRAINT FK_EA60DD824B89032C FOREIGN KEY (post_id) REFERENCES post (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_EA60DD824B89032C ON reaccion_post (post_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE roles roles JSON NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT 'NULL'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE post CHANGE descripcion descripcion VARCHAR(255) DEFAULT 'NULL', CHANGE imagen imagen VARCHAR(30) DEFAULT 'NULL'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reaccion_post DROP FOREIGN KEY FK_EA60DD824B89032C
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_EA60DD824B89032C ON reaccion_post
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reaccion_post DROP post_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `user` CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`
        SQL);
    }
}
