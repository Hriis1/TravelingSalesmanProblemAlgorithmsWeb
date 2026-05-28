<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TSP Algorithms</title>
    <style>
        :root {
            --bg: #f5f7fb;
            --panel: #ffffff;
            --panel-muted: #f8fafc;
            --text: #172033;
            --muted: #64748b;
            --line: #dbe3ef;
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --success: #0f766e;
            --shadow: 0 18px 48px rgba(15, 23, 42, 0.1);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, 0.16), transparent 34rem),
                linear-gradient(135deg, #f8fafc 0%, var(--bg) 48%, #eef2f7 100%);
        }

        button,
        input {
            font: inherit;
        }

        .page {
            width: min(1120px, calc(100% - 32px));
            margin: 0 auto;
            padding: 40px 0;
        }

        .app-shell {
            overflow: hidden;
            border: 1px solid rgba(219, 227, 239, 0.86);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.82);
            box-shadow: var(--shadow);
            backdrop-filter: blur(18px);
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 24px 28px;
            border-bottom: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.74);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .brand-mark {
            display: grid;
            flex: 0 0 44px;
            width: 44px;
            height: 44px;
            place-items: center;
            border-radius: 8px;
            color: #fff;
            background: linear-gradient(135deg, var(--primary), #14b8a6);
            font-weight: 800;
        }

        h1 {
            margin: 0;
            font-size: 1.45rem;
            line-height: 1.15;
        }

        .subtitle {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 0.95rem;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 34px;
            padding: 7px 12px;
            border: 1px solid #bfdbfe;
            border-radius: 999px;
            color: #1e40af;
            background: #eff6ff;
            font-size: 0.9rem;
            white-space: nowrap;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: var(--primary);
        }

        .workspace {
            display: grid;
            grid-template-columns: 360px 1fr;
            min-height: 610px;
        }

        .controls {
            padding: 28px;
            border-right: 1px solid var(--line);
            background: var(--panel);
        }

        .field {
            display: grid;
            gap: 9px;
            margin-bottom: 18px;
        }

        label {
            color: #334155;
            font-size: 0.9rem;
            font-weight: 700;
        }

        input {
            width: 100%;
            min-height: 46px;
            padding: 11px 13px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            color: var(--text);
            background: #fff;
            outline: none;
            transition: border-color 160ms ease, box-shadow 160ms ease;
        }

        input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.13);
        }

        .button-stack {
            display: grid;
            gap: 12px;
            margin: 24px 0;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-height: 46px;
            padding: 11px 16px;
            border: 0;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 800;
            transition: transform 160ms ease, background-color 160ms ease, box-shadow 160ms ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            color: #fff;
            background: var(--primary);
            box-shadow: 0 10px 24px rgba(37, 99, 235, 0.22);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-secondary {
            color: #0f172a;
            background: #e2e8f0;
        }

        .btn-secondary:hover {
            background: #cbd5e1;
        }

        .hint {
            margin: 0;
            color: var(--muted);
            font-size: 0.92rem;
            line-height: 1.55;
        }

        .details-list {
            display: grid;
            gap: 12px;
            margin-top: 28px;
            padding-top: 24px;
            border-top: 1px solid var(--line);
        }

        .detail {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            color: var(--muted);
            font-size: 0.92rem;
        }

        .detail strong {
            color: #334155;
        }

        .display-area {
            display: grid;
            gap: 18px;
            padding: 28px;
            background: linear-gradient(180deg, var(--panel-muted), #ffffff);
        }

        .panel {
            display: grid;
            min-height: 260px;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--panel);
        }

        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 16px 18px;
            border-bottom: 1px solid var(--line);
            background: #fbfdff;
        }

        h2 {
            margin: 0;
            font-size: 1rem;
            line-height: 1.2;
        }

        .panel-tag {
            color: var(--muted);
            font-size: 0.84rem;
        }

        .panel-body {
            padding: 18px;
        }

        .empty-state {
            display: grid;
            min-height: 178px;
            place-items: center;
            padding: 28px;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            color: var(--muted);
            text-align: center;
            background: #f8fafc;
        }

        .instance-card {
            display: none;
            gap: 16px;
        }

        .instance-card.active {
            display: grid;
        }

        .instance-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px;
            border-radius: 8px;
            background: #eef6ff;
        }

        .instance-title strong {
            overflow-wrap: anywhere;
        }

        .metric-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .metric {
            padding: 14px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #fff;
        }

        .metric span {
            display: block;
            color: var(--muted);
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .metric strong {
            display: block;
            margin-top: 6px;
            font-size: 1.25rem;
        }

        .preview-box {
            min-height: 78px;
            padding: 14px;
            border-radius: 8px;
            color: #334155;
            background: #f8fafc;
            line-height: 1.6;
        }

        .path-output {
            min-height: 178px;
            margin: 0;
            padding: 18px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            color: #0f172a;
            background: #f8fafc;
            font-family: "Cascadia Code", "Fira Code", Consolas, monospace;
            line-height: 1.7;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }

        .success-text {
            color: var(--success);
            font-weight: 800;
        }

        @media (max-width: 820px) {
            .page {
                width: min(100% - 20px, 1120px);
                padding: 20px 0;
            }

            .topbar {
                align-items: flex-start;
                flex-direction: column;
                padding: 20px;
            }

            .workspace {
                grid-template-columns: 1fr;
            }

            .controls {
                border-right: 0;
                border-bottom: 1px solid var(--line);
                padding: 20px;
            }

            .display-area {
                padding: 20px;
            }

            .metric-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <main class="page">
        <section class="app-shell" aria-label="TSP solver interface">
            <header class="topbar">
                <div class="brand">
                    <div class="brand-mark" aria-hidden="true">TSP</div>
                    <div>
                        <h1>Travelling Salesman Solver</h1>
                        <p class="subtitle">Load/define a problem, inspect it, run the solver.</p>
                    </div>
                </div>
                <div class="status-pill" aria-live="polite">
                    <span class="status-dot" aria-hidden="true"></span>
                    <span id="statusText">Waiting for instance</span>
                </div>
            </header>

            <div class="workspace">
                <aside class="controls">
                    <form id="tspForm">
                        <div class="field">
                            <label for="instanceName">TSP instance name</label>
                            <input type="text" id="instanceName" name="instanceName" placeholder="example: berlin52"
                                autocomplete="off">
                        </div>

                        <div class="button-stack">
                            <button class="btn btn-primary" type="button" id="loadButton">Load TSP</button>
                            <button class="btn btn-secondary" type="button" id="solveButton">Solve TSP</button>
                        </div>
                    </form>

                    <p class="hint">
                        This screen is UI-only. The buttons update the placeholders locally so the layout is ready for
                        real API calls later.
                    </p>

                    <div class="details-list" aria-label="Instance details">
                        <div class="detail">
                            <span>Problem state</span>
                            <strong id="problemState">None</strong>
                        </div>
                    </div>
                </aside>

                <section class="display-area">
                    <article class="panel">
                        <div class="panel-header">
                            <h2>Loaded TSP</h2>
                            <span class="panel-tag">Instance preview</span>
                        </div>
                        <div class="panel-body">
                            <div class="empty-state" id="loadedEmpty">
                                Enter an instance name and click Load TSP.
                            </div>

                            <div class="instance-card" id="loadedCard">
                                <div class="instance-title">
                                    <strong id="loadedName">No instance loaded</strong>
                                    <span class="success-text">Loaded</span>
                                </div>

                                <div class="metric-grid">
                                    <div class="metric">
                                        <span>Nodes</span>
                                        <strong>--</strong>
                                    </div>
                                    <div class="metric">
                                        <span>Distance type</span>
                                        <strong>--</strong>
                                    </div>
                                    <div class="metric">
                                        <span>Best known</span>
                                        <strong>--</strong>
                                    </div>
                                </div>

                                <div class="preview-box" id="loadedPreview">
                                    Instance data will be shown here after backend integration.
                                </div>
                            </div>
                        </div>
                    </article>

                    <article class="panel">
                        <div class="panel-header">
                            <h2>Output Path</h2>
                            <span class="panel-tag">Solver result</span>
                        </div>
                        <div class="panel-body">
                            <pre class="path-output" id="pathOutput">No output path yet.</pre>
                        </div>
                    </article>
                </section>
            </div>
        </section>
    </main>

    <?php require_once __DIR__ . "/scripts.php"; ?>

    <script>
        $(function () {
            const $instanceInput = $('#instanceName');
            const $loadButton = $('#loadButton');
            const $solveButton = $('#solveButton');
            const $statusText = $('#statusText');
            const $problemState = $('#problemState');
            const $loadedEmpty = $('#loadedEmpty');
            const $loadedCard = $('#loadedCard');
            const $loadedName = $('#loadedName');
            const $loadedPreview = $('#loadedPreview');
            const $pathOutput = $('#pathOutput');

            let activeInstance = '';

            $loadButton.on('click', function () {
                const instanceName = $instanceInput.val().trim();

                if (!instanceName) {
                    $instanceInput.trigger('focus');
                    $statusText.text('Enter an instance name');
                    return;
                }

                activeInstance = instanceName;
                $problemState.text('Loaded');
                $statusText.text('Instance loaded');
                $loadedName.text(instanceName);
                $loadedPreview.text(`${instanceName} is selected. Real TSP coordinates and metadata can be rendered in this panel once the API is connected.`);
                $loadedEmpty.hide();
                $loadedCard.addClass('active');
                $pathOutput.text('No output path yet.');
            });

            $solveButton.on('click', function () {
                if (!activeInstance) {
                    $statusText.text('Load an instance first');
                    $instanceInput.trigger('focus');
                    return;
                }

                $problemState.text('Solved');
                $statusText.text('Solution ready');
                $pathOutput.text([
                    `Instance: ${activeInstance}`,
                    'Path: 1 -> 2 -> 3 -> ... -> 1',
                    'Total distance: --',
                    '',
                    'This is a UI placeholder. Replace it with the API result when the backend is connected.'
                ].join('\n'));
            });
        });
    </script>
</body>

</html>
