<?php

namespace Tests\Feature;

use App\Project;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectTasksTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_cannot_add_tasks_to_projects()
    {
        $project= factory('App\Project')->create();

        $this->post($project->path().'/tasks')->assertRedirect('login');
    }

    /** @test */
    public function only_the_owner_of_a_project_may_add_tasks()
    {
        $this->signIn();

        $project= factory('App\Project')->create();

        $this->post($project->path().'/tasks', ['body' => 'Lorem ipsum'])
                ->assertStatus(403);

        $this->assertDatabaseMissing('tasks', ['body' => 'Lorem ipsum']);
    }

    /** @test */
    public function only_the_owner_of_a_project_may_update_a_task()
    {
        $this->signIn();

        $project= factory('App\Project')->create();

        $task= $project->addTask('task test');

        $this->patch($task->path(), ['body' => 'updated'])
                ->assertStatus(403);

        $this->assertDatabaseMissing('tasks', ['body' => 'updated']);
    }

    /** @test */
    public function a_project_can_have_tasks()
    {
        $this->signIn();

        $project = auth()->user()->projects()->create(
            factory(Project::class)->raw()
        );

        $this->post($project->path().'/tasks', ['body' => 'Lorem ipsum']);

        $this->get($project->path())
            ->assertSee('Lorem ipsum');
    }

    /** @test */
    public function a_task_can_be_updated()
    {
        $this->withoutExceptionHandling();

        $this->signIn();

        $project = auth()->user()->projects()->create(
            factory(Project::class)->raw()
        );

        $task = $project->addTask('A task');

        $this->patch($project->path().'/tasks/'.$task->id, [
            'body' => 'updated',
            'completed' => true
        ]);

        $this->assertDatabaseHas('tasks', [
            'body' => 'updated',
            'completed' => true
        ]);
    }

    /** @test */
    public function a_task_requires_a_body()
    {
        $this->signIn();
         
        $project = auth()->user()->projects()->create(
            factory(Project::class)->raw()
        );

        $projectAttributes = factory('App\Task')->raw(['body' => '']);
        
        $this->post($project->path().'/tasks', $projectAttributes)->assertSessionHasErrors('body');
    }
}
