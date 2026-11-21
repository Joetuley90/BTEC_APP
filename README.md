# BTEC_APP
Feedback AI

## Crafticulum release

The self-hosted site, ready to upload. No config.php and no data folder inside, so your settings and keys are never touched.

- `crafticulum/`: every file of the current release (v140, 21 Nov 2026: an institution-wide lesson library, feedback on every build, versions of a lesson for learners who need them, offline working, refunds and a forecast in Admin, referrals, subject pages, a status page and a weekly summary for managers; v139 added the Business dashboard and invoices). Upload the contents of this folder over the live site, including `.htaccess`.
- `crafticulum-v140.zip` and `crafticulum-release.zip`: the same files as one zip (3,331,719 bytes).

Read `crafticulum/README.txt` for what changed and how to set things up.

## Updating the live site

From v107 the site fetches its own releases from this repository (Settings, Site setup, Updates), so nothing needs uploading after v107 is on.

- `crafticulum-release.zip` and `crafticulum/version.json` are what the site reads. Keep both on `main`.
- `.github/workflows/deploy.yml` tells the site the moment a release is pushed. It needs one repository secret, `CRAFTICULUM_DEPLOY_URL`: the address shown in Crafticulum under Settings, Site setup, Updates. Without the secret the workflow does nothing.
- `.cpanel.yml` is still here for cPanel's "Deploy HEAD Commit" button, as a fallback.
