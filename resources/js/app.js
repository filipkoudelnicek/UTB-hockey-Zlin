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

  let toastTimer;
  const showToast = (message, isError = false) => {
    let toast = document.querySelector('[data-toast]');
    if (!toast) {
      toast = document.createElement('div');
      toast.dataset.toast = '';
      toast.setAttribute('role', 'status');
      toast.setAttribute('aria-live', 'polite');
      toast.className = 'fixed bottom-6 left-1/2 z-50 -translate-x-1/2 rounded-full px-5 py-3 text-sm font-bold text-white shadow-lg transition-opacity duration-300';
      document.body.appendChild(toast);
    }
    toast.textContent = message;
    toast.classList.toggle('bg-ink', !isError);
    toast.classList.toggle('bg-wine', isError);
    toast.style.opacity = '1';
    window.clearTimeout(toastTimer);
    toastTimer = window.setTimeout(() => { toast.style.opacity = '0'; }, 2500);
  };

  document.querySelectorAll('[data-copy-article-link]').forEach((button) => {
    button.addEventListener('click', async () => {
      try {
        const link = button.dataset.copyArticleLink || window.location.href;
        await navigator.clipboard.writeText(new URL(link, window.location.origin).href);
        button.setAttribute('aria-label', 'Odkaz zkopírován');
        showToast('Odkaz byl zkopírován do schránky');
        window.setTimeout(() => button.setAttribute('aria-label', 'Kopírovat odkaz'), 1800);
      } catch (error) {
        button.setAttribute('aria-label', 'Odkaz se nepodařilo zkopírovat');
        showToast('Odkaz se nepodařilo zkopírovat', true);
        window.setTimeout(() => button.setAttribute('aria-label', 'Kopírovat odkaz'), 1800);
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

  if (document.getElementById('teamSliderTrack') || document.getElementById('partnersSliderTrack')) {
    import('./features/sliders').then(({ initPartnerSlider, initTeamSlider }) => {
      initTeamSlider();
      initPartnerSlider();
    });
  }

  if (document.querySelector('[data-player-promo]')) {
    import('./features/player-promo').then(({ initPlayerPromo }) => initPlayerPromo());
  }

  if (document.querySelector('[data-faq]')) {
    import('./features/faq').then(({ initFaq }) => initFaq());
  }

  if (document.querySelector('[data-leaflet-map]')) {
    import('./features/venue-map').then(({ initVenueMap }) => initVenueMap());
  }
});
