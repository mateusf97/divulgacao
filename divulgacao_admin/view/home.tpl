
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
        item.append($("<div class='columns small-4 medium-3'>").html("Título"));
        item.append($("<div class='columns small-3 medium-1'>").html("Imagem"));
        item.append($("<div class='columns hide-on-small medium-2'>").html("Preço"));
        item.append($("<div class='columns hide-on-small medium-2'>").html("Parcelado"));
        item.append($("<div class='columns hide-on-small medium-1'>").html("Clicks"));
        item.append($("<div class='columns small-5 medium-3'>").html("Ação"));
        $("#products").append(item);


        for (var i = products.length - 1; i >= 0; i--) {
          var id = products[i].id;
          var title = products[i].title;
          item = $("<div class='product-item row item-" + products[i].id + "'>");
          item.append($("<div class='columns small-4 medium-3'>").html((products[i].title)));
          item.append($("<div class='columns small-3 medium-1'>").html("<img class='item-pic' src='" + (products[i].image_url) + "'>"));
          item.append($("<div class='columns hide-on-small medium-2'>").html((products[i].price)));
          item.append($("<div class='columns hide-on-small medium-2'>").html((products[i].parcel)));
          item.append($("<div class='columns hide-on-small medium-1'>").html((products[i].clicks)));
          var acao = $("<div class='columns small-5 medium-3'>");


          acao.append($("<button data-title='" + title + "' data-id='" + id + "' class='action-button' title='Apagar' >").html("🗑").on('click',function(){
            var _this = $(this);
            if (confirm("Tem certeza que deseja apagar o item: \n" + _this.data('title') + "? \n\n Esta ação não poderá ser desfeita.")){

              $.ajax({
                url: 'http://localhost:9000/product',
                dataType: 'json',
                data: {"id":_this.data('id')},
                type: 'DELETE',
                headers: {
                  "authorization": "SERGIOS:" + $("#hash").val()
                },
                success: function(json) {
                  _this.parent('div').parent('div').remove();
                }, error: function() {
                  alert("Não era pra isso ter acontecido, contate o administrador do sistema");
                }
              });
            }
          }));

          var editar = $("<a  title='Editar' class='action-button' href='?page=editar&hash=" + hash + "&id=" + products[i].id + "'>").html("✐");
          var duplicar = $("<a  title='Duplicar' class='action-button' href='?page=duplicar&hash=" + hash + "&id=" + products[i].id + "'>").html("❐");
          acao.append(editar);
          acao.append(duplicar);

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



