<?php

namespace dhope0000\LXDClient\Model\Deployments;

use dhope0000\LXDClient\Model\Database\Database;

class FetchDeployments
{
    public function __construct(Database $database)
    {
        $this->database = $database->dbObject;
    }

    public function fetchAll()
    {
        $sql = "SELECT
                    `Deployment_ID` as `id`,
                    `Deployment_Name` as `name`
                FROM
                    `Deployments`
                ORDER BY
                    `Deployment_ID` DESC
                ";
        $do = $this->database->query($sql);
        return $do->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function fetch(int $deploymentId)
    {
        $sql = "SELECT
                    `Deployment_ID` as `id`,
                    `Deployment_Name` as `name`
                FROM
                    `Deployments`
                WHERE
                    `Deployment_ID` = :id
                ";
        $do = $this->database->prepare($sql);
        $do->execute([
            ":id"=>$deploymentId
        ]);
        return $do->fetch(\PDO::FETCH_ASSOC);
    }
}
