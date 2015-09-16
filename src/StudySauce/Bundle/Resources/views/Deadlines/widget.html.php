<?php
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Deadline;

?>
<div class="widget-wrapper">
    <div class="widget deadlines-widget">
        <h3>Upcoming deadlines</h3>
        <div class="auto-height">
            <div class="the-list">
                <?php if(empty($deadlines)) { ?>
                    <a href="<?php print $view['router']->generate('deadlines'); ?>" class="cloak">Nothing set up yet.  Click <span class="reveal">here</span> to set up deadlines.</a>
                <?php } else { ?>
                    <?php foreach ($deadlines as $i => $d) {
                        /** @var $d Deadline */
                        ?>
                        <div class="deadline-row">
                        <i class="class<?php print (!empty($d->getCourse()) ? $d->getCourse()->getIndex() : ''); ?>">&nbsp;</i>
                        <strong><span><?php print $d->getDueDate()->format('j'); ?></span> <?php print $d->getDueDate()->format('M'); ?></strong>
                        <div><?php print $d->getAssignment(); ?></div>
                        </div><?php
                    }
                } ?>
            </div>
        </div>
    </div>
</div>

