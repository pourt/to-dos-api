<?php

namespace App\Services;

use App\System\Request;
use App\System\Traits\Database;

class TaskService
{
    use Database;

    private $priorityLists = [
        "high" => 1,
        "moderate" => 2,
        "low" => 3
    ];

    private $taskStatus = [
        "active",
        "completed",
        "new"
    ];

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Create a new task
     *
     * @param Request $request
     * @return array
     */
    public function newTask(Request $request)
    {

        if (!in_array($request->input('priority'), array_keys($this->priorityLists))) {
            throw new \Exception("Please select a priority", 422);
        }

        if (!in_array($request->input('status'), $this->taskStatus)) {
            throw new \Exception("Please select a valid status", 422);
        }

        $request = $request->toArray();
        $request["priority"] = $this->priorityLists[$request['priority']];

        $task = $this->db->insert("tasks", $request);

        if (!$task) {
            throw new \Exception("Unable to create new task", 400);
        }

        return $this->getTaskById($this->db->lastId());
    }

    /**
     * Retrieve all tasks
     *
     * @param Request $request
     * @return array $task
     */
    public function getTask(Request $request)
    {
        $orderBy = [];
        if ($request->get('orderBy')) {
            $orderBy = [
                'order' => [
                    'field' => $request->get('orderBy'),
                    'dir' => $request->get('dir') ? $request->get('dir') : 'asc',
                ]
            ];
        }

        $task = $this->db->get("tasks", [], [], $orderBy);

        if (!$task) {
            throw new \Exception("Unable to retrieve task", 404);
        }

        return $task;
    }

    /**
     * Retrieve tasks statistics
     *
     * @param Request $request
     * @return array $task
     */
    public function getTaskStatistics(Request $request)
    {
        $orderBy = [];
        if ($request->get('orderBy')) {
            $orderBy = [
                'order' => [
                    'field' => $request->get('orderBy'),
                    'dir' => $request->get('dir') ? $request->get('dir') : 'asc',
                ]
            ];
        }

        $select = [
            "count(*) as total"
        ];

        $where = [];
        if ($request->get("status")) {
            $where = [
                "WHERE" => ["status", "=", $request->get("status")]
            ];
        }

        $task = $this->db->get("tasks", $where, $select, $orderBy);

        if (!$task) {
            throw new \Exception("Unable to retrieve task", 404);
        }

        return $task;
    }

    /**
     * Retrieve task by Id
     *
     * @param int $id
     * @return array
     */
    public function getTaskById($id)
    {
        if (!$id) {
            throw new \Exception("Id is invalid", 400);
        }

        $task = $this->db->show("tasks", [
            "WHERE" => ["id", "=", $id]
        ]);

        if (!$task) {
            throw new \Exception("Unable to retrieve task", 404);
        }

        return $task;
    }

    /**
     * Update task
     *
     * @param Request $request
     * @param int $id
     * @return array $task
     */
    public function updateTask(Request $request, $id)
    {
        if (!in_array($request->input('priority'), array_keys($this->priorityLists))) {
            throw new \Exception("Please select a priority", 422);
        }

        if (!in_array($request->input('status'), $this->taskStatus)) {
            throw new \Exception("Please select a valid status", 422);
        }

        if (!$id) {
            throw new \Exception("Id is invalid", 422);
        }

        $request = $request->toArray();
        $request["priority"] = $this->priorityLists[$request['priority']];

        $task = $this->db->update("tasks", $request, [
            "WHERE" => ["id", "=", $id]
        ]);

        if (!$task) {
            throw new \Exception("Unable to update task", 400);
        }

        return $this->getTaskById($id);
    }

    /**
     * Set a priority of a task
     *
     * @param Request $request
     * @param int $id
     * @return array
     */
    public function setPriority(Request $request, $id)
    {
        if (!$id) {
            throw new \Exception("Id is invalid", 422);
        }

        if (!in_array($request->input('priority'), array_keys($this->priorityLists))) {
            throw new \Exception("Please select a priority", 422);
        }

        $task = $this->db->update("tasks", $request->toArray(), [
            "WHERE" => ["id", "=", $id]
        ]);

        if (!$task) {
            throw new \Exception("Unable to update task", 400);
        }

        return $this->getTaskById($id);
    }

    /**
     * Set task as "complete"
     *
     * @param Request $request
     * @param int $id
     * @return array
     */
    public function setComplete(Request $request, $id)
    {
        if (!$id) {
            throw new \Exception("Id is invalid", 422);
        }

        if (!in_array($request->input('status'), $this->taskStatus)) {
            throw new \Exception("Please select a valid status", 422);
        }

        $task = $this->db->update("tasks", $request->toArray(), [
            "WHERE" => ["id", "=", $id]
        ]);

        if (!$task) {
            throw new \Exception("Unable to update task", 400);
        }

        return $this->getTaskById($id);
    }

    /**
     * Delete a task
     *
     * @param int $id
     * @return boolean
     */
    public function deleteTask($id)
    {
        if (!$id) {
            throw new \Exception("Id is invalid", 422);
        }

        $task = $this->db->delete("tasks", [
            "WHERE" => ["id", "=", $id]
        ]);

        if (!$task) {
            throw new \Exception("Unable to delete task", 400);
        }

        return true;
    }
}
