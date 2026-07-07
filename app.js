document.addEventListener('DOMContentLoaded',function(){(function(){var sectionIds=['about','realisations','contact'];var sections=sectionIds.map(function(id){return document.getElementById(id);}).filter(Boolean);var navLinks=document.querySelectorAll('.navbar-links a');var navList=document.querySelector('.navbar-links-wrap');var navIndicator=document.getElementById('navIndicator');function clearActive(){navLinks.forEach(function(link){link.classList.remove('actif');});}
function moveIndicatorTo(link,instant){if(!navIndicator||!navList||!link)return;if(getComputedStyle(navIndicator).display==='none')return;var listRect=navList.getBoundingClientRect();var linkRect=link.getBoundingClientRect();var center=(linkRect.left-listRect.left)+(linkRect.width/2);if(instant){navIndicator.style.transition='none';}
navIndicator.style.left=center+'px';navIndicator.classList.add('is-visible');if(instant){void navIndicator.offsetWidth;navIndicator.style.transition='';}}
function setActive(id,instant){clearActive();var activeLink=null;navLinks.forEach(function(link){var href=link.getAttribute('href')||'';var isMatch=(id==='home'&&(href==='index.html'||href==='./index.html'))||(id==='realisations'&&href.indexOf('#realisations')!==-1)||(id!=='home'&&id!=='realisations'&&href.indexOf('#'+id)!==-1);if(isMatch){link.classList.add('actif');activeLink=link;}});moveIndicatorTo(activeLink,instant);}
var currentId='home';var navLockUntil=0;var observer=new IntersectionObserver(function(entries){if(Date.now()<navLockUntil)return;entries.forEach(function(entry){if(entry.isIntersecting){currentId=entry.target.id;setActive(currentId);}});},{rootMargin:'-40% 0px -40% 0px',threshold:0});sections.forEach(function(section){observer.observe(section);});window.addEventListener('scroll',function(){if(Date.now()<navLockUntil)return;if(window.scrollY<10){setActive('home');}});setActive('home',true);window.addEventListener('resize',function(){moveIndicatorTo(document.querySelector('.navbar-links a.actif'),true);});window.addEventListener('load',function(){moveIndicatorTo(document.querySelector('.navbar-links a.actif'),true);});var homeLink=document.querySelector('.navbar-links a[href="index.html"]');if(homeLink){homeLink.addEventListener('click',function(e){e.preventDefault();navLockUntil=Date.now()+1000;window.scrollTo({top:0,behavior:'smooth'});setActive('home');});}
navLinks.forEach(function(link){var href=link.getAttribute('href')||'';if(href==='index.html'||href==='./index.html')return;var hashIndex=href.indexOf('#');if(hashIndex===-1)return;var targetId=href.slice(hashIndex+1);link.addEventListener('click',function(){navLockUntil=Date.now()+1000;setActive(targetId);});});var hamburger=document.getElementById('navbarHamburger');var links=document.getElementById('navbarLinks');var hamburgerIcon='<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>';var closeIcon='<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="5" y1="5" x2="19" y2="19"></line><line x1="19" y1="5" x2="5" y2="19"></line></svg>';if(hamburger&&links){hamburger.addEventListener('click',function(e){e.preventDefault();e.stopPropagation();var isOpen=links.classList.toggle('open');hamburger.setAttribute('aria-expanded',isOpen?'true':'false');hamburger.innerHTML=isOpen?closeIcon:hamburgerIcon;document.body.classList.toggle('menu-open',isOpen);});links.querySelectorAll('a').forEach(function(a){a.addEventListener('click',function(){links.classList.remove('open');hamburger.setAttribute('aria-expanded','false');hamburger.innerHTML=hamburgerIcon;document.body.classList.remove('menu-open');});});}})();(function(){if(window.location.search.indexOf('contact=')!==-1){var cleanUrl=window.location.pathname+window.location.hash;window.history.replaceState(null,'',cleanUrl);}})();});(function(){var timelineEl=document.querySelector('.timeline');var firstItem=document.querySelector('.timeline-item');var lastItem=document.querySelector('.timeline-item:last-child');var lastSubtitle=lastItem?lastItem.querySelector('.timeline-subtitle'):null;function topRelativeTo(el,ancestor){var top=0;while(el&&el!==ancestor){top+=el.offsetTop;el=el.offsetParent;}return top;}
if(timelineEl&&firstItem&&lastItem){var glow=document.querySelector('.timeline-glow')||document.createElement('div');if(!glow.parentNode){glow.className='timeline-glow';timelineEl.appendChild(glow);}var glowTarget=0;function setGlowTarget(){var start=topRelativeTo(firstItem,timelineEl)+22;var stopAt=lastSubtitle?(topRelativeTo(lastSubtitle,timelineEl)+lastSubtitle.offsetHeight/2):(topRelativeTo(lastItem,timelineEl)+14);glowTarget=stopAt-start;glow.style.top=start+'px';if(!glow.classList.contains('in-view')){glow.style.height='0px';}else{glow.style.height=glowTarget+'px';}}
setGlowTarget();window.addEventListener('resize',setGlowTarget);var timelineObserver=new IntersectionObserver(function(entries){entries.forEach(function(entry){if(entry.isIntersecting){glow.classList.add('in-view');requestAnimationFrame(function(){glow.style.height=glowTarget+'px';});timelineObserver.unobserve(entry.target);}});},{threshold:0.15});timelineObserver.observe(firstItem);}})();(function(){var trigger=document.getElementById("videoTrigger");var video=document.getElementById("myVideo");if(trigger&&video){trigger.addEventListener("click",function(){trigger.style.display="none";video.style.display="block";video.play();});}})();
(function(){
    var el = document.querySelector('.social-float');
    var header = document.querySelector('header');
    if (!el || !header) return;
    var mq = window.matchMedia('(max-width:640px)');
    var initialTop = null;
    var ticking = false;

    function update(){
        if (mq.matches){
            // mobile: positioned via CSS (absolute inside header), nothing to compute
            ticking = false;
            return;
        }
        el.style.display = '';
        if (initialTop === null){
            initialTop = el.getBoundingClientRect().top;
        }
        var vh = window.innerHeight;
        var h = el.offsetHeight;
        var homeHeight = header.offsetHeight;
        var centerTop = (vh - h) / 2;
        var progress = Math.min(Math.max(window.scrollY / homeHeight, 0), 1);
        var top = initialTop + (centerTop - initialTop) * progress;
        el.style.bottom = 'auto';
        el.style.top = top + 'px';
        ticking = false;
    }

    function onScroll(){
        if (!ticking){
            requestAnimationFrame(update);
            ticking = true;
        }
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', function(){
        initialTop = null;
        update();
    });
    window.addEventListener('load', function(){
        initialTop = null;
        update();
    });
    update();
})();