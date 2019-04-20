<?php

namespace dhope0000\LXDClient\Controllers\Deployments;

use dhope0000\LXDClient\Tools\Deployments\Deploy;

class DeployController
{
    public function __construct(Deploy $deploy)
    {
        $this->deploy = $deploy;
    }

    public function deploy(int $deploymentId, array $instances)
    {
        return $this->deploy->deploy($deploymentId, $instances);
    }
}
