<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectsTest extends TestCase
{
   use WithFaker, RefreshDatabase;

    /** @test */
    public function guests_cannot_create_projects()
    {
        $projectAttributes = factory('App\Project')->raw();
    
        $this->post('/projects', $projectAttributes)->assertRedirect('login');
    }

    /** @test */
    public function guests_cannot_view_projects()
    {    
        $this->get('/projects')->assertRedirect('login');
    }

    /** @test */
    public function guests_cannot_view_a_sigle_project()
    {
        $project = factory('App\Project')->create();
    
        $this->get($project->path())->assertRedirect('login');
    }

    /**
     * A user can create a project.
     *
     * @return void
     * @test
     */
    public function a_user_can_create_a_project()
    {
        $this->withoutExceptionHandling();

        $this->actingAs(factory('App\User')->create());

        $projectAttributes = [
            'title'=> $this->faker->sentence,
            'description' => $this->faker->paragraph
        ];

        $this->post('/projects',$projectAttributes)->assertRedirect('/projects');

        $this->assertDatabaseHas('projects',$projectAttributes);

        $this->get('/projects')->assertSee($projectAttributes['title']);
    }

    /** @test */
    public function a_user_can_view_their_project()
    {
        $this->be(factory('App\User')->create());
        $this->withoutExceptionHandling();

        $project = factory('App\Project')->create(['owner_id' => auth()->id()]);

        $this->get($project->path())
            ->assertSee($project->title)
            ->assertSee($project->description);
    }

    /** @test */
    public function a_project_requires_a_title()
    {
        $this->actingAs(factory('App\User')->create());

        $projectAttributes = factory('App\Project')->raw(['title' => '']);

        $this->post('/projects', $projectAttributes)->assertSessionHasErrors('title');
    }
    
    /** @test */
    public function a_project_requires_a_description()
    {
        $this->actingAs(factory('App\User')->create());

        $projectAttributes = factory('App\Project')->raw(['description' => '']);
        
        $this->post('/projects', $projectAttributes)->assertSessionHasErrors('description');
    }
}
