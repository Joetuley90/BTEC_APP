# Crafticulum releases

Every release: what changed, and exactly which files were pushed. Newest first.
From 1.48 on, versions are written with a point. Release 1.48 is build 148, and so on.

## 1.65 · 28 November 2026

**Next Level Labs credit points to its new address**

- **"Created by Next Level Labs" now links to https://nlb.visualize.agency/** everywhere it appears: the footer of the front page, the sample, contact and legal pages, every subject page (19 pages in all), and the credit under the logo in the app's Settings. Before, it went to visualize.agency.
- The quiz room example address (visualize.agency/quiz/) is a separate thing and hasn't changed.

**After updating:** nothing to change.

**Tested:** no page still links the credit to the old address. pwsite, pwcontact, pwland, pwpolish and pw107 all pass.

### What was pushed

- Commit `e5441a9` on `main` (1 added, 25 changed, 1 removed).
- Zip: `crafticulum-v1.65.zip`, 3,766,181 bytes, no `config.php` inside.
- Build number: 165 (shown to people as 1.65).

| | File |
|---|---|
| changed | `README.md` |
| changed | `crafticulum-release.zip` |
| removed | `crafticulum-v1.64.zip` |
| added | `crafticulum-v1.65.zip` |
| changed | `crafticulum/README.txt` |
| changed | `crafticulum/a-level-computer-science-lesson-plans.html` |
| changed | `crafticulum/accessibility.html` |
| changed | `crafticulum/app.html` |
| changed | `crafticulum/app.js` |
| changed | `crafticulum/btec-business-lesson-plans.html` |
| changed | `crafticulum/btec-esports-lesson-plans.html` |
| changed | `crafticulum/btec-health-and-social-care-lesson-plans.html` |
| changed | `crafticulum/btec-it-lesson-plans.html` |
| changed | `crafticulum/btec-sport-lesson-plans.html` |
| changed | `crafticulum/contact.html` |
| changed | `crafticulum/cookies.html` |
| changed | `crafticulum/dpa.html` |
| changed | `crafticulum/functional-skills-lesson-plans.html` |
| changed | `crafticulum/gcse-computer-science-lesson-plans.html` |
| changed | `crafticulum/index.html` |
| changed | `crafticulum/privacy.html` |
| changed | `crafticulum/sample.html` |
| changed | `crafticulum/security.html` |
| changed | `crafticulum/sw.js` |
| changed | `crafticulum/t-level-digital-lesson-plans.html` |
| changed | `crafticulum/terms.html` |
| changed | `crafticulum/version.json` |

## 1.64 · 27 November 2026

**Signing out is easy to find**

Before, Sign out was tucked away in Settings, under Your account. Now it's in plain sight for everyone: teachers, managers and super admins.

- **On a computer:** there's an account button at the top right showing your picture or initials. Click it for a small menu with your name, email and role (and which institution you're in), **Your account**, and **Sign out**. Escape or clicking anywhere else closes it, and you can move through it with the arrow keys.
- **At the bottom of the sidebar:** a **Sign out** button sits under your name. When the sidebar is collapsed it shows just the icon. It turns red when you hover over it, so it's clear what it does.
- **On a phone and in the home screen app:** **Sign out** is at the bottom of the Menu, a full-width button that's easy to tap. The top bar stays as it was, so it isn't cluttered.
- **Super admins:** it works the same inside an institution and on the whole-site Admin view.
- Signing out takes you back to the front page, and the button says "Signing out..." while it does. Settings, Your account still has its Sign out button too.

**After updating:** nothing to change.

**Tested:** pw164 has 14 checks:
- the account button and its menu (name, email, role, Your account, Sign out)
- Escape and clicking away closing it
- Your account going to Settings
- Sign out in the sidebar
- signing out from the account menu
- a super admin outside any institution signing out
- the phone top bar staying clear
- the phone Menu's Sign out being on screen, tappable and at least 36px tall
- one tap signing out on a phone
- no errors

Each sign-out is confirmed with the server, not just on screen.

The suites around the sidebar, Menu and admin views also pass: pw163, pwsa, pwsa1, pw121, pwmnav, pwphone, pw157, pwsuper, pwsaout, pwtidy, pwpolish, pw107, pwnew and pwsuite.

### What was pushed

- Commit `dc41715` on `main` (1 added, 7 changed, 1 removed).
- Zip: `crafticulum-v1.64.zip`, 3,766,060 bytes, no `config.php` inside.
- Build number: 164 (shown to people as 1.64).

| | File |
|---|---|
| changed | `README.md` |
| changed | `crafticulum-release.zip` |
| removed | `crafticulum-v1.63.zip` |
| added | `crafticulum-v1.64.zip` |
| changed | `crafticulum/README.txt` |
| changed | `crafticulum/app.html` |
| changed | `crafticulum/app.js` |
| changed | `crafticulum/sw.js` |
| changed | `crafticulum/version.json` |

## 1.63 · 27 November 2026

**Leave asks first, and a whole-app check**

- **The super admin's Leave button asks first.** In the purple bar at the top, the first tap turns Leave amber and changes it to "Tap again to leave". A second tap within 4 seconds leaves. Leave it alone, or tap anywhere else, and it goes back to normal. It sits right above the Menu button, so a stray tap no longer takes you out of an institution. Leave in the Menu still goes straight away, because choosing it there is already deliberate.

**Whole-app check.** I tested that the features pass work between each other properly.

A new suite, pw163, has 16 checks:
- A lesson built from the form shows as built in Planning, appears in the Library, is found by search, and opens from search.
- This week opens.
- An extra resource appears in the Library and search, and sits under Extra resources while the unit's lessons stay as they were.
- The quality report builds with both lessons and extras in the unit.
- A binned extra drops off Extra resources.
- Crafticulum AI answers through the new background job.
- Leave asks first, resets by itself, is called off by a tap elsewhere, and leaves on the second tap.
- There were no errors anywhere along the way.

I also went through every screen on desktop, an iPhone 13 and a Galaxy S9+. That covered every page, the Menu, the New sheet, both upload windows, a phone build of an extra resource, the Board and More buttons, the back arrow and Planning. Nothing spilled off the side, nothing was hidden, and there were no errors.

**After updating:** nothing to change.

**Full regression run of 91 suites: all pass.** pwmnav (the roadmap vote) and pwbilling (coming back from Stripe) each failed once, then passed when run again on their own. pwsa and pwsa1 now tap Leave twice.

### What was pushed

- Commit `0da8bab` on `main` (1 added, 7 changed, 1 removed).
- Zip: `crafticulum-v1.63.zip`, 3,764,028 bytes, no `config.php` inside.
- Build number: 163 (shown to people as 1.63).

| | File |
|---|---|
| changed | `README.md` |
| changed | `crafticulum-release.zip` |
| removed | `crafticulum-v1.62.zip` |
| added | `crafticulum-v1.63.zip` |
| changed | `crafticulum/README.txt` |
| changed | `crafticulum/app.html` |
| changed | `crafticulum/app.js` |
| changed | `crafticulum/sw.js` |
| changed | `crafticulum/version.json` |

## 1.62 · 27 November 2026

**Phone polish from a full test sweep**

Joe asked for a bug hunt on phones and the home screen app. Every screen was tested as a home screen app on an iPhone 13, an iPhone SE and a Pixel 7:
- every page
- every Menu item
- the New sheet
- both upload windows
- a full build of an extra resource from Home
- the Board and More buttons on a lesson
- the back arrow
- Planning's Extra resources
- the public pages

Nothing was broken. Two small things were fixed:

- **The New sheet fits on shorter screens.** On an iPhone in Safari (the browser, not the home screen app) the last option and Cancel sat below the bottom of the screen, so you had to scroll the sheet to reach them. On short screens the rows are now tighter and the one-line descriptions are hidden, so every option and Cancel are in view.
- **Bigger "Use as an extra resource" ticks on phones.** The tick boxes in the build form, on Home, in Crafti and in the upload window are now 22px, up from 14px, so they're easier to hit with a thumb.

**Checked and fine:**
- Every Menu item goes to the right page in one tap.
- The upload and bulk windows scroll down to their buttons on the iPhone SE.
- An extra resource built on a phone lands in Extra resources.
- Nothing on any page or public page spills off the side.
- No errors in the console.

**Noted, not changed:** the super admin's Leave button works with one tap and doesn't ask first. Say if you'd like it to ask "Leave this institution?".

**After updating:** nothing to change.

### What was pushed

- Commit `b37e310` on `main` (1 added, 7 changed, 1 removed).
- Zip: `crafticulum-v1.62.zip`, 3,763,308 bytes, no `config.php` inside.
- Build number: 162 (shown to people as 1.62).

| | File |
|---|---|
| changed | `README.md` |
| changed | `crafticulum-release.zip` |
| removed | `crafticulum-v1.61.zip` |
| added | `crafticulum-v1.62.zip` |
| changed | `crafticulum/README.txt` |
| changed | `crafticulum/app.html` |
| changed | `crafticulum/app.js` |
| changed | `crafticulum/sw.js` |
| changed | `crafticulum/version.json` |

## 1.61 · 26 November 2026

**Fix: "Couldn't reach your server" when building**

This fixes the build timing out on Joe's iPhone with "Couldn't reach your server. Check the connection and try again", seen when building a 2-hour practical task as an extra resource.

- **What was wrong.** The page sent one request and the server held it open, saying nothing, until Crafticulum AI had written the whole lesson. A long one, like a 2-hour practical task, can take a couple of minutes. Web hosts and phones drop a request that has been silent that long, so the page gave up even though the server was still working.
- **How it works now.** The page asks for the build, and the server says "started" straight away and carries on in the background. The page then checks back every few seconds until the answer is ready, so no request is held open for more than a moment. Lessons, extra resources, Crafti chat, AI tools and spec reading all work this way.
- **Phone blips don't lose the build.** If the connection drops while it's checking back (going into a lift, Wi-Fi swapping to 4G), it keeps trying for about a minute and a half before giving up.
- **Safe and tidy.** Each build can only be collected by the person who started it, and only once. Anything left behind is cleared after an hour. A page that hasn't updated yet still works the old way.

**After updating:** nothing to change. Then close the home screen app on your phone and open it again, so it picks up the new version.

**Tested:**
- pw161, 10 checks:
  - the start answering straight away while an 8-second AI works
  - checking back while it's working, then getting the answer, once only
  - a made-up job being refused
  - a full lesson built on an iPhone-sized screen with a slow AI and the extra resource tick, finishing and landing in Extra resources, with no request held open
  - three dropped connections in a row while checking back, still getting the answer
  - an old page still getting its answer in one go
- pw158 now has 39 checks, covering extra resources and the team-lesson question on a phone.

### What was pushed

- Commit `8268463` on `main` (1 added, 8 changed, 1 removed).
- Zip: `crafticulum-v1.61.zip`, 3,763,075 bytes, no `config.php` inside.
- Build number: 161 (shown to people as 1.61).

| | File |
|---|---|
| changed | `README.md` |
| changed | `crafticulum-release.zip` |
| removed | `crafticulum-v1.60.zip` |
| added | `crafticulum-v1.61.zip` |
| changed | `crafticulum/README.txt` |
| changed | `crafticulum/ai.php` |
| changed | `crafticulum/app.html` |
| changed | `crafticulum/app.js` |
| changed | `crafticulum/sw.js` |
| changed | `crafticulum/version.json` |

## 1.60 · 26 November 2026

**Fix: building on a phone got stuck**

Fixes from Joe's iPhone screenshots. Building a lesson could hang on "Building..." with the page greyed out, and extra resources never arrived.

- **The "Your team already has lessons for this unit" question now pops up properly.** On phones it was drawn at the bottom of the page instead of over it. The grey backdrop covered the screen, but the question itself sat out of sight, so the build waited for an answer you couldn't give. Now it opens in the middle of the screen like the other windows, and the backdrop goes when you answer or close it.
- **Extra resources skip that question.** An extra isn't another copy of a lesson, so when "Use as an extra resource" is ticked Crafticulum just builds it and files it under Extra resources.
- **The tick holds on.** If Home or Crafti take a while to get going (for example, while working out the unit), the tick is still remembered. Before, a slow start could lose it, and the lesson ended up as a normal lesson.

**After updating:** nothing to change. If a build is stuck on your phone now, close the home screen app and open it again after updating.

**Tested:** pw158 now has 39 checks. The new ones cover:
- the team question on a phone, with the page scrolled down: it's on screen, over the whole page, and Build a new one closes it
- an extra with the team question switched on: it's skipped, and the extra still lands after a slow start
- a normal lesson still asking the question

pw155, pwbase and the rest of the nearby suites still pass.

### What was pushed

- Commit `2abded7` on `main` (1 added, 7 changed, 1 removed).
- Zip: `crafticulum-v1.60.zip`, 3,760,990 bytes, no `config.php` inside.
- Build number: 160 (shown to people as 1.60).

| | File |
|---|---|
| changed | `README.md` |
| changed | `crafticulum-release.zip` |
| removed | `crafticulum-v1.59.zip` |
| added | `crafticulum-v1.60.zip` |
| changed | `crafticulum/README.txt` |
| changed | `crafticulum/app.html` |
| changed | `crafticulum/app.js` |
| changed | `crafticulum/sw.js` |
| changed | `crafticulum/version.json` |

## 1.59 · 26 November 2026

**Upload your own extra resource**

Lecturers can now upload a resource they've already made, like a practical task, case study or revision pack, and have it filed under a unit's Extra resources.

- **Upload your own on Extra resources.** In Planning, the Extra resources heading has two buttons: **Build an extra resource** (with Crafticulum AI) and **Upload your own**. Upload your own opens the usual upload window as "Upload your own extra resource". The tick is already on, and you pick the kind.
- **The same tick in the upload window.** The normal Upload your own lesson window now has **Use as an extra resource** under the unit, so an upload from Home or the New button can go in as an extra too.
- **Checked like any upload.** Your wording is kept, it's checked against the criteria, and anything missing is filled in. Crafticulum AI is told the file is an extra resource of that kind, not a taught lesson. The original file is kept, and you get the usual box showing what was kept, added and changed, plus the switch to Your original.
- **Filed under Extra resources.** It shows in the unit's list with its kind and a **Your own** badge. It never takes or replaces a lesson. The lesson shows the Extra resource label.

**After updating:** nothing to change.

**Tested:** pw158 now has 35 checks. The new ones cover:
- Upload your own opening the window as an extra, with the tick on and the kind picker showing
- Crafticulum AI being told it's an extra and what kind
- the upload being filed under Extra resources and marked Your own
- the check box and label on the lesson
- the Your own badge in Planning
- the normal upload window's tick, off to start, with the kind appearing when ticked

pw154, pw155, pw156, pw157, pwbase, pwplain, pwphone, pwchat and pwpolish all still pass.

### What was pushed

- Commit `116cdd4` on `main` (1 added, 7 changed, 1 removed).
- Zip: `crafticulum-v1.59.zip`, 3,760,634 bytes, no `config.php` inside.
- Build number: 159 (shown to people as 1.59).

| | File |
|---|---|
| changed | `README.md` |
| changed | `crafticulum-release.zip` |
| removed | `crafticulum-v1.58.zip` |
| added | `crafticulum-v1.59.zip` |
| changed | `crafticulum/README.txt` |
| changed | `crafticulum/app.html` |
| changed | `crafticulum/app.js` |
| changed | `crafticulum/sw.js` |
| changed | `crafticulum/version.json` |

## 1.58 · 26 November 2026

**Extra resources for each unit**

Lecturers can now build extra resources for a unit, like practical tasks, assessment practice and revision. These go under their own heading in Planning and never take or replace a lesson.

- **The tick.** Under Type, the build form now has a **Use as an extra resource** box. When it's ticked, you pick what kind of resource you want:
  - Practical task
  - Assessment practice
  - Revision
  - Homework
  - Worksheet
  - Case study
  - Stretch and challenge
  - Catch-up
  - Other

  Crafticulum AI is told it's an extra resource of that kind, rather than a taught lesson. Assessment practice is always formative practice. It's never the real assignment, and never something learners could copy into their evidence.
- **Without the tick, it's a lesson in Planning.** A box called **Put it in Planning as** lists the unit's lessons:
  - It picks the first empty lesson for you.
  - You can pick a lesson that's already built instead. It says "replaces the one there", and the old lesson stays in My created lessons. It just leaves Planning.
  - You can also pick "Don't add it to Planning", which is how it worked before.
  - If every lesson is built, it says so and suggests ticking the box instead.
- **Extra resources in Planning.** Each unit has an **Extra resources** heading below the weeks. Every resource shows its name and kind, with Open, PowerPoint, Worksheet and Remove. Remove asks first. It only takes the resource off the unit, so the lesson stays in My created lessons. **Add an extra resource** opens the build form with the tick already on.
- **Home and Crafti too.** The tick and kind picker also sit under the message box on Home, and next to Build the full lesson in a Crafti chat.
- **On the lesson.** An extra resource shows an "Extra resource · kind · unit" label, with a link back to the unit's extras. If you regenerate it, it stays an extra.
- **Kept safe:**
  - When Crafticulum AI rewrites a unit's sequence, the extras stay.
  - Unit for a colleague now includes the extras, and they come in on the other side.
  - If an extra has been binned, opening it takes it off the list.

**After updating:** nothing to change.

**Tested:** pw158 has 28 checks:
- the tick, and the Planning picker with the first empty lesson picked
- a normal build filling that lesson, and the picker moving on
- replacing a built lesson while the old one is kept
- the tick swapping in the kind picker
- the AI being told it's an extra and what kind
- nothing in the sequence being taken or replaced
- the label on the lesson and its link to Planning
- the Extra resources list and where it sits
- Open, Remove with its check, and the lesson being kept after removing
- the empty message, and Add an extra resource turning the tick on
- regenerating, a rewritten sequence, and Unit for a colleague all keeping extras
- the Home tick building an extra, and the Crafti tick
- a phone fitting the list, and a binned extra being taken off

### What was pushed

- Commit `93cb654` on `main` (1 added, 7 changed, 1 removed).
- Zip: `crafticulum-v1.58.zip`, 3,759,497 bytes, no `config.php` inside.
- Build number: 158 (shown to people as 1.58).

| | File |
|---|---|
| changed | `README.md` |
| changed | `crafticulum-release.zip` |
| removed | `crafticulum-v1.57.zip` |
| added | `crafticulum-v1.58.zip` |
| changed | `crafticulum/README.txt` |
| changed | `crafticulum/app.html` |
| changed | `crafticulum/app.js` |
| changed | `crafticulum/sw.js` |
| changed | `crafticulum/version.json` |

## 1.57 · 25 November 2026

**Phone fixes and a 7-day free trial**

Fixes from Joe's iPhone screenshots, plus the full-access trial he asked for.

- **The Menu on phones.** Every tap on a menu item now goes where it should, including in the home screen app. If iPhone holds back the tap, Crafticulum makes it go through anyway, and it's still one tap, one action. The menu now sits above the Crafti button and everything else, and a long press no longer selects the text. Opening it always shows the whole bar, even if you'd scrolled.
- **Switching department keeps you where you were.** Before, picking another department took you back to Home. Now:
  - Planning, This week, Second Marker, Learners, Results, AI tools, Library and Settings come back as they were.
  - On Lessons, you get a new lesson in that department, because a lesson belongs to the department it was made in.
- **Second Marker:**
  - A **Department** picker sits in its own bar, next to Group.
  - **Move through the steps freely.** Every step can be tapped at any time, and Continue is never locked. Before, it stopped you until each step was finished. Now it lets you through and shows what's still to do on an earlier step, for example "Still to do earlier, Submission: Upload the learner's work".
  - Swipe left or right on a phone to go to the next or previous step.
- **7-day free trial.** Anyone who signs up on their own gets full access, everything in the **Department** plan, for 7 days:
  - A countdown bar shows in the app for the people who can pay. It turns amber 2 days before the end and purple on the last day, with a "Choose a plan" button.
  - Plan and billing says it's a trial and when it ends, and lets them choose a plan.
  - The institution's managers get an email **48 hours** and **24 hours** before it ends. Each has a link that opens the payment straight away, plus a link to compare plans. A third email is sent when it has ended.
  - When it ends, they're on Free with everything they made still there, and the app says so once.
  - Super admin: Settings, Plans and payments has the trial length (0 switches it off), which plan it gives (Teacher or Department), and how many are on a trial now. The business page shows "On trial".
  - It only applies to new sign-ups. Existing institutions, MidKent included, aren't touched.

**After updating:** nothing to change. Reminder emails go out when the hourly check runs: the same one that sends the other reminders, through your cron or UptimeRobot hitting the cron link. If nothing pings it, they go out the next time someone uses the site.

**Tested:** pw157 (14: the menu taps, one click per tap, z-index and text selection, the whole bar on opening; staying on Planning, Lessons and Second Marker after switching; Second Marker's department picker; free steps, the "still to do" note, Continue unlocked, swiping). pwtrial (11: default settings, changing them without touching prices, a sign-up getting 7 days of Department, the countdown, Plan and billing, the 48 and 24 hour bars and emails sent once each, the email link opening payment, ending with Free limits plus email plus a one-off note, the trial count, and 0 switching it off). pwpay now turns the trial off, because its checks are about Free.

**Full regression run of 89 suites: all pass.** I updated four tests for this release: pwbilling and pwpay turn the trial off for their Free checks, pwpolish looks for the new What's new item, and pwsa's step inside an institution still lands on its start screen. Keeping your place only applies when switching departments within the same institution. Not tried on a real iPhone yet.

### What was pushed

- Commit `399f7f6` on `main` (1 added, 12 changed, 1 removed).
- Zip: `crafticulum-v1.57.zip`, 3,754,389 bytes, no `config.php` inside.
- Build number: 157 (shown to people as 1.57).

| | File |
|---|---|
| changed | `README.md` |
| changed | `crafticulum-release.zip` |
| removed | `crafticulum-v1.56.zip` |
| added | `crafticulum-v1.57.zip` |
| changed | `crafticulum/README.txt` |
| changed | `crafticulum/app.html` |
| changed | `crafticulum/app.js` |
| changed | `crafticulum/auth-lib.php` |
| changed | `crafticulum/auth.php` |
| changed | `crafticulum/billing.php` |
| changed | `crafticulum/marker/sm.js` |
| changed | `crafticulum/money.php` |
| changed | `crafticulum/sw.js` |
| changed | `crafticulum/version.json` |

## 1.56 · 25 November 2026

**Bring all your lessons in**

Lecturers can bring their existing lessons in, either when they first join or at any time after. Each lesson keeps the original file, with a switch to flip between versions.

- **Bring your lessons in.** Upload a batch of lessons, or a zip of them, up to 40 at a time: Word, PowerPoint, PDF or text. Each file can be set to its own unit. You can open it from:
  - "Bring your lessons" in a manager's first steps on Home
  - a "Already got lessons?" card on Home for anyone new (fewer than 5 lessons)
  - New on a phone
  - the top of My created lessons
  - a link in the single upload window
- **Checked as a set first.** Crafticulum AI reads the whole batch and shows a table for each unit: which of your lessons covers each criterion, and which criteria nothing covers yet. It suggests lessons to fill those holes.
- **Then two choices:** "Bring all of them in", where each one is checked and gaps filled with the wording kept, like a single upload in 1.55. And "Build the missing lessons in your style", where new lessons are built for the holes, told how this teacher writes (kinds of activity, tone, pace). It works through them one by one with a progress list, and each has an Open link.
- **Your original.** Every lesson brought in keeps its original file in the department's data folder (`_orig`), never at a web address. A switch on the lesson flips between **Crafticulum version** and **Your original**. Your original shows exactly what was uploaded, with slides and speaker notes laid out, plus:
  - **Download the original**, which gives back the same file
  - **Keep only what's yours**, which takes out every optional part Crafticulum added
  - **Back to the Crafticulum version**

  Single uploads from 1.55 keep their original from now on too.
- **Google Drive and OneDrive.** Paste a share link instead of downloading the file first, in both the single and bulk windows. Google Docs and Slides come in as Word and PowerPoint. The file must be set to "Anyone with the link". Only Google and Microsoft addresses are ever fetched, and anything else is refused. If a file isn't shared, the lecturer is told how to share it.
- **Quality report.** For each unit, it shows how many lessons are staff-written, how many criteria gaps were filled, and how many were built by Crafticulum AI. Both the on-screen report and the Word download show this.
- **Neutral-name sweep.** Three assignment briefs had real staff names as the assessor; they now say "Your course team". The Second Marker example name, a "college in Kent" placeholder, "MSA Open Event" in the calendar and "What Joe wants done" in the fix pack are gone too. The build now stops if any of these come back.
- **Old tests tidied.** Every test suite now passes. Where the app had moved on (Settings groups, the menu, the new-user welcome, extra public pages, the share link format, the chat button being off the lesson screen on phones), the checks were updated to match. Tests for things that were deliberately removed (the old password sign-in, "Institutions set up twice") were taken out. pwurl now runs its own second address, because 8098 is a brand new site.

**After updating:** nothing to do. Drive and OneDrive links need your host to allow outgoing connections (Krystal does).

**Note on GitHub:** the repository is public and its history still has those staff names from earlier releases. They're gone from this release onwards. Clearing them from history means rewriting it, which I won't do unless you say so.

**Tested:**
- pw156, 25 checks:
  - first steps and the ways in
  - zip unpacking, and unreadable files left out
  - Drive links: shared, private and other hosts
  - the set check and its holes
  - bringing them all in and building the holes
  - originals kept and downloaded byte for byte
  - the switch and Keep only what's yours
  - single uploads keeping their original
  - server refusals for bad keys, file types and hosts
  - the quality report count
  - phone
- pw155 still passes.
- The accessibility check is clean on the bulk window (pick, review, progress) and the original view, in light and dark.
- Full regression run of 87 suites: every one passes. pwtidy was updated for the new first step, and pwmnav failed once on the roadmap vote, then passed twice when rerun on its own. Not tried on the live site yet.

### What was pushed

- Commit `4cc7592` on `main` (1 added, 9 changed, 1 removed).
- Zip: `crafticulum-v1.56.zip`, 3,748,163 bytes, no `config.php` inside.
- Build number: 156 (shown to people as 1.56).

| | File |
|---|---|
| changed | `README.md` |
| changed | `crafticulum-release.zip` |
| removed | `crafticulum-v1.55.zip` |
| added | `crafticulum-v1.56.zip` |
| changed | `crafticulum/README.txt` |
| changed | `crafticulum/app.html` |
| changed | `crafticulum/app.js` |
| changed | `crafticulum/marker/sm.js` |
| changed | `crafticulum/store.php` |
| changed | `crafticulum/sw.js` |
| changed | `crafticulum/version.json` |

## 1.55 · 25 November 2026

**Bring your own lesson**

Lecturers can now start from a lesson they've already made, rather than starting from scratch.

- **Upload your own lesson.** It's on Home, under Build lesson on the criteria form, and in the New sheet on a phone. It takes Word (.docx), PowerPoint (.pptx, including speaker notes), PDF, or pasted text. Pick the subject, level and unit, and optionally the lesson length, a note ("keep my case study") and the criteria it should cover. If no criteria are picked, Crafticulum works out which ones the lesson is aiming at.
- **What Crafticulum AI does with it.** It keeps their title, activities, order, timings, examples and wording. It checks the lesson against the criteria. Where something a criterion needs is missing or thin, it adds an activity, task or question. Any part the lesson doesn't have (starter, tasks, exit quiz, independent study, plenary and so on) is written to match their style. Timings that don't add up to the lesson length get adjusted.
- **"Your lesson, checked" box.** This sits at the top of the lesson and shows:
  - the criteria their lesson already covered
  - each gap, with what was missing and what was added
  - what was kept from theirs
  - what changed, and why

  Parts Crafticulum wrote are tagged **Added** on the lesson tabs, with a note at the top of that part. The optional ones (exit quiz, independent study, stretch, support, how to work, done-by list) can be **taken out** and **put back**. "See what you uploaded" shows the text that was read from their file.
- **Upload your version.** On any lesson, More has "Upload your version". A lecturer can edit a Crafticulum lesson in Word or PowerPoint, then upload it. Their changes win, it's checked against the lesson's criteria again, and gaps are filled. It stays the same lesson with the version number going up, and the previous version is in History. Sign-off is cleared, because the content changed.
- **Learner names.** Before anything is sent, any learner names saved in Crafticulum (groups and learner lists) are swapped for "[learner]". The box says how many were taken out, and the window tells lecturers to remove any others first.

**After updating:** nothing to do.

**Tested:** pw155 27 (new: the ways in on Home, the form, the phone New sheet and More; empty and wrong-file messages; Word and PowerPoint reading, with slides in order and speaker notes; learner names taken out; all criteria sent when none are picked; the teacher's note; title kept; criteria narrowed; the box's covered, gaps, kept and added; tab tags and the note on an added part; saved to your lessons; take out and put back; see what you uploaded; upload your version keeping the same lesson as version 2 with the old one in History; phone layout). The accessibility check is clean on the window and the box, in light and dark. In the full regression run of 83 suites, every failure is one of the old out-of-date checks already listed in 1.54, which fail the same way without this release. pw107 was updated for the new date. Not tried on the live site yet.

### What was pushed

- Commit `999c830` on `main` (1 added, 7 changed, 1 removed).
- Zip: `crafticulum-v1.55.zip`, 3,736,704 bytes, no `config.php` inside.
- Build number: 155 (shown to people as 1.55).

| | File |
|---|---|
| changed | `README.md` |
| changed | `crafticulum-release.zip` |
| removed | `crafticulum-v1.54.zip` |
| added | `crafticulum-v1.55.zip` |
| changed | `crafticulum/README.txt` |
| changed | `crafticulum/app.html` |
| changed | `crafticulum/app.js` |
| changed | `crafticulum/sw.js` |
| changed | `crafticulum/version.json` |

## 1.54 · 25 November 2026

**Behaves like an app on phones and tablets**

Everything here is for phones and tablets (narrower than 992px). Computers keep the layout they had.

- **Title bar.** Every screen shows its name at the top with a back arrow. Home keeps the logo. Scroll down and the bar slides out of the way, scroll up and it comes back. The super admin band is now one slim line and stays on screen, so Leave is always there.
- **Five tabs.** The bottom bar is Home, This week, **New**, Lessons and Planning. New is the round button in the middle and opens a sheet: New lesson, AI tool, Plan a unit, Ask Crafti, or My created lessons. Your account is in Menu.
- **Screens slide.** A new screen slides in from the right, and going back slides it out. Switching tabs fades. Swipe in from the left edge to go back. The phone's own back button still works.
- **Lesson screen.** One row: a big Teach button, Board, and "⋯" for everything else (Edit, the Share and More options, Make a version, Offline copy). Sign-off is a small pill you tap to open. Show on board no longer floats over the lesson.
- **Crafti.** The mascot is a bit smaller, and it tucks away while you scroll or when a sheet or menu is open.
- **Settings and Admin as lists.** They open as a tidy list like phone settings, with your account at the top. Tap a row to open that page, and use the back arrow to return. Links that go straight to a setting still open it directly.
- **Loading and refresh.** Lists that are loading show grey placeholder shapes instead of "Loading...". Pull down at the top of Home, This week, Lessons, Planning, Settings, Library, Learners or Results to refresh.
- **Taps.** Buttons press in slightly when tapped, Android phones give a tiny buzz on key actions, and buttons are at least 44px tall. In My created lessons, swipe a lesson left to **Copy** or **Bin** it.
- **Home screen.** From a teacher's second visit, a card offers "Add Crafticulum to your home screen". On Android and Chrome it installs in one tap. On iPhone it shows how to use Share, then Add to Home Screen. Not now hides it for 30 days. Opened from the icon, it shows the Crafticulum name under the logo while it loads.
- **One sheet style.** Every menu on a phone now opens the same bottom sheet, with a grab handle, a Cancel button, and drag down to close. That includes Teach, Share, More, Download and the rest, the lesson parts, Settings sections and the board. My created lessons opens from the bottom too.

**After updating:** nothing to do.

**Tested:** pw154 38 (new: five tabs and New, title bar and back, the title bar hiding on scroll with the admin band staying, edge swipe back, the lesson row and its "⋯" sheet, Make a version from the sheet, Teach as a sheet, drag to close, the sign-off pill, Settings and Admin lists, direct links, loading shapes, swipe to copy, pull to refresh, install prompt, 44px buttons, tablet sheet, computers unchanged). Full regression run of 82 suites: the only failures are 10 old checks that fail the same way on 1.53 without these changes (pw118, pwchat, pwsuite, pwsuper, pwurl, pwurls, pw111, pwsa, pwmgr, pwpay), so they're out-of-date tests, not this release. Updated for the new layout: pw118, pw107, pwmnav, pwpolish, pwsa1, pwtidy. The accessibility check is clean on phone screens, light and dark, including the new sheets, Settings list and install card. Not tried on the live site yet.

### What was pushed

- Commit `09b806b` on `main` (1 added, 7 changed, 1 removed).
- Zip: `crafticulum-v1.54.zip`, 3,726,106 bytes, no `config.php` inside.
- Build number: 154 (shown to people as 1.54).

| | File |
|---|---|
| changed | `README.md` |
| changed | `crafticulum-release.zip` |
| removed | `crafticulum-v1.53.zip` |
| added | `crafticulum-v1.54.zip` |
| changed | `crafticulum/README.txt` |
| changed | `crafticulum/app.html` |
| changed | `crafticulum/app.js` |
| changed | `crafticulum/sw.js` |
| changed | `crafticulum/version.json` |

## 1.53 · 21 November 2026

**Faster, works offline, lesson sign-off, quality report, learners' progress, checked releases**

- **Faster.** The app's code is now its own file (`app.js`). The page is a fifth of its old size, and browsers keep the code for a year, so after the first visit the app opens much faster. The code gets a new address whenever it changes, so nobody runs an old copy.
- **Offline.** With no internet you can open and teach your saved lessons. Anything you change is kept on the device: it shows straight away, survives a reload, and saves to the server by itself when the internet is back. A note at the bottom says how many changes are waiting. Building with Crafticulum AI still needs the internet.
- **Sign-off.** Under every lesson there are two buttons:
  - **Sign it off.** A teacher can sign their own lesson off, with no approval needed.
  - **Ask for a review.** A head of department sees the lessons waiting on their start page and can Approve them or Ask for changes with a note. The lesson stays the teacher's.
  The department setting that holds teachers' changes until a leader accepts them is unchanged.
- **Quality report.** Planning has "Quality report for (course and level)". It shows every unit against its criteria: lessons built, weeks planned, weeks taught, lessons signed off, exit quiz results and learners achieved. Criteria that aren't covered are flagged, grouped by unit. Print it, save it as a PDF, or download it as Word. Learners aren't named.
- **Learners' progress.** "Progress across units" shows each learner's share of criteria achieved in every unit marked for the group, what they've achieved in the last two weeks, and who is well behind the group. Marks now record when they changed, so the two-week figure fills in from today.
- **Checked releases.** GitHub checks every release before the live site is told to fetch it:
  - no settings file
  - the zip matches the folder
  - every PHP and JavaScript file is valid
  - nothing a page needs is missing
  - no real college is named
  - the site starts and answers its health check
  If any check fails, the deploy doesn't run.

**After updating:** nothing to do. The first open after updating downloads the new code once.

**Tested:** pw153 6 (new: separate code file, offline change kept and saved later, sign-off, review and approval, quality report and Word download, progress across units). Also pw150, pwmoney, pwforms, pwtidy, pw107, pwpolish, pwfix, pwfilm, pwnext, pwsite, pwcontact, pwplain, pwreport, pwteach, pwmore, pwphone, pwroles, pwvideo, pwvoice, pwvoice2, pwg, pw50, pw52, pw53, pw144, pwtabs, pwinteg, pwmarker and pwbrand, and the new GitHub check run locally. None of it has been tried on the live site yet.

### What was pushed

- Commit `fff0f38` on `main` (3 added, 8 changed, 1 removed).
- Zip: `crafticulum-v1.53.zip`, 3,713,234 bytes, no `config.php` inside.
- Build number: 153 (shown to people as 1.53).

| | File |
|---|---|
| added | `.github/check-release.sh` |
| changed | `.github/workflows/deploy.yml` |
| changed | `README.md` |
| changed | `crafticulum-release.zip` |
| removed | `crafticulum-v1.52.zip` |
| added | `crafticulum-v1.53.zip` |
| changed | `crafticulum/.htaccess` |
| changed | `crafticulum/README.txt` |
| changed | `crafticulum/app.html` |
| added | `crafticulum/app.js` |
| changed | `crafticulum/sw.js` |
| changed | `crafticulum/version.json` |

## 1.52 · 21 November 2026

**The same footer inside the app**

- The links at the bottom of Settings and Admin now match the front page's footer: About Crafticulum, Terms, Privacy, Cookies, Security, Data processing, Accessibility, and Contact and support.
- Underneath is "Created by" with the Next Level Labs logo, linking to visualize.agency, as on the public pages. The white logo shows in dark mode.

**After updating:** nothing to do.

**Tested:** pwtidy, pw107, pwphone, pwsite, pwg, and checked on a phone in light and dark.

### What was pushed

- Commit `c27b403` on `main` (1 added, 6 changed, 1 removed).
- Zip: `crafticulum-v1.52.zip`, 3,703,026 bytes, no `config.php` inside.
- Build number: 152 (shown to people as 1.52).

| | File |
|---|---|
| changed | `README.md` |
| changed | `crafticulum-release.zip` |
| removed | `crafticulum-v1.51.zip` |
| added | `crafticulum-v1.52.zip` |
| changed | `crafticulum/README.txt` |
| changed | `crafticulum/app.html` |
| changed | `crafticulum/sw.js` |
| changed | `crafticulum/version.json` |

## 1.51 · 21 November 2026

**Fix: the invoice PDF file was missing from 1.50**

- 1.50 left out `invoice-lib.php`, the file that makes invoice PDFs. Without it, Admin, Money (the Business dashboard and invoices) wouldn't load, and the PDFs and reminders couldn't be made.
- It's included now. The release script also checks, before anything is pushed, that every file a page needs is in the zip and that nothing in the build is left out.

**After updating:** nothing to do. If you updated to 1.50, update again straight away.

**Tested:** pw150 9, pwmoney 14, and the new completeness check (every required file present, nothing left out).

### What was pushed

- Commit `088a85c` on `main` (2 added, 5 changed, 1 removed).
- Zip: `crafticulum-v1.51.zip`, 3,702,446 bytes, no `config.php` inside.
- Build number: 151 (shown to people as 1.51).

| | File |
|---|---|
| changed | `README.md` |
| changed | `crafticulum-release.zip` |
| removed | `crafticulum-v1.50.zip` |
| added | `crafticulum-v1.51.zip` |
| changed | `crafticulum/README.txt` |
| added | `crafticulum/invoice-lib.php` |
| changed | `crafticulum/sw.js` |
| changed | `crafticulum/version.json` |

## 1.50 · 21 November 2026

**Invoice PDFs and reminders, the funnel, tips, your data, alerts, accessibility, subject pages, Next Level Labs credit**

- **Invoice PDFs.** When an invoice is sent, the email has the invoice attached as a PDF, made on the server with nothing to install. The invoice page has Download the PDF, and the invoice list in Admin has a PDF button.
- **Overdue reminders.** A polite reminder with the PDF goes 7 days after the pay-by date, and a second at 14 days. Each goes once. You get a notification, and the invoice list shows when they were reminded.
- **The funnel.** The Business dashboard has a "From looking to paying, last 30 days" card: visitors to the public pages, accounts started, those that built a lesson, and those paying, with the rate between each step. Visitors are counted once a day each, with no cookies. Only the daily total is kept, and robots aren't counted.
- **Tips for new accounts.** For people who start on their own, Free plan only. Day 1 if they haven't built a lesson, day 7 about Planning, and once a month at 80% of their free builds. Each tip has a one-click link to stop them, and they stop when the institution pays.
- **Your institution's data.** In Settings, Data and backups, a manager can download everything as one zip: people, every department's work, what each person keeps, and invoices. They can also ask for it all to be deleted. A request needs the institution's name typed, emails you, and shows in Admin, To do with the days left of the month you have to do it.
- **Downtime alerts.** You're emailed and notified when the AI, the voice, email, payments or the server fail three times in fifteen minutes (at most every six hours for each). Admin, System has an Uptime monitor section with `auth.php?a=health` to give a free service like UptimeRobot. The scheduled job (`auth.php?a=pushcron`) now runs the hourly jobs too.
- **Accessibility.** Checked against WCAG 2.2 AA on the public pages and the main parts of the app, in light and dark mode and at phone width. The following are fixed:
  - green and orange buttons
  - criteria codes
  - filled buttons and badges in dark mode
  - the sample and subject pages in dark mode
  - small tap targets
  - the heading order in Planning
  A new Accessibility statement page is linked from every footer.
- **Subject pages.** Four new ones: BTEC Business, BTEC Health and Social Care, BTEC Sport and A Level Computer Science. They're in the sitemap and the footers. A stray closing tag on the older subject pages is fixed too.
- **Settings keeps open boxes open.** When part of Settings finished loading, the whole page redrew and closed any box you'd opened. It doesn't now, and Messages no longer loads several times over.
- **Next Level Labs credit.** Every public footer says "Created by" with the one-line Next Level Labs logo (white in dark mode), linking to visualize.agency.

**After updating:** in Admin, System, Uptime monitor, copy the address into a free uptime service if you'd like texts when the site is down. If you use a scheduled job, keep it running: it now sends the reminders and tips too.

**Tested:** pwg 7 three runs in a row, pw150 9 (new: PDF attached and readable, PDF address, both reminders, funnel counting, tips and the stop link, data download, deletion request, health and alerts, uptime section). Also pwmoney, pwforms, pwtidy, pw107, pwpolish, pwfix, pwfilm, pwnext, pwsite, pwcontact, pwplain, pwreport, pwteach, and an accessibility check with no issues left on 15 screens in light and in dark at phone width. The live site hasn't been tried yet. The accessibility check is our own, not an independent audit.

### What was pushed

- Commit `22cd162` on `main` (8 added, 26 changed, 1 removed).
- Zip: `crafticulum-v1.50.zip`, 3,697,628 bytes, no `config.php` inside.
- Build number: 150 (shown to people as 1.50).

| | File |
|---|---|
| changed | `README.md` |
| changed | `crafticulum-release.zip` |
| removed | `crafticulum-v1.49.zip` |
| added | `crafticulum-v1.50.zip` |
| changed | `crafticulum/.htaccess` |
| changed | `crafticulum/README.txt` |
| added | `crafticulum/a-level-computer-science-lesson-plans.html` |
| added | `crafticulum/accessibility.html` |
| changed | `crafticulum/app.html` |
| added | `crafticulum/assets/nll-logo-white.svg` |
| added | `crafticulum/assets/nll-logo.svg` |
| changed | `crafticulum/auth-lib.php` |
| changed | `crafticulum/auth.php` |
| added | `crafticulum/btec-business-lesson-plans.html` |
| changed | `crafticulum/btec-esports-lesson-plans.html` |
| added | `crafticulum/btec-health-and-social-care-lesson-plans.html` |
| changed | `crafticulum/btec-it-lesson-plans.html` |
| added | `crafticulum/btec-sport-lesson-plans.html` |
| changed | `crafticulum/contact.html` |
| changed | `crafticulum/cookies.html` |
| changed | `crafticulum/dpa.html` |
| changed | `crafticulum/functional-skills-lesson-plans.html` |
| changed | `crafticulum/gcse-computer-science-lesson-plans.html` |
| changed | `crafticulum/index.html` |
| changed | `crafticulum/money.php` |
| changed | `crafticulum/page.php` |
| changed | `crafticulum/pages.css` |
| changed | `crafticulum/pages.js` |
| changed | `crafticulum/privacy.html` |
| changed | `crafticulum/sample.html` |
| changed | `crafticulum/security.html` |
| changed | `crafticulum/sw.js` |
| changed | `crafticulum/t-level-digital-lesson-plans.html` |
| changed | `crafticulum/terms.html` |
| changed | `crafticulum/version.json` |

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
