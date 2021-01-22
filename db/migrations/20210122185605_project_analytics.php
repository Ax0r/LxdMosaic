<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ProjectAnalytics extends AbstractMigration
{
    public function change(): void
    {
        // Create a table store types of project analytics (you just know LXD
        // team will add more later)
        $table = $this->table('Project_Analytics_Types', ['id' => "PAT_ID", 'primary_key' => ["PAT_ID"]]);
        $table->addColumn('PAT_Name', 'string')
            ->addColumn('PAT_Key', 'string')
            ->create();

        $this->execute("INSERT INTO `Project_Analytics_Types`(`PAT_Name`, `PAT_Key`) VALUES
            ('Containers', 'limits.containers'),
            ('CPU', 'limits.cpu'),
            ('Disk', 'limits.disk'),
            ('Memory', 'limits.memory'),
            ('Networks', 'limits.networks'),
            ('Processes', 'limits.processes'),
            ('Virtual Machines', 'limits.virtual-machine')
        ");

        $table = $this->table('Project_Analytics', ['id' => "PA_ID", 'primary_key' => ["PA_ID"]]);
        $table->addColumn('PA_Date_Created', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('PA_Host_ID', 'integer')
            ->addColumn('PA_Project', 'string')
            ->addColumn('PA_Type_ID', 'integer')
            ->addColumn('PA_Value', 'biginteger', ['null'=>false])
            ->addColumn('PA_Limit', 'biginteger', ['null'=>true])
            ->addForeignKey('PA_Type_ID', 'Project_Analytics_Types', 'PAT_ID', ['delete'=> 'CASCADE', 'update'=> 'RESTRICT'])
            ->addForeignKey('PA_Host_ID', 'Hosts', 'Host_ID', ['delete'=> 'CASCADE', 'update'=> 'RESTRICT'])
            ->create();
    }
}
