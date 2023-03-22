<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230322121555 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prize ADD language_id INT NOT NULL');
        $this->addSql('ALTER TABLE prize ADD CONSTRAINT FK_51C88BC182F1BAF4 FOREIGN KEY (language_id) REFERENCES language (id)');
        $this->addSql('CREATE INDEX IDX_51C88BC182F1BAF4 ON prize (language_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prize DROP FOREIGN KEY FK_51C88BC182F1BAF4');
        $this->addSql('DROP INDEX IDX_51C88BC182F1BAF4 ON prize');
        $this->addSql('ALTER TABLE prize DROP language_id');
    }
}
