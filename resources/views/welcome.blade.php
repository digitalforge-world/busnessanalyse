<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Business Intelligence Analyzer — L'Audit Stratégique Haute Performance</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Mono:ital,wght@0,300;0,400;0,500;1,300&family=Syne:wght@300;400;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
</head>
<body>

<!-- Cursor -->
<div id="cursor"></div>
<div id="cursor-ring"></div>

<!-- Background Elements -->
<canvas id="cvs"></canvas>
<div class="noise"></div>
<div class="grid-bg"></div>
<div class="scan"></div>

<!-- ══════════ NAV ══════════ -->
<nav id="nav">
  <a href="/" class="logo">
    <div class="logo-dot"></div>
    BIA
  </a>
  <div class="nav-mid">
    <a href="#solutions" class="nav-pill">Solutions</a>
    <a href="#fonctionnalites" class="nav-pill">Services</a>
    <a href="#tarifs" class="nav-pill">Tarifs</a>
  </div>
  <div style="display:flex;align-items:center;gap:8px">
    @auth
        <a href="{{ route('analysis.index') }}" class="nav-cta">Tableau de bord</a>
    @else
        <a href="/login" class="nav-pill" style="text-decoration:none">Connexion</a>
        <a href="/register" class="nav-cta">Accès Gratuit</a>
    @endauth
  </div>
</nav>

<!-- ══════════ HERO ══════════ -->
<section class="hero">
  <div class="hero-orb orb1"></div>
  <div class="hero-orb orb2"></div>
  <div class="hero-orb orb3"></div>
  <div class="hero-line"></div>

  <div class="hero-content">
    <div class="hero-badge">
      <div class="badge-blink"></div>
      Standard Mondial en Intelligence Commerciale
    </div>

    <h1 class="hero-h1">
      <span class="h1-line1">Pilotez</span>
      <span class="h1-line2">Votre Vision</span>
      <span class="h1-line3">Stratégique</span>
    </h1>

    <p class="hero-desc">
      Une expertise analytique instantanée pour n'importe quelle entité économique à l'échelle mondiale.
      Identifiez les leviers de croissance, analysez la concurrence internationale et générez des rapports stratégiques de haute valeur en un clic.
    </p>

    <div class="hero-btns">
      @auth
        <a href="{{ route('analysis.index') }}" class="btn-primary">Lancer un Audit →</a>
      @else
        <a href="/register" class="btn-primary">DÉPLOYER_SYSTÈME →</a>
        <a href="#solutions" class="btn-ghost">LOGIQUE_PLATEFORME</a>
      @endauth
    </div>

    <div class="hero-note">Essai gratuit · 3 analyses offertes · Sans engagement</div>

    <div class="hero-stats">
      <div class="stat-item">
        <div class="stat-n">30<span>s</span></div>
        <div class="stat-l">Temps d'Audit Moyen</div>
      </div>
      <div class="stat-item">
        <div class="stat-n">100<span>%</span></div>
        <div class="stat-l">Rapports Actionnables</div>
      </div>
      <div class="stat-item">
        <div class="stat-n">150<span>+</span></div>
        <div class="stat-l">Territoires Couverts</div>
      </div>
      <div class="stat-item">
        <div class="stat-n">∞<span></span></div>
        <div class="stat-l">Précision des Insights</div>
      </div>
    </div>
  </div>

  <div class="hero-visual">
    <div class="mockup-wrapper">
        <img src="{{ asset('assets/dashboard.png') }}" alt="Focus BIA Dashboard" class="mockup-img">
    </div>
  </div>
</section>

<!-- ══════════ TICKER ══════════ -->
<div class="ticker-wrap">
  <div class="ticker-track" id="ticker"></div>
</div>

<!-- ══════════ SOLUTIONS / PROFILES ══════════ -->
<div class="section reveal" id="solutions">
  <div class="sec-label">Écosystème</div>
  <h2 class="sec-title">Une solution pour<br><span>6 profils experts.</span></h2>
  <div class="steps">
    <div class="step">
      <div class="step-icon">💼</div>
      <div class="step-title">Agences & Freelances</div>
      <div class="step-desc">Analysez n'importe quel prospect avant votre premier rendez-vous. Le rapport PDF devient un livrable stratégique que vous pouvez facturer directement à vos clients.</div>
      <div class="step-tag">PROSPECTION · DELIVERABLE</div>
    </div>
    <div class="step">
      <div class="step-icon">👔</div>
      <div class="step-title">Consultants Business</div>
      <div class="step-desc">Réalisez des audits digitaux ultra-rapides. En 30 secondes, obtenez un rapport complet qui justifie la valeur de vos interventions et vos tarifs de conseil.</div>
      <div class="step-tag">AUDIT · EXPERTISE</div>
    </div>
    <div class="step">
      <div class="step-icon">🚀</div>
      <div class="step-title">Entrepreneurs & PME</div>
      <div class="step-desc">Situez-vous instantanément face à vos leaders sectoriels. Indispensable pour les PME n'ayant pas accès aux outils de Business Intelligence traditionnels.</div>
      <div class="step-tag">CROISSANCE · POSITIONNEMENT</div>
    </div>
    <div class="step">
      <div class="step-icon">📲</div>
      <div class="step-title">Équipes Commerciales</div>
      <div class="step-desc">Qualifiez vos leads avant chaque interaction. Envoyez le résumé analytique directement via WhatsApp pour accélérer vos cycles de vente sur les marchés mobiles.</div>
      <div class="step-tag">SALES ENABLEMENT · WHATSAPP</div>
    </div>
    <div class="step">
      <div class="step-icon">💰</div>
      <div class="step-title">Investisseurs & VCs</div>
      <div class="step-desc">Effectuez une due diligence préliminaire sur n'importe quelle cible. Évaluez la maturité digitale et le rayonnement d'une entreprise avant d'engager des capitaux.</div>
      <div class="step-tag">DUE DILIGENCE · VC FLOW</div>
    </div>
    <div class="step">
      <div class="step-icon">🎓</div>
      <div class="step-title">Intelligence Territoriale</div>
      <div class="step-desc">Étudiants, chercheurs et journalistes économiques : générez des études de cas approfondies et des graphiques de performance pour vos analyses de marchés complexes.</div>
      <div class="step-tag">RESEARCH · OPEN DATA</div>
    </div>
  </div>
</div>

<!-- ══════════ HOW IT WORKS ══════════ -->
<div class="section reveal" id="fonctionnalites">
  <div class="sec-label">Le Processus</div>
  <h2 class="sec-title">De la data brute<br><span>à la stratégie pure.</span></h2>
  <div class="steps">
    <div class="step">
      <div class="step-n">01</div>
      <div class="step-icon">🔍</div>
      <div class="step-title">Scan Global</div>
      <div class="step-desc">Capture de l'empreinte numérique totale. Analyse de la visibilité, du rayonnement et de la présence sur les réseaux.</div>
      <div class="step-tag">SEMANTIC MINING</div>
    </div>
    <div class="step">
      <div class="step-n">02</div>
      <div class="step-icon">🧩</div>
      <div class="step-title">Intelligence Core</div>
      <div class="step-desc">Nos modèles croisent les données pour détecter les anomalies de marché et les opportunités de croissance inexploitées.</div>
      <div class="step-tag">AI REASONING</div>
    </div>
    <div class="step">
      <div class="step-n">03</div>
      <div class="step-icon">📊</div>
      <div class="step-title">Action Plan</div>
      <div class="step-desc">Livrable final segmenté : Scoring Digital, Audit Concurentiel, Chronogramme et Recommandations prioritaires.</div>
      <div class="step-tag">OUTPUT EXPORT</div>
    </div>
  </div>
</div>

<!-- ══════════ FEATURES HIGHLIGHT ══════════ -->
<div style="position:relative;z-index:10;padding:0 56px 100px;max-width:1200px;margin:0 auto" class="reveal">
  <div class="features-grid">
    <div class="feat-card">
      <div class="feat-ico">📄</div>
      <div class="feat-title">Rapports Pro PDF</div>
      <div class="feat-desc">Générez des dossiers de 5+ pages, parfaits pour l'envoi immédiat par mail ou lors d'une présentation stratégique.</div>
      <span class="feat-badge badge-free">VALEUR EXTRÊME</span>
    </div>
    <div class="feat-card">
      <div class="feat-ico">💬</div>
      <div class="feat-title">Intégration WhatsApp</div>
      <div class="feat-desc">L'outil indispensable pour les marchés réactifs. Envoyez vos insights directement sur le canal favori de vos clients.</div>
      <span class="feat-badge badge-paid">PRO +</span>
    </div>
    <div class="feat-card">
      <div class="feat-ico">⚔️</div>
      <div class="feat-title">Veille Concurrentielle</div>
      <div class="feat-desc">Ne restez jamais dans le flou. Voyez exactement ce que font vos concurrents et comment ils performent.</div>
      <span class="feat-badge badge-paid">VISIBILITÉ TOTALE</span>
    </div>
  </div>
</div>

<!-- ══════════ PRICING ══════════ -->
<div style="position:relative;z-index:10;padding:100px 56px;max-width:1200px;margin:0 auto" class="reveal" id="tarifs">
  <div class="sec-label">Investissement</div>
  <h2 class="sec-title">Agile.<br><span>Évolutif.</span></h2>
  <div class="pricing-grid">
    <div class="plan-card">
      <div class="plan-name">Free</div>
      <div class="plan-price">$0</div>
      <div class="plan-period">POUR DÉBUTER</div>
      <ul class="plan-features">
        <li>3 analyses / mois</li>
        <li>Rapports Web Complets</li>
        <li>Insights Business</li>
        <li class="off">Exports PDF</li>
        <li class="off">Analyse Concurrents</li>
      </ul>
      <a href="/register" class="plan-btn outline">Lancer l'Essai</a>
    </div>
    <div class="plan-card">
      <div class="plan-name">Starter</div>
      <div class="plan-price">$10<span>/mo</span></div>
      <div class="plan-period">PROPRIÉTAIRE</div>
      <ul class="plan-features">
        <li>30 rapports / mois</li>
        <li>Exports PDF illimités</li>
        <li>Suivi des Concurrents</li>
        <li>Historique Évolutif</li>
        <li class="off">WhatsApp Delivery</li>
      </ul>
      <a href="/register?plan=starter" class="plan-btn outline">Choisir Starter</a>
    </div>
    <div class="plan-card featured">
      <div class="plan-name">Pro</div>
      <div class="plan-price">$29<span>/mo</span></div>
      <div class="plan-period">LE CHOIX DES AGENCES</div>
      <ul class="plan-features">
        <li>Analyses Illimitées</li>
        <li>Marque Blanche PDF</li>
        <li>Analyse Concurrentielle</li>
        <li>WhatsApp Business API</li>
        <li>Support Prioritaire</li>
      </ul>
      <a href="/register?plan=pro" class="plan-btn solid">Choisir Pro</a>
    </div>
    <div class="plan-card">
      <div class="plan-name">Agency</div>
      <div class="plan-price">$79<span>/mo</span></div>
      <div class="plan-period">FLOTTE D'AGENCE</div>
      <ul class="plan-features">
        <li>Tout du plan Pro</li>
        <li>Accès Multi-Comptes</li>
        <li>Intégration API</li>
        <li>Dashboard Central</li>
        <li>Volume Dédié</li>
      </ul>
      <a href="/register?plan=agency" class="plan-btn outline">Contactez-nous</a>
    </div>
  </div>
</div>

<!-- ══════════ CTA ══════════ -->
<div class="cta-section reveal">
  <div class="cta-inner">
    <h2 class="cta-title">Prenez une longueur<br><span class="g">d'avance</span></h2>
    <p class="cta-sub">Rejoignez des milliers de professionnels utilisant l'intelligence décisionnelle pour transformer leurs données en opportunités.</p>
    <div class="cta-btns">
      @auth
        <a href="{{ route('analysis.index') }}" class="btn-primary">Retour au Dashboard →</a>
      @else
        <a href="/register" class="btn-primary">S'inscrire Gratuitement →</a>
        <a href="/login" class="btn-ghost">Connexion Membre</a>
      @endauth
    </div>
  </div>
</div>

<!-- ══════════ FOOTER ══════════ -->
<footer>
  <div class="footer-logo">BIA <span class="opacity-30">SYSTEM</span></div>
  <div class="footer-links">
    <a href="#solutions">PROTOCOLES</a>
    <a href="#fonctionnalites">SERVICES</a>
    <a href="#tarifs">LICENCES</a>
  </div>
  <div class="font-mono opacity-50">© 2026 // BIA_GLOBAL_INTELLIGENCE // CRYPTOGRAPHIC_SECURE_ACCESS</div>
</footer>

<script>
/* ── Scripts Core ── */
const cur=document.getElementById('cursor');
const ring=document.getElementById('cursor-ring');
let mx=0,my=0,rx=0,ry=0;
document.addEventListener('mousemove',e=>{
  mx=e.clientX;my=e.clientY;
  cur.style.left=mx+'px';cur.style.top=my+'px';
});
(function animRing(){
  rx+=(mx-rx)*.12;ry+=(my-ry)*.12;
  ring.style.left=rx+'px';ring.style.top=ry+'px';
  requestAnimationFrame(animRing);
})();

(function(){
  const cvs=document.getElementById('cvs');
  const ctx=cvs.getContext('2d');
  let W,H,pts=[],lines=[];
  function sz(){W=cvs.width=innerWidth;H=cvs.height=innerHeight;}
  function Pt(){this.reset()}
  Pt.prototype.reset=function(){
    this.x=Math.random()*W;this.y=Math.random()*H;
    this.r=Math.random()*.8+.15;
    this.vx=(Math.random()-.5)*.2;this.vy=(Math.random()-.5)*.2;
    this.a=Math.random()*.35+.06;
    this.c=Math.random()>.6?'#00E5FF':'#00FF88';
  };
  Pt.prototype.tick=function(){this.x+=this.vx;this.y+=this.vy;if(this.x<0||this.x>W||this.y<0||this.y>H)this.reset()};
  Pt.prototype.draw=function(){ctx.globalAlpha=this.a;ctx.fillStyle=this.c;ctx.beginPath();ctx.arc(this.x,this.y,this.r,0,Math.PI*2);ctx.fill()};
  function mkLines(){
    lines=[];const n=Math.floor(W/50);
    for(let i=0;i<n;i++) lines.push({
      x:i*50+Math.random()*25,y:Math.random()*H,spd:Math.random()*.4+.1,
      chars:Array.from({length:8},()=>Math.random().toString(36)[2]),
      op:Math.random()*.055+.01
    });
  }
  function drawLines(){
    ctx.font='10px DM Mono,monospace';
    lines.forEach(l=>{
      l.chars.forEach((c,i)=>{
        ctx.globalAlpha=l.op*(1-i/l.chars.length);ctx.fillStyle='#00FF88';ctx.fillText(c,l.x,l.y-i*12);
      });
      l.y+=l.spd;if(l.y>H+90){l.y=-80;l.x=Math.random()*W;}
      if(Math.random()<.02)l.chars[0]=Math.random().toString(36)[2];
    });
  }
  function loop(){
    ctx.clearRect(0,0,W,H);drawLines();
    pts.forEach(p=>{p.tick();p.draw();});
    requestAnimationFrame(loop);
  }
  sz();mkLines();for(let i=0;i<60;i++)pts.push(new Pt());
  window.addEventListener('resize',()=>{sz();mkLines();});loop();
})();

(function(){
  const companies=[
    {name:'Apple Inc',      c:'g'},{name:'Google LLC',     c:'c'},
    {name:'Microsoft',       c:'a'},{name:'Amazon.com',     c:'g'},
    {name:'Meta Platforms',  c:'c'},{name:'Tesla, Inc',    c:'a'},
    {name:'NVIDIA Corp',     c:'g'},{name:'Samsung Group',  c:'c'},
    {name:'LVMH Moët',       c:'a'},{name:'Coca-Cola Co',   c:'g'},
    {name:'Visa Inc',        c:'c'},{name:'Walmart',        c:'a'}
  ];
  const track=document.getElementById('ticker');
  const html=([...companies,...companies]).map(c=>
    `<div class="ticker-item"><div class="ticker-dot ${c.c}"></div>${c.name}</div>`
  ).join('');
  track.innerHTML=html;
})();

window.addEventListener('scroll',()=>{document.getElementById('nav').classList.toggle('scrolled',scrollY>40);});
(function(){
  const obs=new IntersectionObserver(entries=>{
    entries.forEach(e=>{if(e.isIntersecting)e.target.classList.add('visible')});
  },{threshold:.1});
  document.querySelectorAll('.reveal').forEach(el=>obs.observe(el));
})();
</script>

</body>
</html>
