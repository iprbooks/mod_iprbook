<?php

use Iprbooks\Ebs\Sdk\Client;
use Iprbooks\Ebs\Sdk\Managers\IntegrationManager;
use Iprbooks\Ebs\Sdk\Models\Book;
use Iprbooks\Ebs\Sdk\Models\User;

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');
require_once($CFG->dirroot . '/mod/iprbook/vendor/autoload.php');

$id = optional_param('id', 0, PARAM_INT);
$i = optional_param('i', 0, PARAM_INT);

if ($id) {
    $cm = get_coursemodule_from_id('iprbook', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $moduleinstance = $DB->get_record('iprbook', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($i) {
    $moduleinstance = $DB->get_record('iprbook', array('id' => $n), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('iprbook', $moduleinstance->id, $course->id, false, MUST_EXIST);
} else {
    print_error(get_string('missingidandcmid', 'mod_iprbook'));
}

require_login($course, true, $cm);

$modulecontext = context_module::instance($cm->id);


$PAGE->set_url('/mod/iprbook/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);

$user_id = get_config('iprbooks', 'user_id');
$token = get_config('iprbooks', 'user_token');
$client = new Client($user_id, $token);

$integrationManager = new IntegrationManager($client);

$book = new Book($client);
$book->get($moduleinstance->iprbookid);
$autoLoginUrl = $integrationManager->generateAutoAuthUrl($USER->email, "", User::STUDENT, $book->getId());

$style = file_get_contents($CFG->dirroot . "/mod/iprbook/style/iprbook.css");

$template = "<style>" . $style . "</style>
			<div class=\"ipr-item\" data-id=\"" . $book->getId() . "\">
                <div class=\"row\" style='padding: 10px'>
                    <div id=\"ipr-item-image-" . $book->getId() . "\" class=\"col-sm-2 pub-image\">
                        <img src=\"" . $book->getImage() . "\" class=\"img-responsive thumbnail\" alt=\"\">
                        <a id=\"ipr-item-url-" . $book->getId() . "\" href=\"" . $autoLoginUrl . '&goto=' . $book->getId() . "\"></a>
                    </div>
                    <div class=\"col-sm-8\">
                        <div id=\"ipr-item-title-" . $book->getId() . "\"><strong>Название:</strong> " . $book->getTitle() . " </div>
                        <div id=\"ipr-item-title_additional-" . $book->getId() . "\" ><strong>Альтернативное
                            название:</strong> " . $book->getTitleAdditional() . " </div>
                        <div id=\"ipr-item-pubhouse-" . $book->getId() . "\"><strong>Издательство:</strong> " . $book->getPubhouse() . " </div>
                        <div id=\"ipr-item-authors-" . $book->getId() . "\"><strong>Авторы:</strong> " . $book->getAuthors() . " </div>
                        <div id=\"ipr-item-pubyear-" . $book->getId() . "\"><strong>Год издания:</strong> " . $book->getPubyear() . " </div>
                        <div id=\"ipr-item-description-" . $book->getId() . "\" ><strong>Описание:</strong> " . $book->getDescription() . " </div>
                        <div id=\"ipr-item-keywords-" . $book->getId() . "\" ><strong>Ключевые слова:</strong> " . $book->getKeywords() . " </div>
                        <div id=\"ipr-item-pubtype-" . $book->getId() . "\" ><strong>Тип издания:</strong> " . $book->getPubtype() . " </div>
                        <br>
                        <a id=\"ipr-item-detail-read\" class=\"btn btn-secondary\" target=\"_blank\" href=\"" . $autoLoginUrl . '&goto=' . $book->getId() . "\">Читать</a>
                    </div>
                </div>
            </div>";

echo $OUTPUT->header();

echo $template;

echo $OUTPUT->footer();
