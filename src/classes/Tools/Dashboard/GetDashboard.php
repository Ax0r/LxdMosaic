<?php

namespace dhope0000\LXDClient\Tools\Dashboard;

use dhope0000\LXDClient\Model\Users\Projects\FetchUserProject;
use dhope0000\LXDClient\Model\Users\Dashboard\FetchUserDashboards;
use dhope0000\LXDClient\Tools\Universe;
use dhope0000\LXDClient\Tools\Hosts\GetResources;
use dhope0000\LXDClient\Tools\User\GetUserProject;

class GetDashboard
{
    public function __construct(
        FetchUserProject $fetchUserProject,
        FetchUserDashboards $fetchUserDashboards,
        Universe $universe,
        GetResources $getResources,
        GetUserProject $getUserProject
    ) {
        $this->fetchUserProject = $fetchUserProject;
        $this->fetchUserDashboards = $fetchUserDashboards;
        $this->universe = $universe;
        $this->getResources = $getResources;
        $this->getUserProject = $getUserProject;
    }

    public function get($userId)
    {
        $clustersAndHosts = $this->universe->getEntitiesUserHasAccesTo($userId, "projects");
        $clustersAndHosts = $this->addCurrentProjects($userId, $clustersAndHosts);
        $stats = $this->getStatsFromClustersAndHosts($clustersAndHosts);
        $analyticsData = ["warning"=>"Not Enough Data, 10 minutes is minimum time"];
        $dashboards = $this->fetchUserDashboards->fetchAll($userId);

        return [
            "userDashboards"=>$dashboards,
            "clustersAndHosts"=>$clustersAndHosts,
            "stats"=>$stats,
            "analyticsData"=>$analyticsData
        ];
    }

    private function addCurrentProjects($userId, $clustersAndHosts)
    {
        foreach ($clustersAndHosts["clusters"] as $index => $cluster) {
            foreach ($cluster["members"] as $member) {
                $project = $this->getUserProject->getForHost($userId, $member);
                $member->setCustomProp("currentProject", $project);
                $member->setCustomProp("resources", $this->getResources($member));
            }
        }
        foreach ($clustersAndHosts["standalone"]["members"] as $index => $member) {
            $project = $this->getUserProject->getForHost($userId, $member);
            $member->setCustomProp("currentProject", $project);
            $member->setCustomProp("resources", $this->getResources($member));
        }
        return $clustersAndHosts;
    }

    private function getResources($member)
    {
        if ($member->hostOnline() == false) {
            return [];
        }
        $r = $this->getResources->getHostExtended($member);
        unset($r["projects"]);
        return $r;
    }

    private function getStatsFromClustersAndHosts(array $clustersAndHosts)
    {
        $memory = [
            "total"=>0,
            "used"=>0
        ];

        foreach ($clustersAndHosts["clusters"] as $cluster) {
            foreach ($cluster["members"] as $host) {
                if (!$host->hostOnline()) {
                    continue;
                }
                $memory["total"] += $host->getCustomProp("resources")["memory"]["total"];
                $memory["used"] += $host->getCustomProp("resources")["memory"]["used"];
            }
        }

        foreach ($clustersAndHosts["standalone"]["members"] as $host) {
            if (!$host->hostOnline()) {
                continue;
            }
            $memory["total"] += $host->getCustomProp("resources")["memory"]["total"];
            $memory["used"] += $host->getCustomProp("resources")["memory"]["used"];
        }

        return [
            "memory"=>$memory
        ];
    }
}
