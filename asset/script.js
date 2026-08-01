// Placeholder for page-specific JS. Kept minimal so header/footer lucide calls still work.
document.addEventListener('DOMContentLoaded', function(){
    initHeroSlider();
    // initialize lucide icons if script is loaded after DOM
    if (window.lucide && typeof lucide === 'object' && lucide.createIcons) lucide.createIcons();
});

/* Simple client-side view router (for in-page views) */
function navigateTo(viewId){
    document.querySelectorAll('.page-view').forEach(v=>v.classList.add('hidden'));
    const el = document.getElementById('view-' + viewId);
    if (el) el.classList.remove('hidden');
}

/* Hero slider (simple timed carousel) */
const HERO_SLIDES = [
    {badge: 'Flotte Opérationnelle & Expertise', text: 'Ingénieurs congolais hautement qualifiés et engagés QSE.', image: 'assets/images/slide1.jpg'},
    {badge: 'Projets Durables', text: 'Des infrastructures pensées pour le long terme.', image: 'assets/images/slide2.jpg'},
    {badge: 'Gestion Intégrée', text: 'Assainissement, collecte et valorisation des déchets.', image: 'assets/images/slide3.jpg'}
];
let heroIndex = 0;
let heroTimer = null;

function renderHero(){
    const container = document.getElementById('hero-slides-container');
    const badge = document.getElementById('hero-slide-badge');
    const text = document.getElementById('hero-slide-text');
    const dots = document.getElementById('hero-dots-container');
    if (!container || !badge || !text || !dots) return;

    // create new image element and animate
    const slide = HERO_SLIDES[heroIndex];
    const img = document.createElement('img');
    img.src = slide.image;
    img.alt = slide.badge || 'slide';
    img.className = 'hero-slide-img hero-slide-enter';
    container.appendChild(img);

    // trigger transition to active
    // force reflow
    void img.offsetWidth;
    img.classList.remove('hero-slide-enter');
    img.classList.add('hero-slide-active');

    // mark existing images as exit (except the one we just added)
    Array.from(container.querySelectorAll('img')).forEach(el=>{
        if (el === img) return;
        el.classList.remove('hero-slide-active');
        el.classList.add('hero-slide-exit');
        el.addEventListener('transitionend', ()=>{ try{ el.remove(); }catch(e){} }, { once: true });
    });

    badge.textContent = slide.badge;
    text.textContent = slide.text;

    dots.innerHTML = HERO_SLIDES.map((s,i)=>`<button onclick="goToHero(${i})" class="${i===heroIndex? 'bg-emerald-500':'bg-slate-500'}"></button>`).join('');
}

function nextHeroSlide(){ heroIndex = (heroIndex + 1) % HERO_SLIDES.length; renderHero(); resetHeroTimer(); }
function prevHeroSlide(){ heroIndex = (heroIndex - 1 + HERO_SLIDES.length) % HERO_SLIDES.length; renderHero(); resetHeroTimer(); }
function goToHero(i){ heroIndex = i; renderHero(); resetHeroTimer(); }

function initHeroSlider(){ renderHero(); resetHeroTimer(); }

function resetHeroTimer(){ if (heroTimer) clearInterval(heroTimer); heroTimer = setInterval(()=>{ nextHeroSlide(); }, 5000); }

/* AI chat toggles and simple handler (stub) */
function toggleAIChat(){ const box = document.getElementById('ai-chat-box'); if (!box) return; box.classList.toggle('hidden'); }

function handleAIChatSubmit(e){ e.preventDefault(); const input = document.getElementById('ai-input'); const messages = document.getElementById('ai-chat-messages'); if (!input || !messages) return; const v = input.value.trim(); if(!v) return; const el = document.createElement('div'); el.className = 'bg-emerald-50 p-3 rounded-xl max-w-[85%] self-end text-xs text-slate-800'; el.textContent = v; messages.appendChild(el); input.value = '';
    const loader = document.getElementById('ai-loader'); if (loader) loader.classList.remove('hidden');
    setTimeout(()=>{ if (loader) loader.classList.add('hidden'); const reply = document.createElement('div'); reply.className='bg-slate-100 p-3 rounded-xl max-w-[85%] self-start text-xs'; reply.textContent = 'Merci — nous avons bien reçu votre question. Un membre de l’équipe vous répondra bientôt.'; messages.appendChild(reply); messages.scrollTop = messages.scrollHeight; }, 900);
}

