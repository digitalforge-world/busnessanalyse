# BIA — Design System & Animations Guide
## Dark Luxury Terminal · Pro Max · Toutes pages

> Guide complet de style, animations et composants pour chaque page du site.
> Thème : **Dark Luxury Terminal** — fond quasi-noir, typographie monumentale, accents néon vert `#00FF88` + cyan `#00E5FF`, effets terminal/data, cursor personnalisé.

---

## Sommaire

1. [Tokens & Variables CSS](#1-tokens--variables-css)
2. [Typographie](#2-typographie)
3. [Composants globaux](#3-composants-globaux)
4. [Animations fondamentales](#4-animations-fondamentales)
5. [Page Login](#5-page-login)
6. [Page Register](#6-page-register)
7. [Page Analyse Index](#7-page-analyse-index)
8. [Page Show — Rapport complet](#8-page-show--rapport-complet)
9. [Dashboard Utilisateur](#9-dashboard-utilisateur)
10. [Dashboard Admin](#10-dashboard-admin)
11. [Page Abonnement / Pricing](#11-page-abonnement--pricing)
12. [Page PDF Preview](#12-page-pdf-preview)
13. [Layouts Blade](#13-layouts-blade)
14. [Utilitaires JS réutilisables](#14-utilitaires-js-réutilisables)

---

## 1. Tokens & Variables CSS

Coller dans le `<style>` global ou dans `app.css`. Toutes les pages héritent de ces variables.

```css
@import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Mono:ital,wght@0,300;0,400;0,500;1,300&family=Syne:wght@300;400;700;800&display=swap');

:root {
    /* ── Couleurs de fond ── */
    --ink:      #03060D;   /* fond principal */
    --ink2:     #070C18;   /* surfaces secondaires */
    --ink3:     #0D1424;   /* surfaces tertiaires */
    --ink4:     #131C2E;   /* hover léger */

    /* ── Accents néon ── */
    --neon:     #00FF88;   /* accent principal vert */
    --neon2:    #00E5FF;   /* accent secondaire cyan */
    --neon3:    #AAFF44;   /* neon hover */
    --amber:    #FFB800;   /* warning / priorité moyenne */
    --red:      #FF4D4D;   /* danger / priorité haute */
    --pink:     #FF6EB4;   /* accent décoratif */

    /* ── Transparences neon ── */
    --dim:      rgba(0, 255, 136, 0.08);
    --dim2:     rgba(0, 255, 136, 0.14);
    --dimc:     rgba(0, 229, 255, 0.08);
    --dima:     rgba(255, 184, 0, 0.08);
    --dimr:     rgba(255, 77, 77, 0.08);

    /* ── Bordures ── */
    --border:   rgba(0, 255, 136, 0.16);
    --borderc:  rgba(0, 229, 255, 0.14);
    --bord2:    rgba(255, 255, 255, 0.06);
    --bord3:    rgba(255, 255, 255, 0.04);

    /* ── Texte ── */
    --text:     #DDE6F0;   /* texte principal */
    --text2:    #A8B8CC;   /* texte secondaire */
    --muted:    #5A6B82;   /* texte muet */
    --muted2:   #2A3545;   /* bordures muettes */

    /* ── Effets ── */
    --glass:    rgba(255, 255, 255, 0.02);
    --glow-neon: 0 0 20px rgba(0, 255, 136, 0.35);
    --glow-cyan: 0 0 20px rgba(0, 229, 255, 0.35);

    /* ── Rayons ── */
    --r-sm:  2px;
    --r-md:  4px;
    --r-lg:  6px;
    --r-xl:  10px;

    /* ── Transitions ── */
    --t-fast:   150ms ease;
    --t-med:    250ms ease;
    --t-slow:   400ms ease;
    --t-spring: 350ms cubic-bezier(0.34, 1.56, 0.64, 1);
}
```

---

## 2. Typographie

```css
/* ── Display — titres heros & kpis ── */
.font-display {
    font-family: 'Bebas Neue', sans-serif;
    letter-spacing: 0.04em;
}

/* ── Corps — texte courant ── */
.font-body {
    font-family: 'Syne', sans-serif;
    font-weight: 300;
}

/* ── Mono — labels, badges, code, terminal ── */
.font-mono {
    font-family: 'DM Mono', monospace;
    letter-spacing: 0.04em;
}

/* ── Echelle typographique ── */
/* Hero H1 */          font-size: clamp(60px, 9vw, 118px); font-family: 'Bebas Neue'; line-height: 0.91;
/* Section H2 */       font-size: clamp(38px, 5vw, 68px);  font-family: 'Bebas Neue'; line-height: 0.93;
/* Card H3 */          font-size: 20px;                     font-family: 'Bebas Neue'; letter-spacing: 0.06em;
/* Body large */       font-size: 16px;  line-height: 1.8;  font-weight: 300;
/* Body regular */     font-size: 14px;  line-height: 1.75; color: var(--muted);
/* Label mono */       font-size: 10px;  letter-spacing: 0.14em; text-transform: uppercase; font-family: 'DM Mono';
/* KPI number */       font-family: 'Bebas Neue'; font-size: 40px; letter-spacing: 0.03em;
/* Badge / Tag */      font-family: 'DM Mono'; font-size: 9px; letter-spacing: 0.12em; text-transform: uppercase;

/* ── Effets texte ── */

/* Texte contour néon */
.text-stroke-neon {
    color: transparent;
    -webkit-text-stroke: 1.5px var(--neon);
}

/* Texte néon avec glow */
.text-glow-neon {
    color: var(--neon);
    text-shadow: 0 0 60px rgba(0, 255, 136, 0.45), 0 0 120px rgba(0, 255, 136, 0.15);
}

/* Texte gradient */
.text-gradient {
    background: linear-gradient(135deg, var(--neon) 0%, var(--neon2) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Curseur terminal clignotant */
.terminal-cursor::after {
    content: '|';
    color: var(--neon);
    animation: blink-cursor 1.1s step-end infinite;
    margin-left: 2px;
}
@keyframes blink-cursor { 0%, 100% { opacity: 1; } 50% { opacity: 0; } }
```

---

## 3. Composants globaux

### Backgrounds & Overlays

```css
/* ── Background principal ── */
.bg-main {
    background: var(--ink);
    position: relative;
    overflow-x: hidden;
}

/* ── Grille de fond ── */
.bg-grid {
    position: fixed; inset: 0; z-index: 0; pointer-events: none;
    background-image:
        linear-gradient(rgba(0, 255, 136, 0.020) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0, 255, 136, 0.020) 1px, transparent 1px);
    background-size: 68px 68px;
}

/* ── Texture noise ── */
.bg-noise {
    position: fixed; inset: 0; z-index: 1; pointer-events: none;
    opacity: 0.028;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
    background-size: 150px;
}

/* ── Ligne de scan ── */
.scan-line {
    position: fixed; left: 0; right: 0; height: 1px;
    z-index: 2; pointer-events: none; opacity: 0.20;
    background: linear-gradient(90deg, transparent 0%, rgba(0, 255, 136, 0.8) 50%, transparent 100%);
    animation: scanline 9s linear infinite;
}
@keyframes scanline { 0% { top: -2px; } 100% { top: 100vh; } }

/* ── Orbe lumineuse ── */
.orb {
    position: absolute; border-radius: 50%;
    pointer-events: none; filter: blur(90px);
}
.orb-neon  { background: radial-gradient(circle, var(--neon), transparent 70%); opacity: 0.10; }
.orb-cyan  { background: radial-gradient(circle, var(--neon2), transparent 70%); opacity: 0.08; }
.orb-amber { background: radial-gradient(circle, var(--amber), transparent 70%); opacity: 0.06; }

/* ── Particules canvas ── */
/* → voir Section 14 pour le JS */
#bg-canvas {
    position: fixed; inset: 0; z-index: 0; pointer-events: none; opacity: 0.45;
}
```

### Navigation

```css
/* ── Nav fixe dark glass ── */
.bia-nav {
    position: fixed; top: 0; left: 0; right: 0;
    z-index: 500; height: 64px;
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 52px;
    border-bottom: 1px solid var(--bord2);
    backdrop-filter: blur(20px) saturate(180%);
    background: rgba(3, 6, 13, 0.75);
    transition: border-color var(--t-med), background var(--t-med);
}
.bia-nav.scrolled {
    border-bottom-color: var(--border);
    background: rgba(3, 6, 13, 0.92);
}

/* Logo */
.nav-logo {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 20px; letter-spacing: 0.12em;
    color: var(--neon); text-decoration: none;
    display: flex; align-items: center; gap: 10px;
}
.nav-logo-dot {
    width: 8px; height: 8px; border-radius: 50%; background: var(--neon);
    animation: pulse-dot 2.5s ease-in-out infinite;
}
@keyframes pulse-dot {
    0%, 100% { box-shadow: 0 0 0 0 rgba(0, 255, 136, 0.8); }
    50%       { box-shadow: 0 0 0 10px rgba(0, 255, 136, 0); }
}

/* Liens nav */
.nav-link {
    padding: 5px 13px;
    font-family: 'DM Mono', monospace; font-size: 11px;
    color: var(--muted); text-decoration: none;
    border: 1px solid transparent; border-radius: var(--r-sm);
    letter-spacing: 0.05em; transition: all var(--t-med);
}
.nav-link:hover {
    color: var(--neon);
    border-color: var(--border);
    background: var(--dim);
}
.nav-link.active {
    color: var(--neon);
    border-color: var(--border);
    background: var(--dim);
}

/* Bouton CTA nav */
.nav-cta {
    padding: 8px 20px;
    background: var(--neon); color: var(--ink);
    font-family: 'DM Mono', monospace; font-size: 11px; font-weight: 500;
    border: none; border-radius: var(--r-sm); cursor: pointer;
    text-decoration: none; letter-spacing: 0.06em;
    position: relative; overflow: hidden;
    transition: background var(--t-med), transform var(--t-med);
}
.nav-cta::after {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transform: translateX(-100%); transition: transform 0.5s;
}
.nav-cta:hover::after { transform: translateX(100%); }
.nav-cta:hover { background: var(--neon2); transform: translateY(-1px); }

/* Badge plan */
.nav-plan-badge {
    padding: 4px 11px;
    font-family: 'DM Mono', monospace; font-size: 9px;
    letter-spacing: 0.12em; text-transform: uppercase;
    background: var(--dim); border: 1px solid var(--border);
    color: var(--neon); border-radius: var(--r-sm);
}
```

### Boutons

```css
/* ── Bouton primaire ── */
.btn-primary {
    padding: 13px 32px;
    background: var(--neon); color: var(--ink);
    font-family: 'Bebas Neue', sans-serif; font-size: 16px; letter-spacing: 0.10em;
    border: none; border-radius: var(--r-sm); cursor: pointer;
    text-decoration: none; display: inline-block;
    position: relative; overflow: hidden;
    transition: all var(--t-med);
    box-shadow: 0 0 0 0 rgba(0, 255, 136, 0);
}
.btn-primary::before {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.22), transparent);
    transform: translateX(-100%); transition: transform 0.5s;
}
.btn-primary:hover::before { transform: translateX(100%); }
.btn-primary:hover {
    background: var(--neon2);
    box-shadow: 0 0 40px rgba(0, 255, 136, 0.28);
    transform: translateY(-2px);
}
.btn-primary:active { transform: translateY(0); }

/* ── Bouton ghost ── */
.btn-ghost {
    padding: 12px 26px;
    background: transparent;
    border: 1px solid var(--border); color: var(--neon);
    font-family: 'DM Mono', monospace; font-size: 12px; letter-spacing: 0.08em;
    border-radius: var(--r-sm); cursor: pointer; text-decoration: none;
    display: inline-block; transition: all var(--t-med);
}
.btn-ghost:hover {
    background: var(--dim);
    border-color: var(--neon);
    box-shadow: var(--glow-neon);
}

/* ── Bouton action (petit) ── */
.btn-action {
    padding: 7px 13px;
    font-family: 'DM Mono', monospace; font-size: 10px; letter-spacing: 0.06em;
    border: 1px solid var(--muted2); color: var(--muted);
    background: transparent; border-radius: var(--r-sm);
    cursor: pointer; transition: all var(--t-med);
    display: inline-flex; align-items: center; gap: 5px;
}
.btn-action:hover {
    border-color: var(--neon); color: var(--neon); background: var(--dim);
}
.btn-action.active {
    border-color: var(--neon); color: var(--neon); background: var(--dim);
}

/* ── Bouton destructif ── */
.btn-danger {
    padding: 8px 16px;
    background: var(--dimr); border: 1px solid rgba(255, 77, 77, 0.3);
    color: var(--red); font-family: 'DM Mono', monospace; font-size: 11px;
    border-radius: var(--r-sm); cursor: pointer; transition: all var(--t-med);
}
.btn-danger:hover { background: rgba(255, 77, 77, 0.14); }

/* ── Loading state bouton ── */
.btn-loading {
    display: inline-flex; align-items: center; gap: 6px;
    pointer-events: none; opacity: 0.75;
}
.btn-dots span {
    width: 5px; height: 5px; border-radius: 50%; background: currentColor;
    display: inline-block;
    animation: btn-bounce 1.2s ease-in-out infinite;
}
.btn-dots span:nth-child(2) { animation-delay: 0.2s; }
.btn-dots span:nth-child(3) { animation-delay: 0.4s; }
@keyframes btn-bounce { 0%,80%,100%{transform:scale(0.5)} 40%{transform:scale(1)} }
```

### Inputs & Formulaires

```css
/* ── Input terminal ── */
.input-terminal {
    width: 100%;
    background: var(--ink2);
    border: 1px solid var(--muted2);
    border-radius: var(--r-md);
    color: var(--text);
    font-family: 'DM Mono', monospace;
    font-size: 14px; letter-spacing: 0.02em;
    padding: 12px 16px; height: 48px;
    outline: none; caret-color: var(--neon);
    transition: border-color var(--t-med), box-shadow var(--t-med);
}
.input-terminal::placeholder { color: var(--muted2); }
.input-terminal:focus {
    border-color: var(--neon);
    box-shadow: 0 0 0 3px rgba(0, 255, 136, 0.10),
                0 0 20px rgba(0, 255, 136, 0.05);
}
.input-terminal.error {
    border-color: rgba(255, 77, 77, 0.5);
    box-shadow: 0 0 0 3px rgba(255, 77, 77, 0.08);
}

/* ── Label flottant ── */
.input-group {
    position: relative; margin-bottom: 20px;
}
.input-label {
    font-family: 'DM Mono', monospace;
    font-size: 9px; letter-spacing: 0.14em; text-transform: uppercase;
    color: var(--muted); margin-bottom: 7px; display: block;
    transition: color var(--t-med);
}
.input-group:focus-within .input-label { color: var(--neon); }

/* ── Prefix terminal ── */
.input-with-prefix {
    display: flex; align-items: center;
    background: var(--ink2); border: 1px solid var(--muted2);
    border-radius: var(--r-md); overflow: hidden;
    transition: border-color var(--t-med), box-shadow var(--t-med);
}
.input-with-prefix:focus-within {
    border-color: var(--neon);
    box-shadow: 0 0 0 3px rgba(0, 255, 136, 0.10);
}
.input-prefix {
    padding: 0 14px; height: 48px;
    display: flex; align-items: center;
    font-family: 'DM Mono', monospace; font-size: 12px; color: var(--neon);
    border-right: 1px solid var(--muted2);
    background: rgba(0, 255, 136, 0.04);
    white-space: nowrap; user-select: none;
}
.input-with-prefix input {
    flex: 1; background: transparent; border: none; outline: none;
    color: var(--text); font-family: 'DM Mono', monospace; font-size: 14px;
    padding: 0 16px; height: 48px; caret-color: var(--neon);
}
.input-with-prefix input::placeholder { color: var(--muted2); }

/* ── Checkbox néon ── */
.checkbox-neon {
    appearance: none; width: 16px; height: 16px;
    border: 1px solid var(--muted2); border-radius: 2px;
    background: transparent; cursor: pointer;
    transition: all var(--t-med); flex-shrink: 0;
}
.checkbox-neon:checked {
    background: var(--neon); border-color: var(--neon);
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath d='M3 8l3.5 3.5L13 5' stroke='%2303060D' stroke-width='2' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
}

/* ── Error message ── */
.input-error {
    font-family: 'DM Mono', monospace; font-size: 10px;
    color: var(--red); letter-spacing: 0.05em;
    margin-top: 5px; display: flex; align-items: center; gap: 5px;
}
.input-error::before { content: 'ERR ›'; opacity: 0.7; }
```

### Cards

```css
/* ── Card de base ── */
.card {
    background: var(--ink2);
    border: 1px solid var(--bord2);
    border-radius: var(--r-lg);
    overflow: hidden;
    transition: border-color var(--t-med), background var(--t-med);
}
.card:hover { border-color: var(--border); }

/* ── Card avec top-border néon ── */
.card-neon {
    background: var(--ink2);
    border: 1px solid var(--border);
    border-radius: var(--r-lg);
    position: relative; overflow: hidden;
}
.card-neon::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0; height: 2px;
    background: linear-gradient(90deg, var(--neon), var(--neon2));
}

/* ── Card glass ── */
.card-glass {
    background: rgba(7, 12, 24, 0.7);
    border: 1px solid var(--bord2);
    border-radius: var(--r-lg);
    backdrop-filter: blur(16px);
}

/* ── KPI Card ── */
.kpi-card {
    background: var(--ink2); border: 1px solid var(--bord2);
    border-radius: var(--r-lg); padding: 18px 20px;
    position: relative; overflow: hidden;
    transition: all var(--t-med);
}
.kpi-card::after {
    content: '';
    position: absolute; bottom: 0; left: 0;
    height: 2px; background: var(--neon);
    width: 0; transition: width 0.8s ease;
}
.kpi-card.loaded::after { width: 100%; }
.kpi-card:hover { background: var(--ink3); }
.kpi-label {
    font-family: 'DM Mono', monospace; font-size: 9px;
    color: var(--muted); letter-spacing: 0.14em;
    text-transform: uppercase; margin-bottom: 8px;
}
.kpi-value {
    font-family: 'Bebas Neue', sans-serif; font-size: 34px;
    letter-spacing: 0.04em; color: #fff; line-height: 1;
}
.kpi-value.good  { color: var(--neon); }
.kpi-value.info  { color: var(--neon2); }
.kpi-value.warn  { color: var(--amber); }
.kpi-value.small { font-size: 18px; }

/* ── Progress bar ── */
.progress-bar {
    height: 3px; background: var(--muted2);
    border-radius: 2px; overflow: hidden; margin-top: 7px;
}
.progress-fill {
    height: 100%; border-radius: 2px; background: var(--neon);
    width: 0; transition: width 1.1s 0.2s ease;
}
.progress-fill.cyan  { background: var(--neon2); }
.progress-fill.amber { background: var(--amber); }
.progress-fill.red   { background: var(--red); }

/* ── Score ring SVG (optionnel) ── */
/* Utiliser un <svg> avec stroke-dashoffset animé : voir Section 14 */
```

### Badges & Tags

```css
/* ── Chip / Badge ── */
.chip {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px; border-radius: var(--r-sm);
    font-family: 'DM Mono', monospace; font-size: 10px;
    letter-spacing: 0.04em; border: 1px solid;
}
.chip-neon   { background: var(--dim);  color: var(--neon);  border-color: rgba(0,255,136,0.22); }
.chip-cyan   { background: var(--dimc); color: var(--neon2); border-color: rgba(0,229,255,0.22); }
.chip-amber  { background: var(--dima); color: var(--amber); border-color: rgba(255,184,0,0.22); }
.chip-red    { background: var(--dimr); color: var(--red);   border-color: rgba(255,77,77,0.22); }
.chip-gray   { background: var(--bord3); color: var(--muted); border-color: var(--muted2); }

/* ── Priority badge ── */
.priority-haute   { color: var(--red);   font-family: 'DM Mono', monospace; font-size: 9px; }
.priority-moyenne { color: var(--amber); font-family: 'DM Mono', monospace; font-size: 9px; }
.priority-faible  { color: var(--neon);  font-family: 'DM Mono', monospace; font-size: 9px; }
.priority-haute::before   { content: '● '; }
.priority-moyenne::before { content: '● '; }
.priority-faible::before  { content: '● '; }
```

---

## 4. Animations fondamentales

```css
/* ════════════════════════════════════════
   ENTRÉES DE PAGE
════════════════════════════════════════ */

/* Fade + slide depuis le bas */
@keyframes fade-up {
    from { opacity: 0; transform: translateY(28px); }
    to   { opacity: 1; transform: translateY(0); }
}
.anim-fade-up { animation: fade-up 0.7s ease both; }

/* Fade depuis la gauche */
@keyframes fade-left {
    from { opacity: 0; transform: translateX(-24px); }
    to   { opacity: 1; transform: translateX(0); }
}
.anim-fade-left { animation: fade-left 0.6s ease both; }

/* Fade simple */
@keyframes fade-in {
    from { opacity: 0; }
    to   { opacity: 1; }
}
.anim-fade-in { animation: fade-in 0.5s ease both; }

/* Zoom depuis le centre */
@keyframes zoom-in {
    from { opacity: 0; transform: scale(0.95); }
    to   { opacity: 1; transform: scale(1); }
}
.anim-zoom-in { animation: zoom-in 0.5s ease both; }

/* Reveal depuis le haut (cards de résultat) */
@keyframes reveal-down {
    from { opacity: 0; transform: translateY(-16px); }
    to   { opacity: 1; transform: translateY(0); }
}
.anim-reveal-down { animation: reveal-down 0.4s ease both; }

/* ── Stagger helper (retards en cascade) ── */
.stagger-1 { animation-delay: 0.05s; }
.stagger-2 { animation-delay: 0.12s; }
.stagger-3 { animation-delay: 0.19s; }
.stagger-4 { animation-delay: 0.26s; }
.stagger-5 { animation-delay: 0.33s; }
.stagger-6 { animation-delay: 0.40s; }

/* ════════════════════════════════════════
   SCROLL REVEAL
════════════════════════════════════════ */
/* Appliquer .reveal sur les sections. JS IntersectionObserver ajoute .visible */
.reveal {
    opacity: 0;
    transform: translateY(32px);
    transition: opacity 0.75s ease, transform 0.75s ease;
}
.reveal.visible {
    opacity: 1;
    transform: translateY(0);
}
/* Variantes */
.reveal-left  { transform: translateX(-32px); }
.reveal-right { transform: translateX(32px); }
.reveal-scale { transform: scale(0.94); }
.reveal-left.visible, .reveal-right.visible, .reveal-scale.visible {
    transform: none;
}

/* ════════════════════════════════════════
   MICRO-INTERACTIONS
════════════════════════════════════════ */

/* Shimmer sur hover des boutons CTA */
@keyframes shimmer {
    0%   { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}
/* → Appliquer via ::after sur les .btn-primary */

/* Hover lift */
.hover-lift { transition: transform var(--t-med), box-shadow var(--t-med); }
.hover-lift:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
}

/* Hover border neon */
.hover-neon {
    border: 1px solid var(--muted2);
    transition: border-color var(--t-med), box-shadow var(--t-med), background var(--t-med);
}
.hover-neon:hover {
    border-color: var(--neon);
    box-shadow: 0 0 0 1px var(--border), inset 0 0 20px rgba(0,255,136,0.03);
    background: var(--ink3);
}

/* Barre latérale au hover des items de liste */
.hover-bar {
    position: relative; overflow: hidden;
    transition: transform var(--t-med), background var(--t-med);
}
.hover-bar::before {
    content: '';
    position: absolute; top: 0; left: 0;
    width: 2px; height: 100%;
    background: var(--neon);
    transform: scaleY(0);
    transform-origin: bottom;
    transition: transform 0.25s ease;
}
.hover-bar:hover::before  { transform: scaleY(1); }
.hover-bar:hover          { transform: translateX(4px); background: rgba(0,255,136,0.03); }

/* ════════════════════════════════════════
   EFFETS GLOBAUX EN BOUCLE
════════════════════════════════════════ */

/* Pulse glow */
@keyframes glow-pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(0, 255, 136, 0.6); }
    50%       { box-shadow: 0 0 0 10px rgba(0, 255, 136, 0); }
}

/* Scan horizontal d'une surface */
@keyframes surface-scan {
    0%   { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}
.surface-scan {
    background: linear-gradient(90deg,
        transparent 0%, rgba(0,255,136,0.06) 50%, transparent 100%);
    background-size: 200% 100%;
    animation: surface-scan 3s linear infinite;
}

/* Texte typewriter */
@keyframes typewriter {
    from { width: 0; }
    to   { width: 100%; }
}
.typewriter {
    overflow: hidden; white-space: nowrap;
    animation: typewriter 1.8s steps(30, end) both;
}

/* ════════════════════════════════════════
   TRANSITIONS DE PAGE
════════════════════════════════════════ */

/* Overlay de transition entre pages */
.page-transition-overlay {
    position: fixed; inset: 0; z-index: 9999;
    background: var(--ink);
    transform: scaleY(0); transform-origin: top;
    animation: page-out 0.4s ease both;
}
@keyframes page-out {
    0%   { transform: scaleY(1); transform-origin: bottom; }
    100% { transform: scaleY(0); transform-origin: bottom; }
}
```

---

## 5. Page Login

```
URL   : /login
Route : auth.login
Fichier : resources/views/auth/login.blade.php
```

### Concept visuel
Layout **split asymétrique** : côté gauche `40%` avec le formulaire flottant dans un card glass, côté droit `60%` avec l'animation canvas + tagline monumentale. Sur mobile : formulaire full width centré.

### Structure HTML (squelette)
```html
<div class="login-page">
    <!-- Backgrounds -->
    <canvas id="bg-canvas"></canvas>
    <div class="bg-grid"></div>
    <div class="bg-noise"></div>
    <div class="scan-line"></div>

    <div class="login-split">
        <!-- Gauche : formulaire -->
        <div class="login-left">
            <a href="/" class="login-logo">
                <div class="nav-logo-dot"></div> BIA
            </a>
            <div class="login-card">
                <!-- Form content -->
            </div>
        </div>

        <!-- Droite : visuel -->
        <div class="login-right">
            <div class="login-tagline">...</div>
            <div class="login-stats">...</div>
        </div>
    </div>
</div>
```

### CSS spécifique

```css
/* ── Layout split ── */
.login-page {
    min-height: 100vh;
    background: var(--ink);
    display: flex; align-items: stretch;
    font-family: 'Syne', sans-serif;
}
.login-split {
    display: flex; width: 100%; position: relative; z-index: 10;
}
.login-left {
    width: 42%; min-height: 100vh;
    padding: 48px 56px;
    display: flex; flex-direction: column; justify-content: center;
    border-right: 1px solid var(--bord2);
    background: rgba(3, 6, 13, 0.6);
    backdrop-filter: blur(8px);
}
.login-right {
    flex: 1; position: relative; overflow: hidden;
    display: flex; flex-direction: column;
    justify-content: center; padding: 80px 72px;
}

/* ── Orbe déco côté droit ── */
.login-right::before {
    content: '';
    position: absolute; top: -100px; right: -100px;
    width: 500px; height: 500px; border-radius: 50%;
    background: radial-gradient(circle, rgba(0,255,136,0.10), transparent 70%);
    pointer-events: none; filter: blur(60px);
}
.login-right::after {
    content: '';
    position: absolute; bottom: -80px; left: 20%;
    width: 350px; height: 350px; border-radius: 50%;
    background: radial-gradient(circle, rgba(0,229,255,0.08), transparent 70%);
    pointer-events: none; filter: blur(60px);
}

/* ── Logo ── */
.login-logo {
    font-family: 'Bebas Neue', sans-serif; font-size: 18px;
    letter-spacing: 0.12em; color: var(--neon); text-decoration: none;
    display: inline-flex; align-items: center; gap: 9px;
    margin-bottom: 52px;
    animation: fade-up 0.5s ease both;
}

/* ── Card formulaire ── */
.login-card {
    animation: fade-up 0.6s 0.1s ease both;
}
.login-title {
    font-family: 'Bebas Neue', sans-serif; font-size: 36px;
    letter-spacing: 0.06em; color: #fff; margin-bottom: 4px;
}
.login-subtitle {
    font-family: 'DM Mono', monospace; font-size: 11px;
    color: var(--muted); letter-spacing: 0.08em; margin-bottom: 36px;
}
.login-subtitle::before { content: '// '; color: var(--neon); opacity: 0.5; }

/* ── Séparateur ── */
.login-divider {
    display: flex; align-items: center; gap: 12px;
    margin: 20px 0;
}
.login-divider::before, .login-divider::after {
    content: ''; flex: 1; height: 1px; background: var(--muted2);
}
.login-divider span {
    font-family: 'DM Mono', monospace; font-size: 10px;
    color: var(--muted2); letter-spacing: 0.1em;
}

/* ── Lien register ── */
.login-register-link {
    margin-top: 24px; text-align: center;
    font-family: 'DM Mono', monospace; font-size: 11px;
    color: var(--muted); letter-spacing: 0.05em;
}
.login-register-link a {
    color: var(--neon); text-decoration: none;
    transition: color var(--t-med);
}
.login-register-link a:hover { color: var(--neon2); }

/* ── Tagline côté droit ── */
.login-tagline {
    font-family: 'Bebas Neue', sans-serif;
    font-size: clamp(48px, 6vw, 88px);
    letter-spacing: 0.03em; line-height: 0.92; color: #fff;
    margin-bottom: 32px;
    animation: fade-up 0.7s 0.2s ease both;
}
.login-tagline .stroke { -webkit-text-stroke: 1.5px var(--neon); color: transparent; }
.login-tagline .glow   { color: var(--neon); text-shadow: 0 0 60px rgba(0,255,136,0.4); }

/* ── Stats côté droit ── */
.login-stats {
    display: flex; gap: 32px; flex-wrap: wrap;
    animation: fade-up 0.7s 0.3s ease both;
}
.login-stat-n {
    font-family: 'Bebas Neue', sans-serif; font-size: 32px;
    color: #fff; line-height: 1;
}
.login-stat-n span { color: var(--neon); }
.login-stat-l {
    font-family: 'DM Mono', monospace; font-size: 9px;
    color: var(--muted); letter-spacing: 0.12em; text-transform: uppercase;
}

/* ── Animation shake sur erreur ── */
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    20%, 60%  { transform: translateX(-6px); }
    40%, 80%  { transform: translateX(6px); }
}
.input-shake { animation: shake 0.4s ease; }

/* ── Bouton submit avec état loading ── */
.btn-submit {
    width: 100%; height: 48px;
    background: var(--neon); color: var(--ink);
    font-family: 'Bebas Neue', sans-serif; font-size: 16px; letter-spacing: 0.1em;
    border: none; border-radius: var(--r-md); cursor: pointer;
    position: relative; overflow: hidden;
    transition: all var(--t-med);
    display: flex; align-items: center; justify-content: center; gap: 8px;
}
.btn-submit:hover { background: var(--neon2); transform: translateY(-1px); }
.btn-submit:active { transform: translateY(0); }
```

### Animations spécifiques login

```css
/* Scan vertical du card au chargement */
@keyframes card-scan {
    0%   { top: 0; opacity: 1; }
    100% { top: 100%; opacity: 0; }
}
.card-scan-effect::after {
    content: '';
    position: absolute; left: 0; right: 0; height: 2px; top: 0;
    background: linear-gradient(90deg, transparent, var(--neon), transparent);
    animation: card-scan 1.5s ease both;
    pointer-events: none;
}

/* Glow border au focus du form entier */
.login-form-active {
    box-shadow: 0 0 0 1px var(--border), 0 0 40px rgba(0, 255, 136, 0.05);
    transition: box-shadow 0.5s ease;
}

/* Texte "connexion en cours..." qui défile */
@keyframes connecting {
    0%   { content: 'CONNEXION'; }
    33%  { content: 'CONNEXION.'; }
    66%  { content: 'CONNEXION..'; }
    100% { content: 'CONNEXION...'; }
}
```

---

## 6. Page Register

```
URL   : /register
Route : auth.register
Fichier : resources/views/auth/register.blade.php
```

### Concept visuel
Layout **centré vertical full-screen** avec un card plus large (620px) et fond canvas plein écran. Le formulaire est divisé en 2 colonnes sur desktop (Nom + Email / Password + Confirm). Un indicateur de force du mot de passe avec barre colorée.

### CSS spécifique

```css
/* ── Layout centré ── */
.register-page {
    min-height: 100vh;
    background: var(--ink);
    display: flex; align-items: center; justify-content: center;
    padding: 80px 24px;
    position: relative;
}

/* ── Card register ── */
.register-card {
    width: 100%; max-width: 620px;
    background: var(--ink2);
    border: 1px solid var(--border);
    border-radius: var(--r-xl);
    padding: 48px 52px;
    position: relative; z-index: 10;
    animation: zoom-in 0.5s ease both;
}
.register-card::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--neon), var(--neon2));
    border-radius: var(--r-xl) var(--r-xl) 0 0;
}

/* ── Grid 2 colonnes ── */
.register-grid-2 {
    display: grid; grid-template-columns: 1fr 1fr; gap: 16px;
}

/* ── Step indicator (optionnel multi-step) ── */
.register-steps {
    display: flex; align-items: center; gap: 6px; margin-bottom: 36px;
}
.reg-step {
    width: 28px; height: 28px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-family: 'DM Mono', monospace; font-size: 11px;
    border: 1px solid var(--muted2); color: var(--muted);
    transition: all var(--t-med);
}
.reg-step.active {
    border-color: var(--neon); color: var(--neon); background: var(--dim);
    box-shadow: 0 0 12px rgba(0,255,136,0.25);
}
.reg-step.done {
    background: var(--neon); border-color: var(--neon); color: var(--ink);
}
.reg-step-line { flex: 1; height: 1px; background: var(--muted2); }
.reg-step.done + .reg-step-line { background: var(--neon); }

/* ── Password strength bar ── */
.pwd-strength-wrap { margin-top: 8px; }
.pwd-strength-bar {
    height: 3px; background: var(--muted2);
    border-radius: 2px; overflow: hidden; margin-bottom: 5px;
}
.pwd-strength-fill {
    height: 100%; border-radius: 2px;
    transition: width 0.4s ease, background 0.4s ease;
}
.pwd-weak    .pwd-strength-fill { width: 25%; background: var(--red); }
.pwd-fair    .pwd-strength-fill { width: 50%; background: var(--amber); }
.pwd-good    .pwd-strength-fill { width: 75%; background: var(--neon2); }
.pwd-strong  .pwd-strength-fill { width: 100%; background: var(--neon); }
.pwd-label {
    font-family: 'DM Mono', monospace; font-size: 9px; letter-spacing: 0.1em;
    color: var(--muted);
}
.pwd-weak   .pwd-label { color: var(--red); }
.pwd-fair   .pwd-label { color: var(--amber); }
.pwd-good   .pwd-label { color: var(--neon2); }
.pwd-strong .pwd-label { color: var(--neon); }

/* ── Plan selector dans le register ── */
.plan-selector {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px;
    margin-bottom: 24px;
}
.plan-option {
    padding: 12px 10px; text-align: center;
    border: 1px solid var(--muted2); border-radius: var(--r-md);
    cursor: pointer; transition: all var(--t-med);
    position: relative;
}
.plan-option:hover { border-color: var(--border); background: var(--dim); }
.plan-option.selected {
    border-color: var(--neon); background: var(--dim);
    box-shadow: 0 0 0 1px var(--border);
}
.plan-option input[type="radio"] { position: absolute; opacity: 0; }
.plan-option-name {
    font-family: 'Bebas Neue', sans-serif; font-size: 18px;
    letter-spacing: 0.08em; color: #fff; display: block; margin-bottom: 2px;
}
.plan-option-price {
    font-family: 'DM Mono', monospace; font-size: 10px;
    color: var(--muted); letter-spacing: 0.06em;
}
.plan-option.selected .plan-option-name { color: var(--neon); }
.plan-option.selected .plan-option-price { color: rgba(0,255,136,0.6); }
```

---

## 7. Page Analyse Index

```
URL   : /analyser
Route : analysis.index
Fichier : resources/views/analysis/index.blade.php
```

### Concept visuel
Fond canvas plein écran. Hero avec titre Bebas Neue XXL, terminal de recherche centré, steps de progression animés. Section historique en bas.

### CSS spécifique

```css
/* ── Hero section ── */
.analysis-hero {
    padding: 100px 52px 60px;
    max-width: 1100px; margin: 0 auto;
    position: relative; z-index: 10;
}

/* ── Terminal de recherche ── */
.search-terminal {
    max-width: 700px; margin-top: 44px;
}
.terminal-label {
    font-family: 'DM Mono', monospace; font-size: 10px;
    color: var(--neon); letter-spacing: 0.14em; margin-bottom: 8px;
    display: flex; align-items: center; gap: 5px;
}
.terminal-label::before { content: '>'; animation: blink-cursor 1.1s step-end infinite; }

/* ── Boîte de recherche ── */
.search-box {
    display: flex; align-items: center;
    background: var(--ink2); border: 1px solid var(--border);
    border-radius: var(--r-lg); overflow: hidden;
    transition: all 0.35s;
    box-shadow: 0 0 0 0 rgba(0, 255, 136, 0);
}
.search-box:focus-within {
    border-color: var(--neon);
    box-shadow: 0 0 0 3px rgba(0, 255, 136, 0.10), 0 0 50px rgba(0, 255, 136, 0.06);
}

/* ── Steps de progression ── */
.progress-steps {
    max-width: 700px; margin-top: 14px;
    padding: 14px 18px;
    background: var(--ink2); border: 1px solid var(--muted2);
    border-radius: var(--r-lg);
    animation: fade-in 0.3s ease both;
}
.prog-step {
    display: flex; align-items: center; gap: 9px;
    font-family: 'DM Mono', monospace; font-size: 11px;
    color: var(--muted2); padding: 4px 0;
    transition: color 0.3s; letter-spacing: 0.04em;
}
.prog-step.active { color: var(--neon); }
.prog-step.done   { color: var(--muted); }
.prog-step.done::before { content: '✓ '; color: var(--neon); }
.prog-dot {
    width: 6px; height: 6px; border-radius: 50%;
    background: var(--muted2); flex-shrink: 0; transition: all 0.3s;
}
.prog-step.active .prog-dot {
    background: var(--neon);
    box-shadow: 0 0 8px var(--neon);
    animation: glow-pulse 1s ease-in-out infinite;
}

/* ── Résultat zone ── */
#result-zone {
    max-width: 1100px; margin: 0 auto; padding: 0 52px 80px;
    position: relative; z-index: 10;
}

/* ── Résultat card reveal ── */
.result-card {
    animation: result-reveal 0.55s cubic-bezier(0.22, 1, 0.36, 1) both;
}
@keyframes result-reveal {
    from { opacity: 0; transform: translateY(32px) scale(0.98); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}

/* ── Tabs ── */
.tabs-row {
    display: flex; border-bottom: 1px solid var(--muted2);
    overflow-x: auto; scrollbar-width: none;
}
.tabs-row::-webkit-scrollbar { display: none; }
.tab-btn {
    padding: 13px 20px;
    font-family: 'DM Mono', monospace; font-size: 10px;
    letter-spacing: 0.12em; text-transform: uppercase;
    color: var(--muted); background: transparent;
    border: none; border-bottom: 2px solid transparent;
    cursor: pointer; white-space: nowrap;
    transition: color var(--t-med), border-color var(--t-med), background var(--t-med);
}
.tab-btn:hover { color: var(--text); background: var(--glass); }
.tab-btn.active {
    color: var(--neon);
    border-bottom-color: var(--neon);
    background: rgba(0, 255, 136, 0.03);
}

/* ── Historique cards ── */
.history-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 10px;
}
.history-card {
    background: var(--ink2); border: 1px solid var(--muted2);
    border-radius: var(--r-lg); padding: 14px 16px;
    text-decoration: none; display: block;
    transition: all 0.25s;
    position: relative; overflow: hidden;
}
.history-card::before {
    content: '';
    position: absolute; top: 0; left: 0;
    width: 2px; height: 100%;
    background: var(--neon);
    transform: scaleY(0); transform-origin: bottom;
    transition: transform 0.25s ease;
}
.history-card:hover { border-color: var(--border); transform: translateX(4px); }
.history-card:hover::before { transform: scaleY(1); }

/* ── Card avec animation de numérotation des KPI ── */
@keyframes count-up {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}
.kpi-value.counting { animation: count-up 0.4s ease both; }
```

---

## 8. Page Show — Rapport complet

```
URL   : /entreprise/{slug}
Route : analysis.show
Fichier : resources/views/analysis/show.blade.php
```

### Concept visuel
Page longue avec en-tête impactant (nom en Bebas XL, métadonnées en DM Mono), section sticky sidebar optionnelle. Graphiques Chart.js dark. Section compétiteurs en tableau comparatif. Section évolution avec graphique linéaire.

### CSS spécifique

```css
/* ── Page show layout ── */
.show-page {
    max-width: 1100px; margin: 0 auto;
    padding: 90px 52px 80px;
    position: relative; z-index: 10;
}

/* ── En-tête rapport ── */
.report-header {
    padding: 32px 0 28px;
    border-bottom: 1px solid var(--muted2);
    margin-bottom: 32px;
    position: relative;
}
.report-header::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0; height: 2px;
    background: linear-gradient(90deg, var(--neon), var(--neon2), transparent);
}
.report-company-name {
    font-family: 'Bebas Neue', sans-serif;
    font-size: clamp(44px, 6vw, 80px);
    letter-spacing: 0.04em; color: #fff; line-height: 1;
    margin-bottom: 8px;
    animation: fade-up 0.6s ease both;
}
.report-meta-row {
    display: flex; flex-wrap: wrap; gap: 16px;
    font-family: 'DM Mono', monospace; font-size: 11px; color: var(--muted);
    animation: fade-up 0.6s 0.1s ease both;
}
.report-meta-item { display: flex; align-items: center; gap: 5px; }
.report-meta-item::before { content: '[ '; color: var(--neon); }
.report-meta-item::after  { content: ' ]'; color: var(--neon); }

/* ── Grille KPI ── */
.report-kpi-grid {
    display: grid; grid-template-columns: repeat(4, 1fr);
    gap: 1px; background: var(--bord2);
    margin-bottom: 32px;
    animation: fade-up 0.6s 0.15s ease both;
}
.report-kpi-cell {
    background: var(--ink2); padding: 20px 22px;
    border-bottom: 2px solid transparent;
    transition: all var(--t-med);
    position: relative; overflow: hidden;
}
.report-kpi-cell:hover { background: var(--ink3); }
.report-kpi-cell.lit { border-bottom-color: var(--neon); }

/* ── Chart dark style ── */
/* Appliquer à Chart.js via options.scales */
/* colors: text #5A6B82 · grid rgba(255,255,255,0.04) · tick font DM Mono 10px */

/* ── Section concurrents ── */
.competitors-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 12px; margin-top: 20px;
}
.competitor-card {
    background: var(--ink3); border: 1px solid var(--muted2);
    border-radius: var(--r-lg); padding: 18px 20px;
    transition: all var(--t-med);
    position: relative; overflow: hidden;
}
.competitor-card::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0; height: 2px;
    background: var(--neon2); transform: scaleX(0); transform-origin: left;
    transition: transform 0.35s ease;
}
.competitor-card:hover::before { transform: scaleX(1); }
.competitor-card:hover { border-color: var(--borderc); background: var(--ink2); }

/* ── Gauge circulaire SVG ── */
.score-gauge {
    width: 80px; height: 80px; flex-shrink: 0;
}
.gauge-track { fill: none; stroke: var(--muted2); stroke-width: 6; }
.gauge-fill  {
    fill: none; stroke: var(--neon); stroke-width: 6;
    stroke-linecap: round; stroke-dasharray: 188; stroke-dashoffset: 188;
    transform: rotate(-90deg); transform-origin: center;
    transition: stroke-dashoffset 1.2s 0.3s cubic-bezier(0.22, 1, 0.36, 1);
}
/* stroke-dashoffset = 188 * (1 - score/100) → calculer côté JS/Blade */

/* ── Plan d'action timeline ── */
.timeline {
    position: relative; padding-left: 28px;
}
.timeline::before {
    content: '';
    position: absolute; left: 7px; top: 0; bottom: 0;
    width: 1px; background: linear-gradient(180deg, var(--neon), var(--neon2), var(--muted2));
}
.timeline-item {
    position: relative; margin-bottom: 20px;
    padding: 14px 16px;
    background: var(--ink3); border: 1px solid var(--muted2);
    border-radius: var(--r-md);
    transition: all var(--t-med);
}
.timeline-item::before {
    content: '';
    position: absolute; left: -25px; top: 50%;
    transform: translateY(-50%);
    width: 9px; height: 9px; border-radius: 50%;
    background: var(--neon); border: 2px solid var(--ink);
    box-shadow: 0 0 8px rgba(0,255,136,0.5);
}
.timeline-item:hover { border-color: var(--border); background: var(--ink2); }

/* ── Actions flottantes ── */
.floating-actions {
    position: sticky; top: 80px;
    display: flex; flex-direction: column; gap: 8px;
    z-index: 50;
}
```

---

## 9. Dashboard Utilisateur

```
URL   : /dashboard
Route : dashboard
Fichier : resources/views/dashboard/index.blade.php
```

### Concept visuel
Layout sidebar fixe gauche + contenu droit. Sidebar dark avec nav vertical. Contenu avec KPI cards, graphiques, liste analyses. Sidebar se rétracte en mode icon sur tablette.

### CSS spécifique

```css
/* ── Layout dashboard ── */
.dashboard-layout {
    display: flex; min-height: 100vh;
    background: var(--ink); position: relative;
}

/* ── Sidebar ── */
.sidebar {
    width: 240px; flex-shrink: 0;
    background: var(--ink2);
    border-right: 1px solid var(--bord2);
    display: flex; flex-direction: column;
    position: fixed; top: 0; left: 0; bottom: 0;
    z-index: 200;
    transition: width var(--t-slow);
}
.sidebar.collapsed { width: 60px; }
.sidebar-logo {
    padding: 22px 20px 18px;
    border-bottom: 1px solid var(--bord2);
    display: flex; align-items: center; gap: 10px;
}
.sidebar-nav {
    padding: 16px 10px; flex: 1; overflow-y: auto;
}
.sidebar-nav::-webkit-scrollbar { width: 3px; }
.sidebar-nav::-webkit-scrollbar-thumb { background: var(--muted2); border-radius: 2px; }
.sidebar-section-label {
    font-family: 'DM Mono', monospace; font-size: 9px;
    letter-spacing: 0.16em; text-transform: uppercase;
    color: var(--muted2); padding: 10px 12px 6px;
}
.sidebar-item {
    display: flex; align-items: center; gap: 10px;
    padding: 9px 12px; border-radius: var(--r-md);
    font-family: 'DM Mono', monospace; font-size: 11px;
    color: var(--muted); text-decoration: none;
    letter-spacing: 0.04em;
    transition: all var(--t-med);
    position: relative; overflow: hidden;
}
.sidebar-item:hover { background: var(--glass); color: var(--text); }
.sidebar-item.active {
    background: var(--dim); color: var(--neon);
    border: 1px solid var(--border);
}
.sidebar-item.active::before {
    content: '';
    position: absolute; left: 0; top: 0; bottom: 0;
    width: 2px; background: var(--neon);
}
.sidebar-icon { width: 16px; height: 16px; flex-shrink: 0; font-size: 15px; }
.sidebar-label { white-space: nowrap; overflow: hidden; }
.sidebar.collapsed .sidebar-label  { opacity: 0; width: 0; }
.sidebar.collapsed .sidebar-section-label { opacity: 0; }

/* ── Contenu principal ── */
.dashboard-main {
    flex: 1; margin-left: 240px;
    padding: 80px 40px 60px;
    transition: margin-left var(--t-slow);
}
.dashboard-main.sidebar-collapsed { margin-left: 60px; }

/* ── Welcome bar ── */
.welcome-bar {
    display: flex; align-items: flex-end; justify-content: space-between;
    margin-bottom: 32px; flex-wrap: wrap; gap: 16px;
    animation: fade-up 0.5s ease both;
}
.welcome-title {
    font-family: 'Bebas Neue', sans-serif; font-size: 32px;
    letter-spacing: 0.06em; color: #fff;
}
.welcome-sub {
    font-family: 'DM Mono', monospace; font-size: 11px;
    color: var(--muted); letter-spacing: 0.06em; margin-top: 2px;
}
.welcome-sub::before { content: '// '; color: var(--neon); opacity: 0.45; }

/* ── KPI row ── */
.dashboard-kpi-row {
    display: grid; grid-template-columns: repeat(4, 1fr);
    gap: 12px; margin-bottom: 28px;
    animation: fade-up 0.5s 0.08s ease both;
}

/* ── Quota bar section ── */
.quota-section {
    background: var(--ink2); border: 1px solid var(--bord2);
    border-radius: var(--r-lg); padding: 20px 24px;
    margin-bottom: 28px;
    animation: fade-up 0.5s 0.12s ease both;
    position: relative; overflow: hidden;
}
.quota-section::before {
    content: '';
    position: absolute; inset: 0;
    background: radial-gradient(circle at 90% 50%, rgba(0,255,136,0.04), transparent 60%);
    pointer-events: none;
}
.quota-track {
    height: 4px; background: var(--muted2);
    border-radius: 2px; overflow: hidden; margin: 10px 0 6px;
}
.quota-fill {
    height: 100%; border-radius: 2px; background: var(--neon);
    transition: width 1s 0.3s ease;
}
.quota-fill.warn { background: var(--amber); }
.quota-fill.full { background: var(--red); }

/* ── Table analyses ── */
.analyses-table {
    width: 100%; border-collapse: collapse;
    animation: fade-up 0.5s 0.16s ease both;
}
.analyses-table thead th {
    text-align: left; padding: 8px 14px;
    font-family: 'DM Mono', monospace; font-size: 9px;
    color: var(--muted2); letter-spacing: 0.14em; text-transform: uppercase;
    border-bottom: 1px solid var(--muted2);
}
.analyses-table tbody td {
    padding: 12px 14px; font-size: 13px; color: var(--text);
    border-bottom: 1px solid var(--bord3);
    transition: background var(--t-fast);
}
.analyses-table tbody tr:hover td { background: var(--glass); }
.analyses-table tbody tr:last-child td { border-bottom: none; }

/* ── Score pill ── */
.score-pill {
    display: inline-flex; align-items: center; gap: 5px;
    font-family: 'DM Mono', monospace; font-size: 11px;
    padding: 3px 8px; border-radius: 2px;
}
.score-pill.high { background: rgba(0,255,136,0.1); color: var(--neon); }
.score-pill.mid  { background: rgba(255,184,0,0.1); color: var(--amber); }
.score-pill.low  { background: rgba(255,77,77,0.08); color: var(--red); }
```

---

## 10. Dashboard Admin

```
URL   : /admin
Route : admin.dashboard
Fichier : resources/views/admin/dashboard.blade.php
```

### Concept visuel
Même layout sidebar que le dashboard user, mais sidebar avec items différents. En-tête rouge/amber pour distinguer visuellement la zone admin. Graphiques plus denses avec données réelles.

### CSS spécifique

```css
/* ── Admin distinction ── */
.admin-layout .sidebar {
    background: linear-gradient(180deg, var(--ink2), #0D0810);
    border-right-color: rgba(255, 77, 77, 0.15);
}
.admin-layout .sidebar-item.active {
    background: rgba(255, 77, 77, 0.08);
    color: var(--red);
    border-color: rgba(255, 77, 77, 0.25);
}
.admin-layout .sidebar-item.active::before { background: var(--red); }
.admin-badge {
    font-family: 'DM Mono', monospace; font-size: 9px;
    padding: 2px 7px; background: rgba(255,77,77,0.12);
    border: 1px solid rgba(255,77,77,0.25); color: var(--red);
    border-radius: var(--r-sm); letter-spacing: 0.1em;
}

/* ── Stats globales ── */
.admin-stat-highlight {
    background: var(--ink2); border: 1px solid var(--bord2);
    border-radius: var(--r-lg); padding: 22px;
    display: flex; align-items: flex-start; justify-content: space-between;
    transition: all var(--t-med);
    position: relative; overflow: hidden;
}
.admin-stat-highlight::after {
    content: '';
    position: absolute; bottom: 0; left: 0; right: 0; height: 2px;
    background: var(--neon); transform: scaleX(0);
    transition: transform 0.7s ease;
}
.admin-stat-highlight.lit::after { transform: scaleX(1); }

/* ── Chart config recommandée ── */
/*
Chart.js options communes :
{
  color: '#5A6B82',
  borderColor: 'rgba(255,255,255,0.04)',
  grid: { color: 'rgba(255,255,255,0.04)', drawBorder: false },
  ticks: { color: '#5A6B82', font: { family: 'DM Mono', size: 10 } },
  plugins: {
    legend: { labels: { color: '#5A6B82', font: { family: 'DM Mono', size: 10 } } },
    tooltip: {
      backgroundColor: '#0D1424',
      borderColor: 'rgba(0,255,136,0.2)',
      borderWidth: 1,
      titleColor: '#00FF88',
      bodyColor: '#DDE6F0',
      padding: 12
    }
  }
}
*/

/* ── Tableau utilisateurs ── */
.admin-users-table tbody tr:hover { background: rgba(255,77,77,0.03); }
.user-plan-badge {
    font-family: 'DM Mono', monospace; font-size: 9px;
    letter-spacing: 0.1em; padding: 2px 7px;
    border-radius: var(--r-sm); text-transform: uppercase;
}
.plan-free    { background: var(--bord3); color: var(--muted); border: 1px solid var(--muted2); }
.plan-starter { background: var(--dimc); color: var(--neon2); border: 1px solid var(--borderc); }
.plan-pro     { background: var(--dim);  color: var(--neon);  border: 1px solid var(--border); }
.plan-agency  { background: var(--dima); color: var(--amber); border: 1px solid rgba(255,184,0,0.22); }
```

---

## 11. Page Abonnement / Pricing

```
URL   : /abonnement
Route : subscription.index
Fichier : resources/views/subscription/index.blade.php
```

### Concept visuel
Hero avec titre centré + sous-titre. Toggle mensuel/annuel (optionnel). Grid de 4 cards plans. Plan Pro avec mise en avant visuelle (border neon + badge + subtle glow background). Méthodes de paiement en dessous.

### CSS spécifique

```css
/* ── Layout pricing ── */
.pricing-page {
    padding: 90px 52px 80px;
    max-width: 1100px; margin: 0 auto;
    position: relative; z-index: 10;
}
.pricing-hero { text-align: center; margin-bottom: 60px; }

/* ── Toggle billing ── */
.billing-toggle {
    display: inline-flex; align-items: center; gap: 12px;
    padding: 4px; background: var(--ink2);
    border: 1px solid var(--muted2); border-radius: var(--r-xl);
    margin: 24px auto 0; font-family: 'DM Mono', monospace; font-size: 11px;
}
.billing-btn {
    padding: 7px 18px; border-radius: var(--r-lg); cursor: pointer;
    border: none; background: transparent; color: var(--muted);
    letter-spacing: 0.06em; transition: all var(--t-med);
}
.billing-btn.active {
    background: var(--ink3); color: var(--neon);
    border: 1px solid var(--border);
}
.billing-save {
    font-size: 9px; padding: 2px 7px;
    background: var(--neon); color: var(--ink);
    border-radius: var(--r-sm); letter-spacing: 0.06em;
    margin-left: -6px;
}

/* ── Plan grid ── */
.plans-grid {
    display: grid; grid-template-columns: repeat(4, 1fr);
    gap: 1px; background: var(--bord2);
    animation: fade-up 0.6s 0.1s ease both;
}
.plan-card {
    background: var(--ink2); padding: 32px 26px;
    position: relative; overflow: hidden;
    transition: background var(--t-med);
}
.plan-card:hover { background: var(--ink3); }

/* Card Plan Pro (featured) */
.plan-card.featured {
    background: linear-gradient(160deg, rgba(0,255,136,0.07), var(--ink2) 60%);
    border: 1px solid var(--border); margin: -1px;
    z-index: 1;
}
.plan-card.featured::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--neon), var(--neon2));
}

/* Glow subtil sur featured */
.plan-card.featured::after {
    content: '';
    position: absolute; top: -50px; left: -50px;
    width: 200px; height: 200px; border-radius: 50%;
    background: radial-gradient(circle, rgba(0,255,136,0.08), transparent 70%);
    pointer-events: none; filter: blur(30px);
}

/* Animation de hover sur les features */
.plan-features li {
    transition: color var(--t-med), padding-left var(--t-med);
}
.plan-features li:hover { color: var(--text); padding-left: 4px; }

/* ── Méthodes de paiement ── */
.payment-methods-section {
    margin-top: 48px; text-align: center;
    animation: fade-up 0.6s 0.3s ease both;
}
.payment-label {
    font-family: 'DM Mono', monospace; font-size: 10px;
    color: var(--muted2); letter-spacing: 0.12em; margin-bottom: 16px;
}
.payment-methods-row {
    display: flex; align-items: center; justify-content: center;
    gap: 12px; flex-wrap: wrap;
}
.payment-chip {
    padding: 6px 14px;
    background: var(--ink2); border: 1px solid var(--muted2);
    border-radius: var(--r-md); font-family: 'DM Mono', monospace;
    font-size: 11px; color: var(--muted); letter-spacing: 0.06em;
    transition: all var(--t-med);
}
.payment-chip:hover { border-color: var(--border); color: var(--neon); background: var(--dim); }
.payment-chip.africa { border-color: rgba(0,229,255,0.25); color: var(--neon2); }

/* ── Tableau comparatif ── */
.comparison-table {
    width: 100%; border-collapse: collapse; margin-top: 60px;
    animation: fade-up 0.6s 0.35s ease both;
}
.comparison-table th {
    padding: 12px 16px;
    font-family: 'DM Mono', monospace; font-size: 10px;
    color: var(--muted); letter-spacing: 0.12em; text-transform: uppercase;
    border-bottom: 1px solid var(--muted2); text-align: center;
}
.comparison-table th:first-child { text-align: left; }
.comparison-table td {
    padding: 10px 16px; font-size: 12px; color: var(--muted);
    border-bottom: 1px solid var(--bord3); text-align: center;
}
.comparison-table td:first-child { text-align: left; color: var(--text); }
.comparison-table .check { color: var(--neon); font-size: 14px; }
.comparison-table .cross { color: var(--muted2); font-size: 14px; }
```

---

## 12. Page PDF Preview

```
Route : analysis.pdf
Fichier : resources/views/pdf/rapport.blade.php
```

Le PDF utilise DomPDF — les styles sont en CSS embarqué standard (pas de custom properties). Utiliser des couleurs hardcodées.

```css
/* ── PDF Styles (DomPDF — pas de CSS variables) ── */

/* Palette hardcodée pour DomPDF */
/*
  --ink-pdf    : #070C18
  --neon-pdf   : #00FF88
  --neon2-pdf  : #00E5FF
  --text-pdf   : #DDE6F0
  --muted-pdf  : #5A6B82
*/

/* En-tête PDF */
.pdf-header {
    background-color: #0F6E56;  /* vert sombre — pas de var() dans DomPDF */
    color: #fff;
    padding: 24px 32px;
}

/* Corps PDF */
body {
    font-family: 'DejaVu Sans', sans-serif;  /* seule police fiable DomPDF */
    font-size: 11px;
    color: #1a1a2e;
    background: #fff;
}

/* Note importante : dans le PDF, le fond est BLANC avec texte sombre.
   La charte dark terminal n'est pas applicable en PDF papier.
   Utiliser vert #0F6E56 comme couleur d'accentuation. */
```

---

## 13. Layouts Blade

### `layouts/app.blade.php` — Structure complète

```css
/* ── Padding pour la nav fixe ── */
.page-content {
    padding-top: 64px;  /* hauteur de la nav */
}

/* ── Flash messages ── */
.flash-success {
    background: rgba(0,255,136,0.08);
    border: 1px solid rgba(0,255,136,0.25);
    color: var(--neon);
    font-family: 'DM Mono', monospace; font-size: 11px;
    padding: 10px 16px; border-radius: var(--r-md);
    display: flex; align-items: center; gap: 8px;
    animation: fade-up 0.4s ease both;
}
.flash-success::before { content: '✓ OK ›'; opacity: 0.7; }
.flash-error {
    background: var(--dimr);
    border: 1px solid rgba(255,77,77,0.3);
    color: var(--red);
}
.flash-error::before { content: 'ERR ›'; opacity: 0.7; }

/* ── Footer ── */
.site-footer {
    border-top: 1px solid var(--bord2);
    padding: 32px 52px;
    display: flex; align-items: center; justify-content: space-between;
    font-family: 'DM Mono', monospace; font-size: 10px;
    color: var(--muted2); letter-spacing: 0.06em;
    background: var(--ink2); flex-wrap: wrap; gap: 12px;
    position: relative; z-index: 10;
}
```

### `layouts/dashboard.blade.php`

```html
<!-- Squelette du layout dashboard -->
<div class="dashboard-layout">
    @include('layouts.partials.sidebar')
    <div class="dashboard-main" id="dash-main">
        <!-- Topbar contextuel -->
        <div class="dash-topbar">
            <div class="dash-breadcrumb">...</div>
            <div class="dash-topbar-actions">...</div>
        </div>
        @yield('content')
    </div>
</div>
```

```css
/* Topbar dashboard */
.dash-topbar {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 28px; flex-wrap: wrap; gap: 12px;
}
.dash-breadcrumb {
    font-family: 'DM Mono', monospace; font-size: 11px; color: var(--muted);
    display: flex; align-items: center; gap: 6px; letter-spacing: 0.05em;
}
.dash-breadcrumb span { color: var(--muted2); }
.dash-breadcrumb .current { color: var(--neon); }
```

---

## 14. Utilitaires JS réutilisables

### Canvas Background (copier-coller dans chaque page)

```javascript
// ── Initialisation canvas particles + colonnes de données ──
function initBgCanvas(canvasId = 'bg-canvas') {
    const cvs = document.getElementById(canvasId);
    if (!cvs) return;
    const ctx = cvs.getContext('2d');
    let W, H, pts = [], cols = [];

    function resize() {
        W = cvs.width = innerWidth;
        H = cvs.height = innerHeight;
    }

    // Particule flottante
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

    // Colonnes de caractères tombants
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

    // Connexions entre particules proches
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
```

### Cursor personnalisé (pages marketing + auth)

```javascript
// ── Cursor néon ──
function initCursor() {
    const dot  = document.getElementById('cursor');
    const ring = document.getElementById('cursor-ring');
    if (!dot || !ring) return;

    let mx = 0, my = 0, rx = 0, ry = 0;

    document.addEventListener('mousemove', e => {
        mx = e.clientX; my = e.clientY;
        dot.style.left = mx + 'px';
        dot.style.top  = my + 'px';
    });

    (function animRing() {
        rx += (mx - rx) * 0.12;
        ry += (my - ry) * 0.12;
        ring.style.left = rx + 'px';
        ring.style.top  = ry + 'px';
        requestAnimationFrame(animRing);
    })();

    document.querySelectorAll('a, button').forEach(el => {
        el.addEventListener('mouseenter', () => {
            ring.style.width  = '52px';
            ring.style.height = '52px';
            ring.style.borderColor = 'rgba(0, 255, 136, 0.9)';
            dot.style.opacity = '0';
        });
        el.addEventListener('mouseleave', () => {
            ring.style.width  = '34px';
            ring.style.height = '34px';
            ring.style.borderColor = 'rgba(0, 255, 136, 0.45)';
            dot.style.opacity = '1';
        });
    });
}
```

### Scroll Reveal (toutes pages)

```javascript
// ── IntersectionObserver pour .reveal ──
function initScrollReveal() {
    const obs = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('visible');
                obs.unobserve(e.target); // un seul déclenchement
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale')
        .forEach(el => obs.observe(el));
}
```

### KPI Animated Counter

```javascript
// ── Compteur animé pour les valeurs KPI ──
function animateCounter(el, target, duration = 1200) {
    const start = performance.now();
    const from  = parseInt(el.textContent) || 0;

    function step(now) {
        const progress = Math.min((now - start) / duration, 1);
        const ease = 1 - Math.pow(1 - progress, 3); // ease out cubic
        el.textContent = Math.round(from + (target - from) * ease);
        if (progress < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
}

// Utilisation :
// animateCounter(document.querySelector('.kpi-value'), 85);
```

### Progress Bar Animée

```javascript
// ── Animer toutes les progress bars ──
function animateProgressBars() {
    document.querySelectorAll('.progress-fill[data-w], .kbf[data-w]').forEach(el => {
        el.style.width = el.dataset.w + '%';
    });
}
// Appeler après injection du HTML dynamique
```

### Score Ring SVG

```javascript
// ── Animer un gauge circulaire SVG ──
function animateGauge(svgEl, score) {
    const fill = svgEl.querySelector('.gauge-fill');
    if (!fill) return;
    const circumference = 188; // 2 * π * 30 (r=30)
    const offset = circumference * (1 - score / 100);
    setTimeout(() => {
        fill.style.strokeDashoffset = offset;
    }, 300);
}
```

### Nav Scroll Effect

```javascript
// ── Effet nav au scroll ──
function initNavScroll(navId = 'main-nav') {
    const nav = document.getElementById(navId);
    if (!nav) return;
    window.addEventListener('scroll', () => {
        nav.classList.toggle('scrolled', window.scrollY > 40);
    }, { passive: true });
}
```

### Chart.js Config Dark

```javascript
// ── Configuration globale Chart.js dark ──
const chartDarkDefaults = {
    color: '#5A6B82',
    borderColor: 'rgba(255, 255, 255, 0.04)',
    plugins: {
        legend: {
            labels: {
                color: '#5A6B82',
                font: { family: 'DM Mono', size: 10 },
                boxWidth: 10,
            },
        },
        tooltip: {
            backgroundColor: '#0D1424',
            borderColor: 'rgba(0, 255, 136, 0.2)',
            borderWidth: 1,
            titleColor: '#00FF88',
            bodyColor: '#DDE6F0',
            titleFont: { family: 'DM Mono', size: 11 },
            bodyFont: { family: 'DM Mono', size: 11 },
            padding: 12,
            cornerRadius: 4,
        },
    },
    scales: {
        x: {
            grid: { color: 'rgba(255, 255, 255, 0.04)', drawBorder: false },
            ticks: { color: '#5A6B82', font: { family: 'DM Mono', size: 10 } },
        },
        y: {
            grid: { color: 'rgba(255, 255, 255, 0.04)', drawBorder: false },
            ticks: { color: '#5A6B82', font: { family: 'DM Mono', size: 10 } },
        },
    },
};

// Appliquer :
// const chart = new Chart(ctx, { type: 'line', data: {...}, options: { ...chartDarkDefaults, ... } });
```

### Alpine.js App principale

```javascript
// ── App Alpine pour la page d'analyse ──
function analyzerApp() {
    return {
        q: '', loading: false, err: null, sa: 0, _t: null,
        steps: [
            'GEMINI // Connexion Google Search...',
            'SCAN   // Détection présence digitale...',
            'GROQ   // Analyse potentiel de croissance...',
            'BUILD  // Génération recommandations...',
        ],
        async run() {
            const query = this.q.trim();
            if (this.loading || query.length < 2) return;
            this.loading = true; this.err = null; this.sa = 0;
            document.getElementById('result-zone').innerHTML = '';
            clearInterval(this._t);
            this._t = setInterval(() => {
                if (this.sa < this.steps.length - 1) this.sa++;
            }, 2000);
            try {
                const r = await fetch('/analyser', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ entreprise: query }),
                });
                const d = await r.json();
                clearInterval(this._t);
                if (d.success) {
                    document.getElementById('result-zone').innerHTML = d.html;
                    setTimeout(() => {
                        animateProgressBars();
                        document.querySelectorAll('.kpi-card').forEach((el, i) => {
                            setTimeout(() => el.classList.add('loaded'), i * 90);
                        });
                        document.getElementById('result-zone')
                            .scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 80);
                } else {
                    this.err = d.upgrade
                        ? 'QUOTA ATTEINT — Passez au plan supérieur.'
                        : d.message || 'Erreur inconnue.';
                }
            } catch (e) {
                clearInterval(this._t);
                this.err = 'ERREUR RÉSEAU — Vérifiez votre connexion.';
            } finally {
                this.loading = false;
            }
        },
    };
}
```

---

## Récapitulatif — Appels JS par page

| Page | Canvas | Cursor | Scroll Reveal | Counter | Nav Scroll |
|------|--------|--------|--------------|---------|-----------|
| `welcome.blade`   | ✓ | ✓ | ✓ | — | ✓ |
| `login.blade`     | ✓ | ✓ | — | — | — |
| `register.blade`  | ✓ | ✓ | — | — | — |
| `index.blade`     | ✓ | — | ✓ | ✓ | ✓ |
| `show.blade`      | — | — | ✓ | ✓ | ✓ |
| `dashboard.blade` | — | — | ✓ | ✓ | — |
| `admin.blade`     | — | — | ✓ | ✓ | — |
| `subscription.blade` | ✓ | — | ✓ | — | ✓ |

```javascript
// ── Initialisation globale dans layouts/app.blade.php ──
document.addEventListener('DOMContentLoaded', () => {
    initNavScroll('main-nav');
    initScrollReveal();
    initBgCanvas('bg-canvas');
    initCursor();
    animateProgressBars();
});
```

---

## Checklist d'implémentation

```
□ Importer les 3 fonts Google (Bebas Neue + DM Mono + Syne)
□ Coller les :root variables dans app.css
□ Ajouter #bg-canvas, #cursor, #cursor-ring dans layouts/app.blade.php
□ Inclure <div class="bg-grid"></div> et <div class="bg-noise"></div>
□ Inclure <div class="scan-line"></div>
□ Ajouter meta[name="csrf-token"] dans <head>
□ Charger Alpine.js via CDN dans le layout
□ Charger Chart.js via CDN dans le layout
□ Appliquer body { cursor: none } sur les pages avec cursor personnalisé
□ Vérifier que tous les .reveal ont l'observer JS
□ Tester les transitions mobile (sidebar collapse, grid responsive)
□ Vérifier Chart.js avec chartDarkDefaults sur tous les graphiques
```

---

*BIA Design System v1.0 — Dark Luxury Terminal — DBS 2025*