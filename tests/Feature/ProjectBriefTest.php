<?php

namespace Tests\Feature;

use App\Mail\ProjectBriefSubmitted;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ProjectBriefTest extends TestCase
{
    public function test_project_brief_form_can_be_viewed(): void
    {
        $this->get(route('project-brief.create'))
            ->assertOk()
            ->assertSee('Tell us what you want to build.');
    }

    public function test_project_brief_can_be_sent_with_images(): void
    {
        Mail::fake();

        $this->post(route('project-brief.store'), [
            'name' => 'Oliver Chimuka',
            'email' => 'oliver@example.com',
            'phone' => '+263 77 000 0000',
            'organization' => 'Kodomo Client',
            'service' => 'software-development',
            'budget' => '$1,000 - $3,000',
            'timeline' => 'Next month',
            'project_goal' => 'Build a customer portal for service requests.',
            'features' => 'Login, request tracking, admin dashboard.',
            'references' => 'https://example.com/reference',
            'images' => [
                UploadedFile::fake()->image('dashboard.png'),
            ],
        ])
            ->assertRedirect(route('project-brief.create'))
            ->assertSessionHas('status');

        Mail::assertSent(ProjectBriefSubmitted::class, function (ProjectBriefSubmitted $mail): bool {
            return $mail->hasTo(config('mail.project_brief_to'))
                && $mail->brief['email'] === 'oliver@example.com'
                && $mail->brief['project_goal'] === 'Build a customer portal for service requests.'
                && count($mail->images) === 1;
        });
    }

    public function test_project_brief_requires_core_details(): void
    {
        Mail::fake();

        $this->post(route('project-brief.store'), [])
            ->assertSessionHasErrors(['name', 'email', 'service', 'project_goal']);

        Mail::assertNothingSent();
    }
}
