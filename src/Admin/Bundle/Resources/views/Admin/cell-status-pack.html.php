<?php
use StudySauce\Bundle\Entity\Pack;
use \DateTime as Date;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;

/** @var GlobalVariables $app */

/** @var Pack $pack */
$view['slots']->start('cell_status_pack'); ?>
    <div>
        <label class="input select status">
            <select name="status">
                <option value="UNPUBLISHED">Unpublished</option>
                <option value="GROUP" data-confirm="#pack-publish">Published</option>
                <?php
                // TODO: if user changes, we need to force the whole template to rebuild
                if ($app->getUser()->hasRole('ROLE_ADMIN') && $app->getUser()->getEmail() == 'brian@studysauce.com') { ?>
                    <option value="PUBLIC">Public</option>
                    <option value="UNLISTED">Unlisted</option>
                    <option value="DELETED">Deleted</option>
                <?php } ?>
            </select>
        </label>
    </div>
<?php
$view['slots']->stop();


// update the template
$row = jQuery($this);

// TODO: generalize this in a cell-select generic template
$select = $row->find('select');
if ($select->length == 0) {
    $select = $row->append($view['slots']->get('cell_status_pack'))->find('select');
    // TODO: this could be some sort of binding API
    $value = $pack->getStatus();
    $schedule = $pack->getProperty('schedule');
    $publish = [
        'schedule' => !empty($schedule)
            ? (is_a($schedule, Date::class) ? $schedule->format('r') : $schedule)
            : '',
        'email' => $pack->getProperty('email'),
        'alert' => $pack->getProperty('alert'),
    ];
    // create update code vs read code below?
    $select->val(empty($value) ? '' : $value);
    $select->attr('data-publish', json_encode($publish));
    $select->find(implode('', ['option[value="', $value, '"]']))->attr('selected', 'selected');
} else {
// TODO: this is update code specific to status field, generalize this in model
    $publish = $select->data('publish');
    $value = $select->val();
}
$schedule = new Date($publish['schedule']);

// TODO: this is specific to status
$select->parents('.status')->attr('class', implode('', ['status ' , strtolower($value) , ($schedule <= new Date() ? '' : ' pending')]));

// set schedule data
$select->find('option[value="GROUP"]')->text($schedule > new Date()
    ? implode('', ['Pending (', $schedule->format('m/d/Y H:m'), ')'])
    : (!empty($schedule) && $value == 'GROUP' ? 'Published' : 'Publish...'));


print ($row->html());

