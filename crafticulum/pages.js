/* Crafticulum: fills the public pages with this site's name, logo, colour and contact email */
/* a colleague's link (?ref=code): we ask before saving the cookie that remembers it for 90 days, so both sides get their free month if this visitor signs up */
try{ (function(){ var m = /[?&]ref=([a-z0-9]{8})(?:&|$)/.exec(location.search); if(!m || /(?:^|; )cc_ref=/.test(document.cookie)) return; try{ if(sessionStorage.getItem("cc_ref_no") === m[1]) return; }catch(e){}
  function show(){ if(document.getElementById("ccRefAsk")) return; var d = document.createElement("div"); d.id = "ccRefAsk"; d.setAttribute("role", "region"); d.setAttribute("aria-label", "A colleague's invitation");
    d.style.cssText = "position:fixed;left:12px;right:12px;bottom:12px;z-index:2000;max-width:520px;margin:0 auto;background:#fff;color:#1e1b4b;border:1px solid #c7d2fe;border-radius:16px;box-shadow:0 12px 40px rgba(30,27,75,.28);padding:14px 16px;font:15px/1.45 system-ui,-apple-system,Segoe UI,sans-serif;text-align:left";
    var bs = "border-radius:10px;padding:9px 16px;font:inherit;font-weight:700;cursor:pointer;min-height:44px;";
    d.innerHTML = '<b style="display:block;margin-bottom:4px">A colleague invited you</b><span>If you start a paid plan, you both get a free month. To remember the invitation we would save one small cookie for 90 days. It holds the code from the link and nothing about you. <a href="cookies.html" style="color:#4f46e5">About cookies</a></span><div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px"><button type="button" data-r="y" style="' + bs + 'border:0;color:#fff;background:linear-gradient(135deg,#4f46e5,#7c3aed)">Yes, remember it</button><button type="button" data-r="n" style="' + bs + 'border:1px solid #c7d2fe;background:#fff;color:#4338ca">No thanks</button></div>';
    d.addEventListener("click", function(e){ var b = e.target.closest ? e.target.closest("[data-r]") : null; if(!b) return; if(b.getAttribute("data-r") === "y") document.cookie = "cc_ref=" + m[1] + ";path=/;max-age=" + 90 * 86400 + ";samesite=lax"; else { try{ sessionStorage.setItem("cc_ref_no", m[1]); }catch(x){} } d.parentNode.removeChild(d); });
    document.body.appendChild(d); }
  if(document.body) show(); else document.addEventListener("DOMContentLoaded", show); })(); }catch(e){}
(function(){
  var year = document.querySelectorAll("[data-year]"); for(var i = 0; i < year.length; i++) year[i].textContent = new Date().getFullYear();
  function each(sel, fn){ var l = document.querySelectorAll(sel); for(var i = 0; i < l.length; i++) fn(l[i]); }
  function apply(s){
    s = s || {};
    if(/^#[0-9a-f]{6}$/i.test(s.color || "")) document.documentElement.style.setProperty("--p", s.color);
    each("[data-site-name]", function(el){ el.textContent = s.org || s.name || el.getAttribute("data-site-name") || "the organisation that runs this site"; });
    if(s.name) each(".brand small", function(el){ el.textContent = s.name; el.hidden = false; });
    if(s.logo) each(".brand", function(b){ var svg = b.querySelector("svg"); if(svg){ var im = document.createElement("img"); im.src = s.logo; im.alt = ""; svg.replaceWith(im); } });
    each("[data-contact]", function(a){ if(s.contact){ a.href = "mailto:" + s.contact; if(a.hasAttribute("data-contact-text")) a.textContent = s.contact; } });
    each("[data-if-contact]", function(el){ el.hidden = !s.contact; });
    each("[data-no-contact]", function(el){ el.hidden = !!s.contact; });
  }
  apply(null);
  fetch("auth.php?a=config", {credentials: "same-origin"}).then(function(r){ return r.json(); }).then(function(j){ if(j && j.setup && document.querySelector(".hero")){ location.replace(document.querySelector('a[href="app"]') ? "app" : "app.html"); return; }   // nothing set up yet: the app walks them through it
    apply(j && j.site);
    if(j && j.demo){ each("[data-demo],[data-demo-note]", function(el){ el.hidden = false; });
      each("[data-demo]", function(a){ a.addEventListener("click", function(e){ e.preventDefault(); if(a.getAttribute("aria-busy")) return; a.setAttribute("aria-busy", "true"); var was = a.textContent; a.textContent = "Opening...";
        fetch("auth.php?a=demo", {method: "POST", headers: {"Content-Type": "application/json"}, credentials: "same-origin", body: "{}"}).then(function(r){ if(!r.ok) throw 0; try{ sessionStorage.removeItem("cc_reloaded"); }catch(x){} location.href = (document.querySelector('a[href="app"]') ? "app" : "app.html"); })
          .catch(function(){ a.removeAttribute("aria-busy"); a.textContent = was; alert("The demo couldn't be opened just now. Try again in a minute."); }); }); }); }
  }).catch(function(){});
  /* prices, when plans are switched on */
  function esc(t){ return String(t).replace(/[&<>"]/g, function(c){ return {"&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;"}[c]; }); }
  if(document.querySelector("[data-if-plans]")) fetch("billing.php?a=plans", {credentials: "same-origin"}).then(function(r){ return r.json(); }).then(function(j){ if(!j || !j.on || !j.plans || !j.plans.length) return;
    each("[data-if-plans]", function(el){ el.hidden = false; }); each("[data-no-plans]", function(el){ el.hidden = true; });
    var box = document.getElementById("plans"); if(!box) return; var app = document.querySelector('a[href="app"]') ? "app" : "app.html", contact = document.querySelector('a[href="contact"]') ? "contact" : "contact.html";
    var yearly = false, money = function(v){ return "\u00a3" + (v % 100 ? (v / 100).toFixed(2) : String(v / 100)); };
    function draw(){ box.innerHTML = j.plans.map(function(p){ var paid = p.price > 0, lines = [p.people > 0 ? (p.people === 1 ? "Just you" : "Up to " + p.people + " people") : "As many people as you need", p.depts > 0 ? (p.depts === 1 ? "One department" : p.depts + " departments") : "As many departments as you need",
        p.builds > 0 ? p.builds + " builds with Crafticulum AI a month" : "No monthly limit on builds", p.checks > 0 ? p.checks + " Second Marker checks a month" : "No monthly limit on Second Marker"];
      var yr = yearly && paid, go = p.price < 0 ? '<a class="btn btn-o" href="' + contact + '?about=enquiry">Ask for a quote</a>' : '<a class="btn ' + (p.id === "pro" ? "btn-p" : "btn-o") + '" href="' + app + "?start=" + p.id + (yr ? "&yearly=1" : "") + '">' + (paid ? "Choose " + esc(p.name) : "Start free") + "</a>";
      var price = p.price < 0 ? "Custom quote" : yr ? money(p.price * 10) + "<small> a year</small>" : esc(p.priceText) + (paid ? "<small> a month</small>" : "");
      var save = yr ? '<p class="plan-save"><b>2 months free.</b> You save ' + money(p.price * 2) + ", so it works out at " + money(Math.round(p.price * 10 / 12)) + " a month.</p>" : (paid && !yearly ? '<p class="plan-save plan-save-q">Or ' + money(p.price * 10) + " a year: 2 months free</p>" : "");
      return '<div class="plan' + (p.id === "pro" ? " hot" : "") + '"><h3>' + esc(p.name) + '</h3><p class="plan-for">' + esc(p["for"]) + '</p><p class="plan-price">' + price + "</p>" + save + "<ul>" + lines.map(function(l){ return "<li>" + l + "</li>"; }).join("") + "</ul>" + go + "</div>"; }).join(""); }
    if(j.plans.some(function(p){ return p.price > 0; }) && !document.getElementById("planYear")){ var sw = document.createElement("div"); sw.className = "plan-year"; sw.id = "planYear"; sw.setAttribute("role", "group"); sw.setAttribute("aria-label", "How often to pay");
      sw.innerHTML = '<button type="button" data-y="0" class="on" aria-pressed="true">Pay monthly</button><button type="button" data-y="1" aria-pressed="false">Pay for 12 months <span>2 months free</span></button>'; box.parentNode.insertBefore(sw, box);
      sw.addEventListener("click", function(e){ var b = e.target.closest ? e.target.closest("[data-y]") : null; if(!b) return; yearly = b.getAttribute("data-y") === "1"; each("#planYear [data-y]", function(x){ var on = (x.getAttribute("data-y") === "1") === yearly; x.className = on ? "on" : ""; x.setAttribute("aria-pressed", on ? "true" : "false"); }); draw(); }); }
    draw();
  }).catch(function(){});
})();

/* Sign in and Register open in a pop-up over this page, so nobody is sent away from it. The pop-up is the app's own sign-in, in a frame, so every way of signing in works exactly as it does there.
   With scripts off, or if the frame can't load, the links still go to the sign-in page as before. */
(function(){
  var back = null, frame = null, from = null, warm = null, ready = false, timer = 0;
  function isApp(a){ var h = a.getAttribute("href") || ""; return /^(\.\/)?app(\.html)?(\?[^#]*)?$/.test(h); }
  function src(a){ var h = a.getAttribute("href"); return h + (h.indexOf("?") < 0 ? "?" : "&") + "embed=1"; }
  function close(){ if(!back) return; clearTimeout(timer); back.remove(); back = frame = null; ready = false; document.documentElement.classList.remove("am-open"); if(from && document.contains(from)) try{ from.focus(); }catch(e){} from = null; }
  function open(a){ if(back) return; from = a; ready = false;
    back = document.createElement("div"); back.className = "am-back"; back.setAttribute("role", "dialog"); back.setAttribute("aria-modal", "true"); back.setAttribute("aria-label", "Sign in or register");
    back.innerHTML = '<div class="am-card"><button type="button" class="am-x" aria-label="Close">&times;</button><p class="am-load" role="status">Loading...</p><iframe class="am-frame" title="Sign in or register" allow="identity-credentials-get; publickey-credentials-get"></iframe></div>';
    frame = back.querySelector("iframe"); frame.src = src(a); document.body.appendChild(back); document.documentElement.classList.add("am-open");
    back.querySelector(".am-x").focus();
    back.addEventListener("mousedown", function(e){ if(e.target === back) close(); });
    back.querySelector(".am-x").addEventListener("click", close);
    timer = setTimeout(function(){ if(back && !ready){ var href = a.getAttribute("href"); close(); location.href = href; } }, 12000); }   // the frame never spoke: fall back to the page
  document.addEventListener("click", function(e){ if(e.defaultPrevented || e.button || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return; var a = e.target.closest ? e.target.closest("a[href]") : null; if(!a || !isApp(a) || a.target === "_blank") return; e.preventDefault(); open(a); });
  document.addEventListener("keydown", function(e){ if(back && e.key === "Escape") close(); });
  addEventListener("message", function(e){ if(e.origin !== location.origin || !frame || e.source !== frame.contentWindow) return; var d = e.data || {}; if(d.cc !== "auth") return;
    if(d.type === "size"){ ready = true; back.classList.add("ready"); frame.style.height = Math.max(260, Math.ceil(d.h)) + "px"; var card = back.querySelector(".am-card"), ok = /^rgba?\([\d\s.,%\/]+\)$/; if(ok.test(d.bg || "")){ card.style.background = d.bg; frame.style.background = d.bg; } if(ok.test(d.fg || "")) card.style.color = d.fg; }
    else if(d.type === "done"){ var to = (from && from.getAttribute("href")) || "app"; clearTimeout(timer); location.href = to; }
    else if(d.type === "close") close(); });
  /* start fetching the app as soon as someone reaches for the button, so the pop-up opens quickly */
  function warmUp(e){ var a = e.target.closest ? e.target.closest("a[href]") : null; if(!a || !isApp(a) || warm) return; warm = document.createElement("link"); warm.rel = "prefetch"; warm.href = a.getAttribute("href").split("?")[0]; document.head.appendChild(warm); }
  document.addEventListener("pointerover", warmUp, {passive: true}); document.addEventListener("touchstart", warmUp, {passive: true}); document.addEventListener("focusin", warmUp);
})();

/* the front-page film: a voice-over the site made with its own voice, kept in step with the picture. The big play button starts both in one tap,
   which is what lets a phone play the sound. The voice only runs while the picture is really moving, so it can't get ahead while the film is still
   loading. Without a voice-over the film plays silently with its captions, as before. */
(function(){ var v = document.getElementById("demoFilm"), btn = document.getElementById("filmPlay"); if(!v || !btn) return; var a = null, live = false;
  btn.hidden = false;
  function sync(force){ if(!a || a.seeking) return; try{ if(force ? Math.abs(a.currentTime - v.currentTime) > 0.12 : Math.abs(a.currentTime - v.currentTime) > 0.6) a.currentTime = v.currentTime; }catch(e){} }
  function go(){ if(!a) return; live = true; sync(true); a.playbackRate = v.playbackRate; a.volume = v.volume; a.muted = v.muted; var p = a.play(); if(p && p.catch) p.catch(function(){}); }
  function stop(){ live = false; if(a) a.pause(); }
  try{ fetch("speak.php?a=filminfo", {cache: "no-store"}).then(function(r){ return r.ok ? r.json() : null; }).then(function(j){ if(j && j.on){ a = new Audio("speak.php?a=filmwav&v=" + j.v); a.preload = "auto"; try{ a.load(); }catch(e){} v.setAttribute("aria-label", v.getAttribute("aria-label") + ", with a voice-over"); } }).catch(function(){}); }catch(e){}
  btn.addEventListener("click", function(){ btn.hidden = true;
    if(a){ var p = a.play(); if(p && p.then) p.then(function(){ if(!live) a.pause(); else sync(true); }).catch(function(){}); }   /* this tap is what allows the sound later */
    var q = v.play(); if(q && q.catch) q.catch(function(){}); v.focus(); });
  v.addEventListener("play", function(){ btn.hidden = true; });
  v.addEventListener("playing", go);
  v.addEventListener("pause", stop); v.addEventListener("ended", stop); v.addEventListener("waiting", stop); v.addEventListener("seeking", function(){ if(a) a.pause(); });
  v.addEventListener("seeked", function(){ if(!v.paused && live) go(); else sync(true); });
  v.addEventListener("ratechange", function(){ if(a) a.playbackRate = v.playbackRate; });
  v.addEventListener("volumechange", function(){ if(a){ a.volume = v.volume; a.muted = v.muted; } });
  v.addEventListener("timeupdate", function(){ if(a && live && !v.paused){ sync(false); if(a.paused && !a.ended){ var p = a.play(); if(p && p.catch) p.catch(function(){}); } } });
})();
