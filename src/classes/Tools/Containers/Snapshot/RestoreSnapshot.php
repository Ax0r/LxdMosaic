<?php
namespace dhope0000\LXDClient\Tools\Containers\Snapshot;

use dhope0000\LXDClient\Model\Client\LxdClient;

class RestoreSnapshot
{
    public function __construct(LxdClient $lxdClient)
    {
        $this->lxdClient = $lxdClient;
    }

    public function restoreSnapshot(int $hostId, string $container, string $snapshotName)
    {
        $client = $this->lxdClient->getANewClient($hostId);
        return $client->containers->snapshots->restore($container, $snapshotName, true);
    }
}
