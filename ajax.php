<?php

use Iprbooks\Ebs\Sdk\Client;
use Iprbooks\Ebs\Sdk\collections\BooksCollection;
use Iprbooks\Ebs\Sdk\Managers\IntegrationManager;
use Iprbooks\Ebs\Sdk\Models\Book;
use Iprbooks\Ebs\Sdk\Models\User;

define('AJAX_SCRIPT', true);
require_once('../../config.php');
require_once($CFG->dirroot . '/mod/iprbook/vendor/autoload.php');

require_login();

$page = optional_param('page', 0, PARAM_INT);
$title = optional_param('title', "", PARAM_TEXT);
$id = optional_param('iprbookid', 0, PARAM_TEXT);


$clientId = get_config('iprbooks', 'user_id');
$token = get_config('iprbooks', 'user_token');

//$clientId = 187;
//$token = '5G[Usd=6]~F!b+L<a4I)Ya9S}Pb{McGX';

$content = "";
$details = "";
try {
    $client = new Client($clientId, $token);
} catch (Exception $e) {
    die();
}

$integrationManager = new IntegrationManager($client);
$autoLoginUrl = $integrationManager->generateAutoAuthUrl($USER->email, "", User::STUDENT);


if ($id > 0) {
    $book = new Book($client);
    $book->get($id);
    $details .= getDetails($book, $autoLoginUrl);
}

$booksCollection = new BooksCollection($client);
$booksCollection->setFilter(BooksCollection::TITLE, $title);
$booksCollection->setOffset($booksCollection->getLimit() * $page);
$booksCollection->get();

foreach ($booksCollection as $book) {
    $content .= getTemplate($book, $autoLoginUrl);
}

$content .= pagination($booksCollection->getTotalCount(), $page + 1);

echo json_encode(['page' => $page, 'html' => $content, 'details' => $details]);

function getTemplate(Book $book, $autoLoginUrl)
{
    return "<div class=\"ipr-item\" data-id=\"" . $book->getId() . "\">
                    <div class=\"row\" style='padding: 10px'>
                        <div id=\"ipr-item-image-" . $book->getId() . "\" class=\"col-sm-3 pub-image\">
                            <img src=\"" . $book->getImage() . "\" class=\"img-responsive thumbnail\" alt=\"\">
                            <a id=\"ipr-item-url-" . $book->getId() . "\" href=\"" . $autoLoginUrl . '&goto=' . $book->getId() . "\"></a>
                        </div>
                        <div class=\"col-sm-8\">
                            <div id=\"ipr-item-title-" . $book->getId() . "\"><strong>Название:</strong> " . $book->getTitle() . " </div>
                            <div id=\"ipr-item-title_additional-" . $book->getId() . "\" hidden><strong>Альтернативное
                                название:</strong> " . $book->getTitleAdditional() . " </div>
                            <div id=\"ipr-item-pubhouse-" . $book->getId() . "\"><strong>Издательство:</strong> " . $book->getPubhouse() . " </div>
                            <div id=\"ipr-item-authors-" . $book->getId() . "\"><strong>Авторы:</strong> " . $book->getAuthors() . " </div>
                            <div id=\"ipr-item-pubyear-" . $book->getId() . "\"><strong>Год издания:</strong> " . $book->getPubyear() . " </div>
                            <div id=\"ipr-item-description-" . $book->getId() . "\" hidden><strong>Описание:</strong> " . $book->getDescription() . " </div>
                            <div id=\"ipr-item-keywords-" . $book->getId() . "\" hidden><strong>Ключевые слова:</strong> " . $book->getKeywords() . " </div>
                            <div id=\"ipr-item-pubtype-" . $book->getId() . "\" hidden><strong>Тип издания:</strong> " . $book->getPubtype() . " </div>
                            <br>
                            <a  class=\"btn btn-secondary iprbook-select\" data-id=\"" . $book->getId() . "\">Выбрать</a>
                        </div>
                    </div>
                </div>";
}

function getDetails(Book $book, $autoLoginUrl)
{
    return "<div class=\"row\">
                <div id=\"ipr-item-detail-image\" class=\"col-sm-5 pub-image\">
                            <img src=\"" . $book->getImage() . "\" class=\"img-responsive thumbnail\" alt=\"\">
                            <a id=\"ipr-item-url-" . $book->getId() . "\" href=\"" . $autoLoginUrl . '&goto=' . $book->getId() . "\"></a>
                        </div>
                <div class=\"col-sm-7\">
                    <br>
                    <div id=\"ipr-item-detail-title\"><strong>Название:</strong> " . $book->getTitle() . " </div>
                    <div id=\"ipr-item-detail-title_additional\"></div>
                    <div id=\"ipr-item-detail-pubhouse\"><strong>Издательство:</strong> " . $book->getPubhouse() . " </div>
                    <div id=\"ipr-item-detail-authors\"><strong>Авторы:</strong> " . $book->getAuthors() . " </div>
                    <div id=\"ipr-item-detail-pubtype\"><strong>Тип издания:</strong> " . $book->getPubtype() . " </div>
                    <div id=\"ipr-item-detail-pubyear\"><strong>Год издания:</strong> " . $book->getPubyear() . " </div>
                    <br>
                    <a id=\"ipr-item-detail-read\" style=\"display: none\" class=\"btn btn-secondary\" target=\"_blank\">Читать</a>
                </div>
            </div>
            <br>
            <div id=\"ipr-details-fields\">
                <div id=\"ipr-item-detail-description\"><strong>Описание:</strong> " . $book->getDescription() . " </div>
                <br>
                <div id=\"ipr-item-detail-keywords\"><strong>Ключевые слова:</strong> " . $book->getKeywords() . " </div>
            </div>";
}

function pagination($count, $page)
{
    $output = '';
    $output .= "<nav aria-label=\"Страница\" class=\"pagination pagination-centered justify-content-center\"><ul class=\"mt-1 pagination \">";
    $pages = ceil($count / 10);


    if ($pages > 1) {

        if ($page > 1) {
            $output .= "<li class=\"page-item\"><a data-page=\"" . ($page - 2) . "\" class=\"page-link ipr-page\" ><span>«</span></a></li>";
        }
        if (($page - 3) > 0) {
            $output .= "<li class=\"page-item \"><a data-page=\"0\" class=\"page-link ipr-page\">1</a></li>";
        }
        if (($page - 3) > 1) {
            $output .= "<li class=\"page-item disabled\"><span class=\"page-link ipr-page\">...</span></li>";
        }


        for ($i = ($page - 2); $i <= ($page + 2); $i++) {
            if ($i < 1) continue;
            if ($i > $pages) break;
            if ($page == $i)
                $output .= "<li class=\"page-item active\"><a data-page=\"" . ($i - 1) . "\" class=\"page-link ipr-page\" >" . $i . "</a ></li > ";
            else
                $output .= "<li class=\"page-item \"><a data-page=\"" . ($i - 1) . "\" class=\"page-link ipr-page\">" . $i . "</a></li>";
        }


        if (($pages - ($page + 2)) > 1) {
            $output .= "<li class=\"page-item disabled\"><span class=\"page-link ipr-page\">...</span></li>";
        }
        if (($pages - ($page + 2)) > 0) {
            if ($page == $pages)
                $output .= "<li class=\"page-item active\"><a data-page=\"" . ($pages - 1) . "\" class=\"page-link ipr-page\" >" . $pages . "</a ></li > ";
            else
                $output .= "<li class=\"page-item \"><a data-page=\"" . ($pages - 1) . "\" class=\"page-link ipr-page\">" . $pages . "</a></li>";
        }
        if ($page < $pages) {
            $output .= "<li class=\"page-item\"><a data-page=\"" . $page . "\" class=\"page-link ipr-page\"><span>»</span></a></li>";
        }

    }

    $output .= "</ul></nav>";
    return $output;
}

die();