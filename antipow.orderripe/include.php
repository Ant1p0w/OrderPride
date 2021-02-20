<?php

use \Bitrix\Main\Service\GeoIp;
use Bitrix\Main\Web\HttpClient;

Bitrix\Main\Loader::registerAutoloadClasses(
    'antipow.orderripe',
    [
        'Antipow\OrderRipe\EventHandlers' => 'lib/EventHandlers.php',
        'Antipow\OrderRipe\AdminOrderTab' => 'lib/AdminOrderTab.php',
    ]
);

class OrderRipe
{
    const MODULE_ID = 'antipow.orderripe';
    const URL = 'https://rest.db.ripe.net/search.json?query-string=';

    protected $userIp;
    protected $orderId;
    protected $geoData;

    private function getIp(): string
    {
        return GeoIp\Manager::getRealIp();
    }

    public function saveIpToOrder($orderId): bool
    {
        $this->orderId = $orderId;
        $this->userIp = $this->getIp();
        if ($this->parseGeoData()) {
            return $this->saveGeoData() ? true : false;
        }
        return false;
    }

    private function parseGeoData(): bool
    {
        $httpClient = new HttpClient();
        $arJsonData = json_decode($httpClient->get(OrderRipe::URL . $this->userIp), true);
        if (!empty($arJsonData['objects']['object'])) {
            $this->geoData = serialize($arJsonData['objects']['object']);
            return true;
        }
        return false;
    }

    private function saveGeoData(): \Bitrix\Main\ORM\Data\AddResult
    {
        return \Antipow\OrderRipe\GeoDataTable::add(
            [
                'ORDER_ID'  => $this->orderId,
                'JSON_DATA' => $this->geoData,
            ]
        );
    }

    public function getGeoData()
    {
        return $this->geoData;
    }

    public static function getGeoDataByOrderId($orderId)
    {
        $result = '';
        if (!empty($orderId)) {
            $orderGeoList = \Antipow\OrderRipe\GeoDataTable::getList([
                'select' => ['JSON_DATA'],
                'order'  => ['ID' => 'DESC'],
                'filter' => ['=ORDER_ID' => $orderId],
                'limit'  => 1
            ]);
            while ($arData = $orderGeoList->fetch()) {
                $result = $arData;
            }
        }
        return unserialize($result['JSON_DATA']);
    }
}
