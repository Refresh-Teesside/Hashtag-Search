delayedAlert();

function delayedAlert() {
    timeoutID = window.setTimeout(showWinner, 10000);
}

function showWinner() {

    $('#winnerModal').modal({
        show: true
    });

    $(".loading").hide();
}