<?php

namespace Tests\Feature;

use App\Enums\ApplicationType;
use App\Models\Application;
use App\Models\TableType;
use http\Client\Curl\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * This tests primary usecase is to sensecheck our overly complicated applications system
 * Primarily applications/create and applications/edit that can contain an invitation code or not
 */
class ApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() : void
    {
        parent::setUp();

        // to avoid registration fields not being filled
        Config::set('con.reg_end_date', Carbon::tomorrow());
    }

    public function test_application_creation_and_edit_normal()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);
        $this->seed();

        $table = TableType::first();

        $response = $this->post(route('applications.store'), [
            "applicationType" => ApplicationType::Dealer->value,
            "code" => "",
            "displayName" => "Tin",
            "website" => "https://eurofurence.org",
            "merchandise" => "I am selling plushies",
            "denType" => "regular",
            "space" => $table->id,
            "wallseat" => "on",
            "power" => "on",
            "wanted" => "I want plushies",
            "comment" => "this is a test, who cares.",
            "tos" => "on",
            // Expected default for new Profile()
            // Should be refactored into separate view
            "attends_thu" => "on",
            "attends_fri" => "on",
            "attends_sat" => "on",
        ]);
        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('applications', [
            'user_id' => $user->id,
            'table_type_requested' => $table->id,
            'table_type_assigned' => null,
            'type' => ApplicationType::Dealer->value,
            'parent' => null,
            'display_name' => "Tin",
            'website' => "https://eurofurence.org",
            'table_number' => null,
            'merchandise' => "I am selling plushies",
            'wanted_neighbors' => "I want plushies",
            'comment' => "this is a test, who cares.",
            'is_afterdark' => 0,
            'is_power' => 1,
            'is_wallseat' => 1,
            'waiting_at' => null,
            'offer_sent_at' => null,
            'offer_accepted_at' => null,
        ]);
    }

    public function test_application_requires_fields()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);
        $this->seed();

        $table = TableType::first();

        /**
         * Assert Dealers
         */
        $response = $this->post(route('applications.store'), [
            "applicationType" => ApplicationType::Dealer->value,
        ]);
        $response->assertSessionHasErrors(['tos', 'denType', 'merchandise', 'space']);

        /**
         * Assert Assistants
         */
        $response = $this->post(route('applications.store'), [
            "applicationType" => ApplicationType::Assistant->value,
        ]);
        $response->assertSessionHasErrors(['tos', 'code']);
        $response->assertSessionDoesntHaveErrors(['denType', 'merchandise', 'space']);

        /**
         * Assert Shares
         */
        $response = $this->post(route('applications.store'), [
            "applicationType" => ApplicationType::Share->value,
        ]);
        $response->assertSessionHasErrors(['tos', 'merchandise', 'code']);
        $response->assertSessionDoesntHaveErrors(['denType', 'space']);
    }

    public function test_updating_existing_share_does_not_require_code()
    {
        $parent = \App\Models\User::factory()->create();
        $parentApp = Application::factory()->create(['user_id' => $parent->id]);

        $child = \App\Models\User::factory()->create();
        $childApp = Application::factory()->create([
                'user_id' => $child->id,
                'type' => ApplicationType::Share->value,
                'parent' => $parentApp->id,
                "canceled_at" => null,
            ]
        );

        $this->actingAs($child);

        $response = $this->put(route('applications.update'), [
            "applicationType" => ApplicationType::Share->value,
        ]);
        $response->assertSessionHasErrors(['merchandise']);
        $response->assertSessionDoesntHaveErrors(['code', 'tos']);
    }


    public function test_application_creation_edit_normal()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);
        $this->seed();
        $application = Application::factory()->create([
            'user_id' => $user,
            'canceled_at' => null
        ]);

        $table = TableType::first();
        $response = $this->put(route('applications.update'), [
            "applicationType" => ApplicationType::Dealer->value,
            "displayName" => "TinUpdate",
            "website" => "https://eurofurence-update.org",
            "merchandise" => "I am selling dragons",
            "denType" => "regular",
            "space" => $table->id,
            "wallseat" => "on",
            "power" => "on",
            "wanted" => "I want more dragons",
            "comment" => "I care.",
            "tos" => "on",
            // Expected default for new Profile()
            // Should be refactored into separate view
            "attends_thu" => "on",
            "attends_fri" => "on",
            "attends_sat" => "on",
        ]);
        $response->assertRedirect(route('applications.edit'));
        $this->assertDatabaseHas('applications', [
            'user_id' => $user->id,
            'table_type_requested' => $table->id,
            'type' => ApplicationType::Dealer->value,
            'parent' => null,
            'display_name' => "TinUpdate",
            'website' => "https://eurofurence-update.org",
            'merchandise' => "I am selling dragons",
            'wanted_neighbors' => "I want more dragons",
            'comment' => "I care.",
            'is_afterdark' => 0,
            'is_power' => 1,
            'is_wallseat' => 1,
        ]);
    }
}
