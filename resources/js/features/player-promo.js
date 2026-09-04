export function initPlayerPromo() {
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
