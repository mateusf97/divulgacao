
<div class="text-center top-space" id="products">
  Carregando...
</div>



<script type="text/javascript">
  var hash = $("#hash").val();

  $.ajax({
    url: 'http://localhost:9000/products',
    dataType: 'json',
    type: 'GET',
    success: function(products) {

      if (products.length) {

        $("#products").html("");

        item = $("<div class='product-item-title row'>");
        item.append($("<div class='columns small-5'>").html("Título"));
        item.append($("<div class='columns small-1'>").html("Imagem"));
        item.append($("<div class='columns small-1'>").html("Preço"));
        item.append($("<div class='columns small-1'>").html("Parcelado"));
        item.append($("<div class='columns small-1'>").html("Clicks"));
        item.append($("<div class='columns small-3'>").html("Ação"));
        $("#products").append(item);


        for (var i = products.length - 1; i >= 0; i--) {
          item = $("<div class='product-item row item-" + products[i].id + "'>");
          item.append($("<div class='columns small-5'>").html((products[i].title)));
          item.append($("<div class='columns small-1'>").html("<img class='item-pic' src='" + (products[i].image_url) + "'>"));
          item.append($("<div class='columns small-1'>").html((products[i].price)));
          item.append($("<div class='columns small-1'>").html((products[i].parcel)));
          item.append($("<div class='columns small-1'>").html((products[i].clicks)));


          var apagar = $("<button class='action-button' title='Apagar' >").html("🗑").on('click',function(){
            if (confirm("Tem certeza que deseja apagar \n\n" + products[i].title + "\n\n? \n\n Esta ação não poderá ser desfeita.")){

              var id = products[i].id;
                $.ajax({
                  url: 'http://localhost:8889/product',
                  dataType: 'json',
                  data: {"id":id},
                  type: 'DELETE',
                  success: function(json) {
                    $(".item-" + products[i].id).remove();
                  }
                });
            }
          });

          var editar = $("<a  title='Editar' class='action-button' href='?page=editar&hash=" + hash + "&id=" + products[i].id + "'>").html("✎");

          var acao = $("<div class='columns small-3'>");

          acao.append(apagar);
          acao.append(editar);

          item.append(acao);

          $("#products").append(item);
        }

      } else {
        $("#products").html("Nenhum item cadastrado.");

      }


    }, error: function(json) {
    }
  });

</script>



