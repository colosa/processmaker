<?php if ($table->hasPrimaryKey()) { ?>
    CONSTRAINT <?php echo $table->getName() ?>_PKsss PRIMARY KEY(<?php echo $table->printPrimaryKey() ?>),
<?php } ?>