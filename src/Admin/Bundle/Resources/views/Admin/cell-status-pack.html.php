<?php
use StudySauce\Bundle\Entity\Pack;
use \DateTime as Date;

/** @var Pack $pack */
$view['slots']->start('cell_status_pack'); ?>
    <div>
        <label class="input status">
            <select name="status">
                <option value="UNPUBLISHED">Unpublished</option>
                <option value="GROUP">Published</option>
            </select>
        </label>
    </div>
<?php
$view['slots']->stop();

$view['slots']->start('cell_status_pack_admin'); ?>
    <option value="PUBLIC">Public</option>
    <option value="UNLISTED">Unlisted</option>
    <option value="DELETED">Deleted</option>
<?php
$view['slots']->stop();


// update the template
$row = jQuery($this);

$status = $row->find('> div');
if($status->length == 0) {
    $status = $row->append($view['slots']->get('cell_status_pack'))->find('> div');
}

$select = $status->find('select');

$schedule = $select->data('publish');
$value = $select->val();
if(empty($schedule)) {
    $schedule = [
        'schedule' => !empty($pack->getProperty('schedule')) ? $pack->getProperty('schedule')->format('r') : '',
        'email' => $pack->getProperty('email'),
        'alert' => $pack->getProperty('alert'),
    ];
    $select->data('publish', $schedule)->attr('data-publish', json_encode($schedule));
    $value = $pack->getStatus();
}

$status->attr('class', strtolower($value) . ($schedule['schedule'] <= new Date() ? '' : ' pending'));

// set schedule data
$status->find('option[value="GROUP"]')->text($schedule['schedule'] > new Date()
    ? ('Pending (' . $schedule['schedule']->format('m/d/Y H:m') . ')')
    : (!empty($schedule) ? 'Published' : 'Publish'));

if ($app->getUser()->hasRole('ROLE_ADMIN') && $app->getUser()->getEmail() == 'brian@studysauce.com' &&
    $status->find('option[value="PUBLIC"]')->length == 0
) {
    $select->append($view['slots']->get('cell_status_pack_admin'));
}

if($select->val() != $value) {
    $status->find('option[selected]')->removeAttr('selected');
    $select->val(empty($value) ? '' : $value);
    $select->find('option[value="' . $value . '"]')->attr('selected', 'selected');
}

print ($row->html());

