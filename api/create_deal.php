<?php
/*
    Данный PHP скрипт написан для установки как агент в Bitrix24
    Этот скрипт необходимо подключить в файле local/php_interface/init.php
*/

function createDeal() {
    use Bitrix\Crm\Service;
    use Bitrix\Crm\Item;

    require_once ($_SERVER["DOCUMENT_ROOT"] . "/test/classes/DB.php");
    require_once ($_SERVER["DOCUMENT_ROOT"] . "/test/classes/Api.php");
    $DB = new DB();

    $factoryContact = Service\Container::getInstance()->getFactory(\CCrmOwnerType::Contact);
    $factoryDeal = Service\Container::getInstance()->getFactory(\CCrmOwnerType::Deal);

    $list = $DB->getList("feedback", array("not_processed" => 0));

    if (count($list)) {
        foreach ($list as $row) {
            $fio = explode(" ", $row["fio"]);
            $fields = array(
                "LAST_NAME" => $fio[0],
                "NAME" => $fio[1],
                "SECOND_NAME" => $fio[2],
                "PHONE" => array( "VALUE" => $row["phone"], "VALUE_TYPE" => "WORK" ),
                "EMAIL" => array( "VALUE" => $row["email"], "VALUE_TYPE" => "WORK" )
            );

            $respContact = Api::createEntity($factoryContact, $fields);

            if (isset($respContact["ID"])) {
                $fields = array(
                    "CONTACT_ID	" => $respContact["ID"],
                    "TITLE" => "Отклик от {$row["fio"]}"
                );

                $respDeal = Api::createEntity($factoryDeal, $fields);

                if (isset($respDeal["ID"])) {
                    $insert = $DB->update("feedback", array("id" => $row["id"]) array("not_processed" => 1));
                }
            }
        }
    }
}

?>