<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BIA SYSTEM // GLOBAL_INTELLIGENCE')</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Mono:ital,wght@0,300;0,400;0,500;1,300&family=Syne:wght@300;400;700;800&display=swap" rel="stylesheet">

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Syne', 'sans-serif'],
                        display: ['Bebas Neue', 'sans-serif'],
                        mono: ['DM Mono', 'monospace'],
                    },
                    colors: {
                        primary: {
                            50: '#f0f9f6',
                            100: '#d1f0e4',
                            500: '#00FF88', /* Neon Green */
                            600: '#00E5FF', /* Cyan */
                            700: '#0a4939',
                        },
                        ink: {
                            DEFAULT: '#03060D',
                            light: '#070C18',
                            dark: '#010204',
                        }
                    }
                }
            }
        }
    </script>

    {{-- Global CSS --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    @stack('styles')
</head>
<body class="bg-main antialiased overflow-x-hidden">

    {{-- Éléments de fond fixes --}}
    <canvas id="bg-canvas"></canvas>
    <div class="bg-grid"></div>
    <div class="bg-noise"></div>
    <div class="scan-line"></div>

    {{-- Navigation --}}
    <nav class="bia-nav" id="main-nav">
        <div class="flex items-center gap-8">
            <a href="{{ route('analysis.index') }}" class="nav-logo">
                <div class="nav-logo-dot"></div>
                BIA <span class="opacity-40 font-light">SYSTEM</span>
            </a>

            <div class="hidden lg:flex items-center gap-2">
                <a href="{{ route('analysis.index') }}" class="nav-link {{ request()->routeIs('analysis.index') ? 'active' : '' }}">Analyses</a>
                <a href="{{ route('subscription.index') }}" class="nav-link {{ request()->routeIs('subscription.index') ? 'active' : '' }}">Tarifs</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">Mon Espace</a>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="nav-link">Admin</a>
                    @endif
                @endauth
            </div>
        </div>

        <div class="flex items-center gap-6">
            {{-- Langue --}}
            <div class="hidden md:flex gap-3">
                @foreach(['fr' => 'FR', 'en' => 'EN'] as $code => $label)
                <form method="POST" action="{{ route('langue.changer', $code) }}">
                    @csrf
                    <button type="submit" class="font-mono text-[10px] tracking-widest {{ app()->getLocale() === $code ? 'text-primary-500' : 'text-muted hover:text-white' }} transition">
                        [{{ $label }}]
                    </button>
                </form>
                @endforeach
            </div>

            @auth
                @php $plan = config('plans.' . auth()->user()->plan) @endphp
                <div class="flex items-center gap-4">
                    <span class="nav-plan-badge">{{ $plan['label'] ?? 'Free' }}</span>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="font-mono text-[10px] text-red-400 hover:text-red-500 transition tracking-widest">
                            [LOGOUT]
                        </button>
                    </form>
                </div>
            @else
                <div class="flex items-center gap-4">
                    <a href="{{ route('login') }}" class="nav-link">Connexion</a>
                    <a href="{{ route('register') }}" class="nav-cta">S'INSCRIRE</a>
                </div>
            @endauth
        </div>
    </nav>

    <main class="relative z-10 max-w-7xl mx-auto px-6 py-12">
        @if(session('message'))
        <div class="anim-fade-up mb-10 p-5 bg-dim border border-border text-primary-500 rounded-lg flex items-center gap-4">
            <div class="w-8 h-8 rounded bg-primary-500/10 flex items-center justify-center font-mono text-xs">!</div>
            <span class="font-mono text-xs tracking-wide uppercase">{{ session('message') }}</span>
        </div>
        @endif

        @yield('content')
    </main>

    <footer class="relative z-10 mt-20 border-t border-bord2 py-12 text-center">
        <div class="max-w-7xl mx-auto px-6">
            <div class="font-display text-2xl text-gradient mb-4">BIA SYSTEM</div>
            <p class="font-mono text-[9px] text-muted tracking-[0.2em] uppercase">
                © {{ date('Y') }} BIA SYSTEM GLOBAL — Propulsé par Deep Reasoning Models
            </p>
        </div>
    </footer>

    {{-- Scripts Globaux --}}
    <script>
        // ── Canvas Background ──
        function initBgCanvas(canvasId = 'bg-canvas') {
            const cvs = document.getElementById(canvasId);
            if (!cvs) return;
            const ctx = cvs.getContext('2d');
            let W, H, pts = [], cols = [];

            function resize() {
                W = cvs.width = window.innerWidth;
                H = cvs.height = window.innerHeight;
            }

            function Particle() { this.reset(); }
            Particle.prototype.reset = function () {
                this.x  = Math.random() * W;
                this.y  = Math.random() * H;
                this.r  = Math.random() * 0.9 + 0.15;
                this.vx = (Math.random() - 0.5) * 0.22;
                this.vy = (Math.random() - 0.5) * 0.22;
                this.a  = Math.random() * 0.35 + 0.06;
                this.c  = Math.random() > 0.65 ? '#00E5FF' : '#00FF88';
            };
            Particle.prototype.tick = function () {
                this.x += this.vx; this.y += this.vy;
                if (this.x < 0 || this.x > W || this.y < 0 || this.y > H) this.reset();
            };
            Particle.prototype.draw = function () {
                ctx.globalAlpha = this.a;
                ctx.fillStyle = this.c;
                ctx.beginPath(); ctx.arc(this.x, this.y, this.r, 0, Math.PI * 2); ctx.fill();
            };

            function makeCols() {
                cols = [];
                const n = Math.floor(W / 52);
                for (let i = 0; i < n; i++) {
                    cols.push({
                        x: i * 52 + Math.random() * 26,
                        y: Math.random() * H,
                        spd: Math.random() * 0.45 + 0.12,
                        chars: Array.from({ length: 8 }, () => Math.random().toString(36)[2]),
                        op: Math.random() * 0.055 + 0.012,
                    });
                }
            }

            function drawConnections() {
                for (let i = 0; i < pts.length; i++) {
                    for (let j = i + 1; j < pts.length; j++) {
                        const dx = pts[i].x - pts[j].x, dy = pts[i].y - pts[j].y;
                        const d = Math.sqrt(dx * dx + dy * dy);
                        if (d < 130) {
                            ctx.globalAlpha = (1 - d / 130) * 0.035;
                            ctx.strokeStyle = '#00FF88';
                            ctx.lineWidth = 0.5;
                            ctx.beginPath(); ctx.moveTo(pts[i].x, pts[i].y);
                            ctx.lineTo(pts[j].x, pts[j].y); ctx.stroke();
                        }
                    }
                }
            }

            function drawCols() {
                ctx.font = '10px DM Mono, monospace';
                cols.forEach(c => {
                    c.chars.forEach((ch, i) => {
                        ctx.globalAlpha = c.op * (1 - i / c.chars.length);
                        ctx.fillStyle = '#00FF88';
                        ctx.fillText(ch, c.x, c.y - i * 12);
                    });
                    c.y += c.spd;
                    if (c.y > H + 90) { c.y = -80; c.x = Math.random() * W; }
                    if (Math.random() < 0.022) c.chars[0] = Math.random().toString(36)[2];
                });
            }

            function loop() {
                ctx.clearRect(0, 0, W, H);
                drawCols();
                drawConnections();
                pts.forEach(p => { p.tick(); p.draw(); });
                ctx.globalAlpha = 1;
                requestAnimationFrame(loop);
            }

            resize(); makeCols();
            for (let i = 0; i < 55; i++) pts.push(new Particle());
            window.addEventListener('resize', () => { resize(); makeCols(); });
            loop();
        }

        // ── Nav Scroll Effect ──
        function initNavScroll(navId = 'main-nav') {
            const nav = document.getElementById(navId);
            if (!nav) return;
            window.addEventListener('scroll', () => {
                nav.classList.toggle('scrolled', window.scrollY > 40);
            }, { passive: true });
        }

        // ── Scroll Reveal ──
        function initScrollReveal() {
            const obs = new IntersectionObserver(entries => {
                entries.forEach(e => {
                    if (e.isIntersecting) {
                        e.target.classList.add('visible');
                        obs.unobserve(e.target);
                    }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

            document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale')
                .forEach(el => obs.observe(el));
        }

        // ── Navigation par Onglets (BIA Terminal) ──
        function tab(id) {
            // Boutons
            document.querySelectorAll('.t-btn').forEach(btn => {
                btn.classList.toggle('on', btn.dataset.t === id);
                if (btn.dataset.t === id) {
                    btn.classList.add('bg-primary-500/5', 'text-primary-500', 'border-primary-500');
                    btn.classList.remove('text-muted', 'border-transparent');
                } else {
                    btn.classList.remove('bg-primary-500/5', 'text-primary-500', 'border-primary-500');
                    btn.classList.add('text-muted', 'border-transparent');
                }
            });

            // Panneaux
            document.querySelectorAll('.panel').forEach(p => {
                if (p.id === 'p-' + id) {
                    p.classList.remove('hidden');
                    p.classList.add('on');
                } else {
                    p.classList.add('hidden');
                    p.classList.remove('on');
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            initBgCanvas('bg-canvas');
            initNavScroll('main-nav');
            initScrollReveal();
        });
    </script>
    @stack('scripts')
</body>
</html>
