/* Xmenu Game Kit — start screen, scoreboard, series, results, sound, confetti, i18n.
   Usage: const kit = XKit({ ...config, newMatch(state){}, newRound(state){} }); */
(function () {
  'use strict';
  var P = new URLSearchParams(location.search);
  var LANG = P.get('lang') === 'am' ? 'am' : 'en';
  var STR = {
    en: { start: 'Start game', mode: 'Mode', difficulty: 'Difficulty', players: 'Players', length: 'Match length', easy: 'Easy', medium: 'Medium', hard: 'Hard',
      bo1: 'Single', bo3: 'Best of 3', bo5: 'Best of 5', you: 'You', computer: 'Computer', player: 'Player', howto: 'How to play', gotit: 'Got it',
      wins: 'wins!', winsMatch: 'wins the match!', draw: 'Draw', nextRound: 'Next round', playAgain: 'Play again', settings: 'Settings', exit: 'Exit',
      round: 'Round', firstTo: 'First to', yourTurn: 'Your turn', turn: 'to play', thinking: 'Thinking', restarted: 'Round restarted', soundOn: 'Sound on', soundOff: 'Sound off',
      restart: 'Restart round', youLose: 'Computer wins', tryAgain: 'So close. Try again!', wellPlayed: 'Well played!',
      back: 'Menu', paused: 'Paused', resume: 'Resume', newGame: 'New game', winner: 'Winner', result: 'Result' },
    am: { start: 'ጨዋታ ጀምር', mode: 'የጨዋታ አይነት', difficulty: 'ደረጃ', players: 'ተጫዋቾች', length: 'የውድድር ርዝመት', easy: 'ቀላል', medium: 'መካከለኛ', hard: 'ከባድ',
      bo1: 'አንድ ዙር', bo3: 'ከ3 ምርጥ', bo5: 'ከ5 ምርጥ', you: 'እርስዎ', computer: 'ኮምፒውተር', player: 'ተጫዋች', howto: 'እንዴት እንደሚጫወቱ', gotit: 'ገባኝ',
      wins: 'አሸነፈ!', winsMatch: 'ውድድሩን አሸነፈ!', draw: 'አቻ', nextRound: 'ቀጣይ ዙር', playAgain: 'እንደገና ተጫወት', settings: 'ቅንብሮች', exit: 'ውጣ',
      round: 'ዙር', firstTo: 'ቀድሞ', yourTurn: 'ተራዎ ነው', turn: 'ተራ', thinking: 'እያሰበ', restarted: 'ዙሩ እንደገና ጀመረ', soundOn: 'ድምጽ በርቷል', soundOff: 'ድምጽ ጠፍቷል',
      restart: 'ዙሩን እንደገና', youLose: 'ኮምፒውተሩ አሸነፈ', tryAgain: 'ተቃርበዋል። እንደገና ይሞክሩ!', wellPlayed: 'በጣም ጥሩ!',
      back: 'ምናሌ', paused: 'ቆሟል', resume: 'ቀጥል', newGame: 'አዲስ ጨዋታ', winner: 'አሸናፊ', result: 'ውጤት' }
  };
  function t(k) { return (STR[LANG][k] !== undefined ? STR[LANG][k] : STR.en[k]) || k; }
  function L(o) { return o && typeof o === 'object' ? (o[LANG] || o.en) : o; }

  var ICONS = {
    exit: '<path d="M15 18l-6-6 6-6"/>', help: '<circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 1 1 3.5 2.3c-.6.3-1 .9-1 1.6V14M12 17.5h.01"/>',
    sound: '<path d="M11 5 6 9H2v6h4l5 4z"/><path d="M15.5 8.5a5 5 0 0 1 0 7M19 5a10 10 0 0 1 0 14"/>', mute: '<path d="M11 5 6 9H2v6h4l5 4z"/><path d="m22 9-6 6M16 9l6 6"/>',
    refresh: '<path d="M21 12a9 9 0 1 1-2.6-6.4L21 8"/><path d="M21 3v5h-5"/>', users: '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/>',
    cpu: '<rect x="5" y="5" width="14" height="14" rx="2"/><rect x="9" y="9" width="6" height="6" rx="1"/><path d="M9 2v3M15 2v3M9 19v3M15 19v3M2 9h3M2 15h3M19 9h3M19 15h3"/>',
    user: '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>', play: '<path d="M7 4.5v15l12.5-7.5z"/>',
    trophy: '<path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 0 1-10 0z"/><path d="M17 5h3v2a4 4 0 0 1-4 4M7 5H4v2a4 4 0 0 0 4 4"/>',
    equal: '<path d="M5 9h14M5 15h14"/>', pause: '<path d="M9 5v14M15 5v14"/>', back: '<path d="M15 18l-6-6 6-6"/>', sliders: '<path d="M4 6h10M18 6h2M4 12h4M12 12h8M4 18h12M20 18h0"/><circle cx="16" cy="6" r="2"/><circle cx="10" cy="12" r="2"/><circle cx="18" cy="18" r="2"/>',
    settings: '<circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M4.2 4.2l2.1 2.1M17.7 17.7l2.1 2.1M2 12h3M19 12h3M4.2 19.8l2.1-2.1M17.7 6.3l2.1-2.1"/>'
  };
  function ic(n) { return '<svg class="xi" viewBox="0 0 24 24" aria-hidden="true">' + (ICONS[n] || '') + '</svg>'; }
  function el(tag, cls, html) { var e = document.createElement(tag); if (cls) e.className = cls; if (html != null) e.innerHTML = html; return e; }
  function esc(s) { var d = document.createElement('div'); d.textContent = s == null ? '' : s; return d.innerHTML; }

  // ---------- Sound (synthesised; no files to download) ----------
  var muted = false, ac = null; try { muted = localStorage.getItem('xk_mute') === '1'; } catch (e) {}
  function tone(freq, dur, type, vol, when, slideTo) {
    if (muted) return;
    try {
      ac = ac || new (window.AudioContext || window.webkitAudioContext)();
      var t0 = ac.currentTime + (when || 0), o = ac.createOscillator(), g = ac.createGain();
      o.type = type || 'sine'; o.frequency.setValueAtTime(freq, t0);
      if (slideTo) o.frequency.exponentialRampToValueAtTime(slideTo, t0 + dur);
      g.gain.setValueAtTime(0.0001, t0); g.gain.exponentialRampToValueAtTime(vol || 0.12, t0 + 0.012); g.gain.exponentialRampToValueAtTime(0.0001, t0 + dur);
      o.connect(g); g.connect(ac.destination); o.start(t0); o.stop(t0 + dur + 0.02);
    } catch (e) {}
  }
  var SOUNDS = {
    tap: function () { tone(520, 0.07, 'triangle', 0.1); },
    place: function () { tone(660, 0.08, 'triangle', 0.12); tone(990, 0.06, 'sine', 0.05, 0.03); },
    drop: function () { tone(420, 0.18, 'sine', 0.14, 0, 140); },
    flip: function () { tone(900, 0.05, 'square', 0.04); },
    match: function () { tone(660, 0.09, 'triangle', 0.1); tone(990, 0.12, 'triangle', 0.1, 0.08); },
    miss: function () { tone(300, 0.12, 'sine', 0.08, 0, 220); },
    tick: function () { tone(1200, 0.03, 'square', 0.03); },
    go: function () { tone(880, 0.18, 'square', 0.08); },
    win: function () { [523, 659, 784, 1047].forEach(function (f, i) { tone(f, 0.16, 'triangle', 0.12, i * 0.09); }); },
    lose: function () { [392, 330, 262].forEach(function (f, i) { tone(f, 0.2, 'sine', 0.1, i * 0.12); }); },
    draw: function () { tone(440, 0.15, 'sine', 0.1); tone(440, 0.15, 'sine', 0.1, 0.18); },
    start: function () { tone(440, 0.1, 'triangle', 0.08); tone(660, 0.14, 'triangle', 0.08, 0.08); }
  };

  // ---------- Offline: cache every game the first time any of them (or the menu) loads ----------
  if ('serviceWorker' in navigator && location.protocol !== 'file:') {
    try { navigator.serviceWorker.register(new URL('../sw.js', document.currentScript ? document.currentScript.src : location.href).href); } catch (e) {}
  }
  // Behave like an app, not a web page: no long-press menus, no pinch, no double-tap zoom.
  document.addEventListener('contextmenu', function (e) { if (!e.target.closest('input')) e.preventDefault(); });
  document.addEventListener('gesturestart', function (e) { e.preventDefault(); });
  document.addEventListener('dblclick', function (e) { e.preventDefault(); }, { passive: false });

  window.XKit = function (cfg) {
    var root = document.documentElement;
    root.style.setProperty('--p1', cfg.playerColors ? cfg.playerColors[0] : '#E5484D');
    root.style.setProperty('--p2', cfg.playerColors ? cfg.playerColors[1] : '#2F7BEA');
    if (cfg.bg) root.style.setProperty('--bg', cfg.bg);
    if (cfg.accent) root.style.setProperty('--accent', cfg.accent);
    var meta = document.createElement('meta'); meta.name = 'theme-color'; meta.content = cfg.bg || '#F4F1EA'; document.head.appendChild(meta);
    document.title = L(cfg.title);
    var key = 'xk_' + cfg.id;
    var saved = {}; try { saved = JSON.parse(localStorage.getItem(key) || '{}'); } catch (e) {}

    var S = {
      mode: saved.mode || cfg.modes[0].id, diff: saved.diff || 'medium', target: saved.target || (cfg.defaultTarget || 2),
      names: saved.names || [t('player') + ' 1', t('player') + ' 2'], score: [0, 0], round: 1, starter: 0, draws: 0, token: 0, turn: 0
    };
    function modeCfg() { return cfg.modes.filter(function (m) { return m.id === S.mode; })[0] || cfg.modes[0]; }
    S.isAI = function (i) { return !!modeCfg().ai && i === 1; };
    S.isSolo = function () { return !!modeCfg().solo; };
    function displayName(i) { if (S.isAI(i)) return t('computer'); return S.names[i] || (t('player') + ' ' + (i + 1)); }

    // ---------- DOM ----------
    var app = el('div', 'xk-app'); document.body.appendChild(app);
    var bar = el('div', 'xk-bar' + (cfg.compactBar ? ' compact' : ''));
    bar.innerHTML = '<button class="xk-back" data-a="exit" aria-label="' + t('exit') + '">' + ic('back') + '<span>' + t('back') + '</span></button><h1>' + esc(L(cfg.title)) + '</h1><span class="xk-sp"></span>' +
      '<button class="xk-ib" data-a="sound" aria-label="Sound">' + ic(muted ? 'mute' : 'sound') + '</button>' +
      '<button class="xk-ib" data-a="pause" aria-label="' + t('paused') + '">' + ic('pause') + '</button>';
    app.appendChild(bar);
    var score = el('div', 'xk-score'); if (cfg.scoreboard === false) score.style.display = 'none';
    score.innerHTML = '<div class="xk-p" data-p="0"><span class="dot" style="background:var(--p1)">' + esc(cfg.marks ? cfg.marks[0] : '') + '</span><div class="nm"><b></b><small></small></div><span class="sc">0</span></div>' +
      '<div class="xk-round"></div>' +
      '<div class="xk-p" data-p="1"><span class="dot" style="background:var(--p2)">' + esc(cfg.marks ? cfg.marks[1] : '') + '</span><div class="nm"><b></b><small></small></div><span class="sc">0</span></div>';
    app.appendChild(score);
    var stage = el('div', 'xk-stage'); app.appendChild(stage);
    var hint = el('div', 'xk-hint'); app.appendChild(hint);
    if (cfg.hideHint) hint.style.display = 'none';
    var toastEl = el('div', 'xk-toast'); document.body.appendChild(toastEl);

    var openSheets = 0;
    function modal(inner, dismissable) {
      var m = el('div', 'xk-modal'); var c = el('div', 'xk-card', inner); m.appendChild(c); document.body.appendChild(m);
      openSheets++;
      requestAnimationFrame(function () { m.classList.add('on'); });
      m.close = function () { if (m._closed) return; m._closed = true; openSheets--; m.classList.remove('on'); setTimeout(function () { m.remove(); }, 300); };
      if (dismissable) m.addEventListener('click', function (e) { if (e.target === m) m.close(); });
      return m;
    }
    function showPause() {
      var m = modal('<h2>' + t('paused') + '</h2><div class="xk-list" style="margin-top:18px">' +
        '<button data-p="resume">' + ic('play') + t('resume') + '</button>' +
        '<button data-p="restart">' + ic('refresh') + t('restart') + '</button>' +
        '<button data-p="new">' + ic('sliders') + t('newGame') + '</button>' +
        '<button data-p="help">' + ic('help') + t('howto') + '</button>' +
        '<button data-p="exit" class="danger">' + ic('back') + t('back') + '</button></div>', true);
      cfg.onPause && cfg.onPause(); var closeM = m.close; m.close = function () { closeM(); cfg.onResume && cfg.onResume(); };
      m.addEventListener('click', function (e) {
        var b = e.target.closest('[data-p]'); if (!b) return; var a = b.dataset.p; m.close(); SOUNDS.tap();
        if (a === 'restart') api.restartRound(); else if (a === 'new') showStart(false); else if (a === 'help') showHelp(); else if (a === 'exit') exit();
      });
    }
    var pChips = score.querySelectorAll('.xk-p');
    function paintScore() {
      for (var i = 0; i < 2; i++) {
        pChips[i].querySelector('b').textContent = displayName(i);
        pChips[i].querySelector('.sc').textContent = S.score[i];
      }
      pChips[1].style.display = S.isSolo() ? 'none' : '';
      score.querySelector('.xk-round').innerHTML = S.isSolo() ? '' : t('round') + '<br>' + S.round;
    }

    // ---------- Start screen ----------
    function showStart(first) {
      var m = modal('');
      var c = m.firstChild;
      function render() {
        var mc = modCfg();
        var html = '<div class="xk-art"><svg viewBox="0 0 24 24">' + (cfg.icon || ICONS.play) + '</svg></div><h2>' + esc(L(cfg.title)) + '</h2><p class="sub">' + esc(L(cfg.desc)) + '</p>';
        if (cfg.modes.length > 1) {
          html += '<div class="xk-sec"><label>' + t('mode') + '</label><div class="xk-seg">' + cfg.modes.map(function (md) {
            return '<button data-mode="' + md.id + '" class="' + (md.id === S.mode ? 'on' : '') + '">' + ic(md.icon || (md.ai ? 'cpu' : (md.solo ? 'user' : 'users'))) + esc(L(md.label)) + '</button>'; }).join('') + '</div></div>';
        }
        if (mc.ai) {
          html += '<div class="xk-sec"><label>' + t('difficulty') + '</label><div class="xk-seg">' + ['easy', 'medium', 'hard'].map(function (d) {
            return '<button data-diff="' + d + '" class="' + (d === S.diff ? 'on' : '') + '">' + t(d) + '</button>'; }).join('') + '</div></div>';
        }
        if (mc.options) {
          html += '<div class="xk-sec"><label>' + esc(L(mc.options.label)) + '</label><div class="xk-seg">' + mc.options.items.map(function (o) {
            return '<button data-opt="' + o.id + '" class="' + (o.id === (S.opt || mc.options.def) ? 'on' : '') + '">' + esc(L(o.label)) + '</button>'; }).join('') + '</div></div>';
        }
        if (!mc.solo) {
          html += '<div class="xk-sec"><label>' + t('players') + '</label><div class="xk-names">' +
            '<div><i style="background:var(--p1)"></i><input maxlength="14" data-name="0" value="' + esc(S.names[0]) + '" aria-label="Player 1"></div>' +
            '<div><i style="background:var(--p2)"></i><input maxlength="14" data-name="1" value="' + esc(mc.ai ? t('computer') : S.names[1]) + '" ' + (mc.ai ? 'disabled' : '') + ' aria-label="Player 2"></div></div></div>';
          if (cfg.series !== false) {
            html += '<div class="xk-sec"><label>' + t('length') + '</label><div class="xk-seg">' + [[1, 'bo1'], [2, 'bo3'], [3, 'bo5']].map(function (b) {
              return '<button data-target="' + b[0] + '" class="' + (b[0] === S.target ? 'on' : '') + '">' + t(b[1]) + '</button>'; }).join('') + '</div></div>';
          }
        }
        html += '<button class="xk-btn" data-go>' + ic('play') + t('start') + '</button>';
        if (first) html += '<button class="xk-btn ghost" data-a="help2">' + ic('help') + t('howto') + '</button>';
        c.innerHTML = html;
      }
      function modCfg() { return cfg.modes.filter(function (md) { return md.id === S.mode; })[0] || cfg.modes[0]; }
      render();
      c.addEventListener('input', function (e) { var i = e.target.dataset.name; if (i != null) S.names[+i] = e.target.value.trim() || (t('player') + ' ' + (+i + 1)); });
      c.addEventListener('click', function (e) {
        var b = e.target.closest('button'); if (!b) return;
        if (b.dataset.mode) { S.mode = b.dataset.mode; S.opt = null; SOUNDS.tap(); render(); }
        else if (b.dataset.diff) { S.diff = b.dataset.diff; SOUNDS.tap(); render(); }
        else if (b.dataset.target) { S.target = +b.dataset.target; SOUNDS.tap(); render(); }
        else if (b.dataset.opt) { S.opt = b.dataset.opt; SOUNDS.tap(); render(); }
        else if (b.dataset.a === 'help2') { showHelp(); }
        else if (b.hasAttribute('data-go')) {
          if (!S.opt && modCfg().options) S.opt = modCfg().options.def;
          try { localStorage.setItem(key, JSON.stringify({ mode: S.mode, diff: S.diff, target: S.target, names: S.names })); } catch (er) {}
          m.close(); SOUNDS.start(); newMatch();
        }
      });
    }
    function showHelp() {
      var m = modal('<h2>' + t('howto') + '</h2><ol class="xk-rules">' + (L(cfg.rules) || []).map(function (r) { return '<li>' + esc(r) + '</li>'; }).join('') + '</ol><button class="xk-btn" data-ok>' + t('gotit') + '</button>', true);
      m.querySelector('[data-ok]').onclick = m.close;
    }

    // ---------- Flow ----------
    function newMatch() { S.score = [0, 0]; S.round = 1; S.draws = 0; S.starter = 0; S.token++; paintScore(); cfg.newMatch && cfg.newMatch(S); newRound(); }
    function newRound() { S.token++; S.turn = S.starter; paintScore(); api.setTurn(S.starter); cfg.newRound(S); }

    var api = {
      lang: LANG, t: t, L: L, state: S, stage: stage, el: el, esc: esc,
      sound: function (n) { SOUNDS[n] && SOUNDS[n](); },
      vibrate: function (p) { try { navigator.vibrate && navigator.vibrate(p); } catch (e) {} },
      hint: function (txt) { hint.innerHTML = txt || ''; },
      toast: function (txt) { toastEl.textContent = txt; toastEl.classList.add('on'); clearTimeout(toastEl._t); toastEl._t = setTimeout(function () { toastEl.classList.remove('on'); }, 1600); },
      name: displayName,
      setTurn: function (i, thinking) {
        S.turn = i;
        for (var k = 0; k < 2; k++) {
          pChips[k].classList.toggle('on', k === i);
          pChips[k].querySelector('small').innerHTML = k === i ? (thinking ? t('thinking') + ' <span class="xk-think"><i></i><i></i><i></i></span>' : (S.isAI(k) ? '' : (S.isAI(1) ? t('yourTurn') : t('turn')))) : '';
        }
        if (!S.isSolo()) api.hint(S.isAI(i) ? '' : esc(displayName(i)) + ' · ' + (S.isAI(1) ? t('yourTurn') : t('turn')));
      },
      setSoloScore: function (value, sub) { pChips[0].querySelector('.sc').textContent = value; pChips[0].querySelector('small').textContent = sub || ''; pChips[0].classList.add('on'); },
      /** winner: 0 | 1 | -1 (draw). opts: {stats:[[label,value]], title, sub, solo} */
      roundEnd: function (winner, opts) {
        opts = opts || {};
        var token = S.token;
        if (winner >= 0 && !opts.solo) S.score[winner]++; else if (winner < 0) S.draws++;
        paintScore();
        var over = opts.solo || winner < 0 && S.target === 1 || (winner >= 0 && S.score[winner] >= S.target);
        var aiWon = winner === 1 && S.isAI(1);
        var title = opts.title || (winner < 0 ? t('draw') : (aiWon ? t('youLose') : displayName(winner) + ' ' + (over && S.target > 1 ? t('winsMatch') : t('wins'))));
        var sub = opts.sub || (winner < 0 ? t('wellPlayed') : (aiWon ? t('tryAgain') : (S.target > 1 && !opts.solo ? t('firstTo') + ' ' + S.target + ' · ' + t('round') + ' ' + S.round : t('wellPlayed'))));
        setTimeout(function () {
          if (token !== S.token) return;
          api.sound(winner < 0 ? 'draw' : (aiWon ? 'lose' : 'win')); api.vibrate(winner < 0 ? 40 : [30, 40, 60]);
          var stats = (opts.stats || []).map(function (s) { return '<div><b>' + esc(s[1]) + '</b><small>' + esc(s[0]) + '</small></div>'; }).join('');
          var series = '';
          if (!opts.solo && S.target > 1) {
            series = '<div class="xk-series">' + [0, 1].map(function (p) {
              var pips = ''; for (var k = 0; k < S.target; k++) pips += '<i style="' + (k < S.score[p] ? 'background:var(--p' + (p + 1) + ')' : '') + '"></i>';
              return '<div style="text-align:center"><div class="pip">' + pips + '</div><div style="font-size:13px;color:var(--muted);margin-top:6px">' + esc(displayName(p)) + '</div></div>'; }).join('<b style="font-size:24px;font-variant-numeric:tabular-nums">' + S.score[0] + ' : ' + S.score[1] + '</b>') + '</div>';
          }
          var badge = winner >= 0 && !opts.solo ? '<span class="xk-win"><i style="background:var(--p' + (winner + 1) + ')"></i>' + (over ? t('winner') : t('round') + ' ' + S.round) + '</span>' : '<span class="xk-win">' + t('result') + '</span>';
          var m = modal('<div class="xk-result">' + badge +
            '<h2>' + esc(title) + '</h2><p class="sub">' + esc(sub) + '</p>' + (stats ? '<div class="xk-stats">' + stats + '</div>' : '') + series +
            '<button class="xk-btn" data-next>' + ic(over ? 'refresh' : 'play') + (over ? t('playAgain') : t('nextRound')) + '</button>' +
            '<div class="xk-row"><button class="xk-btn ghost" data-settings>' + ic('settings') + t('settings') + '</button><button class="xk-btn ghost" data-exit>' + ic('exit') + t('exit') + '</button></div></div>');
          m.querySelector('[data-next]').onclick = function () { m.close(); if (over) newMatch(); else { S.round++; S.starter = 1 - S.starter; newRound(); } };
          m.querySelector('[data-settings]').onclick = function () { m.close(); showStart(false); };
          m.querySelector('[data-exit]').onclick = exit;
        }, opts.delay != null ? opts.delay : 650);
      },
      restartRound: function () { newRound(); api.toast(t('restarted')); },
      newMatch: newMatch
    };

    function exit() {
      if (window.parent !== window) window.parent.postMessage({ xmenu: 'exit' }, '*');
      else if (history.length > 1) history.back(); else location.href = '/';
    }
    bar.addEventListener('click', function (e) {
      var b = e.target.closest('[data-a]'); if (!b) return;
      var a = b.dataset.a;
      if (a === 'exit') exit();
      else if (a === 'pause') { SOUNDS.tap(); showPause(); }
      else if (a === 'sound') { muted = !muted; try { localStorage.setItem('xk_mute', muted ? '1' : '0'); } catch (er) {} b.innerHTML = ic(muted ? 'mute' : 'sound'); api.toast(muted ? t('soundOff') : t('soundOn')); if (!muted) SOUNDS.tap(); }
    });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !openSheets) showPause(); });

    paintScore();
    setTimeout(function () { showStart(true); }, 30);
    return api;
  };
})();
