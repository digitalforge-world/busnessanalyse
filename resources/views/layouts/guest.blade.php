<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'BIA SYSTEM'))</title>

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
                            500: '#00FF88',
                            600: '#00E5FF',
                        },
                        ink: {
                            DEFAULT: '#03060D',
                            light: '#070C18',
                        }
                    }
                }
            }
        }
    </script>

    {{-- Global CSS --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body class="bg-main antialiased overflow-x-hidden min-h-screen">
    
    {{-- Éléments de fond fixes --}}
    <canvas id="bg-canvas"></canvas>
    <div class="bg-grid"></div>
    <div class="bg-noise"></div>
    <div class="scan-line"></div>

    <div class="relative z-10">
        {{ $slot }}
    </div>

    {{-- Scripts Globaux --}}
    <script>
        function initBgCanvas(canvasId = 'bg-canvas') {
            const cvs = document.getElementById(canvasId);
            if (!cvs) return;
            const ctx = cvs.getContext('2d');
            let W, H, pts = [], cols = [];
            function resize() { W = cvs.width = window.innerWidth; H = cvs.height = window.innerHeight; }
            function Particle() { this.reset(); }
            Particle.prototype.reset = function () {
                this.x = Math.random() * W; this.y = Math.random() * H;
                this.r = Math.random() * 0.9 + 0.15;
                this.vx = (Math.random() - 0.5) * 0.22; this.vy = (Math.random() - 0.5) * 0.22;
                this.a = Math.random() * 0.35 + 0.06; this.c = Math.random() > 0.65 ? '#00E5FF' : '#00FF88';
            };
            Particle.prototype.tick = function () { this.x += this.vx; this.y += this.vy; if (this.x < 0 || this.x > W || this.y < 0 || this.y > H) this.reset(); };
            Particle.prototype.draw = function () { ctx.globalAlpha = this.a; ctx.fillStyle = this.c; ctx.beginPath(); ctx.arc(this.x, this.y, this.r, 0, Math.PI * 2); ctx.fill(); };
            function makeCols() {
                cols = []; const n = Math.floor(W / 52);
                for (let i = 0; i < n; i++) cols.push({ x: i * 52 + Math.random() * 26, y: Math.random() * H, spd: Math.random() * 0.45 + 0.12, chars: Array.from({ length: 8 }, () => Math.random().toString(36)[2]), op: Math.random() * 0.055 + 0.012 });
            }
            function drawConnections() {
                for (let i = 0; i < pts.length; i++) {
                    for (let j = i + 1; j < pts.length; j++) {
                        const dx = pts[i].x - pts[j].x, dy = pts[i].y - pts[j].y, d = Math.sqrt(dx * dx + dy * dy);
                        if (d < 130) { ctx.globalAlpha = (1 - d / 130) * 0.035; ctx.strokeStyle = '#00FF88'; ctx.lineWidth = 0.5; ctx.beginPath(); ctx.moveTo(pts[i].x, pts[i].y); ctx.lineTo(pts[j].x, pts[j].y); ctx.stroke(); }
                    }
                }
            }
            function drawCols() {
                ctx.font = '10px DM Mono, monospace';
                cols.forEach(c => {
                    c.chars.forEach((ch, i) => { ctx.globalAlpha = c.op * (1 - i / c.chars.length); ctx.fillStyle = '#00FF88'; ctx.fillText(ch, c.x, c.y - i * 12); });
                    c.y += c.spd; if (c.y > H + 90) { c.y = -80; c.x = Math.random() * W; }
                    if (Math.random() < 0.022) c.chars[0] = Math.random().toString(36)[2];
                });
            }
            function loop() { ctx.clearRect(0, 0, W, H); drawCols(); drawConnections(); pts.forEach(p => { p.tick(); p.draw(); }); ctx.globalAlpha = 1; requestAnimationFrame(loop); }
            resize(); makeCols(); for (let i = 0; i < 55; i++) pts.push(new Particle());
            window.addEventListener('resize', () => { resize(); makeCols(); }); loop();
        }
        document.addEventListener('DOMContentLoaded', () => { initBgCanvas(); });
    </script>
</body>
</html>
