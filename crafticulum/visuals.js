/* Crafticulum: diagrams and pictures in the teaching content. Shared by the app and the student page.
   A diagram is a small description (a type and some labelled items) that is drawn here, so nothing drawn by anyone else is ever put on the page.
   A picture is a link to a freely licensed photo on Wikimedia Commons, shown with its credit. */
(function(root){
  var esc = function(s){ return String(s == null ? "" : s).replace(/[&<>"']/g, function(c){ return {"&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;"}[c]; }); };
  var str = function(s, n){ return String(s == null ? "" : s).replace(/\s+/g, " ").trim().slice(0, n); };
  var TYPES = ["flow", "cycle", "compare", "tree", "timeline", "layers"];
  var NAMES = {flow: "Step by step", cycle: "Cycle", compare: "Comparison", tree: "Breakdown", timeline: "Timeline", layers: "Layers"};

  function cleanDiagram(d){
    if(!d || typeof d !== "object") return null;
    var type = TYPES.indexOf(String(d.type || "").toLowerCase()) >= 0 ? String(d.type).toLowerCase() : "";
    if(!type) return null;
    var items = (Array.isArray(d.items) ? d.items : []).map(function(it){
      if(typeof it === "string") it = {label: it};
      if(!it || typeof it !== "object") return null;
      var o = {label: str(it.label, 60), note: str(it.note, 130)};
      if(type === "compare") o.points = (Array.isArray(it.points) ? it.points : []).map(function(p){ return str(p, 90); }).filter(Boolean).slice(0, 5);
      return o.label ? o : null;
    }).filter(Boolean).slice(0, type === "compare" ? 3 : 6);
    if(items.length < 2) return null;
    var out = {type: type, title: str(d.title, 80), items: items};
    if(type === "tree"){ out.root = str(d.root, 60) || out.title; if(!out.root) return null; out.items = items.slice(0, 5); }
    return out;
  }
  function cleanImage(m){
    if(!m || typeof m !== "object") return null;
    var o = {search: str(m.search, 80), caption: str(m.caption, 160)};
    if(/^https:\/\/upload\.wikimedia\.org\/[^\s"'<>]+$/.test(String(m.url || ""))){
      o.url = String(m.url); o.page = /^https:\/\/commons\.wikimedia\.org\/[^\s"'<>]+$/.test(String(m.page || "")) ? String(m.page) : "";
      o.by = str(m.by, 80); o.lic = str(m.lic, 40); o.w = Math.max(0, Math.round(+m.w || 0)); o.h = Math.max(0, Math.round(+m.h || 0));
    }
    if(m.n) o.n = Math.max(0, Math.min(40, Math.round(+m.n || 0)));
    if(m.tried) o.tried = 1;
    return o.search || o.url ? o : null;
  }

  /* ---- drawing ---- */
  var W = 960, PAD = 20;
  function wrap(text, max){ var words = String(text || "").split(" "), lines = [], cur = "";
    words.forEach(function(w){ while(w.length > max){ if(cur){ lines.push(cur); cur = ""; } lines.push(w.slice(0, max - 1) + "-"); w = w.slice(max - 1); }
      if(!cur) cur = w; else if((cur + " " + w).length <= max) cur += " " + w; else { lines.push(cur); cur = w; } });
    if(cur) lines.push(cur); return lines; }
  var chars = function(width, size){ return Math.max(6, Math.floor(width / (size * 0.54))); };
  function diagramSvg(d, pal){
    d = cleanDiagram(d); if(!d) return "";
    pal = pal || {};
    var C = {ink: pal.ink || "var(--dg-ink,#111827)", mute: pal.mute || "var(--dg-mute,#4b5563)", line: pal.line || "var(--dg-line,#c3c8d4)", box: pal.box || "var(--dg-box,#eef2ff)", acc: pal.accent || "var(--dg-accent,#4f46e5)", on: pal.on || "var(--dg-on,#ffffff)"};
    var font = pal.font || "inherit", out = [], y = PAD;
    var T = function(x, yy, lines, size, color, weight, anchor){ return '<text x="' + x + '" y="' + yy + '" font-size="' + size + '" font-weight="' + (weight || 400) + '" text-anchor="' + (anchor || "start") + '" style="fill:' + color + '">' +
      lines.map(function(l, i){ return '<tspan x="' + x + '" dy="' + (i ? size * 1.25 : 0) + '">' + esc(l) + "</tspan>"; }).join("") + "</text>"; };
    var R = function(x, yy, w, h, fill, stroke, r){ return '<rect x="' + x + '" y="' + yy + '" width="' + w + '" height="' + h + '" rx="' + (r == null ? 14 : r) + '" style="fill:' + fill + ";stroke:" + (stroke || "none") + ';stroke-width:1.5"/>'; };
    var badge = function(cx, cy, n){ return '<circle cx="' + cx + '" cy="' + cy + '" r="15" style="fill:' + C.acc + '"/>' + T(cx, cy + 6, [String(n)], 16, C.on, 700, "middle"); };
    var arrow = function(x1, y1, x2, y2){ var a = Math.atan2(y2 - y1, x2 - x1), s = 9, hx = x2 - Math.cos(a) * 2, hy = y2 - Math.sin(a) * 2;
      return '<line x1="' + x1 + '" y1="' + y1 + '" x2="' + x2 + '" y2="' + y2 + '" style="stroke:' + C.acc + ';stroke-width:2.5;stroke-linecap:round"/>' +
        '<path d="M' + hx + " " + hy + " L" + (hx - Math.cos(a - 0.5) * s * 1.5) + " " + (hy - Math.sin(a - 0.5) * s * 1.5) + " L" + (hx - Math.cos(a + 0.5) * s * 1.5) + " " + (hy - Math.sin(a + 0.5) * s * 1.5) + ' Z" style="fill:' + C.acc + '"/>'; };
    if(d.title && d.type !== "tree"){ var tl = wrap(d.title, chars(W - PAD * 2, 22)); out.push(T(PAD, y + 22, tl, 22, C.ink, 700)); y += tl.length * 28 + 16; }
    var n = d.items.length, it = d.items;

    if(d.type === "flow" && n <= 4){
      var gap = 44, bw = (W - PAD * 2 - gap * (n - 1)) / n, cw = chars(bw - 32, 19), nw = chars(bw - 32, 15);
      var L = it.map(function(x){ return {a: wrap(x.label, cw), b: wrap(x.note, nw)}; });
      var bh = Math.max.apply(null, L.map(function(l){ return 58 + l.a.length * 24 + (l.b.length ? 8 + l.b.length * 19 : 0); })) + 12;
      it.forEach(function(x, i){ var bx = PAD + i * (bw + gap);
        out.push(R(bx, y, bw, bh, C.box, C.line)); out.push(badge(bx + 30, y + 30, i + 1));
        out.push(T(bx + 16, y + 74, L[i].a, 19, C.ink, 700)); if(L[i].b.length) out.push(T(bx + 16, y + 74 + L[i].a.length * 24 + 6, L[i].b, 15, C.mute));
        if(i < n - 1) out.push(arrow(bx + bw + 8, y + bh / 2, bx + bw + gap - 8, y + bh / 2)); });
      y += bh;
    } else if(d.type === "flow" || d.type === "timeline" && n > 5){
      it.forEach(function(x, i){ var a = wrap(x.label, chars(W - 110, 19)), b = wrap(x.note, chars(W - 110, 15)), h = Math.max(46, a.length * 24 + (b.length ? 4 + b.length * 19 : 0) + 10);
        if(i < n - 1) out.push('<line x1="' + (PAD + 18) + '" y1="' + (y + 34) + '" x2="' + (PAD + 18) + '" y2="' + (y + h + 12) + '" style="stroke:' + C.line + ';stroke-width:2.5"/>');
        out.push(badge(PAD + 18, y + 18, i + 1)); out.push(T(PAD + 54, y + 24, a, 19, C.ink, 700)); if(b.length) out.push(T(PAD + 54, y + 24 + a.length * 24, b, 15, C.mute));
        y += h + 12; });
      y -= 12;
    } else if(d.type === "cycle"){
      var bw2 = 250, cw2 = chars(bw2 - 28, 18), nw2 = chars(bw2 - 28, 14);
      var L2 = it.map(function(x){ return {a: wrap(x.label, cw2), b: wrap(x.note, nw2).slice(0, 3)}; });
      var bh2 = Math.max.apply(null, L2.map(function(l){ return 22 + l.a.length * 23 + (l.b.length ? 4 + l.b.length * 18 : 0); })) + 14;
      var rx = (W - PAD * 2 - bw2) / 2, ry = Math.max(120, 60 + n * 28), cx = W / 2, cy = y + ry + bh2 / 2;
      var pos = it.map(function(_, i){ var a = -Math.PI / 2 + i * 2 * Math.PI / n; return {x: cx + Math.cos(a) * rx, y: cy + Math.sin(a) * ry, a: a}; });
      out.push('<ellipse cx="' + cx + '" cy="' + cy + '" rx="' + rx + '" ry="' + ry + '" style="fill:none;stroke:' + C.acc + ';stroke-width:2.5;stroke-dasharray:2 9;stroke-linecap:round"/>');
      pos.forEach(function(p, i){ var a = p.a + Math.PI / n, mx = cx + Math.cos(a) * rx, my = cy + Math.sin(a) * ry, tx = -Math.sin(a) * rx, ty = Math.cos(a) * ry, len = Math.sqrt(tx * tx + ty * ty) || 1;
        out.push(arrow(mx - tx / len * 12, my - ty / len * 12, mx + tx / len * 12, my + ty / len * 12)); });
      pos.forEach(function(p, i){ var bx = p.x - bw2 / 2, by = p.y - bh2 / 2;
        out.push(R(bx, by, bw2, bh2, C.box, C.line)); out.push(badge(bx + 4, by + 4, i + 1));
        out.push(T(p.x, by + 30, L2[i].a, 18, C.ink, 700, "middle")); if(L2[i].b.length) out.push(T(p.x, by + 30 + L2[i].a.length * 23, L2[i].b, 14, C.mute, 400, "middle")); });
      y = cy + ry + bh2 / 2;
    } else if(d.type === "compare"){
      var g3 = 18, cw3 = (W - PAD * 2 - g3 * (n - 1)) / n, hc = chars(cw3 - 32, 19), pc = chars(cw3 - 58, 15);
      var cols = it.map(function(x){ var pts = (x.points && x.points.length ? x.points : x.note ? [x.note] : []).map(function(p){ return wrap(p, pc); }); return {h: wrap(x.label, hc), pts: pts}; });
      var hh = Math.max.apply(null, cols.map(function(c){ return c.h.length; })) * 24 + 24;
      var bh3 = Math.max.apply(null, cols.map(function(c){ return c.pts.reduce(function(s, p){ return s + p.length * 19 + 12; }, 0); })) + 22;
      cols.forEach(function(c, i){ var bx = PAD + i * (cw3 + g3);
        out.push(R(bx, y, cw3, hh + bh3, C.box, C.line)); out.push('<path d="M' + bx + " " + (y + hh) + " V" + (y + 14) + " a14 14 0 0 1 14 -14 H" + (bx + cw3 - 14) + " a14 14 0 0 1 14 14 V" + (y + hh) + ' Z" style="fill:' + C.acc + '"/>');
        out.push(T(bx + 16, y + 30, c.h, 19, C.on, 700));
        var py = y + hh + 28; c.pts.forEach(function(p){ out.push('<circle cx="' + (bx + 24) + '" cy="' + (py - 5) + '" r="4" style="fill:' + C.acc + '"/>'); out.push(T(bx + 40, py, p, 15, C.ink)); py += p.length * 19 + 12; }); });
      y += hh + bh3;
    } else if(d.type === "tree"){
      var rl = wrap(d.root, 34), rw = Math.min(460, Math.max(220, Math.max.apply(null, rl.map(function(l){ return l.length; })) * 12 + 48)), rh = rl.length * 26 + 26, rxx = (W - rw) / 2;
      out.push(R(rxx, y, rw, rh, C.acc)); out.push(T(W / 2, y + 33, rl, 20, C.on, 700, "middle"));
      var g4 = 16, cw4 = (W - PAD * 2 - g4 * (n - 1)) / n, lc = chars(cw4 - 24, 17), nc = chars(cw4 - 24, 14), ty4 = y + rh + 54;
      var L4 = it.map(function(x){ return {a: wrap(x.label, lc), b: wrap(x.note, nc)}; });
      var bh4 = Math.max.apply(null, L4.map(function(l){ return 16 + l.a.length * 22 + (l.b.length ? 4 + l.b.length * 18 : 0); })) + 14;
      var mid = y + rh + 27, x0 = PAD + cw4 / 2, x1 = PAD + (n - 1) * (cw4 + g4) + cw4 / 2;
      out.push('<path d="M' + W / 2 + " " + (y + rh) + " V" + mid + " M" + x0 + " " + mid + " H" + x1 + '" style="fill:none;stroke:' + C.line + ';stroke-width:2.5"/>');
      it.forEach(function(x, i){ var bx = PAD + i * (cw4 + g4), mx = bx + cw4 / 2;
        out.push('<line x1="' + mx + '" y1="' + mid + '" x2="' + mx + '" y2="' + ty4 + '" style="stroke:' + C.line + ';stroke-width:2.5"/>');
        out.push(R(bx, ty4, cw4, bh4, C.box, C.line)); out.push(T(mx, ty4 + 28, L4[i].a, 17, C.ink, 700, "middle")); if(L4[i].b.length) out.push(T(mx, ty4 + 28 + L4[i].a.length * 22, L4[i].b, 14, C.mute, 400, "middle")); });
      y = ty4 + bh4;
    } else if(d.type === "timeline"){
      var cw5 = (W - PAD * 2) / n, lc5 = chars(cw5 - 20, 17), nc5 = chars(cw5 - 20, 14), ly = y + 18;
      out.push('<line x1="' + (PAD + cw5 / 2) + '" y1="' + ly + '" x2="' + (W - PAD - cw5 / 2) + '" y2="' + ly + '" style="stroke:' + C.line + ';stroke-width:3"/>');
      var maxH = 0;
      it.forEach(function(x, i){ var mx = PAD + i * cw5 + cw5 / 2, a = wrap(x.label, lc5), b = wrap(x.note, nc5);
        out.push(badge(mx, ly, i + 1)); out.push(T(mx, ly + 46, a, 17, C.ink, 700, "middle")); if(b.length) out.push(T(mx, ly + 46 + a.length * 22, b, 14, C.mute, 400, "middle"));
        maxH = Math.max(maxH, 46 + a.length * 22 + b.length * 18); });
      y = ly + maxH;
    } else {   // layers
      var lw = 300;
      it.forEach(function(x, i){ var a = wrap(x.label, chars(lw - 60, 18)), b = wrap(x.note, chars(W - PAD * 2 - lw - 30, 15)), h = Math.max(50, Math.max(a.length * 23, b.length * 19) + 22);
        out.push(R(PAD, y, W - PAD * 2, h, C.box, C.line, 10)); out.push('<path d="M' + (PAD + lw) + " " + y + " H" + (PAD + 10) + " a10 10 0 0 0 -10 10 V" + (y + h - 10) + " a10 10 0 0 0 10 10 H" + (PAD + lw) + ' Z" style="fill:' + C.acc + '"/>');
        out.push(T(PAD + 16, y + h / 2 - (a.length - 1) * 11.5 + 6, a, 18, C.on, 700)); if(b.length) out.push(T(PAD + lw + 18, y + h / 2 - (b.length - 1) * 9.5 + 5, b, 15, C.ink));
        y += h + 8; });
      y -= 8;
    }
    var H = Math.ceil(y + PAD), label = (d.title || NAMES[d.type]) + ": " + (d.root ? d.root + ". " : "") + it.map(function(x){ return x.label; }).join(", ");
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' + W + " " + H + '" width="' + W + '" height="' + H + '" role="img" aria-label="' + esc(label) + '" font-family="' + esc(font) + '"' + (pal.bg ? ' style="background:' + pal.bg + '"' : "") + ">" +
      (pal.bg ? '<rect width="' + W + '" height="' + H + '" style="fill:' + pal.bg + '"/>' : "") + out.join("") + "</svg>";
  }

  /* the picture and diagram for one part of the teaching content, as HTML */
  function visualHtml(t, o){
    if(!t) return ""; o = o || {};
    var dg = o.pic === true ? "" : diagramSvg(t.diagram), im = o.dg === true ? null : cleanImage(t.image), parts = [];
    if(dg) parts.push('<figure class="cc-dg">' + dg + "</figure>");
    if(im && im.url) parts.push('<figure class="cc-pic"><img src="' + esc(im.url) + '" alt="' + esc(im.caption || im.search || "Picture for this part") + '" loading="lazy" decoding="async" referrerpolicy="no-referrer"' + (im.w && im.h ? ' width="' + im.w + '" height="' + im.h + '"' : "") + ">" +
      "<figcaption>" + (im.caption ? "<span>" + esc(im.caption) + "</span> " : "") + '<span class="cc-credit">Picture' + (im.by ? ": " + esc(im.by) : "") + (im.lic ? ", " + esc(im.lic) : "") + (im.page ? ', <a href="' + esc(im.page) + '" target="_blank" rel="noopener noreferrer">Wikimedia Commons</a>' : ", Wikimedia Commons") + "</span></figcaption></figure>");
    return parts.length ? '<div class="cc-visual' + (parts.length > 1 ? " two" : "") + '">' + parts.join("") + "</div>" : "";
  }
  root.ccCleanDiagram = cleanDiagram; root.ccCleanImage = cleanImage; root.ccDiagramSvg = diagramSvg; root.ccVisualHtml = visualHtml;
})(typeof window !== "undefined" ? window : globalThis);
