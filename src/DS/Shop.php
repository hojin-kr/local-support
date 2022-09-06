<?php
namespace Hojin\Url\DS;

use Exception;
use Google\Client;
use Google\Service\Sheets;
use Hojin\Url\Logger\Logger;

class Shop
{
    const STATUS_DRAFT = 0;
    const STATUS_LIVE = 1;
    const RNAGES = [1,3455,31174,32675,41210,52609,59107,67188,88191,91718,118590,154395,178867,187200,204173,212114,216426,221378,223332,231181,257597,261426,275937,284359,299303,318223,325101,333684,362377];
    const LABELS = "가평군,고양시,과천시,광명시,광주시,구리시,군포시,남양주시시,동두천시,부천시,수원시,안산시,안성시,안양시,양주시,양평군,여주시,연천군,오산시,용인시,의왕시,의정부시,이천시,파주시,평택시,포천시,하남시,화성시";

    /**
     * Returns an authorized API client.
     * @return Client the authorized client object
     */
    public function getClient()
    {
        $client = new Client();
        $client->setApplicationName('LocalCurrency');
        $client->setScopes(Sheets::SPREADSHEETS_READONLY);
        $client->setAuthConfig('localsupport-361715-c2b193ac653b.json');
        $client->setAccessType('offline');
        return $client;
    }


    public function get(string $sigungu, string $lat, string $lng)
    {
        (new Logger)->info("get/shop", ["position"=>$lat."/".$lng]);
        $client = $this->getClient();
        $service = new Sheets($client);
        try{
            $spreadsheetId = '';
            $labels = explode(",", Shop::LABELS);
            $rangeKey = array_search($sigungu, $labels);
            if (!isset(Shop::RNAGES[$rangeKey])) {
                $rangeKey = 4;
            }
            $start = Shop::RNAGES[$rangeKey] + 1;
            $end = Shop::RNAGES[$rangeKey + 1] - 1;
            $sheetRange = "places!A$start:L$end";
            $response = $service->spreadsheets_values->get($spreadsheetId, $sheetRange);
            $values = $response->getValues();
            if (empty($values)) {
                throw new Exception("do not found data");
            }
            $range['lat']['up'] = (float)$lat + 0.005;
            $range['lat']['down'] = (float)$lat - 0.005;
            $range['lng']['up'] = (float)$lng + 0.005;
            $range['lng']['down'] = (float)$lng - 0.005;
            // (new Logger)->info("test", $range);
            $shop = [];
            foreach ($values as $row) {
                if (!isset($row[10]) && !isset($row[11])) {
                    continue;
                }
                if ($range['lat']['up'] < $row[10] || $range['lat']['down'] > $row[10]) {
                    continue;
                }
                if ($range['lng']['up'] < $row[11] || $range['lng']['down'] > $row[11]) {
                    continue;
                }
                $shop[] = $row;
            }
            return $shop;
        }
        catch(Exception $e) {
            (new Logger)->info("get/shop/error", ["position"=>$lat."/".$lng, "error"=>$e->getMessage()]);
        }
    }

}

