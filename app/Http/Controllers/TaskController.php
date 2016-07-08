<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Domain\Tasks\FinishType;
use App\FinishedTask;
use App\Http\Controllers\Admin\RequestsController;
use App\Task;
use App\TaskRequest;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Pusher;

class TaskController extends BaseController
{

    private $pusher;

    public function __construct()
    {
        parent::__construct();

        $this->pusher = new Pusher(env('PUSHER_KEY'), env('PUSHER_SECRET'), env('PUSHER_APP_ID'), [
            'cluster' => 'eu',
            'encrypted' => true
        ]);
    }

    public function show($id)
    {
        $task = Task::findOrFail($id);

        return view('task', [
            'task' => $task
        ]);
    }

    public function finish($id, Request $request)
    {
        $task = Task::findOrFail($id);
        $user = $request->user();

        switch ($task->finish_type) {
            case FinishType::SELF:
                return $this->self($task, $user);
            case FinishType::MULTIPLE_CHOICE:
                return $this->multipleChoice($task, $user, $request);
            case FinishType::TEACHER:
                return $this->teacher($task, $user);
        }

        throw new Exception('Unknown finish type: ' . $task->finish_type);
    }

    private function self(Task $task, User $user)
    {
        $this->finishTask($task, $user);

        return redirect()->back();
    }

    private function multipleChoice(Task $task, User $user, Request $request)
    {
        $this->validate($request, [
            'answer_id' => 'required|numeric|exists:answers,id'
        ]);

        $answerId = $request->get('answer_id');
        $answer = Answer::findOrFail($answerId);

        if (!$answer->correct) {
            return redirect()->back()->withErrors([
                'wrong_answer' => 'Diese Antwort war leider falsch, denk noch einmal genau nach!'
            ]);
        }

        $this->finishTask($task, $user);

        return redirect()->back();
    }

    private function teacher(Task $task, User $user)
    {
        TaskRequest::create([
            'user_id' => $user->id,
            'task_id' => $task->id
        ]);

        $payload = RequestsController::requestsPayload();

        $this->pusher->trigger('admin', 'requests', $payload);

        return redirect()->back();
    }

    private function finishTask(Task $task, User $user)
    {
        FinishedTask::create([
            'user_id' => $user->id,
            'task_id' => $task->id
        ]);
    }

}
