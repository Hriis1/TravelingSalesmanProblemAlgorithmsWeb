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

    const $instanceName = $('#instanceName');
    const $instanceAlgorithm = $('#instanceAlgorithm');
    const $customAlgorithm = $('#customAlgorithm');
    const $coordsMin = $('#coordsMin');
    const $coordsMax = $('#coordsMax');

    //Base Req body representation that will be sent to api
    let tspRequestBody = {
        inputType: null,
        instance: null,
        algorithm: null,
    };

    //coords for the loaded tsp
    let tspCoords = [];

    //Display names for the two supported input modes
    function getInputTypeLabel(inputType) {
        return inputType === 'custom' ? 'Custom TSP' : 'TSP Instance';
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

    //Mirror custom coord inputs onto the input board labels
    function updateInputBoardCoords() {
        $inputBoardCoordsMin.text($coordsMin.val().trim() || '0');
        $inputBoardCoordsMax.text($coordsMax.val().trim() || '0');
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
    function drawPointsOnGrid(gridId, coords, shouldResetGrid = true) {
        const $grid = $(gridId.startsWith('#') ? gridId : `#${gridId}`);
        const safeCoords = Array.isArray(coords) ? coords : [];
        const { min, max } = getMinMaxCoords(safeCoords);
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
    function drawPathOnGrid(gridId, coords, path) {
        const $grid = $(gridId.startsWith('#') ? gridId : `#${gridId}`);
        const safeCoords = Array.isArray(coords) ? coords : [];
        const safePath = Array.isArray(path) ? path : [];
        const { min, max } = getMinMaxCoords(safeCoords);
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

    //Show only the form fields for the selected input type
    function switchInputType(inputType) {
        $typeOptions.removeClass('active');
        $inputTypes.filter(`[value="${inputType}"]`).closest('.type-option').addClass('active');

        $inputPanels.removeClass('active');
        $inputPanels.filter(`[data-input-panel="${inputType}"]`).addClass('active');

        $selectedInputType.text(getInputTypeLabel(inputType));
        setProblemState('None', `${getInputTypeLabel(inputType)} selected`);
        resetOutputCoords();
        resetSolutionData();
    }

    //Toggle between TSP Instance and Custom TSP input
    $inputTypes.on('change', function () {
        switchInputType($(this).val());
    });

    //Update board labels while the user edits the coordinate range
    $coordsMin.add($coordsMax).on('input', updateInputBoardCoords);

    //Build the body that will be sent to api
    function buildTspReqBody() {
        const inputType = $inputTypes.filter(':checked').val();

        //Load an instance
        if (inputType === 'instance') {
            return {
                inputType: 'instance',
                instance: $instanceName.val(),
                algorithm: $instanceAlgorithm.val()
            };
        }

        //TODO custom
        return {
            inputType: 'customTSP',
            algorithm: $customAlgorithm.val(),
        };
    }

    //Validate and store the instance request body
    $loadTspButton.on('click', function () {
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
                url: 'backend/tspApi/tspApiController.php',
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
            setProblemState('Not loaded', 'Custom TSP loading is not implemented yet');
            return;
        } else { //input type not recognized
            setProblemState('Not loaded', 'Unrecognized input type');
            return;
        }
    });

    //Solve tsp
    $solveTspButton.on('click', function () {

        //Reset solution
        resetGrid('outputPathBoard');
        setSolutionData('--', '--', '--', '--');

        //Check if tsp is loaded correctly
        if (!tspRequestBody.inputType || !tspCoords) {
            console.log(tspRequestBody, tspCoords)
            setProblemState('Not loaded', 'Load TSP before solving');
            return;
        }

        //Send the req to solve tsp
        $.ajax({
            url: 'backend/tspApi/tspApiController.php',
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
                drawPathOnGrid('outputPathBoard', tspCoords, path);
                drawPointsOnGrid('outputPathBoard', tspCoords, false);
                setProblemState('Solved', 'Solution ready');
                setSolutionData(nCities, dist, nnDist, optimalDist, optimalIncrease);
            },
            error: function (xhr) {
                console.log(xhr.responseText);
                resetGrid('outputPathBoard');
                setSolutionData('--', '--', '--', '--');
                setProblemState('Error loading TSP', 'Could not load instance coords');
                return;
            }
        });
    });

});
