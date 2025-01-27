$(() => {
    $("#selectPlan").on('change', async () => {
        const selectedPlanId = $("#selectPlan").val();
        httpPost('../../server/select-plan', { selectedPlanId }).then(() => {
            window.location.reload();
        })
    })
});
