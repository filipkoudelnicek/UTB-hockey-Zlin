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
