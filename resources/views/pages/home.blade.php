@extends('layouts.app')

@section('title', 'Diwebs Tech Agency - Engineering Tomorrow With AI, Software & Infrastructure')

@section('content')
<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

@php
    $lowBandwidth = request()->query('low_bandwidth') || config('app.low_bandwidth', false);
    
    // Choose images & labels based on bandwidth mode
    if ($lowBandwidth) {
        $slides = [
            [
                'image' => 'https://images.openai.com/static-rsc-4/hELpAqfbM338eEHx_n8cFSgh-FpK3ARCuy3Az2jUUOjnZ2y9bw7dY7lPYAgHM6nP3-Gc-KTt3mdZrzs0NBLq3IevhcLf4CXCxrmeY4fQEJ0xER97UUgoYZxWCOy1mIengJFwgm3h-AfGCaa-UBprPdCUylA8d-7KWzRz95Ne0EyWqUxtfpDidev_eiX2Qr8X?purpose=fullsize',
                'label' => 'Enterprise Software Development'
            ],
            [
                'image' => 'https://images.openai.com/static-rsc-4/zcP6Cw1qwBPzsOv1r6WX2MUsXDYKZUuSNCZ91tnFA0F5-SHG7JbY08Itp33-Z7YHQQnFqyHYADWbTGRbKDyNYTChTv15zd5ysIK707a-OxM20_RwPWpR8d-7r19vKaZTtc3QuO8p29A2w083jWE1vxSqfL9hJWdFea8lpyn7VAcFlHJM7kwruilbRaQT4uOn?purpose=fullsize',
                'label' => 'Artificial Intelligence & Automation'
            ],
            [
                'image' => 'https://images.openai.com/static-rsc-4/G2ZgLD4BXO6lqhFXDs0qvCpEl8qfQGKEvizk1NZuxYqiQiBz6MV9M0pC2pmtr1fyGuPBi3y3roGu1_gflpaIsEwTULWGD3sltmIca3Pncec_WMbwlK7kXmN7yOH1-MFty6Uzc3MKEF3cOSwuQHAHXtFXalTYg8isz1Eie917zyoFD9lOe7eEq95Wu5a7JGRD?purpose=fullsize',
                'label' => 'Cybersecurity & Threat Intelligence'
            ]
        ];
    } else {
        $slides = [
            [
                'image' => 'https://images.openai.com/static-rsc-4/hELpAqfbM338eEHx_n8cFSgh-FpK3ARCuy3Az2jUUOjnZ2y9bw7dY7lPYAgHM6nP3-Gc-KTt3mdZrzs0NBLq3IevhcLf4CXCxrmeY4fQEJ0xER97UUgoYZxWCOy1mIengJFwgm3h-AfGCaa-UBprPdCUylA8d-7KWzRz95Ne0EyWqUxtfpDidev_eiX2Qr8X?purpose=fullsize',
                'label' => 'Enterprise Software Development'
            ],
            [
                'image' => 'https://images.openai.com/static-rsc-4/zcP6Cw1qwBPzsOv1r6WX2MUsXDYKZUuSNCZ91tnFA0F5-SHG7JbY08Itp33-Z7YHQQnFqyHYADWbTGRbKDyNYTChTv15zd5ysIK707a-OxM20_RwPWpR8d-7r19vKaZTtc3QuO8p29A2w083jWE1vxSqfL9hJWdFea8lpyn7VAcFlHJM7kwruilbRaQT4uOn?purpose=fullsize',
                'label' => 'Artificial Intelligence & Automation'
            ],
            [
                'image' => 'https://images.openai.com/static-rsc-4/yKJ4giKxduRdn_Ekp8ExKdoLEiqekterRebUqVrTZiiuAQjcztp1dBCTBX1XhWUDaO7qgTl9iZfw4w5QCpaWwQAkOANWRfcvcObg9iD-iyiW1uB4tNgHvnkNaGmqazwJeigTR73nlGGiw25bOlA5V3Jkmo0Co4rkGwZzXLcssmQyuXoLTbnxCQ_rHytuSv3e?purpose=fullsize',
                'label' => 'CBT Infrastructure & Digital Examinations'
            ],
            [
                'image' => 'https://images.openai.com/static-rsc-4/G2ZgLD4BXO6lqhFXDs0qvCpEl8qfQGKEvizk1NZuxYqiQiBz6MV9M0pC2pmtr1fyGuPBi3y3roGu1_gflpaIsEwTULWGD3sltmIca3Pncec_WMbwlK7kXmN7yOH1-MFty6Uzc3MKEF3cOSwuQHAHXtFXalTYg8isz1Eie917zyoFD9lOe7eEq95Wu5a7JGRD?purpose=fullsize',
                'label' => 'Cybersecurity & Threat Intelligence'
            ],
            [
                'image' => 'https://images.openai.com/static-rsc-4/l3H0GPFJ6bD_dBLgMze2I9ntFqfQUHN4qhBPOLjMMPXP-XArP-qrxxw-WjzKuBymcoTkUN8spvPj5DsFhodUHNP4Fq6j4nPqrn_ed6lK8cc9TJDsL724MHzz4dDTyKTfsD7CI-bKtIU-8eeMozqD3VOEjdueIzemd6Kt5m6IjkHcIqgWAYoFeQwfyAviuJXH?purpose=fullsize',
                'label' => 'Cloud & Infrastructure Engineering'
            ],
            [
                'image' => 'https://images.openai.com/static-rsc-4/ONRnjRrmj4VmxyYzpMW0VZ8NiWfkPPPnqj4PcGVArA3ZHKQXeNAuC3iC6he22qaZ2nejD98bGGmywtg6JtGdQ0OjncmpJbX6e3MdKkAb9SUJG49_8o2m1OUJTqcw1pOMSBwlJ74Ram_5Pp3zo2y4tWL8Vph5MPFtfVJU0cOX0fiDlvWwMC7p2PGAOB7fhhCL?purpose=fullsize',
                'label' => 'Technology Training & Certification'
            ],
            [
                'image' => 'https://images.openai.com/static-rsc-4/_Q6VEYnTqptgupRh2bS5YdD6f8Ji_7cFRzCJ0y82jSEJJ8zIE-mBsT1qxGrYWIqvhUX_KYXdoDiu33CvUPB107eXFocebuWQ24cMQoYDrdR4LEQdFQr_fxjZmJPyFs01X9noQXkBuoo66sKzH9XR9SnStg7ip7bweXD0qYIieYxV8EfgpfiqwY5KnQZ4gaLj?purpose=fullsize',
                'label' => 'Building Africa’s Global Innovation Hub'
            ]
        ];
    }
@endphp

<style>
    /* Fullscreen background slider container */
    .hero-slider-wrapper {
        position: absolute;
        top: -40px;
        left: 0;
        width: 100%;
        height: calc(100vh - 64px);
        min-height: 700px;
        z-index: -20;
        overflow: hidden;
    }
    
    .hero-swiper {
        width: 100%;
        height: 100%;
    }

    /* Cinematic layered gradient overlay */
    .slider-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(
            135deg,
            rgba(8, 10, 15, 0.82),
            rgba(0, 151, 167, 0.35)
        );
        z-index: 2;
    }

    /* Ken Burns zoom effect */
    @keyframes kenburns {
        0% {
            transform: scale(1.02);
        }
        100% {
            transform: scale(1.08);
        }
    }

    .swiper-slide-active .hero-slider-img {
        animation: kenburns 8s ease-out forwards;
    }

    /* Floating Cinematic Label Tag */
    .slide-info-tag {
        position: absolute;
        bottom: 24px;
        left: 24px;
        z-index: 10;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border-radius: 9999px;
        background: rgba(30, 33, 37, 0.65);
        border: 1px solid rgba(0, 151, 167, 0.25);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        pointer-events: auto;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }
    
    /* Ensure slider wrapper image elements are fully stretched and display preloader cleanly */
    .hero-slider-img-wrap {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        transform: scale(1.08);
        will-change: transform;
    }

    .hero-slider-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0;
        transition: opacity 0.6s ease-in-out;
    }

    .hero-slider-img.swiper-lazy-loaded {
        opacity: 1;
    }
</style>

<div class="relative isolate overflow-hidden">
    <!-- Cinematic Fullscreen Background Slider -->
    <div class="hero-slider-wrapper">
        <div class="swiper hero-swiper">
            <div class="swiper-wrapper">
                @foreach($slides as $index => $slide)
                    <div class="swiper-slide" data-label="{{ $slide['label'] }}">
                        <div class="hero-slider-img-wrap">
                            <!-- Eager-load the first slide to prevent LCP issue; lazy-load the rest -->
                            @if($index === 0)
                                <img src="{{ $slide['image'] }}" class="hero-slider-img swiper-lazy-loaded" style="opacity: 1;" alt="{{ $slide['label'] }}">
                            @else
                                <img data-src="{{ $slide['image'] }}" class="swiper-lazy hero-slider-img" alt="{{ $slide['label'] }}">
                                <div class="swiper-lazy-preloader swiper-lazy-preloader-white"></div>
                            @endif
                        </div>
                        <div class="slider-overlay"></div>
                    </div>
                @endforeach
            </div>
            
            <!-- Custom Particles Canvas inside the background container -->
            <canvas id="hero-particles" class="absolute inset-0 z-3 pointer-events-none"></canvas>

            <!-- Floating tag showcasing current active slide capabilities -->
            <div class="slide-info-tag select-none">
                <span class="h-2 w-2 rounded-full bg-brand-cyan animate-pulse"></span>
                <span id="active-slide-label" class="text-xs font-semibold text-brand-white tracking-wider uppercase opacity-85 transition-opacity duration-300">
                    {{ $slides[0]['label'] }}
                </span>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <div class="mx-auto max-w-7xl px-6 pb-24 pt-10 sm:pb-32 lg:px-8 lg:pt-20">
        <div class="mx-auto max-w-3xl text-center">
            <!-- Neon Glowing Badge -->
            <div class="inline-flex items-center gap-x-2 rounded-full bg-brand-teal/10 px-4 py-1.5 text-sm font-medium text-brand-cyan border border-brand-teal/20 mb-8 animate-pulse">
                <span>Enterprise Technology Partner</span>
            </div>
            
            <h1 class="text-4xl font-extrabold tracking-tight text-brand-white sm:text-6xl text-glow bg-gradient-to-r from-brand-white via-brand-gray to-brand-cyan bg-clip-text text-transparent leading-none">
                Engineering Tomorrow With AI, Software & Digital Infrastructure
            </h1>
            <p class="mt-6 text-lg leading-8 text-brand-gray max-w-2xl mx-auto">
                Diwebs Tech Agency builds world-class software, enterprise platforms, CBT infrastructure, AI systems, and digital training ecosystems for businesses, institutions, and governments.
            </p>
            <div class="mt-10 flex items-center justify-center gap-x-6">
                <a href="#contact-section" class="rounded-md bg-gradient-to-r from-brand-teal to-brand-cyan px-6 py-3 text-sm font-semibold text-brand-dark-secondary shadow-md hover:opacity-90 transition-all font-bold">Start Project</a>
                <a href="{{ route('services') }}" class="text-sm font-semibold leading-6 text-brand-white hover:text-brand-cyan transition-all flex items-center gap-1">Explore Solutions <span aria-hidden="true">→</span></a>
            </div>
        </div>
    </div>

    <!-- Live Interactive Metrics Dashboard Section -->
    <div class="mx-auto max-w-7xl px-6 lg:px-8 mb-24">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <!-- Metric Card 1 -->
            <div class="glass-card glass-card-hover rounded-2xl p-6 text-center">
                <dt class="text-xs font-semibold text-brand-cyan uppercase tracking-wider">Projects Delivered</dt>
                <dd class="mt-2 text-3xl font-extrabold text-brand-white tracking-tight">120+</dd>
                <div class="mt-1 text-[10px] text-emerald-400">99.8% Success Rate</div>
            </div>
            <!-- Metric Card 2 -->
            <div class="glass-card glass-card-hover rounded-2xl p-6 text-center">
                <dt class="text-xs font-semibold text-brand-cyan uppercase tracking-wider">Students Trained</dt>
                <dd class="mt-2 text-3xl font-extrabold text-brand-white tracking-tight">15,000+</dd>
                <div class="mt-1 text-[10px] text-brand-cyan">Global Academy Partners</div>
            </div>
            <!-- Metric Card 3 -->
            <div class="glass-card glass-card-hover rounded-2xl p-6 text-center">
                <dt class="text-xs font-semibold text-brand-cyan uppercase tracking-wider">CBT Centers Powered</dt>
                <dd class="mt-2 text-3xl font-extrabold text-brand-white tracking-tight">450+</dd>
                <div class="mt-1 text-[10px] text-emerald-400">5M+ Exams Completed</div>
            </div>
            <!-- Metric Card 4 -->
            <div class="glass-card glass-card-hover rounded-2xl p-6 text-center">
                <dt class="text-xs font-semibold text-brand-cyan uppercase tracking-wider">Countries Reached</dt>
                <dd class="mt-2 text-3xl font-extrabold text-brand-white tracking-tight">12+</dd>
                <div class="mt-1 text-[10px] text-brand-cyan">Cross-border Deployments</div>
            </div>
        </div>
    </div>

    <!-- Interactive Solutions / Grid List -->
    <div class="mx-auto max-w-7xl px-6 lg:px-8 mb-28">
        <div class="mx-auto max-w-2xl lg:text-center mb-16">
            <h2 class="text-base font-semibold text-brand-cyan uppercase tracking-wider">Ecosystem Architecture</h2>
            <p class="mt-2 text-3xl font-bold tracking-tight text-brand-white sm:text-4xl">
                Ecosystem Modules Designed for Global Scale
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Card 1 -->
            <div class="glass-card glass-card-hover rounded-2xl p-8 flex flex-col justify-between relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-brand-teal/5 rounded-full blur-xl"></div>
                <div>
                    <span class="text-2xl">⚡</span>
                    <h3 class="mt-4 text-xl font-bold text-brand-white">Enterprise Systems</h3>
                    <p class="mt-2 text-sm text-brand-gray">
                        High-availability distributed architectures, custom API layers, and multi-tenant SaaS infrastructure built on Laravel 12.
                    </p>
                </div>
                <div class="mt-6">
                    <a href="{{ route('services') }}" class="text-xs text-brand-cyan hover:underline font-medium">Read details →</a>
                </div>
            </div>
            <!-- Card 2 -->
            <div class="glass-card glass-card-hover rounded-2xl p-8 flex flex-col justify-between relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-brand-cyan/5 rounded-full blur-xl"></div>
                <div>
                    <span class="text-2xl">🧠</span>
                    <h3 class="mt-4 text-xl font-bold text-brand-white">AI Solutions & Agents</h3>
                    <p class="mt-2 text-sm text-brand-gray">
                        Cognitive automation pipelines, predictive analytics, intelligent chat assistance, and dynamic learning algorithms.
                    </p>
                </div>
                <div class="mt-6">
                    <a href="{{ route('services') }}" class="text-xs text-brand-cyan hover:underline font-medium">Read details →</a>
                </div>
            </div>
            <!-- Card 3 -->
            <div class="glass-card glass-card-hover rounded-2xl p-8 flex flex-col justify-between relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-brand-teal/5 rounded-full blur-xl"></div>
                <div>
                    <span class="text-2xl">📝</span>
                    <h3 class="mt-4 text-xl font-bold text-brand-white">CBT Exam Engine</h3>
                    <p class="mt-2 text-sm text-brand-gray">
                        Secure exam hosting with localized seat allocation, browser restriction locks, tab exit logs, and webcam facial verification.
                    </p>
                </div>
                <div class="mt-6">
                    <a href="{{ route('cbt.dashboard') }}" class="text-xs text-brand-cyan hover:underline font-medium">Test CBT Portal →</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Lead Intake CRM Form Section -->
    <div id="contact-section" class="mx-auto max-w-3xl px-6 lg:px-8 pb-12">
        <div class="glass-card rounded-3xl p-8 md:p-12 relative overflow-hidden">
            <div class="absolute inset-0 bg-dot-matrix opacity-40"></div>
            
            <div class="relative z-10 text-center mb-8">
                <h3 class="text-2xl font-bold text-brand-white">Initiate Your Digital Transformation</h3>
                <p class="mt-2 text-sm text-brand-gray">Provide project parameters, and our system architects will schedule an engineering workshop.</p>
            </div>

            <form action="{{ route('lead.submit') }}" method="POST" class="relative z-10 space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-xs font-semibold text-brand-cyan uppercase">Company Representative Name</label>
                        <input type="text" name="name" id="name" required class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-3 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                    </div>
                    <div>
                        <label for="email" class="block text-xs font-semibold text-brand-cyan uppercase">Corporate Email Address</label>
                        <input type="email" name="email" id="email" required class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-3 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="phone" class="block text-xs font-semibold text-brand-cyan uppercase">Direct Contact Number</label>
                        <input type="text" name="phone" id="phone" class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-3 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all" placeholder="+234...">
                    </div>
                    <div>
                        <label for="service_needed" class="block text-xs font-semibold text-brand-cyan uppercase">Required Specialization</label>
                        <select name="service_needed" id="service_needed" required class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-3 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                            <option value="Enterprise Systems & Web App">Enterprise Systems & Web App</option>
                            <option value="AI Cognitive Solutions">AI Cognitive Solutions</option>
                            <option value="CBT Infrastructure Deployment">CBT Infrastructure Deployment</option>
                            <option value="Cloud Migration & DevOps">Cloud Migration & DevOps</option>
                            <option value="Training Academy Partnership">Training Academy Partnership</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="message" class="block text-xs font-semibold text-brand-cyan uppercase">Project Objectives & Timeline</label>
                    <textarea name="message" id="message" rows="4" required class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-3 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all" placeholder="Briefly detail what you are looking to build..."></textarea>
                </div>

                <div class="text-center">
                    <button type="submit" class="w-full sm:w-auto rounded-md bg-gradient-to-r from-brand-teal to-brand-cyan px-8 py-3.5 text-sm font-bold text-brand-dark-secondary shadow-md hover:opacity-90 transition-all cursor-pointer">Submit Requirements</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Swiper JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Swiper Initialization
        const swiper = new Swiper('.hero-swiper', {
            loop: true,
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },
            autoplay: {
                delay: 7000, // Every 7 seconds
                disableOnInteraction: false,
            },
            speed: 1500, // 1.5s fade duration
            lazy: {
                loadPrevNext: true,
                loadPrevNextAmount: 1
            },
            on: {
                slideChange: function () {
                    const activeSlide = this.slides[this.activeIndex];
                    if (!activeSlide) return;
                    const label = activeSlide.getAttribute('data-label');
                    const labelEl = document.getElementById('active-slide-label');
                    if (labelEl && label) {
                        labelEl.classList.add('opacity-0');
                        setTimeout(() => {
                            labelEl.textContent = label;
                            labelEl.classList.remove('opacity-0');
                        }, 300);
                    }
                }
            }
        });

        // Subtly shift background wrappers depending on mouse movements (Subtle Cinematic Mouse Interaction)
        const sliderWrapper = document.querySelector('.hero-slider-wrapper');
        const imgWrappers = document.querySelectorAll('.hero-slider-img-wrap');

        if (sliderWrapper) {
            sliderWrapper.addEventListener('mousemove', (e) => {
                const rect = sliderWrapper.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                // Very subtle shift (-8px to 8px)
                const shiftX = ((x / rect.width) - 0.5) * 16;
                const shiftY = ((y / rect.height) - 0.5) * 16;

                imgWrappers.forEach(wrap => {
                    wrap.style.transform = `scale(1.08) translate(${shiftX}px, ${shiftY}px)`;
                });
            });

            sliderWrapper.addEventListener('mouseleave', () => {
                imgWrappers.forEach(wrap => {
                    wrap.style.transform = 'scale(1.08) translate(0px, 0px)';
                    wrap.style.transition = 'transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                });
                setTimeout(() => {
                    imgWrappers.forEach(wrap => {
                        wrap.style.transition = '';
                    });
                }, 800);
            });
        }

        // Particle System Canvas Above Background but below text content
        const canvas = document.getElementById('hero-particles');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            let particles = [];
            let width = canvas.width = canvas.offsetWidth;
            let height = canvas.height = canvas.offsetHeight;

            const resizeObserver = new ResizeObserver(entries => {
                for (let entry of entries) {
                    width = canvas.width = entry.contentRect.width;
                    height = canvas.height = entry.contentRect.height;
                    initParticles();
                }
            });
            resizeObserver.observe(canvas);

            let mouse = { x: null, y: null, radius: 180 };
            const heroSection = document.querySelector('.relative.isolate.overflow-hidden');
            if (heroSection) {
                heroSection.addEventListener('mousemove', (e) => {
                    const rect = canvas.getBoundingClientRect();
                    mouse.x = e.clientX - rect.left;
                    mouse.y = e.clientY - rect.top;
                });
                heroSection.addEventListener('mouseleave', () => {
                    mouse.x = null;
                    mouse.y = null;
                });
            }

            class Particle {
                constructor() {
                    this.reset();
                }
                reset() {
                    this.x = Math.random() * width;
                    this.y = Math.random() * height;
                    this.size = Math.random() * 2.5 + 1;
                    this.vx = (Math.random() - 0.5) * 0.4;
                    this.vy = (Math.random() - 0.5) * 0.4;
                    this.density = (Math.random() * 35) + 5;
                    const colors = [
                        'rgba(0, 194, 209, 0.35)', // Cyan
                        'rgba(0, 151, 167, 0.35)', // Teal
                        'rgba(248, 250, 252, 0.25)'  // Soft White
                    ];
                    this.color = colors[Math.floor(Math.random() * colors.length)];
                }
                draw() {
                    ctx.fillStyle = this.color;
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                    ctx.closePath();
                    ctx.fill();
                }
                update() {
                    this.x += this.vx;
                    this.y += this.vy;

                    if (this.x < 0 || this.x > width) this.vx = -this.vx;
                    if (this.y < 0 || this.y > height) this.vy = -this.vy;

                    // Mouse interactive deflection push
                    if (mouse.x !== null && mouse.y !== null) {
                        let dx = mouse.x - this.x;
                        let dy = mouse.y - this.y;
                        let distance = Math.sqrt(dx * dx + dy * dy);
                        if (distance < mouse.radius) {
                            let forceDirectionX = dx / distance;
                            let forceDirectionY = dy / distance;
                            let maxDistance = mouse.radius;
                            let force = (maxDistance - distance) / maxDistance;
                            let directionX = forceDirectionX * force * this.density * 0.3;
                            let directionY = forceDirectionY * force * this.density * 0.3;
                            this.x -= directionX;
                            this.y -= directionY;
                        }
                    }
                }
            }

            function initParticles() {
                particles = [];
                const numberOfParticles = Math.floor((width * height) / 12000);
                for (let i = 0; i < numberOfParticles; i++) {
                    particles.push(new Particle());
                }
            }

            function animateParticles() {
                ctx.clearRect(0, 0, width, height);
                for (let i = 0; i < particles.length; i++) {
                    particles[i].update();
                    particles[i].draw();
                }
                requestAnimationFrame(animateParticles);
            }

            initParticles();
            animateParticles();
        }
    });
</script>
@endsection
