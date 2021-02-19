<?php

namespace A3020\OptimizeDatabaseTables\Job;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Utility\Service\Number;
use Exception;
use Psr\Log\LoggerInterface;

class Optimizer implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * @var \Concrete\Core\Database\Connection\Connection
     */
    private $db;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Concrete\Core\Utility\Service\Number
     */
    private $numberHelper;

    public function __construct(Connection $db, LoggerInterface $logger, Number $numberHelper)
    {
        $this->db = $db;
        $this->logger = $logger;
        $this->numberHelper = $numberHelper;
    }

    public function run()
    {
        // Alternative could be SELECT Data_free FROM information_schema.tables WHERE table_schema = DATABASE()
        // But I didn't find it any faster / simpler.
        $tableStatuses = $this->db->fetchAll('SHOW TABLE STATUS');

        $possibleGain = 0;
        foreach ($tableStatuses as $tableStatus) {
            // Data_free is the column name defined by MySQL
            // and denotes the amount of unused space due to fragmentation.
            $possibleGain += $tableStatus['Data_free'];
        }

        // Nothing to do...
        if ($possibleGain === 0) {
            return $this->successMessage();
        }

        foreach ($tableStatuses as $tableStatus) {
            try {
                // Name is a column name defined by MySQL
                // and contains the database table name.
                $this->optimizeTable($tableStatus['Name']);

                $this->updateSpaceFreed($tableStatus['Data_free']);
            } catch (Exception $e) {
                $this->logger->debug($e->getMessage());
            }
        }

        return $this->successMessage();
    }

    /**
     * Update track record in database of how much space has been gained
     *
     * @param int $freed In bytes
     */
    private function updateSpaceFreed($freed)
    {
        if ($freed === 0) {
            return;
        }

        $config = $this->app->make('config/database');

        $config->save('optimize_database_tables.space_freed',
            $this->getTotalSpaceFreed() + $freed
        );
    }

    /**
     * Get total space that has been gained (from database config)
     *
     * @return int Space that has been freed in bytes
     */
    private function getTotalSpaceFreed()
    {
        $config = $this->app->make('config/database');

        return (int) $config->get('optimize_database_tables.space_freed', 0);
    }

    /**
     * @param array $table The table name
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function optimizeTable($table)
    {
        $this->db->executeQuery('OPTIMIZE TABLE ' . $table);
    }

    /**
     * @return string
     */
    private function successMessage()
    {
        $freed = $this->getTotalSpaceFreed();

        if ($freed === 0) {
            return t('All tables are optimized.');
        }

        return t('All tables have been optimized. Total space freed: %s.',
            $this->numberHelper->formatSize($freed)
        );
    }
}
