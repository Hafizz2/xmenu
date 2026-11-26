// Xmenu owner panel — small vanilla helpers.
(function () {
  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));
  const csrf = $('meta[name="csrf-token"]')?.content;

  // Data tables that stack into cards on phones: label each cell from its column header.
  $$('table.tbl.cards').forEach(t => {
    const heads = $$('thead th', t).map(th => th.textContent.trim());
    $$('tbody tr', t).forEach(tr => $$('td', tr).forEach((td, i) => {
      if (td.hasAttribute('colspan')) return;
      if (i === 0) td.classList.add('lead');
      else if (!heads[i]) td.classList.add('actions');
      td.dataset.label = heads[i] || '';
    }));
  });

  // "More" bottom sheet on mobile
  const more = $('#more-sheet');
  $$('[data-open-more]').forEach(b => b.addEventListener('click', () => more.classList.add('open')));
  more?.addEventListener('click', e => { if (e.target === more) more.classList.remove('open'); });

  // Dropdown menus
  document.addEventListener('click', e => {
    const t = e.target.closest('[data-dropdown]');
    $$('.dropdown.open').forEach(d => { if (!t || d !== t.parentElement) d.classList.remove('open'); });
    if (t) { e.preventDefault(); t.parentElement.classList.toggle('open'); }
  });

  // Flash messages fade away
  $$('[data-autohide]').forEach(el => setTimeout(() => { el.style.transition = 'opacity .4s'; el.style.opacity = '0'; setTimeout(() => el.remove(), 400); }, 3500));

  // Confirm before destructive submits
  document.addEventListener('submit', e => {
    const msg = e.target.getAttribute('data-confirm');
    if (msg && !confirm(msg)) e.preventDefault();
  });

  // Dialogs: <button data-dialog="id">
  document.addEventListener('click', e => {
    const b = e.target.closest('[data-dialog]');
    if (b) { e.preventDefault(); $('#' + b.dataset.dialog)?.showModal(); }
    const c = e.target.closest('[data-close]');
    if (c) { e.preventDefault(); c.closest('dialog')?.close(); }
  });

  // Copy buttons
  document.addEventListener('click', async e => {
    const b = e.target.closest('[data-copy]');
    if (!b) return;
    try { await navigator.clipboard.writeText(b.dataset.copy); } catch (_) {}
    const old = b.innerHTML; b.textContent = 'Copied!'; setTimeout(() => (b.innerHTML = old), 1400);
  });

  // Small confirmation so owners always know a change went through.
  let toastEl;
  function toast(msg, bad) {
    if (!toastEl) { toastEl = document.createElement('div'); toastEl.className = 'toast'; toastEl.setAttribute('role', 'status'); document.body.appendChild(toastEl); }
    toastEl.classList.toggle('err', !!bad);
    toastEl.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">' + (bad ? '<path d="M12 8v5M12 16.5h.01"/><circle cx="12" cy="12" r="9"/>' : '<path d="M20 6 9 17l-5-5"/>') + '</svg>';
    toastEl.appendChild(document.createTextNode(msg));
    toastEl.classList.add('on'); clearTimeout(toastEl._t); toastEl._t = setTimeout(() => toastEl.classList.remove('on'), 1800);
  }
  window.xToast = toast;

  // Quick edit: price fields and switches save on change, no reload.
  async function quick(url, body) {
    const r = await fetch(url, { method: 'PATCH', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }, body: JSON.stringify(body) });
    if (!r.ok) throw new Error('save failed');
    return r.json();
  }
  $$('[data-quick-price]').forEach(inp => {
    let last = inp.value;
    const save = async () => {
      if (inp.value === last || inp.value === '') { inp.value = last; return; }
      try {
        await quick(inp.dataset.quickPrice, { price: inp.value });
        last = inp.value; const box = inp.closest('.price-box') || inp; box.classList.add('saved'); setTimeout(() => box.classList.remove('saved'), 1200); toast('Price saved');
      } catch (_) { inp.value = last; toast('Not saved. Check your internet.', true); }
    };
    inp.addEventListener('change', save);
    inp.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); inp.blur(); } });
  });
  $$('[data-quick-toggle]').forEach(cb => cb.addEventListener('change', async () => {
    const field = cb.dataset.field;
    const row = cb.closest('.it');
    try {
      await quick(cb.dataset.quickToggle, { [field]: cb.checked });
      if (field === 'sold_out' && row) row.classList.toggle('is-sold', cb.checked);
      toast(field === 'sold_out' ? (cb.checked ? 'Marked sold out' : 'Available again') : (cb.checked ? 'Shown on menu' : 'Hidden from menu'));
      if (field === 'visible' && row) row.classList.toggle('is-off', !cb.checked);
    } catch (_) { cb.checked = !cb.checked; toast('Not saved. Check your internet.', true); }
  }));

  // Photo picker previews
  $$('[data-photo]').forEach(box => {
    const input = $('input[type=file]', box);
    input.addEventListener('change', () => {
      const f = input.files[0];
      if (!f) return;
      if (f.size > 5 * 1024 * 1024) { alert('Photos can be up to 5 MB.'); input.value = ''; return; }
      const url = URL.createObjectURL(f);
      const target = box.dataset.photo ? $(box.dataset.photo) : box;
      if (target.classList.contains('cover-prev')) {
        target.style.backgroundImage = `url(${url})`;
        target.querySelector('span')?.remove();
      } else {
        if (!target.classList.contains('photo-drop')) target.querySelector('svg')?.remove();
        let img = $('img', target);
        if (!img) { img = document.createElement('img'); target.prepend(img); }
        img.src = url;
      }
      const rm = box.closest('div')?.querySelector('[name^=remove_]'); if (rm) rm.checked = false;
    });
  });

  // Repeaters (sizes, add-ons)
  $$('[data-repeater]').forEach(rep => {
    const list = $('[data-rows]', rep);
    const tpl = $('template', rep);
    let i = list.children.length + 100;
    $('[data-add]', rep).addEventListener('click', () => {
      const html = tpl.innerHTML.replace(/__i__/g, i++);
      list.insertAdjacentHTML('beforeend', html);
      list.lastElementChild.querySelector('input')?.focus();
    });
    rep.addEventListener('click', e => { const b = e.target.closest('[data-remove]'); if (b) b.closest('.repeater-row').remove(); });
  });

  // Payment type → show bank select only for banks
  $$('[data-pay-type]').forEach(sel => {
    const form = sel.closest('form');
    const sync = () => {
      const t = sel.value;
      $$('[data-show-for]', form).forEach(el => {
        const on = el.dataset.showFor.split(',').includes(t);
        el.classList.toggle('hide', !on);
        $$('input,select', el).forEach(i => (i.disabled = !on));
      });
    };
    sel.addEventListener('change', sync); sync();
  });
})();
