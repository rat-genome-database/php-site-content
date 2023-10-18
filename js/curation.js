$(function () {
    $("#objectName").autocomplete({
        source:function(request, response){
            $.ajax({
                url:"https://ontomate.rgd.mcw.edu/OntoSolr/select",
                data:{
                    "q":request.term,
                    //"qf": "term_en^5 term_str^3 term^3 synonym_en^4.5  synonym_str^2 synonym^2 def^1 idl_s^1",
                    "fq": "cat:(BP CC MF MP HP NBO PW RDO RS VT CMO MMO XCO CHEBI)",
                    "wt": "velocity",
                    "bf": "term_len_l^10",
                    "v.template": "termmatch",
                    "cacheLength": 0
                },
                scrollHeight: 240,
                max: 40,

            })
        }

    });
});