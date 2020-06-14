<?php
namespace dhope0000\LXDClient\Tools\Profiles;

use dhope0000\LXDClient\Model\Client\LxdClient;
use dhope0000\LXDClient\Model\Hosts\HostList;

class GetProfilesOnAllHosts
{
    public function __construct(HostList $hostList)
    {
        $this->hostList = $hostList;
    }

    public function getProfilesOnAllHosts()
    {
        $profiles = array();
        $hosts = $this->hostList->getOnlineHostsWithDetails();
        $numberOfHosts = count($hosts);
        $seenProfiles = [];

        foreach ($hosts as $host) {
            $hostProfiles = $host->profiles->all();
            foreach ($hostProfiles as $profile) {
                if (!isset($seenProfiles[$profile])) {
                    $seenProfiles[$profile] = 0;
                }
                $seenProfiles[$profile]++;
                if ($seenProfiles[$profile] == $numberOfHosts) {
                    $profiles[] = ["profile"=>$profile, "host"=>$host];
                }
            }
        }
        return $profiles;
    }
}
