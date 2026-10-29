<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\BranchComplianceRecord;
use App\Models\Company;
use App\Models\Compliance;
use App\Models\ComplianceType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. ROLES ──────────────────────────────────────────────────────────
        $roleNames = ['admin', 'branch_manager', 'compliance_officer', 'viewer'];
        foreach ($roleNames as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // ── 2. PERMISSIONS ────────────────────────────────────────────────────
        $permissions = [
            'view dashboard',
            'manage users',
            'manage branches',
            'manage compliances',
            'manage compliance records',
            'view reports',
        ];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        Role::findByName('admin')->syncPermissions($permissions);
        Role::findByName('branch_manager')->syncPermissions([
            'view dashboard', 'manage compliances', 'manage compliance records', 'view reports',
        ]);
        Role::findByName('compliance_officer')->syncPermissions([
            'view dashboard', 'manage compliance records', 'view reports',
        ]);
        Role::findByName('viewer')->syncPermissions(['view dashboard', 'view reports']);

        // ── 3. USERS ──────────────────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@compliance.com'],
            ['name' => 'Admin User', 'password' => 'admin123']
        );
        $admin->syncRoles(['admin']);

        $manager1 = User::firstOrCreate(
            ['email' => 'rahul@compliance.com'],
            ['name' => 'Rahul Sharma', 'password' => 'rahul123']
        );
        $manager1->syncRoles(['branch_manager']);

        $manager2 = User::firstOrCreate(
            ['email' => 'amit@compliance.com'],
            ['name' => 'Amit Verma', 'password' => 'amit123']
        );
        $manager2->syncRoles(['branch_manager']);

        $officer1 = User::firstOrCreate(
            ['email' => 'neha@compliance.com'],
            ['name' => 'Neha Gupta', 'password' => 'neha123']
        );
        $officer1->syncRoles(['compliance_officer']);

        $officer2 = User::firstOrCreate(
            ['email' => 'suresh@compliance.com'],
            ['name' => 'Suresh Patel', 'password' => 'suresh123']
        );
        $officer2->syncRoles(['compliance_officer']);

        $viewer = User::firstOrCreate(
            ['email' => 'viewer@compliance.com'],
            ['name' => 'Viewer User', 'password' => 'viewer123']
        );
        $viewer->syncRoles(['viewer']);

        // ── 4. COMPLIANCE TYPES ───────────────────────────────────────────────
        // 'terms' is now the required field (Half Yearly / Annually / Five Years)
        // 'description' column still exists in DB but is no longer used in the form
        $typeData = [
            ['name' => 'Legal & Regulatory', 'terms' => 'annually',    'is_active' => true],
            ['name' => 'Environmental',       'terms' => 'half_yearly', 'is_active' => true],
            ['name' => 'Health & Safety',     'terms' => 'annually',    'is_active' => true],
            ['name' => 'Financial Audit',     'terms' => 'annually',    'is_active' => true],
            ['name' => 'Data Protection',     'terms' => 'five_years',  'is_active' => true],
        ];
        $types = [];
        foreach ($typeData as $t) {
            $types[] = ComplianceType::firstOrCreate(['name' => $t['name']], $t);
        }

        // ── 5. COMPANIES ──────────────────────────────────────────────────────
        $companies = [];
        foreach (['Alpha Corp', 'Beta Enterprises', 'Gamma Industries'] as $name) {
            $companies[] = Company::firstOrCreate(['name' => $name]);
        }

        // ── 6. BRANCHES ───────────────────────────────────────────────────────
        $branchDefs = [
            ['title' => 'Delhi HQ',       'location' => 'New Delhi',  'company' => 0, 'responsible' => $manager1, 'approver' => $admin],
            ['title' => 'Mumbai Office',   'location' => 'Mumbai',     'company' => 0, 'responsible' => $manager1, 'approver' => $admin],
            ['title' => 'Bangalore Tech',  'location' => 'Bangalore',  'company' => 0, 'responsible' => $manager2, 'approver' => $admin],
            ['title' => 'Chennai Hub',     'location' => 'Chennai',    'company' => 1, 'responsible' => $manager2, 'approver' => $manager1],
            ['title' => 'Pune Centre',     'location' => 'Pune',       'company' => 1, 'responsible' => $officer1, 'approver' => $manager1],
            ['title' => 'Hyderabad Plant', 'location' => 'Hyderabad',  'company' => 2, 'responsible' => $officer2, 'approver' => $manager2],
            ['title' => 'Kolkata Depot',   'location' => 'Kolkata',    'company' => 2, 'responsible' => $officer1, 'approver' => $manager2],
        ];

        $branches = [];
        foreach ($branchDefs as $b) {
            $branches[] = Branch::firstOrCreate(
                ['title' => $b['title']],
                [
                    'location'          => $b['location'],
                    'company_id'        => $companies[$b['company']]->id,
                    'responsible_id'    => $b['responsible']->id,
                    'first_approver_id' => $b['approver']->id,
                ]
            );
        }

        // Assign users to branches (branch_user pivot)
        $branches[0]->users()->syncWithoutDetaching([$admin->id, $manager1->id, $officer1->id]);
        $branches[1]->users()->syncWithoutDetaching([$manager1->id, $officer1->id]);
        $branches[2]->users()->syncWithoutDetaching([$manager2->id, $officer2->id]);
        $branches[3]->users()->syncWithoutDetaching([$manager2->id, $officer2->id]);
        $branches[4]->users()->syncWithoutDetaching([$officer1->id, $viewer->id]);
        $branches[5]->users()->syncWithoutDetaching([$officer2->id, $viewer->id]);
        $branches[6]->users()->syncWithoutDetaching([$officer1->id, $manager2->id]);

        // ── 7. COMPLIANCES ────────────────────────────────────────────────────
        $complianceDefs = [
            ['name' => 'GST Registration',             'type' => 0],
            ['name' => 'Factory License',               'type' => 0],
            ['name' => 'Trade License',                 'type' => 0],
            ['name' => 'Pollution Control Certificate', 'type' => 1],
            ['name' => 'Effluent Treatment Report',     'type' => 1],
            ['name' => 'Fire Safety Certificate',       'type' => 2],
            ['name' => 'First Aid Compliance',          'type' => 2],
            ['name' => 'Annual Audit Report',           'type' => 3],
            ['name' => 'Tax Assessment',                'type' => 3],
            ['name' => 'GDPR Data Policy',              'type' => 4],
            ['name' => 'Data Backup Certificate',       'type' => 4],
        ];

        $compliances = [];
        foreach ($complianceDefs as $c) {
            $compliances[] = Compliance::firstOrCreate(
                ['name' => $c['name']],
                ['compliance_type_id' => $types[$c['type']]->id]
            );
        }

        // Assign 3–6 random compliances to each branch
        foreach ($branches as $branch) {
            $branch->compliances()->syncWithoutDetaching(
                collect($compliances)->random(rand(3, 6))->pluck('id')->toArray()
            );
        }

        // ── 8. COMPLIANCE RECORDS ─────────────────────────────────────────────
        // Status is derived from from_date / to_date:
        //   process  = both dates null
        //   renewal  = to_date in the past
        //   critical = to_date within next 15 days
        //   pending  = to_date more than 15 days away (approval pending)
        //   approved = explicitly set by approver

        $today = Carbon::today();

        foreach ($branches as $branch) {
            foreach ($branch->compliances as $compliance) {
                if (BranchComplianceRecord::where('branch_id', $branch->id)
                    ->where('compliance_id', $compliance->id)->exists()) {
                    continue;
                }

                // Randomly assign one of 5 scenarios
                $scenario = rand(1, 5);
                [$fromDate, $toDate, $status, $renewalDue] = match ($scenario) {
                    // Under Process — no dates
                    1 => [null, null, 'process', false],
                    // Renewal Due — to_date expired
                    2 => [
                        $today->copy()->subMonths(rand(6, 24))->toDateString(),
                        $today->copy()->subDays(rand(1, 30))->toDateString(),
                        'renewal', true,
                    ],
                    // Critical — to_date within 15 days
                    3 => [
                        $today->copy()->subMonths(rand(1, 6))->toDateString(),
                        $today->copy()->addDays(rand(1, 14))->toDateString(),
                        'critical', false,
                    ],
                    // Approval Pending — to_date > 15 days away
                    4 => [
                        $today->copy()->subMonths(rand(1, 3))->toDateString(),
                        $today->copy()->addDays(rand(16, 180))->toDateString(),
                        'pending', false,
                    ],
                    // Approved — to_date in future, manually approved
                    5 => [
                        $today->copy()->subMonths(rand(1, 6))->toDateString(),
                        $today->copy()->addDays(rand(30, 365))->toDateString(),
                        'approved', false,
                    ],
                };

                BranchComplianceRecord::create([
                    'branch_id'     => $branch->id,
                    'compliance_id' => $compliance->id,
                    'from_date'     => $fromDate,
                    'to_date'       => $toDate,
                    'status'        => $status,
                    'renewal_due'   => $renewalDue,
                ]);
            }
        }

        // Guarantee at least 3 records of every status type
        $this->ensureStatusCoverage($branches, $compliances, $today);

        // ── 9. SUMMARY ────────────────────────────────────────────────────────
        $this->command->info('✅ Seeding complete!');
        $this->command->table(
            ['Stat', 'Count'],
            [
                ['Companies',             Company::count()],
                ['Branches',              Branch::count()],
                ['Compliance Types',      ComplianceType::count()],
                ['Compliances',           Compliance::count()],
                ['Users',                 User::count()],
                ['Roles',                 Role::count()],
                ['Compliance Records',    BranchComplianceRecord::count()],
                ['↻ Under Process',       BranchComplianceRecord::where('status', 'process')->count()],
                ['⏳ Approval Pending',   BranchComplianceRecord::where('status', 'pending')->count()],
                ['⚠ Critical',           BranchComplianceRecord::where('status', 'critical')->count()],
                ['↺ Renewal Due',         BranchComplianceRecord::where('status', 'renewal')->count()],
                ['✓ Approved',           BranchComplianceRecord::where('status', 'approved')->count()],
            ]
        );

        $this->command->newLine();
        $this->command->info('🔑 Login credentials:');
        $this->command->table(
            ['Name', 'Email', 'Password', 'Role'],
            [
                ['Admin User',   'admin@compliance.com',  'admin123',  'admin'],
                ['Rahul Sharma', 'rahul@compliance.com',  'rahul123',  'branch_manager'],
                ['Amit Verma',   'amit@compliance.com',   'amit123',   'branch_manager'],
                ['Neha Gupta',   'neha@compliance.com',   'neha123',   'compliance_officer'],
                ['Suresh Patel', 'suresh@compliance.com', 'suresh123', 'compliance_officer'],
                ['Viewer User',  'viewer@compliance.com', 'viewer123', 'viewer'],
            ]
        );
    }

    /**
     * Ensure every status has at least 3 records for a meaningful dashboard.
     */
    private function ensureStatusCoverage(array $branches, array $compliances, Carbon $today): void
    {
        $needed = [
            'process'  => [null,                                        null,                                         false],
            'renewal'  => [$today->copy()->subYear()->toDateString(),   $today->copy()->subDays(5)->toDateString(),   true],
            'critical' => [$today->copy()->subMonths(2)->toDateString(),$today->copy()->addDays(7)->toDateString(),   false],
            'pending'  => [$today->copy()->subMonth()->toDateString(),  $today->copy()->addDays(60)->toDateString(),  false],
            'approved' => [$today->copy()->subMonths(3)->toDateString(),$today->copy()->addDays(90)->toDateString(),  false],
        ];

        foreach ($needed as $status => [$from, $to, $renewal]) {
            $existing = BranchComplianceRecord::where('status', $status)->count();
            $toCreate = max(0, 3 - $existing);

            $attempts = 0;
            $created  = 0;
            while ($created < $toCreate && $attempts < 30) {
                $attempts++;
                $branch     = $branches[array_rand($branches)];
                $compliance = $compliances[array_rand($compliances)];

                if (BranchComplianceRecord::where('branch_id', $branch->id)
                    ->where('compliance_id', $compliance->id)->exists()) {
                    continue;
                }

                BranchComplianceRecord::create([
                    'branch_id'     => $branch->id,
                    'compliance_id' => $compliance->id,
                    'from_date'     => $from,
                    'to_date'       => $to,
                    'status'        => $status,
                    'renewal_due'   => $renewal,
                ]);
                $created++;
            }
        }
    }
}
