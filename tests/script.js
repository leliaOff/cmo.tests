$('.table-container tr').each(function() {
    let i = Math.round(Math.random() * (10 - 0) + 0);
    $('.easy-checkbox-icon', $(this)).eq(i).click();
    console.log(i);
});