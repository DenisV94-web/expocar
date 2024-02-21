<?php

class Api  
{
    public  static function createEntity($factory, $fields)
    {
        $item = $factory->createItem();
        $item->setFromCompatibleData($fields);

        $context = new \Bitrix\Crm\Service\Context();
        $context->setUserId(1);

        $operation = $factory->getAddOperation($item, $context);
        return $operation->launch();
    }
}

?>