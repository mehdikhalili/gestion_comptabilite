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

    $('#date_input').change(function() {
        var path = $(this).attr('path')
        var date = $(this).val()
        $.post(path, {
                'date': date
            }, function(data) {
                $('#table').html(data);
        })
    })
});