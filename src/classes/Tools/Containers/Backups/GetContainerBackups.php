<?php

namespace dhope0000\LXDClient\Tools\Containers\Backups;

use dhope0000\LXDClient\Model\Client\LxdClient;
use dhope0000\LXDClient\Model\Hosts\Backups\Containers\FetchContainerBackups;
use dhope0000\LXDClient\Tools\Hosts\HasExtension;
use dhope0000\LXDClient\Constants\LxdApiExtensions;

class GetContainerBackups
{
    private $lxdClient;
    private $fetchContainerBackups;
    private $hasExtension;

    public function __construct(
        LxdClient $lxdClient,
        FetchContainerBackups $fetchContainerBackups,
        HasExtension $hasExtension
    ) {
        $this->lxdClient = $lxdClient;
        $this->fetchContainerBackups = $fetchContainerBackups;
        $this->hasExtension = $hasExtension;
    }

    public function get(int $hostId, string $container)
    {
        $client = $this->lxdClient->getANewClient($hostId);

        if ($this->hasExtension->checkWithClient($client, LxdApiExtensions::CONTAINER_BACKUP) !== true) {
            throw new \Exception("Host doesn't support backups", 1);
        }

        $localBackups = $this->fetchContainerBackups->fetchAll($hostId, $container);

        $remoteBackups = $this->getRemoteBackups($client, $container, $localBackups);

        return [
            "localBackups"=>$localBackups,
            "remoteBackups"=>$remoteBackups
        ];
    }


    private function getRemoteBackups($client, string $container, array $localBackups)
    {
        $backups = $client->instances->backups->all($container);

        foreach ($backups as $index => $backupName) {
            // var_dump($backupName);
            $info = $client->instances->backups->info($container, $backupName);

            $info["storedLocally"] = $this->backupDownloadedLocally($localBackups, $backupName, $info["created_at"]);
            $backups[$index] = $info;
        }
        usort($backups, function ($a, $b) {
            $t1 = strtotime($a["created_at"]);
            $t2 = strtotime($b["created_at"]);
            return $t2 - $t1;
        });
        return $backups;
    }

    private function backupDownloadedLocally($localBackups, string $name, $created)
    {
        foreach ($localBackups as $backup) {
            if ($backup["backupName"] == $name && new \DateTime($created) == new \DateTime($backup["dateCreated"])) {
                return true;
            }
        }

        return false;
    }
}
