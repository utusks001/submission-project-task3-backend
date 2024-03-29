<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TaskService;
use App\Http\Controllers\Controller;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class TaskController extends Controller
{
    private $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function getTasks()
    {
        $data = request(['title']);
        if (request(['is_done'])) $data['is_done'] = request()->boolean('is_done');

        try {
            $tasks = $this->taskService->getAllFilteredData($data);
            return response()->json([
                'status'    => 200,
                'data'      => $tasks,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'    => 500,
                'error'     => $e->getMessage(),
            ]);
        }
    }

    public function getTask($id)
    {
        try {
            $task = $this->taskService->getById($id);
            return response()->json([
                'status'    => 200,
                'data'      => $task,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status'    => 404,
                'error'     => $e->getMessage(),
            ]);
        }
    }

    public function createTask(Request $request)
	{
        $data = $request->only(["title", "description"]);

        try {
		    $task = $this->taskService->create($data);
		    return response()->json([
                'status'    => 201,
                'data'      => $task,
            ]);
        } catch (\InvalidArgumentException $e) {
            // Check if the exception is indeed an InvalidArgumentException
                    return response()->json([
                           'status' => 422,
                           'error' => $e->getMessage(),
                    ]);
        }
	}


    public function updateTask(Request $request)
    {
        $data = $request->only(["_id", "title", "description"]);

        try {
            $this->taskService->update($data);
            return response()->json([
                'status'    => 200,
                'data'      => 'Data updated successfully.',
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'status' => 422,
                'error' => $e->getMessage(),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status'    => 404,
                'error'     => $e->getMessage(),
            ]);
        }
    }

    public function deleteTask($id)
	{
        try {
            $this->taskService->delete($id);
            return response()->json([
                'status'    => 200,
                'message'   => 'Data deleted successfully.',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status'    => 404,
                'error'     => $e->getMessage(),
            ]);
        }
	}

    public function doTask($id)
    {
        try {
		    $this->taskService->updateStatus($id, true);
		    return response()->json([
                'status'    => 200,
                'data'      => 'Data updated successfully.',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status'    => 404,
                'error'     => $e->getMessage(),
            ]);
        }
	}

    public function undoTask($id)
    {
        try {
		    $this->taskService->updateStatus($id, false);
		    return response()->json([
                'status'    => 200,
                'data'      => 'Data updated successfully.',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status'    => 404,
                'error'     => $e->getMessage(),
            ]);
        }
	}
}
