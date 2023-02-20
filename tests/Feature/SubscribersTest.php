<?php

namespace Tests\Feature;

use Corals\Modules\Newsletter\Facades\Newsletter;
use Corals\Modules\Newsletter\Models\Subscriber;
use Corals\User\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class SubscribersTest extends TestCase
{
    use DatabaseTransactions;

    protected $subscriber;
    protected $mailLists = [];

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $user = User::query()->whereHas('roles', function ($query) {
            $query->where('name', 'superuser');
        })->first();
        Auth::loginUsingId($user->id);
    }

    public function test_subscribers_store()
    {
        foreach (Newsletter::getAllMailLists() as $id => $mailList) {
            $this->mailLists[] = $id;
        }

        $email = 'subscribers@gmail.com';
        $response = $this->post('newsletter/subscribers', [
            'email' => $email,
            'name' => 'subscribers',
            'status' => 'active',
            'mail_lists' => $this->mailLists
        ]);

        $this->subscriber = Subscriber::query()->where('email', $email)->first();

        $response->assertDontSee('The given data was invalid')
            ->assertRedirect('newsletter/subscribers');

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => $this->subscriber->email,
            'name' => $this->subscriber->name,
        ]);
    }

    public function test_subscribers_show()
    {
        $this->test_subscribers_store();
        if ($this->subscriber) {
            $response = $this->get('newsletter/subscribers/' . $this->subscriber->hashed_id);

            $response->assertViewIs('Newsletter::subscribers.show')->assertStatus(200);
        }
        $this->assertTrue(true);
    }

    public function test_subscribers_edit()
    {
        $this->test_subscribers_store();
        if ($this->subscriber) {
            $response = $this->get('newsletter/subscribers/' . $this->subscriber->hashed_id . '/edit');

            $response->assertViewIs('Newsletter::subscribers.create_edit')->assertStatus(200);
        }
        $this->assertTrue(true);
    }

    public function test_subscribers_update()
    {
        $this->test_subscribers_store();

        if ($this->subscriber) {
            $response = $this->put('newsletter/subscribers/' . $this->subscriber->hashed_id, [
                'email' => $this->subscriber->email,
                'name' => $this->subscriber->name,
                'status' => 'inactive',
                'mail_lists' => $this->mailLists
            ]);

            $response->assertRedirect('newsletter/subscribers');
            $this->assertDatabaseHas('newsletter_subscribers', [
                'email' => $this->subscriber->email,
                'name' => $this->subscriber->name,
                'status' => 'inactive'

            ]);
        }

        $this->assertTrue(true);
    }

    public function test_subscribers_delete()
    {
        $this->test_subscribers_store();

        if ($this->subscriber) {
            $response = $this->delete('newsletter/subscribers/' . $this->subscriber->hashed_id);

            $response->assertStatus(200)->assertSeeText('Subscriber has been deleted successfully.');

            $this->isSoftDeletableModel(Subscriber::class);
            $this->assertDatabaseMissing('newsletter_subscribers', [
                'email' => $this->subscriber->email,
                'name' => $this->subscriber->name,
                'status' => $this->subscriber->status
            ]);
        }
        $this->assertTrue(true);
    }
}
