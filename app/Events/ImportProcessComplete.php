<?php

namespace App\Events;

use App\Models\ImportProcess;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ImportProcessComplete implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $importProcess;

    /**
     * Create a new event instance.
     *
     * @param  ImportProcess  $importProcess
     */
    public function __construct(ImportProcess $importProcess)
    {
        $this->importProcess = $importProcess;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('App.Models.ImportProcess.'.$this->importProcess->id);
    }
}
