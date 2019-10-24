<?php
declare(strict_types=1);

namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Migrations\AbortMigrationException;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add relation table for optimized resources.
 */
class Version20180731154744 extends AbstractMigration
{

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Add relation table for optimized resources.';
    }

    /**
     * @param Schema $schema
     * @return void
     * @throws DBALException
     * @throws AbortMigrationException
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on "mysql".');

        $this->addSql('CREATE TABLE flownative_imageoptimizer_domain_model_optimizedresourcer_8d9e5 (originalresourceidentificationhash VARCHAR(255) NOT NULL, optimizedresource VARCHAR(40) DEFAULT NULL, UNIQUE INDEX UNIQ_DA9A4AC721E3BF4 (optimizedresource), PRIMARY KEY(originalresourceidentificationhash)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE flownative_imageoptimizer_domain_model_optimizedresourcer_8d9e5 ADD CONSTRAINT FK_DA9A4AC721E3BF4 FOREIGN KEY (optimizedresource) REFERENCES neos_flow_resourcemanagement_persistentresource (persistence_object_identifier)');
    }

    /**
     * @param Schema $schema
     * @return void
     * @throws DBALException
     * @throws AbortMigrationException
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on "mysql".');

        $this->addSql('DROP TABLE flownative_imageoptimizer_domain_model_optimizedresourcer_8d9e5');
    }
}
