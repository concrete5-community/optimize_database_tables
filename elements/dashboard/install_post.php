<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Url;
?>

<p><?php echo t("Congratulations, %s has been installed!", t('Optimize Database Tables')); ?></p>
<br>

<p>
    <?php echo t(/*i18n: %s is a page name (automated jobs)*/ 'To run the optimizer, go to %s.', t('Automated Jobs')); ?>
</p>

<a class="btn btn-primary" href="<?php echo Url::to('/dashboard/system/optimization/jobs') ?>">
    <?php
    echo t('Automated Jobs');
    ?>
</a>
<br>
<hr>

<p>
    <i class="fa fa-info-circle"></i> <?php echo t('For large websites the CLI mode is recommended:'); ?>
</p>

<code style="font-size: 13px;">./concrete/bin/concrete5 c5:job optimize_database_tables</code>
