<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SaveTaskQuestionRequest;
use App\Http\Requests\SaveTaskRequest;
use App\Level;
use App\Task;

class TaskController extends BaseController
{

    public function show($id = null)
    {
        $task = new Task();
        $new = true;
        $levels = Level::all();

        if ($id !== null) {
            $task = Task::findOrFail($id);
            $new = false;
        }

        return $this->adminView('task', [
            'task' => $task,
            'new' => $new,
            'levels' => $levels
        ]);
    }

    public function save(SaveTaskRequest $request)
    {
        $task = Task::firstOrNew(['id' => $request->get('id')]);

        $task->fill($request->all());
        $task->save();

        return redirect('/admin/level/' . $task->level->id);
    }

    public function delete($id)
    {
        Task::destroy($id);

        return response()->json(true);
    }

    public function saveQuestion($id, SaveTaskQuestionRequest $request)
    {
        $task = Task::findOrFail($id);
        $task->question = $request->get('question');

        $task->save();

        return redirect()->back();
    }

}