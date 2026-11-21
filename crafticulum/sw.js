/* Crafticulum: keeps the app itself on this device so it opens without the internet.
   Lessons, planning and student links still come from your server when you're online. */
const CACHE = "cc-shell-v78";
const SHELL = ["app.html", "manifest.webmanifest", "icon-192.png", "icon-512.png"];
self.addEventListener("install", e => { e.waitUntil(caches.open(CACHE).then(c => c.addAll(SHELL)).then(() => self.skipWaiting())); });
self.addEventListener("activate", e => { e.waitUntil(caches.keys().then(ks => Promise.all(ks.filter(k => k !== CACHE && k !== OFFLINE).map(k => caches.delete(k)))).then(() => self.clients.claim())); });
/* Offline: the last answer to "who am I" and "what is in my department" is kept on this device (and wiped when you sign out).
   With no internet those are given back, so the lessons, planning and slides you had open before are all still there to teach from. Nothing can be saved until you're back online. */
const OFFLINE = "cc-offline-v1", KEEP = /^(auth\.php\?a=me|store\.php\?a=(all|deptsubjects|directory|tplinfo|library))$/;
async function keepPost(r){ const body = await r.clone().text(); let h = 0; for(let i = 0; i < body.length; i++) h = (h * 31 + body.charCodeAt(i)) | 0; const key = new Request(r.url + "&_b=" + (h >>> 0));
  try{ const res = await fetch(r); if(res.ok){ const c = await caches.open(OFFLINE); c.put(key, res.clone()); } return res; }
  catch(err){ const m = await caches.match(key, {cacheName: OFFLINE}); if(!m) throw err; const hd = new Headers(m.headers); hd.set("X-CC-Offline", "1"); return new Response(await m.blob(), {status: 200, headers: hd}); } }
self.addEventListener("fetch", e => {
  const r = e.request;
  if(r.method === "POST"){ const u = new URL(r.url); if(u.origin === location.origin && KEEP.test(u.pathname.split("/").pop() + u.search)) e.respondWith(keepPost(r)); return; }
  if(r.method !== "GET") return;
  const u = new URL(r.url);
  if(u.origin === location.origin){
    if(/\.php$/.test(u.pathname) || u.pathname.includes("/data/")) return;          // live data always comes from the server
    if(r.mode === "navigate" || /\.html?$/.test(u.pathname)){                         // pages: newest when online, saved copy when not
      e.respondWith(fetch(r).then(res => { if(res.ok){ const c = res.clone(); caches.open(CACHE).then(x => x.put(r, c)); } return res; })
        .catch(() => caches.match(r).then(m => m || caches.match("app.html"))));
      return;
    }
    e.respondWith(caches.match(r).then(m => { const net = fetch(r).then(res => { if(res.ok){ const c = res.clone(); caches.open(CACHE).then(x => x.put(r, c)); } return res; }).catch(() => m); return m || net; }));
    return;
  }
  if(/cdnjs\.cloudflare\.com|fonts\.(googleapis|gstatic)\.com/.test(u.host)){             // libraries and fonts: saved after the first visit
    e.respondWith(caches.match(r).then(m => m || fetch(r).then(res => { const c = res.clone(); caches.open(CACHE).then(x => x.put(r, c)); return res; })));
  }
});

/* notifications: show what the server sent, and open the app at the right place when it's pressed */
self.addEventListener("push", e => {
  let d = {}; try{ d = e.data ? e.data.json() : {}; }catch{ try{ d = {title: "Crafticulum", body: e.data.text()}; }catch{} }
  e.waitUntil(self.registration.showNotification(d.title || "Crafticulum", {body: d.body || "", icon: "icon-192.png", badge: "favicon-32.png", tag: d.tag || undefined, data: {url: d.url || ""}}));
});
self.addEventListener("notificationclick", e => {
  e.notification.close(); const url = (e.notification.data && e.notification.data.url) || new URL("app.html", self.registration.scope).href;
  e.waitUntil(self.clients.matchAll({type: "window", includeUncontrolled: true}).then(cs => { const c = cs.find(x => new URL(x.url).origin === location.origin); if(c){ c.focus(); if(e.notification.data && e.notification.data.go && c.navigate) return c.navigate(url).catch(() => {}); return; } return self.clients.openWindow(url); }));
});
