export function initFaq() {
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
