<?php

namespace dhope0000\LXDClient\Tools\Instances;

use dhope0000\LXDClient\Tools\Hosts\Instances\HostsHaveInstance;
use dhope0000\LXDClient\Tools\Hosts\Images\ImportImageIfNotHave;
use dhope0000\LXDClient\Model\Client\LxdClient;

class CreateInstance
{
    public function __construct(
        LxdClient $lxdClient,
        HostsHaveInstance $hostsHaveInstance,
        ImportImageIfNotHave $importImageIfNotHave
    ) {
        $this->client = $lxdClient;
        $this->hostsHaveInstance = $hostsHaveInstance;
        $this->importImageIfNotHave = $importImageIfNotHave;
    }
    /**
     * TODO Combine the two profiles array
     * TODO Find out the $server param and send it to space
     */
    public function create(
        string $type,
        string $name,
        array $profiles,
        array $hosts,
        array $imageDetails,
        $server = "",
        array $profileNames = [],
        string $instanceType = "",
        array $gpus = null,
        array $config = []
    ) {
        $this->hostsHaveInstance->ifHostInListHasContainerNameThrow($hosts, $name);

        $profiles = $this->createProfileNameArray($profiles, $profileNames);

        $options = $this->createOptionsArray(
            $type,
            $profiles,
            $imageDetails,
            $server,
            $instanceType,
            $gpus,
            $config
        );

        $results = [];

        foreach ($hosts as $host) {
            $client = $this->client->getANewClient($host);
            $this->importImageIfNotHave->importIfNot($client, $imageDetails);

            $response = $client->instances->create($name, $options, true);

            if ($response["status_code"] == 400) {
                throw new \Exception("Host: $host " . $response["err"], 1);
            }

            $results[] = $response;
        }

        return $results;
    }


    private function createOptionsArray(
        $type,
        $profiles,
        $imageDetails,
        $server = "",
        $instanceType = "",
        array $gpus = null,
        array $config = []
    ) {
        $x = [
            "type"=>$type,
            "fingerprint"=>$imageDetails["fingerprint"],
            "profiles"=>$profiles,
            "server"=>$server,
            "instance_type"=>$instanceType
        ];
        if (is_array($gpus) && !empty($gpus)) {
            $x["devices"] = [];
            foreach ($gpus as $index => $id) {
                $x["devices"]["gpu_$index"] = [
                    "type"=>"gpu",
                    "pci"=>$id
                ];
            }
        }


        if (!empty($config)) {
            $x["config"] = $config;
        }

        return $x;
    }

    private function createProfileNameArray($profiles, $additionalProfiles)
    {
        return array_merge($profiles, $additionalProfiles);
    }
}
