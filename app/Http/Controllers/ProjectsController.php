<?php

namespace App\Http\Controllers;

use App\Project;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateProjectRequest;

class ProjectsController extends Controller
{
    public function index()
    {
        $projects = auth()->user()->projects;

        return view('projects.index', compact('projects'));
    }

    public function show(Project $project)
    {
        $this->authorize('update', $project);

        return view('projects.show', compact('project'));
    }

     public function create()
    {
        return view('projects.create');
    }

    public function store()
    {
         $project = auth()->user()->projects()->create($this->validateRequest());

        if ($tasks = request('tasks')) {
            $project->addTasks($tasks);
        }

        if (request()->wantsJson()) {
            return ['message' => $project->path()];
        }

        return redirect($project->path());
    }

    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    public function update()
    {
        $this->authorize('update', $project);

        $project->update($this->validateRequest());

        return redirect($project->path());
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title'=>  'sometimes|required', 
            'description' => 'sometimes|required',
            'notes' => 'nullable'
        ]);
    }
}
