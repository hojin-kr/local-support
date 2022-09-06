<?php
namespace Hojin\Url\DS;

use Exception;
use Google\Client;
use Google\Service\Sheets;
use Hojin\Url\Logger\Logger;

class Article
{
    const STATUS_DRAFT = 0;
    const STATUS_LIVE = 1;

    /**
     * Returns an authorized API client.
     * @return Client the authorized client object
     */
    public function getClient()
    {
        $client = new Client();
        $client->setApplicationName('TLDR');
        $client->setScopes(Sheets::SPREADSHEETS_READONLY);
        $client->setAuthConfig('localsupport-361715-c2b193ac653b.json');
        $client->setAccessType('offline');
        return $client;
    }


    public function get(string $page)
    {
        (new Logger)->info("get/article", ["page"=>$page]);
        $client = $this->getClient();
        $service = new Sheets($client);
        try{
            $spreadsheetId = '1BRBsOP7iFeTUFTzywqJY0JKYm8rBrREId03iV93WyDU';
            $range = "$page!A1:H1000";
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $values = $response->getValues();
            if (empty($values)) {
                throw new Exception("do not found data");
            }
            $articles = [];
            foreach ($values as $row) {
                if ($row[0] == static::STATUS_DRAFT) {
                    continue;
                }
                $articles['articles'][] = [
                    'id'=>$row[1] ?? "",
                    'title'=>$row[2] ?? "",
                    'content'=>$row[3] ?? "",
                    'url'=>$row[4] ?? "",
                    'shorten'=>$row[5] ?? "",
                    'contry_code'=>$row[6] ?? "",
                    'created'=>$row[7] ?? "",
                ];
            }
            return $articles;
        }
        catch(Exception $e) {
            (new Logger)->info("get/article/error", ["page"=>$page, "error"=>$e->getMessage()]);
        }
    }
}

