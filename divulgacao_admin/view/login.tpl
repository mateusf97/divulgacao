<div class="row">
  <div class="columns small-12 top-space">
    Faça seu login:
  </div>
  <div class="columns small-12 medium-4 text-right top-space">
    CPF:
  </div>
  <div class="columns small-12 medium-8 top-space">
    <input type="text" class="input-text-default" id="cpf">
  </div>
  <div class="columns small-12 medium-4 text-right top-space">
    Senha:
  </div>
  <div class="columns small-12 medium-8 top-space">
    <input type="text" class="input-text-default" id="password">
  </div>
  <div class="columns small-12 text-right top-space">
    <input type="button" class="input-button-default" value="Acessar" id="login">
  </div>
</div>

<script type="text/javascript">
  $('#login').on('click',function(){

    $.ajax({
      url: 'http://localhost:9000/login',
      dataType: 'json',
      data: {
        "login": $("#cpf").val(),
        "password": $("#password").val()
      },
      type: 'POST',
      success: function(data) {
        console.log(data)
        window.location = 'http://localhost:8889?page=home&hash=' + data.access_token

      }, error: function(json) {
        alert(json.responseJSON)
      }
    });


  });


</script>