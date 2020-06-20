<?php

  /**
  * This file represents the constants used in this API
  * @example  define(constant, array)
  */

  /**
  * @static ERRORS represents errors messages used in API
  * @uses in class Error
  * @example  define(name, value)
  */

  define('ERRORS', array(
        'NOT_AUTHENTICATED'                             => 'Erro de autenticação. Verifique os dados e tente novamente',
        'UNDEFINED_ERROR'                               => 'Ocorreu um erro no sistema, tente novamente',
        'UNAUTHORIZED'                                  => 'Você não tem as permissões necessárias para executar esta ação',
        'PASSWORD_CHANGED_SUCCESSFULLY'                 => 'Senha alterada com sucesso',
    )
  );

?>
