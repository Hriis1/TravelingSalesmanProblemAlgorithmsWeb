<?php
require_once __DIR__ . "/backend/componenets/includes.php";


//if user is not logged in
$userId = (int) ($_SESSION["user_id"] ?? 0);
if ($userId <= 0) {
    header("Location: login.php");
    exit;
}

//Get user
$user = getFromDBByID("users", $userId, $mysqli);
if (!$user) {
    unset($_SESSION["user_id"]);
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TSP Algorithms</title>
    <link rel="stylesheet" href="css/main.css">
</head>

<body>
    <?php require __DIR__ . "/backend/componenets/topBar.php"; ?>

    <main class="page">
        <section class="app-shell" aria-label="TSP solver interface">
            <header class="topbar">
                <div class="brand">
                    <div class="brand-mark" aria-hidden="true">TSP</div>
                    <div>
                        <h1>Travelling Salesman Solver</h1>
                        <p class="subtitle">Define and solve a traveling salesman problem using various algorithms.</p>
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
                        <label class="type-option" for="inputTypeCustomFile">
                            <input type="radio" id="inputTypeCustomFile" name="inputType" value="customTSPFile">
                            <span>Custom TSP File</span>
                        </label>
                    </div>

                    <form id="tspForm">
                        <div class="input-panel active" id="instanceInputPanel" data-input-panel="instance">
                            <div class="field">
                                <label for="instanceName">Instance</label>
                                <select id="instanceName" name="instanceName">
                                    <option value="" selected disabled>-- Chouse an instance --</option>
                                    <option value="a280">a280</option>
                                    <option value="ali535">ali535</option>
                                    <option value="att48">att48</option>
                                    <option value="att532">att532</option>
                                    <option value="bayg29">bayg29</option>
                                    <option value="bays29">bays29</option>
                                    <option value="berlin52">berlin52</option>
                                    <option value="bier127">bier127</option>
                                    <option value="burma14">burma14</option>
                                    <option value="ch130">ch130</option>
                                    <option value="ch150">ch150</option>
                                    <option value="d198">d198</option>
                                    <option value="d493">d493</option>
                                    <option value="d657">d657</option>
                                    <option value="d1291">d1291</option>
                                    <option value="d1655">d1655</option>
                                    <option value="dantzig42">dantzig42</option>
                                    <option value="dsj1000">dsj1000</option>
                                    <option value="eil51">eil51</option>
                                    <option value="eil76">eil76</option>
                                    <option value="fl417">fl417</option>
                                    <option value="fl1400">fl1400</option>
                                    <option value="fnl4461">fnl4461</option>
                                    <option value="gil262">gil262</option>
                                    <option value="gr96">gr96</option>
                                    <option value="gr120">gr120</option>
                                    <option value="gr137">gr137</option>
                                    <option value="gr202">gr202</option>
                                    <option value="gr229">gr229</option>
                                    <option value="gr431">gr431</option>
                                    <option value="gr666">gr666</option>
                                    <option value="kroa100">kroa100</option>
                                    <option value="krob100">krob100</option>
                                    <option value="kroc100">kroc100</option>
                                    <option value="krod100">krod100</option>
                                    <option value="kroe100">kroe100</option>
                                    <option value="kroa150">kroa150</option>
                                    <option value="krob150">krob150</option>
                                    <option value="kroa200">kroa200</option>
                                    <option value="krob200">krob200</option>
                                    <option value="lin105">lin105</option>
                                    <option value="lin318">lin318</option>
                                    <option value="linhp318">linhp318</option>
                                    <option value="nrw1379">nrw1379</option>
                                    <option value="p654">p654</option>
                                    <option value="pa561">pa561</option>
                                    <option value="pcb442">pcb442</option>
                                    <option value="pcb1173">pcb1173</option>
                                    <option value="pcb3038">pcb3038</option>
                                    <option value="pla7397">pla7397</option>
                                    <option value="pr76">pr76</option>
                                    <option value="pr107">pr107</option>
                                    <option value="pr124">pr124</option>
                                    <option value="pr136">pr136</option>
                                    <option value="pr144">pr144</option>
                                    <option value="pr152">pr152</option>
                                    <option value="pr226">pr226</option>
                                    <option value="pr264">pr264</option>
                                    <option value="pr299">pr299</option>
                                    <option value="pr439">pr439</option>
                                    <option value="pr1002">pr1002</option>
                                    <option value="pr2392">pr2392</option>
                                    <option value="rat99">rat99</option>
                                    <option value="rat195">rat195</option>
                                    <option value="rat575">rat575</option>
                                    <option value="rat783">rat783</option>
                                    <option value="rd100">rd100</option>
                                    <option value="rd400">rd400</option>
                                    <option value="rl1304">rl1304</option>
                                    <option value="rl1323">rl1323</option>
                                    <option value="rl1889">rl1889</option>
                                    <option value="st70">st70</option>
                                    <option value="ts225">ts225</option>
                                    <option value="tsp225">tsp225</option>
                                    <option value="u159">u159</option>
                                    <option value="u574">u574</option>
                                    <option value="u724">u724</option>
                                    <option value="u1060">u1060</option>
                                    <option value="u1432">u1432</option>
                                    <option value="u1817">u1817</option>
                                    <option value="u2152">u2152</option>
                                    <option value="u2319">u2319</option>
                                    <option value="ulysses16">ulysses16</option>
                                    <option value="ulysses22">ulysses22</option>
                                    <option value="vm1084">vm1084</option>
                                    <option value="vm1748">vm1748</option>
                                </select>
                            </div>

                            <div class="field">
                                <label for="instanceAlgorithm">Algorithm</label>
                                <select id="instanceAlgorithm" name="instanceAlgorithm">
                                    <option value="" selected disabled>-- Chouse an algorithm --</option>
                                    <option value="genetic">genetic</option>
                                    <option value="mmas">mmas</option>
                                    <option value="lkh">lkh</option>
                                </select>
                            </div>

                            <div class="field">
                                <label>Problem definition</label>
                                <div class="point-board-frame">
                                    <div class="board-coord board-coord-max" id="instanceBoardCoordsMax">0</div>
                                    <div class="point-board point-board-readonly" id="instancePointBoard" role="img"
                                        aria-label="Loaded TSP instance point display area">
                                    </div>
                                    <div class="board-coord board-coord-min" id="instanceBoardCoordsMin">0</div>
                                </div>
                            </div>
                        </div>

                        <div class="input-panel" id="customInputPanel" data-input-panel="custom">
                            <div class="field">
                                <label for="customAlgorithm">Algorithm</label>
                                <select id="customAlgorithm" name="customAlgorithm">
                                    <option value="" selected disabled>-- Chouse an algorithm --</option>
                                    <option value="genetic">genetic</option>
                                    <option value="mmas">mmas</option>
                                    <option value="lkh">lkh</option>
                                </select>
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

                        <div class="input-panel" id="customFileInputPanel" data-input-panel="customTSPFile">
                            <div class="field">
                                <label for="customFileAlgorithm">Algorithm</label>
                                <select id="customFileAlgorithm" name="customFileAlgorithm">
                                    <option value="" selected disabled>-- Chouse an algorithm --</option>
                                    <option value="genetic">genetic</option>
                                    <option value="mmas">mmas</option>
                                    <option value="lkh">lkh</option>
                                </select>
                            </div>

                            <div class="field">
                                <label for="customTspFile">TSP file</label>
                                <input type="file" id="customTspFile" name="customTspFile" accept=".tsp,.txt">
                            </div>

                            <div class="field">
                                <label>Problem definition</label>
                                <div class="point-board-frame">
                                    <div class="board-coord board-coord-max" id="customFileBoardCoordsMax">0</div>
                                    <div class="point-board point-board-readonly" id="customFilePointBoard" role="img"
                                        aria-label="Custom TSP file point display area">
                                    </div>
                                    <div class="board-coord board-coord-min" id="customFileBoardCoordsMin">0</div>
                                </div>
                            </div>
                        </div>

                        <div class="button-stack">
                            <button class="btn btn-secondary" type="button" id="loadButton">Load TSP</button>
                            <button class="btn btn-secondary" type="button" id="saveLoadedCustomTspButton">
                                Save loaded TSP
                            </button>
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
                                <div class="solution-data-grid" aria-label="Solution data">
                                    <div class="solution-data-field">
                                        <span>Number of cities</span>
                                        <strong id="solutionNCities">--</strong>
                                    </div>
                                    <div class="solution-data-field">
                                        <span>Distance found by solver</span>
                                        <strong id="solutionDist">--</strong>
                                    </div>
                                    <div class="solution-data-field">
                                        <span>Distance found by nearest neighbor</span>
                                        <strong id="solutionNnDist">--</strong>
                                    </div>
                                    <div class="solution-data-field">
                                        <span>Optimal distance</span>
                                        <strong id="solutionOptimalDist">--</strong>
                                    </div>
                                    <div class="solution-data-field">
                                        <span>Increase from optimal</span>
                                        <strong id="solutionOptimalIncrease">--</strong>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                </section>
            </div>
        </section>
    </main>

    <div class="solve-modal" id="solveModal" aria-hidden="true">
        <div class="solve-modal-card" role="dialog" aria-modal="true" aria-labelledby="solveModalTitle">
            <button class="solve-modal-close" type="button" id="cancelSolveButton" aria-label="Cancel solve">x</button>
            <h2 id="solveModalTitle">Solving TSP</h2>
            <p class="solve-modal-status" id="solveModalStatus">Solving TSP using -- algorithm for 0 secs</p>
            <p class="solve-modal-note">This may take a while</p>
        </div>
    </div>

    <div class="save-tsp-modal" id="saveTspModal" aria-hidden="true">
        <div class="save-tsp-modal-card" role="dialog" aria-modal="true" aria-labelledby="saveTspModalTitle">
            <button class="save-tsp-modal-close" type="button" id="closeSaveTspModalButton"
                aria-label="Close save TSP modal">x</button>
            <h2 id="saveTspModalTitle">Save loaded TSP</h2>
            <form class="save-tsp-form" id="saveTspForm">
                <div class="field">
                    <label for="saveTspName">TSP name</label>
                    <input type="text" id="saveTspName" name="saveTspName" autocomplete="off">
                </div>
                <p class="save-tsp-message" id="saveTspMessage" aria-live="polite"></p>
                <button class="btn btn-primary" type="submit" id="saveTspSubmitButton">Save TSP</button>
            </form>
        </div>
    </div>

    <?php require_once __DIR__ . "/scripts.php"; ?>

    <script src="js/main.js"></script>
</body>

</html>
