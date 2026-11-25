# BTEC_APP
Feedback AI

## Crafticulum release

The self-hosted site, ready to upload. No config.php and no data folder inside, so your settings and keys are never touched.

- `crafticulum/`: every file of the current release (1.56, 25 Nov 2026: Bring all your lessons in: batch or zip, checked as a set, holes filled in your style, originals kept with a switch, Drive and OneDrive links, staff-written lessons in the quality report, and a neutral-name sweep.). Upload the contents of this folder over the live site, including `.htaccess`. What changed in each release, and which files, is in `RELEASES.md`.
- `crafticulum-v1.56.zip` and `crafticulum-release.zip`: the same files as one zip (3,748,163 bytes).

Read `crafticulum/README.txt` for what changed and how to set things up.

## Updating the live site

From v107 the site fetches its own releases from this repository (Settings, Site setup, Updates), so nothing needs uploading after v107 is on.

- `crafticulum-release.zip` and `crafticulum/version.json` are what the site reads. Keep both on `main`.
- `.github/workflows/deploy.yml` tells the site the moment a release is pushed. It needs one repository secret, `CRAFTICULUM_DEPLOY_URL`: the address shown in Crafticulum under Settings, Site setup, Updates. Without the secret the workflow does nothing.
- `.cpanel.yml` is still here for cPanel's "Deploy HEAD Commit" button, as a fallback.
