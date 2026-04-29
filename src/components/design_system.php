<?php /*
   Shared design system (pc-*) for all role headers.
   Include via:  <?php include "../src/components/design_system.php"; ?>
*/ ?>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root {
    --pc-red-900: #7f1d1d;
    --pc-red-800: #991b1b;
    --pc-red-700: #b91c1c;
    --pc-red-50:  #fef2f2;
  }
  body {
    font-family: 'Inter', 'Poppins', system-ui, -apple-system, sans-serif;
    background: #f3f4f6;
    color: #1f2937;
  }

  /* ---------- Top bar ---------- */
  .pc-topbar {
    position: relative;
    background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 50%, #b91c1c 100%);
    border-radius: 0.85rem;
    box-shadow: 0 12px 28px -16px rgba(127, 29, 29, .55), 0 2px 4px rgba(127, 29, 29, .15);
    overflow: hidden;
    margin-bottom: 1.5rem;
  }
  .pc-topbar::before {
    content: '';
    position: absolute; inset: 0;
    background:
      radial-gradient(circle at top right, rgba(255,255,255,.18), transparent 55%),
      radial-gradient(circle at bottom left, rgba(0,0,0,.25), transparent 60%);
    pointer-events: none;
  }
  .pc-topbar-inner {
    position: relative;
    display: flex; align-items: center; justify-content: space-between;
    padding: 1.05rem 1.5rem; gap: 1rem; flex-wrap: wrap;
  }
  .pc-topbar-title { display: flex; align-items: center; gap: 0.85rem; }
  .pc-topbar-icon {
    width: 2.6rem; height: 2.6rem; border-radius: 0.6rem;
    background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.25);
    display: flex; align-items: center; justify-content: center;
    color: #fff; backdrop-filter: blur(4px); flex-shrink: 0;
  }
  .pc-topbar-icon .material-icons { font-size: 1.6rem; }
  .pc-topbar-title h2 {
    color: #fff; font-weight: 700; font-size: 1.25rem; line-height: 1.1;
    letter-spacing: 0.02em; margin: 0;
  }
  .pc-topbar-title p {
    color: rgba(254, 226, 226, .85); font-size: 0.75rem; margin-top: 2px; margin-bottom: 0;
  }
  .pc-topbar-meta { display: flex; align-items: center; gap: 0.85rem; }
  .pc-topbar-welcome { text-align: right; line-height: 1.1; }
  .pc-topbar-welcome .small {
    color: rgba(254, 226, 226, .8); font-size: 0.7rem;
    text-transform: uppercase; letter-spacing: 0.04em;
  }
  .pc-topbar-welcome .name { color: #fff; font-weight: 600; font-size: 0.9rem; }
  .pc-avatar {
    width: 2.6rem; height: 2.6rem; border-radius: 9999px;
    background: linear-gradient(135deg, rgba(255,255,255,.25), rgba(255,255,255,.1));
    border: 2px solid rgba(255,255,255,.45);
    color: #fff; display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 1.05rem; text-transform: uppercase;
    box-shadow: 0 2px 6px rgba(0,0,0,.2); flex-shrink: 0;
  }

  /* ---------- Cards ---------- */
  .pc-card {
    background: #fff; border-radius: 0.85rem;
    box-shadow: 0 1px 3px rgba(0,0,0,.04), 0 6px 18px rgba(15, 23, 42, .05);
    border: 1px solid rgba(15, 23, 42, .06);
  }
  .pc-card-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 1rem 1.25rem; border-bottom: 1px solid rgba(15,23,42,.07);
    flex-wrap: wrap; gap: .75rem;
  }
  .pc-card-title {
    display: flex; align-items: center; gap: .55rem;
    color: var(--pc-red-900); font-weight: 700; font-size: 1.05rem;
  }
  .pc-card-body { padding: 1.25rem; }

  /* ---------- Stat cards ---------- */
  .pc-stat {
    position: relative; overflow: hidden;
    border-radius: 0.85rem; padding: 1.1rem 1.25rem;
    color: #fff; display: flex; align-items: center; gap: 1rem;
    box-shadow: 0 12px 24px -16px rgba(127, 29, 29, .6);
    transition: transform .2s, box-shadow .2s;
  }
  .pc-stat:hover { transform: translateY(-2px); box-shadow: 0 18px 30px -18px rgba(127, 29, 29, .65); }
  .pc-stat::before {
    content: ''; position: absolute; inset: 0;
    background:
      radial-gradient(circle at top right, rgba(255,255,255,.2), transparent 50%),
      radial-gradient(circle at bottom left, rgba(0,0,0,.2), transparent 60%);
    pointer-events: none;
  }
  .pc-stat-icon {
    width: 3.25rem; height: 3.25rem; border-radius: 0.75rem;
    display: flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.25);
    backdrop-filter: blur(4px); flex-shrink: 0;
  }
  .pc-stat-icon .material-icons { font-size: 1.85rem; }
  .pc-stat .pc-stat-label {
    font-size: 0.78rem; text-transform: uppercase; letter-spacing: .06em;
    color: rgba(255,255,255,.85); font-weight: 500;
  }
  .pc-stat .pc-stat-value { font-size: 1.85rem; font-weight: 800; line-height: 1.1; }
  .pc-stat-red    { background: linear-gradient(135deg, #7f1d1d, #b91c1c); }
  .pc-stat-amber  { background: linear-gradient(135deg, #b45309, #d97706); }
  .pc-stat-emerald{ background: linear-gradient(135deg, #047857, #059669); }
  .pc-stat-indigo { background: linear-gradient(135deg, #3730a3, #4f46e5); }

  /* ---------- Buttons ---------- */
  .pc-btn {
    display: inline-flex; align-items: center; gap: .45rem;
    padding: .55rem 1rem; border-radius: .55rem; font-weight: 600;
    font-size: .88rem; cursor: pointer; transition: all .18s ease;
    border: 1px solid transparent; line-height: 1;
    white-space: nowrap;
  }
  .pc-btn .material-icons { font-size: 1.1rem; }
  .pc-btn:disabled { opacity: .55; cursor: not-allowed; }
  .pc-btn:focus { outline: none; }
  .pc-btn:focus-visible {
    outline: none;
    box-shadow: 0 0 0 3px rgba(185, 28, 28, .25);
  }
  .pc-btn-neutral:focus-visible {
    box-shadow: 0 0 0 3px rgba(75, 85, 99, .22);
  }
  .pc-btn-primary {
    background: linear-gradient(135deg, #7f1d1d, #991b1b); color: #fff;
    box-shadow: 0 4px 12px -4px rgba(127, 29, 29, .55);
  }
  .pc-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 18px -6px rgba(127, 29, 29, .65); }
  .pc-btn-secondary {
    background: #fff; color: var(--pc-red-900); border-color: #fecaca;
  }
  .pc-btn-secondary:hover { background: #fef2f2; }
  .pc-btn-success {
    background: linear-gradient(135deg, #047857, #059669); color: #fff;
    box-shadow: 0 4px 12px -4px rgba(4, 120, 87, .55);
  }
  .pc-btn-success:hover { transform: translateY(-1px); box-shadow: 0 8px 18px -6px rgba(4, 120, 87, .6); }
  .pc-btn-ghost {
    background: rgba(127, 29, 29, .08); color: var(--pc-red-900);
  }
  .pc-btn-ghost:hover { background: rgba(127, 29, 29, .14); }
  .pc-btn-neutral {
    background: #e5e7eb; color: #374151;
  }
  .pc-btn-neutral:hover { background: #d1d5db; }
  .pc-btn-danger {
    background: linear-gradient(135deg, #b91c1c, #dc2626); color: #fff;
    box-shadow: 0 4px 12px -4px rgba(220, 38, 38, .55);
  }
  .pc-btn-danger:hover { transform: translateY(-1px); box-shadow: 0 8px 18px -6px rgba(220, 38, 38, .65); }
  .pc-btn-sm { padding: .35rem .65rem; font-size: .78rem; border-radius: .4rem; }
  .pc-btn-sm .material-icons { font-size: .95rem; }

  /* ---------- Inputs ---------- */
  .pc-input, .pc-select, .pc-textarea {
    width: 100%; padding: .55rem .75rem; border: 1px solid #e5e7eb;
    border-radius: .55rem; background: #fff; font-size: .9rem; color: #1f2937;
    transition: border-color .15s, box-shadow .15s;
  }
  .pc-input:focus, .pc-select:focus, .pc-textarea:focus {
    outline: none; border-color: #b91c1c;
    box-shadow: 0 0 0 3px rgba(185, 28, 28, .12);
  }
  .pc-label {
    display: block; font-size: .72rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: .04em;
    color: #4b5563; margin-bottom: .25rem;
  }

  /* ---------- Tables ---------- */
  .pc-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: .88rem;
    border-radius: .65rem;
    overflow: hidden;
    box-shadow:
      0 1px 2px rgba(15, 23, 42, .04),
      0 8px 24px -12px rgba(15, 23, 42, .12);
    background: #fff;
  }
  .pc-table thead th {
    position: relative;
    background: linear-gradient(180deg, #7f1d1d 0%, #991b1b 60%, #7f1d1d 100%);
    color: #fff;
    padding: .85rem 1rem;
    text-align: left;
    font-weight: 700;
    letter-spacing: .06em;
    font-size: .72rem;
    text-transform: uppercase;
    text-shadow: 0 1px 1px rgba(0, 0, 0, .25);
    border-bottom: 2px solid rgba(255, 215, 0, .35);
  }
  .pc-table thead th::after {
    content: '';
    position: absolute; right: 0; top: 25%; height: 50%; width: 1px;
    background: rgba(255, 255, 255, .12);
  }
  .pc-table thead th:last-child::after { display: none; }
  .pc-table thead th:first-child { border-top-left-radius: .65rem; }
  .pc-table thead th:last-child  { border-top-right-radius: .65rem; }

  .pc-table tbody tr {
    background: #fff;
    transition: all .18s ease;
  }
  .pc-table tbody tr:nth-child(even) { background: #fafbfc; }
  .pc-table tbody tr:hover {
    background: linear-gradient(90deg, #fef2f2, #fff7f7) !important;
    box-shadow:
      inset 3px 0 0 var(--pc-red-700),
      0 4px 12px -6px rgba(127, 29, 29, .25);
    transform: translateY(-1px);
  }
  .pc-table tbody td {
    padding: .8rem 1rem;
    border-bottom: 1px solid #f1f3f5;
    vertical-align: middle;
    color: #374151;
  }
  .pc-table tbody tr:last-child td { border-bottom: none; }
  .pc-table tbody tr:last-child td:first-child { border-bottom-left-radius: .65rem; }
  .pc-table tbody tr:last-child td:last-child { border-bottom-right-radius: .65rem; }
  .pc-table tbody td:first-child {
    font-weight: 600;
    color: #1f2937;
  }
  .pc-table tbody tr td:first-child {
    border-left: 3px solid transparent;
    transition: border-color .18s ease;
  }
  .pc-table tbody tr:hover td:first-child {
    border-left-color: var(--pc-red-700);
  }

  /* Action buttons inside table */
  .pc-table .pc-btn-sm { box-shadow: none; }
  .pc-table tbody tr:hover .pc-btn-sm { box-shadow: 0 2px 6px -2px rgba(0,0,0,.15); }

  /* Empty state row */
  .pc-table tbody tr.pc-table-empty:hover {
    background: #fff !important;
    box-shadow: none;
    transform: none;
  }
  .pc-table tbody tr.pc-table-empty td {
    text-align: center;
    color: #9ca3af;
    padding: 2.5rem 1rem;
    font-style: italic;
  }

  /* Schedule grid (week view) */
  .pc-week-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: .85rem;
    border-radius: .65rem;
    overflow: hidden;
    box-shadow: 0 8px 24px -12px rgba(15, 23, 42, .15);
  }
  .pc-week-table thead th {
    background: linear-gradient(135deg, #7f1d1d, #991b1b);
    color: #fff;
    padding: .7rem .55rem;
    border: 1px solid rgba(127, 29, 29, .4);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    font-size: .72rem;
    text-shadow: 0 1px 1px rgba(0, 0, 0, .25);
  }
  .pc-week-table thead th:first-child { border-top-left-radius: .65rem; }
  .pc-week-table thead th:last-child { border-top-right-radius: .65rem; }
  .pc-week-table tbody td {
    border: 1px solid #e5e7eb;
    padding: .4rem .5rem;
    vertical-align: top;
    background: #fff;
    transition: background .15s ease;
  }
  .pc-week-table tbody tr:hover td:not(.time-col) {
    background: #fafbfc;
  }
  .pc-week-table tbody td.time-col {
    background: linear-gradient(180deg, #fef2f2, #fee2e2);
    color: #7f1d1d;
    font-weight: 700;
    text-align: center;
    letter-spacing: .03em;
    border-right: 2px solid rgba(127, 29, 29, .15);
  }

  /* ---------- Subject filters bar ---------- */
  .subject-filters {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr)) auto;
    gap: .75rem;
    align-items: end;
  }
  .subject-filters .subject-filter-field { min-width: 0; }
  .subject-filters .subject-filter-reset {
    height: calc(.55rem * 2 + .9rem + 2px); /* match pc-input/.pc-select height */
    align-self: end;
  }
  @media (max-width: 1024px) {
    .subject-filters {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .subject-filters .subject-filter-reset {
      grid-column: 1 / -1;
      justify-self: end;
      height: auto;
    }
  }
  @media (max-width: 640px) {
    .subject-filters { grid-template-columns: 1fr; }
    .subject-filters .subject-filter-reset {
      justify-self: stretch;
      width: 100%;
      justify-content: center;
    }
  }

  /* ---------- Report page toolbar ---------- */
  .report-toolbar {
    display: flex; flex-direction: row;
    align-items: center; justify-content: space-between;
    gap: .75rem;
    flex-wrap: wrap;
  }
  .report-toolbar > .pc-tabs { flex: 1 1 auto; }
  .report-toolbar #printReport {
    flex: 0 0 auto;
    width: auto;
    white-space: nowrap;
  }
  @media (max-width: 640px) {
    .report-toolbar { flex-direction: column; align-items: stretch; }
    .report-toolbar #printReport { width: 100%; justify-content: center; }
  }

  /* ---------- Rooms availability page (multi-room day grid) ---------- */
  .rooms-stats-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 1rem;
  }
  @media (max-width: 640px) {
    .rooms-stats-grid { grid-template-columns: 1fr; }
  }
  .rooms-stat .pc-card-body { padding: 1rem 1.1rem; }
  .rooms-stat-icon {
    width: 2.75rem; height: 2.75rem;
    border-radius: .65rem;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
  }
  .rooms-stat-icon .material-icons { font-size: 1.5rem; }
  .rooms-stat-value {
    font-size: 1.65rem; font-weight: 800; line-height: 1.05;
    font-variant-numeric: tabular-nums;
  }
  .rooms-stat-label {
    font-size: .68rem; color: #6b7280;
    text-transform: uppercase; letter-spacing: .06em;
    font-weight: 600; margin-top: .15rem;
  }
  .rooms-stat--total .rooms-stat-icon {
    background: rgba(127, 29, 29, .08);
    color: #7f1d1d;
    box-shadow: inset 0 0 0 1px rgba(127, 29, 29, .18);
  }
  .rooms-stat--total .rooms-stat-value { color: #7f1d1d; }
  .rooms-stat--avail .rooms-stat-icon {
    background: rgba(5, 150, 105, .1);
    color: #047857;
    box-shadow: inset 0 0 0 1px rgba(5, 150, 105, .25);
  }
  .rooms-stat--avail .rooms-stat-value { color: #047857; }
  .rooms-stat--occupied .rooms-stat-icon {
    background: rgba(220, 38, 38, .1);
    color: #b91c1c;
    box-shadow: inset 0 0 0 1px rgba(220, 38, 38, .22);
  }
  .rooms-stat--occupied .rooms-stat-value { color: #b91c1c; }

  /* Legend card */
  .rooms-legend-card {
    background: #fff;
    border-radius: .75rem;
    padding: .9rem 1.1rem 1rem;
    margin-bottom: 1rem;
    box-shadow: 0 1px 2px rgba(15, 23, 42, .04), 0 4px 14px -8px rgba(15, 23, 42, .12);
    border: 1px solid #f3f4f6;
  }
  .rooms-legend-title {
    display: inline-flex; align-items: center; gap: .35rem;
    font-size: .72rem; font-weight: 700;
    color: #7f1d1d;
    text-transform: uppercase; letter-spacing: .08em;
    margin-bottom: .65rem;
  }
  .rooms-legend-title .material-icons { font-size: .95rem; }
  .rooms-legend-list {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: .75rem 1.25rem;
  }
  @media (max-width: 768px) {
    .rooms-legend-list { grid-template-columns: 1fr; }
  }
  .rooms-legend-row {
    display: flex; align-items: flex-start; gap: .55rem;
    padding: .35rem 0;
  }
  .rooms-legend-swatch {
    display: inline-block;
    width: 1rem; height: 1rem;
    border-radius: .3rem;
    flex-shrink: 0;
    margin-top: .15rem;
  }
  .rooms-legend-swatch.is-free     { background: #ecfdf5; box-shadow: inset 0 0 0 1px #6ee7b7; }
  .rooms-legend-swatch.is-occupied { background: #fef2f2; box-shadow: inset 0 0 0 1px #fca5a5; }
  .rooms-legend-swatch.is-now      { background: #fffbeb; box-shadow: inset 0 0 0 1px #fcd34d; }
  .rooms-legend-text {
    display: flex; flex-direction: column; gap: .1rem;
    line-height: 1.3;
  }
  .rooms-legend-name {
    font-size: .82rem; font-weight: 700; color: #111827;
  }
  .rooms-legend-desc {
    font-size: .72rem; color: #6b7280;
    line-height: 1.4;
  }

  /* Availability grid cells */
  .rooms-avail-grid thead th .material-icons {
    font-size: .9rem; vertical-align: middle; margin-right: .15rem;
    opacity: .85;
  }
  .rooms-avail-grid tbody td.rooms-free {
    background: linear-gradient(180deg, #f0fdf4, #ecfdf5);
    text-align: center;
    height: 2.4rem;
  }
  .rooms-avail-grid tbody td.rooms-occupied {
    background: linear-gradient(180deg, #fff5f5, #fee2e2);
    padding: .35rem;
  }
  .rooms-avail-grid tbody td.rooms-occupied .pc-slot {
    --pc-slot-accent: #b91c1c;
    background: #fff;
  }
  .rooms-avail-grid tbody tr.is-now td.time-col {
    background: linear-gradient(180deg, #fffbeb, #fde68a);
    color: #78350f;
  }
  .rooms-avail-grid tbody tr.is-now td:not(.time-col):not(.has-slot) {
    background: linear-gradient(180deg, #fffbeb, #fef3c7);
  }

  /* Status tags */
  .rooms-status-tag {
    display: inline-flex; align-items: center; gap: .15rem;
    padding: .12rem .5rem;
    border-radius: 9999px;
    font-size: .62rem; font-weight: 700;
    letter-spacing: .04em; text-transform: uppercase;
  }
  .rooms-status-tag .material-icons { font-size: .8rem; }
  .rooms-status-tag.is-free {
    background: #ecfdf5; color: #047857;
    box-shadow: inset 0 0 0 1px rgba(5, 150, 105, .25);
  }
  .rooms-status-tag.is-occupied {
    background: #fef2f2; color: #b91c1c;
    box-shadow: inset 0 0 0 1px rgba(220, 38, 38, .22);
    margin-top: .35rem;
  }
  .rooms-now-tag {
    display: inline-block; margin-top: .2rem;
    padding: .05rem .4rem;
    border-radius: 9999px;
    background: #f59e0b; color: #fff;
    font-size: .55rem; font-weight: 800;
    letter-spacing: .1em;
  }

  /* ---------- Room timetable (per-room weekly grid) ---------- */
  .pc-room-grid {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: .82rem;
    background: #fff;
    border-radius: .65rem;
    overflow: hidden;
    box-shadow: 0 1px 2px rgba(15, 23, 42, .04), 0 8px 24px -12px rgba(15, 23, 42, .12);
  }
  .pc-room-grid thead th {
    background: linear-gradient(180deg, #7f1d1d 0%, #991b1b 60%, #7f1d1d 100%);
    color: #fff;
    padding: .75rem .55rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    font-size: .7rem;
    text-align: center;
    text-shadow: 0 1px 1px rgba(0, 0, 0, .25);
    border-bottom: 2px solid rgba(255, 215, 0, .35);
    position: sticky; top: 0; z-index: 5;
  }
  .pc-room-grid thead th:first-child {
    left: 0; z-index: 6;
    border-right: 1px solid rgba(255, 255, 255, .12);
    width: 7rem;
  }
  .pc-room-grid thead th:not(:last-child) {
    border-right: 1px solid rgba(255, 255, 255, .08);
  }
  .pc-room-grid tbody td {
    padding: .35rem .4rem;
    border-bottom: 1px solid #f1f3f5;
    border-right: 1px solid #f1f3f5;
    vertical-align: top;
    background: #fff;
    height: 2.4rem;
  }
  .pc-room-grid tbody tr:last-child td { border-bottom: none; }
  .pc-room-grid tbody td:last-child { border-right: none; }

  /* Sticky time column */
  .pc-room-grid tbody td.time-col {
    background: linear-gradient(180deg, #fef2f2, #fee2e2);
    color: #7f1d1d;
    font-weight: 700;
    text-align: center;
    font-size: .7rem;
    letter-spacing: .02em;
    white-space: nowrap;
    position: sticky; left: 0; z-index: 2;
    border-right: 2px solid rgba(127, 29, 29, .18);
    width: 7rem;
  }

  /* Hour boundary — every other row gets a stronger border */
  .pc-room-grid tbody tr.hour-mark td { border-bottom: 1px solid #e5e7eb; }

  /* Lunch row */
  .pc-room-grid tbody tr.is-lunch td:not(.time-col):not(.has-slot) {
    background-color: #fafaf9;
    background-image: repeating-linear-gradient(
      45deg,
      transparent 0,
      transparent 6px,
      rgba(180, 83, 9, .06) 6px,
      rgba(180, 83, 9, .06) 12px
    );
  }
  .pc-room-grid tbody tr.is-lunch td.time-col {
    background: linear-gradient(180deg, #fffbeb, #fef3c7);
    color: #92400e;
  }
  .pc-room-grid tbody tr.is-lunch td.time-col::after {
    content: 'LUNCH';
    display: block;
    font-size: .55rem;
    color: #b45309;
    letter-spacing: .12em;
    margin-top: 1px;
  }

  /* Empty cell — subtle dot pattern */
  .pc-room-grid tbody td.is-empty {
    background-image: radial-gradient(circle at 1px 1px, rgba(15, 23, 42, .055) 1px, transparent 0);
    background-size: 12px 12px;
  }

  /* Occupied slot card */
  .pc-slot {
    --pc-slot-accent: #374151;
    background: #f3f4f6;
    border-radius: .5rem;
    padding: .5rem .55rem .5rem .85rem;
    display: flex; flex-direction: column; gap: .1rem;
    height: 100%;
    position: relative;
    overflow: hidden;
    box-shadow: inset 0 0 0 1px rgba(15, 23, 42, .06), 0 1px 3px rgba(15, 23, 42, .08);
    transition: transform .15s ease, box-shadow .15s ease;
  }
  .pc-slot::before {
    content: '';
    position: absolute; left: 0; top: 0; bottom: 0; width: 4px;
    background: var(--pc-slot-accent);
    border-radius: 4px 0 0 4px;
  }
  .pc-slot:hover {
    transform: translateY(-1px);
    box-shadow: inset 0 0 0 1px rgba(15, 23, 42, .1), 0 4px 10px -4px rgba(15, 23, 42, .25);
  }
  .pc-slot .pc-slot-subject {
    font-weight: 800;
    font-size: .82rem;
    line-height: 1.15;
    color: #111827;
    letter-spacing: .01em;
    word-break: break-word;
  }
  .pc-slot .pc-slot-faculty {
    font-size: .7rem;
    color: #4b5563;
    line-height: 1.25;
    text-transform: capitalize;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .pc-slot .pc-slot-time {
    font-size: .6rem;
    color: #6b7280;
    margin-top: .15rem;
    letter-spacing: .03em;
    font-variant-numeric: tabular-nums;
  }
  .pc-slot .pc-slot-prog {
    align-self: flex-start;
    margin-top: .2rem;
    padding: .08rem .45rem;
    border-radius: 9999px;
    font-size: .58rem;
    font-weight: 800;
    letter-spacing: .08em;
    color: var(--pc-slot-accent);
    background: rgba(255, 255, 255, .85);
    box-shadow: inset 0 0 0 1px rgba(15, 23, 42, .06);
  }
  .pc-slot .pc-slot-edit {
    align-self: flex-start;
    margin-top: .35rem;
    display: inline-flex; align-items: center; gap: .2rem;
    padding: .15rem .55rem;
    border-radius: .35rem;
    font-size: .65rem; font-weight: 700;
    background: var(--pc-slot-accent);
    color: #fff;
    border: none; cursor: pointer;
    box-shadow: 0 2px 6px -2px rgba(0, 0, 0, .3);
    transition: filter .15s, transform .15s;
  }
  .pc-slot .pc-slot-edit:hover { filter: brightness(1.1); transform: translateY(-1px); }
  .pc-slot .pc-slot-edit .material-icons { font-size: .85rem; }

  /* Program palettes */
  .pc-slot.prog-bsce  { background: #fef2f2; --pc-slot-accent: #b91c1c; }
  .pc-slot.prog-bscoe { background: #eff6ff; --pc-slot-accent: #1d4ed8; }
  .pc-slot.prog-bsee  { background: #fffbeb; --pc-slot-accent: #b45309; }
  .pc-slot.prog-bsece { background: #f5f3ff; --pc-slot-accent: #6d28d9; }
  .pc-slot.prog-bsie  { background: #ecfdf5; --pc-slot-accent: #047857; }
  .pc-slot.prog-bsme  { background: #eef2ff; --pc-slot-accent: #4338ca; }
  .pc-slot.prog-other { background: #f3f4f6; --pc-slot-accent: #374151; }

  /* Legend chip */
  .pc-prog-legend {
    display: flex; flex-wrap: wrap; gap: .4rem;
    padding: .65rem .75rem;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: .55rem;
  }
  .pc-prog-legend-item {
    display: inline-flex; align-items: center; gap: .35rem;
    font-size: .7rem; font-weight: 600; color: #374151;
    padding: .15rem .55rem;
    border-radius: 9999px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
  }
  .pc-prog-legend-item .dot {
    width: .55rem; height: .55rem; border-radius: 9999px;
    background: var(--pc-slot-accent, #374151);
  }

  /* Compact on phones */
  @media (max-width: 640px) {
    .pc-room-grid thead th { font-size: .62rem; padding: .55rem .35rem; letter-spacing: .04em; }
    .pc-room-grid tbody td { padding: .25rem .3rem; }
    .pc-room-grid tbody td.time-col { font-size: .62rem; width: 5.5rem; }
    .pc-slot { padding: .4rem .45rem .4rem .7rem; }
    .pc-slot .pc-slot-subject { font-size: .75rem; }
    .pc-slot .pc-slot-faculty { font-size: .65rem; }
    .pc-slot .pc-slot-time { font-size: .55rem; }
    .pc-slot .pc-slot-prog { font-size: .52rem; padding: .06rem .35rem; }
  }

  /* ---------- Sidebar ---------- */
  #sidebar.pc-sidebar {
    position: relative;
    background:
      radial-gradient(circle at top right, rgba(255, 200, 200, .08), transparent 55%),
      radial-gradient(circle at bottom left, rgba(0, 0, 0, .35), transparent 60%),
      linear-gradient(180deg, #5b0f0f 0%, #7f1d1d 45%, #5b0f0f 100%);
    box-shadow:
      6px 0 28px -12px rgba(0, 0, 0, .45),
      inset -1px 0 0 rgba(255, 255, 255, .06);
    border-right: 1px solid rgba(0, 0, 0, .25);
  }
  #sidebar.pc-sidebar::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,.045) 1px, transparent 0);
    background-size: 18px 18px;
    pointer-events: none;
    opacity: .55;
    border-radius: inherit;
  }
  #sidebar.pc-sidebar::after {
    content: '';
    position: absolute; top: 0; right: 0; width: 1px; height: 100%;
    background: linear-gradient(180deg, transparent, rgba(255, 215, 0, .25), transparent);
    pointer-events: none;
  }
  #sidebar.pc-sidebar > * { position: relative; z-index: 1; }

  /* Sidebar profile card */
  .pc-sidebar > div:first-child {
    background: linear-gradient(135deg, rgba(255, 255, 255, .12), rgba(255, 255, 255, .03)) !important;
    border: 1px solid rgba(255, 215, 0, .22) !important;
    box-shadow:
      0 10px 28px -14px rgba(0, 0, 0, .55),
      inset 0 1px 0 rgba(255, 255, 255, .12) !important;
    position: relative;
  }
  .pc-sidebar > div:first-child::after {
    content: '';
    position: absolute; left: 1rem; right: 1rem; bottom: -1px; height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255, 215, 0, .55), transparent);
  }
  .pc-sidebar > div:first-child img {
    border: 2px solid rgba(255, 215, 0, .55) !important;
    box-shadow:
      0 6px 16px rgba(0, 0, 0, .35),
      0 0 0 4px rgba(255, 255, 255, .06) !important;
  }
  .pc-sidebar > div:first-child h1 {
    position: relative;
    margin-top: .35rem;
    font-size: 1.15rem !important;
    font-weight: 800 !important;
    letter-spacing: .14em !important;
    text-transform: uppercase;
    background: linear-gradient(135deg, #fff8dc 0%, #FFD700 50%, #f59e0b 100%);
    -webkit-background-clip: text; background-clip: text;
    -webkit-text-fill-color: transparent;
    filter: drop-shadow(0 1px 2px rgba(0, 0, 0, .35));
    display: inline-flex; align-items: center; gap: .55rem;
  }
  .pc-sidebar > div:first-child h1::before,
  .pc-sidebar > div:first-child h1::after {
    content: '';
    width: 1.1rem; height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255, 215, 0, .7));
    -webkit-text-fill-color: initial;
  }
  .pc-sidebar > div:first-child h1::after {
    background: linear-gradient(90deg, rgba(255, 215, 0, .7), transparent);
  }
  .pc-sidebar > div:first-child p {
    display: inline-flex !important;
    align-items: center;
    gap: .35rem;
    margin-top: .55rem !important;
    padding: .2rem .7rem;
    width: auto !important;
    max-width: 100%;
    background: rgba(255, 215, 0, .12);
    border: 1px solid rgba(255, 215, 0, .25);
    border-radius: 9999px;
    color: #ffe9b0 !important;
    font-size: .68rem !important;
    font-weight: 600;
    letter-spacing: .08em;
    text-transform: uppercase;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, .06);
  }
  .pc-sidebar > div:first-child p::before {
    content: '';
    width: .35rem; height: .35rem;
    border-radius: 9999px;
    background: #34d399;
    box-shadow: 0 0 6px rgba(52, 211, 153, .8);
    flex-shrink: 0;
  }

  /* Nav items */
  .pc-sidebar .nav-link,
  .pc-sidebar #accountsDropdown a,
  .pc-sidebar #toggleAccounts {
    position: relative;
    transition: all .2s ease;
    border-radius: .6rem;
    color: rgba(255, 255, 255, .82);
    font-weight: 500;
    font-size: .92rem;
    letter-spacing: .01em;
    border: 1px solid transparent;
    overflow: hidden;
  }
  .pc-sidebar .nav-link .material-icons,
  .pc-sidebar #toggleAccounts .material-icons {
    color: rgba(255, 255, 255, .72);
    transition: transform .2s ease, color .2s ease;
  }
  .pc-sidebar .nav-link:hover,
  .pc-sidebar #toggleAccounts:hover {
    background: linear-gradient(90deg, rgba(255, 255, 255, .14), rgba(255, 255, 255, .03)) !important;
    color: #fff !important;
    transform: translateX(3px);
    border-color: rgba(255, 255, 255, .08);
  }
  .pc-sidebar .nav-link:hover .material-icons,
  .pc-sidebar #toggleAccounts:hover .material-icons {
    color: #FFD700;
    transform: scale(1.1);
  }

  /* Accounts dropdown rail */
  .pc-sidebar #accountsDropdown {
    margin-left: 1.5rem !important;
    padding-left: .65rem;
    border-left: 1px dashed rgba(255, 215, 0, .25);
  }
  .pc-sidebar #accountsDropdown a {
    color: rgba(255, 255, 255, .7);
    font-size: .85rem;
  }
  .pc-sidebar #accountsDropdown a:hover {
    background: rgba(255, 255, 255, .08) !important;
    color: #fff !important;
    transform: translateX(2px);
  }
  .pc-sidebar #accountsDropdown a:hover .material-icons {
    color: #FFD700;
  }

  /* Active state */
  .pc-sidebar .nav-link.pc-active,
  .pc-sidebar #accountsDropdown a.pc-active {
    background: linear-gradient(90deg, rgba(255, 215, 0, .20), rgba(255, 215, 0, .04)) !important;
    color: #FFD700 !important;
    font-weight: 600;
    box-shadow:
      inset 0 1px 0 rgba(255, 255, 255, .08),
      0 6px 16px -8px rgba(0, 0, 0, .5);
    border-color: rgba(255, 215, 0, .25);
  }
  .pc-sidebar .nav-link.pc-active .material-icons,
  .pc-sidebar #accountsDropdown a.pc-active .material-icons {
    color: #FFD700;
  }
  .pc-sidebar .nav-link.pc-active::before,
  .pc-sidebar #accountsDropdown a.pc-active::before {
    content: '';
    position: absolute; left: 0; top: 18%; height: 64%; width: 3px;
    background: linear-gradient(180deg, #FFD700, #f59e0b);
    border-radius: 0 4px 4px 0;
    box-shadow: 0 0 10px rgba(255, 215, 0, .55);
  }

  /* Logout link */
  .pc-sidebar a[href="logout"] {
    margin-top: .5rem;
    padding-top: .85rem !important;
    border-top: 1px solid rgba(255, 255, 255, .1);
    color: rgba(254, 226, 226, .85);
    border-radius: 0 0 .6rem .6rem;
  }
  .pc-sidebar a[href="logout"]:hover {
    background: linear-gradient(90deg, rgba(220, 38, 38, .28), rgba(220, 38, 38, .04)) !important;
    color: #fff !important;
    transform: translateX(3px);
  }
  .pc-sidebar a[href="logout"]:hover .material-icons {
    color: #fca5a5 !important;
  }

  /* Scrollbar inside sidebar nav */
  .pc-sidebar nav::-webkit-scrollbar { width: 5px; }
  .pc-sidebar nav::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, .18);
    border-radius: 4px;
  }
  .pc-sidebar nav::-webkit-scrollbar-thumb:hover { background: rgba(255, 215, 0, .35); }
  .pc-sidebar nav::-webkit-scrollbar-track { background: transparent; }

  /* ---------- Modals ---------- */
  .pc-modal {
    position: fixed; inset: 0; z-index: 50;
    display: none;
    align-items: center; justify-content: center;
    background: rgba(0, 0, 0, .55);
    padding: 1rem;
  }
  .pc-modal.is-open { display: flex; }
  .pc-modal-card {
    background: #fff; border-radius: .9rem;
    box-shadow: 0 25px 60px -20px rgba(0,0,0,.45);
    border: 1px solid rgba(15,23,42,.05);
    width: 100%;
  }
  .pc-modal-header {
    display: flex; align-items: center; gap: .65rem;
    padding: 1rem 1.25rem;
    background: linear-gradient(135deg, #7f1d1d, #991b1b);
    color: #fff; border-top-left-radius: .9rem; border-top-right-radius: .9rem;
  }
  .pc-modal-header.pc-modal-header-emerald {
    background: linear-gradient(135deg, #047857, #059669);
  }
  .pc-modal-header.pc-modal-header-amber {
    background: linear-gradient(135deg, #b45309, #d97706);
  }
  .pc-modal-header h2 { font-weight: 700; font-size: 1.1rem; margin: 0; }
  .pc-modal-header p { margin: 0; }
  .pc-modal-footer {
    display: flex; justify-content: flex-end; gap: .5rem;
    padding-top: .85rem; margin-top: .25rem;
    border-top: 1px solid #f3f4f6;
  }

  /* ---------- Misc ---------- */
  .pc-section-title {
    display: flex; align-items: center; gap: .5rem;
    color: var(--pc-red-900); font-weight: 700;
  }
  .pc-section-title .material-icons { color: var(--pc-red-900); }
  .pc-chip {
    display: inline-flex; align-items: center;
    padding: .25rem .65rem; border-radius: 9999px;
    font-size: .72rem; font-weight: 600;
  }
  .pc-chip-red    { background: #fee2e2; color: #991b1b; }
  .pc-chip-green  { background: #d1fae5; color: #065f46; }
  .pc-chip-amber  { background: #fef3c7; color: #92400e; }
  .pc-chip-gray   { background: #f3f4f6; color: #374151; }
  .pc-chip-blue   { background: #dbeafe; color: #1e40af; }

  .pc-divider {
    height: 1px; background: linear-gradient(90deg, transparent, rgba(15,23,42,.1), transparent);
    margin: 1rem 0;
  }

  .pc-empty {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    padding: 3rem 1.5rem; color: #6b7280; text-align: center;
  }
  /* Tailwind's `hidden` utility must win over `pc-empty`'s `display: flex` */
  .pc-empty.hidden { display: none; }
  .pc-empty .material-icons {
    font-size: 3rem; color: #d1d5db; margin-bottom: .5rem;
  }
  .pc-empty h3 { font-weight: 700; color: #374151; margin: 0 0 .25rem; }
  .pc-empty p { font-size: .85rem; margin: 0; max-width: 22rem; }

  /* Tabs */
  .pc-tabs { display: flex; flex-wrap: wrap; gap: .4rem; }
  .pc-tab {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .5rem .9rem; border-radius: .5rem;
    background: #fff; color: #374151;
    border: 1px solid #e5e7eb; font-weight: 600; font-size: .85rem;
    cursor: pointer; transition: all .15s ease;
  }
  .pc-tab:hover { background: #fef2f2; color: var(--pc-red-900); border-color: #fecaca; }
  .pc-tab.is-active {
    background: linear-gradient(135deg, #7f1d1d, #991b1b);
    color: #fff; border-color: transparent;
    box-shadow: 0 4px 12px -4px rgba(127, 29, 29, .55);
  }
  .pc-tab .material-icons { font-size: 1rem; }

  /* Search input wrap */
  .pc-search-wrap { position: relative; }
  .pc-search-wrap .material-icons {
    position: absolute; left: .65rem; top: 50%; transform: translateY(-50%);
    color: #9ca3af; font-size: 1.05rem; pointer-events: none;
  }
  .pc-search-wrap .pc-input { padding-left: 2.1rem; }

  /* ---------- Room picker (chip input) ---------- */
  .pc-room-picker {
    display: flex; flex-wrap: wrap; align-items: center; gap: .35rem;
    width: 100%; padding: .45rem .55rem; min-height: 2.65rem;
    border: 1px solid #e5e7eb; border-radius: .55rem; background: #fff;
    transition: border-color .15s, box-shadow .15s;
  }
  .pc-room-picker:focus-within {
    border-color: #b91c1c;
    box-shadow: 0 0 0 3px rgba(185, 28, 28, .12);
  }
  .pc-room-chip {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .22rem .25rem .22rem .6rem;
    border-radius: 9999px;
    background: linear-gradient(135deg, #fef2f2, #fee2e2);
    color: #7f1d1d; font-size: .8rem; font-weight: 700;
    border: 1px solid #fecaca;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, .6);
  }
  .pc-room-chip .material-icons {
    font-size: .95rem; color: #b91c1c;
  }
  .pc-room-chip button {
    display: inline-flex; align-items: center; justify-content: center;
    width: 1.1rem; height: 1.1rem;
    padding: 0; margin-left: .15rem;
    border: none; background: rgba(127, 29, 29, .12); color: #7f1d1d;
    border-radius: 9999px; cursor: pointer; line-height: 1;
    transition: background .15s, color .15s;
  }
  .pc-room-chip button:hover { background: #b91c1c; color: #fff; }
  .pc-room-chip button .material-icons { font-size: .8rem; color: inherit; }
  .pc-room-input {
    flex: 1 1 8rem; min-width: 8rem;
    border: none; outline: none; background: transparent;
    padding: .25rem .35rem; font-size: .88rem; color: #1f2937;
  }
  .pc-room-input::placeholder { color: #9ca3af; }

  .pc-room-suggest {
    display: flex; flex-wrap: wrap; gap: .3rem;
    margin-top: .55rem; align-items: center;
  }
  .pc-room-suggest-label {
    font-size: .65rem; color: #6b7280;
    text-transform: uppercase; letter-spacing: .06em; font-weight: 600;
    margin-right: .15rem;
  }
  .pc-quick-room {
    font-size: .72rem; padding: .25rem .6rem;
    border-radius: 9999px;
    background: #fff; color: #4b5563;
    border: 1px dashed #d1d5db; cursor: pointer;
    transition: all .15s ease;
    display: inline-flex; align-items: center; gap: .25rem;
  }
  .pc-quick-room::before {
    content: '+'; font-weight: 700; color: #9ca3af;
  }
  .pc-quick-room:hover {
    background: #fef2f2; border-color: #fecaca; color: #7f1d1d;
    border-style: solid;
  }
  .pc-quick-room:hover::before { color: #b91c1c; }
  .pc-quick-room.is-added {
    background: #f3f4f6; color: #9ca3af; border-style: solid;
    cursor: not-allowed; opacity: .6;
  }
  .pc-quick-room.is-added::before { content: '✓'; color: #6b7280; }

  /* Auth (login/register) shell */
  .pc-auth-shell {
    min-height: 100vh; display: flex; align-items: center; justify-content: center;
    padding: 2rem 1rem;
    background:
      radial-gradient(circle at 15% 20%, rgba(254, 226, 226, .35), transparent 45%),
      radial-gradient(circle at 85% 80%, rgba(0, 0, 0, .35), transparent 50%),
      linear-gradient(135deg, #6b1414 0%, #7f1d1d 50%, #991b1b 100%);
  }
  .pc-auth-card {
    width: 100%; max-width: 28rem;
    background: #fff; border-radius: 1rem;
    box-shadow: 0 25px 60px -15px rgba(0, 0, 0, .55), 0 4px 12px rgba(0, 0, 0, .15);
    overflow: hidden;
  }
  .pc-auth-card.pc-auth-card-wide { max-width: 36rem; }
  .pc-auth-header {
    background: linear-gradient(135deg, #7f1d1d, #991b1b);
    color: #fff; padding: 1.5rem 1.75rem; text-align: center;
  }
  .pc-auth-header img {
    width: 4.5rem; height: 4.5rem; border-radius: 50%;
    border: 2px solid rgba(255,255,255,.6); margin: 0 auto .65rem;
    object-fit: cover;
  }
  .pc-auth-header h1 {
    font-size: 1.4rem; font-weight: 700; letter-spacing: .02em; margin: 0;
  }
  .pc-auth-header p {
    font-size: .8rem; color: rgba(254, 226, 226, .85); margin-top: .15rem;
  }
  .pc-auth-body { padding: 1.75rem; }

  /* Tier picker (course tier radio pills) */
  .pc-tier-pill {
    display: flex; align-items: center; gap: .5rem;
    padding: .55rem .7rem; border: 1px solid #e5e7eb; border-radius: .55rem;
    background: #fff; cursor: pointer; font-size: .78rem; line-height: 1.15;
    transition: border-color .15s, background .15s, box-shadow .15s;
  }
  .pc-tier-pill:hover { border-color: #fecaca; background: #fff5f5; }
  .pc-tier-pill input[type="radio"] { accent-color: #b91c1c; }
  .pc-tier-pill input[type="radio"]:checked + span strong { color: #7f1d1d; }
  .pc-tier-pill:has(input[type="radio"]:checked) {
    border-color: #b91c1c;
    background: #fef2f2;
    box-shadow: 0 0 0 3px rgba(185,28,28,.10);
  }

  /* Utility */
  .pc-text-muted { color: #6b7280; }
  .pc-text-red   { color: var(--pc-red-900); }

  /* ---------- Toast notifications ---------- */
  .pc-toast-container {
    position: fixed; top: 1rem; right: 1rem;
    display: flex; flex-direction: column; gap: .65rem;
    z-index: 99999; max-width: calc(100vw - 2rem);
    pointer-events: none;
  }
  .pc-toast {
    pointer-events: auto;
    display: flex; align-items: flex-start; gap: .75rem;
    min-width: 18rem; max-width: 26rem;
    padding: .85rem 2.25rem .95rem 1rem;
    background: #fff; border-radius: .7rem;
    box-shadow: 0 18px 45px -12px rgba(0, 0, 0, .25), 0 4px 10px rgba(0, 0, 0, .08);
    border: 1px solid rgba(15, 23, 42, .08);
    position: relative; overflow: hidden;
    transform: translateX(120%); opacity: 0;
    transition: transform .35s cubic-bezier(.4, 0, .2, 1), opacity .35s;
  }
  .pc-toast.is-shown { transform: translateX(0); opacity: 1; }
  .pc-toast.is-leaving { transform: translateX(120%); opacity: 0; }

  .pc-toast-icon {
    width: 2.5rem; height: 2.5rem; flex-shrink: 0;
    border-radius: .55rem;
    display: inline-flex; align-items: center; justify-content: center;
  }
  .pc-toast-icon .material-icons { font-size: 1.45rem; }

  .pc-toast-body { flex: 1; min-width: 0; }
  .pc-toast-title {
    font-weight: 700; font-size: .92rem; color: #111827;
    line-height: 1.2; letter-spacing: .01em;
  }
  .pc-toast-msg {
    font-size: .82rem; color: #4b5563;
    margin-top: .2rem; line-height: 1.4;
    word-wrap: break-word;
  }

  .pc-toast-close {
    position: absolute; top: .4rem; right: .4rem;
    width: 1.5rem; height: 1.5rem; border-radius: 9999px;
    display: inline-flex; align-items: center; justify-content: center;
    border: none; background: transparent; color: #9ca3af; cursor: pointer;
    transition: background .15s, color .15s;
  }
  .pc-toast-close:hover { background: #f3f4f6; color: #374151; }
  .pc-toast-close .material-icons { font-size: 1rem; }

  .pc-toast-progress {
    position: absolute; left: 0; right: 0; bottom: 0; height: 3px;
    transform-origin: left center;
    animation: pc-toast-progress 4.5s linear forwards;
  }

  /* Variants */
  .pc-toast-success { border-left: 4px solid #059669; }
  .pc-toast-success .pc-toast-icon { background: #d1fae5; color: #047857; }
  .pc-toast-success .pc-toast-progress { background: linear-gradient(90deg, #047857, #34d399); }

  .pc-toast-error { border-left: 4px solid #dc2626; }
  .pc-toast-error .pc-toast-icon { background: #fee2e2; color: #b91c1c; }
  .pc-toast-error .pc-toast-progress { background: linear-gradient(90deg, #b91c1c, #f87171); }

  .pc-toast-warning { border-left: 4px solid #d97706; }
  .pc-toast-warning .pc-toast-icon { background: #fef3c7; color: #b45309; }
  .pc-toast-warning .pc-toast-progress { background: linear-gradient(90deg, #b45309, #fbbf24); }

  .pc-toast-info { border-left: 4px solid #4f46e5; }
  .pc-toast-info .pc-toast-icon { background: #e0e7ff; color: #4338ca; }
  .pc-toast-info .pc-toast-progress { background: linear-gradient(90deg, #4338ca, #818cf8); }

  @keyframes pc-toast-progress {
    from { transform: scaleX(1); }
    to   { transform: scaleX(0); }
  }

  @media (max-width: 480px) {
    .pc-toast-container { left: 1rem; right: 1rem; max-width: none; }
    .pc-toast { min-width: 0; max-width: none; }
  }

  /* ========================================================
     Mobile responsiveness
     ======================================================== */

  /* Mobile menu button (in role nav.php files).
     The original Tailwind classes (text-white bg-red-800/20) render almost
     invisibly on the light gray main area. Force a visible solid style. */
  #menuButton {
    background: linear-gradient(135deg, #7f1d1d, #991b1b) !important;
    color: #fff !important;
    box-shadow: 0 4px 10px -3px rgba(127, 29, 29, .55);
    border-radius: .55rem;
  }
  #menuButton:hover {
    background: linear-gradient(135deg, #991b1b, #b91c1c) !important;
  }
  #menuButton .material-icons { color: #fff; }

  /* Prevent body horizontal scrolling on mobile while still allowing
     designated wrappers (tables, etc.) to scroll horizontally. */
  html, body { max-width: 100%; overflow-x: hidden; }

  /* Tablets and phones */
  @media (max-width: 1024px) {
    /* Sidebar nav scroll area: original "calc(100vh - 120px)" leaves
       short heights with awkward dead space and can clip the logout link
       on small phones. Switch to flex sizing in mobile drawer mode.
       Also force `position: fixed` here — the base `#sidebar.pc-sidebar`
       rule sets `position: relative` (needed by ::before/::after on
       desktop), and its id+class specificity beats Tailwind's `.fixed`
       utility. Without this override the sidebar stays in flex flow on
       mobile, and `-translate-x-full` only hides it visually while it
       still reserves its full height at the top of the page. */
    #sidebar.pc-sidebar { padding: 1.25rem; position: fixed; }
    #sidebar.pc-sidebar > nav {
      height: auto !important;
      max-height: calc(100vh - 180px);
      padding-bottom: 1.5rem;
    }

    /* Main content area uses Tailwind `flex-1 p-8 overflow-auto h-screen`
       which on mobile creates a nested scroll container inside the page
       (main scrolls instead of body) and combined with the inner
       `<div class="min-h-screen">` forces 200vh of content. Reset all of
       it on mobile so content flows naturally and body scrolls. */
    main.flex-1 {
      padding: 1rem !important;
      height: auto !important;
      min-height: 0 !important;
      overflow: visible !important;
      flex: 1 1 auto !important;
      width: 100% !important;
    }
    /* Inner min-h-screen wrapper opened in role nav.php files —
       neutralize so it doesn't add a redundant 100vh on mobile. */
    main.flex-1 > .min-h-screen { min-height: 0 !important; }
  }

  @media (max-width: 768px) {
    /* Topbar: tighter padding, smaller text */
    .pc-topbar { border-radius: .65rem; margin-bottom: 1rem; }
    .pc-topbar-inner { padding: .85rem 1rem; gap: .75rem; }
    .pc-topbar-icon { width: 2.2rem; height: 2.2rem; border-radius: .5rem; }
    .pc-topbar-icon .material-icons { font-size: 1.35rem; }
    .pc-topbar-title h2 { font-size: 1.05rem; }
    .pc-topbar-title p { font-size: .7rem; }
    .pc-topbar-meta { gap: .5rem; }
    .pc-avatar { width: 2.2rem; height: 2.2rem; font-size: .9rem; }

    /* Cards */
    .pc-card { border-radius: .65rem; }
    .pc-card-header { padding: .8rem 1rem; }
    .pc-card-title { font-size: .95rem; }
    .pc-card-body { padding: 1rem; }

    /* Stat cards: more compact */
    .pc-stat { padding: .9rem 1rem; gap: .75rem; border-radius: .65rem; }
    .pc-stat-icon { width: 2.6rem; height: 2.6rem; border-radius: .55rem; }
    .pc-stat-icon .material-icons { font-size: 1.5rem; }
    .pc-stat .pc-stat-label { font-size: .7rem; }
    .pc-stat .pc-stat-value { font-size: 1.5rem; }

    /* Tables: allow horizontal scroll for wrappers and shrink cell padding */
    .pc-table thead th { padding: .65rem .6rem; font-size: .68rem; letter-spacing: .04em; }
    .pc-table tbody td { padding: .6rem .6rem; font-size: .82rem; }
    .pc-week-table thead th { padding: .55rem .35rem; font-size: .65rem; }
    .pc-week-table tbody td { padding: .35rem .35rem; font-size: .78rem; }

    /* Modals: full bleed with safe gutter */
    .pc-modal { padding: .5rem; }
    .pc-modal-card { border-radius: .7rem; max-height: calc(100vh - 1rem); }
    .pc-modal-header { padding: .85rem 1rem; border-top-left-radius: .7rem; border-top-right-radius: .7rem; }
    .pc-modal-header h2 { font-size: 1rem; }
    .pc-modal-footer { flex-wrap: wrap; }

    /* Buttons: allow wrapping rather than overflow */
    .pc-btn { white-space: normal; }

    /* Section title & tabs scroll when too many */
    .pc-tabs { flex-wrap: nowrap; overflow-x: auto; padding-bottom: .25rem; }
    .pc-tabs::-webkit-scrollbar { height: 4px; }
    .pc-tabs::-webkit-scrollbar-thumb { background: rgba(127, 29, 29, .3); border-radius: 2px; }
    .pc-tab { flex-shrink: 0; }

    /* Auth */
    .pc-auth-shell { padding: 1rem .75rem; }
    .pc-auth-header { padding: 1.1rem 1rem; }
    .pc-auth-header img { width: 3.5rem; height: 3.5rem; }
    .pc-auth-header h1 { font-size: 1.15rem; }
    .pc-auth-body { padding: 1.25rem; }
  }

  @media (max-width: 480px) {
    .pc-topbar-welcome { display: none !important; }
    .pc-topbar-inner { padding: .75rem .85rem; }
    .pc-stat .pc-stat-value { font-size: 1.3rem; }
    .pc-card-body { padding: .85rem; }
    .pc-card-header { flex-wrap: wrap; }
    .pc-search-wrap { width: 100%; }

    /* Modal action buttons stack on tiny screens */
    .pc-modal-footer .pc-btn,
    .pc-modal-card form > div:last-child .pc-btn { width: 100%; justify-content: center; }
    .pc-modal-footer { flex-direction: column-reverse; align-items: stretch; }
  }

  /* Helper: wrap any plain pc-table that isn't already in a scrollable
     parent. Applied at runtime via the small script below. */
  .pc-table-scroll {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    width: 100%;
  }
  .pc-table-scroll > .pc-table { min-width: 640px; }
</style>

<script>
/* Auto-wrap any .pc-table that isn't already inside a scrollable container.
   Many tables are dynamically rendered, so observe the DOM. */
(function () {
  if (window.__pcTableWrapReady) return;
  window.__pcTableWrapReady = true;

  function isScrollable(el) {
    if (!el) return false;
    const style = getComputedStyle(el);
    const ox = style.overflowX;
    if (ox === 'auto' || ox === 'scroll') return true;
    return el.classList && el.classList.contains('pc-table-scroll');
  }

  function wrap(table) {
    if (!table || table.__pcWrapped) return;
    table.__pcWrapped = true;
    const parent = table.parentElement;
    if (!parent) return;
    if (isScrollable(parent)) return;
    const wrap = document.createElement('div');
    wrap.className = 'pc-table-scroll';
    parent.insertBefore(wrap, table);
    wrap.appendChild(table);
  }

  function scan(root) {
    (root || document).querySelectorAll('table.pc-table').forEach(wrap);
  }

  document.addEventListener('DOMContentLoaded', () => {
    scan(document);
    const obs = new MutationObserver(muts => {
      muts.forEach(m => {
        m.addedNodes && m.addedNodes.forEach(n => {
          if (!(n instanceof Element)) return;
          if (n.matches && n.matches('table.pc-table')) wrap(n);
          else scan(n);
        });
      });
    });
    obs.observe(document.body, { childList: true, subtree: true });
  });
})();
</script>

<script>
(function () {
  if (window.__pcToastReady) return;
  window.__pcToastReady = true;

  const ICONS  = { success: 'check_circle', error: 'error', warning: 'warning', info: 'info' };
  const TITLES = { success: 'Success',      error: 'Oops!',  warning: 'Heads up', info: 'Notice' };

  function detectType(msg) {
    const m = String(msg || '').toLowerCase();
    if (/\b(error|fail|failed|invalid|cannot|must|denied|conflict|wrong|exists|already)\b/.test(m)) return 'error';
    if (/\b(warn|warning|please|note)\b/.test(m)) return 'warning';
    if (/\b(success|successful|updated|saved|created|approved|added|deleted|removed|done|generated|enabled|disabled|sent|completed)\b/.test(m)) return 'success';
    return 'info';
  }

  function ensureContainer() {
    let c = document.getElementById('pc-toast-container');
    if (!c) {
      c = document.createElement('div');
      c.id = 'pc-toast-container';
      c.className = 'pc-toast-container';
      (document.body || document.documentElement).appendChild(c);
    }
    return c;
  }

  function escapeHtml(s) {
    return String(s).replace(/[&<>"']/g, c => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));
  }

  function dismiss(toast) {
    if (!toast || toast.__leaving) return;
    toast.__leaving = true;
    toast.classList.remove('is-shown');
    toast.classList.add('is-leaving');
    setTimeout(() => toast.remove(), 360);
  }

  window.pcToast = function (message, type, opts) {
    if (!message && message !== 0) return;
    const t = type || detectType(message);
    const o = opts || {};
    const duration = o.duration === undefined ? 4500 : o.duration;
    const title = o.title || TITLES[t] || TITLES.info;

    const container = ensureContainer();
    const toast = document.createElement('div');
    toast.className = 'pc-toast pc-toast-' + t;
    toast.innerHTML =
      '<span class="pc-toast-icon"><span class="material-icons">' + ICONS[t] + '</span></span>' +
      '<div class="pc-toast-body">' +
        '<div class="pc-toast-title">' + escapeHtml(title) + '</div>' +
        '<div class="pc-toast-msg">' + escapeHtml(message) + '</div>' +
      '</div>' +
      '<button class="pc-toast-close" type="button" aria-label="Close">' +
        '<span class="material-icons">close</span>' +
      '</button>' +
      (duration > 0 ? '<div class="pc-toast-progress" style="animation-duration:' + duration + 'ms;"></div>' : '');

    container.appendChild(toast);
    requestAnimationFrame(() => toast.classList.add('is-shown'));

    const timer = duration > 0 ? setTimeout(() => dismiss(toast), duration) : null;
    toast.querySelector('.pc-toast-close').addEventListener('click', () => {
      if (timer) clearTimeout(timer);
      dismiss(toast);
    });

    return toast;
  };

  // Shim native alert() — keep escape hatch via window.pcNativeAlert
  window.pcNativeAlert = window.alert;
  window.alert = function (msg) {
    pcToast(msg);
  };
})();
</script>
