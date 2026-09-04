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

export function initTeamSlider() {
  makeSlider({ trackId: 'teamSliderTrack', prevId: 'teamPrev', nextId: 'teamNext', dotsId: 'teamDots' });
}

export function initPartnerSlider() {
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
