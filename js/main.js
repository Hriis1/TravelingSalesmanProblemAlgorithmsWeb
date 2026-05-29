$(function () {
    const $inputTypes = $('input[name="inputType"]');
    const $typeOptions = $('.type-option');
    const $inputPanels = $('.input-panel');
    const $selectedInputType = $('#selectedInputType');
    const $problemState = $('#problemState');
    const $statusText = $('#statusText');
    const $pathOutput = $('#pathOutput');
    const $outputCoordsMin = $('#outputCoordsMin');
    const $outputCoordsMax = $('#outputCoordsMax');
    const $inputBoardCoordsMin = $('#inputBoardCoordsMin');
    const $inputBoardCoordsMax = $('#inputBoardCoordsMax');
    const $outputBoardCoordsMin = $('#outputBoardCoordsMin');
    const $outputBoardCoordsMax = $('#outputBoardCoordsMax');
    const $solveButton = $('#solveButton');

    const $instanceName = $('#instanceName');
    const $instanceAlgorithm = $('#instanceAlgorithm');
    const $customAlgorithm = $('#customAlgorithm');
    const $coordsMin = $('#coordsMin');
    const $coordsMax = $('#coordsMax');

    function getInputTypeLabel(inputType) {
        return inputType === 'custom' ? 'Custom TSP' : 'TSP Instance';
    }

    function setProblemState(state, status) {
        $problemState.text(state);
        $statusText.text(status);
    }

    function resetOutputCoords() {
        $outputCoordsMin.text('0');
        $outputCoordsMax.text('0');
        $outputBoardCoordsMin.text('0');
        $outputBoardCoordsMax.text('0');
    }

    function updateInputBoardCoords() {
        $inputBoardCoordsMin.text($coordsMin.val().trim() || '0');
        $inputBoardCoordsMax.text($coordsMax.val().trim() || '0');
    }

    function focusFirstEmpty($fields) {
        const emptyField = $fields.toArray().find((field) => !$(field).val().trim());

        if (emptyField) {
            $(emptyField).trigger('focus');
        }

        return Boolean(emptyField);
    }

    function switchInputType(inputType) {
        $typeOptions.removeClass('active');
        $inputTypes.filter(`[value="${inputType}"]`).closest('.type-option').addClass('active');

        $inputPanels.removeClass('active');
        $inputPanels.filter(`[data-input-panel="${inputType}"]`).addClass('active');

        $selectedInputType.text(getInputTypeLabel(inputType));
        setProblemState('None', `${getInputTypeLabel(inputType)} selected`);
        resetOutputCoords();
        $pathOutput.text('No output path yet.');
    }

    $inputTypes.on('change', function () {
        switchInputType($(this).val());
    });

    $coordsMin.add($coordsMax).on('input', updateInputBoardCoords);

    $solveButton.on('click', function () {
        const inputType = $inputTypes.filter(':checked').val();

        if (inputType === 'instance') {
            if (focusFirstEmpty($instanceName.add($instanceAlgorithm))) {
                setProblemState('Missing input', 'Enter instance and algorithm');
                return;
            }

            setProblemState('Solved', 'Solution ready');
            resetOutputCoords();
            $pathOutput.text([
                `Input type: TSP Instance`,
                `Instance: ${$instanceName.val().trim()}`,
                `Algorithm: ${$instanceAlgorithm.val().trim()}`,
                'Path: 1 -> 2 -> 3 -> ... -> 1',
                'Total distance: --'
            ].join('\n'));

            return;
        }

        if (focusFirstEmpty($customAlgorithm.add($coordsMin).add($coordsMax))) {
            setProblemState('Missing input', 'Enter algorithm and coordinate range');
            return;
        }

        const coordsMin = Number($coordsMin.val());
        const coordsMax = Number($coordsMax.val());

        if (coordsMax <= coordsMin) {
            $coordsMax.trigger('focus');
            setProblemState('Invalid input', 'Coords max must be greater than coords min');
            return;
        }

        setProblemState('Solved', 'Solution ready');
        $outputCoordsMin.text(coordsMin);
        $outputCoordsMax.text(coordsMax);
        $outputBoardCoordsMin.text(coordsMin);
        $outputBoardCoordsMax.text(coordsMax);
        $pathOutput.text([
            `Input type: Custom TSP`,
            `Algorithm: ${$customAlgorithm.val().trim()}`,
            `Coordinate range: ${coordsMin} to ${coordsMax}`,
            'Path: custom points will appear here after point placement is implemented',
            'Total distance: --'
        ].join('\n'));
    });
});
