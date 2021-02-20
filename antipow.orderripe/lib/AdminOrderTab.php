<?php

namespace Antipow\OrderRipe;

use OrderRipe;

class AdminOrderTab
{
    public static function onInit()
    {
        return [
            "TABSET"  => "AdminOrderTab",
            "GetTabs" => ["Antipow\\OrderRipe\\AdminOrderTab", "getTabs"],
            "ShowTab" => ["Antipow\\OrderRipe\\AdminOrderTab", "showTab"],
        ];
    }

    public static function getTabs($arArgs): array
    {
        return [
            [
                "DIV"   => "order-ripe",
                "TAB"   => "GeoIP",
                "ICON"  => "sale",
                "TITLE" => "Информацию об ip-адресе покупателя",
                "SORT"  => 500
            ]
        ];
    }

    public static function showTab($divName, $arArgs): void
    {
        if ($divName == "order-ripe") {
            $arGeoData = OrderRipe::getGeoDataByOrderId($arArgs['ID']);
            foreach ($arGeoData as $data) {
                ?>
                <table border="1" cellpadding="10" style="margin-bottom: 30px; width: 100%;">
                    <? foreach ($data as $key => $item) {
                        ?>
                        <tr>
                            <td style="width: 100px"><?= $key ?></td>
                            <td><?= AdminOrderTab::parseGeoDataItem($item) ?></td>
                        </tr>
                        <?
                    }
                    ?>
                </table>

                <?
            }
        }
    }

    public static function parseGeoDataItem($item): string
    {
        if (is_array($item)) {
            $str = '<ul>';
            if (is_array($item['attribute'])) {
                foreach ($item['attribute'] as $attribute) {
                    $str .= '<li>' . $attribute['name'] . ': ' . $attribute['value'] . '</li>';
                }
            } else {
                foreach ($item as $key => $value) {
                    $str .= '<li>' . $key . ': ' . $value . '</li>';
                }
            }
            $str .= '<ul>';
            return $str;
        } else {
            return $item;
        }
    }
}
