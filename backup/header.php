<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$dashboard_url = APP_BASE_URL . (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'
    ? 'dashboard_admin.php'
    : 'dashboard.php');
$theme_class = isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'guru', 'siswa'], true)
    ? 'theme-' . $_SESSION['role']
    : 'theme-default';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPN - Sistem Informasi Pengelolaan Nilai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --sipn-primary: #1d4ed8;
            --sipn-primary-rgb: 29, 78, 216;
            --sipn-primary-dark: #1e3a8a;
            --sipn-accent: #f59e0b;
            --sipn-soft: #eef4ff;
            --sipn-border: rgba(148, 163, 184, 0.25);
            --sipn-shadow: 0 18px 35px rgba(15, 23, 42, 0.10);
            --sipn-page: linear-gradient(135deg, #f7faff 0%, #eef6ff 35%, #fffaf1 100%);
            --sipn-hero: linear-gradient(135deg, var(--sipn-primary-dark), var(--sipn-primary));
            --sipn-focus: rgba(37, 99, 235, 0.15);
        }

        body.theme-admin {
            --sipn-primary: #8a6b37;
            --sipn-primary-rgb: 138, 107, 55;
            --sipn-primary-dark: #171b22;
            --sipn-accent: #d7bd83;
            --sipn-soft: #f4efe4;
            --sipn-border: rgba(146, 119, 69, 0.2);
            --sipn-shadow: 0 18px 38px rgba(30, 29, 25, 0.09);
            --sipn-page: linear-gradient(135deg, #f7f5f0 0%, #f1eee7 55%, #faf8f3 100%);
            --sipn-hero: linear-gradient(125deg, #171b22, #252a32 65%, #51432d);
            --sipn-focus: rgba(168, 132, 69, 0.17);
        }

        body.theme-guru {
            --sipn-primary: #a34f36;
            --sipn-primary-rgb: 163, 79, 54;
            --sipn-primary-dark: #493b2d;
            --sipn-accent: #d2a45f;
            --sipn-soft: #f5ecdd;
            --sipn-border: rgba(124, 91, 58, 0.2);
            --sipn-shadow: 0 14px 30px rgba(82, 61, 40, 0.09);
            --sipn-page: linear-gradient(135deg, #f4eee2 0%, #efe5d3 50%, #f8f3e9 100%);
            --sipn-hero: linear-gradient(125deg, #493b2d, #76543b 58%, #a34f36);
            --sipn-focus: rgba(163, 79, 54, 0.16);
        }

        body.theme-siswa {
            --sipn-primary: #16827e;
            --sipn-primary-rgb: 22, 130, 126;
            --sipn-primary-dark: #115b61;
            --sipn-accent: #f0b35a;
            --sipn-soft: #e9f7f5;
            --sipn-border: rgba(53, 135, 130, 0.18);
            --sipn-shadow: 0 18px 35px rgba(20, 83, 89, 0.08);
            --sipn-page: linear-gradient(135deg, #eff9f8 0%, #e7f3f6 45%, #fff8ed 100%);
            --sipn-hero: linear-gradient(125deg, #115b61, #16827e 58%, #61aaa0);
            --sipn-focus: rgba(22, 130, 126, 0.16);
        }

        body {
            background: var(--sipn-page);
            color: #1f2937;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.55;
        }

        .navbar {
            background: linear-gradient(135deg, var(--sipn-primary-dark), var(--sipn-primary)) !important;
            box-shadow: 0 10px 28px var(--sipn-focus);
            border-bottom: 1px solid rgba(255,255,255,0.12);
        }

        .navbar-brand {
            font-size: 1.25rem;
            letter-spacing: 0.06em;
        }

        .navbar-brand,
        .nav-link,
        .navbar .btn {
            letter-spacing: 0.01em;
        }

        .navbar .nav-link {
            color: rgba(255,255,255,0.85) !important;
            font-weight: 500;
        }

        .navbar .nav-link:hover,
        .navbar .nav-link:focus {
            color: #fff !important;
        }

        .navbar .dropdown-menu {
            padding: 0.55rem;
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 14px;
            box-shadow: 0 16px 35px rgba(15, 23, 42, 0.16);
        }

        .navbar .dropdown-item {
            padding: 0.6rem 0.75rem;
            border-radius: 9px;
            font-weight: 500;
        }

        .navbar .dropdown-item:hover {
            color: var(--sipn-primary-dark);
            background: var(--sipn-soft);
        }

        .card {
            border: 1px solid var(--sipn-border);
            border-radius: 20px;
            box-shadow: var(--sipn-shadow);
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(4px);
        }

        .card:not(.dashboard-hero) {
            transition: box-shadow 0.2s ease, transform 0.2s ease;
        }

        .card-body {
            padding: 1.5rem;
        }

        .btn {
            border-radius: 12px;
            padding: 0.7rem 1.2rem;
            font-weight: 600;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--sipn-primary-dark), var(--sipn-primary));
            border: none;
            box-shadow: 0 10px 20px var(--sipn-focus);
        }

        .btn-primary:hover {
            filter: brightness(0.92);
        }

        .btn-outline-primary {
            color: var(--sipn-primary-dark);
            border-color: var(--sipn-primary);
        }

        .btn-outline-primary:hover {
            color: #fff;
            background: var(--sipn-primary-dark);
            border-color: var(--sipn-primary-dark);
        }

        .text-primary {
            color: var(--sipn-primary) !important;
        }

        .bg-primary {
            background-color: var(--sipn-primary) !important;
        }

        .bg-primary.bg-opacity-10 {
            background-color: rgba(var(--sipn-primary-rgb), 0.12) !important;
        }

        .btn-outline-primary {
            border-width: 1.5px;
            border-radius: 12px;
        }

        .btn-danger,
        .btn-outline-danger,
        .btn-success,
        .btn-warning,
        .btn-info,
        .btn-dark {
            border-radius: 10px;
            font-weight: 600;
        }

        .form-control {
            border-radius: 12px;
            border: 1px solid rgba(148, 163, 184, 0.45);
            background: rgba(255,255,255,0.7);
            box-shadow: inset 0 1px 2px rgba(15,23,42,0.03);
            min-height: 48px;
        }

        .form-control:focus {
            border-color: var(--sipn-primary);
            box-shadow: 0 0 0 0.2rem var(--sipn-focus);
        }

        .form-select {
            min-height: 48px;
            border-radius: 12px;
            border-color: rgba(148, 163, 184, 0.45);
        }

        .form-select:focus {
            border-color: var(--sipn-primary);
            box-shadow: 0 0 0 0.2rem var(--sipn-focus);
        }

        .table {
            --bs-table-bg: transparent;
            margin-bottom: 0;
        }

        .table > thead th {
            padding: 0.9rem 1rem;
            border-bottom: 0;
            background: #f1f5f9;
            color: #475569;
            font-size: 0.76rem;
            letter-spacing: 0.055em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .table > thead.table-dark th,
        .table > thead.text-bg-dark th {
            background: var(--sipn-primary-dark);
            color: #fff;
        }

        .table > tbody > tr > * {
            padding: 0.85rem 1rem;
            border-color: #eef2f7;
            vertical-align: middle;
        }

        .table-striped > tbody > tr:nth-of-type(odd) > * {
            --bs-table-bg-type: #f8fafc;
        }

        .table-hover > tbody > tr:hover > * {
            --bs-table-bg-state: var(--sipn-soft);
        }

        .page-heading {
            margin-bottom: 1.5rem;
        }

        .page-heading h1 {
            color: #0f172a;
            font-weight: 750;
            letter-spacing: -0.035em;
        }

        .page-heading p {
            color: #64748b;
            margin-bottom: 0;
        }

        .alert {
            border-radius: 14px;
        }

        .dashboard-hero {
            background: var(--sipn-hero);
            border: none;
            border-radius: 24px;
            box-shadow: 0 20px 35px var(--sipn-focus);
        }

        body.theme-admin .dashboard-hero {
            border: 1px solid rgba(215, 189, 131, 0.28);
        }

        body.theme-admin .stat-card {
            border-top: 2px solid rgba(168, 132, 69, 0.55);
        }

        .student-note {
            background: linear-gradient(115deg, #fff, var(--sipn-soft));
        }

        .student-note-icon {
            display: flex;
            width: 48px;
            height: 48px;
            flex: 0 0 48px;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: rgba(var(--sipn-primary-rgb), 0.12);
            color: var(--sipn-primary);
            font-size: 1.4rem;
        }

        .teacher-note {
            border-left: 5px solid var(--sipn-accent) !important;
            background:
                repeating-linear-gradient(0deg, rgba(111, 83, 53, 0.025) 0, rgba(111, 83, 53, 0.025) 1px, transparent 1px, transparent 31px),
                #fffaf0;
        }

        .teacher-note-label {
            color: var(--sipn-primary);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            font-size: 0.72rem;
            font-weight: 750;
            letter-spacing: 0.12em;
        }

        .teacher-note p {
            color: #493b2d;
            font-size: 1.08rem;
            font-style: italic;
        }

        body.theme-guru {
            font-family: Georgia, "Times New Roman", serif;
        }

        body.theme-guru .navbar,
        body.theme-guru .btn,
        body.theme-guru .form-control,
        body.theme-guru .form-select {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        body.theme-guru .card {
            border-radius: 14px;
            background: rgba(255, 252, 245, 0.94);
        }

        body.theme-guru .page-heading h1,
        body.theme-guru .card h2 {
            font-family: Georgia, "Times New Roman", serif;
        }

        body.theme-guru .teacher-heading {
            background: linear-gradient(125deg, #493b2d, #76543b 58%, #a34f36);
            box-shadow: 0 16px 32px rgba(82, 61, 40, 0.18);
        }

        body.theme-guru .table > thead th {
            background: #e9ddc7;
            color: #493b2d;
        }

        body.theme-siswa .stat-card {
            border-top: 3px solid var(--sipn-accent);
        }

        body.theme-siswa .dashboard-hero {
            border-radius: 30px;
        }

        .stat-card {
            border-radius: 20px;
            border: 1px solid var(--sipn-border);
            background: linear-gradient(180deg, rgba(255,255,255,0.96), var(--sipn-soft));
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.05);
        }

        .stat-card .card-body {
            padding: 1.35rem 1.2rem;
        }

        .stat-card small {
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #64748b;
        }

        .stat-card h2 {
            font-size: clamp(1.6rem, 2vw, 2.2rem);
            font-weight: 700;
            color: #0f172a;
        }

        .badge {
            border-radius: 999px;
            padding: 0.55rem 0.85rem;
            font-weight: 700;
            letter-spacing: 0.08em;
        }

        @media print { .no-print { display: none !important; } }
        @media (max-width: 575.98px) {
            .card-body {
                padding: 1.15rem;
            }

            .table > thead th,
            .table > tbody > tr > * {
                padding: 0.7rem;
            }
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100 <?= htmlspecialchars($theme_class, ENT_QUOTES, 'UTF-8'); ?>">

<?php include __DIR__ . '/navbar.php'; ?>