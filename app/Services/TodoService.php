<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\TodoRepositoryInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class TodoService
{
    public function __construct(
        protected TodoRepositoryInterface $todoRepository
    ) {
    }

    public function getTodos(User $user)
    {
        return $this->todoRepository->getTodos($user);
    }

    public function getTodo(User $user, int $id)
    {
        return $this->todoRepository->getTodo($user, $id);
    }

    public function createTodo(User $user, array $data)
    {
        $validator = Validator::make($data, [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'The validation is failed',
                'errors' => $validator->errors()->all(),
            ];
        }

        return $this->todoRepository->createTodo($user, [
            'title' => $data['title'],
            'description' => $data['description'],
            'completed' => false,
        ]);
    }

    public function updateTodo(User $user, array $data)
    {
        $validator = Validator::make($data, [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'The validation is failed',
                'errors' => $validator->errors()->all(),
            ];
        }

        $todo = $this->todoRepository->getTodo($user, $data['id']);
        $this->todoRepository->updateTodo($todo, [
            'title' => $data['title'],
            'description' => $data['description']
        ]);

        return $todo;
    }

    public function deleteTodo(User $user, int $id)
    {
        $todo = $this->todoRepository->getTodo($user, $id);
        return $this->todoRepository->deleteTodo($todo);
    }

    public function markAsCompletedOrNot(User $user, int $id)
    {
        $todo = $this->todoRepository->getTodo($user, $id);
        $this->todoRepository->markAsCompletedOrNot($todo);

        return $todo;
    }
}