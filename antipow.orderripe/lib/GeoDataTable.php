<?php

namespace Antipow\OrderRipe;

use Bitrix\Main\Entity;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\TextField;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class GeoDataTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'orderripe_data';
    }

    public static function getMap()
    {
        return [
            new IntegerField('ID', [
                'autocomplete' => true,
                'primary'      => true,
                'title'        => Loc::getMessage('ANTIPOW_ORDERRIPE_ID'),
            ]),
            new IntegerField('ORDER_ID', [
                'required' => true,
                'title'    => Loc::getMessage('ANTIPOW_ORDERRIPE_ORDER_ID'),
            ]),
            new TextField('JSON_DATA', [
                'required' => true,
                'title'    => Loc::getMessage('ANTIPOW_ORDERRIPE_JSON_DATA'),
            ]),
        ];
    }

    /**
     * @param Entity\Event $event
     */
    public static function OnAdd(Entity\Event $event)
    {
        GeoDataTable::getEntity()->cleanCache();
    }

    /**
     * @param Entity\Event $event
     */
    public static function OnUpdate(Entity\Event $event)
    {
        GeoDataTable::getEntity()->cleanCache();
    }

    /**
     * @param Entity\Event $event
     */
    public static function OnDelete(Entity\Event $event)
    {
        GeoDataTable::getEntity()->cleanCache();
    }
}
