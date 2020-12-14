

<div class="row text-center  top-space">
  <div class="columns small-12 medium-6">
    <img src="images/financas.png">
  </div>
  <div class="columns small-12 medium-6">
    <b><h3>Bem-vindos ao Sergio's Showcase</h3></b>

    <div class="top-space"></div>
    Nós dias atuais tanto para investidores experientes, iniciantes ou para quem deseja ganhar
    dinheiro no mercado financeiro investir em conhecimento e ter informações de qualidade é
    essencial.

    <br><br>

    Pensando nisso a segio’s showcase oferece os melhores cursos e-books e ferramentas para
    quem deseja aprimorar seus conhecimentos ou adquirir novos conhecimentos no mundo dos
    investimentos.
  </div>
</div>

<hr>

<div id="products">


</div>

<script type="text/javascript">

  $.ajax({
    url: 'http://localhost:9000/products',
    dataType: 'json',
    type: 'GET',
    success: function(products) {


        for (var i = products.length - 1; i >= 0; i--) {

          var $row = $('<div class="row top-space-double">');
            var $a = $('<a href="' + products[i].url + '">');
              var $columns = $('<div class="columns small-12 top-space banner-div top-space">');

                var $row2 = $('<div class="row">');

                  var $columns2 = $('<div class="columns small-12 medium-4 top-space image-banner">');
                    var $image = $('<img src="' + products[i].image_url + '">')

                    $columns2.append($image)

                  var $columns3 = $('<div class="columns small-12 medium-5">');
                    var $row3 = $('<div class="row">');

                      var $columns4 = $('<div class="columns small-12 top-space banner-title">').html(products[i].title);
                      var $columns5 = $('<div class="columns small-12 top-space banner-desc">">').html(products[i].description);

                    $row3.append($columns4);
                    $row3.append($columns5);
                    $columns3.append($row3);

                  var $columnsPrice = $('<div class="columns small-12 medium-3 top-space-triple banner-price">');

                    var $price = $('<div>').html("Preço:");
                    var $columnsPriceNotParcel = $('<div class="price">').html(products[i].parcel);
                    var $columnsPriceParcel = $('<div class="price-parcelado">').html(products[i].price);

                  $columnsPrice.append($price);
                  $columnsPrice.append($columnsPriceNotParcel);
                  $columnsPrice.append($columnsPriceParcel);

                $row2.append($columns2);
                $row2.append($columns3);
                $row2.append($columnsPrice);

              $columns.append($row2);

            $a.append($columns);
          $row.append($a);

          $("#products").append($row);
        }
      }, error: function(){

      }
    });




</script>