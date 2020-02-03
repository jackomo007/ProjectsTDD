<?php

namespace Tests\Feature;

use App\Project;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectsTest extends TestCase
{
   use WithFaker, RefreshDatabase;

    /** @test */
    public function guests_cannot_manage_projects()
    {
        $project = factory('App\Project')->create();
    
        $this->get('/projects')->assertRedirect('login');

        $this->get('/projects/create')->assertRedirect('login');
        
        $this->get($project->path())->assertRedirect('login');

        $this->post('/projects', $project->toArray())->assertRedirect('login');
    } 

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

        $this->signIn();

        $this->get('/projects/create')->assertStatus(200);

        $projectAttributes = [
            'title'=> $this->faker->sentence,
            'description' => $this->faker->paragraph
        ];

        $this->post('/projects', $projectAttributes);
        // $response = $this->post('/projects', $projectAttributes);

        // $project = Project::where($projectAttributes)->first();

        // $response->assertRedirect($project->path());

        $this->assertDatabaseHas('projects', $projectAttributes);

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
    public function an_authenticated_user_cannot_view_the_project_of_others()
    {
        $this->be(factory('App\User')->create());

        $project = factory('App\Project')->create();

        $this->get($project->path())->assertStatus(403);

    }

    /** @test */
    public function a_project_requires_a_title()
    {
        $this->signIn();

        $projectAttributes = factory('App\Project')->raw(['title' => '']);

        $this->post('/projects', $projectAttributes)->assertSessionHasErrors('title');
    }
    
    /** @test */
    public function a_project_requires_a_description()
    {
        $this->signIn();

        $projectAttributes = factory('App\Project')->raw(['description' => '']);
        
        $this->post('/projects', $projectAttributes)->assertSessionHasErrors('description');
    }
}
