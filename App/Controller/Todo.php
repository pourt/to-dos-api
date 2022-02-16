<?php

namespace App\Controller;

use App\Services\Response\{TaskCollection, TaskResource, TaskStatisticsCollection};
use App\Services\TaskService;
use App\System\{Controller, Request};
use App\System\Traits\Response;

class Todo extends Controller
{
    use Response;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Insert a new task into the tasks table
     * @param type $taskName
     * @param type $startDate
     * @param type $completedDate
     * @param type $completed
     * @param type $projectId
     * @return int id of the inserted task
     */
    public function create(Request $request)
    {
        try {
            $task = (new TaskService)->newTask($request);
        } catch (\Exception $e) {
            Response::error($e->getMessage(), $e->getCode());
        }

        Response::success(
            (new TaskResource($task))->map(),
            'Task succesfully created'
        );
    }

    public function get(Request $request)
    {
        try {
            $task = (new TaskService)->getTask($request);
        } catch (\Exception $e) {
            Response::error($e->getMessage(), $e->getCode());
        }

        Response::success(
            (new TaskCollection($task))->collect(),
            'Task succesfully retrieved'
        );
    }

    public function statistics(Request $request)
    {
        try {
            $task = (new TaskService)->getTaskStatistics($request);
        } catch (\Exception $e) {
            Response::error($e->getMessage(), $e->getCode());
        }

        Response::success(
            (new TaskStatisticsCollection($task))->collect(),
            'Task statistics succesfully retrieved'
        );
    }

    public function show(Request $request)
    {
        try {
            $task = (new TaskService)->getTaskById($request->get('id'));
        } catch (\Exception $e) {
            Response::error($e->getMessage(), $e->getCode());
        }
        Response::success(
            (new TaskResource($task))->map(),
            'Task succesfully retrieved'
        );
    }

    public function update(Request $request)
    {
        try {
            $task = (new TaskService)->updateTask($request, $request->input('id'));
        } catch (\Exception $e) {
            Response::error($e->getMessage(), $e->getCode());
        }

        Response::success($task, 'Task succesfully modified');
    }

    public function setPriority(Request $request)
    {
        try {
            $task = (new TaskService)->setPriority($request, $request->input('id'));
        } catch (\Exception $e) {
            Response::error($e->getMessage(), $e->getCode());
        }

        Response::success($task, 'Task succesfully modified');
    }

    public function destroy(Request $request)
    {
        try {
            $task = (new TaskService)->deleteTask($request->input('id'));
        } catch (\Exception $e) {
            Response::error($e->getMessage(), $e->getCode());
        }

        Response::success($task, 'Task succesfully removed');
    }
}
