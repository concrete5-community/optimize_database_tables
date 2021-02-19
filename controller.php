<?php

namespace Concrete\Package\OptimizeDatabaseTables;

use A3020\OptimizeDatabaseTables\Installer\Installer;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Package\Package;

final class Controller extends Package
{
    protected $pkgHandle = 'optimize_database_tables';
    protected $appVersionRequired = '8.0.0';
    protected $pkgVersion = '1.0.0';
    protected $pkgAutoloaderRegistries = [
        'src/OptimizeDatabaseTables' => '\A3020\OptimizeDatabaseTables',
    ];

    public function getPackageName()
    {
        return t('Optimize Database Tables');
    }

    public function getPackageDescription()
    {
        return t('Uses OPTIMIZE TABLE to optimize all MySQL tables.');
    }

    public function install()
    {
        $pkg = parent::install();

        $installer = $this->app->make(Installer::class);
        $installer->install($pkg);
    }

    public function uninstall()
    {
        parent::uninstall();

        // Clean up some statistics that were saved in the database
        $db = $this->app->make(Connection::class);
        $db->executeQuery('DELETE FROM Config WHERE configGroup = ?', [
            'optimize_database_tables',
        ]);
    }
}
