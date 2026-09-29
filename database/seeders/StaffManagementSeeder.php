<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\StaffRole;
use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Departments Structure
        $departmentsData = [
            ['name' => 'Executive Team', 'code' => 'EXEC', 'description' => 'C-Suite Executive management and agency directors.'],
            ['name' => 'Technical Team', 'code' => 'TECH', 'description' => 'Software engineering, cloud infrastructure, AI development, and cybersecurity.'],
            ['name' => 'Product & Design Team', 'code' => 'PROD', 'description' => 'UI/UX designs, product management, and creative design systems.'],
            ['name' => 'Operations & Administration', 'code' => 'OPS', 'description' => 'Human Resources, Operations, legal affairs, and office administration.'],
            ['name' => 'Sales & Marketing Team', 'code' => 'MKT', 'description' => 'Business development, client acquisitions, brand strategies, and digital marketing.'],
            ['name' => 'Client Support Team', 'code' => 'SUPP', 'description' => 'Client success management and customer support operations.'],
            ['name' => 'Diwebs Academy / Training Division', 'code' => 'ACAD', 'description' => 'LMS course coordinators, CBT assessments, and student support.'],
            ['name' => 'Future Expansion Roles', 'code' => 'EXP', 'description' => 'Enterprise scaling, cloud architecture, and investor relations.'],
        ];

        $departments = [];
        foreach ($departmentsData as $dep) {
            $departments[$dep['code']] = Department::create($dep);
        }

        // 2. Predefined Roles per Department
        $rolesData = [
            'EXEC' => [
                ['title' => 'CEO', 'permissions' => ['super_admin_access']],
                ['title' => 'COO', 'permissions' => ['super_admin_access', 'view_dashboard', 'view_reports']],
                ['title' => 'CTO', 'permissions' => ['super_admin_access', 'view_dashboard', 'manage_projects', 'manage_academy', 'manage_cbt']],
                ['title' => 'CFO', 'permissions' => ['view_dashboard', 'manage_finance', 'view_reports']],
                ['title' => 'CMO', 'permissions' => ['view_dashboard', 'manage_clients', 'view_reports']],
            ],
            'TECH' => [
                ['title' => 'Senior Backend Developer', 'permissions' => ['view_dashboard', 'manage_projects']],
                ['title' => 'Frontend Developer', 'permissions' => ['view_dashboard', 'manage_projects']],
                ['title' => 'Mobile App Developer', 'permissions' => ['view_dashboard', 'manage_projects']],
                ['title' => 'AI/ML Engineer', 'permissions' => ['view_dashboard', 'manage_projects']],
                ['title' => 'DevOps Engineer', 'permissions' => ['view_dashboard', 'manage_projects']],
                ['title' => 'Cybersecurity Specialist', 'permissions' => ['view_dashboard', 'manage_projects']],
                ['title' => 'Database Administrator', 'permissions' => ['view_dashboard', 'manage_projects']],
                ['title' => 'QA Engineer', 'permissions' => ['view_dashboard', 'manage_projects']],
            ],
            'PROD' => [
                ['title' => 'Product Manager', 'permissions' => ['view_dashboard', 'manage_projects', 'manage_clients']],
                ['title' => 'UI/UX Designer', 'permissions' => ['view_dashboard', 'manage_projects']],
                ['title' => 'Graphic Designer', 'permissions' => ['view_dashboard', 'manage_projects']],
                ['title' => 'Video Editor', 'permissions' => ['view_dashboard', 'manage_projects']],
            ],
            'OPS' => [
                ['title' => 'Operations Manager', 'permissions' => ['view_dashboard', 'manage_staff', 'manage_finance', 'view_reports']],
                ['title' => 'Project Manager', 'permissions' => ['view_dashboard', 'manage_projects', 'manage_clients']],
                ['title' => 'HR Manager', 'permissions' => ['view_dashboard', 'manage_staff', 'view_reports']],
                ['title' => 'Administrative Officer', 'permissions' => ['view_dashboard', 'manage_staff']],
                ['title' => 'Legal Officer', 'permissions' => ['view_dashboard', 'view_reports']],
            ],
            'MKT' => [
                ['title' => 'Business Development Manager', 'permissions' => ['view_dashboard', 'manage_clients', 'view_reports']],
                ['title' => 'Sales Executive', 'permissions' => ['view_dashboard', 'manage_clients']],
                ['title' => 'Digital Marketing Specialist', 'permissions' => ['view_dashboard', 'manage_clients']],
                ['title' => 'SEO Specialist', 'permissions' => ['view_dashboard', 'manage_clients']],
                ['title' => 'Social Media Manager', 'permissions' => ['view_dashboard', 'manage_clients']],
                ['title' => 'Brand Strategist', 'permissions' => ['view_dashboard', 'manage_clients']],
            ],
            'SUPP' => [
                ['title' => 'Customer Support Manager', 'permissions' => ['view_dashboard', 'manage_clients']],
                ['title' => 'Support Officer', 'permissions' => ['view_dashboard', 'manage_clients']],
                ['title' => 'Client Success Manager', 'permissions' => ['view_dashboard', 'manage_clients']],
                ['title' => 'Account Manager', 'permissions' => ['view_dashboard', 'manage_clients']],
            ],
            'ACAD' => [
                ['title' => 'Academy Director', 'permissions' => ['view_dashboard', 'manage_academy', 'view_reports']],
                ['title' => 'Course Coordinator', 'permissions' => ['view_dashboard', 'manage_academy']],
                ['title' => 'Technical Instructor', 'permissions' => ['view_dashboard', 'manage_academy']],
                ['title' => 'CBT Administrator', 'permissions' => ['view_dashboard', 'manage_cbt']],
                ['title' => 'Student Support Officer', 'permissions' => ['view_dashboard', 'manage_academy']],
            ],
            'EXP' => [
                ['title' => 'SaaS Product Director', 'permissions' => ['view_dashboard', 'manage_projects', 'view_reports']],
                ['title' => 'Cloud Architect', 'permissions' => ['view_dashboard', 'manage_projects']],
                ['title' => 'Data Analyst', 'permissions' => ['view_dashboard', 'view_reports']],
                ['title' => 'Research & Innovation Lead', 'permissions' => ['view_dashboard', 'view_reports']],
                ['title' => 'Investor Relations Manager', 'permissions' => ['view_dashboard', 'view_reports']],
            ],
        ];

        $createdRoles = [];
        foreach ($rolesData as $code => $roles) {
            $dep = $departments[$code];
            foreach ($roles as $r) {
                $createdRoles[$dep->code . '_' . $r['title']] = StaffRole::create([
                    'department_id' => $dep->id,
                    'title' => $r['title'],
                    'permissions' => $r['permissions'],
                ]);
            }
        }

        // 3. Default Staff Member (HR Manager under OPS department)
        $hrRole = $createdRoles['OPS_HR Manager'];
        $hrDep = $departments['OPS'];

        $staff = Staff::create([
            'staff_id' => 'DWS-1001',
            'name' => 'Diwebs HR Coordinator',
            'email' => 'staff@diwebstechagency.website',
            'username' => 'staffmember',
            'password' => Hash::make('password123'),
            'phone' => '+2348000000000',
            'address' => 'Diwebs Agency Headquarters, Tech Hub, Lagos',
            'dob' => '1992-05-15',
            'gender' => 'Male',
            'nationality' => 'Nigerian',
            'department_id' => $hrDep->id,
            'role_id' => $hrRole->id,
            'employment_type' => 'Full-time',
            'salary_grade' => 'SG-12',
            'base_salary' => 750000.00,
            'date_hired' => '2024-01-10',
            'office_location' => 'Lagos Office, Block A',
            'status' => 'Active',
            'force_password_change' => true,
        ]);

        // Update department head
        $hrDep->update(['head_id' => $staff->id]);
    }
}
