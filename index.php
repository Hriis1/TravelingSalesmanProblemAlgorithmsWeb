<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TSP Algorithms</title>
    <link rel="stylesheet" href="css/main.css">
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
    
    <script src="js/main.js"></script>
</body>

</html>