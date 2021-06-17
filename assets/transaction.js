import $ from 'jquery';

$(function() {
    $('#transaction_credit').val($('#currentCredit').text())
    $('#transaction_debit').val($('#currentDebit').text())

    modeDePaiement()
    libelle()

    $('#transaction_modeDePaiement').change(function() {
        modeDePaiement()
    })

    $('#transaction_libelle').change(function() {
        libelle()
    })

    function modeDePaiement() {
        var modeDePaiement = $('#transaction_modeDePaiement').val()
        if ('virement' === modeDePaiement || 'prelevement' === modeDePaiement) {
            $('#transaction_cheque').parent().parent().hide()
            $('#transaction_cheque').val(null)
            $('#transaction_rib').parent().parent().show()
        }
        else if('cheque' === modeDePaiement){
            $('#transaction_rib').parent().parent().hide()
            $('#transaction_rib').val(null)
            $('#transaction_cheque').parent().parent().show()
        }
        else if('especes' === modeDePaiement){
            $('#transaction_rib').parent().parent().hide()
            $('#transaction_rib').val(null)
            $('#transaction_cheque').parent().parent().hide()
            $('#transaction_cheque').val(null)
        }
    }

    function libelle() {
        if ('paiement_client' === $('#transaction_libelle').val()) {
            $('#transaction_credit').parent().parent().hide()
            $('#transaction_credit').val(null)
            $('#transaction_debit').parent().parent().show()
        } else {
            $('#transaction_debit').parent().parent().hide()
            $('#transaction_debit').val(null)
            $('#transaction_credit').parent().parent().show()
        }
    }

    $('#filtrer').click(function() {
        var path = $(this).attr('path')
        var bankAccount = $('#form_bankAccount').val()
        var date_du = $('#date_du').val()
        var date_au = $('#date_au').val()
        $.post(path, {
                'bankAccount': bankAccount,
                'date_du': date_du,
                'date_au': date_au,
            }, function(data) {
                $('#table').html(data);
        })
        /* alert("compte: "+ bankAccount+ "\ndu : "+date_du+"\nau:"+date_au) */
    })
});