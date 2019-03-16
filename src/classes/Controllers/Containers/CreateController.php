<?php

namespace dhope0000\LXDClient\Controllers\Containers;

use dhope0000\LXDClient\Tools\Containers\CreateContainer;

class CreateController
{
    public function __construct(CreateContainer $createContainer)
    {
        $this->createContainer = $createContainer;
    }

    public function create(
        $name,
        $profileIds = [],
        $hosts,
        array $imageDetails,
        string $instanceType = "",
        $server = ""
    ) {
        $lxdResponses = $this->createContainer->create(
            $name,
            $profileIds,
            $hosts,
            $imageDetails,
            $server,
            [],
            $instanceType
        );
        return ["success"=>"Created Container", "lxdResponses"=>$lxdResponses];
    }
}
