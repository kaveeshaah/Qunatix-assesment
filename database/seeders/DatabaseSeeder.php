<?php

namespace Database\Seeders;

use App\Enums\RequestStatus;
use App\Models\RequestActivity;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $manager = User::factory()->manager()->create(['name' => 'Maya Manager', 'email' => 'manager@example.com']);
        $alice = User::factory()->reportingTo($manager)->create(['name' => 'Alice Employee', 'email' => 'alice@example.com']);
        $bob = User::factory()->reportingTo($manager)->create(['name' => 'Bob Employee', 'email' => 'bob@example.com']);
        $ops = User::factory()->operations()->create(['name' => 'Omar Operations', 'email' => 'ops@example.com']);

        // A second manager with no link to the first, handy for trying 403 responses.
        $other = User::factory()->manager()->create(['name' => 'Nina Manager', 'email' => 'manager2@example.com']);
        User::factory()->reportingTo($other)->create(['name' => 'Carl Employee', 'email' => 'carl@example.com']);

        $S = RequestStatus::class;
        $paths = [
            [],
            [],
            [$S::Submitted],
            [$S::Submitted],
            [$S::Submitted, $S::Approved],
            [$S::Submitted, $S::Rejected],
            [$S::Submitted, $S::Returned],
            [$S::Submitted, $S::Approved, $S::Assigned],
            [$S::Submitted, $S::Approved, $S::Assigned, $S::InProgress],
            [$S::Submitted, $S::Approved, $S::Assigned, $S::InProgress, $S::Completed],
        ];

        foreach ($paths as $i => $path) {
            $this->seedRequest($i % 2 === 0 ? $alice : $bob, $manager, $ops, $path);
        }
    }

    /** @param  list<RequestStatus>  $path */
    private function seedRequest(User $requester, User $manager, User $ops, array $path): void
    {
        $status = $path === [] ? RequestStatus::Draft : $path[array_key_last($path)];
        $assigned = in_array($status, [RequestStatus::Assigned, RequestStatus::InProgress, RequestStatus::Completed], true);

        $request = ServiceRequest::factory()->create([
            'requester_id' => $requester->id,
            'status' => $status,
            'assignee_id' => $assigned ? $ops->id : null,
            'submitted_at' => $path === [] ? null : now()->subDays(3),
        ]);

        RequestActivity::create([
            'service_request_id' => $request->id,
            'actor_id' => $requester->id,
            'action' => 'created',
            'to_status' => RequestStatus::Draft,
        ]);

        $from = RequestStatus::Draft;
        foreach ($path as $to) {
            $actor = match ($to) {
                RequestStatus::Submitted => $requester,
                RequestStatus::Approved, RequestStatus::Rejected, RequestStatus::Returned => $manager,
                default => $ops,
            };

            RequestActivity::create([
                'service_request_id' => $request->id,
                'actor_id' => $actor->id,
                'action' => $to->value,
                'from_status' => $from,
                'to_status' => $to,
                'comment' => in_array($to, [RequestStatus::Rejected, RequestStatus::Returned], true)
                    ? 'Please add more detail.' : null,
            ]);
            $from = $to;
        }
    }
}