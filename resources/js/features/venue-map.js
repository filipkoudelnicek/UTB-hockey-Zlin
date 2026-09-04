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

export function initVenueMap() {
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
    linkIcon.innerHTML = '<svg aria-hidden="true" fill="none" viewBox="0 0 24 24"><path d="M14 3h7v7m0-7-9 9M10 5H5a2 2 0 0 0-2 2v12a2 2 0 0 2 2h12a2 2 0 0 0 2-2v-5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"></path></svg>';
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
