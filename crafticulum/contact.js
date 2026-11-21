/* Crafticulum: the contact form. Sends to auth.php, which keeps the message for the site's admin and emails it on. */
(function(){
  var f = document.getElementById("cf"); if(!f) return;
  var $ = function(id){ return document.getElementById(id); }, opened = Date.now();
  var labels = {support: ["How can we help?", "Say what you were doing and what happened. Please don't include learners' names or their work."], problem: ["What went wrong?", "Say which page you were on, what you pressed and what you saw. Please don't include learners' names or their work."],
    feedback: ["What would you like to tell us?", "Good or bad, it all helps."], enquiry: ["Tell us a bit about what you teach", "Which subjects and levels, roughly how many staff, and anything you'd want it to do."]};
  function kind(){ var r = f.querySelector('input[name="kind"]:checked'); return r ? r.value : "support"; }
  function paint(){ var k = kind(); $("cfMsgLabel").textContent = labels[k][0]; $("cfHint").textContent = labels[k][1]; $("cfOrgRow").hidden = k !== "enquiry"; }
  f.addEventListener("change", function(e){ if(e.target.name === "kind") paint(); });
  var want = (new URLSearchParams(location.search).get("about") || "").replace(/[^a-z]/g, ""); if(labels[want]){ f.querySelector('input[value="' + want + '"]').checked = true; }
  paint();
  var from = (new URLSearchParams(location.search).get("from") || "").replace(/[^a-z-]/g, "").slice(0, 30);
  /* signed in already? fill in the name and email */
  fetch("auth.php?a=me", {method: "POST", headers: {"Content-Type": "application/json"}, credentials: "same-origin", body: "{}"}).then(function(r){ return r.ok ? r.json() : null; }).then(function(j){
    if(!j || !j.me || j.me.demo) return; if(!$("cfName").value) $("cfName").value = j.me.name || ""; if(!$("cfEmail").value && /@/.test(j.me.email || "") && !/@password$/.test(j.me.email)) $("cfEmail").value = j.me.email; }).catch(function(){});
  function fail(t, el){ var e = $("cfErr"); e.textContent = t; e.hidden = false; if(el) el.focus(); }
  function browser(){ var u = navigator.userAgent, m = u.match(/(Edg|OPR|Firefox|CriOS|FxiOS|Chrome|Version)\/(\d+)/) || [], n = {Edg: "Edge", OPR: "Opera", CriOS: "Chrome", FxiOS: "Firefox", Version: "Safari"}[m[1]] || m[1] || "Browser";
    var os = /iPad/.test(u) || (navigator.maxTouchPoints > 1 && /Macintosh/.test(u)) ? "iPad" : /iPhone/.test(u) ? "iPhone" : /Android/.test(u) ? "Android" : /Windows/.test(u) ? "Windows" : /Mac OS X/.test(u) ? "Mac" : /CrOS/.test(u) ? "Chromebook" : "";
    return n + (m[2] ? " " + m[2] : "") + (os ? " on " + os : ""); }
  f.addEventListener("submit", function(e){
    e.preventDefault(); $("cfErr").hidden = true;
    var name = $("cfName").value.trim(), email = $("cfEmail").value.trim(), msg = $("cfMsg").value.trim();
    if(!name) return fail("Add your name so we know who to reply to.", $("cfName"));
    if(!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) return fail("That email address doesn't look right. Check it so we can reply.", $("cfEmail"));
    if(msg.length < 10) return fail("Tell us a little more so we can help.", $("cfMsg"));
    var b = $("cfSend"); b.disabled = true; b.textContent = "Sending...";
    fetch("auth.php?a=contact", {method: "POST", headers: {"Content-Type": "application/json"}, credentials: "same-origin",
      body: JSON.stringify({kind: kind(), name: name, email: email, org: $("cfOrg").value.trim(), msg: msg, website: $("cfWeb").value, took: Date.now() - opened, page: from, browser: browser(), shot: shot || undefined})})
      .then(function(r){ return r.json().catch(function(){ return {}; }).then(function(j){ return {ok: r.ok, status: r.status, j: j}; }); })
      .then(function(x){ b.disabled = false; b.textContent = "Send message";
        if(!x.ok) return fail(x.status === 429 ? "That's a lot of messages in a short time. Try again in an hour." : x.j.error === "email" ? "That email address doesn't look right. Check it so we can reply." : x.j.error === "short" ? "Tell us a little more so we can help." : "That didn't send. Check your connection and try again.");
        f.hidden = true; $("cfDone").hidden = false; $("cfDoneText").textContent = "We'll reply to " + email + "."; $("cfDone").scrollIntoView({block: "center"}); })
      .catch(function(){ b.disabled = false; b.textContent = "Send message"; fail("That didn't send. Check your connection and try again."); });
  });
  /* a screenshot: made smaller on the device before it is sent */
  var shot = "", pick = $("cfShot");
  function shrink(file, done){ var u = URL.createObjectURL(file), im = new Image(); im.onload = function(){ try{ var w = im.naturalWidth, h = im.naturalHeight, k = Math.min(1, 1500 / Math.max(w, h)), q = .78, out = ""; w = Math.max(1, Math.round(w * k)); h = Math.max(1, Math.round(h * k));
        for(var i = 0; i < 4; i++){ var c = document.createElement("canvas"); c.width = w; c.height = h; var g = c.getContext("2d"); g.fillStyle = "#fff"; g.fillRect(0, 0, w, h); g.drawImage(im, 0, 0, w, h); out = c.toDataURL("image/jpeg", q); if(out.length < 600000) break; w = Math.round(w * .75); h = Math.round(h * .75); q -= .08; }
        URL.revokeObjectURL(u); done(out.length < 800000 ? out : ""); }catch(e){ done(""); } }; im.onerror = function(){ URL.revokeObjectURL(u); done(""); }; im.src = u; }
  if(pick) pick.addEventListener("change", function(){ var fl = pick.files && pick.files[0], hint = $("cfShotHint"); shot = ""; if(!fl) return; hint.textContent = "Getting it ready..."; shrink(fl, function(d){ shot = d; hint.textContent = d ? "Added. It will be sent with your message." : "That picture couldn't be read. Try a PNG or JPEG."; if(!d) pick.value = ""; }); });
  $("cfAgain").addEventListener("click", function(){ $("cfMsg").value = ""; shot = ""; if(pick){ pick.value = ""; $("cfShotHint").textContent = "A picture of what you saw. Please make sure it doesn't show a learner's name."; } opened = Date.now(); $("cfDone").hidden = true; f.hidden = false; $("cfMsg").focus(); });
})();
