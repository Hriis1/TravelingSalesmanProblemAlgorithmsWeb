$(function () {
    const $inputTypes = $('input[name="inputType"]');
    const $typeOptions = $('.type-option');
    const $inputPanels = $('.input-panel');
    const $selectedInputType = $('#selectedInputType');
    const $problemState = $('#problemState');
    const $statusText = $('#statusText');
    const $inputBoardCoordsMin = $('#inputBoardCoordsMin');
    const $inputBoardCoordsMax = $('#inputBoardCoordsMax');
    const $outputBoardCoordsMin = $('#outputBoardCoordsMin');
    const $outputBoardCoordsMax = $('#outputBoardCoordsMax');
    const $solutionNCities = $('#solutionNCities');
    const $solutionDist = $('#solutionDist');
    const $solutionNnDist = $('#solutionNnDist');
    const $solutionOptimalDist = $('#solutionOptimalDist');
    const $solutionOptimalIncrease = $('#solutionOptimalIncrease');
    const $loadTspButton = $('#loadButton');
    const $solveTspButton = $('#solveButton');
    const $logOutButton = $('#logOutButton');
    const $saveLoadedCustomTspButton = $('#saveLoadedCustomTspButton');
    const $saveTspModal = $('#saveTspModal');
    const $closeSaveTspModalButton = $('#closeSaveTspModalButton');
    const $saveTspForm = $('#saveTspForm');
    const $saveTspName = $('#saveTspName');
    const $saveTspMessage = $('#saveTspMessage');
    const $saveTspSubmitButton = $('#saveTspSubmitButton');
    const $solveModal = $('#solveModal');
    const $solveModalStatus = $('#solveModalStatus');
    const $cancelSolveButton = $('#cancelSolveButton');

    const $instanceName = $('#instanceName');
    const $instanceAlgorithm = $('#instanceAlgorithm');
    const $savedCustomTsp = $('#savedCustomTsp');
    const $loadSavedCustomTspButton = $('#loadSavedCustomTspButton');
    const $customAlgorithm = $('#customAlgorithm');
    const $coordsMin = $('#coordsMin');
    const $coordsMax = $('#coordsMax');
    const $customPointBoard = $('#customPointBoard');
    const $customFileAlgorithm = $('#customFileAlgorithm');
    const $customTspFile = $('#customTspFile');

    //Base Req body representation that will be sent to api
    let tspRequestBody = {
        inputType: null,
        instance: null,
        algorithm: null,
    };

    //coords for the loaded tsp
    let tspCoords = [];

    //coords placed by the user for custom tsp
    let customTspCoords = [];

    //contents of the selected custom tsp file
    let customTspFileContents = '';

    //State for the current solve request
    let solveRequest = null;
    let solveTimer = null;
    let solveStartedAt = null;
    let solveWasCancelled = false;

    //Display names for the supported input modes
    function getInputTypeLabel(inputType) {
        if (inputType === 'custom') {
            return 'Custom TSP';
        }

        if (inputType === 'customTSPFile') {
            return 'Custom TSP File';
        }

        return 'TSP Instance';
    }

    //Keep the status pill and problem state in sync
    function setProblemState(state, status) {
        $problemState.text(state);
        $statusText.text(status);
    }

    //Reset output board coordinate labels to their default values
    function resetOutputCoords() {
        $outputBoardCoordsMin.text('0');
        $outputBoardCoordsMax.text('0');
    }

    //Reset solution data to empty placeholder values
    function resetSolutionData() {
        $solutionNCities.text('--');
        $solutionDist.text('--');
        $solutionNnDist.text('--');
        $solutionOptimalDist.text('--');
        $solutionOptimalIncrease.text('--');
    }

    //Reset the loaded tsp state when input mode changes
    function resetLoadedTspState() {
        tspRequestBody = {
            inputType: null,
            instance: null,
            algorithm: null,
        };
        tspCoords = [];
        customTspCoords = [];
        customTspFileContents = '';
        resetGrid('instancePointBoard');
        resetGrid('customPointBoard');
        resetGrid('customFilePointBoard');
        resetGrid('outputPathBoard');
        resetOutputCoords();
        resetSolutionData();
    }

    //Write the current placeholder solution values
    function setSolutionData(nCities, dist, nnDist, optimalDist, optimalIncrease = '--') {
        $solutionNCities.text(nCities);
        $solutionDist.text(dist);
        $solutionNnDist.text(nnDist);
        $solutionOptimalDist.text(optimalDist);
        $solutionOptimalIncrease.text(optimalIncrease);
    }

    //Calculate how much worse the found distance is than optimal
    function getOptimalIncreasePercent(dist, optimalDist) {
        const numericDist = Number(dist);
        const numericOptimalDist = Number(optimalDist);

        if (!Number.isFinite(numericDist) || !Number.isFinite(numericOptimalDist) || numericOptimalDist <= 0) {
            return '--';
        }

        return `${(((numericDist - numericOptimalDist) / numericOptimalDist) * 100).toFixed(2)}%`;
    }

    //Format elapsed solve time as seconds or minutes
    function formatSolveElapsed(totalSeconds) {
        if (totalSeconds < 60) {
            return `${totalSeconds} secs`;
        }

        const minutes = Math.floor(totalSeconds / 60);
        const seconds = totalSeconds % 60;

        return `${minutes} mins ${seconds} secs`;
    }

    //Update the solving modal elapsed time
    function updateSolveModalStatus() {
        const elapsedSeconds = Math.floor((Date.now() - solveStartedAt) / 1000);
        const algorithm = tspRequestBody.algorithm || '--';

        $solveModalStatus.text(`Solving TSP using ${algorithm} algorithm for ${formatSolveElapsed(elapsedSeconds)}`);
    }

    //Show the solving modal and start its timer
    function showSolveModal() {
        solveStartedAt = Date.now();
        updateSolveModalStatus();
        $solveModal.addClass('active').attr('aria-hidden', 'false');
        solveTimer = setInterval(updateSolveModalStatus, 1000);
    }

    //Hide the solving modal and stop its timer
    function hideSolveModal() {
        clearInterval(solveTimer);
        solveTimer = null;
        solveStartedAt = null;
        $solveModal.removeClass('active').attr('aria-hidden', 'true');
    }

    //Show the save tsp modal
    function showSaveTspModal() {
        $saveTspName.val('');
        setSaveTspMessage('');
        $saveTspModal.addClass('active').attr('aria-hidden', 'false');
        $saveTspName.trigger('focus');
    }

    //Hide the save tsp modal
    function hideSaveTspModal() {
        $saveTspModal.removeClass('active').attr('aria-hidden', 'true');
    }

    //Show the current save tsp message
    function setSaveTspMessage(message, type = 'error') {
        $saveTspMessage
            .removeClass('success error')
            .addClass(type)
            .text(message);
    }

    //Mirror custom coord inputs onto the input board labels
    function updateInputBoardCoords() {
        $inputBoardCoordsMin.text($coordsMin.val().trim() || '0');
        $inputBoardCoordsMax.text($coordsMax.val().trim() || '0');
    }

    //Read and validate the custom coord range
    function getCustomCoordBounds() {
        const min = Number($coordsMin.val());
        const max = Number($coordsMax.val());

        if (!Number.isFinite(min) || !Number.isFinite(max) || max <= min) {
            return null;
        }

        return { min, max };
    }

    //Calculate padded min and max values for displaying coords on a grid
    function getMinMaxCoords(coords) {
        //Return defaults when no coords exist
        if (!Array.isArray(coords) || coords.length === 0) {
            return {
                min: 0,
                max: 0,
            };
        }

        //Collect all x and y values from supported coord shapes
        const coordValues = coords.flatMap((coord) => [
            Number(coord.x ?? coord[0]),
            Number(coord.y ?? coord[1])
        ]).filter(Number.isFinite);

        //Return defaults when coords cannot be parsed
        if (coordValues.length === 0) {
            return {
                min: 0,
                max: 0,
            };
        }

        const lowestCoord = Math.min(...coordValues);
        const highestCoord = Math.max(...coordValues);
        const coordRange = highestCoord - lowestCoord;

        //Add space around the outer points
        const padding = coordRange > 0 ? coordRange * 0.05 : 1;

        //Round outward so every point fits inside the grid
        return {
            min: Math.max(0, Math.floor(lowestCoord - padding)),
            max: Math.ceil(highestCoord + padding),
        };
    }

    //Reset grid
    function resetGrid(gridId) {
        const $grid = $(gridId.startsWith('#') ? gridId : `#${gridId}`);
        $grid.find('.grid-point, .grid-path-edge').remove();
    }

    //Convert one coord to a percent position inside the grid
    function getGridPointPosition(coord, min, coordRange) {
        const x = Number(coord.x ?? coord[0]);
        const y = Number(coord.y ?? coord[1]);

        if (!Number.isFinite(x) || !Number.isFinite(y)) {
            return null;
        }

        return {
            leftPercent: ((x - min) / coordRange) * 100,
            topPercent: (1 - ((y - min) / coordRange)) * 100,
        };
    }

    //Draw coord points inside a grid using padded min and max bounds
    function drawPointsOnGrid(gridId, coords, shouldResetGrid = true, coordBounds = null) {
        const $grid = $(gridId.startsWith('#') ? gridId : `#${gridId}`);
        const safeCoords = Array.isArray(coords) ? coords : [];
        const { min, max } = coordBounds ?? getMinMaxCoords(safeCoords);
        const coordRange = max - min || 1;

        //Clear old points before drawing new ones
        if (shouldResetGrid) {
            resetGrid(gridId);
        } else {
            $grid.find('.grid-point').remove();
        }

        //Update labels that belong to the same board frame
        $grid.closest('.point-board-frame').find('.board-coord-min').text(min);
        $grid.closest('.point-board-frame').find('.board-coord-max').text(max);

        //Place every valid point inside the grid
        safeCoords.forEach((coord) => {
            const position = getGridPointPosition(coord, min, coordRange);

            if (!position) {
                return;
            }

            $('<div>')
                .addClass('grid-point')
                .css({
                    left: `${position.leftPercent}%`,
                    top: `${position.topPercent}%`,
                })
                .appendTo($grid);
        });
    }

    //Draw only the path edges and close it back to the first city
    function drawPathOnGrid(gridId, coords, path, coordBounds = null) {
        const $grid = $(gridId.startsWith('#') ? gridId : `#${gridId}`);
        const safeCoords = Array.isArray(coords) ? coords : [];
        const safePath = Array.isArray(path) ? path : [];
        const { min, max } = coordBounds ?? getMinMaxCoords(safeCoords);
        const coordRange = max - min || 1;

        //Start from a clean grid with the same bounds as points
        resetGrid(gridId);
        $grid.closest('.point-board-frame').find('.board-coord-min').text(min);
        $grid.closest('.point-board-frame').find('.board-coord-max').text(max);

        const pathPositions = safePath.map((coordIndex) => {
            const coord = safeCoords[Number(coordIndex)];

            return coord ? getGridPointPosition(coord, min, coordRange) : null;
        }).filter(Boolean);

        //Draw every edge plus the final closing edge
        pathPositions.forEach((position, index) => {
            const nextPosition = pathPositions[(index + 1) % pathPositions.length];

            if (!nextPosition || pathPositions.length < 2) {
                return;
            }

            const deltaX = nextPosition.leftPercent - position.leftPercent;
            const deltaY = nextPosition.topPercent - position.topPercent;
            const edgeLength = Math.hypot(deltaX, deltaY);
            const edgeAngle = Math.atan2(deltaY, deltaX) * 180 / Math.PI;

            $('<div>')
                .addClass('grid-path-edge')
                .css({
                    left: `${position.leftPercent}%`,
                    top: `${position.topPercent}%`,
                    width: `${edgeLength}%`,
                    transform: `translateY(-50%) rotate(${edgeAngle}deg)`,
                })
                .appendTo($grid);
        });

    }

    //Focus the first required field that has no value
    function focusFirstEmpty($fields) {
        const emptyField = $fields.toArray().find((field) => {
            const fieldValue = $(field).val();

            return !String(fieldValue ?? '').trim();
        });

        if (emptyField) {
            $(emptyField).trigger('focus');
        }

        return Boolean(emptyField);
    }

    //Read the selected custom tsp file as text
    function readCustomTspFile() {
        const file = $customTspFile[0].files[0];

        return new Promise((resolve, reject) => {
            if (!file) {
                reject('No file selected');
                return;
            }

            const reader = new FileReader();

            //Return file contents after the browser finishes reading
            reader.onload = function (event) {
                resolve(event.target.result);
            };

            //Surface file read errors to the load handler
            reader.onerror = function () {
                reject('Could not read selected file');
            };

            reader.readAsText(file);
        });
    }

    //Show only the form fields for the selected input type
    function switchInputType(inputType) {
        $typeOptions.removeClass('active');
        $inputTypes.filter(`[value="${inputType}"]`).closest('.type-option').addClass('active');

        $inputPanels.removeClass('active');
        $inputPanels.filter(`[data-input-panel="${inputType}"]`).addClass('active');

        $saveLoadedCustomTspButton.toggle(inputType == 'custom');
        $selectedInputType.text(getInputTypeLabel(inputType));
        setProblemState('None', `${getInputTypeLabel(inputType)} selected`);
        resetLoadedTspState();
    }

    $saveLoadedCustomTspButton.hide();

    //Load saved tsp names for the custom tsp select
    function loadSavedCustomTsps() {
        $.ajax({
            url: 'backend/users/userRouter.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'getAllUsersTspsNames'
            },
            success: function (result) {
                $savedCustomTsp.find('option:not(:first)').remove();

                if (result.success != true || !Array.isArray(result.tsps)) {
                    return;
                }

                result.tsps.forEach(function (tsp) {
                    $('<option>')
                        .val(tsp.id)
                        .text(tsp.name)
                        .appendTo($savedCustomTsp);
                });
            },
            error: function (xhr) {
                console.log(xhr.responseText);
            }
        });
    }

    loadSavedCustomTsps();

    //Toggle between the available input modes
    $inputTypes.on('change', function () {
        switchInputType($(this).val());
    });

    //Log out the current user through userRouter
    $logOutButton.on('click', function () {
        $.ajax({
            url: 'backend/users/userRouter.php',
            type: 'POST',
            data: {
                action: 'logOutUser'
            },
            complete: function () {
                window.location.href = 'login.php';
            }
        });
    });

    //Update board labels while the user edits the coordinate range
    $coordsMin.add($coordsMax).on('input', function () {
        updateInputBoardCoords();
        customTspCoords = [];
        resetGrid('customPointBoard');

        if (tspRequestBody.inputType != 'custom') {
            return;
        }

        tspRequestBody = {
            inputType: null,
            instance: null,
            algorithm: null,
        };
        tspCoords = [];
        resetGrid('outputPathBoard');
        resetOutputCoords();
        resetSolutionData();
        setProblemState('Not loaded', 'Load TSP before saving or solving');
    });

    //Load the selected saved tsp into the custom tsp editor
    $loadSavedCustomTspButton.on('click', function () {
        const savedTspId = $savedCustomTsp.val();

        if (!savedTspId) {
            setProblemState('Missing input', 'Choose a saved TSP');
            $savedCustomTsp.trigger('focus');
            return;
        }

        $loadSavedCustomTspButton.prop('disabled', true).text('Loading...');

        $.ajax({
            url: 'backend/users/userRouter.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'getUsersTsp',
                tsp_id: savedTspId
            },
            success: function (result) {
                if (result.success != true || !result.tsp) {
                    setProblemState('Error loading TSP', result.error || 'Could not load saved TSP');
                    return;
                }

                const savedTsp = result.tsp;
                const coordBounds = {
                    min: Number(savedTsp.coords_min),
                    max: Number(savedTsp.coords_max)
                };

                if (!Array.isArray(savedTsp.coords) || !Number.isFinite(coordBounds.min) || !Number.isFinite(coordBounds.max)) {
                    setProblemState('Error loading TSP', 'Saved TSP data is invalid');
                    return;
                }

                $coordsMin.val(coordBounds.min);
                $coordsMax.val(coordBounds.max);
                updateInputBoardCoords();

                customTspCoords = savedTsp.coords.map((coord) => [...coord]);
                tspCoords = customTspCoords.map((coord) => [...coord]);
                tspRequestBody = buildTspReqBody();

                drawPointsOnGrid('customPointBoard', customTspCoords, true, coordBounds);
                resetGrid('outputPathBoard');
                resetOutputCoords();
                resetSolutionData();
                setProblemState('Loaded', 'Saved TSP loaded');
            },
            error: function (xhr) {
                console.log(xhr.responseText);
                setProblemState('Error loading TSP', 'Could not load saved TSP');
            },
            complete: function () {
                $loadSavedCustomTspButton.prop('disabled', false).text('Load');
            }
        });
    });

    //Invalidate loaded custom file data when the chosen file changes
    $customTspFile.on('change', function () {
        customTspFileContents = '';

        if (tspRequestBody.inputType != 'customTSPFile') {
            return;
        }

        tspRequestBody = {
            inputType: null,
            instance: null,
            algorithm: null,
        };
        tspCoords = [];
        resetGrid('customFilePointBoard');
        resetGrid('outputPathBoard');
        resetOutputCoords();
        resetSolutionData();
        setProblemState('Not loaded', 'Load TSP file before solving');
    });

    //Add a custom point where the user clicks on the board
    $customPointBoard.on('click', function (event) {
        const coordBounds = getCustomCoordBounds();

        if (!coordBounds) {
            setProblemState('Missing input', 'Enter valid coords min and max');
            return;
        }

        const boardOffset = $customPointBoard.offset();
        const boardWidth = $customPointBoard.width();
        const boardHeight = $customPointBoard.height();
        const clickX = event.pageX - boardOffset.left;
        const clickY = event.pageY - boardOffset.top;
        const coordRange = coordBounds.max - coordBounds.min;
        const coordX = coordBounds.min + (clickX / boardWidth) * coordRange;
        const coordY = coordBounds.max - (clickY / boardHeight) * coordRange;

        customTspCoords.push([
            Math.round(coordX),
            Math.round(coordY)
        ]);

        drawPointsOnGrid('customPointBoard', customTspCoords, true, coordBounds);
    });

    //Abort the active solve request when the modal is closed
    $cancelSolveButton.on('click', function () {
        if (!solveRequest) {
            hideSolveModal();
            return;
        }

        solveWasCancelled = true;
        solveRequest.abort();
        hideSolveModal();
        setProblemState('Cancelled', 'Solve cancelled');
    });

    //Open save modal only when a custom tsp is loaded
    $saveLoadedCustomTspButton.on('click', function () {
        if (tspRequestBody.inputType != 'custom' || tspCoords.length === 0) {
            setProblemState('Not loaded', 'Load custom TSP before saving');
            return;
        }

        if (!getCustomCoordBounds()) {
            setProblemState('Invalid input', 'Enter valid minimum and maximum');
            return;
        }

        showSaveTspModal();
    });

    //Close the save modal without saving
    $closeSaveTspModalButton.on('click', function () {
        hideSaveTspModal();
    });

    //Save the loaded custom tsp for the current user
    $saveTspForm.on('submit', function (event) {
        event.preventDefault();

        const tspName = $saveTspName.val().trim();
        const coordBounds = getCustomCoordBounds();

        if (!tspName) {
            setSaveTspMessage('Enter a TSP name');
            $saveTspName.trigger('focus');
            return;
        }

        if (tspRequestBody.inputType != 'custom' || tspCoords.length === 0 || !coordBounds) {
            hideSaveTspModal();
            setProblemState('Not loaded', 'Load custom TSP before saving');
            return;
        }

        $saveTspSubmitButton.prop('disabled', true).text('Saving...');
        setSaveTspMessage('');

        $.ajax({
            url: 'backend/users/userRouter.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'saveCustomTsp',
                name: tspName,
                coords_min: coordBounds.min,
                coords_max: coordBounds.max,
                coords: JSON.stringify(tspCoords)
            },
            success: function (result) {
                if (result.success == true) {
                    setSaveTspMessage('TSP saved', 'success');
                    setProblemState('Saved', 'Custom TSP saved');
                    hideSaveTspModal();
                    loadSavedCustomTsps();
                    return;
                }

                setSaveTspMessage(result.error || 'Could not save TSP');
            },
            error: function (xhr) {
                console.log(xhr.responseText);
                setSaveTspMessage('Could not save TSP');
            },
            complete: function () {
                $saveTspSubmitButton.prop('disabled', false).text('Save TSP');
            }
        });
    });

    //Build the body that will be sent to api
    function buildTspReqBody() {
        const inputType = $inputTypes.filter(':checked').val();

        if (inputType === 'instance') {
            //Load an instance
            return {
                inputType: inputType,
                instance: $instanceName.val(),
                algorithm: $instanceAlgorithm.val()
            };
        } else if (inputType == 'custom') {
            //Load custom
            return {
                inputType: inputType,
                algorithm: $customAlgorithm.val(),
                customTSP: {
                    numCities: tspCoords.length,
                    cities: tspCoords
                }
            };
        } else if (inputType == 'customTSPFile') {
            //Load custom tsp file
            return {
                inputType: inputType,
                algorithm: $customFileAlgorithm.val(),
                customTSPFile: customTspFileContents
            };
        }
    }

    //Validate and store the instance request body
    $loadTspButton.on('click', async function () {
        const inputType = $inputTypes.filter(':checked').val();

        if (inputType == 'instance') { //tsp instance input
            //if there are unfilled fields
            if (focusFirstEmpty($instanceName.add($instanceAlgorithm))) {
                setProblemState('Missing input', 'Enter instance and algorithm');
                return;
            }

            //build the req body for sending to the tsp solver api
            tspRequestBody = buildTspReqBody();

            //Reset coords
            tspCoords = [];

            //Send the req to load the tsp coords for display
            $.ajax({
                url: 'backend/tspApi/tspApiRouter.php',
                type: 'GET',
                data: {
                    action: 'getTspInstanceCoords',
                    instance: $instanceName.val()
                },
                dataType: 'json',
                success: function (result) {
                    //Error with loading coords
                    if (result.success == false) {
                        resetGrid('instancePointBoard');
                        setProblemState('Error loading tsp', result.error);
                        return
                    }

                    //Success wolading coords
                    tspCoords = result.coords
                    drawPointsOnGrid('instancePointBoard', tspCoords);
                    setProblemState('TSP Loaded', 'Loaded');
                    resetGrid('outputPathBoard');
                    resetOutputCoords();
                    resetSolutionData();
                },
                error: function (xhr) {
                    console.log(xhr.responseText);
                    resetGrid('instancePointBoard');
                    setProblemState('Error loading TSP', 'Could not load instance coords');
                    return;
                }
            });

        } else if (inputType == 'custom') { //custom tsp input

            //Validate
            if (focusFirstEmpty($customAlgorithm.add($coordsMin).add($coordsMax))) {
                setProblemState('Missing input', 'Enter algorithm and coordinate range');
                return;
            }
            const coordBounds = getCustomCoordBounds();
            if (!coordBounds) {
                setProblemState('Invalid input', 'Enter valid minimum and maximum');
                return;
            }
            if (customTspCoords.length === 0) {
                setProblemState('Missing input', 'Add custom points before loading');
                return;
            }

            //Build the coords
            tspCoords = customTspCoords.map((coord) => [...coord]);

            //build the req body for sending to the tsp solver api
            tspRequestBody = buildTspReqBody();

            drawPointsOnGrid('customPointBoard', customTspCoords, true, coordBounds);
            resetGrid('outputPathBoard');
            resetOutputCoords();
            resetSolutionData();
            setProblemState('Loaded', 'Custom TSP loaded');
            return;
        } else if (inputType == 'customTSPFile') { //custom tsp file input

            //Validate
            if (focusFirstEmpty($customFileAlgorithm.add($customTspFile))) {
                setProblemState('Missing input', 'Enter algorithm and choose a TSP file');
                return;
            }

            try {
                //Read file contents before building the request body
                customTspFileContents = await readCustomTspFile();
            } catch (error) {
                setProblemState('Error loading file', error);
                return;
            }

            //build the req body for sending to the tsp solver api
            tspRequestBody = buildTspReqBody();

            //Reset coords
            tspCoords = [];

            //Send the req to load the tsp file coords for display
            $.ajax({
                url: 'backend/tspApi/tspApiRouter.php',
                type: 'POST',
                data: {
                    action: 'getTspCustomFileCoords',
                    tspRequestBody: JSON.stringify(tspRequestBody)
                },
                dataType: 'json',
                success: function (result) {
                    //Error with loading coords
                    if (result.success == false) {
                        resetGrid('customFilePointBoard');
                        setProblemState('Error loading tsp', result.error);
                        return
                    }

                    //Success loading coords
                    tspCoords = result.coords
                    drawPointsOnGrid('customFilePointBoard', tspCoords);
                    resetGrid('outputPathBoard');
                    resetOutputCoords();
                    resetSolutionData();
                    setProblemState('TSP Loaded', 'Custom TSP file loaded');
                },
                error: function (xhr) {
                    console.log(xhr.responseText);
                    resetGrid('customFilePointBoard');
                    setProblemState('Error loading TSP', 'Could not load custom file coords');
                    return;
                }
            });
        } else { //input type not recognized
            setProblemState('Not loaded', 'Unrecognized input type');
            return;
        }
    });

    //Solve tsp
    $solveTspButton.on('click', function () {
        if (solveRequest) {
            setProblemState('Solving', 'Solve already running');
            return;
        }

        //Reset solution
        resetGrid('outputPathBoard');
        setSolutionData('--', '--', '--', '--');

        //Check if tsp is loaded correctly
        if (!tspRequestBody.inputType || tspCoords.length === 0) {
            console.log(tspRequestBody, tspCoords)
            setProblemState('Not loaded', 'Load TSP before solving');
            return;
        }

        const inputType = tspRequestBody.inputType;

        //Refresh custom request body with the current algorithm
        if (inputType == 'custom') {
            if (focusFirstEmpty($customAlgorithm)) {
                setProblemState('Missing input', 'Enter algorithm before solving');
                return;
            }

            tspRequestBody = buildTspReqBody();
        }

        //Send the req to solve tsp
        solveWasCancelled = false;
        showSolveModal();
        setProblemState('Solving', 'Solve in progress');

        solveRequest = $.ajax({
            url: 'backend/tspApi/tspApiRouter.php',
            type: 'POST',
            data: {
                action: 'solveTsp',
                tspRequestBody: JSON.stringify(tspRequestBody)
            },
            dataType: 'json',
            success: function (result) {
                console.log(result);
                //Error with loading coords
                if (result.success == false) {
                    resetGrid('outputPathBoard');
                    setSolutionData('--', '--', '--', '--');
                    setProblemState('Error solving tsp', result.error);
                    return
                }

                //Success wolading coords
                const nCities = result.nCities;
                const dist = result.dist;
                const nnDist = result.nnDist;
                const optimalDist = result.optimalDist > 0 ? result.optimalDist : '--';
                const optimalIncrease = getOptimalIncreasePercent(dist, result.optimalDist);
                const path = result.path;
                const coordBounds = inputType == 'custom' ? getCustomCoordBounds() : null;
                drawPathOnGrid('outputPathBoard', tspCoords, path, coordBounds);
                drawPointsOnGrid('outputPathBoard', tspCoords, false, coordBounds);
                setProblemState('Solved', 'Solution ready');
                setSolutionData(nCities, dist, nnDist, optimalDist, optimalIncrease);
            },
            error: function (xhr, textStatus) {
                if (textStatus == 'abort') {
                    return;
                }

                console.log(xhr.responseText);
                resetGrid('outputPathBoard');
                setSolutionData('--', '--', '--', '--');
                setProblemState('Error loading TSP', 'Could not load instance coords');
                return;
            },
            complete: function () {
                solveRequest = null;

                if (!solveWasCancelled) {
                    hideSolveModal();
                }
            }
        });
    });

});
