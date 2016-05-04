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

// TODO: generalize this in a cell-select generic template
$status = $row->find('> div');
if($status->length == 0) {
    $status = $row->append($view['slots']->get('cell_status_pack'))->find('> div');
    // TODO: this is specific to status
    $select = $status->find('select');
    if ($app->getUser()->hasRole('ROLE_ADMIN') && $app->getUser()->getEmail() == 'brian@studysauce.com') {
        $select->append($view['slots']->get('cell_status_pack_admin'));
    }
    // TODO: this could be some sort of binding API
    $publish = [
        'schedule' => !empty($pack->getProperty('schedule'))
            ? $pack->getProperty('schedule')->format('r')
            : '',
        'email' => $pack->getProperty('email'),
        'alert' => $pack->getProperty('alert'),
    ];
    $value = $pack->getStatus();
    $select->val(empty($value) ? '' : $value);
    $select->data('publish', $publish)->attr('data-publish', json_encode($publish));
    $select->find(concat('option[value="' , $value , '"]'))->attr('selected', 'selected');
}
else {
// TODO: this is update code specific to status field, generalize this in model
    $select = $status->find('select');
    $publish = $select->data('publish');
}
$schedule = new Date($publish['schedule']);

$status->attr('class', concat(strtolower($select->val()) , ($schedule <= new Date() ? '' : ' pending')));

// set schedule data
$status->find('option[value="GROUP"]')->text($schedule > new Date()
    ? concat('Pending (' , $schedule->format('m/d/Y H:m') , ')')
    : (!empty($schedule) ? 'Published' : 'Publish'));


print ($row->html());

