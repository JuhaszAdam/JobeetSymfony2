$(document).ready(function () {
    $('.search input[type="submit"]').hide();

    $('#search_keywords').keyup(function (key) {
        if (this.value.length >= 3 || this.value.length <= 1) {
            $('#loader').show();
            $('#jobs').load(
                $(this).parent('form').attr('action'),
                {query: this.value ? this.value + '*' : this.value},
                function () {
                    $('#loader').hide();
                }
            );
        }
    });
});
