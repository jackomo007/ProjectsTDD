<?php

namespace Tests\Feature;

use App\Project;
use Tests\TestCase;
use Facades\Tests\Setup\ProjectFactory;
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
       $this->signIn();

        $this->get('/projects/create')->assertStatus(200);

        $attributes = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->sentence,
            'notes' => 'Some notes here...',
        ];

        $response = $this->post('/projects', $attributes);

        $project = Project::where($attributes)->first();

        $response->assertRedirect($project->path());

        $this->get($project->path())
            ->assertSee($attributes['title'])
            ->assertSee($attributes['description'])
            ->assertSee($attributes['notes']);
    }

    /** @test */
    public function a_user_can_update_a_project()
    {
        $project = ProjectFactory::create();

        $this->actingAs($project->owner)
                ->patch($project->path(), $attributes = ['notes' => 'Changed'])
                ->assertRedirect($project->path());

        $this->assertDatabaseHas('projects', $attributes);
    }


    /** @test */
    public function a_user_can_view_their_project()
    {
        $project = ProjectFactory::create();
        
        $this->actingAs($project->owner)->get($project->path())
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
    public function an_authenticated_user_cannot_update_the_project_of_others()
    {
        $this->signIn();

        $project = factory('App\Project')->create();

        $this->patch($project->path())->assertStatus(403);

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
