<?php
namespace dhope0000\LXDClient\Controllers\Containers;

use dhope0000\LXDClient\Tools\Containers\GetContainerSettings;

class GetCurrentContainerSettingsController
{
    public function __construct(GetContainerSettings $getContainerSettings)
    {
        $this->getContainerSettings = $getContainerSettings;
    }

    public function get(int $hostId, string $container)
    {
        return $this->getContainerSettings->get($hostId, $container);
    }
}
