# BTEC_APP
Feedback AI

## Crafticulum release

The self-hosted site, ready to upload. No config.php and no data folder inside, so your settings and keys are never touched.

- `crafticulum/`: every file of the current release (v111, 19 Nov 2026: staff profiles, a where-do-you-work step for Google and Microsoft sign-ins, colleges that can't be set up twice and a merge for any that were, targets and a printed report on learner profiles, and the guide brought up to date). Upload the contents of this folder over the live site, including `.htaccess`.
- `crafticulum-v111.zip` and `crafticulum-release.zip`: the same files as one zip (1,854,646 bytes).

Read `crafticulum/README.txt` for what changed and how to set things up.

## Updating the live site

From v107 the site fetches its own releases from this repository (Settings, Site setup, Updates), so nothing needs uploading after v107 is on.

- `crafticulum-release.zip` and `crafticulum/version.json` are what the site reads. Keep both on `main`.
- `.github/workflows/deploy.yml` tells the site the moment a release is pushed. It needs one repository secret, `CRAFTICULUM_DEPLOY_URL`: the address shown in Crafticulum under Settings, Site setup, Updates. Without the secret the workflow does nothing.
- `.cpanel.yml` is still here for cPanel's "Deploy HEAD Commit" button, as a fallback.
