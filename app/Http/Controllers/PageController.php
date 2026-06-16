<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        $reviews = collect();
        if (\Illuminate\Support\Facades\Schema::hasTable('reviews')) {
            $reviews = \App\Models\Review::where('status', 'approved')->orderBy('created_at', 'desc')->get();
        }
        return view('pages.home', compact('reviews'));
    }

    public function about()
    {
        return view('pages.about');
    }

    public function services()
    {
        return view('pages.services');
    }

    public function solutions()
    {
        return view('pages.solutions');
    }

    public function portfolio()
    {
        $portfolios = \App\Models\Portfolio::orderBy('order', 'asc')->orderBy('created_at', 'desc')->get();
        return view('pages.portfolio', compact('portfolios'));
    }

    public function caseStudies()
    {
        return view('pages.case-studies');
    }

    public function blog()
    {
        return view('pages.blog');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function submitLead(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'service_needed' => 'required|string',
            'message' => 'required|string'
        ]);

        Lead::create($validated);

        \App\Models\AdminNotification::create([
            'type' => 'contact_form',
            'title' => 'New Contact Lead: ' . $validated['name'],
            'details' => $validated
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Your project details have been captured successfully. Our team will contact you shortly!']);
        }

        return back()->with('success', 'Your inquiry has been submitted successfully. A technology specialist will contact you shortly.');
    }

    public function careers()
    {
        $jobs = [
            [
                'title' => 'Senior Laravel Backend Engineer',
                'department' => 'Engineering',
                'location' => 'Lagos, Nigeria / Hybrid',
                'type' => 'Full-time',
                'description' => 'Join our core engineering team to build scalable multi-tenant SaaS architectures and robust exam platforms. Experience with Laravel, Redis, and MySQL optimization required.',
            ],
            [
                'title' => 'AI Integration Specialist',
                'department' => 'Artificial Intelligence',
                'location' => 'Remote',
                'type' => 'Full-time',
                'description' => 'Finetune models, implement retrieval-augmented generation (RAG) pipelines, and integrate semantic vector databases into our corporate systems.',
            ],
            [
                'title' => 'Infrastructure & DevOps Lead',
                'department' => 'Operations',
                'location' => 'Lagos, Nigeria / On-site',
                'type' => 'Full-time',
                'description' => 'Oversee continuous deployment pipelines, optimize AWS/Azure cloud nodes, and manage local-area offline sync architectures for high-capacity CBT centers.',
            ]
        ];

        return view('pages.careers', compact('jobs'));
    }

    public function serviceDetail($slug)
    {
        $services = [
            'web-development' => [
                'title' => 'Web Development',
                'subtitle' => 'High-Performance Web Applications',
                'description' => 'We design and build bespoke, high-performance web applications tailored to solve complex business problems. Leveraging modern frameworks like Laravel, Vue, React, and Tailwind CSS, we ensure your web solutions are scalable, secure, and visually stunning.',
                'icon' => '🌐',
                'features' => [
                    'Custom Web App Architecture',
                    'Single Page Applications (SPAs)',
                    'API Development & Integration',
                    'Headless CMS & Serverless Setups',
                ],
                'tech_stack' => ['Laravel', 'Vue.js', 'React.js', 'Tailwind CSS', 'MySQL', 'Redis'],
            ],
            'mobile-apps' => [
                'title' => 'Mobile Apps',
                'subtitle' => 'Native & Cross-Platform Mobile Solutions',
                'description' => 'Transform your ideas into feature-rich mobile applications. We build high-performance native (iOS & Android) and hybrid mobile apps that offer smooth performance, intuitive user interfaces, and robust offline capabilities.',
                'icon' => '📱',
                'features' => [
                    'iOS & Android Native Development',
                    'Flutter & React Native Cross-Platform Apps',
                    'Real-Time Synchronization',
                    'Secure Mobile Payment Integration',
                ],
                'tech_stack' => ['Swift', 'Kotlin', 'Flutter', 'React Native', 'Firebase', 'SQLite'],
            ],
            'enterprise-saas' => [
                'title' => 'Enterprise SaaS',
                'subtitle' => 'Scalable Multi-Tenant Platforms',
                'description' => 'Build and scale your Software-as-a-Service platforms with our specialized multi-tenant engineering expertise. We handle everything from secure billing and subscription tiers to automated tenant provisioning and custom database isolation models.',
                'icon' => '🏢',
                'features' => [
                    'Multi-Tenant Database Architectures',
                    'Stripe & Recurly Billing Gateways',
                    'Granular RBAC Permissions',
                    'Scalable Core Infrastructure',
                ],
                'tech_stack' => ['Laravel Multitenancy', 'Docker', 'AWS', 'PostgreSQL', 'Stripe API'],
            ],
            'ai-automation' => [
                'title' => 'AI & Automation',
                'subtitle' => 'Neural Integrations & Intelligent Systems',
                'description' => 'Supercharge your operations by integrating state-of-the-art AI models. From custom LLM fine-tuning and retrieval-augmented generation (RAG) to intelligent workflow automation and OCR document processing, we bring intelligence to your software.',
                'icon' => '🤖',
                'features' => [
                    'Large Language Model (LLM) Integration',
                    'RAG & Semantic Vector Search',
                    'Intelligent Automated Agents',
                    'Neural Document & Image Processing',
                ],
                'tech_stack' => ['OpenAI API', 'Python', 'LangChain', 'Pinecone', 'HuggingFace', 'FastAPI'],
            ],
            'cbt-infrastructure' => [
                'title' => 'CBT Infrastructure',
                'subtitle' => 'Secure, Lock-Down Exam Assessment Environments',
                'description' => 'We engineer secure Computer-Based Testing (CBT) ecosystems designed to resist cheating, network drops, and system failures. Features offline synchronization, candidate browser locking, live exam session monitoring, and biometric authorization.',
                'icon' => '📝',
                'features' => [
                    'Offline-Capable Local Synchronization Engines',
                    'Secure Locked-Down Exam Browsers',
                    'Candidate Biometric Authentication',
                    'Real-Time Invigilation & Proctoring Dashboards',
                ],
                'tech_stack' => ['Node.js', 'Laravel', 'Electron', 'SQLite', 'WebRTC', 'Redis'],
            ],
            'cloud-devops' => [
                'title' => 'Cloud & DevOps',
                'subtitle' => 'Zero-Downtime Infrastructure & CI/CD Pipelines',
                'description' => 'Scale your operations securely with modern Cloud Engineering. We deploy resilient, auto-scaling architectures on AWS and Azure, automate deployments with CI/CD pipelines, and implement comprehensive monitoring and security audits.',
                'icon' => '☁️',
                'features' => [
                    'Auto-Scaling & Load Balancing Orchestration',
                    'Docker & Kubernetes Containerization',
                    'GitHub Actions & GitLab CI/CD Automation',
                    '24/7 Security & Infrastructure Monitoring',
                ],
                'tech_stack' => ['AWS', 'Azure', 'Docker', 'Kubernetes', 'Terraform', 'GitHub Actions'],
            ],
            'cybersecurity' => [
                'title' => 'Cybersecurity',
                'subtitle' => 'Zero-Trust Defenses & Security Audits',
                'description' => 'Secure your company assets with zero-trust architecture. We run penetration testing, security auditing, and deploy hardware-backed passkeys, multi-factor authentication (MFA/2FA), and secure encrypted vaults for enterprise secrets.',
                'icon' => '🛡️',
                'features' => [
                    'Penetration Testing & Vulnerability Assessment',
                    'Hardware-backed Passkeys & WebAuthn',
                    'Advanced End-to-End Encryption Protocols',
                    'Intrusion Detection & Threat Mitigation',
                ],
                'tech_stack' => ['WebAuthn', 'AES-256', 'OpenSSL', 'Vault', 'WAF', 'Cloudflare'],
            ],
            'workflow-automation' => [
                'title' => 'Workflow Automation',
                'subtitle' => 'Eliminate Manual Overhead with Custom Integration',
                'description' => 'Connect your corporate systems and remove human bottlenecks. We design automated triggers and workflows to handle leads, billing, client updates, and internal notifications, making your operations fast and error-free.',
                'icon' => '⚡',
                'features' => [
                    'API-First Integration Hubs',
                    'Real-Time Background Job Processing',
                    'Automated Billing & Reporting',
                    'CRM & Project Management Syncing',
                ],
                'tech_stack' => ['Laravel Horizon', 'Redis', 'Zapier APIs', 'Webhooks', 'OAuth 2.0'],
            ],
        ];

        if (!array_key_exists($slug, $services)) {
            abort(404);
        }

        $service = $services[$slug];
        return view('pages.service-detail', compact('service'));
    }

    public function legal($slug)
    {
        $policies = [
            'privacy-policy' => [
                'title' => 'Privacy Policy',
                'last_updated' => 'June 13, 2026',
                'sections' => [
                    '1. Information Collection' => 'We collect personal information that you provide to us, such as name, email address, and company details when subscribing to our newsletter or requesting inquiries.',
                    '2. Data Usage' => 'Your information is used strictly to deliver requested services, maintain platform security, and analyze website engagement to improve our client workspaces.',
                    '3. Data Protection' => 'We deploy state-of-the-art security measures including AES-256 encryption, TLS connections, and regular third-party audits to prevent unauthorized data access.',
                    '4. Cookie Consent' => 'We use analytical and essential cookies to optimize layout preferences. You can adjust your choices in the Cookie Settings panel.'
                ]
            ],
            'terms-of-service' => [
                'title' => 'Terms of Service',
                'last_updated' => 'June 13, 2026',
                'sections' => [
                    '1. Acceptance of Terms' => 'By accessing our portals, dashboards, or services, you agree to comply with and be bound by these legal terms.',
                    '2. Acceptable Use' => 'You agree not to use our networks or Computer-Based Testing systems for unauthorized vulnerability probes or cheating.',
                    '3. IP Rights' => 'All software modules, templates, designs, and content developed by Diwebs Tech remain the intellectual property of Diwebs Tech Agency.',
                    '4. Limitation of Liability' => 'Diwebs Tech is not liable for indirect or consequential damages arising from network interruptions or service maintenance.'
                ]
            ],
            'cookie-settings' => [
                'title' => 'Cookie Policy & Settings',
                'last_updated' => 'June 13, 2026',
                'sections' => [
                    '1. Types of Cookies' => 'We utilize essential session tokens (for authentication) and analytical trackers (to optimize performance).',
                    '2. Custom Settings' => 'You can toggle cookie configurations directly in your web browser settings. Disabling functional cookies may affect dashboard performance.',
                    '3. Privacy Guarantee' => 'No tracking cookies are shared with third-party advertising companies or marketing profiles.'
                ]
            ],
            'platform-security' => [
                'title' => 'Platform Security Standards',
                'last_updated' => 'June 13, 2026',
                'sections' => [
                    '1. Cryptographic Safeguards' => 'All communications are protected via TLS 1.3. User credentials and secrets are encrypted with bcrypt and secure salt hashes.',
                    '2. Access Control' => 'Super Admins and clients utilize mandatory WebAuthn passkeys or 2FA OTP codes for all database actions.',
                    '3. Penetration Audits' => 'Our secure gate logins are routinely inspected via automated vulnerability scanning tools to guarantee Zero Trust standards.'
                ]
            ]
        ];

        if (!array_key_exists($slug, $policies)) {
            abort(404);
        }

        $policy = $policies[$slug];
        return view('pages.legal', compact('policy'));
    }
}

