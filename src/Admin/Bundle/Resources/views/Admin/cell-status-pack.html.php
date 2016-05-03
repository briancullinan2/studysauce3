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
$view['slots']->stop('cell_status_pack');

$view['slots']->start('cell_status_pack_admin'); ?>
    <option value="PUBLIC">Public</option>
    <option value="UNLISTED">Unlisted</option>
    <option value="DELETED">Deleted</option>
<?php
$view['slots']->stop('cell_status_pack');


// update the template
$row = phpQuery::newDocument('');

$schedule = [
    'schedule' => $pack->getProperty('schedule'),
    'email' => $pack->getProperty('email'),
    'alert' => $pack->getProperty('alert'),
    ];

$status = $row->find('> div');
if($status->length == 0) {
    $status = $row->append($view['slots']->get('cell_status_pack'))->find('> div');
}

$status->attr('class', strtolower($pack->getStatus()) . ($schedule['schedule'] <= new Date() ? '' : 'pending'));

$select = $status->find('select');

// set schedule data
$select->data('publish', $schedule);
$status->find('option[value="GROUP"]')->text($schedule > new Date()
    ? 'Pending (' . $schedule['schedule']->format('m/d/Y H:m')
    : (!empty($schedule) ? 'Published' : 'Publish'));

if ($app->getUser()->hasRole('ROLE_ADMIN') && $app->getUser()->getEmail() == 'brian@studysauce.com' &&
    $status->find('option[value="PUBLIC"]')->length == 0
) {
    $select->append($view['slots']->get('cell_status_pack_admin'));
}

if($select->val() != $pack->getStatus()) {
    $status->find('option[selected]')->removeAttr('selected');
    $select->val(empty($pack->getStatus()) ? '' : $pack->getStatus());
    $select->find('option[value="' . $pack->getStatus() . '"]')->attr('selected', 'selected');
}

print ($row->htmlOuter());

