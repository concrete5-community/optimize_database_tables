<?php

namespace A3020\OptimizeDatabaseTables\Installer;

use Concrete\Core\Job\Job;

class Installer
{
    /**
     * @param \Concrete\Core\Package\Package $pkg
     */
    public function install($pkg)
    {
        $job = Job::getByHandle('optimize_database_tables');

        if (!$job) {
            Job::installByPackage('optimize_database_tables', $pkg);
        }
    }
}
