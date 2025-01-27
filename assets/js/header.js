$(() => {
    $("#selectPlan").on('change', async () => {
        const selectedPlanId = $("#selectPlan").val();
        httpPost('select-plan', { selectedPlanId }).then(() => {
            window.location.href = "/";
        })
    })
});
