<?php

namespace dhope0000\LXDClient\Tools\InstanceSettings\RecordActions\Convertors\Networks;

use dhope0000\LXDClient\Objects\RecordedAction;
use dhope0000\LXDClient\Interfaces\RecordedActionConvertor;
use dhope0000\LXDClient\Constants\RecordedActions\Categories;
use dhope0000\LXDClient\Constants\RecordedActions\Methods;

class CreateNetwork implements RecordedActionConvertor
{
    public function convert(array $recordedAction) :RecordedAction
    {
        $title = "Created network " . $recordedAction["params"]["name"];

        if (isset($recordedAction["params"]["hosts"]) && !empty($recordedAction["params"]["hosts"])) {
            $title .= " On " . count($recordedAction["params"]["hosts"]) . " hosts ";
        }

        return new RecordedAction(
            $title,
            new \DateTime($recordedAction["date"]),
            Categories::NETWORKS,
            Methods::CREATE
        );
    }
}
