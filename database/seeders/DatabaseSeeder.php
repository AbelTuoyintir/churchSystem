<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\Contribution;
use App\Models\Fund;
use App\Models\Group;
use App\Models\Household;
use App\Models\Message;
use App\Models\Person;
use App\Models\Pledge;
use App\Models\Service;
use App\Models\Team;
use App\Models\TeamPosition;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Password for all seeded users
        $password = Hash::make('password');

        // 1. Create People & Users for Personas
        $adminPerson = Person::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'phone' => '555-0101',
            'membership_status' => 'member',
            'is_active' => true,
        ]);

        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => $password,
            'role' => 'admin',
            'person_id' => $adminPerson->id,
        ]);

        $staffPerson = Person::create([
            'first_name' => 'Staff',
            'last_name' => 'Member',
            'email' => 'staff@example.com',
            'phone' => '555-0102',
            'membership_status' => 'member',
            'is_active' => true,
        ]);

        $staffUser = User::create([
            'name' => 'Staff Member',
            'email' => 'staff@example.com',
            'password' => $password,
            'role' => 'staff',
            'person_id' => $staffPerson->id,
        ]);

        $leaderPerson = Person::create([
            'first_name' => 'Leader',
            'last_name' => 'John',
            'email' => 'leader@example.com',
            'phone' => '555-0103',
            'membership_status' => 'member',
            'is_active' => true,
        ]);

        $leaderUser = User::create([
            'name' => 'Leader John',
            'email' => 'leader@example.com',
            'password' => $password,
            'role' => 'leader',
            'person_id' => $leaderPerson->id,
        ]);

        $memberPerson = Person::create([
            'first_name' => 'Jane',
            'last_name' => 'Member',
            'preferred_name' => 'Jane',
            'email' => 'member@example.com',
            'phone' => '555-0104',
            'address_line1' => '123 Grace Way',
            'city' => 'Springfield',
            'state' => 'IL',
            'postal_code' => '62701',
            'date_of_birth' => '1990-05-15',
            'membership_status' => 'member',
            'is_active' => true,
            'email_opt_in' => true,
            'sms_opt_in' => true,
        ]);

        $memberUser = User::create([
            'name' => 'Jane Member',
            'email' => 'member@example.com',
            'password' => $password,
            'role' => 'member',
            'person_id' => $memberPerson->id,
        ]);

        $viewerPerson = Person::create([
            'first_name' => 'Viewer',
            'last_name' => 'Guest',
            'email' => 'viewer@example.com',
            'phone' => '555-0105',
            'membership_status' => 'visitor',
            'is_active' => true,
        ]);

        $viewerUser = User::create([
            'name' => 'Viewer Guest',
            'email' => 'viewer@example.com',
            'password' => $password,
            'role' => 'viewer',
            'person_id' => $viewerPerson->id,
        ]);

        // 2. Create Household
        $household = Household::create([
            'name' => 'The Member Household',
            'address_line1' => '123 Grace Way',
            'city' => 'Springfield',
            'state' => 'IL',
            'postal_code' => '62701',
        ]);

        $household->people()->attach($memberPerson->id, ['role' => 'head']);

        // 3. Create Group
        $group = Group::create([
            'name' => 'Young Adults Life Group',
            'description' => 'A weekly gathering for young adults to study scripture and encourage each other.',
            'type' => 'small_group',
            'meeting_day' => 'wednesday',
            'meeting_time' => '19:00',
            'is_active' => true,
            'leader_id' => $leaderPerson->id,
        ]);

        $group->people()->attach($leaderPerson->id, ['role' => 'leader']);
        $group->people()->attach($memberPerson->id, ['role' => 'member']);

        // 4. Create Services
        $sundayService = Service::create([
            'name' => 'Sunday Morning Worship',
            'day_of_week' => 0,
            'start_time' => '10:00:00',
            'is_active' => true,
        ]);

        $midweekService = Service::create([
            'name' => 'Midweek Prayer Meeting',
            'day_of_week' => 3,
            'start_time' => '19:00:00',
            'is_active' => true,
        ]);

        // 5. Create Attendance Records
        Attendance::create([
            'person_id' => $memberPerson->id,
            'service_id' => $sundayService->id,
            'attended_on' => now()->subDays(3)->toDateString(),
            'status' => 'present',
        ]);

        Attendance::create([
            'person_id' => null,
            'service_id' => $sundayService->id,
            'attended_on' => now()->subDays(3)->toDateString(),
            'headcount' => 120,
            'status' => 'present',
        ]);

        // 6. Create Teams & Serving Assignments
        $worshipTeam = Team::create([
            'name' => 'Worship Ministry',
            'description' => 'Leads musical worship during services.',
        ]);

        $vocalistPosition = TeamPosition::create([
            'team_id' => $worshipTeam->id,
            'name' => 'Vocalist',
            'description' => 'Sings backing or lead vocals.',
        ]);

        Assignment::create([
            'team_id' => $worshipTeam->id,
            'position_id' => $vocalistPosition->id,
            'person_id' => $memberPerson->id,
            'service_id' => $sundayService->id,
            'assigned_on' => now()->addDays(4)->toDateString(),
            'status' => 'scheduled',
        ]);

        // 7. Create Messages
        Message::create([
            'subject' => 'Welcome to Church',
            'body' => 'Thank you for joining us this Sunday!',
            'channel' => 'email',
            'audience_filter' => ['type' => 'all'],
            'status' => 'sent',
            'sent_at' => now()->subDays(2),
        ]);

        // 8. Create Financial Funds, Contributions & Pledges
        $generalFund = Fund::create([
            'name' => 'General Fund',
            'description' => 'General operating fund for church expenses.',
            'is_active' => true,
        ]);

        $buildingFund = Fund::create([
            'name' => 'Building Expansion Fund',
            'description' => 'Capital campaign for building renovations.',
            'goal_amount' => 50000.00,
            'is_active' => true,
        ]);

        Contribution::create([
            'person_id' => $memberPerson->id,
            'fund_id' => $generalFund->id,
            'amount' => 250.00,
            'method' => 'online',
            'contributed_on' => now()->subDays(5)->toDateString(),
            'note' => 'Weekly tithe',
            'is_anonymous' => false,
        ]);

        Pledge::create([
            'person_id' => $memberPerson->id,
            'fund_id' => $buildingFund->id,
            'amount' => 1000.00,
            'frequency' => 'annual',
            'start_date' => now()->startOfYear()->toDateString(),
            'end_date' => now()->endOfYear()->toDateString(),
        ]);
    }
}
