<?php

namespace dhope0000\LXDClient\Tools\CloudConfig;

use dhope0000\LXDClient\Model\CloudConfig\Data\Update as UpdateModel;

class Update
{
    public function __construct(UpdateModel $updateModel)
    {
        $this->updateModel = $updateModel;
    }

    public function update(int $cloudConfigId, string $code, array $imageDetails)
    {
        $imageDetails = json_encode($imageDetails);
        return $this->updateModel->insert($cloudConfigId, $code, $imageDetails);
    }
}
