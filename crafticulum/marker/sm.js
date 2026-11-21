/* Crafticulum: Second Marker. Built by build-marker.py. Loaded by the app the first time Second Marker is opened. */
window.SMinit = function (SMHOST) {
  const SM_CSS = "/* \u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\n   Second Marker\n   Blue pencil = the second marker (AI) suggests.\n   Purple ink  = the assessor decides.\n   Brand colours can be set in keys.js (BTEC_BRAND_PURPLE / _BLUE).\n   \u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500 */\n.sm-root {\n  --ground: #F1F0F6;\n  --paper: #FFFFFF;\n  --ink: #1C1733;\n  --ink-2: #3A3550;\n  --slate: #5E5A73;\n  --rule: #DCD9E6;\n  --rule-2: #E9E7F1;\n  --purple: var(--brand-purple, #5B2BD6);\n  --blue: var(--brand-blue, #1D63D8);\n  --purple-text: var(--purple);\n  --sunken: #FBFAFE;\n  --toast: #1C1733;\n  --purple-ink: color-mix(in srgb, var(--purple) 82%, #000);\n  --purple-tint: #EFEAFB;\n  --purple-tint: color-mix(in srgb, var(--purple) 10%, #fff);\n  --blue-tint: #E8F0FC;\n  --blue-tint: color-mix(in srgb, var(--blue) 9%, #fff);\n  --blue-soft: color-mix(in srgb, var(--blue) 45%, #fff);\n  --yes: #17734A; --yes-tint: #E2F3EA;\n  --no: #B4410E; --no-tint: #FBEBE1;\n  --resub: #8A5A00; --resub-tint: #FBF1D6;\n  --sans: 'Atkinson Hyperlegible Next', 'Atkinson Hyperlegible', system-ui, -apple-system, 'Segoe UI', sans-serif;\n  --mono: 'Atkinson Hyperlegible Mono', ui-monospace, 'SFMono-Regular', Consolas, monospace;\n  --doc: Carlito, Calibri, 'Segoe UI', Arial, sans-serif;\n  --r-s: 8px; --r-m: 12px; --r-l: 16px;\n  --lift: 0 1px 2px rgba(28,23,51,.07), 0 10px 28px rgba(28,23,51,.07);\n}\n*, *::before, *::after { box-sizing: border-box; }\n[hidden] { display: none !important; }\n.sm-root { -webkit-text-size-adjust: 100%; }\n.sm-root { margin: 0; background: var(--ground); color: var(--ink); font-family: var(--sans); font-size: 15px; line-height: 1.5; -webkit-font-smoothing: antialiased; }\nh1, h2, h3, h4, p, ul, ol { margin: 0; }\nbutton, input, select, textarea { font: inherit; color: inherit; }\nbutton { cursor: pointer; }\na { color: var(--purple-text); }\n:focus-visible { outline: 3px solid color-mix(in srgb, var(--purple) 55%, #fff); outline-offset: 2px; }\ninput[type=\"file\"] { display: none; }\nkbd { font-family: var(--mono); font-size: 12px; border: 1px solid var(--rule); border-bottom-width: 2px; border-radius: 5px; padding: 0 6px; background: var(--paper); color: var(--ink); }\n.sr-only { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; }\n.m-0 { margin: 0 !important; } .me-2 { margin-right: 8px; } .m-auto { margin: auto; } .d-flex { display: flex; } .align-items-center { align-items: center; } .border-0 { border: 0; }\n\n/* \u2500\u2500 page frame \u2500\u2500 */\n.page { min-height: 100vh; display: flex; flex-direction: column; }\n.page > .step, .page > .wiz-nav, .page > .draft-banner, .page > .info-box { width: 100%; max-width: var(--w, 960px); margin-inline: auto; padding-inline: 28px; }\n.sm-root[data-step=\"5\"] { --w: 1720px; }\n.sm-root[data-step=\"4\"], .sm-root[data-step=\"6\"] { --w: 1080px; }\n\n/* \u2500\u2500 header \u2500\u2500 */\n.topbar { display: flex; flex-wrap: wrap; align-items: center; gap: 14px 28px; padding: 12px 28px; background: var(--paper); border-bottom: 1px solid var(--rule); }\n.brand { display: flex; align-items: center; gap: 14px; min-width: 0; }\n.brand-mark { flex: none; }\n.brand h1 { font-size: 19px; font-weight: 800; letter-spacing: -0.01em; white-space: nowrap; }\n.ctx { display: flex; flex-direction: column; line-height: 1.3; padding-left: 18px; border-left: 1px solid var(--rule); min-width: 0; }\n.ctx .c1 { font-size: 13px; color: var(--slate); }\n.ctx .c2 { font-size: 14px; font-weight: 700; max-width: 360px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }\n.stepper { list-style: none; display: flex; gap: 6px; padding: 0; margin: 0 auto; align-items: flex-end; }\n.stepper li { width: 92px; display: flex; flex-direction: column; gap: 6px; font-size: 13px; color: var(--slate); }\n.stepper li > button, .stepper li > span.sl { all: unset; display: block; cursor: default; padding: 2px 0; border-radius: 4px; }\n.stepper li > button { cursor: pointer; }\n.stepper li > button:hover { color: var(--purple-text); }\n.stepper li > button:focus-visible { outline: 3px solid color-mix(in srgb, var(--purple) 55%, #fff); }\n.stepper .bar { height: 4px; border-radius: 2px; background: var(--rule); }\n.stepper li.done .bar { background: var(--purple); }\n.stepper li.active { color: var(--ink); font-weight: 700; }\n.stepper li.active .bar { background: linear-gradient(90deg, var(--purple) 50%, var(--rule) 50%); }\n.top-right { display: flex; align-items: center; gap: 14px; margin-left: auto; }\n.draft-status { color: var(--slate); font-size: 13px; white-space: nowrap; }\n.draft-status.err { color: var(--no); }\n.btn-settings { display: inline-flex; align-items: center; gap: 8px; height: 44px; padding: 0 14px; border: 1px solid var(--rule); border-radius: 10px; background: var(--paper); color: var(--ink); font-weight: 600; }\n.btn-settings:hover { border-color: var(--slate); }\n@media (max-width: 1360px) { .stepper { order: 5; flex-basis: 100%; justify-content: flex-start; overflow-x: auto; } }\n@media (max-width: 640px) { .topbar { padding: 10px 16px; } .ctx { display: none; } .stepper li { width: 64px; font-size: 12px; } .page > .step, .page > .wiz-nav, .page > .draft-banner { padding-inline: 16px; } }\n\n/* \u2500\u2500 steps: setup steps read as one sheet \u2500\u2500 */\n.step { display: none; padding-top: 24px; padding-bottom: 8px; }\n.step.active { display: block; }\n.step > .card, .step > div > .card { background: var(--paper); border: 1px solid var(--rule); padding: 24px 28px; border-radius: var(--r-l); }\n.step > .card + .card { border-top: 1px solid var(--rule-2); margin-top: -1px; border-top-left-radius: 0; border-top-right-radius: 0; }\n.step > .card:has(+ .card) { border-bottom-left-radius: 0; border-bottom-right-radius: 0; }\n.step > div:empty { display: none; }\n.step[data-step=\"4\"] > div > .card { margin-bottom: 14px; }\n#integrityCard { display: none; }\n.step-title { font-size: 20px; font-weight: 800; letter-spacing: -0.012em; margin-bottom: 14px; color: var(--ink); }\n.label { font-size: 15px; font-weight: 700; margin-bottom: 4px; color: var(--ink); }\n.label span { font-weight: 400 !important; color: var(--slate); }\n.helper { font-size: 13.5px; color: var(--slate); margin-top: 6px; }\n.field-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 14px 18px; }\n.field-group { display: flex; flex-direction: column; gap: 6px; min-width: 0; }\n.field-label { font-size: 14px; font-weight: 600; color: var(--ink-2); }\n.csv-bar { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; margin-bottom: 14px; }\n.btn-row { display: flex; gap: 10px; flex-wrap: wrap; padding: 16px 0; }\n.info-box { margin-top: 16px; padding: 14px 18px; border-radius: var(--r-m); background: var(--no-tint); color: var(--no); }\n\n/* \u2500\u2500 controls \u2500\u2500 */\n.btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 40px; padding: 0 16px; border-radius: 10px; border: 1px solid transparent; font-weight: 700; font-size: 14.5px; line-height: 1.2; text-decoration: none; background: none; color: var(--ink); transition: background-color .12s, border-color .12s, color .12s; }\n.btn:disabled { opacity: .45; cursor: not-allowed; }\n.btn-primary { background: var(--purple); color: #fff; border-color: var(--purple-text); }\n.btn-primary:hover:not(:disabled) { background: var(--purple-ink); border-color: var(--purple-ink); }\n.btn-outline-secondary { background: var(--paper); border-color: var(--rule); color: var(--ink); }\n.btn-outline-secondary:hover:not(:disabled) { border-color: var(--slate); }\n.btn-sm { min-height: 36px; padding: 0 12px; font-size: 13.5px; border-radius: var(--r-s); }\n.btn-link, .linkish { background: none; border: 0; color: var(--purple-text); padding: 0 2px; min-height: 32px; font-weight: 700; text-decoration: underline; text-underline-offset: 3px; text-decoration-thickness: 1px; }\n.btn-link:hover { color: var(--purple-ink); }\n.wiz-nav .btn, .btn-export, .btn-run { min-height: 46px; padding: 0 20px; font-size: 15px; }\n.btn-remove { background: none; border: 0; color: var(--slate); font-size: 13px; padding: 4px 6px; border-radius: 6px; min-height: 32px; }\n.btn-remove:hover { color: var(--no); background: var(--no-tint); }\n.btn-close { width: 40px; height: 40px; border: 0; border-radius: 10px; background: none; position: relative; }\n.btn-close::before, .btn-close::after { content: ''; position: absolute; left: 12px; right: 12px; top: 19px; height: 2px; background: currentColor; transform: rotate(45deg); }\n.btn-close::after { transform: rotate(-45deg); }\n.btn-close:hover { background: var(--rule-2); }\n.btn-close-white { color: #fff; }\n.btn-close-white:hover { background: rgba(255,255,255,.15); }\n.form-control, .form-select, .field-input { width: 100%; min-height: 44px; padding: 9px 12px; border: 1px solid var(--rule); border-radius: 10px; background: var(--paper); color: var(--ink); font-size: 15px; line-height: 1.4; transition: border-color .12s, box-shadow .12s; }\ntextarea.form-control, textarea.field-input { min-height: 96px; resize: vertical; }\ntextarea.email-body { min-height: 260px; }\n.form-control:hover, .form-select:hover { border-color: #C6C2D6; }\n.form-control:focus, .form-select:focus, .field-input:focus { outline: none; border-color: var(--purple-text); box-shadow: 0 0 0 3px var(--purple-tint); }\n.form-control::placeholder { color: #8C88A0; }\n.form-control-sm, .form-select-sm { min-height: 36px; padding: 6px 10px; font-size: 14px; border-radius: var(--r-s); }\n.form-select { appearance: none; padding-right: 36px; background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1.5l5 5 5-5' fill='none' stroke='%235E5A73' stroke-width='1.8' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 12px center; }\n.form-select:disabled { background-color: var(--rule-2); color: var(--slate); cursor: not-allowed; }\n.form-check-input { width: 18px; height: 18px; margin: 0; accent-color: var(--purple-text); flex: none; vertical-align: -3px; cursor: pointer; }\n.form-check { display: flex; align-items: center; gap: 10px; }\n.form-switch .form-check-input { appearance: none; width: 40px; height: 24px; border-radius: 12px; background: var(--rule); position: relative; transition: background .15s; }\n.form-switch .form-check-input::after { content: ''; position: absolute; top: 3px; left: 3px; width: 18px; height: 18px; border-radius: 50%; background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,.2); transition: transform .15s; }\n.form-switch .form-check-input:checked { background: var(--purple); }\n.form-switch .form-check-input:checked::after { transform: translateX(16px); }\n.form-check-label { cursor: pointer; }\n.input-group { display: flex; flex-wrap: wrap; gap: 6px; }\n.input-group > .form-control { flex: 1 1 220px; }\n.badge { display: inline-flex; align-items: center; padding: 2px 9px; border-radius: 6px; font-size: 12.5px; font-weight: 700; line-height: 1.5; white-space: nowrap; }\n.badge-y { background: var(--yes-tint); color: var(--yes); } .badge-n { background: var(--no-tint); color: var(--no); } .badge-r { background: var(--resub-tint); color: var(--resub); } .badge-p { background: var(--rule-2); color: var(--slate); }\n.grade-pill { background: var(--purple-tint); color: var(--purple-text); }\n.grade-pill.g-U { background: var(--no-tint); color: var(--no); } .grade-pill.g-X { background: var(--rule-2); color: var(--slate); }\n.alert { padding: 12px 16px; border-radius: var(--r-m); margin-top: 12px; }\n.alert-warning { background: var(--resub-tint); color: #5C3D00; }\n.alert-warning .btn { margin-left: 8px; }\n.spinner-border { display: inline-block; width: 22px; height: 22px; border: 3px solid var(--blue-tint); border-top-color: var(--blue); border-radius: 50%; animation: spin .7s linear infinite; vertical-align: -5px; }\n.spinner-border-sm { width: 16px; height: 16px; border-width: 2px; vertical-align: -3px; }\n@keyframes spin { to { transform: rotate(360deg); } }\n.table { width: 100%; border-collapse: collapse; font-size: 14px; }\n.table th { text-align: left; font-weight: 600; color: var(--slate); font-size: 13px; padding: 8px 10px; border-bottom: 1px solid var(--rule); }\n.table td { padding: 10px; border-bottom: 1px solid var(--rule-2); vertical-align: top; }\n.code-tag { font-family: var(--mono); font-weight: 600; font-size: 13.5px; }\ndetails.more { margin-top: 14px; }\ndetails.more > summary { cursor: pointer; color: var(--purple-text); font-weight: 700; font-size: 14px; list-style: none; display: inline-flex; align-items: center; gap: 6px; min-height: 32px; }\ndetails.more > summary::-webkit-details-marker { display: none; }\ndetails.more > summary::before { content: ''; width: 7px; height: 7px; border-right: 2px solid currentColor; border-bottom: 2px solid currentColor; transform: rotate(-45deg); transition: transform .15s; margin-right: 2px; }\ndetails.more[open] > summary::before { transform: rotate(45deg); }\n.file-chip { display: inline-flex; align-items: center; gap: 6px; background: var(--purple-tint); border-radius: var(--r-s); padding: 4px 4px 4px 10px; font-size: 13.5px; color: var(--ink); max-width: 100%; }\n.assign-wrap { margin-top: 16px; padding: 14px 16px; border-radius: var(--r-m); background: var(--purple-tint); }\n.assign-wrap .form-select { background-color: var(--paper); }\n.assign-info { font-size: 14px; color: var(--ink-2); margin-top: 4px; }\n.assign-info:empty { display: none; }\n.assign-info ul { list-style: none; padding: 0; margin: 6px 0 4px; display: flex; flex-direction: column; gap: 3px; }\n.assign-info .code-tag { font-size: 13px; }\n.assign-info details.more { margin-top: 4px; }\n.brief-text { white-space: pre-wrap; font-family: var(--sans); font-size: 13.5px; line-height: 1.5; max-height: 380px; overflow: auto; background: var(--paper); border: 1px solid var(--rule); border-radius: 10px; padding: 12px 14px; margin: 8px 0 0; }\n.file-chip span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }\n\n/* \u2500\u2500 step 1: unit + criteria picker \u2500\u2500 */\n.combine { display: flex; flex-wrap: wrap; align-items: center; gap: 4px 12px; margin-top: 10px; }\n.combine-list { flex-basis: 100%; display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 6px 14px; padding: 12px 14px; border: 1px solid var(--rule); border-radius: var(--r-m); margin-top: 6px; }\n.combine-list label { display: flex; gap: 10px; align-items: flex-start; font-size: 14px; cursor: pointer; }\n.combine-list label.primary { color: var(--slate); }\n.combine-group { grid-column: 1 / -1; font-size: 13px; font-weight: 700; color: var(--slate); margin-top: 6px; }\n.chips { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 12px; padding: 0; border: 0; background: none; min-height: 0; }\n.chip { min-height: 36px; padding: 0 14px; border-radius: 18px; border: 1px solid var(--rule); background: var(--paper); font-weight: 600; font-size: 13.5px; }\n.chip:hover { border-color: var(--slate); }\n.chip.on { background: var(--ink); border-color: var(--ink); color: var(--paper); }\n.crit-pick { display: flex; flex-direction: column; border-top: 1px solid var(--rule-2); }\n.crit-item { display: grid; grid-template-columns: 18px 70px minmax(0, 1fr) auto; gap: 14px; align-items: start; padding: 11px 4px; border-bottom: 1px solid var(--rule-2); cursor: pointer; font-size: 14.5px; }\n.crit-item:hover { background: var(--sunken); }\n.crit-item .form-check-input { margin-top: 2px; }\n.crit-item .cc { font-family: var(--mono); font-weight: 600; font-size: 14px; }\n.crit-item .cd { color: var(--ink-2); }\n.band { font-size: 12px; font-weight: 700; padding: 1px 8px; border-radius: 6px; background: var(--rule-2); color: var(--slate); white-space: nowrap; }\n.band.band-M { background: var(--purple-tint); color: var(--purple-text); }\n.band.band-D { background: var(--purple); color: #fff; }\n.unit-head { font-weight: 700; padding: 14px 4px 6px; font-size: 14px; }\n.add-crit { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 10px; }\n.add-crit .desc-input { flex: 1 1 260px; }\n.ref-row { display: grid; grid-template-columns: minmax(0, 1fr) 200px auto; gap: 8px 10px; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--rule-2); }\n.ref-row textarea { grid-column: 1 / -1; min-height: 64px; }\n.ref-name { font-size: 14px; font-weight: 600; overflow: hidden; text-overflow: ellipsis; }\n#refList:not(:empty) { margin-top: 10px; }\n.resub-head { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }\n.resub-head .form-check-label { font-weight: 700; }\n.resub-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 8px; margin-top: 12px; }\n.resub-grid label { display: flex; align-items: center; justify-content: space-between; gap: 8px; border: 1px solid var(--rule); border-radius: 10px; padding: 6px 6px 6px 12px; }\n.resub-grid .cc { font-family: var(--mono); font-weight: 600; font-size: 14px; }\n.resub-grid select { width: 104px; }\n\n/* \u2500\u2500 step 3: upload \u2500\u2500 */\n.drop-zone { border: 2px dashed var(--rule); border-radius: var(--r-l); padding: 44px 20px; text-align: center; color: var(--slate); cursor: pointer; background: var(--sunken); transition: border-color .15s, background .15s; }\n.drop-zone:hover, .drop-zone.drag { border-color: var(--purple-text); background: var(--purple-tint); color: var(--ink); }\n.drop-zone strong { color: var(--purple-text); }\n.drop-icon { display: flex; justify-content: center; margin-bottom: 8px; color: var(--purple-text); }\n.drop-zone.has-file { border-style: solid; border-color: var(--purple-text); background: var(--purple-tint); color: var(--ink); font-weight: 700; }\n.or-line { display: flex; align-items: center; gap: 12px; color: var(--slate); font-size: 13px; margin: 16px 0 10px; }\n.or-line::before, .or-line::after { content: ''; flex: 1; height: 1px; background: var(--rule); }\n.link-row { display: flex; gap: 8px; }\n.upload-progress { list-style: none; padding: 0; margin-top: 18px; display: flex; flex-direction: column; gap: 8px; font-size: 14.5px; color: var(--slate); }\n.upload-progress li { display: flex; align-items: center; gap: 10px; }\n.pdot { width: 16px; height: 16px; border-radius: 50%; border: 2px solid var(--rule); flex: none; }\n.upload-progress li.run { color: var(--ink); } .upload-progress li.run .pdot { border-color: var(--blue-tint); border-top-color: var(--blue); animation: spin .7s linear infinite; }\n.upload-progress li.ok { color: var(--ink); } .upload-progress li.ok .pdot { border-color: var(--purple-text); background: var(--purple) url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24'%3E%3Cpath d='M5 12.5l4.5 4.5L19 7.5' fill='none' stroke='white' stroke-width='3.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E\") center no-repeat; }\n.upload-progress li.err { color: var(--no); } .upload-progress li.err .pdot { border-color: var(--no); background: var(--no); }\n\n/* \u2500\u2500 step 4: checks \u2500\u2500 */\n.status { display: flex; align-items: center; gap: 10px; color: var(--ink-2); }\n.ai-grade .big { margin: 6px 0 8px; }\n.ai-grade .big .badge { font-size: 20px !important; padding: 4px 14px; color: var(--blue); background: var(--blue-tint); font-style: italic; font-weight: 600; }\n.ihead { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; margin: 8px 0 12px; }\n.flag-tag { display: inline-block; font-size: 12px; font-weight: 700; padding: 1px 8px; border-radius: 6px; background: var(--rule-2); color: var(--slate); margin-right: 8px; white-space: nowrap; }\n.tag-high { background: var(--no-tint); color: var(--no); } .tag-medium { background: var(--resub-tint); color: var(--resub); } .tag-low { background: var(--yes-tint); color: var(--yes); }\n.flags { list-style: none; padding: 0; display: flex; flex-direction: column; gap: 2px; margin-bottom: 14px; }\n.flag { padding: 9px 0; border-bottom: 1px solid var(--rule-2); font-size: 14.5px; }\n.flag-high .flag-tag { background: var(--no-tint); color: var(--no); } .flag-medium .flag-tag { background: var(--resub-tint); color: var(--resub); }\n.igrid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 14px; }\n.isec { border: 1px solid var(--rule-2); border-radius: var(--r-m); padding: 14px 16px; min-width: 0; }\n.ititle { font-weight: 700; font-size: 14px; margin-bottom: 6px; }\n.itable, .vtable { width: 100%; border-collapse: collapse; font-size: 13.5px; }\n.itable td, .vtable td, .vtable th { padding: 5px 0; vertical-align: top; border-bottom: 1px solid var(--rule-2); text-align: left; white-space: pre-line; overflow-wrap: anywhere; }\n.itable td.ik, .k { color: var(--slate); width: 42%; padding-right: 10px; }\n.ilist, .vlist { padding-left: 18px; font-size: 13.5px; display: flex; flex-direction: column; gap: 4px; overflow-wrap: anywhere; }\n.ilist li.ailink { color: var(--no); font-weight: 700; }\n.cloud-sec { grid-column: 1 / -1; }\n.tick { color: var(--yes); font-weight: 700; }\n\n/* \u2500\u2500 step 5: the marking desk \u2500\u2500 */\n.learner-strip { display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 18px; padding: 0 2px 18px; }\n.learner-strip h2 { font-size: 28px; font-weight: 800; letter-spacing: -0.015em; line-height: 1.15; }\n.learner-strip .meta { color: var(--slate); }\n.learner-strip .tag { font-size: 13px; font-weight: 700; color: var(--purple-text); background: var(--purple-tint); padding: 2px 10px; border-radius: 6px; }\n.desk { display: grid; grid-template-columns: minmax(300px, 360px) minmax(0, 1fr) minmax(290px, 350px); gap: 24px; align-items: start; }\n.desk > * { min-width: 0; }\n.marksheet, .sidepane { position: sticky; top: 16px; max-height: calc(100vh - 112px); overflow-y: auto; overscroll-behavior: contain; scrollbar-width: thin; padding: 2px 4px 12px 2px; }\n.ms-head { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }\n.ms-head h2 { font-size: 17px; font-weight: 800; }\n.ms-head .count { font-size: 13px; color: var(--slate); }\n.ms-head .btn { margin-left: auto; }\n.grade-box { display: grid; grid-template-columns: 1fr 1fr; border: 1px solid var(--rule); border-radius: var(--r-m); background: var(--paper); overflow: hidden; }\n.grade-box > div { padding: 12px 14px; display: flex; flex-direction: column; min-width: 0; }\n.grade-box .gb-k { font-size: 13px; color: var(--slate); display: flex; align-items: center; gap: 6px; }\n.grade-big { font-size: 26px; font-weight: 800; color: var(--purple-text); line-height: 1.2; overflow-wrap: anywhere; }\n.grade-big.g-X, .grade-big.g-U { color: var(--ink-2); font-size: 18px; font-weight: 700; padding-top: 5px; }\n.gb-ai { background: var(--blue-tint); border-left: 1px dashed var(--blue); }\n.gb-ai .gb-k { color: var(--blue); }\n.gb-ai .gb-v { font-size: 22px; font-style: italic; color: var(--blue); line-height: 1.25; padding-top: 2px; overflow-wrap: anywhere; }\n.gb-ai .gb-v.small { font-size: 16px; padding-top: 6px; }\n.grade-note { display: flex; flex-wrap: wrap; align-items: center; gap: 4px 10px; margin: 8px 0 4px; }\n.grade-note .helper { margin: 0; }\n.crit-list { display: flex; flex-direction: column; margin-top: 8px; }\n.crit-row { position: relative; padding: 12px 12px; border-radius: var(--r-m); border: 2px solid transparent; border-bottom: 1px solid var(--rule); border-bottom-left-radius: 0; border-bottom-right-radius: 0; cursor: pointer; transition: background-color .12s; }\n.crit-row:hover { background: color-mix(in srgb, var(--paper) 60%, transparent); }\n.crit-row.current { background: var(--paper); border: 2px solid var(--purple); border-radius: var(--r-m); box-shadow: 0 6px 20px rgba(28,23,51,.08); cursor: default; margin: 4px 0; }\n.crit-top { display: flex; align-items: center; gap: 8px; }\n.code-input { all: unset; field-sizing: content; min-width: 4ch; font-family: var(--mono); font-weight: 600; font-size: 15px; width: 9ch; min-width: 0; flex: 0 1 auto; border-radius: 4px; cursor: text; }\n.code-input:focus-visible { outline: 2px solid var(--purple-tint); }\n.band-tag { font-size: 12px; font-weight: 700; color: var(--slate); }\n.band-tag.band-M { color: var(--purple-text); }\n.band-tag.band-D { color: var(--purple-text); }\n.prev-tag { margin-left: auto; font-size: 12px; color: var(--slate); white-space: nowrap; }\n.prev-tag b { color: var(--ink); }\n.crit-desc { font-size: 14px; color: var(--ink-2); margin: 4px 0 8px; line-height: 1.45; }\n.ai-cell { display: flex; flex-wrap: wrap; align-items: center; gap: 4px 8px; font-size: 13px; color: var(--blue); margin-bottom: 10px; }\n.sugg { display: inline-flex; align-items: center; gap: 5px; font-style: italic; border: 1px dashed var(--blue); border-radius: 6px; padding: 0 8px; line-height: 1.6; }\n.sugg::before { content: ''; width: 11px; height: 11px; flex: none; background: currentColor; -webkit-mask: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M16.5 3.5l4 4L8 20H4v-4z' fill='none' stroke='black' stroke-width='2.4' stroke-linejoin='round'/%3E%3C/svg%3E\") center / contain no-repeat; mask: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M16.5 3.5l4 4L8 20H4v-4z' fill='none' stroke='black' stroke-width='2.4' stroke-linejoin='round'/%3E%3C/svg%3E\") center / contain no-repeat; }\n.sugg.idle { color: var(--slate); border-color: var(--rule); }\n.sugg.idle::before { display: none; }\n.ai-cell .task { font-weight: 700; font-size: 12.5px; }\n.ai-cell .ev { flex-basis: 100%; font-style: italic; line-height: 1.45; display: none; font-size: 13.5px; }\n.crit-row.current .ai-cell .ev { display: block; }\n.stamps { display: flex; gap: 6px; }\n.stamp { width: 44px; height: 44px; border-radius: var(--r-s); border: 1px solid var(--rule); background: var(--paper); color: var(--ink); display: inline-flex; flex-direction: column; align-items: center; justify-content: center; gap: 1px; padding: 0; transition: transform .12s, background-color .12s; }\n.stamp b { font-size: 17px; font-weight: 800; line-height: 1; }\n.stamp small { display: none; font-size: 12.5px; font-weight: 600; }\n.stamp:hover:not(:disabled) { border-color: var(--purple-text); color: var(--purple-text); }\n.stamp.sug { border: 1.5px dashed var(--blue); background: var(--blue-tint); color: var(--blue); }\n.stamp.on { background: var(--purple); border: 2px solid var(--purple); color: #fff; transform: rotate(-3deg); box-shadow: 0 4px 10px color-mix(in srgb, var(--purple) 30%, transparent); }\n.stamp.on:nth-child(2) { transform: rotate(2deg); } .stamp.on:nth-child(3) { transform: rotate(-2deg); }\n.stamp.on:hover { color: #fff; }\n.stamp:disabled { opacity: .4; cursor: not-allowed; }\n.stamp.thud { animation: thud .22s ease-out; }\n@keyframes thud { 0% { transform: scale(1.18) rotate(-6deg); } 100% { transform: scale(1) rotate(-3deg); } }\n.achieved-sel { position: absolute; width: 1px; height: 1px; opacity: 0; pointer-events: none; right: 8px; top: 8px; }\n.row-more { display: none; margin-top: 10px; }\n.crit-row.current .row-more { display: block; }\n.desc-input { width: 100%; min-height: 64px; padding: 8px 12px; border: 1px solid var(--rule); border-radius: 10px; background: var(--paper); font-size: 14px; line-height: 1.45; resize: vertical; }\n.desc-input:focus { outline: none; border-color: var(--purple-text); box-shadow: 0 0 0 3px var(--purple-tint); }\n.row-more .btn-remove { margin-top: 4px; }\n.keys { font-size: 13px; color: var(--slate); display: flex; flex-wrap: wrap; align-items: center; gap: 5px; margin-top: 14px; }\n.focus-only { display: none !important; }\n\n/* the work */\n.workpane { container-type: inline-size; }\n.work-bar { display: flex; flex-wrap: wrap; align-items: center; gap: 8px 14px; margin-bottom: 12px; min-height: 36px; }\n.work-bar h2 { font-size: 17px; font-weight: 800; }\n.work-bar .fname { font-size: 13px; color: var(--slate); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 420px; }\n.work-bar label { margin-left: auto; display: flex; align-items: center; gap: 8px; font-size: 13.5px; color: var(--ink-2); cursor: pointer; }\n.work-bar .form-check-input { accent-color: var(--blue); }\n.work-wrap { display: grid; grid-template-columns: minmax(0, 1fr) 230px; gap: 18px; align-items: start; }\n.work-wrap.hide-notes { grid-template-columns: minmax(0, 1fr); }\n.work-wrap.hide-notes .notes { display: none; }\n.work-wrap.hide-notes mark.ann-mark { background: none; border-bottom-color: transparent; }\n.paper { background: var(--paper); border-radius: 3px; box-shadow: var(--lift); min-height: 640px; position: relative; }\n.paper.doc { padding: 56px clamp(24px, 6%, 72px) 72px; font-family: var(--doc); font-size: 16.5px; line-height: 1.62; color: #222; }\n.paper.doc p { margin: 0 0 13px; white-space: pre-wrap; overflow-wrap: anywhere; }\n.paper.doc h3 { font-family: var(--doc); font-size: 20px; font-weight: 700; color: #1F3864; margin: 22px 0 10px; }\n.paper.doc h3:first-child, .paper.doc h2:first-child { margin-top: 0; }\n.paper.doc h2 { font-family: var(--doc); font-size: 27px; font-weight: 400; color: #1F3864; margin: 0 0 16px; padding-bottom: 6px; border-bottom: 1px solid #4472C4; }\n.paper.doc h4 { font-family: var(--doc); font-size: 17px; font-weight: 700; color: #2F5496; margin: 16px 0 8px; }\n.paper.doc p.li { padding-left: 24px; position: relative; margin-bottom: 6px; }\n.paper.doc p.li::before { content: ''; position: absolute; left: 8px; top: .7em; width: 5px; height: 5px; border-radius: 50%; background: #333; }\n.paper.doc table.doc-table { border-collapse: collapse; width: 100%; margin: 4px 0 16px; font-size: 15px; }\n.paper.doc table.doc-table td { border: 1px solid #BFBFBF; padding: 6px 9px; vertical-align: top; white-space: pre-wrap; }\n.img-ph { display: flex; align-items: center; justify-content: center; gap: 8px; height: 72px; margin: 0 0 13px; border: 1px dashed #C9C6D6; border-radius: 4px; color: var(--slate); font-family: var(--sans); font-size: 13px; background: #FAFAFC; }\n.paper.empty, .paper.loading { display: flex; align-items: center; justify-content: center; padding: 40px; text-align: center; }\n.paper.pdf { background: none; box-shadow: none; display: flex; flex-direction: column; gap: 16px; min-height: 0; }\n.pdf-page { position: relative; background: var(--paper); box-shadow: var(--lift); border-radius: 2px; overflow: hidden; width: 100%; }\n.pdf-page canvas { display: block; width: 100%; height: auto; }\n.pdf-num { position: absolute; top: 8px; right: 10px; font-size: 12px; color: #5E5A73; background: rgba(255,255,255,.85); padding: 0 6px; border-radius: 4px; }\nmark.ann-mark { background: var(--blue-tint); color: inherit; border-bottom: 2px dashed var(--blue-soft); padding: 0 1px; border-radius: 2px; cursor: pointer; transition: background-color .15s; }\nmark.ann-mark.off { background: none; border-bottom: 1px dashed var(--rule); }\nmark.ann-mark.lit { background: color-mix(in srgb, var(--blue) 20%, #fff); border-bottom: 2px solid var(--blue); }\nmark.ann-mark.hot { background: color-mix(in srgb, var(--blue) 28%, #fff); }\n.notes { position: relative; }\n.notes-empty { font-style: italic; }\n.ann { width: 100%; background: var(--paper); border: 1px dashed var(--blue); border-radius: 10px; padding: 8px 10px 6px; color: var(--blue); font-size: 13.5px; transition: box-shadow .15s, opacity .15s; }\n.ann.hot, .ann.lit { box-shadow: 0 0 0 3px var(--blue-tint); border-style: solid; }\n.ann.off { opacity: .5; border-color: var(--rule); color: var(--slate); }\n.ann-head { display: flex; align-items: center; gap: 6px; margin-bottom: 2px; }\n.ann-head label { display: flex; align-items: center; gap: 6px; cursor: pointer; }\n.ann-head .form-check-input { width: 16px; height: 16px; accent-color: var(--blue); }\n.ann-n { font-family: var(--mono); font-size: 11.5px; font-weight: 600; min-width: 16px; height: 16px; border-radius: 8px; background: var(--blue); color: #fff; display: inline-flex; align-items: center; justify-content: center; padding: 0 4px; }\n.ann.off .ann-n { display: none; }\n.ann-code { font-family: var(--mono); font-weight: 600; font-size: 12px; }\n.ann-type { font-size: 12px; margin-left: auto; }\n.ann.t-met .ann-type { color: var(--yes); } .ann.t-not_met .ann-type { color: var(--no); }\n.ann textarea { all: unset; display: block; width: 100%; font-style: italic; line-height: 1.45; white-space: pre-wrap; overflow-wrap: anywhere; min-height: 1.45em; field-sizing: content; cursor: text; border-radius: 4px; }\n.ann textarea:focus { background: var(--blue-tint); }\n.ann-quote { font-size: 12px; color: var(--slate); margin-top: 4px; font-style: normal; }\n.ann-quote:empty { display: none; }\n.notes-sub { font-size: 12.5px; font-weight: 700; color: var(--slate); padding: 6px 0 2px; }\n@container (max-width: 680px) {\n  .work-wrap { grid-template-columns: minmax(0, 1fr); }\n  .notes { display: flex; flex-direction: column; gap: 10px; height: auto !important; }\n  .notes .ann, .notes .notes-sub { position: static !important; }\n}\n\n/* right-hand side */\n.sidepane { display: flex; flex-direction: column; gap: 20px; }\n.sidepane h2, .sidepane .sp-label { font-size: 17px; font-weight: 800; display: block; }\n.sidepane .sp-label.small { font-size: 15px; font-weight: 700; }\n.feedback-box { min-height: 240px; padding: 14px 16px; border: 1px solid var(--rule); border-radius: var(--r-m); background: var(--paper); font-size: 14.5px; line-height: 1.55; white-space: pre-wrap; overflow-wrap: anywhere; margin-top: 8px; }\n.feedback-box:focus { outline: none; border-color: var(--purple-text); box-shadow: 0 0 0 3px var(--purple-tint); }\n.feedback-box:empty::before { content: attr(data-ph); color: #8C88A0; }\n.fb-tools { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 4px 10px; margin-top: 6px; }\n.staff-note { min-height: 76px !important; margin-top: 8px; font-size: 14px; }\n.mini-checks { padding: 14px 16px; border-radius: var(--r-m); background: var(--rule-2); display: flex; flex-direction: column; gap: 8px; font-size: 14px; }\n.mini-checks .mc { display: flex; gap: 10px; align-items: flex-start; }\n.mini-checks svg { flex: none; margin-top: 2px; }\n\n/* focus mode: one criterion at a time, next to the work */\n.desk.focus { grid-template-columns: minmax(0, 1fr) minmax(380px, 470px); }\n.desk.focus .marksheet { order: 2; }\n.desk.focus .sidepane { display: none; }\n.desk.focus .focus-only { display: flex !important; }\n.desk.focus .desk-only { display: none !important; }\n.desk.focus .crit-row { display: none; }\n.desk.focus .crit-row.current { display: block; background: none; border: 0; box-shadow: none; padding: 4px 0 0; margin: 0; }\n.desk.focus .crit-row.current .code-input { font-size: 42px; letter-spacing: -0.02em; width: auto; max-width: 9ch; line-height: 1.1; }\n.desk.focus .crit-top { gap: 14px; align-items: baseline; }\n.desk.focus .band-tag { font-size: 13px; }\n.desk.focus .crit-desc { font-size: 20px; line-height: 1.4; font-weight: 500; color: var(--ink); margin: 8px 0 16px; }\n.desk.focus .ai-cell { display: flex; border: 1px dashed var(--blue); border-radius: var(--r-m); background: var(--blue-tint); padding: 12px 14px; font-size: 14.5px; margin-bottom: 18px; }\n.desk.focus .ai-cell .sugg { border: 0; padding: 0; font-size: 20px; }\n.desk.focus .ai-cell .sugg::before { width: 15px; height: 15px; }\n.desk.focus .ai-cell .ev { font-size: 15px; }\n.desk.focus .stamps { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; }\n.desk.focus .stamp { width: auto; height: 92px; border-radius: 12px; }\n.desk.focus .stamp b { font-size: 34px; }\n.desk.focus .stamp small { display: block; }\n.desk.focus .desc-input { min-height: 88px; font-size: 14.5px; }\n.focus-chips { flex-wrap: wrap; gap: 6px; margin: 14px 0 6px; }\n.fchip { min-width: 64px; height: 40px; padding: 0 10px; border-radius: var(--r-s); border: 1px solid var(--rule); background: var(--paper); font-family: var(--mono); font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; }\n.fchip b { font-family: var(--sans); font-weight: 800; }\n.fchip.done { background: var(--purple); border-color: var(--purple-text); color: #fff; }\n.fchip.current { outline: 2px solid var(--ink); outline-offset: 1px; }\n.focus-pos { font-size: 13px; color: var(--slate); }\n.focus-nav { gap: 10px; margin-top: 18px; position: sticky; bottom: -12px; z-index: 2; padding: 10px 0 12px; background: linear-gradient(transparent, var(--ground) 14px); }\n.desk.focus .grade-note .helper { display: none; }\n.desk.focus .keys { margin-top: 6px; }\n.focus-nav .btn { min-height: 50px; }\n.focus-nav .btn-primary { flex: 1; }\n\n@media (max-width: 1640px) {\n  .desk { grid-template-columns: minmax(290px, 340px) minmax(0, 1fr); }\n  .marksheet { grid-row: 1 / span 2; }\n  .sidepane { grid-column: 2; grid-row: 2; position: static; max-height: none; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); align-items: start; }\n  .desk.focus .marksheet { grid-row: auto; }\n}\n@media (max-width: 900px) {\n  .desk, .desk.focus { grid-template-columns: minmax(0, 1fr); }\n  .marksheet, .sidepane { grid-row: auto !important; grid-column: auto !important; }\n  .marksheet, .sidepane { position: static; max-height: none; }\n  .desk.focus .marksheet { order: 0; }\n}\n\n/* \u2500\u2500 step 6: export \u2500\u2500 */\n.sum-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 14px 20px; margin-bottom: 18px; font-size: 14.5px; }\n.sum-grid .k { font-size: 13px; width: auto; }\n.staff-box { padding: 12px 14px; border-radius: var(--r-m); background: var(--rule-2); font-size: 14px; }\n.step[data-step=\"6\"] .feedback-box { min-height: 0; white-space: pre-wrap; }\n.dl-grid { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 8px; }\n.attach-row { display: flex; flex-direction: column; gap: 8px; font-size: 14.5px; margin-top: 6px; }\n.attach-row label { display: flex; align-items: center; gap: 10px; cursor: pointer; }\n\n/* \u2500\u2500 navigation \u2500\u2500 */\n.wiz-nav { position: sticky; bottom: 0; z-index: 20; display: flex; align-items: center; gap: 12px; padding-top: 12px; padding-bottom: 14px; margin-top: 20px; background: color-mix(in srgb, var(--ground) 92%, transparent); backdrop-filter: blur(6px); border-top: 1px solid var(--rule); }\n.nav-msg { flex: 1; text-align: right; font-size: 13.5px; color: var(--slate); }\n.nav-next { margin-left: auto; }\n@media (max-width: 620px) { .wiz-nav { flex-wrap: wrap; } .nav-msg { order: -1; flex-basis: 100%; text-align: left; } }\n\n/* \u2500\u2500 draft banner \u2500\u2500 */\n.draft-banner { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-top: 18px; }\n.draft-banner > * { flex: none; }\n.draft-banner .dtext { flex: 1 1 260px; background: var(--paper); border: 1px solid var(--rule); border-radius: var(--r-m); padding: 12px 16px; font-size: 14.5px; }\n.draft-banner .dtext small { display: block; color: var(--slate); font-size: 13px; margin-top: 2px; }\n\n/* \u2500\u2500 settings panel \u2500\u2500 */\n.offcanvas { position: fixed; top: 0; right: 0; bottom: 0; width: min(460px, 100vw); background: var(--paper); z-index: 60; display: flex; flex-direction: column; box-shadow: -12px 0 40px rgba(28,23,51,.18); transform: translateX(100%); transition: transform .22s ease; visibility: hidden; }\n.offcanvas.show { transform: none; }\n.offcanvas-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--rule); }\n.offcanvas-title { font-size: 19px; font-weight: 800; }\n.offcanvas-body { padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 22px; }\n.offcanvas-backdrop { position: fixed; inset: 0; background: rgba(28,23,51,.35); z-index: 55; opacity: 0; transition: opacity .2s; }\n.offcanvas-backdrop.show { opacity: 1; }\n.settings-row { display: flex; flex-direction: column; gap: 8px; margin: 0 !important; }\n.model-row { display: flex; flex-direction: column; gap: 6px; }\n.model-row label { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border: 1px solid var(--rule); border-radius: 10px; cursor: pointer; font-weight: 600; }\n.model-row label:has(input:checked) { border-color: var(--purple-text); background: var(--purple-tint); }\n.model-note { font-weight: 400; color: var(--slate); }\n.key-box { display: flex; flex-direction: column; }\n.agree-box { padding: 10px 12px; border: 1px solid var(--rule); border-radius: 10px; }\n#agreeSummary { line-height: 1.55; }\n#agreeSummary strong { color: var(--ink, inherit); }\n.blocked-card { border-left: 4px solid var(--resub); }\n.blocked-main { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 10px; }\n.blocked-more { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }\n\n/* \u2500\u2500 toasts \u2500\u2500 */\n.toast-container { position: fixed; right: 0; bottom: 0; z-index: 80; padding: 16px; display: flex; flex-direction: column; gap: 8px; align-items: flex-end; pointer-events: none; }\n.toast { pointer-events: auto; min-width: 260px; max-width: min(420px, calc(100vw - 32px)); border-radius: var(--r-m); background: var(--toast); color: #fff; box-shadow: 0 10px 30px rgba(28,23,51,.25); font-size: 14.5px; animation: toastin .2s ease-out; }\n.toast-body { padding: 12px 4px 12px 16px; flex: 1; }\n.text-bg-danger { background: var(--no); } .text-bg-success { background: #14583A; } .text-bg-warning { background: #FFE9B0; color: #4A3200; } .text-bg-dark { background: var(--toast); }\n.text-bg-warning .btn-close-white { color: #4A3200; }\n.position-fixed { position: fixed; } .bottom-0 { bottom: 0; } .end-0 { right: 0; } .p-3 { padding: 16px; }\n@keyframes toastin { from { transform: translateY(8px); opacity: 0; } }\n\n@media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation-duration: .01ms !important; transition: none !important; } }\n@media print { .topbar, .wiz-nav, .marksheet, .sidepane { display: none; } }\n\n/* \u2500\u2500 marking choice, manual comments \u2500\u2500 */\n.choose-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 12px; margin-top: 16px; }\n.choice { text-align: left; display: flex; flex-direction: column; gap: 6px; padding: 18px 20px; border-radius: var(--r-m); border: 1.5px solid var(--rule); background: var(--paper); color: var(--ink); min-height: 128px; font-weight: 400; transition: border-color .12s, box-shadow .12s; }\n.choice:hover { box-shadow: var(--lift); }\n.choice b { font-size: 17px; display: flex; align-items: center; gap: 10px; }\n.choice span { font-size: 14px; color: var(--ink-2); line-height: 1.45; }\n.choice.ai { border: 1.5px dashed var(--blue); }\n.choice.ai b { color: var(--blue); }\n.choice.me { border-color: var(--purple); }\n.choice.me b { color: var(--purple-text); }\n.remember { display: flex; align-items: center; gap: 10px; margin-top: 14px; font-size: 14px; cursor: pointer; width: fit-content; }\n.ask-btn { min-height: 28px; padding: 0; margin-top: 2px; align-self: flex-start; color: var(--blue) !important; font-size: 13px; }\n.sel-pop { position: absolute; z-index: 40; display: none; }\n.sel-pop.show { display: block; }\n.sel-pop .btn { box-shadow: 0 6px 18px rgba(28,23,51,.25); }\nbutton.ann-code { all: unset; cursor: pointer; font-family: var(--mono); font-weight: 600; font-size: 12px; border-radius: 4px; }\nbutton.ann-code:hover { text-decoration: underline; }\nbutton.ann-type { all: unset; cursor: pointer; font-size: 12px; margin-left: auto; border-radius: 4px; padding: 0 3px; text-decoration: underline dotted; text-underline-offset: 3px; }\nbutton.ann-type:focus-visible, button.ann-code:focus-visible, .ann-del:focus-visible { outline: 2px solid var(--blue); }\n.ann-del { all: unset; cursor: pointer; width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; color: var(--slate); font-size: 16px; line-height: 1; }\n.ann-del:hover { background: var(--no-tint); color: var(--no); }\n.ann.manual { border: 1.5px solid var(--purple); color: var(--ink); }\n.ann.manual .ann-n { background: var(--purple); color: #fff; }\n.ann.manual textarea { font-style: normal; }\n.ann.manual textarea::placeholder { color: var(--slate); }\nmark.ann-mark.by-me { background: var(--purple-tint); border-bottom: 2px solid var(--purple); }\n.work-bar .add-note { margin-left: auto; }\n.work-bar .add-note + label { margin-left: 0; }\n\n/* \u2500\u2500 focus mode: everything but the work and the criterion fades back \u2500\u2500 */\n.topbar, .learner-strip, .wiz-nav { transition: filter .25s ease, opacity .25s ease; }\n.sm-root.focus-on .topbar, .sm-root.focus-on .learner-strip, .sm-root.focus-on .wiz-nav { filter: blur(3px) saturate(.6); opacity: .38; }\n.sm-root.focus-on .topbar:hover, .sm-root.focus-on .topbar:has(:focus-visible), .sm-root.focus-on .wiz-nav:hover, .sm-root.focus-on .wiz-nav:has(:focus-visible) { filter: none; opacity: 1; }\n.sm-root.focus-on .work-bar .fname, .sm-root.focus-on .work-bar h2 { opacity: .55; }\n.desk.focus .ann { transition: filter .2s ease, opacity .2s ease; }\n.desk.focus .ann:not(.lit) { filter: blur(1.6px); opacity: .42; }\n.desk.focus .ann:not(.lit):hover, .desk.focus .ann:not(.lit):focus-within { filter: none; opacity: 1; }\n.desk.focus mark.ann-mark:not(.lit) { background: none; border-bottom: 1px dashed var(--rule); }\n\n/* \u2500\u2500 learner details: named or anonymous \u2500\u2500 */\n.lmode { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 10px; margin: 4px 0 16px; }\n.lm-opt { display: flex; gap: 12px; align-items: flex-start; padding: 14px 16px; border: 1.5px solid var(--rule); border-radius: var(--r-m); cursor: pointer; background: var(--paper); }\n.lm-opt .form-check-input { margin-top: 3px; }\n.lm-opt b { display: block; font-size: 15.5px; }\n.lm-opt small { display: block; font-size: 13.5px; color: var(--ink-2); margin-top: 2px; line-height: 1.4; }\n.lm-opt:has(input:checked) { border-color: var(--purple); background: var(--purple-tint); }\n.sm-root[data-theme=\"dark\"] .lm-opt:has(input:checked) { background: var(--rule-2); }\n.anon-box { padding: 12px 16px; border-radius: var(--r-m); background: var(--rule-2); margin-bottom: 16px; }\n.anon-ref { display: flex; align-items: baseline; flex-wrap: wrap; gap: 4px 12px; }\n.anon-ref strong { font-family: var(--mono); font-size: 18px; letter-spacing: .02em; }\n.step.anon .pii { display: none !important; }\n.privacy-note { display: flex; gap: 8px; align-items: flex-start; font-size: 13px; color: var(--slate); margin: 14px 0 0; }\n.privacy-note svg { flex: none; margin-top: 2px; }\n.privacy-line { display: flex; gap: 8px; align-items: flex-start; font-size: 13.5px; color: var(--ink-2); margin-top: 10px; }\n.privacy-line svg { flex: none; margin-top: 2px; color: var(--yes); }\n\n/* \u2500\u2500 deadlines \u2500\u2500 */\n.date-label { display: flex; align-items: center; justify-content: space-between; gap: 8px; }\n.date-label .btn-link { min-height: 0; padding: 0; font-size: 13px; }\n.ext-row { display: flex; gap: 8px; }\n.ext-row select { flex: 0 0 auto; width: 120px; }\n.ext-row input { flex: 1; min-width: 0; }\n.late-status { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 10px; margin-top: 14px; font-size: 14px; color: var(--ink-2); }\n.late-status:empty { display: none; }\n.late-chip { font-size: 13px; font-weight: 700; padding: 2px 10px; border-radius: 6px; white-space: nowrap; }\n.late-chip.ok { background: var(--yes-tint); color: var(--yes); }\n.late-chip.late { background: var(--no-tint); color: var(--no); }\n.late-chip.ext { background: var(--resub-tint); color: var(--resub); }\n.late-chip.unknown { background: var(--rule-2); color: var(--slate); }\n.learner-strip .tag.late { background: var(--no-tint); color: var(--no); }\n.learner-strip .tag.ext { background: var(--resub-tint); color: var(--resub); }\n\n/* \u2500\u2500 start again \u2500\u2500 */\n.dlg { border: 0; border-radius: var(--r-l); padding: 24px 24px 18px; width: min(600px, calc(100vw - 32px)); background: var(--paper); color: var(--ink); box-shadow: 0 24px 64px rgba(0,0,0,.35); }\n.dlg::backdrop { background: rgba(18,18,22,.5); backdrop-filter: blur(3px); }\n.dlg h2 { font-size: 20px; font-weight: 800; letter-spacing: -0.01em; }\n.dlg .helper { margin-top: 4px; }\n.dlg .choose-grid { margin-top: 16px; }\n.choice.keep { border-color: var(--purple); }\n.choice.keep b { color: var(--purple-text); }\n.choice.wipe { border-color: var(--rule); }\n.choice.wipe:hover { border-color: var(--no); }\n.choice.wipe b { color: var(--no); }\n.dlg-foot { display: flex; justify-content: flex-end; margin-top: 12px; }\n\n/* \u2500\u2500 dark mode \u2500\u2500 */\n.sm-root[data-theme=\"dark\"] {\n  color-scheme: dark;\n  --ground: #161618; --paper: #202023; --ink: #EDEDEF; --ink-2: #C8C8CD; --slate: #A1A1A8; --rule: #37373C; --rule-2: #2A2A2E; --sunken: #1B1B1E;\n  --purple: #7C5CF0; --purple-text: #B19AFF; --purple-ink: #6B4AE6;\n  --purple-tint: color-mix(in srgb, #7C5CF0 16%, #202023);\n  --blue: #7AAAFF; --blue-tint: color-mix(in srgb, #7AAAFF 11%, #202023); --blue-soft: color-mix(in srgb, #7AAAFF 40%, #202023);\n  --yes: #66D7A0; --yes-tint: #19302A; --no: #FF9C72; --no-tint: #382420; --resub: #F1C562; --resub-tint: #332C18;\n  --toast: #2E2E33;\n  --lift: 0 1px 2px rgba(0,0,0,.45), 0 12px 30px rgba(0,0,0,.4);\n}\n.sm-root[data-theme=\"dark\"] .form-select { background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1.5l5 5 5-5' fill='none' stroke='%23B9B4CC' stroke-width='1.8' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E\"); }\n.sm-root[data-theme=\"dark\"] .form-control::placeholder { color: #8E8E96; }\n/* large panels stay grey in dark mode; purple is kept for actions and what's selected */\n.sm-root[data-theme=\"dark\"] .assign-wrap { background: var(--rule-2); }\n.sm-root[data-theme=\"dark\"] .drop-zone:hover, .sm-root[data-theme=\"dark\"] .drop-zone.drag, .sm-root[data-theme=\"dark\"] .drop-zone.has-file { background: var(--rule-2); }\n.sm-root[data-theme=\"dark\"] .file-chip { background: var(--rule); }\n.sm-root[data-theme=\"dark\"] .learner-strip .tag, .sm-root[data-theme=\"dark\"] .grade-pill { background: var(--rule-2); }\n.sm-root[data-theme=\"dark\"] .alert-warning { color: var(--resub); }\n.sm-root[data-theme=\"dark\"] .ann-n { color: var(--ground); }\n.sm-root[data-theme=\"dark\"] .btn-outline-secondary:hover:not(:disabled), .sm-root[data-theme=\"dark\"] .btn-settings:hover { border-color: var(--slate); }\n.sm-root[data-theme=\"dark\"] .offcanvas-backdrop { background: rgba(0,0,0,.55); }\n.sm-root[data-theme=\"dark\"] .text-bg-warning { background: #4A3A12; color: #FFE9B0; }\n.sm-root[data-theme=\"dark\"] .text-bg-warning .btn-close-white { color: #FFE9B0; }\n/* the learner's work stays a light page on the dark desk */\n.sm-root[data-theme=\"dark\"] .paper.doc { background: #F4F4F5; color: #222; --ink: #1C1733; --ink-2: #3A3550; --slate: #5E5A73; --rule: #D6D6DA; --blue: var(--brand-blue, #1D63D8); --blue-tint: #E2EBFB; --blue-soft: #94B4EC; --purple: var(--brand-purple, #5B2BD6); --purple-tint: #ECE6FB; --purple-text: var(--brand-purple, #5B2BD6); }\n.sm-root[data-theme=\"dark\"] .paper.doc h2, .sm-root[data-theme=\"dark\"] .paper.doc h3 { color: #1F3864; }\n.sm-root[data-theme=\"dark\"] .pdf-page { box-shadow: 0 1px 2px rgba(0,0,0,.5), 0 12px 30px rgba(0,0,0,.45); }\n@media (max-width: 1720px) { #restartBtn { width: 44px; padding: 0; justify-content: center; } #restartBtn .lbl { display: none; } .stepper li { width: 84px; } }\n@media (max-width: 1400px) { .draft-status { display: none; } }\n.btn-icon { width: 44px; height: 44px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid var(--rule); border-radius: 10px; background: var(--paper); color: var(--ink); padding: 0; }\n.btn-icon:hover { border-color: var(--slate); }\n\n/* \u2500\u2500 IV sampling and brief IV (iv.js) \u2500\u2500 */\n.sm-root:not(.iv-on) .iv-only { display: none !important; }\n.sm-root.iv-on .not-iv { display: none !important; }\n.sm-root.iv-on .stamp[data-v=\"R\"] { display: none; }\n.wmode { margin: 0 0 18px; }\n.sm-root.iv-on .grade-box { grid-template-columns: 1fr 1fr 1fr; }\n.gb-asr { border-left: 1px solid var(--rule); }\n.gb-asr .gb-v { font-size: 16px; font-weight: 700; color: var(--ink); padding-top: 6px; overflow-wrap: anywhere; }\n.iv-cell { display: flex; flex-wrap: wrap; align-items: center; gap: 4px 8px; font-size: 13px; margin: -4px 0 10px; }\n.asr { border: 1px solid var(--ink-2); border-radius: 6px; padding: 0 8px; line-height: 1.6; color: var(--ink-2); }\n.asr b { color: var(--ink); font-weight: 700; }\n.asr.a-none { border-style: dashed; border-color: var(--rule); color: var(--slate); }\n.verdict { border-radius: 999px; padding: 0 9px; line-height: 1.7; font-size: 12.5px; font-weight: 700; white-space: nowrap; }\n.v-agree { background: var(--yes-tint); color: var(--yes); }\n.v-over { background: var(--no-tint); color: var(--no); }\n.v-under { background: var(--resub-tint); color: var(--resub); }\n.v-none { color: var(--slate); font-weight: 400; }\n.iva-grid { display: flex; flex-direction: column; border-top: 1px solid var(--rule); }\n.iva-row { display: grid; grid-template-columns: minmax(70px, auto) auto 1fr; align-items: center; gap: 6px 14px; padding: 9px 0; border-bottom: 1px solid var(--rule); }\n.iva-opts { display: inline-flex; border: 1px solid var(--rule); border-radius: 8px; overflow: hidden; }\n.iva-opt { all: unset; cursor: pointer; padding: 5px 11px; font-size: 13.5px; font-weight: 600; color: var(--ink-2); border-left: 1px solid var(--rule); }\n.iva-opt:first-child { border-left: 0; }\n.iva-opt:hover { background: var(--purple-tint); }\n.iva-opt:focus-visible { outline: 2px solid var(--purple-text); outline-offset: -2px; }\n.iva-opt.on.o-Y { background: var(--yes-tint); color: var(--yes); }\n.iva-opt.on.o-P { background: var(--resub-tint); color: var(--resub); }\n.iva-opt.on.o-N { background: var(--no-tint); color: var(--no); }\n.iva-ev { font-size: 13px; color: var(--slate); line-height: 1.4; }\n.iva-warn { display: flex; gap: 10px; align-items: flex-start; padding: 10px 12px; margin: 0 0 12px; border-radius: var(--r-m); background: var(--resub-tint); color: var(--ink); font-size: 14px; }\n.iva-warn ul { margin: 4px 0 0; padding-left: 18px; }\n@media (max-width: 640px) { .iva-row { grid-template-columns: 1fr; } }\n.iv-side .ivc { display: flex; flex-direction: column; margin-top: 6px; }\n.ivc-row, .biv-row { display: grid; grid-template-columns: 1fr auto; align-items: center; gap: 4px 10px; padding: 7px 0; border-bottom: 1px solid var(--rule); font-size: 13.5px; }\n.ivc-row .ivc-mine { grid-column: 1 / -1; font-size: 11.5px; color: var(--purple-text); margin-top: -2px; }\n.ivc-overall .ivc-q { font-weight: 700; }\n.ivc-q { line-height: 1.35; color: var(--ink-2); }\n.ivc-yn { display: inline-flex; border: 1px solid var(--rule); border-radius: 8px; overflow: hidden; }\n.ivc-btn { all: unset; cursor: pointer; min-width: 30px; text-align: center; padding: 3px 8px; font-weight: 700; font-size: 13px; color: var(--slate); border-left: 1px solid var(--rule); }\n.ivc-btn:first-child { border-left: 0; }\n.ivc-btn:hover { background: var(--purple-tint); }\n.ivc-btn:focus-visible { outline: 2px solid var(--purple-text); outline-offset: -2px; }\n.ivc-btn.on.o-Y { background: var(--yes-tint); color: var(--yes); }\n.ivc-btn.on.o-N { background: var(--no-tint); color: var(--no); }\n.iv-target { display: flex; align-items: center; gap: 10px; margin-top: 8px; }\n.iv-target .field-label { margin: 0; white-space: nowrap; }\n.iv-target input { max-width: 150px; }\n.iv-sum-list { list-style: none; padding: 0; margin: 4px 0 0; border-top: 1px solid var(--rule); }\n.iv-sum-list li { display: flex; justify-content: space-between; gap: 12px; padding: 6px 0; border-bottom: 1px solid var(--rule); font-size: 14px; }\n.iv-sum-list b.no { color: var(--no); }\n.dlg-wide { width: min(880px, calc(100vw - 32px)); max-height: calc(100vh - 40px); overflow-y: auto; }\n.dlg-wide .field-group { margin-top: 10px; }\n.biv-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 0 14px; }\n.biv-check { margin-top: 14px; border-top: 1px solid var(--rule); }\n.biv-row { grid-template-columns: minmax(0, 1.3fr) auto minmax(0, 1fr); }\n.biv-actions { display: grid; grid-template-columns: 1fr 170px; gap: 0 14px; align-items: start; }\n.biv-foot { gap: 8px; flex-wrap: wrap; margin-top: 16px; }\n@media (max-width: 700px) { .biv-row, .biv-actions { grid-template-columns: 1fr; } }\n\n/* \u2500\u2500 Batch marking (batch.js) \u2500\u2500 */\n.sm-root:not(.batch-on) .batch-only { display: none !important; }\n.sm-root.batch-on .not-batch { display: none !important; }\n.sm-root.batch-hidden .batch-opt { display: none !important; }\n.sm-root.batch-none .pii, .sm-root.batch-none #anonBox, .sm-root.batch-none #resubCard { display: none !important; }\n.sm-root.batch-none .step[data-step=\"2\"] > .card:first-child { border-radius: var(--r-l); }\n.wmode:has(.batch-opt) { grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); }\n.batch-note { margin: 0 0 14px; padding: 10px 14px; border-radius: var(--r-s); background: var(--purple-tint); color: var(--ink); font-size: 14px; }\n.bt-head { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 10px 16px; margin-bottom: 14px; }\n.bt-tools, .bt-foot { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }\n.bt-foot { margin-top: 14px; }\n.bt-drop { padding: 36px 20px; }\n#batchCard.drag { outline: 2px dashed var(--purple-text); outline-offset: 3px; }\n.bt-scroll { overflow-x: auto; border: 1px solid var(--rule); border-radius: var(--r-m); }\n.bt-table { margin: 0; font-size: 14px; width: 100%; min-width: 680px; --bs-table-bg: transparent; --bs-table-color: var(--ink); }\n.bt-table th { font-size: 12.5px; font-weight: 700; color: var(--slate); background: var(--sunken); border-bottom: 1px solid var(--rule); padding: 8px 10px; white-space: nowrap; }\n.bt-table td { vertical-align: middle; border-color: var(--rule-2); padding: 6px 10px; }\n.bt-table tr:last-child td { border-bottom: 0; }\n.bt-table tr.is-open td { background: var(--purple-tint); }\n.bt-table .bt-n { width: 34px; color: var(--slate); font-variant-numeric: tabular-nums; text-align: right; }\n.bt-table .bt-name { min-width: 130px; }\n.bt-table .bt-reg { width: 92px; }\n.bt-table .field-input { padding: 4px 8px; font-size: 14px; min-height: 0; height: 36px; border-radius: 8px; }\n.bt-file { max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: var(--slate); }\n.bt-anon { font-weight: 700; }\n.bt-act { white-space: nowrap; text-align: right; }\n.bt-act .btn-link { margin-left: 2px; padding-inline: 4px; }\n.bt-why { font-size: 12.5px; color: var(--slate); margin-top: 3px; min-width: 200px; max-width: 260px; white-space: normal; line-height: 1.35; }\n.bt-table .grade-pill { font-size: 12px; margin-left: 4px; vertical-align: middle; }\n.bt-status { display: inline-flex; align-items: center; gap: 5px; font-size: 13px; font-weight: 700; white-space: nowrap; padding: 1px 8px; border-radius: 6px; color: var(--slate); background: var(--rule-2); }\n.bt-status .spinner-border { width: 11px; height: 11px; border-width: 2px; }\n.bt-status.s-checked { color: var(--blue); background: var(--blue-tint); }\n.bt-status.s-needs { color: var(--no); background: var(--no-tint); cursor: help; }\n.bt-status.s-marking { color: var(--purple-text); background: var(--purple-tint); }\n.bt-status.s-ready, .bt-status.s-done { color: var(--yes); background: var(--yes-tint); }\n.bt-late { font-size: 12px; font-weight: 700; color: var(--no); }\n.page > .batch-strip { width: calc(100% - 56px); max-width: calc(var(--w, 960px) - 56px); margin: 20px auto -8px; }\n@media (max-width: 640px) { .page > .batch-strip { width: calc(100% - 32px); } }\n.batch-strip { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 16px; margin: 0 0 14px; padding: 8px 10px 8px 14px; border-radius: var(--r-m); background: var(--paper); border: 1px solid var(--rule); }\n.bs-where b { color: var(--purple-text); margin-right: 6px; }\n.bs-meta { color: var(--slate); font-size: 14px; flex: 1; }\n.bs-btns { display: flex; gap: 6px; align-items: center; }\n.sm-root[data-theme=\"dark\"] .bt-status { background: var(--rule-2); }\n@media (max-width: 700px) { .bs-meta { flex-basis: 100%; order: 3; } }\n\n/* \u2500\u2500 QuickMarks (quickmarks.js) \u2500\u2500 */\n.qm-bar { display: flex; align-items: center; gap: 8px; margin: -4px 0 12px; min-width: 0; }\n.qm-label { font-size: 12.5px; font-weight: 700; color: var(--purple-text); white-space: nowrap; }\n.qm-chips { display: flex; gap: 6px; overflow-x: auto; scrollbar-width: thin; padding: 2px 2px 4px; min-width: 0; flex: 1; }\n.qm-chip { all: unset; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; font-size: 13px; font-weight: 600; color: var(--purple-text); border: 1.5px solid color-mix(in srgb, var(--purple-text) 55%, transparent); border-radius: 7px; padding: 2px 9px; background: var(--paper); transition: background-color .12s; }\n.qm-chip:hover { background: var(--purple-tint); }\n.qm-chip:focus-visible { outline: 2px solid var(--purple-text); outline-offset: 1px; }\n.qm-chip:active { transform: translateY(1px); }\n.qm-chip kbd { font: 600 11px var(--mono); color: var(--slate); border: 1px solid var(--rule); border-radius: 4px; padding: 0 4px; background: transparent; }\n.qm-chip.t-met { color: var(--yes); border-color: color-mix(in srgb, var(--yes) 55%, transparent); }\n.qm-chip.t-not_met { color: var(--no); border-color: color-mix(in srgb, var(--no) 55%, transparent); }\n.qm-chip.small { font-size: 12.5px; padding: 1px 8px; box-shadow: 0 4px 12px rgba(28,23,51,.15); }\n.qm-edit { flex: none; }\n.qm-pop { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 6px; max-width: 380px; }\n.qm-pop:empty { display: none; }\n.ann-qm { font-size: 11.5px; font-weight: 700; color: var(--purple-text); border: 1px solid color-mix(in srgb, var(--purple-text) 45%, transparent); border-radius: 5px; padding: 0 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 9em; min-width: 0; }\n.ann.qm-new { animation: qmflash 1.1s ease-out; }\n@keyframes qmflash { from { box-shadow: 0 0 0 3px var(--purple-tint); } to { box-shadow: none; } }\n@media (prefers-reduced-motion: reduce) { .ann.qm-new { animation: none; } }\n.qm-list { display: flex; flex-direction: column; gap: 6px; margin-top: 12px; }\n.qm-item { display: grid; grid-template-columns: 20px minmax(110px, 0.9fr) minmax(160px, 2fr) 110px auto; gap: 6px; align-items: center; }\n.qm-n { font: 600 12px var(--mono); color: var(--slate); text-align: right; }\n.qm-move { display: inline-flex; gap: 4px; }\n.qm-move .btn { padding: 2px 8px; }\n.qm-tools { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 10px; margin-top: 12px; }\n#qmBody code { font-family: var(--mono); font-size: 12.5px; background: var(--purple-tint); padding: 0 4px; border-radius: 4px; }\n@media (max-width: 700px) { .qm-item { grid-template-columns: 20px 1fr; } .qm-item > :not(.qm-n) { grid-column: 2; } }\n/* ---- Crafticulum's look: the marking screens take the app's own colours, font, corners and light or dark mode ---- */\n:host{display:block}\n.sm-root{--ground:var(--cc-canvas,#f5f6fa);--paper:var(--cc-surface,#fff);--ink:var(--cc-ink,#0f172a);--ink-2:color-mix(in srgb,var(--cc-ink,#0f172a) 82%,var(--cc-surface,#fff));--slate:var(--cc-muted,#5c6579);--rule:var(--cc-line-strong,#d4d8e3);--rule-2:var(--cc-surface-2,#eef0f6);--sunken:color-mix(in srgb,var(--cc-surface-2,#f1f3f8) 50%,var(--cc-surface,#fff));--toast:#0f172a;\n  --purple:var(--cc-primary,#4f46e5);--purple-text:var(--cc-primary-text,#4338ca);--purple-ink:var(--cc-primary-hover,#4338ca);--purple-tint:var(--cc-primary-soft,#eef2ff);--blue:#2563eb;\n  --sans:var(--bs-body-font-family,\"Plus Jakarta Sans\",system-ui,sans-serif);--r-s:8px;--r-m:10px;--r-l:14px;--lift:0 1px 2px rgba(15,23,42,.05),0 4px 16px rgba(15,23,42,.06);\n  --cc-top:58px;position:relative;font-size:14.5px;text-align:left}\n.sm-root[data-theme=\"dark\"]{--ground:var(--cc-canvas,#0a0d16);--paper:var(--cc-surface,#111623);--ink:var(--cc-ink,#e7eaf3);--slate:var(--cc-muted,#9aa3b8);--rule:var(--cc-line-strong,#2f3850);--rule-2:var(--cc-surface-2,#171d2d);--toast:#232a3b;\n  --purple:var(--cc-primary,#6366f1);--purple-text:var(--cc-primary-text,#a5b4fc);--purple-ink:var(--cc-primary-hover,#7477f5);--purple-tint:var(--cc-primary-soft,rgba(99,102,241,.16));--blue:#7aaaff;--blue-tint:color-mix(in srgb,#7aaaff 11%,var(--cc-surface,#111623));--blue-soft:color-mix(in srgb,#7aaaff 40%,var(--cc-surface,#111623))}\n.sm-root .page{min-height:calc(100vh - var(--cc-top))}\n.sm-root .topbar .brand,.sm-root #themeBtn,.sm-root #settingsToggle{display:none!important}\n.sm-root .topbar{position:sticky;top:var(--cc-top);z-index:30;flex-wrap:nowrap;padding:10px clamp(16px,3vw,32px);gap:10px 20px;background:color-mix(in srgb,var(--ground) 90%,transparent);backdrop-filter:blur(8px)}\n.sm-root .stepper{margin:0;order:0;flex:1 1 auto;min-width:0;justify-content:flex-start;overflow-x:auto}\n.sm-root .top-right{gap:8px}.sm-root .btn-settings,.sm-root .btn-icon{height:38px;border-radius:.625rem;font-size:13.5px}.sm-root .btn-icon{width:38px}\n.sm-root .page>.step,.sm-root .page>.wiz-nav,.sm-root .page>.draft-banner,.sm-root .page>.info-box{padding-inline:clamp(16px,3vw,32px)}\n.sm-root .card{border-radius:.9rem}.sm-root .btn,.sm-root .form-control,.sm-root .form-select,.sm-root .field-input{border-radius:.625rem}\n.sm-root .btn-primary{border-color:transparent}.sm-root .step-title{letter-spacing:-.02em}\n.sm-root .marksheet,.sm-root .sidepane{top:calc(var(--cc-top) + 74px);max-height:calc(100vh - var(--cc-top) - 170px)}\n.sm-root .wiz-nav{bottom:var(--cc-bottom,0px)}\n@media (max-width:640px){.sm-root{--cc-top:44px}.sm-root .stepper{gap:4px}.sm-root .stepper li{width:auto;flex:1 1 0;min-width:0;font-size:11px}.sm-root .stepper li>button,.sm-root .stepper li>span.sl{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}}\n@media (max-width:991.98px){.sm-root{--cc-bottom:64px}}\n/* the checks (step 4) and the class set card wear the same clothes as the app's own steps: the same card, and a numbered heading on each */\n.sm-root .step[data-step=\"3\"],.sm-root .step[data-step=\"4\"]{counter-reset:smc}\n.sm-root .step[data-step=\"4\"] .card,.sm-root #batchCard{padding:20px 22px;border-color:var(--cc-line,#e5e7ef);box-shadow:0 1px 3px rgba(15,23,42,.06),0 8px 24px -12px rgba(15,23,42,.12)}\n.sm-root .step[data-step=\"4\"] .card:empty{display:none}\n.sm-root .step[data-step=\"4\"] .card .step-title,.sm-root .step[data-step=\"4\"] .card>.label,.sm-root .step[data-step=\"4\"] .ai-grade>.label,.sm-root #batchCard .step-title{display:flex;align-items:center;gap:8px;font-size:.78rem;font-weight:700;letter-spacing:0;line-height:1.3;margin:0 0 .55rem!important;color:var(--bs-heading-color,inherit)}\n.sm-root .step[data-step=\"4\"] .card .step-title::before,.sm-root .step[data-step=\"4\"] .card>.label::before,.sm-root .step[data-step=\"4\"] .ai-grade>.label::before,.sm-root #batchCard .step-title::before{counter-increment:smc;content:counter(smc);flex:none;width:20px;height:20px;border-radius:50%;display:inline-grid;place-items:center;font-size:.68rem;font-weight:800;color:var(--cc-primary-text);background:var(--cc-primary-soft);border:1px solid rgba(var(--cc-primary-rgb),.25)}\n.sm-root .step[data-step=\"4\"] .ai-grade .big{margin:.2rem 0 .5rem}.sm-root .step[data-step=\"4\"] .ai-grade .grade-pill{font-size:.95rem;padding:.25rem .8rem;border-radius:99px}\n/* the marking desk (step 5) in the same clothes: a slim learner bar, the mark sheet and feedback as cards with numbered headings, codes as pills, and green, red and amber for Y, N and R. Looks only: nothing here moves the work or its comments */\n.sm-root .step[data-step=\"5\"]{counter-reset:smc}\n.sm-root .learner-strip{align-items:center;gap:6px 12px;padding:11px 18px;margin:0 0 16px;background:var(--paper);border:1px solid var(--cc-line,#e5e7ef);border-radius:.9rem;box-shadow:0 1px 3px rgba(15,23,42,.06)}\n.sm-root .learner-strip h2{font-size:1.05rem;font-weight:700;letter-spacing:-.01em;line-height:1.3;margin:0}\n.sm-root .learner-strip .meta{font-size:.85rem}.sm-root .learner-strip .tag{font-size:.74rem;padding:.2rem .65rem;border-radius:99px}\n.sm-root .desk:not(.focus) .marksheet{padding:16px 16px 14px;background:var(--paper);border:1px solid var(--cc-line,#e5e7ef);border-radius:.9rem;box-shadow:0 1px 3px rgba(15,23,42,.06),0 8px 24px -12px rgba(15,23,42,.12)}\n.sm-root .desk:not(.focus) .sidepane>div{padding:16px;background:var(--paper);border:1px solid var(--cc-line,#e5e7ef);border-radius:.9rem;box-shadow:0 1px 3px rgba(15,23,42,.06),0 8px 24px -12px rgba(15,23,42,.12)}\n.sm-root .desk:not(.focus) .sidepane>div:empty{display:none}\n.sm-root .desk:not(.focus) .ms-head h2,.sm-root .desk:not(.focus) .work-bar h2,.sm-root .desk:not(.focus) .sidepane h2,.sm-root .desk:not(.focus) .sidepane .sp-label{display:flex;align-items:center;gap:8px;font-size:.78rem;font-weight:700;letter-spacing:0;line-height:1.3;margin:0;color:var(--bs-heading-color,inherit)}\n.sm-root .desk:not(.focus) .ms-head h2::before,.sm-root .desk:not(.focus) .work-bar h2::before,.sm-root .desk:not(.focus) .sidepane h2::before{content:\"1\";flex:none;width:20px;height:20px;border-radius:50%;display:inline-grid;place-items:center;font-size:.68rem;font-weight:800;color:var(--cc-primary-text);background:var(--cc-primary-soft);border:1px solid rgba(var(--cc-primary-rgb),.25)}\n.sm-root .desk:not(.focus) .work-bar h2::before{content:\"2\"}.sm-root .desk:not(.focus) .sidepane h2::before{content:\"3\"}\n.sm-root .desk:not(.focus) .ms-head h2,.sm-root .ms-head .count,.sm-root .ms-head .btn{white-space:nowrap}.sm-root .desk:not(.focus) .ms-head{gap:8px;flex-wrap:wrap}.sm-root .desk:not(.focus) .ms-head .btn{margin-left:auto;font-size:.78rem;padding:.25rem .6rem;min-height:0}\n.sm-root .desk:not(.focus) .sidepane h2{margin-bottom:.55rem}.sm-root .desk:not(.focus) .sidepane h2 .fb-pre{font-weight:700}\n.sm-root .ms-head .count{font-size:.78rem}\n.sm-root .grade-box{border-color:var(--cc-line,#e5e7ef);border-radius:.75rem}\n.sm-root .desk:not(.focus) .grade-big{font-size:1.15rem;line-height:1.25}.sm-root .desk:not(.focus) .gb-ai .gb-v{font-size:1.05rem;font-style:normal;font-weight:600}\n.sm-root .grade-box .gb-k{font-size:.74rem;font-weight:700}\n.sm-root .desk:not(.focus) .crit-row{border-radius:.75rem;border-bottom-color:var(--cc-line,#e5e7ef)}\n.sm-root .desk:not(.focus) .crit-row.current{border-width:1.5px;border-radius:.75rem;box-shadow:0 0 0 3px var(--cc-primary-soft)}\n.sm-root .desk:not(.focus) .code-input{font-family:inherit;font-size:.78rem;font-weight:700;letter-spacing:0;padding:.15rem .55rem;border-radius:.4rem;background:var(--cc-primary-soft);color:var(--cc-primary-text);width:auto;min-width:3ch}\n.sm-root .band-tag{font-size:.74rem}\n.sm-root .desk:not(.focus) .crit-desc{font-size:.86rem}\n.sm-root .desk:not(.focus) .stamp{width:38px;height:38px;border-radius:.625rem;border-color:var(--cc-line-strong,#d4d8e3)}\n.sm-root .desk:not(.focus) .stamp b{font-size:.92rem}\n.sm-root .stamp.on,.sm-root .stamp.on:nth-child(3){transform:none;box-shadow:none}\n.sm-root .stamp[data-v=\"Y\"].on{background:#15803d;border-color:#15803d;color:#fff}\n.sm-root .stamp[data-v=\"N\"].on{background:#b91c1c;border-color:#b91c1c;color:#fff}\n.sm-root .stamp[data-v=\"R\"].on{background:#b45309;border-color:#b45309;color:#fff}\n.sm-root .desc-input,.sm-root .feedback-box{border-color:var(--cc-line-strong,#d4d8e3);border-radius:.625rem;font-size:.875rem}\n.sm-root .sugg{border-radius:99px;font-style:normal;font-size:.78rem;padding:.05rem .6rem}\n.sm-root .qm-chip{border-radius:99px;border-width:1px;font-size:.78rem;padding:.15rem .7rem}.sm-root .qm-label{font-size:.72rem;text-transform:uppercase;letter-spacing:.04em}\n.sm-root .work-bar .fname{font-size:.8rem}.sm-root .paper{border-radius:.5rem;border:1px solid var(--cc-line,#e5e7ef)}\n.sm-root .keys{font-size:.74rem}\n.sm-root .wiz-nav .btn{min-height:42px;font-size:.9rem;border-radius:.625rem}\n@media (pointer:coarse){.sm-root .desk:not(.focus) .stamp{width:44px;height:44px}}\n";
  const SM_HTML = "<div class=\"page\">\n  <header class=\"topbar\">\n    <div class=\"brand\">\n      <svg class=\"brand-mark\" width=\"32\" height=\"32\" viewBox=\"0 0 30 30\" aria-hidden=\"true\"><path d=\"M5 16.5l5.5 5.5L24 8.5\" fill=\"none\" style=\"stroke: var(--blue)\" stroke-width=\"2.4\" stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-dasharray=\"3 3.2\"/><path d=\"M7.5 18l5.5 5.5L26.5 10\" fill=\"none\" style=\"stroke: var(--purple)\" stroke-width=\"3.4\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/></svg>\n      <h1>Second Marker</h1>\n      <div class=\"ctx\" id=\"ctxLine\"><span class=\"c1\">BTEC assignment checker</span><span class=\"c2\">Choose a unit to start</span></div>\n    </div>\n    <ol class=\"stepper\" id=\"stepper\" aria-label=\"Progress\"></ol>\n    <div class=\"top-right\"><span class=\"draft-status\" id=\"draftStatus\" aria-live=\"polite\"></span>\n      <button type=\"button\" class=\"btn-settings\" id=\"restartBtn\" data-sm-click=\"openRestart()\" aria-label=\"Start again\" title=\"Start again\"><svg viewBox=\"0 0 24 24\" width=\"17\" height=\"17\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"1.9\" stroke-linecap=\"round\" stroke-linejoin=\"round\" aria-hidden=\"true\"><path d=\"M3.5 12a8.5 8.5 0 1 0 2.6-6.1\"/><path d=\"M3.5 4v4.5H8\"/></svg><span class=\"lbl\">Start again</span></button>\n      <button type=\"button\" class=\"btn-icon\" id=\"themeBtn\" data-sm-click=\"toggleTheme()\" aria-label=\"Switch to dark mode\"></button>\n      <button class=\"btn-settings\" data-sm-click=\"toggleSettings()\" id=\"settingsToggle\"><svg viewBox=\"0 0 24 24\" width=\"18\" height=\"18\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"1.8\" stroke-linecap=\"round\" stroke-linejoin=\"round\" aria-hidden=\"true\"><path d=\"M4 6h10M18 6h2M4 12h4M12 12h8M4 18h12\"/><circle cx=\"16\" cy=\"6\" r=\"2\"/><circle cx=\"10\" cy=\"12\" r=\"2\"/><circle cx=\"18\" cy=\"18\" r=\"2\"/></svg>Settings</button>\n    </div>\n  </header>\n\n  <div class=\"offcanvas offcanvas-end\" tabindex=\"-1\" id=\"settingsCard\" aria-labelledby=\"settingsTitle\">\n    <div class=\"offcanvas-header\">\n      <h2 class=\"offcanvas-title\" id=\"settingsTitle\">Settings</h2>\n      <button type=\"button\" class=\"btn-close\" aria-label=\"Close\" data-sm-click=\"toggleSettings(false)\"></button>\n    </div>\n    <div class=\"offcanvas-body\">\n    <div class=\"settings-row\" style=\"margin-bottom:10px\">\n      <span class=\"field-label\" style=\"min-width:110px\">AI model</span>\n      <div class=\"model-row\">\n        <label><input class=\"form-check-input\" type=\"radio\" name=\"model\" value=\"claude-opus-5-5\" checked> Opus 5.5 <span class=\"model-note\">(most capable)</span></label>\n        <label><input class=\"form-check-input\" type=\"radio\" name=\"model\" value=\"claude-sonnet-5-5\"> Sonnet 5.5 <span class=\"model-note\">(fast, cheaper)</span></label>\n        <label><input class=\"form-check-input\" type=\"radio\" name=\"model\" value=\"claude-haiku-5-5\"> Haiku 5.5 <span class=\"model-note\">(fastest, lowest cost)</span></label>\n      </div>\n    </div>\n    <div class=\"settings-row\" style=\"margin-bottom:10px\">\n      <span class=\"field-label\" style=\"min-width:110px\">API key</span>\n      <div class=\"key-box\">\n        <div class=\"input-group\">\n          <input class=\"form-control field-input\" id=\"apiKeyInput\" type=\"password\" autocomplete=\"off\" spellcheck=\"false\" placeholder=\"Paste your Anthropic API key (sk-ant-...)\" data-sm-keydown=\"if(event.key==='Enter') saveKey()\">\n          <button class=\"btn btn-sm btn-outline-secondary btn-csv\" data-sm-click=\"toggleKeyVisible()\" id=\"keyShowBtn\">Show</button>\n          <button class=\"btn btn-primary btn-export key-save\" data-sm-click=\"saveKey()\">Save key</button>\n          <button class=\"btn btn-sm btn-outline-secondary btn-csv\" data-sm-click=\"clearKey()\">Clear</button>\n        </div>\n        <span id=\"keyStatus\" class=\"helper\" style=\"margin-top:4px;display:block\"></span>\n      </div>\n    </div>\n    <div class=\"settings-row\">\n      <span class=\"field-label\" style=\"min-width:110px\">When work is uploaded</span>\n      <div class=\"model-row\">\n        <label><input class=\"form-check-input\" type=\"radio\" name=\"checkMode\" value=\"ask\" data-sm-change=\"setCheckMode(this.value)\"> Ask me each time</label>\n        <label><input class=\"form-check-input\" type=\"radio\" name=\"checkMode\" value=\"ai\" data-sm-change=\"setCheckMode(this.value)\"> Check it with the second marker <span class=\"model-note\">(AI)</span></label>\n        <label><input class=\"form-check-input\" type=\"radio\" name=\"checkMode\" value=\"manual\" data-sm-change=\"setCheckMode(this.value)\"> Mark it myself</label>\n      </div>\n    </div>\n    <div class=\"settings-row\">\n      <span class=\"field-label\" style=\"min-width:110px\">Batch marking</span>\n      <div class=\"model-row\">\n        <label><input class=\"form-check-input\" type=\"radio\" name=\"batchOption\" value=\"on\" checked data-sm-change=\"setBatchOption(true)\"> On <span class=\"model-note\">(offer \"Marking a class set\" in step 1)</span></label>\n        <label><input class=\"form-check-input\" type=\"radio\" name=\"batchOption\" value=\"off\" data-sm-change=\"setBatchOption(false)\"> Off <span class=\"model-note\">(one learner at a time only)</span></label>\n      </div>\n      <p class=\"helper\" style=\"margin:0\">Mark a whole class at once: add every learner's file together, the second marker checks them in the background, and you download all the records at the end.</p>\n    </div>\n    <div class=\"settings-row\">\n      <span class=\"field-label\" style=\"min-width:110px\">PDFs with personal details</span>\n      <div class=\"model-row\">\n        <label><input class=\"form-check-input\" type=\"radio\" name=\"pdfPrivacy\" value=\"text\" data-sm-change=\"setPdfPrivacy(this.value)\"> Send as text with them hidden <span class=\"model-note\">(images not sent)</span></label>\n        <label><input class=\"form-check-input\" type=\"radio\" name=\"pdfPrivacy\" value=\"full\" data-sm-change=\"setPdfPrivacy(this.value)\"> Send the whole PDF <span class=\"model-note\">(details stay in it)</span></label>\n      </div>\n      <p class=\"helper\" style=\"margin:0\">Word files are always sent as text with personal details hidden. A PDF with none in its text has only its file details removed and is sent whole.</p>\n    </div>\n    <div class=\"settings-row\">\n      <span class=\"field-label\" style=\"min-width:110px\">Appearance</span>\n      <div class=\"model-row\">\n        <label><input class=\"form-check-input\" type=\"radio\" name=\"theme\" value=\"system\" data-sm-change=\"setTheme(this.value)\"> Match my computer</label>\n        <label><input class=\"form-check-input\" type=\"radio\" name=\"theme\" value=\"light\" data-sm-change=\"setTheme(this.value)\"> Light</label>\n        <label><input class=\"form-check-input\" type=\"radio\" name=\"theme\" value=\"dark\" data-sm-change=\"setTheme(this.value)\"> Dark</label>\n      </div>\n    </div>\n    <div class=\"settings-row agree-row\">\n      <span class=\"field-label\" style=\"min-width:110px\">Second marker agreement</span>\n      <div class=\"agree-box\">\n        <p class=\"helper\" id=\"agreeSummary\" style=\"margin:0\"></p>\n        <div id=\"agreeTools\" hidden style=\"margin-top:6px\">\n          <button class=\"btn btn-sm btn-outline-secondary btn-csv\" data-sm-click=\"downloadAgreement()\">Download as CSV</button>\n          <button class=\"btn btn-link btn-sm linkish\" data-sm-click=\"clearAgreement()\">Clear</button>\n        </div>\n      </div>\n    </div>\n    <div class=\"settings-row\">\n      <span class=\"field-label\" style=\"min-width:110px\">Word template</span>\n      <span id=\"templateStatus\" class=\"helper\" style=\"margin-top:0\"><span class=\"tick\">\u2713</span> Pearson template built in</span>\n      <button class=\"btn btn-sm btn-outline-secondary btn-csv\" data-sm-click=\"document.getElementById('templateInput').click()\">Replace template</button>\n      <input type=\"file\" id=\"templateInput\" accept=\".docx\" data-sm-change=\"handleTemplate(this.files[0])\">\n    </div>\n  </div>\n\n  </div>\n  <div class=\"offcanvas-backdrop fade\" id=\"settingsBackdrop\" hidden data-sm-click=\"toggleSettings(false)\"></div>\n  <div class=\"draft-banner\" id=\"draftBanner\" style=\"display:none\">\n    <div class=\"dtext\" id=\"draftText\"></div>\n    <button class=\"btn btn-primary btn-sm\" data-sm-click=\"resumeDraft()\">Resume marking</button>\n    <button class=\"btn btn-outline-secondary btn-sm\" data-sm-click=\"discardDraft()\">Start fresh</button>\n  </div>\n\n  <div class=\"batch-strip\" id=\"batchStrip\" hidden></div>\n\n  <!-- STEP 1: UNIT -->\n  <section class=\"step\" data-step=\"1\"><slot name=\"s1\"></slot><div class=\"sm-old\" style=\"display:none\" aria-hidden=\"true\">\n    <div class=\"card\">\n      <div class=\"lmode wmode\" role=\"radiogroup\" aria-label=\"What are you doing?\">\n        <label class=\"lm-opt\"><input class=\"form-check-input\" type=\"radio\" name=\"workMode\" value=\"mark\" checked data-sm-change=\"setWorkMode('mark')\"><span><b id=\"wmMarkTitle\">Marking work</b><small>Assess a learner's submission and write their record and feedback.</small></span></label>\n        <label class=\"lm-opt batch-opt\"><input class=\"form-check-input\" type=\"radio\" name=\"workMode\" value=\"class\" data-sm-change=\"setWorkMode('class')\"><span><b>Marking a class set</b><small>Add the whole class's work at once. The second marker checks it in the background while you mark each learner.</small></span></label>\n        <label class=\"lm-opt\"><input class=\"form-check-input\" type=\"radio\" name=\"workMode\" value=\"iv\" data-sm-change=\"setWorkMode('iv')\"><span><b>IV sampling</b><small>Check a colleague's marked work. The second marker reads the learner's work without seeing their marking, then you compare.</small></span></label>\n      </div>\n      <div class=\"step-title\">Which unit is this assignment for?</div>\n      <div class=\"field-grid\" style=\"grid-template-columns:1fr 1fr 1fr\">\n        <div class=\"field-group\">\n          <span class=\"field-label\">Subject</span>\n          <select class=\"form-select field-input\" id=\"subjectSelect\" data-sm-change=\"onSubjectChange()\">\n            <option value=\"\">Select a subject...</option>\n          </select>\n        </div>\n        <div class=\"field-group\">\n          <span class=\"field-label\">Level</span>\n          <select class=\"form-select field-input\" id=\"levelSelect\" data-sm-change=\"onLevelChange()\" disabled>\n            <option value=\"\">Select a level...</option>\n          </select>\n        </div>\n        <div class=\"field-group\">\n          <span class=\"field-label\">Unit</span>\n          <select class=\"form-select field-input\" id=\"unitSelect\" data-sm-change=\"onUnitChange()\" disabled>\n            <option value=\"\">Select a unit...</option>\n          </select>\n        </div>\n      </div>\n      <div class=\"combine\" id=\"combineWrap\" style=\"display:none\">\n        <button class=\"btn btn-link btn-sm linkish\" id=\"combineToggle\" data-sm-click=\"toggleCombine()\">+ Combine with other units</button>\n        <span class=\"helper\" id=\"combineSummary\" style=\"margin:0\"></span>\n        <div class=\"combine-list\" id=\"combineList\" style=\"display:none\"></div>\n      </div>\n      <div class=\"field-group assign-wrap\" id=\"assignWrap\" style=\"display:none\">\n        <label class=\"field-label\" for=\"builtinBrief\">Assignment</label>\n        <select class=\"form-select field-input\" id=\"builtinBrief\" data-sm-change=\"chooseBuiltinBrief(this.value)\"></select>\n        <div class=\"assign-info\" id=\"builtinBriefInfo\"></div>\n      </div>\n      <div class=\"field-group\" style=\"margin-top:10px\">\n        <span class=\"field-label\">Programme title</span>\n        <select class=\"form-select field-input\" id=\"programmeSelect\" style=\"display:none;margin-bottom:6px\" data-sm-change=\"if(this.value) document.getElementById('programme_title').value = this.value\"></select>\n        <input class=\"form-control field-input\" id=\"programme_title\" type=\"text\" placeholder=\"Autofilled from subject and level\">\n      </div>\n    </div>\n\n    <div class=\"card\">\n      <div class=\"step-title\">Which criteria does it target?</div>\n      <p class=\"helper\" style=\"margin:0 0 10px\">Tick the criteria this assignment covers. Use the learning aim buttons for a quick pick.</p>\n      <div class=\"chips\" id=\"aimChips\"></div>\n      <div id=\"critPick\" class=\"list-group crit-pick\"><p class=\"helper\">Choose a unit above to load its criteria.</p></div>\n      <details class=\"more\">\n        <summary>Add a criterion manually or import from CSV</summary>\n        <div class=\"add-crit\">\n          <input class=\"form-control form-control-sm code-input\" id=\"newCritCode\" placeholder=\"A.P1\" style=\"width:90px\">\n          <input class=\"form-control form-control-sm desc-input\" id=\"newCritDesc\" placeholder=\"Criterion wording\">\n          <button class=\"btn btn-sm btn-outline-secondary btn-csv\" data-sm-click=\"addCustomCriterion()\">Add</button>\n        </div>\n        <div class=\"csv-bar\" style=\"margin-top:8px\">\n          <button class=\"btn btn-sm btn-outline-secondary btn-csv\" data-sm-click=\"document.getElementById('criteriaCsvInput').click()\">Import criteria from CSV</button>\n          <span class=\"helper\" id=\"criteriaCsvStatus\" style=\"margin-top:0\"></span>\n        </div>\n        <input type=\"file\" id=\"criteriaCsvInput\" accept=\".csv\" data-sm-change=\"handleCriteriaCsv(this.files[0])\">\n      </details>\n    </div>\n\n    <div class=\"card\">\n      <div class=\"label\">Assignment brief <span style=\"font-weight:400;text-transform:none;letter-spacing:0;font-size:11px\">(optional)</span></div>\n      <p class=\"helper\" style=\"margin-bottom:10px\" id=\"briefHelp\">Add the brief the learners worked from. The AI uses it to see which task covers which criterion, so the feedback can send the learner back to the right task. It stays loaded for the next learner.</p>\n      <div class=\"csv-bar\" style=\"margin:0\">\n        <button class=\"btn btn-sm btn-outline-secondary btn-csv\" id=\"briefBtn\" data-sm-click=\"document.getElementById('briefInput').click()\">Add the brief</button>\n        <span id=\"briefChip\"></span>\n        <button class=\"btn btn-sm btn-outline-secondary btn-csv iv-brief-btn\" data-sm-click=\"openBriefIv()\" title=\"Fill in the Internal Verification of Assignment Brief form for this brief\">IV this brief</button>\n      </div>\n      <input type=\"file\" id=\"briefInput\" accept=\".pdf,.docx\" data-sm-change=\"addBrief(this.files[0]); this.value = ''\">\n      <details class=\"more\" id=\"briefPaste\" style=\"margin-top:10px\">\n        <summary>Or paste the brief</summary>\n        <textarea class=\"form-control field-input\" id=\"brief_text\" rows=\"6\" style=\"margin-top:8px\" placeholder=\"Paste the brief here: the scenario, each task and the criteria it covers. Handy when the brief is on Teams, Moodle or in an email.\" data-sm-input=\"onBriefText()\"></textarea>\n        <span class=\"helper\" id=\"briefTextNote\" style=\"display:block;margin-top:4px\"></span>\n      </details>\n    </div>\n\n    <div class=\"card\">\n      <div class=\"label\">Assessor guidance <span style=\"font-weight:400;text-transform:none;letter-spacing:0;font-size:11px\">(optional)</span></div>\n      <p class=\"helper\" style=\"margin-bottom:10px\">Anything the AI should know that isn't in the brief, such as what you expect at each grade.</p>\n      <textarea class=\"form-control field-input\" id=\"unit_context\" placeholder=\"e.g. For A.M1, look for comparison across at least three genres. A.D1 requires evaluation with supporting evidence...\"></textarea>\n    </div>\n\n    <div class=\"card\">\n      <div class=\"label\">Reference marked work <span style=\"font-weight:400;text-transform:none;letter-spacing:0;font-size:11px\">(optional)</span></div>\n      <p class=\"helper\" style=\"margin-bottom:10px\">Add up to 3 pieces of work you've already marked for this unit, with the grade you gave. The AI uses them to match your standard; it won't assess or mention them. Exemplars from Pearson or anonymised past work are best.</p>\n      <button class=\"btn btn-sm btn-outline-secondary btn-csv\" data-sm-click=\"document.getElementById('refInput').click()\">Add marked work</button>\n      <input type=\"file\" id=\"refInput\" accept=\".pdf,.docx\" multiple data-sm-change=\"addReferences(this.files); this.value = ''\">\n      <div id=\"refList\"></div>\n    </div>\n  </div></section>\n\n  <!-- STEP 2: LEARNER -->\n  <section class=\"step\" data-step=\"2\"><slot name=\"s2\"></slot><div class=\"sm-old\" style=\"display:none\" aria-hidden=\"true\">\n    <div class=\"card\">\n      <div class=\"step-title\" id=\"step2Title\">Learner and assignment details</div>\n      <div class=\"lmode\" role=\"radiogroup\" aria-label=\"Learner details\">\n        <label class=\"lm-opt\"><input class=\"form-check-input\" type=\"radio\" name=\"learnerMode\" value=\"named\" checked data-sm-change=\"setLearnerMode('named')\"><span><b>Enter their details</b><small>Name and ID go on the record, the file names and the email.</small></span></label>\n        <label class=\"lm-opt\"><input class=\"form-check-input\" type=\"radio\" name=\"learnerMode\" value=\"anon\" data-sm-change=\"setLearnerMode('anon')\"><span><b>Keep it anonymous</b><small>No name, ID or email is entered. The work gets a reference instead.</small></span></label>\n      </div>\n      <div class=\"anon-box\" id=\"anonBox\" hidden>\n        <div class=\"anon-ref\"><span class=\"field-label\">Reference</span><strong id=\"anonRefText\"></strong><button type=\"button\" class=\"btn btn-link btn-sm\" data-sm-click=\"setLearnerMode('anon', newAnonRef())\">New reference</button></div>\n        <p class=\"helper\" style=\"margin:4px 0 0\">This goes on the assessment record and the file names in place of the learner's name. Note it on your own list if you need to match it to the learner later.</p>\n      </div>\n      <div class=\"csv-bar pii\">\n        <button class=\"btn btn-sm btn-outline-secondary btn-csv\" data-sm-click=\"document.getElementById('csvInput').click()\">Import from CSV</button>\n        <select class=\"form-select field-input\" id=\"presetSelect\" style=\"display:none;width:auto;min-width:220px\" data-sm-change=\"applyPreset(this.value)\">\n          <option value=\"\">Select a row to autofill...</option>\n        </select>\n        <span class=\"helper\" id=\"csvStatus\" style=\"margin-top:0\"></span>\n      </div>\n      <input type=\"file\" id=\"csvInput\" accept=\".csv\" data-sm-change=\"handleCsv(this.files[0])\">\n      <p class=\"batch-note batch-only\" id=\"batchNote\"></p>\n      <div class=\"field-grid\">\n        <div class=\"field-group pii\">\n          <span class=\"field-label\">Student name</span>\n          <input class=\"form-control field-input\" id=\"student_name\" type=\"text\" placeholder=\"e.g. John Smith\" data-sm-input=\"updateContext()\">\n        </div>\n        <div class=\"field-group pii\">\n          <span class=\"field-label\">Student registration number</span>\n          <input class=\"form-control field-input\" id=\"student_reg\" type=\"text\" placeholder=\"e.g. 123456\" data-sm-input=\"autoEmail()\">\n        </div>\n        <div class=\"field-group pii\">\n          <span class=\"field-label\">Student email</span>\n          <input class=\"form-control field-input\" id=\"student_email\" type=\"email\" placeholder=\"Filled in from the student ID\" data-sm-input=\"emailManual = !!this.value.trim(); if (!emailManual) autoEmail()\">\n        </div>\n        <div class=\"field-group\">\n          <span class=\"field-label\">Assignment title</span>\n          <input class=\"form-control field-input\" id=\"assignment_title\" type=\"text\" placeholder=\"e.g. Assignment 1\" data-sm-input=\"delete this.dataset.fromBrief\">\n        </div>\n        <div class=\"field-group\">\n          <span class=\"field-label\" data-iv=\"Assessor (whose marking you're sampling)\">Assessor name</span>\n          <input class=\"form-control field-input\" id=\"assessor_name\" type=\"text\" placeholder=\"e.g. Joe Tuley\" data-sm-input=\"updateContext()\">\n        </div>\n        <div class=\"field-group iv-only\">\n          <label class=\"field-label\" for=\"iv_name\">Internal verifier</label>\n          <input class=\"form-control field-input\" id=\"iv_name\" type=\"text\" placeholder=\"Your name\" data-sm-input=\"storeSet('btec_iv_name', this.value); scheduleSave()\">\n        </div>\n        <div class=\"field-group\">\n          <span class=\"field-label\">Deadline</span>\n          <input class=\"form-control field-input\" id=\"deadline\" type=\"text\" placeholder=\"e.g. 01/05/2026\" data-sm-input=\"delete this.dataset.fromBrief; renderLateStatus()\">\n        </div>\n        <div class=\"field-group\">\n          <span class=\"field-label date-label\"><span>Date submitted</span><button type=\"button\" class=\"btn btn-link btn-sm\" data-sm-click=\"setSubmittedToday()\">Today</button></span>\n          <input class=\"form-control field-input\" id=\"date_submitted\" type=\"text\" placeholder=\"e.g. 01/05/2026\" data-sm-input=\"renderLateStatus()\">\n        </div>\n        <div class=\"field-group\">\n          <label class=\"field-label\" for=\"extension\">Extension approved?</label>\n          <div class=\"ext-row\">\n            <select class=\"form-select field-input\" id=\"extension\" data-sm-change=\"renderLateStatus()\"><option value=\"\">Not set</option><option value=\"No\">No</option><option value=\"Yes\">Yes</option></select>\n            <input class=\"form-control field-input\" id=\"extension_date\" type=\"text\" placeholder=\"New deadline\" aria-label=\"Extended deadline\" data-sm-input=\"renderLateStatus()\" hidden>\n          </div>\n        </div>\n        <div class=\"field-group\">\n          <span class=\"field-label\">Unit number and title</span>\n          <input class=\"form-control field-input\" id=\"unit_title\" type=\"text\" placeholder=\"Autofilled from step 1\">\n        </div>\n      </div>\n      <div class=\"late-status\" id=\"lateStatus\" aria-live=\"polite\"></div>\n      <div class=\"field-group\" style=\"margin-top:14px\">\n        <label class=\"field-label\" for=\"hide_extra\">Other names to hide from the AI <span style=\"font-weight:400;color:var(--slate)\">(optional)</span></label>\n        <input class=\"form-control field-input\" id=\"hide_extra\" type=\"text\" autocomplete=\"off\" placeholder=\"e.g. Mrs Patel, Jordan Smith\" data-sm-input=\"scheduleSave()\">\n        <span class=\"helper\" style=\"margin-top:2px\">Separate names with commas: staff, other learners, or the learner's own name when you're keeping it anonymous. These never go on the record.</span>\n      </div>\n      <p class=\"privacy-note\"><svg width=\"15\" height=\"15\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" aria-hidden=\"true\"><rect x=\"5\" y=\"11\" width=\"14\" height=\"9\" rx=\"2\"/><path d=\"M8 11V8a4 4 0 0 1 8 0v3\"/></svg><span>Learner details stay on this computer. Before anything goes to the second marker, names, IDs, emails, phone numbers and postcodes in the work and its file details are replaced with placeholders like [Learner].</span></p>\n    </div>\n    <div class=\"card\" id=\"resubCard\">\n      <div class=\"resub-head\">\n        <div class=\"form-check form-switch m-0\">\n          <input class=\"form-check-input\" type=\"checkbox\" role=\"switch\" id=\"resubToggle\" data-sm-change=\"setResub(this.checked)\">\n          <label class=\"form-check-label\" for=\"resubToggle\">This is a resubmission or retake</label>\n        </div>\n        <select class=\"form-select form-select-sm\" id=\"resubType\" style=\"width:auto;display:none\" data-sm-change=\"resub.type = this.value; renderResub(); updateGrade()\">\n          <option>Resubmission</option><option>Retake</option>\n        </select>\n      </div>\n      <div id=\"resubBody\" style=\"display:none\">\n        <p class=\"helper\" style=\"margin:10px 0\">Add the first assessment record so the earlier decisions carry over. Criteria already achieved stay achieved, and the AI only reassesses the rest. The new record keeps the first submission and fills in the Resubmission section.</p>\n        <div class=\"csv-bar\" style=\"margin:0\">\n          <button class=\"btn btn-sm btn-outline-secondary btn-csv\" data-sm-click=\"document.getElementById('resubInput').click()\">Add first assessment record</button>\n          <span id=\"resubChip\"></span>\n        </div>\n        <input type=\"file\" id=\"resubInput\" accept=\".docx\" data-sm-change=\"loadFirstRecord(this.files[0]); this.value = ''\">\n        <p class=\"helper\" id=\"resubNote\" style=\"margin:10px 0 0\"></p>\n        <div class=\"resub-grid\" id=\"resubGrid\"></div>\n      </div>\n    </div>\n  </div></section>\n\n  <!-- STEP 3: SUBMISSION -->\n  <section class=\"step\" data-step=\"3\">\n    <div class=\"card batch-only\" id=\"batchCard\"></div>\n    <input type=\"file\" id=\"batchInput\" accept=\".pdf,.docx\" multiple>\n    <slot name=\"s3\"></slot><div class=\"sm-old\" style=\"display:none\" aria-hidden=\"true\"><div class=\"card not-batch\">\n      <div class=\"step-title\" data-iv=\"Upload the marked work\">Upload the learner's work</div>\n      <p class=\"helper iv-only\" style=\"margin:0 0 10px\">A Turnitin download works best: only the learner's writing goes to the second marker, and the assessor's marking is read separately. You can also upload the learner's own file and add the assessor's record or decisions on the next step.</p>\n      <div class=\"drop-zone\" id=\"dropZone\">\n        <div class=\"drop-icon\" id=\"dropIcon\"><svg viewBox=\"0 0 24 24\" width=\"30\" height=\"30\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"1.6\" stroke-linecap=\"round\" stroke-linejoin=\"round\" aria-hidden=\"true\"><path d=\"M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z\"/><path d=\"M14 3v5h5\"/><path d=\"M12 17v-6M9 14l3-3 3 3\"/></svg></div>\n        <div id=\"dropText\">Drop a Word or PDF file here, or <strong>click to browse</strong></div>\n      </div>\n      <input type=\"file\" id=\"fileInput\" accept=\".pdf,.docx\">\n      <div id=\"linkFeature\" style=\"display:none\">\n      <div class=\"or-line\"><span>or paste a link</span></div>\n      <div class=\"link-row\">\n        <input class=\"form-control field-input\" id=\"docLink\" type=\"url\" placeholder=\"Google Docs, Google Drive, OneDrive, SharePoint or Word online link\" data-sm-keydown=\"if(event.key==='Enter') fetchFromLink()\">\n        <button class=\"btn btn-primary btn-export\" id=\"linkBtn\" data-sm-click=\"fetchFromLink()\">Open link</button>\n      </div>\n      <p class=\"helper\" id=\"linkStatus\" style=\"margin-top:6px\">You'll sign in with an account that can open the document. Reads the work and its version history.</p>\n      </div>\n      <p class=\"helper\">PDF or Word (.docx). As soon as it's added, the file is checked for authorship, dates, editing history, AI traces and sources, and the AI assessment starts automatically.</p>\n      <div class=\"alert alert-warning key-warn\" id=\"keyBanner\" style=\"display:none\">No API key set yet, so the AI check can't run. <button class=\"btn btn-sm btn-outline-secondary btn-csv\" data-sm-click=\"openKeySettings()\">Add API key</button></div>\n      <ul class=\"upload-progress\" id=\"uploadProgress\" style=\"display:none\">\n        <li id=\"pFile\"><span class=\"pdot\"></span>Reading the file</li>\n        <li id=\"pIntegrity\"><span class=\"pdot\"></span>Checking authorship, dates, editing history and sources</li>\n        <li id=\"pAi\"><span class=\"pdot\"></span><span id=\"pAiText\">Second marker assessing each criterion</span></li>\n      </ul>\n    </div>\n  </div></section>\n\n  <!-- STEP 4: CHECKS -->\n  <section class=\"step\" data-step=\"4\">\n    <div class=\"card iv-only\" id=\"ivAssessorCard\"></div>\n    <input type=\"file\" id=\"ivRecordInput\" accept=\".docx\" data-sm-change=\"ivLoadRecord(this.files[0]); this.value = ''\">\n    <div id=\"aiStatus\"></div>\n    <div id=\"results\"></div>\n    <div class=\"card\" id=\"integrityCard\"></div>\n  </section>\n\n  <!-- STEP 5: GRADE (the marking desk) -->\n  <section class=\"step desk-step\" data-step=\"5\">\n    <div class=\"learner-strip\" id=\"learnerStrip\"></div>\n    <div class=\"desk\" id=\"desk\">\n      <aside class=\"marksheet\" aria-labelledby=\"msTitle\">\n        <div class=\"ms-head\">\n          <h2 id=\"msTitle\" class=\"desk-only\">Mark sheet</h2>\n          <span class=\"focus-pos focus-only\" id=\"focusPos\"></span>\n          <span class=\"count desk-only\" id=\"decidedCount\"></span>\n          <button type=\"button\" class=\"btn btn-sm btn-outline-secondary\" id=\"focusBtn\" aria-pressed=\"false\" data-sm-click=\"toggleFocus()\">Focus mode</button>\n        </div>\n        <div class=\"grade-box\">\n          <div><span class=\"gb-k\" data-iv=\"Your view\">Your grade</span><span class=\"grade-big g-X\" id=\"assessorGrade\">Not graded</span></div>\n          <div class=\"gb-asr iv-only\"><span class=\"gb-k\">Assessor</span><span class=\"gb-v small\" id=\"ivAsrGrade\">Not entered</span></div>\n          <div class=\"gb-ai\"><span class=\"gb-k\"><svg width=\"13\" height=\"13\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2.2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" aria-hidden=\"true\"><path d=\"M16.5 3.5l4 4L8 20H4v-4z\"/></svg>Second marker</span><span class=\"gb-v small\" id=\"aiGradeMini\">Not checked</span><button type=\"button\" class=\"btn btn-link btn-sm ask-btn\" id=\"aiAsk\" data-sm-click=\"askSecondMarker()\" hidden>Ask now</button></div>\n        </div>\n        <div class=\"grade-note\"><span class=\"helper\" id=\"gradeNote\">Waiting for the AI check.</span><button class=\"btn btn-link btn-sm linkish\" id=\"skipAiBtn\" data-sm-click=\"unlockGrading(true)\">Grade without the AI check</button></div>\n        <div class=\"focus-chips focus-only\" id=\"focusChips\" role=\"group\" aria-label=\"Criteria\"></div>\n        <div id=\"criteriaBody\" class=\"crit-list\"></div>\n        <div class=\"focus-nav focus-only\"><button type=\"button\" class=\"btn btn-outline-secondary\" id=\"focusPrev\" data-sm-click=\"moveCurrent(-1)\">Previous</button><button type=\"button\" class=\"btn btn-primary\" id=\"focusNext\" data-sm-click=\"moveCurrent(1, true)\">Next criterion</button></div>\n        <p class=\"keys\"><kbd>Y</kbd><kbd>N</kbd><kbd>R</kbd> decide, <kbd>&uarr;</kbd><kbd>&darr;</kbd> move, <kbd>F</kbd> focus mode, <kbd>1</kbd> to <kbd>9</kbd> QuickMarks</p>\n      </aside>\n\n      <section class=\"workpane\" aria-labelledby=\"workTitle\">\n        <div class=\"work-bar\">\n          <h2 id=\"workTitle\">The work</h2>\n          <span class=\"fname\" id=\"workName\"></span>\n          <button type=\"button\" class=\"btn btn-sm btn-outline-secondary add-note\" data-sm-click=\"addManualComment(selectionText())\" title=\"Select text in the work first to comment on it\">Add comment</button>\n          <label><input class=\"form-check-input\" type=\"checkbox\" id=\"showNotes\" checked data-sm-change=\"document.getElementById('workWrap').classList.toggle('hide-notes', !this.checked); placeNotes()\">Pencil notes</label>\n        </div>\n        <div class=\"qm-bar\" id=\"qmBar\" aria-label=\"QuickMarks\"></div>\n        <div class=\"work-wrap\" id=\"workWrap\">\n          <div class=\"paper empty\" id=\"workPaper\"><p class=\"helper\">The learner's work shows here once it's uploaded.</p></div>\n          <div class=\"notes\" id=\"annotationList\" aria-label=\"Comments on the work\"></div>\n        </div>\n      </section>\n\n      <aside class=\"sidepane\" aria-label=\"Feedback and notes\">\n        <div>\n          <h2 id=\"fbTitle\"><span class=\"fb-pre\">Feedback to</span> <span id=\"fbName\">the learner</span></h2>\n          <div id=\"feedbackWrap\"><div class=\"feedback-box\" id=\"feedbackBox\" contenteditable=\"true\" role=\"textbox\" aria-multiline=\"true\" aria-labelledby=\"fbTitle\" data-ph=\"The AI's feedback appears here after the check: an overall comment on the assignment, then what's needed for each criterion not yet met. You can also write your own.\" data-sm-input=\"lastFeedback = this.innerText; feedbackEdited = true; showRebuild()\" data-sm-blur=\"if (/[\u2013\u2014]/.test(this.innerText)) { this.innerText = noDashes(this.innerText); lastFeedback = this.innerText; }\"></div>\n        <div class=\"fb-tools\"><span class=\"helper\" style=\"margin:0\"><span class=\"not-iv\">Updates automatically as you change Y/N/R, until you edit it yourself. This goes into General comments on export.</span><span class=\"iv-only\">Updates as you decide, until you edit it yourself. This goes into Internal Verifier Comments on the IV record.</span></span><button class=\"btn btn-link btn-sm linkish\" id=\"rebuildFb\" style=\"display:none\" data-sm-click=\"feedbackEdited = false; composeFeedback(true)\">Rebuild from my decisions</button></div></div>\n        </div>\n        <div class=\"iv-only iv-side\">\n          <span class=\"sp-label small\">IV checklist</span>\n          <div id=\"ivChecklist\" class=\"ivc\"></div>\n          <label class=\"sp-label small\" for=\"ivActions\" style=\"margin-top:12px\">Action for the assessor</label>\n          <textarea class=\"form-control field-input\" id=\"ivActions\" rows=\"3\" placeholder=\"n/a\" data-sm-input=\"iv.actions = this.value; iv.actionsEdited = true; scheduleSave(); if (!feedbackEdited) composeFeedback(true)\" data-sm-blur=\"this.value = noDashes(this.value); iv.actions = this.value\"></textarea>\n          <div class=\"iv-target\"><label class=\"field-label\" for=\"ivTarget\">Target date</label><input class=\"form-control field-input\" id=\"ivTarget\" type=\"text\" placeholder=\"dd/mm/yyyy\" data-sm-input=\"iv.target = this.value; scheduleSave()\"></div>\n          <div class=\"fb-tools\"><span class=\"helper\" style=\"margin:0\" id=\"ivReviewNote\"></span><button class=\"btn btn-link btn-sm linkish\" id=\"ivActionsReset\" style=\"display:none\" data-sm-click=\"iv.actionsEdited = false; ivRenderSide()\">Rebuild from my decisions</button></div>\n        </div>\n        <div class=\"not-iv\">\n          <label class=\"sp-label small\" for=\"staffNote\">Note for other lecturers</label>\n          <textarea class=\"form-control field-input staff-note\" id=\"staffNote\" rows=\"2\" placeholder=\"One line for colleagues on what the learner needs to do next. Filled in from your decisions.\" data-sm-input=\"staffEdited = true; showStaffRebuild()\" data-sm-blur=\"this.value = noDashes(this.value)\"></textarea>\n      <div class=\"fb-tools\"><span class=\"helper\" style=\"margin:0\">Added as its own box at the end of the assessment record. Updates with your decisions until you edit it.</span><button class=\"btn btn-link btn-sm linkish\" id=\"rebuildStaff\" style=\"display:none\" data-sm-click=\"staffEdited = false; composeStaffNote(true)\">Rebuild from my decisions</button></div>\n        </div>\n        <div class=\"mini-checks\" id=\"miniChecks\"></div>\n      </aside>\n    </div>\n  </section>\n\n  <!-- STEP 6: EXPORT -->\n  <section class=\"step\" data-step=\"6\"><slot name=\"s6\"></slot><div class=\"sm-old\" style=\"display:none\" aria-hidden=\"true\">\n    <div class=\"card\" id=\"exportSummary\"></div>\n    <div class=\"card\">\n      <div class=\"label\">Downloads</div>\n      <div class=\"dl-grid\">\n        <button class=\"btn btn-primary btn-export iv-only\" id=\"ivRecordBtn\" data-sm-click=\"ivDownloadRecord()\">Download IV record</button>\n        <button class=\"btn btn-primary btn-export not-iv\" id=\"exportBtn\" data-sm-click=\"exportDoc()\">Download assessment record</button>\n        <button class=\"btn btn-outline-secondary btn-run not-iv\" id=\"markedPdfBtn\" data-sm-click=\"downloadMarkedPdf()\">Download marked work (PDF)</button>\n        <button class=\"btn btn-outline-secondary btn-run not-iv\" id=\"commentedDocxBtn\" data-sm-click=\"downloadCommentedDocx()\">Download Word file with comments</button>\n      </div>\n      <p class=\"helper\" id=\"dlNote\"></p>\n    </div>\n    <div class=\"card\" id=\"emailCard\">\n      <div class=\"step-title\" data-iv=\"Email the assessor\">Email the learner</div>\n      <div class=\"field-grid\">\n        <div class=\"field-group\">\n          <span class=\"field-label\">To</span>\n          <input class=\"form-control field-input\" id=\"emailTo\" type=\"email\" placeholder=\"Learner's email address\" data-sm-input=\"if (!ivMode) { document.getElementById('student_email').value = this.value; emailManual = true; }\">\n        </div>\n        <div class=\"field-group\">\n          <span class=\"field-label\">Subject</span>\n          <input class=\"form-control field-input\" id=\"emailSubject\" type=\"text\" data-sm-input=\"emailEdited = true\">\n        </div>\n      </div>\n      <div class=\"field-group\" style=\"margin-top:10px\">\n        <span class=\"field-label\">Message</span>\n        <textarea class=\"form-control field-input email-body\" id=\"emailBody\" data-sm-input=\"emailEdited = true\" data-sm-blur=\"this.value = noDashes(this.value)\"></textarea>\n      </div>\n      <div class=\"fb-tools\"><span class=\"helper\" style=\"margin:0\" data-iv=\"Written from your comments and the actions. Edit freely.\">Written from your grade, decisions and feedback. Edit freely.</span><button class=\"btn btn-link btn-sm linkish\" data-sm-click=\"emailEdited = false; composeEmail(true)\">Rewrite from my decisions</button></div>\n      <div class=\"label not-iv\" style=\"margin-top:12px\">Attach</div>\n      <p class=\"helper iv-only\" style=\"margin:12px 0 0\">\"Create email\" attaches the IV record.</p>\n      <div class=\"attach-row not-iv\">\n        <label><input class=\"form-check-input\" type=\"checkbox\" id=\"attMarked\" data-sm-change=\"composeEmail(false)\" checked> Marked work with comments (PDF)</label>\n        <label><input class=\"form-check-input\" type=\"checkbox\" id=\"attRecord\" data-sm-change=\"composeEmail(false)\" checked> Assessment record (Word)</label>\n        <label id=\"attCommentedWrap\"><input class=\"form-check-input\" type=\"checkbox\" id=\"attCommented\" data-sm-change=\"composeEmail(false)\"> Their Word file with comments</label>\n        <label><input class=\"form-check-input\" type=\"checkbox\" id=\"attStaff\"> Include the note for other lecturers on the record</label>\n      </div>\n      <div class=\"dl-grid\" style=\"margin-top:12px\">\n        <button class=\"btn btn-primary btn-export\" id=\"emlBtn\" data-sm-click=\"createEmailDraft()\">Create email with attachments</button>\n        <button class=\"btn btn-outline-secondary btn-run\" data-sm-click=\"openMailApp()\">Open in my email app (text only)</button>\n        <button class=\"btn btn-outline-secondary btn-run\" id=\"copyEmailBtn\" data-sm-click=\"copyEmail()\">Copy email text</button>\n      </div>\n      <p class=\"helper\">\"Create email\" downloads a ready-to-send draft (.eml) with the files attached: in desktop Outlook it opens as a new message, so check it and press Send. Outlook on the web and Gmail can't open drafts this way; use \"Open in my email app\" or \"Copy email text\" and attach the downloaded files.</p>\n    </div>\n    <div class=\"btn-row\">\n      <button class=\"btn btn-outline-secondary btn-run\" data-sm-click=\"nextLearner()\">Next learner, same unit</button>\n    </div>\n  </div></section>\n\n  <div class=\"wiz-nav\" id=\"wizardNav\">\n    <button class=\"btn btn-outline-secondary btn-run nav-back\" id=\"backBtn\" data-sm-click=\"goStep(currentStep - 1)\">Back</button>\n    <span class=\"nav-msg\" id=\"navMsg\"></span>\n    <button class=\"btn btn-primary btn-export nav-next\" id=\"nextBtn\" data-sm-click=\"goStep(currentStep + 1)\">Continue</button>\n  </div>\n</div>\n<div class=\"toast-container position-fixed bottom-0 end-0 p-3\" id=\"toastArea\" aria-live=\"polite\"></div>\n<dialog class=\"dlg dlg-wide\" id=\"briefIvDialog\" aria-labelledby=\"briefIvTitle\"><div id=\"briefIvBody\"></div></dialog>\n<dialog class=\"dlg\" id=\"restartDialog\" aria-labelledby=\"restartTitle\">\n  <h2 id=\"restartTitle\">Start again?</h2>\n  <p class=\"helper\" id=\"restartNote\">Your settings stay as they are.</p>\n  <div class=\"choose-grid\">\n    <button type=\"button\" class=\"choice keep\" id=\"restartLearnerBtn\" data-sm-click=\"restartLearner()\"><b>New learner, same unit</b><span>Keeps the unit, assignment and brief. Clears the learner's details, their work and the marking.</span></button>\n    <button type=\"button\" class=\"choice wipe\" data-sm-click=\"restartAll()\"><b>Clear everything</b><span>Back to the very start with nothing filled in, and the saved draft removed.</span></button>\n  </div>\n  <div class=\"dlg-foot\"><button type=\"button\" class=\"btn btn-outline-secondary\" data-sm-click=\"closeRestart()\">Cancel</button></div>\n</dialog>\n<div class=\"sel-pop\" id=\"selPop\"><button type=\"button\" class=\"btn btn-primary btn-sm\" data-sm-mousedown=\"event.preventDefault()\" data-sm-click=\"addManualComment(this.parentNode._text)\">Comment on this</button><div class=\"qm-pop\" id=\"selPopQm\"></div></div>\n<dialog class=\"dlg dlg-wide\" id=\"qmDialog\" aria-labelledby=\"qmTitle\"><div id=\"qmBody\"></div></dialog>\n<dialog class=\"dlg dlg-wide\" id=\"batchDialog\" aria-label=\"Class set\"><div id=\"batchDialogBody\"></div></dialog>\n<input type=\"file\" id=\"qmFile\" accept=\".json,application/json\" data-sm-change=\"qmLoad(this.files[0]); this.value = ''\">";
  const SM_META = {"levels":{"Esports|Level 2":{"programmes":["Pearson BTEC Level 2 Award in Esports","Pearson BTEC Level 2 Certificate in Esports","Pearson BTEC Level 2 Diploma in Esports"],"grading":"","note":""},"Esports|Level 3":{"programmes":["Pearson BTEC Level 3 National Extended Certificate in Esports","Pearson BTEC Level 3 National Foundation Diploma in Esports","Pearson BTEC Level 3 National Diploma in Esports","Pearson BTEC Level 3 National Extended Diploma in Esports"],"grading":"","note":""},"IT|Level 2 BTEC First Award":{"programmes":["Pearson BTEC Level 1/Level 2 First Award in Information and Creative Technology"],"grading":"first","note":"BTEC First Award grading: criteria coded 1x.n are Level 1; 2x.Pn Level 2 Pass, 2x.Mn Merit, 2x.Dn Distinction. Unit 1 is externally assessed so has no criteria here."},"IT|Level 2 TLM Cyber Security":{"programmes":["TLM Level 2 Certificate in Cyber Security Principles"],"grading":"pass","note":"TLM qualification graded Pass only. Criteria are \"I can\" statements grouped by learning outcome (1.n, 2.n, ...); all targeted statements must be evidenced."},"IT|Level 3 BTEC National":{"programmes":["Pearson BTEC Level 3 National Extended Certificate in Information Technology","Pearson BTEC Level 3 National Certificate in Information Technology","Pearson BTEC Level 3 National Foundation Diploma in Information Technology","Pearson BTEC Level 3 National Diploma in Information Technology","Pearson BTEC Level 3 National Extended Diploma in Information Technology"],"grading":"btec","note":"Units 1, 2, 11 and 14 are externally assessed so have no criteria here."}},"briefs":{"esports-level2-u1-1":{"qualification":"Pearson BTEC Level 2 Diploma in Esports"},"esports-level2-u1-2":{"qualification":"Pearson BTEC Level 2 Diploma in Esports"},"esports-level2-u2-u4-1":{"ivNotes":[{"q":"criteria","text":"2/B.P3 \"Explore existing Esports brands\" sits under Task 1, which only asks about global sporting organisations. Add an esports brands question to Task 1, or move B.P3 to a task that asks for it."}],"qualification":"Pearson BTEC Level 2 Diploma in Esports"},"esports-level2-u2-u4-2":{"qualification":"Pearson BTEC Level 2 Diploma in Esports"},"esports-level2-u3-1":{"ivNotes":[{"q":"requirements","text":"A.M1 uses the American spelling \"Analyze\". Use \"Analyse\".","minor":true}],"qualification":"Pearson BTEC Level 2 Diploma in Esports"},"esports-level2-u5-1":{"qualification":"Pearson BTEC Level 2 Diploma in Esports"},"esports-level2-u5-2":{"qualification":"Pearson BTEC Level 2 Diploma in Esports"},"esports-level2-u6-1":{"qualification":"Pearson BTEC Level 2 Diploma in Esports"},"esports-level2-u6-2":{"ivNotes":[{"q":"criteria","text":"Task 1 has no criteria table of its own. Add one so learners can see what each task is marked against (aim B for Task 1, aim C for Task 2)."}],"qualification":"Pearson BTEC Level 2 Diploma in Esports"},"esports-level3-u1-1":{"qualification":"BTEC Level 3 National Foundation Diploma in Esports"},"esports-level3-u1-2":{"qualification":"BTEC Level 3 National Foundation Diploma in Esports"},"esports-level3-u2-1":{"qualification":"Pearson BTEC Level 3 National Foundation Diploma in Esports"},"esports-level3-u2-2":{"ivNotes":[{"q":"requirements","text":"D.P6 reads \"Carry out a performance analysis and the opportunities they provide.\" Use the spec wording: \"Carry out a performance analysis on an esports game.\""}],"qualification":"Pearson BTEC Level 3 National Foundation Diploma in Esports"},"esports-level3-u3-1":{"ivNotes":[{"q":"requirements","text":"A.D1 reads \"how a successful esports enterprise has been\". The spec says \"how successful a selected esports enterprise has been\".","minor":true}],"qualification":"Pearson BTEC Level 3 National Foundation Diploma in Esports"},"esports-level3-u3-2":{"ivNotes":[{"q":"requirements","text":"D.P7 adds \"giving suggestions for improvements\", which is D.M4 wording, so the Pass asks for Merit work. Remove it."},{"q":"requirements","text":"C.M3 \"well-structure\" should be \"well-structured\", and D.M4 \"giving suggesting\" should be \"giving suggestions\".","minor":true}],"qualification":"Pearson BTEC Level 3 National Foundation Diploma in Esports"},"esports-level3-u4-1":{"ivNotes":[{"q":"requirements","text":"A.M1 reads \"the ways different\" where the spec has \"the ways in which different\".","minor":true}],"qualification":"Pearson BTEC Level 3 National Foundation Diploma in Esports"},"esports-level3-u4-2":{"qualification":"Pearson BTEC Level 3 National Foundation Diploma in Esports"},"esports-level3-u5-u19-1":{"ivNotes":[{"q":"requirements","text":"\"events organiser\" where the spec has \"event organiser\" (5/A.P1, A.P2, A.M1, A.D1), and 5/A.D1 is missing \"as\" before \"an esports event organiser\".","minor":true},{"q":"requirements","text":"Spaces round slashes, such as \"products / services\". The spec has \"products/services\".","minor":true}],"qualification":"BTEC Level 3 National Extended Diploma in Esports"},"esports-level3-u5-u19-2":{"qualification":"Pearson BTEC Level 3 National Extended Diploma in Esports"},"esports-level3-u6-1":{"ivNotes":[{"q":"requirements","text":"Spaces round slashes, such as \"esports / games\". The spec has \"esports/games\".","minor":true}],"qualification":"BTEC Level 3 National Foundation Diploma in Esports"},"esports-level3-u7-u10-1":{"qualification":"Pearson BTEC Level 3 National Foundation Diploma in Esports"},"esports-level3-u7-u10-2":{"ivNotes":[{"q":"requirements","text":"7/C.M3 drops \"for a specific esports team\" from the end, so learners aren't asked to aim the work at one team. Use the spec wording."}],"qualification":"Pearson BTEC Level 3 National Foundation Diploma in Esports"},"esports-level3-u8-1":{"ivNotes":[{"q":"requirements","text":"Spaces round slashes, such as \"esports / games\", and B.M2 adds \"video\" (the brief arguably reads better here).","minor":true}],"qualification":"BTEC Level 3 National Extended Diploma in Esports"},"esports-level3-u9-1":{"qualification":"Pearson BTEC Level 3 National Extended Diploma in Esports"},"esports-level3-u11-1":{"qualification":"Pearson BTEC Level 3 National Extended Diploma in Esports"},"esports-level3-u11-2":{"qualification":"Pearson BTEC Level 3 National Extended Diploma in Esports"},"esports-level3-u12-1":{"ivNotes":[{"q":"requirements","text":"D.P5 and D.M4 say \"your own\" where the spec has \"own\".","minor":true}],"qualification":"Pearson BTEC Level 3 National Extended Diploma in Esports"},"esports-level3-u13-1":{"ivNotes":[{"q":"requirements","text":"C.M4 is missing \"an\".","minor":true}],"qualification":"Pearson BTEC Level 3 National Foundation Diploma in Esports"},"esports-level3-u15-1":{"qualification":"Pearson BTEC Level 3 National Extended Diploma in Esports"},"esports-level3-u15-2":{"qualification":"Pearson BTEC Level 3 National Extended Diploma in Esports"},"it-level2btecfirstaward-u3-u13-2":{"ivNotes":[{"q":"details","text":"Qualification shown as \"Level1/2 Award in Information and Creative Technology\". Use \"Pearson BTEC Level 1/Level 2 First Award in Information and Creative Technology\"."},{"q":"requirements","text":"Small wording differences from the spec: 13/2B.P3 \"8 page\" (spec \"eight page\") and 3/2A.M1 is missing \"the\" before \"layout\".","minor":true}],"qualification":"BTEC Level1/2 Award in Information and Creative Technology"},"it-level2btecfirstaward-u3-u13-3":{"ivNotes":[{"q":"deadlines","text":"No hand-out or deadline dates. Add them."},{"q":"details","text":"Qualification shown as \"Level1/2 Award in Information and Creative Technology\". Use \"Pearson BTEC Level 1/Level 2 First Award in Information and Creative Technology\"."},{"q":"details","text":"The title \"PROJECT 1 - Develop a Portfolio to get a job!\" doesn't follow the style of the other titles.","minor":true},{"q":"requirements","text":"13/2C.M3 says \"user's\" where the spec has \"users'\".","minor":true}],"qualification":"BTEC Level1/2 Award in Information and Creative Technology"},"it-level2btecfirstaward-u3-u13-4":{"ivNotes":[{"q":"deadlines","text":"No hand-out or deadline dates. Add them."},{"q":"details","text":"Qualification shown as \"Level1/2 Award in Information and Creative Technology\". Use \"Pearson BTEC Level 1/Level 2 First Award in Information and Creative Technology\"."},{"q":"requirements","text":"3/2C.D3 says \"improvements\" where the spec has \"improvement\".","minor":true}],"qualification":"BTEC Level1/2 Award in Information and Creative Technology"},"it-level2btecfirstaward-u13-1":{"ivNotes":[{"q":"deadlines","text":"No hand-out or deadline dates. Add them."},{"q":"details","text":"Qualification shown as \"BTEC Level 1/2 Award in Information and Creative Technology\". Use \"Pearson BTEC Level 1/Level 2 First Award in Information and Creative Technology\"."},{"q":"criteria","text":"The Level 1 criteria table has no unit column, so it isn't clear which unit the Level 1 criteria belong to.","minor":true}],"qualification":"BTEC Level 1/2 Award in Information and Creative Technology"},"it-level-3-btec-national-u7-project-1-task-1":{"ivNotes":[{"q":"deadlines","text":"Dates are written as \"28/9/2026\" and \"06/11/26\". Use one format, such as 28/09/2026.","minor":true}],"fullTitle":"Project 1: Investigating Digital Solutions, Task 1: Mobile App Investigation","qualification":"BTEC Level 3 National Extended Diploma in Information Technology"},"it-level-3-btec-national-u8-project-1-task-2":{"fullTitle":"Project 1: Investigating Digital Solutions, Task 2: Gaming technology investigation","qualification":"BTEC Level 3 National Extended Diploma in Information Technology"},"it-level-3-btec-national-u3-project-2-task-1":{"fullTitle":"Project 2: Exploring Social Media Campaigns, Task 1: Social Media Analysis","qualification":"BTEC Level 3 National Extended Diploma in Information Technology"},"it-level-3-btec-national-u3-project-2-task-2":{"fullTitle":"Project 2: Exploring Social Media Campaigns, Task 2: Running a Social Media Campaign","qualification":"BTEC Level 3 National Extended Diploma in Information Technology"},"it-level-3-btec-national-u4-project-3-task-1":{"fullTitle":"Project 3: Developing Game Ideas, Task 1: Game Concept Iteration","qualification":"BTEC Level 3 National Extended Diploma in Information Technology"},"it-level-3-btec-national-u4-u7-u8-project-3-task-2":{"fullTitle":"Project 3: Developing Game Ideas, Task 2: Game Development and Review","qualification":"BTEC Level 3 National Extended Diploma in Information Technology"},"it-level-3-btec-national-u20-project-4-task-1":{"ivNotes":[{"q":"details","text":"Task titles across Projects 4 and 5 use different styles (\"- Task 1\", \"(Task 2)\", \"– Task 3\"). Use one style.","minor":true}],"fullTitle":"Project 4: Managing an IT Enterprise - Task 1","qualification":"BTEC Level 3 National Extended Diploma in Information Technology"},"it-level-3-btec-national-u20-project-4-task-2":{"fullTitle":"Project 4: Managing an IT Enterprise (Task 2)","qualification":"BTEC Level 3 National Extended Diploma in Information Technology"},"it-level-3-btec-national-u20-project-4-task-3":{"fullTitle":"Project 4: Managing an IT Enterprise - Task 3","qualification":"BTEC Level 3 National Extended Diploma in Information Technology"},"it-level-3-btec-national-u12-project-5-task-1":{"fullTitle":"Project 5: Supporting an IT Infrastructure (Task 1)","qualification":"BTEC Level 3 National Extended Diploma in Information Technology"},"it-level-3-btec-national-u12-project-5-task-2":{"fullTitle":"Project 5: Supporting an IT Infrastructure (Task 2)","qualification":"BTEC Level 3 National Extended Diploma in Information Technology"},"it-level-3-btec-national-u12-project-5-task-3":{"fullTitle":"Project 5: Supporting an IT Infrastructure (Task 3)","qualification":"BTEC Level 3 National Extended Diploma in Information Technology"},"it-level-3-btec-national-u6-u16-u9-project-6-task-1":{"ivNotes":[{"q":"requirements","text":"Typos in criteria: Unit 6 A.D1 \"used to produced\", \"high performance\" (spec \"high-performance\") and \"which\" (spec \"that\") in A.M1 and A.D1; Unit 16 A.D1 \"organization\".","minor":true}],"fullTitle":"Project 6: Developing a commercial website, Task 1: Investigating industry successes","qualification":"BTEC Level 3 National Extended Diploma in Information Technology"},"it-level-3-btec-national-u6-u16-u9-project-6-task-2":{"ivNotes":[{"q":"criteria","text":"Unit 16 \"B.M3\" is the wrong code: the wording is C.M3's. Change the code to C.M3."}],"fullTitle":"Project 6: Developing a commercial website, Task 2: Develop and deploy a website","qualification":"BTEC Level 3 National Extended Diploma in Information Technology"},"it-level-3-btec-national-u9-project-6-task-3":{"fullTitle":"Project 6: Developing a commercial website, Task 3: Project Closure and Reflection","qualification":"BTEC Level 3 National Extended Diploma in Information Technology"},"it-level-2-tlm-cyber-security-csdf1-assignment-1":{"ivNotes":[{"q":"deadlines","text":"The brief shows 15/10/2024 to 19/11/2024. Update to 15/10/2026 to 19/11/2026."},{"q":"details","text":"Qualification shown as \"TLM Level 2 Information Technology and Cyber Security\". Use \"TLM Level 2 Certificate in Cyber Security Principles\"."},{"q":"evidence","text":"The contents page is left over from a Unit 3 Social Media brief. Replace it with this brief's contents."},{"q":"details","text":"The unit title uses \"&\" where the handbook has \"and\".","minor":true}],"qualification":"TLM Level 2 Information Technology and Cyber Security"},"it-level-2-tlm-cyber-security-csdf2-assignment-1":{"ivNotes":[{"q":"requirements","text":"2.2 reads \"I can describe the hardware features of an IT system\". The handbook says \"that protect an IT system\". Use the handbook wording."},{"q":"deadlines","text":"The brief shows 27/01/25 to 04/04/25. Update to 27/01/2027 to 04/04/2027."},{"q":"details","text":"Qualification shown as \"TLM Level 2 Information Technology and Cyber Security\". Use \"TLM Level 2 Certificate in Cyber Security Principles\"."},{"q":"details","text":"The unit title leaves out \"Cyber\". Use the handbook title."},{"q":"requirements","text":"Typos in criteria: 1.4 \"difference user services\", 1.5 is missing \"main\", 2.1 \"analysis the common place treats associated the with\", 2.5 \"port used one a server in relation the key services\".","minor":true},{"q":"criteria","text":"Heading reads \"CRITIERA TARGETED IN THIS BREIF\".","minor":true}],"qualification":"TLM Level 2 Information Technology and Cyber Security"},"it-level-2-tlm-cyber-security-csdf3-assignment-1":{"ivNotes":[{"q":"deadlines","text":"The brief shows 28/03/24 to 04/05/25, so the issue date is a year before the deadline. Update to 28/03/2027 to 04/05/2027."},{"q":"details","text":"Qualification shown as \"TLM Level 2 Information Technology and Cyber Security\". Use \"TLM Level 2 Certificate in Cyber Security Principles\"."},{"q":"details","text":"The unit title says \"Development\" where the handbook says \"Deployment\"."},{"q":"criteria","text":"Heading reads \"CRITIERA TARGETED IN THIS BREIF\".","minor":true}],"qualification":"TLM Level 2 Information Technology and Cyber Security"},"it-level-2-tlm-cyber-security-csdf4-extended-project":{"ivNotes":[{"q":"requirements","text":"4.3 says \"State the expected result\" and 4.4 says \"record the actual results\". The handbook asks to \"explain the expected results\" and \"describe the test results and what they mean\". Match the handbook wording."},{"q":"deadlines","text":"Issue date and hand-in deadline say \"To be confirmed\". Add them before the brief is issued."},{"q":"criteria","text":"The criteria table lists short labels such as \"4.3 Expected results\" rather than the \"I can\" statements. Show the full statements, as CSDF1 to CSDF3 do.","minor":true}],"fullTitle":"Securing Hilltop College’s New Online System","qualification":"TLM Level 2 Certificate in Cyber Security Principles"}}};
  /* ---- Second Marker runs as part of Crafticulum's own page. Its screens sit in their own style scope so the two sets of styles can't interfere,
     and it reads the app's settings, courses, briefs and dates directly. ---- */
  const CC = window.ccMarkerCtx();
  const SMDOC = SMHOST.attachShadow({ mode: "open" });
  SMDOC.innerHTML = "<style>" + SM_CSS + "</style><div class=\"sm-root\" data-theme=\"" + (CC.theme === "dark" ? "dark" : "light") + "\">" + SM_HTML + "</div>";
  const SMROOT = SMDOC.querySelector(".sm-root");
  var CCNS = CC.ns || "";
  const __active = () => { try { return window.ccMarkerActive(); } catch (e) { return false; } };
  const __inside = e => { const t = e.composedPath ? e.composedPath()[0] : e.target; return !!(t && t.getRootNode && t.getRootNode() === SMDOC); };
  /* what the old code listened for on the whole document: inside the marking screens always; key presses, paste and mouse-up elsewhere only while Second Marker is the page on show */
  function __on(type, fn, opts) {
    if (type === "DOMContentLoaded") { setTimeout(fn, 0); return; }
    if (type === "visibilitychange") { document.addEventListener(type, fn, opts); return; }
    SMDOC.addEventListener(type, fn, opts);
    if (/^(keydown|keyup|paste|mouseup)$/.test(type)) document.addEventListener(type, e => {
      if (!__active() || __inside(e)) return;
      if (type !== "mouseup" && e.target !== document.body && e.target !== document.documentElement) return;
      fn(e);
    }, opts);
  }
  function __won(type, fn, opts) { if (type === "pagehide" || type === "beforeunload") window.addEventListener(type, fn, opts); else window.addEventListener(type, e => { if (__active()) fn(e); }, opts); }
  /* the text someone has selected in the work, wherever the browser keeps it */
  function __getSel() {
    const s = window.getSelection ? window.getSelection() : null; if (!s) return null;
    let range = null;
    const from = sr => { if (!sr) return null; const r = document.createRange(); r.setStart(sr.startContainer, sr.startOffset); r.setEnd(sr.endContainer, sr.endOffset); return r; };
    if (s.getComposedRanges) { try { range = from(s.getComposedRanges({ shadowRoots: [SMDOC] })[0]); } catch (e) { try { range = from(s.getComposedRanges(SMDOC)[0]); } catch (e2) {} } }
    if (!range && SMDOC.getSelection) { try { const x = SMDOC.getSelection(); if (x && x.rangeCount) range = x.getRangeAt(0); } catch (e) {} }
    if (!range && s.rangeCount) { const r = s.getRangeAt(0); if (r.commonAncestorContainer.getRootNode && r.commonAncestorContainer.getRootNode() === SMDOC) range = r; }
    return { isCollapsed: !range || range.collapsed, rangeCount: range ? 1 : 0, getRangeAt: () => range, toString: () => (range ? range.toString() : ""), removeAllRanges: () => { try { s.removeAllRanges(); } catch (e) {} } };
  }
  /* page positions, measured from the top left of the marking screens */
  const __sy = () => -SMROOT.getBoundingClientRect().top, __sx = () => -SMROOT.getBoundingClientRect().left;
  function __restart() { try { location.hash = "#marker"; } catch (e) {} location.reload(); }
  /* buttons and boxes in the screens say what to run in a data-sm-<event> attribute; this runs it with the marking code in reach */
  function __run(code, el, event) { return eval("(function(event){" + code + "\n})").call(el, event); }
  ["click", "change", "input", "keydown", "mousedown"].forEach(type => SMDOC.addEventListener(type, e => {
    const path = e.composedPath ? e.composedPath() : [e.target];
    for (const el of path) { if (el === SMDOC) break; if (el.getAttribute && el.hasAttribute("data-sm-" + type)) { try { __run(el.getAttribute("data-sm-" + type), el, e); } catch (err) { console.error(err); } if (e.cancelBubble) break; } }
  }));
  SMDOC.addEventListener("blur", e => { const el = e.target; if (el && el.getAttribute && el.hasAttribute("data-sm-blur")) { try { __run(el.getAttribute("data-sm-blur"), el, e); } catch (err) { console.error(err); } } }, true);

  window.BTEC_API_KEY = "kept-on-the-server";   // never a real key: checks go to marker/api.php, which adds the site's key
  window.BTEC_ENABLE_LINKS = false; window.BTEC_GOOGLE_CLIENT_ID = ""; window.BTEC_MS_CLIENT_ID = ""; window.BTEC_MS_TENANT = "common";
  window.BTEC_EMAIL_DOMAIN = CC.emailDomain || ""; window.BTEC_BRAND_PURPLE = ""; window.BTEC_BRAND_BLUE = "";
  /* one source of truth: subjects, levels, units, criteria, briefs and dates are Crafticulum's own */
  const __uk = iso => { const m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(iso || ""); return m ? m[3] + "/" + m[2] + "/" + m[1] : ""; };
  function __courses() {
    const P = window.ccMarkerData(), data = [], META = SM_META;
    (P.units || []).forEach(x => {
      const m = META.levels[x.s + "|" + x.l] || null, quals = (x.src || []).map(s => s.qualification).filter(Boolean);
      const units = x.u.map(u => { const o = { title: u.t, criteria: u.c.map(c => ({ code: c[0], desc: c[1] })) }; if (u.g) o.group = u.g; if (u.a) o.outcomes = u.a; if (u.o) o.optional = true; return o; });
      const level = { level: x.l || "Level", programmes: m ? m.programmes : (quals.length ? [...new Set(quals)] : [(x.s + " " + (x.l || "")).trim()]), units };
      if (m) { if (m.grading) level.grading = m.grading; if (m.note) level.note = m.note; }
      else if (!units.some(u => u.criteria.some(c => /[PMD]\d/.test(c.code)))) { level.grading = "pass"; level.note = "Graded Pass only. Every targeted criterion must be evidenced."; }
      let s = data.find(d => d.subject === x.s); if (!s) { s = { subject: x.s, levels: [] }; data.push(s); }
      s.levels.push(level);
    });
    const briefs = (P.briefs || []).map(b => { const m = META.briefs[b.id] || {};
      const o = { id: b.id, subject: b.s, level: b.l, units: b.u || [], assignment: b.a || "", title: b.t || "", handout: __uk(b.out), deadline: __uk(b.due), qualification: m.qualification || "", criteria: (b.c || []).map(k => ({ unit: k[0], code: k[1], task: k[2] || "" })), text: b.x || "" };
      if (m.ivNotes) o.ivNotes = m.ivNotes; if (m.fullTitle) o.fullTitle = m.fullTitle; return o; });
    return { data, briefs, sel: P.sel || null };
  }
  const __first = __courses();
  window.BTEC_DATA = __first.data; window.BTEC_BRIEFS = __first.briefs;

/* ===== template.js ===== */
// ─────────────────────────────────────────────────────────────
// Embedded Pearson BTEC assessment record template (.docx, base64)
// Used by the Word export. To swap in a different copy of the
// template, use the "Upload template" override in Settings, or
// regenerate this file from a new .docx.
// ─────────────────────────────────────────────────────────────

window.BTEC_TEMPLATE_B64 = "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LjM4IDEuNjggMi4xMiAxLjU2IDEuNjUgMS4zMiAxLjIgMSAwLjggMC43MyAwLjQ5IDAuMzkgMC4yMiAwIDAgMC4zNyAwLjU5IDAuNjIgMS42OCAwLjYyIDIuOTMgMC42MiA0LjE4IDAuMzggNS4yOCAwIDUuODcgMCA1Ljg3IDAgNS44OCAwIDUuODkgMC4yMSA1Ljc3IDAuNDEgNS42MyAwLjU5IDUuNDggMS4yMiA0Ljg4IDEuNjMgNC4xNiAxLjc1IDMuMzggMS43NyAzLjI0IDEuNzggMy4xIDEuNzggMi45NiAxLjc4IDQuNDggMS42NiA1LjM3IDEuNjYgNS4zNkwxLjY2IDUuMzZDMi4yNSA0LjczIDIuNjIgMy44OCAyLjYyIDIuOTVMMi42MiAyLjk1QzIuNjIgMi4wMiAyLjI2IDEuMTcgMS42NiAwLjU0TDEuNjYgMC41NFoiIGNsYXNzPSJNc2Z0T2ZjUmVzcG9uc2l2ZV9GaWxsXzBkMDA0ZCIgc3Ryb2tlPSJub25lIiBzdHJva2Utd2lkdGg9IjEiIHN0cm9rZS1saW5lY2FwPSJidXR0IiBzdHJva2UtbGluZWpvaW49Im1pdGVyIiBzdHJva2UtbWl0ZXJsaW1pdD0iNCIgZmlsbD0iIzBEMDA0RCIgZmlsbC1vcGFjaXR5PSIxIi8+PHBhdGggZD0iTTIzLjgzIDEuOEMyMi42NSAxLjggMjEuNyAyLjczIDIxLjcgMy45IDIxLjcgNS4wNyAyMi42NCA1Ljk5IDIzLjgzIDUuOTkgMjUuMDIgNS45OSAyNS45NiA1LjA2IDI1Ljk2IDMuOSAyNS45NiAyLjc0IDI1LjAyIDEuOCAyMy44NCAxLjhaTTIzLjgzIDUuMDRDMjMuMTUgNS4wNCAyMi43MyA0LjUxIDIyLjczIDMuODkgMjIuNzMgMy4yNyAyMy4xNSAyLjc0IDIzLjgzIDIuNzQgMjQuNTEgMi43NCAyNC45MyAzLjI3IDI0LjkzIDMuODkgMjQuOTMgNC41MSAyNC41MSA1LjA0IDIzLjgzIDUuMDRaIiBjbGFzcz0iTXNmdE9mY1Jlc3BvbnNpdmVfRmlsbF8wZDAwNGQiIHN0cm9rZT0ibm9uZSIgc3Ryb2tlLXdpZHRoPSIxIiBzdHJva2UtbGluZWNhcD0iYnV0dCIgc3Ryb2tlLWxpbmVqb2luPSJtaXRlciIgc3Ryb2tlLW1pdGVybGltaXQ9IjQiIGZpbGw9IiMwRDAwNEQiIGZpbGwtb3BhY2l0eT0iMSIvPjxwYXRoIGQ9Ik04LjkzIDEuOEM3Ljc1IDEuOCA2LjkxIDIuNzUgNi45MSAzLjkgNi45MSA1LjAzIDcuNzIgNS45OSA4LjkgNS45OSA5Ljc1IDUuOTkgMTAuNTMgNS40OCAxMC44MiA0LjY4TDkuNzkgNC42OEM5LjYzIDQuODkgOS4zMiA1LjA3IDguOTEgNS4wNyA4LjI2IDUuMDcgNy45NyA0LjYxIDcuOTQgNC4yNkwxMC45IDQuMjZDMTAuOTIgNC4xNSAxMC45MyAzLjk2IDEwLjkzIDMuODMgMTAuOTMgMi42MSAxMC4wMyAxLjggOC45NSAxLjhaTTcuOTMgMy40OUM3LjkzIDMuMDUgOC4zNiAyLjcgOC44OSAyLjcgOS40MiAyLjcgOS44NSAzLjA1IDkuODUgMy40OUw3LjkyIDMuNDlaIiBjbGFzcz0iTXNmdE9mY1Jlc3BvbnNpdmVfRmlsbF8wZDAwNGQiIHN0cm9rZT0ibm9uZSIgc3Ryb2tlLXdpZHRoPSIxIiBzdHJva2UtbGluZWNhcD0iYnV0dCIgc3Ryb2tlLWxpbmVqb2luPSJtaXRlciIgc3Ryb2tlLW1pdGVybGltaXQ9IjQiIGZpbGw9IiMwRDAwNEQiIGZpbGwtb3BhY2l0eT0iMSIvPjxwYXRoIGQ9Ik0yOCAxLjhDMjcgMS44IDI2LjIgMi42MSAyNi4yIDMuNkwyNi4yIDUuODggMjcuMjEgNS44OCAyNy4yMSAzLjZDMjcuMjEgMy4wOCAyNy41OCAyLjc2IDI4IDIuNzYgMjguNDIgMi43NiAyOC43OSAzLjA5IDI4Ljc5IDMuNjFMMjguNzkgNS44OUMyOC43OSA1Ljg5IDI5LjgxIDUuODkgMjkuODEgNS44OUwyOS44MSAzLjYxQzI5LjgxIDIuNjEgMjkgMS44IDI4LjAxIDEuOFoiIGNsYXNzPSJNc2Z0T2ZjUmVzcG9uc2l2ZV9GaWxsXzBkMDA0ZCIgc3Ryb2tlPSJub25lIiBzdHJva2Utd2lkdGg9IjEiIHN0cm9rZS1saW5lY2FwPSJidXR0IiBzdHJva2UtbGluZWpvaW49Im1pdGVyIiBzdHJva2UtbWl0ZXJsaW1pdD0iNCIgZmlsbD0iIzBEMDA0RCIgZmlsbC1vcGFjaXR5PSIxIi8+PHBhdGggZD0iTTE4LjQ1IDEuOTJDMTguMzQgMS44NyAxOC4xIDEuNzkgMTcuODIgMS43OSAxNy4wMiAxLjc5IDE2LjcxIDIuNCAxNi42OCAyLjQ3TDE2LjY4IDEuODkgMTUuNzIgMS44OSAxNS43MiA1Ljg4QzE1LjcyIDUuODggMTYuNzMgNS44OCAxNi43MyA1Ljg4TDE2LjczIDMuMjdDMTYuNzYgMy4yMiAxNy4wNCAyLjczIDE3LjYzIDIuNzMgMTcuODUgMi43MyAxOC4wNSAyLjggMTguMSAyLjgyTDE4LjQ1IDEuOTFaIiBjbGFzcz0iTXNmdE9mY1Jlc3BvbnNpdmVfRmlsbF8wZDAwNGQiIHN0cm9rZT0ibm9uZSIgc3Ryb2tlLXdpZHRoPSIxIiBzdHJva2UtbGluZWNhcD0iYnV0dCIgc3Ryb2tlLWxpbmVqb2luPSJtaXRlciIgc3Ryb2tlLW1pdGVybGltaXQ9IjQiIGZpbGw9IiMwRDAwNEQiIGZpbGwtb3BhY2l0eT0iMSIvPjxwYXRoIGQ9Ik0xMy4yNCAxLjhDMTIuMDYgMS44IDExLjExIDIuNzIgMTEuMTEgMy45MSAxMS4xMSA1LjAzIDExLjk1IDUuOTkgMTMuMDggNS45OSAxMy42NSA1Ljk5IDE0LjEgNS43NCAxNC4zMyA1LjM5TDE0LjMzIDUuODkgMTUuMzUgNS44OUMxNS4zNSA1Ljg5IDE1LjM1IDMuODQgMTUuMzUgMy44NCAxNS4zNSAyLjY5IDE0LjM5IDEuOCAxMy4yMyAxLjhaTTEzLjI0IDUuMDZDMTIuNTYgNS4wNiAxMi4xNCA0LjUyIDEyLjE0IDMuOSAxMi4xNCAzLjI4IDEyLjU2IDIuNzQgMTMuMjQgMi43NCAxMy45MiAyLjc0IDE0LjM0IDMuMjggMTQuMzQgMy45IDE0LjM0IDQuNTIgMTMuOTIgNS4wNiAxMy4yNCA1LjA2WiIgY2xhc3M9Ik1zZnRPZmNSZXNwb25zaXZlX0ZpbGxfMGQwMDRkIiBzdHJva2U9Im5vbmUiIHN0cm9rZS13aWR0aD0iMSIgc3Ryb2tlLWxpbmVjYXA9ImJ1dHQiIHN0cm9rZS1saW5lam9pbj0ibWl0ZXIiIHN0cm9rZS1taXRlcmxpbWl0PSI0IiBmaWxsPSIjMEQwMDREIiBmaWxsLW9wYWNpdHk9IjEiLz48cGF0aCBkPSJNMjAuMjggMy41MUMxOS42MSAzLjM3IDE5LjM0IDMuMzEgMTkuMzQgMy4wOCAxOS4zNCAyLjgyIDE5LjY4IDIuNjkgMjAuMDUgMi42OSAyMC42IDIuNjkgMjEuMDEgMyAyMS4wNiAzLjAzTDIxLjU2IDIuMjlDMjEuMyAyLjEyIDIwLjgxIDEuOCAyMC4wNCAxLjggMTguOTcgMS44IDE4LjM1IDIuNDEgMTguMzUgMy4wOSAxOC4zNSAzLjggMTkuMDQgNC4xNCAxOS44MyA0LjMxIDIwLjQzIDQuNDQgMjAuNjIgNC40OSAyMC42MiA0LjcgMjAuNjIgNC45OSAyMC4yOCA1LjExIDE5LjkyIDUuMTEgMTkuMzggNS4xMSAxOC45MyA0LjgzIDE4LjY2IDQuNTRMMTguMSA1LjI3QzE4LjMzIDUuNDkgMTguOTQgNiAxOS45MSA2IDIwLjg4IDYgMjEuNjQgNS40NSAyMS42NCA0Ljc3IDIxLjY0IDMuOTUgMjEuMDggMy42OSAyMC4yOSAzLjUyWiIgY2xhc3M9Ik1zZnRPZmNSZXNwb25zaXZlX0ZpbGxfMGQwMDRkIiBzdHJva2U9Im5vbmUiIHN0cm9rZS13aWR0aD0iMSIgc3Ryb2tlLWxpbmVjYXA9ImJ1dHQiIHN0cm9rZS1saW5lam9pbj0ibWl0ZXIiIHN0cm9rZS1taXRlcmxpbWl0PSI0IiBmaWxsPSIjMEQwMDREIiBmaWxsLW9wYWNpdHk9IjEiLz48cGF0aCBkPSJNNS4yMiAwIDMuMjIgMCAzLjIyIDUuODlDMy4yMiA1Ljg5IDQuMjIgNS44OSA0LjIyIDUuODlMNC4yMiAzLjc3IDUuMjEgMy43N0M2LjM4IDMuNzcgNy4xNiAyLjk2IDcuMTYgMS44OSA3LjE2IDAuODIgNi4zOCAwLjAxIDUuMjEgMC4wMVpNNS4wOSAyLjgxIDQuMjIgMi44MSA0LjIyIDAuOTYgNS4wOSAwLjk2QzUuNzggMC45NiA2LjE0IDEuMzMgNi4x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";

/* ===== iv-templates.js ===== */
// The college's IV forms (Word), filled in by iv.js.
// brief: Internal Verification of Assignment Brief, as supplied.
// decisions: Internal Verification of Assessment Decisions, built in the same layout.
window.BTEC_IV_TEMPLATES = {
  brief: "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aHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIKICAgICAgICAgICAgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iCiAgICAgICAgICAgIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIgogICAgICAgICAgICB4bWxuczpzdEV2dD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlRXZlbnQjIgogICAgICAgICAgICB4bWxuczppbGx1c3RyYXRvcj0iaHR0cDovL25zLmFkb2JlLmNvbS9pbGx1c3RyYXRvci8xLjAvIgogICAgICAgICAgICB4bWxuczp4bXBUUGc9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC90L3BnLyIKICAgICAgICAgICAgeG1sbnM6c3REaW09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9EaW1lbnNpb25zIyIKICAgICAgICAgICAgeG1sbnM6eG1wRz0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL2cvIgogICAgICAgICAgICB4bWxuczpwZGY9Imh0dHA6Ly9ucy5hZG9iZS5jb20vcGRmLzEuMy8iCiAgICAgICAgICAgIHhtbG5zOnBob3Rvc2hvcD0iaHR0cDovL25zLmFkb2JlLmNvbS9waG90b3Nob3AvMS4wLyIKICAgICAgICAgICAgeG1sbnM6dGlmZj0iaHR0cDovL25zLmFkb2JlLmNvbS90aWZmLzEuMC8iCiAgICAgICAgICAgIHhtbG5zOmV4aWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20vZXhpZi8xLjAvIj4KICAgICAgICAgPGRjOmZvcm1hdD5pbWFnZS9wbmc8L2RjOmZvcm1hdD4KICAgICAgICAgPGRjOnRpdGxlPgogICAgICAgICAgICA8cmRmOkFsdD4KICAgICAgICAgICAgICAgPHJkZjpsaSB4bWw6bGFuZz0ieC1kZWZhdWx0Ij5QcmludDwvcmRmOmxpPgogICAgICAgICAgICA8L3JkZjpBbHQ+CiAgICAgICAgIDwvZGM6dGl0bGU+CiAgICAgICAgIDx4bXA6TWV0YWRhdGFEYXRlPjIwMTYtMDUtMjZUMTQ6Mzk6NDArMDE6MDA8L3htcDpNZXRhZGF0YURhdGU+CiAgICAgICAgIDx4bXA6TW9kaWZ5RGF0ZT4yMDE2LTA1LTI2VDE0OjM5OjQwKzAxOjAwPC94bXA6TW9kaWZ5RGF0ZT4KICAgICAgICAgPHhtcDpDcmVhdGVEYXRlPjIwMTUtMDktMjJUMDc6NTc6NDQrMDE6MDA8L3htcDpDcmVhdGVEYXRlPgogICAgICAgICA8eG1wOkNyZWF0b3JUb29sPkFkb2JlIFBob3Rvc2hvcCBDQyAyMDE1IChNYWNpbnRvc2gpPC94bXA6Q3JlYXRvclRvb2w+CiAgICAgICAgIDx4bXBNTTpJbnN0YW5jZUlEPnhtcC5paWQ6YTcwYmY2OWItZGExMy00NjNmLWFlNWQtYWFmNTVmZjM0YTBjPC94bXBNTTpJbnN0YW5jZUlEPgogICAgICAgICA8eG1wTU06RG9jdW1lbnRJRD5hZG9iZTpkb2NpZDpwaG90b3Nob3A6YTlhNjMxOWYtNjNkNS0xMTc5LTk2MTQtZWUyYjNkMjcyOTFiPC94bXBNTTpEb2N1bWVudElEPgogICAgICAgICA8eG1wTU06T3JpZ2luYWxEb2N1bWVudElEPnV1aWQ6NUQyMDg5MjQ5M0JGREIxMTkxNEE4NTkwRDMxNTA4Qzg8L3htcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD4KICAgICAgICAgPHhtcE1NOlJlbmRpdGlvbkNsYXNzPnByb29mOnBkZjwveG1wTU06UmVuZGl0aW9uQ2xhc3M+CiAgICAgICAgIDx4bXBNTTpEZXJpdmVkRnJvbSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgIDxzdFJlZjppbnN0YW5jZUlEPnhtcC5paWQ6ZjJiMjcyYzYtNGY5Ny00ZWViLWEwYzQtNzY1NWJhOTA4NmIyPC9zdFJlZjppbnN0YW5jZUlEPgogICAgICAgICAgICA8c3RSZWY6ZG9jdW1lbnRJRD5hZG9iZTpkb2NpZDpwaG90b3Nob3A6MDVkODllMjYtNDg0NC0xMTc5LWE1MmUtOTk4OTA4YWQ5ZjE2PC9zdFJlZjpkb2N1bWVudElEPgogICAgICAgICAgICA8c3RSZWY6b3JpZ2luYWxEb2N1bWVudElEPnV1aWQ6NUQyMDg5MjQ5M0JGREIxMTkxNEE4NTkwRDMxNTA4Qzg8L3N0UmVmOm9yaWdpbmFsRG9jdW1lbnRJRD4KICAgICAgICAgICAgPHN0UmVmOnJlbmRpdGlvbkNsYXNzPnByb29mOnBkZjwvc3RSZWY6cmVuZGl0aW9uQ2xhc3M+CiAgICAgICAgIDwveG1wTU06RGVyaXZlZEZyb20+CiAgICAgICAgIDx4bXBNTTpIaXN0b3J5PgogICAgICAgICAgICA8cmRmOlNlcT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+c2F2ZWQ8L3N0RXZ0OmFjdGlvbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0Omluc3RhbmNlSUQ+eG1wLmlpZDphYTdlM2YxMS0xMDMzLTRmZmQtYjU3OS1kMTEyYWZkZTc4NGI8L3N0RXZ0Omluc3RhbmNlSUQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDp3aGVuPjIwMTUtMDgtMjRUMTU6MjM6MzErMDE6MDA8L3N0RXZ0OndoZW4+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpzb2Z0d2FyZUFnZW50PkFkb2JlIElsbHVzdHJhdG9yIENDIDIwMTUgKE1hY2ludG9zaCk8L3N0RXZ0OnNvZnR3YXJlQWdlbnQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpjaGFuZ2VkPi88L3N0RXZ0OmNoYW5nZWQ+CiAgICAgICAgICAgICAgIDwvcmRmOmxpPgogICAgICAgICAgICAgICA8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmFjdGlvbj5jb252ZXJ0ZWQ8L3N0RXZ0OmFjdGlvbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OnBhcmFtZXRlcnM+ZnJvbSBhcHBsaWNhdGlvbi9wb3N0c2NyaXB0IHRvIGFwcGxpY2F0aW9uL3ZuZC5hZG9iZS5pbGx1c3RyYXRvcjwvc3RFdnQ6cGFyYW1ldGVycz4KICAgICAgICAgICAgICAgPC9yZGY6bGk+CiAgICAgICAgICAgICAgIDxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6YWN0aW9uPnNhdmVkPC9zdEV2dDphY3Rpb24+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDppbnN0YW5jZUlEPnhtcC5paWQ6MGRiYTU3YzYtNTQ0OS00Y2EzLThlZjYtZDZhZDViNmRhMGVjPC9zdEV2dDppbnN0YW5jZUlEPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6d2hlbj4yMDE1LTA5LTIyVDA3OjU3OjQ0KzAxOjAwPC9zdEV2dDp3aGVuPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6c29mdHdhcmVBZ2VudD5BZG9iZSBJbGx1c3RyYXRvciBDQyAyMDE1IChNYWNpbnRvc2gpPC9zdEV2dDpzb2Z0d2FyZUFnZW50PgogICAgICAgICAgICAgICAgICA8c3RFdnQ6Y2hhbmdlZD4vPC9zdEV2dDpjaGFuZ2VkPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+Y29udmVydGVkPC9zdEV2dDphY3Rpb24+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpwYXJhbWV0ZXJzPmZyb20gYXBwbGljYXRpb24vcG9zdHNjcmlwdCB0byBhcHBsaWNhdGlvbi92bmQuYWRvYmUuaWxsdXN0cmF0b3I8L3N0RXZ0OnBhcmFtZXRlcnM+CiAgICAgICAgICAgICAgIDwvcmRmOmxpPgogICAgICAgICAgICAgICA8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmFjdGlvbj5jb252ZXJ0ZWQ8L3N0RXZ0OmFjdGlvbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OnBhcmFtZXRlcnM+ZnJvbSBhcHBsaWNhdGlvbi9wb3N0c2NyaXB0IHRvIGFwcGxpY2F0aW9uL3ZuZC5hZG9iZS5pbGx1c3RyYXRvcjwvc3RFdnQ6cGFyYW1ldGVycz4KICAgICAgICAgICAgICAgPC9yZGY6bGk+CiAgICAgICAgICAgICAgIDxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6YWN0aW9uPmNvbnZlcnRlZDwvc3RFdnQ6YWN0aW9uPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6cGFyYW1ldGVycz5mcm9tIGFwcGxpY2F0aW9uL3Bvc3RzY3JpcHQgdG8gaW1hZ2UvZXBzZjwvc3RFdnQ6cGFyYW1ldGVycz4KICAgICAgICAgICAgICAgPC9yZGY6bGk+CiAgICAgICAgICAgICAgIDxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6YWN0aW9uPnNhdmVkPC9zdEV2dDphY3Rpb24+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDppbnN0YW5jZUlEPnhtcC5paWQ6YjM5ODljN2YtYTRhYy00N2VjLWJkZmUtNjJmYWVjNGUyYTQyPC9zdEV2dDppbnN0YW5jZUlEPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6d2hlbj4yMDE2LTA0LTIxVDEyOjM5OjAyKzAxOjAwPC9zdEV2dDp3aGVuPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6c29mdHdhcmVBZ2VudD5BZG9iZSBQaG90b3Nob3AgQ0MgMjAxNSAoTWFjaW50b3NoKTwvc3RFdnQ6c29mdHdhcmVBZ2VudD4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmNoYW5nZWQ+Lzwvc3RFdnQ6Y2hhbmdlZD4KICAgICAgICAgICAgICAgPC9yZGY6bGk+CiAgICAgICAgICAgICAgIDxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6YWN0aW9uPmNvbnZlcnRlZDwvc3RFdnQ6YWN0aW9uPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6cGFyYW1ldGVycz5mcm9tIGFwcGxpY2F0aW9uL3Bvc3RzY3JpcHQgdG8gaW1hZ2UvanBlZzwvc3RFdnQ6cGFyYW1ldGVycz4KICAgICAgICAgICAgICAgPC9yZGY6bGk+CiAgICAgICAgICAgICAgIDxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6YWN0aW9uPmRlcml2ZWQ8L3N0RXZ0OmFjdGlvbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OnBhcmFtZXRlcnM+Y29udmVydGVkIGZyb20gaW1hZ2UvZXBzZiB0byBpbWFnZS9qcGVnPC9zdEV2dDpwYXJhbWV0ZXJzPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+c2F2ZWQ8L3N0RXZ0OmFjdGlvbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0Omluc3RhbmNlSUQ+eG1wLmlpZDowNDEwZGIzZi1mNTA5LTRhMTYtYjU5Mi0yNDFlOTQyZWU1OWE8L3N0RXZ0Omluc3RhbmNlSUQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDp3aGVuPjIwMTYtMDQtMjFUMTI6Mzk6MDIrMDE6MDA8L3N0RXZ0OndoZW4+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpzb2Z0d2FyZUFnZW50PkFkb2JlIFBob3Rvc2hvcCBDQyAyMDE1IChNYWNpbnRvc2gpPC9zdEV2dDpzb2Z0d2FyZUFnZW50PgogICAgICAgICAgICAgICAgICA8c3RFdnQ6Y2hhbmdlZD4vPC9zdEV2dDpjaGFuZ2VkPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+c2F2ZWQ8L3N0RXZ0OmFjdGlvbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0Omluc3RhbmNlSUQ+eG1wLmlpZDpmMmIyNzJjNi00Zjk3LTRlZWItYTBjNC03NjU1YmE5MDg2YjI8L3N0RXZ0Omluc3RhbmNlSUQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDp3aGVuPjIwMTYtMDUtMjZUMTQ6Mzk6NDArMDE6MDA8L3N0RXZ0OndoZW4+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpzb2Z0d2FyZUFnZW50PkFkb2JlIFBob3Rvc2hvcCBDQyAyMDE1IChNYWNpbnRvc2gpPC9zdEV2dDpzb2Z0d2FyZUFnZW50PgogICAgICAgICAgICAgICAgICA8c3RFdnQ6Y2hhbmdlZD4vPC9zdEV2dDpjaGFuZ2VkPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+Y29udmVydGVkPC9zdEV2dDphY3Rpb24+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpwYXJhbWV0ZXJzPmZyb20gaW1hZ2UvanBlZyB0byBpbWFnZS9wbmc8L3N0RXZ0OnBhcmFtZXRlcnM+CiAgICAgICAgICAgICAgIDwvcmRmOmxpPgogICAgICAgICAgICAgICA8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmFjdGlvbj5kZXJpdmVkPC9zdEV2dDphY3Rpb24+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpwYXJhbWV0ZXJzPmNvbnZlcnRlZCBmcm9tIGltYWdlL2pwZWcgdG8gaW1hZ2UvcG5nPC9zdEV2dDpwYXJhbWV0ZXJzPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+c2F2ZWQ8L3N0RXZ0OmFjdGlvbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0Omluc3RhbmNlSUQ+eG1wLmlpZDphNzBiZjY5Yi1kYTEzLTQ2M2YtYWU1ZC1hYWY1NWZmMzRhMGM8L3N0RXZ0Omluc3RhbmNlSUQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDp3aGVuPjIwMTYtMDUtMjZUMTQ6Mzk6NDArMDE6MDA8L3N0RXZ0OndoZW4+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpzb2Z0d2FyZUFnZW50PkFkb2JlIFBob3Rvc2hvcCBDQyAyMDE1IChNYWNpbnRvc2gpPC9zdEV2dDpzb2Z0d2FyZUFnZW50PgogICAgICAgICAgICAgICAgICA8c3RFdnQ6Y2hhbmdlZD4vPC9zdEV2dDpjaGFuZ2VkPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgPC9yZGY6U2VxPgogICAgICAgICA8L3htcE1NOkhpc3Rvcnk+CiAgICAgICAgIDxpbGx1c3RyYXRvcjpTdGFydHVwUHJvZmlsZT5QcmludDwvaWxsdXN0cmF0b3I6U3RhcnR1cFByb2ZpbGU+CiAgICAgICAgIDx4bXBUUGc6SGFzVmlzaWJsZU92ZXJwcmludD5GYWxzZTwveG1wVFBnOkhhc1Zpc2libGVPdmVycHJpbnQ+CiAgICAgICAgIDx4bXBUUGc6SGFzVmlzaWJsZVRyYW5zcGFyZW5jeT5GYWxzZTwveG1wVFBnOkhhc1Zpc2libGVUcmFuc3BhcmVuY3k+CiAgICAgICAgIDx4bXBUUGc6TlBhZ2VzPjE8L3htcFRQZzpOUGFnZXM+CiAgICAgICAgIDx4bXBUUGc6TWF4UGFnZVNpemUgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPgogICAgICAgICAgICA8c3REaW06dz4zMTguMjA5MDgzPC9zdERpbTp3PgogICAgICAgICAgICA8c3REaW06aD4xNDMuOTI5ODA2PC9zdERpbTpoPgogICAgICAgICAgICA8c3REaW06dW5pdD5NaWxsaW1ldGVyczwvc3REaW06dW5pdD4KICAgICAgICAgPC94bXBUUGc6TWF4UGFnZVNpemU+CiAgICAgICAgIDx4bXBUUGc6UGxhdGVOYW1lcz4KICAgICAgICAgICAgPHJkZjpTZXE+CiAgICAgICAgICAgICAgIDxyZGY6bGk+Q3lhbjwvcmRmOmxpPgogICAgICAgICAgICAgICA8cmRmOmxpPk1hZ2VudGE8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaT5ZZWxsb3c8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaT5CbGFjazwvcmRmOmxpPgogICAgICAgICAgICA8L3JkZjpTZXE+CiAgICAgICAgIDwveG1wVFBnOlBsYXRlTmFtZXM+CiAgICAgICAgIDx4bXBUUGc6U3dhdGNoR3JvdXBzPgogICAgICAgICAgICA8cmRmOlNlcT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDx4bXBHOmdyb3VwTmFtZT5EZWZhdWx0IFN3YXRjaCBHcm91cDwveG1wRzpncm91cE5hbWU+CiAgICAgICAgICAgICAgICAgIDx4bXBHOmdyb3VwVHlwZT4wPC94bXBHOmdyb3VwVHlwZT4KICAgICAgICAgICAgICAgICAgPHhtcEc6Q29sb3JhbnRzPgogICAgICAgICAgICAgICAgICAgICA8cmRmOlNlcT4KICAgICAgICAgICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgICAgICAgICAgIDx4bXBHOnN3YXRjaE5hbWU+V2hpdGU8L3htcEc6c3dhdGNoTmFtZT4KICAgICAgICAgICAgICAgICAgICAgICAgICAgPHhtcEc6bW9kZT5SR0I8L3htcEc6bW9kZT4KICAgICAgICAgICAgICAgICAgICAgICAgICAgPHhtcEc6dHlwZT5QUk9DRVNTPC94bXBHOnR5cGU+CiAgICAgICAgICAgICAgICAgICAgICAgICAgIDx4bXBHOnJlZD4yNTU8L3htcEc6cmVkPgogICAgICAgICAgICAgICAgICAgICAgICAgICA8eG1wRzpncmVlbj4yNTU8L3htcEc6Z3JlZW4+CiAgICAgICAgICAgICAgICAgICAgICAgICAgIDx4bXBHOmJsdWU+MjU1PC94bXBHOmJsdWU+CiAgICAgICAgICAgICAgICAgICAgICAgIDwvcmRmOmxpPgogICAgICAgICAgICAgICAgICAgICAgICA8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgPHhtcEc6c3dhdGNoTmFtZT5CbGFjazwveG1wRzpzd2F0Y2hOYW1lPgogICAgICAgICAgICAgICAgICAgICAgICAgICA8eG1wRzptb2RlPlJHQjwveG1wRzptb2RlPgogICAgICAgICAgICAgICAgICAgICAgICAgICA8eG1wRzp0eXBlPlBST0NFU1M8L3htcEc6dHlwZT4KICAgICAgICAgICAgICAgICAgICAgICAgICAgPHhtcEc6cmVkPjM0PC94bXBHOnJlZD4KICAgICAgICAgICAgICAgICAgICAgICAgICAgPHhtcEc6Z3JlZW4+MzQ8L3htcEc6Z3JlZW4+CiAgICAgICAgICAgICAgICAgICAgICAgICAgIDx4bXBHOmJsdWU+MzM8L3htcEc6Ymx1ZT4KICAgICAgICAgICAgICAgICAgICAgICAgPC9yZGY6bGk+CiAgICAgICAgICAgICAgICAgICAgIDwvcmRmOlNlcT4KICAgICAgICAgICAgICAgICAgPC94bXBHOkNvbG9yYW50cz4KICAgICAgICAgICAgICAgPC9yZGY6bGk+CiAgICAgICAgICAgICAgIDxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPgogICAgICAgICAgICAgICAgICA8eG1wRzpncm91cE5hbWU+R3JheXM8L3htcEc6Z3JvdXBOYW1lPgogICAgICAgICAgICAgICAgICA8eG1wRzpncm91cFR5cGU+MTwveG1wRzpncm91cFR5cGU+CiAgICAgICAgICAgICAgICAgIDx4bXBHOkNvbG9yYW50cz4KICAgICAgICAgICAgICAgICAgICAgPHJkZjpTZXE+CiAgICAgICAgICAgICAgICAgICAgICAgIDxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPgogICAgICAgICAgICAgICAgICAgICAgICAgICA8eG1wRzpzd2F0Y2hOYW1lPkM9MCBNPTAgWT0wIEs9MTAwPC94bXBHOnN3YXRjaE5hbWU+CiAgICAgICAgICAgICAgICAgICAgICAgICAgIDx4bXBHOm1vZGU+UkdCPC94bXBHOm1vZGU+CiAgICAgICAgICAgICAgICAgICAgICAgICAgIDx4bXBHOnR5cGU+UFJPQ0VTUzwveG1wRzp0eXBlPgogICAgICAgICAgICAgICAgICAgICAgICAgICA8eG1wRzpyZWQ+MzQ8L3htcEc6cmVkPgogICAgICAgICAgICAgICAgICAgICAgICAgICA8eG1wRzpncmVlbj4zNDwveG1wRzpncmVlbj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgPHhtcEc6Ymx1ZT4zMzwveG1wRzpibHVlPgogICAgICAgICAgICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgICAgICAgICAgPC9yZGY6U2VxPgogICAgICAgICAgICAgICAgICA8L3htcEc6Q29sb3JhbnRzPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgPC9yZGY6U2VxPgogICAgICAgICA8L3htcFRQZzpTd2F0Y2hHcm91cHM+CiAgICAgICAgIDxwZGY6UHJvZHVjZXI+QWRvYmUgUERGIGxpYnJhcnkgMTAuMDE8L3BkZjpQcm9kdWNlcj4KICAgICAgICAgPHBob3Rvc2hvcDpMZWdhY3lJUFRDRGlnZXN0PjcxQjIxRUVGQjJBQzVERjk5NzhDNUZBNjdDNDY3RTQxPC9waG90b3Nob3A6TGVnYWN5SVBUQ0RpZ2VzdD4KICAgICAgICAgPHBob3Rvc2hvcDpDb2xvck1vZGU+MzwvcGhvdG9zaG9wOkNvbG9yTW9kZT4KICAgICAgICAgPHRpZmY6SW1hZ2VXaWR0aD4zNzU4PC90aWZmOkltYWdlV2lkdGg+CiAgICAgICAgIDx0aWZmOkltYWdlTGVuZ3RoPjE3MDA8L3RpZmY6SW1hZ2VMZW5ndGg+CiAgICAgICAgIDx0aWZmOkJpdHNQZXJTYW1wbGU+CiAgICAgICAgICAgIDxyZGY6U2VxPgogICAgICAgICAgICAgICA8cmRmOmxpPjg8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaT44PC9yZGY6bGk+CiAgICAgICAgICAgICAgIDxyZGY6bGk+ODwvcmRmOmxpPgogICAgICAgICAgICA8L3JkZjpTZXE+CiAgICAgICAgIDwvdGlmZjpCaXRzUGVyU2FtcGxlPgogICAgICAgICA8dGlmZjpQaG90b21ldHJpY0ludGVycHJldGF0aW9uPjI8L3RpZmY6UGhvdG9tZXRyaWNJbnRlcnByZXRhdGlvbj4KICAgICAgICAgPHRpZmY6T3JpZW50YXRpb24+MTwvdGlmZjpPcmllbnRhdGlvbj4KICAgICAgICAgPHRpZmY6U2FtcGxlc1BlclBpeGVsPjM8L3RpZmY6U2FtcGxlc1BlclBpeGVsPgogICAgICAgICA8dGlmZjpYUmVzb2x1dGlvbj4zMDAwMDAwLzEwMDAwPC90aWZmOlhSZXNvbHV0aW9uPgogICAgICAgICA8dGlmZjpZUmVzb2x1dGlvbj4zMDAwMDAwLzEwMDAwPC90aWZmOllSZXNvbHV0aW9uPgogICAgICAgICA8dGlmZjpSZXNvbHV0aW9uVW5pdD4yPC90aWZmOlJlc29sdXRpb25Vbml0PgogICAgICAgICA8ZXhpZjpFeGlmVmVyc2lvbj4wMjIxPC9leGlmOkV4aWZWZXJzaW9uPgogICAgICAgICA8ZXhpZjpDb2xvclNwYWNlPjY1NTM1PC9leGlmOkNvbG9yU3BhY2U+CiAgICAgICAgIDxleGlmOlBpeGVsWERpbWVuc2lvbj4zMTI8L2V4aWY6UGl4ZWxYRGltZW5zaW9uPgogICAgICAgICA8ZXhpZjpQaXhlbFlEaW1lbnNpb24+OTQ8L2V4aWY6UGl4ZWxZRGltZW5zaW9uPgogICAgICA8L3JkZjpEZXNjcmlwdGlvbj4KICAgPC9yZGY6UkRGPgo8L3g6eG1wbWV0YT4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg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IGZpbGw6IzBEMDA0RDsgDQp9DQo8L3N0eWxlPg0KPGRlZnMvPjxnIGlkPSJMYXllcl8xLTIiPjxnPjxwYXRoIGQ9Ik0xLjY2IDAuNTNDMS42NiAwLjUzIDEuNzggMS40MiAxLjc4IDIuOTQgMS43OCAyLjk0IDEuNzggMi45NCAxLjc4IDIuOTQgMS43OCAyLjY2IDEuNzUgMi4zOCAxLjY4IDIuMTIgMS41NiAxLjY1IDEuMzIgMS4yIDEgMC44IDAuNzMgMC40OSAwLjM5IDAuMjIgMCAwIDAuMzcgMC41OSAwLjYyIDEuNjggMC42MiAyLjkzIDAuNjIgNC4xOCAwLjM4IDUuMjggMCA1Ljg3IDAgNS44NyAwIDUuODggMCA1Ljg5IDAuMjEgNS43NyAwLjQxIDUuNjMgMC41OSA1LjQ4IDEuMjIgNC44OCAxLjYzIDQuMTYgMS43NSAzLjM4IDEuNzcgMy4yNCAxLjc4IDMuMSAxLjc4IDIuOTYgMS43OCA0LjQ4IDEuNjYgNS4zNyAxLjY2IDUuMzZMMS42NiA1LjM2QzIuMjUgNC43MyAyLjYyIDMuODggMi42MiAyLjk1TDIuNjIgMi45NUMyLjYyIDIuMDIgMi4yNiAxLjE3IDEuNjYgMC41NEwxLjY2IDAuNTRaIiBjbGFzcz0iTXNmdE9mY1Jlc3BvbnNpdmVfRmlsbF8wZDAwNGQiIHN0cm9rZT0ibm9uZSIgc3Ryb2tlLXdpZHRoPSIxIiBzdHJva2UtbGluZWNhcD0iYnV0dCIgc3Ryb2tlLWxpbmVqb2luPSJtaXRlciIgc3Ryb2tlLW1pdGVybGltaXQ9IjQiIGZpbGw9IiMwRDAwNEQiIGZpbGwtb3BhY2l0eT0iMSIvPjxwYXRoIGQ9Ik0yMy44MyAxLjhDMjIuNjUgMS44IDIxLjcgMi43MyAyMS43IDMuOSAyMS43IDUuMDcgMjIuNjQgNS45OSAyMy44MyA1Ljk5IDI1LjAyIDUuOTkgMjUuOTYgNS4wNiAyNS45NiAzLjkgMjUuOTYgMi43NCAyNS4wMiAxLjggMjMuODQgMS44Wk0yMy44MyA1LjA0QzIzLjE1IDUuMDQgMjIuNzMgNC41MSAyMi43MyAzLjg5IDIyLjczIDMuMjcgMjMuMTUgMi43NCAyMy44MyAyLjc0IDI0LjUxIDIuNzQgMjQuOTMgMy4yNyAyNC45MyAzLjg5IDI0LjkzIDQuNTEgMjQuNTEgNS4wNCAyMy44MyA1LjA0WiIgY2xhc3M9Ik1zZnRPZmNSZXNwb25zaXZlX0ZpbGxfMGQwMDRkIiBzdHJva2U9Im5vbmUiIHN0cm9rZS13aWR0aD0iMSIgc3Ryb2tlLWxpbmVjYXA9ImJ1dHQiIHN0cm9rZS1saW5lam9pbj0ibWl0ZXIiIHN0cm9rZS1taXRlcmxpbWl0PSI0IiBmaWxsPSIjMEQwMDREIiBmaWxsLW9wYWNpdHk9IjEiLz48cGF0aCBkPSJNOC45MyAxLjhDNy43NSAxLjggNi45MSAyLjc1IDYuOTEgMy45IDYuOTEgNS4wMyA3LjcyIDUuOTkgOC45IDUuOTkgOS43NSA1Ljk5IDEwLjUzIDUuNDggMTAuODIgNC42OEw5Ljc5IDQuNjhDOS42MyA0Ljg5IDkuMzIgNS4wNyA4LjkxIDUuMDcgOC4yNiA1LjA3IDcuOTcgNC42MSA3Ljk0IDQuMjZMMTAuOSA0LjI2QzEwLjkyIDQuMTUgMTAuOTMgMy45NiAxMC45MyAzLjgzIDEwLjkzIDIuNjEgMTAuMDMgMS44IDguOTUgMS44Wk03LjkzIDMuNDlDNy45MyAzLjA1IDguMzYgMi43IDguODkgMi43IDkuNDIgMi43IDkuODUgMy4wNSA5Ljg1IDMuNDlMNy45MiAzLjQ5WiIgY2xhc3M9Ik1zZnRPZmNSZXNwb25zaXZlX0ZpbGxfMGQwMDRkIiBzdHJva2U9Im5vbmUiIHN0cm9rZS13aWR0aD0iMSIgc3Ryb2tlLWxpbmVjYXA9ImJ1dHQiIHN0cm9rZS1saW5lam9pbj0ibWl0ZXIiIHN0cm9rZS1taXRlcmxpbWl0PSI0IiBmaWxsPSIjMEQwMDREIiBmaWxsLW9wYWNpdHk9IjEiLz48cGF0aCBkPSJNMjggMS44QzI3IDEuOCAyNi4yIDIuNjEgMjYuMiAzLjZMMjYuMiA1Ljg4IDI3LjIxIDUuODggMjcuMjEgMy42QzI3LjIxIDMuMDggMjcuNTggMi43NiAyOCAyLjc2IDI4LjQyIDIuNzYgMjguNzkgMy4wOSAyOC43OSAzLjYxTDI4Ljc5IDUuODlDMjguNzkgNS44OSAyOS44MSA1Ljg5IDI5LjgxIDUuODlMMjkuODEgMy42MUMyOS44MSAyLjYxIDI5IDEuOCAyOC4wMSAxLjhaIiBjbGFzcz0iTXNmdE9mY1Jlc3BvbnNpdmVfRmlsbF8wZDAwNGQiIHN0cm9rZT0ibm9uZSIgc3Ryb2tlLXdpZHRoPSIxIiBzdHJva2UtbGluZWNhcD0iYnV0dCIgc3Ryb2tlLWxpbmVqb2luPSJtaXRlciIgc3Ryb2tlLW1pdGVybGltaXQ9IjQiIGZpbGw9IiMwRDAwNEQiIGZpbGwtb3BhY2l0eT0iMSIvPjxwYXRoIGQ9Ik0xOC40NSAxLjkyQzE4LjM0IDEuODcgMTguMSAxLjc5IDE3LjgyIDEuNzkgMTcuMDIgMS43OSAxNi43MSAyLjQgMTYuNjggMi40N0wxNi42OCAxLjg5IDE1LjcyIDEuODkgMTUuNzIgNS44OEMxNS43MiA1Ljg4IDE2LjczIDUuODggMTYuNzMgNS44OEwxNi43MyAzLjI3QzE2Ljc2IDMuMjIgMTcuMDQgMi43MyAxNy42MyAyLjczIDE3Ljg1IDIuNzMgMTguMDUgMi44IDE4LjEgMi44MkwxOC40NSAxLjkxWiIgY2xhc3M9Ik1zZnRPZmNSZXNwb25zaXZlX0ZpbGxfMGQwMDRkIiBzdHJva2U9Im5vbmUiIHN0cm9rZS13aWR0aD0iMSIgc3Ryb2tlLWxpbmVjYXA9ImJ1dHQiIHN0cm9rZS1saW5lam9pbj0ibWl0ZXIiIHN0cm9rZS1taXRlcmxpbWl0PSI0IiBmaWxsPSIjMEQwMDREIiBmaWxsLW9wYWNpdHk9IjEiLz48cGF0aCBkPSJNMTMuMjQgMS44QzEyLjA2IDEuOCAxMS4xMSAyLjcyIDExLjExIDMuOTEgMTEuMTEgNS4wMyAxMS45NSA1Ljk5IDEzLjA4IDUuOTkgMTMuNjUgNS45OSAxNC4xIDUuNzQgMTQuMzMgNS4zOUwxNC4zMyA1Ljg5IDE1LjM1IDUuODlDMTUuMzUgNS44OSAxNS4zNSAzLjg0IDE1LjM1IDMuODQgMTUuMzUgMi42OSAxNC4zOSAxLjggMTMuMjMgMS44Wk0xMy4yNCA1LjA2QzEyLjU2IDUuMDYgMTIuMTQgNC41MiAxMi4xNCAzLjkgMTIuMTQgMy4yOCAxMi41NiAyLjc0IDEzLjI0IDIuNzQgMTMuOTIgMi43NCAxNC4zNCAzLjI4IDE0LjM0IDMuOSAxNC4zNCA0LjUyIDEzLjkyIDUuMDYgMTMuMjQgNS4wNloiIGNsYXNzPSJNc2Z0T2ZjUmVzcG9uc2l2ZV9GaWxsXzBkMDA0ZCIgc3Ryb2tlPSJub25lIiBzdHJva2Utd2lkdGg9IjEiIHN0cm9rZS1saW5lY2FwPSJidXR0IiBzdHJva2UtbGluZWpvaW49Im1pdGVyIiBzdHJva2UtbWl0ZXJsaW1pdD0iNCIgZmlsbD0iIzBEMDA0RCIgZmlsbC1vcGFjaXR5PSIxIi8+PHBhdGggZD0iTTIwLjI4IDMuNTFDMTkuNjEgMy4zNyAxOS4zNCAzLjMxIDE5LjM0IDMuMDggMTkuMzQgMi44MiAxOS42OCAyLjY5IDIwLjA1IDIuNjkgMjAuNiAyLjY5IDIxLjAxIDMgMjEuMDYgMy4wM0wyMS41NiAyLjI5QzIxLjMgMi4xMiAyMC44MSAxLjggMjAuMDQgMS44IDE4Ljk3IDEuOCAxOC4zNSAyLjQxIDE4LjM1IDMuMDkgMTguMzUgMy44IDE5LjA0IDQuMTQgMTkuODMgNC4zMSAyMC40MyA0LjQ0IDIwLjYyIDQuNDkgMjAuNjIgNC43IDIwLjYyIDQuOTkgMjAuMjggNS4xMSAxOS45MiA1LjExIDE5LjM4IDUuMTEgMTguOTMgNC44MyAxOC42NiA0LjU0TDE4LjEgNS4yN0MxOC4zMyA1LjQ5IDE4Ljk0IDYgMTkuOTEgNiAyMC44OCA2IDIxLjY0IDUuNDUgMjEuNjQgNC43NyAyMS42NCAzLjk1IDIxLjA4IDMuNjkgMjAuMjkgMy41MloiIGNsYXNzPSJNc2Z0T2ZjUmVzcG9uc2l2ZV9GaWxsXzBkMDA0ZCIgc3Ryb2tlPSJub25lIiBzdHJva2Utd2lkdGg9IjEiIHN0cm9rZS1saW5lY2FwPSJidXR0IiBzdHJva2UtbGluZWpvaW49Im1pdGVyIiBzdHJva2UtbWl0ZXJsaW1pdD0iNCIgZmlsbD0iIzBEMDA0RCIgZmlsbC1vcGFjaXR5PSIxIi8+PHBhdGggZD0iTTUuMjIgMCAzLjIyIDAgMy4yMiA1Ljg5QzMuMjIgNS44OSA0LjIyIDUuODkg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",
  decisions: "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cmcvZGMvZWxlbWVudHMvMS4xLyIKICAgICAgICAgICAgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIgogICAgICAgICAgICB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIKICAgICAgICAgICAgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiCiAgICAgICAgICAgIHhtbG5zOnN0RXZ0PSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VFdmVudCMiCiAgICAgICAgICAgIHhtbG5zOmlsbHVzdHJhdG9yPSJodHRwOi8vbnMuYWRvYmUuY29tL2lsbHVzdHJhdG9yLzEuMC8iCiAgICAgICAgICAgIHhtbG5zOnhtcFRQZz0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3QvcGcvIgogICAgICAgICAgICB4bWxuczpzdERpbT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL0RpbWVuc2lvbnMjIgogICAgICAgICAgICB4bWxuczp4bXBHPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvZy8iCiAgICAgICAgICAgIHhtbG5zOnBkZj0iaHR0cDovL25zLmFkb2JlLmNvbS9wZGYvMS4zLyIKICAgICAgICAgICAgeG1sbnM6cGhvdG9zaG9wPSJodHRwOi8vbnMuYWRvYmUuY29tL3Bob3Rvc2hvcC8xLjAvIgogICAgICAgICAgICB4bWxuczp0aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyIKICAgICAgICAgICAgeG1sbnM6ZXhpZj0iaHR0cDovL25zLmFkb2JlLmNvbS9leGlmLzEuMC8iPgogICAgICAgICA8ZGM6Zm9ybWF0PmltYWdlL3BuZzwvZGM6Zm9ybWF0PgogICAgICAgICA8ZGM6dGl0bGU+CiAgICAgICAgICAgIDxyZGY6QWx0PgogICAgICAgICAgICAgICA8cmRmOmxpIHhtbDpsYW5nPSJ4LWRlZmF1bHQiPlByaW50PC9yZGY6bGk+CiAgICAgICAgICAgIDwvcmRmOkFsdD4KICAgICAgICAgPC9kYzp0aXRsZT4KICAgICAgICAgPHhtcDpNZXRhZGF0YURhdGU+MjAxNi0wNS0yNlQxNDozOTo0MCswMTowMDwveG1wOk1ldGFkYXRhRGF0ZT4KICAgICAgICAgPHhtcDpNb2RpZnlEYXRlPjIwMTYtMDUtMjZUMTQ6Mzk6NDArMDE6MDA8L3htcDpNb2RpZnlEYXRlPgogICAgICAgICA8eG1wOkNyZWF0ZURhdGU+MjAxNS0wOS0yMlQwNzo1Nzo0NCswMTowMDwveG1wOkNyZWF0ZURhdGU+CiAgICAgICAgIDx4bXA6Q3JlYXRvclRvb2w+QWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKE1hY2ludG9zaCk8L3htcDpDcmVhdG9yVG9vbD4KICAgICAgICAgPHhtcE1NOkluc3RhbmNlSUQ+eG1wLmlpZDphNzBiZjY5Yi1kYTEzLTQ2M2YtYWU1ZC1hYWY1NWZmMzRhMGM8L3htcE1NOkluc3RhbmNlSUQ+CiAgICAgICAgIDx4bXBNTTpEb2N1bWVudElEPmFkb2JlOmRvY2lkOnBob3Rvc2hvcDphOWE2MzE5Zi02M2Q1LTExNzktOTYxNC1lZTJiM2QyNzI5MWI8L3htcE1NOkRvY3VtZW50SUQ+CiAgICAgICAgIDx4bXBNTTpPcmlnaW5hbERvY3VtZW50SUQ+dXVpZDo1RDIwODkyNDkzQkZEQjExOTE0QTg1OTBEMzE1MDhDODwveG1wTU06T3JpZ2luYWxEb2N1bWVudElEPgogICAgICAgICA8eG1wTU06UmVuZGl0aW9uQ2xhc3M+cHJvb2Y6cGRmPC94bXBNTTpSZW5kaXRpb25DbGFzcz4KICAgICAgICAgPHhtcE1NOkRlcml2ZWRGcm9tIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4KICAgICAgICAgICAgPHN0UmVmOmluc3RhbmNlSUQ+eG1wLmlpZDpmMmIyNzJjNi00Zjk3LTRlZWItYTBjNC03NjU1YmE5MDg2YjI8L3N0UmVmOmluc3RhbmNlSUQ+CiAgICAgICAgICAgIDxzdFJlZjpkb2N1bWVudElEPmFkb2JlOmRvY2lkOnBob3Rvc2hvcDowNWQ4OWUyNi00ODQ0LTExNzktYTUyZS05OTg5MDhhZDlmMTY8L3N0UmVmOmRvY3VtZW50SUQ+CiAgICAgICAgICAgIDxzdFJlZjpvcmlnaW5hbERvY3VtZW50SUQ+dXVpZDo1RDIwODkyNDkzQkZEQjExOTE0QTg1OTBEMzE1MDhDODwvc3RSZWY6b3JpZ2luYWxEb2N1bWVudElEPgogICAgICAgICAgICA8c3RSZWY6cmVuZGl0aW9uQ2xhc3M+cHJvb2Y6cGRmPC9zdFJlZjpyZW5kaXRpb25DbGFzcz4KICAgICAgICAgPC94bXBNTTpEZXJpdmVkRnJvbT4KICAgICAgICAgPHhtcE1NOkhpc3Rvcnk+CiAgICAgICAgICAgIDxyZGY6U2VxPgogICAgICAgICAgICAgICA8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmFjdGlvbj5zYXZlZDwvc3RFdnQ6YWN0aW9uPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6aW5zdGFuY2VJRD54bXAuaWlkOmFhN2UzZjExLTEwMzMtNGZmZC1iNTc5LWQxMTJhZmRlNzg0Yjwvc3RFdnQ6aW5zdGFuY2VJRD4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OndoZW4+MjAxNS0wOC0yNFQxNToyMzozMSswMTowMDwvc3RFdnQ6d2hlbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OnNvZnR3YXJlQWdlbnQ+QWRvYmUgSWxsdXN0cmF0b3IgQ0MgMjAxNSAoTWFjaW50b3NoKTwvc3RFdnQ6c29mdHdhcmVBZ2VudD4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmNoYW5nZWQ+Lzwvc3RFdnQ6Y2hhbmdlZD4KICAgICAgICAgICAgICAgPC9yZGY6bGk+CiAgICAgICAgICAgICAgIDxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6YWN0aW9uPmNvbnZlcnRlZDwvc3RFdnQ6YWN0aW9uPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6cGFyYW1ldGVycz5mcm9tIGFwcGxpY2F0aW9uL3Bvc3RzY3JpcHQgdG8gYXBwbGljYXRpb24vdm5kLmFkb2JlLmlsbHVzdHJhdG9yPC9zdEV2dDpwYXJhbWV0ZXJzPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+c2F2ZWQ8L3N0RXZ0OmFjdGlvbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0Omluc3RhbmNlSUQ+eG1wLmlpZDowZGJhNTdjNi01NDQ5LTRjYTMtOGVmNi1kNmFkNWI2ZGEwZWM8L3N0RXZ0Omluc3RhbmNlSUQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDp3aGVuPjIwMTUtMDktMjJUMDc6NTc6NDQrMDE6MDA8L3N0RXZ0OndoZW4+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpzb2Z0d2FyZUFnZW50PkFkb2JlIElsbHVzdHJhdG9yIENDIDIwMTUgKE1hY2ludG9zaCk8L3N0RXZ0OnNvZnR3YXJlQWdlbnQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpjaGFuZ2VkPi88L3N0RXZ0OmNoYW5nZWQ+CiAgICAgICAgICAgICAgIDwvcmRmOmxpPgogICAgICAgICAgICAgICA8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmFjdGlvbj5jb252ZXJ0ZWQ8L3N0RXZ0OmFjdGlvbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OnBhcmFtZXRlcnM+ZnJvbSBhcHBsaWNhdGlvbi9wb3N0c2NyaXB0IHRvIGFwcGxpY2F0aW9uL3ZuZC5hZG9iZS5pbGx1c3RyYXRvcjwvc3RFdnQ6cGFyYW1ldGVycz4KICAgICAgICAgICAgICAgPC9yZGY6bGk+CiAgICAgICAgICAgICAgIDxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6YWN0aW9uPmNvbnZlcnRlZDwvc3RFdnQ6YWN0aW9uPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6cGFyYW1ldGVycz5mcm9tIGFwcGxpY2F0aW9uL3Bvc3RzY3JpcHQgdG8gYXBwbGljYXRpb24vdm5kLmFkb2JlLmlsbHVzdHJhdG9yPC9zdEV2dDpwYXJhbWV0ZXJzPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+Y29udmVydGVkPC9zdEV2dDphY3Rpb24+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpwYXJhbWV0ZXJzPmZyb20gYXBwbGljYXRpb24vcG9zdHNjcmlwdCB0byBpbWFnZS9lcHNmPC9zdEV2dDpwYXJhbWV0ZXJzPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+c2F2ZWQ8L3N0RXZ0OmFjdGlvbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0Omluc3RhbmNlSUQ+eG1wLmlpZDpiMzk4OWM3Zi1hNGFjLTQ3ZWMtYmRmZS02MmZhZWM0ZTJhNDI8L3N0RXZ0Omluc3RhbmNlSUQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDp3aGVuPjIwMTYtMDQtMjFUMTI6Mzk6MDIrMDE6MDA8L3N0RXZ0OndoZW4+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpzb2Z0d2FyZUFnZW50PkFkb2JlIFBob3Rvc2hvcCBDQyAyMDE1IChNYWNpbnRvc2gpPC9zdEV2dDpzb2Z0d2FyZUFnZW50PgogICAgICAgICAgICAgICAgICA8c3RFdnQ6Y2hhbmdlZD4vPC9zdEV2dDpjaGFuZ2VkPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+Y29udmVydGVkPC9zdEV2dDphY3Rpb24+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpwYXJhbWV0ZXJzPmZyb20gYXBwbGljYXRpb24vcG9zdHNjcmlwdCB0byBpbWFnZS9qcGVnPC9zdEV2dDpwYXJhbWV0ZXJzPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+ZGVyaXZlZDwvc3RFdnQ6YWN0aW9uPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6cGFyYW1ldGVycz5jb252ZXJ0ZWQgZnJvbSBpbWFnZS9lcHNmIHRvIGltYWdlL2pwZWc8L3N0RXZ0OnBhcmFtZXRlcnM+CiAgICAgICAgICAgICAgIDwvcmRmOmxpPgogICAgICAgICAgICAgICA8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmFjdGlvbj5zYXZlZDwvc3RFdnQ6YWN0aW9uPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6aW5zdGFuY2VJRD54bXAuaWlkOjA0MTBkYjNmLWY1MDktNGExNi1iNTkyLTI0MWU5NDJlZTU5YTwvc3RFdnQ6aW5zdGFuY2VJRD4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OndoZW4+MjAxNi0wNC0yMVQxMjozOTowMiswMTowMDwvc3RFdnQ6d2hlbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OnNvZnR3YXJlQWdlbnQ+QWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKE1hY2ludG9zaCk8L3N0RXZ0OnNvZnR3YXJlQWdlbnQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpjaGFuZ2VkPi88L3N0RXZ0OmNoYW5nZWQ+CiAgICAgICAgICAgICAgIDwvcmRmOmxpPgogICAgICAgICAgICAgICA8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmFjdGlvbj5zYXZlZDwvc3RFdnQ6YWN0aW9uPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6aW5zdGFuY2VJRD54bXAuaWlkOmYyYjI3MmM2LTRmOTctNGVlYi1hMGM0LTc2NTViYTkwODZiMjwvc3RFdnQ6aW5zdGFuY2VJRD4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OndoZW4+MjAxNi0wNS0yNlQxNDozOTo0MCswMTowMDwvc3RFdnQ6d2hlbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OnNvZnR3YXJlQWdlbnQ+QWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKE1hY2ludG9zaCk8L3N0RXZ0OnNvZnR3YXJlQWdlbnQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpjaGFuZ2VkPi88L3N0RXZ0OmNoYW5nZWQ+CiAgICAgICAgICAgICAgIDwvcmRmOmxpPgogICAgICAgICAgICAgICA8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmFjdGlvbj5jb252ZXJ0ZWQ8L3N0RXZ0OmFjdGlvbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OnBhcmFtZXRlcnM+ZnJvbSBpbWFnZS9qcGVnIHRvIGltYWdlL3BuZzwvc3RFdnQ6cGFyYW1ldGVycz4KICAgICAgICAgICAgICAgPC9yZGY6bGk+CiAgICAgICAgICAgICAgIDxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6YWN0aW9uPmRlcml2ZWQ8L3N0RXZ0OmFjdGlvbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OnBhcmFtZXRlcnM+Y29udmVydGVkIGZyb20gaW1hZ2UvanBlZyB0byBpbWFnZS9wbmc8L3N0RXZ0OnBhcmFtZXRlcnM+CiAgICAgICAgICAgICAgIDwvcmRmOmxpPgogICAgICAgICAgICAgICA8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmFjdGlvbj5zYXZlZDwvc3RFdnQ6YWN0aW9uPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6aW5zdGFuY2VJRD54bXAuaWlkOmE3MGJmNjliLWRhMTMtNDYzZi1hZTVkLWFhZjU1ZmYzNGEwYzwvc3RFdnQ6aW5zdGFuY2VJRD4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OndoZW4+MjAxNi0wNS0yNlQxNDozOTo0MCswMTowMDwvc3RFdnQ6d2hlbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OnNvZnR3YXJlQWdlbnQ+QWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKE1hY2ludG9zaCk8L3N0RXZ0OnNvZnR3YXJlQWdlbnQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpjaGFuZ2VkPi88L3N0RXZ0OmNoYW5nZWQ+CiAgICAgICAgICAgICAgIDwvcmRmOmxpPgogICAgICAgICAgICA8L3JkZjpTZXE+CiAgICAgICAgIDwveG1wTU06SGlzdG9yeT4KICAgICAgICAgPGlsbHVzdHJhdG9yOlN0YXJ0dXBQcm9maWxlPlByaW50PC9pbGx1c3RyYXRvcjpTdGFydHVwUHJvZmlsZT4KICAgICAgICAgPHhtcFRQZzpIYXNWaXNpYmxlT3ZlcnByaW50PkZhbHNlPC94bXBUUGc6SGFzVmlzaWJsZU92ZXJwcmludD4KICAgICAgICAgPHhtcFRQZzpIYXNWaXNpYmxlVHJhbnNwYXJlbmN5PkZhbHNlPC94bXBUUGc6SGFzVmlzaWJsZVRyYW5zcGFyZW5jeT4KICAgICAgICAgPHhtcFRQZzpOUGFnZXM+MTwveG1wVFBnOk5QYWdlcz4KICAgICAgICAgPHhtcFRQZzpNYXhQYWdlU2l6ZSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgIDxzdERpbTp3PjMxOC4yMDkwODM8L3N0RGltOnc+CiAgICAgICAgICAgIDxzdERpbTpoPjE0My45Mjk4MDY8L3N0RGltOmg+CiAgICAgICAgICAgIDxzdERpbTp1bml0Pk1pbGxpbWV0ZXJzPC9zdERpbTp1bml0PgogICAgICAgICA8L3htcFRQZzpNYXhQYWdlU2l6ZT4KICAgICAgICAgPHhtcFRQZzpQbGF0ZU5hbWVzPgogICAgICAgICAgICA8cmRmOlNlcT4KICAgICAgICAgICAgICAgPHJkZjpsaT5DeWFuPC9yZGY6bGk+CiAgICAgICAgICAgICAgIDxyZGY6bGk+TWFnZW50YTwvcmRmOmxpPgogICAgICAgICAgICAgICA8cmRmOmxpPlllbGxvdzwvcmRmOmxpPgogICAgICAgICAgICAgICA8cmRmOmxpPkJsYWNrPC9yZGY6bGk+CiAgICAgICAgICAgIDwvcmRmOlNlcT4KICAgICAgICAgPC94bXBUUGc6UGxhdGVOYW1lcz4KICAgICAgICAgPHhtcFRQZzpTd2F0Y2hHcm91cHM+CiAgICAgICAgICAgIDxyZGY6U2VxPgogICAgICAgICAgICAgICA8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4KICAgICAgICAgICAgICAgICAgPHhtcEc6Z3JvdXBOYW1lPkRlZmF1bHQgU3dhdGNoIEdyb3VwPC94bXBHOmdyb3VwTmFtZT4KICAgICAgICAgICAgICAgICAgPHhtcEc6Z3JvdXBUeXBlPjA8L3htcEc6Z3JvdXBUeXBlPgogICAgICAgICAgICAgICAgICA8eG1wRzpDb2xvcmFudHM+CiAgICAgICAgICAgICAgICAgICAgIDxyZGY6U2VxPgogICAgICAgICAgICAgICAgICAgICAgICA8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgPHhtcEc6c3dhdGNoTmFtZT5XaGl0ZTwveG1wRzpzd2F0Y2hOYW1lPgogICAgICAgICAgICAgICAgICAgICAgICAgICA8eG1wRzptb2RlPlJHQjwveG1wRzptb2RlPgogICAgICAgICAgICAgICAgICAgICAgICAgICA8eG1wRzp0eXBlPlBST0NFU1M8L3htcEc6dHlwZT4KICAgICAgICAgICAgICAgICAgICAgICAgICAgPHhtcEc6cmVkPjI1NTwveG1wRzpyZWQ+CiAgICAgICAgICAgICAgICAgICAgICAgICAgIDx4bXBHOmdyZWVuPjI1NTwveG1wRzpncmVlbj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgPHhtcEc6Ymx1ZT4yNTU8L3htcEc6Ymx1ZT4KICAgICAgICAgICAgICAgICAgICAgICAgPC9yZGY6bGk+CiAgICAgICAgICAgICAgICAgICAgICAgIDxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPgogICAgICAgICAgICAgICAgICAgICAgICAgICA8eG1wRzpzd2F0Y2hOYW1lPkJsYWNrPC94bXBHOnN3YXRjaE5hbWU+CiAgICAgICAgICAgICAgICAgICAgICAgICAgIDx4bXBHOm1vZGU+UkdCPC94bXBHOm1vZGU+CiAgICAgICAgICAgICAgICAgICAgICAgICAgIDx4bXBHOnR5cGU+UFJPQ0VTUzwveG1wRzp0eXBlPgogICAgICAgICAgICAgICAgICAgICAgICAgICA8eG1wRzpyZWQ+MzQ8L3htcEc6cmVkPgogICAgICAgICAgICAgICAgICAgICAgICAgICA8eG1wRzpncmVlbj4zNDwveG1wRzpncmVlbj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgPHhtcEc6Ymx1ZT4zMzwveG1wRzpibHVlPgogICAgICAgICAgICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgICAgICAgICAgPC9yZGY6U2VxPgogICAgICAgICAgICAgICAgICA8L3htcEc6Q29sb3JhbnRzPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDx4bXBHOmdyb3VwTmFtZT5HcmF5czwveG1wRzpncm91cE5hbWU+CiAgICAgICAgICAgICAgICAgIDx4bXBHOmdyb3VwVHlwZT4xPC94bXBHOmdyb3VwVHlwZT4KICAgICAgICAgICAgICAgICAgPHhtcEc6Q29sb3JhbnRzPgogICAgICAgICAgICAgICAgICAgICA8cmRmOlNlcT4KICAgICAgICAgICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgICAgICAgICAgIDx4bXBHOnN3YXRjaE5hbWU+Qz0wIE09MCBZPTAgSz0xMDA8L3htcEc6c3dhdGNoTmFtZT4KICAgICAgICAgICAgICAgICAgICAgICAgICAgPHhtcEc6bW9kZT5SR0I8L3htcEc6bW9kZT4KICAgICAgICAgICAgICAgICAgICAgICAgICAgPHhtcEc6dHlwZT5QUk9DRVNTPC94bXBHOnR5cGU+CiAgICAgICAgICAgICAgICAgICAgICAgICAgIDx4bXBHOnJlZD4zNDwveG1wRzpyZWQ+CiAgICAgICAgICAgICAgICAgICAgICAgICAgIDx4bXBHOmdyZWVuPjM0PC94bXBHOmdyZWVuPgogICAgICAgICAgICAgICAgICAgICAgICAgICA8eG1wRzpibHVlPjMzPC94bXBHOmJsdWU+CiAgICAgICAgICAgICAgICAgICAgICAgIDwvcmRmOmxpPgogICAgICAgICAgICAgICAgICAgICA8L3JkZjpTZXE+CiAgICAgICAgICAgICAgICAgIDwveG1wRzpDb2xvcmFudHM+CiAgICAgICAgICAgICAgIDwvcmRmOmxpPgogICAgICAgICAgICA8L3JkZjpTZXE+CiAgICAgICAgIDwveG1wVFBnOlN3YXRjaEdyb3Vwcz4KICAgICAgICAgPHBkZjpQcm9kdWNlcj5BZG9iZSBQREYgbGlicmFyeSAxMC4wMTwvcGRmOlByb2R1Y2VyPgogICAgICAgICA8cGhvdG9zaG9wOkxlZ2FjeUlQVENEaWdlc3Q+NzFCMjFFRUZCMkFDNURGOTk3OEM1RkE2N0M0NjdFNDE8L3Bob3Rvc2hvcDpMZWdhY3lJUFRDRGlnZXN0PgogICAgICAgICA8cGhvdG9zaG9wOkNvbG9yTW9kZT4zPC9waG90b3Nob3A6Q29sb3JNb2RlPgogICAgICAgICA8dGlmZjpJbWFnZVdpZHRoPjM3NTg8L3RpZmY6SW1hZ2VXaWR0aD4KICAgICAgICAgPHRpZmY6SW1hZ2VMZW5ndGg+MTcwMDwvdGlmZjpJbWFnZUxlbmd0aD4KICAgICAgICAgPHRpZmY6Qml0c1BlclNhbXBsZT4KICAgICAgICAgICAgPHJkZjpTZXE+CiAgICAgICAgICAgICAgIDxyZGY6bGk+ODwvcmRmOmxpPgogICAgICAgICAgICAgICA8cmRmOmxpPjg8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaT44PC9yZGY6bGk+CiAgICAgICAgICAgIDwvcmRmOlNlcT4KICAgICAgICAgPC90aWZmOkJpdHNQZXJTYW1wbGU+CiAgICAgICAgIDx0aWZmOlBob3RvbWV0cmljSW50ZXJwcmV0YXRpb24+MjwvdGlmZjpQaG90b21ldHJpY0ludGVycHJldGF0aW9uPgogICAgICAgICA8dGlmZjpPcmllbnRhdGlvbj4xPC90aWZmOk9yaWVudGF0aW9uPgogICAgICAgICA8dGlmZjpTYW1wbGVzUGVyUGl4ZWw+MzwvdGlmZjpTYW1wbGVzUGVyUGl4ZWw+CiAgICAgICAgIDx0aWZmOlhSZXNvbHV0aW9uPjMwMDAwMDAvMTAwMDA8L3RpZmY6WFJlc29sdXRpb24+CiAgICAgICAgIDx0aWZmOllSZXNvbHV0aW9uPjMwMDAwMDAvMTAwMDA8L3RpZmY6WVJlc29sdXRpb24+CiAgICAgICAgIDx0aWZmOlJlc29sdXRpb25Vbml0PjI8L3RpZmY6UmVzb2x1dGlvblVuaXQ+CiAgICAgICAgIDxleGlmOkV4aWZWZXJzaW9uPjAyMjE8L2V4aWY6RXhpZlZlcnNpb24+CiAgICAgICAgIDxleGlmOkNvbG9yU3BhY2U+NjU1MzU8L2V4aWY6Q29sb3JTcGFjZT4KICAgICAgICAgPGV4aWY6UGl4ZWxYRGltZW5zaW9uPjMxMjwvZXhpZjpQaXhlbFhEaW1lbnNpb24+CiAgICAgICAgIDxleGlmOlBpeGVsWURpbWVuc2lvbj45NDwvZXhpZjpQaXhlbFlEaW1lbnNpb24+CiAgICAgIDwvcmRmOkRlc2NyaXB0aW9uPgogICA8L3JkZjpSREY+CjwveDp4bXBtZXRhPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgIAo8P3hwYWNrZXQgZW5kPSJ3Ij8+908/gwAAACBjSFJNAAB6JQAAgIMAAPn/AACA6QAAdTAAAOpgAAA6mAAAF2+SX8VGAAAzmElEQVR42uxdZ2BUVdOe27ZnW+pudlNJpxN6l957EaUJYkWKiP31s4uCICB2EVBQEJAuvUuHJAIJhJDeNtt7ueX7sTEskGw2kCDqfX4lu3fPvefOec7MmTMzB2EYBh4UnCSVWWG4YbBUWp0am7Pc4tA5XDY36aQoAkUEBCHmEhEifoSIFyrkqsXCFuHSyCABsGDxnwfe1Dcgaeb3G6UHblb8WWk8W6636kzg9ABFAc0AigKKAAKAIUAD0AwwDNAMAAMYBjgGYkFKhLxNhKxNhGx0ijpOFsQKjMV/E0gTaVST07M5u2hrTtGh/Ep7hR6cbuAQIOAClwAUAQSp5/cMAwwASYLdDU43MAzIxfGRwYPiFBObx3SNCmUlx4Il6n3hssb48ansTZk3nSVawFAQ8UDABRSF+7kRAuAmweoEhwt4nNgE1YxWcc93SJTwOKwIWbBEbRjOl+nnHbh44lIeWOwgF4GQB42urREEKBqMVrC7IEI+vWPyu71asOtYFixRA0K21jR9x5kzF3PBQ0GIGDg40E3so0IRsDlBbwWJcHSn5JUD0xUiPitOFixRawdJM8/+fu6bvRfA6YYwKRBYk1P0Dro63FBlAlnQiwPTF/dpzUqUBUvUO7HrRtm4nw47CishXAp8LtD039MJFAWbA6rMknjlhok9B8UrWLmyYIlajZk7z363/RTwORAsBoYG5u/tBwCCgNYMTveUoZ3WDO/EipbFf52o5TZn729/v5Z5ExQy4HL+NkVaq2p1e6BUl9Ay7sisQUohjxUwi/8oUU+U6h5Zsc1jsEBkcPVu58PVIQAEgTI9IhYceH74I1FhrIxZ/OeI+kt28cRV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PjxzdHlsZT4NCi5Nc2Z0T2ZjUmVzcG9uc2l2ZV9GaWxsXzBkMDA0ZCB7DQogZmlsbDojMEQwMDREOyANCn0NCjwvc3R5bGU+DQo8ZGVmcy8+PGcgaWQ9IkxheWVyXzEtMiI+PGc+PHBhdGggZD0iTTEuNjYgMC41M0MxLjY2IDAuNTMgMS43OCAxLjQyIDEuNzggMi45NCAxLjc4IDIuOTQgMS43OCAyLjk0IDEuNzggMi45NCAxLjc4IDIuNjYgMS43NSAyLjM4IDEuNjggMi4xMiAxLjU2IDEuNjUgMS4zMiAxLjIgMSAwLjggMC43MyAwLjQ5IDAuMzkgMC4yMiAwIDAgMC4zNyAwLjU5IDAuNjIgMS42OCAwLjYyIDIuOTMgMC42MiA0LjE4IDAuMzggNS4yOCAwIDUuODcgMCA1Ljg3IDAgNS44OCAwIDUuODkgMC4yMSA1Ljc3IDAuNDEgNS42MyAwLjU5IDUuNDggMS4yMiA0Ljg4IDEuNjMgNC4xNiAxLjc1IDMuMzggMS43NyAzLjI0IDEuNzggMy4xIDEuNzggMi45NiAxLjc4IDQuNDggMS42NiA1LjM3IDEuNjYgNS4zNkwxLjY2IDUuMzZDMi4yNSA0LjczIDIuNjIgMy44OCAyLjYyIDIuOTVMMi42MiAyLjk1QzIuNjIgMi4wMiAyLjI2IDEuMTcgMS42NiAwLjU0TDEuNjYgMC41NFoiIGNsYXNzPSJNc2Z0T2ZjUmVzcG9uc2l2ZV9GaWxsXzBkMDA0ZCIgc3Ryb2tlPSJub25lIiBzdHJva2Utd2lkdGg9IjEiIHN0cm9rZS1saW5lY2FwPSJidXR0IiBzdHJva2UtbGluZWpvaW49Im1pdGVyIiBzdHJva2UtbWl0ZXJsaW1pdD0iNCIgZmlsbD0iIzBEMDA0RCIgZmlsbC1vcGFjaXR5PSIxIi8+PHBhdGggZD0iTTIzLjgzIDEuOEMyMi42NSAxLjggMjEuNyAyLjczIDIxLjcgMy45IDIxLjcgNS4wNyAyMi42NCA1Ljk5IDIzLjgzIDUuOTkgMjUuMDIgNS45OSAyNS45NiA1LjA2IDI1Ljk2IDMuOSAyNS45NiAyLjc0IDI1LjAyIDEuOCAyMy44NCAxLjhaTTIzLjgzIDUuMDRDMjMuMTUgNS4wNCAyMi43MyA0LjUxIDIyLjczIDMuODkgMjIuNzMgMy4yNyAyMy4xNSAyLjc0IDIzLjgzIDIuNzQgMjQuNTEgMi43NCAyNC45MyAzLjI3IDI0LjkzIDMuODkgMjQuOTMgNC41MSAyNC41MSA1LjA0IDIzLjgzIDUuMDRaIiBjbGFzcz0iTXNmdE9mY1Jlc3BvbnNpdmVfRmlsbF8wZDAwNGQiIHN0cm9rZT0ibm9uZSIgc3Ryb2tlLXdpZHRoPSIxIiBzdHJva2UtbGluZWNhcD0iYnV0dCIgc3Ryb2tlLWxpbmVqb2luPSJtaXRlciIgc3Ryb2tlLW1pdGVybGltaXQ9IjQiIGZpbGw9IiMwRDAwNEQiIGZpbGwtb3BhY2l0eT0iMSIvPjxwYXRoIGQ9Ik04LjkzIDEuOEM3Ljc1IDEuOCA2LjkxIDIuNzUgNi45MSAzLjkgNi45MSA1LjAzIDcuNzIgNS45OSA4LjkgNS45OSA5Ljc1IDUuOTkgMTAuNTMgNS40OCAxMC44MiA0LjY4TDkuNzkgNC42OEM5LjYzIDQuODkgOS4zMiA1LjA3IDguOTEgNS4wNyA4LjI2IDUuMDcgNy45NyA0LjYxIDcuOTQgNC4yNkwxMC45IDQuMjZDMTAuOTIgNC4xNSAxMC45MyAzLjk2IDEwLjkzIDMuODMgMTAuOTMgMi42MSAxMC4wMyAxLjggOC45NSAxLjhaTTcuOTMgMy40OUM3LjkzIDMuMDUgOC4zNiAyLjcgOC44OSAyLjcgOS40MiAyLjcgOS44NSAzLjA1IDkuODUgMy40OUw3LjkyIDMuNDlaIiBjbGFzcz0iTXNmdE9mY1Jlc3BvbnNpdmVfRmlsbF8wZDAwNGQiIHN0cm9rZT0ibm9uZSIgc3Ryb2tlLXdpZHRoPSIxIiBzdHJva2UtbGluZWNhcD0iYnV0dCIgc3Ryb2tlLWxpbmVqb2luPSJtaXRlciIgc3Ryb2tlLW1pdGVybGltaXQ9IjQiIGZpbGw9IiMwRDAwNEQiIGZpbGwtb3BhY2l0eT0iMSIvPjxwYXRoIGQ9Ik0yOCAxLjhDMjcgMS44IDI2LjIgMi42MSAyNi4yIDMuNkwyNi4yIDUuODggMjcuMjEgNS44OCAyNy4yMSAzLjZDMjcuMjEgMy4wOCAyNy41OCAyLjc2IDI4IDIuNzYgMjguNDIgMi43NiAyOC43OSAzLjA5IDI4Ljc5IDMuNjFMMjguNzkgNS44OUMyOC43OSA1Ljg5IDI5LjgxIDUuODkgMjkuODEgNS44OUwyOS44MSAzLjYxQzI5LjgxIDIuNjEgMjkgMS44IDI4LjAxIDEuOFoiIGNsYXNzPSJNc2Z0T2ZjUmVzcG9uc2l2ZV9GaWxsXzBkMDA0ZCIgc3Ryb2tlPSJub25lIiBzdHJva2Utd2lkdGg9IjEiIHN0cm9rZS1saW5lY2FwPSJidXR0IiBzdHJva2UtbGluZWpvaW49Im1pdGVyIiBzdHJva2UtbWl0ZXJsaW1pdD0iNCIgZmlsbD0iIzBEMDA0RCIgZmlsbC1vcGFjaXR5PSIxIi8+PHBhdGggZD0iTTE4LjQ1IDEuOTJDMTguMzQgMS44NyAxOC4xIDEuNzkgMTcuODIgMS43OSAxNy4wMiAxLjc5IDE2LjcxIDIuNCAxNi42OCAyLjQ3TDE2LjY4IDEuODkgMTUuNzIgMS44OSAxNS43MiA1Ljg4QzE1LjcyIDUuODggMTYuNzMgNS44OCAxNi43MyA1Ljg4TDE2LjczIDMuMjdDMTYuNzYgMy4yMiAxNy4wNCAyLjczIDE3LjYzIDIuNzMgMTcuODUgMi43MyAxOC4wNSAyLjggMTguMSAyLjgyTDE4LjQ1IDEuOTFaIiBjbGFzcz0iTXNmdE9mY1Jlc3BvbnNpdmVfRmlsbF8wZDAwNGQiIHN0cm9rZT0ibm9uZSIgc3Ryb2tlLXdpZHRoPSIxIiBzdHJva2UtbGluZWNhcD0iYnV0dCIgc3Ryb2tlLWxpbmVqb2luPSJtaXRlciIgc3Ryb2tlLW1pdGVybGltaXQ9IjQiIGZpbGw9IiMwRDAwNEQiIGZpbGwtb3BhY2l0eT0iMSIvPjxwYXRoIGQ9Ik0xMy4yNCAxLjhDMTIuMDYgMS44IDExLjExIDIuNzIgMTEuMTEgMy45MSAxMS4xMSA1LjAzIDExLjk1IDUuOTkgMTMuMDggNS45OSAxMy42NSA1Ljk5IDE0LjEgNS43NCAxNC4zMyA1LjM5TDE0LjMzIDUuODkgMTUuMzUgNS44OUMxNS4zNSA1Ljg5IDE1LjM1IDMuODQgMTUuMzUgMy44NCAxNS4zNSAyLjY5IDE0LjM5IDEuOCAxMy4yMyAxLjhaTTEzLjI0IDUuMDZDMTIuNTYgNS4wNiAxMi4xNCA0LjUyIDEyLjE0IDMuOSAxMi4xNCAzLjI4IDEyLjU2IDIuNzQgMTMuMjQgMi43NCAxMy45MiAyLjc0IDE0LjM0IDMuMjggMTQuMzQgMy45IDE0LjM0IDQuNTIgMTMuOTIgNS4wNiAxMy4yNCA1LjA2WiIgY2xhc3M9Ik1zZnRPZmNSZXNwb25zaXZlX0ZpbGxfMGQwMDRkIiBzdHJva2U9Im5vbmUiIHN0cm9rZS13aWR0aD0iMSIgc3Ryb2tlLWxpbmVjYXA9ImJ1dHQiIHN0cm9rZS1saW5lam9pbj0ibWl0ZXIiIHN0cm9rZS1taXRlcmxpbWl0PSI0IiBmaWxsPSIjMEQwMDREIiBmaWxsLW9wYWNpdHk9IjEiLz48cGF0aCBkPSJNMjAuMjggMy41MUMxOS42MSAzLjM3IDE5LjM0IDMuMzEgMTkuMzQgMy4wOCAxOS4zNCAyLjgyIDE5LjY4IDIuNjkgMjAuMDUgMi42OSAyMC42IDIuNjkgMjEuMDEgMyAyMS4wNiAzLjAzTDIxLjU2IDIuMjlDMjEuMyAyLjEyIDIwLjgxIDEuOCAyMC4wNCAxLjggMTguOTcgMS44IDE4LjM1IDIuNDEgMTguMzUgMy4wOSAxOC4zNSAzLjggMTkuMDQgNC4xNCAxOS44MyA0LjMxIDIwLjQzIDQuNDQgMjAuNjIgNC40OSAyMC42MiA0LjcgMjAuNjIgNC45OSAyMC4yOCA1LjExIDE5LjkyIDUuMTEgMTkuMzggNS4xMSAxOC45MyA0LjgzIDE4LjY2IDQuNTRMMTguMSA1LjI3QzE4LjMzIDUuNDkgMTguOTQgNiAxOS45MSA2IDIwLjg4IDYgMjEuNjQgNS40NSAyMS42NCA0Ljc3IDIxLjY0IDMuOTUgMjEuMDggMy42OSAyMC4yOSAzLjUyWiIgY2xhc3M9Ik1zZnRPZmNSZXNwb25zaXZlX0ZpbGxfMGQwMDRkIiBzdHJva2U9Im5vbmUiIHN0cm9rZS13aWR0aD0iMSIgc3Ryb2tlLWxpbmVjYXA9ImJ1dHQiIHN0cm9rZS1saW5lam9pbj0ibWl0ZXIiIHN0cm9rZS1taXRlcmxpbWl0PSI0IiBmaWxsPSIjMEQwMDREIiBmaWxsLW9wYWNpdHk9IjEiLz48cGF0aCBkPSJN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"
};

/* ===== integrity.js ===== */
// ─────────────────────────────────────────────────────────────
// Submission integrity checks (runs in the browser, nothing uploaded)
//
// Reads the uploaded file's own metadata and structure:
//   Word (.docx): author, last modified by, created/modified dates,
//     revision number, total editing time, editing sessions (rsids),
//     tracked changes, comments, generating application, hyperlinks,
//     reference list and text left over from AI chat tools.
//   PDF: Info dictionary, XMP metadata and history, number of
//     incremental saves, producing application and hyperlinks.
//
// These are INDICATORS for an authenticity conversation, not proof.
// Metadata can be missing, stripped or edited, and no automated check
// can reliably prove AI use.
// ─────────────────────────────────────────────────────────────

(function () {
  const AI_TOOL_RE = /(chat\s?gpt|openai|\bgpt-?\d|claude|anthropic|gemini|\bbard\b|copilot|perplexity|jasper|quillbot|writesonic|copy\.ai|gamma\.app|\bgamma\b|notion ai|grammarly ?go|deepseek|mistral|llama|character\.ai|you\.com|poe\.com|tome\.app|beautiful\.ai|scribbr ai|humani[sz]e|undetectable)/i;
  const CODE_GEN_RE = /(python-docx|docx\.js|docx4j|pandoc|reportlab|wkhtmltopdf|puppeteer|headless ?chrome|playwright|aspose|openxml sdk|phpword|officegen|weasyprint|pdfkit|fpdf|jspdf|libreoffice.*convert|html2pdf)/i;
  const AI_LINK_RE = /(utm_source=chatgpt\.com|chatgpt\.com|chat\.openai\.com|claude\.ai|gemini\.google\.com|bard\.google\.com|copilot\.microsoft\.com|perplexity\.ai|poe\.com|you\.com\/search|deepseek\.com)/i;
  const TEXT_ARTEFACTS = [
    { re: /as an ai(?: language)? model/i, label: '"As an AI language model" phrase', level: 'high' },
    { re: /contentReference\[oaicite|oaicite:\d|:contentReference|turn\d+search\d+|【\d+†[^】]*】/i, label: 'ChatGPT citation markers left in the text (e.g. oaicite / 【†】)', level: 'high' },
    { re: /\b(certainly|sure|absolutely)!? here(?:'s| is| are)\b/i, label: 'Chatbot reply opener ("Certainly! Here\'s...")', level: 'high' },
    { re: /\b(i hope this helps|let me know if you(?:'d| would)? like|feel free to ask|would you like me to)\b/i, label: 'Chatbot sign-off ("I hope this helps", "Would you like me to...")', level: 'high' },
    { re: /regenerate response|copy code|as of my (?:last )?knowledge cut-?off/i, label: 'Chatbot interface or knowledge-cutoff text', level: 'high' },
    { re: /\[(?:insert|your|add|student|name|date|company)[^\]]{0,40}\]/i, label: 'Unfilled template placeholder like [Insert ...]', level: 'medium' },
    { re: /^\s{0,3}#{1,6}\s+\S/m, label: 'Markdown headings (#, ##) pasted as plain text', level: 'medium' },
    { re: /\*\*[^*\n]{2,80}\*\*/, label: 'Markdown bold (**text**) pasted as plain text', level: 'medium' },
  ];

  const fmtDate = d => {
    if (!d) return '';
    const dt = d instanceof Date ? d : new Date(d);
    if (isNaN(dt)) return String(d);
    return dt.toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
  };
  const uniq = a => [...new Set(a.filter(Boolean))];
  const xmlText = (xml, tag) => {
    const m = xml.match(new RegExp(`<${tag}[^>]*>([\\s\\S]*?)</${tag}>`));
    return m ? decodeEntities(m[1].trim()) : '';
  };
  const decodeEntities = t => t.replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"').replace(/&apos;/g, "'").replace(/&amp;/g, '&');

  function findReferences(text) {
    const lines = text.split('\n').map(l => l.trim());
    const idx = lines.findIndex(l => /^(references|reference list|bibliography|works cited|sources|sources used|citations)\s*:?$/i.test(l));
    if (idx === -1) return [];
    return lines.slice(idx + 1).filter(l => l.length > 8).slice(0, 40);
  }

  // ───────────────────────── Word ─────────────────────────
  // Word files don't always keep their parts at the usual paths (Google Docs, Pages,
  // converters and Strict OOXML use other names), so follow the package relationships.
  async function openDocx(data, opts) {
    let zip;
    try { zip = await JSZip.loadAsync(data, opts); }
    catch (e) { throw new Error("This isn't a valid .docx file. It may be an old .doc or a renamed file; ask for it to be saved again as .docx or PDF."); }
    const names = Object.keys(zip.files);
    const find = path => {
      if (!path) return null;
      const clean = path.replace(/^\//, '');
      return zip.file(clean) || zip.file(names.find(n => n.toLowerCase() === clean.toLowerCase()) || '\u0000');
    };
    const read = async path => { const f = find(path); return f ? f.async('string') : ''; };
    const rels = xml => [...xml.matchAll(/<Relationship\b([^>]*)\/?>/g)].map(m => {
      const a = k => (m[1].match(new RegExp(`\\b${k}="([^"]*)"`)) || [])[1] || '';
      return { type: a('Type'), target: a('Target'), mode: a('TargetMode') };
    });
    const resolve = (base, target) => {
      if (target.startsWith('/')) return target.slice(1);
      const parts = (base ? base.split('/') : []).concat(target.split('/'));
      const out = [];
      parts.forEach(p => { if (p === '..') out.pop(); else if (p && p !== '.') out.push(p); });
      return out.join('/');
    };
    const rootRels = rels(await read('_rels/.rels'));
    const byType = (list, suffix) => list.find(r => r.type.toLowerCase().endsWith(suffix));
    let mainPath = (byType(rootRels, '/officedocument') || {}).target;
    mainPath = mainPath ? resolve('', mainPath) : null;
    if (!find(mainPath)) mainPath = names.find(n => /^word\/document[^/]*\.xml$/i.test(n)) || names.find(n => /document[^/]*\.xml$/i.test(n) && !/_rels/.test(n));
    if (!mainPath || !find(mainPath)) throw new Error("Couldn't find the document text inside this Word file. Try saving it again as .docx or PDF.");
    const dir = mainPath.includes('/') ? mainPath.slice(0, mainPath.lastIndexOf('/')) : '';
    const file = mainPath.slice(dir.length ? dir.length + 1 : 0);
    const docRelsXml = await read(`${dir ? dir + '/' : ''}_rels/${file}.rels`);
    const docRels = rels(docRelsXml);
    const part = (list, base, suffix) => { const r = byType(list, suffix); return r ? resolve(base, r.target) : null; };
    return {
      zip, mainPath, docRels,
      read,
      main: () => read(mainPath),
      core: () => read(part(rootRels, '', '/core-properties') || 'docProps/core.xml'),
      app: () => read(part(rootRels, '', '/extended-properties') || 'docProps/app.xml'),
      custom: () => read(part(rootRels, '', '/custom-properties') || 'docProps/custom.xml'),
      settings: () => read(part(docRels, dir, '/settings') || `${dir}/settings.xml`),
      comments: () => read(part(docRels, dir, '/comments') || `${dir}/comments.xml`),
      relsXml: docRelsXml,
    };
  }
  window.openDocx = openDocx;

  async function analyseDocx(buf) {
    const pkg = await openDocx(buf);
    const core = await pkg.core();
    const app = await pkg.app();
    const custom = await pkg.custom();
    const settings = await pkg.settings();
    const doc = await pkg.main();
    const comments = await pkg.comments();
    const rels = pkg.relsXml;

    const meta = {
      title: xmlText(core, 'dc:title'),
      author: xmlText(core, 'dc:creator'),
      lastModifiedBy: xmlText(core, 'cp:lastModifiedBy'),
      created: xmlText(core, 'dcterms:created'),
      modified: xmlText(core, 'dcterms:modified'),
      revision: xmlText(core, 'cp:revision'),
      description: xmlText(core, 'dc:description'),
      application: [xmlText(app, 'Application'), xmlText(app, 'AppVersion')].filter(Boolean).join(' '),
      totalEditMinutes: xmlText(app, 'TotalTime'),
      words: xmlText(app, 'Words'),
      pages: xmlText(app, 'Pages'),
      template: xmlText(app, 'Template'),
      company: xmlText(app, 'Company'),
    };

    const rsidBlock = (settings.match(/<w:rsids>([\s\S]*?)<\/w:rsids>/) || [])[1] || '';
    const sessions = (rsidBlock.match(/<w:rsid\b/g) || []).length + (/<w:rsidRoot\b/.test(rsidBlock) ? 1 : 0);
    const ins = [...doc.matchAll(/<w:ins\b[^>]*w:author="([^"]*)"[^>]*?(?:w:date="([^"]*)")?/g)];
    const del = [...doc.matchAll(/<w:del\b[^>]*w:author="([^"]*)"[^>]*?(?:w:date="([^"]*)")?/g)];
    const commentAuthors = [...comments.matchAll(/<w:comment\b[^>]*w:author="([^"]*)"/g)].map(m => decodeEntities(m[1]));
    const customProps = [...custom.matchAll(/<property\b[^>]*name="([^"]*)"[^>]*>([\s\S]*?)<\/property>/g)]
      .map(m => `${decodeEntities(m[1])}: ${decodeEntities(m[2].replace(/<[^>]+>/g, '').trim())}`);

    const links = uniq([...rels.matchAll(/Target="([^"]+)"[^>]*TargetMode="External"/g)].map(m => decodeEntities(m[1])))
      .concat(uniq([...rels.matchAll(/TargetMode="External"[^>]*Target="([^"]+)"/g)].map(m => decodeEntities(m[1]))));

    const text = doc
      .replace(/<w:tab\/>/g, '\t').replace(/<w:br[^>]*\/>/g, '\n').replace(/<\/w:p>/g, '\n')
      .replace(/<[^>]+>/g, '');
    const plain = decodeEntities(text).replace(/\n{3,}/g, '\n\n').trim();
    const wordCount = (plain.match(/\b[\w'’-]+\b/g) || []).length;

    return {
      kind: 'Word document',
      meta, customProps,
      history: {
        sessions,
        trackedInsertions: ins.length,
        trackedDeletions: del.length,
        trackedAuthors: uniq(ins.concat(del).map(m => decodeEntities(m[1]))),
        trackedDates: uniq(ins.concat(del).map(m => m[2])).sort(),
        comments: commentAuthors.length,
        commentAuthors: uniq(commentAuthors),
      },
      links: uniq(links),
      references: findReferences(plain),
      text: plain,
      wordCount,
    };
  }

  // ───────────────────────── PDF ─────────────────────────
  function pdfString(raw) {
    if (!raw) return '';
    raw = raw.trim();
    if (raw.startsWith('<') && !raw.startsWith('<<')) {
      const hex = raw.slice(1, -1).replace(/\s+/g, '');
      const bytes = hex.match(/../g) || [];
      if (/^feff/i.test(hex)) {
        let s = '';
        for (let i = 2; i + 1 < bytes.length; i += 2) s += String.fromCharCode(parseInt(bytes[i] + bytes[i + 1], 16));
        return s;
      }
      return bytes.map(b => String.fromCharCode(parseInt(b, 16))).join('');
    }
    let s = raw.slice(1, -1)
      .replace(/\\([nrtbf()\\])/g, (m, c) => ({ n: '\n', r: '\r', t: '\t', b: '\b', f: '\f' }[c] || c))
      .replace(/\\([0-7]{1,3})/g, (m, o) => String.fromCharCode(parseInt(o, 8)));
    if (s.charCodeAt(0) === 0xfe && s.charCodeAt(1) === 0xff) {
      let out = '';
      for (let i = 2; i + 1 < s.length; i += 2) out += String.fromCharCode((s.charCodeAt(i) << 8) | s.charCodeAt(i + 1));
      s = out;
    }
    return s;
  }
  function pdfDate(s) {
    const m = (s || '').match(/D?:?(\d{4})(\d{2})?(\d{2})?(\d{2})?(\d{2})?(\d{2})?([Z+\-])?(\d{2})?'?(\d{2})?/);
    if (!m) return s;
    const [, y, mo = '01', d = '01', h = '00', mi = '00', se = '00', tz, th = '00', tm = '00'] = m;
    const zone = !tz || tz === 'Z' ? 'Z' : `${tz}${th}:${tm}`;
    return `${y}-${mo}-${d}T${h}:${mi}:${se}${zone}`;
  }
  const literal = '\\((?:\\\\.|[^\\\\)])*\\)|<[0-9A-Fa-f\\s]*>';
  function infoField(src, key) {
    const m = src.match(new RegExp(`/${key}\\s*(${literal})`));
    return m ? pdfString(m[1]) : '';
  }

  // Inflate a FlateDecode stream, keeping whatever decodes before any trailing junk
  async function inflate(bytes) {
    const reader = new Blob([bytes]).stream().pipeThrough(new DecompressionStream('deflate')).getReader();
    const chunks = [];
    try {
      for (;;) { const { done, value } = await reader.read(); if (done) break; chunks.push(value); }
    } catch (e) { if (!chunks.length) throw e; }
    return chunks.map(c => new TextDecoder('latin1').decode(c)).join('');
  }

  async function analysePdf(buf) {
    const bytes = new Uint8Array(buf);
    const raw = new TextDecoder('latin1').decode(bytes);
    const objects = {};   // objnum -> dictionary/text (latest definition in the file wins)
    const streams = {};   // objnum -> decoded stream text
    const offsets = {};   // objnum -> byte offset of its definition
    const objRe = /(\d+)\s+\d+\s+obj\b([\s\S]*?)endobj/g;
    let m, inflated = 0;
    while ((m = objRe.exec(raw))) {
      const num = m[1], body = m[2];
      const sIdx = body.search(/stream\r?\n/);
      const dict = sIdx === -1 ? body : body.slice(0, sIdx);
      objects[num] = dict;
      offsets[num] = m.index;
      if (sIdx === -1) continue;
      const dataStart = m.index + m[0].indexOf(body) + sIdx + body.slice(sIdx).match(/stream\r?\n/)[0].length;
      let dataEnd = raw.indexOf('endstream', dataStart);
      const len = dict.match(/\/Length\s+(\d+)(?!\s+\d+\s+R)/);
      if (len && dataEnd !== -1) dataEnd = Math.min(dataEnd, dataStart + parseInt(len[1], 10));
      if (dataEnd === -1) continue;
      // only object streams (hold Info, catalog, link annotations) and XMP metadata are needed
      if (!/\/Type\s*\/(ObjStm|Metadata)/.test(dict)) continue;
      if (!/FlateDecode/.test(dict)) { if (!/\/Filter/.test(dict)) streams[num] = raw.slice(dataStart, dataEnd); continue; }
      if (typeof DecompressionStream === 'undefined' || inflated > 2000 || dataEnd - dataStart > 2e7) continue;
      inflated++;
      try { streams[num] = await inflate(bytes.subarray(dataStart, dataEnd)); } catch (e) { /* skip */ }
    }
    // Objects packed inside object streams
    Object.keys(streams).sort((a, b) => offsets[a] - offsets[b]).forEach(num => {
      const dict = objects[num] || '';
      if (!/\/Type\s*\/ObjStm/.test(dict)) return;
      const n = parseInt((dict.match(/\/N\s+(\d+)/) || [])[1], 10), first = parseInt((dict.match(/\/First\s+(\d+)/) || [])[1], 10);
      if (!n || isNaN(first)) return;
      const data = streams[num];
      const head = data.slice(0, first).trim().split(/\s+/).map(Number);
      for (let i = 0; i < n; i++) {
        const on = head[2 * i], off = head[2 * i + 1], next = i + 1 < n ? head[2 * i + 3] : data.length - first;
        // a later definition (incremental update) replaces an earlier one
        if (on !== undefined && (!(on in offsets) || offsets[on] < offsets[num])) {
          objects[on] = data.slice(first + off, first + next);
          offsets[on] = offsets[num];
        }
      }
    });
    const lastRef = key => { const all = [...raw.matchAll(new RegExp(`/${key}\\s+(\\d+)\\s+\\d+\\s+R`, 'g'))]; return all.length ? all[all.length - 1][1] : null; };
    const infoNum = lastRef('Info'), rootNum = lastRef('Root');
    const info = (infoNum && objects[infoNum]) || '';
    const everything = raw + '\n' + Object.values(streams).join('\n');
    const field = key => {
      if (info) return infoField(info, key);
      const all = [...everything.matchAll(new RegExp(`/${key}\\s*(${literal})`, 'g'))];
      return all.length ? pdfString(all[all.length - 1][1]) : '';
    };

    let xmp = '';
    const metaNum = rootNum && objects[rootNum] ? (objects[rootNum].match(/\/Metadata\s+(\d+)\s+\d+\s+R/) || [])[1] : null;
    if (metaNum && streams[metaNum]) xmp = (streams[metaNum].match(/<x:xmpmeta[\s\S]*?<\/x:xmpmeta>/) || [''])[0];
    if (!xmp) xmp = ([...everything.matchAll(/<x:xmpmeta[\s\S]*?<\/x:xmpmeta>/g)].map(x => x[0]).find(x => /pdf:Producer/.test(x)) || '');
    const xmpVal = tag => {
      const a = xmp.match(new RegExp(`<${tag}>([\\s\\S]*?)</${tag}>`));
      if (a) return decodeEntities(a[1].replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim());
      const b = xmp.match(new RegExp(`${tag}="([^"]*)"`));
      return b ? decodeEntities(b[1]) : '';
    };
    const xmpHistory = [...xmp.matchAll(/<rdf:li\b([^>]*)>/g)].map(x => x[1]).filter(a => /stEvt:action/.test(a))
      .map(a => {
        const g = k => (a.match(new RegExp(`stEvt:${k}="([^"]*)"`)) || [])[1] || '';
        return [g('action'), g('when') ? fmtDate(g('when')) : '', g('softwareAgent')].filter(Boolean).join(' · ');
      });

    const meta = {
      title: field('Title') || xmpVal('dc:title'),
      author: field('Author') || xmpVal('dc:creator'),
      created: pdfDate(field('CreationDate')) || xmpVal('xmp:CreateDate'),
      modified: pdfDate(field('ModDate')) || xmpVal('xmp:ModifyDate'),
      application: field('Creator') || xmpVal('xmp:CreatorTool'),
      producer: field('Producer') || xmpVal('pdf:Producer'),
      pages: (rootNum && (() => { const pagesRef = (objects[rootNum] || '').match(/\/Pages\s+(\d+)\s+\d+\s+R/); const c = pagesRef && (objects[pagesRef[1]] || '').match(/\/Count\s+(\d+)/); return c ? c[1] : ''; })()) || '',
    };
    const links = uniq([...everything.matchAll(new RegExp(`/URI\\s*(${literal})`, 'g'))].map(x => pdfString(x[1])))
      .filter(l => l && !/^about:blank$/i.test(l));
    const eofs = (raw.match(/%%EOF/g) || []).length;
    const linearised = /\/Linearized/.test(raw.slice(0, 2048));

    return {
      kind: 'PDF',
      meta,
      customProps: [],
      history: { incrementalSaves: Math.max(0, eofs - 1 - (linearised ? 1 : 0)), xmpHistory },
      links,
      references: [],
      text: '',
      wordCount: 0,
    };
  }

  // ───────────────────────── Flags ─────────────────────────
  function buildFlags(r, submittedOn) {
    const flags = [];
    const add = (level, text) => flags.push({ level, text });
    const m = r.meta;
    const toolFields = [m.author, m.lastModifiedBy, m.application, m.producer, m.description, m.template, m.company, ...(r.customProps || [])].filter(Boolean).join(' | ');

    const aiTool = toolFields.match(AI_TOOL_RE);
    if (aiTool) add('high', `An AI tool is named in the file's own properties ("${aiTool[0]}").`);
    const codeGen = toolFields.match(CODE_GEN_RE);
    if (codeGen) add('high', `File was generated by code ("${codeGen[0]}"), not typed in Word or Docs. Chatbots that offer a "download as Word/PDF" button produce files like this.`);
    if (/generated by python-docx/i.test(m.description)) add('high', 'Description says "generated by python-docx" (a typical ChatGPT file export).');

    const aiLinks = r.links.filter(l => AI_LINK_RE.test(l));
    if (aiLinks.length) add('high', `${aiLinks.length} link(s) point to or were copied from an AI chatbot (e.g. utm_source=chatgpt.com): ${aiLinks.slice(0, 3).join(', ')}`);

    TEXT_ARTEFACTS.forEach(a => { if (r.text && a.re.test(r.text)) add(a.level, a.label + ' found in the text.'); });

    const words = parseInt(m.words, 10) || r.wordCount || 0;
    const mins = parseInt(m.totalEditMinutes, 10);
    if (!isNaN(mins) && words > 300) {
      if (mins <= 2) add('medium', `Only ${mins} minute(s) of recorded editing time for about ${words} words. Consistent with text being pasted in from elsewhere.`);
      else if (words / mins > 40) add('medium', `About ${words} words in ${mins} minutes of recorded editing time (${Math.round(words / mins)} words/min). Fast for original typing.`);
    }
    if (r.history.sessions !== undefined && r.history.sessions > 0 && r.history.sessions <= 2 && words > 500)
      add('medium', `Only ${r.history.sessions} editing session(s) recorded for about ${words} words. Built-up work usually shows many sessions.`);
    if (m.created && m.modified && words > 500) {
      const span = (new Date(m.modified) - new Date(m.created)) / 60000;
      if (!isNaN(span) && span >= 0 && span < 15) add('medium', `Created and last saved within ${Math.round(span)} minute(s) of each other.`);
    }
    if (m.author && m.lastModifiedBy && m.author.toLowerCase() !== m.lastModifiedBy.toLowerCase())
      add('info', `Created by "${m.author}" but last saved by "${m.lastModifiedBy}". Check the work is the student's own (could be a shared or college template).`);
    if (r.history.trackedAuthors && r.history.trackedAuthors.length > 1)
      add('info', `Tracked changes by more than one person: ${r.history.trackedAuthors.join(', ')}.`);
    if (r.history.incrementalSaves > 0)
      add('info', `PDF was edited ${r.history.incrementalSaves} time(s) after it was first created.`);
    if (!m.author && !m.created && !m.application)
      add('info', 'Little or no metadata in the file. It may have been stripped, exported or re-saved through another tool.');
    if (submittedOn && m.modified) {
      const sub = parseUkDate(submittedOn), mod = new Date(m.modified);
      if (sub && !isNaN(mod) && mod - sub > 24 * 3600e3) add('info', `File was last saved ${fmtDate(mod)}, after the recorded submission date (${submittedOn}).`);
    }
    if (r.text) {
      const dashes = (r.text.match(/—/g) || []).length;
      if (words > 300 && dashes / words * 1000 > 6) add('info', `Heavy use of em dashes (${dashes}). A weak signal on its own; common in AI-written text.`);
    }
    if (!r.links.length && !r.references.length && r.kind === 'Word document' && words > 600)
      add('info', 'No hyperlinks or reference list found. Check whether research has been referenced.');
    return flags;
  }

  function parseUkDate(s) {
    const m = (s || '').match(/(\d{1,2})[\/.\-](\d{1,2})[\/.\-](\d{2,4})/);
    if (!m) return null;
    let y = parseInt(m[3], 10); if (y < 100) y += 2000;
    return new Date(y, parseInt(m[2], 10) - 1, parseInt(m[1], 10), 23, 59);
  }

  window.analyseSubmission = async function (file, buf, submittedOn) {
    const isPdf = file.type === 'application/pdf' || /\.pdf$/i.test(file.name);
    const r = isPdf ? await analysePdf(buf) : await analyseDocx(buf);
    r.fileName = file.name;
    r.size = file.size;
    r.lastModifiedOnDisk = file.lastModified ? new Date(file.lastModified) : null;
    r.flags = buildFlags(r, submittedOn);
    return r;
  };

  // Short text summary for the AI prompt
  window.integritySummary = function (r) {
    if (!r) return '';
    const m = r.meta;
    const lines = [
      `File type: ${r.kind}`,
      m.author && `Author: ${m.author}`, m.lastModifiedBy && `Last modified by: ${m.lastModifiedBy}`,
      m.created && `Created: ${m.created}`, m.modified && `Modified: ${m.modified}`,
      m.application && `Application: ${m.application}`, m.producer && `Producer: ${m.producer}`,
      m.totalEditMinutes && `Total editing time: ${m.totalEditMinutes} min`, m.revision && `Revision: ${m.revision}`,
      r.history.sessions !== undefined && `Editing sessions: ${r.history.sessions}`,
      r.links.length && `Hyperlinks: ${r.links.slice(0, 25).join(' , ')}`,
      r.flags.length && `Automated flags: ${r.flags.map(f => f.text).join(' / ')}`,
      r.cloud && window.BTECCloud && window.BTECCloud.historySummary(r.cloud),
    ];
    return lines.filter(Boolean).join('\n');
  };

  // ───────────────────────── Rendering ─────────────────────────
  const esc = t => String(t ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  const row = (k, v) => v ? `<tr><td class="ik">${esc(k)}</td><td>${esc(v)}</td></tr>` : '';

  window.renderIntegrity = function (r, ai) {
    const el = SMDOC.getElementById('integrityCard');
    if (!el) return;
    if (!r) { el.style.display = 'none'; el.innerHTML = ''; return; }
    const m = r.meta, h = r.history;
    const high = r.flags.filter(f => f.level === 'high').length, med = r.flags.filter(f => f.level === 'medium').length;
    const overall = high ? ['high', 'Strong indicators found'] : med ? ['medium', 'Some indicators found'] : ['low', 'No strong indicators in the file'];
    const flagHtml = r.flags.length
      ? r.flags.map(f => `<li class="flag flag-${f.level}"><span class="flag-tag">${{ high: 'Strong', medium: 'Moderate', info: 'Note' }[f.level] || 'Note'}</span>${esc(f.text)}</li>`).join('')
      : '<li class="flag flag-info"><span class="flag-tag">Clear</span>Nothing unusual found in the file itself.</li>';

    let aiHtml = '';
    if (ai) {
      const lvl = (ai.ai_likelihood || '').toLowerCase();
      aiHtml = `
        <div class="isec"><div class="ititle">AI review of the writing</div>
          <p style="margin:4px 0"><span class="flag-tag tag-${esc(lvl)}">AI-generated likelihood: ${esc(lvl || 'n/a')}</span></p>
          ${(ai.ai_indicators || []).length ? `<ul class="ilist">${ai.ai_indicators.map(x => `<li>${esc(x)}</li>`).join('')}</ul>` : ''}
          ${(ai.sources_cited || []).length ? `<div class="ititle" style="margin-top:8px">Sources cited in the work</div><ul class="ilist">${ai.sources_cited.map(x => `<li>${esc(x)}</li>`).join('')}</ul>` : '<p class="helper">The AI found no cited sources in the work.</p>'}
          ${(ai.sources_concerns || []).length ? `<div class="ititle" style="margin-top:8px">Referencing concerns</div><ul class="ilist">${ai.sources_concerns.map(x => `<li>${esc(x)}</li>`).join('')}</ul>` : ''}
        </div>`;
    }

    el.style.display = 'block';
    el.innerHTML = `
      <div class="label">Submission integrity</div>
      <div class="ihead"><span class="flag-tag tag-${overall[0]}">${overall[1]}</span><span class="helper" style="margin:0">${high} strong, ${med} moderate, ${r.flags.length - high - med} notes</span></div>
      <ul class="flags">${flagHtml}</ul>
      <div class="igrid">
        <div class="isec"><div class="ititle">File details</div><table class="itable">
          ${row('File', r.fileName)}${row('Type', r.kind)}${row('Size', (r.size / 1024).toFixed(0) + ' KB')}
          ${row('Title', m.title)}${row('Pages', m.pages)}${row('Words', (parseInt(m.words, 10) || r.wordCount) ? String(parseInt(m.words, 10) || r.wordCount) : '')}
          ${row('Made with', m.application)}${row('PDF producer', m.producer)}${row('Template', m.template)}${row('Organisation', m.company)}
        </table></div>
        <div class="isec"><div class="ititle">Authorship and dates</div><table class="itable">
          ${row('Author', m.author || 'Not recorded')}${row('Last saved by', m.lastModifiedBy)}
          ${row('Created', m.created ? fmtDate(m.created) : 'Not recorded')}${row('Last edited', m.modified ? fmtDate(m.modified) : 'Not recorded')}
          ${row('Saved to this device', r.lastModifiedOnDisk ? fmtDate(r.lastModifiedOnDisk) : '')}
        </table></div>
        <div class="isec"><div class="ititle">Version history</div><table class="itable">
          ${row('Revision number', m.revision)}
          ${row('Total editing time', m.totalEditMinutes ? m.totalEditMinutes + ' min' : (r.kind === 'Word document' ? 'Not recorded' : ''))}
          ${row('Editing sessions', h.sessions !== undefined ? String(h.sessions) : '')}
          ${row('Tracked changes', h.trackedInsertions !== undefined ? `${h.trackedInsertions} insertions, ${h.trackedDeletions} deletions${h.trackedAuthors.length ? ' by ' + h.trackedAuthors.join(', ') : ''}` : '')}
          ${row('Tracked change dates', h.trackedDates && h.trackedDates.length ? fmtDate(h.trackedDates[0]) + ' to ' + fmtDate(h.trackedDates[h.trackedDates.length - 1]) : '')}
          ${row('Comments', h.comments ? `${h.comments} by ${h.commentAuthors.join(', ')}` : '')}
          ${row('Edits after creation', h.incrementalSaves !== undefined ? String(h.incrementalSaves) : '')}
          ${(h.xmpHistory || []).length ? row('Save history', h.xmpHistory.join('\n')) : ''}
        </table>
        ${r.cloud ? '<p class="helper">This is the history stored inside the downloaded file. The online version history is shown separately.</p>' : (window.BTEC_ENABLE_LINKS === true ? '<p class="helper">Files don\'t carry full version history. Paste the learner\'s Google Docs or OneDrive link instead of uploading to see it.</p>' : '<p class="helper">Files don\'t carry full version history. For that, ask to see the learner\'s OneDrive, SharePoint or Google Docs version history.</p>')}</div>
        ${r.cloud && window.BTECCloud ? window.BTECCloud.historyHtml(r.cloud) : ''}
        <div class="isec"><div class="ititle">Links found (${r.links.length})</div>
          ${r.links.length ? `<ul class="ilist links">${r.links.slice(0, 50).map(l => `<li class="${AI_LINK_RE.test(l) ? 'ailink' : ''}">${esc(l)}</li>`).join('')}</ul>` : '<p class="helper">No hyperlinks in the file.</p>'}
          ${r.references.length ? `<div class="ititle" style="margin-top:8px">Reference list (${r.references.length})</div><ul class="ilist">${r.references.map(l => `<li>${esc(l)}</li>`).join('')}</ul>` : ''}
        </div>
        ${aiHtml}
      </div>
      <p class="helper" style="margin-top:10px">These are indicators to prompt an authenticity conversation, not proof. Metadata can be missing or altered and AI detection is unreliable, so follow your centre's assessment malpractice policy before drawing conclusions.</p>`;
  };
})();

/* ===== marking.js ===== */
// ─────────────────────────────────────────────────────────────
// Marked copies of the learner's work
//
//   buildMarkedPdf()      → PDF of the submission with numbered,
//                           colour-coded comments in a right-hand
//                           margin, plus a final page with the
//                           criteria decisions and the feedback box.
//   buildCommentedDocx()  → the learner's original .docx with real
//                           Word comments attached to the passages.
//
// PDF uploads keep their exact pages (comments sit in an added margin,
// passages are highlighted). Word uploads are re-drawn page by page
// from the document's text and images, so layout is close but not
// identical to Word. Uses pdf-lib, and pdf.js to find passages in PDFs.
// ─────────────────────────────────────────────────────────────

(function () {
  const PDFLIB_URL = 'https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js';
  const PDFJS_URL = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@6.2.108/build/pdf.min.mjs';
  const PDFJS_WORKER_URL = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@6.2.108/build/pdf.worker.min.mjs';
  const W_NS = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';

  // ── loading libraries on demand ──
  function loadScript(src) {
    return new Promise((res, rej) => {
      const s = document.createElement('script');
      s.src = src; s.onload = res; s.onerror = () => rej(new Error('Could not load ' + src.split('/').slice(-1)[0] + '. Check the internet connection.'));
      document.head.appendChild(s);
    });
  }
  async function pdfLib() { if (!window.PDFLib) await loadScript(PDFLIB_URL); return window.PDFLib; }
  // pdf.js 6 relies on very new JavaScript features; add them for browsers that lack them
  function polyfillForPdfjs() {
    [Map, WeakMap].forEach(C => {
      if (!C.prototype.getOrInsertComputed) C.prototype.getOrInsertComputed = function (k, f) { if (!this.has(k)) this.set(k, f(k)); return this.get(k); };
      if (!C.prototype.getOrInsert) C.prototype.getOrInsert = function (k, v) { if (!this.has(k)) this.set(k, v); return this.get(k); };
    });
    if (!Promise.withResolvers) Promise.withResolvers = function () { let resolve, reject; const promise = new Promise((a, b) => { resolve = a; reject = b; }); return { promise, resolve, reject }; };
    if (!Uint8Array.prototype.toHex) Uint8Array.prototype.toHex = function () { return Array.from(this, b => b.toString(16).padStart(2, '0')).join(''); };
    if (!Uint8Array.fromBase64) Uint8Array.fromBase64 = s => Uint8Array.from(atob(s), c => c.charCodeAt(0));
    if (!Uint8Array.prototype.toBase64) Uint8Array.prototype.toBase64 = function () { let s = ''; this.forEach(b => s += String.fromCharCode(b)); return btoa(s); };
    if (!Math.sumPrecise) Math.sumPrecise = arr => { let t = 0; for (const x of arr) t += x; return t; };
  }
  let pdfjsPromise = null;
  function pdfjs() {
    polyfillForPdfjs();
    if (!pdfjsPromise) pdfjsPromise = import(PDFJS_URL).then(lib => { lib.GlobalWorkerOptions.workerSrc = PDFJS_WORKER_URL; return lib; });
    return pdfjsPromise;
  }

  // ── text helpers ──
  const norm = s => (s || '').toLowerCase()
    .replace(/[‘’‛′`]/g, "'").replace(/[“”″]/g, '"').replace(/[‐‑‒–—―]/g, '-')
    .replace(/[^a-z0-9'\- ]+/g, ' ').replace(/\s+/g, ' ').trim();
  const tokens = s => norm(s).split(' ').filter(w => w.length > 2);
  function bestMatch(texts, quote) {
    const q = norm(quote);
    if (!q) return -1;
    let i = texts.findIndex(t => norm(t).includes(q));
    if (i !== -1) return i;
    const qt = tokens(quote);
    if (qt.length < 3) return -1;
    let best = -1, score = 0;
    texts.forEach((t, idx) => {
      const tt = new Set(tokens(t));
      const hit = qt.filter(w => tt.has(w)).length / qt.length;
      if (hit > score) { score = hit; best = idx; }
    });
    return score >= 0.6 ? best : -1;
  }

  const COLORS = {
    met: [0.114, 0.620, 0.459],
    not_met: [0.639, 0.176, 0.176],
    note: [0.216, 0.541, 0.867],
  };
  const LABEL = { met: 'Met', not_met: 'Not yet met', note: 'Note' };

  function makeFonts(doc, PDFLib) {
    return Promise.all([
      doc.embedFont(PDFLib.StandardFonts.Helvetica),
      doc.embedFont(PDFLib.StandardFonts.HelveticaBold),
      doc.embedFont(PDFLib.StandardFonts.HelveticaOblique),
    ]).then(([regular, bold, italic]) => ({ regular, bold, italic }));
  }
  // Standard PDF fonts only cover Western European characters; swap anything else
  function safe(text, font) {
    const cache = safe.cache || (safe.cache = new Map());
    return Array.from(String(text || '').replace(/\t/g, '    ')).map(ch => {
      if (ch === '\n') return ch;
      const key = font.name + ch;
      if (!cache.has(key)) { try { font.encodeText(ch); cache.set(key, true); } catch (e) { cache.set(key, false); } }
      return cache.get(key) ? ch : (/\s/.test(ch) ? ' ' : '?');
    }).join('');
  }
  function wrap(text, font, size, width) {
    const out = [];
    String(text || '').split('\n').forEach(para => {
      const words = para.split(/\s+/).filter(Boolean);
      if (!words.length) { out.push(''); return; }
      let line = '';
      words.forEach(w => {
        while (font.widthOfTextAtSize(w, size) > width && w.length > 1) {
          // break very long words
          let cut = w.length - 1;
          while (cut > 1 && font.widthOfTextAtSize(w.slice(0, cut), size) > width) cut--;
          if (line) { out.push(line); line = ''; }
          out.push(w.slice(0, cut)); w = w.slice(cut);
        }
        const trial = line ? line + ' ' + w : w;
        if (font.widthOfTextAtSize(trial, size) > width && line) { out.push(line); line = w; } else line = trial;
      });
      out.push(line);
    });
    return out;
  }

  // ── margin comment boxes ──
  const NOTE_W = 168, NOTE_PAD = 6, NOTE_SIZE = 7.6, NOTE_LH = 9.6;
  function noteHeight(ann, fonts) {
    const body = wrap(safe(ann.text, fonts.regular), fonts.regular, NOTE_SIZE, NOTE_W - NOTE_PAD * 2 - 3);
    const quote = ann.quote ? wrap(safe('"' + ann.quote + '"', fonts.italic), fonts.italic, NOTE_SIZE - 0.4, NOTE_W - NOTE_PAD * 2 - 3).slice(0, 2) : [];
    return { body, quote, h: NOTE_PAD * 2 + 10 + (body.length + quote.length) * NOTE_LH };
  }
  function drawNote(page, PDFLib, fonts, ann, x, yTop, lay) {
    const { rgb } = PDFLib;
    const c = COLORS[ann.type] || COLORS.note;
    const col = rgb(...c);
    page.drawRectangle({ x, y: yTop - lay.h, width: NOTE_W, height: lay.h, color: rgb(1, 1, 1), borderColor: col, borderWidth: 0.8, opacity: 1 });
    page.drawRectangle({ x, y: yTop - lay.h, width: 3, height: lay.h, color: col });
    const head = `${ann.n}  ${ann.code ? ann.code + '  ' : ''}${LABEL[ann.type] || ''}`;
    page.drawText(safe(head, fonts.bold), { x: x + NOTE_PAD + 2, y: yTop - NOTE_PAD - 7, size: 7.8, font: fonts.bold, color: col });
    let y = yTop - NOTE_PAD - 10 - NOTE_LH + 1;
    lay.body.forEach(l => { page.drawText(l, { x: x + NOTE_PAD + 2, y, size: NOTE_SIZE, font: fonts.regular, color: rgb(0.15, 0.15, 0.15) }); y -= NOTE_LH; });
    lay.quote.forEach(l => { page.drawText(l, { x: x + NOTE_PAD + 2, y, size: NOTE_SIZE - 0.4, font: fonts.italic, color: rgb(0.45, 0.45, 0.45) }); y -= NOTE_LH; });
  }
  function badge(page, PDFLib, fonts, n, type, x, y) {
    const { rgb } = PDFLib;
    const col = rgb(...(COLORS[type] || COLORS.note));
    page.drawCircle({ x, y, size: 6, color: col });
    const t = String(n), w = fonts.bold.widthOfTextAtSize(t, 7);
    page.drawText(t, { x: x - w / 2, y: y - 2.5, size: 7, font: fonts.bold, color: rgb(1, 1, 1) });
  }
  // Place notes down the margin of each page, in the order of their anchors
  function layoutNotes(pageNotes, fonts, top, bottom) {
    const placed = [], overflow = [];
    let cursor = top;
    pageNotes.sort((a, b) => b.anchorY - a.anchorY).forEach(n => {
      const lay = noteHeight(n, fonts);
      const yTop = Math.min(cursor, n.anchorY + 6);
      if (yTop - lay.h < bottom) { overflow.push(n); return; }
      placed.push({ n, lay, yTop });
      cursor = yTop - lay.h - 6;
    });
    return { placed, overflow };
  }

  // ── final page: decisions + feedback box ──
  function addFeedbackPages(doc, PDFLib, fonts, info, leftovers) {
    const { rgb } = PDFLib;
    const W = 595.28, H = 841.89, M = 48, CW = W - 2 * M;
    let page = doc.addPage([W, H]);
    let y = H - M;
    const newPage = () => { page = doc.addPage([W, H]); y = H - M; };
    const ensure = need => { if (y - need < M) newPage(); };
    const text = (t, opts) => page.drawText(safe(t, opts.font || fonts.regular), { x: opts.x ?? M, y, size: opts.size || 10, font: opts.font || fonts.regular, color: opts.color || rgb(0.1, 0.1, 0.1) });

    text('Assessment feedback', { font: fonts.bold, size: 18 }); y -= 26;
    const details = [
      ['Learner', [info.student_name, info.student_reg && `(${info.student_reg})`].filter(Boolean).join(' ')],
      ['Programme', info.programme_title], ['Unit', info.unit_title], ['Assignment', info.assignment_title],
      ['Assessor', info.assessor_name], ['Date submitted', info.date_submitted],
    ].filter(d => d[1]);
    details.forEach(([k, v]) => {
      text(k, { font: fonts.bold, size: 9, color: rgb(0.45, 0.45, 0.45) });
      wrap(safe(v, fonts.regular), fonts.regular, 10, CW - 110).forEach((l, i) => { text(l, { x: M + 110, size: 10 }); if (i >= 0) y -= 13; });
    });
    y -= 6;
    text('Grade', { font: fonts.bold, size: 9, color: rgb(0.45, 0.45, 0.45) });
    text(info.gradeLabel || '', { x: M + 110, font: fonts.bold, size: 12 }); y -= 24;

    // criteria table
    text('Criteria decisions', { font: fonts.bold, size: 12 }); y -= 16;
    const cols = [M, M + 62, M + 120];
    page.drawLine({ start: { x: M, y: y + 10 }, end: { x: W - M, y: y + 10 }, thickness: 0.5, color: rgb(0.8, 0.8, 0.8) });
    ['Criteria', 'Achieved', 'Comment'].forEach((h, i) => text(h, { x: cols[i], font: fonts.bold, size: 8.5, color: rgb(0.45, 0.45, 0.45) }));
    y -= 14;
    const achColor = { Y: rgb(0.23, 0.43, 0.07), N: rgb(0.64, 0.18, 0.18), R: rgb(0.52, 0.31, 0.04) };
    (info.rows || []).forEach(r => {
      const lines = wrap(safe(r.comment || '', fonts.regular), fonts.regular, 9, W - M - cols[2]);
      ensure(lines.length * 12 + 8);
      text(r.code, { x: cols[0], font: fonts.bold, size: 9 });
      text({ Y: 'Y  Yes', N: 'N  No', R: 'R  Resub' }[r.achieved] || '-', { x: cols[1], font: fonts.bold, size: 9, color: achColor[r.achieved] });
      lines.forEach((l, i) => { text(l, { x: cols[2], size: 9 }); y -= 12; });
      if (!lines.length) y -= 12;
      y -= 4;
      page.drawLine({ start: { x: M, y: y + 9 }, end: { x: W - M, y: y + 9 }, thickness: 0.3, color: rgb(0.88, 0.88, 0.88) });
    });

    // feedback box, on its own page
    newPage();
    text('Feedback to the learner', { font: fonts.bold, size: 14 }); y -= 22;
    let fbLines = wrap(safe(info.feedback || '', fonts.regular), fonts.regular, 10.5, CW - 24);
    if (!fbLines.join('').trim()) fbLines = [''];
    const LH = 15, PAD = 14;
    while (fbLines.length) {
      const room = Math.floor((y - M - PAD * 2) / LH);
      const chunk = fbLines.splice(0, Math.max(1, room));
      const boxH = Math.max(fbLines.length ? 0 : 160, chunk.length * LH + PAD * 2);
      page.drawRectangle({ x: M, y: y - boxH, width: CW, height: boxH, borderColor: rgb(0.11, 0.62, 0.46), borderWidth: 1.2, color: rgb(0.97, 0.99, 0.98) });
      let ly = y - PAD - 9;
      chunk.forEach(l => { page.drawText(l, { x: M + 12, y: ly, size: 10.5, font: fonts.regular, color: rgb(0.1, 0.1, 0.1) }); ly -= LH; });
      y -= boxH + 24;
      if (fbLines.length) newPage();
    }

    if (leftovers.length) {
      ensure(40);
      text('Further comments', { font: fonts.bold, size: 12 }); y -= 16;
      leftovers.forEach(a => {
        const lines = wrap(safe(`${a.n}. ${a.code ? a.code + ' ' : ''}(${LABEL[a.type] || 'Note'}) ${a.text}${a.quote ? '  "' + a.quote + '"' : ''}`, fonts.regular), fonts.regular, 9, CW);
        ensure(lines.length * 12 + 4);
        lines.forEach(l => { text(l, { size: 9 }); y -= 12; });
        y -= 4;
      });
    }
    ensure(30);
    text('Margin comments: green = criterion met, red = not yet met, blue = note.', { size: 8, color: rgb(0.5, 0.5, 0.5) });
  }

  // ── PDF submission: keep original pages, add a comment margin ──
  async function markPdf(bytes, anns, info) {
    const PDFLib = await pdfLib();
    const { rgb, PDFDocument } = PDFLib;
    const src = await PDFDocument.load(bytes, { ignoreEncryption: true });
    const doc = await PDFDocument.create();
    const fonts = await makeFonts(doc, PDFLib);
    const embedded = await doc.embedPdf(src, src.getPageIndices());

    // locate quotes with pdf.js (fall back to page-level notes if it can't load)
    const located = new Map();
    try {
      const lib = await pdfjs();
      const pdf = await lib.getDocument({ data: bytes.slice(0), isEvalSupported: false }).promise;
      for (let p = 1; p <= pdf.numPages; p++) {
        const pg = await pdf.getPage(p);
        const tc = await pg.getTextContent();
        let full = '';
        const owner = [];
        tc.items.forEach((it, i) => {
          if (!it.str) return;
          const piece = norm(it.str) + ' ';
          full += piece;
          for (let k = 0; k < piece.length; k++) owner.push(i);
        });
        anns.forEach(a => {
          if (located.has(a) || !a.quote) return;
          const q = norm(a.quote);
          let at = q ? full.indexOf(q) : -1;
          let len = q.length;
          if (at === -1) {
            const words = q.split(' ');
            if (words.length >= 6) { const head = words.slice(0, 5).join(' '); at = full.indexOf(head); len = head.length; }
          }
          if (at === -1) return;
          const its = [...new Set(owner.slice(at, at + len))].map(i => tc.items[i]);
          const boxes = its.map(it => {
            const [, , , d, e, f] = it.transform;
            const h = it.height || Math.abs(d) || 10;
            return { x1: e, y1: f - h * 0.22, x2: e + (it.width || 0), y2: f + h * 0.85 };
          });
          located.set(a, { page: p - 1, boxes });
        });
      }
    } catch (e) { console.warn('pdf.js unavailable, using page-level comments', e); }

    const MARGIN = NOTE_W + 26;
    const perPage = {};
    const leftovers = [];
    anns.forEach(a => {
      const loc = located.get(a);
      if (loc) (perPage[loc.page] = perPage[loc.page] || []).push({ ...a, loc });
      else if (a.page && a.page >= 1 && a.page <= embedded.length) (perPage[a.page - 1] = perPage[a.page - 1] || []).push({ ...a, loc: null });
      else leftovers.push(a);
    });

    embedded.forEach((ep, i) => {
      const w = ep.width, h = ep.height;
      const page = doc.addPage([w + MARGIN, h]);
      page.drawPage(ep, { x: 0, y: 0, width: w, height: h });
      page.drawRectangle({ x: w, y: 0, width: MARGIN, height: h, color: rgb(0.965, 0.965, 0.95) });
      page.drawLine({ start: { x: w, y: 0 }, end: { x: w, y: h }, thickness: 0.5, color: rgb(0.8, 0.8, 0.8) });
      const stacked = {};
      const notes = (perPage[i] || []).map(a => {
        let anchorY = h - 40;
        if (a.loc) {
          const c = COLORS[a.type] || COLORS.note;
          a.loc.boxes.forEach(b => page.drawRectangle({ x: b.x1 - 1, y: b.y1, width: b.x2 - b.x1 + 2, height: b.y2 - b.y1, color: rgb(...c), opacity: 0.18 }));
          const top = Math.max(...a.loc.boxes.map(b => b.y2)), left = Math.min(...a.loc.boxes.map(b => b.x1));
          anchorY = top;
          const key = Math.round(top) + ':' + Math.round(left);
          const k = (stacked[key] = (stacked[key] || 0) + 1) - 1;
          badge(page, PDFLib, fonts, a.n, a.type, Math.max(8, left - 9 - k * 13), top - 4);
        }
        return { ...a, anchorY };
      });
      const { placed, overflow } = layoutNotes(notes, fonts, h - 16, 16);
      placed.forEach(p => {
        drawNote(page, PDFLib, fonts, p.n, w + 13, p.yTop, p.lay);
        if (p.n.loc) page.drawLine({ start: { x: w + 13, y: p.yTop - 8 }, end: { x: Math.min(w, Math.max(...p.n.loc.boxes.map(b => b.x2)) + 2), y: p.n.anchorY - 4 }, thickness: 0.4, color: rgb(...(COLORS[p.n.type] || COLORS.note)), opacity: 0.6 });
      });
      leftovers.push(...overflow);
    });
    addFeedbackPages(doc, PDFLib, fonts, info, leftovers.sort((a, b) => a.n - b.n));
    return doc.save();
  }

  // ── Word submission: re-draw text and images, comments in the margin ──
  async function readDocx(b64) {
    const pkg = await window.openDocx(b64, { base64: true });
    const xml = await pkg.main();
    const dom = new DOMParser().parseFromString(xml, 'application/xml');
    const paras = Array.from(dom.getElementsByTagNameNS(W_NS, 'p')).filter(p => {
      for (let a = p.parentNode; a; a = a.parentNode) if (a.localName === 'p' && a.namespaceURI === W_NS) return false;
      return true;
    });
    return { pkg, dom, paras };
  }
  function paraText(p) {
    let t = '';
    const walk = n => {
      n.childNodes.forEach(c => {
        if (c.nodeType !== 1) return;
        if (c.namespaceURI === W_NS && c.localName === 't') t += c.textContent;
        else if (c.namespaceURI === W_NS && (c.localName === 'tab')) t += ' ';
        else if (c.namespaceURI === W_NS && (c.localName === 'br' || c.localName === 'cr')) t += '\n';
        else if (!(c.namespaceURI === W_NS && (c.localName === 'delText' || c.localName === 'instrText'))) walk(c);
      });
    };
    walk(p);
    return t;
  }

  async function markDocx(b64, anns, info) {
    const PDFLib = await pdfLib();
    const { rgb, PDFDocument } = PDFLib;
    const { pkg, paras } = await readDocx(b64);
    const doc = await PDFDocument.create();
    const fonts = await makeFonts(doc, PDFLib);
    const W = 595.28, H = 841.89, M = 36, GAP = 14;
    const COLW = W - 2 * M - NOTE_W - GAP;
    const NOTE_X = M + COLW + GAP;
    const texts = paras.map(paraText);

    const relById = {};
    const relsDom = new DOMParser().parseFromString(pkg.relsXml || '<Relationships/>', 'application/xml');
    Array.from(relsDom.getElementsByTagName('Relationship')).forEach(r => relById[r.getAttribute('Id')] = r.getAttribute('Target'));
    const dir = pkg.mainPath.includes('/') ? pkg.mainPath.slice(0, pkg.mainPath.lastIndexOf('/')) : '';
    const imageCache = {};
    async function image(rId) {
      const target = relById[rId];
      if (!target) return null;
      const path = target.startsWith('/') ? target.slice(1) : (dir ? dir + '/' : '') + target;
      if (path in imageCache) return imageCache[path];
      const f = pkg.zip.file(path) || pkg.zip.file(path.replace(/^word\/\.\.\//, ''));
      let img = null;
      if (f) {
        const bytes = await f.async('uint8array');
        try {
          if (bytes[0] === 0x89 && bytes[1] === 0x50) img = await doc.embedPng(bytes);
          else if (bytes[0] === 0xff && bytes[1] === 0xd8) img = await doc.embedJpg(bytes);
        } catch (e) { img = null; }
      }
      return (imageCache[path] = img);
    }

    let page, y, pageNo = 0;
    const pages = [];
    const newPage = () => {
      page = doc.addPage([W, H]); pages.push(page); pageNo++;
      page.drawRectangle({ x: NOTE_X - GAP / 2, y: 0, width: W - NOTE_X + GAP / 2, height: H, color: rgb(0.965, 0.965, 0.95) });
      page.drawText(safe(`Marked copy: ${info.fileName || ''}${info.student_name ? '  |  ' + info.student_name : ''}`, fonts.regular), { x: M, y: H - 22, size: 7.5, font: fonts.regular, color: rgb(0.55, 0.55, 0.55) });
      page.drawText(String(pageNo), { x: M + COLW / 2, y: 16, size: 8, font: fonts.regular, color: rgb(0.55, 0.55, 0.55) });
      y = H - M - 8;
    };
    newPage();
    const positions = [];
    const rowPos = new Map();

    for (let i = 0; i < paras.length; i++) {
      const p = paras[i];
      const ps = p.getElementsByTagNameNS(W_NS, 'pStyle')[0];
      const style = ps ? (ps.getAttributeNS(W_NS, 'val') || ps.getAttribute('w:val') || '') : '';
      const isList = p.getElementsByTagNameNS(W_NS, 'numPr').length > 0 || /^List/i.test(style);
      let row = null;
      for (let a = p.parentNode; a; a = a.parentNode) if (a.localName === 'tr' && a.namespaceURI === W_NS) { row = a; break; }
      const inTable = !!row;
      if (row) {
        // draw each table row once, cells side by side as "cell | cell"
        if (rowPos.has(row)) { positions.push(rowPos.get(row)); continue; }
        const cells = Array.from(row.childNodes).filter(c => c.localName === 'tc');
        const rowText = cells.map(c => Array.from(c.getElementsByTagNameNS(W_NS, 'p')).map(paraText).join(' ').trim()).join('   |   ');
        const lines = wrap(safe(rowText, fonts.regular), fonts.regular, 9.5, COLW - 12);
        const pos = { segs: [] };
        let segStart = null;
        lines.forEach(l => {
          if (y - 13 < M) { if (segStart !== null) pos.segs.push({ page: pages.length - 1, top: segStart, bottom: y }); newPage(); segStart = null; }
          if (segStart === null) segStart = y;
          page.drawText(l, { x: M + 6, y: y - 9.5, size: 9.5, font: fonts.regular, color: rgb(0.1, 0.1, 0.1) });
          y -= 13;
        });
        if (segStart !== null) pos.segs.push({ page: pages.length - 1, top: segStart, bottom: y });
        page.drawLine({ start: { x: M, y: y + 2 }, end: { x: M + COLW, y: y + 2 }, thickness: 0.3, color: rgb(0.8, 0.8, 0.8) });
        y -= 3;
        rowPos.set(row, pos); positions.push(pos);
        continue;
      }
      let size = 10, font = fonts.regular, before = 4, lh = 13.2;
      const hm = style.match(/^(Heading|heading)\s*(\d)|^Title$/);
      if (/^Title$/i.test(style)) { size = 18; font = fonts.bold; before = 10; lh = 22; }
      else if (hm) { const lvl = parseInt(hm[2], 10); size = lvl === 1 ? 15 : lvl === 2 ? 13 : 11.5; font = fonts.bold; before = 10; lh = size * 1.3; }
      const allBold = !hm && p.getElementsByTagNameNS(W_NS, 'r').length > 0 && Array.from(p.getElementsByTagNameNS(W_NS, 'r')).every(r => r.getElementsByTagNameNS(W_NS, 'b').length || !r.getElementsByTagNameNS(W_NS, 't').length);
      if (allBold) font = fonts.bold;
      const indent = isList ? 12 : inTable ? 8 : 0;
      const raw = texts[i];
      const lines = raw.trim() ? wrap(safe((isList ? '•  ' : '') + raw, font), font, size, COLW - indent) : [];
      const pos = { segs: [] };
      if (!lines.length && !p.getElementsByTagNameNS('http://schemas.openxmlformats.org/drawingml/2006/main', 'blip').length) { y -= 6; positions.push(null); continue; }
      y -= before;
      let segStart = null;
      lines.forEach(l => {
        if (y - lh < M) { if (segStart !== null) pos.segs.push({ page: pages.length - 1, top: segStart, bottom: y }); newPage(); segStart = null; }
        if (segStart === null) segStart = y;
        page.drawText(l, { x: M + indent, y: y - size, size, font, color: rgb(0.1, 0.1, 0.1) });
        y -= lh;
      });
      if (segStart !== null) pos.segs.push({ page: pages.length - 1, top: segStart, bottom: y });

      // pictures in this paragraph
      const blips = Array.from(p.getElementsByTagNameNS('http://schemas.openxmlformats.org/drawingml/2006/main', 'blip'));
      for (const b of blips) {
        const rId = b.getAttributeNS('http://schemas.openxmlformats.org/officeDocument/2006/relationships', 'embed');
        const img = await image(rId);
        if (!img) continue;
        let ext = null;
        for (let a = b.parentNode; a && !ext; a = a.parentNode) {
          const e = a.getElementsByTagNameNS ? a.getElementsByTagNameNS('http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing', 'extent')[0] : null;
          if (e) ext = e;
        }
        let iw = ext ? parseInt(ext.getAttribute('cx'), 10) / 12700 : img.width * 0.75;
        let ih = ext ? parseInt(ext.getAttribute('cy'), 10) / 12700 : img.height * 0.75;
        const scale = Math.min(1, COLW / iw, 380 / ih);
        iw *= scale; ih *= scale;
        if (y - ih - 6 < M) newPage();
        page.drawImage(img, { x: M, y: y - ih - 4, width: iw, height: ih });
        pos.segs.push({ page: pages.length - 1, top: y, bottom: y - ih - 4 });
        y -= ih + 10;
      }
      positions.push(pos);
    }

    // anchor each comment to its paragraph
    const perPage = {};
    const leftovers = [];
    const badgeCount = new Map();
    anns.forEach(a => {
      const idx = bestMatch(texts, a.quote);
      const pos = idx >= 0 ? positions[idx] : null;
      if (!pos || !pos.segs.length) { leftovers.push(a); return; }
      const seg = pos.segs[0];
      const c = rgb(...(COLORS[a.type] || COLORS.note));
      pos.segs.forEach(s => pages[s.page].drawRectangle({ x: M - 9, y: s.bottom + 2, width: 3, height: s.top - s.bottom - 2, color: c }));
      const k = badgeCount.get(pos) || 0; badgeCount.set(pos, k + 1);
      badge(pages[seg.page], PDFLib, fonts, a.n, a.type, M - 18, seg.top - 6 - k * 14);
      (perPage[seg.page] = perPage[seg.page] || []).push({ ...a, anchorY: seg.top });
    });
    Object.entries(perPage).forEach(([pi, notes]) => {
      const { placed, overflow } = layoutNotes(notes, fonts, H - M, M);
      placed.forEach(p => drawNote(pages[pi], PDFLib, fonts, p.n, NOTE_X, p.yTop, p.lay));
      leftovers.push(...overflow);
    });
    addFeedbackPages(doc, PDFLib, fonts, info, leftovers.sort((a, b) => a.n - b.n));
    return doc.save();
  }

  // ── Original .docx with real Word comments ──
  async function commentDocx(b64, anns, info) {
    const { pkg, dom, paras } = await readDocx(b64);
    const texts = paras.map(paraText);
    const zip = pkg.zip;
    const dir = pkg.mainPath.includes('/') ? pkg.mainPath.slice(0, pkg.mainPath.lastIndexOf('/')) : '';
    const mainFile = pkg.mainPath.slice(dir ? dir.length + 1 : 0);
    const relsPath = `${dir ? dir + '/' : ''}_rels/${mainFile}.rels`;

    // existing comments part, or a new one
    const commentRel = pkg.docRels.find(r => /\/comments$/i.test(r.type));
    let commentsPath = commentRel ? (commentRel.target.startsWith('/') ? commentRel.target.slice(1) : (dir ? dir + '/' : '') + commentRel.target) : `${dir ? dir + '/' : ''}comments.xml`;
    let cDom;
    if (commentRel && zip.file(commentsPath)) cDom = new DOMParser().parseFromString(await zip.file(commentsPath).async('string'), 'application/xml');
    else cDom = new DOMParser().parseFromString(`<?xml version="1.0" encoding="UTF-8" standalone="yes"?><w:comments xmlns:w="${W_NS}"></w:comments>`, 'application/xml');
    const ids = [...dom.getElementsByTagNameNS(W_NS, 'commentRangeStart')].map(e => parseInt(e.getAttributeNS(W_NS, 'id'), 10))
      .concat([...cDom.getElementsByTagNameNS(W_NS, 'comment')].map(e => parseInt(e.getAttributeNS(W_NS, 'id'), 10))).filter(n => !isNaN(n));
    let nextId = ids.length ? Math.max(...ids) + 1 : 0;
    const author = info.assessor_name || 'Assessor';
    const initials = author.split(/\s+/).map(w => w[0]).join('').slice(0, 3).toUpperCase();
    const now = new Date().toISOString().replace(/\.\d+Z$/, 'Z');
    const W = tag => dom.createElementNS(W_NS, 'w:' + tag);
    const setA = (el, k, v) => el.setAttributeNS(W_NS, 'w:' + k, v);
    const leftovers = [];
    const docBody = dom.getElementsByTagNameNS(W_NS, 'body')[0];

    anns.forEach(a => {
      const idx = bestMatch(texts, a.quote);
      const p = idx >= 0 ? paras[idx] : null;
      const id = String(nextId++);
      // comment text
      const c = cDom.createElementNS(W_NS, 'w:comment');
      c.setAttributeNS(W_NS, 'w:id', id); c.setAttributeNS(W_NS, 'w:author', author); c.setAttributeNS(W_NS, 'w:initials', initials); c.setAttributeNS(W_NS, 'w:date', now);
      const cp = cDom.createElementNS(W_NS, 'w:p');
      const addRun = (txt, bold) => {
        const r = cDom.createElementNS(W_NS, 'w:r');
        if (bold) { const rp = cDom.createElementNS(W_NS, 'w:rPr'); rp.appendChild(cDom.createElementNS(W_NS, 'w:b')); r.appendChild(rp); }
        const t = cDom.createElementNS(W_NS, 'w:t'); t.setAttribute('xml:space', 'preserve'); t.textContent = txt; r.appendChild(t); cp.appendChild(r);
      };
      addRun(`${p ? '' : 'General comment. '}${a.code ? a.code + ' ' : ''}${LABEL[a.type] || 'Note'}: `, true);
      addRun(a.text, false);
      c.appendChild(cp); cDom.documentElement.appendChild(c);
      // anchor in the document
      const target = p || (paras.find(x => texts[paras.indexOf(x)].trim()) || null);
      if (!target) { leftovers.push(a); return; }
      const start = W('commentRangeStart'); setA(start, 'id', id);
      const end = W('commentRangeEnd'); setA(end, 'id', id);
      const refRun = W('r'); const rPr = W('rPr'); const rs = W('rStyle'); setA(rs, 'val', 'CommentReference'); rPr.appendChild(rs); refRun.appendChild(rPr);
      const ref = W('commentReference'); setA(ref, 'id', id); refRun.appendChild(ref);
      const pPr = Array.from(target.childNodes).find(n => n.localName === 'pPr');
      if (pPr) pPr.after(start); else target.insertBefore(start, target.firstChild);
      target.appendChild(end); target.appendChild(refRun);
    });

    zip.file(pkg.mainPath, new XMLSerializer().serializeToString(dom));
    zip.file(commentsPath, new XMLSerializer().serializeToString(cDom));
    if (!commentRel) {
      const relsXml = pkg.relsXml || '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"></Relationships>';
      const used = [...relsXml.matchAll(/Id="rId(\d+)"/g)].map(m => parseInt(m[1], 10));
      const rid = 'rId' + ((used.length ? Math.max(...used) : 0) + 1);
      const rel = `<Relationship Id="${rid}" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/comments" Target="comments.xml"/>`;
      zip.file(relsPath, relsXml.replace('</Relationships>', rel + '</Relationships>'));
      let ct = await zip.file('[Content_Types].xml').async('string');
      if (!/PartName="\/[^"]*comments\.xml"/i.test(ct)) ct = ct.replace('</Types>', `<Override PartName="/${commentsPath}" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.comments+xml"/></Types>`);
      zip.file('[Content_Types].xml', ct);
    }
    return { blob: await zip.generateAsync({ type: 'blob', compression: 'DEFLATE', mimeType: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' }), leftovers };
  }

  // ── public API ──
  function numbered(anns) {
    return (anns || []).filter(a => a && a.include !== false && (a.text || a.comment))
      .map((a, i) => ({ n: i + 1, code: a.code || '', type: ['met', 'not_met', 'note'].includes(a.type) ? a.type : 'note', text: a.text || a.comment || '', quote: a.quote || '', page: a.page }));
  }
  window.BTECPdfjs = pdfjs;   // the marking desk uses it to show PDF pages
  window.BTECPdfLib = pdfLib;  // used to strip personal details from PDF metadata before the AI check
  window.buildMarkedPdf = async function (file, anns, info) {
    const list = numbered(anns);
    const bytes = Uint8Array.from(atob(file.base64), c => c.charCodeAt(0));
    const out = file.kind === 'pdf' ? await markPdf(bytes, list, info) : await markDocx(file.base64, list, info);
    return new Blob([out], { type: 'application/pdf' });
  };
  window.buildCommentedDocx = async function (file, anns, info) {
    return commentDocx(file.base64, numbered(anns), info);
  };
})();

/* ===== cloud.js ===== */
// ─────────────────────────────────────────────────────────────
// Read work from a Google Docs / Google Drive or Microsoft OneDrive /
// SharePoint / Word online link, including its version history.
//
// Needs (see README, "Linking to online documents"):
//   • the app hosted on a web address (sign-in can't run from a local file)
//   • window.BTEC_GOOGLE_CLIENT_ID and/or window.BTEC_MS_CLIENT_ID in keys.js
//
// The assessor signs in with an account that can open the learner's
// document. Tokens stay in the browser and are only sent to Google or
// Microsoft. Nothing is stored.
// ─────────────────────────────────────────────────────────────

(function () {
  const GSI_URL = 'https://accounts.google.com/gsi/client';
  const MSAL_URL = 'https://cdn.jsdelivr.net/npm/@azure/msal-browser@2.39.0/lib/msal-browser.min.js';
  const DRIVE = 'https://www.googleapis.com/drive/v3';
  const GRAPH = 'https://graph.microsoft.com/v1.0';
  const DOCX = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

  function loadScript(src) {
    return new Promise((res, rej) => {
      if ([...document.scripts].some(s => s.src === src)) return res();
      const s = document.createElement('script');
      s.src = src; s.async = true; s.onload = res;
      s.onerror = () => rej(new Error('Could not load the sign-in library. Check the internet connection.'));
      document.head.appendChild(s);
    });
  }

  // ── which service a link belongs to ──
  function detect(url) {
    let u;
    try { u = new URL(url.trim()); } catch (e) { return null; }
    const h = u.hostname.toLowerCase();
    if (h === 'docs.google.com' || h === 'drive.google.com') {
      const m = u.pathname.match(/\/d\/([A-Za-z0-9_-]{10,})/) || [null, u.searchParams.get('id')];
      return m[1] ? { provider: 'google', id: m[1], url: u.href } : { provider: 'google', id: null, url: u.href };
    }
    if (/(^|\.)sharepoint\.com$/.test(h) || h === 'onedrive.live.com' || h === '1drv.ms' || /(^|\.)officeapps\.live\.com$/.test(h)) {
      return { provider: 'microsoft', url: u.href };
    }
    return { provider: 'unknown', url: u.href };
  }

  async function getJson(url, token) {
    const r = await fetch(url, { headers: { Authorization: 'Bearer ' + token } });
    if (!r.ok) throw await apiError(r);
    return r.json();
  }
  async function apiError(r) {
    let msg = '';
    try { const j = await r.json(); msg = (j.error && (j.error.message || j.error.code)) || ''; } catch (e) {}
    if (r.status === 401) return new Error('Sign-in expired or was refused. Try again.');
    if (r.status === 403) return new Error("Your account doesn't have access to this document. Ask the learner to share it with you (Viewer is enough). " + msg);
    if (r.status === 404) return new Error("Document not found. Check the link, and that it's shared with the account you signed in with.");
    return new Error(`The service returned ${r.status}. ${msg}`);
  }

  // ───────────────────────── Google ─────────────────────────
  let gToken = null, gExpiry = 0;
  async function googleToken() {
    const id = (window.BTEC_GOOGLE_CLIENT_ID || '').trim();
    if (!id) throw new Error('Google linking is not set up yet. Add BTEC_GOOGLE_CLIENT_ID to keys.js (see README).');
    if (gToken && Date.now() < gExpiry - 60000) return gToken;
    await loadScript(GSI_URL);
    return new Promise((res, rej) => {
      const client = window.google.accounts.oauth2.initTokenClient({
        client_id: id,
        scope: 'https://www.googleapis.com/auth/drive.readonly',
        callback: t => {
          if (t.error) return rej(new Error('Google sign-in failed: ' + (t.error_description || t.error)));
          gToken = t.access_token; gExpiry = Date.now() + (t.expires_in || 3600) * 1000; res(gToken);
        },
        error_callback: e => rej(new Error(e && e.type === 'popup_closed' ? 'Google sign-in was closed before finishing.' : 'Google sign-in was blocked. Allow pop-ups for this page and try again.')),
      });
      client.requestAccessToken({ prompt: '' });
    });
  }

  async function fetchGoogle(link) {
    if (!link.id) throw new Error("Couldn't find the document ID in that Google link. Use the link from the Share button or the address bar.");
    const token = await googleToken();
    const meta = await getJson(`${DRIVE}/files/${link.id}?supportsAllDrives=true&fields=id,name,mimeType,createdTime,modifiedTime,size,owners(displayName,emailAddress),lastModifyingUser(displayName,emailAddress),sharingUser(displayName),webViewLink`, token);
    let blobUrl, mime, name = meta.name || 'Google document';
    if (meta.mimeType === 'application/vnd.google-apps.document') {
      blobUrl = `${DRIVE}/files/${link.id}/export?mimeType=${encodeURIComponent(DOCX)}`; mime = DOCX; name += '.docx';
    } else if (meta.mimeType === 'application/pdf' || meta.mimeType === DOCX) {
      blobUrl = `${DRIVE}/files/${link.id}?alt=media&supportsAllDrives=true`; mime = meta.mimeType;
      if (!/\.(pdf|docx)$/i.test(name)) name += mime === DOCX ? '.docx' : '.pdf';
    } else if (meta.mimeType === 'application/vnd.google-apps.presentation' || meta.mimeType === 'application/vnd.google-apps.spreadsheet') {
      blobUrl = `${DRIVE}/files/${link.id}/export?mimeType=application/pdf`; mime = 'application/pdf'; name += '.pdf';
    } else {
      throw new Error(`That file type (${meta.mimeType}) can't be marked. Use a Google Doc, Word file or PDF.`);
    }
    const r = await fetch(blobUrl, { headers: { Authorization: 'Bearer ' + token } });
    if (!r.ok) throw await apiError(r);
    const bytes = await r.arrayBuffer();

    // version history
    const revisions = [];
    let page = '';
    let historyNote = '';
    try {
      do {
        const j = await getJson(`${DRIVE}/files/${link.id}/revisions?pageSize=1000&fields=nextPageToken,revisions(id,modifiedTime,lastModifyingUser(displayName,emailAddress),size)${page ? '&pageToken=' + page : ''}`, token);
        (j.revisions || []).forEach(v => revisions.push({ when: v.modifiedTime, who: (v.lastModifyingUser || {}).displayName || (v.lastModifyingUser || {}).emailAddress || '', size: v.size ? +v.size : null }));
        page = j.nextPageToken || '';
      } while (page);
    } catch (e) { historyNote = "Version history isn't available to your account (only people with edit access can see it). " + e.message; }
    if (meta.mimeType === 'application/vnd.google-apps.document')
      historyNote = (historyNote ? historyNote + ' ' : '') + 'Google merges small edits into one version, so the list shows editing sessions rather than every keystroke.';

    return {
      file: new File([bytes], name, { type: mime }),
      cloud: {
        provider: 'Google', source: meta.mimeType === 'application/vnd.google-apps.document' ? 'Google Docs' : 'Google Drive',
        name: meta.name, link: meta.webViewLink || link.url,
        created: meta.createdTime, modified: meta.modifiedTime,
        owner: ((meta.owners || [])[0] || {}).displayName || ((meta.owners || [])[0] || {}).emailAddress || '',
        lastEditor: (meta.lastModifyingUser || {}).displayName || '',
        versions: revisions, note: historyNote,
      },
    };
  }

  // ───────────────────────── Microsoft ─────────────────────────
  let msal = null;
  async function msToken() {
    const id = (window.BTEC_MS_CLIENT_ID || '').trim();
    if (!id) throw new Error('Microsoft linking is not set up yet. Add BTEC_MS_CLIENT_ID to keys.js (see README).');
    await loadScript(MSAL_URL);
    if (!msal) {
      msal = new window.msal.PublicClientApplication({
        auth: { clientId: id, authority: 'https://login.microsoftonline.com/' + ((window.BTEC_MS_TENANT || 'common').trim()), redirectUri: location.origin + location.pathname },
        cache: { cacheLocation: 'sessionStorage' },
      });
      if (msal.initialize) await msal.initialize();
    }
    const scopes = ['Files.Read.All', 'User.Read'];
    const acct = msal.getAllAccounts()[0];
    try {
      if (acct) return (await msal.acquireTokenSilent({ scopes, account: acct })).accessToken;
    } catch (e) { /* fall through to pop-up */ }
    try {
      return (await msal.acquireTokenPopup({ scopes, account: acct })).accessToken;
    } catch (e) {
      if (/popup_window_error|BrowserAuthError.*popup/i.test(String(e))) throw new Error('Microsoft sign-in was blocked. Allow pop-ups for this page and try again.');
      if (/consent|AADSTS65001|AADSTS90094/i.test(String(e))) throw new Error('Your college account needs IT to approve this app before it can read files. ' + (e.message || ''));
      throw new Error('Microsoft sign-in failed: ' + (e.errorMessage || e.message || e));
    }
  }
  function shareId(url) {
    const b64 = btoa(unescape(encodeURIComponent(url))).replace(/=+$/, '').replace(/\//g, '_').replace(/\+/g, '-');
    return 'u!' + b64;
  }

  async function fetchMicrosoft(link) {
    const token = await msToken();
    const item = await getJson(`${GRAPH}/shares/${shareId(link.url)}/driveItem?$select=id,name,size,file,webUrl,createdDateTime,lastModifiedDateTime,createdBy,lastModifiedBy,parentReference,@microsoft.graph.downloadUrl`, token);
    const name = item.name || 'Document';
    if (!/\.(docx|pdf)$/i.test(name)) throw new Error(`"${name}" isn't a Word (.docx) or PDF file, so it can't be marked.`);
    let bytes;
    const dl = item['@microsoft.graph.downloadUrl'];
    const r = dl ? await fetch(dl) : await fetch(`${GRAPH}/drives/${item.parentReference.driveId}/items/${item.id}/content`, { headers: { Authorization: 'Bearer ' + token } });
    if (!r.ok) throw await apiError(r);
    bytes = await r.arrayBuffer();

    const versions = [];
    let note = '';
    try {
      let next = `${GRAPH}/drives/${item.parentReference.driveId}/items/${item.id}/versions?$top=200`;
      while (next) {
        const j = await getJson(next, token);
        (j.value || []).forEach(v => versions.push({ when: v.lastModifiedDateTime, who: ((v.lastModifiedBy || {}).user || {}).displayName || '', size: typeof v.size === 'number' ? v.size : null, label: v.id }));
        next = j['@odata.nextLink'] || '';
      }
    } catch (e) { note = "Version history isn't available to your account (it usually needs edit access). " + e.message; }
    if (!note) note = 'OneDrive and SharePoint save a version roughly every few minutes of editing, so short sessions can share one version.';
    return {
      file: new File([bytes], name, { type: /\.pdf$/i.test(name) ? 'application/pdf' : DOCX }),
      cloud: {
        provider: 'Microsoft', source: /sharepoint\.com/i.test(item.webUrl || link.url) ? 'SharePoint / OneDrive for work' : 'OneDrive',
        name, link: item.webUrl || link.url,
        created: item.createdDateTime, modified: item.lastModifiedDateTime,
        owner: ((item.createdBy || {}).user || {}).displayName || '',
        lastEditor: ((item.lastModifiedBy || {}).user || {}).displayName || '',
        versions, note,
      },
    };
  }

  // ───────────────────────── Version history analysis ─────────────────────────
  function historyFlags(c, wordCount, submittedOn) {
    const flags = [];
    const add = (level, text) => flags.push({ level, text });
    const v = (c.versions || []).filter(x => x.when).map(x => ({ ...x, t: new Date(x.when) })).sort((a, b) => a.t - b.t);
    if (!v.length) { if (c.note && /isn't available/.test(c.note)) add('info', 'Version history could not be read with your account. Ask for edit access, or ask the learner to show it.'); return flags; }
    const spanH = (v[v.length - 1].t - v[0].t) / 3.6e6;
    const days = new Set(v.map(x => x.t.toDateString())).size;
    if (v.length <= 2 && wordCount > 400) add('high', `Only ${v.length} version${v.length > 1 ? 's' : ''} saved for about ${wordCount} words. Work built up over time normally shows many versions; this pattern fits text pasted in from elsewhere.`);
    else if (spanH < 1 && wordCount > 600) add('medium', `All ${v.length} versions were saved within ${Math.max(1, Math.round(spanH * 60))} minutes, for about ${wordCount} words.`);
    // biggest single jump in size (Microsoft gives a size per version)
    const sized = v.filter(x => x.size);
    if (sized.length >= 3) {
      const final = sized[sized.length - 1].size;
      let jump = 0, at = null;
      for (let i = 1; i < sized.length; i++) { const d = sized[i].size - sized[i - 1].size; if (d > jump) { jump = d; at = sized[i]; } }
      if (final > 30000 && jump / final > 0.6) add('medium', `One save added about ${Math.round(jump / final * 100)}% of the file's final size (${fmt(at.when)}). Large single jumps can mean content was pasted in.`);
    }
    const editors = [...new Set(v.map(x => x.who).filter(Boolean))];
    if (editors.length > 1) add('info', `More than one person edited this document: ${editors.join(', ')}.`);
    if (c.owner && c.lastEditor && c.owner !== c.lastEditor) add('info', `Owned by ${c.owner}, last edited by ${c.lastEditor}.`);
    const dl = parseUk(submittedOn);
    if (dl) {
      const after = v.filter(x => x.t > dl);
      if (after.length) add('info', `${after.length} version(s) saved after the recorded submission date, the last on ${fmt(after[after.length - 1].when)}.`);
    }
    if (v.length > 2 && days === 1 && wordCount > 1200) add('info', `All editing happened on a single day (${v[0].t.toLocaleDateString('en-GB')}).`);
    return flags;
  }
  function parseUk(s) {
    const m = (s || '').match(/(\d{1,2})[\/.\-](\d{1,2})[\/.\-](\d{2,4})/);
    if (!m) return null;
    let y = +m[3]; if (y < 100) y += 2000;
    return new Date(y, +m[2] - 1, +m[1], 23, 59);
  }
  const fmt = d => { const x = new Date(d); return isNaN(x) ? String(d || '') : x.toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }); };
  const esc = t => String(t ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');

  function historyHtml(c) {
    if (!c) return '';
    const v = (c.versions || []).filter(x => x.when).slice().sort((a, b) => new Date(a.when) - new Date(b.when));
    const days = new Set(v.map(x => new Date(x.when).toDateString())).size;
    const editors = [...new Set(v.map(x => x.who).filter(Boolean))];
    const rows = v.slice(-60).map((x, i, arr) => {
      const prev = i > 0 ? arr[i - 1] : null;
      const delta = x.size && prev && prev.size ? x.size - prev.size : null;
      return `<tr><td>${esc(fmt(x.when))}</td><td>${esc(x.who)}</td><td>${x.size ? Math.round(x.size / 1024) + ' KB' + (delta ? ` (${delta > 0 ? '+' : ''}${Math.round(delta / 1024)})` : '') : ''}</td></tr>`;
    }).join('');
    return `<div class="isec cloud-sec"><div class="ititle">Online version history (${esc(c.source)})</div>
      <table class="itable">
        <tr><td class="ik">Document</td><td><a href="${esc(c.link)}" target="_blank" rel="noopener">${esc(c.name)}</a></td></tr>
        ${c.owner ? `<tr><td class="ik">Owner</td><td>${esc(c.owner)}</td></tr>` : ''}
        <tr><td class="ik">Created</td><td>${esc(fmt(c.created))}</td></tr>
        <tr><td class="ik">Last edited</td><td>${esc(fmt(c.modified))}${c.lastEditor ? ' by ' + esc(c.lastEditor) : ''}</td></tr>
        <tr><td class="ik">Versions</td><td>${v.length ? `${v.length} across ${days} day${days === 1 ? '' : 's'}${v.length > 1 ? `, ${esc(fmt(v[0].when))} to ${esc(fmt(v[v.length - 1].when))}` : ''}` : 'Not available'}</td></tr>
        ${editors.length ? `<tr><td class="ik">Edited by</td><td>${esc(editors.join(', '))}</td></tr>` : ''}
      </table>
      ${rows ? `<details class="vlist"><summary>Show versions${v.length > 60 ? ' (latest 60)' : ''}</summary><table class="itable vtable"><tr><td class="ik">Saved</td><td class="ik">By</td><td class="ik">Size</td></tr>${rows}</table></details>` : ''}
      ${c.note ? `<p class="helper">${esc(c.note)}</p>` : ''}
    </div>`;
  }
  function historySummary(c) {
    if (!c) return '';
    const v = (c.versions || []).filter(x => x.when).sort((a, b) => new Date(a.when) - new Date(b.when));
    const days = new Set(v.map(x => new Date(x.when).toDateString())).size;
    return [`Online document (${c.source}). Owner: ${c.owner || 'unknown'}. Created ${c.created || '?'}, last edited ${c.modified || '?'}${c.lastEditor ? ' by ' + c.lastEditor : ''}.`,
      v.length ? `Version history: ${v.length} versions over ${days} day(s), from ${v[0].when} to ${v[v.length - 1].when}; editors: ${[...new Set(v.map(x => x.who).filter(Boolean))].join(', ') || 'unknown'}.` : 'Version history not available.'].join('\n');
  }

  window.BTECCloud = {
    detect,
    configured: p => !!((p === 'google' ? window.BTEC_GOOGLE_CLIENT_ID : window.BTEC_MS_CLIENT_ID) || '').trim(),
    fetch: link => link.provider === 'google' ? fetchGoogle(link) : fetchMicrosoft(link),
    historyFlags, historyHtml, historySummary,
  };
})();

/* ===== main ===== */

// ═════════════════════════ Toast messages (Bootstrap) ═════════════════════════
function toast(msg, kind = 'info') {
  const area = SMDOC.getElementById('toastArea');
  if (!area) { console.log(msg); return; }
  const t = document.createElement('div');
  const tone = { error: 'text-bg-danger', ok: 'text-bg-success', warn: 'text-bg-warning', info: 'text-bg-dark' }[kind] || 'text-bg-dark';
  t.className = `toast align-items-center border-0 ${tone} show`;
  t.setAttribute('role', kind === 'error' ? 'alert' : 'status');
  t.innerHTML = '<div class="d-flex"><div class="toast-body"></div><button type="button" class="btn-close btn-close-white me-2 m-auto" aria-label="Close"></button></div>';
  t.querySelector('.toast-body').textContent = msg;
  t.querySelector('button').onclick = () => t.remove();
  area.appendChild(t);
  setTimeout(() => t.remove(), kind === 'error' ? 9000 : 5000);
}

// ═════════════════════════ Subject, level & unit selection ═════════════════════════
const subjectSelect = SMDOC.getElementById('subjectSelect');
const levelSelect = SMDOC.getElementById('levelSelect');
const unitSelect = SMDOC.getElementById('unitSelect');

if (!window.BTEC_DATA || !window.BTEC_DATA.length) {
  const warn = document.createElement('div');
  warn.className = 'info-box';
  warn.style.cssText = 'border-left-color:var(--no);color:var(--no);margin:0 0 1rem';
  warn.innerHTML = '<strong>units.js not loaded.</strong> Keep index.html, units.js, template.js and keys.js in the same folder (extract the whole zip first), or use the single-file btec-checker.html instead.';
  SMDOC.querySelector('.page').insertBefore(warn, SMDOC.querySelector('.step'));
}
(window.BTEC_DATA || []).forEach((subj, i) => {
  const opt = document.createElement('option');
  opt.value = i;
  opt.textContent = subj.subject;
  subjectSelect.appendChild(opt);
});

function onSubjectChange() {
  const si = subjectSelect.value;
  levelSelect.innerHTML = '<option value="">Select a level...</option>';
  unitSelect.innerHTML = '<option value="">Select a unit...</option>';
  unitSelect.disabled = true;
  SMDOC.getElementById('programmeSelect').style.display = 'none';
  if (typeof renderBuiltinBriefs === 'function') renderBuiltinBriefs();
  if (si === '') { levelSelect.disabled = true; return; }
  const subject = window.BTEC_DATA[parseInt(si)];
  subject.levels.forEach((lvl, i) => {
    const opt = document.createElement('option');
    opt.value = i;
    opt.textContent = lvl.level;
    levelSelect.appendChild(opt);
  });
  levelSelect.disabled = false;
}

function onLevelChange() {
  const si = subjectSelect.value, li = levelSelect.value;
  unitSelect.innerHTML = '<option value="">Select a unit...</option>';
  selectedUnits = []; SMDOC.getElementById('combineWrap').style.display = 'none'; SMDOC.getElementById('combineSummary').textContent = '';
  if (typeof renderBuiltinBriefs === 'function') renderBuiltinBriefs();
  if (si === '' || li === '') { unitSelect.disabled = true; return; }
  const level = window.BTEC_DATA[parseInt(si)].levels[parseInt(li)];
  const progs = level.programmes || (level.programme ? [level.programme] : []);
  const progSel = SMDOC.getElementById('programmeSelect');
  progSel.innerHTML = '';
  progs.forEach(p => { const o = document.createElement('option'); o.value = p; o.textContent = p; progSel.appendChild(o); });
  progSel.style.display = progs.length > 1 ? 'block' : 'none';
  SMDOC.getElementById('programme_title').style.display = progs.length > 1 ? 'none' : '';
  if (progs.length) SMDOC.getElementById('programme_title').value = progs[0];
  let groupEl = null, lastGroup;
  level.units.forEach((u, i) => {
    const opt = document.createElement('option');
    opt.value = i;
    opt.textContent = u.title + (u.optional ? ' (optional)' : '');
    if (u.group) {
      if (u.group !== lastGroup) { groupEl = document.createElement('optgroup'); groupEl.label = u.group; unitSelect.appendChild(groupEl); lastGroup = u.group; }
      groupEl.appendChild(opt);
    } else unitSelect.appendChild(opt);
  });
  unitSelect.disabled = false;
}

// ── units: one, or several combined into one assignment ──
let selectedUnits = [];   // indexes into the level's units; the first is the one in the dropdown
const cleanTitle = t => String(t || '').replace(/\s*\(Optional\)\s*$/i, '').trim();
function unitTag(unit, i) { const t = unit.title || ''; const c = t.match(/^([A-Z]{2,6}\d+)\s*:/); if (c) return c[1]; const m = t.match(/Unit\s+(\d+)/i); return 'U' + (m ? m[1] : i + 1); }
const tagName = t => /^U\d+$/.test(t) ? t.replace('U', 'Unit ') : t;
function splitUnit(code) { const m = String(code || '').match(/^(U\d+|[A-Z]{2,6}\d+)\s+(.*\..*)$/); return m ? { unit: m[1], code: m[2] } : { unit: '', code: String(code || '') }; }

function onUnitChange() {
  const ui = unitSelect.value;
  if (!currentLevel() || ui === '') return;
  selectedUnits = [parseInt(ui, 10)];
  buildCombineList();
  applyUnits();
}
function applyUnits() {
  const level = currentLevel();
  if (!level || !selectedUnits.length) return;
  const units = selectedUnits.map(i => level.units[i]);
  const multi = units.length > 1;
  SMDOC.getElementById('unit_title').value = units.map(u => cleanTitle(u.title)).join(' / ');
  const prev = new Map(pick.map(p => [p.code, p.on]));
  const list = [];
  units.forEach((u, k) => {
    const tag = unitTag(u, selectedUnits[k]);
    (u.criteria || []).forEach(c => {
      const code = multi ? `${tag} ${c.code}` : c.code;
      const keys = [code, c.code, `${tag} ${c.code}`];
      const hit = keys.find(k => prev.has(k));
      const was = hit !== undefined ? prev.get(hit) : true;
      list.push({ code, desc: c.desc || '', on: was, unit: tag, unitTitle: cleanTitle(u.title) });
    });
  });
  setPick(list);
  const sum = SMDOC.getElementById('combineSummary');
  sum.textContent = multi ? `Combined: ${units.map((u, k) => tagName(unitTag(u, selectedUnits[k]))).join(' + ')}. Codes are prefixed with the unit, e.g. ${list[0] ? list[0].code : ''}.` : '';
  if (typeof renderBuiltinBriefs === 'function') renderBuiltinBriefs();
}
function buildCombineList() {
  const level = currentLevel();
  const wrap = SMDOC.getElementById('combineWrap'), box = SMDOC.getElementById('combineList');
  if (!level || level.units.length < 2 || !selectedUnits.length) { wrap.style.display = 'none'; return; }
  wrap.style.display = 'flex';
  box.innerHTML = '';
  let lastG;
  level.units.forEach((u, i) => {
    if (u.group && u.group !== lastG) { lastG = u.group; const h = document.createElement('div'); h.className = 'combine-group'; h.textContent = u.group; box.appendChild(h); }
    const lab = document.createElement('label');
    const primary = i === selectedUnits[0];
    lab.className = primary ? 'primary' : '';
    lab.innerHTML = `<input class="form-check-input" type="checkbox" ${selectedUnits.includes(i) ? 'checked' : ''} ${primary ? 'disabled' : ''}><span></span>`;
    lab.querySelector('span').textContent = cleanTitle(u.title) + (primary ? ' (selected above)' : '');
    lab.querySelector('input').onchange = e => {
      if (e.target.checked) selectedUnits.push(i); else selectedUnits = selectedUnits.filter(x => x !== i);
      const first = selectedUnits[0];
      selectedUnits = [first, ...selectedUnits.slice(1).sort((a, b) => a - b)];
      applyUnits();
    };
    box.appendChild(lab);
  });
}
function toggleCombine() {
  const box = SMDOC.getElementById('combineList'), b = SMDOC.getElementById('combineToggle');
  const open = box.style.display === 'none';
  box.style.display = open ? 'grid' : 'none';
  b.textContent = open ? '− Hide unit list' : '+ Combine with other units';
}

// ═════════════════════════ File upload ═════════════════════════
let fileBase64 = null, fileMediaType = null, fileNameStr = null;
let brief = null;            // {name, kind, base64}: the assignment brief, kept for the next learner
let lastAiIntegrity = null;  // the AI's integrity view, kept so a restored draft shows it
function newResub() { return { on: false, type: 'Resubmission', prev: {}, comments: {}, general: '', header: {}, recordName: '', recordBuffer: null, warning: '' }; }
let resub = newResub();
let lastFeedback = '', lastResults = [], lastAnnotations = [];
let aiRunId = '', aiRunModel = '';   // which second marker check the agreement log is comparing against
let aiOverall = '', aiNextSteps = '', feedbackEdited = false;
const FEEDBACK_BOX_HTML = `<div class="feedback-box" id="feedbackBox" contenteditable="true" role="textbox" aria-multiline="true" aria-labelledby="fbTitle" data-ph="The AI's feedback appears here after the check: an overall comment on the assignment, then what's needed for each criterion not yet met. You can also write your own." data-sm-input="lastFeedback = this.innerText; feedbackEdited = true; showRebuild()" data-sm-blur="if (/[–—]/.test(this.innerText)) { this.innerText = noDashes(this.innerText); lastFeedback = this.innerText; }"></div>
        <div class="fb-tools"><span class="helper" style="margin:0"><span class="not-iv">Updates automatically as you change Y/N/R, until you edit it yourself. This goes into General comments on export.</span><span class="iv-only">Updates as you decide, until you edit it yourself. This goes into Internal Verifier Comments on the IV record.</span></span><button class="btn btn-link btn-sm linkish" id="rebuildFb" style="display:none" data-sm-click="feedbackEdited = false; composeFeedback(true)">Rebuild from my decisions</button></div>`;

// Feedback = overall comment + what's needed for each criterion the assessor has marked N or R
function composeFeedback(force) {
  const box = SMDOC.getElementById('feedbackBox');
  if (!box || (feedbackEdited && !force) || (!aiOverall && !lastResults.length)) return;
  const rows = getCriteria().filter(c => c.code);
  const aiFor = code => lastResults.find(r => r.code.toLowerCase() === code.toLowerCase());
  const missed = rows.filter(r => r.achieved ? r.achieved !== 'Y' : !(aiFor(r.code) || {}).met);
  const parts = [];
  if (aiOverall) parts.push(aiOverall.trim());
  if (missed.length) {
    parts.push('Criteria not yet achieved:\n' + missed.map(r => {
      const ai = aiFor(r.code) || {};
      const line = ai.improve || ai.comment || r.comment || '';
      const tags = [r.achieved === 'R' ? 'resubmission' : '', ai.attempt === 'partial' ? 'part achieved' : ''].filter(Boolean);
      return `${r.code}${tags.length ? ` (${tags.join(', ')})` : ''}: ${line}`.trim();
    }).join('\n'));
  } else if (rows.length) {
    parts.push('All targeted criteria have been achieved.' + (aiNextSteps ? ' ' + aiNextSteps.trim() : ''));
  }
  const text = noDashes(parts.join('\n\n'));
  box.innerText = text;
  lastFeedback = text;
  showRebuild();
}
let staffEdited = false;
// One line for colleagues: current grade and what's still outstanding, from the assessor's decisions
function composeStaffNote(force) {
  const box = SMDOC.getElementById('staffNote');
  if (!box || (staffEdited && !force)) return;
  const rows = getCriteria().filter(c => c.code);
  if (!rows.length || rows.some(r => !r.achieved)) { if (!staffEdited) box.value = ''; showStaffRebuild(); return; }
  const g = gradeOf(rows, r => r.achieved === 'Y');
  const aiFor = code => lastResults.find(r => r.code.toLowerCase() === code.toLowerCase()) || {};
  const missed = rows.filter(r => r.achieved !== 'Y');
  const resubCodes = rows.filter(r => r.achieved === 'R').map(r => r.code);
  const gradeText = g.grade === 'U' && !g.perUnit ? 'Not yet achieved (Pass criteria outstanding)' : g.label;
  let text;
  if (!missed.length) text = `${gradeText}. All targeted criteria achieved, no further action needed.`;
  else {
    const needs = missed.map(r => {
      const need = (aiFor(r.code).need || '').replace(/\.$/, '').trim();
      return need ? `${r.code} ${need.charAt(0).toLowerCase() + need.slice(1)}` : r.code;
    });
    text = `Currently ${gradeText}. Still needs to: ${needs.join('; ')}.`;
    if (resubCodes.length) text += ` Resubmission agreed for ${resubCodes.join(', ')}.`;
  }
  const late = submissionStatus();
  const lateBit = late.state === 'late' ? `Handed in ${plural(late.days, 'day')} late, no extension. ` : late.state === 'ext-late' ? `Handed in ${plural(late.days, 'day')} after the agreed extension. ` : late.state === 'ext-ontime' ? 'Handed in within an agreed extension. ' : '';
  box.value = noDashes((resub.on ? resub.type + '. ' : '') + lateBit + text);
  showStaffRebuild();
}
function showStaffRebuild() {
  const b = SMDOC.getElementById('rebuildStaff');
  if (b) b.style.display = staffEdited ? '' : 'none';
}
function showRebuild() {
  const b = SMDOC.getElementById('rebuildFb');
  if (b) b.style.display = feedbackEdited && (aiOverall || lastResults.length) ? '' : 'none';
}

// No em or en dashes in anything written for the learner
function noDashes(t) {
  return String(t ?? '')
    .replace(/(\d)\s*[–—]\s*(\d)/g, '$1-$2')
    .replace(/\s*[—–]\s*/g, ', ')
    .replace(/,\s*,/g, ',').replace(/,\s*([.!?:;])/g, '$1')
    .replace(/^,\s*/gm, '');
}
let templateBuffer = null;

// Load the embedded Pearson template (template.js) as the default
if (window.BTEC_TEMPLATE_B64) {
  const bin = atob(window.BTEC_TEMPLATE_B64);
  const bytes = new Uint8Array(bin.length);
  for (let i = 0; i < bin.length; i++) bytes[i] = bin.charCodeAt(i);
  templateBuffer = bytes.buffer;
}

const dropZone = SMDOC.getElementById('dropZone');
const fileInput = SMDOC.getElementById('fileInput');
dropZone.onclick = () => fileInput.click();
fileInput.onchange = e => handleFile(e.target.files[0]);
dropZone.ondragover = e => { e.preventDefault(); dropZone.style.background = '#f0f0eb'; };
dropZone.ondragleave = () => { dropZone.style.background = ''; };
dropZone.ondrop = e => { e.preventDefault(); dropZone.style.background = ''; handleFile(e.dataTransfer.files[0]); };

let lastIntegrity = null;
let integrityReady = Promise.resolve();   // resolves once the file checks are done, so the AI gets them

// cloud: version history from a Google / Microsoft link (see cloud.js), or null for uploads
function handleFile(file, cloud) {
  if (!file) return;
  if (!cloud) { const dl = SMDOC.getElementById('docLink'); if (dl) dl.value = ''; setLinkStatus(''); }
  const isPdf = file.type === 'application/pdf' || /\.pdf$/i.test(file.name);
  const isDocx = /\.docx$/i.test(file.name);
  if (/\.doc$/i.test(file.name)) { toast('Old .doc files are not supported. Ask for the work as .docx or PDF.', 'error'); return; }
  if (!isPdf && !isDocx) { toast('Please upload a PDF or Word (.docx) document.', 'error'); return; }
  lastIntegrity = null;
  renderIntegrity(null);
  integrityReady = file.arrayBuffer()
    .then(buf => analyseSubmission(file, buf, SMDOC.getElementById('date_submitted').value.trim()))
    .then(r => {
      if (cloud && window.BTECCloud) {
        r.cloud = cloud;
        r.flags = r.flags.concat(BTECCloud.historyFlags(cloud, parseInt(r.meta.words, 10) || r.wordCount || 0, SMDOC.getElementById('date_submitted').value.trim()));
        const order = { high: 0, medium: 1, info: 2 };
        r.flags.sort((a, b) => order[a.level] - order[b.level]);
      }
      return r;
    })
    .then(r => { lastIntegrity = r; lastAiIntegrity = null; renderIntegrity(r); setProgress('pIntegrity', 'ok'); renderLateStatus(); maybeAdvanceAfterUpload(); scheduleSave(); })
    .catch(err => {
      const el = SMDOC.getElementById('integrityCard');
      el.style.display = 'block';
      el.innerHTML = `<div class="label">Submission integrity</div><p class="helper" style="color:var(--no)">Couldn't read the file's details: ${escapeHtml(err.message)}</p>`;
      setProgress('pIntegrity', 'err'); integrityFailed = true; maybeAdvanceAfterUpload();
    });
  integrityReady = integrityReady.catch(() => {});
  fileMediaType = isPdf ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
  fileNameStr = file.name;
  const reader = new FileReader();
  reader.onload = ev => {
    fileBase64 = ev.target.result.split(',')[1];
    dropZone.classList.add('has-file');
    SMDOC.getElementById('dropIcon').innerHTML = '<svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>';
    SMDOC.getElementById('dropText').textContent = file.name;
    startFileScan();
    onFileReady();
  };
  reader.readAsDataURL(file);
  resetAi();
}

async function docxToText(b64) {
  const pkg = await openDocx(b64, { base64: true });
  const xml = await pkg.main();
  return xml
    .replace(/<w:tab\/>/g, '\t')
    .replace(/<w:br[^>]*\/>/g, '\n')
    .replace(/<\/w:p>/g, '\n')
    .replace(/<[^>]+>/g, '')
    .replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"').replace(/&apos;/g, "'").replace(/&amp;/g, '&')
    .replace(/\n{3,}/g, '\n\n').trim();
}

// Link option is hidden until BTEC_ENABLE_LINKS is set to true in keys.js
const linksEnabled = () => window.BTEC_ENABLE_LINKS === true;
__on('DOMContentLoaded', () => { const f = SMDOC.getElementById('linkFeature'); if (f) f.style.display = linksEnabled() ? '' : 'none'; });
function setLinkStatus(msg, kind) {
  const el = SMDOC.getElementById('linkStatus');
  if (!el) return;
  el.innerHTML = msg || "You'll sign in with an account that can open the document. Reads the work and its version history.";
  el.style.color = kind === 'err' ? 'var(--no)' : kind === 'ok' ? 'var(--yes)' : '';
}
async function fetchFromLink() {
  if (!linksEnabled()) return;
  const url = SMDOC.getElementById('docLink').value.trim();
  if (!url) { setLinkStatus('Paste a link first.', 'err'); return; }
  const link = BTECCloud.detect(url);
  if (!link) { setLinkStatus("That doesn't look like a web link.", 'err'); return; }
  if (link.provider === 'unknown') { setLinkStatus('Only Google Docs/Drive and OneDrive/SharePoint/Word online links are supported. For anything else, download the file and upload it.', 'err'); return; }
  if (location.protocol === 'file:') { setLinkStatus('Links only work when the app is opened from a web address (sign-in is blocked for files opened from your computer). See "Linking to online documents" in the README.', 'err'); return; }
  const name = link.provider === 'google' ? 'Google' : 'Microsoft';
  if (!BTECCloud.configured(link.provider)) { setLinkStatus(`${name} linking isn't set up yet. Add the ${name} client ID to keys.js (see README). Until then, download the file and upload it.`, 'err'); return; }
  const btn = SMDOC.getElementById('linkBtn');
  btn.disabled = true; btn.textContent = 'Opening...';
  setLinkStatus(`Signing in to ${name} and reading the document...`);
  try {
    const { file, cloud } = await BTECCloud.fetch(link);
    setLinkStatus(`Opened "${escapeHtml(cloud.name)}" from ${escapeHtml(cloud.source)} with ${cloud.versions.length || 'no'} saved version${cloud.versions.length === 1 ? '' : 's'}.`, 'ok');
    handleFile(file, cloud);
  } catch (e) {
    setLinkStatus(escapeHtml(e.message), 'err');
  }
  btn.disabled = false; btn.textContent = 'Open link';
}

function handleTemplate(file) {
  if (!file) return;
  if (!file.name.match(/\.docx$/i)) { toast('Please upload the .docx template.', 'error'); return; }
  const reader = new FileReader();
  reader.onload = ev => {
    templateBuffer = ev.target.result;
    SMDOC.getElementById('templateStatus').innerHTML = `<span class="tick">✓</span> ${file.name} loaded (replacing built-in)`;
  };
  reader.readAsArrayBuffer(file);
}

// ═════════════════════════ Criteria rows ═════════════════════════
function updateSelColor(sel) {
  sel.className = 'achieved-sel val-' + sel.value;
  const row = sel.closest('.crit-row'); if (row) refreshRow(row);
}

let gradingUnlocked = false;

const STAMP_WORDS = { Y: 'Achieved', N: 'Not yet', R: 'Resub' };
function addRow(code='', desc='', achieved='', comment='') {
  const list = SMDOC.getElementById('criteriaBody');
  const row = document.createElement('div');
  row.className = 'crit-row';
  row.dataset.desc = desc;
  row.innerHTML = `<div class="crit-top"><input class="code-input" type="text" aria-label="Criterion code" spellcheck="false" placeholder="A.P1"><span class="band-tag"></span></div>
    <div class="crit-desc"></div>
    <div class="ai-cell"><span class="sugg idle">Not checked yet</span></div>
    <div class="stamps" role="group">${['Y', 'N', 'R'].map(v => `<button type="button" class="stamp" data-v="${v}" aria-pressed="false"><b>${v}</b><small>${STAMP_WORDS[v]}</small></button>`).join('')}</div>
    <select class="achieved-sel val-${achieved}" tabindex="-1" aria-hidden="true" data-sm-change="updateSelColor(this); updateGrade()"><option value="">Select</option><option value="Y">Y: Yes</option><option value="N">N: No</option><option value="R">R: Resub</option></select>
    <div class="row-more"><textarea class="desc-input" rows="2" placeholder="Assessment comment for the record (the AI fills this in, edit freely)"></textarea><button type="button" class="btn-remove" data-sm-click="removeRow(this)">Remove criterion</button></div>`;
  const ci = row.querySelector('.code-input'); ci.value = code;
  row.querySelector('.crit-desc').textContent = desc;
  const band = bandOf(code), bt = row.querySelector('.band-tag');
  bt.textContent = band ? bandLabel(band) : ''; bt.className = 'band-tag band-' + (band || '');
  row.querySelector('.stamps').setAttribute('aria-label', `Decision for ${code || 'this criterion'}`);
  row.querySelector('.desc-input').setAttribute('aria-label', `Assessment comment for ${code || 'this criterion'}`);
  const sel = row.querySelector('.achieved-sel');
  sel.value = achieved; sel.disabled = !gradingUnlocked;
  row.querySelector('.desc-input').value = comment;
  row.querySelectorAll('.stamp').forEach(b => b.addEventListener('click', e => { e.stopPropagation(); stampRow(row, b.dataset.v, isFocus()); }));
  row.addEventListener('click', e => { if (!e.target.closest('textarea, input, button')) setCurrentRow(row, false); });
  // a stamp press selects the row itself after the click, so the row doesn't grow under the pointer
  row.addEventListener('focusin', e => { if (!row.classList.contains('current') && !e.target.closest('.stamp')) setCurrentRow(row, false); });
  list.appendChild(row);
  refreshRow(row);
  updateGrade();
}
function removeRow(btn) { btn.closest('.crit-row').remove(); updateGrade(); }

function getCriteria() {
  return Array.from(SMDOC.querySelectorAll('#criteriaBody .crit-row')).map(tr => ({
    code: tr.querySelector('.code-input').value.trim(),
    desc: tr.dataset.desc || '',
    achieved: tr.querySelector('.achieved-sel').value,
    comment: tr.querySelector('.desc-input').value.trim()
  })).filter(c => c.code || c.desc || c.comment);
}

function unlockGrading(manual) {
  gradingUnlocked = true;
  SMDOC.querySelectorAll('.achieved-sel').forEach(s => s.disabled = false);
  refreshDesk();
  SMDOC.getElementById('skipAiBtn').style.display = 'none';
  if (manual) SMDOC.getElementById('gradeNote').textContent = 'Marking it yourself. You can ask the second marker at any point.';
  updateGrade();
}

// BTEC unit grading: Pass needs every targeted P met; Merit adds every M; Distinction adds every D.
// Bands: A.P1 / 2A.P1 → P, M, D;  1A.1 (BTEC First Level 1) → L1;  1.1 (TLM "I can" statement) → P
function bandOf(code) {
  const c = splitUnit((code || '').trim()).code;
  const m = c.match(/\.([PMD])\d+/i);
  if (m) return m[1].toUpperCase();
  if (/^1[A-Z]{1,4}\.\d+$/i.test(c)) return 'L1';
  if (/^\d+\.\d+$/.test(c)) return 'P';
  return null;
}
function computeGrade(rows, isMet) {
  const by = { L1: [], P: [], M: [], D: [] };
  rows.forEach(r => { const b = bandOf(r.code); if (b) by[b].push(r); });
  if (!by.L1.length && !by.P.length && !by.M.length && !by.D.length) return { grade: 'X', label: 'No criteria' };
  const all = arr => arr.every(isMet);
  const pre = by.L1.length ? 'Level 2 ' : '';
  const level1 = () => by.L1.length && all(by.L1) ? { grade: 'L1', label: 'Level 1' } : { grade: 'U', label: 'Not yet achieved' };
  if (!by.P.length) return level1();
  if (!all(by.P)) return level1();
  if (!by.M.length || !all(by.M)) return { grade: 'P', label: pre + 'Pass' };
  if (!by.D.length || !all(by.D)) return { grade: 'M', label: pre + 'Merit' };
  return { grade: 'D', label: pre + 'Distinction' };
}
// Each unit is graded on its own criteria; combined assignments show one grade per unit
const GRADE_RANK = { X: -1, U: 0, L1: 1, P: 2, M: 3, D: 4 };
function gradeOf(rows, isMet) {
  const groups = new Map();
  rows.forEach(r => { const u = splitUnit(r.code).unit; if (!groups.has(u)) groups.set(u, []); groups.get(u).push(r); });
  if (groups.size <= 1) return computeGrade(rows, isMet);
  const parts = [...groups.entries()].map(([u, rs]) => ({ u, g: computeGrade(rs, isMet) }));
  const worst = parts.reduce((a, b) => (GRADE_RANK[b.g.grade] < GRADE_RANK[a.g.grade] ? b : a));
  return { grade: worst.g.grade, label: parts.map(p => `${tagName(p.u)}: ${p.g.label}`).join(', '), perUnit: parts };
}
function currentLevel() {
  const si = SMDOC.getElementById('subjectSelect').value, li = SMDOC.getElementById('levelSelect').value;
  return si !== '' && li !== '' ? window.BTEC_DATA[+si].levels[+li] : null;
}

function updateGrade() {
  const pill = SMDOC.getElementById('assessorGrade');
  if (!pill) return;
  const rows = getCriteria();
  const pending = rows.filter(r => !r.achieved).length;
  if (!rows.length) { pill.className = 'grade-big g-X'; pill.textContent = 'No criteria'; return; }
  if (pending) { pill.className = 'grade-big g-X'; pill.textContent = `${pending} to decide`; return; }
  const g = gradeOf(rows, r => r.achieved === 'Y');
  pill.className = 'grade-big g-' + g.grade;
  pill.textContent = g.label;
}

function getHeaderData() {
  const ids = ['programme_title','unit_title','assignment_title','assessor_name','student_name','student_reg','student_email','deadline','date_submitted','extension'];
  const out = {};
  ids.forEach(id => out[id] = SMDOC.getElementById(id).value.trim());
  const extDate = (SMDOC.getElementById('extension_date') || {}).value || '';
  if (out.extension === 'Yes' && extDate.trim()) out.extension = `Yes, to ${extDate.trim()}`;
  return out;
}


// ═════════════════════════ CSV parsing ═════════════════════════
function parseCsv(text) {
  const rows = [];
  let row = [], cell = '', inQuotes = false;
  for (let i = 0; i < text.length; i++) {
    const ch = text[i];
    if (inQuotes) {
      if (ch === '"') { if (text[i+1] === '"') { cell += '"'; i++; } else inQuotes = false; }
      else cell += ch;
    } else {
      if (ch === '"') inQuotes = true;
      else if (ch === ',') { row.push(cell); cell = ''; }
      else if (ch === '\n' || ch === '\r') {
        if (ch === '\r' && text[i+1] === '\n') i++;
        row.push(cell); cell = '';
        if (row.some(c => c.trim() !== '')) rows.push(row);
        row = [];
      } else cell += ch;
    }
  }
  row.push(cell);
  if (row.some(c => c.trim() !== '')) rows.push(row);
  return rows;
}

function normaliseHeader(h) {
  return h.trim().toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
}

let csvPresets = [];
const FIELD_IDS = ['programme_title','unit_title','assignment_title','assessor_name','student_name','student_reg','student_email','deadline','date_submitted','extension'];
const HEADER_ALIASES = {
  programme: 'programme_title', programme_title: 'programme_title', program: 'programme_title',
  unit: 'unit_title', unit_title: 'unit_title', unit_number_and_title: 'unit_title',
  assignment: 'assignment_title', assignment_title: 'assignment_title',
  assessor: 'assessor_name', assessor_name: 'assessor_name',
  student_name: 'student_name', student: 'student_name', name: 'student_name',
  student_email: 'student_email', email: 'student_email', email_address: 'student_email', e_mail: 'student_email',
  student_reg: 'student_reg', student_registration_number: 'student_reg', registration: 'student_reg', reg: 'student_reg',
  deadline: 'deadline',
  date_submitted: 'date_submitted', submitted: 'date_submitted',
  extension: 'extension', extension_approved: 'extension'
};

function handleCsv(file) {
  if (!file) return;
  const reader = new FileReader();
  reader.onload = ev => {
    const rows = parseCsv(ev.target.result);
    if (rows.length < 2) { toast('CSV needs a header row plus at least one data row.', 'error'); return; }
    const headers = rows[0].map(h => HEADER_ALIASES[normaliseHeader(h)] || null);
    if (!headers.some(Boolean)) {
      toast('No recognised columns found. Use headers like: programme_title, unit_title, assignment_title, assessor_name, student_reg, deadline, date_submitted, extension', 'error');
      return;
    }
    csvPresets = rows.slice(1).map(r => {
      const preset = {};
      headers.forEach((field, i) => { if (field && r[i] !== undefined) preset[field] = r[i].trim(); });
      return preset;
    }).filter(p => Object.values(p).some(v => v));

    const sel = SMDOC.getElementById('presetSelect');
    sel.innerHTML = '<option value="">Select a row to autofill...</option>';
    csvPresets.forEach((p, i) => {
      const label = [p.unit_title, p.assignment_title, p.programme_title].filter(Boolean).join(', ') || `Row ${i+1}`;
      const opt = document.createElement('option');
      opt.value = i; opt.textContent = label;
      sel.appendChild(opt);
    });
    sel.style.display = 'inline-block';
    SMDOC.getElementById('csvStatus').textContent = `${csvPresets.length} row${csvPresets.length===1?'':'s'} loaded from ${file.name}`;
    if (csvPresets.length === 1) { sel.value = '0'; applyPreset('0'); }
    SMDOC.getElementById('csvInput').value = '';
  };
  reader.readAsText(file);
}

function applyPreset(idx) {
  if (idx === '' || idx === null) return;
  const preset = csvPresets[parseInt(idx)];
  if (!preset) return;
  FIELD_IDS.forEach(id => { if (preset[id] !== undefined) SMDOC.getElementById(id).value = preset[id]; });
  emailManual = !!(preset.student_email || '').trim(); autoEmail();
}

const CRITERIA_ALIASES = {
  code: 'code', targeted_criteria: 'code', criteria: 'code', criterion: 'code',
  achieved: 'achieved', criteria_achieved: 'achieved', status: 'achieved', grade: 'achieved',
  desc: 'desc', description: 'desc', criterion_description: 'desc', wording: 'desc',
  comment: 'comment', comments: 'comment', assessment_comments: 'comment', assessment_comment: 'comment'
};

function normaliseAchieved(v) {
  const s = (v || '').trim().toUpperCase();
  if (s.startsWith('Y')) return 'Y';
  if (s.startsWith('N')) return 'N';
  if (s.startsWith('R')) return 'R';
  return '';
}

function handleCriteriaCsv(file) {
  if (!file) return;
  const reader = new FileReader();
  reader.onload = ev => {
    const rows = parseCsv(ev.target.result);
    if (rows.length < 2) { toast('CSV needs a header row plus at least one data row.', 'error'); return; }
    const headers = rows[0].map(h => CRITERIA_ALIASES[normaliseHeader(h)] || null);
    if (!headers.includes('code')) {
      toast('No criteria code column found. Use headers like: code (or targeted_criteria), achieved, comment', 'error');
      return;
    }
    const imported = rows.slice(1).map(r => {
      const item = {};
      headers.forEach((field, i) => { if (field && r[i] !== undefined) item[field] = r[i].trim(); });
      return item;
    }).filter(item => item.code);
    if (imported.length === 0) { toast('No criteria rows found in the CSV.', 'error'); return; }
    SMDOC.getElementById('criteriaBody').innerHTML = '';
    imported.forEach(item => addPick({ code: item.code, desc: item.desc || '', on: true }));
    SMDOC.getElementById('criteriaCsvStatus').textContent = `${imported.length} criteri${imported.length===1?'on':'a'} loaded from ${file.name}`;
    SMDOC.getElementById('criteriaCsvInput').value = '';
  };
  reader.readAsText(file);
}

// ═════════════════════════ API key ═════════════════════════
// Priority: keys.js (window.BTEC_API_KEY) → key entered in Settings.
// Browser storage can be blocked (preview panes, private windows, strict settings),
// so every access is guarded and the key falls back to memory for this session.
let memKey = '';
let keyPersisted = false;
const ccKey = k => k === 'btec_theme' ? k : k + CCNS;
function storeGet(k) { k = ccKey(k); try { return window.localStorage.getItem(k); } catch (e) { return null; } }
function storeSet(k, v) { k = ccKey(k); try { window.localStorage.setItem(k, v); return window.localStorage.getItem(k) === v; } catch (e) { return false; } }
function storeDel(k) { k = ccKey(k); try { window.localStorage.removeItem(k); } catch (e) {} }

function fileKey() { return (window.BTEC_API_KEY || '').trim(); }
function getApiKey() { return fileKey() || memKey || (storeGet('anthropic_key') || '').trim(); }

function saveKey() {
  const input = SMDOC.getElementById('apiKeyInput');
  const key = input.value.trim().replace(/^["']|["']$/g, '');
  if (!key) { setKeyStatus('Paste a key first.', 'err'); input.focus(); return; }
  if (!/^sk-ant-/.test(key) && !confirm("That doesn't look like an Anthropic key (they start with sk-ant-). Save it anyway?")) return;
  memKey = key;
  keyPersisted = storeSet('anthropic_key', key);
  input.value = '';
  updateKeyStatus();
  toast(keyPersisted ? 'API key saved in this browser.' : 'API key set for this session.', 'ok');
  toggleSettings(false);
  // if an upload was waiting on the key, carry on
  if (fileBase64 && aiState === 'error') runCheck();
}
function clearKey() {
  memKey = ''; keyPersisted = false; storeDel('anthropic_key');
  SMDOC.getElementById('apiKeyInput').value = '';
  updateKeyStatus();
}
function toggleKeyVisible() {
  const i = SMDOC.getElementById('apiKeyInput'), b = SMDOC.getElementById('keyShowBtn');
  i.type = i.type === 'password' ? 'text' : 'password';
  b.textContent = i.type === 'password' ? 'Show' : 'Hide';
}
function setKeyStatus(html, kind) {
  const el = SMDOC.getElementById('keyStatus');
  el.innerHTML = html;
  el.style.color = kind === 'err' ? 'var(--no)' : kind === 'warn' ? 'var(--resub)' : '';
}
function updateKeyStatus() {
  const k = getApiKey();
  const masked = k ? `${k.slice(0, 10)}…${k.slice(-4)}` : '';
  if (fileKey()) setKeyStatus(`<span class="tick">✓</span> Using the key in keys.js (${escapeHtml(masked)}). Edit keys.js to change it.`);
  else if (k && (keyPersisted || storeGet('anthropic_key'))) setKeyStatus(`<span class="tick">✓</span> Key saved in this browser (${escapeHtml(masked)}).`);
  else if (k) setKeyStatus(`<span class="tick">✓</span> Key set for this session only (${escapeHtml(masked)}). This browser won't let the page store it, so you'll need to paste it again next time, or put it in keys.js.`, 'warn');
  else setKeyStatus('No key set. Paste it above and press Save key.', 'warn');
  const banner = SMDOC.getElementById('keyBanner');
  if (banner) banner.style.display = k ? 'none' : 'flex';
}
function openKeySettings() {
  toggleSettings(true);
  setTimeout(() => SMDOC.getElementById('apiKeyInput').focus(), 300);
}
updateKeyStatus();


// ═════════════════════════ Reference marked work (calibration) ═════════════════════════
// Kept for the session (and across "Next learner"); never sent anywhere except with the AI check.
const MAX_REFS = 3, MAX_REF_MB = 15;
let references = [];   // [{name, kind, base64, grade, notes}]
function addReferences(fileList) {
  const files = [...fileList];
  for (const f of files) {
    if (references.length >= MAX_REFS) { toast(`Up to ${MAX_REFS} reference pieces. Remove one to add another.`, 'error'); break; }
    const isPdf = f.type === 'application/pdf' || /\.pdf$/i.test(f.name), isDocx = /\.docx$/i.test(f.name);
    if (!isPdf && !isDocx) { toast(`${f.name}: only PDF or Word (.docx) files can be used.`, 'error'); continue; }
    if (f.size > MAX_REF_MB * 1048576) { toast(`${f.name} is over ${MAX_REF_MB} MB. Use a smaller copy (e.g. save as PDF without large images).`, 'error'); continue; }
    const ref = { name: f.name, kind: isPdf ? 'pdf' : 'docx', base64: null, grade: '', notes: '' };
    references.push(ref);
    const r = new FileReader();
    r.onload = ev => { ref.base64 = String(ev.target.result).split(',')[1]; renderReferences(); scheduleSave(); };
    r.readAsDataURL(f);
  }
  renderReferences(); markCriteriaChanged();
}
function renderReferences() {
  const box = SMDOC.getElementById('refList');
  if (!box) return;
  box.innerHTML = '';
  const lv = currentLevel();
  const grades = lv && lv.grading === 'pass' ? ['Pass', 'Not yet achieved']
    : lv && lv.grading === 'first' ? ['Level 2 Distinction', 'Level 2 Merit', 'Level 2 Pass', 'Level 1', 'Not yet achieved']
    : ['Distinction', 'Merit', 'Pass', 'Not yet achieved'];
  references.forEach((ref, i) => {
    const row = document.createElement('div');
    row.className = 'ref-row';
    row.innerHTML = `<div class="ref-name"></div>
      <select class="form-select field-input"><option value="">Grade given...</option>${grades.map(g => `<option>${g}</option>`).join('')}</select>
      <button class="btn btn-sm btn-remove" title="Remove">&#215;</button>
      <textarea class="form-control field-input" placeholder="Optional: which criteria were met and why, e.g. A.M1 met: compared four genres with examples. A.D1 not met: no conclusion."></textarea>`;
    row.querySelector('.ref-name').textContent = ref.name + (ref.base64 ? '' : ' (reading...)');
    const sel = row.querySelector('select'); sel.value = ref.grade; sel.onchange = () => { ref.grade = sel.value; markCriteriaChanged(); };
    const ta = row.querySelector('textarea'); ta.value = ref.notes; ta.oninput = () => { ref.notes = ta.value; };
    row.querySelector('button').onclick = () => { references.splice(i, 1); renderReferences(); markCriteriaChanged(); };
    box.appendChild(row);
  });
}
async function referenceBlocks() {
  const blocks = [];
  for (const [i, ref] of references.entries()) {
    if (!ref.base64) continue;
    const title = `Reference example ${i + 1} (already marked, ${ref.grade || 'grade not given'})`;
    const context = `Previously marked by the assessor for this unit. Grade awarded: ${ref.grade || 'not given'}.${ref.notes ? ' Assessor notes: ' + ref.notes : ''} For calibration only: do not assess, quote or mention it.`;
    if (ref.kind === 'pdf') { const clean = await pdfWithoutDetails(ref.base64).catch(() => null); if (clean) blocks.push({ type: 'document', source: { type: 'base64', media_type: 'application/pdf', data: clean }, title, context }); }
    else {
      const text = await docxToText(ref.base64).catch(() => '');
      if (text) blocks.push({ type: 'document', source: { type: 'text', media_type: 'text/plain', data: redactForAi(text) }, title, context });
    }
  }
  return blocks;
}

// ═════════════════════════ AI check ═════════════════════════
function resetAi() {
  lastFeedback = ''; lastResults = []; lastAnnotations = []; aiState = 'idle'; aiError = ''; aiRunId = ''; aiRunModel = '';
  if (typeof renderAnnotations === 'function') renderAnnotations();
  SMDOC.getElementById('results').innerHTML = '';
  feedbackEdited = false; aiOverall = ''; aiNextSteps = '';
  staffEdited = false; const sn = SMDOC.getElementById('staffNote'); if (sn) sn.value = ''; showStaffRebuild();
  SMDOC.getElementById('feedbackWrap').innerHTML = FEEDBACK_BOX_HTML;
  if (typeof renderAiStatus === 'function') renderAiStatus();
  SMDOC.querySelectorAll('#criteriaBody .ai-cell').forEach(td => td.innerHTML = '<span class="sugg idle">Not checked yet</span>');
}

// ── the pieces of a second marker check, shared with batch marking (batch.js) ──
function criteriaPromptText(criteria) {
  const unitsInPlay = [...new Set(criteria.map(c => splitUnit(c.code).unit).filter(Boolean))];
  const unitTitleOf = u => (pick.find(p => p.unit === u) || {}).unitTitle || u;
  return (unitsInPlay.length > 1
    ? `These units are assessed together in one assignment. Each criterion code is prefixed with its unit (e.g. ${criteria[0].code}); keep the prefix exactly in your codes. Judge each unit's criteria against that unit's wording.\n` +
      unitsInPlay.map(u => `${unitTitleOf(u)}:\n` + criteria.filter(c => splitUnit(c.code).unit === u).map(c => `- ${c.code}: ${c.desc || '(wording not supplied)'}`).join('\n')).join('\n\n')
    : criteria.map(c => `- ${c.code}: ${c.desc || '(wording not supplied)'}`).join('\n'));
}
function checkSystemPrompt() {
  const levelRule = /3/.test((currentLevel() || {}).level || '')
    ? '\n- This is Level 3 work. Expect named organisations, governing bodies, real examples and evidence of research throughout, including at Pass. Work that discusses a topic well in general terms but names none of these is part achieved at best.'
    : '';
  return `You are an experienced vocational assessor (Pearson BTEC and TLM qualifications) carrying out a first-pass assessment for a human assessor, who will make the final decision.

Judge the submission against EACH criterion independently, using the criterion's command verb (describe, explain, analyse, evaluate, justify, etc.) and Pearson's standard: a criterion is met only if the evidence fully satisfies the wording at that level. Be rigorous and evidence-based; do not give the benefit of the doubt. Merit and Distinction criteria need the depth their verb requires.

How this assessor marks:
- The criterion wording is the standard. Where the brief lists things to include ("such as", "include details like"), treat the list as guidance for the learner: a few missing items do not on their own mean a criterion is not met, but say what is missing in "improve".
- Check that each example actually shows the point being made. An example that does not fit what is being described is a gap, and say why it does not fit.
- Describing something means saying how it works or what defines it, in enough detail to show what separates it from the others, not only naming it or saying it is popular.
- Where a criterion asks for different things or a range (teams, tournaments, genres, roles, organisations), one example of each is not enough: it needs several, each described well enough to show how they differ.
- Where the brief asks for work to be supported by research, points need something specific behind them: named organisations or bodies, real examples, data or sources. General discussion with nothing specific behind it does not meet a criterion, even at Pass, however fluent it reads.
- Point out factual errors (wrong dates, ratings, names) in annotations, but only count them against a criterion when they undermine it.
- A short paragraph or a passing remark is not analysis or evaluation. Name what a full answer would need. Evaluation needs a judgement: a balanced conclusion backed by the evidence.
- Flag spelling, grammar, capital letters and American spellings with a few short "note" annotations, and mention them in "overall" if they are frequent. They do not decide a criterion on their own.${levelRule}

Return ONLY a JSON object, no markdown, no backticks:
{
  "results": [
    {
      "code": "criterion code exactly as given",
      "met": true or false,
      "confidence": "high" | "medium" | "low",
      "attempt": "full" | "partial" | "none" (full if met; partial if the learner has clearly tried this criterion but falls short; none if there is no real attempt),
      "evidence": "Where and how the submission does or does not meet it (section, heading or short paraphrase; max 25 words)",
      "comment": "One short sentence to the learner. If met: the evidence that meets it. If not met: exactly what is missing for this criterion.",
      "improve": "One or two sentences to the learner about THIS criterion: what they did in relation to it, and exactly what is missing or what they need to do to achieve it. Write this for every criterion, even ones you judge met, in case the assessor disagrees.",
      "need": "For staff, max 10 words, starting with a verb: what the learner still has to do for this criterion, e.g. 'analyse differences between the genres with examples'. Write it for every criterion.",
      "task": "The task or activity in the assignment brief where this criterion is assessed, e.g. 'Task 2' or 'Activity 1b'. Empty string if no brief is attached."
    }
  ],
  "assignment_title": "The assignment title as written in the brief, or an empty string if no brief is attached.",
  "overall": "General feedback on the assignment as a whole, addressed to the learner, 2 to 4 sentences: what the work covers, its overall quality and presentation, the main strength, and the main thing holding the grade back. Where the writing shows the learner could reach a higher grade, say so. No criterion codes. Plain UK English, honest rather than flattering.",
  "next_steps": "Only if every criterion is met: one or two sentences on how the learner could strengthen the work further. Otherwise an empty string.",
  "annotations": [
    {
      "quote": "4 to 15 consecutive words copied EXACTLY from the submission, unique, at the point you are commenting on",
      "code": "criterion code this relates to, or empty",
      "type": "met" | "not_met" | "note",
      "comment": "Margin comment for the learner, max 20 words, saying what this part achieves or what is missing here. Where something is missing, a direct question often works best, e.g. 'How does a match end?'",
      "page": "page number if the submission is a PDF, else null"
    }
  ],
  "integrity": {
    "ai_likelihood": "low" | "medium" | "high",
    "ai_indicators": ["Specific, quoted or located reasons the writing may (or may not) be AI-generated, e.g. generic phrasing, no personal or local context, uniform paragraph structure, invented or unverifiable references, style shifts between sections, content beyond the learner's level. Consider the file metadata and automated flags provided. Max 6 items, each under 30 words."],
    "sources_cited": ["Every source the learner names or links (website, book, video, article), one per item, as written"],
    "sources_concerns": ["Referencing problems: claims or statistics with no source, sources that look invented or wrong, passages that read as copied without quotation marks, AI chatbot links. Max 6 items."]
  }
}
Include every criterion listed, in the same order. Give 6 to 15 annotations spread through the work, covering where each criterion is evidenced or falls short. AI-likelihood is a judgement for the assessor to follow up, not proof; be calibrated and explain your reasoning.
Never use em dashes or en dashes anywhere in your writing; use commas, colons or full stops instead.`;

}
function checkContextBlock(opts) {
  const header = getHeaderData();
  const unitContext = SMDOC.getElementById('unit_context').value.trim();
  const allCriteria = opts.allCriteria || [];
  return [
    header.unit_title ? `Unit: ${header.unit_title}` : '',
    header.programme_title ? `Programme: ${header.programme_title}` : '',
    header.assignment_title ? `Assignment: ${header.assignment_title}` : '',
    currentLevel() && currentLevel().note ? `Grading: ${currentLevel().note}` : '',
    unitContext ? `Assessor guidance:\n${unitContext}` : '',
    opts.resub && resub.on ? `This is a ${resub.type.toLowerCase()}. Already achieved at first submission (these stand, do not reassess): ${allCriteria.filter(c => prevMet(c.code)).map(c => c.code).join(', ') || 'none'}.${resub.general ? `\nFeedback the learner was given at first submission:\n${resub.general}` : ''}\nIn "improve" and "overall", say whether the learner has acted on that earlier feedback.` : '',
    opts.integrity ? `File metadata and automated integrity checks:\n${integritySummary(opts.integrity)}` : ''
  ].filter(Boolean).join('\n');

}
function checkUserPrompt(contextBlock, criteriaText, nBriefs, nRefs) {
  const briefText = nBriefs ? `\n\nThe assignment brief is attached as "Assignment brief". Use it to see what each task asked for and which criteria each task targets. Fill in "task" for each criterion, and where it helps, name the task in "improve" so the learner knows where to go back to. Do not assess or annotate the brief.` : '';
  const refText = nRefs ? `\n\nReference marked work: ${nRefs} example(s) titled "Reference example" are attached, each already graded by this assessor for this unit (grade and notes in each document's context). Use them only to calibrate how strictly to apply each criterion and command verb. Do not assess them, and do not quote or mention them in any comments, feedback or annotations. Annotations must quote only the learner submission.` : '';
  return `${contextBlock}\n\nTargeted criteria:\n${criteriaText}${briefText}${refText}\n\nAssess the document titled "Learner submission" and return the JSON.`;
}
async function sendCheck(apiKey, systemPrompt, content) {
  const resp = await fetch('marker/api.php?a=ask', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      
      'anthropic-version': '2023-06-01',
      'X-Requested-With': 'crafticulum'
    },
    body: JSON.stringify({
      model: SMDOC.querySelector('input[name="model"]:checked').value,
      max_tokens: 8000,
      system: systemPrompt,
      messages: [{ role: 'user', content }]
    })
  });
  const data = await resp.json();
  if (data.error) throw new Error(data.error.message);
  const raw = data.content.map(b => b.text || '').join('');
  const jsonText = raw.slice(raw.indexOf('{'), raw.lastIndexOf('}') + 1);
  return JSON.parse(jsonText);
}
function normaliseCheck(parsed) {
  return {
    overall: noDashes(parsed.overall || parsed.feedback || ''),
    next: noDashes(parsed.next_steps || ''),
    results: (parsed.results || []).map(r => ({ ...r, code: (r.code || '').trim(), comment: noDashes(r.comment), evidence: noDashes(r.evidence), improve: noDashes(r.improve || ''), need: noDashes(r.need || ''), task: noDashes(String(r.task || '')).trim(),
      attempt: r.met ? 'full' : (['partial', 'none'].includes(r.attempt) ? r.attempt : '') })),
    aiIntegrity: parsed.integrity || null,
    assignmentTitle: noDashes(String(parsed.assignment_title || '')).trim(),
    annotations: (parsed.annotations || []).filter(a => a && a.comment).map(a => ({
      quote: String(a.quote || '').trim(), code: String(a.code || '').trim(),
      type: ['met', 'not_met', 'note'].includes(a.type) ? a.type : 'note',
      text: noDashes(a.comment), page: parseInt(a.page, 10) || null, include: true,
    })),
  };
}

async function runCheck() {
  if (!fileBase64) { toast('Please upload a student assignment first.', 'error'); return; }
  syncRowsFromPick();
  const allCriteria = getCriteria().filter(c => c.code);
  if (allCriteria.length === 0) { toast('Please tick at least one criterion in step 1.', 'error'); return; }
  const criteria = allCriteria.filter(c => !prevMet(c.code));
  if (!criteria.length) {
    lastResults = []; lastAnnotations = []; aiOverall = ''; aiNextSteps = ''; renderAnnotations();
    applyAiResults(); aiState = 'done'; aiStale = false; setProgress('pAi', 'ok'); renderAiStatus(); refreshNav(); scheduleSave();
    toast('Every criterion was achieved at first submission, so there is nothing for the AI to reassess.', 'info');
    return;
  }
  const missingDesc = criteria.filter(c => !c.desc).map(c => c.code);
  if (missingDesc.length && !confirm(`No criterion wording for ${missingDesc.join(', ')}. The AI will only see the code for these. Continue?`)) { aiState = 'idle'; setProgress('pAi', ''); renderAiStatus(); return; }
  const apiKey = getApiKey();
  if (!apiKey) { aiState = 'error'; aiError = 'No API key set. Paste it in Settings and press Save key; the check will start straight away.'; setProgress('pAi', 'err'); renderAiStatus(); openKeySettings(); return; }

  syncRowsFromPick();
  aiState = 'running'; aiError = ''; aiStale = false;
  await Promise.race([integrityReady, new Promise(r => setTimeout(r, 8000))]);
  await Promise.race([fileScanReady, new Promise(r => setTimeout(r, 8000))]);
  if (ivMode && fileScan && fileScan.turnitin && fileScan.learnerText && !fileScanMode) fileScanMode = 'learner';
  if (fileScan && fileScan.turnitin && !fileScanAck && !fileScanMode) { blockAi(fileScan.learnerText ? TURNITIN_SPLIT_MSG : TURNITIN_MSG); return; }
  SMDOC.getElementById('results').innerHTML = '';
  setProgress('pAi', 'run'); renderAiStatus(); refreshNav();

  const criteriaText = criteriaPromptText(criteria);
  const systemPrompt = checkSystemPrompt();
  const contextBlock = checkContextBlock({ integrity: lastIntegrity, allCriteria, resub: true });

  try {
    startPrivacy();
    const submissionBlock = await privateSubmissionBlock();
    const refs = await referenceBlocks();
    const briefs = await briefBlocks();
    const userPrompt = checkUserPrompt(contextBlock, criteriaText, briefs.length, refs.length);
    const parsed = restoreFromAi(await sendCheck(apiKey, systemPrompt, [submissionBlock, ...briefs, ...refs, { type: 'text', text: redactForAi(userPrompt) }]));   // placeholders back to real names, for the assessor only
    const chk = normaliseCheck(parsed);
    aiOverall = chk.overall;
    aiNextSteps = chk.next;
    lastResults = chk.results;
    aiRunId = Date.now().toString(36) + Math.random().toString(36).slice(2, 6);
    aiRunModel = SMDOC.querySelector('input[name="model"]:checked').value;
    lastAiIntegrity = chk.aiIntegrity;
    { const at = SMDOC.getElementById('assignment_title'); if (at && !at.value.trim() && chk.assignmentTitle) at.value = chk.assignmentTitle; }
    const mine = lastAnnotations.filter(a => a.manual);   // the assessor's own comments stay
    lastAnnotations = mine.concat(chk.annotations);
    renderAnnotations();
    if (lastIntegrity) renderIntegrity(lastIntegrity, chk.aiIntegrity);
    applyAiResults();
    aiState = 'done'; setProgress('pAi', 'ok');
  } catch(err) {
    if (err && err.blocked) { blockAi(err.message); return; }
    aiState = 'error'; aiError = err.message; setProgress('pAi', 'err');
  }
  renderAiStatus(); refreshNav(); scheduleSave();
}

// ═════════════════════════ PDFs the second marker shouldn't get as they are ═════════════════════════
// A Turnitin download carries the cover sheet and the assessor's own comments. A PDF made of
// pictures of pages loses the work entirely if it's sent as text.
const TURNITIN_MSG = "This looks like a Turnitin download: it has the cover sheet and marking comments in it. The second marker would read any comments already on it, and the learner's name from the cover. Upload the learner's original file if you can.";
const TURNITIN_SPLIT_MSG = "This looks like a Turnitin download, with a cover sheet, feedback pages and marking comments on the work. The learner's own writing can be taken out on its own, so the second marker doesn't see any of that.";
const TURNITIN_RE = /GRADEMARK REPORT|Feedback Studio|Submission ID:?\s*(?:trn:oid|\d{6,})|Page \d+ of \d+ - Grading and Feedback/i;
// Turnitin's own text (cover, feedback pages, comment boxes) uses fonts it embeds as e.g. "NotoSans-Regular-6660"
const TURNITIN_FONT_RE = /^(?:NotoSans|LexendDeca)[\w-]*-\d+$/;
let fileScanMode = '';   // 'learner': send only the learner's text from a Turnitin download
const PICTURE_PDF_MSG = "Most pages of this PDF are pictures, so sending it as text with names hidden would leave the work out. You can send the whole PDF this time (names in it won't be hidden), or upload the learner's Word file instead.";
let fileScan = null, fileScanReady = Promise.resolve(), fileScanAck = false;
async function scanPdf(b64) {
  const { text } = await pdfTextOf(b64);
  const pages = text.split(/--- Page \d+ ---/).slice(1).map(t => t.trim());
  const thin = pages.filter(t => t.replace(/\s+/g, ' ').length < 200).length;
  const turnitin = TURNITIN_RE.test(text);
  const learner = turnitin ? await turnitinLearnerText(b64).catch(() => null) : null;
  return { pages: pages.length, thin, pictures: pages.length > 0 && thin * 2 >= pages.length, turnitin,
    learnerText: learner && learner.words >= 100 ? learner.text : '', learnerWords: learner ? learner.words : 0, learnerPages: learner ? learner.pages : 0 };
}
// The learner's own writing from a Turnitin download: everything not in Turnitin's fonts, on pages that have some
async function turnitinLearnerText(b64) {
  const lib = await window.BTECPdfjs();
  const doc = await lib.getDocument({ data: Uint8Array.from(atob(b64), c => c.charCodeAt(0)), isEvalSupported: false }).promise;
  const out = []; let words = 0, theirs = 0;
  for (let p = 1; p <= Math.min(doc.numPages, 80); p++) {
    const page = await doc.getPage(p);
    const tc = await page.getTextContent();
    await page.getOperatorList();   // loads the fonts so their real names can be read
    const fontName = id => { try { return (page.commonObjs.get(id) || {}).name || ''; } catch (e) { return ''; } };
    const mine = tc.items.filter(i => !TURNITIN_FONT_RE.test(fontName(i.fontName)));
    theirs += tc.items.length - mine.length;
    const text = mine.map(i => i.str + (i.hasEOL ? '\n' : ' ')).join('').replace(/[ \t]+\n/g, '\n').replace(/\n{3,}/g, '\n\n').trim();
    const n = text ? text.split(/\s+/).length : 0;
    if (n >= 15) { out.push(`--- Page ${p} ---\n${text}`); words += n; }
  }
  // older GradeMark downloads don't use Turnitin's fonts, so their text is the marking, not the learner's work
  if (!theirs) return { text: '', words: 0, pages: 0 };
  return { text: fixLigatures(out.join('\n\n')), words, pages: out.length };
}
function startFileScan() {
  fileScan = null; fileScanAck = false; fileScanMode = '';
  fileScanReady = fileBase64 && fileMediaType === 'application/pdf'
    ? scanPdf(fileBase64).then(s => { fileScan = s; return integrityReady; }).then(() => {
        if (fileScan && fileScan.turnitin && lastIntegrity && !lastIntegrity.turnitin) { lastIntegrity = forTurnitin(lastIntegrity); renderIntegrity(lastIntegrity, lastAiIntegrity); scheduleSave(); }
      }).catch(() => { fileScan = fileScan || null; })
    : Promise.resolve();
}
// A Turnitin download's file details belong to Turnitin's copy, so the checks that read them don't apply
function forTurnitin(r) {
  const skip = /generated by code|Little or no metadata|PDF was edited \d+ time|after the recorded submission date|Created and last saved within/i;
  const flags = [{ level: 'info', text: "This is a Turnitin download, so its file details describe Turnitin's copy, not the learner's file. Check the original file for authorship and editing history." }, ...r.flags.filter(f => !skip.test(f.text))];
  return { ...r, flags, turnitin: true };
}
function blockAi(message) {
  aiState = 'blocked'; aiError = message; setProgress('pAi', 'err');
  renderAiStatus(); refreshNav(); scheduleSave();
  if (currentStep >= 5) goStep(4);
}
function sendAnyway() { fileScanAck = true; runCheck(); }
function sendLearnerOnly() { fileScanMode = 'learner'; runCheck(); }

function escapeHtml(t) {
  return String(t ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function applyAiResults() {
  const hadDecisions = getCriteria().some(c => c.code && c.achieved);
  unlockGrading(false);
  const rows = Array.from(SMDOC.querySelectorAll('#criteriaBody .crit-row'));
  rows.forEach(tr => {
    const code = tr.querySelector('.code-input').value.trim();
    const r = lastResults.find(x => x.code.toLowerCase() === code.toLowerCase());
    const aiCell = tr.querySelector('.ai-cell');
    if (!r) {
      if (prevMet(code)) {
        aiCell.innerHTML = '<span class="sugg">Y at first submission</span>';
        const s0 = tr.querySelector('.achieved-sel'); if (!s0.value) { s0.value = 'Y'; updateSelColor(s0); }
        const t0 = tr.querySelector('.desc-input'); if (!t0.value.trim()) t0.value = resub.comments[codeKey(code)] || 'Achieved at first submission.';
      } else aiCell.innerHTML = '<span class="sugg idle">No suggestion</span>';
      return;
    }
    aiCell.innerHTML = `<span class="sugg">suggests ${r.met ? 'Y' : 'N'}${!r.met && r.attempt === 'partial' ? ' (part achieved)' : ''}${r.confidence ? ', ' + escapeHtml(r.confidence) : ''}</span>${r.task ? `<span class="task">${escapeHtml(r.task)}</span>` : ''}<div class="ev">${escapeHtml(r.comment || r.evidence || '')}${r.evidence && r.comment ? ' <span style="opacity:.8">(' + escapeHtml(r.evidence) + ')</span>' : ''}</div>`;
    const sel = tr.querySelector('.achieved-sel');
    if (!sel.value) { sel.value = r.met ? 'Y' : 'N'; updateSelColor(sel); }   // a decision already made stays
    const ta = tr.querySelector('.desc-input');
    if (!ta.value.trim()) ta.value = r.comment || '';
  });
  SMDOC.getElementById('gradeNote').textContent = hadDecisions ? "Second marker's suggestions added. Your decisions were kept." : 'Pre-filled from the second marker. Confirm or change each one.';
  updateGrade();

  const crit = getCriteria().filter(c => c.code);
  const aiMet = c => { if (prevMet(c.code)) return true; const r = lastResults.find(x => x.code.toLowerCase() === c.code.toLowerCase()); return !!(r && r.met); };
  const g = gradeOf(crit, aiMet);
  const met = crit.filter(aiMet).map(c => c.code);
  const notMet = crit.filter(c => !aiMet(c)).map(c => c.code);
  const lowConf = lastResults.filter(r => r.confidence === 'low').map(r => r.code);

  if (!feedbackEdited) composeFeedback(true);
  SMDOC.getElementById('results').innerHTML = `
      <div class="card">
        <div class="ai-grade" style="margin:0">
          <div class="label" style="margin:0">Second marker's rough grade (advisory)</div>
          <div class="big"><span class="badge grade-pill g-${g.grade}">${g.label}</span></div>
          <div class="helper" style="margin-top:8px"><strong>Achieved (${met.length}):</strong> ${met.join(', ') || 'none'}</div>
          <div class="helper"><strong>Not yet achieved (${notMet.length}):</strong> ${notMet.join(', ') || 'none'}</div>
          ${lowConf.length ? `<div class="helper" style="color:var(--resub)"><strong>Low confidence, check closely:</strong> ${lowConf.join(', ')}</div>` : ''}
        </div>
      ${privacySummaryHtml()}</div>`;
}

// ═════════════════════════ Word export (in-browser) ═════════════════════════
// Fills the Pearson template by editing word/document.xml inside the .docx zip,
// targeting known paragraph IDs — same approach as the server-side script.
const PARA_IDS = {
  programme_title: '5464ECEE',
  student_name: '376B185F',
  student_reg: '090E988B',
  assignment_title: '5766A358',
  assessor_name: '56709983',
  unit_title: '721CE378',
  deadline: '0CB9F3DD',
  date_submitted: '5A55CCDB',
  extension: '47BFE51A',
  general_comments: '48A65F4A',
  criteria_code: ['2FF9B27D', '384EC949', '628F292F'],
  criteria_achieved: ['19412CAB', '418CC04C', '292BCE69'],
  criteria_comment: ['1B3DEE63', '50EE83A3', '0B43C6E1']
};

function escapeXml(t) {
  return t.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function makeRuns(text, size, bold) {
  // Split on newlines, join with <w:br/> so multi-line comments render properly
  const sz = size ? `<w:sz w:val="${size}"/><w:szCs w:val="${size}"/>` : '';
  const rPr = `<w:rPr><w:rFonts w:ascii="Plus Jakarta Sans" w:hAnsi="Plus Jakarta Sans" w:cs="Open Sans"/>${bold ? '<w:b/><w:bCs/>' : ''}<w:color w:val="auto"/>${sz}</w:rPr>`;
  const parts = text.split('\n');
  let out = '';
  parts.forEach((p, i) => {
    if (i > 0) out += `<w:r>${rPr}<w:br/></w:r>`;
    if (p !== '') out += `<w:r>${rPr}<w:t xml:space="preserve">${escapeXml(p)}</w:t></w:r>`;
  });
  return out;
}

// Replaces whatever the template has in that cell (e.g. the printed "Y / N / R" prompt)
// with the value, keeping the cell's paragraph formatting.
function fillParagraph(xml, paraId, text, size, bold, clearIfEmpty) {
  if (!text && !clearIfEmpty) return xml;
  const runs = text ? makeRuns(text, size, bold) : '';
  const re = new RegExp(`(<w:p [^>]*w14:paraId="${paraId}"[^>]*>)([\\s\\S]*?)(</w:p>)`);
  return xml.replace(re, (m, open, inner, close) => {
    const pPr = (inner.match(/<w:pPr>[\s\S]*?<\/w:pPr>|<w:pPr\/>/) || [''])[0];
    return open + pPr + runs + close;
  });
}

// Adds a bordered "Note for other lecturers" box after the form
function appendStaffBox(xml, note) {
  const at = xml.indexOf('>Note for other lecturers<');
  if (at !== -1) {
    const a = xml.lastIndexOf('<w:tbl>', at), b = xml.indexOf('</w:tbl>', at);
    if (a !== -1 && b !== -1) xml = xml.slice(0, a) + xml.slice(b + 8);
  }
  if (!note) return xml;
  const font = '<w:rFonts w:ascii="Plus Jakarta Sans" w:hAnsi="Plus Jakarta Sans" w:cs="Open Sans"/>';
  const border = '<w:top w:val="single" w:sz="8" w:space="0" w:color="378ADD"/><w:left w:val="single" w:sz="8" w:space="0" w:color="378ADD"/><w:bottom w:val="single" w:sz="8" w:space="0" w:color="378ADD"/><w:right w:val="single" w:sz="8" w:space="0" w:color="378ADD"/><w:insideH w:val="single" w:sz="4" w:space="0" w:color="378ADD"/>';
  const cell = (inner, shade) => `<w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/>${shade ? '<w:shd w:val="clear" w:color="auto" w:fill="E6F1FB"/>' : ''}<w:tcMar><w:top w:w="80" w:type="dxa"/><w:bottom w:w="80" w:type="dxa"/></w:tcMar></w:tcPr>${inner}</w:tc>`;
  const para = (text, bold, size) => `<w:p><w:pPr><w:spacing w:before="0" w:after="0"/></w:pPr><w:r><w:rPr>${font}${bold ? '<w:b/><w:bCs/>' : ''}<w:color w:val="auto"/><w:sz w:val="${size}"/><w:szCs w:val="${size}"/></w:rPr><w:t xml:space="preserve">${escapeXml(text)}</w:t></w:r></w:p>`;
  const table = `<w:p><w:pPr><w:spacing w:before="120" w:after="0"/></w:pPr></w:p><w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblBorders>${border}</w:tblBorders><w:tblLayout w:type="fixed"/></w:tblPr><w:tblGrid><w:gridCol w:w="10000"/></w:tblGrid>` +
    `<w:tr>${cell(para('Note for other lecturers', true, 20), true)}</w:tr><w:tr>${cell(para(note, false, 20), false)}</w:tr></w:tbl><w:p/>`;
  const i = xml.lastIndexOf('<w:sectPr', xml.lastIndexOf('</w:body>'));
  return i === -1 ? xml.replace('</w:body>', table + '</w:body>') : xml.slice(0, i) + table + xml.slice(i);
}

async function exportDoc() {
  try {
    const { blob, name } = await buildRecord({ staffNote: true });
    saveBlob(blob, name);
    logAgreement();
  } catch (err) { toast(err.message, 'error'); }
}

// "Part achieved: ..." on a criterion not met where the second marker saw a real attempt
function withPart(c) {
  const comment = c.comment || '';
  if (c.achieved === 'Y' || /^part achieved/i.test(comment.trim())) return comment;
  const ai = lastResults.find(r => r.code.toLowerCase() === (c.code || '').toLowerCase());
  return ai && !ai.met && ai.attempt === 'partial' && comment.trim() ? 'Part achieved: ' + comment.trim() : comment;
}

// ═════════════════════════ Second marker agreement log ═════════════════════════
// Each finished learner adds the second marker's call and yours for every criterion.
// No names or learner details are kept, only the unit, assignment and the calls.
const AGREE_KEY = 'btec_agreement_log';
function agreeLog() { try { const v = JSON.parse(storeGet(AGREE_KEY) || '[]'); return Array.isArray(v) ? v : []; } catch (e) { return []; } }
function logAgreement() {
  if (!aiRunId || !lastResults.length) return;
  const rows = getCriteria().filter(c => c.code && c.achieved && !prevMet(c.code)).map(c => {
    const r = lastResults.find(x => x.code.toLowerCase() === c.code.toLowerCase());
    return r ? { code: c.code, band: bandOf(c.code) || '', ai: !!r.met, attempt: r.attempt || '', conf: r.confidence || '', you: c.achieved } : null;
  }).filter(Boolean);
  if (!rows.length) return;
  const h = getHeaderData();
  const entry = { id: aiRunId, at: new Date().toISOString(), unit: h.unit_title || '', assignment: h.assignment_title || '', model: aiRunModel, rows };
  const log = [entry, ...agreeLog().filter(e => e.id !== aiRunId)].slice(0, 300);
  storeSet(AGREE_KEY, JSON.stringify(log));
  renderAgreement();
}
function agreementStats(log) {
  const all = log.flatMap(e => e.rows);
  const same = r => r.ai === (r.you === 'Y');
  const pct = (a, b) => b ? Math.round(100 * a / b) : 0;
  const by = band => { const rs = all.filter(r => r.band === band); return { n: rs.length, k: rs.filter(same).length }; };
  return {
    learners: log.length, n: all.length, k: all.filter(same).length, pct: pct(all.filter(same).length, all.length),
    harsher: all.filter(r => !r.ai && r.you === 'Y').length,   // it said not met, you said met
    softer: all.filter(r => r.ai && r.you !== 'Y').length,     // it said met, you didn't
    bands: ['P', 'M', 'D', 'L1'].map(b => ({ b, ...by(b) })).filter(x => x.n),
    low: (() => { const rs = all.filter(r => r.conf === 'low'); return { n: rs.length, k: rs.filter(same).length }; })(),
  };
}
function renderAgreement() {
  const el = SMDOC.getElementById('agreeSummary'); if (!el) return;
  const log = agreeLog();
  const tools = SMDOC.getElementById('agreeTools');
  if (tools) tools.hidden = !log.length;
  if (!log.length) { el.textContent = "Nothing logged yet. When you download a record after a second marker check, its call and yours for each criterion are saved here, so you can see how often you agree. No names are kept."; return; }
  const s = agreementStats(log);
  const bandName = { P: 'Pass', M: 'Merit', D: 'Distinction', L1: 'Level 1' };
  const crit = n => n === 1 ? '1 criterion' : `${n} criteria`;
  el.innerHTML = `<strong>You agreed on ${s.k} of ${s.n} criteria (${s.pct}%)</strong> across ${plural(s.learners, 'learner')}.<br>` +
    (s.harsher || s.softer
      ? `It was stricter than you on ${crit(s.harsher)} and more generous on ${crit(s.softer)}.<br>`
      : "It wasn't stricter or more generous than you on any of them.<br>") +
    escapeHtml(s.bands.map(x => `${bandName[x.b]} ${x.k} of ${x.n}`).join(', ')) + '.' +
    (s.low.n ? `<br>Low confidence calls: agreed on ${s.low.k} of ${s.low.n}.` : '');
}
function downloadAgreement() {
  const log = agreeLog(); if (!log.length) return;
  const q = v => /[",\n]/.test(String(v)) ? `"${String(v).replace(/"/g, '""')}"` : String(v);
  const lines = [['Date', 'Unit', 'Assignment', 'Model', 'Check', 'Criterion', 'Band', 'Second marker', 'Confidence', 'Part achieved', 'You', 'Agree'].join(',')];
  log.slice().reverse().forEach(e => e.rows.forEach(r => lines.push([e.at.slice(0, 10), e.unit, e.assignment, e.model, e.id, r.code, r.band, r.ai ? 'Y' : 'N', r.conf, r.attempt === 'partial' ? 'Yes' : '', r.you, r.ai === (r.you === 'Y') ? 'Yes' : 'No'].map(q).join(','))));
  saveBlob(new Blob([lines.join('\r\n')], { type: 'text/csv' }), 'Second marker agreement.csv');
}
function clearAgreement() {
  if (!confirm('Clear the agreement log? This removes every saved comparison from this browser.')) return;
  storeDel(AGREE_KEY); renderAgreement();
}

// Builds the filled Pearson record. staffNote: include the "Note for other lecturers" box.
async function buildRecord(opts = {}) {
  if (!templateBuffer) throw new Error('No Word template available. Upload the Pearson template in Settings.');
  const header = getHeaderData();
  const criteria = getCriteria().filter(c => c.code);
  const ungraded = criteria.filter(c => !c.achieved).map(c => c.code);
  if (ungraded.length) throw new Error(`Set Criteria achieved for ${ungraded.join(', ')} before exporting.`);
  const merged = criteria.map(c => ({ code: c.code, achieved: c.achieved, comment: noDashes(withPart(c)) }));
  const lateLine = lateSentence();
  const feedback = noDashes([(lastFeedback || '').trim(), lateLine].filter(Boolean).join('\n\n'));

  // One section of the form: header, three criteria rows, and General comments (extra criteria overflow there)
  const fillSection = (xml, ids, h, rows, general) => {
    ['programme_title', 'assignment_title', 'unit_title', 'deadline', 'extension'].forEach(k => { if (ids[k]) xml = fillParagraph(xml, ids[k], h[k] || ''); });
    ['student_name', 'student_reg', 'assessor_name', 'date_submitted'].forEach(k => { if (ids[k]) xml = fillParagraph(xml, ids[k], h[k] || '', 16); });
    for (let i = 0; i < 3; i++) {
      const row = rows[i];
      // just the decision (Y, N or R), never the printed "Y / N / R" prompt
      xml = fillParagraph(xml, ids.criteria_achieved[i], row ? row.achieved : '', null, true, true);
      if (!row) continue;
      xml = fillParagraph(xml, ids.criteria_code[i], row.code);
      xml = fillParagraph(xml, ids.criteria_comment[i], row.comment, 24);
    }
    const extra = rows.slice(3).map(r => `${r.code} (${r.achieved || '-'})${r.comment ? ': ' + r.comment : ''}`).join('\n');
    const text = [general, extra].filter(Boolean).join('\n\n');
    return fillParagraph(xml, ids.general_comments, text, 24);
  };

  const fromFirst = resub.on && resub.recordBuffer;
  const zip = await JSZip.loadAsync((fromFirst ? resub.recordBuffer : templateBuffer).slice(0));
  if (!zip.file('word/document.xml')) throw new Error('The Word template looks different from the Pearson form. Use the built-in template (reload the page) or upload the original Pearson .docx.');
  let xml = await zip.file('word/document.xml').async('string');
  let first = null;
  if (resub.on) {
    if (!xml.includes(RESUB_IDS.general_comments)) throw new Error("This form has no Resubmission section. Remove the first record in step 2 (or restore the built-in template) and try again.");
    first = merged.map(c => ({ code: c.code, achieved: resub.prev[codeKey(c.code)] || '', comment: noDashes(resub.comments[codeKey(c.code)] || '') }));
    // with the first record, its first-submission section is kept exactly as it was
    if (!fromFirst) xml = fillSection(xml, PARA_IDS, { ...header, deadline: resub.header.deadline || '', date_submitted: resub.header.date_submitted || '', extension: resub.header.extension || '' }, first.filter(r => r.achieved), resub.general);
    xml = fillSection(xml, RESUB_IDS, header, merged, feedback);
    xml = fillParagraph(xml, RESUB_IDS.resub_or_retake, resub.type);
  } else xml = fillSection(xml, PARA_IDS, header, merged, feedback);
  if (opts.staffNote) xml = appendStaffBox(xml, staffNoteText());

  zip.file('word/document.xml', xml);
  await embedDecision(zip, { app: 'btec-checker', v: 1, savedAt: new Date().toISOString(), header, criteria: merged, feedback, staffNote: staffNoteText(), resubmission: resub.on ? { type: resub.type, first } : null });
  const blob = await zip.generateAsync({ type: 'blob', compression: 'DEFLATE', mimeType: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' });
  const who = [header.student_reg, learnerMode === 'anon' ? '' : header.student_name].map(x => (x || '').trim()).filter(Boolean);
  const name = [...who, 'BTEC Assessment Record', ...(resub.on ? [resub.type] : [])].join(' - ').replace(/[\\/:*?"<>|]+/g, '').replace(/\s+/g, ' ') + '.docx';
  return { blob, name };
}


// ═════════════════════════ Comments on the work + marked copies ═════════════════════════
function renderAnnotations() { renderWork(); }

function markingInfo() {
  const h = getHeaderData();
  const rows = getCriteria().filter(c => c.code).map(r => ({ ...r, comment: noDashes(withPart(r)) }));
  const g = gradeOf(rows, r => r.achieved === 'Y');
  const fbBox = SMDOC.getElementById('feedbackBox');
  return { ...h, rows, gradeLabel: g.label, feedback: noDashes((lastFeedback || '').trim()), fileName: fileNameStr || '' };
}
// Student ID - Student name - original file name
function baseName() {
  const h = getHeaderData();
  const file = (fileNameStr || '').replace(/\.[^.]+$/, '');
  const parts = [h.student_reg, learnerMode === 'anon' ? '' : h.student_name, file].map(x => (x || '').trim()).filter(Boolean);
  if (!parts.length) parts.push('Assessment record');
  return parts.join(' - ').replace(/[\\/:*?"<>|]+/g, '').replace(/\s+/g, ' ').slice(0, 120);
}
function saveBlob(blob, name) {
  setTimeout(() => toast(`Downloaded ${name}`, 'ok'), 300);
  const a = document.createElement('a');
  a.href = URL.createObjectURL(blob); a.download = name;
  SMROOT.appendChild(a); a.click(); a.remove();
  setTimeout(() => URL.revokeObjectURL(a.href), 4000);
}
async function withBusy(btnId, label, fn) {
  const b = SMDOC.getElementById(btnId), old = b.innerHTML;
  b.disabled = true; b.textContent = label;
  try { await fn(); } catch (e) { console.error(e); toast('That did not work: ' + e.message, 'error'); }
  b.disabled = false; b.innerHTML = old;
}
function downloadMarkedPdf() {
  if (!fileBase64) { toast('No submission uploaded.', 'error'); return; }
  return withBusy('markedPdfBtn', 'Building PDF...', async () => {
    const blob = await buildMarkedPdf({ kind: fileMediaType === 'application/pdf' ? 'pdf' : 'docx', base64: fileBase64, name: fileNameStr }, lastAnnotations, markingInfo());
    saveBlob(blob, `${baseName()} - marked.pdf`);
  });
}
function downloadCommentedDocx() {
  if (!fileBase64 || fileMediaType === 'application/pdf') return;
  return withBusy('commentedDocxBtn', 'Adding comments...', async () => {
    const { blob } = await buildCommentedDocx({ base64: fileBase64, name: fileNameStr }, lastAnnotations, markingInfo());
    saveBlob(blob, `${baseName()} - with comments.docx`);
  });
}
function updateDownloadOptions() {
  const isPdf = fileMediaType === 'application/pdf';
  const b = SMDOC.getElementById('commentedDocxBtn');
  if (!b) return;
  b.style.display = isPdf ? 'none' : '';
  const cw = SMDOC.getElementById('attCommentedWrap'); if (cw) cw.style.display = isPdf ? 'none' : '';
  SMDOC.getElementById('dlNote').textContent = !lastAnnotations.length
    ? 'No comments from the AI check yet, so the marked PDF will only include the feedback page.'
    : isPdf
      ? 'The marked PDF keeps the original pages, highlights each commented passage and adds the comments in a margin. The last page has the decisions and the feedback box.'
      : 'The marked PDF is redrawn from the Word file (text and pictures), so the layout is close but not identical. For the exact layout, open the commented Word file and save it as PDF.';
}


// ═════════════════════════ Email to the learner ═════════════════════════
let emailEdited = false, emailManual = false;
// Student email = student ID + @yourcollege.ac.uk (domain set in keys.js), unless typed in by hand
function autoEmail() {
  if (emailManual || learnerMode === 'anon') return;
  const id = SMDOC.getElementById('student_reg').value.trim().replace(/\s+/g, '');
  const domain = (window.BTEC_EMAIL_DOMAIN || '').replace(/^@/, '').trim();
  const v = id && domain ? `${id}@${domain}` : '';
  SMDOC.getElementById('student_email').value = v;
  const to = SMDOC.getElementById('emailTo'); if (to) to.value = v;
}
function firstName(n) { if (learnerMode === 'anon') return ''; return (n || '').trim().split(/\s+/)[0] || ''; }
function levelName() { const lv = currentLevel(); return lv ? lv.level : ''; }
function subjectName() { const si = SMDOC.getElementById('subjectSelect').value; return si !== '' ? window.BTEC_DATA[+si].subject : ''; }

function composeEmail(force) {
  const h = getHeaderData();
  autoEmail();
  SMDOC.getElementById('emailTo').value = learnerMode === 'anon' ? (SMDOC.getElementById('emailTo').value || '') : SMDOC.getElementById('student_email').value.trim();
  if (emailEdited && !force) return;
  const rows = getCriteria().filter(c => c.code);
  const g = gradeOf(rows, r => r.achieved === 'Y');
  const met = rows.filter(r => r.achieved === 'Y').map(r => r.code);
  const missed = rows.filter(r => r.achieved === 'N').map(r => r.code);
  const resubCodes = rows.filter(r => r.achieved === 'R').map(r => r.code);
  const what = h.assignment_title || 'your assignment';
  SMDOC.getElementById('emailSubject').value = noDashes(`${resub.on ? resub.type + ' feedback' : 'Assessment feedback'}: ${h.assignment_title || 'assignment'}${h.unit_title ? ', ' + h.unit_title.split(':')[0] : ''}`);
  const lines = [];
  lines.push(`Hi ${firstName(h.student_name) || 'there'},`, '');
  lines.push(resub.on ? `Your ${resub.type.toLowerCase()} for ${what} has been assessed. Here are your results and feedback.` : `Your work for ${what} has been assessed. Here are your results and feedback.`, '');
  const det = [
    ['Course', h.programme_title],
    ['Level', levelName()],
    ['Unit', h.unit_title],
    ['Assignment', h.assignment_title],
    ['Submission', resub.on ? resub.type : ''],
    ['Assessor', h.assessor_name],
    ['Submitted', h.date_submitted],
    ['Feedback date', new Date().toLocaleDateString('en-GB')],
  ].filter(d => d[1]);
  det.forEach(([k, v]) => lines.push(`${k}: ${v}`));
  lines.push('', `Grade: ${g.grade === 'U' && !g.perUnit ? 'Not yet achieved' : g.label}`);
  if (met.length) lines.push(`Criteria achieved: ${met.join(', ')}`);
  if (missed.length) lines.push(`Criteria not yet achieved: ${missed.join(', ')}`);
  if (resubCodes.length) lines.push(`Resubmission: ${resubCodes.join(', ')}`);
  const fb = noDashes((lastFeedback || '').trim());
  if (fb) lines.push('', 'Feedback', fb);
  if (resub.on && (missed.length || resubCodes.length)) lines.push('', 'Only one resubmission is allowed per assignment, so please speak to me about what happens next for the criteria not yet achieved.');
  else if (resubCodes.length) lines.push('', `You have been given a resubmission for ${resubCodes.join(', ')}. Use the feedback above and the comments on your work to improve it. Resubmissions must be authorised and handed in within 15 working days of receiving this feedback, so please check the exact deadline with me.`);
  else if (missed.length) lines.push('', 'Please read the comments on your work so you can see where the missing criteria could have been met, and speak to me if you have any questions.');
  const att = [];
  if (SMDOC.getElementById('attMarked').checked) att.push('your work with my comments marked on it (PDF)');
  if (SMDOC.getElementById('attRecord').checked) att.push('your assessment record');
  if (SMDOC.getElementById('attCommented').checked && fileMediaType !== 'application/pdf') att.push('your Word file with my comments added');
  if (att.length) lines.push('', `Attached: ${att.join('; ')}.`);
  lines.push('', 'Please note this grade is provisional until it has been internally verified.', '', 'Kind regards,', h.assessor_name || '');
  SMDOC.getElementById('emailBody').value = noDashes(lines.join('\n')).replace(/\n{3,}/g, '\n\n').trim();
}

function emailParts() {
  return {
    to: SMDOC.getElementById('emailTo').value.trim(),
    subject: noDashes(SMDOC.getElementById('emailSubject').value.trim()),
    body: noDashes(SMDOC.getElementById('emailBody').value.trim()),
  };
}
function copyEmail() {
  const e = emailParts();
  const text = `Subject: ${e.subject}\n\n${e.body}`;
  const done = () => { const b = SMDOC.getElementById('copyEmailBtn'); b.textContent = 'Copied'; setTimeout(() => b.textContent = 'Copy email text', 2000); };
  if (navigator.clipboard) navigator.clipboard.writeText(text).then(done, () => toast('The browser blocked copying. Select the message text and copy it instead.', 'warn')); else toast('The browser blocked copying. Select the message text and copy it instead.', 'warn');
}
function openMailApp() {
  const e = emailParts();
  let body = e.body;
  // mail links have length limits in some apps
  if (body.length > 1800) body = body.slice(0, 1800) + '\n\n[Message shortened, paste the full text with "Copy email text"]';
  location.href = `mailto:${encodeURIComponent(e.to)}?subject=${encodeURIComponent(e.subject)}&body=${encodeURIComponent(body)}`;
}

// .eml draft with attachments. "X-Unsent: 1" makes desktop Outlook open it as a new, sendable message.
function b64Wrap(b64) { return b64.replace(/.{1,76}/g, '$&\r\n'); }
function utf8b64(s) { return btoa(unescape(encodeURIComponent(s))); }
function mimeWord(s) { return /^[\x20-\x7e]*$/.test(s) ? s : `=?UTF-8?B?${utf8b64(s)}?=`; }
function blobToB64(blob) {
  return new Promise((res, rej) => { const r = new FileReader(); r.onload = () => res(String(r.result).split(',')[1]); r.onerror = rej; r.readAsDataURL(blob); });
}
function bodyHtml(text) {
  const esc = t => t.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  return '<html><body style="font-family:Calibri,Arial,sans-serif;font-size:11pt;line-height:1.45">' +
    text.split(/\n{2,}/).map(par => {
      const ls = par.split('\n');
      if (ls[0] === 'Feedback') return `<p><b>Feedback</b><br>${ls.slice(1).map(esc).join('<br>')}</p>`;
      return '<p>' + ls.map(l => { const m = l.match(/^(Course|Level|Unit|Assignment|Assessor|Submitted|Feedback date|Grade|Criteria achieved|Criteria not yet achieved|Resubmission|Attached): (.*)$/); return m ? `<b>${esc(m[1])}:</b> ${esc(m[2])}` : esc(l); }).join('<br>') + '</p>';
    }).join('') + '</body></html>';
}
async function createEmailDraft() {
  const e = emailParts();
  if (!e.body) { toast('The message is empty.', 'error'); return; }
  return withBusy('emlBtn', 'Preparing email...', async () => {
    const files = [];
    const fileKind = fileMediaType === 'application/pdf' ? 'pdf' : 'docx';
    if (SMDOC.getElementById('attMarked').checked && fileBase64) {
      files.push({ blob: await buildMarkedPdf({ kind: fileKind, base64: fileBase64, name: fileNameStr }, lastAnnotations, markingInfo()), name: `${baseName()} - marked.pdf`, type: 'application/pdf' });
    }
    if (SMDOC.getElementById('attRecord').checked) {
      const r = await buildRecord({ staffNote: SMDOC.getElementById('attStaff').checked });
      files.push({ blob: r.blob, name: r.name, type: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' });
    }
    if (SMDOC.getElementById('attCommented').checked && fileBase64 && fileKind === 'docx') {
      const { blob } = await buildCommentedDocx({ base64: fileBase64, name: fileNameStr }, lastAnnotations, markingInfo());
      files.push({ blob, name: `${baseName()} - with comments.docx`, type: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' });
    }
    const mixed = 'mixed_' + Math.random().toString(36).slice(2), alt = 'alt_' + Math.random().toString(36).slice(2);
    const out = [
      'MIME-Version: 1.0', 'X-Unsent: 1',
      `To: ${e.to}`, `Subject: ${mimeWord(e.subject)}`,
      `Content-Type: multipart/mixed; boundary="${mixed}"`, '', `--${mixed}`,
      `Content-Type: multipart/alternative; boundary="${alt}"`, '',
      `--${alt}`, 'Content-Type: text/plain; charset="utf-8"', 'Content-Transfer-Encoding: base64', '', b64Wrap(utf8b64(e.body)),
      `--${alt}`, 'Content-Type: text/html; charset="utf-8"', 'Content-Transfer-Encoding: base64', '', b64Wrap(utf8b64(bodyHtml(e.body))),
      `--${alt}--`, '',
    ];
    for (const f of files) {
      const fn = mimeWord(f.name);
      out.push(`--${mixed}`, `Content-Type: ${f.type}; name="${fn}"`, 'Content-Transfer-Encoding: base64', `Content-Disposition: attachment; filename="${fn}"`, '', b64Wrap(await blobToB64(f.blob)));
    }
    out.push(`--${mixed}--`, '');
    saveBlob(new Blob([out.join('\r\n')], { type: 'message/rfc822' }), `${baseName()} - feedback email.eml`);
  });
}

// ═════════════════════════ Journey (step-by-step wizard) ═════════════════════════
const STEPS = ['Unit', 'Learner', 'Submission', 'Checks', 'Grade', 'Export'];
let currentStep = 1, maxStep = 1;
let aiState = 'idle', aiError = '', aiStale = false, integrityFailed = false;
let pick = [];   // [{code, desc, on}]

function toggleSettings(open) {
  const c = SMDOC.getElementById('settingsCard'), bd = SMDOC.getElementById('settingsBackdrop');
  const show = typeof open === 'boolean' ? open : !c.classList.contains('show');
  if (show) renderAgreement();
  if (show) { c.style.visibility = 'visible'; requestAnimationFrame(() => c.classList.add('show')); bd.hidden = false; requestAnimationFrame(() => bd.classList.add('show')); }
  else { c.classList.remove('show'); bd.classList.remove('show'); setTimeout(() => { if (!c.classList.contains('show')) { c.style.visibility = 'hidden'; bd.hidden = true; } }, 300); }
}
__on('keydown', e => { if (e.key === 'Escape' && SMDOC.getElementById('settingsCard').classList.contains('show')) toggleSettings(false); });

// ── criteria pick list (step 1) ──
const aimLetters = full => {
  const { unit, code } = splitUnit(full);
  const pre = ((code || '').split('.')[0] || '').toUpperCase();
  const keys = /^\d+$/.test(pre) ? ['#' + pre]               // TLM learning outcome number
    : pre.replace(/^\d/, '').split('').filter(ch => /[A-Z]/.test(ch));
  return unit ? keys.map(k => `${unit}:${k}`) : keys;
};
function bandLabel(b) {
  const lv = currentLevel();
  if (lv && lv.grading === 'pass') return 'Pass';
  if (lv && lv.grading === 'first') return { L1: 'Level 1', P: 'L2 Pass', M: 'L2 Merit', D: 'L2 Distinction' }[b];
  return { L1: 'Level 1', P: 'Pass', M: 'Merit', D: 'Distinction' }[b];
}
const aimLabel = key => {
  const m = key.match(/^([A-Z]{1,6}\d+):(.+)$/);
  const k = m ? m[2] : key;
  const base = k[0] === '#' ? `Outcome ${k.slice(1)}` : `Learning aim ${k}`;
  return m ? `${tagName(m[1])} ${base.charAt(0).toLowerCase() + base.slice(1)}` : base;
};
function setPick(list) { pick = list; renderPick(); markCriteriaChanged(); }
function addPick(item) {
  if (!item.code) return;
  const existing = pick.find(p => p.code.toLowerCase() === item.code.toLowerCase());
  if (existing) { existing.on = true; if (item.desc) existing.desc = item.desc; } else pick.push(item);
  renderPick(); markCriteriaChanged();
}
function addCustomCriterion() {
  const code = SMDOC.getElementById('newCritCode').value.trim();
  const desc = SMDOC.getElementById('newCritDesc').value.trim();
  if (!code) { toast('Enter a criterion code, e.g. A.P1', 'error'); return; }
  addPick({ code, desc, on: true });
  SMDOC.getElementById('newCritCode').value = ''; SMDOC.getElementById('newCritDesc').value = '';
}
function renderPick() {
  const box = SMDOC.getElementById('critPick');
  const chips = SMDOC.getElementById('aimChips');
  if (!pick.length) { box.innerHTML = '<p class="helper">Choose a unit above to load its criteria.</p>'; chips.innerHTML = ''; refreshNav(); return; }
  box.innerHTML = '';
  const multi = new Set(pick.map(p => p.unit || '')).size > 1;
  let lastUnit = null;
  pick.forEach((p, i) => {
    if (multi && p.unit !== lastUnit) {
      lastUnit = p.unit;
      const h = document.createElement('div'); h.className = 'unit-head'; h.textContent = p.unitTitle || p.unit; box.appendChild(h);
    }
    const band = bandOf(p.code) || '';
    const lab = document.createElement('label');
    lab.className = 'list-group-item list-group-item-action crit-item';
    lab.innerHTML = `<input class="form-check-input" type="checkbox" ${p.on ? 'checked' : ''}><span class="cc"></span><span class="cd"></span>${band ? `<span class="band band-${band}">${bandLabel(band)}</span>` : ''}`;
    lab.querySelector('.cc').textContent = p.code;
    lab.querySelector('.cd').textContent = p.desc || '(no wording)';
    lab.querySelector('input').onchange = e => { p.on = e.target.checked; renderChips(); markCriteriaChanged(); refreshNav(); };
    box.appendChild(lab);
  });
  renderChips(); refreshNav();
}
function renderChips() {
  const chips = SMDOC.getElementById('aimChips');
  const letters = [...new Set(pick.flatMap(p => aimLetters(p.code)))].sort();
  const html = [`<button class="btn btn-sm chip" data-sm-click="pickAll(true)">All</button>`, `<button class="btn btn-sm chip" data-sm-click="pickAll(false)">None</button>`];
  letters.forEach(l => {
    const own = pick.filter(p => aimLetters(p.code).includes(l));
    const allOn = own.length && own.every(p => p.on);
    html.push(`<button class="btn btn-sm chip ${allOn ? 'on' : ''}" data-sm-click="toggleAim('${l}')">${aimLabel(l)}</button>`);
  });
  chips.innerHTML = html.join('');
}
function pickAll(on) { pick.forEach(p => p.on = on); renderPick(); markCriteriaChanged(); }
function toggleAim(letter) {
  // selected aims = letters whose own criteria are all on, toggled by this click
  const letters = [...new Set(pick.flatMap(p => aimLetters(p.code)))];
  const isOn = l => { const own = pick.filter(p => aimLetters(p.code).length === 1 && aimLetters(p.code)[0] === l); return own.length && own.every(p => p.on); };
  const selected = new Set(letters.filter(isOn));
  if (selected.has(letter)) selected.delete(letter); else selected.add(letter);
  pick.forEach(p => { const ls = aimLetters(p.code); p.on = ls.length > 0 && ls.every(l => selected.has(l)); });
  renderPick(); markCriteriaChanged();
}
function markCriteriaChanged() { if (aiState === 'done') aiStale = true; }

// Build the grading table from the ticked criteria, keeping any decisions already made
function syncRowsFromPick() {
  const keep = {};
  SMDOC.querySelectorAll('#criteriaBody .crit-row').forEach(tr => {
    keep[tr.querySelector('.code-input').value.trim().toLowerCase()] = {
      achieved: tr.querySelector('.achieved-sel').value,
      comment: tr.querySelector('.desc-input').value,
      ai: tr.querySelector('.ai-cell').innerHTML,
    };
  });
  const tbody = SMDOC.getElementById('criteriaBody');
  tbody.innerHTML = '';
  pick.filter(p => p.on).forEach(p => {
    const k = keep[p.code.toLowerCase()];
    addRow(p.code, p.desc, k ? k.achieved : '', k ? k.comment : '');
    if (k) tbody.lastElementChild.querySelector('.ai-cell').innerHTML = k.ai;
  });
  applyPrevTags();
  updateGrade();
}

// ── upload → integrity + AI automatically (step 3) ──
function setProgress(id, state) {
  const li = SMDOC.getElementById(id);
  if (li) li.className = state || '';
}
function onFileReady() {
  SMDOC.getElementById('uploadProgress').style.display = 'flex';
  setProgress('pFile', 'ok');
  if (!lastIntegrity && !integrityFailed) setProgress('pIntegrity', 'run');
  // the AI check runs only when the assessor asks for it (or has said "always")
  const mode = checkMode();
  if (mode === 'ai') runCheck();
  else if (mode === 'manual') startManual(false);
  else { aiState = 'choose'; renderAiStatus(); }
  renderWork();
  scheduleSave();
  maybeAdvanceAfterUpload();
}
function maybeAdvanceAfterUpload() {
  refreshNav();
  // move on to the checks once the file details are ready (AI keeps working there)
  if (currentStep === 3 && fileBase64 && (lastIntegrity || integrityFailed)) setTimeout(() => { if (currentStep === 3) goStep(aiState === 'manual' ? 5 : 4); }, 600);
}

function renderAiStatus() {
  const el = SMDOC.getElementById('aiStatus');
  if (!el) return;
  let html = '';
  if (aiState === 'running') html = `<div class="card"><div class="status" style="padding:0.5rem"><span class="spinner-border spinner-border-sm text-primary me-2" role="status" aria-hidden="true"></span>The AI is assessing each criterion. This usually takes 20 to 60 seconds; the file checks below are ready to read now.</div>${privacySummaryHtml()}<button class="btn btn-link btn-sm linkish" style="margin-top:6px" data-sm-click="unlockGrading(true); aiState='idle'; renderAiStatus(); goStep(5)">Don't wait, grade without the AI</button></div>`;
  else if (aiState === 'error') html = `<div class="card"><p style="color:var(--no);font-size:13px;margin-bottom:8px"><strong>The AI check didn't complete:</strong> ${escapeHtml(aiError)}</p>${getApiKey() ? '' : '<button class="btn btn-sm btn-outline-secondary btn-csv" data-sm-click="openKeySettings()">Add API key</button> '}<button class="btn btn-sm btn-outline-secondary btn-csv" data-sm-click="runCheck()">Try again</button> <button class="btn btn-link btn-sm linkish" data-sm-click="unlockGrading(true); goStep(5)">Grade without the AI</button></div>`;
  else if (aiState === 'done' && aiStale) html = `<div class="card"><p style="color:var(--resub);font-size:13px;margin-bottom:8px">The targeted criteria changed after the AI check.</p><button class="btn btn-sm btn-outline-secondary btn-csv" data-sm-click="runCheck()">Re-run the AI check</button></div>`;
  else if (aiState === 'idle' && fileBase64) html = `<div class="card"><button class="btn btn-sm btn-outline-secondary btn-csv" data-sm-click="runCheck()">Run the AI check</button></div>`;
  else if (aiState === 'choose') html = `<div class="card choose-card">
      <div class="step-title">How do you want to mark this?</div>
      <p class="helper" style="margin:0">The file checks below are done. Ask the second marker for a first opinion, or go straight to the marking desk. You can still ask the second marker later.</p>
      <div class="choose-grid">
        <button type="button" class="choice ai" data-sm-click="chooseMarking('ai')"><b><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16.5 3.5l4 4L8 20H4v-4z"/></svg>Check it with the second marker</b><span>The AI suggests Y or N for each criterion, comments on the work and drafts the feedback. Names, IDs and contact details are hidden first. Takes 20 to 60 seconds.</span></button>
        <button type="button" class="choice me" data-sm-click="chooseMarking('manual')"><b><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>Mark it myself</b><span>Open the marking desk now. Stamp each criterion and add your own comments to the work.</span></button>
      </div>
      <label class="remember"><input class="form-check-input" type="checkbox" id="rememberChoice"> Do this every time</label>
    </div>`;
  else if (aiState === 'blocked') {
    const split = fileScan && fileScan.turnitin && fileScan.learnerText && !fileScanMode;
    const first = split
      ? `<div class="blocked-main"><button class="btn btn-primary btn-sm btn-export" data-sm-click="sendLearnerOnly()">Send only the learner's writing</button><span class="helper" style="margin:0">${fileScan.learnerWords.toLocaleString('en-GB')} words from ${plural(fileScan.learnerPages, 'page')}</span></div>`
      : '';
    html = `<div class="card blocked-card"><div class="step-title">Check this file before the second marker sees it</div><p class="helper" style="margin:0 0 12px">${escapeHtml(aiError)}</p>${first}<div class="blocked-more">${split ? '' : '<button class="btn btn-sm btn-outline-secondary btn-csv" data-sm-click="sendAnyway()">Send it anyway</button>'}<button class="btn btn-sm btn-outline-secondary btn-csv" data-sm-click="startManual(true)">Mark it myself</button><button class="btn btn-link btn-sm linkish" data-sm-click="goStep(3)">Upload a different file</button></div></div>`;
  }
  else if (aiState === 'manual') html = `<div class="card"><div class="status">You're marking this yourself.</div><button class="btn btn-link btn-sm linkish" style="margin-top:4px" data-sm-click="askSecondMarker()">Ask the second marker after all</button></div>`;
  el.innerHTML = html;
  const pt = SMDOC.getElementById('pAiText');
  if (pt) pt.textContent = aiState === 'choose' ? 'Next: choose the second marker or mark it yourself' : aiState === 'manual' ? 'Marking it yourself' : aiState === 'blocked' ? 'Waiting: check the file before sending it' : 'Second marker assessing each criterion';
}

// ── navigation ──
function stepProblem(n) {
  if (n === 1) {
    if (!pick.some(p => p.on)) return 'Choose a unit and tick at least one criterion.';
  }
  if (n === 3) {
    if (!fileBase64) return 'Upload the learner\'s work to continue.';
  }
  if (n === 4) {
    if (aiState === 'running') return 'The AI is still assessing. Wait a moment, or choose "Don\'t wait" above.';
    if (aiState === 'choose') return 'Choose how you want to mark this work.';
    if (aiState === 'blocked') return 'Send the file anyway, mark it yourself, or upload a different file.';
  }
  if (n === 5) {
    const ungraded = getCriteria().filter(c => c.code && !c.achieved).map(c => c.code);
    if (!gradingUnlocked) return 'Waiting for the AI check, or choose "Grade without the AI check".';
    if (ungraded.length) return `Set Criteria achieved for ${ungraded.join(', ')}.`;
  }
  return '';
}

function goStep(n) {
  if (n < 1 || n > STEPS.length) return;
  if (n > currentStep) {
    for (let s = currentStep; s < n; s++) {
      const p = stepProblem(s);
      if (p) { SMDOC.getElementById('navMsg').textContent = p; return; }
    }
  }
  if (currentStep === 1 && n > 1) syncRowsFromPick();
  currentStep = n;
  maxStep = Math.max(maxStep, n);
  SMDOC.querySelectorAll('.step').forEach(sec => sec.classList.toggle('active', +sec.dataset.step === n));
  SMROOT.dataset.step = n;
  SMROOT.classList.toggle('focus-on', n === 5 && typeof isFocus === 'function' && isFocus());
  if (n === 4) renderAiStatus();
  if (n === 5) { syncRowsFromPick(); if (aiState === 'done' && !gradingUnlocked) unlockGrading(false); enterDesk(); }
  if (n === 1) { renderReferences(); renderBrief(); onBriefText(); }
  if (n === 2) { renderResub(); renderLateStatus(); }
  if (n === 6) { renderExportSummary(); updateDownloadOptions(); composeEmail(false); }
  renderStepper(); refreshNav();
  window.scrollTo({ top: 0, behavior: 'smooth' });
  saveDraft();
}

const DROP_ICON = '<svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/><path d="M12 17v-6M9 14l3-3 3 3"/></svg>';
// Header line: what's being marked right now
function updateContext() {
  const el = SMDOC.getElementById('ctxLine'); if (!el) return;
  const si = SMDOC.getElementById('subjectSelect').value;
  const lv = currentLevel();
  const units = (selectedUnits || []).map(i => lv && lv.units[i] ? cleanTitle(lv.units[i].title) : '').filter(Boolean);
  const top = [lv ? lv.level : '', si !== '' ? window.BTEC_DATA[+si].subject : ''].filter(Boolean).join(' ');
  const who = SMDOC.getElementById('student_name').value.trim();
  el.querySelector('.c1').textContent = top ? (who ? `${top}, marking ${who}` : top) : 'BTEC assignment checker';
  el.querySelector('.c2').textContent = units.length ? units.join(' + ') : 'Choose a unit to start';
  el.querySelector('.c2').title = units.join(' + ');
}
function renderStepper() {
  const ol = SMDOC.getElementById('stepper');
  ol.innerHTML = STEPS.map((t, i) => {
    const n = i + 1;
    const cls = n === currentStep ? 'active' : n <= maxStep ? 'done' : '';
    const inner = n <= maxStep && n !== currentStep ? `<button type="button" data-sm-click="goStep(${n})">${t}</button>` : `<span class="sl">${t}</span>`;
    return `<li class="${cls}" ${n === currentStep ? 'aria-current="step"' : ''}>${inner}<span class="bar"></span></li>`;
  }).join('');
}

function refreshNav() {
  updateContext();
  const back = SMDOC.getElementById('backBtn'), next = SMDOC.getElementById('nextBtn'), msg = SMDOC.getElementById('navMsg');
  if (!back) return;
  back.style.visibility = currentStep === 1 ? 'hidden' : 'visible';
  next.style.display = currentStep === STEPS.length ? 'none' : '';
  const labels = { 1: 'Continue to learner details', 2: 'Continue to upload', 3: 'Continue to checks', 4: ivMode ? 'Continue to compare' : 'Continue to grading', 5: 'Review and export' };
  next.textContent = labels[currentStep] || 'Continue';
  const p = stepProblem(currentStep);
  next.disabled = !!p;
  msg.textContent = '';
  msg.style.color = '#888';
  if (p && currentStep !== 2) msg.textContent = p;
}

// Re-check the nav whenever a grade changes
const _updateGrade = updateGrade;
updateGrade = function () { _updateGrade(); if (typeof refreshDesk === 'function') refreshDesk(); if (typeof warnLateResub === 'function') warnLateResub(); if (typeof composeFeedback === 'function') composeFeedback(false); if (typeof composeStaffNote === 'function') composeStaffNote(false); if (typeof refreshNav === 'function' && SMDOC.getElementById('nextBtn')) refreshNav(); };

// ── export summary (step 6) ──
function renderExportSummary() {
  const h = getHeaderData();
  const rows = getCriteria().filter(c => c.code);
  const g = gradeOf(rows, r => r.achieved === 'Y');
  const fb = lastFeedback || '';
  const flags = lastIntegrity ? lastIntegrity.flags : [];
  const high = flags.filter(f => f.level === 'high').length, med = flags.filter(f => f.level === 'medium').length;
  const label = { Y: 'Y', N: 'N', R: 'R' };
  SMDOC.getElementById('exportSummary').innerHTML = `
    <div class="step-title">Ready to export</div>
    <div class="sum-grid">
      <div><span class="k">Learner</span><br>${escapeHtml(h.student_name || 'Not entered')} ${h.student_reg ? '(' + escapeHtml(h.student_reg) + ')' : ''}</div>
      <div><span class="k">Unit</span><br>${escapeHtml(h.unit_title || '')}</div>
      <div><span class="k">Assignment</span><br>${escapeHtml(h.assignment_title || 'Not entered')}</div>
      <div><span class="k">Assessor</span><br>${escapeHtml(h.assessor_name || 'Not entered')}</div>
      <div><span class="k">Submission</span><br>${(() => { const st = submissionStatus(); return st.state === 'unknown' ? (h.date_submitted ? escapeHtml(h.date_submitted) : 'Date not entered') : lateChip(st); })()}</div>
      <div><span class="k">Grade</span><br><span class="badge rounded-pill grade-pill g-${g.grade}">${g.label}</span></div>
      <div><span class="k">Integrity</span><br>${lastIntegrity ? (high ? `<span style="color:var(--no)">${high} strong indicator(s)</span>` : med ? `<span style="color:var(--resub)">${med} moderate indicator(s)</span>` : 'No strong indicators') : 'Not checked'}</div>
    </div>
    <table class="table table-sm result-table" style="margin-bottom:12px">
      <thead><tr><th style="width:90px">Criteria</th><th style="width:80px">Achieved</th><th>Comment</th></tr></thead>
      <tbody>${rows.map(r => `<tr><td><span class="code-tag">${escapeHtml(r.code)}</span></td><td><span class="badge rounded-pill badge-${r.achieved.toLowerCase()}">${label[r.achieved] || '?'}</span></td><td style="color:var(--ink-2)">${escapeHtml(r.comment)}</td></tr>`).join('')}</tbody>
    </table>
    <div class="label">General comments</div>
    <div class="feedback-box" style="margin-bottom:6px">${escapeHtml(fb) || '<span class="helper">No feedback written.</span>'}</div>
    ${rows.length > 3 ? `<p class="helper">The Pearson form has three criteria rows; ${rows.length - 3} more will be added to General comments.</p>` : ''}
    <div class="label" style="margin-top:12px">Note for other lecturers</div>
    <div class="staff-box">${escapeHtml(staffNoteText()) || '<span class="helper">No note written.</span>'}</div>
    <button class="btn btn-link btn-sm linkish" style="margin-top:6px" data-sm-click="navigator.clipboard && navigator.clipboard.writeText(staffNoteText()).then(() => { this.textContent = 'Copied'; })">Copy note</button>`;
}
function staffNoteText() { return noDashes(((SMDOC.getElementById('staffNote') || {}).value || '').trim()); }

// ── start the next learner on the same unit ──
// ── start again ──
function openRestart() {
  const d = SMDOC.getElementById('restartDialog');
  const who = SMDOC.getElementById('student_name').value.trim();
  const busy = !!(fileBase64 || who || getCriteria().some(c => c.achieved));
  SMDOC.getElementById('restartNote').textContent = (busy ? `This clears ${who ? who + "'s" : 'the current'} marking. ` : '') + 'Your settings stay as they are.';
  SMDOC.getElementById('restartLearnerBtn').disabled = !pick.some(p => p.on);
  if (d.showModal) d.showModal(); else d.setAttribute('open', '');
}
function closeRestart() { const d = SMDOC.getElementById('restartDialog'); if (d.close) d.close(); else d.removeAttribute('open'); }
function restartLearner() {
  closeRestart();
  if (isFocus()) toggleFocus(false);
  nextLearner();
  SMDOC.getElementById('student_name').focus();
  toast('Ready for the next learner on the same unit.', 'ok');
}
async function restartAll() {
  closeRestart();
  resetting = true; clearTimeout(draftTimer); pendingDraft = null;
  try { if (window.indexedDB) await draftDb('readwrite', st => st.delete('current')); } catch (e) {}
  storeDel('btec_focus');
  __restart();
}

function nextLearner() {
  lateResubWarned = false;
  ['student_name', 'student_reg', 'student_email', 'date_submitted', 'extension', 'extension_date'].forEach(id => SMDOC.getElementById(id).value = '');
  emailEdited = false; emailManual = false;
  fileBase64 = null; fileMediaType = null; fileNameStr = null; lastIntegrity = null; integrityFailed = false;
  SMDOC.getElementById('fileInput').value = '';
  SMDOC.getElementById('docLink').value = ''; setLinkStatus('');
  dropZone.classList.remove('has-file');
  SMDOC.getElementById('dropIcon').innerHTML = DROP_ICON;
  SMDOC.getElementById('dropText').innerHTML = 'Drop a Word or PDF file here, or <strong>click to browse</strong>';
  SMDOC.getElementById('uploadProgress').style.display = 'none';
  ['pFile', 'pIntegrity', 'pAi'].forEach(id => setProgress(id, ''));
  renderIntegrity(null);
  resetAi();
  lastAiIntegrity = null;
  resub = newResub(); renderResub();
  if (learnerMode === 'anon') { anonRef = ''; setLearnerMode('anon'); }
  clearDraft();
  gradingUnlocked = false;
  SMDOC.getElementById('skipAiBtn').style.display = '';
  SMDOC.getElementById('gradeNote').textContent = 'Waiting for the AI check.';
  SMDOC.getElementById('criteriaBody').innerHTML = '';
  work.key = ''; renderWork();
  syncRowsFromPick();
  maxStep = 2;
  goStep(2);
}

// ═════════════════════════ Assignment brief ═════════════════════════
function addBrief(f) {
  if (!f) return;
  const isPdf = f.type === 'application/pdf' || /\.pdf$/i.test(f.name), isDocx = /\.docx$/i.test(f.name);
  if (!isPdf && !isDocx) { toast('The brief needs to be a PDF or Word (.docx) file.', 'error'); return; }
  if (f.size > MAX_REF_MB * 1048576) { toast(`The brief is over ${MAX_REF_MB} MB. Save a smaller copy, e.g. a PDF without large images.`, 'error'); return; }
  const r = new FileReader();
  r.onload = ev => {
    brief = { name: f.name, kind: isPdf ? 'pdf' : 'docx', base64: String(ev.target.result).split(',')[1] };
    renderBrief(); markCriteriaChanged(); scheduleSave();
    toast(`Brief added: ${f.name}`, 'ok');
  };
  r.readAsDataURL(f);
}
function removeBrief() { brief = null; renderBrief(); onBriefText(); scheduleSave(); }
function renderBrief() {
  const chip = SMDOC.getElementById('briefChip'), btn = SMDOC.getElementById('briefBtn');
  if (!chip) return;
  chip.innerHTML = brief ? `<span class="file-chip"><span></span><button class="btn btn-sm btn-remove" title="Remove the brief" data-sm-click="removeBrief()">&#215;</button></span>` : '';
  if (brief) chip.querySelector('span span').textContent = brief.name;
  btn.textContent = brief ? 'Replace the brief' : 'Add the brief';
}
const briefPasted = () => ((SMDOC.getElementById('brief_text') || {}).value || '').trim();
function onBriefText() {
  const t = briefPasted(), note = SMDOC.getElementById('briefTextNote');
  if (note) note.textContent = t ? `${t.split(/\s+/).length} words pasted.${brief ? ' Sent alongside the uploaded file.' : ''} Kept for the next learner.` : '';
  markCriteriaChanged();
}
async function briefBlocks() {
  const title = 'Assignment brief';
  const context = 'The assignment brief the learner was set. Use it to see what each task asked for and which criteria each task targets. Do not assess it, and never quote it in annotations.';
  const blocks = [];
  if (brief && brief.base64) {
    if (brief.kind === 'pdf') blocks.push({ type: 'document', source: { type: 'base64', media_type: 'application/pdf', data: brief.base64 }, title, context });
    else { const text = await docxToText(brief.base64).catch(() => ''); if (text) blocks.push({ type: 'document', source: { type: 'text', media_type: 'text/plain', data: text }, title, context }); }
  }
  const lib = currentBuiltinBrief();
  if (lib) blocks.push({ type: 'document', source: { type: 'text', media_type: 'text/plain', data: builtinBriefText(lib) }, title: blocks.length ? 'Assignment brief (approved copy)' : title, context });
  const pasted = briefPasted();
  if (pasted) blocks.push({ type: 'document', source: { type: 'text', media_type: 'text/plain', data: pasted }, title: blocks.length ? 'Assignment brief (pasted text)' : title, context });
  return blocks;
}

// ═════════════════════════ Deadlines, extensions and late work ═════════════════════════
const MONTHS = { jan: 0, feb: 1, mar: 2, apr: 3, may: 4, jun: 5, jul: 6, aug: 7, sep: 8, sept: 8, oct: 9, nov: 10, dec: 11 };
// UK dates: 02/10/2026, 2/10/26, 2-10-2026, 2.10.2026, 2026-10-02, 2 Oct 2026, 2nd October 2026
function parseUkDate(v) {
  const t = String(v || '').trim().toLowerCase();
  if (!t) return null;
  let d, m, y, x;
  if ((x = t.match(/^(\d{4})-(\d{1,2})-(\d{1,2})/))) { y = +x[1]; m = +x[2] - 1; d = +x[3]; }
  else if ((x = t.match(/^(\d{1,2})[\/.\-](\d{1,2})[\/.\-](\d{2,4})$/))) { d = +x[1]; m = +x[2] - 1; y = +x[3]; }
  else if ((x = t.match(/^(\d{1,2})(?:st|nd|rd|th)?\s+([a-z]{3,9})\.?,?\s+(\d{2,4})$/)) && MONTHS[x[2].slice(0, x[2].startsWith('sept') ? 4 : 3)] !== undefined) { d = +x[1]; m = MONTHS[x[2].slice(0, x[2].startsWith('sept') ? 4 : 3)]; y = +x[3]; }
  else return null;
  if (y < 100) y += 2000;
  const dt = new Date(Date.UTC(y, m, d));
  return dt.getUTCFullYear() === y && dt.getUTCMonth() === m && dt.getUTCDate() === d ? dt : null;
}
const fmtUk = dt => dt ? `${String(dt.getUTCDate()).padStart(2, '0')}/${String(dt.getUTCMonth() + 1).padStart(2, '0')}/${dt.getUTCFullYear()}` : '';
const dayDiff = (a, b) => Math.round((a - b) / 86400000);
const plural = (n, w) => `${n} ${w}${n === 1 ? '' : 's'}`;
function todayUk() { const n = new Date(); return fmtUk(new Date(Date.UTC(n.getFullYear(), n.getMonth(), n.getDate()))); }
function setSubmittedToday() { const el = SMDOC.getElementById('date_submitted'); el.value = todayUk(); renderLateStatus(); scheduleSave(); }

function submissionStatus() {
  const val = id => (SMDOC.getElementById(id) || {}).value || '';
  const deadline = parseUkDate(val('deadline')), submitted = parseUkDate(val('date_submitted'));
  const ext = val('extension'), extDate = parseUkDate(val('extension_date'));
  const effective = ext === 'Yes' && extDate ? extDate : deadline;
  const st = { deadline, submitted, ext, extDate, effective, state: 'unknown', days: 0, fileLate: 0, fileSaved: null };
  // the file's own last-saved date, when it's later than the deadline
  const mod = lastIntegrity && lastIntegrity.meta && lastIntegrity.meta.modified;
  if (mod) { const fd = new Date(mod); if (!isNaN(fd)) { st.fileSaved = new Date(Date.UTC(fd.getFullYear(), fd.getMonth(), fd.getDate())); if (effective) st.fileLate = Math.max(0, dayDiff(st.fileSaved, effective)); } }
  if (!effective || !submitted) return st;
  st.days = dayDiff(submitted, effective);
  if (st.days > 0) st.state = ext === 'Yes' && extDate ? 'ext-late' : 'late';
  else st.state = ext === 'Yes' && extDate ? 'ext-ontime' : 'ontime';
  return st;
}
// One plain sentence for the record and the note to other lecturers ('' when on time with no extension)
function lateSentence(st) {
  st = st || submissionStatus();
  if (st.state === 'late') return `Submitted ${fmtUk(st.submitted)}, ${plural(st.days, 'day')} after the deadline of ${fmtUk(st.deadline)}. No extension was agreed.`;
  if (st.state === 'ext-late') return `Submitted ${fmtUk(st.submitted)}, ${plural(st.days, 'day')} after the agreed extension to ${fmtUk(st.extDate)}.`;
  if (st.state === 'ext-ontime') return `Submitted ${fmtUk(st.submitted)}, within the agreed extension to ${fmtUk(st.extDate)}.`;
  return '';
}
const isLateNoResub = st => st.state === 'late' || st.state === 'ext-late';
function lateChip(st) {
  if (st.state === 'ontime') return '<span class="late-chip ok">On time</span>';
  if (st.state === 'ext-ontime') return '<span class="late-chip ext">On time, with extension</span>';
  if (st.state === 'late') return `<span class="late-chip late">${plural(st.days, 'day')} late</span>`;
  if (st.state === 'ext-late') return `<span class="late-chip late">${plural(st.days, 'day')} after the extension</span>`;
  return '';
}
function renderLateStatus() {
  const ext = SMDOC.getElementById('extension'), extDate = SMDOC.getElementById('extension_date');
  if (ext && extDate) extDate.hidden = ext.value !== 'Yes';
  const el = SMDOC.getElementById('lateStatus');
  if (!el) return;
  const st = submissionStatus(), bits = [];
  const deadlineText = (SMDOC.getElementById('deadline') || {}).value || '', subText = (SMDOC.getElementById('date_submitted') || {}).value || '';
  if (deadlineText && !st.deadline) bits.push("<span>The deadline isn't a date the app can read. Use the format 02/10/2026.</span>");
  if (subText && !st.submitted) bits.push("<span>The date submitted isn't a date the app can read. Use the format 02/10/2026.</span>");
  if (ext && ext.value === 'Yes' && !st.extDate) bits.push('<span>Add the new deadline for the extension.</span>');
  if (st.state !== 'unknown') {
    bits.unshift(lateChip(st));
    if (isLateNoResub(st)) bits.push('<span>Pearson only allows a resubmission when the work met the deadline or an agreed extension.</span>');
    else if (st.state === 'ext-ontime') bits.push(`<span>Handed in by the extended deadline of ${fmtUk(st.extDate)}.</span>`);
  } else if (st.effective && !subText) bits.push('<span class="late-chip unknown">Date submitted not entered</span>');
  if (st.fileLate > 0 && st.state !== 'late' && st.state !== 'ext-late') bits.push(`<span>The file itself was last saved on ${fmtUk(st.fileSaved)}, ${plural(st.fileLate, 'day')} after the ${st.ext === 'Yes' && st.extDate ? 'extended ' : ''}deadline.</span>`);
  el.innerHTML = bits.join('');
  if (typeof composeStaffNote === 'function') composeStaffNote(false);
}
let lateResubWarned = false;
function warnLateResub() {
  if (lateResubWarned || !isLateNoResub(submissionStatus())) return;
  if (!getCriteria().some(c => c.achieved === 'R')) return;
  lateResubWarned = true;
  toast('This work was handed in late without an agreed extension. Pearson only allows a resubmission when the deadline or an extension was met.', 'warn');
}

// ═════════════════════════ Learner details: named or anonymous ═════════════════════════
var learnerMode = 'named', anonRef = '';
var ivMode = false;   // IV sampling: see iv.js
function newAnonRef() {
  const abc = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ', n = new Uint8Array(4);
  (window.crypto || window.msCrypto).getRandomValues(n);
  return 'ANON-' + [...n].map(x => abc[x % abc.length]).join('');
}
function setLearnerMode(mode, ref) {
  const name = SMDOC.getElementById('student_name'), reg = SMDOC.getElementById('student_reg'), mail = SMDOC.getElementById('student_email');
  const was = learnerMode;
  learnerMode = mode === 'anon' ? 'anon' : 'named';
  storeSet('btec_learner_mode', learnerMode);
  SMDOC.querySelectorAll('input[name="learnerMode"]').forEach(r => r.checked = r.value === learnerMode);
  SMDOC.querySelector('.step[data-step="2"]').classList.toggle('anon', learnerMode === 'anon');
  SMDOC.getElementById('anonBox').hidden = learnerMode !== 'anon';
  if (learnerMode === 'anon') {
    anonRef = ref || anonRef || newAnonRef();
    name.value = 'Anonymous learner'; reg.value = anonRef; mail.value = ''; emailManual = false;
    const to = SMDOC.getElementById('emailTo'); if (to) to.value = '';
    SMDOC.getElementById('anonRefText').textContent = anonRef;
  } else if (was === 'anon') {
    // back to named details: clear the placeholders
    if (name.value === 'Anonymous learner') name.value = '';
    if (/^ANON-/.test(reg.value)) reg.value = '';
    anonRef = '';
  }
  updateContext();
  if (typeof renderLearnerStrip === 'function' && currentStep === 5) renderLearnerStrip();
  scheduleSave();
}

// ═════════════════════════ Keeping personal details away from the AI ═════════════════════════
// Everything sent to the second marker goes through redactForAi(). Names, IDs and contact details
// become placeholders such as [Learner] or [Person 1]; the AI's answers are put back with restoreFromAi().
var privacyMap = [], privacyReport = null;
function pdfPrivacy() { return storeGet('btec_pdf_privacy') === 'full' ? 'full' : 'text'; }
function setPdfPrivacy(v) { storeSet('btec_pdf_privacy', v === 'full' ? 'full' : 'text'); SMDOC.querySelectorAll('input[name="pdfPrivacy"]').forEach(r => r.checked = r.value === pdfPrivacy()); }
// software and generic labels that turn up as a file's author but aren't people
const NOT_A_PERSON = /python-docx|openpyxl|microsoft|libreoffice|openoffice|google docs|adobe|acrobat|^(word|office|windows|apple|mac|user|admin|administrator|owner|unknown|default|student|teacher|pc|laptop|author)( user)?$|^\W*$/i;
const escRe = x => x.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
function buildPrivacyTerms(ctx) {
  // ctx (optional): { name, reg, email, hideExtra, integrity, mode } for a learner who isn't the one open
  const terms = [], seen = new Set();
  const addName = (full, token) => {
    const n = String(full || '').replace(/\s+/g, ' ').trim();
    if (n.length < 3 || /\d/.test(n) || seen.has(n.toLowerCase())) return;
    if (token !== '[Learner]' && NOT_A_PERSON.test(n)) return;
    seen.add(n.toLowerCase());
    terms.push({ re: new RegExp(`(?<![\\p{L}])${escRe(n).replace(/ /g, '\\s+')}(?![\\p{L}])`, 'giu'), token, label: token === '[Learner]' ? "the learner's name" : 'other names', original: n });
    const words = n.split(' ');
    if (words.length > 1) words.forEach((part, wi) => {
      if (part.length < 3 || !/^\p{Lu}/u.test(part) || seen.has(part.toLowerCase())) return;
      seen.add(part.toLowerCase());
      const which = wi === 0 ? 'first name' : wi === words.length - 1 ? 'surname' : 'middle name';
      terms.push({ re: new RegExp(`(?<![\\p{L}])${escRe(part)}(?![\\p{L}])`, 'gu'), token: token.slice(0, -1) + ' ' + which + ']', label: token === '[Learner]' ? "the learner's name" : 'other names', original: part });
    });
  };
  const h = ctx ? { student_name: ctx.name || '', student_reg: ctx.reg || '', student_email: ctx.email || '' } : getHeaderData();
  const mode = ctx ? (ctx.mode || 'named') : learnerMode;
  const integ = ctx ? ctx.integrity : lastIntegrity;
  if (mode === 'named') {
    addName(h.student_name, '[Learner]');
    if (h.student_reg && h.student_reg.length >= 4) terms.push({ re: new RegExp(`(?<![\\w])${escRe(h.student_reg)}(?![\\w])`, 'gi'), token: '[Learner ID]', label: "the learner's ID", original: h.student_reg });
    if (h.student_email) terms.push({ re: new RegExp(escRe(h.student_email), 'gi'), token: '[Email hidden]', label: 'email addresses', original: h.student_email });
  }
  // other people named in the file's own details (often the learner, sometimes a template's author), and any the assessor lists
  const people = String(ctx ? ctx.hideExtra || '' : (SMDOC.getElementById('hide_extra') || {}).value || '').split(/[,;\n]+/).map(x => x.trim()).filter(Boolean);
  if (integ) {
    const m = integ.meta || {}, hi = integ.history || {};
    people.push(m.author, m.lastModifiedBy, ...(hi.commentAuthors || []), ...(hi.revisionAuthors || []));
    const c = integ.cloud;
    if (c) people.push(c.owner, c.lastEditor, ...((c.revisions || []).map(r => r.who)));
  }
  let k = 0;
  people.filter(Boolean).forEach(n => {
    const nn = String(n).trim();
    if (mode === 'named' && h.student_name && nn.toLowerCase() === h.student_name.toLowerCase()) return;
    if (!seen.has(nn.toLowerCase()) && !NOT_A_PERSON.test(nn) && !/\d/.test(nn) && nn.length >= 3) addName(nn, `[Person ${++k}]`);
  });
  terms.sort((a, b) => b.original.length - a.original.length);
  const tokens = new Set();
  return terms.filter(t => !tokens.has(t.token + '|' + t.original) && tokens.add(t.token + '|' + t.original));
}
const PRIVACY_PATTERNS = [
  { re: /[\w.+'-]+@[\w-]+(?:\.[\w-]+)+/g, token: '[Email hidden]', label: 'email addresses' },
  { re: /(?:(?:\+44\s?|\b0)7\d{3}|\b0\d{2,4})[\s-]?\d{3}[\s-]?\d{3,4}\b/g, token: '[Phone hidden]', label: 'phone numbers' },
  { re: /\b[A-Z]{1,2}\d[A-Z\d]?\s?\d[A-Z]{2}\b/g, token: '[Postcode hidden]', label: 'postcodes' },
  { re: /(?<!\w|\d[.,])\d{8}(?!\w|[.,]\d)/g, token: '[ID hidden]', label: 'student ID numbers' },
];
function startPrivacy() { privacyMap = buildPrivacyTerms(); privacyReport = { counts: {}, pdf: '', notes: [] }; }
function redactForAi(text, map, report) {
  if (!text) return text;
  const M = map || privacyMap, R = report === undefined ? privacyReport : report;
  let out = String(text);
  const count = (label, n) => { if (n && R) R.counts[label] = (R.counts[label] || 0) + n; };
  PRIVACY_PATTERNS.slice(0, 1).forEach(p => { out = out.replace(p.re, () => { count(p.label, 1); return p.token; }); });
  M.forEach(t => { out = out.replace(t.re, () => { count(t.label, 1); return t.token; }); });
  PRIVACY_PATTERNS.slice(1).forEach(p => { out = out.replace(p.re, () => { count(p.label, 1); return p.token; }); });
  return out;
}
function mentionsPersonal(text, map) {
  const t = String(text || '');
  return (map || privacyMap).some(x => { x.re.lastIndex = 0; return x.re.test(t); }) || PRIVACY_PATTERNS.some(p => { p.re.lastIndex = 0; return p.re.test(t); });
}
function restoreFromAi(v, map) {
  const M = map || privacyMap;
  if (typeof v === 'string') {
    let out = v;
    // put the real name back for the assessor; in anonymous mode the learner stays "the learner"
    // every placeholder goes back to exactly what it replaced; in anonymous mode "[Learner]" reads "the learner"
    M.forEach(t => { if (out.includes(t.token)) out = out.split(t.token).join(t.original); });
    return out.replace(/\[Learner(?: first name| surname| middle name)?\]/g, 'the learner');
  }
  if (Array.isArray(v)) return v.map(x => restoreFromAi(x, M));
  if (v && typeof v === 'object') { const o = {}; Object.keys(v).forEach(k => o[k] = restoreFromAi(v[k], M)); return o; }
  return v;
}
const b64FromBytes = bytes => { let bin = ''; for (let i = 0; i < bytes.length; i += 0x8000) bin += String.fromCharCode.apply(null, bytes.subarray(i, i + 0x8000)); return btoa(bin); };
async function pdfTextOf(b64) {
  const lib = await window.BTECPdfjs();
  const doc = await lib.getDocument({ data: Uint8Array.from(atob(b64), c => c.charCodeAt(0)), isEvalSupported: false }).promise;
  const out = [];
  for (let p = 1; p <= Math.min(doc.numPages, 150); p++) {
    const tc = await (await doc.getPage(p)).getTextContent();
    out.push(`--- Page ${p} ---\n` + tc.items.map(i => i.str + (i.hasEOL ? '\n' : ' ')).join('').replace(/[ \t]+\n/g, '\n').trim());
  }
  return { text: fixLigatures(out.join('\n\n')), pages: doc.numPages };
}
// Word's Calibri ligatures come out of some PDFs as odd letters: "federaƟon", "plaƞorm", "aƩract", "diƯerent", "WarcraŌ"
function fixLigatures(t) {
  const lig = { 'Ɵ': 'ti', 'ƞ': 'tf', 'Ʃ': 'tt', 'Ư': 'ff' };
  return String(t || '')
    .replace(/(?<=[a-z])[ƟƞƩƯ]|[ƟƞƩƯ](?=[a-z])/g, c => lig[c])
    .replace(/(?<=[a-z])Ō/g, 'ft')
    .replace(/ﬁ/g, 'fi').replace(/ﬂ/g, 'fl').replace(/ﬀ/g, 'ff').replace(/ﬃ/g, 'ffi').replace(/ﬄ/g, 'ffl');
}
async function pdfWithoutDetails(b64) {
  const PDFLib = await window.BTECPdfLib();
  const doc = await PDFLib.PDFDocument.load(Uint8Array.from(atob(b64), c => c.charCodeAt(0)), { ignoreEncryption: true, updateMetadata: false });
  doc.setTitle(''); doc.setAuthor(''); doc.setSubject(''); doc.setKeywords([]); doc.setCreator(''); doc.setProducer('');
  try { doc.catalog.delete(PDFLib.PDFName.of('Metadata')); } catch (e) {}
  return b64FromBytes(await doc.save());
}
// The learner's work as the second marker will see it
async function privateSubmissionBlock(src) {
  // src (optional): { b64, mediaType, scan, scanMode, scanAck, map, report } for a learner who isn't the one open
  const S = src || { b64: fileBase64, mediaType: fileMediaType, scan: fileScan, scanMode: fileScanMode, scanAck: fileScanAck };
  const map = S.map || privacyMap, report = S.report || privacyReport;
  const hide = t => redactForAi(t, map, report);
  const title = 'Learner submission';
  if (S.mediaType !== 'application/pdf') {
    const text = hide(await docxToText(S.b64));
    report.pdf = '';
    return { type: 'document', source: { type: 'text', media_type: 'text/plain', data: text }, title };
  }
  if (S.scanMode === 'learner' && S.scan && S.scan.learnerText) {
    report.pdf = 'turnitin';
    return { type: 'document', source: { type: 'text', media_type: 'text/plain', data: hide(S.scan.learnerText) }, title,
      context: "The learner's own writing, taken out of a Turnitin download. The cover sheet, feedback pages and the marker's comments were removed, and images are not included: where a criterion depends on visual evidence, say the assessor should check it. Page numbers match the original download." };
  }
  let extracted = null;
  try { extracted = await pdfTextOf(S.b64); } catch (e) { extracted = null; }
  const hasText = extracted && extracted.text.replace(/--- Page \d+ ---/g, '').trim().length > 40;
  const pictures = S.scan && S.scan.pictures;
  const personal = hasText && mentionsPersonal(extracted.text, map);
  if (personal && pdfPrivacy() === 'text' && pictures && !S.scanAck) {
    const e = new Error(PICTURE_PDF_MSG); e.blocked = true; throw e;
  }
  if (personal && pdfPrivacy() === 'text' && !(pictures && S.scanAck)) {
    report.pdf = 'text';
    return { type: 'document', source: { type: 'text', media_type: 'text/plain', data: hide(extracted.text) }, title,
      context: 'This PDF was converted to text so personal details could be hidden. Images, screenshots and layout are not included: where a criterion depends on visual evidence, say the assessor should check it.' };
  }
  try { const clean = await pdfWithoutDetails(S.b64); report.pdf = 'clean'; if (!hasText) report.notes.push("This PDF has no selectable text, so anything written in its images couldn't be checked for names."); else if (personal) report.notes.push(pictures && S.scanAck && pdfPrivacy() === 'text' ? 'Sent as the whole PDF because its pages are pictures, so names in it were not hidden.' : 'Sent as the whole PDF (your setting), so names in its pages were not hidden.'); return { type: 'document', source: { type: 'base64', media_type: 'application/pdf', data: clean }, title }; }
  catch (e) { throw new Error("Couldn't remove personal details from this PDF's file details, so it wasn't sent. Mark it yourself, or upload it as a Word file."); }
}
function privacySummaryHtml() {
  if (!privacyReport) return '';
  const parts = Object.entries(privacyReport.counts).map(([k, n]) => `${k} (${n})`);
  const lead = parts.length ? `Hidden from the second marker: ${parts.join(', ')}.` : 'No personal details found in the work or its file details.';
  const pdf = privacyReport.pdf === 'text' ? ' The PDF was sent as text so they could be hidden, so its images weren\'t seen.' : privacyReport.pdf === 'clean' ? ' The PDF was sent with its file details removed.' : privacyReport.pdf === 'turnitin' ? " Only the learner's own writing was sent, without Turnitin's cover sheet, feedback pages or comments." : '';
  return `<p class="privacy-line"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/></svg><span>${escapeHtml(lead + pdf + (privacyReport.notes.length ? ' ' + privacyReport.notes.join(' ') : ''))}</span></p>`;
}

// ═════════════════════════ How to mark: second marker or yourself ═════════════════════════
function checkMode() { const m = storeGet('btec_check_mode'); return m === 'ai' || m === 'manual' ? m : 'ask'; }
function setCheckMode(m) { if (m === 'ask') storeDel('btec_check_mode'); else storeSet('btec_check_mode', m); syncPrefRadios(); }
function syncPrefRadios() {
  const m = checkMode(), t = storeGet('btec_theme') || 'system';
  SMDOC.querySelectorAll('input[name="pdfPrivacy"]').forEach(r => r.checked = r.value === pdfPrivacy());
  SMDOC.querySelectorAll('input[name="checkMode"]').forEach(r => r.checked = r.value === m);
  SMDOC.querySelectorAll('input[name="theme"]').forEach(r => r.checked = r.value === t);
}
function startManual(go) {
  aiState = 'manual'; aiError = '';
  setProgress('pAi', '');
  unlockGrading(true);
  renderAiStatus(); refreshNav(); scheduleSave();
  if (go && currentStep < 5) goStep(5);
}
function chooseMarking(mode) {
  const rem = SMDOC.getElementById('rememberChoice');
  if (rem && rem.checked) { setCheckMode(mode); toast(mode === 'ai' ? 'The second marker will check new work straight away. Change this in Settings, Second Marker.' : "New work will open straight in the marking desk. Change this in Settings.", 'ok'); }
  if (mode === 'ai') { aiState = 'idle'; runCheck(); } else startManual(true);
}
function askSecondMarker() {
  if (aiState === 'running' || !fileBase64) return;
  aiState = 'idle'; runCheck();
}

// ── your own comments on the work ──
function selectionText() {
  const sel = __getSel && __getSel();
  if (!sel || sel.isCollapsed || !sel.rangeCount) return '';
  const paper = SMDOC.getElementById('workPaper');
  if (!paper || !paper.contains(sel.getRangeAt(0).commonAncestorContainer)) return '';
  return sel.toString().split(/\n+/).map(x => x.trim()).filter(Boolean)[0] || '';
}
function updateSelPop() {
  const pop = SMDOC.getElementById('selPop');
  if (!pop) return;
  const text = currentStep === 5 ? selectionText() : '';
  if (text.length < 3) { pop.classList.remove('show'); return; }
  const r = __getSel().getRangeAt(0).getBoundingClientRect();
  pop._text = text;
  pop.style.top = (r.bottom + __sy() + 8) + 'px';
  pop.classList.add('show');
  const w = pop.offsetWidth || 180;
  pop.style.left = Math.max(8, Math.min(SMROOT.clientWidth - w - 8, r.left + __sx() + r.width / 2 - w / 2)) + 'px';
}
__on('mouseup', () => setTimeout(updateSelPop, 0));
__on('keyup', e => { if (e.shiftKey || e.key === 'Escape') setTimeout(updateSelPop, 0); });
function addManualComment(quote) {
  const row = rowsEl()[currentRowIdx];
  const code = row ? rowCode(row) : '', v = row ? rowValue(row) : '';
  const type = v === 'Y' ? 'met' : (v === 'N' || v === 'R') ? 'not_met' : 'note';
  let page = null;
  if (work.kind === 'pdf') {
    let best = null, d = Infinity;
    SMDOC.querySelectorAll('#workPaper .pdf-page').forEach(pg => { const r = pg.getBoundingClientRect(), dd = Math.abs((r.top + r.bottom) / 2 - window.innerHeight / 2); if (dd < d) { d = dd; best = pg; } });
    page = best ? +best.dataset.page : 1;
  }
  lastAnnotations.push({ quote: String(quote || '').trim().slice(0, 300), code, type, text: '', page, include: true, manual: true });
  const a = lastAnnotations[lastAnnotations.length - 1];
  if (__getSel) __getSel().removeAllRanges();
  SMDOC.getElementById('selPop').classList.remove('show');
  const show = SMDOC.getElementById('showNotes');
  if (show && !show.checked) { show.checked = true; SMDOC.getElementById('workWrap').classList.remove('hide-notes'); }
  renderWork().then(() => {
    const el = SMDOC.querySelector(`#annotationList .ann[data-ann="${lastAnnotations.indexOf(a)}"]`);
    if (el) { el.querySelector('textarea').focus({ preventScroll: true }); el.scrollIntoView({ block: 'nearest', behavior: 'smooth' }); }
  });
  scheduleSave();
}

// ── light and dark ──
const ICON_MOON = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 14.5A8 8 0 0 1 9.5 4a8 8 0 1 0 10.5 10.5z"/></svg>';
const ICON_SUN = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2.5v2M12 19.5v2M2.5 12h2M19.5 12h2M5.3 5.3l1.4 1.4M17.3 17.3l1.4 1.4M5.3 18.7l1.4-1.4M17.3 6.7l1.4-1.4"/></svg>';
const darkQuery = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;
function applyTheme() {
  const pref = storeGet('btec_theme') || 'system';
  const dark = pref === 'dark' || (pref === 'system' && darkQuery && darkQuery.matches);
  SMROOT.setAttribute('data-theme', dark ? 'dark' : 'light');
  const b = SMDOC.getElementById('themeBtn');
  if (b) { b.innerHTML = dark ? ICON_SUN : ICON_MOON; b.setAttribute('aria-label', dark ? 'Switch to light mode' : 'Switch to dark mode'); b.title = dark ? 'Light mode' : 'Dark mode'; }
  syncPrefRadios();
}
function setTheme(pref) { if (pref === 'system') storeDel('btec_theme'); else storeSet('btec_theme', pref); applyTheme(); }
function toggleTheme() { setTheme(SMROOT.getAttribute('data-theme') === 'dark' ? 'light' : 'dark'); }
if (darkQuery && darkQuery.addEventListener) darkQuery.addEventListener('change', () => { if (!storeGet('btec_theme')) applyTheme(); });

// ═════════════════════════ Built-in brief library (briefs.js) ═════════════════════════
var builtinBriefId = '';
var briefChoiceMade = false;   // the assessor picked (or cleared) a brief for these units
function currentBuiltinBrief() { return (window.BTEC_BRIEFS || []).find(b => b.id === builtinBriefId) || null; }
function briefsForUnits() {
  const si = subjectSelect.value, lv = currentLevel();
  if (si === '' || !lv || !selectedUnits.length || !lv.units[selectedUnits[0]]) return [];
  const subject = window.BTEC_DATA[+si].subject, primary = unitTag(lv.units[selectedUnits[0]], selectedUnits[0]);
  return (window.BTEC_BRIEFS || []).filter(b => b.subject === subject && b.level === lv.level && b.units.includes(primary))
    .sort((a, b) => a.assignment.localeCompare(b.assignment, undefined, { numeric: true }));
}
function builtinBriefText(b) {
  const tasks = {};
  b.criteria.forEach(c => { (tasks[c.task] = tasks[c.task] || []).push((b.units.length > 1 ? c.unit + ' ' : '') + c.code); });
  return b.text + '\n\nCriteria by task (from the approved brief):\n' + Object.entries(tasks).map(([t, cs]) => `${t}: ${cs.join(', ')}`).join('\n');
}
function joinList(xs) { return xs.length < 2 ? xs.join('') : xs.slice(0, -1).join(', ') + ' and ' + xs[xs.length - 1]; }
function renderBuiltinBriefs(noAuto) {
  const wrap = SMDOC.getElementById('assignWrap'), sel = SMDOC.getElementById('builtinBrief');
  if (!wrap) return;
  const list = briefsForUnits();
  if (builtinBriefId && !list.some(b => b.id === builtinBriefId)) {
    builtinBriefId = ''; briefChoiceMade = false;
    // the unit changed: drop the title and deadline that came from the old brief
    ['assignment_title', 'deadline'].forEach(fid => { const el = SMDOC.getElementById(fid); if (el && el.dataset.fromBrief) { el.value = ''; delete el.dataset.fromBrief; } });
  }
  wrap.style.display = list.length ? '' : 'none';
  sel.innerHTML = list.map(b => `<option value="${b.id}">${escapeHtml(b.assignment)}: ${escapeHtml(b.title)}${b.units.length > 1 ? ' (' + b.units.map(tagName).join(' + ') + ')' : ''}</option>`).join('')
    + '<option value="">Another assignment (add or paste the brief below)</option>';
  if (!builtinBriefId && !briefChoiceMade && !noAuto && list.length) {
    // one approved brief for this unit: use it straight away
    if (list.length === 1) { sel.value = list[0].id; chooseBuiltinBrief(list[0].id, true); return; }
    sel.insertAdjacentHTML('afterbegin', '<option value="" selected disabled>Choose the assignment...</option>');
  }
  sel.value = builtinBriefId || (briefChoiceMade ? '' : sel.value);
  renderBuiltinInfo();
}
var applyingBrief = false;
function chooseBuiltinBrief(id, auto) {
  if (applyingBrief) return;
  builtinBriefId = id || '';
  briefChoiceMade = !auto || !!id;
  const b = currentBuiltinBrief(), lv = currentLevel();
  if (b && lv) {
    applyingBrief = true;
    try {
      // combine every unit the brief covers
      const idx = b.units.map(tag => lv.units.findIndex((u, i) => unitTag(u, i) === tag)).filter(i => i >= 0);
      // units in the brief's own order (Unit 2 then Unit 4), the first one in the dropdown
      const want = idx.length ? [...new Set(idx)] : [selectedUnits[0]];
      if (want.join() !== selectedUnits.join()) { selectedUnits = want; unitSelect.value = String(want[0]); buildCombineList(); applyUnits(); }
      // tick exactly the brief's criteria
      const multi = selectedUnits.length > 1;
      const codes = new Set(b.criteria.map(c => codeKey(multi ? `${c.unit} ${c.code}` : c.code)));
      pick.forEach(p => p.on = codes.has(codeKey(p.code)));
      renderPick(); markCriteriaChanged();
      // programme title from the brief's qualification, when it's one of this level's programmes
      const plain = x => String(x || '').toLowerCase().replace(/^pearson\s+/, '').trim();
      const ps = SMDOC.getElementById('programmeSelect'), q = plain(b.qualification);
      const opt = q && [...ps.options].find(o => plain(o.value) === q);
      if (opt) { ps.value = opt.value; SMDOC.getElementById('programme_title').value = opt.value; }
      // title and deadline from the brief, unless typed in by hand
      [['assignment_title', b.fullTitle || b.title], ['deadline', b.deadline]].forEach(([fid, v]) => {
        const el = SMDOC.getElementById(fid);
        if (el && v && (!el.value.trim() || el.dataset.fromBrief)) { el.value = v; el.dataset.fromBrief = '1'; }
      });
      renderLateStatus();
    } finally { applyingBrief = false; }
  }
  const sel = SMDOC.getElementById('builtinBrief');
  if (sel) { const ph = sel.querySelector('option[disabled]'); if (ph && (id || briefChoiceMade)) ph.remove(); sel.value = builtinBriefId; }
  renderBuiltinInfo(); scheduleSave();
}
function renderBuiltinInfo() {
  const el = SMDOC.getElementById('builtinBriefInfo'), help = SMDOC.getElementById('briefHelp');
  if (!el) return;
  const b = currentBuiltinBrief();
  if (help) help.textContent = b
    ? `Using the approved brief for ${b.assignment}, "${b.title}". You only need to add one here if learners were given a different version.`
    : 'Add the brief the learners worked from. The AI uses it to see which task covers which criterion, so the feedback can send the learner back to the right task. It stays loaded for the next learner.';
  if (!b) { el.innerHTML = ''; return; }
  const tasks = {};
  b.criteria.forEach(c => { (tasks[c.task] = tasks[c.task] || []).push((b.units.length > 1 ? tagName(c.unit) + ' ' : '') + c.code); });
  el.innerHTML = `<ul>${Object.entries(tasks).map(([t, cs]) => `<li><b>${escapeHtml(t)}</b> ${/^(Tasks|Parts)\b|\band\b/.test(t) ? 'cover' : 'covers'} ${joinList(cs.map(c => `<span class="code-tag">${escapeHtml(c)}</span>`))}</li>`).join('')}</ul>`
    + `<div>${b.handout || b.deadline ? `${b.handout ? `Handed out ${escapeHtml(b.handout)}, due` : 'Due'} ${escapeHtml(b.deadline || 'date not set')}.` : 'Hand-out and deadline dates to be confirmed.'}</div>`
    + `<details class="more"><summary>Read the brief</summary><pre class="brief-text"></pre></details>`;
  el.querySelector('.brief-text').textContent = b.text;
}

// ═════════════════════════ Resubmission ═════════════════════════
const RESUB_IDS = {
  assignment_title: '4D14726C', student_reg: '704EA461', unit_title: '1088F969', student_name: '14EAF370',
  resub_or_retake: '7CABE0B3', assessor_name: '68D82956', deadline: '71350345', date_submitted: '2C5D9E4C',
  criteria_code: ['05A69E88', '6F8EF208', '039530A8'],
  criteria_achieved: ['641C80CD', '59E8055B', '0D7846FF'],
  criteria_comment: ['3486178F', '352255FE', '5A4858D1'],
  general_comments: '413A7E8F'
};
const codeKey = c => String(c || '').toLowerCase().replace(/\s+/g, ' ').trim();
const prevMet = code => !!(resub.on && resub.prev[codeKey(code)] === 'Y');

function setResub(on) {
  resub.on = on;
  renderResub(); applyPrevTags(); markCriteriaChanged(); renderAiStatus(); updateGrade(); scheduleSave();
}
function renderResub() {
  const t = SMDOC.getElementById('resubToggle');
  if (!t) return;
  t.checked = resub.on;
  SMDOC.getElementById('resubBody').style.display = resub.on ? '' : 'none';
  const type = SMDOC.getElementById('resubType');
  type.style.display = resub.on ? '' : 'none'; type.value = resub.type;
  const chip = SMDOC.getElementById('resubChip');
  chip.innerHTML = resub.recordName ? `<span class="file-chip"><span></span><button class="btn btn-sm btn-remove" title="Remove the first record" data-sm-click="clearFirstRecord()">&#215;</button></span>` : '';
  if (resub.recordName) chip.querySelector('span span').textContent = resub.recordName;
  const grid = SMDOC.getElementById('resubGrid');
  grid.innerHTML = '';
  const on = pick.filter(p => p.on);
  on.forEach(p => {
    const k = codeKey(p.code);
    const lab = document.createElement('label');
    lab.innerHTML = `<span class="cc"></span><select class="form-select form-select-sm" aria-label="First submission decision"><option value="">Not set</option><option value="Y">Y: Yes</option><option value="N">N: No</option><option value="R">R: Resub</option></select>`;
    lab.querySelector('.cc').textContent = p.code;
    const sel = lab.querySelector('select');
    sel.value = resub.prev[k] || '';
    sel.onchange = () => { if (sel.value) resub.prev[k] = sel.value; else delete resub.prev[k]; renderResubNote(); applyPrevTags(); markCriteriaChanged(); renderAiStatus(); scheduleSave(); };
    lab.title = 'Decision at first submission';
    grid.appendChild(lab);
  });
  renderResubNote();
}
function renderResubNote() {
  const el = SMDOC.getElementById('resubNote');
  if (!el) return;
  const on = pick.filter(p => p.on);
  const met = on.filter(p => resub.prev[codeKey(p.code)] === 'Y').map(p => p.code);
  const set = on.filter(p => resub.prev[codeKey(p.code)]);
  const redo = on.filter(p => resub.prev[codeKey(p.code)] !== 'Y').map(p => p.code);
  const parts = [];
  if (!on.length) parts.push('Tick the criteria in step 1 first.');
  else if (!set.length) parts.push('Set what each criterion got at first submission, or add the first record above.');
  else parts.push(`Achieved at first submission: ${met.join(', ') || 'none'}. The AI will reassess ${redo.join(', ') || 'nothing'}.`);
  if (resub.type === 'Retake') parts.push('Pearson caps a retake at Pass, so check the grade before you export.');
  if (resub.warning) parts.push(resub.warning);
  el.textContent = parts.join(' ');
}
function clearFirstRecord() {
  Object.assign(resub, { recordName: '', recordBuffer: null, comments: {}, general: '', header: {}, warning: '' });
  renderResub(); scheduleSave();
}
// Shows the first-submission decision under each criterion in the grading table
function applyPrevTags() {
  SMDOC.querySelectorAll('#criteriaBody .crit-row').forEach(tr => {
    const code = tr.querySelector('.code-input').value.trim();
    const cell = tr.querySelector('.crit-top');
    const old = cell.querySelector('.prev-tag'); if (old) old.remove();
    const v = resub.on ? resub.prev[codeKey(code)] : '';
    if (!v) return;
    const d = document.createElement('div');
    d.className = 'prev-tag';
    d.innerHTML = `First submission: <b>${v}</b>`;
    cell.appendChild(d);
    const sel = tr.querySelector('.achieved-sel');
    if (v === 'Y' && !sel.value) { sel.value = 'Y'; updateSelColor(sel); }
  });
}

// Reads paragraph text by its w14:paraId, keeping line breaks
function paraText(xml, id) {
  const m = xml.match(new RegExp(`<w:p [^>]*w14:paraId="${id}"[^>]*>([\\s\\S]*?)</w:p>`));
  if (!m) return '';
  let out = '';
  m[1].replace(/<w:t(?:\s[^>]*)?>([^<]*)<\/w:t>|<w:br\s*\/>/g, (all, t) => { out += t === undefined ? '\n' : t; return all; });
  return out.replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"').replace(/&apos;/g, "'").replace(/&amp;/g, '&').trim();
}

async function loadFirstRecord(file) {
  if (!file) return;
  if (!/\.docx$/i.test(file.name)) { toast('Add the first assessment record as a Word (.docx) file.', 'error'); return; }
  try {
    const buf = await file.arrayBuffer();
    const zip = await JSZip.loadAsync(buf.slice(0));
    const docF = zip.file('word/document.xml');
    if (!docF) throw new Error("That file doesn't look like a Word document.");
    const xml = await docF.async('string');
    if (!xml.includes(PARA_IDS.criteria_achieved[0])) throw new Error("This isn't a Pearson assessment record made with the checker. Set the first decisions by hand below instead.");
    let data = null;
    const j = zip.file('btec-checker/decision.json');
    if (j) { try { data = JSON.parse(await j.async('string')); } catch (e) { data = null; } }

    const prev = {}, comments = {};
    let general = '', header = {};
    if (data && Array.isArray(data.criteria)) {
      data.criteria.forEach(c => { if (/^[YNR]$/.test(c.achieved)) { prev[codeKey(c.code)] = c.achieved; comments[codeKey(c.code)] = c.comment || ''; } });
      general = data.feedback || '';
      header = data.header || {};
    } else {
      // a record that already went through a resubmission: its latest decisions are in that section
      const ids = /^[YNR]$/.test(paraText(xml, RESUB_IDS.criteria_achieved[0]).toUpperCase()) ? RESUB_IDS : PARA_IDS;
      for (let i = 0; i < 3; i++) {
        const code = paraText(xml, ids.criteria_code[i]), ach = paraText(xml, ids.criteria_achieved[i]).toUpperCase();
        if (code && /^[YNR]$/.test(ach)) { prev[codeKey(code)] = ach; comments[codeKey(code)] = paraText(xml, ids.criteria_comment[i]); }
      }
      const rest = [];
      paraText(xml, ids.general_comments).split('\n').forEach(line => {
        const m = line.match(/^([^()\n]{2,24}) \(([YNR])\)(?::\s?(.*))?$/);
        if (m) { prev[codeKey(m[1])] = m[2]; comments[codeKey(m[1])] = m[3] || ''; } else rest.push(line);
      });
      general = rest.join('\n').trim();
      ['student_name', 'student_reg', 'assignment_title', 'assessor_name', 'unit_title', 'deadline', 'date_submitted', 'extension'].forEach(k => header[k] = paraText(xml, PARA_IDS[k]));
    }
    if (!Object.keys(prev).length) throw new Error('No criteria decisions found in that record. Set them by hand below.');

    const already = /^[YNR]$/.test(paraText(xml, RESUB_IDS.criteria_achieved[0]).toUpperCase());
    Object.assign(resub, { on: true, prev, comments, general, header, recordName: file.name, recordBuffer: buf,
      warning: already ? 'This record already has a resubmission filled in. Pearson allows only one resubmission per assignment.' : '' });

    // fill any blank learner details from the first record
    if (learnerMode === 'anon' && /^ANON-/.test(header.student_reg || '')) setLearnerMode('anon', header.student_reg);
    ['student_name', 'student_reg', 'assignment_title', 'assessor_name'].forEach(k => {
      const el = SMDOC.getElementById(k);
      if (el && !el.value.trim() && header[k]) el.value = header[k];
    });
    autoEmail(); updateContext();

    // make sure every criterion from the first record is ticked
    const missing = [];
    Object.keys(prev).forEach(k => { const p = pick.find(x => codeKey(x.code) === k); if (p) p.on = true; else missing.push(k.toUpperCase()); });
    renderPick();
    if (missing.length) resub.warning = (resub.warning + ` ${missing.join(', ')} from the first record ${missing.length > 1 ? "aren't" : "isn't"} in this unit's criteria.`).trim();
    renderResub(); applyPrevTags(); markCriteriaChanged(); scheduleSave();
    const metN = Object.values(prev).filter(v => v === 'Y').length;
    toast(`First record loaded: ${metN} achieved, ${Object.keys(prev).length - metN} to reassess.`, 'ok');
  } catch (err) { toast(err.message, 'error'); }
}

// Adds the checker's own copy of the decisions inside the record, so a resubmission can read it back exactly
async function embedDecision(zip, decision) {
  zip.file('btec-checker/decision.json', JSON.stringify(decision));
  const ctF = zip.file('[Content_Types].xml');
  if (!ctF) return;
  let ct = await ctF.async('string');
  if (!/Extension="json"/i.test(ct)) ct = ct.replace(/(<Types[^>]*>)/, '$1<Default Extension="json" ContentType="application/json"/>');
  zip.file('[Content_Types].xml', ct);
}

// ═════════════════════════ Drafts (saved in this browser) ═════════════════════════
const DRAFT_FIELDS = ['programme_title', 'programmeSelect', 'unit_context', 'brief_text', 'hide_extra', 'extension_date', 'student_name', 'student_reg', 'student_email', 'assignment_title', 'assessor_name', 'deadline', 'date_submitted', 'extension', 'unit_title'];
let draftTimer = null, restoring = false, pendingDraft = null, draftChecked = false;

function draftDb(mode, fn) {
  return new Promise((resolve, reject) => {
    let req;
    try { req = indexedDB.open('btec-checker' + CCNS, 1); } catch (e) { reject(e); return; }
    req.onupgradeneeded = () => req.result.createObjectStore('drafts');
    req.onerror = () => reject(req.error);
    req.onsuccess = () => {
      const db = req.result;
      try {
        const tx = db.transaction('drafts', mode);
        const r = fn(tx.objectStore('drafts'));
        tx.oncomplete = () => { db.close(); resolve(r && r.result); };
        tx.onerror = tx.onabort = () => { db.close(); reject(tx.error); };
      } catch (e) { db.close(); reject(e); }
    };
  });
}
function setDraftStatus(text, err) {
  const el = SMDOC.getElementById('draftStatus');
  if (el) { el.textContent = text; el.classList.toggle('err', !!err); }
}
const hhmm = t => new Date(t).toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
const draftHasContent = d => d && d.subject !== '' && !!(d.file || (d.fields && (d.fields.student_name || d.fields.student_reg)));
function cloneSafe(o) { if (!o) return o; try { return structuredClone(o); } catch (e) { return JSON.parse(JSON.stringify(o)); } }

function draftSnapshot() {
  const val = id => { const el = SMDOC.getElementById(id); return el ? el.value : ''; };
  return {
    v: 1, savedAt: Date.now(), step: currentStep, maxStep,
    subject: subjectSelect.value, level: levelSelect.value, units: selectedUnits.slice(),
    pick: pick.map(p => ({ code: p.code, desc: p.desc, on: p.on, unit: p.unit, unitTitle: p.unitTitle })),
    fields: Object.fromEntries(DRAFT_FIELDS.map(id => [id, val(id)])),
    model: (SMDOC.querySelector('input[name="model"]:checked') || {}).value,
    references: references.map(r => ({ ...r })), brief: brief ? { ...brief } : null, builtinBrief: builtinBriefId, briefChoiceMade, learnerMode, anonRef,
    resub: { ...resub, prev: { ...resub.prev }, comments: { ...resub.comments }, header: { ...resub.header } },
    file: fileBase64 ? { base64: fileBase64, type: fileMediaType, name: fileNameStr } : null,
    integrity: cloneSafe(lastIntegrity), aiIntegrity: cloneSafe(lastAiIntegrity), integrityFailed,
    ai: { state: aiState === 'running' ? 'idle' : aiState, error: aiError, stale: aiStale, results: cloneSafe(lastResults), annotations: cloneSafe(lastAnnotations), overall: aiOverall, next: aiNextSteps, runId: aiRunId, runModel: aiRunModel, resultsHtml: SMDOC.getElementById('results').innerHTML },
    grading: gradingUnlocked, gradeNote: SMDOC.getElementById('gradeNote').textContent,
    rows: [...SMDOC.querySelectorAll('#criteriaBody .crit-row')].map(tr => ({ code: tr.querySelector('.code-input').value.trim(), achieved: tr.querySelector('.achieved-sel').value, comment: tr.querySelector('.desc-input').value, ai: tr.querySelector('.ai-cell').innerHTML })),
    feedback: lastFeedback, feedbackEdited, staff: val('staffNote'), staffEdited,
    email: { to: val('emailTo'), subject: val('emailSubject'), body: val('emailBody'), edited: emailEdited, manual: emailManual },
  };
}
function scheduleSave() {
  if (restoring) return;
  if (pendingDraft) { setDraftStatus('Not saving: resume or start fresh'); return; }
  clearTimeout(draftTimer);
  draftTimer = setTimeout(saveDraft, 700);
}
var resetting = false;
async function saveDraft() {
  clearTimeout(draftTimer);
  if (resetting || restoring || pendingDraft || !draftChecked || !window.indexedDB) return;
  const snap = draftSnapshot();
  try {
    if (!draftHasContent(snap)) { await draftDb('readwrite', st => st.delete('current')); setDraftStatus(''); return; }
    await draftDb('readwrite', st => st.put(snap, 'current'));
    setDraftStatus(`Draft saved ${hhmm(snap.savedAt)}`);
  } catch (e) { setDraftStatus("Draft not saved in this browser", true); }
}
function clearDraft() { clearTimeout(draftTimer); if (window.indexedDB) draftDb('readwrite', st => st.delete('current')).catch(() => {}); setDraftStatus(''); }

async function checkForDraft() {
  if (!window.indexedDB) { setDraftStatus("Drafts can't be saved in this browser", true); return; }
  let d = null;
  try { d = await draftDb('readonly', st => st.get('current')); } catch (e) { d = null; }
  draftChecked = true;
  if (!draftHasContent(d)) return;
  pendingDraft = d;
  const lv = (window.BTEC_DATA[+d.subject] || {}).levels;
  const unit = d.fields.unit_title || '';
  const day = new Date(d.savedAt).toDateString() === new Date().toDateString() ? 'today' : new Date(d.savedAt).toLocaleDateString('en-GB', { weekday: 'short', day: 'numeric', month: 'short' });
  const who = d.fields.student_name || (d.fields.student_reg ? `student ${d.fields.student_reg}` : 'an unnamed learner');
  const txt = SMDOC.getElementById('draftText');
  txt.innerHTML = '<strong></strong><small></small>';
  txt.querySelector('strong').textContent = `Carry on marking ${who}?`;
  txt.querySelector('small').textContent = [unit, `${STEPS[(d.step || 1) - 1] || 'Unit'} step`, `saved ${day} at ${hhmm(d.savedAt)}`].filter(Boolean).join(', ');
  SMDOC.getElementById('draftBanner').style.display = 'flex';
}
function discardDraft() {
  pendingDraft = null;
  SMDOC.getElementById('draftBanner').style.display = 'none';
  clearDraft();
}
function resumeDraft(quiet) {
  const d = pendingDraft;
  if (!d) return;
  restoring = true;
  try {
    subjectSelect.value = d.subject; onSubjectChange();
    levelSelect.value = d.level; onLevelChange();
    if (d.units && d.units.length) { unitSelect.value = String(d.units[0]); selectedUnits = d.units.slice(); buildCombineList(); applyUnits(); }
    (d.pick || []).forEach(sp => { const p = pick.find(x => x.code === sp.code); if (p) p.on = sp.on; else pick.push({ ...sp }); });
    renderPick();
    builtinBriefId = d.builtinBrief || ''; briefChoiceMade = !!d.briefChoiceMade || !!builtinBriefId; renderBuiltinBriefs(true);
    if (d.learnerMode) setLearnerMode(d.learnerMode, d.anonRef);
    Object.entries(d.fields || {}).forEach(([id, v]) => { const el = SMDOC.getElementById(id); if (el) el.value = v; });
    renderLateStatus();
    if (d.model) { const r = SMDOC.querySelector(`input[name="model"][value="${d.model}"]`); if (r) r.checked = true; }
    references = d.references || []; renderReferences();
    brief = d.brief || null; renderBrief();
    if (briefPasted()) { SMDOC.getElementById('briefPaste').open = true; onBriefText(); }
    resub = Object.assign(newResub(), d.resub || {}); renderResub();
    emailManual = !!(d.email && d.email.manual);

    if (d.file) {
      fileBase64 = d.file.base64; fileMediaType = d.file.type; fileNameStr = d.file.name;
      dropZone.classList.add('has-file');
      SMDOC.getElementById('dropIcon').innerHTML = '<svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>';
      SMDOC.getElementById('dropText').textContent = fileNameStr;
      SMDOC.getElementById('uploadProgress').style.display = 'flex';
      setProgress('pFile', 'ok');
      startFileScan();
    }
    lastIntegrity = d.integrity || null; lastAiIntegrity = d.aiIntegrity || null; integrityFailed = !!d.integrityFailed;
    integrityReady = Promise.resolve();
    if (lastIntegrity) { renderIntegrity(lastIntegrity, lastAiIntegrity); setProgress('pIntegrity', 'ok'); }
    else if (integrityFailed) setProgress('pIntegrity', 'err');

    const ai = d.ai || {};
    aiState = ai.state || 'idle'; aiError = ai.error || ''; aiStale = !!ai.stale;
    lastResults = ai.results || []; lastAnnotations = ai.annotations || []; aiOverall = ai.overall || ''; aiNextSteps = ai.next || '';
    aiRunId = ai.runId || ''; aiRunModel = ai.runModel || '';
    setProgress('pAi', aiState === 'done' ? 'ok' : aiState === 'error' || aiState === 'blocked' ? 'err' : '');
    SMDOC.getElementById('results').innerHTML = ai.resultsHtml || '';
    renderAnnotations();

    gradingUnlocked = !!d.grading;
    syncRowsFromPick();
    const saved = new Map((d.rows || []).map(r => [codeKey(r.code), r]));
    SMDOC.querySelectorAll('#criteriaBody .crit-row').forEach(tr => {
      const r = saved.get(codeKey(tr.querySelector('.code-input').value)); if (!r) return;
      const sel = tr.querySelector('.achieved-sel'); sel.value = r.achieved; updateSelColor(sel);
      tr.querySelector('.desc-input').value = r.comment;
      tr.querySelector('.ai-cell').innerHTML = r.ai;
    });
    applyPrevTags();
    SMDOC.querySelectorAll('.achieved-sel').forEach(s => s.disabled = !gradingUnlocked);
    SMDOC.getElementById('skipAiBtn').style.display = gradingUnlocked ? 'none' : '';
    if (d.gradeNote) SMDOC.getElementById('gradeNote').textContent = d.gradeNote;

    feedbackEdited = !!d.feedbackEdited; lastFeedback = d.feedback || '';
    SMDOC.getElementById('feedbackBox').innerText = lastFeedback; showRebuild();
    staffEdited = !!d.staffEdited; SMDOC.getElementById('staffNote').value = d.staff || ''; showStaffRebuild();
    if (d.email) {
      SMDOC.getElementById('emailTo').value = d.email.to || '';
      SMDOC.getElementById('emailSubject').value = d.email.subject || '';
      SMDOC.getElementById('emailBody').value = d.email.body || '';
      emailEdited = !!d.email.edited;
    }
    updateGrade(); renderAiStatus();
    maxStep = Math.max(d.maxStep || 1, d.step || 1);
    currentStep = d.step || 1;
    goStep(currentStep);
  } finally { restoring = false; }
  pendingDraft = null;
  SMDOC.getElementById('draftBanner').style.display = 'none';
  if (quiet) { scheduleSave(); return; }
  setDraftStatus(`Draft restored, saved ${hhmm(d.savedAt)}`);
  toast('Marking restored where you left off.', 'ok');
}
__on('input', e => { if (!e.target.closest || !e.target.closest('#settingsCard')) scheduleSave(); }, true);
__on('change', e => { if (!e.target.closest || !e.target.closest('#settingsCard') || e.target.name === 'model') scheduleSave(); }, true);
__on('visibilitychange', () => { if (document.visibilityState === 'hidden' && draftTimer) saveDraft(); });
__won('pagehide', () => { if (draftTimer) saveDraft(); });


// ═════════════════════════ The marking desk (step 5) ═════════════════════════
// Mark sheet on the left, the learner's work in the middle with the second
// marker's pencil notes beside it, feedback on the right. Focus mode shows one
// criterion at a time next to the work. Keys: Y / N / R decide, arrows move,
// F toggles focus mode, Enter goes to the next criterion in focus mode.
var work = { key: '', kind: '', paras: null, pdf: null, io: null, ready: false, building: null, error: '' };
var currentRowIdx = 0;
var notesTimer = null;

(function applyBrand() {
  const root = SMROOT.style;
  if (window.BTEC_BRAND_PURPLE) root.setProperty('--brand-purple', window.BTEC_BRAND_PURPLE);
  if (window.BTEC_BRAND_BLUE) root.setProperty('--brand-blue', window.BTEC_BRAND_BLUE);
})();

function rowsEl() { return [...SMDOC.querySelectorAll('#criteriaBody .crit-row')]; }
function isFocus() { const d = SMDOC.getElementById('desk'); return !!(d && d.classList.contains('focus')); }
const rowCode = row => row.querySelector('.code-input').value.trim();
const rowValue = row => row.querySelector('.achieved-sel').value;

function refreshRow(row) {
  const sel = row.querySelector('.achieved-sel'), v = sel.value, code = rowCode(row);
  const r = lastResults.find(x => x.code.toLowerCase() === code.toLowerCase());
  const sug = prevMet(code) ? 'Y' : (r ? (r.met ? 'Y' : 'N') : '');
  row.classList.toggle('decided', !!v);
  row.querySelectorAll('.stamp').forEach(b => {
    const on = b.dataset.v === v;
    b.classList.toggle('on', on);
    b.setAttribute('aria-pressed', on ? 'true' : 'false');
    b.classList.toggle('sug', !v && b.dataset.v === sug);
    b.disabled = sel.disabled;
    b.title = sel.disabled ? 'Waiting for the second marker' : `${STAMP_WORDS[b.dataset.v]} (${b.dataset.v})`;
  });
}

function aiGradeText() {
  if (aiState === 'running') return { text: 'Checking...', small: true };
  if (aiState !== 'done') return { text: aiState === 'error' ? "Didn't finish" : aiState === 'manual' ? 'Not asked' : aiState === 'blocked' ? 'Waiting on you' : 'Not checked', small: true };
  const crit = getCriteria().filter(c => c.code);
  if (!crit.length) return { text: 'No criteria', small: true };
  const g = gradeOf(crit, c => prevMet(c.code) || !!(lastResults.find(x => x.code.toLowerCase() === c.code.toLowerCase()) || {}).met);
  const text = g.grade === 'U' && !g.perUnit ? 'Not yet Pass' : g.label;
  return { text, small: text.length > 14 };
}

function refreshDesk() {
  const rows = rowsEl();
  rows.forEach(refreshRow);
  if (rows.length && !rows.some(r => r.classList.contains('current'))) {
    currentRowIdx = Math.min(currentRowIdx, rows.length - 1);
    rows[currentRowIdx].classList.add('current');
    litMarks(rows[currentRowIdx], false);
  }
  const decided = rows.filter(rowValue).length;
  const dc = SMDOC.getElementById('decidedCount');
  if (dc) dc.textContent = rows.length ? `${decided} of ${rows.length} decided` : '';
  const ai = SMDOC.getElementById('aiGradeMini');
  if (ai) { const t = aiGradeText(); ai.textContent = t.text; ai.className = 'gb-v' + (t.small ? ' small' : ''); }
  const ask = SMDOC.getElementById('aiAsk');
  if (ask) { ask.hidden = !(fileBase64 && ['manual', 'idle', 'choose', 'error', 'blocked'].includes(aiState)); ask.textContent = aiState === 'error' ? 'Try again' : 'Ask now'; }
  if (isFocus()) { renderFocusChips(); updateFocusNav(); }
}
const _renderAiStatus = renderAiStatus;
renderAiStatus = function () { _renderAiStatus(); refreshDesk(); };

function setCurrentRow(row, scroll = true) {
  const rows = rowsEl(), i = rows.indexOf(row);
  if (i < 0) return;
  currentRowIdx = i;
  rows.forEach((r, k) => r.classList.toggle('current', k === i));
  litMarks(row, scroll || isFocus());
  if (isFocus()) { renderFocusChips(); updateFocusNav(); }
  else if (scroll) row.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
}
function moveCurrent(d, fromButton) {
  const rows = rowsEl();
  if (!rows.length) return;
  if (d > 0 && currentRowIdx >= rows.length - 1) {
    if (fromButton && isFocus()) { toggleFocus(false); const fb = SMDOC.getElementById('feedbackBox'); if (fb) fb.scrollIntoView({ block: 'center', behavior: 'smooth' }); }
    return;
  }
  setCurrentRow(rows[Math.max(0, Math.min(rows.length - 1, currentRowIdx + d))]);
}
function stampRow(row, v, advance) {
  const sel = row.querySelector('.achieved-sel');
  if (sel.disabled) { toast('The second marker is still checking. Wait a moment, or choose "Grade without the AI check".', 'info'); return; }
  setCurrentRow(row, false);
  sel.value = v;
  sel.dispatchEvent(new Event('change', { bubbles: true }));
  const st = row.querySelector(`.stamp[data-v="${v}"]`);
  if (st) { st.classList.remove('thud'); void st.offsetWidth; st.classList.add('thud'); }
  if (advance) setTimeout(() => { if (rowsEl()[currentRowIdx] === row) moveCurrent(1); }, 260);
}

function litMarks(row, scrollWork) {
  const code = row ? codeKey(rowCode(row)) : '';
  let first = null;
  SMDOC.querySelectorAll('#workPaper mark.ann-mark, #annotationList .ann').forEach(m => {
    const on = !!code && codeKey(m.dataset.code) === code;
    m.classList.toggle('lit', on);
    if (on && !first && m.tagName === 'MARK') first = m;
  });
  if (scrollWork && first && currentStep === 5) {
    const r = first.getBoundingClientRect();
    if (r.top < 90 || r.bottom > window.innerHeight - 110) first.scrollIntoView({ block: 'center', behavior: 'smooth' });
  }
}
function jumpToCode(code) {
  const row = rowsEl().find(r => codeKey(rowCode(r)) === codeKey(code));
  if (row) setCurrentRow(row, true);
}

// ── focus mode ──
function toggleFocus(on) {
  const desk = SMDOC.getElementById('desk');
  if (!desk) return;
  const want = typeof on === 'boolean' ? on : !isFocus();
  desk.classList.toggle('focus', want);
  const b = SMDOC.getElementById('focusBtn');
  b.textContent = want ? 'Back to the desk' : 'Focus mode';
  b.setAttribute('aria-pressed', want ? 'true' : 'false');
  storeSet('btec_focus', want ? '1' : '0');
  SMROOT.classList.toggle('focus-on', want && currentStep === 5);
  const ae = SMDOC.activeElement;
  if (want && ae && ae.closest && ae.closest('.topbar, .wiz-nav')) ae.blur();   // let the header fade back
  const rows = rowsEl();
  if (want && rows.length) {
    const cur = rows[currentRowIdx];
    const target = cur && !rowValue(cur) ? cur : (rows.find(r => !rowValue(r)) || cur || rows[0]);
    setCurrentRow(target, false);
    litMarks(target, true);
  }
  renderFocusChips(); updateFocusNav(); placeNotesSoon(); fitPanelsSoon();
}
function renderFocusChips() {
  const box = SMDOC.getElementById('focusChips');
  if (!box) return;
  box.innerHTML = rowsEl().map((r, i) => {
    const code = escapeHtml(rowCode(r)), v = rowValue(r);
    return `<button type="button" class="fchip${v ? ' done' : ''}${i === currentRowIdx ? ' current' : ''}" data-i="${i}" aria-label="${code}${v ? ', decided ' + v : ', not decided'}"${i === currentRowIdx ? ' aria-current="true"' : ''}>${code}${v ? `<b>${v}</b>` : ''}</button>`;
  }).join('');
  box.querySelectorAll('.fchip').forEach(b => b.onclick = () => setCurrentRow(rowsEl()[+b.dataset.i]));
}
function updateFocusNav() {
  const rows = rowsEl(), n = rows.length, i = currentRowIdx;
  const pos = SMDOC.getElementById('focusPos'); if (pos) pos.textContent = n ? `Criterion ${i + 1} of ${n}` : '';
  const next = SMDOC.getElementById('focusNext'); if (next) next.textContent = i >= n - 1 ? 'Finish and check feedback' : 'Next criterion';
  const prev = SMDOC.getElementById('focusPrev'); if (prev) prev.disabled = i <= 0;
}

// ── entering the desk ──
function enterDesk() {
  renderLearnerStrip(); renderMiniChecks(); renderWork();
  if (storeGet('btec_focus') === '1' && !isFocus()) toggleFocus(true);
  refreshDesk();
  const rows = rowsEl(), open = rows.find(r => !rowValue(r));
  if (open && !isFocus()) setCurrentRow(open, false);
  requestAnimationFrame(placeNotes); fitPanelsSoon();
}
function renderLearnerStrip() {
  const h = getHeaderData(), el = SMDOC.getElementById('learnerStrip');
  if (!el) return;
  const bits = [h.student_reg, h.assignment_title, h.date_submitted ? 'Submitted ' + h.date_submitted : ''].filter(Boolean);
  el.innerHTML = '<h2></h2>' + bits.map(() => '<span class="meta"></span>').join('') + '<span class="tag"></span>';
  el.querySelector('h2').textContent = h.student_name || 'Unnamed learner';
  el.querySelectorAll('.meta').forEach((m, i) => m.textContent = bits[i]);
  el.querySelector('.tag').textContent = resub.on ? resub.type : 'First submission';
  const late = submissionStatus();
  if (late.state !== 'unknown' && late.state !== 'ontime') {
    const t = document.createElement('span');
    t.className = 'tag ' + (late.state === 'ext-ontime' ? 'ext' : 'late');
    t.textContent = late.state === 'late' ? `${plural(late.days, 'day')} late` : late.state === 'ext-late' ? `${plural(late.days, 'day')} after extension` : 'With extension';
    el.appendChild(t);
  }
  const fn = SMDOC.getElementById('fbName'); if (fn) fn.textContent = firstName(h.student_name) || 'the learner';
}
const ICON_OK = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="stroke: var(--yes)" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>';
const ICON_WARN = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="stroke: var(--resub)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 4l9 16H3z"/><path d="M12 10v4M12 17.5v.01"/></svg>';
const ICON_BAD = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="stroke: var(--no)" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7.5v5.5M12 16.5v.01"/></svg>';
function renderMiniChecks() {
  const el = SMDOC.getElementById('miniChecks');
  if (!el) return;
  const head = '<span class="sp-label small">File checks</span>';
  if (!lastIntegrity) { el.innerHTML = head + `<span class="helper" style="margin:0">${integrityFailed ? "Couldn't read the file's details." : 'These run as soon as the work is uploaded.'}</span>`; return; }
  const flags = lastIntegrity.flags || [];
  const top = flags.filter(f => f.level === 'high').concat(flags.filter(f => f.level === 'medium')).slice(0, 3);
  let html = head + (top.length
    ? top.map(f => `<div class="mc">${f.level === 'high' ? ICON_BAD : ICON_WARN}<span>${escapeHtml(f.text)}</span></div>`).join('')
    : `<div class="mc">${ICON_OK}<span>No strong or moderate indicators in the file</span></div>`);
  if (lastAiIntegrity && lastAiIntegrity.ai_likelihood) {
    const lvl = String(lastAiIntegrity.ai_likelihood).toLowerCase();
    html += `<div class="mc">${lvl === 'high' ? ICON_BAD : lvl === 'medium' ? ICON_WARN : ICON_OK}<span>Second marker rates AI-writing likelihood as ${escapeHtml(lvl)}</span></div>`;
  }
  if (flags.length > top.length) html += `<span class="helper" style="margin:0">${flags.length - top.length} more note${flags.length - top.length > 1 ? 's' : ''}</span>`;
  el.innerHTML = html + '<button type="button" class="btn btn-link btn-sm" style="align-self:flex-start" data-sm-click="goStep(4)">See all checks</button>';
}

// ── the work: Word files as a readable page, PDFs page by page ──
const workKey = () => fileBase64 ? fileNameStr + ':' + fileBase64.length : '';
async function ensureWork() {
  const key = workKey();
  if (!key) { work.key = ''; work.ready = false; return false; }
  if (work.key === key) { if (work.building) await work.building; return work.key === key && work.ready; }
  work.key = key; work.ready = false; work.error = ''; work.paras = null; work.pdf = null;
  work.kind = fileMediaType === 'application/pdf' ? 'pdf' : 'docx';
  const paper = SMDOC.getElementById('workPaper');
  paper.className = 'paper loading';
  paper.innerHTML = '<p class="helper"><span class="spinner-border spinner-border-sm"></span> Opening the work...</p>';
  const job = (async () => {
    try {
      if (work.kind === 'docx') {
        let blocks;
        try { blocks = await docxBlocks(fileBase64); }
        catch (e) { blocks = (await docxToText(fileBase64)).split(/\n+/).filter(x => x.trim()).map(text => ({ type: 'p', text })); }
        if (work.key === key) work.paras = blocks;
      } else await buildPdfPages(paper, key);
      if (work.key === key) work.ready = true;
    } catch (e) { if (work.key === key) work.error = (e && e.message) || String(e); }
  })();
  work.building = job;
  await job;
  if (work.building === job) work.building = null;
  return work.key === key && work.ready;
}
async function renderWork() {
  const paper = SMDOC.getElementById('workPaper');
  if (!paper) return;
  const nm = SMDOC.getElementById('workName'); if (nm) nm.textContent = fileNameStr || '';
  if (!fileBase64) {
    work.key = '';
    paper.className = 'paper empty';
    paper.innerHTML = '<p class="helper">The learner\'s work shows here once it\'s uploaded.</p>';
    renderNotes(); return;
  }
  const ok = await ensureWork();
  if (!ok) {
    if (work.error) { paper.className = 'paper empty'; paper.innerHTML = `<p class="helper">Couldn't show the work here (${escapeHtml(work.error)}). The marked PDF and Word downloads still work.</p>`; }
    renderNotes(); return;
  }
  if (work.kind === 'docx') paintDocx(paper);
  renderNotes();
}
// Finds a quote in a paragraph, forgiving curly quotes, dashes, case and spacing
function findQuote(text, quote) {
  const fold = x => x.replace(/[‘’‛′`]/g, "'").replace(/[“”″]/g, '"').replace(/[‐‑‒–—―]/g, '-').toLowerCase();
  const T = fold(text), Q = fold(String(quote || '').trim());
  if (Q.length < 4) return null;
  const i = T.indexOf(Q);
  if (i >= 0) return [i, i + Q.length];
  const words = Q.split(/\s+/).filter(Boolean).map(w => w.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'));
  if (words.length < 3) return null;
  let m = new RegExp(words.join('\\s+')).exec(T);
  if (m) return [m.index, m.index + m[0].length];
  m = new RegExp(words.slice(0, 5).join('\\s+')).exec(T);
  if (m) { let end = Math.min(T.length, m.index + Q.length); const stop = T.slice(m.index + m[0].length).search(/[.!?](\s|$)/); if (stop >= 0) end = Math.min(end, m.index + m[0].length + stop + 1); return [m.index, end]; }
  return null;
}
// Reads headings, lists, tables and image spots from the Word file
async function docxBlocks(b64) {
  const pkg = await openDocx(b64, { base64: true });
  const doc = new DOMParser().parseFromString(await pkg.main(), 'application/xml');
  const W = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
  const body = doc.getElementsByTagNameNS(W, 'body')[0];
  if (!body) throw new Error('no body');
  const textOf = node => {
    let t = '';
    for (const c of node.children) {
      const n = c.localName;
      if (n === 't') t += c.textContent;
      else if (n === 'tab') t += '\t';
      else if (n === 'br' || n === 'cr') t += '\n';
      else if (n === 'delText' || n === 'instrText' || n === 'del' || n === 'rPr' || n === 'pPr') continue;
      else t += textOf(c);
    }
    return t;
  };
  const styleOf = p => { const st = p.getElementsByTagNameNS(W, 'pStyle')[0]; return st ? (st.getAttributeNS(W, 'val') || st.getAttribute('w:val') || '') : ''; };
  const out = [];
  const addPara = p => {
    const style = styleOf(p), text = textOf(p);
    const pics = p.getElementsByTagNameNS(W, 'drawing').length + p.getElementsByTagNameNS(W, 'pict').length;
    const lvl = (style.match(/(?:heading|kop|titre|berschrift)\s*(\d)/i) || [])[1];
    const type = /^title$/i.test(style) ? 'title' : lvl ? (+lvl <= 1 ? 'h' : 'h4') : /^subtitle$/i.test(style) ? 'h4' : p.getElementsByTagNameNS(W, 'numPr').length || /list/i.test(style) ? 'li' : 'p';
    if (text.trim()) out.push({ type, text });
    for (let i = 0; i < pics; i++) out.push({ type: 'img' });
  };
  const walk = parent => {
    for (const c of parent.children) {
      if (c.localName === 'p') addPara(c);
      else if (c.localName === 'tbl') {
        const rows = [...c.children].filter(r => r.localName === 'tr').map(tr => [...tr.children].filter(tc => tc.localName === 'tc')
          .map(tc => [...tc.children].filter(x => x.localName === 'p').map(textOf).join('\n').trim()));
        if (rows.some(r => r.some(Boolean))) out.push({ type: 'table', rows });
      } else if (c.localName === 'sdt') { const sc = [...c.children].find(x => x.localName === 'sdtContent'); if (sc) walk(sc); }
    }
  };
  walk(body);
  return out;
}
function paintDocx(paper) {
  const used = new Set();
  const markText = p => {
    const ranges = [];
    lastAnnotations.forEach((a, i) => {
      if (used.has(i) || !a.quote) return;
      const r = findQuote(p, a.quote);
      if (r && !ranges.some(x => r[0] < x[1] && x[0] < r[1])) { ranges.push([r[0], r[1], a, i]); used.add(i); }
    });
    ranges.sort((x, y) => x[0] - y[0]);
    let out = '', pos = 0;
    ranges.forEach(([st, en, a, i]) => {
      out += escapeHtml(p.slice(pos, st)) + `<mark class="ann-mark t-${a.type}${a.include ? '' : ' off'}${a.manual ? ' by-me' : ''}" data-ann="${i}" data-code="${escapeHtml(a.code || '')}">${escapeHtml(p.slice(st, en))}</mark>`;
      pos = en;
    });
    return out + escapeHtml(p.slice(pos));
  };
  const IMG = '<div class="img-ph"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="M21 17l-5-5-9 8"/></svg>Image (shown in the marked PDF)</div>';
  const html = work.paras.map(b => {
    if (b.type === 'img') return IMG;
    if (b.type === 'table') return `<table class="doc-table"><tbody>${b.rows.map(r => `<tr>${r.map(c => `<td>${markText(c)}</td>`).join('')}</tr>`).join('')}</tbody></table>`;
    const inner = markText(b.text);
    return b.type === 'title' ? `<h2>${inner}</h2>` : b.type === 'h' ? `<h3>${inner}</h3>` : b.type === 'h4' ? `<h4>${inner}</h4>` : b.type === 'li' ? `<p class="li">${inner}</p>` : `<p>${inner}</p>`;
  }).join('');
  paper.className = 'paper doc';
  paper.innerHTML = html || '<p class="helper">No text found in this Word file. It may be mostly images; the marked PDF keeps them.</p>';
}
async function buildPdfPages(paper, key) {
  if (!window.BTECPdfjs) throw new Error('the PDF viewer did not load');
  const lib = await window.BTECPdfjs();
  const data = Uint8Array.from(atob(fileBase64), c => c.charCodeAt(0));
  const doc = await lib.getDocument({ data, isEvalSupported: false }).promise;
  if (work.key !== key) return;
  work.pdf = doc;
  const n = Math.min(doc.numPages, 80);
  const vp = (await doc.getPage(1)).getViewport({ scale: 1 });
  paper.className = 'paper pdf';
  paper.innerHTML = '';
  if (work.io) work.io.disconnect();
  work.io = new IntersectionObserver(es => es.forEach(en => {
    if (en.isIntersecting) { work.io.unobserve(en.target); drawPdfPage(doc, en.target).catch(() => {}); }
  }), { rootMargin: '900px 0px' });
  for (let p = 1; p <= n; p++) {
    const d = document.createElement('div');
    d.className = 'pdf-page'; d.dataset.page = p; d.style.aspectRatio = vp.width + ' / ' + vp.height;
    d.innerHTML = `<span class="pdf-num">Page ${p}</span>`;
    paper.appendChild(d); work.io.observe(d);
  }
  if (doc.numPages > n) paper.insertAdjacentHTML('beforeend', `<p class="helper">Showing the first ${n} of ${doc.numPages} pages.</p>`);
}
async function drawPdfPage(doc, div) {
  const page = await doc.getPage(+div.dataset.page);
  const vp1 = page.getViewport({ scale: 1 });
  div.style.aspectRatio = vp1.width + ' / ' + vp1.height;
  const w = div.clientWidth || 720, dpr = Math.min(2, window.devicePixelRatio || 1);
  const vp = page.getViewport({ scale: (w / vp1.width) * dpr });
  const c = document.createElement('canvas');
  c.width = Math.round(vp.width); c.height = Math.round(vp.height);
  c.setAttribute('role', 'img'); c.setAttribute('aria-label', `Page ${div.dataset.page} of the learner's work`);
  await page.render({ canvas: c, canvasContext: c.getContext('2d'), viewport: vp }).promise;
  div.appendChild(c);
}

// ── pencil notes beside the work ──
function renderNotes() {
  const box = SMDOC.getElementById('annotationList');
  if (!box) return;
  box.style.height = '';
  if (!lastAnnotations.length) {
    box.innerHTML = `<p class="helper notes-empty">${aiState === 'running' ? "The second marker's comments will appear here, next to the passage they're about." : 'Select text in the work and press Comment on this, or use Add comment for a general note.'}</p>`;
    return;
  }
  box.innerHTML = '';
  let n = 0;
  lastAnnotations.forEach((a, i) => {
    const el = document.createElement('div');
    el.className = `ann t-${a.type}${a.include ? '' : ' off'}${a.manual ? ' manual' : ''}`;
    el.dataset.ann = i; el.dataset.code = a.code || '';
    el.innerHTML = `<div class="ann-head"><label><input class="form-check-input" type="checkbox" ${a.include ? 'checked' : ''}><span class="ann-n">${a.include ? ++n : ''}</span></label><button type="button" class="ann-code"></button><button type="button" class="ann-type"></button>${a.qm ? `<span class="ann-qm">${escapeHtml(a.qm)}</span>` : ''}${a.manual ? '<button type="button" class="ann-del" aria-label="Remove this comment">&#215;</button>' : ''}</div><textarea rows="1"${a.manual ? ' placeholder="Your comment"' : ''}></textarea><div class="ann-quote"></div>`;
    const codeBtn = el.querySelector('.ann-code');
    codeBtn.textContent = a.code || 'Note';
    codeBtn.onclick = () => { if (a.code) jumpToCode(a.code); };
    const typeBtn = el.querySelector('.ann-type');
    typeBtn.textContent = { met: 'met', not_met: 'not yet', note: 'note' }[a.type] || 'note';
    typeBtn.title = 'Change to met, not yet or note';
    typeBtn.onclick = () => { a.type = { note: 'met', met: 'not_met', not_met: 'note' }[a.type] || 'note'; renderWork(); scheduleSave(); };
    const del = el.querySelector('.ann-del');
    if (del) del.onclick = () => { lastAnnotations.splice(i, 1); renderWork(); scheduleSave(); };
    const cb = el.querySelector('input');
    cb.setAttribute('aria-label', `Include this ${a.code || ''} comment on the marked copies`);
    cb.onchange = () => { a.include = cb.checked; renderWork(); };
    const ta = el.querySelector('textarea');
    ta.value = a.text;
    ta.setAttribute('aria-label', `Comment ${a.code || ''}`);
    ta.oninput = () => { a.text = ta.value; fitNote(ta); placeNotesSoon(); };
    ta.onblur = () => {
      a.text = noDashes(ta.value); ta.value = a.text;
      // an empty comment of your own is dropped when you click away
      if (a.manual && !a.text.trim()) setTimeout(() => { const k = lastAnnotations.indexOf(a); if (k >= 0 && !lastAnnotations[k].text.trim()) { lastAnnotations.splice(k, 1); renderWork(); } }, 150);
    };
    el.onmouseenter = () => hotMarks(i, true);
    el.onmouseleave = () => hotMarks(i, false);
    box.appendChild(el);
    fitNote(ta);
  });
  SMDOC.querySelectorAll('#workPaper mark.ann-mark').forEach(m => m.onclick = () => {
    const el = box.querySelector(`.ann[data-ann="${m.dataset.ann}"]`);
    if (el) { el.classList.add('hot'); setTimeout(() => el.classList.remove('hot'), 1400); }
    if (m.dataset.code) jumpToCode(m.dataset.code);
  });
  const cur = rowsEl()[currentRowIdx];
  if (cur) litMarks(cur, false);
  placeNotes();
}
function fitNote(ta) { if (!ta.offsetParent) return; ta.style.height = 'auto'; ta.style.height = ta.scrollHeight + 'px'; }
function hotMarks(i, on) { SMDOC.querySelectorAll(`#workPaper mark[data-ann="${i}"]`).forEach(m => m.classList.toggle('hot', on)); }
function placeNotesSoon() { clearTimeout(notesTimer); notesTimer = setTimeout(placeNotes, 60); }
function placeNotes() {
  const box = SMDOC.getElementById('annotationList'), wrap = SMDOC.getElementById('workWrap'), paper = SMDOC.getElementById('workPaper');
  if (!box || !wrap || !wrap.offsetParent) return;
  box.querySelectorAll('.notes-sub').forEach(x => x.remove());
  const items = [...box.querySelectorAll('.ann')];
  items.forEach(el => fitNote(el.querySelector('textarea')));
  if (wrap.clientWidth < 680 || wrap.classList.contains('hide-notes')) {
    items.forEach(el => { el.style.position = ''; el.style.top = ''; });
    box.style.height = '';
    return;
  }
  const base = box.getBoundingClientRect().top;
  const placed = [], loose = [];
  items.forEach(el => {
    const i = el.dataset.ann, a = lastAnnotations[i] || {};
    const mark = paper.querySelector(`mark[data-ann="${i}"]`);
    let anchor = mark;
    if (!anchor && work.kind === 'pdf' && a.page) anchor = paper.querySelector(`.pdf-page[data-page="${a.page}"]`);
    el.querySelector('.ann-quote').textContent = !mark && a.quote ? `"${a.quote}"` : '';
    if (anchor) { el._top = anchor.getBoundingClientRect().top - base + (mark ? -6 : 14); placed.push(el); } else loose.push(el);
  });
  placed.sort((x, y) => x._top - y._top);
  let bottom = 0;
  placed.forEach(el => { const top = Math.max(el._top, bottom); el.style.position = 'absolute'; el.style.top = top + 'px'; bottom = top + el.offsetHeight + 10; });
  if (loose.length) {
    const h = document.createElement('div');
    h.className = 'notes-sub'; h.textContent = placed.length ? 'Other comments' : 'Comments';
    box.appendChild(h);
    h.style.position = 'absolute'; h.style.top = bottom + 'px'; bottom += h.offsetHeight + 4;
    loose.forEach(el => { box.appendChild(el); el.style.position = 'absolute'; el.style.top = bottom + 'px'; bottom += el.offsetHeight + 10; });
  }
  box.style.height = bottom + 'px';
}
if (window.ResizeObserver) { const ro = new ResizeObserver(() => placeNotesSoon()); ro.observe(SMDOC.getElementById('workWrap')); }
// keep the sticky side panels clear of the bottom bar, wherever the page is scrolled
var fitQueued = false;
function fitPanels() {
  fitQueued = false;
  if (currentStep !== 5) return;
  const nav = SMDOC.getElementById('wizardNav');
  const navTop = nav ? nav.getBoundingClientRect().top : window.innerHeight;
  SMDOC.querySelectorAll('.marksheet, .sidepane').forEach(el => {
    if (getComputedStyle(el).position !== 'sticky') { el.style.maxHeight = ''; return; }
    el.style.maxHeight = Math.max(240, navTop - Math.max(el.getBoundingClientRect().top, 16) - 12) + 'px';
  });
}
function fitPanelsSoon() { if (!fitQueued) { fitQueued = true; requestAnimationFrame(fitPanels); } }
__won('scroll', fitPanelsSoon, { passive: true });
__won('resize', fitPanelsSoon);
if (document.fonts && document.fonts.ready) document.fonts.ready.then(placeNotesSoon);

// ── keyboard marking ──
__on('keydown', e => {
  if (currentStep !== 5 || e.ctrlKey || e.metaKey || e.altKey) return;
  if (SMDOC.getElementById('settingsCard').classList.contains('show')) return;
  const t = e.target;
  if (t && t.closest && t.closest('input, textarea, select, [contenteditable="true"]')) return;
  const rows = rowsEl();
  if (!rows.length) return;
  const row = rows[currentRowIdx] || rows[0];
  const k = e.key.length === 1 ? e.key.toLowerCase() : e.key;
  if (k === 'y' || k === 'n' || k === 'r') { e.preventDefault(); stampRow(row, k.toUpperCase(), true); }
  else if (k === 'ArrowDown' || k === 'j') { e.preventDefault(); moveCurrent(1); }
  else if (k === 'ArrowUp' || k === 'k') { e.preventDefault(); moveCurrent(-1); }
  else if (k === 'f') { e.preventDefault(); toggleFocus(); }
  else if (k === 'c') { e.preventDefault(); const ta = row.querySelector('.desc-input'); if (ta) ta.focus(); }
  else if (k === 'Escape' && isFocus()) toggleFocus(false);
  else if (k === 'Enter' && isFocus() && !(t && t.closest && t.closest('button, a'))) { e.preventDefault(); moveCurrent(1, true); }
});

applyTheme();
if (storeGet('btec_learner_mode') === 'anon') setLearnerMode('anon');
renderStepper();
goStep(1);
checkForDraft();

/* ===== iv.js ===== */
// ─────────────────────────────────────────────────────────────
// Internal verification.
//
// IV sampling: check a colleague's marked work. The second marker reads the
// learner's work on its own (it never sees the assessor's marking); the
// assessor's marking is read separately; the IV makes their own decision on
// each criterion; the app compares and fills the college's "Internal
// Verification of Assessment Decisions" form.
//
// Brief IV: fills the college's "Internal Verification of Assignment Brief"
// form for the brief chosen in step 1.
//
// Loaded after the main script, so it can wrap the functions it extends.
// ─────────────────────────────────────────────────────────────

const IV_DEC_IDS = {
  programme: '5D100003', assessor: '5D100009', iv: '5D10000F', unit: '5D100015', assignment: '5D10001B', learner: '5D100021', awarded: '5D100027',
  q: { match: '5D100033', accurate: '5D100039', grade: '5D10003F', constructive: '5D100045', linked: '5D10004B', improve: '5D100051', authentic: '5D100057' },
  overall_yes: '5D10005E', overall_no: '5D100060',
  actions: [['5D100076', '5D100077', '5D100078'], ['5D10007E', '5D10007F', '5D100080']],
  comments: '5D10008A', iv_sig: '5D100099', iv_sig_date: '5D10009B',
};
const IV_BRIEF_IDS = {
  programme: '1304A15A', assessor: '447A26D7', iv: '166476F2', unit: '73A4188D', assignment: '4E20AC01', aims: '6D1E072E', authorised: '5BCC0325',
  q: { details: '033974DD', deadlines: '0FE933DD', timeframe: '69AC398E', scenario: '05C67914', aims: '341B391A', criteria: '66361C5D', requirements: '5D62C649', evidence: '27818DFB', valid: '401462D4' },
  overall_yes: '0C72FDA7', overall_no: '7D668BC7',
  actions: [['6BA9433C', '5785481C', '2F2C48FE'], ['7C306B9D', '689D506E', '76238976']],
  comments: '3E4A1280', iv_sig: '626D4165', iv_sig_date: '108C0DA0',
};
const IV_QUESTIONS = [
  ['match', 'Do the criteria awarded match those targeted by the brief?'],
  ['accurate', 'Has the work been assessed accurately against each criterion?'],
  ['grade', 'Is the grade awarded supported by the evidence?'],
  ['constructive', 'Is the feedback to the learner constructive?'],
  ['linked', 'Is the feedback linked to the relevant criteria?'],
  ['improve', 'Does the feedback identify opportunities to improve?'],
  ['authentic', 'Has authenticity been considered, with any concerns followed up?'],
  ['overall', 'Overall, are the assessment decisions accurate?'],
];
const BRIEF_QUESTIONS = [
  ['details', 'Are the programme and unit details accurate?'],
  ['deadlines', 'Are clear deadlines for assessment given?'],
  ['timeframe', 'Is the time frame of an appropriate duration?'],
  ['scenario', 'Is there a suitable vocational scenario or context?'],
  ['aims', 'Are the learning aims/outcomes to be addressed stated accurately?'],
  ['criteria', 'Does each task show which criteria are being addressed?'],
  ['requirements', 'Do the tasks meet the assessment requirements of the unit/s?'],
  ['evidence', 'Is it clear what evidence the learner needs to generate?'],
  ['valid', 'Is it likely to generate evidence that is valid and sufficient?'],
];
const IV_CALL_WORDS = { Y: 'Achieved', P: 'Part achieved', N: 'Not achieved' };

function newIvState() {
  return {
    calls: {}, how: {}, evidence: {}, extra: [], source: '', markerText: '', feedback: '', feedbackEdited: false,
    review: null, reviewState: 'idle', reviewError: '', contradictions: [], asrGradeText: '',
    checklist: {}, actions: '', actionsEdited: false, target: '',
  };
}
var iv = newIvState();

// ── small helpers ──
const ivFirst = n => String(n || '').trim().split(/\s+/)[0] || '';
const ivLc = s => s ? s.charAt(0).toLowerCase() + s.slice(1) : '';
const ivAiFor = code => lastResults.find(r => r.code.toLowerCase() === String(code).toLowerCase());
function ivAddDays(days) { const d = new Date(); d.setUTCDate(d.getUTCDate() + days); return fmtUk(new Date(Date.UTC(d.getUTCFullYear(), d.getUTCMonth(), d.getUTCDate()))); }
function ivName() { return ((SMDOC.getElementById('iv_name') || {}).value || '').trim(); }
function ivLearnerLabel() {
  const h = getHeaderData();
  if (learnerMode === 'anon') return h.student_reg || 'Anonymous learner';
  return [h.student_name, h.student_reg ? `(${h.student_reg})` : ''].filter(Boolean).join(' ') || 'Learner not named';
}
function ivTargets() { return getCriteria().filter(c => c.code); }
function ivMatchCode(code) {
  const targets = ivTargets();
  const exact = targets.find(t => codeKey(t.code) === codeKey(code));
  if (exact) return exact.code;
  const a = splitUnit(code);
  const same = targets.filter(t => { const b = splitUnit(t.code); return codeKey(b.code) === codeKey(a.code) && (!a.unit || !b.unit || a.unit.replace(/^U/, '') === b.unit.replace(/^U/, '')); });
  return same.length === 1 ? same[0].code : '';
}
const ivCall = code => iv.calls[codeKey(code)] || '';

// Where each criterion stands: the assessor's call, yours, and whether you agree
function ivStats() {
  const rows = ivTargets().map(c => {
    const asr = ivCall(c.code), you = c.achieved;
    const asrMet = asr === 'Y', youMet = you === 'Y';
    const verdict = !asr || !you ? '' : asrMet === youMet ? 'agree' : asrMet ? 'over' : 'under';
    return { code: c.code, asr, you, verdict };
  });
  const complete = rows.length > 0 && rows.every(r => r.asr && r.you);
  const asrGrade = rows.length && rows.every(r => r.asr) ? gradeOf(rows, r => r.asr === 'Y') : null;
  const yourGrade = rows.length && rows.every(r => r.you) ? gradeOf(rows, r => r.you === 'Y') : null;
  return { rows, complete, asrGrade, yourGrade, agree: rows.filter(r => r.verdict === 'agree').length, disagree: rows.filter(r => r.verdict === 'over' || r.verdict === 'under') };
}

// ═════════════════════════ The mode ═════════════════════════
function setIvMode(on) {
  ivMode = !!on;
  SMROOT.classList.toggle('iv-on', ivMode);
  storeSet('btec_iv_mode', ivMode ? '1' : '0');
  SMDOC.querySelectorAll('input[name="workMode"]').forEach(r => r.checked = (r.value === 'iv') === ivMode);
  ivLabels(); updateContext(); renderLearnerStrip();
  if (typeof refreshDesk === 'function') refreshDesk();
  ivRenderAssessorCard(); ivRenderSide();
  if (!feedbackEdited) composeFeedback(true);
  refreshNav(); scheduleSave();
}
// Swap the wording of anything marked data-iv
function ivLabels() {
  SMDOC.querySelectorAll('[data-iv]').forEach(el => {
    if (el.dataset.mark === undefined) el.dataset.mark = el.textContent;
    el.textContent = ivMode ? el.dataset.iv : el.dataset.mark;
  });
  const fb = SMDOC.getElementById('feedbackBox');
  if (fb) {
    if (fb.dataset.phMark === undefined) fb.dataset.phMark = fb.dataset.ph || '';
    fb.dataset.ph = ivMode ? 'Your comments for the assessor appear here once you have decided each criterion: where you agree, where you don\'t and why, and anything about their feedback. Edit freely.' : fb.dataset.phMark;
  }
  const to = SMDOC.getElementById('emailTo'); if (to) to.placeholder = ivMode ? "Assessor's email address" : "Learner's email address";
}

// ═════════════════════════ The assessor's decisions (step 4) ═════════════════════════
function ivRenderAssessorCard() {
  const el = SMDOC.getElementById('ivAssessorCard');
  if (!el) return;
  if (!ivMode) { el.innerHTML = ''; return; }
  const targets = ivTargets();
  const reading = iv.reviewState === 'running' && iv.source === 'turnitin';
  let status;
  if (!fileBase64) status = 'Upload the marked work first.';
  else if (reading) status = '<span class="spinner-border spinner-border-sm text-primary me-2" role="status" aria-hidden="true"></span>Reading the marking in the Turnitin download. The second marker never sees it.';
  else if (iv.source === 'turnitin' && iv.reviewState === 'done') status = `Read from the Turnitin download. Check each one against the marking before you carry on.`;
  else if (iv.source === 'record') status = 'Taken from their assessment record.';
  else if (iv.reviewState === 'error') status = `Couldn't read the marking: ${escapeHtml(iv.reviewError)} Enter the decisions below instead.`;
  else status = "Enter the assessor's decision for each criterion, or load their assessment record.";
  const missing = targets.filter(t => !ivCall(t.code)).length;
  const rows = targets.map((t, i) => {
    const v = ivCall(t.code), ev = iv.evidence[codeKey(t.code)] || '';
    return `<div class="iva-row" data-i="${i}"><span class="code-tag">${escapeHtml(t.code)}</span>
      <div class="iva-opts" role="radiogroup" aria-label="Assessor's decision for ${escapeHtml(t.code)}">${['Y', 'P', 'N'].map(o => `<button type="button" class="iva-opt o-${o}${v === o ? ' on' : ''}" role="radio" aria-checked="${v === o}" data-v="${o}">${IV_CALL_WORDS[o]}</button>`).join('')}</div>
      <span class="iva-ev">${escapeHtml(ev)}</span></div>`;
  }).join('');
  const anonTip = learnerMode === 'anon' && iv.source === 'turnitin' ? '<p class="helper" style="margin:6px 0 0">Keeping it anonymous? Add the learner\'s name to "Other names to hide" in step 2, so it\'s taken out of the assessor\'s comments too.</p>' : '';
  el.innerHTML = `<div class="step-title">The assessor's decisions</div>
    <p class="helper" style="margin:0 0 10px">${status}</p>
    ${iv.contradictions.length ? `<div class="iva-warn">${ICON_WARN}<div><strong>The marking contradicts itself</strong><ul>${iv.contradictions.map(c => `<li>${escapeHtml(c)}</li>`).join('')}</ul></div></div>` : ''}
    <div class="iva-grid">${rows || '<p class="helper">Choose the unit and criteria in step 1.</p>'}</div>
    ${missing && fileBase64 && !reading ? `<p class="helper" style="margin:8px 0 0;color:var(--resub)">${missing} still to enter.</p>` : ''}
    <div class="csv-bar" style="margin:12px 0 0">
      <button class="btn btn-sm btn-outline-secondary btn-csv" data-sm-click="SMDOC.getElementById('ivRecordInput').click()">Load their assessment record</button>
      ${iv.source === 'turnitin' && !reading ? '<button class="btn btn-link btn-sm linkish" data-sm-click="ivReadMarking(\'all\')">Read the marking again</button>' : ''}
    </div>
    <div class="field-group" style="margin-top:14px">
      <label class="field-label" for="ivFeedback">Their feedback to the learner</label>
      <textarea class="form-control field-input" id="ivFeedback" rows="4" placeholder="Paste the assessor's feedback here if it isn't filled in. It's used for the feedback questions on the IV record."></textarea>
      <div class="fb-tools"><span class="helper" style="margin:0">${iv.review ? 'Feedback checked.' : 'Checked by the second marker for the feedback questions.'}</span><button class="btn btn-link btn-sm linkish" data-sm-click="ivReadMarking('feedback')"${iv.reviewState === 'running' ? ' disabled' : ''}>Check their feedback</button></div>
    </div>${anonTip}`;
  el.querySelector('#ivFeedback').value = iv.feedback;
  el.querySelector('#ivFeedback').oninput = e => { iv.feedback = e.target.value; iv.feedbackEdited = true; scheduleSave(); };
  el.querySelectorAll('.iva-row').forEach(r => r.querySelectorAll('.iva-opt').forEach(b => b.onclick = () => {
    const code = targets[+r.dataset.i].code;
    iv.calls[codeKey(code)] = b.dataset.v; iv.how[codeKey(code)] = 'you';
    ivChanged();
  }));
}
function ivChanged() {
  ivRenderAssessorCard();
  if (typeof refreshDesk === 'function') refreshDesk();
  updateGrade(); ivRenderSide(); refreshNav(); scheduleSave();
}

// Their Pearson assessment record: the checker's own copy of the decisions, or the form itself
async function ivLoadRecord(file) {
  if (!file) return;
  if (!/\.docx$/i.test(file.name)) { toast('Add the assessment record as a Word (.docx) file.', 'error'); return; }
  try {
    const zip = await JSZip.loadAsync(await file.arrayBuffer());
    const docF = zip.file('word/document.xml');
    if (!docF) throw new Error("That file doesn't look like a Word document.");
    const xml = await docF.async('string');
    const found = {}; let general = '', header = {};
    const j = zip.file('btec-checker/decision.json');
    let data = null;
    if (j) { try { data = JSON.parse(await j.async('string')); } catch (e) { data = null; } }
    if (data && Array.isArray(data.criteria)) {
      data.criteria.forEach(c => { if (/^[YNR]$/.test(c.achieved)) found[c.code] = c.achieved; });
      general = data.feedback || ''; header = data.header || {};
    } else {
      if (!xml.includes(PARA_IDS.criteria_achieved[0])) throw new Error("This doesn't look like the Pearson assessment record. Enter the decisions by hand instead.");
      const ids = /^[YNR]$/.test(paraText(xml, RESUB_IDS.criteria_achieved[0]).toUpperCase()) ? RESUB_IDS : PARA_IDS;
      for (let i = 0; i < 3; i++) {
        const code = paraText(xml, ids.criteria_code[i]), ach = paraText(xml, ids.criteria_achieved[i]).toUpperCase();
        if (code && /^[YNR]$/.test(ach)) found[code] = ach;
      }
      const rest = [];
      paraText(xml, ids.general_comments).split('\n').forEach(line => {
        const m = line.match(/^([^()\n]{2,24}) \(([YNR])\)(?::\s?(.*))?$/);
        if (m) found[m[1]] = m[2]; else rest.push(line);
      });
      general = rest.join('\n').trim();
      ['student_name', 'student_reg', 'assessor_name'].forEach(k => header[k] = paraText(xml, PARA_IDS[k]));
    }
    let n = 0;
    Object.entries(found).forEach(([code, v]) => { const t = ivMatchCode(code); if (t) { iv.calls[codeKey(t)] = v === 'Y' ? 'Y' : 'N'; iv.how[codeKey(t)] = 'record'; n++; } });
    if (!n) throw new Error("None of the record's criteria match the ones ticked in step 1.");
    iv.source = 'record';
    if (general && (!iv.feedbackEdited || !iv.feedback)) iv.feedback = noDashes(general);
    ['student_name', 'student_reg', 'assessor_name'].forEach(k => { const el = SMDOC.getElementById(k); if (el && !el.value.trim() && header[k]) el.value = header[k]; });
    updateContext(); ivChanged();
    toast(`Assessor's decisions loaded for ${plural(n, 'criterion').replace('criterions', 'criteria')}.`, 'ok');
    if (iv.feedback && getApiKey()) ivReadMarking('feedback');
  } catch (err) { toast(err.message, 'error'); }
}

// Turnitin's own text in a download: stamps, comment boxes, the feedback summary
async function turnitinMarkerText(b64) {
  const lib = await window.BTECPdfjs();
  const doc = await lib.getDocument({ data: Uint8Array.from(atob(b64), c => c.charCodeAt(0)), isEvalSupported: false }).promise;
  const out = []; let fonts = 0;
  for (let p = 1; p <= Math.min(doc.numPages, 80); p++) {
    const page = await doc.getPage(p);
    const tc = await page.getTextContent();
    await page.getOperatorList();
    const fontName = id => { try { return (page.commonObjs.get(id) || {}).name || ''; } catch (e) { return ''; } };
    const theirs = tc.items.filter(i => TURNITIN_FONT_RE.test(fontName(i.fontName)));
    fonts += theirs.length;
    const text = theirs.map(i => i.str + (i.hasEOL ? '\n' : ' ')).join('').split('\n').map(l => l.trim())
      .filter(l => l && !/^Page \d+ of \d+ - Grading and Feedback$/i.test(l) && !/^Submission ID/i.test(l) && !/^turnitin$/i.test(l)).join('\n');
    if (text) out.push(`--- Page ${p} ---\n${text}`);
  }
  if (fonts) return fixLigatures(out.join('\n\n'));
  return (await pdfTextOf(b64)).text;   // older GradeMark downloads: the text layer is the marking
}

async function ivAfterUpload() {
  await fileScanReady;
  if (!ivMode || !fileBase64) return;
  if (fileScan && fileScan.turnitin) {
    iv.source = 'turnitin';
    try { iv.markerText = await turnitinMarkerText(fileBase64); } catch (e) { iv.markerText = ''; }
    ivRenderAssessorCard();
    if (iv.markerText) ivReadMarking('all');
  } else ivRenderAssessorCard();
}

// One call to the AI, outside the blind check, with names hidden the same way
async function ivAsk(system, content, maxTokens) {
  const key = getApiKey();
  if (!key) throw new Error('No API key set. Add it in Settings.');
  const resp = await fetch('marker/api.php?a=ask', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'anthropic-version': '2023-06-01', 'X-Requested-With': 'crafticulum' },
    body: JSON.stringify({ model: (SMDOC.querySelector('input[name="model"]:checked') || {}).value || 'claude-opus-5-5', max_tokens: maxTokens || 3000, system, messages: [{ role: 'user', content }] })
  });
  const data = await resp.json();
  if (data.error) throw new Error(data.error.message);
  const raw = (data.content || []).map(b => b.text || '').join('');
  return JSON.parse(raw.slice(raw.indexOf('{'), raw.lastIndexOf('}') + 1));
}
function ivPrivate() {
  const saved = [privacyMap, privacyReport];
  startPrivacy();
  const map = privacyMap;
  privacyMap = saved[0]; privacyReport = saved[1];
  return {
    hide: t => { const s = [privacyMap, privacyReport]; privacyMap = map; privacyReport = { counts: {}, pdf: '', notes: [] }; try { return redactForAi(t); } finally { privacyMap = s[0]; privacyReport = s[1]; } },
    restore: v => { const s = privacyMap; privacyMap = map; try { return restoreFromAi(v); } finally { privacyMap = s; } },
  };
}

const IV_REVIEW_SYSTEM = `You are helping a further education internal verifier (IV) check a colleague's assessment decisions for a Pearson BTEC or TLM assignment.
You are given ONLY the assessor's marking: stamps, margin comments and summary feedback taken from a Turnitin download, or the feedback the assessor wrote. You do not see the learner's work, so never judge whether the work meets the criteria.

Return ONLY a JSON object, no markdown, no backticks:
{
  "decisions": [{"code": "criterion code exactly as listed", "call": "achieved" | "part" | "not_achieved" | "not_mentioned", "evidence": "the words in the marking that show this, max 20 words"}],
  "assessor_grade": "the overall grade the assessor gave, as written, or an empty string",
  "feedback_text": "the assessor's main feedback to the learner in their own words: the summary or overall comments, not every margin note. Empty string if there is none.",
  "contradictions": ["each place where the marking contradicts itself, in one plain sentence"],
  "feedback_checks": {
    "constructive": {"answer": "Y" | "N", "reason": "one short sentence"},
    "linked": {"answer": "Y" | "N", "reason": "one short sentence"},
    "improve": {"answer": "Y" | "N", "reason": "one short sentence"},
    "authentic": {"answer": "Y" | "N", "reason": "one short sentence"}
  }
}
How to read the marking:
- A stamp with only a criterion code does not by itself mean achieved: read the comment that goes with it. "You have described..." means achieved; "You haven't managed to..." or "not fully" means not achieved, or part achieved where they say so.
- "Part", "Part Achieved" and "Partial" mean part achieved. "Toward" or "Evidence toward" stamps are evidence towards a criterion, not a decision.
- Where the summary and the margin disagree about a criterion, use the later or more specific decision for "call", and record the disagreement in "contradictions".
- A listed criterion the marking never mentions is "not_mentioned". Include every listed criterion once, in order.
- constructive: does the feedback say what was done well and what to do next, in a supportive tone? linked: does it refer to the criteria, by code or wording? improve: does it give specific steps to improve or reach a higher grade? authentic: Y if nothing in the marking suggests an authenticity concern, or a concern (AI use, copying, missing sources) is raised and acted on; N if a concern is mentioned but not followed up.
Never use em dashes or en dashes; use commas, colons or full stops.`;

async function ivReadMarking(mode) {
  const targets = ivTargets();
  if (!targets.length) return;
  const feedbackOnly = mode === 'feedback';
  const text = feedbackOnly ? (iv.feedback || '').trim() : iv.markerText;
  if (!text) { toast(feedbackOnly ? "Add the assessor's feedback first." : 'No marking found in the download.', 'info'); return; }
  if (!getApiKey()) { iv.reviewState = 'error'; iv.reviewError = 'No API key set, so it can\'t be read automatically.'; ivRenderAssessorCard(); return; }
  iv.reviewState = 'running'; iv.reviewError = '';
  ivRenderAssessorCard(); ivRenderSide();
  try {
    const priv = ivPrivate();
    const crit = targets.map(c => `- ${c.code}: ${c.desc || '(wording not supplied)'}`).join('\n');
    const known = feedbackOnly ? '\n\nThe assessor\'s decisions are already known (return them as given in "decisions"):\n' + targets.map(c => `- ${c.code}: ${({ Y: 'achieved', P: 'part', N: 'not_achieved' })[ivCall(c.code)] || 'not_mentioned'}`).join('\n') : '';
    const label = feedbackOnly ? "The assessor's feedback to the learner" : "The assessor's marking, taken from a Turnitin download (Turnitin's stamps, comments and feedback summary, page by page)";
    const user = priv.hide(`Criteria targeted:\n${crit}${known}\n\n${label}:\n${text.slice(0, 60000)}\n\nReturn the JSON.`);
    const parsed = priv.restore(await ivAsk(IV_REVIEW_SYSTEM, [{ type: 'text', text: user }], 3000));
    ivApplyReview(parsed, feedbackOnly);
    iv.reviewState = 'done';
  } catch (err) {
    iv.reviewState = 'error'; iv.reviewError = err.message;
  }
  ivChanged();
  if (!feedbackEdited) composeFeedback(true);
}
function ivApplyReview(p, feedbackOnly) {
  const callOf = { achieved: 'Y', part: 'P', not_achieved: 'N', not_mentioned: 'N' };
  if (!feedbackOnly) {
    iv.extra = [];
    (p.decisions || []).forEach(d => {
      const t = ivMatchCode(d.code || '');
      if (!t) { if (d.call === 'achieved') iv.extra.push(d.code); return; }
      const k = codeKey(t);
      if (iv.how[k] === 'you') return;   // something the IV set by hand stays
      iv.calls[k] = callOf[d.call] || 'N'; iv.how[k] = 'read';
      iv.evidence[k] = noDashes(d.call === 'not_mentioned' ? 'Not mentioned in the marking, so not awarded.' : (d.evidence || ''));
    });
    iv.asrGradeText = noDashes(p.assessor_grade || '');
    if (p.feedback_text && !iv.feedbackEdited) iv.feedback = noDashes(p.feedback_text);
  }
  iv.contradictions = (p.contradictions || []).map(noDashes).filter(Boolean);
  const fc = p.feedback_checks || {};
  iv.review = {};
  ['constructive', 'linked', 'improve', 'authentic'].forEach(k => { const c = fc[k] || {}; iv.review[k] = { answer: /^[YN]$/.test(c.answer) ? c.answer : '', reason: noDashes(c.reason || '') }; });
}

// ═════════════════════════ Checklist, actions and comments ═════════════════════════
function ivSuggest() {
  const s = ivStats(), out = {};
  out.match = iv.extra.length ? 'N' : s.rows.length && s.rows.every(r => r.asr) ? 'Y' : '';
  out.accurate = s.complete ? (s.disagree.length ? 'N' : 'Y') : '';
  out.grade = s.complete && s.asrGrade && s.yourGrade ? (s.asrGrade.label === s.yourGrade.label ? 'Y' : 'N') : '';
  ['constructive', 'linked', 'improve', 'authentic'].forEach(k => { out[k] = (iv.review && iv.review[k] && iv.review[k].answer) || ''; });
  out.overall = s.complete ? (out.accurate === 'Y' && out.grade !== 'N' ? 'Y' : 'N') : '';
  return out;
}
function ivChecklist() {
  const sug = ivSuggest(), out = {};
  IV_QUESTIONS.forEach(([k]) => out[k] = iv.checklist[k] !== undefined ? iv.checklist[k] : sug[k]);
  return out;
}
function ivSuggestActions() {
  const s = ivStats(), lines = [];
  s.disagree.forEach(r => {
    const ai = ivAiFor(r.code) || {};
    if (r.verdict === 'over') lines.push(`Review ${r.code}: awarded, but not met in the IV's view${ai.need ? `. Still needs to ${ivLc(ai.need.replace(/\.$/, ''))}` : ''}.`);
    else lines.push(`Review ${r.code}: not awarded, but the evidence looks enough to meet it${ai.evidence ? ` (${ai.evidence.replace(/\.$/, '')})` : ''}.`);
  });
  if (s.complete && s.asrGrade && s.yourGrade && s.asrGrade.label !== s.yourGrade.label) lines.push(`Agree the grade: the assessor's decisions give ${s.asrGrade.label}, the IV's give ${s.yourGrade.label}.`);
  iv.contradictions.forEach(c => lines.push(`Correct the feedback so it doesn't contradict itself: ${c.replace(/\.$/, '')}.`));
  ['constructive', 'linked', 'improve'].forEach(k => { const c = iv.review && iv.review[k]; if (c && c.answer === 'N' && c.reason) lines.push(`Feedback: ${c.reason}`); });
  return noDashes(lines.join('\n'));
}
function ivActionList() {
  const text = (iv.actionsEdited ? iv.actions : ivSuggestActions()).trim();
  return text && !/^n\/?a$/i.test(text) ? text.split('\n').map(l => l.trim()).filter(Boolean) : [];
}
function ivComposeComments() {
  const s = ivStats(), h = getHeaderData();
  if (!s.rows.length) return '';
  const who = learnerMode === 'anon' ? (h.student_reg || 'the learner') : (h.student_name || 'the learner');
  const unit = (h.unit_title || '').split(':')[0];
  const parts = [`Sampled ${who}'s work for ${h.assignment_title || 'this assignment'}${unit ? ` (${unit})` : ''}.`];
  if (!s.complete) {
    const left = s.rows.filter(r => !r.asr || !r.you).map(r => r.code);
    parts.push(`Still to decide: ${left.join(', ')}.`);
    return noDashes(parts.join(' '));
  }
  if (!s.disagree.length) parts.push(`I agree with all ${s.rows.length} decisions${s.asrGrade ? ` and the grade of ${s.asrGrade.label}` : ''}.`);
  else {
    parts.push(`I agree with ${s.agree} of ${s.rows.length} decisions.`);
    s.disagree.forEach(r => {
      const ai = ivAiFor(r.code) || {};
      const why = (ai.evidence || '').replace(/\.$/, '');
      parts.push(r.verdict === 'over'
        ? `${r.code}: awarded, but I don't think it's met yet.${why ? ` ${why}.` : ''}${ai.need ? ` It still needs to ${ivLc(ai.need.replace(/\.$/, ''))}.` : ''}`
        : `${r.code}: not awarded, but I think the evidence meets it.${why ? ` ${why}.` : ''}`);
    });
    if (s.asrGrade && s.yourGrade && s.asrGrade.label !== s.yourGrade.label) parts.push(`That makes the grade ${s.yourGrade.label} rather than ${s.asrGrade.label}.`);
  }
  if (iv.review) {
    const bad = ['constructive', 'linked', 'improve'].filter(k => iv.review[k] && iv.review[k].answer === 'N');
    const good = ['constructive', 'linked', 'improve'].filter(k => iv.review[k] && iv.review[k].answer === 'Y');
    if (good.length === 3) parts.push('Your feedback is constructive, linked to the criteria and gives clear next steps.');
    bad.forEach(k => iv.review[k].reason && parts.push(`On the feedback: ${iv.review[k].reason}`));
  }
  iv.contradictions.forEach(c => parts.push(`One thing to fix: ${c}`));
  return noDashes(parts.join('\n\n'));
}

// The IV checklist and actions in the side pane of the desk
function ivRenderSide() {
  const box = SMDOC.getElementById('ivChecklist');
  if (!box) return;
  const cl = ivChecklist(), sug = ivSuggest();
  box.innerHTML = IV_QUESTIONS.map(([k, q]) => {
    const v = cl[k] || '';
    return `<div class="ivc-row${k === 'overall' ? ' ivc-overall' : ''}" data-k="${k}"><span class="ivc-q">${escapeHtml(q)}</span><span class="ivc-yn" role="radiogroup" aria-label="${escapeHtml(q)}">${['Y', 'N'].map(o => `<button type="button" class="ivc-btn o-${o}${v === o ? ' on' : ''}" role="radio" aria-checked="${v === o}" data-v="${o}">${o}</button>`).join('')}</span>${iv.checklist[k] !== undefined && iv.checklist[k] !== sug[k] ? '<span class="ivc-mine" title="You changed this">edited</span>' : ''}</div>`;
  }).join('');
  box.querySelectorAll('.ivc-row').forEach(r => r.querySelectorAll('.ivc-btn').forEach(b => b.onclick = () => {
    const k = r.dataset.k; iv.checklist[k] = iv.checklist[k] === b.dataset.v && sug[k] !== b.dataset.v ? undefined : b.dataset.v;
    if (iv.checklist[k] === undefined) delete iv.checklist[k];
    ivRenderSide(); scheduleSave();
  }));
  const ta = SMDOC.getElementById('ivActions');
  if (ta && SMDOC.activeElement !== ta) ta.value = iv.actionsEdited ? iv.actions : (ivSuggestActions() || 'n/a');
  const tg = SMDOC.getElementById('ivTarget');
  if (tg && SMDOC.activeElement !== tg) tg.value = iv.target || (ivActionList().length ? ivAddDays(14) : '');
  const note = SMDOC.getElementById('ivReviewNote');
  if (note) note.textContent = iv.reviewState === 'running' ? 'Checking their feedback...' : iv.review ? 'Feedback answers come from the second marker\'s read of their comments.' : 'Feedback answers fill in once their feedback has been checked (step 4).';
  const rb = SMDOC.getElementById('ivActionsReset'); if (rb) rb.style.display = iv.actionsEdited ? '' : 'none';
}

// ═════════════════════════ The desk: assessor vs you on each criterion ═════════════════════════
function ivDecorateRow(row) {
  let cell = row.querySelector('.iv-cell');
  if (!ivMode) { if (cell) cell.remove(); return; }
  if (!cell) { cell = document.createElement('div'); cell.className = 'iv-cell'; row.querySelector('.ai-cell').after(cell); }
  const code = rowCode(row), asr = ivCall(code), you = rowValue(row);
  const verdict = !asr || !you ? '' : (asr === 'Y') === (you === 'Y') ? 'agree' : asr === 'Y' ? 'over' : 'under';
  const vText = { agree: 'You agree', over: 'Over-awarded', under: 'Under-awarded' }[verdict];
  cell.innerHTML = `<span class="asr a-${asr || 'none'}">Assessor: <b>${asr ? IV_CALL_WORDS[asr] : 'not entered'}</b></span>${vText ? `<span class="verdict v-${verdict}">${vText}</span>` : ''}`;
}

// ═════════════════════════ The IV record (Word) ═════════════════════════
function ivNewId(xml) { let id; do { id = (Math.floor(Math.random() * 0x6FFFFFFF) + 0x10000000).toString(16).toUpperCase(); } while (xml && xml.includes(`"${id}"`)); return id; }
// Copies the table row holding paraId n times, with new paragraph ids; returns the ids of each copy's cells
function ivCloneRow(xml, paraId, n) {
  const at = xml.indexOf(`w14:paraId="${paraId}"`);
  const start = xml.lastIndexOf('<w:tr ', at), end = xml.indexOf('</w:tr>', at) + 7;
  if (at < 0 || start < 0) return { xml, rows: [] };
  const row = xml.slice(start, end);
  let add = ''; const rows = [];
  for (let k = 0; k < n; k++) {
    const ids = [];
    add += row.replace(/w14:paraId="[0-9A-F]{8}"/g, () => { const id = ivNewId(xml + add); ids.push(id); return `w14:paraId="${id}"`; })
      .replace(/w14:textId="[0-9A-F]{8}"/g, () => `w14:textId="${ivNewId(xml + add)}"`);
    rows.push(ids.slice(1));
  }
  return { xml: xml.slice(0, end) + add + xml.slice(end), rows };
}
function ivFillActions(xml, idRows, actions, target) {
  const list = actions.length ? actions : ['n/a'];
  let rows = idRows.slice();
  if (list.length > rows.length) { const r = ivCloneRow(xml, rows[rows.length - 1][0], list.length - rows.length); xml = r.xml; rows = rows.concat(r.rows); }
  list.forEach((a, i) => {
    xml = fillParagraph(xml, rows[i][0], a, 18);
    if (a !== 'n/a' && target) xml = fillParagraph(xml, rows[i][1], target, 18);
  });
  return xml;
}
async function ivTemplate(kind) {
  const b64 = (window.BTEC_IV_TEMPLATES || {})[kind];
  if (!b64) throw new Error('The IV form is missing from this copy of the app.');
  const zip = await JSZip.loadAsync(Uint8Array.from(atob(b64), c => c.charCodeAt(0)));
  return { zip, xml: await zip.file('word/document.xml').async('string') };
}
const ivSafeName = s => String(s || '').replace(/[\\/:*?"<>|]+/g, '').replace(/\s+/g, ' ').trim().slice(0, 120);

function ivAwardedText(s) {
  const rows = s.rows;
  const met = rows.filter(r => r.asr === 'Y').map(r => r.code), notMet = rows.filter(r => r.asr !== 'Y').map(r => r.code);
  const tlm = rows.every(r => /^\d+\.\d+$/.test(splitUnit(r.code).code));
  const lines = [];
  if (tlm) lines.push(`Achieved: ${met.join(', ') || 'none'}`);
  else {
    const by = b => rows.filter(r => r.asr === 'Y' && bandOf(r.code) === b).map(r => r.code);
    if (rows.some(r => bandOf(r.code) === 'L1')) lines.push(`Level 1: ${by('L1').join(', ') || 'none'}`);
    lines.push(`Pass: ${by('P').join(', ') || 'none'}`);
    if (rows.some(r => bandOf(r.code) === 'M')) lines.push(`Merit: ${by('M').join(', ') || 'none'}`);
    if (rows.some(r => bandOf(r.code) === 'D')) lines.push(`Distinction: ${by('D').join(', ') || 'none'}`);
  }
  if (notMet.length) lines.push(`Not awarded: ${notMet.join(', ')}`);
  if (s.asrGrade) lines.push(`Grade awarded: ${s.asrGrade.label}`);
  return lines.join('\n');
}
async function ivBuildRecord() {
  const s = ivStats();
  if (!s.complete) throw new Error("Enter the assessor's decision and make your own for every criterion first.");
  const h = getHeaderData(), cl = ivChecklist(), name = ivName();
  let { zip, xml } = await ivTemplate('decisions');
  const F = (id, text, size = 18, bold) => { xml = fillParagraph(xml, id, text, size, bold); };
  F(IV_DEC_IDS.programme, h.programme_title); F(IV_DEC_IDS.assessor, h.assessor_name); F(IV_DEC_IDS.iv, name);
  F(IV_DEC_IDS.unit, h.unit_title); F(IV_DEC_IDS.assignment, h.assignment_title); F(IV_DEC_IDS.learner, ivLearnerLabel());
  F(IV_DEC_IDS.awarded, ivAwardedText(s));
  Object.entries(IV_DEC_IDS.q).forEach(([k, id]) => F(id, cl[k] || '', 18, true));
  if (cl.overall === 'Y') F(IV_DEC_IDS.overall_yes, 'X', 20, true); else if (cl.overall === 'N') F(IV_DEC_IDS.overall_no, 'X', 20, true);
  const actions = ivActionList(), target = (iv.target || (actions.length ? ivAddDays(14) : '')).trim();
  xml = ivFillActions(xml, IV_DEC_IDS.actions, actions, target);
  F(IV_DEC_IDS.comments, noDashes((lastFeedback || ivComposeComments()).trim()));
  if (cl.overall === 'Y' && name) { F(IV_DEC_IDS.iv_sig, name); F(IV_DEC_IDS.iv_sig_date, todayUk()); }
  zip.file('word/document.xml', xml);
  const blob = await zip.generateAsync({ type: 'blob', compression: 'DEFLATE', mimeType: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' });
  const who = learnerMode === 'anon' ? h.student_reg : [h.student_reg, h.student_name].filter(Boolean).join(' ');
  return { blob, name: ivSafeName(['IV record', who, h.assignment_title].filter(Boolean).join(' - ')) + '.docx' };
}
async function ivDownloadRecord() {
  try { const { blob, name } = await ivBuildRecord(); saveBlob(blob, name); logAgreement(); }
  catch (err) { toast(err.message, 'error'); }
}

// ═════════════════════════ Hooks into the main app ═════════════════════════
const _ivRefreshRow = refreshRow;
refreshRow = function (row) { _ivRefreshRow(row); ivDecorateRow(row); };

const _ivUpdateGrade = updateGrade;
updateGrade = function () {
  _ivUpdateGrade();
  const el = SMDOC.getElementById('ivAsrGrade');
  if (el) { const s = ivStats(); el.textContent = s.asrGrade ? s.asrGrade.label : 'Not entered'; el.className = 'gb-v small'; }
  if (ivMode) ivRenderSide();
};

const _ivComposeFeedback = composeFeedback;
composeFeedback = function (force) {
  if (!ivMode) return _ivComposeFeedback(force);
  const box = SMDOC.getElementById('feedbackBox');
  if (!box || (feedbackEdited && !force)) return;
  const text = ivComposeComments();
  box.innerText = text; lastFeedback = text; showRebuild();
};
const _ivShowRebuild = showRebuild;
showRebuild = function () {
  if (!ivMode) return _ivShowRebuild();
  const b = SMDOC.getElementById('rebuildFb'); if (b) b.style.display = feedbackEdited ? '' : 'none';
};
const _ivComposeStaffNote = composeStaffNote;
composeStaffNote = function (force) { if (!ivMode) return _ivComposeStaffNote(force); };

const _ivRenderAiStatus = renderAiStatus;
renderAiStatus = function () { _ivRenderAiStatus(); ivRenderAssessorCard(); };

const _ivStepProblem = stepProblem;
stepProblem = function (n) {
  if (ivMode && n === 4 && fileBase64) {
    if (iv.reviewState === 'running' && iv.source === 'turnitin') return "Reading the assessor's marking. It takes a few seconds.";
    const missing = ivTargets().filter(t => !ivCall(t.code)).map(t => t.code);
    if (missing.length) return `Enter the assessor's decision for ${joinList(missing)}.`;
  }
  return _ivStepProblem(n);
};

const _ivRenderLearnerStrip = renderLearnerStrip;
renderLearnerStrip = function () {
  _ivRenderLearnerStrip();
  const pre = SMDOC.querySelector('#fbTitle .fb-pre'), fn = SMDOC.getElementById('fbName');
  if (pre) pre.textContent = ivMode ? 'Comments for' : 'Feedback to';
  if (ivMode && fn) fn.textContent = ivFirst(SMDOC.getElementById('assessor_name').value) || 'the assessor';
  const tag = SMDOC.querySelector('#learnerStrip .tag'); if (ivMode && tag) tag.textContent = 'IV sample';
};

const _ivUpdateContext = updateContext;
updateContext = function () {
  _ivUpdateContext();
  if (!ivMode) return;
  const c1 = SMDOC.querySelector('#ctxLine .c1');
  if (c1) c1.textContent = 'IV sampling: ' + c1.textContent.replace(', marking ', ', ');
};

// In IV mode a Turnitin download is split automatically, and the marking read on its own
const _ivOnFileReady = onFileReady;
onFileReady = function () {
  if (ivMode) { iv = newIvState(); ivRenderAssessorCard(); }
  _ivOnFileReady();
  if (ivMode) ivAfterUpload();
};

// Assessor's and IV's names are hidden from the AI too
const _ivBuildPrivacyTerms = buildPrivacyTerms;
buildPrivacyTerms = function (ctx) {
  if (!ivMode || ctx) return _ivBuildPrivacyTerms(ctx);
  const el = SMDOC.getElementById('hide_extra'); const keep = el.value;
  el.value = [keep, SMDOC.getElementById('assessor_name').value.trim(), ivName()].filter(Boolean).join(', ');
  try { return _ivBuildPrivacyTerms(); } finally { el.value = keep; }
};

const _ivRenderExportSummary = renderExportSummary;
renderExportSummary = function () {
  if (!ivMode) return _ivRenderExportSummary();
  const h = getHeaderData(), s = ivStats(), cl = ivChecklist();
  const word = v => v ? IV_CALL_WORDS[v] || v : 'Not entered';
  const vWord = { agree: 'Agree', over: 'Over-awarded', under: 'Under-awarded' };
  const actions = ivActionList();
  SMDOC.getElementById('exportSummary').innerHTML = `
    <div class="step-title">IV sample ready</div>
    <div class="sum-grid">
      <div><span class="k">Learner</span><br>${escapeHtml(ivLearnerLabel())}</div>
      <div><span class="k">Unit</span><br>${escapeHtml(h.unit_title || '')}</div>
      <div><span class="k">Assignment</span><br>${escapeHtml(h.assignment_title || 'Not entered')}</div>
      <div><span class="k">Assessor</span><br>${escapeHtml(h.assessor_name || 'Not entered')}</div>
      <div><span class="k">Internal verifier</span><br>${escapeHtml(ivName() || 'Not entered')}</div>
      <div><span class="k">Assessor's grade</span><br>${s.asrGrade ? `<span class="badge rounded-pill grade-pill g-${s.asrGrade.grade}">${s.asrGrade.label}</span>` : 'Not entered'}</div>
      <div><span class="k">Your view</span><br>${s.yourGrade ? `<span class="badge rounded-pill grade-pill g-${s.yourGrade.grade}">${s.yourGrade.label}</span>` : 'Not decided'}</div>
      <div><span class="k">Decisions accurate?</span><br>${cl.overall === 'Y' ? 'Yes' : cl.overall === 'N' ? '<span style="color:var(--no)">No, action needed</span>' : 'Not answered'}</div>
    </div>
    <table class="table table-sm result-table" style="margin-bottom:12px">
      <thead><tr><th style="width:110px">Criteria</th><th style="width:130px">Assessor</th><th style="width:110px">You</th><th>Verdict</th></tr></thead>
      <tbody>${s.rows.map(r => `<tr><td><span class="code-tag">${escapeHtml(r.code)}</span></td><td>${word(r.asr)}</td><td>${r.you ? (r.you === 'Y' ? 'Achieved' : 'Not achieved') : 'Not decided'}</td><td><span class="verdict v-${r.verdict || 'none'}">${vWord[r.verdict] || 'Not compared'}</span></td></tr>`).join('')}</tbody>
    </table>
    <div class="label">IV checklist</div>
    <ul class="iv-sum-list">${IV_QUESTIONS.map(([k, q]) => `<li><span>${escapeHtml(q)}</span><b class="${cl[k] === 'N' ? 'no' : ''}">${cl[k] || 'Not answered'}</b></li>`).join('')}</ul>
    <div class="label" style="margin-top:12px">Action required</div>
    <div class="staff-box">${actions.length ? actions.map(a => escapeHtml(a)).join('<br>') + (iv.target ? `<br><span class="helper">Target date ${escapeHtml(iv.target)}</span>` : '') : 'n/a'}</div>
    <div class="label" style="margin-top:12px">Internal verifier comments</div>
    <div class="feedback-box" style="margin-bottom:6px;min-height:0">${escapeHtml(lastFeedback || '') || '<span class="helper">No comments written.</span>'}</div>
    ${cl.overall === 'Y' ? (ivName() ? `<p class="helper">Your name and today's date go in as the internal verifier's signature. The assessor signs when they've read it.</p>` : `<p class="helper">Add your name in step 2 so it goes on the record as the internal verifier.</p>`) : `<p class="helper">The signatures are left blank until the actions are done, as the form asks.</p>`}`;
};
const _ivUpdateDownloadOptions = updateDownloadOptions;
updateDownloadOptions = function () { if (!ivMode) return _ivUpdateDownloadOptions(); const n = SMDOC.getElementById('dlNote'); if (n) n.textContent = "The college's Internal Verification of Assessment Decisions form, filled in from your decisions."; };

const _ivComposeEmail = composeEmail;
composeEmail = function (force) {
  if (!ivMode) return _ivComposeEmail(force);
  if (emailEdited && !force) return;
  const h = getHeaderData(), s = ivStats(), actions = ivActionList();
  SMDOC.getElementById('emailSubject').value = noDashes(`IV sample: ${h.assignment_title || 'assignment'}${h.unit_title ? ', ' + h.unit_title.split(':')[0] : ''}`);
  const lines = [`Hi ${ivFirst(h.assessor_name) || 'there'},`, '', `I've internally verified ${learnerMode === 'anon' ? (h.student_reg || 'a learner') : (h.student_name || 'a learner')}'s work for ${h.assignment_title || 'this assignment'}${h.unit_title ? ` (${h.unit_title})` : ''}.`, ''];
  lines.push((lastFeedback || ivComposeComments()).trim());
  lines.push('', actions.length ? `Action needed${iv.target ? ` by ${iv.target}` : ''}:\n${actions.map(a => '- ' + a).join('\n')}` : 'No action needed.');
  lines.push('', 'The IV record is attached. Please read it and add your signature' + (actions.length ? ' once the actions are done.' : '.'), '', 'Thanks,', ivName());
  SMDOC.getElementById('emailBody').value = noDashes(lines.join('\n')).replace(/\n{3,}/g, '\n\n').trim();
};
const _ivCreateEmailDraft = createEmailDraft;
createEmailDraft = async function () {
  if (!ivMode) return _ivCreateEmailDraft();
  const e = emailParts();
  if (!e.body) { toast('The message is empty.', 'error'); return; }
  return withBusy('emlBtn', 'Preparing email...', async () => {
    const r = await ivBuildRecord();
    const mixed = 'mixed_' + Math.random().toString(36).slice(2), alt = 'alt_' + Math.random().toString(36).slice(2), fn = mimeWord(r.name);
    const out = ['MIME-Version: 1.0', 'X-Unsent: 1', `To: ${e.to}`, `Subject: ${mimeWord(e.subject)}`, `Content-Type: multipart/mixed; boundary="${mixed}"`, '', `--${mixed}`,
      `Content-Type: multipart/alternative; boundary="${alt}"`, '', `--${alt}`, 'Content-Type: text/plain; charset="utf-8"', 'Content-Transfer-Encoding: base64', '', b64Wrap(utf8b64(e.body)),
      `--${alt}`, 'Content-Type: text/html; charset="utf-8"', 'Content-Transfer-Encoding: base64', '', b64Wrap(utf8b64(bodyHtml(e.body))), `--${alt}--`, '',
      `--${mixed}`, `Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document; name="${fn}"`, 'Content-Transfer-Encoding: base64', `Content-Disposition: attachment; filename="${fn}"`, '', b64Wrap(await blobToB64(r.blob)),
      `--${mixed}--`, ''];
    saveBlob(new Blob([out.join('\r\n')], { type: 'message/rfc822' }), r.name.replace(/\.docx$/, '') + ' - email.eml');
    logAgreement();
  });
};

const _ivNextLearner = nextLearner;
nextLearner = function () { iv = newIvState(); _ivNextLearner(); ivRenderAssessorCard(); ivRenderSide(); };

const _ivDraftSnapshot = draftSnapshot;
draftSnapshot = function () { const d = _ivDraftSnapshot(); d.iv = { mode: ivMode, state: cloneSafe(iv), name: ivName() }; return d; };
const _ivResumeDraft = resumeDraft;
resumeDraft = function (quiet) {
  const d = pendingDraft, x = d && d.iv;
  if (x) { ivMode = !!x.mode; SMROOT.classList.toggle('iv-on', ivMode); iv = Object.assign(newIvState(), x.state || {}); if (x.name) SMDOC.getElementById('iv_name').value = x.name; }
  _ivResumeDraft(quiet);
  if (x) { setIvMode(ivMode); refreshDesk(); }
};

// After the blind check: the comments and checklist pick up the second marker's reasons
const _ivRunCheck = runCheck;
runCheck = async function () { await _ivRunCheck.apply(this, arguments); if (ivMode) { ivRenderSide(); if (!feedbackEdited) composeFeedback(true); } };

// ═════════════════════════ Brief IV ═════════════════════════
const BRIEF_IV_KEY = 'btec_brief_iv';
let briefIv = null;
function briefIvKey() {
  const b = currentBuiltinBrief();
  if (b) return b.id;
  const t = briefPasted() || (brief && brief.name) || '';
  if (!t) return '';
  let h = 0; for (let i = 0; i < t.length; i++) h = (h * 31 + t.charCodeAt(i)) | 0;
  return 'custom:' + (h >>> 0).toString(36);
}
function briefIvAll() { try { return JSON.parse(storeGet(BRIEF_IV_KEY) || '{}') || {}; } catch (e) { return {}; } }
function briefIvSave() { if (!briefIv) return; const all = briefIvAll(); all[briefIv.key] = briefIv; storeSet(BRIEF_IV_KEY, JSON.stringify(all)); }

// Learning aims or outcomes, as the brief states them
function briefAims(text) {
  const t = String(text || '');
  const lo = [...t.matchAll(/^Learning outcome (\d) \((\w+)\)/gim)].map(m => `LO${m[1]} ${m[2]}`);
  if (lo.length) return lo.join(', ');
  const m = t.match(/Learning Aims?:\s*([\s\S]*?)(?:\n(?:Assignment Title|Hand Out|Vocational|Unit No)|\n\n)/i);
  if (m) return m[1].split('\n').map(s => s.trim()).filter(Boolean).join('\n');
  const aims = [...new Set(ivTargets().map(c => (splitUnit(c.code).code.match(/^([A-Z]{1,3})\./) || [])[1]).filter(Boolean))];
  return aims.length ? `Learning aim${aims.length > 1 ? 's' : ''} ${joinList(aims)}` : '';
}
function briefIvPrefill(key) {
  const b = currentBuiltinBrief(), h = getHeaderData();
  const st = { key, edited: {}, answers: {}, fit: '', actions: '', target: '', comments: '', ai: '' };
  st.programme = h.programme_title || (b && b.qualification) || '';
  st.unit = h.unit_title || '';
  st.assignment = b ? `${b.assignment}: ${b.fullTitle || b.title}` : (h.assignment_title || '');
  st.assessor = h.assessor_name || '';
  st.iv = ivName() || storeGet('btec_iv_name') || '';
  st.aims = briefAims(b ? b.text : briefPasted());
  st.authorised = 'No. This is a centre-devised assignment brief, not one published by Pearson.';
  BRIEF_QUESTIONS.forEach(([k]) => st.answers[k] = { a: b ? 'Y' : '', note: '' });
  const notes = (b && b.ivNotes) || [];
  const major = notes.filter(n => !n.minor), minor = notes.filter(n => n.minor);
  major.forEach(n => { const a = st.answers[n.q]; a.a = 'N'; a.note = (a.note ? a.note + ' ' : '') + n.text; });
  if (b) {
    const ho = parseUkDate(b.handout), dl = parseUkDate(b.deadline);
    if (!ho || !dl) { const a = st.answers.deadlines; if (a.a !== 'N') { a.a = 'N'; a.note = 'No hand-out or deadline date on the brief.'; } st.answers.timeframe = { a: 'N', note: 'No dates to judge the time frame by.' }; }
    else {
      const weeks = Math.round((dl - ho) / (7 * 864e5));
      st.answers.timeframe = weeks < 2 ? { a: 'N', note: `Only ${plural(Math.max(0, weeks), 'week')} between hand-out and deadline.` } : { a: 'Y', note: `${weeks} weeks, ${b.handout} to ${b.deadline}.` };
      if (st.answers.deadlines.a === 'Y') st.answers.deadlines.note = `Hand-out ${b.handout}, deadline ${b.deadline}.`;
    }
  }
  st.actions = major.map(n => n.text).join('\n');
  if (st.answers.deadlines.a === 'N' && !major.some(n => n.q === 'deadlines')) st.actions = [st.actions, 'Add the hand-out date and deadline to the brief.'].filter(Boolean).join('\n');
  st.comments = minor.length ? 'Small things to tidy when the brief is next updated:\n' + minor.map(n => '- ' + n.text).join('\n') : '';
  if (b && !notes.length) st.comments = 'Checked against the specification. No problems found.';
  st.target = st.actions ? ivAddDays(14) : '';
  return st;
}
function briefIvFit(st) { return st.edited.fit ? st.fit : (BRIEF_QUESTIONS.some(([k]) => st.answers[k].a === 'N') ? 'N' : BRIEF_QUESTIONS.every(([k]) => st.answers[k].a === 'Y') ? 'Y' : ''); }

function openBriefIv() {
  const key = briefIvKey();
  if (!key) { toast('Choose a built-in brief, or add or paste the brief, first.', 'info'); return; }
  const saved = briefIvAll()[key];
  briefIv = saved && saved.answers ? saved : briefIvPrefill(key);
  if (!briefIv.iv) briefIv.iv = ivName() || storeGet('btec_iv_name') || '';
  if (!briefIv.assessor) briefIv.assessor = getHeaderData().assessor_name || '';
  renderBriefIv();
  const d = SMDOC.getElementById('briefIvDialog');
  if (d && !d.open) d.showModal();
}
function closeBriefIv() { const d = SMDOC.getElementById('briefIvDialog'); if (d && d.open) d.close(); }
function renderBriefIv() {
  const st = briefIv, el = SMDOC.getElementById('briefIvBody');
  if (!st || !el) return;
  const b = currentBuiltinBrief();
  const fit = briefIvFit(st);
  const field = (k, label, rows) => `<div class="field-group"><label class="field-label" for="biv_${k}">${label}</label>${rows ? `<textarea class="form-control field-input" id="biv_${k}" data-k="${k}" rows="${rows}"></textarea>` : `<input class="form-control field-input" id="biv_${k}" data-k="${k}" type="text">`}</div>`;
  el.innerHTML = `<h2 id="briefIvTitle">Internal verification of this brief</h2>
    <p class="helper">${b ? (b.ivNotes && b.ivNotes.length ? `Filled in from the brief check: ${plural(b.ivNotes.filter(n => !n.minor).length, 'thing')} to fix and ${plural(b.ivNotes.filter(n => n.minor).length, 'small tidy-up')}.` : 'Filled in from the brief check, which found nothing to fix.') + ' Ask the second marker to check it again if the brief has changed.' : 'Ask the second marker to check the brief against the unit, then go through each answer.'}</p>
    <div class="biv-grid">${field('programme', 'Programme title')}${field('unit', 'Unit number and title')}${field('assignment', 'Assignment title')}${field('assessor', 'Assessor')}${field('iv', 'Internal verifier')}</div>
    ${field('aims', 'Learning aims or outcomes targeted', 2)}
    ${field('authorised', 'Pearson authorised brief? Amended?', 2)}
    <div class="biv-check">${BRIEF_QUESTIONS.map(([k, q]) => { const a = st.answers[k]; return `<div class="biv-row" data-q="${k}"><span class="ivc-q">${escapeHtml(q)}</span><span class="ivc-yn" role="radiogroup" aria-label="${escapeHtml(q)}">${['Y', 'N'].map(o => `<button type="button" class="ivc-btn o-${o}${a.a === o ? ' on' : ''}" role="radio" aria-checked="${a.a === o}" data-v="${o}">${o}</button>`).join('')}</span><input class="form-control field-input biv-note" type="text" aria-label="Note for: ${escapeHtml(q)}" placeholder="Note (optional)"></div>`; }).join('')}
      <div class="biv-row ivc-overall" data-q="fit"><span class="ivc-q">Overall, is the assignment fit for purpose?</span><span class="ivc-yn" role="radiogroup" aria-label="Fit for purpose">${['Y', 'N'].map(o => `<button type="button" class="ivc-btn o-${o}${fit === o ? ' on' : ''}" role="radio" aria-checked="${fit === o}" data-v="${o}">${o === 'Y' ? 'Yes' : 'No'}</button>`).join('')}</span><span></span></div>
    </div>
    <div class="biv-actions">${field('actions', 'Action required (one per line, or n/a)', 4)}${field('target', 'Target date')}</div>
    ${field('comments', 'General comments', 3)}
    <p class="helper" id="bivStatus">${st.ai === 'running' ? 'The second marker is checking the brief...' : st.ai && st.ai !== 'done' ? escapeHtml(st.ai) : st.ai === 'done' ? 'Checked by the second marker. Go through each answer before you download.' : ''}</p>
    <div class="dlg-foot biv-foot">
      <button class="btn btn-outline-secondary btn-run" data-sm-click="briefIvCheck()"${st.ai === 'running' ? ' disabled' : ''}>Check it with the second marker</button>
      <button class="btn btn-primary btn-export" data-sm-click="briefIvDownload()">Download IV form</button>
      <button class="btn btn-link linkish" data-sm-click="closeBriefIv()">Close</button>
    </div>`;
  el.querySelectorAll('[data-k]').forEach(inp => {
    inp.value = st[inp.dataset.k] || '';
    inp.oninput = () => { st[inp.dataset.k] = inp.value; st.edited[inp.dataset.k] = true; if (inp.dataset.k === 'iv') storeSet('btec_iv_name', inp.value); briefIvSave(); };
  });
  el.querySelectorAll('.biv-row').forEach(r => {
    const k = r.dataset.q, note = r.querySelector('.biv-note');
    if (note) { note.value = st.answers[k].note || ''; note.oninput = () => { st.answers[k].note = note.value; briefIvSave(); }; }
    r.querySelectorAll('.ivc-btn').forEach(btn => btn.onclick = () => {
      if (k === 'fit') { st.fit = btn.dataset.v; st.edited.fit = true; }
      else { st.answers[k].a = btn.dataset.v; st.edited['q_' + k] = true; }
      briefIvSave(); renderBriefIv();
    });
  });
}

const BRIEF_IV_SYSTEM = `You are an experienced internal verifier for Pearson BTEC and TLM qualifications in a further education college. You are checking an assignment brief before it is issued, using the college's Internal Verification of Assignment Brief checklist. Compare the brief with the unit's criteria as given (the specification wording) and judge it as a careful IV would.

Return ONLY a JSON object, no markdown, no backticks:
{
  "answers": {
    "details": {"answer": "Y" | "N", "note": "one short sentence"},
    "deadlines": {...}, "timeframe": {...}, "scenario": {...}, "aims": {...}, "criteria": {...}, "requirements": {...}, "evidence": {...}, "valid": {...}
  },
  "actions": ["each change the assessor must make before issuing the brief, one plain instruction each"],
  "comments": "small things worth tidying that don't stop the brief being used, or an empty string",
  "aims": "the learning aims or outcomes the brief targets, as the brief states them"
}
The questions:
- details: are the programme and unit details accurate (qualification name, unit number and title)?
- deadlines: are clear hand-out and deadline dates given?
- timeframe: is the time between hand-out and deadline appropriate for the work?
- scenario: is there a suitable vocational scenario or context?
- aims: are the learning aims or outcomes stated accurately?
- criteria: does each task show which criteria it addresses, with the right codes?
- requirements: do the tasks meet the unit's assessment requirements, with criteria worded as in the specification, so learners can reach every targeted grade?
- evidence: is it clear what evidence the learner needs to produce?
- valid: is it likely to generate evidence that is valid and sufficient?
Answer N whenever there is a real problem, and say what it is. A criterion worded differently from the specification in a way that changes what is assessed is a requirements problem. Never use em dashes or en dashes.`;

async function briefIvCheck() {
  const st = briefIv; if (!st) return;
  if (!getApiKey()) { st.ai = 'Add your API key in Settings to use the second marker.'; renderBriefIv(); return; }
  st.ai = 'running'; renderBriefIv();
  try {
    const blocks = await briefBlocks();
    if (!blocks.length) throw new Error('No brief to check.');
    const b = currentBuiltinBrief();
    const crit = pick.map(p => `- ${p.unit && pick.some(q => q.unit !== p.unit) ? p.unit + ' ' : ''}${p.code}: ${p.desc}${p.on ? '' : ' (not targeted by this brief)'}`).join('\n');
    const facts = [
      `Programme: ${st.programme || 'not given'}`, `Unit: ${st.unit || 'not given'}`,
      b ? `Hand-out date: ${b.handout || 'not given'}. Deadline: ${b.deadline || 'not given'}.` : '',
      b && b.ivNotes && b.ivNotes.length ? `An earlier check found these issues; include any that still apply:\n${b.ivNotes.map(n => '- ' + n.text).join('\n')}` : '',
    ].filter(Boolean).join('\n');
    const p = await ivAsk(BRIEF_IV_SYSTEM, [...blocks, { type: 'text', text: `${facts}\n\nThe unit's criteria (specification wording):\n${crit}\n\nCheck the brief and return the JSON.` }], 3000);
    const ans = p.answers || {};
    BRIEF_QUESTIONS.forEach(([k]) => {
      const a = ans[k]; if (!a || st.edited['q_' + k]) return;
      const cur = st.answers[k];
      const yn = /^[YN]$/.test(a.answer) ? a.answer : cur.a;
      if (cur.a === 'N' && yn === 'Y') return;   // a known problem stays a problem
      st.answers[k] = { a: yn, note: noDashes(a.note || cur.note || '') };
    });
    if (!st.edited.actions) {
      const have = st.actions.split('\n').map(s => s.trim()).filter(Boolean);
      (p.actions || []).map(noDashes).forEach(a => { if (!have.some(h => h.toLowerCase() === a.toLowerCase())) have.push(a); });
      st.actions = have.join('\n');
      if (st.actions && !st.target) st.target = ivAddDays(14);
    }
    if (!st.edited.comments && p.comments && !st.comments.includes(p.comments)) st.comments = [st.comments, noDashes(p.comments)].filter(Boolean).join('\n\n');
    if (!st.edited.aims && p.aims && !st.aims) st.aims = noDashes(p.aims);
    st.ai = 'done';
  } catch (err) { st.ai = "The check didn't complete: " + err.message; }
  briefIvSave(); renderBriefIv();
}

async function briefIvDownload() {
  const st = briefIv; if (!st) return;
  try {
    let { zip, xml } = await ivTemplate('brief');
    const F = (id, text, size = 18, bold) => { xml = fillParagraph(xml, id, text, size, bold); };
    F(IV_BRIEF_IDS.programme, st.programme); F(IV_BRIEF_IDS.assessor, st.assessor); F(IV_BRIEF_IDS.iv, st.iv);
    F(IV_BRIEF_IDS.unit, st.unit); F(IV_BRIEF_IDS.assignment, st.assignment); F(IV_BRIEF_IDS.aims, st.aims); F(IV_BRIEF_IDS.authorised, st.authorised);
    Object.entries(IV_BRIEF_IDS.q).forEach(([k, id]) => F(id, st.answers[k] ? st.answers[k].a : '', 18, true));
    const fit = briefIvFit(st);
    if (fit === 'Y') F(IV_BRIEF_IDS.overall_yes, 'X', 20, true); else if (fit === 'N') F(IV_BRIEF_IDS.overall_no, 'X', 20, true);
    const actions = (st.actions || '').split('\n').map(s => s.trim()).filter(s => s && !/^n\/?a$/i.test(s));
    xml = ivFillActions(xml, IV_BRIEF_IDS.actions, actions, st.target);
    const notes = BRIEF_QUESTIONS.filter(([k]) => st.answers[k] && st.answers[k].note && st.answers[k].a === 'Y').map(([k, q]) => `${q.replace(/\?$/, '')}: ${st.answers[k].note}`);
    F(IV_BRIEF_IDS.comments, noDashes([st.comments, notes.length ? 'Notes on the checklist:\n' + notes.map(n => '- ' + n).join('\n') : ''].filter(Boolean).join('\n\n')));
    if (fit === 'Y' && st.iv) { F(IV_BRIEF_IDS.iv_sig, st.iv); F(IV_BRIEF_IDS.iv_sig_date, todayUk()); }
    zip.file('word/document.xml', xml);
    const blob = await zip.generateAsync({ type: 'blob', compression: 'DEFLATE', mimeType: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' });
    saveBlob(blob, ivSafeName(`IV of assignment brief - ${st.assignment || 'brief'}`) + '.docx');
  } catch (err) { toast(err.message, 'error'); }
}

// ── start-up ──
(function ivInit() {
  const n = storeGet('btec_iv_name'); const el = SMDOC.getElementById('iv_name'); if (el && n && !el.value) el.value = n;
  if (storeGet('btec_iv_mode') === '1') setIvMode(true); else ivLabels();
})();

/* ===== quickmarks.js ===== */
// ─────────────────────────────────────────────────────────────
// QuickMarks: your own bank of stamps for the work.
//
// Select text in the work and click a stamp (or press 1 to 9) to add it as a
// comment there. With nothing selected it's added as a general note. "{code}"
// in a stamp becomes the criterion you're on. The set is kept in this browser
// and can be downloaded to share with colleagues.
//
// Loaded after the main script.
// ─────────────────────────────────────────────────────────────

const QM_KEY = 'btec_quickmarks';
const QM_DEFAULTS = [
  { title: 'Well done', text: 'Well done.', type: 'met' },
  { title: 'Good', text: 'Good point.', type: 'met' },
  { title: 'Toward', text: 'Evidence toward {code}.', type: 'note', link: true },
  { title: 'Part achieved', text: 'Part achieved: {code} is touched on here but not fully met yet.', type: 'not_met', link: true },
  { title: 'Like what?', text: 'Like what? Give an example.', type: 'note' },
  { title: 'Source?', text: 'Can you back this up with a source?', type: 'note' },
  { title: 'Explain more', text: 'Explain this further: how, or why?', type: 'note' },
  { title: 'Accurate?', text: 'Is this accurate? Check it against a reliable source.', type: 'note' },
  { title: 'Missing CAP', text: 'Missing capital letter(s).', type: 'note' },
  { title: 'Incorrect use of CAP', text: "This doesn't need a capital letter.", type: 'note' },
  { title: 'Incorrect spelling', text: 'Incorrect spelling.', type: 'note' },
  { title: 'American sp', text: 'American spelling. Use the UK spelling.', type: 'note' },
  { title: 'Grammar error', text: 'Grammar error.', type: 'note' },
  { title: 'Incomplete sentence', text: 'Incomplete sentence.', type: 'note' },
  { title: 'Missing "."', text: 'Missing full stop.', type: 'note' },
];
const QM_TYPES = { note: 'Note', met: 'Met', not_met: 'Not yet' };

function quickMarks() {
  try {
    const v = JSON.parse(storeGet(QM_KEY) || 'null');
    if (v && Array.isArray(v.marks)) return v.marks.filter(m => m && (m.title || m.text));
  } catch (e) {}
  return QM_DEFAULTS.map(m => ({ ...m }));
}
function saveQuickMarks(marks) { storeSet(QM_KEY, JSON.stringify({ v: 1, marks })); renderQmBar(); }
const qmLinked = m => !!m.link || /\{code\}/.test(m.text || '');

// Which PDF page is in view, for a comment with no quote
function qmPdfPage() {
  if (work.kind !== 'pdf') return null;
  let best = null, d = Infinity;
  SMDOC.querySelectorAll('#workPaper .pdf-page').forEach(pg => { const r = pg.getBoundingClientRect(), dd = Math.abs((r.top + r.bottom) / 2 - window.innerHeight / 2); if (dd < d) { d = dd; best = pg; } });
  return best ? +best.dataset.page : 1;
}

function applyQuickMark(i, quote) {
  const m = quickMarks()[i];
  if (!m || currentStep !== 5) return;
  const q = String(quote !== undefined ? quote : selectionText() || '').trim().slice(0, 300);
  const row = rowsEl()[currentRowIdx], code = row ? rowCode(row) : '';
  const linked = qmLinked(m);
  const text = noDashes(String(m.text || m.title).replace(/\{code\}/g, code || 'this criterion')).trim();
  const a = { quote: q, code: linked ? code : '', type: QM_TYPES[m.type] ? m.type : 'note', text, page: qmPdfPage(), include: true, manual: true, qm: m.title || '' };
  lastAnnotations.push(a);
  if (__getSel) __getSel().removeAllRanges();
  const pop = SMDOC.getElementById('selPop'); if (pop) pop.classList.remove('show');
  const show = SMDOC.getElementById('showNotes');
  if (show && !show.checked) { show.checked = true; SMDOC.getElementById('workWrap').classList.remove('hide-notes'); }
  renderWork().then(() => {
    const el = SMDOC.querySelector(`#annotationList .ann[data-ann="${lastAnnotations.indexOf(a)}"]`);
    if (el) { el.classList.add('qm-new'); el.scrollIntoView({ block: 'nearest', behavior: 'smooth' }); setTimeout(() => el.classList.remove('qm-new'), 1200); }
  });
  scheduleSave();
}

// The strip of stamps above the work, and the short row in the "Comment on this" pop-up
function qmChip(m, i, small) {
  const key = i < 9 ? `<kbd>${i + 1}</kbd>` : '';
  return `<button type="button" class="qm-chip t-${QM_TYPES[m.type] ? m.type : 'note'}${small ? ' small' : ''}" data-qm="${i}" data-sm-mousedown="event.preventDefault()" title="${escapeHtml(String(m.text || m.title).replace(/\{code\}/g, 'the current criterion'))}">${small ? '' : key}${escapeHtml(m.title || m.text)}</button>`;
}
function renderQmBar() {
  const marks = quickMarks();
  const bar = SMDOC.getElementById('qmBar');
  if (bar) {
    bar.innerHTML = `<span class="qm-label">QuickMarks</span><div class="qm-chips">${marks.map((m, i) => qmChip(m, i)).join('')}</div><button type="button" class="btn btn-link btn-sm linkish qm-edit" data-sm-click="openQuickMarks()">Edit</button>`;
    bar.querySelectorAll('.qm-chip').forEach(b => b.onclick = () => applyQuickMark(+b.dataset.qm));
  }
  const row = SMDOC.getElementById('selPopQm');
  if (row) {
    row.innerHTML = marks.slice(0, 6).map((m, i) => qmChip(m, i, true)).join('');
    row.querySelectorAll('.qm-chip').forEach(b => b.onclick = () => applyQuickMark(+b.dataset.qm, SMDOC.getElementById('selPop')._text));
  }
}

// ── editing the set ──
let qmDraft = null;
function openQuickMarks() {
  qmDraft = quickMarks().map(m => ({ title: m.title || '', text: m.text || '', type: QM_TYPES[m.type] ? m.type : 'note', link: !!m.link }));
  renderQmDialog();
  const d = SMDOC.getElementById('qmDialog'); if (d && !d.open) d.showModal();
}
function closeQuickMarks() { const d = SMDOC.getElementById('qmDialog'); if (d && d.open) d.close(); }
function qmCommit() { saveQuickMarks(qmDraft.filter(m => m.title.trim() || m.text.trim()).map(m => ({ title: m.title.trim(), text: noDashes(m.text.trim()), type: m.type, ...(m.link ? { link: true } : {}) }))); }
function renderQmDialog() {
  const el = SMDOC.getElementById('qmBody'); if (!el) return;
  el.innerHTML = `<h2 id="qmTitle">QuickMarks</h2>
    <p class="helper">Select text in the work and click a stamp, or press its number, to comment there. With nothing selected, it's added as a general note. Write <code>{code}</code> to put in the criterion you're on, for example "Evidence toward {code}". The first nine have number keys.</p>
    <div class="qm-list" role="list">${qmDraft.map((m, i) => `<div class="qm-item" role="listitem" data-i="${i}">
        <span class="qm-n">${i < 9 ? i + 1 : ''}</span>
        <input class="form-control field-input qm-t" type="text" aria-label="Stamp ${i + 1} name" placeholder="Name">
        <input class="form-control field-input qm-x" type="text" aria-label="Stamp ${i + 1} comment" placeholder="Comment the learner sees">
        <select class="form-select field-input qm-ty" aria-label="Stamp ${i + 1} type">${Object.entries(QM_TYPES).map(([k, v]) => `<option value="${k}"${m.type === k ? ' selected' : ''}>${v}</option>`).join('')}</select>
        <span class="qm-move"><button type="button" class="btn btn-sm btn-outline-secondary" aria-label="Move up"${i ? '' : ' disabled'} data-a="up">&uarr;</button><button type="button" class="btn btn-sm btn-outline-secondary" aria-label="Move down"${i < qmDraft.length - 1 ? '' : ' disabled'} data-a="down">&darr;</button><button type="button" class="btn btn-sm btn-outline-secondary" aria-label="Remove" data-a="del">&#215;</button></span>
      </div>`).join('')}</div>
    <div class="qm-tools">
      <button type="button" class="btn btn-sm btn-outline-secondary btn-csv" data-sm-click="qmDraft.push({ title: '', text: '', type: 'note' }); renderQmDialog(); const t = SMDOC.querySelectorAll('#qmBody .qm-t'); t[t.length - 1].focus()">Add a QuickMark</button>
      <button type="button" class="btn btn-link btn-sm linkish" data-sm-click="qmDownload()">Download this set</button>
      <button type="button" class="btn btn-link btn-sm linkish" data-sm-click="SMDOC.getElementById('qmFile').click()">Load a set</button>
      <button type="button" class="btn btn-link btn-sm linkish" data-sm-click="if (confirm('Go back to the starting set? Your own QuickMarks will be replaced.')) { qmDraft = QM_DEFAULTS.map(m => ({ ...m })); qmCommit(); renderQmDialog(); }">Reset to the starting set</button>
    </div>
    <div class="dlg-foot"><button class="btn btn-primary btn-export" data-sm-click="closeQuickMarks()">Done</button></div>`;
  el.querySelectorAll('.qm-item').forEach(row => {
    const i = +row.dataset.i, m = qmDraft[i];
    const t = row.querySelector('.qm-t'), x = row.querySelector('.qm-x'), ty = row.querySelector('.qm-ty');
    t.value = m.title; x.value = m.text;
    t.oninput = () => { m.title = t.value; qmCommit(); };
    x.oninput = () => { m.text = x.value; qmCommit(); };
    x.onblur = () => { x.value = noDashes(x.value); m.text = x.value; qmCommit(); };
    ty.onchange = () => { m.type = ty.value; qmCommit(); };
    row.querySelectorAll('[data-a]').forEach(b => b.onclick = () => {
      if (b.dataset.a === 'del') qmDraft.splice(i, 1);
      else { const j = b.dataset.a === 'up' ? i - 1 : i + 1; [qmDraft[i], qmDraft[j]] = [qmDraft[j], qmDraft[i]]; }
      qmCommit(); renderQmDialog();
    });
  });
}
function qmDownload() {
  saveBlob(new Blob([JSON.stringify({ app: 'second-marker', kind: 'quickmarks', v: 1, marks: quickMarks() }, null, 1)], { type: 'application/json' }), 'QuickMarks.json');
}
async function qmLoad(file) {
  if (!file) return;
  try {
    const v = JSON.parse(await file.text());
    const marks = (Array.isArray(v) ? v : v.marks || []).filter(m => m && (m.title || m.text)).map(m => ({ title: String(m.title || '').slice(0, 60), text: noDashes(String(m.text || '').slice(0, 400)), type: QM_TYPES[m.type] ? m.type : 'note', ...(m.link ? { link: true } : {}) }));
    if (!marks.length) throw new Error('No QuickMarks found in that file.');
    qmDraft = marks; qmCommit(); renderQmDialog();
    toast(`Loaded ${marks.length} QuickMarks.`, 'ok');
  } catch (e) { toast(e instanceof SyntaxError ? "That file isn't a QuickMarks set." : e.message, 'error'); }
}

// Number keys on the desk apply a stamp to the selected text
__on('keydown', e => {
  if (currentStep !== 5 || e.ctrlKey || e.metaKey || e.altKey || !/^[1-9]$/.test(e.key)) return;
  if (SMDOC.querySelector('dialog[open]') || SMDOC.getElementById('settingsCard').classList.contains('show')) return;
  const t = e.target;
  if (t && t.closest && t.closest('input, textarea, select, [contenteditable="true"]')) return;
  if (+e.key > quickMarks().length) return;
  e.preventDefault();
  applyQuickMark(+e.key - 1);
});

// the pop-up and dialog sit at the end of the page, after this script
if (document.readyState === 'loading') __on('DOMContentLoaded', renderQmBar); else renderQmBar();

/* ===== batch.js ===== */
// ─────────────────────────────────────────────────────────────
// Batch marking: a whole class set at once.
//
// Turned on in Settings. Add all the files in step 3: each becomes a learner
// in the class set, named from the file name where possible. The second
// marker checks them in the background, two at a time, while you mark
// whoever is open. Each learner keeps their own saved state, so you can move
// between them freely. Finish with every record in one zip and a grades CSV.
//
// Not used in IV sampling. Loaded after the main script, iv.js and quickmarks.js.
// ─────────────────────────────────────────────────────────────

const BATCH_KEY = 'btec_batch';            // '1' while marking a class set (chosen in step 1)
const BATCH_OPTION_KEY = 'btec_batch_option';   // '0' hides the class set choice (Settings)
const BATCH_WORKERS = 2;
var batch = { items: [], currentId: '', running: false, paused: false, loaded: false };
let batchSaveTimer = null, batchBusy = false;

const batchOffered = () => storeGet(BATCH_OPTION_KEY) !== '0';
const batchOn = () => batchOffered() && storeGet(BATCH_KEY) === '1' && !ivMode;
const batchItem = id => batch.items.find(i => i.id === id);
const batchCurrent = () => batchItem(batch.currentId);
const fieldVal = id => { const el = SMDOC.getElementById(id); return el ? el.value : ''; };
const batchId = () => 'b' + Date.now().toString(36) + Math.random().toString(36).slice(2, 7);

// ── storage: the list in one record, each file in its own ──
function batchSave(now) {
  clearTimeout(batchSaveTimer);
  const go = () => {
    if (!window.indexedDB) return;
    const meta = { v: 1, currentId: batch.currentId, shared: batch.shared || null, items: batch.items.map(i => ({ ...i, checking: undefined })) };
    draftDb('readwrite', st => st.put(meta, 'batch')).catch(() => {});
  };
  if (now) go(); else batchSaveTimer = setTimeout(go, 600);
}
const batchPutFile = (id, b64) => window.indexedDB ? draftDb('readwrite', st => st.put(b64, 'bfile:' + id)) : Promise.resolve();
const batchGetFile = id => window.indexedDB ? draftDb('readonly', st => st.get('bfile:' + id)) : Promise.resolve(null);
const batchDelFile = id => window.indexedDB ? draftDb('readwrite', st => st.delete('bfile:' + id)).catch(() => {}) : Promise.resolve();
const batchMem = {};   // files read this session, so moving between learners is quick
async function batchFile(id) { if (batchMem[id]) return batchMem[id]; const b = await batchGetFile(id); if (b) batchMem[id] = b; return b; }

async function batchLoad() {
  if (!window.indexedDB) { batch.loaded = true; return; }
  try {
    const meta = await draftDb('readonly', st => st.get('batch'));
    if (meta && Array.isArray(meta.items)) {
      batch.items = meta.items.map(i => ({ ...i, status: i.status === 'checking' ? 'waiting' : i.status }));
      batch.currentId = meta.currentId || ''; batch.shared = meta.shared || null;
    }
  } catch (e) {}
  batch.loaded = true;
  renderBatch();
}

// ── names from file names ──
// "Megan Leeson - Unit 1.docx", "Smith_John_U1A1.pdf", "24001114 - U1 A1.pdf", "RM - Unit 1 Assignment 1 - Marked Work.pdf"
function batchGuess(fileName) {
  const base = String(fileName || '').replace(/\.[^.]+$/, '');
  const reg = (base.match(/(?<!\d)\d{8}(?!\d)/) || [''])[0];
  const junk = /^(unit|u\d+\w*|assignment|assign|a\d+|task|t\d+|project|p\d+|part|marked|work|final|draft|v\d+|version|submission|submitted|resub|resubmission|copy|level|l\d|btec|esports|it|cyber|csdf\d*|tlm|aim|learning|brief|report|essay|the|and|of|for|by|my)$/i;
  // the longest run of words that look like a name, broken by anything that doesn't
  const words = (reg ? base.replace(reg, ' ') : base).split(/[\s_\-–—(),+&]+/).filter(Boolean);
  let run = [], best = [];
  for (const w of words) {
    if (/^\p{L}[\p{L}'’.]*$/u.test(w) && !junk.test(w)) { run.push(w); if (run.length > best.length) best = run.slice(); }
    else run = [];
  }
  let name = best.slice(0, 4).join(' ');
  if (/^\p{L}{2,3}$/u.test(name) && name === name.toUpperCase()) return { name, reg };   // initials, e.g. "RM"
  name = name.replace(/\b(\p{L})(\p{L}*)/gu, (m, a, b) => a.toUpperCase() + (b === b.toUpperCase() && b.length > 1 ? b.toLowerCase() : b));
  return { name, reg };
}

// ── adding the class set ──
async function batchAdd(fileList) {
  const files = [...(fileList || [])];
  if (!files.length) return;
  const bad = files.filter(f => !(/\.pdf$/i.test(f.name) || f.type === 'application/pdf' || /\.docx$/i.test(f.name)));
  if (bad.length) toast(`Skipped ${plural(bad.length, 'file')} that ${bad.length === 1 ? "isn't" : "aren't"} PDF or Word (.docx): ${bad.slice(0, 3).map(f => f.name).join(', ')}${bad.length > 3 ? '...' : ''}`, 'error');
  const good = files.filter(f => !bad.includes(f));
  let added = 0;
  for (const file of good) {
    if (batch.items.some(i => i.fileName === file.name && i.size === file.size)) continue;
    const isPdf = /\.pdf$/i.test(file.name) || file.type === 'application/pdf';
    const buf = await file.arrayBuffer();
    const b64 = b64FromBytes(new Uint8Array(buf));
    const g = batchGuess(file.name);
    const item = { id: batchId(), fileName: file.name, size: file.size, b64len: b64.length, mediaType: isPdf ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
      name: g.name, reg: g.reg, anonRef: '', status: 'waiting', added: Date.now() };
    batch.items.push(item); batchMem[item.id] = b64; added++;
    await batchPutFile(item.id, b64).catch(() => {});
    renderBatch();
    // the file's own details and, for PDFs, whether it's a Turnitin download, worked out now
    try { item.integrity = await analyseSubmission(file, buf, ''); } catch (e) { item.integrity = null; }
    if (isPdf) { try { item.scan = await scanPdf(b64); } catch (e) { item.scan = null; } }
    if (item.scan && item.scan.turnitin && item.integrity && !item.integrity.turnitin) item.integrity = forTurnitin(item.integrity);
    batchSave(); renderBatch();
  }
  if (added) {
    toast(`Added ${plural(added, 'learner')} to the class set.`, 'ok');
    if (checkMode() === 'ai' && getApiKey()) batchStart();
  }
}

// ── checking in the background ──
function batchStart() {
  if (!getApiKey()) { toast('Add your API key in Settings first.', 'error'); openKeySettings(); return; }
  if (!pick.some(p => p.on)) { toast('Choose the unit and criteria in step 1 first.', 'error'); return; }
  batch.paused = false;
  batch.items.forEach(i => { if (batchRetryable(i)) { i.status = 'waiting'; i.error = ''; } });   // a check that failed gets another go
  syncRowsFromPick();
  for (let w = 0; w < BATCH_WORKERS; w++) batchWorker();
  renderBatch();
}
// failed for a reason another go might fix (not a Turnitin download, and not opened yet)
const batchRetryable = i => i.status === 'needs' && !i.blocked && !i.snapshot && i.id !== batch.currentId;
const batchToCheck = () => batch.items.filter(i => (i.status === 'waiting' && i.id !== batch.currentId) || batchRetryable(i)).length;
function batchPause() { batch.paused = true; renderBatch(); }
async function batchWorker() {
  batch.running = true;
  while (!batch.paused) {
    const item = batch.items.find(i => i.status === 'waiting' && i.id !== batch.currentId && !i.checking);
    if (!item) break;
    item.checking = true; item.status = 'checking'; renderBatch();
    try { await batchCheck(item); item.status = 'checked'; item.error = ''; }
    catch (err) { item.status = 'needs'; item.error = err.message || String(err); item.blocked = !!err.blocked; }
    item.checking = false;
    if (item.id === batch.currentId) {
      item.status = 'marking';
      if (item.ai && !item.error && ['idle', 'choose', 'running'].includes(aiState)) {
        batchApplyAi(item); renderAiStatus(); refreshNav(); scheduleSave();
        toast("The second marker's check is in.", 'ok');
      } else if (item.error && aiState === 'running') {
        aiError = item.error; aiState = /Turnitin/.test(aiError) ? 'blocked' : 'error'; setProgress('pAi', 'err');
        renderAiStatus(); refreshNav(); scheduleSave();
      }
    }
    batchSave(); renderBatch();
  }
  batch.running = batch.items.some(i => i.checking);
  renderBatch();
}
// The same check as for the open learner, built from this learner's own file and details
async function batchCheck(item) {
  const b64 = await batchFile(item.id);
  if (!b64) throw new Error('The file is missing. Remove this learner and add the file again.');
  const criteria = getCriteria().filter(c => c.code);
  if (!criteria.length) throw new Error('No criteria ticked in step 1.');
  const scan = item.scan || null;
  let scanMode = '';
  if (scan && scan.turnitin) {
    if (!scan.learnerText) { const e = new Error(TURNITIN_MSG); e.blocked = true; throw e; }
    scanMode = 'learner';
  }
  const anon = learnerMode === 'anon';
  const map = buildPrivacyTerms({ name: anon ? '' : item.name, reg: anon ? '' : item.reg, email: '', hideExtra: SMDOC.getElementById('hide_extra').value, integrity: item.integrity, mode: anon ? 'anon' : 'named' });
  const report = { counts: {}, pdf: '', notes: [] };
  const submission = await privateSubmissionBlock({ b64, mediaType: item.mediaType, scan, scanMode, scanAck: false, map, report });
  const refs = await referenceBlocks();
  const briefs = await briefBlocks();
  const user = checkUserPrompt(checkContextBlock({ integrity: item.integrity, allCriteria: criteria }), criteriaPromptText(criteria), briefs.length, refs.length);
  const parsed = restoreFromAi(await sendCheck(getApiKey(), checkSystemPrompt(), [submission, ...briefs, ...refs, { type: 'text', text: redactForAi(user, map, report) }]), map);
  const chk = normaliseCheck(parsed);
  item.ai = { results: chk.results, annotations: chk.annotations, overall: chk.overall, next: chk.next, aiIntegrity: chk.aiIntegrity, assignmentTitle: chk.assignmentTitle,
    runId: Date.now().toString(36) + Math.random().toString(36).slice(2, 6), model: SMDOC.querySelector('input[name="model"]:checked').value, privacy: report, checkedAt: Date.now() };
}

// ── moving between learners ──
// What's open is saved into its learner before another is opened
function batchStoreCurrent() {
  const item = batchCurrent();
  if (!item || !fileBase64) return;
  const snap = draftSnapshot();
  snap.file = null; snap.references = null; snap.brief = null;   // shared by the set, taken from what's open
  item.snapshot = snap;
  const f = snap.fields || {};
  if (learnerMode !== 'anon') { item.name = f.student_name || item.name; item.reg = f.student_reg || item.reg; }
  if (aiState === 'done' && lastResults.length) item.ai = Object.assign({}, item.ai, { results: cloneSafe(lastResults), annotations: cloneSafe(lastAnnotations), overall: aiOverall, next: aiNextSteps });
  const rows = getCriteria().filter(c => c.code);
  const decided = rows.length && rows.every(c => c.achieved);
  const g = decided ? gradeOf(rows, c => c.achieved === 'Y') : null;
  item.grade = g ? g.label : ''; item.gradeCode = g ? g.grade : '';
  item.decisions = Object.fromEntries(rows.map(c => [c.code, c.achieved || '']));
  item.late = (typeof submissionStatus === 'function' && (submissionStatus() || {}).late) ? true : false;
  if (currentStep >= 6) item.reviewed = true;   // the pre-filled suggestions only count once you've gone on to Export
  item.status = item.exported ? 'done' : decided && item.reviewed ? 'ready' : 'marking';
  // the unit, brief and class details are the whole set's, so a change on one learner carries to the rest
  batch.items.forEach(o => {
    if (o === item || !o.snapshot) return;
    o.snapshot.fields = Object.assign({}, o.snapshot.fields, Object.fromEntries(BATCH_SHARED.map(k => [k, f[k] || ''])));
    BATCH_SHARED_KEYS.forEach(k => { o.snapshot[k] = cloneSafe(snap[k]); });
  });
  batchSave();
}
const BATCH_SHARED = ['programme_title', 'programmeSelect', 'unit_context', 'brief_text', 'hide_extra', 'assignment_title', 'assessor_name', 'deadline', 'unit_title'];
const BATCH_SHARED_KEYS = ['subject', 'level', 'units', 'pick', 'model', 'builtinBrief', 'briefChoiceMade'];
// A learner opened for the first time: the shared details (unit, brief, assessor, deadline) with this learner's own
function batchFreshSnapshot(item) {
  const s = draftSnapshot();
  const anon = learnerMode === 'anon';
  if (anon && !item.anonRef) item.anonRef = newAnonRef();
  const sh = batch.shared || {};
  s.fields = Object.assign({}, s.fields, { student_name: anon ? 'Anonymous learner' : item.name || '', student_reg: anon ? item.anonRef : item.reg || '', student_email: '',
    date_submitted: sh.date_submitted || '', extension: sh.extension || '', extension_date: sh.extension_date || '' });
  s.anonRef = anon ? item.anonRef : '';
  s.resub = Object.assign(newResub(), { on: false });
  s.integrity = item.integrity || null; s.aiIntegrity = item.ai ? item.ai.aiIntegrity : null; s.integrityFailed = !item.integrity;
  s.ai = item.ai
    ? { state: 'done', error: '', stale: false, results: item.ai.results, annotations: item.ai.annotations, overall: item.ai.overall, next: item.ai.next, runId: item.ai.runId, runModel: item.ai.model, resultsHtml: '' }
    : { state: 'idle', error: '', stale: false, results: [], annotations: [], overall: '', next: '', runId: '', runModel: '', resultsHtml: '' };
  s.grading = false; s.gradeNote = ''; s.rows = [];
  s.feedback = ''; s.feedbackEdited = false; s.staff = ''; s.staffEdited = false;
  s.email = { to: '', subject: '', body: '', edited: false, manual: false };
  s.step = 4; s.maxStep = Math.max(s.maxStep || 1, 4);
  delete s.iv;
  return s;
}
async function batchOpen(id, opts) {
  opts = opts || {};
  if (batchBusy && !opts.force) return;
  const item = batchItem(id);
  if (!item) return;
  batchBusy = true;
  try {
    // the dates on step 2 when the set is started are everyone's, until changed for a learner
    if (!batch.currentId && !fileBase64) batch.shared = { date_submitted: fieldVal('date_submitted'), extension: fieldVal('extension'), extension_date: fieldVal('extension_date') };
    if (!opts.keepCurrent) batchStoreCurrent();
    const b64 = await batchFile(id);
    if (!b64) { toast(`The file for ${item.name || item.fileName} is missing. Remove the learner and add the file again.`, 'error'); return; }
    const fresh = !item.snapshot;
    const snap = Object.assign({}, fresh ? batchFreshSnapshot(item) : item.snapshot);
    snap.file = { base64: b64, type: item.mediaType, name: item.fileName };
    snap.references = references.map(r => ({ ...r })); snap.brief = brief ? { ...brief } : null;
    if (opts.step) snap.step = opts.step;
    // a clean slate first, so nothing of the last learner's marking carries over
    SMDOC.getElementById('criteriaBody').innerHTML = '';
    lateResubWarned = false;
    pendingDraft = snap;
    resumeDraft(true);
    batch.currentId = id;
    if (item.scan && item.scan.turnitin && item.scan.learnerText) fileScanMode = 'learner';   // as it was checked
    if (learnerMode !== 'anon' && !fieldVal('student_email') && typeof autoEmail === 'function') autoEmail();
    if (fresh) {
      if (item.ai) { batchApplyAi(item); if (!opts.step) goStep(5); }
      else if (item.status === 'needs') {
        // the background check couldn't go ahead: show why on the checks step, with the usual choices
        aiError = item.error || 'The check did not complete.';
        aiState = /Turnitin/.test(aiError) ? 'blocked' : 'error'; setProgress('pAi', 'err');
      }
      else if (item.status === 'checking') aiState = 'running';   // its check arrives here when it's done
      else if (!opts.step) {
        if (checkMode() === 'manual') startManual(true);
        else if (checkMode() === 'ask') aiState = 'choose';
        else if (getApiKey()) setTimeout(() => { if (batch.currentId === id && aiState === 'idle') runCheck(); }, 0);
      }
    }
    renderAiStatus(); refreshNav(); renderLearnerStrip(); updateContext();
    if (item.status === 'waiting' || item.status === 'checked' || item.status === 'needs') item.status = 'marking';
    if (!opts.quiet) batchSave();
  } finally { batchBusy = false; renderBatch(); }
}
// A background check's results into the open learner
function batchApplyAi(item) {
  const a = item.ai; if (!a) return;
  privacyReport = a.privacy || { counts: {}, pdf: '', notes: [] };
  lastResults = a.results || []; aiOverall = a.overall || ''; aiNextSteps = a.next || '';
  lastAnnotations = lastAnnotations.filter(x => x.manual).concat(a.annotations || []);
  aiRunId = a.runId || ''; aiRunModel = a.model || ''; aiError = ''; aiStale = false;
  lastAiIntegrity = a.aiIntegrity || null;
  if (lastIntegrity) renderIntegrity(lastIntegrity, lastAiIntegrity);
  { const at = SMDOC.getElementById('assignment_title'); if (at && !at.value.trim() && a.assignmentTitle) at.value = a.assignmentTitle; }
  renderAnnotations();
  applyAiResults(); aiState = 'done'; setProgress('pAi', 'ok');
  composeFeedback(true); composeStaffNote(true);
}
// Next learner still to finish, after the open one
function batchNextId(dir) {
  const list = batch.items, i = list.findIndex(x => x.id === batch.currentId);
  if (dir === -1) return i > 0 ? list[i - 1].id : '';
  for (let k = 1; k <= list.length; k++) { const it = list[(i + k + list.length) % list.length]; if (it.id !== batch.currentId && it.status !== 'done') return it.id; }
  return '';
}
async function batchOpenNext(dir) {
  const id = batchNextId(dir);
  if (!id) { batchStoreCurrent(); renderBatch(); toast(dir === -1 ? 'This is the first learner.' : 'Everyone in the class set is done.', 'ok'); return; }
  await batchOpen(id);
}
function batchRemove(id) {
  const item = batchItem(id); if (!item) return;
  if (!confirm(`Remove ${item.name || item.fileName} from the class set? Their marking here is lost.`)) return;
  batch.items = batch.items.filter(i => i !== item); delete batchMem[id]; batchDelFile(id);
  if (batch.currentId === id) batch.currentId = '';
  batchSave(true); renderBatch();
}
async function batchClear() {
  if (!batch.items.length || !confirm('Clear the whole class set? Every learner and their marking here is removed.')) return;
  batch.paused = true;
  for (const i of batch.items) await batchDelFile(i.id);
  batch.items = []; batch.currentId = ''; Object.keys(batchMem).forEach(k => delete batchMem[k]);
  batchSave(true); renderBatch();
}
function batchRename(id, field, value) {
  const item = batchItem(id); if (!item) return;
  item[field] = value.trim();
  if (item.snapshot && item.snapshot.fields && learnerMode !== 'anon') item.snapshot.fields[field === 'name' ? 'student_name' : 'student_reg'] = item[field];
  if (id === batch.currentId && learnerMode !== 'anon') { SMDOC.getElementById(field === 'name' ? 'student_name' : 'student_reg').value = item[field]; updateContext(); renderLearnerStrip(); }
  batchSave();
}

// ── the end of the set: every record, and the grades ──
async function batchExportAll() {
  batchStoreCurrent();
  const ready = batch.items.filter(i => i.status === 'ready' || i.status === 'done');
  if (!ready.length) { toast("Nobody is ready yet. Decide every criterion for a learner and go on to Export first.", 'info'); return; }
  const startId = batch.currentId, startStep = currentStep;
  const zip = new JSZip(); let n = 0;
  toast(`Building ${plural(ready.length, 'record')}...`, 'info');
  for (const item of ready) {
    try {
      await batchOpen(item.id, { step: 2, quiet: true, force: true });
      const { blob, name } = await buildRecord({ staffNote: true });
      zip.file(name, blob); n++;
      logAgreement();
      item.exported = true; item.status = 'done';
      batchStoreCurrent();
    } catch (e) { toast(`${item.name || item.fileName}: ${e.message}`, 'error'); }
  }
  if (startId) await batchOpen(startId, { step: startStep, force: true, keepCurrent: true });
  if (!n) return;
  const h = getHeaderData();
  saveBlob(await zip.generateAsync({ type: 'blob', compression: 'DEFLATE' }), ivSafeName(`Assessment records - ${(h.unit_title || 'unit').split(':')[0]} - ${h.assignment_title || 'assignment'}`) + '.zip');
  batchSave(true); renderBatch();
  toast(`Downloaded ${plural(n, 'record')}.`, 'ok');
}
// Anonymous learners get their reference as soon as they're in the set, so it's the same in the tracker, the CSV and the record
function batchEnsureRefs() {
  let added = false;
  batch.items.forEach(i => { if (!i.anonRef) { i.anonRef = newAnonRef(); added = true; } });
  if (added) batchSave();
}
function batchCsv() {
  batchStoreCurrent();
  const codes = getCriteria().filter(c => c.code).map(c => c.code);
  const q = v => /[",\n]/.test(String(v)) ? `"${String(v).replace(/"/g, '""')}"` : String(v);
  const anon = learnerMode === 'anon';
  if (anon) batchEnsureRefs();
  // anonymous: references only, since file names often carry the learner's name
  const lines = [[...(anon ? ['Reference'] : ['Learner', 'ID', 'File']), 'Status', 'Grade', 'Late', ...codes].map(q).join(',')];
  batch.items.forEach(i => lines.push([...(anon ? [i.anonRef] : [i.name, i.reg, i.fileName]), BATCH_STATUS[i.status] || i.status, i.grade || '', i.late ? 'Yes' : '', ...codes.map(c => (i.decisions || {})[c] || '')].map(q).join(',')));
  const h = getHeaderData();
  saveBlob(new Blob([lines.join('\r\n')], { type: 'text/csv' }), ivSafeName(`Grades - ${(h.unit_title || 'unit').split(':')[0]} - ${h.assignment_title || 'assignment'}`) + '.csv');
}

// ── the tracker ──
const BATCH_STATUS = { waiting: 'Not checked', checking: 'Checking', checked: 'Checked', needs: 'Needs you', marking: 'Marking', ready: 'Ready', done: 'Done' };
function batchCounts() {
  const c = {}; batch.items.forEach(i => c[i.status] = (c[i.status] || 0) + 1); return c;
}
function batchTrackerHtml(where) {
  const c = batchCounts(), n = batch.items.length, anon = learnerMode === 'anon';
  if (anon) batchEnsureRefs();
  const waiting = (c.waiting || 0) + (c.checking || 0);
  const summary = n ? [`${plural(n, 'learner')}`, c.checked ? `${c.checked} checked` : '', waiting ? `${waiting} still to check` : '', c.needs ? `${c.needs} need${c.needs === 1 ? 's' : ''} you` : '', c.marking ? `${c.marking} being marked` : '', c.ready ? `${c.ready} ready` : '', c.done ? `${c.done} done` : ''].filter(Boolean).join(', ') + '.' : '';
  const running = batch.items.some(i => i.checking), toCheck = batchToCheck();
  const rows = batch.items.map((i, k) => `<tr data-id="${i.id}"${i.id === batch.currentId ? ' class="is-open"' : ''}>
      <td class="bt-n">${k + 1}</td>
      <td>${anon ? `<span class="bt-anon">${escapeHtml(i.anonRef)}</span>` : `<input class="form-control field-input bt-name" type="text" value="${escapeHtml(i.name || '')}" placeholder="Learner's name" aria-label="Name for ${escapeHtml(i.fileName)}">`}</td>
      ${anon ? '' : `<td><input class="form-control field-input bt-reg" type="text" value="${escapeHtml(i.reg || '')}" placeholder="ID" aria-label="ID for ${escapeHtml(i.fileName)}"></td>`}
      <td class="bt-file" title="${escapeHtml(i.fileName)}">${escapeHtml(i.fileName)}</td>
      <td><span class="bt-status s-${i.status}"${i.error ? ` title="${escapeHtml(i.error)}"` : ''}>${i.status === 'checking' ? '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> ' : ''}${BATCH_STATUS[i.status] || i.status}</span>${i.grade ? ` <span class="badge rounded-pill grade-pill g-${escapeHtml(i.gradeCode || 'X')}">${escapeHtml(i.grade)}</span>` : ''}${i.late ? ' <span class="bt-late">Late</span>' : ''}${i.status === 'needs' ? `<div class="bt-why">${escapeHtml(/Turnitin/.test(i.error || '') ? "Turnitin download with marking in it. Open them to choose what to send." : String(i.error || '').slice(0, 140))}</div>` : ''}</td>
      <td class="bt-act">${i.id === batch.currentId ? '<span class="helper" style="margin:0">Open</span>' : `<button type="button" class="btn btn-sm btn-outline-secondary bt-open">${i.status === 'done' || i.status === 'ready' ? 'Review' : 'Mark'}</button>`}<button type="button" class="btn btn-link btn-sm linkish bt-del" aria-label="Remove ${escapeHtml(i.name || i.fileName)}">Remove</button></td>
    </tr>`).join('');
  return `<div class="bt-head">
      <div><div class="step-title" style="margin:0">Class set</div><p class="helper" style="margin:2px 0 0">${n ? escapeHtml(summary) : 'Add every learner\'s file at once. Each becomes a learner here, named from the file name where possible. Check the names before you start.'}</p></div>
      <div class="bt-tools">
        <button type="button" class="btn btn-sm btn-outline-secondary btn-csv" data-sm-click="SMDOC.getElementById('batchInput').click()">Add files</button>
        ${n ? (running && !batch.paused ? '<button type="button" class="btn btn-sm btn-outline-secondary btn-csv" data-sm-click="batchPause()">Pause checking</button>' : toCheck ? `<button type="button" class="btn btn-sm btn-primary btn-export" data-sm-click="batchStart()">Check ${toCheck === n ? 'them all' : `${toCheck} more`} with the second marker</button>` : '') : ''}
      </div>
    </div>
    ${n ? '' : `<div class="drop-zone bt-drop" tabindex="0" role="button" data-sm-click="SMDOC.getElementById('batchInput').click()" data-sm-keydown="if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); this.click(); }"><div>Drop the whole class set here, or <strong>click to choose the files</strong></div><div class="helper" style="margin-top:4px">PDF or Word (.docx), one file per learner</div></div>`}
    ${n ? `<div class="bt-scroll"><table class="table table-sm bt-table"><thead><tr><th class="bt-n">#</th><th>Learner</th>${anon ? '' : '<th>ID</th>'}<th>File</th><th>Status and grade</th><th class="bt-act" aria-label="Actions"></th></tr></thead><tbody>${rows}</tbody></table></div>
    <div class="bt-foot">
      <button type="button" class="btn btn-sm btn-outline-secondary btn-csv" data-sm-click="batchExportAll()"${(c.ready || 0) + (c.done || 0) ? '' : ' disabled'}>Download all records</button>
      <button type="button" class="btn btn-sm btn-outline-secondary btn-csv" data-sm-click="batchCsv()">Download grades (CSV)</button>
      <button type="button" class="btn btn-link btn-sm linkish" data-sm-click="batchClear()">Clear the class set</button>
    </div>
    <p class="helper" style="margin:8px 0 0">${checkMode() === 'manual' ? 'You mark everyone yourself (your setting), so nothing is checked in the background.' : 'The second marker checks two at a time while you mark. Each learner is saved as you go, so you can move between them. A learner is Ready once every criterion is decided and you\'ve gone on to Export.'}</p>` : ''}`;
}
function renderBatch() {
  const on = batchOn(), open = on && batchCurrent() && fileBase64 ? batchCurrent() : null;
  SMROOT.classList.toggle('batch-on', on);
  SMROOT.classList.toggle('batch-none', on && !open);   // class details only, no learner open
  SMROOT.classList.toggle('batch-hidden', !batchOffered());
  syncWorkRadios();
  const t2 = SMDOC.getElementById('step2Title');
  if (t2) { if (t2.dataset.single === undefined) t2.dataset.single = t2.textContent; t2.textContent = on && !open ? 'Details for the whole class' : t2.dataset.single; }
  const note = SMDOC.getElementById('batchNote');
  if (note && on) note.textContent = open
    ? `You're on ${learnerMode === 'anon' ? open.anonRef || 'this learner' : open.name || 'this learner'}, learner ${batch.items.indexOf(open) + 1} of ${batch.items.length} in the class set. Their name, ID, dates and resubmission are theirs alone. Everything else here is shared with the class.`
    : "Fill in what the whole class shares. Each learner's name and ID come from their file in the next step. If they all handed in on the same day, put that in Date submitted now.";
  // the learner details choice reads for the whole class
  [['named', 'Use their names', "Each learner's name and ID go on their record, the file names and the email."], ['anon', 'Keep them anonymous', 'Each learner gets a reference instead of a name.']].forEach(([v, title, small]) => {
    const lab = SMDOC.querySelector(`input[name="learnerMode"][value="${v}"]`); const span = lab && lab.closest('label');
    if (!span) return;
    const bEl = span.querySelector('b'), sEl = span.querySelector('small');
    if (bEl.dataset.single === undefined) { bEl.dataset.single = bEl.textContent; sEl.dataset.single = sEl.textContent; }
    bEl.textContent = on && !open ? title : bEl.dataset.single; sEl.textContent = on && !open ? small : sEl.dataset.single;
  });
  if (typeof renderStepper === 'function') renderStepper();
  const card = SMDOC.getElementById('batchCard');
  if (card) card.innerHTML = batchOn() ? batchTrackerHtml('step') : '';
  const dlg = SMDOC.getElementById('batchDialog');
  if (dlg && dlg.open) SMDOC.getElementById('batchDialogBody').innerHTML = batchTrackerHtml('dialog') + '<div class="dlg-foot"><button class="btn btn-primary btn-export" data-sm-click="SMDOC.getElementById(\'batchDialog\').close()">Close</button></div>';
  [card, dlg].forEach(root => root && root.querySelectorAll('tr[data-id]').forEach(tr => {
    const id = tr.dataset.id;
    const nm = tr.querySelector('.bt-name'), rg = tr.querySelector('.bt-reg');
    if (nm) nm.onchange = () => batchRename(id, 'name', nm.value);
    if (rg) rg.onchange = () => batchRename(id, 'reg', rg.value);
    const op = tr.querySelector('.bt-open'); if (op) op.onclick = () => { if (dlg && dlg.open) dlg.close(); batchOpen(id); };
    tr.querySelector('.bt-del').onclick = () => batchRemove(id);
  }));
  renderBatchStrip();
}
// The strip above the desk: where you are in the set
function renderBatchStrip() {
  const el = SMDOC.getElementById('batchStrip'); if (!el) return;
  const item = batchCurrent();
  if (!batchOn() || !item || currentStep < 4) { el.hidden = true; return; }
  const i = batch.items.indexOf(item), c = batchCounts(), left = batch.items.filter(x => x.status !== 'done').length;
  const checking = (c.waiting || 0) + (c.checking || 0);
  el.hidden = false;
  el.innerHTML = `<span class="bs-where"><b>Class set</b> Learner ${i + 1} of ${batch.items.length}</span>
    <span class="bs-meta">${left} to finish${checking ? `, ${checking} still checking` : ''}${c.needs ? `, ${c.needs} need${c.needs === 1 ? 's' : ''} you` : ''}</span>
    <span class="bs-btns"><button type="button" class="btn btn-sm btn-outline-secondary" data-sm-click="batchOpenNext(-1)"${i > 0 ? '' : ' disabled'}>Previous</button><button type="button" class="btn btn-sm btn-outline-secondary" data-sm-click="batchOpenNext()">Next learner</button><button type="button" class="btn btn-link btn-sm linkish" data-sm-click="openBatchDialog()">Class set</button></span>`;
}
function openBatchDialog() { batchStoreCurrent(); const d = SMDOC.getElementById('batchDialog'); if (d && !d.open) d.showModal(); renderBatch(); }
// Step 1: one learner, a class set or IV sampling
function syncWorkRadios() {
  const v = ivMode ? 'iv' : batchOn() ? 'class' : 'mark';
  SMDOC.querySelectorAll('input[name="workMode"]').forEach(r => r.checked = r.value === v);
  const t = SMDOC.getElementById('wmMarkTitle'); if (t) t.textContent = batchOffered() ? 'Marking one learner' : 'Marking work';
  SMDOC.querySelectorAll('input[name="batchOption"]').forEach(r => r.checked = (r.value === 'on') === batchOffered());
}
function setWorkMode(m) {
  if (m === 'iv') { if (batchOn()) batchDetach(); setIvMode(true); return; }
  if (ivMode) setIvMode(false);
  setBatchMode(m === 'class');
}
function setBatchMode(on) {
  const was = batchOn();
  if (was && !on) batchDetach();
  storeSet(BATCH_KEY, on ? '1' : '0');
  if (!was && batchOn()) batchAdoptOpen();
  renderBatch(); refreshNav();
}
function setBatchOption(on) {
  if (!on && batchOn()) batchDetach();
  storeSet(BATCH_OPTION_KEY, on ? '1' : '0');
  renderBatch(); refreshNav();
}
// Leaving the class set: the open learner is saved into it, and stays on screen as a single learner
function batchDetach() {
  if (!batchCurrent()) return;
  batchStoreCurrent();
  batch.currentId = '';
  batchSave(true);
  toast('The class set is saved. Choose "Marking a class set" in step 1 to go back to it.', 'info');
}
// Joining the class set with a learner already open: they come along as a learner in it
function batchAdoptOpen() {
  if (!fileBase64 || batchCurrent()) return;
  let item = batch.items.find(i => i.fileName === fileNameStr && (!i.b64len || i.b64len === fileBase64.length));
  if (!item) {
    const anon = learnerMode === 'anon';
    item = { id: batchId(), fileName: fileNameStr || 'Work', size: Math.round(fileBase64.length * 3 / 4), b64len: fileBase64.length, mediaType: fileMediaType,
      name: anon ? '' : fieldVal('student_name').trim(), reg: anon ? '' : fieldVal('student_reg').trim(), anonRef: anon ? anonRef : '',
      status: 'marking', added: Date.now(), integrity: cloneSafe(lastIntegrity), scan: cloneSafe(fileScan) };
    if (aiState === 'done' && lastResults.length) item.ai = { results: cloneSafe(lastResults), annotations: cloneSafe(lastAnnotations), overall: aiOverall, next: aiNextSteps, aiIntegrity: cloneSafe(lastAiIntegrity), runId: aiRunId, model: aiRunModel, privacy: cloneSafe(privacyReport) };
    batch.items.push(item); batchMem[item.id] = fileBase64; batchPutFile(item.id, fileBase64).catch(() => {});
    toast(`${item.name || (anon ? item.anonRef : '') || 'The learner you had open'} is now in the class set.`, 'ok');
  }
  if (!batch.shared) batch.shared = { date_submitted: fieldVal('date_submitted'), extension: fieldVal('extension'), extension_date: fieldVal('extension_date') };
  batch.currentId = item.id;
  batchStoreCurrent();
}

// ── hooks into the main app ──
const _bStepProblem = stepProblem;
stepProblem = function (n) {
  if (batchOn() && n === 3 && !fileBase64) return batch.items.length ? '' : 'Add the class set to continue.';
  return _bStepProblem(n);
};
const _bGoStep = goStep;
goStep = function (n) {
  if (batchOn() && currentStep === 3 && n > 3 && !fileBase64 && batch.items.length) { batchOpenNext(); return; }
  _bGoStep(n);
  if (batchOn() && currentStep === 6 && batchCurrent() && !batchBusy) { batchStoreCurrent(); renderBatch(); }
  renderBatchStrip();
};
const _bNextLearner = nextLearner;
nextLearner = function () {
  if (batchOn() && batchCurrent()) { batchOpenNext(); return; }
  _bNextLearner();
};
// logAgreement runs only once a record has downloaded, so it's the sign the learner is done
let batchJustExported = false;
const _bLogAgreement = logAgreement;
logAgreement = function () { batchJustExported = true; return _bLogAgreement.apply(this, arguments); };
const _bExportDoc = exportDoc;
exportDoc = async function () {
  batchJustExported = false;
  await _bExportDoc();
  const item = batchOn() && batchCurrent();
  if (item && batchJustExported) { item.exported = true; batchStoreCurrent(); renderBatch(); }
};
const _bSetIvMode = setIvMode;
setIvMode = function (on) { _bSetIvMode(on); renderBatch(); };
// the stepper reads "Details" and "Class set" while marking a class set
const _bRenderStepper = renderStepper;
renderStepper = function () {
  _bRenderStepper();
  if (!batchOn()) return;
  const li = SMDOC.querySelectorAll('#stepper li');
  [[1, 'Details'], [2, 'Class set']].forEach(([i, t]) => { const el = li[i] && li[i].querySelector('button, .sl'); if (el) el.textContent = t; });
};
// keep the open learner's place in the set saved along with the draft
const _bSaveDraft = saveDraft;
saveDraft = async function () { const r = await _bSaveDraft.apply(this, arguments); if (batchOn() && batchCurrent() && !batchBusy) batchStoreCurrent(); return r; };
const _bRefreshNav = refreshNav;
refreshNav = function () {
  _bRefreshNav();
  const b = SMDOC.getElementById('nextBtn');
  if (batchOn() && b) {
    if (currentStep === 1) b.textContent = 'Continue to class details';
    if (currentStep === 2 && !batchCurrent()) b.textContent = 'Continue to the class set';
    if (currentStep === 3 && !fileBase64 && batch.items.length) { b.textContent = 'Start marking'; b.disabled = false; }
  }
  const nb = SMDOC.querySelector('[onclick="nextLearner()"]');
  if (nb) nb.textContent = batchOn() && batchCurrent() ? 'Next in the class set' : 'Next learner, same unit';
};

(function batchInit() {
  const inp = SMDOC.getElementById('batchInput');
  if (inp) inp.onchange = () => { const f = [...inp.files]; inp.value = ''; batchAdd(f); };
  const card = SMDOC.getElementById('batchCard');
  if (card) {
    const zone = on => { const dz = card.querySelector('.bt-drop'); if (dz) dz.classList.toggle('drag', on); card.classList.toggle('drag', on); };
    ['dragenter', 'dragover'].forEach(ev => card.addEventListener(ev, e => { if (!batchOn()) return; e.preventDefault(); zone(true); }));
    card.addEventListener('dragleave', e => { if (!card.contains(e.relatedTarget)) zone(false); });
    card.addEventListener('drop', e => { if (!batchOn()) return; e.preventDefault(); zone(false); batchAdd(e.dataTransfer.files); });
  }
  renderBatch();
  batchLoad();
})();

  /* ---- after the marking code has started: Crafticulum's settings, messages and data ---- */
  const __row = el => (el && el.closest ? el.closest(".settings-row") : null);
  const __show = (el, on) => { if (el) { el.hidden = !on; el.style.display = on ? "" : "none"; } };
  toast = function (msg, kind) { try { window.toast(String(msg), { bad: kind === "error" }); } catch (e) { console.log(msg); } };
  applyTheme = function () { SMROOT.setAttribute("data-theme", window.ccMarkerCtx().theme === "dark" ? "dark" : "light"); };
  applyTheme();
  /* the department's rules, applied again whenever they change */
  function __apply() {
    const C = window.ccMarkerCtx();
    window.BTEC_EMAIL_DOMAIN = C.emailDomain || "";
    __show(SMDOC.getElementById("settingsToggle"), false); __show(SMDOC.getElementById("themeBtn"), false);
    const models = SMDOC.querySelectorAll('input[name="model"]');
    if (C.model) models.forEach(r => { if (r.value === C.model) r.checked = true; });
    else { try { const mv = localStorage.getItem("cc_marker_model" + CCNS), mr = mv && SMDOC.querySelector('input[name="model"][value="' + mv + '"]'); if (mr) mr.checked = true; } catch (e) {} }
    try { if (!C.batch && typeof setBatchOption === "function" && typeof batchOffered === "function" && batchOffered()) setBatchOption(false); } catch (e) {}
    const iv = SMDOC.querySelector('input[name="workMode"][value="iv"]');
    if (iv) { __show(iv.closest("label"), !!C.iv); if (!C.iv) { try { if (ivMode) setWorkMode("mark"); } catch (e) {} } }
    const nm = SMDOC.querySelector('input[name="learnerMode"][value="named"]');
    if (nm) __show(nm.closest("label"), !C.anon);
    try { if (C.anon && learnerMode !== "anon") setLearnerMode("anon"); } catch (e) {}
    try { if (C.pdfText) setPdfPrivacy("text"); } catch (e) {}
    __prefs();
  }
  /* each person's own choices, set in Settings, Second Marker */
  function __prefs() {
    const C = window.ccMarkerCtx();
    try { if (!C.model) { const mv = localStorage.getItem("cc_marker_model" + CCNS), mr = mv && SMDOC.querySelector('input[name="model"][value="' + mv + '"]'); if (mr) mr.checked = true; } } catch (e) {}
    try { const lm = localStorage.getItem("btec_learner_mode" + CCNS) === "anon" ? "anon" : "named", typed = ["student_name", "student_reg"].some(id => { const el = SMDOC.getElementById(id); return el && el.value.trim(); });
      if (!C.anon && learnerMode !== lm && !(lm === "anon" && typed) && !(typeof batchOn === "function" && batchOn())) setLearnerMode(lm); } catch (e) {}
    try { syncPrefRadios(); if (typeof renderBatch === "function") renderBatch(); refreshNav(); } catch (e) {}
  }
  /* the unit and brief to open on, and the latest dates */
  function __data(d) {
    try { const now = __courses(), ss0 = SMDOC.getElementById("subjectSelect"), sig = x => JSON.stringify(x.map(s => [s.subject, s.levels.map(l => [l.level, l.units.map(u => [u.title, u.criteria.length])])]));
      // a subject was added or changed in Crafticulum: use it straight away, unless work is open on the old list
      if (ss0 && ss0.value === "" && sig(now.data) !== sig(window.BTEC_DATA)) { window.BTEC_DATA.splice(0, window.BTEC_DATA.length, ...now.data);
        ss0.innerHTML = '<option value="">Select a subject...</option>'; window.BTEC_DATA.forEach((s, i) => { const o = document.createElement("option"); o.value = i; o.textContent = s.subject; ss0.appendChild(o); }); }
      now.briefs.forEach(nb => { const b = window.BTEC_BRIEFS.find(x => x.id === nb.id); if (b) { b.handout = nb.handout; b.deadline = nb.deadline; } else window.BTEC_BRIEFS.push(nb); }); } catch (e) {}
    try {
      const ss = SMDOC.getElementById("subjectSelect"), ls = SMDOC.getElementById("levelSelect"), us = SMDOC.getElementById("unitSelect"), s = (d && d.sel) || {};
      if (!ss || !s.s || restoring || pendingDraft) return;
      const busy = !!fileBase64 || (typeof batchOn === "function" && batchOn());
      if (ss.value !== "" && !(d.force && !busy)) { if (d.force && busy) toast("You have work open here. Finish it or start again to switch unit.", "error"); return; }
      const si = window.BTEC_DATA.findIndex(x => x.subject === s.s); if (si < 0) return;
      ss.value = String(si); onSubjectChange();
      const li = window.BTEC_DATA[si].levels.findIndex(x => x.level === s.l); if (li < 0) return;
      ls.value = String(li); onLevelChange();
      const ui = window.BTEC_DATA[si].levels[li].units.findIndex(x => x.title === s.u); if (ui < 0) return;
      us.value = String(ui); onUnitChange();
      if (s.brief && window.BTEC_BRIEFS.some(b => b.id === s.brief)) chooseBuiltinBrief(s.brief);
    } catch (e) { console.error(e); }
  }
  __apply();
  const __fillName = () => { const f = SMDOC.getElementById("assessor_name"), n = window.ccMarkerCtx().name; if (f && !f.value && n) { f.value = n; try { f.dispatchEvent(new Event("input", { bubbles: true })); } catch (e) {} } };
  __fillName(); setTimeout(__fillName, 1200);
  setTimeout(() => { if (__first.sel) __data({ sel: __first.sel, force: !!__first.sel.brief }); }, 700);
  /* what was marked, with no learner details: which criteria were met, the grade, the group and the assignment.
     Results uses it to show what a group keeps missing, and the tracker and IV plan count from it. */
  { let runKey = "", lastPrint = ""; const _log = logAgreement;
    const post = (a, body) => fetch("marker/api.php?a=" + a, { method: "POST", headers: { "Content-Type": "application/json" }, credentials: "same-origin", body: JSON.stringify(body) }).then(() => { try { window.ccMarkerLogged(); } catch (e) {} }).catch(() => {});
    logAgreement = function () {
      const r = _log.apply(this, arguments);
      try {
        const C = window.ccMarkerCtx(); if (!C.feedback) return r;
        const h = getHeaderData(), crit = getCriteria().filter(c => c.code), si = SMDOC.getElementById("subjectSelect").value, lv = currentLevel();
        if (!h.unit_title) return r;
        const print = (fileBase64 ? fileBase64.length : 0) + "|" + ((SMDOC.getElementById("student_reg") || {}).value || "") + "|" + ((SMDOC.getElementById("student_name") || {}).value || "");   // stays in the browser: only tells one piece of work from the next
        if (print !== lastPrint) { lastPrint = print; runKey = ""; }
        const id = aiRunId || runKey || (runKey = "m" + Date.now() + Math.random().toString(36).slice(2, 8));
        const base = { id: String(id), unit: h.unit_title, subject: si !== "" && window.BTEC_DATA[+si] ? window.BTEC_DATA[+si].subject : "", level: lv ? lv.level : "", group: C.group || "", brief: (typeof builtinBriefId !== "undefined" && builtinBriefId) || "", assignment: h.assignment_title || "" };
        if (ivMode) { const st = ivStats(); if (!st.complete) return r;
          post("iv", { ...base, id: "iv" + base.id, assessor: h.assessor_name || "", asrGrade: st.asrGrade ? st.asrGrade.grade : "", ivGrade: st.yourGrade ? st.yourGrade.grade : "", agree: st.agree, total: st.rows.length }); return r; }
        const rows = crit.filter(c => c.achieved === "Y" || c.achieved === "N").map(c => ({ code: c.code, met: c.achieved })); if (!rows.length) return r;
        const g = rows.length === crit.length ? gradeOf(crit, c => c.achieved === "Y") : null;
        post("result", { ...base, rows, grade: g ? g.label : "", gradeKey: g ? g.grade : "", resub: !!(resub && resub.on) });
      } catch (e) { console.error(e); }
      return r;
    };
    /* starting on a new learner starts a new line in the log */
    const _rl = typeof restartLearner === "function" ? restartLearner : null; if (_rl) restartLearner = function () { runKey = ""; return _rl.apply(this, arguments); };
  }
  setInterval(() => { if (!__active()) return; try { const c2 = SMDOC.querySelector("#ctxLine .c2"); window.ccMarkerSub(c2 ? c2.textContent : ""); } catch (e) {} }, 1500);
  /* ---- the first two steps are Crafticulum's own screens (r25-native.js). This is everything they need to draw, in one plain object.
     They change things by calling the marking code's own functions through run(), so the rules stay in one place. ---- */
  function __nstate() {
    const g = id => SMDOC.getElementById(id), v = id => { const e = g(id); return e ? e.value || "" : ""; };
    const opts = id => { const e = g(id); return e ? [...e.querySelectorAll("option")].filter(o => o.value !== "" || id === "builtinBrief").map(o => ({ v: o.value, t: o.textContent, d: !!o.disabled, g: o.parentElement && o.parentElement.tagName === "OPTGROUP" ? o.parentElement.label : "" })) : []; };
    const shown = id => { const e = g(id); return !!e && e.style.display !== "none" && !e.hidden; };
    const C = window.ccMarkerCtx(), bOn = typeof batchOn === "function" && batchOn(), open = bOn && batchCurrent() && fileBase64 ? batchCurrent() : null, lv = currentLevel();
    const letters = [...new Set(pick.flatMap(p => aimLetters(p.code)))].sort();
    const libBrief = typeof currentBuiltinBrief === "function" ? currentBuiltinBrief() : null, tasks = {};
    if (libBrief) libBrief.criteria.forEach(c => { (tasks[c.task] = tasks[c.task] || []).push((libBrief.units.length > 1 ? tagName(c.unit) + " " : "") + c.code); });
    return {
      step: currentStep, mode: ivMode ? "iv" : bOn ? "class" : "mark", canClass: typeof batchOffered === "function" && batchOffered() && C.batch !== false, canIv: !!C.iv,
      subjects: opts("subjectSelect"), si: v("subjectSelect"), levels: opts("levelSelect"), li: v("levelSelect"), units: opts("unitSelect"), ui: v("unitSelect"),
      sel: selectedUnits.slice(), all: lv ? lv.units.map((u, i) => ({ i, t: cleanTitle(u.title), g: u.group || "" })) : [], level: lv ? lv.level : "", subject: subjectName(),
      unitTitles: lv ? selectedUnits.map(i => lv.units[i] ? lv.units[i].title : "") : [],
      briefs: shown("assignWrap") ? opts("builtinBrief") : [], briefId: builtinBriefId || "", briefIdx: (g("builtinBrief") || {}).selectedIndex,
      brief: libBrief ? { tasks: Object.entries(tasks).map(([t, cs]) => ({ t, cs })), handout: libBrief.handout || "", deadline: libBrief.deadline || "", text: libBrief.text || "" } : null,
      progs: shown("programmeSelect") ? opts("programmeSelect") : [], prog: v("programme_title"),
      pick: pick.map((p, i) => ({ i, code: p.code, desc: p.desc || "", on: !!p.on, unit: p.unitTitle || p.unit || "", band: bandOf(p.code) || "", bandLabel: bandOf(p.code) ? bandLabel(bandOf(p.code)) : "" })),
      aims: letters.map(l => { const own = pick.filter(p => aimLetters(p.code).includes(l)); return { l, t: aimLabel(l), on: !!own.length && own.every(p => p.on) }; }),
      critCsv: (g("criteriaCsvStatus") || {}).textContent || "",
      learner: learnerMode, anonRef: anonRef || "", forceAnon: !!C.anon, batch: { on: bOn, open: !!open, note: bOn ? (g("batchNote") || {}).textContent || "" : "" },
      f: Object.fromEntries(["student_name", "student_reg", "student_email", "assignment_title", "assessor_name", "iv_name", "deadline", "date_submitted", "extension", "extension_date", "unit_title", "hide_extra"].map(id => [id, v(id)])),
      late: (g("lateStatus") || {}).innerHTML || "", csv: (g("csvStatus") || {}).textContent || "", presets: shown("presetSelect") ? opts("presetSelect") : [],
      problem: stepProblem(currentStep) || "",
      /* the rest of steps 1 to 3 */
      upBrief: brief ? { name: brief.name } : null, briefText: v("brief_text"), briefNote: (g("briefTextNote") || {}).textContent || "", briefHelp: (g("briefHelp") || {}).textContent || "",
      guidance: v("unit_context"),
      refs: references.map(r => ({ name: r.name, ready: !!r.base64, grade: r.grade || "", notes: r.notes || "" })), refMax: MAX_REFS,
      refGrades: lv && lv.grading === "pass" ? ["Pass", "Not yet achieved"] : lv && lv.grading === "first" ? ["Level 2 Distinction", "Level 2 Merit", "Level 2 Pass", "Level 1", "Not yet achieved"] : ["Distinction", "Merit", "Pass", "Not yet achieved"],
      resub: { on: !!resub.on, type: resub.type || "Resubmission", record: resub.recordName || "", note: (g("resubNote") || {}).textContent || "", rows: pick.filter(p => p.on).map(p => ({ code: p.code, k: codeKey(p.code), v: resub.prev[codeKey(p.code)] || "" })) },
      file: fileBase64 ? (fileNameStr || "File added") : "", links: shown("linkFeature"), linkStatus: (g("linkStatus") || {}).innerHTML || "", linkBusy: !!(g("linkBtn") || {}).disabled,
      progress: shown("uploadProgress") ? ["pFile", "pIntegrity", "pAi"].map(id => ({ id, cls: (g(id) || {}).className || "", t: (g(id) || {}).textContent || "" })) : [],
      ex: currentStep === 6 ? __nexport() : null
    };
  }
  /* step 6, review and export: everything the app's own screen shows */
  function __nexport() {
    const g = id => SMDOC.getElementById(id), h = getHeaderData(), btn = id => { const e = g(id); return e ? { t: e.textContent || "", d: !!e.disabled } : { t: "", d: false }; }, chk = id => !!(g(id) || {}).checked;
    const o = { iv: !!ivMode, unit: h.unit_title || "", assignment: h.assignment_title || "", assessor: h.assessor_name || "", fb: lastFeedback || "", note: (g("dlNote") || {}).textContent || "",
      pdf: fileMediaType === "application/pdf", hasFile: !!fileBase64,
      b: { record: btn("exportBtn"), marked: btn("markedPdfBtn"), word: btn("commentedDocxBtn"), ivrec: btn("ivRecordBtn"), eml: btn("emlBtn"), copy: btn("copyEmailBtn") },
      email: { emailTo: (g("emailTo") || {}).value || "", emailSubject: (g("emailSubject") || {}).value || "", emailBody: (g("emailBody") || {}).value || "" },
      att: { attMarked: chk("attMarked"), attRecord: chk("attRecord"), attCommented: chk("attCommented"), attStaff: chk("attStaff") } };
    if (ivMode) { const s = ivStats(), cl = ivChecklist(), word = v => v ? IV_CALL_WORDS[v] || v : "Not entered";
      Object.assign(o, { learner: ivLearnerLabel(), ivName: ivName() || "", asrGrade: s.asrGrade ? { g: s.asrGrade.grade, l: s.asrGrade.label } : null, yourGrade: s.yourGrade ? { g: s.yourGrade.grade, l: s.yourGrade.label } : null, overall: cl.overall || "",
        rows: s.rows.map(r => ({ code: r.code, asr: word(r.asr), you: r.you ? (r.you === "Y" ? "Achieved" : "Not achieved") : "Not decided", verdict: r.verdict || "" })),
        checklist: IV_QUESTIONS.map(([k, q]) => ({ q, a: cl[k] || "" })), actions: ivActionList(), target: iv.target || "" }); }
    else { const rows = getCriteria().filter(c => c.code), gr = gradeOf(rows, r => r.achieved === "Y"), flags = lastIntegrity ? lastIntegrity.flags : [], st = submissionStatus();
      Object.assign(o, { learner: h.student_name || "", reg: h.student_reg || "", grade: { g: gr.grade, l: gr.label }, sub: st.state === "unknown" ? escapeHtml(h.date_submitted || "Date not entered") : lateChip(st),
        integrity: lastIntegrity ? { high: flags.filter(f => f.level === "high").length, med: flags.filter(f => f.level === "medium").length } : null,
        rows: rows.map(r => ({ code: r.code, a: r.achieved || "", c: r.comment || "" })), staff: staffNoteText() || "" }); }
    return o;
  }
  /* a typed field on the app's screen: put it in the marking code's own field and let its own listeners run */
  function __nset(id, val) { const e = SMDOC.getElementById(id); if (!e) return; e.value = val; e.dispatchEvent(new Event(e.tagName === "SELECT" ? "change" : "input", { bubbles: true })); if (typeof scheduleSave === "function") scheduleSave(); }
  return { root: SMROOT, doc: SMDOC, apply: __apply, prefs: __prefs, data: __data, theme: applyTheme, run: code => eval(code), state: __nstate, set: __nset };

};
