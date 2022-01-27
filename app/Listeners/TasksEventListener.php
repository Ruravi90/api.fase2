<?php

namespace fase2\Listeners;

use fase2\Events\ActionTasks;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class TasksEventListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ActionTasks  $event
     * @return void
     */
    public function handle(ActionTasks $event)
    {
        //
    }
}
