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
    }

    //Write the current placeholder solution values
    function setSolutionData(nCities, dist, nnDist, optimalDist) {
        $solutionNCities.text(nCities);
        $solutionDist.text(dist);
        $solutionNnDist.text(nnDist);
        $solutionOptimalDist.text(optimalDist);
    }

    //Mirror custom coord inputs onto the input board labels
    function updateInputBoardCoords() {
        $inputBoardCoordsMin.text($coordsMin.val().trim() || '0');
        $inputBoardCoordsMax.text($coordsMax.val().trim() || '0');
    }

    //Focus the first required field that has no value
    function focusFirstEmpty($fields) {
        const emptyField = $fields.toArray().find((field) => !$(field).val().trim());

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

        if (inputType !== 'instance') {
            setProblemState('Not loaded', 'Custom TSP loading is not implemented yet');
            return;
        }

        if (focusFirstEmpty($instanceName.add($instanceAlgorithm))) {
            setProblemState('Missing input', 'Enter instance and algorithm');
            return;
        }

        tspRequestBody = buildTspReqBody();
        console.log(tspRequestBody);

        setProblemState('Loaded', 'TSP request body built');
        resetOutputCoords();
        resetSolutionData();
    });

    //Write placeholder solution output after a request body is loaded
    $solveTspButton.on('click', function () {
        if (!tspRequestBody.inputType) {
            setProblemState('Not loaded', 'Load TSP first');
            return;
        }

        setProblemState('Solved', 'Solution ready');
        setSolutionData('--', '--', '--', '--');
    });

});
