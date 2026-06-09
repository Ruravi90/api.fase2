<?php

namespace App\Http\Controllers;

use App\Events\ActionTasks;
use App\Models\Task;

class TaskController extends Controller
{
    public function send()
    {
        $task = new Task;
        $task->title = 'Nueva tarea';
        $task->description = 'Accion a realizar';
        $task->date = date('Y-m-d H:i:s');
        $task->is_completed = false;
        $task->user_id = 1;

        event(new ActionTasks($task));

        return response('Pusher tarea', 200);
    }
}
