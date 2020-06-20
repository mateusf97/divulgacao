<!DOCTYPE html>
<html>
<head>
  <title>Sergio's Showcase</title>
  <link rel="stylesheet" type="text/css" href="css/normalize.css">
  <link rel="stylesheet" type="text/css" href="css/grid.css">
  <link rel="stylesheet" type="text/css" href="css/body.css">
  <link rel="stylesheet" type="text/css" href="css/custom.css">
  <link rel="icon" href="images/back.svg" />
  <script type="text/javascript" src="scripts/jquery-3.4.1.min.js"></script>
  <script type="text/javascript" src="scripts/jquery-validation.js"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

  <div class="header">
    <div class="row text-center">
      <div class="columns medium-3 small-12 header-image">
        <a href="?page=home"><img src="images/logo.svg"></a>
      </div>
      <div class="columns medium-9 small-12 header-image hide-on-small">
        <div class="row">
          <div class="columns small-4 ">
            <div class="quotation-style">
              <div class="quotation-title">BOVESPA</div>
              <div>
                <span id="percentIBOV" class="quotation-variation">...</span>
                <span id="pointsIBOV" class="quotation-points">...</span>
              </div>
            </div>
          </div>
          <div class="columns small-4 ">
            <div class="quotation-style">
              <div class="quotation-title">DOW JONES</div>
              <div>
                <span id="percentDOW" class="quotation-variation"> ... </span>
                <span id="pointsDOW" class="quotation-points">...</span>
              </div>
            </div>
          </div>
          <div class="columns small-4 ">
            <div class="quotation-style">
              <div class="quotation-title">S&P 500</div>
              <div>
                <span id="percentSP500" class="quotation-variation"> ... </span>
                <span id="pointsSP500" class="quotation-points"> ... </span>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="columns small-4 ">
            <div class="quotation-style">
              <div class="quotation-title">Bitcoin</div>
              <div>
                <span id="percentBitcoin" class="quotation-variation"> ... </span>
                <span id="pointsBitcoin" class="quotation-points"> ... </span>
              </div>
            </div>
          </div>
          <div class="columns small-4 ">
            <div class="quotation-style">
              <div class="quotation-title">Dólar / Real</div>
              <div>
                <span id="percentDolar" class="quotation-variation"> ... </span>
                <span id="pointsDolar" class="quotation-points"> ... </span>
              </div>
            </div>
          </div>
          <div class="columns small-4 ">
            <div class="quotation-style">
              <div class="quotation-title">Euro / Real</div>
              <div>
                <span id="percentEuro" class="quotation-variation"> ... </span>
                <span id="pointsEuro" class="quotation-points"> ... </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script type="text/javascript">

    var api_url = 'http://localhost:8889';

    function atualizaBovespa() {

      $("#percentIBOV").html("...");
      $("#pointsIBOV").html("...");

      $.ajax({
          url: "http://cotacao.b3.com.br/mds/api/v1/instrumentQuotation/ibov"
      }).then(function(data) {

        var pct = parseFloat(data.Trad[0].scty.SctyQtn.prcFlcn).toFixed(2);

        var pontos = (data.Trad[0].scty.SctyQtn.curPrc);
        var porcentagem = ((((pct>0)?"+"+pct:pct)+'%'));


        $("#percentIBOV").html(porcentagem).removeClass("up").removeClass("down").addClass((pct>0?"up":"down"));
        $("#pointsIBOV").html(pontos + "pts");
        setTimeout(function(){ atualizaBovespa() }, 15000);


      });
    }


  function atualizaDowJones() {


    $("#percentDOW").html("...");
    $("#pointsDOW").html("...");

    $.ajax({
      url: api_url + '/getDowJones',
      dataType: 'json',
      type: 'GET',
      success: function(data) {

        var porcText = ($(data.html.chart_info).find("#chart-info-change-percent").text());
        porcentagem = parseFloat(porcText.replace(",",".")).toFixed(2);

        $("#percentDOW").html((((porcentagem>0)?"+"+porcentagem:porcentagem)+'%')).removeClass("up").removeClass("down").addClass((porcentagem>0?"up":"down"));
        $("#pointsDOW").html(data.attr.last_value + "pts");

        setTimeout(function(){ atualizaDowJones() }, 15000);

      }
    });

  }


  function atualizaSP500() {
    $("#percentSP500").html("...");
    $("#pointsSP500").html("...");

    $.ajax({
      url: api_url + '/getSP500',
      dataType: 'json',
      type: 'GET',
      success: function(data) {

        var porcText = ($(data.html.chart_info).find("#chart-info-change-percent").text());
        porcentagem = parseFloat(porcText.replace(",",".")).toFixed(2);

        $("#percentSP500").html((((porcentagem>0)?"+"+porcentagem:porcentagem)+'%')).removeClass("up").removeClass("down").addClass((porcentagem>0?"up":"down"));
        $("#pointsSP500").html(data.attr.last_value + "pts")
        setTimeout(function(){ atualizaSP500() }, 15000);

      }
    });
  }



  function atualizaBitcoin() {
    $("#percentBitcoin").html("...");
    $("#pointsBitcoin").html("...");

    $.ajax({
      url: api_url + '/getBitcoin',
      dataType: 'json',
      type: 'GET',
      success: function(data) {

        var porcText = ($(data.html.chart_info).find("#chart-info-change-percent").text());
        porcentagem = parseFloat(porcText.replace(",",".")).toFixed(2);

        $("#percentBitcoin").html((((porcentagem>0)?"+"+porcentagem:porcentagem)+'%')).removeClass("up").removeClass("down").addClass((porcentagem>0?"up":"down"));
        $("#pointsBitcoin").html("R$ " + parseFloat(data.attr.last_value).toFixed(2));
        setTimeout(function(){ atualizaBitcoin() }, 15000);

      }
    });
  }


  function atualizaDolar() {
    $("#percentDolar").html("...");
    $("#pointsDolar").html("...");

    $.ajax({
      url: api_url + '/getDolar',
      dataType: 'json',
      type: 'GET',
      success: function(data) {

        var porcText = ($(data.html.chart_info).find("#chart-info-change-percent").text());
        porcentagem = parseFloat(porcText.replace(",",".")).toFixed(2);

        $("#percentDolar").html((((porcentagem>0)?"+"+porcentagem:porcentagem)+'%')).removeClass("up").removeClass("down").addClass((porcentagem>0?"up":"down"));
        $("#pointsDolar").html("R$ " + parseFloat(data.attr.last_value).toFixed(2));
        setTimeout(function(){ atualizaBitcoin() }, 15000);

      }
    });
  }



  function atualizaEuro() {
    $("#percentEuro").html("...");
    $("#pointsEuro").html("...");

    $.ajax({
      url: api_url + '/getEuro',
      dataType: 'json',
      type: 'GET',
      success: function(data) {

        var porcText = ($(data.html.chart_info).find("#chart-info-change-percent").text());
        porcentagem = parseFloat(porcText.replace(",",".")).toFixed(2);

        $("#percentEuro").html((((porcentagem>0)?"+"+porcentagem:porcentagem)+'%')).removeClass("up").removeClass("down").addClass((porcentagem>0?"up":"down"));
        $("#pointsEuro").html("R$ " + parseFloat(data.attr.last_value).toFixed(2));
        setTimeout(function(){ atualizaEuro() }, 15000);

      }
    });
  }


    atualizaBovespa();
    atualizaDowJones();
    atualizaSP500();
    atualizaBitcoin();
    atualizaDolar();
    atualizaEuro();

  </script>

  <div class="body">

<div class="hr-div">

  <div class="row text-center category-bar">
    <div class="columns small-12 medium-3 category-container">
      <div class="category-item">E-books</div>
    </div>
    <div class="columns small-12 medium-3 category-container">
      <div class="category-item">Cursos</div>
    </div>
    <div class="columns small-12 medium-3 category-container">
      <div class="category-item">Assinaturas</div>
    </div>
    <div class="columns small-12 medium-3 category-container">
      <div class="category-item">Material Gratuito</div>
    </div>
  </div>

</div>