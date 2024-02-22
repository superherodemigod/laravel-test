<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use App\Models\Submission;
use App\Events\SubmissionSaved;
use App\Jobs\ProcessSubmission;

class SubmissionControllerTest extends TestCase
{
    // use RefreshDatabase;

    public function test_submit_endpoint()
    {
        Queue::fake();

        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'message' => 'This is a test message.'
        ];

        $response = $this->json('POST', '/api/submit', $data);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Submission received.']);

        Queue::assertPushed(ProcessSubmission::class, function ($job) use ($data) {
            return $job->data === $data;
        });

        $this->assertDatabaseHas('submissions', $data);
    }

    public function test_submit_endpoint_missing_fields()
    {
        $response = $this->json('POST', '/api/submit', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'message']);
    }
}