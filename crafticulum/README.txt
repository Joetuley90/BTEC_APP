COURSE CREATOR - SELF-HOSTED VERSION

UPDATING
  Updates don't include config.php, so upload everything in the zip and your API
  key and password stay as they are. If an update ever needs a new setting, it
  will say so clearly and list exactly what to add.

WHAT'S IN THE ZIP
  index.html     The front page: what Crafticulum is, who it's for, the prices
  app.html       The app itself (at /app)
  billing.php    Plans and payments: talks to Stripe (see PLANS AND PAYMENTS)
  ai.php         Sends lesson requests to Claude using your API key (kept on the server)
  store.php      Saves your lessons and sequences on the server (in a data folder)
  share.php      Student links: lesson pages, unit pages and assessments
  backup.php     Daily backups of the data folder, and clearing old student answers
  auth.php       Google sign-in, staff accounts and departments
  auth-lib.php   Works out who is signed in and what they can do (used by the others)
  assets/        The PowerPoint and Word templates, loaded only when you download
  student.html   The page students see when they open a link
  about.html (now just forwards to the front page), terms.html, privacy.html,
  cookies.html, pages.css, pages.js
                 The public pages (see THE PUBLIC PAGES)
  setup.php      First-time setup on a new site. Does nothing once config.php exists
  config.example.php  Settings template. Only used on a first install (see below)
  .htaccess      Stops anyone opening config.php from the web
  manifest.webmanifest, sw.js and the icon files
                 Let you install Crafticulum as an app (see INSTALL AS AN APP)
  marker/        Second Marker's code and its server file (see SECOND MARKER)
  quiz/          The quiz room: phone quiz with initials and an end-of-quiz report
  README.txt     This file

SET UP (cPanel, about 10 minutes)
1. Make sure the site runs PHP 8.0 or newer with the curl and zip extensions
   (cPanel > Select PHP Version / MultiPHP Manager). Most hosts have them on already.
   zip is only needed for the automatic backups.
2. File Manager > public_html: create a folder, e.g. "lessons", and upload
   everything from this zip into it, keeping the quiz folder as it is.
   (Turn on "Show hidden files" so you can see .htaccess after uploading.)
3. FIRST INSTALL ONLY: in File Manager, copy config.example.php and name the
   copy config.php. Then edit config.php:
     ANTHROPIC_API_KEY  your key from console.anthropic.com
     TEACHER_PASSWORD   a strong password you'll type to write lessons
     CLAUDE_MODEL       leave as it is unless you want a different model
4. Visit https://yourdomain/lessons/
   The first time you build a lesson it asks for the teacher password.

WHAT WORKS DIFFERENTLY FROM THE CLAUDE VERSION
- Writing lessons, starters, quizzes, task sheets, brief support versions and the
  Lesson plan form all go through ai.php. Each request uses your Anthropic API
  credit. MAX_REQUESTS_PER_HOUR in config.php caps it per teacher.
- Downloads (PowerPoint, Word, images) save straight to the computer.
- The lesson library is saved on your server, so it's the same on every site once
  you sign in.
- The phone quiz uses the quiz room in ./quiz/ automatically: students scan, type
  initials and answer, and you get a report when you end it. Ending deletes the
  answers from the server. Anything not ended is deleted after 6 hours.
- The board, timers, tools, display options and overlays work exactly the same.

PLANNING (SEQUENCE OF LEARNING) AND BUILDING LOTS OF LESSONS
- Every unit has a week-by-week sequence ready to go. You can build one lesson,
  a whole unit, or every unit in a level. It builds them one after another.
- Each lesson is one request to Claude. MAX_REQUESTS_PER_HOUR in config.php
  (60 to start with) limits how fast a big build can go. Raise it if you want
  whole units built quicker. When the limit is hit the build waits and carries on.
- Lessons, sequences, groups and term dates are saved on your server in a
  folder called data, next to store.php. The page asks for the teacher password
  when it opens, then loads them. They follow you between computers.
- If saving fails, create the data folder yourself in File Manager and set its
  permissions to 755. Back that folder up now and then: it is your library.

STUDENT LINKS
- On a lesson, Share gives you a Student page link (tasks and independent study,
  no teacher notes or answers) and a Quiz link for the exit quiz.
- In Planning, Share with students gives you a Unit page (every built lesson by
  week) and an End of unit assessment.
- Students open the links without a password. Send them by email or Discord, or
  show the QR code on the board.
- Assessments are marked on the server, so the answers never reach a student's
  browser before they hand in. Results are saved with the name they type, in
  data/share. Clear results or Stop sharing deletes them.
- Shared pages update themselves when you edit a lesson or build more of a unit.
- Student pages show the assignment dates, with the next deadline at the top,
  and a button to add every deadline to their calendar with reminders.
- Cover teacher link (Share menu on a lesson): a read-only page with your plan,
  the starter answers, the quiz answers, the downloads and a simple board view.
  It has the answers, so send it to the cover teacher only.

LIVE LESSONS
- On the board, open the Students tab and press Go live (or Share > Go live).
  Students' lesson pages, and unit pages, then follow the slide, the task and
  the timer you show. They can switch Follow off. Press the red Live button on
  the board to stop. It stops on its own after 15 minutes with no board open.
- Students type their name once, tick off steps and press I've finished this
  task. The Tasks and Students tabs on the board show who's finished what.
  I need help puts them in the orange Help list on the board. Press Helped
  to clear them. Progress is kept in data/share/live for 2 hours.

LESSON RESOURCES
- On a lesson's Overview tab, add links (YouTube, websites) or upload files:
  PDFs, images, MP4, MP3 and Office files up to 25 MB. They show on the board's
  Resources tab and on the student page. Files are kept in data/share/res.
- If uploads fail on big files, your host's PHP upload_max_filesize and
  post_max_size are lower than the file. Raise them in cPanel > Select PHP
  Version > Options, or add a link instead.

WEEKLY RETRIEVAL QUIZ, RESULTS, LEARNER NEEDS, SHARING UNITS
- This week has a Weekly retrieval quiz: questions from lessons already taught,
  picked more often where the class did badly. Share it as a link (students
  keep the same link every week) or show it on the board.
- Results shows quiz and assessment scores by unit and criterion, with a
  Catch-up lesson button for weak criteria.
- Settings > Learner needs: pick what each group needs (EAL, dyslexia and so
  on). Every lesson built for that group has the support built in.
- Planning > Download > Unit for a colleague saves a file with the unit's
  sequence and lessons. Your colleague imports it in Settings > Backup.

DISCORD
- Settings > Discord: add a channel's webhook URL (channel settings >
  Integrations > Webhooks > New Webhook > Copy Webhook URL).
- Every Share window then has Post to Discord, which posts the link, the next
  deadline and the QR code. The webhook URLs stay on your server in data/share.
- Needs the PHP curl extension, the same as writing lessons.

INSTALL AS AN APP
- Settings > Install as an app, or the install icon in Chrome or Edge's address
  bar. On iPhone: Share > Add to Home Screen.
- The app opens in its own window and still opens when the Wi-Fi drops. Building
  lessons and sharing need the internet.

LESSON HISTORY
- Every lesson keeps its last 8 versions (saved when you regenerate, try new
  ideas or edit). Press History under a lesson to restore one.

STAFF ACCOUNTS
- Super admin: sees and manages everything, in every department. Set by
  SUPER_ADMINS in config.php.
- Head of department: approves new accounts, adds staff to their department,
  chooses who can edit lessons by hand, and builds with Claude.
- Teacher: opens every lesson and unit in the department, puts them on the
  board and shares them with students, but can't build with Claude. Can edit
  lessons by hand only if their head of department switches it on.
- Each department has its own lessons, units, term dates and staff. Each person
  keeps their own groups, taught ticks, notes, start weeks, student links and
  results.

HOW PEOPLE SIGN IN
- Email and password: anyone can press Ask for an account. They wait until a
  head of department or super admin approves them in Settings > Team. If email
  is set up, they confirm their address first and get an email when approved.
- Email links (no password): with email set up, people can leave the password
  blank when they ask for an account, and press Email me a sign-in link on the
  sign-in page any time. The link works once, for 15 minutes. Their approval
  email includes their first sign-in link. People with a password can use it too.
- Google, Microsoft or OpenID Connect: the first time someone signs in, it
  becomes a request to approve in the same way. People already on the team go
  straight in. The same email works with every way of signing in.
- Two-step codes: anyone with a password can turn on authenticator app codes
  in Settings > Your account, and gets 8 one-time recovery codes. Heads of
  department can turn them off for someone who's lost their phone.
- Forgotten passwords: with email set up, Forgot your password sends a link.
  Without it, a head of department presses Reset link in Settings > Team and
  sends the link on. Links work once.
- 8 wrong passwords in a row locks that email for 15 minutes.
- Settings > Team > Add someone adds a person straight away, and can give you
  a link (and email it) so they choose their own password.
- The admin password in config.php signs you in as a super admin until Google,
  Microsoft or OpenID Connect is set up, or you have your own password. Set PASSWORD_BACKUP to true to keep it after that.

SIGN IN WITH GOOGLE (optional, about 10 minutes)
1. Go to console.cloud.google.com and create a project, e.g. "Crafticulum".
2. APIs & Services > OAuth consent screen: choose External, give it a name and
   your email, save. Then press Publish app so any Google account can sign in.
3. APIs & Services > Credentials > Create credentials > OAuth client ID.
   Type: Web application. Under Authorised JavaScript origins add your site's
   address with no folder and no slash at the end, e.g.
   https://hopeful-lime-horse.185-199-220-70.cpanel.site
   No redirect URIs are needed. Press Create and copy the Client ID.
4. In config.php set GOOGLE_CLIENT_ID to the Client ID.

SIGN IN WITH MICROSOFT (optional, about 10 minutes)
1. Go to entra.microsoft.com (or portal.azure.com) > App registrations > New
   registration. Name: Crafticulum. Supported account types: Accounts in
   any organisational directory and personal Microsoft accounts.
2. Redirect URI: choose Web and enter your site's address followed by
   auth.php?a=ms_cb, e.g.
   https://hopeful-lime-horse.185-199-220-70.cpanel.site/auth.php?a=ms_cb
   (include the folder if Crafticulum is in one).
3. Copy the Application (client) ID into MS_CLIENT_ID in config.php.
4. Certificates & secrets > New client secret. Copy the Value (not the ID)
   into MS_CLIENT_SECRET. Secrets expire: set a reminder to make a new one.
5. Leave MS_TENANT as common. To allow only one organisation, put its
   tenant ID there instead.

SIGN IN WITH OPENID CONNECT (optional: Okta, Auth0, Keycloak, OneLogin and others)
1. In your provider, create an application of type Web (OpenID Connect,
   authorisation code). Allow the scopes openid, email and profile.
2. Sign-in redirect URI: your site's address followed by auth.php?a=oidc_cb, e.g.
   https://hopeful-lime-horse.185-199-220-70.cpanel.site/auth.php?a=oidc_cb
3. In config.php set:
     OIDC_ISSUER         the issuer URL, e.g. https://your-org.okta.com or
                         https://keycloak.example.com/realms/college
     OIDC_CLIENT_ID      the client ID
     OIDC_CLIENT_SECRET  the client secret
     OIDC_NAME           what the button says, e.g. College login
   Crafticulum reads the rest from the provider's discovery document.
4. The provider must share the person's email address (and not mark it as
   unconfirmed), so it can be matched to someone on the team.

SENDING EMAILS (optional)
- Easiest: fill in SMTP_HOST, SMTP_PORT, SMTP_USER, SMTP_PASS and MAIL_FROM with
  an email account's settings. For a cPanel email account these are shown in
  cPanel > Email Accounts > Connect Devices (usually port 587 with 'tls', or
  465 with 'ssl').
- Or leave SMTP_HOST empty and set only MAIL_FROM to use the host's own mail.
  These are more likely to land in junk.
- Without either, everything still works: heads of department copy links and
  send them on themselves.

NEW SETTINGS IN CONFIG.PHP
  Add whichever of these you want to the end of config.php. All are optional,
  and anything you leave out is simply switched off:
     const GOOGLE_CLIENT_ID = '';
     const SUPER_ADMINS = ['your.email@example.com'];
     const PASSWORD_BACKUP = false;
     const MS_CLIENT_ID = '';
     const MS_CLIENT_SECRET = '';
     const MS_TENANT = 'common';
     const MAIL_FROM = '';
     const SMTP_HOST = '';
     const SMTP_PORT = 587;
     const SMTP_USER = '';
     const SMTP_PASS = '';
     const SMTP_SECURE = 'tls';
     const OIDC_ISSUER = '';
     const OIDC_CLIENT_ID = '';
     const OIDC_CLIENT_SECRET = '';
     const OIDC_NAME = 'College login';

BACKUPS AND STUDENT DATA
- The first time Crafticulum is opened each day, the server zips the data
  folder into data/backups and keeps the last 14. Download any of them from
  Settings > Automatic backups, or press Back up now.
- To restore one, unzip it into the data folder in File Manager (replace the
  files). Keep a copy somewhere else too, now and then.
- Settings > Student data sets how long students' names and answers are kept
  (Never, 30 days up to 1 year), and has a button to delete them all now.

KEEPING IT SAFE
- Never share config.php. The API key never goes to the browser.
- Students can open the quiz pages but can't use ai.php without the teacher password.
- If the password ever leaks, change TEACHER_PASSWORD in config.php.


FIRST-TIME SETUP ON A NEW SITE
------------------------------
On a brand new site you don't need to make config.php yourself. Upload the
files, open the site, and it asks for your college's name, your first
department, and the name, email and password of the person in charge (the
super admin). It then writes config.php for you and signs you in.

After that, everything else is set on screen in Settings:
  Your college   the name, logo and colour
  Site setup     Claude's API key, email, and Google, Microsoft or college sign-in

A setting saved on screen is used before the same setting in config.php, so an
existing site can leave its config.php alone and still change things on screen.
setup.php refuses to do anything once config.php exists.


THE SITE'S ADDRESS
------------------
Main address:       https://crafticulum.co.uk
Temporary address:  https://hopeful-lime-horse.185-199-220-70.cpanel.site

Both addresses open the same site. Links in emails and links given to students
(lesson pages, class pages, quizzes, QR codes) use the main address as soon as
the site answers there. Until then they use the address you are on, so nothing
breaks while the domain is being pointed at the server.

If you use Google, Microsoft or college sign-in, add the redirect addresses for
BOTH site addresses in each provider. Settings, Site setup lists them.

To change either address, use Settings, Site setup, Site address, or add these
two lines to config.php:

  const SITE_URL = 'https://crafticulum.co.uk';
  const SITE_URL_TEMP = 'https://hopeful-lime-horse.185-199-220-70.cpanel.site';

An address saved in Settings is used before the one in config.php.


A NEW SITE STARTS EMPTY
A site made with the first-time setup has no built-in units, no built-in
assignment briefs and none of the first college's forms, term dates or tutorial
themes. The Start page asks for the first subject, which is added by uploading
its specification. Until you upload your own files in Settings, Your documents,
the lesson plan, PowerPoint and sequence of learning come out on plain layouts.

The site this software began on keeps its Esports and IT units and its own
documents. Nothing changes there.


THE PUBLIC PAGES
index.html     the front page: what Crafticulum is, who it's for, how to get it
               (it used to be about.html, which now forwards here)
terms.html     terms of use
privacy.html   privacy notice
cookies.html   what is stored on a device

They pick up your name, logo, colour and contact email from Settings, Your
college. In the same place you choose whether people who aren't signed in see
the front page first or go straight to the sign-in page. Anyone who has signed
in on that device before always goes straight to the sign-in page.

The terms and privacy notice were written to match what the software does, but
they are a starting point. Have whoever looks after data protection for your
organisation read them, and edit the files to suit.


USAGE AND COST
Settings, Usage and cost shows how many requests each department and person
has made to Claude each month, and how much text was sent and written. Type in
your model's prices (per million tokens, from Anthropic's pricing page) to see
a rough cost. You can set the most to spend in a month, the most requests in a
month, and the most requests for each department. When a limit is reached,
building with Claude stops until the 1st or until you raise the limit. Counting
starts from the day this version is installed. Your Anthropic bill is always
the real figure.

Nothing needs adding to config.php for any of this.


SECOND MARKER
Second Marker checks a learner's work against the unit's criteria, suggests a
decision and feedback for each one, and fills in the assessment record and the
internal verification forms. The assessor always makes the final decision.

It is off until a head of department, subject leader or the super admin
switches it on for their department, in Settings, Second Marker. There they
also choose:
  - who can use it: leaders only, or teachers as well
  - the model, or let each person choose
  - whether marking a class set and IV sampling are offered (IV is leaders only)
  - anonymous marking only
  - whether PDFs always go with personal details hidden
  - the ending for learner email addresses
  - their own marking guidance, which is added to every check
  - whether the criteria most often not met are shown on the Results page

Once it's on, Second Marker is in the main menu and opens inside Crafticulum,
with the same look and the same light or dark mode. It starts on the unit you
were last working on, and uses the same subjects, units, criteria, assignment
briefs and hand-in dates as the rest of the site. Each person's own marking
choices are in Settings, Second Marker, under Your marking.

It uses the site's Claude key through marker/api.php, so nobody needs a key of
their own, and it counts towards Usage and cost and the monthly limits. Marking
a long piece of work costs more than building a lesson.

Second Marker has no copy of its own of the courses. The subjects, levels,
units, criteria, assignment briefs and their dates are read from Crafticulum
each time it opens, so adding a subject, uploading a brief or changing a
hand-in date shows up in marking straight away. Planning has a Mark work button
for each assignment, which opens marking on that unit and brief.

Second Marker is part of Crafticulum's own page. There is no separate page or
frame: its code is one file, marker/sm.js, which the app loads the first time
someone opens Second Marker. The marker folder now needs only these files:

  sm.js        Second Marker itself
  api.php      Passes checks to Claude with the site's key, and keeps the
               met or not met counts (no names)
  index.php    Sends old links to the right place
  .htaccess and .user.ini

Anything else left in the marker folder from an earlier version (app.html,
hosted.js, hosted-ui.js, meta.js, units.js, briefs.js, batch.js, iv.js and the
other .js files) is no longer used and can be deleted.

Learner work is sent to Claude to be checked and is NOT stored on your server.
Names, ID numbers, email addresses, phone numbers and postcodes are replaced
before it is sent. Drafts stay in the marker's browser, kept apart for each
member of staff. The only thing kept on the server is which criteria each
finished piece of work met, with no name or ID. Check this fits your college's
data protection rules before switching it on.

Big PDFs: the marker folder asks PHP to accept uploads up to 40 MB. If a large
file is refused, ask your web host to raise post_max_size to 40M.


STAYING SIGNED IN
People stay signed in for 30 days on a device they use, and each day they use
it starts the count again. Change the number of days in Settings, Site setup,
Staying signed in. Signing out always ends it straight away, and takes you back
to the front page.

Sign-ins are now kept in data/_sessions, in this site's own folder, with the
site's own cookie (ccsid). Everyone is asked to sign in once after this update.


STARTING FROM YOUR TEAM'S LESSONS
Before a new lesson is built, the lessons your department already has for that
unit are offered first. You can open one, use one as the base for the new
lesson, or build a new one. Using one as the base keeps its structure and
activities and changes only what you asked for; the original is left alone and
the new lesson says which one it was based on.

Every new lesson, including a whole sequence, is also written to match the
department's existing lessons for that unit and not repeat their starters,
tasks or quiz questions. Regenerating a lesson doesn't ask.

Heads of department and subject leaders can switch this off in Settings,
Department rules, Start from our lessons.


MARKING TRACKER, IV PLAN AND FEEDBACK INTO PLANNING
Second Marker's page now has three parts: Mark work, Tracker and IV plan.

Pick the group before you mark. The tracker then shows, for each group and
assignment: the hand-in date, how many handed in (you type this in, as hand-ins
happen outside Crafticulum), how many are marked, how many need to resubmit and
how many have been sampled. The IV plan (heads of department) shows what's been
marked against what's been sampled this academic year, by assessor, unit and
grade, with the gaps listed at the top. Set the share you aim to sample there.

On Planning, the criteria learners miss most in marking show for that unit,
with a button that sets up a catch-up lesson.

All three count from what's marked in Second Marker, and need "Keep results to
improve teaching" switched on in Settings, Second Marker. No learner names, IDs
or work are kept: only the unit, assignment, group, grade and which criteria
were met. IV sampling done on paper isn't counted.


SET-UP CHECKLIST
The super admin's Start page has a checklist for a new college, with a progress
bar. Each row goes to the right part of Settings. Hide it with the cross and
bring it back from Settings, Your college.


A BACKUP OFF THE SERVER
Settings, Automatic backups, A copy off this server. Two ways, use either or
both:

1. Send it to a folder you own. Give the address of a WebDAV folder (Nextcloud,
   ownCloud, Box, pCloud or a college file server), a user name and a password
   or app password. Today's backup is sent the first time someone opens
   Crafticulum each day. Press Send a copy now to check it works. The address
   has to start with https://

2. Have another computer fetch it. Make a private link and put it in a
   scheduled task or backup service, for example:
     curl -fo crafticulum-backup.zip "the link"
   Anyone with the link can download everything, so treat it like a password.
   Making a new link stops the old one working.

Settings warns you if every backup is on the server, if the last copy failed,
or if none has left the server for three days. A backup holds everything on the
site including its settings, so keep it somewhere private.


ERROR LOG
Settings, Error log (super admin only) lists things that went wrong for anyone
on the site: errors in a browser, on the server, from Claude, from email and
from backups. It keeps what the page was doing, the person's role and
department and their browser. It never keeps what was typed, learner work,
email addresses or keys. The newest 500 or so lines are kept in
data/_errors.jsonl. Clear the log with the button.


DEMO
Off unless you switch it on in Settings, Site setup, Demo (type on). The front
page then shows a Try the demo button. Visitors get no account of their own:
they all share one sample department, signed in as "Demo visitor".

The demo can't change settings, people or accounts, send email, share anything
with students or use Second Marker, and it never shows your college's name or
logo. It's emptied the first time someone opens it each day. Building with
Claude is off unless you allow a number of lessons a day in the same place,
which is shared by all visitors and comes out of your Claude credit.

On a site with built-in subjects the demo shows those subjects and uses that
site's documents. Switch it off by typing off in the same box.


SECOND MARKER'S FIRST TWO STEPS
The Unit step and the Learner step are now Crafticulum's own screens, with the
same pickers as the lesson form: subject buttons, level and unit lists, the
assignment, and the criteria tick list with All, None and learning aim buttons.
A green tick shows criteria already taught in your saved lessons, and "Often
missed" shows criteria learners have missed in marking so far.

The brief, assessor guidance, work you've already marked, the resubmission
section and the upload step are the app's own screens too. The class set card,
the checks, the criteria desk and the export step are still the marker's own.
Nothing about how marking works has changed.


CLEAN ADDRESSES AND SEARCH ENGINES
The .htaccess file in this folder now gives every page a clean address:

  /                 the page about Crafticulum for visitors, the app once
                    you're signed in
  /about /contact /terms /privacy /cookies
  /signin           the sign-in page
  /this-week /lessons /planning /results /second-marker /settings
  /s/CODE           a student's lesson or class page
  /sitemap.xml and /robots.txt

Every old address still works. about.html and the other public pages move to
the new address on their own, and student links and QR codes already given out
(student.html?id=...) carry on working. New student links use the short form.

For search engines and link previews, the public pages now carry their full
address, a share picture (og.png) and structured data: the organisation, the
site, the app, the questions and answers on the front page, and a breadcrumb on
the legal pages. It is built by page.php from what the pages themselves say.
It describes Crafticulum only. A college's own name, logo and details are never
put in it, even when the front pages show them. The app's own pages and student
pages are marked not to be listed.

This needs Apache's mod_rewrite, which almost every cPanel host has. If yours
doesn't, the app notices, keeps the old addresses, and nothing breaks. Upload
.htaccess along with the rest: it starts with a dot, so some file managers hide
it until you switch on "show hidden files".

After uploading, check three things: your address shows the front page when
you're signed out, yoursite/planning opens Planning when you're signed in, and
yoursite/sitemap.xml lists your pages. Then add the sitemap in Google Search
Console. If the whole site shows an error straight after uploading, your host
doesn't allow one of the lines in .htaccess: delete everything from "Clean
addresses" to the end of that file and it's back to how it was.


CONTACT AND SUPPORT
There is a contact page at /contact (contact.html) for help, problems, feedback
and enquiries. It is linked from the front page, the bottom of every public
page, and Help and feedback in the app's menu. Signed-in staff have their name
and email filled in, and the message notes which page they came from.

Every message is kept on the site and listed in Settings, Messages (super admin
only), where you can reply, mark it as done or delete it. A reply written
there is emailed to the person with their message underneath, and is kept under
the message so you can see what was said. It needs email set up in Site setup;
without it the button opens your own email program instead. If email is
set up, each one is also sent to the contact email in Settings, Your college
(or to the super admins if none is set), and replying to that email goes
straight to the person who wrote.

To keep robots out, the form has a hidden box and a short wait, and one place
can send five messages an hour. The newest 500 messages are kept.


DIAGRAMS AND PICTURES IN LESSONS
New lessons get visuals in the teaching content, tied to the unit and the
criteria being taught.

Diagrams: Claude describes the diagram (a flow of steps, a cycle, a comparison,
a breakdown, a timeline or layers) and Crafticulum draws it, so every diagram
matches the lesson's own wording and the app's look. Every lesson that teaches
new content is asked for at least one.

Pictures: where a real photograph would help, Claude gives a few search words
and the app finds a freely licensed photo on Wikimedia Commons and shows it
with its credit and licence. Logos, screenshots and named people are never
asked for. On the Teach tab you can press Different picture, Remove picture or
Remove diagram. Check each picture before you teach with it: it is the first
good match for the search, not something a person chose.

They show on the Teach tab, on the board (Show the diagram, Show the picture),
on the student page, and each gets its own slide in the PowerPoint.

Heads of department can switch pictures off in Settings, Department rules.
Diagrams stay on. Lessons built before this update have none until they are
regenerated or given New teaching content.


REGENERATE EVERY LESSON
The year planner on Planning has Regenerate every lesson, next to Build every
unit. It writes every built lesson again with the same settings and builds any
that are missing. Press it twice to confirm. Lessons keep their place and the
links you have shared keep working, but changes made by hand are replaced.
Each lesson is a full build, so a whole year uses a lot of Claude credit.
Stop at any time with the Stop button.


COLLEGES AND WHO CAN DO WHAT
One site can now hold several colleges. Each college has its own name, logo,
colour, contact email, departments, staff, subjects and lessons, and none can
see another's.

Super admin
  Looks after the whole site and every college. Adds colleges and their
  managers in Settings, Colleges, sets a monthly limit for each, can switch a
  college off (its staff can't sign in until it's back on), and can step into
  any college with "Work in this college". Only the super admin sees Site
  setup, backups, the error log, messages and prices.

College manager
  Like the super admin, but for one college. Sets the college's name, logo,
  colour and contact email, adds its departments, adds and approves its heads
  of department and teachers and chooses what each can do, sees the college's
  usage, and can work in any of its departments. Can't see the site's own
  settings, other colleges, or other managers.

Head of department or subject leader, and teacher
  As before, inside their own department.

What happens to the site you already have: it becomes one college holding
every department you had, with the same name, logo, colour, subjects and
people. Nothing moves and nobody has to do anything. Your own account is still
the super admin.

A new college starts with no subjects: its staff upload their own
specifications. Student pages carry the name, logo and colour of the college
whose teacher shared them. With more than one college on the site, the sign-in
page and public pages always show Crafticulum only, and Settings, Your college
gains "Who runs this site" and the site's own contact email for the terms and
privacy pages.

To add a college: Settings, Colleges, Add a college. Give its name, its first
department and its manager's email. The manager is emailed a link to choose a
password.


THE APP NOW LIVES AT /app
Your site's own address (crafticulum.co.uk) is now the front page for everyone.
The app is at /app, and that's where Sign in goes. Nothing needs doing:
  - Old links still work. /about and about.html go to the front page. Links
    from old emails (sign-in links, password resets, invitations) and old
    bookmarks to index.html#... are sent on to the app.
  - /signin, /lessons, /planning, /settings and the rest are unchanged.
  - Student links and QR codes are unchanged.
  - People who installed it as an app keep it; it opens at /app from now on.
After uploading, check: your address shows the front page, Sign in takes you
to /app, and signing in with Google or Microsoft still comes back signed in.
The redirect addresses you gave Google and Microsoft have not changed.


PLANS AND PAYMENTS
You can charge for Crafticulum with monthly plans, paid by card through Stripe.
It is OFF until you switch it on, and while it is off nothing changes.

The plans (you can change every price and limit):
  Free          GBP 0         1 person, 1 department, 10 builds, 2 Second Marker checks a month
  Teacher       GBP 4.99/mo   1 person, 1 department, 100 builds, 40 checks a month
  Department    GBP 49/mo     10 people, 1 department, 600 builds, 300 checks a month
  Institution   custom quote  no limits; you set it by hand

A build is one request to Claude. A lesson is usually one; a quiz, a set of
slides or a fact check is one each. A Second Marker check is one request, and a
long piece of work can take more than one. Limits start again on the 1st.
Check the limits against what Claude costs you: Settings, Usage and cost shows
your real cost per request, so you can see whether a plan covers its own use.

Setting it up (Settings, Plans and payments, super admin only):
  1. Make a Stripe account at stripe.com. Start in Stripe's test mode.
  2. In Stripe: Developers, API keys. Copy the Secret key (sk_test_...) into
     the Secret key box.
  3. In Stripe: Developers, Webhooks, Add endpoint. Paste the webhook address
     shown in Settings (it ends billing.php?a=hook). Choose these events:
       checkout.session.completed
       customer.subscription.created
       customer.subscription.updated
       customer.subscription.deleted
       invoice.payment_failed
     Copy the endpoint's Signing secret (whsec_...) into the Signing secret box.
  4. From the same API keys page, copy the Publishable key (pk_test_...) into
     the Publishable key box. With it, paying, changing the card, cancelling
     and invoices all happen inside the app, in Stripe's own form. Without it,
     people are sent to Stripe's pages instead, and for that you must save
     Stripe's Customer portal settings once (Settings, Billing, Customer portal).
  5. Save keys, then switch on Plans are switched on.
  6. Try it: sign up as a new person from the front page, choose Teacher
     and pay with Stripe's test card 4242 4242 4242 4242 (any future date, any
     three digits). You should come back on Teacher. Cancel it in Manage
     billing and it should say when the plan ends.
  7. When you're happy, swap in the live Secret key (sk_live_...) and the live
     Publishable key (pk_live_...), add the same webhook in Stripe's live mode
     and paste in its Signing secret. The two keys must be a matching pair.

What changes when plans are on:
  - The front page shows a Pricing section, and the terms and privacy pages
    show their paragraphs about payments.
  - New people can sign up on their own. They get their own space (a college
    of one, on Free) and manage it themselves. They can still ask to join a
    college that's already here instead.
  - Someone new who signs in with Google or Microsoft also gets their own Free
    space. So add your colleges' staff in Settings, Team BEFORE they first sign
    in, or they'll end up in a space of their own rather than in their college.
  - Each college's manager gets Settings, Plan and billing: what's used this
    month, the plans, and buttons to pay or manage billing.
  - Colleges you had already set up are on Institution, so nothing changes for
    them. Change any college's plan by hand in Settings, Colleges.
  - When a limit is reached, building stops with a message that says why.
    Nothing is ever deleted. If a paid plan ends the college goes back to Free
    and keeps its people and lessons; it just can't add more people.

Emails the college's manager gets (when email is set up):
  - the plan has started, with what it includes
  - most of the month's builds or checks are used (at 80%), and when all are
  - a payment didn't go through (once, not every time Stripe tries again)
  - the plan has been cancelled, with the date it ends
  - the plan has ended and the account is on Free

Keeping Free accounts from costing you:
  - A ceiling for Free accounts (Settings, Plans and payments): every Free
    account together can make at most this many requests to Claude a month.
    It starts at 1000. The section shows how many have been used and by how
    many accounts. When it's reached, Free accounts are asked to wait for the
    1st or move to a paid plan. Paid plans are not affected. 0 is no ceiling.
  - Someone who signs up on their own with a password must confirm their email
    before anything is built (when email is set up). They see a bar on their
    Start page with Send it again. Signing in with Google or Microsoft already
    proves the address, so those people aren't asked.
  - New sign-ups from one place are limited to 5 a day, and 300 a day in all.

When a limit is reached:
  - The person sees what happened and the next plan up, with its price and a
    button that opens the payment form there and then. People who can't pay
    for the account are told to ask whoever manages it.
  - The Start page shows how much of the month's allowance has been used.

Good to know:
  - Card details never reach your server. This site stores Stripe's reference
    numbers for the customer and subscription, and whether it's paid up.
  - Prices are charged as you set them. VAT or other tax is not added. If you
    need to charge it, switch on Stripe Tax in Stripe.
  - Changing a price affects new payments. People already paying stay on their
    price until they switch plan.
  - Setting a paying college's plan by hand does not change what Stripe charges
    them. Cancel or refund in Stripe if you need to.
  - The wording about payments in terms.html is a starting point. Read it and
    change it to match how you want to handle cancellations and refunds.


PLAIN DOCUMENT NAMES IN TASKS
New lessons number their tasks (Task 1, Task 2), write each step as a full
sentence, and name any document a student makes in plain words that match the
lesson, such as "Unit 3 Task 2 Research analysis" followed by their name. No
underscores or codes. Lessons built before this show old names such as
B_Analysis_Surname as "B Analysis (your surname)" when opened. To get the new
wording in full, regenerate the lesson, or use Regenerate every lesson on the
year planner.


SECOND MARKER: REVIEW AND EXPORT
The last step of Second Marker (the summary, the downloads and the email) is
now drawn by the app itself, like steps 1 to 3, so it looks and behaves like
the rest of Crafticulum. The records, marked copies and emails it makes are
unchanged. The checks (step 4) and the class set card are still drawn by the
marking code, but now have the same cards and numbered headings as the app's
own steps, so the six steps read as one. The marking desk (step 5) is left as
it was on purpose: it is where highlighting and commenting on the work happen.


THE NEW LOGO
Crafticulum's own logo is now the folded page with a C cut into it, with the
name written as craft + iculum. It is used everywhere Crafticulum's own logo
shows: the front page and the other public pages, the sign-in page, the app's
side menu and Start page, student pages, the browser tab icon, the icon when
it's installed as an app, and the picture shown when a link is shared.
A college's own logo and colour still replace it for that college's staff and
students, exactly as before. Browsers and phones keep old icons for a while:
a tab icon can take a refresh or two, and an installed app may need removing
and adding again to pick up the new icon.


A QUICKER START FOR SOMEONE NEW
Someone who signs up on their own is greeted by name, told what their plan
includes, and sees one list of what to do, not the college set-up list as well.
Anyone with no subjects yet can now choose Start with one topic instead of
uploading a specification: they type the subject, level, topic and (if they
like) what students should be able to do, and can build a lesson straight away.
The full specification can be added later.


THE SUPER ADMIN IS NOT PART OF ANY COLLEGE
The super admin sees and can change everything, but belongs to no college,
including the one the site started with.
  - Settings opens on Site overview: every college with its plan, departments,
    people, lessons, requests to Claude this month and when someone last signed
    in, plus the latest activity across the site. Look inside opens a college.
  - The super admin is never counted as one of a college's people, so they
    never take a place on its plan.
  - What the super admin builds is counted for the site, not for the college
    they are looking at. It doesn't come out of that college's allowance, and
    it shows in Usage and cost as its own line.
  - The whole-site limits in Usage and cost still apply to everyone.


COLLEGES: A LIST, THEN INSIDE EACH ONE
Settings, People, Colleges is now a list of every college: its manager,
departments, people and plan. Open one (from there or from Site overview) and
you get that college's own page, where the super admin can change everything
without leaving Settings:
  - its name, contact email, colour and logo
  - its plan and its monthly limit of requests to Claude
  - its managers (add one and they are emailed a link)
  - its departments (rename, add, remove an empty one)
  - its people (who they are, their role and department, when they last
    signed in; add someone new). Changing a role or moving someone is still
    done in Team.
  - the latest activity in it
  - whether its staff can sign in, and removing it once it has no people
Work inside this college opens the app as that college, for its lessons,
planning and results. All colleges takes you back to the list.


SETTINGS IN GROUPS
Settings is now in groups along the top: Overview (super admin), You, People,
College, Teaching, Site (super admin), and Data and backups. Each group shows
only its own parts. Everything, at the end, shows the lot on one page as
before. Links from elsewhere in the app open the right group for you.

THE SUPER ADMIN STANDS OUTSIDE EVERY COLLEGE (v90)
Once the site has more than one college, the super admin signs in to their own
home: Settings, with the site overview, colleges, team, usage, the site's own
settings and backups. No college's name, colour, lessons or planning is shown,
and the workspace screens are hidden. To see inside a college, open it in
Colleges and press Work inside this college. A bar across the top then says
which college you are inside, with Back to super admin to step out again.
Every new sign-in starts outside. On a site with a single college nothing
changes.

TEAM BY COLLEGE (v90)
For the super admin, Team now starts with a College drop-down. Pick one and
its people are listed under each of its departments. Add someone and they go
into the college that is picked. The department list on each person also has
"Move to ..." for moving them to another college.

DELETING A COLLEGE FOR GOOD (v90)
Open a college in Colleges. Under Switch off or remove there is now Delete
this college and everything in it. You type the college's name, press Delete
for good, then press again to confirm. It removes the college's people and
their accounts, its departments, lessons, planning, results, activity log and
the pages given to its students. It can't be undone from the app, so take a
backup first if you might want any of it back. It is refused for the last
college on the site, the one you are working inside, and one that is still
paying by card (cancel its plan first).

NO FLASH OF THE APP BEFORE SIGN-IN (v90)
Opening Sign in or choosing a plan no longer shows the app for a moment before
the right screen appears. A plain cover with the logo stays up until it's ready.

EMAILS IN THE SITE'S OWN LOOK (v91)
Every email the site sends now has the Crafticulum logo and colours, the
subject as a heading, a proper button for each link, and a footer with links
to the site, privacy, terms and contact. The same words are still sent as
plain text too, for mail apps that don't show pictures. The logo is a new
file, mail-logo.png, which sits beside index.html, so upload it with the rest.
Nothing to set up: it uses the mail settings you already have.

WELCOME EMAIL (v91)
Someone who starts their own space (with a password, Google or Microsoft) is
sent a welcome email with three first steps: add your subject, build your
first lesson, teach it and share it. With a password it also carries the
button to confirm their email.

THE COLLEGE MANAGER'S OVERVIEW (v91)
A college manager's Settings now opens on an Overview of their own college:
people, departments, shared lessons and requests this month, how much of the
plan's monthly allowance is used, anyone waiting for approval, and each
department with its head, staff and lessons and a Work in it button. Their
Team list is grouped by department.

SECOND MARKER: THE MARKING DESK (v91)
Step 5 now looks like the other steps: a slim bar for the learner, the mark
sheet and feedback in cards with numbered headings, criteria codes as pills,
and green, red and amber for Y, N and R. Only the look changed. Highlighting,
comments, QuickMarks, focus mode and the keyboard shortcuts work as before.

"CRAFTICULUM AI" (v92)
Everything people read now calls the AI "Crafticulum AI": the app, the front
page, terms, cookies, the emails and the error messages. Mentions of Claude
and "made by Anthropic" have gone. Three places still name Anthropic, on
purpose:
  - Settings, Site setup, where the super admin pastes the key and reads
    error details. Only the super admin sees these.
  - This file and config.example.php, which only you read.
  - One row of the privacy page's list of who handles data. The law expects
    the companies that process people's data to be named, so that row now
    reads "Anthropic, which provides the AI models behind Crafticulum AI".
Teachers no longer see the AI service's own error text. They get a plain
"tell whoever looks after the site", and the detail goes to the super admin.
In Second Marker the model choices are now Most capable, Balanced and Fastest.

NOTIFICATIONS (v93)
Settings, You, Notifications. Each person switches them on for each phone,
tablet or computer they want, and chooses what to hear about:
  - When something finishes: a lesson, a set of lessons or a Second Marker
    check that finishes while they're on another tab or app.
  - Assignment dates: the day before and on the day an assignment is handed
    out, is due in, or has resubmissions due. The dates are the ones in that
    person's own planning.
  - Comments and sign-off, people waiting for approval, messages from the
    contact page, and (for a college's manager) plan limits and payments.
Nothing to set up and no outside service to pay for. The site makes its own
key the first time (kept in data/_vapid.json; don't delete it or everyone has
to switch notifications on again). New file: push-lib.php. The server needs
PHP's OpenSSL and cURL, which nearly every host has.
On an iPhone or iPad, Crafticulum has to be added to the Home Screen first
(Share, Add to Home Screen) and opened from there. That is Apple's rule.
Assignment reminders go out the first time anyone opens the site after 7am.
To send them even on a day nobody signs in, add a scheduled job (cPanel, Cron
Jobs) that opens the address shown at the bottom of Settings, Notifications
once a day a little after 7am, for example:
  5 7 * * * curl -s "PASTE THE ADDRESS HERE" > /dev/null
The same daily run clears out colleges deleted more than 14 days ago.

DELETED COLLEGES ARE KEPT FOR 14 DAYS (v93)
Deleting a college no longer destroys it on the spot. Its people can't sign in
from that moment and its students' pages stop working, but everything is set
aside in data/_deleted for 14 days. Until then it shows under Deleted colleges
in the Colleges list, with Put it back and Remove now. After 14 days it is
removed for good.
A college whose plan has been cancelled can now be deleted straight away, even
while the month it has paid for runs out. One that is still paying is refused
until its plan is cancelled.

FRONT PAGE (v93)
Questions people ask now runs the full width of the page, and the line about
who writes the lessons has gone from the footer.

AI TOOLS (v94)
A new page in the menu, at /ai-tools. 27 new tools in five groups (Planning,
Resources, Communication and admin, SEND and inclusion, Leadership), plus the
four Crafticulum already had (lesson planner, scheme of learning, curriculum
planner, editable PowerPoint), which open their existing screens.
Every tool works the same way: fill in a few boxes, press Write it, then
change any of the text on the page. "Improve this part" rewrites one part at a
time (again, easier to read, more demanding, shorter, more detail, or say what
to change). Save to your own library or for the department, download as Word,
print or save as PDF, or copy. The last five saved versions are kept.
Tools use the subject, unit and criteria you pick, and the learner needs of
the group you're on. Tools with a tone can use your own saved tones.
Tools about one learner (report, parent letter, feedback, behaviour plan, EHCP
support): the learner's first name is never sent or saved. It is written as
[Name] and put back on your screen. Emails, phone numbers and postcodes are
taken out of your notes before anything is sent, and these can't be shared
with the department.
Who can run them: the same people who can build lessons (heads of department
with that permission, college managers). Teachers can open, change, download
and keep their own copy of anything shared with the department.
Each run counts as one build against the month's allowance.
Upload .htaccess with this update: it carries the new /ai-tools address.

THE MENU IN FIVE AREAS (v95)
The side menu is grouped as Dashboard (was Start), AI tools, Curriculum and
lessons, Learners and assessment, and Resource library. Every screen people
knew is still there under its heading. Settings stays separate, below.

AI TOOLS FOR EVERY TEACHER (v95)
Teachers can now use AI Tools and the assistant, not only people who can build
lessons. Since v96 the college's manager controls this in "Who can use what"
(see ROOMS below). Building full lessons with
the AI is unchanged and still needs that permission. Every use counts toward
the month's allowance.

THE ASSISTANT (v95)
A chat for teaching questions, at /assistant. Chats are saved. It knows the
unit you pick and its criteria. "Remember this" keeps a note it uses in every
chat and every tool. "Save as a prompt" keeps a message to reuse. A file can
be attached for the chat (PDF with real text, Word, plain text): it is read
for that chat and not kept. "Make this a resource" opens any reply in the
editor, to save, share and download.

RESOURCE LIBRARY (v95)
One page, at /library, with every lesson and everything the tools and the
assistant have made, yours and the department's, with search and filters.

POWERPOINT FOR TOOL RESOURCES, AND TIME SAVED (v95)
Anything in the AI Tools editor can be downloaded as an editable PowerPoint.
The college manager's overview shows how many resources AI Tools made this
month and a rough estimate of the time saved. There is a new planning tool,
Intervention session.

LEARNERS (v95)
A new page, at /learners. A teacher pastes a class list, picks a unit, and
marks each learner against each criterion: working towards, achieved, not yet
or resubmission. It shows where the group needs help and can plan an
intervention session from that (only the counts are sent, never names). The
grid downloads as a spreadsheet file. Each teacher's lists and marks are in
their own space, and every mark is the teacher's decision.

HAND-IN LINKS FOR STUDENTS (v95). OFF UNLESS YOU SWITCH IT ON.
Settings, Teaching, Department rules, "Hand-in links for students". Only a
head of department or above can switch it on, for their own department.
When on, staff can make a link in Learners for an assignment. Students open
it with no sign-in, type their name and hand in one file (PDF, Word,
PowerPoint, text, pictures, zip).
THIS IS THE ONE PLACE LEARNERS' WORK IS KEPT ON THE SERVER. Before switching
it on, check it fits your data protection rules. What it does to keep it safe:
  - Each file is deleted after 30 days. Staff can delete sooner.
  - Files are stored in data/_handin under random names with no file type,
    and are only ever sent to the teacher who made the link or a leader of
    that department, as a download.
  - Programs and web pages can't be handed in.
  - Second Marker is unchanged: it still keeps no learner work.
The size limit is 20 MB, or your host's own PHP upload limit if that is lower
(many hosts start at 2 MB; raise upload_max_filesize and post_max_size in
cPanel, "MultiPHP INI Editor", if students need more).
New files: handin.php and handin.html. Upload .htaccess too (new addresses).

ROOMS: WHO CAN USE WHAT (v96)
Each part of the app is a room: AI Tools, Crafti, Resource library, Learners,
Hand-in links and Second Marker. Every plan has every room.
  - Super admin: Settings, Site, Rooms. Close a room and it is gone for every
    college, from the menu, by its address and on the server. You can still
    go into every room yourself. Working inside a college, "Who can use what"
    also lets you close a room for that college only.
  - College manager: Settings, College, "Who can use what". A switch for each
    room for heads of department, and one for teachers. The manager has every
    room that hasn't been closed above them.
The department switch "AI Tools for every teacher" from v95 has gone: this
replaces it. Hand-in links and Second Marker still have to be switched on by
each department as well. Building full lessons with the AI is still its own
permission in Team.

CRAFTI (v96)
The assistant is now called Crafti. He has his own cartoon face, says hello
by name, and talks in a livelier, friendlier way. He is told to drop the jokes
for anything serious (safeguarding, behaviour, SEND, complaints) and to write
anything you'll send to someone else in a normal professional voice.

FRONT PAGE (v96)
A new section, "An AI toolkit for the rest of the job", introduces Crafti, the
32 tools, Learners and the library, with a link in the top menu and a new
question in Questions people ask.

JOINED UP (v96)
  - Handed-in work: a PDF or Word file now has "Check in Second Marker". One
    press fetches the file and loads it into Second Marker, ready to check.
    Nothing new is stored: Second Marker still keeps no learner work.
  - Learners: heads of department and above get a "Whole department" tab with
    every teacher's groups for a unit, how much each has achieved and what is
    most missed. They can look inside a group but only its teacher can change
    it. Teachers are told on the page that their leaders can see their lists.
  - Any lesson can be opened in the same editor as the tools' resources: from
    the bottom of the lesson, or from the Resource library ("Open in the
    editor"). It opens as a copy, part by part, to rewrite, save and download
    as Word, PDF or PowerPoint. The lesson, its board and its student page are
    not changed, and lessons keep their own edit screen for those.

WHAT'S NEW IN v97

HOUSE RULES FOR THE AI (v97)
  Settings, Your college, "House rules for the AI". A college's manager (or the
  super admin working inside the college) writes four short pieces: about the
  college, tone and wording, policies to follow, and a sign-off for letters.
  The server puts them in front of every request to the AI from that college:
  lessons, all the AI Tools and Crafti. Staff can't switch them off. They are
  not used by Second Marker. Don't write about a named person in them.

ANONYMOUS LEARNERS (v97)
  Settings, Your college, "Anonymous learners". One switch for the college's
  manager. While it is on:
  - class lists in Learners take learner numbers, not names
  - the server replaces anything that isn't a number or code with
    "Learner 01", "Learner 02" and so on, when saving and when reading, so a
    name can't be saved by any route. Names saved before the switch are shown
    as numbers and written over the next time each list is saved
  - the name picker's class lists on the board are treated the same way
  - students handing in work type their learner number, a name is refused, and
    the file is renamed after the number
  - the learner-name box in the AI tools is hidden
  The inside of a handed-in file isn't changed. Ask students to leave their
  name off the work.

A CHAT BECOMES A FULL LESSON (v97)
  In Crafti, "Build the full lesson" under the last reply builds a complete
  lesson from the conversation (plan, teaching content, tasks, quiz, slides)
  in the unit it belongs to. It needs the build-lessons permission.

ONE EDITOR (v97)
  "Open in the editor" on a lesson now edits the lesson itself, not a copy.
  Every part of the lesson is there: objectives, starter, the board, the plan,
  teaching parts, tasks, the exit quiz, independent study, stretch and
  support, and the plenary. Rewrite a part with the AI, add a teaching part,
  press Save lesson, and the board, student page, task sheets and quiz all
  use the new version. If a part can't be read back (for example a quiz with
  no answer marked "(correct)") that part is left as it was and you're told.
  The quick Edit button on the lesson page is still there for typos.

RESUBMISSIONS AND INTERNAL VERIFICATION (v97)
  Learners, under the criteria grid for a group and unit:
  - Submissions and resubmissions: the date each learner handed in, the date
    feedback went back, whether a resubmission was authorised and by whom,
    when it's due, when it came back and the outcome. Ticking Authorised sets
    the due date 15 working days after the feedback (weekends not counted,
    holidays are). Change it to suit your centre or awarding body.
  - Internal verification: leaders open Learners, Whole department, press
    Look on a group, choose the learners they sampled and either sign the
    marking off or send actions to the teacher (who is emailed if email is set
    up). The teacher sees them under the grid and marks them done. Nobody can
    verify their own marking. Records are kept in data/d/<dept>/_iv.json and
    hold no learner names.

AI TOOLS PAGE (v97)
  Rebuilt: a search box first, popular tools, an icon
  and colour for each kind of tool, favourites (the star), recent tools, your
  latest saved resources, and a way to Crafti when nothing matches. The
  Lessons menu item has its own icon now.

WHERE YOU TEACH: THE EDUCATION FRAMEWORK (v97)
  Settings, Subjects.
  - "Where you teach": country (England, Wales, Scotland, Northern Ireland),
    setting (early years to professional training), stage, framework level,
    qualification type and awarding organisation. Each box only offers what
    fits the ones above it, every box has "Something else", and changing a
    box clears only what no longer fits below it. Set once per department.
  - Lessons, the AI Tools and Crafti are then told who is teaching and where,
    and write in that system's terms. Pass, Merit and Distinction wording is
    only used where the criteria are graded that way.
  - "Add a subject by hand" for a curriculum subject, a university module or
    a course of your own, with no specification and no awarding organisation.
  - "Details" on each subject: its own context, qualification title and
    number, specification version, status (current, teach-out, withdrawn) and
    a record of who checked it against the official register, and when.
  IMPORTANT: the lists of stages, levels, qualification types and awarding
  organisations were written by hand on 19 Nov 2026. They are a starting set,
  not an official register. Nothing is shown as verified until a person has
  recorded that they checked it. There is no live link to Ofqual,
  Qualifications Wales or the Scottish qualifications body in this release.
  Subjects and lessons saved before v97 are untouched and work as before.

WHAT'S NEW IN v98

THE OFQUAL REGISTER (v98)
  Settings, Subjects, Details on a subject: "Find it on the Ofqual register".
  Type part of the title or the qualification number. The server asks the
  regulator's public register (register-api.ofqual.gov.uk, no key needed) and
  shows what it finds, marked Available, Teach-out or Withdrawn from the
  register's own dates. "Use this" fills in the title, number, awarding
  organisation, level, type and status, and the subject shows "On the Ofqual
  register" with who matched it and when. "Check again" asks the register
  afresh and changes the status if the qualification has been withdrawn.
  - It covers qualifications regulated by Ofqual (England) and CCEA
    Regulation (Northern Ireland). Wales and Scotland still record their own
    check by hand.
  - Answers are kept for a day in data/_register so the same search isn't
    sent twice.
  - Your web host must allow outgoing connections to
    register-api.ofqual.gov.uk. If it doesn't, the page says the register
    couldn't be reached and nothing else is affected.
  New file: register.php.

MY EDUCATION PROFILE (v98)
  Each person can say where and what they teach, in five short steps that can
  all be skipped: country (the four nations, or international), setting,
  institution, what they teach (role, subjects, stages, levels, awarding
  organisations, qualifications), finish.
  - People who haven't done it see a card on the dashboard. Nobody is stopped
    from using the app. It's also in Settings, You, "My education profile".
  - Lessons, AI Tools and Crafti start from it: the AI is told the role,
    subjects, stage, setting and country (not the institution's name), and
    where a department hasn't set "Where you teach", the profile is used.
  - Choosing an institution is information only. It never gives access to a
    college's workspace, lessons or settings: that still goes through Team.
  - Several institutions can be chosen, with one marked as the main one, or
    "I work independently", or one typed in by hand if it isn't listed.

THE INSTITUTION DIRECTORY (v98)
  Settings, Site, "Institution directory" (super admin only). The list people
  search in step three, by name, town, postcode or reference number.
  - England: "Read it in now" downloads the Department for Education's Get
    Information About Schools file and reads it in. The address is in the box
    and can be changed. NOTE: this address could not be tested from where
    this release was built. If it fails, get the current "all establishment
    data" address from the Downloads page of
    get-information-schools.service.gov.uk and paste it in, or download the
    file yourself, put it in data/_inst/import by FTP and read it in from
    there.
  - Any other official list (Scotland, Wales, Northern Ireland, universities,
    training providers): give the CSV's web address or put the file in
    data/_inst/import, then say which column is the name, town, postcode,
    reference, type and open/closed status.
  - Closed institutions are kept and shown as Closed, below the open ones.
  - Each list records where it came from, when, who read it in and how many
    rows. Lists set to refresh are read again weekly by the same scheduled
    job as notifications (Settings, Notifications shows its address).
  - Until a list is read in, people type their institution's name.
  New file: inst-lib.php. Lists are kept in data/_inst.

WHAT'S NEW IN v99

THE DIRECTORY FILLS MORE EASILY (v99)
  - England: if today's file isn't published yet, the last three days are
    tried. A site whose directory is empty reads the England list in by itself
    the first time the daily scheduled job runs.
  - The address of the England file still could not be tested from where this
    release was built. If "Read it in now" fails, download "all establishment
    data" from the Downloads page of get-information-schools.service.gov.uk
    and use "Another list": choose the file, name the list, press Read it in.
  - Files can be sent up from your own computer, in pieces, so your host's
    upload limit doesn't matter. CSV and Excel (.xlsx) both work.
  - Column names no longer have to be typed. They are worked out from the
    file's first row (title rows above it are skipped) and shown after the
    import. Fill a column box in only to correct one.
  - No list is bundled in the download. The official files are too large and
    change daily, so they are read in on your server.

THE STEPS OPEN BY THEMSELVES (v99)
  Someone who has never seen the profile steps gets them on their dashboard
  the first time they sign in, once. Closing them is remembered on every
  device, and the dashboard card stays until the profile is done or hidden.

ASKING TO JOIN A COLLEGE (v99)
  - A college's manager links the college to its record in the directory:
    Settings, Your college, "Your institution and requests to join".
  - Someone whose own profile names that institution is told it uses
    Crafticulum and can press Ask to join. Asking changes nothing.
  - The manager sees who has asked, picks a department and approves or
    declines. Approved people come in as teachers, and are emailed if email is
    set up. Lessons in the workspace they came from stay there.
  - Someone who manages a college with other staff or a paid plan can't be
    moved out from under it.
  - Anyone can type an institution's name into a profile, so approving is the
    check: make sure you know who they are.

PROFILES IN PROPER TABLES (v99)
  The directory and every education profile now live in a small SQLite
  database, data/_inst/directory.sqlite, with separate tables:
  institutions, institution_campuses, institution_import_logs,
  user_education_profiles, user_subjects, user_teaching_levels,
  user_awarding_organisations, user_qualifications, institution_memberships.
  Profiles saved in v98 are carried across the first time each person signs
  in. If your server's PHP doesn't have SQLite, files are used instead and
  everything works the same. Accounts, lessons and the rest of the app are
  stored as they always were.

THE PROFILE SETS WHERE YOU START (v99)
  With no choice remembered yet, the lesson form and Planning open on the
  subject and level in your profile, if your department teaches it. The
  Resource library opens narrowed to it. Change either and your choice stays.

WHAT'S NEW IN v100

YOUR COLLEGE OR SCHOOL, AS YOU TYPE (v100)
  Once a list has been read into the institution directory, both sign-up
  forms search it as people type. Two letters is enough: "MID" brings up
  Hilltop College. Names that start with what was typed come first, closed
  institutions are left out, and the arrow keys and Enter work.
  - "Your college, school or team" (starting your own account): pick from the
    list or type anything. If the one you pick already has a workspace here,
    you're offered "Ask to join it instead".
  - "Register for an account": a new "Your college or school" box. Pick a
    college that uses Crafticulum and the request goes to that college's
    manager, who approves it under Team. Heads of department can't approve
    those. The department list narrows to that college.
  - Typing something that isn't picked from the list works as before: the
    request goes to the head of department.
  - Whatever is picked is saved to the person's own education profile.
    Starting your own account with a school picked does NOT make your
    workspace that school's: only a manager can link a college, in Settings.
  - "Uses Crafticulum" only appears for colleges whose manager has linked
    them (Settings, Your college, "Your institution and requests to join").
  Speed: the search is its own small file (inst.php) that starts no session.
  Short searches are kept on the server until the next import, the browser
  keeps answers too, and a longer word is narrowed from the last answer
  without asking the server again. Each address is limited to 240 searches a
  minute.
  Until a list has been read in, the forms look exactly as they did.
  New file: inst.php.

WHAT'S NEW IN v103

SCHOOLS LIST FIXES (v103)
  - Fixed: the sign-up boxes could show nothing. A search made before the list
    was read in came back empty, and that empty answer was kept by the browser
    for an hour. Empty answers are never kept now, and each search carries the
    date of the last import so nothing old is shown after one.
  - If the list can't be searched, or nothing matches, the box says so
    instead of staying blank.
  - Closed schools are left out when a list is read in, and ones read in by an
    earlier version are removed the first time the directory is used.
  - A list read in by v98 (kept as a file) is moved into the table by itself.
  - Weekly refresh: the Institution directory section now says whether the
    scheduled job is running and when the list is next due, and shows the
    line to add in cPanel, Cron Jobs, if it isn't. It is the same daily job
    that sends notifications.

WHAT'S NEW IN v104

WHAT EACH PERSON CAN USE, ON THE TEAM PAGE (v104)
  Settings, People, Team. Each teacher and head of department now has six
  ticks under "Can use", beside their other options: AI Tools, Crafti,
  Resource library, Learners, Hand-in links, Second Marker.
  - Only a college's manager and the super admin see and change them.
  - A person's own tick wins over what their role gets in "Who can use what".
  - A room switched off for the whole college is greyed out and can't be
    given to anyone.
  - Hand-in links and Second Marker still have to be switched on by the
    department as well.
  - College managers always have everything in their college.

WHAT'S NEW IN v106

CLASS LISTS FROM A CSV FILE (v106)
  Learners, Add learners: "Or bring in a class list as a CSV file".
  - Three columns: First Name, Last Name, Student Ref/ID No. A template can be
    downloaded from the same place.
  - Files from other systems work too: headings like Forename, Surname,
    Student ID or ULN are recognised in any order, commas or semicolons, and
    other columns (email and so on) are ignored. With no headings, the columns
    are taken as first name, last name, reference.
  - The first rows are shown to check before anything is added.
  - Someone already in the group (same reference, or same name) is brought up
    to date, not added twice. A group holds up to 80.
  - Each learner keeps all three details. The reference shows beside the name
    in the grid and is in the download.
  - They are kept in the teacher's own space with the class list, can be seen
    by the leaders of the department, and are never sent to the AI.
  - Anonymous learners: where the college has that switched on, only the
    reference number is kept. The server removes first and last names
    whoever sends them, and a row with no usable reference is left out.

NO CHANGES TO config.php
None of this needs config.php edited. Everything new is set in Settings.


WHAT'S NEW IN v107

A PROFILE FOR EACH LEARNER (v107)
  Learners: press a learner's name.
  - One page for that learner: every unit they've been marked on, how many
    criteria are achieved, each criterion's decision, hand-in and feedback
    dates, any resubmission (and whether it's overdue), and whether their work
    was in an internal verification sample.
  - Files handed in through a hand-in link under their name or reference are
    listed there for the 30 days they're kept.
  - Edit details puts a name or reference right. Your notes is a private note
    only you can see: it is never sent to the AI or to your department.
  - Download gives the profile as a spreadsheet, a row for each criterion.
  - The same learner in two of your classes is joined up by their reference
    number. Names alone are never used to join people up.
  - Leaders can open the same page from Learners, Whole department. They can
    look but not change anything, and they never see a teacher's notes.
  - Anonymous learners: where the college has that switched on, a profile is
    the learner number and nothing else. No name, no notes, no editing. The
    server also now removes first names, last names and notes from lists
    saved before the switch whenever it sends them to anyone.

CLASSES AND CAMPUSES (v107)
  A group is a real class in the college. Each class can now be given its
  campus (Learners, beside Group). It shows on the learner's profile, in the
  download and beside the class in Whole department.

LESSONS COME WITH SOMEONE WHO JOINS (v107)
  Settings, Your college, Asked to join: "Bring their lessons" is ticked.
  - Approve, and a copy of every lesson that person wrote in their old
    workspace goes into the department you chose, as a draft in their name,
    for its leaders to sign off like any other.
  - Only lessons they wrote come. Old share links, quiz links and results
    stay behind, and the originals are left where they were.
  - Untick the box to bring nothing.

SCHOOL LISTS FOR SCOTLAND, WALES AND NORTHERN IRELAND (v107)
  Settings, Site setup, Institution directory.
  - Each government gives its list a new file name every time, so the site
    opens the page the list is published on and takes the newest file:
      Scotland           gov.scot, School contact details (Excel)
      Wales              gov.wales, Address list of schools (OpenDocument)
      Northern Ireland   education-ni.gov.uk, School level data (several Excel
                         workbooks, read into one list)
  - Press Read it in now beside each. With Refresh these every week ticked,
    the daily scheduled job keeps them fresh, like the England list.
  - OpenDocument (.ods) files can now be read, as well as CSV and Excel, and
    every sheet in a workbook that has a name column is read, not only the
    first. Sheets called Notes, Contents or Closed are skipped.
  - These three were built against the pages as they are today and tested on
    copies. If one won't read in on your server, open its page, download the
    file and add it under Another list.

THE SITE UPDATES ITSELF (v107)
  Settings, Site setup, Updates. This is the last zip you need to upload.
  - Check for updates looks at the release on GitHub. Update installs it.
  - Update automatically: the daily scheduled job (the same one that sends
    notifications) takes a newer release when there is one.
  - Optional: the repository has a small workflow that tells the site the
    moment a release is pushed. Copy the address shown under "Update the
    moment a release is pushed" into a GitHub secret called
    CRAFTICULUM_DEPLOY_URL (repository, Settings, Secrets and variables,
    Actions).
  - Each release is downloaded over https, checked and unpacked beside the
    site before anything is replaced. config.php, php.ini and everything in
    the data folder are never written to, whatever the release contains. A
    release with a file path that points outside the site is refused whole.
    It never goes back to an older version.
  - If the server won't let the site change its own files, the section says
    so and you carry on uploading the zip as before.
  - New files: update.php, update-lib.php, version.json.

ALL USERS, FOR THE SUPER ADMIN (v107)
  Settings, People, All users.
  - Every account on the site in one list, whichever college it's in, with
    who is waiting for approval and whose email isn't confirmed. Search by
    name, email or college, or show only those waiting.
  - Open a person to: confirm their email address for them, approve them
    into any college and department (as a teacher, head of department or
    college manager), move someone who's already in to another college
    (their lessons can come with them), send a link to reset or choose a
    password (the link is shown too, in case the email doesn't arrive), or
    remove the account. Removing takes two presses.
  - Someone who manages a college that has other staff or a paid plan can't
    be moved out from under it. Super admins are set in config.php and can't
    be changed here.

SIGNING UP: PICK YOUR COLLEGE, AND NO SECOND COPIES (v107)
  - On both sign-up forms, a college or school name that's typed has to be
    chosen from the list that appears. If it isn't there, "It's not in the
    list" lets the typed name through. Leaving the box empty is still fine
    for someone working on their own.
  - If the place chosen is already on the site, the form turns into a
    request to join it and says so. Its manager approves it as usual.
  - The server checks as well, so it can't be got round: a new workspace
    with the same name as a college that's already here is not made. The
    person becomes a request to join that college instead. "Hilltop",
    "Hilltop college" and "The Hilltop College" all count as the same name.
    A college set up properly is chosen before a workspace someone started
    for themselves.
  - Signing up with Google or Microsoft has no college box, so it isn't
    covered by this. Tidy those up from All users.
  - Workspaces that are already doubled up are left alone. Move the person
    across from All users, then switch the spare one off under Colleges.

NO CHANGES TO config.php
None of this needs config.php edited. Everything new is set in Settings.
Two optional lines exist if you ever need them:
  const UPDATE_REPO = 'owner/repository';   // take updates from another repository
  const UPDATE_OFF = true;                  // switch updating off altogether


WHAT'S NEW IN v108

THE MENU ON A PHONE (v108)
  On a phone or small tablet the top bar used to hold every menu icon, and
  they ran over the logo. Now it holds three things: the logo, Search and a
  Menu button.
  - Menu opens the whole menu full screen, the same as on a computer: every
    section named (Curriculum and lessons, Learners and assessment, Resource
    library, AI tools, Tools), every place with its name beside its icon, and
    your account at the bottom.
  - Choosing somewhere takes you there and closes the menu. Close, or Escape
    on a keyboard, closes it too. The page you're on is highlighted.
  - Anything switched off for you (a room, for instance) stays out of it.
  - The bar along the bottom is unchanged.
  - Nothing changes on a computer.

PROFILE DETAILS ON THE DASHBOARD (v108)
  The row above the lesson box (role, subjects, stage, setting, country,
  institution, Change) is hidden for now. The profile itself is still in
  Settings, You, and still sets what lessons start from.

WHAT'S NEW IN v109

A PLAINER LOOK (v109)
  The whole app, the sign-in page and the public pages.
  - Corners: buttons, boxes, chips, tags and menus are squarer. Pill-shaped
    buttons and chips are gone. Buttons are 6px, cards 8px, tags 4px.
    Round things that should be round (photos, initials, status dots) stay
    round.
  - Colour: one solid colour where there were purple-to-pink gradients. The
    sign-in panel, the front page banner, Crafti's chat bubbles and Send
    button, and the lesson box's arrow are all flat now. Gradient lettering
    is plain lettering.
  - No coloured glows under buttons, and softer, smaller shadows. Edges are
    drawn with a thin line instead.
  - Nothing is tilted or floating on the sign-in and front pages.
  - Clearer focus: a plain 2px outline on buttons and a thin ring on boxes
    when you tab to them.
  - A college's own colour still runs through all of it.
  - The board and the slides are unchanged.

WHAT'S NEW IN v110

THE COLOURS ARE BACK (v110)
  v109 swapped the gradients for flat colour. That has been undone: the
  purple gradients, gradient lettering, glows under the main buttons and the
  tilted card on the sign-in and front pages are all as they were in v108.
  What stays from v109 is the shape: squarer buttons, boxes, chips, tags and
  menus (no pills), and the plain outline when you tab to a button.

WHAT'S NEW IN v111

STAFF PROFILES (v111)
  Settings, People, Team: press a person's name. Or Settings, You, Your
  staff profile, for your own. The super admin can open one from All users.
  - One page per member of staff: role, department, when they last signed
    in, what they teach (from their education profile), their classes, the
    lessons they've written and whether each is in the library or waiting
    for sign-off, and internal verification both ways: their marking checked
    by others, and the checks they've done.
  - Classes are numbers only: learners, units marked, criteria achieved,
    resubmissions open and overdue. Learners are never named, and the server
    doesn't send their names for this page at all.
  - Who can open whose: anyone their own, a head of department their
    department's, a college manager anyone in the college, the super admin
    anyone.

GOOGLE, MICROSOFT AND COLLEGE SIGN-IN ASK WHERE YOU WORK (v111)
  Someone new who signs in that way used to get a new space made for them
  on the spot. Now, when the institution directory is loaded, they're asked
  "Where do you work?" first, with the same pick-from-the-list box as the
  sign-up form.
  - Their college is already here: they become a request to join it.
  - It isn't, and plans are on: they get their own space, named after it.
  - It isn't, and plans are off: they wait to be approved, as before.
  - If they close the page, they're asked again next time they sign in.
  - With no directory loaded there's nothing to pick from, so this step is
    skipped and it works as it did.

A COLLEGE CAN'T BE SET UP TWICE (v111)
  Every way in is now checked, in this order:
    1. the institution they picked from the list
    2. the name, however it's spelt ("Hilltop", "Hilltop College" and "The
       Hilltop College" are one name)
    3. their email address: if it ends the same way as a college manager's
       (name@hilltop.ac.uk), they belong to that college. Free addresses
       such as gmail, outlook and icloud are ignored for this.
  A match is always a request to join, never a new space. This covers the
  sign-up form, Google, Microsoft and the college sign-in.
  - The super admin can't add a college, or rename one, to a name that's
    already here. The message says which college it clashes with.
  - What this can't catch: someone with a personal email address who leaves
    the box empty, or says their college isn't in the list. The next section
    is for those.

COLLEGES SET UP TWICE: FIND AND MERGE (v111)
  Settings, People, Colleges set up twice. Super admin only.
  - Lists colleges that share a name, or whose managers' email addresses
    end the same way, and suggests which to keep (the one set up properly,
    or the one with most people).
  - Merge takes two presses. Everyone moves across as a teacher into the
    department you choose, with their own class lists and settings. Every
    lesson in the old college is copied in as a draft for sign-off. People
    waiting to join the old one now wait to join the one you kept.
  - The college that's folded in is switched off, not deleted, so nothing
    is lost and it can be looked at later.
  - A college that pays by card has to cancel its plan first.
  - "Merge any two colleges" does the same for a pair the list didn't spot.

LEARNER PROFILES: TARGETS AND A PRINTED REPORT (v111)
  - Target grade and Attendance % on each learner's profile. They're yours
    to keep up to date, and they're in the download.
  - Print report opens a plain one-page report in a new tab and brings up
    the print box: who, class and campus, target, attendance, each unit
    with its criteria and your decision on each, hand-in and feedback dates,
    and places for teacher and learner to sign. With learners anonymous it
    carries the learner number only.

THE GUIDE (v111)
  The quick guide has a new part, Profiles and admin: a page for each
  learner, your staff profile and, for the super admin, All users, Colleges
  set up twice and Updates.

WHAT'S NEW IN v112

SIGN IN AND REGISTER IN A POP-UP (v112)
  On the front page and the other public pages, Sign in (and "Sign in or
  register", and the plan buttons) used to send people to a separate sign-in
  page. Now they open a pop-up over the page they're on.
  - It's the same sign-in and the same journey: Sign in and Register tabs,
    Google, Microsoft, the college sign-in, email and password, the emailed
    link, forgotten password, pick-your-college, and the "Where do you
    work?" step. Nothing was rebuilt, so nothing behaves differently.
  - Close it with the cross, Escape or a click outside, and you're back on
    the page exactly where you were.
  - Once signed in, you go straight into the app.
  - On a phone the pop-up fills the screen.
  - Microsoft and the college sign-in have to leave for their own page, as
    they always did. They come back into the app.
  - If the pop-up can't load, or scripts are off, the button goes to the
    sign-in page as before. Links in emails still open the sign-in page.

THE DEMO STARTS EMPTY AND FOLLOWS THE VISITOR (v112)
  "Try the demo" used to open with the site's own subjects in it (on the
  original site that meant Esports and IT, with their units and criteria)
  and an example lesson.
  - Now it comes with nothing built in: no subjects, units, criteria,
    plans, forms or example lesson, and an empty library.
  - The first thing a visitor sees is "Start with what you teach". The
    profile steps open, they give their subject, stage and setting, and the
    demo is then about that: the subjects in the lesson box, the suggested
    lessons, the wording under the box, planning, results and what the AI
    writes for.
  - Each subject gets one open topic, "Any topic", with no criteria, so a
    lesson is built from what they type.
  - The Lessons page says "No lessons yet" until they build one. (The quick
    guide still shows its sample lesson while it's running.)
  - What a visitor fills in stays in their own browser. The demo is one
    shared space, so their profile and subjects are never saved to the
    server, and the next visitor starts empty. Lessons built in the demo
    are still shared until it's emptied each night, as before.
  - New workspaces already started empty. This brings the demo in line.

WHAT'S NEW IN v113

THE SIGN-IN POP-UP IN DARK MODE (v113)
  On a phone or computer set to dark mode, the form inside the pop-up went
  dark but the pop-up around it stayed white, so labels and boxes clashed.
  The pop-up now takes its background and text colour from the form inside
  it, so the two always match, light or dark.

WHAT'S NEW IN v114

A ROADMAP PEOPLE VOTE ON (v114)
  A new page, Roadmap, in the menu above Settings.
  - What's coming over the next 18 months, by quarter from January 2027 to
    June 2028, with each item marked as an AI tool, a feature, an AI
    feature, or something Crafticulum will work with.
  - Everyone with an account can vote for as many things as they like, and
    take a vote back. "Most wanted" puts the list in vote order.
  - "Something missing?" at the bottom sends an idea to the super admin,
    with the person's name and college. Five a day each.
  - On the front page, a short "A roadmap its users vote on" section shows
    some of what's planned. The full list and voting are for people with an
    account.
  - The demo can see the roadmap but can't vote.
  - Votes are kept against a scrambled form of the account, never the email
    address, in data/_roadmap.json.

  What's on it
  - The 24 AI tools that aren't in the first five (see below).
  - Google Classroom, Microsoft Teams, Discord (more than the lesson links it
    posts today), your calendar and timetable, your college's records system,
    and Moodle and Canvas.
  - Video lessons from your slides, presented by Crafti.
  - Quizzes that bring old topics back round, Second Marker for photos and
    handwriting, spoken feedback and read aloud, lessons in other languages.
  - Cover lesson packs, seating plans and groups, a page for each learner to
    sign in to, homework set and marked online, progress reports for parents
    and carers, a dashboard for leaders, a register in the app,
    observations and learning walks, swapping lessons between colleges,
    phone apps, teaching without the internet, and a CPD record.

  For the super admin
  - "Manage the roadmap" on the Roadmap page: move anything to a different
    quarter, release something (it's marked Out now; an AI tool appears on
    the AI Tools page for everyone straight away), take it back, add your
    own items, and read and clear the ideas people send.
  - "Switch every AI tool on now" turns all 32 back on in one go.

FIVE AI TOOLS TO START WITH (v114)
  The AI Tools page now has five tools, the jobs teachers do most:
    Worksheet, Quiz, Editable PowerPoint, Student report, and Differentiate
    a resource.
  The lesson planner, scheme of learning and curriculum planner are still
  there too, as they were, because they're the core of the app.
  - The other 24 are on the roadmap. Under the tools, a box says how many
    are coming and links to the roadmap to vote.
  - Nothing has been deleted. Anything someone saved from a tool that's now
    on the roadmap is still in their library and still opens.
  - The front page, the quick guide and the home page no longer say "32
    tools".

"INSTITUTION" INSTEAD OF "COLLEGE" (v114)
  Inside the app (settings, team, the super admin pages, messages and the
  emails the site sends), "college" now reads "institution": institution
  manager, Your institution, Institutions, Institutions set up twice, and
  so on. "The college year" reads "the academic year".
  - Left as they were: the public pages, the sign-in and register form
    (which already says "college or school"), and names such as Hilltop
    College.
  - Only words people read have changed. Nothing stored has been renamed,
    so no data needs moving.

WHAT'S NEW IN v115

THE ROADMAP, REDESIGNED (v115)
  - A banner at the top with how many things are planned, how many are out
    and how many votes you've given.
  - "Most wanted right now": the top five by votes, with a bar for each.
  - Filters for Everything, AI tools, Features, AI and Works with, each
    with a count.
  - A timeline across the page from Now to June 2028. Press a quarter to
    jump to it.
  - Each quarter has its own marker down the side, and each item a colour
    for its kind (indigo AI tools, purple AI, blue Works with, amber
    Features). Things you've voted for are tinted.
  - "Most wanted" ranks everything, numbered.
  - Works on a phone (the timeline scrolls sideways) and in dark mode.

THE LAST OF "COLLEGE" IN THE SUPER ADMIN PAGES (v115)
  The site overview's tiles and columns, "paying institutions", "Deleted
  institutions", "This is the only institution", "Institution with no name
  yet" and a few more messages now say institution too.

WHAT'S NEW IN v116

VIDEO LESSONS, EARLY PREVIEW (v116)
  - Every lesson has a Video lesson button next to Show on board (or press
    V). Crafti talks the class through it slide by slide: the title and
    objectives, the starter, each part of the teaching, each task, the quiz
    and a wrap-up.
  - Captions show what's being said. It pauses on the starter and on each
    task so learners can have a go, then carries on when you press play.
    Quiz questions get a few seconds' thinking time before the answer.
  - Speed, voice, captions and "Pause at tasks" can be changed, and are
    remembered. Keyboard: space to play or pause, arrows for slides,
    Escape to close.
  - The voice is the device's own speech, so nothing is recorded or sent
    anywhere and it costs nothing. If a device has no voice, the captions
    move on by themselves.
  - "Write a proper script with Crafticulum AI" writes narration that
    explains things rather than reading the bullets. It's saved on the
    lesson, and used on the student page too.
  - The student page has a "Watch the video lesson" button. Learners get
    the same video without the quiz, so no answers are given away.
  - The roadmap shows video lessons as an early preview, out now.

NEW WORKSPACES START EMPTY (v116)
  A new workspace no longer opens on a ready-made sample lesson. Lessons
  says "No lessons yet" with a button to describe one. The guide still
  borrows the sample to show where things are.

STAFF PROFILES, WITH MUCH MORE IN THEM (v116)
  - A banner with your photo (from your Google or Microsoft sign-in) or
    initials, your job title and pronouns, your role, department and
    institution, and how to reach you: email, phone, room and a link.
  - "Edit profile" on your own: job title, about you, specialisms,
    campuses, qualifications you hold (PGCE, TAQA, IQA and so on, or add
    your own), phone, room, when you're around, a "right now" status and a
    link such as LinkedIn.
  - Tabs: Overview, Classes, Lessons (with a search), Verification and CPD.
  - Overview adds your lessons written over the last six months as a small
    chart, the units you write for, and when you joined.
  - CPD: log training, standardisation, industry days and the like, with
    hours. It totals this academic year's hours, handy for appraisals.
  - A bar shows how much of your profile is filled in, until it's done.
  - Who can open a profile hasn't changed. Only you can edit yours.
  - Kept in data/u/<your folder>/_about.json.

CRAFTI LIVE CHAT ON EVERY PAGE (v116)
  - A Crafti button in the bottom right of every page opens a small chat
    window. On a phone it opens full screen, and sits above the bottom
    menu.
  - Crafti knows which page you're on and, on a lesson, which lesson you've
    got open, so "help me with this lesson" just works. Untick "Let Crafti
    see this page" to stop that.
  - Suggestions change with the page. Enter sends, Shift+Enter is a new
    line, Escape closes.
  - The chats are the same ones as on the Crafti page. The expand button
    carries the chat over there.
  - It only shows for people who have Crafti switched on.

PHONE FIX (v116)
  Long labels on a lesson (like a long "Follows:" line) now wrap, instead
  of making the whole page wider than the screen.

WHAT'S NEW IN v117

DEPARTMENTS IN THE LESSON PLANNER (v117)
  - The planner's first row is now the institution's departments. Pick
    one and you get its subjects, levels and units, from the
    specifications that department uploaded.
  - Teachers see their own department. Institution managers and super
    admins see every department in the institution.
  - Esports and IT belong to the original department only. New
    departments no longer get them built in.
  - A department with no subjects yet says so, and can't build until its
    specification is added.

ADMIN, FOR SUPER ADMINS (v117)
  - Admin in the menu (and at /admin) opens on To do: people waiting for
    approval, unconfirmed emails, messages, errors, an update ready and
    institutions set up twice, each with a button that goes straight
    there. The waiting count sits on Admin now.
  - The rest is grouped as Institutions, People, Money, Features and
    System.
  - Settings is just your own preferences and the institution you're
    working inside.

OUR TEAM (v117)
  - A page listing everyone in your institution as cards: photo, job
    title, department, campus, specialisms, qualifications held, and
    whether they're in today. Search and filter by department, campus or
    qualification. A name opens their profile.
  - Colleagues see who someone is and how to reach them, not their
    classes, lessons or CPD. Heads of department and managers still see
    the full profile.
  - Managers and heads of department see CPD hours for this academic
    year, and can download them as a spreadsheet (CSV).

PROFILE PHOTOS AND ONE PROFILE (v117)
  - Add your own photo from Edit profile. It's cropped to a square and
    shrunk before it's sent, and only people signed in can see it. It
    replaces your Google picture everywhere.
  - One Edit profile form now covers your name, title, gender, job title,
    pronouns and phone too. Settings shows what you teach and your staff
    profile inside Your profile, instead of as two more sections.
  - Photos are kept in data/u/<your folder>/photo.jpg and served by
    photo.php (new file).

CRAFTI DOES THINGS (v117)
  - Under each of Crafti's replies: "Build a lesson from this" (for
    people who can build) fills in the dashboard with the lesson and the
    subject ready to check, so one press builds it. "Make it a resource"
    turns the reply into a resource you can edit, save and download.
  - The same buttons are on the Crafti page.

VIDEO LESSONS AS MP4 (v117)
  - The video lesson player has an MP4 button. It makes a 1280 x 720
    video of the slides with the captions burned in, in the browser,
    much faster than playing it through. Nothing is uploaded.
  - It has no sound, because the device's voice can't be recorded.
    Learners read along. Post it on Teams or Google Classroom like any
    video.
  - Works in Chrome, Edge and Safari.

FRONT PAGE WORDING (v117)
  The last "your college" lines on the front page now say "school or
  college" or "institution".

WHAT'S NEW IN v118

SIMPLER, FROM THE AUDIT (v118)
  Nothing has been taken away. Things that moved are one click further
  in, and the search box finds any of them by name.

  The side menu
  - Three headings: Teaching, Learners and AI.
  - The Library sits with This week, Lessons and Planning. My created
    lessons is a button at the top of the Library (and still My lessons
    on a phone).
  - Show on board and Display are no longer in the menu. They're on every
    lesson (and the B key), and in Settings.
  - At the bottom: Our team, Admin (super admins), Settings, Help and
    feedback, and What's coming (the roadmap, same page and address).

  The dashboard
  - One set-up list at a time: the institution's first, then yours once
    that's done or put away.
  - "Make Crafticulum yours" waits until the set-up list is out of the
    way, and no link appears twice.

  A lesson
  - Four buttons: Edit, Teach, Share and More.
  - Teach has Show on board, Video lesson and Go live on the board.
  - Share now includes what was under Export (PowerPoint, the lesson plan
    form, task sheets, the cover pack and the copy buttons), under
    Download.
  - More has Next lesson, and "Change or regenerate", which opens the
    steer box, Regenerate lesson, Undo and History.

  Settings
  - You: your account, your profile, and Preferences (display, install
    as an app and notifications in one card).
  - Teaching is now two tabs: Department (subjects, rules, Second
    Marker, documents, learner needs, deleted lessons) and Calendar and
    groups (term dates, groups, assignment dates, Discord).
  - Term dates show as a short summary (when term starts, the holidays)
    until you press Edit term dates.
  - The Everything tab has gone. Search finds any setting by name.
  - Teachers no longer see the automatic backup and student data cards,
    which only say what their manager has set.

FIXED (v118)
  For a super admin outside every institution, Settings in the menu kept
  going back to Admin, and the roadmap wouldn't open. Settings now opens
  your own settings, Admin opens Admin, and What's coming opens the
  roadmap with its release controls.

WHAT'S NEW IN v119

PHONE LAYOUT FIX (v119)
  v118's app page was packaged from a half-finished build, so it went out
  without the tag that tells phones to use the phone layout. Phones showed
  the app zoomed out, like a desktop. v119 is v118 built properly, and
  the packaging step now refuses to make a zip from an unfinished build.

GO BACK TO THE LAST VERSION (v119)
  - Admin, System, Updates has "Go back to version N". Press it twice
    and the site is back on the version it ran before the last update.
  - Automatic: every update now keeps a copy of the files it replaces
    (data/_update/prev), so going back takes a few seconds and needs
    nothing from GitHub. Going back can itself be undone.
  - GitHub: if there's no copy on the server (the update was uploaded
    as a zip), the release before the one running is fetched from the
    repository's history instead.
  - After going back, automatic updates wait for a release newer than
    the one you left, so the site doesn't jump straight forward again.
    "Update to version" still works by hand whenever you want.
  - config.php and the data folder are never touched, as with updates.
  - The first Go back after installing v119 by the updater returns to
    v118, which has the phone layout fault, so there's no reason to use
    it until the next release.

WHAT'S NEW IN v120

CRAFTI TO A QUIZ OR WORKSHEET (v120)
  Under each of Crafti's replies, in the chat window and on the Crafti
  page: "Quiz from this" and "Worksheet from this". They open the tool
  with the topic and Crafti's ideas already filled in, so one press
  makes it.

THE LIBRARY HAS THE BIN (v120)
  - Resource library is now just "Library".
  - Heads of department and managers get a Bin filter there: deleted
    lessons, with how long is left, and Restore.
  - The "Deleted lessons" card has gone from Settings.

OVERVIEW FOR MANAGERS (v120)
  - Our team has an Overview tab for institution managers (every
    department) and heads of department (their own).
  - Tiles: lessons built this month, waiting for sign-off, staff not
    signed in for 30 days, resubmissions overdue, verification actions
    open, and staff under the CPD target.
  - Lessons built over the last six months as a chart, a table by
    department, and a table by person you can sort (last signed in,
    lessons this year, CPD hours, overdue). A name opens the profile.
  - The CPD target (hours each person should log a year) is set by the
    institution's manager. It starts at 30. 0 switches it off.
  - Numbers only. Learners are never named.
  - The target is kept in data/_mgr.json.

WHAT'S NEW IN v121

MORE THAN ONE DEPARTMENT (v121)
  - A person can now belong to more than one department in their
    institution. In Admin (or Settings), People, Team: under someone's
    department, "Add to another department", tick it and pick their
    role there (teacher or head of department).
  - They switch from the department row in the lesson planner, or from
    Settings, You. Switching loads that department's subjects, lessons,
    plans and classes, and what they make is saved in it.
  - Picking a department in the planner now really works in that
    department, for managers and super admins too.

SPLIT A DEPARTMENT BY SUBJECT (v121)
  For a department that teaches several subjects and should be two (the
  original one on this site teaches Esports and IT).
  - Super admin only. Work inside the institution, then Admin, People,
    Departments, "Split by subject".
  - Tick the subjects that move, name the new department and what this
    one becomes, and press Preview. It says exactly what will move and
    changes nothing.
  - "Split the department" (two presses) then: makes the new department;
    gives each department its own subjects as ordinary specifications
    (nothing is "built in" any more); moves the shared lessons and plans
    for the moving subjects; moves each person's own classes where all
    the marks are in those subjects (a class with marks in both stays);
    copies term dates, rules and documents; and puts everyone in both
    departments with the role they have.
  - A copy of everything touched is kept in data/_split, and "Undo the
    last split" puts it all back, including anything made in the new
    department since.
  - Take a backup first all the same (Admin, System, Backup).

CRAFTI'S CHAT (v121)
  - The button is Crafti on its own, without the purple circle, and sits
    a little higher.
  - Text in the chat can be selected and copied, and the chat isn't
    redrawn underneath a selection.
  - The message box no longer makes an iPhone zoom in when you tap it.
  - It moves out of the way of the build button on the dashboard.

STAR A REPLY ONTO A BOARD (v121)
  - Under each of Crafti's replies there's a Board button with a star.
    Press it and the reply goes onto a blank board, and is copied too.
    "Open the board" then appears beside it.
  - The board is full screen with big type for the class. You can type
    on it, make the text bigger or smaller, copy it or clear it. Escape
    closes it. It keeps the last 12 things you starred, on this device.
  - The same buttons are on the Crafti page.

SUPER ADMIN (v121)
  - Working inside an institution shows a clearer bar: a yellow "Super
    admin" badge, which institution you're inside, and a white Leave
    button. Leave is in the side menu too, so it's always to hand.
  - The super admin belongs to no institution. Stepping inside one no
    longer writes that department to the super admin's own account, it
    only lasts for the session, and any old link is removed the next
    time you step in or leave. Their own profile says "Whole site".
  - The roadmap's "when" menus now stay open while you choose. The page
    was redrawing underneath them and closing them.

VERSION 122: SHARE QUIZ RESULTS WITH THE CLASS
- Quiz room (the quiz with initials): when you end the quiz, the report has a new
  "Show the class" button. It puts the results on the board with made-up names
  (Brave Otter, Rapid Lynx) in place of initials: how many answered, the class
  percentage, the average, how the scores spread, a leaderboard, the questions
  most got wrong with the most common slip and the right answer, and tips for
  getting them all right next time.
- Each learner sees their own made-up name on their phone under their score, so
  they can find themselves on the board and nobody else can.
- "Add tips from Crafticulum AI" writes the tips from the class totals. It is
  never sent initials. Without it the tips come from the quiz's own explanations.
- Hands up on the lesson board: the results slide now shows every question that
  caught the class out (up to three), the common slip, and the tips.
- Your own report, with initials, is unchanged and stays on your screen only.

VERSION 123: OPEN LESSON, ON ITS OWN
- Open lesson in Planning (and opening one from the Library) now shows the
  lesson full width, without the New lesson panel beside it.
- A small bar above it has Back to Planning, and New lesson to bring the panel
  back. Lessons in the menu and Create a lesson work as before.

VERSION 124: THE SUPER ADMIN STANDS OUTSIDE, ALWAYS
- The super admin bar now stays at the top of the screen while you scroll, on
  phones too, with Leave always in reach.
- It also shows on a site with only one institution. Before, with one
  institution the super admin was held inside it with no way out.
- Press Leave once and the site remembers: from then on the super admin signs
  in outside every institution, on Admin, and opens one to work inside it.
- Inside an institution the super admin no longer reads "Your institution"
  anywhere, in Settings or on the dashboard. It says "This institution". The
  institution's own manager still sees "Your institution".
- Nothing is moved or deleted. Lessons, PowerPoints, guides and plans stay
  exactly where they are, and the super admin still sees and edits everything
  after opening the institution.

VERSION 125: CRAFTICULUM'S OWN TEMPLATES, AND A NEUTRAL AUTHOR
- New default PowerPoint template (assets/tpl-plain.pptx) in Crafticulum's
  indigo to violet and its font, Plus Jakarta Sans: a title slide, learning
  objectives, starter, and a content slide that repeats for the plan, teaching,
  tasks, quiz and plenary. Used by every institution that hasn't uploaded its
  own template, and by the starter lesson. Where the font isn't installed,
  PowerPoint and Word use their own sans serif.
- The default lesson plan document matches: Crafticulum wordmark, indigo rule,
  tinted label column. Worksheets use the same indigo.
- An institution's own uploaded templates still win, and the original
  department's built-in template is unchanged.
- Team and Departments have moved to Settings, Institution, for the
  institution's manager, heads of department and the super admin working
  inside it. The People tab has gone. Admin, People is now just everyone on
  the whole site.
- Lessons the super admin built or edited read "By Crafticulum" for everyone,
  and new ones are saved that way. Nothing stored is rewritten until a lesson
  is next saved.

VERSION 126: REPORT A PROBLEM, AND ONE POWERPOINT TEMPLATE FOR EVERYONE
- The Crafti chat (bottom right) has Report a problem: a flag button in its
  header and a link under the message box. One box to say what happened, or
  to send an idea. The page they were on, their browser, screen size and the
  version go with it, plus their name and email so you can reply.
- Reports land in Admin, To do with your other messages, and are emailed to
  the site's contact address.
- The original department's PowerPoint downloads now use Crafticulum's own
  template, like every other department. An uploaded template still wins.
  The quiz QR slide isn't on this template yet. To go back, set CC_OWN_DECK
  to false.

VERSION 127: EASIER TO USE, AND THE ORIGINAL POWERPOINT BACK
- Departments (Settings, Institution) are tiles. Each shows who leads it, how
  many staff, the subjects inside and the awarding body. Pick a tile to rename
  it, see its subjects in a table, work in it or open its subjects.
- Subjects has its own tab in Settings: add a subject from its specification
  or by hand, set where you teach, and manage the briefs. It is still the one
  place units and criteria come from, so a change there shows in Lessons,
  Planning, Second Marker, AI Tools and Results.
- A lesson's tabs are bigger buttons that wrap on to a second row, so none
  hide off the side. On a phone they sit in a neat grid of three. Left and
  right arrow keys move between them.
- Settings' tabs and the AI Tools filters wrap the same way.
- Planning and This week: PowerPoint, Worksheet and the other small links
  under a lesson are proper buttons.
- Phones: bigger buttons and boxes, 16px text in boxes so the iPhone doesn't
  zoom in when you tap one, and the big Show on board bar only shows on a
  lesson (it was covering Planning and This week).
- The original department's PowerPoint downloads use its own built-in
  template again, with the quiz QR slide. Every other department still gets
  Crafticulum's template. Report a problem is unchanged.

VERSION 128: WHICH DEPARTMENT, AND THE BUILT-IN SUBJECTS
- The Subjects and Department pages in Settings say which department they are
  for, with a drop-down of the institution's departments. Pick another and you
  move into it and stay on the same page. Managers and the super admin see
  every department; other people see it only if they are in more than one.
- The drop-down ends with Add a department, and Departments has an Add a
  department tile. Both go to the add form on the Institution page.
- Subjects lists the built-in subjects (Esports and IT, Pearson BTEC) next to
  the ones you added, marked Built in. Each subject you added shows its
  awarding body, or says it isn't set.

VERSION 129: ONE DEPARTMENT SWITCHER, AND RENAME A SUBJECT
- Anyone in more than one department has a Department drop-down under the
  logo in the menu, on every page (on a phone it is in the Menu). Managers
  and the super admin get every department in the institution.
- Pick a department and you stay on the page you were on: Planning, Learners,
  Results, Second Marker, AI Tools or Settings, now showing that department's
  subjects, plans, classes and marks. Each department's work stays its own.
- People in one department see no switcher.
- Settings, Subjects: every subject you added has Rename. It suggests a name
  that says what the course is, for example "IT" with the level "Level 2 TLM
  Cyber Security" becomes "IT (TLM Cyber Security)" at "Level 2". Change
  either box to whatever you want.
- The first press only checks and tells you how many lessons, plans and class
  lists will change. The second press renames it everywhere.
- A copy of every file it touches is kept first in data/_rename, and "Undo it"
  under the list puts the last rename back. Undoing also loses any edits made
  to those lessons and plans after the rename.
- Heads of department, institution managers and the super admin can rename.
  Built-in subjects can't be renamed.
- Take a backup before the first rename on the live site.

VERSION 130: THE TEAM LIVES WITH THE DEPARTMENT, AND PROFILE PAGES
- Our team has left the menu. Settings, Department now opens with Our team:
  the people in the department you're in, as photo tiles. "Everyone in the
  institution" opens the full page with search and filters.
- A profile is a page of its own, not a pop-up, at
  /<institution>/team/<name>, for example /hilltop/team/samlee. The
  address is made from the institution's name and the person's name.
- Signed-in colleagues from the same institution see the profile with how to
  reach the person. Anyone else sees "This profile is private" unless that
  person has switched on "Make my profile public" (Settings, Department, Our
  team). A public profile shows name, photo, role, departments, what they
  teach and qualifications. It never shows email, phone, room or status.
- Team: the department box is now a pick-several list. Tick every department
  someone works in, choose which is their main one, and set their role in
  the others. This replaces the "Also in" link.
- Admin, Institutions: "Institutions set up twice" has been removed.
- NEW FILE: profile.php, and two new lines in .htaccess. Upload .htaccess.

VERSION 131: A SHORTER FRONT PAGE, AND THE QUIZ CODE SLIDE
- The front page (index.html) is about a third of the length and built around
  one action. With plans on, the main button is Start free, in the menu bar,
  the top of the page and again at the bottom. With plans off it is Sign in
  or register, and Get in touch.
- It now has: the headline, three steps, what you get (with real screenshots
  of a lesson, the board and a PowerPoint slide), who it's for in three short
  cards, pricing, six questions and a closing sign-up panel.
- Gone from the front page: the long sections on where you teach, the AI
  toolkit, the roadmap, and four of the ten questions. Nothing was removed
  from the app.
- Link preview: sharing crafticulum.co.uk on WhatsApp, LinkedIn, X or Teams
  now shows the new picture (og.jpg). The page's title and
  description are shorter.
- NEW FILES in assets: shot-lesson.webp, shot-board.webp, shot-slide1.webp,
  shot-slide2.webp, and og.jpg beside index.html. pages.css and page.php
  changed.
- Crafticulum's PowerPoint template now gets the quiz code slide ("Exit quiz:
  answer on your phone") just before the questions, when a deck is downloaded
  with the quiz room or phone quiz open.

VERSION 132: THE FRONT PAGE, SHORTER AGAIN
- The front page is now five parts: the headline, How it works, What you get,
  Pricing and four questions, then the sign-up panel. Who it's for and the
  strip of facts have gone, the ticks are one line each, and there is less
  space between sections.
- Under the headline it says how to say the name: kraf-TIK-yuh-luhm, craft +
  curriculum.

VERSION 133: NO QUESTIONS ON THE FRONT PAGE
- The questions section has gone from the front page. It is now the headline,
  How it works, What you get, Pricing and the sign-up panel.

VERSION 134: FIRST LESSON, A VOICE, CLASSROOM AND TEAMS, PUBLIC PROFILES
- A first lesson with no setting up. Someone who signs up on their own now
  sees three boxes: subject, year or level, and what the lesson is about.
  One button builds the lesson. The subject is saved for them and can be
  filled in properly later, or from a specification.
- A real voice on video lessons. In Admin, System, Site setup there is a new
  section, A voice for video lessons. Add a Google Cloud Text-to-Speech API
  key and the MP4 download gets a spoken voice as well as captions. Each
  caption stays on screen for as long as it takes to say. With no key the
  MP4 is as before. Google gives a million characters a month free on these
  voices (about 80 video lessons); the ceiling in the same section stops the
  site going past it. The key stays on the server, each sentence is only
  paid for once, and only signed-in staff can use it.
  NEW FILE: speak.php. Sound is saved under data/tts.
- Post to Google Classroom or Microsoft Teams. Share, on a lesson, unit,
  quiz or test, now has two buttons under the link. They open Classroom or
  Teams with the student link and the lesson's name filled in.
- Public profiles for the whole institution. Settings, Department, Our team
  has a new switch for the institution's manager: Make everyone's profile
  public. Each person can still switch their own off. Nothing but name,
  photo, role, departments, subjects and qualifications is shown.

VERSION 135: REFUNDS IN THE FIRST 30 DAYS
- The terms of use, under Plans and payments, now say: a full refund on the
  first payment for a plan if you ask within 30 days; no part-month refunds
  after that; refunds for double charges or faults on our side; and what
  happens if an account or the site is closed. Only shown when plans are on.
  Refunds themselves are made in your Stripe dashboard.

VERSION 136: THE COMPANY BEHIND CRAFTICULUM
- The terms, the privacy notice and the foot of every public page now say
  that Crafticulum is a trading name of Next Level Labs Ltd, with its
  company number and registered office. The privacy notice also gives the
  ICO registration number.

VERSION 137: DATA PROCESSING AGREEMENT
- NEW PAGE: /dpa (dpa.html), the data processing agreement for schools,
  colleges and training providers, with the list of sub-processors. It is
  linked from the foot of every public page, the terms and the privacy
  notice. .htaccess and page.php changed to serve it.
- The privacy notice now says the site is hosted by Krystal in London,
  backups are kept for 28 days with a remote copy on Amazon S3, and closed
  accounts are deleted within 30 days.

VERSION 138: ICO NUMBER AND BACKUP LOCATION
- The privacy notice gives the ICO registration number as Z92242123, and it
  and the data processing agreement say the remote backups are in London.

VERSION 139: THE MONEY SIDE, AND THINGS FOR TEST WEEK
- Admin, Money now opens on Business: monthly recurring revenue, annual run
  rate, paying customers, what was lost this month, what is owed on
  invoices and what Crafticulum AI cost. Below that: twelve months of
  monthly revenue, what moved it this month (new, came back, upgrades,
  downgrades, lost), new accounts day by day, who is active, the plans,
  and every institution with what it pays, how it pays, when it renews,
  its people, its builds and its AI cost. It downloads as a spreadsheet.
  The figures are kept in data/_money.json from this version on.
- Invoices. An institution's manager can ask to pay by invoice in Settings,
  Plan and billing, with a purchase order number. In Admin, Money,
  Invoices you set the price, send it (it gets a number and a pay-by
  date and is emailed as a link), and mark it paid. Paid puts the
  institution on its plan for the months it covers. A month before the
  term ends they are reminded, and two weeks after it ends with nothing
  new paid the plan goes back to Free. Add your bank details under Your
  details on invoices first. NEW FILE: money.php.
- A sample lesson at /sample, linked from the front page, with a quiz
  visitors can answer. A security summary at /security for IT teams.
  NEW FILES: sample.html, sample.js, security.html. .htaccess and
  page.php changed to serve them.
- Report a problem (in Crafti) can take a screenshot, by choosing a picture
  or pasting one. It shows with the message in Admin, To do.
- Add several people at once: Settings, Institution, under Team. Paste a
  list or upload a CSV.
- What's new: after an update, people see a short list of what changed,
  once.
- When 80% of the month's builds are used a note says so on screen, with
  the way to a bigger plan for the manager. (The email already existed.)
- The voice for video lessons is picked from a list in Admin, System, and
  Play a sample checks the key works.
- A reminder email goes to someone who signed up on their own three days
  ago and has made fewer than two lessons. Once only.
- App icons (home screen, install) are now the logo itself, not the white
  version on a tile.

VERSION 140: LIBRARY, FEEDBACK, VERSIONS, OFFLINE, AND MORE OF THE MONEY SIDE
- Lesson library across the institution. In the Resource library there is
  Search every department: finished lessons from every department, found
  by title, subject, unit, criteria or teacher, most used first. Opening
  one from another department gives a copy to save. Drafts never leave
  their own department. A manager can keep the library to each department
  in Settings, Institution.
- Was this a good lesson? Asked once under every newly built lesson. If
  not, people say what was off. The answers are in Admin, Money, Business.
- Make a version for...: under any lesson, builds a second version for
  dyslexia, EAL, low literacy and the other needs. It uses one build.
- Offline. Offline copy under any lesson saves a zip with a page that
  works with no internet, the slides and the worksheet. The unit lesson
  pack carries the same page for every lesson. And the app itself now
  opens with no internet and shows the lessons and planning that were on
  the device last time; nothing saves until it is back online, and what
  is kept is wiped on sign-out.
- Admin, Money, Business: Refund or credit on every institution (refund a
  card payment in full or in part, take an amount off the next bill, or
  give a free month), the next three months expected with what needs a
  renewal, renewals coming up in 90 days, what people think of what gets
  built, and a monthly check that the newest backup opens and every file
  in it can be read.
- Give a month, get a month. A manager's link is in Settings, Plan and
  billing. When someone who came by it starts a paid plan, both get a
  month off their next card bill, automatically.
- Weekly summary. Each Monday managers of an institution with two or more
  people get lessons made, who was active and who has not been in for two
  weeks. Switch it off in Settings, Institution.
- The activity log can be searched and downloaded as a spreadsheet.
- NEW PAGES: /status (is it working, with notes you post from Admin,
  System), and five subject pages: /btec-it-lesson-plans,
  /btec-esports-lesson-plans, /t-level-digital-lesson-plans,
  /gcse-computer-science-lesson-plans, /functional-skills-lesson-plans.
  The front page has a 30 second film (assets/demo.mp4).
  NEW FILE: status.php. .htaccess and page.php changed.
- The cookies page now lists a second cookie, cc_ref, set only when someone
  arrives from an invitation link.

VERSION 141: INTEGRATIONS (EVERYTHING OFF UNTIL AN INSTITUTION SWITCHES IT ON)
- Settings, Institution has a new section, Integrations, for the
  institution's manager. Nothing in it is on by default.
- Google Classroom and Microsoft Teams: after a phone quiz the teacher can
  send each learner's score into the class gradebook. The manager puts in
  the institution's own keys (a Google OAuth client, or a Microsoft Entra
  app with its tenant) and switches it on. The section shows the redirect
  address to give Google or Microsoft and the steps to follow. Each
  teacher signs in to their own account once.
- The quiz only knows initials, so the teacher picks the class and matches
  each set of initials to a learner before sending. Initials that can
  only be one learner are matched for them. Class lists are not kept.
- Keys and sign-in tokens are stored encrypted in data/_integrations and
  never sent back to a browser. Switching an integration off stops it for
  everyone at once.
- Your own links: up to four https links (Moodle, Canvas, SharePoint) that
  show as buttons when a lesson is shared.
- NEW FILES: integrate.php, quiz/marks.js.
- NOTE: this has been tested against stand-ins for Google and Microsoft,
  not the real services. Try it with one class before telling staff.

VERSION 142: TIDIER SETTINGS, FIRST STEPS, YEARLY PLANS
- Settings. Institution was one long page of fifteen sections. It is now
  three tabs: Institution (name and look, departments, team, add several
  people, who can use what, the directory), Plan and billing (plan, pay
  by invoice, give a month get a month, usage and cost) and Rules and
  integrations (integrations, weekly summary and library, house rules,
  anonymous learners, activity).
- A manager's start page has a short list: add your team, add a subject,
  build a lesson, share one with a class. Each ticks itself off, and Hide
  puts the list away.
- Yearly plans. Above the plans there is Pay monthly / Pay yearly. Yearly
  charges ten months for twelve. The Business dashboard counts a yearly
  plan as a twelfth of it each month. The front page and the terms say
  so.
- Learners: where an institution has switched on Google Classroom or
  Teams, Send marks puts each learner's number of achieved criteria for
  the chosen unit into the class. Names are matched for you and checked
  by the teacher before anything is sent.
- Phones: on a lesson the Crafti button no longer sits on top of the tabs
  (Crafti is still in the menu), and the buttons under a lesson are
  easier to tap.
- The film on the front page is longer and shows planning and the library
  as well as a lesson being built.
- Example text in the campus and room boxes no longer names real places.

VERSION 143: FROM A REPORT TO A FIX
- Admin, To do, Messages. Every message now has Send to Claude. You read
  the report and its screenshot, edit the message if it names a learner,
  say what you want done, and choose whether the screenshot goes. That
  lines it up.
- Save the pack for Claude makes one zip of everything lined up:
  reports.md (each report, where they were, their device, their role and
  what you want done) and the screenshots you allowed. Names, email
  addresses and institutions are left out. Attach the zip in your chat
  with Claude. Copy as text does the same without pictures.
- Each report then shows Sent to Claude, and Mark as fixed moves it to
  Done. Send again puts it back.
- The contact page takes an optional screenshot, like Report a problem in
  Crafti. Deleting a message now deletes its screenshot.
- Nothing is sent anywhere by the site itself: the pack is a file you
  choose to hand over.
- On a phone, the parts of a lesson (Overview, Starter, Teach and so on)
  used to be a block of eight or nine buttons that scrolled away. They
  are now one bar that stays under the header: the part you are on,
  arrows to the one before and after, and a tap on the name to see the
  whole list and jump. A new part opens at its top. Bigger screens keep
  the tabs as they were.

VERSION 144: PHONES, AND TELLING PEOPLE IT'S FIXED
- The board on a phone (from a report sent through Crafti): the nine slide
  buttons and six action buttons were five rows at the top. Now it is one
  row: back, the slide you are on (tap it for the list), forward, More
  (go live, copy, save, display, full screen) and Close. More turns red
  and says LIVE while a live lesson is on. Swiping left or right moves
  between slides. Bigger screens are unchanged.
- Planning: the buttons on a planned lesson have colour and icons. Open
  lesson is the main button, PowerPoint is orange, Worksheet blue,
  Regenerate violet and Mark taught green. On a phone they sit in a tidy
  grid with full-size tap targets.
- Lessons on a phone: swipe left or right to go to the next or last part.
- Settings and Admin on a phone: the tabs that wrapped into a block are
  now one Section button that opens the list.
- Admin, To do, Messages: when a report has been sent to Claude, there is
  now "Fixed, and email them". It sends the person one short email saying
  it is fixed, quoting what they wrote, and records that they were told.
  "Fixed, don't email" does what Mark as fixed did before.
- A colleague's invitation link (?ref=) now asks before saving its cookie.
  No thanks saves nothing. The cookies page says so.
- The Error log no longer redraws the whole of Settings when it loads, so
  a box you had opened elsewhere on the page stays open.

VERSION 145: THE GOOGLE VOICE IN THE PLAYER, AND A CHOICE OF VOICES
- Video lessons played in the app used the phone or computer's own voice
  (Daniel on an iPhone), which an iPhone mutes when the silent switch is
  on. With a Google key saved, the player now speaks with the Google
  voice, sentence by sentence, through normal audio, so it plays on an
  iPhone even on silent. Each sentence is still only paid for once.
- The voice menu in the player lists Google's voices grouped by type and
  accent: Chirp 3 HD (British, American, Australian), Studio, Neural2,
  WaveNet and Standard, each marked male or female. The device's own
  voices are still at the bottom. Each person's choice is remembered and
  their MP4 downloads use it too.
- Site setup, A voice for video lessons: the voice list has the same
  choice, and a new box, typing 1 stops staff picking their own voice.
- Only voices on the list can be asked for, and the monthly ceiling still
  counts every character, whichever voice is used.

VERSION 146: A HOW-TO FILM WITH A VOICE-OVER
- The film on the front page is new: "how to use it, in six steps". Add
  your subject, build a lesson, look through it, teach it on the board or
  as a video lesson, download it or make a version for learners who need
  support, share it, plan a unit, and find colleagues' lessons. Each step
  is labelled on screen with a caption. About a minute and a half.
- A big play button sits over the film. Tapping it starts the film and its
  voice-over together, which is what lets a phone play the sound.
- To give it a voice: Site setup, A voice for video lessons, pick a voice,
  then press Voice the film. It speaks the film's captions in that voice
  (about 1,000 characters, once) and keeps the result with your data, so
  it survives updates. Press it again to change voice; sentences already
  made cost nothing. Until it's voiced the film plays silently with its
  captions.
- If a later update brings a new film, the old voice-over stops being used
  until you press Voice the film again.

VERSION 147: THE FILM'S VOICE KEPT IN STEP, THE LOGO, AND NO COLLEGE NAMED
- The film has been made again. The logo is on the opening and closing
  cards, and every caption stays up longer so a real voice has room.
- Voice the film now fits each line to its caption. A line that would run
  into the next caption is asked for again a little faster (never more
  than about a third), so the voice can't fall behind the picture.
- On the front page the voice only runs while the picture is really
  moving, so it can't start early while the film is still loading on a
  phone, and it is no longer nudged every second.
- AFTER UPDATING: Site setup, A voice for video lessons, press Remake the
  voice-over. The old one is not used with the new film.
- Download the voice-over (next to that button) saves the finished sound
  as a file.
- Nothing people can see names a real college any more. The example
  assignment briefs use "Hilltop College", the lesson plan form is called
  Lesson plan form, the calendar's open events are just Open Event, and
  the placeholder team is Hilltop College. Institutions that use their
  own Word forms keep them; only the labels changed.

VERSION 1.48: NO COLLEGE'S OWN FORMS IN CRAFTICULUM
- The site this began on had four of its own documents built in: a
  PowerPoint master, a lesson plan form and two scheme of learning forms.
  They are no longer part of Crafticulum, the download or the code.
- That first college keeps them. The first time someone there opens the
  app after this update, the four files are moved out of assets/ into the
  data folder (data/_owntpl), where they are kept with the backups and
  can't be fetched from a public address. Only people in that college are
  given them, and their PowerPoint, lesson plan form and scheme of
  learning downloads work as before.
- Every other institution gets Crafticulum's own templates, or the ones
  it uploads in Settings, as before. They no longer download the old
  PowerPoint master in the background, so their first download is quicker.
- If those four files are ever missing, the first college's downloads
  fall back to Crafticulum's own templates and don't fail.
- UPDATE WITH "CHECK FOR UPDATES", or upload over the top as usual. Don't
  delete the assets folder first: the four files are picked up from it.
- Pricing on the front page has a switch: Pay monthly, or Pay for 12
  months. The 12 month view shows the yearly price (ten months' worth),
  what you save and what it works out at a month. Monthly prices show the
  yearly price underneath. Choosing a plan from the 12 month view carries
  that choice through sign-up to the payment page.
- Versions are now written with a point: this is 1.48, the last was 1.47.
  The update page, reports and the fix pack all show it that way. Nothing
  changes in how updates work.
- The GitHub repository has RELEASES.md: every release, what changed and
  exactly which files were pushed.

VERSION 1.49: INVOICE NUMBERS
- When an invoice is sent, its number now ends with a random code instead
  of a count: CR-2026-7KQ4XM rather than CR-2026-0003, so nobody can tell
  from their invoice how many have gone out before it. The prefix and year
  are as before (Admin, Money, invoice settings). Each number is checked
  against every earlier one so it is never used twice. The same number
  shows on the email, the invoice page and the PDF saved from it.
- Invoices already sent keep their numbers.

VERSION 1.50: PDFS, REMINDERS, THE FUNNEL, TIPS, YOUR DATA, ALERTS, ACCESSIBILITY
- Invoices: the email now has the invoice attached as a PDF, made on the
  server with nothing to install. The invoice page has Download the PDF,
  and Admin, Money, Invoices has a PDF button on each one.
- Overdue invoices: a polite reminder with the PDF goes 7 days after the
  pay-by date, and a second at 14 days. Each goes once. You get a
  notification, and the invoice list shows when they were reminded.
- Business dashboard: "From looking to paying, last 30 days". Visitors to
  the public pages, accounts started, those that built a lesson, and those
  paying. Visitors are counted once a day each, with no cookies, and only
  the daily total is kept. Robots aren't counted.
- Tips for people who start on their own (Free plan only): day 1 if they
  haven't built a lesson, day 7 about Planning, and once a month when
  they've used 80% of their free builds. Every tip has a one-click link to
  stop them, and they stop when the institution pays.
- Settings, Data and backups, for managers: "Your institution's data".
  Download everything as one zip (people, every department's work, what
  each person keeps, invoices), or ask for it all to be deleted. A request
  needs the institution's name typed, emails you, and shows in Admin, To
  do with how many days are left of the month you have to do it.
- Alerts: if the AI, the voice, email, payments or the server fail three
  times in fifteen minutes, you're emailed and notified (at most every six
  hours for each). Admin, System has a new Uptime monitor section with an
  address (auth.php?a=health) to give a free service like UptimeRobot, so
  you're texted if the whole site goes down.
- The scheduled job (auth.php?a=pushcron) now runs the hourly jobs too, so
  reminders and tips go out even on a quiet day.
- Accessibility: colours that failed contrast (green and orange buttons,
  criteria codes, buttons and badges in dark mode, the sample and subject
  pages in dark mode) are fixed, small targets are bigger, and the heading
  order in Planning is right. There's a new Accessibility statement page,
  linked from every footer.
- Four new subject pages: BTEC Business, BTEC Health and Social Care,
  BTEC Sport and A Level Computer Science, in the sitemap and footers.
- Settings: when part of the page finished loading, the whole page
  redrew and closed any box you had opened. Open boxes now stay open.
- Every public page's footer says "Created by" with the Next Level Labs
  logo, linking to visualize.agency. The logo is the one-line version from
  the Next Level Labs pack: assets/nll-logo.svg on light pages and
  assets/nll-logo-white.svg in dark mode.

VERSION 1.51: THE MISSING INVOICE FILE
- 1.50 left invoice-lib.php (the file that makes invoice PDFs) out of the
  download. Without it the Business dashboard and invoices didn't load.
  It's back, and each release is now checked for any file that's needed
  but missing before it goes out.

VERSION 1.52: THE SAME FOOTER INSIDE THE APP
- The links at the bottom of Settings and Admin now match the front
  page's footer: About, Terms, Privacy, Cookies, Security, Data
  processing, Accessibility and Contact and support, with "Created by"
  and the Next Level Labs logo underneath (the white logo in dark mode),
  linking to visualize.agency.

VERSION 1.53: FASTER, OFFLINE, SIGN-OFF, QUALITY REPORT, LEARNERS' PROGRESS
- Faster: the app's code is now its own file (app.js). The page itself is
  a fifth of the size it was, and browsers keep the code for a year, so
  after the first visit the app opens much faster. A new address is used
  whenever the code changes, so nobody gets an old copy.
- Offline: with no internet you can open and teach your saved lessons,
  and anything you change is kept on the device. It shows straight away,
  even after a reload, and is saved to the server by itself when the
  internet comes back. A note at the bottom says how many changes are
  waiting. Building with Crafticulum AI still needs the internet.
- Sign-off: under every lesson, Sign it off (a teacher can sign their own
  lesson off, no approval needed) or Ask for a review. A head of
  department sees lessons waiting for review on their start page and can
  Approve them or Ask for changes with a note. The lesson stays the
  teacher's. This sits alongside the department's own setting that holds
  teachers' changes until a leader accepts them, which is unchanged.
- Quality report: Planning has "Quality report for (course and level)".
  Every unit against its criteria: lessons built, weeks planned, taught,
  signed off, exit quiz results and learners achieved, with criteria not
  covered flagged. Print or save it as a PDF, or download it as Word.
  Learners aren't named in it.
- Learners: "Progress across units" shows each learner's share of
  criteria achieved in every unit marked for the group, how many they've
  achieved in the last two weeks, and who is well behind the group. Marks
  now record when they changed.
- Every release on GitHub is checked before the live site is told to
  fetch it: no settings file, the zip matches the folder, every PHP and
  JavaScript file is valid, nothing a page needs is missing, no real
  college named, and the site starts and answers. If any check fails the
  deploy doesn't happen.

VERSION 1.54: BEHAVES LIKE AN APP ON PHONES AND TABLETS
Only phones and tablets change. Computers keep the layout they had.
- Title bar: every screen shows its name with a back arrow. It slides out
  of the way as you scroll down and comes back when you scroll up. The
  super admin band is one slim line.
- Five tabs: Home, This week, New, Lessons, Planning. New opens a sheet:
  new lesson, AI tool, plan a unit, ask Crafti, or your lessons.
- Screens slide in and out. Swipe in from the left edge to go back.
- Lesson screen: Teach, Board and a "..." button for everything else.
  Sign-off is a small pill you tap to open.
- The Crafti mascot is smaller and tucks away while you scroll.
- Settings and Admin open as lists. Tap a row to open it.
- Grey placeholder shapes while things load, and pull down to refresh.
- Buttons press in when tapped and are at least 44px tall. Swipe a
  lesson left in My created lessons to Copy or Bin it.
- A card offers to add Crafticulum to the home screen from the second
  visit (one tap on Android, instructions on iPhone).
- Every menu on a phone opens the same bottom sheet: grab handle,
  Cancel, and drag down to close.

VERSION 1.55: BRING YOUR OWN LESSON
- Upload your own lesson: Home, the criteria form, the New button on a
  phone. Word (.docx), PowerPoint (.pptx, slides and speaker notes), PDF
  or pasted text. Pick the unit; optionally pick the criteria, or let
  Crafticulum work out which ones it covers.
- Crafticulum AI keeps the teacher's title, activities, order, timings and
  wording, checks it against the criteria, fills any gaps and writes the
  parts that are missing (starter, tasks, quiz, study and so on).
- The lesson shows a "Your lesson, checked" box: what it already covered,
  the gaps and what was added, what was kept, and what changed. Parts that
  Crafticulum wrote are marked "Added" and the optional ones can be taken
  out and put back.
- Upload your version (More on any lesson): upload an edited copy of a
  Crafticulum lesson. Same lesson, version number goes up, the old one
  stays in History. Sign-off is cleared because the content changed.
- Learner names saved in Crafticulum are taken out before anything is sent.

VERSION 1.56: BRING ALL YOUR LESSONS IN
- Bring your lessons in: a batch of lessons or a zip (up to 40 at a time),
  from first steps on Home, a card for new people, the New button, My
  created lessons, or the single upload window. Each file can be set to
  its own unit.
- Crafticulum AI checks the whole set against the criteria first and shows
  which lessons cover each criterion and where the unit has holes. Then it
  brings every lesson in (checked, gaps filled, wording kept) and can build
  the missing lessons in the teacher's own style.
- Every lesson brought in keeps its original file (saved in the data
  folder, in data/.../_orig, never at a web address). A switch on the
  lesson shows "Crafticulum version" or "Your original", with Download the
  original and Keep only what's yours.
- Google Drive and OneDrive: paste a share link (set to "anyone with the
  link") instead of downloading the file. Only those services are fetched.
- The quality report shows how many lessons in each unit were written by
  staff and how many criteria gaps were filled.
- Tidy: no real staff names or college-specific dates left in the app.

VERSION 1.57: PHONE FIXES AND A 7-DAY FREE TRIAL
- Menu on phones: every tap works in the home screen app, the menu sits
  above everything else, and long presses no longer select the text.
- Switching department keeps you on the page you were on (Lessons opens a
  new lesson in that department; Planning, Second Marker, Learners,
  Results and Settings come back as they were).
- Second Marker: a Department picker in its bar, every step can be tapped
  at any time, swipe left or right on a phone to move between steps, and
  Continue is never locked. What's still to do on an earlier step shows.
- Free trial: someone who signs up on their own gets full access (the
  Department plan) for 7 days. A countdown shows in the app. Managers get
  an email 48 and 24 hours before it ends with a link straight to paying,
  and another when it ends. Then it's the Free plan, nothing lost.
  The super admin sets the length (0 = off) and the plan in Settings,
  Plans and payments. Only applies to sign-ups from now on.

VERSION 1.58: EXTRA RESOURCES FOR EACH UNIT
- The build form has a "Use as an extra resource" tick, with a choice of
  kind (practical task, assessment practice, revision, homework, worksheet,
  case study, stretch and challenge, catch-up, other). Extras sit under an
  Extra resources heading in the unit's Planning and never take or replace
  a lesson. The same tick is on Home and in Crafti.
- Without the tick, a lesson built from the form goes into the unit's
  Planning: the first empty lesson, or in place of one the teacher picks
  (the old one stays in My created lessons), or not at all if they choose.
- Extras stay when a sequence is rewritten and travel with Unit for a
  colleague.

VERSION 1.59: UPLOAD YOUR OWN EXTRA RESOURCE
- Extra resources in Planning has Build an extra resource and Upload your
  own. Uploading checks the file against the criteria, fills gaps, keeps
  the original, and files it under the unit's Extra resources with a
  "Your own" badge. The normal upload window has the same tick.

VERSION 1.60: FIX FOR BUILDS STUCK ON PHONES
- The "Your team already has lessons for this unit" question now opens over
  the page on phones, instead of out of sight below it. Extra resources
  skip that question, and the extra resource tick is kept even if the build
  takes a while to start.

VERSION 1.61: BUILDS NO LONGER TIME OUT
- ai.php now runs each Crafticulum AI request as a background job. The page
  starts it, the server answers at once and carries on, and the page checks
  back every few seconds. Answers wait in data/_aijobs until collected
  (once, by the person who asked) and anything left is cleared after an
  hour. Long lessons no longer hit the web host's or the phone's time limit.

NO CHANGES TO config.php
