<?php
namespace dhope0000\LXDClient\Controllers\Hosts\Instances;

use dhope0000\LXDClient\Tools\Instances\Devices\Proxy\DeleteProxyDevice;

class DeleteProxyDeviceController implements \dhope0000\LXDClient\Interfaces\RecordAction
{
    public function __construct(DeleteProxyDevice $deleteProxyDevice)
    {
        $this->deleteProxyDevice = $deleteProxyDevice;
    }

    public function delete(int $hostId, string $instance, string $device)
    {
        $this->deleteProxyDevice->delete($hostId, $instance, $device);
        return ["state"=>"success", "message"=>"Deleted proxy device"];
    }
}
