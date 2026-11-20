/* the sample lesson's exit quiz: pick an answer, see if it was right and why */
(function(){ var box = document.getElementById("smQuiz"); if(!box) return; var done = 0, right = 0, total = box.querySelectorAll(".sm-q").length;
  box.addEventListener("click", function(e){ var b = e.target.closest ? e.target.closest(".sm-o") : null; if(!b) return; var q = b.closest(".sm-q"); if(q.dataset.done) return; q.dataset.done = "1";
    var a = parseInt(q.dataset.a, 10), i = parseInt(b.dataset.i, 10); q.querySelectorAll(".sm-o").forEach(function(o, n){ o.disabled = true; if(n === a) o.classList.add("ok"); else if(n === i) o.classList.add("no"); });
    var w = q.querySelector(".sm-why"); w.hidden = false; w.textContent = (i === a ? "Correct. " : "Not quite. ") + w.textContent; done++; if(i === a) right++;
    if(done === total){ var s = document.getElementById("smScore"); s.hidden = false; s.textContent = "You got " + right + " out of " + total + ". Your learners' scores land on your screen as they answer."; } }); })();
/* the Print link on the security page */
(function(){ var p = document.querySelector("[data-print]"); if(p) p.addEventListener("click", function(e){ e.preventDefault(); window.print(); }); })();
