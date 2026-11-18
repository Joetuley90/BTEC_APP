/* Crafticulum: fills the public pages with this site's name, logo, colour and contact email */
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
    box.innerHTML = j.plans.map(function(p){ var paid = p.price > 0, lines = [p.people > 0 ? (p.people === 1 ? "Just you" : "Up to " + p.people + " people") : "As many people as you need", p.depts > 0 ? (p.depts === 1 ? "One department" : p.depts + " departments") : "As many departments as you need",
        p.builds > 0 ? p.builds + " builds with Claude a month" : "No monthly limit on builds", p.checks > 0 ? p.checks + " Second Marker checks a month" : "No monthly limit on Second Marker"];
      var go = p.price < 0 ? '<a class="btn btn-o" href="' + contact + '?about=enquiry">Ask for a quote</a>' : '<a class="btn ' + (p.id === "pro" ? "btn-p" : "btn-o") + '" href="' + app + "?start=" + p.id + '">' + (paid ? "Choose " + esc(p.name) : "Start free") + "</a>";
      return '<div class="plan' + (p.id === "pro" ? " hot" : "") + '"><h3>' + esc(p.name) + '</h3><p class="plan-for">' + esc(p["for"]) + '</p><p class="plan-price">' + (p.price < 0 ? "Custom quote" : esc(p.priceText) + (paid ? "<small> a month</small>" : "")) + "</p><ul>" + lines.map(function(l){ return "<li>" + l + "</li>"; }).join("") + "</ul>" + go + "</div>"; }).join("");
  }).catch(function(){});
})();
