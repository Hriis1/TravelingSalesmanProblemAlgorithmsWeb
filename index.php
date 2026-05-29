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
                        <p class="subtitle">Define the problem input, then inspect the output path.</p>
                    </div>
                </div>
                <div class="status-pill" aria-live="polite">
                    <span class="status-dot" aria-hidden="true"></span>
                    <span id="statusText">Waiting for input</span>
                </div>
            </header>

            <div class="workspace">
                <section class="input-section" aria-label="TSP input">
                    <div class="section-header">
                        <div>
                            <h2>Input</h2>
                        </div>
                    </div>

                    <div class="type-toggle" aria-label="Input type">
                        <label class="type-option active" for="inputTypeInstance">
                            <input type="radio" id="inputTypeInstance" name="inputType" value="instance" checked>
                            <span>TSP Instance</span>
                        </label>
                        <label class="type-option" for="inputTypeCustom">
                            <input type="radio" id="inputTypeCustom" name="inputType" value="custom">
                            <span>Custom TSP</span>
                        </label>
                    </div>

                    <form id="tspForm">
                        <div class="input-panel active" id="instanceInputPanel" data-input-panel="instance">
                            <div class="field">
                                <label for="instanceName">Instance</label>
                                <input type="text" id="instanceName" name="instanceName" placeholder="example: berlin52"
                                    autocomplete="off">
                            </div>

                            <div class="field">
                                <label for="instanceAlgorithm">Algorithm</label>
                                <input type="text" id="instanceAlgorithm" name="instanceAlgorithm"
                                    placeholder="example: nearest-neighbor" autocomplete="off">
                            </div>
                        </div>

                        <div class="input-panel" id="customInputPanel" data-input-panel="custom">
                            <div class="field">
                                <label for="customAlgorithm">Algorithm</label>
                                <input type="text" id="customAlgorithm" name="customAlgorithm"
                                    placeholder="example: nearest-neighbor" autocomplete="off">
                            </div>

                            <div class="field-row">
                                <div class="field">
                                    <label for="coordsMin">Coords min</label>
                                    <input type="number" id="coordsMin" name="coordsMin" placeholder="0">
                                </div>

                                <div class="field">
                                    <label for="coordsMax">Coords max</label>
                                    <input type="number" id="coordsMax" name="coordsMax" placeholder="100">
                                </div>
                            </div>

                            <div class="field">
                                <label>Problem definition</label>
                                <div class="point-board-frame">
                                    <div class="board-coord board-coord-max" id="inputBoardCoordsMax">0</div>
                                    <div class="point-board" id="customPointBoard" role="img"
                                        aria-label="Custom TSP point placement area">
                                    </div>
                                    <div class="board-coord board-coord-min" id="inputBoardCoordsMin">0</div>
                                </div>
                            </div>
                        </div>

                        <div class="button-stack">
                            <button class="btn btn-primary" type="button" id="solveButton">Solve TSP</button>
                        </div>
                    </form>

                    <div class="details-list" aria-label="Problem details">
                        <div class="detail">
                            <span>Input type</span>
                            <strong id="selectedInputType">TSP Instance</strong>
                        </div>
                        <div class="detail">
                            <span>Problem state</span>
                            <strong id="problemState">None</strong>
                        </div>
                    </div>
                </section>

                <section class="output-section" aria-label="TSP output">
                    <div class="section-header">
                        <div>
                            <h2>Output</h2>
                            <p class="section-subtitle">Solution will appear here.</p>
                        </div>
                    </div>

                    <div class="output-grid">
                        <article class="panel">
                            <div class="panel-header">
                                <h3>Path</h3>
                                <span class="panel-tag">The path the solver found</span>
                            </div>
                            <div class="panel-body">
                                <div class="point-board-frame">
                                    <div class="board-coord board-coord-max" id="outputBoardCoordsMax">0</div>
                                    <div class="point-board point-board-readonly" id="outputPathBoard" role="img"
                                        aria-label="Output TSP path preview area">
                                    </div>
                                    <div class="board-coord board-coord-min" id="outputBoardCoordsMin">0</div>
                                </div>
                            </div>
                        </article>

                        <article class="panel">
                            <div class="panel-header">
                                <h3>Solution data</h3>
                                <span class="panel-tag">Data about the solution</span>
                            </div>
                            <div class="panel-body">
                                <pre class="path-output" id="pathOutput">No output path yet.</pre>
                            </div>
                        </article>
                    </div>
                </section>
            </div>
        </section>
    </main>

    <?php require_once __DIR__ . "/scripts.php"; ?>
    
    <script src="js/main.js"></script>
</body>

</html>
