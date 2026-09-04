import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
  const header = document.querySelector('[data-site-header]');
  const toggle = header?.querySelector('[data-menu-toggle]');
  const navigation = header?.querySelector('[data-site-nav]');

  const normalizePath = (path) => {
    const normalized = `/${String(path || '').replace(/^\/+|\/+$/g, '')}`;
    return normalized === '/' ? normalized : normalized.replace(/\/$/, '');
  };

  const currentPath = normalizePath(window.location.pathname);
  navigation?.querySelectorAll('a.after\\:w-0[href]').forEach((link) => {
    const target = new URL(link.href, window.location.origin);

    if (target.origin === window.location.origin && normalizePath(target.pathname) === currentPath) {
      link.classList.add('after:!w-full');
      link.setAttribute('aria-current', 'page');
    }
  });

  toggle?.addEventListener('click', () => {
    const isOpen = navigation.classList.toggle('max-mobile:!flex');
    toggle.setAttribute('aria-expanded', String(isOpen));
    toggle.setAttribute('aria-label', isOpen ? 'Zavřít navigaci' : 'Otevřít navigaci');
  });

  navigation?.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => {
    navigation.classList.remove('max-mobile:!flex');
    toggle?.setAttribute('aria-expanded', 'false');
    toggle?.setAttribute('aria-label', 'Otevřít navigaci');
  }));

  document.querySelector('[data-scroll-top]')?.addEventListener('click', (event) => {
    event.preventDefault();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  document.querySelectorAll('[data-copy-article-link]').forEach((button) => {
    button.addEventListener('click', async () => {
      try {
        await navigator.clipboard.writeText(button.dataset.copyArticleLink || window.location.href);
        button.setAttribute('aria-label', 'Odkaz zkopírován');
        window.setTimeout(() => button.setAttribute('aria-label', 'Kopírovat odkaz'), 1800);
      } catch (error) {
        button.setAttribute('aria-label', 'Odkaz se nepodařilo zkopírovat');
      }
    });
  });

  const inactiveFilterClasses = ['bg-transparent', 'text-nav-ink', 'border-control-line'];
  const activeFilterClasses = ['bg-wine', 'text-white', 'border-wine'];
  document.querySelectorAll('[data-filter]').forEach((button) => {
    button.addEventListener('click', () => {
      const bar = button.closest('[data-filter-group]');
      bar?.querySelectorAll('[data-filter]').forEach((item) => {
        item.classList.remove(...activeFilterClasses);
        item.classList.add(...inactiveFilterClasses);
      });
      button.classList.remove(...inactiveFilterClasses);
      button.classList.add(...activeFilterClasses);
      const target = bar?.dataset.target ? document.querySelector(bar.dataset.target) : bar?.nextElementSibling;
      if (!target) return;
      const filter = button.dataset.filter;
      target.querySelectorAll('[data-category]').forEach((item) => {
        item.classList.toggle('hidden', filter !== 'all' && item.dataset.category !== filter);
      });
    });
  });

  function makeSlider({ trackId, prevId, nextId, dotsId, auto = 4000 }) {
    const track = document.getElementById(trackId);
    if (!track) return;
    const slides = [...track.children];
    if (!slides.length) return;
    let index = 0;
    const perView = () => window.innerWidth >= 1024 ? Math.min(4, slides.length) : window.innerWidth >= 768 ? Math.min(2, slides.length) : 1;
    const maxIndex = () => Math.max(0, slides.length - perView());

    function render() {
      const count = perView();
      const widthClass = count === 4 ? 'w-1/4' : count === 2 ? 'w-1/2' : 'w-full';
      slides.forEach((slide) => {
        slide.classList.remove('w-full', 'w-1/2', 'w-1/4');
        slide.classList.add(widthClass, 'shrink-0');
      });
      track.style.transform = `translateX(-${index * (100 / count)}%)`;
      document.getElementById(dotsId)?.querySelectorAll('button').forEach((dot, dotIndex) => {
        dot.classList.toggle('bg-orange', dotIndex === index);
        dot.classList.toggle('bg-white/30', dotIndex !== index);
      });
      document.getElementById(prevId)?.classList.toggle('opacity-30', index === 0);
      document.getElementById(nextId)?.classList.toggle('opacity-30', index >= maxIndex());
    }

    const move = (direction) => { index = Math.max(0, Math.min(maxIndex(), index + direction)); render(); };
    document.getElementById(prevId)?.addEventListener('click', () => move(-1));
    document.getElementById(nextId)?.addEventListener('click', () => move(1));
    const dots = document.getElementById(dotsId);
    if (dots) {
      dots.innerHTML = '';
      for (let i = 0; i <= maxIndex(); i += 1) {
        const dot = document.createElement('button');
        dot.type = 'button';
        dot.className = 'h-2 w-2 cursor-pointer rounded-full border-0 p-0 transition-colors duration-200';
        dot.addEventListener('click', () => { index = i; render(); });
        dots.appendChild(dot);
      }
    }
    render();
    window.addEventListener('resize', () => { index = Math.min(index, maxIndex()); render(); });
    if (auto && maxIndex() > 0) setInterval(() => { index = index >= maxIndex() ? 0 : index + 1; render(); }, auto);
  }

  function makePartnerSlider() {
    const track = document.getElementById('partnersSliderTrack');
    if (!track) return;
    const originals = [...track.children];
    if (!originals.length) return;
    const prev = document.getElementById('partnersPrev');
    const next = document.getElementById('partnersNext');
    const dots = document.getElementById('partnersDots');
    let index = 0;
    const perView = () => window.innerWidth < 768 ? 2 : window.innerWidth < 1024 ? 3 : 5;

    function render(animate = true) {
      const count = perView();
      const widthClass = count === 5 ? 'w-1/5' : count === 3 ? 'w-1/3' : 'w-1/2';
      const basisClass = count === 5 ? 'basis-1/5' : count === 3 ? 'basis-1/3' : 'basis-1/2';
      [...track.children].forEach((item) => {
        item.classList.remove('w-1/5', 'w-1/3', 'w-1/2', 'basis-1/5', 'basis-1/3', 'basis-1/2');
        item.classList.add(widthClass, basisClass);
      });
      track.style.transition = animate ? 'transform .45s ease' : 'none';
      track.style.transform = `translateX(-${index * (100 / count)}%)`;
      dots?.querySelectorAll('button').forEach((dot, dotIndex) => {
        dot.classList.toggle('bg-orange', dotIndex === index % originals.length);
        dot.classList.toggle('bg-dot-muted', dotIndex !== index % originals.length);
      });
    }

    const move = (direction) => { index += direction; if (index < 0) index = originals.length - 1; render(); };
    const resetLoop = () => { if (index >= originals.length) { index = 0; render(false); } };
    originals.forEach((item) => track.appendChild(item.cloneNode(true)));
    originals.forEach((_, i) => {
      const dot = document.createElement('button'); dot.type = 'button';
      dot.className = 'h-2 w-2 cursor-pointer rounded-full border-0 p-0 transition-colors duration-200';
      dot.setAttribute('aria-label', `Zobrazit partnera ${i + 1}`);
      dot.addEventListener('click', () => { index = i; render(); });
      dots?.appendChild(dot);
    });
    prev?.addEventListener('click', () => move(-1));
    next?.addEventListener('click', () => move(1));
    track.addEventListener('transitionend', resetLoop);
    window.addEventListener('resize', () => { index %= originals.length; render(false); });
    render(false);
    if (originals.length > 1) setInterval(() => move(1), 4000);
  }

  function makePlayerPromo() {
    document.querySelectorAll('[data-player-promo]').forEach((script) => {
      const el = script.previousElementSibling;
      if (!el || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

      let slides = [];
      try { slides = JSON.parse(script.textContent || '[]'); } catch (e) { slides = []; }
      if (slides.length < 2) return;

      const image = el.querySelector('[data-player-promo-image]');
      const numberElements = el.querySelectorAll('[data-player-promo-number]');
      if (!image || !numberElements.length) return;

      let index = 0;

      const goTo = (i) => {
        el.style.opacity = '0';
        window.setTimeout(() => {
          const digits = Array.from(String(slides[i].number));
          const numberLayout = digits.length > 1
            ? ['justify-center', 'gap-[clamp(1rem,2.4vw,2.5rem)]']
            : ['justify-center', 'translate-x-[25%]'];

          image.src = slides[i].image;
          numberElements.forEach((numberElement) => {
            numberElement.replaceChildren(...digits.map((digit) => {
              const digitElement = document.createElement('span');
              digitElement.textContent = digit;
              return digitElement;
            }));
            numberElement.classList.remove('gap-[clamp(1rem,2.4vw,2.5rem)]', 'translate-x-[25%]');
            numberElement.classList.add(...numberLayout);
          });
          el.style.opacity = '1';
        }, 420);
      };

      window.setInterval(() => {
        index = (index + 1) % slides.length;
        goTo(index);
      }, 5000);
    });
  }

  function enhanceFaq() {
    document.querySelectorAll('[data-faq]').forEach((details) => {
      const summary = details.querySelector('summary');
      const answer = details.querySelector('[data-faq-answer]');
      const icon = summary?.querySelector('[data-faq-icon]');
      if (!summary || !answer) return;
      let animation = null;
      let closing = false;
      let expanding = false;

      const rotate = (open) => {
        if (!icon) return;
        icon.animate([{ transform: open ? 'rotate(0deg)' : 'rotate(45deg)' }, { transform: open ? 'rotate(45deg)' : 'rotate(0deg)' }], { duration: 220, easing: 'ease-out', fill: 'forwards' });
      };
      const openFaq = () => {
        expanding = true; closing = false; details.open = true; animation?.cancel();
        const height = answer.scrollHeight;
        animation = answer.animate([{ height: '0px', opacity: 0, paddingBottom: '0px' }, { height: `${height}px`, opacity: 1, paddingBottom: '20px' }], { duration: 280, easing: 'cubic-bezier(0.22,1,0.36,1)' });
        rotate(true);
        animation.onfinish = () => { expanding = false; answer.style.height = ''; answer.style.opacity = ''; answer.style.paddingBottom = ''; };
      };
      const closeFaq = () => {
        closing = true; expanding = false; animation?.cancel();
        const height = answer.getBoundingClientRect().height;
        animation = answer.animate([{ height: `${height}px`, opacity: 1, paddingBottom: '20px' }, { height: '0px', opacity: 0, paddingBottom: '0px' }], { duration: 220, easing: 'ease-in-out' });
        rotate(false);
        animation.onfinish = () => { closing = false; details.open = false; answer.style.height = ''; answer.style.opacity = ''; answer.style.paddingBottom = ''; };
      };
      summary.addEventListener('click', (event) => { event.preventDefault(); (closing || !details.open) ? openFaq() : closeFaq(); });
    });
  }

  function loadLeaflet() {
    if (window.L) return Promise.resolve(window.L);

    return new Promise((resolve, reject) => {
      if (!document.querySelector('[data-leaflet-styles]')) {
        const stylesheet = document.createElement('link');
        stylesheet.rel = 'stylesheet';
        stylesheet.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
        stylesheet.dataset.leafletStyles = 'true';
        document.head.appendChild(stylesheet);
      }

      const existingScript = document.querySelector('[data-leaflet-script]');
      if (existingScript) {
        existingScript.addEventListener('load', () => resolve(window.L), { once: true });
        existingScript.addEventListener('error', reject, { once: true });
        return;
      }

      const script = document.createElement('script');
      script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
      script.dataset.leafletScript = 'true';
      script.onload = () => resolve(window.L);
      script.onerror = reject;
      document.head.appendChild(script);
    });
  }

  function makeVenueMap() {
    const element = document.querySelector('[data-leaflet-map]');
    if (!element) return;

    const latitude = Number(element.dataset.latitude);
    const longitude = Number(element.dataset.longitude);
    if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) return;

    loadLeaflet().then((L) => {
      if (!L || element.dataset.leafletReady) return;
      element.dataset.leafletReady = 'true';

      const map = L.map(element, {
        attributionControl: true,
        scrollWheelZoom: false,
        zoomControl: true,
      }).setView([latitude, longitude], 15);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> přispěvatelé',
        maxZoom: 19,
      }).addTo(map);

      const marker = L.marker([latitude, longitude], {
        icon: L.divIcon({
          className: 'venue-map-marker',
          html: '<span aria-hidden="true" class="venue-map-marker__dot"></span>',
          iconSize: [38, 48],
          iconAnchor: [19, 44],
          popupAnchor: [0, -38],
        }),
      }).addTo(map);

      const popup = document.createElement('div');
      popup.className = 'venue-map-popup__content';

      const header = document.createElement('div');
      header.className = 'venue-map-popup__header';

      const title = document.createElement('strong');
      title.className = 'venue-map-popup__title';
      title.textContent = element.dataset.mapTitle || 'CCM Aréna';

      const address = document.createElement('span');
      address.className = 'venue-map-popup__address';
      address.textContent = element.dataset.mapAddress || '';

      const link = document.createElement('a');
      link.className = 'venue-map-popup__link';
      link.href = element.dataset.mapLinkUrl || '#';
      link.target = '_blank';
      link.rel = 'noopener noreferrer';
      const linkIcon = document.createElement('span');
      linkIcon.className = 'venue-map-popup__link-icon';
      linkIcon.innerHTML = '<svg aria-hidden="true" fill="none" viewBox="0 0 24 24"><path d="M14 3h7v7m0-7-9 9M10 5H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"></path></svg>';
      link.append(linkIcon, document.createTextNode(element.dataset.mapLinkLabel || 'OTEVŘÍT V MAPÁCH'));

      header.append(title);
      popup.append(header, address, link);
      marker.bindPopup(popup, {
        autoClose: false,
        closeButton: false,
        closeOnClick: false,
        className: 'venue-map-popup',
        offset: [0, 0],
      }).openPopup();
    }).catch(() => {
      element.classList.add('venue-map--unavailable');
    });
  }

  makeSlider({ trackId: 'teamSliderTrack', prevId: 'teamPrev', nextId: 'teamNext', dotsId: 'teamDots' });
  makePartnerSlider();
  makePlayerPromo();
  enhanceFaq();
  makeVenueMap();
});
