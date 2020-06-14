<?php

namespace dhope0000\LXDClient\Tools\Backups;

use dhope0000\LXDClient\Model\Backups\FetchBackups;
use dhope0000\LXDClient\Tools\Backups\GetHostInstanceStatusForBackupSet;

class GetBackupsOverview
{
    private $fetchBackups;

    public function __construct(
        FetchBackups $fetchBackups,
        GetHostInstanceStatusForBackupSet $getHostInstanceStatusForBackupSet
    ) {
        $this->fetchBackups = $fetchBackups;
        $this->getHostInstanceStatusForBackupSet = $getHostInstanceStatusForBackupSet;
    }

    public function get()
    {
        $allBackups = $this->fetchBackups->fetchAll();
        $properties = $this->getProperties($allBackups);

        $allBackups = $this->getHostInstanceStatusForBackupSet->get($allBackups);

        return [
            "sizeByMonthYear"=>$properties["sizeByMonthYear"],
            "filesByMonthYear"=>$properties["filesByMonthYear"],
            "allBackups"=>$allBackups
        ];
    }

    private function getProperties(array $backups)
    {
        $sizeByMonthYear = [];
        $filesByMonthYear = [];

        $dateTime = new \DateTime;
        $currentYear = $dateTime->format("Y");
        $currentMonth = $dateTime->format("m");

        foreach ($backups as $backup) {
            $date = new \DateTime($backup["storedDateCreated"]);
            $month = $date->format("m");
            $year = $date->format("Y");

            $monthLen = $year == $currentYear ? $currentMonth : 12;

            if (!isset($sizeByMonthYear[$year])) {
                $this->createYearArray($sizeByMonthYear, $year, $monthLen);
                $this->createYearArray($filesByMonthYear, $year, $monthLen);
            }

            $filesize = 0;
            // We should be doing something more agressive in this case!
            if (file_exists($backup["localPath"])) {
                $filesize = filesize($backup["localPath"]);
            }

            $sizeByMonthYear[$year][$month] += $filesize;
            $filesByMonthYear[$year][$month] ++;
        }

        return [
            "sizeByMonthYear"=>$sizeByMonthYear,
            "filesByMonthYear"=>$filesByMonthYear
        ];
    }

    private function createYearArray(&$array, $year, $monthLen)
    {
        for ($i = 0; $i <= $monthLen; $i++) {
            $k = $i < 10 ? "0$i" : $i;
            $array[$year][$k] = 0;
        }
    }
}
