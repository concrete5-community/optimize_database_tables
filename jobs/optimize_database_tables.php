<?php

namespace Concrete\Package\OptimizeDatabaseTables\Job;

use A3020\OptimizeDatabaseTables\Job\Optimizer;
use Concrete\Core\Job\Job;
use Concrete\Core\Support\Facade\Application;

final class OptimizeDatabaseTables extends Job
{
    public function getJobName()
    {
        return t('Optimize Database Tables');
    }

    public function getJobDescription()
    {
        return
            t('Optimizes the MySQL database tables.') . ' ' .
            t('The tables and indexes are reorganized, and disk space will be reclaimed.') . ' ' .
            t('After extensive changes to tables, the job may also improve performance of statements, sometimes significantly.');
    }

    public function run()
    {
        $app = Application::getFacadeApplication();

        /** @var Optimizer $optimizer */
        $optimizer = $app->make(Optimizer::class);

        return $optimizer->run();
    }
}
