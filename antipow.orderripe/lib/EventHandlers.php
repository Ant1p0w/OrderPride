<?php

namespace Antipow\OrderRipe;

use Bitrix\Main\Event;
use OrderRipe;

class EventHandlers
{
    /**
     * @param Event $event
     */
    public static function OnSaleOrderSavedHandler(Event $event)
    {
        $order = $event->getParameter("ENTITY");
        $orderRipe = new OrderRipe();
        $orderRipe->saveIpToOrder($order->getId());
    }

    public static function OnEndBufferContentHandler(&$content)
    {
        $orderGeoList = \Antipow\OrderRipe\GeoDataTable::getList([
            'select' => ['ID', 'ORDER_ID', 'JSON_DATA'],
            'order'  => ['ID' => 'ASC'],
        ]);
    }
}
