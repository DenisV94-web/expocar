<?php

// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);

require_once ($_SERVER["DOCUMENT_ROOT"] . "/test/classes/DB.php");

try 
{
    $DB = new DB();
    $table = "feedback";
    $phone = str_replace(array("+", " ", "(", ")", "-"), "", $_REQUEST["phone"]);
    $conc = $DB->getList($table, array("phone" => $phone), array("id"));
    if (!count($conc)) {
        $info = $DB->insert($table, array(
            array(
                "fio" => $_REQUEST["name"],
                "phone" => $phone,
                "email" => $_REQUEST["email"]
            )
        ));

        $info = json_encode(array(
            "err_code" => 2,
            "message" => 'Неизвестная ошибка!'
        ));

        if ($info) {
            $info = json_encode(array(
                "err_code" => 1,
                "message" => 'Спасибо за Ваш отклик!'
            ));
        }
    } else {
        $info = json_encode(array(
            "err_code" => 2,
            "message" => 'Отклик с таким номером телефона уже существует!'
        ));
    }
}
catch (Exception $e) {
    $info = json_encode(array(
        "err_code" => 2,
        "message" => $e->getMessage(),
        "file" => $e->getFile(),
        "line" => $e->getLine(),
        "trace" => $e->getTrace()
    ));
}

echo $info;

?>