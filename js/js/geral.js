
$(function () {
    //EFEITO CONTATO
    $("#saiba-mais").click(function (event) {
        event.preventDefault();
        var idElemento = $(this).attr("href");
        var deslocamento = $(idElemento).offset().top;
        $('html, body').animate({scrollTop: deslocamento}, 'slow');
    });

    // AJAX DO FORMULÁRIO DE NEWSLETTER
    $('#btn-newsletter').on('click', function (e) {
        e.preventDefault();

        var url = "email=" + $("#newsletter-email").val();

        $.get("save_newsletter.php?" + url, function (response) {
            $(".floating-alert-messages").html(response);
            $(".floating-alert-messages").removeClass('hide');
            $(".floating-alert-messages").addClass('show');

            setTimeout(function () {
                $(".floating-alert-messages").fadeOut('slow', function () {
                    $(".floating-alert-messages").removeClass('show');
                    $(".floating-alert-messages").addClass('hide');
                });
            }, 3000);
        });
    });

});
