<?php

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\EventManager;
use Antipow\OrderRipe\GeoDataTable;

Loc::loadMessages(__FILE__);

class antipow_orderripe extends CModule
{
    public function __construct()
    {
        $arModuleVersion = [];

        include __DIR__ . '/version.php';

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_ID = 'antipow.orderripe';
        $this->MODULE_NAME = Loc::getMessage('ANTIPOW_ORDERRIPE_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('ANTIPOW_ORDERRIPE_MODULE_DESCRIPTION');
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = Loc::getMessage('ANTIPOW_ORDERRIPE_PARTNER_NAME');
        $this->PARTNER_URI = 'https://bitrix.dev';
    }

    public function doInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
        $this->installDB();
        $this->installEvents();
    }

    public function doUninstall()
    {
        $this->uninstallDB();
        $this->uninstallEvents();
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    public function installDB()
    {
        if (Loader::includeModule($this->MODULE_ID)) {
            GeoDataTable::getEntity()->createDbTable();
        }
    }

    public function uninstallDB()
    {
        if (Loader::includeModule($this->MODULE_ID)) {
            $connection = Application::getInstance()->getConnection();
            $connection->dropTable(GeoDataTable::getTableName());
        }
    }

    public function installEvents()
    {
        $eventManager = EventManager::getInstance();

        $eventManager->registerEventHandler("sale", "OnSaleOrderSaved", $this->MODULE_ID, "Antipow\\OrderRipe\\EventHandlers", "OnSaleOrderSavedHandler");
        $eventManager->registerEventHandler("main", "OnAdminSaleOrderView", $this->MODULE_ID, "Antipow\\OrderRipe\\AdminOrderTab", "onInit");
        $eventManager->registerEventHandler("main", "OnAdminSaleOrderEdit", $this->MODULE_ID, "Antipow\\OrderRipe\\AdminOrderTab", "onInit");

        return true;
    }

    public function uninstallEvents()
    {
        $eventManager = EventManager::getInstance();

        $eventManager->unRegisterEventHandler("sale", "OnSaleOrderSaved", $this->MODULE_ID, "Antipow\\OrderRipe\\EventHandlers", "OnSaleOrderSavedHandler");
        $eventManager->unRegisterEventHandler("main", "OnAdminSaleOrderView", $this->MODULE_ID, "Antipow\\OrderRipe\\AdminOrderTab", "onInit");
        $eventManager->unRegisterEventHandler("main", "OnAdminSaleOrderEdit", $this->MODULE_ID, "Antipow\\OrderRipe\\AdminOrderTab", "onInit");

        return true;
    }

}
