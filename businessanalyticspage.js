document.addEventListener("DOMContentLoaded", function () {
    const customRangeBtn = document.getElementById("customRange");
    const startDateInput = document.getElementById("startDate");
    const endDateInput = document.getElementById("endDate");
    const applyBtn = document.getElementById("applyCustomRange");

    startDateInput.style.display = "none";
    endDateInput.style.display = "none";
    applyBtn.style.display = "none";

    customRangeBtn.addEventListener("click", function () {
        const isHidden = startDateInput.style.display === "none";
        startDateInput.style.display = isHidden ? "inline-block" : "none";
        endDateInput.style.display = isHidden ? "inline-block" : "none";
        applyBtn.style.display = isHidden ? "inline-block" : "none";
    });
});

