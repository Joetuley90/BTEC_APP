# Crafticulum releases

Every release: what changed, and exactly which files were pushed. Newest first.
From 1.48 on, versions are written with a point. Release 1.48 is build 148, and so on.

## 1.49 · 21 November 2026

**Invoice numbers end with a random code**

- When an invoice is sent, its number ends with a random six-character code instead of a count. For example `CR-2026-7KQ4XM` rather than `CR-2026-0003`, so nobody can tell from their invoice how many have gone out before it.
- The prefix and year work as before (Admin, Money, invoice settings). Letters and digits that look alike (0/O, 1/I/L) are left out.
- Each number is checked against every earlier one, so it's never used twice.
- The same number is on the email, the invoice page and the PDF saved from it.
- Invoices already sent keep their numbers.

**After updating:** nothing to do.

**Tested:** pwmoney 14 (an invoice is sent and gets a number in the new format), pw107 45, pwpolish 10.

### What was pushed

- Commit `0081cca` on `main` (1 added, 6 changed, 1 removed).
- Zip: `crafticulum-v1.49.zip`, 3,667,922 bytes, no `config.php` inside.
- Build number: 149 (shown to people as 1.49).

| | File |
|---|---|
| changed | `README.md` |
| changed | `crafticulum-release.zip` |
| removed | `crafticulum-v1.48.zip` |
| added | `crafticulum-v1.49.zip` |
| changed | `crafticulum/README.txt` |
| changed | `crafticulum/money.php` |
| changed | `crafticulum/sw.js` |
| changed | `crafticulum/version.json` |

## 1.48 · 21 November 2026

**No college's own forms, 12 month pricing on the front page, versions with a point**

- **No college's own forms.** The four documents that belonged to the first college on the original site (a PowerPoint master, a lesson plan form and two scheme of learning forms) are no longer in Crafticulum, the zip or the code. On a site that already has them, they are moved once from `assets/` into `data/_owntpl`, kept with that site's backups, and given only to people in that college. Everyone else gets Crafticulum's own templates, or the ones they upload.
- **If those forms are ever missing**, that college's downloads fall back to Crafticulum's own templates and don't fail.
- **Pricing.** The front page has a switch: Pay monthly, or Pay for 12 months with 2 months free. The 12 month view shows the yearly price, the saving and what it works out at a month. The choice carries through sign-up to the payment page.
- **Version names.** Releases are written with a point: this is 1.48, the last was 1.47. The update page, problem reports and the fix pack show it that way. How updates work is unchanged.
- **This file.** `RELEASES.md` lists every release and the files pushed.
- Other institutions no longer download the old PowerPoint master in the background.

**After updating:** nothing to do. Don't delete the `assets` folder before updating, because the first college's forms are picked up from it.

**Tested:** pwforms 4, pwtidy 11, pw107 45, pwpolish 10, pwfix 8, pwsite 19, pwteach, pwmore 32, pwphone 40, pwroles 28, pwvideo 13, pwg 7, pw50 20, pw52 25, pw53 15, pwnext 17, pwfilm 7, pwcontact 17, pwplain 31, pwreport 8. Not yet tried on the live site.

### What was pushed

- Commit `3bed343` on `main` (1 added, 22 changed, 4 removed).
- Zip: `crafticulum-v1.48.zip`, 3,667,650 bytes, no `config.php` inside.
- Build number: 148 (shown to people as 1.48).

| | File |
|---|---|
| changed | `README.md` |
| added | `RELEASES.md` |
| changed | `crafticulum-release.zip` |
| R080 | `crafticulum-v1.48.zip` |
| changed | `crafticulum/README.txt` |
| changed | `crafticulum/app.html` |
| removed | `crafticulum/assets/tpl-deck.txt` |
| removed | `crafticulum/assets/tpl-lp.txt` |
| removed | `crafticulum/assets/tpl-sol.txt` |
| removed | `crafticulum/assets/tpl-solfull.txt` |
| changed | `crafticulum/btec-esports-lesson-plans.html` |
| changed | `crafticulum/btec-it-lesson-plans.html` |
| changed | `crafticulum/contact.html` |
| changed | `crafticulum/cookies.html` |
| changed | `crafticulum/dpa.html` |
| changed | `crafticulum/functional-skills-lesson-plans.html` |
| changed | `crafticulum/gcse-computer-science-lesson-plans.html` |
| changed | `crafticulum/index.html` |
| changed | `crafticulum/pages.css` |
| changed | `crafticulum/pages.js` |
| changed | `crafticulum/privacy.html` |
| changed | `crafticulum/sample.html` |
| changed | `crafticulum/security.html` |
| changed | `crafticulum/store.php` |
| changed | `crafticulum/sw.js` |
| changed | `crafticulum/t-level-digital-lesson-plans.html` |
| changed | `crafticulum/terms.html` |
| changed | `crafticulum/version.json` |

## Before 1.48

Taken from the repository's history. One line for each push.

| Commit | Date | What | Files |
|---|---|---|---|
| `37f8170` | 21 Nov 2026 | 1.47: release notes no longer name a college (rest of them) | 4 |
| `c1138fc` | 21 Nov 2026 | 1.47: release notes no longer name a college | 4 |
| `1f93896` | 21 Nov 2026 | 1.47: film remade with the logo, voice-over fitted to captions, no college named in the app | 27 |
| `a219545` | 21 Nov 2026 | 1.46: six-step how-to film with a voice-over made from Site setup | 26 |
| `0ffceb6` | 21 Nov 2026 | 1.45: Google voice in the video player, choice of voice type and accent | 11 |
| `e7e5bc6` | 21 Nov 2026 | 1.44: phone board bar, section menu, swipe, planning buttons, tell reporters, ask before the referral cookie | 10 |
| `82ac19a` | 21 Nov 2026 | 1.43: review reports and line up a fix pack, contact form screenshot, phone lesson bar | 12 |
| `8b6b4a7` | 21 Nov 2026 | Crafticulum v142: tidier Settings, first steps for managers, yearly plans, Learners marks, phone pass | 12 |
| `ec4b58d` | 21 Nov 2026 | Crafticulum v141: Integrations, off until an institution switches them on | 12 |
| `95a04e0` | 21 Nov 2026 | Crafticulum v140: lesson library, feedback, versions, offline, refunds, referrals, status page | 32 |
| `970d148` | 20 Nov 2026 | Crafticulum v139: Business dashboard, invoices, sample lesson, security page, test-week polish | 32 |
| `da3a45d` | 20 Nov 2026 | Crafticulum v138: full ICO number and backup location on the legal pages | 8 |
| `7b04896` | 20 Nov 2026 | Crafticulum v137: data processing agreement page, and where data is kept | 14 |
| `55f1e92` | 20 Nov 2026 | Crafticulum v136: the company behind Crafticulum is named on the legal pages | 12 |
| `154e551` | 20 Nov 2026 | Crafticulum v135: the terms give a full refund in the first 30 days | 7 |
| `3e43f0d` | 20 Nov 2026 | Crafticulum v134: first lesson with no setting up, a voice on video lessons, Classroom and Teams, public profiles | 13 |
| `6f7b8a9` | 20 Nov 2026 | Crafticulum v133: front page without the questions section | 7 |
| `fad1e0f` | 20 Nov 2026 | Crafticulum v132: front page shorter again, with how to say the name | 8 |
| `140e098` | 20 Nov 2026 | Crafticulum v131: shorter front page, link preview, quiz code slide | 16 |
| `2031a1f` | 20 Nov 2026 | Crafticulum v130: team on the Department page, profile pages, multi-department box | 11 |
| `5c16e03` | 20 Nov 2026 | Crafticulum v129: department switcher on every page, rename a subject | 8 |
| `5679c49` | 20 Nov 2026 | Crafticulum v128: department drop-down on Subjects and Department, built-in subjects listed | 7 |
| `35372d1` | 20 Nov 2026 | Crafticulum v127: department tiles, Subjects tab, easier lesson tabs | 8 |
| `866ecb5` | 20 Nov 2026 | Crafticulum v126: report a problem from the chat, one PowerPoint template for all | 8 |
| `61f50c3` | 20 Nov 2026 | Crafticulum v125: own templates, neutral author, Team on the Institution page | 9 |
| `3d1beac` | 20 Nov 2026 | Crafticulum v124: the super admin stands outside every institution | 9 |
| `8449c33` | 20 Nov 2026 | Crafticulum v123: Open lesson shows the lesson on its own | 7 |
| `3a0e7ac` | 20 Nov 2026 | Crafticulum v122: share quiz results with the class | 9 |
| `c55a6b2` | 20 Nov 2026 | Crafticulum v121: multi-department staff, split a department by subject, Crafti board, super admin bar, roadmap menu fix | 10 |
| `9564704` | 20 Nov 2026 | Crafticulum v120: Crafti to quiz and worksheet, the Bin in the Library, manager's overview | 8 |
| `c744aeb` | 20 Nov 2026 | Crafticulum v119: phone layout fix (v118 shipped an unfinished app page), and Go back to the last version | 9 |
| `b63c0ef` | 20 Nov 2026 | Crafticulum v118: simpler, from the audit; super admin menu fixed | 8 |
| `2d7dede` | 20 Nov 2026 | Crafticulum v117: departments in the planner, Admin area, Our team, photos, one profile, Crafti actions, MP4 video | 12 |
| `26f584d` | 19 Nov 2026 | Crafticulum v116: video lessons, Crafti live chat, fuller staff profiles, empty new workspaces, phone fix | 10 |
| `cf05000` | 19 Nov 2026 | Crafticulum v115: redesigned roadmap, last of the institution wording | 10 |
| `09d49ae` | 19 Nov 2026 | Crafticulum v114: a roadmap to vote on, five AI tools to start, institution wording | 17 |
| `15399a9` | 19 Nov 2026 | Crafticulum v113: the sign-in pop-up matches dark mode | 14 |
| `290f5ba` | 19 Nov 2026 | Crafticulum v112: sign-in pop-up on the public pages, and an empty demo that follows the visitor's profile | 15 |
| `3af3541` | 19 Nov 2026 | Crafticulum v111: staff profiles, no duplicate colleges, merge, learner reports | 10 |
| `9786cf7` | 19 Nov 2026 | Crafticulum v110: gradients and colours restored, squarer corners kept | 13 |
| `058df91` | 19 Nov 2026 | Crafticulum v109: a plainer look | 13 |
| `ce3e1a5` | 19 Nov 2026 | Crafticulum v108: a proper menu on phones, profile row hidden on the dashboard | 7 |
| `e4c3454` | 19 Nov 2026 | Crafticulum v107: student profiles, All users, sign-up joins existing colleges, self-updating site | 15 |
| `1b76b0d` | 19 Nov 2026 | Crafticulum v106: class lists from CSV, Go live on the board | 8 |
| `3af8087` | 19 Nov 2026 | Crafticulum v105: lesson type on the dashboard, menu order, guide button | 5 |
| `b2d9d76` | 19 Nov 2026 | Crafticulum v104: per-person rooms on Team, front page tidy | 14 |
| `4b7b085` | 19 Nov 2026 | Crafticulum v103: schools list fixes | 10 |
| `44ad989` | 19 Nov 2026 | Crafticulum v102: take Crafti off the sign-in page | 5 |
| `2e33a4f` | 19 Nov 2026 | Crafticulum v101: sign-in page says the name and introduces Crafti | 5 |
| `8564604` | 19 Nov 2026 | Crafticulum v100: the schools list on both sign-up forms | 10 |
| `cd1cd8d` | 19 Nov 2026 | Crafticulum v99: ask to join, profile tables, easier directory imports | 9 |
| `fc29326` | 19 Nov 2026 | Crafticulum v98: the Ofqual register, education profiles, institution directory | 11 |
| `39e7786` | 19 Nov 2026 | Crafticulum v97: every stage and nation, house rules, anonymous learners, one editor | 14 |
| `a490b76` | 18 Nov 2026 | Crafticulum v96: rooms, Crafti, front page, and the three joins | 17 |
| `1ef7782` | 18 Nov 2026 | Crafticulum v95: assistant, resource library, five-area menu, learners, opt-in hand-in links | 13 |
| `2e311e0` | 18 Nov 2026 | Crafticulum v94: AI Tools | 7 |
| `d550b56` | 18 Nov 2026 | Crafticulum v93: notifications, deleted colleges kept for 14 days, front page tidy | 15 |
| `c8cd50c` | 18 Nov 2026 | Crafticulum v92: the AI is called Crafticulum AI everywhere people read | 17 |
| `e73b173` | 18 Nov 2026 | Crafticulum v91: themed emails with a welcome email, manager's overview, marking desk restyled | 10 |
| `a8a8986` | 18 Nov 2026 | Crafticulum v90: super admin stands outside every college, team by college, delete a college for good | 14 |
| `d5abf38` | 18 Nov 2026 | Add a fresh zip of the Crafticulum v89 release | 1 |
| `b11ddd1` | 18 Nov 2026 | Add cPanel deployment file for the Crafticulum release | 1 |
| `679d9dd` | 18 Nov 2026 | Move the Crafticulum v89 release onto main |  |
| `f233aae` | 18 Nov 2026 | Add the full Crafticulum v89 release as files | 49 |
| `797617f` | 18 Nov 2026 | Add Crafticulum v89 update zip | 2 |
| `05fbb97` | 4 Nov 2026 | Initial commit | 1 |
