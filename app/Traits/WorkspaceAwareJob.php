<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait WorkspaceAwareJob
{
    /**
     * @var int The hostname ID of the previously active tenant.
     */
    protected $workspace;

    use \Illuminate\Queue\SerializesModels {
        __sleep as serializedSleep;
        __wakeup as serializedWakeup;
    }

    public function __sleep()
    {
        $this->workspace = config('app.env');
        $attributes = $this->serializedSleep();

        return $attributes;
    }

    public function __wakeup()
    {
        if($this->workspace){
            config(['app.env' => $this->workspace]);
        }

        $this->serializedWakeup();
    }
}
