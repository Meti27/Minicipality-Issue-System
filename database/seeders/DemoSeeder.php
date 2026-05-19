<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\ComplaintStatusHistory;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Staff accounts
        $staff1 = User::firstOrCreate(
            ['email' => 'sara.jones@municipality.gov'],
            [
                'name' => 'Sara Jones',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'is_active' => true,
            ]
        );

        $staff2 = User::firstOrCreate(
            ['email' => 'mike.chen@municipality.gov'],
            [
                'name' => 'Mike Chen',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'is_active' => true,
            ]
        );

        // Citizen accounts
        $citizens = [];
        $citizenData = [
            ['name' => 'Alice Brennan',  'email' => 'alice@example.com'],
            ['name' => 'Bob Kowalski',   'email' => 'bob@example.com'],
            ['name' => 'Fatima Nkosi',   'email' => 'fatima@example.com'],
        ];

        foreach ($citizenData as $data) {
            $citizens[] = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'      => $data['name'],
                    'password'  => Hash::make('password'),
                    'role'      => 'citizen',
                    'is_active' => true,
                ]
            );
        }

        $categories = Category::all()->keyBy('name');

        // Each complaint definition: [citizen index, category, title, location, description, priority, lifecycle]
        // lifecycle = ordered list of [old_status, new_status, changed_by (staff model), comment]
        $complaints = [
            // --- submitted (no staff action yet) ---
            [
                'citizen'     => 0,
                'category'    => 'Potholes',
                'title'       => 'Large pothole on Elm Street',
                'location'    => 'Elm Street near junction with Oak Avenue',
                'description' => 'There is a very large pothole roughly 40 cm wide causing vehicles to swerve into oncoming traffic.',
                'priority'    => 'high',
                'lifecycle'   => [],
            ],
            [
                'citizen'     => 1,
                'category'    => 'Garbage',
                'title'       => 'Overflowing bins at Central Park entrance',
                'location'    => 'Central Park, North Gate',
                'description' => 'The public bins have not been collected for over two weeks. Rubbish is spilling onto the footpath.',
                'priority'    => 'medium',
                'lifecycle'   => [],
            ],

            // --- pending_review ---
            [
                'citizen'     => 2,
                'category'    => 'Streetlights',
                'title'       => 'Three streetlights out on Maple Drive',
                'location'    => 'Maple Drive, between house numbers 12 and 34',
                'description' => 'Three consecutive streetlights have been dark for a week, making the road dangerous at night.',
                'priority'    => 'high',
                'lifecycle'   => [
                    [null, 'submitted', null, null],
                    ['submitted', 'pending_review', 'staff1', 'Logged and assigned for inspection.'],
                ],
            ],

            // --- validated ---
            [
                'citizen'     => 0,
                'category'    => 'Water Leaks',
                'title'       => 'Burst water main on River Road',
                'location'    => 'River Road, opposite number 78',
                'description' => 'Water is gushing from the pavement. The leak started early this morning and is flooding the gutter.',
                'priority'    => 'high',
                'lifecycle'   => [
                    ['submitted', 'pending_review', 'staff1', 'Received and assigned to field team.'],
                    ['pending_review', 'validated', 'staff1', 'Field team confirmed active leak. Work order raised.'],
                ],
            ],
            [
                'citizen'     => 1,
                'category'    => 'Damaged Roads',
                'title'       => 'Missing road markings on High Street',
                'location'    => 'High Street, near the bus terminal',
                'description' => 'The centre-line and pedestrian crossing markings have faded completely. Near-miss accidents reported.',
                'priority'    => 'medium',
                'lifecycle'   => [
                    ['submitted', 'pending_review', 'staff2', 'Under initial review.'],
                    ['pending_review', 'validated', 'staff2', 'Confirmed on-site. Scheduled for repainting.'],
                ],
            ],

            // --- in_progress ---
            [
                'citizen'     => 2,
                'category'    => 'Potholes',
                'title'       => 'Multiple potholes on Station Road',
                'location'    => 'Station Road, between stops 3 and 5',
                'description' => 'Cluster of five potholes making the road almost impassable for cyclists and motorcyclists.',
                'priority'    => 'high',
                'lifecycle'   => [
                    ['submitted', 'pending_review', 'staff1', 'Assigned to roads crew.'],
                    ['pending_review', 'validated', 'staff1', 'Inspection completed. Repair crew dispatched.'],
                    ['validated', 'in_progress', 'staff1', 'Repair crew on site, temporary fill applied.'],
                ],
            ],
            [
                'citizen'     => 0,
                'category'    => 'Garbage',
                'title'       => 'Illegal dumping behind the library',
                'location'    => 'Behind the Municipal Library on Book Lane',
                'description' => 'A large pile of construction waste and old furniture has been dumped in the service alley.',
                'priority'    => 'medium',
                'lifecycle'   => [
                    ['submitted', 'pending_review', 'staff2', 'Received.'],
                    ['pending_review', 'validated', 'staff2', 'Confirmed illegal dump. Clean-up crew booked.'],
                    ['validated', 'in_progress', 'staff2', 'Clean-up crew on site today.'],
                ],
            ],

            // --- resolved ---
            [
                'citizen'     => 1,
                'category'    => 'Streetlights',
                'title'       => 'Streetlight flickering outside school',
                'location'    => 'Pine Street, in front of St. Mary\'s Primary School',
                'description' => 'The streetlight directly in front of the school gate flickers constantly and sometimes goes off completely.',
                'priority'    => 'high',
                'lifecycle'   => [
                    ['submitted', 'pending_review', 'staff1', 'Noted and escalated.'],
                    ['pending_review', 'validated', 'staff1', 'Electrician sent to inspect.'],
                    ['validated', 'in_progress', 'staff1', 'Faulty ballast being replaced.'],
                    ['in_progress', 'resolved', 'staff1', 'Ballast replaced and tested. Light fully operational.'],
                ],
            ],
            [
                'citizen'     => 2,
                'category'    => 'Water Leaks',
                'title'       => 'Dripping fire hydrant on Cedar Close',
                'location'    => 'Cedar Close, near cul-de-sac',
                'description' => 'A fire hydrant has been slowly leaking for about three days. Water is pooling on the pavement.',
                'priority'    => 'low',
                'lifecycle'   => [
                    ['submitted', 'pending_review', 'staff2', 'Assigned to water crew.'],
                    ['pending_review', 'validated', 'staff2', 'Confirmed minor hydrant leak.'],
                    ['validated', 'in_progress', 'staff2', 'Crew replacing hydrant seal.'],
                    ['in_progress', 'resolved', 'staff2', 'Seal replaced. No further leakage observed.'],
                ],
            ],

            // --- closed ---
            [
                'citizen'     => 0,
                'category'    => 'Damaged Roads',
                'title'       => 'Cracked pavement tiles on Market Square',
                'location'    => 'Market Square, east pedestrian zone',
                'description' => 'Several paving tiles are cracked and uneven, creating a trip hazard for pedestrians.',
                'priority'    => 'low',
                'lifecycle'   => [
                    ['submitted', 'pending_review', 'staff1', 'Logged.'],
                    ['pending_review', 'validated', 'staff1', 'Confirmed hazard. Tiles ordered.'],
                    ['validated', 'in_progress', 'staff1', 'Tile replacement underway.'],
                    ['in_progress', 'resolved', 'staff1', 'All tiles replaced and levelled.'],
                    ['resolved', 'closed', 'staff1', 'Citizen confirmed satisfaction. Closing.'],
                ],
            ],

            // --- rejected ---
            [
                'citizen'     => 1,
                'category'    => 'Potholes',
                'title'       => 'Pothole on private car park',
                'location'    => 'Westfield Shopping Centre car park, Level 2',
                'description' => 'Large pothole near the elevator entrance on the second level of the shopping centre car park.',
                'priority'    => 'medium',
                'lifecycle'   => [
                    ['submitted', 'pending_review', 'staff2', 'Under review.'],
                    ['pending_review', 'rejected', 'staff2', null],
                ],
                'rejection_reason' => 'This location is on private property and falls outside municipal jurisdiction. Please contact the shopping centre management directly.',
            ],

            // Extra submitted complaints to pad the list
            [
                'citizen'     => 2,
                'category'    => 'Streetlights',
                'title'       => 'No streetlight at bus stop on King Road',
                'location'    => 'King Road, Bus Stop 14',
                'description' => 'The bus stop has no lighting at all. Commuters waiting at night feel unsafe.',
                'priority'    => 'medium',
                'lifecycle'   => [],
            ],
            [
                'citizen'     => 0,
                'category'    => 'Garbage',
                'title'       => 'Bin fire residue not cleaned up',
                'location'    => 'Green Lane Park, near the playground',
                'description' => 'A bin was set on fire last weekend. The burnt debris and ash are still on the ground.',
                'priority'    => 'low',
                'lifecycle'   => [],
            ],
            [
                'citizen'     => 1,
                'category'    => 'Water Leaks',
                'title'       => 'Wet patch in road surface on Valley Street',
                'location'    => 'Valley Street, between numbers 5 and 9',
                'description' => 'A persistent wet patch suggests an underground pipe leak. The road surface is beginning to sink.',
                'priority'    => 'high',
                'lifecycle'   => [],
            ],
        ];

        $staffMap = ['staff1' => $staff1, 'staff2' => $staff2];

        foreach ($complaints as $def) {
            $citizen = $citizens[$def['citizen']];
            $category = $categories[$def['category']];

            // Determine final status
            $finalStatus = 'submitted';
            foreach ($def['lifecycle'] as $step) {
                $finalStatus = $step[1];
            }

            $complaint = Complaint::create([
                'user_id'          => $citizen->id,
                'category_id'      => $category->id,
                'title'            => $def['title'],
                'location'         => $def['location'],
                'description'      => $def['description'],
                'priority'         => $def['priority'],
                'status'           => $finalStatus,
                'rejection_reason' => $def['rejection_reason'] ?? null,
            ]);

            // Status history records
            foreach ($def['lifecycle'] as $step) {
                [$oldStatus, $newStatus, $staffKey, $comment] = $step;

                if ($oldStatus === null) {
                    // initial submitted record — skip, the complaint creation is implicit
                    continue;
                }

                $changedBy = $staffKey ? $staffMap[$staffKey] : null;

                ComplaintStatusHistory::create([
                    'complaint_id' => $complaint->id,
                    'changed_by'   => $changedBy?->id,
                    'old_status'   => $oldStatus,
                    'new_status'   => $newStatus,
                    'comment'      => $comment,
                ]);

                // Notification to the citizen on each staff action
                $messages = [
                    'pending_review' => "Your complaint \"{$complaint->title}\" is now under review.",
                    'validated'      => "Your complaint \"{$complaint->title}\" has been validated and will be addressed.",
                    'in_progress'    => "Work has started on your complaint \"{$complaint->title}\".",
                    'resolved'       => "Your complaint \"{$complaint->title}\" has been resolved.",
                    'closed'         => "Your complaint \"{$complaint->title}\" has been closed.",
                    'rejected'       => "Your complaint \"{$complaint->title}\" was rejected. Reason: " . ($def['rejection_reason'] ?? ''),
                ];

                if (isset($messages[$newStatus])) {
                    Notification::create([
                        'user_id'      => $citizen->id,
                        'complaint_id' => $complaint->id,
                        'message'      => $messages[$newStatus],
                        'is_read'      => in_array($newStatus, ['resolved', 'closed']),
                    ]);
                }
            }
        }
    }
}
