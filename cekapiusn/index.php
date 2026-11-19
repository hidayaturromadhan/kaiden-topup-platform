<?php
require_once __DIR__ . '/src/ApiGames.php';
$api = new \Aditdev\ApiGames();
$gameList = $api->getGameList();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check IGN Tester & Laravel Integrator | Cek Nickname Game</title>
    <meta name="description" content="Sistem pengujian langsung mekanisme cek nickname game (IGN) berdasarkan Account ID untuk Mobile Legends, Free Fire, Valorant, dll, siap digunakan di Laravel.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-body: #090d16;
            --bg-card: rgba(17, 24, 39, 0.75);
            --bg-card-hover: rgba(24, 34, 53, 0.85);
            --bg-input: #0e1626;
            --border-color: rgba(255, 255, 255, 0.08);
            --border-focus: #6366f1;
            --text-main: #f1f5f9;
            --text-muted: #94a3b8;
            --text-dim: #64748b;
            --primary: #6366f1;
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%);
            --accent-cyan: #06b6d4;
            --accent-emerald: #10b981;
            --accent-rose: #f43f5e;
            --accent-amber: #f59e0b;
            --glass-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.06);
            --radius-sm: 8px;
            --radius-md: 14px;
            --radius-lg: 20px;
            --radius-full: 9999px;
            --transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            line-height: 1.5;
            background-image: 
                radial-gradient(circle at 15% 15%, rgba(99, 102, 241, 0.12) 0%, transparent 40%),
                radial-gradient(circle at 85% 25%, rgba(236, 72, 153, 0.08) 0%, transparent 45%),
                radial-gradient(circle at 50% 80%, rgba(6, 182, 212, 0.08) 0%, transparent 50%);
            background-attachment: fixed;
            padding-bottom: 60px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* Top Header */
        header {
            padding: 32px 0 24px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 32px;
            position: relative;
        }

        .header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .logo-badge {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-md);
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.5);
        }

        .logo-text h1 {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.02em;
            background: linear-gradient(to right, #fff, #cbd5e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .logo-text p {
            font-size: 13px;
            color: var(--text-muted);
        }

        .header-meta {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.25);
            border-radius: var(--radius-full);
            color: var(--accent-emerald);
            font-size: 13px;
            font-weight: 600;
        }

        .status-pill::before {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent-emerald);
            box-shadow: 0 0 10px var(--accent-emerald);
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.85); }
        }

        .api-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-full);
            color: var(--text-muted);
            font-size: 12px;
            text-decoration: none;
            transition: var(--transition);
        }

        .api-badge:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
        }

        /* Navigation Tabs */
        .nav-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 28px;
            background: rgba(17, 24, 39, 0.6);
            padding: 6px;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            width: fit-content;
            backdrop-filter: blur(12px);
        }

        .tab-btn {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: var(--radius-sm);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }

        .tab-btn:hover {
            color: #fff;
        }

        .tab-btn.active {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
        }

        /* Main Layout Grid */
        .tester-grid {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 24px;
            align-items: start;
        }

        @media (max-width: 900px) {
            .tester-grid {
                grid-template-columns: 1fr;
            }
            .nav-tabs {
                width: 100%;
                overflow-x: auto;
            }
        }

        /* Card Styles */
        .glass-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 26px;
            box-shadow: var(--glass-shadow);
            backdrop-filter: blur(16px);
            position: relative;
            overflow: hidden;
        }

        .card-title-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 17px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-title .icon {
            color: var(--accent-cyan);
            font-size: 20px;
        }

        /* Quick Preset Chips */
        .quick-presets {
            margin-bottom: 24px;
            background: rgba(14, 22, 38, 0.6);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 14px 16px;
        }

        .preset-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--accent-cyan);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .preset-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .preset-chip {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-main);
            padding: 7px 13px;
            border-radius: var(--radius-full);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: var(--transition);
        }

        .preset-chip:hover {
            background: rgba(99, 102, 241, 0.2);
            border-color: var(--primary);
            color: #fff;
            transform: translateY(-1px);
        }

        .preset-chip span.badge {
            font-size: 10px;
            background: rgba(255, 255, 255, 0.1);
            padding: 2px 6px;
            border-radius: 4px;
            color: var(--text-muted);
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        label span.req {
            color: var(--accent-rose);
        }

        label span.hint {
            font-size: 11px;
            font-weight: 400;
            color: var(--text-dim);
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        input[type="text"],
        select {
            width: 100%;
            background: var(--bg-input);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            padding: 13px 16px;
            border-radius: var(--radius-md);
            font-family: inherit;
            font-size: 14px;
            transition: var(--transition);
            outline: none;
        }

        input[type="text"]:focus,
        select:focus {
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
            background: #111a2e;
        }

        select {
            appearance: none;
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 18px;
            padding-right: 40px;
        }

        select option {
            background-color: #0e1626;
            color: #f1f5f9;
            padding: 10px;
        }

        .zone-container {
            transition: var(--transition);
        }

        .zone-container.hidden {
            display: none;
        }

        .btn-submit {
            width: 100%;
            background: var(--primary-gradient);
            color: #fff;
            border: none;
            padding: 15px 24px;
            border-radius: var(--radius-md);
            font-family: inherit;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.4);
            transition: var(--transition);
            margin-top: 10px;
        }

        .btn-submit:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(99, 102, 241, 0.6);
        }

        .btn-submit:active:not(:disabled) {
            transform: translateY(0);
        }

        .btn-submit:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none;
        }

        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            display: none;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Result Panel */
        .result-panel {
            min-height: 480px;
            display: flex;
            flex-direction: column;
        }

        .result-placeholder {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px 20px;
            color: var(--text-dim);
        }

        .result-placeholder-icon {
            font-size: 54px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .result-card-inner {
            display: none;
            flex-direction: column;
            gap: 18px;
            animation: fadeIn 0.3s ease forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .ign-highlight-box {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(6, 182, 212, 0.1));
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: var(--radius-md);
            padding: 22px;
            text-align: center;
            position: relative;
        }

        .ign-highlight-box.error-box {
            background: linear-gradient(135deg, rgba(244, 63, 94, 0.15), rgba(245, 158, 11, 0.08));
            border-color: rgba(244, 63, 94, 0.35);
        }

        .ign-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--accent-cyan);
            margin-bottom: 6px;
        }

        .ign-name {
            font-size: 26px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.01em;
            word-break: break-word;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-copy {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: var(--text-muted);
            border-radius: var(--radius-sm);
            padding: 6px 10px;
            font-size: 12px;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-copy:hover {
            color: #fff;
            background: var(--primary);
        }

        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .meta-item {
            background: rgba(14, 22, 38, 0.7);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 12px 14px;
        }

        .meta-item .m-label {
            font-size: 11px;
            color: var(--text-muted);
            margin-bottom: 2px;
        }

        .meta-item .m-val {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-main);
            font-family: 'JetBrains Mono', monospace;
        }

        .json-viewer-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        pre.json-code {
            background: #080d1a;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 14px;
            color: #38bdf8;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            overflow-x: auto;
            max-height: 180px;
        }

        /* Laravel Guide Tab Styles */
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .laravel-section {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .guide-step {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 26px;
            box-shadow: var(--glass-shadow);
        }

        .step-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .step-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 16px;
            font-weight: 700;
        }

        .step-num {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--primary);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
        }

        .code-container {
            position: relative;
            background: #080d1a;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            overflow: hidden;
            margin-top: 10px;
        }

        .code-container-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255, 255, 255, 0.03);
            border-bottom: 1px solid var(--border-color);
            padding: 8px 16px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: var(--text-muted);
        }

        .code-container pre {
            padding: 18px;
            overflow-x: auto;
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            line-height: 1.6;
            color: #cbd5e1;
        }

        /* Games Table Tab */
        .table-responsive {
            overflow-x: auto;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
        }

        table.game-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            text-align: left;
        }

        table.game-table th {
            background: rgba(255, 255, 255, 0.04);
            padding: 14px 18px;
            font-weight: 700;
            color: var(--text-main);
            border-bottom: 1px solid var(--border-color);
        }

        table.game-table td {
            padding: 14px 18px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            color: var(--text-muted);
        }

        table.game-table tr:hover td {
            background: rgba(255, 255, 255, 0.02);
            color: #fff;
        }

        .provider-tag {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 4px;
            text-transform: uppercase;
        }

        .provider-tag.gopay {
            background: rgba(6, 182, 212, 0.15);
            color: var(--accent-cyan);
            border: 1px solid rgba(6, 182, 212, 0.3);
        }

        .provider-tag.codashop {
            background: rgba(245, 158, 11, 0.15);
            color: var(--accent-amber);
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .provider-tag.dancingidol {
            background: rgba(236, 72, 153, 0.15);
            color: #f472b6;
            border: 1px solid rgba(236, 72, 153, 0.3);
        }

        .btn-table-test {
            background: rgba(99, 102, 241, 0.15);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #818cf8;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-table-test:hover {
            background: var(--primary);
            color: #fff;
        }

        /* Toast notification */
        #toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: #1e293b;
            color: #fff;
            padding: 12px 20px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-color);
            font-size: 13px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            display: none;
            align-items: center;
            gap: 10px;
            z-index: 1000;
            animation: slideUp 0.25s ease forwards;
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body>

    <header>
        <div class="container">
            <div class="header-inner">
                <div class="logo-area">
                    <div class="logo-badge">⚡</div>
                    <div class="logo-text">
                        <h1>Check IGN Tester & Laravel Integrator</h1>
                        <p>Validasi Nickname Game Real-Time & Panduan Integrasi Laravel</p>
                    </div>
                </div>
                <div class="header-meta">
                    <div class="status-pill">Sistem Online & Siap Digunakan</div>
                    <a href="api.php?action=games" target="_blank" class="api-badge" title="Lihat Daftar Game format JSON">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        API JSON
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Navigation Tabs -->
        <div class="nav-tabs">
            <button class="tab-btn active" onclick="switchTab('tester')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                Uji Cek Nickname
            </button>
            <button class="tab-btn" onclick="switchTab('laravel')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 18l6-6-6-6"></path><path d="M8 6l-6 6 6 6"></path></svg>
                Cara Pakai di Laravel
            </button>
            <button class="tab-btn" onclick="switchTab('catalog')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                Daftar Game (<?= count($gameList) ?>)
            </button>
        </div>

        <!-- TAB 1: TESTER -->
        <div id="tab-tester" class="tab-content active">
            <div class="tester-grid">
                
                <!-- Left: Form -->
                <div class="glass-card">
                    <div class="card-title-row">
                        <div class="card-title">
                            <span class="icon">🎮</span>
                            Pilih Game & Masukkan Akun
                        </div>
                    </div>

                    <!-- Quick Pre-filled Presets -->
                    <div class="quick-presets">
                        <div class="preset-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path></svg>
                            Klik Cepat Contoh Akun Valid (1-Click Test):
                        </div>
                        <div class="preset-chips">
                            <button type="button" class="preset-chip" onclick="fillPreset('MOBILE_LEGENDS', '604210151', '8425')">
                                MLBB <span class="badge">Diezy x Ruuma</span>
                            </button>
                            <button type="button" class="preset-chip" onclick="fillPreset('FREEFIRE', '116502997', '')">
                                Free Fire <span class="badge">jhonidamn361</span>
                            </button>
                            <button type="button" class="preset-chip" onclick="fillPreset('VALORANT', 'Diezyyy#666', '')">
                                Valorant <span class="badge">Diezyyy #666</span>
                            </button>
                            <button type="button" class="preset-chip" onclick="fillPreset('CALL_OF_DUTY', '10808316016143544796', '')">
                                CODM <span class="badge">tanoniha</span>
                            </button>
                            <button type="button" class="preset-chip" onclick="fillPreset('AOV', '2748846783202461', '')">
                                AOV <span class="badge">PPMMFire</span>
                            </button>
                            <button type="button" class="preset-chip" onclick="fillPreset('AU_TWO_MOBILE', '662311', '')">
                                AU2 Mobile <span class="badge">Juan Bour</span>
                            </button>
                        </div>
                    </div>

                    <!-- Interactive Form -->
                    <form id="checkForm" onsubmit="handleCheck(event)">
                        <div class="form-group">
                            <label for="gameSelect">
                                <span>Pilih Game <span class="req">*</span></span>
                                <span class="hint" id="gameProviderHint">Provider: GoPay Games</span>
                            </label>
                            <select id="gameSelect" onchange="onGameChange()">
                                <?php foreach ($gameList as $g): ?>
                                    <option 
                                        value="<?= htmlspecialchars($g['code']) ?>"
                                        data-zone="<?= $g['requiresZone'] ? '1' : '0' ?>"
                                        data-zonelabel="<?= htmlspecialchars($g['zoneLabel'] ?? '') ?>"
                                        data-provider="<?= htmlspecialchars($g['provider']) ?>"
                                        data-sampleid="<?= htmlspecialchars($g['sampleId'] ?? '') ?>"
                                        data-samplezone="<?= htmlspecialchars($g['sampleZone'] ?? '') ?>"
                                        <?= $g['code'] === 'MOBILE_LEGENDS' ? 'selected' : '' ?>
                                    >
                                        <?= htmlspecialchars($g['name']) ?> (<?= $g['code'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="userIdInput">
                                <span>User ID / Account ID <span class="req">*</span></span>
                                <span class="hint" id="userIdHint">ID Akun Game Pemain</span>
                            </label>
                            <div class="input-wrapper">
                                <input type="text" id="userIdInput" placeholder="Contoh: 604210151" value="604210151" required autocomplete="off">
                            </div>
                        </div>

                        <div class="form-group zone-container" id="zoneGroup">
                            <label for="zoneIdInput">
                                <span id="zoneLabelText">Zone / Server ID <span class="req">*</span></span>
                                <span class="hint">Wajib untuk game dengan sistem server (misal MLBB)</span>
                            </label>
                            <div class="input-wrapper">
                                <input type="text" id="zoneIdInput" placeholder="Contoh: 8425" value="8425" autocomplete="off">
                            </div>
                        </div>

                        <button type="submit" id="btnSubmit" class="btn-submit">
                            <span class="spinner" id="btnSpinner"></span>
                            <span id="btnText">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 6px;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                Periksa Nickname Sekarang
                            </span>
                        </button>
                    </form>
                </div>

                <!-- Right: Results Panel -->
                <div class="glass-card result-panel">
                    <div class="card-title-row">
                        <div class="card-title">
                            <span class="icon">✨</span>
                            Hasil Validasi
                        </div>
                        <div id="latencyBadge" style="display: none; font-size: 12px; font-weight: 700; color: var(--accent-cyan); background: rgba(6, 182, 212, 0.1); padding: 4px 10px; border-radius: 999px;">
                            0 ms
                        </div>
                    </div>

                    <!-- Placeholder before check -->
                    <div class="result-placeholder" id="resultPlaceholder">
                        <div class="result-placeholder-icon">🎯</div>
                        <h3 style="color: var(--text-main); font-size: 16px; margin-bottom: 6px;">Belum Ada Pengecekan</h3>
                        <p style="font-size: 13px; max-width: 280px;">Pilih game di sebelah kiri atau klik tombol contoh di atas untuk langsung menguji respon server.</p>
                    </div>

                    <!-- Live Result Box -->
                    <div class="result-card-inner" id="resultCardInner">
                        <div class="ign-highlight-box" id="ignBox">
                            <div class="ign-label" id="ignStatusLabel">IN-GAME NICKNAME</div>
                            <div class="ign-name">
                                <span id="ignText">-</span>
                                <button type="button" class="btn-copy" onclick="copyNickname()" title="Salin Nickname">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                    Salin
                                </button>
                            </div>
                        </div>

                        <div class="meta-grid">
                            <div class="meta-item">
                                <div class="m-label">Game</div>
                                <div class="m-val" id="resGame">-</div>
                            </div>
                            <div class="meta-item">
                                <div class="m-label">User ID</div>
                                <div class="m-val" id="resUserId">-</div>
                            </div>
                            <div class="meta-item">
                                <div class="m-label">Zone / Server</div>
                                <div class="m-val" id="resZoneId">-</div>
                            </div>
                            <div class="meta-item">
                                <div class="m-label">Provider Aktif</div>
                                <div class="m-val" id="resProvider">-</div>
                            </div>
                        </div>

                        <div>
                            <div class="json-viewer-header">
                                <span>RESPON RAW JSON</span>
                                <button type="button" class="btn-copy" onclick="copyJson()">Salin JSON</button>
                            </div>
                            <pre class="json-code" id="rawJsonDisplay">{}</pre>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- TAB 2: LARAVEL INTEGRATION GUIDE -->
        <div id="tab-laravel" class="tab-content">
            <div class="laravel-section">
                
                <div class="guide-step">
                    <div class="step-header">
                        <div class="step-title">
                            <div class="step-num">1</div>
                            <span>Opsi Rekomendasi: Menggunakan Laravel HTTP Client (Native, Zero Dependency)</span>
                        </div>
                        <span class="status-pill" style="font-size: 11px;">Paling Praktis</span>
                    </div>
                    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 14px;">
                        Anda dapat membuat Service Class di Laravel (misalnya <code>app/Services/GameCheckService.php</code>). Class ini menggunakan <code>Illuminate\Support\Facades\Http</code> bawaan Laravel sehingga sangat cepat dan tidak memerlukan package tambahan.
                    </p>
                    <div class="code-container">
                        <div class="code-container-header">
                            <span>app/Services/GameCheckService.php</span>
                            <button type="button" class="btn-copy" onclick="copyCode('code-service')">Salin Kode</button>
                        </div>
                        <pre id="code-service">&lt;?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class GameCheckService
{
    private const GOPAY_ENDPOINT = 'https://gopay.co.id/games/v1/order/user-account';

    /**
     * Periksa Nickname berdasarkan Game Code, User ID, dan Zone/Server ID
     *
     * @param string $gameCode Contoh: 'MOBILE_LEGENDS', 'FREEFIRE', 'VALORANT', 'AOV', 'CALL_OF_DUTY'
     * @param string $userId   User ID Akun Game
     * @param string|null $zoneId Server / Zone ID (untuk MLBB dll)
     * @return array
     */
    public function checkNickname(string $gameCode, string $userId, ?string $zoneId = null): array
    {
        try {
            $response = Http::withHeaders([
                'Origin'     => 'https://gopay.co.id',
                'Referer'    => 'https://gopay.co.id/',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'Accept'     => 'application/json',
            ])->timeout(10)->post(self::GOPAY_ENDPOINT, [
                'code' => strtoupper(trim($gameCode)),
                'data' => [
                    'userId' => (string) $userId,
                    'zoneId' => (string) ($zoneId ?? ''),
                ],
            ]);

            $data = $response->json();

            if ($response->successful() && !empty($data['data']['username'])) {
                return [
                    'success'  => true,
                    'status'   => 200,
                    'nickname' => urldecode($data['data']['username']),
                    'game'     => $gameCode,
                    'user_id'  => $userId,
                    'zone_id'  => $zoneId,
                ];
            }

            return [
                'success' => false,
                'status'  => 400,
                'message' => $data['message'] ?? 'ID atau Server Game tidak ditemukan.',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'status'  => 500,
                'message' => 'Gagal menghubungi server game: ' . $e->getMessage(),
            ];
        }
    }
}</pre>
                    </div>
                </div>

                <div class="guide-step">
                    <div class="step-header">
                        <div class="step-title">
                            <div class="step-num">2</div>
                            <span>Buat Controller di Laravel</span>
                        </div>
                    </div>
                    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 14px;">
                        Gunakan Service di atas dalam Controller Anda (misalnya <code>app/Http/Controllers/GameCheckController.php</code>) untuk melayani request dari frontend web atau API aplikasi Anda:
                    </p>
                    <div class="code-container">
                        <div class="code-container-header">
                            <span>app/Http/Controllers/GameCheckController.php</span>
                            <button type="button" class="btn-copy" onclick="copyCode('code-controller')">Salin Kode</button>
                        </div>
                        <pre id="code-controller">&lt;?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GameCheckService;

class GameCheckController extends Controller
{
    protected GameCheckService $gameService;

    public function __construct(GameCheckService $gameService)
    {
        $this->gameService = $gameService;
    }

    public function check(Request $request)
    {
        $validated = $request->validate([
            'game'    => 'required|string',
            'user_id' => 'required|string',
            'zone_id' => 'nullable|string',
        ]);

        $result = $this->gameService->checkNickname(
            $validated['game'],
            $validated['user_id'],
            $validated['zone_id'] ?? null
        );

        return response()->json($result, $result['status'] ?? 200);
    }
}</pre>
                    </div>
                </div>

                <div class="guide-step">
                    <div class="step-header">
                        <div class="step-title">
                            <div class="step-num">3</div>
                            <span>Daftarkan Route di <code>routes/api.php</code></span>
                        </div>
                    </div>
                    <div class="code-container">
                        <div class="code-container-header">
                            <span>routes/api.php</span>
                            <button type="button" class="btn-copy" onclick="copyCode('code-route')">Salin Kode</button>
                        </div>
                        <pre id="code-route">use App\Http\Controllers\GameCheckController;

Route::post('/game/check-ign', [GameCheckController::class, 'check']);</pre>
                    </div>
                </div>

                <div class="guide-step">
                    <div class="step-header">
                        <div class="step-title">
                            <div class="step-num">4</div>
                            <span>Opsi Alternatif: Menggunakan Library Check-IGN Ini Langsung</span>
                        </div>
                    </div>
                    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 14px;">
                        Jika Anda ingin memakai class library <code>Aditdev\ApiGames</code> yang persis seperti repository ini di Laravel:
                    </p>
                    <ol style="margin-left: 20px; color: var(--text-muted); font-size: 14px; line-height: 1.8;">
                        <li>Salin folder <code>src/</code> proyek ini ke dalam direktori aplikasi Laravel Anda, misalnya di: <code>app/Libraries/CheckIgn/</code></li>
                        <li>Di Controller Anda, cukup panggil:
                            <pre style="margin-top: 8px; background: #080d1a; padding: 12px; border-radius: 6px; color: #38bdf8; font-size: 12px;">require_once app_path('Libraries/CheckIgn/ApiGames.php');

$api = new \Aditdev\ApiGames();
$result = $api->check('MOBILE_LEGENDS', $userId, $zoneId);
// Hasil berupa array: ['status' => 200, 'nickname' => 'Diezy x Ruuma Kopi']</pre>
                        </li>
                    </ol>
                </div>

            </div>
        </div>

        <!-- TAB 3: GAMES CATALOG -->
        <div id="tab-catalog" class="tab-content">
            <div class="glass-card">
                <div class="card-title-row">
                    <div class="card-title">
                        <span class="icon">📋</span>
                        Katalog Lengkap Game yang Didukung
                    </div>
                    <span style="font-size: 13px; color: var(--text-muted);">Total: <?= count($gameList) ?> Game</span>
                </div>
                <div class="table-responsive">
                    <table class="game-table">
                        <thead>
                            <tr>
                                <th>Nama Game</th>
                                <th>Game Code</th>
                                <th>Server / Zone</th>
                                <th>Provider</th>
                                <th>Contoh ID</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($gameList as $g): ?>
                                <tr>
                                    <td style="font-weight: 600; color: #fff;"><?= htmlspecialchars($g['name']) ?></td>
                                    <td><code style="background: rgba(255,255,255,0.06); padding: 2px 6px; border-radius: 4px;"><?= htmlspecialchars($g['code']) ?></code></td>
                                    <td>
                                        <?php if ($g['requiresZone']): ?>
                                            <span style="color: var(--accent-amber); font-weight: 600;">Wajib Zone ID</span>
                                        <?php else: ?>
                                            <span style="color: var(--text-dim);">Tidak Perlu</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="provider-tag <?= htmlspecialchars($g['provider']) ?>">
                                            <?= htmlspecialchars($g['provider']) ?>
                                        </span>
                                    </td>
                                    <td style="font-family: 'JetBrains Mono', monospace; font-size: 12px;">
                                        <?= htmlspecialchars($g['sampleId'] ?: '-') ?>
                                        <?= $g['sampleZone'] ? ' (' . htmlspecialchars($g['sampleZone']) . ')' : '' ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($g['sampleId'])): ?>
                                            <button 
                                                type="button" 
                                                class="btn-table-test" 
                                                onclick="testFromCatalog('<?= htmlspecialchars($g['code']) ?>', '<?= htmlspecialchars($g['sampleId']) ?>', '<?= htmlspecialchars($g['sampleZone'] ?? '') ?>')"
                                            >
                                                Uji Sekarang
                                            </button>
                                        <?php else: ?>
                                            <span style="color: var(--text-dim); font-size: 12px;">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Notification Toast -->
    <div id="toast">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        <span id="toastMsg">Teks disalin ke clipboard!</span>
    </div>

    <script>
        let lastJsonResponse = null;

        function switchTab(tab) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

            if (tab === 'tester') {
                document.querySelectorAll('.tab-btn')[0].classList.add('active');
                document.getElementById('tab-tester').classList.add('active');
            } else if (tab === 'laravel') {
                document.querySelectorAll('.tab-btn')[1].classList.add('active');
                document.getElementById('tab-laravel').classList.add('active');
            } else if (tab === 'catalog') {
                document.querySelectorAll('.tab-btn')[2].classList.add('active');
                document.getElementById('tab-catalog').classList.add('active');
            }
        }

        function onGameChange() {
            const select = document.getElementById('gameSelect');
            const selectedOpt = select.options[select.selectedIndex];
            const requiresZone = selectedOpt.getAttribute('data-zone') === '1';
            const zoneLabel = selectedOpt.getAttribute('data-zonelabel') || 'Zone / Server ID';
            const provider = selectedOpt.getAttribute('data-provider') || 'codashop';

            // Toggle Zone input
            const zoneGroup = document.getElementById('zoneGroup');
            const zoneInput = document.getElementById('zoneIdInput');
            const zoneLabelText = document.getElementById('zoneLabelText');

            if (requiresZone) {
                zoneGroup.classList.remove('hidden');
                zoneLabelText.innerHTML = zoneLabel + ' <span class="req">*</span>';
                zoneInput.setAttribute('required', 'required');
            } else {
                zoneGroup.classList.add('hidden');
                zoneInput.removeAttribute('required');
                zoneInput.value = '';
            }

            document.getElementById('gameProviderHint').textContent = 'Provider: ' + provider.toUpperCase();
        }

        function fillPreset(gameCode, userId, zoneId) {
            const select = document.getElementById('gameSelect');
            select.value = gameCode;
            onGameChange();

            document.getElementById('userIdInput').value = userId;
            const zoneInput = document.getElementById('zoneIdInput');
            if (zoneInput) {
                zoneInput.value = zoneId || '';
            }

            // Trigger submit immediately for 1-click test
            handleCheck();
        }

        function testFromCatalog(gameCode, userId, zoneId) {
            switchTab('tester');
            fillPreset(gameCode, userId, zoneId);
        }

        async function handleCheck(event) {
            if (event) event.preventDefault();

            const select = document.getElementById('gameSelect');
            const game = select.value;
            const userId = document.getElementById('userIdInput').value.trim();
            const zoneId = document.getElementById('zoneIdInput').value.trim();

            if (!game || !userId) {
                showToast('Mohon lengkapi Game dan User ID');
                return;
            }

            const btn = document.getElementById('btnSubmit');
            const spinner = document.getElementById('btnSpinner');
            const btnText = document.getElementById('btnText');

            btn.disabled = true;
            spinner.style.display = 'inline-block';
            btnText.textContent = 'Memeriksa ke Server Game...';

            const startTime = performance.now();

            try {
                const response = await fetch(`api.php?game=${encodeURIComponent(game)}&user_id=${encodeURIComponent(userId)}&zone_id=${encodeURIComponent(zoneId)}`);
                const data = await response.json();
                const latency = Math.round(performance.now() - startTime);

                renderResult(data, latency);
            } catch (error) {
                renderResult({
                    success: false,
                    status: 500,
                    message: 'Koneksi gagal atau server lokal bermasalah: ' + error.message,
                }, 0);
            } finally {
                btn.disabled = false;
                spinner.style.display = 'none';
                btnText.innerHTML = `
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 6px;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    Periksa Nickname Sekarang
                `;
            }
        }

        function renderResult(data, latency) {
            lastJsonResponse = data;

            document.getElementById('resultPlaceholder').style.display = 'none';
            const card = document.getElementById('resultCardInner');
            card.style.display = 'flex';

            const ignBox = document.getElementById('ignBox');
            const ignStatusLabel = document.getElementById('ignStatusLabel');
            const ignText = document.getElementById('ignText');
            const latencyBadge = document.getElementById('latencyBadge');

            latencyBadge.style.display = 'inline-block';
            latencyBadge.textContent = `${data.execution_time_ms || latency} ms`;

            if (data.success && data.nickname) {
                ignBox.classList.remove('error-box');
                ignStatusLabel.textContent = 'IN-GAME NICKNAME BERHASIL DITEMUKAN';
                ignStatusLabel.style.color = 'var(--accent-emerald)';
                ignText.textContent = data.nickname;
            } else {
                ignBox.classList.add('error-box');
                ignStatusLabel.textContent = 'VALIDASI GAGAL / TIDAK DITEMUKAN';
                ignStatusLabel.style.color = 'var(--accent-rose)';
                ignText.textContent = data.message || 'ID Game Tidak Valid';
            }

            document.getElementById('resGame').textContent = data.game_name || data.game || '-';
            document.getElementById('resUserId').textContent = data.user_id || '-';
            document.getElementById('resZoneId').textContent = data.zone_id || '(Tidak Diperlukan)';
            document.getElementById('resProvider').textContent = (data.provider || 'unknown').toUpperCase();

            document.getElementById('rawJsonDisplay').textContent = JSON.stringify(data, null, 2);
        }

        function copyNickname() {
            const ign = document.getElementById('ignText').textContent;
            if (ign && ign !== '-') {
                navigator.clipboard.writeText(ign);
                showToast('Nickname "' + ign + '" berhasil disalin!');
            }
        }

        function copyJson() {
            if (lastJsonResponse) {
                navigator.clipboard.writeText(JSON.stringify(lastJsonResponse, null, 2));
                showToast('JSON Response berhasil disalin!');
            }
        }

        function copyCode(elementId) {
            const el = document.getElementById(elementId);
            if (el) {
                navigator.clipboard.writeText(el.innerText || el.textContent);
                showToast('Kode berhasil disalin ke clipboard!');
            }
        }

        function showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toastMsg').textContent = msg;
            toast.style.display = 'flex';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 3000);
        }

        // Initialize state on page load
        window.addEventListener('DOMContentLoaded', () => {
            onGameChange();
        });
    </script>
</body>
</html>
