<?php

namespace HDNET\hdadmin\Controller;

use Exception;

class imageController extends importController
{
    protected $deletedArticle = 0;

    public function importImages($_sThisImportCSV): array
    {
        if (($jImportObject = fopen($_sThisImportCSV, "r")) !== FALSE) {
            $picturePath = getShopBasePath() . "out/pictures/master/product/";
            $status = [];

            while ($jImportData = fgetcsv($jImportObject, 10000, chr(59), chr(0))) {
                $array[$jImportData[0]][1] = $jImportData[6];
                $array[$jImportData[0]][2] = $jImportData[7];
                $array[$jImportData[0]][3] = $jImportData[8];
                $array[$jImportData[0]][4] = $jImportData[9];
                $array[$jImportData[0]][5] = $jImportData[10];
                $array[$jImportData[0]][6] = $jImportData[11];
            }

            \array_shift($array);

            foreach($array as  $item) {
                foreach($item as $pic => $key) {
                    $_sThisTarPic = $key;
                    $_sThisDestPic = $picturePath . $pic . "/csr_" . basename($key);

                    if($_sThisTarPic === '') {
                        continue;
                    }

                    if (!file_exists($_sThisDestPic)) {
                        if (!copy($_sThisTarPic, $_sThisDestPic)) {
                            $status['error']++;
                        } else {
                            $status['success']++;
                        }
                    } else {
                        $status['exist']++;
                    }
                }
            }
        }
        return $status;
    }
}