<?php

namespace Tests\Feature;

use Corals\Modules\Newsletter\Models\MailList;
use Corals\User\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class MailListsTest extends TestCase
{
    use DatabaseTransactions;

    protected $mailList;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $user = User::query()->whereHas('roles', function ($query) {
            $query->where('name', 'superuser');
        })->first();
        Auth::loginUsingId($user->id);
    }

    public function test_mail_lists_store()
    {
        $name = 'MailLists';
        $response = $this->post('newsletter/mail-lists', [
            'name' => $name,
            'status' => 'active',
        ]);

        $this->mailList = MailList::query()->where('name', $name)->first();

        $response->assertDontSee('The given data was invalid')
            ->assertRedirect('newsletter/mail-lists');

        $this->assertDatabaseHas('newsletter_mail_lists', [
            'name' => $this->mailList->name,
        ]);
    }

    public function test_mail_lists_show()
    {
        $this->test_mail_lists_store();
        if ($this->mailList) {
            $response = $this->get('newsletter/mail-lists/' . $this->mailList->hashed_id);

            $response->assertViewIs('Newsletter::mail_lists.show')->assertStatus(200);
        }
        $this->assertTrue(true);
    }

    public function test_mail_lists_edit()
    {
        $this->test_mail_lists_store();
        if ($this->mailList) {
            $response = $this->get('newsletter/mail-lists/' . $this->mailList->hashed_id . '/edit');

            $response->assertViewIs('Newsletter::mail_lists.create_edit')->assertStatus(200);
        }
        $this->assertTrue(true);
    }

    public function test_mail_lists_update()
    {
        $this->test_mail_lists_store();

        if ($this->mailList) {
            $response = $this->put('newsletter/mail-lists/' . $this->mailList->hashed_id, [
                'name' => $this->mailList->name,
                'status' => 'inactive',
            ]);

            $response->assertRedirect('newsletter/mail-lists');
            $this->assertDatabaseHas('newsletter_mail_lists', [
                'name' => $this->mailList->name,
                'status' => 'inactive',
            ]);
        }

        $this->assertTrue(true);
    }

    public function test_mail_lists_delete()
    {
        $this->test_mail_lists_store();

        if ($this->mailList) {
            $response = $this->delete('newsletter/mail-lists/' . $this->mailList->hashed_id);

            $response->assertStatus(200)->assertSeeText('Mail List has been deleted successfully.');

            $this->isSoftDeletableModel(MailList::class);
            $this->assertDatabaseMissing('newsletter_mail_lists', [
                'name' => $this->mailList->name,
                'status' => $this->mailList->status,
            ]);
        }
        $this->assertTrue(true);
    }
}
