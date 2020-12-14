<input type="hidden" value="<?=$id;?>" id="id" name="id">

<form id="update-product">

  <div class="admin-container row">
    <div class="columns small-12 menu-title">
      Editar Produto
    </div>




    <div class="columns small-12 medium-3 text-right text-center-on-small small-top-space">
      Imagem:
    </div>

    <div class="columns small-12 medium-6 small-top-space">
      <div class="text-left input-container">
        <input type="file" class="admin-input" id="file" name="file">
      </div>
    </div>

    <div class="columns small-12 medium-3 text-right text-center-on-small small-top-space">
      <img src="" id="image_url"><br>
    </div>

    <div class="columns small-12"></div>

    <div class="columns small-12 medium-3 text-right text-center-on-small small-top-space">
      Título do Item:
    </div>

    <div class="columns small-12 medium-9 small-top-space">
      <div class="text-left input-container">
        <input type="text" required="required" class="admin-input" id="title" name="title">
      </div>
    </div>

    <div class="columns small-12 medium-3 text-right text-center-on-small small-top-space">
      Descrição:
    </div>

    <div class="columns small-12 medium-9 small-top-space">
      <div class="text-left input-container">
        <input type="text" required="required" class="admin-input" id="description" name="description">
      </div>
    </div>

    <div class="columns small-12 medium-3 text-right text-center-on-small small-top-space">
      Preço:
    </div>

    <div class="columns small-12 medium-9 small-top-space">
      <div class="text-left input-container">
        <input type="text" required="required" class="admin-input" id="price" name="price">
      </div>
    </div>

    <div class="columns small-12 medium-3 text-right text-center-on-small small-top-space">
      Preço parcelado:
    </div>

    <div class="columns small-12 medium-9 small-top-space">
      <div class="text-left input-container">
        <input type="text" required="required" class="admin-input" id="parcel" name="parcel">
      </div>
    </div>

    <div class="columns small-12 medium-3 text-right text-center-on-small small-top-space">
      Categorias:
    </div>

    <div class="columns small-12 medium-9 small-top-space">

      <div class="row text-left checkbox-container">
        <div class="columns text-right text-center-on-small small-6 medium-3">
          <label for="category_books">  Livro: </label>
        </div>
        <div class="columns small-6 medium-9 text-left"> <input type="checkbox" class="admin-input" id="category_books" name="category_books"> </div>

        <div class="columns text-right text-center-on-small small-6 medium-3">
          <label for="category_courses">  Curso: </label>
        </div>
        <div class="columns small-6 medium-9 text-left"> <input type="checkbox" class="admin-input" id="category_courses" name="category_courses"> </div>

        <div class="columns text-right text-center-on-small small-6 medium-3">
          <label for="category_subscriptions">  Inscrições: </label>
        </div>
        <div class="columns small-6 medium-9 text-left"> <input type="checkbox" class="admin-input" id="category_subscriptions" name="category_subscriptions"> </div>

        <div class="columns text-right text-center-on-small small-6 medium-3">
          <label for="category_free">  Gratuitos: </label>
        </div>
        <div class="columns small-6 medium-9 text-left"> <input type="checkbox" class="admin-input" id="category_free" name="category_free"> </div>
      </div>
    </div>

    <div class="columns small-12 medium-3 text-right text-center-on-small top-space">
      Mostrar por cima?
    </div>
    <div class="columns small-12 medium-9 top-space">

      <div class="row text-right checkbox-container">
        <div class="columns text-right text-center-on-small small-6 medium-3">
          <label for="is_important">  Mostrar por cima: </label>
        </div>
        <div class="columns small-6 medium-9 text-left"> <input type="checkbox" class="admin-input" id="is_important" name="is_important"> </div>

      </div>
    </div>
  </div>

  <div class="row top-space">
    <div class="menu-admin text-center columns small-12">
      Ultima modificação deste produto em: <div id="last_modification"></div>
    </div>
  </div>

  <div class="row top-space">
    <div class="menu-admin text-center columns small-12">
      <button type="submit" id="Salvar">Salvar</button>
      <button><a  id="Voltar" href="?page=home&hash=<?php echo $hash;?>">Voltar</a></button>
    </div>
  </div>

</form>

<script type="text/javascript">
  var hash = $("#hash").val();

  function readURL(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();

      reader.onload = function(e) {
        $('#image_url').attr('src', e.target.result);
      }

      reader.readAsDataURL(input.files[0]);
    }
  }


  $("#file").change(function() {
    readURL(this);
  });


  $(document).ready(function() {
    $.ajax({
      url: 'http://localhost:9000/product/' + $("#id").val(),
      dataType: 'json',
      type: 'GET',
      success: function(product) {
        console.log(product);

        $("#is_important").prop( "checked", product['is_important'] );

        $("#category_books").prop( "checked", product['category_books'] );
        $("#category_courses").prop( "checked", product['category_courses'] );
        $("#category_free").prop( "checked", product['category_free'] );
        $("#category_subscriptions").prop( "checked", product['category_subscriptions'] );

        $("#description").val(product['description']);
        $("#parcel").val(product['parcel']);
        $("#price").val(product['price']);
        $("#title").val(product['title']);

        $("#image_url").attr("src", product['image_url']);

        $("#last_modification").html(product['last_modification']);
      },
      error: function(json){
        alert(json);
      }
    });
  });



  $("#update-product").submit(function(e){
    e.preventDefault();

    var values = {};

    $.each($("#update-product").serializeArray(), function(i, field) {
      values[field.name] = field.value;
    });

    values["category_books"] = $("#category_books").is(":checked");
    values["category_courses"] = $("#category_courses").is(":checked");
    values["category_subscriptions"] = $("#category_subscriptions").is(":checked");
    values["category_free"] = $("#category_free").is(":checked");
    values["is_important"] = $("#is_important").is(":checked");

    if ($('#file').get(0).files.length === 0) {
      values["image_url"] = $("#image_url").attr("src");
      update(values);
    } else {


      var fd = new FormData();
      var files = $('#file')[0].files;


      if(files.length > 0 ){

        fd.append('file',files[0]);

        $.ajax({
          url: 'http://localhost:9000/upload',
          dataType: 'json',
          data: fd,
          type: 'POST',
          contentType: false,
          processData: false,
          headers: {
            "authorization": "SERGIOS:" + $("#hash").val()
          },
          success: function(json) {
            values["image_url"] = json;
            update(values);

          }, error: function(json) {
            alert("Ocorreu um erro no upload do arquivo. Contate o administrador do sistema.")
          }
        });
      }

    }

  });


  function update(values) {
    $.ajax({
      url: 'http://localhost:9000/update_product/' + $("#id").val(),
      dataType: 'json',
      data: values,
      type: 'POST',
      headers: {
        "authorization": "SERGIOS:" + $("#hash").val()
      },
      success: function(json) {
        window.location = window.location.origin + '?page=home&hash=<?php echo $hash;?>';

      }, error: function(json) {
        alert("Erro ao atualizar o produto.");
        alert(json);
      }
    });
  }

</script>