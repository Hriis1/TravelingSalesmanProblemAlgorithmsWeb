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